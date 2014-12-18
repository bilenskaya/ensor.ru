<?php

function groups_users_list()

{
	global $user_id, $sql_pref, $conn_id, $path, $path_users, $path_contacts,$path_companies, $user_status, $art_url;
	$out="";
    if($user_id==0)
    {
        $out=users_faces();
        $out.="<br><div>Полная функциональность телефонной книги доступна только <a href='/auth/register/'>зарегистрированным</a> пользователям </div>";
    }
    else
    {
    	$sql_query="SELECT ".$sql_pref."_groups_users.group_id, ".$sql_pref."_users.id, ".$sql_pref."_users.pass, ".$sql_pref."_users.surname,
         ".$sql_pref."_users.name, ".$sql_pref."_users.name2, ".$sql_pref."_users.email, 
         ".$sql_pref."_users.phone_work, ".$sql_pref."_users.pol, ".$sql_pref."_users.dt_birth, 
         ".$sql_pref."_users.dt_reg, ".$sql_pref."_users.company_id, ".$sql_pref."_users.doljnost, 
         ".$sql_pref."_users.expirience, ".$sql_pref."_users.vuz, ".$sql_pref."_users.specialnost, 
         ".$sql_pref."_users.last_visit, ".$sql_pref."_users.rate_main, ".$sql_pref."_users.rate_sec
         FROM ".$sql_pref."_groups_users INNER JOIN ".$sql_pref."_users ON (".$sql_pref."_groups_users.user_id=".$sql_pref."_users.id) WHERE ".$sql_pref."_groups_users.group_id='".$art_url."' ";
    	//echo $sql_query;
        $sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
    	{
    		$num_users=mysql_num_rows($sql_res);
    		$out.="<table cellpadding=5 cellspacing=0 border=0>";
    		$out.="
    			<tr><td colspan=5 style='border-bottom:solid 1px #777777;'>&nbsp;</td></tr>
    			<tr bgcolor='#f2f2f2'>
    				<td width=250 align=left style='border-bottom:solid 1px #777777;'>ФИО</td>
    				<td width=100 align=left valign=top style='border-bottom:solid 1px #777777;'>Компания</td>
    				<td width=120 align=center valign=top style='border-bottom:solid 1px #777777;'>Регистрация</td>";
            if($user_status=="admin") $out.= "<td width=100 align=center valign=top style='border-bottom:solid 1px #777777;'>Последнее посещение</td>";
            $out.= "<td width=50 align=center valign=top style='border-bottom:solid 1px #777777;'>Баллы</td>
    			</tr>";
     
    		while(list($group_id, $id, $pass, $surname, $name, $name2, $email, $phone_work, $pol, $dt_birth, $dt_reg, $company_id, $doljnost, $expirience, $vuz, $specialnost, $last_visit, $rate_main, $rate_sec)=mysql_fetch_row($sql_res))
    		{
        		$surname=stripslashes($surname); $name=stripslashes($name); $name2=stripslashes($name2); $phone_work=stripslashes($phone_work); $phone_home=stripslashes($phone_home); $phone_mobile=stripslashes($phone_mobile); $doljnost=stripslashes($doljnost); $expirience=stripslashes($expirience); $vuz=stripslashes($vuz); $specialnost=stripslashes($specialnost);
        		$dt_reg_show=date('d.m.Y',strtotime($dt_reg));
                
                $name_show=$surname." ".$name." ".$name2;
                
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
    					<td align=left valign=middle style='border-bottom:solid 1px #777777;'><a href='".$path_www."/".$path_contacts."/".$id.".html'>".$name_show."</a></td>
    					<td align=left valign=middle style='border-bottom:solid 1px #777777;'>".$company_name."</td>
    					<td align=center valign=middle style='border-bottom:solid 1px #777777;'>".$dt_reg_show."</td>";
                if($user_status=="admin") $out.= "<td align=center valign=middle style='border-bottom:solid 1px #777777;'>".$last_visit."</td>";
                $out.=" <td align=center valign=middle style='border-bottom:solid 1px #777777;'>".$rate_sec."</td>
    				</tr>
    			";
    
    		}
    		$out.="</table>";
    		$out.="<br>Всего: ".$num_users;
    	}
    }
    
	return ($out);
  
}

