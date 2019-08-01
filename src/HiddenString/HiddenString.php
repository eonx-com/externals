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
        $this->disableInline = (($disableInline ?? true) === true);
        $disableSerialization = (($disableSerialization ?? true) === true);

        $this->hiddenString = new BaseHiddenString($value, $this->disableInline, $disableSerialization);
    }

    /**
     * Hide its internal state from var_dump()
     *
     * @return mixed[]
     */
    public function __debugInfo(): array
    {
        /**
         * Test for this code cannot pass in bamboo because of xDebug changes.
         *
         * @see https://bugs.xdebug.org/bug_view_page.php?bug_id=00001662
         *
         * Xdebug has decided to not consider user class defined ___debugInfo()
         * thus this piece of code does not run with xDebug on bamboo.
         */
        // @codeCoverageIgnoreStart
        return [
            'value' =>
                '*',
            'attention' =>
                'If you need the value of a HiddenString, ' .
                'invoke getString() instead of dumping it.'
        ];
        // @codeCoverageIgnoreEnd
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
