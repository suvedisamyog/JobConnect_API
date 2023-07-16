<?php

require_once '../Data_operations/Job_Operation.php';
$response = array(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobId = $_POST['Job_id'];
    $Email = $_POST['email'];
     
    if (isset($Email) && isset($jobId)) {
        $job = new Job();
        $result = $job->applyJob($Email, $jobId);
        
        if ($result) {
            $response['error'] = false;
            $response['message'] = "Applied Successfully";
        } else {
            $response['error'] = true;
            $response['message'] = "Error while Applying! Try Again";
        }
    } else {
        $response['error'] = true;
        $response['message'] = "ID or Email not found";
    }
} else {
    $response['error'] = true;
    $response['message'] = "Invalid Request Method";
}

echo json_encode($response);

