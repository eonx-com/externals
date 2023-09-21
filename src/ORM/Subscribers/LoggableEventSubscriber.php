<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Subscribers;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\Column;
use EoneoPay\Externals\ORM\Interfaces\MagicEntityInterface;
use EoneoPay\Utils\AnnotationReader;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Gedmo\Loggable\LoggableListener as BaseLoggableListener;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;

final class LoggableEventSubscriber extends BaseLoggableListener
{
    /**
     * Closure to resolve username.
     *
     * @var callable
     */
    private $usernameResolver;

    /**
     * LoggableEventSubscriber constructor.
     *
     * @param callable $usernameResolver
     */
    public function __construct(callable $usernameResolver)
    {
        $this->usernameResolver = $usernameResolver;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception Underlying extension throws exception on failure
     */
    public function createLogEntry($action, $object, LoggableAdapter $loggableAdapter): ?AbstractLogEntry
    {
        $logEntry = parent::createLogEntry($action, $object, $loggableAdapter);

        if ($logEntry !== null) {
            $logEntry->setUsername(\call_user_func($this->usernameResolver) ?? 'not_set');
        }

        return $logEntry;
    }

    /**
     * Get the configuration for specific object class
     * if cache driver is present it scans it also
     *
     * @param \Doctrine\Persistence\ObjectManager $objectManager
     * @param string $class
     *
     * @return mixed[]
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException If opcache extension isn't loaded
     *
     * phpcs:disable
     * Unable to add parameter typehint due to interface
     */
    public function getConfiguration(ObjectManager $objectManager, $class): array
    {
        // phpcs:enable

        $config = parent::getConfiguration($objectManager, $class);
        $entity = new $class();

        if (($entity instanceof MagicEntityInterface) === false || \count($this->getEntityFillable($entity)) === 0) {
            return $config;
        }

        $config['loggable'] = true;
        $config['versioned'] = $this->getEntityFillable($entity);

        return $config;
    }

    /**
     * Get fillable properties for given entity.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\MagicEntityInterface $entity
     *
     * @return string[]
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException If opcache extension isn't loaded
     */
    private function getEntityFillable(MagicEntityInterface $entity): array
    {
        if (\in_array('*', $entity->getFillableProperties(), true) === false) {
            return $entity->getFillableProperties();
        }

        return \array_keys((new AnnotationReader())->getClassPropertyAnnotation(\get_class($entity), Column::class));
    }
}
