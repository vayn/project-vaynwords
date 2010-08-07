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
  <title>Project VaynWords</title>
  <atom:link href="http://lab.jixia.org/project_vws/rss.xml" rel="self" type="application/rss+xml" />
  <link>http://lab.jixia.org/project_vws/</link>
  <description>Project VaynWords - Study English with Twitter and RSS</description>
  <generator>http://elnode.com/</generator>
  <language>en</language>

  <xsl:apply-templates select="words/word" />
 
  </channel>
  </rss>
</xsl:template>

<xsl:template match="word">

  <item>
    <title><xsl:value-of select="key" /></title>
    <link>http://lab.jixia.org/project_vws/index.php#id=<xsl:value-of select="@id" /></link>
    <description>
      <xsl:value-of select="key" /> /<xsl:value-of select="defs/pron" />/&lt;br/&gt;&lt;br/&gt;
      <xsl:value-of select="defs/def" />
      <xsl:if test="defs/sent/orig != ''">
        &lt;br/&gt;&lt;br/&gt;
        <xsl:value-of select="defs/sent/orig" />&lt;br/&gt;&lt;br/&gt;
        <xsl:value-of select="defs/sent/trans" />
      </xsl:if>
    </description>
    <pubDate />
    <guid />
  </item>
</xsl:template>

</xsl:stylesheet>
