<?php

/**
 * Send sitelicense feedback mail by View class
 * 画面からのサイトライセンス利用統計フィードバックメール送信クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Sendmail.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/common/WekoAction.class.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Operation log abstract class
 * ログ操作基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/Logbase.class.php';
/**
 * String operator class
 * 文字列操作クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/util/StringOperator.class.php';

/**
 * Send sitelicense feedback mail by View class
 * 画面からのサイトライセンス利用統計フィードバックメール送信クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Edit_Sitelicense_Sendmail extends WekoAction
{
    //----------------------------
    // Request parameters
    //----------------------------
    /**
     * sitelicense ids
     * サイトライセンスID配列
     *
     * @var array
     */
    public $sitelicense_ids = null;
    /**
     * year(from)
     * 集計開始年
     *
     * @var int
     */
    public $from_year = null;
    
    /**
     * month(from)
     * 集計開始月
     *
     * @var int
     */
    public $from_month = null;
    /**
     * year(to)
     * 集計終了年
     *
     * @var int
     */
    public $to_year = null;

    /**
     * month(to)
     * 集計終了月
     *
     * @var int
     */
    public $to_month = null;
    
    /**
     * Execute
     * 実行
     *
     * @return bool true/false success/failed 成功/失敗
     */
    public function executeApp() {
        $this->Session->removeParameter("redirect_flg");

        $this->exitFlag = true;
        
        // 実行可否チェック
        if(!$this->checkExecute()) { return false; }

        // PHPの実行パス取得
        $php_path = $this->searchPhpPath();
        if(strlen($php_path) == 0) { return false; }

        // 送信先リスト作成
        $requestId = $this->createSitelicenseSendList();
        
        // 送信処理
        $command = $php_path."php ".
                   WEBAPP_DIR."/modules/repository/batch/SitelicenseUsagestatistics/SitelicenseUsagestatisticsBatch.php".
                   " --requestid=".$requestId.
                   " --batch_name=sitelicenseUsagestatisticsSelect".
                   " --language=".$this->Session->getParameter("_lang");
        // 非同期実行
        if(PHP_OS == "WIN32" || PHP_OS == "WINNT"){ 
            $fp = popen("start ".$command, "r");
            fclose($fp);
        } else {
            exec($command." > /dev/null 2>&1 &");
        }
        
        print("Start send sitelicensemail.");
        $this->Session->setParameter("redirect_flg", "sitelicensemail");
        
        return true;
    }
    
    /**
     * Check whether or not execute
     * 実行可能であるかチェックする
     *
     * @return true/false can execute/or not 実行可/実行不可
     * @throws AppException
     */
    private function checkExecute() {
        // ロボットリストによるログ削除の実行中チェック
        if($this->isExecuteRemovingLog()) {
            print("Error: Log data is updating.");
            return false;
        }
        // URLによるメール送信実行可否フラグチェック
        $sitelicenseManager = BusinessFactory::getFactory()->getBusiness("businessSitelicensemanager");
        if($sitelicenseManager->checkSendMailAllow() == 0) {
            print("Error: Send sitelicense feedback mail is not allowed.");
            return false;
        }

        return true;
    }
    
    /**
     * Check process deleting robot list log
     * ロボットリストログ削除中かどうか判定する
     *
     * @return bool true/false deleting/or not 削除処理中/削除処理中では無い
     * @throws AppException
     */
    private function isExecuteRemovingLog() {
        // check removing log process
        $query = "SELECT status ". 
                 " FROM ". DATABASE_PREFIX. "repository_lock ". 
                 " WHERE process_name = ? ;";
        $params = array();
        $params[] = 'Repository_Action_Common_Robotlist';
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // when execute removing log, throw exception
        for($cnt = 0; $cnt < count($result); $cnt++) {
            if(intval($result[$cnt]['status']) > 0) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Create list for aggregating and sending
     * サイトライセンスの集計・送信要求用の送信対象リストを作成する
     * 
     * @throws AppException
     */
    private function createSitelicenseSendList() {
        // 日付範囲が"from > to"であった場合はエラー
        if(date("Ym", strtotime($this->from_year."-".$this->from_month)) > date("Ym", strtotime($this->to_year."-".$this->to_month))) {
            throw new AppException("Date range is invalid.");
        }
        // リクエストIDを取得
        $requestId = $this->Db->nextSeq("repository_sitelicense_mail_send_status");

        $this->insertSendListSpecify($requestId);
        
        return $requestId;
    }
    
    /**
     * Add target list (for send specified)
     * 機関指定送信用の送信リストを追加する
     *
     * @param int $requestId reuqest ID リクエストID
     * @throws AppException
     */
    private function insertSendListSpecify($requestId) {
        // from-toから集計範囲が何か月分であるかを計算する
        $from_date_hyphen = date("Y-m", strtotime($this->from_year."-".$this->from_month));
        $to_date_hyphen = date("Y-m", strtotime($this->to_year."-".$this->to_month));
        $date_range = 0; // 集計範囲(ヶ月)
        while(date("Ym", strtotime($from_date_hyphen)) <= date("Ym", strtotime($to_date_hyphen))) {
            $from_date_hyphen = date("Y-m", strtotime($from_date_hyphen." +1 month"));
            $date_range++;
        }
        // INSERTクエリ
        $query = "INSERT INTO {repository_sitelicense_mail_send_status} ".
            "VALUES ".
            "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        
        $sitelicenseIdArray = explode(",", $this->sitelicense_ids);
        for($ii = 0; $ii < count($sitelicenseIdArray); $ii++) {
            // 機関IDから詳細情報を取得する
            $sitelicenseManager = BusinessFactory::getFactory()->getBusiness("businessSitelicensemanager");
            $sitelicenseInfo = $sitelicenseManager->searchSitelicenseInfoById($sitelicenseIdArray[$ii]);
            if(count($sitelicenseInfo) == 0) { continue; }
            // メールアドレスをリスト化する
            $tmpMailAddressList = explode("\n", str_replace("\r\n", "\n", $sitelicenseInfo[0]["mail_address"]));
            
            for($jj = 0; $jj < count($tmpMailAddressList); $jj++) {
                if(strlen($tmpMailAddressList[$jj]) == 0) { continue; }
                
                $params = array();
                $params[] = $requestId; // リクエストID
                $params[] = $sitelicenseInfo[0]["organization_id"]; // サイトライセンス機関ID
                $params[] = $jj + 1; // メール通番
                $params[] = $tmpMailAddressList[$jj]; // メールアドレス
                $params[] = $this->from_year; // 集計年
                $params[] = $this->from_month; // 集計月
                $params[] = $date_range; // 集計範囲(ヶ月)
                $params[] = 0; // 送信状態
                $params[] = $this->accessDate; // 処理開始日時
                $params[] = ""; // 送信完了日時
                $params[] = Repository_Components_Util_Stringoperator::makeRandStr(16); // ランダム文字列(16桁）
                $result = $this->Db->execute($query, $params);
                if($result === false) {
                    $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                    throw new AppException($this->Db->ErrorMsg());
                }
            }
        }
    }
    
    /**
     * Get execute PHP path
     * PHP実行パスを取得する
     *
     * @return string PHP command path PHP実行パス
     * @throws AppException
     */
    private function searchPhpPath() {
        $query = "SELECT param_value FROM {repository_parameter} ".
                 "WHERE param_name = ? ;";
        $params = array();
        $params[] = "path_php";
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }

        // PHP実行パスが未設定の場合はエラー
        if(strlen($result[0]["param_value"]) == 0) {
            print("Error: PHP path is not set.");
        }

        return $result[0]["param_value"];
    }
}

?>