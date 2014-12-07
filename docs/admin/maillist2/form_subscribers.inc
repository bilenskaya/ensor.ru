<h2>Подготовка к рассылке</h2>


<?

$letter_name="Энергетическое сообщество России. Вниманию коллег";
$content=file_get_contents("letter.txt");


echo "<div style='padding: 5 0;'>";
echo "<table cellpadding=5 cellspacing=0 border=0 style='border:solid 1px #bbb;'>";
echo "<tr><td><strong>От:</strong> avkuryatov@ensor.ru</td></tr>";
echo "<tr><td><strong>Тема:</strong> ".$letter_name."</td></tr>";
echo "<tr><td><strong>Письмо:</strong> <span onclick='if (document.getElementById(\"letter_content\").style.display==\"\") document.getElementById(\"letter_content\").style.display=\"none\"; else document.getElementById(\"letter_content\").style.display=\"\";' style='margin: 0 0 0 20;cursor:pointer; border-bottom:dotted 1px #999; color:#777;'>Отобразить / Скрыть</span></td></tr>";
echo "<tr><td><div id=letter_content style='display:none;'>".$content."</div></td></tr>";
echo "</table>";
echo "</div>";





echo "<div style='padding: 20 0 10 0;'><strong>Получатели:</strong></div>";

echo "  <div style='padding: 5 0 5 0;'>
            <a href='?id=".$_REQUEST['id']."&action=subscribers_add_users_save'>Добавить получателей из базы пользователей</a>&nbsp; &nbsp;&nbsp;
            &nbsp; &nbsp;&nbsp;<a href='?id=".$_REQUEST['id']."&action=subscribers_del_users'>Удалить получателей</a>
        </div>";



$sql_query="SELECT id, dt_send, name, email FROM ".$sql_pref."_mail2_subscribers ORDER BY name";
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




$sql_query="SELECT id FROM ".$sql_pref."_mail2_subscribers WHERE dt_send='0000-00-00 00:00:00'";
$sql_res=mysql_query($sql_query, $conn_id);
if (mysql_num_rows($sql_res)>0)
{
    echo "<div style='padding: 20 0 20 0;'><a href='send.html'>Разослать!</a></div>";
}
    


?>
