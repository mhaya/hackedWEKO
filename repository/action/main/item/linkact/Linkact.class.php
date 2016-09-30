<?php

/**
 * Action class for the inter-item link set
 * アイテム間リンク設定用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Linkact.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/RepositoryAction.class.php';
/**
 * Search process class
 * 検索処理クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/RepositorySearch.class.php';

/**
 * Action class for the inter-item link set
 * アイテム間リンク設定用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Main_Item_Linkact extends RepositoryAction
{
    // リクエストパラメタ
    /**
     * Search keyword
     * 検索キーワード
     *
     * @var string
     */
    var $search_keyword = null;
    /**
     * Keyword change flag
     * キーワードチェンジフラグ
     *
     * @var bool
     */
    var $keychange_Flg = null;
    /**
     * Sort order select index ID
     * ソートオーダーセレクトのインデックスID
     *
     * @var int
     */
    var $sort_order_index = null;
    /**
     * For export print check mark array
     * Export印刷用チェックマーク配列
     *
     * @var array
     */
    var $export_check = null;
    /**
     * Index ID
     * インデックスID
     *
     * @var int
     */
    var $index_id = null;
    /**
     * Index name
     * インデックス名
     *
     * @var string
     */
    var $index_name = null;
    /**
     * item ID for delete link list
     * リンクリストから削除するアイテムのID
     *
     * @var int
     */
    var $del_id = null;
    /**
     * item ID for add link list
     * リンクリストに追加するアイテムのID
     *
     * @var int
     */
    var $add_id = null;
    /**
     * Item relation select
     * アイテムリンクの選択値
     *
     * @var array
     */
    var $item_relation_select = null;
    
    // メンバ変数
    /**
     * Item ID
     * アイテムID
     *
     * @var int
     */
    var $Item_ID = null;
    /**
     * Item number
     * アイテム通番
     *
     * @var int
     */
    var $Item_No = null;

    // オプション用
    /**
     * Process mode
     * 処理モード
     *
     * @var string
     */
    var $save_mode = null; // "next" : リンク設定画面へ (デフォルト)
                              // "add_row" : 属性の数を増やす
                              // "up_row" : 属性を入れ替える (attridx-th属性が上に)
                              // "down_row" : 属性を入れ替える (attridx-th属性が下に)
    /**
     * Processed by the link number or search result number,
     * 処理対象のリンク番号、あるいは検索結果番号
     *
     * @var int
     */
    var $target = null;
    /**
     * Keyword
     * キーワード
     *
     * @var string
     */
    var $keyword = null;
    /**
     * Opening node index ID
     * 開いているノードのインデックスID
     *
     * @var string
     */
    var $opening_ids = null;    // 開いているノードのindex_id文字列(1,2,3,4,…)
    
    // Add join set insert index and set item links 2008/12/17 Y.Nakao --start--
    /**
     * Opening index Id
     * 開いているインデックスのID
     *
     * @var string
     */
    var $OpendIds = null;       // open index ids(delemit is ",")
    /**
     * Checked index ID
     * チェックされたインデックスのID
     *
     * @var string
     */
    var $CheckedIds = null;     // check index ids(delemit is "|")
    /**
     * Checked index name
     * チェックされたインデックスの名前
     *
     * @var string
     */
    var $CheckedNames = null;   // check index names(delemit is "|")
    // Add join set insert index and set item links 2008/12/17 Y.Nakao --end--
    
    // Add simple keyword search A.Suzuki 2010/04/12 --start--
    /**
     * simple search or detail search
     * 簡易検索か詳細検索か
     *
     * @var null
     */
    var $search_type = null;    // 簡易検索/詳細検索を示す
    // Add simple keyword search A.Suzuki 2010/04/12 --end--
    
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws RepositoryException
     */
    function execute()
    {
        $user_error_msg = '';       // GUI表示エラーメッセージ
        try {           
            //アクション初期化処理
            $result = $this->initAction();
            if ( $result === false ) {
                $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 ); //主メッセージとログIDを指定して例外を作成
                $DetailMsg = null;                              //詳細メッセージ文字列作成
                sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
                $exception->setDetailMsg( $DetailMsg );         //詳細メッセージ設定
                $this->failTrans();                             //トランザクション失敗を設定(ROLLBACK)
                $user_error_msg = 'initで既に・・・';
                throw $exception;
            }           
            if ($this->save_mode == null) {
                $this->save_mode = 'default';
            }
            // セッション情報取得
            $link = $this->Session->getParameter("link");           // リンク情報, リンクアイテムのレコードをそのまま保存したものである。
            $link_search = $this->Session->getParameter("link_search"); // カレントの検索結果情報, 検索アイテムのレコードをそのまま保存したものである。
            
            // Add join set insert index and set item links 2008/12/17 Y.Nakao --start--
            // set session to check index info
            $indice = array();
            if( $this->CheckedIds != null && $this->CheckedIds != '' ){
                $checked_ids = explode('|', $this->CheckedIds);
                $checked_names = explode('|', $this->CheckedNames);
                for($ii=0; $ii<count($checked_ids); $ii++) {
                    array_push($indice, array(
                            'index_id' => $checked_ids[$ii],
                            'index_name' => $checked_names[$ii])
                            );
                }
            }
            // Add specialized support for open.repo "auto affiliation in private tree" Y.Nakao 2013/06/21 --start--
            $indice = $this->addPrivateTreeInPositionIndex($indice);
            $this->Session->setParameter("indice", $indice);
            // Add specialized support for open.repo "auto affiliation in private tree" Y.Nakao 2013/06/21 --end--
            // Add join set insert index and set item links 2008/12/17 Y.Nakao --end--
            
            // ------------------------------------------------------------
            // オプション個別処理
            // ------------------------------------------------------------
            switch($this->save_mode) {
            // キーワード検索
            case "keyword_search":
                // Add relation 2008/10/16 A.Suzuki --start--
                // 関係性をセッションに保存する
                $link = $this->Session->getParameter("link");
                $relation = '';
                
                for($ii=0; $ii<count($link); $ii++){
                    if($this->item_relation_select[$ii]!=' ') {
                        $relation = $this->item_relation_select[$ii];
                    }else{
                        $relation = '';
                    }
                    $link[$ii]['relation'] = $relation;
                }
                $this->Session->setParameter("link", $link);
                // Add relation 2008/10/16 A.Suzuki --end--
                // 前回の検索結果を削除
                $this->Session->removeParameter("link_search");
                // キーワードをセッションに保存し直す
                $this->Session->setParameter("link_searchkeyword", $this->keyword);
                
                // Add simple keyword search A.Suzuki 2010/04/12 --start--
                $this->Session->setParameter("link_searchtype", $this->search_type);
                $this->Session->removeParameter("search_index_id_link");
                // Add simple keyword search A.Suzuki 2010/04/12 --end--
                
                // Fix change file download action 2013/5/9 Y.Nakao --start--
                $link_search = $this->searchLinkItem($this->keyword, "");
                // Fix change file download action 2013/5/9 Y.Nakao --end--
                
                if(count($link_search) == 0){
                    $index_name = $this->getIndexName($this->target, $this->Session->getParameter("_lang"));
                    $this->Session->setParameter("link_search_no_item", $index_name);
                    $this->failTrans();
                    return 'error';
                }
                $this->Session->setParameter("link_search", $link_search);
                break;
            // インデックス検索
            case "index_search":
                // Add relation 2008/10/16 A.Suzuki --start--
                // 関係性をセッションに保存する
                $link = $this->Session->getParameter("link");
                $relation = '';
                
                for($ii=0; $ii<count($link); $ii++){
                    if($this->item_relation_select[$ii]!=' ') {
                        $relation = $this->item_relation_select[$ii];
                    }else{
                        $relation = '';
                    }
                    $link[$ii]['relation'] = $relation;
                }
                $this->Session->setParameter("link", $link);
                // Add relation 2008/10/16 A.Suzuki --end--
                
                // 前回の検索結果を削除
                $this->Session->removeParameter("link_search");
                // インデックスツリー開閉情報をSessionへ設定             
                $arOpenIndexId = array();
                $arOpenIndexId = explode(",", $this->opening_ids);
                $this->Session->removeParameter("open_node_index_id_link");
                $this->Session->setParameter("open_node_index_id_link", $arOpenIndexId);
                
                // Fix change file download action 2013/5/9 Y.Nakao --start--
                $link_search = $this->searchLinkItem("", $this->target);
                // Fix change file download action 2013/5/9 Y.Nakao --end--
                
                if(count($link_search) == 0){
            
                    $index_name = $this->getIndexName($this->target, $this->Session->getParameter("_lang"));
                    $this->Session->setParameter("link_search_no_item", $index_name);
                    $this->failTrans();
                    return 'error';
                }
                $this->Session->setParameter("link_search", $link_search);  
                break;
            // リンク対象削除
            case "delete_link":
                // Add relation 2008/10/16 A.Suzuki --start--
                // 関係性をセッションに保存する
                $link = $this->Session->getParameter("link");
                $relation = '';
                
                for($ii=0; $ii<count($link); $ii++){
                    if($this->item_relation_select[$ii]!=' ') {
                        $relation = $this->item_relation_select[$ii];
                    }else{
                        $relation = '';
                    }
                    $link[$ii]['relation'] = $relation;
                }
                $this->Session->setParameter("link", $link);
                // Add relation 2008/10/16 A.Suzuki --end--
                
                // リンクリスト更新, 一件削除
                $idx = (int)($this->target);
                $link_new = array();
                for($ii=0;$ii<count($link); $ii++) {
                    if($ii != $idx ) {
                        array_push($link_new, $link[$ii]);
                    }
                }
                $this->Session->setParameter("link", $link_new);
                break;
            // リンク対象追加
            case "add_link":
                // Add relation 2008/10/16 A.Suzuki --start--
                // 関係性をセッションに保存する
                $link = $this->Session->getParameter("link");
                $relation = '';
                
                for($ii=0; $ii<count($link); $ii++){
                    if($this->item_relation_select[$ii]!=' ') {
                        $relation = $this->item_relation_select[$ii];
                    }else{
                        $relation = '';
                    }
                    $link[$ii]['relation'] = $relation;
                }
                $this->Session->setParameter("link", $link);
                // Add relation 2008/10/16 A.Suzuki --end--
                
                // リンクリスト更新, 一件追加
                $idx = (int)($this->target);
//              echo "add link : " .$idx."<br>";                
                // リンクリスト内の重複検査
                $IsExist = false;
                for($ii=0;$ii<count($link); $ii++) {
                    if($link[$ii]['item_id'] == $link_search[$idx]['item_id'] ) {
                        $IsExist = true;
                        break;
                    }
                }
//              echo "add link2 : " .$idx. ', '. $IsExist . ', ' . $link_search[$idx]['item_id'] ."<br>";
                // 重複無しの場合リンクリストを更新して再保存
                if(!$IsExist) {
                    array_push($link, $link_search[$idx]);
                    $this->Session->setParameter("link", $link);
                }
                break;              
            default:
                // ページ切替がある場合厄介・・・
                break;
            }
            
            // アクション終了処理
            $result = $this->exitAction();  // トランザクションが成功していればCOMMITされる
            if ( $result == false ){
                //print "終了処理失敗";
            }
            // リンク画面へ
            $this->Session->removeParameter("error_msg");
            $this->finalize();
//          $this->Session->setParameter("error_msg", $this->save_mode .' '.$this->target);
            return 'success';
        } catch ( RepositoryException $Exception) {
            //エラーログ出力
            $this->logFile(
                "SampleAction",                 //クラス名
                "execute",                      //メソッド名
                $Exception->getCode(),          //ログID
                $Exception->getMessage(),       //主メッセージ
                $Exception->getDetailMsg() );   //詳細メッセージ           
            //アクション終了処理
            $this->failTrans();                 //トランザクションが失敗していればROLLBACKされる        
            //異常終了
            $this->Session->setParameter("error_msg", $user_error_msg);
            return "error";
        }           
    }
    
    /**
     * Return index name by index_id
     * インデックスIDからインデックス名を検索して返す
     *
     * @param int $index_id index ID インデックスID
     * @param string $lang language 表示言語
     * @return string $index_name index name インデックス名
     */
    function getIndexName($index_id, $lang){
        $query = "SELECT index_name, index_name_english".
                 " FROM ".DATABASE_PREFIX."repository_index ".
                 " WHERE index_id = ?;";
        $params = array();
        $params[] = $index_id;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            return false;
        }
        
        if(count($result) > 0){
        	if($lang == "japanese"){
            	$index_name = $result[0]["index_name"];
            	if($index_name == ""){
                	$index_name = $result[0]["index_name_english"];
            	}
        	} else {
            	$index_name = $result[0]["index_name_english"];
            	if($index_name == ""){
                	$index_name = $result[0]["index_name"];
            	}
        	}
        } else {
        	$index_name = null;
        }
        
        return $index_name;
    }
    
    // Fix change file download action 2013/5/9 Y.Nakao --start--
    /**
     * search link item
     * リンクされたアイテムを探す
     *
     * @param string $keyword keyword キーワード
     * @param int $indexId index ID インデックスID
     * @return array linked item data リンクされたアイテム情報配列
     *                array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                     ["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    private function searchLinkItem($keyword, $indexId)
    {
        $link_search = array();
        
        // search
        $repositorySearch = new RepositorySearch();
        $repositorySearch->Db = $this->Db;
        $repositorySearch->Session = $this->Session;
        $repositorySearch->listResords = "all";
        if(strlen($keyword) > 0)
        {
            $keyword = urldecode($keyword);
            $keyword = trim(mb_convert_encoding($keyword, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS"));
            $keyword = RepositoryOutputFilter::string($keyword);
            $keyword = preg_replace("/[\s,]+|　/", " ", $keyword);
            $repositorySearch->search_term[repositorySearch::REQUEST_ALL] = $keyword;
            $repositorySearch->index_id = "";
        }
        else if(strlen($indexId) > 0 && is_numeric($indexId))
        {
            $repositorySearch->keyword = "";
            $repositorySearch->index_id = $indexId;
        }
        else
        {
            return array();
        }
        $searchResult = $repositorySearch->search();
        for($ii=0; $ii<count($searchResult); $ii++)
        {
            // 検索結果のアイテムを取得
            $Result_List;       // DBから取得したレコードの集合
            $Error_Msg;         // エラーメッセージ
            $link_item = $this->getItemTableData(
                                    $searchResult[$ii]["item_id"],
                                    $searchResult[$ii]["item_no"],
                                    $Result_List, $Error_Msg);
            if( $link_item === false) {
                $this->Session->setParameter("link_search_error", $this->keyword);
                $this->failTrans();
                return 'error';     
            }
            if(count($Result_List["item"]) > 0){
                // 検索結果のアイテムタイプを取得
                $query = "SELECT * ".
                         "FROM ". DATABASE_PREFIX ."repository_item_type ".     // アイテムタイプ
                         "WHERE item_type_id = ? AND ".
                         "is_delete = ?; ";                 // 削除フラグ
                $params = null;
                $params[] = $Result_List['item'][0]['item_type_id'];
                $params[] = 0;
                //　SELECT実行
                $link_item_type = $this->Db->execute($query, $params);
                if($link_item_type === false){
                    $this->failTrans();
                    return 'error';      
                }
                // アイテムタイプ名を付加して保存
                $Result_List['item'][0]['item_type_name'] = $link_item_type[0]['item_type_name'];
                array_push($link_search, $Result_List['item'][0]);
            }
        }
        
        return $link_search;
    }
    // Fix change file download action 2013/5/9 Y.Nakao --end--
    
}
?>
