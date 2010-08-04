<?php

include_once('config.php');
//include_once('vws_functions.php');

$dba = mysql_connect($dbhost, $dbuser, $dbpassword);
$dbb = mysql_connect($dbhost, $dbuser, $dbpassword);
mysql_select_db('temper', $dba);
mysql_select_db($dbdatabase, $dbb);
mysql_query("set names utf8");

$wsql = "SELECT * FROM temper.vws_wordlist ORDER BY wl_date DESC";
$result = mysql_query($wsql);

$words = array();
$i = 0;

while ($row = mysql_fetch_assoc($result)) {
    $words[$i]['key'] = $row['wl_key'];
    if ($row['wl_date'] == 0) {
        $words[$i]['date'] = strtotime('now')-28800;
    }
    else {
        $words[$i]['date'] = $row['wl_date'];
    }

    $dsql = "INSERT INTO vws.vws_wordlist (wl_key, wl_date) VALUES ('{$words[$i]['key']}', '{$words[$i]['date']}');";
    mysql_query($dsql);

    $i++;
}

$wl_id = 1;
$aMean = array();

foreach ($words as $key => $value) {
    $arr = query_def($value['key']);
    $fsql = "UPDATE vws.vws_wordlist SET label='{$arr['label']}', text='" . str_replace('/', '', $arr['text']) . "', sound='" . $arr['sound'] . "' WHERE vws.vws_wordlist.wl_id={$wl_id};";
    mysql_query($fsql);

    $count = count($arr['pos']);

    for ($nPos = 0; $nPos < $count; $nPos++) {
        $psql = "INSERT INTO vws.vws_pos (w_id, type) VALUES ('"
            . $wl_id . "', '"
            . $arr['pos'][$nPos]['type'] . "');";
        mysql_query($psql);
        $p_id = mysql_insert_id();

        $mCount = count($aMean = $arr['pos'][$nPos]['meaning']);
        for ($j = 0; $j < $mCount; $j++) {
            $msql = "INSERT INTO vws.vws_def (p_id, m_en, m_zh, eg_en, eg_zh) VALUES ('"
                . $p_id . "', '"
                . $aMean[$j]['def'][0] . "', '"
                . $aMean[$j]['def'][1] . "', '"
                . $aMean[$j]['example'][0] . "', '"
                . $aMean[$j]['example'][1] . "');";
            mysql_query($msql);
        }
    }

    $wl_id++;
}

echo "--END--";


?>
