CREATE TABLE {repository_sitelicense_mail_send_status_all} (
  `organization_id` INT(11) NOT NULL,
  `mail_no` INT(11) NOT NULL,
  `mail_address` VARCHAR(255) NOT NULL default '',
  `year` INT(4) NOT NULL,
  `month` INT(2) NOT NULL,
  `aggregate_range` INT(11) NOT NULL,
  `send_status` int(1) NOT NULL,
  `start_date`  VARCHAR(23) NOT NULL default '',
  `send_date`  VARCHAR(23) NOT NULL default '',
  `request_password`  VARCHAR(16) NOT NULL default '',
  PRIMARY KEY(`organization_id`, `mail_no`)
) ENGINE=MyISAM;