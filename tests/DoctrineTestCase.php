<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup as DoctrineSetup;
use EoneoPay\External\ORM\EntityManager;
use EoneoPay\External\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\External\ORM\Subscribers\ValidateEventSubscriber;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

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
    private $doctrineEM;

    /**
     * @var \EoneoPay\External\ORM\Interfaces\EntityManagerInterface
     */
    private $entityManager;

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
     * @throws \Doctrine\ORM\ORMException
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
     * @throws \Doctrine\ORM\ORMException
     */
    private function getDoctrineEntityManager(): DoctrineEntityManagerInterface
    {
        if (null !== $this->doctrineEM) {
            return $this->doctrineEM;
        }

        $validationFactory = new Factory(new Translator(new ArrayLoader(), 'en'));
        $eventManager = new EventManager();
        $eventManager->addEventSubscriber(new ValidateEventSubscriber($validationFactory));

        $config = DoctrineSetup::createAnnotationMetadataConfiguration(static::$paths, true, null, null, false);
        $this->doctrineEM = DoctrineEntityManager::create(static::$connection, $config, $eventManager);

        return $this->doctrineEM;
    }
}
