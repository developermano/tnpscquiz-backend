<?php
require_once "../dbfunction.php";
require_once "../config.php";

$title=$_POST['title'];
$description=$_POST['description'];
$content=$_POST['content'];
$token=$_POST['token'];


if (!is_null($title) && !is_null($description) && !is_null($content) && !is_null($token)){

    $dbfunction=new dbfunction();
    $response=$dbfunction->addblogpost($token,$title,$description,$content);
    echo json_encode($response);

}else{
    echo "i require token,title,description and content";
}
?>