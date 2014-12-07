<?
function starting()
{}


function main_header()
{
	header("Cache-Control: max-age=-1, must-revalidate");
	header("Expires: ".gmdate("D, d M Y H:i:s", time()-3600)." GMT");
	header("Content-type:text/html; charset=windows-1251");
	header( "Pragma: no-cache" );
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


function html_head()
{
	echo "<head>
			<title>Система управления сайтом.</title>
			<link rel='stylesheet' type='text/css' href='/admin/styles.css'>
			<script language='javascript' src='/admin/jscripts.js'></script>
		</head>";
}








function auth_check()
{
	global $sql_pref, $conn_id;
	global $admin, $admin_auth_error, $admin_auth, $admin_status, $admin_modules;
    
    $user_agent=$_SERVER['HTTP_USER_AGENT'];
    $ip=$_SERVER['REMOTE_ADDR'];
    $admin_auth=false;
    
    
    
    
    
    
    if (@$_REQUEST["action"]=="auth_submit")
    {
    	$login=$_REQUEST['login'];
    	$pass=$_REQUEST['pass'];
        
        $admin_auth_error['login']="<span style='color:red;font-weight:bold;'>Ошибка!</span>";
        $admin_auth_error['pass']="<span style='color:red;font-weight:bold;'>Ошибка!</span>";
        
    	foreach ($admin AS $value)
        {
            if ($login==$value["login"]) $admin_auth_error['login']="";
            if ($login==$value["login"] && $pass==$value["password"])
            {
                $admin_auth_error['pass']="";
                
    			$superkod=$login."_".md5($login.$user_agent.$pass.$ip);
    			setcookie("admin_kod", $superkod,0,"/");
                
                if (isset($_COOKIE['admin_redir']) && !empty($_COOKIE['admin_redir'])) $loc=$_COOKIE['admin_redir']; else $loc="/admin/";
                header("location:".$loc); exit();
            }
        }
    }
    if (@$_REQUEST["action"]=="auth_logoff")
    {
    	if (isset($_COOKIE['admin_kod']) && !empty($_COOKIE['admin_kod']))
    	{
    		setcookie('admin_kod','',0,"/");
            header("location:/admin/"); exit();
    	}
    }
    
    
    
    
    
    
    
    $cpath=$_SERVER["REQUEST_URI"];
    if (substr($cpath,0,1)=="/") $cpath=substr($cpath,1); if (substr($cpath,-1)=="/") $cpath=substr($cpath,0,-1);
    $cpath_data=explode("/",$cpath);
    
    
    foreach ($admin AS $value)
	{
        if ($value["login"]."_".md5($value["login"].$user_agent.$value["password"].$ip)==@$_COOKIE['admin_kod'])
        {
            if (isset($cpath_data[1]) && $value["status"]!="root" && !in_array($cpath_data[1],$value["modules"])) { header("location:/admin/"); exit(); }
            $admin_auth=true;
            $admin_status=$value["status"]; $admin_modules=$value["modules"];
            break;
        }
	}
    
    
    
    if ($admin_auth==false && isset($cpath_data[1]))
    {
    	setcookie("admin_redir", @$_SERVER["REQUEST_URI"], time()+1800, "/");
    	header("location:/admin/"); exit();
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




function sort_up($table_name, $id, $parent_id)
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT code FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($code)=mysql_fetch_row($sql_res);
	if ($code>1)
	{
		if (isset($parent_id) && $parent_id>=0) $pref_par=" AND parent_id='".$parent_id."'"; else $pref_par="";
		$sql_query="SELECT id, code FROM ".$sql_pref."_".$table_name." WHERE code='".($code-1)."'".$pref_par;
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			list($id_change, $code_change)=mysql_fetch_row($sql_res);
			$sql_query="UPDATE ".$sql_pref."_".$table_name." SET code='".$code."' WHERE id='".$id_change."'";
			$sql_res=mysql_query($sql_query, $conn_id);
			$sql_query="UPDATE ".$sql_pref."_".$table_name." SET code='".($code-1)."' WHERE id='".$id."'";
			$sql_res=mysql_query($sql_query, $conn_id);
		}
	}
}



function sort_down($table_name, $id, $parent_id)
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT code FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($code)=mysql_fetch_row($sql_res);
		if (isset($parent_id) && $parent_id>=0) $pref_par=" AND parent_id='".$parent_id."'"; else $pref_par="";
		$sql_query="SELECT id, code FROM ".$sql_pref."_".$table_name." WHERE code='".($code+1)."'".$pref_par;
		$sql_res_1=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res_1)>0)
		{
			list($id_change, $code_change)=mysql_fetch_row($sql_res_1);
			if (isset($id_change) && !empty($id_change))
			{
				$sql_query="UPDATE ".$sql_pref."_".$table_name." SET code='".$code."' WHERE id='".$id_change."'";
				$sql_res_2=mysql_query($sql_query, $conn_id);
				$sql_query="UPDATE ".$sql_pref."_".$table_name." SET code='".($code+1)."' WHERE id='".$id."'";
				$sql_res_2=mysql_query($sql_query, $conn_id);
			}
		}
	}
}



