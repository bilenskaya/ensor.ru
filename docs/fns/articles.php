<?php


function articles_main()
{
	global $sql_pref, $conn_id, $art_url;
	$out="";
	
    if (isset($_REQUEST['action']))
    {
        if ($_REQUEST['action']=="comment_add")  articles_comments_form_save();
        elseif ($_REQUEST['action']=="comment_del")  articles_comments_del();
    }
    
	if (isset($art_url)) $out.=articles_out();
	else $out.=articles_list();
	
	return ($out);
}










function articles_list()
{
	global $sql_pref, $conn_id, $path, $path_articles, $articles_perpage;
	$out="";
	
    $add_link="<table><tr><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=art_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><img width=24px src='/img/add.png' border=0></td><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=art_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><font style='vertical-align: inherit;'>Добавьте свою статью</font></td><td width=80></td><td><a href='http://www.ensor.ru/rabotodateljam/'><img width=24px src='/img/question_big.png' border=0></a></td><td><a href='http://www.ensor.ru/rabotodateljam/'><font style='vertical-align: inherit;'>Узнайте про дополнительные возможности!</font></a></td></tr></table>";
    $out.=$add_link;
    
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$articles_perpage).",".$articles_perpage."";

	$sql_query="SELECT id, url, dt, name, descr, category, views FROM ".$sql_pref."_articles WHERE enable='Yes' ORDER BY dt DESC".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $url, $dt, $name, $descr, $category, $views)=mysql_fetch_row($sql_res))
		{
		    $views_show="";
            if($views>0)
            {
                $views_show="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Просмотров: ".$views."</span>";
            }
			$name=stripslashes($name);$descr=stripslashes($descr);$category=stripslashes($category);
			
			if (!empty($name)) $name_show="<div><a href='".$url.".html' style='font-size:14px;'>".$name."</a></div>"; else $name_show="";
			if ($dt!="0000-00-00 00:00") $dt_show="<span style='font-size:11px;color:#777777;padding:0 5 0 0;'>".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)."</span>"; else $dt_show="";
			//if (!empty($category)) $category_show="<div>Рубрика: <a href='/".$path_articles."/".urlencode($category)."/'>".$category."</a></div>"; else $category_show="";
			if (!empty($category)) $category_show="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Рубрика: ".$category."</span>"; else $category_show="";
			if (!empty($descr)) $descr_show="<div align=justify style='padding: 0 0 5 0;font-size:11px;'>".$descr."</div>"; else $descr_show="";
			
			$img_show="";
			if (file_exists($path."/files/articles/thumbs/".$id.".jpg") || file_exists($path."/files/articles/thumbs/".$id.".gif")) 
			{
				if (file_exists($path."/files/articles/thumbs/".$id.".jpg")) $ext=".jpg";
				elseif (file_exists($path."/files/articles/thumbs/".$id.".gif")) $ext=".gif";
				$size=getimagesize($path."/files/articles/thumbs/".$id.$ext);
				$img_show="<div style='padding: 5 0 5 0;'><a href='".$url.".html'><img src='/files/articles/thumbs/".$id.$ext."' width='".$size[0]."' height='".$size[1]."' border=0 alt='".$name."'></a></div>";
			}
            
        	
            $comments_show="";
            $sql_query="SELECT id FROM ".$sql_pref."_articles_comments WHERE parent_id='".$id."'";
        	$sql_res_1=mysql_query($sql_query, $conn_id);
        	if (mysql_num_rows($sql_res_1)>0)
        	{
                $comments_show.="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Комментариев: ".mysql_num_rows($sql_res_1)."</span>";
            }
            
			
			$out.="<div style='padding: 5 0 10 0;'>";
			
			$out.=" <table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<td valign=middle>
								".$name_show."
                                <div>".$dt_show.$category_show.$comments_show.$views_show."</div>
                                
								".$descr_show."
							</td>
							<td valign=middle align=center width=100>".$img_show."</td>
						</tr>
					</table>";
			
			$out.="</div>";
		}
		
	}
	
	
	$sql_query="SELECT id FROM ".$sql_pref."_articles WHERE enable='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$articles_perpage);
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
    //$add_link="<div style='padding:2 0 2 0;font-size:11px;color:#555;'>Если вы хотите добавить статью, <span style='font-size:11px;cursor:pointer;text-decoration:underline;' onClick=\"window.open('/feedback.html?subj=art_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\">сообщите нам</span> и она будет добавлена на сайт.</div>";
    $out.=$add_link;
	return ($out);
}










