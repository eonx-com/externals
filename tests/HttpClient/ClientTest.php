<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\HttpClient;

use EoneoPay\External\HttpClient\Client;
use EoneoPay\External\HttpClient\Interfaces\InvalidApiResponseExceptionInterface;
use EoneoPay\External\HttpClient\Interfaces\ResponseInterface;
use Tests\EoneoPay\External\HttpClientTestCase;

class ClientTest extends HttpClientTestCase
{
    /**
     * Client should fallback content to empty string if runtime exception is thrown when getting body contents.
     */
    public function testContentEmptyWhenRuntimeExceptionOnBody(): void
    {
        $response = (new Client($this->mockGuzzleClientForResponse($this->mockStreamForRuntimeException())))
            ->request(self::METHOD, self::URI);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals('', $response->getContent());
    }

    /**
     * Client should throw invalid api response exception if status code if different than 200 range.
     */
    public function testInvalidApiResponseExceptionWhenResponseNotSuccessful(): void
    {
        $this->expectException(InvalidApiResponseExceptionInterface::class);

        $this->clientRequest('', 300);
    }

    /**
     * Client should decode json content from response and response should return each field on demand.
     */
    public function testJsonContentSuccessfullyDecoded(): void
    {
        $email = 'test@eoneopay.com.au';
        $contents = \sprintf('{"email":"%s"}', $email);
        $response = $this->clientRequest($contents);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals($contents, $response->getContent());
        self::assertEquals($email, $response->get('email'));
    }

    /**
     * Client should return response interface successfully when no exceptions.
     */
    public function testShouldReturnResponseInterface(): void
    {
        $contents = 'my contents';
        $response = $this->clientRequest($contents);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals($contents, $response->getContent());
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals([], $response->getHeaders());
        self::assertNull($response->getHeader('header'));
    }

    /**
     * Client should return response interface based on exception response when set.
     */
    public function testShouldReturnResponseInterfaceBasedOnRequestExceptionResponse(): void
    {
        $contents = 'my contents';
        $response = (new Client($this->mockGuzzleClientForRequestException($this->mockStreamForContents($contents))))
            ->request(self::METHOD, self::URI);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals($contents, $response->getContent());
    }

    /**
     * Client should return response interface based on exception itself when no response set.
     */
    public function testShouldThrowExceptionWithResponseInterfaceBasedOnRequestException(): void
    {
        try {
            (new Client($this->mockGuzzleClientForRequestException(),
                $this->mockLoggerForException()))->request(self::METHOD, self::URI);
        } catch (InvalidApiResponseExceptionInterface $exception) {
            $response = $exception->getResponse();
        }

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals(\sprintf('{"exception":"%s"}', self::EXCEPTION_MESSAGE), $response->getContent());
        self::assertEquals(self::EXCEPTION_MESSAGE, $response->get('exception'));
    }

    /**
     * Client should decode xml content from response and response should return each field on demand.
     */
    public function testXmlContentSuccessfullyDecoded(): void
    {
        $email = 'test@eoneopay.com.au';
        $contents = \sprintf('<data><email>%s</email></data>', $email);
        $response = $this->clientRequest($contents);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals($contents, $response->getContent());
        self::assertEquals($email, $response->get('email'));
    }
}
