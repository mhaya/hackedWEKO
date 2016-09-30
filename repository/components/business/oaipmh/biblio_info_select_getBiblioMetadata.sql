SELECT biblio_name, biblio_name_english, volume, issue, start_page, end_page, date_of_issued 
 FROM %%DATABASE_PREFIX%%repository_biblio_info 
 WHERE item_id = ? 
 AND attribute_id = ? 
 AND is_delete = ?;