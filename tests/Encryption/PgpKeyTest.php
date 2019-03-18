<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Encryption;

use EoneoPay\Externals\Encryption\PgpKey;
use EoneoPay\Externals\Encryption\PgpUserIdentity;
use Tests\EoneoPay\Externals\TestCase;

/**
 * Test GPG key class
 *
 * @covers \EoneoPay\Externals\Encryption\PgpKey
 */
class PgpKeyTest extends TestCase
{
    /**
     * Test getters/setters for PgpUserIdentity
     *
     * @return void
     */
    public function testGetSet(): void
    {
        $key = new PgpKey();

        $key->setRevoked(true);
        $this->assertTrue($key->isRevoked());

        $key->setFingerprint('12345');
        $this->assertEquals($key->getFingerprint(), '12345');

        $key->setEncryptingCapability(true);
        $this->assertTrue($key->isEncryptingKey());

        $key->setSigningCapability(true);
        $this->assertTrue($key->isSigningKey());

        $key->setSecret(true);
        $this->assertTrue($key->isSecret());

        $key->setExpired(true);
        $this->assertTrue($key->isExpired());

        $key->setDisabled(true);
        $this->assertTrue($key->isDisabled());

        $key->setExpires(12345);
        $this->assertEquals($key->getExpires(), 12345);

        $key->setKeyId('ABCDE');
        $this->assertEquals($key->getKeyId(), 'ABCDE');

        $key->setTimestamp(98765);
        $this->assertEquals($key->getTimestamp(), 98765);

        $subkey = clone $key;

        $subkeys = $key->getSubKeys();
        $this->assertEquals(\count($subkeys), 0);
        $key->addKey($subkey);
        $subkeys = $key->getSubKeys();
        $this->assertEquals(\count($subkeys), 1);

        $identities = $key->getIdentities();
        $this->assertEquals(\count($identities), 0);
        $key->addIdentity(new PgpUserIdentity());
        $identities = $key->getIdentities();
        $this->assertEquals(\count($identities), 1);
    }
}
