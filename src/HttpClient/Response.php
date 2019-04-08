<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Utils\Arr;
use EoneoPay\Utils\Collection;

final class Response extends Collection implements ResponseInterface
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var string[]
     */
    private $headers;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * Response constructor.
     *
     * @param mixed[]|null $data
     * @param int|null $statusCode
     * @param mixed[]|null $headers
     * @param string|null $content
     */
    public function __construct(
        ?array $data = null,
        ?int $statusCode = null,
        ?array $headers = null,
        ?string $content = null
    ) {
        parent::__construct($data);

        $this->content = $content ?? '';
        $this->headers = $headers ?? [];
        $this->statusCode = $statusCode ?? 200;
    }

    /**
     * @inheritdoc
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @inheritdoc
     */
    public function getHeader(string $key): ?string
    {
        return (new Arr())->get($this->headers, $key);
    }

    /**
     * @inheritdoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @inheritdoc
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
}
