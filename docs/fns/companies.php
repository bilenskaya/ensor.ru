<?php

function companies_main()
{
	global $sql_pref, $conn_id, $art_url;
	$out="";


    if (isset($_REQUEST['action']))
    {
        if ($_REQUEST['action']=="comment_add")  companies_comments_form_save();
        if ($_REQUEST['action']=="companies_save_form")  companies_save_form();
        elseif ($_REQUEST['action']=="comment_del")  companies_comments_del();
    }

    
	if (isset($art_url)) $out.=companies_out();
	else 
    {   
        if(isset($_REQUEST['action'])&&$_REQUEST['action']=="companies_add_form")
        {
            $out.=companies_add_form();
        }
        else
        {
            $out.=companies_list();
	    }
    }
	return ($out);
}







function companies_list()
{
	global $sql_pref, $conn_id, $path, $path_companies, $companies_perpage, $page_header1, $page_title;
	$out="";

    $add_link="<table><tr><td><a href='http://www.ensor.ru/rabotodateljam/company_reg/index.html?action=company_add'><img width=24px src='/img/add.png' border=0></a></td><td><a href='http://www.ensor.ru/rabotodateljam/company_reg/index.html?action=company_add'><font style='vertical-align: inherit;'>Добавьте свою организацию</font></a></td><td width=80></td><td><a href='http://www.ensor.ru/rabotodateljam/'><img width=24px src='/img/question_big.png' border=0></a></td><td><a href='http://www.ensor.ru/rabotodateljam/'><font style='vertical-align: inherit;'>Узнайте про дополнительные возможности!</font></a></td></tr></table>";
    
    $out=$add_link;
    if (isset($_REQUEST['letter']) && !empty($_REQUEST['letter'])) $cur_letter=$_REQUEST['letter'];
    if (isset($_REQUEST['sfery']) && !empty($_REQUEST['sfery'])) $cur_sfery=implode(",",$_REQUEST['sfery']);
    if (isset($_REQUEST['directions']) && !empty($_REQUEST['directions'])) $cur_directions=implode(",",$_REQUEST['directions']);
    if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) $cur_search=mysql_escape_string($_REQUEST['search']);
    
    
    if (!isset($cur_sfery) && !isset($cur_directions) && !isset($cur_search))
    {
        if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
        $pref_page=" LIMIT ".(($page-1)*$companies_perpage).",".$companies_perpage."";
        //echo $pref_page;
    }
    
    
    
    
	if (isset($cur_letter) && !empty($cur_letter))
    {
        if (ord($cur_letter)>=ord('А') && ord($cur_letter)<=ord('Я')) $add_sql_list[]="SUBSTRING(c.name,1,1)='".rawurldecode($cur_letter)."'";
        elseif (ord($cur_letter)>=ord('A') && ord($cur_letter)<=ord('Z')) $add_sql_list[]="SUBSTRING(c.name_eng,1,1)='".rawurldecode($cur_letter)."'";
    } 
    if (isset($cur_sfery) && !empty($cur_sfery))
    {
        $as_sa_sql=", ".$sql_pref."_sd_sfery_arts AS sa";
        $add_sql_list[]="sa.table_name='companies'&&sa.sfera_id IN('".$cur_sfery."')&&sa.art_id=c.id";
    }
    if (isset($cur_directions) && !empty($cur_directions)) 
    {
        $as_da_sql=", ".$sql_pref."_sd_directions_arts AS da";
        $add_sql_list[]="da.table_name='companies'&&da.direction_id IN('".$cur_directions."')&&da.art_id=c.id";        
    }
    if (isset($cur_search) && !empty($cur_search)) 
    {
        $add_sql_list[]="c.name LIKE '%".$cur_search."%' || c.name_eng LIKE '%".$cur_search."%' || c.name_full LIKE '%".$cur_search."%'";
    }
    
    
    
    
	$sql_query="SELECT SUBSTRING(name,1,1) FROM ".$sql_pref."_companies GROUP BY SUBSTRING(name,1,1)";
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
    
    
    
    
	$sql_query="SELECT SUBSTRING(name_eng,1,1) FROM ".$sql_pref."_companies GROUP BY SUBSTRING(name_eng,1,1)";
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
        
    
    
    
    if (!empty($cur_directions) || !empty($cur_sfera)) $filsd=""; else $filsd="none";
    if (!empty($cur_search) || !empty($cur_letter)) $filname=""; else $filname="";
    
    $directions_filter="";
    $sql_query="SELECT id, name FROM ".$sql_pref."_sd_directions ORDER BY code";
    $sql_res_1=mysql_query($sql_query, $conn_id);
    if(mysql_num_rows($sql_res_1)>0)
    {
        $directions_filter.="<div style='padding: 5 0 0 5;font-weight:bold;'>Направления деятельности</div>";
        $directions_filter.="<div style='padding: 5 0 10 10;'>";
    	while (list($d_id, $d_name)=mysql_fetch_row($sql_res_1))
    	{
    		$d_name=stripslashes($d_name);
            $d_name_show=$d_name;
            if (isset($_REQUEST['directions']) && in_array($d_id,$_REQUEST['directions'])) $ch="checked"; else $ch="";
            $directions_filter.="<div><input type=checkbox name=directions[] value='".@$d_id."' ".$ch."> ".$d_name_show."</div>";
    	}
        $directions_filter.="</div>";
    }
    
    
    
    $sfery_filter="";
    $sql_query="SELECT id, name FROM ".$sql_pref."_sd_sfery ORDER BY code";
    $sql_res_1=mysql_query($sql_query, $conn_id);
    if(mysql_num_rows($sql_res_1)>0)
    {
        $sfery_filter.="<div style='padding: 5 0 0 5;font-weight:bold;'>Сферы деятельности</div>";
        $sfery_filter.="<div style='padding: 5 0 10 10;'>";
    	while (list($d_id, $d_name)=mysql_fetch_row($sql_res_1))
    	{
    		$d_name=stripslashes($d_name);
            $d_name_show=$d_name;
            if (isset($_REQUEST['sfery']) && in_array($d_id,$_REQUEST['sfery'])) $ch="checked"; else $ch="";
            $sfery_filter.="<div><input type=checkbox name=sfery[] value='".@$d_id."' ".$ch."> ".$d_name_show."</div>";
    	}
        $sfery_filter.="</div>";
    }
     
    
    $form_sd_name_head_show="<div style='padding: 10 0 10 0;'><span onclick='if (document.getElementById(\"divfiltername\").style.display==\"none\") {document.getElementById(\"divfiltersd\").style.display=\"none\";document.getElementById(\"divfiltername\").style.display=\"\";} else document.getElementById(\"divfiltername\").style.display=\"none\";' style='cursor:pointer;font-size:12px;font-weight:normal;text-decoration:none;border-bottom:dotted 1px gray;margin: 0 20 0 0;'>Поиск по наименованию</span><span onclick='if (document.getElementById(\"divfiltersd\").style.display==\"none\") {document.getElementById(\"divfiltername\").style.display=\"none\";document.getElementById(\"divfiltersd\").style.display=\"\";} else document.getElementById(\"divfiltersd\").style.display=\"none\";' style='cursor:pointer;font-size:12px;font-weight:normal;text-decoration:none;border-bottom:dotted 1px gray;margin: 0 20 0 0;'>Поиск по сферам и направлениям деятельности</span></div>";
    $form_sd_show="<div id='divfiltersd' style='padding: 5 0 10 0;display:".$filsd.";border:solid 1px #bbb;background-color:#f6f6f6;'>
                        <form action='' method=post name=filter_sd_form style='padding: 0;margin: 0;'>
                            <table cellpadding=5 cellspacing=0 border=0>
                                <tr>
                                    <td valign=top>".$directions_filter."</td>
                                    <td valign=top>".$sfery_filter."</td>
                                </tr>
                            </table>
                            <div align=center style='padding: 10 0 0 0;'><input type=submit value='Найти' style='font-size: 14px; width:150px; background-color: #fff; color: #555555; border: 1px #555555 solid;'></div>
                        </form>
                    </div>
                   </div>";
    $form_name_show="<div id='divfiltername' style='padding: 5 0 10 0;display:".$filname.";border:solid 1px #bbb;background-color:#f6f6f6;'>
                        ".$letters_ru_filter.$letters_en_filter."
                        <form action='' method=get name=filter_name_form style='padding: 0;margin: 0;'>
                            <div align=center style='padding: 10 0 0 10;font-weight:bold;'>Поиск по наименованию:</div>
                            <div align=center style='padding: 5 0 10 10;'><input type=text name=search value='".$_REQUEST['search']."' style='width:400px;font-size:14px;'></div>
                            <div align=center style='padding: 10 0 0 0;'><input type=submit value='Найти' style='font-size: 14px; width:150px; background-color: #fff; color: #555555; border: 1px #555555 solid;'></div>
                        </form>
                    </div>
                   </div>";
    


    
    
    $out.=$form_sd_name_head_show;
    $out.=$form_sd_show;
    $out.=$form_name_show;
    
    $out.="<br>";


    
    $add_sql_list[]="c.enable='Yes'";
    $add_sql=implode("&&",$add_sql_list);
    


    
    if($cur_letter!="")
    {
       	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
    	$perpage=$companies_perpage; $first=$perpage*($page-1);
    	$sql_query="SELECT c.id FROM ".$sql_pref."_companies AS c".$as_sa_sql.$as_da_sql." WHERE ".$add_sql;
        //echo $sql_query;
        $args="&letter=".$_REQUEST['letter'];
    	$pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums_with_args($page, $perpage, $sql_query,$args)."</div>";
        $first=$perpage*($page-1);
        //$pref_page
    }
    
    $sql_query="SELECT c.id, c.name, c.name_eng, c.name_full, c.descr, c.content, c.site FROM ".$sql_pref."_companies AS c".$as_sa_sql.$as_da_sql." WHERE ".$add_sql." GROUP BY c.name ORDER BY c.name ".@$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $name, $name_eng, $name_full, $descr, $content, $site)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);$name_eng=stripslashes($name_eng);$name_full=stripslashes($name_full);$descr=stripslashes($descr);$content=stripslashes($content);
            
            $name_eng_show="";$name_full_show="";
            if (!empty($name_full)) $name_full_show.="<div style='margin:0 0 0 0;color:#999;font-size:11px;'>".$name_full."</div>";
            if (!empty($name_eng)) $name_eng_show.="<span style='margin:0 0 0 10;color:#555;font-size:12px;'>/ ".$name_eng."</span>";
			if (!empty($name)) $name_show="<div><a href='/".$path_companies."/".$id.".html' style='font-weight:bold;'>".$name."</a>".$name_eng_show."</div>".$name_full_show; else $name_show="";
			
			$descr=str_replace("\n","<br>",$descr);
			$descr_show="<div style='font-size:11px;'>".$descr."</div>";
			
			if (!empty($site)) $site_show="<div>".$site."</div>"; else $site_show="";
			
			if (file_exists($path."/files/companies/thumbs/".$id.".jpg") || file_exists($path."/files/companies/thumbs/".$id.".gif")) 
			{
				if (file_exists($path."/files/companies/thumbs/".$id.".jpg")) $ext=".jpg";
				elseif (file_exists($path."/files/companies/thumbs/".$id.".gif")) $ext=".gif";
				$size=getimagesize($path."/files/companies/thumbs/".$id.$ext);
				$img_show="<table cellpadding=3 cellspacing=0 border=0 style='border: solid 0px #999999;' width=120 height=80><tr><td align=center valign=middle><a href='/".$path_companies."/".$id.".html'><img src='/files/companies/thumbs/".$id.$ext."' width='".$size[0]."' height='".$size[1]."' border=0 alt='".$name."'></a></td></tr></table>";
			}
			else $img_show="";
			
			
			
			$out.="<table cellpadding=3 cellspacing=0 border=0 width=100%><tr>";
			//if (!empty($img_show)) $out.="<td valign=middle align=center width=130>".$img_show."</td>";
			
			$out.="<td valign=middle align=left>".$name_show."</td>";
			$out.="</tr></table>";
			$out.="<br>";
		}
		
		
        if (!isset($cur_directions) && !isset($cur_sfera) && !isset($cur_search) && !isset($cur_letter))
        {
            $sql_query="SELECT id FROM ".$sql_pref."_companies WHERE enable='Yes'".$letter_sql."";
    		$sql_res=mysql_query($sql_query, $conn_id);
    		$num_predl=mysql_num_rows($sql_res);
    		$numpages=ceil($num_predl/$companies_perpage);
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
    
    //$add_link="<div style='padding:2 0 2 0;font-size:11px;color:#555;'>Если вы хотите добавить организацию, <span style='font-size:11px;cursor:pointer;text-decoration:underline;' onClick=\"window.open('/feedback.html?subj=auth_company', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\">сообщите нам</span> и она будет добавлена на сайт.</div>";
    $out.=$add_link;
	return ($out);
}



