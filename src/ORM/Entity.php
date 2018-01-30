<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM;

use EoneoPay\External\ORM\Exceptions\InvalidMethodCallException;
use EoneoPay\External\ORM\Interfaces\EntityInterface;
use EoneoPay\Utils\AnnotationReader;
use EoneoPay\Utils\Arr;
use Exception;
use JsonSerializable;

abstract class Entity implements EntityInterface, JsonSerializable
{
    /**
     * Create a new entity
     *
     * @param array|null $data The data to populate the entity with
     */
    public function __construct(?array $data = null)
    {
        $this->fill($data ?? []);
    }

    /**
     * Allow getX() and setX($value) to get and set column values
     *
     * This method searches case insensitive
     *
     * @param string $method The method being called
     * @param array $parameters Parameters passed to the method
     *
     * @return mixed Value or null on getX(), self on setX(value)
     *
     * @throws \EoneoPay\External\ORM\Exceptions\InvalidMethodCallException If the prefix or property are invalid
     */
    public function __call(string $method, array $parameters)
    {
        // Set available types
        $types = ['get', 'has', 'is', 'set'];

        // Break calling method into type (get, has, is, set) and attribute
        \preg_match('/^(' . \implode('|', $types) . ')([a-zA-Z][\w]+)$/i', $method, $matches);

        $type = mb_strtolower($matches[1] ?? '');
        $property = $this->resolveProperty($matches[2] ?? '');

        // The property being accessed must exist and the type must be valid if one of these things
        // aren't true throw an exception
        if ($type === '' || $property === null || ($type === 'set' && !$this->isFillable($property))) {
            throw new InvalidMethodCallException(
                sprintf('Call to undefined method %s::%s()', \get_class($this), $method)
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
                $this->set($property, reset($parameters));
                break;
        }

        return $this;
    }

    /**
     * Populate a entity from an array of data
     *
     * @param array $data The data to fill the entity with
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
     * Return contents for serializing as json
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Serialize entity as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
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
     * Associate an entity in a bidirectional way from the owning side
     *
     * @param string $attribute The attribute on the entity for the many to one association
     * @param \EoneoPay\External\ORM\Entity $parent The entity to associate
     * @param string $association The attribute on the entity for the one to many collection
     *
     * @return mixed The original entity for fluency
     */
    protected function associate(string $attribute, Entity $parent, string $association)
    {
        // Determine collection method
        $collection = sprintf('get%s', $association);

        // Check if this is already in collection
        $exists = $parent->{$collection}()->contains($this);

        // If attribute is already parent and collection contains this item, return
        if ($exists && $this->{$attribute} === $parent) {
            return $this;
        }

        // If attribute is not this, remove existing association
        if ($this->{$attribute} !== null &&
            $this->{$attribute} !== $this &&
            $this->{$attribute}->{$collection}()->contains($this)) {
            $this->{$attribute}->{$collection}()->removeElement($this);
        }

        // Set parent
        $this->{$attribute} = $parent;

        // Add to collection if it doesn't already exist
        if (!$exists) {
            $parent->{$collection}()->add($this);
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

        return $resolved ? $this->{$resolved} : null;
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
     * Get a list of attributes or keys which are able to be filled, by default all fields can be set
     *
     * @return array
     */
    private function getFillableProperties(): array
    {
        return $this->invokeEntityMethod('getFillable', ['*']);
    }

    /**
     * Get a list of attributes or keys which can't be filled, by default nothing is guarded
     *
     * @return array
     */
    private function getGuardedProperties(): array
    {
        return $this->invokeEntityMethod('getGuarded');
    }

    /**
     * Get property annotations which can be used to resolve property names for this entity
     *
     * @return array
     */
    private function getResolvableAnnotations(): array
    {
        return $this->invokeEntityMethod('getPropertyAnnotations');
    }

    /**
     * Resolve a method on the entity which may or may not exist
     *
     * @param string $method The name of the method to invoke if it exists
     * @param array|null $default The default to return if the method doesn't exist
     *
     * @return array
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
            !\in_array('*', $this->getGuardedProperties(), true) &&
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
            // Ignore error intentionally, this prevents endless bulbbing of exceptions
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
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException Inherited, if opcache isn't caching comments
     * @throws \ReflectionException Inherited, if the class is invalid
     */
    private function resolvePropertyFromAnnotations(string $property): ?string
    {
        // If there are no resolvable annotations, return
        if (\count($this->getResolvableAnnotations()) === 0) {
            return null;
        }

        // Get annotation reader instance
        $reader = new AnnotationReader();

        // Create a fuzzy search version of the searched property
        $fuzzy = \mb_strtolower(\preg_replace('/[^\da-zA-Z]/', '', $property));

        // Attempt to resolve property from annotations
        foreach ($this->getResolvableAnnotations() as $class => $attribute) {
            // If the annotation class doesn't exist, skip
            if (!\class_exists($class)) {
                continue;
            }

            // Get matching annotation properties
            $annotations = $reader->getClassPropertyAnnotation(static::class, $class);

            // Search annotations for attribute
            foreach ($annotations as $realProperty => $annotation) {
                // Only look in annotations which have the correct attribute
                if (!\property_exists($annotation, $attribute)) {
                    continue;
                }

                // Fuzzy search the attribute
                if (\mb_strtolower(\preg_replace('/[^\da-zA-Z]/', '', $annotation->{$attribute})) === $fuzzy) {
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
        if ($resolved === null || !$this->isFillable($resolved)) {
            return $this;
        }

        // Set property value
        $this->{$resolved} = $value;

        // Run transformer if applicable
        $method = sprintf('transform%s', ucfirst($resolved));
        if (method_exists($this, $method)) {
            $this->{$method}();
        }

        return $this;
    }
}
