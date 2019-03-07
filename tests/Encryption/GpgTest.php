<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Encryption;

use EoneoPay\Externals\Encryption\Exceptions\InvalidPublicKeyException;
use EoneoPay\Externals\Encryption\Gpg;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Encryption\Gpg
 */
class GpgTest extends TestCase
{
    /**
     * Test that the supplied string can be signed
     *
     * @return void
     */
    public function testEncrypt(): void
    {
        $encrypted = (new Gpg())->encrypt('Hello World', $this->getPublicKey());

        self::assertStringStartsWith('-----BEGIN PGP MESSAGE-----', $encrypted);
        self::assertStringEndsWith(\sprintf('-----END PGP MESSAGE-----%s', \PHP_EOL), $encrypted);
    }

    /**
     * Test an invalid public key throws an exception
     *
     * @return void
     */
    public function testInvalidPublicKeyThrowsException(): void
    {
        $this->expectException(InvalidPublicKeyException::class);
        $this->expectExceptionMessage('Missing header block in Public Key');

        (new Gpg())->encrypt('Hello World', 'invalid');
    }

    /**
     * Get test key for encryption
     *
     * @return string
     */
    private function getPublicKey(): string
    {
        return '-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: GnuPG v1

mQENBFaKdnIBCACIVyq6yXMOyVygLLMQAc+xom2Mq/Ii0vRm8bh58iOI1kghhxe+
wGDU9FSWEG+msARTUZYYn6iPJLXxTj41i6XdcXdhZai93DR9/fUFGjbbJII6nQ8r
9jAe0tPK7CDOpiE78Kb5p56ViZ1MeOaRDTfzslfNibiU7mkLhCv3XS9jOx1uiCif
VfqIT+/tA3V2mMN071JLXUbfb3FSFA+4Fs0pJUHza5HbDW8nUHmYosNbtoVvj9fC
wiu/W3zOOVx+WI3FLV6cmE8U2UIX3i7SIrGlJDFOgx3vryuTvKRoIwu0lhbhz5qr
l4qYbL0+TQCK4aFqOGHz4894lc/mIbuCliZBABEBAAG0AIkBHAQQAQIABgUCVop2
cgAKCRARb0b/yznPfqZlB/4+dMVjzdG1nz7hxmg/O96iXOJGMctV+KyuKzUZeTqF
5JxCCpd66AKCfa15ZQRi4iSw6ULpc3QDPeytTf7mzULdk94/pH6f4Ass/0anxF0Z
qFgKsr+/5ZXTZ5lYvfu+ehNeHCBFCebsJAsgIMo697Ux3zo5IGbdSXCEWVJRhbDU
kjNnbiVxmaxAslZu5uQ87hTILa9VlhpIzQx3QGyBVZQr4UFEYP7WjQ0enOI2KINr
APKtNNO0x87pw+AnKs2gZ3vtR9CU59xaZe40XWNUeX6Dq8UXDjL5L6qPdFl4Ab4S
RtMRSAL/PdpISDpv0WgQzbjnlxZmRvCvwAxXnXl4Pa2A
=FEVo
-----END PGP PUBLIC KEY BLOCK-----';
    }
}
