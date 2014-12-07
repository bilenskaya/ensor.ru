<?php

function starting()
{
	global $url_decode,$mainpage,$check_auth;
	require_once("fns/out.php");
	require_once("fns/url.php");
	main_header();
	sql_connect();
	if ($check_auth=="Yes")
	{
		require_once("fns/auth.php");
		auth_maincheck();        
	}
	$url_decode=$turl=$_SERVER['REQUEST_URI'];
	if (strpos($turl,"?")) $turl=substr($turl,0,strpos($turl,"?"));
	if ($turl!='/' && $turl!='/index.html' && $turl!='/index_new.html' && $turl!='/feedback.html' && $turl!='/reg_select.php' && $turl!='/add_vote.php' && $turl!='/search.php') {url_decode($url_decode);$mainpage="No";} else $mainpage="Yes";
}









function main_header()
{
	header("Cache-Control: max-age=-1, must-revalidate");
	header("Expires: ".gmdate("D, d M Y H:i:s", time()-3600)." GMT");
	header("Content-type: text/html; charset=windows-1251");
	header("Pragma: no-cache");
}










function sql_connect()
{
	global $sql_database, $sql_host, $sql_login, $sql_passwd;
	global $conn_id;
	$conn_id=mysql_connect($sql_host, $sql_login, $sql_passwd);
	mysql_select_db($sql_database);
}










function sql_close()
{
	global $conn_id;
	mysql_close($conn_id);
}










function is_email_valid($str)
{
	if (!eregi("^[a-zA-Z0-9_\.-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$", $str)) return false;
	return true;
}


function send_mail_to_user($to_user_id, $letter_name, $letter_content_send)
{
    global $sql_pref, $path_domen, $conn_id; 
    $sql_query="SELECT id, surname, name, name2, email FROM ".$sql_pref."_users WHERE id='".$to_user_id."' AND maillist='Yes'";
    //echo $sql_query;
    $sql_res=mysql_query($sql_query, $conn_id);
    $res="Ошибка отправки письма";
    if (mysql_num_rows($sql_res)>0)
    {
        list($id, $surname, $name, $name2, $email)=mysql_fetch_row($sql_res);
        
        $name=$surname." ".$name." ".$name2;
        
        if (!empty($name)) $toemail="".$name." <".$email.">";
        else $toemail=$email;
        
    	$mailheader="";
    	$mailheader.="From: Ensor.ru <robot@".$path_domen.">\r\n";
        //if (!empty($name)) $mailheader.="To: ".$name." <".$email.">\r\n";
    	$mailheader.="MIME-Version: 1.0\r\n";
        $mailheader.="Content-Type: text/html;\n charset=\"WINDOWS-1251\"";
        //echo $mailheader;
        $letter_content_send="Здравствуйте, ".$name.".\n".$letter_content_send;
        
        if(mail($toemail, $letter_name, $letter_content_send, $mailheader))
        {
            $dt_send=date("Y-m-d H:i:s");
            $res="Письмо отправлено ".$dt_send;            
        }
        else
        {
            $res="Письмо не отправлено ";
        }        
    }
    return ($res);        
}



