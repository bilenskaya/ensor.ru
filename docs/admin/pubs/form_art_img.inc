<?
$title="Вставка изображения в страницу (id: ".$_REQUEST['id'].")";
?>
<script language="JavaScript">
function check_resize()
{
	if(document.upl.file_img_resize.checked==false)
	{
		document.upl.file_img_width.disabled=true;
		document.upl.file_img_height.disabled=true;
	}
	else
	{
		document.upl.file_img_width.disabled=false;
		document.upl.file_img_height.disabled=false;
	}
}
</script>
<a name='art_img'></a>
<form action="index.html" method="post" enctype="multipart/form-data" name="upl">
<input type="hidden" name="action" value="art_img_save">
<input type="hidden" name="id" value="<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>">
<input type="hidden" name="parent_id" value="<?php if(isset($_REQUEST['parent_id'])) echo $_REQUEST['parent_id']; ?>">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if(isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<?  if (isset($_REQUEST['id']) && is_dir($path."files/pubs/imgs/".$_REQUEST['id'])) { ?>
	<tr>
		<td class="form_left">Изображение</td>
		<td class="form_main">
			<?php 
				$dir = opendir($path."files/pubs/imgs/".$_REQUEST['id']);
				echo "<table cellpadding=0 cellspacing=0 border=0><tr>";
				$y=1;
				while($file = readdir($dir))
				{
					if ($file!="." && $file!="..") 
					{
						$size=getimagesize($path."files/pubs/imgs/".$_REQUEST['id']."/".$file);
						echo "<td valign=bottom><table><tr height=70><td align=center valign=bottom>";
						echo "<img src='/admin/fns/resize_img.php?image=".$path."files/pubs/imgs/".$_REQUEST['id']."/".$file."&mw=60&mh=60' hspace=0 vspace=5 alt='".$file."' border=1>";
						echo "</td></tr><tr><td align=center valign=middle style='font-size: 10px;'>";
						echo $file."<br>".$size[0]." x ".$size[1]."";
						echo "</td></tr><tr><td align=center valign=middle>";
						echo "<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$_REQUEST['id']."&parent_id=".$_REQUEST['parent_id']."&action=art_img_del&fname=".$file."'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить изображение' border=0></a>";
						echo "</td></tr></table></td>";
						if ($y==8)
						{
							echo "</tr></table><br><table cellpadding=0 cellspacing=0 border=0><tr>";
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
		<td class="form_left" bgcolor="#DEDFDE">Загружаемый файл (jpg)</td>
		<td class="form_main" align="left" bgcolor="#EFEFEF">
			<input class="form_file" type="file" name="file_name" size="65">
		</td>
	</tr>
	<tr>
		<td class="form_left">Новое имя файла</td>
		<td class="form_main">
			<input type="text" name="file_img_name" value="" style="width:400;">
		</td>
	</tr>
	<tr>
		<td class="form_left">Размеры изображения</td>
		<td class="form_main">
			<input type="checkbox" name="file_img_resize" value="Yes" onClick="check_resize();"> - уменьшить &nbsp;&nbsp;&nbsp; <input type="text" name="file_img_width" value="" size="3" disabled style="font-size:10px;text-align:center;"> х <input type="text" name="file_img_height" value="" size="3" disabled style="font-size:10px;text-align:center;"> - (ширина х высота)
			<script language="JavaScript">check_resize();</script>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="form_button" type="submit" name="form_art_img_save" value="Загрузить"></td>
	</tr>
</table>
</form>