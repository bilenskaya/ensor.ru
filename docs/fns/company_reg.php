<?php

function company_reg_main()
{
	global $sql_pref, $conn_id, $path;
	global $auth_error_info, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
	$out="";
    
    //echo "123".$user_contact;
    
	if (isset($_REQUEST['action']))
    {
    	if ($_REQUEST['action']=='company_add') {$out.=form_companies_add();}
        else $out.=form_show();
    }
    else
    {
        $out.=form_show();
    }	      
    
	return ($out);
}

function form_companies_add()       // форма сохранения данных об организации (это не подключение услуг!!!)
{
    global $sql_pref, $conn_id, $path, $path_domen;
	global $auth_error_info, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
    $out="";
    $name="";
    
    if (isset($_REQUEST['name_short'])) $name=addslashes($_REQUEST['name_short']); else $name="";
	if (isset($_REQUEST['name_full'])) $name_full=addslashes($_REQUEST['name_full']); else $name_full="";
	if (isset($_REQUEST['city_id'])) $city_id=addslashes($_REQUEST['city_id']); else $city_id=0;
	if (isset($_REQUEST['address_fact'])) $address_fact=addslashes($_REQUEST['address_fact']); else $address_fact="";
	if (isset($_REQUEST['phone1'])) $phone1=addslashes($_REQUEST['phone1']); else $phone1="";
	if (isset($_REQUEST['fax'])) $fax=addslashes($_REQUEST['fax']); else $fax="";
	if (isset($_REQUEST['email'])) $email=addslashes($_REQUEST['email']); else $email="";
	if (isset($_REQUEST['site'])) $site=addslashes($_REQUEST['site']); else $site="";
   
    if($name=='')
    {
        $antispam=show_codepic();
        $out.="
        <SCRIPT>
            function check_form()
    		{
    			var str = 'OK';
                obj=document.getElementById('company_t'); 
                //alert(obj.checked);         
                if (obj.checked==true)
                {
        			if (document.getElementById('name_short').value=='') str='Название';
                    if (document.getElementById('name_full').value=='') str='Полное название';
                    if (document.getElementById('descr').value=='') str='Краткое описание';
                    if (document.getElementById('phone1').value=='') str='Телефон';
                    if (document.getElementById('email').value=='') str='Е-мэйл';    
                    if (document.getElementById('a_s_u').value!=document.getElementById('a_s_t').value) str='Поле антиспама';                                     
                }
                else
                {
                    if (document.getElementById('comp_id').value==0) str='Организация';
                }
    			return str;
    		}
            </SCRIPT>
        ";
        
        $out.="<form name='form_name' method='post' onSubmit='if (check_form()!=\"OK\") {alert(\"Вы не заполнили поле \" + check_form() + \"!\"); return false;} else return true;'><table>";
      
        $form_comp.="<tr><td>Заполните форму данных об организации:<BR>";    
        $form_comp.="<table><tr>
                        		<td class='form_left'>Название <font color=red>*</font></td>
                        		<td class='form_main'><input class='form' type='text' name='name_short' id='name_short' value='".$name."'></td>
                        	</tr>	
                        	<tr>
                        		<td class='form_left'>Полное название <font color=red>*</font></td>
                        		<td class='form_main'><input class='form' type='text' name='name_full' id='name_full' value='".$name_full."'></td>
                        	</tr>	
                        	<tr>
                        		<td class=form_left>Город</td>
                        		<td class=form_main>
                        			<select name='city_id' id='city_id'>
                        				<option value='0'>Нет данных</option>";
    	   		$sql_query="SELECT c.id, c.name, r.name FROM ".$sql_pref."_cities AS c, ".$sql_pref."_regions AS r WHERE c.region_id=r.id ORDER BY c.name";
    			$sql_res=mysql_query($sql_query, $conn_id);
    			while(list($c_id, $c_name, $r_name)=mysql_fetch_row($sql_res))
    			{
    				$c_name=stripslashes($c_name);$r_name=stripslashes($r_name);
    				if ($c_id==@$city_id) $select="selected"; else $select="";
    				$form_comp.="<option value=".$c_id." ".$select.">".$c_name." (".$r_name.")</option>";
    			}
    		   
            $form_comp.="</select>
                                    <div>
                                        <span onclick=\"document.getElementById('city_id').value='1459';\" style='cursor:pointer;border-bottom:dotted 1px gray;'>Москва</span>
                                        <span onclick=\"document.getElementById('city_id').value='1900';\" style='cursor:pointer;border-bottom:dotted 1px gray;'>Санкт-Петербург</span>
                                    </div>
                        		</td>
                        	</tr>
                        	<tr>
                        		<td class='form_left'>Краткое описание <font color=red>*</font></td>
                        		<td class='form_main'><textarea class='form' name='descr' id='descr' rows='4'>".$descr."</textarea></td>
                        	</tr>
                        	<tr>
                        		<td class='form_left'>Фактический адрес</td>
                        		<td class='form_main'><input class='form' type='text' name='address_fact' id='address_fact' value='".$address_fact."'></td>
                        	</tr>	
                        	<tr>
                        		<td class='form_left'>Телефон<font color=red>*</font></td>
                        		<td class='form_main'><input class='form' type='text' name='phone1' id='phone1' value='".$phone1."'></td>
                        	</tr>	
                        	<tr>
                        		<td class='form_left'>Факс</td>
                        		<td class='form_main'><input class='form' type='text' name='fax' id='fax' value='".$fax."'></td>
                        	</tr>
                        	<tr>
                        		<td class='form_left'>E-mail <font color=red>*</font></td>
                        		<td class='form_main'><input class='form' type='text' name='email' id='email' value='".$email."'></td>
                        	</tr>
                        	<tr>
                        		<td class='form_left'>Сайт</td>
                        		<td class='form_main'><input class='form' type='text' name='site' id='site' value='".$site."'></td>
                        	</tr>
                            <tr>
                                <td class='form_left'> Код (защита от спама)</td>
                                <td class='form_main'><div><img src='".$antispam['pic']."' border='1' width='100' height='25'><BR><input style='width:100px' id='a_s_u' name='a_s_u'></div>
                                    <input type=hidden name=a_s_t id=a_s_t value=\"".$antispam['code']."\">
                                    <input type=hidden name=a_s_p id=a_s_p value=\"".$antispam['pic']."\"></td></tr>
                            <tr><td colspan=2>Звездочкой отмечены поля, обязательные для заполнения</td></tr>
                        	</table>";
        
        
        $form_comp.="</td></tr>";
        $form_comp.="<tr><td>                    
                        <input type='hidden' name='action' value='company_add'>
                        <input class='form_button' type='submit' name='button_submit' value='Сохранить'>
                    </td></tr>";
        
       	$out.= $form_comp; 
        
        $out.="</table></form>";
    }
    else
    {
         $sql_query="INSERT INTO ".$sql_pref."_companies (name, name_full, city_id, address_fact, phone1, fax, email, site, descr, enable) VALUES ('".$name."', '".$name_full."', '".$city_id."', '".$address_fact."', '".$phone1."', '".$fax."', '".$email."', '".$site."', '".$descr."', 'No')";
    	 $sql_res=mysql_query($sql_query, $conn_id);
    	 $comp_id=mysql_insert_id();
         
         $email="avkuryatov@yandex.ru";
         $mailtitle="Регистрация компании на сайте Ensor.ru";
    	 $mailheader="From: robot@".$path_domen."\n";
    	 $mailheader.="Content-Type: text/plain;\n charset=\"WINDOWS-1251\"";
    
    	 $mailcontent ="На сайте Ensor.ru зарегистрирована новая компания (без подключения услуг)\n\n";
    	 $mailcontent.="Представитель компании: [".get_user_name_by_id($user_id)."]\n";
    	 $mailcontent.="Компания: [".get_company_name_by_id($comp_id)."]\n";
    	 $mailcontent.="\n";
    	 $mailcontent.="С уважением, \n администрация сайта www.ensor.ru.";
    	
    	 mail($email,$mailtitle,$mailcontent,$mailheader);
         $out.="Спасибо за регистрацию! Данные будут обработаны модераторами в течение двух недель.";
    }
    
	return ($out);
    
}

