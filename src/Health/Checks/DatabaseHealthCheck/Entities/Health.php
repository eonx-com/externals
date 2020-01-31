<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Health\Checks\DatabaseHealthCheck\Entities;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Entity;

/**
 * @ORM\Entity()
 */
class Health extends Entity
{
    /**
     * Health id
     *
     * @var string
     *
     * @ORM\Column(type="guid", name="id")
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id()
     */
    protected $healthId;

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty(): string
    {
        return 'healthId';
    }
}
