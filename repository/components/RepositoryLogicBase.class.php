<?php

/**
 * WEKO logic-based base class
 * WEKOロジックベース基底クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryLogicBase.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Repository module constant class
 * WEKO共通定数クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryConst.class.php';
/**
 * WEKO logger class
 * WEKOロガークラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/AppLogger.class.php';

/**
 * WEKO logic-based base class
 * WEKOロジックベース基底クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositoryLogicBase
{

    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    protected $Session = null;
    
    /**
     * Database access object
     * データベースアクセスオブジェクト
     *
     * @var RepositoryDbAccess
     */
    protected $dbAccess = null;
    
    /**
     * Transaction start date and time
     * トランザクション開始日時
     *
     * @var string
     */  
    protected $transStartDate = '';
    
    /**
     * Logger
     * ロガー
     *
     * @var Logger
     */  
    protected $Logger = null;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $Session Session セッション管理オブジェクト
     * @param DbObjectAdodb $db DB object データベース管理オブジェクト
     * @param string $TransStartDate Transaction start date トランザクション開始日時
     */
    protected function __construct($session, $db, $startDate)
    {
        // session
        if($session == null)
        {
            throw new InvalidArgumentException("RepositoryLogicBase : Failed construct, but argument at SessionObject.");
        }
        $this->Session = $session;
        
        // database
        if($db == null)
        {
            throw new InvalidArgumentException("RepositoryLogicBase : Failed construct, but argument at DbObjectAdodb.");
        }
        // check class format
        if(is_a($db, 'DbObjectAdodb'))
        {
            $this->dbAccess = new RepositoryDbAccess($db);
        }
        else if(is_a($db, 'RepositoryDbAccess'))
        {
            $this->dbAccess = $db;
        }
        else
        {
            throw new InvalidArgumentException("RepositoryLogicBase : Failed construct, but argument at DbObjectAdodb.");
        }
        
        // transStartDate
        if($startDate == null || strlen($startDate) < 1)
        {
            throw new InvalidArgumentException("RepositoryLogicBase : Failed construct, but argument at transStartDate.");
        }
        $this->transStartDate = $startDate;
        
        // logger
        $this->Logger = new AppLogger();
    }
    
    /**
     * To add a common item to the query parameters (insertion)
     * クエリパラメータに共通項目を追加する（挿入）
     *
     * @param array $params Query parameters クエリ用パラメータ
     *                      array[$ii]
     */
    protected function addSystemPramsForInsert(&$params)
    {
        $userId = $this->Session->getParameter("_user_id");
        $params[] = $userId;                // ins_user_id
        $params[] = $userId;                // mod_user_id
        $params[] = $this->transStartDate;  // ins_date
        $params[] = $this->transStartDate;  // mod_date
        $params[] = 0;                      // is_delete
    }
    
    /**
     * To add a common item to the query parameters (update)
     * クエリパラメータに共通項目を追加する（更新）
     *
     * @param array $params Query parameters クエリ用パラメータ
     *                      array[$ii]
     */
    protected function addSystemPramsForUpdate(&$params)
    {
        $params[] = $this->Session->getParameter("_user_id");   // mod_user_id
        $params[] = $this->transStartDate;                      // mod_date
    }
    
    /**
     * To add a common item to the query parameters (Delete)
     * クエリパラメータに共通項目を追加する（削除）
     *
     * @param array $params Query parameters クエリ用パラメータ
     *                      array[$ii]
     */
    protected function addSystemPramsForDelete(&$params)
    {
        $userId = $this->Session->getParameter("_user_id");
        $params[] = $userId;                // mod_user_id
        $params[] = $userId;                // del_user_id
        $params[] = $this->transStartDate;  // mod_date
        $params[] = $this->transStartDate;  // del_date
        $params[] = 1;                      // is_delete
    }
}
?>
