<?php

namespace Core;

class LoadEnv
{
    public function load()
    {
        // Read the .env file
        $envFilePath = __DIR__ . '/../../.env';  // Path to .env
        if (file_exists($envFilePath)) {
            $envVariables = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($envVariables as $envVar) {
                if (strpos($envVar, '#') === 0) {
                    continue; // Skip comment lines
                }
                list($key, $value) = explode('=', $envVar, 2);
                putenv("$key=$value");  // Set environment variable
                $_ENV[$key] = $value;   // Optional: Store in $_ENV
            }
        } else {
            throw new \Exception(".env file not found!");
        }
    }
}
