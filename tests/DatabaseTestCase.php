<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External;

use EoneoPay\External\ORM\Entity;
use ReflectionClass;
use ReflectionException;

/**
 * @covers nothing
 */
abstract class DatabaseTestCase extends TestCase
{
    /**
     * Get entity contents via reflection, this is used so there's no reliance
     * on entity methods such as toArray for tests to work
     *
     * @param \EoneoPay\External\ORM\Entity $entity The entity to get data from
     *
     * @return array
     */
    protected function getEntityContents(Entity $entity): array
    {
        // Get properties available for this entity
        try {
            $reflection = new ReflectionClass(\get_class($entity));
        } /** @noinspection BadExceptionsProcessingInspection */ catch (ReflectionException $exception) {
            // Ignore error and return no values
            return [];
        }

        $properties = $reflection->getProperties();

        // Get property values
        $contents = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $contents[$property->name] = $property->getValue($entity);
        }

        return $contents;
    }
}
