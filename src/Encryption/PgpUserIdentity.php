<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Encryption;

/**
 * PGP user identity
 *
 * @package EoneoPay\Utils
 */
class PgpUserIdentity
{
    /** @var string|null */
    private $name;

    /** @var string|null */
    private $comment;

    /** @var string|null */
    private $email;

    /** @var bool|null */
    private $revoked;

    /** @var bool|null */
    private $valid;

    /**
     * Get the name of the user
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the user
     *
     * @param string|null $name
     *
     * @return \EoneoPay\Externals\Encryption\PgpUserIdentity
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param string|null $comment
     *
     * @return \EoneoPay\Externals\Encryption\PgpUserIdentity
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the email address of the user
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the email address of the user
     *
     * @param string|null $email
     *
     * @return \EoneoPay\Externals\Encryption\PgpUserIdentity
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get validity
     *
     * @return bool|null
     */
    public function isValid(): ?bool
    {
        return $this->valid;
    }

    /**
     * Set validity
     *
     * @param bool|null $valid
     *
     * @return \EoneoPay\Externals\Encryption\PgpUserIdentity
     */
    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Check if the identity has been revoked
     *
     * @return bool|null
     */
    public function isRevoked(): ?bool
    {
        return $this->revoked;
    }

    /**
     * Set revocation status
     *
     * @param bool|null $revoked
     *
     * @return \EoneoPay\Externals\Encryption\PgpUserIdentity
     */
    public function setRevoked(?bool $revoked): self
    {
        $this->revoked = $revoked;

        return $this;
    }

    /**
     * Get identity string|null (e.g. 'John Smith (ICT Manager) <john@smiths.com.au>')
     *
     * @return string|null
     */
    public function getIdentityString(): ?string
    {
        $uid = \trim((string)$this->name);

        if (\trim((string)$this->comment) !== '') {
            $uid .= \sprintf(' (%s)', \trim((string)$this->comment));
        }

        if (\trim((string)$this->email) !== '') {
            $uid .= \sprintf(' <%s>', $this->email);
        }

        return $uid;
    }
}
