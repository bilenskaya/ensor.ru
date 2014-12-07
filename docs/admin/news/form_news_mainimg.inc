<?php
	$title="Вставка изображения в новость (id: ".$_REQUEST['id'].")";
		
	if (isset($_REQUEST['id']) && file_exists($path."files/news/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
	elseif (isset($_REQUEST['id']) && file_exists($path."files/news/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
	if (isset($fname))
	{
		$size=getimagesize($path."files/news/imgs/".$fname);
		$size_th=getimagesize($path."files/news/thumbs/".$fname);
	}
?>
<a name='news_mainimg'></a>
<form action="index.html" method="post" enctype="multipart/form-data" name="upl">
<input type="hidden" name="id" value="<?php if (isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>">
<input type="hidden" name="action" value="news_mainimg_save">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr bgcolor="#CFCFFF">
		<td colspan="2" align="left"><b><?php if (isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<? if (isset($fname)) {?>
	<tr>
		<td class="form_left" align="right" bgcolor="#DEDFDE">Изображение</td>
		<td class="form_main" align="left" bgcolor="#EFEFEF">
					<table cellpadding=20 cellspacing=0 border=0>
						<tr>
							<td align=center valign=bottom><img src='/files/news/imgs/<?echo $fname;?>' border=1><br>основное<br><?echo $size[0]."x".$size[1];?></td>
							<td align=center valign=bottom><img src='/files/news/thumbs/<?echo $fname;?>' border=1><br>превью<br><?echo $size_th[0]."x".$size_th[1];?></td>
						</tr>
					</table>
			</td>
	</tr>
	<tr>
		<td class="form_left" align="right" bgcolor="#DEDFDE">Удаление</td>
		<td class="form_main" align="left" bgcolor="#EFEFEF"><a href="javascript:if(confirm('Вы уверены?'))window.location='?id=<?echo $_REQUEST['id'];?>&action=news_mainimg_del&fname=<?echo $fname;?>'"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a></td>
	</tr>
	<? } ?>
	<tr>
		<td class="form_left" align="right" bgcolor="#DEDFDE">Загрузка</td>
		<td class="form_main" align="left" bgcolor="#EFEFEF">
			<input class="form_file" type="file" name="file_name" size="65">
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input class="form_button" type="submit" name="button_submit" value="Загрузить">
		</td>
	</tr>
</table>
</form>