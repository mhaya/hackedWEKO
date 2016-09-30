<?php

/**
 * Repository Components Business Index Additional Data Manager Class
 * インデックス付属情報管理ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Indexadditionaldatamanager.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Repository Components Business Index Additionaldata Manager Class
 * インデックス付属情報管理ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Indexadditionaldatamanager extends BusinessBase
{
    /**
     * Get the additional information of the index
     * インデックスの付属情報を取得する
     *
     * @param int      $index_id index ID インデックスID
     * @param   int    $type_id additional data type ID 付属データタイプID
     * @param string   $lang language 言語名
     * @return  array  $result index additional data インデックス付属メタデータ
     *                          array[0]["additionaldata_type_id"|"additionaldata_type_name"|
     *                                                            "additionaldata_type_name_english"|
     *                                                            "output_flag"|
     *                                                            "attribute"][0]["attribute_id"|
     *                                                                            "attribute_name"|
     *                                                                            "input_type"|
     *                                                                            "format"|
     *                                                                            "is_required"|
     *                                                                            "plural_enable"|
     *                                                                            "hidden"|
     *                                                                            "attribute_value"][$ii]
     */
    public function getIndexAdditionalDataById($index_id, $type_id, $lang="english") {
        // データ初期化
        $additionaldata_id = 0;
        $output_flag = 0;
        
        // 関連テーブルからインデックスIDに紐付く付属データを取得
        $result = $this->searchIndexAdditionalDataById($index_id, $type_id);
        if(count($result) > 0) {
            $additionaldata_id = $result[0]["additionaldata_id"];
            $output_flag = $result[0]["output_flag"];
        }
        
        // 管理クラス
        $additionalDataManager = BusinessFactory::getFactory()->getBusiness("businessAdditionaldatamanager");
        $additionalDataTypeManager = BusinessFactory::getFactory()->getBusiness("businessAdditionaldatatypemanager");
        
        $dataList = array();
        // 付属データタイプ情報を取得する
        //
        $type = $additionalDataTypeManager->getTypeNameByTypeId($type_id, $lang);
        $dataList[0]["additionaldata_type_id"] = $type[0]["additionaldata_type_id"];
        $dataList[0]["additionaldata_type_name"] = $type[0]["additionaldata_type_name"];
        $dataList[0]["additionaldata_type_name_english"] = $type[0]["additionaldata_type_name_english"];
        // 付属データ属性を取得する
        $dataList[0]["attribute"] = $additionalDataTypeManager->getAttrTypeByTypeId($type[0]["additionaldata_type_id"], $lang);
        // 付属データ属性を取得する
        for($ii = 0; $ii < count($dataList[0]["attribute"]); $ii++) {
            // データが存在しない場合は空値を詰める
            if($additionaldata_id == 0) {
                $dataList[0]["attribute"][$ii]["attribute_value"][0] = "";
                continue;
            }
            // value値を取得する
            $attr = $additionalDataManager->getAttrByAttrId($additionaldata_id,
                                                            $dataList[0]["additionaldata_type_id"],
                                                            $dataList[0]["attribute"][$ii]["attribute_id"]);
            for($jj = 0; $jj < count($attr); $jj++) {
                $dataList[0]["attribute"][$ii]["attribute_value"][$jj] = $attr[$jj]["attribute_value"];
            }
        }
        $dataList[0]["output_flag"] = $output_flag;
        
        return $dataList;
    }

    /**
     * Update the additional information of the index
     * インデックスの付属情報を更新する
     *
     * @param   int    $index_id               index ID                     インデックスID
     * @param   int    $additionaldata_type_id additional data type ID      付属データタイプID
     * @param   int    $output_flag            output flag                  出力フラグ
     * @param   array  $attr_id_list           attribute ID array           属性ID配列
     * @param   array  $attr_value_list        attribute value array        属性値配列
     * @return  bool   true/false              update success/update failed 更新成功/更新失敗
     */
    public function upsertIndexAdditionalData($index_id, $additionaldata_type_id, $output_flag, $attr_id_list, $attr_value_list) {
        // 新規か更新のチェック
        $isUpdate = false;
        $additionaldata_id = 0;
        // インデックス付属データを取得
        $result = $this->searchIndexAdditionalDataById($index_id, $additionaldata_type_id);
        for($ii = 0; $ii < count($result); $ii++) {
            // 付属データのタイプIDが既に登録されていた場合は「更新」と判定する
            if($result[$ii]["additionaldata_type_id"] == $additionaldata_type_id) {
                $isUpdate = true ;
                $additionaldata_id = $result[$ii]["additionaldata_id"];
                break;
            }
        }
        
        // 付属データ管理クラス
        $additionaldataManager = BusinessFactory::getFactory()->getBusiness("businessAdditionaldatamanager");
        
        if($isUpdate) {
            $this->updateOutputFlag($index_id, $additionaldata_id, $additionaldata_type_id, $output_flag);
        } else {
            // シーケンスIDを取得
            $additionaldata_id = $this->Db->nextSeq("repository_additionaldata_attr");
            // インデックス付属情報テーブルにレコードを追加
            $this->insertIndexAdditionalData($index_id, $additionaldata_id, $additionaldata_type_id, $output_flag);
        }

        // 属性値を更新or追加
        for($ii = 0; $ii < count($attr_id_list); $ii++) {
            // TODO:第4引数はattribute_noであるが固定値を可変にすること
            $additionaldataManager->upsertAttr($additionaldata_id, $additionaldata_type_id, $attr_id_list[$ii], 1, $attr_value_list[$ii]);
        }
    }

    /**
     * Get a list of the index included information that output settings are
     * 出力設定がされているインデックス付属情報の一覧を取得する
     * 
     * @param  int   $additionaldata_type_id additional data type ID      付属データタイプID
     * @return array $outputIndex index additional data list インデックス付属情報一覧
     *                             array[$ii]["index_id"|"additionaldata_id"|"additionaldata_type_id"|"output_flag"]
     */
    public function getOutputAdditionalDataByTypeId($additionaldata_type_id) {
        // 出力されるインデックス付属データを全て取得する
        // 登録されている中で出力フラグがONかつ全体に公開のインデックスのみ
        $query = "SELECT index_id, additionaldata_id, additionaldata_type_id  FROM ". DATABASE_PREFIX. "repository_index_additionaldata ".
                 "WHERE additionaldata_type_id = ? ".
                 "AND output_flag = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $additionaldata_type_id;
        $params[] = 1;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        
        // 取得した一覧から非公開のインデックスを除外する
        $indexManager = BusinessFactory::getFactory()->getBusiness("businessIndexmanager");
        $outputIndex = array();
        for($ii = 0; $ii < count($result); $ii++) {
            if($indexManager->checkIndexState($result[$ii]["index_id"]) == "public") {
                $outputIndex[] = $result[$ii];
            }
        }
        
        return $outputIndex;
    }

    /**
     * Return to get the supplied data ID stick string to index
     * インデックスに紐付く付属データIDを取得して返す
     * 
     * @param  int   $index_id index ID インデックスID
     * @return array $result index additional data list インデックス付属情報一覧
     *                        array[$ii]["index_id"|"additionaldata_id"|"additionaldata_type_id"|"output_flag"]
     * @throws AppException
     */
    public function getAdditionalDataIdByIndexId($index_id) {
        // 出力されるインデックス付属データを全て取得する
        // 登録されている中で出力フラグがONかつ全体に公開のインデックスのみ
        $query = "SELECT *  FROM ". DATABASE_PREFIX. "repository_index_additionaldata ".
                 "WHERE index_id = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $index_id;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("Failed get index additionaldata id.", __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }

    /**
     * Update output flag in index additional data table
     * インデックス付属情報テーブルの出力フラグを更新する
     *
     * @param  int  $index_id               index ID                     インデックスID
     * @param  int  $additionaldata_id      additional data ID           付属データID
     * @param  int  $additionaldata_type_id additional data type ID      付属データタイプID
     * @param  int  $output_flag            output flag                  出力フラグ
     * @return bool true/false              update success/update failed 更新成功/更新失敗
     * @throws AppException
     */
    public function updateOutputFlag($index_id, $additionaldata_id, $additionaldata_type_id, $output_flag) {
        $query = "UPDATE ". DATABASE_PREFIX. "repository_index_additionaldata ".
            "SET output_flag = ?, mod_user_id = ?, mod_date = ? ".
            "WHERE index_id = ? ".
            "AND additionaldata_id = ? ".
            "AND additionaldata_type_id = ? ;";
        $params = array();
        $params[] = $output_flag;
        $params[] = $this->user_id;
        $params[] = $this->accessDate;
        $params[] = $index_id;
        $params[] = $additionaldata_id;
        $params[] = $additionaldata_type_id;

        $result = $this->Db->execute($query,$params);
        if($result === false) {
            $this->errorLog("Failed udpate index additionaldata OutputFlag.", __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }

        return true;
    }

    /**
     * Delete index additional data
     * インデックス付属情報を論理削除する
     *
     * @param  int  $index_id  index ID                     インデックスID
     * @return bool true/false delete success/delete failed 削除成功/削除失敗
     * @throws AppException
     */
    public function deleteAdditionalDataIdByIndexId($index_id) {
        $additionalDataManager = BusinessFactory::getFactory()->getBusiness("businessAdditionaldatamanager");
        
        // 付属データIDから実データを削除する
        $additional_id_list = $this->getAdditionalDataIdByIndexId($index_id);
        for($ii = 0; $ii < count($additional_id_list); $ii++) {
            $additionalDataManager->deleteAttrById($additional_id_list[$ii]["additionaldata_id"]);
        }
        
        // インデックス付属テーブルのレコードを論理削除する
        $query = "UPDATE ". DATABASE_PREFIX. "repository_index_additionaldata ".
            "SET is_delete = ?, mod_user_id = ?, del_user_id = ?, mod_date = ?, del_date = ? ".
            "WHERE index_id = ? ;";
        $params = array();
        $params[] = 1;
        $params[] = $this->user_id;
        $params[] = $this->user_id;
        $params[] = $this->accessDate;
        $params[] = $this->accessDate;
        $params[] = $index_id;

        $result = $this->Db->execute($query,$params);
        if($result === false) {
            $this->errorLog("Failed delete index additionaldata.", __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }

        return true;
    }

    /**
     * Insert new record into index additional data table
     * インデックス付属情報テーブルに新規レコードを追加する
     *
     * @param  int  $index_id               index ID                インデックスID
     * @param  int  $additionaldata_id      additional data ID      付属データID
     * @param  int  $additionaldata_type_id additional data type ID 付属データタイプID
     * @param  int  $output_flag            output flag             出力フラグ
     * @return bool true/false              add success/add failed  追加成功/追加失敗
     * @throws AppException
     */
    private function insertIndexAdditionalData($index_id, $additionaldata_id, $additionaldata_type_id, $output_flag) {
        $query = "INSERT INTO ". DATABASE_PREFIX. "repository_index_additionaldata ".
                 "(index_id, additionaldata_id, additionaldata_type_id, output_flag, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete) ".
                 "VALUES ".
                 "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ;";
        $params = array();
        $params[] = $index_id;
        $params[] = $additionaldata_id;
        $params[] = $additionaldata_type_id;
        $params[] = $output_flag;
        $params[] = $this->user_id;
        $params[] = $this->user_id;
        $params[] = "";
        $params[] = $this->accessDate;
        $params[] = $this->accessDate;
        $params[] = "";
        $params[] = 0;
        
        $result = $this->Db->execute($query,$params);
        if($result === false) {
            $this->errorLog("Failed insert index additionaldata.", __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return true;
    }

    /**
     * Find index additional data from index ID
     * インデックスIDからインデックス付属情報を探す
     *
     * @param  int   $index_id index ID インデックスID
     * @param  int   $type_id  additional data type ID 付属データタイプID
     * @return array $result   index additional data list インデックス付属情報一覧
     *                          array[0]["index_id"|"additionaldata_id"|"additionaldata_type_id"|"output_flag"]
     * @throws AppException
     */
    private function searchIndexAdditionalDataById($index_id, $type_id) {
        $query = "SELECT * FROM ". DATABASE_PREFIX. "repository_index_additionaldata ".
            "WHERE index_id = ? ".
            "AND additionaldata_type_id = ? ".
            "AND is_delete = ? ;";
        $params = array();
        $params[] = $index_id;
        $params[] = $type_id;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog("Failed get index additional data.", __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
}
?>