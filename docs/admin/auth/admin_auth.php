<?php



function auth_show()
{

	global $sql_pref, $conn_id, $path, $auth_types;
	
	if (isset($_REQUEST['letter']) && !empty($_REQUEST['letter'])) $curletter=$_REQUEST['letter'];
	$sql_query="SELECT SUBSTRING(surname,1,1) FROM ".$sql_pref."_users GROUP BY SUBSTRING(surname,1,1)";
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
	$sql_query="SELECT id, surname, name, name2, email, dt_reg, enable, status FROM ".$sql_pref."_users WHERE ".$usl." OR SUBSTRING(surname,1,1)=' ' ORDER BY surname";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
			<tr class='maintitle'>
				<td width='20' class='maintitle' align='center'><b>id</b></td>
				<td width='50' class='maintitle' align='center'>&nbsp;</td>
				<td class='maintitle' align='center'><b>имя</b></td>
				<td width='180' class='maintitle' align='center'><b>email</b></td>
				<td width='150' class='maintitle' align='center'><b>регистрация</b></td>
				<td width='30' class='maintitle' align='center'><b>del</b></td>
			</tr>";
		while (list($id, $surname, $name, $name2, $email, $dt_reg, $enable, $status)=mysql_fetch_row($sql_res))
		{
			$surname=stripslashes($surname);$name=stripslashes($name);$name2=stripslashes($name2);
			$name_show=$surname." ".$name." ".@$name2;
			if ($enable=="No") { $name_show="<span style='color:gray;'>".$name_show."</span>"; }
            if ($status=="admin") { $name_show="<span style='font-weight:bold;'>".$name_show."</span>"; }
			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&letter=".@$curletter."&action=auth_status'><img src='/admin/img/check_yes.gif' width='25' height='13' alt='Смена статуса пользователя' border='0'></a>"; 
			else $enable_pic="<a href='?id=".$id."&letter=".@$curletter."&action=auth_status'><img src='/admin/img/check_no.gif' width='25' height='13' alt='Смена статуса пользователя' border='0'></a>";
			$edit_pic="<a href='?id=".$id."&letter=".@$curletter."&action=auth_edit#auth_edit'><img src='/admin/img/edit.gif' width='25' height='13' alt='Редактировать' border='0'></a>";

			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&letter=".@$curletter."&action=auth_delete'\"><img src='/admin/img/del.gif' width='25' height='13' alt='Удалить' border='0'></a>";
			echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$enable_pic.$edit_pic."</td>
					<td class='common' align='left'>".$name_show." </td>
					<td class='common' align='center'>".$email."</td>
					<td class='common' align='center'>".$dt_reg."</td>
					<td class='common' align='center'>".$del."</td>
				</tr>";
			if ($enable=="No") $fl_en=1;
		}
		echo "</table><br><br>";
	}
}













function form_auth_save()
{
	global $sql_pref, $conn_id;
    
	if (isset($_REQUEST['pass']) AND !empty($_REQUEST['pass'])) $pass=addslashes($_REQUEST['pass']); else $pass="";
	if (isset($_REQUEST['surname']) AND !empty($_REQUEST['surname'])) $surname=addslashes($_REQUEST['surname']); else $surname="";
	if (isset($_REQUEST['name']) AND !empty($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
	if (isset($_REQUEST['name2']) AND !empty($_REQUEST['name2'])) $name2=addslashes($_REQUEST['name2']); else $name2="";
	if (isset($_REQUEST['email']) AND !empty($_REQUEST['email'])) $email=$_REQUEST['email']; else $email="";
	if (isset($_REQUEST['pol']) AND !empty($_REQUEST['pol'])) $pol=$_REQUEST['pol']; else $pol="m";
	if (isset($_REQUEST['dt_birth']) AND !empty($_REQUEST['dt_birth'])) $dt_birth=$_REQUEST['dt_birth']; else $dt_birth="0000-00-00";
	if (isset($_REQUEST['phone_work']) AND !empty($_REQUEST['phone_work'])) $phone_work=addslashes($_REQUEST['phone_work']); else $phone_work="";
	if (isset($_REQUEST['phone_home']) AND !empty($_REQUEST['phone_home'])) $phone_home=addslashes($_REQUEST['phone_home']); else $phone_home="";
	if (isset($_REQUEST['phone_mobile']) AND !empty($_REQUEST['phone_mobile'])) $phone_mobile=addslashes($_REQUEST['phone_mobile']); else $phone_mobile="";
	if (isset($_REQUEST['company_id']) AND !empty($_REQUEST['company_id'])) $company_id=$_REQUEST['company_id']; else $company_id="0";
	if (isset($_REQUEST['doljnost']) AND !empty($_REQUEST['doljnost'])) $doljnost=addslashes($_REQUEST['doljnost']); else $doljnost="";
	if (isset($_REQUEST['expirience']) AND !empty($_REQUEST['expirience'])) $expirience=addslashes($_REQUEST['expirience']); else $expirience="";
	if (isset($_REQUEST['vuz']) AND !empty($_REQUEST['vuz'])) $vuz=addslashes($_REQUEST['vuz']); else $vuz="";
	if (isset($_REQUEST['specialnost']) AND !empty($_REQUEST['specialnost'])) $specialnost=addslashes($_REQUEST['specialnost']); else $specialnost="";
	if (isset($_REQUEST['enable']) && $_REQUEST['enable']=="Yes") $enable="Yes"; else $enable="No";
	if (isset($_REQUEST['maillist']) && $_REQUEST['maillist']=="Yes") $maillist="Yes"; else $maillist="No";
	if (isset($_REQUEST['status']) AND !empty($_REQUEST['status'])) $status=$_REQUEST['status']; else $status="user";
	if (isset($_REQUEST['forum_admin']) && $_REQUEST['forum_admin']=="Yes") $forum_admin="Yes"; else $forum_admin="No";

	$sql_query="UPDATE ".$sql_pref."_users SET pass='".$pass."', surname='".$surname."', name='".$name."', name2='".$name2."', email='".$email."', pol='".$pol."', dt_birth='".$dt_birth."', phone_work='".$phone_work."', phone_home='".$phone_home."', phone_mobile='".$phone_mobile."', company_id='".$company_id."', doljnost='".$doljnost."', expirience='".$expirience."', vuz='".$vuz."', specialnost='".$specialnost."', enable='".$enable."', status='".$status."', forum_admin='".$forum_admin."', maillist='".$maillist."' WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
}









?>