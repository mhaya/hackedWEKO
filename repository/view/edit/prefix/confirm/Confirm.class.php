<?php

/**
 * View class for prefix registration completion screen display
 * prefix登録完了画面表示用ビュークラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Confirm.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Handle management common classes
 * ハンドル管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';

/**
 * View class for prefix registration completion screen display
 * prefix登録完了画面表示用ビュークラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Edit_Prefix_Confirm extends RepositoryAction
{
	// component 
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
	var $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
	var $Db = null;
    /**
     * Y handle prefix
     * Yハンドルprefix
     *
     * @var string
     */
	var $prefix = null;
	
    /**
     * Display prefix registration completion screen
     * prefix登録完了画面表示
     *
     * @access  public
     */
    function executeApp()
    {
        /////////////////////////////////
        // get PrefixID from DB
        /////////////////////////////////
        
        $repositoryDbAccess = new RepositoryDbAccess($this->Db);
        
        // Bug fix WEKO-2014-006 2014/04/28 T.Koyasu --start--
        $DATE = new Date();
        $this->TransStartDate = $DATE->getDate(). ".000";
        // Bug fix WEKO-2014-006 2014/04/28 T.Koyasu --end--
        
        $repositoryHandleManager = new RepositoryHandleManager($this->Session, $repositoryDbAccess, $this->TransStartDate);
        
        $this->prefix = $repositoryHandleManager->getPrefix(RepositoryHandleManager::ID_Y_HANDLE);
        
        return 'success';
    }
}
?>
