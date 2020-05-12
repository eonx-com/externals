<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\ChildStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\EntityProxyStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\EntityStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\FillableStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\GuardedStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\InvalidRelationshipStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiChildStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiParentStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\ParentStub;
use Tests\EoneoPay\Externals\TestCases\ORMTestCase;

/**
 * @covers \EoneoPay\Externals\ORM\Entity
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Coupling is required to fully test entity functionality
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) All test cases must be public
 */
class EntityTest extends ORMTestCase
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
     * Test associateMultiple function. Stubs used for it are made to provide full coverage.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException
     */
    public function testAssociateMultiParentsWithMultiChildren(): void
    {
        $parent = new MultiParentStub();
        $child = new MultiChildStub(['annotation_name' => 'string', 'value' => 'my-value']);

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

        $childRetrieve = $this->getEntityManager()->getRepository(MultiChildStub::class)->findOneBy([
            'value' => 'my-value',
        ]);

        // Check we have the right type
        if (($childRetrieve instanceof MultiChildStub) === false) {
            self::fail(\sprintf(
                'Many to many association failed, expected %s received %s',
                MultiChildStub::class,
                $childRetrieve === null ? 'null' : \get_class($childRetrieve)
            ));
        }

        /**
         * @var \Tests\EoneoPay\Externals\Stubs\ORM\Entities\MultiChildStub $childRetrieve
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === check
         */
        // Test parent is added to child collection
        self::assertSame(1, $childRetrieve->getParents()->count());
    }

    /**
     * Entity should throw exception when trying to associate on a wrong association.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException
     */
    public function testAssociateMultiWithWrongAssociationException(): void
    {
        $this->expectException(InvalidRelationshipException::class);

        $parent = new MultiParentStub();
        $child = new MultiChildStub(['annotation_name' => 'string']);

        $child->addParentWithWrongAssociation($parent);
    }

    /**
     * Entity should throw exception when trying to associate on a wrong attribute.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException
     */
    public function testAssociateMultiWithWrongAttributeException(): void
    {
        $this->expectException(InvalidRelationshipException::class);

        $parent = new MultiParentStub();
        $child = new MultiChildStub(['annotation_name' => 'string']);

        $child->addParentWithWrongAttribute($parent);
    }

    /**
     * Test associate function. Stubs used for it are made to provide full coverage.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException
     */
    public function testAssociateParentWithChildren(): void
    {
        $parent = new ParentStub();
        $child = new ChildStub(['annotation_name' => 'string']);

        $child->setParent($parent);

        // Test parent is parent class
        self::assertInstanceOf(ParentStub::class, $child->getParent());
        // Test parent contains child
        self::assertTrue($parent->getChildren()->contains($child));

        $child->setParent($parent);

        // Test parent contains child only once
        self::assertEquals(1, $parent->getChildren()->count());
    }

    /**
     * Test associate on invalid property throws an exception.
     *
     * @return void
     */
    public function testAssociateThrowsExceptionWithInvalidProperty(): void
    {
        $child = new InvalidRelationshipStub();

        $this->expectException(InvalidRelationshipException::class);

        $child->setParent(new ParentStub());
    }

    /**
     * Entity should throw exception when trying to associate on a wrong association.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException
     */
    public function testAssociateWithWrongAssociationException(): void
    {
        $parent = new ParentStub();
        $child = new ChildStub(['annotation_name' => 'string']);

        $this->expectException(InvalidRelationshipException::class);

        $child->setInvalidParent($parent);
    }

    /**
     * Test constructor fills entity with data.
     *
     * @return void
     */
    public function testConstructorPopulatesEntity(): void
    {
        $entity = new EntityStub(self::$data);
        self::assertSame(self::$data, $this->getEntityContents($entity));
    }

    /**
     * Test associate allows disassociation.
     *
     * @return void
     * */
    public function testDisassociateAssociation(): void
    {
        $parent = new ParentStub();
        $child = new ChildStub(['annotation_name' => 'string']);

        $child->setParent($parent);

        // Test parent is set
        self::assertInstanceOf(ParentStub::class, $child->getParent());
        // Test parent contains child
        self::assertTrue($parent->getChildren()->contains($child));

        // Disassociate
        $child->setParent(null);

        // Test parent is unset
        self::assertNull($child->getParent());
        // Test parent no longer contains child
        self::assertFalse($parent->getChildren()->contains($child));
    }

    /**
     * Test array is fillable by calling fill.
     *
     * @return void
     */
    public function testFillMethodPopulatesDataInEntity(): void
    {
        $entity = new FillableStub();
        self::assertEmpty(\array_filter($this->getEntityContents($entity)));

        // Add an invalid field to ensure only valid properties are set
        $data = self::$data;
        $data['invalid'] = 'this should not be filled';
        $entity->fill($data);
        self::assertSame(self::$data, $this->getEntityContents($entity));
    }

    /**
     * Tests that getObjectProperties does not allow operations on __ prefixed properties
     * which are considered internal implementation details by php.
     *
     * @return void
     */
    public function testGetObjectPropertiesIgnoresUnderscores(): void
    {
        $entity = new EntityProxyStub();
        $entity->__initializer__ = true;
        $entity->fill([
            // Due to quirks of the Array search method, the trailing __ is omitted
            '__initializer' => false,
        ]);

        self::assertTrue($entity->__initializer__);
    }

    /**
     * Tests that the validatableProperties method returns correct properties.
     *
     * @return void
     */
    public function testGetValidatableProperties(): void
    {
        $entity = new EntityStub();
        $expected = [
            'entityId',
            'integer',
            'string',
            'deletedAt',
        ];

        $properties = $entity->getValidatableProperties();

        self::assertSame($expected, $properties);
    }

    /**
     * Test guarded prevents filling certain fields.
     *
     * @return void
     */
    public function testGuardPreventsFillingDataInEntity(): void
    {
        $entity = new GuardedStub();

        // Fill entity with data including 'id' for entityId
        $data = self::$data;
        $data['entityId'] = 'id';
        $entity->fill($data);

        // Ensure entity id was not populated with 'id'
        $expected = \array_merge($data, ['entityId' => null]);
        self::assertSame($expected, $entity->toArray());
    }

    /**
     * Test entity can be json serialized.
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
     * Tests that setters for non existent properties can be accessed.
     * This is part of a test which checks if setters can be called
     * without needing to have a corresponding property on the entity.
     * In this example $nonExistent does not need to be a property in entity class,
     * but the setter would still be called when we do a new EntityStub(['nonExistent' => 'value'])
     *
     * @return void
     */
    public function testSettingNonExistentPropertyWithASetter(): void
    {
        $entity = new EntityStub([
            'nonExistent' => 'something'
        ]);

        self::assertSame('something', $entity->getString());
    }

    /**
     * Test getting entity data as an array.
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
     * Test getting entity data as json.
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
        $expected = static function (?string $rootNode = null): string {
            return \sprintf('<?xml version="1.0" encoding="UTF-8"?>
                <%s>
                    <entityId></entityId>
                    <integer>%d</integer>
                    <string>%s</string>
                    <deletedAt/>
                </%s>', $rootNode ?? 'data', self::$data['integer'], self::$data['string'], $rootNode ?? 'data');
        };

        $entity = new EntityStub(self::$data);
        self::assertXmlStringEqualsXmlString($expected(), (string)$entity->toXml());
        self::assertXmlStringEqualsXmlString($expected('my-entity'), (string)$entity->toXml('my-entity'));
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
}