function send_mail_to_somebody($reciever_mail, $subj, $text, $filename) 
{
global $sql_pref, $path_domen, $conn_id; 

if(is_email_valid($reciever_mail))
{

$text="<html>".$text."</html>";

$subj=convert_cyr_string($subj,"w","k");
$text=convert_cyr_string($text,"w","k");


if (!strlen($filename)==0) $f= fopen($filename,"rb"); 
$un=strtoupper(uniqid(time())); 
$head= "From: Ensor.ru <robot@".$path_domen.">\r\n"; 
$head.= "To: ".$reciever_mail."\n"; 
$head.= "Subject: ".$subj."\n";
$head.= "X-Mailer: PHPMail Tool\n"; 
$head.= "Mime-Version: 1.0\n"; 
$head.= "Content-Type:multipart/mixed;"; 
$head.= "boundary=\"----------".$un."\"\n\n";
$zag= "------------".$un."\nContent-Type:text/html; charset=koi8-r\n"; 
$zag.= "Content-Transfer-Encoding: 8bit\n\n".$text."\n\n"; 
$zag.= "------------".$un."\n"; 
if (!strlen($filename)==0) {
	$zag.= "Content-Type: application/octet-stream;"; 
	$zag.= "name=\"".basename($filename)."\"\n"; 
	$zag.= "Content-Transfer-Encoding:base64\n"; 
	$zag.= "Content-Disposition:attachment;"; 
	$zag.= "filename=\"".basename($filename)."\"\n\n"; 
	$zag.= chunk_split(base64_encode(fread($f,filesize($filename))))."\n"; 
}

if (!@mail($reciever_mail, $subj, $zag, $head)) 
return "Ошибка отправки сообщения"; 
else 
return "Сообщение отправлено";
}
} 


function is_pass_valid($str)
{
	if (!eregi("^[a-zA-Z0-9]+$", $str)) return false;
	return true;
}











function cut_string($str, $maxlen)
{
	$massiv=explode(" ", $str);
	$kol=count($massiv);
	$out="";
	for ($i=0; $i<$kol; $i++)
	{
		if (strlen($massiv[$i])>$maxlen) $massiv[$i]=substr($massiv[$i], 0, ($maxlen-3))."...";
		$out.=$massiv[$i]." ";
	}
	return ($out);
}










function cut_descr($str, $maxlen)
{
	$massiv=explode(" ", $str);
	$kol=count($massiv);
	$fl=0;
	$out="";
	$descr="";
	for ($i=0; $i<$kol; $i++)
	{
		if ((strlen($descr)+strlen($massiv[$i])+1)<$maxlen) $descr.=" ".$massiv[$i]; else { $fl=1;continue;}
	}
	$out.=$descr;
	if ($fl==1) $out.="...";
	return ($out);
}










// Функция обрабатывает существительное, склоняя его в соответствии с переданным параметром-числительным
function pluralForm($n, $form1, $form2, $form5)
{
	$n = abs($n) % 100;
	$n1 = $n % 10;
	if ($n > 10 && $n < 20) return ($form5);
	if ($n1 > 1 && $n1 < 5) return ($form2);
	if ($n1 == 1) return ($form1);
	return ($form5);
}










// Очистка каталога
function empty_cat($dir_path)
{
	$dh = opendir($dir_path);
	while($file = readdir($dh))
	{
		if ($file!="." && $file!="..")
		{
			unlink ($dir_path."/".$file);
		}
	}
	closedir($dh);
	return true;
}










function antispam_image($string)
{
	global $path;
	$out="";
	$microtime=microtime();
	empty_cat($path."files/antispam");
	$img = imagecreatetruecolor(100,50);
	$black = ImageColorAllocate($img, 0, 0, 0);
	$red = ImageColorAllocateAlpha ($img, 255, 0, 0, 75);
	$green = ImageColorAllocateAlpha($img, 0, 255, 0, 75);
	$blue = ImageColorAllocateAlpha($img, 0, 0, 255, 75);
	$white = ImageColorAllocateAlpha($img, 255, 255, 255, 75);
	$trans = ImageColorTransparent($img, $white);
	ImageFill($img, 0, 0, $white);
	//ImageString($img , 2, 10, 10, "Laa is so happy to see you!", $black);
	ImageAlphaBlending($img, true);
	ImageTTFText($img, 30, 10, 25, 45, $green, $path."img/hobbyh.ttf", substr($string, 1, 1));
	ImageTTFText($img, 40, -10, 35, 40, $red, $path."img/hobbyh.ttf", substr($string, 2, 1));
	ImageTTFText($img, 30, 10, 65, 40, $blue, $path."img/hobbyh.ttf", substr($string, 3, 1));
	ImageGif($img, $path."files/antispam/".$microtime.".gif");
	ImageDestroy($img);
	$out.="<img src='/files/antispam/".$microtime.".gif' width='100' height='50' alt='' title='' style='border: 1px solid #7F9DB9;'/>";
	return($out);
}










