<h2>Подготовка к рассылке</h2>


<?

//$sql_query="SELECT id, dt, name, content FROM ".$sql_pref."_mail_letters WHERE id='".$_REQUEST['id']."'";
$sql_query="SELECT ".$sql_pref."_mail_letters.id, dt, name, content, email FROM ".$sql_pref."_mail_letters INNER JOIN ".$sql_pref."_mail_address ON ".$sql_pref."_mail_letters.email_id=".$sql_pref."_mail_address.id  WHERE ".$sql_pref."_mail_letters.id='".$_REQUEST['id']."'";

$sql_res=mysql_query($sql_query, $conn_id);
if (mysql_num_rows($sql_res)>0)
{
	list($id, $dt, $name, $content, $mail_from)=mysql_fetch_row($sql_res);
	$name=stripslashes($name); $content=stripslashes($content);
    $dt_show=date("d.m.Y H:i", strtotime($dt));
    
    echo "<div style='padding: 5 0;'>";
    echo "<table cellpadding=5 cellspacing=0 border=0 style='border:solid 1px #bbb;'>";
    echo "<tr><td><strong>От:</strong> ".$mail_from."@".$path_domen."</td></tr>";
    echo "<tr><td><strong>Тема:</strong> ".$name."</td></tr>";
    echo "<tr><td><strong>Письмо:</strong> <span onclick='if (document.getElementById(\"letter_content\").style.display==\"\") document.getElementById(\"letter_content\").style.display=\"none\"; else document.getElementById(\"letter_content\").style.display=\"\";' style='margin: 0 0 0 20;cursor:pointer; border-bottom:dotted 1px #999; color:#777;'>Отобразить / Скрыть</span></td></tr>";
    echo "<tr><td><div id=letter_content style='display:none;'>".$content."</div></td></tr>";
    echo "</table>";
    echo "</div>";
}


if (!empty($cur_directions) || !empty($cur_sfera)) $filsd=""; else $filsd="none";

$directions_filter="";
$sql_query="SELECT id, name FROM ".$sql_pref."_sd_directions ORDER BY code";
$sql_res_1=mysql_query($sql_query, $conn_id);
if(mysql_num_rows($sql_res_1)>0)
{
    $directions_filter.="<div style='padding: 5 0 0 5;font-weight:bold;'>Направления деятельности</div>";
    $directions_filter.="<div style='padding: 5 0 10 10;'>";
	while (list($d_id, $d_name)=mysql_fetch_row($sql_res_1))
	{
		$d_name=stripslashes($d_name);
        $d_name_show=$d_name;
        if (isset($_REQUEST['directions']) && in_array($d_id,$_REQUEST['directions'])) $ch="checked"; else $ch="";
        $directions_filter.="<div><input type=checkbox name=directions[] value='".@$d_id."' ".$ch."> ".$d_name_show."</div>";
	}
    $directions_filter.="</div>";
}



$sfery_filter="";
$sql_query="SELECT id, name FROM ".$sql_pref."_sd_sfery ORDER BY code";
$sql_res_1=mysql_query($sql_query, $conn_id);
if(mysql_num_rows($sql_res_1)>0)
{
    $sfery_filter.="<div style='padding: 5 0 0 5;font-weight:bold;'>Сферы деятельности</div>";
    $sfery_filter.="<div style='padding: 5 0 10 10;'>";
	while (list($d_id, $d_name)=mysql_fetch_row($sql_res_1))
	{
		$d_name=stripslashes($d_name);
        $d_name_show=$d_name;
        if (isset($_REQUEST['sfery']) && in_array($d_id,$_REQUEST['sfery'])) $ch="checked"; else $ch="";
        $sfery_filter.="<div><input type=checkbox name=sfery[] value='".@$d_id."' ".$ch."> ".$d_name_show."</div>";
	}
    $sfery_filter.="</div>";
}



echo "<div style='padding: 20 0 10 0;'><strong>Получатели:</strong></div>";

