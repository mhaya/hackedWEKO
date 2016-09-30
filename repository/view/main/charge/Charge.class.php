<?php

/**
 * View class for usage details screen display
 * 利用明細画面表示用ビュークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Charge.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * View class for usage details screen display
 * 利用明細画面表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Main_Charge extends RepositoryAction
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
    
    // member
    /**
     * Use specification confirmation URL
     * 利用明細確認URL
     *
     * @var string
     */
    var $charge_url = "";
    
    /**
     * Usage details Display
     * 利用明細表示
     *
     * @return string Result 結果
     */
    function execute()
    {
        
        // Modify charge list 2011/11/09 Y.Nakao --start--
        
        // start action
        $this->initAction();
        
        // Modify Price method move validator K.Matsuo 2011/10/18 --start--
        require_once WEBAPP_DIR. '/modules/repository/validator/Validator_DownloadCheck.class.php';
        $validator = new Repository_Validator_DownloadCheck();
        $initResult = $validator->setComponents($this->Session, $this->Db);
        if($initResult === 'error'){
            return 'error';
        }
        $charge_pass = $validator->getChargePass();
        // Modify Price method move validator K.Matsuo 2011/10/18 --end--
        $this->charge_url = "https://".$charge_pass["charge_fqdn"]."/weko-usage/list/".$charge_pass["sys_id"]."/";
        
        // end action
        $result = $this->exitAction();
        if ( $result === false ) {
            $exception = new RepositoryException( "ERR_MSG_xxx-xxx3", 1 );
            throw $exception;
        }
        $this->finalize();
        return 'success';
        
    }
}
?>