function companies_catalog($companies_id, $companies_name)
{
	global $sql_pref, $conn_id, $path_equipment;
	$out="";
	$sql_query="SELECT parent_id FROM ".$sql_pref."_catalog WHERE enable='Yes' AND org_id='".$companies_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0) 
		while(list($rub_id)=mysql_fetch_row($sql_res)) 
		{
			$rub_id=unserialize($rub_id); 
			foreach($rub_id as $k=>$v) $found_rubs[]=$v;
		}
	if (is_array($found_rubs)) 
	{$found_rubs=array_unique($found_rubs);
	$out.="<div style='padding: 5 0;font-size:14px;'>Компания <strong>".$companies_name."</strong> представлена в следующих разделах <a href='/".$path_equipment."'>Каталога оборудования</a>:</div>";
	$out.="<ul>";
	foreach($found_rubs as $k=>$v)
		{
		$sql_query="SELECT name FROM ".$sql_pref."_catalog_rub WHERE (enable='Yes' and id='$v')";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) list($name)=mysql_fetch_row($sql_res);
		$out.="<li><a href='/".$path_equipment."/".$v.".html'>".$name."</a></li>";
		}
	$out.="</ul>";
	}
	else 
	$out.="<div style='padding: 5 0;font-size:14px;'>Компания <strong>".$companies_name."</strong> не представлена в <a href='/".$path_equipment."'>Каталоге оборудования</a></div>";
