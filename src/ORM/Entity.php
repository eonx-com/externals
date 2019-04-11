<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException;
use EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Utils\Arr;
use EoneoPay\Utils\Exceptions\InvalidXmlTagException;
use EoneoPay\Utils\XmlConverter;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) Complexity required to enable smaller entities in application
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
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException If the method doesn't exist or is immutable
     */
    public function __call(string $method, array $parameters)
    {
        // Extract type and property from method
        [$type, $property] = $this->getTypeAndPropertyFromMethod($method);

        // Perform action - code coverage disabled due to phpdbg not seeing case statements
        switch ($type) {
            case 'get': // @codeCoverageIgnore
            case 'has': // @codeCoverageIgnore
            case 'is': // @codeCoverageIgnore
                $callable = [$this, $type === 'is' ? 'get' : $type];

                if (\is_callable($callable) === true) {
                    return ($type === 'is') ? (bool)$callable($property) : $callable($property);
                }

                // If call didn't happen, return null/false depending on type
                return $type === 'get' ? null : false;

            case 'set': // @codeCoverageIgnore
                // Return original instance for fluency
                $this->set($property, \reset($parameters));
                break;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fill(array $data): void
    {
        // Loop through data and set values, set will automatically skip invalid or non-fillable properties
        foreach ($data as $property => $value) {
            $this->set($property, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFillableProperties(): array
    {
        return $this->invokeEntityMethod('getFillable', ['*']);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->get($this->getIdProperty());
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties(): array
    {
        return \array_keys(\get_object_vars($this));
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function toJson(): string
    {
        return \json_encode($this->toArray()) ?: '';
    }

    /**
     * {@inheritdoc}
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
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException If relationship property does not exist
     */
    protected function associate(string $attribute, ?EntityInterface $parent = null, ?string $association = null)
    {
        // If attribute does not exist on entity, throw exception
        if (\property_exists($this, $attribute) === false) {
            throw new InvalidRelationshipException(\sprintf(
                'Attempted to create relationship on property %s::%s but the property does not exist',
                \get_class($this),
                $attribute
            ));
        }

        // Get current attribute value
        $current = $this->getValue($attribute);

        // If current property value is already the parent, return
        if ($current === $parent) {
            return $this;
        }

        $this->{$attribute} = $parent;

        // If foreign key column explicitly defined assign parent id
        $foreignKey = \sprintf('%sId', $attribute);
        if (\property_exists($this, $foreignKey)) {
            $this->{$foreignKey} = ($parent instanceof EntityInterface) === true ? $parent->getId() : null;
        }

        // If association set, handle it
        if ($association !== null) {
            try {
                $this->handleReverseAssociation($association, $current, $parent);
            } /** @noinspection PhpRedundantCatchClauseInspection */ catch (InvalidMethodCallException $exception) {
                // We have to throw a different exception otherwise it's caught higher and it dies silently.
                throw new InvalidRelationshipException(
                    \sprintf(
                        'Attempted to create relationship on property %s::%s but the property does not exist',
                        ($parent instanceof EntityInterface) === true ? \get_class($parent) : $attribute,
                        $association
                    ),
                    0,
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
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException If relationship property does not exist
     */
    protected function associateMultiple(string $attribute, EntityInterface $parent, ?string $association = null)
    {
        $this->addToCollection($attribute, $parent, $this);

        // Add bi-directional if association is provided
        if ($association !== null) {
            $this->addToCollection($association, $this, $parent);
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
     * Update a collection with an entity
     *
     * @param string $attribute The attribute which contains the collection
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $child The entity to add to the collection
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $parent The entity to add the child to
     *
     * @return static
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException If relationship property does not exist
     */
    private function addToCollection(string $attribute, EntityInterface $child, EntityInterface $parent)
    {
        // Determine parent collection method
        $collection = [$parent, \sprintf('get%s', \ucfirst($attribute))];

        try {
            // Is callable will always return true since __call is used, so try/catch
            if (\is_callable($collection) === true) {
                // If parent collection contains this item, return
                if ($collection()->contains($child)) {
                    return $this;
                }

                // Add entity to parent collection
                $collection()->add($child);
            }
        } catch (InvalidMethodCallException $exception) {
            throw new InvalidRelationshipException(
                \sprintf('Property %s::%s does not exist', \get_class($parent), $attribute),
                0,
                $exception
            );
        }

        return $this;
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

        return $resolved !== null ? $this->{$resolved} : null;
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
     * Get type and property from method
     *
     * @param string $method The method being called
     *
     * @return string[]
     */
    private function getTypeAndPropertyFromMethod(string $method): array
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

        return [$type, $property];
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
        $getter = [$this, \sprintf('get%s', \ucfirst($property))];

        return \is_callable($getter) === true ? $getter() : null;
    }

    /**
     * Handle reverse association.
     *
     * @param string $association
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null $existing The existing parent
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null $new The new parent relationship
     *
     * @return void
     */
    private function handleReverseAssociation(
        string $association,
        ?EntityInterface $existing = null,
        ?EntityInterface $new = null
    ): void {
        // Determine collection methods
        $getter = \sprintf('get%s', \ucfirst($association));
        $existingCollection = [$existing, $getter];
        $newCollection = [$new, $getter];

        // If there is a existing parent, remove
        if (($existing instanceof EntityInterface) === true &&
            $existing !== $this &&
            \is_callable($existingCollection) === true &&
            $existingCollection()->contains($this) === true) {
            $existingCollection()->removeElement($this);
        }

        // Add to new parent if applicable
        if (($new instanceof EntityInterface) === true &&
            \is_callable($newCollection) === true &&
            $newCollection()->contains($this) === false) {
            $newCollection()->add($this);
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
        $callable = [$this, $method];

        return \method_exists($this, $method) === true &&
        \is_callable($callable) === true &&
        \is_array($callable()) === true ?
            $callable() :
            $default ?? [];
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
        $resolved = (string)$this->resolveProperty($property);

        // If property doesn't exist or is not fillable, return
        if ($this->has($property) === false || $this->isFillable($resolved) === false) {
            return $this;
        }

        // Set property value, prefer setter over direct set
        $setter = \sprintf('set%s', \ucfirst($resolved));
        $callable = [$this, $setter];
        \method_exists($this, $setter) === true && \is_callable($callable) === true ?
            $callable($value) :
            $this->{$resolved} = $value;

        // Run transformer if applicable
        $method = \sprintf('transform%s', \ucfirst($resolved));
        $callable = [$this, $method];
        if (\method_exists($this, $method) === true && \is_callable($callable) === true) {
            $callable();
        }

        return $this;
    }
}
