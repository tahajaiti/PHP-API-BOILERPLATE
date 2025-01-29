<?php
namespace app\Middleware;

use app\Core\Request;
use app\Core\Response;
use app\Model\User;
use app\Repository\UserRepository;
use Exception;

class AuthMiddleware
{
    /**
     * @throws Exception
     */
    public function handle(Request $request): ?Response
    {
        $model = new User($request->all());
        $repo = new UserRepository('users');
        $repo->setModel($model);

        if ($repo->findByEmail()){
            return Response::error('Email already exists');
        }
        return null;
    }
}