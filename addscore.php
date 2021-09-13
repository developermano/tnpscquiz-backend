<?php
require_once "dbfunction.php";
require_once "config.php";


$token=$_POST['token'];
$score=$_POST['score'];

if (!is_null($token) && !is_null($score)){


    $dbfunction=new dbfunction();
    $response=$dbfunction->addscore($token,$score);
    echo json_encode($response);



}else{
    echo "i require token and score";
}
?>