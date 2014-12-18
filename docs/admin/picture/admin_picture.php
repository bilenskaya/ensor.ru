<?php

function check_empty($id)
{
	global $sql_pref, $conn_id;
	$sql_query1="SELECT id FROM ".$sql_pref."_picture WHERE parent_id='$id'";
	$sql_res1=mysql_query($sql_query1, $conn_id);
	if (mysql_num_rows($sql_res1)>0) return (FALSE);
	else return (TRUE);
	}



function admin_rubric_list()
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT id, enable, name FROM ".$sql_pref."_picture_rub ORDER BY name";
	$sql_res=mysql_query($sql_query, $conn_id);

echo "<a href='?action=moderate#moderate'>Модерация картинок</a>";
$add_pic="<a href='?action=rubric_add#rubric_add'><img src='/admin/img/line.gif' width=25 height=13 alt='Добавить рубрику' border=0></a>";
echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
    			<tr class='maintitle'>
    				<td width='20' class='maintitle' align='center'><b>id</b></td>
    				<td width='80' class='maintitle' align='center'>".$add_pic."</td>
    				<td class='maintitle' align='left'><b>название</b></td>
    				<td width='30' class='maintitle' align='center'><b>del</b></td>
    			</tr>";


	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $enable, $name)=mysql_fetch_row($sql_res))
		{
			$name_show="<a href='?id=".$id."&action=rubric_show#rubric_show'>".$name."</a>";
			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=rubric_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=rubric_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение' border=0></a>";
			
			$edit_pic="<a href='?id=".$id."&action=rubric_edit#rubric_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			if (check_empty($id)) $del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&action=rubric_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			else $del="";
			
					echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$enable_pic.$edit_pic."</td>
					<td class=cat_rubric_$sub_level align='left'>".$name_show."</td>
					<td class='common' align='center'>".$del."</td>
					</tr>";
		}
	}

echo "</table>";
}






function admin_show_moderate()
{
	global $sql_pref, $conn_id, $path;

	$sql_query="SELECT id, parent_id, descr, enable, date_upload, format, file_size, file_name, tags, user_id FROM ".$sql_pref."_picture WHERE moderation='Yes' ORDER by date_upload desc";
	$sql_res=mysql_query($sql_query, $conn_id);
	echo "<b>МОДЕРАЦИЯ КАРТИНОК</b>";
	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
    			<tr class='maintitle'>
    				<td width='20' class='maintitle' align='center'><b>id</b></td>
					<td class='maintitle' align='left'></td>
    				<td class='maintitle' align='left'><b>картинка</b></td>
					<td class='maintitle' align='left'><b>рубрика</b></td>
    				<td class='maintitle' align='left'><b>описание</b></td>
    				<td class='maintitle' align='left'><b>имя файла</b></td>
    				<td class='maintitle' align='left'><b>исходный размер</b></td>
    				<td class='maintitle' align='left'><b>формат</b></td>
    				<td class='maintitle' align='left'><b>пользователь</b></td>
    				<td class='maintitle' align='left'><b>добавлен</b></td>
    				<td class='maintitle' align='left'><b>тэги</b></td>
    				<td width='30' class='maintitle' align='center'><b>del</b></td>
    			</tr>";
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $parent_id, $descr, $enable, $date_upload, $format, $file_size, $file_name, $tags, $user_id)=mysql_fetch_row($sql_res))
		{
			
			$descr=stripslashes($descr);
			$date_upload=date("d.m.y", $date_upload);

			$edit_pic="<a href='?id=".$id."&action=moderate_edit#moderate_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			if ($parent_id[0]!=="0")$moderate_pic="<a href=\"javascript:if(confirm('Вы уверены? Принять изображение?'))window.location='?id=".$id."&action=moderate_add'\"><img src='/admin/img/active.gif' width=25 height=13 alt='Принять изображение' border=0></a>"; else $moderate_pic="<a href=\"javascript:if(confirm('Укажите рубрику'))window.location='?&action=moderate'\"><img src='/admin/img/active.gif' width=25 height=13 alt='Принять изображение' border=0></a>";
			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&rub_id=moderate&action=picture_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
	if ($file_name!=="" and file_exists($path."files/picture/tn_".$file_name)) 
	$img="<a href='/files/picture/".$file_name."'><img src='/files/picture/tn_".$file_name."' border='0'></a>";
	else $img="<img src='/files/picture/not_found_pic.png'>";
		
		$sql_user_query="SELECT surname, name FROM ".$sql_pref."_users WHERE id='$user_id'";
	$sql_user_res=mysql_query($sql_user_query, $conn_id);
	if (mysql_num_rows($sql_user_res)>0) {list($surname, $user_name)=mysql_fetch_row($sql_user_res); $full_user=$surname." ".$user_name;}
		else $full_user="Не установлен";
	
	$sql_rub_query="SELECT name FROM ".$sql_pref."_picture_rub WHERE id='$parent_id'";
	$sql_rub_res=mysql_query($sql_rub_query, $conn_id);
	if (mysql_num_rows($sql_rub_res)>0) list($rub_name)=mysql_fetch_row($sql_rub_res); else $rub_name="НЕ УКАЗАНО";
	
					echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$edit_pic.$moderate_pic."</td>
					<td class='common' align='left'>".$img."</td>
					<td class='common' align='left'>".$rub_name."</td>
					<td class='common' align='left'>".$descr."</td>";
					if (strlen($file_name)>0 and file_exists($path."files/picture/".$file_name))
						echo "<td class='common' align='left'>".$file_name."</td>";
						else echo "<td class='common' bgcolor='red' align='left'>".$file_name."</td>";
					echo "<td class='common' align='left'>".$file_size."</td>
					<td class='common' align='left'>".$format."</td>
					<td class='common' align='left'>".$full_user." (".$user_id.")"."</td>
					<td class='common' align='left'>".$date_upload."</td>
					<td class='common' align='left'>".$tags."</td>
					<td class='common' align='center'>".$del."</td>
					</tr>";
		}
	}
	echo "</table>";
}


