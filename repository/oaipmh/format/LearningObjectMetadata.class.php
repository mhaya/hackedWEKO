<?php
/**
 * Item information output class in LOM
 * LOMでのアイテム情報出力クラス
 *
 * @package WEKO
 */
// --------------------------------------------------------------------
//
// $Id: LearningObjectMetadata.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Item information output class in LOM
 * LOMでのアイテム情報出力クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LearningObjectMetadata extends Repository_Oaipmh_FormatAbstract
{
    /**
     * Tag variable
     * タグ変数
     *
     * @var Repository_Oaipmh_LOM_General
     */
    private $general = null;
    /**
     * Tag variable
     * タグ変数
     *
     * @var Repository_Oaipmh_LOM_Life
     */
    private $lifeCycle = null;
    /**
     * Tag variable
     * タグ変数
     *
     * @var Repository_Oaipmh_LOM_MetaMetadate
     */
    private $metaMetadate = null;
    /**
     * Tag variable
     * タグ変数
     *
     * @var Repository_Oaipmh_LOM_Technical
     */
    private $technical = null;
    
    /**
     * Tag variable
     * タグ変数
     *
     * @var array[$ii]
     */
    private $educational = array();
    /**
     * Tag variable
     * タグ変数
     *
     * @var Repository_Oaipmh_LOM_Rights
     */
    private $rights = null;
    /**
     * Tag variable
     * タグ変数
     *
     * @var array[$ii]
     */
    private $relation = array();
    /**
     * Tag variable
     * タグ変数
     *
     * @var array[$ii]
     */
    private $annotation = array();
    /**
     * Tag variable
     * タグ変数
     *
     * @var array[$ii]
     */
    private $classification = array();

    // const xml value
    /**
     * LOM version
     * LOMバージョン
     *
     * @var string
     */
    const LOM_VALUE_SOURCE = 'LOMv1.0';
    
    /**
     * Constructor
     * コンストラクタ
     * 
     * @param Session $Session Session management objects Session管理オブジェクト
     * @param Dbobject $Db Database management objects データベース管理オブジェクト
     */
    public function __construct($Session, $Db){
        parent::Repository_Oaipmh_FormatAbstract($Session, $Db);
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
        if( !isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM]) || 
            !isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_TYPE]) )
         //   基本情報以外のメタデータが存在しない場合に判定に入ってしまうことを防ぐためコメントアウト
         //   !isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR_TYPE]) || 
         //   !isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR]))
        {
            return '';
        }
        
        // new data class.
        $this->general = new Repository_Oaipmh_LOM_General($this->RepositoryAction);
        $this->lifeCycle = new Repository_Oaipmh_LOM_LifeCycle($this->RepositoryAction);
        $this->metaMetadate = new Repository_Oaipmh_LOM_MetaMetadata($this->RepositoryAction);
        $this->technical = new Repository_Oaipmh_LOM_Technical($this->RepositoryAction);
        //$this->educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
        $this->educational = array();
        $this->rights = new Repository_Oaipmh_LOM_Rights($this->RepositoryAction);
        $this->relation = array();
        $this->annotation = array();
        $this->classification = array();
        
        
        //1.基本情報設定処理
        //$this->setBaseData($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM][0]);
        $this->setBaseData($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM]);
        
        //2. マッピング情報設定処理
        $this->setMappingInfo($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR_TYPE], $itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_ATTR]);
        
        //3. リファレンス設定処理
        if(isset($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_REFERENCE]))
        {
            $this->setReference($itemData[RepositoryConst::ITEM_DATA_KEY_ITEM_REFERENCE]);
        }
        
        //4. 初期化
        $xml = '';
        
        
        //5. header出力処理
        $xml .= $this->outputHeader();
        
        //6. metadata出力処理
        $xml .= $this->general->output();
        $xml .= $this->lifeCycle->output();
        $xml .= $this->metaMetadate->output();
        $xml .= $this->technical->output();
        
        for($ii=0;$ii<count($this->educational);$ii++){
            $xml .= $this->educational[$ii]->output();
        }
        
        $xml .= $this->rights->output();
        
        for($ii=0;$ii<count($this->relation);$ii++){
            $xml .= $this->relation[$ii]->output();
        }
        
        for($ii=0;$ii<count($this->annotation);$ii++){
            $xml .= $this->annotation[$ii]->output();
        }
        
        for($ii=0;$ii<count($this->classification);$ii++){
            $xml .= $this->classification[$ii]->output();
        }
        
        //7. footer出力処理
        $xml .= $this->outputFooter();
        
        return $xml;
        
    }
    
    /**
     * Set item base information
     * 基本情報設定処理
     * 
     * @param array $item Item information アイテム情報
     *                    array[$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return boolean Result 結果
     */
    private function setBaseData($item){
        //1レコードのみ
        if(count($item) != 1){
            return false;
        }
        $itemData = $item[0];
        
        //言語チェック
        $language = RepositoryOutputFilter::language($itemData[RepositoryConst::DBCOL_REPOSITORY_ITEM_LANGUAGE]);

        //タイトル
        $title = '';
        $titleLang = $language;
        //日本語
        if($language == RepositoryConst::ITEM_LANG_JA){
            if(strlen($itemData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE]) > 0)
            {
                $title = $itemData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
            }
            else
            {
                $title = $itemData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
                $titleLang = '';
            }
        }
        //英語
        else {
            if(strlen($itemData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH]) > 0)
            {
                $title = $itemData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH];
            }
            else
            {
                $title = $itemData[RepositoryConst::DBCOL_REPOSITORY_ITEM_TITLE];
                $titleLang = '';
            }
        }
        $this->general->addTitle(new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $title, $titleLang));
        
        //language
        $this->general->addLanguage("$language");
        
        //URI
        $uri = new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, 
                                                    $itemData[RepositoryConst::DBCOL_REPOSITORY_ITEM_URI],
                                                    RepositoryConst::LOM_URI);
        $this->general->addIdentifier($uri);
        
        //キーワード
        $keyword = explode("|", $itemData[RepositoryConst::DBCOL_REPOSITORY_ITEM_SEARCH_KEY]."|".$itemData[RepositoryConst::DBCOL_REPOSITORY_ITEM_SEARCH_KEY_ENGLISH]);
        for($ii=0; $ii<count($keyword); $ii++)
        {
            $this->general->addKeyword(new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $keyword[$ii]));
        }
        
        return true;
    }
    
    /**
     * Set mapping information
     * マッピング情報設定処理
     * 
     * @param array $mapping Mapping information マッピング情報
     *                       array[$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata Metadata information メタデータ情報
     *                        array[$ii]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setMappingInfo($mapping, $metadata){
      for($ii=0;$ii<count($mapping);$ii++){
          if($mapping[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_HIDDEN] == 1)
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
          
          $lomMap = $mapping[$ii][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING];
          
          if(preg_match('/^general/', $lomMap)==1){
              $this->setGeneral($mapping[$ii], $metadata[$ii]);
          }else if(preg_match('/^lifeCycle/', $lomMap)==1){
              $this->setLifeCycle($mapping[$ii], $metadata[$ii]);
          }else if(preg_match('/^metaMetadata/', $lomMap)==1){
              $this->setMetaMetadata($mapping[$ii], $metadata[$ii]);
          }else if(preg_match('/^technical/', $lomMap)==1){
              $this->setTechnical($mapping[$ii], $metadata[$ii]);
          }else if(preg_match('/^educational/', $lomMap)==1){
              $this->setEducational($mapping[$ii], $metadata[$ii]);
          }else if(preg_match('/^rights/', $lomMap)==1){
              $this->setRights($mapping[$ii], $metadata[$ii]);
          }else if(preg_match('/^relation/', $lomMap)==1){
              $this->setRelation($mapping[$ii], $metadata[$ii]);
          }else if(preg_match('/^annotation/', $lomMap)==1){
              $this->setAnnotation($mapping[$ii], $metadata[$ii]);
          }else if(preg_match('/^classification/', $lomMap)==1){
              $this->setClassification($mapping[$ii], $metadata[$ii]);
          }else{
              //何もしない
          }
      }
    }
    
    /**
     * setGeneral
     * General設定
     *
     * @param array $mapping_item Mapping information マッピング情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata_item Metadata information メタデータ情報
     *                             array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                             array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                             array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                             array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                             array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                             array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                             array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setGeneral($mapping_item, $metadata_item){
        $language = $mapping_item[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];
        $lomMap = $mapping_item[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING];
        
        for($ii=0; $ii<count($metadata_item); $ii++){
            
            $value = RepositoryOutputFilter::attributeValue($mapping_item, $metadata_item[$ii], 2);
            
            switch($lomMap)
            {
                case RepositoryConst::LOM_MAP_GNRL_IDENTIFER:
                    $this->setGeneralIdentifier($mapping_item, $value);
                    break;
                case RepositoryConst::LOM_MAP_GNRL_TITLE:
                    break;
                case RepositoryConst::LOM_MAP_GNRL_LANGUAGE:
                    $this->general->addLanguage($value);
                    break;
                case RepositoryConst::LOM_MAP_GNRL_DESCRIPTION:
                    $description = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value, $language);
                    $this->general->addDescription($description);
                    break;
                case RepositoryConst::LOM_MAP_GNRL_KEYWORD:
                    $this->general->addKeyword(new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value));
                    break;
                case RepositoryConst::LOM_MAP_GNRL_COVERAGE:
                    $coverage = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value, $language);
                    $this->general->addCoverage($coverage);
                    break;
                case RepositoryConst::LOM_MAP_GNRL_STRUCTURE:
                    $structure = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    $this->general->addStructure($structure);
                    break;
                case RepositoryConst::LOM_MAP_GNRL_AGGREGATION_LEVEL:
                    $aggregationLevel = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    $this->general->addAggregationLevel($aggregationLevel);
                    break;
                default :
                    break;
            }
        }
    }
    
    /**
     * Set GeneralIdentifier
     * GeneralIdentifierの個別設定処理
     * @param array $mapping_item Mapping information マッピング情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param string $value Value 値
     * 
     */
    private function setGeneralIdentifier($mapping_item, $value){
        $attri_name = $mapping_item[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_NAME];
        $input_type = $mapping_item[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IMPUT_TYPE];
        $language  = $mapping_item[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];
        
        if($input_type == RepositoryConst::ITEM_ATTR_TYPE_BIBLIOINFO)
        {
            $biblio = explode("||", $value);
            // jtitle
            $identifier_jtitle = new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $biblio[0], RepositoryConst::LOM_JTITLE);
            $this->general->addIdentifier($identifier_jtitle);
            // volume
            $identifier_volume = new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $biblio[1], RepositoryConst::LOM_VOLUME);
            $this->general->addIdentifier($identifier_volume);
            // issue
            $identifier_issue = new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $biblio[2], RepositoryConst::LOM_ISSUE);
            $this->general->addIdentifier($identifier_issue);
            // spage
            $identifier_spage = new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $biblio[3], RepositoryConst::LOM_SPAGE);
            $this->general->addIdentifier($identifier_spage);
            // epage
            $identifier_epage = new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $biblio[4], RepositoryConst::LOM_EPAGE);
            $this->general->addIdentifier($identifier_epage);
            // dateofissued
            $identifier_dateofissued = new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $biblio[5], RepositoryConst::LOM_DATE_OF_ISSUED);
            $this->general->addIdentifier($identifier_dateofissued);
        }
        else if($attri_name == RepositoryConst::LOM_URI || $attri_name == RepositoryConst::LOM_ISSN 
           || $attri_name == RepositoryConst::LOM_NCID || $attri_name == RepositoryConst::LOM_TEXTVERSION )
        {
            $identifier = new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value, $attri_name);
            $this->general->addIdentifier($identifier);
        }else{
            $identifier = new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value);
            $this->general->addIdentifier($identifier);
        }
    }
    
    /**
     * setLifeCycle
     * LifeCycle設定
     * 
     * @param array $mapping_item Mapping information マッピング情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata_item Metadata information メタデータ情報
     *                             array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                             array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                             array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                             array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                             array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                             array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                             array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setLifeCycle($mapping_item, $metadata_item){
        $lomMap = $mapping_item[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING];
        
        //version
        if($lomMap == RepositoryConst::LOM_MAP_LFCYCL_VERSION){
            for($ii=0;$ii<count($metadata_item);$ii++)
            {
                $value = RepositoryOutputFilter::attributeValue($mapping_item, $metadata_item[$ii]);
                $this->lifeCycle->addVersion(new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value));
            }
        }
        //status
        else if($lomMap == RepositoryConst::LOM_MAP_LFCYCL_STATUS)
        {
            for($ii=0;$ii<count($metadata_item);$ii++)
            {
                $value = RepositoryOutputFilter::attributeValue($mapping_item, $metadata_item[$ii]);
                $this->lifeCycle->addStatus(new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value));
            }
            
        }
        //publishDateの場合
        else if($lomMap == RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_PUBLISH_DATE){
            for($ii=0;$ii<count($metadata_item);$ii++){
                $value = RepositoryOutputFilter::attributeValue($mapping_item, $metadata_item[$ii]);
                
                $contribute = new Repository_Oaipmh_LOM_Contribute($this->RepositoryAction);
                $contribute->addRole(new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_PUBLISH_DATE));
                $date = new Repository_Oaipmh_LOM_DateTime($this->RepositoryAction);
                $date->setDateTime($value);
                
                $contribute->addDate($date);
                $this->lifeCycle->addContribute($contribute, 1);
            }
        }
        else if($lomMap == RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE)
        {
            $roleValue = str_replace("lifecyclecontribute", "", strtolower($lomMap));
            $this->setLifeCycleContribute($mapping_item, $metadata_item, 0, $roleValue);
        }
        //author/publicher/initiator...
        else if(preg_match("/^lifeCycleContribute/", $lomMap)==1)
        {
            $roleValue = str_replace("lifecyclecontributerole", "", strtolower($lomMap));
            $roleValue = RepositoryOutputFilterLOM::lyfeCycleContributeRole($roleValue);
            
            $this->setLifeCycleContribute($mapping_item, $metadata_item, 1, $roleValue);
        }
    }
    
    /**
     * setLifeCycleContribute
     * LifeCycleContribute設定
     * 
     * @param array $mapping Mapping information マッピング情報
     *                       array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata Metadata information メタデータ情報
     *                        array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param boolean $flag Filter flag フィルターフラグ
     * @param string $roleValue Role value Role value
     */
    private function setLifeCycleContribute($mapping, $metadata, $flag, $roleValue=''){
        
        $contribute = new Repository_Oaipmh_LOM_Contribute($this->RepositoryAction);
        
        $contribute->addRole(new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $roleValue));
        
        //entryは複数出力する
        for($ii=0;$ii<count($metadata);$ii++){
            $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
            $contribute->addEntry($value);
        }
        $this->lifeCycle->addContribute($contribute, $flag);
    }
    
    
    /**
     * setMetaMetadata
     * MetaMetadata設定
     * 
     * @param array $mapping Mapping information マッピング情報
     *                       array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata Metadata information メタデータ情報
     *                        array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setMetaMetadata($mapping, $metadata)
    {
        $lomMap = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING];
        
        // metaMetadataContributeで始まるタグの場合
        if(preg_match('/^metaMetadataContribute/', $lomMap)==1){
            $contribute = new Repository_Oaipmh_LOM_Contribute($this->RepositoryAction);
            
            //roleはcreator/validatorのみ
            if($lomMap == RepositoryConst::LOM_MAP_MTMTDT_CONTRIBUTE)
            {
                $roleValue = "";
                $flag = 0;
            } else {
                $roleValue = str_replace("metametadatacontributerole", "", strtolower($lomMap));
                $flag = 1;
            }
            $contribute->addRole(new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $roleValue));
            
            //entryは複数表示する
            for($ii=0; $ii<count($metadata); $ii++)
            {
                $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                $contribute->addEntry($value);
            }
            
            $this->metaMetadate->addContribute($contribute, $flag);
        }
        // それ以外
        else{
            for($ii=0; $ii<count($metadata); $ii++)
            {
                $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                switch($lomMap)
                {
                    case RepositoryConst::LOM_MAP_MTMTDT_IDENTIFER:
                        $identifier = new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value);
                        $this->metaMetadate->addIdentifier($identifier);
                        break;
                    case RepositoryConst::LOM_MAP_MTMTDT_METADATA_SCHEMA:
                        $this->metaMetadate->addMetadataSchema(self::LOM_VALUE_SOURCE);
                        break;
                    case RepositoryConst::LOM_MAP_MTMTDT_LANGUAGE:
                        $this->metaMetadate->addLanguage($value);
                        break;
                    default :
                        break;
                }
            }
        }
    }
    
    /**
     * setTechnical
     * Technical設定
     * 
     * @param array $mapping Mapping information マッピング情報
     *                       array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata Metadata information メタデータ情報
     *                        array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setTechnical($mapping, $metadata)
    {
        $lomMap = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING];
        $language = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];

        for($ii=0; $ii<count($metadata); $ii++){
            $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
            switch($lomMap){
                case RepositoryConst::LOM_MAP_TCHNCL_FORMAT:
                    $this->technical->addFormat($value);
                    break;
                case RepositoryConst::LOM_MAP_TCHNCL_SIZE:
                    $this->technical->addSize($value);
                    break;
                case RepositoryConst::LOM_MAP_TCHNCL_LOCATION:
                    $this->technical->addLocation($value);
                    break;
                case RepositoryConst::LOM_MAP_TCHNCL_INSTALLATION_REMARKS:
                    $langstring = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value, $language);
                    $this->technical->addInstallationRemarks($langstring);
                    break;
                case RepositoryConst::LOM_MAP_TCHNCL_OTHER_PLATFORM_REQUIREMENTS:
                    $langstring = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value, $language);
                    $this->technical->addOtherPlatformRequirements($langstring);
                    break;
                case RepositoryConst::LOM_MAP_TCHNCL_DURATION:
                    $lang = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, '');
                    //$duration = new Repository_Oaipmh_LOM_Duration($this->RepositoryAction, $value, $lang);
                    $duration = new Repository_Oaipmh_LOM_Duration($this->RepositoryAction);
                    $duration->setDescription($lang);
                    $duration->setDuration($value);
                    
                    $this->technical->addDuration($duration);
                    break;
                // ----- technicalRequirement ----- 
                case RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_TYPE:
                    $type = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                      $this->technical->addOrComposite(RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_TYPE, $type);
                      
                    break;
                case RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_NAME:
                    $name = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    $this->technical->addOrComposite(RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_NAME, $name);
                      
                    break;
                case RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MINIMUM_VERSION:
                      $this->technical->addOrComposite(RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MINIMUM_VERSION, $value);
                      
                    break;
                case RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MAXIMUM_VERSION:
                      $this->technical->addOrComposite(RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MAXIMUM_VERSION, $value);
                      
                    break;
                default:break;
            }
            
        }
    }
    
    /**
     * setEducational
     * Educational設定
     * 
     * @param array $mapping Mapping information マッピング情報
     *                       array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata Metadata information メタデータ情報
     *                        array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setEducational($mapping, $metadata)
    {
        $lomMap = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING];
        $language = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];
        
        $index = -1;
        if($lomMap == RepositoryConst::LOM_MAP_EDUCTNL_LEARNING_RESOURCE_TYPE
         || $lomMap == RepositoryConst::LOM_MAP_EDUCTNL_INTENDED_END_USER_ROLE
         || $lomMap == RepositoryConst::LOM_MAP_EDUCTNL_CONTEXT
         || $lomMap == RepositoryConst::LOM_MAP_EDUCTNL_TYPICAL_AGE_RANGE
         || $lomMap == RepositoryConst::LOM_MAP_EDUCTNL_DESCRIPTION
         || $lomMap == RepositoryConst::LOM_MAP_EDUCTNL_LANGUAGE)
        {
            $index = $this->getInsertIndexEducational($lomMap);
        }
        
        for($ii=0; $ii<count($metadata); $ii++)
        {
            $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
            switch ($lomMap){
                //Repository_Oaipmh_LOM_Vocabulary型
                case RepositoryConst::LOM_MAP_EDUCTNL_INTERACTIVITY_TYPE:
                    $index = $this->getInsertIndexEducational(RepositoryConst::LOM_MAP_EDUCTNL_INTERACTIVITY_TYPE);
                    $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addInteractivityType($vocabulary);
                        array_push($this->educational, $educational);
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addInteractivityType($vocabulary);
                    }
                    break;
                case RepositoryConst::LOM_MAP_EDUCTNL_LEARNING_RESOURCE_TYPE:
                    $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addLearningResourceType($vocabulary);
                        array_push($this->educational, $educational);
                        $index = count($this->educational) - 1;
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addLearningResourceType($vocabulary);
                    }
                    
                    break;
                case RepositoryConst::LOM_MAP_EDUCTNL_INTERACTIVITY_LEVEL:
                    $index = $this->getInsertIndexEducational(RepositoryConst::LOM_MAP_EDUCTNL_INTERACTIVITY_LEVEL);
                    $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addInteractivityLevel($vocabulary);
                        array_push($this->educational, $educational);
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addInteractivityLevel($vocabulary);
                    }
                    break;
                case RepositoryConst::LOM_MAP_EDUCTNL_SEMANTIC_DENSITY:
                    $index = $this->getInsertIndexEducational(RepositoryConst::LOM_MAP_EDUCTNL_SEMANTIC_DENSITY);
                    $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addSemanticDensity($vocabulary);
                        array_push($this->educational, $educational);
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addSemanticDensity($vocabulary);
                    }
                    break;
                case RepositoryConst::LOM_MAP_EDUCTNL_INTENDED_END_USER_ROLE:
                    $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addIntendedEndUserRole($vocabulary);
                        array_push($this->educational, $educational);
                        $index = count($this->educational) - 1;
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addIntendedEndUserRole($vocabulary);
                    }
                    break;
                case RepositoryConst::LOM_MAP_EDUCTNL_CONTEXT:
                    $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addContext($vocabulary);
                        array_push($this->educational, $educational);
                        $index = count($this->educational) - 1;
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addContext($vocabulary);
                    }
                    break;
                case RepositoryConst::LOM_MAP_EDUCTNL_DIFFICULTY:
                    $index = $this->getInsertIndexEducational(RepositoryConst::LOM_MAP_EDUCTNL_DIFFICULTY);
                    $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addDifficulty($vocabulary);
                        array_push($this->educational, $educational);
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addDifficulty($vocabulary);
                    }
                    
                    break;
                    
                //Repository_Oaipmh_LOM_LangString型
                case RepositoryConst::LOM_MAP_EDUCTNL_TYPICAL_AGE_RANGE:
                    $typical = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value, $language);
                    
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addTypicalAgeRange($typical);
                        array_push($this->educational, $educational);
                        $index = count($this->educational) - 1;
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addTypicalAgeRange($typical);
                    }
                    break;
                case RepositoryConst::LOM_MAP_EDUCTNL_DESCRIPTION:
                    $description = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value, $language);
                    
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addDescription($description);
                        array_push($this->educational, $educational);
                        $index = count($this->educational) - 1;
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addDescription($description);
                    }
                    break;
                    
                //Repository_Oaipmh_LOM_Duration型
                case RepositoryConst::LOM_MAP_EDUCTNL_TYPICAL_LEARNING_TIME:
                    $index = $this->getInsertIndexEducational(RepositoryConst::LOM_MAP_EDUCTNL_TYPICAL_LEARNING_TIME);
                    $description = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, '', $language);
                    $typicalLearningTime = new Repository_Oaipmh_LOM_Duration($this->RepositoryAction);
                    $typicalLearningTime->setDescription($description);
                    $typicalLearningTime->setDuration($value);
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addTypicalLearningTime($typicalLearningTime);
                        array_push($this->educational, $educational);
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addTypicalLearningTime($typicalLearningTime);
                    }
                    break;
                    
                //直接追加
                case RepositoryConst::LOM_MAP_EDUCTNL_LANGUAGE:
                    //新規のとき
                    if($index == -1){
                        $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
                        $educational->addLanguage($value);
                        array_push($this->educational, $educational);
                        $index = count($this->educational) - 1;
                    }
                    //更新のとき
                    else{
                        $this->educational[$index]->addLanguage($value);
                    }
                    break;
                default:
                    break;
            }
        }
        
    }
    /**
     * getInsertIndexEducational
     * 配列Educationalに格納すべきインデックスを取得する
     *
     * @param string $element Element 要素
     * @return int index インデックス
     */
    public function getInsertIndexEducational($element){
        $index = 0;
        
        //はじめていれる場合
        if(count($this->educational) == 0){
            
            return -1;
        }
        
        $ii=0;
        //すでにはいっている場合
        for($ii=0;$ii<count($this->educational);$ii++)
        {
            if($element == RepositoryConst::LOM_MAP_EDUCTNL_INTERACTIVITY_TYPE){
                
                $inter_type = $this->educational[$ii]->getInteractivityType();
                //入っていなかったらここに入れる
                if($inter_type == null || strlen($inter_type->getValue())){
                    $index = $ii;
                    break;
                }
                
            }else if($element == RepositoryConst::LOM_MAP_EDUCTNL_LEARNING_RESOURCE_TYPE){
                $learning_resource_type = $this->educational[$ii]->getLearningResourceType();
                //入っていなかったらここに入れる
                if(count($learning_resource_type)==0){
                    $index = $ii;
                    break;
                }
                
            }else if($element == RepositoryConst::LOM_MAP_EDUCTNL_INTERACTIVITY_LEVEL){
                $inter_level = $this->educational[$ii]->getInteractivityLevel();
                //入っていなかったらここに入れる
                if($inter_level == null || strlen($inter_level->getValue())){
                    $index = $ii;
                    break;
                }
            }else if($element == RepositoryConst::LOM_MAP_EDUCTNL_SEMANTIC_DENSITY){
                $semantic = $this->educational[$ii]->getSemanticDensity();
                //入っていなかったらここに入れる
                if($semantic == null || strlen($semantic->getValue())){
                    $index = $ii;
                    break;
                }
            }else if($element == RepositoryConst::LOM_MAP_EDUCTNL_INTENDED_END_USER_ROLE){
                $endUserRole = $this->educational[$ii]->getIntendedEndUserRole();
                //入っていなかったらここに入れる
                if(count($endUserRole)==0){
                    $index = $ii;
                    break;
                }
            }else if($element == RepositoryConst::LOM_MAP_EDUCTNL_CONTEXT){
                $context = $this->educational[$ii]->getContext();
                //入っていなかったらここに入れる
                if(count($context)==0){
                    $index = $ii;
                    break;
                }
            }else if($element == RepositoryConst::LOM_MAP_EDUCTNL_DIFFICULTY){
                $difficulty = $this->educational[$ii]->getDifficulty();
                //入っていなかったらここに入れる
                if($difficulty == null || strlen($difficulty->getValue())){
                    $index = $ii;
                    break;
                }
            }else if($element == RepositoryConst::LOM_MAP_EDUCTNL_TYPICAL_AGE_RANGE){
                $typicalAgeRange = $this->educational[$ii]->getTypicalAgeRange();
                //入っていなかったらここに入れる
                if(count($typicalAgeRange)==0){
                    $index = $ii;
                    break;
                }
            }else if($element == RepositoryConst::LOM_MAP_EDUCTNL_DESCRIPTION){
                $description = $this->educational[$ii]->getDescription();
                //入っていなかったらここに入れる
                if(count($description)==0){
                    $index = $ii;
                    break;
                }
            }else if($element == RepositoryConst::LOM_MAP_EDUCTNL_TYPICAL_LEARNING_TIME){
                $rypical_learning_time = $this->educational[$ii]->getTypicalLearningTime();
                //入っていなかったらここに入れる
                if($rypical_learning_time == null || strlen($rypical_learning_time->getDuration())==0){
                    $index = $ii;
                    break;
                }
            }else if($element == RepositoryConst::LOM_MAP_EDUCTNL_LANGUAGE){
                $lang = $this->educational[$ii]->getLanguage();
                //入っていなかったらここに入れる
                if(count($lang)==0 ){
                    $index = $ii;
                    break;
                }
            }
            
            
            else {
                return;
            }
        }
        
        //どこにも入るところがない場合
        if($ii!=0 && $ii == count($this->educational)){
            //新しく作成する
            $educational = new Repository_Oaipmh_LOM_Educational($this->RepositoryAction);
            array_push($this->educational, $educational);
            return $ii;
        }
        
        return $index;
    }
    
    /**
     * setRights
     * Rights設定
     * 
     * @param array $mapping Mapping information マッピング情報
     *                       array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata Metadata information メタデータ情報
     *                        array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setRights($mapping, $metadata)
    {
        $lomMap = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING];
        $language = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];
        
        for($ii=0; $ii<count($metadata); $ii++)
        {
            $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
            switch ($lomMap){
                //Repository_Oaipmh_LOM_Vocabulary型
                case RepositoryConst::LOM_MAP_RGHTS_COST:
                    $cost = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    $this->rights->addCost($cost);
                    break;
                case RepositoryConst::LOM_MAP_RGHTS_COPYRIGHT_AND_OTHER_RESTRICTIONS:
                    $copy = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    $this->rights->addCopyrightAndOtherRestrictions($copy);
                    break;
                    
                //Repository_Oaipmh_LOM_LangString型
                case RepositoryConst::LOM_MAP_RGHTS_DESCRIPTION:
                    $description = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value, $language);
                    $this->rights->addDescription($description);
                    break;
                    
                default:
                    break;
            }
        }
        
    }
    /**
     * setRelation
     * Relation設定
     * 
     * @param array $mapping Mapping information マッピング情報
     *                       array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata Metadata information メタデータ情報
     *                        array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setRelation($mapping, $metadata)
    {
        $relation = new Repository_Oaipmh_LOM_Relation($this->RepositoryAction);
        
        $lomMap = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING];
        $language = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];
        
        switch ($lomMap){
            case RepositoryConst::LOM_MAP_RLTN:
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                //pmid,doiのcatalogはデータ落ちする
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_IS_PART_OF:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_IS_PART_OF);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_HAS_PART_OF:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_HAS_PART);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_IS_VERSION_OF:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_IS_VERSION_OF);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_HAS_VERSION:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_HAS_VERSION);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_IS_FORMAT_OF:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_IS_FORMAT_OF);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_HAS_FORMAT:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_HAS_FORMAT);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_REFERENCES:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_REFERENCES);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_IS_REFERENCED_BY:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_IS_REFERENCED_BY);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_IS_BASED_ON:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_IS_BASED_ON);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value, RepositoryConst::LOM_IS_BASED_ON));
                    
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_IS_BASIS_FOR:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_IS_BASIS_FOR);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value, RepositoryConst::LOM_IS_BASIS_FOR));
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_REQUIRES:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_REQUIRES);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                
                $relation->addResource($resource);
                
                break;
            case RepositoryConst::LOM_MAP_RLTN_IS_REQUIRED_BY:
            
                $vocabulary = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, RepositoryConst::LOM_IS_REQUIRESD_BY);
                $relation->addKind($vocabulary);
                
                $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
                
                for($ii=0; $ii<count($metadata); $ii++){
                    $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
                    $resource->addIdentifier(new Repository_Oaipmh_LOM_Identifier($this->RepositoryAction, $value));
                }
                
                $relation->addResource($resource);
                
            default:
                break;
        }
        
        array_push($this->relation, $relation);
        
    }
    /**
     * setAnnotation
     * Annotation設定
     * 
     * @param array $mapping Mapping information マッピング情報
     *                       array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata Metadata information メタデータ情報
     *                        array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setAnnotation($mapping, $metadata)
    {
        $lomMap = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING];
        $language = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];
        
        for($ii=0; $ii<count($metadata); $ii++)
        {
            $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
            
            switch ($lomMap){
                case RepositoryConst::LOM_MAP_ANNTTN_ENTITY:
                    
                    $index = $this->getInsertIndexAnnotation(RepositoryConst::LOM_MAP_ANNTTN_ENTITY);
                    //新規
                    if($index == -1){
                        $annotation = new Repository_Oaipmh_LOM_Annotation($this->RepositoryAction);
                        $annotation->addEntity($value);
                        array_push($this->annotation, $annotation);
                    }
                    //上書き
                    else{
                        $this->annotation[$index]->addEntity($value);
                    }
                    break;
                case RepositoryConst::LOM_MAP_ANNTTN_DATE:
                    $langstring = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, '', $language);
                    //$date = new Repository_Oaipmh_LOM_DateTime($this->RepositoryAction, $value, $langstring);
                    $date = new Repository_Oaipmh_LOM_DateTime($this->RepositoryAction);
                    $date->setDateTime($value);
                    $date->setDescription($langstring);
                    
                    $index = $this->getInsertIndexAnnotation(RepositoryConst::LOM_MAP_ANNTTN_DATE);
                    //新規
                    if($index == -1){
                        $annotation = new Repository_Oaipmh_LOM_Annotation($this->RepositoryAction);
                        $annotation->addDate($date);
                        array_push($this->annotation, $annotation);
                    }
                    //上書き
                    else{
                        $this->annotation[$index]->addDate($date);
                    }
                    break;
                case RepositoryConst::LOM_MAP_ANNTTN_DESCRIPTION:
                    $description = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value, $language);
                    
                    $index = $this->getInsertIndexAnnotation(RepositoryConst::LOM_MAP_ANNTTN_DESCRIPTION);
                    //新規
                    if($index == -1){
                        $annotation = new Repository_Oaipmh_LOM_Annotation($this->RepositoryAction);
                        $annotation->addDescription($description);
                        array_push($this->annotation, $annotation);
                    }
                    //上書き
                    else{
                        $this->annotation[$index]->addDescription($description);
                    }
                    
                    break;
                default:
                    break;
            }
        }
        
    }
    
    /**
     * getInsertIndexAnnotation
     * 配列Annotationに格納すべきインデックスを取得する
     *
     * @param string $element Element 要素
     * @return int index インデックス
     */
    private function getInsertIndexAnnotation($element){
        $index = 0;
        
        //はじめていれる場合
        if(count($this->annotation) == 0){
            
            return -1;
        }
        
        $ii=0;
        //すでにはいっている場合
        for($ii=0;$ii<count($this->annotation);$ii++)
        {
            if($element == RepositoryConst::LOM_MAP_ANNTTN_ENTITY){
                $entity = $this->annotation[$ii]->getEntity();
                //入っていなかったらここに入れる
                if($entity == null || strlen($entity) == 0){
                    $index = $ii;
                    break;
                }
                
            }else if($element == RepositoryConst::LOM_MAP_ANNTTN_DATE){
                $date = $this->annotation[$ii]->getDate();
                //入っていなかったらここに入れる
                if($date == null){
                    $index = $ii;
                    break;
                }
                else if(strlen($date->getDateTime())==0)
                {
                    $index = $ii;
                    break;
                }
                
            }else if($element == RepositoryConst::LOM_MAP_ANNTTN_DESCRIPTION){
                $description = $this->annotation[$ii]->getDescription();
                //入っていなかったらここに入れる
                if($description == null){
                    $index = $ii;
                    break;
                }
                else if(strlen($description->getString())==0)
                {
                    $index = $ii;
                    break;
                }
            }else {
                return;
            }
        }
        
        //どこにも入るところがない場合
        if($ii!=0 && $ii == count($this->annotation)){
            //新しく作成する
            $annotation = new Repository_Oaipmh_LOM_Annotation($this->RepositoryAction);
            array_push($this->annotation, $annotation);
            return $ii;
        }
        
        return $index;
    }
    
    
    
    /**
     * setClassification
     * Classification設定
     * 
     * @param array $mapping Mapping information マッピング情報
     *                       array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $metadata Metadata information メタデータ情報
     *                        array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setClassification($mapping, $metadata)
    {
        $lomMap = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING];
        $language = $mapping[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];
        
        $index = -1;
        if($lomMap == RepositoryConst::LOM_MAP_CLSSFCTN_KEYWORD || RepositoryConst::LOM_MAP_CLSSFCTN_TAXON)
        {
            $index = $this->getInsertIndexClassification($lomMap);
        }
        
        for($ii=0; $ii<count($metadata); $ii++)
        {
            $value = RepositoryOutputFilter::attributeValue($mapping, $metadata[$ii]);
            
            switch ($lomMap){
                case RepositoryConst::LOM_MAP_CLSSFCTN_PURPOSE:
                    $purpose = new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $value);
                    $index = $this->getInsertIndexClassification(RepositoryConst::LOM_MAP_CLSSFCTN_PURPOSE);
                    //新規
                    if($index == -1){
                        $classification = new Repository_Oaipmh_LOM_Classification($this->RepositoryAction);
                        $classification->addPurpose($purpose);
                        array_push($this->classification, $classification);
                    }
                    //更新
                    else{
                        $this->classification[$index]->addPurpose($purpose);
                    }
                    
                    break;
                    
                case RepositoryConst::LOM_MAP_CLSSFCTN_DESCRIPTION:
                    $description = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value, $language);
                    $index = $this->getInsertIndexClassification(RepositoryConst::LOM_MAP_CLSSFCTN_DESCRIPTION);
                    //新規
                    if($index == -1){
                        $classification = new Repository_Oaipmh_LOM_Classification($this->RepositoryAction);
                        $classification->addDescription($description);
                        array_push($this->classification, $classification);
                    }
                    //更新
                    else{
                        $this->classification[$index]->addDescription($description);
                    }
                    break;
                    
                case RepositoryConst::LOM_MAP_CLSSFCTN_KEYWORD:
                    $keyword = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value, $language);
                    //新規
                    if($index == -1){
                        $classification = new Repository_Oaipmh_LOM_Classification($this->RepositoryAction);
                        $classification->addKeyword($keyword);
                        array_push($this->classification, $classification);
                        $index = count($this->classification) - 1;
                    }
                    //更新
                    else{
                        $this->classification[$index]->addKeyword($keyword);
                    }
                    break;
                    
                case RepositoryConst::LOM_MAP_CLSSFCTN_TAXON_PATH_SOURCE:
                    $index = $this->getInsertIndexClassification(RepositoryConst::LOM_MAP_CLSSFCTN_TAXON_PATH_SOURCE);
                    //新規
                    if($index == -1){
                        $classification = new Repository_Oaipmh_LOM_Classification($this->RepositoryAction);
                        $taxonPath = new Repository_Oaipmh_LOM_TaxonPath($this->RepositoryAction);
                        $taxonPath->addSource(new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value));
                        $classification->addTaxonPath($taxonPath);
                        array_push($this->classification, $classification);
                    }
                    //更新
                    else{
                        $source = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value);
                        $this->classification[$index]->setTaxonPathSource($source);
                    }
                    break;
                    
                case RepositoryConst::LOM_MAP_CLSSFCTN_TAXON:
                    //新規
                    if($index == -1){
                        $classification = new Repository_Oaipmh_LOM_Classification($this->RepositoryAction);
                        $taxon = new Repository_Oaipmh_LOM_TaxonPath($this->RepositoryAction);
                        $entry = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value);
                        
                        $child_taxon = new Repository_Oaipmh_LOM_Taxon($this->RepositoryAction);
                        $child_taxon->setEntry($entry);
                        $child_taxon->setId('');
                        $taxon->addTaxon($child_taxon);
                        $classification->addTaxonPath($taxon);
                        array_push($this->classification, $classification);
                        $index = count($this->classification) - 1;
                    }
                    //更新
                    else{
                        $entry = new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $value);
                        $this->classification[$index]->setTaxonPathEntry($entry);
                    }
                    break;
                    
                default:
                    break;
            }
        }
    }
    /**
     * getInsertIndexClassification
     * 配列Classificationに格納すべきインデックスを取得する
     * 
     * @param string $element Element 要素
     * @return int index インデックス
     */
    private function getInsertIndexClassification($element){
        $index = 0;
        
        if(count($this->classification) == 0){
            return -1;
        }
        
        $ii = 0;
        
        for($ii=0; $ii<count($this->classification); $ii++)
        {
            if($element == RepositoryConst::LOM_MAP_CLSSFCTN_PURPOSE){
                $purposeVal = $this->classification[$ii]->getPurposeValue();
                if(strlen($purposeVal)==0){
                    $index = $ii;
                    break;
                }
                
            }else if($element == RepositoryConst::LOM_MAP_CLSSFCTN_DESCRIPTION){
                $description = $this->classification[$ii]->getDescriptionString();
                if(strlen($description)==0){
                    $index = $ii;
                    break;
                }
                
            }else if($element == RepositoryConst::LOM_MAP_CLSSFCTN_KEYWORD){
                $keyword = $this->classification[$ii]->getKeyword();
                if(count($keyword)==0){
                    $index = $ii;
                    break;
                }
            }else if($element == RepositoryConst::LOM_MAP_CLSSFCTN_TAXON_PATH_SOURCE){
                
                $taxonPath = $this->classification[$ii]->getTaxonPathSource();
                if($taxonPath == null || strlen($taxonPath)==0){
                    $index = $ii;
                    break;
                }
               
            }else if($element == RepositoryConst::LOM_MAP_CLSSFCTN_TAXON){
                $taxonPath = $this->classification[$ii]->getTaxonPathCount();
                if($taxonPath == null || $taxonPath == 0){
                    $index = $ii;
                    break;
                }
                
            }
            
        }
        
        //値をいれる場所がないので新規作成
        if($ii!=0 && $ii == count($this->classification)){
            //作成
            $classification = new Repository_Oaipmh_LOM_Classification($this->RepositoryAction);
            array_push($this->classification, $classification);
        }
        
        return $index;
    }
    
    
    /**
     * setReference
     * Reference設定
     *
     * @param array $reference reference data 参照データ
     *                         array[$ii]["org_reference_item_id"|"org_reference_item_no"|"dest_reference_item_id"|"dest_reference_item_no"|"reference"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function setReference($reference)
    {
        
        for ($ii=0; $ii<count($reference); $ii++)
        {
            // relationを一行追加
            array_push($this->relation, new Repository_Oaipmh_LOM_Relation($this->RepositoryAction));
            $target_idx = count($this->relation)-1;
            
            $resource = new Repository_Oaipmh_LOM_Resource($this->RepositoryAction);
            
            // set Kind
            $ref = strtolower($reference[$ii][RepositoryConst::DBCOL_REPOSITORY_REF_REFERENCE]);
            $ref = RepositoryOutputFilterLOM::relation($ref);
            if(strlen($ref) > 0)
            {
                $this->relation[$target_idx]->addKind(new Repository_Oaipmh_LOM_Vocabulary($this->RepositoryAction, self::LOM_VALUE_SOURCE, $ref));
            }
            
            // set discription
            $destItemId = $reference[$ii][RepositoryConst::DBCOL_REPOSITORY_REF_DEST_ITEM_ID];
            $destItemNo = $reference[$ii][RepositoryConst::DBCOL_REPOSITORY_REF_DEST_ITEM_NO];
            // get detail url
            $refUrl = $this->RepositoryAction->getDetailUri($destItemId, $destItemNo);
            $resource->addDescription(new Repository_Oaipmh_LOM_LangString($this->RepositoryAction, $refUrl));
            
            $this->relation[$target_idx]->addResource($resource);
        }
    }
    
    /**
     * Output header
     * ヘッダ出力処理
     */
    private function outputHeader()
    {
        $xml = '';
        $xml .= '<'.RepositoryConst::LOM_START;
        $xml .= ' xsi:schemaLocation="http://ltsc.ieee.org/xsd/LOM';
        $xml .= ' http://ltsc.ieee.org/xsd/lomv1.0/lom.xsd"';
        $xml .= ' xmlns="http://ltsc.ieee.org/xsd/LOM">'."\n";
        return $xml;
    }
    
    /**
     * Output footer
     * フッダ出力処理
     */
    private function outputFooter()
    {
        $xml = '</'.RepositoryConst::LOM_START.'>'."\n";
        return $xml;
    }
    
    /****************************************** datatype *********************************************/
}

