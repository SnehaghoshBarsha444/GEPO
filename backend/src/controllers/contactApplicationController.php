<?php

namespace Controllers;

use PDO;
use Core\Database;
use Core\Response;

class ContactApplicationController
{

    /**
     * Get all contact applications
     *
     * @return Response
     */
    public static function getAllApplications()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM contact_applications");
        $stmt->execute();
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return Response::success(200, "Applications fetched successfully", $applications);
    }

    /**
     * Create a new contact application
     *
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function createApplication($params, $data)
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['inquiryType']) || empty($data['message'])) {
            return Response::error(400, "All fields are required");
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO contact_applications (name, email, inquiryType, message) VALUES (:name, :email, :inquiryType, :message)");
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':inquiryType' => $data['inquiryType'],
            ':message' => $data['message']
        ]);
        return Response::success(200, "Application created successfully", ['id' => $pdo->lastInsertId()]);
    }

    /**
     * Delete a contact application
     *
     * @param array $params
     * @return Response
     */
    public static function deleteApplication($params)
    {
        $user = $params['user'];

        if ($user['role'] !== 'admin' && $user['role'] !== 'faculty') {
            return Response::error(403, "You don't have enough permissions to delete an application", ["You don't have enough permissions to delete an application", "Forbidden"]);
        }

        $id = $params['id'];
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM contact_applications WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$application) {
            return Response::error(404, "Application not found", ["Application not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM contact_applications WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return Response::success(200, "Application deleted successfully");
    }
}
