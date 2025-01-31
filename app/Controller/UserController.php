<?php

namespace app\Controller;

use app\Repository\UserRepository;
use app\Service\UserService;

class UserController extends Controller
{
    public function __construct(){
        $this->service = new UserService(new UserRepository('users'));
    }

}