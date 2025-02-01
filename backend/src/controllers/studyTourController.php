<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class StudyTourController
{

    /**
     * Get all summer tours
     * 
     * @return Response
     */
    public static function getAllSummerTours()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM study_tours WHERE type = 'summer'");
        $stmt->execute();
        $tourList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tourList as &$tour) {
            $tour['image'] = '/api/image?id=' . $tour['image'];
        }
        return Response::success(200, "all summer tours fetched successfully", $tourList);
    }

    /**
     * Get all winter tours
     * 
     * @return Response
     */
    public static function getAllWinterTours()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM study_tours WHERE type = 'winter'");
        $stmt->execute();
        $tourList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tourList as &$tour) {
            $tour['image'] = '/api/image?id=' . $tour['image'];
        }
        return Response::success(200, "all winter tours fetched successfully", $tourList);
    }

    /**
     * Create a new study tour entry
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function createStudyTour($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to create a study tour entry");
        }

        if (!in_array($data['type'], ['summer', 'winter'])) {
            Response::error(400, "Invalid type specified. Allowed values are 'summer' or 'winter'");
        }

        $fileData = file_get_contents($data['file']['tmp_name']);

        $pdo = Database::connect();

        // upload image
        $stmt = $pdo->prepare("INSERT INTO images (file) VALUES (:file)");
        $stmt->execute([':file' => $fileData]);
        $imageId = $pdo->lastInsertId();

        // create study tour entry
        $stmt = $pdo->prepare("INSERT INTO study_tours (image, title, subtitle, type) VALUES (:image, :title, :subtitle, :type)");
        $stmt->execute([
            ':image' => $imageId,
            ':title' => $data['title'],
            ':subtitle' => $data['subtitle'],
            ':type' => $data['type']
        ]);
        return Response::success(200, "study tour entry created successfully", ['id' => $pdo->lastInsertId()]);
    }

    /**
     * Update a study tour entry
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function updateStudyTour($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to update a study tour entry");
        }

        if (!in_array($data['type'], ['summer', 'winter'])) {
            Response::error(400, "Invalid type specified. Allowed values are 'summer' or 'winter'");
        }

        $id = $params['id'];

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM study_tours WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tour) {
            Response::error(404, "study tour entry not found", ["study tour entry not found", "Id Not Matched"]);
        }

        $title = $data['title'] ?? $tour['title'];
        $subtitle = $data['subtitle'] ?? $tour['subtitle'];
        $type = $data['type'] ?? $tour['type'];

        if (empty($title)) {
            Response::error(400, "Title is required", ['Missing title']);
        }

        if (empty($subtitle)) {
            Response::error(400, "Subtitle is required", ['Missing subtitle']);
        }

        if (empty($type)) {
            Response::error(400, "Type is required", ['Missing type']);
        }

        $stmt = $pdo->prepare("UPDATE study_tours SET title = :title, subtitle = :subtitle, type = :type WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':subtitle' => $subtitle,
            ':type' => $type,
            ':id' => $id
        ]);

        return Response::success(200, "study tour entry updated successfully");
    }

    /**
     * Delete a study tour entry
     * 
     * @param array $params
     * @return Response
     */
    public static function deleteStudyTour($params, $data)
    {

        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to delete a study tour entry");
        }

        $id = $params['id'];
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM study_tours WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tour) {
            Response::error(404, "study tour entry not found", ["study tour entry not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM study_tours WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $stmt = $pdo->prepare("DELETE FROM images WHERE id = :image");
        $stmt->execute([':image' => $tour['image']]);

        return Response::success(200, "study tour entry deleted successfully");
    }
}
