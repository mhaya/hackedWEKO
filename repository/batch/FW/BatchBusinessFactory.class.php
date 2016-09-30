<?php
/**
 * Batch factory class
 * バッチ用ファクトリークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BatchBusinessFactory.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Setting Const process
 * 定数設定処理
 */
require_once(WEBAPP_DIR."/modules/".MODULE_NAME."/batch/FW/BatchNC2Const.inc.php");
/**
 * Business factory abstract class
 * ビジネスファクトリー基底クラス
 */
require_once (WEBAPP_DIR."/modules/".MODULE_NAME."/components/FW/BusinessFactory.class.php");
/**
 * Batch logger Class
 * バッチロガークラス
 */
require_once(WEBAPP_DIR."/modules/".MODULE_NAME."/batch/FW/BatchLogger.class.php");

/**
 * Batch factory class
 * バッチ用ファクトリークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class BatchBusinessFactory extends BusinessFactory
{
    /**
     * Business class object list
     * ビジネスクラスオブジェクト配列
     *
     * @var array
     */
    public $businessList = array();
    
    /**
     * Initialize
     * 初期化
     * 
     * @param Session $_session session セッション
     * @param Db $_db DB object DBオブジェクト
     * @param string $_accessDate start date 処理開始時間
     * @param array $bizList  business name array ビジネスクラス名配列
     */
    public static function initialize($_session, $_db, $_accessDate, $bizList)
    {
        if(!is_null(parent::$instance))
        {
            return;
        }
        
        // ファクトリー
        parent::$instance = new BatchBusinessFactory($_session, $_db, $_accessDate);
        parent::$instance->businessList = $bizList;
        // ロガー
        parent::$instance->logger = new BatchLogger();
    }

    /**
     * Get business logic object
     * ビジネスロジックオブジェクト取得
     *
     * @param string $businessName
     * @return object Business object ビジネスクラスオブジェクト
     */
    public function getBusiness($businessName)
    {
        // ビジネスクラスリストを走査
        if(!array_key_exists($businessName, $this->businessList)) {
            return null;
        }

        // オブジェクトが作成済みならそれを返す
        $tmpPath = $this->businessList[$businessName];
        $className = str_replace(".", "_", $tmpPath);
        for($ii = 0; $ii < count($this->instanceList); $ii++) {
            if(get_class($this->instanceList[$ii]) == $className) {
                return $this->instanceList[$ii];
            }
        }

        // オブジェクトを新規作成する
        $filePath = WEBAPP_DIR.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.str_replace(".", DIRECTORY_SEPARATOR, $tmpPath).".class.php";
        // インスタンス作成
        require_once($filePath);
        $instance = new $className();
        
        if(method_exists($instance, "initializeBusiness")) {
            // 権限系パラメータは固定値
            $user_id = 0;
            $handle = "bat";
            $auth_id = 0;
            $instance->initializeBusiness($this->Db, $this->accessDate, $user_id, $handle, $auth_id, $this->logger);
            $this->instanceList[] =& $instance;
        }
        
        return $instance;
    }
}
?>
