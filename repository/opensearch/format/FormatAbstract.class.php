<?php

/**
 * Opensearch common format base class
 * Opensearch共通形式基底クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: FormatAbstract.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * String format conversion common classes
 * 文字列形式変換共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryOutputFilter.class.php';
/**
 * Handle management common classes
 * ハンドル管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
/**
 * WEKO business factory class
 * WEKO用ビジネスファクトリークラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/WekoBusinessFactory.class.php';

/**
 * Opensearch common format base class
 * Opensearch共通形式基底クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Opensearch_FormatAbstract
{
    // new line
    /**
     * Line feed
     * 改行
     *
     * @var string
     */
    const LF = "\n";
    
    // get item data key
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_TITLE = "title";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_ALTERNATIVE = "alternative";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_URI = "uri";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_SWRC = "swrc";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_OAIORE = "oai-ore";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_WEKO_ID = "weko_id";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_MAPPING_INFO = "mapping_info";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_ITEM_TYPE_NAME = "item_type_name";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_MIME_TYPE = "mime_type";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_FILE_URI = "file_uri";
    // start mhaya
    const DATA_FILE_NAME = "file_name";
    // end mhaya

    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_CREATOR = "creator";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_PUBLISHER = "publisher";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_INDEX_PATH = "index_path";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_JTITLE = "jtitle";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_ISSN = "issn";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_VOLUME = "volume";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_ISSUE = "issue";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_SPAGE = "spage";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_EPAGE = "epage";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_URL = "url";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_DATE_OF_ISSUED = "date_of_issued";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_DESCRIPTION = "description";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_PUB_DATE = "pub_date";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_INS_DATE = "ins_date";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_MOD_DATE = "mod_date";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_WEKO_LOG_TERM = "log_term";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_WEKO_LOG_VIEW = "log_view";
    /**
     * Item key name
     * アイテムキー名
     *
     * @var string
     */
    const DATA_WEKO_LOG_DOWNLOAD = "log_download";
    
    // request parameter key for RepositorySearch
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_KEYWORD = "keyword";
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_INDEX_ID = "index_id";
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_WEKO_ID = "weko_id";
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_PAGE_NO = "page_no";
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_LIST_VIEW_NUM = "list_view_num";
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_SORT_ORDER = "sort_order";
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_SEARCH_TYPE = "search_type";
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_ANDOR = "andor";
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_FORMAT = "format";
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_ITEM_IDS = "item_ids";
    /**
     * Request parameter name
     * リクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_LANG = "lang";
    
    // request parameter key for Repository_Opensearch
    /**
     * Opensearch request parameter name
     * Opensearchリクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_LOG_TERM = "log_term";
    /**
     * Opensearch request parameter name
     * Opensearchリクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_DATA_FILTER = "dataFilter";
    /**
     * Opensearch request parameter name
     * Opensearchリクエストパラメータ名
     *
     * @var string
     */
    const REQUEST_PREFIX = "prefix";
    
    /**
     * Data filter name
     * データフィルタ名
     *
     * @var string
     */
    const DATA_FILTER_SIMPLE = "simple";
    
    // mapping language value
    /**
     * Creator language
     * 著者言語
     *
     * @var string
     */
    const DATA_CREATOR_LANG = "creator_lang";
    /**
     * Publisher language
     * 公開者言語
     *
     * @var string
     */
    const DATA_PUBLISHER_LANG = "publisher_lang";
    /**
     * Description language
     * 概要言語
     *
     * @var string
     */
    const DATA_DESCRIPTION_LANG = "description_lang";
    
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    protected $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
    protected $Db = null;
    
    /**
     * repository action class object
     * RepositoryActionオブジェクト
     *
     * @var RepositoryAction
     */
    protected $RepositoryAction = null;
    
    /**
     * search result total
     * 総件数
     *
     * @var int
     */
    private $total = 0;
    
    /**
     * start page 
     * 開始番号
     *
     * @var int
     */
    private $startIndex = 0;
    
    /**
     * RepositoryHandleManager Object
     * RepositoryHandleManagerオブジェクト
     * 
     * @var RepositoryHandleManager
     */
    protected $repositoryHandleManager = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $session Session management objects Session管理オブジェクト
     * @param Dbobject $db Database management objects データベース管理オブジェクト
     */
    public function __construct($session, $db)
    {
        if(isset($session) && $session!=null)
        {
            $this->Session = $session;
        }
        else
        {
            return null;
        }
        
        // set database object
        if(isset($db) && $db!=null)
        {
            $this->Db = $db;
        }
        else
        {
            return null;
        }
        
        // set Repository Action class
        $this->RepositoryAction = new RepositoryAction();
        $this->RepositoryAction->Session = $this->Session;
        $this->RepositoryAction->Db = $this->Db;
        
        // individual initialize
        $this->initialize();
    }
    
    /**
     * individual initialize
     * 初期化
     */
    private function initialize()
    {
    }
    
    /**
     * make XML for open search 
     * XML作成
     * 
     * @param array $request Request parameter リクエストパラメータ
     * @param int $total total hit num HIT件数
     * @param int sIdx start index num 開始番号
     * @param array $searchResult Search result 検索結果
     *                            array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr_type"][$ii]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return string Output string 出力文字列
     */
    public function outputXml($request, $total, $sIdx, $searchResult)
    {
        $this->total = $total;
        $this->startIndex = $sIdx;
        
        $xml = "";
        return $xml;
    }
    
    /**
     * get index pankuzu list
     * インデックス階層構造取得
     *
     * @param int $indexId Index id インデックスID
     * @param string $del delimiter 区切り文字
     * @param string $lang Language 言語
     * @return string Output string 出力文字列
     */
    protected function getIndexPath($indexId, $del="/", $lang=RepositoryConst::ITEM_ATTR_TYPE_LANG_JA)
    {
        // get parents index names
        $index_data = array();
        $this->RepositoryAction->getParentIndex($indexId, $index_data);
        $idx_names = "";
        for($ii=0; $ii<count($index_data); $ii++)
        {
            if($idx_names != "")
            {
                $idx_names .= " $del ";
            }
            if($lang == RepositoryConst::ITEM_ATTR_TYPE_LANG_JA)
            {
                if(strlen($index_data[$ii]["index_name"]) > 0)
                {
                    $idx_names .= $index_data[$ii]["index_name"];
                }
                else
                {
                    $idx_names .= $index_data[$ii]["index_name_english"];
                }
            }
            else
            {
                if(strlen($index_data[$ii]["index_name_english"]) > 0)
                {
                    $idx_names .= $index_data[$ii]["index_name_english"];
                }
                else
                {
                    $idx_names .= $index_data[$ii]["index_name"];
                }
            }
        }
        
        return $idx_names;
    }
    
    /**
     * Get output date
     * 出力データ取得
     *
     * @param string $request Request リクエスト
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @return array Item information アイテム情報
     *               array["title"|"alternative"|"url"|"swrc"|"oaiore"|"weko_id"|"mapping_info"|"mime_type"|"file_uri"....]
     */
    protected function getOutputData($request, $itemId, $itemNo)
    {
        $itemData = array(self::DATA_TITLE => "",
                        self::DATA_ALTERNATIVE => "",
                        self::DATA_URI => "",
                        self::DATA_SWRC => "",
                        self::DATA_OAIORE => array(),
                        self::DATA_WEKO_ID => "",
                        self::DATA_MAPPING_INFO => "",
                        self::DATA_ITEM_TYPE_NAME => "",
                        self::DATA_MIME_TYPE => array(),
                        self::DATA_FILE_URI => array(),
                        // start mhaya
                        self::DATA_FILE_NAME => array(),
                        // end mhaya
			self::DATA_URL => array(),
                        self::DATA_CREATOR => array(),
                        self::DATA_CREATOR_LANG => array(),
                        self::DATA_PUBLISHER => array(),
                        self::DATA_PUBLISHER_LANG => array(),
                        self::DATA_INDEX_PATH => array(),
                        self::DATA_JTITLE => "",
                        self::DATA_ISSN => "",
                        self::DATA_VOLUME => "",
                        self::DATA_ISSUE => "",
                        self::DATA_SPAGE => "",
                        self::DATA_EPAGE => "",
                        self::DATA_DATE_OF_ISSUED => "",
                        self::DATA_DESCRIPTION => array(),
                        self::DATA_DESCRIPTION_LANG => array(),
                        self::DATA_PUB_DATE => "",
                        self::DATA_INS_DATE => "",
                        self::DATA_MOD_DATE => "",
                        self::DATA_WEKO_LOG_TERM => "",
                        self::DATA_WEKO_LOG_VIEW => "",
                        self::DATA_WEKO_LOG_DOWNLOAD => "");
        
        $data = array();
        $errorMsg = "";
        $status = $this->RepositoryAction->getItemData($itemId, $itemNo, $data, $errorMsg, false, true);
        $status = $this->RepositoryAction->getItemIndexData($itemId, $itemNo, $data, $errorMsg);
        
        $item           = $data[RepositoryConst::ITEM_DATA_KEY_ITEM][0];
        $itemType       = $data[RepositoryConst::ITEM_DATA_KEY_ITEM_TYPE][0];
        $itemAttrType   = $data[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR_TYPE];
        $itemAttr       = $data[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR];
        $posIndex       = $data[RepositoryConst::ITEM_DATA_KEY_POSITION_INDEX];
        
        ///// setting title and alternative /////
        if($request[self::REQUEST_LANG] == RepositoryConst::ITEM_ATTR_TYPE_LANG_EN)
        {
            if(strlen($item[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH]) > 0)
            {
                $itemData[self::DATA_TITLE] = $item[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
                if(strlen($item[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE]) > 0)
                {
                    $itemData[self::DATA_ALTERNATIVE] = $item[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
                }
            }
            else
            {
                $itemData[self::DATA_TITLE] = $item[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
            }
        }
        else
        {
            if(strlen($item[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE]) > 0)
            {
                $itemData[self::DATA_TITLE] = $item[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
                if(strlen($item[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH]) > 0)
                {
                    $itemData[self::DATA_ALTERNATIVE] = $item[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
                }
            }
            else
            {
                $itemData[self::DATA_TITLE] = $item[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
            }
        }
        
        ///// setting uri /////
        //if(strlen($item[RepositoryConst::DBCOL_REPOSITORY_ITEM_URI]) > 0)
        //{
        //    $itemData[self::DATA_URI] = $item[RepositoryConst::DBCOL_REPOSITORY_ITEM_URI];
        //}
        
        ///// setting swrc /////
        $itemData[self::DATA_SWRC] = BASE_URL."/?action=repository_swrc&itemId=$itemId&itemNo=$itemNo";        
        ///// setting oai-ore /////
        // for item
        array_push($itemData[self::DATA_OAIORE], BASE_URL."/?action=repository_oaiore&itemId=$itemId&itemNo=$itemNo");
        for($ii=0; $ii<count($posIndex); $ii++)
        {
            // for position index
            array_push($itemData[self::DATA_OAIORE], BASE_URL."/?action=repository_oaiore&indexId=".$posIndex[$ii]["index_id"]);
            $path = $this->getIndexPath($posIndex[$ii]["index_id"]);
            array_push($itemData[self::DATA_INDEX_PATH], $path);
            $idx = strrpos($path, "/");
            if(is_null($idx))
            {
                $path = substr($path, 0, $idx);
                array_push($itemData[self::DATA_INDEX_PATH], $path);
            }
        }
        
        // suffix(weko id)
        $this->getRepositoryHandleManager();
        $suffix = $this->repositoryHandleManager->getSuffix($itemId, $itemNo, RepositoryHandleManager::ID_Y_HANDLE);
        
        $wekoId = $suffix;
        $itemData[self::DATA_WEKO_ID] = $wekoId;

        ///// setting item type id /////
        $itemData[self::DATA_ITEM_TYPE_NAME] = $itemType[RepositoryConst::DBCOL_REPOSITORY_ITEM_TYPE_NAME];
        
        ///// setting mapping info /////
        $itemData[self::DATA_MAPPING_INFO] = $itemType[RepositoryConst::DBCOL_REPOSITORY_ITEM_TYPE_MAPPING_INFO];
        
        ///// setting meatdata /////
        for($ii=0; $ii<count($itemAttrType); $ii++)
        {
            $inputType  = $itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IMPUT_TYPE];
            $mapping    = $itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_JUNII2_MAPPING];
            
            // Add data filter parameter Y.Nakao 2013/05/17 --start--
            // not output hidden metadata
            if($itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_HIDDEN] == 1)
            {
                continue;
            }
            
            if($request[self::REQUEST_DATA_FILTER] == self::DATA_FILTER_SIMPLE && $itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LIST_VIEW_ENABLE] == 0)
            {
                // when data fileter is "simple", output list_view_enable=1 metadata.
                continue;
            }
            // Add data filter parameter Y.Nakao 2013/05/17 --end--
            
            for($jj=0; $jj<count($itemAttr[$ii]); $jj++)
            {
                // set file information
                if ($inputType == RepositoryConst::ITEM_ATTR_TYPE_FILE) 
                {
                    // set file preview info
                    $extension = $itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_EXTENSION];
                    $mimeType = $itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_MIME_TYPE];
                    $filePrevName = $itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_PREV_NAME];
                    $isImage = preg_match("/^image/", $mimeType);
                    
                    if ((strtolower($extension) == "pdf" || $mimeType == "application/pdf" || $isImage == 1) && $filePrevName != "") {
                        $file = Array(
                            RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID => $itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID],
                            RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_NO => $itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_NO],
                            RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID => $itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID],
                            RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO => $itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO]
                        ); 
                        
                        $itemData[self::DATA_URL][] = $file;
                    }
                    
                    // set file info
                    if(strlen($itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_MIME_TYPE]) > 0)
                    {
                        array_push($itemData[self::DATA_MIME_TYPE], $itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_MIME_TYPE]);
                        $fileUri = BASE_URL."/?action=repository_uri".
                                   "&item_id=".$itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID].
                                   "&file_id=".$itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID].
                                   "&file_no=".$itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO];
                        array_push($itemData[self::DATA_FILE_URI], $fileUri);
                        // start ファイル名を取得 mhaya
                        array_push($itemData[self::DATA_FILE_NAME],$itemAttr[$ii][$jj][RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NAME]);
                        // end mhaya
                    }
                }
                
                /// check value
                $value = RepositoryOutputFilter::attributeValue($itemAttrType[$ii], $itemAttr[$ii][$jj], 2);
                if(strlen($value) == 0)
                {
                    continue;
                }
                
                if($mapping == RepositoryConst::JUNII2_CREATOR)
                {
                    array_push($itemData[self::DATA_CREATOR], $value);
                    array_push($itemData[self::DATA_CREATOR_LANG], $itemAttrType[$ii]["display_lang_type"]);
                }
                else if($mapping == RepositoryConst::JUNII2_PUBLISHER)
                {
                    array_push($itemData[self::DATA_PUBLISHER], $value);
                    array_push($itemData[self::DATA_PUBLISHER_LANG], $itemAttrType[$ii]["display_lang_type"]);
                }
                else if($inputType == RepositoryConst::ITEM_ATTR_TYPE_BIBLIOINFO)
                {
                    $biblio = explode("||", $value);
                    if(strlen($itemData[self::DATA_JTITLE]) == 0 && strlen($biblio[0]) > 0)
                    {
                        $itemData[self::DATA_JTITLE] = $biblio[0];
                    }
                    if(strlen($itemData[self::DATA_VOLUME]) == 0 && strlen($biblio[1]) > 0)
                    {
                        $itemData[self::DATA_VOLUME] = $biblio[1];
                    }
                    if(strlen($itemData[self::DATA_ISSUE]) == 0 && strlen($biblio[2]) > 0)
                    {
                        $itemData[self::DATA_ISSUE] = $biblio[2];
                    }
                    if(strlen($itemData[self::DATA_SPAGE]) == 0 && strlen($biblio[3]) > 0)
                    {
                        $itemData[self::DATA_SPAGE] = $biblio[3];
                    }
                    if(strlen($itemData[self::DATA_EPAGE]) == 0 && strlen($biblio[4]) > 0)
                    {
                        $itemData[self::DATA_EPAGE] = $biblio[4];
                    }
                    if(strlen($itemData[self::DATA_DATE_OF_ISSUED]) == 0 && strlen($biblio[5]) > 0)
                    {
                        $itemData[self::DATA_DATE_OF_ISSUED] = $biblio[5];
                    }
                }
                else if(strlen($itemData[self::DATA_JTITLE]) == 0 && $mapping == RepositoryConst::JUNII2_JTITLE)
                {
                    $itemData[self::DATA_JTITLE] = $value;
                }
                else if(strlen($itemData[self::DATA_VOLUME]) == 0  && $mapping == RepositoryConst::JUNII2_VOLUME)
                {
                    $itemData[self::DATA_VOLUME] = $value;
                }
                else if(strlen($itemData[self::DATA_ISSUE]) == 0 && $mapping == RepositoryConst::JUNII2_ISSUE)
                {
                    $itemData[self::DATA_ISSUE] = $value;
                }
                else if(strlen($itemData[self::DATA_SPAGE]) == 0 && $mapping == RepositoryConst::JUNII2_SPAGE)
                {
                    $itemData[self::DATA_SPAGE] = $value;
                }
                else if(strlen($itemData[self::DATA_EPAGE]) == 0 && $mapping == RepositoryConst::JUNII2_EPAGE)
                {
                    $itemData[self::DATA_EPAGE] = $value;
                }
                else if(strlen($itemData[self::DATA_DATE_OF_ISSUED]) == 0 && $mapping == RepositoryConst::JUNII2_DATE_OF_ISSUED)
                {
                    $itemData[self::DATA_DATE_OF_ISSUED] = $value;
                }
                else if(strlen($itemData[self::DATA_ISSN]) == 0 && $mapping == RepositoryConst::JUNII2_ISSN)
                {
                    $itemData[self::DATA_ISSN] = $value;
                }
                else if($mapping == RepositoryConst::JUNII2_DESCRIPTION)
                {
                    array_push($itemData[self::DATA_DESCRIPTION], $value);
                    array_push($itemData[self::DATA_DESCRIPTION_LANG], $itemAttrType[$ii]["display_lang_type"]);
                }
                // Add link rel="enclosure" element 2015/11/30 mhaya start
                else if($mapping == RepositoryConst::JUNII2_FULL_TEXT_URL)
                {
                    if(array_search($value,$itemData)===FALSE){
                        if(preg_match('/.*\/((.+)\.(.+))$/',$value,$match)){
                            if(preg_match('/jpg$/i',$match[3]))
                            {
                                array_push($itemData[self::DATA_FILE_URI],$value);
                                array_push($itemData[self::DATA_FILE_NAME],basename($value));
                                array_push($itemData[self::DATA_MIME_TYPE],"image/jpeg");
                            }
                            else if(preg_match('/png$/i',$match[3]))
                            {
                                array_push($itemData[self::DATA_FILE_URI],$value);
                                array_push($itemData[self::DATA_FILE_NAME],basename($value));
                                array_push($itemData[self::DATA_MIME_TYPE],"image/png");
                            }
                            else if(preg_match('/[tiff|tif]$/i',$match[3]))
                            {
                                array_push($itemData[self::DATA_FILE_URI],$value);
                                array_push($itemData[self::DATA_FILE_NAME],basename($value));
                                array_push($itemData[self::DATA_MIME_TYPE],"image/tiff");
                            }
                            else if(preg_match('/bmp$/i',$match[3]))
                            {
                                array_push($itemData[self::DATA_FILE_URI],$value);
                                array_push($itemData[self::DATA_FILE_NAME],basename($value));
                                array_push($itemData[self::DATA_MIME_TYPE],"image/bmp");
                            }
                        }
                    }
                }
                // Add link rel="enclosure" element 2015/11/30 mhaya end

            }
        }

        // Add pubdate 2014/08/01 Y.Nakao --start--
        ///// setting pub_date /////
        $itemData[self::DATA_PUB_DATE] = $item[RepositoryConst::DBCOL_REPOSITORY_ITEM_SHOWN_DATE];
        // Add pubdate 2014/08/01 Y.Nakao --end--
        
        ///// setting ins_date and mod_date /////
        $itemData[self::DATA_INS_DATE] = $item[RepositoryConst::DBCOL_COMMON_INS_DATE];
        $itemData[self::DATA_MOD_DATE] = $item[RepositoryConst::DBCOL_COMMON_MOD_DATE];
        
        return $itemData;
    }
    
    /**
     * Get the browsing information and the number of downloads from the item log
     * アイテムログから閲覧情報とダウンロード回数を取得
     *
     * @param array $request Request parameter リクエストパラメータ
     * @param array $searchResult Search result 検索結果
     *                            array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr_type"][$ii]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    protected function getItemLogData($request, &$item_data)
    {
        $terms = explode("-", $request[self::REQUEST_LOG_TERM]);
        if(count($terms) == 2 && (int)$terms[0] != 0 && (int)$terms[1] != 0){
            $year = $terms[0];
            $month = $terms[1];
            
            // ビジネスクラスを呼ぶ
            $transStartDate = $this->getNowDate();
            WekoBusinessFactory::initialize($this->Session, $this->Db, $transStartDate);
            $usagestatistics = BusinessFactory::getFactory()->getBusiness("businessUsagestatistics");
            
            for($ii=0; $ii<count($item_data); $ii++){
                if($item_data[$ii]['item_id'] != "" && $item_data[$ii]['item_no'] != ""){
                    $item_data[$ii][self::DATA_WEKO_LOG_TERM] = $request[self::REQUEST_LOG_TERM];
                    
                    // 閲覧回数取得
                    $usageViews = $usagestatistics->getUsagesViews($item_data[$ii]['item_id'], $item_data[$ii]['item_no'], $year, $month);
                    $item_data[$ii][self::DATA_WEKO_LOG_VIEW] = (string)$usageViews["total"];
                    
                    // ダウンロード回数取得
                    $usagesDownloads = $usagestatistics->getUsagesdownloads($item_data[$ii]['item_id'], $item_data[$ii]['item_no'], $year, $month);
                    $totalDownloadNum = 0;
                    for($cnt = 0; $cnt < count($usagesDownloads); $cnt++)
                    {
                        $totalDownloadNum += $usagesDownloads[$cnt]["usagestatistics"]["total"];
                    }
                    $item_data[$ii][self::DATA_WEKO_LOG_DOWNLOAD] = (string)$totalDownloadNum;
                }
            }
        }
    }
    
    /**
     * Get now date
     * 現在日時取得
     * 
     * @return string Now date 現在日時
     */
    protected function getNowDate()
    {
        $DATE = new Date();
        return $DATE->getDate().".000";
    }
    
    /**
     * Set RepositoryHandleManager object
     * RepositoryHandleManagerオブジェクト設定
     *
     * @return RepositoryHandleManager Handle management common classes ハンドル管理共通クラス
     */
    protected function getRepositoryHandleManager()
    {
        if(!isset($this->repositoryHandleManager))
        {
            if(!isset($this->RepositoryAction->dbAccess))
            {
                $this->RepositoryAction->dbAccess = new RepositoryDbAccess($this->Db);
            }
            if(!isset($this->RepositoryAction->TransStartDate))
            {
                $DATE = new Date();
                $this->RepositoryAction->TransStartDate = $DATE->getDate(). ".000";
            }
            $this->repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->RepositoryAction->dbAccess, $this->RepositoryAction->TransStartDate);
        }
    }
    
    /**
     * Get other language display setting
     * 他言語取得
     * 
     * @return array Other language information 他言語情報
     *               array["japanese"|"english"]
     */
    protected function getAlternativeLanguage()
    {
        $query = "SELECT param_value FROM ". DATABASE_PREFIX. "repository_parameter ".
                 "WHERE param_name = ? ;";
        $params = array();
        $params[] = "alternative_language";
        $result = $this->Db->execute($query, $params);
        
        $lang_display_params = array();
        $language = explode(",", $result[0]["param_value"]);
        $japanese = explode(":", $language[0]);
        $english = explode(":", $language[1]);
        $lang_display_params["japanese"] = $japanese[1];
        $lang_display_params["english"] = $english[1];
        
        return $lang_display_params;
    }
    
    /**
     * Load size of file preview image
     * ファイルのプレビュー画像のサイズを取得する
     *
     * @param int $item_id Item ID
     *                     アイテムID
     * @param int $item_no Item No
     *                     アイテム通番
     * @param int $attribute_id Item attribute ID
     *                          アイテム属性ID
     * @param int $file_no File No
     *                     ファイル通番
     *
     * @return int Size of file preview image
     *             ファイルのプレビュー画像のサイズ
     */
    protected function loadFilePreviewSize($item_id, $item_no, $attribute_id, $file_no)
    {
        $query = "SELECT length(file_prev) AS size FROM ". DATABASE_PREFIX. "repository_file ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND attribute_id = ? ".
                 "AND file_no = ?;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $attribute_id;
        $params[] = $file_no;
        $result = $this->Db->execute($query, $params);
        if($result == false || count($result) == 0)
        {
            return 0;
        }
        
        return $result[0]["size"];
    }
}

?>
