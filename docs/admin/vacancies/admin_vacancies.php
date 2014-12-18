<?php

function vacancies_show()
{
	global $sql_pref, $conn_id, $path;
	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
			<tr class='maintitle'>
				<td width='20' class='maintitle' align='center'><b>id</b></td>
				<td width='80' class='maintitle' align='center'><b>дата</b></td>
				<td width='80' class='maintitle' align='center'>&nbsp;</td>
				<td class='maintitle' align='left'><b>Название вакансии /<br/> Работодатель</b></td>
				<td width='80' class='maintitle' align='center'><b>Зарплата</b></td>
                <td width='80' class='maintitle' align='center'><b>Город</b></td>
                <td width='30' class='maintitle' align='center'><b>del</b></td>
			</tr>";

	$perpage=10;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";

	$sql_query="SELECT id, enable, main, dt, name, zp_value_min, zp_value_max, zp_valuta, company_id code FROM ".$sql_pref."_vacancies ORDER BY dt DESC".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $enable, $main, $dt, $name, $zp_value_min, $zp_value_max, $zp_valuta, $code)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			$dt_show=$dt;
			if ($dt_show=="0000-00-00") $dt_show=" - ";
			if (empty($name)) $name="<i><без заголовка></i>";
//			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=vacancies_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение раздела' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=vacancies_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение раздела' border=0></a>";
			if ($main=='Yes') $main_pic="<a href='?id=".$id."&action=vacancies_main_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение на главной' border=0></a>"; else $main_pic="<a href='?id=".$id."&action=vacancies_main_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение на главной' border=0></a>";
			if ($code>1) $sort_up="<a href='?id=".$id."&action=vacancies_sort_up'><img src='/admin/img/up.gif' width=11 height=13 alt='Сортировка: Выше' border=0></a>"; else $sort_up="<img src='/admin/img/sort_none.gif' width=11 height=13 border=0>";
			$sql_query_1="SELECT id FROM ".$sql_pref."_vacancies WHERE code='".($code+1)."'";
			$sql_res_1=mysql_query($sql_query_1, $conn_id);
			if (mysql_num_rows($sql_res_1)>0) $sort_down="<a href='?id=".$id."&action=vacancies_sort_down'><img src='/admin/img/down.gif' width=11 height=13 alt='Сортировка: Ниже' border=0></a>"; else $sort_down="<img src='/admin/img/sort_none.gif' width=11 height=13 border=0>";            
//			if (file_exists($path."files/vacancies/thumbs/".$id.".jpg") || file_exists($path."files/vacancies/thumbs/".$id.".gif")) $imga="<a href='?id=".$id."&action=vacancies_mainimg#vacancies_mainimg'><img src='/admin/img/img.gif' width=25 height=13 alt='Основное изображение' border=0></a>"; else $imga="<a href='?id=".$id."&action=vacancies_mainimg#vacancies_mainimg'><img src='/admin/img/img_ina.gif' width=25 height=13 alt='Основное изображение' border=0></a>";
			if (is_dir($path."files/vacancies/".$id) && is_cat_empty($path."files/vacancies/".$id)==false)  $imgs="<a href='?id=".$id."&action=vacancies_images#vacancies_images'><img src='/admin/img/img.gif' width=25 height=13 alt='Дополнительные изображения' border=0></a>"; else $imgs="<a href='?id=".$id."&action=vacancies_images#vacancies_images'><img src='/admin/img/img_ina.gif' width=25 height=13 alt='Дополнительные изображения' border=0></a>";
			$edit_pic="<a href='?id=".$id."&action=vacancies_edit#vacancies_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&action=vacancies_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$dt_show."</td>
					<td class='common' align='center'>".$main_pic.$imgs.$edit_pic."</td>
					<td class='common' align='left'>".$sort_down.$sort_up.$name."</td>
					<td class='common' align='left'>".$zp_value_min."-".$zp_value_max." ".$zp_valuta."</td>
                    <td class='common' align='left'>".$name."</td>
                    <td class='common' align='center'>".$del."</td>
				</tr>";
		}
	}
	echo "</table>";
	echo "<br><li><a href='?action=vacancies_add#vacancies_add'>Добавить вакансию</a></li>";
	$sql_query="SELECT id FROM ".$sql_pref."_vacancies";
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










