<?php

function check_empty($id)
{
	global $sql_pref, $conn_id;
	$sql_query1="SELECT id FROM ".$sql_pref."_catalog_rub WHERE parent_id='$id'";
	$sql_res1=mysql_query($sql_query1, $conn_id);
	if (mysql_num_rows($sql_res1)>0) return (FALSE);
	else return (TRUE);
	}

function check_count($rub_id)
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT id FROM ".$sql_pref."_catalog WHERE (enable='Yes' AND parent_id REGEXP '\"$rub_id\"')";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0) return (mysql_num_rows($sql_res));
	else return 0;
	}



function navi_panel($rub_id)
{
	global $sql_pref, $conn_id, $path_equipment;
	$out="";
    $sql_query="SELECT id, parent_id, name FROM ".$sql_pref."_catalog_rub WHERE (enable='Yes' and id='$rub_id')";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0) list($id, $parent_id, $name)=mysql_fetch_row($sql_res);
	$navi_string[]=$name;
	while ($parent_id!=="0")
		{
		$sql_query="SELECT id, parent_id, name FROM ".$sql_pref."_catalog_rub WHERE (enable='Yes' and id='$parent_id')";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) list($id, $parent_id, $name)=mysql_fetch_row($sql_res);
		if (check_empty($id)) $navi_string[]="<a href='/".$path_equipment."/".$id.".html'>".$name."</a> /";
			else $navi_string[]=$name;
		}
	$navi_string[]="<a href='/".$path_equipment."/'>Весь каталог</a>";
	krsort($navi_string);
	foreach($navi_string as $k=>$v) $out.=$v."/";
return ($out);
}


function search_panel()
{
$out="<form name='form_name' action='?' method='post' enctype='multipart/form-data'>
<input type='hidden' name='action' value='search'>
<table cellpadding='1' cellspacing='1' border='0' bgcolor='#FFFFFF'>
	<tr><td colspan='2'>Поиск по каталогу</td></tr>
	<tr>
		<td><input type='text' name='search_str'></td>
		<td><input type='submit' name='button_submit' value='Найти'></td>
	</tr>
</table>
</form>";
return ($out);
}

function equipment_main()
{
	global $sql_pref, $conn_id, $art_url;
	$out="";
    
	if ($_REQUEST['action']=="search")  {$search_string=strip_tags($_REQUEST['search_str']); $out.=show_search($search_string);}
	
	elseif (isset($art_url)) 
	{
		$out.="<table cellpadding='2' cellspacing='2' width=100%><tr><td align='left'>".navi_panel($art_url)."</td><td align='right'>".search_panel()."</td></tr></table>";
		$out.=show_rubric($art_url);
	}
	else 
	{
		$out.="<table cellpadding='2' cellspacing='2' width=100%><tr><td align='left'></td><td align='right'>".search_panel()."</td></tr></table>";
		$out.=rubric_list(0,0);
	}
	if ($_REQUEST['action']=="add") $out.=add_form($art_url); else $out.="<BR><a href='?action=add'>Добавить каталог </a>";
	if ($_REQUEST['action']=="catalog_save") $out.=form_catalog_save(); 
	return ($out);
}


function rubric_list($sub_id, $sub_level)
{
	global $sql_pref, $conn_id, $path, $path_equipment, $page_header1, $page_title;
	$sub_level++;
    $sql_query="SELECT id, name FROM ".$sql_pref."_catalog_rub WHERE (enable='Yes' and parent_id='$sub_id') ORDER BY name";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $name)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			$count=check_count($id);
			if (check_empty($id)) $name_show="<a href='/".$path_equipment."/".$id.".html'>".$name."</a> (".$count.")"; else $name_show=$name;
			
			$out.="<table cellpadding=3 cellspacing=0 border=0 width=100%>";
			if (check_empty($id) and $count==0) $out.=""; 
			else $out.="<tr><td class=cat_rubric_$sub_level valign=middle align=left>".$name_show."</td></tr>";
			$out.="</table>";
			$out.=rubric_list($id, $sub_level);
		}
		
	}
	return ($out);
}


