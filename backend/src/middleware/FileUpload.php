<?php

namespace Middleware;

use Core\Response;

class FileUploadMiddleware
{
    // Max file sizes in bytes
    const MAX_IMAGE_SIZE = 10 * 1024 * 1024; // 10 MB
    const MAX_VIDEO_SIZE = 50 * 1024 * 1024; // 50 MB

    // Allowed file types for images and videos
    const ALLOWED_TYPES = [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'image/webp', // image types
        'video/mp4',
        'video/ogg',
        'video/webm'   // video types
    ];

    public static function handle($data)
    {
        // Check if file data exists
        if (isset($data['file'])) {
            $file = $data['file'];

            // If it's a file uploaded via form-data
            if (is_array($file)) {
                // Get file information
                $fileName = $file['name'];
                $fileTmpName = $file['tmp_name'];
                $fileType = mime_content_type($fileTmpName);
                $fileSize = $file['size'];

                // Check if the file type is allowed
                if (!in_array($fileType, self::ALLOWED_TYPES)) {
                    return Response::error(400, 'Invalid file type', ['Invalid file type']);
                }

                // Check if the file size exceeds the maximum allowed size
                if (strpos($fileType, 'video/') === 0 && $fileSize > self::MAX_VIDEO_SIZE) {
                    return Response::error(400, 'Video file size exceeds the limit (50 MB)', ['File size too large']);
                } elseif (strpos($fileType, 'image/') === 0 && $fileSize > self::MAX_IMAGE_SIZE) {
                    return Response::error(400, 'Image file size exceeds the limit (10 MB)', ['File size too large']);
                }


                $data['file'] = $file; // Return the uploaded file information (name, type, etc.)
            }
            // If it's a Base64 encoded image/video (data URI scheme)
            elseif (strpos($file, 'data:') === 0) {
                // Process Base64 encoded file (image/video)
                list($fileType, $fileData) = explode(';', $file);
                $fileType = str_replace('data:', '', $fileType);
                $fileData = base64_decode(explode(',', $file)[1]);

                // Check if the file type is allowed
                if (!in_array($fileType, self::ALLOWED_TYPES)) {
                    return Response::error(400, 'Invalid file type', ['Invalid file type']);
                }
                // Check if file size is within the allowed limit
                if (strpos($fileType, 'video/') === 0 && strlen($fileData) > self::MAX_VIDEO_SIZE) {
                    return Response::error(400, 'Video file size exceeds the limit (50 MB)', ['File size too large']);
                } elseif (strpos($fileType, 'image/') === 0 && strlen($fileData) > self::MAX_IMAGE_SIZE) {
                    return Response::error(400, 'Image file size exceeds the limit (10 MB)', ['File size too large']);
                }

                $data['file'] = $fileData; // Return the decoded Base64 file data
            }

            return $data;
        }

        // Return false if no valid file is found
        return Response::error(400, 'No valid file found', ['No valid file found, please upload an image or video']);
    }
}
