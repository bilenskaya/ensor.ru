<?php

function calendar_show()
{
	global $sql_pref, $conn_id, $path;
	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
			<tr class='maintitle'>
				<td width='20' class='maintitle' align='center'><b>id</b></td>
				<td width='80' class='maintitle' align='center'><b>дата</b></td>
				<td width='80' class='maintitle' align='center'>&nbsp;</td>
				<td class='maintitle' align='left'><b>заголовок</b></td>
				<td width='30' class='maintitle' align='center'><b>del</b></td>
			</tr>";

	$perpage=10;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";

	$sql_query="SELECT id, enable, datum,  title FROM ".$sql_pref."_events ORDER BY datum DESC".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $enable, $dt, $name)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			$dt_show=$dt;
			if ($dt_show=="0000-00-00") $dt_show=" - ";
			if (empty($name)) $name="<i><без заголовка></i>";
//			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=calendar_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение раздела' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=calendar_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение раздела' border=0></a>";
			if ($enable=='Yes') $main_pic="<a href='?id=".$id."&action=calendar_main_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение на главной' border=0></a>"; else $main_pic="<a href='?id=".$id."&action=calendar_main_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение на главной' border=0></a>";
			$edit_pic="<a href='?id=".$id."&action=calendar_edit#calendar_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&action=calendar_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$dt_show."</td>
					<td class='common' align='center'>".$main_pic.$imgs.$edit_pic."</td>
					<td class='common' align='left'>".$sort_down.$sort_up.$name."</td>
					<td class='common' align='center'>".$del."</td>
				</tr>";
		}
	}
	echo "</table>";
	echo "<br><li><a href='?action=calendar_add#calendar_add'>Добавить событие</a></li>";
	$sql_query="SELECT id FROM ".$sql_pref."_events";
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		echo "<br><br><div align=left>Страницы: ";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show=$i;
			echo "<span style='padding:2 3 2 3;background-color:#eeeeee;border:solid 1px #aaaaaa;'><a href='?page=".$i."' style='text-decoration:none;'>".$i_show."</a></span> ";
		}
		echo "</div><br>";
	}
	echo "<hr>";
}



function form_calendar_save()
{
    //echo $_REQUEST['visibility']."dsfgvsdegre";
	global $sql_pref, $conn_id;
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (isset($_REQUEST['name'])) $name=$_REQUEST['name']; else $name="";
	if (isset($_REQUEST['actuals'])) $actuals=$_REQUEST['actuals']; else $actuals=0;
    if (isset($_REQUEST['visibility'])) $visibility=$_REQUEST['visibility']; else $visibility="user";	
    $name=htmlspecialchars($name, ENT_QUOTES); $name=addslashes($name);
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
	if (isset($_REQUEST['FCKeditor1'])) $content=addslashes($_REQUEST['FCKeditor1']); else $content="";
	if (isset($_REQUEST['xc2_dt']) && $_REQUEST['date']=="Yes") $dt=$_REQUEST['xc2_dt']; else $dt="0000-00-00";
    
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_events SET enable='".$enable."', datum='".$dt."', title='".$name."', content='".$content."', visibility='".$visibility."', actuals='".$actuals."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=$_REQUEST['id'];
	}
	else
	{
		$sql_query="SELECT id FROM ".$sql_pref."_events";
		$sql_res=mysql_query($sql_query, $conn_id);
		$code=mysql_num_rows($sql_res);
		for ($i=$code; $i>=1; $i--)
		{
			$sql_query="UPDATE ".$sql_pref."_events SET code='".($i+1)."' WHERE code='".$i."'";
			$sql_res=mysql_query($sql_query, $conn_id);
		}
		$sql_query="INSERT INTO ".$sql_pref."_events (enable, datum, title, content, visibility, actuals) VALUES ('".$enable."','".$dt."', '".$name."', '".$content."', '".$visibility."', '".$actuals."')";
		//echo $sql_query;
        $sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=mysql_insert_id();
	}
	if (is_uploaded_file( $_FILES['file_name']['tmp_name'])) form_calendar_mainimg_save($pic_id);
	
	
}
?>
