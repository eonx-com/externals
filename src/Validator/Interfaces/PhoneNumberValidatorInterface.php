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
     * Checks if the phone number is valid.
     *
     * @param string $number
     *
     * @return bool
     */
    public function validate(string $number): bool;
}
