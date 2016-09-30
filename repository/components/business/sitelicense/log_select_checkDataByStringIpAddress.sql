SELECT log_no 
 FROM {repository_log} 
 WHERE record_date >= ? AND record_date <= ? 
 AND numeric_ip_address = ? 
 LIMIT 0,1 ;