<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Subscribers;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\Column;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
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
     * @inheritdoc
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     */
    public function getConfiguration(ObjectManager $objectManager, $class): array
    {
        $config = parent::getConfiguration($objectManager, $class);
        $entity = new $class();

        if (($entity instanceof EntityInterface) === false || empty($this->getEntityFillable($entity))) {
            return $config;
        }

        $config['loggable'] = true;
        $config['versioned'] = $this->getEntityFillable($entity);

        return $config;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception Underlying extension throws exception on failure
     */
    protected function createLogEntry($action, $object, LoggableAdapter $loggableAdapter): ?AbstractLogEntry
    {
        $logEntry = parent::createLogEntry($action, $object, $loggableAdapter);

        if ($logEntry !== null) {
            $logEntry->setUsername(\call_user_func($this->usernameResolver) ?? 'not_set');
        }

        return $logEntry;
    }

    /**
     * Get fillable properties for given entity.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity
     *
     * @return string[]
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException
     */
    private function getEntityFillable(EntityInterface $entity): array
    {
        if (\in_array('*', $entity->getFillableProperties(), true) === false) {
            return $entity->getFillableProperties();
        }

        return \array_keys((new AnnotationReader())->getClassPropertyAnnotation(\get_class($entity), Column::class));
    }
}
