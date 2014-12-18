<?php


function objects_main()
{
	global $sql_pref, $conn_id, $art_url;
	$out="";
	
	if (isset($art_url)) $out.=objects_out();
	else $out.=objects_list();
	
	return ($out);
}







function objects_list()
{
	global $sql_pref, $conn_id, $path, $path_objects, $objects_perpage, $page_header1, $page_title;
	$out="";
    
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$objects_perpage).",".$objects_perpage."";
    
    if (isset($_REQUEST['letter']) && !empty($_REQUEST['letter'])) $curletter=$_REQUEST['letter'];
    

    
    
	$sql_query="SELECT SUBSTRING(name,1,1) FROM ".$sql_pref."_objects GROUP BY SUBSTRING(name,1,1)";
	$sql_res=mysql_query($sql_query, $conn_id);
	$letters_ru_array=mysql_fetch_array($sql_res);
    while(list($letters_ru_array[])=mysql_fetch_row($sql_res));
    
    
	$letters_ru_list="";
	$letters_ru_list.="<div align='left' style='padding: 0 0 0 0;'><table cellpadding=0 cellspacing=0 border=0 height=34><tr>";
    for ($i=ord('А'); $i<=ord('Я'); $i++)
    {
        $letter=chr($i);
        if (isset($curletter) && rawurldecode($curletter)==$letter) $letter_show="<a class=letters href='./'><span style='font-weight:bold;font-size:24px;'>".$letter."</span></a>";
        elseif (in_array($letter,$letters_ru_array)) $letter_show="<a class=letters href='?letter=".rawurlencode($letter)."'><span style='font-weight:normal;'>".$letter."</span></a>";
        else $letter_show="<a class=letters href='?letter=".rawurlencode($letter)."'><span style='font-weight:normal;color:#aaa;'>".$letter."</span></a>";
                
        //$letters_ru_list.="<td align='center' valign='middle'><nobr><a href='?letter=".rawurlencode($letter)."' style='font-size:14px;text-decoration:none;'><span style='border:solid 1px #ccc; padding:1 3 1 3;background-color:#fff;text-transform:uppercase;'>".$letter_show."</span></a></nobr></td>";
        $letters_ru_list.="<td align='center' valign='middle' width=22><nobr>".$letter_show."</nobr></td>";
    }
	$letters_ru_list.="</tr></table></div>";
    
    $out.=$letters_ru_list;
    
    $out.="<br>";

    $letter_sql="";
	if (isset($curletter) && !empty($curletter))
    {
        if (ord($curletter)>=ord('А') && ord($curletter)<=ord('Я')) $letter_sql="&&SUBSTRING(name,1,1)='".rawurldecode($curletter)."'";
        //elseif (ord($curletter)>=ord('A') && ord($curletter)<=ord('Z')) $letter_sql="&&SUBSTRING(name_eng,1,1)='".rawurldecode($curletter)."'";
    } 




    
    $sql_query="SELECT id, name, descr, content, site FROM ".$sql_pref."_objects WHERE enable='Yes'".$letter_sql." ORDER BY name".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $name, $descr, $content, $site)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);$descr=stripslashes($descr);$content=stripslashes($content);
			if (!empty($name)) $name_show="<div style='font-weight:bold;'><a href='/".$path_objects."/".$id.".html'>".$name."</a></div>"; else $name_show="";
			
			$descr=str_replace("\n","<br>",$descr);
			$descr_show="<div style='font-size:11px;'>".$descr."</div>";
			
			if (!empty($site)) $site_show="<div>".$site."</div>"; else $site_show="";
			
			if (file_exists($path."/files/objects/thumbs/".$id.".jpg") || file_exists($path."/files/objects/thumbs/".$id.".gif")) 
			{
				if (file_exists($path."/files/objects/thumbs/".$id.".jpg")) $ext=".jpg";
				elseif (file_exists($path."/files/objects/thumbs/".$id.".gif")) $ext=".gif";
				$size=getimagesize($path."/files/objects/thumbs/".$id.$ext);
				$img_show="<table cellpadding=3 cellspacing=0 border=0 style='border: solid 0px #999999;' width=120 height=80><tr><td align=center valign=middle><a href='/".$path_objects."/".$id.".html'><img src='/files/objects/thumbs/".$id.$ext."' width='".$size[0]."' height='".$size[1]."' border=0 alt='".$name."'></a></td></tr></table>";
			}
			else $img_show="";
			
			
			
			$out.="<table cellpadding=3 cellspacing=0 border=0 width=100%><tr>";
			//if (!empty($img_show)) $out.="<td valign=middle align=center width=130>".$img_show."</td>";
			
			$out.="<td valign=middle align=left>".$name_show."</td>";
			$out.="</tr></table>";
			$out.="<br>";
		}
		
		$sql_query="SELECT id FROM ".$sql_pref."_objects WHERE enable='Yes'".$letter_sql."";
		$sql_res=mysql_query($sql_query, $conn_id);
		$num_predl=mysql_num_rows($sql_res);
		$numpages=ceil($num_predl/$objects_perpage);
		if ($numpages>1)
		{
			$out.="<br><br><div align=left>Страницы: | ";
			for ($i=1;$i<=$numpages;$i++)
			{
				if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show="<a href='?page=".$i."&letter=".@$_REQUEST['letter']."'>".$i."</a>";
				$out.="<span style='padding:2 1 2 1;'>".$i_show."</span> | ";
			}
			$out.="</div><br>";
		}
	}
	return ($out);
}