/************************************************ A wooden trunk **********************************************/
/**
 * <general> 
 *     <identifier>  ⇒Identifier型 (※複数存在する可能性あり)
 *        <catalog></catalog>
 *        <entry></entry>
 *     </identifier>
 *     <title>
 *         <string language=""></string>  ⇒LangString型
 *     </title>
 *     <language> </language> (※複数存在する可能性あり)
 *     <description> (※複数存在する可能性あり)
 *         <string language=""></language>  ⇒LangString型
 *     </description>
 *     <keyword> (※複数存在する可能性あり)
 *         <string language=""></string>  ⇒LangString型
 *     </keyword>
 *     <coverage> (※複数存在する可能性あり)
 *         <string language=""></string>  ⇒LangString型
 *     </coverage>
 *     <structure>
 *         [Vocabulary型参照]  =>Vocabulary型
 *     </structure>
 *     <aggregationLevel>
 *         [Vocabulary型参照]  =>Vocabulary型
 *     </aggregationLevel>
 * </general>
 */

/**
 * General tags generated classes
 * General型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_General
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $identifier = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $title = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $language = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $description = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $keyword = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $coverage = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $structure = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $aggregationLevel = null;
    
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    var $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction)
    {
        $this->repositoryAction = $repositoryAction;
    }
    
    /**
     * Set Identifier
     * Identifierセット関数
     *  
     * @param Repository_Oaipmh_LOM_Identifier $identifier Identifier Identifier
     */
    public function addIdentifier(Repository_Oaipmh_LOM_Identifier $identifier){
        array_push($this->identifier,$identifier);
    }
    /**
     * Set title
     * titleセット関数
     * 
     * @param Repository_Oaipmh_LOM_LangString $title Title タイトル
     */
    public function addTitle(Repository_Oaipmh_LOM_LangString $title){
        if($this->title == null){
            $this->title = $title;
        }
    }
    /**
     * Set language
     * languageセット関数 
     * 
     * @param string $language Language 言語
     */
    public function addLanguage($language){
        //encording
        $language = $this->repositoryAction->forXmlChange($language);
        // format language.
        $language = RepositoryOutputFilter::language($language);
        if($language === RepositoryConst::ITEM_LANG_OTHER)
        {
            $language = '';
        }
        if(strlen($language)>0){
            array_push($this->language, $language);
        }
    }
    /**
     * Set description
     * descriptionセット関数
     * 
     * @param Repository_Oaipmh_LOM_LangString $description Description Description
     */
    public function addDescription(Repository_Oaipmh_LOM_LangString $description){
        array_push($this->description, $description);
    }
    /**
     * Set keyword
     * keywordセット関数
     * 
     * @param Repository_Oaipmh_LOM_LangString $keyword Keyword Keyword
     */
    public function addKeyword(Repository_Oaipmh_LOM_LangString $keyword){
        array_push($this->keyword, $keyword);
    }
    /**
     * Set coverage
     * coverageセット関数 
     * 
     * @param Repository_Oaipmh_LOM_LangString $coverage Coverage Coverage
     */
    public function addCoverage(Repository_Oaipmh_LOM_LangString $coverage){
        array_push($this->coverage, $coverage);
    }
    /**
     * Set Structure
     * structureセット関数
     *  
     * @param Repository_Oaipmh_LOM_Vocabulary $structure Structure Structure
     */
    public function addStructure(Repository_Oaipmh_LOM_Vocabulary $structure){
        
        //check
        $structure_value = RepositoryOutputFilterLOM::generalStructureValue($structure->getValue());
        if($this->structure == null && strlen($structure_value)>0){
            $this->structure = $structure;
        }
    }
    /**
     * Set aggregationLevel
     * aggregationLevelセット関数 
     * @param Repository_Oaipmh_LOM_Vocabulary $aggregationLevel AggregationLevel AggrecationLevel
     */
    public function addAggregationLevel(Repository_Oaipmh_LOM_Vocabulary $aggregationLevel){
        
        //check
        $aggregationLevel_value = RepositoryOutputFilterLOM::generalAggregationLevelValue($aggregationLevel->getValue());
        if($this->aggregationLevel == null && strlen($aggregationLevel_value)>0){
            $this->aggregationLevel = $aggregationLevel;
        }
    }
    
    /**
     * Output
     * ※ that calling this method in the member variable after setting the value
     * General型の出力処理
     * ※メンバ変数に値を設定後に本メソッドを呼び出すこと
     * 
     * @return string xml str XML文字列
     */
    public function output()
    {
        $xmlStr = '';

        //identifier
        for($ii=0;$ii<count($this->identifier);$ii++)
        {
            $xmlStr .= $this->identifier[$ii]->output();
        }
        //title
        if($this->title != null)
        {
            $xml = $this->title->output();
            if(strlen($xml)>0){
                $xmlStr .= '<'.RepositoryConst::LOM_TAG_TITLE.'>';
                $xmlStr .= $xml;
                $xmlStr .= '</'.RepositoryConst::LOM_TAG_TITLE.'>'."\n";
            }
        }
        //language
        for($ii=0;$ii<count($this->language);$ii++)
        {
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_LANGUAGE.'>'.$this->language[$ii].'</'.RepositoryConst::LOM_TAG_LANGUAGE.'>'."\n";
        }
        //description
        for($ii=0;$ii<count($this->description);$ii++){
            $xml = $this->description[$ii]->output();
            if(strlen($xml)>0){
                $xmlStr .= '<'.RepositoryConst::LOM_TAG_DESCRIPTION.'>';
                $xmlStr .= $xml;
                $xmlStr .= '</'.RepositoryConst::LOM_TAG_DESCRIPTION.'>'."\n";
            }
        }
        //keyword
        for($ii=0;$ii<count($this->keyword);$ii++){
            $xml = $this->keyword[$ii]->output();
            if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_KEYWORD.'>'."\n";
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_KEYWORD.'>'."\n";
            }
        }
        //coverage
        for($ii=0;$ii<count($this->coverage);$ii++){
            $xml = $this->coverage[$ii]->output();
            if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_COVERAGE.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_COVERAGE.'>'."\n";
            }
        }
        //structure
        if($this->structure != null)
        {
            $xml = $this->structure->output();
            if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_STRUCTURE.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_STRUCTURE.'>'."\n";
            }
        }
        //aggregationLevel
        if($this->aggregationLevel != null)
        {
            $xml = $this->aggregationLevel->output();
            if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_AGGREGATION_LEVEL.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_AGGREGATION_LEVEL.'>'."\n";
            }
        }
        
        if(strlen($xmlStr)>0)
        {
            $xmlStr = '<'.RepositoryConst::LOM_TAG_GENERAL.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_GENERAL.'>'."\n";
        }
        
        return $xmlStr;
    }
}
    
