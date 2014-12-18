<?php


require_once("fns/config.php");
require_once("fns/common.php");
starting();
	
$country_id=$_GET['country_id'];


$sql_query="SELECT c.id, c.name, r.name FROM ".$sql_pref."_reg_regions AS r, ".$sql_pref."_reg_cities AS c WHERE c.country_id='".$country_id."'&&c.region_id=r.id ORDER BY c.name";
$sql_res=mysql_query($sql_query, $conn_id);
if (mysql_num_rows($sql_res)>0)
{
	while(list($reg_c_id, $reg_c_name, $reg_r_name)=mysql_fetch_row($sql_res))
	{
		$reg_c_name=stripslashes($reg_c_name); $reg_r_name=stripslashes($reg_r_name);
        if (!empty($reg_r_name)) $reg_r_name_show=" (".$reg_r_name.")"; else $reg_r_name_show="";
        $reg_c_name_show=$reg_c_name.$reg_r_name_show;
        $reg_c_name_show=iconv("windows-1251", "UTF-8", $reg_c_name_show); 
        $regions[]=array('id'=>$reg_c_id, 'title'=>$reg_c_name_show);
        
	}
}

$result = array('type'=>'success', 'cities'=>$regions);
print json_encode($result);

sql_close();

?>
