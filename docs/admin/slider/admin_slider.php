<?php

$big_pic_width=570;
$big_pic_height=250;
$small_pic_width=80;
$small_pic_height=50;
$slider_pic_path="img/slider";

function picture_trim()
{
global $sql_pref, $conn_id, $path, $big_pic_width, $big_pic_height, $slider_pic_path;
if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
if (!isset($_REQUEST['def']) || $_REQUEST['def']!="Yes") $def="No"; else $def="Yes";
echo "Изменяйте область выделения для задания границ большой картинки<BR>";
$flag=true;
if (is_uploaded_file( $_FILES['img_name']['tmp_name'])) {

	$mime=$_FILES['img_name']['type'];
	if ($mime=="image/jpeg" || $mime=="image/pjpeg" || $mime=="image/gif")
	{
		if ($mime=="image/jpeg" || $mime=="image/pjpeg") {$ext=".jpg";}
		elseif ($mime=="image/gif") {$ext=".gif";}

$sql_query="INSERT INTO ".$sql_pref."_slider_picture (enable, def, ext) VALUES ('".$enable."', '".$def."', '".$ext."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		$pic_name=mysql_insert_id();

		$dest=$path.$slider_pic_path."/".$pic_name."_orig".$ext;
		make_dir($path.$slider_pic_path);
		$src=$_FILES["img_name"]["tmp_name"];
		move_uploaded_file($src, $dest);
		$img="/".$slider_pic_path."/".$pic_name."_orig".$ext;
		$orig_size=getimagesize($dest);
	}
	else echo "Ошибка! Недопустимый формат файла.";
}

else $flag=false;

$ratio=$big_pic_width/$big_pic_height;
if ($orig_size[0]>=$big_pic_width and $orig_size[1]>=$big_pic_height) {
	if($orig_size[1]*$ratio<=$orig_size[0]) {$max_height=$orig_size[1]; $max_width=$max_height*$ratio;}
	if($orig_size[0]/$ratio<=$orig_size[1]){$max_width=$orig_size[0]; $max_height=$max_width/$ratio;}
}
else {echo "Рисунок меньше требуемых размеров $big_pic_width-$big_pic_height<BR>"; $flag=false;}

if ($flag)
echo "<script language=\"Javascript\">
jQuery(document).ready(function() {
	jQuery('#cropbox').Jcrop({
		setSelect: [ 0, 0, $max_width, $max_height ],
		aspectRatio: $ratio,
		minSize: [ $big_pic_width, $big_pic_height],
		addClass: 'custom',
		bgColor: 'yellow',
		bgOpacity: .8,
		sideHandles: false,
		onSelect: showCoords,
		onChange: showCoords
	});
});

function showCoords(c)
			{
				jQuery('#x1').val(c.x);
				jQuery('#y1').val(c.y);
				jQuery('#x2').val(c.x2);
				jQuery('#y2').val(c.y2);
				jQuery('#w').val(c.w);
				jQuery('#h').val(c.h);
			};
</script>";

echo "<img src=\"$img\" id=\"cropbox\" />";

if($flag)
echo "<form name=\"form_name\" action=\"index.html\" method=\"post\" enctype=\"multipart/form-data\">
			<input type=\"hidden\" name=\"action\" value=\"make_small\">
			<input type=\"hidden\" name=\"pic_name\" value=\"$pic_name\">
			<input type=\"hidden\" name=\"ext\" value=\"$ext\">
			<label>X1 <input type=\"text\" size=\"4\" id=\"x1\" name=\"x1\" /></label>
			<label>Y1 <input type=\"text\" size=\"4\" id=\"y1\" name=\"y1\" /></label>
			<label>X2 <input type=\"text\" size=\"4\" id=\"x2\" name=\"x2\" /></label>
			<label>Y2 <input type=\"text\" size=\"4\" id=\"y2\" name=\"y2\" /></label>
			<label>W <input type=\"text\" size=\"4\" id=\"w\" name=\"w\" /></label>
			<label>H <input type=\"text\" size=\"4\" id=\"h\" name=\"h\" /></label>
			<input type=\"submit\" name=\"button_submit\" value=\"Далее\">
		</form>";

}

