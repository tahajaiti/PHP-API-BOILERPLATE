<?php

namespace app\Middleware;

use app\Core\JWToken;
use app\Core\Request;
use app\Core\Response;
use app\Helpers\Helper;

class TokenMiddleware
{

    public function handle(Request $request): true|Response
    {
        $token = $request->getHeader('Authorization');
        if (!JWToken::validate($token)) {
            return Response::error('Invalid credentials');
        }
        return true;
    }
}