<?php

namespace app\Interfaces;

use app\Core\Request;
use app\Core\Response;


/**
 * Interface MiddlewareInt
 *
 * Defines the structure for middleware classes in the application. Middleware
 * is used to intercept, analyze, and potentially modify incoming HTTP requests
 * and outgoing HTTP responses.
 */
interface MiddlewareInt
{
    /**
     * Handles the given request and returns an appropriate response.
     *
     * @param Request $request The request object to be handled.
     * @return Response|null The response object or null if no response is generated.
     */
    public function handle(Request $request): ?Response;
}