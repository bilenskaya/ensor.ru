<?php









function subscribers_add_users_save()
{
	global $sql_pref, $conn_id;
    
    $letter_id=$_REQUEST['id'];
    
    $sql_query="SELECT id, CONCAT_WS(' ', surname, name, name2), email, actkod FROM ".$sql_pref."_users WHERE enable='No'&&maillist='Yes'&&actkod<>''";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
        while(list($id, $name, $email, $actkod)=mysql_fetch_row($sql_res))
        {
            $sql_query="SELECT id FROM ".$sql_pref."_mail2_subscribers WHERE email='".$email."'";
            $sql_res_1=mysql_query($sql_query, $conn_id);
            if (mysql_num_rows($sql_res_1)==0 && !empty($email))
            {
        		$sql_query="INSERT INTO ".$sql_pref."_mail2_subscribers (dt_send, name, email, actkod) VALUES ('0000-00-00 00:00:00', '".$name."', '".$email."', '".$actkod."')";
        		$sql_res_2=mysql_query($sql_query, $conn_id);
            }
        }
    }
}



function mails_add_form_save()
{
	global $sql_pref, $conn_id;
    
    $email=$_REQUEST['email'];
    
    if (!empty($email))
    {
        $sql_query="SELECT id FROM ".$sql_pref."_mail_address WHERE email='".$email."'";
        $sql_res=mysql_query($sql_query, $conn_id);
        if (mysql_num_rows($sql_res)==0)
        {
    		$sql_query="INSERT INTO ".$sql_pref."_mail_address (email) VALUES ('".$email."')";
    		$sql_res_1=mysql_query($sql_query, $conn_id);
        }
    }
    
}



function subscribers_unsend()
{
	global $sql_pref, $conn_id;
    
    $letter_id=$_REQUEST['id'];
    $subscriber_id=$_REQUEST['subscriber_id'];
    
    $sql_query="SELECT id FROM ".$sql_pref."_mail2_subscribers WHERE id='".$subscriber_id."'";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
		$sql_query="UPDATE ".$sql_pref."_mail2_subscribers SET dt_send='0000-00-00 00:00:00' WHERE id='".$subscriber_id."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
    }
}


function del_subscribers()
{
    global $sql_pref, $conn_id;

    $letter_id=$_REQUEST['id'];
    $sql_query="DELETE FROM ".$sql_pref."_mail2_subscribers";
    $sql_res_1=mysql_query($sql_query, $conn_id);
    //echo $sql_query;
    
}





?>