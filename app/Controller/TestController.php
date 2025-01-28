<?php

namespace app\Controller;
use App\Core\Request;
use app\Core\Response;
use app\Helpers\Helper;

class TestController
{
    public function test(Request $request): Response
    {
        $test = $request->all();

        return Response::error();
    }
}