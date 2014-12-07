<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="news_add")
{
	$imgpath="";
	$imgpath_www="";
	$xc2_dt=date("Y-m-d");
	$enable="Yes";
	$main="Yes";
	$title="Добавление новости";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="news_edit")
{
	$imgpath=$path."files/news/".$_REQUEST['id']."/";
	$imgpath_www="/files/news/".$_REQUEST['id']."/";
	$sql_query="SELECT id, enable, main, dt, name, descr, content, tags, source, source_link FROM ".$sql_pref."_news WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $enable, $main, $dt, $name, $descr, $content, $tags, $source, $source_link)=mysql_fetch_row($sql_res);
		$name=stripslashes($name);
        $source=stripslashes($source);
        $source_link=stripslashes($source_link);
		$descr=stripslashes($descr);
		$content=stripslashes($content);
		$xc2_dt=$dt;
//		$cur_day=substr($dt,8,2); $cur_month=substr($dt,5,2); $cur_year=substr($dt,0,4);

		if (isset($_REQUEST['id']) && file_exists($path."files/news/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
		elseif (isset($_REQUEST['id']) && file_exists($path."files/news/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
		if (isset($fname))
		{
			$size=getimagesize($path."files/news/imgs/".$fname);
			$size_th=getimagesize($path."files/news/thumbs/".$fname);
		}
	}
	$title="Редактирование новости (id: ".$_REQUEST['id'].")";
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


<a name='news_add'></a><a name='news_edit'></a>
<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
<input type="hidden" name="action" value="news_save">
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
		<td class="form_left">Анонс</td>
		<td class="form_main"><textarea class="form" name="descr" rows="4"><?php if (isset($descr)) echo $descr; ?></textarea></td>
	</tr>
	<tr>
		<td class="form_left">Новость</td>
		<td class="form_main">
			<?php require_once("../vis/visual.php"); ?>
		</td>
	</tr>
	<tr>
		<td class="form_left">Источник</td>
		<td class="form_main">
            <input class="form" type="text" id="source_main"  name="source_main" value='<?php if (isset($source)) echo $source; ?>'>
           	<input class="form" type="text" id="source_main_link"  name="source_main_link" value='<?php if (isset($source_link)) echo $source_link; ?>'>
               <select onchange="getElementById('source_main').value=this.value;" id="source" name="source">
                <option selected>Отсутствует</option>
                <?
            	$sql_query="SELECT distinct source FROM ".$sql_pref."_news WHERE source!='' AND enable='Yes' ORDER BY source";
            	$sql_res=mysql_query($sql_query, $conn_id);
            	if (mysql_num_rows($sql_res)>0)
            	{
            		while(list($c_source)=mysql_fetch_row($sql_res))
            		{
            			$source=stripslashes($source);
                        if (@$source==$c_source) $sel="selected"; else $sel="";
            			echo "<option ".$sel.">".stripslashes($c_source)."</option>";
            		}
            	}
                ?>
        	</select>
        </td>
	</tr>
	<tr>
		<td class="form_left">Настройки</td>
		<td class="form_main">
			<input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>> - активность<br>
            <input type="checkbox" name="main" value="Yes" <?php if (isset($main) && $main=="Yes") echo ("checked"); ?>> - на главную
		</td>
	</tr>
	<tr>
		<td class="form_left">Теги</td>
		<td class="form_main"><input class="form" type="text" name="tags" value='<?php if (isset($tags)) echo $tags; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Изображение</td>
		<td class="form_main">
			<? if (isset($fname)) {?>
				<div>
                	<img src='/files/news/thumbs/<? echo $fname;?>' border=1>
                    <a href="javascript:if(confirm('Вы уверены?'))window.location='?id=<? echo $_REQUEST['id'];?>&action=news_mainimg_del&fname=<? echo $fname;?>'"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>
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