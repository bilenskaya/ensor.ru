<?php


function news_main()
{
	global $sql_pref, $conn_id, $art_url;
	$out="";
	
    if (isset($_REQUEST['action']))
    {
        if ($_REQUEST['action']=="comment_add")  news_comments_form_save();
        elseif ($_REQUEST['action']=="comment_del")  news_comments_del();
    }

	if (isset($art_url)) $out.=news_out();
	else $out.=news_archive();
	
	return ($out);
}








function news_line($cnt)
{
	global $sql_pref, $conn_id, $path, $path_news;
	$out="";
    $add_link="<table><tr><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=news_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><img width=24px src='/img/add.png' border=0></td><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=news_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><font style='vertical-align: inherit;'>Добавьте свою новость</font></td><td width=80></td><td><a href='http://www.ensor.ru/rabotodateljam/'><img width=24px src='/img/question_big.png' border=0></a></td><td><a href='http://www.ensor.ru/rabotodateljam/'><font style='vertical-align: inherit;'>Узнайте про дополнительные возможности!</font></a></td></tr></table>";
        
 	$sql_query="SELECT id, dt, name, views FROM ".$sql_pref."_news WHERE enable='Yes'&&main='Yes' ORDER BY code DESC LIMIT ".$cnt;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
	   
        $out.="<table cellpadding=0 cellspacing=0 border=0 width='100%'>
                <tr height=7>
                    <td width=1><img src='/img_new/int/main-topleft1.gif' border=0 width=1 height=7 /></td>
                    <td>
                        <table cellpadding=0 cellspacing=0 border=0 width='100%'>
                            <tr>
                                <td width=7><img src='/img_new/int/main-topleft.gif' border=0 width=7 height=7 /></td>
                                <td background='/img_new/int/main-topbg.gif'><img src='/img_new/empty.gif' border=0 width=1 height=7 /></td>
                                <td width=7><img src='/img_new/int/main-topright.gif' border=0 width=7 height=7 /></td>
                            </tr>
                        </table>
                    </td>
                    <td width=1><img src='/img_new/int/main-topright1.gif' border=0 width=1 height=7 /></td>
                </tr>
                <tr>
                    <td width=1 bgcolor='#e1e1e5'><img src='/img_new/empty.gif' border=0 width=1 height=1 /></td>
                    <td valign=top background='/img_new/int/main-topbg1.gif' style='background-image: /img_new/int/main-topbg1.gif; background-position: top; background-repeat: repeat-x;'>
                        <div style='padding: 0 10 10 10;'>
                        <h2 style='padding: 0 0 10 0;'>Новости энергетики России</h2>
                        ";
                        
                        
		while(list($id, $dt, $name,$views)=mysql_fetch_row($sql_res))
		{
		    $views_show="";
            if($views>0)
            {
                $views_show="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Просмотров: ".$views."</span>";
            }
            $comments_show="";
            $sql_query2="SELECT id FROM ".$sql_pref."_news_comments WHERE parent_id='".$id."'";
        	$sql_res_2=mysql_query($sql_query2, $conn_id);
        	if (mysql_num_rows($sql_res_2)>0)
        	{
                $comments_show.="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Комментариев: ".mysql_num_rows($sql_res_2)."</span>";
            }
          
			$name=stripslashes($name);$descr=stripslashes($descr);
			$dt_show="<span class=dates>".date("d.m.Y",strtotime($dt))."</span>";
			            
            $out.=$dt_show;
            $out.="&nbsp;<a href='/".$path_news."/".$id.".html' style='text-decoration:underline;'>".$name."</a>".$comments_show.$views_show."<br><br>";
        }                                
        $out.="<div><a href='/".$path_news."/'>Все новости</a></div><br>".$add_link;
        
                
                
                
        $out.="                
                        </div>
                    </td>
                    <td width=1 bgcolor='#e1e1e5'><img src='/img_new/empty.gif' border=0 width=1 height=1 /></td>
                </tr>
                <tr height=10>
                    <td width=1><img src='/img_new/int/main-bottomleft1.gif' border=0 width=1 height=10 /></td>
                    <td>
                        <table cellpadding=0 cellspacing=0 border=0 width='100%'>
                            <tr>
                                <td width=7><img src='/img_new/int/main-bottomleft.gif' border=0 width=7 height=10 /></td>
                                <td background='/img_new/int/main-bottombg.gif'><img src='/img_new/empty.gif' border=0 width=1 height=10 /></td>
                                <td width=7><img src='/img_new/int/main-bottomright.gif' border=0 width=7 height=10 /></td>
                            </tr>
                        </table>
                    </td>
                    <td width=1><img src='/img_new/int/main-bottomright1.gif' border=0 width=1 height=10 /></td>
                </tr>
            </table>";
        
        	
    }
	return ($out);
}


