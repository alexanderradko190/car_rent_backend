<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class ServiceException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $status = 400,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
