<?php

class Registration_Login{
    private $con; 

    function __construct(){

        require_once '../includes/DbConnection.php';
        $db = new DbConnect();
        $this->con = $db->connect();

    }
    public function createUser($name, $email, $password, $userType,$isComplete)
    {
        if($this->isUserExist($email)){
            return 0; 

        }else{       
            $password = md5($password);
            $stmt = $this->con->prepare("INSERT INTO `registration` (`name`, `email`, `password`, `userType`,`isComplete`) VALUES (?, ?, ?, ?,?);");
            $stmt->bind_param("sssss", $name, $email, $password, $userType,$isComplete);
    
            if ($stmt->execute()) {
                return 1;
            } else {
                return 2;
            }
        }
        
    }
    public function userLogin($email,$password){
        $password = md5($password);
        $stmt = $this->con->prepare("SELECT email FROM registration WHERE email = ? AND password = ?");
			$stmt->bind_param("ss",$email,$password);
			$stmt->execute();
			$stmt->store_result(); 
			return $stmt->num_rows > 0;
    }
        public function getUserDetail($email){
        $stmt = $this->con->prepare("SELECT * FROM registration WHERE email = ?");
			$stmt->bind_param("s",$email);
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc();
    }
    private function isUserExist( $email){
        $stmt = $this->con->prepare("SELECT email FROM registration WHERE  email = ?");
        $stmt->bind_param("s",  $email);
        $stmt->execute(); 
        $stmt->store_result(); 
        return $stmt->num_rows > 0; 
    }
    
		
    
}