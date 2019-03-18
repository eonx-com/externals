<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Encryption\Interfaces;

interface PgpInterface
{
    /**
     * GPG encrypt the supplied plain text
     *
     * @param string $plainText Text to be encrypted
     * @param string $recipientFingerprint PGP key of intended recipient
     *
     * @return string ASCII armored cipher text
     */
    public function encrypt(string $plainText, string $recipientFingerprint): string;

    /**
     * GPG decrypt the supplied plain text
     *
     * @param string $cipherText Text to be decrypted
     * @param string $privateFingerprint Private key fingerprint to be used for decrypting
     *
     * @return string ASCII plain text
     */
    public function decrypt(string $cipherText, string $privateFingerprint): string;


    /**
     * GPG encrypt and cryptographically sign the supplied plain text
     *
     * @param string $plainText Text to be encrypted
     * @param string $recipientFingerprint PGP key of intended recipient
     * @param string $signingFingerprint PGP key of signer
     *
     * @return string ASCII armored cipher text
     */
    public function encryptSign(string $plainText, string $recipientFingerprint, string $signingFingerprint): string;

    /**
     * Decrypt and verify signed cipher text
     *
     * @param string $signedText Signed cipher text
     * @param string $privateFingerprint Private key fingerprint to be used for decrypting
     *
     * @return string Decrypted text
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpValidationException
     */
    public function decryptVerify(string $signedText, string $privateFingerprint): string;
    
    /**
     * Import a PGP key
     *
     * @param string $keyData Contents of key to be imported
     *
     * @return string Fingerprint of the key that was imported
     *
     * @throws \EoneoPay\Externals\Encryption\Exceptions\PgpFailedException
     */
    public function importKey(string $keyData): string;

    /**
     * Return the PGP key
     *
     * @param string $fingerprint Fingerprint of key to retrieve
     *
     * @return array|\EoneoPay\Externals\Encryption\PgpKey[]
     */
    public function getKeys(string $fingerprint): array;
}
