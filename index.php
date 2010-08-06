<?php
/**
 * Author:
 *    Vayn a.k.a. VT <vt@elnode.com>
 *    http://elnode.com
 *
 *    File:             index.php
 *    Create Date:      2010年04月30日 星期五 05时21分23秒
 */
require('config.php');
require('vws_functions.php');

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

    require('header.php');
    generate_content();
}
else {
  ?>
<div id="header"><h1>Please initialize your site. <a href="http://twitter.com/<?php echo $vw_username; ?>" title="Follow me on twitter"><img src="img/twitter_bird.png" alt="<?php $vw_username; ?>" style="vertical-align:text-bottom" /></a></h1></div>
<div id="main">
<div id="whale_error"><img src="./img/whale_error.gif" alt="Oops." /></div>
<?php
}
require('footer.php');
?>