function show_search($search_string)
{
	global $sql_pref, $conn_id, $path, $months_rus, $path_equipment;
	$out.="<table width=100%><tr><td align='left'><a href='/".$path_equipment."/'>Весь каталог</a></td><td align='right'>".search_panel()."</td></tr></table>";
	$search_string=trim($search_string);
	if($search_string!=="")
	{
	$sql_org_query="SELECT id FROM ".$sql_pref."_companies WHERE name LIKE '%$search_string%'";
	$sql_org_res=mysql_query($sql_org_query, $conn_id);
	if (mysql_num_rows($sql_org_res)>0) 
		while (list($search_org_id)=mysql_fetch_row($sql_org_res)) $search_org_str.=" OR org_id='$search_org_id'";

	$sql_query="SELECT id, parent_id FROM ".$sql_pref."_catalog WHERE enable='Yes' AND (name LIKE '%".$search_string."%' ".$search_org_str.")";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0) 
		{
		$out.= "По запросу \"".$search_string."\" найдено ".mysql_num_rows($sql_res)." каталогов";
		while(list($id, $rub_id)=mysql_fetch_row($sql_res)) 
		{
			$rub_id=unserialize($rub_id); 
			foreach($rub_id as $k=>$v) {$found[$v][]=$id;}
		}
	$out.="<table cellpadding='3' cellspacing='2' border='0' width=100%>";
	foreach($found as $rub_id=>$cat_id)
		{
		$sql_query="SELECT name FROM ".$sql_pref."_catalog_rub WHERE (enable='Yes' and id='$rub_id')";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) list($name)=mysql_fetch_row($sql_res);

			$name=stripslashes($name);
			if (check_empty($rub_id)) $name_show="<a href='/".$path_equipment."/".$rub_id.".html'>".$name."</a>"; else $name_show=$name;
			$out.="<tr><td class=cat_rubric_1 valign=middle align=left colspan='4'>".$name_show."</td></tr>";
			foreach($cat_id as $k=>$v)
			{
				$sql_query_cat="SELECT id, parent_id, name, enable, date_upload, date_modify, date_issue, format, file_size, file_name, org_id, tags, user_id FROM ".$sql_pref."_catalog WHERE enable='Yes' AND id='".$v."'";
			$sql_res_cat=mysql_query($sql_query_cat, $conn_id);
			list($id, $parent_id, $name, $enable, $date_upload, $date_modify, $date_issue, $format, $file_size, $file_name, $org_id, $tags, $user_id)=mysql_fetch_row($sql_res_cat);
		
			$date_upload=date("d.m.y", $date_upload);
			$date_modify=date("d.m.y", $date_modify);
			$month_issue=date("m", $date_issue);
			$month_issue=$months_rus["$month_issue"];
			$year_issue=date("Y", $date_issue);
			$date_issue=$month_issue." ".$year_issue;
			$format=explode("/", $format);
			$format=$format[1];
			if (file_exists($path."/img/filetypes/".$format.".gif"))$format_img="<img src='/img/filetypes/".$format.".gif' border=1 hspace='5'>";
			else $format_img="($format) ";

	if (file_exists($path."files/equipment/catalog/imgs/".$id.".jpg")) $fname=$id.".jpg";
	elseif (file_exists($path."files/equipment/catalog/imgs/".$id.".gif")) $fname=$id.".gif";
	$sql_org_query="SELECT name, id FROM ".$sql_pref."_companies WHERE id='$org_id'";
	$sql_org_res=mysql_query($sql_org_query, $conn_id);
	if (mysql_num_rows($sql_org_res)>0) {$org_name=mysql_fetch_row($sql_org_res); $org_name="<a href='/kb/companies/".$org_name[1].".html' target=_blank>".$org_name[0]."</a>";}
	else $org_name="";

	if ($file_name!=="" and file_exists($path."files/equipment/catalog/".$file_name)) $file_name="<a href='/files/equipment/catalog/".$file_name."'>Скачать</a>";
	else {$file_name="Файл не найден"; $format_img="";}

			
					$out.= "<tr class='catalog'>
					<td width=5% class='cat_img'>";
					if (isset($fname)) $out.= "<img src='/files/equipment/catalog/imgs/".$fname."' border=1>";
						else $out.= "<BR>";
					unset($fname);
					$out.= "</td>
					<td width=60%><div class='cat_name'>".$name."</div><div class='cat_issue'>".$date_issue."</div><div class='cat_org'>".$org_name."</div></td>
					<td><div class='cat_size'>".$file_size." Мб</div><div class='cat_dates'>Добавлен ".$date_upload."</div><div class='cat_dates'>Изменен ".$date_modify."</div></td>
					<td>".$format_img.$file_name."</td>
					</tr>";
		unset($format_img);
			}
		
		
		}
		$out.="</table>";
	}
	else $out.="По запросу \"".$search_string."\" ничего не найдено";	
	}
	else $out.="Задан пустой поисковый запрос";
