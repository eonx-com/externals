<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Encryption;

/**
 * PGP Key
 *
 * @package EoneoPay\Utils
 */
class PgpKey
{
    /** @var string|null */
    private $fingerprint;

    /** @var string|null */
    private $keyId;

    /** @var int|null */
    private $timestamp;

    /** @var int|null */
    private $expires;

    /** @var bool|null */
    private $disabled;

    /** @var bool|null */
    private $expired;

    /** @var bool|null */
    private $revoked;

    /** @var bool|null */
    private $secret;

    /** @var bool|null */
    private $signingCapability;

    /** @var bool|null */
    private $encryptingCapability;

    /** @var array|\EoneoPay\Externals\Encryption\PgpUserIdentity[] */
    private $identities = [];

    /** @var array|\EoneoPay\Externals\Encryption\PgpKey[] */
    private $keys = [];

    /**
     * Set fingerprint
     *
     * @param string $fingerprint
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function setFingerprint(?string $fingerprint): self
    {
        $this->fingerprint = $fingerprint;

        return $this;
    }

    /**
     * Get fingerprint
     *
     * @return string
     */
    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    /**
     * Set id
     *
     * @param string $keyId
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function setKeyId(?string $keyId): self
    {
        $this->keyId = $keyId;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getKeyId(): ?string
    {
        return $this->keyId;
    }

    /**
     * Set expires
     *
     * @param int $expires
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function setExpires(?int $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get expires
     *
     * @return int
     */
    public function getExpires(): ?int
    {
        return $this->expires;
    }

    /**
     * Set timestamp
     *
     * @param int $timestamp
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function setTimestamp(?int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return int
     */
    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    /**
     * Set secret status
     *
     * @param bool $secret
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function setSecret(?bool $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Check if the key is a secret key
     *
     * @return bool
     */
    public function isSecret(): ?bool
    {
        return $this->secret;
    }

    /**
     * Set expiry status
     *
     * @param bool $expired
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function setExpired(?bool $expired): self
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Check if the key is expired
     *
     * @return bool
     */
    public function isExpired(): ?bool
    {
        return $this->expired;
    }

    /**
     * Set revocation status
     *
     * @param bool $revoked
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function setRevoked(?bool $revoked): self
    {
        $this->revoked = $revoked;

        return $this;
    }

    /**
     * Check revocation status
     *
     * @return bool
     */
    public function isRevoked(): ?bool
    {
        return $this->revoked;
    }

    /**
     * Set disabled status
     *
     * @param bool $disabled
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function setDisabled(?bool $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Check disabled status
     *
     * @return bool
     */
    public function isDisabled(): ?bool
    {
        return $this->disabled;
    }

    /**
     * Set signing key status
     *
     * @param bool $signingCapability
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function setSigningCapability(?bool $signingCapability): self
    {
        $this->signingCapability = $signingCapability;

        return $this;
    }

    /**
     * Check if the key can be used for signing
     *
     * @return bool
     */
    public function isSigningKey(): ?bool
    {
        return $this->signingCapability;
    }

    /**
     * Set encrypting key status
     *
     * @param bool $encryptingCapability
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function setEncryptingCapability(?bool $encryptingCapability): self
    {
        $this->encryptingCapability = $encryptingCapability;

        return $this;
    }

    /**
     * Check if the key can be used for encrypting
     *
     * @return bool
     */
    public function isEncryptingKey(): ?bool
    {
        return $this->encryptingCapability;
    }

    /**
     * Return sub-keys
     *
     * @return array|\EoneoPay\Externals\Encryption\PgpKey[]
     */
    public function getSubKeys(): array
    {
        return $this->keys;
    }

    /**
     * Add a sub-key
     *
     * @param \EoneoPay\Externals\Encryption\PgpKey $key
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function addKey(PgpKey $key): self
    {
        $this->keys[] = $key;

        return $this;
    }

    /**
     * Return sub-identities
     *
     * @return array|\EoneoPay\Externals\Encryption\PgpUserIdentity[]
     */
    public function getIdentities(): array
    {
        return $this->identities;
    }

    /**
     * Add a sub-identity
     *
     * @param \EoneoPay\Externals\Encryption\PgpUserIdentity $identity
     *
     * @return \EoneoPay\Externals\Encryption\PgpKey
     */
    public function addIdentity(PgpUserIdentity $identity): self
    {
        $this->identities[] = $identity;

        return $this;
    }
}
