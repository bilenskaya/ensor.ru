<?
if (isset($_REQUEST['action']) && $_REQUEST['action']=="companies_add")
{
	$imgpath="";
	$imgpath_www="";
	$enable="Yes";
	$title="����������";
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=="companies_edit")
{
	$imgpath=$path."files/companies/".$_REQUEST['id']."/";
	$imgpath_www="/files/companies/".$_REQUEST['id']."/";
	$sql_query="SELECT id, parent_id, name, name_eng, name_full, city_id, address_legal, address_fact, requisites, phone1, phone2, fax, email, site, descr, content, tags, author_id, enable, moderator, ps  FROM ".$sql_pref."_companies WHERE id='".$_REQUEST['id']."'";
	$sql_res=mysql_query($sql_query, $conn_id);
	if (mysql_num_rows($sql_res)>0)
	{
		list($id, $parent_id, $name, $name_eng, $name_full, $city_id, $address_legal, $address_fact, $requisites, $phone1, $phone2, $fax, $email, $site, $descr, $content, $tags, $author_id, $enable, $moderator, $ps)=mysql_fetch_row($sql_res);
		$name=stripslashes($name); $name_eng=stripslashes($name_eng); $name_full=stripslashes($name_full); $address_legal=stripslashes($address_legal); $address_fact=stripslashes($address_fact); $requisites=stripslashes($requisites); $phone1=stripslashes($phone1); $phone2=stripslashes($phone2); $fax=stripslashes($fax); $tags=stripslashes($tags); $descr=stripslashes($descr); $content=stripslashes($content);
        
		$sql_query="SELECT sfera_id FROM ".$sql_pref."_sd_sfery_arts WHERE table_name='companies'&&art_id='".$id."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
		while(list($sfery_list[])=mysql_fetch_row($sql_res_1));
        
		$sql_query="SELECT direction_id FROM ".$sql_pref."_sd_directions_arts WHERE table_name='companies'&&art_id='".$id."'";
		$sql_res_1=mysql_query($sql_query, $conn_id);
		while(list($directions_list[])=mysql_fetch_row($sql_res_1));
	}
	
	if (isset($_REQUEST['id']) && file_exists($path."files/companies/imgs/".$_REQUEST['id'].".jpg")) $fname=$_REQUEST['id'].".jpg";
	elseif (isset($_REQUEST['id']) && file_exists($path."files/companies/imgs/".$_REQUEST['id'].".gif")) $fname=$_REQUEST['id'].".gif";
	if (isset($fname))
	{
		$size=getimagesize($path."files/companies/imgs/".$fname);
		$size_th=getimagesize($path."files/companies/thumbs/".$fname);
	}
	
	$title="�������������� (id: ".$_REQUEST['id'].")";
}
?>

