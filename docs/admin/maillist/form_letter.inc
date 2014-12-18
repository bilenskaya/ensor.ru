<?

if (isset($_REQUEST['action']) && $_REQUEST['action']=="letter_add")
{
	$title="Новое письмо";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="letter_edit")
{
	$sql_query="SELECT id, dt, name, content, email_id FROM ".$sql_pref."_mail_letters WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $dt, $name, $content, $email_id)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); $content=stripslashes($content);
	}
	$title="Редактирование (id: ".$_REQUEST['id'].")";
}

if (isset($_REQUEST['action']) && $_REQUEST['action']=="news_mail_gen")
{
    //require_once("../../fns/common.php");
    $delT=7;
    
    $str_add.="\n\nЧтобы ознакомиться с новой информацией, перейдите по ссылке: http://www.ensor.ru/\n";
    $str_add.="Если указанная выше ссылка не открывается, скопируйте ее в буфер обмена, вставьте в адресную строку браузера и нажмите ввод.\n\n";
    $str_add.="Вы получили это письмо, потому что зарегистрированы на сайте www.ensor.ru.\n\n";
    $str_add.="--\nС уважением,\nСлужба поддержки www.ensor.ru.\n";
    $str_add.="--------------------------------------------------------------";
    
    //выбираю пользователей, которые подписаны на рассылку
    $sql_query="SELECT id, dt_get_mail, last_visit FROM ".$sql_pref."_users WHERE enable='Yes' AND maillist='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
    //echo $sql_query;
	while (list($id, $dt_get_mail, $last_visit)=mysql_fetch_row($sql_res))
	{
        // если время последнего визита не определено 
        if (date("d.m.Y", strtotime($last_visit))==date("d.m.Y",strtotime("01.01.1970")))
        {
		      if($dt_get_mail!="") $delT=round((time()-strtotime($dt_get_mail))/(60*60*24),0)-1; else $delT=7;
        }
        // если время последнего визита определено
        else
        { 
            // определяю сколько времени прошло с последнего посещения
            $delT=round((time()-(strtotime($last_visit)))/(60*60*24),0)-1;
            // если время последней рассылки не ноль и последняя рассылка позднее последнего визита, то дельта=текущее время-дата последней рассылки
            if($dt_get_mail!=""&&(strtotime($dt_get_mail)>=strtotime($last_visit))) $delT=round((time()-strtotime($dt_get_mail))/(60*60*24),0)-1;
            
        }
        if($delT>7) $delT=7;
	    // бегаю по блогам за заданную дельту по времени        
        $str_blog_act=get_blog_activity($delT,"for_mail");
        // бегаю по статьям с комментами
        $str_article_act=get_article_activity($delT,"for_mail");
        // бегаю по новостям с комментариями
        $str_news_act=get_news_activity($delT,"for_mail");

                    
        if($str_blog_act!=""||$str_article_act!=""||$str_news_act!="")
        {
            // формирую текст сообщения
            $str_common=$str_blog_act."\n\n".$str_article_act."\n\n".$str_news_act.$str_add;
            $result=send_mail_to_user($id,"www.ensor.ru: лента активности сайта Ensor.ru",$str_common);
            if($result!="Ошибка")
            {
                $sql_query2="UPDATE ".$sql_pref."_users SET dt_get_mail='".date("Y-m-d")."' WHERE id='".$id."'";
                $sql_res2=mysql_query($sql_query2, $conn_id);
            }
        }
        $str_common="";
        sleep(3);
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']=="news_mail_gen_test")
{
    //require_once("../../fns/common.php");
    $delT=7;
    echo "ТЕСТ!!!";
    $str_add.="\n\nЧтобы ознакомиться с новой информацией, перейдите по ссылке: http://www.ensor.ru/\n";
    $str_add.="Если указанная выше ссылка не открывается, скопируйте ее в буфер обмена, вставьте в адресную строку браузера и нажмите ввод.\n\n";
    $str_add.="Вы получили это письмо, потому что зарегистрированы на сайте www.ensor.ru.\n\n";
    $str_add.="--\nС уважением,\nСлужба поддержки www.ensor.ru.\n";
    $str_add.="--------------------------------------------------------------";
    
    //выбираю пользователей, которые подписаны на рассылку
    $sql_query="SELECT id, dt_get_mail, last_visit FROM ".$sql_pref."_users WHERE enable='Yes' AND maillist='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
    //echo $sql_query;
	while (list($id, $dt_get_mail, $last_visit)=mysql_fetch_row($sql_res))
	{
        // если время последнего визита не определено 
        if (date("d.m.Y", strtotime($last_visit))==date("d.m.Y",strtotime("01.01.1970")))
        {
		      if($dt_get_mail!="") $delT=round((time()-strtotime($dt_get_mail))/(60*60*24),0)-1; else $delT=7;
        }
        // если время последнего визита определено
        else
        { 
            // определяю сколько времени прошло с последнего посещения
            $delT=round((time()-(strtotime($last_visit)))/(60*60*24),0)-1;
            // если время последней рассылки не ноль и последняя рассылка позднее последнего визита, то дельта=текущее время-дата последней рассылки
            if($dt_get_mail!=""&&(strtotime($dt_get_mail)>=strtotime($last_visit))) $delT=round((time()-strtotime($dt_get_mail))/(60*60*24),0)-1;
            
        }
        if($delT>7) $delT=7;
	    // бегаю по блогам за заданную дельту по времени        
        $str_blog_act=get_blog_activity($delT,"for_mail");
        // бегаю по статьям с комментами
        $str_article_act=get_article_activity($delT,"for_mail");
        // бегаю по новостям с комментариями
        $str_news_act=get_news_activity($delT,"for_mail");

        
        if($str_blog_act!=""||$str_article_act!=""||$str_news_act!="")
        {
            // формирую текст сообщения
            $str_common=$str_blog_act."\n\n".$str_article_act."\n\n".$str_news_act.$str_add;
        //    $result=send_mail_to_user($id,"Лента активности сайта Ensor.ru",$str_common);
        //    if($result!="Ошибка")
        //    {
        //        $sql_query2="UPDATE ".$sql_pref."_users SET dt_get_mail='".date("Y-m-d")."' WHERE id='".$id."'";
        //        $sql_res2=mysql_query($sql_query2, $conn_id);
        //    }
            echo "Пользователь [".$id." рассылка: ".$dt_get_mail." посл.визит:".$last_visit." дельта: ".$delT."]<br/>Текст:".$str_common."<BR/><HR>";
            //$content="Пользователь [".$id."]<br/>Текст:".$str_common."<BR/><HR>";
        }
        $str_common="";        
        sleep(3);
	}
}
?>

<a name='letter_add'></a><a name='letter_edit'></a>
<form name="form_letter" action="index.html" method="post">
<input type="hidden" name="id" value="<?php if (isset($_REQUEST['id'])) echo $_REQUEST['id']; ?>">
<input type="hidden" name="action" value="letter_save">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($name)) echo $name; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">Исходящий адрес</td>
		<td class="form_main">
                <select name="mail_from_id" id="mail_from_id">
				<option value="0">Нет данных</option>
			   <?
					$sql_query="SELECT id, email FROM ".$sql_pref."_mail_address WHERE id<>'".$id."' ORDER BY email";
					$sql_res=mysql_query($sql_query, $conn_id);
					while(list($p_id, $p_email)=mysql_fetch_row($sql_res))
					{
						$p_email=stripslashes($p_email);
						if ($p_id==@$email_id) $select="selected"; else $select="";
						echo "<option value=".$p_id." ".$select.">".$p_email."</option>";
					}
			   ?>
         </td>
	</tr>
    <tr>
		<td class="form_left">Тема</td>
		<td class="form_main"><input class="form" type="text" id="name" name="name" value='<?php if (isset($name)) echo $name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">Письмо</td>
		<td class="form_main"><?php require_once("../vis/visual.php"); ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class="form_button" type="submit" name="button_submit" value="Сохранить"></td>
	</tr>
</table>
</form>
