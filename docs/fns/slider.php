<?php 
$ref_table="ens_news";
$ref_head="name";
$ref_text="content";
$count=4;
$limitations="enable='Yes'&&main='Yes'";



function str_limit($str,$len){
	$slen=strlen($str);
	$str=substr($str,0,$len);
	if ($slen>$len) $str.="...";
return $str;
}


$sql_query="SELECT id, ".$ref_head.", ".$ref_text." FROM ".$ref_table." WHERE (".$limitations.")ORDER BY code LIMIT ".$count."";
$sql_res=mysql_query($sql_query, $conn_id);
if ($sql_res=mysql_query($sql_query, $conn_id) and mysql_num_rows($sql_res)>0){
	
	$divs="";
	echo "<div id=\"featured\">";
	echo "<ul class=\"ui-tabs-nav\">";
	$fragment=0;
	while(list($id, $head, $text)=mysql_fetch_row($sql_res)){
	$head=str_limit($head,70);
	$text=str_limit($text,90);
	$fragment++;
	if($fragment==1)
    { 
        echo "<li class=\"ui-tabs-nav-item ui-tabs-selected\" id=\"nav-fragment-".$fragment."\"><a href=\"#fragment-".$fragment."\"><img src=\"img/int/image".$fragment."-small.jpg\" alt=\"\" /><span class=\"slider_span\">".$head."</span></a></li>";
	}
    else
    {
        echo "<li class=\"ui-tabs-nav-item\" id=\"nav-fragment-".$fragment."\"><a href=\"#fragment-".$fragment."\"><img src=\"img/int/image".$fragment."-small.jpg\" alt=\"\" /><span class=\"slider_span\">".$head."</span></a></li>";
    }
    
	$divs.="<div id=\"fragment-".$fragment."\" class=\"ui-tabs-panel\" style=\"\">
			<img width=360 height=250 src=\"img/int/image".$fragment.".jpg\" alt=\"\" />
			 <div class=\"info\" >
				<h2><a href=\"#\" >".$head."</a></h2>
				<p>".$text."...<a href=\"#\" >читать далее</a></p>
			 </div>
	    </div>";
}
	echo "</ul>";
	echo $divs;
echo "</div>";
}
?>