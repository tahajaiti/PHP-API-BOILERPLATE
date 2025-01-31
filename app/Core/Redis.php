<?php

namespace app\Core;
use Redis as RedisClient;

class Redis
{

    private static ?RedisClient $redis = null;

    public static function getRedis(): RedisClient
    {
        if (self::$redis === null){
            self::$redis = new RedisClient();

            self::$redis->connect('127.0.0.1', 6379);
        }

        return self::$redis;
    }

    public static function checkConnection(): bool
    {
        return self::getRedis()->isConnected();
    }

    public static function set($key, $value): bool
    {
        return self::getRedis()->set($key, $value);
    }

    public static function get($key): string
    {
        return self::getRedis()->get($key);
    }

    public static function delete($key): bool
    {
        return self::getRedis()->del($key);
    }

}