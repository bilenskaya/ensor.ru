<?
if (file_exists($path."styles.css"))
{
	$content=file_get_contents($path."styles.css");
}
?>
<a name='rub_add'></a><a name='rub_edit'></a>
<form action="index.html" method="post">
<input type="hidden" name="action" value="styles_save">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b>Таблица стилей</b></td>
	</tr>
	<tr>
		<td class="form_left">styles.css</td>
		<td class="form_main"><textarea class="form" name="content" rows="30" style='font-size:12px;font-family:Courier New Cyr;'><?php if(isset($content)) echo $content; ?></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class="form_button" type="submit" name="form_rub_save" value="Сохранить"></td>
	</tr>
</table>
</form>



