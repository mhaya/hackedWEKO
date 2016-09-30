CREATE TABLE IF NOT EXISTS %%DATABASE_PREFIX%%repository_doi_change_log (
 `log_no` INT(11) NOT NULL default 0, 
 `record_date` VARCHAR(23) NOT NULL, 
 `user_id` VARCHAR(40) NOT NULL, 
 `item_id` INT(11)  NOT NULL default 0, 
 `ra_changed` VARCHAR(30) NOT NULL, 
 `doi_changed` TEXT NOT NULL, 
PRIMARY KEY(`log_no`),
INDEX `record_date` (`record_date`),
INDEX `item_id` (`item_id`)
) ENGINE=MyISAM;