<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use EoneoPay\Externals\ORM\Entity;
use EoneoPay\Externals\ORM\EntityManager;
use EoneoPay\Externals\ORM\Extensions\SoftDeleteExtension;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\Externals\ORM\Subscribers\ValidateEventSubscriber;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use ReflectionClass;
use ReflectionException;

/** @noinspection EfferentObjectCouplingInspection */

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) To handle magic of Doctrine configuration
 */
abstract class DoctrineTestCase extends TestCase
{
    /**
     * @var string[]
     */
    public static $connection = [
        'driver' => 'pdo_sqlite',
        'path' => ':memory:'
    ];

    /**
     * @var string[]
     */
    public static $paths = [
        // Paths to your entities folder and stubs folder
        __DIR__ . '/ORM/Stubs'
    ];

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $doctrine;

    /**
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Get doctrine entity manager.
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Doctrine
     */
    protected function getDoctrineEntityManager(): DoctrineEntityManagerInterface
    {
        if ($this->doctrine !== null) {
            return $this->doctrine;
        }

        $cache = new ArrayCache();
        // Standard annotation reader
        $annotationReader = new AnnotationReader();

        // Create a driver chain for metadata reading
        $driverChain = new MappingDriverChain();

        // Now we want to register our application entities,
        // for that we need another metadata driver used for Entity namespace
        $annotationDriver = new AnnotationDriver(
            $annotationReader, // our cached annotation reader
            self::$paths // paths to look in
        );
        // NOTE: driver for application Entity can be different, Yaml, Xml or whatever
        // register annotation driver for our application Entity fully qualified namespace
        $driverChain->addDriver($annotationDriver, 'Tests\\EoneoPay\\Externals\\ORM\\Stubs');

        // General ORM configuration
        $config = new Configuration();
        $config->setProxyDir(\sys_get_temp_dir());
        $config->setProxyNamespace('Proxy');
        $config->setAutoGenerateProxyClasses(true); // this can be based on production config.
        // Register metadata driver
        $config->setMetadataDriverImpl($driverChain);
        // Use our already initialized cache driver
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        // Create validator
        $validationFactory = new Factory(new Translator(new ArrayLoader(), 'en'));
        // Instantiate event manager with validation subscriber
        $eventManager = new EventManager();
        $eventManager->addEventSubscriber(new ValidateEventSubscriber($validationFactory));

        // Finally, create entity manager
        $this->doctrine = DoctrineEntityManager::create(self::$connection, $config, $eventManager);

        foreach ($this->getLaravelDoctrineExtensions() as $extension) {
            /** @var \LaravelDoctrine\ORM\Extensions\Extension $extension */
            $extension->addSubscribers($eventManager, $this->doctrine, $annotationReader);

            foreach ($extension->getFilters() as $name => $filter) {
                $config->addFilter($name, $filter);
                $this->doctrine->getFilters()->enable($name);
            }
        }

        return $this->doctrine;
    }

    /**
     * Get entity contents via reflection, this is used so there's no reliance
     * on entity methods such as toArray for tests to work
     *
     * @param \EoneoPay\Externals\ORM\Entity $entity The entity to get data from
     *
     * @return mixed[]
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

    /**
     * Get entity manager.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        if ($this->entityManager !== null) {
            return $this->entityManager;
        }

        $this->entityManager = new EntityManager($this->getDoctrineEntityManager());

        return $this->entityManager;
    }

    /**
     * Create database.
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\Tools\ToolsException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    protected function setUp(): void
    {
        parent::setUp();

        (new SchemaTool($this->getDoctrineEntityManager()))
            ->createSchema($this->getDoctrineEntityManager()->getMetadataFactory()->getAllMetadata());
    }

    /**
     * Drop database.
     *
     * @return void
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function tearDown(): void
    {
        (new SchemaTool($this->getDoctrineEntityManager()))->dropDatabase();

        parent::tearDown();
    }

    /**
     * Get enabled laravel doctrine extensions.
     *
     * @return \LaravelDoctrine\ORM\Extensions\Extension[]
     */
    private function getLaravelDoctrineExtensions(): array
    {
        return [
            new SoftDeleteExtension()
        ];
    }
}
