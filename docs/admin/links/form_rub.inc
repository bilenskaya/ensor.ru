<?php
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rub_add")
{
	$title="Создание рубрики";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rub_edit")
{
	$sql_query="SELECT id, url, enable, name, content FROM ".$sql_pref."_links_rubs WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if(mysql_num_rows($sql_res)>0)
	{
		list($id, $url, $enable, $name, $content)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); 
		$content=stripslashes($content);
	}
	$title="Редактирование рубрики (id: ".$_REQUEST['id'].")";
}
?>
<a name='rub_add'></a><a name='rub_edit'></a>
<form name="form_name" action="index.html" method="post">
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
		<td class="form_left">Краткое описание</td>
		<td class="form_main"><textarea class="form" name="content" rows="3"><?php if (isset($content)) echo $content; ?></textarea></td>
	</tr>
	<tr>
		<td class="form_left">Настройки</td>
		<td class="form_main">
			<input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>> - активность<br>
		</td>
	</tr>
	<tr>
		<td class="form_left">Виртуальный URL<br><nobr><span style='font-weight:normal;color:#777777;'><i>Необязательное поле</i></span><nobr></td>
		<td class="form_main"> 
			<input class="form" type="text" name="url" value='<?php if(isset($url)) echo $url; ?>'><br>
			<a href='javascript: razver("vurlrub");' style='text-decoration:none;border-bottom: dotted 1px gray;color:#777777;'>Что такое "Виртуальный URL"</a>
			<div id='vurlrub' style='display: none; '>
				Виртуальный URL отображается в адресной строке браузера. Например, <?echo $path_www."<b>contacts</b>/";?><br>
				Допустимы только латинские буквы и цифры без пробелов.<br>
				Если поле не заполнено, виртуальный URL будет сформирован автоматически.
			</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="form_button" type="submit" name="form_rub_save" value="Сохранить"></td>
	</tr>
</table>
</form>