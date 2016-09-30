<?php

/**
 * Reconstruct index view rights table
 * インデックス権限テーブル再作成クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Indexauthority.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Reconstruct index view rights table
 * インデックス権限テーブル再作成クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Common_Reconstruction_Indexauthority extends RepositoryAction
{
    // request parameter
    /**
     * login ID
     * NC2ログインID
     *
     * @var int
     */
    public $login_id = null;
    /**
     * login password
     * NC2ログインパスワード
     *
     * @var int
     */
    public $password = null;
    
    // user's authority level
    /**
     * user authority ID
     * ユーザーベース権限
     *
     * @var int
     */
    public $user_authority_id = "";
    /**
     * authority ID
     * ユーザールーム権限
     *
     * @var int
     */
    public $authority_id = '';

    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     */
    function executeApp()
    {
        // check login
        $result = null;
        $error_msg = null;
        $return = $this->checkLogin($this->login_id, $this->password, $result, $error_msg);
        if($return == false){
            print("Incorrect Login!\n");
            return false;
        }
        
        // check user authority id
        if($this->user_authority_id < $this->repository_admin_base || $this->authority_id < $this->repository_admin_room){
            print("You do not have permission to update.\n");
            return false;
        }
        
        // update table
        require_once WEBAPP_DIR. '/modules/repository/components/RepositoryIndexAuthorityManager.class.php';
        $indexManager = new RepositoryIndexAuthorityManager($this->Session, $this->dbAccess, $this->TransStartDate);
        $indexManager->reconstructIndexAuthorityTable();
        
        print("Successfully updated.\n");
        return 'success';
    }
}
?>