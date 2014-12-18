<?php
require_once("fns/groups.php");
require_once("fns/calendar.php");
require_once("fns/admin.php");
require_once("fns/common.php");

function auth_main()
{
	global $sql_pref, $conn_id, $path;
	global $auth_error_info, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
	$out="";
    //echo $rub_url[1];
	echo $user_contact;
    
    if(!isset($_SESSION["exibitions"]))
    {
        $exibitions="Yes";
        $company="Yes";
        $ensor="Yes";
        $other="Yes";
        session_register("exibitions");
        session_register("company");
        session_register("ensor");
        session_register("other");//,"company"=>"Yes","ensor"=>"Yes","other"=>"Yes");
    }
    if (isset($_REQUEST['action']))
    {
        $filter_main="";        
        if ($_REQUEST['action']=="filter_exibitions_change")
        {
            $_SESSION["exibitions"]=$_REQUEST["exibitions"];
        }
        if ($_REQUEST['action']=="filter_company_change")
        {
            $_SESSION["company"]=$_REQUEST["company"];
        }
        if ($_REQUEST['action']=="filter_ensor_change")
        {
            $_SESSION["ensor"]=$_REQUEST["ensor"];
        }
        if ($_REQUEST['action']=="filter_other_change")
        {
            $_SESSION["other"]=$_REQUEST["other"];     
        }            
    }   
    if($_SESSION["exibitions"]=="No") $filter_main.=" AND type not like 'exibitions'";
    if($_SESSION["company"]=="No") $filter_main.=" AND type not like 'company'";
    if($_SESSION["ensor"]=="No") $filter_main.=" AND type not like 'ensor'";
    if($_SESSION["other"]=="No") $filter_main.=" AND type not like 'other'"; 
    
	if (isset($rub_url[0]) && $rub_url[0]=="auth") 
	{
		if (isset($rub_url[1]) && $rub_url[1]=="register") $out.=auth_register();
		elseif (isset($rub_url[1]) && $rub_url[1]=="enter") $out.=auth_enter();
		elseif (isset($rub_url[1]) && $rub_url[1]=="password_restore") $out.=auth_password_restore();
		elseif (isset($rub_url[1]) && $rub_url[1]=="edit_profile") $out.=auth_edit_profile();
		elseif (isset($rub_url[1]) && $rub_url[1]=="user_phones") $out.=auth_user_phones();
		elseif (isset($rub_url[1]) && $rub_url[1]=="user_phones_add") $out.=auth_user_phones_add();
		elseif (isset($rub_url[1]) && $rub_url[1]=="user_notes") $out.=auth_user_notes();
		elseif (isset($rub_url[1]) && $rub_url[1]=="calendar") $out.=out_calendar($filter_main);		
        elseif (isset($rub_url[1]) && $rub_url[1]=="activation") $out.=auth_activation();
		elseif (isset($rub_url[1]) && $rub_url[1]=="logoff") $out.=auth_logoff();
        
        elseif (isset($rub_url[1]) && $rub_url[1]=="views_list") $out.=auth_views_list();    	
        
        elseif (isset($rub_url[1]) && $rub_url[1]=="proposals_list") $out.=auth_proposals_list();
    	elseif (isset($rub_url[1]) && $user_id!=0 && $rub_url[1]=="proposals_add") $out.=auth_proposals_add();
    	elseif (isset($rub_url[1]) && $rub_url[1]=="proposals_edit") $out.=auth_proposals_edit();
    	elseif (isset($rub_url[1]) && $rub_url[1]=="proposals_del") $out.=auth_proposals_del();
        elseif (isset($rub_url[1]) && $rub_url[1]=="proposals_up") { time_up('proposals', 'top_time', $_REQUEST['id'], -1); $out.=auth_proposals_list();	}       
       
        elseif (isset($rub_url[1]) && $rub_url[1]=="demand_list") $out.=auth_demand_list();
    	elseif (isset($rub_url[1]) && $user_id!=0 && $rub_url[1]=="demand_add") $out.=auth_demand_add();
    	elseif (isset($rub_url[1]) && $rub_url[1]=="demand_edit") $out.=auth_demand_edit();
    	elseif (isset($rub_url[1]) && $rub_url[1]=="demand_del") $out.=auth_demand_del();
        elseif (isset($rub_url[1]) && $rub_url[1]=="demand_up") { time_up('demand', 'top_time', $_REQUEST['id'], -1); $out.=auth_demand_list();	}       
       
        
        elseif (isset($rub_url[1]) && $rub_url[1]=="groups_list" && !isset($art_url)) $out.=auth_groups_list();
        elseif (isset($rub_url[1]) && $rub_url[1]=="groups_list" && isset($art_url)) $out.=auth_groups_show();
    	elseif (isset($rub_url[1]) && $rub_url[1]=="groups_add" && !isset($art_url)) $out.=auth_groups_add();
    	elseif (isset($rub_url[1]) && $rub_url[1]=="groups_delete" && !isset($art_url)) $out.=auth_groups_del();
        elseif (isset($rub_url[1]) && $rub_url[1]=="groups_visible" && !isset($art_url)) $out.=auth_groups_vis();
        
        
        
        elseif (isset($rub_url[1]) && $rub_url[1]=="questions_list") $out.=auth_questions_list();

        elseif (isset($rub_url[1]) && $rub_url[1]=="messages_list" && $user_id!=0) $out.=auth_messages_list();
       	elseif (isset($rub_url[1]) && $rub_url[1]=="messages_show" && $user_id!=0) $out.=auth_messages_show();
        elseif (isset($rub_url[1]) && $rub_url[1]=="messages_add" && $user_id!=0) $out.=auth_messages_add();
        elseif (isset($rub_url[1]) && $rub_url[1]=="messages_del" && $user_id!=0) $out.=auth_messages_del();
       
        elseif (isset($rub_url[1]) && $rub_url[1]=="blogs_list") $out.=auth_blogs_list();
       	elseif (isset($rub_url[1]) && $rub_url[1]=="blogs_show") $out.=auth_blogs_show();
        elseif (isset($rub_url[1]) && $rub_url[1]=="blogs_add") $out.=auth_blogs_add();
        elseif (isset($rub_url[1]) && $rub_url[1]=="blogs_edit") $out.=auth_blogs_edit();
        elseif (isset($rub_url[1]) && $rub_url[1]=="posts_del") $out.=auth_posts_del();

        elseif (isset($rub_url[1]) && $rub_url[1]=="inv_user") {$out.=auth_register_inv_user(); }//echo "inv_user";}
        else out_404(); 
        
        if (isset($rub_url[1]) && $rub_url[1]=="admin") 
        {
            $company_id=0;
            $sql_query="SELECT company_id FROM ".$sql_pref."_company_admin WHERE user_id=".$user_id;
            $sql_res=mysql_query($sql_query, $conn_id);
            if (mysql_num_rows($sql_res)>0)
            {
               list($company_id)=mysql_fetch_row($sql_res);
            }
            //echo $company_id."!!!";
            $out.=auth_admin_bar();              
            if (isset($rub_url[2]) && $rub_url[2]=="company" && $_REQUEST['action']=="companies_save") $out.=form_companies_save();
            if (isset($rub_url[2]) && $rub_url[2]=="company") $out.=auth_admin_company_profile($company_id);
            
            
            
            $out_vac="";
            $out_vac_form="";
            if (isset($rub_url[2]) && $rub_url[2]=="vacancies" && $_REQUEST['action']=='vacancies_delete') { del_record('vacancies', $_REQUEST['id'], 'Yes', -1); header("location:index.html"); exit(); }
            if (isset($rub_url[2]) && $rub_url[2]=="vacancies" && $_REQUEST['action']=='vacancies_main_enable') { status_change('vacancies', 'main', 'Yes|No', $_REQUEST['id']); header("location:index.html"); exit(); }
            if (isset($rub_url[2]) && $rub_url[2]=="vacancies" && $_REQUEST['action']=="vacancies_save") form_vacancies_save($company_id);
            if ((isset($rub_url[2]) && $rub_url[2]=="vacancies") && ($_REQUEST['action']=="vacancies_add" || $_REQUEST['action']=="vacancies_edit")) $out_vac_form.=form_vacancies_edit($company_id);
            if (isset($rub_url[2]) && $rub_url[2]=="vacancies") $out_vac.=auth_admin_company_vacancies($company_id);
            $out.=$out_vac."<BR>".$out_vac_form; 
            
            
            $out_news="";
            $out_news_form="";
            //echo $path;
            if (isset($rub_url[2]) && $rub_url[2]=="news" && $_REQUEST['action']=='news_delete') { del_record('news', $_REQUEST['id'], 'Yes', -1); header("location:index.html"); exit(); }
            if (isset($rub_url[2]) && $rub_url[2]=="news" && $_REQUEST['action']=='news_main_enable') { status_change('news', 'main', 'Yes|No', $_REQUEST['id']); header("location:index.html"); exit(); }
            if (isset($rub_url[2]) && $rub_url[2]=="news" && $_REQUEST['action']=="news_save") form_news_save($company_id);
            if ((isset($rub_url[2]) && $rub_url[2]=="news") && ($_REQUEST['action']=="news_add" || $_REQUEST['action']=="news_edit")) $out_news_form.=form_news_edit($company_id);
            if (isset($rub_url[2]) && $rub_url[2]=="news") $out_news.=auth_admin_company_news($company_id);
            $out.=$out_news."<BR>".$out_news_form; 
            
            
        }
        
        elseif (!isset($rub_url[1])) $out.=auth_mainpage(); 
        
		      
	}
	return ($out);
}


function auth_user_bar()
{
	global $user_id, $page_header1, $user_name, $user_surname;
    global $sql_pref, $conn_id, $path, $path_blogs, $path_resume, $path_contacts;
	global $auth_error_info, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
    
    $sql_query="SELECT count(*) FROM ".$sql_pref."_messages_to LEFT JOIN ".$sql_pref."_messages ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages_to.user_id=".$user_id." AND ".$sql_pref."_messages.shown='No'";
    $sql_res=mysql_query($sql_query, $conn_id);
    //echo $sql_query;
    if (mysql_num_rows($sql_res)>0)
    {
       list($new_mess_count)=mysql_fetch_row($sql_res);
       if($new_mess_count>0) $new_mess_count="<b>(".$new_mess_count.")</b>"; else $new_mess_count="";  
    }
    $user_name_show=substr($user_name,0,1).". ".$user_surname;
    $user_name_show="<a href='/".$path_contacts."/".$user_id.".html'>".$user_name_show."</a>";
$out="";
$out.="<table cellpadding=0 cellspacing=0 border=0 width='676px' background='/img_new/int/main-bg.gif'>
                <tr>
                    <td valign=top background='/img_new/int/main-top.gif' style='background-image: /img_new/int/main-top.gif; background-position: top; background-repeat: repeat-x;'>";
                
$out.="<table width=100% border='0'><tr>
	<td align=center valign=middle ><a href='/auth/'><img width=32px src='/img/man.png' border=0></a></td><td><b>$user_name_show</b><BR><a href='/auth/' class='user_bar'>Личный кабинет</a></td>
	<td align=center valign=middle ><a href='/auth/user_notes/'><img width=24px src='/img/note.png' border=0></a><BR><a href='/auth/user_notes/' class='user_bar'>Заметки</a></td>
	<td align=center valign=middle ><a href='/auth/messages_list/'><img width=24px src='/img/message0.png' border=0></a><BR><a href='/auth/messages_list/' class='user_bar'>Сообщения ".$new_mess_count."</a></td>
	<td align=center valign=middle ><a href='/auth/user_phones/'><img width=24px src='/img/contact-list.png' border=0></a><BR><a href='/auth/user_phones/' class='user_bar'>Контакты</a></td>
	<td align=center valign=middle ><a href='/auth/groups_list/'><img width=24px src='/img/group.png' border=0></a><BR><a href='/auth/groups_list/' class='user_bar'>Группы</a></td>
	<td align=center valign=middle ><a href='/auth/views_list/'><img width=24px src='/img/visitor.png' border=0></a><BR><a href='/auth/views_list/' class='user_bar'>Гости</a></td>
	</tr>
    <tr>
    <td></td><td align=left><a href='/auth/logoff/'><img width=24px src='/img/exit.png' border=0></a><BR><a href='/auth/logoff/' class='user_bar'>Выход</a></td>
    <td align=center valign=middle ><a href='/auth/calendar/'><img width=24px src='/img/calendar.png' border=0></a><BR><a href='/auth/calendar/' class='user_bar'>События</a></td>
	<td align=center valign=middle ><a href='/".$path_resume."/".$user_id.".html?action=edit'><img width=24px src='/img/resume.png' border=0></a><BR><a href='/".$path_resume."/".$user_id.".html?action=edit' class='auth_main'>Резюме</a></td>
    <td align=center valign=middle ><a href='/".$path_blogs."/".$user_id."/'><img width=24px src='/img/drawing_pen.png' border=0></a><BR><a href='/".$path_blogs."/".$user_id."/' class='auth_main'>БлогоМысли</a></td>
    <td align=center valign=middle ><a href='/auth/proposals_list/'><img width=24px src='/img/propose.png' border=0></a><BR><a href='/auth/proposals_list/' class='auth_main'>Предложения</a> | <a href='/auth/demand_list/' class='auth_main'>Поиск</a></td>
    <td align=center valign=middle ><a href='/auth/edit_profile/'><img width=24px src='/img/Modify.png' border=0></a><BR><a href='/auth/edit_profile/' class='auth_main'>Профиль</a></td>
    </tr>
    ";
$out.="</table></div></td></tr><tr height=10><td valign=top><img src='/img_new/int/main-bottom.gif' border=0 width=676 height=10 /></td></tr></table>";
if (isset($user_id) AND ($user_id!==0)) return ($out); else return("");
}


function auth_mainpage()
{
	global $sql_pref, $conn_id, $path, $path_blogs, $path_resume;
	global $auth_error_info, $user_id, $user_login, $user_email, $user_company, $page_title, $page_header1; 
	$page_title="Личный кабинет";
	$page_header1="Личный кабинет";
	$out="";

	if (!isset($user_id) || ($user_id==0))
	{
		$out.="<div><a href='/auth/register/'>Регистрация</a></div>";
		$out.="<div><a href='/auth/password_restore/'>Восстановление пароля</a></div>";
	}
	else
	{
	if (file_exists($path."files/users/img/".$user_id.".jpg"))
	$img_show="<img src='/files/users/img/".$user_id.".jpg' border=0>";
	else $img_show="<img src='/img/user.png' border=0>";
	$out.="<table cellpadding=2 cellspacing=0 border=0 width=100%>";
	
	$out.="<tr><td width='1%' align=right><a href='/auth/edit_profile/'><img src='/img/Modify.png' border=0></a></td><td><a href='/auth/edit_profile/' class='auth_main'>Редактировать личные данные</a></td><td rowspan='6' align='right'>".$img_show."</td></tr>";
	$out.="<tr><td width='1%' align=right><a href='/".$path_blogs."/".$user_id."/'><img src='/img/blogroll.png' border=0></a></td><td><a href='/".$path_blogs."/".$user_id."/' class='auth_main'>Мои БлогоМысли</a></td></tr>";
	$out.="<tr><td width='1%' align=right><a href='/auth/questions_list/'><img src='/img/question2.png' border=0></a></td><td><a href='/auth/questions_list/' class='auth_main'>Мои вопросы</a></td></tr>";
	$out.="<tr><td width='1%' align=right><a href='/auth/inv_user/'><img src='/img/invite.png' border=0></a></td><td><a href='/auth/inv_user/' class='auth_main'>Пригласить коллегу</a></td></tr>";
	$out.="<tr><td width='1%' align=right><a href='/auth/proposals_list/'><img src='/img/propose.png' border=0></a></td><td><a href='/auth/proposals_list/' class='auth_main'>Мои предложения</a></td></tr>";
	$out.="<tr><td width='1%' align=right><a href='/auth/demand_list/'><img src='/img/search_user.png' width=48 border=0></a></td><td><a href='/auth/demand_list/' class='auth_main'>Поиск исполнителей</a></td></tr>";
	$out.="<tr><td width='1%' align=right><a href='/".$path_resume."/".$user_id.".html?action=edit'><img src='/img/resume.png' border=0></a></td><td><a href='/".$path_resume."/".$user_id.".html?action=edit' class='auth_main'>Резюме</a></td></tr>";	
	$out.="<tr><td width='1%' align=right><a href='/auth/groups_list/'><img src='/img/group.png' border=0></a></td><td><a href='/auth/groups_list/' class='auth_main'>Мои группы</a></td></tr>";	
    
    $sql_query="SELECT views FROM ".$sql_pref."_users WHERE enable='Yes'&&id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($views)=mysql_fetch_row($sql_res);
	}	
    //количество просмотров
    if (isset($views)&&$views!="") $views=unserialize($views); else $views=array();
    $min_date=time()-2592000;
    foreach($views as $k=>$v) if($v<$min_date) unset($views[$k]);
    $view_count=count($views);
    
    $out.="<tr><td width='1%' align=right><a href='/auth/views_list/'><img src='/img/visitor.png' border=0></a></td><td><a href='/auth/views_list/' class='auth_main'>Количество просмотров профиля (".$view_count.")</a></td></tr>";	

	$sql_query="SELECT company_id, enable FROM ".$sql_pref."_company_admin WHERE user_id=".$user_id;
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
       list($company_id, $enable)=mysql_fetch_row($sql_res);
       if($enable=='Yes')
       {
            $out.="<tr><td width='1%' align=right><a href='/auth/admin/company'><img width=48 src='/img/edit_resume.png' border=0></a></td><td><a href='/auth/admin/company' class='auth_main'>Модерация информации компании</a></td></tr>";	
       }
    }
	
	
	
	 $user_name_show=substr($user_name,0,1).". ".$user_surname;
	
	
	
	
	
	
	$out.="</table>";
	}
	return($out);
}








