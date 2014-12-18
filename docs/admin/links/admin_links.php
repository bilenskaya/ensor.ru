<?php

// ******************  РАБОТА С РУБРИКАМИ  ******************

function rubs_show()
{
	echo "<h1>Рубрикатор</h1>";
	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
			<tr class='maintitle'>
				<td width='25' class='maintitle' align='center'><b>id</b></td>
				<td width='90' class='maintitle' align='center'>&nbsp;</td>
				<td class='maintitle' align='center'><b>рубрикатор</b></td>
				<td width='120' class='maintitle' align='center'><b>url</b></td>
				<td width='30' class='maintitle' align='center'><b>del</b></td>
			</tr>";
	echo "<tr class='common'>
			<td class='common' align='center' valign='top'><font color='#A0A0A0'>-</font></td>
			<td class='common' align='center' valign='top'>
				<img src='/admin/img/empty.gif' width='25' height='13' border='0'>
				<a href='?parent_id=0&action=rub_add#rub_add'><img src='/admin/img/add_sub.gif' width='25' height='13' alt='Добавить рубрику верхнего уровня' border='0'></a>
				<img src='/admin/img/empty.gif' width='25' height='13' border='0'>
			</td>
			<td class='common' align='left'>
				<b>Каталог</b>
			</td>
			<td class='common' align='center' valign='top'>-</td>
			<td class='common' align='center' valign='top'>-</td>
		</tr>";
	subrubs_show(0);
	echo "</table><br>";
	echo "<hr>";
}


function subrubs_show($par_id)
{
	global $sql_pref, $conn_id, $path;
	$sql_query="SELECT id, url, enable, name, parent_id, code, level FROM ".$sql_pref."_links_rubs WHERE parent_id=".$par_id." ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $url, $enable, $name, $parent_id, $code, $level)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			if (empty($name)) $name="<без заголовка>";
			$name_pic="<a href='?parent_id=".$id."' class='artmenu' title='Список товаров раздела' id='rub".$id."' style='cursor: hand;'>".$name."</a>";
			if ($enable=="Yes") $enable_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=rub_enable'><img src='/admin/img/check_yes.gif' width='25' height='13' alt='Видимость' border='0'></a>"; else $enable_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=rub_enable'><img src='/admin/img/check_no.gif' width='25' height='13' alt='Видимость' border='0'></a>";
			if ($code>1) $sort_up="<a href='?id=".$id."&parent_id=".$parent_id."&action=rub_sort_up'><img src='/admin/img/up.gif' width='11' height='13' alt='Сортировка: Выше' border='0'></a>"; else $sort_up="<img src='/admin/img/sort_none.gif' width='11' height='13' border='0'>";
			$sql_query_1="SELECT id FROM ".$sql_pref."_links_rubs WHERE parent_id='".$parent_id."' AND code='".($code+1)."'";
			$sql_res_1=mysql_query($sql_query_1, $conn_id);
			if (mysql_num_rows($sql_res_1)>0) $sort_down="<a href='?id=".$id."&parent_id=".$parent_id."&action=rub_sort_down'><img src='/admin/img/down.gif' width='11' height='13' alt='Сортировка: Ниже' border='0'></a>"; else $sort_down="<img src='/admin/img/sort_none.gif' width='11' height='13' border='0'>";
			$sql_query_1="SELECT id FROM ".$sql_pref."_links_arts WHERE parent_id='".$id."'";
			$sql_res_1=mysql_query($sql_query_1, $conn_id);
			$art_kol=mysql_num_rows($sql_res_1);
			$sql_query_1="SELECT id FROM ".$sql_pref."_links_rubs WHERE parent_id='".$id."'";
			$sql_res_1=mysql_query($sql_query_1, $conn_id);
			$rub_kol=mysql_num_rows($sql_res_1);
			if ($art_kol==0 && $rub_kol==0) $del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&parent_id=".$parent_id."&action=rub_delete'\"><img src='/admin/img/del.gif' width='25' height='13' alt='Удалить' border='0'></a>";
			else $del="<img src='/admin/img/del_inactive.gif' width='25' height='13' alt='Для удаления рубрики необходимо сначала удалить все подрубрики и статьи этой рубрики' border='0'>";
			if ($level<1) $subrub_pic="<a href='?parent_id=".$id."&level=".$level."&action=rub_add#rub_add'><img src='/admin/img/add_sub.gif' width='25' height='13' alt='Добавить подрубрику' border='0'></a>"; else $subrub_pic="<img src='/admin/img/empty.gif' width='25' height='13' border='0'>";
			$edit_pic="<a href='?id=".$id."&level=".$level."&parent_id=".$parent_id."&action=rub_edit#rub_edit'><img src='/admin/img/edit.gif' width='25' height='13' alt='Редактировать' border='0'></a>";
			if(!isset($_REQUEST['parent_id']) || $_REQUEST['parent_id']!=$id) $artm=""; else $artm=arts_show($id);
			echo "<tr class='common'>
						<td class='common' align='center' valign='top'><font color='#A0A0A0'>".$id."</font></td>
						<td class='common' align='center' valign='top'>".$enable_pic.$subrub_pic.$edit_pic."</td>
						<td class='common' align='left' style='padding-left:".(25*($level-1))."px'>".$sort_down."".$sort_up."".$name_pic."".$artm."</td>
						<td class='common' align='center' valign='top'>".$url."</td>
						<td class='common' align='center' valign='top'>".$del."</td>
					</tr>";
			$parid=array();
			if(isset($_REQUEST['parent_id'])) $parid[0]=$_REQUEST['parent_id']; else $parid[0]=0;
			$i=1;
			while ($parid[($i-1)]!=0)
			{
				$sql_query1="SELECT parent_id FROM ".$sql_pref."_links_rubs WHERE id=".$parid[($i-1)];
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

















function form_rub_save()
{
	global $sql_pref, $conn_id;
	
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (isset($_REQUEST['name'])) $name=$_REQUEST['name']; else $name=""; $name=addslashes($name);
	if (isset($_REQUEST['content']))  $content=addslashes($_REQUEST['content']); else $content="";
	$parent_id=$_REQUEST['parent_id'];
	
	if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) $url=translit_url($_REQUEST['url']);
	elseif (!empty($name)) $url=translit_url($name);
	else $url=date("YmdHi");
	
	$fl=1;$i=2;
	while ($fl==1)
	{
		if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) $pref_id=" AND id<>'".$_REQUEST['id']."'"; else $pref_id="";
		$sql_query="SELECT url FROM ".$sql_pref."_links_rubs WHERE parent_id='".$parent_id."' AND url='".$url."'".$pref_id."";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) {$url.=$i;$i++;}
		else $fl=0;
	}
	
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_links_rubs SET url='".$url."', enable='".$enable."', name='".$name."', content='".$content."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
	else
	{
		$sql_query="SELECT id FROM ".$sql_pref."_links_rubs WHERE parent_id='".$parent_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$code=mysql_num_rows($sql_res)+1;
		$level=$_REQUEST['level']+1;
		$sql_query="INSERT INTO ".$sql_pref."_links_rubs (enable, name, content, parent_id, code, level) VALUES ('".$enable."', '".$name."', '".$content."', '".$parent_id."', '".$code."', '".$level."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$sql_query="UPDATE ".$sql_pref."_links_rubs SET url='".$url."' WHERE id='".mysql_insert_id()."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
}








