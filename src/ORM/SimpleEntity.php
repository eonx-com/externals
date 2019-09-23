<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Utils\Arr;

/**
 * This abstract entity class is designed as a stop-gap solution to removal
 * of the base entity entirely.
 */
abstract class SimpleEntity implements EntityInterface
{
    /**
     * Allow getX() and setX($value) to get and set column values.
     *
     * This method searches case insensitive
     *
     * @param string $method The method being called
     * @param mixed[] $parameters Parameters passed to the method
     *
     * @return mixed Value or null on getX(), self on setX(value)
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
        if ($type === '' || $property === null) {
            throw new InvalidMethodCallException(
                \sprintf('Call to undefined method %s::%s()', \get_class($this), $method)
            );
        }

        // Perform action - code coverage disabled due to phpdbg not seeing case statements
        switch ($type) {
            // @codeCoverageIgnoreStart
            case 'get':
            case 'has':
            case 'is':
                /** @codeCoverageIgnoreEnd */
                return $this->callGettableMethod($type, $property);

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
    public function getId()
    {
        return $this->get($this->getIdProperty());
    }

    /**
     * Get the id property for this entity.
     *
     * @return string
     */
    abstract protected function getIdProperty(): string;

    /**
     * Returns all properties on the entity.
     *
     * @return string[]
     */
    protected function getObjectProperties(): array
    {
        $properties = \array_keys(\get_object_vars($this));

        return \array_filter($properties, static function ($property): bool {
            // Skip all properties that have __ at the start, they are reserved properties
            // and should not be processed.
            return \strncmp($property, '__', 2) !== 0;
        });
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
            $this->getId(),
            $this->getIdProperty(),
            $additional
        );

        return $rule;
    }

    /**
     * Perform a gettable call on an entity.
     *
     * @param string $method The method being called
     * @param string $property The property the method is being called on
     *
     * @return mixed The property value, or null/false if method isn't callable
     */
    private function callGettableMethod(string $method, string $property)
    {
        // Determine callable method
        $callable = [$this, $method === 'is' ? 'get' : $method];

        // Only call method if it's callable
        if (\is_callable($callable) === true) {
            return ($method === 'is') ? (bool)$callable($property) : $callable($property);
        }

        // If call didn't happen, return null/false depending on type - this is unlikely since the
        // property is verified via the __call method and has() and get() exist in this class
        return $method === 'get' ? null : false; // @codeCoverageIgnore
    }

    /**
     * Get a value from a property.
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
     * Determine if a property exists on an entity.
     *
     * @noinspection PhpUnusedPrivateMethodInspection This method is used by __call
     *
     * @param string $property The property to test
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod) This method is used by __call
     */
    private function has(string $property): bool
    {
        return $this->resolveProperty($property) !== null;
    }

    /**
     * Resolve property without case sensitivity or special characters, resolves property such as
     * addressStreet to addressstreet, address_street or ADDRESSSTREET.
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
     * Set the value for a property.
     *
     * @param string $property The property to set
     * @param mixed $value The value to set
     *
     * @return mixed The entity the set method was called on
     */
    private function set(string $property, $value)
    {
        $resolved = (string)$this->resolveProperty($property);

        // Set property value, prefer setter over direct set
        $setter = \sprintf('set%s', \ucfirst($resolved));
        $callable = [$this, $setter];
        \method_exists($this, $setter) === true && \is_callable($callable) === true ?
            $callable($value) :
            $this->{$resolved} = $value;

        return $this;
    }
}
