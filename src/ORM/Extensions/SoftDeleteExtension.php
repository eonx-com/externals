<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use EoneoPay\Externals\ORM\Subscribers\SoftDeleteEventSubscriber;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use LaravelDoctrine\Extensions\GedmoExtension;

class SoftDeleteExtension extends GedmoExtension
{
    /**
     * Add subscribers to doctrine event environment.
     *
     * @param \Doctrine\Common\EventManager $manager
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Doctrine\Common\Annotations\Reader|null $reader
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Inherited from LaravelDoctrineExtensions
     */
    public function addSubscribers(
        EventManager $manager,
        EntityManagerInterface $entityManager,
        ?Reader $reader = null
    ): void {
        $this->addSubscriber(new SoftDeleteEventSubscriber(), $manager, $reader);
    }

    /**
     * Get list of filters provided by the extension.
     *
     * @return string[]
     */
    public function getFilters(): array
    {
        return [
            'soft-deleteable' => SoftDeleteableFilter::class
        ];
    }
}
