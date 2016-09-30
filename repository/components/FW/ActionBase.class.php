<?php

/**
 * Action base class for NetCommons
 * NetCommons用アクション基底クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: ActionBase.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Exception class
 * 拡張例外クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/AppException.class.php';
/**
 * Business factory implementation class
 * ビジネスロジックインスタンス生成抽象クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/WekoBusinessFactory.class.php';

/**
 * Action base class for NetCommons
 * NetCommons用アクション基底クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
abstract class ActionBase extends Action
{
    /**
     * Logger object
     * ロガーコンポーネントを受け取る
     *
     * @var AppLogger
     */
    public $Logger = null;

    /**
     * Session management object
     * Session管理オブジェクト
     *
     * @var Session
     */
    public $Session = null;
    /**
     * DB object
     * Dbコンポーネントを受け取る
     *
     * @var DbObjectAdodb
     */
    public $Db = null;
    
    /**
     * アクセス日時
     * 
     * @var string
     */
    protected $accessDate = null;
    
    /**
     * Request parameter
     * リクエストパラメーター
     *
     * @var array
     */
    public $errMsg = null;
    
    /**
     * "exit()" flag
     * 処理を exit() で終了させるフラグ
     *
     * @var bool
     */
    protected $exitFlag = false;
    
    /**
     * Initialize
     * 初期化処理
     */
    protected function initialize()
    {
        // アクセス日時
        if(class_exists("DateTime")){
            $date = new DateTime();
            $this->accessDate = $date->format('Y-m-d H:i:s').".000";
        } else {
            $this->accessDate = date('Y-m-d H:i:s').".000";
        }
        
        // ビジネスロジック生成クラス初期化
        WekoBusinessFactory::initialize($this->Session, $this->Db, $this->accessDate);
        $this->Logger = WekoBusinessFactory::getFactory()->logger;
    }
    
    /**
     * Finalize
     * 終了処理
     */
    protected function finalize()
    {
        // ビジネスロジック生成クラス終了処理
        $businessFactory = BusinessFactory::getFactory();
        if(isset($businessFactory)) {
            $businessFactory->uninitialize();
        }
    }
    
    /**
     * Execute
     * 実行処理
     * 
     * @return string "success" or "error" 成功/失敗
     */
    public function execute()
    {
        try
        {
            // 初期化処理
            $this->initialize();
            
            // トランザクション外前処理
            $this->beforeTrans();
            
            $isTransStared = false;
            if($this->Db->StartTrans() === false)
            {
                $this->infoLog("Failed start trance.", __FILE__, __CLASS__, __LINE__);
                throw new AppException("Failed start trance.");
            }
            $isTransStared = true;
            
            // Actionからエラーメッセージが渡っていない場合に限り初期化する
            if(is_null($this->errMsg))
            {
                $this->errMsg = array();
            }
            
            // トランザクション内前処理呼び出し
            $this->preExecute();
            
            // ロジック呼び出し
            $ret = $this->executeApp();
            
            // トランザクション内後処理呼び出し
            $this->postExecute();
            
            // トランザクションコミット処理
            $this->completeTrans();
            
            // トランザクション外後処理
            $this->afterTrans();
            
            // 終了処理
            $this->finalize();
            
            if($this->exitFlag) {
                if(is_array($this->errMsg) && count($this->errMsg) > 0){
                    echo json_encode($this->errMsg);
                }
                exit();
            }
            else {
                return $ret;
            }
        }
        catch (AppException $e)
        {
            if($isTransStared)
            {
                if($this->Db->FailTrans() === false)
                {
                    $this->errorLog("Failed rollback trance.", __FILE__, __CLASS__, __LINE__);
                }
            }
            
            // エラーログをダンプ
            $this->exeptionLog($e, __FILE__, __CLASS__, __LINE__);
            
            // エラーメッセージを設定
            $errors = $e->getErrors();
            for($ii=0; $ii<count($errors); $ii++)
            {
                foreach ($errors[$ii] as $key => $val)
                {
                    $this->addErrMsg($key, $val);
                }
            }
            
            // ビジネスロジック生成クラス終了処理
            $businessFactory = BusinessFactory::getFactory();
            if(isset($businessFactory)) {
                $businessFactory->uninitialize();
            }
            
            if($this->exitFlag) {
                if(is_array($this->errMsg)){
                    echo json_encode($this->errMsg);
                }
                exit();
            }
            else {
                return "error";
            }
        }
        catch (Exception $e)
        {
            if($isTransStared)
            {
                if($this->Db->FailTrans() === false)
                {
                    $this->errorLog("Failed rollback trance.", __FILE__, __CLASS__, __LINE__);
                }
            }
            // エラーログをダンプ
            $this->exeptionLog($e, __FILE__, __CLASS__, __LINE__);
            
            $this->addErrMsg("予期せぬエラーが発生しました");
            
            // ビジネスロジック生成クラス終了処理
            $businessFactory = BusinessFactory::getFactory();
            if(isset($businessFactory)) {
                $businessFactory->uninitialize();
            }
            
            return "error";
        }
    }
    
    /**
     * Add error message
     * エラーメッセージを追加
     * 
     * @param string $key language key 言語表示キー
     * @param array $params messages エラーメッセージ
     */
    final protected function addErrMsg($key, $params=array())
    {
        // 初期化
        if(is_null($this->errMsg))
        {
            $this->errMsg = array();
        }
        
        // 言語リソース取得
        $container =& DIContainerFactory::getContainer();
        $filterChain =& $container->getComponent('FilterChain');
        $smartyAssign =& $filterChain->getFilterByName('SmartyAssign');
        
        // 補間する
        array_push($this->errMsg, vsprintf($smartyAssign->getLang($key), $params));
        
        // Viewに渡す処理
        $container =& DIContainerFactory::getContainer();
        $request =& $container->getComponent("Request");
        $request->setParameter("errMsg", $this->errMsg);
    }
    
    /**
     * 各アクションは下記メソッドをオーバーライドすること
     */

    /**
     * Execute application
     * 処理実行関数
     */
    protected function executeApp(){}
    
    /**
     * Process before transaction
     * トランザクション開始前処理
     */
    protected function beforeTrans(){}
    
    /**
     * Process after transaction
     * トランザクション終了後処理
     */
    protected function afterTrans(){}
    
    /**
     * Process before execute
     * 前処理
     */
    protected function preExecute(){}
    
    /**
     * Process after execute
     * 後処理
     */
    protected function postExecute(){}
    
    /**
     * Transaction commit processing: Called at the time the transaction is complete, by default is carried out only commit processing of the database.
     *                                If you want to add the processing at the time of transaction completion, and overridden in a derived class side, to add a processing
     * トランザクションのコミット処理：トランザクション完了時に呼び出され、デフォルトではデータベースのコミット処理のみ実施する。
     *                      トランザクション完了時の処理を追加したい場合、派生クラス側でオーバーライドし、処理を追加する
     * @throws AppException
     */
    protected function completeTrans(){
        $this->infoLog("Commit SQL.", __FILE__, __CLASS__, __LINE__);
        $this->Db->CompleteTrans();
        if($this->Db->HasFailedTrans())
        {
            $this->infoLog("Failed commit trance.", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Failed commit trance.");
        }
    }
    
    /**
     * Output exception log
     * Exception時のログ出力
     * 
     * @param Exception $e exception object エクセプションクラス
     * @param string $filePath file path エラー発生ファイルパス
     * @param string $className class name エラー発生クラス名
     * @param string $lineNo line number エラー発生行数
     */
    final function exeptionLog(Exception $e, $filePath, $className, $lineNo)
    {
        $this->Logger->errorLog($e->__toString(), $filePath, $className, $lineNo);
    }
    
    /**
     * Output fatal log
     * fatalレベル以上のログを出力
     * 
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path エラー発生ファイルパス
     * @param string $className class name エラー発生クラス名
     * @param string $lineNo line number エラー発生行数
     */
    final function fatalLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->fatalLog($message, $filePath, $className, $lineNo);
    }
    
    /**
     * Output error log
     * errorレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path エラー発生ファイルパス
     * @param string $className class name エラー発生クラス名
     * @param string $lineNo line number エラー発生行数
     */
    final function errorLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->errorLog($message, $filePath, $className, $lineNo);
    }
    
    /**
     * Output warning log
     * warnレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path エラー発生ファイルパス
     * @param string $className class name エラー発生クラス名
     * @param string $lineNo line number エラー発生行数
     */
    final function warnLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->warnLog($message, $filePath, $className, $lineNo);
    }
    
    /**
     * Output info log
     * infoレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path エラー発生ファイルパス
     * @param string $className class name エラー発生クラス名
     * @param string $lineNo line number エラー発生行数
     */
    final function infoLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->infoLog($message, $filePath, $className, $lineNo);
    }
    
    /**
     * Output debug log
     * debugレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path エラー発生ファイルパス
     * @param string $className class name エラー発生クラス名
     * @param string $lineNo line number エラー発生行数
     */
    final function debugLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->debugLog($message, $filePath, $className, $lineNo);
    }
    
    /**
     * Output trace log
     * traceレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path エラー発生ファイルパス
     * @param string $className class name エラー発生クラス名
     * @param string $lineNo line number エラー発生行数
     */
    final function traceLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->traceLog($message, $filePath, $className, $lineNo);
    }
}
?>