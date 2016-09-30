<?php

/**
 * Item bulk delete common classes
 * アイテム一括削除共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Itemdelete.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Item bulk delete common classes
 * アイテム一括削除共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Itemdelete extends BusinessBase
{
    /**
     * Index ID list of published index in which the comma delimiters
     * カンマを区切り文字とした公開インデックスのインデックスID一覧
     * 
     * @var string
     */
    public $publicIndexQuery = null;
    
    /**
     * Administrator-based authority level
     * 管理者ベース権限レベル
     *
     * @var int
     */
    public $repository_admin_base = null;
    
    /**
     * Administrator Room authority level
     * 管理者ルーム権限レベル
     *
     * @var int
     */
    public $repository_admin_room = null;
    
    /**
     * To delete the specified items
     * 指定されたアイテムを削除する
     *
     * @param int item_id $item_id Item id アイテムID
     * @param int item_no $item_no Item serial number アイテム通番
     * @param Session $session Session management objects Session管理オブジェクト
     * @param int $repository_admin_base Administrator-based authority level 管理者ベース権限レベル
     * @param int $repository_admin_room Administrator Room authority level 管理者ルーム権限レベル
     * @return boolean Delete success or failure 削除成否
     */
    public function deleteItem($item_id, $item_no, $session, $repository_admin_base, $repository_admin_room)
    {
        if( $item_id === null || $item_no === null || $session === null ){
            $this->errorLog("不正な引数", __FILE__, __CLASS__, __LINE__);
            return false;
        }
        
        $this->repository_admin_base = $repository_admin_base;
        $this->repository_admin_room = $repository_admin_room;
        
        $error_msg = "";
        $result = $this->deleteItemData($item_id, $item_no, $error_msg, $session);
        
        if( $result === false ){
            return false;
        }
        else 
        {
            return true;
        }
    }
    
    /**
     * To remove the metadata and file of items from each table or file system
     * アイテムのメタデータおよびファイルを各テーブルまたはファイルシステムから削除する
     * 
     * @param int item_id $item_id Item id アイテムID
     * @param int item_no $item_no Item serial number アイテム通番
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete success or failure 削除成否
     */
    private function deleteItemData($item_id, $item_no, &$error_msg, $session){
        
        // セッションからユーザーIDを取得
        $user_id = $session->getParameter("_user_id");
        
        // アイテムテーブル削除
        $result = $this->deleteItemTableData($item_id,$item_no,$user_id,$error_msg, $session);
        
        // アイテム属性削除
        $this->deleteItemAttrTableData($item_id,$item_no,$user_id,$error_msg, $session);
        
        // 氏名削除
        $this->deletePersonalNameTableData($item_id,$item_no,$user_id,$error_msg, $session);
        
        // サムネイル削除
        $this->deleteThumbnailTableData($item_id,$item_no,$user_id,$error_msg, $session);
        
        // ファイル削除
        $this->deleteFileTableData($item_id,$item_no,$user_id,$error_msg, $session);
        
        // Add biblio info 2008/08/11 Y.Nakao --start--
        // 書誌情報削除
        $this->deleteBiblioInfoTableData($item_id,$item_no,$user_id,$error_msg, $session);
        // Add biblio info 2008/08/11 Y.Nakao --end--
        
        // 添付ファイル削除
        $this->deleteAttachedFileTableData($item_id,$item_no,$user_id,$error_msg, $session);
        
        // 所属インデックステーブルデータ削除
        $this->deletePositionIndexTableData($item_id,$item_no,$user_id,$error_msg, $session);
        
        // 参照テーブルデータ削除
        $this->deleteReference($item_id,$item_no,$user_id,$error_msg, $session);
        
        // 新着情報削除
        $this->deleteWhatsnew($item_id, $session);
        
        // サプリテーブルデータ削除
        $this->deleteSuppleInfoTableData($item_id,$item_no,$user_id,$error_msg, $session);
        
        // サフィックステーブルデータ削除
        $this->deleteItemSuffix($item_id,$item_no,$user_id,$error_msg);
        
        // 検索テーブルデータ削除
        require_once WEBAPP_DIR. '/modules/repository/components/RepositorySearchTableProcessing.class.php';
        $searchTableProcessing = new RepositorySearchTableProcessing($session, $this->Db);
        $searchTableProcessing->deleteDataFromSearchTableByItemId($item_id, $item_no);
        
        // DOI付与状態削除
        require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
        $repositoryHandleManager = new RepositoryHandleManager($session, $this->Db, $this->accessDate);
        $repositoryHandleManager->deleteDoiStatus($item_id, $item_no);
        
        // アイテムが所属するインデックスのコンテンツ数、非公開コンテンツ数の更新
        $this->updateContentsOfIndex($item_id, $item_no, $session);
        
        // アイテムテーブルの削除結果を返す
        return $result;
    }
    
    /**
     * The number of content belongs to the index of the item to be deleted, private content number of update
     * 削除されるアイテムの所属するインデックスのコンテンツ数、非公開コンテンツ数の更新
     * 
     * @param int item_id $item_id Item id アイテムID
     * @param int item_no $item_no Item serial number アイテム通番
     * @param Session $session Session management objects Session管理オブジェクト
     */
    public function updateContentsOfIndex( $item_id, $item_no, $session ){
        
        // アイテムの公開状況を取得する
        $shown_status = $this->getShowStatus($item_id, $item_no, $session);
        
        // 所属インデックス情報を取得する
        $indexInfo = $this->getIndexInfo($item_id, $item_no, $session);
        
        // 公開インデックスを探すクエリを作成する
        $this->getPublicIndexQuery( $session );
        
        if($shown_status == 1){
            // アイテムの公開状況が公開
            // 所属インデックスの数分アイテム数のデクリメント処理
            $this->reduceContentsNum($indexInfo, $session);
        }
        else 
        {
            // アイテムの公開状況が非公開なので、インデックスの公開状況に関わらず非公開コンテンツ数をデクリメント
            for($ii=0; $ii<count($indexInfo); $ii++){
                $this->deletePrivateContents($indexInfo[$ii]['index_id'], $session);
            }
        }
    }
    /**
     * A few minutes of the index belonging to perform the decrement of the number of content
     * 所属するインデックスの数分、コンテンツ数のデクリメントを行う
     * @param $indexInfo Affiliation index information 所属インデックス情報
     *                   array[$ii]["index_id"|"public_state"]
     * @param Session $session Session management objects Session管理オブジェクト
     */
    private function reduceContentsNum( $indexInfo, $session ){
        
        for( $ii=0; $ii<count($indexInfo); $ii++ ){
            
            $index_id = $indexInfo[$ii]['index_id'];
            $public_state = $indexInfo[$ii]['public_state'];
            
            $query = " SELECT index_id ".
                    " FROM ". DATABASE_PREFIX ."repository_index ".
                    " WHERE index_id = ".$index_id.
                    " AND index_id IN(".$this->publicIndexQuery.") ; ";
            
            $result = $this->Db->execute($query);
            if($result === false){
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $session->setParameter("error_cord",-1);
                $this->errorLog("updateContentsOfIndex select index_id error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to select index_id");//エラーログ
                $e->addError("failed to select index_id.");//画面
                throw $e;
            }
            else if( count($result) == 0 )
            {
                // 非公開インデックスなので非公開件数をデクリメント
                $this->deletePrivateContents($index_id, $session);
            }
            else
            {
                // 公開インデックス
                if( $public_state == "1" && $this->checkParentPublicState($index_id, $session) )
                {
                    $this->deleteContents($index_id, $session);
                }
                else
                {
                    $this->deletePrivateContents($index_id, $session);
                }
            }
        }
    }
    
    /**
     * To get the public status of the item
     * アイテムの公開状況を取得する
     * 
     * @param int item_id $item_id Item id アイテムID
     * @param int item_no $item_no Item serial number アイテム通番
     * @param Session $session Session management objects Session管理オブジェクト
     * @return int Public status 公開状況
     */
    private function getShowStatus( $item_id, $item_no, $session ){
        
        $query = "SELECT shown_status ".
                "FROM ". DATABASE_PREFIX ."repository_item ".
                "WHERE item_id = ? AND ".
                "item_no = ? ; ";
        
        $params = null;
        $params[] = $item_id;   // item_id
        $params[] = $item_no;   // item_no
        $result = $this->Db->execute($query,$params);
        if($result === false){
            $errNo = $this->Db->ErrorNo();
            $errMsg = $this->Db->ErrorMsg();
            $session->setParameter("error_code", -1);
            $this->errorLog("getShowStatusx select shown_status error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select shown_status");//エラーログ
            $e->addError("failed to select shown_status.");//画面
            throw $e;
        }
        
        return $result[0]['shown_status'];
    }
    
    /**
     * To get the affiliation index information
     * 所属インデックス情報を取得する
     * 
     * @param int item_id $item_id Item id アイテムID
     * @param int item_no $item_no Item serial number アイテム通番
     * @param Session $session Session management objects Session管理オブジェクト
     * @return $result Index ID list belong 所属するインデックスID一覧
     *                 array[$ii]["pos.index_id"|"idx.public_state"]
     */
    private function getIndexInfo( $item_id, $item_no, $session ){
        
        $query = " SELECT pos.index_id, idx.public_state ".
                 " FROM ". DATABASE_PREFIX ."repository_position_index AS pos, ".
                           DATABASE_PREFIX ."repository_index AS idx ".
                 " WHERE pos.item_id = ? ".
                 " AND   pos.item_no = ? ".
                 " AND pos.index_id = idx.index_id ; ";
        
        $params = null;
        $params[] = $item_id;
        $params[] = $item_no;
        $result = $this->Db->execute($query,$params);
        if($result === false){
            $errNo = $this->Db->ErrorNo();
            $errMsg = $this->Db->ErrorMsg();
            $session->setParameter("error_code", -1);
            $this->errorLog("getIndexInfo select index_id error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select index_id");//エラーログ
            $e->addError("failed to select index_id.");//画面
            throw $e;
        }
        
        return $result;
    }
    
    
    // ↓↓repo_action_main_treeから持ってきたメソッド。メンバ変数などを変更する
    /**
     * To create a public index search query
     * 公開インデックス検索用クエリを作成する
     *
     * @param Session $session Session management objects Session管理オブジェクト
     * @return string Public index acquisition query 公開インデックス取得クエリ
     */
    public function getPublicIndexQuery( $session )
    {
        if(empty($this->publicIndexQuery))
        {
            // ログイン情報を退避し、未ログイン状態にしておく
            $tmp_user_id = $session->getParameter("_user_id");
            $tmp_user_auth_id = $session->getParameter("_user_auth_id");
            $session->setParameter("_user_id", "0");
            $session->setParameter("_user_auth_id", "");
            
            // 公開インデックスを探すクエリを生成する
            $indexAuthorityManager = new RepositoryIndexAuthorityManager($session, $this->Db, $this->accessDate);
            $this->publicIndexQuery = $indexAuthorityManager->getPublicIndexQuery(false, $this->repository_admin_base, $this->repository_admin_room);
            
            // ログイン情報を元に戻す
            $session->setParameter("_user_id", $tmp_user_id);
            $session->setParameter("_user_auth_id", $tmp_user_auth_id);
        }
        return $this->publicIndexQuery;
    }
    
    
    /**
     * The higher the index is checked or not private.
     * 上位インデックスが非公開でないか調べる。
     *
     * @param int $index_id Index id インデックスID
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Public situation (true: in public, false: it is private) 公開状況(true:公開中,false:非公開である)
     */
    public function checkParentPublicState($index_id, $session){
        
        // 親インデックスのIDを取得
        $query = "SELECT parent_index_id ".
                 "FROM ".DATABASE_PREFIX."repository_index ".
                 "WHERE index_id = ? ".
                 "AND is_delete = 0;";
        $params = array();
        $params[] = $index_id;
        $result = $this->Db->execute($query, $params);
        if($result === false || count($result) != 1){
            $errNo = $this->Db->ErrorNo();
            $error_msg = $this->Db->ErrorMsg();
            $session->setParameter("error_cord",-1);
            $this->errorLog("checkParentPublicState select index_id of parent error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select index_id of parent");//エラーログ
            $e->addError("failed to select index_id of parent.");//画面
            throw $e;
        }

        // 親がルートインデックスではない場合
        if($result[0]['parent_index_id'] != "0"){
            // 親インデックスの公開状況を取得
            $query = "SELECT public_state ".
                     "FROM ".DATABASE_PREFIX."repository_index ".
                     "WHERE index_id = ? ".
                     "AND is_delete = 0;";
            $params = array();
            $params[] = $result[0]['parent_index_id'];
            $parent = $this->Db->execute($query, $params);
            if($parent === false){
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $session->setParameter("error_cord",-1);
                $this->errorLog("checkParentPublicState select public_state of parent index error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to select public_state of parent index");//エラーログ
                $e->addError("failed to select public_state of parent index.");//画面
                throw $e;
            }

            if($parent[0]['public_state'] == "0"){
                // 親が非公開の場合
                return false;
            } else {
                // 親が公開の場合、その親を調べる
                if($this->checkParentPublicState($result[0]['parent_index_id'], $session) == false){
                    // 親に非公開があった場合
                    return false;
                }
            }
        }
        // 上位のインデックスが非公開でない場合
        return true;
    }
    
    /**
     * Decrements the number of public items that belong to the index
     * インデックスに所属する公開アイテム数をデクリメントする
     *
     * @param int $index_id Index id インデックスID
     * @param Session $session Session management objects Session管理オブジェクト
     */
    public function deleteContents($index_id, $session){
        // decrement contents
        $query = "UPDATE ".DATABASE_PREFIX."repository_index ".
                "   SET contents = contents - 1 ".
                " WHERE index_id = ? ".
                " AND contents > 0; ";
        $params = null;
        $params[] = $index_id;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $errNo = $this->Db->ErrorNo();
            $error_msg = $this->Db->ErrorMsg();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deleteContents update contents error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to update contents");//エラーログ
            $e->addError("failed to update contents.");//画面
            throw $e;
        }
    
        // update perent contents
        $query = "SELECT parent_index_id ".
                "  FROM ".DATABASE_PREFIX."repository_index ".
                " WHERE index_id = ? ;";
        $params = null;
        $params[] = $index_id;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $errNo = $this->Db->ErrorNo();
            $error_msg = $this->Db->ErrorMsg();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deleteContents update contents of parent index error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to update contents of parent index");//エラーログ
            $e->addError("failed to update contents of parent index.");//画面
            throw $e;
        }
        if(count($result)>0){
            if($result[0]['parent_index_id'] != 0){
                $this->deleteContents($result[0]['parent_index_id'], $session);
            }
        }
    }
    
    /**
     * Decrements the number of private items that belong to the index
     * インデックスに所属する非公開アイテム数をデクリメントする
     *
     * @param int $index_id Index id インデックスID
     * @param Session $session Session management objects Session管理オブジェクト
     */
    public function deletePrivateContents($index_id, $session){
        // decrement contents
        $query = "UPDATE ".DATABASE_PREFIX."repository_index ".
                "   SET private_contents = private_contents - 1 ".
                " WHERE index_id = ? ".
                " AND private_contents > 0; ";
        $params = null;
        $params[] = $index_id;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $errNo = $this->Db->ErrorNo();
            $error_msg = $this->Db->ErrorMsg();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deletePrivateContents update private_contents error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to update private_contents");//エラーログ
            $e->addError("failed to update private_contents.");//画面
            throw $e;
        }
    
        // update perent contents
        $query = "SELECT parent_index_id ".
                "  FROM ".DATABASE_PREFIX."repository_index ".
                " WHERE index_id = ? ;";
        $params = null;
        $params[] = $index_id;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $errNo = $this->Db->ErrorNo();
            $error_msg = $this->Db->ErrorMsg();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deletePrivateContents update private_contents of parent error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to update private_contents of parent");//エラーログ
            $e->addError("failed to update private_contents of parent.");//画面
            throw $e;
        }
        if(count($result)>0){
            if($result[0]['parent_index_id'] != 0){
                $this->deletePrivateContents($result[0]['parent_index_id'], $session);
            }
        }
    }
    
    /**
     * Delete an item table data to be specified by the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定されるアイテムテーブルデータを削除
     *
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deleteItemTableData($Item_ID,$Item_No,$user_ID,&$error_msg, $session){
        
        $delete_result = false;
        
        // アイテム属性テーブルにレコードがあるか判定
        $query = "SELECT * ".       // 属性値
                 "FROM ". DATABASE_PREFIX ."repository_item ".  // アイテム属性テーブル
                 "WHERE item_id = ? AND ".      // アイテムID
                 "item_no = ? AND ".            // アイテム通番
                 "is_delete = ?; ";             // 削除されていない
        $params = null;
        // $queryの?を置き換える配列
        $params[] = $Item_ID;
        $params[] = $Item_No;
        $params[] = 0;
        // SELECT実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $error_msg = $this->Db->ErrorMsg();
            $errNo = $this->Db->ErrorNo();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deleteItemTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select item table data");//エラーログ
            $e->addError("failed to select item table data.");//画面
            throw $e;
        }
        if(count($result) > 0){
            //レコードがあるため、削除実行
            $query = "UPDATE ". DATABASE_PREFIX ."repository_item ".
                     "SET del_user_id = ?, ".
                     "del_date = ?, ".
                     "mod_user_id = ?,".
                     "mod_date = ?, ".
                     "is_delete = ? ".
                     "WHERE item_id = ? AND ".
                     "item_no = ?; ";
            $params = null;
            $params[] = $user_ID;               // del_user_id
            $params[] = $this->accessDate;  // del_date
            $params[] = $user_ID;               // mod_user_id
            $params[] = $this->accessDate;  // mod_date
            $params[] = 1;                      // is_delete
            $params[] = $Item_ID;               // item_id
            $params[] = $Item_No;               // item_no
            //UPDATE実行
            $result = $this->Db->execute($query,$params);
            if($result === false){
                //必要であればSQLエラー番号・メッセージ取得
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $this->errorLog("deleteItemTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to delete item table data");//エラーログ
                $e->addError("failed to delete item table data.");//画面
                throw $e;
            }
            $delete_result = true;
        }
        return $delete_result;
    }
    
    /**
     * Delete an item attribute table data to be specified by the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定されるアイテム属性テーブルデータを削除
     *
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deleteItemAttrTableData($Item_ID,$Item_No,$user_ID,&$error_msg, $session){
        
        $delete_result = false;
        
        // アイテム属性テーブルにレコードがあるか判定
        $query = "SELECT * ".       // 属性値
                "FROM ". DATABASE_PREFIX ."repository_item_attr ". // アイテム属性テーブル
                "WHERE item_id = ? AND ".      // アイテムID
                "item_no = ? AND ".            // アイテム通番
                "is_delete = ? ".              // 削除されていない
                "order by attribute_no; ";     // 属性通番順にソート
        $params = null;
        // $queryの?を置き換える配列
        $params[] = $Item_ID;
        $params[] = $Item_No;
        $params[] = 0;
        // SELECT実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $error_msg = $this->Db->ErrorMsg();
            $errNo = $this->Db->ErrorNo();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deleteItemAttrTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select item attr table data");//エラーログ
            $e->addError("failed to select item attr table data.");//画面
            throw $e;
        }
        if(count($result) > 0){
            //レコードがあるため、削除実行
            $query = "UPDATE ". DATABASE_PREFIX ."repository_item_attr ".
                    "SET del_user_id = ?, ".
                    "del_date = ?, ".
                    "mod_user_id = ?, ".
                    "mod_date = ?, ".
                    "is_delete = ? ".
                    "WHERE item_id = ? AND ".
                    "item_no = ?; ";
            $params = null;
            $params[] = $user_ID;               // del_user_id
            $params[] = $this->accessDate;  // del_date
            $params[] = $user_ID;               // mod_user_id
            $params[] = $this->accessDate;  // mod_date
            $params[] = 1;                      // is_delete
            $params[] = $Item_ID;               // item_id
            $params[] = $Item_No;               // item_no
            //UPDATE実行
            $result = $this->Db->execute($query,$params);
            if($result === false){
                //必要であればSQLエラー番号・メッセージ取得
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $this->errorLog("deleteItemAttrTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to delete item attr table data");//エラーログ
                $e->addError("failed to delete item attr table data.");//画面
                throw $e;
            }
            $delete_result = true;
        }
        return $delete_result;
    
    }
    
    /**
     * Name table data deletion, which is specified by the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定される氏名テーブルデータ削除
     * 
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deletePersonalNameTableData($Item_ID,$Item_No,$user_ID,&$error_msg, $session){
        
        $delete_result = false;
        
        // 氏名テーブルにレコードがあるか判定
        $query = "SELECT * ".       // 属性値
                "FROM ". DATABASE_PREFIX ."repository_personal_name ". //氏名テーブル
                "WHERE item_id = ? AND ".      // アイテムID
                "item_no = ?  AND ".           // アイテム通番
                "is_delete = ?; ";             // 削除されていない
        $params = null;
        // $queryの?を置き換える配列
        $params[] = $Item_ID;
        $params[] = $Item_No;
        $params[] = 0;
        // SELECT実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $error_msg = $this->Db->ErrorMsg();
            $errNo = $this->Db->ErrorNo();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deletePersonalNameTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select repository_personal_name table data");//エラーログ
            $e->addError("failed to select repository_personal_name table data.");//画面
            throw $e;
        }
        if(count($result) > 0){
            //レコードがあるため、削除実行
            $query = "UPDATE ". DATABASE_PREFIX ."repository_personal_name ".
                    "SET del_user_id = ?, ".
                    "del_date = ?, ".
                    "mod_user_id = ?, ".
                    "mod_date = ?, ".
                    "is_delete = ? ".
                    "WHERE item_id = ? AND ".
                    "item_no = ?; ";
            $params = null;
            $params[] = $user_ID;               // del_user_id
            $params[] = $this->accessDate;  // del_date
            $params[] = $user_ID;               // mod_user_id
            $params[] = $this->accessDate;  // mod_date
            $params[] = 1;                      // is_delete
            $params[] = $Item_ID;               // item_id
            $params[] = $Item_No;               // item_no
            //UPDATE実行
            $result = $this->Db->execute($query,$params);
            if($result === false){
                //必要であればSQLエラー番号・メッセージ取得
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $this->errorLog("deletePersonalNameTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to delete personal name table data");//エラーログ
                $e->addError("failed to delete personal name table data.");//画面
                throw $e;
            }
            $delete_result = true;
        }
        return $delete_result;
    
    }
    
    /**
     * Thumbnail table data deletion, which is specified by the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定されるサムネイルテーブルデータ削除
     * 
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deleteThumbnailTableData($Item_ID,$Item_No,$user_ID,&$error_msg, $session){
        
        $delete_result = false;
        
        // サムネイルテーブルにレコードがあるか判定
        $query = "SELECT * ".       // 属性値
                "FROM ". DATABASE_PREFIX ."repository_thumbnail ". //サムネイルテーブル
                "WHERE item_id = ? AND ".      // アイテムID
                "item_no = ?  AND ".           // アイテム通番
                "is_delete = ?; ";             // 削除されていない
        $params = null;
        // $queryの?を置き換える配列
        $params[] = $Item_ID;
        $params[] = $Item_No;
        $params[] = 0;
        // SELECT実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $error_msg = $this->Db->ErrorMsg();
            $errNo = $this->Db->ErrorNo();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deleteThumbnailTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select repository_thumbnail table data");//エラーログ
            $e->addError("failed to select repository_thumbnail table data.");//画面
            throw $e;
        }
        if(count($result) > 0){
            //レコードがあるため、削除実行
            $query = "UPDATE ". DATABASE_PREFIX ."repository_thumbnail ".
                    "SET del_user_id = ?, ".
                    "del_date = ?, ".
                    "mod_user_id = ?, ".
                    "mod_date = ?, ".
                    "is_delete = ? ".
                    "WHERE item_id = ? AND ".
                    "item_no = ?; ";
            $params = null;
            $params[] = $user_ID;               // del_user_id
            $params[] = $this->accessDate;  // del_date
            $params[] = $user_ID;               // mod_user_id
            $params[] = $this->accessDate;  // mod_date
            $params[] = 1;                      // is_delete
            $params[] = $Item_ID;               // item_id
            $params[] = $Item_No;               // item_no
            //UPDATE実行
            $result = $this->Db->execute($query,$params);
            if($result === false){
                //必要であればSQLエラー番号・メッセージ取得
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $this->errorLog("deleteThumbnailTableData:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to delete thumbnail table data");//エラーログ
                $e->addError("failed to delete thumbnail table data.");//画面
                throw $e;
            }
            $delete_result = true;
        }
        return $delete_result;
    }
    
    /**
     * File table data deletion, which is specified by the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定されるファイルテーブルデータ削除
     * 
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deleteFileTableData($Item_ID,$Item_No,$user_ID,&$error_msg, $session){
        
        $delete_result = false;
        
        // ファイルテーブルにレコードがあるか判定
        $query = "SELECT * ".       // 属性値
                "FROM ". DATABASE_PREFIX ."repository_file ".  //ファイルテーブル
                "WHERE item_id = ? AND ".      // アイテムID
                "item_no = ?  AND ".           // アイテム通番
                "is_delete = ?; ";             // 削除されていない
        $params = null;
        // $queryの?を置き換える配列
        $params[] = $Item_ID;
        $params[] = $Item_No;
        $params[] = 0;
        // SELECT実行
        $select_result = $this->Db->execute($query, $params);
        if($select_result === false){
            $error_msg = $this->Db->ErrorMsg();
            $errNo = $this->Db->ErrorNo();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deleteFileTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select repository_file table data");//エラーログ
            $e->addError("failed to select repository_file table data.");//画面
            throw $e;
        }
        
        if(count($select_result) > 0){
            //レコードがあるため、削除実行
            $query = "UPDATE ". DATABASE_PREFIX ."repository_file ".
                    "SET del_user_id = ?, ".
                    "del_date = ?, ".
                    "mod_user_id = ?, ".
                    "mod_date = ?, ".
                    "is_delete = ? ".
                    "WHERE item_id = ? AND ".
                    "item_no = ?; ";
            $params = null;
            $params[] = $user_ID;               // del_user_id
            $params[] = $this->accessDate;  // del_date
            $params[] = $user_ID;               // mod_user_id
            $params[] = $this->accessDate;  // mod_date
            $params[] = 1;                      // is_delete
            $params[] = $Item_ID;               // item_id
            $params[] = $Item_No;               // item_no
            //UPDATE実行
            $result = $this->Db->execute($query,$params);
            if($result === false){
                //必要であればSQLエラー番号・メッセージ取得
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $this->errorLog("deleteFileTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to delete repository_file table data");//エラーログ
                $e->addError("failed to delete repository_file table data.");//画面
                throw $e;
            }
            // 課金情報があるか検索
            $query = "SELECT * ".       // 属性値
                    "FROM ". DATABASE_PREFIX ."repository_file_price ".    //ファイルテーブル
                    "WHERE item_id = ? AND ".      // アイテムID
                    "item_no = ?  AND ".           // アイテム通番
                    "is_delete = ?; ";             // 削除されていない
            $params = null;
            // $queryの?を置き換える配列
            $params[] = $Item_ID;
            $params[] = $Item_No;
            $params[] = 0;
            // SELECT実行
            $result = $this->Db->execute($query, $params);
            if($result === false){
                $error_msg = $this->Db->ErrorMsg();
                $errNo = $this->Db->ErrorNo();
                $session->setParameter("error_cord",-1);
                $this->errorLog("deleteFilePriceTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to select repository_file_price table data");//エラーログ
                $e->addError("failed to select repository_file_price table data.");//画面
                throw $e;
            }
            if(count($result) > 0){
                $query = "UPDATE ". DATABASE_PREFIX ."repository_file_price ".
                        "SET del_user_id = ?, ".
                        "del_date = ?, ".
                        "mod_user_id = ?, ".
                        "mod_date = ?, ".
                        "is_delete = ? ".
                        "WHERE item_id = ? AND ".
                        "item_no = ?; ";
                $params = null;
                $params[] = $user_ID;               // del_user_id
                $params[] = $this->accessDate;  // del_date
                $params[] = $user_ID;               // mod_user_id
                $params[] = $this->accessDate;  // mod_date
                $params[] = 1;                      // is_delete
                $params[] = $Item_ID;               // item_id
                $params[] = $Item_No;               // item_no
                //UPDATE実行
                $result = $this->Db->execute($query,$params);
                if($result === false){
                    //必要であればSQLエラー番号・メッセージ取得
                    $errNo = $this->Db->ErrorNo();
                    $error_msg = $this->Db->ErrorMsg();
                    $this->errorLog("deleteFilePriceTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                    $e = new AppException("failed to delete repository_file_price table data");//エラーログ
                    $e->addError("failed to delete repository_file_price table data.");//画面
                    throw $e;
                }
                $delete_result = true;
            }
    
            // Mod remove physical file T.Koyasu 2016/02/29 --start--
            $businessName = "businessContentfiletransaction";
            $business = BusinessFactory::getFactory()->getBusiness($businessName);
            for($ii = 0; $ii < count($select_result); $ii++){
                $itemId = $select_result[$ii][RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID];
                $attrId = $select_result[$ii][RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID];
                $fileNo = $select_result[$ii][RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO];
                $business->delete($itemId, $attrId, $fileNo);
            }
            // Mod remove physical file T.Koyasu 2016/02/29 --end--
        }
        return $delete_result;
    }
    
    /**
     * Delete the following specified directory
     * 指定したディレクトリ以下を削除
     * 
     * @param string $dir Delete the target directory path 削除対象ディレクトリパス
     */
    private function removeDirectory($dir) {
        if(strlen($dir) > 0)
        {
            if (file_exists($dir)) {
                chmod ($dir, 0777 );
            }
            else
            {
                return;
            }
            if ($handle = opendir("$dir")) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        if (is_dir("$dir/$file")) {
                            $this->removeDirectory("$dir/$file");
                            if(file_exists("$dir/$file")) {
                                rmdir("$dir/$file");
                            }
                        } else {
                            chmod ("$dir/$file", 0777 );
                            unlink("$dir/$file");
                        }
                    }
                }
                closedir($handle);
                rmdir($dir);
            }
        }
    }
    
    /**
     * Bibliographic information table data deletion, which is specified by the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定される書誌情報テーブルデータ削除
     * 
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deleteBiblioInfoTableData($Item_ID,$Item_No,$user_ID,&$error_msg, $session){
        
        $delete_result = false;
        
        // 書誌情報テーブルにレコードがあるか判定
        $query = "SELECT * ".       // 属性値
                "FROM ". DATABASE_PREFIX ."repository_biblio_info ".   //氏名テーブル
                "WHERE item_id = ? AND ".      // アイテムID
                "item_no = ?  AND ".           // アイテム通番
                "is_delete = ?; ";             // 削除されていない
        $params = null;
        // $queryの?を置き換える配列
        $params[] = $Item_ID;
        $params[] = $Item_No;
        $params[] = 0;
        // SELECT実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $error_msg = $this->Db->ErrorMsg();
            $errNo = $this->Db->ErrorNo();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deleteBiblioInfoTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select repository_biblio_info table data");//エラーログ
            $e->addError("failed to select repository_biblio_info table data.");//画面
            throw $e;
        }
        if(count($result) > 0){
            //レコードがあるため、削除実行
            $query = "UPDATE ". DATABASE_PREFIX ."repository_biblio_info ".
                    "SET del_user_id = ?, ".
                    "del_date = ?, ".
                    "mod_user_id = ?, ".
                    "mod_date = ?, ".
                    "is_delete = ? ".
                    "WHERE item_id = ? AND ".
                    "item_no = ?; ";
            $params = null;
            $params[] = $user_ID;               // del_user_id
            $params[] = $this->accessDate;  // del_date
            $params[] = $user_ID;               // mod_user_id
            $params[] = $this->accessDate;  // mod_date
            $params[] = 1;                      // is_delete
            $params[] = $Item_ID;               // item_id
            $params[] = $Item_No;               // item_no
            //UPDATE実行
            $result = $this->Db->execute($query,$params);
            if($result === false){
                //必要であればSQLエラー番号・メッセージ取得
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $this->errorLog("deleteBiblioInfoTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to delete repository_biblio_info table data");//エラーログ
                $e->addError("failed to delete repository_biblio_info table data.");//画面
                throw $e;
            }
            $delete_result = true;
        }
        return $delete_result;
    }
    // Add biblio info 2008/08/11 Y.Nakao --end--
    
    /**
     * Attachment data deletion, which is specified by the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定される添付ファイルデータ削除
     * 
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deleteAttachedFileTableData($Item_ID,$Item_No,$user_ID,&$error_msg, $session){
        
        $delete_result = false;
        
        // 添付ファイルテーブルにレコードがあるか判定
        $query = "SELECT * ".       // 属性値
                "FROM ". DATABASE_PREFIX ."repository_attached_file ". // 添付ファイルテーブル
                "WHERE item_id = ? AND ".      // アイテムID
                "item_no = ?  AND ".           // アイテム通番
                "is_delete = ?; ";             // 削除されていない
        $params = null;
        // $queryの?を置き換える配列
        $params[] = $Item_ID;
        $params[] = $Item_No;
        $params[] = 0;
        // SELECT実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $error_msg = $this->Db->ErrorMsg();
            $errNo = $this->Db->ErrorNo();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deleteAttachedFileTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select repository_attached_file table data");//エラーログ
            $e->addError("failed to select repository_attached_file table data.");//画面
            throw $e;
        }
        if(count($result) > 0){
            //レコードがあるため、削除実行
            $query = "UPDATE ". DATABASE_PREFIX ."repository_attached_file ".
                    "SET del_user_id = ?, ".
                    "del_date = ?, ".
                    "mod_user_id = ?, ".
                    "mod_date = ?, ".
                    "is_delete = ? ".
                    "WHERE item_id = ? AND ".
                    "item_no = ?; ";
            $params = null;
            $params[] = $user_ID;               // del_user_id
            $params[] = $this->accessDate;  // del_date
            $params[] = $user_ID;               // mod_user_id
            $params[] = $this->accessDate;  // mod_date
            $params[] = 1;                      // is_delete
            $params[] = $Item_ID;               // item_id
            $params[] = $Item_No;               // item_no
            //UPDATE実行
            $result = $this->Db->execute($query,$params);
            if($result === false){
                //必要であればSQLエラー番号・メッセージ取得
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $this->errorLog("deleteAttachedFileTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to delete repository_attached_file table data");//エラーログ
                $e->addError("failed to delete repository_attached_file table data.");//画面
                throw $e;
            }
            $delete_result = true;
        }
        return $delete_result;
    }
    
    /**
     * Affiliation index data deletion, which is specified by the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定される所属インデックスデータ削除
     * 
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deletePositionIndexTableData($Item_ID,$Item_No,$user_ID,&$error_msg, $session){
        
        $delete_result = false;
        
        // 所属インデックステーブルにレコードがあるか判定
        $query = "SELECT * ".       // 属性値
                "FROM ". DATABASE_PREFIX ."repository_position_index ".    // 所属インデックステーブル
                "WHERE item_id = ? AND ".      // アイテムID
                "item_no = ?  AND ".           // アイテム通番
                "is_delete = ?; ";             // 削除されていない
        $params = null;
        // $queryの?を置き換える配列
        $params[] = $Item_ID;
        $params[] = $Item_No;
        $params[] = 0;
        // SELECT実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $error_msg = $this->Db->ErrorMsg();
            $errNo = $this->Db->ErrorNo();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deletePositionIndexTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select repository_position_index table data");//エラーログ
            $e->addError("failed to select repository_position_index table data.");//画面
            throw $e;
        }
        if(count($result) > 0){
            //レコードがあるため、削除実行
            $query = "UPDATE ". DATABASE_PREFIX ."repository_position_index ".
                    "SET del_user_id = ?, ".
                    "del_date = ?, ".
                    "mod_user_id = ?, ".
                    "mod_date = ?, ".
                    "is_delete = ? ".
                    "WHERE item_id = ? AND ".
                    "item_no = ?; ";
            $params = null;
            $params[] = $user_ID;               // del_user_id
            $params[] = $this->accessDate;  // del_date
            $params[] = $user_ID;               // mod_user_id
            $params[] = $this->accessDate;  // mod_date
            $params[] = 1;                      // is_delete
            $params[] = $Item_ID;               // item_id
            $params[] = $Item_No;               // item_no
            //UPDATE実行
            $result = $this->Db->execute($query,$params);
            if($result === false){
                //必要であればSQLエラー番号・メッセージ取得
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $this->errorLog("deletePositionIndexTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to delete repository_position_index table data");//エラーログ
                $e->addError("failed to delete repository_position_index table data.");//画面
                throw $e;
            }
            $delete_result = true;
        }
        return $delete_result;
    }
    
    /**
     * Reference table data deletion, which is specified by the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定される参照テーブルデータ削除
     * 
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deleteReference($Item_ID,$Item_No,$user_ID,&$error_msg, $session){
        
        $delete_result = false;
        
        // 参照テーブルにレコードがあるか判定
        $query = "SELECT * ".
                "FROM ". DATABASE_PREFIX ."repository_reference ". // reference table
                "WHERE org_reference_item_id = ? AND ".
                "org_reference_item_no = ?  AND ".
                "is_delete = ?; ";
        $params = null;
        $params[] = $Item_ID;
        $params[] = $Item_No;
        $params[] = 0;
        // sql execute
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $error_msg = $this->Db->ErrorMsg();
            $errNo = $this->Db->ErrorNo();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deleteReference error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select repository_reference table data");//エラーログ
            $e->addError("failed to select repository_reference table data.");//画面
            throw $e;
        }
        if(count($result) > 0){
            // delete action
            $query = "UPDATE ". DATABASE_PREFIX ."repository_reference ".
                    "SET del_user_id = ?, ".
                    "del_date = ?, ".
                    "mod_user_id = ?, ".
                    "mod_date = ?, ".
                    "is_delete = ? ".
                    "WHERE org_reference_item_id = ? AND ".
                    "org_reference_item_no = ?; ";
            $params = null;
            $params[] = $user_ID;               // del_user_id
            $params[] = $this->accessDate;  // del_date
            $params[] = $user_ID;               // mod_user_id
            $params[] = $this->accessDate;  // mod_date
            $params[] = 1;                      // is_delete
            $params[] = $Item_ID;               // item_id
            $params[] = $Item_No;               // item_no
            $result = $this->Db->execute($query,$params);
            if($result === false){
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $this->errorLog("deleteReference error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to delete repository_reference table data");//エラーログ
                $e->addError("failed to delete repository_reference table data.");//画面
                throw $e;
            }
            $delete_result = true;
        }
        return $delete_result;
    }
    
    /**
     * To remove the What's New
     * 新着情報を削除する
     * 
     * @param int $Item_ID Item id アイテムID
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deleteWhatsnew($item_id, $session){
        
        $delete_result = false;
        
        $container =& DIContainerFactory::getContainer();
        $whatsnewAction =& $container->getComponent("whatsnewAction");
        $result = $whatsnewAction->delete($item_id);
        if ($result === false) {
            $session->setParameter("error_cord",-1);
            $errNo = $this->Db->ErrorNo();
            $error_msg = $this->Db->ErrorMsg();
            $this->errorLog("deleteWhatsnew error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to delete whatsnew data");//エラーログ
            $e->addError("failed to delete whatsnew table data.");//画面
            throw $e;
        }
        $delete_result = true;
        return $delete_result;
    }
    // Add send item infomation to whatsnew module 2009/01/27 A.Suzuki --end--
    
    /**
     * [[アイテムIDとアイテム通番にて指定されるサプリテーブルデータ削除]]
     */
    /**
     * Supplemental contents table data deletion, which is specified by the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定されるサプリテーブルデータ削除
     * 
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @param Session $session Session management objects Session管理オブジェクト
     * @return boolean Delete result 削除結果
     */
    private function deleteSuppleInfoTableData($Item_ID,$Item_No,$user_ID,&$error_msg, $session){
        
        $delete_result = false;
        
        // サプリテーブルにレコードがあるか判定
        $query = "SELECT * ".       // 属性値
                "FROM ". DATABASE_PREFIX ."repository_supple ".    //サプリテーブル
                "WHERE item_id = ? AND ".      // アイテムID
                "item_no = ?  AND ".           // アイテム通番
                "is_delete = ?; ";             // 削除されていない
        $params = null;
        // $queryの?を置き換える配列
        $params[] = $Item_ID;
        $params[] = $Item_No;
        $params[] = 0;
        // SELECT実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $error_msg = $this->Db->ErrorMsg();
            $errNo = $this->Db->ErrorNo();
            $session->setParameter("error_cord",-1);
            $this->errorLog("deleteSuppleInfoTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to select repository_supple table data");//エラーログ
            $e->addError("failed to select repository_supple table data.");//画面
            throw $e;
        }
        if(count($result) > 0){
            //レコードがあるため、削除実行
            $query = "UPDATE ". DATABASE_PREFIX ."repository_supple ".
                    "SET mod_user_id = ?, ".
                    "mod_date = ?, ".
                    "del_user_id = ?, ".
                    "del_date = ?, ".
                    "is_delete = ? ".
                    "WHERE item_id = ? AND ".
                    "item_no = ?; ";
            $params = null;
            $params[] = $user_ID;               // mod_user_id
            $params[] = $this->accessDate;  // mod_date
            $params[] = $user_ID;               // del_user_id
            $params[] = $this->accessDate;  // del_date
            $params[] = 1;                      // is_delete
            $params[] = $Item_ID;               // item_id
            $params[] = $Item_No;               // item_no
            //UPDATE実行
            $result = $this->Db->execute($query,$params);
            if($result === false){
                //必要であればSQLエラー番号・メッセージ取得
                $errNo = $this->Db->ErrorNo();
                $error_msg = $this->Db->ErrorMsg();
                $this->errorLog("deleteSuppleInfoTableData error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
                $e = new AppException("failed to delete repository_supple table data");//エラーログ
                $e->addError("failed to delete repository_supple table data.");//画面
                throw $e;
            }
            $delete_result = true;
        }
        return $delete_result;
    }
    // Add input type "supple" 2009/08/24 A.Suzuki --end--
    
    /**
     * Delete the suffix that is specified in the item ID and the item serial number
     * アイテムIDとアイテム通番にて指定されるサフィックスを削除
     * 
     * @param int $Item_ID Item id アイテムID
     * @param int $Item_No Item serial number アイテム通番
     * @param string $user_ID Delete the execution of a user ID 削除実行ユーザID
     * @param string $error_msg Error message エラーメッセージ
     * @return boolean Delete result 削除結果
     */
    private function deleteItemSuffix($Item_ID,$Item_No,$user_ID,&$error_msg){
        
        $delete_result = false;
        
        // 指定されたアイテムのサフィックスを削除
        $query = "UPDATE ". DATABASE_PREFIX ."repository_suffix ".
                "SET mod_user_id = ?, ".
                "del_user_id = ?, ".
                "mod_date = ?, ".
                "del_date = ?, ".
                "is_delete = ? ".
                "WHERE item_id = ? ".
                "AND item_no = ? ;";
        $params = array();
        $params[] = $user_ID;               //mod_user_id
        $params[] = $user_ID;               //del_user_id
        $params[] = $this->accessDate;  //mod_date
        $params[] = $this->accessDate;  //del_date
        $params[] = 1;                      //is_delete
        $params[] = $Item_ID;               //item_id
        $params[] = $Item_No;               //item_no
    
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $errNo = $this->Db->ErrorNo();
            $error_msg = $this->Db->ErrorMsg();
            $this->errorLog("deleteItemSuffix error:errNo=".$errNo.":error_msg=".$error_msg, __FILE__, __CLASS__, __LINE__);//エラーログ
            $e = new AppException("failed to delete repository_suffix table data");//エラーログ
            $e->addError("failed to delete repository_suffix table data.");//画面
            throw $e;
        }
        $delete_result = true;
        
        return $delete_result;
    }
    
    
}
?>