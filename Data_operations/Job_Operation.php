<?php

class Job{

    private $con;
    private  $currentDateTime;


    function __construct()
    {
        require_once '../includes/DbConnection.php';
        $db = new DbConnect();
        $this->con = $db->connect(); 
        $this->currentDateTime = date('Y-m-d H:i:s');
        $this->checkJobDeadline();
  
    }
    public function checkJobDeadline(){    
        $stmt = $this->con->prepare("SELECT j_id,deadline FROM jobs ");
        $stmt->execute();
        $result = $stmt->get_result();
    
        while ($row = $result->fetch_assoc()) {
          $deadline=$row['deadline'];
          $newDeadline = date('Y-m-d H:i:s', strtotime($deadline . ' +2 months'));
          if( $this->currentDateTime>$newDeadline){
            $this->deleteJob($row['j_id']);

          }

        }
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
     $this->currentDateTime = date('Y-m-d H:i:s');

    while ($row = $result->fetch_assoc()) {
      if ($row['deadline'] <  $this->currentDateTime) {
        $this->deleteJob($row['j_id']);
      
    } else {
        $jobs[] = $row;
    }
  }
  return $jobs;

  }  

  public function deleteJob($jobId) {
        $stmt1 = $this->con->prepare("DELETE FROM jobs WHERE j_id = ?");
    $stmt1->bind_param("i", $jobId);
    $stmt1->execute();
    
    $stmt2 = $this->con->prepare("DELETE FROM applied_jobs WHERE Job_id = ?");
    $stmt2->bind_param("i", $jobId);
    $stmt2->execute();
    
    $stmt3 = $this->con->prepare("DELETE FROM job_saved WHERE job_id = ?");
    $stmt3->bind_param("i", $jobId);
    $stmt3->execute();
    
    return ($stmt1->affected_rows > 0 || $stmt2->affected_rows > 0 || $stmt3->affected_rows > 0);
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
    if ($row['deadline'] >  $this->currentDateTime) {
      $jobs[] = $row;
  }  }
  return $jobs;

}



public function getAllSavedJobs($email){
  $stmt=$this->con->prepare("SELECT * FROM job_saved WHERE user_email=? ORDER BY saved_id  DESC");
  $stmt->bind_param("s",$email);
  $stmt->execute();
  $result = $stmt->get_result();
  $jobs = array();
  while ($row = $result->fetch_assoc()){
    $jobId = $row['job_id'];
    $jobData = $this->getJobData($jobId);

    if ($jobData && $jobData['deadline'] >  $this->currentDateTime) {
      $jobs[] = $jobData;
  }
}
  return $jobs;
}

public function getJobData($jobId){
  $stmt = $this->con->prepare("SELECT * FROM jobs WHERE j_id=?");
  $stmt->bind_param("s", $jobId);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if($row = $result->fetch_assoc()){
    return $row;
  }
  

}

public function toggleJob($jobId, $userEmail) {
  $stmt = $this->con->prepare("SELECT * FROM job_saved WHERE job_id = ? AND user_email = ?");
  $stmt->bind_param("is", $jobId, $userEmail);
  $stmt->execute();
  $result = $stmt->get_result();
 if($result->num_rows > 0){
  $stmt = $this->con->prepare("DELETE FROM job_saved WHERE job_id = ? AND user_email = ?");
  $stmt->bind_param("is", $jobId, $userEmail);
  $stmt->execute();
  if ($stmt->affected_rows > 0){
    return "Removed";
  }else{
    return "Error while Removing";
  } 
 }else{
  $stmt = $this->con->prepare("INSERT INTO  job_saved (job_id,user_email) VALUES (?,?)");
  $stmt->bind_param("is", $jobId, $userEmail);
  if ($stmt->execute()) {
    return "Added";
} else {
    return "Error while adding"; 
} 
}
}

public function SavedJobs($userEmail){
  $stmt=$this->con->prepare("SELECT job_id FROM job_saved WHERE user_email=? ORDER BY saved_id  DESC");
  $stmt->bind_param("s",$userEmail);
  $stmt->execute();
  $result = $stmt->get_result();
  $jobId = array();
  while ($row = $result->fetch_assoc()){
    $jobId[] = $row;
  }
  return $jobId;

  
} 

  public function checkId($Email,$jId){
    $stmt=$this->con->prepare("SELECT * FROM job_saved WHERE user_email=? AND  job_id=?");
    $stmt->bind_param("ss",$Email,$jId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      return true;
    } else {
      return false;
    }

}
public function checkApplied($Email, $jobId) {
  $stmt = $this->con->prepare("SELECT Status FROM applied_jobs WHERE Applied_By=? AND Job_id=?");
  $stmt->bind_param("ss", $Email, $jobId);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = $row['Status'];
    return array('status' => $status, 'exists' => true);
  } else {
    return array('status' => null, 'exists' => false);
  }
}
public function applyJob($Email, $jobId) {
  $status = "Pending"; 
  
  $stmt = $this->con->prepare("INSERT INTO applied_jobs (Job_id, Applied_By, Status) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $jobId, $Email, $status); 
  
  if ($stmt->execute()) {
      return true;
  } else {
      return "Error while adding"; 
  } 
}

public function getAllAppliedJobs($email){
  $stmt=$this->con->prepare("SELECT * FROM applied_jobs WHERE Applied_By=? ORDER BY applied_id   DESC");
  $stmt->bind_param("s",$email);
  $stmt->execute();
  $result = $stmt->get_result();
  $jobs = array();
  while ($row = $result->fetch_assoc()){
    $jobId = $row['Job_id'];
    $jobData = $this->getJobData($jobId);
    $jobData['Status'] = $row['Status']; 
    $jobs[] = $jobData;
  }
  return $jobs;
}







}