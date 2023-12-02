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
        $requiredFields = ['first_name', 'last_name', 'address', 'birthday', 'email', 'password', 'confirm_password', 'user_status', 'role'];
        // $requiredFields = ['first_name', 'last_name',  'email', 'password', 'confirm_password'];

        $validationResult = validateUserData($_POST, $requiredFields);


        if ($validationResult['success']) {
            $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
            $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
            $address = isset($_POST['address']) ? $_POST['address'] : '';
            $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : '';
            
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = isset($_POST['role']) ? $_POST['role'] : '';
            $user_status = isset($_POST['user_status']) ? $_POST['user_status'] : '';
            $userInfo = array(
                "email" => $email,
                "password" => $password,
                "role" => $role,
                "user_status" => $user_status,
            );
            
            $user = new User();
            $ret = $user->createUser($userInfo);
            $getUserInfo = $user->getUserInfo($email); 

            if(isset($getUserInfo) && $getUserInfo['id']) { 
                $userProfile = array(
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                    "address" => $address, 
                    "birthday" => $birthday,
                    "user_id" => $getUserInfo['id']
                );

                $createProfile = $user->createUserProfile($userProfile); 

                $token = generateToken($getUserInfo['id']);
            
                $userData = array(
                    'token' => $token,
                    'user_profile' => $userProfile, 
                );
                echo json_encode($userData);
            }else {
                echo json_encode(array('success' => false, 'user_info' => $getUserInfo,'id' => $getUserInfo['id'], 'errors' => 'Cannot be saved.'));
            }
        } else {
            http_response_code(422);
            echo json_encode(array('success' => false, 'errors' => $validationResult['errors']));
        }
    }

    

    function userLogin() {
        require_once('../validators/userValidator.php');
        // $requiredFields = ['first_name', 'last_name', 'email', 'password'];
        $requiredFields = ['email', 'password'];

        $validationResult = validateLogin($_POST, $requiredFields);

        if ($validationResult['success']) {
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            $user = new User();
            $ret = $user->userLogin($email, $password); 
            $uInfo = $user->getUserInfo($email); 
            $token = generateToken($uInfo['id']);
            $eee = array(
                'token' => $token,
                'user_info' => array(
                    'id' => $uInfo['id'],
                    'email' => $uInfo['email'],
                ),
            );

            echo json_encode(array('success' => true, 'data' => $eee));
            http_response_code(200);
            
        } else {
            http_response_code(422); 
            echo json_encode(array('success' => false, 'errors' => $validationResult['errors']));
        }
    } 


    function getAllUsers() {
        $search_query = $_POST['searchQuery']; 
        $users = new User();
        $users = $users->getAllUsers($search_query);
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

    function getUserRole() {
        $userId = $_POST['userId'] ?? null;
        $user = new User();
        $role = $user->getUserRole($userId);
        // $roleIds = array_map(function($role) {
        //     return $role['role_id'];
        // }, $roles);
        echo json_encode(array('success' => true, 'data' => $role[0], 'status' => $role[0]['user_status'])); 
    }

    function getUserProfileData() {
        $userId = $_POST['userId'] ?? null;

        $user = new User(); 

        $userInfo = $user->getUserProfileData($userId); 

        echo json_encode(array('success' => true, 'data' => $userInfo)); 
    }
    // function getUserRoles() {
        // $userId = $_POST['userId'] ?? null;
        // $user = new User();
        // $roles = $user->getUserRoles($userId);
        // $roleIds = array_map(function($role) {
        //     return $role['role_id'];
        // }, $roles);
        // echo json_encode(array('success' => true, 'data' => $roleIds)); 
    // }

    function updateUser() {
        $user = new User(); 

        $requiredFields = ['first_name', 'last_name', 'address', 'birthday', 'email'];

        // isset($_POST['password']) ? $_POST['password'] : '';
        

        $validationResult = validateUserUpdate($_POST, $requiredFields);

        if ($validationResult['success']) { 
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : ''; 
            $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : ''; 
            $address = isset($_POST['address']) ? $_POST['address'] : ''; 
            $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : ''; 
            $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : ''; 
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            $user = new User();
            $ret['userProfile'] = $user->updateUserProfile($first_name, $last_name, $address, $birthday, $_POST['userId']); 
            $ret['id'] = $_POST['userId']; 


            // $firstName = $userInfo['first_name'];
            // $lastName = $userInfo['last_name'];
            // $address = $userInfo['address'];
            // $birthday = $userInfo['birthday'];


            // echo json_encode(array('success' => false, 'message' => $_POST['userInfo'])); 
            echo json_encode(array('success' => true, 'data' => $ret, 'ress' =>  $ret['userProfile']));
            http_response_code(200);
            
        } else {
            http_response_code(422); 
            echo json_encode(array('success' => false, 'errors' => $validationResult['errors']));
        }
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