return $out;
}


function companies_vacancies($companies_id, $companies_name)
{
	global $sql_pref, $conn_id, $path_vacancies;
	$out="";
	$out.="<div style='padding: 5 0;font-size:14px;'><br>Вакансии <strong>".$companies_name."</strong>:</div>";
	$sql_query="SELECT id, dt, name, descr, content, user_id, company_id, zp_value_min, zp_value_max, zp_valuta FROM ".$sql_pref."_vacancies WHERE enable='Yes' and company_id='".$companies_id."'ORDER BY dt DESC";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$out.="<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
                            <td width=20%> </td>
                            <td width=40%> </td>
                            <td width=20%> </td>
                            <td width=20%> </td>
						</tr>";
		
		while(list($id, $dt, $name, $descr, $content, $p_user_id, $company_id, $zp_value_min, $zp_value_max, $zp_valuta)=mysql_fetch_row($sql_res))
		{
			$descr=stripslashes($descr);$descr=str_replace("\n", "<br>", $descr);
			
			$dt_show="<div style='padding: 3 0;'><span style='padding: 2 4;background-color:#eee;'>".date("d.m.Y", strtotime($dt))."</span></div>";
            $name_show="<div style='padding: 3 0;'>".$name."</div>";
            $more_show="<div style='padding: 3 0;'><a href='/".$path_vacancies."/".$id.".html'>Подробнее...</a></div>";
            $zp_show="";
            if(!empty($zp_value_min))
            {
                $zp_show.="от ".$zp_value_min;
            }
            if(!empty($zp_value_max))
            {
                $zp_show.=" до ".$zp_value_max;
            }
            if(empty($zp_value_min) & empty($zp_value_max))
            {
               $zp_show="з/п не указана"; 
            } 
            else
            {
                $zp_show.=" ".$zp_valuta;
            }
			$out.="     <tr><td></br></td><td></td><td></td><td></td></tr>	
                        <tr>
                            <td valign=middle>".$dt_show."</td>
                            <td valign=middle>".$name_show." ".$zp."</td>
                            <td valign=middle>".$zp_show."</td>
                            <td valign=middle>".$more_show."</td>
						</tr>
                        <tr><td></br></td><td></td><td></td><td></td></tr>";

		}
		$out.= "</table>";
	}
	else $out.="Вакансий нет";
	
    $add_link="<div style='padding:2 0 2 0;font-size:11px;color:#555;'>Если вы хотите разместить вакансию, <span style='font-size:11px;cursor:pointer;text-decoration:underline;' onClick=\"window.open('/feedback.html?subj=vacancies_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\">сообщите нам</span> и она будет добавлена на сайт.</div>";
	$out.=$pages_show;
    $out.=$add_link;
