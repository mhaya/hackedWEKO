SELECT conf_value 
 FROM {config_language} 
 WHERE conf_name = "sitename" 
 AND lang_dirname = ? ;