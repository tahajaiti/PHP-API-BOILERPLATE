<?php

namespace app\Controller;
use app\Core\Request;
use app\Core\Response;
use app\Core\Service;
use app\Core\Validator;
use app\Helpers\Helper;
use app\Model\User;
use app\Repository\UserRepository;
use app\Service\AuthService;
use Exception;

class AuthController
{

    private Service $service;

    public function __construct(){
        $this->service = new AuthService();
    }

    /**
     * @throws Exception
     */
    public function register(Request $request): Response {
        if ($this->service->create($request)){
            return Response::success(null,'Registration successful');
        }
        return Response::error("Registration failed");
    }

}