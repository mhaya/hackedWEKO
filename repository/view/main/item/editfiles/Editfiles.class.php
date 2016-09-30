<?php

/**
 * Item register: View of file select
 * アイテム登録：ファイル選択画面表示
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Editfiles.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Item register: View of file select
 * アイテム登録：ファイル選択画面表示
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Main_Item_Editfiles extends WekoAction
{
    // 表示用パラメーター
    /**
     * Display help icon flag
     * ヘルプアイコン表示フラグ
     *
     * @var string
     */
    public $help_icon_display =  "";
    
    // リクエストパラメーター
    /**
     * Warning message
     * 警告メッセージ配列
     *
     * @var array
     */
    public $warningMsg = null;
    
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws AppException
     */
    protected function executeApp()
    {
        // RepositoryActionのインスタンス
        $repositoryAction = new RepositoryAction();
        $repositoryAction->Session = $this->Session;
        $repositoryAction->Db = $this->Db;
        $repositoryAction->dbAccess = $this->Db;
        $repositoryAction->TransStartDate = $this->accessDate;
        
        // Add registered info save action 2009/01/29 Y.Nakao --start--
        $repositoryAction->setLangResource();
        // Add registered info save action 2009/01/29 Y.Nakao --end--
        
        // Set help icon setting
        $tmpErrorMsg = "";
        $result = $repositoryAction->getAdminParam('help_icon_display', $this->help_icon_display, $tmpErrorMsg);
        if ( $result === false ){
            $this->errorLog($tmpErrorMsg, __FILE__, __CLASS__, __LINE__);
            $exception = new AppException($tmpErrorMsg);
            $exception->addError($tmpErrorMsg);
            throw $exception;
        }
        
        return 'success';
    }
}
?>
