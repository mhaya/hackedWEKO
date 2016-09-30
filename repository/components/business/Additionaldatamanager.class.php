<?php

/**
 * Repository Components Business Additional Data Manager Class
 * 付属データ管理ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Additionaldatamanager.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Repository Components Business Additional Data Manager Class
 * 付属データ管理ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Additionaldatamanager extends BusinessBase
{
    /**
     * Get the included data attribute information from the supplied data ID
     * 付属データIDから付属データ属性情報を取得する
     *
     * @param  int   $additionaldata_id additional data ID 付属データID
     * @return array $result            additional data    付属データ
     *                                   array[$ii]["additionaldata_id"|"additionaldata_type_id"|"attribute_id"|"attribute_no"|"attribute_no"|"attribute_value"|...]
     * @throws AppException
     */
    public function getAttrByDataId($additionaldata_id) {
        // IDから付属データを取得
        $query = "SELECT * FROM ". DATABASE_PREFIX. "repository_additionaldata_attr ".
                 "WHERE additionaldata_id = ? ".
                 "AND is_delete = ? ".
                 "ORDER BY attribute_id ASC ;";
        $params = array();
        $params[] = $additionaldata_id;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("Failed get additional data.", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Failed get additional data.");
        }
        
        return $result;
    }
    
    /**
     * Get the included data attribute information from the supplied data attribute ID
     * 付属データ属性IDから付属データ属性情報を取得する
     *
     * @param  int   $additionaldata_id      additional data ID      付属データID
     * @param  int   $additionaldata_type_id additional data type ID 付属データタイプID
     * @param  int   $attribute_id           attribute ID            属性ID
     * @return array $result                 additional data         付属データ
     *                                        array[$ii]["additionaldata_id"|"additionaldata_type_id"|"attribute_id"|"attribute_no"|"attribute_no"|"attribute_value"|...]
     * @throws AppException
     */
    public function getAttrByAttrId($additionaldata_id, $additionaldata_type_id, $attribute_id) {
        // IDから付属データを取得
        $query = "SELECT * FROM ". DATABASE_PREFIX. "repository_additionaldata_attr ".
            "WHERE additionaldata_id = ? ".
            "AND additionaldata_type_id = ? ".
            "AND attribute_id = ? ".
            "AND is_delete = ? ;";
        $params = array();
        $params[] = $additionaldata_id;
        $params[] = $additionaldata_type_id;
        $params[] = $attribute_id;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("Failed get additional data.", __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }

    /**
     * Update additional data attr value
     * 付属データ属性を追加・更新する
     *
     * @param  int    $additionaldata_id      additional data ID      付属データID
     * @param  int    $additionaldata_type_id additional data type ID 付属データタイプID
     * @param  int    $attribute_id           attribute ID            属性ID
     * @param  int    $attribute_no           attribute number        属性通番
     * @param  string $attribute_value        attribute value         属性値
     * @return bool   true/false              success/failed          成功/失敗
     * @throws AppException
     */
    public function upsertAttr($additionaldata_id, $additionaldata_type_id, $attribute_id, $attribute_no, $attribute_value) {
        $query = "INSERT INTO ". DATABASE_PREFIX. "repository_additionaldata_attr ".
            "(additionaldata_id, additionaldata_type_id, attribute_id, attribute_no, attribute_value, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete) ".
            "VALUES ".
            "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ".
            "ON DUPLICATE KEY UPDATE ".
            "attribute_value = VALUES(`attribute_value`), ".
            "mod_user_id = VALUES(`mod_user_id`), ".
            "mod_date = VALUES(`mod_date`), ".
            "is_delete = VALUES(`is_delete`) ;";
        $params = array();
        $params[] = $additionaldata_id;
        $params[] = $additionaldata_type_id;
        $params[] = $attribute_id;
        $params[] = $attribute_no;
        $params[] = $attribute_value;
        $params[] = $this->user_id;
        $params[] = $this->user_id;
        $params[] = 0;
        $params[] = $this->accessDate;
        $params[] = $this->accessDate;
        $params[] = "";
        $params[] = 0;

        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }

        return true;
    }

    /**
     * Delete all the attribute values of the specified accessory data ID
     * 指定された付属データIDの属性値を全て削除する
     *
     * @param  int    $additionaldata_id additional data ID 付属データID
     * @return bool   true/false         success/failed     成功/失敗
     * @throws AppException
     */
    public function deleteAttrById($additionaldata_id) {
        $query = "UPDATE ". DATABASE_PREFIX. "repository_additionaldata_attr ".
            "SET is_delete = ?, mod_user_id = ?, del_user_id = ?, mod_date = ?, del_date = ? ".
            "WHERE additionaldata_id = ? ;";
        $params = array();
        $params[] = 1;
        $params[] = $this->user_id;
        $params[] = $this->user_id;
        $params[] = $this->accessDate;
        $params[] = $this->accessDate;
        $params[] = $additionaldata_id;

        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }

        return true;
    }

    /**
     * Update additional data attr value
     * 属性値のフォーマットチェックを行う
     *
     * @param  int    $additionaldata_type_id additional data type ID 付属データタイプID
     * @param  int    $attribute_id           attribute ID            属性ID
     * @param  string $attribute_value        attribute value         属性値
     * @return bool   true/false              success/failed          成功/失敗
     * @throws AppException
     */
    public function checkAttrFormat($additionaldata_type_id, $attribute_id, $attribute_value) {
        // TODO:DBからフォーマット情報(正規表現パターン)を取得
        // フォーマットが未指定ならチェックを行わない
        // 正規表現によるチェックを行う
    }
}
?>