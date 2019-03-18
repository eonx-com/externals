<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Encryption;

use EoneoPay\Externals\Encryption\Exceptions\PgpFailedException;
use EoneoPay\Externals\Encryption\Exceptions\PgpValidationException;
use EoneoPay\Externals\Encryption\Interfaces\PgpInterface;

class Pgp implements PgpInterface
{
    /** @var \gnupg|null */
    private static $client;

    /**
     * Get static instance of the GNUPG client
     *
     * @return \gnupg
     *
     * @throws \BadFunctionCallException If the GNUPG extension could not be found
     */
    private function getClientInstance(): \gnupg
    {
        if (self::$client === null) {
            // The following line can't be hit by code coverage as there is no way to uninstall an extension

            // @codeCoverageIgnoreStart
            if (\class_exists(\gnupg::class) === false) {
                throw new \BadFunctionCallException('GNUPG PECL extension not installed');
            }
            // @codeCoverageIgnoreEnd

            self::$client = new \gnupg();

            // Turn on ASCII armored format
            self::$client->setarmor(1);

            // Set signing mode
            self::$client->setsignmode(\GNUPG_SIG_MODE_NORMAL);
        }

        return self::$client;
    }

    /**
     * PGP encrypt the supplied plain text
     *
     * @param string $plainText Text to be encrypted
     * @param string $recipientFingerprint PGP key of intended recipient
     *
     * @return string ASCII armored cipher text
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function encrypt(string $plainText, string $recipientFingerprint): string
    {
        $this->getClientInstance()->clearencryptkeys();
        $this->getClientInstance()->addencryptkey($recipientFingerprint);

        $result = $this->getClientInstance()->encrypt($plainText);

        if ((bool)$result === false) {
            throw new PgpFailedException($this->getClientInstance()->geterror());
        }

        return $result;
    }

    /**
     * PGP encrypt and cryptographically sign the supplied plain text
     *
     * @param string $plainText Text to be encrypted
     * @param string $recipientFingerprint PGP key of intended recipient
     * @param string $signingFingerprint PGP key of signer
     *
     * @return string ASCII armored cipher text
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function encryptSign(string $plainText, string $recipientFingerprint, string $signingFingerprint): string
    {
        $this->getClientInstance()->clearencryptkeys();
        $this->getClientInstance()->addencryptkey($recipientFingerprint);

        $this->getClientInstance()->clearsignkeys();
        $this->getClientInstance()->addsignkey($signingFingerprint);

        $result = $this->getClientInstance()->encryptsign($plainText);

        if ((bool)$result === false) {
            throw new PgpFailedException($this->getClientInstance()->geterror());
        }

        return $result;
    }

    /**
     * PGP decrypt the supplied plain text
     *
     * @param string $cipherText Text to be decrypted
     * @param string $privateFingerprint Private key fingerprint to be used for decrypting
     *
     * @return string Decrypted text
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function decrypt(string $cipherText, string $privateFingerprint): string
    {
        $this->getClientInstance()->cleardecryptkeys();
        $this->getClientInstance()->adddecryptkey($privateFingerprint, '');

        $result = $this->getClientInstance()->decrypt($cipherText);

        if ((bool)$result === false) {
            throw new PgpFailedException($this->getClientInstance()->geterror());
        }

        return $result;
    }

    /**
     * Decrypt and verify signed cipher text
     *
     * @param string $signedText Signed cipher text
     * @param string $privateFingerprint Private key fingerprint to be used for decrypting
     *
     * @return string Decrypted text
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpValidationException
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function decryptVerify(string $signedText, string $privateFingerprint): string
    {
        $this->getClientInstance()->addsignkey($privateFingerprint);

        /** @var string $plainText */
        $plainText = null;

        $signatures = $this->getClientInstance()->decryptverify($signedText, $plainText);

        if ((bool)$signatures === false) {
            throw new PgpFailedException($this->getClientInstance()->geterror());
        }

        // Validate the signature

        foreach ($signatures as $signature) {
            // If the signature is fully valid- return the decrypted text
            if ($signature['summary'] & \GNUPG_SIGSUM_VALID === 1 &&
                \mb_strtolower($signature['fingerprint']) === \mb_strtolower($privateFingerprint)) {
                return $plainText;
            }
        }

        throw new PgpValidationException('The signature could not be validate');
    }

    /**
     * Import a PGP key
     *
     * @param string $keyData Contents of key to be imported
     *
     * @return string Fingerprint of the key that was imported
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function importKey(string $keyData): string
    {
        $result = $this->getClientInstance()->import($keyData);

        if ((bool)$result === false) {
            throw new PgpFailedException(
                (string)$this->getClientInstance()->geterror()
            );
        }

        return $result['fingerprint'];
    }

    /**
     * Return the PGP key
     *
     * @param string $fingerprint Fingerprint of key to retrieve
     *
     * @return array|\EoneoPay\Externals\Encryption\PgpKey[]
     */
    public function getKeys(string $fingerprint): array
    {
        $keys = [];
        $keyInfos = $this->getClientInstance()->keyinfo($fingerprint);

        foreach ($keyInfos as $keyInfo) {
            // Get each key

            $key = new PgpKey();
            $key->setDisabled($keyInfo['disabled'] ?? null);
            $key->setExpired($keyInfo['expired'] ?? null);
            $key->setRevoked($keyInfo['revoked'] ?? null);
            $key->setSecret($keyInfo['is_secret'] ?? null);
            $key->setSigningCapability($keyInfo['can_sign'] ?? null);
            $key->setEncryptingCapability($keyInfo['can_encrypt'] ?? null);

            if (\is_iterable($keyInfo['uids'])) {
                // Add user identities

                foreach ($keyInfo['uids'] as $identity) {
                    $uid = new PgpUserIdentity();
                    $uid->setName($identity['name']);
                    $uid->setName($identity['comment']);
                    $uid->setName($identity['email']);
                    $uid->setRevoked($identity['revoked']);
                    $uid->setValid($identity['invalid'] !== false);

                    $key->addIdentity($uid);
                }
            }

            if (\is_iterable($keyInfo['subkeys'])) {
                // Add sub-keys

                foreach ($keyInfo['subkeys'] as $subkeyCurrent) {
                    $subkey = new PgpKey();
                    $subkey->setFingerprint($subkeyCurrent['fingerprint'] ?? null);
                    $subkey->setKeyId($subkeyCurrent['id'] ?? null);
                    $subkey->setTimestamp($subkeyCurrent['timestamp'] ?? null);
                    $subkey->setDisabled($subkeyCurrent['disabled'] ?? null);
                    $subkey->setExpired($subkeyCurrent['expired'] ?? null);
                    $subkey->setRevoked($subkeyCurrent['revoked'] ?? null);
                    $subkey->setSecret($subkeyCurrent['is_secret'] ?? null);
                    $subkey->setSigningCapability($subkeyCurrent['can_sign'] ?? null);
                    $subkey->setEncryptingCapability($subkeyCurrent['can_encrypt'] ?? null);

                    $key->addKey($subkey);
                }
            }

            // Add the key to the return array

            $keys[] = $key;
        }

        return $keys;
    }
}
