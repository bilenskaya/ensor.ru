<?php


function questions_answers_main()
{
	global $sql_pref, $conn_id, $art_url;
	$out="";
    
    if (isset($_REQUEST['action']))
    {
        //echo $_REQUEST['action'];
        if ($_REQUEST['action']=="add_vote")  questions_answers_add_vote();
        if ($_REQUEST['action']=="add_question_to_base") add_question_to_base();
        if ($_REQUEST['action']=="answer_add")  questions_answer_form_save();
        if ($_REQUEST['action']=="comment_del")  questions_comments_del();
        if ($_REQUEST['action']=="question_del")  question_del();
        if ($_REQUEST['action']=="question_change_form")  $out.=question_change_form(); 
        if ($_REQUEST['action']=="question_change")  question_change();    
    }
    
    //echo "lghjh".$art_url;
	if (isset($art_url)) $out.=questions_answers_out();
    elseif (isset($_REQUEST['action']) &&  $_REQUEST['action']=="add_question") $out.=questions_answers_add_form();
	else $out.=questions_answers_list();
	
	return ($out);
}










function questions_answers_list()       // список всех вопросов
{
	global $sql_pref, $conn_id, $path, $path_users, $path_questions, $questions_answers_perpage, $user_id, $user_status;
	$out="<table cellpadding=2 cellspacing=2 border=0 width=100%>
						<tr>
                            <td width=5%> </td>
                            <td width=15%> </td>
                            <td width=30%> </td>
                            <td width=20%> </td>
                            <td width=30%> </td>
						</tr>";
	
	if (isset($_REQUEST['page'])&& $_REQUEST['page']>0) $page=$_REQUEST['page']; else $page=1;
	$perpage=$questions_answers_perpage; $first=$perpage*($page-1);
	$sql_query="SELECT id FROM ".$sql_pref."_questions WHERE enable='Yes'";
	$pages_show="<div align=left style='padding: 20 0 10 0;'>".pages_nums($page, $perpage, $sql_query)."</div>";

	$sql_query="SELECT id, dt, question, user_id, question_type FROM ".$sql_pref."_questions WHERE enable='Yes' ORDER BY dt DESC, id DESC LIMIT ".$first.",".$perpage;
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		while(list($id, $dt, $question, $p_user_id, $question_type)=mysql_fetch_row($sql_res))
		{
			$question=stripslashes($question);$question=str_replace("\n", "<br>", $question);
			
			$dt_show="<div style='padding: 3 0;'><span style='padding: 2 4;background-color:#eee;'>".date("d.m.Y H:i:s", strtotime($dt))."</span></div>";
            $question_show="<div style='padding: 3 0;'>".substr($question,0,50)."...</div>";
            if ($question_type==2) $results='результаты опроса'; else $results='обсуждение';
            $more_show="<div style='padding: 3 0;'><a href='/".$path_questions."/".$id.".html'>Посмотреть ".$results."...</a></div>";
            $question_show="<div style='padding: 3 0;'><a href='/".$path_questions."/".$id.".html'>".substr($question,0,50)."...</a></div>";
            $sql_query="SELECT name, surname FROM ".$sql_pref."_users WHERE id='".$p_user_id."'";
        	$sql_res_1=mysql_query($sql_query, $conn_id);
        	list($user_name, $user_surname)=mysql_fetch_row($sql_res_1);
          
            $p_user_name=$user_name." ".$user_surname;
            //$p_user_show="<div style='padding: 3 0;font-size:18px;'>".$p_user_name."</div>";
            $p_user_show="<a href='/".$path_users."/".$p_user_id.".html' style='font-weight:bold;'>".$p_user_name."</a>";

            if ($user_status=="admin") //if ($p_user_id==@$user_id || $user_status=="admin") 
            { 
                $del_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_questions."/del".$id.".html?action=question_del&question_id=".$id."'\"  style='font-size:9px;color:#999999;'>Удалить</a>";
                $change_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_questions."/change".$id.".html?action=question_change_form&question_id=".$id."'\"  style='font-size:9px;color:#999999;'>Править</a>";
            }
            else
            {
                $del_but="";
                $change_but="";
            }
            if ($question_type==1) $img_view="<img src='/img/question.png' alt='Вопрос'>"; else $img_view="<img src='/img/query.png' border=0 width=20px height=20px alt='Опрос'>";
			
			$out.="     <tr><td></br></td><td></br></td><td></td><td></td><td></td></tr>	
                        <tr>
                            <td valign=middle>".$img_view."</td>
                            <td valign=middle>".$dt_show."</td>
                            <td valign=middle>".$question_show."</td>
                            <td valign=middle><b>".$p_user_show."</b></td>
                            <td valign=middle>".$del_but.$change_but."</td>
						</tr>
                        <tr><td></br></td><td></br></td><td></td><td></td><td></td></tr>";

		}
		
	}
	$out.= "</table>";
    if ($user_id==0) $add_link=""; else $add_link="<div style='padding:50 0 20 0;'><a href='/".$path_questions."?action=add_question'>Добавить вопрос...</a></div>";
	$out.=$pages_show;
    $out.=$add_link;
	
	return ($out);
}











