<?php

function out_main()
{
	global $page_content, $page_title, $page_header1, $rub_num;
	$out="";
	if ($rub_num>0)
	{
		$out.=out_art();
		$out.=out_rubmenu();
		$out.=out_artmenu();
	}
	return ($out);
}










function out_topmenu()
{
	global $sql_pref, $conn_id, $rub_id, $rub_url, $art_url, $mainpage;
	$out="";
	$sql_query="SELECT id, url, name FROM ".$sql_pref."_pub_rubs WHERE level='1' AND enable='Yes' ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<table cellpadding=3 cellspacing=0 border=0>";
		$out.="<tr>";
		$mainp="Главная";
		if ($mainpage=="Yes") $mainp="<b>".$mainp."</b>";
		$out.="<td align=center><a class=topmenu href='/'>".$mainp."</a></td>";
		while(list($id, $url, $name)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			if ($id==$rub_id[0]) $name_show="<b>".$name."</b>"; else $name_show=$name;
			$out.="<td align=center><a class=topmenu href='/".$url."/'>".$name_show."</a></td>";
		}
		$out.="</tr>";
		$out.="</table>"; 
	}
	return ($out);
}


function out_leftmenu()
{
	global $sql_pref, $conn_id, $rub_id, $path;
	$out="";
    $date=date("Y-m-d");

	$sql_query="SELECT id, url, name FROM ".$sql_pref."_pub_rubs WHERE parent_id='0'&&enable='Yes' ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);

    $sql_query_banners="SELECT id, descr, url, show_start, show_end FROM ".$sql_pref."_banners WHERE enable='Yes'AND zone=2 ORDER BY sort";
   $sql_res_banners=mysql_query($sql_query_banners, $conn_id);

    if (mysql_num_rows($sql_res_banners)>0)
    {
        while ($row=mysql_fetch_row($sql_res_banners, MYSQL_ASSOC))
        {
            $id_b[]= $row["id"];
            $descr_b[]= $row["descr"];
            $url_b[]= $row["url"];
            $show_start[]= $row["show_start"];
            $show_end[] = $row["show_end"];
        }
    }

    $total = count($id_b);
    $i=0;

    if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $url, $name)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
            $name_show=$name;
        /*
            $out.="<table style='padding:20px 0px 0px 20px;' cellspacing=0 border=0 width=100% height=19>";
			$out.="<tr><td align=left valign=top style='padding:0px 5px 0px 2px;'>
                        <table cellpadding=0 cellspacing=0 border=0 width=100%>
                            <tr>
                                <!--td width=16 align=center valign=top style='padding-top:8px;'><img src='/img/int/bul-leftmenu.gif' border=0 width=5 height=3></td-->
                                <td align=left valign=top style='padding-top:2px;'><h3><a class=leftmenu href='/".$url."/'>".$name_show."</a></h3></td>
                            </tr>
                        </table>
                   </td></tr>";
            $out.="</table>";
            */
            $out.="<h3 style='padding:20 10 0 20;'><a class=leftmenu href='/".$url."/'>".$name_show."</a></h3>";


            if ($i!=$total)

            {

                if ($show_start[$i]<$date&&$show_end[$i]>$date) {

                    if ($id_b[$i]!=="" and file_exists($path."files/banners/imgs/".$id_b[$i].".jpg"))
                        $img="<img src='/files/banners/imgs/".$id_b[$i].".jpg' width='226'  alt='".$descr_b[$i]."' >";
                    else $img="<img src='/files/banners/not_found_ban.png' width='200'>";

                    $out_ban="<div style='padding: 0px 5px 0px 0px;' align='left'><a href='$url_b[$i]'>".$img."</a></div>";

                    $out.=$out_ban;


                }

                $i++;

            }





            //$out.="<table cellpadding=0 cellspacing=0 border=0 width=100% height=1 background='/img/dots-hor.gif'><tr><td><img src='/img/empty.gif' border=0 width=1 height=1></td></tr></table>";

        	$sql_query="SELECT id, url, name FROM ".$sql_pref."_pub_rubs WHERE parent_id='".$id."'&&enable='Yes' ORDER BY code";
        	$sql_res_1=mysql_query($sql_query, $conn_id);
        	if (mysql_num_rows($sql_res_1)>0)
        	{
                $out.="<div style='padding: 0 5 0 20;'><table cellspacing=0 cellpadding=0 border=0>";
        		while(list($id1, $url1, $name1)=mysql_fetch_row($sql_res_1))
        		{
        			$name1=stripslashes($name1);
                    $name1_show=$name1;
					$inactive_rubs=array (13,28,21,24,26,17,18);
        			if (in_array($id1,$inactive_rubs)) $name1_show=$name1_show;
        			if ($id1==@$rub_id[1]) $name_class="leftmenu1active"; else $name_class="leftmenu1";
        			$out.="<tr>
                                <td valign=top style='padding: 5 0 6 0;'><img src='/img_new/int/left_bul.gif' border=0 width=4 height=6></td>
                                <td align=left valign=top style='padding: 0 5 6 5;'><a class=".$name_class." href='/".$url."/".$url1."/'>".$name1_show."</a></td>
                            </tr>";
        		}
                $out.="</table></div>";
        	}
		}
	}
	return ($out);
}













