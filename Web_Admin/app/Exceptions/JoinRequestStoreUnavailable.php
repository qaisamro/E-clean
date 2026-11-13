<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Raised when the customer_join_requests store is required but unavailable
 * (the table does not exist yet, e.g. the migration has not run).
 *
 * Callers MUST treat this as "approval cannot be verified" and fail closed
 * (deny protected access) instead of assuming an account is approved.
 */
class JoinRequestStoreUnavailable extends RuntimeException
{
    public function __construct(string $message = 'The join-request store is unavailable.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
