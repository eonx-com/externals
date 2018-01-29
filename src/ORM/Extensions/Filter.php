<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Extensions;

use Doctrine\ORM\Query\Filter\SQLFilter;
use ReflectionClass;

abstract class Filter extends SQLFilter
{
    /**
     * Recursively get traits used by a reflection class
     *
     * @param ReflectionClass $reflection
     *
     * @return array
     */
    protected function getTraits(ReflectionClass $reflection): array
    {
        $traits = [];

        // Recurse through parents and grab traits they're using
        while ($reflection !== false) {
            $reflectionTraits = $reflection->getTraits();

            // Check if any traits have traits
            foreach ($reflectionTraits as $trait) {
                $traits[] = $this->getTraits($trait);
            }

            // Add reflection traits to traits array
            $traits[] = $reflectionTraits;

            // Loop to parent and grab any traits
            $reflection = $reflection->getParentClass();
        }

        // Condense to single array
        return \array_merge(...$traits);
    }
}
