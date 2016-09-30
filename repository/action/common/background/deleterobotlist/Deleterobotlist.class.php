<?php
/**
 * Action class for background process robotlist log delete
 * ロボットリストログ削除非同期処理用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Deleterobotlist.class.php 69174 2016-06-22 06:43:30Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 * 
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Base class for carrying out asynchronously and recursively possibility is the ability to process a long period of time
 * 長時間処理する可能性がある機能を非同期かつ再帰的に実施するための基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/BackgroundProcess.class.php';

/**
 * Action class for background process robotlist log delete
 * ロボットリストログ削除非同期処理用アクションクラス
 * 
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Common_Background_Deleterobotlist extends BackgroundProcess
{
    /**
     * Background process name for lock table
     * ロックテーブル用非同期処理名
     *
     * @var string
     */
    const PARAM_NAME = "Repository_Action_Common_Background_Deleterobotlist";
    
    /**
     * Constructer
     * コンストラクタ
     *
     */
    public function __construct()
    {
        parent::__construct(self::PARAM_NAME);
    }
    
    /**
     * Check undeleted log
     * 削除されていないログがあるか確認する
     *
     * @param string $target Not use argument (prepared because it is necessary for extend)
     *                        使用していない引数(継承に必要であるため用意している)
     *
     * @return boolean Whether or not to delete log
     *                 ログを削除するか否か
     */
    protected function prepareBackgroundProcess(&$target)
    {
        $undeleted = $this->getUndeletedList();
        
        // 削除するデータがないならロックを解放して終了
        if (count($undeleted) == 0) {
            $this->infoLog("businessRobotlistbase", __FILE__, __CLASS__, __LINE__);
            $businessRobotlistbase = BusinessFactory::getFactory()->getBusiness("businessRobotlistbase");
            
            $businessRobotlistbase->unlockRobotListTable();
            return false;
        }
        
        // ロボットリストバックグラウンドロックがされていないなら終了
        $lockList = $this->checkRobotlistLock();
        
        if ($lockList == true) {
            return false;
        }
        
        return true;
    }
    
    /** 
     * Delete log by background process
     * 非同期処理でログを削除する
     *
     * @param string $target Not use argument (prepared because it is necessary for extend)
     *                        使用していない引数(継承に必要であるため用意している)
     */
    protected function executeBackgroundProcess(&$target) 
    {
        $undeleted = $this->getUndeletedList();
        
        // logmanager class
        $this->infoLog("businessLogmanager", __FILE__, __CLASS__, __LINE__);
        $logmanager = BusinessFactory::getFactory()->getBusiness("businessLogmanager");
        
        // ログ削除
        $logmanager->removeLogByRobotlistWord($undeleted[0]["del_column"], $undeleted[0]["word"]);
        
        // 更新済みにする
        $this->updateStatus($undeleted[0]["robotlist_id"], $undeleted[0]["list_id"]);
    }
    
    /**
     * Update status of robotlist data
     * ロボットリストデータのステータスを更新する
     *
     * @param int $robotlistId Robotlist ID
     *                         ロボットリスト通番
     * @param int $listId Robotlist master file ID
     *                    ロボットリストのマスターファイル通番
     */
    private function updateStatus($robotlistId, $listId)
    {
        $query = "UPDATE ". DATABASE_PREFIX. "repository_robotlist_data ". 
                 "SET status = ? " . 
                 "WHERE robotlist_id = ? AND list_id = ? ; ";
        
        $params = array();
        $params[] = 1;
        $params[] = $robotlistId;
        $params[] = $listId;
        
        $result = $this->Db->execute($query, $params);
        
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    
    /**
     * Get robotlist data not delete log
     * ログ削除を実施していないロボットリストデータを取得する
     * 
     * @return array Robotlist data not delete log
     *               ログ削除を実施していないロボットリストデータ
     *               array[$ii]["robotlist_id"|"robotlist_url"|"is_robotlist_use"|...]
     */
    private function getUndeletedList()
    {
        $query = "SELECT * " . 
                 "FROM " . DATABASE_PREFIX . "repository_robotlist_master AS MASTER, " . 
                           DATABASE_PREFIX . "repository_robotlist_data AS DATAS " . 
                 "WHERE MASTER.is_robotlist_use = ? AND " .
                 "MASTER.robotlist_id = DATAS.robotlist_id AND " .
                 "DATAS.status != ? AND " .
                 "DATAS.is_delete = ? ; ";
        
        $params = array();
        $params[] = 1;
        $params[] = 1;
        $params[] = 0;
        
        $result = $this->Db->execute($query, $params);
        
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
     * Check status of robotlist process
     * ロボットリスト処理のステータスを確認する
     * 
     * @return boolean Whether or not execute robotlist process
     *                 ロボットリスト処理が実行中でないかどうか
     */
    private function checkRobotlistLock()
    {
        $query = "SELECT * ". 
                 " FROM ". DATABASE_PREFIX. "repository_lock ". 
                 " WHERE process_name = ? ; ";
        
        $params = array();
        $params[] = "Repository_Action_Common_Robotlist";
        
        $result = $this->Db->execute($query, $params);
        
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        if ($result[0]["status"] == 0){
            return true;
        }
        else {
            return false;
        }
    }
}
?>
