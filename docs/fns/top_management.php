<?php


function top_management_main()
{
	global $sql_pref, $conn_id, $art_url, $user_id;
	$out="";
	
	if (!isset($art_url)) $out.=top_management_list();
	else $out.=top_management_out();
	
	return ($out);
}










function top_management_list()
{
    global $user_id, $sql_pref, $conn_id, $path, $path_top_management, $path_companies, $user_status, $top_management_perpage;
    $out="";
    
    
    if (isset($_REQUEST['letter']) && !empty($_REQUEST['letter'])) $cur_letter=$_REQUEST['letter'];
    if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) $cur_search=mysql_escape_string($_REQUEST['search']);
    
    if (!isset($cur_search))
    {
        if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
        $pref_page=" LIMIT ".(($page-1)*$top_management_perpage).",".$top_management_perpage."";
        //echo $pref_page;
    }
    
    if (isset($cur_letter) && !empty($cur_letter))
    {
        if (ord($cur_letter)>=ord('А') && ord($cur_letter)<=ord('Я')) $add_sql_list[]="SUBSTRING(c.surname,1,1)='".rawurldecode($cur_letter)."'";
        elseif (ord($cur_letter)>=ord('A') && ord($cur_letter)<=ord('Z')) $add_sql_list[]="SUBSTRING(c.surname,1,1)='".rawurldecode($cur_letter)."'";
    } 
    if (isset($cur_search) && !empty($cur_search)) 
    {
        $add_sql_list[]="c.surname LIKE '%".$cur_search."%'";
    }
    
	$sql_query="SELECT SUBSTRING(surname,1,1) FROM ".$sql_pref."_top_management WHERE enable='Yes' GROUP BY SUBSTRING(surname,1,1)";
	$sql_res=mysql_query($sql_query, $conn_id);
	$letters_ru_array=mysql_fetch_array($sql_res);
    while(list($letters_ru_array[])=mysql_fetch_row($sql_res));
    
    
	$letters_ru_filter="";
	$letters_ru_filter.="<div align='left' style='padding: 0 0 0 0;'><table cellpadding=0 cellspacing=0 border=0 height=34><tr>";
    for ($i=ord('А'); $i<=ord('Я'); $i++)
    {
        $letter=chr($i);
        if (isset($cur_letter) && rawurldecode($cur_letter)==$letter) $letter_show="<a class=letters href='./'><span style='font-weight:bold;font-size:24px;'>".$letter."</span></a>";
        elseif (in_array($letter,$letters_ru_array)) $letter_show="<a class=letters href='?letter=".rawurlencode($letter)."'><span style='font-weight:normal;'>".$letter."</span></a>";
        else $letter_show="<a class=letters href='?letter=".rawurlencode($letter)."'><span style='font-weight:normal;color:#aaa;'>".$letter."</span></a>";
                
        $letters_ru_filter.="<td align='center' valign='middle' width=22><nobr>".$letter_show."</nobr></td>";
    }
	$letters_ru_filter.="</tr></table></div>";
    
    
    
    
	$sql_query="SELECT SUBSTRING(surname,1,1) FROM ".$sql_pref."_top_management WHERE enable='Yes' GROUP BY SUBSTRING(surname,1,1)";
	$sql_res=mysql_query($sql_query, $conn_id);
	$letters_en_array=mysql_fetch_array($sql_res);
    while(list($letters_en_array[])=mysql_fetch_row($sql_res));
    
    
	$letters_en_filter="";
	$letters_en_filter.="<div align='left' style='padding: 0 0 0 0;'><table cellpadding=0 cellspacing=0 border=0 height=34><tr>";
    for ($i=ord('A'); $i<=ord('Z'); $i++)
    {
        $letter=chr($i);
        if (isset($cur_letter) && rawurldecode($cur_letter)==$letter) $letter_show="<a class=letters href='./'><span style='font-weight:bold;font-size:24px;'>".$letter."</span></a>";
        elseif (in_array($letter,$letters_en_array)) $letter_show="<a class=letters href='?letter=".rawurlencode($letter)."'><span style='font-weight:normal;'>".$letter."</span></a>";
        else $letter_show="<a class=letters href='?letter=".rawurlencode($letter)."'><span style='font-weight:normal;color:#aaa;'>".$letter."</span></a>";
                
        $letters_en_filter.="<td align='center' valign='middle' width=22><nobr>".$letter_show."</nobr></td>";
    }
	$letters_en_filter.="</tr></table></div>";
    
    if (!empty($cur_search) || !empty($cur_letter)) $filname=""; else $filname="";
    
    $form_name_show="<div id='divfiltername' style='padding: 5 0 10 0;display:".$filname.";border:solid 1px #bbb;background-color:#f6f6f6;'>
                        ".$letters_ru_filter.$letters_en_filter."
                        <form action='' method=get name=filter_name_form style='padding: 0;margin: 0;'>
                            <div align=center style='padding: 10 0 0 10;font-weight:bold;'>Поиск по фамилии:</div>
                            <div align=center style='padding: 5 0 10 10;'><input type=text name=search value='".$_REQUEST['search']."' style='width:400px;font-size:14px;'></div>
                            <div align=center style='padding: 10 0 0 0;'><input type=submit value='Найти' style='font-size: 14px; width:150px; background-color: #fff; color: #555555; border: 1px #555555 solid;'></div>
                        </form>
                    </div>
                   </div>";
    
    $out.=$form_name_show."<BR>";
    
    $add_sql_list[]="c.enable='Yes'";
    $add_sql=implode("&&",$add_sql_list);
    
    
    if($cur_letter!="")
    {
       	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
    	$perpage=$top_management_perpage; $first=$perpage*($page-1);
    	$sql_query="SELECT c.id FROM ".$sql_pref."_top_management AS c".$as_sa_sql.$as_da_sql." WHERE ".$add_sql;
        //echo $sql_query;
        $args="&letter=".$_REQUEST['letter'];
    	$pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums_with_args($page, $perpage, $sql_query,$args)."</div>";
        $first=$perpage*($page-1);
        //$pref_page
    }
    
   
    if($user_id==0.5)
    {
        $out=top_management_faces();
        $out.="<br><div>Полная функциональность телефонной книги доступна только <a href='/auth/register/'>зарегистрированным</a> пользователям </div>";
    }
    else
    {
        //if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	    //$pref_page=" LIMIT ".(($page-1)*$top_management_perpage).",".$top_management_perpage."";
        
        $sql_query="SELECT c.id, c.surname, c.name, c.name2, c.email, c.phone_work, c.pol, c.dt_birth, c.company_id, c.doljnost, c.expirience, c.vuz, c.specialnost FROM ".$sql_pref."_top_management AS c".$as_sa_sql.$as_da_sql." WHERE ".$add_sql." ".$show_today." ORDER BY c.surname ".@$pref_page;
        //echo $sql_query;
        $sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
    	{
    		$num_top_management=mysql_num_rows($sql_res);
            $out.="<table width=95% cellpadding=5 cellspacing=0 border=0>";
    		$out.="
    			<tr><td colspan=3 style='border-bottom:solid 1px #777777;'>&nbsp;</td></tr>
    			<tr bgcolor='#f2f2f2'>
    				<td width=100 align=left style='border-bottom:solid 1px #777777;'>ФИО</td>
                    <td width=100 align=left valign=top style='border-bottom:solid 1px #777777;'>Компания <BR/> [Должность]</td>
    				<td width=100 align=center valign=top style='border-bottom:solid 1px #777777;'>Фото</td></tr>";
            
    		while(list($id, $surname, $name, $name2, $email, $phone_work, $pol, $dt_birth, $company_id, $doljnost, $expirience, $vuz, $specialnost)=mysql_fetch_row($sql_res))
    		{
        		$surname=stripslashes($surname); $name=stripslashes($name); $name2=stripslashes($name2); $phone_work=stripslashes($phone_work); $phone_home=stripslashes($phone_home); $phone_mobile=stripslashes($phone_mobile); $doljnost=stripslashes($doljnost); $expirience=stripslashes($expirience); $vuz=stripslashes($vuz); $specialnost=stripslashes($specialnost);
        		$dt_reg_show=date('d.m.Y',strtotime($dt_reg));
                
                $name_show=$surname." ".$name." ".$name2;
                
                if (file_exists($path."files/top_management/img/".$id.".jpg"))
		        {
		              $img_show="<div style='padding:10 0;'><img src='/files/top_management/avatar/".$id.".jpg' border=0></div>";
                }
                else
                {
                      $img_show="";
                }
                
                $company_name="-";
        		$sql_query="SELECT name FROM ".$sql_pref."_companies WHERE id='".$company_id."'";
        		$sql_res_1=mysql_query($sql_query, $conn_id);
        		if(mysql_num_rows($sql_res_1)>0)
        		{
        			list($company_name)=mysql_fetch_row($sql_res_1);
        			$company_name="<a href='/".$path_companies."/".$company_id.".html' style='font-weight:bold;'>".StripSlashes($company_name)."</a>";
        		}
                if (date("d.m.Y", strtotime($last_visit))==date("d.m.Y",strtotime("01.01.1970"))) $last_visit="не определено"; else $last_visit=date("d.m.Y", strtotime($last_visit));
                if (date("d.m.Y", strtotime($last_visit))==date("d.m.Y")&& $user_status=="admin") $highlight='bgcolor=#FFCEB7'; else $highlight="";
    			$out.="
    				<tr ".$highlight.">
    					<td align=left valign=middle style='border-bottom:solid 1px #777777;'><a href='".$id.".html'>".$name_show."</a></td>
    					<td align=left valign=middle style='border-bottom:solid 1px #777777;'>".$company_name."<BR/><BR/> [".$doljnost."]</td>
                        <td align=center valign=middle style='border-bottom:solid 1px #777777;'><a border=0 href='".$id.".html'>".$img_show."</a></td></tr>";
    
    		}
    		$out.="</table>";    		
    	}
        
        $sql_query="SELECT id FROM ".$sql_pref."_top_management WHERE enable='Yes' ".$show_today;
		$sql_res=mysql_query($sql_query, $conn_id);
		$num_predl=mysql_num_rows($sql_res);
        $out.="<br>Всего: ".$num_predl;
        
        
        if (!isset($cur_search) && !isset($cur_letter))
        {
            $sql_query="SELECT id FROM ".$sql_pref."_top_management WHERE enable='Yes'";
    		$sql_res=mysql_query($sql_query, $conn_id);
    		$num_predl=mysql_num_rows($sql_res);
            //echo $num_predl;         
    		$numpages=ceil($num_predl/$top_management_perpage);
            //echo $numpages; 
    		if ($numpages>1)
    		{
    			$out.="<br><br><div style='padding:30 0;'>Страницы: | ";
    			for ($i=1;$i<=$numpages;$i++)
    			{
    				if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show="<a href='?page=".$i."&letter=".@$_REQUEST['letter']."' style='text-decoration:none;'>".$i."</a>";
    				$out.="<span style='padding:2 1 2 1;'>".$i_show."</span> | ";
    			}
    			$out.="</div><br>";
    		}
        }
        $out.=$pages_show;
		
    }
    
	return ($out);
}





