<?php
/*
 * index.php
 *
 * Author: Jixia Lab <vt@elnode.com>
 * Site: http://lab.jixia.org/
 *
 * ver0.4
 * 
 * 04/24/2010
 *
 */

require('config.php');
require('header.php');

if (file_exists('content.php')) {
  require('content.php');
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
