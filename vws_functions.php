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
    $w = urlencode($w);
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

    $vw_perpage = $vw_perpage ? $vw_perpage : 25;
    $page = $page ? $page : 1;

    $db = mysql_connect($dbhost, $dbuser, $dbpassword);
    mysql_select_db($dbdatabase, $db);
    mysql_query("set names 'utf8';");

    $start = ($page==1) ? 0 : (($page - 1) * $vw_perpage) + 1;

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
            $j = $k = $l = 0;

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

            $msql = "SELECT c, m FROM vws_mor WHERE wid=" . $words[$i]['id'] . ";";
            $mres = mysql_query($msql);
             while ($mrow = mysql_fetch_assoc($mres)) {
                $words[$i]['mor'][$l]['c'] = $mrow['c'];
                $words[$i]['mor'][$l]['m'] = $mrow['m'];
                $l++;
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
        $pho = $word['pho'];

        $arr[$tablecount] = '<div class="word"><span id="' . $id . '"></span>' . $key . ' ';
        if ($pho == '' && $mp3 != '') {
            $arr[$tablecount] .= gsound($mp3) . '<br />';
        }
        elseif ($pho != '') {
            if ($mp3 != '') {
                $arr[$tablecount] .= '/' . $pho . '/ ' . gsound($mp3) . '<br />';
            }
            else {
                $arr[$tablecount] .= '/' . $pho . '/ ' . '<br />';
            }
        }

        $defCount = count($word['def']);
        for ($i = 0; $i < $defCount; ++$i) {
            $def = $word['def'][$i]['def'];
            $def_pos = $word['def'][$i]['pos'];
            $arr[$tablecount] .= $def_pos . ' ' . $def . '<br />';
        }

        $senCount = count($word['sen']);
        for ($i = 0; $i < $senCount; ++$i) {
            $senO = $word['sen'][$i]['sen_es'];
            $senT = $word['sen'][$i]['sen_cs'];
            $sen_pos = $word['sen'][$i]['pos'];
             if ($senO != '' || $senT != '') {
                 if ($sen_pos != '') $sen_pos = ' [' . $sen_pos . ']';
                $arr[$tablecount] .= $senO . $sen_pos . '<br />';
                 $arr[$tablecount] .= $senT . '<br />';
            }
        }

        $c = $word['mor'][0]['c'];
        $m = $word['mor'][0]['m'];
        if ($c != '' || $m != '') {
           $arr[$tablecount] .= '[ ' . $c . ': ';
           $arr[$tablecount] .= $m . ' ]';
        }

        $arr[$tablecount] .= '</div>';
        $tablecount++;
    }

    foreach ($arr as $key) {
        echo $key;
    }
    echo pagination();
}

//
// Pagination
//
function pagination() {
    global $page, $pages, $vw_perpage;

    if ($pages > 1) {
        // Assign the "previous" and "next" button
        if ($page > 1) {
            $plink = '<a href="?page=' . ($page - 1) . '">&laquo; Prev</a>';
        }
        if($page > 2) {
            $plink = '<a href="?page=1">&lt;</a> <a href="?page=' . ($page - 1) . '">&laquo; Prev</a>';
        }

        if ($page < ($pages-1)) {
                $nlink = '<a href="?page=' . ($page + 1) . '">Next &raquo;</a> <a href="?page=' . $pages . '">&gt;</a>';
        }
        elseif ($page < $pages) {
            $nlink = '<a href="?page=' . ($page + 1) . '">Next &raquo;</a>';
        }
    }

    // Assign all the page numbers and links to the string
    if ($page < 11) {
        for ($l = 1; $l < 11; $l++) {
            if ($page == $l) {
                $link .= ' <span class="current">' . $l . '</span> '; // If we are on the current page
            }
            else {
                $link .= ' <a href="?page=' . $l . '" class="page">' . $l . '</a> ';
            }
        }
    }
    else {
        for ($l = $page-4; $l < $page+5; $l++) {
            if ($page == $l) {
                $link .= ' <span class="current">' . $l . '</span> ';
            }
            elseif ($l < ($pages+1)) {
                $link .= ' <a href="?page=' . $l . '" class="page">' . $l . '</a> ';
            }
            else break;
        }
    }

    $pagination = '<div id="pagination">' . $plink . $link . $nlink . '</div>';
    return $pagination;
}

function UserAgent() {
    $user_agent = ( ! isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];

    return $user_agent;
}

?>