function check_resume($owner, $user_id){
global $sql_pref, $conn_id;

$sql_query="SELECT show_mode, show_limit_agency, show_limit_my FROM ".$sql_pref."_user_rezume WHERE user_id='".$owner."' AND complete='1'";
if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0)
list($show_mode, $show_limit_agency, $show_limit_my)=mysql_fetch_row($sql_res);
else {return(FALSE); break;}

$show_perm=FALSE;
if($show_mode=="all") $show_perm=TRUE;

elseif($show_limit_agency){
$sql_query="SELECT ".$sql_pref."_top_management.id FROM ".$sql_pref."_top_management INNER JOIN ".$sql_pref."_companies ON ".$sql_pref."_top_management.company_id = ".$sql_pref."_companies.id WHERE ".$sql_pref."_top_management.id='".$user_id."' AND ".$sql_pref."_companies.direction_ids REGEXP '54'";
$sql_res=mysql_query($sql_query, $conn_id);
if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0) $show_perm=TRUE;
}

elseif($show_limit_my){
$sql_query="SELECT id FROM ".$sql_pref."_user_phones WHERE user_id='".$owner."' AND contact_id='".$user_id."'";
$sql_res=mysql_query($sql_query, $conn_id);
if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0) $show_perm=TRUE;
}

if(!$show_perm) return(FALSE);
else return(TRUE);
}




