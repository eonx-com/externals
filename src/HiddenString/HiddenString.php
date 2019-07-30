<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HiddenString;

use ParagonIE\HiddenString\HiddenString as BaseHiddenString;

final class HiddenString
{
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
        $disableInline = ($disableInline === true || $disableInline === null);
        $disableSerialization = ($disableSerialization === true || $disableSerialization === null);

        $this->hiddenString = new BaseHiddenString($value, $disableInline, $disableSerialization);
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
