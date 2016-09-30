SELECT family, name 
 FROM %%DATABASE_PREFIX%%repository_personal_name 
 WHERE item_id = ? 
 AND attribute_id = ? 
 AND is_delete = ? 
 ORDER BY personal_name_no ASC;