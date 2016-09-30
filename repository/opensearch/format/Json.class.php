<?php
/**
 * Output search result by JSON class
 * 検索結果JSON形式出力クラス
 * 
 */

// --------------------------------------------------------------------
//
// $Id: Json.class.php 69174 2016-06-22 06:43:30Z tatsuya_koyasu $
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
 * Search request parameter process class
 * 検索リクエストパラメータ処理クラス
 */
require_once WEBAPP_DIR."/modules/repository/components/RepositorySearchRequestParameter.class.php";
/**
 * WEKO business factory class
 * WEKO用ビジネスファクトリークラス
 * 
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/WekoBusinessFactory.class.php';

/**
 * Output search result by JSON class
 * 検索結果JSON形式出力クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_OpenSearch_Json extends Repository_Opensearch_FormatAbstract
{
    /**
     * Namespace of opensearch
     * opensearchの名前空間
     *
     * @var string
     */
    const NAMESPACE_OPENSEARCH = "http://a9.com/-/spec/opensearch/1.1/";
    
    /**
     * Generate search result output by JSON
     * 検索結果の出力をJSON形式で生成する
     * 
     * @param array $request Request parameter of search
     *                       検索時のリクエストパラメータ
     *                       array["meta"|"all"|"title"|"creator"|...]
     * @param int $total Total of search result 
     *                   検索結果の合計数
     * @param int $sIdx Start page of search result 
     *                  検索結果の開始ページ
     * @param array $searchResult Search result 
     *                            検索結果
     *                            array[$ii]["item_id"|"item_no"|"uri"]
     *
     * @return string Search result by JSON
     *                JSON形式の検索結果
     */
    public function generateOutputJson($request, $total, $sIdx, $searchResult)
    {
        $jsonArray = array();
        
        // contextタグ出力
        $jsonArray["@context"] = array();
        $jsonArray["@context"]["opensearch"] = self::NAMESPACE_OPENSEARCH;
        
        // idタグ出力
        $jsonArray["@id"] = $this->createSearchRequestUriByUrlEncode($request);
        
        // graphタグ出力
        $jsonArray = $this->generateGraph($request, $total, $sIdx, $searchResult, $jsonArray);
        
        $generateJsonTypeData = BusinessFactory::getFactory()->getBusiness("businessGeneratejsontypedata");
        $json = $generateJsonTypeData->raw_json_encode($jsonArray);
        
        return $json;
    }
    
    /**
     * Generate graph tag output by JSON
     * graphタグの出力をJSON形式で生成する
     * 
     * @param array $request Request parameter of search
     *                       検索時のリクエストパラメータ
     *                       array["meta"|"all"|"title"|"creator"|...]
     * @param int $total Total of search result 
     *                   検索結果の合計数
     * @param int $sIdx Start page of search result 
     *                  検索結果の開始ページ
     * @param array $searchResult Search result 
     *                            検索結果
     *                            array[$ii]["item_id"|"item_no"|"uri"]
     * @param array $jsonArray Array for JSON encode
     *                         JSONエンコード用の配列
     *                         array["@context"|"@id"]["opensearch"]
     *
     * @return array Array for JSON encode having graph tag
     *               graphタグを持つJSONエンコード用の配列
     *               array["@context"|"@id"|"@graph"]...
     */
    private function generateGraph($request, $total, $sIdx, $searchResult, $jsonArray)
    {
        $requestUri = $this->createSearchRequestUriByUrlEncode($request);
        
        $jsonArray["@graph"] = array();
        $jsonArray["@graph"][] = array();
        $jsonArray["@graph"][0]["@id"] = $requestUri;
        $jsonArray["@graph"][0]["@type"] = "channel";
        $searchWord = $this->generateAllSearchWordString($request);
        $jsonArray["@graph"][0]["title"] = "WEKO OpenSearch -".$searchWord;
        $jsonArray["@graph"][0]["link"] = array();
        $jsonArray["@graph"][0]["link"]["@id"] = $requestUri;
        $jsonArray["@graph"][0]["opensearch:totalResults"] = strval($total);
        if($total > 0)
        {
            $jsonArray["@graph"][0]["opensearch:startIndex"] = strval($sIdx);
            $jsonArray["@graph"][0]["opensearch:itemsPerPage"] = strval(count($searchResult));
            $jsonArray["@graph"][0]["items"] = $this->generateItems($searchResult);
        }
        
        return $jsonArray;
    }
    
    /**
     * Generate all search word by string
     * 全ての検索ワードをstring形式で生成する
     * 
     * @param array $request Request parameter of search
     *                       検索時のリクエストパラメータ
     *                       array["meta"|"all"|"title"|"creator"|...]
     *
     * @return string String of all search word
     *                全ての検索ワードの文字列
     */
    private function generateAllSearchWordString($request)
    {
        $searchWord = "";
        foreach($request as $requestParam => $requestValue)
        {
            if($requestParam == RepositorySearchRequestParameter::REQUEST_META || $requestParam == RepositorySearchRequestParameter::REQUEST_ALL
                || $requestParam == RepositorySearchRequestParameter::REQUEST_TITLE || $requestParam == RepositorySearchRequestParameter::REQUEST_CREATOR
                || $requestParam == RepositorySearchRequestParameter::REQUEST_KEYWORD || $requestParam == RepositorySearchRequestParameter::REQUEST_SUBJECT_LIST
                || $requestParam == RepositorySearchRequestParameter::REQUEST_SUBJECT_DESC || $requestParam == RepositorySearchRequestParameter::REQUEST_DESCRIPTION
                || $requestParam == RepositorySearchRequestParameter::REQUEST_PUBLISHER || $requestParam == RepositorySearchRequestParameter::REQUEST_CONTRIBUTOR
                || $requestParam == RepositorySearchRequestParameter::REQUEST_DATE || $requestParam == RepositorySearchRequestParameter::REQUEST_ITEMTYPE_LIST
                || $requestParam == RepositorySearchRequestParameter::REQUEST_TYPE_LIST
                || $requestParam == RepositorySearchRequestParameter::REQUEST_FORMAT || $requestParam == RepositorySearchRequestParameter::REQUEST_ID_LIST
                || $requestParam == RepositorySearchRequestParameter::REQUEST_ID_DESC || $requestParam == RepositorySearchRequestParameter::REQUEST_JTITLE
                || $requestParam == RepositorySearchRequestParameter::REQUEST_PUBYEAR_FROM || $requestParam == RepositorySearchRequestParameter::REQUEST_PUBYEAR_UNTIL
                || $requestParam == RepositorySearchRequestParameter::REQUEST_LANGUAGE || $requestParam == RepositorySearchRequestParameter::REQUEST_AREA
                || $requestParam == RepositorySearchRequestParameter::REQUEST_ERA || $requestParam == RepositorySearchRequestParameter::REQUEST_RIGHT_LIST
                || $requestParam == RepositorySearchRequestParameter::REQUEST_RITHT_DESC || $requestParam == RepositorySearchRequestParameter::REQUEST_TEXTVERSION
                || $requestParam == RepositorySearchRequestParameter::REQUEST_GRANTID || $requestParam == RepositorySearchRequestParameter::REQUEST_GRANTDATE_FROM
                || $requestParam == RepositorySearchRequestParameter::REQUEST_GRANTDATE_UNTIL || $requestParam == RepositorySearchRequestParameter::REQUEST_DEGREENAME
                || $requestParam == RepositorySearchRequestParameter::REQUEST_GRANTOR || $requestParam == RepositorySearchRequestParameter::REQUEST_IDX
                || $requestParam == RepositorySearchRequestParameter::REQUEST_PUBDATE_FROM || $requestParam == RepositorySearchRequestParameter::REQUEST_PUBDATE_UNTIL
                || $requestParam == RepositorySearchRequestParameter::REQUEST_WEKO_ID || $requestParam == RepositorySearchRequestParameter::REQUEST_WEKO_AUTHOR_ID
                || $requestParam == RepositorySearchRequestParameter::REQUEST_INDEX_ID)
            {
                if(isset($requestValue) && strlen($requestValue) > 0){
                    $searchWord .= " ".$requestValue;
                }
            }
        }
        
        return $searchWord;
    }
    
    /**
     * Create search request uri by url encode
     * URLエンコードされた検索のリクエストURIを生成する
     * 
     * @param array $request Request parameter of search
     *                       検索時のリクエストパラメータ
     *                       array["meta"|"all"|"title"|"creator"|...]
     *
     * @return string Request uri by url encode
     *                URLエンコードされたリクエストURI
     */
    private function createSearchRequestUriByUrlEncode($request)
    {
        $requestUri = BASE_URL;
        if(substr($requestUri, -1, 1)!="/"){
            $requestUri .= "/";
        }
        $requestUri .= "?".$_SERVER['QUERY_STRING'];
        $requestUri = htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8');
        
        return $requestUri;
    }
    
    /**
     * Generate items output by JSON
     * 複数のアイテム情報の出力をJSON形式で生成する
     * 
     * @param array $searchResult Search result 
     *                            検索結果
     *                            array[$ii]["item_id"|"item_no"|"uri"]
     *
     * @return array Array for JSON encode having items data
     *               複数のアイテム情報を持つJSONエンコード用の配列
     *               array[$ii]["title"|"title_english"|"language"|...]
     */
    private function generateItems($searchResult)
    {
        $items = array();
        $generateJsonTypeData = BusinessFactory::getFactory()->getBusiness("businessGeneratejsontypedata");
        $this->RepositoryAction->setConfigAuthority();
        
        // 公開インデックス一覧取得
        require_once WEBAPP_DIR. '/modules/repository/components/RepositoryIndexAuthorityManager.class.php';
        $indexAuthorityManager = new RepositoryIndexAuthorityManager($this->Session, $this->Db, $this->getNowDate());
        $public_index_list = $indexAuthorityManager->getPublicIndex(true, $this->RepositoryAction->repository_admin_base, $this->RepositoryAction->repository_admin_room);
        
        // WEKOが配置されているブロックの情報取得
        $block_info = $this->RepositoryAction->getBlockPageId();
        
        for($ii = 0; $ii < count($searchResult); $ii++)
        {
            // アイテム情報取得
            $this->RepositoryAction->getItemData($searchResult[$ii]["item_id"], $searchResult[$ii]["item_no"], $itemData, $errorMsg, false, true);
            // アイテムが所属するインデックスの情報取得
            $this->RepositoryAction->getItemIndexData($searchResult[$ii]["item_id"], $searchResult[$ii]["item_no"], $indexData, $errorMsg);
            $items[] = $generateJsonTypeData->generateItem($itemData, $indexData, $public_index_list, $block_info);
        }
        
        return $items;
    }
}
?>