<?php

function maillist_show()
{
	global $sql_pref, $conn_id, $path;
	echo "<table class='main' cellspacing='2 cellpadding='2' width='100%' style='z-index: 1;'>
			<tr class='maintitle'>
				<td width='20' class='maintitle' align='center'><b>id</b></td>
				<td width='100' class='maintitle' align='center'><b>дата</b></td>
				<td width='120' class='maintitle' align='center'>&nbsp;</td>
				<td class='maintitle' align='center'><b>заголовок</b></td>
				<td width='30' class='maintitle' align='center'><b>del</b></td>
			</tr>";
	$sql_query="SELECT id, dt, name FROM ".$sql_pref."_mail_letters ORDER BY dt DESC";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $dt, $name)=mysql_fetch_row($sql_res))
		{
			$name=StripSlashes($name);
			$dt_show=date("d.m.Y H:i", strtotime($dt));
			$edit_pic="<a href='?id=".$id."&action=letter_edit#letter_edit'><img src='/admin/img/edit.gif' width='25' height='13' alt='Редактировать письмо' border='0'></a>";
			$subscribers_pic="<a href='?id=".$id."&action=subscribers_show#subscribers_show'><img src='/admin/img/subscribers.gif' width='25' height='13' alt='Список подписчиков' border='0'></a>";
            
            $sql_query="SELECT id FROM ".$sql_pref."_mail_subscribers WHERE letter_id='".$id."'";
            $sql_res_1=mysql_query($sql_query, $conn_id);
            if (mysql_num_rows($sql_res_1)>0) $del_pic="<img src='/admin/img/del_inactive.gif' width='25' height='13' alt='Удалить' border='0'>";
            else $del_pic="<a href=\"javascript:if(confirm('Вы уверены?')) window.location='?id=".$id."&action=letter_delete'\"><img src='/admin/img/del.gif' width='25' height='13' alt='Удалить' border='0'></a>";
            
            
			echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$dt_show."</td>
					<td class='common' align='center'>".$edit_pic.$subscribers_pic."</td>
					<td class='common' align='left'>".$name."</td>
					<td class='common' align='center'>".$del_pic."</td>
				</tr>";
		}
	}
	echo "</table>";
	echo "<div style='padding: 10 0 20 20;'><a href='?action=letter_add#letter_add'>Новое письмо</a>";
    echo "<br><a href='?action=mail_from_add#mail_from_add'>Добавить исходящий адрес</a>";
    echo "<br><a href='?action=news_mail_gen_test#news_mail_gen_test'>Сгенерировать новостное письмо ТЕСТОВЫЙ ВАРИАНТ</a>";
    echo "<br><a href='?action=news_mail_gen#news_mail_gen'>Сгенерировать новостное письмо</a></div>";
}








