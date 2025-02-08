<?php

namespace app\Controller;
use app\Core\Request;
use app\Core\Response;
use app\Core\Validator;
use app\Repository\UserRepository;
use app\Service\AuthService;

class AuthController extends Controller
{

    public function __construct(){
        $this->service = new AuthService(new UserRepository('users'));
    }

    public function create(Request $request): Response {
        if ($this->service->create($request)){
            return Response::success(null,'Registration successful');
        }
        return Response::error( 'Registration failed', 200, Validator::errors());
    }

    public function login(Request $request): Response {
        $result = $this->service->login($request);
        if ($result){
            return Response::success($result,'Login successful');
        }
        return Response::error( 'Login failed', 200, Validator::errors());
    }

}