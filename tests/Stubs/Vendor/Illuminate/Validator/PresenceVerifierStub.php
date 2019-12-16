<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Validator;

use Illuminate\Validation\PresenceVerifierInterface;

class PresenceVerifierStub implements PresenceVerifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = []): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiCount($collection, $column, array $values, array $extra = []): int
    {
        return 0;
    }
}
