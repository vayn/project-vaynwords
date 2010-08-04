<?php
require("config.php");

$data = simplexml_load_file('trans.xml');

$db = mysql_connect($dbhost, $dbuser, $dbpassword);
mysql_select_db($dbdatabase, $db);
mysql_query("set names 'utf8';");

foreach ($data->word as $word) {
    $date = $word->date;
    $date = strtotime(substr($date, 0, 25));
    $key = $word->key;
    $pron = addslashes($word->defs->pron);
    $def = addslashes($word->defs->def);
    $orig = addslashes($word->defs->sent->orig);
    $trans = addslashes($word->defs->sent->trans);

    $sql = "INSERT INTO vws_wordlist (wl_key, wl_date, wl_pron, wl_def, wl_orig, wl_trans) VALUES(
        '$key',
        '$date',
        '$pron',
        '$def',
        '$orig',
        '$trans');";
    $result = mysql_query($sql);
}

?>
