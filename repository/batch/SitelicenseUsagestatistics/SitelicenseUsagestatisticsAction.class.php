<?php

/**
 * Execute sitelicense usage statistics class
 * サイトライセンス利用統計実行クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: SitelicenseUsagestatisticsAction.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Sitelicense usage statistics batch progress Class
 * サイトライセンス利用統計バッチ進捗クラス
 */
require_once WEBAPP_DIR."/modules/repository/batch/SitelicenseUsagestatistics/SitelicenseUsagestatisticsProgress.class.php";
/**
 * Operate string util class
 * 文字列操作ユーティリティークラス
 */
require_once WEBAPP_DIR."/modules/repository/components/util/StringOperator.class.php";

/**
 * Execute sitelicense usage statistics class
 * サイトライセンス利用統計実行クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class SitelicenseUsagestatisticsAction extends BatchBase
{
    // エラーコード
    /**
     * Shortage argument error
     * 引数不足エラー
     */
    const ERROR_ARGUMENT_SHORTAGE = 200;
    /**
     * from date argument format error
     * 開始日時引数フォーマットエラー
     */
    const ERROR_FROM_FORMAT = 201;
    /**
     * date range argument format error
     * 日時範囲引数フォーマットエラー
     */
    const ERROR_TO_FORMAT = 202;
    /**
     * date range argument format error
     * 日時範囲引数フォーマットエラー
     */
    const ERROR_DATE_RANGE = 203;
    /**
     * Request ID argument format error
     * リクエストID引数フォーマットエラー
     */
    const ERROR_REQUEST_ID_FORMAT = 205;
    /**
     * Retry flag argument format error
     * リトライ実行フラグ引数フォーマットエラー
     */
    const ERROR_RETRY_FORMAT = 206;
    /**
     * Not set PHP commando path error
     * PHPコマンド実行パス未設定エラー
     */
    const ERROR_NOT_SET_PHP_PATH = 207;

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
     * Argument retry flag
     * 再実行フラグ引数
     */
    const ARGUMENT_RETRY_FLAG = "retry";
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

    // 実行モード
    /**
     * Error execute
     * 実行エラー
     */
    const MODE_EXECUTE_ERROR = -1;
    /**
     * All matter execution
     * 全件実行
     */
    const MODE_EXECUTE_ALL = 0;
    /**
     * Retry execution
     * 再実行
     */
    const MODE_EXECUTE_RETRY = 1;
    /**
     * Execution by request ID
     * リクエストIDによる指定実行
     */
    const MODE_EXECUTE_REQUEST_ID = 2;

    // 送信状態テーブル名
    /**
     * Send status table name
     * 送信状態管理テーブル名
     */
    const TABLE_SEND_STATUS = "repository_sitelicense_mail_send_status";
    /**
     * All send status table name
     * 全件送信状態管理テーブル名
     */
    const TABLE_SEND_STATUS_ALL = "repository_sitelicense_mail_send_status_all";

    /**
     * Interpret the argument
     * 引数を解釈する
     *
     * @param array $options options 実行オプション
     *               array["language"|"from"|"to"|"retry"|"requestid"]
     * @return SitelicenseUsagestatisticsProgress sitelicense usage statistics batch progress object サイトライセンス利用統計バッチ進捗オブジェクト
     * @throws Exception
     */
    protected function startProcess($options) {
        // 引数チェック
        $mode = self::MODE_EXECUTE_ERROR;
        
        // 集計範囲・機関を指定する引数のチェック
        if(array_key_exists(self::ARGUMENT_START_DATE, $options) && array_key_exists(self::ARGUMENT_DATE_RANGE, $options)) {
            // 集計開始日時のフォーマットチェック
            if(!preg_match("/^\d{6}$/", $options[self::ARGUMENT_START_DATE])) {
                throw new Exception("Option format error : '".self::ARGUMENT_START_DATE."'.", self::ERROR_FROM_FORMAT);
            }
            // 集計範囲のフォーマットチェック
            if(!preg_match("/^[1-9]\d*$/", $options[self::ARGUMENT_DATE_RANGE])) {
                throw new Exception("Option format error : '".self::ARGUMENT_DATE_RANGE."'.", self::ERROR_DATE_RANGE);
            }
            $mode = self::MODE_EXECUTE_ALL;
        }
        if(array_key_exists(self::ARGUMENT_RETRY_FLAG, $options)) {
            // 引数でモード重複をしていないかチェック
            if($mode > self::MODE_EXECUTE_ERROR) { throw new Exception("Duplicate options.", self::ERROR_DATE_RANGE); }

            if($options[self::ARGUMENT_RETRY_FLAG] != "true") {
                throw new Exception("Option format error : '".self::ARGUMENT_RETRY_FLAG."'.", self::ERROR_RETRY_FORMAT);
            }

            $mode = self::MODE_EXECUTE_RETRY;
        }
        if(array_key_exists(self::ARGUMENT_REQUEST_ID, $options)) {
            // 引数でモード重複をしていないかチェック
            if($mode > self::MODE_EXECUTE_ERROR) { throw new Exception("Duplicate options.", self::ERROR_DATE_RANGE); }
            if(!preg_match("/^[1-9]\d*$/", $options[self::ARGUMENT_REQUEST_ID])) {
                throw new Exception("Option format error : '".self::ARGUMENT_REQUEST_ID."'.", self::ERROR_REQUEST_ID_FORMAT);
            }
            $mode = self::MODE_EXECUTE_REQUEST_ID;
        }
        // 必要な引数が無い場合はエラー
        if($mode == self::MODE_EXECUTE_ERROR) {
            throw new Exception("Option shortage.", self::ERROR_ARGUMENT_SHORTAGE);
        }

        // 言語
        $language = "";
        if(array_key_exists(self::ARGUMENT_LANGUAGE, $options) && strlen($options[self::ARGUMENT_LANGUAGE]) > 0) {
            $language = $options[self::ARGUMENT_LANGUAGE];
        }

        // PHP Path
        $php_path = $this->searchPhpPath();
        if(strlen($php_path) == 0) {
            throw new Exception("PHP Path is not set.", self::ERROR_NOT_SET_PHP_PATH);
        }
        
        //  送信情報リストを取得する
        $sendList = array();
        switch($mode) {
            case self::MODE_EXECUTE_ALL:
                $this->updateSendStatus(self::TABLE_SEND_STATUS_ALL, $options[self::ARGUMENT_START_DATE], $options[self::ARGUMENT_DATE_RANGE]);
                $sendList = $this->searchAllSendList();
                break;
            case self::MODE_EXECUTE_RETRY:
                $sendList = $this->searchAllSendList();
                break;
            case self::MODE_EXECUTE_REQUEST_ID:
                $sendList = $this->searchSendListByRequestId($options[self::ARGUMENT_REQUEST_ID]);
                break;
        }

        if(count($sendList) == 0) {
            throw new Exception("Not exist organization for sending.");
        }

        // サイトライセンス機関IDをリスト化する
        $sitelicenseIds = array();
        for($ii = 0; $ii < count($sendList); $ii++) {
            // サイトライセンス機関IDリスト
            if(!in_array($sendList[$ii]["organization_id"], $sitelicenseIds)) { $sitelicenseIds[] = $sendList[$ii]["organization_id"]; }
        }
        // 集計範囲リスト
        $dates = array();
        for($ii = 0; $ii < $sendList[0]["aggregate_range"]; $ii++) {
            $tmpDate = date("Ym", strtotime($sendList[0]["year"]."-".$sendList[0]["month"]." +".$ii." month"));
            if($tmpDate >= date("Ym")) { throw new Exception("Future date has been specified.", $options[self::ERROR_DATE_RANGE]); }
            $dates[] = $tmpDate;
        }

        // 進捗クラス
        return new SitelicenseUsagestatisticsProgress($options, $sitelicenseIds, $dates, $sendList, $language, $php_path);
    }

    /**
     * Execute
     * 実行
     *
     * @param SitelicenseUsagestatisticsProgress $progress batch progress object バッチ進捗オブジェクト
     * @return SitelicenseUsagestatisticsProgress $progress batch progress object バッチ進捗オブジェクト
     */
    function executeStep($progress) {
        if(!$progress->aggregateCompleteFlag) {
            // 集計処理
            $command = $progress->phpPath."php ".
                       WEBAPP_DIR."/modules/repository/batch/SitelicenseUsagestatisticsSummary/SitelicenseUsagestatisticsSummaryBatch.php ".
                        " --from=".$progress->dateList[$progress->aggregateDateCnt].
                        " --siteid=".$progress->sitelicenseIdList[$progress->aggregateSitelicenseCnt];
            // 別名実行の場合
            if(array_key_exists("batch_name", $progress->options) && strlen($progress->options["batch_name"]) > 0) {
                $command .= " --batch_name=sitelicenseUsagestatisticsSummarySelect";
            }
            $this->debugLog($command, __FILE__, __CLASS__, __LINE__);
            $result = exec($command);
            $this->debugLog("Response Code : ". $result, __FILE__, __CLASS__, __LINE__);
            // 正常終了でなかった場合はエラー終了する
            if($result != 0) { $progress->exitCode = $result; }
            

            if($progress->aggregateDateCnt >= count($progress->dateList)-1 && $progress->aggregateSitelicenseCnt >= count($progress->sitelicenseIdList)-1) {
                // 集計が全て終わった場合は送信モードへ移行する
                $progress->aggregateCompleteFlag = true;
            } elseif($progress->aggregateDateCnt >= count($progress->dateList)-1) {
                // 一つの機関の集計が全て終わった場合次の機関の集計に移る
                $progress->aggregateSitelicenseCnt++;
                $progress->aggregateDateCnt = 0;
            } else {
                $progress->aggregateDateCnt++;
            }
        } else {
            // 送信処理
            $date = date("Ym", strtotime($progress->sendList[$progress->sendCompleteCnt]["year"]."-".$progress->sendList[$progress->sendCompleteCnt]["month"]));
            
            $command = $progress->phpPath."php " .
                       WEBAPP_DIR . "/modules/repository/batch/SitelicenseUsagestatisticsFeedbackMail/SitelicenseUsagestatisticsFeedbackMailBatch.php " .
                       " --from=".$date.
                       " --to=".$progress->sendList[$progress->sendCompleteCnt]["aggregate_range"].
                       " --siteid=".$progress->sendList[$progress->sendCompleteCnt]["organization_id"].
                       " --mailno=".$progress->sendList[$progress->sendCompleteCnt]["mail_no"];
            if(array_key_exists("request_id", $progress->sendList[$progress->sendCompleteCnt])) {
                $command .= " --requestid=".$progress->sendList[$progress->sendCompleteCnt]["request_id"];
            }
            // 別名実行の場合
            if(array_key_exists("batch_name", $progress->options) && strlen($progress->options["batch_name"]) > 0) {
                $command .= " --batch_name=sitelicenseUsagestatisticsFeedbackMailSelect";
            }
            // メールの表示言語
            if(strlen($progress->language) > 0) {
                $command .= " --language=".$progress->language;
            }
            
            $this->debugLog($command, __FILE__, __CLASS__, __LINE__);
            $result = exec($command);
            $this->debugLog("Response Code : ". $result, __FILE__, __CLASS__, __LINE__);
            // 正常終了でなかった場合はエラー終了する
            if($result != 0) { $progress->exitCode = $result; }
            
            // 全て送信が終わった場合正常終了コードを返す
            if($progress->sendCompleteCnt >= count($progress->sendList)-1) { $progress->exitCode = BatchExitCodes::END_SUCCESS; }
            
            $progress->sendCompleteCnt++;
        }
        
        return $progress;
    }
    
    /**
     * Acquire all of the site license destination information from the DB
     * DBからサイトライセンス送信先情報を全て取得する
     *
     * @return array $result sitelicense information list サイトライセンス情報一覧
     *                        array[0]["organization_id" | "organization_name" | "mail_address"]
     * @throws Exception
     */
    private function searchSitelicenseInfo() {
        $query = "SELECT * FROM {repository_sitelicense_info} ".
                 "WHERE is_delete = ? ".
                 "AND mail_address <> '';";
        $params = array();
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            throw new Exception($this->Db->ErrorMsg(), BatchExitCodes::ERROR_SQL);
        }

        return $result;
    }

    /**
     * Set up target data
     * 送信対象データを設定する
     *
     * @param string $table     mail send status table 送信状態テーブル名
     * @param int    $from_date aggregate start date   集計開始年月
     * @param int    $toMonths  aggregate range        集計範囲(単位: ヶ月)
     * @throws Exception
     */
    private function updateSendStatus($table, $from_date, $toMonths) {
        // 既存データを全て削除する
        $result = $this->Db->execute("TRUNCATE ".DATABASE_PREFIX.$table.";");
        if($result === false) {
            throw new Exception($this->Db->ErrorMsg(), BatchExitCodes::ERROR_SQL);
        }
        // 年月のフォーマット整形
        $year = intval(substr($from_date, 0, 4));
        $month = intval(substr($from_date, 4, 2));
        // サイトライセンス送信先情報を全て取得する
        $sitelicenseInfo = $this->searchSitelicenseInfo();
        for($ii = 0; $ii < count($sitelicenseInfo); $ii++) {
            $tmpMail = str_replace("\r\n", "\n", $sitelicenseInfo[$ii]["mail_address"]);
            $tmpMail = explode("\n", $tmpMail);
            for($jj = 0; $jj < count($tmpMail); $jj++) {
                if(strlen($tmpMail[$jj]) == 0) { continue; }
                $this->insertSitelicenseSendStatus($sitelicenseInfo[$ii]["organization_id"],
                                                   $jj+1,
                                                   $tmpMail[$jj],
                                                   $year,
                                                   $month,
                                                   $toMonths);
            }
        }
    }

    /**
     * Add target data
     * 送信対象データを追加する
     *
     * @param int    $organization_id sitelicense id        サイトライセンス機関ID
     * @param int    $mail_no         mail number           メール通番
     * @param string $mail_address    mail address          送信対象メールアドレス
     * @param int    $year            aggregate start year  集計開始年
     * @param int    $month           aggregate start month 集計開始月
     * @param int    $to              aggregate range       集計範囲(単位: ヶ月)
     * @throws Exception
     */
    private function insertSitelicenseSendStatus($organization_id, $mail_no, $mail_address, $year, $month, $to) {
        $query = "INSERT INTO {repository_sitelicense_mail_send_status_all} ".
                 "VALUES ".
                 "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $params = array();
        $params[] = $organization_id;
        $params[] = $mail_no;
        $params[] = $mail_address;
        $params[] = $year;
        $params[] = $month;
        $params[] = $to;
        $params[] = 0;
        $params[] = $this->accessDate;
        $params[] = "";
        $params[] = Repository_Components_Util_Stringoperator::makeRandStr(16);
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            throw new Exception($this->Db->ErrorMsg(), BatchExitCodes::ERROR_SQL);
        }
    }

    /**
     * Search list for sending mail by request ID (for send to all)
     * 送信対象リストを取得する(全件送信時)
     *
     * @return array $result send list 送信対象リスト
     *                        array[0]["request_id" | "organization_id" | "mail_no" | "mail_address" | "year" | "month" | "aggregate_range" | "start_date" | "send_date"]
     * @throws Exception
     */
    private function searchAllSendList() {
        $query = "SELECT * FROM {repository_sitelicense_mail_send_status_all} ".
                 "WHERE send_status <> ? ;";
        $params = array();
        $params[] = 1;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            throw new Exception($this->Db->ErrorMsg(), BatchExitCodes::ERROR_SQL);
        }

        return $result;
    }

    /**
     * Search list for sending mail by request ID
     * リクエストIDから送信対象リストを取得する
     *
     * @param  int   $request_id request ID リクエストID
     * @return array $result     send list  送信対象リスト
     *                            array[0]["request_id" | "organization_id" | "mail_no" | "mail_address" | "year" | "month" | "aggregate_range" | "start_date" | "send_date"]
     * @throws Exception
     */
    private function searchSendListByRequestId($request_id) {
        $query = "SELECT * FROM {repository_sitelicense_mail_send_status} ".
                 "WHERE send_status <> ? ".
                 "AND request_id = ? ;";
        $params = array();
        $params[] = 1;
        $params[] = $request_id;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            throw new Exception($this->Db->ErrorMsg(), BatchExitCodes::ERROR_SQL);
        }

        return $result;
    }

    /**
     * Search PHP Path By DB
     * DBからPHPコマンド実行パスを取得する
     *
     * @return string PHP Path PHPコマンド実行パス
     * @throws Exception
     */
    private function searchPhpPath() {
        $query = "SELECT param_value FROM {repository_parameter} ".
                 "WHERE param_name = ? ;";
        $params = array();
        $params[] = "path_php";
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            throw new Exception($this->Db->ErrorMsg(), BatchExitCodes::ERROR_SQL);
        }

        return $result[0]["param_value"];
    }
}
?>