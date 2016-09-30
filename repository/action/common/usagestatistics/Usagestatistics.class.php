<?php

/**
 * aggregate usage statistics class
 * 利用統計集計送信クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Usagestatistics.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * aggregate usage statistics class
 * 利用統計集計送信クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Common_Usagestatistics extends RepositoryAction
{
    // Request parameter
    /**
     * login id
     * NC2ログインID
     *
     * @var string
     */
    public $login_id = null;
    /**
     * password
     * NC2ログインパスワード
     *
     * @var string
     */
    public $password = null;
    
    // Member
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
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws AppException
     */
    public function executeApp()
    {
        // Init action
        $result = $this->initAction();
        if ($result === false) {
            $this->errorLog("Failed initAction", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Failed to execute.");
        }
        
        // check execute authority
        if(!$this->checkExecuteAuthority()) {
            $this->infoLog("failed to login for usagestatistics", __FILE__, __CLASS__, __LINE__);
            print("Login is failed.\n");
            return "error";
        }
        
        // Aggregate usage statistics
        $this->infoLog("businessUsagestatistics", __FILE__, __CLASS__, __LINE__);
        $usageStatistics = BusinessFactory::getFactory()->getBusiness("businessUsagestatistics");
        if(!$usageStatistics->aggregateUsagestatistics()) {
            $this->errorLog("", __FILE__, __CLASS__, __LINE__);
            print("Update usage statistics is failed.\n");
            throw new AppException("Failed to usage statistics update");
        }
        
        // finalize
        $this->exitAction();
        print("Successfully updated.\n");
        return "success";
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
        if($this->user_authority_id < $this->repository_admin_base || 
           $this->authority_id < $this->repository_admin_room) {
            print("You do not have permission to update.\n");
            return false;
        }
        
        return true;
    }
}
?>