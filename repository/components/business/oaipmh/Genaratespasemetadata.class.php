<?php

/**
 * Common classes for creating the time of OAI-PMH output, meta-data section (different locations by metadataPrefix) in Spase format
 * OAI-PMH出力時、メタデータ部(metadataPrefixによって異なる箇所)をSpase形式で作成するための共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Genaratespasemetadata.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';
/**
 * Structure class summarizes the metadata name and metadata value for each item in the specified mapping format
 * 指定したマッピング形式でアイテム毎にメタデータ名とメタデータ値をまとめた構造体クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/oaipmh/ItemStruct.class.php';
/**
 * Structure class summarizes metadata name and metadata value of the specified mapping format, the attribute
 * 指定したマッピング形式のメタデータ名とメタデータ値、属性をまとめた構造体クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/oaipmh/MetadataStruct.class.php';
/**
 * Structure class with a string attached attribute name and attribute value to the tag of the specified mapping format
 * 指定したマッピング形式のタグに紐付く属性名と属性値を持つ構造体クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/oaipmh/AttributeStruct.class.php';
/**
 * Constant class that defines the constants necessary to Oaipmh output in Spase format
 * Spase形式でのOaipmh出力に必要な定数を定義した定数クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/oaipmh/SpaseConst.class.php';
/**
 * Structure class of bibliographic information
 * 書誌情報の構造体クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/oaipmh/BiblioInfoStruct.class.php';
/**
 * Operate the file system
 * ファイルシステムの操作を行う
 */
