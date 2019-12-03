<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Encryption\Interfaces;

interface GpgInterface
{
    /**
     * Encrypt plain text using gpg.
     *
     * @param string $plainText The plain text to encrypt
     * @param string $publicKey The public key to encrypt with
     *
     * @return string
     */
    public function encrypt(string $plainText, string $publicKey): string;
}