function auth_maincheck()
{
	global $sql_pref, $conn_id, $path;
	global $auth_error_info, $user_id, $user_login, $user_blog, $user_email, $user_company, $user_surname, $user_name, $user_name2, $user_email, $dt_birth, $phone_mobile, $user_status, $user_forum_status, $user_last_visit, $user_rate_main, $user_rate_sec;
	
	$user_id=0; $user_type="none";
	if (isset($_REQUEST['submit']) && isset($_REQUEST['formname']) && $_REQUEST['formname']=="loginform") auth_check_submit();
	elseif (isset($_COOKIE['USID']) && !empty($_COOKIE['USID']))
	{
		$user_id_pr=substr($_COOKIE['USID'],0,strpos($_COOKIE['USID'],"_"));
		$user_agent=$_SERVER['HTTP_USER_AGENT'];
		$sql_query="SELECT id, pass, enable FROM ".$sql_pref."_users WHERE id='".$user_id_pr."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
		list($user_id, $user_pass, $enable)=mysql_fetch_row($sql_res_1);
		if (($user_id."_".md5($user_id.$user_agent.$user_pass)==$_COOKIE['USID']) && $enable=="Yes")
		{

			$sql_query="SELECT id, surname, name, name2, dt_birth, phone_mobile, email, status, forum_admin, last_visit, rate_main, rate_sec FROM ".$sql_pref."_users WHERE id='".$user_id_pr."'";

    		$sql_res_2=mysql_query($sql_query, $conn_id);
    		list($user_id, $user_surname, $user_name, $user_name2, $dt_birth, $phone_mobile, $user_email, $user_status, $forum_admin, $user_last_visit, $user_rate_main, $user_rate_sec)=mysql_fetch_row($sql_res_2);
			$user_surname=stripslashes($user_surname);$user_name=stripslashes($user_name);$user_name2=stripslashes($user_name2); $phone_mobile=stripslashes($phone_mobile);
            if ($forum_admin=="Yes") $user_forum_status="admin"; else $user_forum_status="user";
            if (date("d.m.Y", strtotime($user_last_visit))!=date("d.m.Y")) rate_main($user_id, "посетил сайт", $user_rate_main, $user_rate_sec);//date("Y-m-d");
		    //echo date("d.m.Y", $user_last_visit);
        }
		else $user_id=0;        
        if ($user_id>0)
        {
            $sql_query="SELECT id FROM ".$sql_pref."_blogs_rubs WHERE user_id='".$user_id."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
    		list($blog_id)=mysql_fetch_row($sql_res);
			if(mysql_num_rows($sql_res)>0)
            {	 
                $user_blog=$blog_id;
            }
            else
            {
                $sql_query="INSERT INTO ".$sql_pref."_blogs_rubs (parent_id, name, url, user_id, type) VALUES ('1', 'Личный блог энергетика. ".$user_name." ".$user_surname."', 'user_blog_".$user_id."', '".$user_id."', 'blog')";
                $sql_res=mysql_query($sql_query, $conn_id);
                $user_blog=mysql_insert_id();
            }
        }
	}
    
    if (isset($user_id) && $user_id>0)
    {
        $sql_query2="UPDATE ".$sql_pref."_users SET at_site_now='".date("Y-m-d H:i:s")."' WHERE id=".$user_id;
        $sql_res2=mysql_query($sql_query2, $conn_id);        
    }
	return;
}







function auth_check_submit()
{
	global $sql_pref, $conn_id, $path;
	global $auth_error_info, $user_id;

	$email=$_REQUEST['email'];
	$pass=$_REQUEST['pass'];
	if (!isset($_REQUEST['remember_me']) || $_REQUEST['remember_me']!="Yes") $remember_me="No"; else $remember_me="Yes";
	if (empty($email) || empty($pass))
	{
		$auth_error_info="Заполните, пожалуйста, поля e-mail и пароль";
	}
	else
	{
		$sql_query="SELECT * FROM ".$sql_pref."_users WHERE email='".$email."'&&enable='Yes'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if(mysql_num_rows($sql_res)==0) $auth_error_info="Пользователь <b>".$email."</b> в нашей базе не зарегистрирован";
		else
		{
			$sql_query="SELECT id, email, pass FROM ".$sql_pref."_users WHERE email='".$email."'&&pass='".addslashes($pass)."'";
			$sql_res=mysql_query($sql_query, $conn_id);
			if(mysql_num_rows($sql_res)==0) $auth_error_info="Пароль для пользователя <b>".$email."</b> указан не верно";
			else 
			{ 
				list($user_id, $user_email, $user_pass)=mysql_fetch_row($sql_res);
				$user_agent=$_SERVER['HTTP_USER_AGENT'];
				$superkod=$user_id."_".md5($user_id.$user_agent.$user_pass);
				if (isset($remember_me) && $remember_me=="Yes") setcookie("USID", $superkod,time()+60*60*24*30,"/");
				else setcookie("USID", $superkod,0,"/");
				header("location:".$_SERVER["REQUEST_URI"]);
				exit();
			}
		}
	}
}







function auth_enter()
{
	global $sql_pref, $conn_id, $path;
	global $auth_error, $auth_error_info, $user_id, $user_company, $user_surname, $user_name, $path_blogs;
	
	$out="";

/*
    $out.="<table style='padding:20px 0px 0px 20px;' cellspacing=0 border=0 width=100% height=19>";
	$out.="<tr><td align=left valign=top>
                <table cellpadding=0 cellspacing=0 border=0 width=100%>
                    <tr>
                        <!--td width=14 align=center valign=top style='padding-top:8px;'><img src='/img/int/bul-leftmenu.gif' border=0 width=5 height=3></td-->
                        <td align=left valign=top style='padding-top:2px;'><h3><a class=leftmenu href='/auth/'>Личный кабинет</a></h3></td>
                    </tr>
                </table>
           </td></tr>";
    $out.="</table>";
    */
    $out.="<h3 style='padding:20 10 0 20;'><a class=leftmenu href='/auth/'>Личный кабинет</a></h3>";

    if (!isset($user_id) || ($user_id==0))
    {
    	if (isset($auth_error_info)) $auth_error_info_show="<div align=center style='font-size:10px;'><font color='#FF0000'><b>Ошибка!</b><br>".$auth_error_info."</font></div>"; else $auth_error_info_show="";
    	$out.="
    					<form action='' method=post name=login_form style='padding:5 0 0 10;margin:0;'>
    						<input type=hidden name=formname value=loginform>
                            <input type=hidden name=remember_me value=Yes>
    						
    					<table cellpadding=3 cellspacing=0 border=0>
    						<tr>
    							<td align=left style='color:#605e5f;'>E-mail:</td>
    							<td align=left valign=top><input type=Text maxlength=50 name=email value='".@$_REQUEST['email']."' style='font-size:11px;width:100px;height:18px;border: 1px #605E5F solid;'></td>
    						</tr>
    						<tr>
    							<td align=left style='color:#605e5f;'>Пароль:</td>
    							<td align=left valign=top><input type=Password maxlength=20 name=pass value='' style='font-size:11px;width:70px;height:18px;border: 1px #605E5F solid;'> <input type=Submit name=submit value='&raquo;&raquo;' style='bgcolor: #F2F2F2; font-size: 11px; width:30px; color: #777777; border: 0px #aaaaaa solid;'></td>
    						</tr>
    						<tr>
    							<td align=left colspan=2 style='font-size:11px;'><a class=auth_form_menu href=/auth/register/ style='font-size:11px;color:#605e5f;'>Регистрация</a> &middot; <a class=auth_form_menu href=/auth/password_restore/ style='font-size:11px;color:#605e5f;'>Забыли пароль?</a></td>
    						</tr>
    					
    					</table>
                        </form>
                        ".$auth_error_info_show."";
    
    
    }
    else 
    {
        
        $sql_query="SELECT count(*) FROM ".$sql_pref."_messages_to LEFT JOIN ".$sql_pref."_messages ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages_to.user_id=".$user_id." AND ".$sql_pref."_messages.shown='No'";
        $sql_res=mysql_query($sql_query, $conn_id);
        //echo $sql_query;
	    if (mysql_num_rows($sql_res)>0)
	    {
           list($new_mess_count)=mysql_fetch_row($sql_res);
           if($new_mess_count>0) $new_mess_count="<b>(".$new_mess_count.")</b>"; else $new_mess_count="";  
        }
        
        $user_name_show=substr($user_name,0,1).". ".$user_surname;
        $out.="<div style='padding:0px 20px 20px 20px;'>";
        $out.="<table cellspacing=0 cellpadding=0 border=0>";
    	$out.="<tr><td style='padding: 3 0 6 0;'><b>".$user_name_show."</b></td></tr>";
    	$out.="<tr><td style='padding: 2 0;'><img src='/img/int/arrow-left.gif' border=0 align=absmiddle> <a href='/auth/proposals_list/' class='leftmenu1'>Ваши предложения</a><br></td></tr>";
    	//$out.="<div style='padding: 5px 0px 0px 15px;;'>&middot; <a href='/auth/blogs_list/' class='leftmenu1'>Ваши блогоМысли</a></div>";
    	$out.="<tr><td style='padding: 2 0;'><img src='/img/int/arrow-left.gif' border=0 align=absmiddle> <a href='/".$path_blogs."/".$user_id."/' class='leftmenu1'>Ваши блогоМысли</a><br></td></tr>";
    	$out.="<tr><td style='padding: 2 0;'><img src='/img/int/arrow-left.gif' border=0 align=absmiddle> <a href='/auth/messages_list/' class='leftmenu1'>Ваши сообщения ".$new_mess_count."</a><br></td></tr>";
      	$out.="<tr><td style='padding: 2 0;'><img src='/img/int/arrow-left.gif' border=0 align=absmiddle> <a href='/auth/questions_list/' class='leftmenu1'>Ваши вопросы</a><br></td></tr>";
        $out.="<tr><td style='padding: 2 0;'><img src='/img/int/arrow-left.gif' border=0 align=absmiddle> <a href='/auth/edit_profile/' class='leftmenu1'>Ваши данные</a><br></td></tr>";
        $out.="<tr><td style='padding: 2 0;'><img src='/img/int/arrow-left.gif' border=0 align=absmiddle> <a href='/auth/inv_user/' class='leftmenu1'>Пригласить коллегу</a><br></td></tr>";
        $out.="<tr><td style='padding: 2 0;'><img src='/img/int/arrow-left.gif' border=0 align=absmiddle> <a href='/auth/groups_list/' class='leftmenu1'>Ваши группы</a><br></td></tr>";
      	$out.="<tr><td style='padding: 2 0;'><img src='/img/int/arrow-left.gif' border=0 align=absmiddle> <a href='/auth/logoff/' class='leftmenu1'>Выход</a><br></td></tr>";
        $out.="</table>";
        $out.="</div>";
    }
    
	return($out);
}







function auth_logoff()
{
	global $sql_pref, $conn_id, $path;
	global $auth_error, $auth_error_info;

	if (isset($_COOKIE['USID']) && !empty($_COOKIE['USID']))
	{
		setcookie('USID','',0,'/');
	}
	header("location:/"); 
	exit();
}







function auth_password_restore()
{
	global $sql_pref, $conn_id, $path, $path_www, $path_domen, $page_header1;
	global $auth_restorepass_info;
	$out="";
	$page_header1="Восстановление пароля";
	if (isset($_REQUEST['submit']) && isset($_REQUEST['formname']) && $_REQUEST['formname']=="passwordrestore")
	{
		$email=@$_REQUEST['email'];
		
		//Проверка email
		$sql_query="SELECT pass FROM ".$sql_pref."_users WHERE email='".$email."' && enable='Yes'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if(mysql_num_rows($sql_res)>0)
		{
			$mailtitle="www.ensor.ru: восстановление данных для доступа к сайту ".$path_www;
			$mailheader="From: Ensor.ru <robot@".$path_domen.">\r\n";
            $mailheader.="MIME-Version: 1.0\r\n";        
			$mailheader.="Content-Type: text/plain;\n charset=\"WINDOWS-1251\"";
			$mailcontent="";
			$mailcontent.="Здравствуйте! \n\n";
			$mailcontent.="На e-mail ".$email." на сайте ".$path_www." зарегистрирован следующий пароль:\n\n";
                     
			while(list($pass)=mysql_fetch_row($sql_res))
			{
				$pass=stripslashes($pass);
				$mailcontent.=$pass."\n";
				$mailcontent.="\n";
			}
            $mailcontent.="--\nС уважением,\nСлужба поддержки www.ensor.ru.\n";
            $mailcontent.="--------------------------------------------------------------";
           
			mail($email,$mailtitle,$mailcontent,$mailheader);
			$auth_restorepass_info="Пароль для доступа к сайту выслан на адрес <b>".$email."</b>!";
		}
		else $auth_restorepass_info="<font color='#FF0000'>E-mail <b>".$email."</b> в нашей базе не зарегистрирован!</font>";
	}
	
	if (isset($auth_restorepass_info)) $out.="<br><br><div>".$auth_restorepass_info."</div>";
	else $out.="<p>Введите ваш регистрационный e-mail и мы вышлем на него данные для доступа к сайту.</p>
	<br>
	<form action='' method=post name=password_restore>
	<table cellpadding=3 cellspacing=0 border=0 style='border: solid 0px #999999;'>
		<tr>
			<td align=left>E-Mail:</td>
			<td align=left valign=top><input type=Text maxlength=30 size=25 name=email value=''></td>
		</tr>
		<tr>
			<td align=left>&nbsp;</td>
			<td align=left><input type=hidden name=formname value=passwordrestore><input type=Submit name=submit value='Восстановить!' style='font-size: 10px; width:100px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></td>
		</tr>
	</table>
	</form>";

	return($out);
}







