<?php
/**
 * Author:
 *    Vayn a.k.a. VT <vt@elnode.com>
 *    http://elnode.com
 *
 *    File:             VaynWord.php
 *    Create Date:      2010年 08月 03日 星期二 00:32:21 CST
 */
require('config.php');
require('TwitterSearch.php');
require('vws_functions.php');

if ($_GET['pass'] == $vw_password) {
    // Search from Twitter
    $search = new TwitterSearch();
    $search->user_agent = $vw_useragent;

    $results = $search->from($vw_username)->with($vw_hashtag)->results();

    $db = mysql_connect($dbhost, $dbuser, $dbpassword);
    mysql_select_db($dbdatabase, $db);
    mysql_query("set names 'utf8';");

    $tsql = "SELECT wl_date FROM vws_wordlist ORDER BY wl_date DESC LIMIT 0, 1;";
    $tresult = mysql_query($tsql);

    if ($row = mysql_fetch_assoc($tresult)) {
        $last_item_timestamp = $row['wl_date'];
    }
    else {
        $last_item_timestamp = 0;
    }

    foreach ($results as $key) {
        // Get word from hashtag tweet
        $tweet = substr($key->text, 0, strrpos($key->text, '#'));
        $tweet = explode(',', $tweet);
        $tweet = array_map('trim', $tweet);

        foreach ($tweet as $word) {
            $aMeaning = gdict_query($word);
            $date = $key->created_at;

            if ($aMeaning != FALSE) {
                $date = strtotime(substr($date, 0, 25));

                if ($date > $last_item_timestamp) {
                    $wsql = "INSERT INTO vws_wordlist (wl_key, wl_date, label, text, sound) VALUES ('"
                        . $aMeaning['key'] . "', "
                        . $date . ", '"
                        . $aMeaning['label'] . "', '"
                        . str_replace('/', '', $aMeaning['text']) . "', '"
                        . $aMeaning['sound'] . "');";
                    mysql_query($wsql);
                    $wid =mysql_insert_id();
                    $count = count($aMeaning['pos']);

                    for ($nPos = 0; $nPos < $count; $nPos++) {
                        $psql = "INSERT INTO vws_pos (wid, type) VALUES (" . $wid . ", '" . $aMeaning['pos'][$nPos]['type'] . "');";
                        mysql_query($psql);
                        $pid = mysql_insert_id();

                        $mCount = count($aMean = $aMeaning['pos'][$nPos]['meaning']);
                        for ($j = 0; $j < $mCount; $j++) {
                            $msql = "INSERT INTO vws_def (pid, m_en, m_zh, eg_en, eg_zh) VALUES ("
                                . $pid . ", '"
                                . $aMean[$j]['def'][0] . "', '"
                                . $aMean[$j]['def'][1] . "', '"
                                . $aMean[$j]['example'][0] . "', '"
                                . $aMean[$j]['example'][1] . "');";
                            mysql_query($msql);
                        }
                    }
                }
            }
        }
    }

    echo '--END--';
}
else {
    header('Location: ./');
    exit;
}

?>

