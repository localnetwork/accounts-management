<?php

    class User {
        public function __construct() {
            require_once(__DIR__ . '/../config/config.php');
            require_once(__DIR__ . '/../validators/userValidator.php');
            $dbcon = new Database(); 
            $this->db = $dbcon->getConnection(); 
        } 

        

        public function createUser($userInfo) {

            $formatted_now = str_replace(' ', '#', strtolower(date('Y-m-d H:i:s')));
            $sql = "call sp_createUser(:first_name, :last_name, :email, :password)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':first_name', $userInfo['first_name']); 
            $stmt->bindParam(':last_name', $userInfo['last_name']); 
            $stmt->bindParam(':email', $userInfo['email']); 
            $stmt->bindParam(':password', $userInfo['password']); 
            try {
                if(userEmailValid($userInfo['email'])) {
                    if(userExists($userInfo['email'])) {
                        // throw new Exception(-2);
                        throw new Exception(json_encode(['error' => -2]));
                    } else {
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
                    }
                    $stmt->close();
                }else{
                    throw new Exception(-3);
                }
            }catch(Exception $e) {
                return intval($e->getMessage()); 
            }
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
                    return json_encode(['success' => true, 'message' => 'Login successful.']); 
                } else {
                    // Handle the case when no results are returned
                    return json_encode(['success' => false, 'message' => 'Login failed.']); 
                } 
            } catch (Exception $e) {
                return json_encode(['success' => false, 'message' => 'Database error.']); 
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

        public function getMediaInfo($media_id) {
            try {
                $stmt = $this->db->prepare("call sp_getMediaById(:mid)");
                $stmt->bindParam(':mid', $media_id, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
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


        public function updateUser($email, $first_name, $last_name, $profile_picture) {
            try {              
                $stmt = $this->db->prepare("call sp_userUpdate(:email, :first_name, :last_name, :profile_picture)");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);

                if(isset($profile_picture['name']) && strlen($profile_picture['name']) > 0) {

                    

                    $media_id = insertMedia($profile_picture, $this->db); 
 
                    $stmt->bindParam(':profile_picture', $media_id, PDO::PARAM_STR); 
                    unset($_SESSION['user']['profile_picture']);  
                    $_SESSION['user']['profile_picture'] = $media_id; 
                    $stmt->execute(); 

                    // if($_SESSION['user']['profile_picture']) {
                    //     deleteMedia($_SESSION['user']['profile_picture'], $this->db);
                    // } 
                    
                }else {
                    $stmt->bindParam(':profile_picture', $_SESSION['user']['profile_picture'], PDO::PARAM_STR);
                    $stmt->execute(); 
                } 
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