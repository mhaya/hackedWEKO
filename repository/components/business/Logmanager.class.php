<?php

/**
 * Log management common classes
 * ログ管理共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Bibtex.class.php 48455 2015-02-16 10:53:40Z atsushi_suzuki $
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
require_once WEBAPP_DIR.'/modules/repository/components/FW/BusinessBase.class.php';

/**
 * Repository module constant class
 * WEKO共通定数クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/RepositoryConst.class.php';

/**
 * Log management common classes
 * ログ管理共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Logmanager extends BusinessBase
{
    /**
     * Operation(Regist item)
     * 操作(アイテム登録)
     *
     * @var int
     */
    const LOG_OPERATION_ENTRY_ITEM = 1;
    /**
     * Operation(Download)
     * 操作(ダウンロード)
     *
     * @var int
     */
    const LOG_OPERATION_DOWNLOAD_FILE = 2;
    /**
     * Operation(Detail access)
     * 操作(詳細画面)
     *
     * @var int
     */
    const LOG_OPERATION_DETAIL_VIEW = 3;
    /**
     * Operation(Search)
     * 操作(検索)
     *
     * @var int
     */
    const LOG_OPERATION_SEARCH = 4;
    /**
     * Operation(Top)
     * 操作(トップページ)
     *
     * @var int
     */
    const LOG_OPERATION_TOP = 5;
    /**
     * Delete record number
     * 削除ログレコード数
     *
     * @var int
     */
    const REMOVE_LOG_NUM = 100;
    /**
     * Sub query type
     * サブクエリ形式
     * 
     * @var int
     */
    const SUB_QUERY_TYPE_DEFAULT = 0;
    
    /**
     * Sub query type(search)
     * サブクエリ形式(検索)
     * 
     * @var int
     */
    const SUB_QUERY_TYPE_RANKING = 1;
    /**
     * Sub query From
     * サブクエリFROM句
     *
     * @var string
     */
    const SUB_QUERY_KEY_FROM = "from";
    /**
     * Sub query Where
     * サブクエリWHERE句
     *
     * @var string
     */
    const SUB_QUERY_KEY_WHERE = "where";
    /**
     * Elapsed time
     * 経過時間
     *
     * @var int
     */
    const EXCLUDE_ELAPSED_TIME = 30;
    /**
     * The number of access confirmation log record
     * アクセス確認ログレコード数
     *
     * @var int
     */
    const ACCESS_CHECK_LOG_NUM = 100;
    /**
     * Maximum INT
     * INT最大値
     *
     * @var int
     */
    const INT_MAX_SIZE = 2147483647;
    
    /**
     * Max time that dates back in the elapsed time calculation
     * 経過時間の計算で遡る最大時間
     *
     * @var string
     */
    const MAX_TIME_DATE_BACK_ELAPSED_TIME = "31 second";
    
    // Improve Search Log 2015/03/19 K.Sugimoto --start--
    // logNo of search
    /**
     * Search log number list
     * 検索ログ通番一覧
     *
     * @var array[$ii]
     */
    private $searchLogNoList = null;
    // Improve Search Log 2015/03/19 K.Sugimoto --end--
    
    /**
     * enrty log for operate WEKO
     * ログ登録
     *
     * @param int $operation_id Operation id 操作ID
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $attr_id Attribute id 属性ID
     * @param int $file_no File number ファイル通番
     * @param string $search_keyword Search keyword 検索キーワード
     * @param int $search_id Search id 検索項目ID
     * @return int Log number ログ通番
     */
    private function entryLog($operation_id, $item_id = 0, $item_no = 0, $attr_id= 0, $file_no = 0, $search_keyword = "", $search_id = -1)
    {
        // get session from DIContainer
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        
        // user_id
        $user_id = $this->user_id;
        // ip address(support proxy)
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strlen($_SERVER['HTTP_X_FORWARDED_FOR']) > 0) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = getenv("REMOTE_ADDR");
        }
        // convert ip address to numeric
        
        // check entry log by ip address and user agent
        if($this->isEntryLogByIpAddress($ip) === false )
        {
            return 0;
        }
        if($this->isEntryLogByUserAgent($_SERVER['HTTP_USER_AGENT']) === false)
        {
            return 0;
        }
        // if entry search log and search keyword is empty, search log is not entry 
        if($operation_id == RepositoryConst::LOG_OPERATION_SEARCH && strlen($search_keyword) === 0){
            return 0;
        }
        
        // host
        // when host is null, host eq ip address
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strlen($_SERVER['HTTP_X_FORWARDED_FOR']) > 0) {
            $host = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $host = getenv("REMOTE_HOST");
        }
        if ($host == null || $host == $ip){
            $host = gethostbyaddr($ip);
        }
        // file_status
        $fileStatus = RepositoryConst::LOG_FILE_STATUS_UNKNOWN;
        // site_license
        $siteLicense = RepositoryConst::LOG_SITE_LICENSE_OFF;
        // input_type
        $inputType = null;
        // login_status
        $loginStatus = null;
        // group_id
        $groupId = null;
        
        // numeric ip address
        $ip_elements = explode(".", $ip);
        $numeric_ip = sprintf("%d", $ip_elements[0]).
                      sprintf("%03d", $ip_elements[1]).
                      sprintf("%03d", $ip_elements[2]).
                      sprintf("%03d", $ip_elements[3]);
        // referer
        $referer = "";
        if(isset($_SERVER["HTTP_REFERER"]))
        {
            $referer = $_SERVER["HTTP_REFERER"];
        }
        else 
        {
            $referer = getenv("HTTP_REFERER");
        }
        
        // TODO
        // needs rewrite when devide RepositoryAction and Validator by function
        
        // If file download, set download status
        if($operation_id == self::LOG_OPERATION_DOWNLOAD_FILE 
            && intval($item_id)>0 && intval($item_no)>0 && intval($attr_id)>0 && intval($file_no)>0)
        {
            // Get file download status
            require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
            $repositoryAction = new RepositoryAction();
            $repositoryAction->Session = $session;
            $repositoryAction->Db = $this->Db;
            $repositoryAction->TransStartDate = $this->accessDate;
            $repositoryAction->dbAccess = $this->Db;
            $repositoryAction->setConfigAuthority();
            $repositoryAction->getFileDownloadStatusForEntryLog(
                $item_id, $item_no, $attr_id, $file_no,
                $fileStatus, $inputType, $loginStatus, $groupId);
        }
        // Add site license info to log 2013/07/02 A.Suzuki --start--
        // Set Validator
        require_once WEBAPP_DIR. '/modules/repository/validator/Validator_DownloadCheck.class.php';
        $validator = new Repository_Validator_DownloadCheck();
        $initResult = $validator->setComponents($session, $this->Db);

        // Check Site License
        $siteLicenseId = 0;
        $result = $validator->checkSiteLicense($item_id, $item_no, $siteLicenseId);
        if($result == "true")
        {
            $siteLicense = RepositoryConst::LOG_SITE_LICENSE_ON;
        }
        // Add site license info to log 2013/07/02 A.Suzuki --end--
        
        // get log id
        $log_no = $this->Db->nextSeq("repository_log");
        
        // entry log
        $query_log = "INSERT INTO ". DATABASE_PREFIX ."repository_log ".
                    " ( log_no, record_date, user_id, operation_id, ".
                    " item_id, item_no, attribute_id, file_no, search_keyword, ".
                    " ip_address, numeric_ip_address, host, user_agent, referer, file_status, site_license, site_license_id, input_type, login_status, group_id) ".
                    " VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
        $params = array();
        $params[] = intval($log_no);
        $params[] = $this->accessDate;
        $params[] = $user_id;
        $params[] = $operation_id;
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $attr_id;
        $params[] = $file_no;
        $params[] = $search_keyword;
        $params[] = $ip;
        $params[] = $numeric_ip;
        $params[] = $host;
        $params[] = $_SERVER['HTTP_USER_AGENT'];
        $params[] = $referer;
        $params[] = $fileStatus; // file_status
        $params[] = $siteLicense; // site_license
        $params[] = $siteLicenseId; // site_license_id
        $params[] = $inputType; // input_type
        $params[] = $loginStatus; // login_status
        $params[] = $groupId; // group_id
        $result = $this->Db->execute($query_log, $params);
        if ($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $this->insertElapsedTimeRecord($operation_id, $this->accessDate, $ip, $_SERVER['HTTP_USER_AGENT'], $item_id, $item_no, $attr_id, $file_no, $this->user_id, $log_no, $search_keyword, $search_id);
        
        return $log_no;
    }
    
    /**
     * Check entry log by ip address
     * ログ登録可能かを確認
     *
     * @param string $ipAddr Ip addoress IPアドレス
     * @return boolean Result 結果
     *                 true : entry log OK
     *                 false: is not entry log
     */
    private function isEntryLogByIpAddress($ipAddr)
    {
        // by IP address list table
        $query = "SELECT * ". 
                 "FROM ". DATABASE_PREFIX. "repository_robotlist_master AS MASTER, ". DATABASE_PREFIX. "repository_robotlist_data AS DATAS ".
                 "WHERE MASTER.robotlist_id = DATAS.robotlist_id ". 
                 "AND MASTER.del_column = ? ". 
                 "AND DATAS.word = ? ". 
                 "AND DATAS.status != ? ". 
                 "AND MASTER.is_delete = ? ". 
                 "AND DATAS.is_delete = ? ".
                 "LIMIT 0,1 ;";
        
        $params = array();
        $params[] = "ip_address"; // del_column
        $params[] = $ipAddr;      // word
        $params[] = "-1";         // status
        $params[] = 0;            // is_delete(robotlist_master)
        $params[] = 0;            // is_delete(robotlist_data)
        
        $result = $this->Db->execute($query, $params);
        if($result === false || count($result) > 1)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        if(count($result) === 1)
        {
            return false;
        }
        
        // by parameter table
        $query = "SELECT param_value ". 
                 " FROM ". DATABASE_PREFIX. "repository_parameter ". 
                 " WHERE param_name = ? ". 
                 " AND is_delete = ?;";
        $params = array();
        $params[] = 'log_exclusion';
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // devide param_value
        $ipAddrStr = str_replace("\r\n", "\n", $result[0]['param_value']);
        $ipAddrStr = str_replace("\r", "\n", $ipAddrStr);
        $ipList = explode("\n", $ipAddrStr);
        for($cnt = 0; $cnt < count($ipList); $cnt++)
        {
            if($ipAddr === $ipList[$cnt])
            {
                return false;
            }
        }
        
        // entry log
        return true;
    }
    
    /**
     * Check entry log by user agent
     * ユーザエージェントからログ登録可能かを確認する
     *
     * @param string $userAgent User agent ユーザエージェント
     * @return boolean Result 結果
     *                 true : entry log OK
     *                 false: is not entry log
     */
    private function isEntryLogByUserAgent($userAgent)
    {
        // ロボットリストテーブルから有効なユーザーエージェント情報を全て取得する
        $query = "SELECT * ". 
                 "FROM ". DATABASE_PREFIX. "repository_robotlist_master AS MASTER, ". DATABASE_PREFIX. "repository_robotlist_data AS DATAS ".
                 "WHERE MASTER.robotlist_id = DATAS.robotlist_id ". 
                 "AND MASTER.del_column = ? ". 
                 "AND DATAS.status != ? ". 
                 "AND MASTER.is_delete = ? ". 
                 "AND DATAS.is_delete = ? ; ";
        
        $params = array();
        $params[] = "user_agent"; // del_column
        $params[] = "-1";         // status
        $params[] = 0;            // is_delete(robotlist_master)
        $params[] = 0;            // is_delete(robotlist_data)
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        for($cnt = 0; $cnt < count($result); $cnt++)
        {
            // ユーザーエージェントとロボットリストを部分一致で照合する
            if(is_numeric(strpos($userAgent, $result[$cnt]['word'])))
            {
                return false;
            }
        }
        return true;
    }
    
    /**
     * remove operation logs from weko
     * ログレコード削除
     *
     * @param int $startLogNo Start number 開始番号
     * @param string $whereString Where string WHERE句
     * @param array $whereParams Where parameter WHEREパラメータ
     */
    private function removeExclusionLog($startLogNo, $whereString, $whereParams)
    {
        $query = "DELETE FROM " . DATABASE_PREFIX . "repository_log " .
                 "WHERE log_no >= ? " . 
                 "AND ? > log_no " . 
                 "AND (" . $whereString . ") ; ";
        
        $params = array();
        $params[] = $startLogNo;
        $params[] = $startLogNo + self::REMOVE_LOG_NUM;
        array_merge($params, $whereParams);
        
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    
    /**
     * get sub query for analize log
     * remove same access on 30 seconds
     * 二重アクセス防止用のサブクエリを取得
     *
     * @param int $subQueryType Sub query type サブクエリの種類
     * @return string Subquery for unauthorized access prevention 二重アクセス防止用のサブクエリ
     */
    public static function getSubQueryForAnalyzeLog($subQueryType = self::SUB_QUERY_TYPE_DEFAULT)
    {
        $subQuery = array("query"=>"", "where"=>"");
        
        if($subQueryType === self::SUB_QUERY_TYPE_DEFAULT){
            $subQuery[self::SUB_QUERY_KEY_FROM] = " FROM {repository_log} AS LOG ".
                                 " LEFT JOIN {repository_log_elapsed_time} AS ELA ".
                                 " ON ELA.log_no = LOG.log_no ";
            $subQuery[self::SUB_QUERY_KEY_WHERE] = " IFNULL( ELA.elapsed_time, ".self::INT_MAX_SIZE.") > ".self::EXCLUDE_ELAPSED_TIME." ";
        } else if ($subQueryType === self::SUB_QUERY_TYPE_RANKING){
            $query = "SELECT search_item_id ".
                     "FROM {repository_target_search_item} ".
                     "WHERE ranking_flag = ? ".
                     "AND is_delete = ?;";
            
            $params = array();
            $params[] = 1;
            $params[] = 0;
            
            if(!isset($Db)){
                $container =& DIContainerFactory::getContainer();
                $Db =& $container->getComponent("DbObject");
            }
            $result = $Db->execute($query, $params);
            
            $targetStr = "";
            if(isset($result) && count($result) > 0)
            {
            	for($ii = 0; $ii < count($result); $ii++)
            	{
            		if($ii != 0)
            		{
            			$targetStr .= ", ";
            		}
            		$targetStr .= $result[$ii]['search_item_id'];
            	}
            }
            
            $subQuery[self::SUB_QUERY_KEY_FROM] = " FROM {repository_log} AS LOG ".
                                 " INNER JOIN {repository_log_detail_search} AS ADS ".
                                 " ON ADS.advanced_search_id IN (".$targetStr.") ".
                                 " AND ADS.log_no = LOG.log_no ".
                                 " LEFT JOIN {repository_log_elapsed_time} AS ELA ".
                                 " ON ELA.log_no = LOG.log_no ";
            $subQuery[self::SUB_QUERY_KEY_WHERE] = " IFNULL( ELA.elapsed_time, ".self::INT_MAX_SIZE.") > ".self::EXCLUDE_ELAPSED_TIME." ";
        }
        
        return $subQuery;
    }
    
    /**
     * update elapsed log of same log
     * 経過時間を更新する
     *
     * @param int $startLogNo Start number 開始番号
     */
    public function updateElapsedTime($startLogNo)
    {
        $query = "SELECT * " . 
                 "FROM " . DATABASE_PREFIX . "repository_log " .
                 "WHERE ? > log_no AND log_no >= ? ; ";
        
        $params = array();
        $params[] = $startLogNo + self::ACCESS_CHECK_LOG_NUM;
        $params[] = $startLogNo;
        
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        for ($ii = 0; $ii < count($result); $ii++)
        {
            $this->insertElapsedTimeRecord(
                $result[$ii]["operation_id"], 
                $result[$ii]["record_date"], 
                $result[$ii]["ip_address"], 
                $result[$ii]["user_agent"], 
                $result[$ii]["item_id"], 
                $result[$ii]["item_no"], 
                $result[$ii]["attribute_id"], 
                $result[$ii]["file_no"], 
                $result[$ii]["user_id"], 
                $result[$ii]["log_no"],
                $result[$ii]["search_keyword"]
            );
        }
    }
    
    /**
     * search same access, get elasped time from same access and insert elapsed time
     * ログおよび同アクセスからの経過時間を保存
     *
     * @param int $operation_id Operation id 操作ID
     * @param string $date Date 日付
     * @param string $ip_address IP address IPアドレス
     * @param string $user_agent User agent ユーザエージェント
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $attr_id Attribute id 属性ID
     * @param int $file_no File number ファイル通番
     * @param string $user_id User id ユーザID
     * @param int $log_no Log number ログ通番
     * @param string $search_keyword Search keyword 検索キーワード
     * @param int $search_id Search id 検索項目ID
     */
    public function insertElapsedTimeRecord($operation_id, $date, $ip_address, $user_agent, $item_id, $item_no, $attr_id, $file_no, $user_id, $log_no, $search_keyword, $search_id = -1)
    {
        if($operation_id == self::LOG_OPERATION_SEARCH){
            $result = $this->calcElapsedTimeOnSameKeywordAccess($operation_id, $date, $ip_address, $user_agent, $item_id, $item_no, $attr_id, $file_no, $user_id, $log_no, $search_keyword, $search_id);
        }else{
            $result = $this->calcElapsedTimeOnSameAccess($operation_id, $date, $ip_address, $user_agent, $item_id, $item_no, $attr_id, $file_no, $user_id, $log_no);
        }
        
        $elapseTime = 0;
        if(count($result) === 1){
            $elapseTime = $result[0]['elapsed_time'];
        }
        else if(count($result) === 0){
            $elapseTime = self::INT_MAX_SIZE;
        }
        
        // insert elapsed time
        $query = "INSERT INTO ". DATABASE_PREFIX. "repository_log_elapsed_time ".
                 " (log_no, elapsed_time, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete) ". 
                 " VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $params = array();
        $params[] = $log_no;
        $params[] = $elapseTime;
        $params[] = $this->user_id;
        $params[] = $this->user_id;
        $params[] = '';
        $params[] = $this->accessDate;
        $params[] = $this->accessDate;
        $params[] = '';
        $params[] = 0;
        
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    
    /**
     * calculate elapsed time on same access
     * 同アクセスからの経過時間を計算
     *
     * @param int $operation_id Operation id 操作ID
     * @param string $date Date 日付
     * @param string $ip_address IP address IPアドレス
     * @param string $user_agent User agent ユーザエージェント
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $attr_id Attribute id 属性ID
     * @param int $file_no File number ファイル通番
     * @param string $user_id User id ユーザID
     * @param int $log_no Log number ログ通番
     */
    private function calcElapsedTimeOnSameAccess($operation_id, $date, $ip_address, $user_agent, $item_id, $item_no, $attr_id, $file_no, $user_id, $log_no)
    {
        // search same access
        $query = "SELECT log_no, UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(record_date) AS elapsed_time ". 
                 " FROM {repository_log} ".
                 " WHERE operation_id = ? ".
                 " AND record_date > ? ".
                 " AND record_date <= ? ".
                 " AND user_id = ? ".
                 " AND log_no != ? ".
                 " AND ip_address = ? ".
                 " AND user_agent = ? ".
                 " AND item_id = ? ".
                 " AND item_no = ? ". 
                 " AND attribute_id = ? ".
                 " AND file_no = ? ".
                 " ORDER BY UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(record_date) ASC ".
                 " LIMIT 1;";
        $params = array();
        $params[] = $date;
        $params[] = $operation_id;
        $params[] = date("Y-m-d H:i:s.000", strtotime("$date -".self::MAX_TIME_DATE_BACK_ELAPSED_TIME));
        $params[] = $date;
        $params[] = $user_id;
        $params[] = $log_no;
        $params[] = $ip_address;
        $params[] = $user_agent;
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $attr_id;
        $params[] = $file_no;
        $params[] = $date;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
     * calculate elapsed time on same keyword access
     * 同アクセスからの経過時間を計算(検索)
     *
     * @param int $operation_id Operation id 操作ID
     * @param string $date Date 日付
     * @param string $ip_address IP address IPアドレス
     * @param string $user_agent User agent ユーザエージェント
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $attr_id Attribute id 属性ID
     * @param int $file_no File number ファイル通番
     * @param string $user_id User id ユーザID
     * @param int $log_no Log number ログ通番
     * @param string $search_keyword Search keyword 検索キーワード
     * @param int $search_id Search id 検索項目ID
     */
    private function calcElapsedTimeOnSameKeywordAccess($operation_id, $date, $ip_address, $user_agent, $item_id, $item_no, $attr_id, $file_no, $user_id, $log_no, $search_keyword, $search_id)
    {
        // search same keyword access
        $query = "SELECT LOG.log_no, UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(LOG.record_date) AS elapsed_time ". 
                 " FROM {repository_log} AS LOG".
                 " LEFT JOIN {repository_log_detail_search} AS ADS ".
                 " ON ADS.log_no = LOG.log_no ".
                 " WHERE LOG.operation_id = ? ".
                 " AND LOG.record_date > ? ".
                 " AND LOG.record_date <= ? ".
                 " AND LOG.user_id = ? ".
                 " AND LOG.log_no != ? ".
                 " AND LOG.ip_address = ? ".
                 " AND LOG.user_agent = ? ".
                 " AND LOG.item_id = ? ".
                 " AND LOG.item_no = ? ".
                 " AND LOG.attribute_id = ? ".
                 " AND LOG.file_no = ? ".
                 " AND LOG.search_keyword = ? ";
        $params = array();
        $params[] = $date;
        $params[] = $operation_id;
        $params[] = date("Y-m-d H:i:s.000", strtotime("$date -".self::MAX_TIME_DATE_BACK_ELAPSED_TIME));
        $params[] = $date;
        $params[] = $user_id;
        $params[] = $log_no;
        $params[] = $ip_address;
        $params[] = $user_agent;
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $attr_id;
        $params[] = $file_no;
        $params[] = $search_keyword;
        if($search_id >= 0)
        {
            $query .= " AND ADS.advanced_search_id = ? ";
            $params[] = $search_id;
        }
        $query .= " ORDER BY UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(LOG.record_date) ASC ".
                  " LIMIT 1;";
        $params[] = $date;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
     * entry log for regist item
     * アイテム登録のログを保存
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     */
    public function entryLogForRegistItem($item_id, $item_no)
    {
        $this->entryLog(self::LOG_OPERATION_ENTRY_ITEM, $item_id, $item_no);
    }
    
    /**
     * entry log for downlord file
     * ダウンロードログを保存
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $attr_id Attribute id 属性ID
     * @param int $file_no File number ファイル通番
     */
    public function entryLogForDownload($item_id, $item_no, $attr_id, $file_no)
    {
        $this->entryLog(self::LOG_OPERATION_DOWNLOAD_FILE, $item_id, $item_no, $attr_id, $file_no);
    }
    
    /**
     * entry log for view item detail
     * 詳細画面アクセスログを保存
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     */
    public function entryLogForDetailView($item_id, $item_no)
    {
        $this->entryLog(self::LOG_OPERATION_DETAIL_VIEW, $item_id, $item_no);
    }
    
    /**
     * entry log for keyword search
     * 検索ログを保存
     *
     * @param string $search_keyword Search keyword 検索キーワード
     * @param int $search_id Search id 検索項目ID
     */
    public function entryLogForKeywordSearch($search_keyword, $search_id)
    {
        $ret = $this->entryLog(self::LOG_OPERATION_SEARCH, 0, 0, 0, 0, $search_keyword, $search_id);
        
        if($ret !== 0)
        {
	        // Improve Search Log 2015/03/19 K.Sugimoto --start--
	        $query = "INSERT IGNORE INTO ". DATABASE_PREFIX. "repository_log_detail_search ".
	                 " (log_no, advanced_search_id, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete) ". 
	                 " VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?); ";
	        $params = array();
	        $params[] = $ret;
	        $params[] = $search_id;
	        $params[] = $this->user_id;
	        $params[] = $this->user_id;
	        $params[] = '';
	        $params[] = $this->accessDate;
	        $params[] = $this->accessDate;
	        $params[] = '';
	        $params[] = 0;
	        
	        $result = $this->Db->execute($query, $params);
	        if($result === false){
	            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
	            throw new AppException($this->Db->ErrorMsg());
	        }
	        // Improve Search Log 2015/03/19 K.Sugimoto --end--
        }
    }
    
    /**
     * entry log for view top page
     * トップページログを保存
     */
    public function entryLogForTopView()
    {
        $this->entryLog(self::LOG_OPERATION_TOP);
    }
    
    // Improve Search Log 2015/03/19 K.Sugimoto --start--
    /**
     * Get search log no
     * 検索ログのログ通番を取得する
     *
     * @param int $log_no Start number 開始番号
     * @param int $end_no End number 終了番号
    */
    public function isInsertDetailSearchAndCalcInsertLog($log_no, &$end_no)
    {
        $query = "SELECT log_no ". 
                 " FROM ". DATABASE_PREFIX. "repository_log ".
                 " WHERE operation_id = ? ".  
                 " AND log_no >= ? ". 
                 " ORDER BY log_no ASC ".
                 " LIMIT 10000;";
        
        $params = array();
        $params[] = self::LOG_OPERATION_SEARCH;
        $params[] = $log_no;
        
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $this->searchLogNoList = $result;
        
        if(!isset($result) || count($result) == 0)
        {
        	return false;
        }
        
        $end_no = $result[count($result) - 1]['log_no'];
        
        return true;
    }
    
    /**
     * add detail search item id
     * 詳細検索項目IDを更新
     */
    public function addDetailSearchItem()
    {
        for($ii = 0; $ii < count($this->searchLogNoList); $ii++)
        {
	        $query = "INSERT IGNORE INTO ". DATABASE_PREFIX. "repository_log_detail_search ".
	                 " (log_no, advanced_search_id, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete) ". 
	                 " VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?); ";
	        $params = array();
	        $params[] = $this->searchLogNoList[$ii]['log_no'];
	        $params[] = 0;
	        $params[] = $this->user_id;
	        $params[] = $this->user_id;
	        $params[] = '';
	        $params[] = $this->accessDate;
	        $params[] = $this->accessDate;
	        $params[] = '';
	        $params[] = 0;
	        
	        $result = $this->Db->execute($query, $params);
	        if($result === false){
	            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
	            throw new AppException($this->Db->ErrorMsg());
	        }
        }
    }
    // Improve Search Log 2015/03/19 K.Sugimoto --end--
    
    /**
     * robotlist log delete
     * ロボットのログを削除する
     *
     * @param string $column Column name カラム名
     * @param string $word Word 検索語句
     */
    public function removeLogByRobotlistWord($column, $word)
    {
        $query = "DELETE FROM ". DATABASE_PREFIX. "repository_log ". 
                 "WHERE " . $column . " LIKE ? ; ";
        
        $params = array();
        // Improve Log 2015/06/17 K.Sugimoto --start--
        if($column == 'ip_address')
        {
            $params[] = $word;
        }
        else
        {
            $params[] = "%".$word."%";
        }
        // Improve Log 2015/06/17 K.Sugimoto --end--
        
        $result = $this->Db->execute($query, $params);
        
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    
    /**
     * exclusion address log delete
     * 除外対象アドレスのログを削除する
    */
    public function removeExclusionAddress()
    {
        $query = "SELECT * ". 
                 "FROM ". DATABASE_PREFIX. "repository_parameter ". 
                 "WHERE param_name = ? ".
                 "AND is_delete = ? ; ";
        
        $params = array();
        $params[] = "log_exclusion";
        $params[] = 0;
        
        $result = $this->Db->execute($query, $params);
        
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $log_exclusion = str_replace("\r\n", "\n", $result[0]["param_value"]);
        $exclusionLogs = explode("\n", $log_exclusion);
        
        $addresses = "";
        for ($ii = 0; $ii < count($exclusionLogs); $ii++) {
            if (strlen($exclusionLogs[$ii]) > 0) {
                if (!empty($addresses)) {
                    $addresses .= ",";
                }
                $addresses .= "'" . trim($exclusionLogs[$ii]) ."'";
            }
        }
        
        // delete logs
        if (!empty($addresses)) 
        {
            $query = "DELETE FROM ". DATABASE_PREFIX. "repository_log ". 
                     "WHERE ip_address IN (" . $addresses . ") ; ";
            
            $params = array();
            $result = $this->Db->execute($query, $params);
            
            if($result === false) {
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
        }
    }
    
    /**
     * Add Excluded Ip Address List to repository_parameter table
     * 除外対象IPアドレスを追加する
     * 
     * @param string $addExcludedIpAddr Excluded Ip Address 除外対象IPアドレス
     */
    public function addExcludedIpAddrToDatabase($addExcludedIpAddr)
    {
        // get exclude ip address by parameter table
        $query = "SELECT param_value ". 
                 " FROM ". DATABASE_PREFIX. "repository_parameter ".
                 " WHERE param_name = ?;";
        $params = array();
        $params[] = 'log_exclusion';
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        $ipAddresses = $result[0]['param_value'];
        $ipAddresses = str_replace("\r\n", "\n", $ipAddresses);
        $ipAddresses = str_replace("\r", "\n", $ipAddresses);
        
        // explode ip addresses by request parameter
        $ipList = explode(",", $addExcludedIpAddr);
        
        // join database ip addresses and request parameter
        for($ii = 0; $ii < count($ipList); $ii++)
        {
            if(strlen($ipList[$ii]) > 0)
            {
                if(is_numeric(strpos($ipAddresses, $ipList[$ii])))
                {
                    // the ip address is already exist
                }
                else
                {
                    if(strlen($ipAddresses) > 0)
                    {
                        $ipAddresses .= "\n";
                    }
                    $ipAddresses .= $ipList[$ii];
                }
            }
        }
        
        // update parameter table
        $query = "UPDATE ". DATABASE_PREFIX. "repository_parameter ". 
                 " SET param_value = ? ". 
                 " WHERE param_name = ?;";
        $params = array();
        $params[] = $ipAddresses;
        $params[] = 'log_exclusion';
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
}
?>