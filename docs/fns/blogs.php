<?php

function blogs_main()
{
	global $blogs_user_url, $blogs_user_id, $blogs_user_name;
	global $blogs_post_url, $blogs_post_id, $blogs_post_name;
	global $page_title, $page_header1, $blogs_rub_num;
	
	$out="";
	blogs_url();
	
	if (isset($blogs_user_id) && $blogs_user_id>0) 
	{
		if (isset($blogs_post_id) && $blogs_post_id>0) 
		{
			if (isset($_REQUEST['action']))
			{
				if ($_REQUEST['action']=="comment_del") $out.=blogs_comment_del();
			}
			else $out.=blogs_post();
		}
		else 
		{
			if (isset($_REQUEST['action']))
			{
				if ($_REQUEST['action']=="post_add") $out.=blogs_post_add();
				if ($_REQUEST['action']=="post_edit") $out.=blogs_post_edit();
				if ($_REQUEST['action']=="post_del") $out.=blogs_post_del();
                if ($_REQUEST['action']=="post_visible") $out.=blogs_post_visible($_REQUEST['vis']);
			}
			else $out.=blogs_posts_list();
		}
	}
	else $out.=blogs_list();

	return ($out);
}










function blogs_url()
{
	global $sql_pref, $conn_id, $path, $path_blogs;
	global $url_decode, $module_name, $module_url;
	global $blogs_user_url, $blogs_user_id, $blogs_user_name;
	global $blogs_post_url, $blogs_post_id, $blogs_post_name;
	global $page_title, $page_header1;

	
	if (strpos($url_decode,"?")) $url_decode=substr($url_decode,0,strpos($url_decode,"?"));
	if ("/".$path_blogs."/"!=$url_decode)
	{
		$kol=strlen($path_blogs);
		$str=substr($url_decode,$kol+1);
		if (substr($str,-5)=='.html')
		{
		 	$str=substr($str,0,strlen($str)-5);
			$blogs_post_url=substr($str, strrpos($str, '/')+1);
			$str=substr($str, 0, strrpos($str, '/')+1);
		}
		if (substr($str,-1)=='/') $str=substr($str,0,-1);
		if (substr($str,0,1)=='/') $str=substr($str,1);
		$blogs_rub_url=explode('/', $str);
		$blogs_rnum=count($blogs_rub_url);
//		if ($blogs_rub_url[0]=="post") return;
		if ($blogs_rnum==1)
		{
			$blogs_user_url=$blogs_rub_url[0];
			$sql_query="SELECT id, CONCAT_WS(' ', name, surname) from ".$sql_pref."_users WHERE id='".$blogs_user_url."'";
			$sql_res=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res)==0) error_404();
			list($blogs_user_id, $blogs_user_name)=mysql_fetch_row($sql_res);
			
			$module_name[]=$blogs_user_name; $module_url[]=$blogs_user_url;
			$page_title.=" | Блог ".$blogs_user_name;
			
			if (isset($blogs_post_url) && !empty($blogs_post_url))
			{
				$sql_query="SELECT id, name from ".$sql_pref."_blogs2_posts WHERE url='".$blogs_post_url."'&&user_id='".$blogs_user_id."'";
				$sql_res_1=mysql_query($sql_query, $conn_id);
				if (mysql_num_rows($sql_res_1)==0) error_404();
				list($blogs_post_id, $blogs_post_name)=mysql_fetch_row($sql_res_1);
				$blogs_post_name=stripslashes($blogs_post_name);
				$module_name[]=$blogs_post_name; $module_url[]=$blogs_post_url;
				$page_title.=" | ".$blogs_post_name;
			}
		}
		else  error_404();
		
	}
}






