function antispam_string()
{
	$out="";
	$massiv=array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9");
	$count=count($massiv)-1;
	for ($i=0; $i<6; $i++)
	{
		$random=rand(0, $count);
		$out.=$massiv[$random];
	}
	return ($out);
}








function email_valid($str)
{
	if (!eregi("^[a-zA-Z0-9_\.-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$", $str)) return false;
	return true;
}











function translit_url($str, $length=20)
{
	setlocale (LC_ALL, "ru_RU.CP1251");
	$str=strtolower($str);
	$str=preg_replace("/[^a-zа-я0-9_-]/"," ",$str); //заменяем запрещенные символы
	$str=trim($str); // удаляем пробелы в начале и в конце
	$str=preg_replace("/ +/"," ",$str); //удаляем сдвоенные пробелы
	$str=str_replace(" ","_",$str); //заменяем пробелы
	$trans = array("а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "e", "ж" => "j", "з" => "z", "и" => "i", "й" => "y",
					"к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f",
					"х" => "h", "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "she", "ъ" => "", "ы" => "iy", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ja");
	$str = strtr($str, $trans);
	$str = substr($str,0,$length);
  return ($str);
}










function common_del_file($full_name)
{
	if (file_exists($full_name)) unlink ($full_name);
}







function common_save_img($src, $dest, $resize, $width, $height, $quality=80, $mime="image/jpeg")
{
	global $path;
	$size_limit=6.2;
	if (is_uploaded_file($src) || file_exists($src))
	{
		if (is_uploaded_file($src)) move_uploaded_file($src, $dest);
		if (file_exists($src)) copy($src, $dest);
		chmod ($dest, 0644);
		$size=getimagesize($dest);
		if ($size[0]*$size[1]/1000000 < $size_limit) $size_ok=TRUE; else $size_ok=FALSE;
		if ($resize==true && ($size[0]>$width || $size[1]>$height) && $size_ok)
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
		elseif($resize==true && ($size[0]>$width || $size[1]>$height) && !$size_ok) {common_del_file($dest); return "Превышен допустимый размер изображения (не более $size_limit мегапикселей)<BR>";}
	}
}


















function pages_nums($get_page, $get_perpage, $get_sql_query)
{
	global $sql_pref, $conn_id;
	
	$out="";
	
	$page=$get_page;
	$perpage=$get_perpage;
	
	$sql_query=$get_sql_query;
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_posts=mysql_num_rows($sql_res);
	$numpages=ceil($num_posts/$perpage);
	if ($numpages>1)
	{
		$out.="<div>";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<span style='background-color:#E8E9EC;padding:2 4 2 4;'>".$i."</span>"; else $i_show="<a href='?page=".$i."' style='text-decoration:underline;'>".$i."</a>";
			$out.="<span style='padding:2 4 2 4;background-color:#ffffff;border:solid 0px #aaaaaa;'>".$i_show."</span> ";
		}
		$out.="</div>";
	}
	
	return($out);
}

function pages_nums_with_args($get_page, $get_perpage, $get_sql_query,$args)
{
	global $sql_pref, $conn_id;
	
	$out="";
	
	$page=$get_page;
	$perpage=$get_perpage;
	
	$sql_query=$get_sql_query;
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_posts=mysql_num_rows($sql_res);
	$numpages=ceil($num_posts/$perpage);
	if ($numpages>1)
	{
		$out.="<div>";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<span style='background-color:#E8E9EC;padding:2 4 2 4;'>".$i."</span>"; else $i_show="<a href='?page=".$i.$args."' style='text-decoration:underline;'>".$i."</a>";
			$out.="<span style='padding:2 4 2 4;background-color:#ffffff;border:solid 0px #aaaaaa;'>".$i_show."</span> ";
		}
		$out.="</div>";
	}
	
	return($out);
}

