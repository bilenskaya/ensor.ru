<?php

function resume_main()
{
	global $sql_pref, $conn_id, $path, $art_url, $path_resume;
	global $auth_error_info, $user_id, $user_login, $user_email, $user_site, $user_priv_posts, $user_priv_admin;
	global $rub_url;
	$out="";

	if (isset($rub_url[1]) && $rub_url[1]=="resume") 
	{
		if (!isset($art_url)) $out.=resume_list();
		elseif (isset($_REQUEST['action']) AND $_REQUEST['action']=="edit") $out.=resume_edit();
		elseif (isset($_REQUEST['action']) AND $_REQUEST['action']=="history") $out.=resume_views();
		elseif (isset($art_url)) $out.=resume_show();
	}
        
	return ($out);
}

function resume_edit()
{
	global $sql_pref, $conn_id, $user_id, $page_header1, $user_surname, $user_name, $user_name2, $user_email, $page_title, $dt_birth, $phone_mobile, $path_resume;
$page_header1="������";
	
$page_header1="�������������� ������";
$page_title="������ �������";

function form_array($arr1){
	foreach($arr1 as $k=>$v) {
		$v=trim($v); 
		$v=strip_tags($v); 
		if(empty($v)) $v="�� �������"; 
		$arr1[$k]=$v;
		}
	foreach($arr1 as $k=>$v) if($v==="�� �������") unset($arr1[$k]);
	if (count($arr1)==0) $arr1[]="�� �������";
	$arr1=serialize($arr1);
	$arr1=addslashes($arr1);
return $arr1;
}

	
function form_3array($arr1, $arr2, $arr3){
	foreach($arr1 as $k=>$v) {
		$v=trim($v); 
		$v=strip_tags($v); 
		if(empty($v)) $v="�� �������"; 
		$arr1[$k]=$v; 
		}

	foreach($arr2 as $k=>$v) {
		$v=trim($v); 
		$v=strip_tags($v);
		if(empty($v)) $v="�� �������"; 
		$arr2[$k]=$v;
		}

	foreach($arr3 as $k=>$v) {
		$v=trim($v); 
		$v=strip_tags($v);
		if(empty($v)) $v="�� �������"; 
		$arr3[$k]=$v;
		}

foreach($arr1 as $k=>$v) $arr[$k]=$arr1[$k]."|".$arr2[$k]."|".$arr3[$k];
foreach($arr as $k=>$v) if($v==="�� �������|�� �������|�� �������") unset($arr[$k]);
if (count($arr)==0) $arr[]="�� �������";
	$arr=serialize($arr);
	$arr=addslashes($arr);
return $arr;
}



// �������� � ���������� ������ �� �����
	if (isset($_REQUEST['adress'])) {
		$new['adress']=$_REQUEST['adress'];
		$new['adress']=trim($new['adress']);
		$new['adress']=strip_tags($new['adress']); 
		if(empty($new['adress'])) $new['adress']="�� �������";
		$new['adress']=addslashes($new['adress']);
	}

	if (isset($_REQUEST['phone'])) $new['phone']=form_array($_REQUEST['phone']);
	
	if (isset($_REQUEST['mail'])) {
		$new['mail']=$_REQUEST['mail'];
		$new['mail']=trim($new['mail']); 
		$new['mail']=strip_tags($new['mail']); 
		if(empty($new['mail'])) $new['mail']="�� �������";
		$new['mail']=addslashes($new['mail']);
	}

	if (isset($_REQUEST['family'])) {
		$new['family']=$_REQUEST['family'];
		$new['family']=trim($new['family']);
		$new['family']=strip_tags($new['family']);
		if(empty($new['family'])) $new['family']="�� �������";
		$new['family']=addslashes($new['family']);
	}

	if (isset($_REQUEST['purpose'])) {
		$new['purpose']=$_REQUEST['purpose'];
		$new['purpose']=trim($new['purpose']);
		$new['purpose']=strip_tags($new['purpose']);
		if(empty($new['purpose'])) $new['purpose']="�� �������";
		$new['purpose']=addslashes($new['purpose']);
	}

		if (isset($_REQUEST['zp'])) {
		$new['zp']=$_REQUEST['zp'];
		$new['zp']=trim($new['zp']);
		$new['zp']=strip_tags($new['zp']);
		if(empty($new['zp'])) $new['zp']=0;
	}

	if (isset($_REQUEST['progress1']) and isset($_REQUEST['progress2']) and isset($_REQUEST['progress3'])) {
		$new['progress']=form_3array($_REQUEST['progress1'], $_REQUEST['progress2'], $_REQUEST['progress3']);
	}

	if (isset($_REQUEST['skills'])) $new['skills']=form_array($_REQUEST['skills']);

	if (isset($_REQUEST['experience1']) and isset($_REQUEST['experience2']) and isset($_REQUEST['experience3'])) {
		$new['experience']=form_3array($_REQUEST['experience1'], $_REQUEST['experience2'], $_REQUEST['experience3']);
	}

	if (isset($_REQUEST['education1']) and isset($_REQUEST['education2']) and isset($_REQUEST['education3'])) {
		$new['education']=form_3array($_REQUEST['education1'], $_REQUEST['education2'], $_REQUEST['education3']);
	}

	if (isset($_REQUEST['add_education1']) and isset($_REQUEST['add_education2']) and isset($_REQUEST['add_education3'])) {
		$new['add_education']=form_3array($_REQUEST['add_education1'], $_REQUEST['add_education2'], $_REQUEST['add_education3']);
	}

	if (isset($_REQUEST['hominem'])) $new['hominem']=form_array($_REQUEST['hominem']);

	if (isset($_REQUEST['addit'])) $new['addit']=form_array($_REQUEST['addit']);



	if (isset($_REQUEST['show_mode'])) $new['show_mode']=$_REQUEST['show_mode'];

	if (isset($_REQUEST['show_limit_agency'])) $new['show_limit_agency']=$_REQUEST['show_limit_agency']; else if(isset($_REQUEST['save'])) $new['show_limit_agency']=0;

	if (isset($_REQUEST['show_limit_my'])) $new['show_limit_my']=$_REQUEST['show_limit_my']; else if(isset($_REQUEST['save'])) $new['show_limit_my']=0;

	if(isset($new) and count($new)>0) $new['mod_date']=time();

	if(isset($new) and is_array($new)) foreach($new as $k=>$v) {
	$sql_query="UPDATE ".$sql_pref."_user_rezume SET ".$k."='".$v."' WHERE user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	}
	
	
if($user_id==0) 

$out.="<br><div>���������� ������ �������� ������ <a href='/auth/register/'>������������������</a> ������������� </div>";

else {
//������ ������ �� ����, ���� ������ ����, �� ������� ������
	$flag=TRUE;
	
	while ($flag){
		$sql_query="SELECT id, adress, phone, mail, family, purpose, zp, progress, skills, experience, education, add_education, hominem, addit, show_mode, show_limit_agency, show_limit_my, views, mod_date FROM ".$sql_pref."_user_rezume WHERE user_id='".$user_id."'";
		if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0)
		{
		list($id, $adress, $phone, $mail, $family, $purpose, $zp, $progress, $skills, $experience, $education, $add_education, $hominem, $addit, $show_mode, $show_limit_agency, $show_limit_my,$views, $mod_date)=mysql_fetch_row($sql_res);
		$flag=FALSE;
		}
	else 
		{
		$sql_query="INSERT INTO ".$sql_pref."_user_rezume(user_id, phone, mail, mod_date) VALUES ('".$user_id."','".$phone_mobile."','".$user_email."', '".time()."')";
		$sql_res=mysql_query($sql_query, $conn_id);
		}
	}
	
//���������� ������ � ������

$dt_birth = str_replace("-",".",$dt_birth);
$str_date = explode(".",$dt_birth);
$result_date = $str_date[2].".".$str_date[1].".".$str_date[0];


if (!$adress) $adress="�� �������";
else {$adress=stripslashes($adress); $adress=htmlspecialchars($adress,ENT_QUOTES);}

if (!$phone) $phone[]="�� �������";  
else {$phone=unserialize($phone); 
foreach($phone as $k=>$v) {$v=stripslashes($v); $phone[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$mail) $mail="�� �������";
else {$mail=stripslashes($mail); $mail=htmlspecialchars($mail,ENT_QUOTES);}

if (!$family) $family="�� �������";
else {$family=stripslashes($family); $family=htmlspecialchars($family,ENT_QUOTES);}

if (!$purpose) $purpose="�� �������";
else {$purpose=stripslashes($purpose); $purpose=htmlspecialchars($purpose,ENT_QUOTES);}

if (!$zp) $zp=0;

if (!$progress) $progress[]="�� �������";  
else {$progress=unserialize($progress); 
foreach($progress as $k=>$v) {$v=stripslashes($v); $progress[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$skills) $skills[]="�� �������";  
else {$skills=unserialize($skills); 
foreach($skills as $k=>$v) {$v=stripslashes($v); $skills[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$experience) $experience[]="�� �������";  
else {$experience=unserialize($experience); 
foreach($experience as $k=>$v) {$v=stripslashes($v); $experience[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$education) $education[]="�� �������";  
else {$education=unserialize($education); 
foreach($education as $k=>$v) {$v=stripslashes($v); $education[$k]=htmlspecialchars($v,ENT_QUOTES); }
}

if (!$add_education) $add_education[]="�� �������";  
else {$add_education=unserialize($add_education); 
foreach($add_education as $k=>$v) {$v=stripslashes($v); $add_education[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$hominem) $hominem[]="�� �������";  
else {$hominem=unserialize($hominem); 
foreach($hominem as $k=>$v) {$v=stripslashes($v); $hominem[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$addit) $addit[]="�� �������";  
else {$addit=unserialize($addit); 
foreach($addit as $k=>$v) {$v=stripslashes($v); $addit[$k]=htmlspecialchars($v,ENT_QUOTES);}
}


//�������� ������������ 
$complete=TRUE;
if ($adress=="�� �������") $complete=FALSE;
if ($family=="�� �������") $complete=FALSE;
if ($purpose=="�� �������") $complete=FALSE;

	$sql_query="UPDATE ".$sql_pref."_user_rezume SET complete='".$complete."' WHERE user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);

if(!$complete) {
$out.="<table cellpadding=0 cellspacing=0 border=0 width=60%>";
$out.="<tr><td colspan='2'><div class=\"container\"><b class=\"rtop\"><b class=\"r1\"></b><b class=\"r2\"></b> <b class=\"r3\"></b><b class=\"r4\"></b></b></div></td></tr>";
$out.="<tr><td class=\"container\"><img src='/img/warning_48.png' border=0 hspace='5'></td><td class=\"container\"><div style='padding:7px; font-weight:bold;'>���� ������ �� ���������. �������� ������ �� ��������� �������������� ����������� �������� �� ���� �������� ������������� �������������.</div></td></tr>";
$out.="<tr><td  colspan='2'><div class=\"container\"><b class=\"rbottom\"><b class=\"r4\"></b><b class=\"r3\"></b><b class=\"r2\"></b><b class=\"r1\"></b></b></div></td></tr>";
$out.="</table>";
$out.="<div><BR></div>";
}

//���������� ����������
if (isset($views)) $views=unserialize($views); else $views=array();
$min_date=time()-2592000;
foreach($views as $k=>$v) if($v<$min_date) unset($views[$k]);
$view_count=count($views);
$views=serialize($views);
	$sql_query="UPDATE ".$sql_pref."_user_rezume SET views='".$views."' WHERE user_id='".$user_id."'";
	$sql_res=mysql_query($sql_query, $conn_id);


echo "<script type=\"text/javascript\">";
echo "var phone_a = [\"".str_replace(array("\n","\r"),"",implode("\",\"",$phone))."\"];"; 
echo "var progress_a = [\"".str_replace(array("\n","\r"),"",implode("\",\"",$progress))."\"];"; 
echo "var skills_a = [\"".str_replace(array("\n","\r"),"",implode("\",\"",$skills))."\"];"; 
echo "var experience_a = [\"".str_replace(array("\n","\r"),"",implode("\",\"",$experience))."\"];"; 
echo "var education_a = [\"".str_replace(array("\n","\r"),"",implode("\",\"",$education))."\"];"; 
echo "var add_education_a = [\"".str_replace(array("\n","\r"),"",implode("\",\"",$add_education))."\"];"; 
echo "var hominem_a = [\"".str_replace(array("\n","\r"),"",implode("\",\"",$hominem))."\"];"; 
echo "var addit_a = [\"".str_replace(array("\n","\r"),"",implode("\",\"",$addit))."\"];";
echo "</script>"; 

//������ ���������
$adress_help="�����, ����� ��� ������� �����. �� ������� ������������� ����������� �����, ���, ��������";
$phone_help="�� ����� ������ �������� � ������ [���. +7 (XXX) XXX-XX-XX]";
$mail_help="����� ����������� ����� [email@email.com]";
$family_help="�������� ���������, ���������� �����, ������� ����� [�����, ���� ������� (��� ����)]";
$purpose_help="��������� ������ (���������� ���������, �� ������� �����������, ����� ������������)";
$zp_help="��������� ������� ������ � ���. ���. � �����";
$progress_help="� �������� ��������������� �������. ����������� ������� � �������� �������� (��� ���� ����� ������������), ����, ����������";
$skills_help="������� �������� ��, ����������� ������������, ������ ����������� ������ � �.�.";
$experience_help="� �������� ��������������� �������, ������� � ���������� ����� ������. ����������� ����������� (������� ����� ������������ � �����), ��������� � �������� �������������� �����������";
$education_help="� �������� ��������������� �������, ������� � ���������� ����� �����. ����������� ������ �������� �������� ���������, ���������, �������������, ����������� ������������, �������";
$add_education_help="� �������� ��������������� �������, ������� � ���������� ����� �����. ����������� ������ �������� �������� ���������, �������� ������, ���������, ���������, ����������� ������������, �������";
$hominem_help="����������� ������ �������� (5-10 ��.)";
$addit_help="�������������� ���������� �� ���� ������������� (������� ������������� �������������, ������������ ����������, ���������� � �������������, ������� ��������������, ��������� � �������� � ������ �����, �����, ������� ������������ � �.�.)";


$out.="<form name=rezume action='' method='post'>";


$out.="<table cellpadding='2' cellspacing='2' border='0' width=100%>";

$out.="<tr><td colspan='3'><div style='font-size:26px; text-align:center;'>������</div></td></tr>";

$out.="<tr><td colspan='3'><div style='font-weight:bold;'>".$user_surname." ".$user_name." ".$user_name2."</div></td></tr>";

$out.="<tr><td colspan='3'><b>���� ��������: </b>".$result_date."</td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td width=25%><b>����� ����������:<font color='#FF0000'>*</font></b></td><td><div id=\"adress\">";
$out.=$adress; 
$out.="</div><div id=\"adress_help\" class='resume_help'></div></td><td width=10%><div id=\"adress_change\" onClick='addform(\"adress\", \"$adress\", \"$adress_help\")'><img src='/img/edit_resume.png' border='0' hspace='5' alt='��������'></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>�������: </b></td><td><div id=\"phone\">";
$out.=implode(", ",$phone); 
$out.="</div><div id=\"phone_help\" class='resume_help'></div><div id=\"phone_add\"></div></td><td><div id=\"phone_change\" onClick='add_ext_form(\"phone\", phone_a, \"$phone_help\")'><img src='/img/edit_resume.png' border=0 hspace='5' alt='��������'></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>E-mail: </b></td><td class=\"container\"><div id=\"mail\">";
$out.=$mail; 
$out.="</div><div id=\"mail_help\" class='resume_help'></div></td><td><div id=\"mail_change\" onClick='addform(\"mail\", \"$mail\", \"$mail_help\")'><img src='/img/edit_resume.png' border=0 hspace='5' alt='��������'></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>�������� ���������:<font color='#FF0000'>*</font></b></td><td><div id=\"family\">";
$out.=$family; 
$out.="</div><div id=\"family_help\" class='resume_help'></div></td><td><div id=\"family_change\" onClick='addform(\"family\", \"$family\", \"$family_help\")'><img src='/img/edit_resume.png' border=0 hspace='5' alt='��������'></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>����:<font color='#FF0000'>*</font></b></td><td><div id=\"purpose\">";
$out.=$purpose; 
$out.="</div><div id=\"purpose_help\" class='resume_help'></div></td><td><div id=\"purpose_change\" onClick='addform(\"purpose\", \"$purpose\", \"$purpose_help\")'><img src='/img/edit_resume.png' border='0' hspace='5' alt='��������'></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>��������� ������� ������: </b></td><td><div id=\"zp\"> �� ";
$out.=$zp; 
$out.=" ���. ���./���.</div><div id=\"zp_help\" class='resume_help'></div></td><td><div id=\"zp_change\" onClick='addform(\"zp\", \"$zp\", \"$zp_help\")'><img src='/img/edit_resume.png' border='0' hspace='5' alt='��������'></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td colspan='2'><b>������ � ����������: </b></td><td rowspan='2'><div id=\"progress_change\" onClick='add_tri_form(\"progress\", progress_a, \"��������� ������\", \"����� ������������\", \"�������� ����������\", \"$progress_help\")'><img src='/img/edit_resume.png' border=0 hspace='5' alt='��������'></div></td></tr>";
$out.="<tr><td colspan='2'><div id=\"progress\">";
$out.="<table border='0' cellpadding='3' cellspacing='3' width=100%>";
foreach($progress as $k=>$v) {
$split_progress=explode("|",$v);
$out.="<tr>";
foreach($split_progress as $k1=>$v1) $out.="<td>$v1</td>";
$out.="</tr>";
}
$out.="</table>";
$out.="</div><div id=\"progress_help\" class='resume_help'></div><div id=\"progress_add\"></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>���������������� ������: </b></td><td><div id=\"skills\">";
$out.="<ul>";
foreach($skills as $k=>$v) $out.="<li>$v</li>";
$out.="</ul>";
$out.="</div><div id=\"skills_help\" class='resume_help'></div><div id=\"skills_add\"></div></td><td><div id=\"skills_change\" onClick='add_ext_form(\"skills\", skills_a, \"$skills_help\")'><img src='/img/edit_resume.png' border=0 hspace='5' alt='��������'></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td colspan='2'><b>���� ������: </b></td><td rowspan='2'><div id=\"experience_change\" onClick='add_tri_form(\"experience\", experience_a, \"��������� ������\", \"����������� � ���������\", \"�������������� �����������\", \"$experience_help\")'><img src='/img/edit_resume.png' border=0 hspace='5' alt='��������'></div></td></tr>";
$out.="<tr><td colspan='2'><div id=\"experience\">";
$out.="<table border='0' cellpadding='3' cellspacing='3' width=100%>";
foreach($experience as $k=>$v) {
$split_experience=explode("|",$v);
$out.="<tr>";
foreach($split_experience as $k1=>$v1) $out.="<td>$v1</td>";
$out.="</tr>";
}
$out.="</table>";
$out.="</div><div id=\"experience_help\" class='resume_help'></div><div id=\"experience_add\"></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td colspan='2'><b>�����������: </b></td><td rowspan='2'><div id=\"education_change\" onClick='add_tri_form(\"education\", education_a, \"��������� ������\", \"������ �������� �������� ���������,<BR> ���������, �������������\", \"����������� ������������ \", \"$education_help\")'><img src='/img/edit_resume.png' border=0 hspace='5' alt='��������'></div></td></tr>";
$out.="<tr><td colspan='2'><div id=\"education\">";
$out.="<table border='0' cellpadding='3' cellspacing='3' width=100%>";
foreach($education as $k=>$v) {
$split_education=explode("|",$v);
$out.="<tr>";
foreach($split_education as $k1=>$v1) $out.="<td>$v1</td>";
$out.="</tr>";
}
$out.="</table>";
$out.="</div><div id=\"education_help\" class='resume_help'></div><div id=\"education_add\"></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td colspan='2'><b>�������������� �����������: </b></td><td rowspan='2'><div id=\"add_education_change\" onClick='add_tri_form(\"add_education\", add_education_a, \"��������� ������\", \"������ �������� �������� ���������\", \"�������� ������, ���������, ��������� � �.�.\", \"$add_education_help\")'><img src='/img/edit_resume.png' border=0 hspace='5' alt='��������'></div></td></tr>";
$out.="<tr><td colspan=2><div id=\"add_education\">";
$out.="<table border='0' cellpadding='3' cellspacing='3' width=100%>";
foreach($add_education as $k=>$v) {
$split_add_education=explode("|",$v);
$out.="<tr>";
foreach($split_add_education as $k1=>$v1) $out.="<td>$v1</td>";
$out.="</tr>";
}
$out.="</table>";
$out.="</div><div id=\"add_education_help\" class='resume_help'></div><div id=\"add_education_add\"></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>������ ��������: </b></td><td><div id=\"hominem\">";
$out.="<ul>";
foreach($hominem as $k=>$v) $out.="<li>$v</li>";
$out.="</ul>";
$out.="</div><div id=\"hominem_help\" class='resume_help'></div><div id=\"hominem_add\"></div></td><td><div id=\"hominem_change\" onClick='add_ext_form(\"hominem\", hominem_a, \"$hominem_help\")'><img src='/img/edit_resume.png' border=0 hspace='5' alt='��������'></div></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>�������������� ����������: </b></td><td><div id=\"addit\">";
$out.="<ul>";
foreach($addit as $k=>$v) $out.="<li>$v</li>";
$out.="</ul>";
$out.="</div><div id=\"addit_help\" class='resume_help'></div><div id=\"addit_add\"></div></td><td><div id=\"addit_change\" onClick='add_ext_form(\"addit\", addit_a, \"$addit_help\")'><img src='/img/edit_resume.png' border=0 hspace='5' alt='��������'></div></td></tr>";

$out.="</table>";






$out.="<div><font color='#FF0000'>*</font> - ����������� ��� ����������</div>";
$out.="<div><br>��������� ���������: ".date( 'd.m.y H:i', $mod_date)."</div>";
$out.="<div><br>���������� �� �����: ".$view_count."&nbsp; &nbsp;";
$out.="<a href='/".$path_resume."/".$user_id.".html?action=history' class='auth_main'>������� ����������</a></div>";
$out.="<div><br></div>";






$out.="<table cellpadding='0' cellspacing='0' border='0' width=60%>";
$out.="<tr><td><div class=\"container\"><b class=\"rtop\"><b class=\"r1\"></b><b class=\"r2\"></b> <b class=\"r3\"></b><b class=\"r4\"></b></b></div></td></tr>";
$out.="<tr><td class=\"container\"><div style='padding:4px; font-weight:bold;text-align:center;'>��������� ��������� ������</did></td></tr>";

if($show_limit_agency==1) $show_limit_agency_ch="CHECKED"; else $show_limit_agency_ch="";
if($show_limit_my==1) $show_limit_my_ch="CHECKED"; else $show_limit_my_ch="";

$check_text="<INPUT TYPE=CHECKBOX $show_limit_agency_ch NAME=show_limit_agency VALUE=1>���������� �������������� �������������� ��������<BR><INPUT TYPE=CHECKBOX $show_limit_my_ch NAME=show_limit_my VALUE=1>���������� ������������� �� ����� ������ ���������";

if($show_mode=="all") {$show_mode_all="checked"; $show_mode_limit=""; $show_check="";} else {$show_mode_all=""; $show_mode_limit="checked"; $show_check=$check_text;}


$out.="<tr><td class=\"container\"><INPUT TYPE=RADIO NAME='show_mode' VALUE='all' ".$show_mode_all." onClick='delcheck(\"show_mode_add\")'> �������� ����</td></tr>";
$out.="<tr><td class=\"container\"><INPUT TYPE=RADIO NAME='show_mode' VALUE='limit' ".$show_mode_limit." onClick='addcheck(\"show_mode_add\", \"$check_text\")'> ���������� ��������</td></tr>";
$out.="<tr><td class=\"container\" ><div id=\"show_mode_add\">".$show_check."</div></td></tr>";
$out.="<tr><td><div class=\"container\"><b class=\"rbottom\"><b class=\"r4\"></b><b class=\"r3\"></b><b class=\"r2\"></b><b class=\"r1\"></b></b></div></td></tr>";
$out.="</table>";


$out.="<br>";

$out.="<input type='submit' value='C�������� ���������' name='save'/>";
$out.="</form>";
$out.="<br><a href='javascript:history.back(1)'>��&nbsp;���������</a><br>";
}

return ($out);
}


function resume_show()
{
	global $sql_pref, $conn_id, $path, $path_users, $user_id, $art_url, $page_header1;
	$out="";

$page_header1="�������� ������";

if($user_id==0)
{
$out.="<br><div>�������� ������ �������� ������ <a href='/auth/register/'>������������������</a> ������������� </div>";
}
else {

//������ ������ �� ���� ������ 
$sql_query="SELECT id, adress, phone, mail, family, purpose, zp, progress, skills, experience, education, add_education, hominem, addit, show_mode, show_limit_agency, show_limit_my, complete, views, mod_date FROM ".$sql_pref."_user_rezume WHERE user_id='".$art_url."' AND complete='1'";
if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0)
list($id, $adress, $phone, $mail, $family, $purpose, $zp, $progress, $skills, $experience, $education, $add_education, $hominem, $addit, $show_mode, $show_limit_agency, $show_limit_my, $complete, $views, $mod_date)=mysql_fetch_row($sql_res);

else {return(FALSE); break;}

//������ ������ �� ���� ������� 

$sql_query="SELECT surname, name, name2, dt_birth FROM ".$sql_pref."_users WHERE id='".$art_url."'";
$sql_res=mysql_query($sql_query, $conn_id);
list($user_surname, $user_name, $user_name2, $dt_birth)=mysql_fetch_row($sql_res);

$user_surname=stripslashes($user_surname);
$user_name=stripslashes($user_name);
$user_name2=stripslashes($user_name2);


//�������� ���������� �� ��������

$show_perm=FALSE;
if($show_mode=="all") $show_perm=TRUE;

if($show_limit_agency){
$sql_query_ag="SELECT id FROM ".$sql_pref."_company_admin WHERE user_id='".$user_id."' AND company_resume='Yes'";
$sql_res_ag=mysql_query($sql_query_ag, $conn_id);
if($sql_res_ag=mysql_query($sql_query_ag, $conn_id) AND mysql_num_rows($sql_res_ag)>0) $show_perm=TRUE;
}

if($show_limit_my){
$sql_query="SELECT id FROM ".$sql_pref."_user_phones WHERE user_id='".$art_url."' AND contact_id='".$user_id."'";
$sql_res=mysql_query($sql_query, $conn_id);
if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0) $show_perm=TRUE;
}

if(!$show_perm) {return(FALSE); break;}


//���������� ������ � ������

$dt_birth = str_replace("-",".",$dt_birth);
$str_date = explode(".",$dt_birth);
$result_date = $str_date[2].".".$str_date[1].".".$str_date[0];


if (!$adress) $adress="�� �������";
else {$adress=stripslashes($adress); $adress=htmlspecialchars($adress,ENT_QUOTES);}

if (!$phone) $phone[]="�� �������";  
else {$phone=unserialize($phone); 
foreach($phone as $k=>$v) {$v=stripslashes($v); $phone[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$mail) $mail="�� �������";
else {$mail=stripslashes($mail); $mail=htmlspecialchars($mail,ENT_QUOTES);}

if (!$family) $family="�� �������";
else {$family=stripslashes($family); $family=htmlspecialchars($family,ENT_QUOTES);}

if (!$purpose) $purpose="�� �������";
else {$purpose=stripslashes($purpose); $purpose=htmlspecialchars($purpose,ENT_QUOTES);}

if (!$zp) $zp=0;

if (!$progress) $progress[]="�� �������";  
else {$progress=unserialize($progress); 
foreach($progress as $k=>$v) {$v=stripslashes($v); $progress[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$skills) $skills[]="�� �������";  
else {$skills=unserialize($skills); 
foreach($skills as $k=>$v) {$v=stripslashes($v); $skills[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$experience) $experience[]="�� �������";  
else {$experience=unserialize($experience); 
foreach($experience as $k=>$v) {$v=stripslashes($v); $experience[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$education) $education[]="�� �������";  
else {$education=unserialize($education); 
foreach($education as $k=>$v) {$v=stripslashes($v); $education[$k]=htmlspecialchars($v,ENT_QUOTES); }
}

if (!$add_education) $add_education[]="�� �������";  
else {$add_education=unserialize($add_education); 
foreach($add_education as $k=>$v) {$v=stripslashes($v); $add_education[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$hominem) $hominem[]="�� �������";  
else {$hominem=unserialize($hominem); 
foreach($hominem as $k=>$v) {$v=stripslashes($v); $hominem[$k]=htmlspecialchars($v,ENT_QUOTES);}
}

if (!$addit) $addit[]="�� �������";  
else {$addit=unserialize($addit); 
foreach($addit as $k=>$v) {$v=stripslashes($v); $addit[$k]=htmlspecialchars($v,ENT_QUOTES);}
}


//���������� ����������
if (isset($views)) $views=unserialize($views); else $views=array();
$min_date=time()-2592000;
foreach($views as $k=>$v) if($v<$min_date) unset($views[$k]);
$views[$user_id]=time();
$view_count=count($views);
$views=serialize($views);
	$sql_query="UPDATE ".$sql_pref."_user_rezume SET views='".$views."' WHERE user_id='".$art_url."'";
	$sql_res=mysql_query($sql_query, $conn_id);


//����� ������

$out.="<table cellpadding='0' cellspacing='0' border='0' width=100%>";
$out.="<tr><td colspan='3'><div class=\"container\"><b class=\"rtop\"><b class=\"r1\"></b><b class=\"r2\"></b> <b class=\"r3\"></b><b class=\"r4\"></b></b></div></td></tr>";
$out.="<tr><td class=\"container\">";

$out.="<table cellpadding='2' cellspacing='2' border='0' width=100%>";

$out.="<tr><td colspan='3'><div style='font-size:26px; text-align:center;'>������</div></td></tr>";

$out.="<tr><td colspan='3'><div style='font-weight:bold;'>".$user_surname." ".$user_name." ".$user_name2."</div></td></tr>";

$out.="<tr><td colspan='3'><b>���� ��������: </b>".$result_date."</td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td width=25%><b>����� ����������: </b></td><td><div id=\"adress\">";
$out.=$adress; 
$out.="</div></td><td width=10%></td></tr>";

if(implode(", ",$phone)!=="�� �������") {
$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>�������: </b></td><td><div id=\"phone\">";
$out.=implode(", ",$phone); 
$out.="</div><div id=\"phone_add\"></div></td><td></td></tr>";}

if($mail!=="�� �������") {
$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>E-mail: </b></td><td class=\"container\"><div id=\"mail\">";
$out.=$mail; 
$out.="</div></td><td></td></tr>";}

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>�������� ���������: </b></td><td><div id=\"family\">";
$out.=$family; 
$out.="</div></td><td></td></tr>";

$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>����: </b></td><td><div id=\"purpose\">";
$out.=$purpose; 
$out.="</div></td><td></td></tr>";

if($zp!==0) {
$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>��������� ������� ������: </b></td><td><div id=\"zp\"> �� ";
$out.=$zp; 
$out.=" ���. ���./���.</div></td><td></td></tr>";}

if(implode(", ",$progress)!=="�� �������") {
$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td colspan='2'><b>������ � ����������: </b></td><td rowspan='2'></td></tr>";
$out.="<tr><td colspan='2'><div id=\"progress\">";
$out.="<table border='0' cellpadding='3' cellspacing='3' width=100%>";
foreach($progress as $k=>$v) {
$split_progress=explode("|",$v);
$out.="<tr>";
foreach($split_progress as $k1=>$v1) $out.="<td>$v1</td>";
$out.="</tr>";
}
$out.="</table>";
$out.="</div><div id=\"progress_add\"></div></td></tr>";}

if(implode(", ",$skills)!=="�� �������") {
$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>���������������� ������: </b></td><td><div id=\"skills\">";
$out.="<ul>";
foreach($skills as $k=>$v) $out.="<li>$v</li>";
$out.="</ul>";
$out.="</div><div id=\"skills_add\"></div></td><td></td></tr>";}

if(implode(", ",$experience)!=="�� �������") {
$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td colspan='2'><b>���� ������: </b></td><td rowspan='2'></td></tr>";
$out.="<tr><td colspan='2'><div id=\"experience\">";
$out.="<table border='0' cellpadding='3' cellspacing='3' width=100%>";
foreach($experience as $k=>$v) {
$split_experience=explode("|",$v);
$out.="<tr>";
foreach($split_experience as $k1=>$v1) $out.="<td>$v1</td>";
$out.="</tr>";
}
$out.="</table>";
$out.="</div><div id=\"experience_add\"></div></td></tr>";}

if(implode(", ",$education)!=="�� �������") {
$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td colspan='2'><b>�����������: </b></td><td rowspan='2'></td></tr>";
$out.="<tr><td colspan='2'><div id=\"education\">";
$out.="<table border='0' cellpadding='3' cellspacing='3' width=100%>";
foreach($education as $k=>$v) {
$split_education=explode("|",$v);
$out.="<tr>";
foreach($split_education as $k1=>$v1) $out.="<td>$v1</td>";
$out.="</tr>";
}
$out.="</table>";
$out.="</div><div id=\"education_add\"></div></td></tr>";}

if(implode(", ",$add_education)!=="�� �������") {
$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td colspan='2'><b>�������������� �����������: </b></td><td rowspan='2'></td></tr>";
$out.="<tr><td colspan=2><div id=\"add_education\">";
$out.="<table border='0' cellpadding='3' cellspacing='3' width=100%>";
foreach($add_education as $k=>$v) {
$split_add_education=explode("|",$v);
$out.="<tr>";
foreach($split_add_education as $k1=>$v1) $out.="<td>$v1</td>";
$out.="</tr>";
}
$out.="</table>";
$out.="</div><div id=\"add_education_add\"></div></td></tr>";}

if(implode(", ",$hominem)!=="�� �������") {
$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>������ ��������: </b></td><td><div id=\"hominem\">";
$out.="<ul>";
foreach($hominem as $k=>$v) $out.="<li>$v</li>";
$out.="</ul>";
$out.="</div><div id=\"hominem_add\"></div></td><td></td></tr>";}

if(implode(", ",$addit)!=="�� �������") {
$out.="<tr><td colspan='3' style='border-top:solid 1px #777777;'>&nbsp</td></tr>";
$out.="<tr><td><b>�������������� ����������: </b></td><td><div id=\"addit\">";
$out.="<ul>";
foreach($addit as $k=>$v) $out.="<li>$v</li>";
$out.="</ul>";
$out.="</div><div id=\"addit_add\"></div></td><td></td></tr>";}

$out.="</table>";
$out.="</td></tr>";
$out.="<tr><td><div class=\"container\"><b class=\"rbottom\"><b class=\"r4\"></b><b class=\"r3\"></b><b class=\"r2\"></b><b class=\"r1\"></b></b></div></td></tr>";
$out.="</table>";

$out.="<div><br>��������� ���������: ".date( 'd.m.y H:i', $mod_date)."</div>";


$out.="<table cellpadding=3 cellspacing=5 border=0>";
	$out.="<tr><td><a href='/".$path_users."/".$art_url.".html'><img src='/img/man.png' alt='������� ������������' border=0></a></td><td><a href='/".$path_users."/".$art_url.".html'>������� ������������ ".$user_name." ".$user_surname."</a></td></tr>";
    $out.="<tr><td><a href='/auth/messages_add/?user_id_to=".$art_url."'><img src='/img/message_send.png' alt='�������� ���������' border=0></a></td><td><a href='/auth/messages_add/?user_id_to=".$art_url."'>�������� ������ ��������� ������������</a></td></tr>";
    $out.="<tr><td><a href='/auth/user_phones_add/?contact_id=".$art_url."'><img src='/img/contact-new.png' alt='�������� ������������ � ��� ��������' border=0></a></td><td><a href='/auth/user_phones_add/?contact_id=".$art_url."'>�������� ������������ � ��� ��������</a></td></tr>";
    $out.="</table>";
$out.="<div><br><a href='javascript:history.back(1)'>��&nbsp;���������</a></div><br>";
}
return($out);

}


function resume_list()
{
	global $sql_pref, $conn_id, $path, $path_users, $user_id, $path_companies, $path_resume, $page_header1;
	$out=""; 

$page_header1="������ ������ �����������";

if($user_id==0)
{
$out.="<br><div>�������� ������ ����������� �������� ������ <a href='/auth/register/'>������������������</a> ������������� </div>";
}

//������ ������ �� ���� ������ 
$sql_query="SELECT ".$sql_pref."_user_rezume.id, ".$sql_pref."_user_rezume.user_id, purpose, zp, show_mode, show_limit_agency, show_limit_my, complete, mod_date, surname, name, name2, ".$sql_pref."_users.dt_birth, company_id, doljnost FROM ".$sql_pref."_user_rezume INNER JOIN ".$sql_pref."_users ON ".$sql_pref."_user_rezume.user_id=".$sql_pref."_users.id WHERE complete='1' ORDER BY ".$sql_pref."_user_rezume.mod_date DESC";
if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0){


$out.="<table cellpadding='2' cellspacing='0' border='0' width=100%>";
$out.="
<tr><td colspan='5' style='border-bottom:solid 1px #777777;'>&nbsp;</td></tr>
<tr bgcolor='#f2f2f2'>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>����</b></td>
<td style='border-bottom:solid 1px #777777;'>&nbsp;</td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>���</b></td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>��������</b></td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>��������</b></td>
</tr>";

$count=0;
while (list($id, $owner_id, $purpose, $zp, $show_mode, $show_limit_agency, $show_limit_my, $complete, $mod_date, $surname, $name, $name2, $dt_birth, $company_id, $doljnost)=mysql_fetch_row($sql_res)) {

$surname=stripslashes($surname);
$name=stripslashes($name);
$name2=stripslashes($name2);


//�������� ���������� �� ��������
$show_perm=FALSE;
if($show_mode=="all") $show_perm=TRUE;

if($show_limit_agency){
$sql_query_ag="SELECT id FROM ".$sql_pref."_company_admin WHERE user_id='".$user_id."' AND company_resume='Yes'";
$sql_res_ag=mysql_query($sql_query_ag, $conn_id);
if($sql_res_ag=mysql_query($sql_query_ag, $conn_id) AND mysql_num_rows($sql_res_ag)>0) $show_perm=TRUE;
if($user_id==0) $show_perm=TRUE;
}

if($show_limit_my and $user_id>0){
$sql_query_my="SELECT id FROM ".$sql_pref."_user_phones WHERE user_id='".$art_url."' AND contact_id='".$user_id."'";
$sql_res_my=mysql_query($sql_query_my, $conn_id);
if($sql_res_my=mysql_query($sql_query_my, $conn_id) AND mysql_num_rows($sql_res_my)>0) $show_perm=TRUE;
}


// �����
if(!$zp) $zp="&nbsp"; else $zp="<li>�� $zp ���.���./���.</li>";
if($user_id==0) $name_show="<small>��� ���������� ������ ��� �������������������� �������������</small>"; else $name_show=$surname." ".$name." ".$name2;

if (!$purpose) $purpose="&nbsp";
else {$purpose=stripslashes($purpose); $purpose=htmlspecialchars($purpose,ENT_QUOTES); $purpose="<li>".$purpose."</li>";}

$show_rez="<a href='/".$path_resume."/".$owner_id.".html'><img src='/img/resume_sm.png' border=0 hspace='5' alt='�������� ������'></a>";

$company_name="-";
$sql_query="SELECT name FROM ".$sql_pref."_companies WHERE id='".$company_id."'";
$sql_res_1=mysql_query($sql_query, $conn_id);
if(mysql_num_rows($sql_res_1)>0)
{
list($company_name)=mysql_fetch_row($sql_res_1);
$company_name="<a href='/".$path_companies."/".$company_id.".html' style='font-weight:bold;'>".StripSlashes($company_name)."</a>";
}
if ($show_perm){
$out.="
<tr>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><span class=dates>".date( 'd.m.y', $mod_date)."</span></td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'>".$show_rez."</td>
<td align='left' valign='middle' style='border-bottom:solid 1px #777777;'><a href='/".$path_resume."/".$owner_id.".html'>".$name_show."</a></td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'>".$company_name."<BR><small><i>".$doljnost."</i></small></td>
<td align='left' valign='middle' style='border-bottom:solid 1px #777777;'><ul>".$purpose.$zp."</ul></td>
</tr>";
$count++;
}
}
$out.="</table>";
$out.="����� �������: ".$count;
}

else $out.="� ���������, ��������� ��� ��������� ������ ���.";
if($count==0) $out="� ���������, ��������� ��� ��������� ������ ���.";


	$out.="<table width=100%><tr><td width='1%' align=right><a href='/".$path_resume."/".$user_id.".html?action=edit'><img src='/img/plus.png' border='0' vspace='5'></a></td><td><a href='/".$path_resume."/".$user_id.".html?action=edit' class='auth_main'>�������� ������</a></td></tr></table>";	
	$out.="<br><a href='javascript:history.back(1)'>��&nbsp;���������</a><br>";
return($out);
}


function resume_views()
{
	global $sql_pref, $conn_id, $path, $path_users, $user_id, $path_companies, $path_resume, $page_header1, $page_title;
	$out=""; 

$page_header1="������� ���������� ������";
$page_title="������ �������";

if($user_id==0) 

$out.="<br><div>������� ���������� ������ �������� ������ <a href='/auth/register/'>������������������</a> ������������� </div>";

else {
$count=0;
//������ ������ �� ����
$sql_query="SELECT id, views, mod_date FROM ".$sql_pref."_user_rezume WHERE user_id='".$user_id."'";
if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0){
list($id, $views, $mod_date)=mysql_fetch_row($sql_res);
$views=unserialize($views);
arsort($views);

$out.="<table cellpadding='5' cellspacing='0' border='0' width=100%>";
$out.="
<tr><td colspan='5' style='border-bottom:solid 1px #777777;'>&nbsp;</td></tr>
<tr bgcolor='#f2f2f2'>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>����</b></td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>���</b></td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>��������</b></td>
</tr>";



foreach ($views as $id=>$time){

$sql_query="SELECT ".$sql_pref."_users.surname, ".$sql_pref."_users.name, ".$sql_pref."_users.name2, ".$sql_pref."_users.doljnost, ".$sql_pref."_companies.name FROM ".$sql_pref."_users LEFT JOIN ".$sql_pref."_companies ON ".$sql_pref."_users.company_id=".$sql_pref."_companies.id WHERE ".$sql_pref."_users.enable='Yes' AND ".$sql_pref."_users.id=".$id."";
if($sql_res=mysql_query($sql_query, $conn_id) AND mysql_num_rows($sql_res)>0)
list($surname, $name, $name2, $doljnost, $company_name)=mysql_fetch_row($sql_res);
$viewer="<a href='/".$path_users."/".$id.".html'>".$surname." ".$name." ".$name2."</a>";

if (!isset($company_name)) $company_name="-";
if (!isset($doljnost)) $doljnost="";
$out.="
<tr>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><span class=dates>".date( 'd.m.y H-i', $time)."</span></td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'>".$viewer."</td>
<td align='center' valign='middle' style='border-bottom:solid 1px #777777;'><b>".$company_name."</b><BR><small><i>".$doljnost."</i></small></td>
</tr>";
$count++;
}
$out.="</table>";
$out.="����� ���������� �� �����: ".$count;
}
else $out.="�� ��������� ����� ���� ������ ����� �� �������";
}
$out.="<br><br><a href='javascript:history.back(1)'>��&nbsp;���������</a><br>";
return($out);
}
?>