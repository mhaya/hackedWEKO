<?php

/**
 * Usage statistics common view class
 * 利用統計画面表示汎用クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Usagestatistics.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Usage statistics common view class
 * 利用統計画面表示汎用クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Common_Usagestatistics extends RepositoryAction
{
    // -------------------------------------
    // member
    // -------------------------------------
    /**
     * Item ID
     * アイテムID
     *
     * @var int
     */
    public $itemId = null;
    
    /**
     * item number
     * アイテム通番
     *
     * @var int
     */
    public $itemNo = null;
    
    /**
     * year
     * 年
     *
     * @var int
     */
    public $year = null;
    
    /**
     * month
     * 月
     *
     * @var int
     */
    public $month = null;
    
    /**
     * views data
     * 閲覧統計データ
     *
     * @var array $usagesViews["total"|
     *                         "byDomain"][DOMAINNAME]["cnt"|"rate"|"img"]
     */
    public $usagesViews = array();
    
    /**
     * downloads data
     * ダウンロード統計データ
     *
     * @var array $usagesDownloads[NUM]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|
     *                                  "usagestatistics"]["total"|
     *                                                    "byDomain"][DOMAINNAME]["cnt"|"rate"|"img"]
     */
    public $usagesDownloads = array();
    
    /**
     * date list
     * 日付リスト
     *
     * @var array
     */
    public $dateList = array();
    
    /**
     * title
     * タイトル
     *
     * @var string
     */
    public $title = "";
    
    /**
     * display date
     * 表示日
     *
     * @var string
     */
    public $displayDate = "";
    
    // -------------------------------------
    // public
    // -------------------------------------
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws RepositoryException
     */
    public function executeApp()
    {
        try
        {
            // Init action
            $result = $this->initAction();
            if($result === false)
            {
                $this->failTrans();
                $this->exitAction();
                return "error";
            }
            
            // Set Usage statistics setting 2015/03/24 K.Sugimoto --start--
            $result = $this->getAdminParam('usagestatistics_link_display', $usagestatistics_link_display, $Error_Msg);
            if ( $result == false ){
                $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 ); //主メッセージとログIDを指定して例外を作成
                $DetailMsg = null;                              //詳細メッセージ文字列作成
                sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
                $exception->setDetailMsg( $DetailMsg );         //詳細メッセージ設定
                $this->failTrans();                             //トランザクション失敗を設定(ROLLBACK)
                throw $exception;
            }
            if ($usagestatistics_link_display == 0)
            {
            	return "invalid";
            }
            // Set Usage statistics setting 2015/03/24 K.Sugimoto --end--
            
            // Set item title
            $this->setItemTitle();
            
            // Get date array for pulldown
            $this->dateList = $this->setDateList();
            
            $this->infoLog("businessUsagestatistics", __FILE__, __CLASS__, __LINE__);
            $RepositoryUsagestatistics = BusinessFactory::getFactory()->getBusiness("businessUsagestatistics");
            
            // Get usages views
            $this->usagesViews = $RepositoryUsagestatistics->getUsagesViews($this->itemId, $this->itemNo, $this->year, $this->month);
            
            // Get usages downloads
			$this->usagesDownloads = $RepositoryUsagestatistics->getUsagesDownloads($this->itemId, $this->itemNo, $this->year, $this->month);
            
            $this->exitAction();
            return "success";
        }
        catch (RepositoryException $exception)
        {
            $this->failTrans();
            $this->exitAction();
            return "error";
        }
    }
    
    // -------------------------------------
    // private
    // -------------------------------------
    /**
     * Set date List for pull down
     * プルダウンメニューの日付をセットする
     *
     * @return array pull down dates 日付プルダウンデータ
     *                array[$ii]["value"|"display"|"selected"]
     */
    private function setDateList()
    {
        $retArray = array();
        
        // Get the oldest date at usagestatistics table
        $oldestDate = $this->getOldestDateAtUsageStatisticsTable();
        
        // Get previous month
        $prevMonth = $this->getPreviousMonth();
        
        // Create date list for pulldown
        // Date format: japanese => "YYYY年MM月" / english => MM/YYYY
        if(strlen($oldestDate) == 0)
        {
            $oldestDate = $prevMonth;
        }
        
        $this->setLangResource();
        $dateFormat = $this->Session->getParameter("smartyAssign")->getLang("repository_usagestatistics_date_format");
        $oldestDateArray = explode("-", $oldestDate, 2);
        $oldestYear = intval($oldestDateArray[0]);
        $oldestMonth = intval($oldestDateArray[1]);
        $prevMonthArray = explode("-", $prevMonth, 2);
        $nowYear = intval($prevMonthArray[0]);
        $nowMonth = intval($prevMonthArray[1]);
        $validDate = false;
        
        for($tmpYear=$oldestYear; $tmpYear<=$nowYear; $tmpYear++)
        {
            // Set tmpMonth
            $tmpMonth = 1;
            if($tmpYear == $oldestYear)
            {
                $tmpMonth = $oldestMonth;
            }
            
            // Set limitMonth
            $limitMonth = 12;
            if($tmpYear == $nowYear)
            {
                $limitMonth = $nowMonth;
            }
            
            for(; $tmpMonth<=$limitMonth; $tmpMonth++)
            {
                $value = sprintf("%d-%02d", $tmpYear, $tmpMonth);
                $display = sprintf($dateFormat, $tmpYear, $tmpMonth);
                $isSelected = false;
                if($tmpYear == intval($this->year) && $tmpMonth == intval($this->month))
                {
                    $isSelected = true;
                    $validDate = true;
                    $this->displayDate = $display;
                }
                
                $dateArray = array( "value" => $value,
                                    "display" => $display,
                                    "selected" => $isSelected);
                array_push($retArray, $dateArray);
            }
        }
        
        if(!$validDate)
        {
            $this->year = $nowYear;
            $this->month = $nowMonth;
            if(isset($retArray[count($retArray)-1]["selected"]))
            {
                $retArray[count($retArray)-1]["selected"] = true;
            }
            $this->displayDate = sprintf($dateFormat, $this->year, $this->month);
        }
        
        return $retArray;
    }
    
    /**
     * Get the oldest date at usage statistics table
     * 利用統計テーブルから最も古い日付を取得する
     *
     * @return string date 日付
     */
    private function getOldestDateAtUsageStatisticsTable()
    {
        // Get the oldest date (format: YYYY-MM)
        $query = "SELECT MIN(record_date) AS record_date ".
                 "FROM ".DATABASE_PREFIX."repository_usagestatistics ";
        $result = $this->Db->execute($query);
        if($result === false || count($result)!=1)
        {
            return "";
        }
        
        return $result[0]["record_date"];
    }
    
    /**
     * Get previous month
     * 前月を取得する
     *
     * @return string previous month 前月
     */
    private function getPreviousMonth()
    {
        // Get previous month (format: YYYY-MM)
        $query = "SELECT DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m') AS prevMonth ";
        $result = $this->Db->execute($query);
        if($result === false || count($result)!=1)
        {
            return "";
        }
        
        return $result[0]["prevMonth"];
    }
    
    /**
     * Set item title
     * アイテム名をセットする
     *
     */
    private function setItemTitle()
    {
        $result = array();
        $this->getItemTableData($this->itemId, $this->itemNo, $result, $errMsg);
        
        $title = 0;
        $titleEn = 0;
        
        if( isset($result["item"][0][RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE]) ){
            $title = $result["item"][0][RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
        }
        
        if( isset($result["item"][0][RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH]) ){
            $titleEn = $result["item"][0][RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
        }
        
        if($this->Session->getParameter("_lang")=="japanese")
        {
            $this->title = $title;
            if(strlen($this->title) == 0)
            {
                $this->title = $titleEn;
            }
        }
        else
        {
            $this->title = $titleEn;
            if(strlen($this->title) == 0)
            {
                $this->title = $title;
            }
        }
    }
}
?>