function top_management_out()
{
	global $page_header1, $sql_pref, $conn_id, $path, $path_top_management, $path_questions, $path_forum, $forum_rub_num, $forum_rub_ur, $path_companies, $conf_pol, $art_url, $module_name, $module_url, $user_id, $path_resume;
	$out="";
    
    

    
    $sql_query="SELECT id, surname, name, name2, views FROM ".$sql_pref."_top_management WHERE enable='Yes'&&id='".$art_url."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $surname, $name, $name2, $views)=mysql_fetch_row($sql_res);
		$surname=stripslashes($surname); $name=stripslashes($name); $name2=stripslashes($name2);
	}	
 
    //количество просмотров
    if (isset($views)) $views=unserialize($views); else $views=array();
    $min_date=time()-2592000;
    foreach($views as $k=>$v) if($v<$min_date) unset($views[$k]);
    $views[$user_id]=time();
    $view_count=count($views);
    $views=serialize($views);
    if($user_id!=$id)
    {
    	$sql_query="UPDATE ".$sql_pref."_top_management SET views='".$views."' WHERE id='".$art_url."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    }
    
    
    // вывод информации о пользователе       
    $page_header1="Информация о менеджере (".$name." ".$surname.")";
    //=============  активность на сайте  =========================
   
    
    if (!isset($_REQUEST['action'])||$_REQUEST['action']=="") 
	{
    	$sql_query="SELECT id, surname, name, name2, email, phone_work, pol, dt_birth, company_id, doljnost, expirience, vuz, specialnost, descr city_id FROM ".$sql_pref."_top_management WHERE enable='Yes'&&id='".$art_url."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
    	{
    		list($id, $surname, $name, $name2, $email, $phone_work, $pol, $dt_birth, $company_id, $doljnost, $expirience, $vuz, $specialnost, $descr, $city_id)=mysql_fetch_row($sql_res);
    		$surname=stripslashes($surname); $name=stripslashes($name); $name2=stripslashes($name2); $phone_work=stripslashes($phone_work); $phone_mobile=stripslashes($phone_mobile); $doljnost=stripslashes($doljnost); $expirience=stripslashes($expirience); $vuz=stripslashes($vuz); $specialnost=stripslashes($specialnost); $descr=stripslashes(trim($descr));
    		$dt_reg_show=date('d.m.Y',strtotime($dt_reg));
    		//if ($dt_lastvisit!="0000-00-00 00:00:00") $dt_lastvisit_show=substr($dt_lastvisit,8,2).".".substr($dt_lastvisit,5,2).".".substr($dt_lastvisit,0,4)." ".substr($dt_lastvisit,11,2).":".substr($dt_lastvisit,14,2); else $dt_lastvisit_show="нет данных";
    
    		$name_show=$surname." ".$name." ".$name2;
            
            
            if (!empty($pol))
            {
                if ($pol=="m") $polshow="Мужской"; 
                elseif ($pol=="w") $polshow="Женский";
                $datatable['name'][]="Пол";
                $datatable['value'][]=$polshow;
            } 
            //if (!empty($email) && $user_id>0)
            //{
            //    $datatable['name'][]="E-mail";
            //    $datatable['value'][]=$email;
            //} 
            if (!empty($phone_work) && $user_id>0)
            {
                $datatable['name'][]="Рабочий телефон";
                $datatable['value'][]=$phone_work;
            } 
            if (!empty($phone_mobile) && $user_id>0)
            {
                $datatable['name'][]="Мобильный телефон";
                $datatable['value'][]=$phone_mobile;
            } 
            if (!empty($dt_birth) && $user_id>0)
            {
                $datatable['name'][]="Дата рождения";
                $datatable['value'][]=date("d.m.Y",strtotime($dt_birth));
            } 
            if (!empty($company_id))
            {
                $company_name="";
        		$sql_query="SELECT name FROM ".$sql_pref."_companies WHERE id='".$company_id."'";
        		$sql_res_1=mysql_query($sql_query, $conn_id);
        		if(mysql_num_rows($sql_res_1)>0)
        		{
        			list($company_name)=mysql_fetch_row($sql_res_1);
        			$company_name="<a href='/".$path_companies."/".$company_id.".html' style='font-weight:bold;'>".StripSlashes($company_name)."</a>";
        		}
                $datatable['name'][]="Компания";
                $datatable['value'][]=$company_name;
            }
            if (!empty($city_id))
            {
                $city_name="";
        		$sql_query="SELECT name FROM ".$sql_pref."_reg_cities WHERE id='".$city_id."'";
        		$sql_res_1=mysql_query($sql_query, $conn_id);
        		if(mysql_num_rows($sql_res_1)>0)
        		{
        			list($city_name)=mysql_fetch_row($sql_res_1);
        			//$city_name="<a href='/".$path_companies."/".$company_id.".html' style='font-weight:bold;'>".StripSlashes($company_name)."</a>";
        		}
                $datatable['name'][]="Город";
                $datatable['value'][]=$city_name;
            }
            if (!empty($doljnost) && $user_id>0)
            {
                $datatable['name'][]="Должность";
                $datatable['value'][]=$doljnost;
            } 
            if (!empty($vuz) && $user_id>0)
            {
                $datatable['name'][]="ВУЗ";
                $datatable['value'][]=$vuz;
            } 
            if (!empty($specialnost) && $user_id>0)
            {
                $datatable['name'][]="Специальность";
                $datatable['value'][]=$specialnost;
            } 
            if (!empty($expirience) && $user_id>0)
            {
                $datatable['name'][]="Биография";
                $datatable['value'][]=$expirience;
            } 
            
            if (!empty($descr) && $user_id>0)
            {
                $datatable['name'][]="О себе";
                $datatable['value'][]=$descr;
            } 
    
            $img_show="";
        	if (file_exists($path."files/top_management/img/".$id.".jpg") && $user_id>0)
        		$img_show="<div style='padding:10 0;'><img src='/files/top_management/img/".$id.".jpg' border=0></div>";
    		
            
            
            
            
            $out.="<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td align=left valign=top>";
        
            $out.="<div style='padding:10 0;font-size:14px;font-weight:bold;'>".$name_show."</div>";
            $out.="<table cellpadding=0 cellspacing=0 border=0 style='border-top: solid 0px #999;'>";
            for($i=0;$i<3;$i++)
            {
                if($datatable['name'][$i]!="")
                {
                    $out.="<tr><td valign=top align=left style='padding:6 10;border-bottom: solid 0px #999;'><strong>".$datatable['name'][$i].":</strong></td><td align=left style='padding:3 10;border-bottom: solid 0px #999;'>".$datatable['value'][$i]."</td></tr>";
                }
            }
            $out.="</table>";
            
            if (!empty($img_show)) $out.="<td width=20>&nbsp;</td><td width=300 align=center valign=top>".$img_show."</td><td width=20>&nbsp;</td>";
        
            $out.="</tr><tr><td colspan=3>";
            $out.="<table cellpadding=0 cellspacing=0 border=0 style='border-top: solid 0px #999;'>";
            for($i=3;$i<count($datatable['name']);$i++)
            {
                $out.="<tr><td valign=top align=left style='padding:6 10;border-bottom: solid 0px #999;'><strong>".$datatable['name'][$i].":</strong></td><td align=left style='padding:3 10;border-bottom: solid 0px #999;'>".$datatable['value'][$i]."</td></tr>";
            }
            $out.="</table>";
            $out.="</td></tr></table>";
    	}
        
        if(!$user_id>0) $out.="<br><div>Для получения дополнительной контактной информации необходима <a href='/auth/register'>регистрация</a></div>"; 
        else {
        
            $user_with_id=$id;   
            $sql_query_perepiska="
            SELECT Result_Tab.id  
            FROM (SELECT * FROM (SELECT ".$sql_pref."_messages_to.id, ".$sql_pref."_messages.user_id, ".$sql_pref."_messages.content, ".$sql_pref."_messages.dt  FROM ".$sql_pref."_messages_to LEFT JOIN ".$sql_pref."_messages ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages_to.user_id=".$user_id." AND ".$sql_pref."_messages.user_id=".$user_with_id." 
            UNION ALL
            SELECT ".$sql_pref."_messages.id, ".$sql_pref."_messages_to.user_id, ".$sql_pref."_messages.content, ".$sql_pref."_messages.dt FROM ".$sql_pref."_messages LEFT JOIN ".$sql_pref."_messages_to ON ".$sql_pref."_messages_to.message_id=".$sql_pref."_messages.id WHERE ".$sql_pref."_messages.user_id=".$user_id." AND ".$sql_pref."_messages_to.user_id=".$user_with_id.") As Res_Prom ORDER BY dt DESC)
            AS Result_Tab LEFT JOIN ".$sql_pref."_top_management ON Result_Tab.user_id=".$sql_pref."_top_management.id ORDER BY dt DESC";
            $sql_res_perepiska=mysql_query($sql_query_perepiska, $conn_id);
        	if (mysql_num_rows($sql_res_perepiska)>0)
        	{
        	   $perepiska="<a href='/auth/messages_show/?user_with_id=".$user_with_id."'><img src='/img/mail-search.png' alt='Посмотреть переписку' border=0> Посмотреть переписку с пользователем</a>";
            }
        }
    }
    
        
 	$out.="<br><br><a href='javascript:history.back(1)'>««&nbsp;вернуться</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='./'>все топ-менеджеры</a>";
    
       
	return ($out);
}


