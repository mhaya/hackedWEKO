<?php
/**
 * Action class for background process detail search log create
 * 詳細検索ログ作成非同期処理用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Detailsearchitem.class.php 69174 2016-06-22 06:43:30Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Base class for carrying out asynchronously and recursively possibility is the ability to process a long period of time
 * 長時間処理する可能性がある機能を非同期かつ再帰的に実施するための基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/BackgroundProcess.class.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * Action class for background process detail search log create
 * 詳細検索ログ作成非同期処理用アクションクラス
 * 
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Common_Background_Detailsearchitem extends BackgroundProcess
{

    /**
     * Background process name for lock table
     * ロックテーブル用非同期処理名
     *
     * @var string
     */
    const PARAM_NAME = "Repository_Action_Common_Background_Detailsearchitem";
    
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
     * Check unregistered detail search log
     * 登録されていない詳細検索ログが存在するか検索する
     *
     * @param int $endNo Last log No to be added detail search log
     *                    詳細検索ログを追加する最後のログ通番
     *
     * @return boolean Whether or not to register detail search log
     *                 詳細検索ログを登録するか否か
     */
    protected function prepareBackgroundProcess(&$endNo) {
    	if(isset($_GET['log_no']) && intval($_GET['log_no']) > 0)
    	{
    		$startNo = $_GET['log_no'];
    	}
    	else
    	{
    		$startNo = 1;
    	}
    	
        $this->Logger->infoLog("businessLogmanager", __FILE__, __CLASS__, __LINE__);
        $logManager = BusinessFactory::getFactory()->getBusiness("businessLogmanager");
        
        $ret = $logManager->isInsertDetailSearchAndCalcInsertLog($startNo, $endNo);
    	
    	return $ret;
    }
    
    /**
     * Register detail search log
     * 詳細検索ログを登録する
     *
     * @param int $endNo Last log No to be added detail search log
     *                   詳細検索ログを追加する最後のログ通番
     */
    protected function executeBackgroundProcess($endNo) {
        AppLogger::infoLog("businessLogmanager", __FILE__, __CLASS__, __LINE__);
        $logManager = BusinessFactory::getFactory()->getBusiness("businessLogmanager");
        $logManager->addDetailSearchItem();
        
        $_GET['log_no'] = $endNo + 1;
    }
}
?>