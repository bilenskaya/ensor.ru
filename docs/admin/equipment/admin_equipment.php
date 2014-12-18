<?php

function check_empty($id)
{
	global $sql_pref, $conn_id;
	$sql_query1="SELECT id FROM ".$sql_pref."_catalog_rub WHERE parent_id='$id'";
	$sql_res1=mysql_query($sql_query1, $conn_id);
	$sql_query2="SELECT id FROM ".$sql_pref."_catalog WHERE parent_id REGEXP '\"$id\"'";
	$sql_res2=mysql_query($sql_query2, $conn_id);
	if (mysql_num_rows($sql_res1)>0 OR mysql_num_rows($sql_res2)>0) return (FALSE);
	else return (TRUE);
	}

//Функция нормализации имени файла
function file_name_norm($name)
	{
	$base_name_norm="";
	$names=explode(".",$name);
	$extension=$names[count($names)-1];
	$base_name=str_replace(".".$extension,"", $name);
	$base_name=strtr($base_name,"абвгдеёзийклмнопрстуфхъыэ ", "abvgdeeziyklmnoprstufh'ie_");
	$base_name=strtr($base_name,"АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ", "ABVGDEEZIYKLMNOPRSTUFH'IE");
	$base_name=strtr($base_name,array("ж"=>"zh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"shch","ь"=>"", "ю"=>"yu", "я"=>"ya", "Ж"=>"ZH", "Ц"=>"TS", "Ч"=>"CH", "Ш"=>"SH", "Щ"=>"SHCH","Ь"=>"", "Ю"=>"YU", "Я"=>"YA"));
	$base_len=strlen($base_name);
	for ($i=0; $i<$base_len; $i++){
	$char=substr($base_name, $i, 1);
	if (ereg("[[:alnum:]]+", $char) or $char==="_") $base_name_norm=$base_name_norm.$char;
	}
	$file_name_norm=$base_name_norm.".".$extension;
return $file_name_norm;
}

function admin_subrubric_list ($sub_id, $sub_level)
{
	global $sql_pref, $conn_id;
	$sub_level++;
	$sql_query="SELECT id, enable, name FROM ".$sql_pref."_catalog_rub WHERE parent_id='$sub_id' ORDER BY name";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $enable, $name)=mysql_fetch_row($sql_res))
		{
			$name_show="<a href='?id=".$id."&action=rubric_show#rubric_show'>".$name."</a>";
			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=rubric_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=rubric_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение' border=0></a>";
			
			$edit_pic="<a href='?id=".$id."&action=rubric_edit#rubric_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			$add_pic="<a href='?id=".$id."&action=rubric_add#rubric_add'><img src='/admin/img/line.gif' width=25 height=13 alt='Добавить подрубрику' border=0></a>";
			if (check_empty($id)) $del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&action=rubric_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			else $del="";
			
					echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$enable_pic.$edit_pic.$add_pic."</td>
					<td class=cat_rubric_$sub_level align='left'>".$name_show."</td>
					<td class='common' align='center'>".$del."</td>
					</tr>";
			admin_subrubric_list($id, $sub_level);
		}

	}
}

function admin_rubric_list()
{
echo "<a href='?action=moderate#moderate'>Модерация каталогов</a>";
$add_pic="<a href='?id=0&action=rubric_add#rubric_add'><img src='/admin/img/line.gif' width=25 height=13 alt='Добавить подрубрику' border=0></a>";
echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
    			<tr class='maintitle'>
    				<td width='20' class='maintitle' align='center'><b>id</b></td>
    				<td width='80' class='maintitle' align='center'>".$add_pic."</td>
    				<td class='maintitle' align='left'><b>название</b></td>
    				<td width='30' class='maintitle' align='center'><b>del</b></td>
    			</tr>";
admin_subrubric_list(0, 0);
echo "</table>";
}


