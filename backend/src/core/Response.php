<?php
namespace Core;

class Response {
    public static function success( $status = 200, $message = "Success", $data = []) {
        header("Content-Type: application/json");
        http_response_code($status);
        return json_encode([
            "status" => $status,
            "success" => true,
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }

    public static function error( $status = 500, $message = "Error", $errors = []) {
        header("Content-Type: application/json");
        http_response_code($status);
        return json_encode([
            "status" => $status,
            "success" => false,
            "message" => $message,
            "errors" => $errors,
            "data" => null
        ]);
        exit;
    }
}