function form_rub_img_save()
{
	global $path;
	global $links_rub_img_width, $links_rub_img_height;
	
	$mime=$_FILES['file_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg")
	{
		$ext=".jpg";
		
		$dest=$path."files/links/rubs/".$_REQUEST['id'].$ext;
		$src=$_FILES["file_name"]["tmp_name"];
		$resize=true;
		del_file($dest);
		save_img($src, $dest, $resize, $links_rub_img_width, $links_rub_img_height, 80, $mime);
	}
	else echo "Ошибка! Недопустимый формат файла.";

}















function arts_show($parent_id)
{
	global $sql_pref, $conn_id, $path;
	$out="";
	$out.="<div id='rub".$parent_id."details' style='position: relative;'>";
	$out.="<table class='main' cellspacing='2' cellpadding='2' width='100%'>";
	$sql_query="SELECT id, link, enable, name, code, backlink FROM ".$sql_pref."_links_arts WHERE parent_id='".$parent_id."' ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $link, $enable, $name, $code, $backlink)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			if (empty($name)) $name="<без заголовка>";
			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_enable'><img src='/admin/img/check_yes.gif' width='25' height='13' alt='Видимость' border='0'></a>"; else $enable_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_enable'><img src='/admin/img/check_no.gif' width='25' height='13' alt='Видимость' border='0'></a>";
			if ($code>1) $sort_up="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_sort_up'><img src='/admin/img/up.gif' width='11' height='13' alt='Сортировка: Выше' border='0'></a>"; else $sort_up="<img src='/admin/img/sort_none.gif' width='11' height='13' border='0'>";
			$sql_query_1="SELECT id FROM ".$sql_pref."_links_arts WHERE parent_id='".$parent_id."' AND code='".($code+1)."'";
			$sql_res_1=mysql_query($sql_query_1, $conn_id);
			if (mysql_num_rows($sql_res_1)>0) $sort_down="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_sort_down'><img src='/admin/img/down.gif' width='11' height='13' alt='Сортировка: Ниже' border='0'></a>"; else $sort_down="<img src='/admin/img/sort_none.gif' width='11' height='13' border='0'>";
			if ($code>1) $top_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_sort_top'><img src='/admin/img/top2.gif' width='11' height='13' alt='Сортировка: в начало списка' border='0'></a>"; else $top_pic="<img src='/admin/img/empty.gif' width='11' height='13' border='0'>";
			if (mysql_num_rows($sql_res_1)>0) $bottom_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_sort_bottom'><img src='/admin/img/bottom2.gif' width='11' height='13' alt='Сортировка: в конец списка' border='0'></a>"; else $bottom_pic="<img src='/admin/img/empty.gif' width='11' height='13' border='0'>";
			
			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&parent_id=".$parent_id."&action=art_delete'\"><img src='/admin/img/del.gif' width='25' height='13' alt='Удалить' border='0'></a>";
			
			
			$edit_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_edit#art_edit'><img src='/admin/img/edit.gif' width='25' height='13' alt='Редактировать' border='0'></a>";
			$move_pic="<a href='?id=".$id."&parent_id=".$parent_id."&action=art_move#art_move'><img src='/admin/img/move.gif' width='25' height='13' alt='Переместить' border='0'></a>";
			$out.="<tr class='common'>
						<td width='20' class='common' align='center' bgcolor='#EDECEC'><font color='#A0A0A0'>".$id."</font></td>
						<td width='100' class='common' align='center' bgcolor='#EDECEC'>".$enable_pic.$move_pic.$edit_pic."</td>
						<td class='common' align='left' bgcolor='#EDECEC'>".$bottom_pic.$top_pic."&nbsp;&nbsp;".$sort_down.$sort_up.$name."</td>
						<td width='350' class='common' align='center' bgcolor='#EDECEC'>".$link."<br><a href='".$backlink."' target=_blank style='font-size:10px;color:#999999;'>".$backlink."</a></td>
						<td width='50' class='common' align='center' bgcolor='#EDECEC'>".$del."</td>
					</tr>";
		}
	}
	$out.="<tr><td colspan='5' bgcolor='#EDECEC'>&nbsp;<a href='?parent_id=".$parent_id."&action=art_add#art_add'>Добавить</a></td></tr>";
	$out.="</table></div>";
	return ($out);
}

















