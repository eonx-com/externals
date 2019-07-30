<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HiddenString;

use ParagonIE\HiddenString\HiddenString as BaseHiddenString;

final class HiddenString
{
    /**
     * Disallow the contents from being accessed via __toString()?
     *
     * @var bool
     */
    protected $disableInline = true;

    /**
     * @var \ParagonIE\HiddenString\HiddenString
     */
    private $hiddenString;

    /**
     * HiddenString constructor.
     *
     * @param string $value
     * @param bool|null $disableInline
     * @param bool|null $disableSerialization
     */
    public function __construct(
        string $value,
        ?bool $disableInline = null,
        ?bool $disableSerialization = null
    ) {
        $this->disableInline = ($disableInline === true || $disableInline === null);
        $disableSerialization = ($disableSerialization === true || $disableSerialization === null);

        $this->hiddenString = new BaseHiddenString($value, $this->disableInline, $disableSerialization);
    }

    /**
     * Returns a copy of the string's internal value.
     * Will return empty string if disableInline is true.
     *
     * @return string
     *
     * @throws \TypeError
     */
    public function __toString(): string
    {
        if ($this->disableInline === false) {
            return $this->getString();
        }

        return '';
    }

    /**
     * Hide its internal state from var_dump()
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'value' =>
                '*',
            'attention' =>
                'If you need the value of a HiddenString, ' .
                'invoke getString() instead of dumping it.'
        ];
    }

    /**
     * Assert two hidden strings are equal.
     *
     * @param \EoneoPay\Externals\HiddenString\HiddenString $string
     *
     * @return bool
     */
    public function equals(HiddenString $string): bool
    {
        return $this->hiddenString->equals($string->hiddenString);
    }

    /**
     * Get string value inside hidden string.
     *
     * @return string
     */
    public function getString(): string
    {
        return $this->hiddenString->getString();
    }
}