/*
function news_line($cnt)
{
	global $sql_pref, $conn_id, $path, $path_news;
	$out="";
 	$sql_query="SELECT id, dt, name FROM ".$sql_pref."_news WHERE enable='Yes'&&main='Yes' ORDER BY code LIMIT ".$cnt;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<table class=maincontent>";
        $out.="<tr class=maincontentfirstrow><td class=maincontentfirstrow><h2>Новости энергетики России<h2></td></tr>";
        $out.="<tr class=maincontent><td class=maincontent>";
            //$out.="<div style='font-size:14px;font-weight:normal;'>Новости энергетики:</div>";
		while(list($id, $dt, $name)=mysql_fetch_row($sql_res))
		{
            $comments_show="";
            $sql_query2="SELECT id FROM ".$sql_pref."_news_comments WHERE parent_id='".$id."'";
        	$sql_res_2=mysql_query($sql_query2, $conn_id);
        	if (mysql_num_rows($sql_res_2)>0)
        	{
                $comments_show.="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Комментариев: ".mysql_num_rows($sql_res_2)."</span>";
            }
          
			$name=stripslashes($name);$descr=stripslashes($descr);
			$dt_show="<span class=dates>".date("d.m.Y",strtotime($dt))."</span>";
			            
            $out.=$dt_show;
            $out.="&nbsp;<a href='/".$path_news."/".$id.".html' style='text-decoration:underline;'>".$name."</a>".$comments_show."<br><br>";
        }                                
        $out.="</td></tr><tr><td class=maincontent><a href='/".$path_news."/'>Все новости</a></td></tr>";
        $out.="</table>";	
    }
	return ($out);
}

*/

/*
function news_line($cnt)
{
	global $sql_pref, $conn_id, $path, $path_news;
	$out="";
 	$sql_query="SELECT id, dt, name, descr, content FROM ".$sql_pref."_news WHERE enable='Yes'&&main='Yes' ORDER BY code LIMIT ".$cnt;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $out.="<div style='margin:0 0 5 0;color:#026380;font-size:18px;'>Новости</div>";
        $out.="<table cellpadding=0 cellspacing=0 border=0 background='/img/int/dh.gif' width=100%><tr height=1><td width=100%><img src='/img/empty.gif' width=1 height=1 border=0></td></tr></table>";
        $out.="<div style='padding:5 0 3 0;'>";
		while(list($id, $dt, $name, $descr, $content)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);$descr=stripslashes($descr);$content=stripslashes($content);
			if ($dt!="0000-00-00") $date_show="<div style='padding:1 0 1 0;'><span style='padding:1 3 1 3;color:#ffffff;background-color:#2893c1;font-size:11px;'>".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)."</span></div>"; else $date_show="";
			if (!empty($name)) $name_show="<div style='padding:1 0 1 0;'><a href='/".$path_news."/".$id.".html' style='font-size:12px;font-weight:bold;text-decoration:none;'>".$name."</a></div>"; else $name_show="";
			
			$descr=str_replace("\n","<br>",$descr);
			$descr_show="<div style='padding:1 0 1 0;'>".$descr."</div>";
			

			$out.="<div style='padding:4 0 4 0;'>";
            $out.=$date_show;
            $out.=$name_show;
            $out.=$descr_show;
			$out.="</div>";
		}
        $out.="<a href='/".$path_news."/'>Все новости</a>";
        $out.="</div>";
	}
	return ($out);
}
*/









