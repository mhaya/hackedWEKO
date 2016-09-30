SELECT MIN(CONCAT(year,lpad(month, 2, '0'))) AS MIN, MAX(CONCAT(year,lpad(month, 2, '0'))) AS MAX 
 FROM {repository_sitelicense_dlview} 
 WHERE organization_id = ? ;