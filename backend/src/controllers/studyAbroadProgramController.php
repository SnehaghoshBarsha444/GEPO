<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class StudyAbroadProgramController
{

    public static function createProgram($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty') {
            Response::error(403, "You are not authorized to create a program");
        }

        $fileData = file_get_contents($data['file']['tmp_name']);

        $pdo = Database::connect();

        // upload image
        $stmt = $pdo->prepare("INSERT INTO images (file) VALUES (:file)");
        $stmt->execute([':file' => $fileData]);

        $imageId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO study_abroad_programs (image, institute, title, country, rating) VALUES (:image, :institute, :title, :country, :rating)");
        $stmt->execute([
            ':image' => $imageId,
            ':institute' => $data['institute'],
            ':title' => $data['title'],
            ':country' => $data['country'],
            ':rating' => $data['rating']
        ]);

        return Response::success(200, "Program created successfully", ['id' => $pdo->lastInsertId()]);
    }

    public static function updateProgram($params, $data)
    {
        $user = $data['user'];

        if ($user['role'] != 'admin' && $user['role'] != 'faculty') {
            Response::error(403, "You are not authorized to update a program");
        }

        $id = $params['id'];

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM study_abroad_programs WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$program) {
            Response::error(404, "Program not found");
        }

        $title = $data['title'] ?? $program['title'];
        $institute = $data['institute'] ?? $program['institute'];
        $country = $data['country'] ?? $program['country'];
        $rating = $data['rating'] ?? $program['rating'];

        if (empty($title)) {
            Response::error(400, "Title is required", ['Missing title']);
        }

        if (empty($institute)) {
            Response::error(400, "Institute is required", ['Missing institute']);
        }

        if (empty($country)) {
            Response::error(400, "Country is required", ['Missing country']);
        }

        if (!is_float($rating)) {
            Response::error(400, "Rating must be a float", ['Invalid rating']);
        }

        $stmt = $pdo->prepare("UPDATE study_abroad_programs SET title = :title, institute = :institute, country = :country, rating = :rating WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':institute' => $institute,
            ':country' => $country,
            ':rating' => $rating,
            ':id' => $id
        ]);

        return Response::success(200, "Program updated successfully", ['id' => $id]);
    }

    public static function getAllPrograms()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM study_abroad_programs");
        $stmt->execute();
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($programs as &$program) {
            $program['image'] = '/api/image?id=' . $program['image'];
        }
        return Response::success(200, "Programs fetched successfully", $programs);
    }


    public static function deleteProgram($params)
    {
        $id = $params['id'];
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM study_abroad_programs WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$program) {
            Response::error(404, "Program not found", ["Program not found", "Id Not Matched"]);
        }

        $stmt = $pdo->prepare("DELETE FROM study_abroad_programs WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $stmt = $pdo->prepare("DELETE FROM images WHERE id = :image");
        $stmt->execute([':image' => $program['image']]);

        return Response::success(200, "Program deleted successfully");
    }
}
