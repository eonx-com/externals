<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\ORM;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use LaravelDoctrine\ORM\Extensions\Extension;

class ResolveTargetEntityExtension implements Extension
{
    /**
     * @var \Doctrine\ORM\Tools\ResolveTargetEntityListener
     */
    private $rtel;

    /**
     * ResolveTargetEntityExtension constructor.
     *
     * @param \Doctrine\ORM\Tools\ResolveTargetEntityListener $rtel
     */
    public function __construct(ResolveTargetEntityListener $rtel)
    {
        $this->rtel = $rtel;
    }

    /**
     * @inheritdoc
     */
    public function addSubscribers(
        EventManager $eventManager,
        EntityManagerInterface $entityManager,
        ?Reader $reader = null
    ): void {
        $eventManager->addEventSubscriber($this->rtel);
    }

    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        return [];
    }
}
