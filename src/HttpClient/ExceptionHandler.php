<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Interfaces\ExceptionHandlerInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use function GuzzleHttp\Psr7\stream_for;

class ExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getResponseFrom(GuzzleException $exception): ResponseInterface
    {
        $stringBody = \json_encode(['exception' => $exception->getMessage()]) ?: '';
        $body = stream_for($stringBody);

        if ($exception instanceof RequestException === true) {
            /**
             * @var \GuzzleHttp\Exception\RequestException $exception
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
             */
            return $this->handleRequestException($exception, $body);
        }

        return new Response(500, [], $body);
    }

    /**
     * If the Exception is a RequestException, get the original response from the exception
     * and return that, otherwise throwing a 400 exception.
     *
     * @param \GuzzleHttp\Exception\RequestException $exception
     * @param \Psr\Http\Message\StreamInterface $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleRequestException(RequestException $exception, StreamInterface $body): ResponseInterface
    {
        if ($exception->getResponse() !== null) {
            return $exception->getResponse();
        }

        return new Response(400, [], $body);
    }
}
