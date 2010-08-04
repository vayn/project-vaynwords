<?php
require('config.php');
require('vws_functions.php');

$db = mysql_connect($dbhost, $dbuser, $dbpassword);
mysql_select_db('vws', $db);
mysql_query("set names utf8");

//$sql = "SELECT vws_pos.id, vws_pos.w_id, vws_wordlist.wl_key FROM vws_wordlist, vws_pos WHERE vws_pos.w_id=vws_wordlist.wl_id AND vws_pos.type='' ORDER BY vws_wordlist.wl_id ASC;";
$sql = "SELECT vws_pos.id, vws_wordlist.wl_key FROM vws_pos, vws_wordlist WHERE vws_pos.w_id=vws_wordlist.wl_id AND vws_pos.type='See also:';";

$result = mysql_query($sql);

while ($row = mysql_fetch_assoc($result)) {
    $arr = query_def($row['wl_key']);
    print_r($arr);
    echo "<br/>";

    $count = count($arr['pos']);

    for ($i = 0; $i < $count; $i++) {
        if ($i == 0) {
            $fsql = "UPDATE vws_pos SET type='"
                . $arr['pos'][0]['type']
                . "' WHERE vws_pos.id="
                . $row['id'] . ";";
            mysql_query($fsql);
            $i++;
        }
        else {
           $fsql = "INSERT INTO vws_pos (w_id, type) VALUES ({$row['w_id']}, '{$arr['pos'][$i]['type']}');";
           $i++;
        }
    }
}

?>
