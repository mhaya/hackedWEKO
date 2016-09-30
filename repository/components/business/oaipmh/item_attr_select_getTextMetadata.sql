SELECT attribute_value 
 FROM %%DATABASE_PREFIX%%repository_item_attr 
 WHERE item_id = ? 
 AND attribute_id = ? 
 AND is_delete = ?
 ORDER BY attribute_no ASC;