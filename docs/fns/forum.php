<?php




function forum_main()
{
	global $page_content, $page_title, $page_header1,$forum_rub_num, $forum_rub_url, $forum_topic_id;
	$out="";
	forum_url();
	if (isset($forum_rub_url[0]) && $forum_rub_url[0]=="post") forum_redir_post();
	elseif (isset($forum_rub_url[0]) && $forum_rub_url[0]=="lastposts") $out.=forum_lastposts(10);
	elseif (isset($forum_rub_url[0]) && $forum_rub_url[0]=="userposts") $out.=forum_userposts(@$_REQUEST['user']);
	else
	{
		if ($forum_rub_num==0) $out.=forum_mainpage();
		elseif ($forum_rub_num==1) $out.=forum_rub_1();
		elseif ($forum_rub_num==2)
		{
            $out.=forum_nav();
			if (isset($forum_topic_id))
			{
				if (isset($_REQUEST['action']))
				{
					if ($_REQUEST['action']=="post_add") $out.=forum_post_add();
					if ($_REQUEST['action']=="post_edit") $out.=forum_post_edit();
					if ($_REQUEST['action']=="post_del") $out.=forum_post_del();
					if ($_REQUEST['action']=="someposts_del") $out.=forum_someposts_del();
					if ($_REQUEST['action']=="topic_changeactive") $out.=forum_topic_changeactive();
				}
				else
				{
					require_once($path."inc/lib/bbcode/bbcode.lib.php");
					$out.=forum_posts_show();
				}
			}
			else
			{
				if (isset($_REQUEST['action']))
				{
					if ($_REQUEST['action']=="topic_add") $out.=forum_topic_add();
				}
				else $out.=forum_topics_show();
			}
		}
		else $out.=error_404();
	}
	return ($out);
}










function forum_url()
{
	global $sql_pref, $conn_id, $path, $path_forum;
	global $url_decode, $module_name, $module_url;
	global $forum_rub_url, $forum_rub_id, $forum_rub_name, $forum_rub_num;
	global $forum_topic_url, $forum_topic_id, $forum_topic_name;
	global $page_title, $page_header1;

	if (strpos($url_decode,"?")) $url_decode=substr($url_decode,0,strpos($url_decode,"?"));
	if ("/".$path_forum."/"!=$url_decode)
	{

		$kol=strlen($path_forum);
		$str=substr($url_decode,$kol+1);
		if (substr($str,-5)=='.html')
		{
		 	$str=substr($str,0,strlen($str)-5);
			$forum_topic_url=substr($str, strrpos($str, '/')+1);
			$str=substr($str, 0, strrpos($str, '/')+1);
		}
		if (substr($str,-1)=='/') $str=substr($str,0,-1);
		if (substr($str,0,1)=='/') $str=substr($str,1);
		$forum_rub_url=explode('/', $str);
		$forum_rnum=count($forum_rub_url);
		$forum_rub_num=0;
		$forum_rub_parent_id[0]=0;
		if ($forum_rub_url[0]=="post" || $forum_rub_url[0]=="lastposts" || $forum_rub_url[0]=="userposts") return;
		for ($i=0; $i<=($forum_rnum-1); $i++)
		{
			$forum_rub_num++;
			$sql_query="SELECT id, name FROM ".$sql_pref."_forum_rubs WHERE level='".($i+1)."'&&url='".$forum_rub_url[$i]."'&&parent_id='".$forum_rub_parent_id[$i]."'";
			$sql_res=mysql_query($sql_query, $conn_id);
			list($forum_rub_id[$i], $forum_rub_name[$i])=mysql_fetch_row($sql_res);
			if (@!$forum_rub_id[$i]) error_404();
			$forum_rub_name[$i]=StripSlashes($forum_rub_name[$i]);
			if ($i!=($forum_rnum-1)) $forum_rub_parent_id[($i+1)]=$forum_rub_id[$i];
			$module_name[$i]=$forum_rub_name[$i]; $module_url[$i]=$forum_rub_url[$i];
			$page_title.=" | ".$forum_rub_name[$i];
			$page_header1=$forum_rub_name[$i];
		}
		if (@$forum_topic_url)
		{
			$sql_query="SELECT id, name from ".$sql_pref."_forum_topics WHERE id='".$forum_topic_url."'&&parent_id='".$forum_rub_id[($forum_rub_num-1)]."'";
			$sql_res=mysql_query($sql_query, $conn_id);
			list($forum_topic_id, $forum_topic_name)=mysql_fetch_row($sql_res);
			if (@!$forum_topic_id) error_404();
			$forum_topic_name=StripSlashes($forum_topic_name);
			$module_name[($i)]=$forum_topic_name; $module_url[($i)]=$forum_topic_url;
			$page_title.=" | ".$forum_topic_name;
		}
	}
}










function forum_redir_post()
{
	global $sql_pref, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $path_forum, $forum_topic_url, $posts_perpage;

	$sql_query="SELECT parent_id FROM ".$sql_pref."_forum_posts WHERE id='".$forum_topic_url."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($topic_id)=mysql_fetch_row($sql_res);

		$sql_query="SELECT parent_id FROM ".$sql_pref."_forum_topics WHERE id='".$topic_id."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
		list($parent_id)=mysql_fetch_row($sql_res_1);

		$parid=array();
		$parid[0]=$parent_id;
		$i=1;
		$url="/";
		while ($parid[($i-1)]!=0)
		{
			$sql_query="SELECT url, parent_id FROM ".$sql_pref."_forum_rubs WHERE id=".$parid[($i-1)];
			$sql_res_1=mysql_query($sql_query, $conn_id);
			list($purl, $parid[$i])=mysql_fetch_row($sql_res_1);
			$i++;
			$url="/".$purl.$url;
		}
		$url="/".$path_forum.$url.$topic_id.".html";

		$k=0;
		$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE parent_id=".$topic_id." ORDER BY dt";
		$sql_res_1=mysql_query($sql_query, $conn_id);
		while(list($post_id)=mysql_fetch_row($sql_res_1))
		{
			$k++;
			if ($post_id==$forum_topic_url) break;
		}

		$pref="";
		if (floor(($k-1)/$posts_perpage)>0) $pref="?page=".(floor(($k-1)/$posts_perpage)+1);

		header("location:".$url.$pref."#".$forum_topic_url); exit();
	}
	return ($out);
}










