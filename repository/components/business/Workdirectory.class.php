<?php

/**
 * Repository Components Business Work Directory Class
 * 作業ディレクトリ管理ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Workdirectory.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Operate the file system
 * ファイルシステムの操作を行う
 */
require_once WEBAPP_DIR. '/modules/repository/components/util/OperateFileSystem.class.php';
/**
 * Repository Components Util Create Work Directory Class
 * 作業ディレクトリ作成クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/util/CreateWorkDirectory.class.php';

/**
 * Repository Components Business Work Directory Class
 * 作業ディレクトリ管理ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_business_Workdirectory extends BusinessBase
{
    /**
     * Temporary directory list created
     * 作成した一時ディレクトリリスト
     *
     * @var array
     */
    private $tmpDirectoryList = array();

    /**
     * Create work directory
     * 作業ディレクトリ作成
     *
     * @return string temporary directory path 作成した作業ディレクトリのパス
     * @throws AppException
     */
    public function create()
    {
        // 一時ディレクトリパス
        $tmpDirPath = "";
        
        // 作成
        $tmpDirPath = Repository_Components_Util_CreateWorkDirectory::create(WEBAPP_DIR. "/uploads/repository/");
        if($tmpDirPath === false){
            $errorMsg = "create temp directory is failed.";
            $this->errorLog($errorMsg, __FILE__, __CLASS__, __LINE__);
            $e = new AppException($errorMsg);
            $e->addError($errorMsg);
            throw $e;
        }
        
        // 作成済みリストに追加する
        $this->debugLog("Create Work Directory : ". $tmpDirPath, __FILE__, __CLASS__, __LINE__);
        $this->tmpDirectoryList[] = $tmpDirPath;
        
        return $tmpDirPath;
    }
    
    /**
     * Delete all the working directory you created
     * 作成した作業ディレクトリを全て削除する
     */
    protected function onFinalize() {
        $this->debugLog("[".__FUNCTION__."]"." Start Remove Work Directory", __FILE__, __CLASS__, __LINE__);
        for($ii = 0; $ii < count($this->tmpDirectoryList); $ii++) {
            if(file_exists($this->tmpDirectoryList[$ii])) {
                $this->debugLog("Remove Work Directory : ". $this->tmpDirectoryList[$ii], __FILE__, __CLASS__, __LINE__);
                Repository_Components_Util_OperateFileSystem::removeDirectory($this->tmpDirectoryList[$ii]);
            } else {
                $this->debugLog("Already Removed Work Directory : ". $this->tmpDirectoryList[$ii], __FILE__, __CLASS__, __LINE__);
            }
        }
        // リストクリア
        $this->tmpDirectoryList = null;
    }
}
?>