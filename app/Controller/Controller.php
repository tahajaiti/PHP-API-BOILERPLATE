<?php

namespace app\Controller;

use app\Core\Request;
use app\Core\Response;
use app\Core\Validator;
use app\Service\Service;
use Exception;

abstract class Controller
{
    protected Service $service;

    /**
     * @throws Exception
     */
    public function create(Request $request): Response {
        if ($this->service->create($request)){
            return Response::success(null,'Created successfully');
        }
        return Response::error( 'Creating entity failed', 200, Validator::errors());
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

        return Response::error($this->getClass() . ' not found', 404);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): Response
    {
        $result = $this->service->update($request);

        if ($result) {
            return Response::success(null, $this->getClass() . ' updated successfully');
        }

        return Response::error($this->getClass() . ' not found', 404, Validator::errors());
    }

    /**
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $result = $this->service->delete($id);

        if ($result) {
            return Response::success(null, $this->getClass() . ' deleted successfully');
        }

        return Response::error($this->getClass() . ' not found', 404);
    }

    protected function getClass(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        return str_replace('Controller', '', $className);
    }

}