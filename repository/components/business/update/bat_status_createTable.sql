CREATE TABLE %%DATABASE_PREFIX%%repository_bat_status ( 
`batch_instance_no` INT(11),
`batch_name` varchar(100) NOT NULL,
`process_id` INT(11) NOT NULL,
`status` INT(1) NOT NULL,
`command` TEXT,
`current_progress` INT(11) NOT NULL DEFAULT 0,
`max_length` INT(11) NOT NULL DEFAULT 0,
`exit_code` INT(11), 
`start_date` varchar(19) NOT NULL default '', 
`end_date` varchar(19) NOT NULL default '', 
`ins_date` varchar(23) NOT NULL default '', 
`mod_date` varchar(23) NOT NULL default '', 
PRIMARY KEY  (`batch_instance_no`, `batch_name`) 
) ENGINE=MyISAM;