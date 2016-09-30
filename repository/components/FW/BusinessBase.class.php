<?php

/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BusinessBase.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Expanded exception class for DB
 * DB用拡張例外クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/DbException.class.php';

/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
abstract class BusinessBase
{
    /**
     * Logger object
     * ロガーコンポーネントを受け取る
     *
     * @var object
     */
    public $Logger = null;

    /**
     * DB object
     * Dbコンポーネントを受け取る
     *
     * @var object
     */
    public $Db = null;
    
    /**
     * Access date
     * アクセス日時
     *
     * @var string
     */
    public $accessDate = null;
    
    /**
     * Access user ID (Not login is 0)
     * アクセスユーザーのユーザーID(未ログインは"0")
     * 
     * @var string
     */
    protected $user_id = "";
    
    /**
     * Login user handle name
     * ログインユーザーのハンドル名
     * 
     * @var string
     */
    protected $handle = "";
    
    /**
     * Access user base authority
     * アクセスユーザーのベース権限
     *
     *   管理者 => _ROLE_AUTH_ADMIN
     *   主担 => _ROLE_AUTH_CHIEF
     *   モデレータ => _ROLE_AUTH_MODERATE
     *   一般 => _ROLE_AUTH_GENERAL
     *   ゲスト => _ROLE_AUTH_GUEST
     *   事務局 => _ROLE_AUTH_CLERK
     *   その他 => _ROLE_AUTH_OTHER
     *   未ログイン => 
     * 
     * @var int
     */
    protected $auth_id = null;
    
    /**
     * Initialize
     * 初期化
     *
     * @param object $db DB object DBオブジェクト
     * @param string $accessDate access date アクセス日時
     * @param string $user_id user id ユーザーID
     * @param string $handle handle name ユーザーハンドル名
     * @param int    $auth_id authority id ユーザー権限
     * @param object $logger logger object ロガーオブジェクト
     */
    final function initializeBusiness($db, $accessDate, $user_id, $handle, $auth_id, $logger)
    {
        $this->Db = $db;
        $this->accessDate = $accessDate;
        $this->user_id = $user_id;
        $this->handle = $handle;
        $this->auth_id = $auth_id;
        $this->Logger = $logger;
        $this->onInitialize();
    }
    
    /**
     * Finalize
     * 終了処理
     */
    final function finalizeBusiness()
    {
        $this->onFinalize();
    }
    
    /**
     * Process when create instance
     * インスタンス生成時に実行する処理
     */
    protected function onInitialize(){}
    
    /**
     * Process when destroy instance
     * インスタンス破棄時に実行する処理
     */
    protected function onFinalize(){}
    
    /**
     * Output exception log
     * Exeption時のログ出力
     *
     * @param Exception $e exception object 例外オブジェクト
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
    
    /**
     * Execute query with transaction
     * トランザクションを使用して処理を実行する
     *
     * @param string $funcName function name メソッド名
     * @param array $args method argument メソッドの引数の配列(参照渡ししたい引数は呼び出し側で明示すること)
     * @return mixed $ret result 呼び出しメソッドの戻り値
     * @throws Exception 呼び出しメソッドの例外をリスローする
     */
    final public function executeTrans($funcName, $args=array()){
        $ret = null;
        try {
            $this->Db->StartTrans();
            $ret = call_user_func_array(array($this, $funcName), $args);
            $this->Db->CompleteTrans();
        } catch (Exception $e) {
            $this->Db->FailTrans();
            $this->errorLog("Rollback transaction.", __FILE__, __CLASS__, __LINE__);
            throw $e;
        }
        
        return $ret;
    }
    
    /**
     * Run the universally to perform processing during query execution(When performing the execution of SQL in a class that inherits from the class utilize this function)
     * クエリ実行時に普遍的に行う処理を実行する(本クラスを継承するクラス内でSQLの実行を行う場合、本関数を利用する)
     *
     * @param string $query Query to run 実行するクエリ
     * @param array $params Parameters of the query クエリのパラメータ
     * @return array Result 実行結果
     *                array[$ii]["column name"]
     * @throws DbException
     */
    protected function executeSql($query, $params = array()){
        $this->debugLog("[". __FUNCTION__. "] query: ". $query, __FILE__, __CLASS__, __LINE__);
        $this->debugLog("[". __FUNCTION__. "] params: ". print_r($params, true), __FILE__, __CLASS__, __LINE__);
        $result = $this->Db->execute($query, $params);
        if($result === false){
            // 例外
            $exception = new DbException($this->Db->ErrorMsg(), $this->Db->ErrorNo(), "[Failed execute sql]", 1);
            $exception->addError("repository_error_query_execute", $this->Db->ErrorNo());
            $this->errorLog("[". __FUNCTION__. "] errorMsg: ". $exception->getDbErrorMsg(), __FILE__, __CLASS__, __LINE__);
            $this->errorLog("[". __FUNCTION__. "] errorCode: ". $exception->getDbErrorCode(), __FILE__, __CLASS__, __LINE__);
            $this->errorLog("[". __FUNCTION__. "] query: ". $query, __FILE__, __CLASS__, __LINE__);
            $this->errorLog("[". __FUNCTION__. "] params: ". print_r($params, true), __FILE__, __CLASS__, __LINE__);
            throw $exception;
        }
        return $result;
    }
    
    /**
     * Read the specified SQL file, run the SQL that is described in the SQL file
     * 指定されたSQLファイルを読み込み、SQLファイル内に記載されたSQLを実行する
     *
     * @param string $filePath File path of the SQL file to be read 読込むSQLファイルのファイルパス
     * @param array $params Parameter array to pass at the time of query execution クエリ実行時に渡すパラメータ配列
     *                      array[$ii]
     * @return array Result 実行結果
     *                array[$ii]["column name"]
     */
    protected function executeSqlFile($filePath, $params = array()){
        $query = $this->loadSql($filePath);
        return $this->executeSql($query, $params);
    }
    
    /**
     * Read the specified SQL file and returns a query statement:If you want to run many times the SQL statement to override in the inheritance destination, add the processing
     * 指定されたSQLファイルを読み込みクエリ文を返す：SQL文を何度も実行する場合、継承先でオーバーライドし、処理を追加する
     *
     * @param string $filePath File path of the SQL file to be read 読込むSQLファイルのファイルパス
     * @return string SQL statement SQL文
     * @throws AppException
     */
    protected function loadSql($filePath){
        // ファイルの内容を全て読み込む
        if(file_exists($filePath)){
            $query = file_get_contents($filePath);
            if($query === false){
                throw new AppException("[Failed read file] filePath: ". $filePath);
            }
        } else {
            throw new AppException("[File is not exists] filePath: ". $filePath);
        }
        // テーブルの接頭辞を置換する
        $query = str_replace("%%DATABASE_PREFIX%%", DATABASE_PREFIX, $query);
        
        // ロードしたクエリ文を返す
        return $query;
    }
}
?>