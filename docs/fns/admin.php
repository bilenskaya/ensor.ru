<?

function auth_admin_bar()
{
    
	global $user_id, $page_header1, $user_name, $user_surname, $page_title;
    global $sql_pref, $conn_id, $path, $path_blogs, $path_resume;
	global $auth_error_info, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
    
    $sql_query="SELECT company_id, company_info, company_vacancies, company_news, company_catalog, company_resume FROM ".$sql_pref."_company_admin WHERE user_id=".$user_id;
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
       list($company_id,$company_info,$company_vacancies,$company_news,$company_catalog, $company_resume)=mysql_fetch_row($sql_res);
    }
    
    $page_header1="Управление данными компании";
    $page_title="Управление данными компании";
    $user_name_show=substr($user_name,0,1).". ".$user_surname;
    $out="";
    if($company_resume=='Yes') $out.="Доступ к резюме энергетиков: имеется<br/><br/>Доступ к модерации информации на сайте:<br/>";
    $out.="<table width=100% border='0'><tr>";    
    if($company_info=='Yes') $out.="<td align=center valign=middle ><a href='/auth/admin/company/' class='user_bar'>Профиль компании</a></td>";
    if($company_news=='Yes') $out.="<td align=center valign=middle ><a href='/auth/admin/news/' class='user_bar'>Новости компании</a></td>";
    if($company_vacancies=='Yes') $out.="<td align=center valign=middle ><a href='/auth/admin/vacancies/' class='user_bar'>Вакансии компании</a></td>";
    $out.="</tr></table><br/><hr>";
    if (isset($user_id) AND ($user_id!==0)) return ($out); else return("");
}

function auth_admin_company_profile($company_id)
{
    global $user_id, $page_header1, $user_name, $user_surname, $page_title;
    global $sql_pref, $conn_id, $path, $path_blogs, $path_resume;
	global $auth_error_info, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url, $art_url;
    
    $xc2_inc=file_get_contents($path."inc/xc2.inc");
    $content_script="    
        <script type='text/javascript'>
            new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
        </script>";
	    
    $sql_query="SELECT id, parent_id, name, name_eng, name_full, city_id, address_legal, address_fact, requisites, phone1, phone2, fax, email, site, descr, content, tags, author_id, enable  FROM ".$sql_pref."_companies WHERE id='".$company_id."'";
	//echo $sql_query."111";
    $sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $parent_id, $name, $name_eng, $name_full, $city_id, $address_legal, $address_fact, $requisites, $phone1, $phone2, $fax, $email, $site, $descr, $content, $tags, $author_id, $enable)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); $name_eng=stripslashes($name_eng); $name_full=stripslashes($name_full); $address_legal=stripslashes($address_legal); $address_fact=stripslashes($address_fact); $requisites=stripslashes($requisites); $phone1=stripslashes($phone1); $phone2=stripslashes($phone2); $fax=stripslashes($fax); $tags=stripslashes($tags); $descr=stripslashes($descr); $content=stripslashes($content);
        
		$sql_query="SELECT sfera_id FROM ".$sql_pref."_sd_sfery_arts WHERE table_name='companies'&&art_id='".$id."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
		while(list($sfery_list[])=mysql_fetch_row($sql_res_1));
        
		$sql_query="SELECT direction_id FROM ".$sql_pref."_sd_directions_arts WHERE table_name='companies'&&art_id='".$id."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
		while(list($directions_list[])=mysql_fetch_row($sql_res_1));
	}

    $out="";
    $out.=$xc2_inc;
    $out.="
        <form name='form_name' method='post' enctype='multipart/form-data'>
        <input type='hidden' name='id' value='".$company_id."'>
        <input type='hidden' name='action' value='companies_save'>
        <table cellpadding='2' cellspacing='2' border='0' bgcolor='#FFFFFF'>
        	<tr>
        		<td class='form_left'>Название</td>
        		<td class='form_main'><input class='form' type='text' name='name' value='".$name."'></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Название (eng)</td>
        		<td class='form_main'><input class='form' type='text' name='name_eng' value='".$name_eng."'></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Полное название</td>
        		<td class='form_main'><input class='form' type='text' name='name_full' value='".$name_full."'></td>
        	</tr>
        	<tr>
        		<td class=form_left>Родительская компания</td>
        		<td class=form_main>
        			<select name='parent_id' id='parent_id'>
        				<option value='0'>Нет данных</option>";        			   
        					$sql_query="SELECT id, name FROM ".$sql_pref."_companies WHERE id<>'".$id."' ORDER BY name";
        					$sql_res=mysql_query($sql_query, $conn_id);
        					while(list($p_id, $p_name)=mysql_fetch_row($sql_res))
        					{
        						$p_name=stripslashes($p_name);
        						if ($p_id==@$parent_id) $select="selected"; else $select="";
        						$out.= "<option value=".$p_id." ".$select.">".$p_name."</option>";
        					}
        			   	$out.= "
        			</select>
        		</td>
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
        						$out.= "<option value=".$c_id." ".$select.">".$c_name." (".$r_name.")</option>";
        					}
        			$out.="</select>
                    <div>
                        <span onclick=\"document.getElementById('city_id').value='1459';\" style='cursor:pointer;border-bottom:dotted 1px gray;'>Москва</span>
                        <span onclick=\"document.getElementById('city_id').value='1900';\" style='cursor:pointer;border-bottom:dotted 1px gray;'>Санкт-Петербург</span>
                    </div>
        		</td>
        	</tr>
        	<tr>
        		<td class='form_left'>Краткое описание</td>
        		<td class='form_main'><textarea class='form' name='descr' rows='4'>".$descr."</textarea></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Полное описание</td>
        		<td class='form_main'>
        			<div><span onclick=\"if (document.getElementById('divcontent').style.display=='none') document.getElementById('divcontent').style.display=''; else document.getElementById('divcontent').style.display='none';\" style='cursor:pointer;font-size:12px;font-weight:normal;text-decoration:none;border-bottom:dotted 1px gray;'>Показать</span></div>
        			<div id='divcontent' style='display:none;'>
            			<textarea rows=8 id=content name=content style='width:450px;height:120;font-size:14px;'>".$content."</textarea>
                    </div>
        		</td>
        	</tr>
        	<tr>
        		<td class='form_left'>Юридический адрес</td>
        		<td class='form_main'><input class='form' type='text' name='address_legal' value='".$address_legal."'></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Фактический адрес</td>
        		<td class='form_main'><input class='form' type='text' name='address_fact' value='".$address_fact."'></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Реквизиты</td>
        		<td class='form_main'><textarea class='form' name='requisites' rows='4'>".$requisites."</textarea></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Телефон 1</td>
        		<td class='form_main'><input class='form' type='text' name='phone1' value='".$phone1."'></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Телефон 2</td>
        		<td class='form_main'><input class='form' type='text' name='phone2' value='".$phone2."'></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Факс</td>
        		<td class='form_main'><input class='form' type='text' name='fax' value='".$fax."'></td>
        	</tr>
        	<tr>
        		<td class='form_left'>E-mail</td>
        		<td class='form_main'><input class='form' type='text' name='email' value='".$email."'></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Сайт</td>
        		<td class='form_main'><input class='form' type='text' name='site' value='".$site."'></td>
        	</tr> 
        	<tr>
        		<td>&nbsp;</td>
        		<td style='padding-top:10;'><input class='form_button' type='submit' name='button_submit' value='Сохранить'></td>
        	</tr>
        </table>
        </form>".$content_script;

    return $out;    
}

