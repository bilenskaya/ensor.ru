<?php

function banners_show()
{
	global $sql_pref, $conn_id, $path;
	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
			<tr class='maintitle'>
				<td width='20' class='maintitle' align='center'><b>id</b></td>
				<td width='80' class='maintitle' align='center'>&nbsp;</td>
				<td class='maintitle' align='left'><b>url</b></td>
				<td width='200' class='maintitle' align='center'><b>zone</b></td>
				<td width='200' class='maintitle' align='center'><b>sort</b></td>
				<td width='30' class='maintitle' align='center'><b>del</b></td>
			</tr>";

	$sql_query="SELECT id, url, enable, sort, zone FROM ".$sql_pref."_banners ORDER BY id";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $url, $enable, $sort, $zone)=mysql_fetch_row($sql_res))
		{
			$url=stripslashes($url);

			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=banners_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=banners_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение' border=0></a>";

            if (is_dir($path."files/banners/".$id) && is_cat_empty($path."files/banners/".$id)==false)
                $imgs="<a href='?id=".$id."&action=banners_images#banners_images'><img src='/admin/img/img.gif' width=25 height=13 alt='Дополнительные изображения' border=0></a>";
                else $imgs="<a href='?id=".$id."&action=banners_images#banners_images'><img src='/admin/img/img_ina.gif' width=25 height=13 alt='Дополнительные изображения' border=0></a>";


            $edit_pic="<a href='?id=".$id."&action=banners_edit#banners_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";


            $del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&action=banners_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$enable_pic.$imgs.$edit_pic."</td>
					<td class='common' align='left'>".$url."</td>
					<td class='common' align='center'>".$zone."</td>
					<td class='common' align='center'>".$sort."</td>
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
	if (isset($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
	if (isset($_REQUEST['FCKeditor1'])) $content=addslashes($_REQUEST['FCKeditor1']); else $content="";
	if (isset($_REQUEST['xc2_dt']) && $_REQUEST['date']=="Yes") $dt=$_REQUEST['xc2_dt']." ".$_REQUEST['time']; else $dt="0000-00-00 00:00";
	if (isset($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
	if (isset($_REQUEST['category']) && !empty($_REQUEST['category'])) $category=addslashes($_REQUEST['category']); else $category=addslashes($_REQUEST['category_new']);
	

	if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) $url=translit_url($_REQUEST['url']);
	elseif (!empty($name)) $url=translit_url($name);
	else $url=date("YmdHi");
	
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
		$sql_query="UPDATE ".$sql_pref."_banners SET url='".$url."', enable='".$enable."', dt='".$dt."', name='".$name."', descr='".$descr."', content='".$content."', tags='".$tags."', category='".$category."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=$_REQUEST['id'];
	}
	else
	{
		$sql_query="INSERT INTO ".$sql_pref."_banners (url, enable, dt, name, descr, content, tags, category) VALUES ('".$url."', '".$enable."', '".$dt."', '".$name."', '".$descr."', '".$content."', '".$tags."', '".$category."')";
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
