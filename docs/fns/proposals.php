<?php


function proposals_main()
{
	global $sql_pref, $conn_id, $art_url;
	$out="";
    
	if (isset($art_url)) $out.=proposals_out();
	else $out.=proposals_list();
	
	return ($out);
}










function proposals_list()
{
	global $sql_pref, $conn_id, $path,$path_contacts, $path_proposals, $path_companies, $proposals_perpage, $user_id;
	$out="";
	
	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	$perpage=$proposals_perpage; $first=$perpage*($page-1);
	$sql_query="SELECT id FROM ".$sql_pref."_proposals WHERE enable='Yes'";
	$pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums($page, $perpage, $sql_query)."</div>";

	$sql_query="SELECT ".$sql_pref."_proposals.id, ".$sql_pref."_proposals.dt, ".$sql_pref."_proposals.descr, ".$sql_pref."_proposals.content, ".$sql_pref."_proposals.user_id, ".$sql_pref."_proposals.company_id, ".$sql_pref."_users.rate_sec, ".$sql_pref."_proposals.top_time FROM ".$sql_pref."_proposals INNER JOIN ".$sql_pref."_users ON ".$sql_pref."_proposals.user_id=".$sql_pref."_users.id WHERE ".$sql_pref."_proposals.enable='Yes' AND ".$sql_pref."_users.enable='Yes' ORDER BY ".$sql_pref."_proposals.top_time DESC, ".$sql_pref."_proposals.dt DESC LIMIT ".$first.",".$perpage;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $dt, $descr, $content, $p_user_id, $company_id, $rate_sec, $top_time)=mysql_fetch_row($sql_res))
		{
			$descr=stripslashes($descr);$descr=str_replace("\n", "<br>", $descr);
			
			$dt_show="<div style='padding: 3 0;'>Дата создания: <span style='padding: 2 4;background-color:#eee;'>".date("d.m.Y", strtotime($dt))."</span>";
            if($top_time!='0000-00-00 00:00:00') $dt_show.="Дата поднятия вверх: <span style='padding: 2 4;background-color:#eee;'>".$top_time."</span></div>";
            $descr_show="<div style='padding: 3 0;'>".$descr."</div>";
            $more_show="<div style='padding: 3 0;'><a href='".$id.".html'>Подробнее...</a></div>";
            
            $sql_query="SELECT CONCAT_WS(' ', name, surname, '[рейтинг', rate_sec, ']'), company_id FROM ".$sql_pref."_users WHERE id='".$p_user_id."'";
        	$sql_res_1=mysql_query($sql_query, $conn_id);
        	list($p_user_name, $company_id)=mysql_fetch_row($sql_res_1);

            $company_name_show="";
            $sql_query="SELECT id,name FROM ".$sql_pref."_companies WHERE id='".$company_id."'";
        	$sql_res_1=mysql_query($sql_query, $conn_id);
            if (mysql_num_rows($sql_res_1)>0)
            {
        	   list($company_id,$company_name)=mysql_fetch_row($sql_res_1);
               $company_name=stripslashes($company_name);
               $company_name_show="<span style='padding: 0 0 0 10;'>(<a href='/".$path_companies."/".$company_id.".html'>".$company_name."</a>)</span>";
            }
            
            $p_user_show="<div style='padding: 3 0;font-size:18px;'><a href='/".$path_contacts."/".$p_user_id.".html'>".$p_user_name."</a></div>";

            $img_show="";
        	if (file_exists($path."files/users/avatar/".$p_user_id.".jpg") && $p_user_id>0)
        		$img_show="<div style='padding:0;'><img src='/files/users/avatar/".$p_user_id.".jpg' border=0></div>";

			
            
            
			$out.="<div style='padding: 5 0 10 0;'>";
			
			$out.=" <table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<td valign=middle>".$p_user_show.$company_name_show.$dt_show.$descr_show.$more_show."</td>
							<td valign=middle align=center width=100>".$img_show."</td>
						</tr>
					</table>";
			
			$out.="</div>";
		}
		
	}
	
	$out.=$pages_show;
	
	return ($out);
}










