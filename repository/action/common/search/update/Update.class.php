<?php
/**
 * Search table rebuild action class
 * 検索テーブル再構築アクションクラス
 *
 * @package     WEKO
 */
// --------------------------------------------------------------------
//
// $Id: Update.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Base class for carrying out asynchronously and recursively possibility is the ability to process a long period of time
 * 長時間処理する可能性がある機能を非同期かつ再帰的に実施するための基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/BackgroundProcess.class.php';
/**
 * Common classes for creating and updating the search table that holds the metadata and file contents of each item to search speed improvement
 * 検索速度向上のためアイテム毎のメタデータおよびファイル内容を保持する検索テーブルの作成・更新を行う共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositorySearchTableProcessing.class.php';

/**
 * Search table rebuild action class
 * 検索テーブル再構築アクションクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Common_Search_Update extends BackgroundProcess
{
    // all metadata key
    /**
     * The number of processing
     * 処理数
     *
     * @var int
     */
    const MAX_RECORDS = "1";
    // all metadata table name
    /**
     * Process name
     * プロセス名
     *
     * @var string
     */
    const PARAM_NAME = "Repository_Action_Common_Search_Update";
    
    
    /**
     * Constructer(To set the process name)
     * コンストラクタ(プロセス名を設定する)
     */
    public function __construct()
    {
        parent::__construct(self::PARAM_NAME);
    }
    
    /**
     * Read the data to be processed
     * 処理対象のデータを読み込む
     * 
     * @param $target Data to be processed 処理対象のデータ
     */
    protected function prepareBackgroundProcess(&$itemList)
    {
        // get update item
        $query = "SELECT item_id, item_no ".
                 "FROM ". DATABASE_PREFIX ."repository_search_update_item ".
                 "ORDER BY item_id ".
                 "LIMIT 0, ".self::MAX_RECORDS.";";
        $itemList = $this->dbAccess->executeQuery($query);
        if(count($itemList) == 0){
            return false;
        }
        return true;
    }
    
    /**
     * To perform the time-consuming process
     * 時間のかかる処理を実行する
     * 
     * @param $target Data to be processed 処理対象のデータ
     */
    protected function executeBackgroundProcess($itemList)
    {
        // update search table
        $searchPrcessing = new RepositorySearchTableProcessing($this->Session, $this->Db);
        $searchPrcessing->setDataToSearchTable($itemList);
        
        // delete update item form repository_search_update_item table
        
        $query = "DELETE FROM ". DATABASE_PREFIX ."repository_search_update_item ".
                 "WHERE (item_id, item_no) IN (";
        $params = array();
        $count = 0;
        for($ii = 0; $ii < count($itemList); $ii++){
            if($count > 0){
                $query .= ",";
            }
            $query .= "(?,?)";
            $params[] = $itemList[$ii]["item_id"];
            $params[] = $itemList[$ii]["item_no"];
            $count++;
        }
        if($count == 0){
        	return;
        } else {
        	$query .= ");";
        }
        $itemList = $this->dbAccess->executeQuery($query, $params);
    }
    
}
?>