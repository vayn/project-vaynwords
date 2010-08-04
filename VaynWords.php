<?php
/**
 * Author:
 *    Vayn a.k.a. VT <vt@elnode.com>
 *    http://elnode.com
 *
 *    File:             VaynWord.php
 *    Create Date:      2010年 08月 03日 星期二 00:32:21 CST
 */
include_once('config.php');
include_once('TwitterSearch.php');
include_once('vws_functions.php');

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

    $list = array();

    foreach ($results as $key) {
        // Get word from hashtag tweet
        $tweet = substr($key->text, 0, strrpos($key->text, '#'));
        $tweet = explode(',', $tweet);
        $tweet = array_map('trim', $tweet);    

        foreach ($tweet as $word) {
            $word_def = dict_query($word);
            $date = $key->created_at;

            if ($word_def != FALSE) {
                $date = strtotime(substr($date, 0, 25));
                $key = $word_def['key'];
                $pron = addslashes($word_def['pron']);
                $def = addslashes($word_def['def']);
                $orig = addslashes($word_def['sent_o']);
                $trans = addslashes($word_def['sent_t']);

                if ($date > $last_item_timestamp) {
                    $list[$date] =array('key'=>$key,
                                   'date'=>$date,
                                   'pron'=>$pron,
                                   'def'=>$def,
                                   'orig'=>$orig,
                                   'trans'=>$trans);
                }
            }
        }
    }

    krsort($list);

    foreach ($list as $list) {
        $sql = "INSERT INTO vws_wordlist (wl_key, wl_date, wl_pron, wl_def, wl_orig, wl_trans) VALUES(
            '{$list['key']}',
            '{$list['date']}',
            '{$list['pron']}',
            '{$list['def']}',
            '{$list['orig']}',
            '{$list['trans']}');";
        mysql_query($sql);
    }

    echo '--END--';
}
else {
    header('Location: ./');
    exit;
}

?>

