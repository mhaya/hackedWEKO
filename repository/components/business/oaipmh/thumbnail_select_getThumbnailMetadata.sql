SELECT item_id, item_no, attribute_id, file_no 
 FROM %%DATABASE_PREFIX%%repository_thumbnail 
 WHERE item_id = ? 
 AND attribute_id = ? 
 AND is_delete = ?
 ORDER BY show_order ASC;