function out_art()
{
	global $sql_pref, $conn_id, $path, $path_www, $page_adv_content;
	global $rub_num, $rub_id, $rub_name, $rub_url, $rub_module, $module_path, $url_decode;
	global $art_id;
	global $page_title, $page_description, $page_keywords, $page_header1;
	$out="";
	
    if (isset($rub_id) && !empty($rub_id))
	{
		$sql_query="SELECT adv_content FROM ".$sql_pref."_pub_rubs WHERE id='".$rub_id[1]."'";
		//echo $sql_query;
        $sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			list($content)=mysql_fetch_row($sql_res);
		    $content=stripslashes($content);
			$page_adv_content=$content;
		}
	}
	
	if (isset($art_id) && !empty($art_id))
	{
		$sql_query="SELECT name,url,content,title FROM ".$sql_pref."_pub_arts WHERE id='".$art_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			list($name,$url,$content,$title)=mysql_fetch_row($sql_res);
			$name=stripslashes($name); $content=stripslashes($content); $title=stripslashes($title); 
			
			$out.=$content;
			
			$adescription=substr(strip_tags($content), 0, 150);
			if (isset($name) && !empty($name)) $page_header1=$name;
            
			if (isset($title) && !empty($title)) $atitle=$title;
            elseif (isset($name) && !empty($name)) $atitle=$name;
		}
	}
	
	if (!isset($atitle) || empty($atitle)) $page_title=$page_keywords=$rub_name[($rub_num-1)]; else $page_title=$page_keywords=$atitle;
	if (!isset($page_header1) || empty($page_header1)) $page_header1=$rub_name[($rub_num-1)];
	if (!isset($adescription) || empty($adescription)) $page_description=$rub_name[($rub_num-1)]; else $page_description=$adescription;

	return ($out);
}







function out_art_attention()
{
    global $sql_pref, $conn_id, $path, $path_www;
	global $rub_num, $rub_id, $rub_name, $rub_url, $rub_module, $module_path, $url_decode;
	global $art_id;
	global $page_title, $page_description, $page_keywords, $page_header1;
	$out="";
	
 	//$sql_query="SELECT name, content, title FROM ".$sql_pref."_pub_arts WHERE id='".$id."'";
 	//$sql_res=mysql_query($sql_query, $conn_id);
 	//if (mysql_num_rows($sql_res)>0)
	//{
 	//	list($name, $content, $title)=mysql_fetch_row($sql_res);
 	//	$name=stripslashes($name); $content=stripslashes($content); $title=stripslashes($title);
		
    //$out.=" <table border='0'>
    //            <tr>
    //                <td><img src='/img/happy_new_year.jpg' border='0'></td><td>Уважаемые коллеги, администрация сайта поздравляет Вас с наступающим 2011 годом и желает Вам в Новом Году здоровья, профессиональных успехов и достижения поставленных целей.</td>
    //            </tr> 
    //            <tr>
    //                <td>&nbsp;</td>
    //            </tr>               
    //        </table><hr><br/>";
	//}

	return ($out);
}