function sort_top($table_name, $id, $parent_id)
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT code FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($code)=mysql_fetch_row($sql_res);
	if ($code>1)
	{
		if (isset($parent_id) && $parent_id>=0) $sql_query="SELECT id, code FROM ".$sql_pref."_".$table_name." WHERE code<'".$code."' AND parent_id='".$parent_id."'"; else $sql_query="SELECT id, code FROM ".$sql_pref."_".$table_name." WHERE code<'".$code."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			while(list($id_change, $code_change)=mysql_fetch_row($sql_res))
			{
				$sql_query1="UPDATE ".$sql_pref."_".$table_name." SET code=code+1 WHERE id='".$id_change."'";
				$sql_res1=mysql_query($sql_query1, $conn_id);
			}
		}
		$sql_query1="UPDATE ".$sql_pref."_".$table_name." SET code=1 WHERE id='".$id."'";
		$sql_res1=mysql_query($sql_query1, $conn_id);
	}
}


function sort_bottom($table_name, $id, $parent_id)
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT code FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($code)=mysql_fetch_row($sql_res);
	if (isset($parent_id) && $parent_id>=0)  $sql_query="SELECT COUNT(*) FROM ".$sql_pref."_".$table_name." WHERE parent_id='".$parent_id."'"; else $sql_query="SELECT COUNT(*) FROM ".$sql_pref."_".$table_name."";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($total)=mysql_fetch_row($sql_res);
	if ($code<$total)
	{
		if (isset($parent_id) && $parent_id>=0) $sql_query="SELECT id, code FROM ".$sql_pref."_".$table_name." WHERE code>'".$code."' AND parent_id='".$parent_id."'"; else $sql_query="SELECT id, code FROM ".$sql_pref."_".$table_name." WHERE code>'".$code."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			while(list($id_change, $code_change)=mysql_fetch_row($sql_res))
			{
				$sql_query1="UPDATE ".$sql_pref."_".$table_name." SET code=code-1 WHERE id='".$id_change."'";
				$sql_res1=mysql_query($sql_query1, $conn_id);
			}
		}
		$sql_query1="UPDATE ".$sql_pref."_".$table_name." SET code='".$total."' WHERE id='".$id."'";
		$sql_res1=mysql_query($sql_query1, $conn_id);
	}
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



function del_record($table_name, $id, $resort, $parent_id)
{
	global $sql_pref, $conn_id;
	$sql_query="DELETE FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (isset($resort) && $resort=='Yes') resort($table_name, $parent_id);
}


