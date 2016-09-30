<?php

/**
 * Harvest processing common classes
 * ハーベスト処理共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryHarvesting.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * Harvest processing common classes
 * ハーベスト処理共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositoryHarvesting extends RepositoryAction
{
    // ---------------------------------------------
    // Const
    // ---------------------------------------------
    // Temporary file path (without WEBAPP_DIR)
    /**
     * Work file path
     * ワークファイルパス
     *
     * @var string
     */
    const PATH_WORKFILE     = "/logs/weko/harvesting/progress.tsv";
    /**
     * Temporary directory path
     * 一時ディレクトリパス
     *
     * @var string
     */
    const PATH_TMP_WORKFILE = "/logs/weko/harvesting/tmp_progress.tsv";
    /**
     * XML file path
     * XMLファイルパス
     *
     * @var string
     */
    const PATH_XMLFILE      = "/logs/weko/harvesting/response.xml";
    /**
     * XML save directory path
     * XML保存ディレクトリパス
     *
     * @var string
     */
    const PATH_XML_SAVEDIR  = "/logs/weko/harvesting/xmlSave/";
    
    // Filter file path (without WEBAPP_DIR)
    /**
     * OAI-PMH filter path
     * OAI-PMHフィルターパス
     *
     * @var string
     */
    const FILTER_OAIPMH = "/modules/repository/action/common/harvesting/filter/HarvestingOaipmh.class.php";
    
    // status
    /**
     * Status(start)
     * ステータス(開始)
     *
     * @var string
     */
    const STATUS_START = "start";
    /**
     * Status(running)
     * ステータス(実行中)
     *
     * @var string
     */
    const STATUS_RUNNING = "running";
    /**
     * Status(end)
     * ステータス(終了)
     *
     * @var string
     */
    const STATUS_END = "end";
    /**
     * Status (in other process is started)
     * ステータス(他のプロセスが起動中)
     *
     * @var string
     */
    const STATUS_BLOCK = "block";
    
    // metadataPrefix
    /**
     * Dublin Core metadata prefix
     * Dublin Coreメタデータ接頭辞
     *
     * @var string
     */
    const MP_OAIDC = RepositoryConst::OAIPMH_METADATA_PREFIX_DC;
    /**
     * junii2 metadata prefix
     * junii2メタデータ接頭辞
     *
     * @var string
     */
    const MP_JUNII2 = RepositoryConst::OAIPMH_METADATA_PREFIX_JUNII2;
    /**
     * LOM metadata prefix
     * LOMメタデータ接頭辞
     *
     * @var string
     */
    const MP_OAILOM = RepositoryConst::OAIPMH_METADATA_PREFIX_LOM;
    
    // tag
    /**
     * Tag name(identifier)
     * タグ名(identifier)
     *
     * @var string
     */
    const OAIPMH_TAG_IDENTIFIER = "identifier";
    /**
     * Tag name(datestamp)
     * タグ名(datestamp)
     *
     * @var string
     */
    const OAIPMH_TAG_DATESTAMP = "datestamp";
    /**
     * Tag name(setSpec)
     * タグ名(setSpec)
     *
     * @var string
     */
    const OAIPMH_TAG_SETSPEC = "setSpec";
    
    // Add Selective Harvesting 2013/09/04 R.Matsuura --start--
    /**
     * Minimum date and time
     * 最小日時
     *
     * @var string
     */
    const DEFAULT_FROM_DATE = "0001-01-01T00:00:00Z";
    /**
     * Up to date and time
     * 最大日時
     *
     * @var string
     */
    const DEFAULT_UNTIL_DATE = "9999-12-31T23:59:59Z";
    
    /**
     * Key name(year)
     * キー名(年)
     *
     * @var string
     */
    const DATE_YEAR = "year";
    /**
     * Key name(month)
     * キー名(月)
     *
     * @var string
     */
    const DATE_MONTH = "month";
    /**
     * Key name(day)
     * キー名(日)
     *
     * @var string
     */
    const DATE_DAY = "day";
    /**
     * Key name(hour)
     * キー名(時)
     *
     * @var string
     */
    const DATE_HOUR = "hour";
    /**
     * Key name(minute)
     * キー名(分)
     *
     * @var string
     */
    const DATE_MINUTE = "minute";
    /**
     * Key name(second)
     * キー名(秒)
     *
     * @var string
     */
    const DATE_SECOND = "second";
    // Add Selective Harvesting 2013/09/04 R.Matsuura --end--
    
    // ---------------------------------------------
    // Member
    // ---------------------------------------------
    /**
     * repository ID
     * 機関ID
     *
     * @var int
     */
    private $repositoryId = 0;
    /**
     * repository's baseUrl
     * ベースURL
     *
     * @var string
     */
    private $baseUrl = "";
    /**
     * metadataPrefix
     * メタデータ接頭辞
     *
     * @var string metadataPrefix:oai_dc/junii2/oai_lom
     */
    private $metadataPrefix = "";
    /**
     * postIndexId
     * 登録先インデックスID
     *
     * @var int
     */
    private $postIndexId = 0;
    /**
     * isAutoSoting
     * サブインデックスへの自動振分
     *
     * @var int 0:not exec/1:execute sorting
     */
    private $isAutoSoting = 0;
    /**
     * requestUrl
     * リクエストURL
     *
     * @var string
     */
    private $requestUrl = "";
    /**
     * progress file path
     * 進捗ファイルパス
     *
     * @var string
     */
    private $workFile = "";
    /**
     * tmp_progress file path
     * 一時進捗ファイルパス
     *
     * @var string
     */
    private $tmpWorkFile  = "";
    /**
     * response xml file path
     * レスポンスXMLファイルパス
     *
     * @var string
     */
    private $xmlFile = "";
    /**
     * Harvesting status
     * ハーベスト実施状態
     * 
     * @var string status:start/running/end/block
     */
    private $status = self::STATUS_BLOCK;
    /**
     * Seconds to sleep
     * ハーベスト実行間隔(秒)
     *
     * @var int
     */
    private $sleepSec = 5;
    /**
     * next request URL
     * 次URL
     * 
     * @var string
     */
    private $nextURL = '';
    /**
     * Harvesting filter class
     * ハーベストフィルタクラスオブジェクト
     *
     * @var classObject
     */
    private $harvestingFilter = null;
    
    // For datacheck
    /**
     * Responce xml file save flag
     * レスポンスXMLファイル保存フラグ
     *
     * @var unknown_type
     */
    private $saveResponseFlag = false;    // default: false
    /**
     * Save directory
     * 保存ディレクトリ
     *
     * @var unknown_type
     */
    private $saveResponseDir = "";
    
    // Add Selective Harvesting 2013/09/04 R.Matsuura --start--
    /**
     * from date for selective harvesting
     * fromパラメータの指定値
     * 
     * @var int
     */
    private $from_date = null;
    /**
     * until date for selective harvesting
     * untilパラメータの指定値
     * 
     * @var string
     */
    private $until_date = null;
    /**
     * set parameter for selective harvesting
     * setパラメータの指定値
     * 
     * @var string
     */
    private $set_param = null;
    // Add Selective Harvesting 2013/09/04 R.Matsuura --end--
    
    // ---------------------------------------------
    // Method
    // ---------------------------------------------
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $Session session object セッション
     * @param DbObject $Db DB object DBオブジェクト
     */
    public function RepositoryHarvesting($Session, $Db){
        $this->Session = $Session;
        $this->Db = $Db;
        
        $this->workFile = WEBAPP_DIR.self::PATH_WORKFILE;
        $this->tmpWorkFile = WEBAPP_DIR.self::PATH_TMP_WORKFILE;
        $this->xmlFile = WEBAPP_DIR.self::PATH_XMLFILE;
        $this->saveResponseDir = WEBAPP_DIR.self::PATH_XML_SAVEDIR;
    }
    
    /**
     * Get now date
     * 現在日時の取得
     * 
     * @return string Date 日時
     */
    private function getNowDate()
    {
        $DATE = new Date();
        return $DATE->getDate().".000";
    }
    
    /**
     * Get member: status
     * ステータス取得
     * 
     * @return string Status ステータス
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set member: nextURL
     * 次URLの取得
     * 
     * @param string Next url 次URL
     */
    public function setNextUrl($url)
    {
        $this->nextURL = $url;
    }
    
    /**
     * Open progress file
     * 進捗ファイルを開く
     * 
     * @param boolean $executeFlg execute or not 実行フラグ
     */
    public function openProgressFile($executeFlg=true)
    {
        // Check progress file exists
        if(!file_exists($this->workFile))
        {
            // Progress file is not exist
            // -> Set status to "start".
            $this->status = self::STATUS_START;
        }
        else
        {
            // Check file read rights
            if(is_readable($this->workFile) && is_writable($this->workFile))
            {
                // Get only one line
                $handle = fopen($this->workFile, "r");
                $line = fgets($handle);
                $line = str_replace("\r\n", "", $line);
                $line = str_replace("\n", "", $line);
                $line = trim($line);
                fclose($handle);
                
                // There is contents in progress file
                if($executeFlg)
                {
                    chmod($this->workFile, 0100); // -wx --- ---
                    
                    // Interval for request to repository
                    sleep($this->sleepSec);
                }
                
                if(strlen($line) > 0)
                {
                    // -> Set status to "running" and get params.
                    $this->status = self::STATUS_RUNNING;
                    
                    // Explode string
                    // Update for Selective Harvesting 2013/09/04 R.Matsuura --start--
                    $progressArray = explode("\t", $line, 9);
                    $this->repositoryId = $progressArray[0];
                    $this->baseUrl = $progressArray[1];
                    $this->from_date = $progressArray[2];
                    $this->until_date = $progressArray[3];
                    $this->set_param = $progressArray[4];
                    $this->metadataPrefix = $progressArray[5];
                    $this->postIndexId = $progressArray[6];
                    $this->isAutoSoting = $progressArray[7];
                    $this->requestUrl = $progressArray[8];
                    
                    // Filter OAI-PMH --start--
                    require_once WEBAPP_DIR.self::FILTER_OAIPMH;
                    $this->harvestingFilter = new HarvestingOaipmh($this->Session, $this->Db);
                    $this->harvestingFilter->setRepositoryId($this->repositoryId);
                    $this->harvestingFilter->setBaseUrl($this->baseUrl);
                    $this->harvestingFilter->setFromDate($this->from_date);
                    $this->harvestingFilter->setUntilDate($this->until_date);
                    $this->harvestingFilter->setSetParam($this->set_param);
                    $this->harvestingFilter->setMetadataPrefix($this->metadataPrefix);
                    $this->harvestingFilter->setPostIndexId($this->postIndexId);
                    $this->harvestingFilter->setIsAutoSoting($this->isAutoSoting);
                    $this->harvestingFilter->setRequestUrl($this->requestUrl);
                    $this->harvestingFilter->setXmlFile($this->xmlFile);
                    // Filter OAI-PMH --end--
                    // Update for Selective Harvesting 2013/09/04 R.Matsuura --end--
                }
                else
                {
                    // Progress file is empty
                    // -> Set status to "end".
                    $this->status = self::STATUS_END;
                }
            }
            else
            {
                $this->status = self::STATUS_BLOCK;
            }
        }
    }
    
    /**
     * Create progress file
     * 進捗ファイル作成
     * 
     * @param boolean $executeAllItemAcquisition All Items acquisition flag 全アイテム取得フラグ
     * @return boolean Result 結果
     */
    public function createProgressFile($executeAllItemAcquisition=false)
    {
        $handle = null;
        
        try
        {
            // require
            require_once WEBAPP_DIR.self::FILTER_OAIPMH;
            
            // Filter class
            $harvestingOaipmh = null;
            
            $progressText = "";
            $records = array();
            $this->getHarvestingTable($records);
            foreach($records as $val)
            {
                // Create progress text
                // For OAI-PMH --start--
                if($harvestingOaipmh == null)
                {
                    $harvestingOaipmh = new HarvestingOaipmh($this->Session, $this->Db);
                    $harvestingOaipmh->setXmlFile($this->xmlFile);
                }
                // Update for Selective Harvesting 2013/09/04 R.Matsuura --start--
                $executionDate = $val[RepositoryConst::DBCOL_REPOSITORY_HARVESTING_EXECUTION_DATE];
                $fromDate = $val[RepositoryConst::DBCOL_REPOSITORY_HARVESTING_FROM_DATE];
                // Add Selective Harvesting R.Matsuura 2013/10/01 --start--
                if($executeAllItemAcquisition == false)
                {
                    if($fromDate < $executionDate)
                    {
                        $fromDate = $executionDate;
                    }
                }
                // Add Selective Harvesting R.Matsuura 2013/10/01 --end--
                $harvestingOaipmh->setRepositoryId($val[RepositoryConst::DBCOL_REPOSITORY_HARVESTING_REPOSITORY_ID]);
                $harvestingOaipmh->setBaseUrl($val[RepositoryConst::DBCOL_REPOSITORY_HARVESTING_BASE_URL]);
                $harvestingOaipmh->setFromDate($fromDate);
                $harvestingOaipmh->setUntilDate($val[RepositoryConst::DBCOL_REPOSITORY_HARVESTING_UNTIL_DATE]);
                $harvestingOaipmh->setSetParam($val[RepositoryConst::DBCOL_REPOSITORY_HARVESTING_SET_PARAM]);
                $harvestingOaipmh->setMetadataPrefix($val[RepositoryConst::DBCOL_REPOSITORY_HARVESTING_METADATA_PREFIX]);
                $harvestingOaipmh->setPostIndexId($val[RepositoryConst::DBCOL_REPOSITORY_HARVESTING_POST_INDEX_ID]);
                $harvestingOaipmh->setIsAutoSoting($val[RepositoryConst::DBCOL_REPOSITORY_HARVESTING_AUTOMATIC_SORTING]);
                $harvestingOaipmh->setResponseDate("");
                // Update for Selective Harvesting 2013/09/04 R.Matsuura --end--
                
                $isError = true;
                $accessUrl = $val[RepositoryConst::DBCOL_REPOSITORY_HARVESTING_BASE_URL];
                if(strlen($accessUrl) > 0)
                {
                    $accessUrl = $harvestingOaipmh->getIdentifyUrl();
                    $responseDate = $this->checkRepositoryAccess($accessUrl);
                    if(strlen($responseDate) > 0)
                    {
                        $harvestingOaipmh->setResponseDate($responseDate);
                        $progressText .= $harvestingOaipmh->createProgressText();
                        $harvestingOaipmh->entryHarvestingLog(RepositoryConst::HARVESTING_OPERATION_ID_REPOSITORY, "", "", "", "", "", "", $accessUrl, "");
                        $isError = false;
                    }
                }
                if($isError)
                {
                    $harvestingOaipmh->setHarvestingLogMsg("repository_harvesting_error_get_url");
                    $harvestingOaipmh->setHarvestingLogStatus(RepositoryConst::HARVESTING_LOG_STATUS_ERROR);
                    $harvestingOaipmh->entryHarvestingLog(RepositoryConst::HARVESTING_OPERATION_ID_REPOSITORY, "", "", "", "", "", "", $accessUrl, "");
                    continue;
                }
                // For OAI-PMH --end--
            }
            
            // Create progress file
            $handle = fopen($this->workFile, "w");
            fwrite($handle, $progressText);
            fclose($handle);
            chmod($this->workFile, 0700);
            
            return true;
        }
        catch (Exception $ex)
        {
            // File close
            if($handle != null)
            {
                fclose($handle);
            }
            return false;
        }
    }
    
    /**
     * Update progress file
     * 進捗ファイル更新
     * 
     * @return boolean Result 結果
     */
    public function updateProgressFile()
    {
        if(!file_exists($this->workFile))
        {
            // Force exit
            $this->updateHarvestingEndDate($this->getNowDate());
            
            return false;
        }
        
        chmod($this->workFile, 0700); // rwx --- ---
        $w_fp = fopen($this->tmpWorkFile, "w");
        $r_fp = fopen($this->workFile, "r");
        $cnt = 0;
        while(!feof($r_fp))
        {
            $r_line = fgets($r_fp);
            $r_line = str_replace("\r\n", "", $r_line);
            $r_line = str_replace("\n", "", $r_line);
            if($cnt == 0)
            {
                // For first line
                if(strlen($this->nextURL) > 0)
                {
                    // Update for Selective Harvesting 2013/09/04 R.Matsuura --start--
                    $w_line = $this->repositoryId."\t".
                              $this->baseUrl."\t".
                              $this->from_date."\t".
                              $this->until_date."\t".
                              $this->set_param."\t".
                              $this->metadataPrefix."\t".
                              $this->postIndexId."\t".
                              $this->isAutoSoting."\t".
                              $this->nextURL."\n";
                    fwrite($w_fp, $w_line);
                    // Update for Selective Harvesting 2013/09/04 R.Matsuura --end--
                }
            }
            else
            {
                if(strlen($r_line) > 0){
                    // For second line below
                    fwrite($w_fp, $r_line."\n");
                }
            }
            $cnt++;
        }
        fclose($r_fp);
        fclose($w_fp);
        unlink($this->workFile);
        rename($this->tmpWorkFile, $this->workFile);
        chmod($this->workFile, 0700); // rwx --- ---
        
        return true;
    }
    
    /**
     * Delete progress file and response file
     * 作業ファイルの削除
     */
    public function deleteHarvestingFiles()
    {
        // delete work files
        if(file_exists($this->workFile))
        {
            chmod($this->workFile, 0700); // rwx --- ---
            unlink($this->workFile);
        }
        if(file_exists($this->xmlFile))
        {
            chmod($this->xmlFile, 0700); // rwx --- ---
            unlink($this->xmlFile);
        }
    }
    
    /**
     * Check access Repository
     * リポジトリへのアクセス確認
     * 
     * @param string $url Request URL リクエストURL
     * @return string Response date レスポンス日時
     */
    private function checkRepositoryAccess($url)
    {
        // init
        $this->requestUrl = $url;
        $responseDate = "";
        
        if($this->createResponseFile())
        {
            $resResponseDateXml = "";
            $resResponseDateFlag = false;
            
            // read file
            $fp = fopen($this->xmlFile, "r");
            while(!feof($fp))
            {
                // Read line
                $line = fgets($fp);
                $line = str_replace("\r\n", "", $line);
                $line = str_replace("\n", "", $line);
                
                if($resResponseDateFlag)
                {
                    $resResponseDateXml .= $line;
                    
                    // "responseDate" tag close
                    if(preg_match("/^<\/responseDate>$/", $line) > 0)
                    {
                        $resResponseDateFlag = false;
                        $this->responseDate = "";
                        
                        $resResponseDateXml = str_replace("\r\n", "\n", $resResponseDateXml);
                        $resResponseDateXml = str_replace("\r", "\n", $resResponseDateXml);
                        $resResponseDateXml = str_replace("\n", "", $resResponseDateXml);
                        
                        $resResponseDateXml = preg_replace("/^<responseDate ?.*>(.*)<\/responseDate>$/", "$1", $resResponseDateXml);
                        
                        // Get responseDate
                        $responseDate = $resResponseDateXml;
                        
                        break;
                    }
                }
                else if(preg_match("/^<responseDate +.*>|<responseDate>$/", $line) > 0){
                    // "responseDate" tag open
                    $resResponseDateFlag = true;
                    $resResponseDateXml = $line;
                }
            }
            fclose($fp);
        }
        
        return $responseDate;
    }
    
    /**
     * Get repositories
     * リポジトリ一覧取得
     *
     * @param array $records repository information list リポジトリ一覧
     *                       array[$ii]["post_index_name"|"repository_id"|"repository_name"|"base_url"|"from_date"|"until_date"|"set_param"|"metadata_prefix"|"post_index_id"|"automatic_sorting"|"execution_date"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return boolean Result 結果
     */
    public function getHarvestingTable(&$records)
    {
        $records = array();
        
        // Get repository_harvesting table
        $query = "SELECT * FROM ".DATABASE_PREFIX."repository_harvesting ".
                 "WHERE is_delete = 0 ".
                 "ORDER BY repository_id ASC;";
        $result = $this->Db->execute($query);
        if($result === false){
            return false;
        }
        
        for($ii=0; $ii<count($result); $ii++)
        {
            $result[$ii]['post_index_name'] = '';
            if($result[$ii]['post_index_id'] > 0)
            {
                $query = "SELECT index_name, index_name_english ".
                        " FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_INDEX.
                        " WHERE index_id = ? ".
                        " AND is_delete = ? ";
                $params = array();
                $params[] = $result[$ii]['post_index_id'];
                $params[] = 0;
                $ret = $this->Db->execute($query, $params);
                if($ret === false || count($ret) != 1){
                    $result[$ii]['post_index_name'] = '';
                }
                if($this->Session->getParameter("_lang") == "japanese")
                {
                    $result[$ii]['post_index_name'] = $ret[0]['index_name'];
                }
                else
                {
                    $result[$ii]['post_index_name'] = $ret[0]['index_name_english'];
                }
            }
            array_push($records, $result[$ii]);
        }
        
        return true;
    }
    
    /**
     * Harvesting start process
     * ハーベスト開始
     * 
     * @return boolean Result 結果
     */
    public function startHarvesting()
    {
        $ret = false;
        
        // Entry start date
        $ret = $this->updateHarvestingStartDate($this->getNowDate());
        if($ret === false)
        {
            return false;
        }
        
        // Delete end date later
        $ret = $this->updateHarvestingEndDate();
        if($ret === false)
        {
            return false;
        }
        
        // Clear Harvesting log table
        $ret = $this->clearHarvestingLog();
        if($ret === false)
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * Execute harvesting
     * ハーベスト実行
     *
     * @return boolean Result 結果
     */
    public function executeHarvesting()
    {
        $ret = false;
        $nextUrl = "";
        $indexError = false;
        
        // Check post_index's delete status
        $indexError = !$this->checkIndexDeleteStatus($this->postIndexId);
        
        // Get response and write to file
        if(!$this->createResponseFile())
        {
            $this->nextURL = "";
            $this->harvestingFilter->setHarvestingLog("repository_harvesting_error_get_url");
            $this->harvestingFilter->setHarvestingLogStatus(RepositoryConst::HARVESTING_LOG_STATUS_ERROR);
            $this->harvestingFilter->entryHarvestingLog(RepositoryConst::HARVESTING_OPERATION_ID_REPOSITORY, "", "", "", "", "", "", $this->requestUrl, "");
            return false;
        }
        
        // For detacheck
        if($this->saveResponseFlag)
        {
            if(!file_exists($this->saveResponseDir))
            {
                mkdir($this->saveResponseDir);
            }
            chmod($this->saveResponseDir, 0700);
            if(file_exists($this->xmlFile))
            {
                $query = "SELECT DATE_FORMAT(NOW(), '%Y%m%d%H%i%s') AS now_date;";
                $result = $this->Db->execute($query);
                $dateStr = $result[0]['now_date'];
                $savePath = $this->saveResponseDir.$dateStr.".xml";
                copy($this->xmlFile, $savePath);
                chmod($savePath, 0700);
            }
        }
        
        // For OAI-PMH
        $ret = $this->harvestingFilter->registIndexAndItems($nextUrl, $indexError);
        
        $this->nextURL = $nextUrl;
        
        return $ret;
    }
    
    /**
     * Harvesting end process
     * ハーベスト終了
     * 
     * @return boolean Result 結果
     */
    public function endHarvesting()
    {
        $ret = false;
        
        // Delete harvesting files.
        $this->deleteHarvestingFiles();
        
        // Entry start date
        $ret = $this->updateHarvestingEndDate($this->getNowDate());
        
        if($ret === false)
        {
            return false;
        }
        
        // Update for Selective Harvesting 2013/09/04 R.Matsuura --start--
        $ret = $this->updateHarvestingExecuteDate();
        // Update for Selective Harvesting 2013/09/04 R.Matsuura --end--
        
        return $ret;
    }
    
    /**
     * Create response file by response body from URL
     * リポジトリへのレスポンスからXMLファイルを作成する
     *
     * @return boolean Result 結果
     */
    public function createResponseFile()
    {
        $ret = false;
        
        /////////////////////////////
        // HTTP_Request init
        /////////////////////////////
        // send http request
        $proxy = $this->getProxySetting();
        $option = null;
        if($proxy['proxy_mode'] == 1)
        {
            $option = array( 
                            "timeout" => 300,
                            "allowRedirects" => true, 
                            "maxRedirects" => 3,
                            "proxy_host"=>$proxy['proxy_host'],
                            "proxy_port"=>$proxy['proxy_port'],
                            "proxy_user"=>$proxy['proxy_user'],
                            "proxy_pass"=>$proxy['proxy_pass']
                        );
        }
        else
        {
            $option = array( 
                            "timeout" => 300,
                            "allowRedirects" => true, 
                            "maxRedirects" => 3
                        );
        }
        
        // HTTP Request
        $http = new HTTP_Request($this->requestUrl, $option);
        
        // setting HTTP header
        $http->addHeader("User-Agent", $_SERVER['HTTP_USER_AGENT']); 
        $http->addHeader("Referer", $_SERVER['HTTP_REFERER']);
        
        /////////////////////////////
        // run HTTP request 
        /////////////////////////////
        $response = $http->sendRequest();
        if (!PEAR::isError($response)) { 
            $responseCode = $http->getResponseCode();        // ResponseCode
            $responseHeader = $http->getResponseHeader();    // ResponseHeader
            $responseBody = $http->getResponseBody();        // ResponseBody
            //$responseCookies = $http->getResponseCookies();  // ResponseCookies
            
            if($responseCode == 200)
            {
                if(key_exists("content-type", $responseHeader) && preg_match("/^(text|application)\/xml.*$/", $responseHeader["content-type"]) != 0)
                {
                    if(strlen($responseBody) != 0)
                    {
                        // To one line at a time element and tag
                        $responseBody = preg_replace("/([^>])</", "$1\n<", $responseBody);
                        $responseBody = str_replace(">", ">\n", $responseBody);
                        
                        // output responseBody to tmpfile
                        $handle = fopen($this->xmlFile, "w");
                        $size = fwrite($handle, $responseBody);
                        fclose($handle);
                        
                        $ret = true;
                    }
                }
            }
        }
        
        return $ret;
    }
    
    /**
     * Check is_delete status in repository_index table
     * インデックスが論理削除されていないかを確認する
     *
     * @param int $indexId Index id インデックスID
     * @return boolean Delete Status 削除されていないか否か
     *                 true: is_delete=0 / false: is_delete=1
     */
    private function checkIndexDeleteStatus($indexId)
    {
        $query = "SELECT COUNT(*) ".
                 "FROM ".DATABASE_PREFIX."repository_index ".
                 "WHERE index_id = ? ".
                 "AND is_delete = 0;";
        $params = array();
        $params[] = $indexId;
        $result = $this->Db->execute($query, $params);
        if($result === false || $result[0]["COUNT(*)"] < 1)
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * Update harvesting start date
     * ハーベスト実行日時更新
     * 
     * @param string $startDate Date and time of execution 実行日時
     * @return boolean Result 結果
     */
    public function updateHarvestingStartDate($startDate="")
    {
        $query = "UPDATE ".DATABASE_PREFIX."repository_parameter ".
                 "SET param_value = ? ".
                 "WHERE param_name = ?;";
        $params = array();
        $params[] = $startDate;
        $params[] = "harvesting_start_date";
        $result = $this->Db->execute($query, $params);
        if($result === false){
            return false;
        }
        
        return true;
    }
    
    /**
     * Update harvesting end date
     * ハーベスト終了日時を更新
     * 
     * @param string $endDate End date and time 終了日時
     * @return boolean Result 結果
     */
    public function updateHarvestingEndDate($endDate="")
    {
        $query = "UPDATE ".DATABASE_PREFIX."repository_parameter ".
                 "SET param_value = ? ".
                 "WHERE param_name = ?;";
        $params = array();
        $params[] = $endDate;
        $params[] = "harvesting_end_date";
        $result = $this->Db->execute($query, $params);
        if($result === false){
            return false;
        }
        
        return true;
    }
    
    /**
     * Disable Repositories Data
     * リポジトリを無効にする
     *
     * @param string $user_id User id ユーザID
     * @param string $updateDate ate and time of update 更新日時
     * @return boolean Result 結果
     */
    public function disableRepositoriesData($user_id, $updateDate)
    {
        // Update repository_harvesting table
        $query = "UPDATE ".DATABASE_PREFIX."repository_harvesting ".
                 "SET mod_user_id = ?, ".
                 "del_user_id = ?, ".
                 "mod_date = ?, ".
                 "del_date = ?, ".
                 "is_delete = 1 ".
                 "WHERE is_delete = 0 ";
        $params = array();
        $params[] = $user_id;
        $params[] = $user_id;
        $params[] = $updateDate;
        $params[] = $updateDate;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            return false;
        }
        
        return true;
    }
    
    // Update for Selective Harvesting 2013/09/04 R.Matsuura
    /**
     * Upsert repositories data
     * リポジトリを挿入・更新する
     *
     * @param array $repoData Repository information リポジトリ情報
     *                        array[$ii]["repository_id"|"base_url"|"from_date"|"until_date"|"set_param"|"metadata_prefix"|"post_index_id"|"automatic_sorting"|"execution_date"]
     * @param string $user_id User id ユーザID
     * @param string $date ate and time of update 更新日時
     * @return boolean Result 結果
     */
    public function upsertRepositoriesData($repoData, $user_id, $date)
    {
        for($ii=0; $ii<count($repoData); $ii++)
        {
            $query = "";
            $params = array();
            if($repoData[$ii]["repository_id"] != 0)
            {
                // already registed
                // update
                $query = "UPDATE ".DATABASE_PREFIX."repository_harvesting ".
                         "SET repository_name = ?, ".
                         "base_url = ?, ".
                         "from_date = ?, ".
                         "until_date = ?, ".
                         "set_param = ?, ".
                         "metadata_prefix = ?, ".
                         "post_index_id = ?, ".
                         "automatic_sorting = ?, ".
                         "execution_date = ?, ".
                         "mod_user_id = ?, ".
                         "del_user_id = '', ".
                         "mod_date = ?, ".
                         "del_date = '', ".
                         "is_delete = 0 ".
                         "WHERE repository_id = ?";
                $params[] = $repoData[$ii]["repository_name"];
                $params[] = $repoData[$ii]["base_url"];
                $params[] = $repoData[$ii]["from_date"];
                $params[] = $repoData[$ii]["until_date"];
                $params[] = $repoData[$ii]["set_param"];
                $params[] = $repoData[$ii]["metadata_prefix"];
                $params[] = intval($repoData[$ii]["post_index_id"]);
                $params[] = intval($repoData[$ii]["automatic_sorting"]);
                $params[] = $repoData[$ii]["execution_date"];
                $params[] = $user_id;
                $params[] = $date;
                $params[] = $repoData[$ii]["repository_id"];
            }
            else
            {
                // new repository
                // insert
                $query = "INSERT INTO ".DATABASE_PREFIX."repository_harvesting ".
                         "(repository_name, base_url, from_date, until_date, set_param, metadata_prefix, post_index_id, ".
                         "automatic_sorting, execution_date, ins_user_id, mod_user_id, del_user_id, ".
                         "ins_date, mod_date, del_date, is_delete) ".
                         "VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '', ?, ?, '', '0');";
                $params[] = $repoData[$ii]["repository_name"];
                $params[] = $repoData[$ii]["base_url"];
                $params[] = $repoData[$ii]["from_date"];
                $params[] = $repoData[$ii]["until_date"];
                $params[] = $repoData[$ii]["set_param"];
                $params[] = $repoData[$ii]["metadata_prefix"];
                $params[] = intval($repoData[$ii]["post_index_id"]);
                $params[] = intval($repoData[$ii]["automatic_sorting"]);
                $params[] = $repoData[$ii]["execution_date"];
                $params[] = $user_id;
                $params[] = $user_id;
                $params[] = $date;
                $params[] = $date;
            }
            $result = $this->Db->execute($query, $params);
            if($result === false)
            {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Clear Harvesting Log
     * ハーベストログクリア
     *
     * @return boolean Result 結果
     */
    private function clearHarvestingLog()
    {
        $query = "TRUNCATE TABLE ".DATABASE_PREFIX. RepositoryConst::DBTABLE_REPOSITORY_HARVESTING_LOG;
        $result = $this->Db->execute($query);
        if($result=== false)
        {
            return false;
        }
        return true;
    }
    
    /**
     * kill harvesting process
     * -> delete workFile
     * ハーベスト強制終了
     */
    public function killProcess()
    {
        // delete workFile
        $this->deleteHarvestingFiles();
        
        // end time output
        $this->updateHarvestingEndDate($this->getNowDate());
    }
    
    // Add Selective Harvesting 2013/09/04 R.Matsuura --start--
    /**
     * Divide Datestamp(YYYY-MM-DDThh:mm:ssZ)
     * DateStamp分割
     * 
     * @param string $datestamp DateStamp 日時
     * @param array $dividedDate Divided date 分割した日時
     *                           array["year"|"month"|"day"|"hour"|"minute"|"second"]
     * @return boolean Result 結果
     */
    public function dividDatestamp($datestamp, &$date_params)
    {
        // divide by 'T' and 'Z'
        $dates = split('[TZ]', $datestamp);
        
        $date_params[RepositoryHarvesting::DATE_YEAR] = "0001";
        $date_params[RepositoryHarvesting::DATE_MONTH] = "01";
        $date_params[RepositoryHarvesting::DATE_DAY] = "01";
        $date_params[RepositoryHarvesting::DATE_HOUR] = "00";
        $date_params[RepositoryHarvesting::DATE_MINUTE] = "00";
        $date_params[RepositoryHarvesting::DATE_SECOND] = "00";
        
        if(count($dates)>=2)
        {
            $splitedYearMonthDay = split('-', $dates[0]);
            $splitedHourMinuteSecond = split(':', $dates[1]);
            if(count($splitedYearMonthDay) != 3)
            {
                return false;
            }
            else if(count($splitedHourMinuteSecond) != 3)
            {
                return false;
            }
            else
            {
                list($year, $month, $day) = $splitedYearMonthDay;
                list($hour, $minute, $second) = $splitedHourMinuteSecond;
                $date_params[RepositoryHarvesting::DATE_YEAR] = $year;
                $date_params[RepositoryHarvesting::DATE_MONTH] = $month;
                $date_params[RepositoryHarvesting::DATE_DAY] = $day;
                $date_params[RepositoryHarvesting::DATE_HOUR] = $hour;
                $date_params[RepositoryHarvesting::DATE_MINUTE] = $minute;
                $date_params[RepositoryHarvesting::DATE_SECOND] = $second;
            }
        }
        else if(count($dates) == 1)
        {
            $splitedYearMonthDay = split('-', $dates[0]);
            if(count($splitedYearMonthDay) != 3)
            {
                return false;
            }
            else
            {
                list($year, $month, $day) = $splitedYearMonthDay;
                $date_params[RepositoryHarvesting::DATE_YEAR] = $year;
                $date_params[RepositoryHarvesting::DATE_MONTH] = $month;
                $date_params[RepositoryHarvesting::DATE_DAY] = $day;
            }
        }
        return true;
    }
    
    /**
     * Update Havesting Execute Date
     * ハーベスト実行日時更新
     * 
     * @return boolean Result 結果
     */
    private function updateHarvestingExecuteDate()
    {
        $records = array();
        $this->getHarvestingTable($records);
        
        // execute getHarvestingStartDate
        require_once WEBAPP_DIR.self::FILTER_OAIPMH;
        $startDate = "";
        $harvestingOaipmh = new HarvestingOaipmh($this->Session, $this->Db);
        $result = $harvestingOaipmh->getHarvestingStartDate($startDate);
        if($result === false)
        {
            return false;
        }
        
        // delete second of decimal places
        $yearToSecondStartDate = substr($startDate, 0, 19);
        
        // get timestamp
        $startDateStamp = strtotime($yearToSecondStartDate);
        if($startDateStamp == -1)
        {
            return false;
        }
        
        // get GMT date
        $startDateHarvestingFormat = gmdate('Y-m-d\TH:i:s\Z', $startDateStamp);
        
        $query = "UPDATE ".DATABASE_PREFIX."repository_harvesting ".
                 "SET execution_date = ? ".
                 "WHERE repository_id IN(";
        $params = array();
        $params[] = $startDateHarvestingFormat;
        for($ii = 0; $ii < count($records); $ii++)
        {
            if($ii == 0) {
                $query .= "?";
            }
            else
            {
                $query .= ", ?";
            }
            $params[] = $records[$ii]["repository_id"];
        }
        $query .= ");";
        
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        return true;
    }
    // Add Selective Harvesting 2013/09/04 R.Matsuura --end--
}

?>
