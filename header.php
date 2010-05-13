<?php
/**
 * Author:
 *    Vayn a.k.a. VT <vt@elnode.com>
 *    http://elnode.com
 *
 *    File:             header.php
 *    Create Date:      2010年04月30日 星期五 05时58分42秒
 */
?>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" /> 
  <meta name="generator" content="http://lab.jixia.org/" />
  <meta http-equiv="cache-control" content="no-cache" />
  <title><?php echo $vw_sitename; ?> - Project Vaynwords</title>
<?php
  if (file_exists('vws_data.xml') && (basename($_SERVER['SCRIPT_NAME']) == 'index.php')) {
    echo '  <link rel="stylesheet" type="text/css" media="screen" href="style.css" />' . "\n";
  }
  else {
    echo '  <link rel="stylesheet" type="text/css" media="screen" href="img/err.css" />' . "\n"; 
  }
?>
  <link rel="shortcut icon" href="favicon.ico" />
  <link rel="alternate" type="application/rss+xml" href="feed/" />
  <script type="text/javascript" src="js/scroll.js"></script>
</head>
<body>
<div id="top"></div>
<?php
if (file_exists('vws_data.xml') && (basename($_SERVER['SCRIPT_NAME']) == 'index.php')) {
?>  
<div id="header"><h1><?php echo $vw_sitename; ?> <a href="http://twitter.com/<?php echo $vw_username; ?>" title="Follow me on twitter"><img src="img/twitter_bird.png" alt="<?php $vw_username; ?>" style="vertical-align:text-bottom" /></a></h1></div>
<div id="main" class="clearfix">
<?php
}
?>