/**
 * <contribute>
 *     <role> 
 *         [Vocabulary型参照]  =>Vocabulary型
 *     </role>
 *     <entity> メタデータ1 </entity>(※複数存在する可能性アリ)
 *     <entity> メタデータ2 </entity>(※複数存在する可能性アリ)
 *     <date>
 *         <dateTime></dateTime>=>DateTime型 (※2013年後期時点では未使用)
 *         <description></description>
 *     </date>
 * <contribute>
 */

/**
 * Contribute tags generated classes
 * Contribute型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Contribute
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $role = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $entry= array();
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $date= null;

    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction)
    {
        $this->repositoryAction = $repositoryAction;
    }

    //setter
    /**
     * Set role
     * roleセット関数
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $role Role Role
     */
    public function addRole(Repository_Oaipmh_LOM_Vocabulary $role){
        if($this->role == null){
            $this->role = $role;
        }
        
    }
    /**
     * Set entry
     * entryセット関数
     * 
     * @param string $entry Entry Entry
     */
    public function addEntry($entry){
        $entry = $this->repositoryAction->forXmlChange($entry);
        if(strlen($entry)>0){
            array_push($this->entry, $entry);
        }
    }
    /**
     * Set date
     * dateセット関数
     * 
     * @param string $date Date Date
     */
    public function addDate(Repository_Oaipmh_LOM_DateTime $date){
        if($this->date == null){
            $this->date = $date;
        }
    }
    
    //getter
    /**
     * Get Role value
     * Roleタグ内Value値取得
     * 
     * @return string Value 値
     */
    public function getRoleValue(){
        if($this->role == null){
            return '';
        }
        return $this->role->getValue();
    }
    
    /**
     * Output
     * ※ that calling this method in the member variable after setting the value
     * Contribute型の出力処理
     * ※メンバ変数に値を設定後に本メソッドを呼び出すこと
     * 
     * @return string xml str XML文字列
     */
    public function output()
    {
        $xmlStr = '';
        
        if(count($this->entry) == 0 && $this->date == null){
            return '';
        }
        
        //role
        if($this->role != null)
        {
            $xml = $this->role->output();
            if(strlen($xml)>0){
            	$xmlStr .= '<'.RepositoryConst::LOM_TAG_ROLE.'>'.$xml.'</'.RepositoryConst::LOM_TAG_ROLE.'>'."\n";
            }
        }
        //entry
        for($ii=0; $ii<count($this->entry); $ii++)
        {
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_ENTITY.'>';
            $xmlStr .= $this->entry[$ii];
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_ENTITY.'>'."\n";
        }
        //date
        if($this->date != null)
        {
            $xml = $this->date->output();
            if(strlen($xml)>0){
            	$xmlStr .= '<'.RepositoryConst::LOM_TAG_DATE.'>'.$xml.'</'.RepositoryConst::LOM_TAG_DATE.'>'."\n";
            }
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_CONTRIBUTE.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_CONTRIBUTE.'>'."\n";
        }
        
        return $xmlStr;
    }

}

