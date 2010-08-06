<?php
/*
 * check_update.php
 *
 * Author: Jixia Lab <vt@elnode.com>
 * Site: http://lab.jixia.org/
 *
 * ver0.4
 * 
 * 04/24/2010
 *
 */
  
session_start();
  
$_SESSION['SESS_QUERY'] = 'Project_Vaynwords'; 
  
require('./config.php');
require('./TwitterSearch.php');
  
$search = new TwitterSearch();
$search->user_agent = $vw_useragent;
  
$search->from($vw_username)->with($vw_hashtag);
$results = $search->rpp(1)->results();

if(count($results) == 0) {
  session_destroy();
  require('header.php');
?>
<div id="header">
  <h1>No new tweets.</h1>
</div>
<div id="main">
  <div id="whale_error"><img src="./img/whale_error.gif" alt="No new tweets." /></div>
<?php
  require('footer.php');
}
else {
  $new_update = substr($results[0]->text, 0, strrpos($results[0]->text, '#'));
  
  if (file_exists('status.php') && (filesize('status.php')<>0)) {
    if (($fp = fopen('status.php', 'r')) == TRUE) {
      $old_update = fread($fp, filesize('status.php'));
      fclose($fp);
    }
    else {
      session_destroy();
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
        session_destroy();
        die('Failed to open file for writing!');
      }
    }
    else {
      require('header.php');
  ?>
  <div id="header">
  	<h1>No new tweets.</h1>
  </div>
  <div id="main">
  <div id="whale_error"><img src="./img/whale_error.gif" alt="No new tweets." /></div>
  <?php
      require('footer.php');
      session_destroy();
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
      session_destroy();
      die('Failed to open file for writing!');
    }
  }
}
  
  ?>