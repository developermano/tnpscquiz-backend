<?php

require_once "vendor/autoload.php";
use Firebase\JWT\JWT;

class dbfunction{

   private $dbconn;
   private $jwtkey;

  function __construct(){
   require_once "dbconfig.php";
   $dbconnection=new dbconnection();
   $this->dbconn=$dbconnection->getdb();
   $this->jwtkey=jwt_key;
  }

  function __destruct(){

  }

   function signup($name,$password,$email){
   
    if(!$this->checkuserexist($email)){
      $stmt=$this->dbconn->prepare("INSERT INTO user (name,password,email) VALUES (?, ?, ?)");
      $stmt->bind_param("sss",$name,$password,$email);
      $execute=$stmt->execute();
      $stmt->close();
      
  
      if($execute){
      $stmt=$this->dbconn->prepare("SELECT id from user WHERE email=?");
      $stmt->bind_param("s",$email);
      $execute2=$stmt->execute();
      $getid=$stmt->get_result()->fetch_assoc();
      $this->dbconn->close();
  
     
  
      $currenttime = new DateTime();
     $currenttimestamp=$currenttime->getTimestamp();
  
     $exptime = new DateTime('tomorrow');
     $exptime->format('Y-m-d H:i:s');
     $exptimestamp=$exptime->getTimestamp();
  
  
     $payload = array(
     "userid"=>$getid['id'],
      "iss" => "https://www.tnpscquiz.com",
      "aud" => "https://www.tnpscquiz.com",
      "iat" => $currenttimestamp,
      "nbf" => $currenttimestamp,
      "exp" => $exptimestamp
  );
  
  $jwt = JWT::encode($payload, $this->jwtkey);
  
  //setup result
  $res['status']=$execute2;
  $res['token']=$jwt;
      }
   else{
         $res["status"]=false;
         $res["reason"]="dbconnection is not possible";
      }
      
  
  //$decoded = JWT::decode($jwt, $this->jwtkey, array('HS256'));
  //$decoded_array = (array) $decoded;
  //echo $jwt;
  //print_r($decoded_array['userid']);
  
    }else{
      $res['status']=false;
      $res['reason']="user already exists";
    }
return $res;
   }


function checkuserexist($email){
   
   $stmt=$this->dbconn->prepare("SELECT id from user WHERE email=?");
   $stmt->bind_param("s",$email);
   $execute2=$stmt->execute();
   $result=$stmt->get_result();
   if($result->num_rows>0){
$exist=true;
   }
   else{
$exist=false;
   }
return $exist;

}

function signin($email,$password){
   if($this->checkuserexist($email)){
      $stmt=$this->dbconn->prepare("SELECT id from user WHERE email=? AND password=?");
      $stmt->bind_param("ss",$email,$password);
      $execute2=$stmt->execute();
      $result=$stmt->get_result();
      if($result->num_rows>0){

         $stmt=$this->dbconn->prepare("SELECT id from user WHERE email=?");
         $stmt->bind_param("s",$email);
         $execute2=$stmt->execute();
         $getid=$stmt->get_result()->fetch_assoc();
         $this->dbconn->close();
     
        
     
         $currenttime = new DateTime();
        $currenttimestamp=$currenttime->getTimestamp();
     
        $exptime = new DateTime('tomorrow');
        $exptime->format('Y-m-d H:i:s');
        $exptimestamp=$exptime->getTimestamp();
     
     
        $payload = array(
        "userid"=>$getid['id'],
         "iss" => "https://www.tnpscquiz.com",
         "aud" => "https://www.tnpscquiz.com",
         "iat" => $currenttimestamp,
         "nbf" => $currenttimestamp,
         "exp" => $exptimestamp
     );
     
     $jwt = JWT::encode($payload, $this->jwtkey);
   $response['status']=true;
   $response['jwt']=$jwt;
      }
      else{
   $response['status']=false;
   $response['reason']='wrong password';
      }
   
   }else{
      $response['status']=false;
      $response['reason']='user didn\'t exists';
   }
   return $response;
   }
   
   
   
   
   
   
   
   


}

?>
