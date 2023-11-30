<?php
 
// // Allow requests from any origin
header("Access-Control-Allow-Origin: *");

// // Allow the following HTTP methods
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

// // Allow the following headers in the request
header("Access-Control-Allow-Headers: Authorize, Authorization, Content-Type");

// // Allow credentials (cookies, authorization headers, etc.)
header("Access-Control-Allow-Credentials: true");

// // Set the content type for the response
header("Content-Type: application/json");



require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key; 
require_once('../object/user.php');

$secretKey = 'aaaa';

$headers = getallheaders(); 


if(isset($headers['Authorize']) && $headers['Authorize']) {
    $tokenParts = explode(' ', $headers['Authorize']); 
    $token = $tokenParts[1]; 
    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        $userExists = checkUserExists($decoded->user_id); 

        $user = new User();
        $user = $user->getUserById($decoded->user_id); 
        if($userExists == true) {
            echo json_encode(array('success' => true, 'message' => 'Access granted.', 'user_id' => $decoded->user_id, 'user_info' => $user));
            http_response_code(200);
        }else {
            http_response_code(403);
            echo json_encode(array('success' => false, 'message' => 'Access denied! :P'));
        }
    }catch(Exception $e) {
        http_response_code(403);
        echo json_encode(array('success' => false, 'message' => 'Access denied! :P'));
    }

    
}else {
    echo json_encode(array('success' => false, 'message' => 'Access denied! :P'));
    exit(); 
}

function checkUserExists($user_id) {
    $user = new User(); 

    $userInfo  = $user->getUserById($user_id); 
    if($userInfo) {
        return true;
    }else {
        return false; 
    }
}