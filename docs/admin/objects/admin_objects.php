<?php

function objects_show()
{
	global $sql_pref, $conn_id, $path;
    
    
	if (isset($_REQUEST['letter']) && !empty($_REQUEST['letter'])) $curletter=$_REQUEST['letter'];
	$sql_query="SELECT SUBSTRING(name,1,1) FROM ".$sql_pref."_objects GROUP BY SUBSTRING(name,1,1)";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		echo "<div align='left' style='padding: 0 0 0 0;'><table cellpadding=3 cellspacing=0 border='0'><tr height=25>";
		while(list($letter)=mysql_fetch_row($sql_res))
		{
			$letter=strtoupper($letter);
			if (isset($curletter) && rawurldecode($curletter)==$letter) $letter_show="<span style='border:solid 1px gray; padding:2 2 2 2;background-color:#eeeeee;'><b>".$letter."</b></span>"; else $letter_show=$letter;
//			else $out.="<td align='center' valign='middle'><nobr><a href='/".$pref."".$catalog_path."".$catalog_rub_url[0]."/".rawurlencode($letter)."/'><u> ".$letter." </u></a></nobr></td>";
			echo "<td align='center' valign='middle'><nobr><a href='?letter=".rawurlencode($letter)."'> ".$letter_show." </a></nobr></td>";
		}
		echo "</tr></table></div><br>";
	}

	if (isset($curletter) && !empty($curletter)) 
    {
        
        
    	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
    			<tr class='maintitle'>
    				<td width='20' class='maintitle' align='center'><b>id</b></td>
    				<td width='80' class='maintitle' align='center'>&nbsp;</td>
    				<td class='maintitle' align='left'><b>название</b></td>
    				<td width='30' class='maintitle' align='center'><b>del</b></td>
    			</tr>";
    
    	$sql_query="SELECT id, enable, name FROM ".$sql_pref."_objects WHERE SUBSTRING(name,1,1)='".rawurldecode($curletter)."' ORDER BY name";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
    	{
    		while (list($id, $enable, $name)=mysql_fetch_row($sql_res))
    		{
    			$name=stripslashes($name);
    			if (empty($name)) $name="<i><без заголовка></i>";
    			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&letter=".@$curletter."&action=objects_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение' border=0></a>"; else $enable_pic="<a href='?id=".$id."&letter=".@$curletter."&action=objects_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение' border=0></a>";
    			if (is_dir($path."files/objects/".$id) && is_cat_empty($path."files/objects/".$id)==false)  $imgs="<a href='?id=".$id."&letter=".@$curletter."&action=objects_images#objects_images'><img src='/admin/img/img.gif' width=25 height=13 alt='Дополнительные изображения' border=0></a>"; else $imgs="<a href='?id=".$id."&letter=".@$curletter."&action=objects_images#objects_images'><img src='/admin/img/img_ina.gif' width=25 height=13 alt='Дополнительные изображения' border=0></a>";
    			$edit_pic="<a href='?id=".$id."&letter=".@$curletter."&action=objects_edit#objects_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
    			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&letter=".@$curletter."&action=objects_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
    			echo "<tr class='common'>
    					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
    					<td class='common' align='center'>".$enable_pic.$imgs.$edit_pic."</td>
    					<td class='common' align='left'>".$name."</td>
    					<td class='common' align='center'>".$del."</td>
    				</tr>";
    		}
    	}
    	echo "</table>";
    	echo "<br><li><a href='?action=objects_add#objects_add'>Добавить</a></li>";
    	echo "<br><li><a href='?action=directions_show#directions'>Направления</a></li>";
    	echo "<li><a href='?action=sfery_show#sfery'>Сферы</a></li>";
        echo "<br>";
    }
}










