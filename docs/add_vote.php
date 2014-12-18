<?php


require_once("fns/config.php");
require_once("fns/common.php");
starting();
require_once("fns/auth.php");
auth_maincheck();

global $user_id;
	
$question_id=$_REQUEST['question_id'];

$out="";

//echo "!!!".$question_id;
if($_REQUEST['vote']=='up') $sql_query="UPDATE ".$sql_pref."_questions SET rate=rate+1 WHERE id=".$question_id; else $sql_query="UPDATE ".$sql_pref."_questions SET rate=rate-1 WHERE id=".$question_id;
$sql_res=mysql_query($sql_query, $conn_id);
        
$sql_query="SELECT rate FROM ".$sql_pref."_questions WHERE id=".$question_id;
$sql_res=mysql_query($sql_query, $conn_id);
list($rate)=mysql_fetch_row($sql_res);
print $rate;

$sql_query="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'quest_vote','".$question_id."')";
$sql_res=mysql_query($sql_query, $conn_id);


sql_close();

?>