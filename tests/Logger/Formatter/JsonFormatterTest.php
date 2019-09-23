<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Logger\Formatter;

use EoneoPay\Externals\Logger\Formatter\JsonFormatter;
use EoneoPay\Utils\DateTime;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Logger\Formatter\JsonFormatter
 */
class JsonFormatterTest extends TestCase
{
    /**
     * Tests format date works.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     */
    public function testFormatDates(): void
    {
        $formatter = new JsonFormatter();
        $result = $formatter->format([
            'datetime' => new DateTime('2019-01-01T00:00:00+00:00'),
        ]);

        self::assertSame('{"datetime":"2019-01-01T00:00:00+00:00"}', \trim($result));
    }

    /**
     * Tests format date works if the property is not a DateTime.
     *
     * @return void
     */
    public function testFormatDatesString(): void
    {
        $formatter = new JsonFormatter();
        $result = $formatter->format([
            'datetime' => 'string',
        ]);

        self::assertSame('{"datetime":"string"}', \trim($result));
    }
}
