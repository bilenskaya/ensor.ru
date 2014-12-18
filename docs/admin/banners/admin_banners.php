<?php

function banners_show()
{
	global $sql_pref, $conn_id, $path;
	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
			<tr class='maintitle'>
				<td width='20' class='maintitle' align='center'><b>id</b></td>
				<td width='80' class='maintitle' align='center'>Активность/редактировать</td>
				<td class='maintitle' align='left'><b>Описание</b></td>
				<td width='50' class='maintitle' align='center'><b>Зона размещения</b></td>
				<td width='50' class='maintitle' align='center'><b>Сортировка</b></td>
				<td width='200' class='maintitle' align='center'><b>Начало показа</b></td>
				<td width='200' class='maintitle' align='center'><b>Окончание показа</b></td>
				<td width='30' class='maintitle' align='center'><b>Удалить</b></td>
			</tr>";

	$sql_query="SELECT id, descr, enable, sort, zone, show_start, show_end FROM ".$sql_pref."_banners ORDER BY zone";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $descr, $enable,  $sort, $zone, $show_start, $show_end)=mysql_fetch_row($sql_res))
		{
			$descr=stripslashes($descr);
            //$show_start=stripslashes($show_start);
            //$show_end=stripslashes($show_end);

			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=banners_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=banners_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение' border=0></a>";



            $edit_pic="<a href='?id=".$id."&action=banners_edit#banners_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";


            $del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&action=banners_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$enable_pic.$edit_pic."</td>
					<td class='common' align='left'>".$descr."</td>
					<td class='common' align='center'>".$zone."</td>
					<td class='common' align='center'>".$sort."</td>
					<td class='common' align='center'>".$show_start."</td>
					<td class='common' align='center'>".$show_end."</td>
					<td class='common' align='center'>".$del."</td>
				</tr>";
		}
	}
	echo "</table>";
	echo "<br><li><a href='?action=banners_add#banners_add'>Добавить</a></li>";
	echo "<hr>";
}


function form_banners_save()
{
	global $sql_pref, $conn_id;
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
	if (isset($_REQUEST['xc2_dt_start']) && $_REQUEST['date_start']=="Yes") $show_start=$_REQUEST['xc2_dt_start']." ".$_REQUEST['time_start']; else $show_start="0000-00-00 00:00";
    if (isset($_REQUEST['xc2_dt_end']) && $_REQUEST['date_end']=="Yes") $show_end=$_REQUEST['xc2_dt_end']." ".$_REQUEST['time_end']; else $show_end="0000-00-00 00:00";
    if (isset($_REQUEST['zone'])) $zone=addslashes($_REQUEST['zone']); else $zone="";
    if (isset($_REQUEST['sort'])) $sort=addslashes($_REQUEST['sort']); else $sort="";
    if (isset($_REQUEST['url'])) $url=addslashes($_REQUEST['url']); else $url="";
	
	$fl=1;$i=2;
	while ($fl==1)
	{
		if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) $pref_id=" AND id<>'".$_REQUEST['id']."'"; else $pref_id="";
		$sql_query="SELECT url FROM ".$sql_pref."_banners WHERE url='".$url."'".$pref_id."";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) {$url.=$i;$i++;}
		else $fl=0;
	}
	
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_banners SET url='".$url."', enable='".$enable."', show_start='".$show_start."', show_end='".$show_end."', sort='".$sort."', descr='".$descr."', zone='".$zone."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=$_REQUEST['id'];
	}
	else
	{
		$sql_query="INSERT INTO ".$sql_pref."_banners (url, enable, show_start, show_end, sort, descr, zone) VALUES ('".$url."', '".$enable."', '".$show_start."', '".$show_end."','".$sort."', '".$descr."', '".$zone."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=mysql_insert_id();
	}
	if (is_uploaded_file( $_FILES['file_name']['tmp_name'])) form_banners_mainimg_save($pic_id);
	
	
}


function form_banners_mainimg_save($pic_id)
{
	global $path;
	global $banners_img_width, $banners_img_height, $banners_img_thumb_width, $banners_img_thumb_height;

	$mime=$_FILES['file_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif")
	{
		if ($mime=="image/jpeg" || $mime=="image/pjpeg") {$ext=".jpg";$ext1=".gif";}
		elseif ($mime=="image/gif") {$ext=".gif";$ext1=".jpg";}

		$dest=$path."files/banners/imgs/".$pic_id.$ext;
		$dest1=$path."files/banners/imgs/".$pic_id.$ext1;
		$src=$_FILES["file_name"]["tmp_name"];
		$resize=true;
		del_file($dest);del_file($dest1);
		save_img($src, $dest, $resize, $banners_img_width, $banners_img_height, 80, $mime);

		$thumb_src=$dest;
		$thumb_dest=$path."files/banners/thumbs/".$pic_id.$ext;
		$thumb_dest1=$path."files/banners/thumbs/".$pic_id.$ext1;
		$thumb_resize=true;
		del_file($thumb_dest);del_file($thumb_dest1);
		save_img($thumb_src, $thumb_dest, $thumb_resize, $banners_img_thumb_width, $banners_img_thumb_height, 80, $mime);
	}
	else echo "Ошибка! Недопустимый формат файла.";
}



function form_banners_images_save()
{
	global $path;

	if (isset($_REQUEST['file_img_name']) && !empty($_REQUEST['file_img_name'])) $filename=$_REQUEST['file_img_name'];
	else $filename=substr($_FILES['file_name']['name'], 0, strpos($_FILES['file_name']['name'], "."));

	$mime=$_FILES['file_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif")
	{
		if ($mime=="image/jpeg" || $mime=="image/pjpeg") $ext=".jpg";
		elseif ($mime=="image/gif") $ext=".gif";

		$dest=$path."files/banners/".$_REQUEST['id']."/".translit_url($filename).$ext;
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

		make_dir($path."files/banners/".$_REQUEST['id']);
		del_file($dest);
		save_img($src, $dest, $resize, @$width, @$height, 80, $mime);
	}
}


?>
