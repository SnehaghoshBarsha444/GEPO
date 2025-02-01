<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class VacancyController
{

    /**
     * Get all vacancies
     * 
     * @return Response
     */
    public static function getAllVacancies()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM vacancies");
        $stmt->execute();
        $vacanciesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return Response::success(200, "vacancies fetched successfully", $vacanciesList);
    }

    /**
     * Create a new vacancy entry
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function createVacancy($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to create a news entry");
        }

        $title = $data['title'] ?? '';
        $country = $data['country'] ?? '';
        $department = $data['department'] ?? '';
        $location = $data['location'] ?? '';

        if (empty($title)) {
            Response::error(400, "Title is required", ['Missing title']);
        }

        if (empty($country)) {
            Response::error(400, "Country is required", ['Missing country']);
        }

        if (empty($department)) {
            Response::error(400, "Department is required", ['Missing department']);
        }

        if (empty($location)) {
            Response::error(400, "Location is required", ['Missing location']);
        }

        $pdo = Database::connect();

        $stmt = $pdo->prepare("INSERT INTO vacancies (title, country, department, location) VALUES (:title, :country, :department, :location)");
        $stmt->execute([
            ':title' => $title,
            ':country' => $country,
            ':department' => $department,
            ':location' => $location
        ]);
        return Response::success(200, "vacancy entry created successfully", ['id' => $pdo->lastInsertId()]);
    }

    /**
     * Update a vacancy entry
     * 
     * @param array $params
     * @param array $data
     * @return Response
     */
    public static function updateVacancy($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to update a news entry");
        }

        $id = $params['id'];

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM vacancies WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $vacancy = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vacancy) {
            Response::error(404, "vacancy not found", ["vacancy not found", "Id Not Matched"]);
        }

        $title = $data['title'] ?? $vacancy['title'];
        $country = $data['country'] ?? $vacancy['country'];
        $department = $data['department'] ?? $vacancy['department'];
        $location = $data['location'] ?? $vacancy['location'];

        if (empty($title)) {
            Response::error(400, "Title is required", ['Missing title']);
        }

        if (empty($country)) {
            Response::error(400, "Country is required", ['Missing country']);
        }

        if (empty($department)) {
            Response::error(400, "Department is required", ['Missing department']);
        }

        if (empty($location)) {
            Response::error(400, "Location is required", ['Missing location']);
        }

        $stmt = $pdo->prepare("UPDATE vacancies SET title = :title, country = :country, department = :department, location = :location WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':country' => $country,
            ':department' => $department,
            ':location' => $location,
            ':id' => $id
        ]);

        return Response::success(200, "vacancy updated successfully");
    }

    /**
     * Delete a vacancy entry
     * 
     * @param array $params
     * @return Response
     */
    public static function deleteVacancy($params, $data)
    {

        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty' && $user['role'] != 'international_partner') {
            Response::error(403, "You are not authorized to delete a news entry");
        }

        $id = $params['id'];
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM vacancies WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $vacancy = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vacancy) {
            Response::error(404, "vacancy not found", ["vacancy not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM vacancies WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return Response::success(200, "vacancy deleted successfully");
    }
}
