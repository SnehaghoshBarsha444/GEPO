<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class FacultyResearchExchangeController
{

    /**
     * Get all latest news
     * 
     * @return Response
     */
    public static function getAllFacultyResearchExchanges()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM faculty_research_exchanges");
        $stmt->execute();
        $newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($newsList as &$news) {
            $news['image'] = '/api/image?id=' . $news['image'];
        }
        return Response::success(200, "all faculty research exchanges fetched successfully", $newsList);
    }

    /**
     * Create a new latest news entry
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function createFacultyResearchExchange($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to create a news entry");
        }

        $fileData = file_get_contents($data['file']['tmp_name']);

        $pdo = Database::connect();

        // upload image
        $stmt = $pdo->prepare("INSERT INTO images (file) VALUES (:file)");
        $stmt->execute([':file' => $fileData]);
        $imageId = $pdo->lastInsertId();

        // create news entry
        $stmt = $pdo->prepare("INSERT INTO faculty_research_exchanges (image, title, subtitle, date) VALUES (:image, :title, :subtitle, :date)");
        $stmt->execute([
            ':image' => $imageId,
            ':title' => $data['title'],
            ':subtitle' => $data['subtitle'],
            ':date' => $data['date']
        ]);
        return Response::success(200, "news entry created successfully", ['id' => $pdo->lastInsertId()]);
    }

    /**
     * Update a latest news entry
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function updateFacultyResearchExchange($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to update a news entry");
        }

        $id = $params['id'];

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM faculty_research_exchanges WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$news) {
            Response::error(404, "news entry not found", ["news entry not found", "Id Not Matched"]);
        }

        $title = $data['title'] ?? $news['title'];
        $subtitle = $data['subtitle'] ?? $news['subtitle'];
        $date = $data['date'] ?? $news['date'];

        if (empty($title)) {
            Response::error(400, "Title is required", ['Missing title']);
        }

        if (empty($subtitle)) {
            Response::error(400, "Subtitle is required", ['Missing subtitle']);
        }

        if (empty($date)) {
            Response::error(400, "Date is required", ['Missing date']);
        }

        $stmt = $pdo->prepare("UPDATE faculty_research_exchanges SET title = :title, subtitle = :subtitle, date = :date WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':subtitle' => $subtitle,
            ':date' => $date,
            ':id' => $id
        ]);

        return Response::success(200, "news entry updated successfully");
    }

    /**
     * Delete a latest news entry
     * 
     * @param array $params
     * @return Response
     */
    public static function deleteFacultyResearchExchange($params, $data)
    {

        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to delete a news entry");
        }

        $id = $params['id'];
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM faculty_research_exchanges WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$news) {
            Response::error(404, "news entry not found", ["news entry not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM faculty_research_exchanges WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $stmt = $pdo->prepare("DELETE FROM images WHERE id = :image");
        $stmt->execute([':image' => $news['image']]);

        return Response::success(200, "news entry deleted successfully");
    }
}
