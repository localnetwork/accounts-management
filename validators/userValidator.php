<?php

    require_once(__DIR__ . '/../config/config.php');

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
        if (empty($errors)) {
            return array('success' => true, 'data' => $data);
        } else {
            return array('success' => false, 'errors' => $errors);
        }
    }

?>