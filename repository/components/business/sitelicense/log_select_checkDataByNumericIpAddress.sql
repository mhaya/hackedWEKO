SELECT log_no 
 FROM {repository_log}
 WHERE record_date >= ? AND record_date <= ? 
 AND site_license_id = ? 
 AND site_license = ? 
 AND numeric_ip_address >= ? 
 LIMIT 0,1 ;