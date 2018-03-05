<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup as DoctrineSetup;
use EoneoPay\External\ORM\Entity;
use EoneoPay\External\ORM\EntityManager;
use EoneoPay\External\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\External\ORM\Subscribers\ValidateEventSubscriber;
use Gedmo\DoctrineExtensions;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use ReflectionClass;
use ReflectionException;

abstract class DoctrineTestCase extends TestCase
{
    /**
     * @var array
     */
    public static $connection = [
        'driver' => 'pdo_sqlite',
        'path' => ':memory:'
    ];

    /**
     * @var array
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
     * @var \EoneoPay\External\ORM\Interfaces\EntityManagerInterface
     */
    private $entityManager;

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

    /**
     * Get entity manager.
     *
     * @return \EoneoPay\External\ORM\Interfaces\EntityManagerInterface
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        if (null !== $this->entityManager) {
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\Tools\ToolsException
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
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function tearDown(): void
    {
        (new SchemaTool($this->getDoctrineEntityManager()))->dropDatabase();

        parent::tearDown();
    }

    /**
     * Get doctrine entity manager.
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     */
    private function getDoctrineEntityManager(): DoctrineEntityManagerInterface
    {
        if (null !== $this->doctrine) {
            return $this->doctrine;
        }

        $cache = new ArrayCache();
        // standard annotation reader
        $annotationReader = new AnnotationReader();

        // create a driver chain for metadata reading
        $driverChain = new MappingDriverChain();

        // now we want to register our application entities,
        // for that we need another metadata driver used for Entity namespace
        $annotationDriver = new AnnotationDriver(
            $annotationReader, // our cached annotation reader
            self::$paths // paths to look in
        );
// NOTE: driver for application Entity can be different, Yaml, Xml or whatever
// register annotation driver for our application Entity fully qualified namespace
        $driverChain->addDriver($annotationDriver, 'Tests\\EoneoPay\\External\\ORM\\Stubs');

// general ORM configuration
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('Proxy');
        $config->setAutoGenerateProxyClasses(true); // this can be based on production config.
// register metadata driver
        $config->setMetadataDriverImpl($driverChain);
// use our allready initialized cache driver
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        // Finally, create entity manager
        $this->doctrine = DoctrineEntityManager::create(self::$connection, $config);


//        $validationFactory = new Factory(new Translator(new ArrayLoader(), 'en'));
//        $eventManager = new EventManager();
//        $eventManager->addEventSubscriber(new ValidateEventSubscriber($validationFactory));

        //$config = DoctrineSetup::createAnnotationMetadataConfiguration(static::$paths, true);
//        $config = DoctrineSetup::createConfiguration(true);
//
//        $driverChain = new MappingDriverChain();
//        $driverChain->addDriver($config->getMetadataDriverImpl(), 'Entity');//\Doctrine\ORM\Mapping\Entity::class);
//        $reader = $config->getMetadataDriverImpl()->getReader();
//
//        DoctrineExtensions::registerMappingIntoDriverChainORM(
//            $driverChain,
//            $reader
//        );
//
//        $config->setMetadataDriverImpl($driverChain);
//
//        $this->doctrine = DoctrineEntityManager::create(static::$connection, $config);//, $eventManager);

//
//        foreach ($this->getLaravelDoctrineExtensions() as $extension) {
//            /** @var \LaravelDoctrine\ORM\Extensions\Extension $extension */
//            $extension->addSubscribers($eventManager, $this->doctrine, $reader);
//
//            foreach ($extension->getFilters() as $name => $filter) {
//                $config->addFilter($name, $filter);
//                $this->doctrine->getFilters()->enable($name);
//            }
//        }

        return $this->doctrine;
    }

    /**
     * Get enabled laravel doctrine extensions.
     *
     * @return array
     */
    private function getLaravelDoctrineExtensions(): array
    {
        return [

        ];
    }
}
