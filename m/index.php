<?php
/**
 * Author:
 *    Vayn a.k.a. VT <vt@elnode.com>
 *    http://elnode.com
 *
 *    File:             index.php
 *    Create Date:      2010年04月30日 星期五 05时21分23秒
 */
chdir('../');

require('config.php');
require('vws_functions.php');
require('m/function.php');

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

    require('m/header.php');
    mobiContent();
    require('m/footer.php');
}
else {
    echo 'There is something wrong.';
}

?>
