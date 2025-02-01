<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class StudyAbroadEligibilityController
{

    /**
     * Get all queries
     *
     * @return Response
     */
    public static function getAllQueries()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM study_abroad_eligibility");
        $stmt->execute();
        $queries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return Response::success(200, "Queries fetched successfully", $queries);
    }

    /**
     * Create a new query
     *
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function createQuery($params, $data)
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['number']) || empty($data['degree']) || empty($data['mode'])) {
            return Response::error(400, "All fields are required");
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO study_abroad_eligibility (name, email, number, degree, mode) VALUES (:name, :email, :number, :degree, :mode)");
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':number' => $data['number'],
            ':degree' => $data['degree'],
            ':mode' => $data['mode']
        ]);
        return Response::success(200, "Query created successfully", ['id' => $pdo->lastInsertId()]);
    }

    /**
     * Delete a query
     *
     * @param array $params
     * @return Response
     */
    public static function deleteQuery($params)
    {
        $user = $params['user'];

        if ($user['role'] !== 'admin' && $user['role'] !== 'faculty') {
            return Response::error(403, "You don't have enough permissions to delete a query", ["You don't have enough permissions to delete a query", "Forbidden"]);
        }

        $id = $params['id'];
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM study_abroad_eligibility WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $query = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$query) {
            return Response::error(404, "Query not found", ["Query not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM study_abroad_eligibility WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return Response::success(200, "Query deleted successfully");
    }
}
