CREATE TABLE IF NOT EXISTS `#__jd_store` (
  `idJdStore` int(11) NOT NULL auto_increment,
  `idLang` tinyint(4) NOT NULL COMMENT 'language id #__languages',
  `idReference` int(11) NOT NULL COMMENT 'Primary key for translation',
  `referenceTable` varchar(67) NOT NULL COMMENT 'Table of the translation',
  `referenceOption` varchar(50) NOT NULL,
  `referenceView` varchar(50) NOT NULL,
  `referenceLayout` varchar(50) NOT NULL,
  `sourcehash` VARCHAR( 32 ) NOT NULL,
  `value` longtext NOT NULL COMMENT 'serialized table value',
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  PRIMARY KEY  (`idJdStore`),
  UNIQUE KEY `idLang` (`idLang`,`referenceTable`,`idReference`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
