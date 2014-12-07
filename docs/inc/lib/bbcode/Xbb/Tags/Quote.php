<?php

/******************************************************************************
 *                                                                            *
 *   Quote.php, v 0.01 2007/04/29 - This is part of xBB library               *
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

// ÐšÐ»Ð°ÑÑ Ð´Ð»Ñ Ñ‚ÐµÐ³Ð¾Ð² [quote] Ð¸ [blockquote]
class Xbb_Tags_Quote extends bbcode {
    public $rbr = 1;
    function get_html($tree = null) {
		global $path_forum;

/*
        if ('blockquote' == $this->tag) {
            $author = htmlspecialchars($this->attrib['blockquote']);
        } else {
            $author = htmlspecialchars($this->attrib['quote']);
        }
*/
        $ainfo="";
				
				$author = isset($this -> attrib['author']) ? $this -> attrib['author'] : '';
        if ($author) { $ainfo .= htmlspecialchars($author).' '; }
				
				$date = isset($this -> attrib['date']) ? $this -> attrib['date'] : '';
        if ($date) { $ainfo .= '('.htmlspecialchars($date).') '; }
				
				$wrote="íàïèñàë(à)";
				$post = isset($this -> attrib['post']) ? $this -> attrib['post'] : '';
        if ($post) { $wrote = '<a href="/'.$path_forum.'/post/'.$post.'.html">'.$wrote.'</a>'; }
				
				if (!empty($ainfo)) $ainfo.=" ".$wrote.":";

        if (!empty($ainfo)) {
            $ainfo = '<div class="bb_quote_author">' . $ainfo . '</div>';
        }
        return '<blockquote class="bb_quote">' . $ainfo
            . parent::get_html($this -> tree) . '</blockquote>';
    }
}
?>
