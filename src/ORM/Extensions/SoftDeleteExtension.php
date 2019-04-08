<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use EoneoPay\Externals\ORM\Subscribers\SoftDeleteEventSubscriber;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use LaravelDoctrine\Extensions\GedmoExtension;

final class SoftDeleteExtension extends GedmoExtension
{
    /**
     * @inheritdoc
     */
    public function addSubscribers(
        EventManager $manager,
        EntityManagerInterface $entityManager,
        ?Reader $reader = null
    ): void {
        $this->addSubscriber(new SoftDeleteEventSubscriber(), $manager, $reader);
    }

    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        return [
            'soft-deleteable' => SoftDeleteableFilter::class
        ];
    }
}
