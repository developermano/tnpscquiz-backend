<?php
require_once "../dbfunction.php";
require_once "../config.php";

$name=$_POST['name'];
$email=$_POST['email'];
$password=$_POST['password'];
$token=$_POST['token'];


if (!is_null($email) && !is_null($password) && !is_null($name) && !is_null($token)){

    $dbfunction=new dbfunction();
    $response=$dbfunction->addadmin($name,password_hash($password, PASSWORD_DEFAULT, user_pass_salt),$email,$token);
    echo json_encode($response);

}else{
    echo "i require name , token email and password";
}
?>