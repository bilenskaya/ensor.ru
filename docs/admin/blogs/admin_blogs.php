<?php

function rubs_show()
{
	if (isset($_REQUEST['parent_id']) && $_REQUEST['parent_id']==0) $artm=arts_show(0); else $artm="";
	echo "<table class=main cellspacing=2 cellpadding=2 width=100%>
			<tr class=maintitle>
				<td width=20 class=maintitle align=center><b>id</b></td>
				<td width=100 class=maintitle align=center>&nbsp;</td>
				<td class=maintitle align=center><b>рубрикатор</b></td>
				<td width=80 class=maintitle align=center><b>url</b></td>
				<td width=30 class=maintitle align=center><b>del</b></td>
			</tr>";
		echo "<tr class=common>
				<td class=common align=center><font color='#A0A0A0'>0</font></td>
				<td class=common align=center>
					<img src='/admin/img/empty.gif' width=25 height=13 border=0>
					<a href='?parent_id=0&action=rub_add#rub_add'><img src='/admin/img/add_sub.gif' width=25 height=13 alt='Добавить рубрику верхнего уровня' border=0></a>
					<img src='/admin/img/empty.gif' width=25 height=13 border=0>
				</td>
				<td class=common align=left>
					<a href='?parent_id=0'><b><font color='#0000DD'>Главная страница</font></b></a>".$artm."
				</td>
				<td class=common align=center>-</td>
				<td class=common align=center>-</td>
			</tr>";
	subrubs_show(0);
	echo "</table><br>";
}


function subrubs_show($par_id)
{
	global $sql_pref, $conn_id, $path;
	$sql_query="SELECT id, url, enable, name, content, parent_id, code, level, module FROM ".$sql_pref."_blogs_rubs WHERE parent_id=".$par_id." ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $url, $enable, $name, $content, $parent_id, $code, $level, $module)=mysql_fetch_row($sql_res))
		{
			if ($name=="divider") echo "<tr valign=top height=10 bgcolor='#ffffff'><td class=common colspan=4><img src='/admin/img/empty.gif' height=10 border=0></td><td class=common align=center><a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&parent_id=".$parent_id."&action=rub_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a></td></tr>";
			else
			{
				$id_show="<font color='#A0A0A0'>".$id."</font>";
				$name=stripslashes($name); if (empty($name)) $name="<без заголовка>";
				if (empty($module)) $name_show="<a class=artmenu href='?parent_id=".$id."' title='Список страниц раздела' id='rub".$id."' style='cursor: hand;'>".$name."</a>";
				else { $name_show="<span style='color: gray;'><b>".$name."</b></span>"; if (is_dir($path."/admin/".$module)) $name_show.="&nbsp;<a href='/admin/".$module."/' title='Перейти к управлению разделом' style='color: gray;'>&raquo;</a>"; }
				if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=rub_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение раздела' border=0></a>"; else $enable_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=rub_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение раздела' border=0></a>";
				if (empty($module) && $level<4) $add_sub="<a href='?parent_id=".$id."&level=".$level."&action=rub_add#rub_add' title='Добавить подрубрику'><img src='/admin/img/add_sub.gif' width=25 height=13 alt='Добавить подрубрику' border=0></a>";
				else $add_sub="<img src='/admin/img/empty.gif' width=25 height=13 border=0>";
				if ($code>1) $sort_up="<a href='?id=".$id."&parent_id=".$parent_id."&action=rub_sort_up'><img src='/admin/img/up.gif' width='11' height=13 alt='Сортировка: Выше' border=0></a>"; else $sort_up="<img src='/admin/img/sort_none.gif' width='11' height=13 border=0>";
				$sql_query_1="SELECT id FROM ".$sql_pref."_blogs_rubs WHERE parent_id='".$parent_id."' AND code='".($code+1)."'"; $sql_res_1=mysql_query($sql_query_1, $conn_id); 
				if (mysql_num_rows($sql_res_1)>0) $sort_down="<a href='?id=".$id."&parent_id=".$parent_id."&action=rub_sort_down'><img src='/admin/img/down.gif' width='11' height=13 alt='Сортировка: Ниже' border=0></a>"; else $sort_down="<img src='/admin/img/sort_none.gif' width='11' height=13 border=0>";
				$sql_query_1="SELECT id FROM ".$sql_pref."_blogs_posts WHERE parent_id='".$id."'";
				$sql_res_1=mysql_query($sql_query_1, $conn_id);
				$art_kol=mysql_num_rows($sql_res_1);
				$sql_query_1="SELECT id FROM ".$sql_pref."_blogs_rubs WHERE parent_id='".$id."'";
				$sql_res_1=mysql_query($sql_query_1, $conn_id);
				$rub_kol=mysql_num_rows($sql_res_1);
				if ($art_kol==0 && $rub_kol==0 && empty($module)) $del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&parent_id=".$parent_id."&action=rub_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
				else $del="<img src='/admin/img/del_inactive.gif' width=25 height=13 alt='Для удаления рубрики необходимо сначала удалить все подрубрики и статьи этой рубрики' border=0>";
				$edit_pic="<a href='?id=".$id."&level=".$level."&parent_id=".$parent_id."&action=rub_edit#rub_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
				if(!empty($module) || !isset($_REQUEST['parent_id']) || $_REQUEST['parent_id']!=$id) $artm=""; else $artm=arts_show($id);
				echo "<tr valign=top class=common>
							<td class=common align=center>".$id_show."</td>
							<td class=common align=center>".$enable_pic.$add_sub.$edit_pic."</td>
							<td class=common align=left style='padding-left:".(25*($level-1))."px'>".$sort_down.$sort_up.$name_show.$artm."</td>
							<td class=common align=center>".$url."</td>
							<td class=common align=center>".$del."</td>
						</tr>";
				
				$parid=array();
				if(isset($_REQUEST['parent_id'])) $parid[0]=$_REQUEST['parent_id']; else $parid[0]=0;
				$i=1;
				while ($parid[($i-1)]!=0)
				{
					$sql_query1="SELECT parent_id FROM ".$sql_pref."_blogs_rubs WHERE id=".$parid[($i-1)];
					$sql_res1=mysql_query($sql_query1, $conn_id);
					list($parid[$i])=mysql_fetch_row($sql_res1);
					$i++;
				}
				$i=0;
				while (isset($parid[$i]))
				{
					if ($id==$parid[$i]) subrubs_show($id);
					$i++;
				} 
			}
		}
	}
}