function forum_mainpage()
{
	global $sql_pref, $conn_id, $forum_rub_id, $forum_rub_url, $path_forum, $posts_perpage, $path_users;
	$out="";
	$sql_query="SELECT id, url, name FROM ".$sql_pref."_forum_rubs WHERE level='1' AND enable='Yes' ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $url, $name)=mysql_fetch_row($sql_res))
		{
			$name=StripSlashes($name);
			$name_show=$name;
			$out.="<table cellpadding=3 cellspacing=0 border=0 width=100% background='/img/forum/bg-main1-top.gif' style='border: solid 1px #555555;background-position: top; background-repeat: repeat-x; background-image: /img/forum/bg-main1-top.gif;' bgcolor='#ffffff'>
						<tr height=30>
							<td align=left valign=middle style='color:#000000;font-weight:bold;padding:0 0 0 10;'><a href='/".$path_forum."/".$url."/' style='color:#000000;font-size:13px;text-decoration:none;font-weight:bold;'>".$name_show."</a></td>
							<td width=46 align=center valign=middle style='color:#000000;border-left: solid 1px #aaaaaa;font-weight:bold;padding:0 0 0 0;'>Тем</td>
							<td width=126 align=center valign=middle style='color:#000000;border-left: solid 1px #aaaaaa;font-weight:bold;padding:0 0 0 0;'>Последнее</td>
						</tr>
					 </table>";

			$sql_query="SELECT id, url, name FROM ".$sql_pref."_forum_rubs WHERE parent_id='".$id."' AND enable='Yes' ORDER BY code";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res_1)>0)
			{
				$out.="<table cellpadding=3 cellspacing=0 border=0 width=100%>";
				while(list($id1, $url1, $name1)=mysql_fetch_row($sql_res_1))
				{
					$name1=StripSlashes($name1);
					$name1_show=$name1;

					$sql_query="SELECT id FROM ".$sql_pref."_forum_topics WHERE type='active'&&parent_id='".$id1."'";
					$sql_res_2=mysql_query($sql_query, $conn_id);
					$num_topics=mysql_num_rows($sql_res_2);

					$login_lastpost_show="&nbsp;";
					$sql_query="SELECT id, dt_lastpost, user_id_lastpost FROM ".$sql_pref."_forum_topics WHERE parent_id='".$id1."' ORDER BY dt_lastpost DESC";
					$sql_res_2=mysql_query($sql_query, $conn_id);
					if (mysql_num_rows($sql_res_2)>0)
					{
						list($lasttopic_id, $dt_lastpost, $user_id_lastpost)=mysql_fetch_row($sql_res_2);

						$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE parent_id='".$lasttopic_id."'";
						$sql_res_3=mysql_query($sql_query, $conn_id);
						$num_posts_lasttopic=mysql_num_rows($sql_res_3);

						$sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$user_id_lastpost."'";
						$sql_res_3=mysql_query($sql_query, $conn_id);
						if (mysql_num_rows($sql_res_3)>0)
						{
							list($login_lastpost)=mysql_fetch_row($sql_res_3);
							$login_lastpost=StripSlashes($login_lastpost);
							if (substr($dt_lastpost,0,10)==date("Y-m-d")) $dt_lastpost_show="<span style='color:#DF0019;'>Сегодня, ".substr($dt_lastpost,11,5)."</span>";
							elseif (substr($dt_lastpost,0,10)==date("Y-m-d", (time()-60*60*24))) $dt_lastpost_show="Вчера, ".substr($dt_lastpost,11,5);
							else $dt_lastpost_show=substr($dt_lastpost,8,2).".".substr($dt_lastpost,5,2).".".substr($dt_lastpost,0,4)." ".substr($dt_lastpost,11,5);
							$url_lastpost_show="/".$path_forum."/".$url."/".$url1."/".$lasttopic_id.".html?page=".(floor(($num_posts_lasttopic-1)/$posts_perpage)+1)."#lastpost";
							$login_lastpost_show="<a href='".$url_lastpost_show."' style='text-decoration:none;'><span style='color:#555555;font-size:11px;'><img src='/img/tools/comments.gif' alt='Автор' width=10 height=9 border=0> ".$dt_lastpost_show."<br>".$login_lastpost."</span></a>";
						}
					}

//					if ($id1==3) $sub="<br><span style='font-size:11px;'>&nbsp;&nbsp;-&nbsp;<a href='/forum/avtoliga/profiles/' style='text-decoration:none;'>Профили игроков</a></span>"; else $sub="";

					$out.="<tr>
									<td align=center valin=middle width=30 style='border-left: solid 1px #777777;'><img src='/img/forum/ball.gif' width=24 height=24 border=0></td>
									<td valin=middle align=left style='border-left: solid 0px #777777;'><a href='/".$path_forum."/".$url."/".$url1."/'>".$name1_show."</a>".@$sub."</td>
									<td width=40 align=center style='border-left: solid 1px #777777;'>".$num_topics."</td>
									<td width=120 align=center style='border-left: solid 1px #777777;border-right: solid 1px #777777;'>".$login_lastpost_show."</td>
								</tr>";
				}
				$out.="</table>";
			}

		}
		$out.="<table cellpadding=0 cellspacing=0 border=0 width=100%><tr height=1><td width=100% bgcolor=#777777><img src=/img/empty.gif border=0 width=1 height=1></td></tr></table>";
	}

	$out.="<br><br>";
	$out.="<li><a href='/".$path_forum."/lastposts/'>10 последних сообщений</a></li>";
	$out.="<br>";
	$out.="<li><a href='/".$path_users."/'>Пользователи</a></li>";
	$out.="<br><br>";

	return ($out);
}










function forum_rub_1()
{
	global $sql_pref, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $path_forum;
	$out="";
	$sql_query="SELECT id, url, name FROM ".$sql_pref."_forum_rubs WHERE parent_id='".$forum_rub_id[0]."' AND enable='Yes' ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<table cellpadding=3 cellspacing=0 border=0 width=100%>";
		while(list($id, $url, $name)=mysql_fetch_row($sql_res))
		{
			$name=StripSlashes($name);
			$name_show=$name;
			$out.="<tr><td align=left><a href='/".$path_forum."/".$forum_rub_url[0]."/".$url."/'>".$name_show."</a></td></tr>";
		}
		$out.="</table>";
	}
	return ($out);
}










function forum_topics_show()
{
	global $sql_pref, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $path_forum, $posts_perpage, $topics_perpage, $path;
	global $user_id;
	$out="";

	$perpage=$topics_perpage;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";
	$pages_show="";
	$sql_query="SELECT id FROM ".$sql_pref."_forum_topics WHERE parent_id='".$forum_rub_id[1]."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		$pages_show="<div>Страницы: ";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show=$i;
			$pages_show.="<span style='padding:0 3 0 3;background-color:#ffffff;border:solid 1px #aaaaaa;'><a href='?page=".$i."' style='text-decoration:none;'>".$i_show."</a></span>&nbsp;";
		}
		$pages_show.="</div>";
	}
	$post_number=($page-1)*$perpage;

