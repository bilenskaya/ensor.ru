<?php



function top_management_show()
{

	global $sql_pref, $conn_id, $path, $top_management_types;
	
	if (isset($_REQUEST['letter']) && !empty($_REQUEST['letter'])) $curletter=$_REQUEST['letter'];
	$sql_query="SELECT SUBSTRING(surname,1,1) FROM ".$sql_pref."_top_management GROUP BY SUBSTRING(surname,1,1)";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		echo "<div align='left' style='padding: 0 0 0 0;'><table cellpadding=3 cellspacing=0 border='0'><tr height=25>";
		while(list($letter)=mysql_fetch_row($sql_res))
		{
			$letter=strtoupper($letter);
			if (isset($curletter) && rawurldecode($curletter)==$letter) $letter_show="<span style='border:solid 1px gray; padding:2 2 2 2;background-color:#eeeeee;text-transform:uppercase;'><b>".$letter."</b></span>"; else $letter_show="<span style='padding:2 2 2 2;text-transform:uppercase;'>".$letter."</span>";
			echo "<td align='center' valign='middle'><nobr><a href='?letter=".rawurlencode($letter)."'> ".$letter_show." </a></nobr></td>";
		}
		echo "</tr></table></div><br>";
	}

	$fl_en=0;$fl_ma=0;
	
	if (isset($curletter) && !empty($curletter)) $usl="SUBSTRING(surname,1,1)='".rawurldecode($curletter)."'"; else return;
	$sql_query="SELECT id, surname, name, name2, email, enable FROM ".$sql_pref."_top_management WHERE ".$usl." ORDER BY surname";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
			<tr class='maintitle'>
				<td width='20' class='maintitle' align='center'><b>id</b></td>
				<td width='50' class='maintitle' align='center'>&nbsp;</td>
				<td class='maintitle' align='center'><b>имя</b></td>
				<td width='180' class='maintitle' align='center'><b>email</b></td>
				<td width='30' class='maintitle' align='center'><b>del</b></td>
			</tr>";
		while (list($id, $surname, $name, $name2, $email, $enable)=mysql_fetch_row($sql_res))
		{
			$surname=stripslashes($surname);$name=stripslashes($name);$name2=stripslashes($name2);
			$name_show=$surname." ".$name." ".@$name2;
			if ($enable=="No") { $name_show="<span style='color:gray;'>".$name_show."</span>"; }
            if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&letter=".@$curletter."&action=top_management_status'><img src='/admin/img/check_yes.gif' width='25' height='13' alt='Смена статуса пользователя' border='0'></a>"; 
			else $enable_pic="<a href='?id=".$id."&letter=".@$curletter."&action=top_management_status'><img src='/admin/img/check_no.gif' width='25' height='13' alt='Смена статуса пользователя' border='0'></a>";
			$edit_pic="<a href='?id=".$id."&letter=".@$curletter."&action=top_management_edit#top_management_edit'><img src='/admin/img/edit.gif' width='25' height='13' alt='Редактировать' border='0'></a>";

			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&letter=".@$curletter."&action=top_management_delete'\"><img src='/admin/img/del.gif' width='25' height='13' alt='Удалить' border='0'></a>";
			echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$enable_pic.$edit_pic."</td>
					<td class='common' align='left'>".$name_show." </td>
					<td class='common' align='center'>".$email."</td>
					<td class='common' align='center'>".$del."</td>
				</tr>";
			if ($enable=="No") $fl_en=1;
		}
		echo "</table><br><br>";
	}
}