function admin_save_moderate($id)
{
	global $sql_pref, $conn_id, $path;

$sql_query="SELECT id, user_id FROM ".$sql_pref."_picture WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
list($id, $user_id)=mysql_fetch_row($sql_res);
$date_upload=time();

$sql_query="UPDATE ".$sql_pref."_picture SET moderation='No', date_upload='".$date_upload."' WHERE id='".$id."'";
$sql_res=mysql_query($sql_query, $conn_id);

$sql_query2="SELECT rate_main, rate_sec FROM ".$sql_pref."_users WHERE id='".$user_id."'";
$sql_res2=mysql_query($sql_query2, $conn_id);
list($user_rate_main, $user_rate_sec)=mysql_fetch_row($sql_res2);
rate_main($user_id, "добавил картинку", $user_rate_main, $user_rate_sec);

$sql_query3="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'add_picture','".$id."')";
$sql_res3=mysql_query($sql_query3, $conn_id);
}









function rate_main($rate_user_id,$rate_act_type,$rate_main_val,$rate_sec_val)
{
    global $sql_pref, $conn_id, $user_status;

    switch ($rate_act_type) {
        case "посетил сайт":
            if($user_status=="admin")
            {                
                $sql_query="UPDATE ".$sql_pref."_users SET last_visit='".date("Y-m-d")."' WHERE id='".$rate_user_id."'";
                $sql_res=mysql_query($sql_query, $conn_id);
            }
            else
            {
                $rate_sec=$rate_sec_val+4;
                $sql_query="UPDATE ".$sql_pref."_users SET last_visit='".date("Y-m-d")."', rate_sec=".$rate_sec." WHERE id='".$rate_user_id."'";
                $sql_res=mysql_query($sql_query, $conn_id);
            }
            break;
        case "добавил комментарий":
            $rate_sec=$rate_sec_val+4;
            $sql_query="UPDATE ".$sql_pref."_users SET rate_sec=".$rate_sec." WHERE id='".$rate_user_id."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
            break;
        case "добавил картинку":
            $rate_sec=$rate_sec_val+4;
            $rate_main=$rate_main_val+4;
            $sql_query="UPDATE ".$sql_pref."_users SET rate_sec=".$rate_sec.", rate_main=".$rate_main." WHERE id='".$rate_user_id."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
            break;
        case "пригласил коллегу":
            $rate_sec=$rate_sec_val+12;
            $rate_main=$rate_main_val+12;
            $sql_query="UPDATE ".$sql_pref."_users SET rate_sec=".$rate_sec.", rate_main=".$rate_main." WHERE id='".$rate_user_id."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
            break;
        case "оценил вопрос":
            $rate_sec=$rate_sec_val+1;
            $rate_main=$rate_main_val+1;
            $sql_query="UPDATE ".$sql_pref."_users SET rate_sec=".$rate_sec.", rate_main=".$rate_main." WHERE id='".$rate_user_id."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
            break;
        default :
}
}











