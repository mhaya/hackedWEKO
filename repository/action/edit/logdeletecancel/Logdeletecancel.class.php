<?php

/**
 * Log deletion cancellation action class of excluded
 * 除外対象のログ削除キャンセルアクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Logdeletecancel.class.php 51725 2015-04-07 09:33:19Z shota_suzuki $
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
 * Action base class for WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * Log deletion cancellation action class of excluded
 * 除外対象のログ削除キャンセルアクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Logdeletecancel extends RepositoryAction
{
    /**
     * To cancel the log deletion of excluded
     * 除外対象のログ削除をキャンセルする
     * 
     * @return string Result 結果
     */
    public function executeApp()
    {
        $this->infoLog("businessRobotlistbase", __FILE__, __CLASS__, __LINE__);
        $businessRobotlistbase = BusinessFactory::getFactory()->getBusiness("businessRobotlistbase");
        
        $businessRobotlistbase->unlockRobotListTable();
        
        return "success";
    }
}
?>
