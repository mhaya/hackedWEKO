<?php

/**
 * Repository Components Business Sitelicense Manager
 * サイトライセンス機関情報管理ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Sitelicensemanager.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';

/**
 * Repository Components Business Sitelicense Manager
 * サイトライセンス機関情報管理ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Sitelicense_Sitelicensemanager extends BusinessBase
{
    // プロセス名
    /**
     * Sitelicense usage statistics process name
     * サイトライセンス利用統計プロセス名
     */
    const SITELICENSE_PROCESS_NAME = "sitelicenseUsagestatisticsSelect";
    /**
     * Sitelicense usage statistics aggregate process name
     * サイトライセンス利用統計集計プロセス名
     */
    const SITELICENSE_SUMMARY_PROCESS_NAME = "sitelicenseUsagestatisticsSummarySelect";
    /**
     * Sitelicense usage statistics send mail process name
     * サイトライセンス利用統計メール送信プロセス名
     */
    const SITELICENSE_FEEDBACK_MAIL_PROCESS_NAME = "sitelicenseUsagestatisticsFeedbackMailSelect";
    
    /**
     * 全てのサイトライセンス機関情報を取得する
     * Acquire all of the site license agency information
     *
     * @return array サイトライセンス情報 array[0]["organization_id"]
     *                                            ["organization_name"]
     *                                            ["show_order"]
     *                                            ["group_name"]
     *                                            ["mail_address"]
     */
    public function searchAllSitelicenseInfo() {
        return $this->executeSqlFile(dirname(__FILE__)."/sitelicense_info_select_getAllData.sql");
    }

    /**
     * Acquire all of the site license agency information
     * 全てのサイトライセンス機関情報を取得する
     *
     * @param int $organization_id sitelicense organization ID サイトライセンス機関ID
     * @return array sitelicense information サイトライセンス情報
     *                array[$ii]["organization_id"|"organization_name"|"show_order"|"group_name"|"mail_address"]
     */
    public function searchSitelicenseInfoById($organization_id) {
        return $this->executeSqlFile(dirname(__FILE__)."/sitelicense_info_select_DetailInfo.sql", func_get_args());
    }
    
    /**
     * Get the IP address information of the site license agency
     * サイトライセンス機関のIPアドレス情報を取得する
     *
     * @param int $organization_id sitelicense organization ID サイトライセンス機関ID
     * @return array sitelicense IP address information サイトライセンスIP情報
     *                array[$ii]["organization_id"|"organization_no"|"start_ip_address"|"finish_ip_address"]
     */
    public function searchSitelicenseIpAddress($organization_id) {
        return $this->executeSqlFile(dirname(__FILE__)."/sitelicense_ip_address_select_getIpData.sql", func_get_args());
    }
    
    /**
     * Get status for sending sitelicense mail
     * サイトライセンスメールの送信状態を取得する
     *
     * @return array $result sitelicense usage statistics mail send status サイトライセンス利用統計メール送信状態
     *                array["status"|"date"]
     */
    public function getSendStatus() {
        $batchManager = BusinessFactory::getFactory()->getBusiness("businessBatchmanager");

        $base =$batchManager->getBatchStatus(self::SITELICENSE_PROCESS_NAME);
        $summary = $batchManager->getBatchStatus(self::SITELICENSE_SUMMARY_PROCESS_NAME);
        $feedbackMail = $batchManager->getBatchStatus(self::SITELICENSE_FEEDBACK_MAIL_PROCESS_NAME);

        $result = array();
        $result["status"] = null; // 実行状況(-1: 一度も送信されていない, 0: 送信済, 1: 送信中)
        $result["date"] = null; // 送信開始or終了日時


        if(count($base) == 0) {
            // 大元のプロセスが無い場合は未実行
            $result["status"] = -1;
        } elseif(count($summary) == 0 || count($feedbackMail) == 0) {
            if($base[0]["status"] == 0) {
                // 大元が終了してれば「送信済」として扱う
                $result["status"] = 0;
                $result["date"] = $base[0]["start_date"];
            } else {
                // 大元が実行中なら「送信中」
                $result["status"] = 1;
                $result["date"] = $base[0]["start_date"];
            }
        // どれかが実行中なら「送信中」
        } elseif($base[0]["status"] == 1 || $summary[0]["status"] == 1 || $feedbackMail[0]["status"] == 1) {
            $result["status"] = 1;
            $result["date"] = $base[0]["start_date"];
        } else {
            $result["status"] = 0;
            $result["date"] = $base[0]["end_date"];
        }
        
        return $result;
    }

    /**
     * Check Send sitelicense mail by URL
     * URLからのサイトライセンスメールの送信許可可否をチェックする
     *
     * @return int 1/0 Clear to Send/Unsendable 送信可/送信不可
     */
    public function checkSendMailAllow() {
        $result = $this->executeSqlFile(dirname(__FILE__)."/parameter_select_sendSitelicenseMailStatus.sql");

        return $result[0]["param_value"];
    }
    
    /**
     * Update the flag for send sitelicense mail
     * サイトライセンスメール送信許可フラグを更新する
     *
     * @param int $value flag value フラグ値
     */
    public function updateAllowCron($value) {
        $query = $this->loadSql(dirname(__FILE__)."/parameter_update_sendSitelicenseMailStatus.sql");
        $params = array();
        $params[] = $value;              // パラメータ値（サイトライセンスクーロン実行フラグ)
        $params[] = $this->accessDate; // 更新日時
        $params[] = "send_sitelicense_mail_activate_flg";
        $this->executeSql($query, $params);
    }
}
?>