function rate_main($rate_user_id,$rate_act_type,$rate_main_val,$rate_sec_val)
{
    global $sql_pref, $conn_id, $user_status;

    switch ($rate_act_type) {
        case "посетил сайт":
            if($user_status=="admin")
            {                
                $sql_query="UPDATE ".$sql_pref."_users SET last_visit='".date("Y-m-d")."' WHERE id='".$rate_user_id."'";
                $sql_res=mysql_query($sql_query, $conn_id);
            }
            else
            {
                $rate_sec=$rate_sec_val+4;
                $sql_query="UPDATE ".$sql_pref."_users SET last_visit='".date("Y-m-d")."', rate_sec=".$rate_sec." WHERE id='".$rate_user_id."'";
                $sql_res=mysql_query($sql_query, $conn_id);
            }
            break;
        case "добавил комментарий":
            $rate_sec=$rate_sec_val+4;
            $sql_query="UPDATE ".$sql_pref."_users SET rate_sec=".$rate_sec." WHERE id='".$rate_user_id."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
            break;
        case "добавил картинку":
            $rate_sec=$rate_sec_val+4;
            $sql_query="UPDATE ".$sql_pref."_users SET rate_sec=".$rate_sec." WHERE id='".$rate_user_id."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
            break;
        case "пригласил коллегу":
            $rate_sec=$rate_sec_val+12;
            $rate_main=$rate_main_val+12;
            $sql_query="UPDATE ".$sql_pref."_users SET rate_sec=".$rate_sec.", rate_main=".$rate_main." WHERE id='".$rate_user_id."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
            break;
        case "оценил вопрос":
            $rate_sec=$rate_sec_val+1;
            $rate_main=$rate_main_val+1;
            $sql_query="UPDATE ".$sql_pref."_users SET rate_sec=".$rate_sec.", rate_main=".$rate_main." WHERE id='".$rate_user_id."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
            break;
        default :
}
    
    
}

function action_check($action_type,$row_id)
{
	global $sql_pref, $conn_id, $path;
	global $user_id;
	
	$sql_query="SELECT * FROM ".$sql_pref."_users_action WHERE user_id=".$user_id."&&action_type='".$action_type."'&&row_id=".$row_id;
	$sql_res=mysql_query($sql_query, $conn_id);
	if(mysql_num_rows($sql_res)==0) $act_check="false"; else $act_check="true";	
    return $act_check;	
}

function get_blog_activity($delT,$view)
{    
    
}

function get_articles_activity($delT,$view)
{
        
}

function get_news_activity($delT,$view)
{
        
}


function get_user_name_by_id($id)
{
    global $conn_id, $sql_pref, $path_contacts, $path_articles, $path_news, $path_blogs, $path_forum, $path_questions, $path_picture;
    
    $out="";
    
    $sql_query="SELECT name, surname FROM ".$sql_pref."_users WHERE id=".$id;
    $sql_res=mysql_query($sql_query, $conn_id);
    while (list($name, $surname)=mysql_fetch_row($sql_res))
	{
        $out.= $surname." ".$name;  
    }   
    return $out;    
}

function get_company_name_by_id($id)
{
    global $conn_id, $sql_pref, $path_contacts, $path_articles, $path_news, $path_blogs, $path_forum, $path_questions, $path_picture;
    
    $out="";
    
    $sql_query="SELECT name, name_full FROM ".$sql_pref."_companies WHERE id=".$id;
    $sql_res=mysql_query($sql_query, $conn_id);
	while (list($name, $name_full)=mysql_fetch_row($sql_res))
	{
        $out.= $name_full." <-> ".$name;  
    } 
    return $out;    
}

