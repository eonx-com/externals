<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\StreamParser;
use EoneoPay\Utils\XmlConverter;
use Tests\EoneoPay\Externals\TestCase;
use function GuzzleHttp\Psr7\stream_for;

/**
 * @covers \EoneoPay\Externals\HttpClient\StreamParser
 */
class StreamParserTest extends TestCase
{
    /**
     * Tests the parser when the body does not parse into json or xml
     *
     * @return void
     */
    public function testParseUnknown(): void
    {
        $stream = stream_for('body');

        $instance = $this->createInstance();
        $result = $instance->parse($stream);

        self::assertSame(['content' => 'body'], $result);
    }

    /**
     * Tests the parser when the body contains json
     *
     * @return void
     */
    public function testParseJson(): void
    {
        $stream = stream_for('{"hello":"world"}');

        $instance = $this->createInstance();
        $result = $instance->parse($stream);

        self::assertSame(['hello' => 'world'], $result);
    }

    /**
     * Tests the parser when the body contains xml
     *
     * @return void
     */
    public function testParseXml(): void
    {
        $stream = stream_for('<?xml version="1.0"?><data><test>1</test></data>');

        $instance = $this->createInstance();
        $result = $instance->parse($stream);

        self::assertSame([
            'test' => '1',
            '@rootNode' => 'data'
        ], $result);
    }

    /**
     * Creates an instance
     *
     * @return \EoneoPay\Externals\HttpClient\StreamParser
     */
    private function createInstance(): StreamParser
    {
        return new StreamParser(new XmlConverter());
    }
}
