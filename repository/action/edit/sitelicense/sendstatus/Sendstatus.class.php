<?php

/**
 * Repository Action Edit Sitelicense SendStatus
 * サイトライセンス送信ステータス更新アクション
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Sendstatus.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR.'/modules/repository/components/common/WekoAction.class.php';

/**
 * Repository Action Edit Sitelicense SendStatus
 * サイトライセンス送信ステータス更新アクション
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Edit_Sitelicense_Sendstatus extends WekoAction
{
    /**
     * sitelicense feedback mail send allow flag
     * サイトライセンス利用統計フィードバックメール送信可否フラグ
     *
     * @var int
     */
    public $status = null;
    
    /**
     * Execute
     * 実行
     */
    function executeApp()
    {
        $sitelicenseManager = BusinessFactory::getFactory()->getBusiness("businessSitelicensemanager");
        $sitelicenseManager->updateAllowCron($this->status);
        
        $this->exitFlag = true;
    }
}

?>