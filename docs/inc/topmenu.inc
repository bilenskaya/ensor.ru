<?
if ($rub_id[0]!=7 && $rub_id[0]!=8) {$menu1class="topmenuactive"; $menu1bg="menu-active.gif";} else {$menu1class="topmenunotactive"; $menu1bg="menu-inactive.gif";}
if ($rub_id[0]==7) {$menu2class="topmenuactive"; $menu2bg="menu-active.gif";} else {$menu2class="topmenunotactive"; $menu2bg="menu-inactive.gif";}
if ($rub_id[0]==8) {$menu3class="topmenuactive"; $menu3bg="menu-active.gif";} else {$menu3class="topmenunotactive"; $menu3bg="menu-inactive.gif";}
if ($rub_id[1]==33) $menu4class="top2menuactive"; else $menu4class="top2menunotactive";
if ($rub_id[1]==15) $menu5class="top2menuactive"; else $menu5class="top2menunotactive";
if ($rub_id[0]==4) $menu6class="top2menuactive"; else $menu6class="top2menunotactive";
if ($rub_id[0]==23) $menu7class="top2menuactive"; else $menu7class="top2menunotactive";
if ($rub_id[1]==14) $menu8class="top2menuactive"; else $menu8class="top2menunotactive";

//echo $menu6class;
?>

<table  border="0" cellpadding="0" cellspacing="0" height="30">
<tr>
	<td background="/img_new/int/<?echo $menu1bg?>" class="<? echo $menu1class;?>" width="177"><a class="topmenu" href="/"><div class="<? echo $menu1class;?>"><b>ЭНЕРГЕТИКАМ</b></div></a></td>
    <td width="5"></td>
	<td background="/img_new/int/<?echo $menu2bg?>" class="<? echo $menu2class;?>" width="177"><a class="topmenu" href="/rabotodateljam/"><div class="<? echo $menu2class;?>"><b>ОРГАНИЗАЦИЯМ</b></div></a></td>
    <td width="5"></td></td>
	<td background="/img_new/int/<?echo $menu3bg?>" class="<? echo $menu3class;?>" width="177"><a class="topmenu" href="/partners/"><div class="<? echo $menu3class;?>"><b>ПАРТНЕРАМ</b></div></a></td>
    <td></td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width=100% height="37">
<tr class="top2menuactive">
    <td width="6"><img src="/img_new/int/menu-left.gif" border="0" width="6" height="37" /></td>
	<td class="<? echo $menu4class;?>" width=16% align="center"><a class="topmenu" href="/discussions/blogs/"><div class="<?echo $menu4class;?>">Блоги</div></a></td>
    <td ><img src="/img_new/int/menu-div.gif" /></td>
    <td class="<? echo $menu5class;?>" width=16% align="center"><a class="topmenu" href="/discussions/forum/"><div class="<?echo $menu5class;?>">Форум</div></a></td>
    <td ><img src="/img_new/int/menu-div.gif" /></td>
    <td class="<? echo $menu6class;?>" width=16% align="center"><a class="topmenu" href="/kb/"><div class="<?echo $menu6class;?>">Каталоги</div></a></td>
    <td ><img src="/img_new/int/menu-div.gif" /></td>
    <td class="<? echo $menu7class;?>" width=16% align="center"><a class="topmenu" href="/main_job/"><div class="<?echo $menu7class;?>">Работа</div></a></td>
    <td ><img src="/img_new/int/menu-div.gif" /></td>
    <td class="<? echo $menu8class;?>" width=16% align="center"><a class="topmenu" href="/discussions/questions_answers/"><div class="<?echo $menu8class;?>">Вопросы/Ответы</div></a></td>
     <td background="/img_new/int/menu-bg2.gif" bgcolor="#EB5C3C" width="114"><a href="/"><img src="/img/int/menu-home.jpg"  border="0" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:avkuryatov@ensor.ru"><img src="/img/int/menu-mail.jpg"  border="0" /></a></td>
    <td width="6" background="/img_new/int/menu-bg2.gif"><img src="/img_new/int/menu-right.gif" border="0" width="6" height="37" /></td>
		
</tr>
</table>