function form_show()
{
    global $sql_pref, $conn_id, $path;
	global $auth_error_info, $user_id, $user_name, $user_surname, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
    
    $out="";
    $out.="<h3>Внимание! Данный функционал предназначен для организаций, осуществляющих свою деятельность в сфере энергетики. Компании, не имеющие никакого, отношения к энергетике будут безжалостно удаляться.</h3>";
    
    if($user_id!=0)
    {        
        //$out.="Вы уже зарегистрированы как: ".$user_surname." ".$user_name.". Для регистрации организации Вам требуется всего шаг.<BR><BR>";   
    }
    
	
    $step1_mouse_out="onmouseout=\"this.style.backgroundColor='#E3E9FF'; this.style.color='#000000';\"";
    if($_REQUEST['action']=='show_step1') {$inner_text=show_comp_form(); $step1_color=" style='background-color: #6688EE; color: #FFFFFF'";  $step1_mouse_out=" ";} else {$step1_color=" style=' background-color: #E3E9FF; color: #000000;'";};
    $step2_mouse_out="onmouseout=\"this.style.backgroundColor='#E3E9FF'; this.style.color='#000000';\"";
    if($_REQUEST['action']=='show_step2') {$inner_text=show_user_form(); $step2_color=" style='background-color: #6688EE; color: #FFFFFF'";  $step2_mouse_out=" ";} else {$step2_color=" style=' background-color: #E3E9FF; color: #000000;'";};
    $step3_mouse_out="onmouseout=\"this.style.backgroundColor='#E3E9FF'; this.style.color='#000000';\"";
    if($_REQUEST['action']=='show_step3') {$inner_text=show_functions_form(); $step3_color=" style='background-color: #6688EE; color: #FFFFFF'";  $step3_mouse_out=" ";} else {$step3_color=" style=' background-color: #E3E9FF; color: #000000;'";};
    if($_REQUEST['action']=='show_step4') {$inner_text=show_message(); $step3_color=" style='background-color: #6688EE; color: #FFFFFF'";  $step3_mouse_out=" ";}
    
    
    $out.="<table width=100% cellpadding=5 cellspacing=0 border=0>";
    $out.="			
			<tr >
				<td ".$step1_mouse_out." ".$step1_color." width=30% align=center><font style='font-size: 11px;'><b>ШАГ 1</b><BR></font><font style='font-size: 9px;'>(регистрация компании)</font></td>
				<td>&nbsp;</td>
                <td ".$step2_mouse_out." ".$step2_color." width=30% align=center><font style='font-size: 11px;'><b>ШАГ 2</b><BR></font><font style='font-size: 9px;'>(регистрация представителя)</font></td>
				<td>&nbsp;</td>
                <td ".$step3_mouse_out." ".$step3_color." width=30% align=center><font style='font-size: 11px;'><b>ШАГ 3</b><BR></font><font style='font-size: 9px;'>(выбор функционала)</font></td>
            </tr>
            <tr><td colspan=5 style='border-top:solid 5px #BBCCFF;'>".$inner_text."</td></tr></table>";

	return ($out);
    
}