function admin_show_catalog($rub_id)
{
	global $sql_pref, $conn_id, $path;
	$add_pic="<a href='?id=".$rub_id."&action=catalog_add#catalog_add'><img src='/admin/img/line.gif' width=25 height=13 alt='Добавить каталог' border=0></a>";
	$sql_query="SELECT id, parent_id, name, enable, date_upload, date_modify, date_issue, format, file_size, file_name, org_id, tags, user_id FROM ".$sql_pref."_catalog WHERE (parent_id REGEXP '\"$rub_id\"' AND moderation = 'No')";
	$sql_res=mysql_query($sql_query, $conn_id);
	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
    			<tr class='maintitle'>
    				<td width='20' class='maintitle' align='center'><b>id</b></td>
    				<td width='80' class='maintitle' align='center'>".$add_pic."</td>
    				<td class='maintitle' align='left'><b>рубрика</b></td>
    				<td class='maintitle' align='left'><b>картинка</b></td>
    				<td class='maintitle' align='left'><b>название</b></td>
    				<td class='maintitle' align='left'><b>дата выпуска</b></td>
    				<td class='maintitle' align='left'><b>имя файла</b></td>
    				<td class='maintitle' align='left'><b>размер</b></td>
    				<td class='maintitle' align='left'><b>формат</b></td>
    				<td class='maintitle' align='left'><b>организация</b></td>
    				<td class='maintitle' align='left'><b>пользователь</b></td>
    				<td class='maintitle' align='left'><b>добавлен</b></td>
    				<td class='maintitle' align='left'><b>изменен</b></td>
    				<td class='maintitle' align='left'><b>тэги</b></td>
    				<td width='30' class='maintitle' align='center'><b>del</b></td>
    			</tr>";
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $parent_id, $name, $enable, $date_upload, $date_modify, $date_issue, $format, $file_size, $file_name, $org_id, $tags, $user_id)=mysql_fetch_row($sql_res))
		{
			
			$parent_id=unserialize($parent_id);
			$parent_id_string="";
			foreach($parent_id as $k=>$v) $parent_id_string=$parent_id_string.$v."<BR>";
			$name=stripslashes($name);
			$date_upload=date("d.m.y", $date_upload);
			$date_modify=date("d.m.y", $date_modify);
			$date_issue=date("F Y", $date_issue);
			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=catalog_enable&rub_id=".$rub_id."'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=catalog_enable&rub_id=".$rub_id."'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение' border=0></a>";
			
			$edit_pic="<a href='?id=".$id."&rub_id=".$rub_id."&action=catalog_edit#catalog_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&rub_id=".$rub_id."&action=catalog_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			if (file_exists($path."files/equipment/catalog/imgs/".$id.".jpg")) $fname=$id.".jpg";
	elseif (file_exists($path."files/equipment/catalog/imgs/".$id.".gif")) $fname=$id.".gif";
	$sql_org_query="SELECT name FROM ".$sql_pref."_companies WHERE id='$org_id'";
	$sql_org_res=mysql_query($sql_org_query, $conn_id);
	if (mysql_num_rows($sql_org_res)>0) {$org_name=mysql_fetch_row($sql_org_res); $org_name=$org_name[0];}
		else $org_name="Не указано";
		$sql_user_query="SELECT surname, name, name2 FROM ".$sql_pref."_users WHERE id='$user_id'";
	$sql_user_res=mysql_query($sql_user_query, $conn_id);
	if (mysql_num_rows($sql_user_res)>0) {list($surname, $user_name, $name2)=mysql_fetch_row($sql_user_res); $full_user=$surname." ".$user_name." ".$name2;}
		else $full_user="Admin";
			
					echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$enable_pic.$edit_pic."</td>
					<td class='common' align='left'>".$parent_id_string."</td>
					<td class='common' align='left'>";
					if (isset($fname)) echo "<img src='/files/equipment/catalog/imgs/".$fname."' border=1>";
						else echo "<BR>";
					unset($fname);
					echo "</td>
					<td class='common' align='left'>".$name."</td>
					<td class='common' align='left'>".$date_issue."</td>";
					if (strlen($file_name)>0 and file_exists($path."files/equipment/catalog/".$file_name))
						echo "<td class='common' align='left'>".$file_name."</td>";
						else echo "<td class='common' bgcolor='red' align='left'>".$file_name."</td>";
					echo "<td class='common' align='left'>".$file_size."</td>
					<td class='common' align='left'>".$format."</td>
					<td class='common' align='left'>".$org_name."</td>
					<td class='common' align='left'>".$full_user."(".$user_id.")"."</td>
					<td class='common' align='left'>".$date_upload."</td>
					<td class='common' align='left'>".$date_modify."</td>
					<td class='common' align='left'>".$tags."</td>
					<td class='common' align='center'>".$del."</td>
					</tr>";
		}
	}
	echo "</table>";
}



