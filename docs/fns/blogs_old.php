<?php
function blogs_main()
{
	global $sql_pref, $conn_id, $art_url;
	$out="";
    
    if (isset($_REQUEST['action']))
    {
        //echo $_REQUEST['action'];

        if ($_REQUEST['action']=="comment_add")  blogs_comments_form_save();
        if ($_REQUEST['action']=="comment_del")  blogs_comments_del();
        if ($_REQUEST['action']=="add_vote")  questions_answers_add_vote();
        if ($_REQUEST['action']=="add_question_to_base") add_question_to_base();
        if ($_REQUEST['action']=="answer_add")  questions_answer_form_save();
        if ($_REQUEST['action']=="comment_del")  questions_comments_del();
        if ($_REQUEST['action']=="question_del")  question_del();
        if ($_REQUEST['action']=="question_change_form")  $out.=question_change_form(); 
        if ($_REQUEST['action']=="question_change")  question_change();    
    }
    
   	if ($_REQUEST['post_id']) $out.=posts_out();
    //echo "lghjh".$art_url;

    elseif (isset($_REQUEST['action']) &&  $_REQUEST['action']=="add_blogs") $out.=blogs_add_form();
	else $out.=blogs_list();
	
	return ($out);
}


function blogs_list()
{
	global $sql_pref, $path_blogs, $conn_id, $user_id, $user_admin, $user_status, $path, $path_www, $page_header1;
	$out="";
    
    $page_header1="БлогоМысли Энергетиков";

	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	$perpage=20; $first=$perpage*($page-1);
	$sql_query="SELECT id FROM ".$sql_pref."_blogs_posts";
    $pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums($page, $perpage, $sql_query)."</div>";

	$sql_query="SELECT ".$sql_pref."_blogs_posts.id, ".$sql_pref."_blogs_posts.enable, ".$sql_pref."_blogs_posts.dt, ".$sql_pref."_blogs_posts.descr, ".$sql_pref."_blogs_posts.content, ".$sql_pref."_blogs_rubs.name FROM ".$sql_pref."_blogs_posts INNER JOIN ".$sql_pref."_blogs_rubs ON (".$sql_pref."_blogs_rubs.id=".$sql_pref."_blogs_posts.parent_id) WHERE ".$sql_pref."_blogs_posts.enable='Yes' ORDER BY dt DESC, id DESC LIMIT ".$first.",".$perpage;
	//echo $sql_query;
    $sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<div style='padding: 0 0 0 0;'>";
		while(list($id, $enable, $dt, $descr, $content, $blog_name)=mysql_fetch_row($sql_res))
		{
			$descr=stripslashes($descr); $descr=str_replace("\n", "<br>", $descr);
            $descr_show=$descr;
			$content=stripslashes($content); $content=str_replace("\n", "<br>", $content);
            $content_show=substr($content,0,450);            
			if ($enable=="Yes") $enable_show="<span style='color:green;'>Пост отображается</span>"; else $enable_show="<span style='color:red;'>Предложение отключено</span>";
			$dt_show=date("d.m.Y", strtotime($dt));
			

			$out.="<div style='padding: 10 0;'>";
			$out.="
					<div style='padding: 1 0 1 0;font-size:12px;'><b>".$blog_name."</b><hr><b>Дата публикации:</b> ".$dt_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><h2>".$descr_show."</h2>".$content_show."...</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><a href='/".$path_blogs."/?post_id=".$id."'>Читать далее...</a></div>";
            if($user_status=="admin")        
    		{	
    		     $out.="<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><a href='/auth/blogs_edit/?id=".$id."'>Редактировать</a> &nbsp; <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/auth/posts_del/?id=".$id."'\">Удалить</a></div>";
            }			
			$out.="</div>";
		}
		$out.="</div>";
	}
	//$out.="<div style='padding: 15 0 5 0;'><a href='/auth/blogs_add/'>Добавить блогоМысль</a></div>"; 
	$out.=$pages_show; 
	
	return ($out);
}


function posts_out()
{
	global $sql_pref, $conn_id, $path, $art_url, $path_blogs;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
    
    if (isset($_REQUEST['post_id'])) $post_id=$_REQUEST['post_id'];

	
 	$sql_query="SELECT id, name, dt, descr, content FROM ".$sql_pref."_blogs_posts WHERE id='".$post_id."' AND enable='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $name, $dt, $descr, $content, $category)=mysql_fetch_row($sql_res);
		$name=stripslashes($name);$descr=stripslashes($descr);
		$content=stripslashes($content);
        $content=str_replace("\n", "<br>", $content);
                
		if ($dt!="0000-00-00 00:00") $dt_show="<div style='font-size:11px;color:#777777;'>".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)."</div>"; else $dt_show="";
		if (!empty($category)) $category_show="<div style='font-size:11px;color:#777777;'>Рубрика: ".$category."</div>"; else $category_show="";
        $content_show="<div style='padding: 5 0 5 0;'>".$descr."</div><div style='padding: 5 0 5 0;'>".$content."</div>";
		
		$page_title=$page_header1=$name;
		$module_name[]=$name; $module_url=$art_url;
		
		$img_show="";

		$out.=$img_show;
		$out.=" <table cellpadding=0 cellspacing=0 border=0>
					<tr>
						<td>".$dt_show."</td>
						<td>&nbsp;&nbsp;&nbsp;</td>
						<td>".$category_show."</td>
					</tr>
				</table>";
		$out.=$content_show;
		
		
		$out.="<div style='padding:50 0 20 0;'><a href='/".$path_blogs."/'>К списку постов...</a></div>";
        
        
        //$out.=posts_comments($id);
	}
    $out.=blogs_comments($post_id);
	return ($out);
}