function del_somerecords($table_name, $pole, $value)
{
	global $sql_pref, $conn_id;
	$sql_query="DELETE FROM ".$sql_pref."_".$table_name." WHERE ".$pole."='".$value."'";
	$sql_res=mysql_query($sql_query, $conn_id);
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



function save_img($src, $dest, $resize, $width, $height, $quality=80, $mime="image/jpeg")
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


function translit($str, $s_symb=false)
{
	setlocale (LC_ALL, "ru_RU.CP1251");
	$str=strtolower($str);
	$trans = array("а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "e", "ж" => "j", "з" => "z", "и" => "i", "й" => "y",
					"к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f",
					"х" => "h", "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "she", "ъ" => "", "ы" => "iy", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ja");
	$trans_big = array("А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D", "Е" => "E", "Ё" => "E", "Ж" => "J", "З" => "Z", "И" => "I", "Й" => "Y",
					"К" => "K", "Л" => "L", "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F",
					"Х" => "H", "Ц" => "C", "Ч" => "Ch", "Ш" => "Sh", "Щ" => "She", "Ъ" => "", "Ы" => "Iy", "Ь" => "", "Э" => "E", "Ю" => "Yu", "Я" => "Ja");
	$symbols = array(" " => "_", "*" => "", ":" => "", "/" => "", "\\" => "", "?" => "", "!" => "", "\"" => "", "'" => "",  "\"" => "", "," => "", "." => "", "<" => "", ">" => "", "(" => "", ")" => "", "|" => "-");
	$str = strtr($str, $trans);
	$str = strtr($str, $trans_big);
	if ($s_symb==true) $str = strtr($str, $symbols);
  return ($str);
}



function translit_url($str, $length=30)
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



function make_dir($dir_path)
{
	if (!is_dir($dir_path)) mkdir($dir_path, 0777);
}


function del_empty_dir($dir_path)
{
	if (is_dir($dir_path))
	{
		$fl=0;
		$dh = opendir($dir_path);
		while($file = readdir($dh))
		{
			if ($file!="." && $file!="..") $fl=1;
		}
		closedir($dh);
		if ($fl==0) rmdir($dir_path);
	}
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

































function enable($table_name, $id)
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT enable FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if(mysql_num_rows($sql_res)>0)
	{
		list($enable)=mysql_fetch_row($sql_res);
		if ($enable=='Yes') $enable="No"; else $enable="Yes";
		$sql_query1="UPDATE ".$sql_pref."_".$table_name." SET enable='".$enable."' WHERE id='".$id."'";
		$sql_res1=mysql_query($sql_query1, $conn_id);
	}
}


function del($table_name, $id, $resort, $parent_id)
{
	global $sql_pref, $conn_id;
	$sql_query="DELETE FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (isset($resort) && $resort=='Yes') resort($table_name, $parent_id);
}





function rub_to_top($table_name, $id, $parent_id)
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT code FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($code)=mysql_fetch_row($sql_res);
	if ($code>1)
	{
		if (isset($parent_id) && $parent_id>=0) $sql_query="SELECT id, code FROM ".$sql_pref."_".$table_name." WHERE code<'".$code."' AND parent_id='".$parent_id."'"; else $sql_query="SELECT id, code FROM ".$sql_pref."_".$table_name." WHERE code<'".$code."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			while(list($id_change, $code_change)=mysql_fetch_row($sql_res))
			{
				$sql_query1="UPDATE ".$sql_pref."_".$table_name." SET code=code+1 WHERE id='".$id_change."'";
				$sql_res1=mysql_query($sql_query1, $conn_id);
			}
		}
		$sql_query1="UPDATE ".$sql_pref."_".$table_name." SET code=1 WHERE id='".$id."'";
		$sql_res1=mysql_query($sql_query1, $conn_id);
	}
}


function rub_to_bottom($table_name, $id, $parent_id)
{
	global $sql_pref, $conn_id;
	$sql_query="SELECT code FROM ".$sql_pref."_".$table_name." WHERE id='".$id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($code)=mysql_fetch_row($sql_res);
	if (isset($parent_id) && $parent_id>=0)  $sql_query="SELECT COUNT(*) FROM ".$sql_pref."_".$table_name." WHERE parent_id='".$parent_id."'"; else $sql_query="SELECT COUNT(*) FROM ".$sql_pref."_".$table_name."";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($total)=mysql_fetch_row($sql_res);
	if ($code<$total)
	{
		if (isset($parent_id) && $parent_id>=0) $sql_query="SELECT id, code FROM ".$sql_pref."_".$table_name." WHERE code>'".$code."' AND parent_id='".$parent_id."'"; else $sql_query="SELECT id, code FROM ".$sql_pref."_".$table_name." WHERE code>'".$code."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			while(list($id_change, $code_change)=mysql_fetch_row($sql_res))
			{
				$sql_query1="UPDATE ".$sql_pref."_".$table_name." SET code=code-1 WHERE id='".$id_change."'";
				$sql_res1=mysql_query($sql_query1, $conn_id);
			}
		}
		$sql_query1="UPDATE ".$sql_pref."_".$table_name." SET code='".$total."' WHERE id='".$id."'";
		$sql_res1=mysql_query($sql_query1, $conn_id);
	}
}