function blogs_list()
{
	global $sql_pref, $conn_id, $path, $path_blogs, $page_header1;
	global $blogs_posts_perpage, $conf_smileys_src, $conf_smileys_dest;
	global $user_id, $path_users;
	$out="";
	
	
	$perpage=$blogs_posts_perpage;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";
	$pages_show="";
	
	$sql_query="SELECT id FROM ".$sql_pref."_blogs2_posts WHERE visible='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		$pages_show="<br><div align=center>";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<a href='?page=".$i."' style='text-decoration:none;padding:2 5 2 5;font-weight:bold;background-color:#eeeeee;'>".$i."</a>"; 
			else $i_show="<a href='?page=".$i."' style='text-decoration:underline;padding:2 5 2 5;font-weight:normal;background-color:#ffffff;'>".$i."</a>";
			$pages_show.="".$i_show."";
		}
		$pages_show.="</div><br>";
	}
	$post_number=($page-1)*$perpage;


	$sql_query="SELECT id, name, content, user_id, dt, url, views FROM ".$sql_pref."_blogs2_posts WHERE visible='Yes' ORDER BY dt DESC".$pref_page."";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $name, $content, $author_id, $dt, $url, $views)=mysql_fetch_row($sql_res))
		{
		    $views_show="";
            if($views>0)
            {
                $views_show="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Просмотров: ".$views."</span>";
            }
			$name=stripslashes($name);$content=stripslashes($content);

			$sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$author_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			list($author_name)=mysql_fetch_row($sql_res_1);
			$author_name=stripslashes($author_name);


			$content=str_replace("\n","<br>",$content);
			$content=preg_replace("#(?<!=)(?<!\")(?<!\')(https?|ftp)://\S+[^\s.,>)\];'\"!?]#",'<a href="\\0">\\0</a>',$content);
			if (strpos($content,"<blogocut")) 
			{
				preg_match("'<blogocut=.*?>'",$content,$blogocut);
				$blogocut_text=substr($blogocut[0],(strpos($blogocut[0],'"')+1),(strrpos($blogocut[0],'"')-strpos($blogocut[0],'"')-1));
				$content=substr($content,0,strpos($content,"<blogocut"));
				$content.=" <a href='/".$path_blogs."/".$author_id."/".$url.".html'>".$blogocut_text."</a>";
			}
			if (strpos($content,"<img src=\"/")) $content=str_replace("<img src=\"/","<img src=\"".$path_www,$content);
			$dt_show=substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4);
			

			$comments_num=0;
			$sql_query="SELECT id FROM ".$sql_pref."_blogs2_comments WHERE parent_id='".$id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			$comments_num=mysql_num_rows($sql_res_1);
			
			
			
			
			$out.="<div style='padding:20 0 20 0;'>";
			$out.="<h2><a href='/".$path_blogs."/".$author_id."/' style='text-decoration:none;'>".$author_name."</a> &raquo; <a href='/".$path_blogs."/".$author_id."/".$url.".html' style='text-decoration:none;'>".$name."</a></h2>\n";
			$out.="<table cellpadding=0 cellspacing=0 border=0 width=90% height=1><tr height=1><td height=1 align=center bgcolor=#999999 background='/img/int/dots-hor.gif'><img src='/img/empty.gif' height=1 border=0></td></tr></table>\n";
			$out.="<div style='margin: 3 0 3 0;color:#888888;'> &middot; <img src=/img/small/date.gif width=8 height=9 border=0> ".$dt_show." &middot; <img src=/img/small/author.gif width=8 height=9 border=0>&nbsp;&nbsp;<span style='color:#888888;'>".$author_name."</span> &middot; <img src=/img/small/rubric.gif width=11 height=9 border=0>&nbsp;&nbsp;<a href='/".$path_blogs."/".$author_id."/' style='text-decoration:none;'>".$author_name.": личный блог</a> &middot; <img src=/img/small/comments.gif width=10 height=9 border=0> Комментарии: <a href='/".$path_blogs."/".$author_id."/".$url.".html#comments'>".$comments_num."</a> &middot;".$views_show."</div>\n";
			$out.="<br><div class=post_descr>".$content."</div>\n";
			$out.="</div>\n\n";
		}
		

		$out.=$pages_show;
		
	}
	else $out.="<br>Пока тут пусто<br><br>";
	
	return ($out);
}












