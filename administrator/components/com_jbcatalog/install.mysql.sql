CREATE TABLE IF NOT EXISTS `#__jbcatalog_adf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `field_type` tinyint(4) NOT NULL,
  `filters` varchar(1) NOT NULL,
  `published` varchar(2) NOT NULL,
  `ordering` int(11) NOT NULL,
  `adf_prefix` VARCHAR( 10 ) NOT NULL ,
  `adf_postfix` VARCHAR( 20 ) NOT NULL ,
  `adf_tooltip` TEXT NOT NULL,
  `adf_numeric` VARCHAR( 1 ) NOT NULL,
  `adf_complex` int(11) NOT NULL DEFAULT  '0',
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbcatalog_adfgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `show_title` varchar(1) NOT NULL,
  `displayopt` varchar(1) NOT NULL,
  `published` varchar(2) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__jbcatalog_adfgroup` (`id`, `name`, `show_title`, `displayopt`, `published`, `ordering`) 
VALUES (1, 'Default group', '', '1', '1', 0);

CREATE TABLE IF NOT EXISTS `#__jbcatalog_adf_select` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `field_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbcatalog_adf_values` (
  `item_id` int(11) NOT NULL,
  `adf_id` int(11) NOT NULL,
  `adf_value` varchar(255) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adf_text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`,`adf_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbcatalog_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `published` varchar(2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lft` (`lft`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__jbcatalog_category`(id,title,parent_id,rgt) VALUES(1,'root',0,1);



CREATE TABLE IF NOT EXISTS `#__jbcatalog_category_adfs` (
  `cat_id` int(11) NOT NULL,
  `adfs_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `listview` varchar(1) NOT NULL DEFAULT '0',
  `filtered` varchar(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `cat_id` (`cat_id`,`adfs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbcatalog_category_adfs_group` (
  `cat_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  UNIQUE KEY `cat_id` (`cat_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbcatalog_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `catid` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  `ftype` tinyint(4) NOT NULL,
  `itemid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`,`itemid`,`ftype`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbcatalog_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `published` varchar(2) NOT NULL,
  `ordering` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbcatalog_items_cat` (
  `item_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  UNIQUE KEY `item_id` (`item_id`,`cat_id`),
  KEY `item_id_2` (`item_id`,`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

CREATE TABLE IF NOT EXISTS `#__jbcatalog_adf_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rating_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `usr_id` int(11) NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rating_id` (`rating_id`,`item_id`,`usr_id`),
  KEY `item_id` (`item_id`,`rating_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbcatalog_complex` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `published` varchar(2) NOT NULL DEFAULT  '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__jbcatalog_complex`(id,`name`,parent_id,`level`,`rgt`) VALUES(1,'root',0,0,1);

CREATE TABLE IF NOT EXISTS `#__jbcatalog_complex_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL,
  `catid` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `published` VARCHAR( 2 ) NOT NULL DEFAULT  '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbcatalog_adf_ingroups` (
  `adfid` int(11) NOT NULL,
  `groupid` int(11) NOT NULL,
  UNIQUE KEY `adfid` (`adfid`,`groupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbcatalog_plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `published` varchar(2) NOT NULL DEFAULT  '1',
  `type` varchar(10) NOT NULL,
  `ordering` int(11) NOT NULL,
  `version` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__jbcatalog_plugins` (`name`, `title`, `description`, `published`, `type`, `ordering`, `version`) VALUES
('adftext', 'COM_JBCATALOG_ADF_FIELDTYPE_TEXT', 'COM_JBCATALOG_ADF_FIELDTYPE_TEXT_DESC', '1', 'adf', 0, '1.0'),
('adfdate', 'COM_JBCATALOG_ADF_FIELDTYPE_DATE', 'COM_JBCATALOG_ADF_FIELDTYPE_DATE_DESC', '1', 'adf', 0, '1.0'),
('adfeditor', 'COM_JBCATALOG_ADF_FIELDTYPE_EDITOR', 'COM_JBCATALOG_ADF_FIELDTYPE_EDITOR_DESC', '1', 'adf', 0, '1.0'),
('adflink', 'COM_JBCATALOG_ADF_FIELDTYPE_LINK', 'COM_JBCATALOG_ADF_FIELDTYPE_LINK_DESC', '1', 'adf', 0, '1.0'),
('adfmultiselect', 'COM_JBCATALOG_ADF_FIELDTYPE_MULTISELECT', 'COM_JBCATALOG_ADF_FIELDTYPE_MULTISELECT_DESC', '1', 'adf', 0, '1.0'),
('adfradio', 'COM_JBCATALOG_ADF_FIELDTYPE_RADIO', 'COM_JBCATALOG_ADF_FIELDTYPE_RADIO_DESC', '1', 'adf', 0, '1.0'),
('adfrating', 'COM_JBCATALOG_ADF_FIELDTYPE_RATING', 'COM_JBCATALOG_ADF_FIELDTYPE_RATING_DESC', '1', 'adf', 0, '1.0'),
('adfselect', 'COM_JBCATALOG_ADF_FIELDTYPE_SELECT', 'COM_JBCATALOG_ADF_FIELDTYPE_SELECT_DESC', '1', 'adf', 0, '1.0');

CREATE TABLE IF NOT EXISTS `#__jbcatalog_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT IGNORE INTO `#__jbcatalog_options` (`name`,`data`) VALUES
('item_position','{"positions":{".dropHeader":["index1"],".dropLeft":["index2"],".dropRight":["index3"],".dropFooter":["index4"]},"css":{"index1":{"font-family":"''Times New Roman''","font-size":"16px","color":"rgb(0, 0, 0)","font-weight":400,"text-decoration":"none solid rgb(0, 0, 0)","font-style":"normal","margin-left":"0px","margin-bottom":"0px","margin-right":"0px","margin-top":"0px"},"index3":{"font-family":"''Times New Roman''","font-size":"16px","color":"rgb(0, 0, 0)","font-weight":400,"text-decoration":"none solid rgb(0, 0, 0)","font-style":"normal","margin-left":"0px","margin-bottom":"0px","margin-right":"0px","margin-top":"0px"},"index2":{"padding":"5px","background-color":"rgb(187, 187, 187)","margin-left":"0px","margin-bottom":"0px","margin-right":"0px","margin-top":"0px"},"index6":{"font-family":"''Times New Roman''","font-size":"16px","color":"rgb(0, 0, 0)","font-weight":400,"text-decoration":"none solid rgb(0, 0, 0)","font-style":"normal","margin-left":"0px","margin-bottom":"0px","margin-right":"0px","margin-top":"0px"},"index4":{"font-family":"''Times New Roman''","font-size":"16px","color":"rgb(0, 0, 0)","font-weight":400,"text-decoration":"none solid rgb(0, 0, 0)","font-style":"normal","margin-left":"0px","margin-bottom":"0px","margin-right":"0px","margin-top":"0px"},"index5":{"font-family":"''Times New Roman''","font-size":"16px","color":"rgb(0, 0, 0)","font-weight":400,"text-decoration":"none solid rgb(0, 0, 0)","font-style":"normal","margin-left":"0px","margin-bottom":"0px","margin-right":"0px","margin-top":"0px"}},"label":{}}');
