<?php

namespace app\Service;

use app\Core\RedisClient;

class PermissionService
{

    private RedisClient $redis;

    public function __construct()
    {
        $this->redis = RedisClient::getRedis();
    }



}