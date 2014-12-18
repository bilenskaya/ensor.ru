<?php

/******************************************************************************
 *                                                                            *
 *   preview.php, v 0.02 2007/07/25 - This is part of xBB library             *
 *   Copyright (C) 2006-2007  Dmitriy Skorobogatov  dima@pc.uz                *
 *                                                                            *
 *   This program is free software; you can redistribute it and/or modify     *
 *   it under the terms of the GNU General Public License as published by     *
 *   the Free Software Foundation; either version 2 of the License, or        *
 *   (at your option) any later version.                                      *
 *                                                                            *
 *   This program is distributed in the hope that it will be useful,          *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of           *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
 *   GNU General Public License for more details.                             *
 *                                                                            *
 *   You should have received a copy of the GNU General Public License        *
 *   along with this program; if not, write to the Free Software              *
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA *
 *                                                                            *
 ******************************************************************************/

header('Content-type: text/html; charset=cp1251');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=cp1251" />
<link href="/styles.css" rel="stylesheet" type="text/css" />
<link href="/style_bb.css" rel="stylesheet" type="text/css" />
</head>
<body>

<br><br>
<h1>Предпросмотр</h1>
<table cellpadding=2 cellspacing=2 border=0 height=90%>
	<tr>
		<td>

<?php
$text = (isset($_POST['xbb_textarea'])) ? $_POST['xbb_textarea'] : '';
if (get_magic_quotes_gpc()) { $text = stripslashes($text); }

require_once './bbcode.lib.php';
$bb = new bbcode($text);
echo $bb->get_html();
?>

		</td>
	</tr>
</table>

<div align=center><span onclick="window.close()" style='color: #999999; cursor: pointer;'><u>Закрыть окно</u></span></div><br><br>

</body>
</html>
