<?php

/**
 * Log management common classes
 * カスタムレポート共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Uploadfiles.class.php 48455 2015-02-16 10:53:40Z atsushi_suzuki $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * At the time the log aggregate common classes
 * ログ集計時共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/Logbase.class.php';

/**
 * Log management common classes
 * カスタムレポート共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Customreport extends Repository_Components_Business_Logbase 
{
    /**
     * each $per_log custom report data by repository_log
     * カスタムレポート
     *
     * @var array
     */
    private $customReportData = array();
    
    /**
     * Aggregate start year
     * 集計開始年
     *
     * @var int
     */
    private $sy_log = 0;
    /**
     * Aggregate start month
     * 集計開始月
     *
     * @var int
     */
    private $sm_log = 0;
    /**
     * Aggregate start day
     * 集計開始日
     *
     * @var int
     */
    private $sd_log = 0;
    
    /**
     * Aggregate end year
     * 集計開始年
     *
     * @var int
     */
    private $ey_log = 0;
    /**
     * Aggregate end month
     * 集計開始月
     *
     * @var int
     */
    private $em_log = 0;
    /**
     * Aggregate end day
     * 集計開始日
     *
     * @var int
     */
    private $ed_log = 0;
    
    /**
     * operation number for calc repository_log
     * アイテム登録集計
     * 
     * @var int
     */
    const TYPE_LOG_REGIST_ITEM = 1;
    /**
     * operation number for calc repository_log
     * ダウンロード集計
     * 
     * @var int
     */
    const TYPE_LOG_DOWNLOAD = 2;
    
    /**
     * operation number for calc repository_log
     * 詳細画面集計
     * 
     * @var int
     */
    const TYPE_LOG_VIEW = 3;
    
    /**
     * Aggregate interest
     * 集計対象
     * 
     * @var int
     */
    private $type_log = self::TYPE_LOG_DOWNLOAD;
    
    /**
     * Period
     * 集計期間
     *
     * @var int
     */
    const PER_LOG_DAY = 1;
    /**
     * Period
     * 集計期間
     *
     * @var int
     */
    const PER_LOG_WEEK = 2;
    /**
     * Period
     * 集計期間
     *
     * @var int
     */
    const PER_LOG_MONTH = 3;
    /**
     * Period
     * 集計期間
     *
     * @var int
     */
    const PER_LOG_YEAR = 4;
    /**
     * Period
     * 集計期間
     *
     * @var int
     */
    const PER_LOG_ITEM = 5;
    /**
     * Period
     * 集計期間
     *
     * @var int
     */
    const PER_LOG_HOST = 6;
    
    /**
     * Period
     * 集計期間
     *
     * @var int
     */
    private $per_log = self::PER_LOG_YEAR;
    
    /**
     * create Custom report by count type
     * カスタムレポート作成
     */
    protected function executeApp()
    {
        $this->traceLog("per_log: ". $this->per_log, __FILE__, __CLASS__, __LINE__);
        $this->traceLog("type_log: ". $this->type_log, __FILE__, __CLASS__, __LINE__);
        
        if($this->per_log == self::PER_LOG_ITEM)
        {
            // per item
            $this->createReportPerItem();
        }
        else if($this->per_log == self::PER_LOG_HOST)
        {
            // per host
            $this->createReportPerHost();
        }
        else 
        {
            // per_date
            $this->createReportPerDate();
        }
    }
    
    /**
     * create custom report by date and set $customReportData
     * 日付からカスタムレポートを作成
     */
    private function createReportPerDate()
    {
        // get date
        $date_list = array();
        $log_per_date = array();
        
        // 
        $date_list = $this->getTermArray($sy, $sm, $sd, $ey, $em, $ed);
    
        $su_db = ""; // next date by log files
        $log_per_date = $this->readRemovedLogFile($sy, $sm, $sd, $ey, $em, $ed, $date_list, $su_db);
    
        $this->makeBetweenDates($sy, $sm, $sd, $ey, $em, $ed, $su_db);
        
        $query="";
        
        switch ( $this->per_log ) {
            case self::PER_LOG_DAY:
                // per day
                $query = "SELECT t1.day, t2.cnt ".
                        " FROM ". DATABASE_PREFIX ."repository_date AS t1 ".
                        " LEFT JOIN ( ";
                $query .= $this->createSubQueryPerDate($sy, $sm, $sd, $ey, $em, $ed);
                $query .= " ) AS t2 ON ( t1.day = t2.d1 ) ";
                $ret = $this->Db->execute($query." WHERE cnt IS NOT NULL ORDER BY cnt DESC LIMIT 0 , 1 ");
                if($ret === false)
                {
                    $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                    throw new AppException($this->Db->ErrorMsg());
                }
                $query .= " ORDER BY t1.day ";
                break;
            case self::PER_LOG_WEEK:
                // per week
                $query = "SELECT t1.day, t1.day2, t2.cnt ".
                        " FROM ( ".
                        "   SELECT `day`, YEARWEEK( CAST( `day` AS DATE ) )  AS day2 ".
                        "   FROM ( ".
                        "       SELECT * FROM ".DATABASE_PREFIX ."repository_date ".
                        "       ORDER BY `day` ASC ".
                        "   ) AS t3 ".
                        "   GROUP BY `day2` ".
                        " ) AS t1 ".
                        " LEFT JOIN ( ";
                $query .= $this->createSubQueryPerDate($sy, $sm, $sd, $ey, $em, $ed);
                $query .= " ) AS t2 ON ( t1.day2 = t2.d1 ) ";
                $ret = $this->Db->execute($query." WHERE cnt IS NOT NULL ORDER BY cnt DESC LIMIT 0 , 1 ");
                if($ret === false)
                {
                    $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                    throw new AppException($this->Db->ErrorMsg());
                }
                $query .= " ORDER BY t1.day ";
                break;
            case self::PER_LOG_MONTH:
                // per month
                $query = "SELECT t1.day, t1.day2, t2.cnt ".
                        " FROM ( ".
                        "   SELECT `day`, SUBSTRING(CAST( day AS DATE), 1, 7) AS day2 ".
                        "   FROM ( ".
                        "       SELECT * FROM ".DATABASE_PREFIX ."repository_date ".
                        "       ORDER BY `day` DESC ".
                        "   ) AS t3 ".
                        "   GROUP BY `day2` ".
                        " ) AS t1 ".
                        " LEFT JOIN ( ";
                $query .= $this->createSubQueryPerDate($sy, $sm, $sd, $ey, $em, $ed);
                $query .= " ) AS t2 ON ( t1.day2 = t2.d1 ) ";
                $ret = $this->Db->execute($query." WHERE cnt IS NOT NULL ORDER BY cnt DESC LIMIT 0 , 1 ");
                if($ret === false)
                {
                    $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                    throw new AppException($this->Db->ErrorMsg());
                }
                $query .= " ORDER BY t1.day2 ";
                break;
            case self::PER_LOG_YEAR:
                // per year
                $query = "SELECT t1.day, t1.day2, t2.cnt ".
                        " FROM ( ".
                        "   SELECT `day`, SUBSTRING(CAST( day AS DATE), 1, 4) AS day2 ".
                        "   FROM ( ".
                        "       SELECT * FROM ".DATABASE_PREFIX ."repository_date ".
                        "       ORDER BY `day` DESC ".
                        "   ) AS t3 ".
                        "   GROUP BY `day2` ".
                        " ) AS t1 ".
                        " LEFT JOIN ( ";
                $query .= $this->createSubQueryPerDate($sy, $sm, $sd, $ey, $em, $ed);
                $query .= " ) AS t2 ON ( t1.day2 = t2.d1 ) ";
                $ret = $this->Db->execute($query." WHERE cnt IS NOT NULL ORDER BY cnt DESC LIMIT 0 , 1 ");
                if($ret === false)
                {
                    $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                    throw new AppException($this->Db->ErrorMsg());
                }
                $query .= " ORDER BY t1.day2 ";
                break;
            default:
                break;
        }
        
        $this->traceLog("query: ". $query, __FILE__, __CLASS__, __LINE__);
        
        // 各日付ごとのカウント結果を取得
        // 各日付のあとに、週、月、年を表す列が存在する
        $items = $this->Db->execute($query);
        if($items === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        $this->traceLog(print_r($items, true), __FILE__, __CLASS__, __LINE__);
        
        // 計算用の一時テーブルを削除
        $query = "DROP TEMPORARY TABLE IF EXISTS ".DATABASE_PREFIX."repository_date ";
        $ret = $this->Db->execute($query);
        if($ret === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // items merge log_per_date
//      print_r($log_per_date);
//      echo "<br/><br/>";
//      print_r($items);
        
        // この時点ではlog_per_dateにはログ退避した際の情報しかない
        // それをマージしている模様
        // 日や月は確実に分かれるため、週、年のみとなっている
        if($this->per_log == self::PER_LOG_WEEK || $this->per_log == self::PER_LOG_YEAR){
            // per week, per year
            if(count($log_per_date) > 0 && count($items) > 0){
                // $log_per_dateの末尾と$itemsの先頭が同じ週または年だった場合加算
                $log_cnt = count($log_per_date)-1;
                if(intval($log_per_date[$log_cnt]['day2']) == intval($items[0]['day2'])){
                    $items[0]['day'] = $log_per_date[$log_cnt]['day'];
                    $items[0]['cnt'] += $log_per_date[$log_cnt]['cnt'];
                    unset($log_per_date[$log_cnt]);
                }
            }
        }
        //$items = array_merge($log_per_date, $items);
        
        $this->customReportData = array_merge($log_per_date, $items);
    }
    
    /**
     * Create sub query per date
     * 年月週日ごとの集計用クエリのサブクエリを作成する
     *
     * @param string $sy Start year
     *                   集計開始年
     * @param string $sm Start month
     *                   集計開始月
     * @param string $sd Start day
     *                   集計開始日
     * @param string $ey End year
     *                   集計終了年
     * @param string $em End month
     *                   集計終了月
     * @param string $ed End day
     *                   集計終了日
     *
     * @return string Sub query for count query per date
     *                年月週日ごとの集計用クエリのサブクエリ
     *
     */
    private function createSubQueryPerDate($sy, $sm, $sd, $ey, $em, $ed)
    {
        $query = "";
        $dateColumn = "";
        
        // アイテム登録数の場合はログではなく、アイテム作成日時を参照
        if($this->type_log != self::TYPE_LOG_REGIST_ITEM)
        {
            $dateColumn = "LOG.record_date AS DATE";
        }
        else
        {
            $dateColumn = "ITEM.ins_date AS DATE";
        }
        
        // 年ごと、月ごと、週ごと、日ごとで日時の取得形式を変更
        switch ( $this->per_log ) {
            case self::PER_LOG_DAY:
                $query = "   SELECT CAST( ".$dateColumn." ) AS d1, count(*) AS cnt ";
                break;
            case self::PER_LOG_WEEK:
                $query = "   SELECT YEARWEEK( CAST( ".$dateColumn." ) ) AS d1, count(*) AS cnt ";
                break;
            case self::PER_LOG_MONTH:
                $query = "   SELECT SUBSTRING(CAST(".$dateColumn."), 1, 7) AS d1, count(*) AS cnt ";
                break;
            case self::PER_LOG_YEAR:
                $query = "   SELECT SUBSTRING(CAST(".$dateColumn."), 1, 4) AS d1, count(*) AS cnt ";
                break;
            default:
                break;
        }
        
        // アイテム登録数の場合はログではなく、アイテム作成日時を参照
        if($this->type_log != self::TYPE_LOG_REGIST_ITEM)
        {
            $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
            
            $query .= $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM];
            // Modify for remove IE Continuation log K.Matsuo 2011/11/17 --end-- 
            $query .= " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                      " AND LOG.record_date >= '$sy-$sm-$sd 00:00:00.000' ".
                      " AND LOG.record_date <= '$ey-$em-$ed 23:59:99.999' ".
                      " AND LOG.operation_id = '".$this->type_log."' ".
                      " AND LOG.item_id IS NOT NULL ".
                      " GROUP BY d1 ";
        }
        else
        {
            $query .= " FROM ".DATABASE_PREFIX."repository_item AS ITEM ".
                      " WHERE ITEM.ins_date >= '$sy-$sm-$sd 00:00:00.000' ".
                      " AND ITEM.ins_date <= '$ey-$em-$ed 23:59:99.999' ".
                      " GROUP BY d1 ";
        }
        return $query;
    }
    
    /**
     * create custom report by item and set $customReportData
     * アイテム毎のカスタムレポート作成
     */
    private function createReportPerItem()
    {
        // get date
        $date_list = array();
        $date_list = $this->getTermArray($sy, $sm, $sd, $ey, $em, $ed);
        
        $tmpDate = str_replace(" ", "", $this->accessDate);
        $tmpDate = str_replace(":", "", $tmpDate);
        $tmpDate = str_replace(".", "", $tmpDate);
        $query = "CREATE TEMPORARY TABLE `".DATABASE_PREFIX."repository_log_".$tmpDate."` ( ".
                " item_id INT, item_no INT, cnt INT default 0, ".
                " PRIMARY KEY(`item_id`, `item_no`) ".
                " ); ";
        $result = $this->Db->execute($query);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // ------------------------------------------
        // make log from log table
        // ------------------------------------------
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        $query=" INSERT INTO `".DATABASE_PREFIX."repository_log_".$tmpDate."` ".
                " SELECT LOG.item_id, LOG.item_no, count(*) AS cnt ";
        $query .= $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM];
        $query .= " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                  " AND LOG.record_date >= ? ". 
                  " AND LOG.record_date <= ? ".
                  " AND LOG.operation_id=? ".
                  " AND LOG.item_id IS NOT NULL ".
                  " GROUP BY item_id, item_no ";
        $params = array();
        $params[] = $sy. "-". $sm. "-". $sd. " 00:00:00.000";
        $params[] = $ey. "-". $em. "-". $ed. " 23:59:59.999";
        $params[] = $this->type_log;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // ------------------------------------------
        // read log file per item
        // ------------------------------------------
        $log_per_item = array();
        $year = $sy;
        $month = $sm;
        while(intval($year.$month) <= intval($ey.$em)){
            if(file_exists(WEBAPP_DIR."/logs/weko/logfile/log_per_item_$year$month.txt")){
                $fp = fopen(WEBAPP_DIR."/logs/weko/logfile/log_per_item_$year$month.txt", "r");
                while(!feof($fp)){
                    // read line
                    // record_date  item_id item_no download_count  view_count
                    $file_line = fgets($fp);
                    $file_line = str_replace("\r\n", "", $file_line);
                    $file_line = str_replace("\n", "", $file_line);
                    $line = split("\t", $file_line);
                    if(in_array($line[0], $date_list)){
                        $item_id = split("-", $line[1]);
                        $query = " INSERT INTO `".DATABASE_PREFIX."repository_log_".$tmpDate."` VALUES ".
                                " (?, ?, ?) ".
                                " ON DUPLICATE KEY UPDATE ".
                                " cnt = cnt + ".intval($line[$this->type_log])." ; ";
                        $params = array();
                        $params[] = $item_id[0];
                        $params[] = $item_id[1];
                        $params[] = $line[$this->type_log];
                        $result = $this->Db->execute($query, $params);
                        if($result === false){
                            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                            throw new AppException($this->Db->ErrorMsg());
                        }
                    }
                }
                fclose($fp);
            }
            //$date = mktime(0,0,0,$month+1,1,$year);
            //$year = date("Y", $date);
            //$month = date("m", $date);
            
            $query = "SELECT DATE_FORMAT(DATE_ADD(?, INTERVAL 1 MONTH), '%Y') AS tmp_year,".
                     " DATE_FORMAT(DATE_ADD(?, INTERVAL 1 MONTH), '%m') AS tmp_month;";
            $params = array();
            $params[] = $year."-".$month."-01";
            $params[] = $year."-".$month."-01";
            $result = $this->Db->execute($query, $params);
            if($result === false || count($result) != 1){
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
            $year = $result[0]['tmp_year'];
            $month = $result[0]['tmp_month'];
        }
        
        /* Mod add item_id to custom report 2012/8/17 Tatsuya.Koyasu -start- */
        $sqlCmd=" SELECT log.item_id, item.title, item.title_english, cnt ".
                " FROM `".DATABASE_PREFIX."repository_log_".$tmpDate."` AS log, ".DATABASE_PREFIX."repository_item AS item ". 
                " WHERE log.item_id = item.item_id ".
                " AND log.item_no = item.item_no ".
                " AND log.item_id IS NOT NULL ".
                " AND cnt > 0 ". 
                " ORDER BY cnt DESC, log.item_id ASC ";
        /* Mod add item_id to custom report 2012/8/17 Tatsuya.Koyasu -start- */
        $items = $this->Db->execute($sqlCmd);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $this->customReportData = $items;
        
        $query = "DROP TEMPORARY TABLE `".DATABASE_PREFIX."repository_log_".$tmpDate."` ;";
        $result = $this->Db->execute($query);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    
    /**
     * create custom report by host and set $customReportData
     * ホスト毎のカスタムレポート作成
     */
    private function createReportPerHost()
    {
        $CSV = "";
        $TSV = "";
        $html = "";
        $html_all = "";
        $cnt_all_host = 0;
        $cnt_host = 0;

        // get date
        $date_list = array();

        $date_list = $this->getTermArray($sy, $sm, $sd, $ey, $em, $ed);
        
        // ------------------------------------------
        // read log file per host
        // ------------------------------------------
        $log_per_host = array();
        $year = $sy;
        $month = $sm;
        while(intval($year.$month) <= intval($ey.$em)){
            if(file_exists(WEBAPP_DIR."/logs/weko/logfile/log_per_host_$year$month.txt")){
                $fp = fopen(WEBAPP_DIR."/logs/weko/logfile/log_per_host_$year$month.txt", "r");
                while(!feof($fp)){
                    // read line
                    // record_date  ip_address  host    item_count  download_count  view_count
                    $file_line = fgets($fp);
                    $file_line = str_replace("\r\n", "", $file_line);
                    $file_line = str_replace("\n", "", $file_line);
                    $line = split("\t", $file_line);
                    if(in_array($line[0], $date_list)){
                        if(array_key_exists($line[1], $log_per_host)){
                            $log_per_host[$line[1]]['cnt'] += $line[intval($this->type_log+2)];
                        } else if(intval($line[intval($this->type_log+2)]) > 0){
                            $log_per_host[$line[1]] = array();
                            $log_per_host[$line[1]]['host'] = $line[2];
                            $log_per_host[$line[1]]['ip_address'] = $line[1];
                            if(isset($log_per_host[$line[1]]['cnt']))
                            {
                                $log_per_host[$line[1]]['cnt'] += $line[intval($this->type_log+2)];
                            }
                            else
                            {
                                $log_per_host[$line[1]]['cnt'] = $line[intval($this->type_log+2)];
                            }
                        }
                    }
                }
                fclose($fp);
            }
            //$date = mktime(0,0,0,$month+1,1,$year);
            //$year = date("Y", $date);
            //$month = date("m", $date);
            
            $query = "SELECT DATE_FORMAT(DATE_ADD(?, INTERVAL 1 MONTH), '%Y') AS tmp_year,".
                     " DATE_FORMAT(DATE_ADD(?, INTERVAL 1 MONTH), '%m') AS tmp_month;";
            $params = array();
            $params[] = $year."-".$month."-01";
            $params[] = $year."-".$month."-01";
            $result = $this->Db->execute($query, $params);
            if($result === false || count($result) != 1){
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
            $year = $result[0]['tmp_year'];
            $month = $result[0]['tmp_month'];
        }

        // ------------------------------------------
        // get item download log per item
        // ------------------------------------------
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        $sqlCmd = "SELECT LOG.host AS host, LOG.ip_address AS ip_address, count(LOG.log_no) AS cnt ";
        $sqlCmd .= $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM];
        $sqlCmd .= " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                   " AND LOG.record_date >= ? ". 
                   " AND LOG.record_date <= ? ".
                   " AND LOG.operation_id = ? ".
                   " AND LOG.item_id IS NOT NULL ".
                   " GROUP BY LOG.ip_address ".
                   " ORDER BY cnt DESC , LOG.ip_address ASC; ";
        $params = array();
        $params[] = $sy. "-". $sm. "-". $sd. " 00:00:00.000";
        $params[] = $ey. "-". $em. "-". $ed. " 23:59:59.999";
        $params[] = $this->type_log;
        $items = $this->Db->execute($sqlCmd, $params);
        
        // マージ
        for($ii=0; $ii < count($items); $ii++){
            if(array_key_exists($items[$ii]['ip_address'], $log_per_host)){
                $log_per_host[$items[$ii]['ip_address']]['cnt'] += $items[$ii]['cnt'];
            } else {
                $log_per_host[$items[$ii]['ip_address']]['ip_address'] = $items[$ii]['ip_address'];
                $log_per_host[$items[$ii]['ip_address']]['host'] = $items[$ii]['host'];
                $log_per_host[$items[$ii]['ip_address']]['cnt'] = $items[$ii]['cnt'];
            }
        }
        
        if($this->type_log == self::TYPE_LOG_REGIST_ITEM)
        {
            $this->countRegistedItemByHarvestingPerHost($sy, $sm, $sd, $ey, $em, $ed, $log_per_host);
        }
        
        // ソート
        $items = array();
        foreach ($log_per_host as $ip => $val){
            array_push($items, $val);
            for($ii=count($items)-1;$ii>0;$ii--){
                if(intval($items[$ii-1]['cnt']) < intval($items[$ii]['cnt'])){
                    $tmp = $items[$ii-1];
                    $items[$ii-1] = $items[$ii];
                    $items[$ii] = $tmp;
                } else {
                    break;
                }
            }
        }
        
        $this->customReportData = $items;
    }
    
    /**
     * Count registed item by harvesting per host
     * ホストごとのハーベストアイテム登録数を集計する
     *
     * @param string $sy Start year
     *                   集計開始年
     * @param string $sm Start month
     *                   集計開始月
     * @param string $sd Start day
     *                   集計開始日
     * @param string $ey End year
     *                   集計終了年
     * @param string $em End month
     *                   集計終了月
     * @param string $ed End day
     *                   集計終了日
     * @param array $log_per_host Count log per host
     *                            ホストごとの集計数
     *                            array["172.18.72.168"|"test.repo.nii.ac.jp"|...]["ip_address"|"host"|"cnt"]
     *
     */
    private function countRegistedItemByHarvestingPerHost($sy, $sm, $sd, $ey, $em, $ed, &$log_per_host)
    {
        require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHarvesting.class.php';
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        $harvesting = new RepositoryHarvesting($session, $this->Db);
        $harvesting->TransStartDate = $this->accessDate;
        
        // ハーベスト対象のリポジトリ取得
        $harvestingInfo = array();
        $harvesting->getHarvestingTable($harvestingInfo);
        
        for($ii = 0; $ii < count($harvestingInfo); $ii++)
        {
            if(preg_match("/^https?:\/\/(([^\/]+)).*$/", $harvestingInfo[$ii]["base_url"], $matches) === 1 && $harvestingInfo[$ii]["post_index_id"] != 0)
            {
                $host = $matches[1];
                // ハーベスト登録先インデックス以下のハーベストアイテム登録数を取得
                $indexIdList = $harvestingInfo[$ii]["post_index_id"];
                $this->createChildIndexIdList($harvestingInfo[$ii]["post_index_id"], $indexIdList);
                $query = "SELECT COUNT(DISTINCT ITEM.item_id) AS cnt ".
                         " FROM ".DATABASE_PREFIX."repository_item AS ITEM ".
                         " INNER JOIN ". DATABASE_PREFIX. "repository_item_attr_type ATTR_TYPE ON ITEM.item_type_id = ATTR_TYPE.item_type_id ". 
                         " INNER JOIN ". DATABASE_PREFIX. "repository_item_attr ATTR ON ITEM.item_id = ATTR.item_id ". 
                         " INNER JOIN ". DATABASE_PREFIX. "repository_position_index POS ON ITEM.item_id = POS.item_id AND ITEM.item_no = POS.item_no ". 
                         " WHERE ITEM.ins_date >= ? ". 
                         " AND ITEM.ins_date <= ? ". 
                         " AND POS.index_id IN(".$indexIdList.") ";
                $params = array();
                $params[] = $sy. "-". $sm. "-". $sd. " 00:00:00.000";
                $params[] = $ey. "-". $em. "-". $ed. " 23:59:59.999";
                
                // ハーベスト先機関に紐付くアイテムを特定するための条件を追加する
                $this->addWherePhraseForHarvestItem($host, $query, $params);
                
                $query .= " ORDER BY cnt DESC; ";
                $result = $this->Db->execute($query, $params);
                if($result === false){
                    $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                    throw new AppException($this->Db->ErrorMsg());
                }
                
                // ホストごとに集計結果を保持
                $ip_address = gethostbyname($host);
                if(array_key_exists($host, $log_per_host)){
                    $log_per_host[$host]["cnt"] += $result[0]["cnt"];
                } else {
                    $log_per_host[$host]["ip_address"] = $ip_address;
                    $log_per_host[$host]["host"] = $host;
                    $log_per_host[$host]["cnt"] = $result[0]["cnt"];
                }
            }
        }
    }
    
    /**
     * Add a WHERE phrase to identify the items that attach straps to harvest destination repository
     * ハーベスト先機関に紐付くアイテムを特定するためのWHERE句を追加する
     *
     * @param string $host Hostname of harvest destination repository
     *                     ハーベスト先機関のホスト名
     * @param string $query Query statement to search for harvest items
     *                      ハーベストアイテムを検索するクエリ文
     * @param array $params Parameters query run-time
     *                      クエリ実行時のパラメータ
     *                      array[$ii]
     *
     */
    private function addWherePhraseForHarvestItem($host, &$query, &$params)
    {
        $query .= " AND ATTR.attribute_value LIKE ? ";
        $params[] = "oai:".$host.":%";
        
        // ハーベスト用アイテムタイプによってコンテンツIDの属性IDが変化するため、
        // 各アイテムタイプごとに条件を設定
        for($jj = 20001; $jj < 20018; $jj++)
        {
            if($jj == 20001)
            {
                $query .= " AND ( ";
            }
            
            $query .= " (ATTR_TYPE.item_type_id = ? ".
                      " AND ATTR_TYPE.attribute_id = ? ) ";
            $params[] = $jj;
            switch($jj)
            {
                case 20001:
                    $params[] = 16;
                    break;
                
                case 20002:
                case 20003:
                case 20004:
                case 20005:
                case 20006:
                case 20007:
                case 20008:
                case 20009:
                case 20010:
                case 20011:
                case 20012:
                case 20013:
                case 20014:
                case 20015:
                    $params[] = 53;
                    break;
                
                case 20016:
                    $params[] = 85;
                    break;
                
                case 20017:
                    $params[] = 36;
                    break;
                
                default:
                    $params[] = 0;
                    break;
            }
            
            if($jj < 20017)
            {
                $query .= " OR ";
            }
            else
            {
                $query .= " ) ";
            }
        }
    }
    
    /**
     * Create child index ID List in order to confirm whether the items 
     * that have been registered at the harvest repository
     * ハーベスト機関にて登録されたアイテムであるかを確認するため、
     * 指定インデックスの子インデックスIDリストを作成する
     *
     * @param int $parentIndexId Parent index ID
     *                           親インデックスのインデックスID
     * @param string $indexIdList Child index ID list comma delimited
     *                            指定インデックスの子インデックスIDリスト(カンマ区切り)
     *
     */
    private function createChildIndexIdList($parentIndexId, &$indexIdList)
    {
        // 親インデックスに所属するインデックスをすべて取得
        $query = "SELECT index_id " .
                 "FROM " . DATABASE_PREFIX . "repository_index " .
                 "WHERE parent_index_id = ? " . 
                 "ORDER BY show_order ;";
        
        $params = array();
        $params[] = $parentIndexId;
        
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        for ($ii = 0; $ii < count($result); $ii++)
        {
            if (strlen($indexIdList) == 0)
            {
                $indexIdList = $result[$ii]["index_id"];
            }
            else
            {
                $indexIdList .= ",".$result[$ii]["index_id"];
            }
            
            // 1階層深く探査
            $this->createChildIndexIdList($result[$ii]["index_id"], $indexIdList);
        }
    }
    
    // private method(international processing)
    /**
     * get calc custom report term
     * カスタムレポート集計期間を取得
     *
     * @param string $sy Aggregate start year 集計開始年
     * @param string $sm Aggregate start month 集計開始月
     * @param string $sd Aggregate start day 集計開始日
     * @param string $ey Aggregate end year 集計開始年
     * @param string $em Aggregate end month 集計開始月
     * @param string $ed Aggregate end day 集計開始日
     * @return array Custom Report Period カスタムレポート集計期間
     *               array[$ii]
     */
    private function getTermArray(&$sy, &$sm, &$sd, &$ey, &$em, &$ed)
    {
        // ------------------------------------------
        // set log start - end date
        // ------------------------------------------
        $sy = sprintf("%04d",$this->sy_log);
        $sm = sprintf("%02d",$this->sm_log);
        $sd = sprintf("%02d",$this->sd_log);
        $ey = sprintf("%04d",$this->ey_log);
        $em = sprintf("%02d",$this->em_log);
        $ed = sprintf("%02d",$this->ed_log);
        
        // get date
        $date_list = array();
        
        $s_date = $sy."-".$sm."-".$sd;
        $e_date = $ey."-".$em."-".$ed;
        $query = "SELECT DATEDIFF(?, ?) AS date_diff;";
        $params = array();
        $params[] = $e_date;
        $params[] = $s_date;
        $result = $this->Db->execute($query, $params);
        if($result === false || count($result) != 1){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        $diff = $result[0]['date_diff'];
        for ($i=0;$i<=$diff;$i++) {
            $query = "SELECT DATE_ADD(?, INTERVAL ? DAY) AS str_date;";
            $params = array();
            $params[] = $s_date;
            $params[] = $i;
            $result = $this->Db->execute($query, $params);
            if($result === false || count($result) != 1){
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
            array_push($date_list, $result[0]['str_date']);
        }
        
        return $date_list;
    }
    
    /**
     * read removed log by file and return count per date
     * 削除したログの集計数を取得する
     *
     * @param string $sy Aggregate start year 集計開始年
     * @param string $sm Aggregate start month 集計開始月
     * @param string $sd Aggregate start day 集計開始日
     * @param string $ey Aggregate end year 集計開始年
     * @param string $em Aggregate end month 集計開始月
     * @param string $ed Aggregate end day 集計開始日
     * @param array $date_list Date list 日付一覧
     *                         array[$ii]
     * @param string $su_db next date by log files ログファイルを調べる次の日付
     * @return array cnt array by date 日毎の集計数
     */
    private function readRemovedLogFile($sy, $sm, $sd, $ey, $em, $ed, $date_list, &$su_db)
    {
        $s_date = $sy."-".$sm."-".$sd;
        $e_date = $ey."-".$em."-".$ed;
        
        $log_per_date = array();
        
        // ------------------------------------------
        // read log file per date
        // ------------------------------------------
        //$su_db = $su;
        $su_db = $s_date;
        for($ii=$sy; $ii<=$ey; $ii++){
            if(file_exists(WEBAPP_DIR."/logs/weko/logfile/log_per_date_$ii.txt")){
                $date = "";
                $cnt = 0;
                $now_key = "";
                $old_key = "";
                $fp = fopen(WEBAPP_DIR."/logs/weko/logfile/log_per_date_$ii.txt", "r");
                $lastDateFlag = false;
                $isDumpDateInTerm = false;
                while(!feof($fp)){
                    // read line
                    // record_date  year_week   item_count  download_count  view_count
                    $file_line = fgets($fp);
                    $file_line = str_replace("\r\n", "", $file_line);
                    $file_line = str_replace("\n", "", $file_line);
                    $line = split("\t", $file_line);
                    if(in_array($line[0], $date_list)){
                        $isDumpDateInTerm = true;
                        // cntを合計するキーを取得
                        $now_key = "";
                        if($this->per_log == self::PER_LOG_DAY) {
                            // per date
                            $now_key = $line[0];
                        } else if($this->per_log == self::PER_LOG_WEEK) {
                            // per week
                            $now_key = $line[1];
                        } else if($this->per_log == self::PER_LOG_MONTH) {
                            // per month
                            $now_key = substr($line[0], 0, 7);
                            // 末日を保持
                            if(strlen($old_key) > 0)
                            {
                                $tmp = explode("-", $old_key);
                            }
                            else
                            {
                                $tmp[0] = $sy;
                                $tmp[1] = $sm;
                            }
                            //$date  = date("Y-m-d", mktime(0, 0, 0, $tmp[1]+1, 0, $tmp[0]));
                            $query = "SELECT LAST_DAY(?) AS str_date;";
                            $params = array();
                            $params[] = $tmp[0]. "-". $tmp[1]. "-01";
                            $result = $this->Db->execute($query, $params);
                            if($result === false || count($result) != 1){
                                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                                throw new AppException($this->Db->ErrorMsg());
                            }
                            $date = $result[0]['str_date'];
                            
                            if(intval(str_replace("-", "", $date)) > intval($ey.$em.$ed)){
                                $date = $ey."-". $em. "-". $ed;
                            }
                        } else if($this->per_log == self::PER_LOG_YEAR) {
                            // per year
                            $now_key = substr($line[0], 0, 4);
                            // 末尾を保持
                            if(intval($old_key."1231") < intval($ey.$em.$ed)){
                                $date = $old_key."-12-31";
                            } else {
                                $date = $ey."-".$em."-".$ed;
                            }
                        } else {
                            // else
                            continue;
                        }
                        
                        if(strlen($old_key) == 0){
                            // 初回のみ
                            $old_key = $now_key;
                            $date = $line[0];
                        } else if($old_key != $now_key){
                            // 合計するキーが変わった場合、前の情報を格納して初期化
                            array_push($log_per_date, array('day'=>$date, 'day2'=> $old_key, 'cnt'=>$cnt));
                            $old_key = $now_key;
                            $date = $line[0];
                            $cnt = 0;
                        }
                        
                        // cntを合計
                        $cnt += intval($line[intval($this->type_log+1)]);
                        
                        //$tmp = explode("-", $line[0]);
                        //$su_db = mktime(0,0,0,$tmp[1],$tmp[2]+1,$tmp[0]);
                        $query = "SELECT DATE_ADD(?, INTERVAL 1 DAY) AS str_date;";
                        $params = array();
                        $params[] = $line[0];
                        $result = $this->Db->execute($query, $params);
                        if($result === false || count($result) != 1){
                            return false;
                        }
                        $su_db = $result[0]['str_date'];
                        if(intval($ey.$em.$ed) <= intval(str_replace("-", "", $line[0]))){
                            array_push($log_per_date, array('day'=>$date, 'day2'=> $now_key, 'cnt'=>$cnt));
                            
                            $lastDateFlag = true;
                            // Excess period
                            break;
                        }
                    }
                }
                if(!$lastDateFlag && $isDumpDateInTerm){
                    array_push($log_per_date, array('day'=>$date, 'day2'=> $now_key, 'cnt'=>$cnt));
                }
                fclose($fp);
            }
        }
        
        return $log_per_date;
    }
    
    /**
     * create date table between start date and end date
     * 集計用に日付テーブルを作成する
     *
     * @param string $sy Aggregate start year 集計開始年
     * @param string $sm Aggregate start month 集計開始月
     * @param string $sd Aggregate start day 集計開始日
     * @param string $ey Aggregate end year 集計開始年
     * @param string $em Aggregate end month 集計開始月
     * @param string $ed Aggregate end day 集計開始日
     * @param array $date_list Date list 日付一覧
     *                         array[$ii]
     */
    private function makeBetweenDates($sy, $sm, $sd, $ey, $em, $ed, $su_db)
    {
        $e_date = $ey."-".$em."-".$ed;
        
        // ------------------------------------------
        // make between dates
        // ------------------------------------------
        $query = "DROP TEMPORARY TABLE IF EXISTS ".DATABASE_PREFIX."repository_date ";
        $ret = $this->Db->execute($query);
        if($ret === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        $query = "CREATE TEMPORARY TABLE ".DATABASE_PREFIX."repository_date ( ".
                "  `day` VARCHAR(23) ".
                " ) ";
        $ret = $this->Db->execute($query);
        if($ret === false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    
        $date_query = "SELECT DATEDIFF(?, ?) AS date_diff;";
        $params = array();
        $params[] = $e_date;
        $params[] = $su_db;
        
        $this->traceLog("e_date: ". $e_date, __FILE__, __CLASS__, __LINE__);
        $this->traceLog("su_db: ". $su_db, __FILE__, __CLASS__, __LINE__);
        
        $result = $this->Db->execute($date_query, $params);
        if($result === false || count($result) != 1){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        $diff = $result[0]['date_diff'];
        
        $this->traceLog("diff: ". $diff, __FILE__, __CLASS__, __LINE__);
        
        // ログ退避時のログファイルで全てのデータが作られない場合、
        // 日付ごとのカウンタ用テーブルを作成している
        if($diff > 0){
            $query = " INSERT INTO ".DATABASE_PREFIX."repository_date VALUES ";
            $params = array();
            for ($i=0;$i<=$diff;$i++) {
                if($i != 0){
                    $query .= " , ";
                }
                $date_query = "SELECT DATE_ADD(?, INTERVAL ? DAY) AS str_date;";
                $date_params = array();
                $date_params[] = $su_db;
                $date_params[] = $i;
                $result = $this->Db->execute($date_query, $date_params);
                if($result === false || count($result) != 1){
                    $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                    throw new AppException($this->Db->ErrorMsg());
                }
                $query .= " ( ? ) ";
                $params[] = $result[0]['str_date'];
            }
            $ret = $this->Db->execute($query, $params);
            if($ret === false){
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
        }
    }
    
    // getter
    /**
     * Get custom report
     * カスタムレポートを取得する
     *
     * @return array[$ii]["ip_address"|"host"|"cnt"]
     *         array[$ii]["day"|"day2"]
     *         array[$ii]["title"|"title_english"|"cnt"]
     */
    public function getCustomReportData(){  return $this->customReportData; }
    
    // setter
    /**
     * Set start year
     * 開始年を設定
     *
     * @param int $year
     */
    public function setStartYear($year){    $this->sy_log = $year;  }
    /**
     * Set start month
     * 開始月を設定
     *
     * @param int $year
     */
    public function setStartMonth($month){  $this->sm_log = $month; }
    /**
     * Set start day
     * 開始日を設定
     *
     * @param int $year
     */
    public function setStartDay($day){      $this->sd_log = $day;   }
    /**
     * Set end year
     * 終了年を設定
     *
     * @param int $year
     */
    public function setEndYear($year){      $this->ey_log = $year;  }
    /**
     * Set end month
     * 終了月を設定
     *
     * @param int $year
     */
    public function setEndMonth($month){    $this->em_log = $month; }
    /**
     * Set end day
     * 終了日を設定
     *
     * @param int $year
     */
    public function setEndDay($day){        $this->ed_log = $day;   }
    /**
     * Set count type
     * 集計形式を設定
     *
     * @param int $year
     */
    public function setCountType($type){    $this->type_log = $type;}
    /**
     * Set count per
     * 集計期間を設定
     *
     * @param int $year
     */
    public function setCountPer($per){      $this->per_log = $per;  }
}
?>