function out_art_main($id)
{
	global $sql_pref, $conn_id, $path, $path_www;
	global $rub_num, $rub_id, $rub_name, $rub_url, $rub_module, $module_path, $url_decode;
	global $art_id;
	global $page_title, $page_description, $page_keywords, $page_header1;
	$out="";
	
 	$sql_query="SELECT name, content, title FROM ".$sql_pref."_pub_arts WHERE id='".$id."'";
 	$sql_res=mysql_query($sql_query, $conn_id);
 	if (mysql_num_rows($sql_res)>0)
	{
 		list($name, $content, $title)=mysql_fetch_row($sql_res);
 		$name=stripslashes($name); $content=stripslashes($content); $title=stripslashes($title);
		
        $out.=$content;
	}

	return ($out);
}


function out_art_maintitle()
{
	global $sql_pref, $conn_id, $path, $path_www;
	global $page_title, $page_description, $page_keywords, $page_header1;
	$out="";
	
 	$sql_query="SELECT name, title FROM ".$sql_pref."_pub_arts WHERE id='1'";
 	$sql_res=mysql_query($sql_query, $conn_id);
 	if (mysql_num_rows($sql_res)>0)
	{
 		list($name, $title)=mysql_fetch_row($sql_res);
 		$name=stripslashes($name); $title=stripslashes($title);
        
        if (!empty($name)) $page_title=$page_header1=$name;
        if (!empty($title)) $page_title=$title;
	}
}










function out_rubmenu()
{
	global $sql_pref, $conn_id;
	global $rub_id, $rub_num;
	global $art_id;
	$out="";
	$sql_query="SELECT id, url, name FROM ".$sql_pref."_pub_rubs WHERE parent_id='".$rub_id[($rub_num-1)]."' AND enable='Yes' ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<div style='padding:20 0;margin:30 0;border-bottom:solid 1px #999;border-top:solid 1px #999;'>";
		while(list($id, $url, $name)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			$url=stripslashes($url);
			$out.="<li>";
			if ($id!=$rub_id) $out.="<a href='".$url."/'>".$name."</a>"; else $out.=$name;
			$out.="</li>";
		}
		$out.="</div>";
	}
	return ($out);
}










function out_artmenu()
{
	global $sql_pref, $conn_id;
	global $rub_num, $rub_id;
	global $art_id;
	$out="";
	$sql_query="SELECT id, url, name, descr, code FROM ".$sql_pref."_pub_arts WHERE enable='Yes' AND parent_id='".$rub_id[($rub_num-1)]."' AND code<>'1' ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if(mysql_num_rows($sql_res)>0)
	{
		$out.="<br><hr size=1><br>";
		while(list($id, $url, $name)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			if ($id==$art_id) $name="<b>".$name."</b>";
			else $name="<a href='".$url.".html'>".$name."</a>";
			$out.="<li>".$name."</li>";
		}
	}
	return($out);
}














function out_404()
{
	global $path, $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
	$module_name[0]="Ошибка 404";	$module_url[0]="404";
	if (file_exists($path."inc/404.inc")) $fdata=file_get_contents($path."inc/404.inc");
	$page_title=$page_header1="Несуществующая страница";
	$out.=$fdata;
	return ($out);
}










function out_regonly()
{
	global $path, $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
	$module_name[0]="Нет доступа";	$module_url[0]="regonly";
	if (file_exists($path."inc/regonly.inc")) $fdata=file_get_contents($path."inc/regonly.inc");
	$page_title=$page_header1="Нет доступа";
	$out.=$fdata;
	return ($out);
}