function groups_users_invite()
{
    global $art_url, $user_name_show;
    
    $name_show="Пригласить пользователя в группу";
    $user_to_out="Укажите фамилию пользователя<BR/><BR/><font style='font-size: smaller;'>(функционал работает при поддержке браузером javascript. В противном случае используйте телефонную книгу)</font>";
    $user_input_out="<input class='text_input' autocomplete='off' onkeydown='keydown(this, event)'  onkeyup='javascript:search(this,event)' name='message_to_user' id='message_to' style='width:550px;font-size:14px;'>";
    $out.=$xc2_inc."
    	".@$error_info."
        	<form action='' method=post name='messages_add' onsubmit='function() { return false };' id='message_form' enctype='multipart/form-data'>
            <input type='hidden' id='what_to_do' name='what_to_do' value='group_invite_user'>
            <input type='hidden' id='to_group' name='to_group' value='".$art_url."'>            
            <table>
                <tr>
                    <td></td>
                    <td>".$name_show."</td>
                </tr>
                <tr style='display: none'>
                    <td width='40%'><div>Кому:</div></td>
                    <td><div><input type='hidden' id='selected_button' name='selected_button' value='to_user'><input type=radio name=type_to value='to_user' checked onclick='display_div(\"to_user\")'> Пользователю &nbsp;&nbsp;&nbsp;</input> <input type=radio name=type_to value='to_org' onclick='display_div(\"to_org\")'> Пользователям организации &nbsp;&nbsp;&nbsp;</input></div></td>
                </tr>
                <tr>
                    <td><div id='div_to'>".$user_to_out."</div></td>
                    <td><div><input type='hidden' id='to_id' name='to_id' value='".$user_id_to."'>".$user_input_out."</div></td>
                </tr>
                <tr><td></td><td><div class='resultdropdown' id='result' style='position:absolute'></div></td></tr>
                <tr>
                    <td></td>
                    <td><div><input type='hidden' name=content id=content value='Пользователь ".$user_name_show." приглашает Вас присоединиться к группе'></td>
                </tr>		
                <tr>
                     <td></td><td><div><input type='submit' name=submit id=submit_button value=Пригласить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;' onclick='check_form();'></div></td>
                </tr>
             </table>
    	</form>
        ".$content_script."
    	";	
    return $out;
}

function groups_users_invite_accept($group_id, $to_user_id)
{
    global $sql_pref, $conn_id, $user_id, $user_admin, $path, $page_header1;
	global $rub_url, $auth, $art_url;
    
    $sql_query="SELECT ".$sql_pref."_groups_users.group_id
     FROM ".$sql_pref."_groups_users WHERE ".$sql_pref."_groups_users.group_id='".$art_url."' and ".$sql_pref."_groups_users.user_id='".$to_user_id."' ";
	//echo $sql_query;
    $sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0)
	{
        $sql_query1="INSERT INTO ".$sql_pref."_groups_users (group_id, user_id, status) VALUES ('".$group_id."', '".$to_user_id."', 'user')";
        $sql_res=mysql_query($sql_query1, $conn_id);
    }
    
    header("location:/auth/groups_list/".$group_id.".html?action=users_show"); exit();
    
}


function groups_messages_list()
{
    global $sql_pref, $conn_id, $user_id, $user_admin, $path, $path_www, $page_header1, $art_url;
	$out="";
    
    if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
		
    $perpage=5; $first=$perpage*($page-1);
	$sql_query="
        SELECT ".$sql_pref."_groups_messages.id, ".$sql_pref."_groups_messages.user_id, ".$sql_pref."_groups_messages.content, ".$sql_pref."_groups_messages.dt, ".$sql_pref."_users.name, ".$sql_pref."_users.surname 
FROM ".$sql_pref."_groups_messages LEFT JOIN ".$sql_pref."_users ON ".$sql_pref."_groups_messages.user_id=".$sql_pref."_users.id WHERE ".$sql_pref."_groups_messages.group_id='".$art_url."' ORDER BY dt DESC";                 
    $args="&action=messages_show";
    $pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums_with_args($page, $perpage, $sql_query,$args)."</div>";  	

	$sql_query="
        SELECT ".$sql_pref."_groups_messages.id, ".$sql_pref."_groups_messages.user_id, ".$sql_pref."_groups_messages.content, ".$sql_pref."_groups_messages.dt, ".$sql_pref."_users.name, ".$sql_pref."_users.surname 
FROM ".$sql_pref."_groups_messages LEFT JOIN ".$sql_pref."_users ON ".$sql_pref."_groups_messages.user_id=".$sql_pref."_users.id WHERE ".$sql_pref."_groups_messages.group_id='".$art_url."' ORDER BY dt DESC LIMIT ".$first.",".$perpage;
	
    $sql_res=mysql_query($sql_query, $conn_id);
    //echo $sql_query;
	if (mysql_num_rows($sql_res)>0)
	{	       
	    $out.="<div style='padding: 0 0 0 0;'>";
		while(list($id, $main_user, $content, $dt, $name, $surname)=mysql_fetch_row($sql_res))
		{
			$user_name_show=$name." ".$surname;
            $content=stripslashes($content); $content=str_replace("\n", "<br>", $content);
            $descr_show=$content;
			$dt_show=date("d.m.Y H:i:s",strtotime($dt));
	         		

            $main_user_show="Пользователь ".$user_name_show." написал:";
			$out.="<div style='padding: 10 0;'>";
			$out.=" 
                    <div style='padding: 1 0 1 0;font-size:12px;'><b>Дата сообщения:</b> ".$dt_show."</div>
    				<div style='padding: 1 0 1 0;font-size:12px;font-weight:normal;'><b>".$main_user_show."</b><br>".$descr_show."<hr></div>
			";
			$out.="</div>";
		}
		$out.="</div>";
	}
	if(isset($user_id) AND $user_id!=0 AND $user_to_id!=0) $out.="<div style='padding: 15 0 5 0;'><a href='/auth/messages_add/?user_id_to=".$user_to_id."'>Написать сообщение</a></div>"; 
	$out.=$pages_show; 
    
	//if($user_to_id!=0) $page_header1="Переписка с ".$user_name_show; else $page_header1="Cообщения группы";
	return ($out);
}