function news_archive()
{
	global $sql_pref, $conn_id, $path, $path_news, $news_perpage;
	$out="";
    
    $add_link="<table><tr><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=news_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><img width=24px src='/img/add.png' border=0></td><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=news_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><font style='vertical-align: inherit;'>Добавьте свою новость</font></td><td width=80></td><td><a href='http://www.ensor.ru/rabotodateljam/'><img width=24px src='/img/question_big.png' border=0></a></td><td><a href='http://www.ensor.ru/rabotodateljam/'><font style='vertical-align: inherit;'>Узнайте про дополнительные возможности!</font></a></td></tr></table>";
    $out.=$add_link;    
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$news_perpage).",".$news_perpage."";

	$sql_query="SELECT id, dt, name, descr, content, views FROM ".$sql_pref."_news WHERE enable='Yes' ORDER BY code DESC".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $out.="<div style='padding:5 0 3 0;'>";
		while(list($id, $dt, $name, $descr, $content, $views)=mysql_fetch_row($sql_res))
		{
            $views_show="";
            if($views>0)
            {
                $views_show="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Просмотров: ".$views."</span>";
            }
            $name=stripslashes($name);$descr=stripslashes($descr);$content=stripslashes($content);
			if ($dt!="0000-00-00") $date_show="<div style='padding:1 0 1 0;'><span style='padding:1 3 1 3;color:#ffffff;background-color:#2893c1;font-size:11px;'>".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)."</span></div>"; else $date_show="";
			if (!empty($name)) $name_show="<div style='padding:1 0 1 0;'><a href='/".$path_news."/".$id.".html' style='font-size:12px;font-weight:bold;text-decoration:none;'>".$name."</a></div>"; else $name_show="";
			
			$descr=str_replace("\n","<br>",$descr);
			$descr_show="<div style='padding:1 0 1 0;'>".$descr."</div>";
			
            
            $comments_show="";
            $sql_query="SELECT id FROM ".$sql_pref."_news_comments WHERE parent_id='".$id."'";
        	$sql_res_1=mysql_query($sql_query, $conn_id);
        	if (mysql_num_rows($sql_res_1)>0)
        	{
                $comments_show.="<div style='font-size:12px;color:#555;'>Комментариев: ".mysql_num_rows($sql_res_1)."</div>";
            }
            
			$img_show="";
			if (file_exists($path."/files/news/thumbs/".$id.".jpg")) 
			{
				$img_show="<div style='padding: 5 0 5 0;'><a href='/".$path_news."/".$id.".html'><img src='/files/news/thumbs/".$id.".jpg' border=0 alt='".$name."'></a></div>";
			}
            

			
			$out.="<div style='padding: 5 0 10 0;'>";
			
			$out.=" <table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<td valign=middle>
								".$date_show."
                                ".$name_show."
                                ".$descr_show."
                                ".$comments_show."
                                ".$views_show."
							</td>
							<td valign=middle align=center width=100>".$img_show."</td>
						</tr>
					</table>";
			
			$out.="</div>";
            
		}
        $out.="</div>";
        
		
		$sql_query="SELECT id FROM ".$sql_pref."_news WHERE enable='Yes'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$num_predl=mysql_num_rows($sql_res);
		$numpages=ceil($num_predl/$news_perpage);
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
    //$add_link="<div style='padding:2 0 2 0;font-size:11px;color:#555;'><span style='font-size:12px;cursor:pointer;text-decoration:underline;' >Добавить новость</span></div>";
    $out.=$add_link;
	return ($out);
}










