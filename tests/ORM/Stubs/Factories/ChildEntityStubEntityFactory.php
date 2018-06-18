<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs\Factories;

use EoneoPay\Externals\ORM\EntityFactory;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub;
use Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub;

class ChildEntityStubEntityFactory extends EntityFactory
{
    /**
     * Create entity.
     *
     * @param mixed[] $data
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidArgumentException
     */
    public function create(array $data): EntityInterface
    {
        $this->handleDefaultRelationEntity($data, 'parent', ParentEntityStub::class);

        return new ChildEntityStub($data);
    }

    /**
     * Get default date used for test.
     *
     * @return mixed[]
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