echo "  <div style='padding: 5 0 5 0;'>
            <span onclick='if (document.getElementById(\"subscriber_add\").style.display==\"\") document.getElementById(\"subscriber_add\").style.display=\"none\"; else document.getElementById(\"subscriber_add\").style.display=\"\";' style='margin: 0 20 0 0;cursor:pointer; border-bottom:dotted 1px #999; color:#555;'>Добавить получателя</span>
            <a href='?id=".$_REQUEST['id']."&action=subscribers_add_users_save'>Добавить получателей из базы пользователей</a>&nbsp; &nbsp;&nbsp;
            <a href='?id=".$_REQUEST['id']."&action=exb_subscribers_add_users_save'>Добавить получателей приглашений на выставки</a>&nbsp; &nbsp;&nbsp;            
            <span onclick='if (document.getElementById(\"divfiltersd\").style.display==\"\") document.getElementById(\"divfiltersd\").style.display=\"none\"; else document.getElementById(\"divfiltersd\").style.display=\"\";' style='margin: 0 20 0 0;cursor:pointer; border-bottom:dotted 1px #999; color:#555;'>Добавить организации</span>
            &nbsp; &nbsp;&nbsp;<a href='?id=".$_REQUEST['id']."&action=subscribers_del_users'>Удалить получателей</a>
        </div>
        
        <div id=subscriber_add style='padding: 10 0 5 0;display:none;'>
            <form name=form_subscriber_add action='index.html' method=post>
                <input type=hidden name=id value='".@$_REQUEST['id']."'>
                <input type=hidden name=action value=subscribers_add_form_save>
                Имя: <input class=form type=text id=name name=name value='' style='width:150px;'> &nbsp; &nbsp;
                Email: <input class=form type=text id=email name=email value='' style='width:150px;'> &nbsp; &nbsp;
                <input class=form_button type=submit name=button_submit value='Добавить'>
            </form>
        </div>
        <div id='divfiltersd' style='padding: 5 0 10 0;display:".$filsd.";border:solid 1px #bbb;background-color:#f6f6f6;'>
            <form action='index.html' method=post name=filter_sd_form style='padding: 0;margin: 0;'>
                <input type=hidden name=action value=filter_sd_form>
                <input type=hidden name=id value='".@$_REQUEST['id']."'>
                <table cellpadding=5 cellspacing=0 border=0>
                    <tr>
                        <td valign=top>".$directions_filter."</td>
                        <td valign=top>".$sfery_filter."</td>
                    </tr>
                </table>
                <div align=center style='padding: 10 0 0 0;'><input type=submit value='Добавить' style='font-size: 14px; width:150px; background-color: #fff; color: #555555; border: 1px #555555 solid;'></div>
            </form>
        </div>";



$sql_query="SELECT id, dt_send, name, email FROM ".$sql_pref."_mail_subscribers WHERE letter_id='".$_REQUEST['id']."' ORDER BY name";
$sql_res=mysql_query($sql_query, $conn_id);
if (mysql_num_rows($sql_res)>0)
{
    $i=0;
    echo "<div style='padding: 15 0 5 0;'>";
    echo "<table cellpadding=5 cellspacing=0 border=0>";
    echo "<tr>";
    echo "<td align=center style='border-bottom: solid 1px #999;'><strong>№</strong></td>";
    echo "<td style='border-bottom: solid 1px #999;'><strong>Получатель</strong></td>";
    echo "<td align=center style='border-bottom: solid 1px #999;'><strong>Email</strong></td>";
    echo "<td align=center style='border-bottom: solid 1px #999;'><strong>Отправка</strong></td>";
    echo "<td align=center style='border-bottom: solid 1px #999;'><strong>Удалить</strong></td>";
    echo "</tr>";
    while(list($id, $dt_send, $name, $email)=mysql_fetch_row($sql_res))
    {
        $name=stripslashes($name);
        $i++;
        $dt_send_show="-";
        if ($dt_send>0) $dt_send_show=date("d.m.Y H:i", strtotime($dt_send));
        
        if ($dt_send>0) $del_pic="<img src='/admin/img/del_inactive.gif' width='25' height='13' alt='Удалить' border='0'>";
        else $del_pic="<a href='?id=".$_REQUEST['id']."&subscriber_id=".$id."&action=subscribers_delete'><img src='/admin/img/del.gif' width='25' height='13' alt='Удалить' border='0'></a>";
        
        $unsend="";
        if ($dt_send>0) $unsend=" <a href='?id=".$_REQUEST['id']."&subscriber_id=".$id."&action=subscribers_unsend' title='Снять отметку об отправке' style='text-decoration:none;'>x</a>";

        
        echo "<tr>";
        echo "<td align=center>".$i."</td>";
        echo "<td>".$name."</td>";
        echo "<td align=center>".$email."</td>";
        echo "<td align=center>".$dt_send_show.@$unsend."</td>";
        echo "<td align=center>".$del_pic."</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
}




$sql_query="SELECT id FROM ".$sql_pref."_mail_subscribers WHERE letter_id='".$_REQUEST['id']."'&&dt_send='0000-00-00 00:00:00'";
$sql_res=mysql_query($sql_query, $conn_id);
if (mysql_num_rows($sql_res)>0)
{
    echo "<div style='padding: 20 0 20 0;'><a href='send.html?id=".$_REQUEST['id']."'>Разослать!</a></div>";
}
    


?>