//	if ($forum_rub_id[1]==3) $out.="<span style='color:#777777;'>Дополнительный раздел:</span><br><b>&ndash;&nbsp;<a href='/forum/avtoliga/profiles/'>Профили игроков</a></b><br><br>";
	$sql_query="SELECT id, name, descr, user_id, dt_lastpost, user_id_lastpost, hits FROM ".$sql_pref."_forum_topics WHERE parent_id='".$forum_rub_id[1]."' ORDER BY dt_lastpost DESC".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
			$out.="<table cellpadding=3 cellspacing=0 border=0 width='100%' background='/img/forum/bg-main1-top.gif' style='border: solid 1px #555555;background-position: top; background-repeat: repeat-x; background-image: /img/forum/bg-main1-top.gif;' bgcolor='#ffffff'>
						<tr height=30>
							<td align=left valign=middle style='color:#000000;font-weight:bold;padding:0 0 0 10;'><b>Темы</b></td>
							<td width=80 align=center valign=middle style='color:#000000;border-left: solid 1px #aaaaaa;font-weight:bold;padding:0 0 0 0;'><b>Ответы</b></td>
							<td width=80 align=center valign=middle style='color:#000000;border-left: solid 1px #aaaaaa;font-weight:bold;padding:0 0 0 0;'><b>Просмотры</b></td>
							<td width=180 align=center valign=middle style='color:#000000;border-left: solid 1px #aaaaaa;font-weight:bold;padding:0 0 0 0;'><b>Последний</b></td>
						</tr>
					 </table>";

		$out.="<table cellpadding=3 cellspacing=0 border=0 width='100%'>";

		$bgcol="#f6f6f6";
		while(list($id, $name, $descr, $topic_user_id, $dt_lastpost, $user_id_lastpost, $hits)=mysql_fetch_row($sql_res))
		{
			$name=StripSlashes($name);
			$name_show=$name;
			$descr=StripSlashes($descr);
			if (!empty($descr)) $descr_show="<span style='color:#555555;font-size:11px;'>".$descr."</span><br>"; else $descr_show="";
			if ($hits==0) $hits_show="-"; else $hits_show=$hits;


			if ($bgcol=="#f6f6f6") $bgcol="#ffffff"; else $bgcol="#f6f6f6";

			$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE parent_id='".$id."'";
			$sql_res_2=mysql_query($sql_query, $conn_id);
			$num_posts=mysql_num_rows($sql_res_2)-1;
			if ($num_posts<0) break;

			$pages="";
			$flstr=0;
			if (($num_posts+1-$posts_perpage)>0)
			{
				$pages.=" &nbsp; ( Стр.: ";
				for ($i=1;$i<=(floor($num_posts/$posts_perpage)+1);$i++)
				{
					if ($i<4 || ((floor($num_posts/$posts_perpage)+1)-$i)<3) $pages.="<a href='".$id.".html?page=".$i."'>".$i."</a> ";
					elseif ($flstr==0) { $flstr=1; $pages.=" ... ";}
				}
				$pages.=")";
			}


			$sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$topic_user_id."'";
			$sql_res_2=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res_2)>0)
			{
				list($login)=mysql_fetch_row($sql_res_2);
				$login=StripSlashes($login);
				$login_show="<span style='color:#555555;font-size:11px;'><img src='/img/tools/author.gif' alt='Автор' width=7 height=9 border=0> ".$login."</span>";
			}

			$sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$user_id_lastpost."'";
			$sql_res_2=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res_2)>0)
			{
				list($login_lastpost)=mysql_fetch_row($sql_res_2);
				$login_lastpost=StripSlashes($login_lastpost);
				if (substr($dt_lastpost,0,10)==date("Y-m-d")) $dt_lastpost_show="<span style='color:#DF0019;'>Сегодня, ".substr($dt_lastpost,11,5)."</span>";
				elseif (substr($dt_lastpost,0,10)==date("Y-m-d", (time()-60*60*24))) $dt_lastpost_show="Вчера, ".substr($dt_lastpost,11,5);
				else $dt_lastpost_show=substr($dt_lastpost,8,2).".".substr($dt_lastpost,5,2).".".substr($dt_lastpost,0,4)." ".substr($dt_lastpost,11,5);
				$url_lastpost_show="".$id.".html?page=".(floor($num_posts/$posts_perpage)+1)."#lastpost";
				$login_lastpost_show="<a href='".$url_lastpost_show."' style='text-decoration:none;'><span style='color:#555555;font-size:11px;'><img src='/img/tools/comments.gif' alt='Автор' width=10 height=9 border=0> ".$dt_lastpost_show."<br>".$login_lastpost."</span></a>";
			}
			else $login_lastpost_show="&nbsp;";

			$out.="<tr bgcolor=".$bgcol.">
							<td valin=middle align=left style='border-left: solid 1px #777777;border-bottom: solid 1px #777777;'><a href='/".$path_forum."/".$forum_rub_url[0]."/".$forum_rub_url[1]."/".$id.".html' style='text-decoration:;'>".$name_show."</a>".$pages."<br>".$descr_show.$login_show."</td>
							<td width=74 align=center style='border-left: solid 1px #777777;border-bottom: solid 1px #777777;'>".$num_posts."</td>
							<td width=74 align=center style='border-left: solid 1px #777777;border-bottom: solid 1px #777777;'>".$hits_show."</td>
							<td width=174 align=center style='border-left: solid 1px #777777;border-right: solid 1px #777777;border-bottom: solid 1px #777777;'>".$login_lastpost_show."</td>
						</tr>";
		}
		$out.="</table>";

		$out.="<table cellpadding=3 cellspacing=0 border=0 width=100%>
					<tr height=30>
						<td align=right valign=middle>".$pages_show."</td>
					</tr>
				 </table>";
	}

	if (isset($user_id) && $user_id>0)
	{
		$out.="<br><div><a href='?parent_id=".$forum_rub_id[1]."&action=topic_add'><img src='/img/forum/new-topic.png' alt='Новая тема' width=84 height=22 border=0></a></div>";
	}
	else
	{
		$out.="<br><div>Чтобы оставить сообщение на нашем форуме необходимо <a href='/auth/register/'>зарегистрироваться</a>.</div>";
	}
	return ($out);
}









function forum_topic_add()
{
	global $sql_pref, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $path_forum, $page_header1, $path;
	global $user_id;
	$out="";

	if (!isset($user_id) || $user_id<=0) return ($out);

	if (isset($_REQUEST['name']) && !empty($_REQUEST['name']) && isset($_REQUEST['content']) && !empty($_REQUEST['content']))
	{
		if (isset($_REQUEST['parent_id'])) $parent_id=$_REQUEST['parent_id']; else $parent_id=0;
		if (isset($_REQUEST['name'])) $name=AddSlashes(strip_tags($_REQUEST['name'])); else $name=""; $name=htmlspecialchars($name, ENT_QUOTES);
		if (isset($_REQUEST['descr'])) $descr=AddSlashes(strip_tags($_REQUEST['descr'])); else $descr="";
		if (isset($_REQUEST['content'])) $content=AddSlashes(strip_tags($_REQUEST['content'])); else $content="";
		$dt=date("Y-m-d H:i:s");

		$sql_query="INSERT INTO ".$sql_pref."_forum_topics (type, name, descr, user_id, parent_id, dt_lastpost, user_id_lastpost) VALUES ('active', '".$name."', '".$descr."', '".$user_id."', '".$parent_id."', '".$dt."', '".$user_id."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$last_topic_id=mysql_insert_id();

		$sql_query="INSERT INTO ".$sql_pref."_forum_posts (content, user_id, parent_id, dt) VALUES ('".$content."', '".$user_id."', '".$last_topic_id."', '".$dt."')";
		$sql_res=mysql_query($sql_query, $conn_id);
        $post_last_id=mysql_insert_id();
        
        $sql_query2="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'add_forum_post','".$post_last_id."')";
        $sql_res2=mysql_query($sql_query2, $conn_id); 
        
        

		header("location:/".$path_forum."/".$forum_rub_url[0]."/".$forum_rub_url[1]."/".$last_topic_id.".html");
		exit();
	}
	else
	{
		if (file_exists($path."inc/forum_smileys.inc")) $smileys=file_get_contents($path."inc/forum_smileys.inc"); else $smileys="";
		$page_header1="Новая тема";
		$out.="<form action='' method=post name=form_topic>
						<input type=hidden name=parent_id value='".$forum_rub_id[1]."'>
						Заголовок темы:<br>
						<input type=text id=name name=name value='' size=40><br><br>
						Краткое описание:<br>
						<input type=text id=descr name=descr value='' size=40> <span style='color:#555555;font-size:11px;'>(можно не заполнять)</span><br><br>
						<textarea id=content_bb name=content></textarea><br>
						<input class=button_submit type=submit value='Создать тему' name='add'>
					</form>";
	}
	return ($out);
}









function forum_topic_changeactive()
{
	global $sql_pref, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $path_forum, $page_header1, $path;
	global $user_id;
	$out="";

	if (!isset($user_id) || $user_id<=0) return ($out);

	$sql_query="UPDATE ".$sql_pref."_forum_topics SET type='".$_REQUEST['value']."' WHERE id='".$_REQUEST['topic_id']."'";
	$sql_res_1=mysql_query($sql_query, $conn_id);
	
	header("location:/".$path_forum."/".$forum_rub_url[0]."/".$forum_rub_url[1]."/".$_REQUEST['topic_id'].".html");
	exit();

	return;
}











