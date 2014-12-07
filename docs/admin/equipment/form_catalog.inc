<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="catalog_add")
{
	$imgpath="";
	$imgpath_www="";
	$enable="Yes";
	$title="Добавление каталога";
	$parent_id=serialize(array($_REQUEST['id']));
	$user_id=0;
	$rub_id=$_REQUEST['id'];
}



if (isset($_REQUEST['action']) && $_REQUEST['action']=="catalog_edit")
{
	$imgpath=$path."files/equipment/catalog/".$_REQUEST['id']."/";
	$imgpath_www="/files/equipment/catalog/".$_REQUEST['id']."/";
	$rub_id=$_REQUEST['rub_id'];
	$sql_query="SELECT id, parent_id, name, enable, date_upload, date_modify, date_issue, format, file_size, file_name, org_id, tags, user_id FROM ".$sql_pref."_catalog WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $parent_id, $name, $enable, $date_upload, $date_modify, $date_issue, $format, $file_size, $file_name, $org_id, $tags, $user_id)=mysql_fetch_row($sql_res);
		$name=stripslashes($name);
		$date_upload=date("d.m.y", $date_upload);
		$date_modify=date("d.m.y", $date_modify);
		$month_issue=date("m", $date_issue);
		$year_issue=date("Y", $date_issue);
	}






	if (isset($_REQUEST['id']) && file_exists($path."files/equipment/catalog/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
	elseif (isset($_REQUEST['id']) && file_exists($path."files/equipment/catalog/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
	if (isset($fname)) $size=getimagesize($path."files/equipment/catalog/imgs/".$fname);
	
	$title="Редактирование (id: ".$_REQUEST['id'].")";
}

	$sql_rubric_query="SELECT id, name FROM ".$sql_pref."_catalog_rub";
	$sql_rubric_res=mysql_query($sql_rubric_query, $conn_id);
	while (list($allrub_id, $allrub_name)=mysql_fetch_row($sql_rubric_res)) $allrubs[$allrub_id]=$allrub_name;
	$currentrubs=unserialize($parent_id);
	
	$sql_org_query="SELECT id, name FROM ".$sql_pref."_companies";
	$sql_org_res=mysql_query($sql_org_query, $conn_id);
	while (list($allorg_id, $allorg_name)=mysql_fetch_row($sql_org_res)) $allorgs[$allorg_id]=$allorg_name;
	asort($allorgs);
	$allorgs[0]="Не указано";


if (isset($_REQUEST['action']) && $_REQUEST['action']=="moderate_edit")
{
	$imgpath=$path."files/equipment/catalog/".$_REQUEST['id']."/";
	$imgpath_www="/files/equipment/catalog/".$_REQUEST['id']."/";
	$rub_id="moderate";
	$sql_query="SELECT id, parent_id, name, enable, date_upload, date_modify, date_issue, format, file_size, file_name, org_id, tags, user_id FROM ".$sql_pref."_catalog WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $parent_id, $name, $enable, $date_upload, $date_modify, $date_issue, $format, $file_size, $file_name, $org_id, $tags, $user_id)=mysql_fetch_row($sql_res);
		$name=stripslashes($name);
		$date_upload=date("d.m.y", $date_upload);
		$date_modify=date("d.m.y", $date_modify);
		$month_issue=date("m", $date_issue);
		$year_issue=date("Y", $date_issue);
	}






	if (isset($_REQUEST['id']) && file_exists($path."files/equipment/catalog/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
	elseif (isset($_REQUEST['id']) && file_exists($path."files/equipment/catalog/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
	if (isset($fname)) $size=getimagesize($path."files/equipment/catalog/imgs/".$fname);
	
	$title="Редактирование (id: ".$_REQUEST['id'].")";
}

	$sql_rubric_query="SELECT id, name FROM ".$sql_pref."_catalog_rub";
	$sql_rubric_res=mysql_query($sql_rubric_query, $conn_id);
	while (list($allrub_id, $allrub_name)=mysql_fetch_row($sql_rubric_res)) $allrubs[$allrub_id]=$allrub_name;
	$allrubs[0]="Не указано";
	$currentrubs=unserialize($parent_id);
	
	$sql_org_query="SELECT id, name FROM ".$sql_pref."_companies";
	$sql_org_res=mysql_query($sql_org_query, $conn_id);
	while (list($allorg_id, $allorg_name)=mysql_fetch_row($sql_org_res)) $allorgs[$allorg_id]=$allorg_name;
	asort($allorgs);
	$allorgs[0]="Не указано";


?>

<a name='catalog_add'></a><a name='catalog_edit'></a>
<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
<input type="hidden" name="action" value="catalog_save">
<input type="hidden" name="rub_id" value="<?echo $rub_id;?>">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>

	<tr>
		<td class="form_left">Рубрики каталога</td>
		<td class="form_main">
		<?php foreach($currentrubs as $kc=>$vc)
			{
			echo "<select name='parent_id[".$vc."]'>";
			if (count($currentrubs)>1) echo "<option value=\"0\">Удалить</option>";
			foreach($allrubs as $ka=>$va) 
				if($ka==$vc) echo "<option value=".$ka." selected>".$va."</option>";
				else echo "<option value=".$ka.">".$va."</option>";
			echo "</select>";
			
			}?></td>
	</tr>
	<tr>
		<td class="form_left">Добавить рубрику</td>
		<td class="form_main"><select name="add_parent_id"><option value="0" selected></option>
		<?php foreach($allrubs as $k=>$v) echo "<option value=".$k.">".$v."</option>";?></select></td>
	</tr>
	<tr>
		<td class="form_left">Изображение</td>
		<td class="form_main">
			<? if (isset($fname)) {?>
				<div>
                	<img src='/files/equipment/catalog/imgs/<? echo $fname;?>' border=1>
                    <a href="javascript:if(confirm('Вы уверены?'))window.location='?id=<? echo $_REQUEST['id'];?>&action=catalog_mainimg_del&fname=<? echo $fname;?>&rub_id=<? echo $rub_id;?>'"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>
                </div>
			<? } ?>
			<div><input class="form_file" type="file" name="img_name" size="65"></div>
		</td>
	</tr>
	<tr>
		<td class="form_left">Название</td>
		<td class="form_main"><input class="form" type="text" name="name" value='<?php if (isset($name)) echo $name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Дата выпуска</td>
		<td class="form_main">
		<select name="month_issue"><option value="0"></option>
		<?php foreach($months_rus2 as $k=>$v) 
			if($month_issue==$k) echo "<option value=".$k." selected>".$v."</option>";
			else echo "<option value=".$k.">".$v."</option>";?>
		</select>
		
		<select name="year_issue"><option value="0"></option>
		<?php for ($i=2000; $i<2021; $i++) 
			if ($year_issue==$i) echo "<option value=".$i." selected>".$i."</option>";
			else echo "<option value=".$i.">".$i."</option>";?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="form_left">Имя файла</td>
		<td class="form_main"><?php if (isset($file_name)) echo $file_name; ?>
		<div><input class="form_file" type="file" name="file_name" size="65"></div>
			</td>
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
		<td class="form_left">Организация</td>
		<td class="form_main"><select name="org_id">
		<?php foreach($allorgs as $k=>$v) 
			if($org_id==$k) echo "<option value=".$k." selected>".$v."</option>";
			else echo "<option value=".$k.">".$v."</option>";?></select></td>
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
		<td class="form_left">Изменен</td>
		<td class="form_main"><?php if (isset($date_modify)) echo $date_modify; ?></td>
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