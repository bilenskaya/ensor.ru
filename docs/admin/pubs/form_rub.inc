<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rub_add")
{
	$title="Создание рубрики";
	$enable="Yes";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rub_edit")
{
	$sql_query="SELECT id, url, enable, name, content, auth, module, adv_content FROM ".$sql_pref."_pub_rubs WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if(mysql_num_rows($sql_res)>0)
	{
		list($id, $url, $enable, $name, $content, $auth, $module, $adv_content)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); 
		$content=stripslashes($adv_content);
        $page_adv_content=stripslashes($adv_content);
	}
	$title="Редактирование рубрики (id: ".$_REQUEST['id'].")";
}
?>
<a name='rub_add'></a><a name='rub_edit'></a>
<form action="index.html" method="post">
<input type="hidden" name="action" value="rub_save">
<input type="hidden" name="id" value="<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>">
<input type="hidden" name="parent_id" value="<?php if(isset($_REQUEST['parent_id'])) echo $_REQUEST['parent_id']; ?>">
<input type="hidden" name="level" value="<?php if(isset($_REQUEST['level'])) echo $_REQUEST['level']; ?>">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><span ondblclick="document.getElementById('tr_modul').style.display='';"><?php if(isset($title)) echo $title; ?></span></b></td>
	</tr>
	<tr>
		<td class="form_left">Название</td>
		<td class="form_main"><input class="form" type="text" name="name" value='<?php if(isset($name)) echo $name; ?>'></td>
	</tr>
	<!--tr>
		<td class="form_left">Краткое описание рубрики</td>
		<td class="form_main"><textarea class="form" name="content" rows="4"><?php if(isset($content)) echo $content; ?></textarea></td>
	</tr-->
	<tr>
		<td class="form_left">Настройки</td>
		<td class="form_main">
			<input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>> - активность<br>
			<!-- <input type="checkbox" name="auth" value="Yes" <?php if (isset($auth) && $auth=="Yes") echo ("checked"); ?>> - только для зарегистрированных пользователей -->
		</td>
	</tr>
	<tr>
		<td class="form_left">Виртуальный URL<br><nobr><span style='font-weight:normal;color:#777777;'><i>Необязательное поле</i></span><nobr></td>
		<td class="form_main"> 
			<input class="form" type="text" name="url" value='<?php if(isset($url)) echo $url; ?>'>
		</td>
	</tr>
	<tr>
		<td class="form_left">Рекламный текст<br><nobr><span style='font-weight:normal;color:#777777;'><i>Необязательное поле</i></span><nobr></td>
		<td class="form_main">
			<?php require_once("../vis/visual.php"); ?>
		</td>
	</tr>
	<tr id=tr_modul style='display:none;'>
		<td class="form_left">Модуль</td>
		<td class="form_main"> 
			<input class="form" type="text" name="module" value='<?php if(isset($module)) echo $module; ?>'>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class="form_button" type="submit" name="form_rub_save" value="Сохранить"></td>
	</tr>
</table>
</form>
<?php if(!isset($id)) echo "После создания рубрики, необходимо создать страницу с текстовым наполнением этой рубрики"; ?>