/**
 * <lifeCycle>
 *     <version>
 *        <string language=""></string> =>LangString型 
 *     </version>
 *     <status>
 *         [Vocabulary型参照]  =>Vocabulary型
 *     </status>
 *     <contribute>(※複数存在する可能性アリ)  =>Contribute型 
 *         [Contribute型の内容参照]
 *     </contribute>
 * </lifeCycle>
 */

/**
 * LifeCycle tags generated classes
 * LifeCycle型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_LifeCycle
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $version = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $status = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $contribute = array();
    
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction){
        $this->repositoryAction = $repositoryAction;
    }
    
    /**
     * Set version
     * versionセット関数
     * 
     * @param Repository_Oaipmh_LOM_LangString $version Version バージョン
     */
    public function addVersion(Repository_Oaipmh_LOM_LangString $version){
        if($this->version == null){
            $this->version = $version;
        }
    }
    /**
     * Set status
     * statusセット関数
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $status Status ステータス
     */
    public function addStatus(Repository_Oaipmh_LOM_Vocabulary $status){
        
        //check
        $status_value = RepositoryOutputFilterLOM::lifeCycleStatusValue($status->getValue());
        if(strlen($status_value)>0){
            $this->status = $status;
        }
    }
    /**
     * Set contributor
     * contributeセット関数
     * 
     * @param Repository_Oaipmh_LOM_Contribute $contribute Contributor Contributor
     * @param boolean $flag flag flag
     */
    public function addContribute(Repository_Oaipmh_LOM_Contribute $contribute, $flag){
        
        //check
        //lifeCycleContributeXXの場合
        if($flag == 1){
            $contribute_value = RepositoryOutputFilterLOM::lyfeCycleContributeRole($contribute->getRoleValue());
            if(strlen($contribute_value)>0){
                array_push($this->contribute, $contribute);
            }
        }
        //lifeCycleContributeの場合
        else{
            array_push($this->contribute, $contribute);
        }
    }
    
    /**
     * Output
     * LifeCycleタグの出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output(){
        $xmlStr = '';
        
        //version
        if($this->version != null){
            $xml = $this->version->output();
            if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_VERSION.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_VERSION.'>'."\n";
            }
        }
        //status
        if($this->status != null){
            $xml = $this->status->output();
            if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_STATUS.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_STATUS.'>'."\n";
            }
        }
        //contribute
        for($ii=0;$ii<count($this->contribute);$ii++){
            $xmlStr .= $this->contribute[$ii]->output();
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_LIFE_CYCLE.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_LIFE_CYCLE.'>'."\n";
        }
        
        return $xmlStr;
    }
}

/**
 * <metaMetadata>
 *     <identifier>  ⇒Identifier型 (※複数存在する可能性アリ)
 *          [Identifier型参照]
 *     </identifier>
 *     <contribute>  =>Contribute型 (※複数存在する可能性アリ)
 *          [Contribute型参照]
 *     </contribute>
 *     <metadataSchema></metadataSchema>(※複数存在する可能性アリ)
 *     <language></language>
 * </metaMetadata>
 */

