<?php

namespace app\Middleware;

use app\Core\JWToken;
use app\Core\Request;
use app\Core\Response;
use app\Interfaces\MiddlewareInt;

class TokenMiddleware implements MiddlewareInt
{

    public function handle(Request $request): ?Response
    {
        $token = $request->getHeader('Authorization');

        if (!$token){
            return Response::error('No token provided');
        }

        if (!JWToken::validate($token)) {
            return Response::error('Invalid credentials');
        }
        return null;
    }
}