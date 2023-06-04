<?php

class Org{
    private $con;
    function __construct()
    {
        require_once '../includes/DbConnection.php';
        $db = new DbConnect();
        $this->con = $db->connect();        
    }
    public function orgprofile($oName,$oEmail,$oPhone,$oImg,$oWeb){

        if($this->isUserExist($oEmail)){
            return 0; 

        }else {
            $stmt=$this->con->prepare("INSERT INTO org_profile (oName,oEmail,oPhone,oImg    ,oWeb) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss",$oName,$oEmail,$oPhone,$oImg,$oWeb );
            if ($stmt->execute()) {
              return 1;
          } else {  
              return 2;
          }
          
        }
  
        
      }
      public function update_reg_complete($oEmai){
        $value="true";
        $stmt = $this->con->prepare("UPDATE registration SET isComplete = ? WHERE Email  = ?");
        $stmt->bind_param("ss",$value, $oEmai);
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
}