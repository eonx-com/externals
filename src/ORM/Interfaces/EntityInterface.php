<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

interface EntityInterface
{
    /**
     * Allow getX() and setX($value) to get and set column values
     *
     * This method searches case insensitive
     *
     * @param string $method The method being called
     * @param array $parameters Parameters passed to the method
     *
     * @return mixed Value or null on getX(), self on setX(value)
     */
    public function __call(string $method, array $parameters);

    /**
     * Fill an entity from an array
     *
     * @param array $data The array to fill the entity from
     *
     * @return void
     */
    public function fill(array $data): void;

    /**
     * Get a list of attributes or keys which are able to be filled, by default all fields can be set
     *
     * @return array
     */
    public function getFillableProperties(): array;

    /**
     * Get entity id.
     *
     * @return null|string|int
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     * @throws \ReflectionException
     */
    public function getId();
}
