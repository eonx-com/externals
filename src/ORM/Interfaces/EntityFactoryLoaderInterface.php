<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

interface EntityFactoryLoaderInterface
{
    /**
     * Return the list of available factories class names.
     *
     * @return string[]
     */
    public function loadFactoriesClassNames(): array;
}
