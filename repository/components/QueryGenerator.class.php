<?php

/**
 * Search query generate class
 * 検索クエリ作成クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: QueryGenerator.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Query generator interface
 * クエリ作成クラスインターフェース
 */
require_once WEBAPP_DIR. '/modules/repository/components/QueryGeneratorInterFace.class.php';
/**
 * WEKO business factory class
 * WEKO用ファクトリークラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/WekoBusinessFactory.class.php';

/**
 * Search query generate class
 * 検索クエリ作成クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Querygenerator implements Repository_Components_Querygeneratorinterface
{
    // search table name
    /**
     * Search table name const
     * 検索テーブル名
     */
    const ALLMETADATA_TABLE = "repository_search_allmetadata";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const FILEDATA_TABLE = "repository_search_filedata";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const TITLE_TABLE = "repository_search_title";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const AUTHOR_TABLE = "repository_search_author";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const KEYWORD_TABLE = "repository_search_keyword";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const NIISUBJECT_TABLE = "repository_search_niisubject";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const NDC_TABLE = "repository_search_ndc";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const NDLC_TABLE = "repository_search_ndlc";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const BSH_TABLE = "repository_search_bsh";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const NDLSH_TABLE = "repository_search_ndlsh";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const MESH_TABLE = "repository_search_mesh";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const DDC_TABLE = "repository_search_ddc";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const LCC_TABLE = "repository_search_lcc";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const UDC_TABLE = "repository_search_udc";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const LCSH_TABLE = "repository_search_lcsh";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const DESCTIPTION_TABLE = "repository_search_description";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const PUBLISHER_TABLE = "repository_search_publisher";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const CONTRIBUTOR_TABLE = "repository_search_contributor";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const DATE_TABLE = "repository_search_date";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const TYPE_TABLE = "repository_search_type";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const FORMAT_TABLE = "repository_search_format";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const IDENTIFER_TABLE = "repository_search_identifier";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const URI_TABLE = "repository_search_uri";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const FULLTEXTURL_TABLE = "repository_search_fulltexturl";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const SELFDOI_TABLE = "repository_search_selfdoi";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const ISBN_TABLE = "repository_search_isbn";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const ISSN_TABLE = "repository_search_issn";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const NCID_TABLE = "repository_search_ncid";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const PMID_TABLE = "repository_search_pmid";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const DOI_TABLE = "repository_search_doi";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const NAID_TABLE = "repository_search_naid";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const ICHUSHI_TABLE = "repository_search_ichushi";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const JTITLE_TABLE = "repository_search_jtitle";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const DATAODISSUED_TABLE = "repository_search_dateofissued";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const LANGUAGE_TABLE = "repository_search_language";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const RELATION_TABLE = "repository_search_relation";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const COVERAGE_TABLE = "repository_search_coverage";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const RIGHTS_TABLE = "repository_search_rights";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const TEXTVERSION_TABLE = "repository_search_textversion";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const GRANTID_TABLE = "repository_search_grantid";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const DATEOFGRANTED_TABLE = "repository_search_dateofgranted";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const DEGREENAME_TABLE = "repository_search_degreename";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const GRANTOR_TABLE = "repository_search_grantor";
    /**
     * Sort table name
     * ソートテーブル名
     */
    const SORT_TABLE = "repository_search_sort";
    /**
     * Item table
     * アイテムテーブル
     */
    const ITEM_TABLE = "repository_item";
    /**
     * Position index table
     * 所属インデックステーブル
     */
    const POS_INDEX_TABLE = "repository_position_index";
    /**
     * Index table
     * インデックステーブル
     */
    const INDEX_TABLE = "repository_index";
    /**
     * Index browsing authority table
     * インデックス閲覧権限テーブル
     */
    const INDEX_RIGHT_TABLE = "repository_index_browsing_authority";
    /**
     * Index browsing group table
     * インデックス閲覧グループテーブル
     */
    const INDEX_GROUP_TABLE = "repository_index_browsing_groups";
    /**
     * Item type table
     * アイテムタイプテーブル
     */
    const ITEMTYPE_TABLE = "repository_item_type";
    /**
     * File table
     * ファイルテーブル
     */
    const FILE_TABLE = "repository_file";
    /**
     * Suffix table
     * サフィックステーブル
     */
    const SUFFIX_TABLE = "repository_suffix";
    /**
     * Search table name const
     * 検索テーブル名
     */
    const DATEOFISSUED_YMD_TABLE = "repository_search_dateofissued_ymd";
    /**
     * Name of personal name table
     * 氏名テーブルのテーブル名
     */
    const PERSONAL_NAME_TABLE = "repository_personal_name";

    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const ALL_TABLE_SHORT_NAME = "allmeta";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const FILEDATA_TABLE_SHORT_NAME = "filedata";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const TITLE_TABLE_SHORT_NAME = "title";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const AUTHOR_TABLE_SHORT_NAME = "auth";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const KEYWORD_TABLE_SHORT_NAME = "kw";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const NIISUBJECT_TABLE_SHORT_NAME = "niisubj";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const NDC_TABLE_SHORT_NAME = "ndc";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const NDLC_TABLE_SHORT_NAME = "ndlc";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const BSH_TABLE_SHORT_NAME = "bsh";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const NDLSH_TABLE_SHORT_NAME = "ndlsh";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const MESH_TABLE_SHORT_NAME = "mesh";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const DDC_TABLE_SHORT_NAME = "ddc";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const LCC_TABLE_SHORT_NAME = "lcc";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const UDC_TABLE_SHORT_NAME = "udc";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const LCSH_TABLE_SHORT_NAME = "lcsh";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const DESCTIPTION_TABLE_SHORT_NAME = "descr";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const PUBLISHER_TABLE_SHORT_NAME = "pub";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const CONTRIBUTOR_TABLE_SHORT_NAME = "contr";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const DATE_TABLE_SHORT_NAME = "date";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const TYPE_TABLE_SHORT_NAME = "type";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const FORMAT_TABLE_SHORT_NAME = "form";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const IDENTIFER_TABLE_SHORT_NAME = "id";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const URI_TABLE_SHORT_NAME = "uri";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const FULLTEXTURL_TABLE_SHORT_NAME = "fullurl";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const SELFDOI_TABLE_SHORT_NAME = "selfdoi";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const ISBN_TABLE_SHORT_NAME = "isbn";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const ISSN_TABLE_SHORT_NAME = "issn";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const NCID_TABLE_SHORT_NAME = "ncid";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const PMID_TABLE_SHORT_NAME = "pmid";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const DOI_TABLE_SHORT_NAME = "doi";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const NAID_TABLE_SHORT_NAME = "naid";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const ICHUSHI_TABLE_SHORT_NAME = "ichushi";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const JTITLE_TABLE_SHORT_NAME = "jtitle";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const DATAODISSUED_TABLE_SHORT_NAME = "dtissue";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const LANGUAGE_TABLE_SHORT_NAME = "lang";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const RELATION_TABLE_SHORT_NAME = "cove";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const COVERAGE_TABLE_SHORT_NAME = "relat";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const RIGHTS_TABLE_SHORT_NAME = "rights";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const TEXTVERSION_TABLE_SHORT_NAME = "textv";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const GRANTID_TABLE_SHORT_NAME = "grantid";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const DATEOFGRANTED_TABLE_SHORT_NAME = "dtgrant";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const DEGREENAME_TABLE_SHORT_NAME = "dgname";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const GRANTOR_TABLE_SHORT_NAME = "grantor";
    /**
     * Sort table short name
     * ソートテーブル短縮名
     */
    const SORT_TABLE_SHORT_NAME = "sort";
    /**
     * Item table short name
     * アイテムテーブル短縮名
     */
    const ITEM_TABLE_SHORT_NAME = "item";
    /**
     * Position index table short name
     * 所属インデックステーブル短縮名
     */
    const POS_INDEX_TABLE_SHORT_NAME = "pos";
    /**
     * Index table short name
     * インデックステーブル短縮名
     */
    const INDEX_TABLE_SHORT_NAME = "idx";
    /**
     * Index browsing authority table short name
     * インデックス権限テーブル短縮名
     */
    const INDEX_RIGHT_TABLE_SHORT_NAME = "idxrt";
    /**
     * Index browsing group table short name
     * インデックスグループテーブル短縮名
     */
    const INDEX_GROUP_TABLE_SHORT_NAME = "idxgr";
    /**
     * Item type table short name
     * アイテムタイプテーブル短縮名
     */
    const ITEMTYPE_TABLE_SHORT_NAME = "itemtype";
    /**
     * File table short name
     * ファイルテーブル短縮名
     */
    const FILE_TABLE_SHORT_NAME = "file";
    /**
     * Search table short name const
     * 検索テーブル短縮名
     */
    const DATEOFISSUED_YMD_TABLE_SHORT_NAME = "pubdate";
    /**
     * Suffix table short name
     * サフィックステーブル短縮名
     */
    const SUFFIX_TABLE_SHORT_NAME = "suf";
    /**
     * External search word table
     * 外部検索キーワードテーブル
     */
    const EXTERNAL_SEARCHWORD_TABLE = "repository_search_external_searchword";
    /**
     * External search word table short name
     * 外部検索キーワードテーブル短縮名
     */
    const EXTERNAL_SEARCHWORD_TABLE_SHORT_NAME = "externalsearch";
    /**
     * Short name of personal name table
     * 氏名テーブルのテーブル省略名
     */
    const PERSONAL_NAME_TABLE_SHORT_NAME = "name";

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
     */
    const REQUEST_WEKO_AUTHOR_ID = "wekoAuthorId";
    
    // search type ID
    /**
     * NII subject ID
     * NII subject ID
     */
    const NIISUBJECT_ID = 1;
    /**
     * NDC ID
     * NDC ID
     */
    const NDC_ID = 2;
    /**
     * NDLC ID
     * NDLC ID
     */
    const NDLC_ID = 3;
    /**
     * BSH ID
     * BSH ID
     */
    const BSH_ID = 4;
    /**
     * NDLSH ID
     * NDLSH ID
     */
    const NDLSH_ID = 5;
    /**
     * MESH ID
     * MESH ID
     */
    const MESH_ID = 6;
    /**
     * DDC ID
     * DDC ID
     */
    const DDC_ID = 7;
    /**
     * LCC ID
     * LCC ID
     */
    const LCC_ID = 8;
    /**
     * UDC ID
     * UDC ID
     */
    const UDC_ID = 9;
    /**
     * LCSH ID
     * LCSH ID
     */
    const LCSH_ID = 10;
    /**
     * Identifier ID
     * Identifier ID
     */
    const IDENTIFER_ID = 1;
    /**
     * URI ID
     * URI ID
     */
    const URI_ID = 2;
    /**
     * FullText ID
     * FullText ID
     */
    const FULLTEXTURL_ID = 3;
    /**
     * selfDOI ID
     * selfDOI ID
     */
    const SELFDOI_ID = 4;
    /**
     * ISBN ID
     * ISBN ID
     */
    const ISBN_ID = 5;
    /**
     * ISSN ID
     * ISSN ID
     */
    const ISSN_ID = 6;
    /**
     * NCID ID
     * NCID ID
     */
    const NCID_ID = 7;
    /**
     * PMID ID
     * PMID ID
     */
    const PMID_ID = 8;
    /**
     * DOI ID
     * DOI ID
     */
    const DOI_ID = 9;
    /**
     * NAID ID
     * NAID ID
     */
    const NAID_ID = 10;
    /**
     * ICHUSHI ID
     * 医中誌ID
     */
    const ICHUSHI = 11;

    /**
     * Inner join flag string
     * InnerJoinフラグ文字列
     */
    const INNER_JOIN = "innerJoin";
    
    /**
     * set fulltext index flag
     * フルテキストインデックスフラグ
     *
     * @var bool
     */
    private $setTableList = false;
    /**
     * Table prefix
     * テーブル名プレフィックス文字列
     *
     * @var string
     */
    public $db_prefix = null;
    /**
     * User ID
     * ユーザーID
     *
     * @var string
     */
    public $user_id = null;
    /**
     * Search engine
     * 検索エンジン
     *
     * @var string
     */
    public $searchEngine = null;
    
    
    /**
     * construct
     * コンストラクタ
     *
     * @param string $db_prefix table prefix テーブル名プレフィックス文字列
     */
    function __construct($db_prefix)
    {
        $this->db_prefix = $db_prefix;
    }
    
    /**
     * create detail search Query
     * 詳細検索クエリ文字列を作成する
     *
     * @param SearchQueryParameter $searchInfo search parameter object 検索パラメータオブジェクト
     * @param string               $searchQuery search query string 検索クエリ文字列
     * @param array                $connectQueryParam search qeury parameter 検索クエリパラメータ
     *                                                        array[$ii]
     * @return bool true/false success/failed 成功/失敗
     */
    public function createDetailSearchQuery($searchInfo, &$searchQuery, &$connectQueryParam)
    {
        // パラメータの宣言
        $this->user_id = $searchInfo->user_id;
        $this->searchEngine = $searchInfo->searchEngine;
        
        $connectToTableName = self::SORT_TABLE_SHORT_NAME;
        $index_id = $searchInfo->index_id;
        $user_auth_id = $searchInfo->user_auth_id;
        $auth_id = $searchInfo->auth_id;
        $adminUser = $searchInfo->adminUser;
        $sort_order = $searchInfo->sort_order;
        $lang = $searchInfo->lang;
        
        // 設定テーブルリスト
        $this->setTableList = null;
        // 連結タイプ
        $connectType = self::INNER_JOIN;
        // 連結実行フラグ
        $connectFlag = false;
        // 検索クエリの検索条件部分
        $connectQuery = "";
        // 検索クエリ連結の条件文
        $connectTermQuery = "";
        $connectQueryParam = array();
        if(!$adminUser){
            $connectTermQuery .= "WHERE ";
            if(isset($this->user_id) && $this->user_id != '0'){
                $connectTermQuery .= "( ".self::ITEM_TABLE_SHORT_NAME.".ins_user_id = ? OR ";
                $connectQueryParam[] = $this->user_id;
            }
            $connectFlag = true;
            // item rights
            $itemTableName = self::ITEM_TABLE_SHORT_NAME;
            if($connectType == self::INNER_JOIN){
                $connectQuery .= "INNER JOIN ".$this->db_prefix.self::ITEM_TABLE." AS ".$itemTableName." ON ".
                                 $connectToTableName.".item_id = ".$itemTableName.".item_id ";
                $this->setTableList[self::ITEM_TABLE] = $itemTableName;
                $connectTermQuery .= "( ".$itemTableName.".shown_status = 1 AND ".$itemTableName.".shown_date <= NOW() AND ".$itemTableName.".is_delete = 0 ) ";
            } else {
                $connectTermQuery .= "( item_id, item_no) IN ( ".
                                     " SELECT item_id, item_no ".
                                     " FROM ".$this->db_prefix.self::ITEM_TABLE." ".
                                     " WHERE ( shown_status = 1 AND shown_date <= NOW() AND is_delete = 0  )";
            }
        }
        // index rights
        $this->createIndexRightsQuery($connectToTableName, $user_auth_id, $auth_id, $searchInfo->groupList,
                                      $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType, $adminUser);
        $addPub = false;
        $addGrant = false;
        $searchFlag = false;
        $addRights = false;
        $addPubDate = false;
        
        if(isset($this->user_id) && $this->user_id != '0' && !$adminUser){
            $connectTermQuery .= ")";
        }
        
        foreach($searchInfo->search_term as $request => $value){
            switch($request){
                case self::REQUEST_META:
                    $tmpTermQuery = "";
                    if($connectFlag){
                        $tmpTermQuery .= "AND ((";
                    } else {
                        $tmpTermQuery .= "WHERE ((";
                    }
                    $tmpFlag = true;
                    $andor = "";
                    $result1 = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::ALLMETADATA_TABLE, self::ALL_TABLE_SHORT_NAME, $value, 
                                               $andor, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $tmpFlag, $connectType);
                    if($result1){
                        $andor = "OR";
                    }
                    $result2 = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::EXTERNAL_SEARCHWORD_TABLE, self::EXTERNAL_SEARCHWORD_TABLE_SHORT_NAME, $value, 
                                               $andor, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $tmpFlag, $connectType, $request);
                    if($result1 || $reuslt2){
                        $connectTermQuery .= $tmpTermQuery .")) ";
                        $connectFlag = true;
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_ALL:
                    $tmpTermQuery = "";
                    if($connectFlag){
                        $tmpTermQuery .= "AND ((";
                    } else {
                        $tmpTermQuery .= "WHERE ((";
                    }
                    $tmpFlag = true;
                    $andor = "";
                    $result1 = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::ALLMETADATA_TABLE, self::ALL_TABLE_SHORT_NAME, $value, 
                                               $andor, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $tmpFlag, $connectType);
                    if($result1){
                        $andor = "OR";
                    }
                    $result2 = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::FILEDATA_TABLE, self::FILEDATA_TABLE_SHORT_NAME, $value, 
                                               $andor, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $tmpFlag, $connectType, $request);
                    $result3 = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::EXTERNAL_SEARCHWORD_TABLE, self::EXTERNAL_SEARCHWORD_TABLE_SHORT_NAME, $value, 
                                               $andor, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $tmpFlag, $connectType, $request);
                    if($result1 || $result2 || $result3){
                        $connectTermQuery .= $tmpTermQuery .")) ";
                        $connectFlag = true;
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_TITLE:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::TITLE_TABLE, self::TITLE_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_CREATOR:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::AUTHOR_TABLE, self::AUTHOR_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_KEYWORD:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::KEYWORD_TABLE, self::KEYWORD_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_SUBJECT_LIST:
                    $result = $this->createSubjectQuery(self::SORT_TABLE_SHORT_NAME, $value, $searchInfo->search_term[self::REQUEST_SUBJECT_DESC], 
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_DESCRIPTION:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::DESCTIPTION_TABLE, self::DESCTIPTION_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_PUBLISHER:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::PUBLISHER_TABLE, self::PUBLISHER_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_CONTRIBUTOR:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::CONTRIBUTOR_TABLE, self::CONTRIBUTOR_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_DATE:
                    $result = $this->createDateQuery(self::SORT_TABLE_SHORT_NAME, self::DATE_TABLE, self::DATE_TABLE_SHORT_NAME, $value, $value, false, 
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_ITEMTYPE_LIST:
                    $result = $this->createINSearchColumnQuery(self::SORT_TABLE_SHORT_NAME, self::ITEM_TABLE, self::ITEM_TABLE_SHORT_NAME, $value, "item_type_id",
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_TYPE_LIST:
                    $tmpValue = str_replace("free_input", "", $value);
                    $result = $this->createTypeQuery(self::SORT_TABLE_SHORT_NAME, self::ITEM_TABLE, self::ITEM_TABLE_SHORT_NAME, $tmpValue,
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_FORMAT:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::FORMAT_TABLE, self::FORMAT_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_ID_LIST:
                    $result = $this->createIDQuery(self::SORT_TABLE_SHORT_NAME, $value, $searchInfo->search_term[self::REQUEST_ID_DESC ], 
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_JTITLE:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::JTITLE_TABLE, self::JTITLE_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_PUBYEAR_FROM:
                case self::REQUEST_PUBYEAR_UNTIL:
                    if(!$addPub){
                        $result = $this->createDateQuery(self::SORT_TABLE_SHORT_NAME, self::DATAODISSUED_TABLE, self::DATAODISSUED_TABLE_SHORT_NAME, 
                                               $searchInfo->search_term[self::REQUEST_PUBYEAR_FROM], $searchInfo->search_term[self::REQUEST_PUBYEAR_UNTIL], true, 
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                        if($result){
                            $searchFlag = true;
                        }
                    }
                    $addPub = true;
                    break;
                case self::REQUEST_LANGUAGE:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::LANGUAGE_TABLE, self::LANGUAGE_TABLE_SHORT_NAME, $value, 
                                               "AND", "OR", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_AREA:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::RELATION_TABLE, self::RELATION_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_ERA:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::COVERAGE_TABLE, self::COVERAGE_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_RIGHT_LIST:
                    $andor = "AND";
                    if($addRights){
                        $andor = "OR";
                    }
                    $tmpValue = str_replace("free_input", "", $value);
                    $result = $this->createINSearchColumnQuery(self::SORT_TABLE_SHORT_NAME, self::FILE_TABLE, self::FILE_TABLE_SHORT_NAME, $tmpValue, "license_id",
                                               $andor, $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                        $addRights = true;
                    }
                    break;
                case self::REQUEST_RITHT_DESC:
                    $andor = "AND";
                    if($addRights){
                        $andor = "OR";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::RIGHTS_TABLE, self::RIGHTS_TABLE_SHORT_NAME, $value, 
                                               $andor, "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                        $addRights = true;
                    }
                    break;
                case self::REQUEST_TEXTVERSION:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::TEXTVERSION_TABLE, self::TEXTVERSION_TABLE_SHORT_NAME, $value, 
                                               "AND", "OR", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_GRANTID:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::GRANTID_TABLE, self::GRANTID_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_GRANTDATE_FROM:
                case self::REQUEST_GRANTDATE_UNTIL:
                    if(!$addGrant){
                        $result = $this->createDateQuery(self::SORT_TABLE_SHORT_NAME, self::DATEOFGRANTED_TABLE, self::DATEOFGRANTED_TABLE_SHORT_NAME, 
                                               $searchInfo->search_term[self::REQUEST_GRANTDATE_FROM], $searchInfo->search_term[self::REQUEST_GRANTDATE_UNTIL], false, 
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                        if($result){
                            $searchFlag = true;
                        }
                    }
                    $addGrant = true;
                    break;
                case self::REQUEST_DEGREENAME:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::DEGREENAME_TABLE, self::DEGREENAME_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_GRANTOR:
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::GRANTOR_TABLE, self::GRANTOR_TABLE_SHORT_NAME, $value, 
                                               "AND", "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                case self::REQUEST_IDX:
                    $result = $this->createINSearchColumnQuery(self::SORT_TABLE_SHORT_NAME, self::POS_INDEX_TABLE, self::POS_INDEX_TABLE_SHORT_NAME, $value, "index_id",
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if($result){
                        $searchFlag = true;
                    }
                    break;
                    
                case self::REQUEST_WEKO_ID:
                    // string length of weko_id is 1-8
                    $result = $this->createINSearchColumnQuery(self::SORT_TABLE_SHORT_NAME, self::SUFFIX_TABLE, self::SUFFIX_TABLE_SHORT_NAME, $value, "suffix", 
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    
                    if($result){
                        $searchFlag = true;
                    }
                    
                    break;

                // Add suppleContentsEntry Y.Yamazawa --start-- 2015/03/20 --start--
                case self::REQUEST_ITEM_IDS:
                        // string length of weko_id is 1-8
                    $result = $this->createINSearchColumnQuery(self::SORT_TABLE_SHORT_NAME, self::ITEM_TABLE, self::ITEM_TABLE_SHORT_NAME, $value, "item_id",
                                                "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);

                    if($result){
                        $searchFlag = true;
                    }

                        break;
               // Add suppleContentsEntry Y.Yamazawa --end-- 2015/03/20 --end--

                case self::REQUEST_PUBDATE_FROM:
                case self::REQUEST_PUBDATE_UNTIL:
                    if(!$addPubDate){
                        if (array_key_exists(self::REQUEST_PUBDATE_FROM, $searchInfo->search_term)) {
                            $pubDateFrom = $searchInfo->search_term[self::REQUEST_PUBDATE_FROM];
                        }
                        else {
                            $pubDateFrom = "";
                        }
                        
                        if (array_key_exists(self::REQUEST_PUBDATE_UNTIL, $searchInfo->search_term)) {
                            $pubDateUntil = $searchInfo->search_term[self::REQUEST_PUBDATE_UNTIL];
                        }
                        else {
                            $pubDateUntil = "";
                        }
                        
                        $result = $this->createDateQuery(self::SORT_TABLE_SHORT_NAME, self::DATEOFISSUED_YMD_TABLE, self::DATEOFISSUED_YMD_TABLE_SHORT_NAME,
                                               $pubDateFrom, $pubDateUntil, false,
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                        if($result){
                            $searchFlag = true;
                        }
                    }
                    $addPubDate = true;
                    break;
                
                case self::REQUEST_WEKO_AUTHOR_ID:
                    $result = $this->createINSearchColumnQuery(self::SORT_TABLE_SHORT_NAME, self::PERSONAL_NAME_TABLE, self::PERSONAL_NAME_TABLE_SHORT_NAME, $value, "author_id", 
                                               "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    
                    if($result){
                        $searchFlag = true;
                    }
                    
                    break;

                default:
                    break;
            }
        }
        if(!$searchFlag && strlen($index_id) > 0){
            $this->createINSearchColumnQuery(self::SORT_TABLE_SHORT_NAME, self::POS_INDEX_TABLE, self::POS_INDEX_TABLE_SHORT_NAME, $index_id, "index_id",
                                       "AND", $connectQuery, $connectTermQuery, $connectQueryParam, $connectFlag, $connectType);
        } else {
            $index_id = "";
        }
        $connectQuery .= $connectTermQuery;
        
        // SELECT対象の選択
        if($searchInfo->countFlag) {
            // 件数検索を行う処理
            $searchQuery = "SELECT COUNT(DISTINCT ".self::SORT_TABLE_SHORT_NAME.".item_id, ".self::SORT_TABLE_SHORT_NAME.".item_no) AS total ".
                           "FROM ".$this->db_prefix.self::SORT_TABLE." AS ".self::SORT_TABLE_SHORT_NAME." ";
            if($connectFlag){
                $searchQuery .= $connectQuery;
            }
        } else {
            // アイテム検索を行う処理
            // sort order
            if($sort_order == self::ORDER_CUSTOM_SORT_ASC || $sort_order == self::ORDER_CUSTOM_SORT_DESC){
                $searchQuery = "SELECT DISTINCT ".self::SORT_TABLE_SHORT_NAME.".item_id, ".self::SORT_TABLE_SHORT_NAME.".item_no, ".self::SORT_TABLE_SHORT_NAME.".uri ".
                               "FROM ".$this->db_prefix.self::SORT_TABLE." AS ".self::SORT_TABLE_SHORT_NAME." ";
                if(!isset($this->setTableList[self::POS_INDEX_TABLE])){
                    $searchQuery .= "INNER JOIN ".$this->db_prefix.self::POS_INDEX_TABLE." AS ".self::POS_INDEX_TABLE_SHORT_NAME." ON ".
                                     self::SORT_TABLE_SHORT_NAME.".item_id = ".$this->db_prefix.self::ITEM_TABLE.".item_id ";
                }
            } else {
                // execute search
                $searchQuery = "SELECT DISTINCT ".self::SORT_TABLE_SHORT_NAME.".item_id, ".self::SORT_TABLE_SHORT_NAME.".item_no, ".self::SORT_TABLE_SHORT_NAME.".uri ".
                               "FROM ".$this->db_prefix.self::SORT_TABLE." AS ".self::SORT_TABLE_SHORT_NAME." ";
            }
            if($connectFlag){
                $searchQuery .= $connectQuery;
            }
        }
        
        // ///// sort order /////
        switch($sort_order)
        {
            case self::ORDER_TITLE_ASC:
            case self::ORDER_TITLE_DESC:
                // sort culum
                $sortTitle = "title";
                if($lang == "japanese")
                {
                    $sortTitle = "title";
                } else {
                    $sortTitle = "title_en";
                }
                // sort order
                if($sort_order == self::ORDER_TITLE_ASC)
                {
                    $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".".$sortTitle." ASC ";
                }
                else
                {
                    $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".".$sortTitle." DESC ";
                }
                break;
            case self::ORDER_INS_USER_ASC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".ins_user_id ASC, ".self::SORT_TABLE_SHORT_NAME.".item_id ASC ";
                break;
            case self::ORDER_INS_USER_DESC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".ins_user_id DESC, ".self::SORT_TABLE_SHORT_NAME.".item_id DESC ";
                break;
            case self::ORDER_ITEM_TYPE_ID_ASC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".item_type_id ASC, ".self::SORT_TABLE_SHORT_NAME.".item_id ASC ";
                break;
            case self::ORDER_ITEM_TYPE_ID_DESC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".item_type_id DESC, ".self::SORT_TABLE_SHORT_NAME.".item_id DESC ";
                break;
            case self::ORDER_WEKO_ID_ASC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".weko_id ASC, ".self::SORT_TABLE_SHORT_NAME.".uri ASC ";
                break;
            case self::ORDER_WEKO_ID_DESC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".weko_id DESC, ".self::SORT_TABLE_SHORT_NAME.".uri DESC ";
                break;
            case self::ORDER_MOD_DATE_ASC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".mod_date ASC, ".self::SORT_TABLE_SHORT_NAME.".item_id ASC ";
                break;
            case self::ORDER_MOD_DATE_DESC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".mod_date DESC, ".self::SORT_TABLE_SHORT_NAME.".item_id DESC ";
                break;
            case self::ORDER_INS_DATE_ASC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".ins_date ASC, ".self::SORT_TABLE_SHORT_NAME.".item_id ASC ";
                break;
            case self::ORDER_INS_DATE_DESC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".ins_date DESC, ".self::SORT_TABLE_SHORT_NAME.".item_id DESC ";
                break;
            case self::ORDER_REVIEW_DATE_ASC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".review_date ASC, ".self::SORT_TABLE_SHORT_NAME.".item_id ASC ";
                break;
            case self::ORDER_REVIEW_DATE_DESC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".review_date DESC, ".self::SORT_TABLE_SHORT_NAME.".item_id DESC ";
                break;
            case self::ORDER_DATEOFISSUED_ASC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".biblio_date ASC, ".self::SORT_TABLE_SHORT_NAME.".item_id ASC ";
                break;
            case self::ORDER_DATEOFISSUED_DESC:
                $searchQuery .= " ORDER BY ".self::SORT_TABLE_SHORT_NAME.".biblio_date DESC, ".self::SORT_TABLE_SHORT_NAME.".item_id DESC ";
                break;
            case self::ORDER_CUSTOM_SORT_ASC:
                $searchQuery .= " ORDER BY ".self::POS_INDEX_TABLE_SHORT_NAME.".custom_sort_order ASC, ".self::POS_INDEX_TABLE_SHORT_NAME.".item_id ASC ";
                break;
            case self::ORDER_CUSTOM_SORT_DESC:
                $searchQuery .= " ORDER BY ".self::POS_INDEX_TABLE_SHORT_NAME.".custom_sort_order DESC, ".self::POS_INDEX_TABLE_SHORT_NAME.".item_id DESC ";
                break;
            default:
                break;
        }
        
        return true;
    }
    
    /**
     * create search index rights Query
     * 「権利」検索クエリ作成
     *
     * @param string $connectToTableName connect to table name 結合先テーブル名
     * @param int $baseRights base authority ベース権限
     * @param int $roomRights room authority ルーム権限
     * @param array $groupIDList group ID list グループIDリスト
     *                            array[$ii]
     * @param string $connectQuery query クエリベース文
     * @param string $connectTermQuery where query クエリWHERE条件文
     * @param array $connectQueryParam query parameter クエリパラメータ
     *                                  array[$ii]
     * @param bool $connectFlag join flag 結合フラグ
     * @param string $connectType join type 結合タイプ
     * @param bool $isAdminUser admin flag 管理者ユーザーフラグ
     * @return bool true/false success/failed 成功/失敗
     */
    private function createIndexRightsQuery($connectToTableName, $baseRights, $roomRights, $groupIDList,
                                         &$connectQuery, &$connectTermQuery, &$connectQueryParam, &$connectFlag, $connectType, $isAdminUser)
    {
        if($connectFlag){
            $connectTermQuery .= "AND ";
        } else {
            $connectTermQuery .= "WHERE ";
            $connectFlag = true;
        }
        
        // connect repository_position_index 
        $posName = self::POS_INDEX_TABLE_SHORT_NAME;
        if($connectType == self::INNER_JOIN){
            $this->setTableList[self::POS_INDEX_TABLE] = $posName;
            $connectQuery .= "INNER JOIN ".$this->db_prefix.self::POS_INDEX_TABLE." AS ".$posName." ON ".
                             $connectToTableName.".item_id = ".$posName.".item_id ";
            $connectTermQuery .= " ".$posName.".is_delete = 0 ";
        } else {
            $connectTermQuery .= "(item_id, item_no ) IN (".
                                 " SELECT item_id, item_no ".
                                 " FROM ".$this->db_prefix.self::POS_INDEX_TABLE." ".
                                 " WHERE is_delete = 0 ";
        }
        if($isAdminUser){
            if($connectType != self::INNER_JOIN){
                $connectTermQuery .= " ) ";
            }
            return true;
        }
        // connect repository_index 
        $indexName = self::INDEX_TABLE_SHORT_NAME;
        if($connectType == self::INNER_JOIN){
            $this->setTableList[self::INDEX_TABLE] = $indexName;
            $connectQuery .= "INNER JOIN ".$this->db_prefix.self::INDEX_TABLE." AS ".$indexName." ON ".
                             $posName.".index_id = ".$indexName.".index_id ";
            $connectTermQuery .= " AND ".$indexName.".is_delete = 0 ";
        }
        
        // connect repository_index_rights 
        $indexRightName = self::INDEX_RIGHT_TABLE_SHORT_NAME;
        if($connectType == self::INNER_JOIN){
            $this->setTableList[self::INDEX_RIGHT_TABLE] = $indexRightName;
            $connectQuery .= "INNER JOIN ".$this->db_prefix.self::INDEX_RIGHT_TABLE." AS ".$indexRightName." ON ".
                             $posName.".index_id = ".$indexRightName.".index_id ";
                         
            $connectTermQuery .= " AND ( ( ".$indexRightName.".public_state = 1 ". 
                                 " AND  ".$indexRightName.".pub_date <= NOW() ) ".
                                 " OR  ".$indexName.".owner_user_id = ? ) ".
                                 " AND  ".$indexRightName.".is_delete = 0 ";
            $connectTermQuery .= " AND ( ".$indexRightName.".exclusive_acl_role_id < ? AND ".$indexRightName.".exclusive_acl_room_auth < ? ) ";
            $connectQueryParam[] = $this->user_id;
            if(!isset($baseRights) || strlen($baseRights) == 0){
                $connectQueryParam[] = 1;
            } else {
                $connectQueryParam[] = $baseRights;
            }
            $connectQueryParam[] = $roomRights;
        } else {
            $connectTermQuery .= " AND ( index_id ) IN (".
                                 " SELECT ".$indexRightName.".index_id ".
                                 " FROM ".$this->db_prefix.self::INDEX_RIGHT_TABLE." AS ".$indexRightName.", ".$this->db_prefix.self::INDEX_TABLE." AS ".$indexName." ".
                                 " WHERE ".$indexRightName.".index_id = ".$indexName.".index_id ".
                                 "  AND ( ( ".$indexRightName.".public_state = 1 ". 
                                 "   AND ".$indexRightName.".pub_date <= NOW() ) ".
                                 "   OR ".$indexName.".owner_user_id = ? ) ".
                                 "  AND ".$indexRightName.".is_delete = 0 ".
                                 "  AND ".$indexName.".is_delete = 0 ".
                                 "  AND ( ".$indexRightName.".exclusive_acl_role_id < ? AND ".$indexRightName.".exclusive_acl_room_auth < ? ) ";
            $connectQueryParam[] = $this->user_id;
            if(isset($baseRights) || strlen($baseRights) == 0){
                $connectQueryParam[] = 1;
            } else {
                $connectQueryParam[] = $baseRights;
            }
            $connectQueryParam[] = $roomRights;
        }
        
        // connect repository_index_groups 
        $indexGroupName = self::INDEX_GROUP_TABLE_SHORT_NAME;
        if(count($groupIDList)>0){
            $connectTermQuery .= "AND ( EXISTS ( ".
                                 " SELECT * ".
                                 " FROM ".$this->db_prefix."pages_users_link AS link ".
                                 " WHERE link.room_id IN ( "; 
            $count = 0;
            for($ii = 0; $ii < count($groupIDList); $ii++){
                if($count > 0){
                    $connectTermQuery .= ",";
                }
                $connectTermQuery .= "?";
                $connectQueryParam[] = $groupIDList[$ii]["room_id"];
                $count++;
            }
            $connectTermQuery .= ") ";    // exclusive_acl_group_id IN ( )
            $connectTermQuery .= " AND link.room_id NOT IN ( ".
                                 "  SELECT ".$indexGroupName.".exclusive_acl_group_id ".
                                 "  FROM ".$this->db_prefix.self::INDEX_GROUP_TABLE." AS ".$indexGroupName." ".
                                 "  WHERE ".$indexGroupName.".is_delete = 0 AND ".$indexGroupName.".index_id = ".$indexName.".index_id ".
                                 " ) ".
                                 ") ";
            $connectTermQuery .= " OR NOT EXISTS ( ".
                                 "  SELECT * ".
                                 "  FROM ".$this->db_prefix.self::INDEX_GROUP_TABLE." AS ".$indexGroupName." ".
                                 "  WHERE ".$indexGroupName.".is_delete = 0 AND ".$indexGroupName.".index_id = ".$indexName.".index_id ".
                                 "  AND ".$indexGroupName.".exclusive_acl_group_id = 0 ".
                                 " ) ".
                                 ") ";
        }
        if($connectType != self::INNER_JOIN){
            $connectTermQuery .= ") ) ";
        }
        return true;
    }
    
    /**
     * create search fulltext Query
     * 全文検索クエリ作成
     *
     * @param string $connectToTableName connect to table name 結合先テーブル名
     * @param string $connetFromTableName connect from table name 結合元テーブル名
     * @param string $shortName table short name テーブル短縮名
     * @param string $searchValue search value 検索文字列
     * @param string $outorAndor "AND" or "OR" in where sentence WHERE句部分のクエリ結合時の条件タイプ式
     * @param string $innerAndor "AND" or "OR" in join sentence JOIN句部分のクエリ結合時の条件タイプ式
     * @param string $connectQuery query クエリベース文
     * @param string $connectTermQuery where query クエリWHERE条件文
     * @param array $connectQueryParam query parameter クエリパラメータ
     *                                  array[$ii]
     * @param bool $connectFlag join flag 結合フラグ
     * @param string $connectType join type 結合タイプ
     * @param string $request search request parameter key 検索リクエストパラメータのキー文字列
     * @return bool true/false success/failed 成功/失敗
     */
    private function createFullTextQuery($connectToTableName, $connetFromTableName, $shortName, $searchValue,
                                         $outorAndor, $innerAndor, &$connectQuery, &$connectTermQuery, &$connectQueryParam, &$connectFlag, $connectType, $request='')
    {
        // connect
        if(strlen($searchValue) == 0){
            return false;
        }
        $searchStringList = preg_split("/[\s,']+/", $searchValue);
        if(count($searchStringList) == 0){
            return false;
        }
        
        // search fulltext
        $isFulltext = false;
        if($this->searchEngine == "senna") {
            $isFulltext = true;
        } else if($this->searchEngine == "mroonga") {
            $isFulltext = true;
            $isMroongaExist = true;
        }
        
        $connectString = "";
        if($connectFlag){
            $connectString .= $outorAndor." ";
        } else {
            $connectString .= "WHERE ";
            $connectFlag = true;
        }
        $innerJoinFlag = true;
        if(array_key_exists($connetFromTableName, $this->setTableList)){
            $shortName = $this->setTableList[$connetFromTableName];
            $innerJoinFlag = false;
        } 
        $count = 0;
        $tmpTermQuery = "( ";
        for($ii = 0; $ii < count($searchStringList); $ii++){
            if(strlen($searchStringList[$ii]) == 0){
                continue;
            }
            if($count > 0){
                $tmpTermQuery .= $innerAndor." ";
            }
            // Add Senna judge T.Ichikawa 2014/12/01 --start--
            if($this->searchEngine == "mroonga") {
                // 括弧は勝手にエスケープされると困るので手動でエスケープに変更
                $tmpTermQuery .= "MATCH(".$shortName.".metadata) AGAINST(mroonga_escape(?, '~><-*`\"') IN BOOLEAN MODE) ";
                $searchStringList[$ii] = str_replace("\\", "\\\\", $searchStringList[$ii]);
                $searchStringList[$ii] = str_replace("(", "\\(", $searchStringList[$ii]);
                $searchStringList[$ii] = str_replace(")", "\\)", $searchStringList[$ii]);
                // 検索文字列パラメータを異体字に変換する
                $convertSearchWord = BusinessFactory::getFactory()->getBusiness("businessConvertsearchword");
                $searchStringList[$ii] = $convertSearchWord->convertSearchWordToCorrespondVariants($searchStringList[$ii]);
                $connectQueryParam[] = "+".$searchStringList[$ii];
            } else if($this->searchEngine == "senna") {
                $tmpTermQuery .= "MATCH(".$shortName.".metadata) AGAINST(? IN BOOLEAN MODE) ";
                $connectQueryParam[] = "+".$searchStringList[$ii];
            } else {
                // \は4重にする必要あり
                $searchStringList[$ii] = mb_ereg_replace('\\\\','\\\\',$searchStringList[$ii]);
                // %, _はエスケープする必要あり
                $searchStringList[$ii] = mb_ereg_replace('%','\%',$searchStringList[$ii]);
                $searchStringList[$ii] = mb_ereg_replace('_','\_',$searchStringList[$ii]);
                $tmpTermQuery .= $shortName.".metadata LIKE ? ";
                $connectQueryParam[] = "%".$searchStringList[$ii]."%";
            }
            // Add Senna judge T.Ichikawa 2014/12/01 --end--
            $count++;
        }
        $tmpTermQuery .= ") ";
        if($count > 0){
            if($connectType == self::INNER_JOIN){
                $connectTermQuery .=  $connectString.$tmpTermQuery;
                $connectFlag = true;
                if($innerJoinFlag){
                    $this->setTableList[$connetFromTableName] = $shortName;
                    if(($connetFromTableName === self::FILEDATA_TABLE && $request === self::REQUEST_ALL) || 
                       ($connetFromTableName === self::EXTERNAL_SEARCHWORD_TABLE && $request === self::REQUEST_ALL) || 
                       ($connetFromTableName === self::EXTERNAL_SEARCHWORD_TABLE && $request === self::REQUEST_META))
                    {
                        $connectQuery .= "LEFT JOIN ".$this->db_prefix.$connetFromTableName." AS $shortName ON ".
                                         $connectToTableName.".item_id = ".$shortName.".item_id ";
                    }
                    else
                    {
                    $connectQuery .= "INNER JOIN ".$this->db_prefix.$connetFromTableName." AS $shortName ON ".
                                         $connectToTableName.".item_id = ".$shortName.".item_id ";
                }
                }
            } else {
                $connectFlag = true;
                $connectTermQuery .=  $connectString ."(". $connectToTableName. ".item_id, ". $connectToTableName. ".item_no) IN (".
                                     " SELECT item_id, item_no ".
                                     " FROM ".$this->db_prefix.$connetFromTableName." AS $shortName ".
                                     " WHERE ".$tmpTermQuery.") ";
            }
            return true;
        }
        return false;
    }
    
    /**
     * create search date Query
     * 「日付」検索クエリ作成
     *
     * @param string $connectToTableName connect to table name 結合先テーブル名
     * @param string $connetFromTableName connect from table name 結合元テーブル名
     * @param string $shortName table short name テーブル短縮名
     * @param string $fromDate from date search value 日付範囲(from)
     * @param string $untilDate until date search value 日付範囲(until)
     * @param bool $onlyYear only year flag 範囲が1年以内に収まってるかのフラグ
     * @param string $andor "AND" or "OR" in where sentence WHERE句部分のクエリ結合時の条件タイプ式
     * @param string $connectQuery query クエリベース文
     * @param string $connectTermQuery where query クエリWHERE条件文
     * @param array $connectQueryParam query parameter クエリパラメータ
     *                                  array[$ii]
     * @param bool $connectFlag join flag 結合フラグ
     * @param string $connectType join type 結合タイプ
     * @return bool true/false success/failed 成功/失敗
     */
    private function createDateQuery($connectToTableName, $connetFromTableName, $shortName, $fromDate, $untilDate, $onlyYear, 
                                         $andor, &$connectQuery, &$connectTermQuery, &$connectQueryParam, &$connectFlag, $connectType)
    {
        // connect INNER JOIN
        if(strlen($fromDate) == 0 && strlen($untilDate) == 0 ){
            return false;
        }
        $fromDateList = preg_split("/[!-\/:-@\[-`{-~\s]/", $fromDate);
        $untilDateList = preg_split("/[!-\/:-@\[-`{-~\s]/", $untilDate);
        $fromDate = $this->validateDate($fromDateList, $onlyYear, true);
        $untilDate = $this->validateDate($untilDateList, $onlyYear, false);
        if($connectFlag){
            $connectTermQuery .= $andor." ";
        } else {
            $connectTermQuery .= "WHERE ";
            $connectFlag = true;
        }
        if(array_key_exists($connetFromTableName, $this->setTableList)){
            $shortName = $this->setTableList[$connetFromTableName];
        } else {
            $this->setTableList[$connetFromTableName] = $shortName;
            
            if($connectType == self::INNER_JOIN){
                $connectQuery .= "INNER JOIN ".$this->db_prefix.$connetFromTableName." AS $shortName ON ".
                                 $connectToTableName.".item_id = ".$shortName.".item_id ";
            } else {
                $connectTermQuery .= "(item_id, item_no) IN (".
                                     " SELECT item_id, item_no ".
                                     " FROM ".$this->db_prefix.$connetFromTableName." AS $shortName ".
                                     " WHERE ";
            }
        }
        if($fromDate == $untilDate){
            $connectTermQuery .= $shortName.".metadata = ? ";
            $connectQueryParam[] = $fromDate;
        } else {
            if(strlen($fromDate) > 0 && strlen($untilDate) > 0){
                $connectTermQuery .= $shortName.".metadata >= ? AND ".$shortName.".metadata <= ? ";
                $connectQueryParam[] = $fromDate;
                $connectQueryParam[] = $untilDate;
            } else if(strlen($fromDate) == 0 ){
                $connectTermQuery .= $shortName.".metadata <= ? ";
                $connectQueryParam[] = $untilDate;
            } else {
                $connectTermQuery .= $shortName.".metadata >= ? ";
                $connectQueryParam[] = $fromDate;
            }
        }
        
        if($connectType != self::INNER_JOIN){
            $connectTermQuery .= ") ";
        }
        return true;
    }
    
    /**
     * Validate date format
     * 日付文字列をバリデートする
     *
     * @param array $dateArray date array 日付配列
     *                          array[$ii]
     * @param bool $onlyYear only year flag 範囲が1年以内に収まってるかのフラグ
     * @param bool $isFrom day of start flag 月初フラグ
     * @return string date string 日付文字列
     */
    private function validateDate($dateArray, $onlyYear, $isFrom)
    {
        if($onlyYear){
            if(count($dateArray) > 0){
                if(strlen($dateArray[0]) > 4){
                    $date = substr($dateArray[0], 0, 4);
                } else if(strlen($dateArray[0]) != 0){
                    $date =  sprintf('%04d', $dateArray[0]);
                } else {
                    $date = "";
                }
            } else {
                $date = "";
            }
        } else {
            if(count($dateArray) >= 2){
                if(strlen($dateArray[0]) >= 4){
                    $date = substr($dateArray[0], 0, 4);
                } else {
                    $date =  sprintf('%04d', $dateArray[0]);
                }
                if(strlen($dateArray[1]) >= 2){
                    $date .= substr($dateArray[1], 0, 2);
                } else {
                    $date .=  sprintf('%02d', $dateArray[1]);
                }
                if(count($dateArray) >= 3){
                    if(strlen($dateArray[2]) >= 2){
                        $date .= substr($dateArray[2], 0, 2);
                    } else {
                        $date .=  sprintf('%02d', $dateArray[2]);
                    }
                } else {
                    if($isFrom){
                        $date .= "01";
                    } else {
                        $date .= "31";
                    }
                }
            } else if(count($dateArray) == 1){
                if(strlen($dateArray[0]) == 0){
                    $date = "";
                } else if(strlen($dateArray[0]) <= 4){
                    if($isFrom){
                        $date =  sprintf('%04d', $dateArray[0])."0101";
                    } else {
                        $date =  sprintf('%04d', $dateArray[0])."1231";
                    }
                } else if(strlen($dateArray[0]) <= 6){
                    if($isFrom){
                        $date = substr($dateArray[0], 0, 4).substr($dateArray[0], 4, 2)."01";
                    } else {
                        $date = substr($dateArray[0], 0, 4).substr($dateArray[0], 4, 2)."31";
                    }
                } else {
                    $date = substr($dateArray[0], 0, 4).substr($dateArray[0], 4, 2).substr($dateArray[0], 6, 2);
                }
            } else {
                $date = "";
            }
        }
        return $date;
    }
    
    /**
     * create search subject Query
     * 「件名・分類」検索クエリ作成
     *
     * @param string $connectToTableName connect to table name 結合先テーブル名
     * @param int $idString ID string ID文字列
     * @param string $searchValue search value 検索文字列
     * @param string $andor "AND" or "OR" in where sentence WHERE句部分のクエリ結合時の条件タイプ式
     * @param string $connectQuery query クエリベース文
     * @param string $connectTermQuery where query クエリWHERE条件文
     * @param array $connectQueryParam query parameter クエリパラメータ
     *                                  array[$ii]
     * @param bool $connectFlag join flag 結合フラグ
     * @param string $connectType join type 結合タイプ
     * @return bool true/false success/failed 成功/失敗
     */
    private function createSubjectQuery($connectToTableName, $idString, $searchValue,
                                         $andor, &$connectQuery, &$connectTermQuery, &$connectQueryParam, &$connectFlag, $connectType)
    {
        if(strlen($idString) == 0 || strlen($searchValue) == 0 ){
            return false;
        }
        $idList = preg_split("/[\s,']+/", $idString);
        if(count($idList) == 0){
            return false;
        }
        
        $tmpTermQuery = "";
        if($connectFlag){
            $tmpTermQuery .= $andor." ( ";
        } else {
            $tmpTermQuery .= "WHERE ( ";
            $connectFlag = true;
        }
        $subjectConnectFlag = false;
        $connectAndOr = "";
        for($ii = 0; $ii < count($idList); $ii++){
            switch($idList[$ii]){
                case self::NIISUBJECT_ID:
                    if($subjectConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::NIISUBJECT_TABLE, self::NIISUBJECT_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$subjectConnectFlag){
                        $subjectConnectFlag = $result;
                    }
                    break;
                case self::NDC_ID:
                    if($subjectConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::NDC_TABLE, self::NDC_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$subjectConnectFlag){
                        $subjectConnectFlag = $result;
                    }
                    break;
                case self::NDLC_ID:
                    if($subjectConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::NDLC_TABLE, self::NDLC_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$subjectConnectFlag){
                        $subjectConnectFlag = $result;
                    }
                    break;
                case self::BSH_ID:
                    if($subjectConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::BSH_TABLE, self::BSH_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$subjectConnectFlag){
                        $subjectConnectFlag = $result;
                    }
                    break;
                case self::NDLSH_ID:
                    if($subjectConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::NDLSH_TABLE, self::NDLSH_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$subjectConnectFlag){
                        $subjectConnectFlag = $result;
                    }
                    break;
                case self::MESH_ID:
                    if($subjectConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::MESH_TABLE, self::MESH_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$subjectConnectFlag){
                        $subjectConnectFlag = $result;
                    }
                    break;
                case self::DDC_ID:
                    if($subjectConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::DDC_TABLE, self::DDC_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$subjectConnectFlag){
                        $subjectConnectFlag = $result;
                    }
                    break;
                case self::LCC_ID:
                    if($subjectConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::LCC_TABLE, self::LCC_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$subjectConnectFlag){
                        $subjectConnectFlag = $result;
                    }
                    break;
                case self::UDC_ID:
                    if($subjectConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::UDC_TABLE, self::UDC_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$subjectConnectFlag){
                        $subjectConnectFlag = $result;
                    }
                    break;
                case self::LCSH_ID:
                    if($subjectConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::LCSH_TABLE, self::LCSH_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$subjectConnectFlag){
                        $subjectConnectFlag = $result;
                    }
                    break;
                default:
                    break;
            }
        }
        if($subjectConnectFlag){
            $connectTermQuery .= $tmpTermQuery." ) ";
            return true;
        }
        return false;
    }
        
    /**
     * create NIItype date Query
     * 「NIIタイプ」検索クエリ作成
     *
     * @param string $connectToTableName connect to table name 結合先テーブル名
     * @param string $connetFromTableName connect from table name 結合元テーブル名
     * @param string $shortName table short name テーブル短縮名
     * @param string $searchValue search value 検索文字列
     * @param string $andor "AND" or "OR" in where sentence WHERE句部分のクエリ結合時の条件タイプ式
     * @param string $connectQuery query クエリベース文
     * @param string $connectTermQuery where query クエリWHERE条件文
     * @param array $connectQueryParam query parameter クエリパラメータ
     *                                  array[$ii]
     * @param bool $connectFlag join flag 結合フラグ
     * @param string $connectType join type 結合タイプ
     * @return bool true/false success/failed 成功/失敗
     */
    private function createTypeQuery($connectToTableName, $connetFromTableName, $shortName, $searchValue, 
                                         $andor, &$connectQuery, &$connectTermQuery, &$connectQueryParam, &$connectFlag, $connectType)
    {
        // connect INNER JOIN
        if(strlen($searchValue) == 0){
            return false;
        }
        $searchStringList = preg_split("/[,']+/", $searchValue);
        if(count($searchStringList) == 0){
            return false;
        }
        $connectString  = "";
        if($connectFlag){
            $connectString .= $andor." ";
        } else {
            $connectString .= "WHERE ";
        }
        $tmpTermQuery  = "";
        $innerJoinFlag = true;
        if(array_key_exists($connetFromTableName, $this->setTableList)){
            $shortName = $this->setTableList[$connetFromTableName];
            $innerJoinFlag = false;
        } 
        $itemTypeShortName = self::ITEMTYPE_TABLE_SHORT_NAME;
        $itemTypeTableName = self::ITEMTYPE_TABLE;
        $innerJoinItemTypeFlag = true;
        if(array_key_exists($itemTypeTableName, $this->setTableList)){
            $itemTypeShortName = $this->setTableList[$itemTypeTableName];
            $innerJoinItemTypeFlag = false;
        } 
        $count = 0;
        $tmpTermQuery .= $itemTypeShortName.".mapping_info IN ( ";
        for($ii = 0; $ii < count($searchStringList); $ii++){
            if(strlen($searchStringList[$ii]) == 0){
                continue;
            }
            if($count > 0){
                $tmpTermQuery .= ", ";
            }
            $tmpTermQuery .= "? ";
            switch($searchStringList[$ii]){
                case 0:
                    $connectQueryParam[] = "Journal Article";
                    break;
                case 1:
                    $connectQueryParam[] = "Thesis or Dissertation";
                    break;
                case 2:
                    $connectQueryParam[] = "Departmental Bulletin Paper";
                    break;
                case 3:
                    $connectQueryParam[] = "Conference Paper";
                    break;
                case 4:
                    $connectQueryParam[] = "Presentation";
                    break;
                case 5:
                    $connectQueryParam[] = "Book";
                    break;
                case 6:
                    $connectQueryParam[] = "Technical Report";
                    break;
                case 7:
                    $connectQueryParam[] = "Research Paper";
                    break;
                case 8:
                    $connectQueryParam[] = "Article";
                    break;
                case 9:
                    $connectQueryParam[] = "Preprint";
                    break;
                case 10:
                    $connectQueryParam[] = "Learning Material";
                    break;
                case 11:
                    $connectQueryParam[] = "Data or Dataset";
                    break;
                case 12:
                    $connectQueryParam[] = "Software";
                    break;
                case 13:
                    $connectQueryParam[] = "Others";
                    break;
            }
            $count++;
        }
        if($count > 0){
            if($connectType == self::INNER_JOIN){
                $connectTermQuery .= $connectString.$tmpTermQuery .") ";
                $connectFlag = true;
                if($innerJoinFlag){
                    $this->setTableList[$connetFromTableName] = $shortName;
                    $connectQuery .= "INNER JOIN ".$this->db_prefix.$connetFromTableName." AS $shortName ON ".
                                     $connectToTableName.".item_id = ".$shortName.".item_id ";
                }
                if($innerJoinItemTypeFlag){
                    $this->setTableList[$itemTypeTableName] = $itemTypeShortName;
                    $connectQuery .= "INNER JOIN ".$this->db_prefix.$itemTypeTableName." AS $itemTypeShortName ON ".
                                     $shortName.".item_type_id = ".$itemTypeShortName.".item_type_id ";
                }
            } else {
                $connectFlag = true;
                $connectTermQuery .= $connectString."(item_id, item_no ) IN ( ".
                                     " SELECT item_id, item_no ".
                                     " FROM ".$this->db_prefix.$connetFromTableName." ".
                                     " WHERE (item_type_id) IN ( ".
                                     "  SELECT item_type_id ".
                                     "  FROM ".$this->db_prefix.$itemTypeTableName." AS $itemTypeShortName ".
                                     "  WHERE ".$tmpTermQuery .") ) ) ";
            }
            return true;
        }
        return false;
    }
    
    /**
     * create search ID Query
     * 「ID」検索クエリ作成
     *
     * @param string $connectToTableName connect to table name 結合先テーブル名
     * @param int $idString ID string ID文字列
     * @param string $searchValue search value 検索文字列
     * @param string $andor "AND" or "OR" in where sentence WHERE句部分のクエリ結合時の条件タイプ式
     * @param string $connectQuery query クエリベース文
     * @param string $connectTermQuery where query クエリWHERE条件文
     * @param array $connectQueryParam query parameter クエリパラメータ
     *                                  array[$ii]
     * @param bool $connectFlag join flag 結合フラグ
     * @param string $connectType join type 結合タイプ
     * @return bool true/false success/failed 成功/失敗
     */
    private function createIDQuery($connectToTableName, $idString, $searchValue,
                                         $andor, &$connectQuery, &$connectTermQuery, &$connectQueryParam, &$connectFlag, $connectType)
    {
    
        if(strlen($idString) == 0 || strlen($searchValue) == 0 ){
            return false;
        }
        $idList = preg_split("/[\s,']+/", $idString);
        if(count($idList) == 0){
            return false;
        }
        
        $tmpTermQuery = "";
        if($connectFlag){
            $tmpTermQuery .= $andor." ( ";
        } else {
            $tmpTermQuery .= "WHERE ( ";
            $connectFlag = true;
        }
        $idConnectFlag = false;
        $connectAndOr = "";
        for($ii = 0; $ii < count($idList); $ii++){
            switch($idList[$ii]){
                case self::IDENTIFER_ID:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::IDENTIFER_TABLE, self::IDENTIFER_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                case self::URI_ID:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::URI_TABLE, self::URI_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                case self::FULLTEXTURL_ID:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::FULLTEXTURL_TABLE, self::FULLTEXTURL_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                case self::SELFDOI_ID:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::SELFDOI_TABLE, self::SELFDOI_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                case self::ISBN_ID:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::ISBN_TABLE, self::ISBN_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                case self::ISSN_ID:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::ISSN_TABLE, self::ISSN_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                case self::NCID_ID:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::NCID_TABLE, self::NCID_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                case self::PMID_ID:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::PMID_TABLE, self::PMID_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                case self::DOI_ID:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::DOI_TABLE, self::DOI_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                case self::NAID_ID:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::NAID_TABLE, self::NAID_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                case self::ICHUSHI:
                    if($idConnectFlag){
                        $connectAndOr = "OR";
                    } else {
                        $connectAndOr = "";
                    }
                    $result = $this->createFullTextQuery(self::SORT_TABLE_SHORT_NAME, self::ICHUSHI_TABLE, self::ICHUSHI_TABLE_SHORT_NAME, $searchValue, 
                                               $connectAndOr, "AND", $connectQuery, $tmpTermQuery, $connectQueryParam, $connectFlag, $connectType);
                    if(!$idConnectFlag){
                        $idConnectFlag = $result;
                    }
                    break;
                default:
                    break;
            }
        }
        if($idConnectFlag){
            $connectTermQuery .= $tmpTermQuery." ) ";
            
            return true;
        }
        return false;
    }

    /**
     * create Query
     * IN句の検索クエリ作成
     *
     * @param string $connectToTableName connect to table name 結合先テーブル名
     * @param string $connetFromTableName connect from table name 結合元テーブル名
     * @param string $shortName table short name テーブル短縮名
     * @param string $searchValue search value 検索文字列
     * @param string $columnName column name カラム名
     * @param string $andor "AND" or "OR" in where sentence WHERE句部分のクエリ結合時の条件タイプ式
     * @param string $connectQuery query クエリベース文
     * @param string $connectTermQuery where query クエリWHERE条件文
     * @param array $connectQueryParam query parameter クエリパラメータ
     *                                  array[$ii]
     * @param bool $connectFlag join flag 結合フラグ
     * @param string $connectType join type 結合タイプ
     * @return bool true/false success/failed 成功/失敗
     */
    private function createINSearchColumnQuery($connectToTableName, $connetFromTableName, $shortName, $searchValue, $columnName,
                                         $andor, &$connectQuery, &$connectTermQuery, &$connectQueryParam, &$connectFlag, $connectType)
    {
        // connect INNER JOIN
        if(strlen($searchValue) == 0){
            return false;
        }
        $searchStringList = preg_split("/[\s,']+/", $searchValue);
        if(count($searchStringList) == 0){
            return false;
        }
        $connectString = "";
        if($connectFlag){
            $connectString .= $andor." ";
        } else {
            $connectString .= "WHERE ";
        }
        $tmpTermQuery = "";
        $innerJoinFlag = true;
        if(array_key_exists($connetFromTableName, $this->setTableList)){
            $shortName = $this->setTableList[$connetFromTableName];
            $innerJoinFlag = false;
        } 
        $count = 0;
        $tmpTermQuery .= $shortName.".".$columnName." IN ( ";
        for($ii = 0; $ii < count($searchStringList); $ii++){
            if(strlen($searchStringList[$ii]) == 0){
                continue;
            }
            if($count > 0){
                $tmpTermQuery .= ", ";
            }
            $tmpTermQuery .= "? ";
            $connectQueryParam[] = $searchStringList[$ii];
            $count++;
        }
        if($count > 0){
        
            if($connectType == self::INNER_JOIN){
                $connectTermQuery .= $connectString.$tmpTermQuery .") ";
                $connectFlag = true;
                if($innerJoinFlag){
                    $this->setTableList[$connetFromTableName] = $shortName;
                    $connectQuery .= "INNER JOIN ".$this->db_prefix.$connetFromTableName." AS $shortName ON ".
                                     $connectToTableName.".item_id = ".$shortName.".item_id ";
                    $connectTermQuery .= " AND ".$shortName.".is_delete = 0 ";
                }
            } else {
                $connectFlag = true;
                $connectTermQuery .= $connectString."(item_id, item_no) IN (".
                                     " SELECT item_id, item_no ".
                                     " FROM ".$this->db_prefix.$connetFromTableName." AS $shortName ".
                                     " WHERE ".$tmpTermQuery." ) AND ".$shortName.".is_delete = 0 ) ";
            }
            return true;
        }
        return false;
    }
}
?>