function auth_register()
{
	global $sql_pref, $conn_id, $path, $path_www, $path_domen, $page_header1, $user_id;
	$out="";
    $antispam=show_codepic();
        
    if (@$user_id>0) { header("location:/"); exit;}
	if (isset($_REQUEST['formname']) && $_REQUEST['formname']=="loginform") return ($out);
	if (isset($_REQUEST['submit']))
	{
		if (isset($_REQUEST['surname']) AND !empty($_REQUEST['surname'] )) 
        { 
            $surname=addslashes($_REQUEST['surname']); 
            $surname=str_replace(" ","_",$surname);
        }
        else
        {
            $error['surname']="Ошибка!";
        }
		if (isset($_REQUEST['name']) AND !empty($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $error['name']="Ошибка!";
		if (isset($_REQUEST['name2']) AND !empty($_REQUEST['name2'])) $name2=addslashes($_REQUEST['name2']); else $error['name2']="Ошибка!";
		if (isset($_REQUEST['email']) AND !empty($_REQUEST['email'])) $email=$_REQUEST['email']; else $error['email']="Ошибка!";
		if (isset($_REQUEST['pol']) AND !empty($_REQUEST['pol'])) $pol=$_REQUEST['pol']; else $error['pol']="Ошибка!";
		if (isset($_REQUEST['a_s_u']) AND !empty($_REQUEST['a_s_u']) AND $_REQUEST['a_s_u']==$_REQUEST['a_s_t']) $pic=$_REQUEST['a_s_p']; else $error['pic']="Ошибка!";
		if (isset($_REQUEST['dt_birth']) AND !empty($_REQUEST['dt_birth'])) $dt_birth=$_REQUEST['dt_birth']; else $dt_birth="0000-00-00";
		if (isset($_REQUEST['phone_work']) AND !empty($_REQUEST['phone_work'])) $phone_work=addslashes($_REQUEST['phone_work']); else $phone_work="";


		if (email_valid(@$email)==false) $error['email']="Ошибка!";
        
		$sql_query="SELECT id FROM ".$sql_pref."_users WHERE email='".@$email."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) $error['email']="Ошибка! ".$email." уже зарегистрирован."; 



		if (!isset($error) || count(@$error)==0)
		{
			$dt_reg=date("Y-m-d H:i:s");
			$pass=getpass();
            $status="user";
			
			$sql_query="INSERT INTO ".$sql_pref."_users (pass,surname,name,name2,email,pol,phone_work,enable,dt_birth,dt_reg,status,forum_admin) VALUES ('".$pass."','".$surname."','".$name."','".$name2."','".$email."','".$pol."','".$phone_work."','Yes','".$dt_birth."','".$dt_reg."','".$status."','No')";
			$sql_res=mysql_query($sql_query, $conn_id);
			
			
			$mailtitle="Регистрация на сайте Ensor.ru";
			$mailheader="From: robot@".$path_domen."\n";
			$mailheader.="Content-Type: text/plain;\n charset=\"WINDOWS-1251\"";
		
			$mailcontent= $name." ".@$name2.",\nдобрый день!\n";
			$mailcontent.="Вы зарегистрировались на сайте Ensor.ru \n\n";
			$mailcontent.="Для входа на сайт Вам требуется ввести в форму регистрации:\n";
			$mailcontent.="E-mail: ".$email."\n";
			$mailcontent.="Предварительный пароль: ".$pass."\n";
			$mailcontent.="\n";
			$mailcontent.="С уважением,\n администрация сайта Ensor.ru";
			
			mail($email,$mailtitle,$mailcontent,$mailheader);
			
			$out.="<h2>Спасибо за регистрацию!</h2>";
			$out.="<br><p>Данные для входа на сайт мы выслали вам на адрес <b>".$email."</b></p>";
			return ($out);
		}
	}
	
	$page_header1="Регистрация нового пользователя";
    
	$xc2_inc=file_get_contents($path."inc/xc2.inc");
	$dt_birth=date("1980-01-01");
	$pol_show="<input type=radio name=pol value='m' checked> мужской &nbsp;&nbsp; <input type=radio name=pol value='w'> женский";
	
	$out.=$xc2_inc."
	".@$error_info."
    
    <div style='padding: 10 0 10 0;'>Обратите внимание! Все поля, кроме поля \"Телефон\", обязательны для заполнения!<BR>
    Убедительная просьба не регистрироваться под вымышленными именами! Не тратьте свое и наше время! Если Вы профессионал - у Вас отсутствует необходимость прятаться за вымышленным именем. Спасибо за понимание!</div>
	<form action='' method=post name=add_profile_form enctype='multipart/form-data'>
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Фамилия:</div>
							<div><input type=Text maxlength=70 name=surname id=surname value='".@$surname."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['surname']."</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Имя:</div>
							<div><input type=Text maxlength=70 name=name id=name value='".@$name."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['name']."</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Отчество:</div>
							<div><input type=Text maxlength=70 name=name2 id=name2 value='".@$name2."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['name2']."</div>
						</div>
					</td>
				</tr>
			</table>
			
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>E-mail (действующий. На него отправляется пароль!):</div>
							<div><input type=Text maxlength=70 name=email id=email value='".@$email."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['email']."</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Телефон (рабочий, по желанию):</div>
							<div><input type=Text maxlength=70 name=phone_work id=phone_work value='".@$phone_work."' style='width:170px;font-size:14px;'></div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
                            &nbsp;
						</div>
					</td>
				</tr>
			</table>

			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td width=170 align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Дата рождения:</div>
                            <div id=holder></div><input onkeydown='return false;' type=Text maxlength=70 name=dt_birth id=dt_birth value='".@$dt_birth."' style='width:100px;font-size:14px;cursor:pointer;' onclick='showCalendar(\"\",document.getElementById(\"dt_birth\"),null,\"".$dt_birth."\",\"holder\",0,25,1)'>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Пол:</div>
							<div>".$pol_show."</div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['pol']."</div>
						</div>
					</td>
                    <td width=20>&nbsp;</td>
					<td align=left>
                        <div style='padding: 10 0 10 0;'>
    							<div>Код (защита от спама):</div>
    							<div><img src='".$antispam['pic']."' border='1' width='100' height='25'><BR><input style='width:100px' name='a_s_u'></div>
                                <div style='color:#ff0000;font-weight:bold;'>".$error['pic']."</div>
    					</div>
                        <input type=hidden name=a_s_t value=\"".$antispam['code']."\">
                        <input type=hidden name=a_s_p value=\"".$antispam['pic']."\">
                    </td>
				</tr>
			</table>
			
			
			<div style='padding: 10 0 10 0;'>
				<div><input type=Submit name=submit value=Сохранить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
			</div>
	</form>
	";



	return($out);
}




function auth_activation()
{
	global $sql_pref, $conn_id, $rub_url, $path, $path_www, $path_domen, $page_header1, $user_id;
	$out="";
    
    $actkod=@$rub_url[2];
    if (!empty($actkod))
    {
    	$sql_query="SELECT id, surname, name, name2, email, pass FROM ".$sql_pref."_users WHERE actkod='".$actkod."'&&enable='No'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
        {
            list($id, $surname, $name, $name2, $email, $pass)=mysql_fetch_row($sql_res);
            $surname=stripslashes($surname);$name=stripslashes($name);$name2=stripslashes($name2);
            
            $dt_reg=date("Y-m-d H:i:s");
			
			$sql_query="UPDATE ".$sql_pref."_users SET enable='Yes', actkod='', dt_reg='".$dt_reg."' WHERE id=".$id."";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			
			
			$mailtitle="Регистрация на сайте Ensor.ru";
			$mailheader="From: robot@".$path_domen."\n";
			$mailheader.="Content-Type: text/plain;\n charset=\"WINDOWS-1251\"";
		
			$mailcontent= $name." ".@$name2.",\nдобрый день!\n";
			$mailcontent.="Вы зарегистрировались на сайте Ensor.ru \n\n";
			$mailcontent.="Для входа на сайт Вам требуется ввести в форму регистрации:\n";
			$mailcontent.="E-mail: ".$email."\n";
			$mailcontent.="Предварительный пароль: ".$pass."\n";
			$mailcontent.="\n";
			$mailcontent.="С уважением,\n администрация сайта Ensor.ru";
			
			mail($email,$mailtitle,$mailcontent,$mailheader);
			
			$out.="<h2>Спасибо за регистрацию!</h2>";
			$out.="<br><p>Данные для входа на сайт мы выслали вам на адрес <b>".$email."</b></p>";
			return ($out);
        } 
        
    }
 

	return("Ошибка!");
}



function auth_register_inv_user()
{
	global $sql_pref, $conn_id, $path, $path_www, $path_domen, $page_header1, $user_id, $user_rate_main, $user_rate_sec;
	$out="";
    //if (@$user_id>0) { header("location:/"); exit;}
    
    $sql_query1="SELECT id, surname, name, name2, email FROM ".$sql_pref."_users WHERE id=".$user_id;
	//echo $sql_query1;
    $sql_res1=mysql_query($sql_query1, $conn_id);
	if (mysql_num_rows($sql_res1)>0)
	{
	   list($m_id, $m_surname, $m_name, $m_name2, $m_email)=mysql_fetch_row($sql_res1);
		$m_surname=stripslashes($m_surname); $m_name=stripslashes($m_name); $m_name2=stripslashes($m_name2); 
		$m_name_show=$m_surname." ".$m_name." ".$m_name2;
    }
    
	if (isset($_REQUEST['formname']) && $_REQUEST['formname']=="loginform") return ($out);
	if (isset($_REQUEST['submit']))
	{
		if (isset($_REQUEST['surname']) AND !empty($_REQUEST['surname'])) $surname=addslashes($_REQUEST['surname']); else $error['surname']="Ошибка!";
		if (isset($_REQUEST['name']) AND !empty($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $error['name']="Ошибка!";
		if (isset($_REQUEST['name2']) AND !empty($_REQUEST['name2'])) $name2=addslashes($_REQUEST['name2']); else $error['name2']="Ошибка!";
		if (isset($_REQUEST['email']) AND !empty($_REQUEST['email'])) $email=$_REQUEST['email']; else $error['email']="Ошибка!";
		if (isset($_REQUEST['pol']) AND !empty($_REQUEST['pol'])) $pol=$_REQUEST['pol']; else $error['pol']="Ошибка!";
		if (isset($_REQUEST['dt_birth']) AND !empty($_REQUEST['dt_birth'])) $dt_birth=$_REQUEST['dt_birth']; else $dt_birth="0000-00-00";
		if (isset($_REQUEST['phone_work']) AND !empty($_REQUEST['phone_work'])) $phone_work=addslashes($_REQUEST['phone_work']); else $phone_work="";


		if (email_valid(@$email)==false) $error['email']="Ошибка!";
        
		$sql_query="SELECT id FROM ".$sql_pref."_users WHERE email='".@$email."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) $error['email']="Ошибка! ".$email." уже зарегистрирован."; 



		if (!isset($error) || count(@$error)==0)
		{
			$dt_reg=date("Y-m-d H:i:s");
			$pass=getpass();
            $status="user";
			
			$sql_query="INSERT INTO ".$sql_pref."_users (pass,surname,name,name2,email,pol,phone_work,enable,dt_birth,dt_reg,status,forum_admin,invited_by) VALUES ('".$pass."','".$surname."','".$name."','".$name2."','".$email."','".$pol."','".$phone_work."','Yes','".$dt_birth."','".$dt_reg."','".$status."','No', '".$user_id."')";
			$sql_res=mysql_query($sql_query, $conn_id);
            $last_user_id=mysql_insert_id();
			
            rate_main($user_id, "пригласил коллегу", $user_rate_main, $user_rate_sec);
            $sql_query2="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'user_invite','".$last_user_id."')";
            $sql_res2=mysql_query($sql_query2, $conn_id);
            
			$mailtitle="Регистрация на сайте ".$path_www;
			$mailheader="From: robot@".$path_domen."\n";
			$mailheader.="Content-Type: text/plain;\n charset=\"WINDOWS-1251\"";
		
			$mailcontent="";
			$mailcontent.=$m_name_show." (".$m_email.") приглашает Вас зарегистрироваться на сайте Энергетического Сообщества России ".$path_www." \n\n";
			$mailcontent.="Ваши данные:\n";
			$mailcontent.="ФИО: ".$surname." ".$name." ".@$name2."\n";
			$mailcontent.="E-mail: ".$email."\n";
			$mailcontent.="Пароль: ".$pass."\n";
			$mailcontent.="\n";
			$mailcontent.="До встречи на сайте! \n";
			$mailcontent.="\n";
			
			mail($email,$mailtitle,$mailcontent,$mailheader);
			
			$out.="<h2>Спасибо за приглашение!</h2>";
			$out.="<br><p>Данные для входа на сайт мы выслали на адрес <b>".$email."</b></p>";
			return ($out);
		}
	}
	
	$page_header1="Пригласить нового пользователя";
    
	$xc2_inc=file_get_contents($path."inc/xc2.inc");
	$dt_birth=date("1980-01-01");
	$pol_show="<input type=radio name=pol value='m' checked> мужской &nbsp;&nbsp; <input type=radio name=pol value='w'> женский";
	
	$out.=$xc2_inc."
	".@$error_info."
    
    <div style='padding: 10 0 10 0;'>Обратите внимание! Все поля, кроме поля \"Телефон\", обязательны для заполнения!</div>
	<form action='' method=post name=add_profile_form enctype='multipart/form-data'>
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Фамилия:</div>
							<div><input type=Text maxlength=70 name=surname id=surname value='".@$surname."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['surname']."</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Имя:</div>
							<div><input type=Text maxlength=70 name=name id=name value='".@$name."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['name']."</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Отчество:</div>
							<div><input type=Text maxlength=70 name=name2 id=name2 value='".@$name2."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['name2']."</div>
						</div>
					</td>
				</tr>
			</table>
			
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>E-mail:</div>
							<div><input type=Text maxlength=70 name=email id=email value='".@$email."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['email']."</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Телефон (рабочий, по желанию):</div>
							<div><input type=Text maxlength=70 name=phone_work id=phone_work value='".@$phone_work."' style='width:170px;font-size:14px;'></div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
                            &nbsp;
						</div>
					</td>
				</tr>
			</table>

			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td width=170 align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Дата рождения:</div>
                            <div id=holder></div><input onkeydown='return false;' type=Text maxlength=70 name=dt_birth id=dt_birth value='".@$dt_birth."' style='width:100px;font-size:14px;cursor:pointer;' onclick='showCalendar(\"\",document.getElementById(\"dt_birth\"),null,\"".$dt_birth."\",\"holder\",0,25,1)'>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Пол:</div>
							<div>".$pol_show."</div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['pol']."</div>
						</div>
					</td>
				</tr>
			</table>
			
			
			<div style='padding: 10 0 10 0;'>
				<div><input type=Submit name=submit value=Пригласить! style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
			</div>
	</form>
	";

    //echo "123123123";

	return($out);
}





function getpass()
{
	global $sql_pref, $conn_id, $path;
	
	$out = "";
	$template='1234567890abcdefghijkmlnoprqstuvwxyzABCDEFGHIJKMLNOPRQSTUVWXYZ'; 
	$len=strlen($template);
	for ($i=0; $i<8; $i++) { $out.=substr($template,rand(0,$len),1);	}
	return ($out);
}
















function auth_edit_profile()
{
	global $sql_pref, $conn_id, $path;
	global $user_id, $user_login, $user_email, $user_site, $page_header1, $users_img_width, $users_img_height, $users_avatar_width, $users_avatar_height;
	$out="";
	
	$page_header1="Редактирование профиля";
	
	if (isset($_REQUEST['action']) && $_REQUEST['action']=="img_del")
	{
		common_del_file($path."files/users/img/".$user_id.".jpg");
		common_del_file($path."files/users/avatar/".$user_id.".jpg");
		header("location:./"); exit();
	}
	if (isset($_REQUEST['submit']))
	{
		if (isset($_REQUEST['surname']) AND !empty($_REQUEST['surname'])) $surname=addslashes($_REQUEST['surname']); else $error['surname']="Ошибка!";
		if (isset($_REQUEST['name']) AND !empty($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $error['name']="Ошибка!";
		if (isset($_REQUEST['name2']) AND !empty($_REQUEST['name2'])) $name2=addslashes($_REQUEST['name2']); else $error['name2']="Ошибка!";
		if (isset($_REQUEST['email']) AND !empty($_REQUEST['email'])) $email=$_REQUEST['email']; else $error['email']="Ошибка!";
		if (isset($_REQUEST['pass']) AND !empty($_REQUEST['pass']) AND strlen($_REQUEST['pass'])>=6) $pass=$_REQUEST['pass']; else $error['pass']="Ошибка!";
		if (isset($_REQUEST['pol']) AND !empty($_REQUEST['pol'])) $pol=$_REQUEST['pol']; else $error['pol']="Ошибка!";
		if (isset($_REQUEST['dt_birth']) AND !empty($_REQUEST['dt_birth'])) $dt_birth=$_REQUEST['dt_birth']; else $dt_birth="0000-00-00";
		if (isset($_REQUEST['phone_work']) AND !empty($_REQUEST['phone_work'])) $phone_work=addslashes($_REQUEST['phone_work']); else $phone_work="";
		if (isset($_REQUEST['phone_home']) AND !empty($_REQUEST['phone_home'])) $phone_home=addslashes($_REQUEST['phone_home']); else $phone_home="";
		if (isset($_REQUEST['phone_mobile']) AND !empty($_REQUEST['phone_mobile'])) $phone_mobile=addslashes($_REQUEST['phone_mobile']); else $phone_mobile="";
		if (isset($_REQUEST['company_id']) AND !empty($_REQUEST['company_id'])) $company_id=$_REQUEST['company_id']; else $company_id="0";
		if (isset($_REQUEST['doljnost']) AND !empty($_REQUEST['doljnost'])) $doljnost=addslashes($_REQUEST['doljnost']); else $doljnost="";
		if (isset($_REQUEST['expirience']) AND !empty($_REQUEST['expirience']) AND $_REQUEST['expirience']!="<BR>") $expirience=addslashes($_REQUEST['expirience']); else $expirience="";
		if (isset($_REQUEST['descr']) AND !empty($_REQUEST['descr']) AND $_REQUEST['descr']!="<BR>") $descr=addslashes($_REQUEST['descr']); else $descr="";
		if (isset($_REQUEST['vuz']) AND !empty($_REQUEST['vuz'])) $vuz=addslashes($_REQUEST['vuz']); else $vuz="";
		if (isset($_REQUEST['specialnost']) AND !empty($_REQUEST['specialnost'])) $specialnost=addslashes($_REQUEST['specialnost']); else $specialnost="";
		if (isset($_REQUEST['city_id']) AND !empty($_REQUEST['city_id'])) $city_id=$_REQUEST['city_id']; else $city_id="0";
		if (isset($_REQUEST['maillist']) && $_REQUEST['maillist']=="Yes") $maillist="Yes"; else $maillist="No";
        if (isset($_REQUEST['maillist_exb']) && $_REQUEST['maillist_exb']=="Yes") $maillist_exb="Yes"; else $maillist_exb="No";


		if (email_valid(@$email)==false) $error['email']="Ошибка!";
        elseif ($email!=$_REQUEST['email'])
        {
    		$sql_query="SELECT id FROM ".$sql_pref."_users WHERE email='".@$email."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
    		if (mysql_num_rows($sql_res)>0) $error['email']="Ошибка! ".$email." уже зарегистрирован.";
        } 


		if (!isset($error) || count(@$error)==0)
		{
    		$sql_query="UPDATE ".$sql_pref."_users SET surname='".$surname."', name='".$name."', name2='".$name2."', email='".$email."', pass='".$pass."', pol='".$pol."', dt_birth='".$dt_birth."', phone_work='".$phone_work."', phone_home='".$phone_home."', phone_mobile='".$phone_mobile."', company_id='".$company_id."', doljnost='".$doljnost."', expirience='".$expirience."', descr='".$descr."', vuz='".$vuz."', specialnost='".$specialnost."', city_id='".$city_id."', maillist='".$maillist."', maillist_exb='".$maillist_exb."' WHERE id='".$user_id."'";
    		$sql_res=mysql_query($sql_query, $conn_id);
    
            $sql_query2="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'user_info_change','".$user_id."')";
            $sql_res2=mysql_query($sql_query2, $conn_id);

    		if (is_uploaded_file($_FILES["file_name"]["tmp_name"]))
    		{
    			$mime=$_FILES['file_name']['type'];
    			if ($mime=="image/jpeg" || $mime=="image/pjpeg")
    			{
                    $src=$_FILES["file_name"]["tmp_name"];
                    $dest=$path."files/users/img/".$user_id.".jpg";
                    $resize=true;
                    common_del_file($dest);
                    $out.=common_save_img($src, $dest, $resize, $users_img_width, $users_img_height, 80, $mime);
                    
    				$src=$dest;
    				$dest=$path."files/users/avatar/".$user_id.".jpg";
    				$resize=true;
    				common_del_file($dest);
    				common_save_img($src, $dest, $resize, $users_avatar_width, $users_avatar_height, 80, $mime);
    			}
    			else $out.="<div>Ошибка загрузки изображения</div>";;
    		}


    		$out.="<div>Информация обновлена</div>";
    		return ($out);
        }
	}
    
    
	if (!isset($_REQUEST['submit']))
	{
        
    	$sql_query="SELECT id, surname, name, name2, email, phone_work, phone_home, phone_mobile, pol, dt_birth, dt_reg, company_id, doljnost, expirience, descr, vuz, specialnost, city_id, pass, maillist, maillist_exb FROM ".$sql_pref."_users WHERE id='".$user_id."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
    	{
    		list($id, $surname, $name, $name2, $email, $phone_work, $phone_home, $phone_mobile, $pol, $dt_birth, $dt_reg, $company_id, $doljnost, $expirience, $descr, $vuz, $specialnost, $city_id, $pass, $maillist, $maillist_exb)=mysql_fetch_row($sql_res);
    		$surname=stripslashes($surname); $name=stripslashes($name); $name2=stripslashes($name2); $phone_work=stripslashes($phone_work); $phone_home=stripslashes($phone_home); $phone_mobile=stripslashes($phone_mobile); $doljnost=stripslashes($doljnost); $expirience=stripslashes($expirience); $vuz=stripslashes($vuz); $specialnost=stripslashes($specialnost); $descr=stripslashes($descr);
            if ($city_id>0)
            {
            	$sql_query="SELECT country.id FROM ".$sql_pref."_reg_countries AS country, ".$sql_pref."_reg_cities AS city WHERE city.id='".$city_id."'&&city.country_id=country.id";
            	$sql_res_1=mysql_query($sql_query, $conn_id);
            	if (mysql_num_rows($sql_res_1)>0)
            	{
            		list($country_id)=mysql_fetch_row($sql_res_1);
            	}
            }
			if ($maillist=="Yes") $check="checked"; else $check="";
			$maillist_show="<input type=checkbox name=maillist value='Yes' ".$check.">";
            
            if ($maillist_exb=="Yes") $check_exb="checked"; else $check_exb="";
			$maillist_exb_show="<input type=checkbox name=maillist_exb value='Yes' ".$check_exb.">";
		}
    }
    
    
    
    $company="";
	$company.="<select name=company_id style='width:360px;font-size:14px;'>";
    $company.="<option value='0'>Нет данных</option>";
	$sql_query="SELECT id, name FROM ".$sql_pref."_companies WHERE enable='Yes' ORDER BY name";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($c_id, $c_name)=mysql_fetch_row($sql_res))
		{
			$c_name=stripslashes($c_name);
            if (@$company_id==$c_id) $sel="selected"; else $sel="";
			$company.="<option value='".$c_id."' ".$sel.">".$c_name."</option>";
		}
	}
	$company.="</select>";
    
    
    
    $city_script="<script type='text/javascript' src='/inc/js/jquery_cities.js'></script>";


    $city="";

	$city.="<select name=country_id id=country_id style='width:220px;font-size:14px;height:22px;'>";
    $city.="<option value='0'>Нет данных</option>";
	$sql_query="SELECT id, name FROM ".$sql_pref."_reg_countries ORDER BY name";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($reg_c_id, $reg_c_name)=mysql_fetch_row($sql_res))
		{
			$reg_c_name=stripslashes($reg_c_name);
            if (@$country_id==$reg_c_id) $sel="selected"; else $sel="";
			$city.="<option value='".$reg_c_id."' ".$sel.">".$reg_c_name."</option>";
		}
	}
    $city.="</select>&nbsp;";
    
    $city.="<select name=city_id id=city_id style='width:324px;font-size:14px;height:22px;'>";
    if (@$country_id>0)
    {
        $sql_query="SELECT c.id, c.name, r.name FROM ".$sql_pref."_reg_regions AS r, ".$sql_pref."_reg_cities AS c WHERE c.country_id='".$country_id."'&&c.region_id=r.id ORDER BY c.name";
        $sql_res=mysql_query($sql_query, $conn_id);
        if (mysql_num_rows($sql_res)>0)
        {
        	while(list($reg_c_id, $reg_c_name, $reg_r_name)=mysql_fetch_row($sql_res))
        	{
        		$reg_c_name=stripslashes($reg_c_name); $reg_r_name=stripslashes($reg_r_name);
                if (!empty($reg_r_name)) $reg_r_name_show=" (".$reg_r_name.")"; else $reg_r_name_show="";
                $reg_c_name_show=$reg_c_name.$reg_r_name_show;
                if (@$city_id==$reg_c_id) $sel="selected"; else $sel="";
    			$city.="<option value='".$reg_c_id."' ".$sel.">".$reg_c_name_show."</option>";
        	}
        }
    }
    else
    {
        $city.="<option value=0> </option>";
    }
    $city.="</select>";
    
    
	$polman="";$polwoman="";
	if (@$pol=="m") $polman=" checked"; else $polwoman=" checked";
	$pol_show="<input type=radio name=pol value='m'".$polman."> мужской &nbsp;&nbsp; <input type=radio name=pol value='w'".$polwoman."> женский";
    
	$xc2_inc=file_get_contents($path."inc/xc2.inc");
	
    
	//$img_show="<div style='padding:10 0;'>Не загружено</div>";
	if (file_exists($path."files/users/img/".$user_id.".jpg"))
		$img_show="<div style='padding:10 0;'><img src='/files/users/avatar/".$user_id.".jpg' border=0>&nbsp;<a href='?action=img_del'><img src='/img/del.gif' width=25 height=13 border=0></a></div>";
    
    $expirience_script="    
        <script type='text/javascript'>
            new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('expirience');
        </script>";
    $descr_script="    
        <script type='text/javascript'>
            new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('descr');
        </script>";
    
	
	$out.=$xc2_inc.$city_script."
	".@$error_info."
    
    <div>Дата регистрации: ".date('d.m.Y',strtotime($dt_reg))."</div>
    
	<form action='' method=post name=edit_profile_form enctype='multipart/form-data'>
        <input type=hidden name=oldemail value='".@$email."'>
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Фамилия:</div>
							<div><input type=Text maxlength=70 name=surname id=surname value='".@$surname."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['surname']."</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Имя:</div>
							<div><input type=Text maxlength=70 name=name id=name value='".@$name."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['name']."</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Отчество:</div>
							<div><input type=Text maxlength=70 name=name2 id=name2 value='".@$name2."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['name2']."</div>
						</div>
					</td>
				</tr>
			</table>
			
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>E-mail:</div>
							<div><input type=Text maxlength=70 name=email id=email value='".@$email."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['email']."</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left width=170>
						<div style='padding: 10 0 10 0;'>
							<div>Пароль: <span style='font-size:11px;color:#555;'>(не менее 6 символов)</span></div>
                            <div><input type=Text maxlength=70 name=pass id=pass value='".@$pass."' style='width:170px;font-size:14px;'></div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['pass']."</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left width=170>
						<div style='padding: 10 0 10 0;'>
							<div>Дата рождения (гггг-мм-дд):</div>
                            <div id=holder></div><input type=Text maxlength=70 name=dt_birth id=dt_birth value='".@$dt_birth."' style='width:100px;font-size:14px;cursor:pointer;' onclick='showCalendar(\"\",document.getElementById(\"dt_birth\"),null,\"".$dt_birth."\",\"holder\",0,25,1)'>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Пол:</div>
							<div>".$pol_show."</div>
                            <div style='color:#ff0000;font-weight:bold;'>".$error['pol']."</div>
						</div>
					</td>
				</tr>
			</table>

			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Телефон (рабочий):</div>
							<div><input type=Text maxlength=70 name=phone_work id=phone_work value='".@$phone_work."' style='width:170px;font-size:14px;'></div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Телефон (домашний):</div>
							<div><input type=Text maxlength=70 name=phone_home id=phone_home value='".@$phone_home."' style='width:170px;font-size:14px;'></div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Телефон (мобильный):</div>
							<div><input type=Text maxlength=70 name=phone_mobile id=phone_mobile value='".@$phone_mobile."' style='width:170px;font-size:14px;'></div>
						</div>
					</td>
				</tr>
			</table>

			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Страна и город:</div>
                            ".@$city."
						</div>
					</td>
				</tr>
			</table>
			
			
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td width=360 align=left valign=top>
						<div style='padding: 10 0 10 0;'>
							<div>Компания:</div>
                            ".@$company."
                            <div style='padding:2 0 2 0;font-size:11px;color:#555;'>Если вы не нашли в списке свою компанию, <span style='font-size:11px;cursor:pointer;text-decoration:underline;' onClick=\"window.open('/feedback.html?subj=auth_company', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\">сообщите нам</span> и она будет добавлена в нашу базу.</div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left valign=top>
						<div style='padding: 10 0 10 0;'>
							<div>Должность:</div>
                            <div><input type=Text maxlength=70 name=doljnost id=doljnost value='".@$doljnost."' style='width:170px;font-size:14px;'></div>
						</div>
					</td>
				</tr>
			</table>
			
			
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td width=170 align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Опыт работы:</div>
                            <textarea rows=8 id=expirience name=expirience style='width:360px;height:120;font-size:14px;'>".@$expirience."</textarea>
						</div>
					</td>
				</tr>
			</table>
			
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td width=170 align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Несколько слов о себе:</div>
                            <textarea rows=8 id=descr name=descr style='width:360px;height:120;font-size:14px;'>".@$descr."</textarea>
						</div>
					</td>
				</tr>
			</table>
			
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>ВУЗ:</div>
							<div><input type=Text maxlength=70 name=vuz id=vuz value='".@$vuz."' style='width:170px;font-size:14px;'></div>
						</div>
					</td>
					<td width=20>&nbsp;</td>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>Специальность:</div>
							<div><input type=Text maxlength=70 name=specialnost id=specialnost value='".@$specialnost."' style='width:170px;font-size:14px;'></div>
						</div>
					</td>
				</tr>
			</table>
            
			<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>".$maillist_show." - получать почтовую рассылку</div>
						</div>
					</td>
				</tr>
				<tr>
					<td align=left>
						<div style='padding: 10 0 10 0;'>
							<div>".$maillist_exb_show." - получать приглашения на выставки</div>
						</div>
					</td>
				</tr>
			</table>
			
            
            <div>Ваше фото (jpg):</div>
            ".$img_show."
            <div><input type=file name=file_name size=35'></div>
            
			
			<div style='padding: 20 0 10 0;'>
				<div><input type=Submit name=submit value=Изменить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
			</div>
	</form>
    ".$expirience_script.$descr_script."
	";

	return($out);
}



function auth_user_phones()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $path_www, $path_companies, $page_header1, $path_contacts, $page_title; 
	$out="";
    $page_header1="Мои контакты";
	$page_title="Личный кабинет";

	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	$perpage=20; $first=$perpage*($page-1);
	$sql_query="SELECT id FROM ".$sql_pref."_user_phones WHERE user_id='".$user_id."'";
	$pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums($page, $perpage, $sql_query)."</div>";

	if (isset($_REQUEST['new_memo'])) {
	$phone_id=$_REQUEST['phone_id'];
	$new_memo=$_REQUEST['new_memo'];
	$new_mamo=strip_tags($new_memo);
	$new_memo=addslashes($new_memo);
	$sql_query="SELECT user_id FROM ".$sql_pref."_user_phones WHERE id='".$phone_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($check_user_id)=mysql_fetch_row($sql_res);
	if ($check_user_id==$user_id) {$sql_query="UPDATE ".$sql_pref."_user_phones SET memo='".$new_memo."' WHERE id='".$phone_id."'"; $sql_res=mysql_query($sql_query, $conn_id);}
	}
	
	if (isset($_REQUEST['action']) and $_REQUEST['action']=="user_phone_delete") {
	$phone_id=$_REQUEST['phone_id'];
	$sql_query="SELECT user_id FROM ".$sql_pref."_user_phones WHERE id='".$phone_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($check_user_id)=mysql_fetch_row($sql_res);
	if ($check_user_id==$user_id) {$sql_query="DELETE FROM ".$sql_pref."_user_phones WHERE id='".$phone_id."'"; $sql_res=mysql_query($sql_query, $conn_id);}
	}
	
	
	$sql_query="SELECT memo, ".$sql_pref."_user_phones.id, ".$sql_pref."_users.id, surname, name, name2, email, phone_work, phone_home, phone_mobile, company_id, doljnost, specialnost FROM ".$sql_pref."_user_phones INNER JOIN ".$sql_pref."_users ON ".$sql_pref."_user_phones.contact_id = ".$sql_pref."_users.id WHERE ".$sql_pref."_user_phones.user_id='".$user_id."' ORDER BY surname, name, name2 LIMIT ".$first.",".$perpage;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
{
    		$num_users=mysql_num_rows($sql_res);
    		$out.="<table cellpadding=5 cellspacing=0 border=0>";
    		$out.="
    			<tr><td colspan=6 style='border-bottom:solid 1px #777777;'>&nbsp;</td></tr>
    			<tr bgcolor='#f2f2f2'>
					<td style='border-bottom:solid 1px #777777;'>&nbsp;</td>
    				<td width=250 align=left style='border-bottom:solid 1px #777777;'>ФИО</td>
    				<td width=100 align=left valign=top style='border-bottom:solid 1px #777777;'>Компания</td>
					<td width=100 align=left valign=top style='border-bottom:solid 1px #777777;'>Телефон</td>
					<td width=100 align=left valign=top style='border-bottom:solid 1px #777777;'>Примечание</td><td style='border-bottom:solid 1px #777777;'>&nbsp;</td></tr>";


    		while(list($memo, $phone_id, $id, $surname, $name, $name2, $email, $phone_work, $phone_home, $phone_mobile, $company_id, $doljnost, $specialnost)=mysql_fetch_row($sql_res))
    		{
        		$surname=stripslashes($surname); $name=stripslashes($name); $name2=stripslashes($name2); $phone_work=stripslashes($phone_work); $phone_home=stripslashes($phone_home); $phone_mobile=stripslashes($phone_mobile); $doljnost=stripslashes($doljnost); $specialnost=stripslashes($specialnost); $memo=stripslashes($memo);
		$phones_table="<table cellpadding='0' cellspacing='0'>";
		if ($phone_work) $phones_table.="<tr><td><img src='/img/stock_cell-phone.png' alt='Рабочий'></td><td><NOBR>".$phone_work."</NOBR></td></tr>";
		if ($phone_mobile) $phones_table.="<tr><td><img src='/img/stock_cell-phone.png' alt='Мобильный'></td><td><NOBR>".$phone_mobile."</NOBR></td></tr>";
		if ($phone_home) $phones_table.="<tr><td><img src='/img/stock_cell-phone.png' alt='Домашний'></td><td><NOBR>".$phone_home."</NOBR></td></tr>";
		$phones_table.="</table>";
		$memo="<form name='form_name' action='index.html' method='post' enctype='multipart/form-data'>
<NOBR><textarea name='new_memo' rows='2'>$memo</textarea><input type='hidden' name='phone_id' value='$phone_id'><INPUT type='image' src='/img/small/ok.gif' alt='Сохранить'></NOBR></form>";
		$mess_send="<a href='/auth/messages_add/?user_id_to=".$id."'><img src='/img/message_send.png' alt='Написать сообщение' border=0></a>";
		$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?phone_id=".$phone_id."&action=user_phone_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
                
                $name_show=$surname." ".$name." ".$name2;
                
                $company_name="-";
        		$sql_query="SELECT name FROM ".$sql_pref."_companies WHERE id='".$company_id."'";
        		$sql_res_1=mysql_query($sql_query, $conn_id);
        		if(mysql_num_rows($sql_res_1)>0)
        		{
        			list($company_name)=mysql_fetch_row($sql_res_1);
        			$company_name="<a href='/".$path_companies."/".$company_id.".html' style='font-weight:bold;'>".StripSlashes($company_name)."</a>";
        		}
    			$out.="
    				<tr>
    					<td align=left valign=middle style='border-bottom:solid 1px #777777;'>".$mess_send."</td>
				<td align=left valign=middle style='border-bottom:solid 1px #777777;'><a href='/discussions/our_contacts/".$id.".html'>".$name_show."</a></td>
    					<td align=left valign=middle style='border-bottom:solid 1px #777777;'>".$company_name."<BR><small><i>".$doljnost."</i></small></td>
						<td align=left valign=middle style='border-bottom:solid 1px #777777;'>".$phones_table."</td>
						<td align=left valign=middle style='border-bottom:solid 1px #777777;'>".$memo."</td>
						<td align=left valign=middle style='border-bottom:solid 1px #777777;'>".$del."</td></tr>";

    		}
    		$out.="</table>";
    		$out.="<br>Всего: ".$num_users;
    	}
	else $out.="Список Ваших контактов пуст. Вы можете добавить пользователей в список контактов из <a href='/".$path_contacts."/' style='font-weight:bold;'>телефонной книги</a>";
		

	
	
	return($out);
}

function auth_system_messages($user_to, $system_message)
{
	global $sql_pref, $conn_id, $user_id;
    $type_to="to_user";
    $sql_query="INSERT INTO ".$sql_pref."_messages (user_id, content, message_type) VALUES ('0', '".$system_message."', '".$type_to."')";
    $sql_res=mysql_query($sql_query, $conn_id);    
    $message_id=mysql_insert_id();
    //echo "!!!".$sql_query;
  
               
                if($message_id>0)
                {
                   
                        $letter_name="www.ensor.ru: Вам поступило системное сообщение";
                        $letter_content_send="Вам поступило системное сообщение<br>";                            
                        $letter_content_send.="Чтобы просмотреть сообщение, перейдите по ссылке: http://www.ensor.ru/.<br>";
                        $letter_content_send.="Если указанная выше ссылка не открывается, скопируйте ее в буфер обмена, вставьте в адресную строку браузера и нажмите ввод.\n\n";
                        $letter_content_send.="Вы получили это письмо, потому что зарегистрированы на сайте www.ensor.ru.<br><br>";
                        $letter_content_send.="--<br>С уважением,<br>Служба поддержки www.ensor.ru.<br>";
                        $letter_content_send.="--------------------------------------------------------------";
                        send_mail_to_user($user_to, $letter_name, $letter_content_send);  
                        
                     }                  
    
    $sql_query="INSERT INTO ".$sql_pref."_messages_to (user_id, message_id) VALUES ('".$user_to."', '".$message_id."')";
    			$sql_res=mysql_query($sql_query, $conn_id);
}

function auth_group_messages($user_to, $system_message, $path)
{
	global $sql_pref, $conn_id, $user_id;
    $type_to="to_user";
    $sql_query="INSERT INTO ".$sql_pref."_messages (user_id, content, message_type) VALUES ('0', '".$system_message."', '".$type_to."')";
    $sql_res=mysql_query($sql_query, $conn_id);    
    $message_id=mysql_insert_id();
    //echo "!!!".$sql_query;
  
               
                if($message_id>0)
                {
                   
                        $letter_name="www.ensor.ru: Вам поступило сообщение группы.";                           
                        $letter_content_send.=$system_message."<br>";
                        $letter_content_send.="Если указанная выше ссылка не открывается, скопируйте ее в буфер обмена, вставьте в адресную строку браузера и нажмите ввод.\n\n";
                        $letter_content_send.="Вы получили это письмо, потому что зарегистрированы на сайте www.ensor.ru.<br><br>";
                        $letter_content_send.="--<br>С уважением,<br>Служба поддержки www.ensor.ru.<br>";
                        $letter_content_send.="--------------------------------------------------------------";
                        send_mail_to_user($user_to, $letter_name, $letter_content_send);  
                        //echo $letter_content_send;
                     }                  
    
    $sql_query="INSERT INTO ".$sql_pref."_messages_to (user_id, message_id) VALUES ('".$user_to."', '".$message_id."')";
    			$sql_res=mysql_query($sql_query, $conn_id);
}

function auth_user_phones_add()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $path_www, $page_header1;
	$out="";
    $page_header1="Добавление контакта";
	$page_title="Личный кабинет";

    if (isset ($_REQUEST['contact_id'])) $contact_id=$_REQUEST['contact_id']; 
	else {$contact_id=""; $out_error="Ошибка добавления пользователя";}
	
	if ($contact_id==$user_id) $out_error="Вы не можете добавлять себя в список контактов";
	
	if (!isset($out_error))
		{
		$sql_query="SELECT surname, name, name2 FROM ".$sql_pref."_users WHERE id='".$contact_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		list($surname, $name, $name2)=mysql_fetch_row($sql_res);
		
		$sql_query="SELECT surname, name, name2 FROM ".$sql_pref."_users WHERE id='".$user_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		list($addsurname, $addname, $addname2)=mysql_fetch_row($sql_res);
		$message_text="Пользователь $addsurname $addname $addname2 добавил Вас в список личных контактов";
		
		$sql_query="SELECT id FROM ".$sql_pref."_user_phones WHERE user_id='".$user_id."' AND contact_id='".$contact_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) $out_error="Пользователь $surname $name $name2 уже внесен в список личных контактов";
		}
	
		if (!isset($out_error))
		{
		$sql_query="INSERT INTO ".$sql_pref."_user_phones (user_id, contact_id) VALUES ('".$user_id."', '".$contact_id."')";
		if (mysql_query($sql_query, $conn_id)) 
		{$out.="Пользователь $surname $name $name2 добавлен в <a href='/auth/user_phones/' style='font-weight:bold;'>список личных контактов</a>";
		auth_system_messages($contact_id, $message_text);
		}
		else $out_error="Ошибка добавления пользователя";
		}
		if (isset($out_error)) $out.=$out_error;
		$out.="<br><br><a href='javascript:history.back(1)'>««&nbsp;вернуться</a>";
		return($out);
}