function forum_posts_show()
{
	global $sql_pref, $path, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $forum_topic_id, $path_forum, $path_lichka, $posts_perpage, $conf_smileys_src,$conf_smileys_dest, $user_forum_status, $page_header1;
	global $user_id;
	$out="";

	$sql_query="SELECT type, name, descr, hits FROM ".$sql_pref."_forum_topics WHERE id='".$forum_topic_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0) return("Ошибка");
	list($topic_type, $topic_name, $topic_descr, $topic_hits)=mysql_fetch_row($sql_res);
	$topic_name=StripSlashes($topic_name);$topic_descr=StripSlashes($topic_descr);

	$topic_hits++;
	$sql_query="UPDATE ".$sql_pref."_forum_topics SET hits='".$topic_hits."' WHERE id='".$forum_topic_id."'";
	$sql_res_1=mysql_query($sql_query, $conn_id);

	$perpage=$posts_perpage;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";
	$pages_show="";
	$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE parent_id='".$forum_topic_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		$dotsfl=0;
		$pages_show="<div>Страницы: ";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i)
			{
				$i_show="<b>".$i."</b>";
			}
			else
			{
				$i_show=$i;
			}
			if ($i<4 || (($page-$i)<2 && ($page-$i)>=0) || (($i-$page)<2 && ($i-$page)>=0) || ($numpages-$i)<3) { $pages_show.="<span style='padding:0 3 0 3;background-color:#ffffff;border:solid 1px #aaaaaa;'><a href='?page=".$i."' style='text-decoration:none;'>".$i_show."</a></span> "; $dotsfl=0; }
			elseif ($dotsfl==0) {$pages_show.=" ... "; $dotsfl=1;}
		}
		$pages_show.="</div>";
	}
	$post_number=($page-1)*$perpage;

	$sql_query="SELECT id, content, user_id, dt, dt_edit FROM ".$sql_pref."_forum_posts WHERE parent_id='".$forum_topic_id."' ORDER BY dt".$pref_page."";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
//		$out.=$pages_show;
		$page_header1.=": ".$topic_name;
		$out.="";
		if ($user_forum_status=="admin") $out.="<form name='forum_posts_form' id='forum_posts_form' action='' method=post><input type=hidden name=action value=someposts_del><input type=hidden name=topic_id value=".$forum_topic_id.">";
		$out.="<br><table cellpadding=3 cellspacing=0 border=0 width=100% background='/img/forum/bg-main1-top.gif' style='border: solid 1px #CCCCCC;background-position: top; background-repeat: repeat-x; background-image: /img/forum/bg-main-top.gif;' bgcolor='#ffffff'>
					<tr height=30>
						<td align=left valign=middle style='color:#000000;font-weight:bold;padding:0 0 0 10;' height=22>&nbsp;&ndash;&nbsp;".$topic_name."</td>
						<td align=right valign=middle>".$pages_show."</td>
					</tr>
				 </table>";

					 $out.="<table cellpadding=0 cellspacing=0 border=0 width=100%>
					 	<tr height=1><td width=100% bgcolor=#ffffff><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
					 	<tr height=4><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=4></td></tr>
					 	<tr height=1><td width=100% bgcolor=#ffffff><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
					 	<tr height=1><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
					</table>
					 ";
		while(list($id, $content, $f_user_id, $dt, $dt_edit)=mysql_fetch_row($sql_res))
		{
			$content=StripSlashes($content);
			$content_show=$content;
			$post_number++;
			$post_number_show="#".$post_number;

//			echo "<textarea rows=20 cols=80>".$content_show."</textarea>";

			$content_show=str_replace('&quot;','"',$content_show);
			$content_show=str_replace('&#039;','"',$content_show);
			$bb = new bbcode($content_show);
//			$bb -> parse($content_show);
//			$bb -> get_html();
			$content_show=$bb -> get_html();
			$content_show=str_replace($conf_smileys_src,$conf_smileys_dest,$content_show);
			$content_show=str_replace('&amp;','&',$content_show);

			if (file_exists($path."files/users/avatar/".$f_user_id.".jpg")) $avatarka_show="<div style='padding:5 0 5 0;'><img src='/files/users/avatar/".$f_user_id.".jpg' border=0></div>";
			elseif (file_exists($path."files/users/avatar/".$f_user_id.".gif")) $avatarka_show="<div style='padding:5 0 5 0;'><img src='/files/users/avatar/".$f_user_id.".gif' border=0></div>";
			else $avatarka_show="";
/*
			$content_show=str_replace("\n","<br>",$content_show);
			$content_show=str_replace($conf_smileys_src,$conf_smileys_dest,$content_show);
			if (substr_count($content_show,"[quote")==substr_count($content_show,"[/quote]"))
			{
				$content_show=str_replace('[quote]', '<table cellpadding=5 cellspacing=0 border=0 width='100%'><tr><td style="border:solid 1px #cccccc;" bgcolor=#f7f7f7>', $content_show);
				$content_show=str_replace('[quote', '<table cellpadding=5 cellspacing=0 border=0 width='100%'><tr><td style="border:solid 1px #cccccc;padding-left:10;" bgcolor=#f7f7f7>', $content_show);
				$content_show=preg_replace('/(?<=>) author\=[^\s]{6}(.*?)[^\s]{6} date\=[^\s]{6}(.*?)[^\s]{6} post\=[^\s]{6}(.*?)[^\s]{5}[^quote]]/', '<div style="color:#888888;padding-bottom:10;"><i><b>\\1 (\\2) <a href="/'.$path_forum.'/post/\\3.html" style="color:#888888;">написал(а)</a>:</b></i></div>', $content_show);
//				$content_show=preg_replace('/(?<=>) author\=[^\s]{6}(.*?)[^\s]{6} date\=[^\s]{6}(.*?)[^\s]{6} post\=[^\s]{6}(.*?)[^\s]{6}\]/', '<div style="color:#888888;padding-bottom:10;"><i><b>\\1 (\\2) <a href="/'.$path_forum.'/post/\\3.html" style="color:#888888;">написал(а)</a>:</b></i></div>', $content_show);
//				$content_show=str_replace('</a>:</b></i></div>]', '</a>:</b></i>', $content_show);
				$content_show=str_replace('[/quote]', '</td></tr></table>', $content_show);

			}
			if (substr_count($content_show,"[i]")==substr_count($content_show,"[/i]")) $content_show=preg_replace('/\[i\](.*?)\[\/i\]/', '<i>\\1</i>', $content_show);
			if (substr_count($content_show,"[b]")==substr_count($content_show,"[/b]")) $content_show=preg_replace('/\[b\](.*?)\[\/b\]/', '<b>\\1</b>', $content_show);
			if (substr_count($content_show,"[img]")==substr_count($content_show,"[/img]")) $content_show=preg_replace('/\[img\](.*?)\[\/img\]/', '<img src="\\1" border=0>', $content_show);
			if (substr_count($content_show,"[url=")==substr_count($content_show,"[/url]")) $content_show=preg_replace('/\[url\=(.*?)\](.*?)\[\/url\]/', '<a href="\\1" target=_blank>\\2</a>', $content_show);
			$content_show=preg_replace("#(?<!=)(?<!\")(?<!\')(https?|ftp)://\S+[^\s.,>)\];'\"!?]#",'<a target=_blank href="\\0">\\0</a>',$content_show);
*/
			$topline_show="
			<table cellpadding=0 cellspacing=0 border=0 width=100% style='padding-bottom:5px;'>
				<tr>
					<td align=left valign=middle>
						<span style='color:#555555;font-size:11px;'><img src='/img/tools/date.gif' alt='Дата' width=8 height=9 border=0> ".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)." &nbsp; ".substr($dt,11,5)."</span>
					</td>
					<td align=right valign=middle>
						<span style='color:#555555;font-size:11px;'><a name='".$id."'></a><a href='#' onclick=\"link_to_post(".$id."); return false;\" style='text-decoration:none;cursor:pointer;'>".$post_number_show."</a></span>
					</td>
				</tr>
			</table>
			<table cellpadding=0 cellspacing=0 border=0 width=100%><tr height=1><td width=100% background='/img/int/dots-hor.gif'><img src=/img/empty.gif border=0 width=1 height=1></td></tr></table>
			";

			$sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$f_user_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res_1)>0)
			{
				list($login)=mysql_fetch_row($sql_res_1);
				$login=StripSlashes($login);
				$login_show="<div><b><a href='/users/".$f_user_id.".html'>".$login."</a></b></div>";
			}

			$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE user_id='".$f_user_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			$num_soob_show="<div><span style='color:#555555;font-size:11px;'>Сообщений: <a href='/".$path_forum."/userposts/?user=".$f_user_id."' style='color:#555555;'><b>".mysql_num_rows($sql_res_1)."</b></a></span></div>";

			$team_name_show="<div><span style='color:#555555;font-size:11px;'>".$team_name."</span></div>";

			if ($user_id>0 && $f_user_id!=$user_id) $tolichka_show="<br><a href='/".$path_lichka."/?action=topic_add&uid=".$f_user_id."' style='color:#555555;font-size:11px;'>Личка</a>"; else $tolichka_show="<br>";


			$dt_15=mktime(substr($dt,11,2), floatval(substr($dt,14,2))+15, substr($dt,17,2), substr($dt,5,2), substr($dt,8,2), substr($dt,0,4));
			$cur_dt=time();
			if (($user_id==$f_user_id && $cur_dt<$dt_15) || $user_forum_status=="admin")
			{
				$edit_pic="&nbsp;<a href='?parent_id=".$forum_topic_id."&id=".$id."&action=post_edit'><img src='/img/forum/edit.png' alt='Редактировать' width=84 height=22 border=0></a>";
				$del_pic="&nbsp;<a href=\"javascript:if(confirm('Сейчас как удалю этот пост!'))window.location='?parent_id=".$forum_topic_id."&id=".$id."&action=post_del'\"><img src='/img/forum/del.png' alt='Удалить' width=84 height=22 border=0></a>";
			}
			else { $edit_pic="";$del_pic=""; }
			if (isset($user_id) && $user_id>0)
			{
				if ($topic_type=="active") $quote_link="<a href='?parent_id=".$forum_topic_id."&quote_id=".$id."&action=post_add'><img src='/img/forum/reply.png' alt='Ответить с цитатой' width=84 height=22 border=0></a>";
				elseif ($topic_type=="closed") $quote_link="";
				else $quote_link="";
			}
			if ($user_forum_status=="admin")
			{
				$check_pic="&nbsp;<input name='check_".$id."' type=checkbox value=Yes>";
			}

			$post_menu="<div align=right>".@$quote_link.@$edit_pic.@$del_pic.@$check_pic."</div>";

			$bottomline_show="
			<br><br><br>
			<table cellpadding=0 cellspacing=0 border=0 width=100%><tr height=1><td width=100% background='/img/int/dots-hor.gif'><img src=/img/empty.gif border=0 width=1 height=1></td></tr></table>
			<table cellpadding=5 cellspacing=0 border=0 width=100% style='padding-bottom:0px;'>
				<tr>
					<td align=left valign=middle>
						 &nbsp;
					</td>
					<td align=right valign=bottom>
						<nobr>".$post_menu."</nobr>
					</td>
				</tr>
			</table>
			";

			$out.="<table cellpadding=0 cellspacing=0 border=0 width=100%>
							<tr height=90 bgcolor='#ffffff'>
								<td width=120 align=left valign=top style='border-left: solid 1px #cccccc;padding:5 5 5 5;'>
									".$login_show."<br>
									".$avatarka_show.$num_soob_show."<br><br>
									<img src='/img/empty.gif' width=140 height=1>
								</td>
								<td width=1 bgcolor='#eeeeee'><img src='/img/empty.gif' width=1></td>
								<td align=left valign=top style='border-right: solid 1px #cccccc;padding:5 5 5 10;'>".$topline_show."<br>".$content_show."<br>".$bottomline_show."</td>
							</tr>
						 </table>
						 <table cellpadding=0 cellspacing=0 border=0 width=100%>
						 	<tr height=5><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=5></td></tr>
						 	<tr height=1><td width=100% bgcolor=#ffffff><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
						 	<tr height=1><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
						</table>
						 ";
		}
		$out.="<a name=lastpost></a>";
