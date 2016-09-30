<?php
/**
 * Action class for background process elapsed time log create
 * 経過時間ログ作成非同期処理用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Elapsedtime.class.php 69174 2016-06-22 06:43:30Z tatsuya_koyasu $
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
 * Base class for carrying out asynchronously and recursively possibility is the ability to process a long period of time
 * 長時間処理する可能性がある機能を非同期かつ再帰的に実施するための基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/BackgroundProcess.class.php';

/**
 * Action class for background process elapsed time log create
 * 経過時間ログ作成非同期処理用アクションクラス
 * 
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Common_Background_Elapsedtime extends BackgroundProcess
{
    /**
     * Background process name for lock table
     * ロックテーブル用非同期処理名
     *
     * @var string
     */
    const PARAM_NAME = "Repository_Action_Common_Background_Elapsedtime";
    
    /**
     * Constructer
     * コンストラクタ
     *
     */
    public function __construct()
    {
        parent::__construct(self::PARAM_NAME);
    }
    
    /**
     * Check unregistered elapsed time log
     * 登録されていない経過時間ログが存在するか検索する
     *
     * @param array $target Unregistered elapsed time log
     *                       経過時間ログが登録されていないログ
     *                       array[$ii]["log_no"|"record_date"|"user_id"|...]
     *
     * @return boolean Whether or not to register elapsed time log
     *                 経過時間ログを登録するか否か
     */
    protected function prepareBackgroundProcess(&$target)
    {
        // get unregistered logs 
        $unregisteredLogs = $this->getUnregisteredLogs();
        
        if (count($unregisteredLogs) == 0){
            return false;
        }
        
        $target = $unregisteredLogs;
        
        return true;
    }
    
    /**
     * Register elapsed time log
     * 経過時間ログを登録する
     *
     * @param array $target Unregistered elapsed time log
     *                       経過時間ログが登録されていないログ
     *                       array[$ii]["log_no"|"record_date"|"user_id"|...]
     */
    protected function executeBackgroundProcess(&$target) 
    {
        $this->infoLog("businessLogmanager", __FILE__, __CLASS__, __LINE__);
        $logManager = BusinessFactory::getFactory()->getBusiness("businessLogmanager");
        
        for ($ii = 0; $ii < count($target); $ii++) 
        {
            $logManager->insertElapsedTimeRecord(
                $target[$ii]["operation_id"],
                $target[$ii]["record_date"],
                $target[$ii]["ip_address"],
                $target[$ii]["user_agent"],
                $target[$ii]["item_id"],
                $target[$ii]["item_no"],
                $target[$ii]["attribute_id"],
                $target[$ii]["file_no"],
                $target[$ii]["user_id"],
                $target[$ii]["log_no"],
                $target[$ii]["search_keyword"]
            );
        }
    }
    
    /** 
     * Get unregistered elapsed time logs from log table
     * ログテーブルから経過時間ログが登録されていないログを取得する
     *
     * @return array Unregistered elapsed time log
     *               経過時間ログが登録されていないログ
     *               array[$ii]["log_no"|"record_date"|"user_id"|...]
     */
    private function getUnregisteredLogs()
    {
        $query = "SELECT * ". 
                 "FROM ". DATABASE_PREFIX. "repository_log " . 
                 "WHERE log_no NOT IN ( ".
                     "SELECT log_no ". 
                     "FROM ". DATABASE_PREFIX. "repository_log_elapsed_time " . 
                  ") " . 
                  "LIMIT 100 ; ";
        
        $result = $this->Db->execute($query);
        
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
}
?>