function admin_show_moderate()
{
	global $sql_pref, $conn_id, $path;

	$sql_query="SELECT id, parent_id, name, enable, date_upload, date_modify, date_issue, format, file_size, file_name, org_id, tags, user_id FROM ".$sql_pref."_catalog WHERE moderation='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	echo "<b>МОДЕРАЦИЯ КАТАЛОГОВ</b>";
	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
    			<tr class='maintitle'>
    				<td width='20' class='maintitle' align='center'><b>id</b></td>
					<td width='20' class='maintitle' align='center'><br></td>
    				<td class='maintitle' align='left'><b>рубрика</b></td>
    				<td class='maintitle' align='left'><b>картинка</b></td>
    				<td class='maintitle' align='left'><b>название</b></td>
    				<td class='maintitle' align='left'><b>дата выпуска</b></td>
    				<td class='maintitle' align='left'><b>имя файла</b></td>
    				<td class='maintitle' align='left'><b>размер</b></td>
    				<td class='maintitle' align='left'><b>формат</b></td>
    				<td class='maintitle' align='left'><b>организация</b></td>
    				<td class='maintitle' align='left'><b>пользователь</b></td>
    				<td class='maintitle' align='left'><b>добавлен</b></td>
    				<td class='maintitle' align='left'><b>изменен</b></td>
    				<td class='maintitle' align='left'><b>тэги</b></td>
    				<td width='30' class='maintitle' align='center'><b>del</b></td>
    			</tr>";
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $parent_id, $name, $enable, $date_upload, $date_modify, $date_issue, $format, $file_size, $file_name, $org_id, $tags, $user_id)=mysql_fetch_row($sql_res))
		{
			
			$parent_id=unserialize($parent_id);
			$parent_id_string="";
			foreach($parent_id as $k=>$v) $parent_id_string=$parent_id_string.$v."<BR>";
			$name=stripslashes($name);
			$date_upload=date("d.m.y", $date_upload);
			$date_modify=date("d.m.y", $date_modify);
			$date_issue=date("F Y", $date_issue);

			$edit_pic="<a href='?id=".$id."&action=moderate_edit#moderate_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			if ($parent_id[0]!=="0")$moderate_pic="<a href=\"javascript:if(confirm('Вы уверены? Принять каталог?'))window.location='?id=".$id."&action=moderate_add'\"><img src='/admin/img/active.gif' width=25 height=13 alt='Принять каталог' border=0></a>"; else $moderate_pic="<a href=\"javascript:if(confirm('Укажите рубрику'))window.location='?&action=moderate'\"><img src='/admin/img/active.gif' width=25 height=13 alt='Принять каталог' border=0></a>";
			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&rub_id=moderate&action=catalog_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			if (file_exists($path."files/equipment/catalog/imgs/".$id.".jpg")) $fname=$id.".jpg";
	elseif (file_exists($path."files/equipment/catalog/imgs/".$id.".gif")) $fname=$id.".gif";
	$sql_org_query="SELECT name FROM ".$sql_pref."_companies WHERE id='$org_id'";
	$sql_org_res=mysql_query($sql_org_query, $conn_id);
	if (mysql_num_rows($sql_org_res)>0) {$org_name=mysql_fetch_row($sql_org_res); $org_name=$org_name[0];}
		else $org_name="Не указано";
	
	$sql_user_query="SELECT surname, name, name2 FROM ".$sql_pref."_users WHERE id='$user_id'";
	$sql_user_res=mysql_query($sql_user_query, $conn_id);
	if (mysql_num_rows($sql_user_res)>0) {list($surname, $user_name, $name2)=mysql_fetch_row($sql_user_res); $full_user=$surname." ".$user_name." ".$name2;}
		else $full_user="Admin";
					echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$edit_pic.$moderate_pic."</td>
					<td class='common' align='left'>".$parent_id_string."</td>
					<td class='common' align='left'>";
					if (isset($fname)) echo "<img src='/files/equipment/catalog/imgs/".$fname."' border=1>";
						else echo "<BR>";
					unset($fname);
					echo "</td>
					<td class='common' align='left'>".$name."</td>
					<td class='common' align='left'>".$date_issue."</td>";
					if (strlen($file_name)>0 and file_exists($path."files/equipment/catalog/".$file_name))
						echo "<td class='common' align='left'><a href=/files/equipment/catalog/".$file_name.">".$file_name."</a></td>";
						else echo "<td class='common' bgcolor='red' align='left'>".$file_name."</td>";
					echo "<td class='common' align='left'>".$file_size."</td>
					<td class='common' align='left'>".$format."</td>
					<td class='common' align='left'>".$org_name."</td>
					<td class='common' align='left'>".$full_user."(".$user_id.")"."</td>
					<td class='common' align='left'>".$date_upload."</td>
					<td class='common' align='left'>".$date_modify."</td>
					<td class='common' align='left'>".$tags."</td>
					<td class='common' align='center'>".$del."</td>
					</tr>";
		}
	}
	echo "</table>";
}