function blogs_posts_list()
{
	global $sql_pref, $conn_id, $path, $path_blogs, $page_header1, $page_title;
	global $blogs_user_id, $blogs_user_name, $blogs_user_url, $blogs_posts_perpage, $conf_smileys_src, $conf_smileys_dest;
	global $user_id,  $user_admin, $path_users;
	$out="";
	
	$page_header1="Блог ".$blogs_user_name;
	
	$perpage=$blogs_posts_perpage;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";
	$pages_show="";
	
    if ($user_id==$blogs_user_id) 
    {
	   $sql_query="SELECT id FROM ".$sql_pref."_blogs2_posts WHERE user_id='".$blogs_user_id."'";
    }
    else
    {
        $sql_query="SELECT id FROM ".$sql_pref."_blogs2_posts WHERE visible='Yes' and user_id='".$blogs_user_id."'";
    }
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		$pages_show="<br><div align=center>";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<a href='?page=".$i."' style='text-decoration:none;padding:2 5 2 5;font-weight:bold;background-color:#eeeeee;'>".$i."</a>"; 
			else $i_show="<a href='?page=".$i."' style='text-decoration:underline;padding:2 5 2 5;font-weight:normal;background-color:#ffffff;'>".$i."</a>";
			$pages_show.="".$i_show."";
		}
		$pages_show.="</div><br>";
	}
	$post_number=($page-1)*$perpage;


	if ($user_id==$blogs_user_id) 
    {
        $out.="<div style='padding:20 0 0 0;'><a href='/".$path_blogs."/".$blogs_user_url."/?action=post_add' style='font-size:14px;color:green;'>Написать в блог</a></div>";
        $sql_query="SELECT id, name, content, user_id, dt, url, visible, views FROM ".$sql_pref."_blogs2_posts WHERE user_id='".$blogs_user_id."' ORDER BY dt DESC".$pref_page."";
    }
    else
    {
	   $sql_query="SELECT id, name, content, user_id, dt, url, visible, views FROM ".$sql_pref."_blogs2_posts WHERE user_id='".$blogs_user_id."' and visible='Yes' ORDER BY dt DESC".$pref_page."";
	}
    $sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $name, $content, $author_id, $dt, $url, $visible, $views)=mysql_fetch_row($sql_res))
		{
		    $views_show="";
            if($views>0)
            {
                $views_show="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Просмотров: ".$views."</span>";
            }
			$name=stripslashes($name);$content=stripslashes($content);
			$content=str_replace("\n","<br>",$content);
			$content=preg_replace("#(?<!=)(?<!\")(?<!\')(https?|ftp)://\S+[^\s.,>)\];'\"!?]#",'<a href="\\0">\\0</a>',$content);
			if (strpos($content,"<blogocut")) 
			{
				preg_match("'<blogocut=.*?>'",$content,$blogocut);
				$blogocut_text=substr($blogocut[0],(strpos($blogocut[0],'"')+1),(strrpos($blogocut[0],'"')-strpos($blogocut[0],'"')-1));
				$content=substr($content,0,strpos($content,"<blogocut"));
				$content.=" <a href='/".$path_blogs."/".$blogs_user_url."/".$url.".html'>".$blogocut_text."</a>";
			}
			if (strpos($content,"<img src=\"/")) $content=str_replace("<img src=\"/","<img src=\"".$path_www,$content);
			$dt_show=substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4);
			
			$comments_num=0;
			$sql_query="SELECT id FROM ".$sql_pref."_blogs2_comments WHERE parent_id='".$id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			$comments_num=mysql_num_rows($sql_res_1);
			
			
			if ($blogs_user_id==@$user_id || $user_admin=="Yes") $edit_but="<a href='/".$path_blogs."/".$blogs_user_url."/?action=post_edit&post_id=".$id."' style='font-size:9px;color:#999999;'><img src='/img/small/edit.gif' width=25 height=13 border=0></a>"; else $edit_but="";
			if ($blogs_user_id==@$user_id || $user_admin=="Yes") 
            {
				if ($comments_num==0) $del_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_blogs."/".$blogs_user_url."/?action=post_del&post_id=".$id."'\"  style='font-size:9px;color:#999999;'><img src='/img/small/del.gif' width=25 height=13 border=0></a>"; 
				else $del_but=" <img src='/img/small/del_inactive.gif' width=25 height=13 border=0 alt='Сначала удали комментарии'>";
			
                if($visible=='Yes') $vis_but="<a href='/".$path_blogs."/".$blogs_user_url."/?action=post_visible&post_id=".$id."&vis=No' style='font-size:9px;color:#999999;'><img src='/img/small/check_yes.gif' width=25 height=13 border=0></a>";
                else $vis_but="<a href='/".$path_blogs."/".$blogs_user_url."/?action=post_visible&post_id=".$id."&vis=Yes' style='font-size:9px;color:#999999;'><img src='/img/small/check_no.gif' width=25 height=13 border=0></a>";
            }
			else 
            {
                $del_but="";
                $vis_but="";
            }
			$out.="<div style='padding:20 0 20 0;'>";
			$out.="<h2><a href='/".$path_blogs."/".$blogs_user_url."/".$url.".html' style='text-decoration:none;'>".$name."</a></h2>\n";
			$out.="<table cellpadding=0 cellspacing=0 border=0 width=90% height=1><tr height=1><td height=1 align=center bgcolor=#999999 background='/img/int/dots-hor.gif'><img src='/img/empty.gif' height=1 border=0></td></tr></table>\n";
			$out.="<div style='margin: 3 0 3 0;color:#888888;'> &middot; <img src=/img/small/date.gif width=8 height=9 border=0> ".$dt_show." &middot; <img src=/img/small/author.gif width=8 height=9 border=0>&nbsp;&nbsp;<span style='color:#888888;'>".$blogs_user_name."</span> &middot; <img src=/img/small/rubric.gif width=11 height=9 border=0>&nbsp;&nbsp;<a href='/".$path_blogs."/".$blogs_user_url."/' style='text-decoration:none;'>".$blogs_user_name.": личный блог</a> &middot; <img src=/img/small/comments.gif width=10 height=9 border=0> Комментарии: <a href='/".$path_blogs."/".$blogs_user_url."/".$url.".html#comments'>".$comments_num."</a> &middot; ".$views_show." ".$edit_but.$del_but.$vis_but."</div>\n";
			$out.="<br><div class=post_descr>".$content."</div>\n";
			$out.="</div>\n\n";
		}

		$out.=$pages_show;
	}
	else $out.="<br>Пока тут пусто<br><br>";
	
	return ($out);
}












