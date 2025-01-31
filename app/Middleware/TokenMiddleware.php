<?php

namespace app\Middleware;

use app\Core\JWToken;
use app\Core\Request;
use app\Core\Response;

class TokenMiddleware implements MiddlewareInt
{

    public function handle(Request $request): ?Response
    {
        $token = $request->getHeader('Authorization');
        if (!JWToken::validate($token)) {
            return Response::error('Invalid credentials');
        }
        return null;
    }
}