function blogs_comments($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_articles, $months_rus1, $user_id, $user_status, $path_users;
	$out="";
	$out.="<a name=comments></a>";
    $out.="<div style='padding: 25 0 15 0;'>";
	$out.="<h2 style='margin: 3 0 3 0;font-size:18px;'>Комментарии</h2>\n";
	$out.="<table cellpadding=0 cellspacing=0 border=0 width=100% height=1><tr height=1><td height=1 align=center bgcolor=#999999 background='/img/dots-hor.gif'><img src='/img/empty.gif' height=1 border=0></td></tr></table>\n";
	$out.="<div style='padding: 0 0 15 0;'>";
	$sql_query="SELECT id, content, user_id, dt FROM ".$sql_pref."_blogs_comments WHERE parent_id='".$parent_id."' ORDER BY dt";
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
			
			if ($commentator_id==@$user_id || $user_status=="admin") $del_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='".$_SERVER['REQUEST_URI']."?action=comment_del&comment_id=".$id."'\"  style='font-size:9px;color:#999999;'>Удалить</a>"; else $del_but="";
			
			$out.="<div style='margin: 5 0 5 0;'><span style='font-size:14px;font-weight:normal;'>".$name_show."</span><br><span style='color:#999999;font-size:11px;'> ".$date_show."</span>".$del_but."</div>";
			$out.="<div style='margin: 5 0 5 20;'>".$content."</div>";
			$out.="<br>";
		}
	}
	else $out.="<div style='margin: 5 0 5 0;'>Пока нет.</div>\n";
	$out.="</div>";
	$out.=blogs_comments_form($parent_id);
    $out.="</div>";
	return ($out);
}

function blogs_comments_form($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_articles, $user_id;
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
	$out.="<form action='' method='post' name='form_comments' onSubmit='if (check_form()!=\"OK\") {alert(\"Вы не заполнили поле \" + check_form() + \"!\"); return false;} else return true;'>";
	$out.="<input type=hidden name=parent_id value='".$parent_id."'>";


	$out.="<div>
					<textarea id=content1 name=content1 rows=4 style='overflow: auto; font-size: 12px;width:500px;'>".@$content."</textarea>
				 </div>";
	$out.="<span style='color:#777777;font-size:11px;'><br>Просьба оставлять комментарии только по теме!</span><br><br>";
	$out.="<div><input type=hidden name=action value='comment_add'>";
	$out.="<input class='button' type='submit' value='Отправить' name='add' style='padding: 2 2 2 2; font-size: 10px; font-weight: bold; background-color: transparent; color: #3E3E3E; border: 1px solid #CCCCCC;'></div>";
	$out.="</form>";
	$out.="";
	$out.="<br><br>";
	return ($out);
}







function blogs_comments_form_save()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_articles, $path_blogs, $user_id, $art_url;
	
	if (isset($_REQUEST['content1']) && !empty($_REQUEST['content1']))
	{
		$dt=date("Y-m-d H:i:s");
        $parent_id=$_REQUEST['parent_id'];
		
		if (isset($_REQUEST['content1'])) $content=AddSlashes(strip_tags($_REQUEST['content1'], '<br>, <b>, <i>, <u>')); else $content="";
		
		$sql_query="INSERT INTO ".$sql_pref."_blogs_comments (content, user_id, parent_id, dt) VALUES ('".$content."', '".$user_id."', '".$parent_id."', '".$dt."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		
		header("location:".$_SERVER['REQUEST_URI']."#comments"); exit();
	}
	return;
}







function blogs_comments_del()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_articles, $user_id, $user_status, $art_url;
	$out="";
	
	if (!isset($_REQUEST['comment_id']) || ($_REQUEST['comment_id']<=0)) return;
    
	$sql_query="SELECT id FROM ".$sql_pref."_blogs_comments WHERE id='".$_REQUEST['post_id']."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0 && $user_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	
	$sql_query="DELETE FROM ".$sql_pref."_blogs_comments WHERE id='".$_REQUEST['comment_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
    
    $requr=substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],"?"));
	header("location:".$requr."#comments"); exit();
}




?>
