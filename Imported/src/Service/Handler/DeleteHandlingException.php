<?php
declare(strict_types=1);

namespace App\Service\Handler;

final class DeleteHandlingException extends \RuntimeException
{
    public function __construct(
        string $message = 'Delete failed',
        public readonly string $codeName = 'delete_failed',
        int $code = 500,
        int $status = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
