<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="top_management_add")
{
	$enable="Yes";	
	$title="Добавление менеджера";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="top_management_edit")
{
	$sql_query="SELECT id, surname, name, name2, email, phone_work, phone_mobile, pol, dt_birth, company_id, doljnost, expirience, vuz, specialnost, enable FROM ".$sql_pref."_top_management WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $surname, $name, $name2, $email, $phone_work, $phone_mobile, $pol, $dt_birth, $company_id, $doljnost, $expirience, $vuz, $specialnost, $enable)=mysql_fetch_row($sql_res);
		$surname=stripslashes($surname); $name=stripslashes($name); $name2=stripslashes($name2); $phone_work=stripslashes($phone_work); $phone_mobile=stripslashes($phone_mobile); $doljnost=stripslashes($doljnost); $expirience=stripslashes($expirience); $vuz=stripslashes($vuz); $specialnost=stripslashes($specialnost);
	}
	$title="Редактирование менеджера (id: ".$_REQUEST['id'].")";
    
    if (file_exists($path."files/top_management/img/".$id.".jpg"))
		$img_show="<div style='padding:10 0;'><img src='/files/top_management/avatar/".$id.".jpg' border=0>&nbsp;<a href='?action=img_del&id=".$id."'><img src='/img/del.gif' width=25 height=13 border=0></a></div>";
    
}
?>

<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="top_management_save">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
<input type="hidden" name="letter" value="<?php if (isset($_REQUEST['letter'])) echo $_REQUEST['letter']; ?>">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($company)) echo $company; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">E-Mail</td>
		<td class="form_main"><input class="form" type="text" name="email" value='<?php if (isset($email)) echo $email; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Фамилия</td>
		<td class="form_main"><input class="form" type="text" name="surname" value='<?php if (isset($surname)) echo $surname; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Имя</td>
		<td class="form_main"><input class="form" type="text" name="name" value='<?php if (isset($name)) echo $name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Отчество</td>
		<td class="form_main"><input class="form" type="text" name="name2" value='<?php if (isset($name2)) echo $name2; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">День рождения</td>
		<td class="form_main"><input class="form" type="text" name="dt_birth" value='<?php if (isset($dt_birth)) echo $dt_birth; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Пол</td>
		<td class="form_main"><div><input type="radio" name="pol" value="m" <?php if (isset($pol) && $pol=="m") echo ("checked"); ?>> - мальчик &nbsp; &nbsp; &nbsp; <input type="radio" name="pol" value="w" <?php if (isset($pol) && $pol=="w") echo ("checked"); ?>> - девочка</div></td>
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
		<td class="form_left">Должность</td>
		<td class="form_main"><input class="form" type="text" name="doljnost" value='<?php if (isset($doljnost)) echo $doljnost; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Опыт работы</td>
		<td class="form_main"><textarea name="expirience" style="width: 500;" rows="4"><?php if (isset($expirience)) echo $expirience; ?></textarea></td>
	</tr>
	<tr>
		<td class="form_left">Телефон (рабочий)</td>
		<td class="form_main"><input class="form" type="text" name="phone_work" value='<?php if (isset($phone_work)) echo $phone_work; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Телефон (мобильный)</td>
		<td class="form_main"><input class="form" type="text" name="phone_mobile" value='<?php if (isset($phone_mobile)) echo $phone_mobile; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">ВУЗ</td>
		<td class="form_main"><input class="form" type="text" name="vuz" value='<?php if (isset($vuz)) echo $vuz; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Специальность</td>
		<td class="form_main"><input class="form" type="text" name="specialnost" value='<?php if (isset($specialnost)) echo $specialnost; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Настройки</td>
		<td class="form_main">
            <div><input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>> - активность</div>
            
        </td>
	</tr>
    <tr>
		<td class="form_left">Фото (jpg)</td>
		<td class="form_main">
            <div><?php if (isset($img_show)) echo $img_show; ?><input type="file" name="file_name" size="35"></div>
            
        </td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class="form_button" type="submit" name="button_submit" value="Сохранить"></td>
	</tr>
</table>
</form>