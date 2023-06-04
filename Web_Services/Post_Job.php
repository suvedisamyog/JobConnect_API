<?php

require_once '../Data_operations/Job_Operation.php';
$response = array(); 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $empType = trim($_POST['empType']);
    $education = trim($_POST['education']);
    $experience = trim($_POST['experience']);
    $industry = trim($_POST['industry']);
    $category = trim($_POST['category']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $deadline = trim($_POST['deadline']);


    if (!empty($title) && !empty($description) && !empty($empType) && !empty($education) && !empty($experience) && !empty($industry) && !empty($category) && !empty($name) && !empty($email) && !empty($deadline)){

        $currentTimestamp = date('Y-m-d H:i:s'); 

        $daysMap = [
            "After 3 Days" => 3,
            "After 5 Days" => 5,
            "After 7 Days" => 7,
            "After 9 Days" => 9,
            "After 13 Days" => 13
        ];
        
        $daysToAdd = isset($daysMap[$deadline]) ? $daysMap[$deadline] : 3;
        $deadlineTimestamp = date('Y-m-d H:i:s', strtotime("+$daysToAdd days", strtotime($currentTimestamp)));
        
      

   
        $job = new Job();
        $result=$job->postjob($title, $description, $empType, $education, $experience, $industry, $category, $name, $email,$deadlineTimestamp);

        if($result==1){
            $response['error'] = false;
            $response['message'] = "Job Posted successfully";
        }else if($result==2){
            $response['error'] = true;
            $response['message'] = "Failed to Post Job,Try Again";
        }
    

}else{
    $response['error'] = true; 
    $response['message'] = "One or more Fields Are Empty,Try Again";
   }
}

else{
    $response['error'] = true; 
    $response['message'] = "Invalid Request Method";
}
echo json_encode($response);