function form_art_save()
{
	global $sql_pref, $conn_id;

	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (!isset($_REQUEST['special']) || $_REQUEST['special']!="Yes") $special="No"; else $special="Yes";
	if (isset($_REQUEST['name'])) $name=$_REQUEST['name']; else $name=""; $name=addslashes($name);
	if (isset($_REQUEST['link'])) $link=addslashes($_REQUEST['link']); else $link="";
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
	if (isset($_REQUEST['button'])) $button=addslashes($_REQUEST['button']); else $button="";
	$parent_id=$_REQUEST['parent_id'];

	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_links_arts SET link='".$link."', enable='".$enable."', name='".$name."', descr='".$descr."', button='".$button."', special='".$special."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
	else
	{
		$sql_query="SELECT id FROM ".$sql_pref."_links_arts WHERE parent_id='".$parent_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$code=mysql_num_rows($sql_res)+1;
		$sql_query="INSERT INTO ".$sql_pref."_links_arts (enable, name, link, descr, button, parent_id, code, special) VALUES ('".$enable."', '".$name."', '".$link."', '".$descr."', '".$button."', '".$parent_id."', '".$code."', '".$special."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		echo mysql_error();
	}
}








function form_art_move_save()
{
	global $sql_pref, $conn_id;
	
	$dest_rub_id=$_REQUEST['new_rub'];
	
	$sql_query="SELECT id FROM ".$sql_pref."_links_arts WHERE parent_id='".$dest_rub_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$code=mysql_num_rows($sql_res)+1;
	
	$sql_query="UPDATE ".$sql_pref."_links_arts SET code='".$code."', parent_id='".$dest_rub_id."' WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	
	resort("links_arts", $_REQUEST['parent_id']);
}























?>