function show_news_activity($commentator_id)
{
    global $sql_pref, $conn_id, $path, $path_news, $news_perpage;
	$out="";
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$news_perpage).",".$news_perpage."";

	$sql_query="SELECT DISTINCT ".$sql_pref."_news.id, ".$sql_pref."_news.dt, ".$sql_pref."_news.name, ".$sql_pref."_news.descr, ".$sql_pref."_news.content FROM ".$sql_pref."_news INNER JOIN ".$sql_pref."_news_comments ON (".$sql_pref."_news.id=".$sql_pref."_news_comments.parent_id) WHERE ".$sql_pref."_news.enable='Yes' AND ".$sql_pref."_news_comments.user_id='".$commentator_id."' ORDER BY code".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $out.="<div style='padding:5 0 3 0;'>";
		while(list($id, $dt, $name, $descr, $content)=mysql_fetch_row($sql_res))
		{
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
							</td>
							<td valign=middle align=center width=100>".$img_show."</td>
						</tr>
					</table>";
			
			$out.="</div>";
            
		}
        $out.="</div>";
        
		
		$sql_query="SELECT DISTINCT ".$sql_pref."_news.id FROM ".$sql_pref."_news INNER JOIN ".$sql_pref."_news_comments ON (".$sql_pref."_news.id=".$sql_pref."_news_comments.parent_id) WHERE ".$sql_pref."_news.enable='Yes' AND ".$sql_pref."_news_comments.user_id='".$commentator_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$num_predl=mysql_num_rows($sql_res);
		$numpages=ceil($num_predl/$news_perpage);
		if ($numpages>1)
		{
			$out.="<br><br><div align=left>Страницы: | ";
			for ($i=1;$i<=$numpages;$i++)
			{
				if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show="<a href='?action=show_news_activity&page=".$i."'>".$i."</a>";
				$out.="<span style='padding:2 1 2 1;'>".$i_show."</span> | ";
			}
			$out.="</div><br>";
		}
	}    
	return ($out);
}

