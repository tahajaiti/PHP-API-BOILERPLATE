<?php
namespace app\Middleware;

use app\Core\Request;
use app\Core\Response;

class AuthMiddleware
{
    public function handle(Request $request): Response
    {
        return Response::error('mok');
    }
}