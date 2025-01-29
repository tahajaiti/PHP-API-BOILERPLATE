<?php

namespace app\Controller;
use app\Core\Request;
use app\Core\Response;

class TestController
{
    public function test(Request $request): Response
    {
        $test = $request->all();

        return Response::error();
    }
}