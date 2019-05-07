<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Traits;

use EoneoPay\Utils\DateTime;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\TransformerStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\ORM\Traits\HasTransformers
 */
class HasTransformersTest extends TestCase
{
    /**
     * Test HasTransformers trait transforms the properties as expected.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException If string passed to constructor is not valid
     */
    public function testHasTransformersTraitWorksAsExcepted(): void
    {
        $entity = new TransformerStub();

        self::assertFalse($entity->setBool(null)->getBool());
        self::assertTrue($entity->setBool('true')->getBool());
        self::assertTrue($entity->setBool(true)->getBool());

        self::assertNull($entity->setDatetime(null)->getDatetime());
        self::assertInstanceOf(DateTime::class, $entity->setDatetime('now')->getDatetime());
        self::assertInstanceOf(DateTime::class, $entity->setDatetime(new DateTime())->getDatetime());

        self::assertSame('', $entity->setString(null)->getString());
        self::assertSame('equals', $entity->setString('equals')->getString());
    }
}