function news_out()
{
	global $sql_pref, $conn_id, $path, $art_url, $path_news;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
	$news_id=$art_url;
 	$sql_query="SELECT id, name, dt, descr, content, source, source_link FROM ".$sql_pref."_news WHERE id='".$news_id."' AND enable='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
	    $sql_query2="UPDATE ".$sql_pref."_news SET views=views+1 WHERE id='".$news_id."'";
		$sql_res2=mysql_query($sql_query2, $conn_id);
		list($id, $name, $dt, $descr, $content, $source,$source_link)=mysql_fetch_row($sql_res);
		$name=stripslashes($name);$descr=stripslashes($descr);$content=stripslashes($content);
		if ($dt!="0000-00-00") $date_show=" / ".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4); else $date_show="";
		if (!empty($name)) $name_show="<span style='font-weight:bold;'>".$name."</span>"; else $name_show="";
        $source=stripslashes($source);
        $source_link=stripslashes($source_link);
        if($source_link!="")
        {
            $source="<a href='http://".$source_link."'>".$source."</a>";
        }
        
		$content_show="<div style='padding: 0 0 10 0;'>".$descr."</div><div>".$content."</div>";
		
        $page_title=$page_header1=$name;
		$module_name[]=$name; $module_url=$art_url;
		
		if (file_exists($path."/files/news/imgs/".$id.".jpg")) 
		{
			$size=getimagesize($path."/files/news/imgs/".$id.".jpg");
			$img_show="<img align=right hspace=10 src='/files/news/imgs/".$id.".jpg' width='".$size[0]."' height='".$size[1]."' border=0 alt='".$name."'>";
		}
		else $img_show="";
		

		//if (!empty($img_show)) $out.="<div>".$img_show."</div>";
		$out.="<div>".$content_show."</div><br>";
        $out.="<div><b>Источник: </b>".$source."</div><br>";

		$out.="<br><br><a href='/".$path_news."/'>К списку новостей...</a>";
        
        
        $out.=news_comments($id);
        
	}
	return ($out);
}


























function news_comments($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_news, $months_rus1, $user_id, $user_status, $path_users;
	$out="";
	$out.="<a name=comments></a>";
    $out.="<div style='padding: 25 0 15 0;'>";
	$out.="<h2 style='margin: 3 0 3 0;font-size:18px;'>Комментарии</h2>\n";
	$out.="<table cellpadding=0 cellspacing=0 border=0 width=100% height=1><tr height=1><td height=1 align=center bgcolor=#999999 background='/img/dots-hor.gif'><img src='/img/empty.gif' height=1 border=0></td></tr></table>\n";
	$out.="<div style='padding: 0 0 15 0;'>";
	$sql_query="SELECT id, content, user_id, dt FROM ".$sql_pref."_news_comments WHERE parent_id='".$parent_id."' ORDER BY dt";
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
			
			if ($user_status=="admin") $del_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_news."/".$parent_id.".html?action=comment_del&comment_id=".$id."'\"  style='font-size:9px;color:#999999;'>Удалить</a>"; else $del_but="";
			
			$out.="<div style='margin: 5 0 5 0;'><span style='font-size:14px;font-weight:normal;'>".$name_show."</span><br><span style='color:#999999;font-size:11px;'> ".$date_show."</span>".$del_but."</div>";
			$out.="<div style='margin: 5 0 5 20;'>".$content."</div>";
			$out.="<br>";
		}
	}
	else $out.="<div style='margin: 5 0 5 0;'>Пока нет.</div>\n";
	$out.="</div>";
	$out.=news_comments_form($parent_id);
    $out.="</div>";
	return ($out);
}







function news_comments_form($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_news, $user_id;
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







function news_comments_form_save()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_news, $user_id, $art_url, $user_rate_main, $user_rate_sec;
	
	if (isset($_REQUEST['content1']) && !empty($_REQUEST['content1']))
	{
		$dt=date("Y-m-d H:i:s");
        $parent_id=$art_url;
		
		if (isset($_REQUEST['content1'])) $content=AddSlashes(strip_tags($_REQUEST['content1'], '<br>, <b>, <i>, <u>')); else $content="";
		
		$sql_query="INSERT INTO ".$sql_pref."_news_comments (content, user_id, parent_id, dt) VALUES ('".$content."', '".$user_id."', '".$parent_id."', '".$dt."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		rate_main($user_id, "добавил комментарий", $user_rate_main, $user_rate_sec);
        
        $sql_query2="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'news_comment','".$parent_id."')";
        $sql_res2=mysql_query($sql_query2, $conn_id);
        
		header("location:/".$path_news."/".$parent_id.".html#comments"); exit();
		exit();
	}
	return;
}







function news_comments_del()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_news, $user_id, $user_status, $art_url;
	$out="";
	
	if (!isset($_REQUEST['comment_id']) || ($_REQUEST['comment_id']<=0)) return;
    
	$sql_query="SELECT id FROM ".$sql_pref."_news_comments WHERE id='".$_REQUEST['comment_id']."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0 && $user_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	
	$sql_query="DELETE FROM ".$sql_pref."_news_comments WHERE id='".$_REQUEST['comment_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);

	header("location:/".$path_news."/".$art_url.".html#comments"); exit();
}







?>