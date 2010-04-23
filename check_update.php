<?php

session_start();

$_SESSION['SESS_QUERY'] = 'Project_Vaynwords'; 

require('./config.php');
require('./TwitterSearch.php');

$search = new TwitterSearch();
$search->user_agent = $vw_useragent;

$search->from($vw_username)->with($vw_hashtag);
$results = $search->rpp(1)->results();

$new_update = substr($results[0]->text, 0, strrpos($results[0]->text, '#'));

if (file_exists('status.php')) {
  if (($fp = fopen('status.php', 'r')) == TRUE) {
    $old_update = fread($fp, filesize('status.php'));
    fclose($fp);
  }
  else {
    die('Failed to open file for writing!');
  }

  if ($new_update != $old_update) {
    if (($fp = fopen('status.php', 'w')) == TRUE) {
      fwrite($fp, $new_update);
      fclose($fp);
      header('Location: VaynWords.php');
      exit;
    }
    else {
      die('Failed to open file for writing!');
    }
  }
  else {
    require('header.php');
?>
<div id="main">
<div id="whale_error"><img src="./img/whale_error.gif" alt="No new tweets." /></div>
<?php
    require('footer.php');
  }
}
else{
  if (($fp = fopen('status.php', 'w')) == TRUE) {
    fwrite($fp, $new_update);
    fclose($fp);
    header('Location: VaynWords.php');
    exit;
  }
  else {
    die('Failed to open file for writing!');
  }
}

?>
