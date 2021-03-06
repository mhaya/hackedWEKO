<?php
/**
 * Class for Harvest item registration (LIDO)
 * ハーベストアイテム登録用クラス(LIDO)
 * 
 * @package WEKO
 */
// --------------------------------------------------------------------
//
// $Id: HarvestingOaipmhLido.class.php 36496 2014-05-30 01:08:48Z rei_matsuura $
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
 * Harvest item registration base class
 * ハーベストアイテム登録基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/action/common/harvesting/filter/HarvestingOaipmhAbstract.class.php';

/**
 * LIDO item type attribute ID constant class
 * LIDOアイテムタイプ属性ID定数クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class AttrId
{
    /**
     * Minimam id
     * 最少ID
     * 
     * @var int
     */
    const MIN_ID = 1;
    /**
     * Maximum id
     * 最大ID
     * 
     * @var int
     */
    const MAX_ID = 38;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_REC_ID = 1;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_OBJECT_WORK_CONCEPT_ID = 2;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_OBJECT_WORK_TERM = 3;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_CLASSIFICATION_CONCEPT_ID = 4;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_CLASSIFICATION_TERM = 5;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_TITLE_SET = 6;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_INSCRIPTION = 7;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_REPOSITORY_NAME_LEGAL_BODY_NAME = 8;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_REPOSITORY_NAME_LEGAL_BODY_WEB_LINK = 9;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_REPOSITORY_SET_WORK_ID = 10;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_DISPLAY_STATE = 11;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_DESCRIPTIVE_NOTE_VALUE = 12;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_DESPLAY_OBJECT_MEASUREMENTS = 13;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_DISPLAY_EVENT = 14;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_EVENT_TYPE = 15;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_EVENT_ACTOR = 16;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_EVENT_DATE_DISPLAY_DATE = 17;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_EVENT_DATE_EARLIEST = 18;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_EVENT_DATE_LATEST = 19;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_PERIOD_NAME = 20;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_EVENT_DISPLAY_PLACE = 21;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_EVENT_PLACE_GML = 22;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_EVENT_MATERIALS_TECH = 23;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_DISPLAY_SUBJECT = 24;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_RELATED_WORK = 25;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_RECORD_ID = 26;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_RECORD_TYPE = 27;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_RECORD_SOURCE = 28;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_RECORD_INFO_LINK = 29;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_RECORD_METADATA_DATE = 30;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_RESOURCE_REPRESENTATION = 31;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_RESOURCE_DESCRIPTION = 32;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_RESOURCE_SOURCE = 33;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LIDO_RIGHTS_RESOURCE = 34;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REPO_ID = 35;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const IDENTIFIER = 36;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const DATESTAMP = 37;
}

