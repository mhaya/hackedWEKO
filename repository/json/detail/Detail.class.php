<?php

/**
 * Output item detail data by JSON class
 * アイテム詳細情報JSON形式出力クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Detail.class.php 36217 2014-05-26 04:22:11Z satoshi_arata $
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
 * Index rights management common classes
 * インデックス権限管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryIndexAuthorityManager.class.php';
/**
 * WEKO business factory class
 * WEKO用ビジネスファクトリークラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/WekoBusinessFactory.class.php';

/**
 * Output item detail data by JSON class
 * アイテム詳細情報JSON形式出力クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Json_Detail extends RepositoryAction
{
    // 使用コンポーネントを受け取るため
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    var $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
    var $Db = null;
    
    // 添付種別のみ特殊対応
    /**
     * Meta data item name to be included in the file information
     * ファイル情報に含めるメタデータ項目名
     *
     * @var string
     */
    const ATTACHMENT_TYPE = "rm_attachment_type";
    /**
     * Meta data item name to be included in the file information(ｅｎｇｌｉｓｈ)
     * ファイル情報に含めるメタデータ項目名(英語)
     *
     * @var string
     */
    const ATTACHMENT_TYPE_SYNC = "rm_attachment_type_sync";
    
    /**
     * Item id
     * アイテムID
     *
     * @var int
     */
    public $item_id = null;
    
    /**
     * List of item ID (delimiter is comma)
     * アイテムIDのリスト(カンマ区切り)
     *
     * @var string
     */
    public $item_id_list = null;
    
    /**
     * Output the item information in JSON format
     * JSON形式でアイテム情報を出力
     *
     * @access  public
     * @return boolean Result 結果
     */
    function executeApp()
    {
        $this->exitFlag = true;

        // リクエストパラメータのチェック
        if((isset($this->item_id) && isset($this->item_id_list)) ||
            (isset($this->item_id) && preg_match('/^[1-9]\d*$/', $this->item_id) !== 1) ||
            (isset($this->item_id_list) && preg_match('/^[1-9][0-9,]*$/', $this->item_id_list) !== 1)) {
            header("HTTP/1.1 400 Bad Request");
            return false;
        }

        $json_text = "";

        if(isset($this->item_id))  {
            // アイテムの閲覧権限チェック
            $itemAuthorityManager = BusinessFactory::getFactory()->getBusiness("businessItemAuthority");
            $pubflg = $itemAuthorityManager->isItemPermission($this->Session, $this->item_id, 1);
            if($pubflg) {
                // アイテムのデータを取得する
                $itemList = null;
                $error_msg = "";
                $result = $this->getItemData($this->item_id, 1, $itemList, $error_msg);
                if($result) {
                    // アイテム情報をJSON形式で出力する
                    $json_text = $this->createJSONItemData($itemList);
                }
            }
        }
        else if(isset($this->item_id_list)) {
            $json_text = $this->generateItemListDataJson($this->item_id_list);
        }
        
        if(strlen($json_text) > 0)  {
            echo $json_text;
        }

        return true;
    }
    
    /**
     * And outputs the item data in JSON format
     * アイテムデータをJSON形式で出力する
     *
     * @param array $itemList Item information アイテム情報
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
    private function createJSONItemData($itemList)
    {
        // 初期化
        $outputJSONArray = array();
        $outputFileJSONArray = array();
        $outputTypeArray = array();
        
        // アイテムの基本情報をJSON配列に登録する
        $this->setJSONItemBaseData($itemList["item"], $itemList["item_type"], $outputJSONArray);
        
        $this->setJSONItemMetaData($itemList["item_attr_type"], $itemList["item_attr"], 
                                   $outputJSONArray, $outputFileJSONArray, $outputTypeArray);
        // JSON文字列を作成する
        $json_text = $this->createJSONString($outputJSONArray, $outputFileJSONArray, $outputTypeArray);
        return $json_text;
    }    
    
    /**
     * Generate output items data by JSON
     * 複数アイテムのデータの出力をJSON形式で生成する
     * 
     * @param string $itemIdList List of item ID
     *                           アイテムIDのリスト
     *
     * @return string Result of items data by JSON
     *                JSON形式の複数アイテムデータの結果
     */
    private function generateItemListDataJson($itemIdList)
    {
        $generateJsonTypeData = BusinessFactory::getFactory()->getBusiness("businessGeneratejsontypedata");
        // 公開インデックス一覧取得
        $indexAuthorityManager = new RepositoryIndexAuthorityManager($this->Session, $this->Db, $this->TransStartDate);
        $public_index_list = $indexAuthorityManager->getPublicIndex(true, $this->repository_admin_base, $this->repository_admin_room);
        // WEKOが配置されているブロックの情報取得
        $block_info = $this->getBlockPageId();

        // アイテムの閲覧権限チェック
        $itemAuthorityManager = BusinessFactory::getFactory()->getBusiness("businessItemAuthority");
        
        $json_text = "[";
        $json_text_item = "";
        $itemIdArray = explode(",", $itemIdList);
        for($ii = 0; $ii < count($itemIdArray); $ii++) {
            $itemDataJson = null;
            // 空文字の要素は飛ばす 例：「1,,2,3」
            if(strlen($itemIdArray[$ii]) > 0) {
                // 要素がint型になっていない場合があるので、int型にする 例：「000001」
                $itemId = intval($itemIdArray[$ii]);
                if($itemId > 0) {
                    $pubflg = $itemAuthorityManager->isItemPermission($this->Session, $itemId, 1);
                    if($pubflg) {
                        // アイテム情報取得
                        $this->getItemData($itemId, 1, $itemData, $errorMsg, false, true);
                        // アイテムが所属するインデックスの情報取得
                        $this->getItemIndexData($itemId, 1, $indexData, $errorMsg);
                        $itemDataJson = $generateJsonTypeData->generateItem($itemData, $indexData, $public_index_list, $block_info);
                    }
                }
            }
            if(isset($itemDataJson) && is_array($itemDataJson)) {
                if(strlen($json_text_item) > 0) { $json_text_item .= ","; }
                $json_text_item .= $generateJsonTypeData->raw_json_encode($itemDataJson);
            }
        }

        $json_text .= $json_text_item;
        $json_text .= "]";

        return $json_text;
    }
    
    /**
     * To register the item basic information in JSON output array
     * アイテム基本情報をJSON出力配列に登録する
     *
     * @param array $itemInfo Item information アイテム情報
     *                        array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $itemTypeInfo Item type information アイテムタイプ情報
     *                            array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $outputJSONArray Output information 出力情報
     *                               array["wekoid"|"title"|"title_sync"|"lang_dirname"|"item_type"][$ii]
     */
    private function setJSONItemBaseData($itemInfo, $itemTypeInfo, &$outputJSONArray)
    {
        // アイテムの基本情報をJSON出力配列に登録する
        $this->addDataToJSONArray("wekoid", $itemInfo[0]["item_id"], $outputJSONArray);
        $this->addDataToJSONArray("title", $itemInfo[0]["title"], $outputJSONArray);
        $this->addDataToJSONArray("title_sync", $itemInfo[0]["title_english"], $outputJSONArray);
        $this->addDataToJSONArray("lang_dirname", $itemInfo[0]["language"], $outputJSONArray);
        
        // アイテムタイプの情報をJSON出力配列に登録する
        $this->addDataToJSONArray("item_type", $itemTypeInfo[0]["item_type_name"], $outputJSONArray);
    }
    
    /**
     * To register the meta data information of the items in the JSON output array
     * アイテムのメタデータ情報をJSON出力配列に登録する
     *
     * @param array $itemAttrTypeInfo Item type information アイテムタイプ情報
     *                                array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $itemAttrInfo Metadata list メタデータ一覧
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                            array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $outputJSONArray Output information 出力情報
     *                               array[Metadata name]
     * @param array $outputFileJSONArray Output type information タイプ出力情報
     *                                   array[Metadata name]
     * @param array $outputTypeJSONArray Output file information ファイル出力情報
     *                                   array[Metadata name]
     */
    private function setJSONItemMetaData($itemAttrTypeInfo, $itemAttrInfo, &$outputJSONArray, 
                                         &$outputFileJSONArray, &$outputTypeJSONArray)
    {
        // アイテムメタデータを登録
        for($ii = 0; $ii < count($itemAttrTypeInfo); $ii++){
            if($itemAttrTypeInfo[$ii]["hidden"] == 1){
                continue;
            }
            if(!isset($itemAttrInfo[$ii])){
                continue;
            }
            $metadataName = $itemAttrTypeInfo[$ii]["attribute_name"];
            
            if(strcmp($metadataName, self::ATTACHMENT_TYPE) == 0 || strcmp($metadataName, self::ATTACHMENT_TYPE_SYNC) == 0){
                $this->setAttachmentType($metadataName, $itemAttrInfo[$ii], $outputTypeJSONArray);
                continue;
            }
            switch($itemAttrTypeInfo[$ii]["input_type"]){
                case "biblio_info":
                    $this->setJSONItemBiblioInfo($metadataName, $itemAttrInfo[$ii], $outputJSONArray);
                    break;
                case "file":
                case "file_price":
                    $this->setJSONItemFile($metadataName, $itemAttrInfo[$ii], $outputFileJSONArray);
                    break;
                default:
                    $this->setJSONItemAttr($metadataName, $itemAttrInfo[$ii], $itemAttrTypeInfo[$ii], $outputJSONArray);
                    break;
            }
        }
    }
    
    /**
     * To create a JSON string from JSON array
     * JSON配列からJSON文字列を作成する
     *
     * @param array $outputJSONArray Output information 出力情報
     *                               array[Metadata name]
     * @param array $outputFileJSONArray Output type information タイプ出力情報
     *                                   array[Metadata name]
     * @param array $outputTypeJSONArray Output file information ファイル出力情報
     *                                   array[Metadata name]
     * @return string JSON string JSON文字列
     */
    private function createJSONString($outputJSONArray, $outputFileJSONArray, $outputTypeArray)
    {
        // JSON出力配列をJSON文字列として出力する
        $json_text = "{";
        $count = 0;
        foreach($outputJSONArray as $key => $data)
        {
            if($count != 0){
                $json_text .= ",";
            }
            $text = "";
            for($ii = 0; $ii < count($data); $ii++){
                if($ii != 0){
                    $text .= " ";
                }
                $text .= RepositoryOutputFilter::escapeJSON($data[$ii], true);
            }
            // "キー"："値"で追加
            $json_text .= "\"". RepositoryOutputFilter::escapeJSON($key, true). "\":\"" . $text."\"";
            $count++;
        }
        if(count($outputFileJSONArray) != 0){
            $json_text .= ",";
        }
        $count = 0;
        // "キー":{"0":",{"name":"ファイル名","link":"ファイルリンク"}}で追加
        foreach($outputFileJSONArray as $key => $data)
        {
            if($count != 0){
                $json_text .= ",";
            }
            $text = "";
            // Mod back key of file metadata is not exists coron T.Koyasu 2014/09/12 --start--
            // "キー":{
            $json_text .= "\"" . RepositoryOutputFilter::escapeJSON($key, true) ."\":{";
            // Mod back key of file metadata is not exists coron T.Koyasu 2014/09/12 --end--
            for($ii = 0; $ii < count($data); $ii++){
                if($ii != 0){
                    $json_text .= ",";
                }
                // "0":{
                $json_text .= "\"" . $ii . "\":{";
                // "name":"ファイル名",
                $json_text .= "\"name\":\"" . RepositoryOutputFilter::escapeJSON($data[$ii]["name"], true). "\",";
                // "link":"ファイルリンク",
                $json_text .= "\"link\":\"" . $data[$ii]["link"]. "\"";
                // "0":{ を閉じる
                $json_text .= "}";
            }
            // "キー":{ を閉じる
            $json_text .= "}";
            $count++;
        }
        
        if(count($outputTypeArray) > 0){
            $json_text .= ",";
        }
        // "キー": {"0":"プレプリント"}, {"1":"発表資料"}
        $count = 0;
        foreach($outputTypeArray as $key => $data)
        {
            if($count > 0){
                $json_text .= ",";
            }
            $json_text .= "\"". RepositoryOutputFilter::escapeJSON($key, true). "\":{";
            for($ii = 0; $ii < count($data); $ii++)
            {
                if($ii != 0){
                    $json_text .= ",";
                }
                // "No.":"attribute_value"
                $json_text .= "\"". $ii. "\":\"". RepositoryOutputFilter::escapeJSON($data[$ii]["type"], true). "\"";
            }
            $json_text .= "}";
            $count++;
        }
        
        $json_text .= "}";
        return $json_text;
    }
    
    /**
     * Metadata attributes to register the meta-data of biblio_info to JSON output array
     * メタデータ属性がbiblio_infoのメタデータをJSON出力配列に登録する
     *
     * @param string $metadataName Metadata name メタデータ項目名
     * @param array $metadata Metadata メタデータ
     *                        array["attribute_value"|"family"|"name"|"item_id"|"item_no"|"attribute_id"|"file_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"uri"]
     * @param array $outputJSONArray Output information 出力情報
     *                               array[Metadata name]
     */
    private function setJSONItemBiblioInfo($metadataName, $metadata, &$outputJSONArray)
    {
        // メタデータを登録
        foreach($metadata as $data)
        {
            // 雑誌名
            $this->addDataToJSONArray($metadataName."_jounal", $data["biblio_name"], $outputJSONArray);
            // 雑誌名(英)
            $this->addDataToJSONArray($metadataName."_jounal_sync", $data["biblio_name_english"], $outputJSONArray);
            // 刊
            $this->addDataToJSONArray($metadataName."_volume", $data["volume"], $outputJSONArray);
            // 号
            $this->addDataToJSONArray($metadataName."_number", $data["issue"], $outputJSONArray);
            // 開始ページ
            $this->addDataToJSONArray($metadataName."_startingPage", $data["start_page"], $outputJSONArray);
            // 終了ページ
            $this->addDataToJSONArray($metadataName."_endingPage", $data["end_page"], $outputJSONArray);
            // 発行日
            $this->addDataToJSONArray($metadataName."_publicationDate", $data["date_of_issued"], $outputJSONArray);
        }
    }
    
    /**
     * Metadata attributes to register the meta-data of the file or file_price to JSON output array
     * メタデータ属性がfileまたはfile_priceのメタデータをJSON出力配列に登録する
     *
     * @param string $metadataName Metadata name メタデータ項目名
     * @param array $metadata Metadata メタデータ
     *                        array["attribute_value"|"family"|"name"|"item_id"|"item_no"|"attribute_id"|"file_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"uri"]
     * @param array $outputFileJSONArray Output information 出力情報
     *                                   array[Metadata name]
     */
    private function setJSONItemFile($metadataName, $metadata, &$outputFileJSONArray)
    {
        // メタデータを登録
        foreach($metadata as $data)
        {
            $fileArray = array();
            $fileArray["name"] = $data["display_name"];
            $fileArray["link"] = BASE_URL . "/?action=repository_uri&item_id=" . $this->item_id.
                                 "&file_id=" . $data["attribute_id"] . "&file_no=" . $data["file_no"];
            if(isset($outputFileJSONArray[$metadataName])){
                array_push($outputFileJSONArray[$metadataName], $fileArray);
            } else {
                $outputFileJSONArray[$metadataName] = array($fileArray);
            }
        }
    }
    
    /**
     * To add an attachment type information
     * 添付種別情報を追加する
     *
     * @param string $metadataName Metadata name メタデータ項目名
     * @param array $metadata Metadata メタデータ
     *                        array["attribute_value"|"family"|"name"|"item_id"|"item_no"|"attribute_id"|"file_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"uri"]
     * @param array $outputTypeJSONArray Output information 出力情報
     *                                   array[Metadata name]
     */
    private function setAttachmentType($metadataName, $metadata, &$outputTypeJSONArray)
    {
        // regist attachment_type
        foreach($metadata as $data)
        {
            $typeArray = array();
            $typeArray["type"] = $data["attribute_value"];
            
            if(isset($outputTypeJSONArray[$metadataName])){
                array_push($outputTypeJSONArray[$metadataName], $typeArray);
            } else {
                $outputTypeJSONArray[$metadataName] = array($typeArray);
            }
        }
    }
    
    /**
     * To register the meta data in JSON output array
     * メタデータをJSON出力配列に登録する
     *
     * @param string $metadataName Metadata name メタデータ項目名
     * @param array $metadata Metadata メタデータ
     *                        array["attribute_value"|"family"|"name"|"item_id"|"item_no"|"attribute_id"|"file_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"uri"]
     * @param string $metadataInfo Input type 入力タイプ
     * @param array $outputJSONArray Output information 出力情報
     *                               array[Metadata name]
     */
    private function setJSONItemAttr($metadataName, $metadata, $metadataInfo, &$outputJSONArray)
    {
        // メタデータを登録
        foreach($metadata as $data)
        {
            // Mod name delimiter changes to comma T.Koyasu 2014/09/12 --start--
            $value = RepositoryOutputFilter::attributeValue($metadataInfo, $data, 1, RepositoryOutputFilter::NAME_DELIMITER_IS_COMMA);
            // Mod name delimiter changes to comma T.Koyasu 2014/09/12 --end--
            
            // Add remove blank word T.Koyasu 2014/09/16 --start--
            $value = RepositoryOutputFilter::exclusiveReservedWords($value);
            // Add remove blank word T.Koyasu 2014/09/16 --end--
            
            $this->addDataToJSONArray($metadataName, $value, $outputJSONArray);
        }
    }
    
    /**
     * To register the data in the JSON output array
     * JSON出力配列にデータを登録する
     *
     * @param string $key Key name キー名
     * @param string $value Value 値
     * @param array $outputTypeJSONArray Output file information ファイル出力情報
     *                                   array[Metadata name]
     */
    private function addDataToJSONArray($key, $value, &$outputJSONArray)
    {
        if(isset($outputJSONArray[$key])){
            array_push($outputJSONArray[$key], $value);
        } else {
            $outputJSONArray[$key] =  array($value);
        }
    }
}
?>
