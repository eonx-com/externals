<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException;
use EoneoPay\Externals\ORM\Exceptions\InvalidRelationshipException;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Interfaces\MagicEntityInterface;
use EoneoPay\Utils\Arr;
use EoneoPay\Utils\Exceptions\InvalidXmlTagException;
use EoneoPay\Utils\XmlConverter;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) Complexity required to enable smaller entities in application
 */
abstract class Entity extends SimpleEntity implements MagicEntityInterface
{
    /**
     * Create a new entity.
     *
     * @param mixed[]|null $data The data to populate the entity with
     */
    public function __construct(?array $data = null)
    {
        $this->fill($data ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function fill(array $data): void
    {
        // Loop through data and set values, set will automatically skip invalid or
        // non-fillable properties.
        foreach ($data as $property => $value) {
            $resolved = $this->resolveProperty($property);
            if ($resolved === null) {
                // No property exists.

                continue;
            }

            $method = \sprintf('set%s', \ucfirst($resolved));
            $callable = [$this, $method];

            if (\is_callable($callable) === false) {
                // @codeCoverageIgnoreStart
                // The callable isnt callable. This wont happen - this entity
                // implements __call which means is_callable will always return
                // true.
                continue;
                // @codeCoverageIgnoreEnd
            }

            try {
                $callable($value);
            } /** @noinspection BadExceptionsProcessingInspection */ catch (InvalidMethodCallException $exception) {
                // Using __call means we might throw an InvalidMethodCallException when
                // the property is guarded, but the BC behaviour for fill()
                // is to ignore and skip the property in $data.
            }
        }
    }

    /**
     * Resolve property without case sensitivity or special characters, resolves property such as
     * addressStreet to addressstreet, address_street or ADDRESSSTREET.
     *
     * Method copied/overridden and suppressed to keep it private. Protected methods
     * will be usable from entities but this method should remain private.
     *
     * @noinspection SenselessMethodDuplicationInspection PhpMissingParentCallCommonInspection
     *
     * @param string $property The property to resolve
     *
     * @return string|null
     */
    private function resolveProperty(string $property): ?string
    {
        // All properties will be camel case within the object
        $property = \lcfirst($property);

        return \property_exists($this, $property)
            ? $property :
            (new Arr())->search($this->getObjectProperties(), $property);
    }

    /**
     * {@inheritdoc}
     *
     * Overrides and adds functionality to check for guarded properties in set calls.
     */
    public function __call(string $method, array $parameters)
    {
        // Look for a setter call
        $matched = \preg_match('/^set([a-zA-Z][\w]+)$/i', $method, $matches);

        if ($matched === 0) {
            // The call is not for a setter.
            return parent::__call($method, $parameters);
        }

        if ($this->isFillable($matches[1]) === true) {
            // The property is fillable, lets fill it and if any transformers run,
            // run those.
            $return = parent::__call($method, $parameters);

            $resolved = $this->resolveProperty($matches[1]);

            // Run transformer if applicable
            $method = \sprintf('transform%s', \ucfirst($resolved ?? ''));
            $callable = [$this, $method];
            if (\method_exists($this, $method) === true && \is_callable($callable) === true) {
                $callable();
            }

            return $return;
        }

        // We've got a setter and the property is not fillable.
        throw new InvalidMethodCallException(\sprintf(
            'Call to undefined method %s::%s()',
            \get_class($this),
            $method
        ));
    }

    /**
     * Determine if a property is fillable.
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
         *  - The model or property must be fillable.
         */
        return $resolved !== null &&
            \in_array('*', $this->getGuardedProperties(), true) === false &&
            $arr->search($guarded, $resolved) === null &&
            (\in_array('*', $this->getFillableProperties(), true) || $arr->search($fillable, $resolved) !== null);
    }

    /**
     * {@inheritdoc}
     */
    public function getFillableProperties(): array
    {
        return $this->invokeEntityMethod('getFillable', null, ['*']);
    }

    /**
     * Resolve a method on the entity which may or may not exist.
     *
     * @param string $method The name of the method to invoke if it exists
     * @param mixed[]|null $args
     * @param mixed[]|null $default The default to return if the method doesn't exist
     *
     * @return mixed[]
     */
    private function invokeEntityMethod(string $method, ?array $args = null, ?array $default = null): array
    {
        $callable = [$this, $method];

        // If method is missing, not callable or doesn't return an array, use default
        if (\method_exists($this, $method) === false ||
            \is_callable($callable) === false ||
            \is_array($callable()) === false) {
            return $default ?? [];
        }

        return $callable(...$args ?? []);
    }

    /**
     * Get a list of attributes or keys which can't be filled, by default nothing is guarded.
     *
     * @return string[]
     */
    private function getGuardedProperties(): array
    {
        return $this->invokeEntityMethod('getGuarded');
    }

    /**
     * Method is defined for BC - the ValidatableInterface requires this method.
     *
     * @return string[]
     */
    public function getValidatableProperties(): array
    {
        return $this->getObjectProperties();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Serialize entity as an array.
     *
     * @return mixed[]
     */
    abstract public function toArray(): array;

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
     * Associate an entity in a bidirectional way from the owning side.
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
        $current = $this->callPropertyGetter($attribute);

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
     * Get property value via getter.
     *
     * @param string $property The property to get
     *
     * @return mixed
     */
    protected function callPropertyGetter(string $property)
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
     * Associate an entity in a bidirectional way on a N-N relation.
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
     * Update a collection with an entity.
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
}
