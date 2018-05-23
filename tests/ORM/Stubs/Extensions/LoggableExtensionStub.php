<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use EoneoPay\Externals\ORM\Subscribers\LoggableEventSubscriber;
use LaravelDoctrine\Extensions\GedmoExtension;

class LoggableExtensionStub extends GedmoExtension
{
    /**
     * Add loggable subscriber to Doctrine events system.
     *
     * @param \Doctrine\Common\EventManager $manager
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Doctrine\Common\Annotations\Reader|null $reader
     *
     * @return void
     */
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, ?Reader $reader = null): void
    {
        $subscriber = new LoggableEventSubscriber(function () {
            return 'username';
        });

        $this->addSubscriber($subscriber, $manager, $reader);
    }

    /**
     * Get filters provided by the extension.
     *
     * @return mixed[]
     */
    public function getFilters(): array
    {
        return [];
    }
}
