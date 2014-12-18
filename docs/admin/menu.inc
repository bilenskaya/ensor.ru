<?
$menu_items=array (
"pubs"=>"Система публикаций",
"slider"=>"Слайдер",
"news"=>"Новости", 
"companies"=>"Организации",
"objects"=>"Объекты", 
"top_management"=>"Топ-менеджмент", 
"sd"=>"Сферы-Направления", 
"equipment"=>"Каталог", 
"gost"=>"Документация", 
"useful"=>"Полезное (шаблоны)",
"producers"=>"Производители",
"gallery"=>"Галерея", 
"links"=>"Ссылки", 
"faq"=>"FAQ", 
"search"=>"Поиск", 
"partners"=>"Партнеры", 
"orders"=>"Заказы",
"lib"=>"Библиотека",
"voc"=>"Словарь",
"articles"=>"Статьи",
"forum"=>"Форум",
"auth"=>"Пользователи",
"blogs"=>"Блоги",
"vacancies"=>"Вакансии",
"resume"=>"Резюме",
"picture"=>"Картинки",
"maillist"=>"Рассылка",
"calendar"=>"События",
"actions"=>"Действия пользователей",
"banners"=>"Баннеры",
"services"=>"Услуги");




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
    
	//echo "<span style='padding 5 0 5 20;'>&middot;&nbsp;<a class=mainmenu href='/admin/welcome/help.html'>Помощь</a></span>";
	echo "<span style='padding 5 0 5 0;'><a class=mainmenu href='/admin/?action=auth_logoff'>Выход</a></span>";
?>