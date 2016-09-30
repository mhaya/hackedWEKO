<?php

/**
 * Send sitelicense usage statistics mail class
 * サイトライセンス利用統計メール送信クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: SitelicenseUsagestatisticsFeedbackMailAction.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Send sitelicense usage statistics mail class
 * サイトライセンス利用統計メール送信クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class SitelicenseUsagestatisticsFeedbackMailAction extends BatchBase
{
    // エラーコード
    /**
     * Shortage argument error
     * 引数不足エラー
     */
    const ERROR_ARGUMENT_SHORTAGE = 220;
    /**
     * Date format error
     * 日時フォーマットエラー
     */
    const ERROR_DATE_FORMAT = 221;
    /**
     * Sitelicense ID format error
     * サイトライセンスIDフォーマットエラー
     */
    const ERROR_SITEID_FORMAT = 222;
    /**
     * Communication socket error
     * 通信ソケットエラー
     */
    const ERROR_FAILED_SOCKET_OPEN = 223;

    // 引数
    /**
     * Argument from date
     * 集計・送信開始日時引数
     */
    const ARGUMENT_START_DATE = "from";
    /**
     * Argument to date
     * 集計・送信日時範囲引数
     */
    const ARGUMENT_DATE_RANGE = "to";
    /**
     * Argument sitelicense ID
     * サイトライセンスID引数
     */
    const ARGUMENT_SITE_ID = "siteid";
    /**
     * Argument mail number
     * メール通番引数
     */
    const ARGUMENT_MAIL_NO = "mailno";
    /**
     * Argument request ID
     * リクエストID引数
     */
    const ARGUMENT_REQUEST_ID = "requestid";
    /**
     * Argument language
     * 表示言語引数
     */
    const ARGUMENT_LANGUAGE = "language";
    
    /**
     * Wait time of retrying socket open (sec)
     * ソケットオープン再試行時の待ち時間 (秒)
     *
     * @var int
     */
    const SOCKOPEN_WAIT_SEC_TIME_RETRY = 1;
    /**
     * Try count of socket open
     * ソケットオープンの試行回数
     *
     * @var int
     */
    const SOCKOPEN_TRY_COUNT = 3;

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
           !isset($progress->options[self::ARGUMENT_DATE_RANGE]) ||
           !isset($progress->options[self::ARGUMENT_SITE_ID]) ||
            !isset($progress->options[self::ARGUMENT_MAIL_NO])) {
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

        // 機関指定送信の場合(=リクエストIDが存在すれば）
        $request_id = 0;
        if(isset($progress->options[self::ARGUMENT_REQUEST_ID])) {
            $request_id = $progress->options[self::ARGUMENT_REQUEST_ID];
        }
        // メール本文言語（指定があれば）
        $language = "";
        if(array_key_exists(self::ARGUMENT_LANGUAGE, $progress->options) && strlen($progress->options[self::ARGUMENT_LANGUAGE]) > 0) {
            $language = $progress->options[self::ARGUMENT_LANGUAGE];
        }

        // 送信処理
        $result = $this->sendRequest($progress->options[self::ARGUMENT_SITE_ID],
                                     $progress->options[self::ARGUMENT_MAIL_NO],
                                     $progress->options[self::ARGUMENT_START_DATE],
                                     $progress->options[self::ARGUMENT_DATE_RANGE],
                                     $request_id,
                                     $language);
        // 終了処理
        if(!$result) {
            $progress->exitCode = self::ERROR_FAILED_SOCKET_OPEN;
        } else {
            $progress->exitCode = BatchExitCodes::END_SUCCESS;
        }

        return $progress;
    }

    /**
     * Send request for sending sitelicense mail
     * サイトライセンスメール送信リクエストを送信する
     *
     * @param int    $sitelicense_id sitelicense ID       サイトライセンス機関ID
     * @param int    $mail_no        mail number          メール通番
     * @param string $start_date     aggregate start date 集計開始年月
     * @param int    $toMonth        aggregate range      集計範囲(単位: ヶ月)
     * @param int    $request_id     request ID           リクエストID
     * @param string $language       language             メール本文言語
     * @return bool  true/false      success/failed       成功/失敗
     * @throws Exception
     */
    private function sendRequest($sitelicense_id, $mail_no, $start_date, $toMonth, $request_id, $language) {
        // 照合用パスワードを送信する
        if($request_id == 0) {
            $query = "SELECT * FROM {repository_sitelicense_mail_send_status_all} ".
                     "WHERE organization_id = ? ".
                     "AND mail_no = ? ;";
            $params = array();
            $params[] = $sitelicense_id;
            $params[] = $mail_no;
        } else {
            $query = "SELECT * FROM {repository_sitelicense_mail_send_status} ".
                     "WHERE organization_id = ? ".
                     "AND mail_no = ? ".
                     "AND request_id = ? ";
            $params = array();
            $params[] = $sitelicense_id;
            $params[] = $mail_no;
            $params[] = $request_id;
        }
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            throw new Exception($this->Db->ErrorMsg());
        }
        // Request parameter for next URL
        $nextRequest = BASE_URL."/?action=repository_action_common_sendsitelicensemail".
                                 "&sitelicense_id=".$sitelicense_id.
                                 "&mail_no=".$mail_no.
                                 "&start_date=".$start_date.
                                 "&range=".$toMonth.
                                 "&request_id=".$request_id.
                                 "&request_password=".$result[0]["request_password"];
        if(strlen($language) > 0) {
            $nextRequest .= "&lang=".$language;
        }

        $url = parse_url($nextRequest);
        $nextRequest = str_replace($url["scheme"]."://".$url["host"], "",  $nextRequest);
        
        // Call oneself by async
        $host = array();
        preg_match("/^https?:\/\/(([^\/]+)).*$/", BASE_URL, $host);
        $hostName = $host[1];
        if($hostName == "localhost") {
            $hostName = gethostbyname($_SERVER['SERVER_NAME']);
        }
        $hostSock = $hostName;
        $serverPort = 80;
        if(preg_match("/^https/", BASE_URL)) {
            $hostSock = "ssl://".$hostName;
            $serverPort = 443;
        }
        
        $handle = false;
        for($count = 0; $count <= self::SOCKOPEN_TRY_COUNT; $count++) {
            $handle = fsockopen($hostSock, $serverPort, $errno, $errstr);
            if($handle) {
                break;
            }
            $this->errorLog("fsockopen error. hostName = ".$hostSock.", port = ".$serverPort.", errorNo = ".$errno.", errorStr = ".$errstr, __FILE__, __CLASS__, __LINE__);
            
            // 最後のfsockopenで失敗した場合、処理は待たない
            if($count < self::SOCKOPEN_TRY_COUNT-1) {
                // fsockopenが失敗した場合、一定時間処理を待つ
                sleep(self::SOCKOPEN_WAIT_SEC_TIME_RETRY);
                $this->errorLog("Retry fsockopen ".($count+1)." time. ", __FILE__, __CLASS__, __LINE__);
            }
        }
        if (!$handle) {
            $this->errorLog("Retry fsockopen ".(self::SOCKOPEN_TRY_COUNT-1)." time, but all failed ", __FILE__, __CLASS__, __LINE__);
            return false;
        }
        
        stream_set_blocking($handle, false);
        $this->debugLog("RequestURL: ".$nextRequest, __FILE__, __CLASS__, __LINE__);
        fwrite($handle, "GET ".$nextRequest." HTTP/1.1\r\nHost: ". $hostName."\r\n\r\n");
        fclose ($handle);
        
        return true;
    }
}
?>