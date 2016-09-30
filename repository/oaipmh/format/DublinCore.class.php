<?php

/**
 * Item information output class in Dublin Core
 * Dublin Coreでのアイテム情報出力クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: DublinCore.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Item information output class in Dublin Core
 * Dublin Coreでのアイテム情報出力クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_DublinCore extends Repository_Oaipmh_FormatAbstract
{
    /**
     * Data output flag
     * データ出力フラグ
     *
     * @var boolean
     */
    private $outputDateFlg = false;
    
    /**
     * constructor
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
     * Initialization
     * 初期化
     */
    private function initialize()
    {
        $this->outputDateFlg = false;
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
        
        // NIIType output
        $niiType = $itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_TYPE][0][RepositoryConst::DBCOL_REPOSITORY_ITEM_TYPE_MAPPING_INFO];
        $xml .= $this->outputNIIType($niiType);
        
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
        
        if(!$this->outputDateFlg)
        {
            // YYYY-MM-DD or YYYY-MM or YYYY only
            $insDate = $itemData[RepositoryConst::ITEM_DATA_KEY_ITEM][0][RepositoryConst::DBCOL_COMMON_INS_DATE];
            $value = explode(" ", $insDate);
            $xml .= $this->outputDate($value[0]);
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
        $xml .= '<'.RepositoryConst::DUBLIN_CORE_START;
        $xml .= ' xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" ';
        $xml .= ' xmlns:dc="http://purl.org/dc/elements/1.1/" ';
        $xml .= ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $xml .= ' xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ ';
        $xml .= ' http://www.openarchives.org/OAI/2.0/oai_dc.xsd">'.self::LF;
        return $xml;
    }
    
    /**
     * The output of the item basic information
     * アイテム基本情報の出力
     *
     * @param array $baseData Item basic information アイテム基本情報
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
        if($language == RepositoryConst::ITEM_LANG_JA)
        {
            // japanese. 日本語
            $title = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
            if(strlen($title) == 0)
            {
                $title = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
            }
        }
        else
        {
            // not japanese. 洋語
            $title = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
            if(strlen($title) == 0)
            {
                $title = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
            }
        }
        $xml .= $this->outputTitle($title);
        
        // language. 言語
        $xml .= $this->outputLanguage($language);
        
        // keyword. キーワード
        $keyword = explode("|", $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_SEARCH_KEY]."|".$baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_SEARCH_KEY_ENGLISH]);
        for($ii=0; $ii<count($keyword); $ii++)
        {
            $xml .= $this->outputSubject($keyword[$ii]);
        }
        
        // URL
        $url = $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_URI];
        $xml .= $this->outputIdentifier($url);
        
        // Permalink
        $xml .= $this->outputPermalinkCnriOrYhandle($baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_ID], $baseData[RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_NO]);
        
        return $xml;
    }
    
    /**
     * Output permalink CNRI or YHandle by Dublin Core xml
     * CNRIまたはYハンドルのPermalinkをDublin CoreのXMLで出力する
     *
     * @param int $item_id Item ID アイテムID
     * @param int $item_no Item No アイテム通番
     * @return string Output of permalink CNRI or YHandle by xml CNRIまたはYハンドルのPermalinkのXML出力
     */
    private function outputPermalinkCnriOrYhandle($item_id, $item_no)
    {
        if(strlen($this->RepositoryAction->TransStartDate) == 0){
            $date = new Date();
            $this->RepositoryAction->TransStartDate = $date->getDate().".000";
        }
        $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->Db, $this->RepositoryAction->TransStartDate);
        
        $permalink = $repositoryHandleManager->createUriForDublinCore($item_id, $item_no);
        
        if(strlen($permalink) == 0)
        {
            return "";
        }
        return $this->outputIdentifier($permalink);
    }
    
    /**
     * NII type output
     * NII type出力
     *
     * @param string $niiType NIItype NIItype
     * @return string The output string 出力文字列
     */
    private function outputNIIType($niiType)
    {
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
        $xml = $this->outputType($niiType);
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
            
            // get mapping info. マッピング情報取得
            $dcMap = $itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DOBLIN_CORE_MAPPING];
            if(strlen($dcMap)==0)
            {
                // when is not mapping info, not output. マッピング情報がなければスルー
                continue;
            }
            
            // get value par input type. 入力タイプ別に出力値を求める
            $inputType = $itemAttrType[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IMPUT_TYPE];
            for($jj=0; $jj<count($itemAttr[$ii]); $jj++)
            {
                $value = RepositoryOutputFilter::attributeValue($itemAttrType[$ii], $itemAttr[$ii][$jj]);
                if(strlen($value) > 0)
                {
                    // Add JuNii2 ver3 R.Matsuura --start--
                    // when input type is biblio_info
                    if($inputType == "biblio_info")
                    {
                        $xml .= $this->outputAttributeValue("identifier", $value);
                        $xml .= $this->outputAttributeValue("date", $itemAttr[$ii][$jj]["date_of_issued"]);
                    }
                    else
                    {
                        // when is value, output. 値があれば出力
                        $xml .= $this->outputAttributeValue($dcMap, $value);
                    }
                    // Add JuNii2 ver3 R.Matsuura --end--
                }
            }
        }
        
        return $xml;
    }
    
    /**
     * And outputs the reference item information
     * 参照アイテム情報を出力する
     *
     * @param array $reference Item Reference Information アイテム参照情報
     *                         array[$ii]["item_id"|"item_no"]
     * @return string The output string 出力文字列
     */
    private function outputReference($reference)
    {
        $xml = '';
        for ($ii=0; $ii<count($reference); $ii++)
        {
            $destItemId = $reference[$ii][RepositoryConst::DBCOL_REPOSITORY_REF_DEST_ITEM_ID];
            $destItemNo = $reference[$ii][RepositoryConst::DBCOL_REPOSITORY_REF_DEST_ITEM_NO];
            // get detail url
            $refUrl = $this->RepositoryAction->getDetailUri($destItemId, $destItemNo);
            $xml .= $this->outputRelation($refUrl);
        }
        return $xml;
    }
    
    /**
     * To output the XML in accordance with mapping settings
     * マッピング設定に従いXMLを出力する
     *
     * @param string $mapping Mapping マッピング
     * @param string $value Metadata メタデータ
     * @return string The output string 出力文字列
     */
    private function outputAttributeValue($mapping, $value)
    {
        $xml = '';
        
        switch ($mapping)
        {
            case RepositoryConst::DUBLIN_CORE_TITLE:
                $xml = $this->outputTitle($value);
                break;
            case RepositoryConst::DUBLIN_CORE_CREATOR:
                $xml = $this->outputCreator($value);
                break;
            case RepositoryConst::DUBLIN_CORE_SUBJECT:
                $xml = $this->outputSubject($value);
                break;
            case RepositoryConst::DUBLIN_CORE_DESCRIPTION:
                $xml = $this->outputDescription($value);
                break;
            case RepositoryConst::DUBLIN_CORE_PUBLISHER:
                $xml = $this->outputPublisher($value);
                break;
            case RepositoryConst::DUBLIN_CORE_CONTRIBUTOR:
                $xml = $this->outputContributor($value);
                break;
            case RepositoryConst::DUBLIN_CORE_DATE:
                $xml = $this->outputDate($value);
                break;
            case RepositoryConst::DUBLIN_CORE_TYPE:
                $xml = $this->outputType($value);
                break;
            case RepositoryConst::DUBLIN_CORE_FORMAT:
                $xml = $this->outputFormat($value);
                break;
            case RepositoryConst::DUBLIN_CORE_IDENTIFIER:
                $xml = $this->outputIdentifier($value);
                break;
            case RepositoryConst::DUBLIN_CORE_SOURCE:
                $xml = $this->outputSource($value);
                break;
            case RepositoryConst::DUBLIN_CORE_LANGUAGE:
                $xml = $this->outputLanguage($value);
                break;
            case RepositoryConst::DUBLIN_CORE_RELATION:
                $xml = $this->outputRelation($value);
                break;
            case RepositoryConst::DUBLIN_CORE_COVERAGE:
                $xml = $this->outputCoverage($value);
                break;
            case RepositoryConst::DUBLIN_CORE_RIGHTS:
                $xml = $this->outputRights($value);
                break;
            default:
                break;
        }
        return $xml;
    }
    
    /**
     * Title tag output
     * Titleタグ出力
     *
     * @param string $title Title タイトル
     * @return string The output string 出力文字列
     */
    private function outputTitle($title)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_TITLE;
        return $this->outputElement($tag, $title);
    }
    
    /**
     * Creator tag output
     * Creatorタグ出力
     *
     * @param string $creator Creaotr 作成者
     * @return string The output string 出力文字列
     */
    private function outputCreator($creator)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_CREATOR;
        return $this->outputElement($tag, $creator);
    }
    
    /**
     * Subject tag output
     * Subjectタグ出力
     *
     * @param string $subject Subject 著者キーワード
     * @return string The output string 出力文字列
     */
    private function outputSubject($subject)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_SUBJECT;
        return $this->outputElement($tag, $subject);
    }
    
    
    /**
     * Description tag output
     * Descriptionタグ出力
     *
     * @param string $description Description 内容
     * @return string The output string 出力文字列
     */
    private function outputDescription($description)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_DESCRIPTION;
        return $this->outputElement($tag, $description);
    }
    
    /**
     * Publisher tag output
     * Publisherタグ出力
     *
     * @param string $publisher Publisher 公開者
     * @return string The output string 出力文字列
     */
    private function outputPublisher($publisher)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_PUBLISHER;
        return $this->outputElement($tag, $publisher);
    }
    
    /**
     * Contributor tag output
     * Contributorタグ出力
     *
     * @param string $contributor Contributor 寄与者
     * @return string The output string 出力文字列
     */
    private function outputContributor($contributor)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_PUBLISHER;
        return $this->outputElement($tag, $contributor);
    }
    
    /**
     * Date tag output
     * Dateタグ出力
     *
     * @param string $date Date 日付
     * @return string The output string 出力文字列
     */
    private function outputDate($date)
    {
        // outputFlg
        $this->outputDateFlg = true;
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_DATE;
        $date = RepositoryOutputFilter::date($date);
        return $this->outputElement($tag, $date);
    }
    
    /**
     * Type tag output
     * Typeタグ出力
     *
     * @param string $type Type タイプ
     * @return string The output string 出力文字列
     */
    private function outputType($type)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_TYPE;
        return $this->outputElement($tag, $type);
    }
    
    /**
     * Format tag output
     * Formatタグ出力
     *
     * @param string $format Format フォーマット
     * @return string The output string 出力文字列
     */
    private function outputFormat($format)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_FORMAT;
        return $this->outputElement($tag, $format);
    }
    
    /**
     * Identifier tag output
     * Identifierタグ出力
     *
     * @param string $identifier Identifier 識別子
     * @return string The output string 出力文字列
     */
    private function outputIdentifier($identifier)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_IDENTIFIER;
        return $this->outputElement($tag, $identifier);
    }
    
    /**
     * Source tag output
     * Sourceタグ出力
     *
     * @param string $source Source ソース
     * @return string The output string 出力文字列
     */
    private function outputSource($source)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_SOURCE;
        return $this->outputElement($tag, $source);
    }
    
    /**
     * Language tag output
     * Languageタグ出力
     *
     * @param string $language Language 言語
     * @return string The output string 出力文字列
     */
    private function outputLanguage($language)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_LANGUAGE;
        $language = RepositoryOutputFilter::language($language);
        if($language === RepositoryConst::ITEM_LANG_OTHER)
        {
            $language = '';
        }
        return $this->outputElement($tag, $language);
    }
    
    /**
     * Relation tag output
     * Relationタグ出力
     *
     * @param string $relation Relation 関連
     * @return string The output string 出力文字列
     */
    private function outputRelation($relation)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_RELATION;
        return $this->outputElement($tag, $relation);
    }
    
    /**
     * Coverage tag output
     * Coverageタグ出力
     *
     * @param string $coverage Coverage 範囲
     * @return string The output string 出力文字列
     */
    private function outputCoverage($coverage)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_COVERAGE;
        return $this->outputElement($tag, $coverage);
    }
    
    /**
     * Rights tag output
     * Rightsタグ出力
     *
     * @param string $rights Rights 権利
     * @return string The output string 出力文字列
     */
    private function outputRights($rights)
    {
        // output
        $tag = RepositoryConst::DUBLIN_CORE_PREFIX.RepositoryConst::DUBLIN_CORE_RIGHTS;
        return $this->outputElement($tag, $rights);
    }
    
    /**
     * Tag output
     * タグ出力
     *
     * @param string $tag Tag name タグ名
     * @param string $value Metadata メタデータ
     * @return string The output string 出力文字列
     */
    private function outputElement($tag, $value)
    {
        $value = $this->RepositoryAction->forXmlChange($value);
        if(strlen($tag) == 0 || strlen($value) == 0)
        {
            return '';
        }
        $xml = "<$tag>$value</$tag>".self::LF;
        return $xml;
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
        $xml .= '</'.RepositoryConst::DUBLIN_CORE_START.'>'.self::LF;
        return $xml;
    }
}

?>