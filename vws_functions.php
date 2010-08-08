<?php
/**
 * Author:
 *    Vayn a.k.a. VT <vt@elnode.com>
 *    http://elnode.com
 *
 *    File:         vws_functions.php
 *    Create Date:    2010年 05月 13日 星期四 18:43:21 CST
 */

//
// 从 dict.cn 获得单词音标、解释、中英文例句
//
function dict_query($value_1) {
    $xml = simplexml_load_file('http://api.dict.cn/ws.php?utf8=true&q=' . $value_1);
    if (!($xml->def == 'Not Found')) {
        $arr['key'] = $xml->key;
        $arr['pron'] = $xml->pron;
        $arr['def'] = $xml->def;
        $arr['sent_o'] = $xml->sent->orig;
        $arr['sent_t'] = $xml->sent->trans;

        return $arr;
    }
    else {
        return FALSE;
    }
}

//
// 从 QQ 获得单词数据
//
function Cuery($w) {
    $json = file_get_contents("http://dict.qq.com/dict?q={$w}");
    return $decode = json_decode($json, true);
}

//
// Google Dictionary 单词发音
//
function gsound($soundUrl) {
    $ueUrl = urlencode($soundUrl);

    $playcode =<<<EOF
<object data="http://www.google.com/dictionary/flash/SpeakerApp16.swf" type="application/x-shockwave-flash" id="pronunciation" height="16" width=" 16">
<param name="movie" value="http://www.google.com/dictionary/flash/SpeakerApp16.swf">
<param name="flashvars" value="sound_name={$ueUrl}">
<param name="wmode" value="transparent">
<a href="{$soundUrl}"><img src="http://www.google.com/dictionary/flash/SpeakerOffA16.png" alt="发音" height="16" width="16" border="0"></a>
</object>
EOF;
    return $playcode;
}

//
// Get word data from database
//
function pullword() {
    global $vw_perpage, $page, $dbhost, $dbuser, $dbpassword, $dbdatabase;

    $db = mysql_connect($dbhost, $dbuser, $dbpassword);
    mysql_select_db($dbdatabase, $db);
    mysql_query("set names 'utf8';");

    $start = ($page==1) ? $start = 0 : $start = (($page - 1) * $vw_perpage) + 1;

    $words = array();
    $wsql = "SELECT * FROM vws_words ORDER BY `date` DESC LIMIT {$start}, {$vw_perpage};";
    $wres = mysql_query($wsql);
    $wnum = mysql_num_rows($wres);
    $i = 0;

    if ($wnum) {
        while ($wrow = mysql_fetch_assoc($wres)) {
            $words[$i] = array('id'=>$wrow['id'], 'date'=>$wrow['date'], 'key'=>$wrow['key'], 'pho'=>$wrow['pho'], 'sound'=>$wrow['sound']);
            $dsql = "SELECT pos, def FROM vws_des WHERE wid=" . $words[$i]['id'] . ";";
            $dres = mysql_query($dsql);
            $j = $k = 0;

            while ($drow = mysql_fetch_assoc($dres)) {
                $words[$i]['def'][$j]['pos'] = $drow['pos'];
                $words[$i]['def'][$j]['def'] = $drow['def'];
                $j++;
            }

            $ssql = "SELECT pos, sen_es, sen_cs FROM vws_sen WHERE wid=" . $words[$i]['id'] . ";";
            $sres = mysql_query($ssql);
             while ($srow = mysql_fetch_assoc($sres)) {
                $words[$i]['sen'][$k]['pos'] = $srow['pos'];
                $words[$i]['sen'][$k]['sen_es'] = $srow['sen_es'];
                $words[$i]['sen'][$k]['sen_cs'] = $srow['sen_cs'];
                $k++;
            }
            $i++;
        }
    }

    return $words;
}

//
// Generate words table
//
function generate_content() {
    $words = pullword();
    $tablecount = 0;

    foreach ($words as $word) {
        $id = $word['id'];
        $key = $word['key'];
        $pho = $word['text'];
        $mp3 = $word['sound'];
        $def = $word['def'][0]['def'];
        $pho = $word['pho'];
        $sent_o = $word['sen'][0]['sen_es'];
        $sent_t = $word['sen'][0]['sen_cs'];

        $arr[$tablecount] = '<table class="word_fleet" cellspacing="2">';
        $arr[$tablecount] .= '<tr>';
        $arr[$tablecount] .= '<td class="word_box_s">';
        $arr[$tablecount] .= $key . '<span id="' . $id . '"></span>';
        $arr[$tablecount] .= '</td>';
        if ($pho == '' && $mp3 != '') {
            $arr[$tablecount] .='<td class="word_box_s">' . gsound($mp3) . '</td>';
        }
        elseif ($pho != '') {
            if ($mp3 != '') {
                $arr[$tablecount] .= '<td class="word_box_s">/' . $pho . '/ ' . gsound($mp3) . '</td>';
            }
            else {
                $arr[$tablecount] .= '<td class="word_box_s">/' . $pho . '/ ' . '</td>';
            }
        }
        $arr[$tablecount] .= '<td class="word_box_s">' . $def . '</td>';
        $arr[$tablecount] .= '</tr>';

        if ($sent_o != '' || $sent_t != '') {
            $arr[$tablecount] .= '<tr><td class="word_box_l" colspan=3>' . $sent_o . '</td></tr>';
            $arr[$tablecount] .= '<tr><td class="word_box_l" colspan=3>' . $sent_t;
            if (($i%5 == 0) && ($i != 0)) {
                $arr[$tablecount] .= '<a href="#top" title="Back to top"><div class="back">&uarr;<div></a>';
            }
            $arr[$tablecount] .= '</td></tr>';
        }
        $arr[$tablecount] .= '</table>';
        $tablecount++;
    }

    $show = pagination($arr);

    foreach ($show as $key) {
        echo $key;
    }
}

//
// Pagination
//
function pagination($aContent) {
    global $page, $pages, $vw_perpage;

    if ($pages > 1 && $page > 1) {
        // Assign the previous page
        $plink = '<a href="?page=' . ($page - 1) . '">&laquo; Prev</a>';
        if ($page < $pages) {
             $nlink = '<a href="?page=' . ($page + 1) . '">Next &raquo;</a>';
        }
    }
    else {
        $nlink = '<a href="?page=' . ($page + 1) . '">Next &raquo;</a>';
    }

        // Assign all the page numbers and links to the string
        for ($l = 1; $l < $pages+1; $l++) {
            if ($page == $l) {
                $link .= ' <span class="current">' . $l . '</span> '; // If we are on the current page
            }
            else {
                $link .= ' <a href="?page=' . $l . '" class="page">' . $l . '</a> ';
            }
        }

        $aContent[] = '<div id="pagination">' . $plink . $link . $nlink . '</div>';
        return $aContent;
}

?>