<?php

/**
 * Batch logger Class
 * バッチロガークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BatchLogger.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Batch logger Class
 * バッチロガークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class BatchLogger
{
    /**
     * Log output level
     * ログ出力レベル
     *
     * @var int
     */
    private $log_level = LEVEL_WARN;
    /**
     * Log file path
     * ログ出力先ファイルパス
     *
     * @var string
     */
    private $log_file = null;

    /**
     * Set log level
     * ログ出力レベル設定
     *
     * @param $log_level
     */
    public function setLogLevel($log_level) { $this->log_level = $log_level; }

    /**
     * Set log file
     * ログ出力先設定
     *
     * @param $log_file
     */
    public function setLogFile($log_file) { $this->log_file = $log_file; }

    /**
     * Output log
     * ログ出力
     *
     * @param int $log_level_int log level ログ出力レベル
     * @param string $log_level_str log level ログ出力レベル
     * @param string $message message ログメッセージ
     * @param string $filePath file path ログ出力先ファイルパス
     * @param string $className class name 出力元クラス名
     * @param int $lineNo line number 出力元行番号
     * @param string $batch batch name 出力元バッチ名
     * @param int $pid process ID プロセスID
     */
    private function output($log_level_int, $log_level_str, $message, $filePath, $className, $lineNo, $batch, $pid) {
        if($this->log_level <= $log_level_int) {
            $str = "[".date('Y-m-d H:i:s')."] ". 
                   "[".$log_level_str."] ". 
                   "[".$pid."] ". 
                   "[".$batch." -> ".$className.":".$lineNo."] ". 
                   $message;
            if(is_null($this->log_file)) {
                echo $str."\n";
            } else {
                $fp = fopen($this->log_file, "a");
                fwrite($fp, $str."\n");
                fclose($fp);
            }
        }
    }

    /**
     * Output fatal log
     * fatalレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     * @param string $batch batch name 発生バッチ名
     * @param int    $pid process ID プロセスID
     */
    public function fatalLog($message, $filePath, $className, $lineNo, $batch="", $pid="")
    {
        $this->output(LEVEL_FATAL, "fatal", $message, $filePath, $className, $lineNo, $batch, $pid);
    }
    
    /**
     * Output error log
     * errorレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     * @param string $batch batch name 発生バッチ名
     * @param int    $pid process ID プロセスID
     */
    public function errorLog($message, $filePath, $className, $lineNo, $batch="", $pid="")
    {
        $this->output(LEVEL_ERROR, "error", $message, $filePath, $className, $lineNo, $batch, $pid);
    }
    
    /**
     * Output warning log
     * warnレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     * @param string $batch batch name 発生バッチ名
     * @param int    $pid process ID プロセスID
     */
    public function warnLog($message, $filePath, $className, $lineNo, $batch="", $pid="")
    {
        $this->output(LEVEL_WARN, "warn", $message, $filePath, $className, $lineNo, $batch, $pid);
    }
    
    /**
     * Output info log
     * infoレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     * @param string $batch batch name 発生バッチ名
     * @param int    $pid process ID プロセスID
     */
    public function infoLog($message, $filePath, $className, $lineNo, $batch="", $pid="")
    {
        $this->output(LEVEL_INFO, "info", $message, $filePath, $className, $lineNo, $batch, $pid);
    }
    
    /**
     * Output debug log
     * debugレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     * @param string $batch batch name 発生バッチ名
     * @param int    $pid process ID プロセスID
     */
    public function debugLog($message, $filePath, $className, $lineNo, $batch="", $pid="")
    {
        $this->output(LEVEL_DEBUG, "debug", $message, $filePath, $className, $lineNo, $batch, $pid);
    }
    
    /**
     * Output trace log
     * traceレベル以上のログを出力
     *
     * @param string $message error message エラーメッセージ
     * @param string $filePath file path 発生ファイルパス
     * @param string $className class name 発生クラス名
     * @param string $lineNo line number 発生行
     * @param string $batch batch name 発生バッチ名
     * @param int    $pid process ID プロセスID
     */
    public function traceLog($message, $filePath, $className, $lineNo, $batch="", $pid="")
    {
        $this->output(LEVEL_TRACE, "trace", $message, $filePath, $className, $lineNo, $batch, $pid);
    }
}
?>
