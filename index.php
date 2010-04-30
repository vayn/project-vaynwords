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
  require('header.php');

  if (file_exists('vws_data.xml')) {
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
