<?php

// Проверка ввода логина-пароля
function auth_check_submit()
{
	global $sql_pref, $conn_id, $path;
	global $auth_error, $auth_error_info, $admin_login, $admin_pass, $admin_status;

	$login=$_REQUEST['login'];
	$pass=$_REQUEST['pass'];
	for ($i=0;$i<count($admin_login);$i++)
	{
		if ($login==$admin_login[$i] and $pass==$admin_pass[$i])
		{
			$user_agent=$_SERVER['HTTP_USER_AGENT'];
			$ip=$_SERVER['REMOTE_ADDR'];
			$superkod=$login."_".md5($login.$user_agent.$pass.$ip);
			setcookie("PSID", $superkod,0,"/");
			
			if (isset($_COOKIE['redir']) && !empty($_COOKIE['redir'])) header("location:".$_COOKIE['redir']); 
			else
			{
				if ($admin_status[$i]=="root") header("location:/admin/welcome/"); 
				elseif ($admin_status[$i]=="articles") header("location:/admin/articles/"); 
			}
			exit();
		}
	}
}




// Проверка юзера на главной
function auth_check_permanent()
{
	global $sql_pref, $conn_id, $path, $admin_login, $admin_pass, $admin_status;
	$user_agent=$_SERVER['HTTP_USER_AGENT'];
	$ip=$_SERVER['REMOTE_ADDR'];
	for ($i=0;$i<count($admin_login);$i++)
	{
		if (isset($_COOKIE['PSID']) && ($admin_login[$i]."_".md5($admin_login[$i].$user_agent.$admin_pass[$i].$ip)==@$_COOKIE['PSID']))
		{
			if ($admin_status[$i]=="root") header("location:/admin/welcome/"); 
			elseif ($admin_status[$i]=="articles") header("location:/admin/articles/"); 
			exit();
		}
	}
}



// Проверка юзера на всех страницах, кроме главной
function auth_check_user()
{
	global $sql_pref, $conn_id, $path, $admin_login, $admin_pass, $admin_status, $admin_current_status;
	$user_agent=$_SERVER['HTTP_USER_AGENT'];
	$ip=$_SERVER['REMOTE_ADDR'];
	
	$checked=0;
	for ($i=0;$i<count($admin_login);$i++)
	{
		if ($admin_login[$i]."_".md5($admin_login[$i].$user_agent.$admin_pass[$i].$ip)==@$_COOKIE['PSID']) { $checked=1; $admin_current_status=$admin_status[$i]; }
	}
	
	if ($checked==0)
	{
		setcookie("redir", @$_SERVER["REQUEST_URI"], time()+1800, "/");
		header("location:/admin/"); 
		exit();
	}
	
	if ($admin_current_status=="articles" && substr($_SERVER['REQUEST_URI'],0,16)!="/admin/articles/") { header("location:/admin/articles/"); exit(); }
}



function auth_delcookie()
{
	global $sql_pref, $conn_id, $path;
	global $auth_error, $auth_error_info;

	if (isset($_COOKIE['PSID']) && !empty($_COOKIE['PSID']))
	{
		setcookie('PSID','',0,"/");
	}
}

?>