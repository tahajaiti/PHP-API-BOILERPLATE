<?php
error_reporting(E_ALL);  // Error/Exception engine, always use E_ALL to see all errors and exceptions
ini_set('display_errors', '0'); // Error/Exception display, use 0 for off

header('Content-Type: application/json');
//header('Access-Control-Allow-Origin: http://localhost:5173'); // Allow requests from specified domain
//Apache normally runs on port 80, and the frontend usually runs on a different port
//In my case, it's 5173. So, I need to allow requests from that port.

require_once "vendor/autoload.php";

use app\Handler\ErrorHandler;
use app\Handler\ExceptionHandler;

//Configure and register the error and exception handlers
ExceptionHandler::configure(true);
ErrorHandler::configure(
    debug: true,
    logFile: __DIR__ . '/logs/error.log'
);
ErrorHandler::register();

try {
    require_once "config.php";
    require_once "app/Routes/api.php";
} catch (Throwable $e) {
    ExceptionHandler::handle($e);
}