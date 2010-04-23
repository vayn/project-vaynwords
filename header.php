<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" /> 
  <meta name="generator" content="http://lab.jixia.org/" />
  <meta http-equiv="cache-control" content="no-cache" />
  <title><?php echo $vw_sitename; ?> - Project Vaynwords</title>
  <link rel="stylesheet" type="text/css" media="screen" href="style.css" />
  <link rel="shortcut icon" href="favicon.ico" />
</head>
<body>
<?php
if (basename($_SERVER['SCRIPT_NAME']) == 'index.php') {
?>  
<div id="header"><h1><?php echo $vw_sitename; ?> <a href="http://twitter.com/<?php echo $vw_username; ?>" title="Follow me on twitter"><img src="img/twitter_bird.png" alt="<?php $vw_username; ?>" style="vertical-align:text-bottom" /></a></h1></div>
<div id="main">
<?php
}
?>
