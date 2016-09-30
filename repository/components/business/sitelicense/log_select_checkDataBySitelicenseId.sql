SELECT log_no 
 FROM {repository_log} 
 WHERE record_date >= ? 
 AND record_date <= ? 
 AND site_license_id > ? 
 LIMIT 0,1 ;