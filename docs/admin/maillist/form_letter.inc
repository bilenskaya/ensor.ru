<?

if (isset($_REQUEST['action']) && $_REQUEST['action']=="letter_add")
{
	$title="����� ������";
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
	$title="�������������� (id: ".$_REQUEST['id'].")";
}

if (isset($_REQUEST['action']) && $_REQUEST['action']=="news_mail_gen")
{
    //require_once("../../fns/common.php");
    $delT=7;
    
    $str_add.="\n\n����� ������������ � ����� �����������, ��������� �� ������: http://www.ensor.ru/\n";
    $str_add.="���� ��������� ���� ������ �� �����������, ���������� �� � ����� ������, �������� � �������� ������ �������� � ������� ����.\n\n";
    $str_add.="�� �������� ��� ������, ������ ��� ���������������� �� ����� www.ensor.ru.\n\n";
    $str_add.="--\n� ���������,\n������ ��������� www.ensor.ru.\n";
    $str_add.="--------------------------------------------------------------";
    
    //������� �������������, ������� ��������� �� ��������
    $sql_query="SELECT id, dt_get_mail, last_visit FROM ".$sql_pref."_users WHERE enable='Yes' AND maillist='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
    //echo $sql_query;
	while (list($id, $dt_get_mail, $last_visit)=mysql_fetch_row($sql_res))
	{
        // ���� ����� ���������� ������ �� ���������� 
        if (date("d.m.Y", strtotime($last_visit))==date("d.m.Y",strtotime("01.01.1970")))
        {
		      if($dt_get_mail!="") $delT=round((time()-strtotime($dt_get_mail))/(60*60*24),0)-1; else $delT=7;
        }
        // ���� ����� ���������� ������ ����������
        else
        { 
            // ��������� ������� ������� ������ � ���������� ���������
            $delT=round((time()-(strtotime($last_visit)))/(60*60*24),0)-1;
            // ���� ����� ��������� �������� �� ���� � ��������� �������� ������� ���������� ������, �� ������=������� �����-���� ��������� ��������
            if($dt_get_mail!=""&&(strtotime($dt_get_mail)>=strtotime($last_visit))) $delT=round((time()-strtotime($dt_get_mail))/(60*60*24),0)-1;
            
        }
        if($delT>7) $delT=7;
	    // ����� �� ������ �� �������� ������ �� �������        
        $str_blog_act=get_blog_activity($delT,"for_mail");
        // ����� �� ������� � ����������
        $str_article_act=get_article_activity($delT,"for_mail");
        // ����� �� �������� � �������������
        $str_news_act=get_news_activity($delT,"for_mail");

                    
        if($str_blog_act!=""||$str_article_act!=""||$str_news_act!="")
        {
            // �������� ����� ���������
            $str_common=$str_blog_act."\n\n".$str_article_act."\n\n".$str_news_act.$str_add;
            $result=send_mail_to_user($id,"www.ensor.ru: ����� ���������� ����� Ensor.ru",$str_common);
            if($result!="������")
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
    echo "����!!!";
    $str_add.="\n\n����� ������������ � ����� �����������, ��������� �� ������: http://www.ensor.ru/\n";
    $str_add.="���� ��������� ���� ������ �� �����������, ���������� �� � ����� ������, �������� � �������� ������ �������� � ������� ����.\n\n";
    $str_add.="�� �������� ��� ������, ������ ��� ���������������� �� ����� www.ensor.ru.\n\n";
    $str_add.="--\n� ���������,\n������ ��������� www.ensor.ru.\n";
    $str_add.="--------------------------------------------------------------";
    
    //������� �������������, ������� ��������� �� ��������
    $sql_query="SELECT id, dt_get_mail, last_visit FROM ".$sql_pref."_users WHERE enable='Yes' AND maillist='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
    //echo $sql_query;
	while (list($id, $dt_get_mail, $last_visit)=mysql_fetch_row($sql_res))
	{
        // ���� ����� ���������� ������ �� ���������� 
        if (date("d.m.Y", strtotime($last_visit))==date("d.m.Y",strtotime("01.01.1970")))
        {
		      if($dt_get_mail!="") $delT=round((time()-strtotime($dt_get_mail))/(60*60*24),0)-1; else $delT=7;
        }
        // ���� ����� ���������� ������ ����������
        else
        { 
            // ��������� ������� ������� ������ � ���������� ���������
            $delT=round((time()-(strtotime($last_visit)))/(60*60*24),0)-1;
            // ���� ����� ��������� �������� �� ���� � ��������� �������� ������� ���������� ������, �� ������=������� �����-���� ��������� ��������
            if($dt_get_mail!=""&&(strtotime($dt_get_mail)>=strtotime($last_visit))) $delT=round((time()-strtotime($dt_get_mail))/(60*60*24),0)-1;
            
        }
        if($delT>7) $delT=7;
	    // ����� �� ������ �� �������� ������ �� �������        
        $str_blog_act=get_blog_activity($delT,"for_mail");
        // ����� �� ������� � ����������
        $str_article_act=get_article_activity($delT,"for_mail");
        // ����� �� �������� � �������������
        $str_news_act=get_news_activity($delT,"for_mail");

        
        if($str_blog_act!=""||$str_article_act!=""||$str_news_act!="")
        {
            // �������� ����� ���������
            $str_common=$str_blog_act."\n\n".$str_article_act."\n\n".$str_news_act.$str_add;
        //    $result=send_mail_to_user($id,"����� ���������� ����� Ensor.ru",$str_common);
        //    if($result!="������")
        //    {
        //        $sql_query2="UPDATE ".$sql_pref."_users SET dt_get_mail='".date("Y-m-d")."' WHERE id='".$id."'";
        //        $sql_res2=mysql_query($sql_query2, $conn_id);
        //    }
            echo "������������ [".$id." ��������: ".$dt_get_mail." ����.�����:".$last_visit." ������: ".$delT."]<br/>�����:".$str_common."<BR/><HR>";
            //$content="������������ [".$id."]<br/>�����:".$str_common."<BR/><HR>";
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
		<td class="form_left">��������� �����</td>
		<td class="form_main">
                <select name="mail_from_id" id="mail_from_id">
				<option value="0">��� ������</option>
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
		<td class="form_left">����</td>
		<td class="form_main"><input class="form" type="text" id="name" name="name" value='<?php if (isset($name)) echo $name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">������</td>
		<td class="form_main"><?php require_once("../vis/visual.php"); ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class="form_button" type="submit" name="button_submit" value="���������"></td>
	</tr>
</table>
</form>