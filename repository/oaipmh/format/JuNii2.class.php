<?php

/**
 * Item information output class in junii2
 * junii2でのアイテム情報出力クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: JuNii2.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * OAI-PMH item information output base class
 * OAI-PMHアイテム情報出力基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/oaipmh/format/FormatAbstract.class.php';

/**
 * Name authority common classes
 * 著者名典拠共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/NameAuthority.class.php';
/**
 * Handle management common classes
 * ハンドル管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';

/**
 * Item information output class in junii2
 * junii2でのアイテム情報出力クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_JuNii2 extends Repository_Oaipmh_FormatAbstract
{
    /**
     * Minimum and maximum number of items for determination array
     * 最小・最大項目数判定用配列
     *
     * @var array["title"|"date"|"niitype"|"uri"|"volume"|"jtitle"|"issue"|"spage"|"epa"|""|""|""|""|""|""]
     */
    private $occurs = array();
    /**
     * item language string
     * アイテム言語
     * 
     * @var string
     */
    private $strItemLanguage = null;
    /**
     * for instance of RepositoryHandleManager class
     * ハンドル管理オブジェクト
     * 
     * @var object
     */
    private $repositoryHandleManager = null;
    /**
     * Constructor
     * コンストラクタ
     * 
     * @param Session $sesssion Session management objects Session管理オブジェクト
     * @param DbObject $db Database management objects データベース管理オブジェクト
     */
    public function __construct($session, $db)
    {
        parent::__construct($session, $db);
    }
    
    /**
     * Initialize
     * 初期化
     */
    private function initialize()
    {
        $this->occurs = array(  RepositoryConst::JUNII2_TITLE=>1,
                                RepositoryConst::JUNII2_DATE=>1,
                                RepositoryConst::JUNII2_NIITYPE=>1,
                                RepositoryConst::JUNII2_URI=>1,
                                RepositoryConst::JUNII2_JTITLE=>1,
                                RepositoryConst::JUNII2_VOLUME=>1,
                                RepositoryConst::JUNII2_ISSUE=>1,
                                RepositoryConst::JUNII2_SPAGE=>1,
                                RepositoryConst::JUNII2_EPAGE=>1,
                                RepositoryConst::JUNII2_DATE_OF_ISSUED=>1,
                                RepositoryConst::JUNII2_PMID=>1,
                                RepositoryConst::JUNII2_DOI=>1,
                                RepositoryConst::JUNII2_TEXTVERSION=>1,
                                // Add JuNii2 ver3 R.Matsuura 2013/09/24 --start--
                                RepositoryConst::JUNII2_SELFDOI=>1,
                                RepositoryConst::JUNII2_SELFDOI_JALC=>1,
                                RepositoryConst::JUNII2_SELFDOI_CROSSREF=>1,
                                RepositoryConst::JUNII2_SELFDOI_DATACITE=>1,
                                RepositoryConst::JUNII2_NAID=>1,
                                RepositoryConst::JUNII2_ICHUSHI=>1,
                                RepositoryConst::JUNII2_GRANTID=>1,
                                RepositoryConst::JUNII2_DATEOFGRANTED=>1,
                                RepositoryConst::JUNII2_DEGREENAME=>1,
                                RepositoryConst::JUNII2_GRANTOR=>1);
                                // Add JuNii2 ver3 R.Matsuura 2013/09/24 --end--
    }
    
    /**
     * Output item information
     * アイテム情報を出力
     *
     * @param array $itemData Item data アイテムデータ
     *                        array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr_type"]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return string The output string 出力文字列
     */
    public function outputRecord($itemData)
    {
        if( !isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM]) || 
            !isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_TYPE]) )
          //  基本情報以外のメタデータが存在しない場合に判定に入ってしまうことを防ぐためコメントアウト
          //  !isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR_TYPE]) || 
          //  !isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR]))
        {
            return '';
        }
        
        // initialize
        $this->initialize();
        $xml = '';
        
        // header output
        $xml .= $this->outputHeader();
        // base info output
        $xml .= $this->outputBasicData($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM][0]);

        // Add new prefix 2013/12/26 T.Ichikawa --start--
        $result = $this->outputSelfDOI($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM][0][RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_ID],
                                       $itemData[RepositoryConst::ITEM_DATA_KEY_ITEM][0][RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_NO]);
        if($result === false) {
            return '';
        }
        $xml .= $result;
        // Add new prefix 2013/12/26 T.Ichikawa --end--
        
        // NIIType output
        $niiType = $itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_TYPE][0][RepositoryConst::DBCOL_REPOSITORY_ITEM_TYPE_MAPPING_INFO];
        
        if( is_null($niiType) )
        {
            return '';
        }
        
        $xml .= $this->outputNiiType($niiType);
        
        // metadata output
        if(isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR]))
        {
            $xml .= $this->outputMetadta($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR_TYPE], $itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR]);
        }
        
        // item link output
        if(isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_REFERENCE]))
        {
            $xml .= $this->outputReference($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_REFERENCE]);
        }
        
        // date tag check
        if($this->occurs[RepositoryConst::JUNII2_DATE] > 0)
        {
            // YYYY-MM-DD or YYYY-MM or YYYY only
            $insDate = $itemData[RepositoryConst::ITEM_DATA_KEY_ITEM][0][RepositoryConst::DBCOL_COMMON_INS_DATE];
            $value = explode(" ", $insDate);
            $xml .= $this->outputDate($value[0]);
        }
        
        // when return false, metadata occurs failed.
        if(!$this->occursCheck())
        {
            return '';
        }
        
        // footer output
        $xml .= $this->outputFooter();
        
        return $xml;
    }
    
    /**
     * The output of the header part
     * ヘッダー部分の出力
     *
     * @return string The output string 出力文字列
     */
    private function outputHeader()
    {
        $xml = '';
        $xml .= '<'.RepositoryConst::JUNII2_START;
        $xml .= ' xsi:schemaLocation="http://irdb.nii.ac.jp/oai ';
        $xml .= ' http://irdb.nii.ac.jp/oai/junii2-3-1.xsd">'.self::LF;
        return $xml;
    }
    
    /**
     * item basic data output
     * アイテム基本情報出力
     *
     * @param array $baseData Item base information アイテム基本情報
     *              array["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return string The output string 出力文字列
     */
    private function outputBasicData($baseData)
    {
        $xml = '';
        // language. 言語
        $language = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_LANGUAGE];
        $language = RepositoryOutputFilter::language($language);
        
        // title. タイトル
        $title = '';
        $alternative = '';
        
        // タイトル言語
        $title_lang = "";
        $alternative_lang = "";
        
        if($language == RepositoryConst::ITEM_LANG_JA)
        {
            // japanese. 日本語
            $title = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
            if(strlen($title) == 0)
            {
                $title = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
                $title_lang = RepositoryConst::ITEM_LANG_EN;
            }
            else
            {
                $alternative = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
                $title_lang = RepositoryConst::ITEM_LANG_JA;
                $alternative_lang = RepositoryConst::ITEM_LANG_EN;
            }
        }
        else
        {
            // not japanese. 洋語
            $title = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
            if(strlen($title) == 0)
            {
                $title = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
                $title_lang = RepositoryConst::ITEM_LANG_JA;
            }
            else
            {
                $alternative = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
                $title_lang = RepositoryConst::ITEM_LANG_EN;
                $alternative_lang = RepositoryConst::ITEM_LANG_JA;
            }
        }
        $xml .= $this->outputTitle($title, $title_lang);
        $xml .= $this->outputAlternative($alternative, $alternative_lang);
        
        // language. 言語
        $xml .= $this->outputLanguage($language);
        
        // keyword. キーワード
        $keyword = explode("|", $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_SEARCH_KEY]."|".$baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_SEARCH_KEY_ENGLISH]);
        for($ii=0; $ii<count($keyword); $ii++)
        {
            $xml .= $this->outputSubject($keyword[$ii]);
        }
        
        // Add new prefix 2013/12/26 T.Ichikawa --start--
        $this->getRepositoryHandleManager();
        // URL
        $url = $this->repositoryHandleManager->createUriForJuNii2($baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_ID],
                                                                  $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_NO]);
        $xml .= $this->outputURI($url);
        // Add new prefix 2013/12/26 T.Ichikawa --end--
        
        return $xml;
    }
    
    /**
     * Metadata output
     * メタデータ出力
     *
     * @param array $itemAttrType Mapping info. マッピング情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $itemAttr Metadata ingo. メタデータ情報
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return string The output string 出力文字列
     */
    private function outputMetadta($itemAttrType, $itemAttr)
    {
        $xml = '';
        
        $value = '';
        for($ii=0; $ii<count($itemAttrType); $ii++)
        {
            if($itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_HIDDEN] == 1)
            {
                // hidden metadata
                continue;
            }
            
            // Add data filter parameter Y.Nakao 2013/05/17 --start--
            if($this->dataFilter == self::DATA_FILTER_SIMPLE && $itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LIST_VIEW_ENABLE] == 0)
            {
                // when data fileter is "simple", output list_view_enable=1 metadata.
                continue;
            }
            // Add data filter parameter Y.Nakao 2013/05/17 --end--
            
            // get value par input type. 入力タイプ別に出力値を求める
            $inputType = $itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IMPUT_TYPE];
            $lang = $itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];
            $lang = RepositoryOutputFilter::language($lang);
            
            // get mapping info. マッピング情報取得
            $junii2Map = $itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_JUNII2_MAPPING];
            if(strlen($junii2Map)==0 && $inputType != "file" && $inputType != "file_price")
            {
                // when is not mapping info, not output. マッピング情報がなければスルー
                continue;
            }
            
            for($jj=0; $jj<count($itemAttr[$ii]); $jj++)
            {
                $value = RepositoryOutputFilter::attributeValue($itemAttrType[$ii], $itemAttr[$ii][$jj], 2, 2);
                if(strlen($value) > 0)
                {
                    if($inputType == RepositoryConst::ITEM_ATTR_TYPE_BIBLIOINFO)
                    {
                        // jtitle,volume,issue,spage,epage,dateofissued
                        $mapping = explode(",", $junii2Map);
                        // $jtitle = $jtitle_en||$volume||$issue||$spage||$epage||$dateofissued
                        $biblio  = explode("||", $value);
                        // when output biblioinfo for junii2.
                        if(count($mapping) == 6 && count($biblio) == 6)
                        {
                            $xml .= $this->outputAttributeValue($mapping[0], $biblio[0]);
                            $xml .= $this->outputAttributeValue($mapping[1], $biblio[1]);
                            $xml .= $this->outputAttributeValue($mapping[2], $biblio[2]);
                            $xml .= $this->outputAttributeValue($mapping[3], $biblio[3]);
                            $xml .= $this->outputAttributeValue($mapping[4], $biblio[4]);
                            $xml .= $this->outputAttributeValue($mapping[5], $biblio[5]);
                        }
                    }
                    // Add JuNii2 ver3 R.Matsuura 2013/09/24 --start--
                    else if($inputType == RepositoryConst::ITEM_ATTR_TYPE_NAME)
                    {
                        $nameAuthority = new NameAuthority($this->Session, $this->Db);
                        $nameAuthorityInfo = $nameAuthority->getExternalAuthorIdData($itemAttr[$ii][$jj]["author_id"]);
                        
                        $reseacherResolverArray = array();
                        if(count($nameAuthorityInfo) > 0 && $nameAuthorityInfo[0]["prefix_id"] == 2)
                        {
                            $reseacherResolverId = $nameAuthorityInfo[0]["suffix"];
                            $reseacherResolverArray = array("prefix_id" => 2, "suffix" => $reseacherResolverId);
                        }
                        $xml .= $this->outputAttributeValue($junii2Map, $value, $lang, $reseacherResolverArray);
                    }
                    // Add JuNii2 ver3 R.Matsuura 2013/09/24 --end--
                    // Add for Bug No.1 Fixes R.Matsuura 2013/09/24 --start--
                    else if($inputType == RepositoryConst::ITEM_ATTR_TYPE_FILE || $inputType == RepositoryConst::ITEM_ATTR_TYPE_FILEPRICE)
                    {
                        $xml .= $this->outputattributeValue($junii2Map, $value);
                        $licenceNotation = RepositoryOutputFilter::fileLicence($itemAttr[$ii][$jj]);
                        $xml .= $this->outputRights($licenceNotation);
                    }
                    // Add for Bug No.1 Fixes R.Matsuura 2013/09/24 --end--
                    else
                    {
			// Add description tag mhaya 2015/11/30 --start--
                        if(_REPOSITORY_REPON_JUNII2_EXDESCRIPTION==true){
                            $attr_name = $itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_NAME];
                            $xml .= $this->outputAttributeValue($junii2Map, $value, $lang,array(),$attr_name);
                        }else{
                            // when is value, output. 値があれば出力
                            $xml .= $this->outputAttributeValue($junii2Map, $value, $lang,array());
                        }
                        // Add description tag mhaya 2015/11/30 --end--

                        // when is value, output. 値があれば出力
                        $xml .= $this->outputAttributeValue($junii2Map, $value, $lang);
                    }
                }
            }
        }
        
        return $xml;
    }
    
    /**
     * To output the XML in accordance with mapping settings
     * マッピング設定に従いXMLを出力する
     *
     * @param string $mapping Mapping マッピング
     * @param string $value Metadata メタデータ
     * @param string $lang Language 言語
     * @param array $authorIdArray External author id list 外部著者ID一覧
     *                             array["prefix"|"suffix"]
     * @param string $attr_name
     * @return string The output string 出力文字列
     */
    private function outputAttributeValue($mapping, $value, $lang="", $authorIdArray=array(),$attr_name="")
    {
        $xml = '';
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --start--
        $lang = RepositoryOutputFilterJuNii2::languageToRFC($lang);
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --end--
        
        switch ($mapping)
        {
            case RepositoryConst::JUNII2_TITLE:
                $xml = $this->outputTitle($value, $lang);
                break;
            case RepositoryConst::JUNII2_ALTERNATIVE:
                $xml = $this->outputAlternative($value, $lang);
                break;
            case RepositoryConst::JUNII2_CREATOR:
                // Update JuNii2 ver3 R.Matsuura 2013/09/24 --start--
                $xml = $this->outputCreator($value, $lang, $authorIdArray);
                // Update JuNii2 ver3 R.Matsuura 2013/09/24 --end--
                break;
            case RepositoryConst::JUNII2_SUBJECT:
                $xml = $this->outputSubject($value);
                break;
            case RepositoryConst::JUNII2_NII_SUBJECT:
                $xml = $this->outputNIISubject($value);
                break;
            case RepositoryConst::JUNII2_NDC:
                $xml = $this->outputNDC($value);
                break;
            case RepositoryConst::JUNII2_NDLC:
                $xml = $this->outputNDLC($value);
                break;
            case RepositoryConst::JUNII2_BSH:
                $xml = $this->outputBSH($value);
                break;
            case RepositoryConst::JUNII2_NDLSH:
                $xml = $this->outputNDLSH($value);
                break;
            case RepositoryConst::JUNII2_MESH:
                $xml = $this->outputMeSH($value);
                break;
            case RepositoryConst::JUNII2_DDC:
                $xml = $this->outputDDC($value);
                break;
            case RepositoryConst::JUNII2_LCC:
                $xml = $this->outputLCC($value);
                break;
            case RepositoryConst::JUNII2_UDC:
                $xml = $this->outputUDC($value);
                break;
            case RepositoryConst::JUNII2_LCSH:
                $xml = $this->outputLCSH($value);
                break;
            case RepositoryConst::JUNII2_DESCRIPTION:
                // Add description tag  mhaya 2015/11/30 --start--
                if(_REPOSITORY_REPON_JUNII2_EXDESCRIPTION==true){
                    $xml = $this->outputDescription($value,$attr_name);
                }else{
                    $xml = $this->outputDescription($value);
                }
                // Add description tag mhaya 2015/11/30 --end--
                $xml = $this->outputDescription($value);
                break;
                // Update JuNii2 ver3 R.Matsuura 2013/09/24 --start--
            case RepositoryConst::JUNII2_PUBLISHER:
                $xml = $this->outputPublisher($value, $lang, $authorIdArray);
                break;
            case RepositoryConst::JUNII2_CONTRIBUTOR:
                $xml = $this->outputContributor($value, $lang, $authorIdArray);
                break;
                // Update JuNii2 ver3 R.Matsuura 2013/09/24 --end--
            case RepositoryConst::JUNII2_DATE:
                $xml = $this->outputDate($value);
                break;
            case RepositoryConst::JUNII2_TYPE:
                $xml = $this->outputType($value);
                break;
            case RepositoryConst::JUNII2_NIITYPE:
                $xml = $this->outputNIIType($value);
                break;
            case RepositoryConst::JUNII2_FORMAT:
                $xml = $this->outputFormat($value);
                break;
            case RepositoryConst::JUNII2_IDENTIFIER:
                $xml = $this->outputIdentifier($value);
                break;
            case RepositoryConst::JUNII2_URI:
                $xml = $this->outputURI($value);
                break;
            case RepositoryConst::JUNII2_FULL_TEXT_URL:
                $xml = $this->outputFullTextURL($value);
                break;
            case RepositoryConst::JUNII2_ISSN:
                $xml = $this->outputISSN($value);
                break;
            case RepositoryConst::JUNII2_NCID:
                $xml = $this->outputNCID($value);
                break;
            case RepositoryConst::JUNII2_JTITLE:
                $xml = $this->outputJtitle($value, $lang);
                break;
            case RepositoryConst::JUNII2_VOLUME:
                $xml = $this->outputVolume($value);
                break;
            case RepositoryConst::JUNII2_ISSUE:
                $xml = $this->outputIssue($value);
                break;
            case RepositoryConst::JUNII2_SPAGE:
                $xml = $this->outputSpage($value);
                break;
            case RepositoryConst::JUNII2_EPAGE:
                $xml = $this->outputEpage($value);
                break;
            case RepositoryConst::JUNII2_DATE_OF_ISSUED:
                $xml = $this->outputDateofissued($value);
                break;
            case RepositoryConst::JUNII2_SOURCE:
                $xml = $this->outputSource($value);
                break;
            case RepositoryConst::JUNII2_LANGUAGE:
                $xml = $this->outputLanguage($value);
                break;
            case RepositoryConst::JUNII2_RELATION:
                $xml = $this->outputRelation($value);
                break;
            case RepositoryConst::JUNII2_PMID:
                $xml = $this->outputPmid($value);
                break;
            case RepositoryConst::JUNII2_DOI:
                $xml = $this->outputDoi($value);
                break;
            case RepositoryConst::JUNII2_IS_VERSION_OF:
                $xml = $this->outputIsVersionOf($value);
                break;
            case RepositoryConst::JUNII2_HAS_VERSION:
                $xml = $this->outputHasVersion($value);
                break;
            case RepositoryConst::JUNII2_IS_REPLACED_BY:
                $xml = $this->outputIsReplacedBy($value);
                break;
            case RepositoryConst::JUNII2_REPLACES:
                $xml = $this->outputReplaces($value);
                break;
            case RepositoryConst::JUNII2_IS_REQUIRESD_BY:
                $xml = $this->outputIsRequiredBy($value);
                break;
            case RepositoryConst::JUNII2_REQUIRES:
                $xml = $this->outputRequires($value);
                break;
            case RepositoryConst::JUNII2_IS_PART_OF:
                $xml = $this->outputIsPartOf($value);
                break;
            case RepositoryConst::JUNII2_HAS_PART:
                $xml = $this->outputHasPart($value);
                break;
            case RepositoryConst::JUNII2_IS_REFERENCED_BY:
                $xml = $this->outputIsReferencedBy($value);
                break;
            case RepositoryConst::JUNII2_REFERENCES:
                $xml = $this->outputReferences($value);
                break;
            case RepositoryConst::JUNII2_IS_FORMAT_OF:
                $xml = $this->outputIsFormatOf($value);
                break;
            case RepositoryConst::JUNII2_HAS_FORMAT:
                $xml = $this->outputHasFormat($value);
                break;
            case RepositoryConst::JUNII2_COVERAGE:
                $xml = $this->outputCoverage($value);
                break;
            case RepositoryConst::JUNII2_SPATIAL:
                $xml = $this->outputSpatial($value);
                break;
            case RepositoryConst::JUNII2_NII_SPATIAL:
                $xml = $this->outputNIISpatial($value);
                break;
            case RepositoryConst::JUNII2_TEMPORAL:
                $xml = $this->outputTemporal($value);
                break;
            case RepositoryConst::JUNII2_NII_TEMPORAL:
                $xml = $this->outputNIITemporal($value);
                break;
            case RepositoryConst::JUNII2_RIGHTS:
                $xml = $this->outputRights($value);
                break;
            case RepositoryConst::JUNII2_TEXTVERSION:
                $xml = $this->outputTextversion($value);
                break;
            // Add JuNii2 ver3 R.Matsuura 2013/09/24 --start--
            case RepositoryConst::JUNII2_ISBN:
                $xml = $this->outputISBN($value);
                break;
            case RepositoryConst::JUNII2_NAID:
                $xml = $this->outputNAID($value);
                break;
            case RepositoryConst::JUNII2_ICHUSHI:
                $xml = $this->outputIchushi($value);
                break;
            case RepositoryConst::JUNII2_GRANTID:
                $xml = $this->outputGrantid($value);
                break;
            case RepositoryConst::JUNII2_DATEOFGRANTED:
                $xml = $this->outputDateofgranted($value);
                break;
            case RepositoryConst::JUNII2_DEGREENAME:
                $xml = $this->outputDegreename($value);
                break;
            case RepositoryConst::JUNII2_GRANTOR:
                $xml = $this->outputGrantor($value);
                break;
            // Add JuNii2 ver3 R.Matsuura 2013/09/24 --end--
            default:
                break;
        }
        return $xml;
    }
    
    /**
     * title output
     *   is necessary.
     *   minOccurs = 1, maxOccurs = 1
     *   option = lang
     * titleタグを出力する
     * 
     * @param string $title title title
     * @param string $lang Language 言語
     * @return string The output string 出力文字列
     */
    private function outputTitle($title, $lang="")
    {
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_TITLE] < 1)
        {
            // when over maxOccurs, output alternative.
            return $this->outputAlternative($title, $lang);
        }
        
        // output title
        $tag = RepositoryConst::JUNII2_TITLE;
        $option = array();
        if(strlen($lang) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_LANG] = $lang;
        }
        $xml = $this->outputElement($tag, $title, $option);
        if(strlen($xml)>0)
        {
            $this->occurs[RepositoryConst::JUNII2_TITLE]--;
        }
        
        return $xml;
        
    }
    
    /**
     * alternative output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = lang
     * alternativeタグを出力する
     * 
     * @param string $alternative alternative alternative
     * @param string $lang Language 言語
     * @return string The output string 出力文字列
     */
    private function outputAlternative($alternative, $lang="")
    {
        $tag = RepositoryConst::JUNII2_ALTERNATIVE;
        $option = array();
        if(strlen($lang) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_LANG] = $lang;
        }
        return $this->outputElement($tag, $alternative, $option);
    }
    
    /**
     * creator output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = lang
     * creatorタグを出力する
     * 
     * @param string $creator creator creator
     * @param string $lang Language 言語
     * @param array $authorIdArray External author id 外部著者ID
     *                             array["prefix"|"suffix"]
     * @return string The output string 出力文字列
     */
    private function outputCreator($creator, $lang="", $authorIdArray=array())
    {
        $tag = RepositoryConst::JUNII2_CREATOR;
        
        $option = array();
        if(strlen($lang) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_LANG] = $lang;
        }
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --start--
        $uri = RepositoryOutputFilter::creatorId($authorIdArray);
        if(strlen($uri) > 0)
        {
            $option["id"] = $uri;
        }
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --end--
        return $this->outputElement($tag, $creator, $option);
    }
    
    /**
     * subject output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = null
     * subjectタグを出力する
     * 
     * @param string $subject subject subject
     * @return string The output string 出力文字列
     */
    private function outputSubject($subject)
    {
        $tag = RepositoryConst::JUNII2_SUBJECT;
        return $this->outputElement($tag, $subject);
    }
    
    /**
     * NIIsubject output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = version
     * NIIsubjectタグを出力する
     * 
     * @param string $niiSubject NIIsubject NIIsubject
     * @param string $version Version バージョン
     * @return string The output string 出力文字列
     */
    private function outputNIISubject($niiSubject, $version="")
    {
        $tag = RepositoryConst::JUNII2_NII_SUBJECT;
        $option = array();
        if(strlen($version) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_VERSION] = $version;
        }
        return $this->outputElement($tag, $niiSubject, $option);
    }
    
    /**
     * NDC output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = version
     * NDCタグを出力する
     * 
     * @param string $NDC NDC NDC
     * @param string $version Version バージョン
     * @return string The output string 出力文字列
     */
    private function outputNDC($NDC, $version="")
    {
        $tag = RepositoryConst::JUNII2_NDC;
        $option = array();
        if(strlen($version) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_VERSION] = $version;
        }
        return $this->outputElement($tag, $NDC, $option);
    }
    
    /**
     * NDLC output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = version
     * NDLCタグを出力する
     * 
     * @param string $NDLC NDLC NDLC
     * @param string $version Version バージョン
     * @return string The output string 出力文字列
     */
    private function outputNDLC($NDLC, $version="")
    {
        $tag = RepositoryConst::JUNII2_NDLC;
        $option = array();
        if(strlen($version) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_VERSION] = $version;
        }
        return $this->outputElement($tag, $NDLC, $option);
    }
    
    /**
     * BSH output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = version
     * BSHタグを出力する
     * 
     * @param string $BSH BSH BSH
     * @param string $version Version バージョン
     * @return string The output string 出力文字列
     */
    private function outputBSH($BSH, $version="")
    {
        $tag = RepositoryConst::JUNII2_BSH;
        $option = array();
        if(strlen($version) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_VERSION] = $version;
        }
        return $this->outputElement($tag, $BSH, $option);
    }
    
    /**
     * NDLSH output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = version
     * NDLSHタグを出力する
     * 
     * @param string $NDLSH NDLSH NDLSH
     * @param string $version Version バージョン
     * @return string The output string 出力文字列
     */
    private function outputNDLSH($NDLSH, $version="")
    {
        $tag = RepositoryConst::JUNII2_NDLSH;
        $option = array();
        if(strlen($version) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_VERSION] = $version;
        }
        return $this->outputElement($tag, $NDLSH, $option);
    }
    
    /**
     * MeSH output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = version
     * MeSHタグを出力する
     * 
     * @param string $MeSH MeSH MeSH
     * @param string $version Version バージョン
     * @return string The output string 出力文字列
     */
    private function outputMeSH($MeSH, $version="")
    {
        $tag = RepositoryConst::JUNII2_MESH;
        $option = array();
        if(strlen($version) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_VERSION] = $version;
        }
        return $this->outputElement($tag, $MeSH, $option);
    }
    
    /**
     * DDC output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = version
     * DDCタグを出力する
     * 
     * @param string $DDC DDC DDC
     * @param string $version Version バージョン
     * @return string The output string 出力文字列
     */
    private function outputDDC($DDC, $version="")
    {
        $tag = RepositoryConst::JUNII2_MESH;
        $option = array();
        if(strlen($version) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_VERSION] = $version;
        }
        return $this->outputElement($tag, $DDC, $option);
    }
    
    /**
     * LCC output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = version
     * LCCタグを出力する
     * 
     * @param string $LCC LCC LCC
     * @param string $version Version バージョン
     * @return string The output string 出力文字列
     */
    private function outputLCC($LCC, $version="")
    {
        $tag = RepositoryConst::JUNII2_DDC;
        $option = array();
        if(strlen($version) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_VERSION] = $version;
        }
        return $this->outputElement($tag, $LCC, $option);
    }
    
    /**
     * UDC output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = version
     * UDCタグを出力する
     * 
     * @param string $UDC UDC UDC
     * @param string $version Version バージョン
     * @return string The output string 出力文字列
     */
    private function outputUDC($UDC, $version="")
    {
        $tag = RepositoryConst::JUNII2_UDC;
        $option = array();
        if(strlen($version) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_VERSION] = $version;
        }
        return $this->outputElement($tag, $UDC, $option);
    }
    
    /**
     * LCSH output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = version
     * LCSHタグを出力する
     * 
     * @param string $LCSH LCSH LCSH
     * @param string $version Version バージョン
     * @return string The output string 出力文字列
     */
    private function outputLCSH($LCSH, $version="")
    {
        $tag = RepositoryConst::JUNII2_LCSH;
        $option = array();
        if(strlen($version) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_VERSION] = $version;
        }
        return $this->outputElement($tag, $LCSH, $option);
    }
    
    /**
     * description output
     *   minOccurs = 0, maxOccurs = unbounded
     * descriptionタグを出力する
     * 
     * @param string $description description description
     * @param string $attr_name add mhaya
     * @return string The output string 出力文字列
     */
    private function outputDescription($description,$attr_name="")
    {
        $tag = RepositoryConst::JUNII2_DESCRIPTION;
        // Add description tag mhaya 2015/11/30 --start --
        if(_REPOSITORY_REPON_JUNII2_EXDESCRIPTION==true){
            return $this->outputElement($tag,$attr_name.":".$description);
        }
        // Add description tag mhaya 2015/11/30 --end--
        return $this->outputElement($tag, $description);
    }
    
    /**
     * publisher output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = lang
     * publisherタグを出力する
     * 
     * @param string $publisher publisher publisher
     * @param string $lang Language 言語
     * @param array $authorIdArray External author id 外部著者ID
     *                             array["prefix"|"suffix"]
     * @return string The output string 出力文字列
     */
    private function outputPublisher($publisher, $lang="", $authorIdArray=array())
    {
        $tag = RepositoryConst::JUNII2_PUBLISHER;
        
        $option = array();
        if(strlen($lang) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_LANG] = $lang;
        }
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --start--
        $uri = RepositoryOutputFilter::creatorId($authorIdArray);
        if(strlen($uri) > 0)
        {
            $option["id"] = $uri;
        }
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --end--
        return $this->outputElement($tag, $publisher, $option);
    }
    
    /**
     * contributor output
     *   minOccurs = 0, maxOccurs = unbounded
     *   option = lang
     * contributorタグを出力する
     * 
     * @param string $contributor contributor contributor
     * @param string $lang Language 言語
     * @param array $authorIdArray External author id 外部著者ID
     *                             array["prefix"|"suffix"]
     * @return string The output string 出力文字列
     */
    private function outputContributor($contributor, $lang="", $authorIdArray=array())
    {
        $tag = RepositoryConst::JUNII2_CONTRIBUTOR;
        
        $option = array();
        if(strlen($lang) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_LANG] = $lang;
        }
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --start--
        $uri = RepositoryOutputFilter::creatorId($authorIdArray);
        if(strlen($uri) > 0)
        {
            $option["id"] = $uri;
        }
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --end--
        return $this->outputElement($tag, $contributor, $option);
    }
    
    /**
     * date output
     *   minOccurs = 0, maxOccurs = unbounded
     * dateタグを出力する
     * 
     * @param string $date date date
     * @return string The output string 出力文字列
     */
    private function outputDate($date)
    {
        $tag = RepositoryConst::JUNII2_DATE;
        $date = RepositoryOutputFilter::date($date);
        $xml = $this->outputElement($tag, $date);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_DATE]--;
        }
        return $xml;
    }
    
    /**
     * type output
     *   minOccurs = 0, maxOccurs = unbounded
     * typeタグを出力する
     * 
     * @param string $type type type
     * @return string The output string 出力文字列
     */
    private function outputType($type)
    {
        $tag = RepositoryConst::JUNII2_TYPE;
        return $this->outputElement($tag, $type);
    }
    
    /**
     * output NIIType
     *   is necessary.
     *   minOccurs = 1, maxOccurs = 1
     * NIITypeタグを出力する
     * 
     * @param string $niiType NIIType NIIType
     * @return string The output string 出力文字列
     */
    private function outputNIIType($niiType)
    {
        $xml = '';
        if( $niiType!=RepositoryConst::NIITYPE_JOURNAL_ARTICLE &&
            $niiType!=RepositoryConst::NIITYPE_THESIS_OR_DISSERTATION &&
            $niiType!=RepositoryConst::NIITYPE_DEPARTMENTAL_BULLETIN_PAPER &&
            $niiType!=RepositoryConst::NIITYPE_CONFERENCE_PAPER &&
            $niiType!=RepositoryConst::NIITYPE_PRESENTATION &&
            $niiType!=RepositoryConst::NIITYPE_BOOK &&
            $niiType!=RepositoryConst::NIITYPE_TECHNICAL_REPORT &&
            $niiType!=RepositoryConst::NIITYPE_RESEARCH_PAPER &&
            $niiType!=RepositoryConst::NIITYPE_ARTICLE &&
            $niiType!=RepositoryConst::NIITYPE_PREPRINT &&
            $niiType!=RepositoryConst::NIITYPE_LEARNING_MATERIAL &&
            $niiType!=RepositoryConst::NIITYPE_DATA_OR_DATASET &&
            $niiType!=RepositoryConst::NIITYPE_SOFTWARE &&
            $niiType!=RepositoryConst::NIITYPE_OTHERS)
        {
            return '';
        }
        
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_NIITYPE] < 1)
        {
            // when over maxOccurs, output type.
            return $this->outputType($niiType);
        }
        
        $tag = RepositoryConst::JUNII2_NIITYPE;
        $xml = $this->outputElement($tag, $niiType);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_NIITYPE]--;
        }
        return $xml;
    }
    
    /**
     * format output
     *   minOccurs = 0, maxOccurs = unbounded
     * formatタグを出力する
     * 
     * @param string $format format format
     * @return string The output string 出力文字列
     */
    private function outputFormat($format)
    {
        $tag = RepositoryConst::JUNII2_FORMAT;
        return $this->outputElement($tag, $format);
    }
    
    /**
     * identifier output
     *   minOccurs = 0, maxOccurs = unbounded
     * identifierタグを出力する
     * 
     * @param string $identifier identifier identifier
     * @return string The output string 出力文字列
     */
    private function outputIdentifier($identifier)
    {
        $tag = RepositoryConst::JUNII2_IDENTIFIER;
        return $this->outputElement($tag, $identifier);
    }
    
    /**
     * URI output
     *   is necessary.
     *   minOccurs = 1, maxOccurs = 1
     * URIタグを出力する
     * 
     * @param string $URI URI URI
     * @return string The output string 出力文字列
     */
    private function outputURI($URI)
    {
        if(strlen($URI)==0)
        {
            return '';
        }
        
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_URI] < 1)
        {
            // when over maxOccurs, output identifier.
            return $this->outputIdentifier($URI);
        }
        
        // output title
        $tag = RepositoryConst::JUNII2_URI;
        $xml = $this->outputElement($tag, $URI);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_URI]--;
        }
        return $xml;
    }
    
    /**
     * fullTextURL output
     *   minOccurs = 0, maxOccurs = unbounded
     * fullTextURLタグを出力する
     * 
     * @param string $fullTextURL fullTextURL fullTextURL
     * @return string The output string 出力文字列
     */
    private function outputFullTextURL($fullTextURL)
    {
        $tag = RepositoryConst::JUNII2_FULL_TEXT_URL;
        return $this->outputElement($tag, $fullTextURL);
    }
    
    /**
     * issn output
     *   minOccurs = 0, maxOccurs = unbounded
     * ISSNタグを出力する
     * 
     * @param string $issn ISSN ISSN
     * @return string The output string 出力文字列
     */
    private function outputISSN($issn)
    {
        $tag = RepositoryConst::JUNII2_ISSN;
        $issn = RepositoryOutputFilterJuNii2::issn($issn);
        return $this->outputElement($tag, $issn);
    }
    
    /**
     * ncid output
     *   minOccurs = 0, maxOccurs = unbounded
     * NCIDタグを出力する
     * 
     * @param string $ncid NCID NCID
     * @return string The output string 出力文字列
     */
    private function outputNCID($ncid)
    {
        $tag = RepositoryConst::JUNII2_NCID;
        return $this->outputElement($tag, $ncid);
    }
    
    /**
     * jtitle output
     *   minOccurs = 0, maxOccurs = 1
     *   option = lang
     * jtitleタグを出力する
     * 
     * @param string $jtitle jtitle jtitle
     * @param string $lang Language 言語
     * @return string The output string 出力文字列
     */
    private function outputJtitle($jtitle, $lang="")
    {
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_JTITLE] < 1)
        {
            // when over maxOccurs, output identifier.
            return $this->outputIdentifier($jtitle, $lang);
        }
        
        // output jtitle
        $tag = RepositoryConst::JUNII2_JTITLE;
        $option = array();
        if(strlen($lang) > 0)
        {
            $option[RepositoryConst::JUNII2_ATTRIBUTE_LANG] = $lang;
        }
        $xml = $this->outputElement($tag, $jtitle, $option);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_JTITLE]--;
        }
        return $xml;
    }
    
    /**
     * volume output
     *   minOccurs = 0, maxOccurs = 1
     * volumeタグを出力する
     * 
     * @param string $volume volume volume
     * @return string The output string 出力文字列
     */
    private function outputVolume($volume)
    {
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_VOLUME] < 1)
        {
            // when over maxOccurs, output identifier.
            return $this->outputIdentifier($volume);
        }
        
        // output volume
        $tag = RepositoryConst::JUNII2_VOLUME;
        $xml = $this->outputElement($tag, $volume);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_VOLUME]--;
        }
        return $xml;
    }
    
    /**
     * issue output
     *   minOccurs = 0, maxOccurs = 1
     * issueタグを出力する
     * 
     * @param string $issue issue issue
     * @return string The output string 出力文字列
     */
    private function outputIssue($issue)
    {
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_ISSUE] < 1)
        {
            // when over maxOccurs, output identifier.
            return $this->outputIdentifier($issue);
        }
        
        // output issue
        $tag = RepositoryConst::JUNII2_ISSUE;
        $xml = $this->outputElement($tag, $issue);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_ISSUE]--;
        }
        return $xml;
    }
    
    /**
     * spage output
     *   minOccurs = 0, maxOccurs = 1
     * spageタグを出力する
     * 
     * @param string $spage spage spage
     * @return string The output string 出力文字列
     */
    private function outputSpage($spage)
    {
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_SPAGE] < 1)
        {
            // when over maxOccurs, output identifier.
            return $this->outputIdentifier($spage);
        }
        
        // output spage
        $tag = RepositoryConst::JUNII2_SPAGE;
        $xml = $this->outputElement($tag, $spage);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_SPAGE]--;
        }
        return $xml;
    }
    
    /**
     * epage output
     *   minOccurs = 0, maxOccurs = 1
     * epageタグを出力する
     * 
     * @param string $epage epage epage
     * @return string The output string 出力文字列
     */
    private function outputEpage($epage)
    {
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_EPAGE] < 1)
        {
            // when over maxOccurs, output identifier.
            return $this->outputIdentifier($epage);
        }
        
        // output spage
        $tag = RepositoryConst::JUNII2_EPAGE;
        $xml = $this->outputElement($tag, $epage);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_EPAGE]--;
        }
        return $xml;
    }
    
    /**
     * dateofissued output
     *   minOccurs = 0, maxOccurs = 1
     * dateofissuedタグを出力する
     * 
     * @param string $dateofissued dateofissued dateofissued
     * @return string The output string 出力文字列
     */
    private function outputDateofissued($dateofissued)
    {
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_DATE_OF_ISSUED] < 1)
        {
            // when over maxOccurs, output identifier.
            return $this->outputIdentifier($dateofissued);
        }
        
        // output spage
        $tag = RepositoryConst::JUNII2_DATE_OF_ISSUED;
        $dateofissued = RepositoryOutputFilter::date($dateofissued);
        $xml = $this->outputElement($tag, $dateofissued);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_DATE_OF_ISSUED]--;
        }
        return $xml;
    }
    
    /**
     * source output
     *   minOccurs = 0, maxOccurs = unbounded
     * sourceタグを出力する
     * 
     * @param string $source source source
     * @return string The output string 出力文字列
     */
    private function outputSource($source)
    {
        $tag = RepositoryConst::JUNII2_SOURCE;
        return $this->outputElement($tag, $source);
    }
    
    /**
     * language output
     *   minOccurs = 0, maxOccurs = unbounded
     * languageタグを出力する
     * 
     * @param string $language language language
     * @return string The output string 出力文字列
     */
    private function outputLanguage($language)
    {
        $tag = RepositoryConst::JUNII2_LANGUAGE;
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --start--
        $language = RepositoryOutputFilterJuNii2::languageToISO($language);
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --end--
        return $this->outputElement($tag, $language);
    }
    
    /**
     * relation output
     *   minOccurs = 0, maxOccurs = unbounded
     * relationタグを出力する
     * 
     * @param string $relation relation relation
     * @return string The output string 出力文字列
     */
    private function outputRelation($relation)
    {
        $tag = RepositoryConst::JUNII2_RELATION;
        return $this->outputElement($tag, $relation);
    }
    
    /**
     * pmid output
     *   minOccurs = 0, maxOccurs = 1
     * pmidタグを出力する
     * 
     * @param string $pmid pmid pmid
     * @return string The output string 出力文字列
     */
    private function outputPmid($pmid)
    {
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_PMID] < 1)
        {
            // when over maxOccurs, output identifier.
            return $this->outputIdentifier($pmid);
        }
        
        // output issue
        $tag = RepositoryConst::JUNII2_PMID;
        $xml = $this->outputElement($tag, $pmid);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_PMID]--;
        }
        return $xml;
    }
    
    /**
     * doi output
     *   minOccurs = 0, maxOccurs = 1
     * doiタグを出力する
     * 
     * @param string $doi doi doi
     * @return string The output string 出力文字列
     */
    private function outputDoi($doi)
    {
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_DOI] < 1)
        {
            // when over maxOccurs, output identifier.
            return $this->outputIdentifier($doi);
        }
        
        // output issue
        $tag = RepositoryConst::JUNII2_DOI;
        $xml = $this->outputElement($tag, $doi);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_DOI]--;
        }
        return $xml;
    }
    
    /**
     * isVersionOf output
     *   minOccurs = 0, maxOccurs = unbounded
     * isVersionOfタグを出力する
     * 
     * @param string $isVersionOf isVersionOf isVersionOf
     * @return string The output string 出力文字列
     */
    private function outputIsVersionOf($isVersionOf)
    {
        $tag = RepositoryConst::JUNII2_IS_VERSION_OF;
        return $this->outputElement($tag, $isVersionOf);
    }
    
    /**
     * hasVersion output
     *   minOccurs = 0, maxOccurs = unbounded
     * hasVersionタグを出力する
     * 
     * @param string $hasVersion hasVersion hasVersion
     * @return string The output string 出力文字列
     */
    private function outputHasVersion($hasVersion)
    {
        $tag = RepositoryConst::JUNII2_HAS_VERSION;
        return $this->outputElement($tag, $hasVersion);
    }
    
    /**
     * isReplacedBy output
     *   minOccurs = 0, maxOccurs = unbounded
     * isReplacedByタグを出力する
     * 
     * @param string $isReplacedBy isReplacedBy isReplacedBy
     * @return string The output string 出力文字列
     */
    private function outputIsReplacedBy($isReplacedBy)
    {
        $tag = RepositoryConst::JUNII2_IS_REPLACED_BY;
        return $this->outputElement($tag, $isReplacedBy);
    }
    
    /**
     * isReplaces output
     *   minOccurs = 0, maxOccurs = unbounded
     * isReplacesタグを出力する
     * 
     * @param string $isReplaces isReplaces isReplaces
     * @return string The output string 出力文字列
     */
    private function outputReplaces($isReplaces)
    {
        $tag = RepositoryConst::JUNII2_REPLACES;
        return $this->outputElement($tag, $replaces);
    }
    
    /**
     * isRequiredBy output
     *   minOccurs = 0, maxOccurs = unbounded
     * isRequiredByタグを出力する
     * 
     * @param string $isRequiredBy isRequiredBy isRequiredBy
     * @return string The output string 出力文字列
     */
    private function outputIsRequiredBy($isRequiredBy)
    {
        $tag = RepositoryConst::JUNII2_IS_REQUIRESD_BY;
        return $this->outputElement($tag, $isRequiredBy);
    }
    
    /**
     * requires output
     *   minOccurs = 0, maxOccurs = unbounded
     * requiresタグを出力する
     * 
     * @param string $requires requires requires
     * @return string The output string 出力文字列
     */
    private function outputRequires($requires)
    {
        $tag = RepositoryConst::JUNII2_REQUIRES;
        return $this->outputElement($tag, $requires);
    }
    
    /**
     * isPartOf output
     *   minOccurs = 0, maxOccurs = unbounded
     * isPartOfタグを出力する
     * 
     * @param string $isPartOf isPartOf isPartOf
     * @return string The output string 出力文字列
     */
    private function outputIsPartOf($isPartOf)
    {
        $tag = RepositoryConst::JUNII2_IS_PART_OF;
        return $this->outputElement($tag, $isPartOf);
    }
    
    /**
     * hasPart output
     *   minOccurs = 0, maxOccurs = unbounded
     * hasPartタグを出力する
     * 
     * @param string $hasPart hasPart hasPart
     * @return string The output string 出力文字列
     */
    private function outputHasPart($hasPart)
    {
        $tag = RepositoryConst::JUNII2_HAS_PART;
        return $this->outputElement($tag, $hasPart);
    }
    
    /**
     * isReferencedBy output
     *   minOccurs = 0, maxOccurs = unbounded
     * isReferencedByタグを出力する
     * 
     * @param string $isReferencedBy isReferencedBy isReferencedBy
     * @return string The output string 出力文字列
     */
    private function outputIsReferencedBy($isReferencedBy)
    {
        $tag = RepositoryConst::JUNII2_IS_REFERENCED_BY;
        return $this->outputElement($tag, $isReferencedBy);
    }
    
    /**
     * references output
     *   minOccurs = 0, maxOccurs = unbounded
     * referencesタグを出力する
     * 
     * @param string $references references references
     * @return string The output string 出力文字列
     */
    private function outputReferences($references)
    {
        $tag = RepositoryConst::JUNII2_REFERENCES;
        return $this->outputElement($tag, $references);
    }
    
    /**
     * isFormatOf output
     *   minOccurs = 0, maxOccurs = unbounded
     * isFormatOfタグを出力する
     * 
     * @param string $isFormatOf isFormatOf isFormatOf
     * @return string The output string 出力文字列
     */
    private function outputIsFormatOf($isFormatOf)
    {
        $tag = RepositoryConst::JUNII2_IS_FORMAT_OF;
        return $this->outputElement($tag, $isFormatOf);
    }
    
    /**
     * hasFormat output
     *   minOccurs = 0, maxOccurs = unbounded
     * hasFormatタグを出力する
     * 
     * @param string $hasFormat hasFormat hasFormat
     * @return string The output string 出力文字列
     */
    private function outputHasFormat($hasFormat)
    {
        $tag = RepositoryConst::JUNII2_HAS_FORMAT;
        return $this->outputElement($tag, $hasFormat);
    }
    
    /**
     * coverage output
     *   minOccurs = 0, maxOccurs = unbounded
     * coverageタグを出力する
     * 
     * @param string $coverage coverage coverage
     * @return string The output string 出力文字列
     */
    private function outputCoverage($coverage)
    {
        $tag = RepositoryConst::JUNII2_COVERAGE;
        return $this->outputElement($tag, $coverage);
    }
    
    /**
     * spatial output
     *   minOccurs = 0, maxOccurs = unbounded
     * spatialタグを出力する
     * 
     * @param string $spatial spatial spatial
     * @return string The output string 出力文字列
     */
    private function outputSpatial($spatial)
    {
        $tag = RepositoryConst::JUNII2_SPATIAL;
        return $this->outputElement($tag, $spatial);
    }
    
    /**
     * NIIspatial output
     *   minOccurs = 0, maxOccurs = unbounded
     * NIIspatialタグを出力する
     * 
     * @param string $NIIspatial NIIspatial NIIspatial
     * @return string The output string 出力文字列
     */
    private function outputNIISpatial($NIIspatial)
    {
        $tag = RepositoryConst::JUNII2_NII_SPATIAL;
        return $this->outputElement($tag, $NIIspatial);
    }
    
    /**
     * temporal output
     *   minOccurs = 0, maxOccurs = unbounded
     * temporalタグを出力する
     * 
     * @param string $temporal temporal temporal
     * @return string The output string 出力文字列
     */
    private function outputTemporal($temporal)
    {
        $tag = RepositoryConst::JUNII2_TEMPORAL;
        return $this->outputElement($tag, $temporal);
    }
    
    /**
     * NIItemporal output
     *   minOccurs = 0, maxOccurs = unbounded
     * NIItemporalタグを出力する
     * 
     * @param string $NIItemporal NIItemporal NIItemporal
     * @return string The output string 出力文字列
     */
    private function outputNIITemporal($NIItemporal)
    {
        $tag = RepositoryConst::JUNII2_NII_TEMPORAL;
        return $this->outputElement($tag, $NIItemporal);
    }
    
    /**
     * rights output
     *   minOccurs = 0, maxOccurs = unbounded
     * rightsタグを出力する
     * 
     * @param string $rights rights rights
     * @return string The output string 出力文字列
     */
    private function outputRights($rights)
    {
        $tag = RepositoryConst::JUNII2_RIGHTS;
        return $this->outputElement($tag, $rights);
    }
    
    /**
     * textversion output
     *   minOccurs = 0, maxOccurs = 1
     * textversionタグを出力する
     * 
     * @param string $textversion textversion textversion
     * @return string The output string 出力文字列
     */
    private function outputTextversion($textversion)
    {
        // occursCheck
        if($this->occurs[RepositoryConst::JUNII2_TEXTVERSION] < 1)
        {
            // when over maxOccurs, output identifier.
            return $this->outputIdentifier($textversion);
        }
        
        // output issue
        $tag = RepositoryConst::JUNII2_TEXTVERSION;
        $textversion = RepositoryOutputFilterJuNii2::textversion($textversion);
        $xml = $this->outputElement($tag, $textversion);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_TEXTVERSION]--;
        }
        return $xml;
    }
    
    /**
     * return XML element.
     * XMLタグを出力する
     *
     * @param string $tag Tag name タグ名
     * @param string $value Value 値
     * @param array $oution Option オプション
     *                      array($key=>$value, $key=>$value, ... )
     * @return string The output string 出力文字列
     */
    private function outputElement($tag, $value, $option=array())
    {
        $value = $this->RepositoryAction->forXmlChange($value);
        if(strlen($tag) == 0 || strlen($value) == 0)
        {
            return '';
        }
        
        $strOption = '';
        foreach ($option as $key => $val)
        {
            if(strlen($key) > 0 && strlen($val) > 0)
            {
                $val = $this->RepositoryAction->forXmlChange($val);
                $strOption .= "$key=\"$val\" ";
            }
        }
        
        if(strlen($strOption) > 0)
        {
            $xml = "<$tag $strOption>$value</$tag>";
        }
        else
        {
            $xml = "<$tag>$value</$tag>";
        }
        return $xml;
    }
    
    /**
     * output item reference link
     * アイテムの参照を出力する
     *
     * @param array $reference Reference information 参照情報
     *                         array[$ii]["item_id"|"item_no"|"reference"]
     * @return string The output string 出力文字列
     */
    private function outputReference($reference)
    {
        $xml = '';
        
        for ($ii=0; $ii<count($reference); $ii++)
        {
            $destItemId = $reference[$ii][RepositoryConst::DBCOL_REPOSITORY_REF_DEST_ITEM_ID];
            $destItemNo = $reference[$ii][RepositoryConst::DBCOL_REPOSITORY_REF_DEST_ITEM_NO];
            $refKey     = $reference[$ii][RepositoryConst::DBCOL_REPOSITORY_REF_REFERENCE];
            // get detail url
            $refUrl = $this->RepositoryAction->getDetailUri($destItemId, $destItemNo);
            // mapping
            if(strlen($refKey) > 0)
            {
                $xml .= $this->outputAttributeValue($refKey, $refUrl);
            }
            else
            {
                $xml .= $this->outputRelation($refUrl);
            }
        }
        return $xml;
    }
    
    /**
     * occurs check
     * 最大最小項目数のチェック
     * 
     * return boolean Is valid 問題ないか否か
     *                true:OK, false:failed
     *
     */
    private function occursCheck()
    {
        if($this->occurs[RepositoryConst::JUNII2_TITLE] != 0)
        {
            // title is necessary.
            // minOccurs=1, maxOccurs=1
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_DATE] == 1)
        {
            // date is min occurs = 1, maxOccurs=unbounded.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_NIITYPE] != 0)
        {
            // NIIType is necessary.
            // minOccurs=1, maxOccurs=1
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_URI] != 0)
        {
            // URI is necessary.
            // minOccurs=1, maxOccurs=1
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_JTITLE] < 0)
        {
            // jtitle is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_VOLUME] < 0)
        {
            // volume is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_ISSUE] < 0)
        {
            // issue is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_SPAGE] < 0)
        {
            // spage is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_EPAGE] < 0)
        {
            // epage is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_DATE_OF_ISSUED] < 0)
        {
            // dateofissued is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_PMID] < 0)
        {
            // pmid is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_DOI] < 0)
        {
            // doi is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_TEXTVERSION] < 0)
        {
            // textversion is min occurs = 0, maxOccurs=1.
            return false;
        }
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --start--
        if($this->occurs[RepositoryConst::JUNII2_SELFDOI] < 0)
        {
            // selfdoi is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_SELFDOI_JALC] < 0)
        {
            // selfdoi(jalc) is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_SELFDOI_CROSSREF] < 0)
        {
            // selfdoi(crossref) is min occurs = 0, maxOccurs=1.
            return false;
        }
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        if($this->occurs[RepositoryConst::JUNII2_SELFDOI_DATACITE] < 0)
        {
            // selfdoi(datacite) is min occurs = 0, maxOccurs=1.
            return false;
        }
        // Add DataCite 2015/02/10 K.Sugimoto --end--
        if($this->occurs[RepositoryConst::JUNII2_NAID] < 0)
        {
            // NAID is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_ICHUSHI] < 0)
        {
            // ichushi is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_GRANTID] < 0)
        {
            // grantid is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_DATEOFGRANTED] < 0)
        {
            // dateofgranted is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_DEGREENAME] < 0)
        {
            // degreename is min occurs = 0, maxOccurs=1.
            return false;
        }
        if($this->occurs[RepositoryConst::JUNII2_GRANTOR] < 0)
        {
            // grantor is min occurs = 0, maxOccurs=1.
            return false;
        }
        // Add JuNii2 ver3 R.Matsuura 2013/09/24 --end--
        return true;
    }
    
    /**
     * Footer output
     * フッター出力
     *
     * @return string The output string 出力文字列
     */
    private function outputFooter()
    {
        $xml = '';
        $xml .= '</'.RepositoryConst::JUNII2_START.'>'.self::LF;
        return $xml;
    }
    
    // Add new prefix 2013/12/24 T.Ichikawa --start--
    /**
     * SelfDOI output
     * selfDOIタグを出力する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return string The output string 出力文字列
     */
    private function outputSelfDOI($item_id, $item_no)
    {
        $tag = RepositoryConst::JUNII2_SELFDOI;
        $xml = "";
        
        $this->getRepositoryHandleManager();
        $uri_jalcdoi = $this->repositoryHandleManager->createSelfDoiUri($item_id, $item_no, RepositoryHandleManager::ID_JALC_DOI);
        $uri_crossref = $this->repositoryHandleManager->createSelfDoiUri($item_id, $item_no, RepositoryHandleManager::ID_CROSS_REF_DOI);
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        $uri_datacite = $this->repositoryHandleManager->createSelfDoiUri($item_id, $item_no, RepositoryHandleManager::ID_DATACITE_DOI);
        $uri_library_jalcdoi = $this->repositoryHandleManager->createSelfDoiUri($item_id, $item_no, RepositoryHandleManager::ID_LIBRARY_JALC_DOI);
        
        if(strlen($uri_jalcdoi) > 0 && strlen($uri_crossref) < 1 && strlen($uri_datacite) < 1 && strlen($uri_library_jalcdoi) < 1)
        {
            $option = array();
            $option[RepositoryConst::JUNII2_SELFDOI_ATTRIBUTE_JALC_DOI] = RepositoryConst::JUNII2_SELFDOI_RA_JALC;
            $xml .= $this->outputElement($tag, $uri_jalcdoi, $option);
            
        }
        else if(strlen($uri_crossref) > 0 && strlen($uri_jalcdoi) < 1 && strlen($uri_datacite) < 1 && strlen($uri_library_jalcdoi) < 1)
        {
            $option = array();
            $option[RepositoryConst::JUNII2_SELFDOI_ATTRIBUTE_JALC_DOI] = RepositoryConst::JUNII2_SELFDOI_RA_CROSSREF;
            $xml = $this->outputElement($tag, $uri_crossref, $option);
            
        }
        else if(strlen($uri_datacite) > 0 && strlen($uri_jalcdoi) < 1 && strlen($uri_crossref) < 1 && strlen($uri_library_jalcdoi) < 1)
        {
            $option = array();
            $option[RepositoryConst::JUNII2_SELFDOI_ATTRIBUTE_JALC_DOI] = RepositoryConst::JUNII2_SELFDOI_RA_DATACITE;
            $xml = $this->outputElement($tag, $uri_datacite, $option);
            
        }
        // Add DataCite 2015/02/10 K.Sugimoto --end--
        else if(strlen($uri_library_jalcdoi) > 0 && strlen($uri_jalcdoi) < 1 && strlen($uri_crossref) < 1 && strlen($uri_datacite) < 1)
        {
            $option = array();
            $option[RepositoryConst::JUNII2_SELFDOI_ATTRIBUTE_JALC_DOI] = RepositoryConst::JUNII2_SELFDOI_RA_JALC;
            $xml = $this->outputElement($tag, $uri_library_jalcdoi, $option);
            
        }
        
        return $xml;
    }
    
    /**
     * Get RepositoryHandleManager object
     * ハンドル管理オブジェクトを取得
     */
    private function getRepositoryHandleManager()
    {
        if(!isset($this->repositoryHandleManager)){
            if(strlen($this->RepositoryAction->TransStartDate) == 0){
                $date = new Date();
                $this->RepositoryAction->TransStartDate = $date->getDate().".000";
            }
            $this->repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->dbAccess, $this->RepositoryAction->TransStartDate);
        }
        
    }
    
    // Add new prefix 2013/12/24 T.Ichikawa --end--
    
    // Add JuNii2 ver3 R.Matsuura 2013/09/24 --start--
    /**
     * ISBN output
     *   minOccurs = 0, maxOccurs = 1
     * ISBNタグを出力する
     *
     * @param string $strIsbn ISBN ISBN
     * @return string The output string 出力文字列
     */
    private function outputISBN($strIsbn)
    {
        $tag = RepositoryConst::JUNII2_ISBN;
        $xml = $this->outputElement($tag, $strIsbn);
        return $xml;
    }
    
    /**
     * NAID output
     *   minOccurs = 0, maxOccurs = 1
     * NAIDタグを出力する
     *
     * @param string $strNaid NAID NAID
     * @return string The output string 出力文字列
     */
    private function outputNAID($strNaid)
    {
        if($this->occurs[RepositoryConst::JUNII2_NAID] < 1)
        {
            return $this->outputRelation($strNaid);
        }
        $tag = RepositoryConst::JUNII2_NAID;
        $naid = RepositoryOutputFilterJuNii2::naid($strNaid);
        $xml = $this->outputElement($tag, $naid);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_NAID]--;
        }
        return $xml;
    }
    
    /**
     * Ichushi output
     *   minOccurs = 0, maxOccurs = 1
     * ichushiタグを出力する
     *
     * @param string $strIchushi ichushi ichushi
     * @return string The output string 出力文字列
     */
    private function outputIchushi($strIchushi)
    {
        if($this->occurs[RepositoryConst::JUNII2_ICHUSHI] < 1)
        {
            return $this->outputRelation($strIchushi);
        }
        $tag = RepositoryConst::JUNII2_ICHUSHI;
        $ichushi = RepositoryOutputFilterJuNii2::ichushi($strIchushi);
        $xml = $this->outputElement($tag, $ichushi);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_ICHUSHI]--;
        }
        return $xml;
    }
    
    /**
     * grantid output
     *   minOccurs = 0, maxOccurs = 1
     * grantidタグを出力する
     *
     * @param string $strGrantId grantid grantid
     * @return string The output string 出力文字列
     */
    private function outputGrantid($strGrantId)
    {
        if($this->occurs[RepositoryConst::JUNII2_GRANTID] < 1)
        {
            return $this->outputIdentifier($strGrantId);
        }
        $tag = RepositoryConst::JUNII2_GRANTID;
        $grantid = RepositoryOutputFilterJuNii2::grantid($strGrantId);
        $xml = $this->outputElement($tag, $grantid);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_GRANTID]--;
        }
        return $xml;
    }
    
    /**
     * dateofgranted output
     *   minOccurs = 0, maxOccurs = 1
     * dateofgrantedタグを出力する
     *
     * @param string $strDateofgrant dateofgranted dateofgranted
     * @return string The output string 出力文字列
     */
    private function outputDateofgranted($strDateofgrant)
    {
        if($this->occurs[RepositoryConst::JUNII2_DATEOFGRANTED] < 1)
        {
            return $this->outputDate($strDateofgrant);
        }
        $tag = RepositoryConst::JUNII2_DATEOFGRANTED;
        $date = RepositoryOutputFilter::date($strDateofgrant);
        $xml = $this->outputElement($tag, $date);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_DATEOFGRANTED]--;
        }
        return $xml;
    }
    
    /**
     * degreename output
     *   minOccurs = 0, maxOccurs = 1
     * degreenameタグを出力
     *
     * @param string $strDegreename degreename degreename
     * @return string The output string 出力文字列
     */
    private function outputDegreename($strDegreename)
    {
        if($this->occurs[RepositoryConst::JUNII2_DEGREENAME] < 1)
        {
            return $this->outputDescription($strDegreename);
        }
        $tag = RepositoryConst::JUNII2_DEGREENAME;
        $xml = $this->outputElement($tag, $strDegreename);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_DEGREENAME]--;
        }
        return $xml;
    }
    
    /**
     * grantor output
     *   minOccurs = 0, maxOccurs = 1
     * grantorタグを出力する
     *
     * @param string $strGrantor grantor grantor
     * @return string The output string 出力文字列
     */
    private function outputGrantor($strGrantor)
    {
        if($this->occurs[RepositoryConst::JUNII2_GRANTOR] < 1)
        {
            return $this->outputDescription($strGrantor);
        }
        $tag = RepositoryConst::JUNII2_GRANTOR;
        $xml = $this->outputElement($tag, $strGrantor);
        if(strlen($xml) > 0)
        {
            $this->occurs[RepositoryConst::JUNII2_GRANTOR]--;
        }
        return $xml;
    }
    // Add JuNii2 ver3 R.Matsuura 2013/09/24 --end--
}
?>
