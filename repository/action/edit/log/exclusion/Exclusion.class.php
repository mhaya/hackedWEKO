<?php

/**
 * Excluded address additional actions class
 * 除外対象アドレス設定アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Exclusion.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR. '/modules/repository/components/common/WekoAction.class.php';

/**
 * Excluded address additional actions class
 * 除外対象アドレス追加アクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Log_Exclusion extends WekoAction
{
    /**
     * additional exclude ip address
     * 追加する除外対象アドレス一覧
     *
     * @var string
     *         ex) 172.17.72.110,172.17.72.111
     */
    public $log_exclusion = null;

    /**
     * add excluded Ip Address List by request parameter
     * 除外対象アドレス追加
     */
    function executeApp()
    {
        $this->infoLog("businessLogmanager", __FILE__, __CLASS__, __LINE__);
        $logManager = BusinessFactory::getFactory()->getBusiness("businessLogmanager");
        $logManager->addExcludedIpAddrToDatabase($this->log_exclusion);
        
        $smartyAssign = $this->Session->getParameter("smartyAssign");
        echo $smartyAssign->getLang("repository_log_update_exclude_address"). 
             "\n". 
             $smartyAssign->getLang("repository_log_announce_update_clowler_list");
	}
}
?>