function out_activity_main($row_count)
{
    global $conn_id, $sql_pref, $path_contacts, $path_articles, $path_news, $path_blogs, $path_forum, $path_questions, $path_picture;
    
    $sql_query="SELECT ".$sql_pref."_users_action.row_id, ".$sql_pref."_users_action.id, ".$sql_pref."_users_action.date, ".$sql_pref."_users_action.user_id, ".$sql_pref."_users_action.action_type FROM ".$sql_pref."_users_action INNER JOIN ".$sql_pref."_users ON (".$sql_pref."_users_action.user_id=".$sql_pref."_users.id) WHERE ".$sql_pref."_users.enable='Yes' AND ".$sql_pref."_users_action.action_type NOT LIKE 'send_message' ORDER BY ".$sql_pref."_users_action.date DESC LIMIT ".$row_count;
	//echo $sql_query;
    $sql_res=mysql_query($sql_query, $conn_id);
	while (list($row_id, $id, $dt, $user_, $action_type)=mysql_fetch_row($sql_res))
	{
        $sql_query2="SELECT id, surname, name, name2, pol FROM ".$sql_pref."_users WHERE enable='Yes' AND id=".$user_;   
        $sql_res2=mysql_query($sql_query2, $conn_id);
        if(mysql_num_rows($sql_res2)>0)
        {
            list($user_, $surname2, $name2, $name22, $pol)=mysql_fetch_row($sql_res2);
            $comm_name=$surname2." ".substr($name2,0,1).". ".substr($name22,0,1).".";
            $comm_name="<a href='/".$path_contacts."/".$user_.".html'>".$comm_name."</a>";
            //echo $pol;
            if($pol=="w") $str_add="а"; else $str_add="";            
        }
        
        switch ($action_type) {
        case "question_comment":
            $act_str=" добавил".$str_add." комментарий к <a href='/".$path_questions."/".$row_id.".html'>вопросу</a>";
            break;
        case "news_comment":
            $act_str=" добавил".$str_add." комментарий к <a href='/".$path_news."/".$row_id.".html'>новости</a>";
            break;
        case "articles_comment":
            $sql_queryArt="SELECT url FROM ".$sql_pref."_articles WHERE enable='Yes' AND id=".$row_id;   
            $sql_resArt=mysql_query($sql_queryArt, $conn_id);
            if(mysql_num_rows($sql_resArt)>0)
            {
                list($art_url)=mysql_fetch_row($sql_resArt);         
            }
            $act_str=" добавил".$str_add." комментарий к <a href='/".$path_articles."/".$art_url.".html#comments'>статье</a>";
            break;
        case "add_post":
            $act_str=" добавил".$str_add." сообщение в <a href='/".$path_blogs."/".$user_."/'>блог</a>";
            break;
        case "blog_comment":
            $sql_queryBlog="SELECT user_id, url FROM ".$sql_pref."_blogs2_posts WHERE visible='Yes' AND id=".$row_id;   
            $sql_resBlog=mysql_query($sql_queryBlog, $conn_id);
            if(mysql_num_rows($sql_resBlog)>0)
            {
                list($user_id_blog, $post_url)=mysql_fetch_row($sql_resBlog);
                $path_blog_post=$user_id_blog."/".$post_url;           
            }
            $act_str=" добавил".$str_add." комментарий к <a href='/".$path_blogs."/".$path_blog_post.".html'>блогу</a>";
            break;     
        case "add_question":
            $act_str=" добавил".$str_add." <a href='/".$path_questions."/".$row_id.".html'>вопрос</a>";
            break;    
        case "add_forum_post":
            $act_str=" добавил".$str_add." сообщение в <a href='/".$path_forum."/lastposts/'>форум</a>";
            break;            
        case "user_invite":
            $act_str=" пригласил".$str_add." коллегу";
            break;  
        case "user_info_change":
            $act_str=" изменил".$str_add." данные своего <a href='/".$path_contacts."/".$row_id.".html'>профиля</a>";
            break;
        case "picture_comment":
            $act_str=" добавил".$str_add." комментарий к <a href='/".$path_picture."/0.html?id=".$row_id."'>картинке</a>";
            break;
        case "add_picture":
            $act_str=" добавил".$str_add." <a href='/".$path_picture."/0.html?id=".$row_id."'>картинку</a>";
            break;    
        default :
            $act_str="";
            break;
      }  
		if($act_str!="") $ret_string.="<span class=dates>".date("m-d H:i",strtotime($dt))."</span> ".$comm_name.$act_str."<br><br>";        
	}	
    if($ret_string<>"") $ret_string="<h3 style='padding: 20px 20px 10px 20px;'>Лента активности пользователей</h3><div style='padding: 0px 20px 20px 20px;'>".$ret_string."</div>";
    return $ret_string;    
}


