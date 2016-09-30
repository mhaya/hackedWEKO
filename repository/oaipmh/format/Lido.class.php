<?php

/**
 * Item information output class in LIDO
 * LIDOでのアイテム情報出力クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Lido.class.php 36348 2014-05-28 01:34:51Z rei_matsuura $
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
 * Item information output class in LIDO
 * LIDOでのアイテム情報出力クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_Lido extends Repository_Oaipmh_FormatAbstract
{
    /**
     * DOM object
     * DOMオブジェクト
     *
     * @var DOMDocument
     */
    private $domDocument = null;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numObjectWorkTypeConceptID = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numObjectWorkTypeTerm = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numClassificationConceptID = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numClassificationTerm = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numReposotorySetName = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numReposotorySetLink = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numReposotorySetID = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numEventSetDisplayEvent = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numEventSetTypeTerm = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numEventSetActor = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numEventSetDisplayDate = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numEventSetEarliestDate = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numEventSetLatestDate = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numEventSetPeriodName = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numEventSetDisplayPlace = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numEventSetPlaceGml = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numEventSetMaterisalsTech = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numRecodInfoSetLink = 0;
    /**
     * Tag count
     * タグ数
     *
     * @var int
     */
    private $numRecodInfoSetDate = 0;
    
    /**
     * Item language
     * アイテム言語
     *
     * @var string
     */
    private $item_language = 'ja';
    /**
     * Constructor
     * コンストラクタ
     * 
     * @param Session $Session Session management objects Session管理オブジェクト
     * @param Dbobject $Db Database management objects データベース管理オブジェクト
     */
    public function __construct($Session, $Db)
    {
        parent::Repository_Oaipmh_FormatAbstract($Session, $Db);
    }
    
    
    /**
     * init member for count
     * 初期化
     */
    private function initMember()
    {
        $this->numObjectWorkTypeConceptID = 0;
        $this->numObjectWorkTypeTerm = 0;
        $this->numClassificationConceptID = 0;
        $this->numClassificationTerm = 0;
        $this->numReposotorySetName = 0;
        $this->numReposotorySetLink = 0;
        $this->numReposotorySetID = 0;
        $this->numEventSetDisplayEvent = 0;
        $this->numEventSetTypeTerm = 0;
        $this->numEventSetActor = 0;
        $this->numEventSetDisplayDate = 0;
        $this->numEventSetEarliestDate = 0;
        $this->numEventSetLatestDate = 0;
        $this->numEventSetPeriodName = 0;
        $this->numEventSetDisplayPlace = 0;
        $this->numEventSetPlaceGml = 0;
        $this->numEventSetMaterisalsTech = 0;
        $this->numRecodInfoSetLink = 0;
        $this->numRecodInfoSetDate = 0;
    }

    /**
     * output OAI-PMH metadata Tag format LIDO
     * LIDO形式のOAI-PMHを出力
     *
     * @param array $itemData Item information アイテム情報
     *                        array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr_type"][$ii]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * 
     * @return string Output string 出力文字列
     */
    public function outputRecord($itemData)
    {
        $this->initMember();
        
        // confirm input item data
        if( !isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM]) || 
            !isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_TYPE]) )
        {
            return '';
        }
        
        // initialize DOM Document
        $this->domDocument = new DOMDocument('1.0', 'UTF-8');
        
        // output header
        $domElement = $this->outputHeader();
        
        // output item Metadata
        $this->outputMetadata($itemData, $domElement);
        
        // comfirm required item
        $lidoRecId = $this->domDocument->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_RECORD_SOURCE);
        $objectWorkTypes = $this->domDocument->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_OBJECT_WORK_TYPE);
        $existObjectWorkType = false;
        foreach($objectWorkTypes as $objectWorkType)
        {
            $objectWorkTypeChildren = $objectWorkType->childNodes;
            $item_one = $objectWorkTypeChildren->item(0);
            $item_two = $objectWorkTypeChildren->item(1);
            if($objectWorkTypeChildren->length === 2 && strlen($item_one->nodeValue) > 0 && strlen($item_two->nodeValue) > 0)
            {
                $existObjectWorkType = true;
                break;
            }
        }
        $titleSet = $this->domDocument->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_TITLE_SET);
        $recordId = $this->domDocument->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_RECORD_ID);
        $recordType = $this->domDocument->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_RECORD_TYPE);
        $recordSource = $this->domDocument->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_RECORD_SOURCE);
        if( ($lidoRecId->length === 0) || 
            ($objectWorkTypes->length === 0) || 
            ($existObjectWorkType === false) || 
            ($titleSet->length === 0) || 
            ($recordId->length === 0) || 
            ($recordType->length === 0) || 
            ($recordSource->length === 0) )
        {
            return '';
        }
        
        // convert DOMDocument to XML string
        $xml = $this->domDocument->saveXML();
        
        // delete '<\?xml version="1.0" encoding="UTF-8"\?\>\n'
        $xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml);
        
        // return
        return $xml;
    }
    
    
    /**
     * output header
     * ヘッダ出力
     */
    private function outputHeader()
    {
        // create <lido:lidoWrap> tag
        // add attribute xmlns:lido="http://www.lido-schema.org"
        $domElement = $this->domDocument->createElementNS(RepositoryConst::LIDO_SCHEMA_ORG, RepositoryConst::LIDO_TAG_LIDO_WRAP);
        // add attribute xmlns:gml="http://www.opengis.net/gml"
        //$domElement = $this->domDocument->createElementNS(RepositoryConst::GML_SCHEMA, RepositoryConst::LIDO_TAG_LIDO_WRAP);
        // add attribute xmlns:xsi="http://www.w3.org/2001/XMLSchemainstance"
        $domElement->setAttribute('xmlns:xsi', RepositoryConst::LIDO_XML_SCHEMAINSTANCE);
        // add attribute xsi:schemaLocation="http://www.lido-schema.org http://www.lido-schema.org/schema/v1.0/lido-v1.0.xsd"
        $domElement->setAttribute('xsi:schemaLocation', RepositoryConst::LIDO_SCHEMA_ORG.' '.RepositoryConst::LIDO_SCHEMA_XSD.' '.RepositoryConst::GML_SCHEMA.' '.RepositoryConst::GML_SCHEMA_XSD);
        
        // add DOM Element to member DOM Document
        $this->domDocument->appendChild($domElement);
        
        // add element <lido:lido>
        $lidoTagElement = $this->domDocument->createElement(RepositoryConst::LIDO_TAG_LIDO_LIDO);
        $lidoElement = $domElement->appendChild($lidoTagElement);
        
        // return
        return $lidoElement;
    }
    
    /**
     * output metadata
     * メタデータ出力
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
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputMetadata($itemData, $domElement)
    {
        // set language
        $this->item_language = $itemData['item'][0]['language'];
        if($this->item_language === RepositoryConst::ITEM_LANG_JA)
        {
            $this->item_language = RepositoryConst::LIDO_LANG_JAPANESE;
        }
        
        // set basic metadata
        // weko id
        $id_array = array('value' => $itemData['item'][0]['item_id'], 'language' => '');
        $this->outputTag(RepositoryConst::LIDO_TAG_LIDO_REC_ID, $id_array, $domElement);
        // title
        if(strlen($itemData['item'][0]['title']) >0)
        {
            $title_array = array('value' => $itemData['item'][0]['title'], 'language' => RepositoryConst::LIDO_LANG_JAPANESE);
            $this->outputTag(RepositoryConst::LIDO_FULLNAME_TITLESET, $title_array, $domElement);
        }
        // title_english
        if(strlen($itemData['item'][0]['title_english']) >0)
        {
            $title_english_array = array('value' => $itemData['item'][0]['title_english'], 'language' => RepositoryConst::LIDO_LANG_ENGLISH);
            $this->outputTag(RepositoryConst::LIDO_FULLNAME_TITLESET, $title_english_array, $domElement);
        }
        // search_key
        if(strlen($itemData['item'][0]['serch_key']) >0)
        {
            $search_key_array = array('value' => $itemData['item'][0]['serch_key'], 'language' => RepositoryConst::LIDO_LANG_JAPANESE);
            $this->outputTag(RepositoryConst::LIDO_FULLNAME_DISPLAYSUBJECT, $search_key_array, $domElement);
        }
        // search_key_english
        if(strlen($itemData['item'][0]['serch_key_english']) >0)
        {
            $search_key_english_array = array('value' => $itemData['item'][0]['serch_key_english'], 'language' => RepositoryConst::LIDO_LANG_ENGLISH);
            $this->outputTag(RepositoryConst::LIDO_FULLNAME_DISPLAYSUBJECT, $search_key_english_array, $domElement);
        }
        
        // uri
        $uri_array = array('value' => $itemData['item'][0]['uri'], 'language' => '');
        $this->outputTag(RepositoryConst::LIDO_FULLNAME_RECOURDINFOLINK, $uri_array, $domElement);
        // mod_date
        $mod_date_array = array('value' => $itemData['item'][0]['mod_date'], 'language' => '');
        $this->outputTag(RepositoryConst::LIDO_FULLNAME_RECORDMETADATADATE, $mod_date_array, $domElement);
        // set mapping info
        for($cnt = 0; $cnt < count($itemData['item_attr_type']); $cnt++)
        {
            $this->setAttributeValue($itemData['item_attr_type'][$cnt], $itemData['item_attr'][$cnt], $domElement);
        }
        
        $this->createGml();
    }
    
    /**
     * set attribute value
     * 属性設定
     * 
     * @param array $itemAttrType Metadata information メタデータ項目情報
     *                            array[$ii]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $itemAttr Item data アイテムデータ
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
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function setAttributeValue($itemAttrType, $itemAttr, $domElement)
    {
        // get lido tag full name
        $tag_full_name = $itemAttrType['lido_mapping'];
        if(strlen($tag_full_name) < 1)
        {
            return;
        }
        
        foreach($itemAttr as $attribute)
        {
            $output_value = RepositoryOutputFilter::attributeValue($itemAttrType, $attribute);
            if(strlen($output_value) > 0)
            {
                $metadata_array = array();
                $metadata_array['value'] = $output_value;
                // check language
                if(isset($itemAttrType['display_lang_type']) && strlen($itemAttrType['display_lang_type']) > 0)
                {
                    if($itemAttrType['display_lang_type'] == 'japanese')
                    {
                        $metadata_array['language'] = RepositoryConst::LIDO_LANG_JAPANESE;
                    }
                    else
                    {
                        $metadata_array['language'] = RepositoryConst::LIDO_LANG_ENGLISH;
                    }
                }
                // output
                $this->outputTag($tag_full_name, $metadata_array, $domElement);
            }
        }
    }
    
    /**
     * output Tag
     * タグ出力
     * 
     * @param string $tag_name Tag name タグ名
     * @param array $metadata_array Metadata list メタデータ一覧
     *                              array[$ii]["value"|"type"]
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputTag($tag_name, $metadata_array, $domElement)
    {
        // when value or tag is empty , exit
        if(strlen($metadata_array['value']) < 1 || strlen($tag_name) < 1)
        {
            return;
        }
        $metadata_array['value'] = $this->RepositoryAction->forXmlChange($metadata_array['value']);
        
        // output tag 
        switch($tag_name)
        {
            case RepositoryConst::LIDO_FULLNAME_OBJECTWORKTYPE_CONCEPTID:
                $metadata_array['type'] = RepositoryConst::LIDO_ATTRIBUTE_TYPE_URI;
            case RepositoryConst::LIDO_FULLNAME_OBJECTWORKTYPE_TERM:
                $this->outputObjectWorkTypeTag($tag_name, $metadata_array, $domElement);
                break;
            case RepositoryConst::LIDO_FULLNAME_CLASSIFICATION_CONCEPTID:
                $metadata_array['type'] = RepositoryConst::LIDO_ATTRIBUTE_TYPE_URI;
            case RepositoryConst::LIDO_FULLNAME_CLASSIFICATION_TERM:
                $this->outputClassificationTag($tag_name, $metadata_array, $domElement);
                break;
            case RepositoryConst::LIDO_FULLNAME_REPOSITORYNAME_LEGALBODYNAME:
            case RepositoryConst::LIDO_FULLNAME_REPOSITORYNAME_LEGALBODYWEBLINK:
            case RepositoryConst::LIDO_FULLNAME_WORKID:
                $this->outputRepositorySetTag($tag_name, $metadata_array, $domElement);
                break;
            case RepositoryConst::LIDO_FULLNAME_DISPLAYEVENT:
            case RepositoryConst::LIDO_FULLNAME_EVENTTYPE:
            case RepositoryConst::LIDO_FULLNAME_DISPLAYACTORINROLE:
            case RepositoryConst::LIDO_FULLNAME_DISPLAYDATE:
            case RepositoryConst::LIDO_FULLNAME_EARLIESTDATE:
            case RepositoryConst::LIDO_FULLNAME_LATESTDATE:
            case RepositoryConst::LIDO_FULLNAME_PERIODNAME:
            case RepositoryConst::LIDO_FULLNAME_DISPLAYPLACE:
            case RepositoryConst::LIDO_FULLNAME_WORKID:
            case RepositoryConst::LIDO_FULLNAME_DISPLAYMATERIALSTECH:
                $this->outputEventSetTag($tag_name, $metadata_array, $domElement);
                break;
            case RepositoryConst::LIDO_FULLNAME_PLACE_GML:
                $this->outputNormalTag($tag_name, $metadata_array, $domElement);
                break;
            case RepositoryConst::LIDO_FULLNAME_RECOURDINFOLINK:
            case RepositoryConst::LIDO_FULLNAME_RECORDMETADATADATE:
                $this->outputRecordInfoSetTag($tag_name, $metadata_array, $domElement);
                break;
            case RepositoryConst::LIDO_FULLNAME_LINKRESOURCE:
                $this->outputLinkResourceTag($tag_name, $metadata_array, $domElement);
                break;
            case RepositoryConst::LIDO_TAG_LIDO_REC_ID:
            case RepositoryConst::LIDO_FULLNAME_RECORDID:
                $metadata_array['type'] = RepositoryConst::LIDO_ATTRIBUTE_TYPE_URI;
                $this->outputNormalTag($tag_name, $metadata_array, $domElement);
                break;
            default:
                $this->outputNormalTag($tag_name, $metadata_array, $domElement);
                break;
            
        }
    }
    
    /**
     * Output tag
     * 通常タグ出力
     * 
     * @param string $tag_name Tag name タグ名
     * @param array $metadata_array Metadata list メタデータ一覧
     *                              array[$ii]["value"|"type"]
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputNormalTag($tag_name, $metadata_array, $domElement)
    {
        // get tag names 
        $tags = explode('.', $tag_name);
        $domElem = $domElement;
        $count = 0;
        foreach($tags as $tag)
        {
            if($count == (count($tags) - 1))
            {
                break;
            }
            $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.$tag);
            if($elements->length === 0)
            {
                // create new tag
                $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.$tag, $domElem);
            }
            else
            {
                foreach($elements as $element)
                {
                    $domElem = $element;
                    if($domElem !== null)
                    {
                        break;
                    }
                }
            }
            $count++;
        }
        
        // create tag
        $this->outputValue($tags[(count($tags)-1)], $metadata_array, $domElem);
    }
    
    /**
     * output Object Work Type Tag
     * Object Work Typeタグ出力
     * 
     * @param string $tag_name Tag name タグ名
     * @param array $metadata_array Metadata list メタデータ一覧
     *                              array[$ii]["value"|"type"]
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputObjectWorkTypeTag($tag_name, $metadata_array, $domElement)
    {
        // divide by dot(.)
        $tags = explode('.', $tag_name);
        // set root element
        $domElem = $domElement;
        
        $count = 0;
        foreach($tags as $tag)
        {
            if($count === (count($tags) - 2))
            {
                break;
            }
            $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.$tag);
            if($elements->length === 0)
            {
                $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.$tag, $domElem);
            }
            else
            {
                foreach($elements as $element)
                {
                    $domElem = $element;
                    if($domElem !== null)
                    {
                        break;
                    }
                }
            }
            $count++;
        }
        // create input tag
        $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_OBJECT_WORK_TYPE);
        $createdTagNum = 0;
        if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_CONCEPT_ID)
        {
            $this->numObjectWorkTypeConceptID++;
            $createdTagNum = $this->numObjectWorkTypeConceptID;
        }
        else
        {
            $this->numObjectWorkTypeTerm++;
            $createdTagNum = $this->numObjectWorkTypeTerm;
        }
        if($elements->length < $createdTagNum)
        {
            $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_OBJECT_WORK_TYPE, $domElem);
        }
        else
        {
            $domElem = $elements->item($createdTagNum - 1);
        }
        // create tag
        $this->outputValue($tags[(count($tags)-1)], $metadata_array, $domElem);
    }
    
    
    /**
     * output Classification Tag
     * Classificationタグ出力
     * 
     * @param string $tag_name Tag name タグ名
     * @param array $metadata_array Metadata list メタデータ一覧
     *                              array[$ii]["value"|"type"]
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputClassificationTag($tag_name, $metadata_array, $domElement)
    {
        // divide by dot(.)
        $tags = explode('.', $tag_name);
        // set root element
        $domElem = $domElement;
        
        $count = 0;
        foreach($tags as $tag)
        {
            if($count === (count($tags) - 2))
            {
                break;
            }
            $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.$tag);
            if($elements->length === 0)
            {
                $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.$tag, $domElem);
            }
            else
            {
                foreach($elements as $element)
                {
                    $domElem = $element;
                    if($domElem !== null)
                    {
                        break;
                    }
                }
            }
            $count++;
        }
        // create input tag
        $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_CLASSIFICATION);
        $createdTagNum = 0;
        if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_CONCEPT_ID)
        {
            $this->numClassificationConceptID++;
            $createdTagNum = $this->numClassificationConceptID;
        }
        else
        {
            $this->numClassificationTerm++;
            $createdTagNum = $this->numClassificationTerm;
        }
        
        if($elements->length < $createdTagNum)
        {
            $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_CLASSIFICATION, $domElem);
        }
        else
        {
            $domElem = $elements->item($createdTagNum - 1);
        }
        // create tag
        $this->outputValue($tags[(count($tags)-1)], $metadata_array, $domElem);
    }
    
    /**
     * output Repository Set Tag
     * Repository Setタグ出力
     * 
     * @param string $tag_name Tag name タグ名
     * @param array $metadata_array Metadata list メタデータ一覧
     *                              array[$ii]["value"|"type"]
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputRepositorySetTag($tag_name, $metadata_array, $domElement)
    {
        // divide by dot(.)
        $tags = explode('.', $tag_name);
        // set root element
        $domElem = $domElement;
        
        $count = 0;
        foreach($tags as $tag)
        {
            if($count === 3)
            {
                break;
            }
            $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.$tag);
            if($elements->length === 0)
            {
                // create new tag
                $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.$tag, $domElem);
            }
            else
            {
                foreach($elements as $element)
                {
                    $domElem = $element;
                    if($domElem !== null)
                    {
                        break;
                    }
                }
            }
            $count++;
        }
        // create input tag
        $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_REPOSITORY_SET);
        $createdTagNum = 0;
        // count created tag's num
        if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_WORK_ID)
        {
            $this->numReposotorySetID++;
            $createdTagNum = $this->numReposotorySetID;
        }
        else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_LEGAL_BODY_WEB_LINK)
        {
            $this->numReposotorySetLink++;
            $createdTagNum = $this->numReposotorySetLink;
        }
        else
        {
            $this->numReposotorySetName++;
            $createdTagNum = $this->numReposotorySetName;
        }
        
        if($elements->length < $createdTagNum)
        {
            // create <lido:repoisitorySet> tag
            $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_REPOSITORY_SET, $domElem);
            
            if($tags[(count($tags)-1)] !== RepositoryConst::LIDO_TAG_WORK_ID)
            {
                // create <lido:repoisitoryName> tag
                $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_REPOSITORY_NAME, $domElem);
                
                if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_APPELLATION_VALUE)
                {
                    // create <lido:legalBodyName> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_LEGAL_BODY_NAME, $domElem);
                }
            }
        }
        else
        {
            $domElem = $elements->item($createdTagNum - 1);
            if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_LEGAL_BODY_WEB_LINK)
            {
                $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_REPOSITORY_NAME);
                $domElem = $elements->item(0);
            }
            else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_APPELLATION_VALUE)
            {
                $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_LEGAL_BODY_NAME);
                $domElem = $elements->item(0);
            }
        }
        $this->outputValue($tags[(count($tags)-1)], $metadata_array, $domElem);
    }
    
    /**
     * output Event Set Tag
     * Event Setタグ出力
     * 
     * @param string $tag_name Tag name タグ名
     * @param array $metadata_array Metadata list メタデータ一覧
     *                              array[$ii]["value"|"type"]
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputEventSetTag($tag_name, $metadata_array, $domElement)
    {
        // divide by dot(.)
        $tags = explode('.', $tag_name);
        // set root element
        $domElem = $domElement;
        // create <lido:eventWrap>
        $count = 0;
        foreach($tags as $tag)
        {
            if($count === 2)
            {
                break;
            }
            $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.$tag);
            if($elements->length === 0)
            {
                $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.$tag, $domElem);
            }
            else
            {
                $domElem = $elements->item(0);
            }
            $count++;
        }
        
        // get created num of <lido:event> tag
        $max_created_num_of_event = max($this->numEventSetTypeTerm, $this->numEventSetActor, $this->numEventSetDisplayDate, 
                                        $this->numEventSetEarliestDate, $this->numEventSetLatestDate, $this->numEventSetPeriodName, 
                                        $this->numEventSetDisplayPlace, $this->numEventSetPlaceGml, $this->numEventSetMaterisalsTech);
        // get created num of <lido:date> tag
        $max_created_num_of_date = max($this->numEventSetEarliestDate, $this->numEventSetLatestDate);
        // get created num of <lido:eventDate> tag
        $max_created_num_of_eventdate = max($this->numEventSetDisplayDate, $this->numEventSetEarliestDate, $this->numEventSetLatestDate);
        // get created num of <lido:eventPlace> tag
        $max_created_num_of_eventplace = max($this->numEventSetDisplayPlace, $this->numEventSetPlaceGml);
        
        $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_SET);
        $createdTagNum = 0;
        
        // count created tag's num
        if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_EVENT)
        {
            $this->numEventSetDisplayEvent++;
            $createdTagNum = $this->numEventSetDisplayEvent;
        }
        else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_TERM)
        {
            if($tags[(count($tags)-2)] === RepositoryConst::LIDO_TAG_EVENT_TYPE)
            {
                $this->numEventSetTypeTerm++;
                $createdTagNum = $this->numEventSetTypeTerm;
            }
            else
            {
                $this->numEventSetPeriodName++;
                $createdTagNum = $this->numEventSetPeriodName;
            }
        }
        else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_ACTOR_IN_ROLE)
        {
            $this->numEventSetActor++;
            $createdTagNum = $this->numEventSetActor;
        }
        else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_DATE)
        {
            $this->numEventSetDisplayDate++;
            $createdTagNum = $this->numEventSetDisplayDate;
        }
        else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_EARLIEST_DATE)
        {
            $this->numEventSetEarliestDate++;
            $createdTagNum = $this->numEventSetEarliestDate;
        }
        else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_LATEST_DATE)
        {
            $this->numEventSetLatestDate++;
            $createdTagNum = $this->numEventSetLatestDate;
        }
        else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_PLACE)
        {
            $this->numEventSetDisplayPlace++;
            $createdTagNum = $this->numEventSetDisplayPlace;
        }
        else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_GML)
        {
            $this->numEventSetPlaceGml++;
            $createdTagNum = $this->numEventSetPlaceGml;
        }
        else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_MATERIALS_TECH)
        {
            $this->numEventSetMaterisalsTech++;
            $createdTagNum = $this->numEventSetMaterisalsTech;
        }
        
        // create new eventSet
        if($elements->length < $createdTagNum)
        {
            // create <lido:eventSet> tag
            $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_SET, $domElem);
            
            if($tags[(count($tags)-1)] !== RepositoryConst::LIDO_TAG_DISPLAY_EVENT)
            {
                // create <lido:event> tag
                $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT, $domElem);
                
                if($tags[(count($tags)-2)] === RepositoryConst::LIDO_TAG_EVENT_TYPE)
                {
                    // create <lido:evetType> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_TYPE, $domElem);
                }
                else if($tags[(count($tags)-2)] === RepositoryConst::LIDO_TAG_EVENT_ACTOR)
                {
                    // create <lido:eventActor> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_ACTOR, $domElem);
                }
                else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_DATE ||
                        $tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_EARLIEST_DATE || 
                        $tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_LATEST_DATE)
                {
                    // create <lido:eventDate> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_DATE, $domElem);
                    
                    if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_EARLIEST_DATE || 
                            $tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_LATEST_DATE)
                    {
                        // create <lido:date> tag
                        $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_DATE, $domElem);
                    }
                }
                else if($tags[(count($tags)-2)] === RepositoryConst::LIDO_TAG_PERIOD_NAME)
                {
                    // create <lido:periodName> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_PERIOD_NAME, $domElem);
                }
                else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_PLACE || 
                        $tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_GML )
                {
                    // create <lido:eventPlace> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_PLACE, $domElem);
                    
                    if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_GML)
                    {
                        // create <lido:place> tag
                        $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_PLACE, $domElem);
                    }
                }
                else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_MATERIALS_TECH)
                {
                    // create <lido:eventMaterialsTech> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_MATERIALS_TECH, $domElem);
                }
            }
        }
        else
        {
            $domElem = $elements->item($createdTagNum - 1);
        
            if($tags[(count($tags)-1)] !== RepositoryConst::LIDO_TAG_DISPLAY_EVENT)
            {

                if($max_created_num_of_event < $createdTagNum)
                {
                    // create <lido:event> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT, $domElem);
                }
                else
                {
                    $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT);
                    foreach($elements as $element)
                    {
                        $domElem = $element;
                        if($domElem !== null)
                        {
                            break;
                        }
                    }
                }
                
                if($tags[(count($tags)-2)] === RepositoryConst::LIDO_TAG_EVENT_TYPE)
                {
                    // create <lido:evetType> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_TYPE, $domElem);
                }
                else if($tags[(count($tags)-2)] === RepositoryConst::LIDO_TAG_EVENT_ACTOR)
                {
                    // create <lido:eventActor> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_ACTOR, $domElem);
                }
                else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_DATE ||
                        $tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_EARLIEST_DATE || 
                        $tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_LATEST_DATE)
                {
                    
                    if($max_created_num_of_eventdate < $createdTagNum)
                    {
                        // create <lido:eventDate> tag
                        $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_DATE, $domElem);
                    }
                    else
                    {
                        $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_DATE);
                        foreach($elements as $element)
                        {
                            $domElem = $element;
                            if($domElem !== null)
                            {
                                break;
                            }
                        }
                    }
                    
                    if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_EARLIEST_DATE || 
                            $tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_LATEST_DATE)
                    {
                        
                        if($max_created_num_of_date < $createdTagNum)
                        {
                            // create <lido:date> tag
                            $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_DATE, $domElem);
                        }
                        else
                        {
                            $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_DATE);
                            foreach($elements as $element)
                            {
                                $domElem = $element;
                                if($domElem !== null)
                                {
                                    break;
                                }
                            }
                        }
                    }
                }
                else if($tags[(count($tags)-2)] === RepositoryConst::LIDO_TAG_PERIOD_NAME)
                {
                    // create <lido:periodName> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_PERIOD_NAME, $domElem);
                }
                else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_PLACE || 
                        $tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_GML )
                {
                    
                    if($max_created_num_of_eventplace < $createdTagNum)
                    {
                        // create <lido:eventPlace> tag
                        $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_PLACE, $domElem);
                    }
                    else
                    {
                        $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_PLACE);
                        foreach($elements as $element)
                        {
                            $domElem = $element;
                            if($domElem !== null)
                            {
                                break;
                            }
                        }
                    }
                    
                    if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_GML)
                    {
                        // create <lido:place> tag
                        $node = $this->domDocument->createElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_PLACE);
                        $domElem->appendChild($node);
                        $domElem = $node;
                    }
                }
                else if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_DISPLAY_MATERIALS_TECH)
                {
                    // create <lido:eventMaterialsTech> tag
                    $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_EVENT_MATERIALS_TECH, $domElem);
                }
            }
        }
        
        $this->outputValue($tags[(count($tags)-1)], $metadata_array, $domElem);
    }
    
    /**
     * output RecordInfoSet Tag
     * RecordInfoSetタグ出力
     * 
     * @param string $tag_name Tag name タグ名
     * @param array $metadata_array Metadata list メタデータ一覧
     *                              array[$ii]["value"|"type"]
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputRecordInfoSetTag($tag_name, $metadata_array, $domElement)
    {
        // divide by dot(.)
        $tags = explode('.', $tag_name);
        // set root element
        $domElem = $domElement;
        
        $count = 0;
        foreach($tags as $tag)
        {
            if($count === 2)
            {
                break;
            }
            $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.$tag);
            if($elements->length === 0)
            {
                // create new tag
                $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.$tag, $domElem);
            }
            else
            {
                foreach($elements as $element)
                {
                    $domElem = $element;
                    if($domElem !== null)
                    {
                        break;
                    }
                }
            }
            $count++;
        }
        // create input tag
        $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_RECORD_INFO_SET);
        $createdTagNum = 0;
        if($tags[(count($tags)-1)] === RepositoryConst::LIDO_TAG_RECORD_INFO_LINK)
        {
            $this->numRecodInfoSetLink++;
            $createdTagNum = $this->numRecodInfoSetLink;
        }
        else
        {
            $this->numRecodInfoSetDate++;
            $createdTagNum = $this->numRecodInfoSetDate;
        }
        if($elements->length < $createdTagNum)
        {
            // create new tag
            $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_RECORD_INFO_SET, $domElem);
        }
        else
        {
            $domElem = $elements->item($createdTagNum - 1);
        }
        // create tag
        $this->outputValue($tags[(count($tags)-1)], $metadata_array, $domElem);
    }
    
    /**
     * output LinkResource Tag
     * LinkResourceタグ出力
     * 
     * @param string $tag_name Tag name タグ名
     * @param array $metadata_array Metadata list メタデータ一覧
     *                              array[$ii]["value"|"type"]
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputLinkResourceTag($tag_name, $metadata_array, $domElement)
    {
        // divide by dot(.)
        $tags = explode('.', $tag_name);
        // set root element
        $domElem = $domElement;
        
        $count = 0;
        foreach($tags as $tag)
        {
            if($count === (count($tags) - 2))
            {
                break;
            }
            $elements = $domElem->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.$tag);
            if($elements->length === 0)
            {
                // create new tag
                $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.$tag, $domElem);
            }
            else
            {
                foreach($elements as $element)
                {
                    $domElem = $element;
                    if($domElem !== null)
                    {
                        break;
                    }
                }
            }
            $count++;
        }
        // create input tag
        $domElem = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_RESOURCE_REPRESENTATION, $domElem);
        
        $this->outputValue($tags[(count($tags)-1)], $metadata_array, $domElem);
    }
    
    /**
     * output value
     * 値を出力する
     * 
     * @param string $tag_name Tag name タグ名
     * @param array $metadata_array Metadata list メタデータ一覧
     *                              array[$ii]["value"|"type"]
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputValue($tag_name, $metadata_array, $domElement)
    {
        // when value is BLANK WORD
        if($metadata_array['value'] == $this->RepositoryAction->forXmlChange(RepositoryConst::BLANK_WORD))
        {
            return;
        }
        // if exist language value
        else
        {
            $node = $this->domDocument->createElement(RepositoryConst::LIDO_TAG_NAMESPACE.$tag_name, $metadata_array['value']);
            $newnode = $domElement->appendChild($node);
            if(isset($metadata_array['language']) && strlen($metadata_array['language']) > 0)
            {
                $newnode->setAttribute(RepositoryConst::LIDO_ATTR_XML_LANG, $metadata_array['language']);
            }
            if(isset($metadata_array['type']) && strlen($metadata_array['type']) > 0)
            {
                $newnode->setAttribute(RepositoryConst::LIDO_ATTR_XML_TYPE, $metadata_array['type']);
            }
        }
    }
    
    /**
     * create element and return new element
     * DOMElementオブジェクトを作成する
     * 
     * @param string $tag_name Tag name タグ名
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     * @return DOMElement Create result 生成結果
     */
    private function createNodeElement($tag_name, $domElement)
    {
        $child = $this->domDocument->createElement($tag_name);
        $newnode = $domElement->appendChild($child);
        
        if($tag_name === RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_DESCRIPTIVE_METADATA || 
           $tag_name === RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_ADMINISTRATIVE_METADATA )
        {
            $newnode->setAttribute(RepositoryConst::LIDO_ATTR_XML_LANG, $this->item_language);
            
            // その他の言語対応語は以下を使用
            /*
            if($this->item_language === RepositoryConst::ITEM_LANG_OTHER)
            {
                $lang = $this->Session->getParameter("_lang");
                if($lang === 'japanese')
                {
                    $newnode->setAttribute(RepositoryConst::LIDO_ATTR_XML_LANG, RepositoryConst::LIDO_LANG_JAPANESE);
                }
                else
                {
                    $newnode->setAttribute(RepositoryConst::LIDO_ATTR_XML_LANG, RepositoryConst::LIDO_LANG_ENGLISH);
                }
            }
            else
            {
                $newnode->setAttribute(RepositoryConst::LIDO_ATTR_XML_LANG, $this->item_language);
            }
            */
        }
        
        $domElement = $child;
        
        return $domElement;
    }
    
    /**
     * create gml tags
     * GMLタグ作成
     */
    private function createGml()
    {
        $places = $this->domDocument->getElementsByTagName(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_PLACE);
        
        foreach($places as $place)
        {
            $gmls = $place->childNodes;
            if($gmls->length > 1)
            {
                $gml_values = '';
                $length = $gmls->length;
                for($cnt = 0; $cnt < $length; $cnt++)
                {
                    $gml = $gmls->item(0);
                    $gml_values .= $gml->nodeValue.RepositoryConst::XML_LF;
                    $place->removeChild($gml);
                }
                $gmlElement = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_GML, $place);
                
                $node = $this->domDocument->createElementNS(RepositoryConst::GML_SCHEMA, RepositoryConst::GML_TAG_NAMESPACE.RepositoryConst::GML_TAG_POLYGON);
                $newnode = $gmlElement->appendChild($node);
                $polygonElement = $node;
                
                //$polygonElement = $this->createNodeElement(RepositoryConst::GML_TAG_POLYGON, $gmlElement);
                $exteriorElement = $this->createNodeElement(RepositoryConst::GML_TAG_NAMESPACE.RepositoryConst::GML_TAG_EXTERIOR, $polygonElement);
                $linearRingElement = $this->createNodeElement(RepositoryConst::GML_TAG_NAMESPACE.RepositoryConst::GML_TAG_LINEAR_RING, $exteriorElement);
                $metadata_array = array('value' => $gml_values, 'language' => '');
                $this->outputGmlValue(RepositoryConst::GML_TAG_NAMESPACE.RepositoryConst::GML_TAG_COORDINATES, $metadata_array, $linearRingElement);
            }
            else if($gmls->length === 1)
            {
                $gml = $gmls->item(0);
                $value = $gml->nodeValue;
                $place->removeChild($gml);
                $gmlElement = $this->createNodeElement(RepositoryConst::LIDO_TAG_NAMESPACE.RepositoryConst::LIDO_TAG_GML, $place);
                
                $node = $this->domDocument->createElementNS(RepositoryConst::GML_SCHEMA, RepositoryConst::GML_TAG_NAMESPACE.RepositoryConst::GML_TAG_POINT);
                $newnode = $gmlElement->appendChild($node);
                $pointElement = $node;
                
                //$polygonElement = $this->createNodeElement(RepositoryConst::GML_TAG_POINT, $gmlElement);
                $metadata_array = array('value' => $value, 'language' => '');
                $this->outputGmlValue(RepositoryConst::GML_TAG_NAMESPACE.RepositoryConst::GML_TAG_POS, $metadata_array, $pointElement);
            }
        }
    }
    
    /**
     * output gml value
     * GML値を出力
     * 
     * @param string $tag_name Tag name タグ名
     * @param array $metadata_array Metadata list メタデータ一覧
     *                              array[$ii]["value"|"type"]
     * @param DOMElement $domElement DOMElement object DOMElementオブジェクト
     */
    private function outputGmlValue($tag_name, $metadata_array, $domElement)
    {
        // when value is BLANK WORD
        if($metadata_array['value'] == $this->RepositoryAction->forXmlChange(RepositoryConst::BLANK_WORD))
        {
            return;
        }
        // if exist language value
        else
        {
            $node = $this->domDocument->createElement($tag_name, $metadata_array['value']);
            $newnode = $domElement->appendChild($node);
            if(isset($metadata_array['language']) && strlen($metadata_array['language']) > 0)
            {
                $newnode->setAttribute(RepositoryConst::LIDO_ATTR_XML_LANG, $metadata_array['language']);
            }
            if(isset($metadata_array['type']) && strlen($metadata_array['type']) > 0)
            {
                $newnode->setAttribute(RepositoryConst::LIDO_ATTR_XML_TYPE, $metadata_array['type']);
            }
        }
    }
}
?>