function questions_answers_out()    // информация по конкретному вопросу
{
	global $sql_pref, $conn_id, $path, $art_url, $path_questions, $path_users, $path_companies;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
	
 	$sql_query="SELECT id, dt, question, user_id, question_type FROM ".$sql_pref."_questions WHERE id='".$art_url."' AND enable='Yes'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $dt, $question, $p_user_id, $question_type)=mysql_fetch_row($sql_res);
	    //echo $question_type;
        if ($question_type==2) $result=votes_out($id);
           
		$question=stripslashes($question); $question=stripslashes($question);
        $question=str_replace("\n", "<br>", $question);
		
		$dt_show=date("d.m.Y H:i:s", strtotime($dt));
        if (!empty($question)) $question_show="<div style='padding: 3 0;'><h3>".$question."</h3></div>"; else $question_show="<div style='padding: 3 0;'>".$question."</div>";
        
        $sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$p_user_id."'";
    	$sql_res_1=mysql_query($sql_query, $conn_id);
    	list($p_user_name)=mysql_fetch_row($sql_res_1);

         
        $p_user_show="<div style='padding: 3 0;font-size:18px;'>".$p_user_name."</div>";
           
           
        
        
        $addinfo="<div style='padding: 10 0 0 0;font-weight:bold;'>Дополнительно:</div>";
        $addinfo.="<ul>";
        $addinfo.="<li>Профиль пользователя <a href='/".$path_users."/".$p_user_id.".html'>".$p_user_name."</a> на нашем сайте</li>";
        if ($company_id>0) $addinfo.="<li>Профиль компании <a href='/".$path_companies."/".$company_id.".html'>".$company_name."</a> на нашем сайте</li>";
        $addinfo.="</ul>";
        
        
        
        
        
		$out.="<div style='padding: 5 0 10 0;'>";
		
		$out.=" <table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<td valign=top><h2>".$dt_show.$question_show."</h2></td>
					</tr>
				</table>";
		
        $out.=$result.$addinfo;
		$out.="</div>";
    
    
        //$out.="<div style='padding: 30 0 10 0;'>".questions_answers_feedback($id)."</div>";
        
		$out.="<div style='padding:5 0 20 0;'><a href='/".$path_questions."/'>К списку вопросов...</a></div>";
        $out.= questions_comments($id, $question_type);
	}
	return ($out);
}


function questions_line()       // опрос на главную страницу
{
	global $sql_pref, $conn_id, $path, $art_url, $path_questions, $path_users, $path_companies;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
 	$sql_query="SELECT id, dt, question, user_id, question_type FROM ".$sql_pref."_questions WHERE (enable='Yes' AND question_type=2) ORDER BY dt DESC LIMIT 1 ";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
    	$out.="<div style='padding:5 0 20 0;'>";
    	//$out.="<div style='font-size:14px;font-weight:normal;'>Новый опрос:</div>";
        
        
		list($id, $dt, $question, $p_user_id, $question_type)=mysql_fetch_row($sql_res);
	    //echo $question_type;
        $result=votes_out($id);
           
		$question=stripslashes($question); $question=stripslashes($question);
        $question=str_replace("\n", "<br>", $question);
		
		$dt_show=date("d.m.Y H:i:s", strtotime($dt));
        
        $sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$p_user_id."'";
    	$sql_res_1=mysql_query($sql_query, $conn_id);
    	list($p_user_name)=mysql_fetch_row($sql_res_1);

         
        $p_user_show="<div style='padding: 3 0;font-size:18px;'>".$p_user_name."</div>";
        
        
        
        
        $out.="<div style='font-size:14px;font-weight:bold;padding:0 0 5 0;'>Примите участие в опросе энергетиков:</div>";
        $out.="<div style='font-size:14px;font-weight:normal;padding:0 0 0 0;'>".$question."</div>";           
                
        $out.=$result;
        
       	$out.="<div><a href='/".$path_questions."/'>Все опросы</a></div>";
       	$out.="</div>";
    }
	return ($out);
}