function auth_proposals_list()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $path_www, $page_header1;
	$out="";
    
    $page_header1="Ваши предложения";

	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	$perpage=20; $first=$perpage*($page-1);
	$sql_query="SELECT id FROM ".$sql_pref."_proposals WHERE user_id='".$user_id."'";
	$pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums($page, $perpage, $sql_query)."</div>";
	

	$sql_query="SELECT id, enable, dt, descr, top_time FROM ".$sql_pref."_proposals WHERE user_id='".$user_id."' ORDER BY dt DESC, id DESC LIMIT ".$first.",".$perpage;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<div style='padding: 0 0 0 0;'>";
		while(list($id, $enable, $dt, $descr, $top_time)=mysql_fetch_row($sql_res))
		{
			$descr=stripslashes($descr); $descr=str_replace("\n", "<br>", $descr);
            $descr_show=$descr;
			if ($enable=="Yes") $enable_show="<span style='color:green;'>Предложение отображается</span>"; else $enable_show="<span style='color:red;'>Предложение отключено</span>";
			$dt_show=date("d.m.Y", strtotime($dt));
			

			$out.="<div style='padding: 10 0;'>";
			$out.="
					<div style='padding: 1 0 1 0;font-size:12px;'><b>Дата публикации:</b> ".$dt_show."&nbsp; <b>Дата поднятия предложения:</b> ".$top_time."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><b>Статус:</b> ".$enable_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'>".$descr_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><a href='/auth/proposals_up/?id=".$id."'><img src='/admin/img/up.gif' width=11 height=13 alt='Сортировка: В топ' border=0>Поднять предложение вверх</a>&nbsp;&nbsp; <a href='/auth/proposals_edit/?id=".$id."'>Редактировать</a> &nbsp; <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/auth/proposals_del/?id=".$id."'\">Удалить</a></div>
			";
			$out.="</div>";
		}
		$out.="</div>";
	}
	$out.="<div style='padding: 15 0 5 0;'><a href='/auth/proposals_add/'>Добавить предложение</a></div>"; 
	$out.=$pages_show; 
	
	return ($out);
}