function articles_out()
{
	global $sql_pref, $conn_id, $path, $art_url, $path_articles;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
	
 	$sql_query="SELECT id, name, dt, descr, content, category FROM ".$sql_pref."_articles WHERE url='".$art_url."' AND enable='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $sql_query2="UPDATE ".$sql_pref."_articles SET views=views+1 WHERE url='".$art_url."'";
		$sql_res2=mysql_query($sql_query2, $conn_id);
        
		list($id, $name, $dt, $descr, $content, $category)=mysql_fetch_row($sql_res);
		$name=stripslashes($name);$descr=stripslashes($descr);$content=stripslashes($content);
		
		if ($dt!="0000-00-00 00:00") $dt_show="<div style='font-size:11px;color:#777777;'>".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)."</div>"; else $dt_show="";
		if (!empty($category)) $category_show="<div style='font-size:11px;color:#777777;'>Рубрика: ".$category."</div>"; else $category_show="";
        $content_show="<div style='padding: 5 0 5 0;'>".$descr."</div><div style='padding: 5 0 5 0;'>".$content."</div>";
		
		$page_title=$page_header1=$name;
		$module_name[]=$name; $module_url=$art_url;
		
		$img_show="";
		/*
		if (file_exists($path."/files/articles/imgs/".$id.".jpg")) 
		{
			$size=getimagesize($path."/files/articles/imgs/".$id.".jpg");
			$img_show="<div style='padding:5 0 10 0;'><img src='/files/articles/imgs/".$id.".jpg' width='".$size[0]."' height='".$size[1]."' border=0 alt='".$name."'></div>";
		}
		*/
		

		$out.=$img_show;
		$out.=" <table cellpadding=0 cellspacing=0 border=0>
					<tr>
						<td>".$dt_show."</td>
						<td>&nbsp;&nbsp;&nbsp;</td>
						<td>".$category_show."</td>
					</tr>
				</table>";
		$out.=$content_show;
		
		
		$out.="<div style='padding:50 0 20 0;'><a href='/".$path_articles."/'>К списку статей...</a></div>";
        
        
        $out.=articles_comments($id);
	}
	return ($out);
}

































function articles_comments($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_articles, $months_rus1, $user_id, $user_status, $path_users;
	$out="";
	$out.="<a name=comments></a>";
    $out.="<div style='padding: 25 0 15 0;'>";
	$out.="<h2 style='margin: 3 0 3 0;font-size:18px;'>Комментарии</h2>\n";
	$out.="<table cellpadding=0 cellspacing=0 border=0 width=100% height=1><tr height=1><td height=1 align=center bgcolor=#999999 background='/img/dots-hor.gif'><img src='/img/empty.gif' height=1 border=0></td></tr></table>\n";
	$out.="<div style='padding: 0 0 15 0;'>";
	$sql_query="SELECT id, content, user_id, dt FROM ".$sql_pref."_articles_comments WHERE parent_id='".$parent_id."' ORDER BY dt";
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
	$out.=articles_comments_form($parent_id);
    $out.="</div>";
	return ($out);
}







function articles_comments_form($parent_id)
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







function articles_comments_form_save()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_articles, $user_id, $art_url;
	
	if (isset($_REQUEST['content1']) && !empty($_REQUEST['content1']))
	{
		$dt=date("Y-m-d H:i:s");
        $parent_id=$_REQUEST['parent_id'];
		
		if (isset($_REQUEST['content1'])) $content=AddSlashes(strip_tags($_REQUEST['content1'], '<br>, <b>, <i>, <u>')); else $content="";
		
		$sql_query="INSERT INTO ".$sql_pref."_articles_comments (content, user_id, parent_id, dt) VALUES ('".$content."', '".$user_id."', '".$parent_id."', '".$dt."')";
		$sql_res=mysql_query($sql_query, $conn_id);        
        rate_main($user_id, "добавил комментарий", $user_rate_main, $user_rate_sec);
        
        $sql_query2="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'articles_comment','".$parent_id."')";
        $sql_res2=mysql_query($sql_query2, $conn_id);
		
		header("location:".$_SERVER['REQUEST_URI']."#comments"); exit();
	}
	return;
}







function articles_comments_del()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_articles, $user_id, $user_status, $art_url;
	$out="";
	
	if (!isset($_REQUEST['comment_id']) || ($_REQUEST['comment_id']<=0)) return;
    
	$sql_query="SELECT id FROM ".$sql_pref."_articles_comments WHERE id='".$_REQUEST['comment_id']."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0 && $user_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	
	$sql_query="DELETE FROM ".$sql_pref."_articles_comments WHERE id='".$_REQUEST['comment_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
    
    $requr=substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],"?"));
	header("location:".$requr."#comments"); exit();
}






?>