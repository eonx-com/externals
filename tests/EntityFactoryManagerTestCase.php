<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals;

use EoneoPay\Externals\ORM\EntityFactoryManager;
use EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface;

class EntityFactoryManagerTestCase extends DoctrineTestCase
{
    /**
     * Get entity factory manager.
     *
     * @param mixed[]|null $namespaceMapping
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    protected function getEntityFactoryManager(?array $namespaceMapping = null): EntityFactoryManagerInterface
    {
        $entityFactoryManager = new EntityFactoryManager($this->getEntityManager(), [
            \realpath(\dirname(__DIR__)) . '/tests/ORM/Stubs/Factories'
        ]);

        if (\is_array($namespaceMapping)) {
            foreach ($namespaceMapping as $factoriesNamespace => $entitiesNamespace) {
                $entityFactoryManager->addNamespaceMapping($factoriesNamespace, $entitiesNamespace);
            }
        }

        return $entityFactoryManager;
    }
}
