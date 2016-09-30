<?php

/**
 * Usage statistics view class
 * 利用統計レポートViewクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Log.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * DB object wrapper class
 * DBオブジェクトクラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDbAccess.class.php';
/**
 * aggregate class
 * 集計クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAggregateCalculation.class.php';

/**
 * Usage statistics view class
 * 利用統計レポートViewクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Edit_Log extends RepositoryAction
{
    // component
    /**
     * Request object
     * リクエストパラメータ処理オブジェクト
     *
     * @var Request
     */
    var $request = null;

    // menber
    /**
     * options: start year
     * 集計開始年月プルダウン(年)
     *
     * @var array
     */
    var $year_option_start=Array();
    /**
     * options: start month
     * 集計開始年月プルダウン(月)
     *
     * @var array
     */
    var $month_option_start=Array();
    /**
     * options: start day
     * 集計開始年月プルダウン(日)
     *
     * @var array
     */
    var $day_option_start=Array();
    /**
     * options: end year
     * 集計終了年月プルダウン(年)
     *
     * @var array
     */
    var $year_option_end=Array();
    /**
     * options: end month
     * 集計終了年月プルダウン(月)
     *
     * @var array
     */
    var $month_option_end=Array();
    /**
     * options: end day
     * 集計終了年月プルダウン(日)
     *
     * @var array
     */
    var $day_option_end=Array();
    
    // Set help icon setting 2010/02/10 K.Ando --start--
    /**
     * Help icon display flag
     * ヘルプアイコン表示フラグ
     *
     * @var int
     */
    var $help_icon_display =  null;
    // Set help icon setting 2010/02/10 K.Ando --end--
    
    // Add send mail for log report 2010/03/10 Y.Nakao --start--
    /**
     * Mail address
     * メールアドレス
     *
     * @var array
     */
    var $mail_address = null;
    /**
     * URL
     * URL
     *
     * @var srray
     */
    var $mail_url = null;
    // Add send mail for log report 2010/03/10 Y.Nakao --start--
    
    // Add log move 2010/05/21 Y.Nakao --start--
    /**
     * Delete log start month
     * ログ削除開始月
     *
     * @var array
     */
    var $startmonth = array();
    /**
     * Delete log end month
     * ログ削除終了月
     *
     * @var array
     */
    var $lastmonth = array();
    // Add log move 2010/05/21 Y.Nakao --end--
    
    // Add number of the items 2014/01/09 S.Suzuki --start--
    /**
     * All items
     * 全アイテム
     *
     * @var array
     */
    public $items = array();
    // Add number of the items 2014/01/09 S.Suzuki --end--

    // Add Sitelicense view 2016/03/11 T.Ichikawa --start--
    /**
     * Send sitelicense mail status
     * サイトライセンス利用統計フィードバックメール送信状態
     *
     * @var int
     */
    public $sitelicense_send_status = null;
    /**
     * Last send date for sitelicense mail
     * サイトライセンス利用統計メール最終送信日時
     *
     * @var string
     */
    public $sitelicense_send_date = null;
    /**
     * Sitelicense info
     * サイトライセンス情報
     *
     * @var array
     */
    public $sitelicense_info = array();
    /**
     * API URL for send sitelicense
     * サイトライセンス利用統計メール送信API
     *
     * @var string
     */
    public $sitelicense_send_url = null;
    /**
     * Send sitelicense mail allow flag
     * サイトライセンス利用統計メール送信可否フラグ
     *
     * @var int
     */
    public $sitelicense_send_allow = null;
    /**
     * options: sitelicense mail start year
     * サイトライセンス利用統計集計開始範囲(年)
     *
     * @var array
     */
    public $sitelicense_year_option_start = array();
    /**
     * options: sitelicense mail start month
     * サイトライセンス利用統計集計開始範囲(月)
     *
     * @var array
     */
    public $sitelicense_month_option_start = array();
    /**
     * options: sitelicense mail end year
     * サイトライセンス利用統計集計終了範囲(年)
     *
     * @var array
     */
    public $sitelicense_year_option_end = array();
    /**
     * options: sitelicense mail end month
     * サイトライセンス利用統計集計終了範囲(月)
     *
     * @var array
     */
    public $sitelicense_month_option_end = array();
    /**
     * Set PHP command path flag
     * PHPコマンドパスがセットされているか否かのフラグ
     *
     * @var bool
     */
    public $sitelicense_set_php_path_flag = false;

    // Add Sitelicense view 2016/03/11 T.Ichikawa --end--
    
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws AppException
     */
    function executeApp()
    {
        // 開始・終了日の選択候補（デフォルトチェック込み）文字列の作成
        $NOW_DATE = new Date();

        // get min log record year
        $sy = $NOW_DATE->getYear();
        $sm = sprintf("%02d",$NOW_DATE->getMonth());
        $sd = sprintf("%02d",$NOW_DATE->getDay());
        $query = " SELECT MIN( DATE_FORMAT(record_date, '%Y-%m-%d') ) AS min_date ".
                " FROM ".DATABASE_PREFIX."repository_log; ";
        $result = $this->Db->execute($query);
        if($result !== false || count($result) == 1){
            $date = explode("-", $result[0]['min_date']);
            if(count($date) == 3){
                $sy = $date[0];
                $sm = $date[1];
                $sd = $date[2];
            }
        }
        
        $date = explode("-", $result[0]['min_date']);
        if(count($date) == 2){
            $sy = $date[0];
            $sm = $date[1];
            $sd = "01";
        }
        
        // move to file
        for($ii=$sy; $ii>2008; $ii--){
            if(file_exists(WEBAPP_DIR."/logs/weko/logfile/log_per_date_$ii.txt")){
                $fp = fopen(WEBAPP_DIR."/logs/weko/logfile/log_per_date_$ii.txt", "r");
                $line = fgets($fp);
                $line = str_replace("\r\n", "", $line);
                $line = str_replace("\n", "", $line);
                $line = preg_replace("/\t.*/", "", $line);
                $date = explode("-", $line);
                $sy = $date[0];
                $sm = $date[1];
                $sd = $date[2];
                fclose($fp);
            }
        }
        
        //$cnt_year = $sy - date('Y',$now);
        $cnt_year = $sy - $NOW_DATE->getYear();
        $sl_lastDate = explode("-", date("Y-n", strtotime("-1 month")));
        $sl_lastYear = $sl_lastDate[0];
        $sl_lastMonth = $sl_lastDate[1];
        for ($ii=$cnt_year; $ii<=0; $ii++) {
            $str_year = $NOW_DATE->getYear() + $ii;
            $select_s = $ii==$cnt_year ? 1 : 0;
            $select_e = $ii==0 ? 1 : 0;
            array_push($this->year_option_start,Array($str_year,$select_s));
            array_push($this->year_option_end,Array($str_year,$select_e));
            // サイトライセンス用
            $sl_select_s = ($str_year == $sl_lastYear) ? 1 : 0;
            array_push($this->sitelicense_year_option_start,Array($str_year,$sl_select_s));
            array_push($this->sitelicense_year_option_end,Array($str_year,$sl_select_s));
        }
        // 月
        for ( $ii=1; $ii<=12; $ii++ ) {
            $str_month = ($ii);
            $select_s = $str_month==$sm ? 1 : 0;
            //$select_e = $str_month==date('m',$now) ? 1 : 0;
            $select_e = $str_month==$NOW_DATE->getMonth() ? 1 : 0;
            array_push($this->month_option_start,Array($str_month,$select_s));
            array_push($this->month_option_end,Array($str_month,$select_e));
            // サイトライセンス用
            $sl_select_s = ($str_month == $sl_lastMonth) ? 1 : 0;
            array_push($this->sitelicense_month_option_start,Array($str_month,$sl_select_s));
            array_push($this->sitelicense_month_option_end,Array($str_month,$sl_select_s));
        }
        // 日
        for ( $ii=1; $ii<=31; $ii++ ) {
            $str_day = ($ii);
            $select_s = $str_day==$sd ? 1 : 0;
            //$select_e = $str_day==date('d',$now) ? 1 : 0;
            $select_e = $str_day==$NOW_DATE->getDay() ? 1 : 0;
            array_push($this->day_option_start,Array($str_day,$select_s));
            array_push($this->day_option_end,Array($str_day,$select_e));
        }
        
        // Add lang resource 2008/11/27 Y.Nakao --start--
        $this->setLangResource();
        // Add lang resource 2008/11/27 Y.Nakao --end--
        
        // Set help icon setting 2010/02/10 K.Ando --start--
        $result = $this->getAdminParam('help_icon_display', $this->help_icon_display, $Error_Msg);
        if ( $result == false ){
            $this->errorLog("Get help icon failed.", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Get help icon failed.");
        }
        // Set help icon setting 2010/02/10 K.Ando --end--
        
        // Add send mail for log report 2010/03/10 Y.Nakao --start--
        $block_id = $this->getBlockPageId();
        $result = $this->getAdminParam('log_report_mail', $this->mail_address, $Error_Msg);
        if ( $result == false ){
            $this->errorLog("Get block page id failed.", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Get block page id failed.");
        }
        $this->mail_url = BASE_URL."/?action=repository_logreport&mail=true".
                        "&block_id=".$block_id['block_id']."&page_id=".$block_id['page_id'].
                        "&login_id=[login_id]&password=[password]";
        // Add send mail for log report 2010/03/10 Y.Nakao --end--
        
        // Add log move 2010/05/21 Y.Nakao --start--
        $query = "SELECT DATE_FORMAT(DATE_SUB('".$NOW_DATE->getYear()."-".$NOW_DATE->getMonth()."-01', INTERVAL 1 MONTH), '%Y') AS tmp_year,".
                 " DATE_FORMAT(DATE_SUB('".$NOW_DATE->getYear()."-".$NOW_DATE->getMonth()."-01', INTERVAL 1 MONTH), '%m') AS tmp_month;";
        $result = $this->Db->execute($query);
        if($result === false || count($result) != 1){
            $this->errorLog("Get formated date failed.", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Get formated date failed.");
        }
        
        $this->lastmonth['year'] = $result[0]['tmp_year'];
        $this->lastmonth['month'] = sprintf("%02d",$result[0]['tmp_month']);
        $query = " SELECT MIN( DATE_FORMAT(record_date, '%Y-%m') ) AS min_date ".
                " FROM ".DATABASE_PREFIX."repository_log; ";
        $result = $this->Db->execute($query);
        if($result == false || count($result) != 1){
            // error
            $this->startmonth['year'] = $this->lastmonth['year'];
            $this->startmonth['month'] = $this->lastmonth['month'];
        } else { 
            $date = explode("-", $result[0]['min_date']);
            $this->startmonth['year'] = $date[0];
            $this->startmonth['month'] = $date[1];
        }
        if(    $this->startmonth['year'] >= $this->lastmonth['year'] && 
            $this->startmonth['month'] >= $this->lastmonth['month']){
            $this->lastmonth['year'] = $this->startmonth['year'];
            $this->lastmonth['month'] = $this->startmonth['month'];
        }
        // Add log move 2010/05/21 Y.Nakao --end--
        $this->setSitelicenseInfo();
        
        // Add get total items 2014/01/14 S.Suzuki --start--
        $this->TransStartDate = $NOW_DATE->getDate().".000";
        $this->dbAccess = new RepositoryDbAccess($this->Db);
        
        $repositoryAggregateCalculation = new RepositoryAggregateCalculation($this->Session, $this->dbAccess, $this->TransStartDate);
        $this->items = $repositoryAggregateCalculation->countItem();
        // Add get total items 2014/01/14 S.Suzuki --end-- 
        
        return 'success';
    }

    /**
     * Set sitelicense info
     * サイトライセンス情報をセットする
     *
     * @throws AppException
     */
    private function setSitelicenseInfo()
    {
        // サイトライセンス管理ビジネスクラス
        $sitelicenseManager = BusinessFactory::getFactory()->getBusiness("businessSitelicensemanager");
        // 機関情報取得
        $sitelicense = $sitelicenseManager->searchAllSitelicenseInfo();
        for($ii = 0; $ii < count($sitelicense); $ii++) {
            // メールアドレスを配列形式で詰め直す
            $tmp_mail = str_replace("\r\n", "\n", $sitelicense[$ii]["mail_address"]);
            $mail_array = explode("\n", $tmp_mail);
            
            $sitelicense[$ii]["mail_address"] = array();
            for($jj = 0; $jj < count($mail_array); $jj++) {
                $sitelicense[$ii]["mail_address"][] = $mail_array[$jj];
            }
        }
        $this->sitelicense_info = $sitelicense;
        
        // 実行状況取得
        $send_status = $sitelicenseManager->getSendStatus();
        // -1: 一度も送信されていない, 0: 送信済み, 1: 送信中
        $this->sitelicense_send_status = $send_status["status"];
        // 送信開始or終了日時
        $this->sitelicense_send_date = $send_status["date"];
        
        // 送信許可フラグ
        $this->sitelicense_send_allow = $sitelicenseManager->checkSendMailAllow();
        // 実行URL設定
        $this->sitelicense_send_url = BASE_URL."/?action=repository_action_common_sitelicensemail&login_id=[login_id]&password=[password]";
        // PHP実行パス設定チェック
        $query = "SELECT param_value FROM {repository_parameter} ".
                 "WHERE param_name = ? ";
        $params = array();
        $params[] = "path_php";
        $result = $this->executeSql($query, $params);
        if(strlen($result[0]["param_value"]) > 0) { $this->sitelicense_set_php_path_flag = true; }
    }
}
?>
