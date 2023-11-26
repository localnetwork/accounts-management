<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../vendor/autoload.php';
    use Firebase\JWT\JWT;
    require_once("../object/user.php");
    $method = isset($_POST['method']) ? $_POST['method']: '';

    if (function_exists($method)) {
        call_user_func($method);
    } else {
        exit();        
    }


    function createUser() {
        require_once('../validators/userValidator.php');
        $requiredFields = ['first_name', 'last_name', 'email', 'password'];

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
            
            $ussss = $user->getUserInfo($email); 

            $token = generateToken($ussss['id']);
            $userData['token'] = $token;

            echo json_encode($userData); 
        } else {
            http_response_code(422);
            echo json_encode(array('success' => false, 'errors' => $validationResult['errors']));
        }
    }

    function userLogin() {
        require_once('../validators/userValidator.php');
        $requiredFields = ['first_name', 'last_name', 'email', 'password'];

        $validationResult = validateLogin($_POST, $requiredFields);

        if ($validationResult['success']) {
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            // $userInfo = array(
            //     "email" => $email,
            //     "password" => $password,
            // ); 

            $user = new User();
            $ret = $user->userLogin($email, $password); 
            $uInfo = $user->getUserInfo($email); 
            $token = generateToken($uInfo['id']);
            $eee = array(
                'token' => $token,
                'user_info' => array(
                    'id' => $uInfo['id'],
                    'email' => $uInfo['email'],
                    'first_name' => $uInfo['first_name'],
                    'last_name' => $uInfo['last_name'],
                ),
            );
            // $eee['token'] = $token;

            echo json_encode(array('success' => true, 'data' => $eee));
            http_response_code(200);
            
        } else {
            http_response_code(422); 
            echo json_encode(array('success' => false, 'errors' => $validationResult['errors']));
        }
    }


    function getAllUsers() {
        $users = new User();
        $users = $users->getAllUsers();
        echo json_encode(array('success' => true, 'data' => $users)); 
    }

    function getUserById() {
        $userId = $_POST['userId'] ?? null;
        if ($userId !== null) {

            $user = new User();
            $user = $user->getUserById($userId); 


            echo json_encode(array('success' => true, 'data' => $user));
            http_response_code(200);
        } else {
            echo json_encode(array('success' => false, 'message' => 'userId not provided'));
            http_response_code(422);
        }
       
    }

    function getUserRoles() {
        $userId = $_POST['userId'] ?? null;
        $user = new User();
        $roles = $user->getUserRoles($userId);
        $roleIds = array_map(function($role) {
            return $role['role_id'];
        }, $roles);
        echo json_encode(array('success' => true, 'data' => $roleIds)); 
    }




    function generateToken($userId) {
        $secretKey = 'aaaa';
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;
    
        $payload = array(
            'user_id' => $userId,
            'iat' => $issuedAt,
            'exp' => $expirationTime
        );
    
        return JWT::encode($payload, $secretKey, 'HS256');
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