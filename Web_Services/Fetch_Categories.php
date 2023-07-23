<?php
require_once '../Data_operations/User_Operation.php';
$response = array(); 


if($_SERVER['REQUEST_METHOD']=='POST'){
    $email=$_POST['email'];
    if(isset($email)){
        $db=new User();
        $result1 = $db->fetchUserInterest($email);
        $result2 = $db->fetchUserApplied($email);
        if($result1 && $result2){
            $response['error'] = false;
            $response['InterestCate'] = $result1;
            $response['AppliedCat'] = $result2;
        }else{
            $response['error'] = true;
            $response['message'] = "No data found";
        }

    }else{
        $response['error'] = true; 
		$response['message'] = "Required fields are missing";
    }


}else{
    $response['error'] = true; 
    $response['message'] = "Invalid Request Method";
}
echo json_encode($response);