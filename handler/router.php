<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once("../object/user.php");

$method = isset($_POST['method']) ? $_POST['method'] : exit();

if (function_exists($method)) {
    call_user_func($method);
} else {
    exit();
}

function createUser() {
    require_once(__DIR__ . '/../validators/userValidator.php');
    // Define the required fields
    $requiredFields = ['first_name', 'last_name', 'email', 'password'];

    // Validate input data
    $validationResult = validateUserData($_POST, $requiredFields);

    if ($validationResult['success']) {
        $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
        $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $userInfo = array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $email,
            "password" => $password,
        );

        $user = new User();
        $ret = $user->createUser($userInfo);
        echo json_encode($ret);
    } else {
        // Validation failed, return error message
        http_response_code(422); // Cannot be proccessed.
        echo json_encode(array('success' => false, 'errors' => $validationResult['errors']));
    }
}

// function validateUserData($data, $requiredFields) {
//     // Implement your validation logic here
//     $errors = array();

//     // Additional validation rules
//     if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
//         $errors['email'] = 'Please provide a valid format.';
//     } 

//     if(userExists($data['email'])) {
//         $errors['email'] = 'Email is already taken. Please try another';
//     }

//     // Check if required fields are not empty
//     foreach ($requiredFields as $field) {
//         if (empty($data[$field])) {
//             $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
//         }
//     }

    

//     if (empty($errors)) {
//         return array('success' => true, 'data' => $data);
//     } else {
//         return array('success' => false, 'errors' => $errors);
//     }
// }

?>