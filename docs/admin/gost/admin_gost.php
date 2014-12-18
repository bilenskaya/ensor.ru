<?php
set_time_limit(6000);
ini_set("max_execution_time", "6000");


function check_empty($id)
{
	global $sql_pref, $conn_id, $path;
	$sql_query1="SELECT file FROM ".$sql_pref."_gost_rub WHERE id='$id'";
	$sql_res1=mysql_query($sql_query1, $conn_id); 
	list($file)=mysql_fetch_row($sql_res1); 
	if(is_file($path.$file)) return (FALSE);
	else return (TRUE);
	}



function encoding ($str)
{
	$code = "UTF-8";                      // Кодировка xml-а
	$curcode = "CP1251";            // Текущая кодировка
	$str = mb_convert_encoding($str, $curcode, $code);
	return $str;
}


 function import_xml($id)
 {
 
global $sql_pref, $conn_id, $path, $tab_name, $new_record_tag, $current_id;

$sql_query="SELECT file FROM ".$sql_pref."_gost_rub WHERE id='$id'";
$sql_res=mysql_query($sql_query, $conn_id); 
list($file)=mysql_fetch_row($sql_res);

$file=$path.$file;



$tab_name=$sql_pref."_gost_".str_replace(".", "_", basename($file));
$sql_query="DROP TABLE IF EXISTS ".$tab_name;
$sql_res=mysql_query($sql_query, $conn_id);

$tags=get_xml_tags($file);

$fields="";
foreach($tags as $k=>$v) {$v=str_replace(" ", "_", $v); $v=str_replace("-", "_", $v); $fields.="`".translit($v)."` text CHARACTER SET utf8, ";}
$new_record_tag=translit($tags[1]);
$new_record_tag=str_replace(" ", "_", $new_record_tag);
$new_record_tag=str_replace("-", "_", $new_record_tag);


 

$sql_query="SELECT fields FROM ".$sql_pref."_gost_rub WHERE id='$id'";
$sql_res=mysql_query($sql_query, $conn_id); 
list($field_settings)=mysql_fetch_row($sql_res);
$field_settings=unserialize($field_settings);

foreach($tags as $k=>$v) {$orig=$v; $v=str_replace(" ", "_", $v); $v=str_replace("-", "_", $v); $v=translit($v); $new_fields[$v]=1; $rus_fields[$v]=$orig;}

if(is_array($field_settings)) foreach($new_fields as $k=>$v) $new_fields[$k]=$field_settings[$k];
$new_fields=serialize($new_fields);
$rus_fields=serialize($rus_fields);


$sql_query="CREATE TABLE IF NOT EXISTS `".$tab_name."` (`id` int(8) unsigned NOT NULL AUTO_INCREMENT, ".$fields."PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=cp1251";
$sql_res=mysql_query($sql_query, $conn_id); 

$sql_query="UPDATE ".$sql_pref."_gost_rub SET import='".filectime($file)."', fields='".$new_fields."', rus_fields='".$rus_fields."' WHERE id='".$id."'";
$sql_res=mysql_query($sql_query, $conn_id);


 
	 ####################################################
     ### функция работы с данными
     function data ($parser, $data)
     {
		 global $sql_pref, $conn_id, $path, $tab_name, $new_record_tag, $current_id, $cur_field, $upd_fields; 
		 if (strlen($data)>1) 
			{
			$upd_data=addslashes(encoding($data));
			$upd_fields[$cur_field].=$upd_data;
			}
     }
     ############################################


     ####################################################
     ### функция открывающих тегов
     function startElement($parser, $name, $attrs)
     {
	global $sql_pref, $conn_id, $path, $tab_name, $new_record_tag, $current_id, $cur_field, $upd_fields, $sql_query_upd; 
		$name=str_replace("_x0020_", " ",$name);
		$name=encoding($name);
		$name=translit($name);
		$name=str_replace(" ", "_", $name);
		$name=str_replace("-", "_", $name);
		$cur_field=$name;
        if ($name==$new_record_tag) 
			{
			if (isset($sql_query_upd)) 
				{
				foreach($upd_fields as $k=>$v) $sql_query_upd.=$k."='".$v."', ";
				$sql_query_upd=substr($sql_query_upd, 0, strlen($sql_query_upd)-2);
				$sql_query_upd.=" WHERE id='".$current_id."'";
				if(!$sql_res=mysql_query($sql_query_upd, $conn_id)) echo "ошибка вставки данных<br>".$sql_query_upd."<br>";
				}
			$sql_query_ins="INSERT INTO ".$tab_name." () VALUES ()";
			$sql_res=mysql_query($sql_query_ins, $conn_id);
			$current_id=mysql_insert_id();
			if(is_array($upd_fields)) foreach($upd_fields as $k=>$v) $upd_fields[$k]="";
			$sql_query_upd="UPDATE ".$tab_name." SET ";
			}
     }
     ###############################################


     ###############################################
     ## функция закрывающих тегов
     function endElement($parser, $name)
     {
         //print "<br>";
     }
     ############################################


     $xml_parser = xml_parser_create();

     xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);

     // указываем какие функции будут работать при открытии и закрытии тегов
     xml_set_element_handler($xml_parser, "startElement", "endElement");

     // указываем функцию для работы с данными
     xml_set_character_data_handler($xml_parser,"data");


     // открываем файл
     $fp = fopen($file, "r");

     $perviy_vxod=1; // флаг для проверки первого входа в файл
     $data="";  // сюда собираем частями данные из файла и отправляем в разборщик xml

     // цикл пока не найден конец файла
     while (!feof ($fp) and $fp)
     {

         $simvol = fgetc($fp); // читаем один символ из файла
         $data.=$simvol; // добавляем этот символ к данным для отправки

         // если символ не завершающий тег, то вернемся к началу цикла и добавим еще один символ к данным, и так до тех пор, пока не будет найден закрывающий тег
         if($simvol!='>') { continue;}
         // если закрывающий тег был найден, теперь отправим эти собранные данные в обработку

         // проверяем, если это первый вход в файл, то удалим все, что находится до тега <?
         // так как иногда может встретиться мусор до начала XML (корявые редакторы, либо файл получен скриптом с другого сервера)
         if($perviy_vxod) {$data=strstr($data, '<?'); $perviy_vxod=0;}


         // теперь кидаем данные в разборщик xml

         if (!xml_parse($xml_parser, $data, feof($fp))) {

             // здесь можно обработать и получить ошибки на валидность...
             // как только встретится ошибка, разбор прекращается
             echo "<br>XML Error: ".xml_error_string(xml_get_error_code($xml_parser));
             echo " at line ".xml_get_current_line_number($xml_parser);
//             break;
         }

         // после разбора скидываем собранные данные для следующего шага цикла.
         $data="";
     }
     fclose($fp);
     xml_parser_free($xml_parser);

 }

 
 function get_xml_tags($file)
 {
global $tags; 
 
//phpinfo();
	 
	 function datatag ($parser, $data)
     {}
	 
     function startElementtag($parser, $name, $attrs)
     {
        global $tags;
		$name=str_replace("_x0020_", " ",$name);
		$tags[]=encoding($name);
		$tags=array_unique($tags);
     }


     function endElementtag($parser, $name)
     {}

     $xml_parser = xml_parser_create();

     xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);

     // указываем какие функции будут работать при открытии и закрытии тегов
     xml_set_element_handler($xml_parser, "startElementtag", "endElementtag");

     // указываем функцию для работы с данными
     xml_set_character_data_handler($xml_parser,"datatag");

     // открываем файл
     $fp = fopen($file, "r");

     $perviy_vxod=1; // флаг для проверки первого входа в файл
     $data="";  // сюда собираем частями данные из файла и отправляем в разборщик xml

     // цикл пока не найден конец файла
     while (!feof ($fp) and $fp)
     {

         
		 $simvol = fgetc($fp); // читаем один символ из файла


         $data.=$simvol; // добавляем этот символ к данным для отправки

         // если символ не завершающий тег, то вернемся к началу цикла и добавим еще один символ к данным, и так до тех пор, пока не будет найден закрывающий тег
         if($simvol!='>') { continue;}

         // если закрывающий тег был найден, теперь отправим эти собранные данные в обработку

         // проверяем, если это первый вход в файл, то удалим все, что находится до тега 
         // так как иногда может встретиться мусор до начала XML (корявые редакторы, либо файл получен скриптом с другого сервера)
         if($perviy_vxod) {$data=strstr($data, '<?'); $perviy_vxod=0;}


         // теперь кидаем данные в разборщик xml

         if (!xml_parse($xml_parser, $data, feof($fp))) {

             // здесь можно обработать и получить ошибки на валидность...
             // как только встретится ошибка, разбор прекращается
            echo "<br>XML Error: ".xml_error_string(xml_get_error_code($xml_parser));
            echo " at line ".xml_get_current_line_number($xml_parser);
            break;
         }
         // после разбора скидываем собранные данные для следующего шага цикла.

         $data="";
     }
	 fclose($fp);
     xml_parser_free($xml_parser);
	 $tags=array_unique($tags);
	 return $tags;
	 
 }

 
 

