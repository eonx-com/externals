<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Request\Interfaces\RequestInterface;
use Illuminate\Http\Request as HttpRequest;

final class Request implements RequestInterface
{
    /**
     * Incoming http request.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create new request instance.
     *
     * @param \Illuminate\Http\Request $request Incoming http request
     */
    public function __construct(HttpRequest $request)
    {
        // Create symfony request and merge in passed request
        $httpRequest = $request::capture();
        $this->request = $request->duplicate(
            \array_merge($httpRequest->query->all(), $request->query->all()),
            \array_merge($httpRequest->request->all(), $request->request->all()),
            \array_merge($httpRequest->attributes->all(), $request->attributes->all()),
            \array_merge($httpRequest->cookies->all(), $request->cookies->all()),
            \array_merge($httpRequest->files->all(), $request->files->all()),
            \array_merge($httpRequest->server->all(), $request->server->all())
        );

        // Set headers due to this being a special cadse
        $this->request->headers->replace(\array_merge($httpRequest->headers->all(), $request->headers->all()));
    }

    /**
     * {@inheritdoc}
     */
    public function getClientIp(): ?string
    {
        return $this->request->getClientIp();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(string $key, $default = null)
    {
        return $this->request->headers->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): string
    {
        return $this->request->getHost();
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): ?string
    {
        return $this->request->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return $this->request->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function input(?string $key = null, $default = null)
    {
        return $this->request->input($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $data): RequestInterface
    {
        $this->request->merge($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $data): RequestInterface
    {
        $this->request->replace($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader(string $key, $value): RequestInterface
    {
        $this->request->headers->set($key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->request->all();
    }
}
