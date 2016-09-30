<?php

/**
 * Item bulk delete action class by keyword search
 * キーワード検索によるアイテム一括削除アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Doi.class.php 49641 2015-03-09 07:02:34Z tomohiro_ichikawa $
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
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Handle management common classes
 * ハンドル管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
/**
 * Common classes for creating and updating the search table that holds the metadata and file contents of each item to search speed improvement
 * 検索速度向上のためアイテム毎のメタデータおよびファイル内容を保持する検索テーブルの作成・更新を行う共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositorySearchTableProcessing.class.php';
/**
 * Action base class for WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/common/WekoAction.class.php';

/**
 * Item bulk delete action class by keyword search
 * キーワード検索によるアイテム一括削除アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Item_Searchdelete extends WekoAction
{
    // *********************
    // リクエストパラメータ
    // *********************
    /**
     * Check the state of the check box
     * チェックボックスのチェック状態
     *
     * @var unknown_type
     */
    public $delete_search_items = null;
    
    /**
     * Search Delete tabs
     * 検索削除のタブNo
     *
     * @var int
     */
    const SEARCH_DELETE_TAB_NUMBER = 2;
    
    /**
     * Enter search keywords
     * 入力した検索キーワード
     *
     * @var string
     */
    public $searchkeyword = null;
    
    /**
     * Search type
     * 検索タイプ
     *
     * @var string
     */
    public $search_type = null;
    
    /**
     * Search Delete
     * 検索削除
     *
     * @return string Result 結果
     */
    public function executeApp()
    {
        // ログ
        $this->infoLog("RequestParameter:searchkeyword=[".$this->searchkeyword."]:search_type=[".$this->search_type."]" , __FILE__, __CLASS__, __LINE__);
        
        // 選択タブの保存
        $this->Session->setParameter("item_setting_active_tab", self::SEARCH_DELETE_TAB_NUMBER);
        
        // キーワードの保存
        $this->Session->setParameter("searchkeywordForDelete", $this->searchkeyword);
        
        // 検索タイプの保存
        $this->Session->setParameter("search_type", $this->search_type);
        
        // セッションから検索結果を取得
        $deleteList = $this->Session->getParameter("search_delete_item_data");
        
        // 削除したアイテム数を保存する変数初期化
        $delete_success_num = 0;
        
        // チェックが入っているアイテムを削除
        for( $ii=0; $ii<count($this->delete_search_items); $ii++ )
        {
            // チェックの入ったアイテムの$cnt
            $cnt = $this->delete_search_items[$ii];
            
            $item_id = $deleteList[$cnt]['item_id'];
            $item_no = $deleteList[$cnt]['item_no'];
            
            // ビジネスクラスの削除処理
            $itemDelete = BusinessFactory::getFactory()->getBusiness("businessItemdelete");
            $delete_result = $itemDelete->deleteItem($item_id, $item_no, $this->Session, $this->repository_admin_base, $this->repository_admin_room);
            
            if($delete_result === true){
                $delete_success_num += 1;
            }
        }
        // 削除件数の保存
        $this->Session->setParameter("delete_success_num", $delete_success_num);
        
        // 検索結果のセッションクリア
        $this->Session->removeParameter("search_delete_item_data");
        
        return 'success';
    }
    
}
?>
