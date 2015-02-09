ALTER TABLE  `#__jd_store` CHANGE  `referenceTable`  `referenceTable` VARCHAR( 67 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  'Table of the translation';
ALTER TABLE  `#__jd_store` DROP INDEX  `idLang` , ADD UNIQUE  `idLang` (  `idLang` ,  `referenceTable` ,  `idReference` );