require_once WEBAPP_DIR. '/modules/repository/components/util/OperateFileSystem.class.php';
/**
 * Expanded exception class for OAI-PMH output
 * OAI-PMH出力用拡張例外クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/oaipmh/OaipmhException.class.php';

/**
 * Common classes for creating the time of OAI-PMH output, meta-data section (different locations by metadataPrefix) in Spase format
 * OAI-PMH出力時、メタデータ部(metadataPrefixによって異なる箇所)をSpase形式で作成するための共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Oaipmh_Genaratespasemetadata extends BusinessBase 
{
    /**
     * To generate a meta data portion of Spase format from the passed item information
     * 渡されたアイテム情報からSpase形式のメタデータ部を作成する
     *
     * @param OaipmhItem $oaipmhItem Information of items to be converted to Space format Spase形式に変換するアイテムの情報
     * @return string XML of the items that were converted into Space format Spase形式に変換したアイテムのメタデータ部
     */
    public function generateMetadataByOaipmhItem($oaipmhItem){
        $this->debugLog("[". __FUNCTION__. "] start.", __FILE__, __CLASS__, __LINE__);
        
        try {
            $xml = $this->writeXml($oaipmhItem->metadataList["Spase"]->metadataValue);
        } catch(OaipmhException $ex){
            return null;
        }
        
        $this->debugLog("[". __FUNCTION__. "] finish. xml:". $xml, __FILE__, __CLASS__, __LINE__);
        return $xml;
    }
    
    /**
     * To create the XML of Space format
     * Spase形式のXMLを作成する
     *
     * @param MetadataStruct $oaipmhMetadataList List of metadata registered in the item アイテムに登録されているメタデータの一覧
     * @return string XML string of Space format Spase形式のXML文字列
     */
    private function writeXml($oaipmhMetadataList){
        $xml = "";
        
        // SpaseのXMLを作成する
        $xml = "<Spase ". 
               SpaseConst::ROOT_TAG_ATTRIBUTE_NAME_LANG. "=\"". SpaseConst::ROOT_TAG_ATTRIBUTE_VALUE_LANG_DEFAULT. "\" ".
               SpaseConst::ROOT_TAG_ATTRIBUTE_NAME_XMLNS_XSI. "=\"". SpaseConst::INSTANCE. "\" ".
               SpaseConst::ROOT_TAG_ATTRIBUTE_NAME_XMLNS. "=\"". SpaseConst::NAME_SPACE. "\" ".
               SpaseConst::ROOT_TAG_ATTRIBUTE_NAME_SCHEMA_LOCATION. "=\"". SpaseConst::SCHEMA_LOCATION. "\" ".
               ">";
        
        // Spaseタグ直下のタグを作成する
        $xml .= $this->writeVersionTag();
        
        $xml .= $this->writeAnnotationTag($oaipmhMetadataList);
        $xml .= $this->writeCatalogTag($oaipmhMetadataList);
        $xml .= $this->writeDisplayDataTag($oaipmhMetadataList);
        $xml .= $this->writeDocumentTag($oaipmhMetadataList);
        $xml .= $this->writeGranuleTag($oaipmhMetadataList);
        $xml .= $this->writeInstrumentTag($oaipmhMetadataList);
        $xml .= $this->writeNumericalDataTag($oaipmhMetadataList);
        $xml .= $this->writeObservatoryTag($oaipmhMetadataList);
        $xml .= $this->writePersonTag($oaipmhMetadataList);
        $xml .= $this->writeRegistryTag($oaipmhMetadataList);
        $xml .= $this->writeRepositoryTag($oaipmhMetadataList);
        $xml .= $this->writeServiceTag($oaipmhMetadataList);
        
        $xml .= "</Spase>";
        
        return $xml;
    }
    
    /**
     * To get the meta data of the specified tag
     * 指定されたタグのメタデータを取得する
     *
     * @param string $tagName Tag name タグ名
     * @param array $metadataList List of metadata registered in the item アイテムに登録されているメタデータの一覧
     * @return array Metadata List メタデータ一覧
     */
    private function popMetadataByTag($tagName, $oaipmhMetadataList){
        // タグ名のリストを定義
        $list = explode(".", $tagName);
        
        // 上位のタグ名を取得
        $upperTagName = $list[0];
        
        // 下位のタグ名の一覧を取得
        $tagList = array_slice($list, 1);
        
        $metadataList = $this->recursivePopMetadata($oaipmhMetadataList, $upperTagName, $tagList);
        
        return $metadataList;
    }
    
    /**
     * Recursive processing. To get the meta data of the specified tag
     * 再帰処理。指定されたタグのメタデータを取得する
     *
     * @param array $metadataList List of metadata registered in the item アイテムに登録されているメタデータの一覧
     * @param string $tagName Tag name タグ名
     * @param array $tagList Tag list タグの一覧
     * @return array Metadata List メタデータ一覧
     */
    private function recursivePopMetadata($metadataList, $tagName, $tagList){
        if(isset($metadataList[$tagName])){
            // 最下層
            if(count($tagList) === 0){
                if(count($metadataList[$tagName]->metadataValue) === 0){
                    return array();
                } else {
                    return $metadataList[$tagName]->metadataValue;
                }
            }
            
            // 最下層ではない
            $subTagName = $tagList[0];
            $subTagList = array_slice($tagList, 1);
            return $this->recursivePopMetadata($metadataList[$tagName]->metadataValue, $subTagName, $subTagList);
        } else {
            return array();
        }
    }
    
    /**
     * Required tag, check the maximum entry, to convert the metadata to XML
     * タグの必須、最大項目を確認し、メタデータをXMLに変換する
     *
     * @param string $tagName Tag name タグ名
     * @param int $min Tags MinOccurs タグのMinOccurs
     * @param int $max Tags MaxOccurs タグのMaxOccurs
     * @param array $oaipmhMetadataList List of metadata registered in the item アイテムに登録されているメタデータの一覧
     * @return string String obtained by converting the list metadata XML メタデータの一覧をXMLに変換した文字列
     */
    private function writeTag($tagName, $min, $max, $oaipmhMetadataList){
        // pop metadata list
        $metadataList = $this->popMetadataByTag($tagName, $oaipmhMetadataList);
        
        // unbound -> null
        $xml = "";
        
        // $min以上のメタデータが存在しない
        if(count($metadataList) < $min){
            $ex = new OaipmhException("The value of the identifier argument is unknown or illegal in this repository.", "idDoesNotExist", "Metadata is not found");
            throw $ex;
        }
        
        // 最下層のタグ名を出力する
        $list = explode(".", $tagName);
        $writeTagName = $list[count($list) - 1];
        
        // $maxがnullでなければ、$max以上の要素を削除する
        if(isset($max)){
            for($ii = 0; $ii < $max && $ii < count($metadataList); $ii++){
                $xml .= $this->writeMetadata($writeTagName, $metadataList[$ii]);
            }
        } else {
            for($ii = 0; $ii < count($metadataList); $ii++){
                $xml .= $this->writeMetadata($writeTagName, $metadataList[$ii]);
            }
        }
        
        return $xml;
    }
    
    /**
     * To create a meta-data of the items in each tag
     * アイテムのメタデータをタグ毎に作成する
     *
     * @param string $tagName Tag name タグ名
     * @param string/BiblioStruct $metadata Metadata メタデータ
     * @return string Metadata surrounded by Tag String タグで囲われたメタデータの文字列
     */
    private function writeMetadata($tagName, $metadata){
        $xml = "";
        $xml .= "<". $tagName. ">";
        if(!is_string($metadata) && get_class($metadata) === "BiblioInfoStruct"){
            $xml .= $this->outputBiblioInfo($metadata);
        } else {
            $xml .= $this->convertSpecialCharsForXml($metadata);
        }
        $xml .= "</". $tagName. ">";
        return $xml;
    }
    
    /**
     * To create the output string for the magazine information
     * 雑誌情報用の出力文字列を作成する
     *
     * @param BiblioInfoStruct $value Magazine information 雑誌情報
     * @return string The output string 出力文字列
     */
    private function outputBiblioInfo($value){
        $biblioStr = "";
        // 雑誌名出力
        if(isset($value->biblioName) && strlen($value->biblioName) > 0){
            $biblioStr = $value->biblioName;
        } else if(isset($value->biblioNameEnglish) && strlen($value->biblioNameEnglish) > 0){
            $biblioStr = $value->biblioNameEnglish;
        }
        if(strlen($biblioStr) > 0){
            $biblioStr .= ", ";
        }
        
        // 巻、号出力
        if(isset($value->volume) && strlen($value->volume) > 0){
            $biblioStr .= $value->volume;
        }
        if(isset($value->issue) && strlen($value->issue) > 0){
            $biblioStr .= "(". $value->issue. ")";
        }
        if(strlen($biblioStr) > 0){
            $biblioStr .= ", ";
        }
        
        // 開始ページ、終了ページ出力
        if(isset($value->startPage) && strlen($value->startPage) > 0 && isset($value->endPage) && strlen($value->endPage) > 0){
            $biblioStr .= $value->startPage. "-". $value->endPage;
        } else if(isset($value->startPage) && strlen($value->startPage) > 0){
            $biblioStr .= $value->startPage;
        } else if(isset($value->endPage) && strlen($value->endPage) > 0){
            $biblioStr .= $value->endPage;
        }
        
        // 発行年月日出力
        if(isset($value->dateOfIssued) && strlen($value->dateOfIssued) > 0){
            $biblioStr .= "(". $value->dateOfIssued. ")";
        }
        
        return $this->convertSpecialCharsForXml($biblioStr);
    }
    
    /**
     * To convert the special character to the printable string to XML
     * 特殊文字をXMLに出力可能な文字列に変換する
     *
     * @param $value String to convert 変換する文字列
     * @return string Converted string 変換された文字列
     */
    private function convertSpecialCharsForXml($value) {
        $this->debugLog("[". __FUNCTION__. "] start. value:". $value, __FILE__, __CLASS__, __LINE__);
        $value = preg_replace('/[\x00-\x1f\x7f]/', '', $value);
        $convertedValue = htmlspecialchars($value, ENT_QUOTES, "UTF-8");
        $this->debugLog("[". __FUNCTION__. "] finish. convertedValue:". $convertedValue, __FILE__, __CLASS__, __LINE__);
        return $convertedValue;
    }
    
    /**
     * Return the contents of the Version tag
     * Versionタグの内容を返す
     *
     * @return string String of Version tag Versionタグの文字列
     */
    private function writeVersionTag(){
        $xml = "";
        
        // Spaseのバージョンを記述する
        $xml .= "<Version>". 
                SpaseConst::VERSION. 
                "</Version>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the Catalog tag
     * Catalogタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of Catalog tag structure Catalogタグ構造の文字列
     */
    private function writeCatalogTag($oaipmhMetadataList){
        $xml = "";
        $xml .= "<Catalog>";
        $xml .= $this->writeTag("Catalog.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= "<ResourceHeader>";
        $xml .= $this->writeTag("Catalog.ResourceHeader.ResourceName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Catalog.ResourceHeader.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Catalog.ResourceHeader.Description",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Catalog.ResourceHeader.Acknowledgement",0,1,$oaipmhMetadataList);
        $xml .= "<Contact>";
        $xml .= $this->writeTag("Catalog.ResourceHeader.Contact.PersonID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Catalog.ResourceHeader.Contact.Role",1,null,$oaipmhMetadataList);
        $xml .= "</Contact>";
        $xml .= "<InformationURL>";
        $xml .= $this->writeTag("Catalog.ResourceHeader.InformationURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</InformationURL>";
        $xml .= "<Association>";
        $xml .= $this->writeTag("Catalog.ResourceHeader.Association.AssociationID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Catalog.ResourceHeader.Association.AssociationType",1,1,$oaipmhMetadataList);
        $xml .= "</Association>";
        $xml .= "</ResourceHeader>";
        $xml .= "<AccessInformation>";
        $xml .= $this->writeTag("Catalog.AccessInformation.RepositoryID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Catalog.AccessInformation.Availability",0,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Catalog.AccessInformation.AccessRights",0,1,$oaipmhMetadataList);
        $xml .= "<AccessURL>";
        $xml .= $this->writeTag("Catalog.AccessInformation.AccessURL.Name",0,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Catalog.AccessInformation.AccessURL.URL",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Catalog.AccessInformation.AccessURL.Description",0,1,$oaipmhMetadataList);
        $xml .= "</AccessURL>";
        $xml .= $this->writeTag("Catalog.AccessInformation.Format",1,1,$oaipmhMetadataList);
        $xml .= "<DataExtent>";
        $xml .= $this->writeTag("Catalog.AccessInformation.DataExtent.Quantity",1,1,$oaipmhMetadataList);
        $xml .= "</DataExtent>";
        $xml .= "</AccessInformation>";
        $xml .= $this->writeTag("Catalog.InstrumentID",0,null,$oaipmhMetadataList);
        $xml .= $this->writeTag("Catalog.PhenomenonType",1,null,$oaipmhMetadataList);
        $xml .= "<TimeSpan>";
        $xml .= $this->writeTag("Catalog.TimeSpan.StartDate",1,1,$oaipmhMetadataList);
        $stopDate = $this->writeTag("Catalog.TimeSpan.StopDate",0,1,$oaipmhMetadataList);
        $relativeStopDate = $this->writeTag("Catalog.TimeSpan.RelativeStopDate",0,1,$oaipmhMetadataList);
        if(strlen($stopDate) > 0 && strlen($relativeStopDate) > 0){
            $ex = new OaipmhException("The value of the identifier argument is unknown or illegal in this repository.", "idDoesNotExist", "StopDate and RelativeStopDate is set");
            throw $ex;
        } else if(strlen($stopDate) > 0){
            $xml .= $stopDate;
        } else if(strlen($relativeStopDate) > 0){
            $xml .= $relativeStopDate;
        } else {
            $ex = new OaipmhException("The value of the identifier argument is unknown or illegal in this repository.", "idDoesNotExist", "StopDate and RelativeStopDate is not set");
            throw $ex;
        }
        $xml .= "</TimeSpan>";
        $xml .= $this->writeTag("Catalog.Keyword",0,null,$oaipmhMetadataList);
        
        $xml .= $this->writeParameterTag($oaipmhMetadataList, "Catalog");
        
        $xml .= "</Catalog>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the parameter tag
     * parameterタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @param string $upperTagName Level tag name of the Parameter tag Parameterタグの上位タグ名
     * @return string String of Parameter tag structure Parameterタグ構造の文字列
     */
    private function writeParameterTag($oaipmhMetadataList, $upperTagName){
        $xml = "";
        
        $xml .= "<Parameter>";
        $xml .= $this->writeTag($upperTagName. ".Parameter.Name",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag($upperTagName. ".Parameter.Description",0,1,$oaipmhMetadataList);
        $xml .= "<CoordinateSystem>";
        $xml .= $this->writeTag($upperTagName. ".Parameter.CoordinateSystem.CoordinateRepresentation",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag($upperTagName. ".Parameter.CoordinateSystem.CoordinateSystemName",1,1,$oaipmhMetadataList);
        $xml .= "</CoordinateSystem>";
        $xml .= "<Structure>";
        $xml .= $this->writeTag($upperTagName. ".Parameter.Structure.Size",1,1,$oaipmhMetadataList);
        $xml .= "<Element>";
        $xml .= $this->writeTag($upperTagName. ".Parameter.Structure.Element.Name",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag($upperTagName. ".Parameter.Structure.Element.Index",1,1,$oaipmhMetadataList);
        $xml .= "</Element>";
        $xml .= "</Structure>";
        
        // Parameterのタグ内にはField、Particle、Wave、Mixed、Supportのタグは共存できず、一つしか出力できない
        // 前もって、アイテムのメタデータ情報からField、Particle、Wave、Mixed、Supportが複数設定されていないことをチェックする
        // 複数設定されている場合、例外をエラーとする
        // なお、一つも設定されていない場合もエラーとする
        $limitsTagNum = 0;
        // メタデータ情報の配列を取得する
        $metadataStructList = $this->popMetadataByTag($upperTagName. ".Parameter", $oaipmhMetadataList);
        // Field、Particle、Wave、Mixed、Supportがあるかをそれぞれ確認する
        $limitsTagList = array("Field", "Particle", "Wave", "Support", "Mixed");
        for($ii = 0; $ii < count($limitsTagList); $ii++){
            if(isset($metadataStructList[$limitsTagList[$ii]])){
                $limitsTagNum++;
            }
        }
        // 複数ある場合または一つもない場合、エラーとする
        if($limitsTagNum > 1 || $limitsTagNum === 0){
            $ex = new OaipmhException("The value of the identifier argument is unknown or illegal in this repository.", "idDoesNotExist", "Parameter tag is invalid(". $upperTagName. ").");
            throw $ex;
        }
        
        if(isset($metadataStructList["Field"])){
            $xml .= "<Field>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Field.FieldQuantity",1,1,$oaipmhMetadataList);
            $xml .= "<FrequencyRange>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Field.FrequencyRange.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Field.FrequencyRange.High",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Field.FrequencyRange.Units",1,1,$oaipmhMetadataList);
            $xml .= "<Bin>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Field.FrequencyRange.Bin.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Field.FrequencyRange.Bin.High",1,1,$oaipmhMetadataList);
            $xml .= "</Bin>";
            $xml .= "</FrequencyRange>";
            $xml .= "</Field>";
        }
        
        if(isset($metadataStructList["Particle"])){
            $xml .= "<Particle>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.ParticleType",1,null,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.ParticleQuantity",1,1,$oaipmhMetadataList);
            $xml .= "<EnergyRange>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.EnergyRange.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.EnergyRange.High",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.EnergyRange.Units",1,1,$oaipmhMetadataList);
            $xml .= "<Bin>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.EnergyRange.Bin.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.EnergyRange.Bin.High",1,1,$oaipmhMetadataList);
            $xml .= "</Bin>";
            $xml .= "</EnergyRange>";
            $xml .= "<AzimuthalAngleRange>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.AzimuthalAngleRange.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.AzimuthalAngleRange.High",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.AzimuthalAngleRange.Units",1,1,$oaipmhMetadataList);
            $xml .= "<Bin>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.AzimuthalAngleRange.Bin.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.AzimuthalAngleRange.Bin.High",1,1,$oaipmhMetadataList);
            $xml .= "</Bin>";
            $xml .= "</AzimuthalAngleRange>";
            $xml .= "<PolarAngleRange>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.PolarAngleRange.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.PolarAngleRange.High",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.PolarAngleRange.Units",1,1,$oaipmhMetadataList);
            $xml .= "<Bin>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.PolarAngleRange.Bin.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Particle.PolarAngleRange.Bin.High",1,1,$oaipmhMetadataList);
            $xml .= "</Bin>";
            $xml .= "</PolarAngleRange>";
            $xml .= "</Particle>";
        }
        
        if(isset($metadataStructList["Wave"])){
            $xml .= "<Wave>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.WaveType",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.WaveQuantity",1,1,$oaipmhMetadataList);
            $xml .= "<EnergyRange>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.EnergyRange.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.EnergyRange.High",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.EnergyRange.Units",1,1,$oaipmhMetadataList);
            $xml .= "<Bin>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.EnergyRange.Bin.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.EnergyRange.Bin.High",1,1,$oaipmhMetadataList);
            $xml .= "</Bin>";
            $xml .= "</EnergyRange>";
            $xml .= "<FrequencyRange>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.FrequencyRange.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.FrequencyRange.High",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.FrequencyRange.Units",1,1,$oaipmhMetadataList);
            $xml .= "<Bin>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.FrequencyRange.Bin.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.FrequencyRange.Bin.High",1,1,$oaipmhMetadataList);
            $xml .= "</Bin>";
            $xml .= "</FrequencyRange>";
            $xml .= "<WavelengthRange>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.WavelengthRange.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.WavelengthRange.High",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.WavelengthRange.Units",1,1,$oaipmhMetadataList);
            $xml .= "<Bin>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.WavelengthRange.Bin.Low",1,1,$oaipmhMetadataList);
            $xml .= $this->writeTag($upperTagName. ".Parameter.Wave.WavelengthRange.Bin.High",1,1,$oaipmhMetadataList);
            $xml .= "</Bin>";
            $xml .= "</WavelengthRange>";
            $xml .= "</Wave>";
        }
        
        if(isset($metadataStructList["Mixed"])){
            $xml .= "<Mixed>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Mixed.MixedQuantity",1,1,$oaipmhMetadataList);
            $xml .= "</Mixed>";
        }
        
        if(isset($metadataStructList["Support"])){
            $xml .= "<Support>";
            $xml .= $this->writeTag($upperTagName. ".Parameter.Support.SupportQuantity",1,1,$oaipmhMetadataList);
            $xml .= "</Support>";
        }
        
        $xml .= "</Parameter>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the DisplayData tag
     * DisplayDataタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of DisplayData tag structure DisplayDataタグ構造の文字列
     */
    private function writeDisplayDataTag($oaipmhMetadataList){
        $xml = "";
        
        // DisplayData
        $xml .= "<DisplayData>";
        $xml .= $this->writeTag("DisplayData.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= "<ResourceHeader>";
        $xml .= $this->writeTag("DisplayData.ResourceHeader.ResourceName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.ResourceHeader.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.ResourceHeader.Description",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.ResourceHeader.Acknowledgement",0,1,$oaipmhMetadataList);
        $xml .= "<Contact>";
        $xml .= $this->writeTag("DisplayData.ResourceHeader.Contact.PersonID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.ResourceHeader.Contact.Role",1,null,$oaipmhMetadataList);
        $xml .= "</Contact>";
        $xml .= "<InformationURL>";
        $xml .= $this->writeTag("DisplayData.ResourceHeader.InformationURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</InformationURL>";
        $xml .= "<Association>";
        $xml .= $this->writeTag("DisplayData.ResourceHeader.Association.AssociationID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.ResourceHeader.Association.AssociationType",1,1,$oaipmhMetadataList);
        $xml .= "</Association>";
        $xml .= "</ResourceHeader>";
        $xml .= "<AccessInformation>";
        $xml .= $this->writeTag("DisplayData.AccessInformation.RepositoryID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.AccessInformation.Availability",0,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.AccessInformation.AccessRights",0,1,$oaipmhMetadataList);
        $xml .= "<AccessURL>";
        $xml .= $this->writeTag("DisplayData.AccessInformation.AccessURL.Name",0,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.AccessInformation.AccessURL.URL",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.AccessInformation.AccessURL.Description",0,1,$oaipmhMetadataList);
        $xml .= "</AccessURL>";
        $xml .= $this->writeTag("DisplayData.AccessInformation.Format",1,1,$oaipmhMetadataList);
        $xml .= "<DataExtent>";
        $xml .= $this->writeTag("DisplayData.AccessInformation.DataExtent.Quantity",1,1,$oaipmhMetadataList);
        $xml .= "</DataExtent>";
        $xml .= "</AccessInformation>";
        $xml .= $this->writeTag("DisplayData.InstrumentID",0,null,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.MeasurementType",1,null,$oaipmhMetadataList);
        $xml .= "<TemporalDescription>";
        $xml .= "<TimeSpan>";
        $xml .= $this->writeTag("DisplayData.TemporalDescription.TimeSpan.StartDate",1,1,$oaipmhMetadataList);
        $stopDate = $this->writeTag("DisplayData.TemporalDescription.TimeSpan.StopDate",0,1,$oaipmhMetadataList);
        $relativeStopDate = $this->writeTag("DisplayData.TemporalDescription.TimeSpan.RelativeStopDate",0,1,$oaipmhMetadataList);
        if(strlen($stopDate) > 0 && strlen($relativeStopDate) > 0){
            $ex = new OaipmhException("The value of the identifier argument is unknown or illegal in this repository.", "idDoesNotExist", "StopDate and RelativeStopDate is set");
            throw $ex;
        } else if(strlen($stopDate) > 0){
            $xml .= $stopDate;
        } else if(strlen($relativeStopDate) > 0){
            $xml .= $relativeStopDate;
        } else {
            $ex = new OaipmhException("The value of the identifier argument is unknown or illegal in this repository.", "idDoesNotExist", "StopDate and RelativeStopDate is not set");
            throw $ex;
        }
        $xml .= "</TimeSpan>";
        $xml .= "</TemporalDescription>";
        $xml .= $this->writeTag("DisplayData.ObservedRegion",0,null,$oaipmhMetadataList);
        $xml .= $this->writeTag("DisplayData.Keyword",0,null,$oaipmhMetadataList);
        
        $xml .= $this->writeParameterTag($oaipmhMetadataList, "DisplayData");
        
        $xml .= "</DisplayData>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the NumericalData tag
     * NumericalDataタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of NumericalData tag structure NumericalDataタグ構造の文字列
     */
    private function writeNumericalDataTag($oaipmhMetadataList){
        $xml = "";
        
        // NumericalData
        $xml .= "<NumericalData>";
        $xml .= $this->writeTag("NumericalData.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= "<ResourceHeader>";
        $xml .= $this->writeTag("NumericalData.ResourceHeader.ResourceName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.ResourceHeader.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.ResourceHeader.Description",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.ResourceHeader.Acknowledgement",0,1,$oaipmhMetadataList);
        $xml .= "<Contact>";
        $xml .= $this->writeTag("NumericalData.ResourceHeader.Contact.PersonID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.ResourceHeader.Contact.Role",1,null,$oaipmhMetadataList);
        $xml .= "</Contact>";
        $xml .= "<InformationURL>";
        $xml .= $this->writeTag("NumericalData.ResourceHeader.InformationURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</InformationURL>";
        $xml .= "<Association>";
        $xml .= $this->writeTag("NumericalData.ResourceHeader.Association.AssociationID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.ResourceHeader.Association.AssociationType",1,1,$oaipmhMetadataList);
        $xml .= "</Association>";
        $xml .= "</ResourceHeader>";
        $xml .= "<AccessInformation>";
        $xml .= $this->writeTag("NumericalData.AccessInformation.RepositoryID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.AccessInformation.Availability",0,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.AccessInformation.AccessRights",0,1,$oaipmhMetadataList);
        $xml .= "<AccessURL>";
        $xml .= $this->writeTag("NumericalData.AccessInformation.AccessURL.Name",0,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.AccessInformation.AccessURL.URL",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.AccessInformation.AccessURL.Description",0,1,$oaipmhMetadataList);
        $xml .= "</AccessURL>";
        $xml .= $this->writeTag("NumericalData.AccessInformation.Format",1,1,$oaipmhMetadataList);
        $xml .= "<DataExtent>";
        $xml .= $this->writeTag("NumericalData.AccessInformation.DataExtent.Quantity",1,1,$oaipmhMetadataList);
        $xml .= "</DataExtent>";
        $xml .= "</AccessInformation>";
        $xml .= $this->writeTag("NumericalData.InstrumentID",0,null,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.MeasurementType",1,null,$oaipmhMetadataList);
        $xml .= "<TemporalDescription>";
        $xml .= "<TimeSpan>";
        $xml .= $this->writeTag("NumericalData.TemporalDescription.TimeSpan.StartDate",1,1,$oaipmhMetadataList);
        $stopDate = $this->writeTag("NumericalData.TemporalDescription.TimeSpan.StopDate",0,1,$oaipmhMetadataList);
        $relativeStopDate = $this->writeTag("NumericalData.TemporalDescription.TimeSpan.RelativeStopDate",0,1,$oaipmhMetadataList);
        if(strlen($stopDate) > 0 && strlen($relativeStopDate) > 0){
            $ex = new OaipmhException("The value of the identifier argument is unknown or illegal in this repository.", "idDoesNotExist", "StopDate and RelativeStopDate is set");
            throw $ex;
        } else if(strlen($stopDate) > 0){
            $xml .= $stopDate;
        } else if(strlen($relativeStopDate) > 0){
            $xml .= $relativeStopDate;
        } else {
            $ex = new OaipmhException("The value of the identifier argument is unknown or illegal in this repository.", "idDoesNotExist", "StopDate and RelativeStopDate is not set");
            throw $ex;
        }
        $xml .= "</TimeSpan>";
        $xml .= "</TemporalDescription>";
        $xml .= $this->writeTag("NumericalData.ObservedRegion",0,null,$oaipmhMetadataList);
        $xml .= $this->writeTag("NumericalData.Keyword",0,null,$oaipmhMetadataList);
        
        $xml .= $this->writeParameterTag($oaipmhMetadataList, "NumericalData");
        
        $xml .= "</NumericalData>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the Document tag
     * Documentタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of Document tag structure Documentタグ構造の文字列
     */
    private function writeDocumentTag($oaipmhMetadataList){
        $xml = "";
        
        // Document
        $xml .= "<Document>";
        $xml .= $this->writeTag("Document.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= "<ResourceHeader>";
        $xml .= $this->writeTag("Document.ResourceHeader.ResourceName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Document.ResourceHeader.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Document.ResourceHeader.Description",1,1,$oaipmhMetadataList);
        $xml .= "<Contact>";
        $xml .= $this->writeTag("Document.ResourceHeader.Contact.PersonID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Document.ResourceHeader.Contact.Role",1,null,$oaipmhMetadataList);
        $xml .= "</Contact>";
        $xml .= "<InformationURL>";
        $xml .= $this->writeTag("Document.ResourceHeader.InformationURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</InformationURL>";
        $xml .= "<Association>";
        $xml .= $this->writeTag("Document.ResourceHeader.Association.AssociationID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Document.ResourceHeader.Association.AssociationType",1,1,$oaipmhMetadataList);
        $xml .= "</Association>";
        $xml .= "</ResourceHeader>";
        $xml .= "<AccessInformation>";
        $xml .= $this->writeTag("Document.AccessInformation.RepositoryID",1,1,$oaipmhMetadataList);
        $xml .= "<AccessURL>";
        $xml .= $this->writeTag("Document.AccessInformation.AccessURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</AccessURL>";
        $xml .= $this->writeTag("Document.AccessInformation.Format",1,1,$oaipmhMetadataList);
        $xml .= "<DataExtent>";
        $xml .= $this->writeTag("Document.AccessInformation.DataExtent.Quantity",1,1,$oaipmhMetadataList);
        $xml .= "</DataExtent>";
        $xml .= "</AccessInformation>";
        $xml .= $this->writeTag("Document.DocumentType",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Document.MIMEType",1,1,$oaipmhMetadataList);
        $xml .= "</Document>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the Granule tag
     * Granuleタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of Granule tag structure Granuleタグ構造の文字列
     */
    private function writeGranuleTag($oaipmhMetadataList){
        $xml = "";
        
        // Granule
        $xml .= "<Granule>";
        $xml .= $this->writeTag("Granule.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Granule.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Granule.ParentID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Granule.StartDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Granule.StopDate",1,1,$oaipmhMetadataList);
        $xml .= "<Source>";
        $xml .= $this->writeTag("Granule.Source.SourceType",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Granule.Source.URL",1,1,$oaipmhMetadataList);
        $xml .= "<Checksum>";
        $xml .= $this->writeTag("Granule.Source.Checksum.HashValue",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Granule.Source.Checksum.HashFunction",1,1,$oaipmhMetadataList);
        $xml .= "</Checksum>";
        $xml .= "<DataExtent>";
        $xml .= $this->writeTag("Granule.Source.DataExtent.Quantity",1,1,$oaipmhMetadataList);
        $xml .= "</DataExtent>";
        $xml .= "</Source>";
        $xml .= "</Granule>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the Instrument tag
     * Instrumentタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of Instrument tag structure Instrumentタグ構造の文字列
     */
    private function writeInstrumentTag($oaipmhMetadataList){
        $xml = "";
        
        // Instrument
        $xml .= "<Instrument>";
        $xml .= $this->writeTag("Instrument.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= "<ResourceHeader>";
        $xml .= $this->writeTag("Instrument.ResourceHeader.ResourceName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Instrument.ResourceHeader.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Instrument.ResourceHeader.Description",1,1,$oaipmhMetadataList);
        $xml .= "<Contact>";
        $xml .= $this->writeTag("Instrument.ResourceHeader.Contact.PersonID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Instrument.ResourceHeader.Contact.Role",1,null,$oaipmhMetadataList);
        $xml .= "</Contact>";
        $xml .= "<InformationURL>";
        $xml .= $this->writeTag("Instrument.ResourceHeader.InformationURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</InformationURL>";
        $xml .= "<Association>";
        $xml .= $this->writeTag("Instrument.ResourceHeader.Association.AssociationID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Instrument.ResourceHeader.Association.AssociationType",1,1,$oaipmhMetadataList);
        $xml .= "</Association>";
        $xml .= "</ResourceHeader>";
        $xml .= $this->writeTag("Instrument.InstrumentType",1,null,$oaipmhMetadataList);
        $xml .= $this->writeTag("Instrument.InvestigationName",1,null,$oaipmhMetadataList);
        $xml .= "<OperatingSpan>";
        $xml .= $this->writeTag("Instrument.OperatingSpan.StartDate",1,1,$oaipmhMetadataList);
        $xml .= "</OperatingSpan>";
        $xml .= $this->writeTag("Instrument.ObservatoryID",1,1,$oaipmhMetadataList);
        $xml .= "</Instrument>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the Observatory tag
     * Observatoryタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of Observatory tag structure Observatoryタグ構造の文字列
     */
    private function writeObservatoryTag($oaipmhMetadataList){
        $xml = "";
        
        // Observatory
        $xml .= "<Observatory>";
        $xml .= $this->writeTag("Observatory.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= "<ResourceHeader>";
        $xml .= $this->writeTag("Observatory.ResourceHeader.ResourceName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Observatory.ResourceHeader.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Observatory.ResourceHeader.Description",1,1,$oaipmhMetadataList);
        $xml .= "<Contact>";
        $xml .= $this->writeTag("Observatory.ResourceHeader.Contact.PersonID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Observatory.ResourceHeader.Contact.Role",1,null,$oaipmhMetadataList);
        $xml .= "</Contact>";
        $xml .= "<InformationURL>";
        $xml .= $this->writeTag("Observatory.ResourceHeader.InformationURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</InformationURL>";
        $xml .= "<Association>";
        $xml .= $this->writeTag("Observatory.ResourceHeader.Association.AssociationID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Observatory.ResourceHeader.Association.AssociationType",1,1,$oaipmhMetadataList);
        $xml .= "</Association>";
        $xml .= "</ResourceHeader>";
        $xml .= "<Location>";
        $xml .= $this->writeTag("Observatory.Location.ObservatoryRegion",1,null,$oaipmhMetadataList);
        $xml .= "</Location>";
        $xml .= "<OperatingSpan>";
        $xml .= $this->writeTag("Observatory.OperatingSpan.StartDate",1,1,$oaipmhMetadataList);
        $xml .= "</OperatingSpan>";
        $xml .= "</Observatory>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the Person tag
     * Personタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of Person tag structure Personタグ構造の文字列
     */
    private function writePersonTag($oaipmhMetadataList){
        $xml = "";
        
        // Person
        $xml .= "<Person>";
        $xml .= $this->writeTag("Person.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Person.ReleaseDate",0,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Person.PersonName",0,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Person.OrganizationName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Person.Email",0,null,$oaipmhMetadataList);
        $xml .= "</Person>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the Registry tag
     * Registryタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of Registry tag structure Registryタグ構造の文字列
     */
    private function writeRegistryTag($oaipmhMetadataList){
        $xml = "";
        // Registry
        $xml .= "<Registry>";
        $xml .= $this->writeTag("Registry.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= "<ResourceHeader>";
        $xml .= $this->writeTag("Registry.ResourceHeader.ResourceName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Registry.ResourceHeader.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Registry.ResourceHeader.Description",1,1,$oaipmhMetadataList);
        $xml .= "<Contact>";
        $xml .= $this->writeTag("Registry.ResourceHeader.Contact.PersonID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Registry.ResourceHeader.Contact.Role",1,null,$oaipmhMetadataList);
        $xml .= "</Contact>";
        $xml .= "<InformationURL>";
        $xml .= $this->writeTag("Registry.ResourceHeader.InformationURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</InformationURL>";
        $xml .= "<Association>";
        $xml .= $this->writeTag("Registry.ResourceHeader.Association.AssociationID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Registry.ResourceHeader.Association.AssociationType",1,1,$oaipmhMetadataList);
        $xml .= "</Association>";
        $xml .= "</ResourceHeader>";
        $xml .= "<AccessURL>";
        $xml .= $this->writeTag("Registry.AccessURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</AccessURL>";
        $xml .= "</Registry>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the Repository tag
     * Repositoryタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of Repository tag structure Repositoryタグ構造の文字列
     */
    private function writeRepositoryTag($oaipmhMetadataList){
        $xml = "";
        
        // Repository
        $xml .= "<Repository>";
        $xml .= $this->writeTag("Repository.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= "<ResourceHeader>";
        $xml .= $this->writeTag("Repository.ResourceHeader.ResourceName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Repository.ResourceHeader.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Repository.ResourceHeader.Description",1,1,$oaipmhMetadataList);
        $xml .= "<Contact>";
        $xml .= $this->writeTag("Repository.ResourceHeader.Contact.PersonID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Repository.ResourceHeader.Contact.Role",1,null,$oaipmhMetadataList);
        $xml .= "</Contact>";
        $xml .= "<InformationURL>";
        $xml .= $this->writeTag("Repository.ResourceHeader.InformationURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</InformationURL>";
        $xml .= "<Association>";
        $xml .= $this->writeTag("Repository.ResourceHeader.Association.AssociationID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Repository.ResourceHeader.Association.AssociationType",1,1,$oaipmhMetadataList);
        $xml .= "</Association>";
        $xml .= "</ResourceHeader>";
        $xml .= "<AccessURL>";
        $xml .= $this->writeTag("Repository.AccessURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</AccessURL>";
        $xml .= "</Repository>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the Service tag
     * Serviceタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of Service tag structure Serviceタグ構造の文字列
     */
    private function writeServiceTag($oaipmhMetadataList){
        $xml = "";
        
        // Service
        $xml .= "<Service>";
        $xml .= $this->writeTag("Service.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= "<ResourceHeader>";
        $xml .= $this->writeTag("Service.ResourceHeader.ResourceName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Service.ResourceHeader.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Service.ResourceHeader.Description",1,1,$oaipmhMetadataList);
        $xml .= "<Contact>";
        $xml .= $this->writeTag("Service.ResourceHeader.Contact.PersonID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Service.ResourceHeader.Contact.Role",1,null,$oaipmhMetadataList);
        $xml .= "</Contact>";
        $xml .= "<InformationURL>";
        $xml .= $this->writeTag("Service.ResourceHeader.InformationURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</InformationURL>";
        $xml .= "<Association>";
        $xml .= $this->writeTag("Service.ResourceHeader.Association.AssociationID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Service.ResourceHeader.Association.AssociationType",1,1,$oaipmhMetadataList);
        $xml .= "</Association>";
        $xml .= "</ResourceHeader>";
        $xml .= "<AccessURL>";
        $xml .= $this->writeTag("Service.AccessURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</AccessURL>";
        $xml .= "</Service>";
        
        return $xml;
    }
    
    /**
     * Return the contents of the Annotation tag
     * Annotationタグの内容を返す
     *
     * @param array $oaipmhMetadataList Meta data information of the item to be output 出力するアイテムのメタデータ情報
     *              array[$ii]
     * @return string String of Annotation tag structure Annotationタグ構造の文字列
     */
    private function writeAnnotationTag($oaipmhMetadataList){
        $xml = "";
        
        // Annotation
        $xml .= "<Annotation>";
        $xml .= $this->writeTag("Annotation.ResourceID",1,1,$oaipmhMetadataList);
        $xml .= "<ResourceHeader>";
        $xml .= $this->writeTag("Annotation.ResourceHeader.ResourceName",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Annotation.ResourceHeader.ReleaseDate",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Annotation.ResourceHeader.Description",1,1,$oaipmhMetadataList);
        $xml .= "<Contact>";
        $xml .= $this->writeTag("Annotation.ResourceHeader.Contact.PersonID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Annotation.ResourceHeader.Contact.Role",1,null,$oaipmhMetadataList);
        $xml .= "</Contact>";
        $xml .= "<InformationURL>";
        $xml .= $this->writeTag("Annotation.ResourceHeader.InformationURL.URL",1,1,$oaipmhMetadataList);
        $xml .= "</InformationURL>";
        $xml .= "<Association>";
        $xml .= $this->writeTag("Annotation.ResourceHeader.Association.AssociationID",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Annotation.ResourceHeader.Association.AssociationType",1,1,$oaipmhMetadataList);
        $xml .= "</Association>";
        $xml .= "</ResourceHeader>";
        $xml .= $this->writeTag("Annotation.AnnotationType",1,1,$oaipmhMetadataList);
        $xml .= "<TimeSpan>";
        $xml .= $this->writeTag("Annotation.TimeSpan.StartDate",1,1,$oaipmhMetadataList);
        $stopDate = $this->writeTag("Annotation.TimeSpan.StopDate",0,1,$oaipmhMetadataList);
        $relativeStopDate = $this->writeTag("Annotation.TimeSpan.RelativeStopDate",0,1,$oaipmhMetadataList);
        if(strlen($stopDate) > 0 && strlen($relativeStopDate) > 0){
            $ex = new OaipmhException("The value of the identifier argument is unknown or illegal in this repository.", "idDoesNotExist", "StopDate and RelativeStopDate is set");
            throw $ex;
        } else if(strlen($stopDate) > 0){
            $xml .= $stopDate;
        } else if(strlen($relativeStopDate) > 0){
            $xml .= $relativeStopDate;
        } else {
            $ex = new OaipmhException("The value of the identifier argument is unknown or illegal in this repository.", "idDoesNotExist", "StopDate and RelativeStopDate is not set");
            throw $ex;
        }
        $xml .= "</TimeSpan>";
        $xml .= "<ObservationExtent>";
        $xml .= $this->writeTag("Annotation.ObservationExtent.StartLocation",1,1,$oaipmhMetadataList);
        $xml .= $this->writeTag("Annotation.ObservationExtent.StopLocation",1,1,$oaipmhMetadataList);
        $xml .= "</ObservationExtent>";
        $xml .= "</Annotation>";
        
        return $xml;
    }
}
?>