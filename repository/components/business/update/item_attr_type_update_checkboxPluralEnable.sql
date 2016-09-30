UPDATE %%DATABASE_PREFIX%%repository_item_attr_type 
 SET `plural_enable` = ?
 WHERE `input_type` = ?;