/*
function questions_line()       // опрос на главную страницу
{
	global $sql_pref, $conn_id, $path, $art_url, $path_questions, $path_users, $path_companies;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="";
    $main="<div style='margin:0 0 5 0;color:#026380;font-size:18px;'>Опросы</div>";
 	$sql_query="SELECT id, dt, question, user_id, question_type FROM ".$sql_pref."_questions WHERE (enable='Yes' AND question_type=2) ORDER BY dt DESC LIMIT 1 ";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
        $out.=$main;
		list($id, $dt, $question, $p_user_id, $question_type)=mysql_fetch_row($sql_res);
	    //echo $question_type;
        $result=votes_out($id);
           
		$question=stripslashes($question); $question=stripslashes($question);
        $question=str_replace("\n", "<br>", $question);
		
		$dt_show=date("d.m.Y H:i:s", strtotime($dt));
        if (!empty($question)) $question_show="<div style='padding: 3 0;'><h3>".$question."</h3></div>"; else $question_show="<div style='padding: 3 0;'>".$question."</div>";
        
        $sql_query="SELECT CONCAT_WS(' ', name, surname) FROM ".$sql_pref."_users WHERE id='".$p_user_id."'";
    	$sql_res_1=mysql_query($sql_query, $conn_id);
    	list($p_user_name)=mysql_fetch_row($sql_res_1);

         
        $p_user_show="<div style='padding: 3 0;font-size:18px;'>".$p_user_name."</div>";
           
                
		$out.="<div style='padding: 5 0 10 0;'>";
		
		$out.=" <table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<td valign=top><h2>".$question_show."</h2></td>
						<td valign=top align=center width=100>".$img_show."</td>
					</tr>
				</table>";		
        $out.=$result;
       	$out.="<div><a href='/".$path_questions."/'>Все опросы</a></div>";
       	$out.="</div>";
    }
	return ($out);
}
*/







function votes_out($question_id)        // результаты голосования
{
	global $sql_pref, $conn_id, $path, $art_url, $path_questions, $path_users, $path_companies;
	global $page_title, $page_header1;
	global $module_name, $module_url;
	$out="<form name='form_name' method='post' style='padding:0;margin:0;'><input type='hidden' name='action' value='add_vote'><table cellpadding=2 cellspacing=0 border=0>";
	
 	$sql_query="SELECT id, vote_text, vote_count, question_id FROM ".$sql_pref."_questions_votes WHERE question_id='".$question_id."' ORDER BY id";
    $sql_query2="SELECT SUM( vote_count) FROM ".$sql_pref."_questions_votes WHERE question_id='".$question_id."'";

	$sql_res=mysql_query($sql_query, $conn_id);
    $sql_res2=mysql_query($sql_query2, $conn_id);
    list($sum_votes)= mysql_fetch_row($sql_res2);
    //echo "ВОТ ТУТ!!!".$sql_query;
    
	if (mysql_num_rows($sql_res)>0)
	{
        while(list($id, $vote_text, $vote_count, $question_id)=mysql_fetch_row($sql_res))
		{
    		$vote_text=stripslashes($vote_text);
            $vote_text=str_replace("\n", "<br>", $vote_text);
    	        
            
            if (!empty($vote_text)) $vote_text_show="<div style='padding: 3 0;'>".$vote_text."</div>"; else $vote_text_show="<div style='padding: 3 0;'>".$vote_text."</div>";
            //$vote_count_show="<div style='padding: 3 0;'>".$vote_count."</div>";        
            if ($vote_count!=0) $img_w=80*$vote_count/$sum_votes; else $img_w=1;
            if ($vote_count!=0) $vote_per=round((100*$vote_count/$sum_votes),1); else $vote_per=0;            
    		$out.="	<tr>
                        <td valign=top><input type='radio' name='vote' value=".$id.">
                                       <input type='hidden' name='question_id' value='".$question_id."'></td> 
						<td valign=top width=65%>".$vote_text_show."</td>
						<td valign=top align=left><img src='/img/vote.png' width='".$img_w."' height='8'> ".$vote_per."%</td>
                       	<td valign=top align=right></td>
					</tr>";	
        }
        $out.="</table>        
        <div style='padding: 5 0;'><input class='form_button' type='submit' name='button_submit' value='Голосовать'></div></form>";
	}
	return ($out);
}


