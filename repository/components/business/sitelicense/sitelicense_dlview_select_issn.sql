SELECT *
 FROM {repository_sitelicense_dlview} 
 WHERE organization_id = ? 
 AND operation_id = ? 
 AND CONCAT(year,lpad(month, 2, '0')) >= ? 
 AND CONCAT(year,lpad(month, 2, '0')) <= ? 
 GROUP BY online_issn;