function admin_show_rubric($rub_id)
{
	global $sql_pref, $conn_id, $path;

	$sql_query="SELECT id, name FROM ".$sql_pref."_picture_rub WHERE id='$rub_id'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $name)=mysql_fetch_row($sql_res);
		
					echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
					<tr class='common'>
					<td align='center' colspan=2><H1>".$name."</H1></td>
					</tr>
					</table>";
	$sql_query="SELECT id, parent_id, descr, enable, date_upload, format, file_size, file_name, tags, user_id FROM ".$sql_pref."_picture WHERE (parent_id = '$rub_id' AND moderation = 'No') ORDER by date_upload desc";
	$sql_res=mysql_query($sql_query, $conn_id);
	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
    			<tr class='maintitle'>
    				<td width='20' class='maintitle' align='center'><b>id</b></td>
					<td class='maintitle' align='left'></td>
    				<td class='maintitle' align='left'><b>картинка</b></td>
    				<td class='maintitle' align='left'><b>описание</b></td>
    				<td class='maintitle' align='left'><b>имя файла</b></td>
    				<td class='maintitle' align='left'><b>исходный размер</b></td>
    				<td class='maintitle' align='left'><b>формат</b></td>
    				<td class='maintitle' align='left'><b>пользователь</b></td>
    				<td class='maintitle' align='left'><b>добавлен</b></td>
    				<td class='maintitle' align='left'><b>тэги</b></td>
    				<td width='30' class='maintitle' align='center'><b>del</b></td>
    			</tr>";
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $parent_id, $descr, $enable, $date_upload, $format, $file_size, $file_name, $tags, $user_id)=mysql_fetch_row($sql_res))
		{
			

			$descr=stripslashes($descr);
			$date_upload=date("d.m.y", $date_upload);
			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=picture_enable&rub_id=".$rub_id."'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=picture_enable&rub_id=".$rub_id."'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение' border=0></a>";
			
			$edit_pic="<a href='?id=".$id."&rub_id=".$rub_id."&action=picture_edit#picture_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&rub_id=".$rub_id."&action=picture_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
		
	if ($file_name!=="" and file_exists($path."files/picture/tn_".$file_name)) 
	$img="<a href='/files/picture/".$file_name."'><img src='/files/picture/tn_".$file_name."' border='0'></a>";
	else $img="<img src='/files/picture/not_found_pic.png'>";
		
		$sql_user_query="SELECT surname, name FROM ".$sql_pref."_users WHERE id='$user_id'";
	$sql_user_res=mysql_query($sql_user_query, $conn_id);
	if (mysql_num_rows($sql_user_res)>0) {list($surname, $user_name)=mysql_fetch_row($sql_user_res); $full_user=$surname." ".$user_name;}
		else $full_user="Не установлен";
			
					echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$enable_pic.$edit_pic."</td>
					<td class='common' align='left'>".$img."</td>
					<td class='common' align='left'>".$descr."</td>";
					if (strlen($file_name)>0 and file_exists($path."files/picture/".$file_name))
						echo "<td class='common' align='left'>".$file_name."</td>";
						else echo "<td class='common' bgcolor='red' align='left'>".$file_name."</td>";
					echo "<td class='common' align='left'>".$file_size."</td>
					<td class='common' align='left'>".$format."</td>
					<td class='common' align='left'>".$full_user." (".$user_id.")"."</td>
					<td class='common' align='left'>".$date_upload."</td>
					<td class='common' align='left'>".$tags."</td>
					<td class='common' align='center'>".$del."</td>
					</tr>";
		}
	}
	echo "</table>";
	}
}




function form_rubric_save()
{
	global $sql_pref, $conn_id;
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (isset($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";


	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_picture_rub SET name='".$name."', enable='".$enable."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
	else
	{
		$sql_query="INSERT INTO ".$sql_pref."_picture_rub (name, enable) VALUES ('".$name."', '".$enable."')";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
}



function form_picture_save()
{
	global $sql_pref, $conn_id;
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) $id=$_REQUEST['id'];
	if (isset($_REQUEST['parent_id'])) $parent_id=$_REQUEST['parent_id'];
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']);
	if (isset($_REQUEST['user_id'])) $user_id=$_REQUEST['user_id'];
	if (isset($_REQUEST['tags'])) $tags=$_REQUEST['tags'];
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_picture SET parent_id='".$parent_id."', descr='".$descr."', user_id='".$user_id."', tags='".$tags."', enable='".$enable."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=$_REQUEST['id'];
	}

}


function del_picture($id)
{
	global $path, $conn_id, $sql_pref;
	$sql_query="SELECT file_name FROM ".$sql_pref."_picture WHERE id='$id'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$name_to_del=mysql_fetch_row($sql_res);
	$dest_to_del=$path."files/picture/".$name_to_del[0];
	$dest_to_del_tn=$path."files/picture/tn_".$name_to_del[0];
	if (strlen($name_to_del[0])>0) {del_file($dest_to_del); del_file($dest_to_del_tn);}
	del_record('picture', $id, 'No', -1);
}
?>