function admin_rubric_list()
{
	global $sql_pref, $conn_id, $path;
	$sql_query="SELECT id, enable, name, file, import FROM ".$sql_pref."_gost_rub ORDER BY name";
	$sql_res=mysql_query($sql_query, $conn_id);

$add_pic="<a href='?action=rubric_add#rubric_add'><img src='/admin/img/line.gif' width=25 height=13 alt='Добавить рубрику' border=0></a>";
echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
    			<tr class='maintitle'>
    				<td width='20' class='maintitle' align='center'><b>id</b></td>
    				<td width='80' class='maintitle' align='center'>".$add_pic."</td>
    				<td class='maintitle' align='left'><b>название</b></td>
					<td class='maintitle' align='left'><b>файл данных</b></td>
    				<td width='30' class='maintitle' align='center'><b>del</b></td>
    			</tr>";


	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $enable, $name, $file, $import)=mysql_fetch_row($sql_res))
		{
			$name_show="<a href='?id=".$id."&action=rubric_show#rubric_show'>".$name."</a>";
			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=rubric_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=rubric_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение' border=0></a>";
			if(is_file($path.$file) and $import==filectime($path.$file)) $info_text=" (Загружен в БД)"; else $info_text=" <b>(Не Загружен в БД)</b>";
			$edit_pic="<a href='?id=".$id."&action=rubric_edit#rubric_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			if (!check_empty($id)) $import_pic="<a href='?id=".$id."&action=rubric_import#rubric_import'><img src='/admin/img/art_main.gif' width=25 height=13 alt='Импорт XML' border=0></a>";
			
			else $import_pic="";
			if (check_empty($id)) {$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&action=rubric_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>"; $info_text=" <b>(Не найден)</b>";}
			else $del="";
			
					echo "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$enable_pic.$edit_pic.$import_pic."</td>
					<td align='left'>".$name_show."</td>
					<td align='left'>".$file.$info_text."</td>
					<td class='common' align='center'>".$del."</td>
					</tr>";
		}
	}

