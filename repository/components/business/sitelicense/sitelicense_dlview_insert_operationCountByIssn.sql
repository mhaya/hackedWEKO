INSERT IGNORE INTO {repository_sitelicense_dlview} 
 (organization_id, year, month, operation_id, online_issn, journal_name_ja, journal_name_en, setspec, cnt) 
 VALUES 
 (?, ?, ?, ?, ?, ?, ?, ?, ?);