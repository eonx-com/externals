<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\SimpleEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use LaravelDoctrine\Extensions\SoftDeletes\SoftDeletes;

/**
 * @method int|null getInteger()
 * @method string|null getEntityId()
 * @method string|null getString()
 * @method bool hasString()
 * @method bool isString()
 * @method self setInteger(int $integer)
 * @method self setEntityId(string $entityId)
 * @method self setString(string $string)
 *
 * The following methods are only used for testing validity of __call
 * @method string|null getAnnotationName()
 * @method null|null getInvalid()
 * @method self setAnnotationName(string $name)
 * @method null whenString()
 *
 * @ORM\Entity()
 *
 * @Gedmo\SoftDeleteable()
 */
class SimpleEntityStub extends SimpleEntity
{
    use SoftDeletes;

    /**
     * Primary id
     *
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $entityId;

    /**
     * Integer test
     *
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned": true})
     */
    protected $integer;

    /**
     * String test
     *
     * @var string
     *
     * @ORM\Column(type="string", length=190, name="annotation_name", nullable=true)
     */
    protected $string;

    /**
     * Property that has a setter.
     *
     * @var string
     */
    protected $withSetter;

    /**
     * Function exclusively for test purposes to test uniqueRuleAsString.
     *
     * @param string[]|null $wheres
     *
     * @return string
     */
    public function getEmailUniqueRuleForTest(?array $wheres = null): string
    {
        return $this->uniqueRuleAsString('email', $wheres ?? []);
    }

    /**
     * Function exclusively for test purposes to test instanceOfRuleAsString.
     *
     * @param string $class
     *
     * @return string
     */
    public function getInstanceOfRuleForTest(string $class): string
    {
        return $this->instanceOfRuleAsString($class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty(): string
    {
        return 'entityId';
    }

    /**
     * Defined setter for testage - protected so __call will run instead of this property.
     *
     * @param string $withSetter
     *
     * @return void
     */
    protected function setWithSetter(string $withSetter): void
    {
        $this->withSetter = $withSetter;
    }
}
