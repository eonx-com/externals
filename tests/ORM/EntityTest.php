<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException;
use EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\ChildStub;
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
            'value' => 'my-value'
        ]);

        // Check we have the right type
        if (($childRetrieve instanceof MultiChildStub) === false) {
            self::fail(\sprintf(
                'Many to many association failed, expected %s received %s',
                MultiChildStub::class,
                $childRetrieve === null ? 'null' : \get_class($childRetrieve)
            ));

            return;
        }

        // Test parent is added to child collection
        self::assertEquals(1, $childRetrieve->getParents()->count());
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
     * Test associate on invalid property throws an exception
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
        $this->expectException(InvalidRelationshipException::class);

        $parent = new ParentStub();
        $child = new ChildStub(['annotation_name' => 'string']);

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
     * Test associate allows disassociation
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
     * Test array is fillable by calling fill
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
     * Test entity has a generic getId method which return id value based on Id doctrine annotation.
     *
     * @return void
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
     * Test guarded property can still be set using setter method.
     *
     * @return void
     */
    public function testGuardCanStillBeSetWithSetterMethod(): void
    {
        $entity = new GuardedStub();

        // Fill entity with data excluding entityId
        $data = self::$data;
        $data['entityId'] = 'skipped-entity-value';
        $entity->fill($data);

        // Ensure that entityId is still null.
        self::assertNull($entity->getEntityId());

        // Call setter method (proceeds to __call)
        $entity->setEntityId('the-entity-id');

        // Test that property is set using setter method even if entityId is guarded.
        self::assertEquals('the-entity-id', $entity->getEntityId());
    }

    /**
     * Test guarded prevents filling certain fields
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
            'instance_of:Tests\EoneoPay\Externals\Stubs\ORM\Entities\EntityStub',
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
        self::assertSame($entity, $entity->setString(self::$data['string']));

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
            'Tests\EoneoPay\Externals\Stubs\ORM\Entities\InvalidClass' => 'name', // This class is invalid
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

        self::assertNull($entity->getString());
        self::assertSame($entity, $entity->setString('test'));
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

    /**
     * Test uniqueRuleAsString build correctly the string representation of the validation rule.
     *
     * @return void
     */
    public function testUniqueRuleAsStringMethod(): void
    {
        self::assertEquals(
            'unique:Tests\EoneoPay\Externals\Stubs\ORM\Entities\EntityStub,email,,entityId,where1,value1',
            (new EntityStub())->getEmailUniqueRuleForTest(['where1' => 'value1'])
        );
    }
}
