<?php
	$title="Перенос товара (id: ".$_REQUEST['id'].")";
?>

<form name="form_name" action="index.html" method="post">
<input type="hidden" name="action" value="art_move_save">
<input type="hidden" name="id" value="<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>">
<input type="hidden" name="parent_id" value="<?php if(isset($_REQUEST['parent_id'])) echo $_REQUEST['parent_id']; ?>">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if(isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">Перенос в рубрику</td>
		<td class="form_main">
		<select name="new_rub">
		<?php
		$sql_query="SELECT id, name FROM ".$sql_pref."_links_rubs WHERE level='1' ORDER BY code";
		$sql_res=mysql_query($sql_query, $conn_id);
		while (list($id, $name)=mysql_fetch_row($sql_res))
		{
			echo "<option value='".$id."'>".$name."</option>\n";
			rubsub($id);
		}
	
		function rubsub($parent_id)
		{
			global $sql_pref, $conn_id;
			$sql_query="SELECT id, name, level FROM ".$sql_pref."_links_rubs WHERE parent_id='".$parent_id."' ORDER BY code";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			while (list($id1, $name1, $level1)=mysql_fetch_row($sql_res_1))
			{
				echo "<option value='".$id1."'>";
				for ($i=1; $i<=$level1; $i++) echo "&nbsp;&nbsp;";
				echo $name1."</option>\n";
				rubsub($id1);
			}
		}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="form_button" type="submit" name="form_art_move" value="Сохранить"></td>
	</tr>
</table>
</form>