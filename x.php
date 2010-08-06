<?php
require('config.php');
require('vws_functions.php');

$dba = mysql_connect($dbhost, $dbuser, $dbpassword);
$dbb = mysql_connect($dbhost, $dbuser, $dbpassword);
mysql_select_db('vws', $dba);
mysql_select_db('z', $dbb);
mysql_query("set names 'utf8';");

$sql = "SELECT * FROM vws.vws_wordlist ORDER BY wl_date  ASC";
$results = mysql_query($sql);

while ($row = mysql_fetch_assoc($results)) {
$w = $row['wl_key'];
//$w = 'apple';
$d = (Cuery($w));
$aWord = array();

if ($d['local'][0]['word'] != '') {
    $key = $d['local'][0]['word'];
    $pho = str_replace("'", "ˈ", $d['local'][0]['pho'][0]);
    $aDes = $d['local'][0]['des'];
    if ($d['local'][0]['des'] != '') $aDes = $d['local'][0]['des'];
    if ($d['local'][0]['sen'] != '') $aSen = $d['local'][0]['sen'];
    if ($d['local'][0]['mor'] != '') $aMor = $d['local'][0]['mor'];

$soundUrl = $row['sound'];
$date = $row['wl_date'];
//$json = file_get_contents("http://www.google.com/dictionary/json?callback=dict_api.callbacks.id100&q={$w}&sl=en&tl=zh&restrict=pr%2Cde&client=te");
//$json = substr($json, strpos($json, "(")+1, -10);
//$json = str_replace("\\", "\\\\", $json);
//$decode = json_decode($json, true);
//$soundUrl = urlencode($decode['primaries'][0]['terms'][2]['text']);
//$date = time();

echo $wsql = "INSERT INTO z.vws_words (`date`, `key`, `pho`, `sound`) VALUES (" . $date . ", '" . $key . "', '" . $pho . "', '" . $soundUrl . "');";
mysql_query($wsql);
$wid = mysql_insert_id();

if ($aDes) {
    for ($i = 0; $i < count($aDes); $i++) {
        $dpos = $aDes[$i]['p'];
        $ddef = $aDes[$i]['d'];
        $dessql = "INSERT INTO z.vws_des (wid, pos, def) VALUES (" . $wid . ", '" . $dpos . "', '" . $ddef . "');";
        mysql_query($dessql);
    }
}

if ($aSen) {
    for ($i = 0; $i < count($aSen); $i++) {
        $spos = $aSen[$i]['p'];
        for ($j = 0; $j < count($aSen[$i]['s']); $j++) {
            $sen_es = $aSen[$i]['s'][$j]['es'];
            $sen_cs = $aSen[$i]['s'][$j]['cs'];
            $sensql = "INSERT INTO z.vws_sen (wid, pos, sen_es, sen_cs) VALUES (" . $wid . ", '" . $spos . "', '" . $sen_es . "', '" . $sen_cs . "');";
            mysql_query($sensql);
        }
    }
}

if ($aMor) {
    for ($i = 0; $i < count($aMor); $i++) {
        $moc = $aMor[$i]['c'];
        $mom = $aMor[$i]['m'];
        $morsql = "INSERT INTO z.vws_mor (wid, c, m) VALUES (" . $wid . ", '" . $moc . "', '" . $mom . "');";
        mysql_query($morsql);
    }
}
}
}
?>
