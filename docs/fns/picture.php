<?php



function picture_main()
{
	global $sql_pref, $conn_id, $art_url, $path_picture, $album_sql_query;
	$out="";
	if (isset($_REQUEST['id'])) 
	{
		$id=$_REQUEST['id'];
		$art_sql_query="SELECT parent_id FROM ".$sql_pref."_picture WHERE (enable='Yes' AND moderation='No' AND id = '".$id."')";
		$sql_res=mysql_query($art_sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) list($art_url)=mysql_fetch_row($sql_res);
	}
	
	$addform=add_form($art_url);
	$out.="<script type=\"text/javascript\"> var picform = \"$addform\";</script>";

	if (isset($_REQUEST['action']))
    {
    if ($_REQUEST['action']=="comment_add")  picture_comments_form_save($id);
    elseif ($_REQUEST['action']=="comment_del")  picture_comments_del($id);
	}
//	$dir_path=$path."files/picture";
//	make_dir($dir_path);
	if (isset($_REQUEST['action']) AND $_REQUEST['action']=="search")  {$search_string=strip_tags($_REQUEST['search_str']); $out.=show_search($search_string);}
	

	elseif (isset($art_url)) 
	{
		$out.="<table cellpadding='1' cellspacing='1' border='0' width=100%><tr><td width=1%><img src='/img/picture_photo_album.png' alt='Все альбомы' border='0'></td><td align='left'><a href='/".$path_picture."/' class='picture_rubric'>Все альбомы</a></td><td align='right'>".search_panel()."</td></tr></table>";
		$album_sql_query="SELECT id, descr, enable, date_upload, format, file_size, file_name, tags, user_id FROM ".$sql_pref."_picture WHERE (enable='Yes' AND moderation='No' AND parent_id = '".$art_url."') ORDER by date_upload desc";
		if (isset($id)) $out.=picture_show($id);
		elseif ($art_url=='postcard') $out.=postcard_show($_REQUEST['post_id']);
		else $out.=picture_album($art_url);
	}
	else 
	{
		$out.="<table cellpadding='2' cellspacing='2' width=100%><tr><td align='left'></td><td align='right'>".search_panel()."</td></tr></table>";
		$out.=picture_rubric();
	}
	
	$out.="<div id=\"add1\" onClick='document.getElementById(\"add2\").innerHTML = picform; document.getElementById(\"add1\").innerHTML = \"\";'><table><tr><td style='cursor: pointer;' width='1%' align=right><img src='/img/plus.png' border='0' vspace='5'></td><td style='cursor: pointer;' class='auth_main'>Добавить картинку</td></tr></table></div><div id=\"add2\"></div>";	
	if (isset($_REQUEST['action']) AND $_REQUEST['action']=="picture_save") $out.=picture_save(); 
	$out.="<br><a href='javascript:history.back(1)'>««&nbsp;вернуться</a><br><br>";

	
	return ($out);
}





function check_count($rub_id)
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT id FROM ".$sql_pref."_picture WHERE (enable='Yes' AND moderation='No' AND parent_id = '".$rub_id."')";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0) return (mysql_num_rows($sql_res));
	else return 0;
	}

//Функция ограничения длины строк
function picture_str_limit($str,$len){
	$slen=strlen($str);
	$str=substr($str,0,$len);
	if ($slen>$len) $str.="...";
return $str;
}

function picture_album_name($rub_id)
{
	global $sql_pref, $conn_id, $path_picture;
	$out="";
    $sql_query="SELECT id, name FROM ".$sql_pref."_picture_rub WHERE (enable='Yes' and id='".$rub_id."')";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0) list($id, $name)=mysql_fetch_row($sql_res);
	$name=stripslashes($name);
	$out.=$name;
return ($out);
}


function search_panel()
{
$out="<form name='form_name' action='?' method='post' enctype='multipart/form-data'>
<input type='hidden' name='action' value='search'>
<table cellpadding='1' cellspacing='1' border='0' bgcolor='#FFFFFF'>
	<tr><td colspan='2'>Поиск изображений</td></tr>
	<tr>
		<td><input type='text' name='search_str'></td>
		<td><input type='submit' name='button_submit' value='Найти'></td>
	</tr>
</table>
</form>";
return ($out);
}




