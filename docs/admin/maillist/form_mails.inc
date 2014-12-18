<h2>Добавление исходящего адреса</h2>


<?

echo "<div style='padding: 20 0 10 0;'><strong>Е-мэйлы:</strong></div>";

echo "  <div id=subscriber_add style='padding: 10 0 5 0;'>
            <form name=form_email_add action='index.html' method=post>
                <input type=hidden name=action value=mails_add_form_save>
                Email: <input class=form type=text id=email name=email value='' style='width:150px;'> &nbsp; &nbsp;
                <input class=form_button type=submit name=button_submit value='Добавить'>
            </form>
        </div>";



$sql_query="SELECT id, email FROM ".$sql_pref."_mail_address ORDER BY email";
$sql_res=mysql_query($sql_query, $conn_id);
if (mysql_num_rows($sql_res)>0)
{
    $i=0;
    echo "<div style='padding: 15 0 5 0;'>";
    echo "<table cellpadding=5 cellspacing=0 border=0>";
    echo "<tr>";
    echo "<td align=center style='border-bottom: solid 1px #999;'><strong>№</strong></td>";
    echo "<td align=center style='border-bottom: solid 1px #999;'><strong>Email</strong></td>";
    echo "<td align=center style='border-bottom: solid 1px #999;'><strong>Удалить</strong></td>";
    echo "</tr>";
    
    
    while(list($id, $email)=mysql_fetch_row($sql_res))
    {
        $i++;       
        $del_pic="<a href='?mail_id=".$id."&action=mails_delete'><img src='/admin/img/del.gif' width='25' height='13' alt='Удалить' border='0'></a>";
        echo "<tr>";
        echo "<td align=center>".$i."</td>";
        echo "<td align=center>".$email."</td>";
        echo "<td align=center>".$del_pic."</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
}

?>
