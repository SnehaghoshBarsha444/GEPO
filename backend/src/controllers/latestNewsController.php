<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class LatestNewsController
{

    /**
     * Get all latest news
     * 
     * @return Response
     */
    public static function getAllLatestNews()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM latest_news");
        $stmt->execute();
        $newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($newsList as &$news) {
            $news['image'] = '/api/image?id=' . $news['image'];
        }
        return Response::success(200, "latest news fetched successfully", $newsList);
    }

    /**
     * Create a new latest news entry
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function createLatestNews($params, $data)
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
        $stmt = $pdo->prepare("INSERT INTO latest_news (image, title, subtitle) VALUES (:image, :title, :subtitle)");
        $stmt->execute([
            ':image' => $imageId,
            ':title' => $data['title'],
            ':subtitle' => $data['subtitle']
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
    public static function updateLatestNews($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to update a news entry");
        }

        $id = $params['id'];

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM latest_news WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$news) {
            Response::error(404, "news entry not found", ["news entry not found", "Id Not Matched"]);
        }

        $title = $data['title'] ?? $news['title'];
        $subtitle = $data['subtitle'] ?? $news['subtitle'];

        if (empty($title)) {
            Response::error(400, "Title is required", ['Missing title']);
        }

        if (empty($subtitle)) {
            Response::error(400, "Subtitle is required", ['Missing subtitle']);
        }

        $stmt = $pdo->prepare("UPDATE latest_news SET title = :title, subtitle = :subtitle WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':subtitle' => $subtitle,
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
    public static function deleteLatestNews($params, $data)
    {

        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to delete a news entry");
        }

        $id = $params['id'];
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM latest_news WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$news) {
            Response::error(404, "news entry not found", ["news entry not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM latest_news WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $stmt = $pdo->prepare("DELETE FROM images WHERE id = :image");
        $stmt->execute([':image' => $news['image']]);

        return Response::success(200, "news entry deleted successfully");
    }
}
