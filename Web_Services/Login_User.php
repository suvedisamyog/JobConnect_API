<?php

require_once '../Data_operations/Registartion_Login.php';
$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST'){
    $email=$_POST['email'];
    $password=$_POST['password'];
    if(isset($email) and isset($password)){
        $db=new Registration_Login();
        if($db->userLogin($email,$password)){
            $user=$db->getUserDetail($email);
            $response['error'] = false; 
            $response['email'] = $user['Email'];
            $response['name'] = $user['Name'];
            $response['userType'] = $user['UserType'];
            $response['isComplete']=$user['isComplete'];
            $response['message'] = "Login Successfull";		
        }else{
            $response['error'] = true; 
			$response['message'] = "Invalid email or password";
        }     
    }else{
        $response['error'] = true; 
		$response['message'] = "Required fields are missing";    }
}else{
    $response['error'] = true; 
    $response['message'] = "Invalid Request Method";
}
echo json_encode($response);