function form_top_management_save()
{
	global $sql_pref, $conn_id, $path;
    
    $users_img_width=300;$users_img_height=300;
    $users_avatar_width=80;$users_avatar_height=80;
    
	if (isset($_REQUEST['surname']) AND !empty($_REQUEST['surname'])) $surname=addslashes($_REQUEST['surname']); else $surname="";
	if (isset($_REQUEST['name']) AND !empty($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
	if (isset($_REQUEST['name2']) AND !empty($_REQUEST['name2'])) $name2=addslashes($_REQUEST['name2']); else $name2="";
	if (isset($_REQUEST['email']) AND !empty($_REQUEST['email'])) $email=$_REQUEST['email']; else $email="";
	if (isset($_REQUEST['pol']) AND !empty($_REQUEST['pol'])) $pol=$_REQUEST['pol']; else $pol="m";
	if (isset($_REQUEST['dt_birth']) AND !empty($_REQUEST['dt_birth'])) $dt_birth=$_REQUEST['dt_birth']; else $dt_birth="0000-00-00";
	if (isset($_REQUEST['phone_work']) AND !empty($_REQUEST['phone_work'])) $phone_work=addslashes($_REQUEST['phone_work']); else $phone_work="";
	if (isset($_REQUEST['phone_mobile']) AND !empty($_REQUEST['phone_mobile'])) $phone_mobile=addslashes($_REQUEST['phone_mobile']); else $phone_mobile="";
	if (isset($_REQUEST['company_id']) AND !empty($_REQUEST['company_id'])) $company_id=$_REQUEST['company_id']; else $company_id="0";
	if (isset($_REQUEST['doljnost']) AND !empty($_REQUEST['doljnost'])) $doljnost=addslashes($_REQUEST['doljnost']); else $doljnost="";
	if (isset($_REQUEST['expirience']) AND !empty($_REQUEST['expirience'])) $expirience=addslashes($_REQUEST['expirience']); else $expirience="";
	if (isset($_REQUEST['vuz']) AND !empty($_REQUEST['vuz'])) $vuz=addslashes($_REQUEST['vuz']); else $vuz="";
	if (isset($_REQUEST['specialnost']) AND !empty($_REQUEST['specialnost'])) $specialnost=addslashes($_REQUEST['specialnost']); else $specialnost="";
	if (isset($_REQUEST['enable']) && $_REQUEST['enable']=="Yes") $enable="Yes"; else $enable="No";
	
    if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
    	$sql_query="UPDATE ".$sql_pref."_top_management SET surname='".$surname."', name='".$name."', name2='".$name2."', email='".$email."', pol='".$pol."', dt_birth='".$dt_birth."', phone_work='".$phone_work."', phone_mobile='".$phone_mobile."', company_id='".$company_id."', doljnost='".$doljnost."', expirience='".$expirience."', vuz='".$vuz."', specialnost='".$specialnost."', enable='".$enable."' WHERE id='".$_REQUEST['id']."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
        $id=$_REQUEST['id'];
    }
	else
	{
 
        $sql_query="INSERT INTO ".$sql_pref."_top_management (surname,name,name2) VALUES ('".$surname."', '".$name."', '".$name2."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$id=mysql_insert_id();
	}
    
    //echo $_FILES['file_name']['tmp_name'];
    if (is_uploaded_file($_FILES["file_name"]["tmp_name"]))
	{
	    //echo "!!!";
		$mime=$_FILES['file_name']['type'];
		if ($mime=="image/jpeg" || $mime=="image/pjpeg")
		{
            $src=$_FILES["file_name"]["tmp_name"];
            $dest=$path."files/top_management/img/".$id.".jpg";
            $resize=true;
            common_del_file($dest);
            $out.=common_save_img($src, $dest, $resize, $users_img_width, $users_img_height, 80, $mime);
            
			$src=$dest;
			$dest=$path."files/top_management/avatar/".$id.".jpg";
			$resize=true;
			common_del_file($dest);
			common_save_img($src, $dest, $resize, $users_avatar_width, $users_avatar_height, 80, $mime);
		}
		else echo "<div>Ошибка загрузки изображения</div>";
	}
    else
    {
        //echo "!@#$#".$_FILES["file_name"]["tmp_name"];
    }
    //return $out;
    //echo "Ошибка загрузки изображения";
}









?>