return($out);
}



function show_catalog($rub_id)
{
	global $sql_pref, $conn_id, $path, $months_rus;
	global $page_title, $page_header1;


	$sql_query="SELECT id, parent_id, name, enable, date_upload, date_modify, date_issue, format, file_size, file_name, org_id, tags, user_id FROM ".$sql_pref."_catalog WHERE enable='Yes' AND parent_id REGEXP '\"$rub_id\"'";
	$sql_res=mysql_query($sql_query, $conn_id);

	if (mysql_num_rows($sql_res)>0)
	{
		$out.= "<table cellspacing='2' cellpadding='2' border='0' width='100%'>";
		while(list($id, $parent_id, $name, $enable, $date_upload, $date_modify, $date_issue, $format, $file_size, $file_name, $org_id, $tags, $user_id)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			$date_upload=date("d.m.y", $date_upload);
			$date_modify=date("d.m.y", $date_modify);
			$month_issue=date("m", $date_issue);
			$month_issue=$months_rus["$month_issue"];
			$year_issue=date("Y", $date_issue);
			$date_issue=$month_issue." ".$year_issue;
			$format=explode("/", $format);
			$format=$format[1];
			if (file_exists($path."/img/filetypes/".$format.".gif"))$format_img="<img src='/img/filetypes/".$format.".gif' border=1 hspace='5'>";
			else $format_img="($format) ";

	if (file_exists($path."files/equipment/catalog/imgs/".$id.".jpg")) $fname=$id.".jpg";
	elseif (file_exists($path."files/equipment/catalog/imgs/".$id.".gif")) $fname=$id.".gif";
	$sql_org_query="SELECT name, id FROM ".$sql_pref."_companies WHERE id='$org_id'";
	$sql_org_res=mysql_query($sql_org_query, $conn_id);
	if (mysql_num_rows($sql_org_res)>0) {$org_name=mysql_fetch_row($sql_org_res); $org_name="<a href='/kb/companies/".$org_name[1].".html' target=_blank>".$org_name[0]."</a>";}
		else $org_name="";
	if ($file_name!=="" and file_exists($path."files/equipment/catalog/".$file_name)) $file_name="<a href='/files/equipment/catalog/".$file_name."'>Скачать</a>";
	else {$file_name="Файл не найден"; $format_img="";}

			
					$out.= "<tr class='catalog' width=5%>
					<td class='cat_img'>";
					if (isset($fname)) $out.= "<img src='/files/equipment/catalog/imgs/".$fname."' border=1>";
						else $out.= "<BR>";
					unset($fname);
					$out.= "</td>
					<td width=60%><div class='cat_name'>".$name."</div><div class='cat_issue'>".$date_issue."</div><div class='cat_org'>".$org_name."</div></td>
					<td><div class='cat_size'>".$file_size." Мб</div><div class='cat_dates'>Добавлен ".$date_upload."</div><div class='cat_dates'>Изменен ".$date_modify."</div></td>
					<td>".$format_img.$file_name."</td>
					</tr>";
		unset($format_img);
		}
	$out.="</table>";
	}
	else $out.="В данной категории нет каталогов";

return($out);
}


function show_rubric($rub_id)
{
	global $sql_pref, $conn_id, $path;
	global $page_title, $page_header1;
	$sql_query="SELECT id, name, descr FROM ".$sql_pref."_catalog_rub WHERE id='$rub_id' AND enable='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $name, $descr)=mysql_fetch_row($sql_res))
		{
		if (file_exists($path."files/equipment/rubric/imgs/".$id.".jpg")) $fname=$id.".jpg";
	elseif (file_exists($path."files/equipment/rubric/imgs/".$id.".gif")) $fname=$id.".gif";
					$out.="<table class='main' cellspacing='2' cellpadding='2' width='100%'>
					<tr class='common'>
					<td align='center' colspan=2><H2>".$name."</H2></td>
					</tr>
					<tr class='common'>
					<td width=10%>";
					if (isset($fname)) $out.="<img src='/files/equipment/rubric/imgs/".$fname."' border='0' alt='".$name."'>";
					else $out.="<BR>";
					$out.="</td><td align='left'>".$descr."</td>
					</tr>
					</table>";
		$out.=show_catalog($id);
		$page_title=$name;
		}
	}
return($out);
}