function show_articles_activity($commentator_id)
{
    global $sql_pref, $conn_id, $path, $path_articles, $news_perpage;
	$out="";
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$news_perpage).",".$news_perpage."";

	$sql_query="SELECT DISTINCT ".$sql_pref."_articles.id, ".$sql_pref."_articles.dt, ".$sql_pref."_articles.name, ".$sql_pref."_articles.descr, ".$sql_pref."_articles.content FROM ".$sql_pref."_articles INNER JOIN ".$sql_pref."_articles_comments ON (".$sql_pref."_articles.id=".$sql_pref."_articles_comments.parent_id) WHERE ".$sql_pref."_articles.enable='Yes' AND ".$sql_pref."_articles_comments.user_id='".$commentator_id."' ORDER BY dt ".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $out.="<div style='padding:5 0 3 0;'>";
		while(list($id, $dt, $name, $descr, $content)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);$descr=stripslashes($descr);$content=stripslashes($content);
			if ($dt!="0000-00-00") $date_show="<div style='padding:1 0 1 0;'><span style='padding:1 3 1 3;color:#ffffff;background-color:#2893c1;font-size:11px;'>".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)."</span></div>"; else $date_show="";
			$sql_queryArt="SELECT url FROM ".$sql_pref."_articles WHERE enable='Yes' AND id=".$id;   
            $sql_resArt=mysql_query($sql_queryArt, $conn_id);
            if(mysql_num_rows($sql_resArt)>0)
            {
                list($art_url)=mysql_fetch_row($sql_resArt);         
            }
            if (!empty($name)) $name_show="<div style='padding:1 0 1 0;'><a href='/".$path_articles."/".$art_url.".html' style='font-size:12px;font-weight:bold;text-decoration:none;'>".$name."</a></div>"; else $name_show="";
			
            
            
			$descr=str_replace("\n","<br>",$descr);
			$descr_show="<div style='padding:1 0 1 0;'>".$descr."</div>";
			
            
            $comments_show="";
            $sql_query="SELECT id FROM ".$sql_pref."_articles_comments WHERE parent_id='".$id."'";
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
							</td>
							<td valign=middle align=center width=100>".$img_show."</td>
						</tr>
					</table>";
			
			$out.="</div>";
            
		}
        $out.="</div>";
        
		
		$sql_query="SELECT DISTINCT ".$sql_pref."_articles.id FROM ".$sql_pref."_articles INNER JOIN ".$sql_pref."_articles_comments ON (".$sql_pref."_articles.id=".$sql_pref."_articles_comments.parent_id) WHERE ".$sql_pref."_articles.enable='Yes' AND ".$sql_pref."_articles_comments.user_id='".$commentator_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$num_predl=mysql_num_rows($sql_res);
		$numpages=ceil($num_predl/$news_perpage);
		if ($numpages>1)
		{
			$out.="<br><br><div align=left>Страницы: | ";
			for ($i=1;$i<=$numpages;$i++)
			{
				if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show="<a href='?action=show_articles_activity&page=".$i."'>".$i."</a>";
				$out.="<span style='padding:2 1 2 1;'>".$i_show."</span> | ";
			}
			$out.="</div><br>";
		}
	}    
	return ($out);
}

