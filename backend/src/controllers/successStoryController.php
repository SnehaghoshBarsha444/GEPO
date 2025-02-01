<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class SuccessStoryController
{

    /**
     * Get all success stories
     * 
     * @return Response
     */
    public static function getAllSuccessStories()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM success_stories");
        $stmt->execute();
        $storiesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($storiesList as &$story) {
            $story['image'] = '/api/image?id=' . $story['image'];
        }
        return Response::success(200, "success stories fetched successfully", $storiesList);
    }

    /**
     * Create a new success story entry
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function createSuccessStory($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to create a success story entry");
        }

        $fileData = file_get_contents($data['file']['tmp_name']);

        $pdo = Database::connect();

        // upload image
        $stmt = $pdo->prepare("INSERT INTO images (file) VALUES (:file)");
        $stmt->execute([':file' => $fileData]);
        $imageId = $pdo->lastInsertId();

        // create success story entry
        $stmt = $pdo->prepare("INSERT INTO success_stories (image, name, institute, course) VALUES (:image, :name, :institute, :course)");
        $stmt->execute([
            ':image' => $imageId,
            ':name' => $data['name'],
            ':institute' => $data['institute'],
            ':course' => $data['course']
        ]);
        return Response::success(200, "success story entry created successfully", ['id' => $pdo->lastInsertId()]);
    }

    /**
     * Update a success story entry
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function updateSuccessStory($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to update a success story entry");
        }

        $id = $params['id'];

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM success_stories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $story = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$story) {
            Response::error(404, "success story entry not found", ["success story entry not found", "Id Not Matched"]);
        }

        $name = $data['name'] ?? $story['name'];
        $institute = $data['institute'] ?? $story['institute'];
        $course = $data['course'] ?? $story['course'];

        if (empty($name)) {
            Response::error(400, "Name is required", ['Missing name']);
        }

        if (empty($institute)) {
            Response::error(400, "Institute is required", ['Missing institute']);
        }

        if (empty($course)) {
            Response::error(400, "Course is required", ['Missing course']);
        }

        $stmt = $pdo->prepare("UPDATE success_stories SET name = :name, institute = :institute, course = :course WHERE id = :id");
        $stmt->execute([
            ':name' => $name,
            ':institute' => $institute,
            ':course' => $course,
            ':id' => $id
        ]);

        return Response::success(200, "success story entry updated successfully");
    }

    /**
     * Delete a success story entry
     * 
     * @param array $params
     * @return Response
     */
    public static function deleteSuccessStory($params, $data)
    {

        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to delete a success story entry");
        }

        $id = $params['id'];
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM success_stories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $story = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$story) {
            Response::error(404, "success story entry not found", ["success story entry not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM success_stories WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $stmt = $pdo->prepare("DELETE FROM images WHERE id = :image");
        $stmt->execute([':image' => $story['image']]);

        return Response::success(200, "success story entry deleted successfully");
    }
}
