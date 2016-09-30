<?php
/**
 * Generate JSON type data business class
 * JSON形式データ生成ビジネスクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Generatejsontypedata.class.php 69174 2016-06-22 06:43:30Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Business Logic base class
 * ビジネスロジック基底クラス
 * 
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 * 
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * Generate JSON type data business class
 * JSON形式データ生成ビジネスクラス
 * 
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Json_Generatejsontypedata extends BusinessBase
{
    /**
     * Encode input by JSON without converting multi byte characters
     * マルチバイト文字を変換せずに入力をJSON形式にエンコードする
     * 
     * @param array $input Array for JSON encode
     *                     JSONエンコードする配列
     *                     array["@context"|"@id"|"@graph"]...
     *
     * @return string Output by JSON
     *                JSON形式の出力
     */
    public function raw_json_encode($input) 
    {
        return preg_replace_callback(
            '/\\\\u([0-9a-zA-Z]{4})/',
            array(get_class($this), 'convertUtf8'),
            json_encode($input)
        );
     
    }
    
    /**
     * Convert string to be replaced to UTF-8
     * 置換対象となった文字列をUTF-8に変換する
     * 
     * @param array $matches String to be replaced
     *                       置換対象となった文字列
     *                       array[$ii]
     *
     * @return string String converted to UTF-8
     *                UTF-8に変換された文字列
     */
    private function convertUtf8($matches)
    {
        return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
    }
    
    /**
     * Generate item output by JSON
     * アイテム情報の出力をJSON形式で生成する
     * 
     * @param array $itemData Item data 
     *                        アイテム情報
     *                        array["item"|"item_type"|"item_attr_type"|...][$ii]["title"|"title_english"|"language"|...]
     * @param array $indexData Index data belonged by item
     *                         アイテムが所属するインデックス情報
     *                         array["position_index"][$ii]["item_id"|"item_no"|"index_id"|...]
     * @param array $public_index_list Public index list
     *                                 公開インデックス一覧
     *                                 array[$ii]
     * @param array $block_info Block data located WEKO
     *                          WEKOが配置されているブロック情報
     *                          array["page_id"|"block_id"]
     *
     * @return array Array for JSON encode having item data
     *               アイテム情報を持つJSONエンコード用の配列
     *               array["title"|"title_english"|"language"|...]
     */
    public function generateItem($itemData, $indexData, $public_index_list, $block_info)
    {
        $item = array();
        
        $this->generateTitle($itemData, $item);
        $item["language"] = $itemData["item"][0]["language"];
        $this->generateShownDate($itemData, $item);
        $this->generateSearchKey($itemData, $item);
        $this->generateMetadata($itemData, $item);
        $this->generateIndex($itemData, $indexData, $public_index_list, $block_info, $item);
        
        return $item;
    }
    
    /**
     * Generate item title output by JSON
     * アイテムタイトル情報の出力をJSON形式で生成する
     * 
     * @param array $itemData Item data 
     *                        アイテム情報
     *                        array["item"|"item_type"|"item_attr_type"|...][$ii]["title"|"title_english"|"language"|...]
     * @param array $titleArray Array for JSON encode having item title
     *                           アイテムタイトル情報を持つJSONエンコード用の配列
     *                           array["title"|"title_english"]
     */
    private function generateTitle($itemData, &$titleArray)
    {
        $titleArray = array();
        $titleArray["title"] = "";
        $titleArray["title_english"] = "";
        if(isset($itemData["item"][0]["title"]) && strlen($itemData["item"][0]["title"]) > 0)
        {
            $titleArray["title"] = $itemData["item"][0]["title"];
        }
        if(isset($itemData["item"][0]["title_english"]) && strlen($itemData["item"][0]["title_english"]) > 0)
        {
            $titleArray["title_english"] = $itemData["item"][0]["title_english"];
        }
    }
    
    /**
     * Generate item shown date output by JSON
     * アイテム公開日の出力をJSON形式で生成する
     * 
     * @param array $itemData Item data 
     *                        アイテム情報
     *                        array["item"|"item_type"|"item_attr_type"|...][$ii]["title"|"title_english"|"language"|...]
     * @param array $shownDateArray Array for JSON encode having item shown date
     *                           アイテム公開日を持つJSONエンコード用の配列
     *                           array["shown_date"]
     */
    private function generateShownDate($itemData, &$shownDateArray)
    {
        $result = preg_match('/^\d{4}-\d{2}-\d{2}/', $itemData["item"][0]["shown_date"], $shown_date);
        if($result && $result > 0)
        {
            $shownDateArray["shown_date"] = $shown_date[0];
        }
    }
    
    /**
     * Generate item search keyword output by JSON
     * アイテムの検索キーワードの出力をJSON形式で生成する
     * 
     * @param array $itemData Item data 
     *                        アイテム情報
     *                        array["item"|"item_type"|"item_attr_type"|...][$ii]["title"|"title_english"|"language"|...]
     * @param array $searchKeyArray Array for JSON encode having item search key
     *                               検索キーワードを持つJSONエンコード用の配列
     *                               array["search_key"]["ja"|"en"][$ii]
     */
    private function generateSearchKey($itemData, &$searchKeyArray)
    {
        $searchKeyArray["search_key"] = array();
        $searchKeyArray["search_key"]["ja"] = array();
        $searchKeyArray["search_key"]["en"] = array();
        if(isset($itemData["item"][0]["serch_key"]) && strlen($itemData["item"][0]["serch_key"]) > 0)
        {
            $searchKey = explode("|", $itemData["item"][0]["serch_key"]);
            $searchKeyArray["search_key"]["ja"] = $searchKey;
        }
        if(isset($itemData["item"][0]["serch_key_english"]) && strlen($itemData["item"][0]["serch_key_english"]) > 0)
        {
            $searchKeyEn = explode("|", $itemData["item"][0]["serch_key_english"]);
            $searchKeyArray["search_key"]["en"] = $searchKeyEn;
        }
    }
    
    /**
     * Generate item metadata output by JSON
     * アイテムメタデータの出力をJSON形式で生成する
     * 
     * @param array $itemData Item data 
     *                        アイテム情報
     *                        array["item"|"item_type"|"item_attr_type"|...][$ii]["title"|"title_english"|"language"|...]
     * @param array $metadataArray Array for JSON encode having item metadata
     *                              アイテムメタデータを持つJSONエンコード用の配列
     *                              array["meta"]["name"|"type"|"values"][$ii]
     */
    private function generateMetadata($itemData, &$metadataArray)
    {
        $metadataArray["meta"] = array();
        for($ii = 0; $ii < count($itemData["item_attr_type"]); $ii++)
        {
            if($itemData["item_attr_type"][$ii]["hidden"] == 0)
            {
                switch($itemData["item_attr_type"][$ii]["input_type"])
                {
                    case "text":
                    case "textarea":
                    case "checkbox":
                    case "radio":
                    case "select":
                        $this->generateString($itemData["item_attr_type"][$ii], $itemData["item_attr"][$ii], $metadataArray["meta"]);
                        break;
                    case "name":
                        $this->generateName($itemData["item_attr_type"][$ii], $itemData["item_attr"][$ii], $metadataArray["meta"]);
                        break;
                    case "link":
                        $this->generateUrl($itemData["item_attr_type"][$ii], $itemData["item_attr"][$ii], $metadataArray["meta"]);
                        break;
                    case "biblio_info":
                        $this->generateJournal($itemData["item_attr_type"][$ii], $itemData["item_attr"][$ii], $metadataArray["meta"]);
                        break;
                    case "file":
                    case "file_price":
                        $this->generateFile($itemData["item_attr_type"][$ii], $itemData["item_attr"][$ii], $metadataArray["meta"]);
                        break;
                    case "thumbnail":
                        $this->generateThumbnail($itemData["item_attr_type"][$ii], $itemData["item_attr"][$ii], $metadataArray["meta"]);
                        break;
                    case "date":
                        $this->generateDate($itemData["item_attr_type"][$ii], $itemData["item_attr"][$ii], $metadataArray["meta"]);
                        break;
                    case "heading":
                        $this->generateHeading($itemData["item_attr_type"][$ii], $itemData["item_attr"][$ii], $metadataArray["meta"]);
                        break;
                    default:
                        break;
                }
            }
        }
    }
    
    /**
     * Generate item metadata text data output by JSON
     * アイテムメタデータのテキストデータの出力をJSON形式で生成する
     * 
     * @param array $itemAttrType Item attribute type data
     *                            アイテムタイプ属性情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|...]
     * @param array $itemAttr Item attribute data
     *                        アイテム属性情報
     *                        array[$ii]["item_id"|"item_no"|"attribute_id"|...]
     * @param array $stringArray Array for JSON encode having item metadata text data
     *                            アイテムメタデータのテキストデータを持つJSONエンコード用の配列
     *                            array["name"|"type"|"values"][$ii]
     */
    private function generateString($itemAttrType, $itemAttr, &$stringArray)
    {
        $stringArray[$itemAttrType["attribute_id"]] = array();
        $stringArray[$itemAttrType["attribute_id"]]["name"] = $itemAttrType["attribute_name"];
        $stringArray[$itemAttrType["attribute_id"]]["type"] = "string";
        $stringArray[$itemAttrType["attribute_id"]]["values"] = array();
        for($ii = 0; $ii < count($itemAttr); $ii++)
        {
            $stringArray[$itemAttrType["attribute_id"]]["values"][] = $itemAttr[$ii]["attribute_value"];
        }
    }
    
    /**
     * Generate item metadata name data output by JSON
     * アイテムメタデータの著者情報の出力をJSON形式で生成する
     * 
     * @param array $itemAttrType Item attribute type data
     *                            アイテムタイプ属性情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|...]
     * @param array $itemAttr Item attribute data
     *                        アイテム属性情報
     *                        array[$ii]["item_id"|"item_no"|"attribute_id"|...]
     * @param array $nameArray Array for JSON encode having item metadata name data
     *                            アイテムメタデータの著者情報を持つJSONエンコード用の配列
     *                            array["name"|"type"|"values"][$ii]["family"|"name"]
     */
    private function generateName($itemAttrType, $itemAttr, &$nameArray)
    {
        $nameArray[$itemAttrType["attribute_id"]] = array();
        $nameArray[$itemAttrType["attribute_id"]]["name"] = $itemAttrType["attribute_name"];
        $nameArray[$itemAttrType["attribute_id"]]["type"] = "name";
        $nameArray[$itemAttrType["attribute_id"]]["values"] = array();
        for($ii = 0; $ii < count($itemAttr); $ii++)
        {
            $nameArray[$itemAttrType["attribute_id"]]["values"][] = array();
            $nameArray[$itemAttrType["attribute_id"]]["values"][$ii]["family"] = $itemAttr[$ii]["family"];
            $nameArray[$itemAttrType["attribute_id"]]["values"][$ii]["name"] = $itemAttr[$ii]["name"];
        }
    }
    
    /**
     * Generate item metadata link data output by JSON
     * アイテムメタデータのリンク情報の出力をJSON形式で生成する
     * 
     * @param array $itemAttrType Item attribute type data
     *                            アイテムタイプ属性情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|...]
     * @param array $itemAttr Item attribute data
     *                        アイテム属性情報
     *                        array[$ii]["item_id"|"item_no"|"attribute_id"|...]
     * @param array $urlArray Array for JSON encode having item metadata link data
     *                            アイテムメタデータのリンク情報を持つJSONエンコード用の配列
     *                            array["name"|"type"|"values"][$ii]["name"|"url"]
     */
    private function generateUrl($itemAttrType, $itemAttr, &$urlArray)
    {
        $urlArray[$itemAttrType["attribute_id"]] = array();
        $urlArray[$itemAttrType["attribute_id"]]["name"] = $itemAttrType["attribute_name"];
        $urlArray[$itemAttrType["attribute_id"]]["type"] = "url";
        $urlArray[$itemAttrType["attribute_id"]]["values"] = array();
        for($ii = 0; $ii < count($itemAttr); $ii++)
        {
            $urlArray[$itemAttrType["attribute_id"]]["values"][] = array();
            $urlData = explode("|", $itemAttr[$ii]["attribute_value"]);
            $urlArray[$itemAttrType["attribute_id"]]["values"][$ii]["name"] = $urlData[1];
            $urlArray[$itemAttrType["attribute_id"]]["values"][$ii]["url"] = $urlData[0];
        }
    }
    
    /**
     * Generate item metadata biblio data output by JSON
     * アイテムメタデータの書誌情報の出力をJSON形式で生成する
     * 
     * @param array $itemAttrType Item attribute type data
     *                            アイテムタイプ属性情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|...]
     * @param array $itemAttr Item attribute data
     *                        アイテム属性情報
     *                        array[$ii]["item_id"|"item_no"|"attribute_id"|...]
     * @param array $journalArray Array for JSON encode having item metadata biblio data
     *                            アイテムメタデータの書誌情報を持つJSONエンコード用の配列
     *                            array["name"|"type"|"values"][$ii]["name"|"name_english"|"volume"|...]
     */
    private function generateJournal($itemAttrType, $itemAttr, &$journalArray)
    {
        $journalArray[$itemAttrType["attribute_id"]] = array();
        $journalArray[$itemAttrType["attribute_id"]]["name"] = $itemAttrType["attribute_name"];
        $journalArray[$itemAttrType["attribute_id"]]["type"] = "journal";
        $journalArray[$itemAttrType["attribute_id"]]["values"] = array();
        for($ii = 0; $ii < count($itemAttr); $ii++)
        {
            $journalArray[$itemAttrType["attribute_id"]]["values"][] = array();
            $journalArray[$itemAttrType["attribute_id"]]["values"][$ii]["name"] = $itemAttr[$ii]["biblio_name"];
            $journalArray[$itemAttrType["attribute_id"]]["values"][$ii]["name_english"] = $itemAttr[$ii]["biblio_name_english"];
            $journalArray[$itemAttrType["attribute_id"]]["values"][$ii]["volume"] = $itemAttr[$ii]["volume"];
            $journalArray[$itemAttrType["attribute_id"]]["values"][$ii]["issue"] = $itemAttr[$ii]["issue"];
            $journalArray[$itemAttrType["attribute_id"]]["values"][$ii]["start_page"] = $itemAttr[$ii]["start_page"];
            $journalArray[$itemAttrType["attribute_id"]]["values"][$ii]["end_page"] = $itemAttr[$ii]["end_page"];
            $journalArray[$itemAttrType["attribute_id"]]["values"][$ii]["date_of_issued"] = $itemAttr[$ii]["date_of_issued"];
        }
    }
    
    /**
     * Generate item metadata file data output by JSON
     * アイテムメタデータのファイル情報の出力をJSON形式で生成する
     * 
     * @param array $itemAttrType Item attribute type data
     *                            アイテムタイプ属性情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|...]
     * @param array $itemAttr Item attribute data
     *                        アイテム属性情報
     *                        array[$ii]["item_id"|"item_no"|"attribute_id"|...]
     * @param array $fileArray Array for JSON encode having item metadata file data
     *                            アイテムメタデータのファイル情報を持つJSONエンコード用の配列
     *                            array["name"|"type"|"values"][$ii]["name"|"url"]
     */
    private function generateFile($itemAttrType, $itemAttr, &$fileArray)
    {
        $fileArray[$itemAttrType["attribute_id"]] = array();
        $fileArray[$itemAttrType["attribute_id"]]["name"] = $itemAttrType["attribute_name"];
        $fileArray[$itemAttrType["attribute_id"]]["type"] = "file";
        $fileArray[$itemAttrType["attribute_id"]]["values"] = array();
        for($ii = 0; $ii < count($itemAttr); $ii++)
        {
            $fileArray[$itemAttrType["attribute_id"]]["values"][] = array();
            if(isset($itemAttr[$ii]["display_name"]) && strlen($itemAttr[$ii]["display_name"]) > 0)
            {
                $fileArray[$itemAttrType["attribute_id"]]["values"][$ii]["name"] = $itemAttr[$ii]["display_name"];
            }
            else
            {
                $fileArray[$itemAttrType["attribute_id"]]["values"][$ii]["name"] = $itemAttr[$ii]["file_name"];
            }
            $fileArray[$itemAttrType["attribute_id"]]["values"][$ii]["url"] = BASE_URL."/?action=repository_action_common_download".
                                                                              "&item_id=".$itemAttr[$ii]["item_id"].
                                                                              "&item_no=".$itemAttr[$ii]["item_no"].
                                                                              "&attribute_id=".$itemAttr[$ii]["attribute_id"].
                                                                              "&file_no=".$itemAttr[$ii]["file_no"];
        }
    }
    
    /**
     * Generate item metadata thumbnail data output by JSON
     * アイテムメタデータのサムネイル情報の出力をJSON形式で生成する
     * 
     * @param array $itemAttrType Item attribute type data
     *                            アイテムタイプ属性情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|...]
     * @param array $itemAttr Item attribute data
     *                        アイテム属性情報
     *                        array[$ii]["item_id"|"item_no"|"attribute_id"|...]
     * @param array $thumbnailArray Array for JSON encode having item metadata thumbnail data
     *                            アイテムメタデータのサムネイル情報を持つJSONエンコード用の配列
     *                            array["name"|"type"|"values"][$ii]
     */
    private function generateThumbnail($itemAttrType, $itemAttr, &$thumbnailArray)
    {
        $thumbnailArray[$itemAttrType["attribute_id"]] = array();
        $thumbnailArray[$itemAttrType["attribute_id"]]["name"] = $itemAttrType["attribute_name"];
        $thumbnailArray[$itemAttrType["attribute_id"]]["type"] = "thumbnail";
        $thumbnailArray[$itemAttrType["attribute_id"]]["values"] = array();
        for($ii = 0; $ii < count($itemAttr); $ii++)
        {
            $thumbnailArray[$itemAttrType["attribute_id"]]["values"][] = BASE_URL."/?action=repository_action_common_download".
                                                                         "&item_id=".$itemAttr[$ii]["item_id"].
                                                                         "&item_no=".$itemAttr[$ii]["item_no"].
                                                                         "&attribute_id=".$itemAttr[$ii]["attribute_id"].
                                                                         "&file_no=".$itemAttr[$ii]["file_no"].
                                                                         "&img=true";
        }
    }
    
    /**
     * Generate item metadata date data output by JSON
     * アイテムメタデータの日付情報の出力をJSON形式で生成する
     * 
     * @param array $itemAttrType Item attribute type data
     *                            アイテムタイプ属性情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|...]
     * @param array $itemAttr Item attribute data
     *                        アイテム属性情報
     *                        array[$ii]["item_id"|"item_no"|"attribute_id"|...]
     * @param array $dateArray Array for JSON encode having item metadata date data
     *                            アイテムメタデータの日付情報を持つJSONエンコード用の配列
     *                            array["name"|"type"|"values"][$ii]
     */
    private function generateDate($itemAttrType, $itemAttr, &$dateArray)
    {
        $dateArray[$itemAttrType["attribute_id"]] = array();
        $dateArray[$itemAttrType["attribute_id"]]["name"] = $itemAttrType["attribute_name"];
        $dateArray[$itemAttrType["attribute_id"]]["type"] = "date";
        $dateArray[$itemAttrType["attribute_id"]]["values"] = array();
        for($ii = 0; $ii < count($itemAttr); $ii++)
        {
            $dateArray[$itemAttrType["attribute_id"]]["values"][] = $itemAttr[$ii]["attribute_value"];
        }
    }
    
    /**
     * Generate item metadata heading data output by JSON
     * アイテムメタデータの見出し情報の出力をJSON形式で生成する
     * 
     * @param array $itemAttrType Item attribute type data
     *                            アイテムタイプ属性情報
     *                            array["item_type_id"|"attribute_id"|"show_order"|...]
     * @param array $itemAttr Item attribute data
     *                        アイテム属性情報
     *                        array[$ii]["item_id"|"item_no"|"attribute_id"|...]
     * @param array $headingArray Array for JSON encode having item metadata heading data
     *                            アイテムメタデータの見出し情報を持つJSONエンコード用の配列
     *                            array["name"|"type"|"values"][$ii]["heading"|"sub_heading"|"heading_english"|"sub_heading_english"]
     */
    private function generateHeading($itemAttrType, $itemAttr, &$headingArray)
    {
        $headingArray[$itemAttrType["attribute_id"]] = array();
        $headingArray[$itemAttrType["attribute_id"]]["name"] = $itemAttrType["attribute_name"];
        $headingArray[$itemAttrType["attribute_id"]]["type"] = "heading";
        $headingArray[$itemAttrType["attribute_id"]]["values"] = array();
        for($ii = 0; $ii < count($itemAttr); $ii++)
        {
            $headingArray[$itemAttrType["attribute_id"]]["values"][] = array();
            $headingData = explode("|", $itemAttr[$ii]["attribute_value"]);
            $headingArray[$itemAttrType["attribute_id"]]["values"][$ii]["heading"] = $headingData[0];
            $headingArray[$itemAttrType["attribute_id"]]["values"][$ii]["sub_heading"] = $headingData[2];
            $headingArray[$itemAttrType["attribute_id"]]["values"][$ii]["heading_english"] = $headingData[1];
            $headingArray[$itemAttrType["attribute_id"]]["values"][$ii]["sub_heading_english"] = $headingData[3];
        }
    }
    
    /**
     * Generate item index output by JSON
     * アイテム所属インデックス情報の出力をJSON形式で生成する
     * 
     * @param array $itemData Item data 
     *                        アイテム情報
     *                        array["item"|"item_type"|"item_attr_type"|...][$ii]["index"|"index_english"|"language"|...]
     * @param array $indexData Index data belonged by item
     *                         アイテムが所属するインデックス情報
     *                         array["position_index"][$ii]["item_id"|"item_no"|"index_id"|...]
     * @param array $public_index_list Public index list
     *                                 公開インデックス一覧
     *                                 array[$ii]
     * @param array $block_info Block data located WEKO
     *                          WEKOが配置されているブロック情報
     *                          array["page_id"|"block_id"]
     * @param array $indexArray Array for JSON encode having item index
     *                           インデックス情報を持つJSONエンコード用の配列
     *                           array[$ii]
     */
    private function generateIndex($itemData, $indexData, $public_index_list, $block_info, &$indexArray)
    {
        $indexArray["index"] = array();
        
        for($ii = 0; $ii < count($indexData["position_index"]); $ii++)
        {
            if(in_array($indexData["position_index"][$ii]["index_id"], $public_index_list))
            {
                $query = "SELECT index_name, index_name_english ".
                         "FROM ".DATABASE_PREFIX."repository_index ".
                         "WHERE index_id = ?;";
                $params = array();
                $params[] = $indexData["position_index"][$ii]["index_id"];
                $ret = $this->Db->execute($query, $params);
                if($ret === false || count($ret) != 1)
                {
                    continue;
                }
                $indexArray["index"][] = array();
                $indexArray["index"][$ii]["name"] = array();
                $indexArray["index"][$ii]["name"]["ja"] = $ret[0]["index_name"];
                $indexArray["index"][$ii]["name"]["en"] = $ret[0]["index_name_english"];
                
                $indexArray["index"][$ii]["url"] = BASE_URL."/index.php".
                                                   "?action=pages_view_main".
                                                   "&active_action=repository_view_main_item_snippet".
                                                   "&index_id=".$indexData["position_index"][$ii]["index_id"].
                                                   "&page_id=".$block_info["page_id"].
                                                   "&block_id=".$block_info["block_id"];
            }
        }
    }
}
?>