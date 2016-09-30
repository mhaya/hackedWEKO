<?php

/**
 * Send sitelicense mail feedback mail class
 * サイトライセンス利用統計フィードバックメール送信クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Sitelicensemail.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/common/WekoAction.class.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Operation log abstract class
 * ログ操作基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/Logbase.class.php';

/**
 * Send sitelicense mail feedback mail class
 * サイトライセンス利用統計フィードバックメール送信クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Common_Sitelicensemail extends WekoAction
{
    //----------------------------
    // Request parameters
    //----------------------------
    /**
     * login id
     * NC2ログインID
     *
     * @var string
     */
    public $login_id = null;
    /**
     * password to login
     * NC2ログインパスワード
     *
     * @var string
     */
    public $password = null;
    /**
     * authority id
     * ユーザーベース権限
     *
     * @var string
     */    
    public $user_authority_id = "";
    /**
     * authority id
     * ユーザールーム権限
     *
     * @var int
     */
    public $authority_id = "";
    /**
     * user id
     * ユーザーID
     *
     * @var string
     */
    public $user_id = "";
    
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     */
    public function executeApp() {
        $this->exitFlag = true;
        
        // 実行可否チェック
        if(!$this->checkExecute()) { return "error"; }
        
        // 送信処理
        $php_path = $this->searchPhpPath();
        if(strlen($php_path) == 0) { return "error"; }
        
        // 集計対象年月(YYYYMM)
        $from = date('Ym', strtotime('-1 month'));
        // 何ヶ月分集計するか
        $to = 1;
        
        // 直前月1か月分の集計を全機関に対して行う
        $command = $php_path."php ".
                   WEBAPP_DIR."/modules/repository/batch/SitelicenseUsagestatistics/SitelicenseUsagestatisticsBatch.php".
                   " --from=".$from.
                   " --to=".$to;
        // 非同期実行
        if(PHP_OS == "WIN32" || PHP_OS == "WINNT"){ 
            $fp = popen("start ".$command, "r");
            fclose($fp);
        } else {
            exec($command." > /dev/null 2>&1 &");
        }
        
        print("Start send sitelicensemail.");
        
        return "success";
    }
    
    /**
     * 実行可能であるかチェックする
     * Check whether or not execute
     *
     * @return true/false can execute/or not 実行可能/実行不可
     * @throws AppException
     */
    private function checkExecute() {
        // 実行権限確認
        if(!$this->checkExecuteAuthority()) {
            return false;
        }
        // ロボットリストによるログ削除の実行中チェック
        if($this->isExecuteRemovingLog()) {
            return false;
        }
        // URLによるメール送信実行可否フラグチェック
        $sitelicenseManager = BusinessFactory::getFactory()->getBusiness("businessSitelicensemanager");
        if($sitelicenseManager->checkSendMailAllow() == 0) {
            print("Please check send mail status.");
            return false;
        }

        return true;
    }
    
    /**
     * check execute authority
     * 実行権限があるかチェックする
     *
     * @return bool true/false admin/general 権限がある/ない
     */
    private function checkExecuteAuthority()
    {
        // check login
        $result = null;
        $error_msg = null;
        
        $repositoryAction = new RepositoryAction();
        $repositoryAction->Session = $this->Session;
        $repositoryAction->Db = $this->Db;
        $repositoryAction->TransStartDate = $this->accessDate;
        $repositoryAction->setConfigAuthority();
        $repositoryAction->dbAccess = $this->Db;
        
        $return = $repositoryAction->checkLogin($this->login_id, $this->password, $result, $error_msg);
        if($return == false){
            print("Incorrect Login!\n");
            return false;
        }
        
        // check user authority id
        if($this->user_authority_id < $repositoryAction->repository_admin_base || $this->authority_id < $repositoryAction->repository_admin_room){
            print("You do not have permission to update.\n");
            return false;
        }
        
        return true;
    }
    
    /**
     * Check process deleting robot list log
     * ロボットリストログ削除中かどうか判定する
     *
     * @return bool true/false deleting/or not 削除処理中/削除処理中では無い
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
        for($cnt = 0; $cnt < count($result); $cnt++) {
            if(intval($result[$cnt]['status']) > 0) {
                print("Log data is updating. Please try again after waiting for a while.");
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * PHP実行パスを取得する
     * Get execute PHP path
     *
     * @return string PHP command path PHP実行パス
     * @throws AppException
     */
    private function searchPhpPath() {
        $query = "SELECT param_value FROM {repository_parameter} ".
                 "WHERE param_name = ? ;";
        $params = array();
        $params[] = "path_php";
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // PHP実行パスが未設定の場合はエラー
        if(strlen($result[0]["param_value"]) == 0) {
            print("PHP path is not set.");
        }
        
        return $result[0]["param_value"];
    }
}

?>