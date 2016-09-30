<?php

/**
 * External search keyword update action class
 * 外部検索キーワード更新アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Updateexternalsearchword.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Base class for carrying out asynchronously and recursively possibility is the ability to process a long period of time
 * 長時間処理する可能性がある機能を非同期かつ再帰的に実施するための基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/BackgroundProcess.class.php';

/**
 * Common class that manages the search keyword taken out from the external search engine as an external search keyword
 * 外部検索エンジンから取り出した検索キーワードを外部検索キーワードとして管理する共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryExternalSearchWordManager.class.php';


/**
 * External search keyword update action class
 * 外部検索キーワード更新アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Common_Updateexternalsearchword extends BackgroundProcess
{
    /**
     * Update record number
     * 更新レコード数
     *
     * @car int
     */
    const MAX_RECORDS = 100;
    
    /**
     * Process name
     * プロセス名
     *
     * @var string
     */
    const PARAM_NAME = "Repository_Action_Common_Updateexternalsearchword";
    
    //----------------------------
    // Request parameters
    //----------------------------
    /**
     * Administrator login ID
     * 管理者ログインID
     *
     * @var string
     */
    public $log_id = null;
    /**
     * Administrator login ID
     * 管理者ログインID
     *
     * @var string
     */
    public $login_id = null;
    /**
     * Administrator password
     * 管理者パスワード
     *
     * @var string
     */
    public $password = null;
    
    /**
     * constructer
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct(self::PARAM_NAME);
    }
    
    /**
     * get log infomartion
     * ログ情報取得
     *
     * @param array $log_info Log information
     *                        array[$ii]["log_no"|"item_id"|"item_no"|"referer"]
     * @return boolean Result 結果
     */
    protected function prepareBackgroundProcess(&$log_info) {
        
        // check login
        $result = null;
        $error_msg = null;
        $return = $this->checkLogin($this->login_id, $this->password, $result, $error_msg);
        if($return == false){
            print("Incorrect Login!\n");
            $this->failTrans();
            return false;
        }
        if($this->log_id == null || $this->log_id == ""){
            $this->log_id = 0;
        }
        $query = "SELECT log_no, item_id, item_no, referer FROM ". DATABASE_PREFIX. "repository_log ".
                 "WHERE LENGTH(referer) > ? ".
                 "AND operation_id = ? ".
                 "AND log_no > ? ".
                 "LIMIT 0, ? ;";
        $params = array();
        $params[] = 1;
        $params[] = 3;
        $params[] = $this->log_id;
        $params[] = self::MAX_RECORDS;
        $log_info = $this->dbAccess->executeQuery($query, $params);
        
        if(count($log_info) == 0) {
            return false;
        }
        for($ii = 0; $ii < count($log_info); $ii++) {
            if($this->log_id < $log_info[$ii]["log_no"]) {
                $this->log_id = $log_info[$ii]["log_no"];
            }
        }
        $_GET["log_id"] = $this->log_id;
        return true;
    }
    
    /** 
     * External search keyword update
     * 外部検索キーワード更新
     * 
     * @param array $log_info Log information
     *                        array[$ii]["log_no"|"item_id"|"item_no"|"referer"]
     */
    protected function executeBackgroundProcess($log_info) {
        $searchWordManager = new Repository_Components_RepositoryExternalSearchWordManager($this->Session, $this->Db, $this->TransStartDate);
        for($ii = 0; $ii < count($log_info); $ii++) {
            $searchWordManager->insertExternalSearchWordFromURL($log_info[$ii]["item_id"], $log_info[$ii]["item_no"], $log_info[$ii]["referer"]);
        }
        // Print message.
        print("Start Update Stopword.\n");
    }
}
?>