<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Bridge\LaravelDoctrine\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use EoneoPay\Externals\ORM\Subscribers\LoggableEventSubscriber;
use LaravelDoctrine\Extensions\GedmoExtension;

class LoggableExtensionStub extends GedmoExtension
{
    /**
     * @inheritdoc
     */
    public function addSubscribers(
        EventManager $manager,
        EntityManagerInterface $entityManager,
        ?Reader $reader = null
    ): void {
        $subscriber = new LoggableEventSubscriber(static function (): string {
            return 'username';
        });

        $this->addSubscriber($subscriber, $manager, $reader);
    }

    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        return [];
    }
}
