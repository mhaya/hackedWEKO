CREATE TABLE {repository_additionaldata_attr_type} (
    `additionaldata_type_id` INT NOT NULL,
    `attribute_id` INT NOT NULL,
    `show_order` INT NOT NULL,
    `input_type` TEXT NOT NULL,
    `length` INT NOT NULL,
    `format` TEXT NOT NULL,
    `is_required` INT(1) NOT NULL,
    `plural_enable` INT(1) NOT NULL,
    `hidden` INT(1) NOT NULL,
    `ins_user_id` VARCHAR(40) NOT NULL default "0",
    `mod_user_id` VARCHAR(40) NOT NULL default "0",
    `del_user_id` VARCHAR(40) NOT NULL default "0",
    `ins_date` varchar(23) NOT NULL,
    `mod_date` varchar(23) NOT NULL,
    `del_date` varchar(23) NOT NULL,
    `is_delete` INT(1),
    PRIMARY KEY(`additionaldata_type_id`, `attribute_id`)
) ENGINE=InnoDb;