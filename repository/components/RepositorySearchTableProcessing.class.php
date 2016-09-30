<?php

/**
 * Common classes for creating and updating the search table that holds the metadata and file contents of each item to search speed improvement
 * 検索速度向上のためアイテム毎のメタデータおよびファイル内容を保持する検索テーブルの作成・更新を行う共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositorySearchTableProcessing.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Db connect class
 * DB接続クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDbAccess.class.php';
/**
 * Process utility class
 * 汎用処理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryProcessUtility.class.php';
/**
 * Handle manager class
 * ハンドル管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
/**
 * Search query generator class
 * 検索クエリ作成クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/QueryGenerator.class.php';
/**
 * Plugin manager class
 * プラグイン管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryPluginManager.class.php';
/**
 * Convert multi byte string class
 * マルチバイト文字変換クラス
 */
require_once WEBAPP_DIR. '/modules/repository/files/plugin/searchkeywordconverter/Twobytechartohalfsizechar.class.php';

/**
 * Common classes for creating and updating the search table that holds the metadata and file contents of each item to search speed improvement
 * 検索速度向上のためアイテム毎のメタデータおよびファイル内容を保持する検索テーブルの作成・更新を行う共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositorySearchTableProcessing extends RepositoryAction
{
    /**
     * all metadata key
     * all metadata key
     */
    const ALLMETADATA = "allMetaData";
    /**
     * Search table name
     * 検索テーブル名
     */
    const ALLMETADATA_TABLE = "repository_search_allmetadata";
    /**
     * file data key
     * file data key
     */
    const FILEDATA = "fileData";
    /**
     * Search table name
     * 検索テーブル名
     */
    const FILEDATA_TABLE = "repository_search_filedata";
    /**
     * title key
     * title key
     */
    const TITLE = "title";
    /**
     * title alternative key
     * title alternative key
     */
    const ALTER_TITLE = "alternative";
    /**
     * Search table name
     * 検索テーブル名
     */
    const TITLE_TABLE = "repository_search_title";
    /**
     * author key
     * author key
     */
    const AUTHOR = "creator";
    /**
     * Search table name
     * 検索テーブル名
     */
    const AUTHOR_TABLE = "repository_search_author";
    /**
     * keyword key
     * keyword key
     */
    const KEYWORD = "subject";
    /**
     * Search table name
     * 検索テーブル名
     */
    const KEYWORD_TABLE = "repository_search_keyword";
    /**
     * NII subject key
     * NII subject key
     */
    const NIISUBJECT = "NIIsubject";
    /**
     * Search table name
     * 検索テーブル名
     */
    const NIISUBJECT_TABLE = "repository_search_niisubject";
    /**
     * NDC key
     * NDC key
     */
    const NDC = "NDC";
    /**
     * Search table name
     * 検索テーブル名
     */
    const NDC_TABLE = "repository_search_ndc";
    /**
     * NDLC key
     * NDLC key
     */
    const NDLC = "NDLC";
    /**
     * Search table name
     * 検索テーブル名
     */
    const NDLC_TABLE = "repository_search_ndlc";
    /**
     * BSH key
     * BSH key
     */
    const BSH = "BSH";
    /**
     * Search table name
     * 検索テーブル名
     */
    const BSH_TABLE = "repository_search_bsh";
    /**
     * NDLSH key
     * NDLSH key
     */
    const NDLSH = "NDLSH";
    /**
     * Search table name
     * 検索テーブル名
     */
    const NDLSH_TABLE = "repository_search_ndlsh";
    /**
     * MeSH key
     * MeSH key
     */
    const MESH = "MeSH";
    /**
     * Search table name
     * 検索テーブル名
     */
    const MESH_TABLE = "repository_search_mesh";
    /**
     * DDC key
     * DDC key
     */
    const DDC = "DDC";
    /**
     * Search table name
     * 検索テーブル名
     */
    const DDC_TABLE = "repository_search_ddc";
    /**
     * LCC key
     * LCC key
     */
    const LCC = "LCC";
    /**
     * Search table name
     * 検索テーブル名
     */
    const LCC_TABLE = "repository_search_lcc";
    /**
     * UDC key
     * UDC key
     */
    const UDC = "UDC";
    /**
     * Search table name
     * 検索テーブル名
     */
    const UDC_TABLE = "repository_search_udc";
    /**
     * LCSH key
     * LCSH key
     */
    const LCSH = "LCSH";
    /**
     * Search table name
     * 検索テーブル名
     */
    const LCSH_TABLE = "repository_search_lcsh";
    /**
     * description key
     * description key
     */
    const DESCTIPTION = "description";
    /**
     * Search table name
     * 検索テーブル名
     */
    const DESCTIPTION_TABLE = "repository_search_description";
    /**
     * publisher key
     * publisher key
     */
    const PUBLISHER = "publisher";
    /**
     * Search table name
     * 検索テーブル名
     */
    const PUBLISHER_TABLE = "repository_search_publisher";
    /**
     * contributor key
     * contributor key
     */
    const CONTRIBUTOR = "contributor";
    /**
     * Search table name
     * 検索テーブル名
     */
    const CONTRIBUTOR_TABLE = "repository_search_contributor";
    /**
     * date key
     * date key
     */
    const DATE = "date";
    /**
     * Search table name
     * 検索テーブル名
     */
    const DATE_TABLE = "repository_search_date";
    /**
     * type key
     * type key
     */
    const TYPE = "type";
    /**
     * Search table name
     * 検索テーブル名
     */
    const TYPE_TABLE = "repository_search_type";
    /**
     * format key
     * format key
     */
    const FORMAT = "format";
    /**
     * Search table name
     * 検索テーブル名
     */
    const FORMAT_TABLE = "repository_search_format";
    /**
     * identifier key
     * identifier key
     */
    const IDENTIFER = "identifier";
    /**
     * Search table name
     * 検索テーブル名
     */
    const IDENTIFER_TABLE = "repository_search_identifier";
    /**
     * URI key
     * URI key
     */
    const URI = "URI";
    /**
     * Search table name
     * 検索テーブル名
     */
    const URI_TABLE = "repository_search_uri";
    /**
     * fulltextURL key
     * fulltextURL key
     */
    const FULLTEXTURL = "fullTextURL";
    /**
     * Search table name
     * 検索テーブル名
     */
    const FULLTEXTURL_TABLE = "repository_search_fulltexturl";
    /**
     * selfDOI key
     * selfDOI key
     */
    const SELFDOI = "selfDOI";
    /**
     * Search table name
     * 検索テーブル名
     */
    const SELFDOI_TABLE = "repository_search_selfdoi";
    /**
     * ISBN key
     * ISBN key
     */
    const ISBN = "isbn";
    /**
     * Search table name
     * 検索テーブル名
     */
    const ISBN_TABLE = "repository_search_isbn";
    /**
     * ISSN key
     * ISSN key
     */
    const ISSN = "issn";
    /**
     * Search table name
     * 検索テーブル名
     */
    const ISSN_TABLE = "repository_search_issn";
    /**
     * NCID key
     * NCID key
     */
    const NCID = "NCID";
    /**
     * Search table name
     * 検索テーブル名
     */
    const NCID_TABLE = "repository_search_ncid";
    /**
     * pmid key
     * pmid key
     */
    const PMID = "pmid";
    /**
     * Search table name
     * 検索テーブル名
     */
    const PMID_TABLE = "repository_search_pmid";
    /**
     * DOI key
     * DOI key
     */
    const DOI = "doi";
    /**
     * Search table name
     * 検索テーブル名
     */
    const DOI_TABLE = "repository_search_doi";
    /**
     * NAID key
     * NAID key
     */
    const NAID = "NAID";
    /**
     * Search table name
     * 検索テーブル名
     */
    const NAID_TABLE = "repository_search_naid";
    /**
     * ichushi key
     * ichushi key
     */
    const ICHUSHI = "ichushi";
    /**
     * Search table name
     * 検索テーブル名
     */
    const ICHUSHI_TABLE = "repository_search_ichushi";
    /**
     * jtitle key
     * jtitle key
     */
    const JTITLE = "jtitle";
    /**
     * Search table name
     * 検索テーブル名
     */
    const JTITLE_TABLE = "repository_search_jtitle";
    /**
     * date of issued key
     * date of issued key
     */
    const DATAODISSUED = "dateofissued";
    /**
     * Search table name
     * 検索テーブル名
     */
    const DATAODISSUED_TABLE = "repository_search_dateofissued";
    /**
     * language key
     * language key
     */
    const LANGUAGE = "language";
    /**
     * Search table name
     * 検索テーブル名
     */
    const LANGUAGE_TABLE = "repository_search_language";
    /**
     * relation key
     * relation key
     */
    const SPATIAL = "spatial";
    /**
     * relation key
     * relation key
     */
    const NIISPATIAL = "NIIspatial";
    /**
     * Search table name
     * 検索テーブル名
     */
    const RELATION_TABLE = "repository_search_relation";
    /**
     * coverage key
     * coverage key
     */
    const TEMPORAL = "temporal";
    /**
     * coverage key
     * coverage key
     */
    const NIITEMPORAL = "NIItemporal";
    /**
     * Search table name
     * 検索テーブル名
     */
    const COVERAGE_TABLE = "repository_search_coverage";
    /**
     * rights key
     * rights key
     */
    const RIGHTS = "rights";
    /**
     * Search table name
     * 検索テーブル名
     */
    const RIGHTS_TABLE = "repository_search_rights";
    /**
     * textversion key
     * textversion key
     */
    const TEXTVERSION = "textversion";
    /**
     * Search table name
     * 検索テーブル名
     */
    const TEXTVERSION_TABLE = "repository_search_textversion";
    /**
     * grant id key
     * grant id key
     */
    const GRANTID = "grantid";
    /**
     * Search table name
     * 検索テーブル名
     */
    const GRANTID_TABLE = "repository_search_grantid";
    /**
     * date of granted key
     * date of granted key
     */
    const DATEOFGRANTED = "dateofgranted";
    /**
     * Search table name
     * 検索テーブル名
     */
    const DATEOFGRANTED_TABLE = "repository_search_dateofgranted";
    /**
     * degree name key
     * degree name key
     */
    const DEGREENAME = "degreename";
    /**
     * Search table name
     * 検索テーブル名
     */
    const DEGREENAME_TABLE = "repository_search_degreename";
    /**
     * grantor key
     * grantor key
     */
    const GRANTOR = "grantor";
    /**
     * Search table name
     * 検索テーブル名
     */
    const GRANTOR_TABLE = "repository_search_grantor";
    /**
     * date of issued key
     * date of issued key
     */
    const DATAODISSUED_YMD = "shown_date";
    /**
     * Search table name
     * 検索テーブル名
     */
    const DATAODISSUED_YMD_TABLE = "repository_search_dateofissued_ymd";
    /**
     * Plugin parameter name
     * 検索プラグインパラメータ名
     */
    const SEARCH_QUERY_COLUMN = "search_query_plugin";

    /**
     * Relation between mapping and a table
     * マッピングとテーブルの対応付配列
     *
     * @var array
     */
    private $mappingTableRelation = null;
    
    /**
     * Instance of RepositoryHandleManager
     * RepositoryHandleManagerオブジェクト
     * 
     * @var RepositoryHandleManager
     */
    private $repositoryHandleManager = null;
    
    /**
     * Instance of query generator
     * QueryGeneratorオブジェクト
     * 
     * @var Repository_Components_Querygeneratorinterface
     */
    private $queryGenerator = null;
    
    /**
     * plugin exist flag
     * プラグインの存在フラグ
     * 
     * @var bool
     */
    private $pluginFlag = false;
    
    // Extend Search Keyword 2015/02/26 K.Sugimoto --start--
    /**
     * Instance of SearchKeywordConverter
     * Repository_Components_Searchkeywordconverterオブジェクト
     * 
     * @var Repository_Components_Searchkeywordconverter
     */
    private $searchKeywordConverter = null;
    // Extend Search Keyword 2015/02/26 K.Sugimoto --end--
    
    /**
     * RepositorySearchTableProcessing constructor.
     *
     * @param Session $Session session セッションオブジェクト
     * @param DbObjectAdodb $db DB object DBオブジェクト
     */
    public function __construct($Session, $db)
    {
        $this->Session = $Session;
        $this->Db = $db;
        $this->dbAccess = new RepositoryDbAccess($db);
        $this->setMappingTableRelation();
        // Add query manager 2014/08/21 T.Ichikawa --start--
        $pluginManager = new RepositoryPluginmanager($this->Session, $this->dbAccess, $this->TransStartDate);
        $this->queryGenerator = $pluginManager->getPlugin(RepositoryPluginManager::SEARCH_QUERY_COLUMN);
        if(isset($this->queryGenerator)) {
            $this->pluginFlag = true;
        } else {
            $this->queryGenerator = new Repository_Components_Querygenerator(DATABASE_PREFIX);
        }
        // Add query manager 2014/08/21 T.Ichikawa --end--
        
        // ロガー
        $this->Logger = WekoBusinessFactory::getFactory()->logger;
    }

    /**
     * update and insert search tables by all item
     * 全てのアイテムの検索テーブルを更新する
     */
    public function updateSearchTableForAllItem()
    {
        if($this->pluginFlag) {
            // プラグインが存在するなら新テーブルの作成処理を行う
            $query = "SHOW ENGINES";
            $engines = $this->dbAccess->executeQuery($query);
            $isMroongaExist = false;
            for($cnt = 0; $cnt < count($engines); $cnt++)
            {
                if($engines[$cnt]["Engine"] == "Mroonga" || $engines[$cnt]["Engine"] == "mroonga")
                {
                    $isMroongaExist = true;
                    break;
                }
            }
            $queryList = $this->queryGenerator->createPluginTableQuery($isMroongaExist);
            for($ii = 0; $ii < count($queryList); $ii++) {
                $this->dbAccess->executeQuery($queryList[$ii]["query"]);
            }
        }
        // insert no delete item to update item table
        $query = "INSERT IGNORE INTO ". DATABASE_PREFIX. "repository_search_update_item ".
                 " ( SELECT item_id, item_no ".
                 "   FROM ". DATABASE_PREFIX. "repository_item ".
                 "   WHERE is_delete = ? ) ;";

        $params = array();
        $params[] = 0;
        $this->dbAccess->executeQuery($query, $params);
        // execute background
        $this->callAsyncProcess();
    }

    /**
     * update and insert search tables the item by which item type was changed.
     * アイテムタイプが変更されたアイテムの検索テーブルを更新する
     *
     * @param int $itemtype_id changed item type ID アイテムタイプID
     */
    public function updateSearchTableForItemtype($itemtype_id)
    {
        // insert no delete item to update item table
        $query = "INSERT IGNORE INTO ". DATABASE_PREFIX. "repository_search_update_item ".
                 " ( SELECT item_id, item_no ".
                 "   FROM ". DATABASE_PREFIX. "repository_item ".
                 "   WHERE item_type_id = ? ".
                 "   AND is_delete = ? ) ;";
        $params = array();
        $params[] = $itemtype_id;
        $params[] = 0;
        $this->dbAccess->executeQuery($query, $params);
        // execute background
        $this->callAsyncProcess();
    }

    /**
     * update and insert search tables the item by which item was changed.
     * 更新されたアイテムの検索テーブルを更新する
     *
     * @param int $item_id item ID アイテムID
     * @param int $item_no item number アイテム通番
     */
    public function updateSearchTableForItem($item_id, $item_no)
    {
        // create update data
        $itemData = array();
        $itemData["item_id"] = $item_id;
        $itemData["item_no"] = $item_no;
        $param = array();
        array_push($param, $itemData);
        // execute update
        $this->setDataToSearchTable($param);
    }

    /**
     * delete records from search table
     * 検索テーブルからレコードを削除する
     */
    public function deleteDataFromSearchTable()
    {
        if($this->pluginFlag) {
            // プラグインが存在するならそちらの処理を行う
            $queryList = $this->queryGenerator->createDeletedDataFromSearchTableQuery();
            for($ii = 0; $ii < count($queryList); $ii++) {
                $this->dbAccess->executeQuery($queryList[$ii]["query"], $queryList[$ii]["params"]);
            }
        } else {
            // insert no delete item to update item table
            foreach($this->mappingTableRelation as $key => $value){
                $query = "DELETE FROM ".DATABASE_PREFIX.$key." ".
                         "WHERE (item_id, item_no) IN ( ".
                         " SELECT item_id, item_no ".
                         " FROM ". DATABASE_PREFIX. "repository_item ".
                         " WHERE is_delete = ? );";

                $params = array();
                $params[] = 1;
                $this->dbAccess->executeQuery($query, $params);
            }

            $query = "DELETE FROM ".DATABASE_PREFIX."repository_search_sort ".
                     "WHERE (item_id, item_no) IN ( ".
                     " SELECT item_id, item_no ".
                     " FROM ". DATABASE_PREFIX. "repository_item ".
                     " WHERE is_delete = ? );";

            $params = array();
            $params[] = 1;
            $this->dbAccess->executeQuery($query, $params);
        }
    }

    /**
     * delete records from search table
     * 指定されたアイテムの検索テーブルレコードを削除する
     *
     * @param array $itemList item list アイテムリスト
     *                         array[$ii]["item_id"|"item_no"]
     */
    private function deleteDataFromSearchTableByItemList($itemList)
    {
        if($this->pluginFlag) {
            // プラグインが存在するならそちらの処理を行う
            $queryList = $this->queryGenerator->createDeletedDataFromSearchTableByItemListQuery($itemList);
            for($ii = 0; $ii < count($queryList); $ii++) {
                $this->dbAccess->executeQuery($queryList[$ii]["query"], $queryList[$ii]["params"]);
            }
        } else {
            $inQuery = "";
            $params = array();
            for($ii = 0; $ii < count($itemList); $ii++){
                $inQuery .= "(?,?)";
                if($ii < count($itemList) - 1){
                    $inQuery .= ",";
                }
                $params[] = $itemList[$ii]["item_id"];
                $params[] = $itemList[$ii]["item_no"];
            }
            // insert no delete item to update item table
            foreach($this->mappingTableRelation as $key => $value){
                $query = "DELETE FROM ".DATABASE_PREFIX.$key." ".
                         "WHERE (item_id, item_no) IN (".$inQuery.");";
                $this->dbAccess->executeQuery($query, $params);
            }
            
            $query = "DELETE FROM ".DATABASE_PREFIX."repository_search_sort ".
                     "WHERE (item_id, item_no) IN (".$inQuery.");";
            
            $this->dbAccess->executeQuery($query, $params);
        }
    }
    
    // Add for OpenDepo R.Matsuura 2014/03/28 --start--
    /**
     * delete one record from search table
     * 指定されたアイテムの検索レコードを削除する
     *
     * @param int $item_id item ID アイテムID
     * @param int $item_no item number アイテム通番
     */
    public function deleteDataFromSearchTableByItemId($item_id, $item_no)
    {
        $itemList = array();
        $itemList[0]["item_id"] = $item_id;
        $itemList[0]["item_no"] = $item_no;
        
        $this->deleteDataFromSearchTableByItemList($itemList);
    }
    // Add for OpenDepo R.Matsuura 2014/03/28 --end--
    
    /**
     * insert search table in repository_search_update_item item
     * テーブルから更新対象を取得して検索テーブルを更新する
     *
     * @param array $itemList item list アイテムリスト
     *                         array[$ii]["item_id"|"item_no"]
     * @return bool true/false update success/update failed 更新成功/更新失敗
     * @throws RepositoryException
     */
    public function setDataToSearchTable($itemList)
    {
        $this->deleteDataFromSearchTableByItemList($itemList);
        
        // input update item data
        $searchAllInfo = array();
        $sortAllInfo = array();
        for($ii = 0; $ii < count($itemList); $ii++){
            // get item data
            $searchItemInfo = array();
            $searchItemInfo["item_id"] = $itemList[$ii]["item_id"];
            $searchItemInfo["item_no"] = $itemList[$ii]["item_no"];
            $sortItemInfo = array();
            $sortItemInfo["item_id"] = $itemList[$ii]["item_id"];
            $sortItemInfo["item_no"] = $itemList[$ii]["item_no"];
            $result = $this->getItemData($itemList[$ii]["item_id"], $itemList[$ii]["item_no"], $itemInfo, $errMsg);
            if($result === false){
                $this->failTrans();
                $exception = new RepositoryException( "ERR_MSG_Failed", 00001 );
                throw $exception;
            } else if(count($itemInfo["item"]) == 0) {
                // item deleted
                continue;
            }
            // input item base data
            $this->addBaseData($itemInfo["item"][0], $searchItemInfo, $sortItemInfo);
            // input item meta data
            for($jj = 0; $jj < count($itemInfo["item_attr_type"]); $jj++){
                $this->addInputData($itemInfo["item_attr"][$jj], $itemInfo["item_attr_type"][$jj],
                                    $searchItemInfo, $sortItemInfo);
            }
            // item shown status
            $searchItemInfo["shown_status"] = $itemInfo["item"][0]["shown_status"];
            
            array_push($searchAllInfo, $searchItemInfo);
            array_push($sortAllInfo, $sortItemInfo);
        }
        // add records to search tables
        if (count($searchAllInfo) > 0 || count($sortAllInfo) > 0 ) {
            $this->insertSearchTable($searchAllInfo, $sortAllInfo);
        }
        return true;
    }

    /**
     * Item basic information is set as search information
     * アイテム基本情報を検索テーブルに追加する
     *
     * @param array $itemBaseInfo item base information
     *                             アイテム基本情報
     *                             array[$ii]["item_id"|"item_no"|"title"|...]
     * @param array $searchItemInfo Data for search table
     *                               検索テーブル挿入用データ
     *                               array["allMetaData"|"creator"|"publisher"|"contributor"|...]
     * @param array $sortItemInfo Data for sort table
     *                             ソートテーブル挿入用データ
     *                             array["biblio_date"|...]
     */
    private function addBaseData($itemBaseInfo, &$searchItemInfo, &$sortItemInfo)
    {
        // set title data
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
        $toSearchKey = new ToSearchKey();
        $titleConverted = $this->convertSearchTableKeyword($itemBaseInfo["title"], self::TITLE, $toSearchKey);
        $titleEnglishConverted = $this->convertSearchTableKeyword($itemBaseInfo["title_english"], self::TITLE, $toSearchKey);
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
        $this->setTextData($searchItemInfo, self::TITLE, $titleConverted);
        $this->setTextData($searchItemInfo, self::TITLE, $titleEnglishConverted);
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
        $titleAllMetadataConverted = $this->convertSearchTableKeyword($itemBaseInfo["title"], self::ALLMETADATA, $toSearchKey);
        $titleEnglishAllMetadataConverted = $this->convertSearchTableKeyword($itemBaseInfo["title_english"], self::ALLMETADATA, $toSearchKey);
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
        $this->setTextData($searchItemInfo, self::ALLMETADATA, $titleAllMetadataConverted);
        $this->setTextData($searchItemInfo, self::ALLMETADATA, $titleEnglishAllMetadataConverted);
        $sortItemInfo["title"] = $itemBaseInfo["title"];
        $sortItemInfo["title_en"] = $itemBaseInfo["title_english"];
        // set shown date data
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
        $shownDateConverted = $this->convertSearchTableKeyword($itemBaseInfo["shown_date"], self::DATE, $toSearchKey);
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
        $this->setTextData($searchItemInfo, self::DATE, $shownDateConverted);
        $this->setTextData($searchItemInfo, self::DATAODISSUED_YMD, $shownDateConverted);
        // set keyword data
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
        $searchKeyConverted = $this->convertSearchTableKeyword($itemBaseInfo["serch_key"], self::KEYWORD, $toSearchKey);
        $searchKeyEnglishConverted = $this->convertSearchTableKeyword($itemBaseInfo["serch_key_english"], self::KEYWORD, $toSearchKey);
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
        $this->setTextData($searchItemInfo, self::KEYWORD, $searchKeyConverted);
        $this->setTextData($searchItemInfo, self::KEYWORD, $searchKeyEnglishConverted);
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
        $searchKeyAllMetadataConverted = $this->convertSearchTableKeyword($itemBaseInfo["serch_key"], self::ALLMETADATA, $toSearchKey);
        $searchKeyEnglishAllMetadataConverted = $this->convertSearchTableKeyword($itemBaseInfo["serch_key_english"], self::ALLMETADATA, $toSearchKey);
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
        $this->setTextData($searchItemInfo, self::ALLMETADATA, $searchKeyAllMetadataConverted);
        $this->setTextData($searchItemInfo, self::ALLMETADATA, $searchKeyEnglishAllMetadataConverted);
        // set language data
        $this->setTextData($searchItemInfo, self::LANGUAGE, $itemBaseInfo["language"]);
        // set itemtype data
        $sortItemInfo["item_type_id"] = $itemBaseInfo["item_type_id"];
        // set uri data
        if(strpos($itemBaseInfo["uri"], "repository_uri")){
            $sortItemInfo["uri"] = $itemBaseInfo["uri"];
            $sortItemInfo["weko_id"] = "1".sprintf("%08d", $itemBaseInfo["item_id"]);
        } else {
            $sortItemInfo["uri"] = $itemBaseInfo["uri"];
            $sortItemInfo["weko_id"] = "0".sprintf("%08d", $itemBaseInfo["item_id"]);
        }
        // set review_date data
        $sortItemInfo["review_date"] = $itemBaseInfo["review_date"];
        // set ins_user data
        $sortItemInfo["ins_user_id"] = $itemBaseInfo["ins_user_id"];
        // set mod_date data
        $sortItemInfo["mod_date"] = $itemBaseInfo["mod_date"];
        // set ins_date data
        $sortItemInfo["ins_date"] = $itemBaseInfo["ins_date"];
    }

    /**
     * Add item metadata information to data for search table
     * アイテムの各メタデータ情報を検索テーブル挿入用データに追加する
     *
     * @param array $itemMetaData Item metadata infomation
     *                            アイテムのメタデータ情報
     *                            array[$ii]["family"|"name"|"familiy_ruby"|"name_ruby"|...]
     * @param array $itemTypeMetaData Itemtype metadata infomation
     *                                アイテムタイプのメタデータ情報
     *                                array["junii2_mapping"|"dublin_core_mapping"|...]
     * @param array $searchItemInfo Data for search table
     *                              検索テーブル挿入用データ
     *                              array["allMetaData"|"creator"|"publisher"|"contributor"|...]
     * @param array $sortItemInfo Data for sort table
     *                            ソートテーブル挿入用データ
     *                            array["biblio_date"|...]
     */
    private function addInputData($itemMetaData, $itemTypeMetaData, &$searchItemInfo, &$sortItemInfo )
    {
        // set item metadata
        for($ii = 0; $ii < count($itemMetaData); $ii++){
	        $toSearchKey = new ToSearchKey();
            switch($itemTypeMetaData["input_type"])
            {
                case "name":
                    $this->createDataForSearchTableFromNameMetadata($itemMetaData[$ii], $itemTypeMetaData, $searchItemInfo);
                    break;
                case "thumbnail":
                    if(isset($itemTypeMetaData["junii2_mapping"]) && strlen($itemTypeMetaData["junii2_mapping"]) > 0){
                        $fileNameJunii2Converted = $this->convertSearchTableKeyword($itemMetaData[$ii]["file_name"], $itemTypeMetaData["junii2_mapping"], $toSearchKey);
                        $this->setTextData($searchItemInfo, $itemTypeMetaData["junii2_mapping"], $fileNameJunii2Converted);
                    }
				    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
                    if(isset($itemTypeMetaData["dublin_core_mapping"]) && strlen($itemTypeMetaData["dublin_core_mapping"]) > 0 && $itemTypeMetaData["dublin_core_mapping"] !== $itemTypeMetaData["junii2_mapping"]){
                        $fileNameDublinCoreConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["file_name"], $itemTypeMetaData["dublin_core_mapping"], $toSearchKey);
                        $this->setTextData($searchItemInfo, $itemTypeMetaData["dublin_core_mapping"], $fileNameDublinCoreConverted);
                    }
				    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
                    $fileNameAllMetadataConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["file_name"], self::ALLMETADATA, $toSearchKey);
                    $this->setTextData($searchItemInfo, self::ALLMETADATA, $fileNameAllMetadataConverted);
                    break;
                case "file":
                case "file_price":
                    $addText = $itemMetaData[$ii]["file_name"].",".$itemMetaData[$ii]["display_name"];
                    if(isset($itemTypeMetaData["junii2_mapping"]) && strlen($itemTypeMetaData["junii2_mapping"]) > 0){
                        $addTextJunii2Converted = $this->convertSearchTableKeyword($addText, $itemTypeMetaData["junii2_mapping"], $toSearchKey);
                        $this->setTextData($searchItemInfo, $itemTypeMetaData["junii2_mapping"], $addTextJunii2Converted);
                    }
				    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
                    if(isset($itemTypeMetaData["dublin_core_mapping"]) && strlen($itemTypeMetaData["dublin_core_mapping"]) > 0 && $itemTypeMetaData["dublin_core_mapping"] !== $itemTypeMetaData["junii2_mapping"]){
                        $addTextDublinCoreConverted = $this->convertSearchTableKeyword($addText, $itemTypeMetaData["dublin_core_mapping"], $toSearchKey);
                        $this->setTextData($searchItemInfo, $itemTypeMetaData["dublin_core_mapping"], $addTextDublinCoreConverted);
                    }
				    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
                    $addTextAllMetadataConverted = $this->convertSearchTableKeyword($addText, self::ALLMETADATA, $toSearchKey);
                    $this->setTextData($searchItemInfo, self::ALLMETADATA, $addTextAllMetadataConverted);
                    $this->addFileData($itemMetaData[$ii], $searchItemInfo);
                    
                    // Add free style license to search_rights table T.Koyasu 2014/06/10 --start--
                    if($itemMetaData[$ii]["license_id"] === "0"){
                        $licenseNotationRightsConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["license_notation"], self::RIGHTS, $toSearchKey);
                        $this->setTextData($searchItemInfo, self::RIGHTS, $licenseNotationRightsConverted);
                        $licenseNotationAllMetadataConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["license_notation"], self::ALLMETADATA, $toSearchKey);
                        $this->setTextData($searchItemInfo, self::ALLMETADATA, $licenseNotationAllMetadataConverted);
                    }
                    // Add free style license to search_rights table T.Koyasu 2014/06/10 --end--
                    
                    break;
                case "biblio_info":
                    $addText = $itemMetaData[$ii]["biblio_name"].",".$itemMetaData[$ii]["biblio_name_english"].",".$itemMetaData[$ii]["date_of_issued"];
                    $biblioNameJunii2Converted = $this->convertSearchTableKeyword($itemMetaData[$ii]["biblio_name"], self::JTITLE, $toSearchKey);
                    $biblioNameEnJunii2Converted = $this->convertSearchTableKeyword($itemMetaData[$ii]["biblio_name_english"], self::JTITLE, $toSearchKey);
                    $dateOfIssuedJunii2Converted = $this->convertSearchTableKeyword($itemMetaData[$ii]["date_of_issued"], self::DATAODISSUED, $toSearchKey);
                    $this->setTextData($searchItemInfo, self::JTITLE, $biblioNameJunii2Converted);
                    $this->setTextData($searchItemInfo, self::JTITLE, $biblioNameEnJunii2Converted);
                    $this->setTextData($searchItemInfo, self::DATAODISSUED, $dateOfIssuedJunii2Converted);
				    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
                    $biblioNameDublinCoreConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["biblio_name"], self::IDENTIFER, $toSearchKey);
                    $biblioNameEnDublinCoreConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["biblio_name_english"], self::IDENTIFER, $toSearchKey);
                    $dateOfIssuedDublinCoreConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["date_of_issued"], self::IDENTIFER, $toSearchKey);
                    $this->setTextData($searchItemInfo, self::IDENTIFER, $biblioNameDublinCoreConverted);
                    $this->setTextData($searchItemInfo, self::IDENTIFER, $biblioNameEnDublinCoreConverted);
                    $this->setTextData($searchItemInfo, self::IDENTIFER, $dateOfIssuedDublinCoreConverted);
				    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
                    // Fix Don't fill biblio_date at sort table. Y.Nakao 2014/03/26 --start--
                    if(!isset($sortItemInfo["biblio_date"]))
                    {
                        $this->setTextData($sortItemInfo, "biblio_date", $itemMetaData[$ii]["date_of_issued"]);
                    }
                    // Fix Don't fill biblio_date at sort table. Y.Nakao 2014/03/26 --end--
                    $addTextAllMetadataConverted = $this->convertSearchTableKeyword($addText, self::ALLMETADATA, $toSearchKey);
                    $this->setTextData($searchItemInfo, self::ALLMETADATA, $addTextAllMetadataConverted);
                    break;
                case "supple":
                    if(isset($itemTypeMetaData["junii2_mapping"]) && strlen($itemTypeMetaData["junii2_mapping"]) > 0){
                    	$suppleTitleJunii2Converted = $this->convertSearchTableKeyword($itemMetaData[$ii]["supple_title"], $itemTypeMetaData["junii2_mapping"], $toSearchKey);
                    	$suppleTitleEnTextJunii2Converted = $this->convertSearchTableKeyword($itemMetaData[$ii]["supple_title_en"], $itemTypeMetaData["junii2_mapping"], $toSearchKey);
                        $this->setTextData($searchItemInfo, $itemTypeMetaData["junii2_mapping"], $suppleTitleJunii2Converted);
                        $this->setTextData($searchItemInfo, $itemTypeMetaData["junii2_mapping"], $suppleTitleEnTextJunii2Converted);
                    }
				    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
                    if(isset($itemTypeMetaData["dublin_core_mapping"]) && strlen($itemTypeMetaData["dublin_core_mapping"]) > 0 && $itemTypeMetaData["dublin_core_mapping"] !== $itemTypeMetaData["junii2_mapping"]){
                    	$suppleTitleDublinCoreConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["supple_title"], $itemTypeMetaData["dublin_core_mapping"], $toSearchKey);
                    	$suppleTitleEnTextDublinCoreConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["supple_title_en"], $itemTypeMetaData["dublin_core_mapping"], $toSearchKey);
                        $this->setTextData($searchItemInfo, $itemTypeMetaData["dublin_core_mapping"], $suppleTitleDublinCoreConverted);
                        $this->setTextData($searchItemInfo, $itemTypeMetaData["dublin_core_mapping"], $suppleTitleEnTextDublinCoreConverted);
                    }
				    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
                    $suppleTitleAllMetadataConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["supple_title"], self::ALLMETADATA, $toSearchKey);
                    $suppleTitleEnTextAllMetadataConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["supple_title_en"], self::ALLMETADATA, $toSearchKey);
                    $this->setTextData($searchItemInfo, self::ALLMETADATA, $suppleTitleAllMetadataConverted);
                    $this->setTextData($searchItemInfo, self::ALLMETADATA, $suppleTitleEnTextAllMetadataConverted);
                    break;
                default:
                    if(isset($itemTypeMetaData["junii2_mapping"]) && strlen($itemTypeMetaData["junii2_mapping"]) > 0){
                    	$attributeValueJunii2Converted = $this->convertSearchTableKeyword($itemMetaData[$ii]["attribute_value"], $itemTypeMetaData["junii2_mapping"], $toSearchKey);
                        $this->setTextData($searchItemInfo, $itemTypeMetaData["junii2_mapping"], $attributeValueJunii2Converted);
                        // Fix Don't fill biblio_date at sort table. Y.Nakao 2014/03/26 --start--
                        if(!isset($sortItemInfo["biblio_date"]) && $itemTypeMetaData["junii2_mapping"] == self::DATAODISSUED)
                        {
                            $this->setTextData($sortItemInfo, "biblio_date", $itemMetaData[$ii]["attribute_value"]);
                        }
                        // Fix Don't fill biblio_date at sort table. Y.Nakao 2014/03/26 --end--
                    }
				    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
                    if(isset($itemTypeMetaData["dublin_core_mapping"]) && strlen($itemTypeMetaData["dublin_core_mapping"]) > 0 && $itemTypeMetaData["dublin_core_mapping"] !== $itemTypeMetaData["junii2_mapping"]){
                    	$attributeValueDublinCoreConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["attribute_value"], $itemTypeMetaData["dublin_core_mapping"], $toSearchKey);
                        $this->setTextData($searchItemInfo, $itemTypeMetaData["dublin_core_mapping"], $attributeValueDublinCoreConverted);
                        // Fix Don't fill biblio_date at sort table. Y.Nakao 2014/03/26 --start--
                        if(!isset($sortItemInfo["biblio_date"]) && $itemTypeMetaData["dublin_core_mapping"] == self::DATAODISSUED)
                        {
                            $this->setTextData($sortItemInfo, "biblio_date", $itemMetaData[$ii]["attribute_value"]);
                        }
                        // Fix Don't fill biblio_date at sort table. Y.Nakao 2014/03/26 --end--
                    }
				    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
                    $attributeValueAllMetadataConverted = $this->convertSearchTableKeyword($itemMetaData[$ii]["attribute_value"], self::ALLMETADATA, $toSearchKey);
                    $this->setTextData($searchItemInfo, self::ALLMETADATA, $attributeValueAllMetadataConverted);
                    break;
            }
        }
    }
    
    /**
     * Create data for search table from name metadata
     * 氏名属性のメタデータから検索テーブル挿入用データを作成する
     *
     * @param array $nameMetadata Name metadata infomation
     *                            氏名属性のメタデータ情報
     *                            array["family"|"name"|"familiy_ruby"|"name_ruby"|...]
     * @param array $itemTypeMetadata Itemtype metadata infomation
     *                                アイテムタイプのメタデータ情報
     *                                array["junii2_mapping"|"dublin_core_mapping"|...]
     * @param array $searchItemInfo Data for search table
     *                              検索テーブル挿入用データ
     *                              array["allMetaData"|"creator"|"publisher"|"contributor"|...]
     */
    private function createDataForSearchTableFromNameMetadata($nameMetadata, $itemTypeMetadata, &$searchItemInfo)
    {
        $toSearchKey = new ToSearchKey();
        // アイテムに登録された氏名属性のメタデータを下記の形式で検索テーブルに追加するようにする
        // 姓,名,姓名,名姓,ヨミ(姓),ヨミ(名),ヨミ(姓)ヨミ(名),メールアドレス,外部著者ID_1,外部著者ID_2,……,外部著者ID_N
        $addText = $nameMetadata["family"].",".$nameMetadata["name"].
                   ",".$nameMetadata["family"].$nameMetadata["name"].
                   ",".$nameMetadata["name"].$nameMetadata["family"].
                   ",".$nameMetadata["family_ruby"].",".$nameMetadata["name_ruby"].
                   ",".$nameMetadata["family_ruby"].$nameMetadata["name_ruby"].
                   ",".$nameMetadata["e_mail_address"];
        $idList = $this->getSuffixId($nameMetadata["author_id"]);
        for($jj = 0; $jj < count($idList); $jj++){
            $addText .= ",".$idList[$jj]["suffix"];
        }
        
        // junii2マッピングを参照し、それに対応した検索テーブルに追加するよう検索テーブル挿入用データを更新する
        if(isset($itemTypeMetadata["junii2_mapping"]) && strlen($itemTypeMetadata["junii2_mapping"]) > 0){
            // キーワード検索時の揺らぎを吸収するため、全角英数字カナを半角に変換し、検索テーブルに追加するようにする
            $addTextJunii2Converted = $this->convertSearchTableKeyword($addText, $itemTypeMetadata["junii2_mapping"], $toSearchKey);
            $this->setTextData($searchItemInfo, $itemTypeMetadata["junii2_mapping"], $addTextJunii2Converted);
        }
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
        // DublinCoreマッピングだけが設定されたメタデータも詳細検索ができるように、DublinCoreマッピングに対応した検索テーブルに追加するよう検索テーブル挿入用データを更新する
        // (junii2マッピングとDublinCoreマッピングの値が異なるときのみ実施)
        if(isset($itemTypeMetadata["dublin_core_mapping"]) && strlen($itemTypeMetadata["dublin_core_mapping"]) > 0 && $itemTypeMetadata["dublin_core_mapping"] !== $itemTypeMetadata["junii2_mapping"]){
            // キーワード検索時の揺らぎを吸収するため、全角英数字カナを半角に変換し、検索テーブルに追加するようにする
            $addTextDublinCoreConverted = $this->convertSearchTableKeyword($addText, $itemTypeMetadata["dublin_core_mapping"], $toSearchKey);
            $this->setTextData($searchItemInfo, $itemTypeMetadata["dublin_core_mapping"], $addTextDublinCoreConverted);
        }
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
        // キーワード検索時の揺らぎを吸収するため、全角英数字カナを半角に変換し、検索テーブルに追加するようにする
        $addTextAllMetadataConverted = $this->convertSearchTableKeyword($addText, self::ALLMETADATA, $toSearchKey);
        $this->setTextData($searchItemInfo, self::ALLMETADATA, $addTextAllMetadataConverted);
    }

    /**
     * The contents of a file are set as search information
     * ファイルの中身を検索テーブルに追加する
     *
     * @param array $itemMetaData item metadata information
     *                             アイテムメタデータ情報
     *                             array[$ii]["item_id"|"item_no"|"title"|...]
     * @param array $searchItemInfo Data for search table
     *                              検索テーブル挿入用データ
     *                              array["allMetaData"|"creator"|"publisher"|"contributor"|...]
     */
    private function addFileData($itemMetaData, &$searchItemInfo)
    {
        $strFullText = "";
        ////////////////////////////////////////////////////////////
        // ファイルから文字列を抽出し、検索用文字列を作成する
        ////////////////////////////////////////////////////////////
        // 作業用ディレクトリ作成
        $this->infoLog("businessWorkdirectory", __FILE__, __CLASS__, __LINE__);
        $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness("businessWorkdirectory");
        
        $dir_path = $businessWorkdirectory->create();
        
        $file_name = $itemMetaData['item_id'].'_'.
                    $itemMetaData['attribute_id'].'_'.
                    $itemMetaData['file_no'].'.'.
                    $itemMetaData['extension'];
        // Add File replace T.Koyasu 2016/02/29 --start--
        $business = BusinessFactory::getFactory()->getBusiness("businessContentfiletransaction");
        $business->copyTo($itemMetaData['item_id'], $itemMetaData['attribute_id'], $itemMetaData['file_no'], 0, $dir_path.$file_name);
        // Add File replace T.Koyasu 2016/02/29 --end--
        
        $txt = "";
        // 外部コマンドパス設定読込み追加 2008/08/07 Y.Nakao --start--
        // wvWare
        $query = "SELECT `param_value` ".
                 "FROM `". DATABASE_PREFIX ."repository_parameter` ".
                 "WHERE `param_name` = 'path_wvWare';";
        $ret = $this->dbAccess->executeQuery($query);
        if(count($ret) > 0){
            $path_wvWare = $ret[0]['param_value'];
        } else {
            $path_wvWare = "";
        }
        // xlhtml
        $query = "SELECT `param_value` ".
                 "FROM `". DATABASE_PREFIX ."repository_parameter` ".
                 "WHERE `param_name` = 'path_xlhtml';";
        $ret = $this->dbAccess->executeQuery($query);
        if(count($ret) > 0){
            $path_xlhtml = $ret[0]['param_value'];
        } else {
            $path_xlhtml = "";
        }
        // poppler
        $query = "SELECT `param_value` ".
                 "FROM `". DATABASE_PREFIX ."repository_parameter` ".
                 "WHERE `param_name` = 'path_poppler';";
        $ret = $this->dbAccess->executeQuery($query);
        if(count($ret) > 0){
            $path_poppler = $ret[0]['param_value'];
        } else {
            $path_poppler = "";
        }
        // Fix processing order correcting. 2014/03/15 Y.Nakao
        // Add separate file from DB 2009/04/21 Y.Nakao --start--

        $txt = "";
        // ファイルがpdf・xls・docの場合
        if ( ( $itemMetaData['mime_type'] == 'application/pdf' ||
               $itemMetaData['mime_type'] == 'application/vnd.ms-excel' ||
               $itemMetaData['mime_type'] == 'application/msword' ||
               $itemMetaData['mime_type'] == 'text/pdf')) {
            // 生成ファイルからTEXT抽出
            $cmd = null;
            // pdfの場合
            if ($itemMetaData['mime_type'] == 'application/pdf' || $itemMetaData['mime_type'] == 'text/pdf') {
                $cmd = "\"". $path_poppler. "pdftotext\" -enc UTF-8 ". $dir_path.$file_name. " ". $dir_path. "pdf.txt";
                //print($cmd. "<br>");
                exec($cmd);
                if (file_exists($dir_path. "pdf.txt")) {
                    $txt = file($dir_path. "pdf.txt");
                    //$txt = implode("", $txt);
                    $strFullText = implode("", $txt);
                    unlink($dir_path. "pdf.txt");
                }
            }
            // xlsの場合
            else if ($itemMetaData['mime_type'] == 'application/vnd.ms-excel') {
                $cmd = "\"". $path_xlhtml. "xlhtml\" ". $dir_path.$file_name. " > ". $dir_path. "xls.html";
                //print($cmd. "<br>");
                exec($cmd);
                if (file_exists($dir_path. "xls.html")) {
                    $txt = file($dir_path. "xls.html");
                    $txt = implode("", $txt);
                    //$txt = strip_tags($txt);
                    $strFullText = strip_tags($txt);
                    unlink($dir_path. "xls.html");
                }
            }
            // docの場合
            else if ($itemMetaData['mime_type'] == 'application/msword') {
                $cmd = "\"". $path_wvWare. "wvHtml\" ". $dir_path.$file_name. " ". $dir_path. "doc.html";
                //print($cmd. "<br>");
                exec($cmd);
                if (file_exists($dir_path. "doc.html")) {
                    $txt = file($dir_path. "doc.html");
                    $txt = implode("", $txt);
                    //$txt = strip_tags($txt);
                    $strFullText = strip_tags($txt);
                    unlink($dir_path. "doc.html");
                }
            }
        }
        // ファイルがテキスト類の場合
        else if ( is_numeric(strpos($itemMetaData['mime_type'], "text")) ) {
            $fp = fopen($dir_path.$file_name, "r");
            if($fp == null){
                return;
            }
            while( ! feof( $fp ) ){
                $line = fgets( $fp );
                $mojicode = mb_detect_encoding($line, "auto", false);
                if(strtoupper($mojicode) != 'UTF-8')
                {
                    $line= mb_convert_encoding($line, "UTF-8", $mojicode);
                }
                $txt .= $line;
            }
            fclose($fp);
            $strFullText = $txt;
            unlink($dir_path.$file_name);
        }
        // ppt
        else if($itemMetaData['mime_type'] == 'application/vnd.ms-powerpoint'){
            $cmd = "\"". $path_xlhtml. "ppthtml\" ". $dir_path.$file_name. " > ". $dir_path. "ppt.html";
            exec($cmd);
            if (file_exists($dir_path. "ppt.html")) {
                $txt = file($dir_path. "ppt.html");
                $txt = implode("", $txt);
                $strFullText = strip_tags($txt);
                unlink($dir_path. "ppt.html");
            }
        }
        // docx
        else if($itemMetaData['mime_type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
            // docx to zip rename
            copy( $dir_path.$file_name, $dir_path. "docx.zip" );
            $tag_val_list = array();
            if (file_exists($dir_path. "docx.zip")) {
                $this->zipDecompress($dir_path, "docx.zip");
                // document.xml get value
                $xml_path = $dir_path."docx". DIRECTORY_SEPARATOR."word";
                $getTagResult = $this->getOfficeXMLText($xml_path, "document.xml");
                if($getTagResult !== false){
                    $strFullText = $getTagResult;
                }
                $this->removeDirectory($dir_path."docx");
            }
        }
        // xlsx
        else if($itemMetaData['mime_type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
            // xlsx to zip
            copy( $dir_path.$file_name, $dir_path. "xlsx.zip" );
            $tag_val_list = array();
            if (file_exists($dir_path. "xlsx.zip")) {

                $this->zipDecompress($dir_path, "xlsx.zip");
                // workbook.xml
                $xml_path = $dir_path."xlsx". DIRECTORY_SEPARATOR."xl";
                $getTagResult = $this->getOfficeXMLAttributes($xml_path, "workbook.xml");
                if($getTagResult !== false){
                    $strFullText = $getTagResult;
                }

                // sharedStrings.xml
                $getTagResult = $this->getOfficeXMLText($xml_path, "sharedStrings.xml");
                if($getTagResult !== false){
                    $strFullText = $strFullText.",". $getTagResult;
                }
                $this->removeDirectory($dir_path."xlsx");
            }
        }
        // pptx
        else if($itemMetaData['mime_type'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation'){
            // pptx to zip
            copy( $dir_path.$file_name, $dir_path. "pptx.zip" );
            $tag_val_list = array();
            if (file_exists($dir_path. "pptx.zip")) {
                $this->zipDecompress($dir_path, "pptx.zip");
                $xml_path = $dir_path."pptx". DIRECTORY_SEPARATOR."ppt". DIRECTORY_SEPARATOR."slides";

                $noCnt = 1;
                foreach (glob($xml_path. DIRECTORY_SEPARATOR.'slide*.xml') as $sheet) {
                    $getTagResult = $this->getOfficeXMLText($xml_path, "slide".$noCnt.".xml");
                    if($getTagResult !== false){
                        $strFullText .= $getTagResult;
                        $noCnt++;
                    }
                }
                $this->removeDirectory($dir_path."pptx");
            }
        }
        // MySQLは4バイト文字に対応していないため4バイト文字を削除
        $strFullText = preg_replace("/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/", "", $strFullText);
        
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
        $toSearchKey = new ToSearchKey();
        $strFullText = $this->convertSearchTableKeyword($strFullText, self::FILEDATA, $toSearchKey);
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--

        $this->setTextData($searchItemInfo, self::FILEDATA, $strFullText);
    }

    /**
     * Insert search table
     * 検索テーブルに追加する
     *
     * @param array $searchAllItemInfo all item search data
     *                                  アイテムメタデータ情報
     *                                  array[$ii]["item_id"|"item_no"|"title"|...]
     * @param array $sortAllItemInfo all item sort data
     *                                検索テーブル挿入用データ
     *                                array[$ii]["allMetaData"|"creator"|"publisher"|"contributor"|...]
     */
    private function insertSearchTable( $searchAllItemInfo, $sortAllItemInfo )
    {
        if($this->pluginFlag) {
            // プラグインが存在するならそちらの処理を行う
            $queryList = $this->queryGenerator->createInsertSearchTableQuery($searchAllItemInfo, $sortAllItemInfo);
            for($ii = 0; $ii < count($queryList); $ii++) {
                $this->dbAccess->executeQuery($queryList[$ii]["query"], $queryList[$ii]["params"]);
            }
        } else {
            // insert no delete item to update item table
            foreach($this->mappingTableRelation as $key => $value){
                $insertItemList = array();
                for($ii = 0; $ii < count($searchAllItemInfo); $ii++){
                    $insertString = "";
                    for($jj = 0; $jj < count($value); $jj++){
                        if(!isset($searchAllItemInfo[$ii][$value[$jj]]) || strlen($searchAllItemInfo[$ii][$value[$jj]]) == 0){
                            continue;
                        }
                        if(strlen($insertString) == 0){
                            $insertString = $searchAllItemInfo[$ii][$value[$jj]];
                        } else {
                            $insertString .= ",".$searchAllItemInfo[$ii][$value[$jj]];
                        }
                    }
                    // Fix When $key == self::FILEDATA_TABLE and $insertString is empty, insert data at search table. 2014/03/26 Y.Nakao --start--
                    if(strlen($insertString) == 0){
                        continue;
                    }
                    // Fix When $key == self::FILEDATA_TABLE and $insertString is empty, insert data at search table. 2014/03/26 Y.Nakao --end--
                    $insertData = array("item_id" => $searchAllItemInfo[$ii]["item_id"],
                                        "item_no" => $searchAllItemInfo[$ii]["item_no"],
                                        "meta_data" => $insertString);
                    array_push($insertItemList, $insertData);
                }
                $params = array();
                $count = 0;
                
                // Add new prefix 2014/01/15 T.Ichikawa --start--                    
                if(strcmp($key, self::SELFDOI_TABLE) == 0)
                {
                    for($ii = 0; $ii < count($searchAllItemInfo); $ii++){
                        $this->updateSelfDoiSearchTable($searchAllItemInfo[$ii]["item_id"], $searchAllItemInfo[$ii]["item_no"]);
                    }
                }
                // Add new prefix 2014/01/15 T.Ichikawa --end--
                
                if(count($insertItemList) == 0){
                    continue;
                }
                $inQuery = "";
                switch($key){
                    case self::DATE_TABLE:
                    case self::DATAODISSUED_TABLE:
                    case self::DATEOFGRANTED_TABLE:
                    case self::DATAODISSUED_YMD_TABLE:
                        $query = "INSERT INTO ".DATABASE_PREFIX.$key." VALUES ";
                        for($ii = 0; $ii < count($insertItemList); $ii++){
                            $tmpDate = explode(",", $insertItemList[$ii]["meta_data"]);
                            $data_no = 1;
                            for($kk = 0; $kk < count($tmpDate); $kk++){
                                if(strlen($tmpDate[$kk]) == 0){
                                    continue;
                                }
                                if($count != 0){
                                    $inQuery .= ",";
                                }
                                $inQuery .= "(?,?,?,?)";
                                $params[] = $insertItemList[$ii]["item_id"];
                                $params[] = $insertItemList[$ii]["item_no"];
                                $params[] = $data_no;
                                if($key == self::DATAODISSUED_TABLE){
                                    $dateList = explode("-", $tmpDate[$kk]);
                                    $params[] = str_pad(intval($dateList[0]), 4, '0', STR_PAD_LEFT);
                                } else {
                                    $dateList = explode(" ", $tmpDate[$kk]);
                                    $params[] = str_replace("-", "", $dateList[0]);
                                }
                                $count++;
                                $data_no++;
                            }
                        }
                        break;
                        
                    default:
                        $query = "INSERT INTO ".DATABASE_PREFIX.$key." VALUES ";
                        for($ii = 0; $ii < count($insertItemList); $ii++){
                            if($count != 0){
                                $inQuery .= ",";
                            }
                            $inQuery .= "(?,?,?)";
                            $params[] = $insertItemList[$ii]["item_id"];
                            $params[] = $insertItemList[$ii]["item_no"];
                            $params[] = $insertItemList[$ii]["meta_data"];
                            $count++;
                        }
                        break;
                }
                if($count == 0){
                    continue;
                }
                $query .= $inQuery;
                $this->dbAccess->executeQuery($query, $params);
            }
            
            $query = "INSERT INTO ".DATABASE_PREFIX."repository_search_sort ".
                     "(item_id, item_no, item_type_id, weko_id, title, title_en, ".
                     "uri, review_date, ins_user_id, mod_date, ins_date, biblio_date) VALUES ";
            $count = 0;
            $params = array();
            $inQuery = "";
            for($ii = 0; $ii < count($sortAllItemInfo); $ii++)
            {
                if($count != 0){
                    $inQuery .= ",";
                }
                $inQuery .= "(?,?,?,?,?,?,?,?,?,?,?,?)";
                $params[] = $sortAllItemInfo[$ii]["item_id"];
                $params[] = $sortAllItemInfo[$ii]["item_no"];
                $params[] = $sortAllItemInfo[$ii]["item_type_id"];
                if(isset($sortAllItemInfo[$ii]["weko_id"])){
                    $params[] = $sortAllItemInfo[$ii]["weko_id"];
                } else {
                    $params[] = 0;
                }
                if(strlen($sortAllItemInfo[$ii]["title"]) != 0){
                    $params[] = $sortAllItemInfo[$ii]["title"];
                } else {
                    $params[] = $sortAllItemInfo[$ii]["title_en"];
                }
                if(strlen($sortAllItemInfo[$ii]["title_en"]) != 0){
                    $params[] = $sortAllItemInfo[$ii]["title_en"];
                } else {
                    $params[] = $sortAllItemInfo[$ii]["title"];
                }
                $params[] = $sortAllItemInfo[$ii]["uri"];
                $params[] = $sortAllItemInfo[$ii]["review_date"];
                $params[] = $sortAllItemInfo[$ii]["ins_user_id"];
                $params[] = $sortAllItemInfo[$ii]["mod_date"];
                $params[] = $sortAllItemInfo[$ii]["ins_date"];
                if(isset($sortAllItemInfo[$ii]["biblio_date"])){
                    $params[] = $sortAllItemInfo[$ii]["biblio_date"];
                } else {
                    $params[] = "";
                }
                $count++;
            }
            if($count == 0){
                return;
            }
            $query .= $inQuery;
            $this->dbAccess->executeQuery($query, $params);
        }
    }

    /**
     * recursive process
     * 再帰実行
     *
     * @return execute result 実行結果
     */
    private function callAsyncProcess()
    {

        // Request parameter for next URL
        $nextRequest = BASE_URL."/?action=repository_action_common_search_update";

        $result = RepositoryProcessUtility::callAsyncProcess($nextRequest);
        return $result;
    }

    /**
     * Set mapping and table relation info
     * マッピングとテーブルの対応付けを行う
     */
    private function setMappingTableRelation()
    {
        $this->mappingTableRelation = array();
        $this->mappingTableRelation[self::ALLMETADATA_TABLE][0] =  self::ALLMETADATA;
        $this->mappingTableRelation[self::FILEDATA_TABLE][0] =  self::FILEDATA;
        $this->mappingTableRelation[self::TITLE_TABLE][0] =  self::TITLE;
        $this->mappingTableRelation[self::TITLE_TABLE][1] =  self::ALTER_TITLE;
        $this->mappingTableRelation[self::AUTHOR_TABLE][0] =  self::AUTHOR;
        $this->mappingTableRelation[self::KEYWORD_TABLE][0] =  self::KEYWORD;
        $this->mappingTableRelation[self::NIISUBJECT_TABLE][0] =  self::NIISUBJECT;
        $this->mappingTableRelation[self::NDC_TABLE][0] =  self::NDC;
        $this->mappingTableRelation[self::NDLC_TABLE][0] =  self::NDLC;
        $this->mappingTableRelation[self::BSH_TABLE][0] =  self::BSH;
        $this->mappingTableRelation[self::NDLSH_TABLE][0] =  self::NDLSH;
        $this->mappingTableRelation[self::MESH_TABLE][0] =  self::MESH;
        $this->mappingTableRelation[self::DDC_TABLE][0] =  self::DDC;
        $this->mappingTableRelation[self::LCC_TABLE][0] =  self::LCC;
        $this->mappingTableRelation[self::UDC_TABLE][0] =  self::UDC;
        $this->mappingTableRelation[self::LCSH_TABLE][0] =  self::LCSH;
        $this->mappingTableRelation[self::DESCTIPTION_TABLE][0] =  self::DESCTIPTION;
        $this->mappingTableRelation[self::PUBLISHER_TABLE][0] =  self::PUBLISHER;
        $this->mappingTableRelation[self::CONTRIBUTOR_TABLE][0] =  self::CONTRIBUTOR;
        $this->mappingTableRelation[self::DATE_TABLE][0] =  self::DATE;
        $this->mappingTableRelation[self::TYPE_TABLE][0] =  self::TYPE;
        $this->mappingTableRelation[self::FORMAT_TABLE][0] =  self::FORMAT;
        $this->mappingTableRelation[self::IDENTIFER_TABLE][0] =  self::IDENTIFER;
        $this->mappingTableRelation[self::URI_TABLE][0] =  self::URI;
        $this->mappingTableRelation[self::FULLTEXTURL_TABLE][0] =  self::FULLTEXTURL;
        $this->mappingTableRelation[self::SELFDOI_TABLE][0] =  self::SELFDOI;
        $this->mappingTableRelation[self::ISBN_TABLE][0] =  self::ISBN;
        $this->mappingTableRelation[self::ISSN_TABLE][0] =  self::ISSN;
        $this->mappingTableRelation[self::NCID_TABLE][0] =  self::NCID;
        $this->mappingTableRelation[self::PMID_TABLE][0] =  self::PMID;
        $this->mappingTableRelation[self::DOI_TABLE][0] =  self::DOI;
        $this->mappingTableRelation[self::NAID_TABLE][0] =  self::NAID;
        $this->mappingTableRelation[self::ICHUSHI_TABLE][0] =  self::ICHUSHI;
        $this->mappingTableRelation[self::JTITLE_TABLE][0] =  self::JTITLE;
        $this->mappingTableRelation[self::DATAODISSUED_TABLE][0] =  self::DATAODISSUED;
        $this->mappingTableRelation[self::LANGUAGE_TABLE][0] =  self::LANGUAGE;
        $this->mappingTableRelation[self::RELATION_TABLE][0] =  self::SPATIAL;
        $this->mappingTableRelation[self::RELATION_TABLE][1] =  self::NIISPATIAL;
        $this->mappingTableRelation[self::COVERAGE_TABLE][0] =  self::TEMPORAL;
        $this->mappingTableRelation[self::COVERAGE_TABLE][1] =  self::NIITEMPORAL;
        $this->mappingTableRelation[self::RIGHTS_TABLE][0] =  self::RIGHTS;
        $this->mappingTableRelation[self::TEXTVERSION_TABLE][0] =  self::TEXTVERSION;
        $this->mappingTableRelation[self::GRANTID_TABLE][0] =  self::GRANTID;
        $this->mappingTableRelation[self::DATEOFGRANTED_TABLE][0] =  self::DATEOFGRANTED;
        $this->mappingTableRelation[self::DEGREENAME_TABLE][0] =  self::DEGREENAME;
        $this->mappingTableRelation[self::GRANTOR_TABLE][0] =  self::GRANTOR;
        $this->mappingTableRelation[self::DATAODISSUED_YMD_TABLE][0] =  self::DATAODISSUED_YMD;
    }

    /**
     * Set text data
     * テキスト情報を連結する
     *
     * @param array $registArray text array テキスト配列
     *                            array[$registKey]
     * @param string $registKey mapping key マッピングキー
     * @param string $registValue text テキスト
     *
     */
    private function setTextData(&$registArray, $registKey, $registValue)
    {
        if(strlen($registValue) == 0){
            return;
        }
        if(isset($registArray[$registKey]) && strlen($registArray[$registKey]) != 0 )
        {
            $registArray[$registKey] .= ",".$registValue;
        } else {
            $registArray[$registKey] = $registValue;
        }
    }
    /**
     * get suffix id for author
     * 著者のサフィックス情報を取得する
     *
     * @param int $auth_id author ID 著者ID
     * @return array author suffix 著者サフィックス
     *                array[$ii]["author_id"|"suffix"|...]
     */
    private function getSuffixId($auth_id)
    {
        $query = "SELECT suffix ".
                 "FROM ".DATABASE_PREFIX."repository_external_author_id_suffix ".
                 "WHERE author_id = ? ;";
        $params = array();
        $params[] = $auth_id;
        $result = $this->dbAccess->executeQuery($query, $params);
        return $result;
    }
    /**
     * Update SelfDoi search table
     * seld DOIの検索テーブルを更新する
     *
     * @param int $item_id item ID アイテムID
     * @param int $item_no item number アイテム通番
     */
    public function updateSelfDoiSearchTable($item_id, $item_no)
    {
        $this->getRepositoryHandleManager();
        
        $uri = "";
        $uri_jalcdoi = $this->repositoryHandleManager->createSelfDoiUri($item_id, $item_no, RepositoryHandleManager::ID_JALC_DOI);
        $uri_crossref = $this->repositoryHandleManager->createSelfDoiUri($item_id, $item_no, RepositoryHandleManager::ID_CROSS_REF_DOI);
        $uri_library_jalcdoi = $this->repositoryHandleManager->createSelfDoiUri($item_id, $item_no, RepositoryHandleManager::ID_LIBRARY_JALC_DOI);
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        $uri_datacite = $this->repositoryHandleManager->createSelfDoiUri($item_id, $item_no, RepositoryHandleManager::ID_DATACITE_DOI);
        if(strlen($uri_jalcdoi) > 0 && strlen($uri_crossref) < 1 && strlen($uri_library_jalcdoi) < 1 && strlen($uri_datacite) < 1)
        {
            $uri = $uri_jalcdoi;
        }
        else if(strlen($uri_crossref) > 0 && strlen($uri_jalcdoi) < 1 && strlen($uri_library_jalcdoi) < 1 && strlen($uri_datacite) < 1)
        {
            $uri = $uri_crossref;
        }
        else if(strlen($uri_library_jalcdoi) > 0 && strlen($uri_jalcdoi) < 1 && strlen($uri_crossref) < 1 && strlen($uri_datacite) < 1)
        {
            $uri = $uri_library_jalcdoi;
        }
        else if(strlen($uri_datacite) > 0 && strlen($uri_jalcdoi) < 1 && strlen($uri_crossref) < 1 && strlen($uri_library_jalcdoi) < 1)
        {
            $uri = $uri_datacite;
        }
        // Add DataCite 2015/02/10 K.Sugimoto --end--
        
        if(strlen($uri) > 0)
        {
            $query = "INSERT INTO ".DATABASE_PREFIX."repository_search_selfdoi ".
                     "(item_id, item_no, metadata) VALUES ".
                     "(?, ?, ?) ".
                     "ON DUPLICATE KEY UPDATE metadata=? ;";
            
            $params = array();
            $params[] = $item_id;
            $params[] = $item_no;
            $params[] = $uri;
            $params[] = $uri;
            $this->dbAccess->executeQuery($query, $params);
        } else {
            $query = "DELETE FROM ".DATABASE_PREFIX."repository_search_selfdoi ".
                     "WHERE item_id=? AND item_no=? ;";
            $params = array();
            $params[] = $item_id;
            $params[] = $item_no;
            $this->dbAccess->executeQuery($query, $params);
        }
    }
    /**
     * Get repositoryHandleManager object
     * ハンドル管理クラスのオブジェクトを取得する
     */
    private function getRepositoryHandleManager()
    {
        if(!isset($this->repositoryHandleManager)){
            if(!isset($this->TransStartDate) || strlen($this->TransStartDate) == 0)
            {
                $DATE = new Date();
                $this->TransStartDate = $DATE->getDate(). ".000";
            }
            
            $rhm = new RepositoryHandleManager($this->Session, $this->dbAccess, $this->TransStartDate);
            $this->repositoryHandleManager = $rhm;
        }
    }
    /**
     * add external search word
     * 外部検索キーワードを検索テーブルに追加する
     *
     * @param int $item_id item ID アイテムID
     * @param int $item_no item number アイテム通番
     * @param string $search_word external search keyword 外部検索キーワード
     */
    public function addExternalSearchWord($item_id, $item_no, $search_word) {
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --start--
        $toSearchKey = new ToSearchKey();
        $search_word = $this->convertSearchTableKeyword($search_word, "externalsearchword", $toSearchKey);
	    // Extend Search Keyword 2015/02/23 K.Sugimoto --end--
        $query = "INSERT INTO ". DATABASE_PREFIX. "repository_search_external_searchword ".
                 "(item_id, item_no, metadata) ".
                 "VALUES (?, ?, ?) ".
                 "ON DUPLICATE KEY UPDATE ".
                 "item_id=VALUES(`item_id`), ".
                 "item_no=VALUES(`item_no`), ".
                 "metadata=CONCAT(metadata, VALUES(`metadata`)) ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = ",".$search_word;
        $this->dbAccess->executeQuery($query, $params);
    }
    
    // Extend Search Keyword 2015/02/26 K.Sugimoto --start--
    /**
     * convert search table keyword
     * 検索テーブルに追加する文字列を変換する
     *
     * @param string $word word 文字列
     * @param string $mapping mapping マッピング情報
     * @param object $toSearchKey search key 検索キーオブジェクト
     * @return string converted word 変換済文字列
     */
    public function convertSearchTableKeyword($word, $mapping, $toSearchKey=null) {
        if(!isset($this->searchKeywordConverter)) {
        	$this->searchKeywordConverter = new Repository_Files_Plugin_Searchkeywordconverter_Twobytechartohalfsizechar();
        }
        
        if($mapping === self::LANGUAGE || $mapping === self::TEXTVERSION) {
        	return $word;
        }
        
        return $this->searchKeywordConverter->toSearchKey($word, $toSearchKey);
    }
    // Extend Search Keyword 2015/02/26 K.Sugimoto --end--
    
}

?>
