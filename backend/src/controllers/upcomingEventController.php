<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class UpcomingEventsController
{

    /**
     * Get all upcoming events
     * 
     * @return Response
     */
    public static function getAllUpcomingEvents()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM upcoming_events");
        $stmt->execute();
        $eventsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return Response::success(200, "Upcoming events fetched successfully", $eventsList);
    }

    /**
     * Create a new upcoming event
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function createUpcomingEvent($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to create an upcoming event");
        }

        $pdo = Database::connect();

        $stmt = $pdo->prepare("INSERT INTO upcoming_events (dateTime, title) VALUES (:dateTime, :title)");
        $stmt->execute([
            ':dateTime' => $data['dateTime'],
            ':title' => $data['title']
        ]);
        return Response::success(200, "Upcoming event created successfully", ['id' => $pdo->lastInsertId()]);
    }

    /**
     * Update an upcoming event
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function updateUpcomingEvent($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to update an upcoming event");
        }

        $id = $params['id'];

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM upcoming_events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            Response::error(404, "Upcoming event not found", ["Upcoming event not found", "Id Not Matched"]);
        }

        $dateTime = $data['dateTime'] ?? $event['dateTime'];
        $title = $data['title'] ?? $event['title'];

        if (empty($title)) {
            Response::error(400, "Title is required", ['Missing title']);
        }

        $stmt = $pdo->prepare("UPDATE upcoming_events SET dateTime = :dateTime, title = :title WHERE id = :id");
        $stmt->execute([
            ':dateTime' => $dateTime,
            ':title' => $title,
            ':id' => $id
        ]);

        return Response::success(200, "Upcoming event updated successfully");
    }

    /**
     * Delete an upcoming event
     * 
     * @param array $params
     * @return Response
     */
    public static function deleteUpcomingEvent($params, $data)
    {

        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to delete an upcoming event");
        }

        $id = $params['id'];
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM upcoming_events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            Response::error(404, "Upcoming event not found", ["Upcoming event not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM upcoming_events WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return Response::success(200, "Upcoming event deleted successfully");
    }
}
