<?php
require_once "../dbfunction.php";
require_once "../config.php";

$token=$_POST['token'];
$question=$_POST["question"];
$option1=$_POST["option1"];
$option2=$_POST["option2"];
$option3=$_POST["option3"];
$option4=$_POST["option4"];
$answer=$_POST["answer"];
$topic=$_POST["topic"];
$subtopic=$_POST["subtopic"];


if (!is_null($token) && !is_null($question) && !is_null($option1) && !is_null($option2) && !is_null($option3) && !is_null($option4) && !is_null($answer) && !is_null($topic) && !is_null($subtopic)){

    $dbfunction=new dbfunction();
    $response=$dbfunction->addquestion($token,$question,$option1,$option2,$option3,$option4,$answer,$topic,$subtopic);
    echo json_encode($response);

}else{
    echo "i require token,question,answer,topic,subtopic and 5 options";
}
?>