//		$out.=$pages_show;
		$out.="<table cellpadding=3 cellspacing=0 border=0 width=100% background='/img/forum/bg-main1-top.gif' style='border: solid 1px #CCCCCC;background-position: top; background-repeat: repeat-x; background-image: /img/forum/bg-main-top.gif;' bgcolor='#ffffff'>
					<tr height=30>
						<td align=left valign=middle>".$pages_show."</td>
						<td align=right valign=middle>&nbsp;</td>
					</tr>
				 </table>";
	}

	if (isset($user_id) && $user_id>0)
	{
		if ($topic_type=="active") $reply_button="<a href='?parent_id=".$forum_topic_id."&action=post_add'><img src='/img/forum/reply.png' alt='Ответить' width=84 height=22 border=0></a>";
		elseif ($topic_type=="closed") $reply_button="";
		else $reply_button="";
		$out.="<br>
						 <table cellpadding=0 cellspacing=0 border=0 width=100%>
						 	<tr>
								<td align=left>
									<div>
										".$reply_button."
										<a href='./?parent_id=".$forum_rub_id[1]."&action=topic_add'><img src='/img/forum/new-topic.png' alt='Новая тема' width=84 height=22 border=0></a>
									</div>
								</td>
								<td align=right>
									<a href='./'><img src='/img/forum/themes.png' alt='К списку тем' width=84 height=22 border=0></a>
									<a href='#top'><img src='/img/forum/up.png' alt='Наверх' width=84 height=22 border=0></a>
								</td>
							</tr>
						</table>";
						
						
		if (@$user_forum_status=="admin") 
		{
			if ($topic_type=="active") $out.="<br><a href='?topic_id=".$forum_topic_id."&action=topic_changeactive&value=closed'>Закрыть тему</a>";
			elseif ($topic_type=="closed") $out.="<br><a href='?topic_id=".$forum_topic_id."&action=topic_changeactive&value=active'>Открыть тему</a>";
		}
		if ($user_forum_status=="admin") $out.="&nbsp;&nbsp;&nbsp;<span onclick='document[\"forum_posts_form\"].submit();' style='text-decoration:underline;cursor:pointer;'>Удалить отмеченные сообщения</span></form>";
	}
	else
	{
		$out.="<br><div>Чтобы оставить сообщение на нашем форуме необходимо <a href='/auth/register/'>зарегистрироваться</a>.</div>";
	}
	return ($out);
}









function forum_post_add()
{
	global $sql_pref, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $forum_topic_id, $forum_topic_name, $path_forum, $page_header1, $path;
	global $user_id;
	$out="";

	if (!isset($user_id) || $user_id<=0) return ($out);

	if (isset($_REQUEST['content']) && !empty($_REQUEST['content']))
	{
		if (isset($_REQUEST['parent_id'])) $parent_id=$_REQUEST['parent_id']; else $parent_id=0;
		if (isset($_REQUEST['content'])) $content=AddSlashes(strip_tags($_REQUEST['content'])); else $content=""; $content=htmlspecialchars($content, ENT_QUOTES);
		$dt=date("Y-m-d H:i:s");

		$sql_query="INSERT INTO ".$sql_pref."_forum_posts (content, user_id, parent_id, dt) VALUES ('".$content."', '".$user_id."', '".$parent_id."', '".$dt."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$post_last_id=mysql_insert_id();

		$sql_query="UPDATE ".$sql_pref."_forum_topics SET dt_lastpost='".$dt."', user_id_lastpost='".$user_id."' WHERE id='".$parent_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
        
        $sql_query2="INSERT INTO ".$sql_pref."_users_action (user_id, action_type, row_id) VALUES ('".$user_id."', 'add_forum_post','".$post_last_id."')";
        //echo $sql_query2;
        $sql_res2=mysql_query($sql_query2, $conn_id); 

		header("location:/".$path_forum."/post/".$post_last_id.".html");
		exit();
	}
	else
	{
		$page_header1="Написать сообщение";
		$content_show="";
		if (isset($_REQUEST['quote_id']) && !empty($_REQUEST['quote_id']))
		{
			$sql_query="SELECT content FROM ".$sql_pref."_forum_posts WHERE id='".$_REQUEST['quote_id']."'";
			$sql_res=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res)>0)
			{
				list($content)=mysql_fetch_row($sql_res);
				$content=StripSlashes($content);
				$content=preg_replace('/\[quote(.*)\[\/quote\]/s', '', $content);
//				$content=preg_replace('/\[quote(.*?)\[\/quote\]/', "", $content);

				$sql_query="SELECT CONCAT_WS(' ', u.name, u.surname), p.dt FROM ".$sql_pref."_users as u, ".$sql_pref."_forum_posts as p WHERE p.user_id=u.id&&p.id='".$_REQUEST['quote_id']."'";
				$sql_res_1=mysql_query($sql_query, $conn_id);
				if (mysql_num_rows($sql_res_1)>0)
				{
					list($login, $dt)=mysql_fetch_row($sql_res_1);
					$login=StripSlashes($login);
				}
				$content=trim($content);
				$content_show="[quote author='".$login."' date='".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)." ".substr($dt,11,5)."' post='".$_REQUEST['quote_id']."']".$content."[/quote]\n";
			}
		}
		if (file_exists($path."inc/forum_smileys.inc")) $smileys=file_get_contents($path."inc/forum_smileys.inc"); else $smileys="";
		$out.="<form action='' method=post name=form_post>
						<input type=hidden name=parent_id value='".$forum_topic_id."'>
						<textarea id=content_bb name=content>".$content_show."</textarea><br>
						<input class=button_submit type=submit value='Опубликовать' name='add'>
					</form>";
	}
	return ($out);
}










