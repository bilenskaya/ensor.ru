<?
if (isset($_REQUEST['action']))
{
    if ($_REQUEST['action']=="note_save")  note_show();
}


function note_show()
{
    global $page_header1, $out, $art_url, $path_www, $sql_pref, $user_id, $conn_id, $user_note_show, $user_note;  
   	
    if (isset($_REQUEST['submit']))
	{
	    if (isset($_REQUEST['note']) AND !empty($_REQUEST['note'])) $note=addslashes($_REQUEST['note']); else $note="";
		    	
		if (!empty($note))
		{
			$sql_query="INSERT INTO ".$sql_pref."_users (note) VALUES ('".$note."')";
			$sql_res=mysql_query($sql_query, $conn_id);
		    $group_id=mysql_insert_id();
            exit();
		}		
	}   
       
    $out.="
	".@$error_info."
	<form action='' method=post name=note_save enctype='multipart/form-data'>
            <div>Заметки:</div>
			<div>
                <textarea name=note id=note rows=12 style='width:550px;font-size:14px;'>".@$user_note."</textarea>
            </div>
    		<div style='padding: 10 0 10 0;'>
    			<div><input type=Submit name=submit value=Сохранить style='font-size: 14px; width:150px; background-color: #eeeeee; color: #555555; border: 1px #555555 solid;'></div>
    		</div>
	</form>";
	return ($out);    
}

?>