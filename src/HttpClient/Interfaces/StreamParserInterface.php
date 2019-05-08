<?php
declare(strict_types=1);

namespace EoneoPay\Externals\HttpClient\Interfaces;

use Psr\Http\Message\StreamInterface;

interface StreamParserInterface
{
    /**
     * Parses the content out of the body into an array.
     *
     * @param \Psr\Http\Message\StreamInterface $stream
     *
     * @return mixed[]|null
     */
    public function parse(StreamInterface $stream): ?array;
}
