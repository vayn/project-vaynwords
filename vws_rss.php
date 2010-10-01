<?php

include_once './inc/common.inc.php';

$site = 'http://' . $_SERVER['SERVER_NAME'] . substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));

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

header("Content-Type: application/rss+xml");

echo $rsshead;

$words = VWSCore::pullwords();

foreach ($words as $word) {
    $id = $word['id'];
    $key = $word['key'];
    $date = date(DATE_RSS, $word['date']);
    $pho = $word['pho'] ? "/{$word['pho']}/" : '';

    $rssbody = "
<item>
<title>{$key}</title>
<link>{$site}/index.php#{$id}</link>
<description>
&lt;p&gt;{$key} {$pho}&lt;/p&gt;";

    $defCount = count($word['def']);
    for ($i = 0; $i < $defCount; ++$i) {
        $def = $word['def'][$i]['def'];
        $def_pos = $word['def'][$i]['pos'];
        $rssbody .= "&lt;p&gt;$def_pos $def&lt;/p&gt;";
    }

    $senCount = count($word['sen']);
    for ($i = 0; $i < $senCount; ++$i) {
        $senO = $word['sen'][$i]['sen_es'];
        $senT = $word['sen'][$i]['sen_cs'];
        $sen_pos = $word['sen'][$i]['pos'];
         if ($senO != '' || $senT != '') {
             if ($sen_pos != '') $sen_pos = ' [' . $sen_pos . ']';
             $rssbody .= '&lt;p&gt;' . $senO . $sen_pos . '&lt;/p&gt;';
             $rssbody .= '&lt;p&gt;' . $senT . '&lt;/p&gt;';
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
