<?php

function sd_show()
{
	global $sql_pref, $conn_id, $path;
    
	echo "<br><li><a href='?action=directions_show#directions'>Направления</a></li>";
	echo "<li><a href='?action=sfery_show#sfery'>Сферы</a></li>";
	echo "<br>";
}














function form_directions_save()
{
	global $sql_pref, $conn_id;
    
    
    $sql_query="SELECT id FROM ".$sql_pref."_sd_directions";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
        
        while(list($id)=mysql_fetch_row($sql_res))
        {
            if (isset($_REQUEST['name_'.$id]) && !empty($_REQUEST['name_'.$id])) $name=addslashes(trim($_REQUEST['name_'.$id])); else $name="";
            if (!empty($name))
            {
        		$sql_query="UPDATE ".$sql_pref."_sd_directions SET name='".$name."' WHERE id='".$id."'";
        		$sql_res_1=mysql_query($sql_query, $conn_id);
            }
        }
    }

    $sql_query="SELECT id FROM ".$sql_pref."_sd_directions";
    $sql_res=mysql_query($sql_query, $conn_id);
    $code=mysql_num_rows($sql_res)+1;
    
	if (isset($_REQUEST['name_new']) && !empty($_REQUEST['name_new'])) $name=addslashes(trim($_REQUEST['name_new'])); else $name="";
    if (!empty($name))
    {
    	$sql_query="INSERT INTO ".$sql_pref."_sd_directions (name, code) VALUES ('".$name."', '".$code."')";
    	$sql_res=mysql_query($sql_query, $conn_id);
    }
    
}





function form_sfery_save()
{
	global $sql_pref, $conn_id;
    
    
    $sql_query="SELECT id FROM ".$sql_pref."_sd_sfery";
    $sql_res=mysql_query($sql_query, $conn_id);
    if (mysql_num_rows($sql_res)>0)
    {
        
        while(list($id)=mysql_fetch_row($sql_res))
        {
            if (isset($_REQUEST['name_'.$id]) && !empty($_REQUEST['name_'.$id])) $name=addslashes(trim($_REQUEST['name_'.$id])); else $name="";
            if (!empty($name))
            {
        		$sql_query="UPDATE ".$sql_pref."_sd_sfery SET name='".$name."' WHERE id='".$id."'";
        		$sql_res_1=mysql_query($sql_query, $conn_id);
            }
        }
    }

    $sql_query="SELECT id FROM ".$sql_pref."_sd_sfery";
    $sql_res=mysql_query($sql_query, $conn_id);
    $code=mysql_num_rows($sql_res)+1;
    
	if (isset($_REQUEST['name_new']) && !empty($_REQUEST['name_new'])) $name=addslashes(trim($_REQUEST['name_new'])); else $name="";
    if (!empty($name))
    {
    	$sql_query="INSERT INTO ".$sql_pref."_sd_sfery (name, code) VALUES ('".$name."', '".$code."')";
    	$sql_res=mysql_query($sql_query, $conn_id);
    }
    
}






?>
