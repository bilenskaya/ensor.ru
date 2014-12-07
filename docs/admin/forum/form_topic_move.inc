<?
$title="Перемещение страницы в другую рубрику (id: ".$_REQUEST['id'].")";
?>
<form name="form_name" action="index.html" method="post">
<input type="hidden" name="action" value="art_move_save">
<input type="hidden" name="id" value="<?php if(isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>">
<input type="hidden" name="parent_id" value="<?php if(isset($_REQUEST['parent_id'])) echo $_REQUEST['parent_id'];?>">
<table width="700" cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if(isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">В рубрику: </td>
		<td class="form_main">
		<select name="new_rub">
		<? 
		rubsub(0);
		
		function rubsub($parent_id)
		{
			global $sql_pref, $conn_id;
			$sql_query="SELECT id, name, level FROM ".$sql_pref."_pub_rubs WHERE parent_id='".$parent_id."' ORDER BY code";
			$sql_res=mysql_query($sql_query, $conn_id);
			if(mysql_num_rows($sql_res)>0)
			{
				while (list($id, $name, $level)=mysql_fetch_row($sql_res))
				{
					$name=StripSlashes($name);
					echo "<option value='".$id."'>";
					for ($i=1; $i<$level; $i++) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					if ($level==2) echo "&ndash;&nbsp;";
					elseif ($level==3) echo "&middot;&nbsp;";
					elseif ($level==4) echo "&nbsp;&nbsp;";
					echo $name."</option>";
					rubsub($id);
				}
			}
		}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input class="form_button" type="submit" name="form_art_move_save" value="Переместить"></td>
	</tr>
</table>
</form>