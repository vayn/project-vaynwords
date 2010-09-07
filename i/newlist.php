<?php
chdir('../');
require('config.php');
require('vws_functions.php');
require('molib/function.php');

if ($db = mysql_connect($dbhost, $dbuser, $dbpassword)) {
    mysql_select_db($dbdatabase, $db);
    mysql_query("set names 'utf8';");

    $sql = "SELECT count(id) AS id FROM vws_words;";
    $res = mysql_query($sql);
    $row = mysql_fetch_assoc($res);
    $amount = $row['id'];
    $pages = ceil($amount/$vw_perpage);

    if (isset($_GET['page'])) {
        if (($_GET['page'] > 1) && ($_GET['page'] < $pages+1)) {
            $page = (int) $_GET['page'];
        }
        elseif ($_GET['page'] > $pages) {
            header('Location: ?page=' . $pages);
            exit;
        }
        else {
            header('Location: ./');
            exit;
        }
    }
    else {
         $page = 1;
    }

    mobiContent();
}
?>