function show_questions_activity($commentator_id)
{
	global $sql_pref, $conn_id, $path, $path_top_management, $path_questions, $questions_answers_perpage, $user_id, $user_status;
	$out="<table cellpadding=2 cellspacing=2 border=0>
						<tr>
                            <td width=5%> </td>
                            <td width=15%> </td>
                            <td width=30%> </td>
                            <td width=20%> </td>
                            <td width=30%> </td>
						</tr>";
	

    
	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	$perpage=$questions_answers_perpage; $first=$perpage*($page-1);
	   
	$sql_query="SELECT DISTINCT ".$sql_pref."_questions.id, ".$sql_pref."_questions.dt, ".$sql_pref."_questions.question, ".$sql_pref."_questions.user_id, ".$sql_pref."_questions.question_type FROM ".$sql_pref."_questions LEFT JOIN ".$sql_pref."_questions_answers ON (".$sql_pref."_questions.id=".$sql_pref."_questions_answers.question_id) WHERE  ".$sql_pref."_questions.enable='Yes' AND (".$sql_pref."_questions.user_id=".$commentator_id." OR ".$sql_pref."_questions_answers.user_id=".$commentator_id.") ORDER BY ".$sql_pref."_questions.dt DESC, ".$sql_pref."_questions.id DESC LIMIT ".$first.",".$perpage;
    //$sql_query="SELECT DISTINCT ".$sql_pref."_questions.id FROM ".$sql_pref."_questions INNER JOIN ".$sql_pref."_questions_answers ON (".$sql_pref."_questions.id=".$sql_pref."_questions_answers.question_id) WHERE ".$sql_pref."_questions.enable='Yes' AND (".$sql_pref."_questions.user_id=".$art_url." OR ".$sql_pref."_questions_answers.user_id=".$art_url.")";
	//echo $sql_query;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $dt, $question, $p_user_id, $question_type)=mysql_fetch_row($sql_res))
		{
			$question=stripslashes($question);$question=str_replace("\n", "<br>", $question);
			
			$dt_show="<div style='padding: 3 0;'><span style='padding: 2 4;background-color:#eee;'>".date("d.m.Y H:i:s", strtotime($dt))."</span></div>";
            $question_show="<div style='padding: 3 0;'>".substr($question,0,50)."...</div>";
            if ($question_type==2) $results='результаты опроса'; else $results='обсуждение';
            $more_show="<div style='padding: 3 0;'><a href='/".$path_questions."/".$id.".html'>Посмотреть ".$results."...</a></div>";
            $question_show="<div style='padding: 3 0;'><a href='/".$path_questions."/".$id.".html'>".substr($question,0,50)."...</a></div>";
            $sql_query="SELECT name, surname FROM ".$sql_pref."_top_management WHERE id='".$p_user_id."'";
        	$sql_res_1=mysql_query($sql_query, $conn_id);
        	list($user_name, $user_surname)=mysql_fetch_row($sql_res_1);
          
            $p_user_name=$user_name." ".$user_surname;
            //$p_user_show="<div style='padding: 3 0;font-size:18px;'>".$p_user_name."</div>";
            $p_user_show="<a href='/".$path_top_management."/".$p_user_id.".html' style='font-weight:bold;'>".$p_user_name."</a>";

            if ($user_status=="admin") //if ($p_user_id==@$user_id || $user_status=="admin") 
            { 
                $del_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_questions."/del".$id.".html?action=question_del&question_id=".$id."'\"  style='font-size:9px;color:#999999;'>Удалить</a>";
                $change_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_questions."/change".$id.".html?action=question_change_form&question_id=".$id."'\"  style='font-size:9px;color:#999999;'>Править</a>";
            }
            else
            {
                $del_but="";
                $change_but="";
            }
            if ($question_type==1) $img_view="<img src='/img/question.png' alt='Вопрос'>"; else $img_view="<img src='/img/query.png' border=0 width=20px height=20px alt='Опрос'>";
            
            $sql_query_com="SELECT id FROM ".$sql_pref."_questions_answers WHERE question_id='".$id."'";
        	$sql_res_com=mysql_query($sql_query_com, $conn_id);
            $comments_num=mysql_num_rows($sql_res_com);
            $comments_show="<img src=/img/small/comments.gif width=10 height=9 border=0> Комментарии: <a href='/".$path_questions."/".$id.".html#comments'>".$comments_num."</a>";
			
            $out.="     <tr><td></br></td><td></br></td><td></td><td></td><td></td></tr>	
                        <tr>
                            <td valign=middle>".$img_view."</td>
                            <td valign=middle>".$dt_show."</td>
                            <td valign=middle>".$question_show."</td>
                            <td valign=middle><img src=/img/small/author.gif width=8 height=9 border=0>&nbsp;&nbsp;<b>".$p_user_show."</b><br>".$comments_show."</td>
                            <td valign=middle>".$del_but.$change_but."</td>
						</tr>
                        <tr><td></br></td><td></br></td><td></td><td></td><td></td></tr>";

		}
		
	}
	$out.= "</table>";
    
    $sql_query="SELECT DISTINCT ".$sql_pref."_questions.id FROM ".$sql_pref."_questions INNER JOIN ".$sql_pref."_questions_answers ON (".$sql_pref."_questions.id=".$sql_pref."_questions_answers.question_id) WHERE ".$sql_pref."_questions.enable='Yes' AND (".$sql_pref."_questions.user_id=".$commentator_id." OR ".$sql_pref."_questions_answers.user_id=".$commentator_id.")";
    $sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$questions_answers_perpage);
	if ($numpages>1)
	{
		$out.="<br><br><div align=left>Страницы: | ";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show="<a href='?action=show_questions_activity&page=".$i."'>".$i."</a>";
			$out.="<span style='padding:2 1 2 1;'>".$i_show."</span> | ";
		}
		$out.="</div><br>";
	}
    
    $out.=$pages_show;
    $out.=$add_link;
	
	return ($out);
}

