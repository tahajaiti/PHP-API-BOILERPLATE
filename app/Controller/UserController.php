<?php

namespace app\Controller;

use app\Core\Request;
use app\Core\Response;
use app\Core\Validator;
use app\Repository\UserRepository;
use app\Service\Service;
use app\Service\UserService;
use Exception;

class UserController extends Controller
{
    public function __construct(){
        $this->service = new UserService(new UserRepository('users'));
    }

}