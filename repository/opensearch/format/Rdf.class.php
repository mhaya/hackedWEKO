<?php

/**
 * Opensearch RDF format common classes
 * Opensearch RDF形式共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
// 
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Opensearch common format base class
 * Opensearch共通形式基底クラス
 */
require_once WEBAPP_DIR."/modules/repository/opensearch/format/FormatAbstract.class.php";

/**
 * Opensearch RDF format common classes
 * Opensearch RDF形式共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_OpenSearch_Rdf extends Repository_Opensearch_FormatAbstract
{
    /**
     * RDF name space
     * RDF名前空間
     *
     * @var string
     */
    const XMLNS_RDF         = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
    /**
     * OPENSEARCH name space
     * OPENSEARCH名前空間
     *
     * @var string
     */
    const XMLNS_OPENSEARCH  = "http://a9.com/-/spec/opensearch/1.1/";
    
    /**
     * Mapping schema name(Dublin core)
     * マッピングスキーマ名(Dublin core)
     *
     * @var string
     */
    const FORMAT_DUBLIN_CORE = "oai_dc";
    /**
     * Mapping schema name(junii2)
     * マッピングスキーマ名(junii2)
     *
     * @var string
     */
    const FORMAT_JUNII2 = "junii2";
    /**
     * Mapping schema name(lom)
     * マッピングスキーマ名(lom)
     *
     * @var string
     */
    const FORMAT_LOM = "oai_lom";
    
    /**
     * output metadata format class
     * 出力形式オブジェクト
     *   Repository_Oaipmh_DublinCore
     *   Repository_Oaipmh_JuNii2
     *   Repository_Oaipmh_LearningObjectMetadata
     *
     * @var Object
     */
    private $metadataClass = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $session Session management objects Session管理オブジェクト
     * @param Dbobject $db Database management objects データベース管理オブジェクト
     */
    public function __construct($session, $db)
    {
        parent::__construct($session, $db);
    }
    
    /**
     * Set format
     * 形式設定
     *
     * @param string $format Format 形式
     */
    public function setFormat($format)
    {
        switch ($format)
        {
            case self::FORMAT_DUBLIN_CORE:
                require_once WEBAPP_DIR. '/modules/repository/oaipmh/format/DublinCore.class.php';
                $this->metadataClass = new Repository_Oaipmh_DublinCore($this->Session, $this->Db);
                break;
            case self::FORMAT_JUNII2:
                require_once WEBAPP_DIR. '/modules/repository/oaipmh/format/JuNii2.class.php';
                $this->metadataClass = new Repository_Oaipmh_JuNii2($this->Session, $this->Db);
                break;
            case self::FORMAT_LOM:
                require_once WEBAPP_DIR. '/modules/repository/oaipmh/format/LearningObjectMetadata.class.php';
                $this->metadataClass = new Repository_Oaipmh_LearningObjectMetadata($this->Session, $this->Db);
                break;
            default:
                break;
        }
    }
    
    /**
     * make RDF XML for open search
     * RDF作成
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
        
        ///// set output data filter /////
        if(isset($request[self::REQUEST_DATA_FILTER]))
        {
            $this->metadataClass->setDataFilter($request[self::REQUEST_DATA_FILTER]);
        }
        
        ///// set data /////
        $xml = "";
        
        ///// header /////
        $xml .= $this->outputHeader();
        
        ///// header /////
        $xml .= $this->outputOpenSearchHeader($searchResult);
        
        ///// items /////
        $xml .= $this->outputItem($searchResult);
        
        ///// footer /////
        $xml .= $this->outputFooter();
        
        return $xml;
    }
    
    /**
     * output header
     * ヘッダ出力
     * 
     * @return string Output string 出力文字列
     */
    private function outputHeader()
    {
        $xml =  '<?xml version="1.0" encoding="UTF-8" ?>'.self::LF.
                '<rdf:RDF xmlns:opensearch="'.self::XMLNS_OPENSEARCH.'" '.
                '         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'.
                '         xmlns:rdf="'.self::XMLNS_RDF.'">'.self::LF;
        return $xml;
    }
    
    /**
     * output rdf header for opensearch
     * RDFヘッダ作成
     *
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
    private function outputOpenSearchHeader($searchResult)
    {
        $xml =  '<header>'.self::LF.
                '<opensearch:totalResults>'.$this->total.'</opensearch:totalResults>'.self::LF.
                '<opensearch:startIndex>'.$this->startIndex.'</opensearch:startIndex>'.self::LF.
                '<opensearch:itemsPerPage>'.count($searchResult).'</opensearch:itemsPerPage>'.self::LF.
                '</header>'.self::LF;
        return $xml;
    }
    
    /**
     * output items
     * アイテム情報出力
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
     * @return string Output string 出力文字列
     */
    private function outputItem($searchResult)
    {
        $xml = '';
        $xml .= '<items>'.self::LF;
        
        for($ii=0; $ii<count($searchResult); $ii++)
        {
            $itemData = array();
            $log = "";
            $ret = $this->RepositoryAction->getItemData($searchResult[$ii]["item_id"], 
                                                        $searchResult[$ii]["item_no"], 
                                                        $itemData, 
                                                        $log,
                                                        false,
                                                        true);
            if($ret)
            {
                $xml .= '<rdf:Description rdf:about="'.$this->RepositoryAction->forXmlChange($searchResult[$ii]["uri"]).'">'.self::LF;
                $xml .= $this->metadataClass->outputRecord($itemData);
                $xml .= '</rdf:Description>'.self::LF;
            }
        }
        
        $xml .= '</items>'.self::LF;
        return $xml;
    }
    
    /**
     * output footer
     * フッター出力
     *
     * @return string Output string 出力文字列
     */
    private function outputFooter()
    {
        $xml = '</rdf:RDF>';
        return $xml;
    }
}
?>