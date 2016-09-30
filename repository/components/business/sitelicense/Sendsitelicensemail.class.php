<?php

/**
 * Repository Components Business Send sitelicense mail class
 * サイトライセンス利用統計メール送信ビジネスクラス
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
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';
/**
 * Log manager class
 * ログ管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/Logmanager.class.php';

/**
 * Repository Components Business Send sitelicensence mail c;ass
 * サイトライセンス利用統計メール送信ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Sitelicense_Sendsitelicensemail extends BusinessBase
{
    /**
     * Session
     * セッション情報
     *
     * @var Session
     */
    private $Session = null;
    /**
     * Smarty Assign
     * Smartyオブジェクト
     *
     * @var Smarty
     */
    private $smartyAssign = null;
    
    /**
     * Initialize
     * 初期化
     */
    public function onInitialize() {
        // メール本文文字列のためにSmartyを使用する
        $container =& DIContainerFactory::getContainer();
        $filterChain =& $container->getComponent("FilterChain");
        $this->smartyAssign =& $filterChain->getFilterByName("SmartyAssign");
    }
    
    /**
     * Send sitelicense usagestatistics mail
     * サイトライセンス利用統計メール送信実行インターフェース
     *
     * @param int    $request_id      request ID                  リクエストID
     * @param int    $organization_id sitelicense organization ID サイトライセンス機関ID
     * @param int    $mail_no         mail number                 メール通番
     * @param int    $start_year      aggregate start year        集計年
     * @param int    $start_month     aggregate start month       集計月
     * @param int    $toMonths        aggregate range             集計範囲(単位:ヶ月)
     * @param string $language        language                    メール本文言語
     */
    public function sendSitelicenseMailExecute($request_id, $organization_id, $mail_no, $start_year, $start_month, $toMonths, $language) {
        // レポートファイル一時配置用ディレクトリ
        $this->infoLog("businessWorkdirectory", __FILE__, __CLASS__, __LINE__);
        $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness("businessWorkdirectory");
        $tmp_dir = $businessWorkdirectory->create();
        // ZIPファイル出力先ディレクトリ
        $zip_dir = $businessWorkdirectory->create();
        // 集計範囲文字列(YYYY-MM or YYYY-MM-YYYY-MM)
        if($toMonths == 1) {
            // 「YYYY-MM」
            $dateScopeStr = $this->dateToStrYm($start_year, $start_month);
            $dateScopeStrHyphen = $this->dateToStrYm($start_year, $start_month, "-");
        } else {
            // 「YYYYMM-YYYYMM」 AND 「YYYY-MM - YYYY-MM」
            $toDate = $this->createNextDateStr($start_year, $start_month, $toMonths);
            $dateScopeStr = $this->dateToStrYm($start_year, $start_month)."-".$this->dateToStrYm($toDate["year"], $toDate["month"]);
            $dateScopeStrHyphen = $this->dateToStrYm($start_year, $start_month, "-")." - ".$this->dateToStrYm($toDate["year"], $toDate["month"], "-");
        }
        
        // サイト名取得
        // NC2のバグにより日英中以外のサイト名情報がDBに無いため、その他の言語の場合は英語のサイト名を使用する
        $params = array();
        $params[] = $language;
        $result = $this->executeSqlFile(dirname(__FILE__)."/config_language_select_getSiteName.sql", $params);
        if(count($result) == 0) {
            $params = array();
            $params[] = "english";
            $result = $this->executeSqlFile(dirname(__FILE__)."/config_language_select_getSiteName.sql", $params);
        }
        $site_name = $result[0]["conf_value"];
        
        // レポートファイル作成
        $this->createReport($tmp_dir, $organization_id, $start_year, $start_month, $toMonths, $dateScopeStr, $dateScopeStrHyphen, $site_name, $language);
        // レポートファイル圧縮
        $zip_name = "SiteLicenseUserReport_". $dateScopeStr. ".zip";
        $this->compressToZip($zip_dir, $zip_name, $tmp_dir);
        // 送信
        $this->sendSitelicenseReport($request_id, $organization_id, $mail_no, $zip_dir, $zip_name, $dateScopeStrHyphen, $site_name, $language);
    }
    
    /**
     * Create usagestatistics report file
     * 利用統計レポートファイル作成
     *
     * @param string $tmp_dir                 report file temporary directory            レポートファイル出力先一時ディレクトリ
     * @param int    $organization_id         sitelicense organization ID                サイトライセンス機関ID
     * @param int    $start_year              aggregate year                             集計年
     * @param int    $start_month             aggregate month                            集計月
     * @param int    $toMonths                aggregate range                            集計範囲(単位:ヶ月)
     * @param string $dateScopeStr            aggregate range string (YYYYMM-YYYYMM)     集計範囲文字列(YYYYMM-YYYYMM)
     * @param string $dateScopeStrHyphenSpace aggregate range string (YYYY-MM - YYYY-MM) 集計範囲文字列(YYYY-MM - YYYY-MM)
     * @param string $site_name               site name                                  サイト名
     * @param string $language                language                                   表示言語
     * @throws AppException
     */
    private function createReport($tmp_dir, $organization_id, $start_year, $start_month, $toMonths, $dateScopeStr, $dateScopeStrHyphenSpace, $site_name, $language) {
        // 機関名取得
        $params = array();
        $params[] = $organization_id; // サイトライセンス機関ID
        $result = $this->executeSqlFile(dirname(__FILE__)."/sitelicense_info_select_getDetail.sql", $params);
        $organization_name = $result[0]["organization_name"];
        
        // 検索レポートを作成する
        $this->createSearchReport($tmp_dir, $organization_id, $site_name, $start_year, $start_month, $toMonths, $dateScopeStr, $dateScopeStrHyphenSpace);
        // ダウンロードレポートを作成する
        $this->createDownloadReport($tmp_dir, $organization_id, $site_name, $start_year, $start_month, $toMonths, $dateScopeStr, $dateScopeStrHyphenSpace, $language);
        // 利用統計レポートを作成する
        $this->createUsagestaticsReport($tmp_dir, $organization_id, $site_name, $start_year, $start_month, $toMonths, $dateScopeStr, $dateScopeStrHyphenSpace, $language);
    }
    
    /**
     * Execute send mail
     * メール送信実行処理
     *
     * @param int    $request_id      reuqest ID                                 リクエストID
     * @param int    $organization_id sitelicense organization id                サイトライセンス機関ID
     * @param int    $mail_no         mail number                                送信先メール通番
     * @param string $zip_dir         ZIP file directory                         ZIPが存在するディレクトリ
     * @param string $zip_name        ZIP file name                              ZIPファイル名
     * @param string $dateScopeStr    aggregate range string (YYYY-MM - YYYY-MM) 集計範囲文字列(YYYY-MM - YYYY-MM)
     * @param string $site_name       site name                                  サイト名
     * @param string $language        language                                   言語
     * @throws AppException
     */
    private function sendSitelicenseReport($request_id, $organization_id, $mail_no, $zip_dir, $zip_name, $dateScopeStr, $site_name, $language) {

        // 管理者メールアドレス取得
        $result = $this->executeSqlFile(dirname(__FILE__)."/config_select_getAdminMailAddress.sql");
        $admin_mail = $result[0]["conf_value"];
        // 送信対象情報取得
        $params = array();
        $params[] = $organization_id; // サイトライセンス機関ID
        $organization_info = $this->executeSqlFile(dirname(__FILE__)."/sitelicense_info_select_getDetail.sql", $params);
        
        // 件名
        $subject = $this->createMailSubject($site_name, $dateScopeStr);
        // 本文
        $body = $this->createMailBody($site_name, $organization_info[0]["organization_name"], $admin_mail, $dateScopeStr);
        // 添付ファイル
        $attachment = array();
        $attachment[0] = $zip_dir.$zip_name;          // ファイルフルパス
        $attachment[1] = basename($zip_name, ".zip"); // ファイル名（拡張子無し）
        $attachment[2] = $zip_name;                   // ファイル名（拡張子有り）
        $attachment[3] = "base64";                    // エンコーディング形式
        $attachment[4] = "application/zip";           // Content-Type
        $attachment[5] = false;                       // バイナリフラグ
        $attachment[6] = "attachment";                // Content-Disposition
        $attachment[7] = "";                          // Content-ID
        
        // 送信先メールアドレス取得
        $params = array();
        if($request_id > 0) {
            $query = "SELECT * FROM " . $this->selectSendStatusTable($request_id) .
                     "WHERE request_id = ? ".
                     "AND organization_id = ? ".
                     "AND mail_no = ? ;";
            $params[] = $request_id;
            $params[] = $organization_id;
            $params[] = $mail_no;
        } else {
            $query = "SELECT * FROM " . $this->selectSendStatusTable($request_id) .
                     "WHERE organization_id = ? ".
                     "AND mail_no = ? ;";
            $params[] = $organization_id;
            $params[] = $mail_no;
        }
        $mail_address = $this->executeSql($query, $params);
        
        // 送信処理
        $mailClass = BusinessFactory::getFactory()->getBusiness("businessSendmail");
        try {
            // 送信先設定
            $destination = array();
            $destination[0]["handle"] = $organization_info[0]["organization_name"];
            $destination[0]["email"] = $mail_address[0]["mail_address"];
            // 送信実行
            $mailClass->sendMail($language, $destination, $subject, $body, $attachment);
            $this->updateSitelicenseSendStatus($request_id, $organization_id, $mail_address[0]["mail_no"], 1);
        } catch(AppException $e) {
            $this->errorLog("Send mail failed. [organization_id] : ". $organization_id. ", [mail_address] : ". $mail_address[0]["mail_address"], __FILE__, __CLASS__, __LINE__);
            $this->updateSitelicenseSendStatus($request_id, $organization_id, $mail_no, -1);
            throw $e;
        }
    }
    
    /**
     * Output search number report
     * 検索回数レポート出力処理
     *
     * @param string $tmp_dir            report file temporary directory            レポートファイル出力先一時ディレクトリ
     * @param int    $organization_id    sitelicense organization ID                サイトライセンス機関ID
     * @param string $site_name          site name                                  サイト名
     * @param int    $start_year         aggregate year                             集計年
     * @param int    $start_month        aggregate month                            集計月
     * @param int    $toMonths           aggregate range                            集計範囲(単位:ヶ月)
     * @param string $dateScopeStr       aggregate range string (YYYYMM-YYYYMM)     集計範囲文字列(YYYYMM-YYYYMM)
     * @param string $dateScopeStrHyphen aggregate range string (YYYY-MM - YYYY-MM) 集計範囲文字列(YYYY-MM - YYYY-MM)
     * @throws AppException
     */
    private function createSearchReport($tmp_dir, $organization_id, $site_name, $start_year, $start_month, $toMonths, $dateScopeStr, $dateScopeStrHyphen) {
        // レポート文面の作成
        $log_file = $tmp_dir."SearchReport_".$dateScopeStr.".tsv";

        // ヘッダー部
        $report_header = $this->smartyAssign->getLang("repository_sitelicense_mail_body_title")."\t".$site_name."\r\n". // サイト名
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_create_date")."\t".date("Y-m-d")."\r\n". // レポート作成日時
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_month")."\t".$dateScopeStrHyphen."\r\n"; // 集計対象年月日
        // データ列ヘッダー部
        $report_data_header = "\t".$this->smartyAssign->getLang("repository_sitelicense_mail_body_interface_name")."\t"; // "インターフェース名"
        // データ列データ部
        $report_body = $this->smartyAssign->getLang("repository_sitelicense_mail_body_weko_database")."\t". // "WEKOデータベース"
                       $site_name."\t"; // サイト名

        // 検索回数を取得
        $cntArray = array();
        $sumCnt = 0;
        for($ii = 0; $ii < $toMonths; $ii++) {
            $nextDate = $this->createNextDateStr($start_year, $start_month, $ii+1);
            $result = $this->searchExecuteSearchingCnt($organization_id, $nextDate["year"], $nextDate["month"]);
            $cntArray[$ii]["year"] = $nextDate["year"]; // 集計対象年
            $cntArray[$ii]["month"] = sprintf('%02d',$nextDate["month"]); // 集計対象月
            $cntArray[$ii]["cnt"] = (count($result) > 0) ? $result[0]["cnt"] : 0; // 検索回数
            $sumCnt += $cntArray[$ii]["cnt"]; // 合計数に追加
        }

        if(count($cntArray) == 1) {
            // 集計対象が1か月分の場合はデータ単独のみ
            $report_data_header .= sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_search_keyword_date"), $cntArray[0]["year"]."-".$cntArray[0]["month"])."\r\n"; // "キーワード検索数"
            $report_body .= $sumCnt;
        } else {
            // 合計値
            $report_data_header .= $this->smartyAssign->getLang("repository_sitelicense_mail_body_search_keyword_sum")."\t"; // "キーワード検索数(合計)"
            $report_body .= $sumCnt."\t";
            // 各月の検索回数
            for($ii = 0; $ii < count($cntArray); $ii++) {
                if($ii > 0) {
                    $report_data_header .= "\t";
                    $report_body .= "\t";
                }
                $report_data_header .= sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_search_keyword_date"), $cntArray[$ii]["year"]."-".$cntArray[$ii]["month"]);
                $report_body .= $cntArray[$ii]["cnt"];
            }
            $report_data_header .= "\r\n";
        }

        // レポートファイル作成
        $report = $report_header. $report_data_header. $report_body;
        $this->createReportFile($log_file, $report);
    }
    
    /**
     * Output download number report
     * ダウンロード回数レポート出力処理
     *
     * @param string $tmp_dir            report file temporary directory            レポートファイル出力先一時ディレクトリ
     * @param int    $organization_id    sitelicense organization ID                サイトライセンス機関ID
     * @param string $site_name          site name                                  サイト名
     * @param int    $start_year         aggregate year                             集計年
     * @param int    $start_month        aggregate month                            集計月
     * @param int    $toMonths           aggregate range                            集計範囲(単位:ヶ月)
     * @param string $dateScopeStr       aggregate range string (YYYYMM-YYYYMM)     集計範囲文字列(YYYYMM-YYYYMM)
     * @param string $dateScopeStrHyphen aggregate range string (YYYY-MM - YYYY-MM) 集計範囲文字列(YYYY-MM - YYYY-MM)
     * @param string $language           language                                   表示言語
     * @throws AppException
     */
    private function createDownloadReport($tmp_dir, $organization_id, $site_name, $start_year, $start_month, $toMonths, $dateScopeStr, $dateScopeStrHyphen, $language) {
        $end_date = $this->createNextDateStr($start_year, $start_month, $toMonths);
        // レポート文面の作成
        $log_file = $tmp_dir. "DownloadReport_".$dateScopeStr.".tsv";
        $report_header = $this->smartyAssign->getLang("repository_sitelicense_mail_body_title")."\t".$site_name."\r\n".
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_create_date")."\t".date("Y-m-d")."\r\n".
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_month")."\t".$dateScopeStrHyphen."\r\n".
                         "\t".
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_setspec")."\t".
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_interface_name")."\t".
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_online_issn")."\t";
        
        // 指定の期間内に存在する全てのISSNを取得する
        $params = array();
        $params[] = $organization_id;
        $params[] = Repository_Components_Business_Logmanager::LOG_OPERATION_DOWNLOAD_FILE;
        $params[] = $start_year. sprintf("%02d", $start_month);
        $params[] = $end_date["year"]. sprintf("%02d", $end_date["month"]);
        $issn = $this->executeSqlFile(dirname(__FILE__)."/sitelicense_dlview_select_issn.sql", $params);
        // 表示言語設定
        $jtitle_lang = ($language == "english") ? "journal_name_en" : "journal_name_ja";
        // ISSNについて対象年月の回数を取得する
        // $cntArray[$ii]["online_issn"] = ONLINE ISSN
        //               ["journal_name"] = インデックス名
        //               ["set_spec] = set spec
        //               ["download"]["YYYY-MM"] = 対象年月の集計数
        // ISSN・年月毎のデータを取得
        $cntArray = $this->searchOperationCntByIssn($organization_id, $start_year, $start_month, $toMonths, $issn, $jtitle_lang);
        
        // ヘッダー部文字列作成
        if($toMonths == 1) {
            $report_header .= sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_file_download_date"), $this->dateToStrYm($start_year, sprintf("%02d", $start_month), "-"))."\r\n"; // "ダウンロード回数"
        } else {
            $report_header .= $this->smartyAssign->getLang("repository_sitelicense_mail_body_file_download_sum")."\t"; // "ダウンロード回数(合計)"
            for($ii = 0; $ii < $toMonths; $ii++) {
                if($ii > 0) { $report_header .= "\t"; }
                $nextDate = $this->createNextDateStr($start_year, $start_month, $ii+1);
                $report_header .= sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_file_download_date"), $this->dateToStrYm($nextDate["year"], $nextDate["month"], "-")); // "ダウンロード回数(YYYY-MM)"
            }
            $report_header .= "\r\n";
        }
        // データ部文字列作成
        $report_body = $this->createBodyRow($cntArray, $site_name, $toMonths, false);
        
        // ヘッダー部とデータ部を結合
        $report = $report_header . $report_body;
        // レポートファイル作成
        $this->createReportFile($log_file, $report);
    }
    
    /**
     * Output usage statistics report
     * 利用統計レポート出力処理
     *
     * @param string $tmp_dir            report file temporary directory            レポートファイル出力先一時ディレクトリ
     * @param int    $organization_id    sitelicense organization ID                サイトライセンス機関ID
     * @param string $site_name          site name                                  サイト名
     * @param int    $start_year         aggregate year                             集計年
     * @param int    $start_month        aggregate month                            集計月
     * @param int    $toMonths           aggregate range                            集計範囲(単位:ヶ月)
     * @param string $dateScopeStr       aggregate range string (YYYYMM-YYYYMM)     集計範囲文字列(YYYYMM-YYYYMM)
     * @param string $dateScopeStrHyphen aggregate range string (YYYY-MM - YYYY-MM) 集計範囲文字列(YYYY-MM - YYYY-MM)
     * @param string $language           language                                   表示言語
     * @throws AppException
     */
    private function createUsagestaticsReport($tmp_dir, $organization_id, $site_name, $start_year, $start_month, $toMonths, $dateScopeStr, $dateScopeStrHyphen, $language) {
        $end_date = $this->createNextDateStr($start_year, $start_month, $toMonths);
        // レポート文面の作成
        // ヘッダー部
        $log_file = $tmp_dir. "UsagestatisticsReport_".$dateScopeStr.".tsv";
        $report_header = $this->smartyAssign->getLang("repository_sitelicense_mail_body_title")."\t".$site_name."\r\n".
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_create_date")."\t".date("Y-m-d")."\r\n".
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_month")."\t".$dateScopeStrHyphen."\r\n".
                         "\t".
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_setspec")."\t".
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_interface_name")."\t".
                         $this->smartyAssign->getLang("repository_sitelicense_mail_body_online_issn")."\t";
        // 指定の期間内に存在する全てのISSNを取得する
        $params = array();
        $params[] = $organization_id;
        $params[] = Repository_Components_Business_Logmanager::LOG_OPERATION_DOWNLOAD_FILE;
        $params[] = $start_year. sprintf("%02d", $start_month);
        $params[] = $end_date["year"]. sprintf("%02d", $end_date["month"]);
        $issn = $this->executeSqlFile(dirname(__FILE__)."/sitelicense_dlview_select_issn.sql", $params);
        // 表示言語設定
        $jtitle_lang = ($language == "english") ? "journal_name_en" : "journal_name_ja";
        // ISSN・年月毎のデータを取得
        $cntArray = $this->searchOperationCntByIssn($organization_id, $start_year, $start_month, $toMonths, $issn, $jtitle_lang, true);
        
        // ヘッダー部文字列作成
        if($toMonths == 1) {
            $report_header .= sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_view_date"), $this->dateToStrYm($start_year, sprintf("%02d", $start_month), "-"))."\t"; // "詳細画面閲覧数"
            $report_header .= sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_file_download_date"), $this->dateToStrYm($start_year, sprintf("%02d", $start_month), "-"))."\r\n"; // "ダウンロード回数"
        } else {
            $report_header_view = $this->smartyAssign->getLang("repository_sitelicense_mail_body_view_sum")."\t"; // "詳細画面閲覧数(合計)"
            $report_header_download = $this->smartyAssign->getLang("repository_sitelicense_mail_body_file_download_sum")."\t"; // "ダウンロード回数(合計)"
            for($ii = 0; $ii < $toMonths; $ii++) {
                if($ii > 0) {
                    $report_header_view .= "\t";
                    $report_header_download .= "\t";
                }
                $nextDate = $this->createNextDateStr($start_year, $start_month, $ii+1);
                $report_header_view .= sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_view_date"), $this->dateToStrYm($nextDate["year"], $nextDate["month"], "-")); // "詳細画面閲覧数(YYYY-MM)"
                $report_header_download .= sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_file_download_date"), $this->dateToStrYm($nextDate["year"], $nextDate["month"], "-")); // "ダウンロード回数(YYYY-MM)"
            }
            $report_header .= $report_header_view."\t".$report_header_download. "\r\n";
        }
        // データ部文字列作成
        $report_body = $this->createBodyRow($cntArray, $site_name, $toMonths);
        
        // ヘッダー部とデータ部を結合
        $report = $report_header. $report_body;
        // レポートファイル作成
        $this->createReportFile($log_file, $report);
    }
    
    /**
     * Get search number by organization, year, month
     * 機関・年月を指定して検索回数取得
     *
     * @param int    $organization_id sitelicense organization ID サイトライセンス機関ID
     * @param int    $year            aggregate year              集計年
     * @param int    $month           aggregate month             集計月
     * @return array $result          result                      取得結果 array[0]["organization_id"]
     *                                 array[0]["year"|"month"|"cnt"]
     */
    private function searchExecuteSearchingCnt($organization_id, $year, $month) {
        // 機関・年・月を指定して検索回数を取得する
        $result = $this->executeSqlFile(dirname(__FILE__)."/sitelicense_usage_searchkeyword_select_searchCount.sql", func_get_args());
        
        return $result;
    }
    
    /**
     * Get search number by organization, year, month
     * 機関・年月を指定して検索回数取得
     *
     * @param int    $organization_id sitelicense organization ID サイトライセンス機関ID
     * @param int    $year            aggregate year              集計年
     * @param int    $month           aggregate month             集計月
     * @param int    $operation_id    operation ID                操作ID
     * @param string $issn            ISSN string                 ISSN文字列(XXXX-XXXX,YYYY-YYYY,...)
     * @return array $result          result                      取得結果
     *                                 array[0]["organization_id"|"year"|"month"|"operation_id"|"online_issn"|"journal_name_ja"|"journal_name_en"|"setspec"|"cnt"]
     */
    private function searchOperationCnt($organization_id, $year, $month, $operation_id, $issn) {
        // 機関・年・月・操作ID・ISSNを指定して操作回数を取得する
        return $this->executeSqlFile(dirname(__FILE__)."/sitelicense_dlview_select_operationCount.sql", func_get_args());
    }
    
    /**
     * create mail subject string
     * メール件名文字列作成
     *
     * @param  string $site_name site name                              サイト名
     * @param  string $dateScope aggregate range string (YYYYMM-YYYYMM) 集計範囲文字列(YYYY-MM-YYYY-MM)
     * @return string $subject   mail subject                           メール件名文字列
     */
    private function createMailSubject($site_name, $dateScope) {
        $subject = "[". $site_name. "] ".
                   $dateScope.
                   " ".$this->smartyAssign->getLang("repository_sitelicense_mail_subject");
        
        return $subject;
    }
    
    /**
     * create mail body string
     * メール本文文字列作成
     *
     * @param  string  $site_name         site name                              サイト名
     * @param  string  $organization_name sitelicense organization ID            サイトライセンス機関名
     * @param  string  $admin_mail        site administrator mail address        サイト管理者メールアドレス
     * @param  string  $dateScopeStr      aggregate range string (YYYYMM-YYYYMM) 集計範囲文字列(YYYY-MM-YYYY-MM)
     * @return string  $body              mail body                              メール本文文字列
     */
    private function createMailBody($site_name, $organization_name, $admin_mail, $dateScopeStr) {
        // 本文
        $body = sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_dear"), $organization_name)."\n\n".
                sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_thank"), $site_name)."\n".
                sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_announcement"), $dateScopeStr)."\n\n".
                sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_body_unnecessary"), $admin_mail)."\n\n\n".
                $this->smartyAssign->getLang("repository_sitelicense_mail_body_explain_rule_1")."\n".
                $this->smartyAssign->getLang("repository_sitelicense_mail_body_explain_rule_2")."\n\n".
                $this->smartyAssign->getLang("repository_sitelicense_mail_body_explain_format")."\n\n".
                $this->smartyAssign->getLang("repository_sitelicense_mail_body_explain_all_file")."\n\n".
                $this->smartyAssign->getLang("repository_sitelicense_mail_body_explain_search_report_1")."\n".
                $this->smartyAssign->getLang("repository_sitelicense_mail_body_explain_search_report_2")."\n\n".
                $this->smartyAssign->getLang("repository_sitelicense_mail_body_explain_download_report_1")."\n".
                $this->smartyAssign->getLang("repository_sitelicense_mail_body_explain_download_report_2")."\n\n".
                $this->smartyAssign->getLang("repository_sitelicense_mail_body_explain_usagestatistics_1")."\n".
                $this->smartyAssign->getLang("repository_sitelicense_mail_body_explain_usagestatistics_2")."\n\n\n".
                "------------------------------------------------"."\n".
                sprintf($this->smartyAssign->getLang("repository_sitelicense_mail_footer_contact"), $site_name)."\n".
                $admin_mail;
        
        return $body;
    }

    /**
     * Return after a few months
     * 指定した年月のNヶ月後の年月を返す
     *
     * @param int    $year    year                年
     * @param int    $month   month               月
     * @param int    $process month after number  「Nヶ月後」の指定数字
     * @return array date 年月
     *                array["year"]
     *                     ["month"]
     */
    private function createNextDateStr($year, $month, $process) {
        $nextDate = explode("-", date("Y-n", strtotime($year."-".$month." +".($process-1)." month")));

        return array("year" => $nextDate[0], "month" => $nextDate[1]);
    }

    /**
     * Convert date to string
     * 年月の数字を0埋め文字列に変換する
     *
     * @param int    $year      year      年
     * @param int    $month     month     月
     * @param string $delimiter delimiter 年月の区切り文字
     * @return string date string         年月文字列
     */
    private function dateToStrYm($year, $month, $delimiter="") {
        return date("Y".$delimiter."m", strtotime($year."-".$month));
    }

    /**
     * create physical report file
     * レポート実ファイル作成処理
     *
     * @param string $file_name file name      ファイル名
     * @param string $file_body file body      ファイル内容
     * @return bool  true/false success/failed 作成成功/作成失敗
     */
    private function createReportFile($file_name, $file_body) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $BOM = pack('C*',0xEF,0xBB,0xBF);
        $logReport = fopen($file_name, "w");
        fwrite($logReport, $BOM.$file_body);
        fclose($logReport);
        
        return true;
    }
    
    /**
     * compress to ZIP
     * ZIP圧縮処理
     *
     * @param string $zip_dir   output ZIP directory ZIPファイル出力先ディレクトリ
     * @param string $zip_name  output ZIP name      ZIPファイル名
     * @param string $file_path source directory     圧縮元ディレクトリ
     */
    private function compressToZip($zip_dir, $zip_name, $file_path) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // 一時ディレクトリを送付用のZIPに圧縮する
        $output_files = array($file_path);
        File_Archive::extract($output_files, 
                              File_Archive::toArchive($zip_name, 
                                                      File_Archive::toFiles($zip_dir)
                                                     )
                             );
    }

    /**
     * Get operation count by issn
     * ISSNから操作実施回数を取得する
     *
     * @param int     $organization_id sitelicense organization ID サイトライセンス機関ID
     * @param int     $start_year      aggregate year              集計年
     * @param int     $start_month     aggregate month             集計月
     * @param int     $toMonths        aggregate range             集計範囲（単位：ヶ月）
     * @param string  $issn            online ISSN                 ONLINE ISSN値
     * @param string  $jtitle_lang     journal name language       雑誌名(日・英)
     * @param bool    $viewFlag        view number aggregate flag  閲覧回数の集計実施フラグ
     * @return array  $dateArray       operation number data       操作回数データ
     *                                  array[0]["online_issn"|
     *                                           "journal_name"|
     *                                           "set_spec"|
     *                                           "cntByDate"]["YYYY-MM"]["download"|"view"]
     */
    private function searchOperationCntByIssn($organization_id, $start_year, $start_month, $toMonths, $issn, $jtitle_lang, $viewFlag=true) {
        // 回数データを配列に1つの配列に統合する
        $dataArray = array();
        for($ii = 0; $ii < count($issn); $ii++) {
            $dataArray[$ii]["online_issn"] = $issn[$ii]["online_issn"]; // ISSN
            $dataArray[$ii]["journal_name"] = $issn[$ii][$jtitle_lang];   // インデックス名
            $dataArray[$ii]["set_spec"] = $issn[$ii]["setspec"];        //  set spec
            // 月別のカウント
            for($jj = 0; $jj < $toMonths; $jj++) {
                $nextDate = $this->createNextDateStr($start_year, $start_month, $jj+1);
                $download = $this->searchOperationCnt($organization_id, $nextDate["year"], $nextDate["month"], Repository_Components_Business_Logmanager::LOG_OPERATION_DOWNLOAD_FILE, $issn[$ii]["online_issn"]);
                $dataArray[$ii]["cntByDate"][$this->dateToStrYm($nextDate["year"], sprintf('%02d',$nextDate["month"]), "-")]["download"] = (count($download) > 0) ? $download[0]["cnt"] : 0;
                if($viewFlag) {
                    $view = $this->searchOperationCnt($organization_id, $nextDate["year"], sprintf('%02d',$nextDate["month"]), Repository_Components_Business_Logmanager::LOG_OPERATION_DETAIL_VIEW, $issn[$ii]["online_issn"]);
                    $dataArray[$ii]["cntByDate"][$this->dateToStrYm($nextDate["year"], sprintf('%02d',$nextDate["month"]), "-")]["view"] = (count($view) > 0) ? $view[0]["cnt"] : 0;
                }
            }
        }
        
        return $dataArray;
    }
    
    /**
     * Create data string
     * 集計データ文字列を作成する
     *
     * @param array  $cntArray          aggregate date              集計データ配列
     * @param string $organization_name aggregate year              集計年
     * @param int    $toMonths          aggregate range             集計範囲(単位:ヶ月)
     * @param bool   $viewFlag          view number aggregate flag  閲覧回数の集計実施フラグ
     * @return string $report_body
     */
    private function createBodyRow($cntArray, $organization_name, $toMonths, $viewFlag=true) {
        $report_body = "";
        for($ii = 0; $ii < count($cntArray); $ii++) {
            // インデックス情報部分
            $report_body .= $cntArray[$ii]["journal_name"]."\t"; // インデックス名
            $report_body .= $cntArray[$ii]["set_spec"]."\t"; // set spec
            $report_body .= $organization_name."\t"; // サイト名
            $report_body .= $cntArray[$ii]["online_issn"]."\t"; // ONLINE ISSN
            
            // 集計数部分
            $tmpNum = 0; // ループカウンタ
            // ダウンロードカウント用
            $tmpDownloadCntStr = "";
            $tmpDownloadCntSum = 0;
            if($viewFlag) {
                $tmpViewCntStr = "";
                $tmpViewCntSum = 0;
            }

            foreach($cntArray[$ii]["cntByDate"] as $aggregateDate => $cnt) {
                // ダウンロードカウント
                if($tmpNum > 0) { $tmpDownloadCntStr .= "\t"; }
                $tmpDownloadCntStr .= $cnt["download"];
                $tmpDownloadCntSum += $cnt["download"];
                // 閲覧数カウント
                if($viewFlag) {
                    if($tmpNum > 0) { $tmpViewCntStr .= "\t"; }
                    $tmpViewCntStr .= $cnt["view"];
                    $tmpViewCntSum += $cnt["view"];
                }
                $tmpNum++;
            }
            $tmpCntStr = ($toMonths > 1) ? $tmpDownloadCntSum."\t".$tmpDownloadCntStr : $tmpDownloadCntStr; // 複数月の場合は合計値も出力する
            if($viewFlag) {
                $viewCounts = ($toMonths > 1) ? $tmpViewCntSum."\t".$tmpViewCntStr : $tmpViewCntStr;
                $tmpCntStr = $viewCounts."\t".$tmpCntStr;
            }
            $report_body .= $tmpCntStr."\r\n";
        }
        
        return $report_body;
    }

    /**
     * Update sitelicense mail sending status
     * サイトライセンスメール送信状態を更新する
     *
     * @param int $request_id      request ID                     リクエストID
     * @param int $organization_id sitelicense organization ID    サイトライセンス機関ID
     * @param int $mail_no         mail number                    メール通番
     * @param int $result          result(1: success, -1: failed) 実行結果(1: 成功, -1: 失敗)
     * @throws DbException
     */
    private function updateSitelicenseSendStatus($request_id, $organization_id, $mail_no, $result) {
        $params = array();
        $params[] = $result;
        $params[] = $this->accessDate;
        $params[] = $organization_id;
        $params[] = $mail_no;
        $query = "UPDATE ". $this->selectsendStatusTable($request_id).
                 "SET send_status = ?, send_date = ? ".
                 "WHERE organization_id = ? ".
                 "AND mail_no = ? ";
        if($request_id > 0) {
            $query .= "AND request_id = ? ";
            $params[] = $request_id;
        }
        $this->executeSql($query, $params);
    }

    /**
     * Select sitelicense mail send status table name
     * サイトライセンスメール送信状態テーブル名を選択する
     *
     * @param int $request_id request ID リクエストID
     * @return string table name         テーブル名
     *                 {repository_sitelicense_mail_send_status} or {repository_sitelicense_mail_send_status_all}
     */
    private function selectsendStatusTable($request_id) {
        return (intval($request_id) > 0) ? " {repository_sitelicense_mail_send_status} " : " {repository_sitelicense_mail_send_status_all} ";
    }
}
?>