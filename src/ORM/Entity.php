<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use Doctrine\ORM\Mapping\Id;
use EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException;
use EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Utils\AnnotationReader;
use EoneoPay\Utils\Arr;
use EoneoPay\Utils\Exceptions\AnnotationCacheException;
use EoneoPay\Utils\Exceptions\InvalidXmlTagException;
use EoneoPay\Utils\XmlConverter;
use Exception;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) Complexity covered by unit tests
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Entity implements EntityInterface
{
    /**
     * Create a new entity
     *
     * @param mixed[]|null $data The data to populate the entity with
     */
    public function __construct(?array $data = null)
    {
        $this->fill($data ?? []);
    }

    /**
     * Serialize entity as an array
     *
     * @return mixed[]
     */
    abstract public function toArray(): array;

    /**
     * Allow getX() and setX($value) to get and set column values
     *
     * This method searches case insensitive
     *
     * @param string $method The method being called
     * @param mixed[] $parameters Parameters passed to the method
     *
     * @return mixed Value or null on getX(), self on setX(value)
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException If the method doesn't exist or is immutable
     */
    public function __call(string $method, array $parameters)
    {
        // Set available types
        $types = ['get', 'has', 'is', 'set'];

        // Break calling method into type (get, has, is, set) and attribute
        \preg_match('/^(' . \implode('|', $types) . ')([a-zA-Z][\w]+)$/i', $method, $matches);

        $type = \mb_strtolower($matches[1] ?? '');
        $property = $this->resolveProperty($matches[2] ?? '');

        // The property being accessed must exist and the type must be valid if one of these things
        // aren't true throw an exception
        if ($type === '' || $property === null || ($type === 'set' && $this->isFillable($property) === false)) {
            throw new InvalidMethodCallException(
                \sprintf('Call to undefined method %s::%s()', \get_class($this), $method)
            );
        }

        // Perform action
        switch ($type) {
            case 'get':
                return $this->get($property);

            case 'has':
                return $this->has($property);

            case 'is':
                // Always return a boolean
                return (bool)$this->get($property);

            case 'set':
                // Return original instance for fluency
                $this->set($property, \reset($parameters));
                break;
        }

        return $this;
    }

    /**
     * Populate a entity from an array of data
     *
     * @param mixed[] $data The data to fill the entity with
     *
     * @return void
     */
    public function fill(array $data): void
    {
        // Loop through data and set values, set will automatically skip invalid or non-fillable properties
        foreach ($data as $property => $value) {
            $this->set($property, $value);
        }
    }

    /**
     * Get a list of attributes or keys which are able to be filled, by default all fields can be set
     *
     * @return string[]
     */
    public function getFillableProperties(): array
    {
        return $this->invokeEntityMethod('getFillable', ['*']);
    }

    /**
     * Get entity id.
     *
     * @return null|string|int
     *
     * @throws \ReflectionException
     */
    public function getId()
    {
        return $this->get($this->getIdProperty());
    }

    /**
     * Return contents for serializing as json
     *
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Serialize entity as json
     *
     * @return string
     */
    public function toJson(): string
    {
        return \json_encode($this->toArray());
    }

    /**
     * Serialize entity as xml
     *
     * @param string|null $rootNode The name of the root node
     *
     * @return string|null
     */
    public function toXml(?string $rootNode = null): ?string
    {
        try {
            return (new XmlConverter())->arrayToXml($this->toArray(), $rootNode);
        } /** @noinspection BadExceptionsProcessingInspection */ catch (InvalidXmlTagException $exception) {
            // If entity can't be serialised due to an invalid tag ignore error and return null
            return null;
        }
    }

    /**
     * Associate an entity in a bidirectional way from the owning side
     *
     * @param string $attribute The attribute on the entity for the many to one association
     * @param \EoneoPay\Externals\ORM\Entity $parent The entity to associate
     * @param string|null $association The attribute on the parent for the one to many collection
     *
     * @return mixed The original entity for fluency
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException If attribute does not exist
     * @throws \ReflectionException Inherited, if class or property does not exist
     */
    protected function associate(string $attribute, Entity $parent, ?string $association = null)
    {
        // If attribute does not exist on entity, throw exception
        $this->checkEntityHasAttribute($attribute);

        // Determine getter
        $getter = \sprintf('get%s', \ucfirst($attribute));

        // Get current attribute value
        $currentValue = $this->{$getter}();

        // If attribute value is already parent, return
        if ($currentValue === $parent) {
            return $this;
        }

        $this->{$attribute} = $parent;

        // If foreign key column explicitly defined assign parent id
        $foreignKey = \sprintf('%sId', $attribute);
        if (\property_exists($this, $foreignKey)) {
            $this->{$foreignKey} = $parent->getId();
        }

        // If association set, handle it
        if ($association !== null) {
            try {
                $this->handleReverseAssociation($association, $parent, $currentValue);
            } catch (InvalidMethodCallException $exception) {
                // We have to throw a different exception otherwise it's caught higher and it dies silently.
                throw new InvalidArgumentException(
                    \sprintf('Property %s::%s does not exist', \get_class($parent), $association),
                    null,
                    $exception
                );
            }
        }

        return $this;
    }

    /**
     * Associate an entity in a bidirectional way on a N-N relation
     *
     * @param string $attribute The attribute on the first entity for the many to many association
     * @param \EoneoPay\Externals\ORM\Entity $parent The entity to associate
     * @param string|null $association The attribute on the second entity for the many to many collection
     *
     * @return mixed The original entity for fluency
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    protected function associateMultiple(string $attribute, Entity $parent, ?string $association = null)
    {
        // If attribute does not exist on entity, throw exception
        $this->checkEntityHasAttribute($attribute);

        // If attribute contains this item, return
        if ($this->{$attribute}->contains($parent)) {
            return $this;
        }

        // Add parent if not exists
        $this->{$attribute}->add($parent);

        // If no association given, return
        if ($association === null) {
            return $this;
        }

        // Determine parent collection method
        $collection = \sprintf('get%s', \ucfirst($association));

        try {
            // If parent collection contains this item, return
            if ($parent->{$collection}()->contains($this)) {
                return $this;
            }

            // Add entity to parent collection
            $parent->{$collection}()->add($this);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (InvalidMethodCallException $exception) {
            // We have to throw a different exception otherwise it's caught higher and it dies silently.
            throw new InvalidArgumentException(
                \sprintf('Property %s::%s does not exist', \get_class($parent), $association),
                null,
                $exception
            );
        }

        return $this;
    }

    /**
     * Get id property name for current entity.
     *
     * @return string
     *
     * @throws \ReflectionException Inherited, if class or property does not exist
     */
    protected function getIdProperty(): string
    {
        // Check for id annotation, if annotations aren't available return 'id'
        try {
            $ids = (new AnnotationReader())->getClassPropertyAnnotation(\get_class($this), Id::class);
            // @codeCoverageIgnoreStart
            // Can't test exception since opcache config can only be set in php.ini
        } /** @noinspection BadExceptionsProcessingInspection */ catch (AnnotationCacheException $exception) {
            // Exception intentionally ignored as opcache has to be missing for annotations to fail
            return 'id';
            // @codeCoverageIgnoreEnd
        }

        return \key($ids) ?? 'id';
    }

    /**
     * Get instance_of rule string for given class.
     *
     * @param string $class
     *
     * @return string
     */
    protected function instanceOfRuleAsString(string $class): string
    {
        return \sprintf('instance_of:%s', $class);
    }

    /**
     * Get unique rule string for given target property and optional where clauses.
     *
     * @param string $target
     * @param mixed[]|null $wheres
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    protected function uniqueRuleAsString(string $target, ?array $wheres = null): string
    {
        $idProperty = $this->getIdProperty();

        $additional = '';
        foreach ($wheres ?? [] as $column => $value) {
            $additional .= \sprintf(',%s,%s', $column, $value);
        }

        $rule = \sprintf(
            'unique:%s,%s,%s,%s%s',
            \get_class($this),
            $target,
            $this->{$idProperty},
            $idProperty,
            $additional
        );

        return $rule;
    }

    /**
     * If attribute does not exist on entity, throw exception
     *
     * @param string $attribute
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException If attribute does not exist
     */
    private function checkEntityHasAttribute(string $attribute): void
    {
        if (\property_exists($this, $attribute) === false) {
            throw new InvalidArgumentException(\sprintf(
                'Property %s::%s does not exist',
                \get_class($this),
                $attribute
            ));
        }
    }

    /**
     * Get a value from a property
     *
     * @param string $property The property to get the value of
     *
     * @return mixed The property value
     */
    private function get(string $property)
    {
        $resolved = $this->resolveProperty($property);

        return $resolved ? $this->{$resolved} : null;
    }

    /**
     * Get a list of attributes or keys which can't be filled, by default nothing is guarded
     *
     * @return string[]
     */
    private function getGuardedProperties(): array
    {
        return $this->invokeEntityMethod('getGuarded');
    }

    /**
     * Get property annotations which can be used to resolve property names for this entity
     *
     * @return mixed[]
     */
    private function getResolvableAnnotations(): array
    {
        return $this->invokeEntityMethod('getPropertyAnnotations');
    }

    /** @noinspection PhpDocRedundantThrowsInspection */
    /**
     * Handle reverse association.
     *
     * @param string $association
     * @param \EoneoPay\Externals\ORM\Entity $parent
     * @param \EoneoPay\Externals\ORM\Entity|null $currentValue
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException If the collection doesn't exist on parent
     */
    private function handleReverseAssociation(string $association, Entity $parent, ?Entity $currentValue = null): void
    {
        // Determine collection method
        $collection = \sprintf('get%s', \ucfirst($association));

        // Check if this is already in collection
        $exists = $parent->{$collection}()->contains($this);

        // If attribute is not this, remove existing association
        if ($currentValue !== null &&
            $currentValue !== $this &&
            $currentValue->{$collection}()->contains($this)) {
            $currentValue->{$collection}()->removeElement($this);
        }

        // Add to collection if it doesn't already exist
        if ($exists === false) {
            $parent->$collection()->add($this);
        }
    }

    /**
     * Determine if a property exists on an entity
     *
     * @param string $property The property to test
     *
     * @return bool
     */
    private function has(string $property): bool
    {
        return $this->resolveProperty($property) !== null;
    }

    /**
     * Resolve a method on the entity which may or may not exist
     *
     * @param string $method The name of the method to invoke if it exists
     * @param mixed[]|null $default The default to return if the method doesn't exist
     *
     * @return mixed[]
     */
    private function invokeEntityMethod(string $method, ?array $default = null): array
    {
        return \method_exists($this, $method) && \is_array($this->{$method}()) ? $this->{$method}() : $default ?? [];
    }

    /**
     * Determine if a property is fillable
     *
     * @param string $property
     *
     * @return bool
     */
    private function isFillable(string $property): bool
    {
        // Get fillable and guarded arrays
        $fillable = $this->getFillableProperties();
        $guarded = $this->getGuardedProperties();

        // Resolve mappings
        $resolved = $this->resolveProperty($property);

        // Get array helper instance
        $arr = new Arr();

        /**
         * To be fillable:
         *  - The property must exist
         *  - The model must not be guarded and
         *  - The property must not be guarded and
         *  - The model or property must be fillable
         */
        return $resolved !== null &&
            \in_array('*', $this->getGuardedProperties(), true) === false &&
            $arr->search($guarded, $resolved) === null &&
            (\in_array('*', $this->getFillableProperties(), true) || $arr->search($fillable, $resolved) !== null);
    }

    /**
     * Resolve property without case sensitivity or special characters, resolves property such as
     * addressStreet to addressstreet, address_street or ADDRESSSTREET
     *
     * @param string $property The property to resolve
     *
     * @return string|null
     *
     * @codeCoverageIgnore This method just passes through to additional methods and is called basically everywhere
     */
    private function resolveProperty(string $property): ?string
    {
        // Search for property within entity properties
        try {
            return (new Arr())->search(\array_keys(\get_object_vars($this)), $property) ??
                $this->resolvePropertyFromAnnotations($property);
        } /** @noinspection BadExceptionsProcessingInspection */ catch (Exception $exception) {
            // Ignore error intentionally, this prevents endless bubbling of exceptions
            return null;
        }
    }

    /**
     * Search annotations for a property
     *
     * @param string $property The property to resolve
     *
     * @return string|null
     *
     * @throws \ReflectionException Inherited, if the class is invalid
     */
    private function resolvePropertyFromAnnotations(string $property): ?string
    {
        // If there are no resolvable annotations, return
        if (\count($this->getResolvableAnnotations()) === 0) {
            return null;
        }

        // Get annotation reader instance, if annotations aren't available return null
        try {
            $reader = new AnnotationReader();
            // @codeCoverageIgnoreStart
            // Can't test exception since opcache config can only be set in php.ini
        } /** @noinspection BadExceptionsProcessingInspection */ catch (AnnotationCacheException $exception) {
            // Exception intentionally ignored as opcache has to be missing for annotations to fail
            return null;
            // @codeCoverageIgnoreEnd
        }

        // Create a fuzzy search version of the searched property
        $fuzzy = \mb_strtolower(\preg_replace('/[^\da-zA-Z]/', '', $property));

        // Attempt to resolve property from annotations
        foreach ($this->getResolvableAnnotations() as $class => $attribute) {
            // If the annotation class doesn't exist, skip
            if (\class_exists($class) === false) {
                continue;
            }

            // Get matching annotation properties
            $annotations = $reader->getClassPropertyAnnotation(static::class, $class);

            // Search annotations for attribute
            foreach ($annotations as $realProperty => $annotation) {
                // Only look in annotations which have the correct attribute
                if (\property_exists($annotation, $attribute) === false) {
                    continue;
                }

                // Fuzzy search the attribute
                if ($fuzzy === \mb_strtolower(\preg_replace('/[^\da-zA-Z]/', '', $annotation->{$attribute}))) {
                    return $realProperty;
                }
            }
        }

        // Property was not found
        return null;
    }

    /**
     * Set the value for a property
     *
     * @param string $property The property to set
     * @param mixed $value The value to set
     *
     * @return mixed The entity the set method was called on
     */
    private function set(string $property, $value)
    {
        $resolved = $this->resolveProperty($property);

        // If property is not found or not fillable, return
        if ($resolved === null || $this->isFillable($resolved) === false) {
            return $this;
        }

        // Set property value, prefer setter over direct set
        $setter = \sprintf('set%s', \ucfirst($resolved));
        \method_exists($this, $setter) ? $this->{$setter}($value) : $this->{$resolved} = $value;

        // Run transformer if applicable
        $method = \sprintf('transform%s', \ucfirst($resolved));
        if (\method_exists($this, $method)) {
            $this->{$method}();
        }

        return $this;
    }
}
