<?php

/**
 * Log aggregation subquery create a common class
 * ログ集計サブクエリ作成共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: LogAnalyzor.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * WEKO logic-based base class
 * WEKOロジックベース基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryLogicBase.class.php';

/**
 * Log aggregation subquery create a common class
 * ログ集計サブクエリ作成共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Loganalyzor extends RepositoryLogicBase
{
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $session Session management objects Session管理オブジェクト
     * @param RepositoryDbAccess $dbAccess DB object wrapper Class DBオブジェクトラッパークラス
     * @param string $transStartDate Transaction start date トランザクション開始日時
     */
    public function __construct($session, $dbAccess, $transStartDate)
    {
        parent::__construct($session, $dbAccess, $transStartDate);
    }

    /**
     * Create a subquery that does not aggregate the excluded IP address
     * 除外対象IPアドレスを集計しないサブクエリを作成
     * 
     * @param string $abbreviation Table alias テーブル別名
     * @return string Subquery サブクエリ
     */
    public function execlusiveIpAddressQuery($abbreviation) {
        $query = "SELECT param_value FROM ". DATABASE_PREFIX. "repository_parameter ".
                 "WHERE param_name = ? ;";
        $params = array();
        $params[] = "log_exclusion";
        $result = $this->dbAccess->executeQuery($query, $params);
        
        $ip_address = str_replace(array("\r\n", "\r", "\n"), ",", $result[0]["param_value"]);
        $ip_exclusion = "";
        $colomun_name = "";
        if(strlen($abbreviation) == 0) {
            $column_name = "ip_address";
        } else if(strlen($abbreviation) > 0) {
            $column_name = $abbreviation. ".ip_address";
        }
        if(strlen($ip_address) > 0) {
            $ip_exclusion = " AND ". $column_name. " NOT IN ('". $ip_address. "') ";
        }
        
        return $ip_exclusion;
    }
  
    /**
     * Subqueries created for the double access removal
     * 二重アクセス除去用のサブクエリ作成
     * 
     * @param int $operation_id Operation id 操作ID
     * @param string $abbreviation Table alias テーブル別名
     * @param string $start_date Aggregate start date and time 集計開始日時
     * @param string $finish_date Aggregate end date and time 集計終了日時
     * @return string Subquery サブクエリ
     */
    public function execlusiveDoubleAccessSubQuery($operation_id, $abbreviation, $start_date, $finish_date) {
        $sub_query = "";
        if($operation_id == RepositoryConst::LOG_OPERATION_DOWNLOAD_FILE) {
            if(strlen($abbreviation) == 0) {
                $sub_query = "SELECT *,DATE_FORMAT(record_date, '%Y%m%d%k%i') FROM ". DATABASE_PREFIX. "repository_log ".
                             "WHERE record_date >= '". $start_date. "' ".
                             "AND record_date <= '". $finish_date. "' ".
                             "AND operation_id = ". $operation_id. " ".
                             "GROUP BY DATE_FORMAT(record_date, '%Y%m%d%k%i'), item_id, item_no, attribute_id, file_no, ".
                             "user_agent, ip_address, operation_id, search_keyword, host, file_status, site_license, ".
                             "input_type, login_status, group_id ";
            } else {
                $sub_query = "SELECT *,DATE_FORMAT(record_date, '%Y%m%d%k%i') FROM ". DATABASE_PREFIX. "repository_log AS ". $abbreviation. " ".
                             "WHERE record_date >= '". $start_date. "' ".
                             "AND record_date <= '". $finish_date. "' ".
                             "AND operation_id = ". $operation_id. " ".
                             "GROUP BY DATE_FORMAT(record_date, '%Y%m%d%k%i'), item_id, item_no, attribute_id, file_no, ".
                             "user_agent, ip_address, operation_id, search_keyword, host, file_status, site_license, ".
                             "input_type, login_status, group_id ";
            }
        } else {
            $sub_query = DATABASE_PREFIX."repository_log";
        }
        
        return $sub_query;
    }
    
    /**
     * Creating a subquery to perform the rounding in the year
     * 年で丸め込みを行うサブクエリを作成
     *
     * @param string $abbreviation Table alias テーブル別名
     * @return string Subquery サブクエリ
     */
    public function dateformatYearQuery($abbreviation) {
        $year = "";
        if(strlen($abbreviation) == 0) {
            $year = " DATE_FORMAT(record_date, '%Y') AS YEAR ";
        } else if(strlen($abbreviation) > 0) {
            $year = " DATE_FORMAT(". $abbreviation. ".record_date, '%Y') AS YEAR ";
        }
        
        return $year;
    }
    
    /**
     * Subquery that grouping in year
     * 年でグルーピングするサブクエリ
     * 
     * @param string $abbreviation Table alias テーブル別名
     * @return string Subquery サブクエリ
     */
    public function perYearQuery() {
        $group_year = " GROUP BY YEAR ";
        
        return $group_year;
    }
    
    /**
     * Creating a subquery to perform the rounding in the month
     * 月で丸め込みを行うサブクエリを作成
     *
     * @param string $abbreviation Table alias テーブル別名
     * @return string Subquery サブクエリ
     */
    public function dateformatMonthlyQuery($abbreviation) {
        $monthly = "";
        if(strlen($abbreviation) == 0) {
            $monthly = " DATE_FORMAT(record_date, '%m') AS MONTHLY ";
        } else if(strlen($abbreviation) > 0) {
            $monthly = " DATE_FORMAT(". $abbreviation. ".record_date, '%m') AS MONTHLY ";
        }
        
        return $monthly;
    }
    
    /**
     * Subquery that grouping in month
     * 月でグルーピングするサブクエリ
     * 
     * @param string $abbreviation Table alias テーブル別名
     * @return string Subquery サブクエリ
     */
    public function perMonthlyQuery() {
        $group_monthly = " GROUP BY MONTHLY ";
        
        return $group_monthly;
    }
    
    /**
     * Creating a subquery to perform the rounding in the week
     * 週で丸め込みを行うサブクエリを作成
     *
     * @param string $abbreviation Table alias テーブル別名
     * @return string Subquery サブクエリ
     */
    public function dateformatWeeklyQuery($abbreviation) {
        $weekly = "";
        if(strlen($abbreviation) == 0) {
            $weekly = " DATE_FORMAT(record_date, '%U') AS WEEKLY ";
        } else if(strlen($abbreviation) > 0) {
            $weekly = " DATE_FORMAT(". $abbreviation. ".record_date, '%U') AS WEEKLY ";
        }
        
        return $weekly;
    }
    
    /**
     * Subquery that grouping in week
     * 週でグルーピングするサブクエリ
     * 
     * @param string $abbreviation Table alias テーブル別名
     * @return string Subquery サブクエリ
     */
    public function perWeeklyQuery() {
        $group_weekly = " GROUP BY WEEKLY ";
        
        return $group_weekly;
    }
    
    /**
     * Creating a subquery to perform the rounding in the day
     * 日で丸め込みを行うサブクエリを作成
     *
     * @param string $abbreviation Table alias テーブル別名
     * @return string Subquery サブクエリ
     */
    public function dateformatDailyQuery($abbreviation) {
        $daily = "";
        if(strlen($abbreviation) == 0) {
            $daily = " DATE_FORMAT(record_date, '%d') AS DAILY ";
        } else if(strlen($abbreviation) > 0) {
            $daily = " DATE_FORMAT(". $abbreviation. ".record_date, '%d') AS DAILY ";
        }
        
        return $daily;
    }
    
    /**
     * Subquery that grouping in day
     * 日でグルーピングするサブクエリ
     * 
     * @param string $abbreviation Table alias テーブル別名
     * @return string Subquery サブクエリ
     */
    public function perDailyQuery() {
        $group_daily = " GROUP BY DAILY ";
        
        return $group_daily;
    }
    
    /**
     * Site license access determination for subqueries created
     * サイトライセンスアクセス判定用サブクエリ作成
     *
     * @param string $abbreviation Table alias テーブル別名
     * @param array $start_ip Start IP address 開始IPアドレス
     * @param array $finish_ip End IP address 終了IPアドレス
     * @param array $user_id User id ユーザID
     * @return string Subquery サブクエリ
     */
    public function checkSitelicenseQuery($abbreviation, $start_ip, $finish_ip, $user_id) {
        // カラム名の設定
        if(strlen($abbreviation) > 0) {
            $ip_column = $abbreviation.".numeric_ip_address";
            $user_column = $abbreviation.".user_id";
        } else {
            $ip_column = "numeric_ip_address";
            $user_column = "user_id";
        }
        
        // サブクエリ文作成
        // IPアドレス判定
        $sitelicense = "";
        for($ii = 0; $ii < count($start_ip); $ii++) {
            // IPが無い場合はスルー
            if(strlen($start_ip[$ii]) > 0 && strlen($finish_ip[$ii]) > 0) {
                 // IPが複数ある場合はORで繋ぐ
                if(strlen($sitelicense) > 0) {
                    $sitelicense .= " OR ";
                }
                // 終了IPが未設定だった場合は一致検索を行う
                if(strlen($finish_ip[$ii]) == 0) {
                    $sitelicense .= $ip_column. " = ". $start_ip[$ii];
                } else {
                    // IP範囲の設定
                    $start_ip_address = 0;
                    $finish_ip_address = 0;
                    // 開始IPの方が終了IPより大きい設定がされていた場合
                    if($start_ip[$ii] > $finish_ip[$ii]) {
                        // 開始IPと終了IPを入れ替える
                        $start_ip_address = $finish_ip[$ii];
                        $finish_ip_address = $start_ip[$ii];
                    } else {
                        $start_ip_address = $start_ip[$ii];
                        $finish_ip_address = $finish_ip[$ii];
                    }
                    $sitelicense .= "(".
                                    $start_ip_address. " <= ". $ip_column.
                                    " AND ".
                                    $ip_column. " <= ". $finish_ip_address.
                                    ")";
                }
            }
        }
        // 組織所属判定
        if(count($user_id) > 0) {
            if(strlen($sitelicense) > 0) {
                $sitelicense .= " OR ";
            }
            $sitelicense .= $user_column. " IN ( ";
            for($ii = 0; $ii < count($user_id); $ii++) {
                if($ii > 0) {
                    $sitelicense .= ", ";
                }
                $sitelicense .= "'". $user_id[$ii]. "'";
            }
            $sitelicense .= " ) ";
        }
        
        if(strlen($sitelicense) > 0) {
            $sitelicense = " AND ( ". $sitelicense. " ) ";
        }
        
        return $sitelicense;
    }
    
    /**
     * Site license subqueries created for exclusion item type removal
     * サイトライセンス除外アイテムタイプ除去用サブクエリ作成
     *
     * @param string $abbreviation Table alias テーブル別名
     * @param int $item_type_id Item type id アイテムタイプID
     * @param array $user_id User id ユーザID
     * @return string Subquery サブクエリ
     */
    public function exclusiveSitelicenseItemtypeQuery($abbreviation, $item_type_id) {
        // カラム名の設定
        if(strlen($abbreviation) > 0) {
            $item_type_id_column = $abbreviation. ".item_type_id";
        } else {
            $item_type_id_column = "item_type_id";
        }
        
        // サブクエリ文作成
        $exclusive_item_type = "";
        if(count($item_type_id) > 0) {
            $exclusive_item_type .= " AND ". $item_type_id_column. " NOT IN ( ";
            for($ii = 0; $ii < count($item_type_id); $ii++) {
                if($ii > 0) {
                    $exclusive_item_type .= ", ";
                }
                $exclusive_item_type .= $item_type_id[$ii];
            }
            $exclusive_item_type .= " ) ";
        }
        
        return $exclusive_item_type;
    }
}
?>