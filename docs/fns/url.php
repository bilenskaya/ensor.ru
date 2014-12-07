<?php


function url_decode($url_decode)
{
	global $sql_pref, $conn_id;
	global $rub_num, $rub_id, $rub_name, $rub_descr, $rub_url, $rub_module;
	global $art, $art_id, $art_name, $art_url;
	global $module_path;
	$module_path="/";
	$str=$url_decode;
	if (strpos($str,"?")) $str=substr($str,0,strpos($str,"?"));
	if (substr($str,-5)=='.html')
	{
	 	$str=substr($str,0,strlen($str)-5);
		$art_url=substr($str, strrpos($str, '/')+1);
		$str=substr($str, 0, strrpos($str, '/')+1);
	}
	$str=substr($str,1);
	if (substr($str,-1)=='/') $str=substr($str,0,-1);
	$rub_url=explode("/", $str);
	$rnum=count ($rub_url);
	$rub_num=0;
	$rub_parent_id[0]=0;
	if ($rub_url[0]=="404") {$rub_module="404";return;}
	if ($rub_url[0]=="map") {$rub_module="map";return;}
	if ($rub_url[0]=="regonly") {$rub_module="regonly";return; }
	for ($i=0; $i<=($rnum-1); $i++)
	{
		$rub_num++;
		$sql_query="SELECT id, name, content, module, auth FROM ".$sql_pref."_pub_rubs WHERE level='".($i+1)."' AND url='".$rub_url[$i]."' AND parent_id='".$rub_parent_id[$i]."'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			list($rub_id[$i], $rub_name[$i], $rub_descr, $rub_module, $rub_auth)=mysql_fetch_row($sql_res);
			$rub_name[$i]=stripslashes($rub_name[$i]);
			$rub_descr=stripslashes($rub_descr);
			$module_path.=$rub_url[$i]."/";
			if ($rub_auth=="Yes") regonly();
			if (isset($rub_module) && !empty($rub_module)) break(1);
			if ($i!=($rnum-1)) $rub_parent_id[($i+1)]=$rub_id[$i];
		}
		else error_404();
	}
	if (isset($art_url) && !empty($art_url))
	{
		$art="Yes";
		$sql_query="SELECT id, name, descr FROM ".$sql_pref."_pub_arts WHERE url='".$art_url."' AND parent_id='".$rub_id[($rub_num-1)]."' AND enable='Yes'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			list($art_id, $art_name, $art_descr)=mysql_fetch_row($sql_res);
			$art_name=stripslashes($art_name);
			$art_descr=stripslashes($art_descr);
		}
	}
	else
	{
		$sql_query="SELECT id, url, name, descr FROM ".$sql_pref."_pub_arts WHERE code='1' AND parent_id='".$rub_id[($rub_num-1)]."' AND enable='Yes'";
		$sql_res=mysql_query($sql_query, $conn_id);
		if (mysql_num_rows($sql_res)>0)
		{
			list($art_id, $art_url, $art_name, $art_descr)=mysql_fetch_row($sql_res);
			$art_name=stripslashes($art_name);
			$art_descr=stripslashes($art_descr);
		}
	}
}










function page_content()
{
	global $sql_pref, $conn_id, $rub_module;
	global $page_content,$mainpage;

	$page_content.=out_main();

	if (isset($rub_module) && !empty($rub_module))
	{
		if ($rub_module=='map') $page_content.=out_map();
		elseif ($rub_module=='404') $page_content.=out_404();
		elseif ($rub_module=='regonly') $page_content.=out_regonly();
		elseif ($rub_module=='news') { require_once("fns/news.php"); $page_content.=news_main();}
		elseif ($rub_module=='catalog') { require_once("fns/catalog.php"); $page_content.=catalog_main();}
		elseif ($rub_module=='useful') { require_once("fns/useful.php"); $page_content.=useful_main();}
        elseif ($rub_module=='gallery') { require_once("fns/gallery.php"); $page_content.=gallery_main();}
		elseif ($rub_module=='lib') { require_once("fns/lib.php"); $page_content.=lib_main();}
		elseif ($rub_module=='auth')  $page_content.=auth_main();
        elseif ($rub_module=='company_reg') { require_once("fns/company_reg.php"); $page_content.=company_reg_main();}
		elseif ($rub_module=='users') { require_once("fns/users.php"); $page_content.=users_main();}
		elseif ($rub_module=='video') { require_once("fns/video.php"); $page_content.=video_main();}
		elseif ($rub_module=='sites') { require_once("fns/sites.php"); $page_content.=sites_main();}
		elseif ($rub_module=='links') { require_once("fns/links.php"); $page_content.=links_main();}
		elseif ($rub_module=='faq') { require_once("fns/faq.php"); $page_content.=faq_main();}
		elseif ($rub_module=='poll') { require_once("fns/poll.php"); $page_content.=poll_main();}
		elseif ($rub_module=='forum') { require_once("fns/forum.php"); $page_content.=forum_main();}
		elseif ($rub_module=='voc') { require_once("fns/voc.php"); $page_content.=voc_main();}
		elseif ($rub_module=='articles') { require_once("fns/articles.php"); $page_content.=articles_main();}
		elseif ($rub_module=='companies') { require_once("fns/companies.php"); $page_content.=companies_main();}
		elseif ($rub_module=='objects') { require_once("fns/objects.php"); $page_content.=objects_main();}
        elseif ($rub_module=='top_management') { require_once("fns/top_management.php"); $page_content.=top_management_main();}
		elseif ($rub_module=='proposals') { require_once("fns/proposals.php"); $page_content.=proposals_main();}
        elseif ($rub_module=='demand') { require_once("fns/demand.php"); $page_content.=demand_main();}
		elseif ($rub_module=='equipment') { require_once("fns/equipment.php"); $page_content.=equipment_main();}
        elseif ($rub_module=='vacancies') { require_once("fns/vacancies.php"); $page_content.=vacancies_main();}
		elseif ($rub_module=='resume') { require_once("fns/resume.php"); $page_content.=resume_main(); }
		elseif ($rub_module=='picture') { require_once("fns/picture.php"); $page_content.=picture_main(); }
        elseif ($rub_module=='questions_answers') { require_once("fns/questions_answers.php"); $page_content.=questions_answers_main();}
		elseif ($rub_module=='gost') { require_once("fns/gost.php"); $page_content.=gost_main();}
        elseif ($rub_module=='blogs') { require_once("fns/blogs.php"); $page_content.=blogs_main();}
        elseif ($rub_module=='calendar') { require_once("fns/calendar.php"); $page_content.=calendar_main();}
	
	}
	return;
}










function error_404()
{
	header("location:/404/"); exit();
}




function regonly()
{
	header("location:/regonly/"); exit();
}




?>
