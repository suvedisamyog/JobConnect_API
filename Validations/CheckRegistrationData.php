<?php
require_once '../includes/DbConnection.php';

class CheckRegistrationData {
    private $con; 

    public function validate($name, $email, $password, $repassword) {
        $validationErrors = array();

        // Validate name
        if (empty($name)) {
            $validationErrors[] = "Name is required";
        } else if (strlen($name) < 5) {
            $validationErrors[] = "Name should be minimum of 5 characters long";
        } else if (!preg_match("/^[a-zA-Z0-9_]+$/", $name)) {
            $validationErrors[] = "Name should only contain alphanumeric and underscore characters";
        }
        
        // Validate email
        if (empty($email)) {
            $validationErrors = "Email is required";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validationErrors = "Invalid email format";
        }
        //checkexisting email
        $db = new DbConnect();
        $this->con = $db->connect();
        $stmt = $this->con->prepare("SELECT email FROM registration WHERE  email = ?");
			$stmt->bind_param("s", $email);
			$stmt->execute(); 
			$stmt->store_result();
            if( $stmt->num_rows > 0){
                $validationErrors= "Email already Registered";

            }


        

        // Validate password
        if (empty($password)) {
            $validationErrors= "Password is required";
        } else if (strlen($password) < 6) {
            $validationErrors= "Password should be minimum of 6 characters long";
        } else if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            $validationErrors = "Password should contain at least one letter and one number";
        }

        // Validate repassword
        if (empty($repassword)) {
            $validationErrors = "Please confirm password";
        } else if ($repassword !== $password) {
            $validationErrors= "Password confirmation does not match";
        }

        return $validationErrors;
    }
}