function show_message()   // сохранение информации о подключенных услугах в табл. ens_company_admin
{
    global $sql_pref, $conn_id, $path, $path_domen;
	global $auth_error_info,$user_name,$user_surname, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
	$out="";
    if (isset($_REQUEST['comp_id'])) $comp_id=addslashes($_REQUEST['comp_id']); else $comp_id="";
    if ($user_id==0)
    {
        if (isset($_REQUEST['user_id'])) $user_id=addslashes($_REQUEST['user_id']);        
    }
    if (isset($_REQUEST['news']) && $_REQUEST['news']=="Yes") $news="Yes"; else $news="No";
    if (isset($_REQUEST['vacancies']) && $_REQUEST['vacancies']=="Yes") $vacancies="Yes"; else $vacancies="No";
    if (isset($_REQUEST['catalog']) && $_REQUEST['catalog']=="Yes") $catalog="Yes"; else $catalog="No";
    if (isset($_REQUEST['info']) && $_REQUEST['info']=="Yes") $info="Yes"; else $info="No";
    if (isset($_REQUEST['resume']) && $_REQUEST['resume']=="Yes") $resume="Yes"; else $resume="No";
    //$dt=date();
        
    $sql_query="INSERT INTO ".$sql_pref."_company_admin (user_id,company_id,company_news,company_info,company_vacancies,company_catalog,company_resume) VALUES ('".$user_id."','".$comp_id."','".$news."', '".$info."', '".$vacancies."', '".$catalog."', '".$resume."')";
	$sql_res=mysql_query($sql_query, $conn_id);
    //echo $sql_query;
    $user_show=get_user_name_by_id($user_id);
    $comp_show=get_company_name_by_id($comp_id);
    
    $email="avkuryatov@yandex.ru";
    $mailtitle="Регистрация услуг на сайте Ensor.ru";
	$mailheader="From: robot@".$path_domen."\n";
	$mailheader.="Content-Type: text/plain;\n charset=\"WINDOWS-1251\"";

	$mailcontent ="На сайте Ensor.ru зарегистрирована новая компания\n\n";
	$mailcontent.="Представитель компании [".$user_id."]: ".$user_show."\n";
	$mailcontent.="Компания [".$comp_id."]: ".$comp_show."\n";
	$mailcontent.="\n";
	$mailcontent.="С уважением, \n администрация сайта www.ensor.ru.";
	
	mail($email,$mailtitle,$mailcontent,$mailheader);
    
    $out.="Спасибо за регистрацию! Данные будут обработаны модераторами в течение двух недель.";
    return $out;    
    
}

