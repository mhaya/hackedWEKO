<?php

/**
 * Opensearch output action class
 * Opensearch出力アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Opensearch.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Search common classes
 * 検索共通クラス
 */
require_once WEBAPP_DIR."/modules/repository/components/RepositorySearch.class.php";
/**
 * WEKO business factory class
 * WEKO用ビジネスファクトリークラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/WekoBusinessFactory.class.php';

/**
 * Opensearch output action class
 * Opensearch出力アクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Opensearch extends RepositorySearch
{
    ///// const /////
    /**
     * Simple display
     * 簡易表示
     * 
     * @var string
     */
    const DATA_FILTER_SIMPLE = "simple";
    /**
     * Detail display
     * 詳細表示
     * 
     * @var string
     */
    const DATA_FILTER_DETAIL = "detail";
    
    /**
     * Output prefix flag
     * prefix出力フラグ
     * 
     * @var string
     */
    const IS_OUTPUT_PREFIX = "true";
    
    /**
     * log_term request
     * log_termリクエスト
     * 
     * @var string
     */
    const REQUEST_LOG_TERM = "log_term";
    /**
     * dataFilter request
     * dataFilterリクエスト
     * 
     * @var string
     */
    const REQUEST_DATA_FILTER = "dataFilter";
    /**
     * prefix request
     * prefixリクエスト
     * 
     * @var string
     */
    const REQUEST_PREFIX = "prefix";
    
    /**
     * format request
     * formatリクエスト
     * 
     * @var string
     */
    const REQUEST_OUTPUT_FORMAT="format";
    /**
     * recursive request
     * recursiveリクエスト
     * 
     * @var string
     */
    const REQUEST_RECURSIVE="recursive";
    // add 2015/12/1 mhaya start ---
    /**
     * count request
     *
     * @var int
     */
    const REQUEST_COUNT = "count";
    // add 2015/12/1 mhaya end ---
    /**
     * Format of output by JSON
     * JSON形式出力用のフォーマット
     *
     * @var string
     */
    const FORMAT_JSON = "json";
    
    /**
     * outuput type
     * when isset this parameter, return "text"
     * 出力形式
     * 
     * @var string
     */
    public $outType = null;
    
    /**
     * log data collection period
     * ログ集計年月
     *
     * @var string YYYY-MM-DD
     */
    public $log_term = null;
    
    /**
     * Metadata output content
     * メタデータ出力内容
     *
     * @var string  simple:output list metadata
     *              detail：output all metadata (default)
     */
    public $dataFilter = null;
    
    /**
     * output PrefixId 
     * prefix出力フラグ
     *
     * @var boolean true: output IDServer prefixId
     */
    public $prefix = null;
    
    // Add index recursive search. 2014/08/12 Y.Nakao --start--
    /**
     * recursive index seach
     * インデックス再帰検索フラグ
     *
     * @var int 1: index search including child indices
     */
    public $recursive=null;
    // Add index recursive search. 2014/08/12 Y.Nakao --end--
    
    /**
     * execute
     * Opensearch実行
     */
    public function execute()
    {
        $this->initialize();
        
        // set start proc time
        $sTime = microtime(true);
        
        // validate request parameter.
        $this->setRequestParameter();
        
        // Add index recursive search. 2014/08/12 Y.Nakao --start--
        // When recursive and index not set, recursive is not run.再帰検索でもインデックス未指定の場合は再帰検索をしない
        if($this->recursive == 1 && isset($this->search_term[self::REQUEST_IDX]) && strlen($this->search_term[self::REQUEST_IDX]) > 0)
        {
            // Set request parameter for opensearch inimitableness.
            if(!is_null($this->dataFilter)){
                $this->search_term[self::REQUEST_DATA_FILTER]=$this->dataFilter;
            }
            if(!is_null($this->log_term)){
                $this->search_term[self::REQUEST_LOG_TERM]=$this->log_term;
            }
            if(!is_null($this->prefix)){
                $this->search_term[self::REQUEST_PREFIX]=$this->prefix;
            }
            
            // get all child index. サブインデックス全取得/閲覧権限などは判定しない（リダイレクト先で考慮する）
            require_once WEBAPP_DIR. '/modules/repository/action/edit/tree/Tree.class.php';
            $tree_action = new Repository_Action_Edit_Tree();
            $tree_action->Session = $this->Session;
            $tree_action->Db = $this->Db;
            $tree_action->TransStartDate = $this->RepositoryAction->TransStartDate;
            
            $idxList = explode(",", $this->search_term[self::REQUEST_IDX]);
            $indices = $idxList;
            foreach($indices as $idx)
            {
                $tree_action->getAllChildIndexID($idx, $idxList);
                // get unique index id. 
                // 指定されたインデックスによりサブインデックスが重複した場合を考慮する
                $idxList = array_unique($idxList);
            }
            
            // Bug fix remove index_id=0 T.Koyasu 2014/08/25 --start--
            $zeroElm = array_search(0, $idxList);
            if($zeroElm !== false){
                array_splice($idxList, $zeroElm, 1);
            }
            // Bug fix remove index_id=0 T.Koyasu 2014/08/25 --end--
            
            $this->search_term[self::REQUEST_IDX] = implode(",", $idxList);
            $redirectUrl = BASE_URL."/index.php?action=repository_opensearch&".$this->getRequestQuery();
            
            // redirect index recursive search.
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$redirectUrl);
            exit();
        }
        // Add index recursive search. 2014/08/12 Y.Nakao --end--
        
        if(isset($this->_request["affiliationid"]) && strlen($this->_request["affiliationid"]) > 0)
        {
        	$this->search_term['affiliationid'] = $this->_request["affiliationid"];
        }
        
        $this->validateRequestParameter();
        
        // switch format.
        $outputXml = "";
        $outputJson = "";
        $redirectUrl = "";
        // Fix count paramter bug 2015/12/1 mhaya ---start
        $this->list_view_num = isset($this->_request[self::REQUEST_COUNT]) ? $this->_request[self::REQUEST_COUNT] : $this->list_view_num;
        // Fix count paramter bug 2015/12/1 mhaya ---end

	switch($this->format)
        {
            case RepositorySearchRequestParameter::FORMAT_DESCRIPTION:
                // description
                $outputXml = $this->getDescription();
                break;
            case RepositorySearchRequestParameter::FORMAT_RSS:
                // output RSS
                require_once WEBAPP_DIR.'/modules/repository/opensearch/format/Rss.class.php';
                $outputClass = new Repository_OpenSearch_Rss($this->Session, $this->Db);
                $searchResult = $this->search();
                $requestParam = $this->getRequestParameter();
                $requestParam[self::REQUEST_LOG_TERM] = $this->log_term;
                $requestParam[self::REQUEST_DATA_FILTER] = $this->dataFilter;
                $requestParam[self::REQUEST_PREFIX] = $this->prefix;
                $outputXml = $outputClass->outputXml(   $requestParam, 
                                                        $this->getTotal(), 
                                                        $this->getStartIndex(), 
                                                        $searchResult);
                break;
            case RepositorySearchRequestParameter::FORMAT_ATOM:
                // output ATOM
                require_once WEBAPP_DIR.'/modules/repository/opensearch/format/Atom.class.php';
                /* start Repository_OpenSearch_Atomにblockidを渡す mhaya  */
                //$outputClass = new Repository_OpenSearch_Atom($this->Session, $this->Db);
                $blockid = $this->RepositoryAction->getBlockPageId();
                $outputClass = new Repository_OpenSearch_Atom($this->Session, $this->Db,$blockid);
                /* end mhaya*/

                $searchResult = $this->search();
                $requestParam = $this->getRequestParameter();
                $requestParam[self::REQUEST_LOG_TERM] = $this->log_term;
                $requestParam[self::REQUEST_DATA_FILTER] = $this->dataFilter;
                $outputXml = $outputClass->outputXml(   $requestParam, 
                                                        $this->getTotal(), 
                                                        $this->getStartIndex(), 
                                                        $searchResult);
                break;
            case RepositorySearchRequestParameter::FORMAT_DUBLIN_CORE:
            case RepositorySearchRequestParameter::FORMAT_JUNII2:
            case RepositorySearchRequestParameter::FORMAT_LOM:
                // output RDF
                require_once WEBAPP_DIR.'/modules/repository/opensearch/format/Rdf.class.php';
                $outputClass = new Repository_OpenSearch_Rdf($this->Session, $this->Db);
                $outputClass->setFormat($this->format);
                $searchResult = $this->search();
                $requestParam = $this->getRequestParameter();
                $requestParam[self::REQUEST_DATA_FILTER] = $this->dataFilter; // TODO
                $outputXml = $outputClass->outputXml(   $requestParam,
                                                        $this->getTotal(), 
                                                        $this->getStartIndex(), 
                                                        $searchResult);
                break;
            case self::FORMAT_JSON:
                // output JSON
                require_once WEBAPP_DIR.'/modules/repository/opensearch/format/Json.class.php';
                $outputClass = new Repository_OpenSearch_Json($this->Session, $this->Db);
                $searchResult = $this->search();
                $requestParam = $this->getRequestParameter();
                $outputJson = $outputClass->generateOutputJson(   $requestParam, 
                                                                  $this->getTotal(), 
                                                                  $this->getStartIndex(), 
                                                                  $searchResult);
                break;
            default:

                // Update suppleContentsEntry Y.Yamazawa --start-- 2015/03/20 --start--
                if((isset($this->search_term[self::REQUEST_WEKO_ID]) && strlen($this->search_term[self::REQUEST_WEKO_ID]) > 0)
                    || (isset($this->search_term[self::REQUEST_ITEM_IDS]) && strlen($this->search_term[self::REQUEST_ITEM_IDS]) > 0))
                {
                    // weko_idに該当するアイテムを検索
                    $result = $this->search();
                    if(count($result) == 1){
                        // 該当あり -> 詳細画面へリダイレクト
                        $redirectUrl = $result[0]["uri"];
                        break;
                    }
                }
                // Update suppleContentsEntry Y.Yamazawa --end-- 2015/03/20 --end--

                // redirect to snippet
                if(strlen($redirectUrl) == 0)
                {
                    $redirectUrl = BASE_URL;
                    if($this->Session->getParameter('_smartphone_flag') == _ON)
                    {
                        $redirectUrl .= "/index.php?action=repository_view_main_item_snippet";
                    }
                    else
                    {
                        $redirectUrl .= "/index.php?action=pages_view_main".
                                         "&active_action=repository_view_main_item_snippet";
                    }
                    $redirectUrl .= "&".$this->getRequestQuery();
                    
                    // get block_id and page_id
                    $block_info = $this->RepositoryAction->getBlockPageId();
                    $redirectUrl .= "&page_id=". $block_info["page_id"].
                                    "&block_id=". $block_info["block_id"];
                }
                break;
        }
        
        if(strlen($this->outType) && strlen($outputXml) > 0)
        {
            return $outputXml;
        }
        if(strlen($outputXml) > 0)
        {
            // ヘッダ出力
            header("Content-Type: text/xml; charset=utf-8");    // レスポンスのContent-Typeを明示的に指定する("text/xml")
            // フィード出力
            $outputXml = mb_convert_encoding($outputXml, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS");
            echo $outputXml;
        }
        else if(strlen($outputJson) > 0)
        {
            echo $outputJson;
        }
        else if(strlen($redirectUrl) > 0)
        {
            // redirect
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$redirectUrl);
        }
        
        $this->outputProcTime("OpenSearch検索結果表示",$sTime, microtime(true));
        
        exit();
    }
    
    /**
     * Initialize
     * 初期化
     *
     */
    private function initialize()
    {
        // Fix OpenSearch 2013.12.16 Y.Nakao --start--
        $this->RepositoryAction->Db = $this->Db;
        $this->RepositoryAction->dbAccess = $this->dbAccess;
        $this->RepositoryAction->Session = $this->Session;
        $DATE = new Date();
        $this->RepositoryAction->TransStartDate = $DATE->getDate().".000";
        // Fix OpenSearch 2013.12.16 Y.Nakao --start--
        
        // Fix 別の不具合にて同様の修正あり
        $this->RepositoryAction->setConfigAuthority();
        // Fix 別の不具合にて同様の修正あり
        
        WekoBusinessFactory::initialize($this->Session, $this->Db, $DATE->getDate().".000");
    }
    
    /**
     * validate for opensearch request parameter
     * リクエストパラメータ精査
     *
     */
    private function validateRequestParameter()
    {
        $this->outType = RepositoryOutputFilter::string($this->outType);
        
        $this->log_term = RepositoryOutputFilter::date($this->log_term);
        
        $this->dataFilter = RepositoryOutputFilter::string($this->dataFilter);
        if($this->dataFilter != self::DATA_FILTER_SIMPLE)
        {
            $this->dataFilter = self::DATA_FILTER_DETAIL;
        }
        
        $this->prefix = RepositoryOutputFilter::string($this->prefix);
        if($this->prefix == self::IS_OUTPUT_PREFIX)
        {
            $this->prefix = true;
        }
        else
        {
            $this->prefix = false;
        }
    }
    
    /**
     * Output OpenSearch description document
     * OpenSearch description document(記述文書)を出力する
     * 
     * @return string XML string XML文字列
     */
    public function getDescription()
    {
        $xml = "";
        $LF = "\n";
        
        // 表示情報取得
        // モジュール名取得
        $query = "SELECT `param_value` ".
                 "FROM `". DATABASE_PREFIX ."repository_parameter` ".
                 "WHERE `param_name` = 'prvd_Identify_repositoryName';";
        $ret = $this->Db->execute($query);
        if ($ret === false) {
            return "";
        }
        if($ret[0]['param_value'] == ""){
            $ret[0]['param_value'] = "WEKO";
        }
        $repositoryName = $this->RepositoryAction->forXmlChange($ret[0]['param_value']);
        
        
        // メールアドレス取得
        $query = "SELECT `param_value` ".
                 "FROM `". DATABASE_PREFIX ."repository_parameter` ".
                 "WHERE `param_name` = 'prvd_Identify_adminEmail';";
        $ret = $this->Db->execute($query);
        if ($ret === false) {
            return '';
        }
        $adminEmail = $this->RepositoryAction->forXmlChange($ret[0]['param_value']);
        
        // xmlヘッダ出力
        $xml =  '<?xml version="1.0" encoding="UTF-8" ?>'.$LF.
                '<OpenSearchDescription'.$LF.
                '   xmlns="http://a9.com/-/spec/opensearch/1.1/"'.$LF.
                '   xmlns:wekolog="'.BASE_URL.'/opensearch/1.0/">'.$LF;
        
        // 情報出力
        $xml .= '   <ShortName>'.$repositoryName.'</ShortName>'.$LF;        // タイトル
        $xml .= '   <Description>'.$repositoryName." item search".'</Description>'.$LF; // 説明文
        if($adminEmail != ""){
            $xml .= '   <Contact>'.$adminEmail.'</Contact>'.$LF;    // 管理者メールアドレス
        }
        
        // define request url
        // format html
        $requestUrl = BASE_URL.'/?action=repository_opensearch'.
                      '&'.self::REQUEST_META.'={allmetadataSearchTerms?}'.
                      '&'.self::REQUEST_ALL.'={alldataSearchTerms?}'.
                      '&'.self::REQUEST_TITLE.'={titleSearchTerms?}'.
                      '&'.self::REQUEST_CREATOR.'={creatorSearchTerms?}'.
                      '&'.self::REQUEST_KEYWORD.'={keywordSearchTerms?}'.
                      '&'.self::REQUEST_SUBJECT_LIST.'={subjectListSearchTerms?}'.
                      '&'.self::REQUEST_SUBJECT_DESC.'={subjectDescriptionSearchTerms?}'.
                      '&'.self::REQUEST_DESCRIPTION.'={DescriptionSearchTerms?}'.
                      '&'.self::REQUEST_PUBLISHER.'={publisherSearchTerms?}'.
                      '&'.self::REQUEST_CONTRIBUTOR.'={contributorSearchTerms?}'.
                      '&'.self::REQUEST_DATE.'={dateSearchTerms?}'.
                      '&'.self::REQUEST_TYPE_LIST.'={typeListSearchTerms?}'.
                      '&'.self::REQUEST_FORMAT.'={formatSearchTerms?}'.
                      '&'.self::REQUEST_ID_LIST.'={idListSearchTerms?}'.
                      '&'.self::REQUEST_ID_DESC.'={idDescriptionSearchTerms?}'.
                      '&'.self::REQUEST_JTITLE.'={journalTitleSearchTerms?}'.
                      '&'.self::REQUEST_PUBYEAR_FROM.'={publishYearFrom?}'.
                      '&'.self::REQUEST_PUBYEAR_UNTIL.'={publishYearUntil?}'.
                      '&'.self::REQUEST_LANGUAGE.'={languageSearchTerms?}'.
                      '&'.self::REQUEST_ERA.'={eraSearchTerms?}'.
                      '&'.self::REQUEST_RIGHT_LIST.'={rightsListSearchTerms?}'.
                      '&'.self::REQUEST_RITHT_DESC.'={rightsDescriptionSearchTerms?}'.
                      '&'.self::REQUEST_TEXTVERSION.'={textversinSearchTerms?}'.
                      '&'.self::REQUEST_GRANTID.'={grantIdSearchTerms?}'.
                      '&'.self::REQUEST_GRANTDATE_FROM.'={dateOfGrantedFrom?}'.
                      '&'.self::REQUEST_GRANTDATE_UNTIL.'={dateOfGrantedUntil?}'.
                      '&'.self::REQUEST_DEGREENAME.'={degreeNameSearchTerms?}'.
                      '&'.self::REQUEST_GRANTOR.'={gratorSearchTerms?}'.
                      '&'.self::REQUEST_IDX.'={wekolog:index?}'.
                      '&'.self::REQUEST_PAGENO.'={startPage?}'.
                      '&'.self::REQUEST_COUNT.'={count?}'.
                      '&'.self::REQUEST_SHOWORDER.'={wekolog:sortorder?}'.
                      '&'.self::REQUEST_LIST_RECORDS.'={all?}';
        //              '&weko_id={wekolog.wekoId?}'.
        //              '&andor={and|or}'.
        //              '&item_ids={wekolog.itemIds?}'.
        //              '&lang={wekolog.lang?}'.
        $xml .= '   <Url type="text/html" template="'.$this->RepositoryAction->forXmlChange($requestUrl).'"/>'.$LF;
        $xml .= '   <Url type="application/rss+xml" template="'.$this->RepositoryAction->forXmlChange($requestUrl.'&format=rss').'"/>'.$LF;
        // format rss for get prefix id ( weko use at supple weko)
        //$requestUrl = BASE_URL.'/?action=repository_opensearch'.
        //              '&prefix=true'.
        //              '&format=rss';
        //$xml .= '   <Url type="application/rss+xml" template="'.$this->RepositoryAction->forXmlChange($requestUrl).'"/>'.$LF;
        // format ATOM
        $xml .= '   <Url type="application/atom+xml" template="'.$this->RepositoryAction->forXmlChange($requestUrl.'&format=atom').'"/>'.$LF;
        // format RDF
        // for dublin core
        $xml .= '   <Url type="application/atom+xml" template="'.$this->RepositoryAction->forXmlChange($requestUrl.'&format=oai_dc').'"/>'.$LF;
        // for junii2
        $xml .= '   <Url type="application/atom+xml" template="'.$this->RepositoryAction->forXmlChange($requestUrl.'&format=junii2').'"/>'.$LF;
        // for lom
        $xml .= '   <Url type="application/atom+xml" template="'.$this->RepositoryAction->forXmlChange($requestUrl.'&format=oai_lom').'"/>'.$LF;
        
        $xml .= '</OpenSearchDescription>';
        
        return $xml;
    }
}
?>
