<?php

/**
 * Repository Components Business Index Manager Class
 * インデックス情報処理ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Indexmanager.class.php 70936 2016-08-09 09:53:57Z keiya_sugimoto $
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
 * Repository Components Business Index Manager Class
 * インデックス情報処理ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Indexmanager extends BusinessBase
{
    /**
     * Index ID of root index
     * ルートインデックスのインデックスID
     * 
     * @var int
     */
    const ID_ROOT_INDEX = 0;
    
    /**
      * Check the public and private for the specified index recursively
      * 指定したインデックスの公開・非公開を再帰的にチェックする
      *
      * @param int $indexId Index ID インデックスID
      * @return string "public" or "private" 「公開」か「非公開」
      */
    public function checkIndexStateRecursive($indexId) {
        $status = $this->checkIndexState($indexId);
        if($status == "private")
        {
            return "private";
        }
        else
        {
            $result = $this->getIndexAllData($indexId);
            if($result[0]["parent_index_id"] == self::ID_ROOT_INDEX)
            {
                return "public";
            }
            else
            {
                return $this->checkIndexStateRecursive($result[0]["parent_index_id"]);
            }
        }
    }
    
    /**
      * Check the public and private for the specified index
      * 指定したインデックスの公開・非公開をチェックする
      *
      * @param int $indexId Index ID インデックスID
      * @return string "public" or "private" 「公開」か「非公開」
      */
    public function checkIndexState($indexId) {
        // インデックス自身の公開フラグのチェック
        $result = $this->getIndexAllData($indexId);
        if($result[0]["public_state"] == 0) {
            return "private";
        }
        
        // インデックス閲覧権限のチェック
        $result = $this->getIndexBrowsingAuthority($indexId);
        // なんらかの権限情報が存在する場合
        if(count($result) > 0) {
            if($result[0]["public_state"] == 0 ||          // 公開フラグが非公開状態
                $result[0]["exclusive_acl_role_id"] > 0 ||  // 閲覧ベース権限に制限がかかっている
                $result[0]["exclusive_acl_room_auth"] > -1) // 閲覧ルーム権限に制限がかかっている
            {
                return "private";
            }
        }
        
        // インデックス閲覧グループ権限のチェック
        $result = $this->getIndexBrowsingGroups($indexId);
        // なんらかの権限情報が存在する場合
        for($ii=0; $ii < count($result); $ii++) {
            // 「非会員」のグループ権限が除外権限に設定されている場合
            if($result[$ii]["exclusive_acl_group_id"] == 0) {
                return "private";
            }
        }
        
        return "public";
    }

    /**
      * Check the harvest public and private for the specified index recursively
      * 指定したインデックスのハーベスト公開・非公開を再帰的にチェックする
      *
      * @param int $indexId Index ID インデックスID
      * @return string "public" or "private" 「公開」か「非公開」
      */
    public function checkIndexHarvestStateRecursive($indexId) {
        $status = $this->checkIndexHarvestState($indexId);
        if($status == "private")
        {
            return "private";
        }
        else
        {
            $result = $this->getIndexAllData($indexId);
            if($result[0]["parent_index_id"] == self::ID_ROOT_INDEX)
            {
                return "public";
            }
            else
            {
                return $this->checkIndexHarvestStateRecursive($result[0]["parent_index_id"]);
            }
        }
    }
    
    /**
      * Check the harvest public and private for the specified index
      * 指定したインデックスのハーベスト公開・非公開をチェックする
      *
      * @param int $indexId Index ID インデックスID
      * @return string "public" or "private" 「公開」か「非公開」
      */
    private function checkIndexHarvestState($indexId) {
        // インデックス自身のハーベスト公開フラグのチェック
        $result = $this->getIndexAllData($indexId);
        if($result[0]["harvest_public_state"] == 0) {
            return "private";
        }
        else
        {
            return "public";
        }
    }

    /**
     * Get all index data
     * 全てのインデックス情報を取得する
     *
     * @param int $indexId index ID インデックスID
     * @return array index data 全インデックスデータ
     *                array[$ii]["index_id"|"index_name"|"index_name_english"...]
     * @throws AppException
     */
    private function getIndexAllData($indexId) {
        $query = "SELECT * FROM ".DATABASE_PREFIX."repository_index ".
                 "WHERE index_id = ? ".
                 "AND is_delete = ?;";
        $params = array();
        $params[] = $indexId;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("");
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }

    /**
     * Get index browsing authority
     * インデックス閲覧権限取得
     *
     * @param int $indexId index ID インデックスID
     * @return array index authority data インデックス閲覧権限データ
     *                array[0]["index_id"|"exclusive_acl_role_id"|"exclusive_acl_room_auth"|"public_state"|"pub_date"|"harvesting_public_state"...]
     * @throws AppException
     */
    private function getIndexBrowsingAuthority($indexId) {
        $query = "SELECT * FROM ". DATABASE_PREFIX. "repository_index_browsing_authority ".
                 "WHERE index_id = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $indexId;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("");
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }

    /**
     * Get index browsing group authority
     * インデックス閲覧グループ権限取得
     *
     * @param int $indexId index ID インデックスID
     * @return array index authority data インデックス閲覧グループ権限データ
     *                array[0]["index_id"|"exclusive_acl_group_id"...]
     * @throws AppException
     */
    private function getIndexBrowsingGroups($indexId) {
        $query = "SELECT * FROM ". DATABASE_PREFIX. "repository_index_browsing_groups ".
                 "WHERE index_id = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $indexId;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("");
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
}
?>