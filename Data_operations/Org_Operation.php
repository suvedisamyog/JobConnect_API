<?php

class Org{
    private $con;
    function __construct()
    {
        require_once '../includes/DbConnection.php';
        $db = new DbConnect();
        $this->con = $db->connect();        
    }
    public function orgprofile($oName,$oEmail,$oPhone,$oImg,$oWeb,$oLocation){

        if($this->isUserExist($oEmail)){
            return 0; 

        }else {
            $stmt=$this->con->prepare("INSERT INTO org_profile (oName,oEmail,oPhone,oImg ,oWeb,oLocation) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("ssssss",$oName,$oEmail,$oPhone,$oImg,$oWeb,$oLocation );
            if ($stmt->execute()) {
              return 1;
          } else {  
              return 2;
          }
          
        }
  
        
      }
      public function update_reg_complete($oEmail){
        $value="true";
        $stmt = $this->con->prepare("UPDATE registration SET isComplete = ? WHERE Email  = ?");
        $stmt->bind_param("ss",$value, $oEmail);
        if ($stmt->execute()) {
          return 1;
      } else {
          return 2;
      }
      
      }
      private function isUserExist( $uEmail){
        $stmt = $this->con->prepare("SELECT oEmail FROM org_profile WHERE  oEmail = ?");
        $stmt->bind_param("s",  $uEmail);
        $stmt->execute(); 
        $stmt->store_result(); 
        return $stmt->num_rows > 0; 
    }
    public function fetchData($email){
        $stmt=$this->con->prepare("SELECT * FROM org_profile WHERE oEmail=? ");
        $stmt->bind_param("s",  $email);
        $stmt->execute(); 
        $result = $stmt->get_result();
        $datas = array();
        while($row=$result->fetch_assoc()){
            $datas[] = $row;
        }
        return $datas;

    }

    public function orgprofileUpdate($oName,$oEmail,$oPhone,$oWeb,$oImg,$oLocation){
        $stmt = $this->con->prepare("UPDATE org_profile SET oName = ?, oPhone = ?, oWeb = ?, oImg=? ,oLocation=? WHERE oEmail = ?");
        $stmt->bind_param("ssssss", $oName,$oPhone,$oWeb,$oImg,$oLocation,$oEmail);
        if ($stmt->execute() &&  $this->changeName($oName,$oEmail)) {
               return true;
               
        } else {
            return false;
        }
    }

    private function changeName($name,$email){
        $stmt = $this->con->prepare("UPDATE registration SET Name=?  WHERE  Email = ?");
        $stmt->bind_param("ss", $name,$email);
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }


    }
}