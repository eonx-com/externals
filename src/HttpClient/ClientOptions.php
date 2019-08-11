<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient;

use EoneoPay\Externals\HttpClient\Interfaces\ClientOptionsInterface;
use GuzzleHttp\RequestOptions;

class ClientOptions implements ClientOptionsInterface
{
    /**
     * @var float The number of seconds the HTTP client will wait to connect.
     */
    private $connectTimeout = 5;

    /**
     * @var float The number of seconds the HTTP client will wait for the response body to
     *          stream before timing out.
     */
    private $readTimeout = 0;

    /**
     * @var float The number of seconds the HTTP client will wait for a response.
     */
    private $requestTimeout = 10;

    /**
     * {@inheritdoc}
     */
    public function getConnectTimeout(): float
    {
        return $this->connectTimeout;
    }

    /**
     * Gets the numbers of seconds the HTTP client will wait for the response body to finish
     * streaming before timing out.
     *
     * @return float
     */
    public function getReadTimeout(): float
    {
        return $this->readTimeout;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTimeout(): float
    {
        return $this->requestTimeout;
    }

    /**
     * {@inheritdoc}
     */
    public function setConnectTimeout(?float $timeout): void
    {
        $this->connectTimeout = $this->validateTimeoutValue($timeout);
    }

    /**
     * Sets the numbers of seconds the HTTP client will wait for the response body to finish
     * streaming before timing out.
     *
     * @param float $timeout
     *
     * @return void
     */
    public function setReadTimeout(float $timeout): void
    {
        $this->readTimeout = $this->validateTimeoutValue($timeout);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestTimeout(?float $timeout): void
    {
        $this->requestTimeout = $this->validateTimeoutValue($timeout);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            RequestOptions::CONNECT_TIMEOUT => $this->connectTimeout,
            RequestOptions::READ_TIMEOUT => $this->readTimeout,
            RequestOptions::TIMEOUT => $this->requestTimeout
        ];
    }

    /**
     * Ensures that the provided timeout value is valid.
     *
     * @param float|null $timeout
     *
     * @return float
     */
    private function validateTimeoutValue(?float $timeout): float
    {
        // Ensure a sane value, anything less than 0.0 we can assume disabled
        if ($timeout === null || $timeout < 0.0) {
            $timeout = 0.0;
        }

        return $timeout;
    }
}
