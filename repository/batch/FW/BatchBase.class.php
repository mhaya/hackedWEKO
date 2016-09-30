<?php

/**
 * Batch base Class
 * バッチ基底クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BatchBase.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Batch exit code const class
 * バッチ終了コード定義クラス
 */
require_once(WEBAPP_DIR."/modules/".MODULE_NAME."/batch/FW/BatchExitCodes.class.php");
/**
 * Batch DB class
 * バッチ用DBクラス
 */
require_once(WEBAPP_DIR."/modules/".MODULE_NAME."/batch/FW/BatchDbObject.class.php");
/**
 * Batch progress Class
 * バッチ進捗クラス
 */
require_once(WEBAPP_DIR."/modules/".MODULE_NAME."/batch/FW/BatchProgress.class.php");
/**
 * Batch factory class
 * バッチ用ファクトリークラス
 */
require_once(WEBAPP_DIR."/modules/".MODULE_NAME."/batch/FW/BatchBusinessFactory.class.php");

/**
 * Batch base Class
 * バッチ基底クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class BatchBase
{
    // 基底変数

    /**
     * Session
     * セッション
     *
     * @var Session
     */
    private $Session = null;
    /**
     * DB Object for batch
     * バッチ用DBオブジェクト
     *
     * @var BatchDbObject
     */
    protected $Db = null;
    /**
     * Start date
     * 実行開始時刻
     *
     * @var string
     */
    protected $accessDate = null;
    /**
     * Execute time
     * 処理開始時刻
     *
     * @var string
     */
    protected $accessDate_datetime = null;
    /**
     * Logger Object
     * ロガーオブジェクト
     *
     * @var BatchLogger
     */
    protected $Logger = null;
    
    /**
     * Process ID
     * プロセスID
     *
     * @var int
     */
    protected $pid = null;
    /**
     * Batch name
     * バッチ名
     *
     * @var string
     */
    protected $batch_name = null;
    /**
     * Time out sec
     * タイムアウト秒
     *
     * @var int
     */
    private $timeout = null;
    /**
     * Batch table name
     * バッチ管理テーブル名
     *
     * @var string
     */
    private $tableName = null;

    /**
     * Start execute
     * 実行開始関数
     *
     * @param array  $argv argv 引数
     * @param string $ini  INI file name INIファイル名
     * @return int|bool exit code or false 終了コード/false(実行失敗)
     */
    final public function execute($argv, $ini) {
        // INIファイルの存在チェック
        if(!file_exists($ini)) {
            return BatchExitCodes::ERROR_INI_FILE;
        }
        
        // TODO:引数オプションの解釈
        $options = null;
        $options = $this->parseBatchOption($argv);
        
        // 基底初期化処理
        $ret = $this->initialize($options, $ini);
        if(!is_null($ret)) {
            echo "Initialize failed.\n";
            $this->finalize();
            return $ret;
        }
        
        // フラグ初期化
        $exitCode = BatchExitCodes::END_SUCCESS;
        $isLocked = false;
        
        try
        {
            // ロック開始
            // TODO:強制起動オプションがある
            $isLocked = $this->lock($argv, $options);
            
            $exitCode = $this->executeApp($options);
        }
        catch (Exception $ex)
        {
            // エラーログを出力
            $this->exeptionLog($ex, __FILE__, __CLASS__, __LINE__);
            // 終了コード
            $exitCode = $ex->getCode();
            if($exitCode == 0) {
                $exitCode = BatchExitCodes::END_ERROR;
            }
        }
        
        // ロック解除
        if($isLocked === true) {
            $this->unlock($exitCode);
        }
        // オブジェクトの解放処理
        $this->finalize();
        
        return $exitCode;
    }
    
    /**
     * Actual process
     * 実処理
     *
     * @param array $options execute options 実行オプション
     * @return int exit code 終了コード
     * @throws Exception
     */
    private function executeApp($options) {
        // Action開始前処理
        $progress = $this->startProcess($options);
        
        // 実行処理
        try
        {
            while(is_null($progress->exitCode))
            {
                // 実行時刻更新
                $this->updateDate();
                
                // メイン処理実行
                $progress = $this->executeStep($progress);
                
                // 実行ステータス更新
                $ret = $this->updateBatchStatus($progress);
                if($ret === false) {
                    $progress->exitCode = BatchExitCodes::ERROR_UPDATE_PROCESS;
                }
            }
        }
        catch(Exception $ex)
        {
            // 二次例外が出た場合は無視する
            $this->tryEndProcess($progress);
            throw $ex;
        }
        
        // Actionの終了処理
        $this->endProcess($progress);
        
        return $progress->exitCode;
    }

    /**
     * End process
     * 終了処理
     *
     * @param BatchProgress $progress Batch progress object バッチ進捗オブジェクト
     */
    private function tryEndProcess($progress) {
        try
        {
            $this->endProcess($progress);
        }
        catch(Exception $ex)
        {
            $this->warnLog("Error and execute endProcess failed.", __CLASS__, __FILE__, __LINE__);
        }
    }
    
    /**
     * Before start processing
     * 実行開始前処理
     *
     * @param array $options execute options 実行オプション
     * @return BatchProgress $progress Batch progress object バッチ進捗オブジェクト
     */
    protected function startProcess($options) { return new BatchProgress($options); }

    /**
     * After start processing
     * 実行終了後
     *
     * @param BatchProgress $progress Batch progress object バッチ進捗オブジェクト
     */
    protected function endProcess($progress) {}

    /**
     * Main process
     * メイン処理
     *
     * @param BatchProgress $progress Batch progress object バッチ進捗オブジェクト
     * @return BatchProgress $progress Batch progress object バッチ進捗オブジェクト
     */
    protected function executeStep($progress) { return $progress; }

    /**
     * Process after initialize
     * 書記が実行後処理
     *
     * @param array $options execute options 実行オプション
     * @param $ini INI file name INIファイル名
     * @return null
     */
    protected function onInitialized($options, $ini) {return null;}
    
    /**
     * Initialize Process
     * 初期化処理
     *
     * @param array $options execute options 実行オプション
     * @param $ini INI file name INIファイル名
     * @return int exit code 終了コード
     */
    final private function initialize($options, $ini) {
        // INI解釈
        $iniData = parse_ini_file($ini, true);
        
        // バッチ名の設定
        $this->batch_name = $iniData["execute"]["batch_name"];
        if(array_key_exists("batch_name", $options) && strlen($options["batch_name"]) > 0) {
            $this->batch_name = $options["batch_name"];
        }
        
        // プロセスIDを取得する
        $this->pid = getmypid();
        if($this->pid === false) {
            return BatchExitCodes::ERROR_UNKNOWN_PID;
        }
        
        // タイムアウト値の設定
        $this->timeout = $iniData["execute"]["timeout"];
        
        // バッチ管理テーブル名の設定
        $this->tableName = $iniData["db"]["table_name"];
        
        // セッションと処理開始時刻を設定する
        $this->Session = null; // バッチ、ビジネスではセッションは使用不可
        $this->updateDate();
        
        // DBに接続する
        $this->Db = BatchDbObject::openConnect(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
        if(is_null($this->Db)) {
            return BatchExitCodes::ERROR_SQL;
        }
        // 管理テーブルチェック
        if(!$this->checkBatchProcessTable()){
            return BatchExitCodes::ERROR_SQL;
        }
        
        // ファクトリーを初期化する
        BatchBusinessFactory::initialize($this->Session, $this->Db, $this->accessDate, $iniData["business"]);
        if(get_parent_class(BusinessFactory::getFactory()) !== "BusinessFactory") {
            return BatchExitCodes::ERROR_GET_FACTORY;
        }
        
        // ロガーを初期化する
        $this->Logger = BatchBusinessFactory::getFactory()->logger;
        if(is_null($this->Logger)) {
            return BatchExitCodes::ERROR_GET_LOGGER;
        }
        if(strlen($iniData["logger"]["log_level"]) > 0) {
            $this->Logger->setLogLevel($iniData["logger"]["log_level"]);
        }
        // ログファイル名が設定されていれば出力先を設定する（無ければ標準出力となる）
        if(array_key_exists("log_file", $iniData["logger"]) && strlen($iniData["logger"]["log_file"])) {
            $this->Logger->setLogFile(WEBAPP_DIR."/logs/weko/batch/".$iniData["logger"]["log_file"]);
        }
        
        return $this->onInitialized($options, $ini);
    }
    
    /**
     * Finalize
     * 終了処理
     */
    final private function finalize() {
        // ビジネスロジック生成クラス終了処理
        $businessFactory = BusinessFactory::getFactory();
        if(isset($businessFactory)) {
            $businessFactory->uninitialize();
        }
        
        // DBオブジェクトの解放
        if(!is_null($this->Db)){
            $this->Db->closeConnect();
            unset($this->Db);
        }
    }
    
    /**
     * Check existing batch table
     * バッチテーブル存在確認
     *
     * @param string $dbName DB name DB名
     * @param string $tbName table name テーブル名
     * @return bool true exists バッチテーブルが存在する
     *               false not exists バッチテーブルが存在しない
     */
    final private function tableExists($dbName, $tbName)
    {
        $sql = "SHOW TABLES FROM ".$dbName." LIKE '{".$tbName."}';";
        $result = $this->Db->execute($sql);
        if(count($result)==0){
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Check batch table status
     * バッチテーブル状態チェック
     *
     * @return bool true no problem 問題無し
     *               false exists problem 問題あり
     */
    final private function checkBatchProcessTable()
    {
        if (!$this->tableExists(DB_NAME, $this->tableName)){
            return false;
        }
        
        // TODO: 管理テーブルのカラムチェック
        
        return true;
    }
    
    /**
     * Get process lock
     * ロック取得処理
     *
     * @param array $argv argv 引数
     * @return bool true got lock ロック成功
     * @throws Exception
     */
    final private function lock($argv) {
        $result = null;
        // 実行コマンド
        $command = "";
        for($ii = 0; $ii < count($argv); $ii++) { $command .= $argv[$ii]." "; }
        
        // バッチのレコード確認
        $query = "SELECT batch_name FROM {".$this->tableName."} ".
                 "WHERE batch_name = ? ;";
        $params = array();
        $params[] = $this->batch_name; // バッチ名;
        $checkRecord = $this->Db->execute($query, $params);
        
        if(count($checkRecord) > 0) {
            // レコードが存在している場合はUPDATEを行ってロック取得
            // 実行ステータスが「未実行」または「実行中だが最終更新時間がタイムアウト値を過ぎている」場合はロックを取得する
            $query = "UPDATE {".$this->tableName."} ".
                     "SET process_id = ?, ".
                          "status = ?, ".
                          "command = ?, ".
                          "current_progress = ?, ".
                          "max_length = ?, ".
                          "exit_code = ?, ".
                          "start_date = ?, ".
                          "end_date = ?, ".
                          "mod_date = ? ".
                     "WHERE batch_instance_no = ? ".
                     "AND batch_name = ? ".
                     "AND (status = ? OR ( UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(mod_date) ) > ?) ;";
            $params = array();
            // SET
            $params[] = $this->pid;                 // プロセスID
            $params[] = 1;                          // 実行ステータス
            $params[] = $command;                   // 実行コマンド
            $params[] = 0;                          // 処理番号
            $params[] = "";                         // 処理最大件数
            $params[] = null;                       // 終了コード
            $params[] = $this->accessDate_datetime; // バッチ開始時刻
            $params[] = "";                         // バッチ終了時刻
            $params[] = $this->accessDate;          // 最終更新時刻
            // WHERE
            $params[] = 0;                          // バッチNo
            $params[] = $this->batch_name;          // バッチ名
            $params[] = 0;                          // 実行ステータス
            $params[] = $this->accessDate_datetime; // 現在時刻
            $params[] = $this->timeout;             // タイムアウト値
        } elseif(count($checkRecord) == 0) {
            // バッチレコード挿入
            $query = "INSERT INTO {".$this->tableName."} ".
                     "(batch_instance_no, batch_name, process_id, status, command, current_progress, max_length, exit_code, start_date, end_date, ins_date, mod_date) ".
                     "VALUES ".
                     "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ;";
            $params = array();
            $params[] = 0;                          // バッチNo
            $params[] = $this->batch_name;          // バッチ名
            $params[] = $this->pid;                 // プロセスID
            $params[] = 1;                          // 実行ステータス
            $params[] = $command;                   // 実行コマンド
            $params[] = 0;                          // 処理番号
            $params[] = 0;                          // 処理最大件数
            $params[] = NULL;                       // 終了コード
            $params[] = $this->accessDate_datetime; // 実行開始日時
            $params[] = "";                         // 実行終了日時
            $params[] = $this->accessDate;          // レコード作成日時
            $params[] = $this->accessDate;          // レコード更新日時
        }
        // ロック取得に失敗(=他プロセスで実行中でレコードが更新できなかった)
        $result = $this->Db->execute($query, $params);
        if($this->Db->getAffectedRows() == 0) {
            $this->warnLog("Get lock failed by multiple execution.", __FILE__, __CLASS__, __LINE__);
            throw new Exception("Get lock failed by multiple execution.", BatchExitCodes::END_MULTI_EXECUTION);
        }
        return true;
    }
    
    /**
     * Unlock process lock
     * ロック解除
     *
     * @param int $exitCode exit code 終了コード
     */
    final private function unlock($exitCode) {
        $query = "UPDATE {".$this->tableName."} ".
            "SET status = ?, ".
            "exit_code = ?, ".
            "end_date = ?, ".
            "mod_date = ? ".
            "WHERE batch_instance_no = ? ".
            "AND batch_name = ? ".
            "AND process_id = ? ;";
        $params = array();
        // SET
        $params[] = 0;                          // 実行ステータス
        $params[] = $exitCode;                  // 終了コード
        $params[] = $this->accessDate_datetime; // バッチ終了時刻
        $params[] = $this->accessDate;          // 最終更新時刻
        // WHERE
        $params[] = 0;                          // バッチNo
        $params[] = $this->batch_name;          // バッチ名
        $params[] = $this->pid;                 // 実行ステータス
        $result = $this->Db->execute($query, $params);
    }
    
    /**
     * 実行状態テーブル更新
     * 
     * @param  BatchDbObject $progress batch process object バッチ進捗オブジェクト
     * @return bool true update success 更新成功
     *               false update failed 更新失敗
     */
    protected function updateBatchStatus($progress) {
        $query = "UPDATE {".$this->tableName."} ".
            "SET status = ?, ".
            "exit_code = ?, ".
            "current_progress = ?, ".
            "max_length = ?, ".
            "end_date = ?, ".
            "mod_date = ? ".
            "WHERE batch_instance_no = ? ".
            "AND batch_name = ? ".
            "AND process_id = ? ;";
        $params = array();
        $params[] = (is_null($progress->exitCode)) ? 1 : 0; // 実行ステータス(終了コードの有無で1(実行中),0(終了))
        $params[] = $progress->exitCode;   // 終了コード
        $params[] = $progress->current;    // カレント
        $params[] = $progress->max_length; // カレント
        $params[] = "";                    // バッチ終了時刻
        $params[] = $this->accessDate;     // 最終更新時刻
        // WHERE
        $params[] = 0;                          // バッチNo
        $params[] = $this->batch_name;     // バッチ名
        $params[] = $this->pid;            // 実行ステータス
        $result = $this->Db->execute($query, $params);
        
        return $result;
    }

    /**
     * Update process start date
     * 処理開始時間更新
     */
    private function updateDate() {
        $this->accessDate_datetime = date('Y-m-d H:i:s'); // YYYY-MM-DD HH:ii:ss
        $this->accessDate = $this->accessDate_datetime.".000"; // YYYY-MM-DD HH:ii:ss.000。WEKO互換
    }

    /**
     * Parse argument
     * 引数解釈処理
     *
     * @param array $argv argv 引数
     * @return array $options options 解釈済引数
     */
    private function parseBatchOption($argv) {
        $options = array();
        for($ii = 0; $ii < count($argv); $ii++) {
            if(preg_match("/^--/", $argv[$ii])) {
                $tmpArgv = explode("=", $argv[$ii]);
                if(count($tmpArgv) != 2) {
                    $options = $argv[$ii];
                } else {
                    $options[preg_replace("/^--/", "", $tmpArgv[0])] = $tmpArgv[1];
                }
            } else {
                $options[] = $argv[$ii];
            }
        }

        return $options;
    }
    
    /**
     * Output exception log
     * Exception時のログ出力
     * 
     * @param Exception $e Exception object 例外オブジェクト
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     */
    final public function exeptionLog(Exception $e, $filePath, $className, $lineNo)
    {
        $this->Logger->errorLog($e->__toString(), $filePath, $className, $lineNo, $this->batch_name, $this->pid);
    }
    
    /**
     * Output fatal log
     * fatalレベル以上のログを出力
     * 
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     */
    final public function fatalLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->fatalLog($message, $filePath, $className, $lineNo, $this->batch_name, $this->pid);
    }
    
    /**
     * Output error log
     * errorレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     */
    final public function errorLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->errorLog($message, $filePath, $className, $lineNo, $this->batch_name, $this->pid);
    }
    
    /**
     * Output warning log
     * warnレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     */
    final public function warnLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->warnLog($message, $filePath, $className, $lineNo, $this->batch_name, $this->pid);
    }
    
    /**
     * Output info log
     * infoレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     */
    final public function infoLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->infoLog($message, $filePath, $className, $lineNo, $this->batch_name, $this->pid);
    }
    
    /**
     * Output debug log
     * debugレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     */
    final public function debugLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->debugLog($message, $filePath, $className, $lineNo, $this->batch_name, $this->pid);
    }
    
    /**
     * Output trace log
     * traceレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     */
    final public function traceLog($message, $filePath, $className, $lineNo)
    {
        $this->Logger->traceLog($message, $filePath, $className, $lineNo, $this->batch_name, $this->pid);
    }
}
?>