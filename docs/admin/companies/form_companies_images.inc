<?
	$title="Вставка изображений (id: ".$_REQUEST['id'].")";
?>
<script language="JavaScript">
function check_resize()
{
	if(document.all.upl.file_img_resize.checked==false)
	{
		document.all.upl.file_img_width.disabled=true;
		document.all.upl.file_img_height.disabled=true;
	}
	else
	{
		document.all.upl.file_img_width.disabled=false;
		document.all.upl.file_img_height.disabled=false;
	}
}
</script>
<a name='companies_images'></a>
<form action="index.html" method="post" enctype="multipart/form-data" name="upl">
<input type="hidden" name="id" value="<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>">
<input type="hidden" name="letter" value="<?php if(isset($_REQUEST['letter'])) echo $_REQUEST['letter']; ?>">
<input type="hidden" name="action" value="companies_images_save">
<table width="700" cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if(isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<?  if (isset($_REQUEST['id']) && is_dir($path."files/companies/".$_REQUEST['id'])) { ?>
	<tr>
		<td class="form_left">Дополнительные изображения</td>
		<td class="form_main">
				<?
				$dir = opendir($path."files/companies/".$_REQUEST['id']);
				echo "<table cellpadding='0' cellspacing='0' border='0'><tr>";
				$y=1;
				while($file = readdir($dir)) 
				{
					if ($file!="." && $file!="..") 
					{
						$size=getimagesize($path."files/companies/".$_REQUEST['id']."/".$file);
						echo "<td valign=bottom><table><tr height=70><td align=center valign=bottom>";
						echo "<img src='/admin/fns/resize_img.php?image=".$path."files/companies/".$_REQUEST['id']."/".$file."&mw=60&mh=60' hspace='0' vspace='5' alt='".$file."' border='1'>";
						echo "</td></tr><tr><td align=center valign=middle style='font-size: 10px;'>";
						echo $file."<br>".$size[0]." x ".$size[1]."";
						echo "</td></tr><tr><td align=center valign=middle>";
						echo "<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$_REQUEST['id']."&letter=".@$_REQUEST['letter']."&action=companies_images_del&fname=".$file."'\"><img src='/admin/img/del.gif' width='25' height='13' alt='Удалить дополнительное изображение' border='0'></a>";
						echo "</td></tr></table></td>";
						if ($y==8)
						{
							echo "</tr></table><br><table cellpadding='0' cellspacing='0' border='0'><tr>";
							$y=0;
						}
						$y++;
					}
				}
				echo "</tr></table>";
				closedir($dir);
			?>
		</td>
	</tr>
	<?}?>
	<tr>
		<td class="form_left" align="right" bgcolor="#DEDFDE">Загрузка</td>
		<td class="form_main" align="left" bgcolor="#EFEFEF">
			<input class="form_file" type="file" name="file_name" size="65">
		</td>
	</tr>
	<tr>
		<td class="form_left">Новое имя файла <br>(латинницей)</td>
		<td class="form_main">
			<input type="text" name="file_img_name" value="" size="24">
		</td>
	</tr>
	<tr>
		<td class="form_left">Размеры изображения</td>
		<td class="form_main">
			Уменьшать изображение <input type="checkbox" name="file_img_resize" value="Yes" onClick="check_resize();">&nbsp;&nbsp;&nbsp;
			Ширина <input type="text" name="file_img_width" value="" size="5" disabled>&nbsp;&nbsp;&nbsp;
			Высота <input type="text" name="file_img_height" value="" size="5" disabled>
			<script language="JavaScript">check_resize();</script>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="form_button" type="submit" name="button_submit" value="Загрузить"></td>
	</tr>
</table>
</form>