return $out;
}


function companies_out()
{
	global $sql_pref, $conn_id, $path, $art_url, $path_top_management, $path_companies, $path_catalog, $path_equipment, $path_objects, $path_users;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
	$companies_id=$art_url;
 	$sql_query="SELECT id, parent_id, name, name_full, city_id, address_legal, address_fact, requisites, phone1, phone2, fax, email, site, descr, content, sfera_ids, direction_ids, tags, author_id, enable FROM ".$sql_pref."_companies WHERE id='".$companies_id."' AND enable='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $parent_id, $name, $name_full, $city_id, $address_legal, $address_fact, $requisites, $phone1, $phone2, $fax, $email, $site, $descr, $content, $sfera_ids, $direction_ids, $tags, $author_id, $enable)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); $name_full=stripslashes($name_full); $address_legal=stripslashes($address_legal); $address_fact=stripslashes($address_fact); $requisites=stripslashes($requisites); $phone1=stripslashes($phone1); $phone2=stripslashes($phone2); $fax=stripslashes($fax); $tags=stripslashes($tags); $descr=stripslashes($descr); $content=stripslashes($content);
        $directions_list=explode(";",$direction_ids);$sfery_list=explode(";",$sfera_ids);
        
        if (!empty($site) && substr($site,0,7)!="http://") $site="http://".$site;
		
        if (!empty($name_full)) $name_full_show="<tr><td class=companiestable>Полное название:</td><td class=companiestable>&nbsp;</td><td class=companiestable>".$name_full."</td></tr>";
        if (!empty($address_legal)) $address_legal_show="<tr><td class=companiestable>Юридический адрес:</td><td class=companiestable>&nbsp;</td><td class=companiestable>".$address_legal."</td></tr>";
        if (!empty($address_fact)) $address_fact_show="<tr><td class=companiestable>Фактический адрес:</td><td class=companiestable>&nbsp;</td><td class=companiestable>".$address_fact."</td></tr>";
        if (!empty($phone1) || !empty($phone2)) $phone_show="<tr><td class=companiestable>Телефоны:</td><td class=companiestable>&nbsp;</td><td class=companiestable>".$phone1." ".$phone2."</td></tr>";
        if (!empty($fax)) $fax_show="<tr><td class=companiestable>Факс:</td><td class=companiestable>&nbsp;</td><td class=companiestable>".$fax."</td></tr>";
        if (!empty($email)) $email_show="<tr><td class=companiestable>E-mail:</td><td class=companiestable>&nbsp;</td><td class=companiestable>".$email."</td></tr>";
        if (!empty($site)) $site_show="<tr><td class=companiestable>Сайт:</td><td class=companiestable>&nbsp;</td><td class=companiestable><a href='".$site."' target=_blank>".$site."</a></td></tr>";

		
		
        $parent_company_show="";
        if ($parent_id>0)
        {
            $sql_query="SELECT name FROM ".$sql_pref."_companies WHERE id='".$parent_id."'";
    		$sql_res_1=mysql_query($sql_query, $conn_id);
            if(mysql_num_rows($sql_res_1)>0)
            {
        		list($parent_name)=mysql_fetch_row($sql_res_1);
       			$parent_name=stripslashes($parent_name);
                $parent_company_show.="<tr><td class=companiestable>Вышестоящая компания:</td><td class=companiestable>&nbsp;</td><td class=companiestable><a href='".$parent_id.".html'>".$parent_name."</a></td></tr>";
            }
        }
        
        
        $sub_company_show="";
        $sql_query="SELECT id, name FROM ".$sql_pref."_companies WHERE parent_id='".$id."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
        if(mysql_num_rows($sql_res_1)>0)
        {
    		while(list($subcompany_id, $subcompany_name)=mysql_fetch_row($sql_res_1))
            {
       			$subcompany_name=stripslashes($subcompany_name);
                $sub_company_array[]="<a href='".$subcompany_id.".html'>".$subcompany_name."</a>";
                
            }
            $sub_company_show.="<tr><td class=companiestable>Дочерние компании:</td><td class=companiestable>&nbsp;</td><td class=companiestable>";
            $sub_company_show.="<div>".implode(', ', $sub_company_array)."</div>";
            $sub_company_show.="</td></tr>";
        }
        
        
        
        $obj_company_show="";
        $sql_query="SELECT id, name FROM ".$sql_pref."_objects WHERE company_id='".$id."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
        if(mysql_num_rows($sql_res_1)>0)
        {
    		while(list($objcompany_id, $objcompany_name)=mysql_fetch_row($sql_res_1))
            {
       			$objcompany_name=stripslashes($objcompany_name);
                $obj_company_array[]="<a href='/".$path_objects."/".$objcompany_id.".html'>".$objcompany_name."</a>";
                
            }
            $obj_company_show.="<tr><td class=companiestable>Объекты:</td><td class=companiestable>&nbsp;</td><td class=companiestable>";
            $obj_company_show.="<div>".implode(', ', $obj_company_array)."</div>";
            $obj_company_show.="</td></tr>";
        }
        
        
        
        
        $city_show="";
		if (!empty($city_id) && $city_id>0)
		{
            $sql_query="SELECT c.name, r.name FROM ".$sql_pref."_cities AS c, ".$sql_pref."_regions AS r WHERE c.id='".$city_id."'&&c.region_id=r.id ORDER BY c.name";
            $sql_res_1=mysql_query($sql_query, $conn_id);
            list($city_name, $region_name)=mysql_fetch_row($sql_res_1);
           	$city_name=stripslashes($city_name);$region_name=stripslashes($region_name);
            $city_show.="<tr><td class=companiestable>Город:</td><td class=companiestable>&nbsp;</td><td class=companiestable>".$city_name." (".$region_name.")</td></tr>";
		}
        
        
        
        
        $sfery_show="";
        $sql_query="SELECT s.id, s.name FROM ".$sql_pref."_sd_sfery AS s, ".$sql_pref."_sd_sfery_arts AS sa WHERE sa.table_name='companies'&&s.id=sa.sfera_id&&sa.art_id='".$id."' ORDER BY s.code";
        $sql_res_1=mysql_query($sql_query, $conn_id);
        if(mysql_num_rows($sql_res_1)>0)
        {
        	while (list($s_id, $s_name)=mysql_fetch_row($sql_res_1))
        	{
        		$s_name=stripslashes($s_name);
                $sfery_array[]="".$s_name."";
        	}
            if (count($sfery_array)>0) $sfery_show.="<div>".implode(', ', $sfery_array)."</div>";
            if (!empty($sfery_show)) $sfery_show="<tr><td class=companiestable>Сферы деятельности:</td><td class=companiestable>&nbsp;</td><td class=companiestable>".$sfery_show."</td></tr>";
        }
        
        
        $directions_show="";
        $sql_query="SELECT d.id, d.name FROM ".$sql_pref."_sd_directions AS d, ".$sql_pref."_sd_directions_arts AS da WHERE da.table_name='companies'&&d.id=da.direction_id&&da.art_id='".$id."' ORDER BY d.code";
        $sql_res_1=mysql_query($sql_query, $conn_id);
        if(mysql_num_rows($sql_res_1)>0)
        {
        	while (list($d_id, $d_name)=mysql_fetch_row($sql_res_1))
        	{
        		$d_name=stripslashes($d_name);
                $directions_array[]="".$d_name."";;
        	}
            if (count($directions_array)>0) $directions_show.="<div>".implode(', ', $directions_array)."</div>";
            if (!empty($directions_show)) $directions_show="<tr><td class=companiestable>Направления деятельности:</td><td class=companiestable>&nbsp;</td><td class=companiestable>".$directions_show."</td></tr>";
        }
        
        
        $descr=str_replace("\n","<br>",$descr);
		if (!empty($content)) $content_show="<span>".$content."</span>"; else $content_show="<span>".$descr."</span>";
		$page_title=$page_header1=$name;
		$module_name[]=$name; $module_url=$art_url;
		
        $img_show="";
		if (file_exists($path."/files/companies/imgs/".$id.".jpg")) 
		{
			$img_show.="<div style='padding: 10 0;'><img src='/files/companies/imgs/".$id.".jpg' border=0 alt='".$name."'></div>";
		}
		elseif (file_exists($path."/files/companies/imgs/".$id.".gif")) 
		{
			$img_show.="<div style='padding: 10 0;'><img src='/files/companies/imgs/".$id.".gif' border=0 alt='".$name."'></div>";
		}
		
        
        $staff_show="";
        $sql_query="SELECT id, surname, name, name2 FROM ".$sql_pref."_users WHERE company_id='".$id."' and enable='Yes' ORDER BY surname, name";
        $sql_res_1=mysql_query($sql_query, $conn_id);
        if(mysql_num_rows($sql_res_1)>0)
        {
            $staff_show.="<div style='padding: 10 0;'>";
            $staff_show.="<div style='padding: 5 0;font-size:14px;'><b>Сотрудники организации, зарегистрированные на сайте:</b></div>";
        	while (list($staff_id, $staff_surname, $staff_name, $staff_name2)=mysql_fetch_row($sql_res_1))
        	{
        		$staff_surname=stripslashes($staff_surname); $staff_name=stripslashes($staff_name); $staff_name2=stripslashes($staff_name2);
                $staff_show.="<div>&ndash;&nbsp;<a href='/".$path_users."/".$staff_id.".html'>".$staff_surname." ".$staff_name." ".$staff_name2."</a></div>";
                
        	}
            $staff_show.="</div>";
        }
        
        $top_show="";
        $sql_query="SELECT id, surname, name, name2 FROM ".$sql_pref."_top_management WHERE company_id='".$id."' and enable='Yes' ORDER BY surname, name";
        $sql_res_2=mysql_query($sql_query, $conn_id);
        if(mysql_num_rows($sql_res_2)>0)
        {
            $top_show.="<div style='padding: 10 0;'>";
            $top_show.="<div style='padding: 5 0;font-size:14px;'><b>Топ-менеджмент ".$name.":</b></div>";
        	while (list($top_id, $top_surname, $top_name, $top_name2)=mysql_fetch_row($sql_res_2))
        	{
        		$top_surname=stripslashes($top_surname); $top_name=stripslashes($top_name); $top_name2=stripslashes($top_name2);
                $top_show.="<div>&ndash;&nbsp;<a href='/kb/".$path_top_management."/".$top_id.".html'>".$top_surname." ".$top_name." ".$top_name2."</a></div>";
                
        	}
            $top_show.="</div>";
        }
        
        
        
        
        

        
        $out.=$img_show;
		$out.="<table cellpadding=0 cellspacing=0 border=0 width=100%>";
		$out.="<tr><td width=180 class=companiestable><img src='/img/empty.gif' width=1 height=1 border=0></td><td class=companiestable><img src='/img/empty.gif' width=1 height=1 border=0></td><td class=companiestable><img src='/img/empty.gif' width=1 height=1 border=0></td></tr>";
		$out.=@$name_full_show.@$parent_company_show.@$sub_company_show.@$obj_company_show.@$city_show.@$address_legal_show.@$address_fact_show.@$requisites_show.@$phone_show.@$fax_show.@$email_show.@$site_show.@$sfery_show.@$directions_show;    
		$out.="</table>";
        
        $out.="<div style='padding:30 0 10 0;'>".$content_show."</div>";
		
		$out.=$staff_show;
        
        $out.=$top_show;
        
		$out.=companies_catalog($companies_id, $name);
		$out.=companies_vacancies($companies_id, $name);
		$out.="<br><br><a href='javascript:history.back();'>Назад...</a>";
        
        
        $out.=companies_comments($id);

        
	}
	return ($out);
}




































