<?php


function vacancies_main()
{
	global $sql_pref, $conn_id, $art_url;
	$out="";
    
	if (isset($art_url)) $out.=vacancies_out();
	else $out.=vacancies_list();
	
	return ($out);
}










function vacancies_list()
{
	global $sql_pref, $conn_id, $path, $path_vacancies, $vacancies_perpage, $user_id;
    $add_link="<table><tr><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=vacancies_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><img width=24px src='/img/add.png' border=0></td><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=vacancies_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><font style='vertical-align: inherit;'>Добавьте свою вакансию</font></td><td width=80></td><td><a href='http://www.ensor.ru/rabotodateljam/'><img width=24px src='/img/question_big.png' border=0></a></td><td><a href='http://www.ensor.ru/rabotodateljam/'><font style='vertical-align: inherit;'>Узнайте про дополнительные возможности!</font></a></td></tr></table>";
    $out="";
    $out.=$add_link."<br>";
	$out.="<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
                            <td width=20%> </td>
                            <td width=40%> </td>
                            <td width=20%> </td>
                            <td width=20%> </td>
						</tr>";
	
	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	$perpage=$vacancies_perpage; $first=$perpage*($page-1);
	$sql_query="SELECT id FROM ".$sql_pref."_vacancies WHERE enable='Yes'";
	$pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums($page, $perpage, $sql_query)."</div>";

	$sql_query="SELECT id, dt, name, descr, content, user_id, company_id, zp_value_min, zp_value_max, zp_valuta FROM ".$sql_pref."_vacancies WHERE enable='Yes' ORDER BY dt DESC, id DESC LIMIT ".$first.",".$perpage;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $dt, $name, $descr, $content, $p_user_id, $company_id, $zp_value_min, $zp_value_max, $zp_valuta)=mysql_fetch_row($sql_res))
		{
			$descr=stripslashes($descr);$descr=str_replace("\n", "<br>", $descr);
			
			$dt_show="<div style='padding: 3 0;'><span style='padding: 2 4;background-color:#eee;'>".date("d.m.Y", strtotime($dt))."</span></div>";
            $name_show="<div style='padding: 3 0;'>".$name."</div>";
            $more_show="<div style='padding: 3 0;'><a href='".$id.".html'>Подробнее...</a></div>";
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
            $sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$p_user_id."'";
        	$sql_res_1=mysql_query($sql_query, $conn_id);
        	list($p_user_name)=mysql_fetch_row($sql_res_1);

            //$company_name_show="";
            //$sql_query="SELECT name FROM ".$sql_pref."_companies WHERE id='".$company_id."'";
        	//$sql_res_1=mysql_query($sql_query, $conn_id);
            //if (mysql_num_rows($sql_res_1)>0)
            //{
        	//   list($company_name)=mysql_fetch_row($sql_res_1);
            //   $company_name=stripslashes($company_name);
            //   $company_name_show="<span style='padding: 0 0 0 10;'>(".$company_name.")</span>";
            //}
            
            $p_user_show="<div style='padding: 3 0;font-size:18px;'>".$p_user_name."</div>";

            //$img_show="";
        	//if (file_exists($path."files/users/avatar/".$p_user_id.".jpg") && $p_user_id>0)
        	//	$img_show="<div style='padding:0;'><img src='/files/users/avatar/".$p_user_id.".jpg' border=0></div>";

			
            
            

			
			$out.="     <tr><td></br></td><td></td><td></td><td></td></tr>	
                        <tr>
                            <td valign=middle>".$dt_show."</td>
                            <td valign=middle>".$name_show." ".$zp."</td>
                            <td valign=middle>".$zp_show."</td>
                            <td valign=middle>".$more_show."</td>
						</tr>
                        <tr><td></br></td><td></td><td></td><td></td></tr>";

		}
		
	}
	$out.= "</table>";
    //$add_link="<div style='padding:2 0 2 0;font-size:11px;color:#555;'>Если вы хотите разместить вакансию, <span style='font-size:11px;cursor:pointer;text-decoration:underline;' onClick=\"window.open('/feedback.html?subj=vacancies_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\">сообщите нам</span> и она будет добавлена на сайт.</div>";
	$out.=$pages_show;
    $out.=$add_link;
	
	return ($out);
}










