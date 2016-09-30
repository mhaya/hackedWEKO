<?php

/**
 * Sitelicense usage statistics batch progress Class
 * サイトライセンス利用統計バッチ進捗クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: SitelicenseUsagestatisticsProgress.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Sitelicense usage statistics batch progress Class
 * サイトライセンス利用統計バッチ進捗クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class SitelicenseUsagestatisticsProgress extends BatchProgress
{
    /**
     * Aggregate all matter completion flag
     * 集計全件完了フラグ
     *
     * @var bool
     */
    public $aggregateCompleteFlag = false;

    // 集計用
    /**
     * Sitelicense ID list
     * サイトライセンスIDリスト
     *
     * @var array array[$ii]
     */
    public $sitelicenseIdList = array();
    /**
     * Completion of processing site license number
     * 処理完了サイトライセンス件数
     *
     * @var int
     */
    public $aggregateSitelicenseCnt = 0;
    /**
     * Aggregate target date list
     * 集計対象年月リスト
     *
     * @var array array[$ii]
     */
    public $dateList = array();
    /**
     * Aggregate completion number
     * 集計完了件数
     *
     * @var int
     */
    public $aggregateDateCnt = 0;

    // 送信用
    /**
     * Transmission target list
     * 送信対象リスト
     *
     * @var array array[$ii][("request_id"|)"organization_id"|"mail_no"|"mail_address"|"year"|"month"|"aggregate_range"|"send_status"|"start_date"|"send_date"|"request_password"]
     */
    public $sendList = array();
    /**
     * Transmission completion number
     * 送信完了件数
     *
     * @var int
     */
    public $sendCompleteCnt = 0;
    /**
     * Language
     * 表示言語
     *
     * @var string
     */
    public $language = "";
    /**
     * PHP command path
     * PHPコマンド実行パス
     *
     * @var string
     */
    public $phpPath = "";

    /**
     * SitelicenseUsagestatisticsProgress constructor.
     * コンストラクタ
     *
     * @param array $options options 実行オプション
     *               array["language"|"from"|"to"|"retry"|"requestid"]
     * @param array $sitelicenseIds sitelicense id サイトライセンスID配列
     *                               array[$ii]
     * @param array $dateList date range list 集計・送信日時範囲配列
     *                         array[$ii]
     * @param array $sendList send to list 送信先配列
     *                         array[$ii][("request_id"|)"organization_id"|"mail_no"|"mail_address"|"year"|"month"|"aggregate_range"|"send_status"|"start_date"|"send_date"|"request_password"]
     * @param string $language language 表示言語
     * @param string $phpPath php command path PHPコマンド実行パス
     */
    public function __construct($options, $sitelicenseIds, $dateList, $sendList, $language, $phpPath) {
        // 集計用
        $this->sitelicenseIdList = $sitelicenseIds;
        $this->dateList = $dateList;
        // 送信用
        $this->sendList = $sendList;
        // 言語
        $this->language = $language;
        // PHP Path
        $this->phpPath = $phpPath;
        
        parent::__construct($options);
    }
}

?>