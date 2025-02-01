<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;


class PartnerController
{
    public static function createPartner($_, $data)
    {
        // if user is logged in and role is admin then create partner
        if (!isset($data['user']) || $data['user']['role'] !== 'admin') {
            return Response::error(401, "Unauthorized", ['You are not authorized to create a partner']);
        }

        $country = $data['country'];
        $institute = $data['institute'];
        $userEmail = $data['email'];

        if (empty($country) || empty($institute) || empty($userEmail)) {
            return Response::error(400, "All fields are required", ['Missing required fields']);
        }

        $pdo = Database::connect();

        // Fetch user by email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $userEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return Response::error(404, "User not found", ['User not found', 'please register a user with this email']);
        }

        // Insert into partners table
        $stmt = $pdo->prepare("INSERT INTO partners (country, user, institute) VALUES (:country, :user, :institute)");
        $stmt->execute([
            'country' => $country,
            'user' => $user['id'],
            'institute' => $institute
        ]);

        return Response::success(201, 'Partner created successfully', ['id' => $pdo->lastInsertId()]);
    }

    public static function getAllPartners()
    {
        // if user is logged in and role is admin then get partners
        // if (!isset($data['user']) || $data['user']['role'] !== 'admin') {
        //     return Response::error(401, "Unauthorized", ['You are not authorized to create a partner']);
        // }

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT partners.*, users.email, users.name FROM partners INNER JOIN users ON partners.user = users.id");
        $stmt->execute();
        $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return Response::success(200, "Partners fetched successfully", $partners);
    }

    public static function getAllCountries()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT DISTINCT country FROM partners");
        $stmt->execute();
        $countries = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return Response::success(200, "Countries fetched successfully", $countries);
    }

    public static function deletePartner($params, $data)
    {
        try {

            if (!isset($data['user']) || $data['user']['role'] !== 'admin') {
                return Response::error(401, "Unauthorized", ['You are not authorized to delete a partner']);
            }

            $pdo = Database::connect();
            $stmt = $pdo->prepare("SELECT * FROM partners WHERE id = :id");
            $stmt->execute(['id' => $params['id']]);
            $partner = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $partner['user']]);

            $stmt = $pdo->prepare("DELETE FROM partners WHERE id = :id");
            $stmt->execute(['id' => $params['id']]);

            return Response::success(200, "Partner deleted successfully");
        } catch (\Exception $e) {
            return Response::error(500, "An error occurred", [$e->getMessage()]);
        }
    }
}
