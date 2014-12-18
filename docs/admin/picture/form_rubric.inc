<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rubric_add")
{
	$enable="Yes";
	$title="Добавление рубрики";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rubric_edit")
{
	$sql_query="SELECT id, name, enable  FROM ".$sql_pref."_picture_rub WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $name, $enable)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); $descr=stripslashes($descr); 
	}

	
	$title="Редактирование (id: ".$_REQUEST['id'].")";
}
?>

<a name='rubric_add'></a><a name='rubric_edit'></a>
<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
<input type="hidden" name="action" value="rubric_save">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">Название</td>
		<td class="form_main"><input class="form" type="text" name="name" value='<?php if (isset($name)) echo $name; ?>'></td>
	</tr>

	<tr>
		<td class="form_left">Настройки</td>
		<td class="form_main">
			<input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>> - активность<br>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class="form_button" type="submit" name="button_submit" value="Сохранить"></td>
	</tr>
</table>
</form>