function forum_post_edit()
{
	global $sql_pref, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $forum_topic_id, $path_forum, $page_header1, $path, $user_forum_status;
	global $user_id;
	$out="";

	if (!isset($user_id) || $user_id<=0) return ($out);

	if (isset($_REQUEST['content']) && !empty($_REQUEST['content']))
	{
		if (isset($_REQUEST['parent_id'])) $parent_id=$_REQUEST['parent_id']; else $parent_id=0;
		if (isset($_REQUEST['post_id'])) $post_id=$_REQUEST['post_id']; else $post_id=0;
		if (isset($_REQUEST['content'])) $content=AddSlashes(strip_tags($_REQUEST['content'])); else $content=""; $content=htmlspecialchars($content, ENT_QUOTES);
		$dt=date("Y-m-d H:i:s");

		$sql_query="SELECT user_id FROM ".$sql_pref."_forum_posts WHERE id='".$post_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			list($f_user_id)=mysql_fetch_row($sql_res);
			if ($user_id!=$f_user_id && $user_forum_status!="admin") return("Ошибка!");

			$sql_query="UPDATE ".$sql_pref."_forum_posts SET content='".$content."' WHERE id='".$post_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);

			header("location:/".$path_forum."/post/".$post_id.".html");
			exit();
		}
	}
	else
	{
		$page_header1="Редактировать сообщение";
		$post_id=$_REQUEST['id'];
		$topic_id=$_REQUEST['parent_id'];

		$sql_query="SELECT content, user_id FROM ".$sql_pref."_forum_posts WHERE id='".$post_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			list($content, $f_user_id)=mysql_fetch_row($sql_res);
			$content=StripSlashes($content);
			if ($user_id!=$f_user_id && $user_forum_status!="admin") return("Ошибка!");

			if (file_exists($path."inc/forum_smileys.inc")) $smileys=file_get_contents($path."inc/forum_smileys.inc"); else $smileys="";

			$out.="<form action='' method=post name=form_post>
							<input type=hidden name=parent_id value='".$topic_id."'>
							<input type=hidden name=post_id value='".$post_id."'>
							<textarea id=content_bb name=content>".$content."</textarea><br>
							<input class=button_submit type=submit value='Сохранить' name='edit'>
						</form>
			";
		}
	}
	return ($out);
}










function forum_post_del()
{
	global $sql_pref, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $forum_topic_id, $path_forum, $page_header1, $user_forum_status;
	global $user_id;
	$out="";

	if (!isset($user_id) || $user_id<=0) return ($out);

	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		if (isset($_REQUEST['id'])) $post_id=$_REQUEST['id']; else $post_id=0;

		$sql_query="SELECT user_id FROM ".$sql_pref."_forum_posts WHERE id='".$post_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			list($f_user_id)=mysql_fetch_row($sql_res);
			if ($user_id!=$f_user_id && $user_forum_status!="admin") return("Ошибка!");

			$sql_query="DELETE FROM ".$sql_pref."_forum_posts WHERE id='".$post_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
            
            
			$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE parent_id='".$forum_topic_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
            if (mysql_num_rows($sql_res_1)>0)
            {
    			$sql_query="SELECT dt, user_id FROM ".$sql_pref."_forum_posts WHERE parent_id='".$forum_topic_id."' ORDER BY dt DESC";
    			$sql_res_2=mysql_query($sql_query, $conn_id);
    			list($last_dt, $last_user_id)=mysql_fetch_row($sql_res_2);
    
    			$sql_query="UPDATE ".$sql_pref."_forum_topics SET dt_lastpost='".$last_dt."', user_id_lastpost='".$last_user_id."' WHERE id='".$forum_topic_id."'";
    			$sql_res_2=mysql_query($sql_query, $conn_id);
                
    			header("location:/".$path_forum."/".$forum_rub_url[0]."/".$forum_rub_url[1]."/".$forum_topic_id.".html");
    			exit();
            }
            else
            {
    			$sql_query="DELETE FROM ".$sql_pref."_forum_topics WHERE id='".$forum_topic_id."'";
    			$sql_res_2=mysql_query($sql_query, $conn_id);
                
    			header("location:/".$path_forum."/".$forum_rub_url[0]."/".$forum_rub_url[1]."/");
    			exit();
            }


		}
	}
	return ($out);
}








function forum_someposts_del()
{
	global $sql_pref, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $forum_topic_id, $path_forum, $page_header1, $user_forum_status;
	global $user_id;
	$out="";

	if (!isset($user_id) || $user_id<=0) return ($out);
	if ($user_forum_status!="admin") return("Ошибка!");
	
	$topic_id=$_REQUEST['topic_id'];

	$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE parent_id='".$topic_id."'";
	$sql_res_1=mysql_query($sql_query, $conn_id);
	while(list($post_id)=mysql_fetch_row($sql_res_1))
	{
		if (isset($_REQUEST['check_'.$post_id]) && $_REQUEST['check_'.$post_id]=="Yes")
		{
			$sql_query="DELETE FROM ".$sql_pref."_forum_posts WHERE id='".$post_id."'";
			$sql_res_2=mysql_query($sql_query, $conn_id);
		}
	}
		
	$sql_query="SELECT dt, user_id FROM ".$sql_pref."_forum_posts WHERE parent_id='".$forum_topic_id."' ORDER BY dt DESC";
	$sql_res_1=mysql_query($sql_query, $conn_id);
	list($last_dt, $last_user_id)=mysql_fetch_row($sql_res_1);
	
	$sql_query="UPDATE ".$sql_pref."_forum_topics SET dt_lastpost='".$last_dt."', user_id_lastpost='".$last_user_id."' WHERE id='".$forum_topic_id."'";
	$sql_res_1=mysql_query($sql_query, $conn_id);
	
	header("location:/".$path_forum."/".$forum_rub_url[0]."/".$forum_rub_url[1]."/".$forum_topic_id.".html");
	exit();
	
	return ($out);
}