function form_rub_save()
{
	global $sql_pref, $conn_id;
	
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (!isset($_REQUEST['auth']) || $_REQUEST['auth']!="Yes") $auth="No"; else $auth="Yes";
	if (isset($_REQUEST['name'])) $name=$_REQUEST['name']; else $name=""; $name=addslashes($name);
	if (isset($_REQUEST['module'])) $module=$_REQUEST['module']; else $module="";
	if (isset($_REQUEST['content']))  $content=addslashes($_REQUEST['content']); else $content="";
	if (isset($_REQUEST['FCKeditor1'])) $adv_content=addslashes($_REQUEST['FCKeditor1']); else $adv_content="";
	
    $parent_id=$_REQUEST['parent_id'];
	
	if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) $url=translit_url($_REQUEST['url']);
	elseif (!empty($name)) $url=translit_url($name);
	else $url=date("YmdHi");
	
	$fl=1;$i=2;
	while ($fl==1)
	{
		if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) $pref_id=" AND id<>'".$_REQUEST['id']."'"; else $pref_id="";
		$sql_query="SELECT url FROM ".$sql_pref."_blogs_rubs WHERE parent_id='".$parent_id."' AND url='".$url."'".$pref_id."";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) {$url.=$i;$i++;}
		else $fl=0;
	}
	
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_blogs_rubs SET url='".$url."', enable='".$enable."', name='".$name."', content='".$content."', auth='".$auth."', module='".$module."', adv_content='".$adv_content."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
	else
	{
		$sql_query="SELECT id FROM ".$sql_pref."_blogs_rubs WHERE parent_id='".$parent_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$code=mysql_num_rows($sql_res)+1;
		$level=$_REQUEST['level']+1;
		$sql_query="INSERT INTO ".$sql_pref."_blogs_rubs (enable, url, name, content, parent_id, code, level, auth, module) VALUES ('".$enable."', '".$url."', '".$name."', '".$content."', '".$parent_id."', '".$code."', '".$level."', '".$auth."', '".$module."')";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
}










