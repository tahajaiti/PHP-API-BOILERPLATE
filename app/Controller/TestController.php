<?php

namespace app\Controller;
use App\Core\Request;
class TestController
{
    public function test(Request $request): void
    {
        echo "Test!";
        $test = $request->get('id');
        echo $test;
    }
}