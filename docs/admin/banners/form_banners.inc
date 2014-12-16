<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="banners_add")
{
	$imgpath="";
	$imgpath_www="";
	$enable="Yes";
	$xc2_dt=date("Y-m-d");
	$time=date("H:i");
	$title="Добавление";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="banners_edit")
{
	$imgpath=$path."files/banners/".$_REQUEST['id']."/";
	$imgpath_www="/files/banners/".$_REQUEST['id']."/";
    $sql_query="SELECT id, enable, url, descr, sort, zone, show_start FROM ".$sql_pref."_banners WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $enable, $url,  $descr, $sort, $zone, $show_start)=mysql_fetch_row($sql_res);
		$name=stripslashes($url);
        $descr=stripslashes($descr);
		$xc2_dt=substr($show_start,0,10);
		$time=substr($show_start,11,5);
	}
	
	if (isset($_REQUEST['id']) && file_exists($path."files/banners/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
	elseif (isset($_REQUEST['id']) && file_exists($path."files/banners/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
	if (isset($fname))
	{
		$size=getimagesize($path."files/banners/imgs/".$fname);
		$size_th=getimagesize($path."files/banners/thumbs/".$fname);
	}
	
	$title="Редактирование (id: ".$_REQUEST['id'].")";
}
?>

<link rel=stylesheet href="../xc2/css/xc2_default.css" type="text/css">
<script language="javascript" src="../xc2/config/xc2_default.js"></script>
<script language="javascript" src="../xc2/script/xc2_inpage.js"></script>
<script language="JavaScript">
function check_date()
{
	if (document.form_name.date.checked==false)
	{
		document.form_name.xc2_dt.disabled=true;
		document.form_name.time.disabled=true;
		if (document.form_name.xc2_dt.value=="0000-00-00")
		{
			var currentTime = new Date()
			var month = currentTime.getMonth() + 1
			var day = currentTime.getDate()
			var year = currentTime.getFullYear()
			if (month < 10) {month = "0" + month;}
			if (day < 10) {day = "0" + day;}
			document.form_name.xc2_dt.value=year + "-" + month + "-" + day;
		}
	}
	else 
	{
		document.form_name.xc2_dt.disabled=false;
		document.form_name.time.disabled=false;
	}
}
</script>

<a name='banners_add'></a><a name='banners_edit'></a>
<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
<input type="hidden" name="action" value="banners_save">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>

    <tr>
        <td class="form_left">Настройки</td>
        <td class="form_main">
            <input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>> - активность<br>
        </td>
    </tr>


	<tr>
		<td class="form_left">Дата начала</td>
		<td class="form_main">
			<table>
				<tr>
					<td><input name="date" type="checkbox" value="Yes" onclick="check_date()" <?php if (isset($show_start) && $show_start=="0000-00-00 00:00") echo ""; else echo "checked"; ?>></td>
					<td id="holder"><input type="text" name="xc2_dt" id="xc2_dt" maxlength=10 size=10 value="<?php echo $xc2_dt; ?>" onclick="showCalendar('',document.getElementById('xc2_dt'),null,'<?php echo $xc2_dt; ?>','holder',0,25,1);" style="width:90;cursor:pointer;"></td>
					<td><input type="text" name="time" id="time" maxlength=10 size=10 value="<?php echo @$time; ?>" style="width:40;"></td>
				</tr>
			</table>
			<script language="JavaScript">check_date();</script>
		</td>
	</tr>

    <tr>
        <td class="form_left">Зона размещения</td>
        <td class="form_main"><input class="form" style="width:70" type="text" name="zone" value='<?php if (isset($zone)) echo $zone; ?>'></td>
    </tr>

    <tr>
        <td class="form_left">Сортировка размещения</td>
        <td class="form_main"><input class="form" style="width:70" type="text" name="sort" value='<?php if (isset($sort)) echo $sort; ?>'></td>
    </tr>

	<tr>
		<td class="form_left">Описание</td>
		<td class="form_main"><textarea class="form" name="descr" rows="4"><?php if (isset($descr)) echo $descr; ?></textarea></td>
	</tr>

	<tr>
		<td class="form_left">URL</td>
		<td class="form_main"> 
			<input class="form" type="text" name="url" value='<?php if(isset($url)) echo $url; ?>'>
		</td>
	</tr>
	<tr>
		<td class="form_left">Изображение</td>
		<td class="form_main">
			<? if (isset($fname)) {?>
				<div>
                	<img src='/files/banners/thumbs/<? echo $fname;?>' border=1>
                    <a href="javascript:if(confirm('Вы уверены?'))window.location='?id=<? echo $_REQUEST['id'];?>&action=banners_mainimg_del&fname=<? echo $fname;?>'"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>
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