/**
 * metaMetadata tags generated classes
 * metaMetadata型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_MetaMetadata
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $identifier = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $contribute = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $metadataSchema = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $language = null;
    
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     * 
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction){
        $this->repositoryAction = $repositoryAction;
    }
    
    /**
     * identifierセット関数
     * @param Repository_Oaipmh_LOM_Identifier $identifier
     */
    public function addIdentifier(Repository_Oaipmh_LOM_Identifier $identifier){
        array_push($this->identifier, $identifier);
    }
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Repository_Oaipmh_LOM_Contribute $contribute Repository_Oaipmh_LOM_Contribute object Repository_Oaipmh_LOM_Contributeオブジェクト
     * @param boolean $flag flag フラグ
     */
    public function addContribute(Repository_Oaipmh_LOM_Contribute $contribute, $flag){
        
        //check
        //mataMatadataContributeXXの場合
        if($flag == 1){
            $contribute_value = RepositoryOutputFilterLOM::metaMetadataContributeRole($contribute->getRoleValue());
            if(strlen($contribute_value)>0){
                array_push($this->contribute, $contribute);
            }
        }
        //mataMatadataContributeの場合
        else{
            array_push($this->contribute, $contribute);
        }
    }
    /**
     * Set metadataSchema
     * metadataSchemaセット関数
     * 
     * @param string $metadataSchema metadataSchema metadataSchema
     */
    public function addMetadataSchema($metadataSchema){
        //encording
        $metadataSchema = $this->repositoryAction->forXmlChange($metadataSchema);
        if(strlen($metadataSchema)>0){
            array_push($this->metadataSchema, $metadataSchema);
        }
    }
    /**
     * Set language
     * languageセット関数
     * 
     * @param string $language Language 言語
     */
    public function addLanguage($language){
        //encording
        $language = $this->repositoryAction->forXmlChange($language);
        $language = RepositoryOutputFilter::language($language);
        
        if($this->language == null && strlen($language)>0){
            $this->language = $language;
        }
        
    }
    
    /**
     * Output
     * MetaMetadataタグの出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output(){
        $xmlStr = '';

        for($ii=0;$ii<count($this->identifier);$ii++){
            $xmlStr .= $this->identifier[$ii]->output();
        }
        for($ii=0;$ii<count($this->contribute);$ii++){
            $xml = $this->contribute[$ii]->output();
            if(strlen($xml)>0){
                //$xmlStr .= '<'.RepositoryConst::LOM_TAG_CONTRIBUTE.'>';
                $xmlStr .= $xml;
                //$xmlStr .= '</'.RepositoryConst::LOM_TAG_CONTRIBUTE.'>';
            }
        }
        for($ii=0;$ii<count($this->metadataSchema);$ii++){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_METADATA_SCHEMA.'>';
            $xmlStr .= $this->metadataSchema[$ii];
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_METADATA_SCHEMA.'>'."\n";
        }
        if($this->language != null && strlen($this->language) > 0){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_LANGUAGE.'>'.$this->language.'</'.RepositoryConst::LOM_TAG_LANGUAGE.'>'."\n";
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_META_METADATA.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_META_METADATA.'>'."\n";
        }
        
        return $xmlStr;
    }
}

/**
 * <technical>
 *      <format></format>(※複数存在する可能性アリ)
 *      <size></size>
 *      <location></location>(※複数存在する可能性アリ)
 *      <requirement>(※複数存在する可能性アリ)
 *          <orComposite>(※複数存在する可能性アリ)
 *              <type>
 *                  [Vocabulary型参照] =>Vocabulary型
 *              </type>
 *              <name>
 *                  [Vocabulary型参照] =>Vocabulary型
 *              </name>
 *              <minimumVersion></minimumVersion>
 *              <maximumVersion></maximumVersion>
 *          </orComposite>
 *      </requirement>
 *      <installationRemarks>
 *          <string language=""></string>  =>LangString型
 *      </installationRemarks>
 *      <otherPlatformRequirements>
 *          <string language=""></string>  =>LangString型
 *      </otherPlatformRequirements>
 *      <duration>  =>Duration型
 *          [Duration型参照]
 *      </duration>
 * </technical>
 */
/**
 * Technical tags generated classes
 * Technical型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Technical
{
    /**
     * 定数
     */
    /**
     * Operating system
     * OS
     *
     * @var string
     */
    const OPERATING_SYSTEM = 'operating system';
    /**
     * Browser
     * ブラウザ
     *
     * @var string
     */
    const BROWSER = 'browser';
    
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $format = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $size = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $location = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $requirement = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $installationRemarks = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $otherPlatformRequirements = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $duration = null;
    
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction){
        $this->repositoryAction = $repositoryAction;
    }
    /**
     * Set format
     * Format設定
     * 
     * @param string $format Format Format
     */
    public function addFormat($format){
        //encoding
        $format = $this->repositoryAction->forXmlChange($format);
        if(strlen($format)>0){
            array_push($this->format, $format);
        }
    }
    /**
     * Set size
     * Size設定
     * 
     * @param string $size Size Size
     */
    public function addSize($size){
        $size = $this->repositoryAction->forXmlChange($size);
        $size = RepositoryOutputFilterLOM::technicalSize($size);
        if($this->size == null && strlen($size)>0){
            $this->size = $size;
        }
    }
    /**
     * Set location
     * Location設定
     * 
     * @param string $location Location Loccation
     */
    public function addLocation($location){
        $location = $this->repositoryAction->forXmlChange($location);
        if(strlen($location)>0){
            array_push($this->location, $location);
        }
    }
    /**
     * Set requirement
     * Requirement設定
     * 
     * @param Repository_Oaipmh_LOM_OrComposite $requirement Requirement Requirement
     */
    public function addRequirement(Repository_Oaipmh_LOM_OrComposite $orComposite){
        //check
        array_push($this->requirement, $orComposite);
    }
    /**
     * InstallationRemarks設定
     * @param Repository_Oaipmh_LOM_LangString $installationRemarks
     */
    public function addInstallationRemarks(Repository_Oaipmh_LOM_LangString $installationRemarks){
        if($this->installationRemarks == null){
            $this->installationRemarks = $installationRemarks;
        }
    }
    /**
     * Set OtherPlatformRequirements
     * OtherPlatformRequirements設定
     * 
     * @param Repository_Oaipmh_LOM_LangString $otherPlatformRequirements OtherPlatformRequirement OtherPlatformRequirement
     */
    public function addOtherPlatformRequirements(Repository_Oaipmh_LOM_LangString $otherPlatformRequirements){
        if($this->otherPlatformRequirements == null){
            $this->otherPlatformRequirements = $otherPlatformRequirements;
        }
    }
    /**
     * Set duration
     * Duration設定
     * 
     * @param Repository_Oaipmh_LOM_Duration $duration Duration Duration
     */
    public function addDuration(Repository_Oaipmh_LOM_Duration $duration){
        //check
        $duration_value = RepositoryOutputFilterLOM::duration($duration->getDuration());
        if(strlen($duration_value)>0){
            $this->duration = $duration;
        }
    }
    
    /**
     * To check that contains the value
     * 値が入っているかを確認する
     *
     * @param string $element
     * @param Repository_Oaipmh_LOM_Vocabulary $value Vocabulary Vocabulary
     * @return boolean Result 結果
     */
    private function checkOrComposite($element, &$value)
    {
        $checkVal = $value;
        if(is_a($value, 'Repository_Oaipmh_LOM_Vocabulary'))
        {
            // type or name
            if($element == RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_TYPE)
            {
                $checkVal = RepositoryOutputFilterLOM::technicalRequirementOrCompositeTypeValue($value->getValue());
                if(strlen($checkVal) == 0)
                {
                    return false;
                }
                $value->setValue($checkVal);
            }
            else if($element == RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_NAME)
            {
                $checkVal = RepositoryOutputFilterLOM::technicalRequirementOrCompositeNameValueForOperatingSystem($value->getValue());
                if(strlen($checkVal) == 0)
                {
                    $checkVal = RepositoryOutputFilterLOM::technicalRequirementOrCompositeNameValueForBrowser($value->getValue());
                    if(strlen($checkVal) == 0)
                    {
                        return false;
                    }
                }
                $value->setValue($checkVal);
            }
            else
            {
                return false;
            }
        }
        
        if(strlen($checkVal) == 0)
        {
            return false;
        }
        
        return true;
    }
    
    //setter
    /**
     * addOrComposite
     * 値を追加する
     *
     * @param string $element
     * @param Repository_Oaipmh_LOM_Vocabulary $value Vocabulary Vocabulary
     */
    public function addOrComposite($element, $value){
        
        if(!$this->checkOrComposite($element, $value))
        {
            return;
        }
        /**
        if(count($this->requirement) == 0)
        {
            $this->addRequirement(new Repository_Oaipmh_LOM_OrComposite($this->repositoryAction, null, null, '', ''));
        }
        */
        $ii = 0;
        for($ii=0; $ii<count($this->requirement); $ii++)
        {
            $orComp = $this->requirement[$ii];
            if($element == RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_TYPE)
            {
                // check type
                if(strlen($this->requirement[$ii]->getTypeValue()) == 0)
                {
                    $name = $this->requirement[$ii]->getNameValue();
                    if(strlen($name) > 0)
                    {
                        if(RepositoryOutputFilterLOM::technicalRequirementOrCompositeCombination($value->getValue(), $name))
                        {
                            // typeに入れようとしている$valueと既に入っているnameの組み合わせはOKな組み合わせ
                            $this->requirement[$ii]->setTypeString($value);
                            break;
                        }
                    }
                    else
                    {
                        // typeもnameも空
                       $this->requirement[$ii]->setTypeString($value);
                        break;
                    }
                }
            }
            else if($element == RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_NAME)
            {
                // check name
                if(strlen($this->requirement[$ii]->getNameValue()) == 0)
                {
                    $type = $this->requirement[$ii]->getTypeValue();
                    if(strlen($type) > 0)
                    {
                        if(RepositoryOutputFilterLOM::technicalRequirementOrCompositeCombination($type, $value->getValue()))
                        {
                            // typeに入れようとしている$valueと既に入っているnameの組み合わせはOKな組み合わせ
                            $this->requirement[$ii]->setName($value);
                            break;
                        }
                    }
                    else
                    {
                        // typeもnameも空
                        $this->requirement[$ii]->setName($value);
                        break;
                    }
                }
            }
            else if($element == RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MINIMUM_VERSION)
            {
                if(strlen($this->requirement[$ii]->getMinimumVersion()) == 0)
                {
                    $this->requirement[$ii]->setMinimumVersion($value);
                    break;
                }
            }
            else if($element == RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MAXIMUM_VERSION)
            {
                if(strlen($this->requirement[$ii]->getMaximumVersion()) == 0)
                {
                    $this->requirement[$ii]->setMaximumVersion($value);
                    break;
                }
            }
        }
        
        if($ii == count($this->requirement))
        {
            // 値を入れる場所がないので、新規作成
            //$orComp = new Repository_Oaipmh_LOM_OrComposite($this->repositoryAction, null, null, '', '');
            $orComp = new Repository_Oaipmh_LOM_OrComposite($this->repositoryAction);
            $orComp->setName(new Repository_Oaipmh_LOM_Vocabulary($this->repositoryAction, '', ''));
            $orComp->setType(new Repository_Oaipmh_LOM_Vocabulary($this->repositoryAction, '', ''));
            $orComp->setMaximumVersion('');
            $orComp->setMinimumVersion('');
            
            if($element == RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_TYPE)
            {
                $orComp->setTypeString($value);
            }
            else if($element == RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_NAME)
            {
                $orComp->setNameString($value);
            }
            else if($element == RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MINIMUM_VERSION)
            {
                $orComp->setMinimumVersion($value);
            }
            else if($element == RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MAXIMUM_VERSION)
            {
                $orComp->setMaximumVersion($value);
            }
            
            $this->addRequirement($orComp);
        }
    }
    
    
    /**
     * Output
     * Technicalタグの出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output(){
        $xmlStr = '';
        //format
        for($ii=0;$ii<count($this->format);$ii++){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_FORMAT.'>';
            $xmlStr .= $this->format[$ii];
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_FORMAT.'>'."\n";
        }
        //size
        if($this->size != null){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_SIZE.'>';
            $xmlStr .= $this->size;
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_SIZE.'>'."\n";
        }
        //location
        for($ii=0;$ii<count($this->location);$ii++){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_LOCATION.'>';
            $xmlStr .= $this->location[$ii];
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_LOCATION.'>'."\n";
        }
        //requirement
        
        for($ii=0;$ii<count($this->requirement);$ii++){
            $xml = $this->requirement[$ii]->output();
            if(strlen($xml)>0){
                if($ii == 0){
                    $xmlStr .= '<'.RepositoryConst::LOM_TAG_REQUIREMENT.'>';
                }
                
                $xmlStr .= $xml;
                
                if($ii == count($this->requirement)-1){
                    $xmlStr .= '</'.RepositoryConst::LOM_TAG_REQUIREMENT.'>'."\n";
                }
            }
        }
        
        //installationRemarks
        if($this->installationRemarks != null){
            $xml = $this->installationRemarks->output();
            if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_INSTALLATION_REMARKS.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_INSTALLATION_REMARKS.'>'."\n";
            }
        }
        //otherPlatformRequirements
        if($this->otherPlatformRequirements != null){
            $xml = $this->otherPlatformRequirements->output();
            if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_OTHER_PLATFORM_REQIREMENTS.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_OTHER_PLATFORM_REQIREMENTS.'>';
            }
        }
        //duration
        if($this->duration != null){
            $xml = $this->duration->output();
            if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_DURATION.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_DURATION.'>'."\n";
            }
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_TECHNICAL.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_TECHNICAL.'>'."\n";
        }
        
        return $xmlStr;
    }
}

/**
 * <educational>
 *      <interactivityType>
 *          [Vocabulary型参照] =>Vocabulary型
 *      </interactivityType>
 *      <learningResourceType>(※複数存在する可能性アリ)
 *          [Vocabulary型参照] =>Vocabulary型
 *      </learningResourceType>
 *      <interactivityLevel>
 *          [Vocabulary型参照] =>Vocabulary型
 *      </interactivityLevel>
 *      <semanticDensity>
 *          [Vocabulary型参照] =>Vocabulary型
 *      </semanticDensity>
 *      <intendedEndUserRole>(※複数存在する可能性アリ)
 *          [Vocabulary型参照] =>Vocabulary型
 *      </intendedEndUserRole>
 *      <context>(※複数存在する可能性アリ)
 *          [Vocabulary型参照] =>Vocabulary型
 *      </context>
 *      <typicalAgeRange>(※複数存在する可能性アリ)
 *          <string language=""></string>  =>LangString型
 *      </typicalAgeRange>
 *      <difficulty>
 *          [Vocabulary型参照] =>Vocabulary型
 *      </difficulty>
 *      <typicalLearningTime>  =>Duration型
 *          [Duration型参照]
 *      </typicalLearningTime>
 *      <description>(※複数存在する可能性アリ)
 *          <string language=""></string>  =>LangString型
 *      </description>
 *      <language></language>(※複数存在する可能性アリ)
 * </educational>
 */

