<?php

/**
 * Send usage statistics feddback mail class
 * 利用統計フィードバックメール送信クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Usagestatisticsmail.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * Send usage statistics feddback mail class
 * 利用統計フィードバックメール送信クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Common_Usagestatisticsmail extends RepositoryAction
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
     * year for feedback
     * フィードバック対象年
     *
     * @var int
     */
    public $year = null;
    /**
     * month for feedback
     * フィードバック対象月
     *
     *
     * @var int
     */
    public $month = null;
    /**
     * user authority id
     * ユーザーベース権限
     *
     * @var int
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
     * language
     * 表示言語
     *
     * @var string
     */
    public $lang = "";
    /**
     * Wait time of retrying socket open (sec)
     * ソケットオープン再試行時の待ち時間 (秒)
     *
     * @var int
     */
    const SOCKOPEN_WAIT_SEC_TIME_RETRY = 1;
    /**
     * Try count of socket open
     * ソケットオープンの試行回数
     *
     * @var int
     */
    const SOCKOPEN_TRY_COUNT = 4;

    /**
     * Execute
     * 実行
     *
     * @return null
     * @throws AppException
     */
    function executeApp() {
        //アクション初期化処理
        $result = $this->initAction();
        if ( $result === false ) {
            $this->errorLog("Failed initAction", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Failed to execute.");
        }
        
        // check execute authority
        if(!$this->checkExecuteAuthority()) {
            $this->errorLog("failed to login for send feedback mail", __FILE__, __CLASS__, __LINE__);
            print("Login is failed.\n");
            $this->exitFlag = true;
            return;
        }
        
        // Set user id to session
        $this->Session->setParameter("_user_id", $this->user_id);
        
        // Set language
        if(strlen($this->lang) == 0) {
            $this->lang = $this->Session->getParameter("_lang");
        }
        
        // Set year and month
        $this->setYearAndMonth();
        
        // get instance
        $this->infoLog("businessSendusagestatisticsmail", __FILE__, __CLASS__, __LINE__);
        $SendMail = BusinessFactory::getFactory()->getBusiness("businessSendusagestatisticsmail");
        
        // parameter for send
        $mailAddress = "";
        $orderNum = 0;
        $isAuthor = false;
        $authorId = 0;
        $status = $SendMail->openProgressFile($mailAddress, $orderNum, $isAuthor, $authorId);
        if($status == "start") {
            // --------------------
            // Start send mail
            // --------------------
            // If activate flag is unavailable, cannot start send mail.
            if(!$this->getSendFeedbackMailActivateFlag()) {
                $this->infoLog("send feedback mail flag is disabled", __FILE__, __CLASS__, __LINE__);
                print("Cannot to send feedback mail. Because setting is not enabled.\n");
                $this->exitFlag = true;
                return;
            }
            
            // Call send mail start process
            $SendMail->startSendMail();
            
            // Create progress file
            if(!$SendMail->createProgressFile()) {
                // Print error message.
                $this->errorLog("create progress file failed", __FILE__, __CLASS__, __LINE__);
                print("Failed to send feedback mail.\n");
                throw new AppException("create progress file failed");
            }
            
            // 再帰リクエストによってトランザクション競合が起きて更新ミスが起きるためここでファイナライズする
            $this->exitAction();
            
            // Call oneself by async
            if(!$this->callAnotherProcessByAsync()) {
                // Print error message.
                $this->errorLog("execute HTTP request failed", __FILE__, __CLASS__, __LINE__);
                print("Failed to send feedback mail.\n");
                throw new AppException("execute HTTP request failed");
            }
            // Print message.
            print("Start send feedback mail.\n");
        } else if($status == "running") {
            // --------------------
            // Running send mail
            // --------------------
            // Execute send mail
            $SendMail->executeSendMail($mailAddress, 
                                       $orderNum, 
                                       $authorId, 
                                       $isAuthor, 
                                       $this->year, 
                                       $this->month, 
                                       $this->lang);
            
            // Update progress file
            if(!$SendMail->updateProgressFile()) {
                // Print error message.
                $this->errorLog("update progress file is failed", __FILE__, __CLASS__, __LINE__);
                print("Failed to send feedback mail.\n");
                throw new AppException("execute HTTP request failed");
            }
            
            // 再帰リクエストによってトランザクション競合が起きて更新ミスが起きるためここでファイナライズする
            $this->exitAction();
            
            // Call oneself by async
            if(!$this->callAnotherProcessByAsync()) {
                // Print error message.
                $this->errorLog("execute HTTP request failed", __FILE__, __CLASS__, __LINE__);
                print("Failed to send feedback mail.\n");
                throw new AppException("execute HTTP request failed");
            }
            // Print message.
            print("Send feedback mail runnung continue.\n");
        } else if($status == "end") {
            // --------------------
            // End send mail
            // --------------------
            // Call send mail end process
            $SendMail->endSendMail();
            
            // 再帰リクエストによってトランザクション競合が起きて更新ミスが起きるためここでファイナライズする
            $this->exitAction();
            
            // Print message.
            print("Send feedback mail completed.\n");
        } else {
            // Print message.
            print("Cannot execute send mail, because running other process.\n");
        }
        
        // Finalize
        $this->exitFlag = true;
        return;
    }
    
    /**
     * Call next request of usage statistics mail
     * 次のフィードバックメール送信のリクエストを呼び出す
     *
     * @return bool Whether or not to success calling request
     *               リクエスト呼び出しが成功したか否か
     */
    private function callAnotherProcessByAsync() {
        // Request parameter for next URL
        $nextRequest = BASE_URL."/?action=repository_action_common_usagestatisticsmail".
                       "&year=".$this->year."&month=".$this->month."&lang=".$this->lang.
                       "&login_id=".$this->login_id."&password=".$this->password;
        $url = parse_url($nextRequest);
        $nextRequest = str_replace($url["scheme"]."://".$url["host"], "",  $nextRequest);
        
        // Call oneself by async
        $host = array();
        preg_match("/^https?:\/\/(([^\/]+)).*$/", BASE_URL, $host);
        $hostName = $host[1];
        if($hostName == "localhost") {
            $hostName = gethostbyname($_SERVER['SERVER_NAME']);
        }
        $hostSock = $hostName;
        if($_SERVER["SERVER_PORT"] == 443) {
            $hostSock = "ssl://".$hostName;
        }
        
        $handle = false;
        for($count = 0; $count < self::SOCKOPEN_TRY_COUNT; $count++) {
            $handle = fsockopen($hostSock, $_SERVER["SERVER_PORT"], $errno, $errstr);
            if($handle) {
                break;
            }
            $this->errorLog("fsockopen error. hostName = ".$hostSock.", port = ".$_SERVER["SERVER_PORT"].", errorNo = ".$errno.", errorStr = ".$errstr, __FILE__, __CLASS__, __LINE__);
            
            // 最後のfsockopenで失敗した場合、処理は待たない
            if($count < self::SOCKOPEN_TRY_COUNT-1) {
                // fsockopenが失敗した場合、一定時間処理を待つ
                sleep(self::SOCKOPEN_WAIT_SEC_TIME_RETRY);
                $this->errorLog("Retry fsockopen ".($count+1)." time. ", __FILE__, __CLASS__, __LINE__);
            }
        }
        if (!$handle) {
            $this->errorLog("Retry fsockopen ".(self::SOCKOPEN_TRY_COUNT-1)." time, but all failed ", __FILE__, __CLASS__, __LINE__);
            return false;
        }
        
        stream_set_blocking($handle, false);
        fwrite($handle, "GET ".$nextRequest." HTTP/1.1\r\nHost: ". $hostName."\r\n\r\n");
        fclose ($handle);
        
        return true;
    }
    
    /**
     * Set year and month
     * 年月をセットする
     */
    private function setYearAndMonth() {
        if( strlen($this->year) == 0 || intval($this->year) < 1 || strlen($this->month) == 0 || intval($this->month) < 1 || intval($this->month) > 12) {
            // Get previous month
            $prevYearMonth = $this->getPreviousMonth();
            $prevYearMonthArray = explode("-", $prevYearMonth, 2);
            $prevYear = intval($prevYearMonthArray[0]);
            $prevMonth = intval($prevYearMonthArray[1]);
            $this->year = $prevYear;
            $this->month = $prevMonth;
        }
    }
    
    /**
     * Get previous month
     * 前月を取得する
     *
     * @return string previous month 前月
     * @throws AppException
     */
    private function getPreviousMonth() {
        // Get previous month (format: YYYY-MM)
        $query = "SELECT DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m') AS prevMonth ";
        $result = $this->Db->execute($query);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        if(count($result) == 0) {
            return "";
        }
        
        return $result[0]["prevMonth"];
    }
    
    /**
     * Get send feedback mail activate flag
     * フィードバックメール送信可否フラグを取得する
     *
     * @return bool true/false allow sending/or not 送信可/不可
     * @throws AppException
     */
    private function getSendFeedbackMailActivateFlag() {
        $rtn = false;
        
        // Get send feedback mail activate flag in parameter
        $query = "SELECT ".RepositoryConst::DBCOL_REPOSITORY_PARAMETER_PARAM_VALUE." ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_PARAMETER." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_PARAMETER_PARAM_NAME." = 'send_feedback_mail_activate_flg';";
        $result = $this->Db->execute($query);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        if(count($result) == 1 && $result[0][RepositoryConst::DBCOL_REPOSITORY_PARAMETER_PARAM_VALUE]=="1") {
            $rtn = true;
        }
        
        return $rtn;
    }
    
    /**
     * check execute authority
     * 実行権限があるかチェックする
     *
     * @return bool true/false admin/general 権限がある/ない
     */
    private function checkExecuteAuthority() {
        // Init user authorities
        $this->user_authority_id = "";
        $this->authority_id = "";
        $this->user_id = "";
        
        // Check login
        $result = null;
        $error_msg = null;
        $return = $this->checkLogin($this->login_id, $this->password, $result, $error_msg);
        if($return == false) {
            print("Incorrect Login!\n");
            return false;
        }
        
        // Check user authority id
        if($this->user_authority_id < $this->repository_admin_base || $this->authority_id < $this->repository_admin_room) {
            print("You do not have permission to update.\n");
            return false;
        }
        
        return true;
    }
}
?>