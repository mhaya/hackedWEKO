<?php
/**
 * WEKO logger class
 * WEKOロガークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: AppLogger.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * WEKO logger class
 * WEKOロガークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class AppLogger
{
    /**
     * Initialize
     * 初期化
     */
    private function initialize()
    {
        // もともとnc2/maple/generate/script/generate.phpにLOG_LEVELは宣言されているが
        // NC2上でLogger_SimpleFileを利用するとUse of undefined constant LOG_LEVELとなる
        // このため、未定義の場合のみ本クラスで宣言するようにしている
        // mapleで定義されているログレベルの定数は下記の通り
        // define('LEVEL_SQL', 2048+7);
        // define('LEVEL_FATAL', 2048+6);
        // define('LEVEL_ERROR', 2048+5);
        // define('LEVEL_WARN',  2048+4);
        // define('LEVEL_INFO',  2048+3);
        // define('LEVEL_DEBUG', 2048+2);
        // define('LEVEL_TRACE', 2048+1);
        
        if(!defined('LOG_LEVEL'))
        {
            define('LOG_LEVEL', LEVEL_WARN);
        }
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
    public function fatalLog($message, $filePath, $className, $lineNo)
    {
        $this->initialize();
        
        $log =& LogFactory::getLog("simpleFile");
        $log->fatal("$className,$lineNo,".session_id().",".$message);
        
        $log =& LogFactory::getLog();
        $log->fatal("$message in file $filePath line $lineNo");
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
    public function errorLog($message, $filePath, $className, $lineNo)
    {
        $this->initialize();
        
        $log =& LogFactory::getLog("simpleFile");
        $log->error("$className,$lineNo,".session_id().",".$message);
        
        $log =& LogFactory::getLog("");
        $log->error("$message in file $filePath line $lineNo");
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
    public function warnLog($message, $filePath, $className, $lineNo)
    {
        $this->initialize();
        
        $log =& LogFactory::getLog("simpleFile");
        $log->warn("$className,$lineNo,".session_id().",".$message);
        
        $log =& LogFactory::getLog("");
        $log->warn("$message in file $filePath line $lineNo");
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
    public function infoLog($message, $filePath, $className, $lineNo)
    {
        $this->initialize();
        
        $log =& LogFactory::getLog("simpleFile");
        $log->info("$className,$lineNo,".session_id().",".$message);
        
        $log =& LogFactory::getLog("");
        $log->info("$message in file $filePath line $lineNo");
    }
    
    /**
     * Output debug log
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path エラー発生ファイルパス
     * @param string $className class name エラー発生クラス名
     * @param string $lineNo line number エラー発生行数
     */
    public function debugLog($message, $filePath, $className, $lineNo)
    {
        $this->initialize();
        
        $log =& LogFactory::getLog("simpleFile");
        $log->debug("$className,$lineNo,".session_id().",".$message);
        
        $log =& LogFactory::getLog("");
        $log->debug("$message in file $filePath line $lineNo");
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
    public function traceLog($message, $filePath, $className, $lineNo)
    {
        $this->initialize();
        
        $log =& LogFactory::getLog("simpleFile");
        $log->trace("$className,$lineNo,".session_id().",".$message);
        
        $log =& LogFactory::getLog("");
        $log->trace("$message in file $filePath line $lineNo");
    }
}
?>
