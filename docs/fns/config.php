<?php
$sql_host="localhost";
$sql_login="root";
$sql_passwd="root"; 
$sql_database="ensor_db";



if ((substr(@$_SERVER["DOCUMENT_ROOT"],1,7))==":/home/")
{
	$sql_host="localhost";
	$sql_login="root";
	$sql_passwd="";
	$sql_database="db_ensor";
}


$path=$_SERVER["DOCUMENT_ROOT"]."/";
$path_www="http://".$_SERVER["HTTP_HOST"]."/";
if (substr($_SERVER["HTTP_HOST"],0,4)=="www.") $path_domen=substr($_SERVER["HTTP_HOST"],4); else $path_domen=$_SERVER["HTTP_HOST"];


$sql_pref="ens";
$check_auth="No";

$page_title="���� ��������";
$page_description="������ ���� ���������� ������������ ��� �����������. ���� ������������ ����� ����� ����������������� ������� � �������������� ����������� � ������������ ������� ��������";
$page_keywords="����������, ����������, ensor, �����, �����";

$admin_email="avkuryatov@gmail.com";


$admin[0]["status"]="root";
$admin[0]["login"]="admin";
$admin[0]["password"]="zkdn2ewf";

$admin[1]["status"]="user";
$admin[1]["login"]="moderator2";
$admin[1]["password"]="1qa2ws";
$admin[1]["modules"]=array("news","articles", "companies");



$months_rus=array ("01"=>"������", "02"=>"�������", "03"=>"����", "04"=>"������", "05"=>"���", "06"=>"����", "07"=>"����", "08"=>"������", "09"=>"��������", "10"=>"�������", "11"=>"������", "12"=>"�������");
$months_rus1=array ("01"=>"������", "02"=>"�������", "03"=>"�����", "04"=>"������", "05"=>"���", "06"=>"����", "07"=>"����", "08"=>"�������", "09"=>"��������", "10"=>"�������", "11"=>"������", "12"=>"�������");
$months_rus2=array ("1"=>"������", "2"=>"�������", "3"=>"����", "4"=>"������", "5"=>"���", "6"=>"����", "7"=>"����", "8"=>"������", "9"=>"��������", "10"=>"�������", "11"=>"������", "12"=>"�������");



$path_companies="kb/companies";
$companies_perpage=30;
$companies_img_width=300;$companies_img_height=200;
$companies_img_thumb_width=80;$companies_img_thumb_height=80;

$path_equipment="kb/equipment";
$equipment_perpage=30;
$equipment_img_width=300;$equipment_img_height=200;
$equipment_img_thumb_width=80;$equipment_img_thumb_height=80;
$equipment_catalog_img_width=80; $equipment_catalog_img_height=80;

$path_useful="kb/useful";
$useful_perpage=30;
$useful_img_width=300;$useful_img_height=200;
$useful_img_thumb_width=80;$useful_img_thumb_height=80;
$useful_useful_img_width=80; $useful_useful_img_height=80;

$path_objects="kb/objects";
$objects_perpage=30;
$objects_img_width=300;$objects_img_height=200;
$objects_img_thumb_width=80;$objects_img_thumb_height=80;



$path_news="discussions/news";
$news_perpage=10;
$news_img_width=200;$news_img_height=150;
$news_img_thumb_width=80;$news_img_thumb_height=80;

$path_articles="discussions/articles";
$articles_perpage=10;
$articles_img_width=200;$articles_img_height=150;
$articles_img_thumb_width=80;$articles_img_thumb_height=80;


$path_users="users";
$users_perpage=30;
$users_img_width=300;$users_img_height=300;
$users_avatar_width=80;$users_avatar_height=80;


$path_top_management="top_management";
$top_management_perpage=30;
$top_management_img_width=300;$top_management_img_height=300;
$top_management_avatar_width=80;$top_management_avatar_height=80;

$path_forum="discussions/forum";
$posts_perpage=20;
$topics_perpage=20;

$path_contacts="discussions/our_contacts";

$path_proposals="add_job/proposals";
$proposals_perpage=20;

$path_demand="add_job/demand";
$demand_perpage=20;

$path_vacancies="main_job/vacancies";
$vacancies_perpage=10;

$path_questions="discussions/questions_answers";
$questions_answers_perpage=10;

$path_blogs="discussions/blogs";
$posts_perpage=10;
$blogs_posts_perpage=10;

$path_links="kb/links";
$links_exchange="Yes";
$links_email=$admin_email;
$links_phrase="�������������� ���������� ������";
$links_perpage=10;
$links_send_email=$admin_email;

$path_resume="main_job/resume";

$path_groups="auth/groups_list";

$path_picture="raznoe/picture";
$picture_perpage=15;

$path_gost="kb/gost";
$gost_perpage=40;


//���������� ���������
$path_calendar="auth/calendar";
$table_dnp_news = "ens_events";
$calendar_email = $admin_email;

$news_num = 7;
// ���� "on" , �� ��� ����������� ���� ��������� � ���������
// ������� �� �����, ��������� � �.�., ������������ $news_num (��. ����)
$start_news = "on"; // on ��� off
// ���� "on" , �� �� ��������� �������� ����������� �������� �������
$news_comon = "off"; // on ��� off

?>