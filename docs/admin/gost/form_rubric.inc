<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rubric_add")
{
	$enable="Yes";
	$title="Добавление рубрики";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rubric_edit")
{
	$sql_query="SELECT id, name, file, enable, fields, rus_fields FROM ".$sql_pref."_gost_rub WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $name, $file, $enable, $fields, $rus_fields)=mysql_fetch_row($sql_res);
		$name=stripslashes($name);
		$fields=unserialize($fields);
		$rus_fields=unserialize($rus_fields);
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
		<td class="form_left">Файл данных</td>
		<td class="form_main"><input class="form" type="text" name="file" value='<?php if (isset($file)) echo $file; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Отображаемые поля</td>
		<td class="form_main">
		<?php if (is_array($fields)) foreach($fields as $k=>$v)
			{
			if ($v==1) echo "<INPUT TYPE=CHECKBOX NAME='fields[".$k."]' checked> ".$rus_fields[$k]."<BR>";
			else echo "<INPUT TYPE=CHECKBOX NAME='fields[".$k."]'> ".$rus_fields[$k]."<BR>";}
		else echo "XML не загружен. Поля не определены";
			?>
		
		</td>
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