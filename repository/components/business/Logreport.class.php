<?php

/**
 * Canned reports create a common class
 * 定型レポート作成共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Logreport.class.php 51740 2015-04-08 01:49:14Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/Logbase.class.php';

/**
 * Canned reports create a common class
 * 定型レポート作成共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Logreport extends Repository_Components_Business_Logbase 
{
    // const
    /**
     * Key name(sitelicense)
     * キー名(サイトライセンス)
     *
     * @var string
     */
    const IS_SITELICENSE = "is_sitelicense";
    /**
     * Key name(not sitelicense)
     * キー名(サイトライセンスではない)
     *
     * @var string
     */
    const IS_NOT_SITELICENSE = "is_not_sitelicense";
    
    /**
     * The maximum number of files records reference
     * ファイルレコード参照最大数
     *
     * @var int
     */
    const LIMIT_NUM = 11000;
    
    // member
    // start date
    /**
     * Aggregate start year
     * 集計開始年
     *
     * @var int
     */
    private $sy_log = 0;
    /**
     * Aggregate start month
     * 集計開始月
     *
     * @var int
     */
    private $sm_log = 0;
    /**
     * Aggregate start day
     * 集計開始日
     *
     * @var int
     */
    private $sd_log = 1;
    
    // end date
    /**
     * Aggregate end year
     * 集計開始年
     *
     * @var int
     */
    private $ey_log = 0;
    /**
     * Aggregate end month
     * 集計開始月
     *
     * @var int
     */
    private $em_log = 0;
    /**
     * Aggregate end day
     * 集計開始日
     *
     * @var int
     */
    private $ed_log = 31;
    
    /**
     * Administrator-based authority level
     * 管理者ベース権限レベル
     *
     * @var int
     */
    private $repository_admin_base = null;
    /**
     * Administrator Room authority level
     * 管理者ルーム権限レベル
     *
     * @var int
     */
    private $repository_admin_room = null;
    
    // setter
    /**
     * Set start year
     * 開始年設定
     *
     * @param string $year start year 開始年
     */
    public function setStartYear($year)     {    $this->sy_log = $year;     }
    /**
     * Set start month
     * 開始月設定
     *
     * @param string $month start month 開始月
     */
    public function setStartMonth($month)   {    $this->sm_log = $month;    }
    /**
     * Set end year
     * 終了年設定
     *
     * @param string $year end year 終了年
     */
    public function setEndYear($year)       {    $this->ey_log = $year;     }
    /**
     * Set end month
     * 終了月設定
     *
     * @param string $month end month 終了月
     */
    public function setEndMonth($month)     {    $this->em_log = $month;    }
    /**
     * Set admin base
     * 管理者ベース権限設定
     *
     * @param int $repository_admin_base admin base 管理者ベース権限
     */
    public function setAdminBase($repository_admin_base){        $this->repository_admin_base = $repository_admin_base;    }
    /**
     * Set admin room
     * 管理者ルーム権限設定
     *
     * @param int $repository_admin_room admin room 管理者ルーム権限
     */
    public function setAdminRoom($repository_admin_room){        $this->repository_admin_room = $repository_admin_room;    }
    
    /**
     * (Deprecated)create each log report data
     * (廃止予定)
     */
    protected function executeApp()
    {
    }
    
    // private method(international processing)
    /**
     * check create supple report
     * Suppleレポートが作成できるかを確認
     *
     * @return boolean Is create 作成できるか
     */
    public function isCreateSuppleReport()
    {
        // supple_weko_url of parameter table is empty?
        $query = "SELECT param_value ". 
                 " FROM ". DATABASE_PREFIX. "repository_parameter ".
                 " WHERE param_name = ? ". 
                 " AND is_delete = ?; ";
        $params = array();
        $params[] = 'supple_weko_url';
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        if(strlen($result[0]['param_value']) === 0)
        {
            $this->traceLog("supple_weko_url is empty", __FILE__, __CLASS__, __LINE__);
            return false;
        }
        
        // is exists record(is_delete = 0) of supple table?
        $query = "SELECT * ". 
                 " FROM ". DATABASE_PREFIX. "repository_supple ". 
                 " WHERE is_delete = ?;";
        $params = array();
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        if(count($result) <= 0)
        {
            $this->traceLog("repository_supple table has not record", __FILE__, __CLASS__, __LINE__);
            return false;
        }
        
        // y-handle prefix is set?
        require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        $handleManager = new RepositoryHandleManager($session, $this->Db, $this->accessDate);
        $yHandlePrefix = $handleManager->getYHandlePrefix();
        if(strlen($yHandlePrefix) === 0)
        {
            $this->traceLog("Y-handle prefix is not set", __FILE__, __CLASS__, __LINE__);
            return false;
        }
        
        return true;
    }
    
    /**
     * return start date string 'YYYY-MM-DD HH:mm:ss.sss'
     * 開始日時を作成する
     *
     * @return string Start date 開始日時
     */
    private function getStartDate()
    {
        return sprintf("%d-%02d-%02d",$this->sy_log, $this->sm_log,$this->sd_log). " 00:00:00.000";
    }
    
    /**
     * return end date string 'YYYY-MM-DD HH:mm:ss.sss'
     * 終了日時を作成する
     *
     * @return string End date 終了日時
     */
    private function getEndDate()
    {
        return sprintf("%d-%02d-%02d",$this->ey_log, $this->em_log,$this->ed_log). " 23:59:99.999";
    }
    
    /**
     * return all group information
     * 全グループ情報を取得する
     *
     * @param array $allGroup All group list グループ情報
     *                        array[$ii]["page_id"|"room_id"]
     * @return boolean Result 結果
     */
    private function getGroupList(&$allGroup)
    {
        $query = "SELECT * ". 
                 " FROM ". DATABASE_PREFIX ."pages ".
                 " WHERE space_type = ? AND ".
                 " private_flag = ? AND ".
                 " NOT thread_num = ? AND ".
                 " room_id = page_id; ";
        $params = array();
        $params[] = _SPACE_TYPE_GROUP;
        $params[] = 0;
        $params[] = 0;
        // SELECT実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // 結果を格納
        $allGroup = $result;
        return true;
    }
    
    /**
     * create keyword ranking report
     * ランキングレポート作成
     * 
     * @return array Ranking report ランキングレポート
     *               array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    public function createKeywordRankingReport()
    {
        $this->infoLog("businessRanking", __FILE__, __CLASS__, __LINE__);
        $ranking = BusinessFactory::getFactory()->getBusiness('businessRanking');
        
        // set to off on unnessesary ranking
        $ranking->toOffReferRanking();
        $ranking->toOffDownloadRanking();
        $ranking->toOffNewItemRanking();
        $ranking->toOffUserRanking();
        
        $ranking->setStartDate($this->getStartDate());
        $ranking->setEndDate($this->getEndDate());
        
        $ranking->execute();
        return $ranking->getKeywordRanking();
    }
    
    /**
     * create item detail view report
     * 詳細画面レポート作成
     * 
     * @return array Detail report 詳細画面レポート
     *               $array[$ii]["title"|"title_en"|"index_name"|"total"|"not_login"|"group"]
     */
    public function createDetailViewReport()
    {
        // -----------------------------------------------
        // get detail view log
        // -----------------------------------------------
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        $query = " SELECT ITEM.title AS title, ". 
                 "        ITEM.title_english AS title_english, ". 
                 "        ITEM.item_id AS item_id, ". 
                 "        ITEM.item_no AS item_no, ". 
                 "        LOG.user_id AS user_id ".
                 " FROM ". DATABASE_PREFIX. "repository_item AS ITEM ". 
                 " LEFT JOIN (".
                     " SELECT LOG.item_id AS item_id, ". 
                     "        LOG.item_no AS item_no, ". 
                     "        LOG.user_id AS user_id ". 
                     $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                     " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                     " AND LOG.record_date BETWEEN ? AND ? ".
                     " AND LOG.operation_id = ?".
                     " AND LOG.item_id IS NOT NULL ".
                 " ) LOG ON ITEM.item_id = LOG.item_id AND ITEM.item_no = LOG.item_no ";
        $params = array();
        $params[] = $this->getStartDate();
        $params[] = $this->getEndDate();
        $params[] = 3;
        $log = $this->Db->execute($query, $params);
        if($log === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $viewLog = array();
        for($ii=0; $ii<count($log); $ii++){
            $key =  $log[$ii]['item_id'].'_'.$log[$ii]['item_no'];
            // ファイル情報取得(get file info)
            if( !isset($viewLog[$key]) ){
                $viewLog[$key]['title'] = $log[$ii]['title'];
                $viewLog[$key]['title_en'] = $log[$ii]['title_english'];
                $viewLog[$key]['index_name'] = $this->getIndexNameByItemKey($key);
                $viewLog[$key]['total'] = 0;// トータル(total)
                $viewLog[$key]['not_login'] = 0;// 個人(not login)
                $viewLog[$key]['group'] = array();// グループ(group(room))
                $viewLog[$key]['group']['0'] = 0;// 非会員(login user(not affiliate))
            }
            // 個人ダウンロード(未ログインダウンロード)かどうか判定(check not login download)
            if(isset($log[$ii]['user_id']))
            {
                $viewLog[$key]['total']++;
                if($log[$ii]['user_id'] == "0"){
                    $viewLog[$key]['not_login']++;
                } else {
                    ///// ユーザが入っているroom_idを取得(get user group list) /////
                    $user_group = $this->getUserGroupIds($log[$ii]['user_id']);
                    // select first room_id
                    
                    if(count($user_group) > 0){
                        $group = $user_group[0]['room_id'];
                    } else {
                        // this group is non member
                        $group = "0";
                    }
                    
                    if(isset($viewLog[$key]['group'][$group])){
                        $viewLog[$key]['group'][$group]++;
                    } else {
                        $viewLog[$key]['group'][$group] = 1;
                    }
                }
            }
        }
        
        return $viewLog;
    }
    
    /**
     * create file download report
     * ダウンロードレポート作成
     * 
     * @return array Download report ダウンロードレポート
     *               array["fileViewReport"]["fileLog"|"fileOpenLog"]
     *               array["payPerViewReport"]["priceLog"|"priceOpenLog"]
     */
    public function createFileViewReport()
    {
        $priceLog = array();
        $priceOpenLog = array();
        $fileLog = array();
        $fileOpenLog = array();
        $currentNum = 0;
        
        do
        {
            // -----------------------------------------------
            // Get file download log
            // -----------------------------------------------
            $log = $this->searchFileDownloadLog($currentNum);
            $currentNum += count($log);
            
            // -----------------------------------------------
            // Get data for make log
            // -----------------------------------------------
            $result = $this->makeDownloadInfo($log, $priceLog, $priceOpenLog, $fileLog, $fileOpenLog);
            
            $this->debugLog("usage_memory = ". memory_get_usage(), __FILE__, __CLASS__, __LINE__);
            
        } while(count($log) > 0);
        
        $fileDownloadReport = array("fileViewReport" => array("fileLog" => $fileLog, "fileOpenLog" => $fileOpenLog), "payPerViewReport" => array("priceLog" => $priceLog, "priceOpenLog" => $priceOpenLog));
        return $fileDownloadReport;
    }

    /**
     * Get file download log
     * ファイルダウンロードログ取得
     *
     * @param int $currentNum Start number 開始番号
     * @return array Download log ダウンロードログ
     *               array[$ii]["LOG.record_date"|"LOG.ip_address"|"LOG.user_agent"|"LOG.item_id"|"LOG.item_no"|"LOG.attribute_id"|"LOG.file_no"|"LOG.user_id"|"LOG.file_status"|"LOG.site_license"|"LOG.input_type"|"LOG.login_status"|"LOG.group_id"]
     */
    private function searchFileDownloadLog($currentNum)
    {
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        $query = "SELECT LOG.record_date, LOG.ip_address, LOG.user_agent, ". 
                 "       LOG.item_id, LOG.item_no, LOG.attribute_id, LOG.file_no, ".
                 "       LOG.user_id, LOG.file_status, LOG.site_license, ". 
                 "       LOG.input_type, LOG.login_status, LOG.group_id ". 
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                 " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 " AND LOG.record_date >= ? ". 
                 " AND LOG.record_date <= ? ". 
                 " AND LOG.operation_id = ? ". 
                 " ORDER BY LOG.item_id ASC, ".
                 "          LOG.attribute_id ASC, ".
                 "          LOG.file_no ASC ".
                 " LIMIT ".$currentNum.", ".self::LIMIT_NUM." ;";
        $params = array();
        $params[] = $this->getStartDate();
        $params[] = $this->getEndDate();
        $params[] = 2;
        $log = $this->Db->execute($query, $params);
        if($log === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $log;
    }

    /**
     * Create file download info
     * ダウンロード情報作成
     *
     * @param array $log Download log ダウンロードログ
     *                   array[$ii]["LOG.record_date"|"LOG.ip_address"|"LOG.user_agent"|"LOG.item_id"|"LOG.item_no"|"LOG.attribute_id"|"LOG.file_no"|"LOG.user_id"|"LOG.file_status"|"LOG.site_license"|"LOG.input_type"|"LOG.login_status"|"LOG.group_id"]
     * @param array $priceLog Private price file download count 非公開課金ファイルダウンロード数
     *                        array["file_name"|"index_name"|"total"|"not_login"|"group"|"site_license"|"admin"|"register"]
     * @param array $priceOpenLog Public price file download count 公開課金ファイルダウンロード数
     *                            array["file_name"|"index_name"|"total"|"not_login"|"group"|"site_license"|"admin"|"register"]
     * @param array $fileLog Private file download count 非公開ファイルダウンロード数
     *                       array["file_name"|"index_name"|"total"|"not_login"|"group"|"site_license"|"admin"|"register"]
     * @param array $fileOpenLog Public file download count 公開ファイルダウンロード数
     *                           array["file_name"|"index_name"|"total"|"not_login"|"group"|"site_license"|"admin"|"register"]
     * @return boolean Result 結果
     */
    private function makeDownloadInfo($log, &$priceLog, &$priceOpenLog, &$fileLog, &$fileOpenLog)
    {
        for($ii=0; $ii<count($log); $ii++)
        {
            $fileStatus = "";
            $siteLicense = "";
            $inputType = "";
            $loginStatus = "";
            $groupId = "";
            $result = $this->checkFileDownloadStatus($log[$ii], $fileName, $fileStatus, $siteLicense, $inputType, $loginStatus, $groupId);
            if($result === false)
            {
                continue;
            }
            
            $key =  $log[$ii]['item_id'].'_'.$log[$ii]['item_no'].'_'.
                    $log[$ii]['attribute_id'].'_'.$log[$ii]['file_no'];
            
            if($inputType == RepositoryConst::LOG_INPUT_TYPE_FILE)
            {
                if($fileStatus == RepositoryConst::LOG_FILE_STATUS_OPEN)
                {
                    $this->setFileLogArray($key, $fileName, $fileStatus, $siteLicense, $loginStatus, $fileOpenLog);
                }
                else if($fileStatus == RepositoryConst::LOG_FILE_STATUS_CLOSE)
                {
                    $this->setFileLogArray($key, $fileName, $fileStatus, $siteLicense, $loginStatus, $fileLog);
                }
            }
            else if($inputType == RepositoryConst::LOG_INPUT_TYPE_FILE_PRICE)
            {
                if($fileStatus == RepositoryConst::LOG_FILE_STATUS_OPEN)
                {
                    $this->setFilePriceLogArray($key, $fileName, $fileStatus, $siteLicense, $loginStatus, $groupId, $priceOpenLog);
                }
                else if($fileStatus == RepositoryConst::LOG_FILE_STATUS_CLOSE)
                {
                    $this->setFilePriceLogArray($key, $fileName, $fileStatus, $siteLicense, $loginStatus, $groupId, $priceLog);
                }
            }
        }
        return true;
    }
    
    /**
     * create file download per each users report
     * ユーザごとのダウンロード数レポート
     * 
     * @return array Report レポート
     *               array[$ii]["user_id"|"login_id"|"handle"|"role_authority_name"|"DLCount"]
     */
    public function createFileViewPerUser()
    {
        // ---------------------------------------------
        // get data
        // ---------------------------------------------
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        $query = "SELECT USERS.user_id AS user_id, ".
                 "       USERS.login_id AS login_id, ". 
                 "       USERS.handle AS handle, ". 
                 "       AUTH.role_authority_name AS role_authority_name, ". 
                 "       IFNULL(LOGCOUNT.filecount, 0) AS DLCount ". 
                 " FROM ".DATABASE_PREFIX."users AS USERS ".
                 " LEFT JOIN ".DATABASE_PREFIX."authorities AS AUTH ".
                 " ON USERS.role_authority_id = AUTH.role_authority_id ".
                 " LEFT JOIN ".
                 " ( ".
                 "     SELECT USERLOG.user_id, COUNT(USERLOG.user_id) AS filecount ".
                 "     FROM ". 
                 "     (".
                 "         SELECT LOG.user_id AS user_id ".
                           $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                 "         WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 "         AND LOG.record_date BETWEEN ? AND ? ".
                 "         AND LOG.operation_id = ? ".
                 "     ) AS USERLOG ".
                 "     GROUP BY USERLOG.user_id ".
                 " ) AS LOGCOUNT ".
                 " ON USERS.user_id = LOGCOUNT.user_id; ";
        $params = array();
        $params[] = $this->getStartDate();
        $params[] = $this->getEndDate();
        $params[] = 2;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $this->createFileViewPerUserBySqlResult($result);
    }
    
    /**
     * set download report data per user to member value
     * ユーザごとのダウンロード数作成
     *
     * @param array $result Download data ダウンロード情報
     *                      array[$ii]["user_id"|"login_id"|"handle"|"role_authority_name"|"DLCount"]
     * @return array Download count per user ユーザごとのダウンロード数
     *               array[$ii]["login_id"|"handle"|"name"|"base_authority_name"|"room_authority_name"|"group_name_list"|"dl_count"]
     */
    private function createFileViewPerUserBySqlResult($result)
    {
        require_once WEBAPP_DIR. '/modules/repository/components/RepositoryUserAuthorityManager.class.php';
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        $userAuthorityManager = new RepositoryUserAuthorityManager($session, $this->Db, $this->accessDate);
        
        $fileViewPerUser = array();
        for($ii=0; $ii<count($result); $ii++){
            $user_id = $result[$ii]['user_id'];
            $login_id = $result[$ii]['login_id'];
            $handle = $result[$ii]['handle'];
            $name = "";
            // 会員氏名取得
            $query = " SELECT * ".
                     " FROM ".DATABASE_PREFIX."users_items_link ".
                     " WHERE user_id = ?".
                     " AND item_id = ?;";
            $params = array();
            $params[] = $user_id;   // user_id
            $params[] = 4;          // item_id = 4 : 会員氏名
            $ret = $this->Db->execute($query, $params);
            if($ret === false){
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
            if(count($ret)>0 && isset($ret[0]["content"]))
            {
                $name = str_replace("\t", " ", $ret[0]["content"]);
            }
            
            //Add ルーム権限取得処理 2012/12/05 A.Jin --start--
            $room_authority_name = "";
            //4以上だったら _AUTH_CHIEF_NAME
            //3 _AUTH_MODERATE_NAME
            //2 _AUTH_GENERAL_NAME
            //1 _AUTH_GUEST_NAME
            //それ以外 _AUTH_GUEST_NAME
            $auth_id = $userAuthorityManager->getRoomAuthorityID($user_id);
            if($auth_id >= 4){
                $room_authority_name = _AUTH_CHIEF_NAME;
            } else if($auth_id == 3){
                $room_authority_name = _AUTH_MODERATE_NAME;
            } else if($auth_id == 2){
                $room_authority_name = _AUTH_GENERAL_NAME;
            } else if($auth_id == 1){
                $room_authority_name = _AUTH_GUEST_NAME;
            } else {
                $room_authority_name = _AUTH_GUEST_NAME;
            }
            //Add ルーム権限取得処理 2012/12/05 A.Jin --end--
            
            $base_authority_name = "";
            if(defined($result[$ii]['role_authority_name'])){
                $base_authority_name = constant($result[$ii]['role_authority_name']);
            } else {
                $base_authority_name = $result[$ii]['role_authority_name'];
            }
            
            $group_name_list = $this->getUserGroupNameList($user_id);
            $dl_count = $result[$ii]['DLCount'];
            
            // ---------------------------------------------
            // create output a row text
            // ---------------------------------------------
            array_push($fileViewPerUser, array("login_id" => $login_id, 
                                               "handle" => $handle,
                                               "name" => $name, 
                                               "base_authority_name" => $base_authority_name, 
                                               "room_authority_name" => $room_authority_name, 
                                               "group_name_list" => $group_name_list, 
                                               "dl_count" => strval($dl_count)
                                               ));
        }
        return $fileViewPerUser;
    }
    
    /**
     * create host access report
     * ホストアクセスレポート作成
     * 
     * @return array Report レポート
     *               array[$ii]["host"|"ip_address"|"operation_id"|"cnt"]
     */
    public function createHostAccessReport()
    {
        // -----------------------------------------------
        // get access log report per host
        // count top page access
        // -----------------------------------------------
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        $query = "SELECT LOG.host AS host, LOG.ip_address AS ip_address, LOG.operation_id AS operation_id, count( ip_address ) AS cnt ". 
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                 " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 " AND LOG.record_date >= ? ". 
                 " AND LOG.record_date <= ? ".
                 " AND LOG.operation_id = ? ".
                 " GROUP BY LOG.ip_address; ";
        $params = array();
        $params[] = $this->getStartDate();
        $params[] = $this->getEndDate();
        $params[] = 5;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
     * create index access report
     * インデックスごとのアクセス数作成
     * 
     * @return array Report レポート
     *               array["totalAccess"|"detailViewPerIndex"]
     */
    public function createIndexAccessReport()
    {
        // -----------------------------------------------
        // get detail access num on each index
        // -----------------------------------------------
        $detailViewPerIndex = $this->getDetailViewPerIndexTree();
        
        $totalAccess = 0;
        foreach($detailViewPerIndex as $parentArray)
        {
            foreach($parentArray as $indexData)
            {
                $totalAccess += $indexData['detail_view'];
            }
        }
        
        return array("totalAccess" => $totalAccess, 
                     "detailViewPerIndex" => $detailViewPerIndex);
    }
    
    /**
     * get detail view per each indexes
     * インデックスごとの詳細画面アクセス数作成
     *
     * @return array Access count アクセス数
     *               array[$ii][$jj]["id"|"pid"|"name"|"detail_view"]
     */
    private function getDetailViewPerIndexTree(){
        // log report have closed indexs.
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        $query = " SELECT idx.index_id, ". 
                 "        idx.index_name, ". 
                 "        idx.index_name_english, ". 
                 "        idx.parent_index_id, ". 
                 "        cnt.detail_view ".
                 " FROM ".DATABASE_PREFIX."repository_index AS idx ".
                 " LEFT JOIN ( ".
                 "   SELECT POS.index_id, count(LOG.log_no) AS detail_view ".
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                 "        LEFT JOIN ". DATABASE_PREFIX."repository_position_index POS ". 
                 "          ON POS.is_delete = ? ".
                 "          AND POS.item_id = LOG.item_id ".
                 "   WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 "   AND LOG.record_date BETWEEN ? AND ? ".
                 "   AND LOG.operation_id = ? ".
                 "   AND LOG.item_id IS NOT NULL ".
                 "   GROUP BY POS.index_id ".
                 " ) AS cnt ".
                 " ON cnt.index_id = idx.index_id ".
                 " WHERE idx.is_delete = ? ".
                 " ORDER BY show_order, index_id ";
        $params = array();
        $params[] = 0;
        $params[] = $this->getStartDate();
        $params[] = $this->getEndDate();
        $params[] = 3;
        $params[] = 0;
        
        $this->debugLog($query, __FILE__, __CLASS__, __LINE__);
        $this->debugLog(print_r($params, true), __FILE__, __CLASS__, __LINE__);
        
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $index = array();
        for($ii=0; $ii<count($result); $ii++){
            $node = array(
                        'id'=>$result[$ii]['index_id'],
                        'name'=>"",
                        'pid'=>$result[$ii]['parent_index_id'],
                        'detail_view'=>intval($result[$ii]['detail_view'])
                    );
            if($this->isDisplayLanguageJapanese()){
                $node['name'] = $result[$ii]['index_name'];
            } else {
                $node['name'] = $result[$ii]['index_name_english'];
            }
            if($node['detail_view'] == null || strlen($node['detail_view']) == 0 || !is_numeric($node['detail_view'])){
                $node['detail_view'] = 0;
            }
            
            if(!isset($index[$node['pid']])){
                $index[$node['pid']] = array();
            }
            array_push($index[$node['pid']], $node);
        }
        return $index;
    }
    
    /**
     * display language is japanese or not
     * 表示言語が日本語であるか否か
     *
     * @return boolean Is japanese 日本語であるか否か
     */
    private function isDisplayLanguageJapanese()
    {
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        
        $language = $session->getParameter("_lang");
        if($language === "japanese")
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * select sitelicense access record by database
     * サイトライセンスアクセスのログを検索する
     *
     * @param int $operationId Operation id 操作ID
     * @return array Result 結果
     *               array[$ii]["user_id"|"site_license_id"|"site_license"|"ip_address"]
     */
    private function selectSiteLicense($operationId)
    {
        if($operationId === RepositoryConst::LOG_OPERATION_SEARCH){
            $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_RANKING);
        } else {
            $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        }
        
        $query = "SELECT LOG.user_id AS user_id, ". 
                 " LOG.site_license_id AS site_license_id, ". 
                 " LOG.site_license AS site_license, ".
                 " LOG.ip_address AS ip_address ". 
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM]. 
                 " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE]. 
                 " AND LOG.record_date >= ? ". 
                 " AND LOG.record_date <= ? ".
                 " AND LOG.item_id IS NOT NULL ".
                 " AND LOG.operation_id=? "; 
        $params = array();
        $params[] = $this->getStartDate();
        $params[] = $this->getEndDate();
        $params[] = $operationId;
        
        if($operationId === RepositoryConst::LOG_OPERATION_SEARCH){
            // 検索のアクセス回数を確認する場合、検索キーワードが空文字でないかの判定を実施する必要がある
            $query .= " AND NOT(LOG.search_keyword=?) ";
            $params[] = "";
        }
        $query .= ";";
        
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $this->debugLog($query, __FILE__, __CLASS__, __LINE__);
        $this->debugLog(print_r($params, true), __FILE__, __CLASS__, __LINE__);
        
        return $result;
    }
    
    /**
     * calculate site license report(top or search)
     * サイトライセンスレポートを作成する
     *
     * @param array $result Sitelicense search result サイトライセンスアクセス検索結果
     *                      array[$ii]["user_id"|"site_license_id"|"site_license"|"ip_address"]
     * @param string $is_sitelicense key name(sitelicense) キー名(サイトライセンス)
     * @param string $not_sitelicense key name(not sitelicense) キー名(サイトライセンスではない)
     * @param string $operation Operation 操作
     * @param array $log_data Site license report サイトライセンスレポート
     * @return array Site license report サイトライセンスレポート
     *               array[organization name|"is_sitelicense"|"is_not_sitelicense"]["top"|"search"|"detail"|"download"]
     */
    private function calcSiteLicenseReportNoSupportItemType($result, $is_sitelicense, $not_sitelicense, $operation, $log_data)
    {
        // check site license
        for($ii=0; $ii<count($result); $ii++){
            if(isset($result[$ii]['site_license']))
            {
                // Exist site license info in log recode
                if($result[$ii]['site_license'] == 1)
                {
                    $organization = "";
                    if($result[$ii]['site_license_id'] > 0){
                        $organization = $this->getSiteLicenseOrganizationById($result[$ii]['site_license_id']);
                    } else {
                        $this->checkSiteLicenseForLogReport($result[$ii]['ip_address'], $result[$ii]['user_id'], $organization);
                    }
                    if(strlen($organization) == 0){
                        // Not exist organization
                        $organization = $result[$ii]['ip_address'];
                    }
                    
                    if(!isset($log_data[$organization])){
                        $log_data[$organization]['top'] = 0;
                        $log_data[$organization]['search'] = 0;
                        $log_data[$organization]['detail'] = 0;
                        $log_data[$organization]['download'] = 0;
                    }
                    $log_data[$organization][$operation]++;
                    $log_data[$is_sitelicense][$operation]++;
                }
                else
                {
                    // Site license OFF
                    $log_data[$not_sitelicense][$operation]++;
                }
            }
            else
            {
                // Not exist site license info in log recode
                $organization = "";
                if($this->checkSiteLicenseForLogReport($result[$ii]['ip_address'], $result[$ii]['user_id'], $organization)){
                    $log_data[$organization][$operation]++;
                    $log_data[$is_sitelicense][$operation]++;
                } else {
                    $log_data[$not_sitelicense][$operation]++;
                }
            }
        }
        
        return $log_data;
    }
    
    /**
     * calculate top page access by site lisence user and no site license user
     * サイトライセンスアクセスレポート作成(トップ)
     *
     * @param string $is_sitelicense key name(sitelicense) キー名(サイトライセンス)
     * @param string $not_sitelicense key name(not sitelicense) キー名(サイトライセンスではない)
     * @param array $log_data Site license report サイトライセンスレポート
     *               array[organization name|"is_sitelicense"|"is_not_sitelicense"]["top"|"search"|"detail"|"download"]
     */
    private function calcTopAccessReport($is_sitelicense, $not_sitelicense, &$log_data)
    {
        $this->traceLog(__FUNCTION__, __FILE__, __CLASS__, __LINE__);
        // -----------------------------------------------
        // get WEKO Top page access
        // -----------------------------------------------
        $result = $this->selectSiteLicense(RepositoryConst::LOG_OPERATION_TOP);
        
        $this->traceLog(count($result), __FILE__, __CLASS__, __LINE__);
        
        $log_data = $this->calcSiteLicenseReportNoSupportItemType($result, $is_sitelicense, $not_sitelicense, "top", $log_data);
        
        $this->traceLog(print_r($log_data, true), __FILE__, __CLASS__, __LINE__);
    }
    
    /**
     * get site license organization name by site_license_id
     * サイトライセンス認可機関名取得
     *
     * @param int $site_license_id Site license id サイトライセンスID
     * @return string organization name サイトライセンス認可機関名
     */
    private function getSiteLicenseOrganizationById($site_license_id)
    {
        $this->traceLog(__FUNCTION__, __FILE__, __CLASS__, __LINE__);
        $query = "SELECT organization_name ". 
                 " FROM ". DATABASE_PREFIX. "repository_sitelicense_info ". 
                 " WHERE organization_id = ? ". 
                 " AND is_delete = ?; ";
        $params = array();
        $params[] = intval($site_license_id);
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        } else if(count($result) === 0){
            return "";
        }
        
        return $result[0]['organization_name'];
    }
    
    /**
     * calculate keyword search access by site lisence user and no site license user
     * サイトライセンスレポート作成(検索)
     *
     * @param string $is_sitelicense key name(sitelicense) キー名(サイトライセンス)
     * @param string $not_sitelicense key name(not sitelicense) キー名(サイトライセンスではない)
     * @param array $log_data Site license report サイトライセンスレポート
     *               array[organization name|"is_sitelicense"|"is_not_sitelicense"]["top"|"search"|"detail"|"download"]
     */
    private function calcSearchAccessReport($is_sitelicense, $not_sitelicense, &$log_data)
    {
        // -----------------------------------------------
        // get search result page access
        // -----------------------------------------------
        $result = $this->selectSiteLicense(RepositoryConst::LOG_OPERATION_SEARCH);
        
        $log_data = $this->calcSiteLicenseReportNoSupportItemType($result, $is_sitelicense, $not_sitelicense, "search", $log_data);
    }
    
    /**
     * select sitelicense(detail or download) access record by database
     * サイトライセンス認可機関のアクセスをログから取得
     *
     * @param int $operationId Operation 操作
     * @return array Site license report サイトライセンスレポート
     *               array[$ii]["user_id"|"ip_address"|"item_type_id"|"site_license_id"|"site_license"]
     */
    private function selectSiteLicenseLogSupportItemtype($operationId)
    {
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        $query = "SELECT LOG.user_id AS user_id, ". 
                 " LOG.ip_address AS ip_address, ". 
                 " ITEM.item_type_id AS item_type_id, ".
                 " LOG.site_license_id AS site_license_id, ". 
                 " LOG.site_license AS site_license ".
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                 " LEFT JOIN ".DATABASE_PREFIX."repository_item AS ITEM ".
                 " ON LOG.item_id = ITEM.item_id AND LOG.item_no = ITEM.item_no ".
                 " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 " AND LOG.record_date >= ? ".
                 " AND LOG.record_date <= ? ".
                 " AND LOG.item_id IS NOT NULL ".
                 " AND LOG.operation_id=?; ";
        // Add exclude item_type_id for site license 2013/07/01 A.Suzuki --end--
        $params = array();
        $params[] = $this->getStartDate();
        $params[] = $this->getEndDate();
        $params[] = $operationId;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
     * calculate sitelicense access report(download or detail)
     * サイトライセンスレポートを作成(詳細、ダウンロード)
     *
     * @param array $result Sitelicense search result サイトライセンスアクセス検索結果
     *                      array[$ii]["user_id"|"site_license_id"|"site_license"|"ip_address"]
     * @param string $is_sitelicense key name(sitelicense) キー名(サイトライセンス)
     * @param string $not_sitelicense key name(not sitelicense) キー名(サイトライセンスではない)
     * @param array $siteLicenseItemTypeId Site license item type id サイトライセンスアイテムタイプID
     * @param string $operation Operation 操作
     * @param array $log_data Site license report サイトライセンスレポート
     * @return array Site license report サイトライセンスレポート
     *               array[organization name|"is_sitelicense"|"is_not_sitelicense"]["top"|"search"|"detail"|"download"]
     */
    private function calcSiteLicenseReportSupportItemType($result, $is_sitelicense, $not_sitelicense, $siteLicenseItemTypeId, $operation, $log_data)
    {
        // check site license
        for($ii=0; $ii<count($result); $ii++){
            if(isset($result[$ii]['site_license']))
            {
                // Exist site license info in log recode
                if($result[$ii]['site_license'] == 1)
                {
                    $organization = "";
                    if($result[$ii]['site_license_id'] > 0){
                        $organization = $this->getSiteLicenseOrganizationById($result[$ii]['site_license_id']);
                    } else {
                        $this->checkSiteLicenseForLogReport($result[$ii]['ip_address'], $result[$ii]['user_id'], $organization);
                    }
                    if(strlen($organization) == 0){
                        // Not exist organization
                        $organization = $result[$ii]['ip_address'];
                    }
                    
                    if(!isset($log_data[$organization]))
                    {
                        $log_data[$organization]['top'] = 0;
                        $log_data[$organization]['search'] = 0;
                        $log_data[$organization]['detail'] = 0;
                        $log_data[$organization]['download'] = 0;
                    }
                    $log_data[$organization][$operation]++;
                    $log_data[$is_sitelicense][$operation]++;
                }
                else
                {
                    // Site license OFF
                    $log_data[$not_sitelicense][$operation]++;
                }
            }
            else
            {
                // Not exist site license info in log recode
                $log_data = $this->calcSiteLicenseReportSupportItemTypeNotExistSitelicenseInfo($result[$ii]['ip_address'], $result[$ii]['user_id'], $result[$ii]['item_type_id'], $siteLicenseItemTypeId, $log_data, $operation, $is_sitelicense, $not_sitelicense);
            }
        }
        
        return $log_data;
    }
    
    /**
     * calculation sitelicense report for the records not exists sitelicense info
     * サイトライセンスレポート作成
     *
     * @param string $ip_address Ip address IPアドレス
     * @param string $user_id User id ユーザID
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $siteLicenseItemTypeId Site license item type id サイトライセンスアイテムタイプID
     * @param array $log_data Site license report サイトライセンスレポート
     * @param string $operation Operation 操作
     * @param string $is_sitelicense key name(sitelicense) キー名(サイトライセンス)
     * @param string $not_sitelicense key name(not sitelicense) キー名(サイトライセンスではない)
     * @return array Site license report サイトライセンスレポート
     *               array[organization name|"is_sitelicense"|"is_not_sitelicense"]["top"|"search"|"detail"|"download"]
     */
    private function calcSiteLicenseReportSupportItemTypeNotExistSitelicenseInfo($ip_address, $user_id, $item_type_id, $siteLicenseItemTypeId, $log_data, $operation, $is_sitelicense, $not_sitelicense)
    {
        // Not exist site license info in log recode
        $this->debugLog(__FUNCTION__, __FILE__, __CLASS__, __LINE__);
        $organization = "";
        if($this->checkSiteLicenseForLogReport($ip_address, $user_id, $organization))
        {
            // Add exclude item_type_id for site license 2013/07/01 A.Suzuki --start--
            $matchFlag = false;
            for($jj=0; $jj<count($siteLicenseItemTypeId); $jj++)
            {
                if($siteLicenseItemTypeId[$jj] == $item_type_id)
                {
                    $matchFlag = true;
                    break;
                }
            }
            if($matchFlag)
            {
                $log_data[$not_sitelicense][$operation]++;
            }
            else
            {
                $log_data[$organization][$operation]++;
                $log_data[$is_sitelicense][$operation]++;
            }
            // Add exclude item_type_id for site license 2013/07/01 A.Suzuki --end--
        } else {
            $log_data[$not_sitelicense][$operation]++;
        }
        
        return $log_data;
    }
    
    
    /**
     * calculate item detail view access by site lisence user and no site license user
     * 詳細、ダウンロードのサイトライセンスアクセスレポートを作成
     *
     * @param string $is_sitelicense key name(sitelicense) キー名(サイトライセンス)
     * @param string $not_sitelicense key name(not sitelicense) キー名(サイトライセンスではない)
     * @param int $siteLicenseItemTypeId Item type id アイテムタイプID
     * @param array $log_data Site license report サイトライセンスレポート
     *                        array[organization name|"is_sitelicense"|"is_not_sitelicense"]["top"|"search"|"detail"|"download"]
     */
    private function calcDetailViewReport($is_sitelicense, $not_sitelicense, $siteLicenseItemTypeId, &$log_data)
    {
        // -----------------------------------------------
        // get detail display page access
        // -----------------------------------------------
        $result = $this->selectSiteLicenseLogSupportItemtype(RepositoryConst::LOG_OPERATION_DETAIL_VIEW);
        
        $log_data = $this->calcSiteLicenseReportSupportItemType($result, $is_sitelicense, $not_sitelicense, $siteLicenseItemTypeId, "detail", $log_data);
    }
    
    /**
     * calculate file download access by site lisence user and no site license user
     * ダウンロードのサイトライセンスレポートを作成する
     *
     * @param string $is_sitelicense key name(sitelicense) キー名(サイトライセンス)
     * @param string $not_sitelicense key name(not sitelicense) キー名(サイトライセンスではない)
     * @param int $siteLicenseItemTypeId Item type id アイテムタイプID
     * @param array $log_data Site license report サイトライセンスレポート
     *                        array[organization name|"is_sitelicense"|"is_not_sitelicense"]["top"|"search"|"detail"|"download"]
     */
    private function calcDownloadReport($is_sitelicense, $not_sitelicense, $siteLicenseItemTypeId, &$log_data)
    {
        // -----------------------------------------------
        // get download access
        // -----------------------------------------------
        $result = $this->selectSiteLicenseLogSupportItemtype(RepositoryConst::LOG_OPERATION_DOWNLOAD_FILE);
        
        $log_data = $this->calcSiteLicenseReportSupportItemType($result, $is_sitelicense, $not_sitelicense, $siteLicenseItemTypeId, "download", $log_data);
    }
    
    /**
     * create site access report
     * サイトアクセスレポート作成
     * 
     * @return array Site license report サイトライセンスレポート
     *               array[organization name|"is_sitelicense"|"is_not_sitelicense"]["top"|"search"|"detail"|"download"]
     */
    public function createSiteAccessReport()
    {
        $log_data = array();
        // -----------------------------------------------
        // site license or not init
        // -----------------------------------------------
        // site license total
        $is_sitelicense = self::IS_SITELICENSE;
        $log_data[$is_sitelicense] = array('top' => 0, 
                                           'search' => 0, 
                                           'detail' => 0, 
                                           'download' => 0);
        // not site license total
        $not_sitelicense = self::IS_NOT_SITELICENSE;
        $log_data[$not_sitelicense] = array('top' => 0, 
                                            'search' => 0, 
                                            'detail' => 0, 
                                            'download' => 0);
        // -----------------------------------------------
        // get site license organization and init
        // -----------------------------------------------
        $query = "SELECT organization_name ". 
                 " FROM ". DATABASE_PREFIX ."repository_sitelicense_info ".
                 " WHERE is_delete = ? ;";
        $params = array();
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        for($ii = 0; $ii < count($result); $ii++) {
            $organization = $result[$ii]["organization_name"];
            $log_data[$organization] = array('top' => 0, 
                                             'search' => 0, 
                                             'detail' => 0, 
                                             'download' => 0);
        }
        
        // Add exclude item_type_id for site license 2013/07/01 A.Suzuki --start--
        // Get param table data : site_license_item_type_id
        $query = "SELECT param_value ". 
                 " FROM ". DATABASE_PREFIX ."repository_parameter ".
                 " WHERE param_name = ?; ";
        $params = array();
        $params[] = 'site_license_item_type_id';
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        $siteLicenseItemTypeId = explode(",", trim($result[0]['param_value']));
        
        // increment top access num
        // (speed up log report, decrease for loop by group-by to user_id and site_license_flg)
        $this->calcTopAccessReport($is_sitelicense, $not_sitelicense, $log_data);
        
        $this->calcSearchAccessReport($is_sitelicense, $not_sitelicense, $log_data);
        
        $this->calcDetailViewReport($is_sitelicense, $not_sitelicense, $siteLicenseItemTypeId, $log_data);
        
        $this->calcDownloadReport($is_sitelicense, $not_sitelicense, $siteLicenseItemTypeId, $log_data);
        
        return $log_data;
    }
    
    /**
     * create user's affiliation report
     * ユーザ所属レポート作成
     * 
     * @return array user's affiliation report ユーザ所属レポート
     *               array["all_group"|"user_auth"]
     */
    public function createUserAffiliateReport()
    {
        // -----------------------------------------------
        // init
        // -----------------------------------------------
        $str = "";
        
        // -----------------------------------------------
        // get user per BASE_AUTHOHRITY
        // -----------------------------------------------
        $query = "SELECT auth.role_authority_name, count( users.user_id ) cnt ".
                " FROM ".DATABASE_PREFIX."authorities AS auth ".
                " LEFT JOIN ".DATABASE_PREFIX."users AS users ON users.role_authority_id = auth.role_authority_id ".
                " GROUP BY auth.role_authority_id; ";
        $userAuth = $this->Db->execute($query);
        if($userAuth === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $error_msg = "";
        $result = $this->getGroupList($all_group, $error_msg);
        if($result === false){
            $this->errorLog($error_msg, __FILE__, __CLASS__, __LINE__);
            throw new AppException($error_msg);
        }
        // -----------------------------------------------
        // get user per room
        // -----------------------------------------------
        $query = "SELECT links.room_id, count(users.user_id) AS cnt ".
                " FROM ".DATABASE_PREFIX."users AS users, ".DATABASE_PREFIX."pages_users_link AS links ".
                " WHERE users.user_id=links.user_id ".
                " GROUP BY room_id ";
        $userRoom = $this->Db->execute($query);
        if($userRoom === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        for($ii=0; $ii<count($all_group); $ii++){
            $all_group[$ii]['cnt'] = 0;
            for($jj=0; $jj<count($userRoom); $jj++){
                if($all_group[$ii]['room_id'] == $userRoom[$jj]['room_id']){
                    $all_group[$ii]['cnt'] = $userRoom[$jj]['cnt'];
                }
            }
            if($jj == count($userRoom)){
                unset($userRoom[$jj]);
                $userRoom = array_values($userRoom);
            }
        }
        $userAffiliateReport = array("all_group" => array(), 
                                     "userAuth" => array());
        $userAffiliateReport["all_group"] = $all_group;
        $userAffiliateReport["userAuth"] = $userAuth;
        
        return $userAffiliateReport;
    }
    
    /**
     * create supplement contents report 
     * サプリレポートを作成する
     * 
     * @return array Supple report サプリレポート
     *               array[]
     */
    public function createSuppleReport()
    {
        // サプリテーブルの情報を取得
        $query = "SELECT * ". 
                 " FROM ".DATABASE_PREFIX."repository_supple ".
                 " WHERE is_delete = ?;";
        $params = array();
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // サプリコンテンツのサプリWEKOアイテムIDを連結する
        $item_ids = "";
        foreach($result as $val){
            if($item_ids != ""){
                $item_ids .= ",";
            }
            $item_ids .= $val['supple_weko_item_id'];
        }
        
        // request URL send for supple weko
        // パラメタテーブルからサプリWEKOのアドレスを取得する
        $query = "SELECT param_value ". 
                 " FROM ".DATABASE_PREFIX."repository_parameter ".
                 " WHERE param_name = ?;";
        $params = array();
        $params[] = 'supple_weko_url';
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        if($result[0]['param_value'] == ""){
            $this->traceLog("supplemental contents weko url is empty", __FILE__, __CLASS__, __LINE__);
            return;
        } else {
            $sendParam = $result[0]['param_value'];
        }
        
        $sendParam .= "/?action=repository_opensearch&item_ids=".$item_ids."&log_term=".sprintf("%d-%02d",$this->sy_log, $this->sm_log)."&format=rss";
        
        // send http request and get response
        $responseArray = $this->sendHttpRequest($sendParam);
        
        // get response
        $response_xml = $responseArray["body"];
        
        $suppleData = array();
        if($this->analyzeXml($response_xml, $suppleData) === false){
            $msg = "XML is invalid";
            $this->errorLog($msg, __FILE__, __CLASS__, __LINE__);
            throw new AppException($msg);
        }
        
        return $suppleData;
    }
    
    /**
     * analyze responce xml
     * サプリWEKOからのXMLを解析する
     *
     * @param string $responseXml XML string XML文字列
     * @param array $supple_data Supple data サプリデータ
     *                           array["log_view"|"log_download"]
     * @return boolean Result 結果
     */
    private function analyzeXml($responseXml, &$supple_data)
    {
        /////////////////////////////
        // parse response XML
        /////////////////////////////
        try{
            $xml_parser = xml_parser_create();
            $rtn = xml_parse_into_struct( $xml_parser, $responseXml, $vals );
            if($rtn == 0){
                $this->traceLog("parse is failed", __FILE__, __CLASS__, __LINE__);
                return false;
            }
            xml_parser_free($xml_parser);
        } catch(Exception $ex){
            $this->traceLog("exception is occured", __FILE__, __CLASS__, __LINE__);
            return false;
        }
        
        /////////////////////////////
        // analize XML data
        /////////////////////////////
        $item_flag = false;
        $supple_data = array();
        foreach($vals as $val){
            if($val['tag'] == "ITEM"){
                if($val['type'] == "open"){
                    $item_flag = true;
                    $item_data = array();
                }
                if($item_flag == true && $val['type'] == "close"){
                    $item_flag = false;
                    if($item_data["supple_weko_item_id"] != "" && $item_data["supple_weko_item_id"] != null){
                        array_push($supple_data, $item_data);
                    }
                }
            }
            if($item_flag){
                switch($val['tag']){
                    case "DC:IDENTIFIER":   // サプリアイテム:アイテムID(Yhandle suffix)
                        if(preg_match("/^[0-9]+/", $val['value']) === 1){
                            $item_data["supple_weko_item_id"] = $val['value'];
                        }
                        break;
                    case "WEKOLOG:VIEW":    // サプリアイテム:閲覧回数
                        $item_data["log_view"] = $val['value'];
                        break;
                    case "WEKOLOG:DOWNLOAD":    // サプリアイテム:ダウンロード回数
                        $item_data["log_download"] = $val['value'];
                        break;
                    default :
                        break;
                }
            }
        }
        
        $this->traceLog(print_r($supple_data, true), __FILE__, __CLASS__, __LINE__);
        
        return true;
    }
    
    /**
     * is site license user or not by access ipaddress, user_id and organization name
     * サイトライセンス認可機関からのアクセスであるかをチェックする
     *
     * @param string $access_ip check ip address IPアドレス
     * @param string $user_id user id ユーザID
     * @param string $organization 機関名
     *                  when $access_ip is site license, 
     *                  set $organization is site license organization.
     * @return boolean Result 結果
     */
    private function checkSiteLicenseForLogReport($access_ip, $user_id, &$organization){
        // IPアドレスを0埋めの12桁の文字列にする
        $ipaddress = explode(".", $access_ip);
        $ip = sprintf("%03d", $ipaddress[0]).
              sprintf("%03d", $ipaddress[1]).
              sprintf("%03d", $ipaddress[2]).
              sprintf("%03d", $ipaddress[3]);
        // サイトライセンスに設定されたIPレンジの取得
        $query = "SELECT organization_id, start_ip_address, finish_ip_address ". 
                 " FROM ". DATABASE_PREFIX. "repository_sitelicense_ip_address ".
                 " WHERE is_delete = ? ;";
        $params = array();
        $params[] = 0;
        $sitelicense_ip = $this->Db->execute($query, $params);
        if($sitelicense_ip === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // チェック処理
        for($ii=0; $ii<count($sitelicense_ip); $ii++){
            if(isset($sitelicense_ip[$ii]["start_ip_address"]) && strlen($sitelicense_ip[$ii]["start_ip_address"]) > 0){
                // IPレンジの始点を0埋め12ケタの文字列にする
                $start_ip = explode(".", $sitelicense_ip[$ii]["start_ip_address"]);
                $from = sprintf("%03d", $start_ip[0]).
                        sprintf("%03d", $start_ip[1]).
                        sprintf("%03d", $start_ip[2]).
                        sprintf("%03d", $start_ip[3]);
                if(isset($sitelicense_ip[$ii]["finish_ip_address"]) && strlen($sitelicense_ip[$ii]["finish_ip_address"]) > 0){
                    // IPレンジの終点を0埋め12ケタの文字列にする
                    $finish_ip = explode(".", $sitelicense_ip[$ii]["finish_ip_address"]);
                    $to = sprintf("%03d", $finish_ip[0]).
                          sprintf("%03d", $finish_ip[1]).
                          sprintf("%03d", $finish_ip[2]).
                          sprintf("%03d", $finish_ip[3]);
                    // ユーザーのIPアドレスが範囲内に収まっていればtrueを返す
                    if($from <= $ip && $ip <= $to){
                        $query = "SELECT organization_name FROM ". DATABASE_PREFIX. "repository_sitelicense_info ".
                                 "WHERE organization_id = ? ".
                                 "AND is_delete = ? ;";
                        $params = array();
                        $params[] = $sitelicense_ip[$ii]["organization_id"];;
                        $params[] = 0;
                        $result = $this->Db->execute($query, $params);
                        if($result === false)
                        {
                            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                            throw new AppException($this->Db->ErrorMsg());
                        }
                        else if($result) {
                            $organization = $result[0]["organization_name"];
                        }
                        return true;
                    }
                } else if($ip == $from) {
                    // IPが始点(=from)のみ設定されている場合はそれと一致するかどうかを判定する
                    $query = "SELECT organization_name FROM ". DATABASE_PREFIX. "repository_sitelicense_info ".
                             "WHERE organization_id = ? ".
                             "AND is_delete = ? ;";
                    $params = array();
                    $params[] = $sitelicense_ip[$ii]["organization_id"];;
                    $params[] = 0;
                    $result = $this->Db->execute($query, $params);
                    if($result === false)
                    {
                        $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                        throw new AppException($this->Db->ErrorMsg());
                    }
                    else if($result) {
                        $organization = $result[0]["organization_name"];
                    }
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * when price file download, the user is used group?
     * ユーザがどのグループでダウンロードしたかを取得する
     *
     * @param int $price Price 課金額
     * @param string $user_id User id ユーザID
     * @return int room_id of used group ルームID
     */
    private function getDownloadType($price, $user_id){
        $room_id = '0';
        $room_name = '';
        ///// get groupID and price /////
        $room_price = explode("|",$price);
        ///// ユーザが入っているroom_idを取得 /////
        $query = "SELECT room_id FROM ". DATABASE_PREFIX ."pages_users_link ".
                 "WHERE user_id = ?; ";
        $params = array();
        $params[] = $user_id;
        // SELECT実行
        $user_group = $this->Db->execute($query, $params);
        if($user_group === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // Add Nonmember
        // search file price for setting download type
        for($price_Cnt=0;$price_Cnt<count($room_price);$price_Cnt++){
            $price = explode(",", $room_price[$price_Cnt]);
            // There is a pair of room_id and the price. 
            if($price!=null && count($price)==2) {
                // It is judged whether it is user's belonging group.
                for($user_group_cnt=0;$user_group_cnt<count($user_group);$user_group_cnt++){
                    if($price[0] == $user_group[$user_group_cnt]["room_id"]){
                        // When the price is set to the belonging group
                        if($file_price==""){
                            // The price is maintained at the unsetting. 
                            $file_price = $price[1];
                            $room_id = $user_group[$user_group_cnt]["room_id"];
                        } else if(intval($file_price) > intval($price[1])){
                            // It downloads it by the lowest price. 
                            $file_price = $price[1];
                            $room_id = $user_group[$user_group_cnt]["room_id"];
                        }
                    }
                }
            }
        }
        return $room_id;
    }
    
    /**
     * get group list on the user
     * ユーザが所属するグループを取得
     * 
     * @param string user_id
     * @return array[$ii]["links.room_id"] Room id on user ユーザが所属するグループのルームID
     *
     */
    private function getUserGroupIds($user_id){
        $query = "SELECT DISTINCT links.room_id ".
                " FROM ".DATABASE_PREFIX."pages_users_link AS links, ".
                       DATABASE_PREFIX."pages AS pages ".
                " WHERE links.user_id = ? ".
                " AND pages.private_flag = ? ".
                " AND pages.space_type = ? ".
                " AND NOT pages.thread_num = ? ".
                " AND pages.room_id = pages.page_id ".
                " AND links.room_id = pages.room_id ". 
                " AND links.role_authority_id != ?; ";
        $params = null;
        $params[] = $user_id;
        $params[] = 0;
        $params[] = _SPACE_TYPE_GROUP;
        $params[] = 0;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        return $result;
    }
    
    /**
     * Check file download status and file input type
     * ファイルダウンロード状態をチェックする
     *
     * @param array $logRecord 'repository_log' table record repository_logテーブルレコード
     * @param string $fileName file name ファイル名
     * @param int $fileStatus File status ファイル状態
     *                        0:unknown / 1: public / -1: private
     * @param int $siteLicense Site license status サイトライセンス状態
     * @param int $inputType Input type 入力タイプ
     *                       0: file / 1: file_price
     * @param int $loginStatus Login status ログイン状態
     * @param int $groupId Group id グループID
     * @return boolean Result 結果
     */
    private function checkFileDownloadStatus($logRecord, &$fileName, &$fileStatus, &$siteLicense, &$inputType, &$loginStatus, &$groupId)
    {
        // Init
        $fileName = "";
        $fileStatus = RepositoryConst::LOG_FILE_STATUS_UNKNOWN;
        $siteLicense = RepositoryConst::LOG_SITE_LICENSE_OFF;
        $inputType = null;
        $loginStatus = null;
        $groupId = null;
        
        // Check params
        if(!is_array($logRecord))
        {
            return false;
        }
        
        $itemId = $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_ITEM_ID];
        $itemNo = $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_ITEM_NO];
        $attributeId = $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_ATTRIBUTE_ID];
        $fileNo = $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_FILE_NO];
        
        if(!isset($logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_FILE_STATUS])
            || $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_FILE_STATUS] == RepositoryConst::LOG_FILE_STATUS_UNKNOWN)
        {
            // File status is not set or 'unknown'
            // Check input type is "file" or "file_price"
            $key =  $itemId.'_'.$itemNo.'_'.$attributeId.'_'.$fileNo;
            $query = "SELECT ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IMPUT_TYPE." ".
                     "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_ITEM." as item, ".
                             DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_ITEM_ATTR_TYPE." as attr_type ".
                     "WHERE item.".RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_TYPE_ID." = attr_type.".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ITEM_TYPE_ID." ".
                     "AND item.".RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_ID." = ? ".
                     "AND item.".RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_NO." = ? ".
                     "AND attr_type.".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_ID." = ?; ";
            $params = array();
            $params[] = $itemId;
            $params[] = $itemNo;
            $params[] = $attributeId;
            $result = $this->Db->execute($query, $params);
            if($result === false)
            {
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
            
            // Set input type
            if($result[0][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IMPUT_TYPE] == RepositoryConst::ITEM_ATTR_TYPE_FILE)
            {
                $inputType = RepositoryConst::LOG_INPUT_TYPE_FILE;
            }
            else if($result[0][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IMPUT_TYPE] == RepositoryConst::ITEM_ATTR_TYPE_FILEPRICE)
            {
                $inputType = RepositoryConst::LOG_INPUT_TYPE_FILE_PRICE;
            }
            else
            {
                // Illegal input type
                return false;
            }
            
            // Check site license
            $organization = "";
            if($this->checkSiteLicenseForLogReport($logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_IP_ADDRESS], $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_USER_ID], $organization))
            {
                // Add exclude item_type_id for site license 2013/07/01 A.Suzuki --start--
                // Get item_type_id
                $query = "SELECT item_type_id ".
                         "FROM ".DATABASE_PREFIX."repository_item ".
                         "WHERE item_id = ? ".
                         "AND item_no = ? ".
                         "AND is_delete = ?;";
                $params = array();
                $params[] = $itemId;
                $params[] = $itemNo;
                $params[] = 0;
                $result = $this->Db->execute($query, $params);
                if($result === false)
                {
                    $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                    throw new AppException($this->Db->ErrorMsg());
                }
                $itemTypeId = $result[0]['item_type_id'];
                
                // Get param table data : site_license_item_type_id
                $query = "SELECT param_value ". 
                         " FROM ". DATABASE_PREFIX ."repository_parameter ".
                         " WHERE param_name = ?; ";
                $params = array();
                $params[] = 'site_license_item_type_id';
                $result = $this->Db->execute($query, $params);
                if($result === false)
                {
                    $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                    throw new AppException($this->Db->ErrorMsg());
                }
                $siteLicenseItemTypeIdArray = explode(",", trim($result[0]['param_value']));
                
                $matchFlag = false;
                for($ii=0; $ii<count($siteLicenseItemTypeIdArray); $ii++)
                {
                    if($siteLicenseItemTypeIdArray[$ii] == $itemTypeId)
                    {
                        $matchFlag = true;
                        break;
                    }
                }
                if(!$matchFlag)
                {
                    $siteLicense = RepositoryConst::LOG_SITE_LICENSE_ON;
                }
                // Add exclude item_type_id for site license 2013/07/01 A.Suzuki --end--
            }
            
            // Get file info
            $query = "SELECT ".RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NAME.", ".
                               RepositoryConst::DBCOL_REPOSITORY_FILE_PUB_DATE.", ".
                     "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_FILE." ".
                     "WHERE ".RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID." = ? ".
                     "AND ".RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_NO." = ? ".
                     "AND ".RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID." = ? ".
                     "AND ".RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO." = ?;";
            $params = array();
            $params[] = $itemId;
            $params[] = $itemNo;
            $params[] = $attributeId;
            $params[] = $fileNo;
            $file = $this->Db->execute($query, $params);
            if($file === false)
            {
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
            
            // Set file name
            $fileName = $file[0][RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NAME];
            
            // Check open access or pay per view
            $pub_date = explode(" ", $file[0][RepositoryConst::DBCOL_REPOSITORY_FILE_PUB_DATE]);
            $pub_date = explode("-", $pub_date[0]);
            $pub_date = sprintf("%04d%02d%02d", $pub_date[0],$pub_date[1],$pub_date[2]);
            $log_date = explode(" ", $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_RECORD_DATE]);
            $log_date = explode("-", $log_date[0]);
            $log_date = sprintf("%04d%02d%02d", $log_date[0],$log_date[1],$log_date[2]);
            $download_log = array();
            if($pub_date <= $log_date){
                // Open access
                $fileStatus = RepositoryConst::LOG_FILE_STATUS_OPEN;
            } else {
                // Need login file / Pay per view
                $fileStatus = RepositoryConst::LOG_FILE_STATUS_CLOSE;
            }
            
            // Check not login download
            if(strlen($logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_USER_ID]) == 0 || $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_USER_ID] == "0")
            {
                // No login user
                $loginStatus = RepositoryConst::LOG_LOGIN_STATUS_NO_LOGIN;
            }
            else
            {
                // Check user's authority
                $userAuthId = $this->getUserAuthIdByUserId($logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_USER_ID]);
                
                require_once WEBAPP_DIR. '/modules/repository/components/RepositoryUserAuthorityManager.class.php';
                $container = & DIContainerFactory::getContainer();
                $session = $container->getComponent("Session");
                $userAuthorityManager = new RepositoryUserAuthorityManager($session, $this->Db, $this->accessDate);
                $authId = $userAuthorityManager->getRoomAuthorityID($logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_USER_ID]);

                $businessItemAuthority = BusinessFactory::getFactory()->getBusiness("businessItemAuthority");
                
                if(strlen($userAuthId) > 0 && ($userAuthId >= $this->repository_admin_base && $authId >= $this->repository_admin_room))
                {
                    // Admin user
                    $loginStatus = RepositoryConst::LOG_LOGIN_STATUS_ADMIN;
                }
                else if($businessItemAuthority->isItemContributor($logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_USER_ID], $itemId, $itemNo))
                {
                    // Register
                    $loginStatus = RepositoryConst::LOG_LOGIN_STATUS_REGISTER;
                }
                else
                {
                    // Login user
                    $loginStatus = RepositoryConst::LOG_LOGIN_STATUS_LOGIN;
                    if($inputType == RepositoryConst::LOG_INPUT_TYPE_FILE_PRICE)
                    {
                        // Get file price info
                        $query = "SELECT ".RepositoryConst::DBCOL_REPOSITORY_FILE_PRICE_PRICE." ".
                                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_FILE_PRICE." ".
                                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_FILE_PRICE_ITEM_ID." = '".$itemId."' ".
                                 "AND ".RepositoryConst::DBCOL_REPOSITORY_FILE_PRICE_ITEM_NO." = '".$itemNo."' ".
                                 "AND ".RepositoryConst::DBCOL_REPOSITORY_FILE_PRICE_ATTRIBUTE_ID." = '".$attributeId."' ".
                                 "AND ".RepositoryConst::DBCOL_REPOSITORY_FILE_PRICE_FILE_NO." = '".$fileNo."' ";
                        $price = $this->Db->execute($query);
                        if($price === false)
                        {
                            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                            throw new AppException($this->Db->ErrorMsg());
                        }
                        
                        // Check download user's affiliate
                        $groupId = $this->getDownloadType($price[0][RepositoryConst::DBCOL_REPOSITORY_FILE_PRICE_PRICE], $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_USER_ID]);
                        if(strlen($groupId) > 0 && $groupId != "0")
                        {
                            // Group user
                            $loginStatus = RepositoryConst::LOG_LOGIN_STATUS_GROUP;
                        }
                    }
                }
            }
        }
        else
        {
            // Get file status by log record.
            $fileStatus = $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_FILE_STATUS];
            $siteLicense = $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_SITE_LICENSE];
            $inputType = $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_INPUT_TYPE];
            $loginStatus = $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_LOGIN_STATUS];
            $groupId = $logRecord[RepositoryConst::DBCOL_REPOSITORY_LOG_GROUP_ID];
            
            // Get file name
            $query = "SELECT ".RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NAME." ".
                     "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_FILE." ".
                     "WHERE ".RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID." = ? ".
                     "AND ".RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_NO." = ? ".
                     "AND ".RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID." = ? ".
                     "AND ".RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO." = ?; ";
            $params = array();
            $params[] = $itemId;
            $params[] = $itemNo;
            $params[] = $attributeId;
            $params[] = $fileNo;
            $file = $this->Db->execute($query, $params);
            if($file === false)
            {
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
            $fileName = $file[0][RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NAME];
        }
        
        return true;
    }
    
    /**
     * Set file download log report to array
     * ファイルダウンロードデータに情報を入れる
     *
     * @param string $key Key キー
     * @param string $fileName File name ファイル名
     * @param int $fileStatus File status ファイル状態
     * @param int $siteLicense Site license status サイトライセンス状態
     * @param int $loginStatus Login status ログイン状態
     * @param array $fileLogFile download data ファイルダウンロードデータ
     *                       array[key]["file_name"|"index_name"|"total"|"not_login"|"login"|"site_license"|"admin"|"register"]
     */
    private function setFileLogArray($key, $fileName, $fileStatus, $siteLicense, $loginStatus, &$fileLog)
    {
        if(!isset($fileLog[$key]))
        {
            $fileLog[$key]['file_name'] = $fileName;
            $fileLog[$key]['index_name'] = $this->getIndexNameByItemKey($key);
            $fileLog[$key]['total'] = 0;        // トータル(total)
            $fileLog[$key]['not_login'] = 0;    // 個人(not login)
            $fileLog[$key]['login'] = 0;        // ログインユーザー(login)
            $fileLog[$key]['site_license'] = 0; // Download by site license
            $fileLog[$key]['admin'] = 0;        // Download by admin
            $fileLog[$key]['register'] = 0;     // Download by register
        }
        
        $this->traceLog($loginStatus, __FILE__, __CLASS__, __LINE__);
        
        // Check not login download
        $fileLog[$key]['total']++;
        if($loginStatus == RepositoryConst::LOG_LOGIN_STATUS_ADMIN)
        {
            $fileLog[$key]['admin']++;
        }
        else if($loginStatus == RepositoryConst::LOG_LOGIN_STATUS_REGISTER)
        {
            $fileLog[$key]['register']++;
        }
        else if($siteLicense == RepositoryConst::LOG_SITE_LICENSE_ON)
        {
            $fileLog[$key]['site_license']++;
        }
        else if($loginStatus == RepositoryConst::LOG_LOGIN_STATUS_NO_LOGIN)
        {
            $fileLog[$key]['not_login']++;
        }
        else
        {
            $fileLog[$key]['login']++;
        }
    }

    /**
     * Set file_price download log report to array
     * 課金ファイルのファイルダウンローデータに情報を入れる
     *
     * @param string $key Key キー
     * @param string $fileName File name ファイル名
     * @param int $fileStatus File status ファイル状態
     * @param int $loginStatus Login status ログイン状態
     * @param int $siteLicense Site license status サイトライセンス状態
     * @param int $groupId Group id グループID
     * @param array $fileLogFile download data ファイルダウンロードデータ
     *                       array[key]["file_name"|"index_name"|"total"|"not_login"|"login"|"site_license"|"admin"|"register"]
     * @param array $priceLog download price data 課金ファイルダウンロードデータ
     *                       array[key]["file_name"|"index_name"|"total"|"not_login"|"login"|"site_license"|"admin"|"register"]
     */
    private function setFilePriceLogArray($key, $fileName, $fileStatus, $siteLicense, $loginStatus, $groupId, &$priceLog)
    {
        $this->traceLog("setFilePriceLogArray", __FILE__, __CLASS__, __LINE__);
        if(!isset($priceLog[$key]))
        {
            $priceLog[$key]['file_name'] = $fileName;
            $priceLog[$key]['index_name'] = $this->getIndexNameByItemKey($key);
            $priceLog[$key]['total'] = 0;           // トータル(total)
            $priceLog[$key]['not_login'] = 0;       // 個人(not login)
            $priceLog[$key]['group'] = array();     // グループ(group(room))
            $priceLog[$key]['group']['0'] = 0;      // 非会員(login user(not affiliate))
            $priceLog[$key]['site_license'] = 0;    // Download by site license
            $priceLog[$key]['admin'] = 0;           // Download by admin
            $priceLog[$key]['register'] = 0;        // Download by register
        }
        
        // Check not login download
        $this->traceLog($loginStatus, __FILE__, __CLASS__, __LINE__);
        $priceLog[$key]['total']++;
        if($loginStatus == RepositoryConst::LOG_LOGIN_STATUS_ADMIN)
        {
            $priceLog[$key]['admin']++;
        }
        else if($loginStatus == RepositoryConst::LOG_LOGIN_STATUS_REGISTER)
        {
            $priceLog[$key]['register']++;
        }
        else if($siteLicense == RepositoryConst::LOG_SITE_LICENSE_ON)
        {
            $priceLog[$key]['site_license']++;
        }
        else if($loginStatus == RepositoryConst::LOG_LOGIN_STATUS_NO_LOGIN)
        {
            $priceLog[$key]['not_login']++;
        }
        else if($loginStatus == RepositoryConst::LOG_LOGIN_STATUS_GROUP)
        {
            if(isset($priceLog[$key]['group'][$groupId]))
            {
                $priceLog[$key]['group'][$groupId]++;
            } else {
                $priceLog[$key]['group'][$groupId] = 1;
            }
        }
        else
        {
            $priceLog[$key]['group']['0']++;
        }
    }

    /**
     * The list of affiliation group names is acquired to the user who specified.
     * グループ名一覧を取得
     *
     * @param string $user_id User id ユーザID
     * @return string User Group Name List グループ名一覧
     */
    private function getUserGroupNameList($user_id){
        // ---------------------------------------------
        // init
        // ---------------------------------------------
        $str = "";
        // ---------------------------------------------
        // get List from pages Table
        // ---------------------------------------------
        $query = "SELECT PAGES.page_name".
                 " FROM ".DATABASE_PREFIX."pages AS PAGES".
                 " INNER JOIN ".DATABASE_PREFIX."pages_users_link AS PAGES_USERS_LINK".
                 " ON PAGES.room_id=PAGES_USERS_LINK.room_id".
                 " WHERE PAGES_USERS_LINK.user_id = ?".
                 " AND PAGES.private_flag = ? ".
                 " AND PAGES.space_type = ?".
                 " AND PAGES_USERS_LINK.role_authority_id != ? ". 
                 " ORDER BY PAGES.page_name ASC;";
        $params = null;
        $params[] = $user_id;
        $params[] = 0;
        $params[] = _SPACE_TYPE_GROUP;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $error_msg = $this->Db->ErrorMsg();
            return false;
        }
        for($ii=0; $ii<count($result); $ii++){
            
            if($ii == count($result)-1){
                $str .= $result[$ii]['page_name'];
            }else{
                $str .= $result[$ii]['page_name'].",";
            }
        }
        return $str;
    }
    
    /**
     * send http request and get response
     * リクエスト送信
     *
     * @param string $sendParam send url リクエストURL
     * @return array Responce レスポンス
     *               array["code"|"header"|"body"|"cookies"]
     */
    private function sendHttpRequest($sendParam)
    {
        $this->traceLog("sendParam: ". $sendParam, __FILE__, __CLASS__, __LINE__);
        
        /////////////////////////////
        // HTTP_Request init
        /////////////////////////////
        // send http request
        $option = array( 
            "timeout" => "10",
            "allowRedirects" => true, 
            "maxRedirects" => 3, 
        );
        // Modfy proxy 2011/12/06 Y.Nakao --start--
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
        $repositoryAction = new RepositoryAction();
        $repositoryAction->Db = $this->Db;
        $proxy = $repositoryAction->getProxySetting();
        
        if($proxy['proxy_mode'] == 1)
        {
            $option = array( 
                    "timeout" => "10",
                    "allowRedirects" => true, 
                    "maxRedirects" => 3,
                    "proxy_host"=>$proxy['proxy_host'],
                    "proxy_port"=>$proxy['proxy_port'],
                    "proxy_user"=>$proxy['proxy_user'],
                    "proxy_pass"=>$proxy['proxy_pass']
                );
        }
        // Modfy proxy 2011/12/06 Y.Nakao --end--
        $http = new HTTP_Request($sendParam, $option);
        // setting HTTP header
        $http->addHeader("User-Agent", $_SERVER['HTTP_USER_AGENT']); 
        $http->addHeader("Referer", $_SERVER['HTTP_REFERER']);
        
        /////////////////////////////
        // run HTTP request 
        /////////////////////////////
        $response = $http->sendRequest(); 
        if (!PEAR::isError($response)) { 
            $charge_code = $http->getResponseCode();// ResponseCode(200等)を取得 
            $charge_header = $http->getResponseHeader();// ResponseHeader(レスポンスヘッダ)を取得 
            $charge_body = $http->getResponseBody();// ResponseBody(レスポンステキスト)を取得 
            $charge_Cookies = $http->getResponseCookies();// クッキーを取得 
        } else {
            $this->errorLog($sendParam, __FILE__, __CLASS__, __LINE__);
            $this->errorLog("send request is failed", __FILE__, __CLASS__, __LINE__);
            throw new AppException("send request is failed");
        }
        
        $res = array("code" => $charge_code, 
                     "header" => $charge_header, 
                     "body" => $charge_body, 
                     "cookies" => $charge_Cookies);
        return $res;
    }
    
    /**
     * get index path by item key(itemId_itemNo)
     * インデックスパス取得
     *
     * @param string $key Key キー
     * @return string Index path インデックスパス
     */
    private function getIndexNameByItemKey($key)
    {
        $index_name = '';
        $info = explode("_", $key);
        $query = "SELECT index_id ".
                 " FROM ". DATABASE_PREFIX ."repository_position_index ".
                 " WHERE item_id = ? ".
                 " AND item_no = ?; ";
        $params = array();
        $params[] = $info[0];
        $params[] = $info[1];
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        for($ii=0; $ii<count($result); $ii++){
            if($ii != 0){
                $index_name .= " | ";
            }
            $index_name .= $this->getIndexFullPathByIndexId($result[$ii]['index_id']);
        }
        return $index_name;
    }
    
    /**
     * get index path by recursive processing
     * 再帰的にインデックスパスを取得する
     *
     * @param int $indexId Index id インデックスID
     * @return string Index path インデックスパス
     */
    private function getIndexFullPathByIndexId($indexId)
    {
        $index_name = ''; 
        // get this index info
        $query = "SELECT index_name, index_name_english, parent_index_id ".
                " FROM ". DATABASE_PREFIX ."repository_index ".
                " WHERE index_id = ?; ";
        $params = array();
        $params[] = $indexId;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        if(count($result) == 0){
            // the parent index of this index is root index
            return "";
        }
        
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        if($session->getParameter("_lang") == "japanese"){
            $index_name = $result[0]['index_name'];
        } else {
            $index_name = $result[0]['index_name_english'];
        }
        // search parent index name
        $p_name = $this->getIndexFullPathByIndexId($result[0]['parent_index_id']);
        if($p_name != ""){
            $index_name = $p_name."\\".$index_name;
        }
        
        return $index_name;
    }
    
    /**
     * Get user_authority_id by user ID
     * ユーザのベース権限を取得する
     *
     * @param  string $userId User id ユーザID
     * @return string User auth id ユーザ権限ID
     */
    private function getUserAuthIdByUserId($userId)
    {
        $userAuthId = "";
        if(strlen($userId) != 0 && $userId != "0")
        {
            $query = "SELECT AUTH.user_authority_id ".
                    "FROM ".DATABASE_PREFIX."users AS USERS, ".
                    DATABASE_PREFIX."authorities AS AUTH ".
                    "WHERE USERS.role_authority_id = AUTH.role_authority_id ".
                    "AND USERS.user_id = ? ;";
            $params = array();
            $params[] = $userId;
            $result = $this->Db->execute($query, $params);
            if($result !== false && count($result) > 0)
            {
                $userAuthId = $result[0]["user_authority_id"];
            }
        }
    
        return $userAuthId;
    }

}
?>