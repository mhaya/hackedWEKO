<?php

/**
 * Actual file Delete action class already logical delete
 * 論理削除済み実ファイル削除アクションクラス
 * 
 * @package WEKO
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
 * Actual file Delete action class already logical delete
 * 論理削除済み実ファイル削除アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Common_Filecleanup_Update extends BackgroundProcess
{
    // all metadata key
    /**
     * 1 processing number of each request
     * 1リクエスト毎の処理件数
     *
     * @var int
     */
    const MAX_RECORDS = "50";
    // all metadata table name
    /**
     * Process name
     * プロセス名
     *
     * @var string
     */
    const PARAM_NAME = "Repository_Action_Common_Filecleanup_Update";
    
    /**
     * Constructer
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct(self::PARAM_NAME);
    }
    
    /**
     * To get the information of the file to be deleted
     * 削除するファイルの情報を取得する
     *
     * @param array $fileList Delete File List 削除ファイル一覧
     *                        array[$ii]["item_id"|"attribute_id"|"file_no"|"extension"]
     */
    protected function prepareBackgroundProcess(&$fileList)
    {
        // get update item
        $query = "SELECT item_id, attribute_id, file_no, extension ".
                 "FROM ". DATABASE_PREFIX ."repository_filecleanup_deleted_file ".
                 "ORDER BY item_id ASC, attribute_id ASC, file_no ASC ".
                 "LIMIT 0, ".self::MAX_RECORDS.";";
        $fileList = $this->dbAccess->executeQuery($query);
        if(count($fileList) == 0){
            return false;
        }
        return true;
    }
    
    /**
     * Removes the specified actual files, exclude deleted files from the file list of deleted plans
     * 指定された実ファイルを削除し、削除したファイルを削除予定のファイルリストから除外する
     *
     * @param array $fileList Delete File List 削除ファイル一覧
     *                        array[$ii]["item_id"|"attribute_id"|"file_no"|"extension"]
     */
    protected function executeBackgroundProcess($fileList)
    {
        
        set_time_limit(0);
        
        $fileContentsPath = $this->getFileSavePath("file");
        if(strlen($fileContentsPath) == 0){
            // default directory
            $fileContentsPath = BASE_DIR.'/webapp/uploads/repository/files';
        }
        for($ii=0; $ii<count($fileList); $ii++)
        {
            $filePath = $fileContentsPath."/".$fileList[$ii]["item_id"]."_".
                        $fileList[$ii]["attribute_id"]."_".$fileList[$ii]["file_no"].".".
                        $fileList[$ii]["extension"];
            if(file_exists($filePath))
            {
                chmod($filePath, 0777 );
                unlink($filePath);
            }
        }
        
        // delete record from repository_filecleanup_deleted_file
        $query = "DELETE FROM ".DATABASE_PREFIX ."repository_filecleanup_deleted_file ".
                 "WHERE (item_id, attribute_id, file_no) IN (";
        $params = array();
        $count = 0;
        for($ii = 0; $ii < count($fileList); $ii++){
            if($count > 0){
                $query .= ",";
            }
            $query .= "(?,?,?)";
            $params[] = $fileList[$ii]["item_id"];
            $params[] = $fileList[$ii]["attribute_id"];
            $params[] = $fileList[$ii]["file_no"];
            $count++;
        }
        if($count == 0){
        	return;
        } else {
        	$query .= ");";
        }
        $fileList = $this->dbAccess->executeQuery($query, $params);
    }
    
}
?>