<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\ClientFactory;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\HttpClient\ClientFactory
 */
class ClientFactoryTest extends TestCase
{
    /**
     * Tests that the ClientFactory creates a Client
     *
     * @return void
     */
    public function testCreation(): void
    {
        (new ClientFactory())->create();

        // There is very little to assert, besides an exception not being thrown
        $this->addToAssertionCount(1);
    }
}