function out_map()
{
	global $sql_pref, $conn_id;
	global $out;
	global $module_name, $module_url;
	global $page_title, $page_header1;
	$module_name[0]="Карта сайта";	$module_url[0]="map";
	$page_title=$page_header1="Карта сайта";
	$out="";
	$out.=out_map_sub(0,"/");
	return ($out);
}
function out_map_sub($par_id, $t_url)
{
	global $sql_pref, $conn_id;
	$out="";
	$sql_query="SELECT id, url, name, parent_id, level FROM ".$sql_pref."_pub_rubs WHERE parent_id='".$par_id."' AND enable='Yes' ORDER BY code";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $url, $name, $parent_id, $level)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			if ($level==1) $name="<b>".$name."</b>";
			$out.="<li style='margin-left:".(($level-1)*20)."px'><a href='".$t_url.$url."/'>".$name."</a><br>";
			
			$sql_query="SELECT url, name FROM ".$sql_pref."_pub_arts WHERE parent_id='".$id."' AND enable='Yes' AND code!='1' ORDER BY code";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			if (mysql_num_rows($sql_res_1)>0)
			{
				while(list($urla, $namea)=mysql_fetch_row($sql_res_1))
				{
					$namea=stripslashes($namea);
					$out.="<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&ndash;&nbsp;<a href='".$t_url.$url."/".$urla.".html'>".$namea."</a>";
				}
			}
			$out.="<br><br></li>";
			$out.=out_map_sub($id, $t_url.$url."/");
		}
	}
	return ($out);
}










function out_nav()
{
	global $sql_pref, $conn_id;
	global $rub_num, $rub_name, $rub_url, $rub_module;
	global $art, $art_name, $art_url;
	global $module_name, $module_url;
	$out_nav="<a class=nav href='/'>Главная</a>";
	$out_nav.=" &raquo; ";
	for ($i=0; $i<$rub_num; $i++)
	{
		$nav_url[$i]=$rub_url[$i];
		$nav_name[$i]=$rub_name[$i];
	}
	if (isset($art) && $art=='Yes' && !isset($module_url))
	{
		$nav_url[count($nav_name)]=$art_url;
		$nav_name[count($nav_name)]=$art_name;
	}
	if (isset($rub_module))
	{
		$kol=count(@$nav_name);
		for ($i=$kol; $i<($kol+count($module_name)); $i++)
		{
			$nav_url[$i]=$module_url[($i-$kol)];
			$nav_name[$i]=$module_name[($i-$kol)];
		}
	}
	$n_url="/";
	for ($i=0; $i<(count($nav_name)-1); $i++)
	{
		$n_url.=$nav_url[$i]."/";
		$out_nav.="<a class=nav href='".$n_url."'>".$nav_name[$i]."</a>";
		$out_nav.=" &raquo; ";
	}
	$out_nav.=$nav_name[(count($nav_name)-1)];
	return ($out_nav);
}








