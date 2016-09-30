<?php

/**
 * Item authority Class
 * アイテム権限クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Itemauthority.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Item authority Class
 * アイテムの権限データ取得クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Authority_Itemauthority extends BusinessBase 
{
    /**
     * It determines whether the item has been deleted .
     * アイテムが削除されている。
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item no アイテム通番
     * @return bool　Delete item state. true=>削除されている。false=>削除されていない。
     */
    public function isItemDeleted($itemId, $itemNo)
    {
        $query = "SELECT is_delete ".
                " FROM {repository_item} ".
                " WHERE item_id = ? ".
                " AND item_no = ? ".
                " AND is_delete = ?; ";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = 0;
        $ret = $this->executeSql($query, $params);
        if(count($ret) > 0 && $ret[0]['is_delete'] == 0)
        {
            return false;
        }
        return true;
    }
    
    /**
     * Check whether is file deleted or not
     * ファイルが削除されているかどうか判定する
     *
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item no アイテム通番
     * @param int $attributeId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     *
     * @return bool Is file deleted or not
     *              ファイルが削除されているかどうか。true=>削除されている。false=>削除されていない。
     */
    public function isFileDeleted($itemId, $itemNo, $attributeId, $fileNo)
    {
        $query = "SELECT FILE.is_delete ".
                " FROM ". DATABASE_PREFIX. "repository_file AS FILE ".
                " WHERE FILE.item_id = ? ".
                " AND FILE.item_no = ? ".
                " AND FILE.attribute_id = ? ".
                " AND FILE.file_no = ?; ";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attributeId;
        $params[] = $fileNo;
        
        $ret = $this->executeSql($query, $params);
        if(count($ret) > 0 && $ret[0]['is_delete'] == 0)
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * Access user determines whether it is possible to browse the items items .
     * アクセスユーザーがアイテムの閲覧が可能か判定する。
     * 
     * @param Object $session session data. セッションオブジェクト
     * @param int $itemId Item ID アイテムID
     * @param int $itemNo Item No アイテム通番
     * @return bool permission, 閲覧可否。true=>閲覧可。false=>閲覧不可
     */
    public function isItemPermission($session, $itemId, $itemNo)
    {
        // TODO Validatorクラスの処理を呼び出しているが、Validatorからこちらに移すなど検討が必要
        require_once WEBAPP_DIR. '/modules/repository/validator/Validator_DownloadCheck.class.php';
        
        $repositoryValidator = new Repository_Validator_DownloadCheck();
        $repositoryValidator->setComponents($session, $this->Db);
        
        return $repositoryValidator->checkCanItemAccess($itemId, $itemNo);
    }

    /**
     * Check whether is Item's Contributor or not
     * アイテムの投稿者であるかどうか判定する
     *
     * @param string $userId User ID ユーザーID
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item no アイテム通番
     *
     * @return bool Is file deleted or not
     *              アイテムの投稿者であるか。true=>投稿者である。false=>投稿者では無い。
     */
    public function isItemContributor($userId, $itemId, $itemNo)
    {
        $query = "SELECT ins_user_id ".
                 " FROM {repository_item} ".
                 " WHERE item_id = ? ".
                 " AND item_no = ? ".
                 " AND is_delete = ? ";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = 0;
        $ret = $this->executeSql($query, $params);
        if(count($ret) > 0 && $ret[0]['ins_user_id'] == $userId)
        {
            return true;
        }

        return false;
    }
}
?>