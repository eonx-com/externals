<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HiddenString;

use EoneoPay\Externals\HiddenString\HiddenString;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\HiddenString\HiddenString
 */
class HiddenStringTest extends TestCase
{
    /**
     * Test equals method.
     *
     * @return void
     */
    public function testEquals(): void
    {
        $password1 = new HiddenString('secret');
        $password2 = new HiddenString('secret');
        $password3 = new HiddenString('password');

        self::assertTrue($password1->equals($password2));
        self::assertFalse($password1->equals($password3));
    }

    /**
     * Test getString on hiddenString returns original value.
     *
     * @return void
     */
    public function testGetString(): void
    {
        $password = new HiddenString('secret');

        self::assertSame('secret', $password->getString());
    }

    /**
     * Test hidden string can be casted to string.
     * Also asserts if disable inline is set to true, cast is hidden.
     *
     * @return void
     */
    public function testHiddenStringCanBeCastedAsString(): void
    {
        $passwordVisible = new HiddenString('secret', false);
        $passwordHidden = new HiddenString('secret', true);

        self::assertSame('secret', (string)$passwordVisible);
        self::assertSame('', (string)$passwordHidden);
    }

    /**
     * Test string is hidden when serialized.
     *
     * @return void
     */
    public function testHideWorksWithSerialization(): void
    {
        $name = 'ABC';
        $password = new HiddenString('secret');

        $data = \compact('name', 'password');

        $serialized = \serialize($data);

        self::assertNotFalse(\mb_strpos($serialized, 'ABC'));
        self::assertFalse(\mb_strpos($serialized, 'secret'));
    }
}
