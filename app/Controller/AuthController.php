<?php

namespace app\Controller;
use app\Core\Request;
use app\Core\Response;
use app\Core\Service;
use app\Core\Validator;
use app\Helpers\Helper;
use app\Repository\UserRepository;
use app\Service\AuthService;
use Exception;

class AuthController
{

    private Service $service;

    public function __construct(){
        $this->service = new AuthService(new UserRepository('users'));
    }

    /**
     * @throws Exception
     */
    public function register(Request $request): Response {
        if ($this->service->create($request)){
            return Response::success(null,'Registration successful');
        }
        return Response::error( 'Registration failed', 200, Validator::errors());
    }

    /**
     * @throws Exception
     */
    public function all(): Response
    {
        $data = $this->service->getAll();
        if ($data){
            return Response::success($data,'All successful');
        }
        return Response::error('No data found');
    }

    /**
     * @throws Exception
     */
    public function get(Request $request): Response
    {
        $data = $this->service->getById($request->get('id'));
        if ($data){
            return Response::success($data,'Successful');
        }

        return Response::error('No data found');
    }

}