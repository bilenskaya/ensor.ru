<?
$filter_all_enable=" enable='Yes' AND visibility='all'";
$filter_all_enable_exibitions=" enable='Yes' AND visibility='all' AND type='exibition'";
$filter_all_enable_company=" enable='Yes' AND visibility='all' AND type='company'";
$filter_all_enable_ensor=" enable='Yes' AND visibility='all' AND type='ensor'";
$filter_all_enable_other=" enable='Yes' AND visibility='all' AND type='other'";

$filter_all_not_enable=" enable='No' AND visibility='all'";




function calendar_main()
{
	global $sql_pref, $conn_id, $art_url;
	$out="";
    
    //if (isset($_REQUEST['action']))
    //{
        //echo $_REQUEST['action'];
        //if ($_REQUEST['action']=="add_vote")  questions_answers_add_vote();
        
    //}    
	//else $out.=out_calendar_common();
    $out.=out_calendar_common();
	
	return ($out);
}


function out_calendar_common()
{
    global $table_dnp_news, $page_header1, $page_title, $user_id, $filter_all_enable;
    $page_header1="Календарь событий";
	$page_title="Энергетика. Календарь событий";
    
    $out.="<table align=left border=1 bordercolor=#BDD7D6 cellpadding=10>
            <tr>
                <td align=left valign=top>";

    //echo ."!!!jhg jg jg";
    if(isset($_REQUEST['action']) && $_REQUEST['action']=="add_on") {
       add_event_on('all','No',"",0);
    }
    if(isset($_REQUEST['action']) && $_REQUEST['action']=="del_ev") {
       events_del($_REQUEST['id']);
    }


       //очистка от гамна
    if (isset($_GET['month'])) {
       $month = $_GET['month'];
       $month = ereg_replace ("[[:space:]]", "", $month);
       $month = ereg_replace ("[[:punct:]]", "", $month);
       $month = ereg_replace ("[[:alpha:]]", "", $month);
       if ($month < 1) { $month = 12; }
       if ($month > 12) { $month = 1; }
       }
    
    if (isset($_GET['year'])) {
       $year = $_GET['year'];
       $year = ereg_replace ("[[:space:]]", "", $year);
       $year = ereg_replace ("[[:punct:]]", "", $year);
       $year = ereg_replace ("[[:alpha:]]", "", $year);
       if ($year < 1990) { $year = 1990; }
       if ($year > 2035) { $year = 2035; }
       }
    
    if (isset($_GET['today'])) {
       $today = $_GET['today'];
       $today = ereg_replace ("[[:space:]]", "", $today);
       $today = ereg_replace ("[[:punct:]]", "", $today);
       $today = ereg_replace ("[[:alpha:]]", "", $today);
       }
    
    
    $month = (isset($month)) ? $month : date("n",time());
    $year  = (isset($year)) ? $year : date("Y",time());
    $today = (isset($today))? $today : date("j", time());
    $daylong   = date("l",mktime(1,1,1,$month,$today,$year)); //день недели текст англ.
    $monthlong = date("F",mktime(1,1,1,$month,$today,$year)); //название месяца англ.
    $dayone    = date("w",mktime(1,1,1,$month,1,$year)); //день недели цифрой
    $numdays   = date("t",mktime(1,1,1,$month,1,$year)); //количество дней в месяце
    $alldays   = array('Пн','Вт','Ср','Чт','Пт','<font color=red>Сб</font>','<font color=red>Вс</font>');
    $next_year = $year + 1;
    $last_year = $year - 1;
    $next_month = $month + 1;
    $last_month = $month - 1;
    if ($today > $numdays) { $today--; }
            if($month == "1" ){$month_ru="январь";}
        elseif($month == "2" ){$month_ru="февраль";}
        elseif($month == "3" ){$month_ru="март";}
        elseif($month == "4" ){$month_ru="апрель";}
        elseif($month == "5" ){$month_ru="май";}
        elseif($month == "6" ){$month_ru="июнь";}
        elseif($month == "7" ){$month_ru="июль";}
        elseif($month == "8" ){$month_ru="август";}
        elseif($month == "9" ){$month_ru="сентябрь";}
        elseif($month == "10"){$month_ru="октябрь";}
        elseif($month == "11"){$month_ru="ноябрь";}
        elseif($month == "12"){$month_ru="декабрь";}
    
    
    $out.="<table><tr><td><table border=0 cellpadding=4 cellspacing=1 width=170>";
    
    //выводим название года
    $out.="<tr bgcolor=#E7EBEF>
          <td align=center><a href=".$path_calendar."?year=".$last_year."&today=".$today."&month=".$month."&action=view>&laquo;</a></td>";
    $out.="<td width=100% class=\"cellbg\" colspan=\"5\" valign=\"middle\" align=\"center\">
          <b>".$year." г.</b></td>\n";
    $out.="<td align=center><a href=".$path_calendar."?year=".$next_year."&today=".$today."&month=".$month."&action=view>&raquo;</a></td>";
    $out.= "</tr></table>";
    
    //выводим название месяца
    $out.= "<table border=0 cellpadding=4 cellspacing=1 width=170>";
    $out.= "<tr bgcolor=#E7EBEF>
          <td align=center><a href=".$path_calendar."?year=".$year."&today=".$today."&month=".$last_month."&action=view>&laquo;</a></td>";
    $out.= "<td width=100% class=\"cellbg\" colspan=\"5\" valign=\"middle\" align=\"center\">
          <b>".$month_ru."</b></td>\n";
    $out.= "<td align=center><a href=".$path_calendar."?year=".$year."&today=".$today."&month=".$next_month."&action=view>&raquo;</a></td>";
    $out.= "</tr></table>";
    
    //==================месяц
    $out.="<table border=0 cellpadding=2 cellspacing=1 width=170>
            <tr>";
    //выводим дни недели
    foreach($alldays as $value) {
      $out.= "<td valign=\"middle\" align=\"center\" width=\"10%\">
            <b>".$value."</b></td>";
    }
    $out.= "</tr>
            <tr>";    
    
    //выводим пустые дни месяца как пробелы
    if ($dayone == 0) {$dayone=7;}
    for ($i = 0; $i < ($dayone-1); $i++) {
      $out.= "<td valign=\"middle\" align=\"center\">&nbsp;</td>";
    }
    
    
    //выводим дни месяца
    for ($zz = 1; $zz <= $numdays; $zz++) {
     $stat_date = $year."-".$month."-".$zz;
      $stat_result = mysql_query("select * from ".$table_dnp_news." where ".$filter_all_enable." and datum = '".$stat_date."' ");
      $stat_rows=mysql_fetch_array($stat_result);
      $act_status = $stat_rows["act_status"];
      //echo "select * from ".$table_dnp_news." where user_id=".$user_id." and datum = '".$stat_date."' ";
      if ($i >= 7) {  $out.="</tr><tr>"; $i=0; }
    
      if ($zz == $today && $month==date("m",time()) && $year==date("Y",time())) {
        $out.= "<td valign=\"middle\" align=\"center\" bgcolor=#B9D7D5>";
              $news_date = $year."-".$month."-".$zz;
              $news_result = mysql_query("select * from ".$table_dnp_news." where ".$filter_all_enable." and datum = '".$news_date."'");
              $news_rows = mysql_num_rows($news_result);
               if($news_rows >0 and $act_status=="on") {
               $out.= "<a class=linkz href=\"".$path_calendar."?year=$year&today=$zz&month=$month&action=view\"><FONT COLOR=red>".$zz."</FONT></a>";
               }
    		  elseif($news_rows >0) {
               $out.= "<a class=linkz href=\"".$path_calendar."?year=$year&today=$zz&month=$month&action=view\" >".$zz."</a>";
               }
              else {
               $out.= $zz;
               }
              $out.= "</td>\n";
      }
      else {
        
    			
    	$out.= "<td valign=\"middle\" align=\"center\">";
              $news_date = $year."-".$month."-".$zz;
              $news_result = mysql_query("select * from ".$table_dnp_news." where ".$filter_all_enable." and datum = '".$news_date."'");
              $news_rows = mysql_num_rows($news_result);
              
    		  
    		  if($news_rows >0) {
    		     if ($act_status!="on") {
               $out.= "<a class=linkz href=\"".$path_calendar."?year=".$year."&today=".$zz."&month=".$month."&action=view\">".$zz."</a>";
    		   } else {
    		   $out.= "<a class=linkz href=\"".$path_calendar."?year=".$year."&today=".$zz."&month=".$month."&action=view\"><FONT COLOR=red>".$zz."</FONT></a>";
    		   }
              }
              else {
               $out.= $zz;
               }
              $out.= "</td>\n";
      }
    
      $i++;
    }
    
    $create_emptys = 7 - ((($dayone-1) + $numdays) % 7);
    if ($create_emptys == 7) { $create_emptys = 0; }
    
    //выводим пустые ячейки
    if ($create_emptys != 0) {
      $out.= "<td valign=\"middle\" align=\"center\" colspan=\"$create_emptys\"></td>\n";
    }
    
    $out.= "</tr></table></td><td> </td>";
    //текст поясняющий
    $out.="<td align=center>Если Вы хотите сообщить  о каком либо событии - заполните нижеприведенную форму. <br/> После модерации событие будет опубликовано на сайте.</td></tr></table>";
    //====================месяц  
    
    
    if(isset($_REQUEST['id']) && $_REQUEST['id']!="") $id=$_REQUEST['id']; else $id=-1;
    $out.="</td></tr><tr><td>".draw_event_line($year, $month, $today,$id,"all")."</td></tr><tr><td>".draw_add_event_form("all")."</td></tr></table>";     
    return $out;
}


