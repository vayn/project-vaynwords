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

    $tsql = "SELECT date FROM vws_words ORDER BY date DESC LIMIT 0, 1;";
    $tresult = mysql_query($tsql);

    if ($row = mysql_fetch_assoc($tresult)) {
        $last_item_timestamp = $row['date'];
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
            $d = Cuery($word);
            $date = $key->created_at;
            $date = strtotime(substr($date, 0, 25));

            if ($date > $last_item_timestamp) {
                if ($d['local'][0]['word'] != '') {
                    $key = $d['local'][0]['word'];
                    $pho = str_replace("'", "ˈ", $d['local'][0]['pho'][0]);
                    $aDes = $d['local'][0]['des'];
                    if ($d['local'][0]['des'] != '') $aDes = $d['local'][0]['des'];
                    if ($d['local'][0]['sen'] != '') $aSen = $d['local'][0]['sen'];
                    if ($d['local'][0]['mor'] != '') $aMor = $d['local'][0]['mor'];

                    $json = file_get_contents("http://www.google.com/dictionary/json?callback=dict_api.callbacks.id100&q={$w}&sl=en&tl=zh&restrict=pr%2Cde&client=te");
                    $json = substr($json, strpos($json, "(")+1, -10);
                    $json = str_replace("\\", "\\\\", $json);
                    $decode = json_decode($json, true);
                    $soundUrl = $decode['primaries'][0]['terms'][2]['text'];

                    $wsql = "INSERT INTO vws_words (`date`, `key`, `pho`, `sound`) VALUES (" . $date . ", '" . $key . "', '" . $pho . "', '" . $soundUrl . "');";
                    mysql_query($wsql);
                    $wid = mysql_insert_id();

                    if ($aDes) {
                        for ($i = 0; $i < count($aDes); $i++) {
                            $dpos = $aDes[$i]['p'];
                            $ddef = $aDes[$i]['d'];
                            $dessql = "INSERT INTO vws_des (wid, pos, def) VALUES (" . $wid . ", '" . $dpos . "', '" . $ddef . "');";
                            mysql_query($dessql);
                        }
                    }

                    if ($aSen) {
                        for ($i = 0; $i < count($aSen); $i++) {
                            $spos = $aSen[$i]['p'];
                            for ($j = 0; $j < count($aSen[$i]['s']); $j++) {
                                $sen_es = $aSen[$i]['s'][$j]['es'];
                                $sen_cs = $aSen[$i]['s'][$j]['cs'];
                                $sensql = "INSERT INTO vws_sen (wid, pos, sen_es, sen_cs) VALUES (" . $wid . ", '" . $spos . "', '" . $sen_es . "', '" . $sen_cs . "');";
                                mysql_query($sensql);
                            }
                        }
                    }

                    if ($aMor) {
                        for ($i = 0; $i < count($aMor); $i++) {
                            $moc = $aMor[$i]['c'];
                            $mom = $aMor[$i]['m'];
                            $morsql = "INSERT INTO vws_mor (wid, c, m) VALUES (" . $wid . ", '" . $moc . "', '" . $mom . "');";
                            mysql_query($morsql);
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