/**
 * Educational tags generated classes
 * Educational型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Educational
{
    /**
     * メンバ
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $interactivityType = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $learningResourceType = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $interactivityLevel = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $semanticDensity = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $intendedEndUserRole = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $context = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $typicalAgeRange = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $difficulty = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $typicalLearningTime = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $description = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $language = array();
    
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction){
        $this->repositoryAction = $repositoryAction;
    }
    
    /**
     * Set interactivityType
     * interactivityType設定
     * @param Repository_Oaipmh_LOM_Vocabulary $interactivityType InteractivityType InteractivityType
     */
    public function addInteractivityType(Repository_Oaipmh_LOM_Vocabulary $interactivityType){
        //check
        $interactivityType_value = RepositoryOutputFilterLOM::educationalInteractivityType($interactivityType->getValue());
        if($this->interactivityType == null && strlen($interactivityType_value)>0){
            $this->interactivityType = $interactivityType;
        }
    }
    /**
     * Set LeanrningResourceType
     * learningResourceType設定
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $learningResourceType LeanrningResourceType LeanrningResourceType
     */
    public function addLearningResourceType(Repository_Oaipmh_LOM_Vocabulary $learningResourceType){
        //check
        $learningResourceType_value = RepositoryOutputFilterLOM::educationalLearningResourceType($learningResourceType->getValue());
        if(strlen($learningResourceType_value)>0){
            array_push($this->learningResourceType,$learningResourceType);
        }
    }
    /**
     * Set InteractivityLevel
     * InteractivityLevel設定
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $interactivityLevel InteractivityLevel InteractivityLevel
     */
    public function addInteractivityLevel(Repository_Oaipmh_LOM_Vocabulary $interactivityLevel){
        //check
        $interactivityLevel_value = RepositoryOutputFilterLOM::educationalInteractivityLevel($interactivityLevel->getValue());
        if($this->interactivityLevel == null && strlen($interactivityLevel_value)>0){
            $this->interactivityLevel = $interactivityLevel;
        }
    }
    /**
     * Set SemanticDensity
     * SemanticDensity設定
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $semanticDensity SemanticDensity SemanticDensity
     */
    public function addSemanticDensity(Repository_Oaipmh_LOM_Vocabulary $semanticDensity){
        //check
        $semanticDensity_value = RepositoryOutputFilterLOM::educationalSemanticDensity($semanticDensity->getValue());
        if($this->semanticDensity == null && strlen($semanticDensity_value)>0){
            $this->semanticDensity = $semanticDensity;
        }
    }
    /**
     * Set IntendedEndUserRole
     * IntendedEndUserRole設定
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $intendedEndUserRole IntendedEndUserRole IntendedEndUserRole
     */
    public function addIntendedEndUserRole(Repository_Oaipmh_LOM_Vocabulary $intendedEndUserRole){
        $intendedEndUserRole_value = RepositoryOutputFilterLOM::educationalIntendedEndUserRole($intendedEndUserRole->getValue());
        if(strlen($intendedEndUserRole_value)>0){
            array_push($this->intendedEndUserRole, $intendedEndUserRole);
        }
    }
    /**
     * Set Context
     * Context設定
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $context Context Context
     */
    public function addContext(Repository_Oaipmh_LOM_Vocabulary $context){
        $context_value = RepositoryOutputFilterLOM::educationalContext($context->getValue());
        if(strlen($context_value)>0){
            array_push($this->context, $context);
        }
    }
    /**
     * Set TypicalAgeRange
     * TypicalAgeRange設定
     * 
     * @param Repository_Oaipmh_LOM_LangString $typicalAgeRange TypicalAgeRange TypicalAgeRange
     */
    public function addTypicalAgeRange(Repository_Oaipmh_LOM_LangString $typicalAgeRange){
        array_push($this->typicalAgeRange,$typicalAgeRange);
    }
    /**
     * Set Difficulty
     * Difficulty設定
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $difficulty Difficulty Difficulty
     */
    public function addDifficulty(Repository_Oaipmh_LOM_Vocabulary $difficulty){
        $difficulty_value = RepositoryOutputFilterLOM::educationalDifficulty($difficulty->getValue());
        if($this->difficulty == null && strlen($difficulty_value)>0){
            $this->difficulty = $difficulty;
        }
    }
    /**
     * Set TypicalLearningTime
     * TypicalLearningTime設定
     * 
     * @param Repository_Oaipmh_LOM_LangString $typicalLearningTime TypicalLearningTime TypicalLearningTime
     */
    public function addTypicalLearningTime(Repository_Oaipmh_LOM_Duration $typicalLearningTime){
        //check
        $typicalLearningTime_value = RepositoryOutputFilterLOM::duration($typicalLearningTime->getDuration());
        if($this->typicalLearningTime == null && strlen($typicalLearningTime_value)>0){
            $this->typicalLearningTime = $typicalLearningTime;
        }
    }
    /**
     * Set Description
     * Description設定
     * 
     * @param Repository_Oaipmh_LOM_LangString $description Description Description
     */
    public function addDescription(Repository_Oaipmh_LOM_LangString $description){
        array_push($this->description,$description);
    }
    /**
     * Set Language
     * Language設定
     * 
     * @param string $language Language Language
     */
    public function addLanguage($language){
        //check
        //encoding
        $language = $this->repositoryAction->forXmlChange($language);
        //format language
        $language = RepositoryOutputFilter::language($language);
        if(strlen($language)>0){
            array_push($this->language,$language);
        }
    }
    
    //getter
    /**
     * Get InteractivityType
     * InteractivityType取得
     *
     * @return string
     */
    public function getInteractivityType(){
        return $this->interactivityType;
    }

    /**
     * Get LearningResourceType
     * LearningResourceType取得
     *
     * @return array[$ii]
     */
    public function getLearningResourceType(){
        return $this->learningResourceType;
    }

    /**
     * Get InteractivityLevel
     * InteractivityLevel取得
     *
     * @return string
     */
    public function getInteractivityLevel(){
        return $this->interactivityLevel;
    }

    /**
     * Get SemanticDensity
     * SemanticDensity取得
     *
     * @return string
     */
    public function getSemanticDensity(){
        return $this->semanticDensity;
    }

    /**
     * Get IntendedEndUserRole
     * IntendedEndUserRole取得
     *
     * @return array[$ii]
     */
    public function getIntendedEndUserRole(){
        return $this->intendedEndUserRole;
    }

    /**
     * Get Context
     * Context取得
     *
     * @return array[$ii]
     */
    public function getContext(){
        return $this->context;
    }

    /**
     * Get TypicalAgeRange
     * TypicalAgeRange取得
     *
     * @return array[$ii]
     */
    public function getTypicalAgeRange(){
        return $this->typicalAgeRange;
    }

    /**
     * Get Difficulty
     * Difficulty取得
     *
     * @return string
     */
    public function getDifficulty(){
        return $this->difficulty;
    }

    /**
     * Get TypicalLearningTime
     * TypicalLearningTime取得
     *
     * @return string
     */
    public function getTypicalLearningTime(){
        return $this->typicalLearningTime;
    }

    /**
     * Get Description
     * Description取得
     *
     * @return array[$ii]
     */
    public function getDescription(){
        return $this->description;
    }

    /**
     * Get Language
     * Language取得
     *
     * @param string $language language 言語
     * @return array[$ii]
     */
    public function getLanguage($language){
        return $this->language;
    }
    
    
    /**
     * educationalタグの出力処理
     * @return string xml str
     */
    public function output(){
        $xmlStr = '';
        //interactivityType
        if($this->interactivityType != null){
        	$xml = $this->interactivityType->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_INTERACTIVITY_TYPE.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_INTERACTIVITY_TYPE.'>'."\n";
            }
        }
        //learningResourceType
        for($ii=0;$ii<count($this->learningResourceType);$ii++){
        	$xml = $this->learningResourceType[$ii]->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_LEARNING_RESOURCE_TYPE.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_LEARNING_RESOURCE_TYPE.'>'."\n";
            }
        }
        //interactivityLevel
        if($this->interactivityLevel != null){
        	$xml = $this->interactivityLevel->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_INTERACTIVITY_LEVEL.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_INTERACTIVITY_LEVEL.'>'."\n";
            }
        }
        //semanticDensity
        if($this->semanticDensity != null){
        	$xml = $this->semanticDensity->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_SEMANTIC_DENSITY.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_SEMANTIC_DENSITY.'>'."\n";
            }
        }
        //intendedEndUserRole
        for($ii=0;$ii<count($this->intendedEndUserRole);$ii++){
        	$xml = $this->intendedEndUserRole[$ii]->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_INTENDED_END_USER_ROLE.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_INTENDED_END_USER_ROLE.'>'."\n";
            }
        }
        //context
        for($ii=0;$ii<count($this->context);$ii++){
        	$xml = $this->context[$ii]->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_CONTEXT.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_CONTEXT.'>'."\n";
            }
        }
        //typicalAgeRange
        for($ii=0;$ii<count($this->typicalAgeRange);$ii++){
        	$xml = $this->typicalAgeRange[$ii]->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_TYPICAL_AGE_RANGE.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_TYPICAL_AGE_RANGE.'>'."\n";
            }
        }
        //difficulty
        if($this->difficulty != null){
        	$xml = $this->difficulty->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_DIFFICULTY.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_DIFFICULTY.'>'."\n";
            }
        }
        //typicalLearningTime
        if($this->typicalLearningTime != null){
        	$xml = $this->typicalLearningTime->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_TYPICAL_LEARNING_TIME.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_TYPICAL_LEARNING_TIME.'>'."\n";
            }
        }
        //description
        for($ii=0;$ii<count($this->description);$ii++){
        	$xml = $this->description[$ii]->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_DESCRIPTION.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_DESCRIPTION.'>'."\n";
            }
        }
        //language
        for($ii=0;$ii<count($this->language);$ii++){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_LANGUAGE.'>';
            $xmlStr .= $this->language[$ii];
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_LANGUAGE.'>'."\n";
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_EDUCATIONAL.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_EDUCATIONAL.'>'."\n";
        }
        return $xmlStr;
    }
    
}

/**
 * <rights>
 *      <cost>
 *          [Vocabulary型参照] =>Vocabulary型
 *      </cost>
 *      <copyrightAndOtherRestrictions>
 *          [Vocabulary型参照] =>Vocabulary型
 *      </copyrightAndOtherRestrictions>
 *      <description>
 *          <string language=""></string>  =>LangString型
 *      </description>
 * </rights>
 */

/**
 * Rights tags generated classes
 * Rights型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Rights
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $cost = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $copyrightAndOtherRestrictions = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $description = null;
    
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction){
        $this->repositoryAction = $repositoryAction;
    }
    
    /**
     * Set Cost
     * Cost設定
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $cost Cost Cost
     */
    public function addCost(Repository_Oaipmh_LOM_Vocabulary $cost){
        $cost_value = RepositoryOutputFilterLOM::yesno($cost->getValue());
        if($this->cost == null && strlen($cost_value)>0){
            $this->cost = $cost;
        }
    }
    /**
     * Set CopyrightAndOtherRestrictions
     * CopyrightAndOtherRestrictions設定
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $copyrightAndOtherRestrictions CopyrightAndOtherRestrictions CopyrightAndOtherRestrictions
     */
    public function addCopyrightAndOtherRestrictions(Repository_Oaipmh_LOM_Vocabulary $copyrightAndOtherRestrictions){
        $copyright_value = RepositoryOutputFilterLOM::yesno($copyrightAndOtherRestrictions->getValue());
        if($this->copyrightAndOtherRestrictions == null && strlen($copyright_value)>0){
            $this->copyrightAndOtherRestrictions = $copyrightAndOtherRestrictions;
        }
    }
    /**
     * Set Description
     * Description設定
     * 
     * @param Repository_Oaipmh_LOM_LangString $description Description Description
     */
    public function addDescription(Repository_Oaipmh_LOM_LangString $description){
        if($this->description == null){
            $this->description = $description;
        }
    }
    /**
     * Output
     * rightsタグ出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output(){
        $xmlStr = '';
        
        //cost
        if($this->cost != null){
        	$xml = $this->cost->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_COST.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_COST.'>'."\n";
            }
        }
        //copyrightAndOtherRestrictions
        if($this->copyrightAndOtherRestrictions != null){
        	$xml = $this->copyrightAndOtherRestrictions->output();
        	
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_COPYRIGHT_AND_OTHER_RESTRICTIONS.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_COPYRIGHT_AND_OTHER_RESTRICTIONS.'>'."\n";
            }
        }
        //description
        if($this->description != null){
        	$xml = $this->description->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_DESCRIPTION.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_DESCRIPTION.'>'."\n";
            }
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_RIGHTS.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_RIGHTS.'>'."\n";
        }
        
        return $xmlStr;
    }
}

/**
 * <relation>
 *      <kind>
 *          [Vocabulary型参照] =>Vocabulary型
 *      </kind>
 *      <resource>
 *          <identifier>(※複数存在する可能性アリ)  =>Identifier型 
 *              [Identifier型参照]
 *          </identifier>
 *          <description>(※複数存在する可能性アリ)
 *              <string language=""></string>  =>LangString型
 *          </description>
 *      </resource>
 * </relation>
 */

/**
 * Relation tags generated classes
 * Relation型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Relation
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $kind = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $resource = null;
    
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;

    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction){
        $this->repositoryAction = $repositoryAction;
    }
    /**
     * Set Kind
     * Kind設定
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $kind Kind Kind
     */
    public function addKind(Repository_Oaipmh_LOM_Vocabulary $kind){
        //check
        $kind_value = RepositoryOutputFilterLOM::relation($kind->getValue());
        if($this->kind == null && strlen($kind_value)>0){
            $this->kind = $kind;
        }
    }
    /**
     * Set Resource
     * Resource設定
     * 
     * @param Repository_Oaipmh_LOM_Resource $resource Resource Resource
     */
    public function addResource(Repository_Oaipmh_LOM_Resource $resource){
        if($this->resource == null){
            $this->resource = $resource;
        }
    }
    /**
     * Output
     * Relationタグ出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output(){
        $xmlStr = '';
        
        if($this->resource == null){
            return '';
        }
        
        //kind
        $resource = '';
        if($this->resource != null){
            $resource = $this->resource->output();
        }
        
        if($this->kind != null && strlen($resource)>0){
        	$xml = $this->kind->output();
        	if(strlen($xml)>0){
                $xmlStr .= '<'.RepositoryConst::LOM_TAG_KIND.'>';
                $xmlStr .= $xml;
                $xmlStr .= '</'.RepositoryConst::LOM_TAG_KIND.'>'."\n";
            }
        }
        //resource
        $xmlStr .= $resource;
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_RELATION.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_RELATION.'>'."\n";
        }
        
        return $xmlStr;
    }
    
}

/**
 * <annotation>
 *      <entity></entity>
 *      <date>
 *          [DateTime型参照]  =>DateTime型参照
 *      </date>
 *      <description>
 *          <string language=""></string>  =>LangString型
 *      </description>
 * </annotation>
 */

