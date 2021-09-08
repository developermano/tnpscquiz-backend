<?php

class dbfunction{

   private $dbconn;

  function __construct(){
   require_once "dbconfig.php";
   $dbconnection=new dbconnection();
   $this->dbconn=$dbconnection->getdb();


  }

  function __destruct(){

  }

   function signup($name,$password,$email){
   
    $stmt=$this->dbconn->prepare("INSERT INTO user (name,password,email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss",$name,$password,$email);
    $res=$stmt->execute();
    $stmt->close();
    $this->dbconn->close();
    return $res;

   }



   


}

?>
