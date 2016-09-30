<?php

/**
 * Repository Components Business Batch Manager Class
 * バッチ管理クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Batchmanager.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Repository Components Business Batch Manager Class
 * バッチ管理クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Batchmanager extends BusinessBase
{
    /**
     * Get batch process status
     * バッチプロセス状態取得
     *
     * @param string $batchName batch name   バッチ名
     * @return array $result    batch status バッチ状態
     *                           array[0]["batch_instance_no"|"batch_name"|"process_id"|"status"|"command"|"current_progress"|"max_length"|"exit_code"|"start_date"|"end_date"|"ins_date"|"mod_date"]
     * @throws DbException
     */
    public function getBatchStatus($batchName) {
        $query = "SELECT * FROM {repository_bat_status} ".
                 "WHERE batch_name = ? ;";
        $params = array();
        $params[] = $batchName;
        $result = $this->executeSql($query, $params);
        
        return $result;
    }
}
?>