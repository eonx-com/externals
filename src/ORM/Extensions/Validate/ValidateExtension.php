<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Extensions\Validate;

use EoneoPay\External\ORM\Extensions\Extension;

class ValidateExtension extends Extension
{
    /**
     * Get the filters used for queries with this extension
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [];
    }

    /**
     * Get the listeners with this extension
     *
     * @return array
     */
    public function getListeners(): array
    {
        return [ValidateListener::class];
    }
}