echo "</table>";
}


function admin_show_rubric($rub_id)
{
	global $sql_pref, $conn_id, $path;

	$sql_query="SELECT id, name, fields, rus_fields, file FROM ".$sql_pref."_gost_rub WHERE id='$rub_id'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $name, $fields, $rus_fields, $file)=mysql_fetch_row($sql_res);
		$name=stripslashes($name);
		echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
			<tr class='common'>
			<td align='center' colspan=2><H1>".$name."</H1></td>
			</tr>
			</table>";
		
		$fields=unserialize($fields);
		$rus_fields=unserialize($rus_fields);
		$select_fields="";
		if(is_array($fields)) foreach($fields as $k=>$v) if($v==1) $fields_to_show[$k]=$rus_fields[$k];
		if(is_array($fields_to_show)) foreach($fields_to_show as $k=>$v) {$select_fields.=$k.", ";}
		$select_fields=substr($select_fields, 0, strlen($select_fields)-2);
		
		$file=$path.$file;
		$tab_name=$sql_pref."_gost_".str_replace(".", "_", basename($file));
		
		
		$perpage=50;
		if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
		$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";
		
		
		$sql_query_gost="SELECT id, ".$select_fields." FROM ".$tab_name." ORDER BY id".$pref_page;
		$sql_res_gost=mysql_query($sql_query_gost, $conn_id);


		echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'><tr class='maintitle'>";
		
		for ($i=0; $i < mysql_num_fields($sql_res_gost); $i++)
		{
		$field_name=mysql_field_name($sql_res_gost,$i);
		echo "<td class='maintitle' align='left'><b>".$rus_fields[$field_name]."</b></td>";
		}
		echo "</tr>";
		
		if (mysql_num_rows($sql_res)>0)
		{
			while($array_data=mysql_fetch_row($sql_res_gost))
			{
			echo "<tr class='common'>";
			for ($i=0; $i < mysql_num_fields($sql_res_gost); $i++)
			echo "<td class='common' align='center'><font color='#A0A0A0'>".stripslashes($array_data[$i])."</font></td>";
			echo "</tr>";
			}
		}
		echo "</table>";
	}
	$sql_query_page="SELECT id FROM ".$tab_name;
	$sql_res_page=mysql_query($sql_query_page, $conn_id);
	$num_predl=mysql_num_rows($sql_res_page);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		echo "<br><br><div align=left>Страницы: ";
		for ($i=1;$i<=$numpages;$i++)
		{
		if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show=$i;
		echo "<span style='padding:2 3 2 3;background-color:#eeeeee;border:solid 1px #aaaaaa;'><a href='?id=".$id."&action=rubric_show&page=".$i."' style='text-decoration:none;'>".$i_show."</a></span> ";
		}
		echo "</div><br>";
	}
}


function form_rubric_save()
{
	global $sql_pref, $conn_id, $path;
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (isset($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
	if (isset($_REQUEST['file'])) $file=$_REQUEST['file']; else $file="";
	if (isset($_REQUEST['fields'])) $fields=$_REQUEST['fields']; else $fields="";
	
	$sql_query="SELECT fields FROM ".$sql_pref."_gost_rub WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($full_fields)=mysql_fetch_row($sql_res);
		$full_fields=unserialize($full_fields);
	}
	
	if(is_array($full_fields)) 
	{foreach($full_fields as $k=>$v) if(isset($fields[$k])) $full_fields[$k]=1; else $full_fields[$k]=0;
	$full_fields=serialize($full_fields);} 
	else $full_fields="";
	
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_gost_rub SET name='".$name."', file='".$file."', enable='".$enable."', fields='".$full_fields."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
	else
	{
		$sql_query="INSERT INTO ".$sql_pref."_gost_rub (name, enable, file) VALUES ('".$name."', '".$enable."', '".$file."')";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
}



function del_gost($id)
{
	del_record('gost_rub', $id, 'No', -1);
}
?>
