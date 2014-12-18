<?


echo "<br><br>";
echo '<a name="directions"></a>';
echo "<h2>Направления деятельности</h2>";
echo '<div style="width:370px;">';
echo '<form name="form_name" action="index.html" method="post">';
echo '<input type="hidden" name="action" value="directions_save">';

$sql_query="SELECT id, name, code FROM ".$sql_pref."_sd_directions ORDER BY code";
$sql_res=mysql_query($sql_query, $conn_id);
if (mysql_num_rows($sql_res)>0)
{
    while(list($id, $name, $code)=mysql_fetch_row($sql_res))
    {
        $name=stripslashes($name);
        $name_show='<input type="text" name="name_'.$id.'" value="'.$name.'" style="width:300px;border:0px;">';
        
		if ($code>1) $sort_up="<a href='?id=".$id."&action=directions_sort_up'><img src='/admin/img/up.gif' width='11' height=13 alt='Сортировка: Выше' border=0></a>"; else $sort_up="<img src='/admin/img/sort_none.gif' width='11' height=13 border=0>";
		$sql_query="SELECT id FROM ".$sql_pref."_sd_directions WHERE code='".($code+1)."'"; $sql_res_1=mysql_query($sql_query, $conn_id); 
		if (mysql_num_rows($sql_res_1)>0) $sort_down="<a href='?id=".$id."&action=directions_sort_down'><img src='/admin/img/down.gif' width='11' height=13 alt='Сортировка: Ниже' border=0></a>"; else $sort_down="<img src='/admin/img/sort_none.gif' width='11' height=13 border=0>";
        
        $fl=0;
		$sql_query="SELECT id FROM ".$sql_pref."_companies WHERE direction_ids='".$id."' || direction_ids LIKE '".$id.";%' || direction_ids LIKE '%;".$id."' || direction_ids LIKE '%;".$id.";%' ";
        $sql_res_1=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res_1)>0) $fl=1; 
		$sql_query="SELECT id FROM ".$sql_pref."_objects WHERE direction_ids='".$id."' || direction_ids LIKE '".$id.";%' || direction_ids LIKE '%;".$id."' || direction_ids LIKE '%;".$id.";%' ";
        $sql_res_1=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res_1)>0) $fl=1; 
        if ($fl==1) $del="<img src='/admin/img/del_inactive.gif' width=25 height=13 alt='Удалить' border=0>";
        else $del="<a href=\"javascript:if(confirm('Вы уверены?'))window.location='?id=".$id."&action=directions_delete'\"><img src='/admin/img/del.gif' width=25 height=13 alt='Удалить' border=0></a>";
        
		echo '<div style="padding:2px;border-bottom:solid 1px #ddd;">'.$sort_down.$sort_up.'&nbsp;&nbsp;'.$name_show.'&nbsp;'.$del.'</div>';
    }
}
echo '<div style="padding:10px 0px 0px 30px;"><input type="text" name="name_new" value="" style="width:300px;"></div>';
echo '<div style="padding:10px 0px 0px 30px;"><input class="form_button" type="submit" name="button_submit" value="Сохранить"></div>';
echo '</form>';
echo '</div>';

?>