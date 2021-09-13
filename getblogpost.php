<?php
require_once "dbfunction.php";
require_once "config.php";


$dbfunction=new dbfunction();
$response=$dbfunction->getlastfiveblogpost();
echo json_encode($response);


?>