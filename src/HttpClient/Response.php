<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use Laminas\Diactoros\MessageTrait;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

final class Response implements ResponseInterface
{
    use MessageTrait;

    /**
     * Map of standard HTTP status code/reason phrases.
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
        599 => 'Network Connect Timeout Error',
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
     * Response constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(PsrResponseInterface $response)
    {
        $this->protocol = $response->getProtocolVersion();
        $this->reasonPhrase = $response->getReasonPhrase();
        $this->statusCode = $response->getStatusCode();
        $this->stream = $response->getBody();

        $this->setHeaders($response->getHeaders());
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): string
    {
        return (string)$this->getBody();
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
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = null): PsrResponseInterface
    {
        $new = clone $this;
        $new->reasonPhrase = static::$phrases[$code] ?? '';
        $new->statusCode = $code;

        return $new;
    }
}