function arts_show($parent_id)
{
	global $sql_pref, $conn_id, $path;
	global $use_comments;
	$artm="<div id='rub".$parent_id."details' style='padding-left:0'>";
	$artm.="<table class=main cellspacing=2 cellpadding=2 width=100%>";
	$sql_query="SELECT id, url, enable, name, code FROM ".$sql_pref."_blogs_posts WHERE parent_id=".$parent_id." ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $url, $enable, $name, $code)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			if (empty($name)) $name_show="<i><без названия></i>"; else $name_show=$name;
			if ($code==1) $name_show="<b>".$name."</b>";
			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Видимость страницы на сайте' border=0></a>"; else $enable_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Видимость страницы на сайте' border=0></a>";
			if ($code>1) $sort_up="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_sort_up'><img src='/admin/img/up.gif' width=11 height=13 alt='Сортировка: Выше' border=0></a>"; else $sort_up="<img src='/admin/img/sort_none.gif' width=11 height=13 border=0>";
			$sql_query_1="SELECT id FROM ".$sql_pref."_blogs_posts WHERE parent_id='".$parent_id."' AND code='".($code+1)."'";	$sql_res_1=mysql_query($sql_query_1, $conn_id);
			if (mysql_num_rows($sql_res_1)>0) $sort_down="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_sort_down'><img src='/admin/img/down.gif' width=11 height=13 alt='Сортировка: Ниже' border=0></a>"; else $sort_down="<img src='/admin/img/sort_none.gif' width=11 height=13 border=0>";
			$move_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_move#art_move'><img src='/admin/img/move.gif' width=25 height=13 alt='Переместить' border=0></a>";
			$files_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_files#art_files'><img src='/admin/img/files.gif' width=25 height=13 alt='Файлы' border=0></a>";
			if (is_dir($path."files/pubs/imgs/".$id)) $imga="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_img#art_img'><img src='/admin/img/img.gif' width=25 height=13 alt='Изображение' border=0></a>"; else $imga="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_img#art_img'><img src='/admin/img/img_ina.gif' width=25 height=13 alt='Изображение' border=0></a>";
			if (!is_dir($path."files/pubs/files/".$id) && !is_dir($path."files/pubs/imgs/".$id)) $del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&parent_id=".$parent_id."&action=art_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			else $del="<img src='/admin/img/del_inactive.gif' width=25 height=13 alt='Для удаления статьи необходимо сначала удалить все файлы и изображения этой статьи' border=0>";
			$edit_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_edit#art_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			$artm.="<tr class=common>
						<td width=20 class=common align=center bgcolor=#EDECEC><font color=#A0A0A0>".$id."</font></td>
						<td width=140 class=common align=center bgcolor=#EDECEC>".$enable_pic.$move_pic.$imga.$files_pic.$edit_pic."</td>
						<td class=common align=left bgcolor=#EDECEC>".$sort_down.$sort_up.$name_show."</td>
						<td width=50 class=common align=center bgcolor=#EDECEC>".$del."</td>
					</tr>";
		}
	}
	if ($parent_id==0)
	{
		$sql_query="SELECT name FROM ".$sql_pref."_blogs_posts WHERE id='1'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)==0) $artm.="<tr class=common><td colspan=4 bgcolor='#EDECEC'>&nbsp;<a href='?parent_id=".$parent_id."&action=art_add#art_add'>Создать страницу</a></td></tr>";
	}
	else $artm.="<tr class=common><td colspan=4 bgcolor='#EDECEC'>&nbsp;<a href='?parent_id=".$parent_id."&action=art_add#art_add'>Создать страницу</a></td></tr>";
	$artm.="</table></div>";
	return ($artm);
}










function form_art_save()
{
	global $sql_pref, $conn_id;

	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (isset($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
	if (isset($_REQUEST['title'])) $title=addslashes($_REQUEST['title']); else $title="";
	if (isset($_REQUEST['FCKeditor1'])) $content=addslashes($_REQUEST['FCKeditor1']); else $content="";
	$parent_id=$_REQUEST['parent_id'];

	if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) $url=translit_url($_REQUEST['url']);
	elseif (!empty($name)) $url=translit_url($name);
	else $url=date("YmdHi");

	$fl=1;$i=2;
	while ($fl==1)
	{
		if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) $pref_id=" AND id<>'".$_REQUEST['id']."'"; else $pref_id="";
		$sql_query="SELECT url FROM ".$sql_pref."_blogs_posts WHERE parent_id='".$parent_id."' AND url='".$url."'".$pref_id."";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) {$url.=$i;$i++;}
		else $fl=0;
	}

	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_blogs_posts SET url='".$url."', enable='".$enable."', name='".$name."', descr='".$descr."', content='".$content."', title='".$title."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
	else
	{
		$sql_query="SELECT id FROM ".$sql_pref."_blogs_posts WHERE parent_id='".$parent_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$code=mysql_num_rows($sql_res)+1;
		$sql_query="INSERT INTO ".$sql_pref."_blogs_posts (enable, url, name, descr, content, parent_id, code, title) VALUES ('".$enable."', '".$url."', '".$name."', '".$descr."', '".$content."', '".$parent_id."', '".$code."', '".$title."')";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
}










