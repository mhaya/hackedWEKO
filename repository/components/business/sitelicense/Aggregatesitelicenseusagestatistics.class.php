<?php

/**
 * Repository Components Business AggregateSitelicense
 * サイトライセンス利用統計集計ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Aggregatesitelicenseusagestatistics.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Log manager class
 * ログ管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/Logmanager.class.php';
/**
 * Repository Components Business Sitelicense Manager
 * サイトライセンス機関情報管理ビジネスクラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/sitelicense/Sitelicensemanager.class.php';

/**
 * Repository Components Business AggregateSitelicense
 * サイトライセンス利用統計集計ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Sitelicense_Aggregatesitelicenseusagestatistics extends BusinessBase
{
    /**
     * aggregate by sitelicense id
     * サイトライセンスIDによる集計フラグ値
     */
    const EXEFLAG_SITELICENSE_ID = 0;
    /**
     * aggregate by numeric ip address
     * 数値化IPアドレスによる集計フラグ
     */
    const EXEFLAG_NUMERIC_IP_ADDRESS = 1;
    /**
     * aggregate by string ip address
     * 文字列IPアドレスによる集計フラグ
     */
    const EXEFLAG_IP_ADDRESS = 2;
    
    /**
     * Do the aggregation of usage statistics
     * サイトライセンス利用統計の集計を行う
     *
     * @param int   $organization_id sitelicense organization ID サイトライセンス機関ID
     * @param int   $year aggregate year 集計年
     * @param int   $month aggregate month 集計月
     * @return bool true/false success/failed 成功/失敗
     */
    public function aggregateUsageStatistics($organization_id, $year, $month) {
        // 実行する集計パターンを設定する
        // array[0] = bool : 「サイトライセンス機関ID」を使用したログが存在する(ver2.2.0～)
        //      [1] = bool : 「サイトライセンス機関ID」を使用していないログが存在する(～ver2.2.0)
        //      [2] = bool : 「数値化IPアドレス」を使用していないログが存在する(～ver1.x)
        $exePattern = $this->setAggregateFormat($year, $month);
        
        // 検索数を集計する
        $this->aggregateSearchCnt($organization_id, $year, $month, $exePattern);
        // 閲覧数を集計する
        $this->aggregateOperationCntByIssn($organization_id, $year, $month, Repository_Components_Business_Logmanager::LOG_OPERATION_DETAIL_VIEW, $exePattern);
        // ダウンロード数を集計する
        $this->aggregateOperationCntByIssn($organization_id, $year, $month, Repository_Components_Business_Logmanager::LOG_OPERATION_DOWNLOAD_FILE, $exePattern);
        
        return true;
    }
    
    /**
     * Do a search number of aggregation of site license agency
     * サイトライセンス機関の検索数集計を行う
     *
     * @param int $organization_id sitelicense organization ID サイトライセンス機関ID
     * @param int $year aggregate year 集計年
     * @param int $month aggregate month 集計月
     * @param array $exePattern execute pattern 集計パターン
     *                           array["exeSitelicenseId"|"exeNumericIpAddress"|"exeStringIpAddress]
     * @return bool true/false success/failed 成功/失敗
     */
    private function aggregateSearchCnt($organization_id, $year, $month, $exePattern) {
        // 指定の範囲が集計済の場合は何も行わず終了する
        $params = array();
        $params[] = $organization_id;
        $params[] = $year;
        $params[] = $month;
        $result = $this->executeSqlFile(WEBAPP_DIR."/modules/repository/components/business/sitelicense/sitelicense_usage_searchkeyword_select_searchCount.sql", $params);
        if(count($result) > 0) {
            return true;
        }
        
        // 集計範囲取得
        $date = $this->crateDataScopeStr($year, $month);
        
        // 連続アクセス・集計対象外検索項目を除外するサブクエリ文字列
        // $subQuery["from"] from句
        //          ["where"] where句
        $excludeDuplicationSubQuery = array();
        // from
        $result = $this->executeSqlFile(WEBAPP_DIR."/modules/repository/components/business/sitelicense/target_search_item_select_getSearchItemId.sql");
        $targetStr = "";
        for($ii = 0; $ii < count($result); $ii++) {
            if($ii > 0) { $targetStr .= ", "; }
            $targetStr .= $result[$ii]['search_item_id'];
        }
        $excludeDuplicationSubQuery["from"] = " FROM {repository_log} AS LOG ".
                                              " INNER JOIN {repository_log_detail_search} AS ADS ".
                                              " ON ADS.advanced_search_id IN (".$targetStr.") ".
                                              " AND ADS.log_no = LOG.log_no ".
                                              " LEFT JOIN {repository_log_elapsed_time} AS ELA ".
                                              " ON ELA.log_no = LOG.log_no ";
        // where
        $excludeDuplicationSubQuery["where"] = " IFNULL( ELA.elapsed_time, 2147483647) > 30 ";
        
        $cntResult = 0;
        // 検索回数集計実行(サイトライセンスID別)
        if($exePattern[self::EXEFLAG_SITELICENSE_ID]) {
            $cntResult += $this->calcSearchCnt($organization_id, 1, $date["start_date"], $date["end_date"], $excludeDuplicationSubQuery, "");
        }
        // 検索回数集計実行(数値化IPアドレス別)
        if($exePattern[self::EXEFLAG_NUMERIC_IP_ADDRESS]) {
            // 数値化IPアドレスでの判定用のWhere句作成
            $conditionWhereQuery = $this->createWhereQueryByNumericIpAddress($organization_id);
            $cntResult += $this->calcSearchCnt(0, 1, $date["start_date"], $date["end_date"], $excludeDuplicationSubQuery, $conditionWhereQuery);
        }
        // 検索回数集計実行(文字列IPアドレス別)
        if($exePattern[self::EXEFLAG_IP_ADDRESS]) {
            // 文字列IPアドレスでの判定用のWhere句作成
            $conditionWhereQuery = $this->createWhereQueryByStringIpAddress($organization_id);
            $cntResult += $this->calcSearchCnt(0, 1, $date["start_date"], $date["end_date"], $excludeDuplicationSubQuery, $conditionWhereQuery);
            // ver1.5以前のログデータはサイトライセンスフラグがnullのため下記のパターンも実行する
            $cntResult += $this->calcSearchCnt(0, null, $date["start_date"], $date["end_date"], $excludeDuplicationSubQuery, $conditionWhereQuery);
        }
        
        // 集計テーブルへ追加
        $this->insertSearchAggregateCnt($organization_id, $year, $month, $cntResult);
        
        return true;
    }
    
    /**
     * Aggregate operation count by ISSN
     * ISSN別に操作回数を集計する
     *
     * @param int $organization_id sitelicense organization ID サイトライセンス機関ID
     * @param int $year aggregate year 集計年
     * @param int $month aggregate month 集計月
     * @param int $operation_id operation ID 操作ID
     * @param array $exePattern execute pattern 集計パターン
     *                           array["exeSitelicenseId"|"exeNumericIpAddress"|"exeStringIpAddress]
     * @return bool true/false success/failed 成功/失敗
     * @throws AppException
     */
    private function aggregateOperationCntByIssn($organization_id, $year, $month, $operation_id, $exePattern) {
        // 集計範囲取得
        $date = $this->crateDataScopeStr($year, $month);
        
        // 連続アクセス・集計対象外検索項目を除外するサブクエリ文字列
        // $subQuery["from"] from句
        //          ["where"] where句
        $excludeDuplicationSubQuery = array();
        $excludeDuplicationSubQuery["from"] = " FROM {repository_log} AS LOG ".
                                              " LEFT JOIN {repository_log_elapsed_time} AS ELA ".
                                              " ON ELA.log_no = LOG.log_no ";
        $excludeDuplicationSubQuery["where"] = " IFNULL( ELA.elapsed_time, 2147483647) > 30 ";
        
        // 全てのISSNを取得する
        $issnArray = $this->getAllOnlineIssn();
        $issn = "";
        $insertIssn = array();
        for($ii = 0; $ii < count($issnArray); $ii++) {
            // ISSN mojiretuwosakuseisuru string = "'xxxx-xxxx','yyyy-yyyy'..."
            if($ii > 0) { $issn .= ","; }
            $issn .= "'".$issnArray[$ii]["issn"]."'";
            // 集計用配列を作成する array[$online_issn] = 検索回数（最初に0で初期化する）
            $insertIssn[$issnArray[$ii]["issn"]] = 0;
        }
        
        // 検索回数集計実行(サイトライセンスID別)
        if($exePattern[self::EXEFLAG_SITELICENSE_ID]) {
            $result = $this->calcOperationCntForItem($organization_id, 1, $date["start_date"], $date["end_date"], $operation_id, $excludeDuplicationSubQuery, "");
            $this->calcOperationLogCountByIssn($insertIssn, $result, $issn);
        }
        // 検索回数集計実行(数値化IPアドレス別)
        if($exePattern[self::EXEFLAG_NUMERIC_IP_ADDRESS]) {
            // 数値化IPアドレスでの判定用のWhere句作成
            $conditionWhereQuery = $this->createWhereQueryByNumericIpAddress($organization_id);
            $result = $this->calcOperationCntForItem(0, 1, $date["start_date"], $date["end_date"], $operation_id, $excludeDuplicationSubQuery, $conditionWhereQuery);
            $this->calcOperationLogCountByIssn($insertIssn, $result, $issn);
        }
        // 検索回数集計実行(文字列IPアドレス別)
        if($exePattern[self::EXEFLAG_IP_ADDRESS]) {
            // 文字列IPアドレスでの判定用のWhere句作成
            $conditionWhereQuery = $this->createWhereQueryByStringIpAddress($organization_id);
            $result = $this->calcOperationCntForItem(0, 1, $date["start_date"], $date["end_date"], $operation_id, $excludeDuplicationSubQuery, $conditionWhereQuery);
            $this->calcOperationLogCountByIssn($insertIssn, $result, $issn);
            // ver1.5以前のログデータはサイトライセンスフラグがnullのため下記のパターンも実行する
            $result = $this->calcOperationCntForItem(0, null, $date["start_date"], $date["end_date"], $operation_id, $excludeDuplicationSubQuery, $conditionWhereQuery);
            $this->calcOperationLogCountByIssn($insertIssn, $result, $issn);
        }
        
        //ISSN別に集計テーブルへ追加
        // $insertIssn[$online_issn] = $cnt
        foreach($insertIssn AS $key => $value) {
            $this->insertOperationAggregateCnt($organization_id, $year, $month, $operation_id, $key, $value);
        }
        
        return true;
    }
    
    /**
     * Set the aggregate format to be executed by checking the log data
     * ログデータをチェックして実行する集計形式を設定する
     *
     * @param int $year aggregate year 集計年
     * @param int $month aggregate month 集計月
     * @return array $aggregateType aggregate type 集計実行を行う形式
     *                               array["exeSitelicenseId"|"exeNumericIpAddress"|"exeStringIpAddress]
     */
    private function setAggregateFormat($year, $month) {
        // 集計範囲取得
        $date = $this->crateDataScopeStr($year, $month);
        
        // 返却用配列
        $aggregateType = array();
        
        // サイトライセンス機関IDを使用しているデータがあるかチェックする
        $params = array();
        $params[] = $date["start_date"]; // 集計範囲(開始)
        $params[] = $date["end_date"];   // 集計範囲(終了)
        $params[] = 0;                     // サイトライセンス機関ID
        $result = $this->executeSqlFile(WEBAPP_DIR."/modules/repository/components/business/sitelicense/log_select_checkDataBySitelicenseId.sql", $params);
        $aggregateType[self::EXEFLAG_SITELICENSE_ID] = (count($result) > 0) ? true : false;
        
        // サイトライセンス機関IDが使用されていないデータがあるかチェックする
        $params = array();
        $params[] = $date["start_date"]; // 集計範囲(開始)
        $params[] = $date["end_date"];   // 集計範囲(終了)
        $params[] = 0;                     // サイトライセンス機関ID
        $params[] = 1;                     // サイトライセンスフラグ
        $params[] = 0;                     // 数値化IPアドレス
        $result = $this->executeSqlFile(WEBAPP_DIR."/modules/repository/components/business/sitelicense/log_select_checkDataByNumericIpAddress.sql", $params);
        $aggregateType[self::EXEFLAG_NUMERIC_IP_ADDRESS] = (count($result) > 0) ? true : false;
        
        // 数値化IPアドレスデータが-1であるデータがあるかチェックする
        $params = array();
        $params[] = $date["start_date"]; // 集計範囲(開始)
        $params[] = $date["end_date"];   // 集計範囲(終了)
        $params[] = -1;                    // 数値化IPアドレス
        $result = $this->executeSqlFile(WEBAPP_DIR."/modules/repository/components/business/sitelicense/log_select_checkDataByStringIpAddress.sql", $params);
        $aggregateType[self::EXEFLAG_IP_ADDRESS] = (count($result) > 0) ? true : false;
        
        return $aggregateType;
    }
    
    /**
     * Aggregate the number of search site license agency
     * 指定したサイトライセンス機関の検索数を集計する
     *
     * @param int $sitelicense_id sitelicense organization ID サイトライセンス機関ID
     * @param int $sitelicense_flag is sitelicense flag サイトライセンス機関であるかのフラグ値
     * @param string $start_date aggregate start date 集計開始年月日
     * @param string $end_date aggregate end date     集計終了年月日
     * @param string $excludeDuplicationSubQuery multiple access exclusion sub query 多重アクセス除外サブクエリ
     * @param string $conditionWhereQuery WHERE query WHERE句部分のクエリ文字列
     * @return int search count 指定範囲の検索回数
     */
    private function calcSearchCnt($sitelicense_id, $sitelicense_flag, $start_date, $end_date, $excludeDuplicationSubQuery, $conditionWhereQuery) {
        $params = array();
        $params[] = $start_date;                                                        // 集計開始年月日
        $params[] = $end_date;                                                          // 集計終了年月日
        $params[] = Repository_Components_Business_Logmanager::LOG_OPERATION_SEARCH;     // 操作ログID(検索)
        $params[] = '';                                                                 // 検索キーワード
        $params[] = $sitelicense_id;                                                    // サイトライセンス機関ID


        $query = " SELECT COUNT(LOG.record_date) AS CNT ".
                 $excludeDuplicationSubQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                 " WHERE ". $excludeDuplicationSubQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 " AND LOG.record_date >= ? ".
                 " AND LOG.record_date <= ? ".
                 " AND LOG.operation_id = ? ".
                 " AND NOT(LOG.search_keyword = ?) ".
                 " AND LOG.site_license_id = ? ";
        if(is_null($sitelicense_flag)) {
            $query .= "AND LOG.site_license IS NULL ";
        } else {
            $query .= "AND LOG.site_license = ? ";
            $params[] = $sitelicense_flag;                                                  // サイトライセンスフラグ
        }
        $query .= $conditionWhereQuery." ;";

        $result = $this->executeSql($query, $params);
        
        return intval($result[0]["CNT"]);
    }
    
    /**
     * Aggregate the operation number of the specified site license agency
     * 指定したサイトライセンス機関の操作件数を集計する
     *
     * @param int $sitelicense_id sitelicense organization ID サイトライセンス機関ID
     * @param int $sitelicense_flag is sitelicense flag サイトライセンス機関であるかのフラグ値
     * @param string $start_date aggregate start date 集計開始年月日
     * @param string $end_date aggregate end date     集計終了年月日
     * @param int $operation_id operation ID 操作ID
     * @param string $excludeDuplicationSubQuery multiple access exclusion sub query 多重アクセス除外サブクエリ
     * @param string $conditionWhereQuery WHERE query WHERE句部分のクエリ文字列
     * @return array $result operation count by item ID アイテムID別操作回数
     *                        array[0]["item_id"|"CNT"]
     */
    private function calcOperationCntForItem($sitelicense_id, $sitelicense_flag, $start_date, $end_date, $operation_id, $excludeDuplicationSubQuery, $conditionWhereQuery) {
        $params = array();
        $params[] = $start_date;
        $params[] = $end_date;
        $params[] = $operation_id;
        $params[] = $sitelicense_id;

        $query = "SELECT LOG.item_id, COUNT(LOG.record_date) AS CNT ".
                 $excludeDuplicationSubQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM]." ".
                 "WHERE ".$excludeDuplicationSubQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE]." ".
                 "AND LOG.record_date >= ? ".
                 "AND LOG.record_date <= ? ".
                 "AND LOG.operation_id = ? ".
                 "AND LOG.site_license_id = ? ";
        if(is_null($sitelicense_flag)) {
            $query .= "AND LOG.site_license IS NULL ";
        } else {
            $query .= "AND LOG.site_license = ? ";
            $params[] = $sitelicense_flag;                                                  // サイトライセンスフラグ
        }
        $query .= $conditionWhereQuery.
                  "GROUP BY LOG.item_id ;";

        $result = $this->executeSql($query, $params);
        
        return $result;
    }
    
    /**
     * Add a number of searches of the summary results to the database
     * 検索回数の集計結果をデータベースへ追加する
     *
     * @param int $organization_id sitelicense organization ID サイトライセンス機関ID
     * @param int $year aggregate year 集計年
     * @param int $month aggregate month 集計月
     * @param int $cnt search count 検索回数
     */
    private function insertSearchAggregateCnt($organization_id, $year, $month, $cnt) {
        // INSERT
        $params = array();
        $params[] = $organization_id;
        $params[] = $year;
        $params[] = $month;
        $params[] = $cnt;
        $this->executeSqlFile(WEBAPP_DIR."/modules/repository/components/business/sitelicense/sitelicense_usage_searchkeyword_insert_countData.sql", $params);
    }
    
    /**
     * Add an aggregate result of certain operations in the database
     * 特定の操作の集計結果をデータベースに追加する
     *
     * @param int $organization_id sitelicense organization ID サイトライセンス機関ID
     * @param int $year aggregate year 集計年
     * @param int $month aggregate month 集計月
     * @param int $operation_id operation ID 操作ID
     * @param string $issn online issn ONLINE ISSN値
     * @param int $cnt operation count 操作回数
     * @throws AppException
     */
    private function insertOperationAggregateCnt($organization_id, $year, $month, $operation_id, $issn, $cnt) {
        // ISSN詳細情報取得
        $issn_info = $this->getIssnInfo($issn);
        if(count($issn_info) == 0) {
            $this->errorLog("Get ISSN info failed.", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Get ISSN info failed.");
        }
        // INSERT
        $params = array();
        $params[] = $organization_id;             // サイトライセンス機関ID
        $params[] = $year;                        // 年
        $params[] = $month;                       // 月
        $params[] = $operation_id;                // 操作ID
        $params[] = $issn_info[0]["issn"];       // ISSN
        $params[] = $issn_info[0]["jtitle"];     // 雑誌名(日)
        $params[] = $issn_info[0]["jtitle_en"]; // 雑誌名(英)
        $params[] = $issn_info[0]["set_spec"];  // set spec
        $params[] = $cnt;                         // 操作回数
        $this->executeSqlFile(WEBAPP_DIR."/modules/repository/components/business/sitelicense/sitelicense_dlview_insert_operationCountByIssn.sql", $params);
    }
    
    /**
     * Get All online issn
     * 全てのISSNを取得する
     *
     * @return array $result ISSN array ISSN配列
     *                        array[0]["issn"]
     */
    private function getAllOnlineIssn() {
        // ISSN値を全て取得する
        $params = array();
        $params[] = 0; // 削除フラグ
        $result = $this->executeSqlFile(WEBAPP_DIR."/modules/repository/components/business/sitelicense/issn_select_getAllData.sql", $params);

        return $result;
    }

    /**
     * Get the ISSN information
     * 詳細なISSN情報を取得する
     *
     * @param string $issn online issn ONLINE ISSN値
     * @return array $result ISSN information ISSN詳細情報
     *                        array[0]["issn"|"jtitle"|"jtitle_en"|"set_spec"]
     */
    private function getIssnInfo($issn) {
        $params = array();
        $params[] = $issn; // ISSN値
        $params[] = 0;
        $result = $this->executeSqlFile(WEBAPP_DIR."/modules/repository/components/business/sitelicense/issn_select_getDetail.sql", $params);
        
        return $result;
    }
    
    /**
     * Exclusion item type sub-query acquisition process
     * 除外アイテムタイプサブクエリ取得処理
     * 
     * @param string $abbreviation table alias テーブル別名
     * 
     * @return string sub query string サブクエリ文字列
     */
    private function getExclusiveSitelicenseItemtype($abbreviation) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // 集計対象外アイテムタイプを取得する
        $result = $this->executeSqlFile(WEBAPP_DIR."/modules/repository/components/business/sitelicense/parameter_select_exclusiveItemType.sql");
        
        if(strlen($result[0]["param_value"]) == 0) {
            return "";
        }
        
        // サイトライセンス除外アイテムタイプID配列
        $sitelicense_item_type_id = explode(",", $result[0]["param_value"]);
        // PREFIX付きカラム名
        $column_name = $abbreviation. ".item_type_id";
        // サブクエリ文作成
        $exclusive_item_type = " AND ". $column_name. " NOT IN ( ";
        for($ii = 0; $ii < count($sitelicense_item_type_id); $ii++) {
            if($ii > 0) { $exclusive_item_type .= ", "; }
            $exclusive_item_type .= $sitelicense_item_type_id[$ii];
        }
        $exclusive_item_type .= " ) ";
        
        return $exclusive_item_type;
    }
    
    /**
     * create where query by numeric ip addoress
     * 数値化したIPアドレスを利用してwhere句を作成する
     *
     * @param int $sitelicense_id sitelicense ID サイトライセンスID
     * @return string WHERE query WHERE句のクエリ文字列
     *                 ex) LOG.numeric_ip_address >= 172017072019 AND LOG.numeric_ip_address <= 172017072020 ) OR (LOG.numeric_ip_address = 172017072214
     */
    private function createWhereQueryByNumericIpAddress($sitelicense_id){
        // サイトライセンスIPアドレス範囲情報を取得
        $sitelicenseManager = BusinessFactory::getFactory()->getBusiness("businessSitelicensemanager");
        $sitelicense_ip = $sitelicenseManager->searchSitelicenseIpAddress($sitelicense_id);

        $whereString = "";
        $whereParts = array();
        for($ii = 0; $ii < count($sitelicense_ip); $ii++) {
            $start_ip = 0;
            $finish_ip = 0;
            if(strlen($sitelicense_ip[$ii]["start_ip_address"]) > 0) {
                $start_ip_elements = explode(".", $sitelicense_ip[$ii]["start_ip_address"]);
                $start_ip = sprintf("%d", $start_ip_elements[0]).
                            sprintf("%03d", $start_ip_elements[1]).
                            sprintf("%03d", $start_ip_elements[2]).
                            sprintf("%03d", $start_ip_elements[3]);
                // 先頭の0を削除してINT型にする（32bit環境だとintvalが使えないため文字列操作で行う）
                $start_ip = ltrim($start_ip, "0");
                if(strlen($start_ip) == 0) { $start_ip = 0; }
            }
            if(strlen($sitelicense_ip[$ii]["finish_ip_address"]) > 0) {
                $finish_ip_elements = explode(".", $sitelicense_ip[$ii]["finish_ip_address"]);
                $finish_ip = sprintf("%d", $finish_ip_elements[0]).
                             sprintf("%03d", $finish_ip_elements[1]).
                             sprintf("%03d", $finish_ip_elements[2]).
                             sprintf("%03d", $finish_ip_elements[3]);
                // 先頭の0を削除してINT型にする（32bit環境だとintvalが使えないため文字列操作で行う）
                $finish_ip = ltrim($finish_ip, "0");
                if(strlen($finish_ip) == 0) { $finish_ip = 0; }
            }

            // IPレンジは開始のみ、開始&終了だけが設定可能
            if(intval($finish_ip) == 0) {
                $whereParts[] = " ( LOG.numeric_ip_address = ". $start_ip. " )";
            } else {
                $whereParts[] = " ( LOG.numeric_ip_address >= ". $start_ip. " AND LOG.numeric_ip_address <= ". $finish_ip. " )";
            }
        }

        // AND ( (n_ip >= 172017072019 AND n_ip <= 172017072020 ) OR (n_ip = 172017072214) )
        if(count($whereParts) > 0) {
            for($ii = 0; $ii < count($whereParts); $ii++) {
                if(strlen($whereString) > 0) { $whereString .= " OR "; }
                $whereString .= $whereParts[$ii];
            }
            $whereString = " AND (".$whereString.") ";
        }

        return $whereString;
    }
    
    /**
     * create where query by string ip addoress
     * IPアドレス文字列を利用してwhere句を作成する
     *
     * @param int $sitelicense_id sitelicense ID サイトライセンスID
     * @return string WHERE query WHERE句のクエリ文字列
     *                 ex) LOG.numeric_ip_address >= 172017072019 AND LOG.numeric_ip_address <= 172017072020 ) OR (LOG.numeric_ip_address = 172017072214
     */
    private function createWhereQueryByStringIpAddress($sitelicense_id){
        // サイトライセンスIPアドレス範囲情報を取得
        $sitelicenseManager = BusinessFactory::getFactory()->getBusiness("businessSitelicensemanager");
        $sitelicense_ip = $sitelicenseManager->searchSitelicenseIpAddress($sitelicense_id);
        
        // IPアドレス文字列で比較する（inet_aton : IPアドレスを数値型として扱うSQLの関数）
        $whereString = "";
        $whereParts = array();
        for($ii = 0; $ii < count($sitelicense_ip); $ii++) {
            $start_ip = $sitelicense_ip[$ii]["start_ip_address"];
            $finish_ip = $sitelicense_ip[$ii]["finish_ip_address"];
            // IPレンジは開始のみ、開始&終了だけが設定可能
            if(strlen($finish_ip) == 0){
                $whereParts[] = " ( inet_aton(LOG.ip_address) = inet_aton(\"". $start_ip. "\") )";
            } else {
                $whereParts[] = " ( inet_aton(LOG.ip_address) >= inet_aton(\"". $start_ip. "\") AND inet_aton(LOG.ip_address) <= inet_aton(\"". $finish_ip. "\") )";
            }
        }
        
        // AND ( (n_ip >= 172017072019 AND n_ip <= 172017072020 ) OR (n_ip = 172017072214) )
        if(count($whereParts) > 0) {
            for($ii = 0; $ii < count($whereParts); $ii++){
                if(strlen($whereString) > 0){ $whereString .= " OR "; }
                $whereString .= $whereParts[$ii];
            }
            $whereString = " AND LOG.numeric_ip_address = -1 AND (".$whereString.") ";
        }


        return $whereString;
    }
    
    /**
     * Get the item ID that belongs to the specified ISSN
     * 指定したISSNに所属するアイテムIDを取得する
     *
     * @param string $item_ids item ID string アイテムID文字列(zzz,yyy,zzz...)
     * @param string $issn     online issn string ISSN文字列(XXXX-YYYY,ZZZZ-AAAA,BBBB-CCCC,..)
     * @return array $result   item ID with online issn アイテムIDを所属ISSN
     *                          array[$ii]["item_id"|"online_issn"]
     */
    private function filterItemIdByOnlineIssn($itemIds, $issn) {
        $query = "SELECT ITEM.item_id, IDX.online_issn ".
                 "FROM {repository_index} AS IDX ".
                 "INNER JOIN {repository_position_index} AS POS ON IDX.index_id = POS.index_id ".
                 "INNER JOIN {repository_item} AS ITEM ON ITEM.item_id = POS.item_id ".
                 "WHERE IDX.biblio_flag = ? ".
                 "AND ITEM.item_id IN (".$itemIds.") ". 
                 "AND IDX.online_issn IN ( ". $issn. " ) ".
                 $this->getExclusiveSitelicenseItemtype("ITEM").";";
        $params = array();
        $params[] = 1;
        $result = $this->executeSql($query, $params);
        
        return $result;
    }
    
    /**
     * Get the date and time range of DateTime type from years
     * 年月からDateTime型の日時範囲を取得する
     *
     * @param int $year year 年
     * @param int $month month 月
     * @return array $date date range 開始・終了日時範囲
     *                      array["start_date"|"end_date"]
     */
    private function crateDataScopeStr($year, $month) {
        $firstDay = "01";
        $lastDay = date("t", mktime(0, 0, 0, $month, 1, $year));
        
        $date = array();
        $date["start_date"] = $year."-".$month."-".$firstDay." 00:00:00.000";
        $date["end_date"] = $year."-".$month."-".$lastDay." 23:59:59.000";
        
        return $date;
    }
    
    /**
     * It summarizes the aggregate results for each ISSN
     * ISSN毎に集計結果をまとめる
     *
     * @param array  $result aggregate result array 集計結果配列
     *                        array[$online_issn]
     * @param array $logs operation count by item ID アイテムID別操作回数
     *                     array[$ii]["item_id"|"CNT"]
     * @param  string $issn online issn ONLINE ISSN値("AAAA-BBBB,CCCC-DDDD,...")
     */
    private function calcOperationLogCountByIssn(&$result, $logs, $issn) {
        // 集計対象ログが無い場合終了する
        if(count($logs) == 0) { return; }
        
        // クエリ用のアイテムID文字列を作成する
        $itemIds = "";
        for($ii = 0; $ii < count($logs); $ii++) {
            if(strlen($itemIds) > 0) { $itemIds .= ","; }
            $itemIds .= $logs[$ii]["item_id"];
        }
        
        // ISSNが設定されているアイテムを絞り込む
        $item = $this->filterItemIdByOnlineIssn($itemIds, $issn);
        // ISSN毎に操作ログ件数をまとめなおす
        $dataCnt = 0;
        for($ii = 0; $ii < count($item); $ii++) {
            for($jj = 0; $jj < count($logs); $jj++) {
                if($item[$ii]["item_id"] == $logs[$jj]["item_id"]) {
                    $result[$item[$ii]["online_issn"]] += $logs[$jj]["CNT"];
                }
            }
        }
    }
}
?>