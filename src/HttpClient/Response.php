<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Exceptions\InvalidArgumentException;
use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Utils\Collection;
use EoneoPay\Utils\XmlConverter;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\MessageTrait;

final class Response extends Collection implements ResponseInterface
{
    use MessageTrait {
        // We redefine withBody to do additional work when a new body is provided
        withBody as _unusedWithBody;
    }

    /**
     * Map of standard HTTP status code/reason phrases
     *
     * This array was copy and pasted shamelessly from the Zend Diactoros
     * implementation because it wasnt part of the trait.
     *
     * @var string[]
     */
    private static $phrases = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated to 306 => '(Unused)'
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        // SERVER ERROR
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error'
    ];

    /**
     * @var string
     */
    private $reasonPhrase;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * Constructor
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidXmlException
     */
    public function __construct(PsrResponseInterface $response)
    {
        // The parent data array is dealt with in the populateData call
        parent::__construct([]);

        $this->stream = $response->getBody();

        $this->setStatusCode($response->getStatusCode(), $response->getReasonPhrase());
        $this->setHeaders($response->getHeaders());
        $this->populateData();
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): string
    {
        return $this->getBody()->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * @inheritdoc
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidXmlException
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        $new = clone $this;
        $new->stream = $body;
        $new->populateData();

        return $new;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidArgumentException on an invalid status code or reason
     */
    public function withStatus($code, $reasonPhrase = null): Response
    {
        if (\is_int($code) === false) {
            throw new InvalidArgumentException('code must be an integer');
        }

        if ($reasonPhrase !== null && \is_string($reasonPhrase) === false) {
            throw new InvalidArgumentException('reasonPhrase must be a string or null');
        }

        $new = clone $this;
        $new->setStatusCode($code, $reasonPhrase);

        return $new;
    }

    /**
     * Determine if a string is xml
     *
     * @param string $string The string to check
     *
     * @return bool
     */
    private function isXml(string $string): bool
    {
        \libxml_use_internal_errors(true);

        return \simplexml_load_string($string) !== false;
    }

    /**
     * Populates the underlying collection data array with the current data from the
     * body stream.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidXmlException
     */
    private function populateData(): void
    {
        $bodyString = $this->getBody()->__toString();

        $this->replace($this->processResponseContent($bodyString));
    }

    /**
     * Process response body into an array.
     *
     * @param string $content
     *
     * @return mixed[]|null
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidXmlException
     */
    private function processResponseContent(string $content): ?array
    {
        // If content is xml, decode it
        if ($this->isXml($content) === true) {
            return (new XmlConverter())->xmlToArray($content);
        }

        // If contents is json, decode it otherwise encase in array
        return $this->isJson($content) === true ?
            \json_decode($content, true) :
            ['content' => $content];
    }

    /**
     * Set a valid status code.
     *
     * @param int $code
     * @param string|null $reasonPhrase
     *
     * @return void
     */
    private function setStatusCode(int $code, ?string $reasonPhrase = null): void
    {
        if ($reasonPhrase === null && isset(static::$phrases[$code])) {
            $reasonPhrase = static::$phrases[$code];
        }

        $this->reasonPhrase = $reasonPhrase;
        $this->statusCode = $code;
    }
}