function objects_out()
{
	global $sql_pref, $conn_id, $path, $art_url, $path_objects, $path_companies, $path_catalog;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
	$objects_id=$art_url;
 	$sql_query="SELECT id, parent_id, name, name_full, city_id, company_id, address, phone1, phone2, fax, email, site, descr, content, sfera_ids, direction_ids, tags, author_id, enable FROM ".$sql_pref."_objects WHERE id='".$objects_id."' AND enable='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $parent_id, $name, $name_full, $city_id, $company_id, $address, $phone1, $phone2, $fax, $email, $site, $descr, $content, $sfera_ids, $direction_ids, $tags, $author_id, $enable)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); $name_full=stripslashes($name_full); $address=stripslashes($address); $requisites=stripslashes($requisites); $phone1=stripslashes($phone1); $phone2=stripslashes($phone2); $fax=stripslashes($fax); $tags=stripslashes($tags); $descr=stripslashes($descr); $content=stripslashes($content);
        $directions_list=explode(";",$direction_ids);$sfery_list=explode(";",$sfera_ids);
        
        if (!empty($site) && substr($site,0,7)!="http://") $site="http://".$site;
		
        if (!empty($name_full)) $name_full_show="<tr><td class=objectstable width=180>Полное название:</td><td class=objectstable>&nbsp;</td><td class=objectstable>".$name_full."</td></tr>";
        if (!empty($address)) $address_show="<tr><td class=objectstable width=180>Адрес:</td><td class=objectstable>&nbsp;</td><td class=objectstable>".$address."</td></tr>";
        if (!empty($phone1) || !empty($phone2)) $phone_show="<tr><td class=objectstable width=180>Телефоны:</td><td class=objectstable>&nbsp;</td><td class=objectstable>".$phone1." ".$phone2."</td></tr>";
        if (!empty($fax)) $fax_show="<tr><td class=objectstable width=180>Факс:</td><td class=objectstable>&nbsp;</td><td class=objectstable>".$fax."</td></tr>";
        if (!empty($email)) $email_show="<tr><td class=objectstable width=180>E-mail:</td><td class=objectstable>&nbsp;</td><td class=objectstable>".$email."</td></tr>";
        if (!empty($site)) $site_show="<tr><td class=objectstable width=180>Сайт:</td><td class=objectstable>&nbsp;</td><td class=objectstable><a href='".$site."'>".$site."</a></td></tr>";

		
		
        $company_name_show="";
        if ($parent_id>0)
        {
            $sql_query="SELECT name FROM ".$sql_pref."_companies WHERE id='".$company_id."'";
    		$sql_res_1=mysql_query($sql_query, $conn_id);
            if(mysql_num_rows($sql_res_1)>0)
            {
        		list($company_name)=mysql_fetch_row($sql_res_1);
       			$company_name=stripslashes($company_name);
                $company_name_show.="<tr><td class=objectstable width=180>Организация:</td><td class=objectstable>&nbsp;</td><td class=objectstable><a href='/".$path_companies."/".$company_id.".html'>".$company_name."</a></td></tr>";
            }
        }
        
        
        $parent_object_show="";
        if ($parent_id>0)
        {
            $sql_query="SELECT name FROM ".$sql_pref."_objects WHERE id='".$parent_id."'";
    		$sql_res_1=mysql_query($sql_query, $conn_id);
            if(mysql_num_rows($sql_res_1)>0)
            {
        		list($parent_name)=mysql_fetch_row($sql_res_1);
       			$parent_name=stripslashes($parent_name);
                $parent_object_show.="<tr><td class=objectstable width=180>Родительский объект:</td><td class=objectstable>&nbsp;</td><td class=objectstable><a href='".$parent_id.".html'>".$parent_name."</a></td></tr>";
            }
        }
        
        
        $city_show="";
		if (!empty($city_id) && $city_id>0)
		{
            $sql_query="SELECT c.name, r.name FROM ".$sql_pref."_cities AS c, ".$sql_pref."_regions AS r WHERE c.id='".$city_id."'&&c.region_id=r.id ORDER BY c.name";
            $sql_res_1=mysql_query($sql_query, $conn_id);
            list($city_name, $region_name)=mysql_fetch_row($sql_res_1);
           	$city_name=stripslashes($city_name);$region_name=stripslashes($region_name);
            $city_show.="<tr><td class=objectstable width=180>Город:</td><td class=objectstable>&nbsp;</td><td class=objectstable>".$city_name." (".$region_name.")</td></tr>";
		}
        
        
        $sfery_show="";
        $sql_query="SELECT id, name FROM ".$sql_pref."_sd_sfery ORDER BY code";
        $sql_res_1=mysql_query($sql_query, $conn_id);
        if(mysql_num_rows($sql_res_1)>0)
        {
        	while (list($s_id, $s_name)=mysql_fetch_row($sql_res_1))
        	{
        		$s_name=stripslashes($s_name);
                if (isset($sfery_list) && in_array($s_id, $sfery_list)) 
                    $sfery_show.='<div>'.$s_name.'</div>';
        	}
            if (!empty($sfery_show)) $sfery_show="<tr><td class=objectstable width=180>Сферы деятельности:</td><td class=objectstable>&nbsp;</td><td class=objectstable>".$sfery_show."</td></tr>";
        }
        
        $directions_show="";
        $sql_query="SELECT id, name FROM ".$sql_pref."_sd_directions ORDER BY code";
        $sql_res_1=mysql_query($sql_query, $conn_id);
        if(mysql_num_rows($sql_res_1)>0)
        {
        	while (list($d_id, $d_name)=mysql_fetch_row($sql_res_1))
        	{
        		$d_name=stripslashes($d_name);
                if (isset($directions_list) && in_array($d_id, $directions_list)) 
                    $directions_show.='<div>'.$d_name.'</div>';
        	}
            if (!empty($directions_show)) $directions_show="<tr><td class=objectstable width=180>Направления деятельности:</td><td class=objectstable>&nbsp;</td><td class=objectstable>".$directions_show."</td></tr>";
        }
        
        
        $descr=str_replace("\n","<br>",$descr);
		if (!empty($content)) $content_show="<span>".$content."</span>"; else $content_show="<span>".$descr."</span>";
		$page_title=$page_header1=$name;
		$module_name[]=$name; $module_url=$art_url;
		
        $img_show="";
		if (file_exists($path."/files/objects/imgs/".$id.".jpg")) 
		{
			$img_show.="<div style='padding: 10 0;'><img src='/files/objects/imgs/".$id.".jpg' border=0 alt='".$name."'></div>";
		}
		elseif (file_exists($path."/files/objects/imgs/".$id.".gif")) 
		{
			$img_show.="<div style='padding: 10 0;'><img src='/files/objects/imgs/".$id.".gif' border=0 alt='".$name."'></div>";
		}
		

        
        $out.=$img_show;
		$out.="<table cellpadding=0 cellspacing=0 border=0 width=100%>";
		$out.="<tr><td class=objectstable width=180><img src='/img/empty.gif' width=1 height=1 border=0></td><td class=objectstable><img src='/img/empty.gif' width=1 height=1 border=0></td><td class=objectstable><img src='/img/empty.gif' width=1 height=1 border=0></td></tr>";
		$out.=@$name_full_show.@$parent_object_show.@$company_name_show.@$city_show.@$address_show.@$company_show.@$requisites_show.@$phone_show.@$fax_show.@$email_show.@$site_show.@$sfery_show.@$directions_show;    
		$out.="</table>";
        
        $out.="<div style='padding:30 0 10 0;'>".$content_show."</div>";
		
		

		$out.="<br><br><a href='javascript:history.back();'>Назад...</a>";
	}
	return ($out);
}

?>