function questions_answers_add_vote()       // сохранение голоса
{    
    global $sql_pref, $conn_id, $path, $page_header1, $path_questions, $user_id, $art_url;
	
	if (isset($_REQUEST['vote']) && !empty($_REQUEST['vote']))
	{
        $vote=$_REQUEST['vote'];
		$dt=date("Y-m-d H:i:s");
        //echo $vote;
        $parent_id=$_REQUEST['question_id'];
	
		$sql_query="UPDATE ".$sql_pref."_questions_votes SET vote_count=(vote_count+1) WHERE (question_id=".$parent_id." AND id=".$vote." AND last_ip NOT LIKE '".$_SERVER['REMOTE_ADDR']."')";
        $sql_res=mysql_query($sql_query, $conn_id);
        
        $sql_query1="UPDATE ".$sql_pref."_questions_votes SET last_ip='".$_SERVER['REMOTE_ADDR']."' WHERE question_id=".$parent_id."";
        $sql_res=mysql_query($sql_query1, $conn_id);
		//echo $sql_query;
        //exit();
	}
	return;
}












function questions_answers_add_form()           // форма добавления вопроса
{
    global $sql_pref, $conn_id, $path, $art_url, $path_questions, $path_users, $path_companies;
	global $page_title, $page_header1;
	global $module_name, $module_url;
    $out="
    <SCRIPT>
        function display_div(src_id)
        {
            switch (src_id) 
            {
                case 1:
            	
                    obj_div=document.getElementById('div_1');  
            		obj_div.style.display='inline';	
                    obj_div=document.getElementById('div_2');  
            		obj_div.style.display='inline';   
            	    break;
                
                case 10:
                    for(i=1;i<=6;i++)
                    {
            		  obj_div=document.getElementById('div_'+i);  
            		  obj_div.style.display='none';
                    }	 
                    break;
                    
                default:
                    // проверка наличия текста в предыдущем варианте
                    s1=src_id-2;
                    obj_div=document.getElementById('answer_'+s1);  
            		if (obj_div.value!='')
            		{
            		  obj_div=document.getElementById('div_'+src_id);  
            		  obj_div.style.display='inline';
                    }
                    else
                    {
                      alert('Прежде чем добавлять очередной вариант ответа - заполните, пожалуйста, предыдущий вариант.');
                    }
                    break;	 
            }
            return;
        }
        
        </SCRIPT>
    ";
	$out.="<form name='form_name' method='post'>
            <input type='hidden' name='action' value='add_question_to_base'>
            <table cellpadding=0 cellspacing=0 border=0 width=100%>	
            	<tr>
            		<td class='form_left'>Вопрос</td>
            		<td class='form_main'><textarea id=question name=question rows=4 style='overflow: auto; font-size: 12px;width:500px;'></textarea></td>
            	</tr>
           	    <tr>
            		<td class='form_left'>Тип вопроса</td>
            		<td class='form_main'><div><input type='radio' onclick='display_div(10)' name='question_type' checked='yes' value='1'> - вопрос &nbsp; &nbsp; &nbsp; <input type='radio' onclick='display_div(1)' name='question_type' value='2'> - опрос</div></td>
            	</tr>  
       	        <tr>
            		<td class='form_left'></br></td>
            		<td class='form_main'></td>
            	</tr>      	    
                   <tr style='DISPLAY: none;' id='div_1'>
            		  <td class='form_left'>Варианты ответа</td>
            		  <td class='form_main'></td>
            	   </tr>
                   <tr style='DISPLAY: none;' id='div_2'>
            		  <td class='form_left'>Вариант 1</td>
            		  <td class='form_main'><textarea name='answer1' id='answer_1' rows=2 style='overflow: auto; font-size: 12px;width:500px;'></textarea><input type='button' name='button_1' onclick='display_div(3)' value='...'></td>
            	   </tr>
                   <tr style='DISPLAY: none;' id='div_3'>
            		  <td class='form_left'>Вариант 2</td>
            		  <td class='form_main'><textarea name='answer2' id='answer_2' rows=2 style='overflow: auto; font-size: 12px;width:500px;'></textarea><input type='button' name='button_2' onclick='display_div(4)' value='...'></td>
            	   </tr>
                   <tr style='DISPLAY: none;' id='div_4'>
            		  <td class='form_left'>Вариант 3</td>
            		  <td class='form_main'><textarea name='answer3' id='answer_3' rows=2 style='overflow: auto; font-size: 12px;width:500px;'></textarea><input type='button' name='button_3' onclick='display_div(5)' value='...'></td>
            	   </tr>
                   <tr style='DISPLAY: none;' id='div_5'>
            		  <td class='form_left'>Вариант 4</td>
            		  <td class='form_main'><textarea name='answer4' id='answer_4' rows=2 style='overflow: auto; font-size: 12px;width:500px;'></textarea><input type='button' name='button_4' onclick='display_div(6)' value='...'></td>
            	   </tr>  
                   <tr style='DISPLAY: none;' id='div_6'>
            		  <td class='form_left'>Вариант 5</td>
            		  <td class='form_main'><textarea name='answer5' id='answer_5' rows=2 style='overflow: auto; font-size: 12px;width:500px;'></textarea></td>
            	   </tr>                                     
                </div>
            </table>
            <input type='hidden' name='question_id' value='".$question_id."'>
            <input class='form_button' type='submit' name='button_submit' value='Добавить вопрос'>
           </form>";
	return ($out);    
}


