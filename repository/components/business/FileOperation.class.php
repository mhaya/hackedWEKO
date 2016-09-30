<?php

/**
 * Structure class that summarizes the information about the operation to file
 * 操作するファイルについての情報をまとめた構造体クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: FileOperation.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Structure class that summarizes the information about the operation to file
 * 操作するファイルについての情報をまとめた構造体クラス
 * 
 * @package WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class FileOperation
{
    /**
     * ID of the content file update operations
     * 本文ファイル更新操作のID
     * 
     * @var int
     */
    const OPERATION_ID_UPDATE = 0;
    
    /**
     * ID of the content file deletion and the FLASH delete operation
     * 本文ファイル削除とFLASH削除操作のID
     * 
     * @var int
     */
    const OPERATION_ID_DELETE = 1;
    
    /**
     * ID of the content file deletion and the FLASH delete operation
     * 本文ファイルとFLASH新規登録操作のID
     *
     * @var int
     */
    const OPERATION_ID_INSERT = 2;
    
    /**
     * ID indicating that a process is incomplete
     * 処理が未了であることを示すID
     * 
     * @var int
     */
    const RESULT_ID_NOTYET = 0;
    
    /**
     * ID indicating that the process has been completed
     * 処理が完了していることを示すID
     * 
     * @var int
     */
    const RESULT_ID_DONE = 1;
    
    /**
     * Item id
     * アイテムID
     *
     * @var int
     */
    public $itemId = 0;
    
    /**
     * Attribute id
     * 属性ID
     *
     * @var int
     */
    public $attrId = 0;
    
    /**
     * File number
     * ファイル通番
     *
     * @var int
     */
    public $fileNo = 0;
    
    /**
     * Operation id(OPERATION_ID_UPDATE, OPERATION_ID_INSERT, OPERATION_ID_DELETE)
     * 操作ID（OPERATION_ID_UPDATE, OPERATION_ID_INSERT, OPERATION_ID_DELETE）
     *
     * @var int
     */
    public $operationId = 0;
    
    /**
     * Result id(RESULT_ID_NOTYET, RESULT_ID_DONE)
     * 結果ID(RESULT_ID_NOTYET, RESULT_ID_DONE)
     *
     * @var int
     */
    public $resultId = 0;
    
    /**
     * Registration, update the original content file path
     * 登録、更新元の本文ファイルパス
     *
     * @var string
     */
    public $operateContentFilePath = null;
    
    /**
     * Registration, update source of FLASH file path list
     * 登録、更新元のFLASHファイルパスリスト
     *
     * @var unknown_type
     */
    public $operateFlashFilePathList = null;
    
    /**
     * Resources of the lock file
     * ロックファイルのリソース
     *
     * @var resource
     */
    public $lockResource = null;
    
    /**
     * Content file path is registered or updated
     * 登録や更新される本文ファイルパス
     *
     * @var string
     */
    public $contentFilePath = null;
    
    /**
     * The path of the FLASH directory to be registered or updated
     * 登録や更新されるFLASHディレクトリのパス
     *
     * @var string
     */
    public $flashDirectoryPath = null;
    
    /**
     * The saved content file path
     * 退避した本文ファイルパス
     *
     * @var string
     */
    public $backupContentFilePath = null;
    
    /**
     * The evacuated FLASH directory path
     * 退避したFLASHディレクトリパス
     *
     * @var string
     */
    public $backupFlashDirectoryPath = null;
    
    /**
     * Temporary path of the content file you created in the text file storage
     * 本文ファイル置き場に作成した本文ファイルの一時パス
     *
     * @var string
     */
    public $tmpContentFilePath = null;
    
    /**
     * Temporary path of FLASH directory that was created in FLASH file storage
     * FLASHファイル置き場に作成したFLASHディレクトリの一時パス
     *
     * @var string
     */
    public $tmpFlashDirectoryPath = null;
    
    /**
     * Version that was registered in the file update history
     * ファイル更新履歴に登録したバージョン
     *
     * @var int
     */
    public $version = null;
    
    /**
     * The latest version of the file path
     * 最新バージョンのファイルパス
     *
     * @var string
     */
    public $tmpVersionFilePath = null;
    
    /**
     * Constructor of structure
     * 構造体のコンストラクタ
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $operateContentFilePath Registration, update the original content file path 登録、更新元の本文ファイルパス
     * @param string $operateFlashFilePathList Registration, update source of FLASH file path list 登録、更新元のFLASHファイルパスリスト
     * @param int $operationId Operation id 操作ID
     * @param string $contentFilePath Content file path is registered or updated 登録や更新される本文ファイルパス
     * @param string $flashDirectoryPath Path The path of the FLASH directory to be registered or updated 登録や更新されるFLASHディレクトリのパス
     */
    public function __construct($itemId, $attrId, $fileNo, $operateContentFilePath, $operateFlashFilePathList, $operationId, $contentFilePath, $flashDirectoryPath){
        $this->itemId = $itemId;
        $this->attrId = $attrId;
        $this->fileNo = $fileNo;
        $this->operateContentFilePath = $operateContentFilePath;
        $this->operateFlashFilePathList = $operateFlashFilePathList;
        $this->operationId = $operationId;
        $this->resultId = self::RESULT_ID_NOTYET;
        $this->contentFilePath = $contentFilePath;
        $this->flashDirectoryPath = $flashDirectoryPath;
        $this->version = 0;
    }
}
?>