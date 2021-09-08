<?php
require_once "dbfunction.php";
require_once "config.php";

$name=$_POST['name'];
$email=$_POST['email'];
$password=$_POST['password'];


if (!is_null($name) && !is_null($email) && !is_null($password)){

    $dbfunction=new dbfunction();
    $response['signupstatus']=$dbfunction->signup($name,password_hash($password, PASSWORD_DEFAULT, user_pass_salt),$email);
    echo json_encode($response);

}else{
    echo "i require name email and password";
}
?>