function out_main_articles($count)
{
	global $sql_pref, $conn_id;
	global $path_articles;
	$out="";
    $add_link="<table><tr><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=art_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><img width=24px src='/img/add.png' border=0></td><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=art_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><font style='vertical-align: inherit;'>Добавьте свою статью</font></td><td width=80></td><td><a href='http://www.ensor.ru/rabotodateljam/'><img width=24px src='/img/question_big.png' border=0></a></td><td><a href='http://www.ensor.ru/rabotodateljam/'><font style='vertical-align: inherit;'>Узнайте про дополнительные возможности!</font></a></td></tr></table>";
    
	$sql_query="SELECT id, name, url, dt, descr, views FROM ".$sql_pref."_articles WHERE enable='Yes' ORDER BY dt DESC LIMIT 0,".$count." ";
	$sql_res=mysql_query($sql_query, $conn_id);
	if(mysql_num_rows($sql_res)>0)
	{	    
		$out.="<table cellpadding=0 cellspacing=0 border=0 width='100%' background='/img_new/int/main-bg.gif'>
                <tr>
                    <td valign=top background='/img_new/int/main-top.gif' style='background-image: /img_new/int/main-top.gif; background-position: top; background-repeat: repeat-x;'>
                        <div style='padding: 0 10 10 10;'>
                        <h2>Статьи об энергетике</h2>";        
        while(list($id, $name, $url, $dt, $descr, $views)=mysql_fetch_row($sql_res))
		{
		    $views_show="";
            if($views>0)
            {
                $views_show="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Просмотров: ".$views."</span>";
            }
            $comments_show="";
            $sql_query2="SELECT id FROM ".$sql_pref."_articles_comments WHERE parent_id='".$id."'";
        	$sql_res_2=mysql_query($sql_query2, $conn_id);
        	if (mysql_num_rows($sql_res_2)>0)
        	{
                $comments_show.="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Комментариев: ".mysql_num_rows($sql_res_2)."</span>";
            }
            
			$name=stripslashes($name);$descr=stripslashes($descr);
			$dt_show="<span class=dates>".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)."</span>";
			
			$out.=$dt_show."&nbsp;<a href='/".$path_articles."/".$url.".html' style='text-decoration:underline;'>".$name."</a>".$comments_show.$views_show."<br><br>";
		}
        $out.="                
                        <div><a href='/".$path_articles."/'>Все статьи</a></div></div><br/>".$add_link."                        
                    </td>
                </tr>
                <tr height=10>
                    <td valign=top><img src='/img_new/int/main-bottom.gif' border=0 width=676 height=10 /></td>
                </tr>
            </table>";
	}
	return($out);
}

function users_faces()
{
	global $sql_pref, $conn_id, $path, $path_users;
	$out="";
    
	$sql_query="SELECT id, surname, name, name2, email, dt_birth, dt_reg FROM ".$sql_pref."_users WHERE enable='Yes' AND id<>2 ORDER BY RAND() LIMIT 0,12";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$num_users=mysql_num_rows($sql_res);
		$out.="<table cellpadding=5 cellspacing=0 border=0><thead>12 произвольно выбранных пользователей сайта</thead>"; //<tr ><td align='center' colspan=3><b>Пользователи сайта</b></td></tr>";
        $k=1;
		while(list($id, $surname, $name, $name2, $email, $dt_birth, $dt_reg)=mysql_fetch_row($sql_res))
		{
                      
    		$surname=stripslashes($surname); $name=stripslashes($name); $name2=stripslashes($name2); $phone_work=stripslashes($phone_work); $phone_home=stripslashes($phone_home); $phone_mobile=stripslashes($phone_mobile); $doljnost=stripslashes($doljnost); $expirience=stripslashes($expirience); $vuz=stripslashes($vuz); $specialnost=stripslashes($specialnost);
    		$dt_reg_show=date('d.m.Y',strtotime($dt_reg));
                       
            
            $name_show=$surname." ".$name." ".$name2;
            
            $img_show="<font style='font-size: smaller;'>".$surname."<BR/>".$name."<BR/>".$name2."</font>";
            if (file_exists($path."files/users/img/".$id.".jpg")) $img_show="<div style='padding:0 0; text-align: center'><img width='36px' align=middle src='/files/users/img/".$id.".jpg' border=0></div>"; else $img_show="<div style='padding:0 0; text-align: center'><img width='36px' align='middle' src='/img/nofotouser.jpg' border=0></div>";
            //echo $path;

            if($k==1 || $k==7) $out.="<tr>";  
            $out.="<td align=center valign=middle><a title='".$name_show.". Дата регистрации: ".$dt_reg_show.".' href='/discussions/our_contacts/".$id.".html'>".$img_show."<BR>".$surname." ".substr($name,0,1).". ".substr($name2,0,1).".</a></td>";                
            if($k==6 || $k==12) $out.="</tr>";
			
            $k=$k+1;

		}
		$out.="</table>";
	}
	return ($out);
}





