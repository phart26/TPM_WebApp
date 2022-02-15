<?php

ini_set("error_displays","1");

include_once($_SERVER['DOCUMENT_ROOT'].'/api/core/config.php');
$db = new mysqli($config['database']['host'], $config['database']['username'], $config['database']['password'], $config['database']['database']);

class quotes_setting{
    private $user_id;
    
    public function __construct(){

        $this->user_id = $_SESSION['user_id'];   
        global $db;
        

   }
  
   public function settring_field(){
    $stmt = $db->prepare("SELECT `fields` from `quotes_setting` where user_id = ?");
    $stmt->bindParam(1,$this->user_id);
    return $this->user_id;
        if ($stmt->execute()){
            //echo 'run';
            return $this->user_id;
        }
        else{
            
            return $this->user_id;
        }

   }


}
