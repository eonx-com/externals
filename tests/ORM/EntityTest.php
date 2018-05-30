<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException;
use EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException;
use Tests\EoneoPay\Externals\DoctrineTestCase;
use Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityStub;
use Tests\EoneoPay\Externals\ORM\Stubs\MultiChildEntityStub;
use Tests\EoneoPay\Externals\ORM\Stubs\MultiParentEntityStub;
use Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub;

/**
 * @covers \EoneoPay\Externals\ORM\Entity
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Entity itself is complex so lot of tests to perform
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Entity itself is complex so lot of tests to perform
 */
class EntityTest extends DoctrineTestCase
{
    /**
     * Data to populate the entity with for testing
     *
     * @var mixed[]
     */
    private static $data = [
        'entityId' => null,
        'integer' => 1,
        'string' => 'test@test.com',
        'deletedAt' => null
    ];

    /**
     * Test associateMultiple function. Stubs used for it are made to provide full coverage.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     */
    public function testAssociateMultiParentsWithMultiChildren(): void
    {
        $parent = new MultiParentEntityStub();
        $child = new MultiChildEntityStub(['annotation_name' => 'string', 'value' => 'my-value']);

        $child->addParent($parent);

        // Test child is added to parent collection
        self::assertEquals(1, $parent->getChildren()->count());
        self::assertTrue($parent->getChildren()->contains($child));
        // Test parent is added to child collection
        self::assertEquals(1, $child->getParents()->count());
        self::assertTrue($child->getParents()->contains($parent));

        // Add parent twice
        $child->addParent($parent);
        // Remove parent from child but not the other side of the relation
        $child->getParents()->removeElement($parent);
        // Add parent again
        $child->addParent($parent);
        // Remove parent from child to avoid skipping method
        $child->getParents()->removeElement($parent);
        // Add parent with no association
        $child->addParentWithNoAssociation($parent);

        // Test parent is added only once
        self::assertEquals(1, $child->getParents()->count());

        $this->getEntityManager()->persist($parent);
        $this->getEntityManager()->persist($child);

        $this->getEntityManager()->flush();
        $this->getDoctrineEntityManager()->clear();

        $childRetrieve = $this->getEntityManager()->getRepository(MultiChildEntityStub::class)->findOneBy([
            'value' => 'my-value'
        ]);

        // Test parent is added to child collection
        self::assertEquals(1, $childRetrieve->getParents()->count());
    }

    /**
     * Entity should throw exception when trying to associate on a wrong association.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException
     */
    public function testAssociateMultiWithWrongAssociationException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $parent = new MultiParentEntityStub();
        $child = new MultiChildEntityStub(['annotation_name' => 'string']);

        $child->addParentWithWrongAssociation($parent);
    }

    /**
     * Entity should throw exception when trying to associate on a wrong attribute.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException
     */
    public function testAssociateMultiWithWrongAttributeException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $parent = new MultiParentEntityStub();
        $child = new MultiChildEntityStub(['annotation_name' => 'string']);

        $child->addParentWithWrongAttribute($parent);
    }

    /**
     * Test associate function. Stubs used for it are made to provide full coverage.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException If the method doesn't exist on an entity
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     * @throws \ReflectionException
     */
    public function testAssociateParentWithChildren(): void
    {
        $parent = new ParentEntityStub();
        $child = new ChildEntityStub(['annotation_name' => 'string']);

        $child->setParent($parent);

        // Test parent is parent class
        self::assertInstanceOf(ParentEntityStub::class, $child->getParent());
        // Test parent contains child
        self::assertTrue($parent->getChildren()->contains($child));

        $child->setParent($parent);

        // Test parent contains child only once
        self::assertEquals(1, $parent->getChildren()->count());
    }

    /**
     * Entity should throw exception when trying to associate on a wrong association.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     * @throws \ReflectionException
     */
    public function testAssociateWithWrongAssociationException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $parent = new ParentEntityStub();
        $child = new ChildEntityStub(['annotation_name' => 'string']);

        $child->setInvalidParent($parent);
    }

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
        self::assertEmpty(\array_filter($this->getEntityContents($entity)));

        // Add an invalid field to ensure only valid properties are set
        $data = self::$data;
        $data['invalid'] = 'this should not be filled';
        $entity->fill($data);
        self::assertSame(self::$data, $this->getEntityContents($entity));
    }

    /**
     * Test entity has a generic getId method which return id value based on Id doctrine annotation.
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     * @throws \ReflectionException
     */
    public function testGetIdReturnsRightValueBasedOnIdAnnotation(): void
    {
        $entity = new EntityStub();
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
        self::assertEquals(
            'instance_of:stdClass',
            (new EntityStub())->getInstanceOfRuleForTest(\stdClass::class)
        );

        self::assertEquals(
            'instance_of:Tests\EoneoPay\Externals\ORM\Stubs\EntityStub',
            (new EntityStub())->getInstanceOfRuleForTest(EntityStub::class)
        );
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
        self::assertSame(\json_encode($this->getEntityContents($entity)), \json_encode($entity));
    }

    /**
     * Test __call allows setting and getting of data
     *
     * @return void
     */
    public function testMagicCallCanGetAndSetAccessesEntityProperties(): void
    {
        $entity = new EntityStub();
        self::assertEmpty(\array_filter($this->getEntityContents($entity)));

        // Test check entity has properties
        self::assertTrue($entity->hasString());

        // Test string is not set
        self::assertFalse($entity->isString());

        // Test setting email returns entity instance
        $return = $entity->setString(self::$data['string']);
        self::assertInstanceOf(EntityStub::class, $return);

        // Test string is set
        self::assertTrue($entity->isString());

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
            'Tests\EoneoPay\Externals\ORM\Stubs\InvalidClass' => 'name', // This class is invalid
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
        self::assertSame(\json_encode($this->getEntityContents($entity)), $entity->toJson());
    }

    /**
     * Test toXml() returns the expected xml string representation of the entity.
     *
     * @return void
     */
    public function testToXmlReturnsRightString(): void
    {
        $expected = function (?string $rootNode = null) {
            /** @noinspection SyntaxError Closing tag name added from sprintf */
            return \sprintf('<?xml version="1.0" encoding="UTF-8"?>
                <%s>
                    <entityId></entityId>
                    <integer>%d</integer>
                    <string>%s</string>
                    <deletedat/>
                </%s>', $rootNode ?? 'data', self::$data['integer'], self::$data['string'], $rootNode ?? 'data');
        };

        $entity = new EntityStub(self::$data);
        self::assertXmlStringEqualsXmlString($expected(), $entity->toXml());
        self::assertXmlStringEqualsXmlString($expected('my-entity'), $entity->toXml('my-entity'));
    }

    /**
     * Test toXml() returns null when entity cannot be serialised as xml.
     *
     * @return void
     */
    public function testToXmlWithInvalidRootNodeReturnsNull(): void
    {
        self::assertNull((new EntityStub())->toXml('@invalid'));
    }

    /**
     * Test uniqueRuleAsString build correctly the string representation of the validation rule.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     * @throws \ReflectionException
     */
    public function testUniqueRuleAsStringMethod(): void
    {
        self::assertEquals(
            'unique:Tests\EoneoPay\Externals\ORM\Stubs\EntityStub,email,,entityId,where1,value1',
            (new EntityStub())->getEmailUniqueRuleForTest(['where1' => 'value1'])
        );
    }
}