function out_activity_persons($row_count)
{
    global $conn_id, $sql_pref, $path_contacts;
    
    $sql_query="SELECT id, name, name2, surname, rate_sec FROM ".$sql_pref."_users WHERE enable='Yes' ORDER BY rate_sec DESC LIMIT ".$row_count;
	$sql_res=mysql_query($sql_query, $conn_id);
    $ret_string="<table width=80%>";
	while (list($id, $name, $name2, $surname, $rate_sec)=mysql_fetch_row($sql_res))
	{
        $comm_name=$surname." ".substr($name,0,1).". ".substr($name2,0,1).".";         
		$ret_string.="<tr><td align='left'><a href='/".$path_contacts."/".$id.".html'>".$comm_name."</a></td><td align='right'> ".$rate_sec." <br></td></tr>";        
	}	
    $ret_string.="</table>";
    
    if($ret_string<>"") $ret_string="<h3 style='padding: 20px 20px 10px 20px;'>Рейтинг активных пользователей</h3><div style='padding: 0px 20px 20px 20px;'>".$ret_string."</div>";
    return $ret_string;    
}

function out_new_persons($row_count)
{
    global $conn_id, $sql_pref, $path_contacts;
    
    $sql_query="SELECT id, name, name2, surname, dt_reg FROM ".$sql_pref."_users WHERE enable='Yes' ORDER BY dt_reg DESC LIMIT ".$row_count;
	$sql_res=mysql_query($sql_query, $conn_id);
    $ret_string="<table>";
	while (list($id, $name, $name2, $surname, $dt_reg)=mysql_fetch_row($sql_res))
	{
        $comm_name=$surname." ".substr($name,0,1).". ".substr($name2,0,1).".";         
        $ret_string.="<tr><td align='left'><span class=dates>".date("m-d H:i",strtotime($dt_reg))."</span>&nbsp;<a href='/".$path_contacts."/".$id.".html'>".$comm_name."</a><br><br></td></tr>";     
        $ret_string.="<tr><td></td></tr>";   
	}	
    $ret_string.="</table>";
    if($ret_string<>"") $ret_string="<h3 style='padding: 20px 20px 10px 20px;'>\"Присоединившиеся\" энергетики</h3><div style='padding: 0px 20px 20px 20px;'>".$ret_string."</div>";

    return $ret_string;    
}

function out_now_persons($row_count,$duration)
{
    global $conn_id, $sql_pref, $path_contacts;
    
    $sql_query="SELECT id, name, name2, surname, dt_reg FROM ".$sql_pref."_users WHERE enable='Yes' AND TIMESTAMPDIFF( 
MINUTE ,  TIMESTAMP(at_site_now), '".date("Y-m-d H:i:s")."')<=".$duration." ORDER BY at_site_now DESC LIMIT ".$row_count;
	//echo $sql_query;
    $sql_res=mysql_query($sql_query, $conn_id);
    $ret_string="<table>";
	while (list($id, $name, $name2, $surname, $dt_reg)=mysql_fetch_row($sql_res))
	{
        $comm_name=$surname." ".substr($name,0,1).". ".substr($name2,0,1).".";         
        $ret_string.="<tr><td align='left'><a href='/".$path_contacts."/".$id.".html'>".$comm_name."</a><br><br></td></tr>";     
        $ret_string.="<tr><td></td></tr>";   
	}	
    $ret_string.="</table>";
    if($ret_string<>"") $ret_string="<h3 style='padding: 20px 20px 10px 20px;'>Сейчас на сайте</h3><div style='padding: 0px 20px 20px 20px;'>".$ret_string."</div>";

    return $ret_string;    
}