function admin_show_rubric($rub_id)
{
	global $sql_pref, $conn_id, $path;

	$sql_query="SELECT id, name, descr FROM ".$sql_pref."_catalog_rub WHERE id='$rub_id'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $name, $descr)=mysql_fetch_row($sql_res))
		{
		if (file_exists($path."files/equipment/rubric/imgs/".$id.".jpg")) $fname=$id.".jpg";
	elseif (file_exists($path."files/equipment/rubric/imgs/".$id.".gif")) $fname=$id.".gif";
	else $fname="no_image.gif";
					echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
					<tr class='common'>
					<td align='center' colspan=2><H1>".$name."</H1></td>
					</tr>
					<tr class='common'>
					<td width=10%><img src='/files/equipment/rubric/imgs/".$fname."' border=1></td><td align='center'>".$descr."</td>
					</tr>
					</table>";
		admin_show_catalog($id);
		}
	}
}




function form_rubric_save()
{
	global $sql_pref, $conn_id;
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (isset($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
	if (isset($_REQUEST['parent_id'])) $parent_id=addslashes($_REQUEST['parent_id']); else $parent_id=0;
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";

	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_catalog_rub SET name='".$name."', parent_id='".$parent_id."', descr='".$descr."', enable='".$enable."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=$_REQUEST['id'];
	}
	else
	{
		$sql_query="INSERT INTO ".$sql_pref."_catalog_rub (name, parent_id, descr, enable) VALUES ('".$name."', '".$parent_id."', '".$descr."', '".$enable."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=mysql_insert_id();
	}
	if (is_uploaded_file( $_FILES['file_name']['tmp_name'])) form_rubric_mainimg_save($pic_id);
	
	
}







function form_catalog_save()
{
	global $sql_pref, $conn_id;
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) $id=$_REQUEST['id'];
	if (isset($_REQUEST['parent_id'])) $parent_id=$_REQUEST['parent_id'];
	if (isset($_REQUEST['add_parent_id']) and $_REQUEST['add_parent_id']>0) $parent_id[]=$_REQUEST['add_parent_id'];
	if (isset($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
	if (isset($_REQUEST['month_issue'])) $month_issue=$_REQUEST['month_issue'];
	if (isset($_REQUEST['year_issue'])) $year_issue=$_REQUEST['year_issue'];
	if (isset($_REQUEST['org_id'])) $org_id=$_REQUEST['org_id'];
	if (isset($_REQUEST['user_id'])) $user_id=$_REQUEST['user_id'];
	if (isset($_REQUEST['tags'])) $tags=$_REQUEST['tags'];
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";

$parent_id=array_unique($parent_id);
foreach($parent_id as $k=>$v) if ($v==0 and count($parent_id)>1) unset($parent_id[$k]);
if (array_sum($parent_id)==0) foreach($parent_id as $k=>$v) $parent_id[$k]="$k";
$parent_id=serialize($parent_id);

if (is_uploaded_file( $_FILES['file_name']['tmp_name'])) 
	{
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) $replace_catalog=$id; else $replace_catalog="";
	$data_file=form_catalog_file_save($replace_catalog);
	$file_name=$data_file["name"];
	$file_size=$data_file["size"];
	$format=$data_file["type"];
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
		{
		$sql_query="UPDATE ".$sql_pref."_catalog SET file_name='".$file_name."', file_size='".$file_size."', format='".$format."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		}
	}

	$date=time();
	$date_upload=$date;
	$date_modify=$date;
	if ($month_issue==0 and $year_issue==0) $date_issue=0;
	else $date_issue=mktime( 1,0,0,$month_issue,1,$year_issue);

	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_catalog SET parent_id='".$parent_id."', name='".$name."', date_issue='".$date_issue."', org_id='".$org_id."', user_id='".$user_id."', date_modify='".$date_modify."', tags='".$tags."', enable='".$enable."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=$_REQUEST['id'];
	}
	else
	{
		$sql_query="INSERT INTO ".$sql_pref."_catalog (parent_id, name, file_name, file_size, format, date_issue, org_id, user_id, date_upload, date_modify, tags, enable) VALUES ('".$parent_id."', '".$name."', '".$file_name."', '".$file_size."', '".$format."', '".$date_issue."', '".$org_id."', '".$user_id."','".$date_upload."', '".$date_modify."', '".$tags."', '".$enable."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=mysql_insert_id();
	}
	if (is_uploaded_file( $_FILES['img_name']['tmp_name'])) form_catalog_mainimg_save($pic_id);
	
	
}













function form_rubric_mainimg_save($pic_id)
{
	global $path;
	global $equipment_img_width, $equipment_img_height, $equipment_img_thumb_width, $equipment_img_thumb_height;

	$mime=$_FILES['file_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif")
	{
		if ($mime=="image/jpeg" || $mime=="image/pjpeg") {$ext=".jpg";$ext1=".gif";}
		elseif ($mime=="image/gif") {$ext=".gif";$ext1=".jpg";}

		$dest=$path."files/equipment/rubric/imgs/".$pic_id.$ext;
		$dest1=$path."files/equipment/rubric/imgs/".$pic_id.$ext1;
		$src=$_FILES["file_name"]["tmp_name"];
		$resize=true;
		del_file($dest);del_file($dest1);
		save_img($src, $dest, $resize, $equipment_img_width, $equipment_img_height, 80, $mime);

		$thumb_src=$dest;
		$thumb_dest=$path."files/equipment/rubric/thumbs/".$pic_id.$ext;
		$thumb_dest1=$path."files/equipment/rubric/thumbs/".$pic_id.$ext1;
		$thumb_resize=true;
		del_file($thumb_dest);del_file($thumb_dest1);
		save_img($thumb_src, $thumb_dest, $thumb_resize, $equipment_img_thumb_width, $equipment_img_thumb_height, 80, $mime);
	}
	else echo "Ошибка! Недопустимый формат файла.";
}

function form_catalog_mainimg_save($pic_id)
{
	global $path, $equipment_catalog_img_width, $equipment_catalog_img_height;

	$mime=$_FILES['img_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif")
	{
		if ($mime=="image/jpeg" || $mime=="image/pjpeg") {$ext=".jpg";$ext1=".gif";}
		elseif ($mime=="image/gif") {$ext=".gif";$ext1=".jpg";}

		$dest=$path."files/equipment/catalog/imgs/".$pic_id.$ext;
		$dest1=$path."files/equipment/catalog/imgs/".$pic_id.$ext1;
		$src=$_FILES["img_name"]["tmp_name"];
		$resize=true;
		del_file($dest);del_file($dest1);
		save_img($src, $dest, $resize, $equipment_catalog_img_width, $equipment_catalog_img_height, 80, $mime);
	}
	else echo "Ошибка! Недопустимый формат файла.";
}



function form_catalog_file_save($replace)
{
	global $path, $conn_id, $sql_pref;
	if ($replace!=="")
	{
	$sql_query="SELECT file_name FROM ".$sql_pref."_catalog WHERE id='$replace'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$name_to_del=mysql_fetch_row($sql_res);
	$dest_to_del=$path."files/equipment/catalog/".$name_to_del[0];
	if (strlen($name_to_del[0])>0) del_file($dest_to_del);
	$sql_query="UPDATE ".$sql_pref."_catalog SET file_name='' WHERE id='$replace'";
	$sql_res=mysql_query($sql_query, $conn_id);
	}


	$orig_name=$_FILES['file_name']['name'];
	$orig_name=file_name_norm($orig_name);
	
	$exist=mysql_query("select count(if(file_name='$orig_name',1,NULL)) from ".$sql_pref."_catalog");
	$r=mysql_fetch_row($exist);
	$name=$orig_name;
	for ($i=1;$r[0]>0;$i++)
	{
		$name=$i."_".$orig_name;
		$exist=mysql_query("select count(if(file_name='$name',1,NULL)) from ".$sql_pref."_catalog");
		$r=mysql_fetch_row($exist);
	}
	
	$mime=$_FILES['file_name']['type'];
	$size=$_FILES['file_name']['size'];

		$dest=$path."files/equipment/catalog/".$name;
		$src=$_FILES["file_name"]["tmp_name"];
		del_file($dest);
		save_file($src, $dest);
$data_file['name']=$name;
$data_file['type']=$mime;
$data_file['size']=round($size/1048576, 2);
return $data_file;
}

function del_catalog($id)
{
	global $path, $conn_id, $sql_pref;
	$sql_query="SELECT file_name FROM ".$sql_pref."_catalog WHERE id='$id'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$name_to_del=mysql_fetch_row($sql_res);
	$dest_to_del=$path."files/equipment/catalog/".$name_to_del[0];
	if (strlen($name_to_del[0])>0) del_file($dest_to_del);
	del_file($path."/files/equipment/catalog/imgs/".$id.".jpg");
	del_file($path."/files/equipment/catalog/imgs/".$id.".gif");
	del_record('catalog', $id, 'No', -1);
}
?>
