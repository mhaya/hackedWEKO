<?php

/**
 * Repository Components Business Operation log class
 * 操作ログ管理ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Operationlog.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR.'/modules/repository/components/FW/BusinessBase.class.php';

/**
 * Repository Components Business Operation log class
 * 操作ログ管理ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Operationlog extends BusinessBase{
    
    /**
     * Insert start log
     * 開始ログを挿入する
     *
     * @param string $userId user ID ユーザーID
     * @param string $requestPrams request parameter リクエストパラメータ
     * @return int log ID ログID
     */
    public function startLog($userId,$requestPrams){
        return $this->SetLog(0, $userId, $requestPrams);
    }
    
    /**
     * Insert end log
     * 終了ログを挿入する
     *
     * @param string $startLogId Log ID of the corresponding start operation 対応する開始操作のログID
     * @param string $userId user ID ユーザーID
     * @param string $requestPrams request parameter リクエストパラメータ
     * @return int log ID ログID
     */
    public function endLog($startLogId,$userId,$requestPrams){
        return $this->SetLog($startLogId, $userId, $requestPrams);
    }
    
    /**
     * Insert operation log
     * 操作ログをDBへ挿入する
     *
     * @param string $startLogId Log ID of the corresponding start operation 対応する開始操作のログID
     * @param string $userId user ID ユーザーID
     * @param string $requestPrams request parameter リクエストパラメータ
     * @return int log ID ログID
     */
    private function SetLog($startLogId,$userId,$requestPrams){
        $tableName = "repository_operation_log";
        $logId = $this->Db->nextSeq($tableName);
        $query = "INSERT INTO {".$tableName."} ".
                 "(log_id, record_date, user_id, request_parameter, start_log_id) ".
                 "VALUES(?, NOW(), ?, ?, ?)";
        $params = array();
        $params[] = $logId;
        $params[] = $userId;
        $params[] = $requestPrams;
        $params[] = $startLogId;
        $result = $this->Db->execute($query, $params);
        if (!$result) {
            $this->Db->addError();
            return false;
        }
        
        return $logId;
    }
}
?>