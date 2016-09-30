<?php

/**
 * At the time the log aggregate common classes
 * ログ集計時共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Bibtex.class.php 48455 2015-02-16 10:53:40Z atsushi_suzuki $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';

/**
 * Log management common classes
 * ログ管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/Logmanager.class.php';

/**
 * At the time the log aggregate common classes
 * ログ集計時共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Logbase extends BusinessBase
{
    /**
     * Log deletion exception code
     * ログ削除例外コード
     *
     * @var int
     */
    const APP_EXCEPTION_KEY_REMOVING_LOG = 0;
    /**
     * Exception code when the inherited class does not exist
     * 継承クラスが存在しないときの例外コード
     *
     * @var int
     */
    const APP_EXCEPTION_KEY_NO_EXECUTE_APP = 1;
    
    /**
     * Make sure that the log deletion has not been performed, perform each log aggregation Inheritors
     * ログ削除が実施されていないことを確認し、継承クラスで各ログ集計を行う
     */
    public function execute()
    {
        // check removing log process
        $query = "SELECT status ". 
                 " FROM ". DATABASE_PREFIX. "repository_lock ". 
                 " WHERE process_name = ? ;";
        $params = array();
        $params[] = 'Repository_Action_Common_Robotlist';
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // when execute removing log, throw exception
        for($cnt = 0; $cnt < count($result); $cnt++)
        {
            if(intval($result[$cnt]['status']) > 0){
                $exception = new AppException("repository_log_excluding", self::APP_EXCEPTION_KEY_REMOVING_LOG);
                $exception->addError("repository_log_excluding");
                throw $exception;
            }
        }
        
        $this->executeApp();
    }
    
    /**
     * Log aggregation processing
     * ログ集計処理
     */
    protected function executeApp()
    {
        throw new AppException("no AppExecute on extended class", self::APP_EXCEPTION_KEY_NO_EXECUTE_APP);
    }
    
    /**
     * Subquery acquisition process at the time of site license log summary
     * サイトライセンスログ集計時のサブクエリ取得処理
     */
    protected function getSubQueryForSiteLicenseLog()
    {
        
    }
}
?>