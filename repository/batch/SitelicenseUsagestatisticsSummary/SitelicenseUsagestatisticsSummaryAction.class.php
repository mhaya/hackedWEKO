<?php

/**
 * Aggregate sitelicense usage statistics mail class
 * サイトライセンス利用統計集計クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: SitelicenseUsagestatisticsSummaryAction.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Batch base Class
 * バッチ基底クラス
 */
require_once WEBAPP_DIR."/modules/repository/batch/FW/BatchBase.class.php";

/**
 * Aggregate sitelicense usage statistics mail class
 * サイトライセンス利用統計集計クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class SitelicenseUsagestatisticsSummaryAction extends BatchBase
{
    // エラーコード
    /**
     * Shortage argument error
     * 引数不足エラー
     */
    const ERROR_ARGUMENT_SHORTAGE = 210;
    /**
     * Date format error
     * 日時フォーマットエラー
     */
    const ERROR_DATE_FORMAT = 211;
    /**
     * Sitelicense ID format error
     * サイトライセンスIDフォーマットエラー
     */
    const ERROR_SITEID_FORMAT = 212;

    // 引数
    /**
     * Argument from date
     * 集計・送信開始日時引数
     */
    const ARGUMENT_START_DATE = "from";
    /**
     * Argument sitelicense ID
     * サイトライセンスID引数
     */
    const ARGUMENT_SITEID = "siteid";
    
    /**
     * Execute
     * 実行
     *
     * @param BatchProgress $progress batch progress object バッチ進捗オブジェクト
     * @return BatchProgress $progress batch progress object バッチ進捗オブジェクト
     */
    function executeStep($progress) {
        // 引数チェック
        if(!isset($progress->options[self::ARGUMENT_START_DATE]) || 
           !isset($progress->options[self::ARGUMENT_SITEID])) {
            $progress->exitCode = self::ERROR_ARGUMENT_SHORTAGE;
            return $progress;
        }
        // サイトライセンス機関IDの引数チェック
        if(!preg_match("/^[1-9]\d*$/", $progress->options["siteid"])) {
            $progress->exitCode = self::ERROR_SITEID_FORMAT;
            return $progress;
        }
        // 日付範囲の引数チェック
        if(!preg_match("/^\d{6}$/", $progress->options["from"])) {
            $progress->exitCode = self::ERROR_DATE_FORMAT;
            return $progress;
        }
        
        // 集計処理
        $aggregate = BusinessFactory::getFactory()->getBusiness("businessAggregatesitelicenseusagestatistics");
        $this->debugLog("SitelicenseId: ".$progress->options["siteid"].", year: ".substr($progress->options["from"], 0, 4).", month: ".substr($progress->options["from"], 4, 2) , __FILE__, __CLASS__, __LINE__);
        $aggregate->aggregateUsageStatistics($progress->options["siteid"], 
                                             substr($progress->options["from"], 0, 4), 
                                             substr($progress->options["from"], 4, 2)
                                            );
        
        // 終了処理
        $progress->exitCode = BatchExitCodes::END_SUCCESS;
        
        return $progress;
    }
}
?>