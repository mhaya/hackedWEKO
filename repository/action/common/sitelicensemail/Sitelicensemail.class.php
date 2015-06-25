<?php
// --------------------------------------------------------------------
//
// $Id: Sitelicensemail.class.php 53594 2015-05-28 05:25:53Z kaede_matsushita $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

require_once WEBAPP_DIR. '/modules/repository/components/common/WekoAction.class.php';
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * Sitelicensemail
 *
 * @package     NetCommons
 * @author      T.Ichikawa(IVIS)
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Repository_Action_Common_Sitelicensemail extends WekoAction
{
    //----------------------------
    // Request parameters
    //----------------------------
    /**
     * login_id
     *
     * @var string
     */
    public $login_id = null;
    /**
     * password to login
     *
     * @var string
     */
    public $password = null;
    /**
     * authority_id
     *
     * @var string
     */    
    public $user_authority_id = "";
    /**
     * authority_id
     *
     * @var int
     */
    public $authority_id = "";
    /**
     * user_id
     *
     * @var string
     */
    public $user_id = "";
    
    /**
     * サイトライセンスメール送信処理開始
     * 
     */
    public function executeApp() {
        // ログインチェック
        if(!$this->checkExecuteAuthority()) {
            return "error";
        }
        
        // サイトライセンスメール用のビジネスクラス取得
        $this->infoLog("businessSendsitelicensemail", __FILE__, __CLASS__, __LINE__);
        $sendSitelicense = BusinessFactory::getFactory()->getBusiness("businessSendsitelicensemail");
        
        // サイトライセンスメールの送信フラグのチェック
        $send_flag = $sendSitelicense->checkSendSitelicense();
        
        if($send_flag == true) {
            // サイトライセンスメール送信対象者リストの作成
            $sendSitelicense->insertSendSitelicenseMailList();
            
            // サイトライセンス送信のバックグラウンド処理のリクエストを送信する
            // Call oneself by async
            if(!$this->callAnotherProcessByAsync())
            {
                // Print error message.
                print("Failed to send site license mail.\n");
                $this->exitFlag = true;
                return "error";
            } 
            
            // Print message.
            print("Start send site license mail.\n");
            
            return "success";
        }
        
        return "error";
    }
    
    /**
     * Call another process by async
     *
     * @return bool
     */
    public function callAnotherProcessByAsync()
    {
        // Request parameter for next URL
        $lang = $this->Session->getParameter("_lang");
        $nextRequest = BASE_URL."/?action=repository_action_common_background_sitelicensemail".
                       "&login_id=".$this->login_id."&password=".$this->password. "&lang=". $lang;
        $url = parse_url($nextRequest);
        $nextRequest = str_replace($url["scheme"]."://".$url["host"], "",  $nextRequest);
        
        // Call oneself by async
        $host = array();
        preg_match("/^https?:\/\/(([^\/]+)).*$/", BASE_URL, $host);
        $hostName = $host[1];
        if($hostName == "localhost"){
            $hostName = gethostbyname($_SERVER['SERVER_NAME']);
        }
        $hostSock = $hostName;
        if($_SERVER["SERVER_PORT"] == 443)
        {
            $hostSock = "ssl://".$hostName;
        }
        
        $handle = fsockopen($hostSock, $_SERVER["SERVER_PORT"]);
        if (!$handle)
        {
            return false;
        }
        
        stream_set_blocking($handle, false);
        fwrite($handle, "GET ".$nextRequest." HTTP/1.1\r\nHost: ". $hostName."\r\n\r\n");
        fclose ($handle);
        
        return true;
    }
    
    /**
     * check execute authority
     *
     * @return bool
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
}

?>