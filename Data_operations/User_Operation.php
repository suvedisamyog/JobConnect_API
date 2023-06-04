<?php

class User{

    private $con;

    function __construct()
    {
        require_once '../includes/DbConnection.php';
        $db = new DbConnect();
        $this->con = $db->connect();        
    }

    public function userProfile($uName,$uEmai,$uDob,$uPhone,$uEducation,$uBio,$uImg,$uCv,$uCategories){

        if($this->isUserExist($uEmai)){
            return 0; 

        }else {
            $stmt=$this->con->prepare("INSERT INTO user_profile (uName,uEmail,uDob,uPhone,uEducation,uBio,uImg,uCv,uCategories) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssssss",$uName,$uEmai,$uDob,$uPhone,$uEducation,$uBio,$uImg,$uCv,$uCategories );
            if ($stmt->execute()) {
              return 1;
          } else {  
              return 2;
          }
          
        }
  
        
      }
      
      public function update_reg_complete($uEmai){
        $value="true";
        $stmt = $this->con->prepare("UPDATE registration SET isComplete = ? WHERE Email  = ?");
        $stmt->bind_param("ss",$value, $uEmai);
        if ($stmt->execute()) {
          return 1;
      } else {
          return 2;
      }
      
      }

      private function isUserExist( $uEmai){
        $stmt = $this->con->prepare("SELECT uEmail FROM user_profile WHERE  uEmail = ?");
        $stmt->bind_param("s",  $uEmai);
        $stmt->execute(); 
        $stmt->store_result(); 
        return $stmt->num_rows > 0; 
    }
    

}