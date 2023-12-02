<?php

    require_once('../config/config.php');
    require_once('../object/user.php');

    function userExists($email) {
        $dbcon = new Database(); 

        $db = $dbcon->getConnection();  

        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);

        try {
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            return true;
        }
    } 

    function userEmailValid($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true; 
        } else {
            return false; 
        }
    }

    function validateUserData($data, $requiredFields) {
        // Implement your validation logic here
        $errors = array();

        $allowedRoleIds = [1, 2]; 
        
        if(!in_array($_POST['roleId'], $allowedRoleIds)) {
            $errors['not_allowed'] = "You're not allowed to do this function.";
        }else {
            // Additional validation rules
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please provide a valid format.';
            } 
        
            if(userExists($data['email'])) {
                $errors['email'] = 'Email is already taken. Please try another';
            }
            if($_POST['password'] != $_POST['confirm_password']) {
                $errors['password'] = 'Password does not match.';
                $errors['confirm_password'] = 'Password does not match.';
            }
        
            // Check if required fields are not empty
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                }
            }
        }
        
        if (empty($errors)) {
            return array('success' => true, 'data' => $data);
        } else {
            return array('success' => false, 'errors' => $errors);
        }
    }

    function validateLogin($data, $requiredFields) {
        $user = new User();
        $errors = array();
        $default_error = 'These credentials do not match our records.'; 


        $result = $user->getUserInfo($data['email']);

        if(isset($result['user_status']) && $result['user_status'] === 1) {
            $errors['status'] = 'You are not allowed to login. Your account has been blocked, please contact administrator.'; 
        }
        
        

        if(userExists($data['email'])) {
            if(isset($result['password'])) {
                $hashedPassword = $result['password'];
                if (password_verify($data['password'], $hashedPassword)) {
                    // Throw session to frontend.

                } else {
                    $errors['email'] = $default_error; 
                    $errors['password'] = $default_error; 
                }
            }
            
        }else {
            $errors['email'] = $default_error; 
            $errors['password'] = $default_error; 
        }

        if (empty($errors)) {
            // return array('success' => true); 
            return array('success' => true, 'data' => $data);
        } else {
            return array('success' => false, 'errors' => $errors);
        }
    }

    function validateUserUpdate($data, $requiredFields) {
        $user = new User();
        $errors = array();
        // $errors['test'] = $_POST['current_password']; 
        // if(!empty($_POST['current_password'])) {
        //     $errors['not_empty'] = "Not empty"; 
        // }
        // if(!empty($_POST['current_password']) && (empty($_POST['password']) || empty($_POST['confirm_password']))) {
            // $errors['password'] = 'Password is required.';  
            // $errors['confirm_password'] = 'Confirm Password is required.';  
        // }

        if($_POST['current_password'] != '') {
            if ($_POST['password'] == 'undefined' || $_POST['confirm_password'] == 'undefined') {
                $errors['password'] = 'New Password is required.';  
                $errors['confirm_password'] = 'Confirm Password is required.';  
            } else {
                // 'password' and 'confirm_password' are set, you can proceed with your logic
                $current_password = $_POST['current_password'];
                $new_password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];
        
                // Now you can perform further validation or update the password
                // For example, you might check if the new password matches the confirm password
                if ($new_password !== $confirm_password) {
                    $errors['password'] = 'New Password does not match to confirm password.';  
                    $errors['confirm_password'] = 'Confirm password does not match to new password.';  
                } else {
                    // Your logic for updating the password goes here
                    // ...
                }
            }
        }

        if(isset($_POST['userId'])) {
            $getPassword = $user->getPasswordById($_POST['userId']);

            if(!empty($_POST['current_password']) && !password_verify($_POST['current_password'], $getPassword['password'])) {
                $errors['current_password'] = 'The current password is incorrect.';
            }

            if(!empty($_POST['current_password']) || $_POST['current_password'] != '') {
                $currentPasswordInput = $getPassword['password'];
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                if (password_verify($_POST['password'], $currentPasswordInput)) {
                    $errors['password'] = 'Current password and new password cannot be the same.'; 
                    $errors['confirm_password'] = 'Current password and new password cannot be the same.'; 
                }
            }
        }

        if(strlen($_POST['current_password']) > 0 && strlen($_POST['password']) == 0 && strlen($_POST['confirm_password']) == 0) {
            $password_error = 'Password should not be empty.';
            $errors['password'] = $password_error;
            $errors['confirm_password'] = $password_error;
        }

        if(strlen($_POST['password']) > 1 || strlen($_POST['confirm_password']) > 1 && strlen($_POST['current_password']) == 0) {
            $errors['current_password'] = 'Current password is required.'; 
        }

        if(isset($_POST['current_password']) && $_POST['current_password'] && $_POST['password'] != $_POST['confirm_password']) {
            $errors['password'] = 'Password does not match.';
            $errors['confirm_password'] = 'Password does not match.';
        } 

        if(isset($data['email']) && $data['email'] != $_POST['email'] && userExists($_POST['email'])) {
            $errors['email'] = 'Email is already taken. Please try another';
        }


        // Check if required fields are not empty
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        } 

        if (empty($errors)) {
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);  
            $test = $user->updateUser($_POST['email'], $new_password, $_POST['userId']); 

            $errors['test'] = $test; 
            
            return array('success' => true, 'data' => $data);
            
        } else {
            return array('success' => false, 'errors' => $errors);
        }

    }

?>