<?php



function gost_main()
{
	global $sql_pref, $conn_id, $art_url, $path_gost;
	$out="";

	if (isset($_REQUEST['action']) AND $_REQUEST['action']=="search")  {$search_string=strip_tags($_REQUEST['search_str']); $out.=show_search($search_string, $art_url);}
	

	elseif (isset($art_url)) 
	{
			$out.="<table width=100%><tr><td align='left'><a href='/".$path_gost."/' class='gost_rubric'>Все рубрики</a></td><td align='right'>".search_panel($art_url)."</td></tr></table>";
		$out.=gost_list($art_url);
	}
	else 
	{
		$out.="<table cellpadding='2' cellspacing='2' width=100%><tr><td align='left'></td><td align='right'></td></tr></table>";
		$out.=gost_rubric();
	}
	
	$out.="<br><a href='javascript:history.back(1)'>««&nbsp;вернуться</a><br><br>";

	
	return ($out);
}





function check_count($rub_id)
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT file FROM ".$sql_pref."_gost_rub WHERE id='$rub_id'";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($file)=mysql_fetch_row($sql_res);
	$tab_name=$sql_pref."_gost_".str_replace(".", "_", basename($file));
	$sql_query_gost="SELECT id FROM ".$tab_name;
	$sql_res_gost=mysql_query($sql_query_gost, $conn_id);
	if (mysql_num_rows($sql_res_gost)>0) return (mysql_num_rows($sql_res_gost));
	else return 0;
	}



function gost_rub_name($rub_id)
{
	global $sql_pref, $conn_id, $path_gost;
	$out="";
    $sql_query="SELECT id, name FROM ".$sql_pref."_gost_rub WHERE (enable='Yes' and id='".$rub_id."')";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0) list($id, $name)=mysql_fetch_row($sql_res);
	$name=stripslashes($name);
	$out.=$name;
return ($out);
}


function search_panel($id)
{
$out="<form name='form_name' action='?' method='post' enctype='multipart/form-data'>
<input type='hidden' name='action' value='search'>
<input type='hidden' name='id' value='".$id."'>
<table cellpadding='1' cellspacing='1' border='0' bgcolor='#FFFFFF'>
	<tr><td colspan='2'>Поиск документации</td></tr>
	<tr>
		<td><input type='text' name='search_str'></td>
		<td><input type='submit' name='button_submit' value='Найти'></td>
	</tr>
</table>
</form>";
//$out="";
return ($out);
}




function gost_rubric()
{
	global $sql_pref, $conn_id, $path, $path_gost, $page_header1, $page_title;

    $sql_query="SELECT id, name FROM ".$sql_pref."_gost_rub WHERE (enable='Yes') ORDER BY name";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
	$out.="<table cellpadding='2' cellspacing='0' border='0' width=100%>";
		while(list($id, $name)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			$count=check_count($id);
			$name_show="<a href='/".$path_gost."/".$id.".html' class='gost_rubric'>".$name."</a> (".$count.")";
			$out.="<tr><td valign=middle align=left>".$name_show."</td></tr>";
		}
	$out.="</table>";
	}
	return ($out);
}



function gost_list($rub_id)
{
	global $sql_pref, $conn_id, $path, $gost_perpage;
	global $page_title, $page_header1;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$gost_perpage).",".$gost_perpage."";

$page_header1="Нормативная документация - ".gost_rub_name($rub_id);
$page_title=$page_header1;

	

	$sql_query="SELECT id, name, fields, rus_fields, file FROM ".$sql_pref."_gost_rub WHERE id='$rub_id'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $name, $fields, $rus_fields, $file)=mysql_fetch_row($sql_res);

		$fields=unserialize($fields);
		$rus_fields=unserialize($rus_fields);
		$select_fields="";
		if(is_array($fields)) foreach($fields as $k=>$v) if($v==1) $fields_to_show[$k]=$rus_fields[$k];
		if(is_array($fields_to_show)) foreach($fields_to_show as $k=>$v) {$select_fields.=$k.", ";}
		$select_fields=substr($select_fields, 0, strlen($select_fields)-2);
		
		$file=$path.$file;
		$tab_name=$sql_pref."_gost_".str_replace(".", "_", basename($file));
		
		
		$perpage=$gost_perpage;
		if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
		$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";
		
		
		$sql_query_gost="SELECT id, ".$select_fields." FROM ".$tab_name." ORDER BY id".$pref_page;
		$sql_res_gost=mysql_query($sql_query_gost, $conn_id);


		$out.="<table cellpadding='5' cellspacing='0' border='0' width=100%><tr>";
		$rus_fields['id']="№";
		for ($i=0; $i < mysql_num_fields($sql_res_gost); $i++)
		{
		$field_name=mysql_field_name($sql_res_gost,$i);
		$out.="<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>".$rus_fields[$field_name]."</b></td>";
		}
		$out.="</tr>";
		
		if (mysql_num_rows($sql_res)>0)
		{
			while($array_data=mysql_fetch_row($sql_res_gost))
			{
			$out.="<tr>";
			for ($i=0; $i < mysql_num_fields($sql_res_gost); $i++)
			$out.="<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'>".stripslashes($array_data[$i])."</td>";
			$out.="</tr>";
			}
		}
		else $out.="В данной категории нет документов";
		$out.="</table>";
	}
	$sql_query_page="SELECT id FROM ".$tab_name;
	$sql_res_page=mysql_query($sql_query_page, $conn_id);
	$num_predl=mysql_num_rows($sql_res_page);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		$out.="<br><br><div align=left>Страницы: | ";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show="<a href='?page=".$i."'>".$i."</a>";
			$out.="<span style='padding:2 1 2 1;'>".$i_show."</span> | ";
		}
		$out.="</div><br>";
	}