function out_main_blogposts($cnt)
{
	global $sql_pref, $conn_id, $path, $path_blogs, $page_header1;
	global $blogs_posts_perpage, $conf_smileys_src, $conf_smileys_dest;
	global $user_id, $path_users;
	$out="";
	

	$sql_query="SELECT id, name, user_id, dt, url, views FROM ".$sql_pref."_blogs2_posts WHERE visible='Yes' ORDER BY dt DESC LIMIT ".$cnt;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
	    $out.="<table cellpadding=0 cellspacing=0 border=0 width='100%' background='/img_new/int/main-bg.gif'>
                 <tr>
                    <td valign=top background='/img_new/int/main-top.gif' style='background-image: /img_new/int/main-top.gif; background-position: top; background-repeat: repeat-x;'>
                        <div style='padding: 0 10 10 10;'>
                        <h2 style='padding: 0 0 10 0;'>Записи в блогах</h2>
                        ";        
		while(list($id, $name, $author_id, $dt, $url, $views)=mysql_fetch_row($sql_res))
		{
		    $views_show="";
            if($views>0)
            {
                $views_show="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Просмотров: ".$views."</span>";
            }
			$name=stripslashes($name);

			$sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$author_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			list($author_name)=mysql_fetch_row($sql_res_1);
			$author_name=stripslashes($author_name);
            $dt_show="<span class=dates>".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)."</span>";
			
			$comments_num=0;
			$sql_query="SELECT id FROM ".$sql_pref."_blogs2_comments WHERE parent_id='".$id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			$comments_num=mysql_num_rows($sql_res_1);
			if ($comments_num>0) $comments_show="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Комментариев: ".$comments_num."</span>"; else $comments_show="";
			
			$out.=$dt_show."&nbsp;<a href='/".$path_blogs."/".$author_id."/".$url.".html' style='text-decoration:underline;'>".$name."</a>"." ".$comments_show." ".$views_show."<br><br>";
			
        }
        $out.="<div><a href='/".$path_blogs."/'>Все посты</a></div>";
        $out.="                
                        </div>
                    </td>
                </tr>
                <tr height=10>
                    <td valign=top><img src='/img_new/int/main-bottom.gif' border=0 width=676 height=10 /></td>
                </tr>
            </table>";	    	
	}
	
	return ($out);
}

function out_picture()
{
    global $conn_id, $sql_pref, $path_picture, $path;
    
    $sql_query="SELECT ".$sql_pref."_picture.id, ".$sql_pref."_picture.parent_id, ".$sql_pref."_picture.descr, ".$sql_pref."_picture.file_name, ".$sql_pref."_picture_rub.name FROM ".$sql_pref."_picture INNER JOIN ".$sql_pref."_picture_rub ON (".$sql_pref."_picture.parent_id=".$sql_pref."_picture_rub.id) WHERE ".$sql_pref."_picture.enable='Yes' AND ".$sql_pref."_picture.moderation='No'";
	//echo $sql_query;
    $sql_res=mysql_query($sql_query, $conn_id);
	$count=mysql_num_rows($sql_res);
	$seek=rand(1, $count);
	mysql_data_seek ($sql_res, $seek-1);
	list($id, $parent_id, $descr, $file_name, $rub_name)=mysql_fetch_row($sql_res);
	
	$descr=stripslashes($descr);
	if ($file_name!=="" and file_exists($path."files/picture/tn_".$file_name)) 
	$img="<img src='/files/picture/tn_".$file_name."' width='200'  alt='".$descr."' border='1'>";
	else $img="<img src='/files/picture/not_found_pic.png' width='200'>";

	$out_pic="<h3 style='padding: 10px 10px 0px 10px;'><a href='/".$path_picture."/' class='left_menu_H3'>Энергетика в картинках</a></h3>";
	$out_pic.="<h3 style='padding: 10px 10px 0px 20px;'><a href='/".$path_picture."/".$parent_id.".html' class='leftmenu'>".$rub_name."</a></h3>";
	$out_pic.="<div style='padding: 5px 30px 0px 10px;' align='center'><a href='/".$path_picture."?id=".$id."'>".$img."</a></div>";
	$out_pic.="<div style='padding: 5px 30px 0px 10px;' align='center'><a href='/".$path_picture."?id=".$id."' class='picture_album'>".$descr."</a></div>";
	
	
	
	$comments_num=0;
	$sql_query="SELECT id FROM ".$sql_pref."_picture_comments WHERE parent_id='".$id."'";
	$sql_res_1=mysql_query($sql_query, $conn_id);
	$comments_num=mysql_num_rows($sql_res_1);
	if ($comments_num>0) $comments_show="<span style='font-size:11px;color:#777777;padding:0 5 0 5;'>Комментариев: ".$comments_num."</span>"; else $comments_show="";
			
	$out_pic.="<div style='padding: 5px 30px 0px 10px;'>".$comments_show."</div>";
	
	
	
	
	
    return $out_pic;    
}


