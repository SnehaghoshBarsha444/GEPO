<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class ScholarshipController
{
    /**
     * Get all scholarships
     * 
     * @return Response
     */
    public static function getAllScholarships()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM scholarships");
        $stmt->execute();
        $scholarships = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($scholarships as &$scholarship) {
            $scholarship['image'] = '/api/image?id=' . $scholarship['image'];
        }
        return Response::success(200, "scholarships fetched successfully", $scholarships);
    }

    /**
     * Create a new scholarship
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function createScholarship($params, $data)
    {

        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to create a scholarship");
        }

        $fileData = file_get_contents($data['file']['tmp_name']);

        $pdo = Database::connect();

        // upload image
        $stmt = $pdo->prepare("INSERT INTO images (file) VALUES (:file)");
        $stmt->execute([':file' => $fileData]);
        $imageId = $pdo->lastInsertId();

        // create scholarship
        $stmt = $pdo->prepare("INSERT INTO scholarships (image, title, date) VALUES (:image, :title, :date)");
        $stmt->execute([
            ':image' => $imageId,
            ':title' => $data['title'],
            ':date' => $data['date']
        ]);
        return Response::success(200, "scholarship created successfully", ['id' => $pdo->lastInsertId()]);
    }

    /**
     * Update a scholarship
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function updateScholarship($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to update a scholarship");
        }

        $id = $params['id'];

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM scholarships WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $scholarship = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$scholarship) {
            Response::error(404, "scholarship not found", ["scholarship not found", "Id Not Matched"]);
        }

        $title = $data['title'] ?? $scholarship['title'];
        $date = $data['date'] ?? $scholarship['date'];

        if (empty($title)) {
            Response::error(400, "Title is required", ['Missing title']);
        }

        if (empty($date)) {
            Response::error(400, "Date is required", ['Missing date']);
        }

        $stmt = $pdo->prepare("UPDATE scholarships SET title = :title, date = :date WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':date' => $date,
            ':id' => $id
        ]);

        return Response::success(200, "scholarship updated successfully");
    }

    /**
     * Delete a scholarship
     * 
     * @param array $params
     * @return Response
     */
    public static function deleteScholarship($params, $data)
    {

        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to delete a scholarship");
        }


        $id = $params['id'];
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM scholarships WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $scholarship = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$scholarship) {
            Response::error(404, "scholarship not found", ["scholarship not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM scholarships WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $stmt = $pdo->prepare("DELETE FROM images WHERE id = :image");
        $stmt->execute([':image' => $scholarship['image']]);

        return Response::success(200, "scholarship deleted successfully");
    }
}