function form_letter_save()
{
	global $sql_pref, $conn_id;
	if (isset($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
	if (isset($_REQUEST['FCKeditor1'])) $content=addslashes($_REQUEST['FCKeditor1']); else $content="";
	if (isset($_REQUEST['mail_from_id']) && ($_REQUEST['mail_from_id']!=0)) $mail_from_id=addslashes($_REQUEST['mail_from_id']); else $mail_from_id="6";
	$dt=date("Y-m-d H:i:s");
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_mail_letters SET dt='".$dt."', name='".$name."', content='".$content."', email_id='".$mail_from_id."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
	else
	{
		$sql_query="INSERT INTO ".$sql_pref."_mail_letters (dt, name, content, email_id) VALUES ('".$dt."', '".$name."', '".$content."', '".$mail_from_id."')";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
}











function subscribers_add_users_save()
{
	global $sql_pref, $conn_id;
    
    $letter_id=$_REQUEST['id'];
    
    $sql_query="SELECT id, CONCAT_WS(' ', surname, name, name2), email FROM ".$sql_pref."_users WHERE enable='Yes'&&maillist='Yes'";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
        while(list($id, $name, $email)=mysql_fetch_row($sql_res))
        {
            $sql_query="SELECT id FROM ".$sql_pref."_mail_subscribers WHERE letter_id='".$letter_id."'&&email='".$email."'";
            $sql_res_1=mysql_query($sql_query, $conn_id);
            if (mysql_num_rows($sql_res_1)==0 && !empty($email))
            {
        		$sql_query="INSERT INTO ".$sql_pref."_mail_subscribers (dt_send, name, email, letter_id) VALUES ('0000-00-00 00:00:00', '".$name."', '".$email."', '".$letter_id."')";
        		$sql_res_2=mysql_query($sql_query, $conn_id);
            }
        }
    }
}

function exb_subscribers_add_users_save()
{
	global $sql_pref, $conn_id;
    
    $letter_id=$_REQUEST['id'];
    
    $sql_query="SELECT id, CONCAT_WS(' ', surname, name, name2), email FROM ".$sql_pref."_users WHERE enable='Yes'&&maillist_exb='Yes'";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
        while(list($id, $name, $email)=mysql_fetch_row($sql_res))
        {
            $sql_query="SELECT id FROM ".$sql_pref."_mail_subscribers WHERE letter_id='".$letter_id."'&&email='".$email."'";
            $sql_res_1=mysql_query($sql_query, $conn_id);
            if (mysql_num_rows($sql_res_1)==0 && !empty($email))
            {
        		$sql_query="INSERT INTO ".$sql_pref."_mail_subscribers (dt_send, name, email, letter_id) VALUES ('0000-00-00 00:00:00', '".$name."', '".$email."', '".$letter_id."')";
        		$sql_res_2=mysql_query($sql_query, $conn_id);
            }
        }
    }
}


function filter_sd_form_save()
{
	global $sql_pref, $conn_id;    
    $letter_id=$_REQUEST['id'];
    if (isset($_REQUEST['sfery']) && !empty($_REQUEST['sfery'])) $cur_sfery=implode(",",$_REQUEST['sfery']);
    if (isset($_REQUEST['directions']) && !empty($_REQUEST['directions'])) $cur_directions=implode(",",$_REQUEST['directions']);

    if (isset($cur_sfery) && !empty($cur_sfery))
    {
        $as_sa_sql=", ".$sql_pref."_sd_sfery_arts AS sa";
        $add_sql_list[]="sa.table_name='companies'&&sa.sfera_id IN('".$cur_sfery."')&&sa.art_id=c.id";
    }
    if (isset($cur_directions) && !empty($cur_directions)) 
    {
        $as_da_sql=", ".$sql_pref."_sd_directions_arts AS da";
        $add_sql_list[]="da.table_name='companies'&&da.direction_id IN('".$cur_directions."')&&da.art_id=c.id";        
    }
    
    $add_sql_list[]="c.enable='Yes'";
    $add_sql=implode("&&",$add_sql_list);

    
    $sql_query="SELECT c.id, CONCAT_WS(' ', c.name, c.name_full), c.email FROM ".$sql_pref."_companies AS c".$as_sa_sql.$as_da_sql." WHERE ".$add_sql." GROUP BY c.name ORDER BY c.name";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {            
        while(list($id, $name, $email)=mysql_fetch_row($sql_res))
        {   
            $sql_query="SELECT id FROM ".$sql_pref."_mail_subscribers WHERE letter_id='".$letter_id."'&&email='".$email."'";
            $sql_res_1=mysql_query($sql_query, $conn_id);
            //echo "_".$email."_ _".mysql_num_rows($sql_res_1)."_";
            
            if (mysql_num_rows($sql_res_1)==0 && !empty($email))
            {
        		$sql_query="INSERT INTO ".$sql_pref."_mail_subscribers (dt_send, name, email, letter_id) VALUES ('0000-00-00 00:00:00', '".$name."', '".$email."', '".$letter_id."')";
        		$sql_res_2=mysql_query($sql_query, $conn_id);
            }
        }
    }
}



function subscribers_add_form_save()
{
	global $sql_pref, $conn_id;
    
    $letter_id=$_REQUEST['id'];
    $name=addslashes($_REQUEST['name']);
    $email=$_REQUEST['email'];
    
    if (!empty($email))
    {
        $sql_query="SELECT id FROM ".$sql_pref."_mail_subscribers WHERE letter_id='".$letter_id."'&&email='".$email."'";
        $sql_res=mysql_query($sql_query, $conn_id);
        if (mysql_num_rows($sql_res)==0)
        {
    		$sql_query="INSERT INTO ".$sql_pref."_mail_subscribers (dt_send, name, email, letter_id) VALUES ('0000-00-00 00:00:00', '".$name."', '".$email."', '".$letter_id."')";
    		$sql_res_1=mysql_query($sql_query, $conn_id);
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
    
    $sql_query="SELECT id FROM ".$sql_pref."_mail_subscribers WHERE id='".$subscriber_id."'";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
		$sql_query="UPDATE ".$sql_pref."_mail_subscribers SET dt_send='0000-00-00 00:00:00' WHERE id='".$subscriber_id."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
    }
}


function del_subscribers()
{
    global $sql_pref, $conn_id;

    $letter_id=$_REQUEST['id'];
    $sql_query="DELETE FROM ".$sql_pref."_mail_subscribers WHERE letter_id='".$letter_id."'";
    $sql_res_1=mysql_query($sql_query, $conn_id);
    //echo $sql_query;
    
}





?>