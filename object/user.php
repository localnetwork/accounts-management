<?php

    class User {
        public function __construct() {
            require_once('../config/config.php');
            require_once('../validators/userValidator.php');
            $dbcon = new Database(); 
            $this->db = $dbcon->getConnection(); 
        } 

        

        public function createUser($userInfo) {

            $formatted_now = str_replace(' ', '#', strtolower(date('Y-m-d H:i:s')));
            $sql = "call sp_createUser(:email, :password, :role, :user_status)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $userInfo['email']); 
            $stmt->bindParam(':password', $userInfo['password']); 
            $stmt->bindParam(':role', $userInfo['role']); 
            $stmt->bindParam(':user_status', $userInfo['user_status']); 
            try {
                $stmt->execute(); 
            }catch(Exception $e) {
                return intval($e->getMessage()); 
            }
        }

        public function createUserProfile($userProfile) {
            $formatted_now = str_replace(' ', '#', strtolower(date('Y-m-d H:i:s')));
            $sql = "call sp_createUserProfile(:first_name, :last_name, :address, :birthday, :user_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':first_name', $userProfile['first_name']); 
            $stmt->bindParam(':last_name', $userProfile['last_name']); 
            $stmt->bindParam(':address', $userProfile['address']); 
            $stmt->bindParam(':birthday', $userProfile['birthday']); 
            $stmt->bindParam(':user_id', $userProfile['user_id']);  

            try {
                $stmt->execute();
                if(isset($stmt)) {  
                    // return 1; 
                    return json_encode(['success' => 1]);
                }else {
                    // throw new Exception(0);
                    throw new Exception(json_encode(['error' => 0]));
                }
            }catch(Exception $e) {
                // echo "Error: " . $e->getMessage();
                // throw new Exception(-1);
                throw new Exception(json_encode(['error' => -1, 'message' => $e->getMessage()]));
            }
            $stmt->close();
        }



        public function userLogin($email, $password) {
            $sql = "CALL sp_userLoginPost(:p_email, :p_password)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':p_email', $email); 
            $stmt->bindParam(':p_password', $password); 
            $stmt->execute(); 
            try {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($result) {
                    // return json_encode(['success' => true, 'message' => 'Login successful.']); 
                } else {
                    // Handle the case when no results are returned
                    // return json_encode(['success' => false, 'message' => 'Login failed.']); 
                } 
            } catch (Exception $e) {
                // return json_encode(['success' => false, 'message' => 'Database error.']); 
            }
        } 
        
        public function getUserInfo($email) {
            try {
                $stmt = $this->db->prepare("CALL sp_getUserInfo(:email)");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return $user;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false; // Error 
            } 
        }

        public function getAllUsers() {
            try {
                $stmt = $this->db->prepare("CALL sp_getAllUsers()");
                $stmt->execute();
                $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $user;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false; // Error 
            } 
        }


        public function getUserById($userId) {
            try {
                $stmt = $this->db->prepare("CALL sp_getUserById(:userId)");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return $user;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false; // Error 
            } 
        }

        public function getUserProfileData($userId){
            try {
                $stmt = $this->db->prepare("CALL sp_getUserProfileData(:uid)");
                $stmt->bindParam(':uid', $userId, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $user;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false; // Error 
            } 
        }

        public function getUserRole($userId) {
            try {
                $stmt = $this->db->prepare("CALL sp_getUserRole(:uid)");
                $stmt->bindParam(':uid', $userId, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $user;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false; // Error 
            } 
        }

        public function getRole($user_role_id) {
            try {
                $stmt = $this->db->prepare("call sp_getRole(:role_id)");
                $stmt->bindParam(':role_id', $user_role_id, PDO::PARAM_STR);
                $stmt->execute();
                $role = $stmt->fetch(PDO::FETCH_ASSOC);
                return $role;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false; // Error 
            } 
        }


        public function updateUser($email,) {
            try {              
                $stmt = $this->db->prepare("call sp_userUpdate(:email)");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);

                $stmt->execute(); 
                $user = $stmt->fetch(PDO::FETCH_ASSOC); 

                var_dump($_SESSION['user']['profile_picture']); 
                return 1; 
                
            } catch (PDOException $e) { 
                echo "Error: " . $e->getMessage();
                return false; // Error
            }

            // $stmt->close();
            // $conn->close();
        }
        
    } 
?>