function add_question_to_base()     // добавление вопроса в БД
{
	global $sql_pref, $conn_id, $user_id;
	if (isset($_REQUEST['question'])) $question=$_REQUEST['question']; else $question="";
	$question=htmlspecialchars($question, ENT_QUOTES); $question=addslashes($question);
    if (isset($_REQUEST['question_type'])) $question_type=$_REQUEST['question_type']; else $question_type=1;
       
    $sql_query="INSERT INTO ".$sql_pref."_questions (question, question_type, user_id) VALUES ('".$question."', '".$question_type."','".$user_id."')";
    $sql_res=mysql_query($sql_query, $conn_id);
    $question_id=mysql_insert_id();
    //echo $question_id;

    if ($question_type=2) 
    {
        for($k=1; $k<=5; $k++)
        {
            if (isset($_REQUEST["answer".$k])&& $_REQUEST["answer".$k]!="") 
            {
                $answer=$_REQUEST["answer".$k];
                $sql_query="INSERT INTO ".$sql_pref."_questions_votes (question_id, vote_text) VALUES ('".$question_id."', '".$answer."')";
                $sql_res=mysql_query($sql_query, $conn_id);
            }
            else{ echo $_REQUEST["answer".$k];}
        }
    }
    //echo $sql_query;	
}







function questions_comments($parent_id, $question_type)         // вывод ответов и комментов
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_questions, $months_rus1, $user_id, $user_status, $path_users;
	
    if ($question_type==1) $type="Ответы"; else $type="Комментарии";
    
    $out="";
	$out.="<a name=comments></a>";
    $out.="<div style='padding: 25 0 15 0;'>";
	$out.="<h2 style='margin: 3 0 3 0;font-size:18px;'>".$type."</h2>\n";
	$out.="<table cellpadding=0 cellspacing=0 border=0 width=100% height=1><tr height=1><td height=1 align=center bgcolor=#999999 background='/img/dots-hor.gif'><img src='/img/empty.gif' height=1 border=0></td></tr></table>\n";
	$out.="<div style='padding: 0 0 15 0;'>";
	$sql_query="SELECT id, content, user_id, dt FROM ".$sql_pref."_questions_answers WHERE question_id='".$parent_id."' ORDER BY dt";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		$comments_num=mysql_num_rows($sql_res);
