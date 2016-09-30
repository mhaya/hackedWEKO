CREATE TABLE %%DATABASE_PREFIX%%repository_ranking_count_period ( 
 `from_date` varchar(23) NOT NULL, 
 `to_date` varchar(23) NOT NULL, 
 `mod_user_id` varchar(40) NOT NULL default 0, 
 `mod_date` varchar(23) NOT NULL 
 ) ENGINE=InnoDb;