SELECT * 
 FROM {repository_sitelicense_dlview}
 WHERE organization_id = ? 
 AND year = ?
 AND month = ? 
 AND operation_id = ? 
 AND online_issn = ? ;