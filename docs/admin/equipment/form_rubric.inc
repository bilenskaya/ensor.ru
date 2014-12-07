<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rubric_add")
{
	$imgpath="";
	$imgpath_www="";
	$enable="Yes";
	$title="Добавление рубрики";
	$parent_id=$_REQUEST['id'];
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="rubric_edit")
{
	$imgpath=$path."files/equipment/rubric/".$_REQUEST['id']."/";
	$imgpath_www="/files/equipment/rubric/".$_REQUEST['id']."/";
	$sql_query="SELECT id, parent_id, name, descr, enable  FROM ".$sql_pref."_catalog_rub WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $parent_id, $name, $descr, $enable)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); $descr=stripslashes($descr); 
	}

	if (isset($_REQUEST['id']) && file_exists($path."files/equipment/rubric/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
	elseif (isset($_REQUEST['id']) && file_exists($path."files/equipment/rubric/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
	if (isset($fname))
	{
		$size=getimagesize($path."files/equipment/rubric/imgs/".$fname);
		$size_th=getimagesize($path."files/equipment/rubric/thumbs/".$fname);
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
		<td class="form_left">Описание</td>
		<td class="form_main"><textarea class="form" name="descr" rows="4"><?php if (isset($descr)) echo $descr; ?></textarea></td>
	</tr>

	<tr>
		<td class="form_left">Родительская рубрика</td>
		<td class="form_main"><input class="form" type="text" name="parent_id" value='<?php if (isset($parent_id)) echo $parent_id; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Настройки</td>
		<td class="form_main">
			<input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>> - активность<br>
		</td>
	</tr>
	<tr>
		<td class="form_left">Изображение</td>
		<td class="form_main">
			<? if (isset($fname)) {?>
				<div>
                	<img src='/files/equipment/rubric/thumbs/<? echo $fname;?>' border=1>
                    <a href="javascript:if(confirm('Вы уверены?'))window.location='?id=<? echo $_REQUEST['id'];?>&letter=<? echo @$_REQUEST['letter'];?>&action=rubric_mainimg_del&fname=<? echo $fname;?>'"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>
                </div>
			<? } ?>
			<div><input class="form_file" type="file" name="file_name" size="65"></div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class="form_button" type="submit" name="button_submit" value="Сохранить"></td>
	</tr>
</table>
</form>