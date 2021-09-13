<?php
require_once "dbfunction.php";
require_once "config.php";


$limit=$_POST['limit'];


if (!is_null($limit)){

    $dbfunction=new dbfunction();
    $response=$dbfunction->getquestionbyrandom($limit);
    echo json_encode($response);

}else{
    echo "i require limit";
}
?>