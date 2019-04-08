<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException;
use EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Utils\Arr;
use EoneoPay\Utils\Exceptions\InvalidXmlTagException;
use EoneoPay\Utils\XmlConverter;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) Complexity covered by unit tests
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

        // Perform action - code coverage disabled due to phpdbg not seeing case statements
        switch ($type) {
            case 'get': // @codeCoverageIgnore
                return $this->get($property);

            case 'has': // @codeCoverageIgnore
                return $this->has($property);

            case 'is': // @codeCoverageIgnore
                // Always return a boolean
                return (bool)$this->get($property);

            case 'set': // @codeCoverageIgnore
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
     * @return int|string|null
     */
    public function getId()
    {
        return $this->get($this->getIdProperty());
    }

    /**
     * @inheritdoc
     */
    public function getProperties(): array
    {
        return \array_keys(\get_object_vars($this));
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
        return \json_encode($this->toArray()) ?: '';
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
     * Get the id property for this entity
     *
     * @return string
     */
    abstract protected function getIdProperty(): string;

    /**
     * Associate an entity in a bidirectional way from the owning side
     *
     * @param string $attribute The attribute on the entity for the many to one association
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null $parent The entity to associate
     * @param string|null $association The attribute on the parent for the one to many collection
     *
     * @return mixed The original entity for fluency
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException If attribute does not exist
     */
    protected function associate(string $attribute, ?EntityInterface $parent = null, ?string $association = null)
    {
        // If attribute does not exist on entity, throw exception
        $this->checkEntityHasAttribute($attribute);

        // Get current attribute value
        $currentValue = $this->getValue($attribute);

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
            } /** @noinspection PhpRedundantCatchClauseInspection */ catch (InvalidMethodCallException $exception) {
                // We have to throw a different exception otherwise it's caught higher and it dies silently.
                throw new InvalidArgumentException(
                    \sprintf(
                        'Property %s::%s does not exist',
                        $parent !== null ? \get_class($parent) : '',
                        $association
                    ),
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
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $parent The entity to associate
     * @param string|null $association The attribute on the second entity for the many to many collection
     *
     * @return mixed The original entity for fluency
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException If attribute does not exist
     */
    protected function associateMultiple(string $attribute, EntityInterface $parent, ?string $association = null)
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
     * @param string $target The target entity
     * @param mixed[]|null $wheres Additional/optional where clauses
     *
     * @return string
     */
    protected function uniqueRuleAsString(string $target, ?array $wheres = null): string
    {
        $additional = '';
        foreach ($wheres ?? [] as $column => $value) {
            $additional .= \sprintf(',%s,%s', $column, $value);
        }

        $rule = \sprintf(
            'unique:%s,%s,%s,%s%s',
            \get_class($this),
            $target,
            $this->getValue($this->getIdProperty()),
            $this->getIdProperty(),
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
     * Get property value via getter
     *
     * @param string $property The property to get
     *
     * @return mixed
     */
    private function getValue(string $property)
    {
        $getter = \sprintf('get%s', \ucfirst($property));

        return $this->{$getter}();
    }

    /** @noinspection PhpDocRedundantThrowsInspection */

    /**
     * Handle reverse association.
     *
     * @param string $association
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null $parent
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null $currentValue
     *
     * @return void
     */
    private function handleReverseAssociation(
        string $association,
        ?EntityInterface $parent,
        ?EntityInterface $currentValue = null
    ): void {
        // Determine collection method
        $collection = \sprintf('get%s', \ucfirst($association));

        // Check if this is already in collection
        $exists = $parent !== null && $parent->{$collection}()->contains($this);

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
        // All properties will be camel case within the object
        $property = \lcfirst($property);

        return \property_exists($this, $property) ? $property : (new Arr())->search($this->getProperties(), $property);
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
