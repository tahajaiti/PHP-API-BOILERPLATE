<?php

namespace app\Controller;

use app\Core\Request;
use app\Core\Response;
use app\Core\Service;
use app\Core\Validator;
use app\Repository\UserRepository;
use app\Service\UserService;
use Exception;

class UserController
{
    private Service $service;

    public function __construct(){
        $this->service = new UserService(new UserRepository('users'));
    }

    /**
     * @throws Exception
     */
    public function index(): Response
    {
        $data = $this->service->getAll();
        return Response::success($data);
    }

    /**
     * @throws Exception
     */
    public function getById(Request $request): Response
    {
        $id = $request->get('id');
        $data = $this->service->getById($id);
        if ($data) {
            return Response::success($data);
        }
        return Response::error('User not found', 404);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): Response
    {
        $result = $this->service->update($request);
        if ($result) {
            return Response::success(null, 'User updated successfully');
        }
        return Response::error('User not found', 404, Validator::errors());
    }

    /**
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $result = $this->service->delete($id);
        if ($result) {
            return Response::success(null, 'User deleted successfully');
        }
        return Response::error('User not found', 404);
    }
}