function picture_rubric()
{
	global $sql_pref, $conn_id, $path, $path_picture, $page_header1, $page_title;

    $sql_query="SELECT id, name FROM ".$sql_pref."_picture_rub WHERE (enable='Yes') ORDER BY name";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
	$out.="<table cellpadding='2' cellspacing='0' border='0' width=100%>";
		while(list($id, $name)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			$count=check_count($id);
			$name_show="<a href='/".$path_picture."/".$id.".html' class='picture_rubric'>".$name."</a> (".$count.")";
			

			$out.="<tr><td width=1%><img src='/img/picture_photo_album.png' alt='".$name."' border='0'></td><td valign=middle align=left>".$name_show."</td></tr>";

		}
	$out.="</table>";
	}
	return ($out);
}



function picture_album($rub_id)
{
	global $sql_pref, $conn_id, $path, $path_picture, $picture_perpage, $album_sql_query;
	global $page_title, $page_header1;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$picture_perpage).",".$picture_perpage."";

$page_header1=picture_album_name($rub_id);
$page_title=picture_album_name($rub_id);

	$sql_query=$album_sql_query.$pref_page;

	$stolb=3;
	$stolb_count=1;
	if ($sql_res=mysql_query($sql_query, $conn_id) and mysql_num_rows($sql_res)>0)
	{
		$out.= "<table cellspacing='2' cellpadding='2' border='0' width='100%'>";
		while(list($id, $descr, $enable, $date_upload, $format, $file_size, $file_name, $tags, $user_id)=mysql_fetch_row($sql_res))
	{
	$descr=stripslashes($descr);
	$descr_shot=picture_str_limit($descr, 45);
	$date_upload=date("d.m.y", $date_upload);

	if ($file_name!=="" and file_exists($path."files/picture/tn_".$file_name)) 
	$img="<a href=\"?id=$id\"><img src='/files/picture/tn_".$file_name."' alt='".$descr."' border='0'></a>";
	else $img="<img src='/files/picture/not_found_pic.png'>";

	if($stolb_count==1) {$out.="<tr>"; $out2="<tr>";}
	$out.="<td align=\"center\" valign=\"bottom\">".$img."</td>";
	$out2.="<td align=\"center\" valign=\"top\"><a href=\"?id=$id\" class=\"picture_album\">".$descr_shot."</a></td>";
	if($stolb_count==$stolb){$out.="</tr>"; $out2.="</tr>"; $stolb_count=0; $out.=$out2;}
	$stolb_count++;
	

	}
	if($stolb_count!==1){$out.="</tr>"; $out2.="</tr>"; $out.=$out2;}
	$out.="</table>";
	
	
	$sql_query=$album_sql_query;
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$picture_perpage);
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
	
	
	
	}
	else $out.="В данной категории нет фотографий";

return($out);
}