function add_form($rub_id)
{
	global $sql_pref, $conn_id, $path, $months_rus2, $user_id;

	$sql_rubric_query="SELECT id, name FROM ".$sql_pref."_catalog_rub";
	$sql_rubric_res=mysql_query($sql_rubric_query, $conn_id);
	while (list($allrub_id, $allrub_name)=mysql_fetch_row($sql_rubric_res)) $allrubs[$allrub_id]=$allrub_name;
	$allrubs[0]="Не указано";
	
	$sql_org_query="SELECT id, name FROM ".$sql_pref."_companies";
	$sql_org_res=mysql_query($sql_org_query, $conn_id);
	while (list($allorg_id, $allorg_name)=mysql_fetch_row($sql_org_res)) $allorgs[$allorg_id]=$allorg_name;
	asort($allorgs);
	$allorgs[0]="Не указано";

if ($user_id==0) return ("<div>Добавлять каталоги могут только <a href='/auth/register/'>зарегистрированные</a> пользователи</div>");
$out="<form name=\"form_name\" action=\"?\" method=\"post\" enctype=\"multipart/form-data\">
<input type=\"hidden\" name=\"rub_id\" value=\"$rub_id\">
<input type=\"hidden\" name=\"action\" value=\"catalog_save\">
<input type=\"hidden\" name=\"user_id\" value=\"$user_id\">";
$out.="<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" bgcolor=\"#FFFFFF\">";
$out.="<tr class=\"form_topline\">
		<td colspan=\"2\" align=\"center\"><b>Добавление каталога</b></td>
	</tr>";

$out.="<tr>
		<td class=\"form_left\">Рубрика каталога</td>
		<td class=\"form_main\"><select name='parent_id'>";
			foreach($allrubs as $ka=>$va) 
				if($ka==$rub_id) $out.="<option value=".$ka." selected>".$va."</option>";
				else $out.="<option value=".$ka.">".$va."</option>";
			$out.="</select>
			
			</td>
	</tr>
	<tr>
		<td class=\"form_left\">Изображение</td>
		<td class=\"form_main\">
			<div><input class=\"form_file\" type=\"file\" name=\"img_name\" size=\"65\"></div>
		</td>
	</tr>
	<tr>
		<td class=\"form_left\">Название</td>
		<td class=\"form_main\"><input class=\"form\" type=\"text\" name=\"name\" size=\"65\"> </td>
	</tr>
	<tr>
		<td class=\"form_left\">Дата выпуска</td>
		<td class=\"form_main\">
		<select name=\"month_issue\"><option value=\"0\"></option>";
		 foreach($months_rus2 as $k=>$v) 
		$out.="<option value=".$k.">".$v."</option>";
		$out.="</select>";
		$out.="<select name=\"year_issue\"><option value=\"0\"></option>";
		for ($i=2000; $i<2021; $i++) 
			$out.="<option value=".$i.">".$i."</option>";
		$out.="</select>
		</td>
	</tr>
	<tr>
		<td class=\"form_left\">Файл (pdf)</td>
		<td class=\"form_main\">
		<div><input class=\"form_file\" type=\"file\" name=\"file_name\" size=\"65\"></div>
			</td>
	</tr>
	<tr>
		<td class=\"form_left\">Организация</td>
		<td class=\"form_main\"><select name=\"org_id\">";
		foreach($allorgs as $k=>$v) 
			if ($k==0) $out.="<option value=".$k." selected>".$v."</option>"; 
			else $out.="<option value=".$k.">".$v."</option>";
		$out.="</select></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class=\"form_button\" type=\"submit\" name=\"button_submit\" value=\"Отправить\"></td>
	</tr>
</table>
</form>";
return($out);
}





