<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\Vendor\Guzzle;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @coversNothing
 */
class ClientStub implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfig($option = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function request($method, $uri, array $options = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function requestAsync($method, $uri, array $options = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request, array $options = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function sendAsync(RequestInterface $request, array $options = [])
    {
    }
}