/**
 * Annotation tags generated classes
 * Annotation型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Annotation
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $entity = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $date = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $description = null;
    
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction){
        $this->repositoryAction = $repositoryAction;
    }
    
    /**
     * Set Entity
     * Entity設定
     * 
     * @param string $entity Entity Entity
     */
    public function addEntity($entity){
        //encording
        $entity = $this->repositoryAction->forXmlChange($entity);
        if($this->entity == null || strlen($entity)==0){
            $this->entity = $entity;
        }
    }
    /**
     * Set Date
     * Date設定
     * 
     * @param Repository_Oaipmh_LOM_DateTime $date Date Date
     */
    public function addDate(Repository_Oaipmh_LOM_DateTime $date){
        if($this->date == null){
            $this->date = $date;
        }
    }
    /**
     * Set Description
     * Description設定
     * 
     * @param Repository_Oaipmh_LOM_LangString $description Description Description
     */
    public function addDescription(Repository_Oaipmh_LOM_LangString $description){
        if($this->description == null){
            $this->description = $description;
        }
    }
    
    //getter
    /**
     * Get Entity
     * Entity取得
     *
     * @return string
     */
    public function getEntity(){
        return $this->entity;
    }
    /**
     * Get Date
     * Date取得
     *
     * @return string
     */
    public function getDate(){
        return $this->date;
    }
    /**
     * Get Description
     * Description取得
     *
     * @return string
     */
    public function getDescription(){
        return $this->description;
    }
    
    /**
     * Output
     * Annotationタグ出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output(){
        $xmlStr = '';
        
        //entity
        if($this->entity != null && strlen($this->entity)>0){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_ENTITY.'>'.$this->entity.'</'.RepositoryConst::LOM_TAG_ENTITY.'>'."\n";
        }
        //date
        if($this->date != null){
        	$xml = $this->date->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_DATE.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_DATE.'>'."\n";
            }
        }
        //description
        if($this->description != null){
        	$xml = $this->description->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_DESCRIPTION.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_DESCRIPTION.'>'."\n";
            }
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_ANNOTAION.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_ANNOTAION.'>'."\n";
        }
        
        return $xmlStr;
    }
}

/**
 * <classification>
 *      <purpose>
 *          [Vocabulary型参照] =>Vocabulary型
 *      </purpose>
 *      <taxonPath>(※複数存在する可能性アリ)
 *          <source>
 *              <string language=""></string>  =>LangString型
 *          </source>
 *          <taxon>(※複数存在する可能性アリ)
 *              <id></id>
 *              <entry>
 *                  <string language=""></string>  =>LangString型
 *              </entry>
 *          </taxon>
 *      </taxonPath>
 *      <description>
 *          <string language=""></string>  =>LangString型
 *      </description>
 *      <keyword>(※複数存在する可能性アリ)
 *          <string language=""></string>  =>LangString型
 *      </keyword>
 * </classification>
 */

/**
 * Classification tags generated classes
 * Classification型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Classification
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $purpose = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $taxonPath = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $description = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $keyword = array();
    
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction){
        $this->repositoryAction = $repositoryAction;
    }
    
    /**
     * Set Purpose
     * Purpose設定
     * 
     * @param Repository_Oaipmh_LOM_Vocabulary $purpose Purpose Purpose
     */
    public function addPurpose(Repository_Oaipmh_LOM_Vocabulary $purpose){
        //check
        $purpose_value = RepositoryOutputFilterLOM::classificationPurpose($purpose->getValue());
        if($this->purpose == null && strlen($purpose_value)>0){
            $this->purpose = $purpose;
        }
        
    }
    /**
     * Set TaxonPath
     * TaxonPath設定
     * 
     * @param Repository_Oaipmh_LOM_TaxonPath $taxonPath TaxonPath TaxonPath
     */
    public function addTaxonPath(Repository_Oaipmh_LOM_TaxonPath $taxonPath){
        array_push($this->taxonPath, $taxonPath);
    }
    
    /**
     * Set Description
     * Description設定
     * 
     * @param Repository_Oaipmh_LOM_LangString $description Description Description
     */
    public function addDescription(Repository_Oaipmh_LOM_LangString $description){
        if($this->description == null){
            $this->description = $description;
        }
    }
    /**
     * Set Keyword
     * Keyword設定
     * 
     * @param Repository_Oaipmh_LOM_LangString $keyword Keyword Keyword
     */
    public function addKeyword(Repository_Oaipmh_LOM_LangString $keyword){
        array_push($this->keyword, $keyword);
    }
    
    //getter
    /**
     * Get PurposeValue
     * purposeValue取得
     *
     * @return string Output String 出力文字列
     */
    public function getPurposeValue(){
        if($this->purpose == null){
           return '';
        }
        return $this->purpose->getValue();
    }
    
    /**
     * Get TaxonPath
     * TaxonPath取得
     *
     * @return string Output String 出力文字列
     */
    public function getTaxonPathSource(){
        if($this->taxonPath == null){
            return null;
        }
        
        $ret_source = '';
        
        for($ii=0;$ii<count($this->taxonPath);$ii++){
            $source = $this->taxonPath[$ii]->getSource();
            if(strlen($source)>0){
                $ret_source = $source;
                break;
            }
        }
        return $ret_source;
    }
    
    /**
     * Get TaxonPathCount
     * TaxonPathCount取得
     *
     * @return int Count TaxonPathの数
     */
    public function getTaxonPathCount(){
        if($this->taxonPath == null){
            return null;
        }
        $count = 0;
        
        for($ii=0;$ii<count($this->taxonPath);$ii++){
            $taxCnt = $this->taxonPath[$ii]->getTaxonCount();
            if($taxCnt != 0){
                $count = $taxCnt;
                break;
            }
        }
        return $count;
    }
    
    /**
     * Get DescriptionString
     * DescriptionString取得
     *
     * @return string Output String 出力文字列
     */
    public function getDescriptionString(){
        if($this->description == null){
            return '';
        }
        return $this->description->getString();
    }
    /**
     * Get Keyword
     * Keyword取得
     *
     * @return string Output String 出力文字列
     */
    public function getKeyword(){
        return $this->keyword;
    }
    
    /**
     * Set TaxonPathSource
     * TaxonPathSource設定
     *
     * @param string $taxonPathString TaxonPathSource TaxonPathSource
     */
    public function setTaxonPathSource($taxonPathString){
        if($this->taxonPath == null){
            $taxonPath = new Repository_Oaipmh_LOM_TaxonPath($this->repositoryAction);
            $taxonPath->addSource($taxonPathString);
            array_push($this->taxonPath, $taxonPath);
        }else{
            for($ii=0;$ii<count($this->taxonPath);$ii++){
                if(strlen($this->taxonPath[$ii]->getSource()) == 0){
                    //いれる
                    $this->taxonPath[$ii]->addSource($taxonPathString);
                    break;
                }
            }
        }
    }
    
    /**
     * Set setTaxonPathEntry
     * setTaxonPathEntry設定
     *
     * @param string $taxonPathEntry TaxonPathEntry TaxonPathEntry
     */
    public function setTaxonPathEntry($taxonPathEntry){
        if($this->taxonPath == null){
            $taxonPath = new Repository_Oaipmh_LOM_TaxonPath($this->repositoryAction);
            //$taxon = new Repository_Oaipmh_LOM_Taxon($this->repositoryAction, '', $taxonPathEntry);
            $taxon = new Repository_Oaipmh_LOM_Taxon($this->repositoryAction);
            $taxon->setId('');
            $taxon->setEntry($taxonPathEntry);
            
            $taxonPath->addTaxon($taxon);
            array_push($this->taxonPath, $taxonPath);
        }else{
            for($ii=0;$ii<count($this->taxonPath);$ii++){
                if($this->taxonPath[$ii]->getTaxonCount() == 0){
                    //いれる
                    $taxonPath = new Repository_Oaipmh_LOM_TaxonPath($this->repositoryAction);
                    //$taxon = new Repository_Oaipmh_LOM_Taxon($this->repositoryAction, '', $taxonPathEntry);
                    $taxon = new Repository_Oaipmh_LOM_Taxon($this->repositoryAction);
                    $taxon->setId('');
                    $taxon->setEntry($taxonPathEntry);
                    
                    $this->taxonPath[$ii]->addTaxon($taxon);
                    break;
                }
            }
        }
    }
    
    
    /**
     * Output
     * classificationタグ生成処理
     * 
     * @return string xml str XML文字列
     */
    public function output(){
        $xmlStr = '';
        
        //purpose
        if($this->purpose != null){
        	$xml = $this->purpose->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_PURPOSE.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_PURPOSE.'>'."\n";
            }
        }
        //taxonPath
        for($ii=0;$ii<count($this->taxonPath);$ii++){
        	$xml = $this->taxonPath[$ii]->output();
        	if(strlen($xml)>0){
	            $xmlStr .= $xml;
            }
        }
        //description
        if($this->description != null){
        	$xml = $this->description->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_DESCRIPTION.'>';
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_DESCRIPTION.'>'."\n";
            }
        }
        //keyword
        for($ii=0;$ii<count($this->keyword);$ii++){
        	$xml = $this->keyword[$ii]->output();
        	if(strlen($xml)>0){
	            $xmlStr .= '<'.RepositoryConst::LOM_TAG_KEYWORD.'>'."\n";
	            $xmlStr .= $xml;
	            $xmlStr .= '</'.RepositoryConst::LOM_TAG_KEYWORD.'>'."\n";
            }
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_CLASSIFICATION.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_CLASSIFICATION.'>'."\n";
        }
        
        return $xmlStr;
    }
    
}

/************************************************ The point of a branch  **********************************************/

/**
 * <string languege=""> </string>
 */

/**
 * LangString tags generated classes
 * LangString型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_LangString
{

    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $string = '';
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $language= '';
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     * @param string $str string 文字列
     * @param string $lang Language 言語
     */
    public function __construct($repositoryAction, $str, $lang='')
    {
        $this->repositoryAction = $repositoryAction;
        $this->string = $str;
        $this->language = $lang;
    }
    
    //getter
    /**
     * Get LangString
     * LangString取得
     *
     * @return string Output string 出力文字列
     */
    public function getString(){
        return $this->string;
    }
    //setter
    /**
     * Set LangString
     * LangString取得
     *
     * @param string $string LangString LangString
     */
    public function setString($string){
        $this->string = $string;
    }
    
    /**
     * Output
     * LangString型の出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output()
    {
        $xmlStr = '';
        
        //encording language
        $this->language = $this->repositoryAction->forXmlChange($this->language);
        //encording string
        $this->string = $this->repositoryAction->forXmlChange($this->string);
        
        if(strlen($this->string) > 0){
            // format language.
            $this->language = RepositoryOutputFilter::language($this->language);
            if($this->language === RepositoryConst::ITEM_LANG_OTHER)
            {
                $this->language = '';
            }
            // set language
            if(strlen($this->language) == 0)
            {
                $xmlStr .= '<'.RepositoryConst::LOM_TAG_STRING.'>'."\n";
            }
            else
            {
                $xmlStr .= '<'.RepositoryConst::LOM_TAG_STRING.' '.RepositoryConst::LOM_TAG_LANGUAGE.'="'.$this->language.'">'."\n";
            }
            $xmlStr .= $this->string.'</'.RepositoryConst::LOM_TAG_STRING.'>'."\n";
        }
        return $xmlStr;
    }

}

/**
 * <dateTime> </dateTime>
 * <description> </description>
 */

/**
 * DateTime tags generated classes
 * DateTime型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_DateTime
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $dateTime = '';
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $description = '';
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction)
    {
        $this->repositoryAction = $repositoryAction;
        
    }
    
    //getter
    /**
     * Get DateTime
     * DateTime取得
     *
     * @return string Output string 出力文字列
     */
    public function getDateTime(){
        return $this->dateTime;
        
    }
    //setter
    /**
     * Set Description
     * Description設定
     *
     * @param Repository_Oaipmh_LOM_LangString $description Description Description
     */
    public function setDescription(Repository_Oaipmh_LOM_LangString $description){
        $this->description = $description;
    }
    
    /**
     * Set DateTime
     * DateTime設定
     *
     * @param string $dateTime DateTime DateTime
     */
    public function setDateTime($dateTime){
        $this->dateTime = $dateTime;
        
    }
    
    /**
     * Output
     * DateTime型の出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output()
    {
        $xmlStr = '';
        
        //encording
        $this->dateTime = $this->repositoryAction->forXmlChange($this->dateTime);
        
        // format date
        $this->dateTime = RepositoryOutputFilter::date($this->dateTime);
        
        if(strlen($this->dateTime)>0){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_DATE_TIME.'>'.$this->dateTime.'</'.RepositoryConst::LOM_TAG_DATE_TIME.'>'."\n";
        }
        
        if($this->description != null){
            $xml = $this->description->output();
            if(strlen($xml)>0){
                $xmlStr .= '<'.RepositoryConst::LOM_TAG_DESCRIPTION.'>'.$this->description->output().'</'.RepositoryConst::LOM_TAG_DESCRIPTION.'>'."\n";
            }
        }
        return $xmlStr;
    }
}
    
/**
 * <duration> </duration>
 * <description> </description>
 */