function out_calendar()
{
    global $table_dnp_news, $page_header1, $page_title, $user_id, $filter_all_enable;
    $page_header1="Мои события";
	$page_title="Личный кабинет";
    
    $out.="<table align=left border=1 bordercolor=#BDD7D6 cellpadding=10>
            <tr>
                <td valign=top width=170>";

    //echo ."!!!jhg jg jg";
    if(isset($_REQUEST['action']) && $_REQUEST['action']=="add_on") {
       add_event_on("user","Yes","",0);
    }
    if(isset($_REQUEST['action']) && $_REQUEST['action']=="del_ev") {
       events_del($_REQUEST['id']);
    }


       //очистка от гамна
    if (isset($_GET['month'])) {
       $month = $_GET['month'];
       $month = ereg_replace ("[[:space:]]", "", $month);
       $month = ereg_replace ("[[:punct:]]", "", $month);
       $month = ereg_replace ("[[:alpha:]]", "", $month);
       if ($month < 1) { $month = 12; }
       if ($month > 12) { $month = 1; }
       }
    
    if (isset($_GET['year'])) {
       $year = $_GET['year'];
       $year = ereg_replace ("[[:space:]]", "", $year);
       $year = ereg_replace ("[[:punct:]]", "", $year);
       $year = ereg_replace ("[[:alpha:]]", "", $year);
       if ($year < 1990) { $year = 1990; }
       if ($year > 2035) { $year = 2035; }
       }
    
    if (isset($_GET['today'])) {
       $today = $_GET['today'];
       $today = ereg_replace ("[[:space:]]", "", $today);
       $today = ereg_replace ("[[:punct:]]", "", $today);
       $today = ereg_replace ("[[:alpha:]]", "", $today);
       }
    
    
    $month = (isset($month)) ? $month : date("n",time());
    $year  = (isset($year)) ? $year : date("Y",time());
    $today = (isset($today))? $today : date("j", time());
    $daylong   = date("l",mktime(1,1,1,$month,$today,$year)); //день недели текст англ.
    $monthlong = date("F",mktime(1,1,1,$month,$today,$year)); //название месяца англ.
    $dayone    = date("w",mktime(1,1,1,$month,1,$year)); //день недели цифрой
    $numdays   = date("t",mktime(1,1,1,$month,1,$year)); //количество дней в месяце
    $alldays   = array('Пн','Вт','Ср','Чт','Пт','<font color=red>Сб</font>','<font color=red>Вс</font>');
    $next_year = $year + 1;
    $last_year = $year - 1;
    $next_month = $month + 1;
    $last_month = $month - 1;
    if ($today > $numdays) { $today--; }
            if($month == "1" ){$month_ru="январь";}
        elseif($month == "2" ){$month_ru="февраль";}
        elseif($month == "3" ){$month_ru="март";}
        elseif($month == "4" ){$month_ru="апрель";}
        elseif($month == "5" ){$month_ru="май";}
        elseif($month == "6" ){$month_ru="июнь";}
        elseif($month == "7" ){$month_ru="июль";}
        elseif($month == "8" ){$month_ru="август";}
        elseif($month == "9" ){$month_ru="сентябрь";}
        elseif($month == "10"){$month_ru="октябрь";}
        elseif($month == "11"){$month_ru="ноябрь";}
        elseif($month == "12"){$month_ru="декабрь";}
    
    
    $out.="<table border=0 cellpadding=4 cellspacing=1 width=170>";
    
    //выводим название года
    $out.="<tr bgcolor=#E7EBEF>
          <td align=center><a href=".$path_calendar."?year=".$last_year."&today=".$today."&month=".$month."&action=view>&laquo;</a></td>";
    $out.="<td width=100% class=\"cellbg\" colspan=\"5\" valign=\"middle\" align=\"center\">
          <b>".$year." г.</b></td>\n";
    $out.="<td align=center><a href=".$path_calendar."?year=".$next_year."&today=".$today."&month=".$month."&action=view>&raquo;</a></td>";
    $out.= "</tr></table>";
    
    //выводим название месяца
    $out.= "<table border=0 cellpadding=4 cellspacing=1 width=170>";
    $out.= "<tr bgcolor=#E7EBEF>
          <td align=center><a href=".$path_calendar."?year=".$year."&today=".$today."&month=".$last_month."&action=view>&laquo;</a></td>";
    $out.= "<td width=100% class=\"cellbg\" colspan=\"5\" valign=\"middle\" align=\"center\">
          <b>".$month_ru."</b></td>\n";
    $out.= "<td align=center><a href=".$path_calendar."?year=".$year."&today=".$today."&month=".$next_month."&action=view>&raquo;</a></td>";
    $out.= "</tr></table>";
    
    //==================месяц
    $out.="<table border=0 cellpadding=2 cellspacing=1 width=170>
            <tr>";
    //выводим дни недели
    foreach($alldays as $value) {
      $out.= "<td valign=\"middle\" align=\"center\" width=\"10%\">
            <b>".$value."</b></td>";
    }
    $out.= "</tr>
            <tr>";    
    
    //выводим пустые дни месяца как пробелы
    if ($dayone == 0) {$dayone=7;}
    for ($i = 0; $i < ($dayone-1); $i++) {
      $out.= "<td valign=\"middle\" align=\"center\">&nbsp;</td>";
    }
    
    
    //выводим дни месяца
    for ($zz = 1; $zz <= $numdays; $zz++) {
     $stat_date = $year."-".$month."-".$zz;
      $stat_result = mysql_query("select * from ".$table_dnp_news." where (user_id='".$user_id."' AND datum = '".$stat_date."') OR (".$filter_all_enable.")");
      $stat_rows=mysql_fetch_array($stat_result);
      $act_status = $stat_rows["act_status"];
      //echo "act_status=$act_status";
      if ($i >= 7) {  $out.="</tr><tr>"; $i=0; }
    
      if ($zz == $today && $month==date("m",time()) && $year==date("Y",time())) {
        $out.= "<td valign=\"middle\" align=\"center\" bgcolor=#B9D7D5>";
              $news_date = $year."-".$month."-".$zz;
              $news_result = mysql_query("select * from ".$table_dnp_news." where user_id='".$user_id."' AND datum = '".$news_date."'");
              $news_rows = mysql_num_rows($news_result);
               if($news_rows >0 and $act_status=="on") {
               $out.= "<a class=linkz href=\"".$path_calendar."?year=$year&today=$zz&month=$month&action=view\"><FONT COLOR=red>".$zz."</FONT></a>";
               }
    		  elseif($news_rows >0) {
               $out.= "<a class=linkz href=\"".$path_calendar."?year=$year&today=$zz&month=$month&action=view\" >".$zz."</a>";
               }
              else {
               $out.= $zz;
               }
              $out.= "</td>\n";
      }
      else {
        
    			
    	$out.= "<td valign=\"middle\" align=\"center\">";
              $news_date = $year."-".$month."-".$zz;
              $news_result = mysql_query("select * from ".$table_dnp_news." where user_id='".$user_id."' AND datum = '".$news_date."'");
              $news_rows = mysql_num_rows($news_result);
              
    		  
    		  if($news_rows >0) {
    		     if ($act_status!="on") {
               $out.= "<a class=linkz href=\"".$path_calendar."?year=".$year."&today=".$zz."&month=".$month."&action=view\">".$zz."</a>";
    		   } else {
    		   $out.= "<a class=linkz href=\"".$path_calendar."?year=".$year."&today=".$zz."&month=".$month."&action=view\"><FONT COLOR=red>".$zz."</FONT></a>";
    		   }
              }
              else {
               $out.= $zz;
               }
              $out.= "</td>\n";
      }
    
      $i++;
    }
    
    $create_emptys = 7 - ((($dayone-1) + $numdays) % 7);
    if ($create_emptys == 7) { $create_emptys = 0; }
    
    //выводим пустые ячейки
    if ($create_emptys != 0) {
      $out.= "<td valign=\"middle\" align=\"center\" colspan=\"$create_emptys\"></td>\n";
    }
    
    $out.= "</tr></table>";
    //====================месяц  
    
    
    if(isset($_REQUEST['id']) && $_REQUEST['id']!="") $id=$_REQUEST['id']; else $id=-1;
    $out.="</td></tr><tr><td>".draw_event_line($year, $month, $today,$id,"user")."</td></tr><tr><td>".draw_add_event_form("user")."</td></tr></table>";     
    return $out;
}

