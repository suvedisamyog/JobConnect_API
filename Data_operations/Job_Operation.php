<?php

class Job{

    private $con;

    function __construct()
    {
        require_once '../includes/DbConnection.php';
        $db = new DbConnect();
        $this->con = $db->connect();        
    }

public function postjob($title, $description, $empType, $education, $experience, $industry, $category, $name, $email,$deadline,$salary,$vacancies){
  $stmt = $this->con->prepare("INSERT INTO jobs (j_title, j_description, j_empType, j_education, j_experience, j_industry, j_category,j_name, j_email,deadline,j_salary,vacancies) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");
  $stmt->bind_param("ssssssssssss", $title, $description, $empType, $education, $experience, $industry, $category, $name, $email,$deadline,$salary,$vacancies);
  if ($stmt->execute()) {
    return 1;
} else {
    return 2; 
}

        
    }

  public function JobsByOrg($email){
    $stmt=$this->con->prepare("SELECT * FROM jobs WHERE j_email=? ORDER BY j_id DESC");

    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();
    $jobs = array();
    $currentDateTime = date('Y-m-d H:i:s');

    while ($row = $result->fetch_assoc()) {
      if ($row['deadline'] < $currentDateTime) {
        $this->deleteJob($row['j_id']);
      
    } else {
        $jobs[] = $row;
    }
  }
  return $jobs;

  }  

  public function deleteJob($jobId) {
    $stmt = $this->con->prepare("DELETE FROM jobs WHERE j_id = ?");
    $stmt->bind_param("i", $jobId);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}

public function update_job($jobId,$title,$description,$empType,$education,$experience,$industry,$category,$vacancies,$salary){
      $stmt = $this->con->prepare("UPDATE jobs SET j_title = ?, j_description = ?, j_empType = ?, j_education = ?, j_experience = ?, j_industry = ?, j_category = ? ,j_salary=?,vacancies=? WHERE j_id = ?");
     
      $stmt->bind_param("ssssssssss", $title, $description, $empType, $education, $experience, $industry, $category,$salary,$vacancies,$jobId);
      if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }

}

public function allJobs(){
  $stmt=$this->con->prepare("SELECT * FROM jobs  ORDER BY j_id DESC");
  $stmt->execute();
  $result = $stmt->get_result();
  $jobs = array();
  while ($row = $result->fetch_assoc()){
    $jobs[] = $row;
  }
  return $jobs;

}


}