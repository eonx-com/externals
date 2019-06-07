<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator\Interfaces;

interface PhoneNumberValidatorInterface
{
    /**
     * Parses a phone number and returns a standardised formatted phone
     * number.
     *
     * @param string $number
     *
     * @return string
     */
    public function format(string $number): ?string;

    /**
     * Checks if the phone number is valid according to phone number patterns. This
     * method takes into account the country code, and implementations might provide
     * a default region for numbers to be processed as (which will allow skipping of
     * +61 for australian numbers).
     *
     * @param string $number
     *
     * @return bool
     */
    public function validate(string $number): bool;
}