function auth_proposals_add()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $page_header1;
	global $rub_url, $auth;
	$out="";
    
    $page_header1="Добавить предложение";
    
	if (isset($_REQUEST['submit']))
	{
		if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";		
		if (isset($_REQUEST['dt_date']) AND !empty($_REQUEST['dt_date'])) $dt=$_REQUEST['dt_date']; else $dt=date("Y-m-d");
		if (isset($_REQUEST['descr']) AND !empty($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
		if (isset($_REQUEST['content']) AND !empty($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $content="";
		if (isset($_REQUEST['tags']) AND !empty($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
		if (isset($_REQUEST['sfera']) AND !empty($_REQUEST['sfera'])) $sfera_ids=implode(";",$_REQUEST['sfera']); else $sfera_ids="";
		if (isset($_REQUEST['direction']) AND !empty($_REQUEST['direction'])) $direction_ids=implode(";",$_REQUEST['direction']); else $direction_ids="";
		
    	$sql_query="SELECT company_id FROM ".$sql_pref."_users WHERE id='".$user_id."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0) list($company_id)=mysql_fetch_row($sql_res);
		else $company_id=0;
        
		if (!empty($descr))
		{
			$sql_query="INSERT INTO ".$sql_pref."_proposals (user_id, company_id, enable, dt, descr, content, tags, sfera_ids, direction_ids) VALUES ('".$user_id."', '".$company_id."', '".$enable."', '".$dt."', '".$descr."', '".$content."', '".$tags."', '".$sfera_ids."', '".$direction_ids."')";
			$sql_res=mysql_query($sql_query, $conn_id);
		
			header("location:/auth/proposals_list/"); 
			exit();
		}
		{
			$error_info="<div style='padding: 10 0 10 0;color:#ff0000;font-weight:bold;'>Ошибка! Вы не заполнили одно из обязательных полей: Краткое описание.</div>";
		}
	}

	$xc2_inc=file_get_contents($path."inc/xc2.inc");
	$dt_date=date("Y-m-d");
	$enable_show="<input type=checkbox name=enable value='Yes' checked>";
	$main_show="<input type=checkbox name=main value='Yes' checked>";
    
	$sfera_show="";
    $sql_query="SELECT id, name FROM ".$sql_pref."_sd_sfery ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	while (list($s_id, $s_name)=mysql_fetch_row($sql_res))
	{
		$s_name=stripslashes($s_name);
		$sfera_show.='<div><input type="checkbox" name="sfera[]" value="'.$s_id.'"> - '.$s_name.'</div>';
	}
    
	$direction_show="";
    $sql_query="SELECT id, name FROM ".$sql_pref."_sd_directions ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	while (list($d_id, $d_name)=mysql_fetch_row($sql_res))
	{
		$d_name=stripslashes($d_name);
		$direction_show.='<div><input type="checkbox" name="direction[]" value="'.$d_id.'"> - '.$d_name.'</div>';
	}
    
    
    $content_script="    
        <script type='text/javascript'>
            new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
        </script>";
    
    
	$out.=$xc2_inc."
	".@$error_info."
	<form action='' method=post name=news_add enctype='multipart/form-data'>
	<form action='' method=post name=news_add enctype='multipart/form-data'>
		<div style='padding: 10 0 10 0;'>
			<div>Краткое описание:</div>
			<div><textarea name=descr id=descr rows=4 style='width:550px;font-size:14px;'>".@$descr."</textarea></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Полный текст:</div>
			<div><textarea name=content id=content rows=12 style='width:550px;font-size:14px;'>".@$content."</textarea></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Дата:</div>
			<div><div id=holder></div><input readonly type=Text maxlength=70 name=dt_date id=dt_date value='".@$dt_date."' style='width:80px;font-size:14px;cursor:pointer;' onclick='showCalendar(\"\",document.getElementById(\"dt_date\"),null,\"".$dt_date."\",\"holder\",0,25,1)'></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Теги: (через запятую)</div>
			<div><input type=Text maxlength=70 name=tags id=tags value='".@$tags."' style='width:550px;font-size:14px;'></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Настройки:</div>
			<div>".$enable_show." - отображение предложения</div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Сферы деятельности:</div>
			<div>".$sfera_show."</div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Направления деятельности:</div>
			<div>".$direction_show."</div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div><input type=Submit name=submit value=Сохранить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
		</div>
	</form>
    ".$content_script."
	";
	return ($out);
}





function auth_proposals_edit()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $page_header1;
	global $rub_url, $auth;
	$out="";
    $page_header1="Редактировать предложение";
	if (isset($_REQUEST['submit']) && isset($_REQUEST['proposals_id']))
	{
		if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";		
		if (isset($_REQUEST['dt_date']) AND !empty($_REQUEST['dt_date'])) $dt=$_REQUEST['dt_date']; else $dt=date("Y-m-d");
		if (isset($_REQUEST['descr']) AND !empty($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
		if (isset($_REQUEST['content']) AND !empty($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $content="";
		if (isset($_REQUEST['tags']) AND !empty($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
		if (isset($_REQUEST['sfera']) AND !empty($_REQUEST['sfera'])) $sfera_ids=implode(";",$_REQUEST['sfera']); else $sfera_ids="";
		if (isset($_REQUEST['direction']) AND !empty($_REQUEST['direction'])) $direction_ids=implode(";",$_REQUEST['direction']); else $direction_ids="";
		
		$sql_query="UPDATE ".$sql_pref."_proposals SET enable='".$enable."', dt='".$dt."', descr='".$descr."', content='".$content."', tags='".$tags."', sfera_ids='".$sfera_ids."', direction_ids='".$direction_ids."' WHERE id='".$_REQUEST['proposals_id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	
		header("location:/auth/proposals_list/"); 
		exit();
	}

	
	$sql_query="SELECT id, enable, dt, descr, content, tags, sfera_ids, direction_ids FROM ".$sql_pref."_proposals WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $enable, $dt, $descr, $content, $tags, $sfera_ids, $direction_ids)=mysql_fetch_row($sql_res);
		$descr=stripslashes($descr);$content=stripslashes($content);$tags=stripslashes($tags);
		$dt_date=$dt;
		if ($enable=="Yes") $ch="checked"; else $ch=""; 
		$enable_show="<input type=checkbox name=enable value='Yes' ".$ch.">";
		$xc2_inc=file_get_contents($path."inc/xc2.inc");
        
        $sfera_array=explode(";", $sfera_ids);
    	$sfera_show="";
        $sql_query="SELECT id, name FROM ".$sql_pref."_sd_sfery ORDER BY code";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if(mysql_num_rows($sql_res)>0)
    	{
    		while (list($s_id, $s_name)=mysql_fetch_row($sql_res))
    		{
    			$s_name=stripslashes($s_name);
                if (in_array($s_id, $sfera_array)) $checked="checked"; else $checked="";
    			$sfera_show.='<div><input type="checkbox" name="sfera[]" value="'.$s_id.'" '.$checked.'> - '.$s_name.'</div>';
    		}
    	}
		
    	$direction_array=explode(";", $direction_ids);
        $direction_show="";
        $sql_query="SELECT id, name FROM ".$sql_pref."_sd_directions ORDER BY code";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	while (list($d_id, $d_name)=mysql_fetch_row($sql_res))
    	{
    		$d_name=stripslashes($d_name);
            if (in_array($d_id, $direction_array)) $checked="checked"; else $checked="";
    		$direction_show.='<div><input type="checkbox" name="direction[]" value="'.$d_id.'" '.$checked.'> - '.$d_name.'</div>';
    	}
        
        
		$del_proposals="<div style='padding: 20 0 30 0;'><div><a href=\"javascript:if(confirm('Вы уверены?'))window.location='/auth/proposals_del/?id=".$id."'\">Удалить предложение</a></div></div>";
		
        $content_script="    
            <script type='text/javascript'>
                new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
            </script>";
	
		$out.=$xc2_inc."
		<form action='' method=post name=proposals_add enctype='multipart/form-data'>
			<div style='padding: 10 0 10 0;'>
				<div>Краткое описание:</div>
				<div><textarea name=descr id=descr rows=4 style='width:550px;font-size:14px;'>".@$descr."</textarea></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Полный текст:</div>
				<div><textarea name=content id=content rows=12 style='width:550px;font-size:14px;'>".@$content."</textarea></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Теги:</div>
				<div><input type=Text maxlength=70 name=tags id=tags value='".@$tags."' style='width:550px;font-size:14px;'></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Дата:</div>
				<div><div id=holder></div><input readonly type=Text maxlength=70 name=dt_date id=dt_date value='".@$dt_date."' style='width:80px;font-size:14px;cursor:pointer;' onclick='showCalendar(\"\",document.getElementById(\"dt_date\"),null,\"".$dt_date."\",\"holder\",0,25,1)'></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Настройки:</div>
				<div>".$enable_show." - отображение предложения</div>
			</div>
    		<div style='padding: 10 0 10 0;'>
    			<div>Сферы деятельности:</div>
    			<div>".$sfera_show."</div>
    		</div>
    		<div style='padding: 10 0 10 0;'>
    			<div>Направления деятельности:</div>
    			<div>".$direction_show."</div>
    		</div>
			<div style='padding: 10 0 10 0;'>
				<input type=hidden name=proposals_id value='".$id."'>
				<div><input type=Submit name=submit value=Сохранить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
			</div>
		</form>
		".$del_proposals."
        ".$content_script."
		";
	}
	else return ("Ошибка!");
	return ($out);
}





function auth_proposals_del()
{
	global $sql_pref, $conn_id, $user_id, $path, $path_www;

	$id=$_REQUEST['id'];
	
	$sql_query="SELECT id FROM ".$sql_pref."_proposals WHERE id='".$id."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $sql_query="DELETE FROM ".$sql_pref."_proposals WHERE id='".$id."'";
        $sql_res_1=mysql_query($sql_query, $conn_id);
    }
	
	header("location:/auth/proposals_list/"); exit();
}





function auth_demand_list()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $path_www, $page_header1;
	$out="";
    
    $page_header1="Ваш поиск";

	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	$perpage=20; $first=$perpage*($page-1);
	$sql_query="SELECT id FROM ".$sql_pref."_demand WHERE user_id='".$user_id."'";
	$pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums($page, $perpage, $sql_query)."</div>";
	

	$sql_query="SELECT id, enable, dt, descr, top_time FROM ".$sql_pref."_demand WHERE user_id='".$user_id."' ORDER BY dt DESC, id DESC LIMIT ".$first.",".$perpage;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<div style='padding: 0 0 0 0;'>";
		while(list($id, $enable, $dt, $descr, $top_time)=mysql_fetch_row($sql_res))
		{
			$descr=stripslashes($descr); $descr=str_replace("\n", "<br>", $descr);
            $descr_show=$descr;
			if ($enable=="Yes") $enable_show="<span style='color:green;'>Поиск отображается</span>"; else $enable_show="<span style='color:red;'>Предложение отключено</span>";
			$dt_show=date("d.m.Y", strtotime($dt));
			

			$out.="<div style='padding: 10 0;'>";
			$out.="
					<div style='padding: 1 0 1 0;font-size:12px;'><b>Дата публикации:</b> ".$dt_show."&nbsp; <b>Дата поднятия поиска:</b> ".$top_time."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><b>Статус:</b> ".$enable_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'>".$descr_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><a href='/auth/demand_up/?id=".$id."'><img src='/admin/img/up.gif' width=11 height=13 alt='Сортировка: В топ' border=0>Поднять поиск вверх</a>&nbsp;&nbsp; <a href='/auth/demand_edit/?id=".$id."'>Редактировать</a> &nbsp; <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/auth/demand_del/?id=".$id."'\">Удалить</a></div>
			";
			$out.="</div>";
		}
		$out.="</div>";
	}
	$out.="<div style='padding: 15 0 5 0;'><a href='/auth/demand_add/'>Добавить поиск исполнителей</a></div>"; 
	$out.=$pages_show; 
	
	return ($out);
}





function auth_demand_add()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $page_header1;
	global $rub_url, $auth;
	$out="";
    
    $page_header1="Добавить поиск исполнителей";
    
	if (isset($_REQUEST['submit']))
	{
		if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";		
		if (isset($_REQUEST['dt_date']) AND !empty($_REQUEST['dt_date'])) $dt=$_REQUEST['dt_date']; else $dt=date("Y-m-d");
		if (isset($_REQUEST['descr']) AND !empty($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
		if (isset($_REQUEST['content']) AND !empty($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $content="";
		if (isset($_REQUEST['tags']) AND !empty($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
		if (isset($_REQUEST['sfera']) AND !empty($_REQUEST['sfera'])) $sfera_ids=implode(";",$_REQUEST['sfera']); else $sfera_ids="";
		if (isset($_REQUEST['direction']) AND !empty($_REQUEST['direction'])) $direction_ids=implode(";",$_REQUEST['direction']); else $direction_ids="";
		
    	$sql_query="SELECT company_id FROM ".$sql_pref."_users WHERE id='".$user_id."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0) list($company_id)=mysql_fetch_row($sql_res);
		else $company_id=0;
        
		if (!empty($descr))
		{
			$sql_query="INSERT INTO ".$sql_pref."_demand (user_id, company_id, enable, dt, descr, content, tags, sfera_ids, direction_ids) VALUES ('".$user_id."', '".$company_id."', '".$enable."', '".$dt."', '".$descr."', '".$content."', '".$tags."', '".$sfera_ids."', '".$direction_ids."')";
			$sql_res=mysql_query($sql_query, $conn_id);
		
			header("location:/auth/demand_list/"); 
			exit();
		}
		{
			$error_info="<div style='padding: 10 0 10 0;color:#ff0000;font-weight:bold;'>Ошибка! Вы не заполнили одно из обязательных полей: Краткое описание.</div>";
		}
	}

	$xc2_inc=file_get_contents($path."inc/xc2.inc");
	$dt_date=date("Y-m-d");
	$enable_show="<input type=checkbox name=enable value='Yes' checked>";
	$main_show="<input type=checkbox name=main value='Yes' checked>";
    
	$sfera_show="";
    $sql_query="SELECT id, name FROM ".$sql_pref."_sd_sfery ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	while (list($s_id, $s_name)=mysql_fetch_row($sql_res))
	{
		$s_name=stripslashes($s_name);
		$sfera_show.='<div><input type="checkbox" name="sfera[]" value="'.$s_id.'"> - '.$s_name.'</div>';
	}
    
	$direction_show="";
    $sql_query="SELECT id, name FROM ".$sql_pref."_sd_directions ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	while (list($d_id, $d_name)=mysql_fetch_row($sql_res))
	{
		$d_name=stripslashes($d_name);
		$direction_show.='<div><input type="checkbox" name="direction[]" value="'.$d_id.'"> - '.$d_name.'</div>';
	}
    
    
    $content_script="    
        <script type='text/javascript'>
            new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
        </script>";
    
    
	$out.=$xc2_inc."
	".@$error_info."
	<form action='' method=post name=news_add enctype='multipart/form-data'>
	<form action='' method=post name=news_add enctype='multipart/form-data'>
		<div style='padding: 10 0 10 0;'>
			<div>Краткое описание:</div>
			<div><textarea name=descr id=descr rows=4 style='width:550px;font-size:14px;'>".@$descr."</textarea></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Полный текст:</div>
			<div><textarea name=content id=content rows=12 style='width:550px;font-size:14px;'>".@$content."</textarea></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Дата:</div>
			<div><div id=holder></div><input readonly type=Text maxlength=70 name=dt_date id=dt_date value='".@$dt_date."' style='width:80px;font-size:14px;cursor:pointer;' onclick='showCalendar(\"\",document.getElementById(\"dt_date\"),null,\"".$dt_date."\",\"holder\",0,25,1)'></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Теги: (через запятую)</div>
			<div><input type=Text maxlength=70 name=tags id=tags value='".@$tags."' style='width:550px;font-size:14px;'></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Настройки:</div>
			<div>".$enable_show." - отображение предложения</div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Сферы деятельности:</div>
			<div>".$sfera_show."</div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Направления деятельности:</div>
			<div>".$direction_show."</div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div><input type=Submit name=submit value=Сохранить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
		</div>
	</form>
    ".$content_script."
	";
	return ($out);
}





function auth_demand_edit()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $page_header1;
	global $rub_url, $auth;
	$out="";
    $page_header1="Редактировать поиск";
	if (isset($_REQUEST['submit']) && isset($_REQUEST['demand_id']))
	{
		if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";		
		if (isset($_REQUEST['dt_date']) AND !empty($_REQUEST['dt_date'])) $dt=$_REQUEST['dt_date']; else $dt=date("Y-m-d");
		if (isset($_REQUEST['descr']) AND !empty($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
		if (isset($_REQUEST['content']) AND !empty($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $content="";
		if (isset($_REQUEST['tags']) AND !empty($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
		if (isset($_REQUEST['sfera']) AND !empty($_REQUEST['sfera'])) $sfera_ids=implode(";",$_REQUEST['sfera']); else $sfera_ids="";
		if (isset($_REQUEST['direction']) AND !empty($_REQUEST['direction'])) $direction_ids=implode(";",$_REQUEST['direction']); else $direction_ids="";
		
		$sql_query="UPDATE ".$sql_pref."_demand SET enable='".$enable."', dt='".$dt."', descr='".$descr."', content='".$content."', tags='".$tags."', sfera_ids='".$sfera_ids."', direction_ids='".$direction_ids."' WHERE id='".$_REQUEST['demand_id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	
		header("location:/auth/demand_list/"); 
		exit();
	}

	
	$sql_query="SELECT id, enable, dt, descr, content, tags, sfera_ids, direction_ids FROM ".$sql_pref."_demand WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $enable, $dt, $descr, $content, $tags, $sfera_ids, $direction_ids)=mysql_fetch_row($sql_res);
		$descr=stripslashes($descr);$content=stripslashes($content);$tags=stripslashes($tags);
		$dt_date=$dt;
		if ($enable=="Yes") $ch="checked"; else $ch=""; 
		$enable_show="<input type=checkbox name=enable value='Yes' ".$ch.">";
		$xc2_inc=file_get_contents($path."inc/xc2.inc");
        
        $sfera_array=explode(";", $sfera_ids);
    	$sfera_show="";
        $sql_query="SELECT id, name FROM ".$sql_pref."_sd_sfery ORDER BY code";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if(mysql_num_rows($sql_res)>0)
    	{
    		while (list($s_id, $s_name)=mysql_fetch_row($sql_res))
    		{
    			$s_name=stripslashes($s_name);
                if (in_array($s_id, $sfera_array)) $checked="checked"; else $checked="";
    			$sfera_show.='<div><input type="checkbox" name="sfera[]" value="'.$s_id.'" '.$checked.'> - '.$s_name.'</div>';
    		}
    	}
		
    	$direction_array=explode(";", $direction_ids);
        $direction_show="";
        $sql_query="SELECT id, name FROM ".$sql_pref."_sd_directions ORDER BY code";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	while (list($d_id, $d_name)=mysql_fetch_row($sql_res))
    	{
    		$d_name=stripslashes($d_name);
            if (in_array($d_id, $direction_array)) $checked="checked"; else $checked="";
    		$direction_show.='<div><input type="checkbox" name="direction[]" value="'.$d_id.'" '.$checked.'> - '.$d_name.'</div>';
    	}
        
        
		$del_demand="<div style='padding: 20 0 30 0;'><div><a href=\"javascript:if(confirm('Вы уверены?'))window.location='/auth/demand_del/?id=".$id."'\">Удалить предложение</a></div></div>";
		
        $content_script="    
            <script type='text/javascript'>
                new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
            </script>";
	
		$out.=$xc2_inc."
		<form action='' method=post name=demand_add enctype='multipart/form-data'>
			<div style='padding: 10 0 10 0;'>
				<div>Краткое описание:</div>
				<div><textarea name=descr id=descr rows=4 style='width:550px;font-size:14px;'>".@$descr."</textarea></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Полный текст:</div>
				<div><textarea name=content id=content rows=12 style='width:550px;font-size:14px;'>".@$content."</textarea></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Теги:</div>
				<div><input type=Text maxlength=70 name=tags id=tags value='".@$tags."' style='width:550px;font-size:14px;'></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Дата:</div>
				<div><div id=holder></div><input readonly type=Text maxlength=70 name=dt_date id=dt_date value='".@$dt_date."' style='width:80px;font-size:14px;cursor:pointer;' onclick='showCalendar(\"\",document.getElementById(\"dt_date\"),null,\"".$dt_date."\",\"holder\",0,25,1)'></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Настройки:</div>
				<div>".$enable_show." - отображение поиска</div>
			</div>
    		<div style='padding: 10 0 10 0;'>
    			<div>Сферы деятельности:</div>
    			<div>".$sfera_show."</div>
    		</div>
    		<div style='padding: 10 0 10 0;'>
    			<div>Направления деятельности:</div>
    			<div>".$direction_show."</div>
    		</div>
			<div style='padding: 10 0 10 0;'>
				<input type=hidden name=demand_id value='".$id."'>
				<div><input type=Submit name=submit value=Сохранить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
			</div>
		</form>
		".$del_demand."
        ".$content_script."
		";
	}
	else return ("Ошибка!");
	return ($out);
}





function auth_demand_del()
{
	global $sql_pref, $conn_id, $user_id, $path, $path_www;

	$id=$_REQUEST['id'];
	
	$sql_query="SELECT id FROM ".$sql_pref."_demand WHERE id='".$id."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $sql_query="DELETE FROM ".$sql_pref."_demand WHERE id='".$id."'";
        $sql_res_1=mysql_query($sql_query, $conn_id);
    }
	
	header("location:/auth/demand_list/"); exit();
}








function auth_questions_list()
{
	global $user_rate_sec, $page_header1, $sql_pref, $conn_id, $path, $path_questions, $questions_answers_perpage, $user_id, $user_status;
	$page_header1="Ваши вопросы";
    
    $out="<table cellpadding=4 cellspacing=0 border=0 width=100%>
						<tr>
                            <td width=5%> </td>
                            <td width=15%> </td>
                            <td width=50%> </td>
                            <td width=30%> </td>
						</tr>";
	
	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	$perpage=$questions_answers_perpage; $first=$perpage*($page-1);
	$sql_query="SELECT id FROM ".$sql_pref."_questions WHERE user_id=".$user_id;
	$pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums($page, $perpage, $sql_query)."</div>";

	$sql_query="SELECT id, dt, question, user_id, question_type, send FROM ".$sql_pref."_questions WHERE user_id=".$user_id." ORDER BY dt DESC, id DESC LIMIT ".$first.",".$perpage;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $dt, $question, $p_user_id, $question_type, $send)=mysql_fetch_row($sql_res))
		{
			$question=stripslashes($question);$question=str_replace("\n", "<br>", $question);
			
			$dt_show="<div style='padding: 3 0;'><span style='padding: 2 4;background-color:#eee;'>".date("d.m.Y H:i:s", strtotime($dt))."</span></div>";
            $question_show="<div style='padding: 3 0;'>".$question."</div>";
            if ($question_type==2) $results='результаты опроса'; else $results='обсуждение';
            $more_show="<div style='padding: 3 0;'><a href='/".$path_questions."/".$id.".html'>Посмотреть ".$results."...</a></div>";
           
            $sql_query="SELECT name, surname FROM ".$sql_pref."_users WHERE id='".$p_user_id."'";
        	$sql_res_1=mysql_query($sql_query, $conn_id);
        	list($user_name, $user_surname)=mysql_fetch_row($sql_res_1);
          
            $p_user_name=$user_name." ".$user_surname;
            $p_user_show="<div style='padding: 3 0;font-size:18px;'>".$p_user_name."</div>";

            if ($p_user_id==@$user_id || $user_status=="admin") 
            {
                //if($send=='No'&&($user_rate_sec>38||$user_status=="admin")) $send_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_questions."/send".$id.".html?action=question_send&question_id=".$id."'\"  style='font-size:9px;color:#999999;'>Разослать приглашение к обсуждению</a><br>"; else $send_but="";               
                if($send=='No'&&$user_status=="admin") $send_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_questions."/send".$id.".html?action=question_send&question_id=".$id."'\"  style='font-size:9px;color:#999999;'>Разослать приглашение к обсуждению</a><br>"; else $send_but="";               
                $del_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_questions."/del".$id.".html?action=question_del&question_id=".$id."'\"  style='font-size:9px;color:#999999;'>Удалить</a>";
                $change_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_questions."/change".$id.".html?action=question_change_form&question_id=".$id."'\"  style='font-size:9px;color:#999999;'>Править</a>";
            }
            else
            {
                $send_but="";
                $del_but="";
                $change_but="";
            }
            if ($question_type==1) $img_view="<img src='/img/question.png' alt='Вопрос'>"; else $img_view="<img src='/img/query.png' border=0 width=20px height=20px alt='Опрос'>";
			
			$out.="     <tr><td></br></td><td></br></td><td></td><td></td></tr>	
                        <tr>
                            <td valign=middle>".$img_view."</td>
                            <td valign=middle>".$dt_show."</td>
                            <td valign=middle>".$question_show."</td>
                            <td valign=middle>".$more_show.$send_but.$del_but.$change_but."</td>
						</tr>
                        <tr><td></br></td><td></br></td><td></td><td></td></tr>";

		}
		
	}
	$out.= "</table>";
    $add_link="<div style='padding:5 0 20 0;'><a href='/".$path_questions."?action=add_question'>Добавить вопрос...</a></div>";
	$out.=$pages_show;
    $out.=$add_link;
	
	return ($out);
}










function auth_messages_list()
{
    global $sql_pref, $conn_id, $user_id, $user_admin, $path, $path_www, $page_header1, $path_contacts;
	$out="";
    
    $page_header1="Ваши сообщения";

	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
    
    $perpage=5; $first=$perpage*($page-1);
	$sql_query="
        SELECT Result_Tab.id, Result_Tab.user_id
FROM (SELECT * FROM (SELECT ".$sql_pref."_messages_to.id, ".$sql_pref."_messages.user_id  FROM ".$sql_pref."_messages_to LEFT JOIN ".$sql_pref."_messages ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages_to.user_id=".$user_id." 
UNION ALL
SELECT ".$sql_pref."_messages.id, ".$sql_pref."_messages_to.user_id FROM ".$sql_pref."_messages LEFT JOIN ".$sql_pref."_messages_to ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages.user_id=".$user_id.") As Res_Prom)
AS Result_Tab GROUP BY Result_Tab.user_id";
                 
    $pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums($page, $perpage, $sql_query)."</div>";  	

	//$sql_query="SELECT id, dt, message_type, message_title, content, group_id, sfera_id, napr_id, object_id FROM ".$sql_pref."_messages WHERE user_id='".$user_id."' ORDER BY dt DESC, id DESC LIMIT ".$first.",".$perpage;
	$sql_query="
        SELECT Result_Tab.id, Result_Tab.user_id, Result_Tab.content, Result_Tab.dt
            FROM (
                    SELECT  Res_Prom.id, Res_Prom.user_id, Res_Prom.content, Res_Prom.dt
                        FROM (
                                SELECT ".$sql_pref."_messages_to.id, ".$sql_pref."_messages.user_id, ".$sql_pref."_messages.content, ".$sql_pref."_messages.dt  FROM ".$sql_pref."_messages_to LEFT JOIN ".$sql_pref."_messages ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages_to.user_id=".$user_id." 
                                    UNION ALL
                                SELECT ".$sql_pref."_messages.id, ".$sql_pref."_messages_to.user_id, ".$sql_pref."_messages.content, ".$sql_pref."_messages.dt FROM ".$sql_pref."_messages LEFT JOIN ".$sql_pref."_messages_to ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages.user_id=".$user_id."
                             ) 
                    As Res_Prom ORDER BY Res_Prom.dt DESC
                 )
        AS Result_Tab 
        GROUP BY Result_Tab.user_id ORDER BY dt DESC LIMIT ".$first.",".$perpage;

		

    $sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
	    $out.="<div style='padding: 0 0 0 0;'>";
		while(list($id, $user_with_id, $content, $dt)=mysql_fetch_row($sql_res))
		{   
            //echo $content;
            $content=strip_tags($content);
            $replace_find = array("\n", "<br>", "<P>", "</P>","<",">");
			$replace_with = array(" ", " ", " ", " ","&lt;","&gt;");
            $content=stripslashes($content); $content=str_replace($replace_find, $replace_with, $content);
            $descr_show=substr($content,0,60)."...";
			$dt_show=date("d.m.Y",strtotime($dt));
			

			$out.="<div style='padding: 10 0;'>";
			if($user_with_id) 
			$out.="<div style='padding: 1 0 1 0;font-size:12px;'><b>Переписка с </b> <a href='/".$path_contacts."/".$user_with_id.".html'> ".get_user_name_by_id($user_with_id)." </a></div>";
			else 
			$out.="<div style='padding: 1 0 1 0;font-size:12px;'><b>Системное сообщение</b></div>";
    		$out.="<div style='padding: 1 0 1 0;font-size:12px;'><b>Дата последнего сообщения:</b> ".$dt_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'>".$descr_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><a href='/auth/messages_show/?user_with_id=".$user_with_id."'>Посмотреть переписку</a> &nbsp; </div>
			";
			$out.="</div>";
		}
		$out.="</div>";
        $sql_query="UPDATE ".$sql_pref."_messages INNER JOIN ".$sql_pref."_messages_to ON ".$sql_pref."_messages.id=".$sql_pref."_messages_to.message_id SET ".$sql_pref."_messages.shown='Yes' WHERE (".$sql_pref."_messages.shown='No' AND ".$sql_pref."_messages_to.user_id=".$user_id.")";
        //echo $sql_query;
        $sql_res=mysql_query($sql_query, $conn_id);
	}
    if(isset($user_id) AND $user_id!=0) $out.="<div style='padding: 15 0 5 0;'><a href='/auth/messages_add/'>Написать сообщение</a></div>"; 
	$out.=$pages_show; 
	
	return ($out);
}


function auth_messages_show()
{
    global $sql_pref, $conn_id, $user_id, $user_admin, $path, $path_www, $page_header1, $path_contacts;
	$out="";
    
    if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	if (isset($_REQUEST['user_with_id'])&& $_REQUEST['user_with_id']>=0) {$user_with_id=$_REQUEST['user_with_id']; $user_to_id=$_REQUEST['user_with_id'];}
	
    $perpage=5; $first=$perpage*($page-1);
	$sql_query="
        SELECT Result_Tab.id, Result_Tab.user_id, Result_Tab.content, Result_Tab.dt, ".$sql_pref."_users.name, ".$sql_pref."_users.surname 
FROM (SELECT * FROM (SELECT ".$sql_pref."_messages_to.id, ".$sql_pref."_messages.user_id, ".$sql_pref."_messages.content, ".$sql_pref."_messages.dt  FROM ".$sql_pref."_messages_to LEFT JOIN ".$sql_pref."_messages ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages_to.user_id=".$user_id." AND ".$sql_pref."_messages.user_id=".$user_with_id." 
UNION ALL
SELECT ".$sql_pref."_messages.id, ".$sql_pref."_messages_to.user_id, ".$sql_pref."_messages.content, ".$sql_pref."_messages.dt FROM ".$sql_pref."_messages LEFT JOIN ".$sql_pref."_messages_to ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages.user_id=".$user_id." AND ".$sql_pref."_messages_to.user_id=".$user_with_id.") As Res_Prom ORDER BY dt DESC)
AS Result_Tab LEFT JOIN ".$sql_pref."_users ON Result_Tab.user_id=".$sql_pref."_users.id ORDER BY dt DESC";
                 
    $args="&user_with_id=".$user_with_id;
    $pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums_with_args($page, $perpage, $sql_query,$args)."</div>";  	

	//$sql_query="SELECT id, dt, message_type, message_title, content, group_id, sfera_id, napr_id, object_id FROM ".$sql_pref."_messages WHERE user_id='".$user_id."' ORDER BY dt DESC, id DESC LIMIT ".$first.",".$perpage;
	$sql_query="
        SELECT Result_Tab.id, Result_Tab.user_id, Result_Tab.main_user, Result_Tab.content, Result_Tab.dt, ".$sql_pref."_users.name, ".$sql_pref."_users.surname 
FROM (SELECT * FROM (SELECT ".$sql_pref."_messages_to.id, ".$sql_pref."_messages.user_id, (".$sql_pref."_messages.user_id) As 'main_user', ".$sql_pref."_messages.content, ".$sql_pref."_messages.dt  FROM ".$sql_pref."_messages_to LEFT JOIN ".$sql_pref."_messages ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages_to.user_id=".$user_id." AND ".$sql_pref."_messages.user_id=".$user_with_id." 
UNION ALL
SELECT ".$sql_pref."_messages.id, ".$sql_pref."_messages_to.user_id, (".$sql_pref."_messages.user_id) As 'main_user' ,  ".$sql_pref."_messages.content, ".$sql_pref."_messages.dt FROM ".$sql_pref."_messages LEFT JOIN ".$sql_pref."_messages_to ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages.user_id=".$user_id." AND ".$sql_pref."_messages_to.user_id=".$user_with_id.") As Res_Prom ORDER BY dt DESC)
AS Result_Tab LEFT JOIN ".$sql_pref."_users ON Result_Tab.user_id=".$sql_pref."_users.id ORDER BY dt DESC LIMIT ".$first.",".$perpage;

		
    
    $sql_res=mysql_query($sql_query, $conn_id);
    //echo $sql_query;
	if (mysql_num_rows($sql_res)>0)
	{	       
	    $out.="<div style='padding: 0 0 0 0;'>";
		while(list($id, $user_with_id, $main_user, $content, $dt, $name, $surname)=mysql_fetch_row($sql_res))
		{
			$user_name_show=$name." ".$surname;
            $content=stripslashes($content); $content=str_replace("\n", "<br>", $content);
            $descr_show=$content;
			$dt_show=date("d.m.Y H:i:s",strtotime($dt));
	         		

            if($user_to_id==0) $main_user_show=""; elseif($main_user==$user_id) $main_user_show="Вы писали:"; else $main_user_show="<font color=red><b>Пользователь ".$user_name_show." писал:</b></font>";
			$out.="<div style='padding: 10 0;'>";
			$out.=" 
                    <div style='padding: 1 0 1 0;font-size:12px;'><b>Дата сообщения:</b> ".$dt_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><b>".$main_user_show."</b><br>".$descr_show."<hr></div>
			";
			$out.="</div>";
		}
		$out.="</div>";
	}
	if(isset($user_id) AND $user_id!=0 AND $user_to_id!=0) $out.="<div style='padding: 15 0 5 0;'><a href='/auth/messages_add/?user_id_to=".$user_to_id."'>Написать сообщение</a></div>"; 
	$out.=$pages_show; 
    
	if($user_to_id!=0) $page_header1="Переписка с <a href='/".$path_contacts."/".$user_to_id.".html'>".$user_name_show."</a>"; else $page_header1="Системные сообщения";
	return ($out);
}

function auth_messages_del()
{
    
    	$sql_query="
            SELECT Result_Tab.id, Result_Tab.user_id, Result_Tab.main_user, Result_Tab.content, Result_Tab.dt, ".$sql_pref."_users.name, ".$sql_pref."_users.surname 
            FROM (SELECT * FROM (SELECT ".$sql_pref."_messages_to.id, ".$sql_pref."_messages.user_id, (".$sql_pref."_messages.user_id) As 'main_user', ".$sql_pref."_messages.content, ".$sql_pref."_messages.dt  FROM ".$sql_pref."_messages_to LEFT JOIN ".$sql_pref."_messages ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages_to.user_id=".$user_id." AND ".$sql_pref."_messages.user_id=".$user_with_id." 
            UNION ALL
            SELECT ".$sql_pref."_messages.id, ".$sql_pref."_messages_to.user_id, (".$sql_pref."_messages.user_id) As 'main_user' ,  ".$sql_pref."_messages.content, ".$sql_pref."_messages.dt FROM ".$sql_pref."_messages LEFT JOIN ".$sql_pref."_messages_to ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages.user_id=".$user_id." AND ".$sql_pref."_messages_to.user_id=".$user_with_id.") As Res_Prom ORDER BY dt DESC)
            AS Result_Tab LEFT JOIN ".$sql_pref."_users ON Result_Tab.user_id=".$sql_pref."_users.id ORDER BY dt DESC LIMIT ".$first.",".$perpage;

    //$sql_query="DELETE FROM ".$sql_pref."_groups WHERE id='".$group_id."'";
    //    $sql_res_1=mysql_query($sql_query, $conn_id);
    //    $flag=1; 
    //
	//if($user_to_id!=0) $page_header1="Переписка с ".$user_name_show; else $page_header1="Системные сообщения";
	//return ($out);
}


function auth_messages_add()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $page_header1;
	global $rub_url, $auth;
	$out="";
    
    if (isset ($_REQUEST['user_id_to']) && $_REQUEST['user_id_to']!=$user_id) $user_id_to=$_REQUEST['user_id_to']; else $user_id_to="";
	//echo "!!!".$_REQUEST['user_id_to'];
    
    if(isset($user_id) AND $user_id!=0)
    {
        $page_header1="Написать сообщение";
            
    	if (isset($_REQUEST['submit']))
    	{
    	    //echo "тест2";
        	if (isset($_REQUEST['selected_button']) AND !empty($_REQUEST['selected_button'])) $type_to=$_REQUEST['selected_button'];
    		if (isset($_REQUEST['to_id']) AND !empty($_REQUEST['to_id'])) $id=$_REQUEST['to_id'];
            if (isset($_REQUEST['content']) AND !empty($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $content="";
    		//if (isset($_REQUEST['sfera']) AND !empty($_REQUEST['sfera'])) $sfera_ids=implode(";",$_REQUEST['sfera']); else $sfera_ids="";
    		//if (isset($_REQUEST['direction']) AND !empty($_REQUEST['direction'])) $direction_ids=implode(";",$_REQUEST['direction']); else $direction_ids="";
    		//echo "/".$type_to."/".$id."/".$content;
            
        	if (!empty($content) && !empty($id))
    		{            
    			$sql_query="INSERT INTO ".$sql_pref."_messages (user_id, content, message_type) VALUES ('".$user_id."', '".$content."', '".$type_to."')";
    			$sql_res=mysql_query($sql_query, $conn_id);
                $message_id=mysql_insert_id();
  
               
                //echo $sql_query;
                if($message_id>0)
                {
                    $sql_query="SELECT id, surname, name, name2, email FROM ".$sql_pref."_users WHERE id=".$user_id;
                    $sql_res=mysql_query($sql_query, $conn_id);
                    //echo $sql_query;
                    if (mysql_num_rows($sql_res)>0)
                    {
                        list($id_from, $surname_from, $name_from, $name2_from, $email_from)=mysql_fetch_row($sql_res);
                        
                        $name_from=$surname_from." ".$name_from." ".$name2_from;
                        
                        $letter_name="www.ensor.ru: Вам поступило личное сообщение от пользователя ".$name_from.".";
                        $letter_content_send="Вам поступило сообщение от пользователя ".$name_from.".<br>";                            
                        $letter_content_send.="Чтобы просмотреть сообщение, перейдите по ссылке: http://www.ensor.ru/.<br>";
                        $letter_content_send.="Если указанная выше ссылка не открывается, скопируйте ее в буфер обмена, вставьте в адресную строку браузера и нажмите ввод.<br><br>";
                        $letter_content_send.="Вы получили это письмо, потому что зарегистрированы на сайте www.ensor.ru.<br><br>";
                        $letter_content_send.="--<br>С уважением,<br>Служба поддержки www.ensor.ru.<br>";
                        $letter_content_send.="--------------------------------------------------------------";
                        send_mail_to_user($id, $letter_name, $letter_content_send);  
                        
                     }                  
                }  
    
    			$sql_query="INSERT INTO ".$sql_pref."_messages_to (user_id, message_id) VALUES ('".$id."', '".$message_id."')";
    			$sql_res=mysql_query($sql_query, $conn_id);

                $sql_query2="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'send_message','".$message_id."')";
                $sql_res2=mysql_query($sql_query2, $conn_id);    
    		    
    			header("location:/auth/messages_list/"); 
    			//echo $sql_query;  
                exit();
                
    		}
            else
    		{
    			$error_info="<div style='padding: 10 0 10 0;color:#ff0000;font-weight:bold;'>Ошибка! Вы не заполнили одно из обязательных полей.</div>";
    		}
    	}
    
    	$xc2_inc=file_get_contents($path."inc/xc2.inc");
    	$dt_date=date("Y-m-d");
    	
        //$content_script="    
        //    <script type='text/javascript'>
        //        new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
        //    </script>";
        
        $out.="
        <SCRIPT>
            function display_div(src_id)
            {
                switch (src_id) 
                {
                    case 'to_user':
                	
                        obj_div=document.getElementById('div_to');  
                		obj_div.innerText='Укажите фамилию пользователя';
                        obj_div=document.getElementById('message_to');  
                		obj_div.value='';                      
                        obj_div=document.getElementById('selected_button');  
                		obj_div.value='to_user';
                	    break;
                    
                    case 'to_org':
                                            
                        obj_div=document.getElementById('div_to');  
                		obj_div.innerText='Укажите название организации'; 
                	    obj_div=document.getElementById('message_to');  
                		obj_div.value='';       
                        obj_div=document.getElementById('selected_button');  
                		obj_div.value='to_org';                           	    
                        break;
                        
                    case 'to_group':
                                            
                        obj_div=document.getElementById('div_to');  
                		obj_div.innerText='Укажите название группы'; 
                        obj_div=document.getElementById('message_to');  
                		obj_div.value='';
                        obj_div=document.getElementById('selected_button');  
                		obj_div.value='to_org';                      
                	    break;
                        
                    default:                   
                        
                        obj_div=document.getElementById('div_to');  
                		obj_div.innerText='Укажите фамилию пользователя'; 
                        obj_div=document.getElementById('message_to');  
                		obj_div.value='';                                  	    
                        obj_div=document.getElementById('selected_button');  
                		obj_div.value='to_user';
                	    break;
                }
                return;
            }
            
            </SCRIPT>";
        if($user_id_to!='') 
        {
            $sql_query="SELECT surname, name, name2  FROM ".$sql_pref."_users WHERE enable='Yes'&&id='".$user_id_to."'";
	        $sql_res=mysql_query($sql_query, $conn_id);
        	if (mysql_num_rows($sql_res)>0)
        	{
        		list($surname, $name, $name2)=mysql_fetch_row($sql_res);
        		$surname=stripslashes($surname); $name=stripslashes($name); $name2=stripslashes($name2); $phone_work=stripslashes($phone_work); $phone_home=stripslashes($phone_home); $phone_mobile=stripslashes($phone_mobile); $doljnost=stripslashes($doljnost); $expirience=stripslashes($expirience); $vuz=stripslashes($vuz); $specialnost=stripslashes($specialnost); $descr=stripslashes(trim($descr));
        		
        		$name_show="Пользователь, которому Вы пишите сообщение: <b>".$surname." ".$name." ".$name2."</b>";
            }
            $user_to_out="";
            $user_input_out=""; 
        }
        else 
        {
            $name_show="";
            $user_to_out="Укажите фамилию пользователя<BR/><BR/><font style='font-size: smaller;'>(функционал работает при поддержке браузером javascript. В противном случае используйте телефонную книгу)</font>";
            $user_input_out="<input class='text_input' autocomplete='off' onkeydown='keydown(this, event)'  onkeyup='javascript:search(this,event)' name='message_to_user' id='message_to' style='width:550px;font-size:14px;'>";
    	}
        $out.=$xc2_inc."
    	".@$error_info."
        	<form action='' method=post name='messages_add' onsubmit='function() { return false };' id='message_form' enctype='multipart/form-data'>
            <table>
                <tr>
                    <td></td>
                    <td>".$name_show."</td>
                </tr>
                <tr style='display: none'>
                    <td width='40%'><div>Кому:</div><input type='hidden' name='action' value='messages_add'></td>
                    <td><div><input type='hidden' id='selected_button' name='selected_button' value='to_user'><input type=radio name=type_to value='to_user' checked onclick='display_div(\"to_user\")'> Пользователю &nbsp;&nbsp;&nbsp;</input> <input type=radio name=type_to value='to_org' onclick='display_div(\"to_org\")'> Пользователям организации &nbsp;&nbsp;&nbsp;</input></div></td>
                </tr>
                <tr>
                    <td><div id='div_to'>".$user_to_out."</div></td>
                    <td><div><input type='hidden' id='to_id' name='to_id' value='".$user_id_to."'>".$user_input_out."</div></td>
                </tr>
                <tr><td></td><td><div class='resultdropdown' id='result' style='position:absolute'></div></td></tr>
                <tr style='display: none'>
                    <td><div>Тема сообщения:</div></td>
                    <td><div><textarea name=message_title id=message_title rows=2 style='width:550px;font-size:14px;'>".@$descr."</textarea></div></td>
                </tr>
                <tr>
    			     <td><div>Текст сообщения:</div></td>
                     <td><div><textarea name=content id=content rows=12 style='width:550px; height:350px; font-size:14px;'>".@$content."</textarea></div></td>
                </tr>		
                <tr>
                     <td></td><td><div><input type='submit' name=submit id=submit_button value=Отправить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;' onclick='check_form();'></div></td>
                </tr>
             </table>
    	</form>
        ".$content_script."
    	";
    }
    else
    {
        $out.="Чтобы написать сообщение необходимо авторизоваться!";
    }
	return ($out);
}



function auth_blogs_list()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $path_www, $page_header1;
	$out="";
    
    $page_header1="Ваши блогоМысли";

	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	$perpage=20; $first=$perpage*($page-1);
	$sql_query="SELECT id FROM ".$sql_pref."_blogs_posts WHERE user_id='".$user_id."'";
    $pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums($page, $perpage, $sql_query)."</div>";

	$sql_query="SELECT id, enable, dt, descr FROM ".$sql_pref."_blogs_posts WHERE user_id='".$user_id."' ORDER BY dt DESC, id DESC LIMIT ".$first.",".$perpage;
	//echo $sql_query;
    $sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<div style='padding: 0 0 0 0;'>";
		while(list($id, $enable, $dt, $descr)=mysql_fetch_row($sql_res))
		{
			$descr=stripslashes($descr); $descr=str_replace("\n", "<br>", $descr);
            $descr_show=$descr;
			if ($enable=="Yes") $enable_show="<span style='color:green;'>Пост отображается</span>"; else $enable_show="<span style='color:red;'>Предложение отключено</span>";
			$dt_show=date("d.m.Y", strtotime($dt));
			

			$out.="<div style='padding: 10 0;'>";
			$out.="
					<div style='padding: 1 0 1 0;font-size:12px;'><b>Дата публикации:</b> ".$dt_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><b>Статус:</b> ".$enable_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'>".$descr_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><a href='/auth/blogs_edit/?id=".$id."'>Редактировать</a> &nbsp; <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/auth/posts_del/?id=".$id."'\">Удалить</a></div>
			";
			$out.="</div>";
		}
		$out.="</div>";
	}
	$out.="<div style='padding: 15 0 5 0;'><a href='/auth/blogs_add/'>Добавить блогоМысль</a></div>"; 
	$out.=$pages_show; 
	
	return ($out);
}


function auth_blogs_add()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $page_header1, $user_blog;
	global $rub_url, $auth;
	$out="";
    
    $page_header1="Добавить блогоМысль";
    
	if (isset($_REQUEST['submit']))
	{
		if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";		
		if (isset($_REQUEST['dt_date']) AND !empty($_REQUEST['dt_date'])) $dt=$_REQUEST['dt_date']; else $dt=date("Y-m-d");
		if (isset($_REQUEST['descr']) AND !empty($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
		if (isset($_REQUEST['content']) AND !empty($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $content="";
		if (isset($_REQUEST['tags']) AND !empty($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
   
		if (!empty($descr))
		{
			$sql_query="INSERT INTO ".$sql_pref."_blogs_posts (user_id, enable, dt, descr, content, tags, parent_id) VALUES ('".$user_id."', '".$enable."', '".$dt."', '".$descr."', '".$content."', '".$tags."', '".$user_blog."')";
			$sql_res=mysql_query($sql_query, $conn_id);
		
			header("location:/auth/blogs_list/"); 
			exit();
		}
		{
			$error_info="<div style='padding: 10 0 10 0;color:#ff0000;font-weight:bold;'>Ошибка! Вы не заполнили одно из обязательных полей: Краткое описание.</div>";
		}
	}

	$xc2_inc=file_get_contents($path."inc/xc2.inc");
	$dt_date=date("Y-m-d");
	$enable_show="<input type=checkbox name=enable value='Yes' checked>";
	$main_show="<input type=checkbox name=main value='Yes' checked>";
    
	   
   // $content_script="    
   //     <script type='text/javascript'>
   //         new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
   //     </script>";
    
    
	$out.=$xc2_inc."
	".@$error_info."
	<form action='' method=post name=news_add enctype='multipart/form-data'>
		<div style='padding: 10 0 10 0;'>
			<div>Краткое описание:</div>
			<div><textarea name=descr id=descr rows=4 style='width:550px;font-size:14px;'>".@$descr."</textarea></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Полный текст:</div>
			<div><textarea name=content id=content rows=12 style='width:550px;font-size:14px;'>".@$content."</textarea></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Дата:</div>
			<div><div id=holder></div><input readonly type=Text maxlength=70 name=dt_date id=dt_date value='".@$dt_date."' style='width:80px;font-size:14px;cursor:pointer;' onclick='showCalendar(\"\",document.getElementById(\"dt_date\"),null,\"".$dt_date."\",\"holder\",0,25,1)'></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Теги: (через запятую)</div>
			<div><input type=Text maxlength=70 name=tags id=tags value='".@$tags."' style='width:550px;font-size:14px;'></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Настройки:</div>
			<div>".$enable_show." - отображение предложения</div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div><input type=Submit name=submit value=Сохранить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
		</div>
	</form>
    ".$content_script."
	";
	return ($out);
}

function auth_blogs_edit()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $page_header1;
	global $rub_url, $auth;
	$out="";
    $page_header1="Редактировать пост";
	if (isset($_REQUEST['submit']) && isset($_REQUEST['post_id']))
	{
		if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";		
		if (isset($_REQUEST['dt_date']) AND !empty($_REQUEST['dt_date'])) $dt=$_REQUEST['dt_date']; else $dt=date("Y-m-d");
		if (isset($_REQUEST['descr']) AND !empty($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
		if (isset($_REQUEST['content']) AND !empty($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $content="";
		if (isset($_REQUEST['tags']) AND !empty($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
		if (isset($_REQUEST['sfera']) AND !empty($_REQUEST['sfera'])) $sfera_ids=implode(";",$_REQUEST['sfera']); else $sfera_ids="";
		if (isset($_REQUEST['direction']) AND !empty($_REQUEST['direction'])) $direction_ids=implode(";",$_REQUEST['direction']); else $direction_ids="";
		
		$sql_query="UPDATE ".$sql_pref."_blogs_posts SET enable='".$enable."', dt='".$dt."', descr='".$descr."', content='".$content."', tags='".$tags."'WHERE id='".$_REQUEST['post_id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
	
		header("location:/auth/blogs_list/"); 
		exit();
	}

	
	$sql_query="SELECT id, enable, dt, descr, content, tags FROM ".$sql_pref."_blogs_posts WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $enable, $dt, $descr, $content, $tags, $sfera_ids, $direction_ids)=mysql_fetch_row($sql_res);
		$descr=stripslashes($descr);$content=stripslashes($content);$tags=stripslashes($tags);
		$dt_date=$dt;
		if ($enable=="Yes") $ch="checked"; else $ch=""; 
		$enable_show="<input type=checkbox name=enable value='Yes' ".$ch.">";
		$xc2_inc=file_get_contents($path."inc/xc2.inc");
        
        $sfera_array=explode(";", $sfera_ids);
    	        
		$del_posts="<div style='padding: 20 0 30 0;'><div><a href=\"javascript:if(confirm('Вы уверены?'))window.location='/auth/posts_del/?id=".$id."'\">Удалить пост</a></div></div>";
		
        //$content_script="    
        //    <script type='text/javascript'>
        //        new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
        //    </script>";
	
		$out.=$xc2_inc."
		<form action='' method=post name=posts_add enctype='multipart/form-data'>
			<div style='padding: 10 0 10 0;'>
				<div>Краткое описание:</div>
				<div><textarea name=descr id=descr rows=4 style='width:550px;font-size:14px;'>".@$descr."</textarea></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Полный текст:</div>
				<div><textarea name=content id=content rows=12 style='width:550px;font-size:14px;'>".@$content."</textarea></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Теги:</div>
				<div><input type=Text maxlength=70 name=tags id=tags value='".@$tags."' style='width:550px;font-size:14px;'></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Дата:</div>
				<div><div id=holder></div><input readonly type=Text maxlength=70 name=dt_date id=dt_date value='".@$dt_date."' style='width:80px;font-size:14px;cursor:pointer;' onclick='showCalendar(\"\",document.getElementById(\"dt_date\"),null,\"".$dt_date."\",\"holder\",0,25,1)'></div>
			</div>
			<div style='padding: 10 0 10 0;'>
				<div>Настройки:</div>
				<div>".$enable_show." - отображение предложения</div>
			</div>

			<div style='padding: 10 0 10 0;'>
				<input type=hidden name=post_id value='".$id."'>
				<div><input type=Submit name=submit value=Сохранить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
			</div>
		</form>
		".$del_posts."
        ".$content_script."
		";
	}
	else return ("Ошибка!");
	return ($out);
}

function auth_posts_del()
{
	global $sql_pref, $conn_id, $user_id, $path, $path_www;

	$id=$_REQUEST['id'];
	
	$sql_query="SELECT id FROM ".$sql_pref."_blogs_posts WHERE id='".$id."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $sql_query="DELETE FROM ".$sql_pref."_blogs_posts WHERE id='".$id."'";
        $sql_res_1=mysql_query($sql_query, $conn_id);
    }
	
	header("location:/auth/blogs_list/"); exit();
}


function auth_groups_list()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $path_www, $page_header1;
	$out="";
    
    $page_header1="Ваши группы";

	
	$sql_query="SELECT ".$sql_pref."_groups.id, ".$sql_pref."_groups_users.status, ".$sql_pref."_groups.name, ".$sql_pref."_groups.descr, ".$sql_pref."_groups.enable, ".$sql_pref."_groups.visible FROM ".$sql_pref."_groups_users INNER JOIN ".$sql_pref."_groups ON (".$sql_pref."_groups_users.group_id=".$sql_pref."_groups.id) WHERE user_id='".$user_id."' ORDER BY ".$sql_pref."_groups_users.id DESC, ".$sql_pref."_groups.dt DESC";
    //echo $sql_query;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$num_users=mysql_num_rows($sql_res);
		$out.="<table cellpadding=5 cellspacing=0 border=0>";
		$out.="
			<tr><td colspan=5 style='border-bottom:solid 1px #777777;'>&nbsp;</td></tr>
			<tr bgcolor='#f2f2f2'>
				<td width=30% align=left style='border-bottom:solid 1px #777777;'>Название группы</td>
				<td width=30% align=left valign=top style='border-bottom:solid 1px #777777;'>Описание группы</td>
				<td width=20% align=center valign=top style='border-bottom:solid 1px #777777;'>Статус группы</td>
                <td width=10% align=center valign=top style='border-bottom:solid 1px #777777;'>Ваш статус</td>
                <td width=10% align=center valign=top style='border-bottom:solid 1px #777777;'>Действия</td>
			</tr>";
		while(list($group_id, $user_status_show, $name_show, $descr, $enable, $visible)=mysql_fetch_row($sql_res))
		{
			if($user_status_show=='admin') $del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='/auth/groups_delete/?group_id=".$group_id."&action=groups_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>"; 
			if($user_status_show=='admin' && $visible=="No") $vis="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='/auth/groups_visible/?group_id=".$group_id."&action=groups_visible&visible=Yes'\"><img src='/admin/img/check_no.gif' width=25 height=13 alt='Видимость' border=0></a>"; 
			if($user_status_show=='admin' && $visible=="Yes") $vis="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='/auth/groups_visible/?group_id=".$group_id."&action=groups_visible&visible=No'\"><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Видимость' border=0></a>";
			if($user_status_show!='admin') {$del=""; $vis="";}
            
            $name_show=stripslashes($name_show);
            $descr=stripslashes($descr); $descr=str_replace("\n", "<br>", $descr);
            $descr_show=$descr;
			if ($enable=="Yes") $enable_show="<span style='color:green;'>Группа активна</span>"; else $enable_show="<span style='color:red;'>Группа не активна</span>";
			if ($visible=="Yes") $visible_show="<span style='color:green;'>Группа видна</span>"; else $visible_show="<span style='color:red;'>Группа скрыта</span>";
			
            $out.="
    				<tr>
    					<td align=left valign=middle style='border-bottom:solid 1px #777777;'><a href='".$group_id.".html?action=messages_show'>".$name_show."</a></td>
    					<td align=left valign=middle style='border-bottom:solid 1px #777777;'>".$descr_show."</td>
    					<td align=center valign=middle style='border-bottom:solid 1px #777777;'>".$enable_show."<BR>".$visible_show."</td>
                        <td align=center valign=middle style='border-bottom:solid 1px #777777;'>".$user_status_show."</td>
    			     	<td align=center valign=middle style='border-bottom:solid 1px #777777;'>".$del."<BR>".$vis."</td>
    		        </tr>";		
		}
		$out.="</table>";
	}
	$out.="<div style='padding: 15 0 5 0;'><a href='/auth/groups_add/'>Добавить группу</a></div>"; 
	
	return ($out);
}

function auth_groups_add()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $page_header1;
	global $rub_url, $auth;
	$out="";
    
    $page_header1="Добавить группу";
    
	if (isset($_REQUEST['submit']))
	{
		if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";		
	    if (isset($_REQUEST['name']) AND !empty($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
		if (isset($_REQUEST['descr']) AND !empty($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
		    	
		if (!empty($descr))
		{
			$sql_query="INSERT INTO ".$sql_pref."_groups (name, enable, descr, admin_id) VALUES ('".$name."', '".$enable."', '".$descr."', '".$user_id."')";
			$sql_res=mysql_query($sql_query, $conn_id);
		    $group_id=mysql_insert_id();
            
   			$sql_query1="INSERT INTO ".$sql_pref."_groups_users (group_id, user_id, status) VALUES ('".$group_id."', '".$user_id."', 'admin')";
			$sql_res=mysql_query($sql_query1, $conn_id);
            
			header("location:/auth/groups_list/"); 
			exit();
		}
		{
			$error_info="<div style='padding: 10 0 10 0;color:#ff0000;font-weight:bold;'>Ошибка! Вы не заполнили одно из обязательных полей: Краткое описание.</div>";
		}
	}

	
	$enable_show="<input type=checkbox name=enable value='Yes' checked>";
	$main_show="<input type=checkbox name=main value='Yes' checked>";
    
	 
    
    $content_script="    
        <script type='text/javascript'>
            new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
        </script>";
    
    
	$out.="
	".@$error_info."
	<form action='' method=post name=news_add enctype='multipart/form-data'>
	<form action='' method=post name=news_add enctype='multipart/form-data'>
		<div style='padding: 10 0 10 0;'>
			<div>Название группы:</div>
			<div><textarea name=name id=name rows=4 style='width:550px;font-size:14px;'>".@$name."</textarea></div>
		</div>
		<div style='padding: 10 0 10 0;'>
			<div>Описание группы:</div>
			<div><textarea name=descr id=descr rows=12 style='width:550px;font-size:14px;'>".@$descr."</textarea></div>
		</div>		
		<div style='padding: 10 0 10 0;'>
			<div>Настройки:</div>
			<div>".@$enable_show." - активность группы</div>
		</div>		
		<div style='padding: 10 0 10 0;'>
			<div><input type=Submit name=submit value=Сохранить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
		</div>
	</form>
    ".$content_script."
	";
	return ($out);
}

function auth_groups_del()
{
	global $sql_pref, $conn_id, $user_id, $path, $path_www;

	$group_id=$_REQUEST['group_id'];
	
	$sql_query="SELECT id FROM ".$sql_pref."_groups WHERE id='".$group_id."'&&admin_id='".$user_id."'";
	//echo $sql_query;
    $sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $sql_query="DELETE FROM ".$sql_pref."_groups WHERE id='".$group_id."'";
        $sql_res_1=mysql_query($sql_query, $conn_id);
        $flag=1;
    }
    
    if($flag==1)
    {
       	$sql_query="SELECT id FROM ".$sql_pref."_groups_users WHERE group_id='".$group_id."'";
    	//echo $sql_query;
        $sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
    	{
            $sql_query="DELETE FROM ".$sql_pref."_groups_users WHERE group_id='".$group_id."'";
            $sql_res_1=mysql_query($sql_query, $conn_id);
        }
    }
	
	header("location:/auth/groups_list/"); exit();
}

function auth_groups_vis()
{
	global $sql_pref, $conn_id, $user_id, $path, $path_www;

	$group_id=$_REQUEST['group_id'];
	$visible=$_REQUEST['visible'];
    
	$sql_query="UPDATE ".$sql_pref."_groups SET visible='".$visible."' WHERE id=".$group_id."";
	//echo $sql_query;
    $sql_res_1=mysql_query($sql_query, $conn_id);
    
    
	header("location:/auth/groups_list/"); exit();
}

function auth_groups_show()
{
    global $page_header1, $out, $art_url, $path_www, $path_groups, $sql_pref, $user_id, $conn_id;  
   	$sql_query_main="SELECT ".$sql_pref."_groups.name 
     FROM ".$sql_pref."_groups INNER JOIN ".$sql_pref."_groups_users ON (".$sql_pref."_groups.id=".$sql_pref."_groups_users.group_id) WHERE ".$sql_pref."_groups_users.user_id='".$user_id."' AND ".$sql_pref."_groups.id='".$art_url."'";
	//echo $sql_query_main;
    $sql_res_main=mysql_query($sql_query_main, $conn_id);
	if (mysql_num_rows($sql_res_main)>0)
	{
        list($group_name)=mysql_fetch_row($sql_res_main);
		
        $group_name=stripslashes($group_name);
        //$mess_color="#E3E9FF";
        $mess_mouse_out="onmouseout=\"this.style.backgroundColor='#E3E9FF'; this.style.color='#000000';\"";
        $users_mouse_out="onmouseout=\"this.style.backgroundColor='#E3E9FF'; this.style.color='#000000';\"";
        //$users_color="#E3E9FF";
        if($_REQUEST['action']==messages_show) {$mess_color=" style='cursor: pointer; background-color: #6688EE; color: #FFFFFF'";  $mess_mouse_out=" ";} else {$mess_color=" style='cursor: pointer; background-color: #E3E9FF; color: #000000;'";};
        if($_REQUEST['action']==users_show) {$users_color=" style='cursor: pointer; background-color: #6688EE; color: #FFFFFF'";  $users_mouse_out=" ";} else {$users_color=" style='cursor: pointer; background-color: #E3E9FF; color: #000000;'";};
        //$out=$art_url;
        $out.="<table width=100% cellpadding=5 cellspacing=0 border=0>";
        $out.="			
    			<tr >
    				<td ".$mess_mouse_out." onmouseover=\"this.style.backgroundColor='#6688EE'; this.style.color='#FFFFFF';\" onclick=\"location.href='".$path_www.$path_groups."/".$art_url.".html?action=messages_show'\" ".$mess_color." width=20% align=center>Сообщения</td>
    				<td>&nbsp;</td>
                    <td ".$users_mouse_out." onmouseover=\"this.style.backgroundColor='#6688EE'; this.style.color='#FFFFFF';\" onclick=\"location.href='".$path_www.$path_groups."/".$art_url.".html?action=users_show'\" ".$users_color." width=20% align=center valign=top>Участники</td>
    				<td>&nbsp;</td>
                    <td bgcolor='#f2f2f2' width=20% align=center valign=top style=' '>События</td>
                    <td>&nbsp;</td>
                    <td bgcolor='#f2f2f2' width=20% align=center valign=top style=' '>Документы</td>
                    <td>&nbsp;</td>
                    <td bgcolor='#f2f2f2' width=20% align=center valign=top style=' '>Настройки</td>
    			</tr>
                <tr><td colspan=9 style='border-top:solid 5px #BBCCFF;'>&nbsp;</td></tr></table>";
        
        if($_REQUEST['action']=='users_show') 
        {
            $page_header1="Участники группы '".$group_name."'";
            $out.=groups_users_list();
            $out.=groups_users_invite();
        }
        if(isset($_REQUEST['what_to_do']) && $_REQUEST['what_to_do']==group_invite_user) 
        {
            groups_users_invite_accept($_REQUEST['to_group'],$_REQUEST['to_id']);
        }
       
       if($_REQUEST['action']=='messages_show') 
        {
            $page_header1="Сообщения группы '".$group_name."'";
            $out.=groups_messages_list();
            $out.=groups_message_add();
        }
        
        if(isset($_REQUEST['what_to_do']) && $_REQUEST['what_to_do']==group_send_message) 
        {
            groups_message_add_accept($_REQUEST['to_group'],$_REQUEST['content']);
        }
    
    }
    else
    {
        $out="У Вас отсутствует доступ к данной группе.";
    }
    return ($out);
    
}

function auth_user_notes()
{
	global $sql_pref, $conn_id, $user_id, $user_admin, $path, $path_www, $path_companies, $page_header1, $path_contacts, $page_title; 
	$out="";
    $page_header1="Мои заметки";
	$page_title="Личный кабинет";
	
	if (isset($_REQUEST['new_memo'])) {
	$new_memo=$_REQUEST['new_memo'];
	$new_memo=strip_tags($new_memo);
	$new_memo=addslashes($new_memo);
	$sql_query="UPDATE ".$sql_pref."_users SET note='".$new_memo."' WHERE id='".$user_id."'"; $sql_res=mysql_query($sql_query, $conn_id);
	}
	
	
	
	$sql_query="SELECT note FROM ".$sql_pref."_users WHERE id=".$user_id;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
    {
    		$num_users=mysql_num_rows($sql_res);
    		$out.="";
    		while(list($memo)=mysql_fetch_row($sql_res))
    		{
    		  $memo=stripslashes($memo);		      
		      $memo="<table><tr><td><form name='form_name' action='index.html' method='post' enctype='multipart/form-data'>
                    <NOBR><textarea style='width:500' name='new_memo' rows='20'>$memo</textarea><input type='hidden' name='phone_id' value='$phone_id'></td><td align=center>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=Submit name=submit value=Сохранить style='font-size: 14px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></NOBR></form></td></tr></table>";
              $del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?phone_id=".$phone_id."&action=user_phone_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
                                              
    			$out.=$memo;

    		}    		
    	}	
	return($out);
}

function auth_views_list()
{
	global $sql_pref, $conn_id, $path, $path_users, $user_id, $path_companies, $path_resume, $page_header1, $page_title;
	$out=""; 

$page_header1="История просмотров личного профиля";
$page_title="Личный кабинет";

if($user_id==0) 

$out.="<br><div>История просмотров профиля доступна только <a href='/auth/register/'>зарегистрированным</a> пользователям </div>";

else {
$count=0;
//Чтение данных из базы
$sql_query="SELECT id, views FROM ".$sql_pref."_users WHERE id='".$user_id."'";
if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0){
list($id, $views)=mysql_fetch_row($sql_res);
$views=unserialize($views);
arsort($views);

$out.="<table cellpadding='5' cellspacing='0' border='0' width=100%>";
$out.="
<tr><td colspan='5' style='border-bottom:solid 1px #777777;'>&nbsp;</td></tr>
<tr bgcolor='#f2f2f2'>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>Дата</b></td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>ФИО</b></td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>Компания</b></td>
</tr>";



foreach ($views as $id=>$time){
$company_name="";
$doljnost="";
$sql_query="SELECT ".$sql_pref."_users.surname, ".$sql_pref."_users.name, ".$sql_pref."_users.name2, ".$sql_pref."_users.doljnost, ".$sql_pref."_companies.name FROM ".$sql_pref."_users LEFT JOIN ".$sql_pref."_companies ON ".$sql_pref."_users.company_id=".$sql_pref."_companies.id WHERE ".$sql_pref."_users.enable='Yes' AND ".$sql_pref."_users.id=".$id."";
if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0)
list($surname, $name, $name2, $doljnost, $company_name)=mysql_fetch_row($sql_res);
if($id!=0) $viewer="<a href='/".$path_users."/".$id.".html'>".$surname." ".$name." ".$name2."</a>"; else $viewer="Незарегистрированный гость";

if ($company_name=="") $company_name="-";
if ($doljnost=="") $doljnost="";
$out.="
<tr>
<td width=90 align='center' valign='middle' style='border-bottom:solid 1px #777777;'><span class=dates>".date( 'd.m.y H-i', $time)."</span></td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'>".$viewer."</td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>".$company_name."</b><BR><small><i>".$doljnost."</i></small></td>
</tr>";
$count++;
}
$out.="</table>";
$out.="Всего просмотров за месяц: ".$count;
}
else $out.="За последний месяц Ваш профиль никто не смотрел";
}
$out.="<br><br><a href='javascript:history.back(1)'>««&nbsp;вернуться</a><br>";
return($out);
}

?>
