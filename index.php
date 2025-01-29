<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');

header('Content-Type: application/json');

require_once "vendor/autoload.php";

use app\Handler\ErrorHandler;
use app\Handler\ExceptionHandler;

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