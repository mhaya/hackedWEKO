CREATE TABLE %%DATABASE_PREFIX%%repository_sitelicense_usage_searchkeyword (
  `organization_id` INT(11) NOT NULL,
  `year` INT(4) NOT NULL,
  `month` INT(2) NOT NULL,
  `cnt` INT(11) default NULL,
  PRIMARY KEY(`organization_id`, `year`, `month`)
) ENGINE=MyISAM;