<?php

/**
 * The number of registered items aggregate common classes
 * 登録アイテム数集計共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryAggregateCalculation.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

 /**
 * WEKO logic-based base class
 * WEKOロジックベース基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryLogicBase.class.php';

/**
 * DB object wrapper Class
 * DBオブジェクトラッパークラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDbAccess.class.php';

/**
 * The number of registered items aggregate common classes
 * 登録アイテム数集計共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositoryAggregateCalculation extends RepositoryLogicBase
{
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $session Session management objects Session管理オブジェクト
     * @param DbObject $dbAccess DB object wrapper Class DBオブジェクトラッパークラス
     * @param string $transStartDate Transaction start date and time トランザクション開始日時
     */
    public function __construct($session, $dbAccess, $transStartDate)
    {
        parent::__construct($session, $dbAccess, $transStartDate);
    }
    
    /**
     * Number of items every public situation summary
     * 公開状況毎のアイテム数集計
     * 
     * @return array $items The number of items of every public situation 公開状況毎のアイテム数
     *                      array["total"|"public"|"private"|"includeFulltext"|"excludeFulltext"]
     */
    public function countItem()
    {
        // set values
        $items = array();
        $items["total"] = $this->cntTotalItems();
        $items["public"] = $this->cntPublicItems();
        $items["private"] = $items["total"] - $items["public"];
        $items["includeFulltext"] = $this->cntFileExistsItems();
        $items["excludeFulltext"] = $items["total"] - $items["includeFulltext"];
        
        return $items;
    }
    
    /**
     * Retrieve items total number
     * アイテム総数を取得
     * 
     * @return int Items total number アイテム総数
     */
    private function cntTotalItems()
    {
        // get total items
        $query = "SELECT count(*) AS RESULT ".
                 "FROM ".DATABASE_PREFIX."repository_item ".
                 "WHERE is_delete = ?;";
        $params = array();
        $params[] = 0;
        
        $result = $this->dbAccess->executeQuery($query, $params);
        return $result[0]["RESULT"];
    }
    
    /**
     * Whether or not the inspection authority of each group has been set for the affiliation index of the item
     * アイテムの所属インデックスに対してグループごとの閲覧権限が設定されているか否か
     * 
     * @return boolean Whether or not the viewing rights has been set 閲覧権限が設定されているか否か
     */
    private function isIndexBrowsingGroups()
    {
        $notNullGroupTable = false;
        $count_query = "SELECT index_id ".
                       "FROM ".DATABASE_PREFIX."repository_index_browsing_groups ;";
        $count_result = $this->dbAccess->executeQuery($count_query);
        if(isset($count_result) && count($count_result) > 0)
        {
            $notNullGroupTable = true;
        }
        return $notNullGroupTable;
    }
    
    /**
     * The number of public items aggregate
     * 公開アイテム数集計
     *
     * @return int The number of public items 公開アイテム数
     */
    private function cntPublicItems()
    {
        $notNullGroupTable = $this->isIndexBrowsingGroups();
        
        // get released items
        $query = "SELECT COUNT( ITEM_IDS.TOTAL ) AS RESULT FROM ( ".
                     "SELECT ITEM.item_id AS TOTAL ".
                     "FROM ".DATABASE_PREFIX."repository_item AS ITEM, ".
                     DATABASE_PREFIX."repository_index AS IDX, ".
                     DATABASE_PREFIX."repository_position_index AS POS, ".
                     DATABASE_PREFIX."repository_index_browsing_authority AS AUTH ";
        $query .=    "WHERE ITEM.is_delete = ? ".
                     "AND ITEM.item_id = POS.item_id ".
                     "AND ITEM.item_no = POS.item_no ".
                     "AND ITEM.shown_status = ? ".
                     "AND ITEM.shown_date <= NOW() ".
                     "AND POS.index_id = AUTH.index_id ";
        if($notNullGroupTable)
        {
            // グループに対して閲覧権限が設定されているものは「公開」ではないため集計から除外する
            $query .=    "AND (".
                     "     POS.index_id NOT IN ( ".
                     "         SELECT GROUPS.index_id ".
                     "         FROM ".DATABASE_PREFIX."repository_index_browsing_groups AS GROUPS ".
                     "         WHERE GROUPS.exclusive_acl_group_id = ? ".
                     "         AND GROUPS.is_delete = ? ".
                     "     ) ".
                     "    ) ";
        }
        $query .=    "AND IDX.index_id = POS.index_id ".
                     "AND IDX.is_delete = ? ".
                     "AND ! ( ".
                     "    AUTH.public_state = ? ".
                     "    OR AUTH.pub_date > NOW() ".
                     "    OR AUTH.exclusive_acl_role_id > ? ".
                     "    OR AUTH.exclusive_acl_room_auth > ? ".
                     ") ".
                     "GROUP BY ITEM.item_id ".
                 ") AS ITEM_IDS; ";
        
        $params = array();
        $params[] = 0;
        $params[] = 1;
        if($notNullGroupTable)
        {
            $params[] = 0;
            $params[] = 0;
        }
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $params[] = -1;
        
        $result = $this->dbAccess->executeQuery($query, $params);
        return $result[0]["RESULT"];
    }
    
    /**
     * Body has the number of items aggregate
     * 本文ありのアイテム数集計
     * 
     * @return int Body has the number of items 本文ありアイテム数
     */
    private function cntFileExistsItems()
    {
        $query = "SELECT COUNT(DISTINCT item_id) AS RESULT ".
                " FROM {repository_file} ".
                " WHERE is_delete = ? ";
        $params = array();
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        return $result[0]["RESULT"];
    }
}
?>
