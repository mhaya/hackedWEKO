ALTER TABLE %%DATABASE_PREFIX%%repository_personal_name 
 ADD INDEX `author_id` (`item_id`, `author_id`);