<?php
require_once "../dbfunction.php";
require_once "../config.php";


$email=$_POST['email'];
$password=$_POST['password'];


if (!is_null($email) && !is_null($password)){

    $dbfunction=new dbfunction();
    $response=$dbfunction->signin($email,password_hash($password, PASSWORD_DEFAULT, user_pass_salt));
    echo json_encode($response);

}else{
    echo "i require email and password";
}
?>