<?php

/**
 * Expanded exception class for DB
 * DB用拡張例外クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: DbException.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR. '/modules/repository/components/FW/AppException.class.php';

/**
 * Expanded exception class for DB
 * DB用拡張例外クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class DbException extends AppException 
{
    /**
     * Query execution time of the error message
     * クエリ実行時のエラーメッセージ
     *
     * @var string
     */
    protected $dbErrorMsg = "";
    
    /**
     * Error code at the time of query execution
     * クエリ実行時のエラーコード
     *
     * @var int
     */
    protected $dbErrorCode = 0;
    
    /**
     * Redefine the exception, query execution time of the error message, so that code can also input
     * 例外を再定義し、クエリ実行時のエラーメッセージ、コードも入力できるようにする
     *
     * @param string $dbErrorMsg Query execution time of the error message クエリ実行時のエラーメッセージ
     * @param int $dbErrorCode Error code at the time of query execution クエリ実行時のエラーコード
     * @param string $message Exception message to throw スローする例外メッセージ
     * @param int $code Exception code 例外コード
     * @param Exception $previous Inner exception インナーエクセプション
     */
    public function __construct($dbErrorMsg, $dbErrorCode, $message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        
        $this->dbErrorMsg = $dbErrorMsg;
        $this->dbErrorCode = $dbErrorCode;
    }
    
    /**
     * To get the query run-time error message
     * クエリ実行時のエラーメッセージを取得する
     *
     * @return string
     */
    public function getDbErrorMsg(){
        return $this->dbErrorMsg;
    }
    
    /**
     * To get the error code at the time of query execution
     * クエリ実行時のエラーコードを取得する
     *
     * @return int
     */
    public function getDbErrorCode(){
        return $this->dbErrorCode;
    }
}
?>