function out_banner_zone1()
{
    global $conn_id, $sql_pref, $path_banners, $path;
    $date=date("Y-m-d");

    $sql_query="SELECT id, descr, url, show_start, show_end FROM ".$sql_pref."_banners WHERE enable='Yes'AND zone=1 ORDER BY sort";
    $sql_res=mysql_query($sql_query, $conn_id);

    if (mysql_num_rows($sql_res)>0)
    {
        while (list($id, $descr, $url, $show_start, $show_end)=mysql_fetch_row($sql_res))
        {
            if ($show_start<$date&&$show_end>$date) {

            if ($id!=="" and file_exists($path."files/banners/imgs/".$id.".jpg"))
                $img="<img src='/files/banners/imgs/".$id.".jpg' width='960' height='60' alt='".$descr."' >";
            else $img="<img src='/files/banners/not_found_ban.png' width='200'>";

            $out_ban="<div style='padding: 5px 0px 0px 0px;' align='center'><a href='$url'>".$img."</a></div>";

        }

        }
    }
    return ($out_ban);

}


function out_banner_zone2()
{
    global $conn_id, $sql_pref, $path_banners, $path;
    $date=date("Y-m-d");
    $sql_query="SELECT id, descr, url, show_start, show_end FROM ".$sql_pref."_banners WHERE enable='Yes'AND zone=2 ORDER BY sort";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
        while (list($id, $descr, $url, $show_start, $show_end)=mysql_fetch_row($sql_res))
        {
            if ($show_start<$date&&$show_end>$date) {

                if ($id!=="" and file_exists($path."files/banners/imgs/".$id.".jpg"))
                    $img="<img src='/files/banners/imgs/".$id.".jpg' width='224'  alt='".$descr."' >";
                else $img="<img src='/files/banners/not_found_ban.png' width='200'>";

                $out_ban="<div style='padding: 0px 5px 0px 0px;' align='left'><a href='$url'>".$img."</a></div>";

            }

        }
    }
    return ($out_ban);

}

function out_banner_zone3()
{
    global $conn_id, $sql_pref, $path_banners, $path;
    $date=date("Y-m-d");

    $sql_query="SELECT id, descr, url, show_start, show_end FROM ".$sql_pref."_banners WHERE enable='Yes'AND zone=3 ORDER BY sort";
    $sql_res=mysql_query($sql_query, $conn_id);

    if (mysql_num_rows($sql_res)>0)
    {
        while (list($id, $descr, $url, $show_start, $show_end)=mysql_fetch_row($sql_res))
        {
            if ($show_start<$date&&$show_end>$date) {

                if ($id!=="" and file_exists($path."files/banners/imgs/".$id.".jpg"))
                    $img="<img src='/files/banners/imgs/".$id.".jpg' width='460'  height='60' alt='".$descr."' >";
                else $img="<img src='/files/banners/not_found_ban.png' width='200'>";

                $out_ban="<div style='padding: 5px 0px 0px 0px;' align='center'><a href='$url'>".$img."</a></div>";

            }

        }
    }
    return ($out_ban);

}


?>