function show_functions_form()  // отображение информации о доступных услугах (функциях) и сохранение ранее введенной информации о пользователе
{
    global $sql_pref, $conn_id, $path;
	global $auth_error_info,$user_name,$user_surname, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
	$out="";
    $out.="<SCRIPT>
                    function check_form()
            		{
            			var str = 'OK';
            			//obj=document.getElementById('news');
                        //alert(obj.value);
                        //str = 'NO';
                        return str;
            		}
                 </SCRIPT>";
    
    if (isset($_REQUEST['comp_id'])) $comp_id=addslashes($_REQUEST['comp_id']); else $comp_id="";
    
    if ($user_id==0)     
    {
        
        if (isset($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
    	if (isset($_REQUEST['name2'])) $name2=addslashes($_REQUEST['name2']); else $name2="";
        if (isset($_REQUEST['surname'])) $surname=addslashes($_REQUEST['surname']); else $surname="";
    	if (isset($_REQUEST['phone_work'])) $phone1=addslashes($_REQUEST['phone_work']); else $phone_work="";
    	if (isset($_REQUEST['email'])) $email=addslashes($_REQUEST['email']); else $email="";
        if (isset($_REQUEST['pol']) AND !empty($_REQUEST['pol'])) $pol=$_REQUEST['pol']; else $error['pol']="Ошибка!";
		if (isset($_REQUEST['dt_birth']) AND !empty($_REQUEST['dt_birth'])) $dt_birth=$_REQUEST['dt_birth']; else $dt_birth="0000-00-00";
		
        $sql_query="SELECT id FROM ".$sql_pref."_users WHERE email='".@$email."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0) 
        {
            $out.="Ошибка! ".$email." уже зарегистрирован. Необходимо вернуться на предыдущую страницу!"; 
            return $out;
        }
        else
        {
            $pass=getpass();
            $dt_reg=date("Y-m-d H:i:s");
            $sql_query="INSERT INTO ".$sql_pref."_users (pass,surname,name,name2,email,pol,phone_work,enable,dt_birth,dt_reg,status,forum_admin,company_id) VALUES ('".$pass."','".$surname."','".$name."','".$name2."','".$email."','".$pol."','".$phone_work."','Yes','".$dt_birth."','".$dt_reg."','".$status."','No','".$comp_id."')";
			$sql_res=mysql_query($sql_query, $conn_id);
			$user_id=mysql_insert_id();
			
			$mailtitle="Регистрация на сайте Ensor.ru";
			$mailheader="From: robot@".$path_domen."\n";
			$mailheader.="Content-Type: text/plain;\n charset=\"WINDOWS-1251\"";
		
			$mailcontent="Здравствуйте! \n";
			$mailcontent.="Вы зарегистрировались на сайте Ensor.ru \n\n";
			$mailcontent.="Ваши данные для входа на сайт:\n";
			$mailcontent.="ФИО: ".$surname." ".$name." ".@$name2."\n";
			$mailcontent.="E-mail: ".$email."\n";
			$mailcontent.="Предварительный пароль: ".$pass."\n";
			$mailcontent.="\n";
			$mailcontent.="С уважением, \n администрация сайта www.ensor.ru.";
			
			mail($email,$mailtitle,$mailcontent,$mailheader);
			
			$out.="<h2>Спасибо за регистрацию!</h2>";
			$out.="<br><p>Данные для входа на сайт мы выслали вам на адрес <b>".$email."</b></p>";
			
        }
    }
    if($user_id!=0)
    { 
        
        $sql_query="UPDATE ".$sql_pref."_users SET company_id='".$comp_id."' WHERE id='".$user_id."'";
		$sql_res=mysql_query($sql_query, $conn_id);
        
        $sql_query="SELECT id, name, name_full FROM ".$sql_pref."_companies WHERE id='".$comp_id."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
    	{
    		while (list($id, $name, $name_full)=mysql_fetch_row($sql_res))
    		{
    		      $comp_show=$name." [".$name_full."]";
            }
        }
        $sql_query="SELECT id, name, surname FROM ".$sql_pref."_users WHERE id='".$user_id."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
    	{
    		while (list($id, $name, $surname)=mysql_fetch_row($sql_res))
    		{
    		      $user_show=$surname." ".$name;
            }
        }
        
        $out.="<form name='form_name' method='post' onSubmit='if (check_form()!=\"OK\") {alert(\"Вы не заполнили поле \" + check_form() + \"!\"); return false;} else return true;'>";
        $out.="<table>";
        $out.="<tr>
                    <td class='form_left'>Организация<input type=hidden name=comp_id id=comp_id value='".$comp_id."'></td>
                    <td class='form_main'>".$comp_show."</td></tr>";
        $out.="<tr>
                    <td class='form_left'>Представитель организации<input type=hidden name=user_id id=user_id value='".$user_id."'></td>
                    <td class='form_main'>".$user_show."</td></tr>";
        $out.="<tr>
                    <td class='form_left' colspan=2>Выбор доступного функционала.<BR><b>Пожалуйста укажите только тот функционал, который планируете использовать!</b></td></tr>";
        $out.="<tr>
                    <td class='form_left'>Администрирование персональной страницы</td>
                    <td class='form_main'><input type=checkbox name=info id=info value='Yes'></td></tr>";       
        $out.="<tr>
                    <td class='form_left'>Публикация новостей</td>
                    <td class='form_main'><input type=checkbox name=news id=news value='Yes'></td></tr>";
        $out.="<tr>
                    <td class='form_left'>Публикация вакансий</td>
                    <td class='form_main'><input type=checkbox name=vacancies id=vacancies value='Yes'></td></tr>";
        $out.="<tr>
                    <td class='form_left'>Создание каталога продукции</td>
                    <td class='form_main'><input type=checkbox name=catalog id=catalog value='Yes'></td></tr>";

        $out.="<tr>
                    <td class='form_left'>Доступ к базе резюме</td>
                    <td class='form_main'><input type=checkbox name=resume id=resume value='Yes'></td></tr>";
        //$out.="<tr><td class='form_left'>Публикация событий</td><td class='form_main'><input type=checkbox name=events id=events value='Yes'></td></tr>";
        $out.="<tr><td>                    
                    <input type='hidden' name='action' value='show_step4'>
                    <input class='form_button' type='submit' name='button_submit' value='Продолжить'>
                </td></tr>";
        $out.="</table></form>";
    }
    
    return $out;        
    
}


function show_user_form()   // ШАГ 2 - регистрация пользователя (сохраняются данные организации, введенные на предыдущем шаге)
{
    global $sql_pref, $conn_id, $path;
	global $auth_error_info,$user_name,$user_surname, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
	$out="";
    
    if (isset($_REQUEST['comp_id']) AND !empty($_REQUEST['comp_id'])) 
    {
        $comp_id=$_REQUEST['comp_id'];
        //echo "!!!".$comp_id;
    }
    else
    {
        if (isset($_REQUEST['name_short'])) $name=addslashes($_REQUEST['name_short']); else $name="";
    	if (isset($_REQUEST['name_full'])) $name_full=addslashes($_REQUEST['name_full']); else $name_full="";
    	if (isset($_REQUEST['city_id'])) $city_id=addslashes($_REQUEST['city_id']); else $city_id=0;
    	if (isset($_REQUEST['address_fact'])) $address_fact=addslashes($_REQUEST['address_fact']); else $address_fact="";
    	if (isset($_REQUEST['phone1'])) $phone1=addslashes($_REQUEST['phone1']); else $phone1="";
    	if (isset($_REQUEST['fax'])) $fax=addslashes($_REQUEST['fax']); else $fax="";
    	if (isset($_REQUEST['email'])) $email=addslashes($_REQUEST['email']); else $email="";
    	if (isset($_REQUEST['site'])) $site=addslashes($_REQUEST['site']); else $site="";
        $sql_query="INSERT INTO ".$sql_pref."_companies (name, name_full, city_id, address_fact, phone1, fax, email, site, descr, enable) VALUES ('".$name."', '".$name_full."', '".$city_id."', '".$address_fact."', '".$phone1."', '".$fax."', '".$email."', '".$site."', '".$descr."', 'No')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$comp_id=mysql_insert_id();
    }
    
    $sql_query="SELECT id, name, name_full FROM ".$sql_pref."_companies WHERE id='".$comp_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $name, $name_full)=mysql_fetch_row($sql_res))
		{
		      $comp_show=$name." [".$name_full."]";
        }
    }
    
    if($user_id!=0)
    {
        $out.="Вы уже зарегистрированы. Перейдите к следующему шагу!";
        $out.="<form name='form_name' method='post' onSubmit='if (check_form()!=\"OK\") {alert(\"Вы не заполнили поле \" + check_form() + \"!\"); return false;} else return true;'>";
        $out.="<table>";
        $out.="<tr>
                    <td class='form_left'>Организация<input type=hidden name=comp_id id=comp_id value='".$comp_id."'></td>
                    <td class='form_main'>".$comp_show."</td></tr>";
                    
        $out.="<tr>
                    <td class='form_left'>Представитель организации</td>
                    <td class='form_main'>".$user_surname." ".$user_name."</td></tr>";
        $out.="<tr><td>                    
                    <input type='hidden' name='action' value='show_step3'>
                    <input class='form_button' type='submit' name='button_submit' value='Продолжить'>
                </td></tr>";
        $out.="</table>";
    }
    else
    {
        $out.="<SCRIPT>
                    function check_form()
            		{
            			var str = 'OK';
            			if (document.getElementById('name').value=='') str='Имя';
                        if (document.getElementById('surname').value=='') str='Фамилия';
                        if (document.getElementById('name2').value=='') str='Отчетство';
                        if (document.getElementById('phone_work').value=='') str='Телефон';
                        if (document.getElementById('email').value=='') str='Е-мэйл';       
                        if (document.getElementById('a_s_u').value!=document.getElementById('a_s_t').value) str='Поле антиспама'; 
                        
            			return str;
            		}
                 </SCRIPT>";
        $xc2_inc=file_get_contents($path."inc/xc2.inc");
	    $dt_birth=date("1980-01-01");
        $antispam=show_codepic();
        $out.=$xc2_inc;
        
        $out.="<form name='form_name' method='post' onSubmit='if (check_form()!=\"OK\") {alert(\"Вы не заполнили поле \" + check_form() + \"!\"); return false;} else return true;'>";
        $out.="<table>";
        $out.="<tr>
                    <td class='form_left'>Организация<input type=hidden name=comp_id id=comp_id value='".$comp_id."'></td>
                    <td class='form_main'>".$comp_show."</td></tr>";
                    
        $out.="<tr>
                    <td class='form_left' colspan=2>Представитель организации</td></tr>";
        $out.="<tr>
                    <td class='form_left'>Фамилия <font color=red>*</font></td>
                    <td class='form_main'><input type=Text maxlength=70 name=surname id=surname value='".@$surname."' style='width:170px;font-size:14px;'></td></tr>"; 
        $out.="<tr>
                    <td class='form_left'>Имя <font color=red>*</font></td>
                    <td class='form_main'><input type=Text maxlength=70 name=name id=name value='".@$name."' style='width:170px;font-size:14px;'></td></tr>";          
         $out.="<tr>
                    <td class='form_left'>Отчество <font color=red>*</font></td>
                    <td class='form_main'><input type=Text maxlength=70 name=name2 id=name2 value='".@$name2."' style='width:170px;font-size:14px;'></td></tr>";          
         $out.="<tr>
                    <td class='form_left'>E-mail <font color=red>*</font></td>
                    <td class='form_main'><input type=Text maxlength=70 name=email id=email value='".@$email."' style='width:170px;font-size:14px;'></td></tr>";          
         $out.="<tr>
                    <td class='form_left'>Телефон (рабочий) <font color=red>*</font></td>
                    <td class='form_main'><input type=Text maxlength=70 name=phone_work id=phone_work value='".@$phone_work."' style='width:170px;font-size:14px;'></td></tr>";          
        $out.="<tr>
                    <td class='form_left'>Дата рождения</td>
                    <td class='form_main'><div id=holder></div><input onkeydown='return false;' type=Text maxlength=70 name=dt_birth id=dt_birth value='".@$dt_birth."' style='width:100px;font-size:14px;cursor:pointer;' onclick='showCalendar(\"\",document.getElementById(\"dt_birth\"),null,\"".$dt_birth."\",\"holder\",0,25,1)'></td></tr>";          
        $out.="<tr>
                    <td class='form_left'>Пол</td>
                    <td class='form_main'><input type=radio name=pol value='m' checked> мужской &nbsp;&nbsp; <input type=radio name=pol value='w'> женский</td></tr>";          
       
        $out.="<tr>
                    <td class='form_left'> Код (защита от спама)</td>
                    <td class='form_main'><div><img src='".$antispam['pic']."' border='1' width='100' height='25'><BR><input style='width:100px' id='a_s_u' name='a_s_u'></div>
                        <input type=hidden name=a_s_t id=a_s_t value=\"".$antispam['code']."\">
                        <input type=hidden name=a_s_p id=a_s_p value=\"".$antispam['pic']."\"></td></tr>";          
       
        $out.="<tr><td colspan=2>Звездочкой отмечены поля, обязательные для заполнения</td></tr>";
        						
       
        $out.="<tr><td>                    
                    <input type='hidden' name='action' value='show_step3'>
                    <input class='form_button' type='submit' name='button_submit' value='Продолжить'>
                </td></tr>";
        $out.="</table></form>";
    }
    
   	      
    
	return ($out);
    
}

