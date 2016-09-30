<?php

/**
 * Common classes in accordance with specified mapping format, you get the item information from the database, to create a structure that summarizes the metadata name and metadata value
 * 指定されたマッピング形式に従い、データベースからアイテム情報を取得し、メタデータ名とメタデータ値をまとめた構造体を作成する共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Oaipmhmapper.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Structure class of bibliographic information
 * 書誌情報の構造体クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/oaipmh/BiblioInfoStruct.class.php';
/**
 * Constant class that defines the constants necessary to Oaipmh output in Spase format
 * Spase形式でのOaipmh出力に必要な定数を定義した定数クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/oaipmh/SpaseConst.class.php';

/**
 * Common classes in accordance with specified mapping format, you get the item information from the database, to create a structure that summarizes the metadata name and metadata value
 * 指定されたマッピング形式に従い、データベースからアイテム情報を取得し、メタデータ名とメタデータ値をまとめた構造体を作成する共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Oaipmh_Oaipmhmapper extends BusinessBase 
{
    /**
     * To create in the specified mapping format summarizes the items and string attached metadata information in it structure
     * 指定されたマッピング形式でアイテムとそれに紐付くメタデータ情報をまとめた構造体を作成する
     *
     * @param string $mappingFormat Mapping format マッピング形式
     * @param int $itemId Item ID for identifying the item アイテムを特定するためのアイテムID
     * @return ItemStruct Item information for the specified mapping format 指定されたマッピング形式のアイテム情報
     */
    public function createOaipmhItem($mappingFormat, $itemId){
        $this->debugLog("[". __FUNCTION__. "] start. mappingFormat:". $mappingFormat. " itemId:". $itemId, __FILE__, __CLASS__, __LINE__);
        $oaipmhItem = null;
        switch($mappingFormat){
            case SpaseConst::METADATA_PREFIX:
                $this->debugLog("[". __FUNCTION__. "] Spase.", __FILE__, __CLASS__, __LINE__);
                $columnName = "spase_mapping";
                $tagName = SpaseConst::ROOT_TAG_NAME;
                break;
            default:
                // マッピング名の指定に失敗しているため、nullを返す
                return null;
        }
        
        // タグ名とアイテムIDからアイテム情報を初期化する
        $oaipmhItem = new ItemStruct($tagName, $itemId, array());
        
        // ルートタグのメタデータ情報を初期化する
        $oaipmhMetadata = new MetadataStruct($tagName, array(), array());
        
        // メタデータの情報を作成する
        $metadataList = $this->createMetadataStruct($itemId, $columnName);
        
        // メタデータ情報をアイテム情報に挿入する
        $oaipmhMetadata->metadataValue = $metadataList;
        $oaipmhItem->metadataList[$tagName] = $oaipmhMetadata;
        
        $this->debugLog("[". __FUNCTION__. "] finish.", __FILE__, __CLASS__, __LINE__);
        return $oaipmhItem;
    }
    
    /**
     * To create a string attached metadata information to an item
     * アイテムに紐付くメタデータ情報を作成する
     *
     * @param int $itemId Item ID for identifying the item アイテムを特定するためのアイテムID
     * @param string $columnName Column name of the mapping to be read 読込むマッピングのカラム名
     * @return MetadataStruct Metadata stick string to items アイテムに紐付くメタデータ
     */
    private function createMetadataStruct($itemId, $columnName){
        $this->debugLog("[". __FUNCTION__. "] start. itemId:". $itemId. " columnName:". $columnName, __FILE__, __CLASS__, __LINE__);
        
        $metadataList = array();
        
        // メタデータ項目を検索し、削除されていない属性ID分ループする(非表示のデータまたは削除済みのデータは出力しない)
        $query = "SELECT ATTR.attribute_id, ATTR.input_type, ATTR.$columnName ". 
                 " FROM ". DATABASE_PREFIX. "repository_item_attr_type AS ATTR, ". 
                 "      ". DATABASE_PREFIX. "repository_item AS ITEM ".
                 " WHERE ATTR.is_delete = ? ". 
                 " AND ITEM.is_delete = ? ". 
                 " AND ITEM.item_id = ? ". 
                 " AND ITEM.item_type_id = ATTR.item_type_id ". 
                 " AND ATTR.hidden = ? ". 
                 " ORDER BY show_order ASC;";
        $params = array();
        $params[] = 0;
        $params[] = 0;
        $params[] = $itemId;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        
        for($ii = 0; $ii < count($result); $ii++){
            // マッピングのデータが空であれば、処理は未実施
            if(strlen($result[$ii][$columnName]) == 0){
                $this->debugLog("[". __FUNCTION__. "] mapping is empty. go to next metadata.", __FILE__, __CLASS__, __LINE__);
                continue;
            }
            
            // ピリオド区切りのマッピングを分解し、タグ名を取得する
            $tagList = explode(".", $result[$ii][$columnName]);
            $upperTagName = $tagList[0];
            if(!isset($metadataList[$upperTagName])){
                $metadataList[$upperTagName] = new MetadataStruct("", array(), array());
            }
            
            // メタデータの属性および値を取得する(配列)
            $valueList = array();
            $attributeList = array();
            $this->generateMetadata($itemId, $result[$ii]["attribute_id"], $result[$ii]["input_type"], $valueList, $attributeList);
            
            // 取得したデータを一覧に追加する
            for($jj = 0; $jj < count($valueList); $jj++){
                $this->generateMetadataStruct($result[$ii][$columnName], $valueList[$jj], $attributeList[$jj], $metadataList[$upperTagName]);
            }
        }
        
        $this->debugLog("[". __FUNCTION__. "] finish.", __FILE__, __CLASS__, __LINE__);
        return $metadataList;
    }
    
    /**
     * To create a string attached metadata information to an item from the mapping and metadata and attribute array
     * マッピング、メタデータおよび属性配列からアイテムに紐付くメタデータ情報を作成する
     *
     * @param string $mapping
     * @param string $value
     * @param array $attribute Associative array of attribute names and values 属性名と属性値の連想配列
     *                         array["lang"|"id"|......]
     * @param array $metadataList Meta data structure of each tag list タグごとのメタデータ構造体一覧
     *                            array[tagName]
     * @return MetadataStruct Metadata information that attach straps to the item アイテムに紐付くメタデータ情報
     */
    private function generateMetadataStruct($mapping, $value, $attribute, $metadataList){
        if(!is_string($value) && get_class($value) === "BiblioInfoStruct"){
            $msg = "[". __FUNCTION__. "] start. mapping:". $mapping. 
                    " journal name:". $value->biblioName. 
                    " journal name(en):". $value->biblioNameEnglish. 
                    " volume:". $value->volume. 
                    " issue:". $value->issue. 
                    " startPage:". $value->startPage. 
                    " endPage:". $value->endPage. 
                    " dateOfIssued:". $value->dateOfIssued. 
                    " attribute:". print_r($attribute, true);
            $this->debugLog($msg, __FILE__, __CLASS__, __LINE__);
        } else {
            $this->debugLog("[". __FUNCTION__. "] start. mapping:". $mapping. " value:". $value. " attribute:". print_r($attribute, true), __FILE__, __CLASS__, __LINE__);
        }
        
        // マッピングを「.」で分解
        $mappingList = explode(".", $mapping);
        
        if(count($mappingList) > 1){
            // 1番要素以降がある
            // 構造体を生成し、詰める
            if(isset($metadataList->metadataValue[$mappingList[1]])){
                $this->debugLog("[". __FUNCTION__. "] metadata is exists.", __FILE__, __CLASS__, __LINE__);
                $metadata = $metadataList->metadataValue[$mappingList[1]];
            } else {
                $this->debugLog("[". __FUNCTION__. "] metadata is not exists. create metadata.", __FILE__, __CLASS__, __LINE__);
                $metadata = new MetadataStruct($mappingList[1], array(), array());
            }
            $mapping = "";
            for($ii = 1; $ii <count($mappingList); $ii++){
                $this->debugLog("[". __FUNCTION__. "] reimplode mapping.", __FILE__, __CLASS__, __LINE__);
                if(strlen($mapping) > 0){
                    $this->debugLog("[". __FUNCTION__. "] reimplode mapping over one.", __FILE__, __CLASS__, __LINE__);
                    $mapping .= ".";
                }
                $mapping .= $mappingList[$ii];
            }
            $metadataList->metadataName = $mappingList[0];
            $metadataList->metadataValue[$mappingList[1]] = $this->generateMetadataStruct($mapping, $value, $attribute, $metadata);
        } else {
            $this->debugLog("[". __FUNCTION__. "] bottom layer. set metadata value.", __FILE__, __CLASS__, __LINE__);
            // 1番要素以降がない
            $attributeList = array();
            foreach($attribute as $key => $val){
                $this->debugLog("[". __FUNCTION__. "] create OaipmhAttribute array.", __FILE__, __CLASS__, __LINE__);
                array_push($attributeList, new AttributeStruct($key, $val));
            }
            
            // 配列を作成し、詰める
            array_push($metadataList->metadataValue, $value);
            if(count($attributeList) > 0){
                $this->debugLog("[". __FUNCTION__. "] attribute is over zero.", __FILE__, __CLASS__, __LINE__);
                array_push($metadataList->attributes, $attributeList);
            }
        }
        
        $this->debugLog("[". __FUNCTION__. "] finish.", __FILE__, __CLASS__, __LINE__);
        return $metadataList;
    }
    
    /**
     * To create a metadata information of the item to be passed to other processing, the attribute ID and item ID of the given item type, acquires metadata
     * 他の処理に渡すアイテムのメタデータ情報を作成するため、指定されたアイテムタイプの属性IDとアイテムIDから、メタデータを取得する
     *
     * @param int $itemId ID of the item that you want to get metadata information メタデータ情報を取得するアイテムのID
     * @param int $attributeId Attribute ID identifying the item's metadata アイテムのメタデータを特定する属性ID
     * @param string $inputType Input format of meta data メタデータの入力形式
     * @param array $valueList Meta data array メタデータ配列
     *                         array[$ii]
     * @param array $attributeList Array of string attached attribute to tag タグに紐付く属性の配列
     *                             array[$ii][attribute_key]
     */
    private function generateMetadata($itemId, $attributeId, $inputType, &$valueList, &$attributeList){
        $this->debugLog("[". __FUNCTION__. "] start. itemId:". $itemId. " attributeId:". $attributeId. " inputType:". $inputType, __FILE__, __CLASS__, __LINE__);
        $valueList = array(); // string
        $attributeList = array(); // OaipmhAttribute
        
        // inputTypeによって参照するテーブルが異なり、メタデータの表記も異なる
        switch($inputType){
            case "name":
                $this->debugLog("[". __FUNCTION__. "] select metadata by repository_personal_name", __FILE__, __CLASS__, __LINE__);
                // 姓名を半角スペースで結合
                $this->generateMetadataForName($itemId, $attributeId, $valueList, $attributeList);
                break;
            case "thumbnail":
                $this->debugLog("[". __FUNCTION__. "] select metadata by repository_thumbnail", __FILE__, __CLASS__, __LINE__);
                // URL
                $this->generateMetadataForThumbnail($itemId, $attributeId, $valueList, $attributeList);
                break;
            case "file":
            case "file_price":
                $this->debugLog("[". __FUNCTION__. "] select metadata by repository_file", __FILE__, __CLASS__, __LINE__);
                // URIを表示
                $this->generateMetadataForFile($itemId, $attributeId, $valueList, $attributeList);
                break;
            case "biblio_info":
                $this->debugLog("[". __FUNCTION__. "] select metadata by repository_biblio_info", __FILE__, __CLASS__, __LINE__);
                // journal(ja) = journal(en),volume,issue,spage,epage,dateofissued
                $this->generateMetadataForBiblio($itemId, $attributeId, $valueList, $attributeList);
                break;
            case "supple":
                $this->debugLog("[". __FUNCTION__. "] select metadata by repository_supple", __FILE__, __CLASS__, __LINE__);
                // URI
                $this->generateMetadataForSupple($itemId, $attributeId, $valueList, $attributeList);
                break;
            default:
                $this->debugLog("[". __FUNCTION__. "] select metadata by repository_item_attr", __FILE__, __CLASS__, __LINE__);
                // repository_item_attrテーブルより登録されている値を取得する
                $this->generateMetadataForText($itemId, $attributeId, $valueList, $attributeList, $inputType);
                break;
        }
        $this->debugLog("[". __FUNCTION__. "] finish.", __FILE__, __CLASS__, __LINE__);
    }
    
    /**
     * To create a meta-data and attributes of the text format
     * テキスト形式のメタデータと属性を作成する
     *
     * @param int $itemId ID of the item that you want to get metadata information メタデータ情報を取得するアイテムのID
     * @param int $attributeId Attribute ID identifying the item's metadata アイテムのメタデータを特定する属性ID
     * @param array $valueList String array 文字列配列
     *                         array[$ii]
     * @param array $attributeList Array of string attached attribute to tag タグに紐付く属性の配列
     *                             array[$ii][attribute_key]
     * @param string $inputType Input format of meta data メタデータの入力形式
     */
    private function generateMetadataForText($itemId, $attributeId, &$valueList, &$attributeList, $inputType){
        $this->debugLog("[". __FUNCTION__. "] start. itemId:". $itemId. " attributeId:". $attributeId, __FILE__, __CLASS__, __LINE__);
        $result = $this->executeSqlFile(dirname(__FILE__). "/item_attr_select_getTextMetadata.sql", $this->generateArrayToSelectAttrVal($itemId, $attributeId));
        
        $valueList = array();
        $attributeList = array();
        foreach($result as $key => $val){
            $this->debugLog("[". __FUNCTION__. "] value:". $val, __FILE__, __CLASS__, __LINE__);
            if($inputType === "link"){
                $linkData = explode("|", $val["attribute_value"]);
                // リンクURLを属性値とする(リンクURL|リンク名の形式で保存されているため、0番目の要素を取り出す)
                $attributeValue = $linkData[0];
            } else {
                $attributeValue = $val["attribute_value"];
            }
            
            array_push($valueList, $attributeValue);
            array_push($attributeList, array());
        }
        $this->debugLog("[". __FUNCTION__. "] finish.", __FILE__, __CLASS__, __LINE__);
    }
    
    /**
     * To create a meta-data and attributes of the name format
     * 氏名形式のメタデータと属性を作成する
     *
     * @param int $itemId ID of the item that you want to get metadata information メタデータ情報を取得するアイテムのID
     * @param int $attributeId Attribute ID identifying the item's metadata アイテムのメタデータを特定する属性ID
     * @param array $valueList Array of strings linked name a single-byte space 氏名を半角スペースで連結した文字列の配列
     *                         array[$ii]
     * @param array $attributeList Array of string attached attribute to tag タグに紐付く属性の配列
     *                             array[$ii]["lang"|"id"......]
     */
    private function generateMetadataForName($itemId, $attributeId, &$valueList, &$attributeList){
        $this->debugLog("[". __FUNCTION__. "] start. itemId:". $itemId. " attributeId:". $attributeId, __FILE__, __CLASS__, __LINE__);
        
        // 言語を取得する
        $language = $this->selectMetadataLanguage($itemId, $attributeId);
        
        // 姓名を取得する
        $result = $this->executeSqlFile(dirname(__FILE__). "/personal_name_select_getNameMetadata.sql", $this->generateArrayToSelectAttrVal($itemId, $attributeId));
        
        $valueList = array();
        $attributeList = array();
        foreach($result as $key => $val){
            $authorName = "";
            if($language === "english"){
                // name family
                if(strlen($val["name"]) > 0){
                    $this->debugLog("[". __FUNCTION__. "] set english name", __FILE__, __CLASS__, __LINE__);
                    $authorName = $val["name"];
                }
                if(strlen($authorName) > 0){
                    $this->debugLog("[". __FUNCTION__. "] set space", __FILE__, __CLASS__, __LINE__);
                    $authorName .= " ";
                }
                $authorName .= $val["family"];
                $this->debugLog("[". __FUNCTION__. "] authorName:". $authorName, __FILE__, __CLASS__, __LINE__);
            } else {
                // family name
                $authorName .= $val["family"];
                if(strlen($val["name"]) > 0){
                    $this->debugLog("[". __FUNCTION__. "] set no english name", __FILE__, __CLASS__, __LINE__);
                    $authorName .= " ". $val["name"];
                }
                $this->debugLog("[". __FUNCTION__. "] authorName:". $authorName, __FILE__, __CLASS__, __LINE__);
            }
            array_push($valueList, $authorName);
            array_push($attributeList, array("lang" => $language));
        }
        $this->debugLog("[". __FUNCTION__. "] finish.", __FILE__, __CLASS__, __LINE__);
    }
    
    /**
     * To get the language of the metadata
     * メタデータの言語を取得する
     *
     * @param int $itemId Item ID for identifying the meta data item メタデータ項目を特定するためのアイテムID
     * @param int $attributeId Attribute ID for identifying the meta data item メタデータ項目を特定するための属性ID
     * @return string Metadata items of language メタデータ項目の言語
     */
    private function selectMetadataLanguage($itemId, $attributeId){
        $this->debugLog("[". __FUNCTION__. "] start. itemId:". $itemId. " attributeId:". $attributeId, __FILE__, __CLASS__, __LINE__);
        
        // アイテムと属性に紐付く言語を取得する
        $params = array();
        $params[] = $itemId;
        $params[] = $attributeId;
        $params[] = 0;
        $params[] = 0;
        $result = $this->executeSqlFile(dirname(__FILE__). "/item_attr_type_select_getMetadataLang.sql", $params);
        
        $this->debugLog("[". __FUNCTION__. "] finish. language:". $result[0]["language"], __FILE__, __CLASS__, __LINE__);
        return $result[0]["language"];
    }
    
    /**
     * To create a meta-data and attributes of the thumbnail format
     * サムネイル形式のメタデータと属性を作成する
     *
     * @param int $itemId ID of the item that you want to get metadata information メタデータ情報を取得するアイテムのID
     * @param int $attributeId Attribute ID identifying the item's metadata アイテムのメタデータを特定する属性ID
     * @param array $valueList Thumbnail download URL array サムネイルダウンロードURL配列
     *                         array[$ii]
     * @param array $attributeList Array of string attached attribute to tag タグに紐付く属性の配列
     *                             array[$ii][attribute_key]
     */
    private function generateMetadataForThumbnail($itemId, $attributeId, &$valueList, &$attributeList){
        $this->debugLog("[". __FUNCTION__. "] start. itemId:". $itemId. " attributeId:". $attributeId, __FILE__, __CLASS__, __LINE__);
        $result = $this->executeSqlFile(dirname(__FILE__). "/thumbnail_select_getThumbnailMetadata.sql", $this->generateArrayToSelectAttrVal($itemId, $attributeId));
        
        $valueList = array();
        $attributeList = array();
        foreach($result as $key => $val){
            $downloadUrl = BASE_URL.'/?action=repository_action_common_download'.
                           '&item_id='.$val["item_id"].
                           '&item_no='.$val["item_no"].
                           '&attribute_id='.$val["attribute_id"].
                           '&file_no='.$val["file_no"].
                           '&img=true';
            array_push($valueList, $downloadUrl);
            $this->debugLog("[". __FUNCTION__. "] downloadUrl:". $downloadUrl, __FILE__, __CLASS__, __LINE__);
            array_push($attributeList, array());
        }
        $this->debugLog("[". __FUNCTION__. "] finish.", __FILE__, __CLASS__, __LINE__);
    }
    
    /**
     * To create a meta-data and attributes of the file format
     * ファイル形式のメタデータと属性を作成する
     *
     * @param int $itemId ID of the item that you want to get metadata information メタデータ情報を取得するアイテムのID
     * @param int $attributeId Attribute ID identifying the item's metadata アイテムのメタデータを特定する属性ID
     * @param array $valueList File download URL array ファイルダウンロードURL配列
     *                         array[$ii]
     * @param array $attributeList Array of string attached attribute to tag タグに紐付く属性の配列
     *                             array[$ii][attribute_key]
     */
    private function generateMetadataForFile($itemId, $attributeId, &$valueList, &$attributeList){
        $this->debugLog("[". __FUNCTION__. "] start. itemId:". $itemId. " attributeId:". $attributeId, __FILE__, __CLASS__, __LINE__);
        $result = $this->executeSqlFile(dirname(__FILE__). "/file_select_getFileMetadata.sql", $this->generateArrayToSelectAttrVal($itemId, $attributeId));
        
        $valueList = array();
        $attributeList = array();
        foreach($result as $key => $val){
            $downloadUrl = BASE_URL.'/?action=repository_action_common_download'.
                           '&item_id='.$val["item_id"].
                           '&item_no='.$val["item_no"].
                           '&attribute_id='.$val["attribute_id"].
                           '&file_no='.$val["file_no"];
            array_push($valueList, $downloadUrl);
            $this->debugLog("[". __FUNCTION__. "] downloadUrl:". $downloadUrl, __FILE__, __CLASS__, __LINE__);
            array_push($attributeList, array());
        }
        $this->debugLog("[". __FUNCTION__. "] finish.", __FILE__, __CLASS__, __LINE__);
    }
    
    /**
     * To create a meta-data and attributes of the bibliographic information format
     * 書誌情報形式のメタデータと属性を作成する
     *
     * @param int $itemId ID of the item that you want to get metadata information メタデータ情報を取得するアイテムのID
     * @param int $attributeId Attribute ID identifying the item's metadata アイテムのメタデータを特定する属性ID
     * @param array $valueList Meta data array メタデータ配列
     *                         array[$ii]
     * @param array $attributeList Array of string attached attribute to tag タグに紐付く属性の配列
     *                             array[$ii][attribute_key]
     */
    private function generateMetadataForBiblio($itemId, $attributeId, &$valueList, &$attributeList){
        $this->debugLog("[". __FUNCTION__. "] start. itemId:". $itemId. " attributeId:". $attributeId, __FILE__, __CLASS__, __LINE__);
        $result = $this->executeSqlFile(dirname(__FILE__). "/biblio_info_select_getBiblioMetadata.sql", $this->generateArrayToSelectAttrVal($itemId, $attributeId));
        
        $valueList = array();
        $attributeList = array();
        foreach($result as $key => $val){
            // 書誌情報に関してはjunii2、DublinCore、LIDOなどでマッピング設定方法が異なるため、
            // 構造体を出力クラスに渡すようにし、どのように出力するかに関しては出力クラスで決定する
            $biblioInfo = new BiblioInfoStruct($val["biblio_name"], $val["biblio_name_english"], $val["volume"], $val["issue"], $val["start_page"], $val["end_page"], $val["date_of_issued"]);
            
            array_push($valueList, $biblioInfo);
            $this->debugLog("[". __FUNCTION__. "] biblio_name:". $biblioInfo->biblioName. " biblio_name_english:". $biblioInfo->biblioNameEnglish. " volume:". $biblioInfo->volume. " issue:". $biblioInfo->issue. " start_page:". $biblioInfo->startPage. " end_page:". $biblioInfo->endPage. " date_of_issued:". $biblioInfo->dateOfIssued, __FILE__, __CLASS__, __LINE__);
            array_push($attributeList, array());
        }
        $this->debugLog("[". __FUNCTION__. "] finish.", __FILE__, __CLASS__, __LINE__);
    }
    
    /**
     * To create a meta-data and attributes of the supplemental content format
     * サプリメンタルコンテンツ形式のメタデータと属性を作成する
     *
     * @param int $itemId ID of the item that you want to get metadata information メタデータ情報を取得するアイテムのID
     * @param int $attributeId Attribute ID identifying the item's metadata アイテムのメタデータを特定する属性ID
     * @param array $valueList URL array of supplemental content サプリメンタルコンテンツのURI配列
     *                         array[$ii]
     * @param array $attributeList Array of string attached attribute to tag タグに紐付く属性の配列
     *                             array[$ii][attribute_key]
     */
    private function generateMetadataForSupple($itemId, $attributeId, &$valueList, &$attributeList){
        $this->debugLog("[". __FUNCTION__. "] start. itemId:". $itemId. " attributeId:". $attributeId, __FILE__, __CLASS__, __LINE__);
        // select repository_supple on uri column
        $result = $this->executeSqlFile(dirname(__FILE__). "/supple_select_getSuppleMetadata.sql", $this->generateArrayToSelectAttrVal($itemId, $attributeId));
        
        $valueList = array();
        $attributeList = array();
        foreach($result as $key => $val){
            array_push($valueList, $val["uri"]);
            $this->debugLog("[". __FUNCTION__. "] uri:". $val["uri"], __FILE__, __CLASS__, __LINE__);
            array_push($attributeList, array());
        }
        $this->debugLog("[". __FUNCTION__. "] finish.", __FILE__, __CLASS__, __LINE__);
    }
    
    /**
     * Get a list of strings to be replaced with SQL file to identify the metadata values
     * メタデータ値を特定するためにSQLファイル内で置換する文字列の一覧を取得する
     *
     * @param int $itemId Item ID required to identify the metadata values メタデータ値を特定するのに必要なアイテムID
     * @param int $attrId Attribute ID required to identify the metadata values メタデータ値を特定するのに必要な属性ID
     * @return array Replacement string list required to run the SQL to identify the metadata メタデータを特定するためSQLを実行するために必要な置換文字列一覧
     */
    private function generateArrayToSelectAttrVal($itemId, $attrId){
        $params = array();
        $params[] = $itemId;    // item_id
        $params[] = $attrId;    // attribute_id
        $params[] = 0;          // is_delete
        
        return $params;
    }
}
?>