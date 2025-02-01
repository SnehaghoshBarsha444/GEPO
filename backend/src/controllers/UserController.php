<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;
use Core\Jwt;

class UserController
{
    // get user Data
    public static function getUser($_, $data)
    {
        // if (!isset($data['user'])) {
        //     return Response::error(401, 'Unauthorized', ['You are not logged in', 'Unauthorize access', 'please login']);
        // }

        $user = $data['user'];
        return Response::success(200, "User Data Fetched", $user);
    }

    // Create a new user
    public static function createUser($_, $data)
    {
        if (empty($data['name'])) {
            return Response::error(400, 'Name is required', ['Missing name']);
        }
        if (empty($data['email'])) {
            return Response::error(400, 'Email is required', ['Missing email']);
        }
        if (empty($data['username'])) {
            return Response::error(400, 'Username is required', ['Missing username']);
        }
        if (empty($data['password'])) {
            return Response::error(400, 'Password is required', ['Missing password']);
        }
        if (empty($data['role'])) {
            return Response::error(400, 'Role is required', ['Missing role']);
        }

        $allowedRoles = ['student', 'faculty', 'international_partner', 'admin'];
        if (!in_array($data['role'], $allowedRoles)) {
            return Response::error(400, 'Invalid role', ['Invalid role, allowed values are: ' . implode(', ', $allowedRoles)]);
        }
        if (empty($data['institute'])) {
            return Response::error(400, 'Institute is required', ['Missing institute']);
        }

        $encryptedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO users (name, email, username, password, role, institute) VALUES (:name, :email, :username, :password, :role, :institute)");
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':username' => $data['username'],
            ':password' => $encryptedPassword,
            ':role' => $data['role'],
            ':institute' => $data['institute'],
        ]);

        $lastInsertId = $pdo->lastInsertId();

        // Return a success response with the created user data
        return Response::success(201, 'User created successfully', ["id" => $lastInsertId]);
    }

    public static function login($_, $data)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $data['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $verified = password_verify($data['password'], $user['password']);
            if ($verified) {
                // Generate a JWT
                $token = Jwt::generateToken(['id' => $user['id']]);

                // Store the JWT in a cookie
                setcookie('accessToken', $token, time() + 3600, '/', '', false, true);


                // Return the JWT in the header
                header('Authorization: Bearer ' . $token);

                // Return a success response with the logged in user data
                unset($user['password']); // Remove password from response
                return Response::success(200, 'Logged in successfully', $user);
            } else {
                return Response::error(401, 'Incorrect password', ['Incorrect password']);
            }
        } else {
            return Response::error(404, 'User not found', ['User not found']);
        }
    }

    public static function logout($_, $data)
    {
        if (!isset($data['user'])) {
            return Response::error(401, 'Unauthorized', ['You are not logged in', 'Unauthorize access', 'please login']);
        }
        setcookie('accessToken', '', time() - 3600, '', '', true, true);
        return Response::success(200, 'Logged out successfully');
    }

    public static function updatePassword($_, $data)
    {
        if (!isset($data['user'])) {
            return Response::error(401, 'Unauthorized', ['You are not logged in', 'Unauthorize access', 'please login']);
        }

        if (empty($data['oldPassword'])) {
            return Response::error(400, 'Old password is required', ['Old password is required']);
        }

        if (empty($data['newPassword'])) {
            return Response::error(400, 'New password is required', ['New password is required']);
        }

        $user = $data['user'];
        $oldPassword = $data['oldPassword'];
        $newPassword = $data['newPassword'];

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $user['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $verified = password_verify($oldPassword, $user['password']);
            if ($verified) {
                $encryptedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
                $stmt->execute([':password' => $encryptedPassword, ':id' => $user['id']]);

                // Return a success response with the updated user data
                unset($user['password']); // Remove password from response
                return Response::success(200, 'Password updated successfully', $user);
            } else {
                return Response::error(401, 'Incorrect old password', ['Incorrect old password']);
            }
        } else {
            return Response::error(404, 'User not found', ['User not found']);
        }
    }

    public static function updateUserData($_, $data)
    {
        if (!isset($data['user'])) {
            return Response::error(401, 'Unauthorized', ['You are not logged in', 'Unauthorize access', 'please login']);
        }

        $user = $data['user'];
        if (!isset($data['password'])) {
            return Response::error(400, 'Password is required', ['Password is required']);
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute([':id' => $user['id']]);
        $storedUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($storedUser && password_verify($data['password'], $storedUser['password'])) {
            $newEmail = $data['email'] ?? $user['email'];
            $newUsername = $data['username'] ?? $user['username'];
            $newName = $data['name'] ?? $user['name'];

            $stmt = $pdo->prepare("UPDATE users SET email = :email, username = :username, name = :name WHERE id = :id");
            $stmt->execute([':email' => $newEmail, ':username' => $newUsername, ':name' => $newName, ':id' => $user['id']]);

            // Return a success response with the updated user data
            return Response::success(200, 'User data updated successfully', [
                'id' => $user['id']
            ]);
        } else {
            return Response::error(401, 'Incorrect password', ['Incorrect password']);
        }
    }

    public static function deleteUser($_, $data)
    {
        if (!isset($data['user'])) {
            return Response::error(401, 'Unauthorized', ['You are not logged in', 'Unauthorized access', 'please login']);
        }

        $user = $data['user'];

        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();
            // Delete the user from the users table
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $user['id']]);

            $pdo->commit();

            setcookie('accessToken', '', time() - 3600, '', '', true, true);

            return Response::success(200, 'User data deleted successfully');
        } catch (\Exception $e) {
            $pdo->rollBack();
            return Response::error(500, 'Failed to delete user', [$e->getMessage()]);
        }
    }
}
