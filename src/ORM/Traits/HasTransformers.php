<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Traits;

use DateTime as BaseDateTime;
use EoneoPay\Utils\DateTime;

trait HasTransformers
{
    /**
     * Transform given property to bool.
     *
     * @param string $property
     *
     * @return void
     */
    protected function transformToBool(string $property): void
    {
        $this->{$property} = (bool)$this->{$property};
    }

    /**
     * Transform the given property value to a DateTime when applicable.
     *
     * @param string $property
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException If string passed to constructor is not valid
     */
    protected function transformToDateTime(string $property): void
    {
        if ($this->{$property} === null || $this->{$property} instanceof BaseDateTime) {
            return;
        }

        $this->{$property} = new DateTime($this->{$property});
    }

    /**
     * Transform given property value to string.
     *
     * @param string $property
     *
     * @return void
     */
    protected function transformToString(string $property): void
    {
        $this->{$property} = (string)$this->{$property};
    }
}
