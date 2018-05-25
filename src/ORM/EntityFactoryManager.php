<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException;
use EoneoPay\Externals\ORM\Interfaces\EntityFactoryInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\Utils\Str;

class EntityFactoryManager implements EntityFactoryManagerInterface
{
    /**
     * Entity array to cache entities instances.
     *
     * @var mixed[]
     */
    private $entityInstances = [];

    /**
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Factory array to cache factories classes.
     *
     * @var string[]
     */
    private $factoryClasses;

    /**
     * Factory array to cache factories instances.
     *
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityFactoryInterface[]
     */
    private $factoryInstances = [];

    /**
     * Paths array where to find factories classes.
     *
     * @var string[]
     */
    private $factoryPaths;

    /**
     * Mapping between factories and entities namespace as follows: [<factoriesNamespace> => <entitiesNamespace>]
     *
     * @var string[]
     */
    private $namespaceMapping = [];

    /**
     * EntityFactoryManager constructor.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface $entityManager
     * @param string[] $factoryPaths
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function __construct(EntityManagerInterface $entityManager, array $factoryPaths)
    {
        if (empty($factoryPaths)) {
            throw new InvalidArgumentException('EntityFactory paths must be provided to EntityFactoryManager');
        }

        $this->entityManager = $entityManager;
        $this->factoryPaths = $factoryPaths;
    }

    /**
     * Add mapping between factories and entities namespace.
     *
     * @param string $factoriesNamespace Namespace where to find entity factories
     * @param string $entitiesNamespace Namespace where to find related entities
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityFactoryManagerInterface
     */
    public function addNamespaceMapping(
        string $factoriesNamespace,
        string $entitiesNamespace
    ): EntityFactoryManagerInterface {
        $factoriesNamespace = $this->formatNamespace($factoriesNamespace);
        $entitiesNamespace = $this->formatNamespace($entitiesNamespace);

        $this->namespaceMapping[$factoriesNamespace] = $entitiesNamespace;

        return $this;
    }

    /**
     * Persist the test entity and return it.
     *
     * @param string $className
     * @param mixed[]|null $data
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException
     * @throws \EoneoPay\Externals\ORM\Exceptions\ORMException
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \ReflectionException If invalid class detected in factories folders
     */
    public function create(string $className, ?array $data = null): EntityInterface
    {
        // Initiate entity instances array
        if (isset($this->entityInstances[$className]) === false) {
            $this->entityInstances[$className] = [];
        }

        // Merge passed data into default data
        $data = \array_merge($this->getDefaultData($className), $data ?? []);

        // Create key for this data set
        $key = \md5(\json_encode($data));

        // If entity exists return
        if (isset($this->entityInstances[$className][$key]) === true) {
            return $this->entityInstances[$className][$key];
        }

        // Create and persist new entity instance
        $entity = $this->getEntityFactory($className)->create($data);
        $this->entityManager->persist($entity);

        return $this->entityInstances[$className][$key] = $entity;
    }

    /**
     * Get entity factory default data based on entity class name.
     *
     * @param string $className
     *
     * @return mixed[]
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \ReflectionException If invalid class detected in factories folders
     */
    public function getDefaultData(string $className): array
    {
        return $this->getEntityFactory($className)->getDefaultData();
    }

    /**
     * Get entity factory based on entity class name.
     *
     * @param string $className
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityFactoryInterface
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \ReflectionException If invalid class detected in factories folders
     */
    public function getEntityFactory(string $className): EntityFactoryInterface
    {
        if (isset($this->factoryInstances[$className])) {
            return $this->factoryInstances[$className];
        }

        $class = $this->resolve($className);

        /** @var \EoneoPay\Externals\ORM\Interfaces\EntityFactoryInterface $entityFactory */
        $entityFactory = new $class($this);

        return $this->factoryInstances[$className] = $entityFactory;
    }

    /**
     * Add backslashes to namespace if not present.
     *
     * @param string $namespace
     *
     * @return string
     */
    private function formatNamespace(string $namespace): string
    {
        if ((new Str())->endsWith($namespace, '\\')) {
            return $namespace;
        }

        return \sprintf('%s\\', $namespace);
    }

    /**
     * Get factory classes.
     *
     * @return string[]
     *
     * @throws \ReflectionException If invalid class detected in factories folders
     */
    private function getFactoryClasses(): array
    {
        if ($this->factoryClasses !== null) {
            return $this->factoryClasses;
        }

        return $this->factoryClasses = (new EntityFactoryLoader($this->factoryPaths))->loadFactoriesClassNames();
    }

    /**
     * Get the test entity namespace based on the entity's namespace.
     *
     * @param string $className
     *
     * @return string
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     * @throws \ReflectionException If invalid class detected in factories folders
     */
    private function resolve(string $className): string
    {
        if (empty($this->namespaceMapping)) {
            throw new InvalidArgumentException(\sprintf(
                'No namespace mapping configured, please see %s::addNamespaceMapping method.',
                EntityFactoryManagerInterface::class
            ));
        }

        $factoryClass = null;

        foreach ($this->namespaceMapping as $factoriesNamespace => $entitiesNamespace) {
            $class = \sprintf(
                '%s%sEntityFactory',
                $factoriesNamespace,
                \str_replace($entitiesNamespace, '', $className)
            );

            if (\in_array($class, $this->getFactoryClasses(), true) === false) {
                continue;
            }

            $factoryClass = $class;
            break;
        }

        if ($factoryClass === null) {
            throw new InvalidArgumentException(\sprintf(
                'EntityFactory for %s not found in any of configured namespaces: %s',
                $className,
                \implode(',', \array_keys($this->namespaceMapping))
            ));
        }

        return $factoryClass;
    }
}
