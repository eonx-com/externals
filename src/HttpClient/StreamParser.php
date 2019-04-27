<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Interfaces\StreamParserInterface;
use EoneoPay\Utils\Exceptions\InvalidXmlException;
use EoneoPay\Utils\XmlConverter;
use Exception;
use Psr\Http\Message\StreamInterface;

class StreamParser implements StreamParserInterface
{
    /**
     * @var \EoneoPay\Utils\XmlConverter
     */
    private $xmlConverter;

    /**
     * Constructor
     *
     * @param \EoneoPay\Utils\XmlConverter|null $xmlConverter
     */
    public function __construct(?XmlConverter $xmlConverter = null)
    {
        $this->xmlConverter = $xmlConverter ?? new XmlConverter();
    }

    /**
     * Parses the content out of the body into an array.
     *
     * @param \Psr\Http\Message\StreamInterface $stream
     *
     * @return mixed[]|null
     */
    public function parse(StreamInterface $stream): ?array
    {
        $content = $this->getBodyContents($stream);

        // If content is xml, decode it
        if ($this->isXml($content) === true) {
            try {
                return $this->xmlConverter->xmlToArray($content);
                // @codeCoverageIgnoreStart
            } /** @noinspection BadExceptionsProcessingInspection */ catch (InvalidXmlException $exception) {
                // This exception is unlikely as the `isXML()` method would return false
                // if the content contains invalid/unparseable XML
                // @codeCoverageIgnoreEnd
            }
        }

        // If contents is json, decode it otherwise encase in array
        return $this->isJson($content) === true ?
            \json_decode($content, true) :
            ['content' => $content];
    }

    /**
     * Get response body contents.
     *
     * @param \Psr\Http\Message\StreamInterface $body
     *
     * @return string
     */
    private function getBodyContents(StreamInterface $body): string
    {
        try {
            return $body->getContents();
            // @codeCoverageIgnoreStart
        } /** @noinspection BadExceptionsProcessingInspection */ catch (Exception $exception) {
            // This exception is unlikely as the stream is retrieved directly from Guzzle

            return '';
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Determine if a string is json
     *
     * @param string $string The string to check
     *
     * @return bool
     */
    private function isJson(string $string): bool
    {
        \json_decode($string, false);

        return \json_last_error() === \JSON_ERROR_NONE;
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
}
