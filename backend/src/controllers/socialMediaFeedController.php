<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class SocialMediaFeedController
{
    /**
     * Get all social media feeds
     * 
     * @return Response
     */
    public static function getAllSocialMediaFeeds()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM social_media_feeds");
        $stmt->execute();
        $feeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return Response::success(200, "social media feeds fetched successfully", $feeds);
    }

    /**
     * Create a new social media feed
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function createSocialMediaFeed($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to create a social media feed");
        }

        $url = $data['url'];

        if (empty($url)) {
            Response::error(400, "URL is required", ['Missing URL']);
        }

        $pdo = Database::connect();

        // create feed
        $stmt = $pdo->prepare("INSERT INTO social_media_feeds (url) VALUES (:url)");
        $stmt->execute([
            ':url' => $url
        ]);
        return Response::success(200, "social media feed created successfully", ['id' => $pdo->lastInsertId()]);
    }

    /**
     * Update a social media feed
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function updateSocialMediaFeed($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to update a social media feed");
        }

        $id = $params['id'];

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM social_media_feeds WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $feed = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$feed) {
            Response::error(404, "social media feed not found", ["social media feed not found", "Id Not Matched"]);
        }

        $url = $data['url'] ?? $feed['url'];

        if (empty($url)) {
            Response::error(400, "URL is required", ['Missing URL']);
        }

        $stmt = $pdo->prepare("UPDATE social_media_feeds SET url = :url WHERE id = :id");
        $stmt->execute([
            ':url' => $url,
            ':id' => $id
        ]);

        return Response::success(200, "social media feed updated successfully");
    }

    /**
     * Delete a social media feed
     * 
     * @param array $params
     * @return Response
     */
    public static function deleteSocialMediaFeed($params)
    {
        $user = $params['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to delete a social media feed");
        }

        $id = $params['id'];
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM social_media_feeds WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $feed = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$feed) {
            Response::error(404, "social media feed not found", ["social media feed not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM social_media_feeds WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return Response::success(200, "social media feed deleted successfully");
    }
}
