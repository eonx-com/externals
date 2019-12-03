<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\SimpleEntityStub;
use Tests\EoneoPay\Externals\TestCases\ORMTestCase;

/**
 * @covers \EoneoPay\Externals\ORM\SimpleEntity
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Coupling is required to fully test entity functionality
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) All test cases must be public
 */
class SimpleEntityTest extends ORMTestCase
{
    /**
     * Data to populate the entity with for testing.
     *
     * @var mixed[]
     */
    private static $data = [
        'entityId' => null,
        'integer' => 1,
        'string' => 'test@test.com',
        'deletedAt' => null,
    ];

    /**
     * Test entity has a generic getId method which return id value based on Id doctrine annotation.
     *
     * @return void
     */
    public function testGetIdReturnsRightValueBasedOnIdAnnotation(): void
    {
        $entity = new SimpleEntityStub();
        $entityId = 'my-entity-id';

        self::assertNull($entity->getId());

        $entity->setEntityId($entityId);

        self::assertSame($entityId, $entity->getId());
    }

    /**
     * Test instanceOfRuleAsString build correctly the string representation of the validation rule.
     *
     * @return void
     */
    public function testInstanceOfRuleAsStringMethod(): void
    {
        self::assertSame(
            'instance_of:stdClass',
            (new SimpleEntityStub())->getInstanceOfRuleForTest(\stdClass::class)
        );

        self::assertSame(
            'instance_of:Tests\EoneoPay\Externals\Stubs\ORM\Entities\SimpleEntityStub',
            (new SimpleEntityStub())->getInstanceOfRuleForTest(SimpleEntityStub::class)
        );
    }

    /**
     * Test __call allows setting and getting of data.
     *
     * @return void
     */
    public function testMagicCallCanGetAndSetAccessesEntityProperties(): void
    {
        $entity = new SimpleEntityStub();
        self::assertEmpty(\array_filter($this->getEntityContents($entity)));

        // Test check entity has properties
        self::assertTrue($entity->hasString());

        // Test string is not set
        self::assertFalse($entity->isString());

        // Test setting email returns entity instance
        self::assertSame($entity, $entity->setString(self::$data['string']));

        // Test string is set
        self::assertTrue($entity->isString());

        // Attempt to grab value
        self::assertSame(self::$data['string'], $entity->getString());
    }

    /**
     * Tests an entity property that has a real setter.
     *
     * @return void
     */
    public function testSetterWithDefinedSetter(): void
    {
        $entity = new SimpleEntityStub();
        $entity->setWithSetter('test');

        self::assertSame('test', $entity->getWithSetter());
    }

    /**
     * Test __call with invalid accessor throws exception.
     *
     * @return void
     */
    public function testMagicCallThrowsExceptionIfAccessorInvalid(): void
    {
        $entity = new SimpleEntityStub();

        $this->expectException(InvalidMethodCallException::class);
        $entity->whenString();
    }

    /**
     * Test __call with invalid property throws exception.
     *
     * @return void
     */
    public function testMagicCallThrowsExceptionIfPropertyInvalid(): void
    {
        $entity = new SimpleEntityStub();

        $this->expectException(InvalidMethodCallException::class);
        $entity->getInvalid();
    }

    /**
     * Test uniqueRuleAsString build correctly the string representation of the validation rule.
     *
     * @return void
     */
    public function testUniqueRuleAsStringMethod(): void
    {
        self::assertSame(
            'unique:Tests\EoneoPay\Externals\Stubs\ORM\Entities\SimpleEntityStub,email,,entityId,where1,value1',
            (new SimpleEntityStub())->getEmailUniqueRuleForTest(['where1' => 'value1'])
        );
    }
}
