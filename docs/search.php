<?php


require_once("fns/config.php");
require_once("fns/common.php");
starting();
require_once("fns/auth.php");
auth_maincheck();

global $user_id, $_SERVER;
$search_table=$_GET['table'];
$out="";	
$query=$_GET['q'];

$rus = array("�","�", "�", "�", "�", "�", "�", 
"�", "�", "�", "�", "�", "�", "�", "�", "�", 
"�", "�", "�", "�", "�", "�", "�", "�", "�", 
"�", "�", "�", "�", "�", "�", "�", "�"); 
//if (!eregi("^[a-zA-Z0-9]+$", $query)) 
//{
//    $query=iconv("UTF-8", "WINDOWS-1251" ,$query);
//}
//$trans = array("192"=>"�","81"=>"�", "87"=>"�", "69"=>"�", "82"=>"�", "84"=>"�", "89"=>"�", 
//"85"=>"�", "73"=>"�", "79"=>"�", "80"=>"�", "219"=>"�", "221"=>"�","65"=>"�", "83"=>"�", "68"=>"�", 
//"70"=>"�", "71"=>"�", "72"=>"�", "74"=>"�", "75"=>"�", "76"=>"�", "186"=>"�", "222"=>"�", "90"=>"�", 
//"88"=>"�", "67"=>"�", "86"=>"�", "66"=>"�", "78"=>"�", "77"=>"�", "188"=>"�", "190"=>"�"); 
//if(strtr($query, $rus)) $out.="����"; else $out="���";

if(eregi("opera",$_SERVER['HTTP_USER_AGENT']))
{    
    $query=iconv("UTF-8", "WINDOWS-1251" ,$query);   
}

//$out.=$query;
//$out.=iconv("UTF-8", "WINDOWS-1251" ,$query);
//echo $search_table;

//echo $sql_query;
$out.="<table border=0 class='search'>";
switch ($search_table)
{
    case 'to_user':
        $sql_query="SELECT id, surname, name, name2 FROM ".$sql_pref."_users WHERE ((INSTR(surname,'".$query."')>0) AND (id<>".@$user_id.")) AND enable='Yes' ORDER BY surname LIMIT 25";
        //echo $sql_query;
        $sql_res=mysql_query($sql_query, $conn_id);
        if (mysql_num_rows($sql_res)>0)
        {
            $i=1;
            while(list($user_id, $surname, $name, $name2)=mysql_fetch_row($sql_res))
        	{
                $user_name_show=$surname." ".$name." ".$name2;
        	    $out.="<tr style='cursor:default' onmouseover=\"select_popup(".$i.");\" onclick=\"fill_fields(".$i.");\"><td width=100% alttext='".$user_name_show."' value='".$user_id."'>".$user_name_show."</td></tr>";
                $i++;
            }
        }
        break;
    
    case 'to_org':
        $sql_query="SELECT id, name_full FROM ".$sql_pref."_companies WHERE INSTR(name_full,'".$query."')>0 ORDER BY name LIMIT 13";
        $sql_res=mysql_query($sql_query, $conn_id);
        if (mysql_num_rows($sql_res)>0)
        {
            
        	while(list($id, $name_full)=mysql_fetch_row($sql_res))
        	{
        	    $out.="<tr><td width=100% alttext='".stripslashes($name_full)."' value='".$id."'>".stripslashes($name_full)."</td></tr>";
            }
        }
        break;
        
    default:
    
        break;
}
$out.="</table>";
        
echo $out;

sql_close();

?>