<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\TestCases;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Doctrine\ORM\Tools\SchemaTool;
use EoneoPay\Externals\Bridge\Laravel\Translator;
use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\Bridge\LaravelDoctrine\Extensions\ResolveTargetEntityExtension;
use EoneoPay\Externals\Bridge\LaravelDoctrine\Extensions\SoftDeleteExtension;
use EoneoPay\Externals\ORM\Entity;
use EoneoPay\Externals\ORM\EntityManager;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\Externals\ORM\Listeners\GenerateUniqueValue;
use EoneoPay\Externals\ORM\Subscribers\ValidateEventSubscriber;
use EoneoPay\Utils\Generator;
use Exception;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator as IlluminateTranslator;
use Illuminate\Validation\Factory;
use ReflectionClass;
use ReflectionException;
use Tests\EoneoPay\Externals\Stubs\Bridge\LaravelDoctrine\Extensions\LoggableExtensionStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Repositories\RepositoryStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) To handle magic of Doctrine configuration
 */
abstract class ORMTestCase extends TestCase
{
    /**
     * Connection parameters
     *
     * @var string[]
     */
    private static $connection = [
        'driver' => 'pdo_sqlite',
        'path' => ':memory:'
    ];

    /**
     * Paths to look for entities in
     *
     * @var string[]
     */
    private static $paths = [
        // Paths to your entities folder and stubs folder
        __DIR__ . '/../Stubs/ORM/Entities',
        __DIR__ . '/../../vendor/gedmo/doctrine-extensions/lib/Gedmo/Loggable/Entity'
    ];

    /**
     * Default repository class to use
     *
     * @var string
     */
    private static $repository = RepositoryStub::class;

    /**
     * Generated sql for migrations
     *
     * @var string
     */
    private static $sql;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $doctrine;

    /**
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Whether the database has been seeded with migration sql or not
     *
     * @var bool
     */
    private $seeded = false;

    /**
     * Require the annotations for doctrine and extensions.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // Require Doctrine annotations
        require_once __DIR__ . '/../../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php';
        // Require Gedmo annotations
        require_once __DIR__ . '/../../vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping/Annotation/All.php';
    }

    /**
     * Lazy load database schema only when required
     *
     * @return void
     */
    protected function createSchema(): void
    {
        // If schema is already created, return
        if ($this->seeded === true) {
            return;
        }

        // Create schema
        try {
            // Use doctrine entity manager
            $entityManager = $this->getDoctrineEntityManager();

            // If schema hasn't been defined, define it, this will happen once per run
            if (self::$sql === null) {
                $tool = new SchemaTool($entityManager);
                $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
                self::$sql = \implode(';', $tool->getCreateSchemaSql($metadata));
            }

            $entityManager->getConnection()->exec(self::$sql);
        } catch (Exception $exception) {
            self::fail(\sprintf('Exception thrown when creating database schema: %s', $exception->getMessage()));
        }

        $this->seeded = true;
    }

    /**
     * Get doctrine entity manager.
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Constructor of EntityManager is protected so static method must be used
     */
    protected function getDoctrineEntityManager(): DoctrineEntityManagerInterface
    {
        if ($this->doctrine !== null) {
            return $this->doctrine;
        }

        // Set defaults for annotations
        $annotationDriver = null;
        $annotationReader = null;

        // Create cache for metadata
        $cache = new ArrayCache();

        // Create a driver chain for metadata reading
        $driverChain = new MappingDriverChain();

        // Standard annotation reader
        try {
            $annotationReader = new AnnotationReader();

            // Now we want to register our application entities,
            // for that we need another metadata driver used for Entity namespace
            $annotationDriver = new AnnotationDriver($annotationReader, self::$paths);
        } catch (AnnotationException $exception) {
            self::fail(\sprintf(
                'Exception thrown when creating instantiating annotation reader: %s',
                $exception->getMessage()
            ));
        }

        // NOTE: driver for application Entity can be different, Yaml, Xml or whatever
        // register annotation driver for our application Entity fully qualified namespace
        $driverChain->addDriver($annotationDriver, 'Tests\\EoneoPay\\Externals\\Stubs\\ORM\\Entities');
        $driverChain->addDriver($annotationDriver, 'Gedmo\\Loggable\\Entity');

        // General ORM configuration
        $config = new Configuration();
        $config->setAutoGenerateProxyClasses(true);
        $config->setMetadataDriverImpl($driverChain);
        $config->setMetadataCacheImpl($cache);
        $config->setProxyDir(\sys_get_temp_dir());
        $config->setProxyNamespace('Proxy');
        $config->setQueryCacheImpl($cache);

        // Set default repository class
        try {
            $config->setDefaultRepositoryClassName(self::$repository);
        } catch (ORMException $exception) {
            self::fail(\sprintf('Exception thrown when setting custom repository: %s', $exception->getMessage()));
        }

        // Create translator
        $illuminateTranslator = new IlluminateTranslator(new ArrayLoader(), 'en');
        $translator = new Translator($illuminateTranslator);

        // Create validator
        $validator = new Validator(new Factory($illuminateTranslator));

        // Instantiate event manager with validation subscriber
        $eventManager = new EventManager();
        $eventManager->addEventListener(Events::prePersist, new GenerateUniqueValue(new Generator(), $translator));
        $eventManager->addEventSubscriber(new ValidateEventSubscriber($translator, $validator));

        // Finally, create entity manager
        try {
            $this->doctrine = DoctrineEntityManager::create(self::$connection, $config, $eventManager);
        } catch (ORMException $exception) {
            self::fail(\sprintf('Exception thrown when creating instantiating doctrine: %s', $exception->getMessage()));
        }

        // Load extensions
        foreach ($this->getLaravelDoctrineExtensions() as $extension) {
            $extension->addSubscribers($eventManager, $this->doctrine, $annotationReader);

            foreach ($extension->getFilters() as $name => $filter) {
                $config->addFilter($name, $filter);
                $this->doctrine->getFilters()->enable($name);
            }
        }

        // Build sql for migration
        $this->createSchema();

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
     * Get enabled laravel doctrine extensions.
     *
     * @return \LaravelDoctrine\ORM\Extensions\Extension[]
     */
    private function getLaravelDoctrineExtensions(): array
    {
        return [
            new ResolveTargetEntityExtension(new ResolveTargetEntityListener()),
            new SoftDeleteExtension(),
            new LoggableExtensionStub()
        ];
    }
}
