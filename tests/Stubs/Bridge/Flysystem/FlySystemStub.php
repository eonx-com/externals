<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Bridge\Flysystem;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\Handler;
use League\Flysystem\PluginInterface;
use Tests\EoneoPay\Externals\Stubs\StubBase;

class FlySystemStub extends StubBase implements FilesystemInterface
{
    /**
     * {@inheritDoc}
     */
    public function has($path)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function read($path)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function readStream($path)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function listContents($directory = '', $recursive = false)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata($path)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function getSize($path)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function getMimetype($path)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function getTimestamp($path)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function getVisibility($path)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function write($path, $contents, array $config = [])
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function writeStream($path, $resource, array $config = [])
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function update($path, $contents, array $config = [])
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function updateStream($path, $resource, array $config = [])
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function rename($path, $newpath)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function copy($path, $newpath)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDir($dirname)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function createDir($dirname, array $config = [])
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function setVisibility($path, $visibility)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, $contents, array $config = [])
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function putStream($path, $resource, array $config = [])
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function readAndDelete($path)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, Handler $handler = null)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * {@inheritDoc}
     */
    public function addPlugin(PluginInterface $plugin)
    {
        $this->saveCalls(__FUNCTION__, \get_defined_vars());
        return $this->returnOrThrowResponse(__FUNCTION__);
    }

    /**
     * Get the calls to to has().
     *
     * @return mixed[]
     */
    public function getHasCalls(): array
    {
        return $this->getCalls(__FUNCTION__);
    }

    /**
     * Get the calls to listContents().
     *
     * @return mixed[]
     */
    public function getListContentsCalls(): array
    {
        return $this->getCalls(__FUNCTION__);
    }
}