function picture_show($id)
{
	global $sql_pref, $conn_id, $path, $path_picture, $art_url, $path_users, $picture_perpage, $album_sql_query;
	global $page_title, $page_header1;

$page_header1=picture_album_name($art_url);

	$sql_query="SELECT id, parent_id, descr, enable, date_upload, format, file_size, file_name, tags, user_id FROM ".$sql_pref."_picture WHERE (enable='Yes' AND moderation='No' AND id = '".$id."')";

	if ($sql_res=mysql_query($sql_query, $conn_id) and mysql_num_rows($sql_res)>0)
	{
		$out.= "<table cellspacing='1' cellpadding='1' border='0' width='100%'>";
	list($id, $parent_id, $descr, $enable, $date_upload, $format, $file_size, $file_name, $tags, $user_id)=mysql_fetch_row($sql_res);
	$descr=stripslashes($descr);
	$page_title=picture_album_name($art_url)." - ".$descr;
	
	$sql_user_query="SELECT id, surname, name, name2 FROM ".$sql_pref."_users WHERE id='".$user_id."'";
	$sql_user_res=mysql_query($sql_user_query, $conn_id);
    		list($adder_id, $user_surname, $user_name, $user_name2)=mysql_fetch_row($sql_user_res);
			$user_surname=stripslashes($user_surname);
			$user_name=stripslashes($user_name);
			$user_name2=stripslashes($user_name2);
			$adder_name=$user_surname." ".substr($user_name,0,1).". ".substr($user_name2,0,1).".";
	
	
	
	$date_upload=date("d.m.y", $date_upload);
	$adder="<span class=dates>".$date_upload."</span> <a href='/".$path_users."/".$adder_id.".html'>".$adder_name."</a> ";
	
	if ($file_name!=="" and file_exists($path."files/picture/".$file_name)) 
	$img="<img src='/files/picture/".$file_name."'  alt='".$descr."'>";
	else $img="<img src='/files/picture/not_found_pic.png'>";
	
	
//Определение следующей и предыдущей картинки с номером страницы
	$sql_res=mysql_query($album_sql_query, $conn_id);
	$pos=0;
	$count=mysql_num_rows($sql_res);
	$max_pos=$count-1;
	for($row=mysql_fetch_array($sql_res); $row['id']!=$id; $row=mysql_fetch_array($sql_res)) $pos++;
	if($pos>0) $prev_pos=$pos-1; else $prev_pos=0;
	if($pos<$max_pos) $next_pos=$pos+1; else $next_pos=$max_pos;
	
	mysql_data_seek ($sql_res, $prev_pos);
	$row=mysql_fetch_array($sql_res);
	if($row['id']==$id) $prev_id=false; else $prev_id=$row['id'];
	
	
	mysql_data_seek ($sql_res, $next_pos);
	$row=mysql_fetch_array($sql_res);
	if($row['id']==$id) $next_id=false; else $next_id=$row['id'];
		
	$page=ceil(($pos+1)/$picture_perpage);
	
	if ($prev_id) $prev_but="<a href='/".$path_picture."/".$parent_id.".html?id=".$prev_id."'><img src='/img/player_rew.png' alt='Назад'></a>"; else $prev_but="<img src='/img/player_rew_na.png' alt='Назад'>";
	if ($next_id) $next_but="<a href='/".$path_picture."/".$parent_id.".html?id=".$next_id."'><img src='/img/player_fwd.png' alt='Вперед '></a>"; else $next_but="<img src='/img/player_fwd_na.png' alt='Вперед '>";
	$stop_but="<a href='/".$path_picture."/".$parent_id.".html?page=".$page."'><img src='/img/player_stop.png' alt='Альбом'></a>";

	$out.="<tr><td align=\"center\" colspan='3'>".$img."</td></tr>";
	$out.="<tr><td align=\"center\" colspan='3'><div class='picture_descr'>".$descr."</div></td></tr>";
	$out.="<tr><td align=\"left\" colspan='3'>".$adder."</td></tr>";
	$out.="<tr><td align=\"right\">".$prev_but."</td><td align=\"center\">".$stop_but."</td><td align=\"left\">".$next_but."</td></tr>";
	
	$send_form=send_form($id, $file_name);
	$out.="<script type=\"text/javascript\"> var sendform = \"$send_form\";</script>";
	$out.="<tr><td align=\"left\" colspan='3'><div id=\"send1\" onClick='document.getElementById(\"send2\").innerHTML = sendform; document.getElementById(\"send1\").innerHTML = \"\";'><table><tr><td width='1%' align=right><img style='cursor: pointer;' src='/img/send_card.png' border='0' vspace='5'></td><td style='cursor: pointer;' class='auth_main'>Отправить как открытку</td></tr></table></div><div id=\"send2\"></div></td></tr>";	
	$out.="</table>";
	if (isset($_REQUEST['action']) AND $_REQUEST['action']=="picture_send") $out.=picture_send(); 
	
	$out.=picture_comments($id);

	}

return($out);
}









