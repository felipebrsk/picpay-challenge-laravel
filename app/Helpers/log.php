<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('logWithContext')) {
    /**
     * Log the exception with context.
     *
     * @param string $message
     * @param \Exception|\Throwable $e
     * @param string $severity
     * @return void
     */
    function logWithContext(
        string $message,
        \Exception|\Throwable $e,
        string $severity = 'error',
    ): void {
        Log::$severity($message, [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
