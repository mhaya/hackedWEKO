<?php

/**
 * Item type manager class
 * アイテムタイプ管理クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: ItemtypeManager.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Logic base class for the WEKO
 * WEKO用コンポーネント基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryLogicBase.class.php';

/**
 * Item type manager class
 * アイテムタイプ管理クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Itemtypemanager extends RepositoryLogicBase
{
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $session session object セッションオブジェクト
     * @param DbObjectAdodb $dbAccess DB object DBオブジェクト
     * @param string $transStartDate process start date 処理開始時間
     */
    public function __construct($session, $dbAccess, $transStartDate)
    {
        parent::__construct($session, $dbAccess, $transStartDate);
    }
    
    /**
     * set exclusive authority id
     * 除外権限IDをセットする
     *
     * @param int $item_type_id item type ID アイテムタイプID
     * @param array $exclusive_base_auth exclusive base authorit ID array 除外ベース権限配列
     *                                       array[$ii]
     * @param int $exclusive_room_auth exclusice room authority 除外ルーム権限
     * @return bool true/false success/failed 成功/失敗
     */
    public function setExclusiveItemtypeAuthority($item_type_id, $exclusive_base_auth, $exclusive_room_auth) {
        // アイテムタイプベース権限を更新する
        $this->removeBaseAuthority($item_type_id);
        if(count($exclusive_base_auth) > 0) {
            for($ii = 0; $ii < count($exclusive_base_auth); $ii++) {
                $this->setBaseAuthority($item_type_id, $exclusive_base_auth[$ii]);
            }
        }
        // アイテムタイプルーム権限を更新する
        $this->setRoomAuthority($item_type_id, $exclusive_room_auth);
        
        return true;
    }
    
    /**
     * set exclusive base authority id
     * 除外ベース権限をセットする
     *
     * @param int $item_type_id item type ID アイテムタイプID
     * @param int $exclusive_base_auth_id exclusice base authority 除外ベース権限ID
     */
    private function setBaseAuthority($item_type_id, $exclusive_base_auth_id) {
        $query = "INSERT INTO ". DATABASE_PREFIX. "repository_item_type_exclusive_base_auth ".
                 "(item_type_id, exclusive_base_auth_id, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete) ".
                 "VALUES ".
                 "(?, ?, ?, ?, ?, ?, ?, ?, ?) ".
                 "ON DUPLICATE KEY UPDATE ".
                 "exclusive_base_auth_id = ?, ".
                 "mod_user_id = ?, ".
                 "mod_date = ?, ".
                 "is_delete = ? ;";
        $params = array();
        // insert
        $params[] = $item_type_id;                            // アイテムタイプID
        $params[] = $exclusive_base_auth_id;                  // 除外ベース権限
        $params[] = $this->Session->getParameter("_user_id"); // 挿入者ユーザーID
        $params[] = $this->Session->getParameter("_user_id"); // 更新者ユーザーID
        $params[] = 0;                                        // 削除者ユーザーID
        $params[] = $this->transStartDate;                    // 挿入日時
        $params[] = $this->transStartDate;                    // 更新日時
        $params[] = "";                                       // 削除日時
        $params[] = 0;                                        // 削除フラグ
        // on duplicate key update
        $params[] = $exclusive_base_auth_id;                  // 除外ベース権限
        $params[] = $this->Session->getParameter("_user_id"); // 更新者ユーザーID
        $params[] = $this->transStartDate;                    // 更新日時
        $params[] = 0;                                        // 削除フラグ
        $this->dbAccess->executeQuery($query, $params);
    }
    
    /**
     * set exclusive room authority id
     * 除外ルーム権限をセットする
     *
     * @param int $item_type_id item type ID アイテムタイプID
     * @param int $exclusive_room_auth_id exclusive room authority ID 除外ルーム権限ID
     */
    private function setRoomAuthority($item_type_id, $exclusive_room_auth_id) {
        $query = "INSERT INTO ". DATABASE_PREFIX. "repository_item_type_exclusive_room_auth ".
                 "(item_type_id, exclusive_room_auth_id, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete) ".
                 "VALUES ".
                 "(?, ?, ?, ?, ?, ?, ?, ?, ?) ".
                 "ON DUPLICATE KEY UPDATE ".
                 "exclusive_room_auth_id = ?, ".
                 "mod_user_id = ?, ".
                 "mod_date = ?, ".
                 "is_delete = ? ;";
        $params = array();
        // insert
        $params[] = $item_type_id;                            // アイテムタイプID
        $params[] = $exclusive_room_auth_id;                  // 除外ルーム権限
        $params[] = $this->Session->getParameter("_user_id"); // 挿入者ユーザーID
        $params[] = $this->Session->getParameter("_user_id"); // 更新者ユーザーID
        $params[] = 0;                                        // 削除者ユーザーID
        $params[] = $this->transStartDate;                    // 挿入日時
        $params[] = $this->transStartDate;                    // 更新日時
        $params[] = "";                                       // 削除日時
        $params[] = 0;                                        // 削除フラグ
        // on duplicate key update
        $params[] = $exclusive_room_auth_id;                  // 除外ルーム権限
        $params[] = $this->Session->getParameter("_user_id"); // 更新者ユーザーID
        $params[] = $this->transStartDate;                    // 更新日時
        $params[] = 0;                                        // 削除フラグ
        $this->dbAccess->executeQuery($query, $params);
    }
    
    /**
     * get itemtype authority
     * アイテムタイプ権限を取得する
     *
     * @param int $item_type_id item type ID アイテムタイプID
     * @param array &$exclusive_base_auth exclusive base authority ID 除外ベース権限配列
     *                                     array[$ii]
     * @param array &$exclusive_room_auth exclusive room authority ID 除外ルーム権限配列
     *                                     array[$ii]
     */
    public function getItemtypeAuthority($item_type_id, &$exclusive_base_auth, &$exclusive_room_auth) {
        // ベース権限情報取得
        $exclusive_base_auth = $this->getBaseAuthority($item_type_id);
        // ルーム権限情報取得
        $exclusive_room_auth = $this->getRoomAuthority($item_type_id);
    }
    
    /**
     * get exclusive base authority
     * 除外ベース権限を取得する
     *
     * @param int $item_type_id item type ID アイテムタイプID
     * @return array exclusive base authority ID 除外ベース権限
     *                array[$ii]["exclusive_base_id"]
     */
    private function getBaseAuthority($item_type_id) {
        $query = "SELECT * ".
                 "FROM ". DATABASE_PREFIX. "repository_item_type_exclusive_base_auth ".
                 "WHERE item_type_id = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_type_id;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        
        return $result;
    }
    
    /**
     * get exclusive room authority
     *
     * @param int $item_type_id item type ID アイテムタイプID
     * @return array exclusive room authority ID 除外ルーム権限
     *                array[$ii]["exclusive_room_auth"]
     */
    private function getRoomAuthority($item_type_id) {
        $query = "SELECT * ".
                 "FROM ". DATABASE_PREFIX. "repository_item_type_exclusive_room_auth ".
                 "WHERE item_type_id = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_type_id;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        
        return $result;
    }
    
    /**
     * get usable item type id
     * 利用可能なアイテムタイプを返す
     *
     * @param int $user_role_id user role ID ユーザーベース権限
     * @param int $user_room_auth_id user room authority ID ユーザールーム権限
     * @param bool $isReturnDeleted not delete only flag 削除されていない物のみを対象とした検索を行うフラグ
     * @return array item type data アイテムタイプデータ
     *                array[$ii]["item_type_id"|...]
     */
    public function getItemtypeDataByUserAuth($user_role_id, $user_room_auth_id, $isReturnDeleted = false) {
        // ユーザー権限で使用可能なアイテムタイプの一覧を返す
        $query = "SELECT * ".
                 "FROM ". DATABASE_PREFIX. "repository_item_type ".
                 "WHERE item_type_id NOT IN (SELECT item_type_id FROM ". DATABASE_PREFIX. "repository_item_type_exclusive_base_auth ".
                                            "WHERE exclusive_base_auth_id = ? AND is_delete = ?) ".
                 "AND item_type_id NOT IN (SELECT item_type_id FROM ". DATABASE_PREFIX. "repository_item_type_exclusive_room_auth ".
                                          "WHERE exclusive_room_auth_id >= ? AND is_delete = ?) ";
        if(!$isReturnDeleted)
        {
                 $query .= "AND is_delete = 0 ;";
        }
        else
        {
                 $query .= ";";
        }
        $params = array();
        $params[] = $user_role_id;
        $params[] = 0;
        $params[] = $user_room_auth_id;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        
        return $result;
    }
    
    /**
     * remove exclusive authority id
     * 除外権限情報を削除する
     *
     * @param int $item_type_id item type ID アイテムタイプID
     */
    public function removeExclusiveItemtypeAuthority($item_type_id) {
        // アイテムタイプベース権限を削除する
        $this->removeBaseAuthority($item_type_id);
        // アイテムタイプルーム権限を削除する
        $this->removeRoomAuthority($item_type_id);
    }
    
    /**
     * remove exclusive base authority id
     * 除外ベース権限情報を削除する
     *
     * @param int $item_type_id item type ID アイテムタイプID
     */
    private function removeBaseAuthority($item_type_id) {
        $query = "UPDATE ". DATABASE_PREFIX. "repository_item_type_exclusive_base_auth ".
                 "SET ".
                 "mod_user_id = ?, ".
                 "del_user_id = ?, ".
                 "mod_date = ?, ".
                 "del_date = ?, ".
                 "is_delete = ? ".
                 "WHERE item_type_id = ? ;";
        $params = array();
        // remove
        $params[] = $this->Session->getParameter("_user_id"); // 更新者ユーザーID
        $params[] = $this->Session->getParameter("_user_id"); // 削除者ユーザーID
        $params[] = $this->transStartDate;                    // 更新日時
        $params[] = $this->transStartDate;                    // 削除日時
        $params[] = 1;                                        // 削除フラグ
        $params[] = $item_type_id;                            // アイテムタイプID
        $this->dbAccess->executeQuery($query, $params);
    }
    
    /**
     * remove exclusive room authority id
     * 除外ルーム権限情報を削除する
     *
     * @param int $item_type_id item type ID アイテムタイプID
     */
    private function removeRoomAuthority($item_type_id) {
        $query = "UPDATE ". DATABASE_PREFIX. "repository_item_type_exclusive_room_auth ".
                 "SET ".
                 "mod_user_id = ?, ".
                 "del_user_id = ?, ".
                 "mod_date = ?, ".
                 "del_date = ?, ".
                 "is_delete = ? ".
                 "WHERE item_type_id = ? ;";
        $params = array();
        // remove
        $params[] = $this->Session->getParameter("_user_id"); // 更新者ユーザーID
        $params[] = $this->Session->getParameter("_user_id"); // 削除者ユーザーID
        $params[] = $this->transStartDate;                    // 更新日時
        $params[] = $this->transStartDate;                    // 削除日時
        $params[] = 1;                                        // 削除フラグ
        $params[] = $item_type_id;                            // アイテムタイプID
        $this->dbAccess->executeQuery($query, $params);
    }
}
?>