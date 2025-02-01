<?php

namespace Middleware;

use PDO;
use Core\Response;
use Core\Database;
use Core\Jwt;

class VerifyUser
{
    public static function handle($data)
    {
        $accessToken = null;

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            if (strpos($authHeader, 'Bearer ') === 0) {
                $accessToken = substr($authHeader, 7); // Remove "Bearer " prefix
            }
        }

        if (!$accessToken && isset($_COOKIE['accessToken'])) {
            $accessToken = $_COOKIE['accessToken'];
        }

        if (!$accessToken) {
            return Response::error(401, 'Unauthorized', ['Token missing', 'Please log in']);
        }

        $decodedToken = Jwt::verifyToken($accessToken);
        if (!$decodedToken) {
            return Response::error(401, 'Unauthorized', ['Invalid or expired token', 'Please log in']);
        }

        $userId = $decodedToken['id'];
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            unset($user['password']); // Remove sensitive data
            $data['user'] = $user;
            return $data;
        } else {
            header("Content-Type: application/json");
            http_response_code(401);
            echo Response::error(401, 'Unauthorized', ['User not found', 'Please log in']);
        }
    }
}
