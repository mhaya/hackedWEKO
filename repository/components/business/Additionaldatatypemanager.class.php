<?php

/**
 * Repository Components Business Additional Data Type Manager Class
 * 付属データタイプ管理ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Additionaldatatypemanager.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Repository Components Business Additional Data Type Manager Class
 * 付属データタイプ管理ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Additionaldatatypemanager extends BusinessBase
{
    /**
     * Get the supplied data type information from the supplied data type ID
     * 付属データタイプIDから付属データタイプ情報を取得する
     *
     * @param  int   $additionaldata_type_id additional data type ID   付属データタイプID
     * @return array $result                 additional data type info 付属データタイプ情報
     *                                        array[0]["additionaldata_type_id"|"additionaldata_type_name"|"additionaldata_type_name_english"|...]
     * @throws AppException
     */
    public function getTypeNameByTypeId($additionaldata_type_id) {
        // IDから付属データタイプを取得
        $query = "SELECT * FROM " . DATABASE_PREFIX . "repository_additionaldata_type " .
            "WHERE additionaldata_type_id = ? " .
            "AND is_delete = ? ;";
        $params = array();
        $params[] = $additionaldata_type_id;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("Failed get additional data.", __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
     * Get the included data attribute type information from the supplied data type ID
     * 付属データタイプIDから付属データ属性タイプ情報を取得する
     *
     * @param  int    $additionaldata_type_id additionaldata type ID        付属データタイプID
     * @param  string $lang                   language                      言語名
     * @return array  $result                 additionaldata attr type info 属性タイプ情報
     *                                         array[$ii]["additionaldata_type_id"|"attribute_id"|"language"|"attribute_name"|"show_order"|"input_type"|"length"|"format"|"is_required"|"plural_enable"|"hidden"|...]
     * @throws AppException
     */
    public function getAttrTypeByTypeId($additionaldata_type_id, $lang = "english") {
        // IDから付属データタイプを取得
        $query = "SELECT * FROM " . DATABASE_PREFIX . "repository_additionaldata_attr_type AS TYPE " .
            "INNER JOIN " . DATABASE_PREFIX . "repository_additionaldata_attr_type_name AS NAME " .
            "ON TYPE.additionaldata_type_id = NAME.additionaldata_type_id " .
            "AND TYPE.attribute_id = NAME.attribute_id " .
            "WHERE TYPE.additionaldata_type_id = ? " .
            "AND NAME.language = ? " .
            "AND TYPE.is_delete = ? " .
            "AND NAME.is_delete = ? " .
            "ORDER BY TYPE.show_order ASC ;";
        $params = array();
        $params[] = $additionaldata_type_id;
        $params[] = $lang;
        $params[] = 0;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("Failed get additional data.", __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
     * Get the included data attribute type information from the supplied data type ID
     * 付属データ属性IDから付属データ属性タイプ情報を取得する
     *
     * @param  int    $additionaldata_type_id additionaldata type ID           付属データタイプID
     * @param  int    $attribute_id           additionaldata attribute type ID 付属データ属性タイプID
     * @param  string $lang                   language                         言語名
     * @return array  $result                 additionaldata attr type info    属性タイプ情報
     *                                         array[0]["additionaldata_type_id"|"attribute_id"|"language"|"attribute_name"|"show_order"|"input_type"|"length"|"format"|"is_required"|"plural_enable"|"hidden"|...]
     * @throws AppException
     */
    public function getAttrTypeByAttrId($additionaldata_type_id, $attribute_id, $lang = "english") {
        // IDから付属データタイプを取得
        $query = "SELECT * FROM " . DATABASE_PREFIX . "repository_additionaldata_attr_type AS TYPE " .
            "INNER JOIN " . DATABASE_PREFIX . "repository_additionaldata_attr_type_name AS NAME " .
            "ON TYPE.additionaldata_type_id = NAME.additionaldata_type_id " .
            "AND TYPE.attribute_id = NAME.attribute_id " .
            "WHERE NAME.language = ? " .
            "AND TYPE.additionaldata_type_id = ? " .
            "AND TYPE.attribute_id = ? " .
            "AND TYPE.is_delete = ? " .
            "AND NAME.is_delete = ? ;";
        $params = array();
        $params[] = $lang;
        $params[] = $additionaldata_type_id;
        $params[] = $attribute_id;
        $params[] = 0;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("Failed get additional data.", __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }

        return $result;
    }

    /**
     * Get the all candidate data information from the supplied data type ID
     * 付属データタイプIDから選択肢情報を全て取得する
     *
     * @param  int    $additionaldata_type_id additionaldata type ID        付属データタイプID
     * @param  string $language               language                      言語名
     * @return array  $result                 candidate info                選択肢情報配列
     *                                         array[$ii]["attribute_id"|"candidate_value"|"candidate_label"]
     * @throws AppException
     */
    public function getCandidateValueByTypeId($additionaldata_type_id, $language) {
        $query = "SELECT TYPE.attribute_id, CAND.candidate_value, CANL.candidate_label " .
                 "FROM {repository_additionaldata_attr_type} AS TYPE " .
                 "INNER JOIN {repository_additionaldata_attr_candidate} AS CAND " .
                 "ON TYPE.additionaldata_type_id = CAND.additionaldata_type_id AND TYPE.attribute_id = CAND.attribute_id " .
                 "INNER JOIN {repository_additionaldata_attr_candidate_label} AS CANL ".
                 "ON CAND.additionaldata_type_id = CANL.additionaldata_type_id  AND CAND.attribute_id = CANL.attribute_id AND CAND.candidate_no = CANL.candidate_no " .
                 "WHERE TYPE.additionaldata_type_id = ? " .
                 "AND CANL.language = ? ".
                 "AND TYPE.is_delete = ? " .
                 "AND CAND.is_delete = ? " .
                 "AND CANL.is_delete = ? " .
                 "ORDER BY TYPE.show_order ASC, CANL.show_order ASC; ";
        $params = array();
        $params[] = $additionaldata_type_id;
        $params[] = $language;
        $params[] = 0;
        $params[] = 0;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("Failed get additional data candidate.", __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }

        return $result;
    }
}
?>