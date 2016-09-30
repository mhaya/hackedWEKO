<?php

/**
 * Repository Components Business Usage Statistics Class
 * 利用統計フィードバック集計クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Aggregatesitelicenseusagestatistics.class.php 68463 2016-06-06 06:05:40Z tomohiro_ichikawa $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Log operation abstract class
 * ログ操作基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/Logbase.class.php';

/**
 * Repository Components Business Usage Statistics Class
 * 利用統計フィードバック集計クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Usagestatistics extends BusinessBase
{
    /**
     * Perform the aggregation processing of usage statistics log
     * 利用統計ログの集計処理を実行する
     * 
     * @return bool true/false success/failed 成功/失敗
     */
    public function aggregateUsageStatistics() {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $removingLogFlag = $this->isExecuteRemovingLog();
        if($removingLogFlag)
        {
            $exception = new AppException("repository_log_excluding", Repository_Components_Business_Logbase::APP_EXCEPTION_KEY_REMOVING_LOG);
            $exception->addError("repository_log_excluding");
            throw $exception;
        }
        
        // ログテーブルから最も古いログの日付を取得する
        $oldestDate = $this->getOldestDateAtLogTable();
        
        // 取得した日付より新しい利用統計を削除する
        if(!$this->deleteUsageStatisticsRecords($oldestDate)) {
            return false;
        }
        
        // 利用統計を挿入する
        if(!$this->insertUsageStatistics()) {
            return false;
        }
        
        // 利用統計ログ集計の最終実行時間を更新する
        if(!$this->updateParameterUsageStatisticsLastDate()) {
            return false;
        }
        
        return true;
        
    }
    
    /**
     * Check existing usage statistics data record
     * 利用統計レコードが存在するかチェックする
     * 
     * @param int $itemId item ID アイテムID
     * @param int $itemNo item number アイテム通番
     * @return bool true/false exist/not exist 存在する/存在しない
     * @throws AppException
     */
    public function checkUsageStatisticsRecords($itemId, $itemNo) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $query = "SELECT record_date ".
                 "FROM ". DATABASE_PREFIX. RepositoryConst::DBTABLE_REPOSITORY_USAGESTATISTICS." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_ITEM_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_ITEM_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_RECORD_DATE." < DATE_FORMAT(NOW(), '%Y-%m');";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        if(count($result) == 0) {
            // レコードが存在しない場合falseを返す
            return false;
        }
        
        return true;
    }
    
    /**
     * Get the download statistics of the specified item
     * 指定したアイテムのダウンロード統計を取得する
     * 
     * @param int $itemId item ID アイテムID
     * @param int $itemNo item number アイテム通番
     * @param int $year year 年
     * @param int $month month 月
     * @return array donwload statistics ダウンロード統計
     *                $array[$ii]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|
     *                                                                                     "usagestatistics"]["total"|
     *                                                                                                        "byDomain"][DOMAINNAME]["cnt"|"rate"|"img"]
     * @throws AppException
     */
    public function getUsagesDownloads($itemId, $itemNo, $year, $month) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $retArray = array();
        
        // Get files data
        $query = "SELECT FILE.* ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_FILE." AS FILE, ".
                 "     ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_ITEM_ATTR_TYPE." AS ATTRTYPE ".
                 "WHERE FILE.".RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID." = ? ".
                 "AND FILE.".RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_NO." = ? ".
                 "AND FILE.".RepositoryConst::DBCOL_COMMON_IS_DELETE." = ? ".
                 "AND FILE.".RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_TYPE_ID." = ATTRTYPE.".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ITEM_TYPE_ID." ".
                 "AND FILE.".RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID." = ATTRTYPE.".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_ID." ".
                 "AND ATTRTYPE.".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_HIDDEN." = 0 ".
                 "ORDER BY ATTRTYPE.".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_SHOW_ORDER.", FILE.".RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO.";";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        foreach($result as $fileData)
        {
            $usagestatistics = $this->getUsagesDownloadsByFile(
                                    $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID],
                                    $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_NO],
                                    $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID],
                                    $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO],
                                    $year, $month);
            if(count($usagestatistics)==0)
            {
                continue;
            }
            
            array_push( $retArray,
                        array(  RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID => $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID],
                                RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_NO => $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_NO],
                                RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID => $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID],
                                RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO => $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO],
                                RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NAME => $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NAME],
                                RepositoryConst::DBCOL_REPOSITORY_FILE_DISPLAY_NAME => $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_DISPLAY_NAME],
                                "usagestatistics" => $usagestatistics
                        )
            );
        }
        
        return $retArray;
    }
    
    /**
     * Get the download statistics of the specified file
     * 指定したファイルのダウンロード統計を取得する
     *
     * @param int $itemId item ID アイテムID
     * @param int $itemNo item number アイテム通番
     * @param int $attributeId attribute ID 属性ID
     * @param int $fileNo file number ファイル通番
     * @param int $year year 年
     * @param int $month month 月
     * @return array donwload statistics ダウンロード統計
     *                $array["total"|
     *                       "byDomain"][DOMAINNAME]["cnt"|"rate"|"img"]
     * @throws AppException
     */
    public function getUsagesDownloadsByFile($itemId, $itemNo, $attributeId, $fileNo, $year, $month) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $retArray = array();
        
        // Get downloads by file
        $query = "SELECT * ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_USAGESTATISTICS." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_ITEM_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_ITEM_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_ATTRIBUTE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_FILE_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_RECORD_DATE." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_OPERATION_ID." = ? ;";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attributeId;
        $params[] = $fileNo;
        $params[] = sprintf("%d-%02d", $year, $month);
        $params[] = 2;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // 出力情報を整形する
        $total = 0;
        $byDomain = array();
        foreach($result as $record)
        {
            $domain = $record[RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_DOMAIN];
            $count = $record[RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_CNT];
            
            $total += intval($count);
            $this->setArrayByDomain($byDomain, $domain, $count);
        }
        arsort($byDomain);
        
        $this->setUsagestatisticsArray($retArray, $total, $byDomain);
        
        return $retArray;
    }
    
    /**
     * And outputs the sum of logs and usage statistics to date
     * 現在までのログと利用統計の合計を出力する
     *
     * @param int $itemId item ID アイテムID
     * @param int $itemNo item number アイテム通番
     * @param int $attributeId attribute ID 属性ID
     * @param int $fileNo file number ファイル通番
     * @return int $retCnt sum log and statistics ログと利用統計の合計
     */
    public function getUsagesDownloadsNow($itemId, $itemNo, $attributeId, $fileNo) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $retCnt = 0;
        
        // Get latest month in usagestatistics table
        $latestDate = $this->getLatestDateAtUsageStatistics();
        
        // Get download count from usagestatistics table
        $retCnt += $this->getDownloadCountFromUsagestatistics(
                            $itemId, $itemNo, $attributeId, $fileNo, $latestDate);
        
        // Get download count from log table
        $retCnt += $this->getDownloadCountFromLog(
                            $itemId, $itemNo, $attributeId, $fileNo, $latestDate);
        
        return $retCnt;
    }
    
    /**
     * Get the usage statistics of browsing
     * 閲覧の利用統計を取得する
     *
     * @param int $itemId item ID アイテムID
     * @param int $itemNo item number アイテム通番
     * @param int $year year 年
     * @param int $month month 月
     * @return array $retArray["total"|
     *                          "byDomain"][DOMAINNAME]["cnt"|"rate"|"img"]
     * @throws AppException
     */
    public function getUsagesViews($itemId, $itemNo, $year, $month) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $retArray = array();
        
        // 閲覧の利用統計を取得する
        $query = "SELECT * ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_USAGESTATISTICS." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_ITEM_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_ITEM_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_RECORD_DATE." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_OPERATION_ID." = ? ;";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = sprintf("%d-%02d", $year, $month);
        $params[] = RepositoryConst::LOG_OPERATION_DETAIL_VIEW;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // 出力情報を整形する
        $total = 0;
        $byDomain = array();
        foreach($result as $record)
        {
            $domain = $record[RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_DOMAIN];
            $count = $record[RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_CNT];
            
            $total += intval($count);
            $this->setArrayByDomain($byDomain, $domain, $count);
        }
        arsort($byDomain);
        
        $this->setUsagestatisticsArray($retArray, $total, $byDomain);
        
        return $retArray;
        
    }
    
    /**
     * Get oldest log record date
     * 最も古いログの日付を取得する
     * 
     * @return string log oldest record date 最古のログ日付
     * @throws AppException
     */
    private function getOldestDateAtLogTable() {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // Get the oldest date (format: YYYY-MM)
        $query = "SELECT MIN(DATE_FORMAT(". RepositoryConst::DBCOL_REPOSITORY_LOG_RECORD_DATE. ", '%Y-%m')) AS oldestDate ".
                 "FROM ". DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_LOG. " ;";
        $result = $this->Db->execute($query);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        if(count($result) != 1) {
            return "";
        }
        
        return $result[0]["oldestDate"];
    }
    
    /**
     * Delete a new usage statistics than the passed time
     * 渡された時刻より新しい利用統計を削除する
     * 
     * @param string $oldestDate oldest date 日付情報
     * @return bool true/false delete success/delete failed 削除成功/削除失敗
     * @throws AppException
     */
    private function deleteUsageStatisticsRecords($oldestDate) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // 日時が不正な場合はfalseを返す
        if(strlen($oldestDate) == 0) {
            return false;
        }
        
        $query = "DELETE FROM ".DATABASE_PREFIX."repository_usagestatistics ".
                 "WHERE record_date >= ?; ";
        $params = array();
        $params[] = $oldestDate;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return true;
    }
    
    /**
     * Insert usage statistics log
     * 利用統計ログを挿入する
     * 
     * @return bool true/false insert success/insert failed 挿入成功/挿入失敗
     */
    private function insertUsageStatistics() {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // 除外ログサブクエリを取得する
        $logExclusion = $this->getLogExclusion("LOG");
        
        // 閲覧ログを挿入する
        if(!$this->insertUsageStatisticsRecords($this->accessDate, RepositoryConst::LOG_OPERATION_DETAIL_VIEW, $logExclusion)) {
            return false;
        }
        
        // ダウンロードログを挿入する
        if(!$this->insertUsageStatisticsRecords($this->accessDate, RepositoryConst::LOG_OPERATION_DOWNLOAD_FILE, $logExclusion)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Inserting data obtained from the log table
     * ログテーブルから取得したデータを挿入する
     * 
     * @param string $insDate log insert date ログ挿入日時
     * @param int $operationId operation ID 操作種別ID
     * @param string $logExclusion exclusion condition query 除外条件クエリ文
     * @return bool true/false insert success/insert failed 挿入成功/挿入失敗
     * @throws AppException
     */
    private function insertUsageStatisticsRecords($insDate, $operationId, $logExclusion) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // 指定した条件でログテーブルからデータを取得してそのまま利用統計テーブルに入れる
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog();
        $query = "INSERT INTO ".DATABASE_PREFIX."repository_usagestatistics ".
                 "(record_date, item_id, item_no, attribute_id, file_no, operation_id, domain, cnt, ins_user_id, ins_date) ".
                 "SELECT DATE_FORMAT(LOG.record_date, '%Y-%m') AS yearMonth, LOG.item_id, LOG.item_no, LOG.attribute_id, LOG.file_no, LOG.operation_id, SUBSTRING_INDEX(LOG.host, '.', -2)AS domain, COUNT(*) AS cnt, ?, ? ".
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                 "WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 "AND LOG.operation_id = ? ".
                 "AND LOG.item_id >= 1 ".
                 "AND LOG.item_no >= 1 ".
                 $logExclusion.
                 "GROUP BY LOG.item_id, LOG.attribute_id, LOG.file_no, yearMonth, domain;";
        $params = array();
        $params[] = $this->user_id; // ins_user_id
        $params[] = $insDate;       // ins_date
        $params[] = $operationId;   // operation_id
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return true;
    }
    
    /**
     * It updates the last execution time of usage statistics summary
     * 利用統計集計の最終実行時間を更新する
     *
     * @return bool true/false update success/update failed 更新成功/更新失敗
     * @throws AppException
     */
    private function updateParameterUsageStatisticsLastDate() {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $DATE = new Date();
        $execute_time = str_replace("-","/",$DATE->getDate());
        
        // パラメータテーブルを更新する
        $query = "UPDATE ". DATABASE_PREFIX. "repository_parameter ".
                 "SET param_value = ? , mod_user_id = ?, mod_date = ? ".
                 "WHERE param_name = ? ;";
        $params = array();
        $params[] = $execute_time;                          // param_value
        $params[] = $this->user_id;                         // mod_user_id
        $params[] = $this->accessDate;                      // mod_date
        $params[] = "update_usage_statistics_last_date";    // param_name
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return true;
    }
    
    /**
     * Get the exclusion log sub query
     * 除外ログサブクエリを取得する
     * 
     * @param  string $prefix table alias テーブル別名
     * @return string sub query サブクエリ文
     * @throws AppException
     */
    private function getLogExclusion($prefix="") {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $logExclusion = "";
        if(strlen($prefix) > 0) {
            $header = $prefix.".";
        } else {
            $header = "";
        }
        
        // ログ解析除外IPアドレスを取得する
        $query = "SELECT param_value FROM ". DATABASE_PREFIX ."repository_parameter ".
                 "WHERE param_name = 'log_exclusion'; ";
        $ip_list = $this->Db->execute($query);
        if($ip_list === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $ip_list = str_replace("\r\n", "\n", $ip_list[0]["param_value"]);
        $ip_list = str_replace("\r", "\n", $ip_list);
        $ip_list = explode("\n", $ip_list);
        for($ii=0; $ii<count($ip_list); $ii++){
            if(strlen($ip_list[$ii]) > 0){
                if(strlen($logExclusion) == 0){
                    $logExclusion = " AND ". $header."ip_address NOT IN ( ";
                } else {
                    $logExclusion .= " , ";
                }
                $logExclusion .= " '". $ip_list[$ii]. "' ";
            }
        }
        if(strlen($logExclusion) > 0){
            $logExclusion .= " ) ";
        }
        
        return $logExclusion;
    }

    /**
     * Divide the statistics for each domain
     * ドメイン毎に統計を分ける
     * 
     * @param array $domainArray statistics by domain array ドメイン別統計配列
     * @param string $domain domain name ドメイン名
     * @param int $count statistics count 統計情報
     * @return bool true/false success/failed 成功/失敗
     */
    private function setArrayByDomain(&$domainArray, $domain, $count) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $pattern = "/.*(".RepositoryConst::USAGESTATISTICS_DOMAIN_COM.
                   "|".RepositoryConst::USAGESTATISTICS_DOMAIN_ORG.
                   "|".RepositoryConst::USAGESTATISTICS_DOMAIN_AC_JP.
                   "|".RepositoryConst::USAGESTATISTICS_DOMAIN_CO_JP.
                   "|".RepositoryConst::USAGESTATISTICS_DOMAIN_GO_JP.
                   "|".RepositoryConst::USAGESTATISTICS_DOMAIN_EDU.")$/";
        $matches = array();
        if(preg_match($pattern, $domain, $matches))
        {
            if(key_exists($matches[1], $domainArray))
            {
                $domainArray[$matches[1]]["cnt"] += $count;
            }
            else
            {
                $domainArray[$matches[1]]["cnt"] = $count;
                $domainArray[$matches[1]]["img"] = $this->getFlagImagePath($matches[1]);
            }
        }
        else
        {
            // Other Country or unknown
            // Get top domain
            $topDomain = RepositoryConst::USAGESTATISTICS_DOMAIN_UNKNOWN;
            $imgPath = "";
            $matches = array();
            $pattern = "/\.?([^.]*)$/";
            preg_match($pattern, $domain, $matches);
            if(isset($matches[1]) && strlen($matches[1]) > 0)
            {
                // Check flag image for top domain is exist.
                // if no exist image, this domain is 'unknown'.
                $imgPath = $this->getFlagImagePath($matches[1]);
                if(strlen($imgPath) > 0)
                {
                    $topDomain = $matches[1];
                }
            }
            if(key_exists($topDomain, $domainArray))
            {
                $domainArray[$topDomain]["cnt"] += $count;
            }
            else
            {
                $domainArray[$topDomain]["cnt"] = $count;
                $domainArray[$topDomain]["img"] = $imgPath;
            }
        }
        return true;
    }
    
    /**
     * Set array for usage statistics data
     * 統計配列にデータを追加する
     * 
     * @param array $retArray statistics by domain array ドメイン別統計配列
     * @param int $total total count 統計合計値
     * @param array $byDomain statistics count by domain ドメインの統計情報
     * @return bool
     */
    private function setUsageStatisticsArray(&$retArray, $total, $byDomain) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        foreach($byDomain as $key => $val)
        {
            if($total > 0)
            {
                $byDomain[$key]["rate"] = $val["cnt"]/$total*100;
            }
            else
            {
                $byDomain[$key]["rate"] = 0;
            }
        }
        
        $retArray["total"] = $total;
        $retArray["byDomain"] = $byDomain;
        
        return true;
    }
    
    /**
     * Get flag image path
     * フラグイメージパスを取得
     * 
     * @param string $domain domain name ドメイン名
     * @return string image path イメージパス
     */
    private function getFlagImagePath($domain) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $imgPath = "";
        $flagDir = "/images/repository/flags/";
        $topDomain = "";
        
        if(preg_match("/.*".RepositoryConst::USAGESTATISTICS_DOMAIN_AC_JP."$/", $domain)
            || preg_match("/.*".RepositoryConst::USAGESTATISTICS_DOMAIN_CO_JP."$/", $domain)
            || preg_match("/.*".RepositoryConst::USAGESTATISTICS_DOMAIN_GO_JP."$/", $domain))
        {
            $topDomain = RepositoryConst::USAGESTATISTICS_DOMAIN_JP;
        }
        else if(preg_match("/.*".RepositoryConst::USAGESTATISTICS_DOMAIN_EDU."$/", $domain))
        {
            $topDomain = RepositoryConst::USAGESTATISTICS_DOMAIN_US;
        }
        else
        {
            $matches = array();
            $pattern = "/\.?([^.]*)$/";
            preg_match($pattern, $domain, $matches);
            if(isset($matches[1]) && strlen($matches[1]) > 0)
            {
                $topDomain = $matches[1];
            }
        }
        
        if(strlen($topDomain) > 0 && file_exists(HTDOCS_DIR.$flagDir.$topDomain.".png"))
        {
            $imgPath = ".".$flagDir.$topDomain.".png";
        }
        return $imgPath;
    }
    
    /**
     * Get the date and time of the most recent available statistics
     * 最新の利用統計の日時を取得する
     *
     * @return string latest date 最新日時
     * @throws AppException
     */
    private function getLatestDateAtUsageStatistics() {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // Get the latest date (format: YYYY-MM)
        $query = "SELECT MAX(".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_RECORD_DATE.") AS latestDate ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_USAGESTATISTICS." ;";
        $result = $this->Db->execute($query);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        if(count($result) == 0) {
            return "";
        }
        
        return $result[0]["latestDate"];
    }
    
    /**
     * Get the download count of the specified files from the usage statistics log
     * 利用統計ログから指定したファイルのダウンロード回数を取得する
     *
     * @param int $itemId item ID アイテムID
     * @param int $itemNo item number アイテム通番
     * @param int $attributeId attribute ID 属性ID
     * @param int $fileNo file number ファイル通番
     * @param string $latestDate latest date 最新日時
     * @return int download count ダウンロード回数
     * @throws AppException
     */
    private function getDownloadCountFromUsageStatistics($itemId, $itemNo, $attributeId, $fileNo, $latestDate) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        if(strlen($latestDate) == 0)
        {
            return 0;
        }
        
        // Get download count
        $query = "SELECT SUM(".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_CNT.") AS cnt ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_USAGESTATISTICS." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_ITEM_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_ITEM_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_ATTRIBUTE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_FILE_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_RECORD_DATE." < ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_USAGESTATISTICS_OPERATION_ID." = ? ;";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attributeId;
        $params[] = $fileNo;
        $params[] = $latestDate;
        $params[] = RepositoryConst::LOG_OPERATION_DOWNLOAD_FILE;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        if(count($result) == 0) {
            return 0;
        }
        
        return intval($result[0]['cnt']);
    }
    
    /**
     * Get the download count of the specified files from the log
     * ログから指定したファイルのダウンロード回数を取得する
     *
     * @param int $itemId item ID アイテムID
     * @param int $itemNo item number アイテム通番
     * @param int $attributeId attribute ID 属性ID
     * @param int $fileNo file number ファイル通番
     * @param string $latestDate latest date 最新日時
     * @return int download count ダウンロード回数
     * @throws AppException
    */
    private function getDownloadCountFromLog($itemId, $itemNo, $attributeId, $fileNo, $latestDate) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // Get log exclusion
        $logExclusion = $this->getLogExclusion();
        
        // Get download count
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog();
        $query = "SELECT LOG.log_no ".
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                 "WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 "AND LOG.item_id = ? ".
                 "AND LOG.item_no = ? ".
                 "AND LOG.attribute_id = ? ".
                 "AND LOG.file_no = ? ".
                 "AND LOG.operation_id = ? ".
                 $logExclusion." ";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attributeId;
        $params[] = $fileNo;
        $params[] = RepositoryConst::LOG_OPERATION_DOWNLOAD_FILE;
        
        if(strlen($latestDate) > 0)
        {
            $query .= "AND LOG.record_date >= ? ;";
            $params[] = $latestDate;
        }
        
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        if(count($result) == 0) {
            return 0;
        }
        
        return count($result);
    }
    
    /**
     * And determines whether or not during the removal robot list log
     * ロボットリストログ削除中かどうか判定する
     *
     * @return bool true/false deleting/not deleting 削除中である/削除中では無い
     * @throws AppException
    */
    private function isExecuteRemovingLog() {
        // check removing log process
        $query = "SELECT status ". 
                 " FROM ". DATABASE_PREFIX. "repository_lock ". 
                 " WHERE process_name = ? ;";
        $params = array();
        $params[] = 'Repository_Action_Common_Robotlist';
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // when execute removing log, throw exception
        for($cnt = 0; $cnt < count($result); $cnt++)
        {
            if(intval($result[$cnt]['status']) > 0){
                return true;
            }
        }
        
        return false;
    }

}
?>