function form_companies_save()
{
	global $sql_pref, $conn_id;
    
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (isset($_REQUEST['name'])) $name=addslashes($_REQUEST['name']); else $name="";
	if (isset($_REQUEST['name_eng'])) $name_eng=addslashes($_REQUEST['name_eng']); else $name_eng="";
	if (isset($_REQUEST['name_full'])) $name_full=addslashes($_REQUEST['name_full']); else $name_full="";
	if (isset($_REQUEST['city_id'])) $city_id=addslashes($_REQUEST['city_id']); else $city_id=0;
	if (isset($_REQUEST['parent_id'])) $parent_id=addslashes($_REQUEST['parent_id']); else $parent_id=0;
	if (isset($_REQUEST['address_legal'])) $address_legal=addslashes($_REQUEST['address_legal']); else $address_legal="";
	if (isset($_REQUEST['address_fact'])) $address_fact=addslashes($_REQUEST['address_fact']); else $address_fact="";
	if (isset($_REQUEST['requisites'])) $requisites=addslashes($_REQUEST['requisites']); else $requisites="";
	if (isset($_REQUEST['phone1'])) $phone1=addslashes($_REQUEST['phone1']); else $phone1="";
	if (isset($_REQUEST['phone2'])) $phone2=addslashes($_REQUEST['phone2']); else $phone2="";
	if (isset($_REQUEST['fax'])) $fax=addslashes($_REQUEST['fax']); else $fax="";
	if (isset($_REQUEST['email'])) $email=addslashes($_REQUEST['email']); else $email="";
	if (isset($_REQUEST['site'])) $site=addslashes($_REQUEST['site']); else $site="";
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
	if (isset($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $content="";
	
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_companies SET name='".$name."', name_eng='".$name_eng."', name_full='".$name_full."', parent_id='".$parent_id."', city_id='".$city_id."', address_legal='".$address_legal."', address_fact='".$address_fact."', requisites='".$requisites."', phone1='".$phone1."', phone2='".$phone2."', fax='".$fax."', email='".$email."', site='".$site."', descr='".$descr."', content='".$content."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$cur_company_id=$_REQUEST['id'];
	}    
        
    $sql_query="DELETE FROM ".$sql_pref."_sd_sfery_arts WHERE table_name='companies'&&art_id='".$cur_company_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
    
    $sql_query="SELECT id FROM ".$sql_pref."_sd_sfery";
	$sql_res=mysql_query($sql_query, $conn_id);
	while (list($s_id)=mysql_fetch_row($sql_res))
	{
       	if (@$_REQUEST['sfery_'.$s_id]=="Yes")
        {
    		$sql_query="INSERT INTO ".$sql_pref."_sd_sfery_arts (sfera_id, art_id, table_name) VALUES ('".$s_id."', '".$cur_company_id."', 'companies')";
    		$sql_res_1=mysql_query($sql_query, $conn_id);
        }
    }
    

    $sql_query="DELETE FROM ".$sql_pref."_sd_directions_arts WHERE table_name='companies'&&art_id='".$cur_company_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
    
    $sql_query="SELECT id FROM ".$sql_pref."_sd_directions";
	$sql_res=mysql_query($sql_query, $conn_id);
	while (list($d_id)=mysql_fetch_row($sql_res))
	{
       	if (@$_REQUEST['direction_'.$d_id]=="Yes")
        {
    		$sql_query="INSERT INTO ".$sql_pref."_sd_directions_arts (direction_id, art_id, table_name) VALUES ('".$d_id."', '".$cur_company_id."', 'companies')";
    		$sql_res_1=mysql_query($sql_query, $conn_id);
        }
    }
    
	if (is_uploaded_file( $_FILES['file_name']['tmp_name'])) form_companies_mainimg_save($cur_company_id);
}

function auth_admin_company_news($company_id)
{
    global $sql_pref, $conn_id, $path;
    
    $out="";
	$out.= "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
			<tr class='maintitle'>
				<td width='20' class='maintitle' align='center'><b>id</b></td>
				<td width='80' class='maintitle' align='center'><b>дата</b></td>
				<td width='80' class='maintitle' align='center'>&nbsp;</td>
				<td class='maintitle' align='left'><b>заголовок</b></td>
				<td width='30' class='maintitle' align='center'><b>del</b></td>
			</tr>";

	$perpage=10;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";

	$sql_query="SELECT id, enable, main, dt, name, code FROM ".$sql_pref."_news WHERE company_id=".$company_id." ORDER BY code".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $enable, $main, $dt, $name, $code)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			$dt_show=$dt;
			if ($dt_show=="0000-00-00") $dt_show=" - ";
			if (empty($name)) $name="<i><без заголовка></i>";
//			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=news_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение раздела' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=news_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение раздела' border=0></a>";
			if ($main=='Yes') $main_pic="<a href='?id=".$id."&action=news_main_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение на главной' border=0></a>"; else $main_pic="<a href='?id=".$id."&action=news_main_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение на главной' border=0></a>";
			if ($code>1) $sort_up="<a href='?id=".$id."&action=news_sort_up'><img src='/admin/img/up.gif' width=11 height=13 alt='Сортировка: Выше' border=0></a>"; else $sort_up="<img src='/admin/img/sort_none.gif' width=11 height=13 border=0>";
			$sql_query_1="SELECT id FROM ".$sql_pref."_news WHERE code='".($code+1)."'";
			$sql_res_1=mysql_query($sql_query_1, $conn_id);
			if (mysql_num_rows($sql_res_1)>0) $sort_down="<a href='?id=".$id."&action=news_sort_down'><img src='/admin/img/down.gif' width=11 height=13 alt='Сортировка: Ниже' border=0></a>"; else $sort_down="<img src='/admin/img/sort_none.gif' width=11 height=13 border=0>";
//			if (file_exists($path."files/news/thumbs/".$id.".jpg") || file_exists($path."files/news/thumbs/".$id.".gif")) $imga="<a href='?id=".$id."&action=news_mainimg#news_mainimg'><img src='/admin/img/img.gif' width=25 height=13 alt='Основное изображение' border=0></a>"; else $imga="<a href='?id=".$id."&action=news_mainimg#news_mainimg'><img src='/admin/img/img_ina.gif' width=25 height=13 alt='Основное изображение' border=0></a>";
			if (is_dir($path."files/news/".$id) && is_cat_empty($path."files/news/".$id)==false)  $imgs="<a href='?id=".$id."&action=news_images#news_images'><img src='/admin/img/img.gif' width=25 height=13 alt='Дополнительные изображения' border=0></a>"; else $imgs="<a href='?id=".$id."&action=news_images#news_images'><img src='/admin/img/img_ina.gif' width=25 height=13 alt='Дополнительные изображения' border=0></a>";
			$edit_pic="<a href='?id=".$id."&action=news_edit#news_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='index.html?id=".$id."&action=news_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			$out.= "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$dt_show."</td>
					<td class='common' align='center'>".$main_pic.$edit_pic."</td>
					<td class='common' align='left'>".$name."</td>
					<td class='common' align='center'>".$del."</td>
				</tr>";
		}
	}
	$out.= "</table>";
	$out.= "<br><li><a href='?action=news_add#news_add'>Добавить новость</a></li>";
	$sql_query="SELECT id FROM ".$sql_pref."_news WHERE company_id=".$company_id;
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		$out.= "<br><br><div align=left>Страницы: ";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show=$i;
			$out.= "<span style='padding:2 3 2 3;background-color:#eeeeee;border:solid 1px #aaaaaa;'><a href='?page=".$i."' style='text-decoration:none;'>".$i_show."</a></span> ";
		}
		$out.= "</div><br>";
	}
	$out.= "<hr>";
    return $out;    
}

