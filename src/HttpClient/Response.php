<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Utils\Arr;
use EoneoPay\Utils\Repository;

class Response extends Repository implements ResponseInterface
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * Response constructor.
     *
     * @param array|null $data
     * @param int|null $statusCode
     * @param array|null $headers
     * @param string|null $content
     */
    public function __construct(
        array $data = null,
        int $statusCode = null,
        array $headers = null,
        string $content = null
    ) {
        parent::__construct($data);

        $this->content = $content ?? '';
        $this->headers = $headers ?? [];
        $this->statusCode = $statusCode ?? 200;
    }

    /**
     * Get response content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get response header.
     *
     * @param string $key
     *
     * @return null|string
     */
    public function getHeader(string $key): ?string
    {
        return (new Arr())->get($this->headers, $key);
    }

    /**
     * Get response headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get response status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Determine if the response is successful or not
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
}
