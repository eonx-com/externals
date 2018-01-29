<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

abstract class Listener implements EventSubscriber
{
    /**
     * Custom annotation reader
     *
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $annotationReader;

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return \get_class_methods($this);
    }

    /**
     * Set annotation reader class since older doctrine versions do not provide an interface it must provide
     * these methods:
     *     getClassAnnotations([reflectionClass])
     *     getClassAnnotation([reflectionClass], [name])
     *     getPropertyAnnotations([reflectionProperty])
     *     getPropertyAnnotation([reflectionProperty], [name])
     *
     * @param \Doctrine\Common\Annotations\Reader $reader - annotation reader class
     */
    public function setAnnotationReader(Reader $reader)
    {
        $this->annotationReader = $reader;
    }

    /**
     * Determine if an entity has a trait, recursively
     *
     * @param \Doctrine\ORM\Event\LifeCycleEventArgs $eventArgs
     *
     * @return bool
     */
    protected function canRun(LifecycleEventArgs $eventArgs): bool
    {
        return \in_array($this->getTrait(), class_uses_recursive($eventArgs->getEntity()), true);
    }

    /**
     * Get the trait associated with the listener
     *
     * @return string
     */
    abstract protected function getTrait(): string;
}