function show_comp_form()   // ШАГ 2 - регистрация компании (вводятся данные организации, сохраняемые на следующем шаге)
{
    global $sql_pref, $conn_id, $path;
	global $auth_error_info, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
    $out="";
    
    $antispam=show_codepic();
    $out.="
    <SCRIPT>
        function check_form()
		{
			var str = 'OK';
            obj=document.getElementById('company_t'); 
            //alert(obj.checked);         
            if (obj.checked==true)
            {
    			if (document.getElementById('name_short').value=='') str='Название';
                if (document.getElementById('name_full').value=='') str='Полное название';
                if (document.getElementById('descr').value=='') str='Краткое описание';
                if (document.getElementById('phone1').value=='') str='Телефон';
                if (document.getElementById('email').value=='') str='Е-мэйл';    
                if (document.getElementById('a_s_u').value!=document.getElementById('a_s_t').value) str='Поле антиспама';                                     
            }
            else
            {
                if (document.getElementById('comp_id').value==0) str='Организация';
            }
			return str;
		}
        
        function display_div(src_id)
        {
            switch (src_id) 
            {
                case 1:
            	
                    obj_div=document.getElementById('div_1');  
            		obj_div.style.display='';	
                    obj_div=document.getElementById('div_3');  
            		obj_div.style.display='none';   
            	    break;
                    
                case 3:
            	
                    obj_div=document.getElementById('div_3');  
            		obj_div.style.display='';	                    
                    obj_div=document.getElementById('div_1');  
            		obj_div.style.display='none';
            	    break;
                
                case 2:
                    for(i=1;i<=6;i++)
                    {
            		  obj_div=document.getElementById('div_'+i);  
            		  obj_div.style.display='none';
                    }	 
                    break;
                    
                default:
                    // проверка наличия текста в предыдущем варианте
                    s1=src_id-2;
                    obj_div=document.getElementById('answer_'+s1);  
            		if (obj_div.value!='')
            		{
            		  obj_div=document.getElementById('div_'+src_id);  
            		  obj_div.style.display='';
                    }
                    else
                    {
                      alert('Прежде чем добавлять очередной вариант ответа - заполните, пожалуйста, предыдущий вариант.');
                    }
                    break;	 
            }
            return;
        }
        
        </SCRIPT>
    ";
    
    $out.="<form name='form_name' method='post' onSubmit='if (check_form()!=\"OK\") {alert(\"Вы не заполнили поле \" + check_form() + \"!\"); return false;} else return true;'><table>";
    
    $check_text.="<tr style='' id='div_1'><td>1. Проверьте наличие организации в базе данных<BR>";
    
    $check_comp="<select name='comp_id' id='comp_id'>";	
	$sql_query="SELECT id, name FROM ".$sql_pref."_companies WHERE id<>'".$id."' ORDER BY name";
	$sql_res=mysql_query($sql_query, $conn_id);
    $check_comp.= "<option value='0'>Нет данных</option>";
	while(list($p_id, $p_name)=mysql_fetch_row($sql_res))
	{
		$p_name=stripslashes($p_name);
		if ($p_id==@$parent_id) $select="selected"; else $select="";
		$check_comp.= "<option value=".$p_id." ".$select.">".$p_name."</option>";
	}
	$check_comp.="</select></td></tr>";
    $check_comp.="<tr style='' id='div_2'><td>2. Ваша организация отсутствует в списке?<input type='radio' onclick='display_div(1)' name='company_type' checked='yes' value='old'> - присутствует &nbsp; &nbsp; &nbsp; <input type='radio' onclick='display_div(3)' name='company_type' id='company_t' value='new'> - отсутствует</td></tr>";
    //7434355
    $form_comp.="<tr style='display: none;' id='div_3'><td>3. Заполните форму данных об организации:<BR>";    
    $form_comp.="<table><tr>
                    		<td class='form_left'>Название <font color=red>*</font></td>
                    		<td class='form_main'><input class='form' type='text' name='name_short' id='name_short' value='".$name."'></td>
                    	</tr>	
                    	<tr>
                    		<td class='form_left'>Полное название <font color=red>*</font></td>
                    		<td class='form_main'><input class='form' type='text' name='name_full' id='name_full' value='".$name_full."'></td>
                    	</tr>	
                    	<tr>
                    		<td class=form_left>Город</td>
                    		<td class=form_main>
                    			<select name='city_id' id='city_id'>
                    				<option value='0'>Нет данных</option>";
	   		$sql_query="SELECT c.id, c.name, r.name FROM ".$sql_pref."_cities AS c, ".$sql_pref."_regions AS r WHERE c.region_id=r.id ORDER BY c.name";
			$sql_res=mysql_query($sql_query, $conn_id);
			while(list($c_id, $c_name, $r_name)=mysql_fetch_row($sql_res))
			{
				$c_name=stripslashes($c_name);$r_name=stripslashes($r_name);
				if ($c_id==@$city_id) $select="selected"; else $select="";
				$form_comp.="<option value=".$c_id." ".$select.">".$c_name." (".$r_name.")</option>";
			}
		   
        $form_comp.="</select>
                                <div>
                                    <span onclick=\"document.getElementById('city_id').value='1459';\" style='cursor:pointer;border-bottom:dotted 1px gray;'>Москва</span>
                                    <span onclick=\"document.getElementById('city_id').value='1900';\" style='cursor:pointer;border-bottom:dotted 1px gray;'>Санкт-Петербург</span>
                                </div>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td class='form_left'>Краткое описание <font color=red>*</font></td>
                    		<td class='form_main'><textarea class='form' name='descr' id='descr' rows='4'>".$descr."</textarea></td>
                    	</tr>
                    	<tr>
                    		<td class='form_left'>Фактический адрес</td>
                    		<td class='form_main'><input class='form' type='text' name='address_fact' id='address_fact' value='".$address_fact."'></td>
                    	</tr>	
                    	<tr>
                    		<td class='form_left'>Телефон<font color=red>*</font></td>
                    		<td class='form_main'><input class='form' type='text' name='phone1' id='phone1' value='".$phone1."'></td>
                    	</tr>	
                    	<tr>
                    		<td class='form_left'>Факс</td>
                    		<td class='form_main'><input class='form' type='text' name='fax' id='fax' value='".$fax."'></td>
                    	</tr>
                    	<tr>
                    		<td class='form_left'>E-mail <font color=red>*</font></td>
                    		<td class='form_main'><input class='form' type='text' name='email' id='email' value='".$email."'></td>
                    	</tr>
                    	<tr>
                    		<td class='form_left'>Сайт</td>
                    		<td class='form_main'><input class='form' type='text' name='site' id='site' value='".$site."'></td>
                    	</tr>
                        <tr>
                            <td class='form_left'> Код (защита от спама)</td>
                            <td class='form_main'><div><img src='".$antispam['pic']."' border='1' width='100' height='25'><BR><input style='width:100px' id='a_s_u' name='a_s_u'></div>
                                <input type=hidden name=a_s_t id=a_s_t value=\"".$antispam['code']."\">
                                <input type=hidden name=a_s_p id=a_s_p value=\"".$antispam['pic']."\"></td></tr>
                        <tr><td colspan=2>Звездочкой отмечены поля, обязательные для заполнения</td></tr>
                    	</table>";
    
    
    $form_comp.="</td></tr>";
    $form_comp.="<tr><td>                    
                    <input type='hidden' name='action' value='show_step2'>
                    <input class='form_button' type='submit' name='button_submit' value='Продолжить'>
                </td></tr>";
    
   	$out.= $check_text.$check_comp.$form_comp; 
    
    $out.="</table></form>";
    
	return ($out);
    
    
}


?>