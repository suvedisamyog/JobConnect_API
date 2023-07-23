<?php
require_once '../Data_operations/Job_Operation.php';
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['category_names'])) {
        $categoryNamesJsonString  = $_POST['category_names'];
        $categoryNamesArray  = json_decode($categoryNamesJsonString, true);

        foreach ($categoryNamesArray as $categoryName) {
            $job = new Job();
            $results = $job->suggestedJobs($categoryName);
            if ($results) {
                $response['error'] = false;
                $response['data'][] = $results;
            } else {
                $response['error'] = false;
                $response['message'] = "No data found";
            }
        }
        
    } else {
        $response['error'] = true;
        $response['message'] = "No categories received";
    }
} else {
    $response['error'] = true;
    $response['message'] = "Invalid Request Method";
}

echo json_encode($response);
