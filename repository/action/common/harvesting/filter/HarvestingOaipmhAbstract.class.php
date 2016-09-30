<?php

/**
 * Harvest item registration base class
 * ハーベストアイテム登録基底クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: HarvestingOaipmhAbstract.class.php 35791 2014-05-16 04:10:29Z rei_matsuura $
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
 * String format conversion common classes
 * 文字列形式変換共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryOutputFilter.class.php';

/**
 * Harvest item registration base class
 * ハーベストアイテム登録基底クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class HarvestingOaipmhAbstract extends RepositoryAction
{
    // ---------------------------------------------
    // Const
    // ---------------------------------------------
    // Itemtype data
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_LINK = RepositoryConst::ITEM_ATTR_TYPE_LINK;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_TEXT = RepositoryConst::ITEM_ATTR_TYPE_TEXT;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_BIBLIOINFO = RepositoryConst::ITEM_ATTR_TYPE_BIBLIOINFO;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_SELECT = RepositoryConst::ITEM_ATTR_TYPE_SELECT;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_TEXTAREA = RepositoryConst::ITEM_ATTR_TYPE_TEXTAREA;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_NAME = RepositoryConst::ITEM_ATTR_TYPE_NAME;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_DATE = RepositoryConst::ITEM_ATTR_TYPE_DATE;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_CHECKBOX = RepositoryConst::ITEM_ATTR_TYPE_CHECKBOX;
    
    // Error / Warning message
    /**
     * Error message
     * エラーメッセージ
     * 
     * @var string
     */
    const MSG_ER_GET_TITLE = "repository_harvesting_error_get_title";
    /**
     * Warning message
     * 警告メッセージ
     * 
     * @var string
     */
    const MSG_WN_MISS_LANGAGE = "repository_harvesting_warning_miss_language";
    
    // Log status
    /**
     * Status
     * 状態
     * 
     * @var int
     */
    const LOG_STATUS_OK = RepositoryConst::HARVESTING_LOG_STATUS_OK;
    /**
     * Status
     * 状態
     * 
     * @var int
     */
    const LOG_STATUS_WARNING = RepositoryConst::HARVESTING_LOG_STATUS_WARNING;
    /**
     * Status
     * 状態
     * 
     * @var int
     */
    const LOG_STATUS_ERROR = RepositoryConst::HARVESTING_LOG_STATUS_ERROR;
    
    // Metadata array for ItemRegister
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_IR_BASIC = "irBasic";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_IR_METADATA = "irMetadata";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ITEM_ID = "item_id";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ITEM_NO = "item_no";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ITEM_TYPE_ID = "item_type_id";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_TITLE = "title";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_TITLE_EN = "title_english";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_LANGUAGE = "language";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_PUB_YEAR = "pub_year";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_PUB_MONTH = "pub_month";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_PUB_DAY = "pub_day";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_SEARCH_KEY = "serch_key";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_SEARCH_KEY_EN = "serch_key_english";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ATTR_ID = "attribute_id";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ATTR_NO = "attribute_no";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_INPUT_TYPE = "input_type";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ATTR_VALUE = "attribute_value";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_FAMILY = "family";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_NAME = "name";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_FAMILY_RUBY = "family_ruby";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_NAME_RUBY = "name_ruby";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_EMAIL = "e_mail_address";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_AUTHOR_ID = "author_id";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_NAME_NO = "personal_name_no";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_BIBLIO_NAME = "biblio_name";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_BIBLIO_NAME_EN = "biblio_name_english";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_VOLUME = "volume";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ISSUE = "issue";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_SPAGE = "start_page";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_EPAGE = "end_page";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_DATE_OF_ISSUED = "date_of_issued";
    
    // Others
    /**
     * Delimiter
     * 区切り文字
     * 
     * @var string
     */
    const IDENTIFIER_DELIMITER = ":";
    /**
     * Delimiter
     * 区切り文字
     * 
     * @var string
     */
    const TAXON_DELIMITER = ":";
    /**
     * Delimiter
     * 区切り文字
     * 
     * @var string
     */
    const NAME_DELIMITER = " ";
    /**
     * Language
     * 言語
     * 
     * @var string
     */
    const ITEM_LANG_JA = RepositoryConst::ITEM_LANG_JA;
    /**
     * Language
     * 言語
     * 
     * @var string
     */
    const ITEM_LANG_EN = RepositoryConst::ITEM_LANG_EN;
    /**
     * Language
     * 言語
     * 
     * @var string
     */
    const DEFAULT_LANGUAGE = RepositoryConst::ITEM_LANG_JA;
    
    // Database
    /**
     * Table name
     * テーブル名
     * 
     * @var string
     */
    const DB_ITEM = RepositoryConst::DBTABLE_REPOSITORY_ITEM;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ITEM_ID = RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ITEM_NO = RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_NO;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_IS_DELETE = RepositoryConst::DBCOL_COMMON_IS_DELETE;
    /**
     * Table name
     * テーブル名
     * 
     * @var string
     */
    const DB_ITEM_ATTR = RepositoryConst::DBTABLE_REPOSITORY_ITEM_ATTR;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ITEM_ID = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ITEM_NO = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_NO;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ATTR_ID = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ATTR_NO = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_NO;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ATTR_VAL = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_VALUE;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ITEM_TYPE_ID = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_TYPE_ID;
    /**
     * Table name
     * テーブル名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME = RepositoryConst::DBTABLE_REPOSITORY_PERSONAL_NAME;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_ITEM_ID = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_ITEM_NO = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_NO;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_ATTR_ID = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ATTRIBUTE_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_NAME_NO = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_PERSONAL_NAME_NO;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_FAMILY = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_FAMILY;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_NAME = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_NAME;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_FAMILY_RUBY = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_FAMILY_RUBY;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_NAME_RUBY = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_NAME_RUBY;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_EMAIL_ADDRES = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_E_MAIL_ADDRESS;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_ITEM_TYPE_ID = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_TYPE_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_AUTHOR_ID = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_AUTHOR_ID;
    
    /**
     * Item type id
     * アイテムタイプID
     * 
     * @var int
     */
    private $itemtype_id = 0;
    /**
     * Repository id
     * リポジトリID
     * 
     * @var int
     */
    private $attr_id_repository_id = 0;
    /**
     * Identifier
     * 識別子
     * 
     * @var int
     */
    private $attr_id_identifier = 0;
    /**
     * Datestamp
     * Datestamp
     * 
     * @var int
     */
    private $attr_id_datestamp = 0;
    /**
     * Maximum attribute id
     * 最大属性ID
     * 
     * @var int
     */
    private $max_attr_id = 0;
    
    // ---------------------------------------------
    // Constructor
    // ---------------------------------------------
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $Session Session object セッション管理オブジェクト
     * @param DbObject $Db Database object データベース管理オブジェクト
     */
    public function __construct($Session, $Db){
        $this->Session = $Session;
        $this->Db = $Db;
    }
    
    /**
     * set itemtype id
     * アイテムタイプID設定
     *
     * @param int $itemtypeId Itemtype id アイテムタイプID
     */
    protected function setItemtypeId($itemtypeId){
        $this->itemtype_id = $itemtypeId;
    }
    
    /**
     * set param
     * パラメータ設定
     *
     * @param int $attr_repositoryId Repository id リポジトリID
     * @param string $attr_identifier Identifier 識別子
     * @param string $attr_datestamp date stamp Date stamp
     * @param int $max_attr_id maximum attribute id 最大属性ID
     */
    protected function setRequiredParam($attr_repositoryId, $attr_identifier, $attr_datestamp, $max_attr_id){
        $this->attr_id_repository_id = $attr_repositoryId;
        $this->attr_id_identifier = $attr_identifier;
        $this->attr_id_datestamp = $attr_datestamp;
        $this->max_attr_id = $max_attr_id;
    }
    
    // ---------------------------------------------
    // Private method
    // ---------------------------------------------
    
    /**
     * Explode name string
     * 名前分割
     *
     * @param string $str String 文字列
     * @return array["family"|"name"] Name 名前
     */
    protected function explodeNameStr($str)
    {
        $family = "";
        $name = "";
        
        $str = str_replace("　", " ", $str);
        $str = preg_replace("/ +/", " ", $str);
        $str = preg_replace("/ +/", " ", $str);
        
        $nameArray = explode(self::NAME_DELIMITER, $str, 2);
        $family = $nameArray[0];
        if(isset($nameArray[1]))
        {
            $name = $nameArray[1];
        }
        
        return array(self::KEY_FAMILY => $family, self::KEY_NAME => $name);
    }
    
    /**
     * Init irBasic array
     * アイテム基本情報初期化
     *
     * @return array Basic item information list アイテム基本情報
     *               array["item_id"|"item_no"|"item_type_id"|"title"|"title_english"|"language"|"pub_year"|"pub_month"|"pub_day"|"serch_key"|"serch_key_english"]
     */
    protected function initIrBasic()
    {
        $irBasic = array(
                self::KEY_ITEM_ID => "",
                self::KEY_ITEM_NO => "",
                self::KEY_ITEM_TYPE_ID => $this->itemtype_id,
                self::KEY_TITLE => "",
                self::KEY_TITLE_EN => "",
                self::KEY_LANGUAGE => "",
                self::KEY_PUB_YEAR => "",
                self::KEY_PUB_MONTH => "",
                self::KEY_PUB_DAY => "",
                self::KEY_SEARCH_KEY => "",
                self::KEY_SEARCH_KEY_EN => ""
            );
        return $irBasic;
    }
    
    /**
     * Create irMetadata array
     * メタデータ一覧作成
     *
     * @param int $attrId Attribute id 属性ID
     * @param string $inputType Input type 入力タイプ
     * @param string $data Value 値
     * @return array Metadata list メタデータ一覧
     *               array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    protected function createIrMetadata($attrId, $inputType, $data)
    {
        $irMetadata = array();
        switch($inputType)
        {
            case self::INPUT_TYPE_LINK:
            case self::INPUT_TYPE_TEXT:
            case self::INPUT_TYPE_SELECT:
            case self::INPUT_TYPE_TEXTAREA:
            case self::INPUT_TYPE_CHECKBOX:
            case self::INPUT_TYPE_DATE:
                $irMetadata = $this->initIrMetadata();
                $irMetadata[self::KEY_ATTR_ID] = $attrId;
                $irMetadata[self::KEY_ATTR_NO] = $this->cntMetadata[$attrId];
                $irMetadata[self::KEY_INPUT_TYPE] = $inputType;
                if(isset($data[self::KEY_ATTR_VALUE]))
                    $irMetadata[self::KEY_ATTR_VALUE] = $data[self::KEY_ATTR_VALUE];
                break;
            case self::INPUT_TYPE_BIBLIOINFO:
                $irMetadata = $this->initIrBiblioMetadata();
                $irMetadata[self::KEY_ATTR_ID] = $attrId;
                $irMetadata[self::KEY_BIBLIO_NO] = $this->cntMetadata[$attrId];
                if(isset($data[self::KEY_BIBLIO_NAME]))
                    $irMetadata[self::KEY_BIBLIO_NAME] = $data[self::KEY_BIBLIO_NAME];
                if(isset($data[self::KEY_BIBLIO_NAME_EN]))
                    $irMetadata[self::KEY_BIBLIO_NAME_EN] = $data[self::KEY_BIBLIO_NAME_EN];
                if(isset($data[self::KEY_VOLUME]))
                    $irMetadata[self::KEY_VOLUME] = $data[self::KEY_VOLUME];
                if(isset($data[self::KEY_ISSUE]))
                    $irMetadata[self::KEY_ISSUE] = $data[self::KEY_ISSUE];
                if(isset($data[self::KEY_SPAGE]))
                    $irMetadata[self::KEY_SPAGE] = $data[self::KEY_SPAGE];
                if(isset($data[self::KEY_EPAGE]))
                    $irMetadata[self::KEY_EPAGE] = $data[self::KEY_EPAGE];
                if(isset($data[self::KEY_DATE_OF_ISSUED]))
                    $irMetadata[self::KEY_DATE_OF_ISSUED] = $data[self::KEY_DATE_OF_ISSUED];
                break;
            case self::INPUT_TYPE_NAME:
                $irMetadata = $this->initIrNameMetadata();
                $irMetadata[self::KEY_ATTR_ID] = $attrId;
                $irMetadata[self::KEY_NAME_NO] = $this->cntMetadata[$attrId];
                if(isset($data[self::KEY_FAMILY]))
                    $irMetadata[self::KEY_FAMILY] = $data[self::KEY_FAMILY];
                if(isset($data[self::KEY_NAME]))
                    $irMetadata[self::KEY_NAME] = $data[self::KEY_NAME];
                if(isset($data[self::KEY_FAMILY_RUBY]))
                    $irMetadata[self::KEY_FAMILY_RUBY] = $data[self::KEY_FAMILY_RUBY];
                if(isset($data[self::KEY_NAME_RUBY]))
                    $irMetadata[self::KEY_NAME_RUBY] = $data[self::KEY_NAME_RUBY];
                if(isset($data[self::KEY_EMAIL]))
                    $irMetadata[self::KEY_EMAIL] = $data[self::KEY_EMAIL];
                if(isset($data[self::KEY_AUTHOR_ID]))
                    $irMetadata[self::KEY_AUTHOR_ID] = $data[self::KEY_AUTHOR_ID];
                if(isset($data[self::KEY_LANGUAGE]))
                    $irMetadata[self::KEY_LANGUAGE] = $data[self::KEY_LANGUAGE];
                break;
            default:
                break;
        }
        return $irMetadata;
    }
    
    /**
     * Init irMetadata array
     * メタデータ一覧初期化
     *
     * @return array Metadata list メタデータ一覧
     *               array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function initIrMetadata()
    {
        $irMetadata = array(
                self::KEY_ITEM_ID => "",
                self::KEY_ITEM_NO => "",
                self::KEY_ITEM_TYPE_ID => $this->itemtype_id,
                self::KEY_ATTR_ID => "",
                self::KEY_ATTR_NO => "",
                self::KEY_INPUT_TYPE => "",
                self::KEY_ATTR_VALUE => ""
            );
        return $irMetadata;
    }
    
    /**
     * Init irMetadata array for name
     * メタデータ一覧初期化
     *
     * @return array Metadata list メタデータ一覧
     *               array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"name_no"|"input_type"|"family"|"name"|"family_ruby"|"name_ruby"|"email"|"author_id"|"language"]
     */
    private function initIrNameMetadata()
    {
        $irMetadata = array(
                self::KEY_ITEM_ID => "",
                self::KEY_ITEM_NO => "",
                self::KEY_ITEM_TYPE_ID => $this->itemtype_id,
                self::KEY_ATTR_ID => "",
                self::KEY_FAMILY => "",
                self::KEY_NAME => "",
                self::KEY_FAMILY_RUBY => "",
                self::KEY_NAME_RUBY => "",
                self::KEY_EMAIL => "",
                self::KEY_AUTHOR_ID => "",
                self::KEY_LANGUAGE => "",
                self::KEY_INPUT_TYPE => self::INPUT_TYPE_NAME,
                self::KEY_NAME_NO => ""
            );
        return $irMetadata;
    }
    
    /**
     * Init irMetadata array for biblio_info
     * メタデータ一覧初期化
     *
     * @return array Metadata list メタデータ一覧
     *               array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"biblio_name"|"biblio_name_en"|"volume"|"issue"|"spage"|"epage"|"date_of_issued"|"biblio_no"]
     */
    private function initIrBiblioMetadata()
    {
        $irMetadata = array(
                self::KEY_ITEM_ID => "",
                self::KEY_ITEM_NO => "",
                self::KEY_ITEM_TYPE_ID => $this->itemtype_id,
                self::KEY_ATTR_ID => "",
                self::KEY_BIBLIO_NAME => "",
                self::KEY_BIBLIO_NAME_EN => "",
                self::KEY_VOLUME => "",
                self::KEY_ISSUE => "",
                self::KEY_SPAGE => "",
                self::KEY_EPAGE => "",
                self::KEY_DATE_OF_ISSUED => "",
                self::KEY_INPUT_TYPE => self::INPUT_TYPE_BIBLIOINFO,
                self::KEY_BIBLIO_NO => ""
            );
        return $irMetadata;
    }
    
    /**
     * Set require metadata to array
     * 必須オプション設定
     *
     * @param int $repositoryId Repository id リポジトリID
     * @param string $metadata Metadata メタデータ
     *                         array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    protected function setRequireMetadataToArray($repositoryId, &$metadata)
    {
        // repositoryId
        $attrId = $this->attr_id_repository_id;
        $this->cntMetadata[$attrId]++;
        $data = array(self::KEY_ATTR_VALUE => $repositoryId);
        $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
        if(count($irMetadata)>0)
        {
            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
        }
        
        // identifier
        $attrId = $this->attr_id_identifier;
        $this->cntMetadata[$attrId]++;
        $data = array(self::KEY_ATTR_VALUE => $metadata[RepositoryConst::HARVESTING_COL_HEADERIDENTIFIER][0]["value"]);
        $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
        if(count($irMetadata)>0)
        {
            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
        }
        
        // datestamp
        $attrId = $this->attr_id_datestamp;
        $this->cntMetadata[$attrId]++;
        $data = array(self::KEY_ATTR_VALUE => $metadata[RepositoryConst::HARVESTING_COL_DATESTAMP][0]["value"]);
        $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
        if(count($irMetadata)>0)
        {
            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
        }
    }
    
    // ---------------------------------------------
    // Public method
    // ---------------------------------------------
    /**
     * Set TransStartDate
     * トランザクション開始日時設定
     *
     * @param string $transStartDate Transaction start date トランザクション開始日時
     */
    public function setTransStartDate($transStartDate)
    {
        $this->TransStartDate = $transStartDate;
    }
    
    /**
     * Get metadata array from ListRecords(record)
     * メタデータ取得
     *
     * @param string $metadataXml Metadata XML XML文字列
     * @param int $repositoryId Repository id リポジトリID
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     * @return boolean Result 結果
     */
    public function setMetadataFromListRecords($metadataXml, $repositoryId, &$metadata)
    {
        // over ride
    }
    
    /**
     * Check metadata
     * メタデータチェック
     *
     * @param array $metadata Metadata メタデータ
     *                        array["HEARDER"][0]["attributes"]["STATUS"]
     *                        array["TITLE"|"LANGUAGE"|"URI"|"NIITYPE"]
     * @param int $logStatus Status 状態
     * @param array $logMsg Log message ログメッセージ
     *                      array[$ii]
     * @return boolean Result 結果
     */
    public function checkMetadata($metadata, &$logStatus, &$logMsg)
    {
        // title
        $title = $metadata[self::KEY_IR_BASIC][self::KEY_TITLE];
        $titleEn = $metadata[self::KEY_IR_BASIC][self::KEY_TITLE_EN];
        if(strlen($title)==0 && strlen($titleEn)==0)
        {
            array_push($logMsg, self::MSG_ER_GET_TITLE);
            $logStatus = self::LOG_STATUS_ERROR;
            return false;
        }
        
        // language
        $language = RepositoryOutputFilter::language($metadata[self::KEY_IR_BASIC][self::KEY_LANGUAGE]);
        if(strlen($language)==0)
        {
            array_push($logMsg, self::MSG_WN_MISS_LANGAGE);
            $logStatus = self::LOG_STATUS_WARNING;
        }
        
        return true;
    }
    
    /**
     * Check item exists
     * アイテム存在確認
     * 
     * @param array $metadata Metadata メタデータ
     *                        array[KEYNAME]
     * @param int $repositoryId Repository id リポジトリID
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param string $datestamp Date stamp Date stamp
     * @param string $isDelete Is delete 削除済みか
     * @return bool true: Exists / false: No exists
     */
    public function isItemExists($metadata, $repositoryId, &$itemId, &$itemNo, &$datestamp, &$isDelete)
    {
        // Init
        $itemId = "";
        $itemNo = "";
        $datestamp = "";
        $isDelete = "";
        $query = "SELECT DISTINCT ".self::DB_ITEM_ATTR_ITEM_ID.", ".self::DB_ITEM_ATTR_ITEM_NO." ".
                 "FROM ".DATABASE_PREFIX.self::DB_ITEM_ATTR." ".
                 "WHERE ".self::DB_ITEM_ATTR_ATTR_ID." = ? ".
                 "AND ".self::DB_ITEM_ATTR_ATTR_NO." = 1 ".
                 "AND ".self::DB_ITEM_ATTR_ATTR_VAL." = ? ".
                 "AND ".self::DB_ITEM_ATTR_ITEM_ID." IN (".
                 "  SELECT DISTINCT ".self::DB_ITEM_ATTR_ITEM_ID." ".
                 "  FROM ".DATABASE_PREFIX.self::DB_ITEM_ATTR." ".
                 "  WHERE ".self::DB_ITEM_ATTR_ATTR_ID." = ? ".
                 "  AND ".self::DB_ITEM_ATTR_ATTR_NO." = 1 ".
                 "  AND ".self::DB_ITEM_ATTR_ATTR_VAL." = ? ".
                 "  AND ".self::DB_ITEM_ATTR_ITEM_TYPE_ID." = ?);";
        $params = array();
        $params[] = $this->attr_id_identifier; //attribute_id / Identifier
        $params[] = $metadata[RepositoryConst::HARVESTING_COL_HEADERIDENTIFIER][0]["value"];  //attribute_value / Itentifier
        $params[] = $this->attr_id_repository_id;    //attribute_id / repositoryId
        $params[] = $repositoryId;    //attribute_value / repositoryId
        $params[] = $this->itemtype_id;  //item_type_id
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        
        if(count($result) == 0)
        {
            // Not exists
            return false;
        }
        else
        {
            $itemId = $result[0][self::DB_ITEM_ATTR_ITEM_ID];
            $itemNo = $result[0][self::DB_ITEM_ATTR_ITEM_NO];
            
            // Exists
            $query = "SELECT ".self::DB_ITEM_ATTR_ATTR_VAL." ".
                     "FROM ".DATABASE_PREFIX.self::DB_ITEM_ATTR." ".
                     "WHERE ".self::DB_ITEM_ATTR_ITEM_ID." = ? ".
                     "AND ".self::DB_ITEM_ATTR_ITEM_NO." = ? ".
                     "AND ".self::DB_ITEM_ATTR_ATTR_ID." = ? ".
                     "AND ".self::DB_ITEM_ATTR_ITEM_NO." = 1 ;";
            $params = array();
            $params[] = $itemId;
            $params[] = $itemNo;
            $params[] = $this->attr_id_datestamp; //attribute_id / datestamp
            $result = $this->Db->execute($query, $params);
            if(count($result) > 0)
            {
                $datestamp = $result[0][self::DB_ITEM_ATTR_ATTR_VAL];
            }
            
            // Get repository_item table's is_delete
            $query = "SELECT ".self::DB_ITEM_IS_DELETE." ".
                     "FROM ".DATABASE_PREFIX.self::DB_ITEM." ".
                     "WHERE ".self::DB_ITEM_ITEM_ID." = ? ".
                     "AND ".self::DB_ITEM_ITEM_NO." = ? ;";
            $params = array();
            $params[] = $itemId;
            $params[] = $itemNo;
            $result = $this->Db->execute($query, $params);
            if(count($result) > 0)
            {
                $isDelete = intval($result[0][self::DB_ITEM_IS_DELETE]);
            }
            
            return true;
        }
    }
    
    /**
     * Set item_id and item_no to irBasic and irMetadata
     * アイテムID、アイテム通番設定
     *
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param array $metadata Metadata メタデータ
     *                        array[KEYNAME]
     * @param array $irBasic Basic item information list アイテム基本情報
     *                       array["item_id"|"item_no"|"item_type_id"|"title"|"title_english"|"language"|"pub_year"|"pub_month"|"pub_day"|"serch_key"|"serch_key_english"]
     * @param array $irMetadata Metadata list メタデータ一覧
     *                          array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     * @return boolean Result 結果
     */
    public function setItemIdForIrData($itemId, $itemNo, &$metadata, &$irBasic, &$irMetadataArray)
    {
        // Check param
        $itemId = intval($itemId);
        $itemNo = intval($itemNo);
        if($itemId<1 || $itemNo<1)
        {
            return false;
        }
        
        // Set item_id and item_no
        $metadata[self::KEY_IR_BASIC][self::KEY_ITEM_ID] = $itemId;
        $metadata[self::KEY_IR_BASIC][self::KEY_ITEM_NO] = $itemNo;
        if(strlen($metadata[self::KEY_IR_BASIC][self::KEY_LANGUAGE])==0)
        {
            $metadata[self::KEY_IR_BASIC][self::KEY_LANGUAGE] = self::DEFAULT_LANGUAGE;
        }
        
        for($ii=0; $ii<count($metadata[self::KEY_IR_METADATA]); $ii++)
        {
            $metadata[self::KEY_IR_METADATA][$ii][self::KEY_ITEM_ID] = $itemId;
            $metadata[self::KEY_IR_METADATA][$ii][self::KEY_ITEM_NO] = $itemNo;
            
            if($metadata[self::KEY_IR_METADATA][$ii][self::KEY_INPUT_TYPE] == self::INPUT_TYPE_NAME)
            {
                // Set author ID
                $attrId = $metadata[self::KEY_IR_METADATA][$ii][self::KEY_ATTR_ID];
                $nameNo = $metadata[self::KEY_IR_METADATA][$ii][self::KEY_NAME_NO];
                $family = $metadata[self::KEY_IR_METADATA][$ii][self::KEY_FAMILY];
                $name = $metadata[self::KEY_IR_METADATA][$ii][self::KEY_NAME];
                $authorId = $this->getAuthorIdForIrMetadata($itemId, $itemNo, $attrId, $nameNo, $family, $name);
                $metadata[self::KEY_IR_METADATA][$ii][self::KEY_AUTHOR_ID] = $authorId;
            }
        }
        
        $irBasic = $metadata[self::KEY_IR_BASIC];
        $irMetadataArray = $metadata[self::KEY_IR_METADATA];
        
        // Set additional metadata
        $this->setAdditionalMetadata($itemId, $itemNo, $irMetadataArray);
        
        return true;
    }
    
    /**
     * Get author id
     * 著者ID取得
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param int $attrId Attribute id 属性ID
     * @param int $nameNo Name number 名前通番
     * @param int $family Family 姓
     * @param int $name Name 名
     * @return int Author id 著者ID
     */
    private function getAuthorIdForIrMetadata($itemId, $itemNo, $attrId, $nameNo, $family, $name)
    {
        // Get author ID from database
        $query = "SELECT ".self::DB_PERSONAL_NAME_FAMILY.", ".self::DB_PERSONAL_NAME_NAME.", ".self::DB_PERSONAL_NAME_AUTHOR_ID." ".
                 "FROM ".DATABASE_PREFIX.self::DB_PERSONAL_NAME." ".
                 "WHERE ".self::DB_PERSONAL_NAME_ITEM_ID." = ? ".
                 "AND ".self::DB_PERSONAL_NAME_ITEM_NO." = ? ".
                 "AND ".self::DB_PERSONAL_NAME_ATTR_ID." = ? ".
                 "AND ".self::DB_PERSONAL_NAME_NAME_NO." = ? ;";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attrId;
        $params[] = $nameNo;
        $result = $this->Db->execute($query, $params);
        if($result===false || count($result)==0)
        {
            return 0;
        }
        
        // Check author ID
        $authorId = 0;
        if($result[0][self::DB_PERSONAL_NAME_FAMILY] == $family && $result[0][self::DB_PERSONAL_NAME_NAME] == $name)
        {
            $authorId = intval($result[0][self::DB_PERSONAL_NAME_AUTHOR_ID]);
        }
        
        return $authorId;
    }
    
    /**
     * Set additional metadata
     * 追加メタデータ設定
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param array $metadata Metadata list メタデータ一覧
     *                        array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     * @return boolean Result 結果
     */
    private function setAdditionalMetadata($itemId, $itemNo, &$metadataArray)
    {
        $itemTypeId = $this->itemtype_id;
        $startAddAttrId = $this->max_attr_id;
        
        // Get itemAttrType
        $query = "SELECT * ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_ITEM_ATTR_TYPE." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ITEM_TYPE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_ID." >= ? ".
                 "AND ".RepositoryConst::DBCOL_COMMON_IS_DELETE." = 0 ".
                 "ORDER BY ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_ID." ASC;";
        $params = array();
        $params[] = $itemTypeId;
        $params[] = $startAddAttrId;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        
        foreach($result as $itemAttrType)
        {
            $inputType = $itemAttrType[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IMPUT_TYPE];
            $attrId = $itemAttrType[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_ID];
            if($inputType == RepositoryConst::ITEM_ATTR_TYPE_BIBLIOINFO)
            {
                $this->setAdditionalBiblioInfo($itemId, $itemNo, $attrId, $itemTypeId, $metadataArray);
            }
            else if($inputType == RepositoryConst::ITEM_ATTR_TYPE_FILE || $inputType == RepositoryConst::ITEM_ATTR_TYPE_FILEPRICE)
            {
                // no update
                continue;
            }
            else if($inputType == RepositoryConst::ITEM_ATTR_TYPE_NAME)
            {
                $language = $itemAttrType[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];
                $this->setAdditionalName($itemId, $itemNo, $attrId, $itemTypeId, $language, $metadataArray);
            }
            else if($inputType == RepositoryConst::ITEM_ATTR_TYPE_THUMBNAIL)
            {
                // no update
                continue;
            }
            else
            {
                $this->setAdditionalAttribute($itemId, $itemNo, $attrId, $itemTypeId, $inputType, $metadataArray);
            }
        }
        
        return true;
    }
    
    /**
     * Set additional biblioInfo
     * 追加メタデータ設定
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param int $attrId Attribute id 属性ID
     * @param int $itemTypeId Item type id アイテムタイプID
     * @param array $metadataArray Metadata list メタデータ一覧
     *                             array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     * @return boolean Result 結果
     */
    private function setAdditionalBiblioInfo($itemId, $itemNo, $attrId, $itemTypeId, &$metadataArray)
    {
        // Get BiblioInfo
        $query = "SELECT * ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_BIBLIO_INFO." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_BIBLIO_INFO_ITEM_TYPE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_BIBLIO_INFO_ITEM_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_BIBLIO_INFO_ITEM_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_BIBLIO_INFO_ATTRIBUTE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_COMMON_IS_DELETE." = 0 ".
                 "ORDER BY ".RepositoryConst::DBCOL_REPOSITORY_BIBLIO_INFO_BIBLIO_NO." ASC;";
        $params = array();
        $params[] = $itemTypeId;
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attrId;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        
        foreach($result as $biblioInfo)
        {
            $biblioInfo["input_type"] = RepositoryConst::ITEM_ATTR_TYPE_BIBLIOINFO;
            array_push($metadataArray, $biblioInfo);
        }
    }
    
    /**
     * Set additional name
     * 追加メタデータ設定
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param int $attrId Attribute id 属性ID
     * @param int $itemTypeId Item type id アイテムタイプID
     * @param string $language Language 言語
     * @param array $metadataArray Metadata list メタデータ一覧
     *                             array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     * @return boolean Result 結果
     */
    private function setAdditionalName($itemId, $itemNo, $attrId, $itemTypeId, $language, &$metadataArray)
    {
        // Get personalName
        $query = "SELECT * ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_PERSONAL_NAME." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_TYPE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ATTRIBUTE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_COMMON_IS_DELETE." = 0 ".
                 "ORDER BY ".RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_PERSONAL_NAME_NO." ASC;";
        $params = array();
        $params[] = $itemTypeId;
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attrId;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        
        foreach($result as $personalName)
        {
            $personalName["language"] = $language;
            $personalName["input_type"] = RepositoryConst::ITEM_ATTR_TYPE_NAME;
            array_push($metadataArray, $personalName);
        }
    }
    
    /**
     * Set additional attribute
     * 追加メタデータ設定
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param int $attributeId Attribute id 属性ID
     * @param int $itemTypeId Item type id アイテムタイプID
     * @param string $inputType Input type 入力タイプ
     * @param array $metadataArray Metadata list メタデータ一覧
     *                             array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     * @return boolean Result 結果
     */
    private function setAdditionalAttribute($itemId, $itemNo, $attrId, $itemTypeId, $inputType, &$metadataArray)
    {
        // Get attribute
        $query = "SELECT * ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_ITEM_ATTR." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_TYPE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_COMMON_IS_DELETE." = 0 ".
                 "ORDER BY ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_NO." ASC;";
        $params = array();
        $params[] = $itemTypeId;
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attrId;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        
        foreach($result as $itemAttr)
        {
            $itemAttr["input_type"] = $inputType;
            array_push($metadataArray, $itemAttr);
        }
    }
}

?>