function form_vacancies_save()
{
	global $sql_pref, $conn_id;
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (!isset($_REQUEST['main']) || $_REQUEST['main']!="Yes") $main="No"; else $main="Yes";
	if (isset($_REQUEST['name'])) $name=$_REQUEST['name']; else $name="";
	$name=htmlspecialchars($name, ENT_QUOTES); $name=addslashes($name);
	if (isset($_REQUEST['zp_value_min'])) $zp_value_min=$_REQUEST['zp_value_min']; else $zp_value_min="0";
	$zp_value_min=htmlspecialchars($zp_value_min, ENT_QUOTES); $zp_value_min=addslashes($zp_value_min);
	if (isset($_REQUEST['zp_value_max'])) $zp_value_max=$_REQUEST['zp_value_max']; else $zp_value_max="0";
	$zp_value_max=htmlspecialchars($zp_value_max, ENT_QUOTES); $zp_value_max=addslashes($zp_value_max);
    if (isset($_REQUEST['zp_valuta'])) $zp_valuta=$_REQUEST['zp_valuta']; else $zp_valuta="руб";
	$zp_valuta=htmlspecialchars($zp_valuta, ENT_QUOTES); $zp_valuta=addslashes($zp_valuta);
    if (isset($_REQUEST['city_id'])) $city_id=addslashes($_REQUEST['city_id']); else $city_id=0;
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
	if (isset($_REQUEST['FCKeditor1'])) $content=addslashes($_REQUEST['FCKeditor1']); else $content="";
//	if (isset($_REQUEST['dt_day']) && isset($_REQUEST['dt_month']) && isset($_REQUEST['dt_year']) && isset($_REQUEST['date']) && $_REQUEST['date']=="Yes") $dt=$_REQUEST['dt_year']."-".$_REQUEST['dt_month']."-".$_REQUEST['dt_day']; else $dt="0000-00-00";
	if (isset($_REQUEST['xc2_dt']) && $_REQUEST['date']=="Yes") $dt=$_REQUEST['xc2_dt']; else $dt="0000-00-00";
	if (isset($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
	if (isset($_REQUEST['company_id']) AND !empty($_REQUEST['company_id'])) $company_id=$_REQUEST['company_id']; else $company_id="0";

	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_vacancies SET enable='".$enable."', main='".$main."', dt='".$dt."', name='".$name."', zp_value_min=".$zp_value_min.", zp_value_max=".$zp_value_max.", zp_valuta='".$zp_valuta."',	descr='".$descr."',	content='".$content."',	tags='".$tags."', city_id='".$city_id."', company_id=".$company_id." WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=$_REQUEST['id'];
	}
	else
	{
		$sql_query="SELECT id FROM ".$sql_pref."_vacancies";
		$sql_res=mysql_query($sql_query, $conn_id);
		$code=mysql_num_rows($sql_res);
		for ($i=$code; $i>=1; $i--)
		{
			$sql_query="UPDATE ".$sql_pref."_vacancies SET code='".($i+1)."' WHERE code='".$i."'";
			$sql_res=mysql_query($sql_query, $conn_id);
		}
		$sql_query="INSERT INTO ".$sql_pref."_vacancies (enable, main, dt, name, descr, content, tags, code, zp_value_min, zp_value_max, zp_valuta, company_id, city_id) VALUES ('".$enable."', '".$main."', '".$dt."', '".$name."', '".$descr."', '".$content."', '".$tags."', '1', '".$zp_value_min."', '".$zp_value_max."', '".$zp_valuta."', '".$company_id."', '".$city_id."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=mysql_insert_id();
	}
	if (is_uploaded_file( $_FILES['file_name']['tmp_name'])) form_vacancies_mainimg_save($pic_id);
	
	
}










function form_vacancies_mainimg_save($pic_id)
{
	global $path;
	global $vacancies_img_width, $vacancies_img_height, $vacancies_img_thumb_width, $vacancies_img_thumb_height;

	$mime=$_FILES['file_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif")
	{
		if ($mime=="image/jpeg" || $mime=="image/pjpeg") {$ext=".jpg";$ext1=".gif";}
		elseif ($mime=="image/gif") {$ext=".gif";$ext1=".jpg";}

		$dest=$path."files/vacancies/imgs/".$pic_id.$ext;
		$dest1=$path."files/vacancies/imgs/".$pic_id.$ext1;
		$src=$_FILES["file_name"]["tmp_name"];
		$resize=true;
		del_file($dest);del_file($dest1);
		save_img($src, $dest, $resize, $vacancies_img_width, $vacancies_img_height, 80, $mime);

		$thumb_src=$dest;
		$thumb_dest=$path."files/vacancies/thumbs/".$pic_id.$ext;
		$thumb_dest1=$path."files/vacancies/thumbs/".$pic_id.$ext1;
		$thumb_resize=true;
		del_file($thumb_dest);del_file($thumb_dest1);
		save_img($thumb_src, $thumb_dest, $thumb_resize, $vacancies_img_thumb_width, $vacancies_img_thumb_height, 80, $mime);
	}
	else echo "Ошибка! Недопустимый формат файла.";
}










function form_vacancies_images_save()
{
	global $path;

	if (isset($_REQUEST['file_img_name']) && !empty($_REQUEST['file_img_name'])) $filename=$_REQUEST['file_img_name'];
	else $filename=substr($_FILES['file_name']['name'], 0, strpos($_FILES['file_name']['name'], "."));

	$mime=$_FILES['file_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif")
	{
		if ($mime=="image/jpeg" || $mime=="image/pjpeg") $ext=".jpg";
		elseif ($mime=="image/gif") $ext=".gif";

		$dest=$path."files/vacancies/".$_REQUEST['id']."/".translit_url($filename).$ext;
		$src=$_FILES["file_name"]["tmp_name"];

		$resize=false;
		if (isset($_REQUEST['file_img_resize']) && $_REQUEST['file_img_resize']=="Yes")
		{
			$size=getimagesize($_FILES['file_name']['tmp_name']);
			if (isset($_REQUEST['file_img_width']) && !empty($_REQUEST['file_img_width']) && is_int(intval($_REQUEST['file_img_width']))) $width=intval($_REQUEST['file_img_width']);
			if (isset($_REQUEST['file_img_height']) && !empty($_REQUEST['file_img_height']) && is_int(intval($_REQUEST['file_img_height']))) $height=intval($_REQUEST['file_img_height']);
			if ((isset($width) && !empty($width)) || (isset($height) && !empty($height)))
			{
				if (empty($width)) $width=$size[0];
				if (empty($height)) $height=$size[0];
				if (@$width<$size[0] || @$height<$size[1])
				{
					if (@$width<$size[0] && @$height>=$size[1]) {$height=($width*$size[1])/$size[0]; $width=$size[0];}
					elseif (@$width>=$size[0] && @$height<$size[1]) {$width=($height*$size[0])/$size[1];$height=$size[1];}
					$resize=true;
				}
			}
		}

		make_dir($path."files/vacancies/".$_REQUEST['id']);
		del_file($dest);
		save_img($src, $dest, $resize, @$width, @$height, 80, $mime);
	}
}


?>
