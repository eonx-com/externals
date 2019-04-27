<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Utils\Collection;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

final class Response extends Collection implements ResponseInterface
{
    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    private $response;

    /**
     * Response constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param mixed[]|null $data
     */
    public function __construct(PsrResponseInterface $response, ?array $data = null)
    {
        parent::__construct($data);

        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): string
    {
        return $this->response->getBody()->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(string $key): ?string
    {
        $headers = $this->response->getHeader($key);

        return \reset($headers) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * @inheritdoc
     */
    public function getPsrResponse(): PsrResponseInterface
    {
        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful(): bool
    {
        return $this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300;
    }
}
