<?php

namespace KnowCoin\KnowCoinPhp\Exceptions;

use Exception;
use Throwable;

class KnowCoinException extends Exception
{
    protected ?string $endpoint;
    protected ?int $statusCode;

    public function __construct(string $message, int $code = 0, ?string $endpoint = null, ?Throwable $previous = null)
    {
        $this->endpoint = $endpoint;
        $this->statusCode = $code;

        $fullMessage = $endpoint
            ? "Error at [{$endpoint}]: {$message}"
            : $message;

        parent::__construct($fullMessage, $code, $previous);
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

}
