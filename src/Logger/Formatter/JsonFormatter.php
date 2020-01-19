<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Logger\Formatter;

use DateTime;
use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

class JsonFormatter extends BaseJsonFormatter
{
    /**
     * Formats a log record.
     *
     * @param mixed[] $record A record to format
     *
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        // fix date serialisation so we get date strings instead of DateTime
        // objects serialised into json
        if (($record['datetime'] instanceof DateTime) === true) {
            $record['datetime'] = $record['datetime']->format(\DateTime::RFC3339);
        }

        return parent::format($record);
    }
}
