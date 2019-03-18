<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Encryption;

use EoneoPay\Externals\Encryption\Exceptions\PgpFailedException;
use EoneoPay\Externals\Encryption\Exceptions\PgpValidationException;
use EoneoPay\Externals\Encryption\Pgp;
use Tests\EoneoPay\Externals\TestCase;

/**
 * Test GNUPG client
 *
 * @covers \EoneoPay\Externals\Encryption\Pgp
 *
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class PgpTest extends TestCase
{
    /**
     * PGP key types
     */
    private const KEY_PRIVATE = 0;
    private const KEY_PUBLIC = 1;

    /**
     * Array of PGP keys for testing purposes
     *
     * @var array|array[]
     */
    private $keyData = [
        self::KEY_PRIVATE => [],
        self::KEY_PUBLIC => []
    ];

    /**
     * Array of expired PGP keys for testing purposes
     *
     * @var array|array[]
     */
    private $keyDataExpired = [
        self::KEY_PRIVATE => [],
        self::KEY_PUBLIC => []
    ];

    /** @var \EoneoPay\Externals\Encryption\Pgp */
    private $gpg;

    /**
     * Multi-lingual demo lipsum for testing
     *
     * @var string
     */
    private $multilingualLipsum;

    /**
     * Setup test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // Load demo text for comparison

        $this->multilingualLipsum = (string)\file_get_contents(\sprintf('%s/Files/lipsum.txt', __DIR__));

        // Load all available keys for testing

        $keyFiles = \glob(\sprintf('%s/Files/PGPValid/*.public', __DIR__), 0);

        foreach ($keyFiles as $filename) {
            $fingerprint =
                \mb_strtolower(
                    \mb_substr(\basename($filename), 0, -7)
                );

            if (\is_readable($filename)) {
                $this->keyData[self::KEY_PUBLIC][$fingerprint] = \file_get_contents($filename);
            }

            $filename = \str_replace('public', 'private', $filename);

            if (\is_readable($filename)) {
                $this->keyData[self::KEY_PRIVATE][$fingerprint] = \file_get_contents($filename);
            }
        }

        $expiredKeyFiles = \glob(\sprintf('%s/Files/PGPExpired/*.public', __DIR__), 0);

        foreach ($expiredKeyFiles as $filename) {
            $fingerprint =
                \mb_strtolower(
                    \mb_substr(\basename($filename), 0, -7)
                );

            if (\is_readable($filename)) {
                $this->keyDataExpired[self::KEY_PUBLIC][$fingerprint] = \file_get_contents($filename);
            }

            $filename = \str_replace('public', 'private', $filename);

            if (\is_readable($filename)) {
                $this->keyDataExpired[self::KEY_PRIVATE][$fingerprint] = \file_get_contents($filename);
            }
        }
    }

    /**
     * Test keys import and report back the expected fingerprint
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function testImportFingerprint(): void
    {
        $this->gpg = new Pgp();

        foreach ($this->keyData[self::KEY_PUBLIC] as $fingerprint => $keyData) {
            $this->assertEquals(
                \mb_strtolower($this->gpg->importKey($this->keyData[self::KEY_PUBLIC][$fingerprint])),
                $fingerprint
            );

            $this->assertEquals(
                \mb_strtolower($this->gpg->importKey($this->keyData[self::KEY_PRIVATE][$fingerprint])),
                $fingerprint
            );
        }
    }


    /**
     * Test keys import and report back the expected fingerprint
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function testImportError(): void
    {
        $this->expectException(PgpFailedException::class);

        $this->gpg = new Pgp();
        $this->gpg->importKey('this_is_not_a_key');
    }

    /**
     * Test retrieval of a key using its fingerprint
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function testGetKey(): void
    {
        $this->setupTestKeys();

        foreach ($this->keyData[self::KEY_PUBLIC] as $fingerprint => $keyData) {
            $this->assertEquals(
                \mb_strtolower($this->gpg->importKey($this->keyData[self::KEY_PUBLIC][$fingerprint])),
                $fingerprint
            );

            $keys = $this->gpg->getKeys($fingerprint);

            $this->assertEquals(\count($keys), 1);
            $key = $keys[0];

            $subkeys = $key->getSubKeys();

            $found = false;

            foreach ($subkeys as $subkey) {
                $this->assertEquals(\count($subkey->getSubKeys()), 0);

                if (\mb_strtolower((string)$subkey->getFingerprint()) === $fingerprint) {
                    $found = true;
                    break;
                }
            }

            $this->assertTrue($found);

            $identities = $key->getIdentities();

            $this->assertEquals(\count($identities), 1);
            $identity = $identities[0];

            $this->assertEquals($identity->getName(), 'valid@testkey.com');
            $this->assertFalse($identity->isRevoked());

            $this->assertEquals(
                \mb_strtolower($this->gpg->importKey($this->keyData[self::KEY_PRIVATE][$fingerprint])),
                $fingerprint
            );
        }
    }

    /**
     * Test that the plain text can be encrypted/decrypted correctly
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function testEncryptDecryptFunction(): void
    {
        $this->setupTestKeys();

        // Encrypt the lipsum text with each public keys and make sure it can be decrypted

        foreach ($this->keyData[self::KEY_PUBLIC] as $fingerprint => $keyData) {
            $cipherLipsum = $this->gpg->encrypt(
                $this->multilingualLipsum,
                $fingerprint
            );

            $plainLipsum = $this->gpg->decrypt(
                $cipherLipsum,
                $fingerprint
            );

            $this->assertEquals($plainLipsum, $this->multilingualLipsum);
        }
    }

    /**
     * Test that the plain text can be encrypted/signed correctly
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpValidationException
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function testEncryptSignFunction(): void
    {
        $this->setupTestKeys();

        // Encrypt the lipsum text with each public keys and make sure it can be decrypted

        foreach ($this->keyData[self::KEY_PUBLIC] as $fingerprint => $keyData) {
            $cipherLipsum = $this->gpg->encryptSign(
                $this->multilingualLipsum,
                $fingerprint,
                $fingerprint
            );

            $decryptedLipsum = $this->gpg->decryptVerify($cipherLipsum, $fingerprint);

            $this->assertEquals($this->multilingualLipsum, $decryptedLipsum);
        }
    }

    /**
     * Test signature failure
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpValidationException
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function testSignatureFailure(): void
    {
        $this->setupTestKeys();

        // Encrypt the lipsum text with each public keys and make sure it can be decrypted

        foreach ($this->keyData[self::KEY_PUBLIC] as $fingerprint => $keyData) {
            $cipherLipsum = $this->gpg->encryptSign(
                $this->multilingualLipsum,
                $fingerprint,
                $fingerprint
            );

            // Test failed decryption

            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->expectException(PgpValidationException::class);

            $this->gpg->decryptVerify($cipherLipsum, 'broken');
        }
    }

    /**
     * Setup PGP keys for testing
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    private function setupTestKeys(): void
    {
        $this->gpg = new Pgp();

        foreach ($this->keyData[self::KEY_PUBLIC] as $fingerprint => $keyData) {
            $this->gpg->importKey($this->keyData[self::KEY_PUBLIC][$fingerprint]);
            $this->gpg->importKey($this->keyData[self::KEY_PRIVATE][$fingerprint]);
        }
    }


    /**
     * Test exception is thrown with invalid encrypt
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function testExceptionEncrypt(): void
    {
        $this->expectException(PgpFailedException::class);

        $this->setupTestKeys();
        $this->gpg->encrypt($this->multilingualLipsum, 'exception');
    }

    /**
     * Test exception is thrown with invalid encrypt and sign
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function testExceptionEncryptSignRecipient(): void
    {
        $this->expectException(PgpFailedException::class);

        $this->setupTestKeys();
        $this->expectException(PgpFailedException::class);

        foreach ($this->keyData[self::KEY_PUBLIC] as $fingerprint => $keyData) {
            $this->gpg->encryptSign(
                $this->multilingualLipsum,
                'exception',
                $fingerprint
            );
        }
    }

    /**
     * Test exception is thrown with invalid encrypt and sign
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function testExceptionEncryptSignSigner(): void
    {
        $this->expectException(PgpFailedException::class);

        $this->setupTestKeys();
        $this->gpg->encryptSign($this->multilingualLipsum, 'exception', 'exception');

        foreach ($this->keyData[self::KEY_PUBLIC] as $fingerprint => $keyData) {
            $this->gpg->encryptSign(
                $this->multilingualLipsum,
                $fingerprint,
                'exception'
            );
        }
    }

    /**
     * Test exception is thrown with invalid decrypt
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function testExceptionDecrypt(): void
    {
        $this->expectException(PgpFailedException::class);

        $this->setupTestKeys();
        $this->gpg->decrypt('exception', 'exception');
    }

    /**
     * Test exception is thrown with invalid decrypt
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpValidationException
     */
    public function testExceptionDecryptVerify(): void
    {
        $this->expectException(PgpFailedException::class);

        $this->setupTestKeys();
        $this->gpg->decryptVerify('exception', 'exception');
    }
}
