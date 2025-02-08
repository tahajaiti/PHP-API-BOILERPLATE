<?php

namespace app\Core;

use Redis;

class RedisClient
{
    private static ?Redis $redis = null;
    public static function getRedis(): Redis
    {
        if (self::$redis === null) {
            self::$redis = new Redis();

            self::$redis->connect('127.0.0.1', 6379);
        }

        return self::$redis;
    }

    // Check Redis connection
    public static function checkConnection(): bool
    {
        return self::getRedis()->isConnected();
    }
}
