<?php
// I DID NOT MAKE THIS CLASS AT ALL, IT'S AI GENERATED,
// SOMEDAY I'LL GIVE SOME TIME TO MAKE A ROBUST EXCEPTION HANDLER
// BUT NOT NOW

namespace app\Handler;

use app\Core\Response;
use InvalidArgumentException;
use JsonException;
use LogicException;
use PDOException;
use RuntimeException;
use Throwable;

class ExceptionHandler
{
    /**
     * Error message mapping for specific error codes
     */
    private const array ERROR_MESSAGES = [
        '23502' => 'Required fields are missing.',
        '23503' => 'Referenced record does not exist.',
        '23505' => 'This record already exists.',
    ];

    /**
     * HTTP status codes mapping for different exception types
     */
    private const array STATUS_CODES = [
        RuntimeException::class => 400,
        InvalidArgumentException::class => 400,
        LogicException::class => 400,
        PDOException::class => 500,
    ];

    /**
     * @var bool Whether to show detailed error information
     */
    private static bool $debug = false;

    /**
     * Configure the exception handler
     */
    public static function configure(bool $debug = false): void
    {
        self::$debug = $debug;
    }

    /**
     * Handle all types of exceptions
     */
    public static function handle(Throwable $e): never
    {
        $status = self::determineStatusCode($e);
        $message = self::determineMessage($e);

        // Log the original error
        self::logError($e);

        try {
            // Create error details if debug is enabled
            $errors = self::$debug ? [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace(),
            ] : null;

            Response::error($message, $status, $errors)->send();
        } catch (Throwable $je) {
            // Fallback response if Response class fails
            http_response_code(500);
            try {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Response encoding error',
                        'errors' => null
                    ],
                    JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
                );
            } catch (JsonException) {
                echo '{"success":false,"message":"Internal server error","errors":null}';
            }
        }

        exit(1);
    }

    /**
     * Extract error code from error message
     */
    private static function extractErrorCode(string $message): ?string
    {
        // Check for SQLSTATE format
        if (preg_match('/SQLSTATE\[(\d{5})\]/', $message, $matches)) {
            return $matches[1];
        }

        // Check for direct error code mentions
        foreach (array_keys(self::ERROR_MESSAGES) as $code) {
            if (str_contains($message, $code)) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Determine if message contains database error
     */
    private static function isDbError(string $message): bool
    {
        $dbErrorPatterns = [
            'SQLSTATE',
            'duplicate key',
            'violates unique constraint',
            'not-null constraint',
            'foreign key constraint',
        ];

        foreach ($dbErrorPatterns as $pattern) {
            if (stripos($message, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine the appropriate HTTP status code for the exception
     */
    private static function determineStatusCode(Throwable $e): int
    {
        foreach (self::STATUS_CODES as $class => $code) {
            if ($e instanceof $class) {
                return $code;
            }
        }

        return 500;
    }

    /**
     * Determine the appropriate error message
     */
    private static function determineMessage(Throwable $e): string
    {
        $originalMessage = $e->getMessage();

        // Check if it's a database-related error in any exception type
        if (self::isDbError($originalMessage)) {
            $errorCode = self::extractErrorCode($originalMessage);
            if ($errorCode && isset(self::ERROR_MESSAGES[$errorCode])) {
                return self::ERROR_MESSAGES[$errorCode];
            }
            return 'A database error occurred.';
        }

        // Handle non-database RuntimeException and LogicException
        if ($e instanceof RuntimeException || $e instanceof LogicException) {
            return $originalMessage;
        }

        return self::$debug ? $originalMessage : 'An unexpected error has occurred.';
    }

    /**
     * Log the error to the system log
     */
    private static function logError(Throwable $e): void
    {
        $message = sprintf(
            '[%s] [%s] %s in %s:%d',
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        error_log($message);

        if (self::$debug) {
            error_log($e->getTraceAsString());
        }
    }
}