function show_proposals_activity($commentator_id)
{
    global $sql_pref, $conn_id, $path, $path_proposals, $news_perpage;
	$out="";
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$news_perpage).",".$news_perpage."";

	$sql_query="SELECT DISTINCT ".$sql_pref."_proposals.id, ".$sql_pref."_proposals.dt, ".$sql_pref."_proposals.descr, ".$sql_pref."_proposals.content FROM ".$sql_pref."_proposals WHERE ".$sql_pref."_proposals.enable='Yes' AND ".$sql_pref."_proposals.user_id='".$commentator_id."' ORDER BY dt".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $out.="<div style='padding:5 0 3 0;'>";
		while(list($id, $dt, $descr, $content)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);$descr=stripslashes($descr);$content=stripslashes($content);
			if ($dt!="0000-00-00") $date_show="<div style='padding:1 0 1 0;'><span style='padding:1 3 1 3;color:#ffffff;background-color:#2893c1;font-size:11px;'>".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)."</span></div>"; else $date_show="";
			if (!empty($name)) $name_show="<div style='padding:1 0 1 0;'><a href='/".$path_news."/".$id.".html' style='font-size:12px;font-weight:bold;text-decoration:none;'>".$name."</a></div>"; else $name_show="";
			
			$descr=str_replace("\n","<br>",$descr);
			$descr_show="<div style='padding:1 0 1 0;'>".$descr."</div>";
            $more_show="<div style='padding: 3 0;'><a href='/".$path_proposals."/".$id.".html'>Подробнее...</a></div>";
			
            
            $comments_show="";                   

			
			$out.="<div style='padding: 5 0 10 0;'>";
			
			$out.=" <table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<td valign=middle>
								".$date_show."
                                ".$descr_show."                                
                                ".$more_show."
							</td>
						</tr>
					</table>";
			
			$out.="</div>";
            
		}
        $out.="</div>";
        
		$sql_query="SELECT DISTINCT ".$sql_pref."_proposals.id, ".$sql_pref."_proposals.dt, ".$sql_pref."_proposals.descr, ".$sql_pref."_proposals.content FROM ".$sql_pref."_proposals WHERE ".$sql_pref."_proposals.enable='Yes' AND ".$sql_pref."_proposals.user_id='".$commentator_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$num_predl=mysql_num_rows($sql_res);
		$numpages=ceil($num_predl/$news_perpage);
		if ($numpages>1)
		{
			$out.="<br><br><div align=left>Страницы: | ";
			for ($i=1;$i<=$numpages;$i++)
			{
				if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show="<a href='?action=show_proposals_activity&page=".$i."'>".$i."</a>";
				$out.="<span style='padding:2 1 2 1;'>".$i_show."</span> | ";
			}
			$out.="</div><br>";
		}
	}    
	return ($out);
}