function form_art_img_save()
{
	global $path;
	
	if (isset($_REQUEST['file_img_name']) && !empty($_REQUEST['file_img_name'])) $filename=$_REQUEST['file_img_name']; 
	else $filename=substr($_FILES['file_name']['name'], 0, strpos($_FILES['file_name']['name'], "."));
	
	$mime=$_FILES['file_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif")
	{
		if ($mime=="image/jpeg" || $mime=="image/pjpeg") $ext=".jpg";
		elseif ($mime=="image/gif") $ext=".gif";
		
		$dest=$path."files/pubs/imgs/".$_REQUEST['id']."/".translit_url($filename).$ext;
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
				if (empty($height)) $height=$size[1];
				if (@$width<$size[0] || @$height<$size[1])
				{
					if (@$width<$size[0] && @$height>=$size[1]) {$height=($width*$size[1])/$size[0]; $width=$size[0];}
					elseif (@$width>=$size[0] && @$height<$size[1]) {$width=($height*$size[0])/$size[1];$height=$size[1];}
					$resize=true; 
				}
			}
		}
		make_dir($path."files/pubs/imgs/".$_REQUEST['id']);
		del_file($dest); 
		save_img($src, $dest, $resize, @$width, @$height, 80, $mime);
	}
	else echo "Ошибка! Недопустимый формат файла.";
}










function form_art_files_save()
{
	global $path;
	
	if (isset($_REQUEST['file_file_name']) && !empty($_REQUEST['file_file_name'])) 
	{
		$file_name=$_REQUEST['file_file_name'];
		$file_ext=substr($_FILES['file_name']['name'], strrpos($_FILES['file_name']['name'], ".")+1);
	}
	else 
	{
		$file_name=substr($_FILES['file_name']['name'], 0, strrpos($_FILES['file_name']['name'], "."));
		$file_ext=substr($_FILES['file_name']['name'], strrpos($_FILES['file_name']['name'], ".")+1);
	}
	
	$file_name=translit_url($file_name);
	
	$dest=$path."files/pubs/files/".$_REQUEST['id']."/".$file_name.".".$file_ext;
	$src=$_FILES["file_name"]["tmp_name"];
	
	make_dir($path."files/pubs/files/".$_REQUEST['id']);
	del_file($dest);
	save_file($src, $dest);
}










function form_art_move_save()
{
	global $sql_pref, $conn_id;
	
	$dest_rub_id=$_REQUEST['new_rub'];
	
  $sql_query="SELECT url FROM ".$sql_pref."_blogs_posts WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($url)=mysql_fetch_row($sql_res);
	
  $sql_query="SELECT id FROM ".$sql_pref."_blogs_posts WHERE parent_id='".$dest_rub_id."' AND url='".$url."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($d_id)=mysql_fetch_row($sql_res);
		$url=$url."_".$_REQUEST['id'];
	}

	$sql_query="SELECT id FROM ".$sql_pref."_blogs_posts WHERE parent_id='".$dest_rub_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$code=mysql_num_rows($sql_res)+1;
	
	$sql_query="UPDATE ".$sql_pref."_blogs_posts SET code='".$code."', parent_id='".$dest_rub_id."', url='".$url."' WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	
	resort("blogs_posts", $_REQUEST['parent_id']);
}












function form_styles_save()
{
	global $sql_pref, $conn_id, $path;
	
	if (isset($_REQUEST['content']))  $content=$_REQUEST['content']; else $content="";
	$filename = $path."styles.css";
	$handle=fopen($filename, 'w');
	if (fwrite($handle, $content) === FALSE) 
	{
		echo "Ошибка записи в файл";
		exit;
	}
	fclose($handle);
}


?>
