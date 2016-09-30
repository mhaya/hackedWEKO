CREATE TABLE {repository_index_additionaldata} (
  `index_id` INT NOT NULL,
  `additionaldata_id` INT NOT NULL,
  `additionaldata_type_id` INT NOT NULL,
  `output_flag` INT(1) NOT NULL,
  `ins_user_id` varchar(40) NOT NULL default 0,
  `mod_user_id` varchar(40) NOT NULL default 0,
  `del_user_id` varchar(40) NOT NULL default 0,
  `ins_date` varchar(23) NOT NULL,
  `mod_date` varchar(23) NOT NULL,
  `del_date` varchar(23) NOT NULL,
  `is_delete` INT(1),
  PRIMARY KEY  (`index_id`, `additionaldata_id`)
) ENGINE=InnoDb;