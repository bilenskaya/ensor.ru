<?php

$mailheader="";
$mailheader.="From: Ensor.ru <robot@ensor.ru>\r\n";
$mailheader.="MIME-Version: 1.0\r\n";
$mailheader.="Content-Type: text/html;\n charset=\"WINDOWS-1251\"";
    
mail("optima@tomos.ru", "Название письма", "Контент<br><br>письма", $mailheader);


?>