function out_top_info($row_count,$duration)
{
    global $conn_id, $sql_pref, $path_news;
    
    $sql_query="SELECT id, name FROM ".$sql_pref."_news WHERE enable='Yes' AND TIMESTAMPDIFF( 
DAY ,  top_time, '".date("Y-m-d H:i:s")."')<=".$duration." ORDER BY top_time DESC LIMIT ".$row_count;
	//$ret_string = $sql_query;
    $sql_res=mysql_query($sql_query, $conn_id);
    $ret_string="<table cellspacing='0' cellpadding='0' border='1' width=100%>";
	$n=1;
    while (list($id, $name)=mysql_fetch_row($sql_res))
	{             
	    $name=stripslashes($name);
        $name=substr($name,0,80);
        if(intval($n/2)==$n/2) $bgcolor='#02436F'; else $bgcolor='#E75635';
        $ret_string.="<tr><td align=center width=80% style='vertical-align: middle;' align='left'><a class='topmenu' href='/".$path_news."/".$id.".html'><font style='color: ".$bgcolor.";'>Новость: <b>".$name."</b>... Читать далее...</font></a></td></tr>";   
	    $n=$n+1;
    }	
    $ret_string.="</table>";
    
    return $ret_string;    
}

//Функция кодовой картинки
function show_codepic(){
    global $path;
    
    $directory=$path."img/codepic/";
    // открываем директорию (получаем дескриптор директории) 
    $dir = opendir($directory);    
    // считываем содержание директории 
        while(($file = readdir($dir))) 
        { 
           // Если это файл и он равен удаляемому ... 
          if(is_file($directory."/".$file))
           { 
             unlink($directory."/".$file);                            
           } 
        } 
      // Закрываем дескриптор директории. 
      closedir($dir); 

    
    mt_srand(time()+(double)microtime()*1000000);
    $code=mt_rand(1000,9999);
    
    // создаем изображение
    $im=imagecreate(101, 26);
    
    // Выделяем цвет фона (белый)
    $w=imagecolorallocate($im, 255, 255, 255);
     
    // Выделяем цвет для фона (светло-серый)
    $g1=imagecolorallocate($im, 192, 192, 192);
    
    // Выделяем цвет для более темных помех (темно-серый)
    $g2=imagecolorallocate($im, 64,64,64);
    
    // Выделяем четыре случайных темных цвета для символов
    $cl1=imagecolorallocate($im,rand(0,128),rand(0,128),rand(0,128));
    $cl2=imagecolorallocate($im,rand(0,128),rand(0,128),rand(0,128));
    $cl3=imagecolorallocate($im,rand(0,128),rand(0,128),rand(0,128));
    $cl4=imagecolorallocate($im,rand(0,128),rand(0,128),rand(0,128));
    
    // Рисуем сетку
    for ($i=0;$i<=100;$i+=5) imageline($im,$i,0,$i,25,$g1);
    for ($i=0;$i<=25;$i+=5) imageline($im,0,$i,100,$i,$g1);
    
    // Выводим каждую цифру по отдельности, немного смещая случайным образом
    imagestring($im, 5, 0+rand(0,10), 5+rand(-5,5),
        substr($code,0,1), $cl1);
    imagestring($im, 5, 25+rand(-10,10), 5+rand(-5,5),
        substr($code,1,1), $cl2);
    imagestring($im, 5, 50+rand(-10,10), 5+rand(-5,5),
        substr($code,2,1), $cl3);
    imagestring($im, 5, 75+rand(-10,10), 5+rand(-5,5),
        substr($code,3,1), $cl4);
    
    // Выводим пару случайных линий тесного цвета, прямо поверх символов.
    // Для увеличения количества линий можно увеличить,
    // изменив число выделенное красным цветом
    for ($i=0;$i<6;$i++)
        imageline($im,rand(0,100),rand(0,25),rand(0,100),rand(0,25),$g2);
    
    // Коэффициент увеличения/уменьшения картинки
    $k=2.2;
    
    // Создаем новое изображение, увеличенного размера
    $im1=imagecreatetruecolor(101*$k,26*$k);
    
    // Копируем изображение с изменением размеров в большую сторону
    imagecopyresized($im1, $im, 0, 0, 0, 0, 101*$k, 26*$k, 101, 26); 
    
    // Создаем новое изображение, нормального размера
    $im2=imagecreatetruecolor(101,26);
    
    // Копируем изображение с изменением размеров в меньшую сторону
    imagecopyresampled($im2, $im1, 0, 0, 0, 0, 101, 26, 101*$k, 26*$k); 
    
    // Генерируем изображение -- ЗДЕСЬ УКАЗАТЬ КОРРЕКТНЫЙ ПУТЬ К ПАПКЕ
    $pic_path="img/codepic/codepic".time().".png";
    //echo $pic_path;
    imagepng($im2, $path.$pic_path);
    
    // Освобождаем память
    imagedestroy($im2);
    imagedestroy($im1);
    imagedestroy($im);
    
    //Возвращает массив с сылкой на картинку и значением числа
    $result['pic']="/".$pic_path;
    $result['code']=$code;
    return $result;
}