function vacancies_out()
{
	global $sql_pref, $conn_id, $path, $art_url, $path_vacancies, $path_users, $path_companies;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
	   
 	$sql_query="SELECT id, dt, name, descr, content, user_id, company_id, zp_value_min, zp_value_max, zp_valuta FROM ".$sql_pref."_vacancies WHERE id='".$art_url."' AND enable='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $dt, $name, $descr, $content, $p_user_id, $company_id, $zp_value_min, $zp_value_max, $zp_valuta)=mysql_fetch_row($sql_res);
		$descr=stripslashes($descr); 
        $content=stripslashes($content);
        $descr=str_replace("\n", "<br>", $descr);
        $content=str_replace("\n", "<br>", $content);
        
		$name=stripslashes($name); $name=stripslashes($name);
        $name=str_replace("\n", "<br>", $name);
		
		$dt_show=date("d.m.Y", strtotime($dt));
        if (!empty($content)) $content_show="<div style='padding: 3 0;'>".$content."</div>"; else $content_show="<div style='padding: 3 0;'>".$content."</div>";
        if (!empty($name)) $name_show="<div style='padding: 3 0;'><h3>".$name."</h3></div>"; else $name_show="<div style='padding: 3 0;'>".$name."</div>";
        
        $sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$p_user_id."'";
    	$sql_res_1=mysql_query($sql_query, $conn_id);
    	list($p_user_name)=mysql_fetch_row($sql_res_1);

        $company_name_show="";
        $sql_query="SELECT name FROM ".$sql_pref."_companies WHERE id='".$company_id."'";
    	$sql_res_1=mysql_query($sql_query, $conn_id);
        if (mysql_num_rows($sql_res_1)>0)
        {
    	   list($company_name)=mysql_fetch_row($sql_res_1);
           $company_name=stripslashes($company_name);
           $company_name_show="<span style='padding: 0 0 0 10;'>вакансия ".$company_name."</span>";
        }
        
        $p_user_show="<div style='padding: 3 0;font-size:18px;'>".$p_user_name.$company_name_show."</div>";

        $img_show="";
    	if (file_exists($path."files/users/avatar/".$p_user_id.".jpg") && $p_user_id>0)
    		$img_show="<div style='padding:0;'><img src='/files/users/avatar/".$p_user_id.".jpg' border=0></div>";

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
           $zp_show=" не указан"; 
        } 
        else
        {
            $zp_show.=" ".$zp_valuta;
        }
        
        //$sfery_show="";
        //$sfery_list=explode(";",$sfera_ids);
        //$sql_query="SELECT id, name FROM ".$sql_pref."_sd_sfery ORDER BY code";
        //$sql_res_1=mysql_query($sql_query, $conn_id);
        //if(mysql_num_rows($sql_res_1)>0)
        //{
        //	while (list($s_id, $s_name)=mysql_fetch_row($sql_res_1))
        //	{
        //		$s_name=stripslashes($s_name);
        //        if (isset($sfery_list) && in_array($s_id, $sfery_list)) $sfery_array[]="<li>".$s_name."</li>";
        //	}
        //    if (count($sfery_array)>0) $sfery_show.="<div>".implode('', $sfery_array)."</div>";
        //    if (!empty($sfery_show)) $sfery_show="<div style='padding: 10 0 0 0;font-weight:bold;'>Сферы деятельности:</div><ul>".$sfery_show."</ul>";
        //}
        
        //$directions_show="";
        //$directions_list=explode(";",$direction_ids);
        //$sql_query="SELECT id, name FROM ".$sql_pref."_sd_directions ORDER BY code";
        //$sql_res_1=mysql_query($sql_query, $conn_id);
        //if(mysql_num_rows($sql_res_1)>0)
        //{
        //	while (list($d_id, $d_name)=mysql_fetch_row($sql_res_1))
        //	{
        //		$d_name=stripslashes($d_name);
        //        if (isset($directions_list) && in_array($d_id, $directions_list))  $directions_array[]="<li>".$d_name."</li>";
        //	}
        //    if (count($directions_array)>0) $directions_show.="<div>".implode('', $directions_array)."</div>";
        //    if (!empty($directions_show)) $directions_show="<div style='padding: 10 0 0 0;font-weight:bold;'>Направления деятельности:</div><ul>".$directions_show."</ul>";
        //}
        
        
        
        $addinfo="<div style='padding: 10 0 0 0;font-weight:bold;'>Дополнительно:</div>";
        $addinfo.="<ul>";
        //$addinfo.="<li>Профиль пользователя <a href='/".$path_users."/".$p_user_id.".html'>".$p_user_name."</a> на нашем сайте</li>";
        if ($company_id>0) $addinfo.="<li>Профиль компании <a href='/".$path_companies."/".$company_id.".html'>".$company_name."</a> на нашем сайте</li>";
        $addinfo.="</ul>";
        
        
        
        
        
		$out.="<div style='padding: 5 0 10 0;'>";
		
		$out.=" <table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<td valign=top><h2>".$dt_show.$company_name_show."</h2></td>
						<td valign=top align=left width=30%><h3>Доход: ".$zp_show."</h3></td>
					</tr>
				</table>";
		
        $out.=$name_show.$content_show.$sfery_show.$directions_show.$addinfo;
		$out.="</div>";
    
    
        //$out.="<div style='padding: 30 0 10 0;'>".vacancies_feedback($id)."</div>";
        
		$out.="<div style='padding:50 0 20 0;'><a href='/".$path_vacancies."/'>К списку вакансий...</a></div>";
        
	}
	return ($out);
}





