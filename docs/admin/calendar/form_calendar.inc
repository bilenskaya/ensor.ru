<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="calendar_add")
{
	$imgpath="";
	$imgpath_www="";
	$xc2_dt=date("Y-m-d");
	$enable="Yes";
	$main="Yes";
	$title="Добавление события";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="calendar_edit")
{
	$sql_query="SELECT id, enable, datum, title, content, visibility, actuals FROM ".$sql_pref."_events WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $enable, $dt, $name, $content, $visibility, $actuals)=mysql_fetch_row($sql_res);
		$name=stripslashes($name);
        $source=stripslashes($source);
		$descr=stripslashes($descr);
		$content=stripslashes($content);
		$xc2_dt=$dt;
        $vis_all=""; $vis_user="";
	    if (@$visibility=="all") $vis_all=" checked"; else $vis_user=" checked";
	
//		$cur_day=substr($dt,8,2); $cur_month=substr($dt,5,2); $cur_year=substr($dt,0,4);

		if (isset($_REQUEST['id']) && file_exists($path."files/news/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
		elseif (isset($_REQUEST['id']) && file_exists($path."files/news/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
		if (isset($fname))
		{
			$size=getimagesize($path."files/news/imgs/".$fname);
			$size_th=getimagesize($path."files/news/thumbs/".$fname);
		}
	}
	$title="Редактирование события (id: ".$_REQUEST['id'].")";
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
	else document.form_name.xc2_dt.disabled=false;
}
</script>


<a name='calendar_add'></a><a name='calendar_edit'></a>
<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
<input type="hidden" name="action" value="calendar_save">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">Заголовок</td>
		<td class="form_main"><input class="form" type="text" name="name" value='<?php if (isset($name)) echo $name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Дата</td>
		<td class="form_main">
			<table>
				<tr>
					<td><input name="date" type="checkbox" value="Yes" onclick="check_date()" <?php if (isset($dt) && $dt=="0000-00-00") echo ""; else echo "checked"; ?>></td>
					<td id="holder"><input type="text" name="xc2_dt" id="xc2_dt" maxlength=10 size=10 value="<?php echo $xc2_dt; ?>" onclick="showCalendar('',document.getElementById('xc2_dt'),null,'<?php echo $xc2_dt; ?>','holder',0,25,1);" style="width:70;cursor:pointer;"></td>
				</tr>
			</table>
			<script language="JavaScript">check_date();</script>
		</td>
	</tr>
    
    <tr>
        <td class="form_left">Видимость события:</td>
        <td class="form_main"><input type="radio" name="visibility" value="user" <?php if (isset($vis_user)) echo $vis_user; ?> > только мне &nbsp;&nbsp; <input type="radio" name="visibility" value="all" <?php if (isset($vis_all)) echo $vis_all; ?> > всем
        </td> 
    </tr>
        <tr>
        <td class="form_left">Событие актуально:</td>
        <td class="form_main"><input name="actuals" value=<?php if (isset($actuals)) echo $actuals; ?> >
        </td> 
    </tr>
	<tr>
		<td class="form_left">Событие</td>
		<td class="form_main">
			<?php require_once("../vis/visual.php"); ?>
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