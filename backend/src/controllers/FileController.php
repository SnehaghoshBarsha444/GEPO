<?php

namespace Controllers;

use PDO;
use Core\Response;
use Core\Database;

class FileController
{
    public static function getFile($params)
    {
        $id = $params['id'];
        $type = $params['type'];

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM $type WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file && isset($file['file'])) {
            $fileData = $file['file'];
            $mimeType = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $fileData); // Detect MIME type

            // Set appropriate headers
            header("Content-Type: $mimeType");
            header("Content-Disposition: inline"); // Allow browser to display directly

            // Output the file data
            echo $fileData;
            exit;
        } else {
            return Response::error(404, 'File not found', ["file not found", "file not matched with id"]);
        }
    }

    public static function getVideo($params)
    {
        return self::getFile(['id' => $params['id'], 'type' => 'videos']);
    }

    public static function getImage($params)
    {
        return self::getFile(['id' => $params['id'], 'type' => 'images']);
    }
}
