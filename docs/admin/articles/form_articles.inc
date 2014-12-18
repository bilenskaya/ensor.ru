<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="articles_add")
{
	$imgpath="";
	$imgpath_www="";
	$enable="Yes";
	$xc2_dt=date("Y-m-d");
	$time=date("H:i");
	$title="Добавление";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="articles_edit")
{
	$imgpath=$path."files/articles/".$_REQUEST['id']."/";
	$imgpath_www="/files/articles/".$_REQUEST['id']."/";
	$sql_query="SELECT id, enable, dt, url, name, descr, content, category, tags FROM ".$sql_pref."_articles WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $enable, $dt, $url, $name, $descr, $content, $category, $tags)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); $descr=stripslashes($descr); $content=stripslashes($content); $category=stripslashes($category); $tags=stripslashes($tags);
		$xc2_dt=substr($dt,0,10);
		$time=substr($dt,11,5);
	}
	
	if (isset($_REQUEST['id']) && file_exists($path."files/articles/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
	elseif (isset($_REQUEST['id']) && file_exists($path."files/articles/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
	if (isset($fname))
	{
		$size=getimagesize($path."files/articles/imgs/".$fname);
		$size_th=getimagesize($path."files/articles/thumbs/".$fname);
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

<a name='articles_add'></a><a name='articles_edit'></a>
<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
<input type="hidden" name="action" value="articles_save">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">Название</td>
		<td class="form_main"><input class="form" type="text" name="name" value='<?php if (isset($name)) echo $name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Дата</td>
		<td class="form_main">
			<table>
				<tr>
					<td><input name="date" type="checkbox" value="Yes" onclick="check_date()" <?php if (isset($dt) && $dt=="0000-00-00 00:00") echo ""; else echo "checked"; ?>></td>
					<td id="holder"><input type="text" name="xc2_dt" id="xc2_dt" maxlength=10 size=10 value="<?php echo $xc2_dt; ?>" onclick="showCalendar('',document.getElementById('xc2_dt'),null,'<?php echo $xc2_dt; ?>','holder',0,25,1);" style="width:70;cursor:pointer;"></td>
					<td><input type="text" name="time" id="time" maxlength=10 size=10 value="<?php echo @$time; ?>" style="width:40;"></td>
				</tr>
			</table>
			<script language="JavaScript">check_date();</script>
		</td>
	</tr>
	<tr>
		<td class="form_left">Анонс</td>
		<td class="form_main"><textarea class="form" name="descr" rows="4"><?php if (isset($descr)) echo $descr; ?></textarea></td>
	</tr>
	<tr>
		<td class="form_left">Статья</td>
		<td class="form_main">
			<?php require_once("../vis/visual.php"); ?>
		</td>
	</tr>
	<tr>
		<td class="form_left">Теги</td>
		<td class="form_main"><input class="form" type="text" name="tags" value='<?php if (isset($tags)) echo $tags; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Категория</td>
		<td class="form_main">
			<?
				$sql_query="SELECT category FROM ".$sql_pref."_articles GROUP BY category ORDER BY category";
				$sql_res=mysql_query($sql_query, $conn_id);
				if(mysql_num_rows($sql_res)>0)
				{
					while (list($category_list)=mysql_fetch_row($sql_res))
					{
						$category_list=stripslashes($category_list);
						if ($category_list==@$category) $sel="checked"; else  $sel=""; 
						if (!empty($category_list)) echo "<div><input type=radio name='category' value='".$category_list."' ".$sel."> ".$category_list."</div>";
					}
				}
			?>
			<div><input type=radio name='category' id='category_other' value=''> <input type=text name='category_new' value='' onclick='document.getElementById("category_other").checked=true;' style='font-size:11px;width:150;'></div>
		</td>
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
			<input class="form" type="text" name="url" value='<?php if(isset($url)) echo $url; ?>'>
		</td>
	</tr>
	<tr>
		<td class="form_left">Изображение</td>
		<td class="form_main">
			<? if (isset($fname)) {?>
				<div>
                	<img src='/files/articles/thumbs/<? echo $fname;?>' border=1>
                    <a href="javascript:if(confirm('Вы уверены?'))window.location='?id=<? echo $_REQUEST['id'];?>&action=articles_mainimg_del&fname=<? echo $fname;?>'"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>
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