function show_forum_activity($commentator_id)
{
    global $sql_pref, $conn_id, $path, $path_forum, $news_perpage;
	$out="";
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$news_perpage).",".$news_perpage."";
    
	$sql_query="SELECT id, dt, content FROM ".$sql_pref."_forum_posts WHERE user_id=".$commentator_id." ORDER BY dt ".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $out.="<div style='padding:5 0 3 0;'>";
		while(list($id, $dt, $content)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);$descr=stripslashes($content);
			if ($dt!="0000-00-00") $date_show="<div style='padding:1 0 1 0;'><span style='padding:1 3 1 3;color:#ffffff;background-color:#2893c1;font-size:11px;'>".substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)."</span></div>"; else $date_show="";
			if (!empty($name)) $name_show="<div style='padding:1 0 1 0;'><a href='/".$path_news."/".$id.".html' style='font-size:12px;font-weight:bold;text-decoration:none;'>".$name."</a></div>"; else $name_show="";
			
			$descr=str_replace("\n","<br>",$descr);
			$descr_show="<div style='padding:1 0 1 0;'>".$descr."</div>";
            $more_show="<div style='padding: 3 0;'><a href='/".$path_forum."/'>Подробнее...</a></div>";
			
            
            $comments_show="";                   

			
			$out.="<div style='padding: 5 0 10 0;'>";
			
			$out.=" <table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<td valign=middle>
								".$date_show."
                                ".$descr_show."                                
                                ".$more_show."
							</td>
						</tr>
					</table>";
			
			$out.="</div>";
            
		}
        $out.="</div>";
        
		$sql_query="SELECT id, dt, content FROM ".$sql_pref."_forum_posts WHERE user_id=".$commentator_id;
		$sql_res=mysql_query($sql_query, $conn_id);
		$num_predl=mysql_num_rows($sql_res);
		$numpages=ceil($num_predl/$news_perpage);
		if ($numpages>1)
		{
			$out.="<br><br><div align=left>Страницы: | ";
			for ($i=1;$i<=$numpages;$i++)
			{
				if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show="<a href='?action=show_forum_activity&page=".$i."'>".$i."</a>";
				$out.="<span style='padding:2 1 2 1;'>".$i_show."</span> | ";
			}
			$out.="</div><br>";
		}
	}    
	return ($out);
}
?>