/**
 * Duration tags generated classes
 * Duration型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Duration
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $duration = '';
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $description = '';
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction)
    {
        $this->repositoryAction = $repositoryAction;
    }
    
    //getter
    /**
     * Get Duration
     * Duration取得
     * 
     * @param string Output string 出力文字列
     */
    public function getDuration(){
        return $this->duration;
    }
    
    //setter
    /**
     * Set Duration
     * Duration設定
     *
     * @param string $duration Duration Duration
     */
    public function setDuration($duration){
        $this->duration = $duration;
    }
    /**
     * Set Description
     * Description設定
     *
     * @param Repository_Oaipmh_LOM_LangString $description Description Description
     */
    public function setDescription(Repository_Oaipmh_LOM_LangString $description){
        $this->description = $description;
    }
    
    /**
     * Output
     * Duration型の出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output()
    {
        $xmlStr = '';
        
        //encording
        $this->duration = $this->repositoryAction->forXmlChange($this->duration);
        
        // format duration.
        $this->duration = RepositoryOutputFilterLOM::duration($this->duration);
        if(strlen($this->duration)>0){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_DURATION.'>'.$this->duration.'</'.RepositoryConst::LOM_TAG_DURATION.'>'."\n";
        }
        
        $xml_description = $this->description->output();
        if(strlen($xml_description)>0){
        	$xmlStr .= '<'.RepositoryConst::LOM_TAG_DESCRIPTION.'>'.$xml_description.'</'.RepositoryConst::LOM_TAG_DESCRIPTION.'>'."\n";
        }
        
        return $xmlStr;
    }
}
    
/**
 * <source> </source>
 * <value> </value>
 */

/**
 * Vocabulary tags generated classes
 * Vocabulary型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Vocabulary
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $source = '';
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $value = '';
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;

    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     * @param string $source Source Source
     * @param string $value Value Value
     */
    public function __construct($repositoryAction, $source ,$value)
    {
        $this->repositoryAction = $repositoryAction;
        $this->source = $source;
        $this->value = $value;
    }

    //getter
    /**
     * Get value
     * valueを取得する
     * 
     * @return string Output string 出力文字列
     */
    public function getValue(){
        return $this->value;
    }
    //setter
    /**
     * Set Value
     * valueを設定する
     * 
     * @param string $value Value Value
     */
    public function setValue($value){
        $this->value = $value;
    }
    
    /**
     * Output
     * Vocabulary型の出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output()
    {
        $xmlStr = '';
        
        //encording
        $this->source = $this->repositoryAction->forXmlChange($this->source);
        $this->value = $this->repositoryAction->forXmlChange($this->value);
        
        if(strlen($this->source)>0){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_SOURCE.'>'.$this->source.'</'.RepositoryConst::LOM_TAG_SOURCE.'>'."\n";
        }
        if(strlen($this->value)>0){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_VALUE.'>'.$this->value.'</'.RepositoryConst::LOM_TAG_VALUE.'>'."\n";
        }
        
        return $xmlStr;
    }
}
    
/****************************************** tag *********************************************/
    
/**
 * <identifier>
 *     <catalog> </catalog>
 *     <entity> </entity>
 * </identifier>
 */

/**
 * Identifier tags generated classes
 * Identifier型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Identifier
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $entry = '';
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $catalog = '';
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     * @param string $entry Entry Entry
     * @param string $catalog Catalog Catalog
     */
    public function __construct($repositoryAction, $entry, $catalog=RepositoryConst::LOM_TAG_IDENTIFIER)
    {
        $this->repositoryAction = $repositoryAction;
        $this->entry = $entry;
        $this->catalog = $catalog;
    }
    
    /**
     * Output
     * Identifier型の出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output()
    {
        $xmlStr = '';
        
        //encording
        $this->catalog = $this->repositoryAction->forXmlChange($this->catalog);
        $this->entry = $this->repositoryAction->forXmlChange($this->entry);
        if(strlen($this->entry) > 0){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_IDENTIFIER.'>';
            if(strlen($this->catalog) > 0)
            {
                $xmlStr .= '<'.RepositoryConst::LOM_TAG_CATALOG.'>'.$this->catalog.'</'.RepositoryConst::LOM_TAG_CATALOG.'>'."\n";
            }
            
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_ENTRY.'>'.$this->entry.'</'.RepositoryConst::LOM_TAG_ENTRY.'>'."\n";
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_IDENTIFIER.'>';
        }
        
        return $xmlStr;
    }
}

/**
 * <taxon>
 *      <id> </id>
 *      <entry>
 *          <string language=""></string>  =>LangString型 
 *      </entry>
 * </taxon>
 */

/**
 * Taxon tags generated classes
 * Taxon型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Taxon
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $id = '';
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $entry = null;
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction)
    {
        $this->repositoryAction = $repositoryAction;
    }
    
    //getter
    /**
     * Get TaxonEntry
     * TaxonEntry取得
     *
     * @return string Output string 出力文字列
     */
    public function getTaxonEntry(){
        if($this->entry == null){
            return '';
        }
        
        return $this->entry->getString();
    }
    //setter
    /**
     * Set Id
     * Id設定
     *
     * @param string $id Id Id
     */
    public function setId($id){
        $this->id = $id;
    }
    /**
     * Set Entry
     * Entry設定
     *
     * @param string $entry Entry Entry
     */
    public function setEntry(Repository_Oaipmh_LOM_LangString $entry){
        $this->entry = $entry;
    }
    /**
     * Set TaxonEntry
     * TaxonEntry設定
     *
     * @param string $entry TaxonEntry TaxonEntry
     */
    public function setTaxonEntry($entry){
        if($this->entry == null){
            return;
        }
        $this->entry->setString($entry);
    }
    
    /**
     * Taxon型の出力処理
     * @return string xml str
     */
    public function output()
    {
        $xmlStr = '';
        
        //encording
        $this->id = $this->repositoryAction->forXmlChange($this->id);
        
        $xmlStr .= '<'.RepositoryConst::LOM_TAG_TAXON.'>'."\n";
        if(strlen($this->id)>0){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_ID.'>'.$this->id.'</'.RepositoryConst::LOM_TAG_ID.'>'."\n";
        }
        $xmlStr .= '<'.RepositoryConst::LOM_TAG_ENTRY.'>'.$this->entry->output().'</'.RepositoryConst::LOM_TAG_ENTRY.'>'."\n";
        $xmlStr .= '</'.RepositoryConst::LOM_TAG_TAXON.'>'."\n";
        
        return $xmlStr;
    }
    
}

/**
 * <taxonPath>
 *   <source>
 *      <string language="XX"></string>
 *   </source>
 *   <taxon>(※複数入力可能性アリ)
 *      <id> </id>
 *      <entry>
 *          <string language=""></string>  =>LangString型 
 *      </entry>
 *   </taxon>
 * </taxonPath>
 */

/**
 * TaxonPath tags generated classes
 * TaxonPath型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_TaxonPath{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var Repository_Oaipmh_LOM_LangString
     */
    private $source = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $taxon = array();
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction)
    {
        $this->repositoryAction = $repositoryAction;
    }
    /**
     * Add Source
     * Source追加
     *
     * @param Repository_Oaipmh_LOM_LangString $source Source Source
     */
    public function addSource(Repository_Oaipmh_LOM_LangString $source){
        if($this->source == null){
             $this->source = $source;
        }
    }
    /**
     * Add Taxon
     * Taxon追加
     *
     * @param Repository_Oaipmh_LOM_Taxon $taxon Taxon Taxon
     */
    public function addTaxon(Repository_Oaipmh_LOM_Taxon $taxon){
        array_push($this->taxon, $taxon);
    }
    
    //getter
    /**
     * Get Source
     * Source取得
     *
     * @return string Output string 出力文字列
     */
    public function getSource(){
        if($this->source == null){
            return '';
        }
        return $this->source->getString();
    }
    /**
     * Get TaxonCount
     * TaxonCount取得
     *
     * @return int Count TaxonCount数
     */
    public function getTaxonCount(){
        if($this->taxon == null){
            return 0;
        }
        return count($this->taxon);
    }
    
    /**
     * Output
     * TaxonPathタグ出力処理
     * 
     * @return string xml string XML文字列
     */
    public function output(){
        $xmlStr = '';
        
        //source
        if($this->source != null){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_SOURCE.'>';
            $xmlStr .= $this->source->output();
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_SOURCE.'>';
        }
        //taxon
        for($ii=0;$ii<count($this->taxon);$ii++){
            $xmlStr .= $this->taxon[$ii]->output();
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_TAXON_PATH.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_TAXON_PATH.'>'."\n";
        }
        return $xmlStr;
    }
    
    
}

/**
 * <orComposite>
 *      <type>
 *          [Vocabulary参照]  =>Vocabulary型
 *      </type>
 *      <name>
 *          [Vocabulary参照]  =>Vocabulary型
 *      </name>
 *      <minimumVersion></minimumVersion>
 *      <maximumVersion></maximumVersion>
 * </orComposite>
 */

/**
 * OrComposite tags generated classes
 * OrComposite型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_OrComposite
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $type = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $name = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $minimumVersion = null;
    /**
     * Metadata
     * メタデータ
     *
     * @var string
     */
    private $maximumVersion = null;
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction)
    {
        $this->repositoryAction = $repositoryAction;
    }
    
    //getter
    /**
     * Get TypeValue
     * typeタグのValueを取得
     * 
     * @return string output string 出力文字列
     */
    public function getTypeValue(){
        $type = '';
        if($this->type != null){
            $type = $this->type->getValue();
        }
        return $type;
    }
    /**
     * Get NameValue
     * nameタグのValueを取得
     * 
     * @return string output string 出力文字列
     */
    public function getNameValue(){
        $name = '';
        if($this->name != null){
            $name = $this->name->getValue();
        }
        return $name;
    }
    /**
     * Get MinimumVersion
     * MinimumVersionタグを取得
     * 
     * @return string MinimumVersion MinimumVersion
     */
    public function getMinimumVersion(){
        return $this->minimumVersion;
    }
    /**
     * Get MaximumVersion
     * MaximumVersionタグを取得
     * 
     * @return string MaximumVersion MaximumVersion
     */
    public function getMaximumVersion(){
        return $this->maximumVersion;
    }
    
    //setter
    /**
     * Set TypeString
     * TypeStringを設定
     * 
     * @param string $value TypeString TypeString
     */
    public function setTypeString($value){
        $this->type = $value;
    }
    /**
     * Set NameString
     * NameStringを設定
     * 
     * @param string $value NameString NameString
     */
    public function setNameString($value){
        $this->name = $value;
    }
    /**
     * Set Type
     * Type設定
     *
     * @param Repository_Oaipmh_LOM_Vocabulary $value Type Type
     */
    public function setType(Repository_Oaipmh_LOM_Vocabulary $value){
        $this->type = $value;
    }
    /**
     * Set Name
     * Name設定
     *
     * @param Repository_Oaipmh_LOM_Vocabulary $value Name Name
     */
    public function setName(Repository_Oaipmh_LOM_Vocabulary $value){
        $this->name = $value;
    }
    
    /**
     * Set MinimumVersion
     * MinimumVersion設定
     *
     * @param string $value MinimumVersion MinimumVersion
     */
    public function setMinimumVersion($value){
        $this->minimumVersion = $value;
    }
    /**
     * Set MaximumVersion
     * MaximumVersion設定
     *
     * @param string $value MaximumVersion MaximumVersion
     */
    public function setMaximumVersion($value){
        $this->maximumVersion = $value;
    }
    
    /**
     * Output
     * OnComposite型の出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output(){
        $xmlStr = '';
        //type
        if($this->type != null){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_TYPE.'>';
            $xmlStr .= $this->type->output();
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_TYPE.'>'."\n";
        }
        //name
        if($this->name != null){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_NAME.'>';
            $xmlStr .= $this->name->output();
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_NAME.'>'."\n";
        }
        //minimumVersion
        if($this->minimumVersion != null){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_MINIMUM_VERSION.'>'.$this->minimumVersion.'</'.RepositoryConst::LOM_TAG_MINIMUM_VERSION.'>'."\n";
        }
        //maximumVersion
        if($this->maximumVersion != null){
           $xmlStr .= '<'.RepositoryConst::LOM_TAG_MAXIMUM_VERSION.'>'.$this->maximumVersion.'</'.RepositoryConst::LOM_TAG_MAXIMUM_VERSION.'>'."\n";
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_OR_COMPOSITE.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_OR_COMPOSITE.'>'."\n";
        }
        return $xmlStr;
    }
}

/**
 * <resource>
 *      <identifier> (※複数存在する可能性アリ)  =>Identifier型
 *          [Identifier参照]
 *      </identifier>
 *      <description> (※複数存在する可能性アリ)
 *          <string language=""></string>  =>LangString型
 *      </description>
 * </resource>
 */

/**
 * Resource tags generated classes
 * Resource型タグ生成クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_LOM_Resource
{
    /**
     * メンバ変数
     */
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $identifier = array();
    /**
     * Metadata
     * メタデータ
     *
     * @var array[$ii]
     */
    private $description = array();
    
    /**
     * WEKO common processing object
     * WEKO共通処理オブジェクト
     *
     * @var RepositoryAction
     */
    private $repositoryAction = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param RepositoryAction $repositoryAction WEKO common processing object WEKO共通処理オブジェクト
     */
    public function __construct($repositoryAction){
        $this->repositoryAction = $repositoryAction;
    }
    
    /**
     * Set Identifier
     * Identifier設定
     * 
     * @param Repository_Oaipmh_LOM_Identifier $identifier Identifier Identifier
     */
    public function addIdentifier(Repository_Oaipmh_LOM_Identifier $identifier){
        array_push($this->identifier, $identifier);
    }
    /**
     * Set Description
     * Description設定
     * 
     * @param Repository_Oaipmh_LOM_LangString $description Description Description
     */
    public function addDescription(Repository_Oaipmh_LOM_LangString $description){
        array_push($this->description, $description);
    }
    
    /**
     * Output
     * Resource型の出力処理
     * 
     * @return string xml str XML文字列
     */
    public function output(){
        $xmlStr = '';
        
        //identifier
        for($ii=0;$ii<count($this->identifier);$ii++){
            $xmlStr .= $this->identifier[$ii]->output();
        }
        //description
        for($ii=0;$ii<count($this->description);$ii++){
            $xmlStr .= '<'.RepositoryConst::LOM_TAG_DESCRIPTION.'>';
            $xmlStr .= $this->description[$ii]->output();
            $xmlStr .= '</'.RepositoryConst::LOM_TAG_DESCRIPTION.'>'."\n";
        }
        
        if(strlen($xmlStr)>0){
            $xmlStr = '<'.RepositoryConst::LOM_TAG_RESOURCE.'>'.$xmlStr.'</'.RepositoryConst::LOM_TAG_RESOURCE.'>'."\n";
        }
        
        return $xmlStr;
    }
    
}
?>