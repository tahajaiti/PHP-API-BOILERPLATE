<?php

namespace app\Controller;
use app\Core\Request;
use app\Core\Response;
use app\Core\Validator;
use app\Helpers\Helper;
use app\Model\User;
use app\repository\UserRepository;
use Exception;

class AuthController
{

    /**
     * @throws Exception
     */
    public function register(Request $request): Response {

        Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        $errors = Validator::errors();

        if (!empty($errors)){
            return Response::error($errors[0]);
        }

        $user = new UserRepository();
        $result = $user->create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ]);

        if (!$result){
            return Response::error('Error creating user');
        }

        return Response::success('User registered successfully');
    }

}