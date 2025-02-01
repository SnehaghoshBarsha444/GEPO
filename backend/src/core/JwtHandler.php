<?php

namespace Core;

class Jwt
{
    private static $secret_key; // Change this to a secure key

    public static function init()
    {
        self::$secret_key = getenv('JWT_SECRET_KEY'); // Change this to a secure key
    }

    public static function generateToken($payload, $expiry = 3600)
    {
        // Set token expiration time
        $issuedAt = time();
        $expireAt = $issuedAt + $expiry;

        // Define JWT Header
        $header = json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]);

        // Add default claims
        $payload['iat'] = $issuedAt;  // Issued at
        $payload['exp'] = $expireAt;  // Expiry time

        // Encode Header & Payload
        $base64Header = self::base64UrlEncode($header);
        $base64Payload = self::base64UrlEncode(json_encode($payload));

        // Create Signature
        $signature = hash_hmac("sha256", "$base64Header.$base64Payload", self::$secret_key, true);
        $base64Signature = self::base64UrlEncode($signature);

        // Return the final JWT
        return "$base64Header.$base64Payload.$base64Signature";
    }

    public static function verifyToken($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($base64Header, $base64Payload, $base64Signature) = $parts;
        $payload = json_decode(self::base64UrlDecode($base64Payload), true);

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false; // Token expired
        }

        // Recreate signature for verification
        $expectedSignature = hash_hmac("sha256", "$base64Header.$base64Payload", self::$secret_key, true);
        $expectedBase64Signature = self::base64UrlEncode($expectedSignature);

        return hash_equals($expectedBase64Signature, $base64Signature) ? $payload : false;
    }

    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
