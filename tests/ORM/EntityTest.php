<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\ORM;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use EoneoPay\External\ORM\Exceptions\InvalidMethodCallException;
use Tests\EoneoPay\External\DatabaseTestCase;
use Tests\EoneoPay\External\ORM\Stubs\EntityStub;

/**
 * @covers \EoneoPay\External\ORM\Entity
 */
class EntityTest extends DatabaseTestCase
{
    /**
     * Data to populate the entity with for testing
     *
     * @var array
     */
    private static $data = [
        'entityId' => null,
        'integer' => 1,
        'string' => 'test@test.com'
    ];

    /**
     * Test constructor fills entity with data
     *
     * @return void
     */
    public function testConstructorPopulatesEntity(): void
    {
        $entity = new EntityStub(self::$data);
        self::assertSame(self::$data, $this->getEntityContents($entity));
    }

    /**
     * Test array is fillable by calling fill
     *
     * @return void
     */
    public function testFillMethodPopulatesDataInEntity(): void
    {
        $entity = new EntityStub();
        self::assertEmpty(array_filter($this->getEntityContents($entity)));

        // Add an invalid field to ensure only valid properties are set
        $data = self::$data;
        $data['invalid'] = 'this should not be filled';
        $entity->fill($data);
        self::assertSame(self::$data, $this->getEntityContents($entity));
    }

    /**
     * Test entity can be json serialized
     *
     * @return void
     *
     * @depends testConstructorPopulatesEntity
     */
    public function testJsonSerializeReturnsEntityContentsAsJson(): void
    {
        $entity = new EntityStub(self::$data);
        self::assertSame(json_encode($this->getEntityContents($entity)), json_encode($entity));
    }

    /**
     * Test __call allows setting and getting of data
     *
     * @return void
     */
    public function testMagicCallCanGetAndSetAccessesEntityProperties(): void
    {
        $entity = new EntityStub();
        self::assertEmpty(array_filter($this->getEntityContents($entity)));

        // Test setting email returns entity instance
        $return = $entity->setString(self::$data['string']);
        self::assertInstanceOf(EntityStub::class, $return);

        // Attempt to grab value
        self::assertSame(self::$data['string'], $entity->getString());
    }

    /**
     * Test __call with invalid accessor throws exception
     *
     * @return void
     */
    public function testMagicCallThrowsExceptionIfAccessorInvalid(): void
    {
        $entity = new EntityStub();

        $this->expectException(InvalidMethodCallException::class);
        $entity->whenString();
    }

    /**
     * Test __call with invalid property throws exception
     *
     * @return void
     */
    public function testMagicCallThrowsExceptionIfPropertyInvalid(): void
    {
        $entity = new EntityStub();

        $this->expectException(InvalidMethodCallException::class);
        $entity->getInvalid();
    }

    /**
     * Test the property annotations method on the entity stub contains invalid items
     *
     * @return void
     */
    public function testPropertyAnnotationsContainsInvalidClassAndAttribute(): void
    {
        $entity = new EntityStub();

        $expected = [
            'Tests\EoneoPay\External\ORM\Stubs\InvalidClass' => 'name', // This class is invalid
            Column::class => 'name',
            Id::class => 'invalid' // This attribute is invalid
        ];

        self::assertSame($expected, $entity->getPropertyAnnotations());
    }

    /**
     * Test __call finds properties via annotations
     *
     * @return void
     *
     * @depends testPropertyAnnotationsContainsInvalidClassAndAttribute
     */
    public function testPropertyAnnotationsSetAndGetViaMagicMethods(): void
    {
        $entity = new EntityStub();

        // Set 'annotationName' which is based off the column 'annotation_name' for the 'string' attribute
        $entity->setAnnotationName('test');

        // The value should be fetched via annotationName and string directly
        self::assertSame('test', $entity->getAnnotationName());
        self::assertSame('test', $entity->getString());
    }

    /**
     * Test getting entity data as an array
     *
     * @return void
     *
     * @depends testConstructorPopulatesEntity
     */
    public function testToArrayReturnsEntityContentsAsArray(): void
    {
        $entity = new EntityStub(self::$data);
        self::assertSame($this->getEntityContents($entity), $entity->toArray());
    }

    /**
     * Test getting entity data as json
     *
     * @return void
     *
     * @depends testConstructorPopulatesEntity
     */
    public function testToJsonReturnsEntityContentsAsJson(): void
    {
        $entity = new EntityStub(self::$data);
        self::assertSame(json_encode($this->getEntityContents($entity)), $entity->toJson());
    }
}