function blogs_post()
{
	global $sql_pref, $conn_id, $path, $path_blogs, $page_header1;
	global $blogs_user_id, $blogs_user_name, $blogs_user_url, $blogs_posts_perpage;
	global $blogs_post_id, $blogs_post_name, $blogs_post_url;
	global $user_id, $user_admin, $path_users;
	
	$out="";
	$sql_query="SELECT id, name, content, dt, url FROM ".$sql_pref."_blogs2_posts WHERE id='".$blogs_post_id."'&&user_id='".$blogs_user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
	    $sql_query2="UPDATE ".$sql_pref."_blogs2_posts SET views=views+1 WHERE id='".$blogs_post_id."'&&user_id='".$blogs_user_id."'";
		$sql_res2=mysql_query($sql_query2, $conn_id);
        
		list($id, $name, $content, $dt, $url)=mysql_fetch_row($sql_res);
		$name=stripslashes($name);$content=stripslashes($content);
		
		$page_header1=$name;
		
		$content_parts=explode("\n",$content);
		$content_parts_num=count($content_parts);
		$fl_code=0;
		$content_show="";
		for ($i=0;$i<$content_parts_num;$i++)
		{
			if (substr($content_parts[$i],0,11)=="<blogohtml>") $fl_code=1;
			elseif (substr($content_parts[$i],0,12)=="</blogohtml>") $fl_code=0;
			
			if ($fl_code==0)
			{
//				$content_parts[$i]=str_replace("\n","<br>",$content_parts[$i]);
				$content_parts[$i].="<br>\n";
				$content_parts[$i]=preg_replace("#(?<!=)(?<!\")(?<!\')(https?|ftp)://\S+[^\s.,>)\];'\"!?]#",'<a href="\\0">\\0</a>',$content_parts[$i]);
				$content_parts[$i]=preg_replace ("'<br><blogocut.*?>'",'',$content_parts[$i]); 
				$content_parts[$i]=preg_replace ("'<blogocut.*?>'",'',$content_parts[$i]); 
				if (strpos($content_parts[$i],"<img src=\"/")) $content_parts[$i]=str_replace("<img src=\"/","<img src=\"".$path_www,$content_parts[$i]);
			}
			
			$content_show.=$content_parts[$i];
		}
		
		$comments_num=0;
		$sql_query="SELECT id FROM ".$sql_pref."_blogs2_comments WHERE parent_id='".$id."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
		$comments_num=mysql_num_rows($sql_res_1);
		
		$dt_show=substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4);
		
		if ($blogs_user_id==@$user_id || $user_admin=="Yes") $edit_but="<a href='/".$path_blogs."/".$blogs_user_url."/?action=post_edit&post_id=".$id."' style='font-size:9px;color:#999999;'><img src='/img/small/edit.gif' width=25 height=13 border=0></a>"; else $edit_but="";
		if ($blogs_user_id==@$user_id || $user_admin=="Yes") 
		{
			if ($comments_num==0) $del_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_blogs."/".$blogs_user_url."/?action=post_del&post_id=".$id."'\"  style='font-size:9px;color:#999999;'><img src='/img/small/del.gif' width=25 height=13 border=0></a>"; 
			else $del_but=" <img src='/img/small/del_inactive.gif' width=25 height=13 border=0 alt='Сначала удали комментарии'>";
		}
		else $del_but="";
		
		$out.="<table cellpadding=0 cellspacing=0 border=0 width=90% height=1><tr height=1><td height=1 align=center bgcolor=#999999 background='/img/int/dots-hor.gif'><img src='/img/empty.gif' height=1 border=0></td></tr></table>\n";
		$out.="<div style='margin: 3 0 3 0;color:#888888;'> &middot; <img src=/img/small/date.gif width=8 height=9 border=0> ".$dt_show." &middot; <img src=/img/small/author.gif width=8 height=9 border=0>&nbsp;&nbsp;<a href='/".$path_users."/".$blogs_user_url."/' style='text-decoration:none;'><span style='color:#888888;'>".$blogs_user_name."</span></a> &middot; <img src=/img/small/rubric.gif width=11 height=9 border=0>&nbsp;&nbsp;<a href='/".$path_blogs."/".$blogs_user_url."/'>Блог ".$blogs_user_name."</a> &middot; <img src=/img/small/comments.gif width=10 height=9 border=0> Комментарии: <a href='/".$path_blogs."/".$blogs_user_url."/".$url.".html#comments'>".$comments_num."</a> &middot; ".$edit_but.$del_but."</div>\n";
		$out.="<br><div class=post_descr>".$content_show."</div><br>\n\n";
		

		$out.="<br>\n\n";
		$out.=blogs_comments($id);
	}
	return ($out);
}

























