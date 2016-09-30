<?php

/**
 * ELS automatic registration action class
 * ELS自動登録アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Cinii.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * ELS automatic registration common classes
 * ELS自動登録共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryShelfregistration.class.php';

/**
 * ELS automatic registration action class
 * ELS自動登録アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Common_Cinii extends RepositoryAction
{
    // memba
    
    // component
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    public $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
    public $Db = null;
    
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
     * User of the base level of authority
     * ユーザのベース権限レベル
     *
     * @var string
     */
    public $user_authority_id = "";
    /**
     * User of room privilege level
     * ユーザのルーム権限レベル
     *
     * @var string
     */
    public $authority_id = "";

    /**
     * User id
     * ユーザID
     *
     * @var string
     */
    public $user_id = "";
    
    /**
     * Perform an automatic registration to ELS
     * ELSへ自動登録を行う
     */
    function execute()
    {
        try {
            $result = $this->initAction();
            if ( $result === false ) {
                $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );
                $DetailMsg = null;
                sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
                $exception->setDetailMsg( $DetailMsg );
                $this->failTrans(); // ROLLBACK
                throw $exception;
            }
            
            $this->user_authority_id = "";
            $this->authority_id = "";
            $this->user_id = "";
            
            // check login
            $result = null;
            $errMsg = null;
            $ret = $this->checkLogin($this->login_id, $this->password, $result, $errMsg);
            if($ret == false){
                print("Incorrect Login!\n");
                $this->failTrans();
                return false;
            }
            
            // check user authority id
            if($this->user_authority_id < $this->repository_admin_base || $this->authority_id < $this->repository_admin_room){
                print("You do not have permission to update.\n");
                $this->failTrans();
                return false;
            }
            
            // Set user id to session
            $this->Session->setParameter("_user_id", $this->user_id);
            
            // set constructor
            $shelfregistration = new RepositoryShelfregistration($this->Session, $this->Db, $this->TransStartDate);
            
            // start,runnning,end
            $shelfregistration->openProgressFile();
            $status = $shelfregistration->getStatus();
            if($status == "start"){
                
                // -------------------------------
                // start shelf registration
                // -------------------------------
                // create process file
                if(!$shelfregistration->createProgressFile())
                {
                    // Print error message.
                    print("Failed to shelf registration.\n");
                    $this->failTrans();
                    exit();
                }
                
                // create convert failed index list
                if(!$shelfregistration->createConvertFailedIndexList())
                {
                    // Print error message.
                    print("Failed to shelf registration.\n");
                    $this->failTrans();
                    exit();
                }
                
                // Call oneself by async
                if(!$this->callAnotherProcessByAsync())
                {
                    // Print error message.
                    print("Failed to shelf registration.\n");
                    $this->failTrans();
                    exit();
                }
                // Print message.
                print("Start shelf registration.\n");
                
            } else if($status == "running") {
                // -------------------------------
                // runnning shelf registration
                // -------------------------------
                // execute shelf registration
                if(!$shelfregistration->executeShelfRegistration())
                {
                    // Print error message.
                    print("Failed to shelf registration.\n");
                    $this->failTrans();
                    exit();
                }
                
                // update progress file
                if(!$shelfregistration->updateProgressFile())
                {
                    // Print error message.
                    print("Failed to shelf registration.\n");
                    $this->failTrans();
                    exit();
                }
                
                // call oneself by async
                if(!$this->callAnotherProcessByAsync())
                {
                    // Print error message.
                    print("Failed to shelf registration.\n");
                    $this->failTrans();
                    exit();
                }
                // Print message.
                print("Shelf registration runnung continue.\n");
            } else if($status == "end") {
                // -------------------------------
                // finish shelf registration
                // -------------------------------
                // call end process
                $shelfregistration->endShelfregistration();
                
                // Print message.
                print("Shelf registration completed.\n");
                
            } else {
                // error
                print("Cannot execute, because running other process.\n");
            }
            
            // Finalize
            $this->exitAction();
            exit();
        }
        catch ( RepositoryException $Exception) {
            // rollback
            $this->failTrans();
            print($exception->getMessage(). "\n");
            exits();
        }
    }
    
    /**
     * To run the ELS automatic registration action to asynchronous
     * ELS自動登録アクションを非同期に実行する
     *
     * @return boolean Execution result 実行結果
     */
    private function callAnotherProcessByAsync()
    {
        // create request url
        $nextRequest = BASE_URL. "/?action=repository_action_common_cinii". 
                       "&login_id=". $this->login_id. 
                       "&password=". $this->password;
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
}
?>
