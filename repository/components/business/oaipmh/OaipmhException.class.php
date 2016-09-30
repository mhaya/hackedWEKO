<?php

/**
 * Expanded exception class for OAI-PMH output
 * OAI-PMH出力用拡張例外クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: OaipmhException.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * 例外基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/AppException.class.php';

/**
 * Expanded exception class for OAI-PMH output
 * OAI-PMH出力用拡張例外クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class OaipmhException extends AppException 
{
    /**
     * Query execution time of the error message
     * クエリ実行時のエラーメッセージ
     *
     * @var string
     */
    protected $oaipmhErrorMsg = "";
    
    /**
     * Error code at the time of query execution
     * クエリ実行時のエラーコード
     *
     * @var string
     */
    protected $oaipmhErrorCode = "";
    
    /**
     * Constructor (OAI-PMH output at the time of the error message, enter the code)
     * コンストラクタ(OAI-PMH出力時のエラーメッセージ、コードを入力する)
     *
     * @param string $dbErrorMsg Query execution time of the error message OAI-PMH出力時のエラーメッセージ
     * @param string $dbErrorCode Error code at the time of query execution OAI-PMH出力時のエラーコード
     * @param string $message Exception message to throw スローする例外メッセージ
     * @param int $code Exception code 例外コード
     * @param Exception $previous Inner exception インナーエクセプション
     */
    public function __construct($oaipmhErrorMsg, $oaipmhErrorCode, $message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        
        $this->oaipmhErrorMsg = $oaipmhErrorMsg;
        $this->oaipmhErrorCode = $oaipmhErrorCode;
    }
    
    /**
     * To get the OAI-PMH output at the time of the error message
     * OAI-PMH出力時のエラーメッセージを取得する
     *
     * @return string
     */
    public function getOaipmhErrorMsg(){
        return $this->oaipmhErrorMsg;
    }
    
    /**
     * To get the error code at the time of OAI-PMH output
     * OAI-PMH出力時のエラーコードを取得する
     *
     * @return string
     */
    public function getOaipmhErrorCode(){
        return $this->oaipmhErrorCode;
    }
}
?>