<?



if (isset($_REQUEST['action']) && $_REQUEST['action']=="picture_edit")
{
	$rub_id=$_REQUEST['rub_id'];
	$sql_query="SELECT id, parent_id, descr, enable, date_upload, format, file_size, file_name, tags, user_id FROM ".$sql_pref."_picture WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $parent_id, $descr, $enable, $date_upload, $format, $file_size, $file_name, $tags, $user_id)=mysql_fetch_row($sql_res);
		$descr=stripslashes($descr);
		$date_upload=date("d.m.y", $date_upload);
		if ($file_name!=="" and file_exists($path."files/picture/tn_".$file_name)) 
	$img="<a href='/files/picture/".$file_name."'><img src='/files/picture/tn_".$file_name."' border='0'></a>";
	else $img="<img src='/files/picture/not_found_pic.png'>";
	}

	$title="Редактирование (id: ".$_REQUEST['id'].")";
}

	$sql_rubric_query="SELECT id, name FROM ".$sql_pref."_picture_rub";
	$sql_rubric_res=mysql_query($sql_rubric_query, $conn_id);
	while (list($allrub_id, $allrub_name)=mysql_fetch_row($sql_rubric_res)) $allrubs[$allrub_id]=$allrub_name;
	$currentrubs=$parent_id;



if (isset($_REQUEST['action']) && $_REQUEST['action']=="moderate_edit")
{
	$rub_id="moderate";
	$sql_query="SELECT id, parent_id, descr, enable, date_upload, format, file_size, file_name, tags, user_id FROM ".$sql_pref."_picture WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $parent_id, $descr, $enable, $date_upload, $format, $file_size, $file_name, $tags, $user_id)=mysql_fetch_row($sql_res);
		$descr=stripslashes($descr);
		$date_upload=date("d.m.y", $date_upload);
		if ($file_name!=="" and file_exists($path."files/picture/tn_".$file_name)) 
		$img="<a href='/files/picture/".$file_name."'><img src='/files/picture/tn_".$file_name."' border='0'></a>";
		else $img="<img src='/files/picture/not_found_pic.png'>";
	}
	$title="Редактирование (id: ".$_REQUEST['id'].")";
}

?>

<a name='picture_edit'></a>
<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
<input type="hidden" name="action" value="picture_save">
<input type="hidden" name="rub_id" value="<?echo $rub_id;?>">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>

	<tr>
		<td class="form_left">Рубрика каталога</td>
		<td class="form_main">
		<?php 
			echo "<select name='parent_id'>";
			echo "<option value=\"0\">Не указано</option>";
			foreach($allrubs as $ka=>$va) 
				if($ka==$parent_id) echo "<option value=".$ka." selected>".$va."</option>";
				else echo "<option value=".$ka.">".$va."</option>";
			echo "</select>";
			?></td>
	</tr>

	<tr>
		<td class="form_left">Изображение</td>
		<td class="form_main">
			<? if (isset($img)) echo $img ?>
		</td>
	</tr>
	<tr>
		<td class="form_left">Описание</td>
		<td class="form_main"><input class="form" type="text" name="descr" value='<?php if (isset($descr)) echo $descr; ?>'></td>
	</tr>

	<tr>
		<td class="form_left">Имя файла</td>
		<td class="form_main"><?php if (isset($file_name)) echo $file_name; ?></td>
	</tr>
	<tr>
		<td class="form_left">Размер, Мб</td>
		<td class="form_main"><?php if (isset($file_size)) echo $file_size; ?></td>
	</tr>
	<tr>
		<td class="form_left">Формат</td>
		<td class="form_main"><?php if (isset($format)) echo $format; ?></td>
	</tr>
	<tr>
		<td class="form_left">Добавил</td>
		<td class="form_main"><input class="form" type="text" name="user_id" value='<?php if (isset($user_id)) echo $user_id; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Добавлен</td>
		<td class="form_main"><?php if (isset($date_upload)) echo $date_upload; ?></td>
	</tr>
	<tr>
		<td class="form_left">Тэги</td>
		<td class="form_main"><input class="form" type="text" name="tags" value='<?php if (isset($tags)) echo $tags; ?>'></td>
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