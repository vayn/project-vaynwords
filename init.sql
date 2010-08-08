--
-- 数据库: `vws`
--

-- --------------------------------------------------------

--
-- 表的结构 `vws_des`
--

CREATE TABLE IF NOT EXISTS `vws_des` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `wid` mediumint(8) NOT NULL,
  `pos` varchar(20) NOT NULL,
  `def` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wid` (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `vws_mor`
--

CREATE TABLE IF NOT EXISTS `vws_mor` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `wid` mediumint(8) NOT NULL,
  `c` varchar(50) NOT NULL,
  `m` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wid` (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `vws_ph`
--

CREATE TABLE IF NOT EXISTS `vws_ph` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `wid` mediumint(8) NOT NULL,
  `phs` varchar(100) NOT NULL,
  `phd` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wid` (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `vws_sen`
--

CREATE TABLE IF NOT EXISTS `vws_sen` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `wid` mediumint(9) NOT NULL,
  `pos` varchar(20) NOT NULL,
  `sen_es` varchar(100) NOT NULL,
  `sen_cs` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wid` (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `vws_words`
--

CREATE TABLE IF NOT EXISTS `vws_words` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `date` int(10) unsigned NOT NULL,
  `key` varchar(100) NOT NULL,
  `pho` varchar(50) NOT NULL,
  `sound` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 限制导出的表
--

--
-- 限制表 `vws_des`
--
ALTER TABLE `vws_des`
  ADD CONSTRAINT `vws_des_ibfk_1` FOREIGN KEY (`wid`) REFERENCES `vws_words` (`id`) ON DELETE CASCADE;

--
-- 限制表 `vws_mor`
--
ALTER TABLE `vws_mor`
  ADD CONSTRAINT `vws_mor_ibfk_1` FOREIGN KEY (`wid`) REFERENCES `vws_words` (`id`) ON DELETE CASCADE;

--
-- 限制表 `vws_ph`
--
ALTER TABLE `vws_ph`
  ADD CONSTRAINT `vws_ph_ibfk_1` FOREIGN KEY (`wid`) REFERENCES `vws_words` (`id`) ON DELETE CASCADE;

--
-- 限制表 `vws_sen`
--
ALTER TABLE `vws_sen`
  ADD CONSTRAINT `vws_sen_ibfk_1` FOREIGN KEY (`wid`) REFERENCES `vws_words` (`id`) ON DELETE CASCADE;