function form_objects_save()
{
	global $sql_pref, $conn_id;
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (isset($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
	if (isset($_REQUEST['name_full'])) $name_full=addslashes($_REQUEST['name_full']); else $name_full="";
	if (isset($_REQUEST['city_id'])) $city_id=addslashes($_REQUEST['city_id']); else $city_id=0;
	if (isset($_REQUEST['parent_id'])) $parent_id=addslashes($_REQUEST['parent_id']); else $parent_id=0;
	if (isset($_REQUEST['company_id'])) $company_id=addslashes($_REQUEST['company_id']); else $company_id=0;
	if (isset($_REQUEST['address'])) $address=addslashes($_REQUEST['address']); else $address="";
	if (isset($_REQUEST['phone1'])) $phone1=addslashes($_REQUEST['phone1']); else $phone1="";
	if (isset($_REQUEST['phone2'])) $phone2=addslashes($_REQUEST['phone2']); else $phone2="";
	if (isset($_REQUEST['fax'])) $fax=addslashes($_REQUEST['fax']); else $fax="";
	if (isset($_REQUEST['email'])) $email=addslashes($_REQUEST['email']); else $email="";
	if (isset($_REQUEST['site'])) $site=addslashes($_REQUEST['site']); else $site="";
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
	if (isset($_REQUEST['FCKeditor1'])) $content=addslashes($_REQUEST['FCKeditor1']); else $content="";
	if (isset($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
	
    $sfera_ids="";
    $sql_query="SELECT id FROM ".$sql_pref."_sd_sfery ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	while (list($s_id)=mysql_fetch_row($sql_res))
	{
       	if (@$_REQUEST['sfery_'.$s_id]=="Yes") $sfera_elem[]=$s_id;
    }
    if (isset($sfera_elem)) $sfera_ids=implode(";",$sfera_elem);
    
    $direction_ids="";
    $sql_query="SELECT id FROM ".$sql_pref."_sd_directions ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	while (list($d_id)=mysql_fetch_row($sql_res))
	{
       	if (@$_REQUEST['direction_'.$d_id]=="Yes") $direction_elem[]=$d_id;
    }
    if (isset($direction_elem)) $direction_ids=implode(";",$direction_elem);
	
	
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_objects SET name='".$name."', name_full='".$name_full."', parent_id='".$parent_id."', city_id='".$city_id."', company_id='".$company_id."', address='".$address."', phone1='".$phone1."', phone2='".$phone2."', fax='".$fax."', email='".$email."', site='".$site."', descr='".$descr."', content='".$content."', sfera_ids='".$sfera_ids."', direction_ids='".$direction_ids."', tags='".$tags."', enable='".$enable."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=$_REQUEST['id'];
	}
	else
	{
		$sql_query="INSERT INTO ".$sql_pref."_objects (name, name_full, parent_id, city_id, company_id, address, phone1, phone2, fax, email, site, descr, content, sfera_ids, direction_ids, tags, enable) VALUES ('".$name."', '".$name_full."', '".$parent_id."', '".$city_id."', '".$company_id."', '".$address."', '".$phone1."', '".$phone2."', '".$fax."', '".$email."', '".$site."', '".$descr."', '".$content."', '".$sfera_ids."', '".$direction_ids."', '".$tags."', '".$enable."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=mysql_insert_id();
	}
	if (is_uploaded_file( $_FILES['file_name']['tmp_name'])) form_objects_mainimg_save($pic_id);
	
	
}










function form_objects_mainimg_save($pic_id)
{
	global $path;
	global $objects_img_width, $objects_img_height, $objects_img_thumb_width, $objects_img_thumb_height;

	$mime=$_FILES['file_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif")
	{
		if ($mime=="image/jpeg" || $mime=="image/pjpeg") {$ext=".jpg";$ext1=".gif";}
		elseif ($mime=="image/gif") {$ext=".gif";$ext1=".jpg";}

		$dest=$path."files/objects/imgs/".$pic_id.$ext;
		$dest1=$path."files/objects/imgs/".$pic_id.$ext1;
		$src=$_FILES["file_name"]["tmp_name"];
		$resize=true;
		del_file($dest);del_file($dest1);
		save_img($src, $dest, $resize, $objects_img_width, $objects_img_height, 80, $mime);

		$thumb_src=$dest;
		$thumb_dest=$path."files/objects/thumbs/".$pic_id.$ext;
		$thumb_dest1=$path."files/objects/thumbs/".$pic_id.$ext1;
		$thumb_resize=true;
		del_file($thumb_dest);del_file($thumb_dest1);
		save_img($thumb_src, $thumb_dest, $thumb_resize, $objects_img_thumb_width, $objects_img_thumb_height, 80, $mime);
	}
	else echo "Ошибка! Недопустимый формат файла.";
}










function form_objects_images_save()
{
	global $path;

	if (isset($_REQUEST['file_img_name']) && !empty($_REQUEST['file_img_name'])) $filename=$_REQUEST['file_img_name'];
	else $filename=substr($_FILES['file_name']['name'], 0, strpos($_FILES['file_name']['name'], "."));

	$mime=$_FILES['file_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif")
	{
		if ($mime=="image/jpeg" || $mime=="image/pjpeg") $ext=".jpg";
		elseif ($mime=="image/gif") $ext=".gif";

		$dest=$path."files/objects/".$_REQUEST['id']."/".translit_url($filename).$ext;
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

		make_dir($path."files/objects/".$_REQUEST['id']);
		del_file($dest);
		save_img($src, $dest, $resize, @$width, @$height, 80, $mime);
	}
}











function form_directions_save()
{
	global $sql_pref, $conn_id;
    
    
    $sql_query="SELECT id FROM ".$sql_pref."_sd_directions";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
        
        while(list($id)=mysql_fetch_row($sql_res))
        {
            if (isset($_REQUEST['name_'.$id]) && !empty($_REQUEST['name_'.$id])) $name=addslashes(trim($_REQUEST['name_'.$id])); else $name="";
            if (!empty($name))
            {
        		$sql_query="UPDATE ".$sql_pref."_sd_directions SET name='".$name."' WHERE id='".$id."'";
        		$sql_res_1=mysql_query($sql_query, $conn_id);
            }
        }
    }

    $sql_query="SELECT id FROM ".$sql_pref."_sd_directions";
    $sql_res=mysql_query($sql_query, $conn_id);
    $code=mysql_num_rows($sql_res)+1;
    
	if (isset($_REQUEST['name_new']) && !empty($_REQUEST['name_new'])) $name=addslashes(trim($_REQUEST['name_new'])); else $name="";
    if (!empty($name))
    {
    	$sql_query="INSERT INTO ".$sql_pref."_sd_directions (name, code) VALUES ('".$name."', '".$code."')";
    	$sql_res=mysql_query($sql_query, $conn_id);
    }
    
}





function form_sfery_save()
{
	global $sql_pref, $conn_id;
    
    
    $sql_query="SELECT id FROM ".$sql_pref."_sd_sfery";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
        
        while(list($id)=mysql_fetch_row($sql_res))
        {
            if (isset($_REQUEST['name_'.$id]) && !empty($_REQUEST['name_'.$id])) $name=addslashes(trim($_REQUEST['name_'.$id])); else $name="";
            if (!empty($name))
            {
        		$sql_query="UPDATE ".$sql_pref."_sd_sfery SET name='".$name."' WHERE id='".$id."'";
        		$sql_res_1=mysql_query($sql_query, $conn_id);
            }
        }
    }

    $sql_query="SELECT id FROM ".$sql_pref."_sd_sfery";
    $sql_res=mysql_query($sql_query, $conn_id);
    $code=mysql_num_rows($sql_res)+1;
    
	if (isset($_REQUEST['name_new']) && !empty($_REQUEST['name_new'])) $name=addslashes(trim($_REQUEST['name_new'])); else $name="";
    if (!empty($name))
    {
    	$sql_query="INSERT INTO ".$sql_pref."_sd_sfery (name, code) VALUES ('".$name."', '".$code."')";
    	$sql_res=mysql_query($sql_query, $conn_id);
    }
    
}






?>
