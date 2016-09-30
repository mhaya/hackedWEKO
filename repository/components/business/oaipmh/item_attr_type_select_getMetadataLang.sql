SELECT ATTR.display_lang_type AS language 
 FROM %%DATABASE_PREFIX%%repository_item_attr_type AS ATTR, 
      %%DATABASE_PREFIX%%repository_item AS ITEM 
 WHERE ITEM.item_id = ? 
 AND ATTR.item_type_id = ITEM.item_type_id 
 AND ATTR.attribute_id = ? 
 AND ATTR.is_delete = ? 
 AND ITEM.is_delete = ?;