function form_catalog_save()
{
	global $sql_pref, $conn_id;
	if (isset($_REQUEST['parent_id'])) $parent_id=$_REQUEST['parent_id'];
	if (isset($_REQUEST['name'])) {$name=strip_tags($_REQUEST['name']); $name=addslashes($name);} else $name="";
	if (isset($_REQUEST['month_issue'])) $month_issue=$_REQUEST['month_issue'];
	if (isset($_REQUEST['year_issue'])) $year_issue=$_REQUEST['year_issue'];
	if (isset($_REQUEST['org_id'])) $org_id=$_REQUEST['org_id'];
	if (isset($_REQUEST['user_id'])) $user_id=$_REQUEST['user_id'];

$enable="No";
$moderation="Yes";

$parent_id=array($parent_id);
$parent_id=serialize($parent_id);


	$date=time();
	$date_upload=$date;
	$date_modify=$date;
	if ($month_issue==0 and $year_issue==0) $date_issue=0;
	else $date_issue=mktime( 1,0,0,$month_issue,1,$year_issue);

if (is_uploaded_file( $_FILES['file_name']['tmp_name'])) 
	{
	$data_file=user_catalog_file_save();
		if (is_array($data_file))
		{
		$file_name=$data_file["name"];
		$file_size=$data_file["size"];
		$format=$data_file["type"];
		$out.="<div>Каталог успешно добавлен и будет доступен после модерации. Спасибо.</div>";
		$out.="Файл ".$file_name." (".$file_size." Мб)<BR>";
		$sql_query="INSERT INTO ".$sql_pref."_catalog (parent_id, name, file_name, file_size, format, date_issue, org_id, user_id, date_upload, date_modify, enable, moderation) VALUES ('".$parent_id."', '".$name."', '".$file_name."', '".$file_size."', '".$format."', '".$date_issue."', '".$org_id."', '".$user_id."','".$date_upload."', '".$date_modify."', '".$enable."', '".$moderation."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$name=stripslashes($name);
		$out.=stripslashes($name)."<BR>";
		$pic_id=mysql_insert_id();
		if (is_uploaded_file( $_FILES['img_name']['tmp_name'])) $out.=user_catalog_img_save($pic_id);
		}
		else $out.=$data_file;
	
	}
	else $out.="<div>Не указан файл каталога</div>";
	return $out;
}



function make_dir($dir_path)
{
	if (!is_dir($dir_path)) mkdir($dir_path, 0777);
}

function save_file($src, $dest)
{
	global $path;
	if (is_uploaded_file($src) || file_exists($src))
	{
		if (is_uploaded_file($src)) move_uploaded_file($src, $dest);
		if (file_exists($src)) copy($src, $dest);
		chmod ($dest, 0644);
	}
}

function save_img($src, $dest, $resize, $width, $height, $quality=80, $mime="image/jpeg")
{
	global $path;
	if (is_uploaded_file($src) || file_exists($src))
	{
		if (is_uploaded_file($src)) move_uploaded_file($src, $dest);
		if (file_exists($src)) copy($src, $dest);
		chmod ($dest, 0644);
		$size=getimagesize($dest);
		if ($resize==true && ($size[0]>$width || $size[1]>$height))
		{
			if (($size[0]/$size[1])>($width/$height))
			{
				$t_h=($size[1]/$size[0])*$width;
				if ($mime=="image/jpeg" || $mime=="image/pjpeg")
				{
					$im_p=imageCreateFromJpeg($dest);
					$im_t=imageCreateTrueColor($width,$t_h);
					imageCopyResampled($im_t,$im_p,0,0,0,0,$width,$t_h,$size[0],$size[1]);
					imageJpeg($im_t,$dest,$quality);
				}
				elseif ($mime=="image/gif")
				{
					$im_p=imageCreateFromGif($dest);
					$im_t=imageCreateTrueColor($width,$t_h);
					imageCopyResampled($im_t,$im_p,0,0,0,0,$width,$t_h,$size[0],$size[1]);
					imageGif($im_t,$dest,$quality);
				}
			}
			else
			{
				$t_w=($size[0]/$size[1])*$height;
				if ($mime=="image/jpeg" || $mime=="image/pjpeg")
				{
					$im_p=imageCreateFromJpeg($dest);
					$im_t=imageCreateTrueColor($t_w,$height);
					imageCopyResampled($im_t,$im_p,0,0,0,0,$t_w,$height,$size[0],$size[1]);
					imageJpeg($im_t,$dest,$quality);
				}
				elseif ($mime=="image/gif")
				{
					$im_p=imageCreateFromGif($dest);
					$im_t=imageCreateTrueColor($t_w,$height);
					imageCopyResampled($im_t,$im_p,0,0,0,0,$t_w,$height,$size[0],$size[1]);
					imageGif($im_t,$dest,$quality);
				}
			}
		}
	}
}

function user_catalog_img_save($pic_id)
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
	else return "Ошибка! Недопустимый формат файла с изображением";
}



function user_catalog_file_save()
{
	global $path, $conn_id, $sql_pref;

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
		if($mime=="application/pdf") 
		{
		save_file($src, $dest);
		$data_file['name']=$name;
		$data_file['type']=$mime;
		$data_file['size']=round($size/1048576, 2);
		return $data_file;
		}
		else return "Ошибка! Недопустимый формат файла каталога. К загрузке допускаются файлы PDF";
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

?>