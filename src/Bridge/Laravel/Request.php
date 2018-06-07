<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Request\Interfaces\RequestInterface;
use Illuminate\Http\Request as HttpRequest;

class Request implements RequestInterface
{
    /**
     * Incoming http request
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create new request instance
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
     * Get a header by name
     *
     * @param string $key The key to find
     * @param mixed $default The default to return if key isn't found
     *
     * @return mixed
     */
    public function getHeader(string $key, $default = null)
    {
        return $this->request->headers->get($key, $default);
    }

    /**
     * Retrieve the server host
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->request->getHost();
    }

    /**
     * Get user from request
     *
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->request->getUser();
    }

    /**
     * Determine if the request contains a given input item key
     *
     * @param string $key The key to find
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->request->has($key);
    }

    /**
     * Retrieve an input item from the request
     *
     * @param string|null $key The key to retrieve from the input
     * @param mixed $default The default value to use if key isn't set
     *
     * @return mixed
     */
    public function input(?string $key = null, $default = null)
    {
        return $this->request->input($key, $default);
    }

    /**
     * Merge a new data set into an existing request
     *
     * @param mixed[] $data The data to merge into the request
     *
     * @return static
     */
    public function merge(array $data): self
    {
        $this->request->merge($data);

        return $this;
    }

    /**
     * Replace request with a new set of data
     *
     * @param mixed[] $data The data to replace in the request
     *
     * @return static
     */
    public function replace(array $data): self
    {
        $this->request->replace($data);

        return $this;
    }

    /**
     * Set a header on the request
     *
     * @param string $key The key to set
     * @param mixed $value The value to set against the header
     *
     * @return static
     */
    public function setHeader(string $key, $value): self
    {
        $this->request->headers->set($key, $value);

        return $this;
    }

    /**
     * Retrieve the entire request as an array
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return $this->request->all();
    }
}
