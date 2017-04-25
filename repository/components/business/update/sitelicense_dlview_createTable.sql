CREATE TABLE %%DATABASE_PREFIX%%repository_sitelicense_dlview (
  `organization_id` INT(11) NOT NULL,
  `year` INT(4) NOT NULL,
  `month` INT(2) NOT NULL,
  `operation_id` INT(1) NOT NULL,
  `online_issn` VARCHAR(9) NOT NULL,
  `journal_name_ja` TEXT,
  `journal_name_en` TEXT,
  `setspec` TEXT,
  `cnt` INT(11) default NULL,
  PRIMARY KEY(`organization_id`, `year`, `month`, `operation_id`, `online_issn`)
) ENGINE=MyISAM;