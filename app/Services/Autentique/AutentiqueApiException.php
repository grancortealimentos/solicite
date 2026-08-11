<?php

namespace App\Services\Autentique;

use RuntimeException;

class AutentiqueApiException extends RuntimeException
{
    /**
     * @param  array<string, array<int, string>>  $errors
     */
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly array $errors = [],
    ) {
        parent::__construct($message);
    }

    public function isValidationError(): bool
    {
        return $this->status === 422;
    }

    public function isUnauthorized(): bool
    {
        return $this->status === 401;
    }

    public function isNotFound(): bool
    {
        return $this->status === 404;
    }
}
