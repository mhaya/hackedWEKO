<?php
/**
 * Business clas for robotlist management base
 * ロボットリスト管理基底ビジネスクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Robotlistbase.class.php 69174 2016-06-22 06:43:30Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Business Logic base class
 * ビジネスロジック基底クラス
 * 
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';

/**
 * Business clas for robotlist management base
 * ロボットリスト管理基底ビジネスクラス
 * 
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Robotlistbase extends BusinessBase
{
    /**
     * Get robotlist master file
     * ロボットリストマスタファイルを取得する
     * 
     * @param $listId Robotlist master ID
     *                ロボットリストマスタ通番
     *
     * @return array Robotlist master information
     *               ロボットリストマスタ情報
     *               array["header"|"word"]["Version"|"Date"|"Revision"|...]
     */
    public function getRobotList($listId)
    {
        $query = "SELECT robotlist_url " . 
                 "FROM " . DATABASE_PREFIX . "repository_robotlist_master " . 
                 "WHERE robotlist_id = ? AND is_delete = ? ; ";
        
        $params = array();
        $params[] = $listId;
        $params[] = 0;
        
        $result = $this->Db->execute($query, $params);
        
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // access to robot list
        $info = explode("\n", file_get_contents($result[0]["robotlist_url"]));
        
        // connection error
        if ($info == null) {
            return false;
        }
        
        $robotlist = $this->createRobotListArray($info);
        
        // バージョン情報が存在しない
        if ( empty($robotlist["header"]["Version"]) ) {
            return false;
        }
        
        return $robotlist;
    }
    
    /**
     * Update robotlist table
     * ロボットリストテーブルを更新する
     * 
     * @param int $robotlistId Robotlist master ID
     *                         ロボットリストマスタ通番
     * @param array $robotList Robotlist data list
     *                         ロボットリストデータ一覧
     *                         array["word"][$ii]
     */
    public function updateRobotList($robotlistId, $robotList)
    {
        $result = $this->getRobotMaster($robotlistId);
        
        if ($robotList["header"]["Version"] > $result[0]["robotlist_version"]) 
        {
            // update robot list master
            $this->updateRobotListMaster($robotlistId, $robotList["header"]);
            
            // 新しいロボットリストを追加
            $this->updateRobotListData($robotlistId, $robotList);
            
            // 新しいロボットリストにないデータを削除
            $this->deleteNotExistRobotList($robotlistId, $robotList);
        }
    }
    
    /**
     * Delete data not to exist new robotlist data
     * 新しいロボットリストにないデータを削除
     * 
     * @param int $robotlistId Robotlist master ID
     *                         ロボットリストマスタ通番
     * @param array $robotList Robotlist data list
     *                         ロボットリストデータ一覧
     *                         array["word"][$ii]
     */
    private function deleteNotExistRobotList($robotlistId, $robotList)
    {
        $current = $this->getCurrentRobotListData($robotlistId);
        
        for ($ii = 0; $ii < count($current); $ii++)
        {
            $found = false;
            for ($jj = 0; $jj < count($robotList["word"]); $jj++){
                if (strcmp($current[$ii]["word"], $robotList["word"][$jj]) == 0) {
                    $found = true;
                }
            }
            
            if ($found == false){
                $query = "UPDATE " . DATABASE_PREFIX . "repository_robotlist_data " . 
                         "SET del_user_id = ?, del_date = ?, is_delete = ? " . 
                         "WHERE robotlist_id = ? AND list_id = ? ; ";
                
                $params = array();
                $params[] = $this->user_id;
                $params[] = $this->accessDate;
                $params[] = 1;
                $params[] = $robotlistId;
                $params[] = $current[$ii]["list_id"];
                
                $update = $this->Db->execute($query, $params);
                
                if($update === false)
                {
                    $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                    throw new AppException($this->Db->ErrorMsg());
                }
            }
        }
    }
    
    /**
     * Organize robotlist master information gotten
     * 取得したロボットリストマスタ情報を整理する
     * 
     * @param array $info Robotlist master information
     *                    ロボットリストマスタ情報
     *                    array[$ii]
     *
     * @return array Robotlist master information organized
     *               整理したロボットリストマスタ情報
     *               array["header"|"word"]["Version"|"Date"|"Revision"|...]
     */
    private function createRobotListArray($info)
    {
        $headers = array("Version", "Date", "Revision", "Author");
        
        $robotlist = array();
        // 初期化
        $robotlist["header"] = array();
        for($cnt = 0; $cnt < count($headers); $cnt++)
        {
            $robotlist["header"][$headers[$cnt]] = "";
        }
        
        for($ii = 0; $ii < count($info); $ii++) {
            if (strcmp(substr($info[$ii], 0, 1), "#") == 0) {
                // $ $ の形式か確認
                if (preg_match('/\$.+\$/', preg_quote($info[$ii]), $matches) == 1)
                {
                    $data = preg_replace('/\\\\/', "", $matches[0]);
                    
                    // $を削除
                    $data = substr($data , 1 , strlen($data));
                    $data = substr($data , 0 , strlen($data) - 1);
                    $data = trim($data);
                    
                    // ヘッダの種類を取得
                    $values = explode(":", $data);
                    $header = trim($values[0]);
                    
                    // 数字部分だけを取り出す
                    $headerValue = strstr($data, ":");
                    $headerValue = trim(substr($headerValue , 1, strlen($headerValue)));
                    
                    for ($jj = 0; $jj < count($headers); $jj++) {
                        if (strcmp($headers[$jj], $header) == 0) {
                            $robotlist["header"][$headers[$jj]] = $headerValue;
                        }
                    }
                }
            }
            else {
                if (!empty($info[$ii])){
                    $robotlist["word"][] = trim($info[$ii]);
                }
            }
        }
        
        return $robotlist;
    }
    
    /**
     * Get robotlist master current registed
     * 現在登録されているロボットリストマスタを取得する
     *
     * @param int $robotlistId Robotlist master ID
     *                         ロボットリストマスタ通番
     */
    private function getRobotMaster($robotlistId) {
        $query = "SELECT * " . 
                 "FROM " . DATABASE_PREFIX . "repository_robotlist_master " . 
                 "WHERE robotlist_id = ? AND is_delete = ? ; ";
        
        $params = array();
        $params[] = $robotlistId;
        $params[] = 0;
        
        $result = $this->Db->execute($query, $params);
        
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    
    /**
     * Update version etc of robotlist master table
     * ロボットリストマスタテーブルのバージョン等を更新する
     *
     * @param int $robotlistId Robotlist master ID
     *                         ロボットリストマスタ通番
     * @param array $header Robotlist master header information
     *                      ロボットリストマスタヘッダ情報
     *                      array["Version"|"Date"|"Revision"|...]
     */
    private function updateRobotListMaster($robotlistId, $header) {
        $query = "UPDATE " . DATABASE_PREFIX . "repository_robotlist_master " . 
                 "SET robotlist_version = ?, " . 
                 "robotlist_date = ?, " . 
                 "robotlist_revision = ?, " . 
                 "robotlist_author = ?, " . 
                 "mod_user_id = ?, " . 
                 "mod_date = ? " . 
                 "WHERE robotlist_id = ? ; ";
        
        $params = array();
        $params[] = $header["Version"]; 
        $params[] = $header["Date"];
        $params[] = $header["Revision"];
        $params[] = $header["Author"];
        $params[] = $this->user_id;
        $params[] = $this->accessDate;
        $params[] = $robotlistId;
        
        $update = $this->Db->execute($query, $params);
        
        if($update === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    
    /**
     * Get current robotlist data
     * 現在のロボットリストデータを取得する
     *
     * @param int $robotlistId Robotlist master ID
     *                         ロボットリストマスタ通番
     * 
     * @return array Current robotlist data
     *               現在のロボットリストデータ
     *               array[$ii]["list_id"|"robotlist_id"|"word"|...]
     *               
     */
    private function getCurrentRobotListData($robotlistId){
        $query = "SELECT * " . 
                 "FROM " . DATABASE_PREFIX . "repository_robotlist_data " .
                 "WHERE robotlist_id = ? AND is_delete = ? ; ";
        
        $params = array();
        $params[] = $robotlistId;
        $params[] = 0;
        
        $result = $this->Db->execute($query, $params);
        
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
     * Update robotlist data table
     * ロボットリストデータテーブルを更新する
     *
     * @param int $robotlistId Robotlist master ID
     *                         ロボットリストマスタ通番
     * @param array $robotList Robotlist data list
     *                         ロボットリストデータ一覧
     *                         array["word"][$ii]
     */
    private function updateRobotListData($robotlistId, $robotList)
    {
        $current = $this->getCurrentRobotListData($robotlistId);
        
        for ($ii = 0; $ii < count($robotList["word"]); $ii++)
        {
            $found = false;
            
            for ($jj = 0; $jj < count($current); $jj++) {
                if (strcmp($current[$jj]["word"], $robotList["word"][$ii]) == 0) 
                {
                    $query = "UPDATE " . DATABASE_PREFIX . "repository_robotlist_data " . 
                             "SET mod_user_id = ?, mod_date = ? " . 
                             "WHERE robotlist_id = ? AND list_id = ? ; ";
                    
                    $params = array();
                    $params[] = $this->user_id;
                    $params[] = $this->accessDate;
                    $params[] = $robotlistId;
                    $params[] = $current[$jj]["list_id"];
                    
                    $result = $this->Db->execute($query, $params);
                    
                    if($result === false) {
                        $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                        throw new AppException($this->Db->ErrorMsg());
                    }
                    
                    $found = true;
                    
                    // 次のロボットリストデータの更新へ
                    break;
                }
            }
            
            // ないなら追加
            if ($found == false){
                $this->insertRobotListData($robotlistId, $robotList["word"][$ii]);
            }
        }
        
    }
    
    /**
     * Insert robotlist data
     * ロボットリストデータを追加する
     *
     * @param int $robotlistId Robotlist master ID
     *                         ロボットリストマスタ通番
     * @param string $word IP address or user agent of robotlist
     *                     ロボットリストのIPアドレスまたはユーザーエージェント
     */
    private function insertRobotListData($robotlistId, $word){
        $query = "INSERT INTO " . DATABASE_PREFIX . "repository_robotlist_data " . 
                 "(robotlist_id, word, status, ins_user_id , mod_user_id, ins_date, mod_date, is_delete) " . 
                 "VALUES (?, ?, ?, ?, ?, ?, ?, ?) ; ";
        
        $params = array();
        $params[] = $robotlistId;
        $params[] = $word;
        $params[] = "0";
        $params[] = $this->user_id;
        $params[] = $this->user_id;
        $params[] = $this->accessDate;
        $params[] = $this->accessDate;
        $params[] = 0;
        
        $result = $this->Db->execute($query, $params);
        
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    
    /**
     * Lock process deleting robotlist log
     * ロボットリストログ削除処理用のロックを取得する
     *
     */
    public function lockRobotListTable() {
        $this->updateRobotListStatus(1);
    }
    
    /**
     * Unlock process deleting robotlist log
     * ロボットリストログ削除処理用のロックを解放する
     *
     */
    public function unlockRobotListTable() {
        $this->updateRobotListStatus(0);
    }
    
    /**
     * Update status of process deleting robotlist log
     * ロボットリストログ削除処理のステータスを更新する
     *
     * @param int $status Status of process deleting robotlist log(0: unlock, 1: lock)
     *                    ロボットリストログ削除処理のステータス(0：ロック解除、1:ロック取得)
     */
    private function updateRobotListStatus($status)
    {
        $query = "UPDATE ". DATABASE_PREFIX. "repository_lock ". 
                 "SET status = ? " . 
                 "WHERE process_name = ? ; ";
        
        $params = array();
        $params[] = $status;
        $params[] = "Repository_Action_Common_Robotlist";
        
        $result = $this->Db->execute($query, $params);
        
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
}
?>