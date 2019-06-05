<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Validator;

use EoneoPay\Externals\Validator\Interfaces\PhoneNumberValidatorInterface;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * @SuppressWarnings(PHPMD.StaticAccess) Libphonenumber construction is with static methods
 */
class PhoneNumberValidator implements PhoneNumberValidatorInterface
{
    /**
     * @var string
     */
    private $defaultRegion;

    /**
     * @var \libphonenumber\PhoneNumberUtil
     */
    private $parser;

    /**
     * Constructor
     *
     * @param string $defaultRegion
     */
    public function __construct(?string $defaultRegion = null)
    {
        $this->defaultRegion = $defaultRegion ?? 'AU';
        $this->parser = PhoneNumberUtil::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function format(string $number): ?string
    {
        $parsed = $this->parseNumber($number);

        if ($parsed === null) {
            return null;
        }

        return $this->parser->format($parsed, PhoneNumberFormat::E164);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(string $number): bool
    {
        $parsed = $this->parseNumber($number);

        if ($parsed === null) {
            return false;
        }

        return $this->parser->isValidNumber($parsed);
    }

    /**
     * Parses and returns a PhoneNumber instance.
     *
     * @param string $number
     *
     * @return \libphonenumber\PhoneNumber|null
     */
    private function parseNumber(string $number): ?PhoneNumber
    {
        try {
            return $this->parser->parse($number, $this->defaultRegion);
        } catch (NumberParseException $exception) {
            return null;
        }
    }
}