function vacancies_line($cnt)
{
	global $sql_pref, $conn_id, $path, $path_vacancies, $vacancies_perpage, $user_id;
	$add_link="<table><tr><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=vacancies_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><img width=24px src='/img/add.png' border=0></td><td style='cursor: pointer;' onClick=\"window.open('/feedback.html?subj=vacancies_add_new', 'Feedback','toolbar=no, scrollbars=yes, width=400, height=550, resizable=yes, menubar=no'); return false;\"><font style='vertical-align: inherit;'>Добавьте свою вакансию</font></td><td width=80></td><td><a href='http://www.ensor.ru/rabotodateljam/'><img width=24px src='/img/question_big.png' border=0></a></td><td><a href='http://www.ensor.ru/rabotodateljam/'><font style='vertical-align: inherit;'>Узнайте про дополнительные возможности!</font></a></td></tr></table>";
        
	$sql_query="SELECT id, dt, name, descr, content, user_id, company_id, zp_value_min, zp_value_max, zp_valuta FROM ".$sql_pref."_vacancies WHERE enable='Yes' ORDER BY dt DESC, id DESC LIMIT ".$cnt;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        
        $out.="<table cellpadding=0 cellspacing=0 border=0 width='100%' background='/img_new/int/main-bg.gif'>
                <tr>
                    <td valign=top background='/img_new/int/main-top.gif' style='background-image: /img_new/int/main-top.gif; background-position: top; background-repeat: repeat-x;'>
                        <div style='padding: 0 10 10 10;'>
                        <h2 style='padding: 0 0 10 0;'>Вакансии для энергетиков</h2>
                        ";       
        
		while(list($id, $dt, $name, $descr, $content, $p_user_id, $company_id, $zp_value_min, $zp_value_max, $zp_valuta)=mysql_fetch_row($sql_res))
		{
			$descr=stripslashes($descr);$descr=str_replace("\n", "<br>", $descr);
		    $out.="<span class=dates>".date("d.m.Y", strtotime($dt))."</span>&nbsp;<a href='/".$path_vacancies."/".$id.".html' style='text-decoration:underline;'>".$name."</a><br><br>";
		}
        $out.="<div><a href='/".$path_vacancies."/'>Все вакансии</a></div><br>".$add_link;
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









?>