/**
 * Class for Harvest item registration (LIDO)
 * ハーベストアイテム登録用クラス(LIDO)
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class HarvestingOaipmhLido extends HarvestingOaipmhAbstract
{
    // ---------------------------------------------
    // Const
    // ---------------------------------------------
    // Itemtype data
    /**
     * Item type id
     * アイテムタイプID
     * 
     * @var int
     */
    const ITEMTYPE_ID = 20017;
    /**
     * Error message
     * エラーメッセージ
     * 
     * @var string
     */
    const MSG_ER_GET_LIDO_REQUIRED_DATA = "repository_harvesting_error_get_required_data_for_lido";
    
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_CATEGORY_START = "<lido:category";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_CATEGORY_END = "<\/lido:category>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_REPOSITORY_LOCATION_START = "<lido:repositoryLocation";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_REPOSITORY_LOCATION_END = "<\/lido:repositoryLocation>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RELATED_WORK_REL_TYPE_START = "<lido:relatedWorkRelType";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RELATED_WORK_REL_TYPE_END = "<\/lido:relatedWorkRelType>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RELATED_WORK_OBJECT_ONE_START = "<lido:object ";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RELATED_WORK_OBJECT_TWO_START = "<lido:object>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RELATED_WORK_OBJECT_END = "<\/lido:object>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RESOURCE_ID_START = "<lido:resourceID";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RESOURCE_ID_END = "<\/lido:resourceID>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RESOURCE_TYPE_START = "<lido:resourceType";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RESOURCE_TYPE_END = "<\/lido:resourceType>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RESOURCE_REL_TYPE_START = "<lido:resourceRelType";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RESOURCE_REL_TYPE_END = "<\/lido:resourceRelType>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RESOURCE_PERSPECTIVE_START = "<lido:resourcePerspective";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RESOURCE_PERSPECTIVE_END = "<\/lido:resourcePerspective>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RESOURCE_DATE_TAKEN_START = "<lido:resourceDateTaken";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RESOURCE_DATE_TAKEN_END = "<\/lido:resourceDateTaken>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_ACTOR_IN_ROLE_START = "<lido:actorInRole";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_ACTOR_IN_ROLE_END = "<\/lido:actorInRole>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_TERM_MATERIALS_TECH_START = "<lido:termMaterialsTech";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_TERM_MATERIALS_TECH_END = "<\/lido:termMaterialsTech>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_NAME_PLACE_SET_START = "<lido:namePlaceSet";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_NAME_PLACE_SET_END = "<\/lido:namePlaceSet>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_PART_OF_PLACE_START = "<lido:partOfPlace";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_PART_OF_PLACE_END = "<\/lido:partOfPlace>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_MATERIAL_TECH_START = "<lido:materialsTech";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_MATERIAL_TECH_END = "<\/lido:materialsTech>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_SUBJECT_ONE_START = "<lido:subject ";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_SUBJECT_TWO_START = "<lido:subject>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_SUBJECT_END = "<\/lido:subject>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RIGHTS_WORK_WRAP_START = "<lido:rightsWorkWrap";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RIGHTS_WORK_WRAP_END = "<\/lido:rightsWorkWrap>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RECORD_RIGHTS_START = "<lido:recordRights";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_RECORD_RIGHTS_END = "<\/lido:recordRights>";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_OBJECT_PUBLISHED_ID_START = "<lido:objectPublishedID";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const LIDO_TAG_NOT_USED_OBJECT_PUBLISHED_ID_END = "<\/lido:objectPublishedID>";
    /**
     * Pattern
     * パターン
     * 
     * @var string
     */
    const NOT_USED_CONTENT = ".*?";
    /**
     * Delimiter
     * 分割文字
     * 
     * @var string
     */
    const OR_STRING = "|";
    
    // ---------------------------------------------
    // Private member
    // ---------------------------------------------
    /**
     * Metadata count
     * メタデータ数
     * 
     * @var array[$ii]
     */
    protected $cntMetadata = array();
    /**
     * Blank required count
     * 空文字必須一覧
     * 
     * @var array[$ii]
     */
    private $cntBlankRequiredMetadata = array();
    
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
        $this->setItemtypeId(self::ITEMTYPE_ID);
        $this->setRequiredParam(AttrId::REPO_ID, AttrId::IDENTIFIER, AttrId::DATESTAMP, AttrId::MAX_ID);
        $this->initMember();
    }
    
    // ---------------------------------------------
    // Private method
    // ---------------------------------------------
    /**
     * Init data
     * 初期化
     */
    private function initMember()
    {
        $this->cntMetadata = array();
        for($ii=AttrId::MIN_ID; $ii<=AttrId::MAX_ID; $ii++)
        {
            $this->cntMetadata[$ii] = 0;
        }
        $this->cntBlankRequiredMetadata[AttrId::LIDO_OBJECT_WORK_CONCEPT_ID] = 0;
        $this->cntBlankRequiredMetadata[AttrId::LIDO_OBJECT_WORK_TERM] = 0;
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
        // 1. Init metadata array
        $metadata[self::KEY_IR_BASIC] = $this->initIrBasic();
        $metadata[self::KEY_IR_METADATA] = array();
        
        // 2. Set metadata to array
        $ret = $this->setMetadataToArray($metadataXml, $repositoryId, $metadata);
        
        return $ret;
    }
    
    /**
     * Set metadata to array
     * メタデータ変換
     *
     * @param string $metadataXml Metadata XML XML文字列
     * @param int $repositoryId Repository id リポジトリID
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     * @return boolean Result 結果
     */
    private function setMetadataToArray($metadataXml, $repositoryId, &$metadata)
    {
        try
        {
            // Set pub_date by TransStartDate
            $tmpDate = explode(" ", $this->TransStartDate);
            $tmpDate = explode("-", $tmpDate[0]);
            $metadata[self::KEY_IR_BASIC][self::KEY_PUB_YEAR] = intval($tmpDate[0]);
            $metadata[self::KEY_IR_BASIC][self::KEY_PUB_MONTH] = intval($tmpDate[1]);
            $metadata[self::KEY_IR_BASIC][self::KEY_PUB_DAY] = intval($tmpDate[2]);
            
            // DOM作成
            $dom = new DOMDocument('1.0', 'UTF-8');
            
            // metadataタグ内のXML読み込み
            $this->deleteTagNotUsed($metadataXml);
            $load_result = $dom->loadXML($metadataXml);
            if($load_result === false)
            {
                return false;
            }
            // get language
            $administrativeMetadatas = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_ADMINISTRATIVE_METADATA);
            $language = "";
            foreach($administrativeMetadatas as $administrativeMetadata)
            {
                if($administrativeMetadata !== null)
                {
                    $language = $administrativeMetadata->getAttributeNS(RepositoryConst::LIDO_XML_NAMESPACE_URL, RepositoryConst::LIDO_ATTR_LANG);
                }
            }
            $metadata[self::KEY_IR_BASIC][self::KEY_LANGUAGE] = RepositoryOutputFilter::language($language);
            
            /*****************************************************************/
            /****************************lidoRecID****************************/
            /*****************************************************************/
            // lidoRecID
            $lidoRecIDs = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_LIDO_REC_ID);
            $this->setLidoRecId($lidoRecIDs, $metadata);
            
            
            /*****************************************************************/
            /***********************descriptiveMetadata***********************/
            /*****************************************************************/
            
            //================================================================//
            //=====================objectClassificationWrap===================//
            //================================================================//
            // objectWorkTypeWrap.objectWorkType
            $objectWorkTypes = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_OBJECT_WORK_TYPE);
            $this->setObjectWorkType($objectWorkTypes, $metadata);
            // classificationWrap.classification
            $classifications = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_CLASSIFICATION);
            $this->setClassification($classifications, $metadata);
            
            //================================================================//
            //=====================objectIdentificationWrap===================//
            //================================================================//
            // titleWrap.titleSet
            $titleSets = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_TITLE_SET);
            $this->setTitleSet($titleSets, $metadata);
            // inscriptionsWrap.inscriptions.inscriptionTranscription
            $inscriptionTranscriptions = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_INSCRIPTION_TRANSCRIPTION);
            $this->setInscriptionTranscription($inscriptionTranscriptions, $metadata);
            // repositoryWrap.repositorySet
            $repositorySets = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_REPOSITORY_SET);
            $this->setRepositorySet($repositorySets, $metadata);
            // displayStateEditionWrap.displayState
            $displayStates = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_DISPLAY_STATE);
            $this->setDisplayState($displayStates, $metadata);
            // objectDescriptionWrap.objectDescriptionSet.objectDescriptionSet.descriptiveNoteValue
            $descriptiveNoteValues = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_DESCRIPTIVE_NOTE_VALUE);
            $this->setDescriptiveNoteValue($descriptiveNoteValues, $metadata);
            // objectMeasurementsWrap.objectMeasurementsSet.displayObjectMeasurements
            $displayObjectMeasurementss = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_DISPLAY_OBJECT_MEASUREMENTS);
            $this->setDisplayObjectMeasurements($displayObjectMeasurementss, $metadata);
            
            //================================================================//
            //============================eventWrap===========================//
            //================================================================//
            // eventSet
            $eventSets = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_EVENT_SET);
            $this->setEventSet($eventSets, $metadata);
            
            //================================================================//
            //========================objectRelationWrap======================//
            //================================================================//
            // subjectWrap.subjectSet.displaySubject
            $displaySubjects = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_DISPLAY_SUBJECT);
            $this->setDisplaySubject($displaySubjects, $metadata);
            // relatedWorksWrap.relatedWorkSet.relatedWork.displayObject
            $displayObjects = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_DISPLAY_OBJECT);
            $this->setDisplayObject($displayObjects, $metadata);
            
            
            /*****************************************************************/
            /**********************administrativeMetadata*********************/
            /*****************************************************************/
            
            //================================================================//
            //============================recordWrap==========================//
            //================================================================//
            // recordID
            $recordIDs = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_RECORD_ID);
            $this->setRecordID($recordIDs, $metadata);
            // recordType
            $recordTypes = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_RECORD_TYPE);
            $this->setRecordType($recordTypes, $metadata);
            // recordSource
            $recordSources = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_RECORD_SOURCE);
            $this->setRecordSource($recordSources, $metadata);
            // recordInfoSet
            $recordInfoSets = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_RECORD_INFO_SET);
            $this->setRecordInfoSet($recordInfoSets, $metadata);
            
            //================================================================//
            //===========================resourceWrap=========================//
            //================================================================//
            // resourceRepresentation
            $resourceRepresentations = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_RESOURCE_REPRESENTATION);
            $this->setResourceRepresentation($resourceRepresentations, $metadata);
            // resourceDescription
            $resourceDescriptions = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_RESOURCE_DESCRIPTION);
            $this->setResourceDescription($resourceDescriptions, $metadata);
            // resourceSource
            $resourceSources = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_RESOURCE_SOURCE);
            $this->setResourceSource($resourceSources, $metadata);
            // creditLine
            $creditLines = $dom->getElementsByTagName(RepositoryConst::LIDO_TAG_CREDIT_LINE);
            $this->setCreditLine($creditLines, $metadata);
            
            // required metadata
            $this->setRequireMetadataToArray($repositoryId, $metadata);
            
            return true;
        }
        catch(DOMException $dom_error)
        {
            return false;
        }
    }
    
    /**
     * Set lidoRecId metadata to array
     * lidoRecID
     * メタデータ変換
     *
     * @param DOMNodeList $lidoRecIds XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setLidoRecId(DOMNodeList $lidoRecIds, &$metadata)
    {
        // 各lidoRecIdタグごとに処理
        foreach($lidoRecIds as $lidoRecId)
        {
            // 属性IDを設定
            $attrId = AttrId::LIDO_REC_ID;
            // 値
            $value = $lidoRecId->nodeValue;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // メタデータ配列作成
            $this->setAttributeData($attrId, $value, $inputType, $metadata);
        }
    }
    
    /**
     * Set objectWorkType metadata to array
     * descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType
     * メタデータ変換
     *
     * @param DOMNodeList $objectWorkTypes XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setObjectWorkType(DOMNodeList $objectWorkTypes, &$metadata)
    {
        // 各objectWorkTypeタグごとに処理
        foreach($objectWorkTypes as $objectWorkType)
        {
            $count_conceptId = 0;
            $count_term = 0;
            // objectWorkTypeの子ごとに処理
            foreach($objectWorkType->childNodes as $childNode)
            {
                // objectWorkType.conceptID
                if($childNode->localName === RepositoryConst::LIDO_TAG_CONCEPT_ID)
                {
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_OBJECT_WORK_CONCEPT_ID;
                    // 値
                    $value = $childNode->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_conceptId++;
                }
                // objectWorkType.term
                else if($childNode->localName === RepositoryConst::LIDO_TAG_TERM)
                {
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_OBJECT_WORK_TERM;
                    // 値
                    $value = $childNode->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_term++;
                }
            }
            if(($count_conceptId + $count_term) === 1)
            {
                if($count_conceptId === 0)
                {
                    $attrId = AttrId::LIDO_OBJECT_WORK_CONCEPT_ID;
                    $this->cntBlankRequiredMetadata[AttrId::LIDO_OBJECT_WORK_CONCEPT_ID]++;
                }
                else if($count_term === 0)
                {
                    $attrId = AttrId::LIDO_OBJECT_WORK_TERM;
                    $this->cntBlankRequiredMetadata[AttrId::LIDO_OBJECT_WORK_TERM]++;
                }
                // 入力形式
                $inputType = self::INPUT_TYPE_TEXT;
                // 空白文字挿入
                $this->setBlankWord($attrId, $inputType, $metadata);
            }
        }
    }
    
    /**
     * Set classification metadata to array
     * descriptiveMetadata.objectClassificationWrap.classificationWrap.classification
     * メタデータ変換
     *
     * @param DOMNodeList $classifications XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setClassification(DOMNodeList $classifications, &$metadata)
    {
        // 各classificationタグごとに処理
        foreach($classifications as $classification)
        {
            $count_conceptId = 0;
            $count_term = 0;
            // classificationの子ごとに処理
            foreach($classification->childNodes as $childNode)
            {
                // classification.conceptID
                if($childNode->localName === RepositoryConst::LIDO_TAG_CONCEPT_ID)
                {
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_CLASSIFICATION_CONCEPT_ID;
                    // 値
                    $value = $childNode->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_conceptId++;
                }
                // classification.term
                else if($childNode->localName === RepositoryConst::LIDO_TAG_TERM)
                {
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_CLASSIFICATION_TERM;
                    // 値
                    $value = $childNode->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_term++;
                }
            }
            if(($count_conceptId + $count_term) === 1)
            {
                if($count_conceptId === 0)
                {
                    $attrId = AttrId::LIDO_CLASSIFICATION_CONCEPT_ID;
                }
                else if($count_term === 0)
                {
                    $attrId = AttrId::LIDO_CLASSIFICATION_TERM;
                }
                // 入力形式
                $inputType = self::INPUT_TYPE_TEXT;
                // 空白文字挿入
                $this->setBlankWord($attrId, $inputType, $metadata);
            }
        }
    }
    
    /**
     * Set titleSet metadata to array
     * descriptiveMetadata.objectIdentificationWrap.titleWrap.titleSet
     * メタデータ変換
     *
     * @param DOMNodeList $titleSets XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setTitleSet(DOMNodeList $titleSets, &$metadata)
    {
        // 各titleSetタグごとに処理
        foreach($titleSets as $titleSet)
        {
            // titleSetの子ごとに処理
            foreach($titleSet->childNodes as $childNode)
            {
                $title = $metadata[self::KEY_IR_BASIC][self::KEY_TITLE];
                $titleEn = $metadata[self::KEY_IR_BASIC][self::KEY_TITLE_EN];
                $attribute_lang = $childNode->getAttributeNS(RepositoryConst::LIDO_XML_NAMESPACE_URL, RepositoryConst::LIDO_ATTR_LANG);
                if($attribute_lang === RepositoryConst::ITEM_LANG_EN && strlen($titleEn) < 1)
                {
                    $metadata[self::KEY_IR_BASIC][self::KEY_TITLE_EN] = $childNode->nodeValue;
                }
                else if(strlen($title) < 1)
                {
                    $metadata[self::KEY_IR_BASIC][self::KEY_TITLE] = $childNode->nodeValue;
                }
                else
                {
                    // titleSet.appellationValue
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_TITLE_SET;
                    // 値
                    $value = $childNode->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                }
            }
        }
    }
    
    /**
     * Set inscriptionTranscription metadata to array
     * descriptiveMetadata.objectIdentificationWrap.inscriptionsWrap.inscriptions.inscriptionTranscription
     * メタデータ変換
     *
     * @param DOMNodeList $inscriptionTranscriptions XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setInscriptionTranscription(DOMNodeList $inscriptionTranscriptions, &$metadata)
    {
        // 各inscriptionTranscriptionタグごとに処理
        foreach($inscriptionTranscriptions as $inscriptionTranscription)
        {
            // inscriptionTranscription
            // 属性IDを設定
            $attrId = AttrId::LIDO_INSCRIPTION;
            // 値
            $value = $inscriptionTranscription->nodeValue;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // メタデータ配列作成
            $this->setAttributeData($attrId, $value, $inputType, $metadata);
        }
    }
    
    /**
     * Set repositorySet metadata to array
     * descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet
     * メタデータ変換
     *
     * @param DOMNodeList $repositorySets XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setRepositorySet(DOMNodeList $repositorySets, &$metadata)
    {
        // 各repositorySetタグごとに処理
        foreach($repositorySets as $repositorySet)
        {
            $count_repositoryName = 0;
            $count_workID = 0;
            // repositorySetの子ごとに処理
            foreach($repositorySet->childNodes as $childNode)
            {
                // repositorySet.repositoryName
                if($childNode->localName === RepositoryConst::LIDO_TAG_REPOSITORY_NAME)
                {
                    // repositorySet.repositoryName
                    $this->setRepositoryName($childNode, $metadata);
                    $count_repositoryName++;
                }
                // repositorySet.workID
                else if($childNode->localName === RepositoryConst::LIDO_TAG_WORK_ID)
                {
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_REPOSITORY_SET_WORK_ID;
                    // 値
                    $value = $childNode->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_workID++;
                }
            }
            if(($count_repositoryName + $count_workID) === 1)
            {
                if($count_repositoryName === 0)
                {
                    // repositorySet.repositoryName
                    // repositorySet.repositoryName.legalBodyName.appellationValue
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_REPOSITORY_NAME_LEGAL_BODY_NAME;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_NAME;
                    // 空白文字挿入
                    $this->setBlankWord($attrId, $inputType, $metadata);
                    
                    // repositorySet.repositoryName.legalBodyWeblink
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_REPOSITORY_NAME_LEGAL_BODY_WEB_LINK;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_LINK;
                    // 空白文字挿入
                    $this->setBlankWord($attrId, $inputType, $metadata);
                }
                else if($count_workID === 0)
                {
                    $attrId = AttrId::LIDO_REPOSITORY_SET_WORK_ID;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // 空白文字挿入
                    $this->setBlankWord($attrId, $inputType, $metadata);
                }
            }
        }
    }
    
    /**
     * Set repositoryName metadata to array
     * descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.repositoryName
     * メタデータ変換
     *
     * @param DOMNodeList $repositoryName XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setRepositoryName(DOMNode $repositoryName, &$metadata)
    {
        $count_legalBodyName = 0;
        $count_legalBodyWeblink = 0;
        // repositoryNameの子ごとに処理
        foreach($repositoryName->childNodes as $childNode)
        {
            // repositorySet.repositoryName.legalBodyName
            if($childNode->localName === RepositoryConst::LIDO_TAG_LEGAL_BODY_NAME)
            {
                foreach($childNode->childNodes as $appellationValue)
                {
                    // repositorySet.repositoryName.legalBodyName.appellationValue
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_REPOSITORY_NAME_LEGAL_BODY_NAME;
                    // 値
                    $value = $appellationValue->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_NAME;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_legalBodyName++;
                }
            }
            // repositorySet.repositoryName.legalBodyWeblink
            else if($childNode->localName === RepositoryConst::LIDO_TAG_LEGAL_BODY_WEB_LINK)
            {
                // 属性IDを設定
                $attrId = AttrId::LIDO_REPOSITORY_NAME_LEGAL_BODY_WEB_LINK;
                // 値
                $value = $childNode->nodeValue;
                // 入力形式
                $inputType = self::INPUT_TYPE_LINK;
                // メタデータ配列作成
                $this->setAttributeData($attrId, $value, $inputType, $metadata);
                $count_legalBodyWeblink++;
            }
        }
        if(($count_legalBodyName + $count_legalBodyWeblink) === 1)
        {
            if($count_legalBodyName === 0)
            {
                // 属性IDを設定
                $attrId = AttrId::LIDO_REPOSITORY_NAME_LEGAL_BODY_NAME;
                // 入力形式
                $inputType = self::INPUT_TYPE_NAME;
            }
            else if($count_legalBodyWeblink === 0)
            {
                // 属性IDを設定
                $attrId = AttrId::LIDO_REPOSITORY_NAME_LEGAL_BODY_WEB_LINK;
                // 入力形式
                $inputType = self::INPUT_TYPE_LINK;
            }
            // 空白文字挿入
            $this->setBlankWord($attrId, $inputType, $metadata);
        }
    }
    
    /**
     * Set displayState metadata to array
     * descriptiveMetadata.objectIdentificationWrap.displayStateEditionWrap.displayState
     * メタデータ変換
     *
     * @param DOMNodeList $displayStates XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setDisplayState(DOMNodeList $displayStates, &$metadata)
    {
        // 各displayStateタグごとに処理
        foreach($displayStates as $displayState)
        {
            // displayState
            // 属性IDを設定
            $attrId = AttrId::LIDO_DISPLAY_STATE;
            // 値
            $value = $displayState->nodeValue;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // メタデータ配列作成
            $this->setAttributeData($attrId, $value, $inputType, $metadata);
        }
    }
    
    /**
     * Set descriptiveNoteValue metadata to array
     * descriptiveMetadata.objectIdentificationWrap.objectDescriptionWrap.objectDescriptionSet.descriptiveNoteValue
     * メタデータ変換
     *
     * @param DOMNodeList $descriptiveNoteValues XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setDescriptiveNoteValue(DOMNodeList $descriptiveNoteValues, &$metadata)
    {
        // 各descriptiveNoteValueタグごとに処理
        foreach($descriptiveNoteValues as $descriptiveNoteValue)
        {
            // descriptiveNoteValue
            // 属性IDを設定
            $attrId = AttrId::LIDO_DESCRIPTIVE_NOTE_VALUE;
            // 値
            $value = $descriptiveNoteValue->nodeValue;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // メタデータ配列作成
            $this->setAttributeData($attrId, $value, $inputType, $metadata);
        }
    }
    
    /**
     * Set displayObjectMeasurements metadata to array
     * descriptiveMetadata.objectIdentificationWrap.objectMeasurementsWrap.objectMeasurementsSet.displayObjectMeasurements
     * メタデータ変換
     *
     * @param DOMNodeList $displayObjectMeasurementses XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setDisplayObjectMeasurements(DOMNodeList $displayObjectMeasurementses, &$metadata)
    {
        // 各displayObjectMeasurementsタグごとに処理
        foreach($displayObjectMeasurementses as $displayObjectMeasurements)
        {
            // displayObjectMeasurements
            // 属性IDを設定
            $attrId = AttrId::LIDO_DESPLAY_OBJECT_MEASUREMENTS;
            // 値
            $value = $displayObjectMeasurements->nodeValue;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // メタデータ配列作成
            $this->setAttributeData($attrId, $value, $inputType, $metadata);
        }
    }
    
    /**
     * Set eventSet metadata to array
     * descriptiveMetadata.eventWrap.eventSet
     * メタデータ変換
     *
     * @param DOMNodeList $eventSets XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setEventSet(DOMNodeList $eventSets, &$metadata)
    {
        // 各eventSetタグごとに処理
        foreach($eventSets as $eventSet)
        {
            $count_event = 0;
            $count_displayEvent = 0;
            // eventSetの子ごとに処理
            foreach($eventSet->childNodes as $childNode)
            {
                // eventSet.event
                if($childNode->localName === RepositoryConst::LIDO_TAG_EVENT)
                {
                    // eventSet.event
                    $this->setEvent($childNode, $metadata);
                    $count_event++;
                }
                // eventSet.displayEvent
                else if($childNode->localName === RepositoryConst::LIDO_TAG_DISPLAY_EVENT)
                {
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_DISPLAY_EVENT;
                    // 値
                    $value = $childNode->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXTAREA;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_displayEvent++;
                }
            }
            if(($count_event + $count_displayEvent) === 1)
            {
                if($count_event === 0)
                {
                    $this->setBlankForEvent($metadata);
                }
                else if($count_displayEvent === 0)
                {
                    $attrId = AttrId::LIDO_DISPLAY_EVENT;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXTAREA;
                    // 空白文字挿入
                    $this->setBlankWord($attrId, $inputType, $metadata);
                }
            }
        }
    }
    
    /**
     * Set event metadata to array
     * descriptiveMetadata.eventWrap.eventSet.eventWrap.eventSet.event
     * メタデータ変換
     *
     * @param DOMNodeList $event XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setEvent(DOMNode $event, &$metadata)
    {
        $count_eventType = 0;
        $count_eventActor = 0;
        $count_eventDate = 0;
        $count_periodName = 0;
        $count_eventPlace = 0;
        $count_eventMaterialsTech = 0;
        // eventの子ごとに処理
        foreach($event->childNodes as $childNode)
        {
            // event.eventType
            if($childNode->localName === RepositoryConst::LIDO_TAG_EVENT_TYPE)
            {
                foreach($childNode->childNodes as $term)
                {
                    // event.eventType.term
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_EVENT_TYPE;
                    // 値
                    $value = $term->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_eventType++;
                }
            }
            // event.eventActor
            else if($childNode->localName === RepositoryConst::LIDO_TAG_EVENT_ACTOR)
            {
                foreach($childNode->childNodes as $displayActorInRole)
                {
                    // event.eventActor.displayActorInRole
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_EVENT_ACTOR;
                    // 値
                    $value = $displayActorInRole->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_eventActor++;
                }
            }
            // event.eventDate
            else if($childNode->localName === RepositoryConst::LIDO_TAG_EVENT_DATE)
            {
                $this->setEventDate($childNode, $metadata);
                $count_eventDate++;
            }
            // event.periodName
            else if($childNode->localName === RepositoryConst::LIDO_TAG_PERIOD_NAME)
            {
                foreach($childNode->childNodes as $term)
                {
                    // event.periodName.term
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_PERIOD_NAME;
                    // 値
                    $value = $term->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_periodName++;
                }
            }
            // event.eventPlace
            else if($childNode->localName === RepositoryConst::LIDO_TAG_EVENT_PLACE)
            {
                $this->setEventPlace($childNode, $metadata);
                $count_eventPlace++;
            }
            // event.eventMaterialsTech
            else if($childNode->localName === RepositoryConst::LIDO_TAG_EVENT_MATERIALS_TECH)
            {
                foreach($childNode->childNodes as $displayMaterialsTech)
                {
                    // event.eventMaterialsTech.displayMaterialsTech
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_EVENT_MATERIALS_TECH;
                    // 値
                    $value = $displayMaterialsTech->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_TEXT;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_eventMaterialsTech++;
                }
            }
        }
        if($count_eventType === 0)
        {
            // 属性IDを設定
            $attrId = AttrId::LIDO_EVENT_TYPE;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // 空白文字挿入
            $this->setBlankWord($attrId, $inputType, $metadata);
        }
        if($count_eventActor === 0)
        {
            // 属性IDを設定
            $attrId = AttrId::LIDO_EVENT_ACTOR;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // 空白文字挿入
            $this->setBlankWord($attrId, $inputType, $metadata);
        }
        if($count_eventDate === 0)
        {
            $this->setBlankForEventDate($metadata);
        }
        if($count_periodName === 0)
        {
            // 属性IDを設定
            $attrId = AttrId::LIDO_PERIOD_NAME;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // 空白文字挿入
            $this->setBlankWord($attrId, $inputType, $metadata);
        }
        if($count_eventPlace === 0)
        {
            $this->setBlankForEventPlace($metadata);
        }
        if($count_eventMaterialsTech === 0)
        {
            // 属性IDを設定
            $attrId = AttrId::LIDO_EVENT_MATERIALS_TECH;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // 空白文字挿入
            $this->setBlankWord($attrId, $inputType, $metadata);
        }
    }
    
    /**
     * set blank word
     * 空文字設定
     *
     * @param int $attrId Attribute id 属性ID
     * @param string $inputType Input type 入力タイプ
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setBlankWord($attrId, $inputType, &$metadata)
    {
        $value = RepositoryConst::BLANK_WORD;
        $this->setAttributeData($attrId, $value, $inputType, $metadata);
    }
    
    /**
     * Set blank word for event metadata to array
     * descriptiveMetadata.eventWrap.eventSet.eventWrap.eventSet.event
     * 空文字設定
     *
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setBlankForEvent(&$metadata)
    {
        // event.eventType.term
        // 属性IDを設定
        $attrId = AttrId::LIDO_EVENT_TYPE;
        // 入力形式
        $inputType = self::INPUT_TYPE_TEXT;
        // 空白文字挿入
        $this->setBlankWord($attrId, $inputType, $metadata);
        
        // event.eventActor.displayActorInRole
        // 属性IDを設定
        $attrId = AttrId::LIDO_EVENT_ACTOR;
        // 入力形式
        $inputType = self::INPUT_TYPE_TEXT;
        // 空白文字挿入
        $this->setBlankWord($attrId, $inputType, $metadata);
        
        // event.eventDate
        $this->setBlankForEventDate($metadata);
        
        // event.periodName.term
        // 属性IDを設定
        $attrId = AttrId::LIDO_PERIOD_NAME;
        // 入力形式
        $inputType = self::INPUT_TYPE_TEXT;
        // 空白文字挿入
        $this->setBlankWord($attrId, $inputType, $metadata);
        
        // event.eventPlace
        $this->setBlankForEventPlace($metadata);
            
        // event.eventMaterialsTech.displayMaterialsTech
        // 属性IDを設定
        $attrId = AttrId::LIDO_EVENT_MATERIALS_TECH;
        // 入力形式
        $inputType = self::INPUT_TYPE_TEXT;
        // 空白文字挿入
        $this->setBlankWord($attrId, $inputType, $metadata);
    }
    
    /**
     * Set Blank for eventDate metadata to array
     * descriptiveMetadata.event.eventDate
     * 空文字設定
     *
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setBlankForEventDate(&$metadata)
    {
        // event.eventDate.date.earliestDate
        // 属性IDを設定
        $attrId = AttrId::LIDO_EVENT_DATE_EARLIEST;
        // 入力形式
        $inputType = self::INPUT_TYPE_DATE;
        // 空白文字挿入
        $this->setBlankWord($attrId, $inputType, $metadata);
        
        // event.eventDate.date.latestDate
        // 属性IDを設定
        $attrId = AttrId::LIDO_EVENT_DATE_LATEST;
        // 入力形式
        $inputType = self::INPUT_TYPE_DATE;
        // 空白文字挿入
        $this->setBlankWord($attrId, $inputType, $metadata);
        
        // 属性IDを設定
        $attrId = AttrId::LIDO_EVENT_DATE_DISPLAY_DATE;
        // 入力形式
        $inputType = self::INPUT_TYPE_TEXT;
        // 空白文字挿入
        $this->setBlankWord($attrId, $inputType, $metadata);
    }
    
    /**
     * Set blank for eventPlace metadata to array
     * descriptiveMetadata.event.eventPlace
     * 空文字設定
     *
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setBlankForEventPlace(&$metadata)
    {
        // event.eventPlace.place
        // 属性IDを設定
        $attrId = AttrId::LIDO_EVENT_PLACE_GML;
        // 入力形式
        $inputType = self::INPUT_TYPE_TEXT;
        // 空白文字挿入
        $this->setBlankWord($attrId, $inputType, $metadata);
        
        // 属性IDを設定
        $attrId = AttrId::LIDO_EVENT_DISPLAY_PLACE;
        // 入力形式
        $inputType = self::INPUT_TYPE_TEXT;
        // 空白文字挿入
        $this->setBlankWord($attrId, $inputType, $metadata);
    }
    
    /**
     * Set eventDate metadata to array
     * descriptiveMetadata.event.eventDate
     * メタデータ変換
     *
     * @param DOMNodeList $eventDate XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setEventDate(DOMNode $eventDate, &$metadata)
    {
        $count_earliestDate = 0;
        $count_latestDate = 0;
        $count_displayDate = 0;
        // eventDateの子ごとに処理
        foreach($eventDate->childNodes as $childNode)
        {
            // event.eventDate.date
            if($childNode->localName === RepositoryConst::LIDO_TAG_DATE)
            {
                foreach($childNode->childNodes as $dateNode)
                {
                    // event.eventDate.date.earliestDate
                    if($dateNode->localName === RepositoryConst::LIDO_TAG_EARLIEST_DATE)
                    {
                        // event.eventDate.date.earliestDate
                        // 属性IDを設定
                        $attrId = AttrId::LIDO_EVENT_DATE_EARLIEST;
                        // 値
                        $value = $dateNode->nodeValue;
                        // 入力形式
                        $inputType = self::INPUT_TYPE_DATE;
                        // メタデータ配列作成
                        $this->setAttributeData($attrId, $value, $inputType, $metadata);
                        $count_earliestDate++;
                    }
                    // event.eventDate.date.latestDate  
                    else if($dateNode->localName === RepositoryConst::LIDO_TAG_LATEST_DATE)
                    {
                        // event.eventDate.date.latestDate
                        // 属性IDを設定
                        $attrId = AttrId::LIDO_EVENT_DATE_LATEST;
                        // 値
                        $value = $dateNode->nodeValue;
                        // 入力形式
                        $inputType = self::INPUT_TYPE_DATE;
                        // メタデータ配列作成
                        $this->setAttributeData($attrId, $value, $inputType, $metadata);
                        $count_latestDate++;
                    }
                }
            }
            // event.eventDate.displayDate
            else if($childNode->localName === RepositoryConst::LIDO_TAG_DISPLAY_DATE)
            {
                // 属性IDを設定
                $attrId = AttrId::LIDO_EVENT_DATE_DISPLAY_DATE;
                // 値
                $value = $childNode->nodeValue;
                // 入力形式
                $inputType = self::INPUT_TYPE_TEXT;
                // メタデータ配列作成
                $this->setAttributeData($attrId, $value, $inputType, $metadata);
                $count_displayDate++;
            }
        }
        if($count_earliestDate === 0)
        {
            // 属性IDを設定
            $attrId = AttrId::LIDO_EVENT_DATE_EARLIEST;
            // 入力形式
            $inputType = self::INPUT_TYPE_DATE;
            // 空白文字挿入
            $this->setBlankWord($attrId, $inputType, $metadata);
        }
        if($count_latestDate === 0)
        {
            // 属性IDを設定
            $attrId = AttrId::LIDO_EVENT_DATE_LATEST;
            // 入力形式
            $inputType = self::INPUT_TYPE_DATE;
            // 空白文字挿入
            $this->setBlankWord($attrId, $inputType, $metadata);
        }
        if($count_displayDate === 0)
        {
            // 属性IDを設定
            $attrId = AttrId::LIDO_EVENT_DATE_DISPLAY_DATE;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // 空白文字挿入
            $this->setBlankWord($attrId, $inputType, $metadata);
        }
    }
    
    /**
     * Set eventPlace metadata to array
     * descriptiveMetadata.event.eventPlace
     * メタデータ変換
     *
     * @param DOMNodeList $eventPlace XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setEventPlace(DOMNode $eventPlace, &$metadata)
    {
        $count_gml = 0;
        $count_displayPlace = 0;
        // eventPlaceの子ごとに処理
        foreach($eventPlace->childNodes as $childNode)
        {
            // event.eventPlace.place
            if($childNode->localName === RepositoryConst::LIDO_TAG_PLACE)
            {
                foreach($childNode->childNodes as $gml)
                {
                    // event.eventPlace.place.gml
                    // <gml:Point>
                    $points = $gml->getElementsByTagName(RepositoryConst::GML_TAG_POS);
                    foreach($points as $point)
                    {
                        // 属性IDを設定
                        $attrId = AttrId::LIDO_EVENT_PLACE_GML;
                        // 値
                        $value = $point->nodeValue;
                        // 入力形式
                        $inputType = self::INPUT_TYPE_TEXT;
                        // メタデータ配列作成
                        $this->setAttributeData($attrId, $value, $inputType, $metadata);
                        $count_gml++;
                    }
                    
                    // <gml:Polygon>
                    $polygons = $gml->getElementsbyTagName(RepositoryConst::GML_TAG_COORDINATES);
                    foreach($polygons as $polygon)
                    {
                        // 属性IDを設定
                        $attrId = AttrId::LIDO_EVENT_PLACE_GML;
                        // 入力形式
                        $inputType = self::INPUT_TYPE_TEXT;
                        // 値
                        $polygon_value = $polygon->nodeValue;
                        $polygon_value = str_replace(RepositoryConst::XML_LF, "\n", $polygon_value);
                        $values = explode("\n", $polygon_value);
                        foreach($values as $value)
                        {
                            // メタデータ配列作成
                            $this->setAttributeData($attrId, $value, $inputType, $metadata);
                            $count_gml++;
                        }
                    }
                }
            }
            // event.eventPlace.displayPlace
            else if($childNode->localName === RepositoryConst::LIDO_TAG_DISPLAY_PLACE)
            {
                // 属性IDを設定
                $attrId = AttrId::LIDO_EVENT_DISPLAY_PLACE;
                // 値
                $value = $childNode->nodeValue;
                // 入力形式
                $inputType = self::INPUT_TYPE_TEXT;
                // メタデータ配列作成
                $this->setAttributeData($attrId, $value, $inputType, $metadata);
                $count_displayPlace++;
            }
        }
        if($count_displayPlace === 0)
        {
            // 属性IDを設定
            $attrId = AttrId::LIDO_EVENT_DISPLAY_PLACE;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // 空白文字挿入
            $this->setBlankWord($attrId, $inputType, $metadata);
        }
    }
    
    /**
     * Set displaySubject metadata to array
     * descriptiveMetadata.objectRelationWrap.subjectWrap.subjectSet.displaySubject
     * メタデータ変換
     *
     * @param DOMNodeList $displaySubjects XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setDisplaySubject(DOMNodeList $displaySubjects, &$metadata)
    {
        // 各displaySubjectタグごとに処理
        foreach($displaySubjects as $displaySubject)
        {
            $keyword = $metadata[self::KEY_IR_BASIC][self::KEY_SEARCH_KEY];
            $keywordEn = $metadata[self::KEY_IR_BASIC][self::KEY_SEARCH_KEY_EN];
            $attribute_lang = $displaySubject->getAttributeNS(RepositoryConst::LIDO_XML_NAMESPACE_URL, RepositoryConst::LIDO_ATTR_LANG);
            if($attribute_lang === RepositoryConst::ITEM_LANG_EN && strlen($keywordEn) < 1)
            {
                $metadata[self::KEY_IR_BASIC][self::KEY_SEARCH_KEY_EN] = $displaySubject->nodeValue;
            }
            else if($attribute_lang === RepositoryConst::ITEM_LANG_JA && strlen($keyword) < 1)
            {
                $metadata[self::KEY_IR_BASIC][self::KEY_SEARCH_KEY] = $displaySubject->nodeValue;
            }
            else
            {
                // displaySubject
                // 属性IDを設定
                $attrId = AttrId::LIDO_DISPLAY_SUBJECT;
                // 値
                $value = $displaySubject->nodeValue;
                // 入力形式
                $inputType = self::INPUT_TYPE_TEXT;
                // メタデータ配列作成
                $this->setAttributeData($attrId, $value, $inputType, $metadata);
            }
        }
    }
    
    /**
     * Set displayObject metadata to array
     * descriptiveMetadata.objectRelationWrap.relatedWorksWrap.relatedWorkSet.relatedWork.displayObject
     * メタデータ変換
     *
     * @param DOMNodeList $displayObjects XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setDisplayObject(DOMNodeList $displayObjects, &$metadata)
    {
        // 各displayObjectタグごとに処理
        foreach($displayObjects as $displayObject)
        {
            // displayObject
            // 属性IDを設定
            $attrId = AttrId::LIDO_RELATED_WORK;
            // 値
            $value = $displayObject->nodeValue;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // メタデータ配列作成
            $this->setAttributeData($attrId, $value, $inputType, $metadata);
        }
    }
    
    /**
     * Set recordID metadata to array
     * administrativeMetadata.recordWrap.recordID
     * メタデータ変換
     *
     * @param DOMNodeList $recordIDs XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setRecordID(DOMNodeList $recordIDs, &$metadata)
    {
        // 各recordIDタグごとに処理
        foreach($recordIDs as $recordID)
        {
            // recordID
            // 属性IDを設定
            $attrId = AttrId::LIDO_RECORD_ID;
            // 値
            $value = $recordID->nodeValue;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // メタデータ配列作成
            $this->setAttributeData($attrId, $value, $inputType, $metadata);
        }
    }
    
    /**
     * Set recordType metadata to array
     * administrativeMetadata.recordWrap.recordType
     * メタデータ変換
     *
     * @param DOMNodeList $recordTypes XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setRecordType(DOMNodeList $recordTypes, &$metadata)
    {
        // 各recordTypeタグごとに処理
        foreach($recordTypes as $recordType)
        {
            // recordTypeの子ごとに処理
            foreach($recordType->childNodes as $childNode)
            {
                // recordType.term
                // 属性IDを設定
                $attrId = AttrId::LIDO_RECORD_TYPE;
                // 値
                $value = $childNode->nodeValue;
                // 入力形式
                $inputType = self::INPUT_TYPE_TEXT;
                // メタデータ配列作成
                $this->setAttributeData($attrId, $value, $inputType, $metadata);
            }
        }
    }
    
    /**
     * Set recordSource metadata to array
     * administrativeMetadata.recordWrap.recordSource
     * メタデータ変換
     *
     * @param DOMNodeList $recordSources XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setRecordSource(DOMNodeList $recordSources, &$metadata)
    {
        // 各recordTypeタグごとに処理
        foreach($recordSources as $recordSource)
        {
            // recordTypeの子ごとに処理
            foreach($recordSource->childNodes as $legalBodyNames)
            {
                // legalBodyNameの子ごとに処理
                foreach($legalBodyNames->childNodes as $appellationValue)
                {
                    // recordSource.legalBodyName.appellationValue
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_RECORD_SOURCE;
                    // 値
                    $value = $appellationValue->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_NAME;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                }
            }
        }
    }
    
    /**
     * Set recordInfoSet metadata to array
     * administrativeMetadata.recordWrap.recordInfoSet
     * メタデータ変換
     *
     * @param DOMNodeList $recordInfoSets XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setRecordInfoSet(DOMNodeList $recordInfoSets, &$metadata)
    {
        // 各recordInfoSetタグごとに処理
        foreach($recordInfoSets as $recordInfoSet)
        {
            $count_recordInfoLink = 0;
            $count_recordMetadataDate = 0;
            // recordInfoSetの子ごとに処理
            foreach($recordInfoSet->childNodes as $childNode)
            {
                // recordInfoSet.recordInfoLink
                if($childNode->localName === RepositoryConst::LIDO_TAG_RECORD_INFO_LINK)
                {
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_RECORD_INFO_LINK;
                    // 値
                    $value = $childNode->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_LINK;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_recordInfoLink++;
                }
                // recordInfoSet.recordMetadataDate
                else if($childNode->localName === RepositoryConst::LIDO_TAG_RECORD_METADATA_DATE)
                {
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_RECORD_METADATA_DATE;
                    // 値
                    $value = $childNode->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_DATE;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                    $count_recordMetadataDate++;
                }
            }
            if(($count_recordInfoLink + $count_recordMetadataDate) === 1)
            {
                if($count_recordInfoLink === 0)
                {
                    $attrId = AttrId::LIDO_RECORD_INFO_LINK;
                    $inputType = self::INPUT_TYPE_LINK;
                }
                else if($count_recordMetadataDate === 0)
                {
                    $attrId = AttrId::LIDO_RECORD_METADATA_DATE;
                    $inputType = self::INPUT_TYPE_DATE;
                }
                // 空白文字挿入
                $this->setBlankWord($attrId, $inputType, $metadata);
            }
        }
    }
    
    /**
     * Set resourceRepresentation metadata to array
     * administrativeMetadata.resourceWrap.resourceSet.resourceRepresentation
     * メタデータ変換
     *
     * @param DOMNodeList $resourceRepresentations XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setResourceRepresentation(DOMNodeList $resourceRepresentations, &$metadata)
    {
        // 各resourceRepresentationタグごとに処理
        foreach($resourceRepresentations as $resourceRepresentation)
        {
            // resourceRepresentationの子ごとに処理
            foreach($resourceRepresentation->childNodes as $childNode)
            {
                // resourceRepresentation
                // 属性IDを設定
                $attrId = AttrId::LIDO_RESOURCE_REPRESENTATION;
                // 値
                $value = $childNode->nodeValue;
                // 入力形式
                $inputType = self::INPUT_TYPE_TEXT;
                // メタデータ配列作成
                $this->setAttributeData($attrId, $value, $inputType, $metadata);
            }
        }
    }
    
    /**
     * Set resourceDescription metadata to array
     * administrativeMetadata.resourceWrap.resourceSet.resourceDescription
     * メタデータ変換
     *
     * @param DOMNodeList $resourceDescriptions XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setResourceDescription(DOMNodeList $resourceDescriptions, &$metadata)
    {
        // 各resourceDescriptionタグごとに処理
        foreach($resourceDescriptions as $resourceDescription)
        {
            // resourceDescription
            // 属性IDを設定
            $attrId = AttrId::LIDO_RESOURCE_DESCRIPTION;
            // 値
            $value = $resourceDescription->nodeValue;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // メタデータ配列作成
            $this->setAttributeData($attrId, $value, $inputType, $metadata);
        }
    }
    
    /**
     * Set resourceSource metadata to array
     * administrativeMetadata.resourceWrap.resourceSet.resourceSource
     * メタデータ変換
     *
     * @param DOMNodeList $resourceSources XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setResourceSource(DOMNodeList $resourceSources, &$metadata)
    {
        // 各resourceSourceタグごとに処理
        foreach($resourceSources as $resourceSource)
        {
            // resourceSourceの子ごとに処理
            foreach($resourceSource->childNodes as $legalBodyName)
            {
                // resourceSource.legalBodyNameの子ごとに処理
                foreach($legalBodyName->childNodes as $childNode)
                {
                    // resourceSource.legalBodyName.appellationValue
                    // 属性IDを設定
                    $attrId = AttrId::LIDO_RESOURCE_SOURCE;
                    // 値
                    $value = $childNode->nodeValue;
                    // 入力形式
                    $inputType = self::INPUT_TYPE_NAME;
                    // メタデータ配列作成
                    $this->setAttributeData($attrId, $value, $inputType, $metadata);
                }
            }
        }
    }
    
    /**
     * Set creditLine metadata to array
     * administrativeMetadata.resourceWrap.resourceSet.rightsResource.creditLine
     * メタデータ変換
     *
     * @param DOMNodeList $creditLines XML Node list XMLNode一覧
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setCreditLine(DOMNodeList $creditLines, &$metadata)
    {
        // 各creditLineタグごとに処理
        foreach($creditLines as $creditLine)
        {
            // creditLine
            // 属性IDを設定
            $attrId = AttrId::LIDO_RIGHTS_RESOURCE;
            // 値
            $value = $creditLine->nodeValue;
            // 入力形式
            $inputType = self::INPUT_TYPE_TEXT;
            // メタデータ配列作成
            $this->setAttributeData($attrId, $value, $inputType, $metadata);
        }
    }
    
    /**
     * Set attribute data for ItemRegister
     * ItemRegister用のメタデータを作成し、メタデータ配列に追加する
     *
     * @param int $attrId Attribute id 属性ID
     * @param string $value Value 値
     * @param string $inputType Input type 入力タイプ
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     */
    private function setAttributeData($attrId, $value, $inputType, &$metadata)
    {
        // XMLデコード
        $string = $this->forXmlChangeDecode($value);
        // データが存在すれば
        if(strlen($string) > 0)
        {
            if($inputType === self::INPUT_TYPE_NAME)
            {
                // 値をデータに詰める
                $data = $this->explodeNameStr($string);
            }
            else
            {
                // 値をデータに詰める
                $data = array(self::KEY_ATTR_VALUE => $string);
            }
            // 同一属性IDのメタデータの数をカウント
            // この値はattribute_noの値となるので、このインクリメントの処理は
            // createIrMetadataの前になければならない
            $this->cntMetadata[$attrId]++;
            // ItemRegister用のメタデータを作成
            $irMetadata = $this->createIrMetadata($attrId, $inputType, $data);
            if(count($irMetadata)>0)
            {
                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
            }
        }
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
        
        // LIDO
        if($this->cntMetadata[AttrId::LIDO_REC_ID] === 0 ||
           ($this->cntMetadata[AttrId::LIDO_OBJECT_WORK_CONCEPT_ID] - $this->cntBlankRequiredMetadata[AttrId::LIDO_OBJECT_WORK_CONCEPT_ID]) <= 0 ||
           ($this->cntMetadata[AttrId::LIDO_OBJECT_WORK_TERM] - $this->cntBlankRequiredMetadata[AttrId::LIDO_OBJECT_WORK_TERM]) <= 0 ||
           $this->cntMetadata[AttrId::LIDO_RECORD_ID] === 0 ||
           $this->cntMetadata[AttrId::LIDO_RECORD_TYPE] === 0 ||
           $this->cntMetadata[AttrId::LIDO_RECORD_SOURCE] === 0 )
        {
            array_push($logMsg, self::MSG_ER_GET_LIDO_REQUIRED_DATA);
            $logStatus = self::LOG_STATUS_ERROR;
            return false;
        }
        
        return true;
    }
    
    /**
     * delete tag that is not used
     * 利用しないタグの消去
     *
     * @param string $metadataXML Metadata XML メタデータXML
     */
    private function deleteTagNotUsed(&$metadataXml)
    {
        $metadataXml = str_replace(">\r\n<", "><", $metadataXml);
        $metadataXml = str_replace(">\n<", "><", $metadataXml);
        $pattern = '/'.self::LIDO_TAG_NOT_USED_CATEGORY_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_CATEGORY_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_REPOSITORY_LOCATION_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_REPOSITORY_LOCATION_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_RELATED_WORK_REL_TYPE_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_RELATED_WORK_REL_TYPE_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_RELATED_WORK_OBJECT_ONE_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_RELATED_WORK_OBJECT_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_RELATED_WORK_OBJECT_TWO_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_RELATED_WORK_OBJECT_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_RESOURCE_ID_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_RESOURCE_ID_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_RESOURCE_TYPE_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_RESOURCE_TYPE_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_RESOURCE_REL_TYPE_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_RESOURCE_REL_TYPE_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_RESOURCE_PERSPECTIVE_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_RESOURCE_PERSPECTIVE_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_RESOURCE_DATE_TAKEN_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_RESOURCE_DATE_TAKEN_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_ACTOR_IN_ROLE_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_ACTOR_IN_ROLE_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_TERM_MATERIALS_TECH_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_TERM_MATERIALS_TECH_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_NAME_PLACE_SET_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_NAME_PLACE_SET_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_PART_OF_PLACE_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_PART_OF_PLACE_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_MATERIAL_TECH_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_MATERIAL_TECH_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_SUBJECT_ONE_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_SUBJECT_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_SUBJECT_TWO_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_SUBJECT_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_RIGHTS_WORK_WRAP_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_RIGHTS_WORK_WRAP_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_RECORD_RIGHTS_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_RECORD_RIGHTS_END.self::OR_STRING.
                   self::LIDO_TAG_NOT_USED_OBJECT_PUBLISHED_ID_START.self::NOT_USED_CONTENT.self::LIDO_TAG_NOT_USED_OBJECT_PUBLISHED_ID_END.'/';
        $metadataXml = preg_replace($pattern, "", $metadataXml);
    }
}

?>