function forum_lastposts($num_lim)
{
	global $sql_pref, $path, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $forum_topic_id, $path_forum, $path_lichka, $posts_perpage, $conf_smileys_src,$conf_smileys_dest, $user_forum_status, $page_header1;
	global $user_id;
	require_once($path."inc/lib/bbcode/bbcode.lib.php");
	$out="";
	$topic_name="Лента последних сообщений на форуме";
	$page_header1.=": ".$topic_name;

	$sql_query="SELECT id, content, user_id, dt, dt_edit, parent_id FROM ".$sql_pref."_forum_posts ORDER BY dt DESC LIMIT 0,".$num_lim."";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="";
		$out.="<br><table cellpadding=3 cellspacing=0 border=0 width=100% background='/img/forum/bg-main1-top.gif' style='border: solid 1px #CCCCCC;background-position: top; background-repeat: repeat-x; background-image: /img/forum/bg-main-top.gif;' bgcolor='#ffffff'>
					<tr height=30>
						<td align=left valign=middle style='color:#000000;font-weight:bold;padding:0 0 0 10;' height=22>&nbsp;&ndash;&nbsp;".$topic_name."</td>
						<td align=right valign=middle>&nbsp;</td>
					</tr>
				 </table>";

					 $out.="<table cellpadding=0 cellspacing=0 border=0 width=100%>
					 	<tr height=1><td width=100% bgcolor=#ffffff><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
					 	<tr height=4><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=4></td></tr>
					 	<tr height=1><td width=100% bgcolor=#ffffff><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
					 	<tr height=1><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
					</table>
					 ";
		while(list($id, $content, $f_user_id, $dt, $dt_edit, $parent_id)=mysql_fetch_row($sql_res))
		{
			$content=StripSlashes($content);
			$content_show=$content;

			$content_show=str_replace('&quot;','"',$content_show);
			$content_show=str_replace('&#039;','"',$content_show);
			$bb = new bbcode($content_show);
			$content_show=$bb -> get_html();
			$content_show=str_replace($conf_smileys_src,$conf_smileys_dest,$content_show);
			$content_show=str_replace('&amp;','&',$content_show);

			if (file_exists($path."files/users/avatar/".$f_user_id.".jpg")) $avatarka_show="<div style='padding:5 0 5 0;'><img src='/files/users/avatar/".$f_user_id.".jpg' border=0></div>";
			elseif (file_exists($path."files/users/avatar/".$f_user_id.".gif")) $avatarka_show="<div style='padding:5 0 5 0;'><img src='/files/users/avatar/".$f_user_id.".gif' border=0></div>";
			else $avatarka_show="";

			$sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$f_user_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res_1)>0)
			{
				list($login)=mysql_fetch_row($sql_res_1);
				$login=StripSlashes($login);
				$login_show="<div><b><a href='/users/".$f_user_id.".html'>".$login."</a></b></div>";
			}

			$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE user_id='".$f_user_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			$num_soob_show="<div><span style='color:#555555;font-size:11px;'>Сообщений: <b>".mysql_num_rows($sql_res_1)."</b></span></div>";

			$team_name_show="<div><span style='color:#555555;font-size:11px;'>".$team_name."</span></div>";


			$sql_query="SELECT type, name, parent_id FROM ".$sql_pref."_forum_topics WHERE id='".$parent_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res_1)==0) return("Ошибка");
			list($topic_type, $topic_name, $parent_rubid)=mysql_fetch_row($sql_res_1);
			$topic_name=StripSlashes($topic_name);

			$topline_show="
			<table cellpadding=0 cellspacing=0 border=0 width=100% style='padding-bottom:5px;'>
				<tr>
					<td align=left valign=middle>
						<span style='color:#555555;font-size:11px;'><img src='/img/tools/date.gif' alt='Дата' width=8 height=9 border=0> ".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)." &nbsp; ".substr($dt,11,5)."</span>
						&nbsp;&nbsp;&nbsp;
						<span style='color:#555555;font-size:11px;'>Тема: <b><a href='/".$path_forum."/post/".$id.".html'>".$topic_name."</a></b></span>
					</td>
					<td align=right valign=middle>&nbsp;</td>
				</tr>
			</table>
			<table cellpadding=0 cellspacing=0 border=0 width=100%><tr height=1><td width=100% background='/img/int/dots-hor.gif'><img src=/img/empty.gif border=0 width=1 height=1></td></tr></table>
			";

			$t_parent_rubid=$parent_rubid;
			$t_url="";
			while ($t_parent_rubid>0)
			{
				$sql_query="SELECT url, parent_id FROM ".$sql_pref."_forum_rubs WHERE id='".$t_parent_rubid."'";
				$sql_res_1=mysql_query($sql_query, $conn_id);
				list($rurl,$t_parent_rubid)=mysql_fetch_row($sql_res_1);
				$t_url=$rurl."/".$t_url;
			}
			if (isset($user_id) && $user_id>0)
			{
				if ($topic_type=="active") $quote_link="<a href='/".$path_forum."/".$t_url.$parent_id.".html?parent_id=".$parent_id."&quote_id=".$id."&action=post_add'><img src='/img/forum/reply.png' alt='Ответить с цитатой' width=84 height=22 border=0></a>";
				elseif ($topic_type=="closed") $quote_link="";
				else $quote_link="";

			}
			$post_menu="<div align=right>".$quote_link."</div>";

			$bottomline_show="
			<br><br><br>
			<table cellpadding=0 cellspacing=0 border=0 width=100%><tr height=1><td width=100% background='/img/int/dots-hor.gif'><img src=/img/empty.gif border=0 width=1 height=1></td></tr></table>
			<table cellpadding=5 cellspacing=0 border=0 width=100% style='padding-bottom:0px;'>
				<tr>
					<td align=left valign=middle>
						&nbsp;
					</td>
					<td align=right valign=bottom>
						<nobr>".$post_menu."</nobr>
					</td>
				</tr>
			</table>
			";



			$dt_15=mktime(substr($dt,11,2), floatval(substr($dt,14,2))+15, substr($dt,17,2), substr($dt,5,2), substr($dt,8,2), substr($dt,0,4));
			$cur_dt=time();
			if (isset($user_id) && $user_id>0) $quote_link="<a href='?parent_id=".$forum_topic_id."&quote_id=".$id."&action=post_add'><img src='/img/forum/reply.png' alt='Ответить с цитатой' width=84 height=22 border=0></a>"; else $quote_link="";

			$out.="<table cellpadding=0 cellspacing=0 border=0 width=100%>
							<tr height=90 bgcolor='#ffffff'>
								<td width=120 align=left valign=top style='border-left: solid 1px #cccccc;padding:5 5 5 5;'>
									".$login_show."<br>
									".$avatarka_show.$num_soob_show.$spec_status_show.$team_name_show."<br><br>
									<img src='/img/empty.gif' width=140 height=1>
								</td>
								<td width=1 bgcolor='#eeeeee'><img src='/img/empty.gif' width=1></td>
								<td align=left valign=top style='border-right: solid 1px #cccccc;padding:5 5 5 10;'>".$topline_show."<br>".$content_show."<br>".$bottomline_show."<br><br></td>
							</tr>
						 </table>
						 <table cellpadding=0 cellspacing=0 border=0 width=100%>
						 	<tr height=5><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=5></td></tr>
						 	<tr height=1><td width=100% bgcolor=#ffffff><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
						 	<tr height=1><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
						</table>
						 ";
		}
	}

	return ($out);
}












