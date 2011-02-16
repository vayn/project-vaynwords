<?php
session_start();

require_once('./inc/common.inc.php');

if ((preg_match("/(iphone|ipad|ipod|android)/i", strtolower(VWSCore::UserAgent()))) AND strstr(strtolower(VWSCore::UserAgent()), 'webkit'))
{ 
    header('Location: mobile/');
    exit;
}
else if (trim(VWSCore::UserAgent()) == '' OR preg_match("/(nokia|sony|ericsson|mot|htc|samsung|sgh|lg|philips|lenovo|ucweb|opera mobi|windows mobile|blackberry)/i", strtolower(VWSCore::UserAgent())))
{
    header('Location: mobile/?m');
    exit;
}

require_once 'header.php';

$sql = "SELECT COUNT(DISTINCT vws_words.key) AS id FROM vws_words;";
$res = $db->query($sql);
$row = $db->fetch_array($res);
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
}
else {
     $page = 1;
}

$lists = DisplayCore::indexDisplay();
foreach ($lists as $list) {
    echo $list;
}

if ($pages > 1) {
    echo VWSCore::pagination();
}

require_once 'footer.php';

?>