function postcard_show($id)
{
global $sql_pref, $conn_id, $path, $path_picture, $art_url, $path_users, $picture_perpage, $album_sql_query;
global $page_title, $page_header1;

$sql_query_senders="SELECT id, pic_id, send_text, date_sending, sender_name, reciever_mail, sender_id FROM ".$sql_pref."_picture_senders WHERE (enable='Yes' AND id = '".$id."')";
if ($sql_res_senders=mysql_query($sql_query_senders, $conn_id) and mysql_num_rows($sql_res_senders)>0)
	{
	list($id, $pic_id, $send_text, $date_sending, $sender_name, $reciever_mail, $sender_id)=mysql_fetch_row($sql_res_senders);

	$sql_query="SELECT parent_id, descr, enable, date_upload, format, file_size, file_name, tags, user_id FROM ".$sql_pref."_picture WHERE (enable='Yes' AND moderation='No' AND id = '".$pic_id."')";
	if ($sql_res=mysql_query($sql_query, $conn_id) and mysql_num_rows($sql_res)>0)
		{
		list($parent_id, $descr, $enable, $date_upload, $format, $file_size, $file_name, $tags, $user_id)=mysql_fetch_row($sql_res);

		$send_text=stripslashes($send_text);
		$send_text=nl2br($send_text);
		$sender_name=stripslashes($sender_name);
		$descr=stripslashes($descr);
		$page_header1="Вам открытка от пользователя ".$sender_name;
		$page_title="Открытки энергетиков";	
		if ($file_name!=="" and file_exists($path."files/picture/".$file_name)) 
		$img="<img src='/files/picture/".$file_name."'  alt='".$descr."'>";
		else $img="<img src='/files/picture/not_found_pic.png'>";
		$date_sending=date("d.m.y", $date_sending);
		$date_sending="<span class=dates>".$date_sending."</span>";	
		$send_form=send_form($id, $file_name);
	
	$out.= "<table cellspacing='1' cellpadding='1' border='0' width='100%'>";
	$out.="<tr><td align=\"center\" colspan='3'>".$img."</td></tr>";
	$out.="<tr><td align=\"left\" colspan='3'>".$date_sending."</td></tr>";
	$out.="<tr><td align=\"center\" colspan='3'><div class='post_text'>".$send_text."</div></td></tr>";
	$out.="<tr><td align=\"right\" colspan='3'><div class='post_sender'>".$sender_name."</div></td></tr>";
	$out.="<script type=\"text/javascript\"> var sendform = \"$send_form\";</script>";
	$out.="</table>";
	if($sender_id>0)
	{
	$out.="<div style='padding: 10 0 0 0;font-weight:bold;'>Дополнительно:</div>";
	$out.="<ul>";
	$out.="<li>Профиль пользователя <a href='/".$path_users."/".$sender_id.".html'>".$sender_name."</a> на нашем сайте</li>";
	$out.="</ul>";
	$out.="<div id=\"send1\" onClick='document.getElementById(\"send2\").innerHTML = sendform; document.getElementById(\"send1\").innerHTML = \"\";'><table><tr><td width='1%' align=right><img src='/img/send_card.png' border='0' vspace='5'></td><td class='auth_main'>Отправить как открытку</td></tr></table></div><div id=\"send2\"></div>";
	}
	}
	else $out.="Сожалеем, но Ваша открытка удалена";
	}
	else $out.="Сожалеем, но Ваша открытка удалена";
	
return($out);
}










function show_search($search_string)
{
	global $sql_pref, $conn_id, $path, $path_picture;
	$out.="<table width=100%><tr><td width=1%><img src='/img/picture_photo_album.png' alt='Все альбомы' border='0'></td><td align='left'><a href='/".$path_picture."/' class='picture_rubric'>Все альбомы</a></td><td align='right'>".search_panel()."</td></tr></table>";
	$search_string=trim($search_string);
	if($search_string!=="")
	{
	$sql_query="SELECT id, parent_id, descr, file_name FROM ".$sql_pref."_picture WHERE (enable='Yes'  AND moderation='No' AND (descr LIKE '%".$search_string."%')) ORDER by date_upload desc";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0) 
		{
		$out.= "По запросу \"".$search_string."\" найдено изображений: ".mysql_num_rows($sql_res);
		$out.="<table cellpadding='3' cellspacing='2' border='0' width=100%>";
			while(list($id, $parent_id, $descr, $file_name)=mysql_fetch_row($sql_res)) 
		{
		$sql_query="SELECT name FROM ".$sql_pref."_picture_rub WHERE (enable='Yes' and id='$parent_id')";
		$sql_res1=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res1)>0) list($name)=mysql_fetch_row($sql_res1);
		$name=stripslashes($name);
		$name_show="<a href='/".$path_picture."/".$parent_id.".html' class='picture_rubric'>".$name."</a>";
		$descr=stripslashes($descr);

		if ($file_name!=="" and file_exists($path."files/picture/tn_".$file_name)) 
	$img="<a href='/".$path_picture."/".$parent_id.".html?id=".$id."'><img src='/files/picture/tn_".$file_name."' alt='".$descr."' border='0'></a>";
	else $img="<img src='/files/picture/not_found_pic.png'>";
		
		$out.="<tr><td valign=middle align=left width=1%>".$img."</td><td align=left><div>".$name_show."</div><div>".$descr."</div></td></tr>";
		}
		$out.="</table>";
	}
	else $out.="По запросу \"".$search_string."\" ничего не найдено";	
	}
	else $out.="Задан пустой поисковый запрос";
return($out);
}



