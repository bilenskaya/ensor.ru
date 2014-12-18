<?php

function links_main()
{
	global $path_links, $links_tov_id, $links_exchange, $links_rub_id, $links_rub_url;
	$out="";
	links_url();
	
	if (@$links_rub_url[0]=="add" && $links_exchange=="Yes") $out.=links_exchange();
	elseif (!isset($links_rub_id)) $out.=links_rub_out();
	else $out.=links_rubtov_out();
	
	return ($out);
}

















function links_url()
{
	global $sql_pref, $conn_id, $path_links;
	global $url_decode, $module_name, $module_url;
	global $links_rub_url, $links_rub_id, $links_rub_name, $links_rub_num;
	global $links_tov_url, $links_tov_id, $links_tov_name;
	global $page_title, $page_header1;
	if ($url_decode!="/".$path_links."/" && substr($url_decode,0,strpos($url_decode,"?"))!="/".$path_links."/")
	{
		$kol=strlen($path_links)+1;
		$str=substr($url_decode,$kol);
		if (strpos($str,"?")) $str=substr($str,0,strpos($str,"?"));
		if (substr($str,-5)=='.html')
		{
		 	$str=substr($str,0,strlen($str)-5);
			$links_tov_url=substr($str, strrpos($str, '/')+1);
			$str=substr($str, 0, strrpos($str, '/')+1);
		}
		if (substr($str,-1)=='/') $str=substr($str,0,-1);
		if (substr($str,0,1)=='/') $str=substr($str,1);
		$links_rub_url=explode('/', $str);
		$links_rnum=count($links_rub_url);
		$links_rub_num=0;
		$links_rub_parent_id[0]=0;
		if ($links_rub_url[0]=="add") return;
		for ($i=0; $i<=($links_rnum-1); $i++)
		{
			$links_rub_num++;
			$sql_query="SELECT id, name FROM ".$sql_pref."_links_rubs WHERE level='".($i+1)."' AND url='".$links_rub_url[$i]."' AND parent_id='".$links_rub_parent_id[$i]."'";
			$sql_res=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res)==0 || mysql_error()) error_404();
			list($links_rub_id[$i], $links_rub_name[$i])=mysql_fetch_row($sql_res);
			$links_rub_name[$i]=stripslashes($links_rub_name[$i]);
			if ($i!=($links_rnum-1)) $links_rub_parent_id[($i+1)]=$links_rub_id[$i];
			$module_name[$i]=$links_rub_name[$i]; $module_url[$i]=$links_rub_url[$i];
			$page_title.=" | ".$links_rub_name[$i];
			$page_header1=$links_rub_name[$i];
		}
		if (isset($links_tov_url) && !empty($links_tov_url))
		{
			$sql_query="SELECT id, name FROM ".$sql_pref."_links_arts WHERE url='".$links_tov_url."' AND parent_id='".$links_rub_id[($links_rub_num-1)]."' AND enable='Yes'";
			$sql_res=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res)==0 || mysql_error()) error_404();
			list($links_tov_id, $links_tov_name)=mysql_fetch_row($sql_res);
			$links_tov_name=stripslashes($links_tov_name);
			$module_name[($i)]=$links_tov_name;
			$module_url[($i)]=$links_tov_url;
			$page_title.=" | ".$links_tov_name;
		}
	}
}
























