<?php
if ($_REQUEST['action']=="art_add")
{
	$enable="Yes";
	$title1="Новая ссылка";
}
if ($_REQUEST['action']=="art_edit")
{
	$sql_query="SELECT id, link, enable, name, descr, button, special, backlink, backlink_name, backlink_email FROM ".$sql_pref."_links_arts WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if(mysql_num_rows($sql_res)>0)
	{
		list($id, $link, $enable, $name, $descr, $button, $special, $backlink, $backlink_name, $backlink_email)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); $link=stripslashes($link); $descr=stripslashes($descr); $button=stripslashes($button);$backlink_name=stripslashes($backlink_name);
	}
	$title1="Редактирование (id: ".$_REQUEST['id'].")";
}
?>
<form name="form_name" action="index.html" method="post">
<input type="hidden" name="action" value="art_save">
<input type="hidden" name="id" value="<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>">
<input type="hidden" name="parent_id" value="<?php if(isset($_REQUEST['parent_id'])) echo $_REQUEST['parent_id']; ?>">
<table width="700" cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($title1)) echo $title1; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">Ссылка</td>
		<td class="form_main"><input class="form" type="text" name="link" value='<?php if (isset($link)) echo $link; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Название</td>
		<td class="form_main"><input class="form" type="text" name="name" value='<?php if (isset($name)) echo $name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Описание</td>
		<td class="form_main"><textarea class="form" name="descr" rows="3"><?php if (isset($descr)) echo $descr; ?></textarea></td>
	</tr>
	<tr>
		<td class="form_left">Баннер</td>
		<td class="form_main"><textarea class="form" name="button" rows="3"><?php if (isset($button)) echo $button; ?></textarea></td>
	</tr>
	<tr>
		<td class="form_left">Обратка</td>
		<td class="form_main"><input class="form" type="text" name="backlink" value='<?php if (isset($backlink)) echo $backlink; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Обратка (имя)</td>
		<td class="form_main"><input class="form" type="text" name="backlink_name" value='<?php if (isset($backlink_name)) echo $backlink_name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Обратка (email)</td>
		<td class="form_main"><input class="form" type="text" name="backlink_email" value='<?php if (isset($backlink_email)) echo $backlink_email; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Настройки</td>
		<td class="form_main">
			<input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>> - активность<br>
			<input type="checkbox" name="special" value="Yes" <?php if (isset($special) && $special=="Yes") echo ("checked"); ?>> - спецссылка
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="form_button" type="submit" name="form_art_save" value="Сохранить"></td>
	</tr>
</table>
</form>
