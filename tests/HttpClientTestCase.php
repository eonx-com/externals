<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals;

use EoneoPay\Externals\HttpClient\Client;
use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Test case needs high cohesion to mock container
 */
abstract class HttpClientTestCase extends TestCase
{
    /**
     * Request exception message.
     *
     * @var string
     */
    protected const EXCEPTION_MESSAGE = 'exception_message';

    /**
     * Method used to mock guzzle client request.
     *
     * @var string
     */
    protected const METHOD = 'GET';

    /**
     * Uri used to mock guzzle client request.
     *
     * @var string
     */
    protected const URI = 'https://eoneopay.com.au';

    /**
     * Headers used to mock guzzle client response.
     *
     * @var string[]
     */
    protected static $headers = [];

    /**
     * Send request using package http client.
     *
     * @param string $contents
     * @param int|null $statusCode
     *
     * @return \EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface
     *
     * @throws \EoneoPay\Externals\HttpClient\Exceptions\InvalidApiResponseException Inherited, for non-200 responses
     */
    protected function clientRequest(string $contents, ?int $statusCode = null): ResponseInterface
    {
        /** @var \GuzzleHttp\Client $mockedClient */
        $mockedClient = $this->mockGuzzleClientForResponse($this->mockStreamForContents($contents), $statusCode);

        return (new Client($mockedClient))->request(self::METHOD, self::URI);
    }

    /**
     * Mock guzzle client for request exception throwing.
     *
     * @param \Mockery\MockInterface|null $body
     * @param int|null $statusCode
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Mockery methods are static
     */
    protected function mockGuzzleClientForRequestException(
        ?MockInterface $body = null,
        ?int $statusCode = null
    ): MockInterface {
        $exception = new RequestException(
            self::EXCEPTION_MESSAGE,
            new Request('', self::URI),
            $body !== null ? $this->mockGuzzleResponse($body, $statusCode) : null
        );

        $client = Mockery::mock(GuzzleClient::class);

        $client
            ->shouldReceive('request')
            ->once()
            ->with(self::METHOD, self::URI, [])
            ->andThrow($exception);

        return $client;
    }

    /**
     * Mock guzzle client for normal behaviour when sending request.
     *
     * @param \Mockery\MockInterface $body
     * @param int|null $statusCode
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Mockery methods are static
     */
    protected function mockGuzzleClientForResponse(MockInterface $body, ?int $statusCode = null): MockInterface
    {
        $client = Mockery::mock(GuzzleClient::class);

        $client
            ->shouldReceive('request')
            ->once()
            ->with(self::METHOD, self::URI, [])
            ->andReturn($this->mockGuzzleResponse($body, $statusCode));

        return $client;
    }

    /**
     * Mock guzzle response for given body and status code.
     *
     * @param \Mockery\MockInterface $body
     * @param int|null $statusCode
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Mockery methods are static
     */
    protected function mockGuzzleResponse(MockInterface $body, ?int $statusCode = null): MockInterface
    {
        $response = Mockery::mock(PsrResponseInterface::class);

        $response->shouldReceive('getBody')->once()->withNoArgs()->andReturn($body);
        $response->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn($statusCode ?? 200);
        $response->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn([]);

        return $response;
    }

    /**
     * Mock logger for the case that an exception is thrown.
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Mockery methods are static
     */
    protected function mockLoggerForException(): MockInterface
    {
        $logger = Mockery::mock(LoggerInterface::class);

        $logger->shouldReceive('exception')->once()->with(Mockery::type(Exception::class));
        $logger->shouldReceive('info')->once()->with('API request sent', [
            'method' => self::METHOD,
            'uri' => self::URI,
            'options' => null
        ]);
        $logger->shouldReceive('info')->once()->with('API response received', [
            'exception' => self::EXCEPTION_MESSAGE
        ]);

        return $logger;
    }

    /**
     * Mock stream interface for normal behaviour when getting contents.
     *
     * @param string $contents
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Mockery methods are static
     */
    protected function mockStreamForContents(string $contents): MockInterface
    {
        $stream = Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('getContents')->once()->withNoArgs()->andReturn($contents);

        return $stream;
    }

    /**
     * Mock stream interface to throw runtime exception when getting contents.
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Mockery methods are static
     */
    protected function mockStreamForRuntimeException(): MockInterface
    {
        $stream = Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('getContents')->once()->withNoArgs()->andThrow(RuntimeException::class);

        return $stream;
    }
}