function companies_comments($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_companies, $months_rus1, $user_id, $user_status, $path_users;
	$out="";
	$out.="<a name=comments></a>";
    $out.="<div style='padding: 25 0 15 0;'>";
	$out.="<h2 style='margin: 3 0 3 0;font-size:18px;'>Комментарии</h2>\n";
	$out.="<table cellpadding=0 cellspacing=0 border=0 width=100% height=1><tr height=1><td height=1 align=center bgcolor=#999999 background='/img/dots-hor.gif'><img src='/img/empty.gif' height=1 border=0></td></tr></table>\n";
	$out.="<div style='padding: 0 0 15 0;'>";
	$sql_query="SELECT id, content, user_id, dt FROM ".$sql_pref."_companies_comments WHERE parent_id='".$parent_id."' ORDER BY dt";
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
	$out.=companies_comments_form($parent_id);
    $out.="</div>";
	return ($out);
}







function companies_comments_form($parent_id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_companies, $user_id;
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







function companies_comments_form_save()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_companies, $user_id, $art_url;
	
	if (isset($_REQUEST['content1']) && !empty($_REQUEST['content1']))
	{
		$dt=date("Y-m-d H:i:s");
        $parent_id=$_REQUEST['parent_id'];
		
		if (isset($_REQUEST['content1'])) $content=AddSlashes(strip_tags($_REQUEST['content1'], '<br>, <b>, <i>, <u>')); else $content="";
		
		$sql_query="INSERT INTO ".$sql_pref."_companies_comments (content, user_id, parent_id, dt) VALUES ('".$content."', '".$user_id."', '".$parent_id."', '".$dt."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		
		header("location:".$_SERVER['REQUEST_URI']."#comments"); exit();
	}
	return;
}