return($out);
}




function show_search($search_string, $id)
{
	global $sql_pref, $conn_id, $path_gost, $gost_perpage, $page_header1, $page_title;
	
	$page_header1="Нормативная документация - ".gost_rub_name($id);
	$page_title=$page_header1;
	$out.="<table width=100%><tr><td align='left'><a href='/".$path_gost."/".$id.".html' class='gost_rubric'>Все документы</a></td><td align='right'>".search_panel($id)."</td></tr></table>";
	$search_string=trim($search_string);
	if($search_string!=="")
	{

	$sql_query="SELECT id, name, fields, rus_fields, file FROM ".$sql_pref."_gost_rub WHERE id='$id'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $name, $fields, $rus_fields, $file)=mysql_fetch_row($sql_res);

		$fields=unserialize($fields);
		$rus_fields=unserialize($rus_fields);
		$select_fields="";
		$where_fields="";
		if(is_array($fields)) foreach($fields as $k=>$v) if($v==1) $fields_to_show[$k]=$rus_fields[$k];
		if(is_array($fields_to_show)) foreach($fields_to_show as $k=>$v) {$select_fields.=$k.", ";}
		if(is_array($fields_to_show)) foreach($fields_to_show as $k=>$v) {$where_fields.=$k." LIKE '%".$search_string."%' OR ";}
		
		$select_fields=substr($select_fields, 0, strlen($select_fields)-2);
		$where_fields=substr($where_fields, 0, strlen($where_fields)-3);
		$where_fields=" WHERE (".$where_fields.")";
		
		$tab_name=$sql_pref."_gost_".str_replace(".", "_", basename($file));

		$perpage=$gost_perpage;
		if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
		$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";
		
		
		
		$sql_query_gost="SELECT id, ".$select_fields." FROM ".$tab_name.$where_fields." ORDER BY id".$pref_page;
		$sql_res_gost=mysql_query($sql_query_gost, $conn_id);

		$sql_query_page="SELECT id FROM ".$tab_name.$where_fields;
		$sql_res_page=mysql_query($sql_query_page, $conn_id);
		$num_predl=mysql_num_rows($sql_res_page);
		$numpages=ceil($num_predl/$perpage);
		
		if (mysql_num_rows($sql_res)>0)
		{
			if (mysql_num_rows($sql_res_gost)>0)
			{
			$out.= "По запросу \"".$search_string."\" найдено документов: ".$num_predl;
			$out.="<table cellpadding='5' cellspacing='0' border='0' width=100%><tr>";
			$rus_fields['id']="№";
			for ($i=0; $i < mysql_num_fields($sql_res_gost); $i++)
			{
			$field_name=mysql_field_name($sql_res_gost,$i);
			$out.="<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>".$rus_fields[$field_name]."</b></td>";
			}
			$out.="</tr>";
			while($array_data=mysql_fetch_row($sql_res_gost))
			{
			$out.="<tr>";
			for ($i=0; $i < mysql_num_fields($sql_res_gost); $i++)
			$out.="<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'>".stripslashes($array_data[$i])."</td>";
			$out.="</tr>";
			}
			$out.="</table>";

			if ($numpages>1)
			{
			$out.="<br><br><div align=left>Страницы: | ";
			for ($i=1;$i<=$numpages;$i++)
			{
			if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show="<a href='?page=".$i."&action=search&search_str=".$search_string."'>".$i."</a>";
			$out.="<span style='padding:2 1 2 1;'>".$i_show."</span> | ";
			}
			$out.="</div><br>";
			}
			}
			else $out.="По запросу \"".$search_string."\" документов не найдено";
		}
	}




	}
	else $out.="Задан пустой поисковый запрос";
return($out);
}

?>