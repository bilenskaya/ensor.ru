<?
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
	$imgpath=$path."files/vacancies/".$_REQUEST['id']."/";
	$imgpath_www="/files/vacancies/".$_REQUEST['id']."/";
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
	$title="Редактирование новости (id: ".$_REQUEST['id'].")";
}
?>

<link rel=stylesheet href="../xc2/css/xc2_default.css" type="text/css">
<script language="javascript" src="../xc2/config/xc2_default.js"></script>
<script language="javascript" src="../xc2/script/xc2_inpage.js"></script>
<script language="JavaScript">
function check_date()
{
	if (document.form_name.date.checked==false)
	{
		document.form_name.xc2_dt.disabled=true;
		if (document.form_name.xc2_dt.value=="0000-00-00")
		{
			var currentTime = new Date()
			var month = currentTime.getMonth() + 1
			var day = currentTime.getDate()
			var year = currentTime.getFullYear()
			if (month < 10) {month = "0" + month;}
			if (day < 10) {day = "0" + day;}
			document.form_name.xc2_dt.value=year + "-" + month + "-" + day;
		}
	}
	else document.form_name.xc2_dt.disabled=false;
}
</script>


<a name='vacancies_add'></a><a name='vacancies_edit'></a>
<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
<input type="hidden" name="action" value="vacancies_save">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">Название вакансии</td>
		<td class="form_main"><input class="form" type="text" name="name" value='<?php if (isset($name)) echo $name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Дата</td>
		<td class="form_main">
			<table>
				<tr>
					<td><input name="date" type="checkbox" value="Yes" onclick="check_date()" <?php if (isset($dt) && $dt=="0000-00-00") echo ""; else echo "checked"; ?>></td>
					<td id="holder"><input type="text" name="xc2_dt" id="xc2_dt" maxlength=10 size=10 value="<?php echo $xc2_dt; ?>" onclick="showCalendar('',document.getElementById('xc2_dt'),null,'<?php echo $xc2_dt; ?>','holder',0,25,1);" style="width:70;cursor:pointer;"></td>
				</tr>
			</table>
			<script language="JavaScript">check_date();</script>
		</td>
	</tr>	
	<tr>
		<td class="form_left">Вакансия</td>
		<td class="form_main">
			<?php require_once("../vis/visual.php"); ?>
		</td>
	</tr>
    <tr>
		<td class="form_left">Компания</td>
		<td class="form_main">
        	<select name=company_id>
            <option value='0'>Нет данных</option>
            <?
        	$sql_query="SELECT id, name FROM ".$sql_pref."_companies WHERE enable='Yes' ORDER BY name";
        	$sql_res=mysql_query($sql_query, $conn_id);
        	if (mysql_num_rows($sql_res)>0)
        	{
        		while(list($c_id, $c_name)=mysql_fetch_row($sql_res))
        		{
        			$c_name=stripslashes($c_name);
                    if (@$company_id==$c_id) $sel="selected"; else $sel="";
        			echo "<option value='".$c_id."' ".$sel.">".$c_name."</option>";
        		}
        	}
            ?>
        	</select>
        </td>
	</tr>
	<tr>
		<td class="form_left">Зарплата</td>
		<td class="form_main">От <input class="form" style="width:70" type="text" name="zp_value_min" value='<?php if (isset($zp_value_min)) echo $zp_value_min; ?>'> До <input class="form" style="width:70" type="text" name="zp_value_max" value='<?php if (isset($zp_value_max)) echo $zp_value_max; ?>'> Валюта <input class="form" style="width:70" type="text" name="zp_valuta" value='<?php if (isset($zp_valuta)) echo $zp_valuta; ?>'</td>
	</tr>
	<tr>
		<td class=form_left>Город</td>
		<td class=form_main>
			<select name="city_id" id="city_id">
				<option value="0">Нет данных</option>
			   <?
					$sql_query="SELECT c.id, c.name, r.name FROM ".$sql_pref."_cities AS c, ".$sql_pref."_regions AS r WHERE c.region_id=r.id ORDER BY c.name";
					$sql_res=mysql_query($sql_query, $conn_id);
					while(list($c_id, $c_name, $r_name)=mysql_fetch_row($sql_res))
					{
						$c_name=stripslashes($c_name);$r_name=stripslashes($r_name);
						if ($c_id==@$city_id) $select="selected"; else $select="";
						echo "<option value=".$c_id." ".$select.">".$c_name." (".$r_name.")</option>";
					}
			   ?>
			</select>
            <div>
                <span onclick="document.getElementById('city_id').value='1459';" style="cursor:pointer;border-bottom:dotted 1px gray;">Москва</span>
                <span onclick="document.getElementById('city_id').value='1900';" style="cursor:pointer;border-bottom:dotted 1px gray;">Санкт-Петербург</span>
            </div>
		</td>
	</tr>
    
    
	<tr>
		<td class="form_left">Настройки</td>
		<td class="form_main">
			<input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>> - активность<br>
            <input type="checkbox" name="main" value="Yes" <?php if (isset($main) && $main=="Yes") echo ("checked"); ?>> - на главную
		</td>
	</tr>
	<tr>
		<td class="form_left">Теги</td>
		<td class="form_main"><input class="form" type="text" name="tags" value='<?php if (isset($tags)) echo $tags; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Изображение</td>
		<td class="form_main">
			<? if (isset($fname)) {?>
				<div>
                	<img src='/files/vacancies/thumbs/<? echo $fname;?>' border=1>
                    <a href="javascript:if(confirm('Вы уверены?'))window.location='?id=<? echo $_REQUEST['id'];?>&action=vacancies_mainimg_del&fname=<? echo $fname;?>'"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>
                </div>
			<? } ?>
			<div><input class="form_file" type="file" name="file_name" size="65"></div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class="form_button" type="submit" name="button_submit" value="Сохранить"></td>
	</tr>
</table>
</form>