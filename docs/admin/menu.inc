<?
$menu_items=array (
"pubs"=>"������� ����������",
"slider"=>"�������",
"news"=>"�������", 
"companies"=>"�����������",
"objects"=>"�������", 
"top_management"=>"���-����������", 
"sd"=>"�����-�����������", 
"equipment"=>"�������", 
"gost"=>"������������", 
"useful"=>"�������� (�������)",
"producers"=>"�������������",
"gallery"=>"�������", 
"links"=>"������", 
"faq"=>"FAQ", 
"search"=>"�����", 
"partners"=>"��������", 
"orders"=>"������",
"lib"=>"����������",
"voc"=>"�������",
"articles"=>"������",
"forum"=>"�����",
"auth"=>"������������",
"blogs"=>"�����",
"vacancies"=>"��������",
"resume"=>"������",
"picture"=>"��������",
"maillist"=>"��������",
"calendar"=>"�������",
"actions"=>"�������� �������������",
"banners"=>"�������",
"services"=>"������");




echo "<span style='padding: 5 0 5 0;'><a class=mainmenu href='".$path_www."' target=_blank>".$path_www."</a></span>";
echo "<span style='padding: 5 10 5 10;'>&middot;</span>";

foreach ($menu_items AS $key=>$value)
{
    if (is_dir($path."/admin/".$key) && ($admin_status=="root" || in_array($key,$admin_modules)))
    {
        $name_show=$value;
        if ("/".$key."/"==substr($_SERVER["REQUEST_URI"],6,(strlen($key)+2))) $name_show="<span style='text-decoration:underline;'>".$name_show."</span>";
        echo "<span style='padding: 5 10 5 0;'><a class=mainmenu href='/admin/".$key."/'>".$name_show."</a></span>";
    }
    
}

	//echo "<span style='padding: 5 10 5 0;'>&middot;</span>";
    //if ($admin_status=="root") echo "<span style='padding: 5 10 5 0;'><a class=mainmenu href='/admin/sql/'>SQL</a></span>";
    
	//echo "<span style='padding 5 0 5 20;'>&middot;&nbsp;<a class=mainmenu href='/admin/welcome/help.html'>������</a></span>";
	echo "<span style='padding 5 0 5 0;'><a class=mainmenu href='/admin/?action=auth_logoff'>�����</a></span>";
?>