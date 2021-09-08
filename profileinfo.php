<?php
require_once "dbfunction.php";
require_once "config.php";


$token=$_POST['token'];


if (!is_null($token)){

    $dbfunction=new dbfunction();
    $response=$dbfunction->profileinfo($token);
    echo json_encode($response);

}else{
    echo "i require token";
}
?>