function links_rub_out()
{
	global $sql_pref, $conn_id;
	global $path_links, $links_rub_id, $links_rub_num, $links_exchange;
	$out="";
	$sql_query="SELECT id, name, url FROM ".$sql_pref."_links_rubs WHERE parent_id='0' AND enable='Yes' ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<div>";
		while(list($id, $name, $url)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			
			
			$sql_query="SELECT id, link, name, descr FROM ".$sql_pref."_links_arts WHERE parent_id='".$id."' AND enable='Yes' ORDER BY code LIMIT 0,3";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res_1)>0)
			{
				$out.="<div style='padding:5 0 10 0;'>";
				$out.="<h2><a href='/".$path_links."/".$url."/' style='text-decoration:none;'>".$name."</a></h2>";
				
				$out.="<div style='padding:0 0 0 20;'>";
				while(list($l_id, $l_link, $l_name, $l_descr)=mysql_fetch_row($sql_res_1))
				{
					$l_link=stripslashes($l_link);$l_name=stripslashes($l_name);$l_descr=stripslashes($l_descr);
					$out.="<div style='padding:3 0 3 0;'>";
					$out.="<div><a href='".$l_link."' target=_blank>".$l_name."</a></div>";
					$out.="<div>".$l_descr."</div>";
					$out.="<div><span style='color:#999999;font-size:11px;'>".$l_link."</span></div>";
					$out.="</div>";
				}
				$out.="<div style='padding:8 0 3 0;'><a href='/".$path_links."/".$url."/' style='text-decoration:underline;color:#999;font-size:11px;'>Все сайты в разделе \"".$name."\" ...</a></div>";
				$out.="</div>";
				
				$out.="</div>";
			}
		}
		$out.="</div>";
	}
	
	if ($links_exchange=="Yes") $out.="<div style='padding: 30 0 10 0;'><a href='/".$path_links."/add/' style='color:#999999; font-size:12px;'>Добавить сайт в каталог</a>";
	
	return ($out);
}


























function links_rubtov_out()
{
	global $sql_pref, $conn_id, $path;
	global $path_links, $links_rub_id, $links_rub_num, $links_rub_url, $links_exchange;
	global $links_arts_perpage;
	$out="";
	if(isset($_REQUEST['page'])) $page=intval($_REQUEST['page']); else $page=1;
	if (isset($links_arts_perpage)) $kol=$links_arts_perpage; else $kol=20;
	$first=$kol*($page-1);
	$sql_query="SELECT id, link, name, descr FROM ".$sql_pref."_links_arts WHERE parent_id='".$links_rub_id[($links_rub_num-1)]."' AND enable='Yes' ORDER BY code LIMIT ".$first.", ".$kol."";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<br>";
		if ($page>1) $add_params="?page=".$page; else $add_params="";
		while(list($id, $link, $name, $descr)=mysql_fetch_row($sql_res))
		{
			$link=stripslashes($link);$name=stripslashes($name);$descr=stripslashes($descr);
			$out.="<div>";
			$out.="<div><b><a href='".$link."' target=_blank>".$name."</a></b></div>";
			$out.="<div>".$descr."</div>";
			$out.="<div><span style='color:#999999;'>".$link."</span></div>";
			$out.="</div>";
			$out.="<br><br>";
		}
		$out.="<br><br>";
		$out.="<br><div align=center>";
		if($page>1) $out.="<a href='?page=".($page-1)."'>Предыдущие ".$kol." ссылок</a>&nbsp;&nbsp;&nbsp;";
		$sql_query1="SELECT id FROM ".$sql_pref."_links_arts WHERE parent_id='".$links_rub_id[($links_rub_num-1)]."' AND enable='Yes' ORDER BY code LIMIT ".($first+$kol).", 1";
		$sql_res1=mysql_query($sql_query1, $conn_id);
		if (mysql_num_rows($sql_res1)>0) $out.="<a href='?page=".($page+1)."'>Следующие ".$kol." ссылок</a>";
		$out.="</div>";
	}
	
	if ($links_exchange=="Yes") $out.="<div style='padding: 30 0 10 0;'><a href='/".$path_links."/add/' style='color:#999999; font-size:12px;'>Добавить сайт в каталог</a>";	
	
	return ($out);
}

