function resort($table_name, $parent_id)
{
	global $sql_pref, $conn_id;
	if (isset($parent_id) && $parent_id>=0) $pref_par=" WHERE parent_id='".$parent_id."'"; else $pref_par="";
	$sql_query="SELECT id FROM ".$sql_pref."_".$table_name."".$pref_par." ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$i=1;
		while(list($id)=mysql_fetch_row($sql_res))
		{
			$sql_query="UPDATE ".$sql_pref."_".$table_name." SET code='".$i."' WHERE id='".$id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			$i++;
		}
	}
}

function status_change($table_name, $table_field, $status_vaiants, $id)
{
	global $sql_pref, $conn_id;

	$variants=explode("|",$status_vaiants);
	$variants_num=count($variants);

	$sql_query="SELECT ".$table_field." FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if(mysql_num_rows($sql_res)>0)
	{
		list($status_old)=mysql_fetch_row($sql_res);
		for($i=0;$i<$variants_num;$i++)
		{
			if ($variants[$i]==$status_old)
			{
				if ($i<($variants_num-1)) $status_new=$variants[($i+1)];
				else $status_new=$variants[0];
			}
		}
		if (isset($status_new))
		{
			$sql_query="UPDATE ".$sql_pref."_".$table_name." SET ".$table_field."='".$status_new."' WHERE id='".$id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
		}
	}
}

function del_record($table_name, $id, $resort, $parent_id)
{
	global $sql_pref, $conn_id;
	$sql_query="DELETE FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (isset($resort) && $resort=='Yes') resort($table_name, $parent_id);
}

function del_file($full_name)
{
	if (file_exists($full_name)) unlink ($full_name);
}

function del_dir($full_path)
{
	if (is_dir($full_path))
	{
		$dh = opendir($full_path);
		while($file = readdir($dh))
		{
			if ($file!="." && $file!="..")
			{
				unlink ($full_path."/".$file);
			}
		}
		closedir($dh);
		rmdir($full_path);
	}
}

function time_up($table_name, $column_name, $id, $parent_id)
{
	global $sql_pref, $conn_id;
	$sql_query="UPDATE ".$sql_pref."_".$table_name." SET ".$column_name."=CURRENT_TIMESTAMP() WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
}
?>
