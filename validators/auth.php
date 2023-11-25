<?php

require 'vendor/autoload.php'; // Include the Composer autoloader
use Firebase\JWT\JWT;

function authenticateToken($token, $secretKey) {
    try {
        // Decode and verify the JWT
        $decoded = JWT::decode($token, $secretKey, array('HS256'));

        // Access the decoded data
        $userId = $decoded->data->user_id;

        // Check if the user exists in the database
        $userExists = checkUserExists($userId);

        if ($userExists) {
            // Perform additional authentication and authorization steps
            $userRole = $decoded->data->role;

            // Check user role for authorization
            if ($userRole === 'admin') {
                // User is an admin, perform admin-specific actions
                return 'Token is valid. User is an admin.';
            } else {
                // User is not an admin, perform regular user actions
                return 'Token is valid. User is not an admin.';
            }
        } else {
            return 'Token is valid, but the user does not exist.';
        }

    } catch (Exception $e) {
        // Handle invalid token
        http_response_code(401);
        return 'Invalid token: ' . $e->getMessage();
    }
}

function checkUserExists($userId) {
    // Implement your logic to check if the user exists in the database
    // This is just a placeholder function, replace it with your actual logic
    return true; // Return true if the user exists, false otherwise
}