function forum_userposts($userposts_id)
{
	global $sql_pref, $path, $conn_id, $forum_rub_id, $forum_rub_url, $forum_rub_name, $forum_topic_id, $path_forum, $path_lichka, $posts_perpage, $conf_smileys_src,$conf_smileys_dest, $user_forum_status, $page_header1;
	global $user_id;
	require_once($path."inc/lib/bbcode/bbcode.lib.php");
	$out="";
	$topic_name="Лента сообщений пользователя";
	$page_header1.=": ".$topic_name;



/*
	$perpage=$posts_perpage;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";
	$pages_show="";
	$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE user_id='".$userposts_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		$pages_show="<div>Страницы: ";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show=$i;
			$pages_show.="<span style='padding:0 3 0 3;background-color:#ffffff;border:solid 1px #aaaaaa;'><a href='?user=".$userposts_id."&page=".$i."' style='text-decoration:none;'>".$i_show."</a></span>&nbsp;";
		}
		$pages_show.="</div>";
	}
	$post_number=($page-1)*$perpage;
*/
	$perpage=$posts_perpage;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";
	$pages_show="";
	$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE user_id='".$userposts_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		$dotsfl=0;
		$pages_show="<div>Страницы: ";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i)
			{
				$i_show="<b>".$i."</b>";
			}
			else
			{
				$i_show=$i;
			}
			if ($i<4 || (($page-$i)<2 && ($page-$i)>=0) || (($i-$page)<2 && ($i-$page)>=0) || ($numpages-$i)<3) { $pages_show.="<span style='padding:0 3 0 3;background-color:#ffffff;border:solid 1px #aaaaaa;'><a href='?user=".$userposts_id."&page=".$i."' style='text-decoration:none;'>".$i_show."</a></span> "; $dotsfl=0; }
			elseif ($dotsfl==0) {$pages_show.=" ... "; $dotsfl=1;}
		}
		$pages_show.="</div>";
	}
	$post_number=($page-1)*$perpage;


	$sql_query="SELECT id, content, user_id, dt, dt_edit, parent_id FROM ".$sql_pref."_forum_posts WHERE user_id='".$userposts_id."' ORDER BY dt DESC".$pref_page."";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="";
		$out.="<br><table cellpadding=3 cellspacing=0 border=0 width=100% background='/img/forum/bg-main1-top.gif' style='border: solid 1px #CCCCCC;background-position: top; background-repeat: repeat-x; background-image: /img/forum/bg-main-top.gif;' bgcolor='#ffffff'>
					<tr height=30>
						<td align=left valign=middle style='color:#000000;font-weight:bold;padding:0 0 0 10;' height=22>&nbsp;&ndash;&nbsp;".$topic_name."</td>
						<td align=right valign=middle>".$pages_show."</td>
					</tr>
				 </table>";

					 $out.="<table cellpadding=0 cellspacing=0 border=0 width=100%>
					 	<tr height=1><td width=100% bgcolor=#ffffff><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
					 	<tr height=4><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=4></td></tr>
					 	<tr height=1><td width=100% bgcolor=#ffffff><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
					 	<tr height=1><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
					</table>
					 ";
		while(list($id, $content, $f_user_id, $dt, $dt_edit, $parent_id)=mysql_fetch_row($sql_res))
		{
			$content=StripSlashes($content);
			$content_show=$content;

			$content_show=str_replace('&quot;','"',$content_show);
			$content_show=str_replace('&#039;','"',$content_show);
			$bb = new bbcode($content_show);
			$content_show=$bb -> get_html();
			$content_show=str_replace($conf_smileys_src,$conf_smileys_dest,$content_show);

			$sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$f_user_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res_1)>0)
			{
				list($login)=mysql_fetch_row($sql_res_1);
				$login=StripSlashes($login);
				$login_show="<div><b><a href='/users/".$f_user_id.".html'>".$login."</a></b></div>";
				if (!empty($spec_status)) $spec_status_show="<div><span style='color:#555555;font-size:11px;'>".$spec_status."</span></div>"; else $spec_status_show="";
			}

			$sql_query="SELECT id FROM ".$sql_pref."_forum_posts WHERE user_id='".$f_user_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			$num_soob_show="<div><span style='color:#555555;font-size:11px;'>Сообщений: <b>".mysql_num_rows($sql_res_1)."</b></span></div>";

			$team_name_show="<div><span style='color:#555555;font-size:11px;'>".$team_name."</span></div>";


			$sql_query="SELECT name FROM ".$sql_pref."_forum_topics WHERE id='".$parent_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res_1)==0) return("Ошибка");
			list($topic_name)=mysql_fetch_row($sql_res_1);
			$topic_name=StripSlashes($topic_name);

			$topline_show="
			<table cellpadding=0 cellspacing=0 border=0 width=100% style='padding-bottom:5px;'>
				<tr>
					<td align=left valign=middle>
						<span style='color:#555555;font-size:11px;'><img src='/img/tools/date.gif' alt='Дата' width=8 height=9 border=0> ".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)." &nbsp; ".substr($dt,11,5)."</span>
						&nbsp;&nbsp;&nbsp;
						<span style='color:#555555;font-size:11px;'>Тема: <b><a href='/".$path_forum."/post/".$id.".html'>".$topic_name."</a></b></span>
					</td>
					<td align=right valign=middle>&nbsp;</td>
				</tr>
			</table>
			<table cellpadding=0 cellspacing=0 border=0 width=100%><tr height=1><td width=100% background='/img/int/dots-hor.gif'><img src=/img/empty.gif border=0 width=1 height=1></td></tr></table>
			";

			$dt_15=mktime(substr($dt,11,2), floatval(substr($dt,14,2))+15, substr($dt,17,2), substr($dt,5,2), substr($dt,8,2), substr($dt,0,4));
			$cur_dt=time();
			if (isset($user_id) && $user_id>0) $quote_link="<a href='?parent_id=".$forum_topic_id."&quote_id=".$id."&action=post_add'><img src='/img/forum/reply.png' alt='Ответить с цитатой' width=84 height=22 border=0></a>"; else $quote_link="";

			$out.="<table cellpadding=0 cellspacing=0 border=0 width=100%>
							<tr height=90 bgcolor='#ffffff'>
								<td width=120 align=left valign=top style='border-left: solid 1px #cccccc;padding:5 5 5 5;'>
									".$login_show."<br>
									".$num_soob_show.$spec_status_show.$team_name_show."<br><br>
									<img src='/img/empty.gif' width=140 height=1>
								</td>
								<td width=1 bgcolor='#eeeeee'><img src='/img/empty.gif' width=1></td>
								<td align=left valign=top style='border-right: solid 1px #cccccc;padding:5 5 5 10;'>".$topline_show."<br>".$content_show."<br><br></td>
							</tr>
						 </table>
						 <table cellpadding=0 cellspacing=0 border=0 width=100%>
						 	<tr height=5><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=5></td></tr>
						 	<tr height=1><td width=100% bgcolor=#ffffff><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
						 	<tr height=1><td width=100% bgcolor=#cccccc><img src=/img/empty.gif border=0 width=1 height=1></td></tr>
						</table>
						 ";
		}
		$out.="<table cellpadding=3 cellspacing=0 border=0 width=100% background='/img/forum/bg-main1-top.gif' style='border: solid 1px #CCCCCC;background-position: top; background-repeat: repeat-x; background-image: /img/forum/bg-main-top.gif;' bgcolor='#ffffff'>
					<tr height=30>
						<td align=left valign=middle>".$pages_show."</td>
						<td align=right valign=middle>&nbsp;</td>
					</tr>
				 </table>";
	}

	return ($out);
}















function forum_nav()
{
	global $sql_pref, $conn_id, $path, $path_forum;
	global $url_decode, $module_name, $module_url;
	global $forum_rub_url, $forum_rub_id, $forum_rub_name, $forum_rub_num;
	global $forum_topic_url, $forum_topic_id, $forum_topic_name;
	global $page_title, $page_header1;
    
	$out="";
    
    $out.="<div style='padding: 5 0 20 0;font-size:11px;'>";
    $out.="<a href='/".$path_forum."/' style='font-size:11px;'>Форум</a>";
	$out.=" &raquo; ";
    
	for ($i=0; $i<$forum_rub_num; $i++)
	{
		$nav_url[$i]=$forum_rub_url[$i];
		$nav_name[$i]=$forum_rub_name[$i];
	}
	if (isset($forum_topic_url) && !empty($forum_topic_url))
	{
		$nav_url[]=$forum_topic_url;
		$nav_name[]=$forum_topic_name;
	}
	$n_url="/".$path_forum."/";
	for ($i=0; $i<(count($nav_name)-1); $i++)
	{
		$n_url.=$nav_url[$i]."/";
		$out.="<a href='".$n_url."' style='font-size:11px;'>".$nav_name[$i]."</a>";
		$out.=" &raquo; ";
	}
	$out.=$nav_name[(count($nav_name)-1)];
    
    $out.="</div>"; 
	return ($out);
}











?>
