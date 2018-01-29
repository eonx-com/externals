<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\ORM\Extensions\Extension as ExtensionContract;

abstract class Extension implements ExtensionContract
{
    /**
     * Add event subscriptions
     *
     * @param \Doctrine\Common\EventManager $manager
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Doctrine\Common\Annotations\Reader|null $reader
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $entityManager is not used but is implemented in abstract class
     */
    public function addSubscribers(
        EventManager $manager,
        EntityManagerInterface $entityManager,
        Reader $reader = null
    ): void {
        foreach ($this->getListeners() as $listener) {
            $this->addSubscriber(new $listener, $manager, $reader);
        }
    }

    /**
     * Get the filters used for queries with this extension
     *
     * @return array
     */
    abstract public function getFilters(): array;

    /**
     * Get the listeners with this extension
     *
     * @return array
     */
    abstract public function getListeners(): array;

    /**
     * Add an event subscription
     *
     * @param \EoneoPay\External\ORM\Extensions\Listener $listener
     * @param \Doctrine\Common\EventManager $manager
     * @param \Doctrine\Common\Annotations\Reader|null $reader
     *
     * @return void
     */
    protected function addSubscriber(Listener $listener, EventManager $manager, Reader $reader = null): void
    {
        if ($reader instanceof Reader) {
            $listener->setAnnotationReader($reader);
        }

        $manager->addEventSubscriber($listener);
    }
}
