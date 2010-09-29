<?php

include_once './inc/common.inc.php';
include_once './core/TwitterAPI.php';

if ($_GET['pass'] == $vw_password) {
    // Search from Twitter
    $search = new TwitterSearch();
    $search->user_agent = $vw_useragent;

    $results = $search->results($vw_userid);

    $db = mysql_connect($dbhost, $dbuser, $dbpassword);
    mysql_select_db($dbdatabase, $db);
    $db->query("set names 'utf8';");

    // Select latest date from DB
    $tsql = "SELECT date FROM vws_words ORDER BY date DESC LIMIT 0, 1;";
    $tresult = $db->query($tsql);

    if ($row = mysql_fetch_assoc($tresult)) {
        $last_item_timestamp = $row['date'];
    }
    else {
        $last_item_timestamp = 0;
    }

    foreach ($results as $key) {
        if (strpos($key->text, $vw_hashtag)) {
            // Get word from hashtag tweet
            $tweet = substr($key->text, 0, strrpos($key->text, '#'));
            $tweet = explode(',', $tweet);
            $tweet = array_map('trim', $tweet);
            $date = $key->created_at;
            $date = strtotime(substr($date, 0, 25));

            foreach ($tweet as $word) {
                $word = new query_Word($word);
                $dict = new query_qqDict();

                $d = $word->query($dict);

                if ($date > $last_item_timestamp) { // Determine the word from Twitter
                                                    // is newer than the newest word in DB 
                    if ($d['local'][0]['word'] != '') {
                        $key = $d['local'][0]['word'];
                        $pho = str_replace("'", "ˈ", $d['local'][0]['pho'][0]);
                        if ($d['local'][0]['des'] != '') $aDes = $d['local'][0]['des'];
                        if ($d['local'][0]['sen'] != '') $aSen = $d['local'][0]['sen'];
                        if ($d['local'][0]['mor'] != '') $aMor = $d['local'][0]['mor'];

                        // Lookup the sound from Google Dictionary
                        $soundUrl = VWSCore::gSound($word);

                        // Store word, date, phonogram and sound url to DB
                        $wsql = "INSERT INTO vws_words (`date`, `key`, `pho`, `sound`) VALUES (" . $date . ", '" . $key . "', '" . $pho . "', '" . $soundUrl . "');";
                        $db->query($wsql);
                        $wid = $db->insert_id();

                        // Store definiton and part of speech to DB
                        if ($aDes) {
                            for ($i = 0; $i < count($aDes); $i++) {
                                $dpos = $aDes[$i]['p'];
                                $ddef = $aDes[$i]['d'];
                                $dessql = "INSERT INTO vws_des (wid, pos, def) VALUES (" . $wid . ", '" . $dpos . "', '" . $ddef . "');";
                                $db->query($dessql);
                                unset($aDes);
                            }
                        }

                        // Store example sentences (both English and Chinese)
                        // and the part of speech of the word in example sentence
                        // to DB
                        if ($aSen) {
                            for ($i = 0; $i < count($aSen); $i++) {
                                $spos = $aSen[$i]['p'];
                                for ($j = 0; $j < count($aSen[$i]['s']); $j++) {
                                    $sen_es = $aSen[$i]['s'][$j]['es'];
                                    $sen_cs = $aSen[$i]['s'][$j]['cs'];
                                    $sensql = "INSERT INTO vws_sen (wid, pos, sen_es, sen_cs) VALUES (" . $wid . ", '" . $spos . "', '" . $sen_es . "', '" . $sen_cs . "');";
                                    $db->query($sensql);
                                    unset($aSen);
                                }
                            }
                        }

                        // Store morphology to DB
                        if ($aMor) {
                            for ($i = 0; $i < count($aMor); $i++) {
                                $moc = $aMor[$i]['c'];
                                $mom = $aMor[$i]['m'];
                                $morsql = "INSERT INTO vws_mor (wid, c, m) VALUES (" . $wid . ", '" . $moc . "', '" . $mom . "');";
                                $db->query($morsql);
                                unset($aMor);
                            }
                        }

                    }
                }
            }
        }
    }
}
else {
    header('Location: ./');
    exit;
}

?>

