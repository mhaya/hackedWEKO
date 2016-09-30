<?php

/**
 * Item authority Class
 * アイテムタイプ権限クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Itemtypeauthority.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * アイテムタイプの権限データ取得クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Authority_Itemtypeauthority extends BusinessBase 
{
    /**
     * Check whether is metadata topic hidden or not
     * アイテムのメタデータ項目が非表示かどうか判定する
     *
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item no アイテム通番
     * @param int $attrId Attribute id 属性ID
     *
     * @return bool Is metadata topic hidden or not
     *              メタデータ項目が非表示かどうか
     */
    public function isItemMetadataTopicHidden($itemId, $itemNo, $attrId)
    {
        $query = "SELECT ATTR_TYPE.hidden ".
                " FROM ". DATABASE_PREFIX. "repository_item AS ITEM, ".
                DATABASE_PREFIX. "repository_item_attr_type AS ATTR_TYPE ".
                " WHERE ITEM.item_id = ? ".
                " AND ITEM.item_no = ? ".
                " AND ATTR_TYPE.attribute_id = ? ".
                " AND ITEM.item_type_id = ATTR_TYPE.item_type_id; ";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attrId;
        
        $ret = $this->executeSql($query, $params);
        if(count($ret) > 0 && $ret[0]['hidden'] == 0)
        {
            return false;
        }
        
        return true;
    }   
}
?>