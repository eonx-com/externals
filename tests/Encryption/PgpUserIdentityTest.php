<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Encryption;

use EoneoPay\Externals\Encryption\PgpUserIdentity;
use Tests\EoneoPay\Externals\TestCase;

/**
 * Test GPG key class
 *
 * @covers \EoneoPay\Externals\Encryption\PgpUserIdentity
 */
class PgpUserIdentityTest extends TestCase
{
    /**
     * Test getters/setters for PgpUserIdentity
     *
     * @return void
     */
    public function testGetSet(): void
    {
        $userIdentity = new PgpUserIdentity();
        $userIdentity->setValid(true);
        $this->assertTrue($userIdentity->isValid());

        $userIdentity->setRevoked(true);
        $this->assertTrue($userIdentity->isRevoked());

        $userIdentity->setComment('comment');
        $this->assertEquals('comment', $userIdentity->getComment());

        $userIdentity->setEmail('email');
        $this->assertEquals('email', $userIdentity->getEmail());

        $userIdentity->setName('name');
        $this->assertEquals('name', $userIdentity->getName());

        $this->assertEquals('name (comment) <email>', $userIdentity->getIdentityString());
    }
}
