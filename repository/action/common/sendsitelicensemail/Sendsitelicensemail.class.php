<?php

/**
 * Execute to send sitelicense feedback mail class
 * サイトライセンス利用統計フィードバックメール送信実行クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Sendsitelicensemail.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Execute to send sitelicense feedback mail class
 * サイトライセンス利用統計フィードバックメール送信実行クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Common_Sendsitelicensemail extends WekoAction
{
    //----------------------------
    // Request parameters
    //----------------------------
    /**
     * sitelicense id
     * サイトライセンスID
     *
     * @var int
     */
    public $sitelicense_id = null;
    /**
     * mail number
     * メール通番
     *
     * @var array
     */
    public $mail_no = null;
    /**
     * start date
     * 集計開始年月
     *
     * @var int
     */
    public $start_date = null;
    /**
     * aggregate range(month)
     * 集計範囲(ヶ月)
     *
     * @var int
     */
    public $range = null;
    /**
     * request id
     * リクエストID
     *
     * @var int
     */
    public $request_id = null;
    /**
     * password for executing
     * 実行パスワード
     *
     * @var string
     */
    public $request_password = null;
    
    /**
     * Start send sitelicense mail
     * サイトライセンスメール送信処理開始
     *
     * @return string "success"/"error" 成功/失敗
     */
    public function executeApp() {
        $this->exitFlag = true;
        
        if(!$this->checkRequestPassword($this->request_id, $this->sitelicense_id, $this->mail_no, $this->request_password)) {
            return "error";
        }
        
        // 送信処理
        $sendSitelicenseMail = BusinessFactory::getFactory()->getBusiness("businessSendsitelicensemail");
        $year = intval(substr($this->start_date, 0, 4));
        $month = intval(substr($this->start_date, 4, 2));
        $sendSitelicenseMail->sendSitelicenseMailExecute($this->request_id, $this->sitelicense_id, $this->mail_no, $year, $month, $this->range, $this->Session->getParameter("_lang"));
        
        return "success";
    }

    /**
     * Check password for execute
     * 実行パスワードを照合する
     *
     * @param int $request_id request ID リクエストID
     * @param int $sitelicense_id sitelicense ID サイトライセンスID
     * @param int $mail_no mail number メール通番
     * @param string $request_password password for execute 実行パスワード
     * @return bool true/false can execute/or not 実行可能/実行不可
     * @throws Exception
     */
    private function checkRequestPassword($request_id, $sitelicense_id, $mail_no, $request_password) {
        // 乱数値をチェックする
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
        
        if($result[0]["request_password"] == $request_password) {
            return true;
        } else {
            return false;
        }
    }
}

?>