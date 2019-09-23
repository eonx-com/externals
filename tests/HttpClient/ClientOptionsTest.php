<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\ClientOptions;
use GuzzleHttp\RequestOptions;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\HttpClient\ClientOptions
 */
class ClientOptionsTest extends TestCase
{
    /**
     * Tests that the internal `parseTimeoutValue` method correctly.
     *
     * @return void
     */
    public function testClientOptionsFixedInvalidNegativeValue(): void
    {
        $options = new ClientOptions();

        /** @noinspection PhpStrictTypeCheckingInspection Phpstorm sees this negative value as an integer */
        $options->setConnectTimeout(-123.45);

        self::assertSame(0.0, $options->getConnectTimeout());
    }

    /**
     * Tests that the `getConnectTimeout` method returns the value provided to the `setConnectTimeout` method.
     *
     * @return void
     */
    public function testConnectTimeout(): void
    {
        $options = new ClientOptions();
        $expected = 2.25;

        $options->setConnectTimeout(2.25);

        self::assertSame($expected, $options->getConnectTimeout());
    }

    /**
     * Tests that the `getReadTimeout` method returns the value provided to the `setReadTimeout` method.
     *
     * @return void
     */
    public function testReadTimeout(): void
    {
        $options = new ClientOptions();
        $expected = 42.0;

        $options->setReadTimeout(42.0);

        self::assertSame($expected, $options->getReadTimeout());
    }

    /**
     * Tests that the `getRequestTimeout` method returns the value provided to the `setRequestTimeout` method.
     *
     * @return void
     */
    public function testRequestTimeout(): void
    {
        $options = new ClientOptions();
        $expected = 123.0;

        $options->setRequestTimeout(123.0);

        self::assertSame($expected, $options->getRequestTimeout());
    }

    /**
     * Tests that the `toArray` method returns the correctly formatted array.
     *
     * @return void
     */
    public function testToArray(): void
    {
        $options = new ClientOptions();
        $options->setConnectTimeout(1.2);
        $options->setReadTimeout(6.0);
        $options->setRequestTimeout(1.3);
        $expected = [
            RequestOptions::READ_TIMEOUT => 6.0,
            RequestOptions::CONNECT_TIMEOUT => 1.2,
            RequestOptions::TIMEOUT => 1.3,
        ];

        $result = $options->toArray();

        self::assertEquals($expected, $result);
    }
}
