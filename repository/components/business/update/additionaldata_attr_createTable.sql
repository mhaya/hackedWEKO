CREATE TABLE {repository_additionaldata_attr} (
    `additionaldata_id` INT NOT NULL,
    `additionaldata_type_id` INT NOT NULL,
    `attribute_id` INT NOT NULL,
    `attribute_no` INT NOT NULL,
    `attribute_value` TEXT NOT NULL,
    `ins_user_id` VARCHAR(40) NOT NULL default "0",
    `mod_user_id` VARCHAR(40) NOT NULL default "0",
    `del_user_id` VARCHAR(40) NOT NULL default "0",
    `ins_date` varchar(23) NOT NULL,
    `mod_date` varchar(23) NOT NULL,
    `del_date` varchar(23) NOT NULL,
    `is_delete` INT(1),
    PRIMARY KEY(`additionaldata_id`, `additionaldata_type_id`, `attribute_id`, `attribute_no`)
) ENGINE=InnoDb;