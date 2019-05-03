<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Exceptions\NetworkException;
use EoneoPay\Externals\HttpClient\Interfaces\ExceptionHandlerInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\stream_for;

final class ExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\NetworkException
     */
    public function handle(RequestInterface $request, GuzzleException $exception): ResponseInterface
    {
        if (($exception instanceof ConnectException) === true) {
            /**
             * @var \GuzzleHttp\Exception\ConnectException $exception
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
             */
            throw new NetworkException($request, $exception);
        }

        return $this->extractPsrResponse($exception);
    }

    /**
     * Extracts a response if one exists, otherwise creating one.
     *
     * @param \GuzzleHttp\Exception\GuzzleException $exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function extractPsrResponse(GuzzleException $exception): ResponseInterface
    {
        $response = null;

        if ($exception instanceof RequestException === true) {
            /**
             * @var \GuzzleHttp\Exception\RequestException $exception
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
             */
            $response = $exception->getResponse();
        }

        if ($response === null) {
            $stringBody = \json_encode(['exception' => $exception->getMessage()]) ?: '';
            $body = stream_for($stringBody);

            $response = new PsrResponse(500, [], $body);
        }

        return $response;
    }
}
