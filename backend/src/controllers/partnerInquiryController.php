<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class PartnerInquiryController
{
    public static function createInquiry($_, $data)
    {
        $personName = $data['personName'];
        $email = $data['email'];
        $institute = $data['institute'];
        $partnershipType = $data['partnershipType'];
        $message = $data['message'];

        if (empty($personName) || empty($email) || empty($institute) || empty($partnershipType) || empty($message)) {
            return Response::error(400, "All fields are required", ["All fields are required", "must not be empty"]);
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO partner_inquires (personName, email, institute, partnershipType, message) VALUES (:personName, :email, :institute, :partnershipType, :message)");
        $stmt->execute([
            ":personName" => $personName,
            ":email" => $email,
            ":institute" => $institute,
            ":partnershipType" => $partnershipType,
            ":message" => $message
        ]);

        return Response::success(200, "Inquiry submitted successfully", ["id" => $pdo->lastInsertId()]);
    }

    public static function getAllInquiries()
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM partner_inquires");
        $stmt->execute();
        $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return Response::success(200, "Inquiries fetched successfully", $inquiries);
    }

    public static function deleteInquiry($params)
    {
        $id = $params['id'];
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM partner_inquires WHERE id = :id");
        $stmt->execute([
            ":id" => $id
        ]);
        return Response::success(200, "Inquiry deleted successfully", ["id" => $id]);
    }
}