function companies_comments_del()
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_companies, $user_id, $user_status, $art_url;
	$out="";
	
	if (!isset($_REQUEST['comment_id']) || ($_REQUEST['comment_id']<=0)) return;
    
	$sql_query="SELECT id FROM ".$sql_pref."_companies_comments WHERE id='".$_REQUEST['comment_id']."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0 && $user_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	
	$sql_query="DELETE FROM ".$sql_pref."_companies_comments WHERE id='".$_REQUEST['comment_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
    
    $requr=substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],"?"));
	header("location:".$requr."#comments"); exit();
}


//require_once("/admin/companies/admin_companies.php");
//function companies_add_form()
//{
//    include ("/admin/companies/form_companies.inc");
//
//}
//function companies_add_form()
//{
//   global $sql_pref, $conn_id, $path, $page_header1, $path_companies, $user_id, $art_url;
//   // echo "!!!";	  
//   $out.= "
//   <form name='form_name' action='index.html' method='post' enctype='multipart/form-data'>
//     <input type='hidden' name='action' value='companies_save'>
//     <table width='100%' cellpadding='2' cellspacing='2' border='0' bgcolor='#FFFFFF'>
//    	<tr>
//    		<td class='form_left'>Название</td>
//    		<td class='form_main'><input class='form' type='text' name='name'></td>
//    	</tr>
//    	<tr>
//    		<td class='form_left'>Название (eng)</td>
//    		<td class='form_main'><input class='form' type='text' name='name_eng'></td>
//    	</tr>
//    	<tr>
//    		<td class='form_left'>Полное название</td>
//    		<td class='form_main'><input class='form' type='text' name='name_full'></td>
//    	</tr>
//    	
//    	<tr>
//    		<td class='form_left'>Краткое описание</td>
//    		<td class='form_main'><textarea class='form' name='descr' rows='4'></textarea></td>
//    	</tr>    	
//    	<tr>
//    		<td class='form_left'>Юридический адрес</td>
//    		<td class='form_main'><input class='form' type='text' name='address_legal'></td>
//    	</tr>
//    	<tr>
//    		<td class='form_left'>Фактический адрес</td>
//    		<td class='form_main'><input class='form' type='text' name='address_fact'></td>
//    	</tr>
//    	<tr>
//    		<td class='form_left'>Реквизиты</td>
//    		<td class='form_main'><textarea class='form' name='requisites' rows='4'></textarea></td>
//    	</tr>
//    	<tr>
//    		<td class='form_left'>Телефон 1</td>
//    		<td class='form_main'><input class='form' type='text' name='phone1'></td>
//    	</tr>
//    	<tr>
//    		<td class='form_left'>Телефон 2</td>
//    		<td class='form_main'><input class='form' type='text' name='phone2'></td>
//    	</tr>
//    	<tr>
//    		<td class='form_left'>Факс</td>
//    		<td class='form_main'><input class='form' type='text' name='fax'></td>
//    	</tr>
//    	<tr>
//    		<td class='form_left'>E-mail</td>
//    		<td class='form_main'><input class='form' type='text' name='email'></td>
//    	</tr>
//    	<tr>
//    		<td class='form_left'>Сайт</td>
//    		<td class='form_main'><input class='form' type='text' name='site'></td>
//    	</tr>    	
//    	<tr>
//    		<td>&nbsp;</td>
//    		<td style='padding-top:10;'><input class='form_button' type='submit' name='button_submit' value='Сохранить'></td>
//    	</tr>
//    </table>
//    </form>";
//    return ($out);
//}

?>