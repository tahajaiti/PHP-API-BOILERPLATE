<?php

namespace app\Core;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use FIreBase\JWT\Key;

class JWToken
{
    private static string $secret = 'secret';
    private static string $algo = 'HS256';
    private static int $expiration = 3600;

    public static function generate (array $payload = []) : string {
        $payload['iat'] = time();
        $payload['exp'] = time() + self::$expiration;

        return JWT::encode($payload, self::$secret, self::$algo);
    }

    public static function validate(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key(self::$secret, self::$algo));
        } catch (ExpiredException) {
            return null;
        }
    }
}