function proposals_out()
{
	global $sql_pref, $conn_id, $path, $art_url, $path_proposals, $path_users, $path_companies;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
	
 	$sql_query="SELECT id, dt, descr, content, tags, sfera_ids, direction_ids, user_id, company_id FROM ".$sql_pref."_proposals WHERE id='".$art_url."' AND enable='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $dt, $descr, $content, $tags, $sfera_ids, $direction_ids, $p_user_id, $company_id)=mysql_fetch_row($sql_res);
		$descr=stripslashes($descr); $content=stripslashes($content);
        $descr=str_replace("\n", "<br>", $descr);
		
		$dt_show="<div style='padding: 5 0;'>".date("d.m.Y", strtotime($dt))."</div>";
        if (!empty($content)) $content_show="<div style='padding: 3 0;'>".$content."</div>"; else $content_show="<div style='padding: 3 0;'>".$descr."</div>";
        
        $sql_query="SELECT CONCAT_WS(' ', name, surname), company_id FROM ".$sql_pref."_users WHERE id='".$p_user_id."'";
    	$sql_res_1=mysql_query($sql_query, $conn_id);
    	list($p_user_name, $company_id)=mysql_fetch_row($sql_res_1);

        $company_name_show="";
        $sql_query="SELECT name FROM ".$sql_pref."_companies WHERE id='".$company_id."'";
    	$sql_res_1=mysql_query($sql_query, $conn_id);
        if (mysql_num_rows($sql_res_1)>0)
        {
    	   list($company_name)=mysql_fetch_row($sql_res_1);
           $company_name=stripslashes($company_name);
           $company_name_show="<span style='padding: 0 0 0 10;'>(".$company_name.")</span>";
        }
        
        $p_user_show="<div style='padding: 3 0;font-size:18px;'>".$p_user_name."</div>";

        $img_show="";
    	if (file_exists($path."files/users/avatar/".$p_user_id.".jpg") && $p_user_id>0)
    		$img_show="<div style='padding:0;'><img src='/files/users/avatar/".$p_user_id.".jpg' border=0></div>";

		
        
        $sfery_show="";
        $sfery_list=explode(";",$sfera_ids);
        $sql_query="SELECT id, name FROM ".$sql_pref."_sd_sfery ORDER BY code";
        $sql_res_1=mysql_query($sql_query, $conn_id);
        if(mysql_num_rows($sql_res_1)>0)
        {
        	while (list($s_id, $s_name)=mysql_fetch_row($sql_res_1))
        	{
        		$s_name=stripslashes($s_name);
                if (isset($sfery_list) && in_array($s_id, $sfery_list)) $sfery_array[]="<li>".$s_name."</li>";
        	}
            if (count($sfery_array)>0) $sfery_show.="<div>".implode('', $sfery_array)."</div>";
            if (!empty($sfery_show)) $sfery_show="<div style='padding: 10 0 0 0;font-weight:bold;'>Сферы деятельности:</div><ul>".$sfery_show."</ul>";
        }
        
        $directions_show="";
        $directions_list=explode(";",$direction_ids);
        $sql_query="SELECT id, name FROM ".$sql_pref."_sd_directions ORDER BY code";
        $sql_res_1=mysql_query($sql_query, $conn_id);
        if(mysql_num_rows($sql_res_1)>0)
        {
        	while (list($d_id, $d_name)=mysql_fetch_row($sql_res_1))
        	{
        		$d_name=stripslashes($d_name);
                if (isset($directions_list) && in_array($d_id, $directions_list))  $directions_array[]="<li>".$d_name."</li>";
        	}
            if (count($directions_array)>0) $directions_show.="<div>".implode('', $directions_array)."</div>";
            if (!empty($directions_show)) $directions_show="<div style='padding: 10 0 0 0;font-weight:bold;'>Направления деятельности:</div><ul>".$directions_show."</ul>";
        }
        
        
        
        $addinfo="<div style='padding: 10 0 0 0;font-weight:bold;'>Дополнительно:</div>";
        $addinfo.="<ul>";
        $addinfo.="<li>Профиль пользователя <a href='/".$path_users."/".$p_user_id.".html'>".$p_user_name."</a> на нашем сайте</li>";
        if ($company_id>0) $addinfo.="<li>Профиль компании <a href='/".$path_companies."/".$company_id.".html'>".$company_name."</a> на нашем сайте</li>";
        $addinfo.="</ul>";
        
        
        
        
        
		$out.="<div style='padding: 5 0 10 0;'>";
		
		$out.=" <table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<td valign=top>".$p_user_show.$dt_show."</td>
						<td valign=top align=center width=100>".$img_show."</td>
					</tr>
				</table>";
		
        $out.=$content_show.$sfery_show.$directions_show.$addinfo;
		$out.="</div>";
    
    
        //$out.="<div style='padding: 30 0 10 0;'>".proposals_feedback($id)."</div>";
        
		$out.="<div style='padding:50 0 20 0;'><a href='/".$path_proposals."/'>К списку предложений...</a></div>";
        
	}
	return ($out);
}
















?>