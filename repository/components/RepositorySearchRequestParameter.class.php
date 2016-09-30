<?php

/**
 * Search request parameter process class
 * 検索リクエストパラメータ処理クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositorySearchRequestParameter.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR.'/modules/repository/components/RepositoryAction.class.php';
/**
 * Const for WEKO class
 * WEKO用定数クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/RepositoryConst.class.php';
/**
 * Output filter class
 * 出力フィルタリングクラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/RepositoryOutputFilter.class.php';
/**
 * DB connect class
 * DB接続クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/RepositoryDbAccess.class.php';

/**
 * Search request parameter process class
 * 検索リクエストパラメータ処理クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositorySearchRequestParameter
{
    /**
     * Format description
     * 抄録フォーマット
     */
    const FORMAT_DESCRIPTION = "description";
    /**
     * Format RSS
     * RSSフォーマット
     */
    const FORMAT_RSS = "rss";
    /**
     * Format ATOM
     * ATOMフォーマット
     */
    const FORMAT_ATOM = "atom";
    /**
     * Format OAI-DC
     * OAI-DCフォーマット
     */
    const FORMAT_DUBLIN_CORE = "oai_dc";
    /**
     * Format JuNii2
     * JuNii2フォーマット
     */
    const FORMAT_JUNII2 = "junii2";
    /**
     * Format OAI-LOM
     * OAI-LOMフォーマット
     */
    const FORMAT_LOM = "oai_lom";

    /**
     * title ASC
     * タイトル昇順
     */
    const ORDER_TITLE_ASC           =  1;
    /**
     * title DESC
     * タイトル降順
     */
    const ORDER_TITLE_DESC          =  2;
    /**
     * Insert user ID ASC
     * 登録ユーザーID昇順
     */
    const ORDER_INS_USER_ASC        =  3;
    /**
     * Insert user ID DESC
     * 登録ユーザーID降順
     */
    const ORDER_INS_USER_DESC       =  4;
    /**
     * Item type ID ASC
     * アイテムタイプID昇順
     */
    const ORDER_ITEM_TYPE_ID_ASC    =  5;
    /**
     * Item type ID DESC
     * アイテムタイプID降順
     */
    const ORDER_ITEM_TYPE_ID_DESC   =  6;
    /**
     * WEKO ID ASC
     * WEKO ID昇順
     */
    const ORDER_WEKO_ID_ASC         =  7;
    /**
     * WEKO ID DESC
     * WEKO ID降順
     */
    const ORDER_WEKO_ID_DESC        =  8;
    /**
     * Mod date ASC
     * 更新日時昇順
     */
    const ORDER_MOD_DATE_ASC        =  9;
    /**
     * Mod date DESC
     * 更新日時降順
     */
    const ORDER_MOD_DATE_DESC       = 10;
    /**
     * Insert date ASC
     * 登録日時昇順
     */
    const ORDER_INS_DATE_ASC        = 11;
    /**
     * Insert date DESC
     * 登録日時降順
     */
    const ORDER_INS_DATE_DESC       = 12;
    /**
     * Review date ASC
     * 査読日時昇順
     */
    const ORDER_REVIEW_DATE_ASC     = 13;
    /**
     * Review date DESC
     * 査読日時降順
     */
    const ORDER_REVIEW_DATE_DESC    = 14;
    /**
     * Date of issued ASC
     * 発行年月日昇順
     */
    const ORDER_DATEOFISSUED_ASC    = 15;
    /**
     * Date of issued DESC
     * 発行年月日昇降順
     */
    const ORDER_DATEOFISSUED_DESC   = 16;
    /**
     * Custom sort order ASC
     * カスタムソート順序昇順
     */
    const ORDER_CUSTOM_SORT_ASC     = 17;
    /**
     * Custom sort order DESC
     * カスタムソート順序降順
     */
    const ORDER_CUSTOM_SORT_DESC    = 18;
        
    // request parameter
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_META = "meta";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_ALL = "all";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_TITLE = "title";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_CREATOR = "creator";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_KEYWORD = "kw";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_SUBJECT_LIST = "scList";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_SUBJECT_DESC = "scDes";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_DESCRIPTION = "des";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_PUBLISHER = "pub";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_CONTRIBUTOR = "con";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_DATE = "date";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_ITEMTYPE_LIST = "itemTypeList";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_TYPE_LIST = "typeList";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_FORMAT = "form";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_ID_LIST = "idList";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_ID_DESC = "idDes";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_JTITLE = "jtitle";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_PUBYEAR_FROM = "pubYearFrom";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_PUBYEAR_UNTIL = "pubYearUntil";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_LANGUAGE = "ln";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_AREA = "sp";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_ERA = "era";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_RIGHT_LIST = "riList";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_RITHT_DESC = "riDes";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_TEXTVERSION = "textver";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_GRANTID = "grantid";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_GRANTDATE_FROM = "grantDateFrom";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_GRANTDATE_UNTIL = "grantDateUntil";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_DEGREENAME = "degreename";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_GRANTOR = "grantor";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_IDX = "idx";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_SHOWORDER = "order";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_COUNT = "count";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_PAGENO = "pn";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_LIST_RECORDS = "listRecords";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_OUTPUT_TYPE = "format";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_INDEX_ID = "index_id";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_PAGE_ID = "page_id";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_BLOCK_ID = "block_id";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_WEKO_ID = "weko_id";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_ITEM_IDS = "item_ids";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_DISPLAY_LANG = "lang";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_SEARCH_TYPE = "st";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_MODULE_ID = "module_id";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_HEADER = "_header";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_OLD_SEARCH_TYPE = "search_type";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_OLD_KEYWORD = "keyword";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_OLD_PAGENO = "page_no";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_OLD_COUNT = "list_view_num";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_OLD_SHOWORDER = "sort_order";
    // Add OpenSearch WekoId K.Matsuo 2014/04/04 --start--
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_PUBDATE_FROM = "pubDateFrom";
    /**
     * Search request parameter key
     * 検索リクエストパラメータキー
     */
    const REQUEST_PUBDATE_UNTIL = "pubDateUntil";
    // Add OpenSearch WekoId K.Matsuo 2014/04/04 --end--
    /**
     * Key of request parameter for WEKO author id
     * WEKO著者ID用のリクエストパラメータのキー
     *
     * @var string
     */
    const REQUEST_WEKO_AUTHOR_ID = "wekoAuthorId";
    
    /***** components *****/
    /**
     * Container object
     * コンテナオブジェクト
     *
     * @var DIContainer
     */
    public $_container = null;
    /**
     * Request object
     * リクエスト処理オブジェクト
     *
     * @var Request
     */
    public $_request = null;
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    public $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var RepositoryDbAccess
     */
    public $dbAccess = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObjectAdodb
     */
    public $Db = null;
    /**
     * Process start date
     * 処理開始時間
     * 
     * @var string
     */
    public $TransStartDate = null;
    
    /**
     * RepositoryAction class
     * RepositoryActionオブジェクト
     *
     * @var RepositoryAction
     */
    public $RepositoryAction = null;
    
    /***** search key *****/
    /**
     * search keyword
     * 検索キーワード
     *
     * @var string
     */
    public $keyword = null;
    /**
     * search index
     * インデックスID
     *
     * @var string (int is exclude 0)
     */
    public $index_id = null;
    
    /***** view status *****/
    /**
     * page number
     * ページ番号
     *
     * @var int
     */
    public $page_no = null;
    /**
     * list view number
     * 1ページに表示するアイテム数
     *
     * @var int
     */
    public $list_view_num = null;
    /**
     * sort order
     * 表示順序
     *
     * @var int
     */
    public $sort_order = null;
    /**
     * output format
     * 出力フォーマット
     *
     * @var string
     */
    public $format = null;
    /**
     * language
     * 言語
     *
     * @var string
     */
    public $lang = null;
    /**
     * when this parameter is 'all', output all search result.
     * リストレコード
     * 
     * @var string
     */
    public $listResords = "";
    
    
    /**
     * search request parameter.
     * 検索キーワード
     * 
     * @var array
     */
    public $search_term = array();
    
    /**
     * search request parameter.
     * 検索タイプ
     * 
     * @var array
     */
    public $search_type = null;
    
    /**
     * search request parameter.
     * 全文検索タイプ
     * 
     * @var string
     */
    public $all_search_type = null;
    
    /**
     * construct
     * コンストラクタ
     */
    public function __construct()
    {
        $this->_container =& DIContainerFactory::getContainer();
        $this->_request = $_GET;
        
        $this->RepositoryAction = new RepositoryAction();
        $_db =& $this->_container->getComponent("DbObject");
        if($_db == null)
        {
            return null;
        }
        $this->Db = $_db;
        $this->dbAccess = new RepositoryDbAccess($_db);
        $this->RepositoryAction->Db = $_db;
        $this->RepositoryAction->dbAccess = $this->dbAccess;
        
        $_session =& $this->_container->getComponent("Session");
        if($_session == null)
        {
            return null;
        }
        $this->Session = $_session;
        $this->RepositoryAction->Session = $_session;
        
        $DATE = new Date();
        $this->TransStartDate = $DATE->getDate().".000";
        
        $this->RepositoryAction->TransStartDate = $this->TransStartDate;
        $this->RepositoryAction->setConfigAuthority();
        
        $this->setRequestParameter();
    }
    
    /**
     * set request parameter
     * リファラからリクエストパラメータ(検索条件)を設定する
     */
    public function setRequestParameterFromReferrer()
    {
        $this->search_term = array();
        $this->sort_order = null;
        $this->page_no = null;
        $this->list_view_num = null;
        $this->lang = null;
        $this->listResords = null;
        $this->search_type = null;
        $this->format = null;
        $this->index_id = null;
        if(isset($_SERVER["HTTP_REFERER"]))
        {
        	parse_str($_SERVER["HTTP_REFERER"],$refererRequest);
        }
        else
        {
        	$refererRequest = array();
        }
        $this->_request = $refererRequest;
        $this->setRequestParameter();
    }

    /**
     * set request parameter
     * $this->_requestコンポーネントからリクエストパラメータ(検索条件)を設定する
     */
    public function setRequestParameter()
    {
        // ソート条件、検索範囲
        if(isset($this->_request[self::REQUEST_SHOWORDER]))
        { 
            $this->sort_order       = $this->_request[self::REQUEST_SHOWORDER];
        }
        
        // 表示条件
        if(isset($this->_request[self::REQUEST_PAGENO]))
        {
            $this->page_no          = $this->_request[self::REQUEST_PAGENO];
        }
        
        if(isset($this->_request[self::REQUEST_COUNT]))
        {
            $this->list_view_num    = $this->_request[self::REQUEST_COUNT];
        }
        
        if(isset($this->_request[self::REQUEST_DISPLAY_LANG]))
        {
            $this->lang             = $this->_request[self::REQUEST_DISPLAY_LANG];
        }
        
        if(isset($this->_request[self::REQUEST_LIST_RECORDS]))
        {
            $this->listResords      = $this->_request[self::REQUEST_LIST_RECORDS];
        }
        
        if(isset($this->_request[self::REQUEST_SEARCH_TYPE])){
            $this->search_type      = $this->_request[self::REQUEST_SEARCH_TYPE];
        }
        // 出力形式
        if(isset($this->_request[self::REQUEST_OUTPUT_TYPE]))
        {
            $this->format           = $this->_request[self::REQUEST_OUTPUT_TYPE];
        }
        
        $this->index_id = "";
        if(isset($this->_request[self::REQUEST_INDEX_ID]))
        {
            $this->index_id         = $this->_request[self::REQUEST_INDEX_ID];
        }
        // Fix When set search parameter weko_id, other search parameter are invalidity. 2014/05/08 Y.nakao --start--
        // 検索条件に「weko_id」がある場合、weko_id以外のパラメータは無効とする。
        $detailSearchFlag = false;
        if(isset($this->_request[self::REQUEST_WEKO_ID]))
        {
            $this->search_term[self::REQUEST_WEKO_ID] = $this->_request[self::REQUEST_WEKO_ID];;
        }
        // Add suppleContentsEntry Y.Yamazawa --start-- 2015/03/20 --start--
        else if(isset($this->_request[self::REQUEST_ITEM_IDS]))
        {
            $this->search_term[self::REQUEST_ITEM_IDS] = $this->_request[self::REQUEST_ITEM_IDS];
        }
        // Add suppleContentsEntry Y.Yamazawa --end-- 2015/03/20 --end--
        else
        {
        foreach($this->_request as $requestParam => $requestValue){
            if($requestParam == self::REQUEST_META || $requestParam == self::REQUEST_ALL
                || $requestParam == self::REQUEST_TITLE || $requestParam == self::REQUEST_CREATOR
                || $requestParam == self::REQUEST_KEYWORD || $requestParam == self::REQUEST_SUBJECT_LIST
                || $requestParam == self::REQUEST_SUBJECT_DESC || $requestParam == self::REQUEST_DESCRIPTION
                || $requestParam == self::REQUEST_PUBLISHER || $requestParam == self::REQUEST_CONTRIBUTOR
                || $requestParam == self::REQUEST_DATE || $requestParam == self::REQUEST_ITEMTYPE_LIST
                || $requestParam == self::REQUEST_TYPE_LIST
                || $requestParam == self::REQUEST_FORMAT || $requestParam == self::REQUEST_ID_LIST
                || $requestParam == self::REQUEST_ID_DESC || $requestParam == self::REQUEST_JTITLE
                || $requestParam == self::REQUEST_PUBYEAR_FROM || $requestParam == self::REQUEST_PUBYEAR_UNTIL
                || $requestParam == self::REQUEST_LANGUAGE || $requestParam == self::REQUEST_AREA
                || $requestParam == self::REQUEST_ERA || $requestParam == self::REQUEST_RIGHT_LIST
                || $requestParam == self::REQUEST_RITHT_DESC || $requestParam == self::REQUEST_TEXTVERSION
                || $requestParam == self::REQUEST_GRANTID || $requestParam == self::REQUEST_GRANTDATE_FROM
                || $requestParam == self::REQUEST_GRANTDATE_UNTIL || $requestParam == self::REQUEST_DEGREENAME
                || $requestParam == self::REQUEST_GRANTOR || $requestParam == self::REQUEST_IDX
                || $requestParam == self::REQUEST_PUBDATE_FROM || $requestParam == self::REQUEST_PUBDATE_UNTIL
                || $requestParam == self::REQUEST_WEKO_ID || $requestParam == self::REQUEST_WEKO_AUTHOR_ID)
            {
                if(!isset($requestValue) || strlen($requestValue) == 0){
                    continue;
                }
                $this->search_term[$requestParam] = $requestValue;
                $detailSearchFlag = true;
            }
        }
        }
        // Fix When set search parameter weko_id, other search parameter are invalidity. 2014/05/08 Y.nakao --end--
        
        // Fix subject, id search 2013.12.16 Y.Nakao --start--
        // チェック＋自由記述形式の場合、チェックなし=全チェックと同じ扱いにする
        if(!isset($this->search_term[self::REQUEST_SUBJECT_LIST]) && isset($this->search_term[self::REQUEST_SUBJECT_DESC]))
        {
            $this->search_term[self::REQUEST_SUBJECT_LIST] = "";
            for($ii=1; $ii<11; $ii++)
            {
                // set 1-10
                if($ii>1)
                {
                    $this->search_term[self::REQUEST_SUBJECT_LIST] .= ",";
                }
                $this->search_term[self::REQUEST_SUBJECT_LIST] .= $ii;
            }
        }
        
        if(!isset($this->search_term[self::REQUEST_ID_LIST]) && isset($this->search_term[self::REQUEST_ID_DESC]))
        {
            $this->search_term[self::REQUEST_ID_LIST] = "";
            for($ii=1; $ii<12; $ii++)
            {
                // set 1-11
                if($ii>1)
                {
                    $this->search_term[self::REQUEST_ID_LIST] .= ",";
                }
                $this->search_term[self::REQUEST_ID_LIST] .= $ii;
            }
        }
        // Fix subject, id search 2013.12.16 Y.Nakao --end--
        
        // Comment 2014/-8/25 Y.Nakao --start--
        // $detailSearchFlagは下記の理由で利用されているため削除しないでください
        //  * NC2は「***_id」という変数を自動的にintでキャストします
        //  * このため、TOPページ表示時などで「index_id=""」のリクエストが「index_id=0」となる場合があります
        //  * 本現象の対処として、index_id以外の検索条件がない場合はindex_idを無効化する措置を
        //  * $detailSearchFlagで行っています
        // Comment 2014/-8/25 Y.Nakao --end--
        if($detailSearchFlag && strlen($this->index_id) > 0){
            if(isset($this->search_term[self::REQUEST_IDX]) && strlen($this->search_term[self::REQUEST_IDX]) > 0){
                $this->search_term[self::REQUEST_IDX] .= ",".$this->index_id;
            } else {
                $this->search_term[self::REQUEST_IDX] = $this->index_id;
            }
            $this->index_id = "";
        }
        // validate
        $this->validate();
        
        $this->setActionParameter();
    }
    
    /**
     * request query
     * リクエストクエリを取得する
     *
     * @return string request query リクエストクエリ
     */
    public function getRequestQuery()
    {
        $req = array();
        if(strlen($this->index_id) > 0)
        {
            array_push($req, self::REQUEST_INDEX_ID."=".$this->index_id);
        }
        
        if(strlen($this->page_no) > 0)
        {
            array_push($req, self::REQUEST_PAGENO."=".$this->page_no);
        }
        
        if(strlen($this->list_view_num) > 0)
        {
            array_push($req, self::REQUEST_COUNT."=".$this->list_view_num);
        }
        
        if(strlen($this->sort_order) > 0)
        {
            array_push($req, self::REQUEST_SHOWORDER."=".$this->sort_order);
        }
        
        if(strlen($this->format) > 0)
        {
            array_push($req, self::REQUEST_OUTPUT_TYPE."=".$this->format);
        }
        
        if(strlen($this->lang) > 0)
        {
            array_push($req, self::REQUEST_DISPLAY_LANG."=".$this->lang);
        }
        
        if(strlen($this->listResords) > 0)
        {
            array_push($req, self::REQUEST_LIST_RECORDS."=".$this->listResords);
        }
        
        foreach($this->search_term as $requestParam => $requestValue)
        {
            array_push($req, $requestParam."=".urlencode($requestValue));
        }
        return implode("&", $req);
    }
    
    /**
     * get request parameter
     * リクエストパラメータを取得する
     *
     * @return array request parameters リクエストパラメータ配列
     *                array["index_is"|"page_no"|"count"|"show_order"|"output_type"|"language"|"listRecords"|"search_type"]
     */
    public function getRequestParameter()
    {
        $req = $this->search_term;
        $req[self::REQUEST_INDEX_ID]        = $this->index_id;
        $req[self::REQUEST_PAGENO]         = $this->page_no;
        $req[self::REQUEST_COUNT]   = $this->list_view_num;
        $req[self::REQUEST_SHOWORDER]      = $this->sort_order;
        $req[self::REQUEST_OUTPUT_TYPE]          = $this->format;
        $req[self::REQUEST_DISPLAY_LANG]            = $this->lang;
        $req[self::REQUEST_LIST_RECORDS]     = $this->listResords;
        if($this->search_type != null){
            $req[self::REQUEST_SEARCH_TYPE]     = $this->search_type;
        }
        return $req;
    }
    
    /**
     * get request parameter array
     * リクエストパラメータ配列を取得する
     *
     * @return array request parameter array リクエストパラメータ配列
     *                array[$ii]["param"|"value"]
     */
    public function getRequestParameterList()
    {
        $req = array();
        foreach($this->search_term as $requestParam => $requestValue)
        {
            $param = array();
            $param["param"]=$requestParam;
            $param["value"]=rawurlencode($requestValue);
            array_push($req, $param);
        }
        if($this->index_id != null){
            $param = array();
            $param["param"]="index_id";
            $param["value"]=$this->index_id;
            array_push($req, $param);
        }
        return $req;
    }
    
    /**
     * validate page no for over max page no
     * ページ番号が最大ページ数を超えていないかバリデートする
     *
     * @param int $maxPageNo max page number 最大ページ数
     */
    public function validatePageNo($maxPageNo)
    {
        if($maxPageNo < $this->page_no)
        {
            $this->page_no = $maxPageNo;
        }
    }
    
    /**
     * validate sort order
     * 表示順序をバリデートする
     *
     * @param array $searchTerm search terms 検索キーワード
     *                           array[$searchKey]
     * @param int $indexId index ID インデックスID
     * @param int $sortOrder sort order 表示順序
     * @return int sort order バリデータされた表示順序
     */
    public function validateSortOrder($searchTerm, $indexId, $sortOrder)
    {
        // validate int
        $sortOrder = intval($sortOrder);
        
        // 表示可能なソート条件のみ指定可能 /out of display sort order
        $this->RepositoryAction->getAdminParam("sort_disp", $order, $errorMsg);
        $availableSortOrder = explode("|", $order);
        if(!is_numeric(array_search($sortOrder, $availableSortOrder)))
        {
            $sortOrder = 0;
        }
        
        // インデックス特化ソート / when sort_order is custom sort order, index_id indispensable.
        if(strlen($indexId) == 0)
        {
            if($sortOrder == self::ORDER_CUSTOM_SORT_ASC || $sortOrder == self::ORDER_CUSTOM_SORT_DESC)
            {
                $sortOrder = 0;
            }
        }
        
        if($sortOrder > 0)
        {
            return $sortOrder;
        }
        
        $this->RepositoryAction->getAdminParam("sort_disp_default", $order, $errorMsg);
        $orderArray = explode("|", $order, 2);
        // first keyword search
        if(isset($orderArray[1]) && count($searchTerm) > 0)
        {
            $sortOrder = $orderArray[1];
        }
        else if(isset($orderArray[0]) && strlen($indexId) > 0)
        {
            $sortOrder = $orderArray[0];
        }
        else 
        {
            $sortOrder = self::ORDER_WEKO_ID_ASC;
        }
        
        return $sortOrder;
    }
    
    /**
     * Check search type "index" or "keyword"
     * インデックス検索かキーワード検索か判定する
     *
     * @return string search type 検索タイプ
     */
    public function getSearchType(){
        $type = '';
        
        //TODO:phase13.0の分岐はここに追加
        //if(インデックス&&アイテムタイプ && Junii2マッピング && ){retun 'detail';}
        
        //インデックス検索
        if(strlen($this->index_id) > 0){
            $type = 'index';
        }
        if(count($this->search_term) > 0){
            $type = 'keyword';
        }
        
        return $type;
    }
    
    /**
     * validate request parameter
     * リクエストパラメータを精査し、範囲外の場合は適切な値を代入する
     */
    private function validate()
    {
        $this->validateBackwordCompatible();
        
        ///// search keys  検索条件 /////
        foreach($this->search_term as $requestParam => $requestValue){
            if($requestParam == self::REQUEST_WEKO_ID){
                $tmpValue = RepositoryOutputFilter::string($requestValue);
                if($tmpValue != 0){
                    if(strlen($tmpValue) <= 8){
                        $this->search_term[$requestParam] = sprintf("%08d", $tmpValue);
                    } else {
                        // error
                        $this->search_term[$requestParam] = 0;
                    }
                }
            }
            else if($requestParam == self::REQUEST_SUBJECT_LIST || $requestParam == self::REQUEST_ITEMTYPE_LIST
                       || $requestParam == self::REQUEST_TYPE_LIST || $requestParam == self::REQUEST_ID_LIST
                       || $requestParam == self::REQUEST_IDX
                       || $requestParam == self::REQUEST_ITEM_IDS){// Add suppleContentsEntry Y.Yamazawa 2015/03/20
                $tmpValue = RepositoryOutputFilter::string($requestValue);
                $divideValue = explode(",", $tmpValue);
                $tmpValue = "";
                $count = 0;
                for($ii = 0; $ii < count($divideValue); $ii++){
                    if(strlen($divideValue[$ii]) == 0){
                        continue;
                    }
                    $divideValue[$ii] = (string)intval($divideValue[$ii]);
                    if($count > 0){
                        $tmpValue .= ",".$divideValue[$ii];
                    } else {
                        $tmpValue = $divideValue[$ii];
                    }
                    $count++;
                }
                $this->search_term[$requestParam] = $tmpValue;
            } else if($requestParam == self::REQUEST_TEXTVERSION || $requestParam == self::REQUEST_LANGUAGE
                   || $requestParam == self::REQUEST_RIGHT_LIST){
                $tmpValue = $requestValue;
                $tmpValue = trim(mb_convert_encoding($tmpValue, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS"));
                $tmpValue = RepositoryOutputFilter::string($tmpValue);
                $this->search_term[$requestParam] = trim(preg_replace("/[\s,]+|　/", ",", $tmpValue));
            } else {
                $tmpValue = $requestValue;
                $tmpValue = trim(mb_convert_encoding($tmpValue, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS"));
                $tmpValue = RepositoryOutputFilter::string($tmpValue);
                $this->search_term[$requestParam] = trim(preg_replace("/[\s]+|　|\+/", " ", $tmpValue));
            }
            
            // Bug fix WEKO-2014-012 T.Koyasu 2014/06/10 --start--
            $this->validateList($requestParam);
            // Bug fix WEKO-2014-012 T.Koyasu 2014/06/10 --end--
        }
        
        $this->index_id = RepositoryOutputFilter::string($this->index_id);
        // $this->index_idの指定がある または 詳細検索条件がない かつ $this->index_id==nullの場合
        if(strlen($this->index_id) > 0 || (count($this->search_term) == 0 && strlen($this->index_id) == 0))
        {
            $this->index_id = (string)intval($this->index_id);
        }
        
        ///// view keys  表示条件 /////
        
        $this->page_no = RepositoryOutputFilter::string($this->page_no);
        if(strlen($this->page_no) == 0)
        {
            $this->page_no = 1;
        }
        else if(!is_numeric($this->page_no))
        {
            $this->page_no = 1;
        }
        else
        {
            $this->page_no = intval($this->page_no);
            if($this->page_no < 1)
            {
                $this->page_no = 1;
            }
        }
        
        $this->list_view_num = RepositoryOutputFilter::string($this->list_view_num);
        $this->list_view_num = intval($this->list_view_num);
        
        if(strlen($this->list_view_num) == 0 || $this->list_view_num == 0)
        {
            $this->RepositoryAction->getAdminParam("default_list_view_num", $this->list_view_num, $errorMsg);
        }
        else if($this->list_view_num <= 20)
        {
            $this->list_view_num = 20;
        }
        else if($this->list_view_num <= 50)
        {
            $this->list_view_num = 50;
        }
        else if($this->list_view_num <= 75)
        {
            $this->list_view_num = 75;
        }
        else if($this->list_view_num <= 100 || $this->list_view_num > 100)
        {
            $this->list_view_num = 100;
        }
        
        $this->sort_order = RepositoryOutputFilter::string($this->sort_order);
        $this->sort_order = $this->validateSortOrder($this->search_term, $this->index_id, $this->sort_order);
        // 言語指定あり
        $this->lang = RepositoryOutputFilter::string($this->lang);
        $this->lang = strtolower($this->lang);
        if($this->Session != null && strlen($this->lang) == 0)
        {
            $this->lang = $this->Session->getParameter("_lang");
        }
        // 設定されていない言語が選択された場合、日本語で表示する
        $query = "SELECT lang_dirname FROM ".DATABASE_PREFIX ."language ".
                 "WHERE lang_dirname = ? ;";
        $params = array();
        $params[] = $this->lang;
        $result = $this->dbAccess->executeQuery($query, $params);
        
        if(count($result) == 0)
        {
            $this->lang = RepositoryConst::ITEM_ATTR_TYPE_LANG_JA;
        }
        
        if(strlen($this->listResords) > 0)
        {
            if($this->listResords != "all")
            {
                $this->listResords = "";
            }
        }
        
        ///// output  出力形式 /////
        $this->format = RepositoryOutputFilter::string($this->format);
        $this->format = strtolower($this->format);
    }
    
    
    /**
     * set request parameter
     * リクエストパラメータを精査し、範囲外の場合は適切な値を代入する
     */
    private function validateBackwordCompatible()
    {
        if(!isset($this->search_term[self::REQUEST_META]) && !isset($this->search_term[self::REQUEST_ALL])){
            if(isset($this->_request[self::REQUEST_OLD_KEYWORD])){
                if(!isset($this->_request[self::REQUEST_OLD_SEARCH_TYPE])){
                    $this->search_term[self::REQUEST_ALL] = $this->_request[self::REQUEST_OLD_KEYWORD];
                } else if($this->_request[self::REQUEST_OLD_SEARCH_TYPE] == "simple"){
                    $this->search_term[self::REQUEST_META] = $this->_request[self::REQUEST_OLD_KEYWORD];
                } else {
                    $this->search_term[self::REQUEST_ALL] = $this->_request[self::REQUEST_OLD_KEYWORD];
                }
            }
        }
        if(!isset($this->_request[self::REQUEST_PAGENO]) && isset($this->_request[self::REQUEST_OLD_PAGENO])){
            $this->page_no = $this->_request[self::REQUEST_OLD_PAGENO];
        }
        if(!isset($this->_request[self::REQUEST_COUNT]) && isset($this->_request[self::REQUEST_OLD_COUNT])){
            $this->list_view_num = $this->_request[self::REQUEST_OLD_COUNT];
        }
        if(!isset($this->_request[self::REQUEST_SHOWORDER]) && isset($this->_request[self::REQUEST_OLD_SHOWORDER])){
            $this->sort_order = $this->_request[self::REQUEST_OLD_SHOWORDER];
        }
    }

    /**
     * Set INI parameter
     * 各種設定を行う
     *
     * @throws RepositoryException
     */
    public function setActionParameter(){
        $this->initSearchList();
        if($this->search_type != null || count($this->search_term) == 0){
            if(isset($this->search_term[self::REQUEST_META])){
                $keyword = $this->search_term[self::REQUEST_META];
                $this->all_search_type = "simple";
            } else if(isset($this->search_term[self::REQUEST_ALL])){
                $keyword = $this->search_term[self::REQUEST_ALL];
                $this->all_search_type = "detail";
            } else {
            	
            	// Add Default Search Type 2014/12/03 K.Sugimoto --start--
        		$result = $this->getDefaultSearchType();
        		if(isset($result[0]['param_value'])){
        			if($result[0]['param_value'] == 0){
        				$this->all_search_type = "detail";
        			} else if($result[0]['param_value'] == 1){
        				$this->all_search_type = "simple";
        			}
        		}
            	// Add Default Search Type 2014/12/03 K.Sugimoto --end--
            	
                $keyword = "";
            }
            $this->Session->setParameter("searchkeyword", $keyword);
            $this->active_search_flag = 0;   // simple search
        } else {
            $this->setShowSearchParameter($this->search_term);
            $this->active_search_flag = 1;   // detail search
        }
        // 検索選択肢を設定する
        $query = "SELECT type_id ".
                 "FROM ". DATABASE_PREFIX ."repository_search_item_setup ".
                 "WHERE use_search = ?;";
        $params = null;
        $params[] = 1;
        $result = $this->dbAccess->executeQuery($query, $params);
        $this->detail_search_usable_item = $result;
        // 全文検索タイプを設定する
        if(is_null($this->all_search_type))
        {
            $this->all_search_type = "detail";
        }
        
        //アイテムタイプ項目を取得する
        $query = "SELECT item_type_id,item_type_name ".
                 "FROM ". DATABASE_PREFIX ."repository_item_type ";
        $result = $this->dbAccess->executeQuery($query);
        $this->detail_search_item_type = $result;
        
        $this->active_search_flag = intval($this->active_search_flag);
        if($this->active_search_flag < 0){
            $this->active_search_flag = 0;
        } else if($this->active_search_flag > 1){
            $this->active_search_flag = 1;
        }
    }
    
    // Add Default Search Type 2014/12/09 K.Sugimoto --start--
    /**
     * get default_search_type
     * デフォルト検索タイプを取得する
     *
     * @return array default search type
     *                array[0]["param_value"]
     */
    public function getDefaultSearchType()
    {
        $query = "SELECT param_value ".
                 "FROM ".DATABASE_PREFIX."repository_parameter ".
                 "WHERE param_name = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = "default_search_type";
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        
        return $result;
    }
    // Add Default Search Type 2014/12/09 K.Sugimoto --end--
    
    /**
     * init session detail_search_select_item
     * 詳細検索項目を設定する
     */
    private function initSearchList()
    {
        $query = "SELECT type_id ".
                 "FROM ". DATABASE_PREFIX ."repository_search_item_setup ".
                 "WHERE default_show = ?;";
        $params = null;
        $params[] = 1;
        $result = $this->dbAccess->executeQuery($query, $params);
        $this->detail_search_select_item = $result;
        $this->default_detail_search = $result;
    }
    
    /**
     * set show search parameter
     * 画面に表示する検索キーワードパラメータを設定する
     *
     * @param array $reqParam request parameter リクエストパラメータ
     *                         array[$searchKey]
     */
    private function setShowSearchParameter($reqParam)
    {
        $count = 0;
        $selectInfo = array();
        $subectFlag = false;
        $typeFlag = false;
        $idFlag = false;
        $pubYearFlag = false;
        $rightFlag = false;
        $grantDateFlag = false;
        foreach($reqParam as $key => $value){
            switch($key)
            {
                case self::REQUEST_META:
                    $this->Session->setParameter("searchkeyword", $value);
                    $this->all_search_type = "simple";
                    continue;
                    break;
                case self::REQUEST_ALL:
                    $this->Session->setParameter("searchkeyword", $value);
                    $this->all_search_type = "detail";
                    continue;
                    break;
                case self::REQUEST_TITLE:
                    $selectInfo[$count]["type_id"] = "1";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_CREATOR:
                    $selectInfo[$count]["type_id"] = "2";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_KEYWORD:
                    $selectInfo[$count]["type_id"] = "3";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_SUBJECT_DESC:
                case self::REQUEST_SUBJECT_LIST:
                    if($subectFlag){
                        break;
                    }
                    $selectInfo[$count]["type_id"] = "4";
                    if(isset($reqParam[self::REQUEST_SUBJECT_DESC])){
                        $selectInfo[$count]["value"] = $reqParam[self::REQUEST_SUBJECT_DESC];
                    }
                    if(isset($reqParam[self::REQUEST_SUBJECT_LIST])){
                        $selectInfo[$count]["checkList"] = $reqParam[self::REQUEST_SUBJECT_LIST];
                    }
                    $subectFlag = true;
                    $count++;
                    break;
                case self::REQUEST_DESCRIPTION:
                    $selectInfo[$count]["type_id"] = "5";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_PUBLISHER:
                    $selectInfo[$count]["type_id"] = "6";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_CONTRIBUTOR:
                    $selectInfo[$count]["type_id"] = "7";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_DATE:
                    $selectInfo[$count]["type_id"] = "8";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_ITEMTYPE_LIST:
                    $selectInfo[$count]["type_id"] = "9";
                    $selectInfo[$count]["checkList"] = $value;
                    $count++;
                    break;
                case self::REQUEST_TYPE_LIST:
                    if($typeFlag){
                        break;
                    }
                    $selectInfo[$count]["type_id"] = "10";
                    if(isset($reqParam[self::REQUEST_TYPE_LIST])){
                        $selectInfo[$count]["checkList"] = $reqParam[self::REQUEST_TYPE_LIST];
                    }
                    $typeFlag = true;
                    $count++;
                    break;
                case self::REQUEST_FORMAT:
                    $selectInfo[$count]["type_id"] = "11";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_ID_LIST:
                case self::REQUEST_ID_DESC:
                    if($idFlag){
                        break;
                    }
                    $selectInfo[$count]["type_id"] = "12";
                    if(isset($reqParam[self::REQUEST_ID_DESC])){
                        $selectInfo[$count]["value"] = $reqParam[self::REQUEST_ID_DESC];
                    }
                    if(isset($reqParam[self::REQUEST_ID_LIST])){
                        $selectInfo[$count]["checkList"] = $reqParam[self::REQUEST_ID_LIST];
                    }
                    $idFlag = true;
                    $count++;
                    break;
                case self::REQUEST_JTITLE:
                    $selectInfo[$count]["type_id"] = "13";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_PUBYEAR_FROM:
                case self::REQUEST_PUBYEAR_UNTIL:
                    if($pubYearFlag){
                        break;
                    }
                    $selectInfo[$count]["type_id"] = "14";
                    $selectInfo[$count]["value"] = "";
                    if(isset($reqParam[self::REQUEST_PUBYEAR_FROM])){
                        $selectInfo[$count]["value"] .= $reqParam[self::REQUEST_PUBYEAR_FROM];
                    }
                    $selectInfo[$count]["value"] .= "|";
                    if(isset($reqParam[self::REQUEST_PUBYEAR_UNTIL])){
                        $selectInfo[$count]["value"] .= $reqParam[self::REQUEST_PUBYEAR_UNTIL];
                    }
                    $pubYearFlag = true;
                    $count++;
                    break;
                case self::REQUEST_LANGUAGE:
                    $selectInfo[$count]["type_id"] = "15";
                    $selectInfo[$count]["checkList"] = $value;
                    $count++;
                    break;
                case self::REQUEST_AREA:
                    $selectInfo[$count]["type_id"] = "16";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_ERA:
                    $selectInfo[$count]["type_id"] = "17";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_RITHT_DESC:
                case self::REQUEST_RIGHT_LIST:
                    if($rightFlag){
                        break;
                    }
                    $selectInfo[$count]["type_id"] = "18";
                    if(isset($reqParam[self::REQUEST_RIGHT_LIST])){
                        $selectInfo[$count]["checkList"] = $reqParam[self::REQUEST_RIGHT_LIST];
                    }
                    if(isset($reqParam[self::REQUEST_RITHT_DESC])){
                        $selectInfo[$count]["value"] = $reqParam[self::REQUEST_RITHT_DESC];
                        $selectInfo[$count]["checkList"] .= ",free_input";
                    }
                    $rightFlag = true;
                    $count++;
                    break;
                case self::REQUEST_TEXTVERSION:
                    $selectInfo[$count]["type_id"] = "19";
                    if(strtolower($value) == "etd")
                    {
                        $value = strtoupper($value);
                    }
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_GRANTID:
                    $selectInfo[$count]["type_id"] = "20";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_GRANTDATE_FROM:
                case self::REQUEST_GRANTDATE_UNTIL:
                    if($grantDateFlag){
                        break;
                    }
                    $selectInfo[$count]["type_id"] = "21";
                    $selectInfo[$count]["value"] = "";
                    if(isset($reqParam[self::REQUEST_GRANTDATE_FROM])){
                        $selectInfo[$count]["value"] .= $reqParam[self::REQUEST_GRANTDATE_FROM];
                    }
                    $selectInfo[$count]["value"] .= "|";
                    if(isset($reqParam[self::REQUEST_GRANTDATE_UNTIL])){
                        $selectInfo[$count]["value"] .= $reqParam[self::REQUEST_GRANTDATE_UNTIL];
                    }
                    $grantDateFlag = true;
                    $count++;
                    break;
                case self::REQUEST_DEGREENAME:
                    $selectInfo[$count]["type_id"] = "22";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_GRANTOR:
                    $selectInfo[$count]["type_id"] = "23";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                case self::REQUEST_IDX:
                    $selectInfo[$count]["type_id"] = "24";
                    $selectInfo[$count]["checkList"] = $value;
                    $count++;
                    break;
                case self::REQUEST_WEKO_AUTHOR_ID:
                    $selectInfo[$count]["type_id"] = "25";
                    $selectInfo[$count]["value"] = $value;
                    $count++;
                    break;
                default:
                    break;
            }
        }
        if(count($selectInfo) == 0){
            $this->initSearchList();
        } else {
            $this->detail_search_select_item = $selectInfo;
        }
    }
    
    /**
     * set default search parameter
     * 検索キーワードパラメータのデフォルトを設定する
     */
    public function setDefaultSearchParameter()
    {
        // set search index
        $errorMsg = "";
        $this->RepositoryAction->getAdminParam('disp_index_type', $disp_index_type, $errorMsg);
        $this->RepositoryAction->getAdminParam('default_disp_index', $default_disp_index, $errorMsg);

        // 最も新しいアイテムのインデックス
        $indexAuthorityManager = new RepositoryIndexAuthorityManager($this->Session, $this->dbAccess, $this->TransStartDate);
        // Add OpenDepo 2013/12/02 R.Matsuura --end--
        if($disp_index_type == 1)
        {
            // インデックス指定
            $this->index_id = $default_disp_index;
            $public_index = $indexAuthorityManager->getPublicIndexQuery(false, $this->RepositoryAction->repository_admin_base, $this->RepositoryAction->repository_admin_room, $this->index_id);
            if(count($public_index) == 0){
                // Index closed.
                // Set root index for default search index.
                $this->index_id = 0;
            }
        }
        else
        {
            $publicIndexQuery = $indexAuthorityManager->getPublicIndexQuery(false, $this->RepositoryAction->repository_admin_base, $this->RepositoryAction->repository_admin_room);
            $sqlCmd = "SELECT idx.index_id ".
                      "FROM ". DATABASE_PREFIX ."repository_item AS item, ".
                      "     ". DATABASE_PREFIX ."repository_index AS idx, ".
                      "     ". DATABASE_PREFIX ."repository_position_index AS pidx ".
                      "INNER JOIN (".$publicIndexQuery.") pub ON pidx.index_id = pub.index_id ".
                      "WHERE item.shown_date<=NOW() ".
                      "AND item.item_id = pidx.item_id ".
                      "AND item.item_no = pidx.item_no ".
                      "AND item.shown_status = 1 ".
                      "AND pidx.index_id = idx.index_id ".
                      "AND idx.pub_date<=NOW() ".
                      "AND idx.public_state = 1 ".
                      "AND item.is_delete = 0 ".
                      "AND pidx.is_delete = 0 ".
                      "AND idx.is_delete = 0 ".
                      " ORDER BY item.shown_date desc, item.item_id desc; ";
            $items = $this->dbAccess->executeQuery($sqlCmd);
            if($items === false)
            {
                $this->index_id = 0;
            }
            if(count($items)==0){
                // if no items, shows root index
                $this->index_id = 0;
            } else {
                $this->index_id = $items[0]['index_id'];
            }
        }
        
        // validate
        $this->validate();
        
        $this->setActionParameter();
    }
    
    // Bug fix WEKO-2014-012 T.Koyasu 2014/06/10 --start--
    /**
     * Validate request parameters(search_term[$key]) for remove bad strings
     * 使用不可文字列をバリデートする
     *
     * @param string $key search key 検索キー
     */
    private function validateList($key)
    {
        $value = $this->search_term[$key];
        if(strlen($value) === 0){
            return;
        }
        
        // check min occur and max occur of each param
        switch($key)
        {
            case self::REQUEST_SUBJECT_LIST:
                $tmpArray = explode(",", $value);
                if(count($tmpArray) === 0){
                    return;
                }
                $searchArray = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
                $validateArray = array_intersect($searchArray, $tmpArray);
                
                $this->search_term[$key] = implode(",", $validateArray);
                
                break;
            case self::REQUEST_ITEMTYPE_LIST:
                $tmpArray = explode(",", $value);
                if(count($tmpArray) === 0){
                    return;
                }
                
                $query = "SELECT item_type_id ". 
                         " FROM ". DATABASE_PREFIX. "repository_item_type ".
                         " WHERE is_delete = ? ". 
                         " AND item_type_id IN (";
                $params = array();
                $params[] = 0;
                
                $tmpStr = "";
                for($ii = 0; $ii < count($tmpArray); $ii++)
                {
                    if(strlen($tmpStr) > 0){
                        $tmpStr .= ",";
                    }
                    $tmpStr .= "?";
                    $params[] = $tmpArray[$ii];
                }
                $query .= $tmpStr. ");";
                $result = $this->dbAccess->executeQuery($query, $params);
                
                $this->search_term[$key] = "";
                for($ii = 0; $ii < count($result); $ii++)
                {
                    if(strlen($this->search_term[$key]) > 0){
                        $this->search_term[$key] .= ",";
                    }
                    $this->search_term[$key] .= $result[$ii]["item_type_id"];
                }
                
                break;
            case self::REQUEST_TYPE_LIST:
                $tmpArray = explode(",", $value);
                if(count($tmpArray) === 0){
                    return;
                }
                $searchArray = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13);
                $validateArray = array_intersect($searchArray, $tmpArray);
                
                $this->search_term[$key] = implode(",", $validateArray);
                
                break;
            case self::REQUEST_ID_LIST:
                $tmpArray = explode(",", $value);
                if(count($tmpArray) === 0){
                    return;
                }
                $searchArray = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);
                $validateArray = array_intersect($searchArray, $tmpArray);
                
                $this->search_term[$key] = implode(",", $validateArray);
                
                break;
            case self::REQUEST_LANGUAGE:
                $tmpArray = explode(",", $value);
                if(count($tmpArray) === 0){
                    return;
                }
                $searchArray = array("ja", "en", "fr", "it", "de", "es", "zh", "ru", "la", "ms", "eo", "ar", "el", "ko", "otherlanguage");
                $validateArray = array_intersect($searchArray, $tmpArray);
                
                $this->search_term[$key] = implode(",", $validateArray);
                
                break;
            case self::REQUEST_RIGHT_LIST:
                $tmpArray = explode(",", $value);
                if(count($tmpArray) === 0){
                    return;
                }
                $searchArray = array(101, 102, 103, 104, 105, 106, "free_input");
                $validateArray = array_intersect($searchArray, $tmpArray);
                
                $this->search_term[$key] = implode(",", $validateArray);
                
                break;
            default:
                break;
        }
    }
    // Bug fix WEKO-2014-012 T.Koyasu 2014/06/10 --end--
    
    /**
     * Run the universally to perform processing during query execution
     * クエリ実行時に普遍的に行う処理を実行する
     *
     * @param string $query Query to run 実行するクエリ
     * @param array $params Parameters of the query クエリのパラメータ
     * @return array Result 実行結果
     *                array[$ii][$columnName]
     * @throws AppException
     */
    protected function executeSql($query, $params=array()){
        $result = $this->Db->execute($query, $params);
        if($result === false){
            // 例外
            throw new AppException($this->Db->ErrorMsg());
        }
        return $result;
    }
}
?>