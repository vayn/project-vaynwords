<?php

  require('config.php');

  $xslt = new XSLTProcessor();

  $site = 'http://' . $_SERVER['SERVER_NAME'] . substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));

  $xslstring =<<<XSL
<?xml version="1.0" encoding="UTF-8"?>
<!-- Create by Vayn@JxLab -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" encoding="UTF-8" indent="yes" />

<xsl:template match="/">
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

  <xsl:apply-templates select="words/word[position() &lt; $vw_rss_output]" />
 
  </channel>
  </rss>
</xsl:template>

<xsl:template match="word">

  <item>
    <title><xsl:value-of select="key" /></title>
    <link>$site/index.php#<xsl:value-of select="@id" /></link>
    <description>
      &lt;p&gt;<xsl:value-of select="key" /> /<xsl:value-of select="defs/pron" />/&lt;/p&gt;
      &lt;p&gt;<xsl:value-of select="defs/def" />&lt;/p&gt;
      <xsl:if test="defs/sent/orig != ''">
        &lt;p&gt;<xsl:value-of select="defs/sent/orig" />&lt;/p&gt;
        &lt;p&gt;<xsl:value-of select="defs/sent/trans" />&lt;/p&gt;
      </xsl:if>
    </description>
    <pubDate><xsl:value-of select="date" /></pubDate>
    <guid>$site/index.php#<xsl:value-of select="@id" /></guid>
  </item>

</xsl:template>

</xsl:stylesheet>
XSL;

  $xsl = new DOMDocument();
  $xsl->loadXML($xslstring);

  $xslt->importStylesheet($xsl);

  $xml = new DOMDocument();
  $xml->load('vws_data.xml');

  $results = $xslt->transformToXML($xml);

  echo $results;
  
?>