function links_exchange()
{
	global $sql_pref, $conn_id;
	global $links_rub_id, $links_rub_num, $path_www, $path_domen, $path_links, $links_phrase, $links_email;
	global $module_name, $module_url;
	global $page_title, $page_header1;
	
	$page_title=$page_header1="Добавить ссылку";
	$module_name[]="Добавить ссылку";
	$module_url[]="add";

	$out="";

	if (isset($_REQUEST['submit']) && $_REQUEST['antispam']=="1917")
	{
		$parent_id=$_REQUEST['parent_id'];
		$fields_form=array(
												"link" 						=> array ("", "required"),
												"name" 						=> array ("addslashes",	"required"),
												"descr"			 			=> array ("addslashes",	""),
												"backlink_name" 			=> array ("addslashes",	""),
												"backlink_email"			=> array ("", "required"),
												"backlink" 					=> array ("addslashes",	"required")
											);
		$fields_name=array_keys($fields_form);
		$fields_sql=$fields_form;

		$sql_fields="";
		$sql_data="";
		for ($i=0;$i<count($fields_name);$i++)
		{
			$fields_data[$fields_name[$i]]="";
			if (isset($_REQUEST[$fields_name[$i]]) AND !empty($_REQUEST[$fields_name[$i]]))
			{
				if ($fields_form[$fields_name[$i]][0]=="addslashes") $fields_data[$fields_name[$i]]=addslashes($_REQUEST[$fields_name[$i]]);
				else $fields_data[$fields_name[$i]]=$_REQUEST[$fields_name[$i]];
			}
			elseif ($fields_form[$fields_name[$i]][1]=="required") $error[$fields_name[$i]]="Ошибка!";

			$sql_fields.=$fields_name[$i].", ";
			$sql_data.="\"".$fields_data[$fields_name[$i]]."\", ";
		}
//		if (email_valid(@$email)==false) $error['email']="Ошибка!";

		if ($_REQUEST['backlink']==$_REQUEST['link'] || $_REQUEST['backlink']==$_REQUEST['backlink_name']) return;

		if (!isset($error) || count(@$error)==0)
		{
			$sql_query="SELECT id FROM ".$sql_pref."_links_arts WHERE parent_id='".$parent_id."'";
			$sql_res=mysql_query($sql_query, $conn_id);
			$code=mysql_num_rows($sql_res);

			$sql_query="INSERT INTO ".$sql_pref."_links_arts (".$sql_fields."enable, parent_id, code) VALUES (".$sql_data."'No','".$parent_id."','".$code."')";
			$sql_res=mysql_query($sql_query, $conn_id);

			$mailtitle=$path_domen.": обмен ссылками";
			$mailheader="From: robot@".$path_domen."\n";
			$mailheader.="Content-Type: text/plain;\n charset=\"WINDOWS-1251\"";

			$mailcontent="";
			$mailcontent.="\n";
			$mailcontent.="Название: ".$fields_data["name"]."\n";
			$mailcontent.="URL: ".$fields_data["link"]."\n";
			$mailcontent.="Описание: ".$fields_data["descr"]."\n";
			$mailcontent.="\n";
			$mailcontent.="Обратная ссылка:\n".$fields_data["backlink"]."\n";
			$mailcontent.="".$fields_data["backlink_name"]." | ".$fields_data["backlink_email"]."\n";
			$mailcontent.="\n";
			$mailcontent.="Админка: ".$path_www."admin/links/?parent_id=".$parent_id." \n\n";
			$mailcontent.="\n";

			mail($links_email,$mailtitle,$mailcontent,$mailheader);

			$out.="<h3>Спасибо!</h3>";
			$out.="После проверки мы добавим вашу ссылку<br>";
			$out.="<br><br><a href='/".$path_links."/' style='color:#999999; font-size:12px;'>Добавить еще один сайт</a>";
			return ($out);
		}
	}



	$out.="<h3>Предлагаем обменяться ссылками с нашим сайтом</h3>
	<b>Обратите внимание!</b><br>
	- К размещению принимаются только сайты со схожей тематикой, которые будут интересны нашим посетителям.<br>
	- Ссылки, состоящие из несвязного набора ключевых слов размещены не будут.";

	$out.="<br><br>";

	$out.="<h3>Наша ссылка</h3>";
	$out.="<textarea style='width:600;height:40;' readonly onclick=\"this.select()\"><a href=\"".$path_www."\">".$links_phrase."</a></textarea>";

	$out.="<br><br>";

	$out.="
	<form action='' method=post name=register_form>
		<table cellpadding=3 cellspacing=0 border=0>
			<tr>
				<td align=left>URL:</td>
				<td align=left valign=top><input type=Text maxlength=100 size=40 name=link style='width:400px;font-size: 14px;' value='".@$_REQUEST['link']."'></td>
				<td align=left><font color=#FF0000><b>".@$error['link']."</b></font></td>
			</tr>
			<tr>
				<td align=left>Название:</td>
				<td align=left valign=top><input type=Text maxlength=50 size=40 name=name style='width:400px;font-size: 14px;' value='".@$_REQUEST['name']."'></td>
				<td align=left><font color=#FF0000><b>".@$error['name']."</b></font></td>
			</tr>
			<tr>
				<td align=left>Описание:</td>
				<td align=left valign=top><textarea name=descr cols=48 rows=5 style='width:400px;font-size: 14px;'>".@$_REQUEST['descr']."</textarea></td>
				<td align=left><font color=#FF0000><b>".@$error['descr']."</b></font></td>
			</tr>
			<tr>
				<td align=left>Раздел:</td>
				<td align=left valign=top>
					<select name=parent_id style='width:400px;font-size: 14px;'>";
					$sql_query="SELECT id, name FROM ".$sql_pref."_links_rubs ORDER BY code";
					$sql_res=mysql_query($sql_query, $conn_id);
					if (mysql_num_rows($sql_res)>0)
					{
						while(list($id, $name)=mysql_fetch_row($sql_res))
						{
							$name=stripslashes($name);
							$out.="<option value='".$id."' style='font-size: 14px;'> ".$name." </option>";
						}
					}
					else
					{
						$out.="<option value='0' style='font-size: 14px;'> Общая рубрика </option>";
					}
				$out.="</select>
				<td align=left>&nbsp;</td>
			</tr>

			<tr>
				<td align=left>&nbsp;</td>
				<td align=left valign=top>&nbsp;</td>
				<td align=left><font color=#FF0000>&nbsp;</td>
			</tr>
			<tr>
				<td align=left>Ваше имя:</td>
				<td align=left valign=top><input type=Text maxlength=100 size=40 name=backlink_name style='width:400px;font-size: 14px;' value='".@$_REQUEST['backlink_name']."'></td>
				<td align=left><font color=#FF0000><b>".@$error['backlink_name']."</b></font></td>
			</tr>
			<tr>
				<td align=left>E-mail:</td>
				<td align=left valign=top><input type=Text maxlength=100 size=40 name=backlink_email style='width:400px;font-size: 14px;' value='".@$_REQUEST['backlink_email']."'></td>
				<td align=left><font color=#FF0000><b>".@$error['backlink_email']."</b></font></td>
			</tr>
			<tr>
				<td align=left>URL страницы,<br>где размещена<br>наша ссылка:</td>
				<td align=left valign=middle><input type=Text maxlength=100 size=40 name=backlink style='width:400px;font-size: 14px;' value='".@$_REQUEST['backlink']."'></td>
				<td align=left><font color=#FF0000><b>".@$error['backlink']."</b></font></td>
			</tr>
            <tr>
				<td align=left>Антиспам: <br>в каком году<br>была октябрьская <br>революция?</td>
				<td align=left valign=middle><input type=Text maxlength=100 size=40 name=antispam style='width:400px;font-size: 14px;' value='".@$_REQUEST['antispam']."'></td>
				<td align=left><font color=#FF0000><b>".@$error['antispam']."</b></font></td>
			</tr>
			<tr>
				<td align=left><br><br>&nbsp;</td>
				<td align=left><input type=Submit name=submit value=Отправить style='font-size: 12px; width:140px; background-color: #eeeeee; color: #555555; border: 1px #aaaaaa solid;'></td>
				<td align=left>&nbsp;</td>
			</tr>
		</table>
	</form>";

	return($out);
}














?>