function draw_add_event_form($form_type)
{
    global $path_calendar, $user_id;
    $year=date('Y');
    $month=date('m');
    $day=date('d');
    $now_date = $year."-".$month."-".$day;
    $time=date('H:i');
    
    //echo $path_calendar." ".$_SERVER['PHP_SELF'];
    $out="<br/><br/><br/><form action='' method=post enctype=multipart/form-data name=formata>
    ..:: Добавление события ::.. <hr>
    <table cellpadding=5 cellspacing=0 border=0 width=100%>
      <tr>
        <td width=13%>Дата:</td>
        <td>";
            $out.="<select name='c_day'>";
            $days = array('00', '01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31',);
           	$months = array('None', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',);
           	$months_num = array('None', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12',);
            foreach($days as $key => $value) {
            	if ($key != "0") {
               		$out.="<option value=\"$value\" ";
               		if($value == date(d)) { $out.="selected"; };
               		$out.= ">".$key."</option>";
               		}
            	}
            	$out.= "</select><select id=\"c_month\" name=\"c_month\">";
                foreach($months_num as $key => $value) {
                	if ($key != "0") {
                   		$out.= "<option value=\"$value\" ";
                   		if($value == date(m)) { $out.=" selected"; };
              			$out.= ">".$value."</option>";}              		
            	}
            	$out.= "</select><input type='text' id='c_year' name='c_year' size='4' maxlength='4' value='".date(Y)."'/></p>
            	   
        </td>
      </tr>
      <tr>
        <td>Событие актуально:</td><td><input type=text name='actuals' value='0' size='2' maxlength='3'> дней <INPUT TYPE='checkbox' NAME='act_on'> Включить&nbsp;|&nbsp;будет отображаться в самом верху событий</td> 
      </tr>";
      if($user_id!=0&&$form_type=="user")
      {
            $out.="<tr>
                    <td>Видимость события:</td><td><input type=radio name=visibility value='user' checked> только мне &nbsp;&nbsp; <input type=radio name=visibility value='all'> всем
                    </td> 
                  </tr>";
      }
      if($form_type=="all")
      {        
            $out.="<tr>
                    <td></td><td><input type=hidden name=visibility value='all'>
                    </td> 
                  </tr>";
            
            $out.="<tr>
                    <td>Тип события:</td>
                    <td>
                           <input type=radio  name=type value='exibition'>событие выставок
                           <input type=radio  name=type value='company'>событие компании
                           <input type=radio  name=type value='ensor'>событие Ensor    
                           <input type=radio  name=type value='other' checked='Yes'>прочие события  
                    </td> 
                  </tr>";
      }
      if($user_id==0)
      {
           $out.="<tr>
                    <td>Контактная информация (е-мэйл, ФИО):</td>
                    <td><input type=text name=title style='width: 100%;'></td>
                  </tr>";
      }
      $out.="<tr>
        <td>Заголовок:</td>
        <td><input type=text name=title style='width: 100%;'></td>
      </tr>
      <tr>
        <td></td>
        <td>";
        
        if ($db_status_us == 1) {
                       
               $out.="<a href=\"javascript: voidPutATag('<b>','</b>')\" title=\"полужирный текст\" class=buten>b</a>
                      <a href=\"javascript: voidPutATag('<i>','</i>')\" title=\"курсив\" class=buten>i</a>
                      <a href=\"javascript: voidPutATag('<u>','</u>')\" title=\"подчеркивание\" class=buten>u</u>
                      <a href=\"javascript: voidPutATag('<center>','</center>')\" title=\"\" class=buten>center</a>
                      <a href=\"javascript: voidPutATag('<ul>','</ul>')\" title=\"список\" class=buten>ul</a>
                      <a href=\"javascript: voidPutATag('<li>','</li>')\" title=\"элемент списка\" class=buten>li</a>
                      <a href=\"javascript: voidPutATag('&laquo;','&raquo;')\" title=\"кавычки\" class=buten>&laquo; &raquo;</a>
                      <a href=\"javascript: voidPutATag('\n<br>','\n ')\" title=\"перенос строки\" class=buten>br</a>
                      <a href=\"javascript: voidPutATag('\n<p>','\n ')\" title=\"абзац\" class=buten>Абзац</a>
                      <a href=\"javascript: voidPutATag('<a href=>','</a>')\" title=\"ссылка\" class=buten>ссылка</a>";
        } 
        else {};
         $out.="</td>
                  </tr>
                  <tr>
                    <td></td>
                    <td>
                      
                    </td>
                  </tr>
        <tr>
            <td valign=top>Содержание:</td>
            <td><textarea name=\"content\" style=\"height: 250px; width: 100%; padding: 5px;\"></textarea></td>
        </tr>
        <tr>
            <td valign=top></td>
            <td><INPUT TYPE=\"reset\" class=btn value=\"Очистить\">
                <input type=submit class=btn value=\"Сохранить\">
            </td>
        </tr>
    </table>
        <input type=hidden name=do value=\"save\">
        <input type=hidden name=action value=\"add_on\">
        <input type=hidden name=time value=\"".$time."\">
    </form>";
    
    return $out;
    
}

function add_event_on($visibility,$enable,$type,$group_id)
{
   echo $visibility."___".$group_id."___".$enable."___".$type;
   global $path_calendar, $table_dnp_news, $user_id;
   
   $datum=$_REQUEST['c_year']."-".$_REQUEST['c_month']."-".$_REQUEST['c_day'];
   $c_year=$_REQUEST['c_year'];
   $c_month=$_REQUEST['c_month'];
   $c_day=$_REQUEST['c_day'];
   $time=$_REQUEST['time'];
   $actuals=$_REQUEST['actuals'];
   $act_on=$_REQUEST['act_on'];
   $visibility=$_REQUEST['visibility'];
   $type=$_REQUEST['type'];
   
   if(isset($_REQUEST['title']) && $_REQUEST['title']!=""  &&
      isset($_REQUEST['content']) && $_REQUEST['content']!="")
   {
    
     $title=$_REQUEST['title'];
     $content=$_REQUEST['content'];
     
     $visible='on';
     $ip=getenv("REMOTE_ADDR")."::".getenv("HTTP_X_FORWARDED_FOR");
     $brouser=getenv("HTTP_USER_AGENT");

     
	 $datum=$c_year."-".$c_month."-".$c_day;	
	//HTML с переносом строк
	if ($db_status_us == 1) {} else {
	 $content = str_replace("\n",'<br />', $content);
	 $content = preg_replace("#\[php\](.+?)\[/php\]#ies","php_highlight('\\1')",$content);
	}
     $title   = str_replace("\"","&quot;", $title);
     
	 //$n_actuals=$actuals*3600*24;
	 $nd_mk_date=mktime(0, 0, 0, $c_month, $c_day, $c_year);
	 //$and_mk_date=$nd_mk_date+$n_actuals;
     $and_mk_date=$actuals;

	 mysql_query("insert into ".$table_dnp_news."
            values(null,
                  \"$time\",
                  \"$datum\",
                  \"$nd_mk_date\",
                  \"$title\",
                  \"$content\",
                  \"$visible\",
                  \"$ip\",
                  \"$and_mk_date\",
                  \"$act_on\",
                  \"$brouser\",
                  \"$user_id\",
                  \"$enable\",
                  \"$group_id\",
                  \"$type\",
                  \"$visibility\")")or die(mysql_error());
     //echo "<font color=green>Запись внесена!!!</font><hr>";
     $raz=explode("-",$datum);
     //echo "<nobr><a href='".$_SERVER['PHP_SELF']."?action=view&year=".$raz[0]."&today=".$raz[2]."&month=".$raz[1]."' ";
     //echo "class=buten>В просмотр &raquo;</a></nobr>";
   }
   else
   {
      //echo "Информация не может быть добавлена, вы ввели неверные данные<br><ul>";
      //if(!isset($title)   || $title==""){echo "<li>Поле \"Заголовок\" должно быть заполнено обязательно!</li>";}
      //if(!isset($content) || $content==""){echo "<li>\"Содержание\" должно быть заполнено обязательно!</li>";}
      //if(!isset($datum)   || $datum==""){echo "<li>Поле \"Дата\" $datum $c_year должно быть заполнено обязательно!</li>";}
      //echo "</ul><hr size=1 color=black noshade></a><a href=javascript:history.back(2) class=menu><< Попробуйте еще раз.</a>";
   }
}

function draw_event_line($year,$month,$today,$id, $type)
{
   global $path_calendar, $table_dnp_news, $user_id, $filter_all_enable;
   
   $sql_date = $year."-".$month."-".$today;
   $monthNum=$month;
   if($type=="user")
   {
        $result = mysql_query("select * from ".$table_dnp_news." where enable='Yes' AND (user_id=".$user_id." OR ".$filter_all_enable.") AND (datum='".$sql_date."' OR DATEDIFF(CURDATE(),datum)<actuals) order by datum desc");
   }
   elseif($type=="all")
   {
    $result = mysql_query("select * from ".$table_dnp_news." where ".$filter_all_enable." AND (datum='".$sql_date."' OR DATEDIFF(CURDATE(),datum)<actuals) order by datum desc");
   }
   $out.=$result;
   $rows = mysql_num_rows($result);


   if($rows > 0 && $id<0)
   {
      
      $out="<table width=100% cellpadding=4 border=0 cellspacing=2>";
      
      for($k=0;$k < $rows;$k++)
      {
        $time=mysql_result($result, $k , "time");
        $datum=mysql_result($result, $k , "datum");
        $title=mysql_result($result, $k , "title");
        $id=mysql_result($result, $k , "id");
        $author_id=mysql_result($result, $k , "user_id");

        $datun=explode("-",$datum);
            if($datun[1] == "1" || $datun[1] == "01"){$month="января";}
        elseif($datun[1] == "2" || $datun[1] == "02"){$month="февраля";}
        elseif($datun[1] == "3" || $datun[1] == "03"){$month="марта";}
        elseif($datun[1] == "4" || $datun[1] == "04"){$month="апреля";}
        elseif($datun[1] == "5" || $datun[1] == "05"){$month="мая";}
        elseif($datun[1] == "6" || $datun[1] == "06"){$month="июня";}
        elseif($datun[1] == "7" || $datun[1] == "07"){$month="июля";}
        elseif($datun[1] == "8" || $datun[1] == "08"){$month="августа";}
        elseif($datun[1] == "9" || $datun[1] == "09"){$month="сентября";}
        elseif($datun[1] == "10"){$month="октября";}
        elseif($datun[1] == "11"){$month="ноября";}
        elseif($datun[1] == "12"){$month="декабря";}

        if(($k % 2) == 0){$bgcol="#F7F8FC";}
        else{$bgcol="#EBEBEC";}
        $kp=$k+1;
        $out.="
         <tr bgcolor=".$bgcol.">
           <td>".$kp."</td>
           <td>".$time."</td>
           <td><nobr>".$datun[2]." ".$month." ".$datun[0]." </nobr></td>
           <td><a href=\"./?id=".$id."&action=view&long=ok&year=".$year."&today=".$today."&month=".$monthNum."\">".$title."></a>";
           if($user_id!=0 && $user_id==$author_id)
           {
                $out.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_calendar."?id=".$id."&action=del_ev&long=ok&year=".$year."&today=".$today."&month=".$monthNum."'\"  style='font-size:9px;color:#999999;'>Удалить</a>"; 
           }
           $out.="</td></tr>";        
        }
        $out.="</table>";
      }
      elseif($id>0 && $_REQUEST['action']!="del_ev")
      {
         $result = mysql_query("select * from ".$table_dnp_news." where id='".$id."'  limit 1");
         $rows = mysql_num_rows($result);

         $datum=mysql_result($result, 0 , "datum");
         $title=mysql_result($result, 0 , "title");
         $id=mysql_result($result, 0 , "id");
         $ip=mysql_result($result, 0 , "ip");
         $time=mysql_result($result, 0 , "time");
         $brouser=mysql_result($result, 0 , "brouser");
         $content=mysql_result($result, 0 , "content");
         $content=str_replace("admin/","../admin/",$content);

         $out.="<hr>
         <table width=100% cellpadding=2 border=0 cellspacing=0 style=\"border: solid 1 px gray;\">
           <tr>
             <td class=header bgcolor=\"#F0F0F0\">Дата: <b>".$datum."</b> | Время: <b>".$time."</b></td>
           </tr>
           <tr>
             <td class=header bgcolor=\"#F0F0F0\">Заголовок: <b>".$title."</b></td>
           </tr>
           <!--tr>
             <td class=header bgcolor=\"#F0F0F0\">IP адрес автора: <b>".$ip."</b></td>
           </tr>
           <tr>
             <td class=header bgcolor=\"#F0F0F0\">Система: <b>".$brouser."</b></td>
           </tr!-->
           <tr>
             <td>
              <table style=\"text-align: justify; border: solid 1 px black; padding: 10px; width: 100%\">
              <tr><td>
              ".$content."
              </td></tr>
              </table>
             </td>
           </tr>
         </table>
         <hr>
         <a href=\"javascript: history.back(2)\">&laquo; назад</a>";         
      }
      
      return $out;  
}

function events_del($id)
{
	global $sql_pref, $conn_id, $path, $page_header1, $user_id, $user_status, $art_url, $path_calendar, $table_dnp_news;
	$out="";
	
	if ($id<=0) return;
    
	$sql_query="SELECT id FROM ".$table_dnp_news." WHERE id='".$id." AND user_id=".$user_id;
	$sql_res=mysql_query($sql_query, $conn_id);
	//if (mysql_num_rows($sql_res)==0 && $user_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	
	
	$sql_query="DELETE FROM ".$table_dnp_news." WHERE id=".$id;
	$sql_res=mysql_query($sql_query, $conn_id);

	//header("location:/".$path_calendar."/".$art_url.".html"); exit();
}

?>