<a name='companies_add'></a><a name='companies_edit'></a>
<form name="form_name" action="index.html" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
<input type="hidden" name="letter" value="<?php if(isset($_REQUEST['letter'])) echo $_REQUEST['letter']; ?>">
<input type="hidden" name="action" value="companies_save">
<table cellpadding="2" cellspacing="2" border="0" bgcolor="#FFFFFF">
	<tr class="form_topline">
		<td colspan="2" align="left"><b><?php if (isset($title)) echo $title; ?>&nbsp;</b></td>
	</tr>
	<tr>
		<td class="form_left">��������</td>
		<td class="form_main"><input class="form" type="text" name="name" value='<?php if (isset($name)) echo $name; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">�������� (eng)</td>
		<td class="form_main"><input class="form" type="text" name="name_eng" value='<?php if (isset($name_eng)) echo $name_eng; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">������ ��������</td>
		<td class="form_main"><input class="form" type="text" name="name_full" value='<?php if (isset($name_full)) echo $name_full; ?>'></td>
	</tr>
	<tr>
		<td class=form_left>������������ ��������</td>
		<td class=form_main>
			<select name="parent_id" id="parent_id">
				<option value="0">��� ������</option>
			   <?
					$sql_query="SELECT id, name FROM ".$sql_pref."_companies WHERE id<>'".$id."' ORDER BY name";
					$sql_res=mysql_query($sql_query, $conn_id);
					while(list($p_id, $p_name)=mysql_fetch_row($sql_res))
					{
						$p_name=stripslashes($p_name);
						if ($p_id==@$parent_id) $select="selected"; else $select="";
						echo "<option value=".$p_id." ".$select.">".$p_name."</option>";
					}
			   ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class=form_left>�����</td>
		<td class=form_main>
			<select name="city_id" id="city_id">
				<option value="0">��� ������</option>
			   <?
					$sql_query="SELECT c.id, c.name, r.name FROM ".$sql_pref."_cities AS c, ".$sql_pref."_regions AS r WHERE c.region_id=r.id ORDER BY c.name";
					$sql_res=mysql_query($sql_query, $conn_id);
					while(list($c_id, $c_name, $r_name)=mysql_fetch_row($sql_res))
					{
						$c_name=stripslashes($c_name);$r_name=stripslashes($r_name);
						if ($c_id==@$city_id) $select="selected"; else $select="";
						echo "<option value=".$c_id." ".$select.">".$c_name." (".$r_name.")</option>";
					}
			   ?>
			</select>
            <div>
                <span onclick="document.getElementById('city_id').value='1459';" style="cursor:pointer;border-bottom:dotted 1px gray;">������</span>
                <span onclick="document.getElementById('city_id').value='1900';" style="cursor:pointer;border-bottom:dotted 1px gray;">�����-���������</span>
            </div>
		</td>
	</tr>
	<tr>
		<td class="form_left">������� ��������</td>
		<td class="form_main"><textarea class="form" name="descr" rows="4"><?php if (isset($descr)) echo $descr; ?></textarea></td>
	</tr>
	<tr>
		<td class="form_left">������ ��������</td>
		<td class="form_main">
			<div><span onclick='if (document.getElementById("divcontent").style.display=="none") document.getElementById("divcontent").style.display=""; else document.getElementById("divcontent").style.display="none";' style='cursor:pointer;font-size:12px;font-weight:normal;text-decoration:none;border-bottom:dotted 1px gray;'>��������</span></div>
			<div id='divcontent' style='display:none;'>
    			<?php require_once("../vis/visual.php"); ?>
            </div>
		</td>
	</tr>
	<tr>
		<td class="form_left">����������� �����</td>
		<td class="form_main"><input class="form" type="text" name="address_legal" value='<?php if (isset($address_legal)) echo $address_legal; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">����������� �����</td>
		<td class="form_main"><input class="form" type="text" name="address_fact" value='<?php if (isset($address_fact)) echo $address_fact; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">���������</td>
		<td class="form_main"><textarea class="form" name="requisites" rows="4"><?php if (isset($requisites)) echo $requisites; ?></textarea></td>
	</tr>
	<tr>
		<td class="form_left">������� 1</td>
		<td class="form_main"><input class="form" type="text" name="phone1" value='<?php if (isset($phone1)) echo $phone1; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">������� 2</td>
		<td class="form_main"><input class="form" type="text" name="phone2" value='<?php if (isset($phone2)) echo $phone2; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">����</td>
		<td class="form_main"><input class="form" type="text" name="fax" value='<?php if (isset($fax)) echo $fax; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">E-mail</td>
		<td class="form_main"><input class="form" type="text" name="email" value='<?php if (isset($email)) echo $email; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">����</td>
		<td class="form_main"><input class="form" type="text" name="site" value='<?php if (isset($site)) echo $site; ?>'></td>
	</tr>
	<tr>
		<td class=form_left>����� ������������</td>
		<td class=form_main>
			<div><span onclick='if (document.getElementById("divsfera").style.display=="none") document.getElementById("divsfera").style.display=""; else document.getElementById("divsfera").style.display="none";' style='cursor:pointer;font-size:12px;font-weight:normal;text-decoration:none;border-bottom:dotted 1px gray;'>��������</span></div>
			<div id='divsfera' style='display:none;'>
			   <?
        			$sql_query="SELECT id, name FROM ".$sql_pref."_sd_sfery ORDER BY code";
        			$sql_res=mysql_query($sql_query, $conn_id);
        			if(mysql_num_rows($sql_res)>0)
        			{
        				while (list($s_id, $s_name)=mysql_fetch_row($sql_res))
        				{
        					$s_name=stripslashes($s_name);
                            if (isset($sfery_list) && in_array($s_id, $sfery_list)) $checked="checked"; else $checked="";
        					echo '<div><input type="checkbox" name="sfery_'.$s_id.'" value="Yes" '.$checked.'> - '.$s_name.'</div>';
        				}
        			}
			   ?>
            </div>
		</td>
	</tr>
	<tr>
		<td class=form_left>����������� ������������</td>
		<td class=form_main>
			<div><span onclick='if (document.getElementById("divdirection").style.display=="none") document.getElementById("divdirection").style.display=""; else document.getElementById("divdirection").style.display="none";' style='cursor:pointer;font-size:12px;font-weight:normal;text-decoration:none;border-bottom:dotted 1px gray;'>��������</span></div>
			<div id='divdirection' style='display:none;'>
			   <?
        			$sql_query="SELECT id, name FROM ".$sql_pref."_sd_directions ORDER BY code";
        			$sql_res=mysql_query($sql_query, $conn_id);
        			if(mysql_num_rows($sql_res)>0)
        			{
        				while (list($d_id, $d_name)=mysql_fetch_row($sql_res))
        				{
        					$d_name=stripslashes($d_name);
                            if (isset($directions_list) && in_array($d_id, $directions_list)) $checked="checked"; else $checked="";
        					echo '<div><input type="checkbox" name="direction_'.$d_id.'" value="Yes" '.$checked.'> - '.$d_name.'</div>';
        				}
        			}
			   ?>
            </div>
		</td>
	</tr>
	<tr>
		<td class="form_left">���������</td>
		<td class="form_main"><input class="form" type="text" name="moderator" value='<?php if (isset($moderator)) echo $moderator; ?>'></td>
	</tr>
    	<tr>
		<td class="form_left">����������</td>
		<td class="form_main"><input class="form" type="text" name="ps" value='<?php if (isset($ps)) echo $ps; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">����</td>
		<td class="form_main"><input class="form" type="text" name="tags" value='<?php if (isset($tags)) echo $tags; ?>'></td>
	</tr>
	<tr>
		<td class="form_left">���������</td>
		<td class="form_main">
			<input type="checkbox" name="enable" value="Yes" <?php if (isset($enable) && $enable=="Yes") echo ("checked"); ?>> - ����������<br>
		</td>
	</tr>
	<tr>
		<td class="form_left">�������</td>
		<td class="form_main">
			<? if (isset($fname)) {?>
				<div>
                	<img src='/files/companies/thumbs/<? echo $fname;?>' border=1>
                    <a href="javascript:if(confirm('�� �������?'))window.location='?id=<? echo $_REQUEST['id'];?>&letter=<? echo @$_REQUEST['letter'];?>&action=companies_mainimg_del&fname=<? echo $fname;?>'"><img src='/admin/img/del.gif' width=25 height=13 alt='�������' border=0></a>
                </div>
			<? } ?>
			<div><input class="form_file" type="file" name="file_name" size="65"></div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style='padding-top:10;'><input class="form_button" type="submit" name="button_submit" value="���������"></td>
	</tr>
</table>
</form>