function add_form($rub_id)
{
	global $sql_pref, $conn_id, $path, $user_id;
	$sql_rubric_query="SELECT id, name FROM ".$sql_pref."_picture_rub WHERE enable='Yes'";
	$sql_rubric_res=mysql_query($sql_rubric_query, $conn_id);
	while (list($allrub_id, $allrub_name)=mysql_fetch_row($sql_rubric_res)) $allrubs[$allrub_id]=$allrub_name;
	$allrubs[0]="Не указано";

if ($user_id==0) return ("<div>Добавлять изображения могут только <a href='/auth/register/'>зарегистрированные</a> пользователи</div>");
$out="<form name='form_name' action='' method='post' enctype='multipart/form-data'><input type='hidden' name='rub_id' value='$rub_id'><input type='hidden' name='action' value='picture_save'><input type='hidden' name='user_id' value='$user_id'>";
$out.="<table cellpadding='2' cellspacing='2' border='0' bgcolor='#FFFFFF'>";
$out.="<tr class='form_topline'><td colspan='2' align='center'><b>Добавление изображения</b></td></tr>";

$out.="<tr><td class='form_left'>Рубрика</td><td class='form_main'><select name='parent_id'>";
			foreach($allrubs as $ka=>$va) 
				if($ka==$rub_id) $out.="<option value=".$ka." selected>".$va."</option>";
				else $out.="<option value=".$ka.">".$va."</option>";
			$out.="</select></td></tr><tr><td class='form_left'>Файл</td><td class='form_main'><div><input class='form_file' type='file' name='img_name' size='65'></div></td></tr><tr><td class='form_left'>Описание</td><td class='form_main'><textarea class='form' name='descr' rows='2' cols='65'></textarea></td></tr><td>&nbsp;</td><td style='padding-top:10;'><input class='form_button' type='submit' name='button_submit' value='Отправить'></td></tr></table></form>";
return($out);
}




function send_form($pic_id, $pic_file_name)
{
global $user_id;
$antispam=show_codepic();

$out="<form name='form_name' action='' method='post' enctype='multipart/form-data'><input type='hidden' name='pic_id' value='$pic_id'><input type='hidden' name='action' value='picture_send'><input type='hidden' name='user_id' value='$user_id'><input type='hidden' name='pic_file_name' value='tn_$pic_file_name'>";
$out.="<table cellpadding='2' cellspacing='2' border='0' bgcolor='#FFFFFF'>";
$out.="<tr class='form_topline'><td colspan='2' align='center'><b>Отправка изображения как открытки</b></td></tr>";

if ($user_id==0) $out.="<tr><td class='form_left'>Ваше имя</td><td class='form_main'><div><input class='form_main' type='text' name='sender_name' size='65'></div></td></tr>";
$out.="<tr><td class='form_left'>E-mail получателя</td><td class='form_main'><div><input class='form_main' type='text' name='reciever_mail' size='65'></div></td></tr><tr><td class='form_left'>Текст</td><td class='form_main'><textarea class='form' name='send_text' rows='5' cols='65'></textarea></td></tr><tr><td class='form_left'>Код (защита от спама):</td><td class='form_main'><img src='".$antispam['pic']."' border='1' width='100' height='25'><br><input style='width:100px' name='a_s_u'></td></tr><tr><td><input type=hidden name=a_s_t value='".$antispam['code']."'><input type=hidden name=a_s_p value='".$antispam['pic']."'></td><td style='padding-top:10;'><input class='form_button' type='submit' name='button_submit' value='Отправить'></td></tr></table></form>";
return($out);
}



function picture_save()
{
	global $sql_pref, $conn_id;
	if (isset($_REQUEST['parent_id'])) $parent_id=$_REQUEST['parent_id'];
	if (isset($_REQUEST['descr'])) {$descr=strip_tags($_REQUEST['descr']); $descr=trim($descr); $descr=addslashes($descr);} else $descr="";
	if (isset($_REQUEST['user_id'])) $user_id=$_REQUEST['user_id'];

$enable="Yes";
$moderation="Yes";

	$date_upload=time();

if (is_uploaded_file( $_FILES['img_name']['tmp_name'])) 
	{
	$data_file=picture_file_save();
		if (is_array($data_file))
		{
		$file_name=$data_file["name"];
		$file_size=$data_file["size"];
		$format=$data_file["type"];
		$out="<div>Картинка успешно добавлена и будет доступна после модерации. Спасибо.</div>";
		$out.="Файл ".$file_name." (".$file_size." Мб)<BR>";
		$sql_query="INSERT INTO ".$sql_pref."_picture (parent_id, descr, file_name, file_size, format, user_id, date_upload, enable, moderation) VALUES ('".$parent_id."', '".$descr."', '".$file_name."', '".$file_size."', '".$format."', '".$user_id."','".$date_upload."', '".$enable."', '".$moderation."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$descr=stripslashes($descr);
		$out.=$descr."<BR>";
		}
		else $out.=$data_file;
	
	}
	else $out.="<div>Не указан файл или превышен его допустимый размер</div>";
	return $out;
}



