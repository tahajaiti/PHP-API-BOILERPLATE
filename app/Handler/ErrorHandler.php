<?php
// I DID NOT MAKE THIS CLASS AT ALL, IT'S AI GENERATED,
// SOMEDAY I'LL GIVE SOME TIME TO MAKE A ROBUST ERROR HANDLER
// BUT NOT NOW

namespace app\Handler;

use app\Core\Response;
use ErrorException;
use JsonException;
use Throwable;

class ErrorHandler
{
    /**
     * Severity levels that should be treated as fatal errors
     */
    private const FATAL_ERRORS = [
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        E_USER_ERROR,
        E_RECOVERABLE_ERROR
    ];

    /**
     * @var bool Whether to show detailed error information
     */
    private static bool $debug = false;

    /**
     * @var string|null Custom error log file path
     */
    private static ?string $logFile = null;

    /**
     * Configure the error handler
     */
    public static function configure(bool $debug = false, ?string $logFile = null): void
    {
        self::$debug = $debug;
        self::$logFile = $logFile;

        error_reporting(self::$debug ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_STRICT);
        ini_set('display_errors', '0');
        ini_set('html_errors', '0');
    }

    /**
     * Register all error handlers
     */
    public static function register(): void
    {
        // Set default timezone if not set
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }

        // Configure error logging
        ini_set('log_errors', '1');
        if (self::$logFile) {
            ini_set('error_log', self::$logFile);
        }

        // Register handlers
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
        set_exception_handler([ExceptionHandler::class, 'handle']);
    }

    /**
     * Handle PHP errors and convert them to exceptions
     */
    public static function handleError(
        int $severity,
        string $message,
        string $file,
        int $line,
        ?array $context = null
    ): bool {
        // Check if error should be reported
        if (!(error_reporting() & $severity)) {
            return false;
        }

        // Log the error
        self::logError($severity, $message, $file, $line);

        // Handle fatal errors immediately
        if (in_array($severity, self::FATAL_ERRORS, true)) {
            self::sendErrorResponse($message, $file, $line);
            exit(1);
        }

        // Convert other errors to exceptions
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * Handle fatal errors during shutdown
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error === null || !in_array($error['type'], self::FATAL_ERRORS, true)) {
            return;
        }

        self::logError($error['type'], $error['message'], $error['file'], $error['line']);
        self::sendErrorResponse($error['message'], $error['file'], $error['line']);
    }

    /**
     * Send error response using Response class
     */
    private static function sendErrorResponse(string $message, string $file, int $line): void
    {
        try {
            $errors = null;
            if (self::$debug) {
                $errors = [
                    'file' => $file,
                    'line' => $line,
                    'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                ];
            }

            Response::error(
                self::$debug ? $message : 'A system error occurred.',
                500,
                $errors
            )->send();
        } catch (Throwable) {
            // Fallback if Response class fails
            http_response_code(500);
            try {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'A system error occurred.',
                        'errors' => null
                    ],
                    JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
                );
            } catch (JsonException) {
                echo '{"success":false,"message":"A system error occurred.","errors":null}';
            }
        }
    }

    /**
     * Log error details
     */
    private static function logError(int $severity, string $message, string $file, int $line): void
    {
        $severityName = match ($severity) {
            E_ERROR, E_USER_ERROR => 'ERROR',
            E_WARNING, E_USER_WARNING => 'WARNING',
            E_NOTICE, E_USER_NOTICE => 'NOTICE',
            E_DEPRECATED, E_USER_DEPRECATED => 'DEPRECATED',
            default => 'UNKNOWN'
        };

        error_log(sprintf(
            '[%s] [%s] %s in %s:%d',
            date('Y-m-d H:i:s'),
            $severityName,
            $message,
            $file,
            $line
        ));
    }
}