function blogs_post_add()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_blogs;
	global $blogs_user_id, $blogs_user_name, $blogs_user_url, $user_id, $user_admin;
	$out="";
	
	$page_header1=$blogs_user_name.": добавить запись";
	
	if ($blogs_user_id!=$user_id && $user_admin!="Yes") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	if (isset($_REQUEST['submit']))
	{
		if (isset($_REQUEST['name']) AND !empty($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $error_name="<br>Ошибка!";
		if (isset($_REQUEST['content']) AND !empty($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $error_content="Ошибка!";

		if (empty($error_name) && empty($error_content))
		{
			$dt=date("Y-m-d H:i:s");
			
			if (!empty($name)) $url=translit_url($name);
			else $url=date("YmdHi");
			$fl=1;$i=2;
			while ($fl==1)
			{
				if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) $pref_id=" AND id<>'".$_REQUEST['id']."'"; else $pref_id="";
				$sql_query="SELECT url FROM ".$sql_pref."_blogs2_posts WHERE user_id='".$user_id."' AND url='".$url."'".$pref_id."";
				$sql_res=mysql_query($sql_query, $conn_id);
				if (mysql_num_rows($sql_res)>0) {$url.=$i;$i++;}
				else $fl=0;
			}

			$sql_query="INSERT INTO ".$sql_pref."_blogs2_posts (dt, name, content, user_id, url) VALUES ('".$dt."', '".$name."', '".$content."', '".$user_id."', '".$url."')";
			$sql_res=mysql_query($sql_query, $conn_id);

			header("location:/".$path_blogs."/".$blogs_user_url."/"); exit();
		}
	}
	
	$out.=blogs_post_form(0);
	return ($out);
}







function blogs_post_edit()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_blogs;
	global $blogs_user_id, $blogs_user_name, $blogs_user_url, $user_id, $user_forum_status, $user_admin;
	$out="";
	$page_header1=$blogs_user_name.": редактировать запись";
	
	if ($blogs_user_id!=$user_id && $user_admin!="Yes") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	if (!isset($_REQUEST['post_id']) || ($_REQUEST['post_id']<=0)) return;
	
	if (isset($_REQUEST['submit']))
	{
		if (isset($_REQUEST['name']) AND !empty($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
		if (isset($_REQUEST['content']) AND !empty($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $content="";
		$dt=date("Y-m-d H:i:s");
			
		$sql_query="UPDATE ".$sql_pref."_blogs2_posts SET name='".$name."',content='".$content."' WHERE id='".$_REQUEST['post_id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);

		header("location:/".$path_blogs."/".$blogs_user_url."/"); exit();
	}

	$out.=blogs_post_form($_REQUEST['post_id']);
	return ($out);
}








function blogs_post_form($post_id)
{
	global $sql_pref, $conn_id, $path;
	global $rub_url, $user_id;
	$out="";

	if (isset($post_id) && $post_id>0)
	{
		$sql_query="SELECT id, name, content, dt, url FROM ".$sql_pref."_blogs2_posts WHERE id='".$post_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			list($id, $name, $content, $dt,  $url)=mysql_fetch_row($sql_res);
			$name=stripslashes($name);$content=stripslashes($content);
		}
	}

	$vis="<script src=/inc/js/vis.js type=text/javascript></script>
				<div>
					<a href='' onclick=\"return insert_text('b');\"><img src=/img/vis/bold.gif alt='Жирный' width=24 height=24 border=0></a>
					<a href='' onclick=\"return insert_text('i');\"><img src=/img/vis/italic.gif alt='Курсив' width=24 height=24 border=0></a>
					<a href='' onclick=\"return insert_text('u');\"><img src=/img/vis/underline.gif alt='Подчеркивание' width=24 height=24 border=0></a>
					<a href='' onclick=\"return insert_text('strike');\"><img src=/img/vis/strike.gif alt='Зачеркивание' width=24 height=24 border=0></a>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href='' onclick=\"return insert_div('left');\"><img src=/img/vis/align_left.gif alt='Выравнивание по левому краю' width=24 height=24 border=0></a>
					<a href='' onclick=\"return insert_div('center');\"><img src=/img/vis/align_center.gif alt='Выравнивание по центру' width=24 height=24 border=0></a>
					<a href='' onclick=\"return insert_div('right');\"><img src=/img/vis/align_right.gif alt='Выравнивание по правому краю' width=24 height=24 border=0></a>
					<a href='' onclick=\"return insert_div('justify');\"><img src=/img/vis/align_justify.gif alt='Выравнивание по обоим краям' width=24 height=24 border=0></a>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href='' onclick=\"insert_link(); return false;\"><img src=/img/vis/hyperlink.gif alt='Ссылка' width=24 height=24 border=0></a>
					<a href='' onclick=\"insert_image(); return false;\"><img src=/img/vis/image.gif alt='Изображение' width=24 height=24 border=0></a>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href='' onclick=\"insert_cut(); return false;\"><img src=/img/vis/divider.gif alt='Разрыв' width=24 height=24 border=0></a>
				</div>";

	$out.="
	<form action='' method=post name=post_add_edit>
	<input type=hidden name=post_id value='".$post_id."'>
	
	Заголовок:<br>
	<input type=Text maxlength=60 name=name id=name value='".@$name."' style='width:500px;font-size:14px;'><br>
	<br>
	".$vis."<textarea class=form name=content id=content rows=20 style='width:500px;font-size:14px;'>".@$content."</textarea><br>
	<br>
	<input type=Submit name=submit value=Сохранить style='font-size: 10px; width:100px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'>
	</form>
	<div><b>Обратите внимание!</b><br>Пожалуйста, при написании больших сообщений используйте функцию <b><i>разрыв (cut)</i></b> <img src=/img/vis/divider.gif alt='Разрыв' width=24 height=24 border=0>.</div>
	";
	return ($out);
}





function blogs_post_del()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_blogs;
	global $blogs_user_id, $blogs_user_name, $blogs_user_url, $user_id, $user_admin;
	$out="";
	
	$sql_query="SELECT id FROM ".$sql_pref."_blogs2_posts WHERE id='".$_REQUEST['post_id']."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0 && $user_admin!="Yes") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	if (!isset($_REQUEST['post_id']) || ($_REQUEST['post_id']<=0)) return;
	
	$sql_query="DELETE FROM ".$sql_pref."_blogs2_posts WHERE id='".$_REQUEST['post_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);

	header("location:/".$path_blogs."/".$blogs_user_url."/"); exit();
}

function blogs_post_visible($visible)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_blogs;
	global $blogs_user_id, $blogs_user_name, $blogs_user_url, $user_id, $user_admin;
	$out="";
    
    //if (isset($_REQUEST['vis'])) $visible=$_REQUEST['vis']; $visible="Yes";
    
	
	$sql_query="SELECT id FROM ".$sql_pref."_blogs2_posts WHERE id='".$_REQUEST['post_id']."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0 && $user_admin!="Yes") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	if (!isset($_REQUEST['post_id']) || ($_REQUEST['post_id']<=0)) return;
	
	$sql_query="UPDATE ".$sql_pref."_blogs2_posts SET visible='".$visible."' WHERE id='".$_REQUEST['post_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
    
    if($visible=='Yes')
    {
        $last_post_id=$_REQUEST['post_id'];
        $sql_query2="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'add_post','".$last_post_id."')";
        $sql_res2=mysql_query($sql_query2, $conn_id);
    }
    if($visible=='No')
    {
        $last_post_id=$_REQUEST['post_id'];
        $sql_query="DELETE FROM ".$sql_pref."_users_action WHERE action_type='add_post' AND row_id='".$_REQUEST['post_id']."'";
        $sql_res=mysql_query($sql_query, $conn_id);
    }
    

	header("location:/".$path_blogs."/".$blogs_user_url."/"); exit();
}























function blogs_comments($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_blogs, $path_users;
	global $blogs_user_id, $blogs_user_name, $blogs_user_url, $user_id, $user_forum_status;
	global $blogs_post_id, $blogs_post_name, $blogs_post_url;
	global $months_rus1;
	$out="";
	$out.="<a name=comments></a><h2 style='margin: 3 0 3 0;font-size:18px;'>Комментарии</h2>\n";
	$out.="<table cellpadding=0 cellspacing=0 border=0 width=90% height=1><tr height=1><td height=1 align=center bgcolor=#999999 background='/img/int/dots-hor.gif'><img src='/img/empty.gif' height=1 border=0></td></tr></table>\n";
	$out.="<br>";
	$sql_query="SELECT id, content, user_id, dt FROM ".$sql_pref."_blogs2_comments WHERE parent_id='".$parent_id."' ORDER BY dt";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$comments_num=mysql_num_rows($sql_res);
//		$out.="<div style='margin: 3 0 3 0;'>Всего: <b>".$comments_num."</b></div><br>\n";
		while(list($id, $content, $commentator_id, $dt)=mysql_fetch_row($sql_res))
		{
			$sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$commentator_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			list($name)=mysql_fetch_row($sql_res_1);
			
			$name=stripslashes($name);$content=stripslashes($content);
			$content=str_replace("\n","<br>",$content);
			$content=preg_replace("#(?<!=)(?<!\")(?<!\')(https?|ftp)://\S+[^\s.,>)\];'\"!?]#",'<a href="\\0">\\0</a>',$content);
			$date_show=substr($dt,8,2)." ".$months_rus1[substr($dt,5,2)]." ".substr($dt,0,4)." в ".substr($dt,11,2).":".substr($dt,14,2);
			$name_show="<a href='/".$path_users."/".$commentator_id.".html'>".$name."</a>";
			
			$dt_5=mktime(substr($dt,11,2), floatval(substr($dt,14,2))+5, substr($dt,17,2), substr($dt,5,2), substr($dt,8,2), substr($dt,0,4));
			$cur_dt=time();
			if ($commentator_id==@$user_id)
			{
				if ($cur_dt<$dt_5) $del_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_blogs."/".$blogs_user_url."/".$blogs_post_url.".html?action=comment_del&comment_id=".$id."'\"  style='font-size:9px;color:#999999;'><img src='/img/small/del.gif' width=25 height=13 border=0></a>"; 
				else $del_but="<img src='/img/small/del_inactive.gif' width=25 height=13 border=0 alt='Вы можете удалить свой комментарий только в течение 5 минут после написания'>";
			}
			else $del_but="";
			
			$out.="<div style='margin: 5 0 5 0;'><span style='font-size:18px;'>".$name_show."</span><br><span style='color:#999999;font-size:11px;'> ".$date_show."</span>".$del_but."</div>";
			$out.="<div style='margin: 5 0 5 20;'>".$content."</div>";
			$out.="<br>";
		}
	}
	else $out.="<div style='margin: 5 0 5 0;'>Пока нет.</div>\n";
	$out.="<br><br>";
	$out.=blogs_comments_form($parent_id);
	return ($out);
}







function blogs_comments_form($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_blogs;
	global $blogs_user_id, $blogs_user_name, $blogs_user_url, $user_id;
	global $user_id;
	$out="";
	if ($user_id==0) return ("<div style='font-size:12px;color:#999999;'><i>Комментарии могут оставлять только <a href='/auth/register/'>зарегистрированные</a> пользователи.</i></div>");
	if (isset($_REQUEST['new_comment']) && $_REQUEST['new_comment']=="Yes")  blogs_comments_form_save();
	
	$out.="<h2 style='margin: 3 0 3 0;'>Ваш комментарий:</h2>\n";
	$out.="<script language='Javascript'>
		function check_form()
		{
			var str = 'OK';
			if (document.getElementById('content1').value=='') str='КОММЕНТАРИЙ';
			if (document.getElementById('name').value=='') str='ВАШЕ ИМЯ';
			return str;
		}
		</script>";
	$out.="<form action='' method='post' name='form_comments' onSubmit='if (check_form()!=\"OK\") {alert(\"Вы не заполнили поле \" + check_form() + \"!\"); return false;} else return true;'>";


	$out.="<div>
					<textarea id=content1 name=content1 rows=8 style='overflow: auto; font-size: 12px;width:500px;'>".@$content."</textarea>
				 </div>";
	$out.="<div style='padding: 5 0 10 0;color:#777777;font-size:11px;'>Комментарии не по теме будут безжалостно изничтожаться!</div>";
	$out.="<input type=hidden name=new_comment value='Yes'>";
	$out.="<input class='button' type='submit' value='Отправить' name='add' style='padding: 2 2 2 2; font-size: 10px; font-weight: bold; background-color: transparent; color: #3E3E3E; border: 1px solid #CCCCCC;'></div>";
	$out.="</form>";
	$out.="";
	$out.="<br><br>";
	return ($out);
}







function blogs_comments_form_save()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_blogs;
	global $blogs_user_id, $blogs_user_name, $blogs_user_url;
	global $blogs_post_id, $blogs_post_name, $blogs_post_url;
	global $user_id;
	
	if (isset($_REQUEST['content1']) && !empty($_REQUEST['content1']))
	{
		$dt=date("Y-m-d H:i:s");
		
		if (isset($_REQUEST['content1'])) $content=addslashes(strip_tags($_REQUEST['content1'], '<br>, <b>, <i>, <u>')); else $content="";
		
		$sql_query="INSERT INTO ".$sql_pref."_blogs2_comments (content, user_id, parent_id, dt) VALUES ('".$content."', '".$user_id."', '".$blogs_post_id."', '".$dt."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		
        $sql_query2="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'blog_comment','".$blogs_post_id."')";
        $sql_res2=mysql_query($sql_query2, $conn_id);
 
        
		header("location:/".$path_blogs."/".$blogs_user_url."/".$blogs_post_url.".html#comments"); exit();
		exit();
	}
	return;
}







function blogs_comment_del()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_blogs;
	global $blogs_user_id, $blogs_user_name, $blogs_user_url, $user_id, $user_forum_status;
	global $blogs_post_id, $blogs_post_name, $blogs_post_url;
	$out="";
	
	if ($blogs_user_id!=$user_id) return ("<b>К сожалению, у вас нет прав для этой операции</b>");
//	if (mysql_num_rows($sql_res)==0 && $user_forum_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	if (!isset($_REQUEST['comment_id']) || ($_REQUEST['comment_id']<=0)) return;
	
	$sql_query="DELETE FROM ".$sql_pref."_blogs2_comments WHERE id='".$_REQUEST['comment_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);

	header("location:/".$path_blogs."/".$blogs_user_url."/".$blogs_post_url.".html#comments"); exit();
}












?>