function picture_send()
{
	global $sql_pref, $conn_id, $path_picture, $path_domen;
	if (isset($_REQUEST['pic_id'])) $pic_id=$_REQUEST['pic_id'];
	if (isset($_REQUEST['pic_file_name'])) $pic_file_name="files/picture/".$_REQUEST['pic_file_name'];
	if (isset($_REQUEST['user_id'])) $user_id=$_REQUEST['user_id'];
	if (isset($_REQUEST['sender_name'])) $sender_name=$_REQUEST['sender_name']; else $sender_name=get_user_name_by_id($user_id);
	if (isset($_REQUEST['reciever_mail'])) $reciever_mail=$_REQUEST['reciever_mail'];
	if (isset($_REQUEST['send_text'])) $send_text=$_REQUEST['send_text'];
    $antispam_user=$_REQUEST['a_s_u'];
    $antispam_true=$_REQUEST['a_s_t'];
    $antispam_pic=$_REQUEST['a_s_p'];
   
    if($antispam_user!=$antispam_true)
    {
        $out.="Антиспам код введен не верно!";
    }
    else
    {
        $date_sending=time();

        $sender_name=strip_tags($sender_name);
        $reciever_mail=strip_tags($reciever_mail);
        $send_text=strip_tags($send_text);
        
        if(is_email_valid($reciever_mail))
        {
        	$letter_name="Вам открытка от пользователя ".$sender_name." с сайта энергетиков";
        	$letter_content_send="<b>Здравствуйте!</b><br> Пользователь ".$sender_name." прислал Вам открытку с <a href='http://".$path_domen."'>сайта энергетиков</a>.<br>";
        	$letter_content_send.="Для просмотра открытки перейдите по ссылке или скопируйте её в адресную строку браузера:<br>";
        
        
        $sender_name=addslashes($sender_name);
        $reciever_mail=addslashes($reciever_mail);
        $send_text=addslashes($send_text);
        
        	$sql_query="INSERT INTO ".$sql_pref."_picture_senders (pic_id, sender_id, sender_name, reciever_mail, send_text, date_sending, enable) VALUES ('".$pic_id."', '".$user_id."', '".$sender_name."', '".$reciever_mail."', '".$send_text."', '".$date_sending."','Yes')";
        	$sql_res=mysql_query($sql_query, $conn_id);
        	$cur_sending_id=mysql_insert_id();
        
        	$letter_content_send.="<a href='http://".$path_domen."/".$path_picture."/postcard.html?post_id=".$cur_sending_id."'>http://".$path_domen."/".$path_picture."/postcard.html?post_id=".$cur_sending_id."</a>";
        
        	send_mail_to_somebody($reciever_mail, $letter_name, $letter_content_send, $pic_file_name);
        	$out.="Открытка отправлена. ";
        	$out.="<a href='/".$path_picture."/postcard.html?post_id=".$cur_sending_id."'>Посмотреть открытку</a>";
        
        }
        else $out.="Неправильно указан e-mail адрес";
    }       

return $out;
}



