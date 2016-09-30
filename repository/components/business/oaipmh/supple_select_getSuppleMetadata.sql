SELECT uri 
 FROM %%DATABASE_PREFIX%%repository_supple 
 WHERE item_id = ? 
 AND attribute_id = ? 
 AND is_delete = ?
 ORDER BY supple_no ASC;