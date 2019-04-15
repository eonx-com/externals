<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Encryption;

use EoneoPay\Externals\Encryption\Exceptions\EncryptionFailedException;
use EoneoPay\Externals\Encryption\Exceptions\InvalidPublicKeyException;
use EoneoPay\Externals\Encryption\Interfaces\GpgInterface;
use Exception;
use nicoSWD\GPG\GPG as Client;
use nicoSWD\GPG\PublicKey;

final class Gpg implements GpgInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\InvalidPublicKeyException If public key is invalid
     * @throws \EoneoPay\Externals\Encryption\Exceptions\EncryptionFailedException If encryption fails
     */
    public function encrypt(string $plainText, string $publicKey): string
    {
        // Create public key
        try {
            $key = new PublicKey($publicKey);
        } catch (Exception $exception) {
            throw new InvalidPublicKeyException($exception->getMessage(), $exception->getCode(), $exception);
        }

        // Attempt to encrypt text
        try {
            return (new Client())->encrypt($key, $plainText);
            // @codeCoverageIgnoreStart
        } catch (Exception $exception) {
            // Not entirely sure where this exception is thrown from - this is only here as a safety
            throw new EncryptionFailedException($exception->getMessage(), $exception->getCode(), $exception);
            // @codeCoverageIgnoreEnd
        }
    }
}
