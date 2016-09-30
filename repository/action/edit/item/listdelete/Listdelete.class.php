<?php

/**
 * Action class for items bulk deletion
 * アイテム一括削除用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Listdelete.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Action class for the index operation
 * インデックス操作用アクションクラス
 */
require_once WEBAPP_DIR. '/modules/repository/action/edit/tree/Tree.class.php';

/**
 * Action class for items bulk deletion
 * アイテム一括削除用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Item_Listdelete extends RepositoryAction
{
    // コンポーネント受け取り
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

    // *********************
    // リクエストパラメータ
    // *********************
    // 選択インデックスID
    /**
     * Select index id
     * 選択インデックスID
     *
     * @var int
     */
    public $targetIndexId = null;
    // サブインデックス以下削除フラグ
    /**
     * Sub-index following the deletion flag
     * サブインデックス以下削除フラグ
     *
     * @var boolean
     */
    public $isDeleteSubIndexItem = null;

    // *********************
    // メンバ変数
    // *********************
    /**
     * Repository_Action_Edit_Tree object
     * Repository_Action_Edit_Treeオブジェクト
     *
     * @var Repository_Action_Edit_Tree
     */
    private $edit_tree_instance = array();
    
    /**
     * Item bulk deletion
     * アイテム一括削除
     *
     * @return string Result 結果
     */
    function execute()
    {
        try {
            //アクション初期化処理
            $result = $this->initAction();

            if ( $result === false ) {
                $exception = new RepositoryException( "ERR_MSG_xxx-xxx1", 001 );    //主メッセージとログIDを指定して例外を作成
                $this->failTrans();                                                 //トランザクション失敗を設定(ROLLBACK)
                throw $exception;
            }

            // 選択タブの保存
            $this->Session->setParameter("item_setting_active_tab", 2);
            

            // 引数チェック //
            if( $this->targetIndexId == null || $this->targetIndexId == "" ){
                $this->Session->setParameter("error_msg", "Select Index Error.");
                return 'error';
            }
            //セッションクリア
            $this->Session->removeParameter("targetIndexId");
            $this->Session->removeParameter("searchIndexId");
            $this->Session->removeParameter("isDeleteSubIndexItem");
            
            $this->Session->setParameter("targetIndexId", $this->targetIndexId);
            $this->Session->setParameter("searchIndexId", $this->targetIndexId);

            //1  Repository_Action_Edit_Treeをインスタンス化する
            $this->edit_tree_instance = new Repository_Action_Edit_Tree();
            $this->edit_tree_instance->Session = $this->Session;
            $this->edit_tree_instance->Db = $this->Db;
            $this->edit_tree_instance->TransStartDate = $this->TransStartDate;
            
            
            $index_info = array();
            //1  メンバ変数: isDeleteSubIndexItemがfalseの場合
            if( $this->isDeleteSubIndexItem == 0){
                // 1.1   セッション: isDeleteSubIndexItemにfalseを格納する
                $this->Session->setParameter("isDeleteSubIndexItem", false);
            
                array_push($index_info,$this->targetIndexId);
                
            }
            //2  メンバ変数: isDeleteSubIndexItemがtrueの場合
            else {
                //2.1 セッション: isDeleteSubIndexItemにtrueを格納する
                $this->Session->setParameter("isDeleteSubIndexItem", true);
                
                //自分自身のindex_idも格納する
                array_push($index_info,$this->targetIndexId);
                //サブインデックス情報を取得する。
                $this->edit_tree_instance->getAllChildIndexID($this->targetIndexId, $index_info);
                

            }

            $ret = $this->edit_tree_instance->deleteIndexItem($index_info, false);  // アイテムのみ削除する
            if($ret === false){
                $this->Session->setParameter("error_msg", "Delete Items Error.");
                return 'error';
            }
            
            // インデックスツリーコンテンツ数対応 add index contents item num 2013/08/01 A.Jin --start--
            $pid = $this->edit_tree_instance->getParentIndexId($this->targetIndexId);
            
            //親インデックスのコンテンツ数を再計算
            $result = $this->edit_tree_instance->subIndexContents($pid, $this->targetIndexId);
            if($result === false){
                $errMsg = $this->Db->ErrorMsg();
                return false;
            }
            //このインデックス以下の公開コンテンツ数を再計算する(再帰的に処理される)
            $result = $this->edit_tree_instance->recountContents($this->targetIndexId);
            if($result === false){
                $error = $this->Db->ErrorMsg();
                return false; 
            }
            // このインデックス以下の非公開コンテンツ数を再計算する(再帰的に処理される)
            $result = $this->edit_tree_instance->recountPrivateContents($this->targetIndexId);
            if($result === false){
                $error = $this->Db->ErrorMsg();
                return false; 
            }
            // インデックスツリーコンテンツ数対応 add index contents item num 2013/08/01 A.Jin --end--
            
            // アクション終了処理
            $result = $this->exitAction();     //トランザクションが成功していればCOMMITされる
            if ( $result === false ) {
                $exception = new RepositoryException( "ERR_MSG_xxx-xxx1", 001 );    //主メッセージとログIDを指定して例外を作成
                $this->failTrans();	                                                //トランザクション失敗を設定(ROLLBACK)
                throw $exception;
            }
            // エラーメッセージ開放
            $this->Session->removeParameter("error_msg");
            $this->finalize();
            return 'success';
        }
        catch ( RepositoryException $Exception) {
            //エラーログ出力
            $this->logFile(
                "Repository_Action_Edit_Item_Listdelete",   //クラス名
                "execute",                                  //メソッド名
                $Exception->getCode(),                      //ログID
                $Exception->getMessage(),                   //主メッセージ
                $Exception->getDetailMsg() );               //詳細メッセージ
            //アクション終了処理
            $this->exitAction();                            //トランザクションが失敗していればROLLBACKされる

            //異常終了
            return "error";
        }
    }
}
?>
