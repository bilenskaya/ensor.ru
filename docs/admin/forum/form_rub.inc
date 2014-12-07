<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rub_add")
{
	$title="Создание рубрики";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rub_edit")
{
	$sql_query="SELECT id, url, enable, name, descr FROM ".$sql_pref."_forum_rubs WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if(mysql_num_rows($sql_res)>0)
	{
		list($id, $url, $enable, $name, $descr)=mysql_fetch_row($sql_res);
		$name=StripSlashes($name); 
		$content=StripSlashes($content);
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
<table width="700" cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if(isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">Название рубрики</td>
		<td class="form_main"><input class="form" type="text" name="name" value='<?php if(isset($name)) echo $name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Краткое описание рубрики</td>
		<td class="form_main"><textarea class="form" name="descr" rows="4"><?php if(isset($descr)) echo $descr; ?></textarea></td>
	</tr>
	<tr>
		<td class="form_left">Активность рубрики</td>
		<td class="form_main"><input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>></td>
	</tr>
	<tr>
		<td class="form_left">Виртуальный URL</td>
		<td class="form_main">
			<input class="form" type="text" name="url" value='<?php if(isset($url)) echo $url; ?>'>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="form_button" type="submit" name="form_rub_save" value="Сохранить"></td>
	</tr>
</table>
</form>