//		$out.="<div style='margin: 3 0 3 0;'>Всего: <b>".$comments_num."</b></div><br>\n";
		while(list($id, $content, $commentator_id, $dt)=mysql_fetch_row($sql_res))
		{
            $content=StripSlashes($content);
            
			$sql_query="SELECT name, surname FROM ".$sql_pref."_users WHERE id='".$commentator_id."'";
			$sql_res_1=mysql_query($sql_query, $conn_id);
			list($name, $surname)=mysql_fetch_row($sql_res_1);
			
			$name=StripSlashes($name); $surname=StripSlashes($surname);
			$content=str_replace("\n","<br>",$content);
			$content=preg_replace("#(?<!=)(?<!\")(?<!\')(https?|ftp)://\S+[^\s.,>)\];'\"!?]#",'<a href="\\0">\\0</a>',$content);
			$date_show=date("d.m.Y H:i", strtotime($dt));
			$name_show="<a href='/".$path_users."/".$commentator_id.".html'>".$name." ".$surname."</a>";
			
			if ($user_status=="admin") $del_but=" <a href=\"javascript:if(confirm('Вы уверены?'))window.location='/".$path_questions."/".$parent_id.".html?action=comment_del&comment_id=".$id."'\"  style='font-size:9px;color:#999999;'>Удалить</a>"; else $del_but="";
			
			$out.="<div style='margin: 5 0 5 0;'><span style='font-size:14px;font-weight:normal;'>".$name_show."</span><br><span style='color:#999999;font-size:11px;'> ".$date_show."</span>".$del_but."</div>";
			$out.="<div style='margin: 5 0 5 20;'>".$content."</div>";
			$out.="<br>";
		}
	}
	else $out.="<div style='margin: 5 0 5 0;'>Пока нет.</div>\n";
	$out.="</div>";
	$out.=questions_comments_form($parent_id, $question_type);
    $out.="</div>";
	return ($out);
}







function questions_comments_form($parent_id, $question_type)        // форма добавления ответов и комментов
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_questions, $user_id;
	$out="";
    if ($question_type==1) $type="ответ"; else $type="комментарий";
        
	if ($user_id==0) return ("<div>Обратная связь доступна только для <a href='/auth/register/'>зарегистрированных</a> пользователей</div>");
	
	$out.="<h2 style='margin: 3 0 3 0;'>Ваш ".$type."</h2>\n";
	$out.="<script language='Javascript'>
		function check_form()
		{
			var str = 'OK';
			if (document.getElementById('content1').value=='') str='ОТВЕТ';
			return str;
		}
		</script>";
	$out.="<form action='' method='post' name='form_comments' onSubmit='if (check_form()!=\"OK\") {alert(\"Вы не заполнили поле \" + check_form() + \"!\"); return false;} else return true;'>";


	$out.="<div>
					<textarea id=content1 name=content1 rows=4 style='overflow: auto; font-size: 12px;width:500px;'>".@$content."</textarea>
				 </div>";
	$out.="<span style='color:#777777;font-size:11px;'><br>Просьба оставлять ".$type." только по теме!</span><br><br>";
	$out.="<div><input type=hidden name=action value='answer_add'>";
	$out.="<input class='button' type='submit' value='Отправить' name='add' style='padding: 2 2 2 2; font-size: 10px; font-weight: bold; background-color: transparent; color: #3E3E3E; border: 1px solid #CCCCCC;'></div>";
	$out.="</form>";
	$out.="";
	$out.="<br><br>";
	return ($out);
}




