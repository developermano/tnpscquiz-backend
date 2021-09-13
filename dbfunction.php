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
   
   
   
function jwttouserid($jwt){
   try {
      $decoded = JWT::decode($jwt, $this->jwtkey, array('HS256'));
  $decoded_array = (array) $decoded;
  $response['isjwt']=true;
  $response['id']=$decoded_array['userid'];
  return $response;
   } catch (Exception $e) {
      $response['isjwt']=false;
   }
} 
   
  function profileinfo($token){
   $preresult=$this->jwttouserid($token);
   if($preresult['isjwt']){

   $stmt=$this->dbconn->prepare("SELECT * from user WHERE id=?");
   $stmt->bind_param("i",$preresult['id']);
   $execute=$stmt->execute();
   $getresult=$stmt->get_result()->fetch_assoc();
   $this->dbconn->close();
$response['name']=$getresult['name'];
$response['email']=$getresult['email'];
  }else{
     $response['signinstatus']=false;
  }

  return $response;
   
}  


function isauth($token){

  try {
   $decoded = JWT::decode($token, $this->jwtkey, array('HS256'));
  $response=true;
  } catch (Exception $e) {
   $response=false;
  }
  
return $response;
}


function getquestionbyrandom($limit){
//we need to change the function back
//because i give question with answer.
   $stmt=$this->dbconn->prepare("SELECT * from quiz ORDER BY RAND () LIMIT 0,?");
   $stmt->bind_param("i",$limit);
   $execute=$stmt->execute();
   $result=$stmt->get_result();
   $this->dbconn->close();
   while ($row = $result->fetch_assoc()) {
     
      $prefinalresult[]=$row;
  }
  
   return $prefinalresult;

}


function addscore($token,$score){
   $jwttouserid=$this->jwttouserid($token);

   if($jwttouserid["isjwt"]==true)
   {
   $userid=$jwttouserid["id"];
   $currentdate=date("Y-m-d");

   $stmt=$this->dbconn->prepare("INSERT INTO score(userid,score,date) VALUES (?,?,?)");
   $stmt->bind_param("iis",$userid,$score,$currentdate);
   $execute=$stmt->execute();

   if($execute){
      $response['scoreisadded']=true;
   }else{
      $response['scoreisadded']=false;
   }

}
   else{
      $response['scoreisadded']=false;
   }

$stmt->close();
   return $response;
}


function listscore(){
   $currentdate=date("Y-m-d");
   $stmt=$this->dbconn->prepare("SELECT user.name,score.score from score INNER JOIN user ON score.userid = user.id AND score.date=?");
   $stmt->bind_param("s",$currentdate);
   $execute=$stmt->execute();
   $result=$stmt->get_result();
   $this->dbconn->close();
   while ($row = $result->fetch_assoc()) {
     
      $prefinalresult[]=$row;
  }

   return $prefinalresult;

}



function getlastfiveblogpost(){


   $stmt=$this->dbconn->prepare("SELECT * from blog ORDER BY id DESC limit 0,5");
   $execute=$stmt->execute();
   $result=$stmt->get_result();
   $this->dbconn->close();
   while ($row = $result->fetch_assoc()) {
     
      $prefinalresult[]=$row;
  }

   return $prefinalresult;

   
}



function checkadminexist($email){
   
   $stmt=$this->dbconn->prepare("SELECT id from admin WHERE email=?");
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


function adminlogin($email,$password){

      if($this->checkadminexist($email)){
         $stmt=$this->dbconn->prepare("SELECT id from admin WHERE email=? AND password=?");
         $stmt->bind_param("ss",$email,$password);
         $execute2=$stmt->execute();
         $result=$stmt->get_result();
         if($result->num_rows>0){
   
            $stmt=$this->dbconn->prepare("SELECT id from admin WHERE email=?");
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
            "isadmin"=>true,
            "adminid"=>$getid['id'],
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




      function isauthadmin($token){

         try {
          $decoded = JWT::decode($token, $this->jwtkey, array('HS256'));
          $decoded_array = (array) $decoded;
          if($decoded_array["isadmin"]==true){
            $response=true;
          }else{
            $response=false;
          }
          
         
         } catch (Exception $e) {
          $response=false;
         }
         
       return $response;
       }





      function addadmin($name,$password,$email,$token){
   
        if($this->isauthadmin($token)){
         if(!$this->checkadminexist($email)){
            $stmt=$this->dbconn->prepare("INSERT INTO admin (name,password,email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss",$name,$password,$email);
            $execute=$stmt->execute();
            $stmt->close();
            
        
            if($execute){
            $stmt=$this->dbconn->prepare("SELECT id from admin WHERE email=?");
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
             "isadmin"=>true,
             "adminid"=>$getid['id'],
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
 
        
          }else{
            $res['status']=false;
            $res['reason']="admin already exists";
          
          }
        }else{
         $res['status']=false;
         $res['reason']="you are not a admin";}
     return $res;
        }




        function jwttoadminid($jwt){
         try {
            $decoded = JWT::decode($jwt, $this->jwtkey, array('HS256'));
        $decoded_array = (array) $decoded;
        $response['isjwt']=true;
        $response['adminid']=$decoded_array['adminid'];
        return $response;
         } catch (Exception $e) {
            $response['isjwt']=false;
         }
      } 


        function addblogpost($token,$title,$description,$content){

            $jwttoadminid=$this->jwttoadminid($token);
         
            if($jwttoadminid["adminid"]!=null)
            {

            $stmt=$this->dbconn->prepare("INSERT INTO `blog` (`title`, `description`, `content`) VALUES (?,?,?)");
            $stmt->bind_param("sss",$title,$description,$content);
            $execute=$stmt->execute();
         
            if($execute){
               $response['blogadded']=true;
            }else{
               $response['blogadded']=false;
            }
         
         }
            else{
               $response['blogadded']=false;
               $response['reason']="you are not a admin";
            }
         
            return $response;
         

        }



function addquestion($token,$question,$option1,$option2,$option3,$option4,$answer,$topic,$subtopic){

if($this->isauthadmin($token)){
   $stmt=$this->dbconn->prepare("INSERT INTO `quiz` (`question`, `option1`, `option2`, `option3`, `option4`, `answer`, `topic`, `subtopic`) VALUES (?,?,?,?,?,?,?,?)");
      $stmt->bind_param("ssssssss",$question,$option1,$option2,$option3,$option4,$answer,$topic,$subtopic);
      $execute=$stmt->execute();
      $stmt->close();

      if($execute){
         $response['questionadded']=true;
      }else{
         $response['questionadded']=false;
      }
   }else{
      $response['questionadded']=false;
   }
      return $response;
}


}

?>