function groups_message_add()
{
    global $art_url, $user_name_show;
    
    $name_show="Написать группе";
    $out.="
        	<form action='' method=post name='messages_add' onsubmit='function() { return false };' id='message_form' enctype='multipart/form-data'>
            <input type='hidden' id='what_to_do' name='what_to_do' value='group_send_message'>
            <input type='hidden' id='to_group' name='to_group' value='".$art_url."'>            
            <table>
                <tr>
                    <td></td>
                    <td>".$name_show."</td>
                </tr>
                <tr style='display: none'>
                    <td width='40%'><div>Кому:</div></td>
                    <td><div><input type='hidden' id='selected_button' name='selected_button' value='to_user'><input type=radio name=type_to value='to_user' checked onclick='display_div(\"to_user\")'> Пользователю &nbsp;&nbsp;&nbsp;</input> <input type=radio name=type_to value='to_org' onclick='display_div(\"to_org\")'> Пользователям организации &nbsp;&nbsp;&nbsp;</input></div></td>
                </tr>
                <tr>
                    <td>Текст сообщения</td>
                    <td><div><textarea name=content id=content rows=12 style='width:550px; height:350px; font-size:14px;'>".@$content."</textarea></div></td>
                </tr>		
                <tr>
                     <td></td><td><div><input type='submit' name=submit id=submit_button value=Отправить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;' onclick='check_form();'></div></td>
                </tr>
             </table>
    	</form>
        ".$content_script."
    	";	
    return $out;
}

function groups_message_add_accept($group_id, $content)
{
    global $sql_pref, $conn_id, $user_id, $user_admin, $path, $page_header1;
	global $rub_url, $auth, $art_url, $user_name, $user_surname, $path_www, $path_groups;
    
    
    
    $sql_query1="INSERT INTO ".$sql_pref."_groups_messages (group_id, user_id, content) VALUES ('".$group_id."', '".$user_id."', '".$content."')";
    $sql_res=mysql_query($sql_query1, $conn_id);
    $last_message_id=mysql_insert_id();
    
    $sql_query="SELECT ".$sql_pref."_groups_users.user_id, ".$sql_pref."_users.name, ".$sql_pref."_users.surname 
                FROM ".$sql_pref."_groups_users LEFT JOIN ".$sql_pref."_users ON ".$sql_pref."_groups_users.user_id=".$sql_pref."_users.id WHERE NOT ".$sql_pref."_groups_users.user_id='".$user_id."' AND ".$sql_pref."_groups_users.group_id='".$art_url."'";                 
    //echo $sql_query;
    $sql_res=mysql_query($sql_query, $conn_id);
    if(mysql_num_rows($sql_res)>0)
    {        
        while(list($group_user_id, $name, $surname)=mysql_fetch_row($sql_res))
    	{
    	    $content="Пользователь ".$user_surname." ".$user_name." написал(а) новое сообщение в <a href='".$path_www.$path_groups."/".$group_id.".html?action=messages_show'>группе</a> (".$path_www.$path_groups."/".$group_id.".html?action=messages_show).<br>";       
            //echo $content;
            auth_group_messages($group_user_id,addslashes($content),$path);
        }
    }
    header("location:/auth/groups_list/".$group_id.".html?action=messages_show"); exit();
    
}

?>