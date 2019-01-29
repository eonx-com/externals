<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs;

class UnwritableFilesystemStub extends VirtualFilesystemAdapterStub
{
    /**
     * @inheritdoc
     */
    public function __construct(?string $root = null)
    {
        parent::__construct($root);

        // Create read-only file system
        \chmod($this->getPathPrefix(), 0400);
    }
}