function questions_answer_form_save()           // сохранение ответа
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_questions, $user_id, $art_url, $user_rate_main, $user_rate_sec;
	//echo "location:/".$path_questions."/".$art_url.".html#comments";
	if (isset($_REQUEST['content1']) && !empty($_REQUEST['content1']))
	{
		$dt=date("Y-m-d H:i:s");
        $parent_id=$art_url;
		
		if (isset($_REQUEST['content1'])) $content=AddSlashes(strip_tags($_REQUEST['content1'], '<br>, <b>, <i>, <u>')); else $content="";
		
		$sql_query="INSERT INTO ".$sql_pref."_questions_answers (content, user_id, question_id, dt) VALUES ('".$content."', '".$user_id."', '".$parent_id."', '".$dt."')";
		$sql_res=mysql_query($sql_query, $conn_id);
        rate_main($user_id, "добавил комментарий", $user_rate_main, $user_rate_sec);
        $answer_id=mysql_insert_id();
        //echo $sql_query;
        if($answer_id>0)
        {
            $sql_query="SELECT ".$sql_pref."_questions.id, ".$sql_pref."_users.id, ".$sql_pref."_users.surname, ".$sql_pref."_users.name, ".$sql_pref."_users.name2, ".$sql_pref."_users.email
                         FROM ".$sql_pref."_questions INNER JOIN ".$sql_pref."_users ON ".$sql_pref."_questions.user_id=".$sql_pref."_users.id  WHERE ".$sql_pref."_questions.id=".$parent_id;
            $sql_res=mysql_query($sql_query, $conn_id);
            //echo $sql_query;
            if (mysql_num_rows($sql_res)>0)
            {
                list($quest_id, $id_from, $surname_from, $name_from, $name2_from, $email_from)=mysql_fetch_row($sql_res);
                
                $sql_query2="SELECT ".$sql_pref."_users.id, ".$sql_pref."_users.surname, ".$sql_pref."_users.name, ".$sql_pref."_users.name2, ".$sql_pref."_users.email
                         FROM ".$sql_pref."_users WHERE ".$sql_pref."_users.id=".$user_id;
                $sql_res2=mysql_query($sql_query2, $conn_id);
                list($id_from2, $surname_from2, $name_from2, $name2_from2, $email_from2)=mysql_fetch_row($sql_res2);
                
                $name_from2=$surname_from2." ".$name_from2." ".$name2_from2;
                if($id_from!=$user_id)
                {
                    $letter_name="www.ensor.ru: Вам поступил ответ на вопрос от пользователя ".$name_from2.".";
                    $letter_content_send="Вам поступил ответ на опубликованный Вами вопрос от пользователя ".$name_from2.".\n";                            
                    $letter_content_send.="Чтобы просмотреть ответ, перейдите по ссылке: http://www.ensor.ru/discussions/questions_answers/".$quest_id.".html#comments .\n";
                    $letter_content_send.="Если указанная выше ссылка не открывается, скопируйте ее в буфер обмена, вставьте в адресную строку браузера и нажмите ввод.\n\n";
                    $letter_content_send.="Вы получили это письмо, потому что зарегистрированы на сайте www.ensor.ru.\n\n";
                    $letter_content_send.="--\nС уважением,\nСлужба поддержки www.ensor.ru.\n";
                    $letter_content_send.="--------------------------------------------------------------";
                    send_mail_to_user($id_from, $letter_name, $letter_content_send);  
                }
             }                  
        }  
        
        
		header("location:/".$path_questions."/".$parent_id.".html#comments"); 
        exit();
	}
	return;
}



function questions_comments_del()               // удаление ответа
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_questions, $user_id, $user_status, $art_url;
	//if (@$user_id>0 && ($p_user_id==@$user_id || $user_status=="admin"))
    //{
        $out="";
    	
    	if (!isset($_REQUEST['comment_id']) || ($_REQUEST['comment_id']<=0)) return;
        
    	$sql_query="SELECT id FROM ".$sql_pref."_questions_answers WHERE id='".$_REQUEST['comment_id']."'&&user_id='".$user_id."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    	if (mysql_num_rows($sql_res)==0 && $user_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
    	
    	
    	$sql_query="DELETE FROM ".$sql_pref."_questions_answers WHERE id='".$_REQUEST['comment_id']."'";
    	$sql_res=mysql_query($sql_query, $conn_id);
    
    	header("location:/".$path_questions."/".$art_url.".html#comments"); exit();
     //}
     //else exit();
}

