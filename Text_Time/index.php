<?php
/*
 * index.php
 *
 * Author: Jixia Lab <vt@elnode.com>
 * Site: http://lab.jixia.org/
 *
 * ver0.4.6
 * 
 * 04/28/2010
 *
 */

require('config.php');
require('header.php');

if (file_exists('content.php')) {
  if (($fp = fopen('content.php', 'r')) == TRUE) {
    $content = fread($fp, filesize('content.php'));
    fclose($fp);
  }
  else {
    die('Failed to open file for writing!');
  }
  echo gzuncompress($content);
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