function form_news_edit($company_id)
{
    
    global $sql_pref, $conn_id, $path;
    
    $out="";
    
    if (isset($_REQUEST['action']) && $_REQUEST['action']=="news_add")
    {
    	$imgpath="";
    	$imgpath_www="";
    	$xc2_dt=date("Y-m-d");
    	$enable="Yes";
    	$main="Yes";
    	$title="Добавление новости";
    }
    if (isset($_REQUEST['action']) && $_REQUEST['action']=="news_edit")
    {
    	$imgpath=$path."files/news/".$_REQUEST['id']."/";
    	$imgpath_www="/files/news/".$_REQUEST['id']."/";
    	$sql_query="SELECT id, enable, main, dt, name, descr, content, tags, source FROM ".$sql_pref."_news WHERE id='".$_REQUEST['id']."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
    	{
    		list($id, $enable, $main, $dt, $name, $descr, $content, $tags, $source)=mysql_fetch_row($sql_res);
    		$name=stripslashes($name);
            $source=stripslashes($source);
    		$descr=stripslashes($descr);
    		$content=stripslashes($content);
    		$xc2_dt=$dt;
    //		$cur_day=substr($dt,8,2); $cur_month=substr($dt,5,2); $cur_year=substr($dt,0,4);
    
    		if (isset($_REQUEST['id']) && file_exists($path."files/news/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
    		elseif (isset($_REQUEST['id']) && file_exists($path."files/news/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
    		if (isset($fname))
    		{
    			$size=getimagesize($path."files/news/imgs/".$fname);
    			$size_th=getimagesize($path."files/news/thumbs/".$fname);
    		}
    	}
    	$title="Редактирование новости (id: ".$_REQUEST['id'].")";
    }
    
    $xc2_inc=file_get_contents($path."inc/xc2.inc");
    $content_script="    
        <script type='text/javascript'>
            new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
        </script>";
    
    $out.="<link rel=stylesheet href='/admin/xc2/css/xc2_default.css' type='text/css'>
            <script language='javascript' src='/admin/xc2/config/xc2_default.js'></script>
            <script language='javascript' src='/admin/xc2/script/xc2_inpage.js'></script>
            <script language='JavaScript'>
                function check_date()
                {
                    //alert('asd');
                	if (document.form_name.date.checked==false)
                	{
                		document.form_name.xc2_dt.disabled=true;
                		if (document.form_name.xc2_dt.value=='0000-00-00')
                		{
                			var currentTime = new Date()
                			var month = currentTime.getMonth() + 1
                			var day = currentTime.getDate()
                			var year = currentTime.getFullYear()
                			if (month < 10) {month = '0' + month;}
                			if (day < 10) {day = '0' + day;}
                			document.form_name.xc2_dt.value=year + '-' + month + '-' + day;
                		}
                	}
                	else document.form_name.xc2_dt.disabled=false;
                }
            </script>";
    $out.="
    <a name='news_add'></a><a name='news_edit'></a>
    <form name='form_name' method='post' enctype='multipart/form-data'>
        <input type='hidden' name='id' value='".$id."'>
        <input type='hidden' name='company_id' value='".$company_id."'>
        <input type='hidden' name='action' value='news_save'>
        <table cellpadding='2' cellspacing='2' border='0' bgcolor='#FFFFFF'>
        	<tr>
        		<td class='form_left'>Заголовок</td>
        		<td class='form_main'><input class='form' type='text' name='name' value='".$name."'></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Дата</td>
        		<td class='form_main'>
        			<table>
        				<tr>
        					<td><input name='date' type='checkbox' value='Yes' onclick='check_date()' ";
                            if (isset($dt) && $dt=='0000-00-00') $out.=""; else $out.= 'checked';
                            $out.="></td>
        					<td id='holder'><input type='text' name='xc2_dt' id='xc2_dt' maxlength=10 size=10 value='".$xc2_dt."' onclick='showCalendar(\"\",document.getElementById(\"xc2_dt\"),null,\"".$xc2_dt."\",\"holder\",0,25,1)'></td>
        				</tr>
        			</table>
        			<script language='JavaScript'>check_date();</script>
        		</td>
        	</tr>
        	<tr>
        		<td class='form_left'>Анонс</td>
        		<td class='form_main'><textarea class='form' name='descr' rows='4'>".$descr."</textarea></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Новость</td>
        		<td class='form_main'><textarea class='form' style='width:450px;height:120;font-size:14px;' id=content name='content' rows='4'>".$content."</textarea></td>
        	</tr>        	
        	<tr>
        		<td>&nbsp;</td>
        		<td style='padding-top:10;'><input class='form_button' type='submit' name='button_submit' value='Сохранить'></td>
        	</tr>
        </table>
        </form>".$content_script;
    return $out;
}

function form_news_save()
{
	global $sql_pref, $conn_id;
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (!isset($_REQUEST['main']) || $_REQUEST['main']!="Yes") $main="No"; else $main="Yes";
    if (isset($_REQUEST['company_id'])) $company_id=$_REQUEST['company_id']; else $company_id=0;
	if (isset($_REQUEST['name'])) $name=$_REQUEST['name']; else $name="";
	$name=htmlspecialchars($name, ENT_QUOTES); $name=addslashes($name);
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
	if (isset($_REQUEST['content'])) $content=addslashes($_REQUEST['content']); else $content="";
//	if (isset($_REQUEST['dt_day']) && isset($_REQUEST['dt_month']) && isset($_REQUEST['dt_year']) && isset($_REQUEST['date']) && $_REQUEST['date']=="Yes") $dt=$_REQUEST['dt_year']."-".$_REQUEST['dt_month']."-".$_REQUEST['dt_day']; else $dt="0000-00-00";
	if (isset($_REQUEST['xc2_dt']) && $_REQUEST['date']=="Yes") $dt=$_REQUEST['xc2_dt']; else $dt="0000-00-00";
	if (isset($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
	if (isset($_REQUEST['source_main'])) {$source=htmlspecialchars($_REQUEST['source_main'], ENT_QUOTES); $source=addslashes($source);} else {$source="";}
    
	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_news SET enable='".$enable."', main='".$main."', dt='".$dt."', name='".$name."',	descr='".$descr."',	content='".$content."',	tags='".$tags."', source='".$source."' WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=$_REQUEST['id'];
	}
	else
	{
		$sql_query="SELECT COUNT(*) FROM ".$sql_pref."_news";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	list($total)=mysql_fetch_row($sql_res);
        $total=$total+1;
        
		$sql_query="INSERT INTO ".$sql_pref."_news (enable, main, dt, name, descr, content, tags, code, source, company_id) VALUES ('".$enable."', '".$main."', '".$dt."', '".$name."', '".$descr."', '".$content."', '".$tags."', '".$total."', '".$source."', '".$company_id."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=mysql_insert_id();
	}
    header("location:news"); exit();    
	//if (is_uploaded_file( $_FILES['file_name']['tmp_name'])) form_news_mainimg_save($pic_id);	
	
}







function auth_admin_company_vacancies($company_id)
{
    global $sql_pref, $conn_id, $path;
	$out= "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
			<tr class='maintitle'>
				<td width='20' class='maintitle' align='center'><b>id</b></td>
				<td width='80' class='maintitle' align='center'><b>дата</b></td>
				<td width='80' class='maintitle' align='center'>&nbsp;</td>
				<td class='maintitle' align='left'><b>Название вакансии /<br/> Работодатель</b></td>
				<td width='80' class='maintitle' align='center'><b>Зарплата</b></td>
                <td width='80' class='maintitle' align='center'><b>Город</b></td>
                <td width='30' class='maintitle' align='center'><b>del</b></td>
			</tr>";

	$perpage=10;
	if (isset($_REQUEST['page'])) $page=$_REQUEST['page']; else $page=1;
	$pref_page=" LIMIT ".(($page-1)*$perpage).",".$perpage."";

	$sql_query="SELECT id, enable, main, dt, name, zp_value_min, zp_value_max, zp_valuta, company_id code FROM ".$sql_pref."_vacancies WHERE company_id=".$company_id." ORDER BY dt DESC".$pref_page;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while (list($id, $enable, $main, $dt, $name, $zp_value_min, $zp_value_max, $zp_valuta, $code)=mysql_fetch_row($sql_res))
		{
			$name=stripslashes($name);
			$dt_show=$dt;
			if ($dt_show=="0000-00-00") $dt_show=" - ";
			if (empty($name)) $name="<i><без заголовка></i>";
//			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=vacancies_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение раздела' border=0></a>"; else $enable_pic="<a href='?id=".$id."&action=vacancies_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение раздела' border=0></a>";
			if ($main=='Yes') $main_pic="<a href='?id=".$id."&action=vacancies_main_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение на главной' border=0></a>"; else $main_pic="<a href='?id=".$id."&action=vacancies_main_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение на главной' border=0></a>";
			if ($code>1) $sort_up="<a href='?id=".$id."&action=vacancies_sort_up'><img src='/admin/img/up.gif' width=11 height=13 alt='Сортировка: Выше' border=0></a>"; else $sort_up="<img src='/admin/img/sort_none.gif' width=11 height=13 border=0>";
			$sql_query_1="SELECT id FROM ".$sql_pref."_vacancies WHERE code='".($code+1)."'";
			$sql_res_1=mysql_query($sql_query_1, $conn_id);
			if (mysql_num_rows($sql_res_1)>0) $sort_down="<a href='?id=".$id."&action=vacancies_sort_down'><img src='/admin/img/down.gif' width=11 height=13 alt='Сортировка: Ниже' border=0></a>"; else $sort_down="<img src='/admin/img/sort_none.gif' width=11 height=13 border=0>";            
//			if (file_exists($path."files/vacancies/thumbs/".$id.".jpg") || file_exists($path."files/vacancies/thumbs/".$id.".gif")) $imga="<a href='?id=".$id."&action=vacancies_mainimg#vacancies_mainimg'><img src='/admin/img/img.gif' width=25 height=13 alt='Основное изображение' border=0></a>"; else $imga="<a href='?id=".$id."&action=vacancies_mainimg#vacancies_mainimg'><img src='/admin/img/img_ina.gif' width=25 height=13 alt='Основное изображение' border=0></a>";
			if (is_dir($path."files/vacancies/".$id) && is_cat_empty($path."files/vacancies/".$id)==false)  $imgs="<a href='?id=".$id."&action=vacancies_images#vacancies_images'><img src='/admin/img/img.gif' width=25 height=13 alt='Дополнительные изображения' border=0></a>"; else $imgs="<a href='?id=".$id."&action=vacancies_images#vacancies_images'><img src='/admin/img/img_ina.gif' width=25 height=13 alt='Дополнительные изображения' border=0></a>";
			$edit_pic="<a href='?id=".$id."&action=vacancies_edit#vacancies_edit'><img src='/admin/img/edit.gif' width=25 height=13 alt='Редактировать' border=0></a>";
			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&action=vacancies_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
			$out.= "<tr class='common'>
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$dt_show."</td>
					<td class='common' align='center'>".$main_pic.$edit_pic."</td>
					<td class='common' align='left'>".$name."</td>
					<td class='common' align='left'>".$zp_value_min."-".$zp_value_max." ".$zp_valuta."</td>
                    <td class='common' align='left'>".$name."</td>
                    <td class='common' align='center'>".$del."</td>
				</tr>";
		}
	}
	$out.= "</table>";
	$out.= "<br><li><a href='?action=vacancies_add#vacancies_add'>Добавить вакансию</a></li>";
	$sql_query="SELECT id FROM ".$sql_pref."_vacancies WHERE company_id=".$company_id;
	$sql_res=mysql_query($sql_query, $conn_id);
	$num_predl=mysql_num_rows($sql_res);
	$numpages=ceil($num_predl/$perpage);
	if ($numpages>1)
	{
		$out.= "<br><br><div align=left>Страницы: ";
		for ($i=1;$i<=$numpages;$i++)
		{
			if ($page==$i) $i_show="<b>".$i."</b>"; else $i_show=$i;
			$out.= "<span style='padding:2 3 2 3;background-color:#eeeeee;border:solid 1px #aaaaaa;'><a href='?page=".$i."' style='text-decoration:none;'>".$i_show."</a></span> ";
		}
		$out.= "</div><br>";
	}
	$out.= "<hr>";
    return $out;    
}

function form_vacancies_edit($company_id)
{
    
    global $sql_pref, $conn_id, $path;
    
    $out="";
    
    if (isset($_REQUEST['action']) && $_REQUEST['action']=="vacancies_add")
    {
    	$imgpath="";
    	$imgpath_www="";
    	$xc2_dt=date("Y-m-d");
    	$enable="Yes";
    	$main="Yes";
    	$title="Добавление вакансии";
    }
    if (isset($_REQUEST['action']) && $_REQUEST['action']=="vacancies_edit")
    {
    	$sql_query="SELECT id, enable, main, dt, name, zp_value_min, zp_value_max, zp_valuta, descr, content, tags, company_id, city_id FROM ".$sql_pref."_vacancies WHERE id='".$_REQUEST['id']."'";
	    $sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)>0)
    	{
    		list($id, $enable, $main, $dt, $name, $zp_value_min, $zp_value_max, $zp_valuta, $descr, $content, $tags, $company_id, $city_id)=mysql_fetch_row($sql_res);
    		$name=stripslashes($name);
    		$descr=stripslashes($descr);
    		$content=stripslashes($content);
    		$xc2_dt=$dt;
    //		$cur_day=substr($dt,8,2); $cur_month=substr($dt,5,2); $cur_year=substr($dt,0,4);
    
    		if (isset($_REQUEST['id']) && file_exists($path."files/vacancies/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
    		elseif (isset($_REQUEST['id']) && file_exists($path."files/vacancies/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
    		if (isset($fname))
    		{
    			$size=getimagesize($path."files/vacancies/imgs/".$fname);
    			$size_th=getimagesize($path."files/vacancies/thumbs/".$fname);
    		}
    	}
    	$title="Редактирование вакансии (id: ".$_REQUEST['id'].")";
    }
    
    $xc2_inc=file_get_contents($path."inc/xc2.inc");
    $content_script="    
        <script type='text/javascript'>
            new nicEditor({buttonList : ['bold','italic','underline','strikeThrough','subscript','superscript','ol','ul','link','unlink','image','forecolor','xhtml']}).panelInstance('content');
        </script>";
    
    $out.="<link rel=stylesheet href='/admin/xc2/css/xc2_default.css' type='text/css'>
            <script language='javascript' src='/admin/xc2/config/xc2_default.js'></script>
            <script language='javascript' src='/admin/xc2/script/xc2_inpage.js'></script>
            <script language='JavaScript'>
                function check_date()
                {
                    //alert('asd');
                	if (document.form_name.date.checked==false)
                	{
                		document.form_name.xc2_dt.disabled=true;
                		if (document.form_name.xc2_dt.value=='0000-00-00')
                		{
                			var currentTime = new Date()
                			var month = currentTime.getMonth() + 1
                			var day = currentTime.getDate()
                			var year = currentTime.getFullYear()
                			if (month < 10) {month = '0' + month;}
                			if (day < 10) {day = '0' + day;}
                			document.form_name.xc2_dt.value=year + '-' + month + '-' + day;
                		}
                	}
                	else document.form_name.xc2_dt.disabled=false;
                }
            </script>";
    $out.="
    <a name='vacancies_add'></a><a name='vacancies_edit'></a>
    <form name='form_name' method='post' enctype='multipart/form-data'>
        <input type='hidden' name='id' value='".$id."'>
        <input type='hidden' name='company_id' value='".$company_id."'>
        <input type='hidden' name='action' value='vacancies_save'>
        <table cellpadding='2' cellspacing='2' border='0' bgcolor='#FFFFFF'>
        	<tr>
        		<td class='form_left'>Название вакансии</td>
        		<td class='form_main'><input class='form' type='text' name='name' value='".$name."'></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Дата</td>
        		<td class='form_main'>
        			<table>
        				<tr>
        					<td><input name='date' type='checkbox' value='Yes' onclick='check_date()' ";
                            if (isset($dt) && $dt=='0000-00-00') $out.=""; else $out.= 'checked';
                            $out.="></td>
        					<td id='holder'><input type='text' name='xc2_dt' id='xc2_dt' maxlength=10 size=10 value='".$xc2_dt."' onclick='showCalendar(\"\",document.getElementById(\"xc2_dt\"),null,\"".$xc2_dt."\",\"holder\",0,25,1)'></td>
        				</tr>
        			</table>
        			<script language='JavaScript'>check_date();</script>
        		</td>
        	</tr>
        	<tr>
        		<td class='form_left'>Вакансия</td>
        		<td class='form_main'><textarea class='form' style='width:450px;height:120;font-size:14px;' id=content name='content' rows='4'>".$content."</textarea></td>
        	</tr> 
            <tr>
        		<td class='form_left'>Зарплата</td>
        		<td class='form_main'>От <input class='form' style='width:70' type='text' name='zp_value_min' value='".$zp_value_min."'> До <input class='form' style='width:70' type='text' name='zp_value_max' value='".$zp_value_max."'> Валюта <input class='form' style='width:70' type='text' name='zp_valuta' value='".$zp_valuta."'</td>
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
        						$out.= "<option value=".$c_id." ".$select.">".$c_name." (".$r_name.")</option>";
        					}
        			$out.="</select>
                    <div>
                        <span onclick=\"document.getElementById('city_id').value='1459';\" style='cursor:pointer;border-bottom:dotted 1px gray;'>Москва</span>
                        <span onclick=\"document.getElementById('city_id').value='1900';\" style='cursor:pointer;border-bottom:dotted 1px gray;'>Санкт-Петербург</span>
                    </div>
        		</td>
        	</tr>       	
        	<tr>
        		<td>&nbsp;</td>
        		<td style='padding-top:10;'><input class='form_button' type='submit' name='button_submit' value='Сохранить'></td>
        	</tr>
        </table>
        </form>".$content_script;
    return $out;
}

function form_vacancies_save()
{
	global $sql_pref, $conn_id;
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (!isset($_REQUEST['main']) || $_REQUEST['main']!="Yes") $main="No"; else $main="Yes";
	if (isset($_REQUEST['name'])) $name=$_REQUEST['name']; else $name="";
	$name=htmlspecialchars($name, ENT_QUOTES); $name=addslashes($name);
	if (isset($_REQUEST['zp_value_min'])) $zp_value_min=$_REQUEST['zp_value_min']; else $zp_value_min="0";
	$zp_value_min=htmlspecialchars($zp_value_min, ENT_QUOTES); $zp_value_min=addslashes($zp_value_min);
	if (isset($_REQUEST['zp_value_max'])) $zp_value_max=$_REQUEST['zp_value_max']; else $zp_value_max="0";
	$zp_value_max=htmlspecialchars($zp_value_max, ENT_QUOTES); $zp_value_max=addslashes($zp_value_max);
    if (isset($_REQUEST['zp_valuta'])) $zp_valuta=$_REQUEST['zp_valuta']; else $zp_valuta="руб";
	$zp_valuta=htmlspecialchars($zp_valuta, ENT_QUOTES); $zp_valuta=addslashes($zp_valuta);
    if (isset($_REQUEST['city_id'])) $city_id=addslashes($_REQUEST['city_id']); else $city_id=0;
	if (isset($_REQUEST['descr'])) $descr=addslashes($_REQUEST['descr']); else $descr="";
	if (isset($_REQUEST['FCKeditor1'])) $content=addslashes($_REQUEST['FCKeditor1']); else $content="";
//	if (isset($_REQUEST['dt_day']) && isset($_REQUEST['dt_month']) && isset($_REQUEST['dt_year']) && isset($_REQUEST['date']) && $_REQUEST['date']=="Yes") $dt=$_REQUEST['dt_year']."-".$_REQUEST['dt_month']."-".$_REQUEST['dt_day']; else $dt="0000-00-00";
	if (isset($_REQUEST['xc2_dt']) && $_REQUEST['date']=="Yes") $dt=$_REQUEST['xc2_dt']; else $dt="0000-00-00";
	if (isset($_REQUEST['tags'])) $tags=addslashes($_REQUEST['tags']); else $tags="";
	if (isset($_REQUEST['company_id']) AND !empty($_REQUEST['company_id'])) $company_id=$_REQUEST['company_id']; else $company_id="0";

	if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	{
		$sql_query="UPDATE ".$sql_pref."_vacancies SET enable='".$enable."', main='".$main."', dt='".$dt."', name='".$name."', zp_value_min=".$zp_value_min.", zp_value_max=".$zp_value_max.", zp_valuta='".$zp_valuta."',	descr='".$descr."',	content='".$content."',	tags='".$tags."', city_id='".$city_id."', company_id=".$company_id." WHERE id='".$_REQUEST['id']."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=$_REQUEST['id'];
	}
	else
	{
		$sql_query="SELECT id FROM ".$sql_pref."_vacancies";
		$sql_res=mysql_query($sql_query, $conn_id);
		$code=mysql_num_rows($sql_res);
		for ($i=$code; $i>=1; $i--)
		{
			$sql_query="UPDATE ".$sql_pref."_vacancies SET code='".($i+1)."' WHERE code='".$i."'";
			$sql_res=mysql_query($sql_query, $conn_id);
		}
		$sql_query="INSERT INTO ".$sql_pref."_vacancies (enable, main, dt, name, descr, content, tags, code, zp_value_min, zp_value_max, zp_valuta, company_id, city_id) VALUES ('".$enable."', '".$main."', '".$dt."', '".$name."', '".$descr."', '".$content."', '".$tags."', '1', '".$zp_value_min."', '".$zp_value_max."', '".$zp_valuta."', '".$company_id."', '".$city_id."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_id=mysql_insert_id();
	}
	if (is_uploaded_file( $_FILES['file_name']['tmp_name'])) form_vacancies_mainimg_save($pic_id);
	header("location:vacancies"); exit();    
	
}



?>