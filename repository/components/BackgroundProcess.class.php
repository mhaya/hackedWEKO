<?php

/**
 * Base class for carrying out asynchronously and recursively possibility is the ability to process a long period of time
 * 長時間処理する可能性がある機能を非同期かつ再帰的に実施するための基底クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BackgroundProcess.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Asynchronous processing run common classes
 * 非同期処理実行共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryProcessUtility.class.php';

/**
 * Base class for carrying out asynchronously and recursively possibility is the ability to process a long period of time
 * 長時間処理する可能性がある機能を非同期かつ再帰的に実施するための基底クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class BackgroundProcess extends RepositoryAction
{
    /**
     * Process name
     * プロセス名
     *
     * @var string
     */
    private $process_name = null;
    
    /**
     * Asynchronous processing end flag
     * 非同期処理終了フラグ
     *
     * @var boolean
     */
    private $isFinish = false;
    
    /**
     * Constructer(To set the process name)
     * コンストラクタ(プロセス名を設定する)
     *
     * @param string paramter Process name プロセス名
     */
    protected function __construct($parameter)
    {
        $this->process_name = $parameter;
    }
    
    /**
     * Data to be processed is read, and executes the processing
     * 処理対象のデータを読み込み、処理を実行する
     */
    protected function executeApp()
    {
        $this->exitFlag = true;
        
        // check process
        $status = $this->lockProcess();
        
        // init background process
        if($status != 0){
            $this->isFinish = true;
            return;
        }
        
        // get target 
        $executeFlag = $this->prepareBackgroundProcess($target);
        
        if($executeFlag == false){
            $this->unlockProcess();
            $this->isFinish = true;
            return;
        }
        
        // execute Background Process
        $this->executeBackgroundProcess($target);
        
        // execute next process
        $this->unlockProcess();
    }
    
    /**
     * Transaction outside the post-processing (calling the following processing)
     * トランザクション外後処理(次の処理を呼び出す)
     */
    final protected function afterTrans()
    {
        if(!$this->isFinish)
        {
            $this->callAsyncProcess();
        }
    }
    
    /**
     * As the same asynchronous processing is not multiple execution, leaving the effect that running the database
     * 同じ非同期処理が多重実行されないよう、データベースに実行中である旨を残す
     */
    private function lockProcess()
    {
        // update process status
        $query = "UPDATE ".DATABASE_PREFIX."repository_lock ".
                 "SET status = ? ".
                 "WHERE process_name = ? ".
                 "AND status = ?;";
        $params = array();
        $params[] = 1;
        $params[] = $this->process_name;
        $params[] = 0;
        $retRef = $this->dbAccess->executeQuery($query, $params);
        $count = $this->dbAccess->affectedRows();
        
        if($count == 0){
            return 1;
        } 
        return 0;
    }
    
    /**
     * Read the data to be processed
     * 処理対象のデータを読み込む
     * 
     * @param $target Data to be processed 処理対象のデータ
     */
    protected function prepareBackgroundProcess(&$target)
    {
        // for override
        return true;
    }
    
    /**
     * To perform the time-consuming process
     * 時間のかかる処理を実行する
     * 
     * @param $target Data to be processed 処理対象のデータ
     */
    protected function executeBackgroundProcess($target)
    {
        // for override
    }
    
    /**
     * To perform an action to asynchronous
     * アクションを非同期に実行する
     */
    private function callAsyncProcess()
    {
        // Request parameter for next URL
        $nextRequest = BASE_URL;
        $count = 0;
        foreach($_GET as $key => $value){
            if($count == 0){
                $nextRequest .= "/?";
            } else {
                $nextRequest .= "&";
            }
            $nextRequest .= $key."=".$value;
            $count++;
        }
        $result = RepositoryProcessUtility::callAsyncProcess($nextRequest);
        return $result;
    }
    
    /**
     * To OFF the flag for multiple execution prevention
     * 多重実行防止用のフラグをOFFにする
     */
    private function unlockProcess()
    {
        // update process status
        $query = "UPDATE ".DATABASE_PREFIX."repository_lock ".
                 "SET status = ? ".
                 "WHERE process_name = ?; ";
        $params = array();
        $params[] = 0;
        $params[] = $this->process_name;
        $retRef = $this->dbAccess->executeQuery($query, $params);
        return;
    }
    
}

?>