function make_small()
{
global $path, $small_pic_width, $small_pic_height, $big_pic_width, $big_pic_height, $slider_pic_path;
if (isset($_REQUEST['x1'])) $x1=$_REQUEST['x1']; else $x1=0;
if (isset($_REQUEST['y1'])) $y1=$_REQUEST['y1']; else $y1=0;
if (isset($_REQUEST['x2'])) $x2=$_REQUEST['x2']; else $x2=0;
if (isset($_REQUEST['y2'])) $y2=$_REQUEST['y2']; else $y2=0;
if (isset($_REQUEST['pic_name'])) $pic_name=$_REQUEST['pic_name']; 
if (isset($_REQUEST['ext'])) $ext=$_REQUEST['ext']; 
echo "Изменяйте область выделения для задания границ маленькой картинки<BR>";
$flag=true;
$src=$path.$slider_pic_path."/".$pic_name."_orig".$ext;

if (file_exists($src)) {

$dest=$path.$slider_pic_path."/".$pic_name."_big".$ext;

$big_w=$x2-$x1;
$big_h=$y2-$y1;

if ($ext==".jpeg" || $ext==".jpg")
				{
					$src_i=imageCreateFromJpeg($src);
					$dst_i=imageCreateTrueColor($big_pic_width,$big_pic_height);
					imageCopyResampled($dst_i,$src_i,0,0,$x1,$y1,$big_pic_width,$big_pic_height,$big_w,$big_h);
					imageJpeg($dst_i,$dest,100);
				}
				elseif ($ext==".gif")
				{
					$src_i=imageCreateFromGif($src);
					$dst_i=imageCreateTrueColor($big_pic_width,$big_pic_height);
					imageCopyResampled($dst_i,$src_i,0,0,$x1,$y1,$big_pic_width,$big_pic_height,$big_w,$big_h);
					imageGif($dst_i,$dest,100);
				}
		$img="/".$slider_pic_path."/".$pic_name."_big".$ext;
		$orig_size=getimagesize($dest);
	}


else $flag=false;

$ratio=$small_pic_width/$small_pic_height;
if ($orig_size[0]>=$small_pic_width and $orig_size[1]>=$small_pic_height) {
	if($orig_size[1]*$ratio<=$orig_size[0]) {$max_height=$orig_size[1]; $max_width=$max_height*$ratio;}
	if($orig_size[0]/$ratio<=$orig_size[1]){$max_width=$orig_size[0]; $max_height=$max_width/$ratio;}
}
else {echo "Рисунок меньше требуемых размеров $big_pic_width-$big_pic_height<BR>"; $flag=false;}

if ($flag)
echo "<script language=\"Javascript\">
jQuery(document).ready(function() {
	jQuery('#cropbox').Jcrop({
		setSelect: [ 0, 0, $max_width, $max_height ],
		aspectRatio: $ratio,
		minSize: [ $small_pic_width, $small_pic_height],
		addClass: 'custom',
		bgColor: 'yellow',
		bgOpacity: .8,
		sideHandles: false,
		onSelect: showCoords,
		onChange: showCoords
	});
});

function showCoords(c)
			{
				jQuery('#x1').val(c.x);
				jQuery('#y1').val(c.y);
				jQuery('#x2').val(c.x2);
				jQuery('#y2').val(c.y2);
				jQuery('#w').val(c.w);
				jQuery('#h').val(c.h);
			};
</script>";

echo "<img src=\"$img\" id=\"cropbox\" />";

if($flag)
echo "<form name=\"form_name\" action=\"index.html\" method=\"post\" enctype=\"multipart/form-data\">
			<input type=\"hidden\" name=\"action\" value=\"save_small\">
			<input type=\"hidden\" name=\"pic_name\" value=\"$pic_name\">
			<input type=\"hidden\" name=\"ext\" value=\"$ext\">
			<label>X1 <input type=\"text\" size=\"4\" id=\"x1\" name=\"x1\" /></label>
			<label>Y1 <input type=\"text\" size=\"4\" id=\"y1\" name=\"y1\" /></label>
			<label>X2 <input type=\"text\" size=\"4\" id=\"x2\" name=\"x2\" /></label>
			<label>Y2 <input type=\"text\" size=\"4\" id=\"y2\" name=\"y2\" /></label>
			<label>W <input type=\"text\" size=\"4\" id=\"w\" name=\"w\" /></label>
			<label>H <input type=\"text\" size=\"4\" id=\"h\" name=\"h\" /></label>
			<input type=\"submit\" name=\"button_submit\" value=\"Далее\">
		</form>";

}

function save_small()
{
global $path, $small_pic_width, $small_pic_height, $slider_pic_path;
if (isset($_REQUEST['x1'])) $x1=$_REQUEST['x1']; else $x1=0;
if (isset($_REQUEST['y1'])) $y1=$_REQUEST['y1']; else $y1=0;
if (isset($_REQUEST['x2'])) $x2=$_REQUEST['x2']; else $x2=0;
if (isset($_REQUEST['y2'])) $y2=$_REQUEST['y2']; else $y2=0;
if (isset($_REQUEST['pic_name'])) $pic_name=$_REQUEST['pic_name']; 
if (isset($_REQUEST['ext'])) $ext=$_REQUEST['ext']; 

$src=$path.$slider_pic_path."/".$pic_name."_big".$ext;

if (file_exists($src)) {

$dest=$path.$slider_pic_path."/".$pic_name."_small".$ext;

$small_w=$x2-$x1;
$small_h=$y2-$y1;

if ($ext==".jpeg" || $ext==".jpg")
				{
					$src_i=imageCreateFromJpeg($src);
					$dst_i=imageCreateTrueColor($small_pic_width,$small_pic_height);
					imageCopyResampled($dst_i,$src_i,0,0,$x1,$y1,$small_pic_width,$small_pic_height,$small_w,$small_h);
					imageJpeg($dst_i,$dest,100);
				}
				elseif ($ext==".gif")
				{
					$src_i=imageCreateFromGif($src);
					$dst_i=imageCreateTrueColor($small_pic_width,$small_pic_height);
					imageCopyResampled($dst_i,$src_i,0,0,$x1,$y1,$small_pic_width,$small_pic_height,$small_w,$small_h);
					imageGif($dst_i,$dest,100);
				}
	}

}

