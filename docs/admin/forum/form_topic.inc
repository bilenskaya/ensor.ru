<?
if ($_REQUEST['action']=="art_add")
{
	$imgpath="";
	$imgpath_www="";
	$title1="Создание страницы";
}
if ($_REQUEST['action']=="art_edit")
{
	$imgpath=$path."files/pubs/imgs/".$_REQUEST['id']."/";
	$imgpath_www="/files/pubs/imgs/".$_REQUEST['id']."/";
	$sql_query="SELECT id, url, enable, name, descr, content, title FROM ".$sql_pref."_pub_arts WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if(mysql_num_rows($sql_res)>0)
	{
		list($id, $url, $enable, $name, $descr, $content, $title)=mysql_fetch_row($sql_res);
		$name=StripSlashes($name); $descr=StripSlashes($descr); $content=StripSlashes($content); $title=StripSlashes($title);
	}
	$title1="Редактирование страницы (id: ".$_REQUEST['id'].")";
}
?>
<a name='art_add'></a><a name='art_edit'></a>
<form name="form_name" action="index.html" method="post">
<input type="hidden" name="action" value="art_save">
<input type="hidden" name="id" value="<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>">
<input type="hidden" name="parent_id" value="<?php if(isset($_REQUEST['parent_id'])) echo $_REQUEST['parent_id']; ?>">
<input type="hidden" name="code" value="<?php if(isset($_REQUEST['code'])) echo $_REQUEST['code']; ?>">
<table width="700" cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
<tr class="form_topline">
	<td colspan="2" align="left"><b><?php if(isset($title1)) echo $title1; ?>&nbsp;</b></td>
</tr>
<tr>
	<td class="form_left">Заголовок</td>
	<td class="form_main"><input class=form type="text" name="name" value='<?php if(isset($name)) echo $name; ?>'></td>
</tr>
<!--tr>
	<td class="form_left">Краткое описание статьи</td>
	<td class="form_main"><textarea class="form" name="descr" rows="3"><?php if(isset($descr)) echo $descr; ?></textarea></td>
</tr-->
<tr>
	<td class="form_left">Статья</td>
	<td class="form_main"><?php require_once("../vis/visual.php"); ?></td>
</tr>
<tr>
	<td class="form_left">Активность статьи</td>
	<td class="form_main"><input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="No") echo (""); else echo ("checked"); ?>></td>
</tr>
<tr>
	<td class="form_left">Виртуальный URL<br>(необязательное поле)</td>
	<td class="form_main"><input class="form" type="text" name="url" value='<?php if(isset($url)) echo $url; ?>'></td>
</tr>
<!--tr>
	<td class="form_left">Тег &lt;Title&gt;<br>(необязательное поле)</td>
	<td class="form_main"><input class="form" type="text" name="title" value='<?php if(isset($title)) echo $title; ?>'></td>
</tr-->
	<tr>
		<td>&nbsp;</td>
		<td><input class="form_button" type="submit" name="form_art_save" value="Сохранить"></td>
	</tr>
</table>
</form>