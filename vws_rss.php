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
  <atom:link href="$site/feed" rel="self" type="application/rss+xml" />
  <link>http://lab.jixia.org/project_vws/</link>
  <description>Project VaynWords - Study English with Twitter and RSS</description>
  <lastBuildDate>$date</lastBuildDate>
  <generator>http://elnode.com/</generator>
  <language>en</language>
XSL;

echo $rsshead;

$words = pullword();

foreach ($words as $word) {
    $id = $word['id'];
    $key = $word['key'];
    $date = date(DATE_RSS, $word['date']);
    $mp3 = $word['sound'];
    if ($word['pho'] != '') $pho = "/{$word['pho']}/";

    $rssbody = "
<item>
<title>{$key}</title>
<link>{$site}/index.php#{$id}</link>
<description>
<p>{$key} {$pho}</p>";

    $defCount = count($word['def']);
    for ($i = 0; $i < $defCount; ++$i) {
        $def = $word['def'][$i]['def'];
        $def_pos = $word['def'][$i]['pos'];
        $rssbody .= "<p>$def_pos $def</p>";
    }

    $senCount = count($word['sen']);
    for ($i = 0; $i < $senCount; ++$i) {
        $senO = $word['sen'][$i]['sen_es'];
        $senT = $word['sen'][$i]['sen_cs'];
        $sen_pos = $word['sen'][$i]['pos'];
         if ($senO != '' || $senT != '') {
             if ($sen_pos != '') $sen_pos = ' [' . $sen_pos . ']';
             $rssbody .= '<p>' . $senO . $sen_pos . '</p>';
             $rssbody .= '<p>' . $senT . '</p>';
        }
    }

    $rssbody .= "
</description>
<pubDate>$date</pubDate>
<guid>$site/index.php#$id</guid>
</item>";
    echo $rssbody;
}

$rssfooter =<<<XSL
</channel>
</rss>
XSL;
echo $rssfooter;

?>