function admin_show_catalog()
{
	global $sql_pref, $conn_id, $path, $slider_pic_path;

	$sql_query="SELECT id, enable, def, ext FROM ".$sql_pref."_slider_picture ORDER by id desc";
	echo "<a href='?action=add_pic'>Добавить картинку</a>";
	echo "<table class='main' cellspacing='2' cellpadding='2' width='100%'>
    			<tr class='maintitle'>
    				<td width='20' class='maintitle' align='center'><b>id</b></td>
					<td class='maintitle' align='left'></td>
					<td class='maintitle' align='left'><b>Оригинал</b></td>
					<td class='maintitle' align='left'><b>Большая</b></td>
					<td class='maintitle' align='left'><b>Маленькая</b></td>
    				<td width='30' class='maintitle' align='center'><b>del</b></td>
    			</tr>";
	if ($sql_res=mysql_query($sql_query, $conn_id) and mysql_num_rows($sql_res)>0)
	{
		while(list($id, $enable, $def, $ext)=mysql_fetch_row($sql_res))
		{

			if ($enable=='Yes') $enable_pic="<a href='?id=".$id."&action=picture_enable'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='Отображение' border=0></a>"; 
			else $enable_pic="<a href='?id=".$id."&action=picture_enable'><img src='/admin/img/check_no.gif' width=25 height=13 alt='Отображение' border=0></a>";
			
			if ($def=='Yes') $def_pic="<a href='?id=".$id."&action=picture_def'><img src='/admin/img/check_yes.gif' width=25 height=13 alt='По умолчанию' border=0></a>"; 
			else $def_pic="<a href='?id=".$id."&action=picture_def'><img src='/admin/img/check_no.gif' width=25 height=13 alt='По умолчанию' border=0></a>";
			
			$del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&action=picture_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
	$file_ok=true;
	if (file_exists($path.$slider_pic_path."/".$id."_orig".$ext)) 
	$orig_img="<a href='/".$slider_pic_path."/".$id."_orig".$ext."'>".$id."_orig".$ext."</a>";
	else $orig_img="<img src='/files/picture/not_found_pic.png'>";
			
	if (file_exists($path.$slider_pic_path."/".$id."_big".$ext)) 
	$big_img="<img src='/".$slider_pic_path."/".$id."_big".$ext."' border='0'>";
	else {$big_img="<img src='/files/picture/not_found_pic.png'>"; $file_ok=false;}
	
	if (file_exists($path.$slider_pic_path."/".$id."_small".$ext)) 
	$small_img="<img src='/".$slider_pic_path."/".$id."_small".$ext."' border='0'>";
	else {$small_img="<img src='/files/picture/not_found_pic.png'>"; $file_ok=false;}
		
					if ($file_ok) 
					echo "<tr class='common'>";
					else 
					echo "<tr class='common' bgcolor='red'>";
					echo "
					<td class='common' align='center'><font color='#A0A0A0'>".$id."</font></td>
					<td class='common' align='center'>".$enable_pic.$def_pic."</td>
					<td class='common' align='left'>".$orig_img."</td>
					<td class='common' align='left'>".$big_img."</td>
					<td class='common' align='left'>".$small_img."</td>
					<td class='common' align='center'>".$del."</td>
					</tr>";
		}
	}
	echo "</table>";
}

function del_picture($id)
{
	global $path, $conn_id, $sql_pref, $slider_pic_path;
	$sql_query="SELECT id, ext FROM ".$sql_pref."_slider_picture WHERE id='$id'";
	$sql_res=mysql_query($sql_query, $conn_id);
	list($id, $ext)=mysql_fetch_row($sql_res);
	$orig=$path.$slider_pic_path."/".$id."_orig".$ext;
	$big=$path.$slider_pic_path."/".$id."_big".$ext;
	$small=$path.$slider_pic_path."/".$id."_small".$ext;
	del_file($orig); 
	del_file($big);
	del_file($small);
	del_record('slider_picture', $id, 'No', -1);
}
?>
