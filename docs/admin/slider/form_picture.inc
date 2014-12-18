<?

	$imgpath="";
	$enable="Yes";
	$title="Добавление картинки слайдера";



?>

<a name='catalog_add'></a><a name='catalog_edit'></a>
<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="picture_trim">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>


	<tr>
		<td class="form_left">Изображение</td>
		<td class="form_main">
			<div><input class="form_file" type="file" name="img_name" size="65"></div>
		</td>
	</tr>

	<tr>
		<td class="form_left">Настройки</td>
		<td class="form_main">
			<input type="checkbox" name="enable" value="Yes" checked> - активность<br>
			<input type="checkbox" name="def" value="Yes"> - по умолчанию<br>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class="form_button" type="submit" name="button_submit" value="Сохранить"></td>
	</tr>
</table>
</form>