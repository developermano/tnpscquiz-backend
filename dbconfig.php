 <?php

require_once "config.php";

class dbconnection{

function getdb(){
$dbconn=new mysqli(db_host,db_user,db_password,db_name);
return $dbconn;
}


}
?>