function question_del()                 // удаление вопроса
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_questions, $user_id, $user_status, $art_url;
	$out="";
	
	if (!isset($_REQUEST['question_id']) || ($_REQUEST['question_id']<=0)) return;
        
	$sql_query="SELECT id FROM ".$sql_pref."_questions WHERE id='".$_REQUEST['question_id']."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0 && $user_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
    	
    
	$sql_query="SELECT id FROM ".$sql_pref."_questions WHERE id='".$_REQUEST['question_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0 && $user_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
	

	$sql_query="DELETE FROM ".$sql_pref."_questions WHERE id='".$_REQUEST['question_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	
	$sql_query1="DELETE FROM ".$sql_pref."_questions_answers WHERE question_id='".$_REQUEST['question_id']."'";
	$sql_res=mysql_query($sql_query1, $conn_id);

	header("location:/".$path_questions."/"); 
    exit();
}


function question_change_form()             // форма изменения вопроса
{
    //header("location:/".$path_questions."/");    
	global $sql_pref, $conn_id, $path, $page_header1, $path_questions, $user_id, $user_status, $art_url;
	$out="";
    if (!isset($_REQUEST['question_id']) || ($_REQUEST['question_id']<=0)) return; 
   	$sql_query="SELECT id FROM ".$sql_pref."_questions WHERE id='".$_REQUEST['question_id']."'&&user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)==0 && $user_status!="admin") return ("<b>К сожалению, у вас нет прав для этой операции</b>");
      
	    
    $sql_query="SELECT id, dt, question, user_id, question_type, enable, main FROM ".$sql_pref."_questions WHERE id='".$_REQUEST['question_id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
    if(mysql_num_rows($sql_res)>0)
    {
        list($id, $dt, $question, $p_user_id, $question_type, $enable, $main)=mysql_fetch_row($sql_res);
        //echo "тест";
        if ($enable=='Yes') $ch_enable='checked'; else $ch_enable='';
        if ($main=='Yes') $ch_main='checked'; else $ch_main='';
        $out.="<form name='form_name' action='' method='post'>
        <input type='hidden' name='id' value='".$id."'>
        <input type='hidden' name='action' value='question_change'>
        <table cellpadding='2' cellspacing='2' border='0' bgcolor='#FFFFFF'>
        	<tr>
        		<td class='form_left'>Вопрос</td>
        		<td class='form_main'><textarea class='form' id=question name='question' rows=4 style='overflow: auto; font-size: 12px;width:500px;'>".$question."</textarea></td>
        	</tr>
        	<tr>
        		<td class='form_left'>Настройки</td>
        		<td class='form_main'>
        			<input type='checkbox' name='enable' value='Yes' ".$ch_enable."> - активность<br>
                    <input type='checkbox' name='main' value='Yes' ".$ch_main."> - на главную
        		</td>
        	</tr>
            <tr>
        		<td>&nbsp;</td>
        		<td style='padding-top:10;'><input class='form_button' type='submit' name='button_submit' value='Сохранить'></td>
        	</tr>    	
        </table>
        <input class='form' type='hidden' name='question_id' value='".$id."'>
        </form>";
    } 
    return ($out);
}

function question_change()              // обновление инфы о вопросе в БД
{
	global $sql_pref, $conn_id, $path, $page_header1, $path_questions, $user_id, $user_status, $art_url;
	$out="";
    if (!isset($_REQUEST['question_id']) || ($_REQUEST['question_id']<=0)) return;
    if (!isset($_REQUEST['question'])) return; else $question=$_REQUEST['question'];
	if (!isset($_REQUEST['enable']) || $_REQUEST['enable']!="Yes") $enable="No"; else $enable="Yes";
	if (!isset($_REQUEST['main']) || $_REQUEST['main']!="Yes") $main="No"; else $main="Yes";
    
	$sql_query="UPDATE ".$sql_pref."_questions SET question='".$question."', enable='".$enable."', main='".$main."' WHERE id='".$_REQUEST['question_id']."'";
    $sql_res=mysql_query($sql_query, $conn_id);

	header("location:/".$path_questions."/"); 
    exit();
}
?>