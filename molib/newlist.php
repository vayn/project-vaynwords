<?php

include_once '../inc/common.inc.php';

$sql = "SELECT count(id) AS id FROM vws_words;";
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

$lists = DisplayCore::mobiDisplay();
foreach ($lists as $list) {
    echo $list;
}

?>
