<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs;

class UnwritableFilesystemStub extends VirtualFilesystemAdapterStub
{
    /**
     * @inheritdoc
     *
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    public function __construct(?string $root = null)
    {
        parent::__construct($root);

        // Create read-only file system
        \chmod((string)$this->getPathPrefix(), 0400);
    }
}