function make_dir($dir_path)
{
if (!is_dir($dir_path)) mkdir($dir_path, 0777);
		chmod ($dir_path, 0777);
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


function save_img($src, $dest, $resize, $width, $height, $quality=80, $mime)
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



function picture_file_save()
{
	global $path, $conn_id, $sql_pref;

	$orig_name=$_FILES['img_name']['name'];
	$orig_name=file_name_norm($orig_name);

	$exist=mysql_query("select count(if(file_name='$orig_name',1,NULL)) from ".$sql_pref."_picture");
	$r=mysql_fetch_row($exist);
	$name=$orig_name;
	for ($i=1;$r[0]>0;$i++)
	{
		$name=$i."_".$orig_name;
		$exist=mysql_query("select count(if(file_name='$name',1,NULL)) from ".$sql_pref."_picture");
		$r=mysql_fetch_row($exist);
	}
	
	$mime=$_FILES['img_name']['type'];
	$size=$_FILES['img_name']['size'];
		$dest=$path."files/picture/".$name;
		$tn_dest=$path."files/picture/tn_".$name;
		$src=$_FILES["img_name"]["tmp_name"];
		if($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif") 
		{
		$resize=true;
		save_img($src, $dest, $resize, 630, 650, 100, $mime);
		save_img($dest, $tn_dest, $resize, 180, 180, 80, $mime);
		picture_add_logo($dest);
		$data_file['name']=$name;
		$data_file['type']=$mime;
		$data_file['size']=round($size/1048576, 2);
		return $data_file;
		}
		else return "Ошибка! Недопустимый формат файла. К загрузке допускаются файлы JPG";
}



//Функция нормализации имени файла
function file_name_norm($name)
	{
	$base_name_norm="";
	$names=explode(".",$name);
	$extension=$names[count($names)-1];
	$base_name=str_replace(".".$extension,"", $name);
	$extension=strtolower($extension);
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


//Функция уменьшения картинок

function resize_img($dir, $file){
	$size=630;
	$orig_image=imageCreatefromJpeg("$dir/$file");
	$SX=imageSX($orig_image);
	$SY=imageSY($orig_image);
	if ($SX>$size){
	$prop=$SX/$SY;
	$new_x=$size;
	$new_y=$new_x/$prop;
	$new_img=imageCreateTrueColor($new_x, $new_y);
	imageCopyResampled($new_img, $orig_image,0,0,0,0,$new_x,$new_y,$SX,$SY);
	ImageJpeg($new_img, "$dir/$file");
	}
}

//перекодировка текста для картинок
function convert_pictext($s){
$s = convert_cyr_string($s,'w','i'); 
for ($result='', $i=0; $i<strlen($s); $i++) { 
$charcode = ord($s[$i]); 
$result .= ($charcode>175)?"&#".(1040+($charcode-176)).";":$s[$i]; 
}
return $result;
}

//Функция добавления логотипа в картинку

function picture_add_logo($file){
global $path;
$ratio=0.125;
	$logo_range=array(50,60,70,80,90,100,110);
	$orig_image=imageCreatefromJpeg("$file");
	$origX=imageSX($orig_image);
	$origY=imageSY($orig_image);
	$logoX=$origX*$ratio;
	$logoX=round($logoX, -1);
	if($logoX<50) $logoX=50;
	if($logoX>110) $logoX=110;
	$logo_file=$path."files/picture/".$logoX."_pic_logo.jpg";
	$logo_image=imageCreatefromJpeg("$logo_file");
	$logoY=imageSY($logo_image);
	$X=$origX-$logoX;
	$Y=$origY-$logoY;
	imageCopyMerge($orig_image, $logo_image, $X,$Y,0,0,$logoX,$logoY,100);
	ImageJpeg($orig_image, "$file");
}


function picture_comments($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_picture, $months_rus1, $user_id, $user_status, $path_users, $art_url;
	$out="";
	$out.="<a name=comments></a>";
    $out.="<div style='padding: 25 0 15 0;'>";
	$out.="<h2 style='margin: 3 0 3 0;font-size:18px;'>Комментарии</h2>\n";
	$out.="<table cellpadding=0 cellspacing=0 border=0 width=100% height=1><tr height=1><td height=1 align=center bgcolor=#999999 background='/img/dots-hor.gif'><img src='/img/empty.gif' height=1 border=0></td></tr></table>\n";
	$out.="<div style='padding: 0 0 15 0;'>";
	$sql_query="SELECT id, content, user_id, dt FROM ".$sql_pref."_picture_comments WHERE parent_id='".$parent_id."' ORDER BY dt";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$comments_num=mysql_num_rows($sql_res);
//		$out.="<div style='margin: 3 0 3 0;'>Всего: <b>".$comments_num."</b></div><br>\n";
		while(list($id, $content, $commentator_id, $dt)=mysql_fetch_row($sql_res))
		{
            $content=StripSlashes($content);
            
			$sql_query="SELECT name, surname FROM ".$sql_pref."_users WHERE id='".$commentator_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			list($name, $surname)=mysql_fetch_row($sql_res_1);
			
			$name=StripSlashes($name); $surname=StripSlashes($surname);
			$content=str_replace("\n","<br>",$content);
			$content=preg_replace("#(?<!=)(?<!\")(?<!\')(https?|ftp)://\S+[^\s.,>)\];'\"!?]#",'<a href="\\0">\\0</a>',$content);
			$date_show=date("d.m.Y H:i", strtotime($dt));
			$name_show="<a href='/".$path_users."/".$commentator_id.".html'>".$name." ".$surname."</a>";
			
			if ($user_status=="admin") $del_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_picture."/".$art_url.".html?action=comment_del&id=".$parent_id."&comment_id=".$id."'\"  style='font-size:9px;color:#999999;'>Удалить</a>"; else $del_but="";
			
			$out.="<div style='margin: 5 0 5 0;'><span style='font-size:14px;font-weight:normal;'>".$name_show."</span><br><span style='color:#999999;font-size:11px;'> ".$date_show."</span>".$del_but."</div>";
			$out.="<div style='margin: 5 0 5 20;'>".$content."</div>";
			$out.="<br>";
		}
	}
	else $out.="<div style='margin: 5 0 5 0;'>Пока нет.</div>\n";
	$out.="</div>";
	$out.=picture_comments_form($parent_id);
    $out.="</div>";
	return ($out);
}







function picture_comments_form($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_picture, $user_id;
	$out="";
	if ($user_id==0) return ("<div>Комментарии могут оставлять только <a href='/auth/register/'>зарегистрированные</a> пользователи</div>");

	$out.="<h2 style='margin: 3 0 3 0;'>Ваш комментарий</h2>\n";
	$out.="<script language='Javascript'>
		function check_form()
		{
			var str = 'OK';
			if (document.getElementById('content1').value=='') str='КОММЕНТАРИЙ';
			return str;
		}
		</script>";
	$out.="<form action='' method='get' name='form_comments' onSubmit='if (check_form()!=\"OK\") {alert(\"Вы не заполнили поле \" + check_form() + \"!\"); return false;} else return true;'>";


	$out.="<div>
					<textarea id=content1 name=content1 rows=4 style='overflow: auto; font-size: 12px;width:500px;'>".@$content."</textarea>
				 </div>";
	$out.="<span style='color:#777777;font-size:11px;'><br>Просьба оставлять комментарии только по теме!</span><br><br>";
	$out.="<div><input type=hidden name=action value='comment_add'>";
	$out.="<div><input type=hidden name=id value='$parent_id'>";
	$out.="<input class='button' type='submit' value='Отправить' name='add' style='padding: 2 2 2 2; font-size: 10px; font-weight: bold; background-color: transparent; color: #3E3E3E; border: 1px solid #CCCCCC;'></div>";
	$out.="</form>";
	$out.="";
	$out.="<br><br>";
	return ($out);
}







function picture_comments_form_save($id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_picture, $user_id, $art_url, $user_rate_main, $user_rate_sec;
	
	if (isset($_REQUEST['content1']) && !empty($_REQUEST['content1']))
	{
		$dt=date("Y-m-d H:i:s");
        $parent_id=$id;

		if (isset($_REQUEST['content1'])) $content=AddSlashes(strip_tags($_REQUEST['content1'], '<br>, <b>, <i>, <u>')); else $content="";
		
		$sql_query="INSERT INTO ".$sql_pref."_picture_comments (content, user_id, parent_id, dt) VALUES ('".$content."', '".$user_id."', '".$parent_id."', '".$dt."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		rate_main($user_id, "добавил комментарий", $user_rate_main, $user_rate_sec);
        
        $sql_query2="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'picture_comment','".$parent_id."')";
        $sql_res2=mysql_query($sql_query2, $conn_id);
        
		header("location:/".$path_picture."/".$art_url.".html?id=".$id."#comments"); exit();
		exit();
	}
	return;
}







function picture_comments_del($id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_picture, $user_id, $user_status, $art_url;
	$out="";
	
	if (!isset($_REQUEST['comment_id']) || ($_REQUEST['comment_id']<=0)) return;
    
	$sql_query="SELECT id FROM ".$sql_pref."_picture_comments WHERE id='".$_REQUEST['comment_id']."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0 && $user_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	
	$sql_query="DELETE FROM ".$sql_pref."_picture_comments WHERE id='".$_REQUEST['comment_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);

	header("location:/".$path_picture."/".$art_url.".html?id=".$id."#comments"); exit();
}





?>