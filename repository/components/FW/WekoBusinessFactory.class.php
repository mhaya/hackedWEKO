<?php

/**
 * WEKO business factory class
 * WEKO用ビジネスファクトリークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: WekoBusinessFactory.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Business factory abstract class
 * ビジネスファクトリー基底クラス
 */
require_once (WEBAPP_DIR."/modules/repository/components/FW/BusinessFactory.class.php");
/**
 * WEKO logger class
 * WEKOロガークラス
 */
require_once (WEBAPP_DIR."/modules/repository/components/FW/AppLogger.class.php");

/**
 * WEKO business factory class
 * WEKO用ビジネスファクトリークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
 class WekoBusinessFactory extends BusinessFactory
{

    /**
     * Initialize
     * 初期化
     * 
     * @param Session $_session Session セッションオブジェクト
     * @param DbObjectAdodb $_db DB object DBオブジェクト
     * @param string $_accessDate execute start date 処理開始時間
     * @static
     */
    public static function initialize($_session, $_db, $_accessDate)
    {
        if(!is_null(parent::$instance))
        {
            return;
        }

        parent::$instance = new WekoBusinessFactory($_session, $_db, $_accessDate);
        // ロガー
        parent::$instance->logger = new AppLogger();
    }

    /**
     * Get business logic object
     * ビジネスロジックインスタンス取得
     *
     * @param string $businessName Business logic class name ビジネスロジック名
     * @return object Business logic object ビジネスロジックオブジェクト
     */
    public function getBusiness($businessName)
    {
        $container =& DIContainerFactory::getContainer();
        $instance =& $container->getComponent($businessName);
        
        if(method_exists($instance, "initializeBusiness"))
        {
            // セッションからユーザーのログイン情報を取得
            $user_id = $this->Session->getParameter("_user_id");
            $handle = $this->Session->getParameter("_handle");
            $auth_id = $this->Session->getParameter("_role_auth_id");
            $instance->initializeBusiness($this->Db, $this->accessDate, $user_id, $handle, $auth_id, $this->logger);
            $this->instanceList[] =& $instance;
        }
        
        return $instance;
    }
}
?>
