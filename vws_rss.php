<?php

require('config.php');
require('vws_functions.php');

$site = 'http://' . $_SERVER['SERVER_NAME'] . substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));

$db = mysql_connect($dbhost, $dbuser, $dbpassword);
mysql_select_db($dbdatabase, $db);
mysql_query("set names 'utf8';");

$rsshead =<<<XSL
<?xml version="1.0" encoding="UTF-8"?>
<!-- Create by Vayn@JxLab -->
<rss version="2.0"
      xmlns:content="http://purl.org/rss/1.0/modules/content/"
      xmlns:wfw="http://wellformedweb.org/CommentAPI/"
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:atom="http://www.w3.org/2005/Atom"
      xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
      xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
      >
  <channel>
  <title>$vw_sitename - Project VaynWords</title>
  <atom:link href="$site/rss.xml" rel="self" type="application/rss+xml" />
  <link>http://lab.jixia.org/project_vws/</link>
  <description>Project VaynWords - Study English with Twitter and RSS</description>
  <lastBuildDate><xsl:value-of select="words/word/date" /></lastBuildDate>
  <generator>http://elnode.com/</generator>
  <language>en</language>
XSL;

echo $rsshead;

$words = pullword();

foreach ($words as $word) {
    $id = $word['id'];
    $key = $word['key'];
    $date = date(DATE_RSS, $word['date']);
    $pho = $word['text'];
    $mp3 = $word['sound'];
    $def = $word['def'][0]['def'];
    $pho = $word['pho'];
    $sent_o = $word['sen'][0]['sen_es'];
    $sent_t = $word['sen'][0]['sen_cs'];

    $rssbody =<<<XSL
<item>
<title>$key</title>
<link>$site/index.php#$id</link>
<description>
    <p>$key $pho</p>
    <p>$def</p>
    <p>$sent_o</p>
    <p>$sent_t</p>
</description>
<pubDate>$date</pubDate>
<guid>$site/index.php#$id</guid>
</item>
XSL;
    echo $rssbody;
}

$rssfooter =<<<XSL
</channel>
</rss>
XSL;
echo $rssfooter;

?>