function img_del($name)
{
	if (file_exists($name)) unlink ($name);
}


function valid_url($url, $table_name, $parent_id, $id_old)
{
	global $sql_pref, $conn_id;
	if (ereg("^[a-zA-Z0-9_\-]+$", $url))
	{
		if ($parent_id>=0) $sql_query="SELECT url FROM ".$sql_pref."_".$table_name." WHERE parent_id='".$parent_id."' AND id!='".$id_old."'"; else $sql_query="SELECT url FROM ".$sql_pref."_".$table_name." WHERE id!='".$id_old."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		while(list($url_1)=mysql_fetch_row($sql_res))
		{
			if ($url==$url_1) return false;
		}
  		return true;
	}
	else return false;
}


function valid_move_url($url, $table_name, $new_rub)
{
	global $sql_pref, $conn_id;
	if (ereg("^[a-zA-Z0-9_\-]+$", $url))
	{
		if ($new_rub>=0) $sql_query="SELECT url FROM ".$sql_pref."_".$table_name." WHERE parent_id='".$new_rub."' ORDER BY code"; else return false;
		$sql_res=mysql_query($sql_query, $conn_id);
		while(list($url_1)=mysql_fetch_row($sql_res))
		{
			if ($url==$url_1) return false;
		}
		return true;
	}
	else return false;
}



function file_save($f_path, $f_name, $f_index, $share=false)
{
	global $path;
	$new_name=translit($f_name, true);
	$ext=substr($_FILES[$f_index]['name'], strpos($_FILES[$f_index]['name'], ".")+1);
	if (is_dir($path."".$f_path)==false)
	{
		if (make_dir($f_path)==false) return false;
	}
	if (unique_file_name($path."".$f_path, $new_name."".$ext)==false) return false;
	if (@is_uploaded_file($_FILES[$f_index]["tmp_name"]))
	{
		if ($share==true)
		{
			$res=move_uploaded_file($_FILES[$f_index]["tmp_name"], $path."files/pubs/share/".$new_name.".".$ext);
			if (!$res) return false;
			chmod ($path."files/pubs/share/".$new_name.".".$ext, 0644);
		}
		else
		{
			$res=move_uploaded_file($_FILES[$f_index]["tmp_name"], $path."".$f_path."/".$new_name.".".$ext);
			if (!$res) return false;
			chmod ($path."".$f_path."/".$new_name.".".$ext, 0644);
		}
	}
	return true;
}




function unique_file_name($f_path, $f_name)
{
	if (file_exists($f_path."/".$f_name)) return false;
	return true;
}


function is_cat_empty($dir_path)
{
	$dh = opendir($dir_path);
	while($file = readdir($dh))
	{
		if ($file!="." && $file!="..")
		{
			closedir($dh);
			return false;
		}
	}
	closedir($dh);
	return true;
}


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


function status($table_name, $id, $status)
{
	global $sql_pref, $conn_id;
	$sql_query="SHOW FIELDS FROM ".$sql_pref."_".$table_name." LIKE 'status'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while ($all_types=mysql_fetch_assoc($sql_res))
		{
			foreach ($all_types as $k => $v)
			{
				if ($k=="Type")
				{
					$types=explode("','", substr($v, 6, -2));
				}
			}
		}
	}
	if (isset($types) && is_array($types))
	{
		for ($i=0; $i<count($types); $i++)
		{
			if ($i!=(count($types)-1))
			{
				if ($status==$types[$i]) $new_status=$types[$i+1];
			}
			else
			{
				if ($status==$types[$i]) $new_status=$types[0];
			}
		}
		$sql_query="UPDATE ".$sql_pref."_".$table_name." SET status='".$new_status."' WHERE id='".$id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	}
}


// Функция проверяет введенный адрес электронной почты на валидность
function is_email_valid($str)
{
	if (!eregi("^[a-zA-Z0-9_\.-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$", $str)) return false;
	return true;
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















?>