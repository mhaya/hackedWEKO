<?php

/**
 * Action class for add the item type authority
 * アイテムタイプ権限登録用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Adddb.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Item type manager class
 * アイテムタイプ管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/ItemtypeManager.class.php';

/**
 * Action class for add the item type authority
 * アイテムタイプ権限登録用アクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Itemtype_Auth_Adddb extends RepositoryAction
{
    /**
     * Item type ID
     * アイテムタイプID
     * 
     * @var int
     */
    var $item_type_id = null;
    /**
     * Item type base authority
     * アイテムタイプベース権限
     * 
     * @var int
     */
    var $exclusive_base_auth = null;
    /**
     * Item type room authority
     * アイテムタイプルーム権限
     * 
     * @var int
     */
    var $exclusive_room_auth = null;
    
    /**
     * Execute
     * 実行
     *
     * @return string "redirect"/"error" success/failed 成功/失敗
     */
    function executeApp()
    {
        // アイテムタイプ管理クラス
        $itemtypeManager = new Repository_Components_ItemtypeManager($this->Session, $this->Db, $this->TransStartDate);
        
        // カンマ区切りのベース権限文字列を配列にする
        if(strlen($this->exclusive_base_auth) > 0) {
            $base_auth = explode(",", $this->exclusive_base_auth);
        } else {
            // 空配列
            $base_auth = array();
        }
        
        // アイテムタイプ権限をDBに登録する
        $result = $itemtypeManager->setExclusiveItemtypeAuthority($this->item_type_id, $base_auth, $this->exclusive_room_auth);
        if($result) {
            $this->Session->setParameter("redirect_flg", "itemtype");
            return "redirect";
        } else {
            echo "error";
            return "error";
        }
    }
}
?>
