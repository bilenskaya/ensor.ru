<?
$title="Вставка файлов в страницу (id: ".$_REQUEST['id'].")";
?>
<a name='art_files'></a>
<form action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="art_files_save">
<input type="hidden" name="id" value="<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>">
<input type="hidden" name="parent_id" value="<?php if(isset($_REQUEST['parent_id'])) echo $_REQUEST['parent_id']; ?>">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if(isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<?  if (isset($_REQUEST['id']) && is_dir($path."files/pubs/files/".$_REQUEST['id'])) { ?>
	<tr>
		<td class="form_left">Файлы</td>
		<td class="form_main">
<?php
	echo "<table class=main cellspacing=2 cellpadding=2>";
	$dir = opendir($path."files/pubs/files/".$_REQUEST['id']);
	while($file = readdir($dir)) 
	{
		if ($file!="." && $file!="..") 
		{
			$ext=substr($file, strrpos($file, ".")+1);
			if (file_exists($path."img/filetypes/".$ext.".gif")) $type="<img src='/img/filetypes/".$ext.".gif' border='0'>"; else $type="<img src='/img/filetypes/empty.gif' width='16' height='16' border='0'>";
			echo "<tr class='common'>
					<td class='common' align='center'>".$type."</td>
					<td class='common' align='left'><a href='/files/pubs/files/".$_REQUEST['id']."/".$file."' title='Открыть в новом окне' target='_blank'>".$file."</a></td>
					<td class='common' align='center'><a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$_REQUEST['id']."&parent_id=".$_REQUEST['parent_id']."&fname=".$file."&action=art_file_del'\"><img src='/admin/img/del.gif' width='25' height='13' alt='Удалить изображение' border='0'></a></td>
				</tr>";
			$flag=1;
		}
	}
	closedir($dir);
	echo "</table>";
?>
		</td>
	</tr>
	<?}?>
	<tr>
		<td class="form_left" align="right" bgcolor="#DEDFDE">Загружаемый файл</td>
		<td class="form_main" align="left" bgcolor="#EFEFEF">
			<input class="form_file" type="file" name="file_name">
		</td>
	</tr>
	<tr>
		<td class="form_left" align="right" bgcolor="#DEDFDE">Новое имя файла</td>
		<td class="form_main" align="left" bgcolor="#EFEFEF">
			<input type="text" name="file_file_name" value="" style="width:400;">
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="form_button" type="submit" name="form_art_files_save" value="Загрузить"></td>
	</tr>
</table>
</form>
