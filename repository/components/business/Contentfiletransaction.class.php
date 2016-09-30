<?php

/**
 * A body file of WEKO and a FLASH file are operated safely.
 * WEKOの本文ファイルおよびFLASHファイルの安全な操作を行う
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Contentfiletransaction.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Structure class that summarizes the information about the operation to file
 * 操作するファイルについての情報をまとめた構造体クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/FileOperation.class.php';

/**
 * Common class to operate string
 * 文字列の操作を行う共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/util/StringOperator.class.php';

/**
 * A body file of WEKO and a FLASH file are operated safely.
 * WEKOの本文ファイルおよびFLASHファイルの安全な操作を行う
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Contentfiletransaction extends BusinessBase 
{
    /**
     * Content file storage directory path
     * 本文ファイル保存ディレクトリパス
     *
     * @var string
     */
    private $contentFileStragePath = "";
    
    /**
     * FLASH file storage directory path
     * FLASHファイル保存ディレクトリパス
     *
     * @var string
     */
    private $flashFileDirectoryStragePath = "";
    
    /**
     * Version file storage directory path
     * バージョンファイル保存ディレクトリパス
     *
     * @var string
     */
    private $versionFileStragePath = "";
    
    /**
     * Maximum value list of the outstanding file serial number
     * 発行済みファイル通番の最大値リスト
     *
     * @var array[$itemId_$attrId]
     */
    private $maxFileNoList = array();
    
    /**
     * Operation List
     * 操作一覧
     *
     * @var array[$itemId_$attrId_$fileNo] = FileOperation()
     */
    private $operationList = array();
    
    /**
     * Shaping content file path to perform the registration and update, the FLASH the path for the log output
     * 登録や更新を行う本文ファイルパス、FLASHパスををログ出力用に整形する
     *
     * @param string $operateFilePath Content file path to register and update 登録や更新を行う本文ファイルパス
     * @param string $operateFlashFilesPathList FLASH path list to register and update 登録や更新を行うFLASHパスリスト
     * @param string $filePath Content file path for log output ログ出力用本文ファイルパス
     * @param string $flashFilesPath FLASH path for the log output ログ出力用FLASHパス
     */
    private function inputPathForOutputLog($operateFilePath, $operateFlashFilesPathList, &$filePath, &$flashFilesPath){
        if(isset($operateFilePath)){
            $filePath = $operateFilePath;
        } else {
            $filePath = "";
        }
        if(isset($operateFlashFilesPathList)){
            $flashFilesPath = print_r($operateFlashFilesPathList, true);
        } else {
            $flashFilesPath = "";
        }
    }
    
    /**
     * Perform file new registration in the path of the specified file (the actual new registration is carried out in the commit)
     * 指定したファイルのパスでファイル新規登録を行う(実際の新規登録はcommitで行う)
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param string $insertFilePath Insert file path 新規登録ファイルパス
     * @param array $insertFlashPathList Insert file path list 新規登録FLASHファイルパスリスト array[$ii]
     * @return int File Number ファイル通番
     */
    public function insert($itemId, $attrId, $insertFilePath, $insertFlashPathList){
        $this->inputPathForOutputLog($insertFilePath, $insertFlashPathList, $filePath, $flashFilesPath);
        $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " insertFilePath: ". $filePath. " insertFlashPathList: ". $flashFilesPath, __FILE__, __CLASS__, __LINE__);
        
        // ファイル通番のリストまたはデータベースからファイル通番を取得する
        $fileNo = $this->generateFileNo($itemId, $attrId, $this->maxFileNoList);
        
        while(true){
            $fp = $this->lock($itemId, $attrId, $fileNo, LOCK_EX | LOCK_NB);
            if(!$fp){
                // ロックに失敗したのでファイル通番をインクリメントして再実行
                $this->debugLog("[".__FUNCTION__."]"." [Failed lock] itemId:".$itemId.", attrId:".$attrId.", fileNo:".$fileNo, __FILE__, __CLASS__, __LINE__);
                $fileNo = $fileNo + 1;
            } else {
                // ロックに成功したのでループを抜ける
                break;
            }
        }
        $this->debugLog("[". __FUNCTION__. "] fileNo: ". $fileNo, __FILE__, __CLASS__, __LINE__);
        
        $operation = $this->addOperation($itemId, $attrId, $fileNo, $insertFilePath, $insertFlashPathList, FileOperation::OPERATION_ID_INSERT);
        $operation->lockResource = $fp;
        
        $key = $itemId. "_". $attrId;
        $this->maxFileNoList[$key] = $fileNo;
        
        if(isset($insertFilePath)){
            // 本文ファイル置き場に一時ファイルとしてコピーする
            $operation->tmpContentFilePath = $this->copyFile($itemId, $attrId, $fileNo, $insertFilePath);
        }
        if(isset($insertFlashPathList)){
            // FLASHファイル置き場に一時ファイルとしてコピーする
            $operation->tmpFlashDirectoryPath = $this->copyFlash($itemId, $attrId, $fileNo, $insertFlashPathList);
        }
        return $fileNo;
    }
    
    /**
     * Issue the file serial number
     * ファイル通番を発行
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param array $maxFileNoList Maximum value list of the outstanding file serial number 発行済みファイル通番の最大値リスト
     * @return int File number ファイル通番
     */
    private function generateFileNo($itemId, $attrId, $maxFileNoList){
        $fileNo = 0;
        $key = $itemId. "_". $attrId;
        if(isset($maxFileNoList[$key])){
            $fileNo = $maxFileNoList[$key] + 1;
        } else {
            // MAX(file_no) + 1
            $query = "SELECT MAX(file_no) + 1 AS file_no ". 
                     " FROM ". DATABASE_PREFIX. "repository_file ". 
                     " WHERE item_id = ? ". 
                     " AND attribute_id = ?;";
            $params = array();
            $params[] = $itemId;
            $params[] = $attrId;
            $result = $this->executeSql($query, $params);
            if(!isset($result[0]['file_no'])){
                $fileNo = 1;
            } else {
                $fileNo = $result[0]['file_no'];
            }
        }
        
        return $fileNo;
    }
    
    /**
     * A file update is performed by a pass of a designated file.
     * In addition, also performs the update of the file table and the file update history table
     * 指定したファイルのパスでファイル更新を行う(実際の更新はcommitで行う)
     * また、ファイルテーブルおよびファイル更新履歴テーブルの更新も行う
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $updateFilePath Update file path 更新対象ファイルパス
     * @param string $mimeType File type ファイル種別
     * @param array $updateFlashFilePathList Update FLASH file path list 更新対象FLASHファイルパスリスト array[$ii]
     */
    public function update($itemId, $attrId, $fileNo, $updateFilePath, $mimeType, $updateFlashFilePathList){
        $this->inputPathForOutputLog($updateFilePath, $updateFlashFilePathList, $filePath, $flashFilesPath);
        $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " fileNo: ". $fileNo. " insertFilePath: ". $filePath. " insertFlashPathList: ". $flashFilesPath, __FILE__, __CLASS__, __LINE__);
        
        $operation = $this->findOperation($itemId, $attrId, $fileNo);
        
        if(isset($operation)){
            switch($operation->operationId){
                case FileOperation::OPERATION_ID_UPDATE:
                    if(isset($updateFilePath)){
                        $operation->operateContentFilePath = $updateFilePath;
                    }
                    if(isset($updateFlashFilePathList)){
                        $operation->flashFileDirectoryPath = $updateFlashFilePathList;
                    }
                    break;
                case FileOperation::OPERATION_ID_INSERT:
                case FileOperation::OPERATION_ID_DELETE:
                    throw new AppException("[".__FUNCTION__."]"." [Duplicate update file] itemId:".$itemId. " attrId:". $attrId. " fileNo:". $fileNo);
            }
        } else {
            $operation = $this->addOperation($itemId, $attrId, $fileNo, $updateFilePath, $updateFlashFilePathList, FileOperation::OPERATION_ID_UPDATE);
        }
        
        // ファイルテーブルおよびファイル更新履歴テーブルの更新と挿入(ファイルの更新時のみ実行する)
        if(strlen($updateFilePath) > 0){
            // ファイル更新履歴への挿入(バージョンの確定)
            $operation->version = $this->addFileUpdateHistory($itemId, $attrId, $fileNo);
            
            // ファイルテーブルの更新
            $this->updateFileTable($itemId, $attrId, $fileNo, $updateFilePath, $mimeType);
        }
    }
    
    /**
     * Designated body file and FLASH file are eliminated.
     * 指定された本文ファイルおよびFLASHファイルを削除する(実際の削除はcommitで行う)
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     */
    public function delete($itemId, $attrId, $fileNo){
        $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " fileNo: ". $fileNo, __FILE__, __CLASS__, __LINE__);
        
        $operation = $this->findOperation($itemId, $attrId, $fileNo);
        
        if(isset($operation)){
            throw new AppException("[".__FUNCTION__."]"." [Duplicate delete file] itemId:".$itemId. " attrId:". $attrId. " fileNo:". $fileNo);
        } else {
            $operation = $this->addOperation($itemId, $attrId, $fileNo, null, null, FileOperation::OPERATION_ID_DELETE);
        }
        
        // ファイル更新履歴のファイル情報を削除する
        $query = "UPDATE ". DATABASE_PREFIX. "repository_file_update_history ". 
                 " SET is_delete = ?, ". 
                 " mod_user_id = ?, ". 
                 " del_user_id = ?, ". 
                 " mod_date = ?, ". 
                 " del_date = ? ". 
                 " WHERE item_id = ? ". 
                 " AND item_no = ? ".
                 " AND attribute_id = ? ". 
                 " AND file_no = ? ";
        $params = array();
        $params[] = 1;
        $params[] = $this->user_id;
        $params[] = $this->user_id;
        $params[] = $this->accessDate;
        $params[] = $this->accessDate;
        $params[] = $itemId;
        $params[] = 1;
        $params[] = $attrId;
        $params[] = $fileNo;
        $this->executeSql($query, $params);
    }
    
    /**
     * Processing at the time of file operations end(Called before the database commit)
     * ファイル操作終了時の処理(データベースコミット前に呼び出す)
     *
     */
    public function finishFileOperation(){
        $this->debugLog("[". __FUNCTION__. "] ", __FILE__, __CLASS__, __LINE__);
        // デッドロックが起きないようにロックするファイルに対してソート(昇順)する
        ksort($this->operationList); // キーでソート
        
        // 全更新ファイルのロックを取得し、ファイルを一時パスにコピーする
        foreach($this->operationList as $fileOperation){
            if($fileOperation->operationId === FileOperation::OPERATION_ID_UPDATE){
                $result = $this->lock($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo, LOCK_EX);
                if(!$result){
                    throw new AppException("[".__FUNCTION__."]"." [Failed lock] itemId: ".$fileOperation->itemId. " attrId: ". $fileOperation->attrId. " fileNo: ". $fileOperation->fileNo);
                }
                $fileOperation->lockResource = $result;
            }
        }
        
        foreach($this->operationList as $fileOperation){
            if($fileOperation->operationId === FileOperation::OPERATION_ID_UPDATE){
                if(isset($fileOperation->operateContentFilePath)){
                    $fileOperation->tmpContentFilePath = $this->copyFile($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo, $fileOperation->operateContentFilePath);
                }
                if(isset($fileOperation->operateFlashFilePathList)){
                    $fileOperation->tmpFlashDirectoryPath = $this->copyFlash($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo, $fileOperation->operateFlashFilePathList);
                }
                if($fileOperation->version > 0){
                    // バージョン管理ディレクトリへのコピーを行うのは設定値_REPOSITORY_MANAGE_PHYSICAL_FILE_VERSIONがtrueの時のみ
                    if(_REPOSITORY_MANAGE_PHYSICAL_FILE_VERSION){
                        $fileOperation->tmpVersionFilePath = $this->saveCurrentFile($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo, $fileOperation->version);
                    }
                }
            }
        }
    }
    
    /**
     * To save the latest version of the text file that is specified by a unique key to the file update history
     * ユニークキーによって指定された最新版の本文ファイルをファイル更新履歴に保存する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param int $version File version ファイルのバージョン
     * @return string The latest version of the file path 最新バージョンのファイルパス
     */
    private function saveCurrentFile($itemId, $attrId, $fileNo, $version){
        $query = "SELECT physical_file_name ". 
                 " FROM ". DATABASE_PREFIX. "repository_file_update_history ". 
                 " WHERE item_id = ? ". 
                 " AND item_no = ? ". 
                 " AND attribute_id = ? ". 
                 " AND file_no = ? ". 
                 " AND version = ?;";
        $params = array();
        $params[] = $itemId;
        $params[] = 1;
        $params[] = $attrId;
        $params[] = $fileNo;
        $params[] = $version;
        $result = $this->executeSql($query, $params);
        $pathInfo = pathinfo($result[0]["physical_file_name"]);
        
        $contentFilePath = $this->generateContentFilePath($itemId, $attrId, $fileNo, $pathInfo["extension"]);
        
        // バージョンファイルパスを作成する
        $tmpFilePath = $this->generateTempVersionFilePath($itemId, $attrId, $fileNo, $version);
        
        // バージョンディレクトリが作成されているかを確認し、ないなら作成する
        $pathInfo = pathinfo($tmpFilePath);
        $versionDirPath = $pathInfo["dirname"];
        if(!file_exists($versionDirPath)){
            Repository_Components_Util_OperateFileSystem::mkdir($versionDirPath);
        }
        
        // ファイルのコピーを実施する
        Repository_Components_Util_OperateFileSystem::copy($contentFilePath, $tmpFilePath);
        
        return $tmpFilePath;
    }
    
    /**
     * Copy the file specified in FLASH file storage
     * 指定されたファイルをFLASHファイル置き場にコピーする
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $flashPathList Flash file path list FLASHファイルのパス一覧
     * @return string The copied FLASH directory path コピーしたFLASHディレクトリパス
     */
    private function copyFlash($itemId, $attrId, $fileNo, $flashPathList){
        // flash file copy
        $tmpFlashDirPath = null;
        if(count($flashPathList) > 0){
            // FLASH ファイルのディレクトリ作成
            $tmpFlashDirPath = $this->generateTempDirectoryPath($itemId, $attrId, $fileNo). DIRECTORY_SEPARATOR;
            if(file_exists($tmpFlashDirPath)){
                Repository_Components_Util_OperateFileSystem::removeDirectory($tmpFlashDirPath);
            }
            Repository_Components_Util_OperateFileSystem::mkdir($tmpFlashDirPath, 0755);
            foreach($flashPathList as $filePath){
                // パスからファイル名を取得
                $fileName = basename($filePath);
                $destFilePath = $tmpFlashDirPath. DIRECTORY_SEPARATOR. $fileName;
                
                Repository_Components_Util_OperateFileSystem::copy($filePath, $destFilePath);
                $this->debugLog("[". __FUNCTION__. "] copy: ". $filePath. " -> ". $destFilePath, __FILE__, __CLASS__, __LINE__);
            }
        }
        
        return $tmpFlashDirPath;
    }
    
    /**
     * Copy the file specified in the content file storage
     * 指定されたファイルを本文ファイル置き場にコピーする
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $filePath Content file path 本文ファイルパス
     * @return string The copied content file path コピーした本文ファイルパス
     */
    private function copyFile($itemId, $attrId, $fileNo, $filePath){
        // 本文ファイル置き場、FLASHファイル置き場にコピーする
        // content file copy(本文ファイル置き場に一時ファイルを作成する)
        $fileInfo = pathinfo($filePath);
        $tmpFilePath = $this->generateTempFilePath($itemId, $attrId, $fileNo, $fileInfo["extension"]);
        Repository_Components_Util_OperateFileSystem::copy($filePath, $tmpFilePath);
        $this->debugLog("[". __FUNCTION__. "] copy: ". $filePath. " -> ". $tmpFilePath, __FILE__, __CLASS__, __LINE__);
        
        return $tmpFilePath;
    }
    
    /**
     * It was carried out the lock, to perform the registration, update and delete files
     * When the file registration is carried out the acquired register the lock in the insert function
     * When a file is updated and updates by obtaining the lock on the file operation ends
     * Delete at the time of lock acquisition of file file, delete, repeat file a few minutes release
     * Call after the database commit
     * ロックを実施し、ファイルの登録・更新・削除を行う
     * ファイル登録時はinsert関数内でロックを取得し登録を行う
     * ファイル更新時はファイル操作終了時にロックを取得して更新を行う
     * ファイル削除時はファイルのロック取得、削除、解除をファイル数分繰り返す
     * データベースコミット後に呼び出す
     */
    public function commit(){
        $this->debugLog("[". __FUNCTION__. "] ", __FILE__, __CLASS__, __LINE__);
        // try
        $ex = null;
        try {
            $this->insertFiles($this->operationList);
            $this->updateFiles($this->operationList);
            $this->deleteFiles($this->operationList);
        } catch(Exception $ex){
            $this->rollback($this->operationList);
            $this->unlockFiles($this->operationList);
            $this->operationList = array();
            throw $ex;
        }
        // クリーンアップ(ファイルの操作はロックを取得している間しかできないため、ここでクリーンアップを実施する)
        $this->cleanup($this->operationList);
        
        // ロックを開放する
        $this->unlockFiles($this->operationList);
        
        // 終了処理完了後、操作一覧を空にする
        $this->operationList = array();
    }
    
    /**
     * Remove the garbage files that created, empty the update list
     * 作成したゴミファイルを削除し、更新リストを空にする
     *
     * @param array $operationList Operation List 操作一覧 array[$itemId][$attrId][$fileNo]["operationId"|"resultId"]
     */
    private function cleanUp(&$operationList){
        foreach($operationList as $fileOperation){
            $backupContentFilePath = $fileOperation->backupContentFilePath;
            if(isset($backupContentFilePath)){
                try{
                    Repository_Components_Util_OperateFileSystem::unlink($backupContentFilePath);
                } catch(AppException $ex){
                    // ロックファイルの削除が失敗したとしても後続処理に問題はなく、ゴミファイルが残るだけであるため、例外発生のログだけ出力し、処理を続行する
                    $this->exeptionLog($ex, __FILE__, __CLASS__, __LINE__);
                }
                // 退避した本文ファイルのパスを解放
                $fileOperation->backupContentFilePath = "";
            }
            
            $backupFlashDirectoryPath = $fileOperation->backupFlashDirectoryPath;
            if(isset($backupFlashDirectoryPath)){
                Repository_Components_Util_OperateFileSystem::removeDirectory($backupFlashDirectoryPath);
                // 退避したflashファイルのパスを解放
                $fileOperation->backupFlashDirectoryPath = "";
            }
            
            $backupVerDirPath = $this->generateBackupVersionDirPath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
            if(file_exists($backupVerDirPath)){
                Repository_Components_Util_OperateFileSystem::removeDirectory($backupVerDirPath);
            }
        }
    }
    
    /**
     * Register the file
     * ファイルを登録する
     *
     * @param array $operationList Operation List 操作一覧
     *                             array[$ii]
     */
    private function insertFiles(&$operationList){
        foreach($operationList as $fileOperation){
            if($fileOperation->operationId === FileOperation::OPERATION_ID_INSERT){
                $this->insertFile($fileOperation);
            }
        }
    }
    
    /**
     * Register the content file and the FLASH file
     * 本文ファイルおよびFLASHファイルを登録する
     *
     * @param FileOperation $fileOperation File operation contents ファイル操作内容
     */
    private function insertFile(&$fileOperation){
        // 本文ファイルを追加ファイルで上書きする
        Repository_Components_Util_OperateFileSystem::rename($fileOperation->tmpContentFilePath, $fileOperation->contentFilePath);
        $this->debugLog("[". __FUNCTION__. "] rename: ". $fileOperation->tmpContentFilePath. " -> ". $fileOperation->contentFilePath, __FILE__, __CLASS__, __LINE__);
        
        // 追加ファイルが消えたので、パスを解除する
        $fileOperation->tmpContentFilePath = "";
        
        if(isset($fileOperation->tmpFlashDirectoryPath)){
            // FLASHファイルを追加ディレクトリで上書きする
            Repository_Components_Util_OperateFileSystem::rename($fileOperation->tmpFlashDirectoryPath, $fileOperation->flashDirectoryPath);
            $this->debugLog("[". __FUNCTION__. "] rename: ". $fileOperation->tmpFlashDirectoryPath. " -> ". $fileOperation->flashDirectoryPath, __FILE__, __CLASS__, __LINE__);
            
            // 追加ディレクトリが消えたので、パスを解除する
            $fileOperation->tmpFlashDirectoryPath = "";
        }
        $fileOperation->resultId = FileOperation::RESULT_ID_DONE;
    }
    
    /**
     * When a file update is completed, unlock
     * ファイル更新が完了したとき、ロックを解除
     *
     * @param array $operationList Operation List 操作一覧
     *                             array[$ii]
     */
    private function unlockFiles($operationList){
        foreach($operationList as $fileOperation){
            $this->unlock($fileOperation->lockResource, $fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
        }
    }
    
    /**
     * Update files
     * ファイルを更新
     * 
     * @param array $operationList Operation List 操作一覧
     *                             array[$ii]
     */
    private function updateFiles(&$operationList){
        foreach($operationList as $fileOperation){
            if($fileOperation->operationId === FileOperation::OPERATION_ID_UPDATE){
                $this->updateFile($fileOperation);
            }
        }
    }
    
    /**
     * Do an update of the content file or FLASH file
     * 本文ファイルまたはFLASHファイルの更新を行う
     * 
     * @param FileOperation $fileOperation File operation contents ファイル操作内容
     */
    private function updateFile(&$fileOperation){
        // 本文ファイル更新
        if(isset($fileOperation->tmpContentFilePath)){
            // ファイル更新履歴から最新のファイル名を取得し、そこから拡張子を取得する
            $ext = $this->selectOldVersionFileExtension($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
            $backupContentFilePath = $this->generateBackupFilePath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo, $ext);
            $contentFilePath = $this->generateContentFilePath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo, $ext);
            
            // 本文ファイルを別名にリネームする
            Repository_Components_Util_OperateFileSystem::rename($contentFilePath, $backupContentFilePath);
            $this->debugLog("[". __FUNCTION__. "] rename: ". $contentFilePath. " -> ". $backupContentFilePath, __FILE__, __CLASS__, __LINE__);
            $fileOperation->backupContentFilePath = $backupContentFilePath;
            // 更新ファイルを本文ファイルにリネームする
            Repository_Components_Util_OperateFileSystem::rename($fileOperation->tmpContentFilePath, $fileOperation->contentFilePath);
            $this->debugLog("[". __FUNCTION__. "] rename: ". $fileOperation->tmpContentFilePath. " -> ". $fileOperation->contentFilePath, __FILE__, __CLASS__, __LINE__);
            // ゴミファイルの判定のため、更新ファイルのパスを解除する
            $fileOperation->tmpContentFilePath = "";
        }
        
        // FLASHファイルが存在しない可能性がある
        // 古いファイルのflashファイルがあるので、アップデート時は確実に退避
        $flashPath = $this->generateFlashDirectoryPath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
        if(file_exists($flashPath)){
            $backupFlashDirectoryPath = $this->generateBackupDirectoryPath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo). DIRECTORY_SEPARATOR;
            Repository_Components_Util_OperateFileSystem::rename($flashPath, $backupFlashDirectoryPath);
            $this->debugLog("[". __FUNCTION__. "] rename: ". $flashPath. " -> ". $backupFlashDirectoryPath, __FILE__, __CLASS__, __LINE__);
            $fileOperation->backupFlashDirectoryPath = $backupFlashDirectoryPath;
        }
        
        // FLASH更新
        if(isset($fileOperation->tmpFlashDirectoryPath)){
            // FLASHファイルを更新ディレクトリで上書きする
            Repository_Components_Util_OperateFileSystem::rename($fileOperation->tmpFlashDirectoryPath, $fileOperation->flashDirectoryPath);
            $this->debugLog("[". __FUNCTION__. "] rename: ". $fileOperation->tmpFlashDirectoryPath. " -> ". $fileOperation->flashDirectoryPath, __FILE__, __CLASS__, __LINE__);
            
            // ゴミファイルの判定のため、更新ディレクトリのパスを解除する
            $fileOperation->tmpFlashDirectoryPath = "";
        }
        
        // 旧バージョンファイルを正式なファイルパスに修正
        if($fileOperation->version > 0){
            if(_REPOSITORY_MANAGE_PHYSICAL_FILE_VERSION){
                $tmpFilePath = $fileOperation->tmpVersionFilePath;
                $verFilePath = $this->generateVersionFilePath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo, $fileOperation->version);
                Repository_Components_Util_OperateFileSystem::rename($tmpFilePath, $verFilePath);
                $this->debugLog("[". __FUNCTION__. "] rename: ". $tmpFilePath. " -> ". $verFilePath, __FILE__, __CLASS__, __LINE__);
                
                // 最新バージョンのファイルパスを解放
                $fileOperation->tmpVersionFilePath = "";
            }
        }
        
        $fileOperation->resultId = FileOperation::RESULT_ID_DONE;
    }
    
    /**
     * To get an extension of the old version of the file
     * 古いバージョンのファイルの拡張子を取得する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return string Extension 拡張子
     */
    private function selectOldVersionFileExtension($itemId, $attrId, $fileNo){
        $query = "SELECT physical_file_name ". 
                 " FROM ". DATABASE_PREFIX. "repository_file_update_history ". 
                 " WHERE item_id = ? ". 
                 " AND item_no = ? ". 
                 " AND attribute_id = ? ". 
                 " AND file_no = ? ". 
                 " AND is_delete = ? ". 
                 " ORDER BY version DESC;";
        $params = array();
        $params[] = $itemId;
        $params[] = 1;
        $params[] = $attrId;
        $params[] = $fileNo;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        $fileInfo = pathinfo($result[0]["physical_file_name"]);
        return $fileInfo["extension"];
    }
    
    /**
     * Undo the update of the file
     * ファイルの更新を元に戻す
     *
     * @param array $operationList Operation List 操作一覧 
     *                             array[$ii]
     */
    private function rollback(&$operationList){
        foreach($operationList as $fileOperation){
            if(isset($fileOperation->backupContentFilePath)){
                $backupContentFilePath = $fileOperation->backupContentFilePath;
                if(isset($backupContentFilePath)){
                    $contentFilePath = $fileOperation->contentFilePath;
                    Repository_Components_Util_OperateFileSystem::rename($backupContentFilePath, $contentFilePath);
                    $this->debugLog("[". __FUNCTION__. "] rename: ". $backupContentFilePath. " -> ". $contentFilePath, __FILE__, __CLASS__, __LINE__);
                }
            }
            if(isset($fileOperation->backupFlashDirectoryPath)){
                $backupFlashDirectoryPath = $fileOperation->backupFlashDirectoryPath;
                if(isset($backupFlashDirectoryPath)){
                    $flashDirectoryPath = $fileOperation->flashDirectoryPath;
                    Repository_Components_Util_OperateFileSystem::rename($backupFlashDirectoryPath, $flashDirectoryPath);
                    $this->debugLog("[". __FUNCTION__. "] rename: ". $backupFlashDirectoryPath. " -> ". $flashDirectoryPath, __FILE__, __CLASS__, __LINE__);
                }
            }
            $backupVerDirPath = $this->generateBackupVersionDirPath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
            if(file_exists($backupVerDirPath)){
                $versionDirPath = $this->generateVersionDirPath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
                Repository_Components_Util_OperateFileSystem::rename($backupVerDirPath, $versionDirPath);
                $this->debugLog("[". __FUNCTION__. "] rename: ". $backupVerDirPath. " -> ". $versionDirPath, __FILE__, __CLASS__, __LINE__);
            }
        }
    }
    
    /**
     * Delete the files
     * ファイルを削除
     *
     * @param array $operationList Operation List 操作一覧
     *                             array[$ii]
     */
    private function deleteFiles(&$operationList){
        static $ii = 0;
        foreach($operationList as $fileOperation){
            if($fileOperation->operationId === FileOperation::OPERATION_ID_DELETE){
                $fp = $this->lock($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo, LOCK_EX);
                if(!$fp){
                    throw new AppException("[".__FUNCTION__."]"." [Failed lock] itemId: ".$fileOperation->itemId. " attrId: ". $fileOperation->attrId. " fileNo: ". $fileOperation->fileNo);
                }
                
                try{
                    $this->deleteFile($fileOperation);
                } catch (AppException $e){
                    $this->unlock($fp, $fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
                    throw $e;
                }
                
                $this->unlock($fp, $fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
            }
        }
    }
    
    /**
     * Remove the specified file
     * 指定されたファイルを削除する
     * 
     * @param FileOperation $fileOperation File operation contents ファイル操作内容
     */
    private function deleteFile(&$fileOperation){
        // delete file
        $ext = $this->selectFileExtension($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
        $backupContentFilePath = $this->generateBackupFilePath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo, $ext);
        
        // 本文ファイルの削除を実行する。本文ファイルがない場合は警告ログを出力する
        if(file_exists($fileOperation->contentFilePath)){
            try{
                // 本文ファイルを退避する。エラー発生時は警告ログを出力する
                Repository_Components_Util_OperateFileSystem::rename($fileOperation->contentFilePath, $backupContentFilePath);
                $this->debugLog("[". __FUNCTION__. "] rename: ". $fileOperation->contentFilePath. " -> ". $backupContentFilePath, __FILE__, __CLASS__, __LINE__);
                $fileOperation->backupContentFilePath = $backupContentFilePath;
            } catch(AppException $ex){
                $this->warnLog("[".__FUNCTION__."]"." [Rename] oldName:".$fileOperation->contentFilePath.", newName:". $backupContentFilePath, __FILE__, __CLASS__, __LINE__);
            }
        } else {
            $this->warnLog("[".__FUNCTION__."]"." [No file] filePath:".$fileOperation->contentFilePath, __FILE__, __CLASS__, __LINE__);
        }
        
        // FLASHファイルの削除を実施する。FLASHファイルの作成は任意であるため、FLASHファイルがない場合は警告ログを出力しない
        if(file_exists($fileOperation->flashDirectoryPath)){
            $backupFlashDirectory = $this->generateBackupDirectoryPath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo). DIRECTORY_SEPARATOR;
            try{
                // FLASHファイルを退避する。エラー発生時は警告ログを出力する
                Repository_Components_Util_OperateFileSystem::rename($fileOperation->flashDirectoryPath, $backupFlashDirectory);
                $this->debugLog("[". __FUNCTION__. "] rename: ". $fileOperation->flashDirectoryPath. " -> ". $backupFlashDirectory, __FILE__, __CLASS__, __LINE__);
                $fileOperation->backupFlashDirectoryPath = $backupFlashDirectory;
            } catch(AppException $ex){
                $this->warnLog("[".__FUNCTION__."]"." [Rename] oldName:".$fileOperation->flashDirectoryPath.", newName:". $backupFlashDirectory, __FILE__, __CLASS__, __LINE__);
            }
        }
        
        // 旧バージョンのファイルを削除する(ディレクトリごとに作成されているため、ディレクトリがあるかを確認し、ある場合はディレクトリごと削除)
        $versionDirPath = $this->generateVersionDirPath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
        $backupVerDirPath = $this->generateBackupVersionDirPath($fileOperation->itemId, $fileOperation->attrId, $fileOperation->fileNo);
        
        if(file_exists($versionDirPath)){
            Repository_Components_Util_OperateFileSystem::rename($versionDirPath, $backupVerDirPath);
        }
        
        $fileOperation->resultId = FileOperation::RESULT_ID_DONE;
    }
    
    /**
     * Get the extension of the file from the database
     * データベースからファイルの拡張子を取得する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return string File extension ファイル拡張子
     */
    private function selectFileExtension($itemId, $attrId, $fileNo){
        $query = "SELECT extension ". 
                 " FROM ". DATABASE_PREFIX. "repository_file ". 
                 " WHERE item_id = ? ". 
                 " AND attribute_id = ? ". 
                 " AND file_no = ?;";
        $params = array();
        $params[] = $itemId;
        $params[] = $attrId;
        $params[] = $fileNo;
        
        $result = $this->executeSql($query, $params);
        
        return $result[0]["extension"];
    }
    
    /**
     * A designated body file is copied in a designated pass.
     * 指定した本文ファイルを指定したパスにコピーする
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param int $version Version of the file that you want to copy (the latest version is when you specify 0) コピーするファイルのバージョン(0を指定したときは最新バージョン)
     * @param string $dest Destination path コピー先のパス
     */
    public function copyTo($itemId, $attrId, $fileNo, $version, $dest){
        $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " fileNo: ". $fileNo. " dest: ". $dest, __FILE__, __CLASS__, __LINE__);
        
        // 既にupdate、insert関数でロックを取得済みのファイルのコピーでは挿入、更新元のファイルをコピーする
        $fileOperator = $this->findOperation($itemId, $attrId, $fileNo);
        if(isset($fileOperator) && isset($fileOperator->operateContentFilePath)){
            // アップロードされた元ファイルをコピーする
            $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " fileNo: ". $fileNo. " source: ". $fileOperator->operateContentFilePath. " dest: ". $dest, __FILE__, __CLASS__, __LINE__);
            Repository_Components_Util_OperateFileSystem::copy($fileOperator->operateContentFilePath, $dest);
            return;
        }
        
        $fp = $this->lock($itemId, $attrId, $fileNo, LOCK_SH);
        if(!$fp){
            throw new AppException("[".__FUNCTION__."]"." [Failed lock] itemId: ".$itemId. " attrId: ". $attrId. " fileNo: ". $fileNo);
        }
        if(intval($version) === 0){
            $this->debugLog("[". __FUNCTION__. "] copy latest version ", __FILE__, __CLASS__, __LINE__);
            $ext = $this->selectFileExtension($itemId, $attrId, $fileNo);
            
            // コピー
            $source = $this->generateContentFilePath($itemId, $attrId, $fileNo, $ext);
        } else {
            $this->debugLog("[". __FUNCTION__. "] copy old version ", __FILE__, __CLASS__, __LINE__);
            
            $source = $this->generateVersionFilePath($itemId, $attrId, $fileNo, $version);
        }
        
        try{
            Repository_Components_Util_OperateFileSystem::copy($source, $dest);
        } catch (AppException $e){
            // ロック解除・削除
            $this->unlock($fp, $itemId, $attrId, $fileNo);
            throw $e;
        }
        $this->debugLog("[". __FUNCTION__. "] copy: ". $source. " -> ". $dest, __FILE__, __CLASS__, __LINE__);
        
        // ロック解除・削除
        $this->unlock($fp, $itemId, $attrId, $fileNo);
    }
    
    /**
     * A designated FLASH file is copied in a designated path.
     * 指定したFLASHファイルを指定したパスにコピーする
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $fileName File name ファイル名
     * @param string $dest Destination path コピー先のファイルパス
     */
    public function copyPreviewTo($itemId, $attrId, $fileNo, $fileName, $dest){
        $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " fileNo: ". $fileNo. " fileName: ". $fileName. " dest: ". $dest, __FILE__, __CLASS__, __LINE__);
        
        // 既にupdate、insert関数でロックを取得済みのファイルのコピーでは挿入、更新元のファイルをコピーする
        $fileOperator = $this->findOperation($itemId, $attrId, $fileNo);
        if(isset($fileOperator)){
            if(isset($fileOperator->operateFlashFilePathList)){
                // アップロードされたFLASHファイルからコピーする
                foreach($fileOperator->operateFlashFilePathList as $operateFlashFilePath){
                    // ファイル名を取得、コピーする
                    $pathStrList = explode(DIRECTORY_SEPARATOR, $operateFlashFilePath);
                    if($pathStrList[count($pathStrList) - 1] == $fileName){
                        $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " fileNo: ". $fileNo. " source: ". $operateFlashFilePath. " dest: ". $dest, __FILE__, __CLASS__, __LINE__);
                        Repository_Components_Util_OperateFileSystem::copy($operateFlashFilePath, $dest);
                    }
                }
            }
            return;
        }
        
        $fp = $this->lock($itemId, $attrId, $fileNo, LOCK_SH);
        if(!$fp){
            throw new AppException("[".__FUNCTION__."]"." [Failed lock] itemId: ".$itemId. " attrId: ". $attrId. " fileNo: ". $fileNo);
        }
        
        // コピー
        $source = $this->generateFlashDirectoryPath($itemId, $attrId, $fileNo). DIRECTORY_SEPARATOR.$fileName;
        try{
           Repository_Components_Util_OperateFileSystem::copy($source, $dest);
        } catch(AppException $e) {
            // ロック解除・削除
            $this->unlock($fp, $itemId, $attrId, $fileNo);
            throw $e;
        }
        $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " fileNo: ". $fileNo. " source: ". $source. " dest: ". $dest, __FILE__, __CLASS__, __LINE__);
        
        // ロック解除・削除
        $this->unlock($fp, $itemId, $attrId, $fileNo);
    }
    
    /**
     * Get file information (information to be acquired as well as the return value of stat function)
     * ファイル情報を取得する(取得する情報はstat関数の戻り値と同様)
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return array File information(Device number, inode number (always in Windows 0), inode protected mode, number of links, the owner of the user ID (always in Windows 0), the owner of the group ID (in Windows always 0), in the case of the inode device, the device type, size in bytes, last access time (Unix timestamp), time of last modification (Unix timestamp), last inode change time (Unix timestamp), on systems that support the block size (st_blksize type of file IO onlyIt enabled), ensuring the number of 512-byte blocks (valid only for systems that support st_blksize type)) 
     *                       ファイル情報(デバイス番号,inode 番号(Windows では常に 0),inode プロテクトモード,リンク数,所有者のユーザー ID(Windows では常に 0),所有者のグループ ID(Windows では常に 0),inode デバイス の場合、デバイスの種類,バイト単位のサイズ,最終アクセス時間 (Unix タイムスタンプ),最終修正時間 (Unix タイムスタンプ),最終 inode 変更時間 (Unix タイムスタンプ),ファイル IO のブロックサイズ(st_blksize タイプをサポートするシステムでのみ有効),512 バイトのブロックの確保数(st_blksize タイプをサポートするシステムでのみ有効))
     *                       array[0-12|"dev"|"ino"|"mode"|"nlink"|"uid"|"gid"|"rdev"|"size"|"atime"|"mtime"|"ctime"|"blksize"|"blocks"]
     */
    public function getFileStat($itemId, $attrId, $fileNo){
        $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " fileNo: ". $fileNo, __FILE__, __CLASS__, __LINE__);
        
        $fp = $this->lock($itemId, $attrId, $fileNo, LOCK_SH);
        if(!$fp){
            throw new AppException("[".__FUNCTION__."]"." [Failed lock] itemId: ".$itemId. " attrId: ". $attrId. " fileNo: ". $fileNo);
        }
        
        $ext = $this->selectFileExtension($itemId, $attrId, $fileNo);
        $filePath = $this->generateContentFilePath($itemId, $attrId, $fileNo, $ext);
        
        try{
            $result = Repository_Components_Util_OperateFileSystem::stat($filePath);
        } catch (AppException $e){
            // ロック解除・削除
            $this->unlock($fp, $itemId, $attrId, $fileNo);
            throw $e;
        }
        
        // ロック解除・削除
        $this->unlock($fp, $itemId, $attrId, $fileNo);
        
        return $result;
    }
    
    /**
     * Get flash file information (information to be acquired as well as the return value of stat function and add a file nameadd a file name)
     * FLASHファイル情報を取得する(取得する情報はstat関数の戻り値にファイル名を追加したもの)
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return array|boolean File information(Device number, inode number (always in Windows 0), inode protected mode, number of links, the owner of the user ID (always in Windows 0), the owner of the group ID (in Windows always 0), in the case of the inode device, the device type, size in bytes, last access time (Unix timestamp), time of last modification (Unix timestamp), last inode change time (Unix timestamp), on systems that support the block size (st_blksize type of file IO onlyIt enabled), ensuring the number of 512-byte blocks (valid only for systems that support st_blksize type)) 
     *                       ファイル情報(デバイス番号,inode 番号(Windows では常に 0),inode プロテクトモード,リンク数,所有者のユーザー ID(Windows では常に 0),所有者のグループ ID(Windows では常に 0),inode デバイス の場合、デバイスの種類,バイト単位のサイズ,最終アクセス時間 (Unix タイムスタンプ),最終修正時間 (Unix タイムスタンプ),最終 inode 変更時間 (Unix タイムスタンプ),ファイル IO のブロックサイズ(st_blksize タイプをサポートするシステムでのみ有効),512 バイトのブロックの確保数(st_blksize タイプをサポートするシステムでのみ有効))
     *                       array[0-12|"dev"|"ino"|"mode"|"nlink"|"uid"|"gid"|"rdev"|"size"|"atime"|"mtime"|"ctime"|"blksize"|"blocks"|"name"]
     *                       false 取得失敗
     */
    public function getPreviewFileStat($itemId, $attrId, $fileNo){
        $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " fileNo: ". $fileNo, __FILE__, __CLASS__, __LINE__);
        
        $fp = $this->lock($itemId, $attrId, $fileNo, LOCK_SH);
        if(!$fp){
            throw new AppException("[".__FUNCTION__."]"." [Failed lock] itemId: ".$itemId. " attrId: ". $attrId. " fileNo: ". $fileNo);
        }
        
        $result = false;
        $flashPath = $this->generateFlashDirectoryPath($itemId, $attrId, $fileNo). DIRECTORY_SEPARATOR;
        foreach(array("weko.swf", "weko1.swf", "weko.flv") as $name){
            if(!file_exists($flashPath.$name)){
                continue;
            }
            try{
                $result = Repository_Components_Util_OperateFileSystem::stat($flashPath.$name);
                if($result !== false){
                    // ファイル名を戻り値に追加
                    $result["name"] = $name;
                    break;
                }
            } catch (AppException $e){
                // ロック解除・削除
                $this->unlock($fp, $itemId, $attrId, $fileNo);
                throw $e;
            }
        }
        
        // ロック解除・削除
        $this->unlock($fp, $itemId, $attrId, $fileNo);
        
        return $result;
    }
    
    /**
     * Create a content file path
     * 本文ファイルパスを作成
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $ext File extension ファイル拡張子
     * @return string Content file path 本文ファイルパス
     */
    private function generateContentFilePath($itemId, $attrId, $fileNo, $ext){
        return $this->contentFileStragePath. DIRECTORY_SEPARATOR. $itemId. "_". $attrId. "_". $fileNo. ".". $ext;
    }
    
    /**
     * Create a update file path
     * 更新ファイルパスを作成
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $ext File extension ファイル拡張子
     * @return string Update file path 更新ファイルパス
     */
    private function generateTempFilePath($itemId, $attrId, $fileNo, $ext){
        $contentFilePath = $this->generateContentFilePath($itemId, $attrId, $fileNo, $ext). ".tmp";
        return $contentFilePath;
    }
    
    /**
     * Create a backup file path
     * 退避ファイルパスを作成
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $ext File extension ファイル拡張子
     * @return string Backup file path 退避ファイルパス
     */
    private function generateBackupFilePath($itemId, $attrId, $fileNo, $ext){
        $contentFilePath = $this->generateContentFilePath($itemId, $attrId, $fileNo, $ext). ".bak";
        return $contentFilePath;
    }
    
    /**
     * It returns the path of the old version of the file specified version
     * 指定したバージョンの旧バージョンファイルのパスを返す
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param int $version File version ファイルバージョン
     * @return string the path of the old version of the file 旧バージョンファイルのパス
     */
    private function generateVersionFilePath($itemId, $attrId, $fileNo, $version){
        // 拡張子を取得
        $query = "SELECT physical_file_name ". 
                 " FROM ". DATABASE_PREFIX. "repository_file_update_history ".
                 " WHERE item_id = ? ".
                 " AND item_no = ? ". 
                 " AND attribute_id = ? ". 
                 " AND file_no = ? ". 
                 " AND version = ?;";
        $params = array();
        $params[] = $itemId;
        $params[] = 1;
        $params[] = $attrId;
        $params[] = $fileNo;
        $params[] = $version;
        $result = $this->executeSql($query, $params);
        $pathInfo = pathinfo($result[0]["physical_file_name"]);
        $extension = $pathInfo["extension"];
        
        return $this->versionFileStragePath. DIRECTORY_SEPARATOR. 
                $itemId. "_". $attrId. "_". $fileNo. DIRECTORY_SEPARATOR. 
                $version. ".". $extension;
    }
    
    /**
     * Return the temporary path of the old version of the file specified version
     * 指定したバージョンの旧バージョンファイルの一時パスを返す
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param int $version File version ファイルバージョン
     * @return string the path of the old version of the file 旧バージョンファイルの一時パス
     */
    private function generateTempVersionFilePath($itemId, $attrId, $fileNo, $version){
        $filePath = $this->generateVersionFilePath($itemId, $attrId, $fileNo, $version);
        
        return $filePath. ".tmp";
    }
    
    /**
     * Create a FLASH directory path
     * FLASHディレクトリパスを作成
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return string FLASH directory path FLASHディレクトリパス
     */
    private function generateFlashDirectoryPath($itemId, $attrId, $fileNo){
        return $this->flashFileDirectoryStragePath. DIRECTORY_SEPARATOR. $itemId. "_". $attrId. "_". $fileNo;
    }
    
    /**
     * Create a update directory path
     * 更新ディレクトリパスを作成
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return string Update directory path 更新ディレクトリパス
     */
    private function generateTempDirectoryPath($itemId, $attrId, $fileNo){
        $flashDirectoryPath = $this->generateFlashDirectoryPath($itemId, $attrId, $fileNo). ".tmp";
        return $flashDirectoryPath;
    }
    
    /**
     * Create a backup directory path
     * 退避ディレクトリパスを作成
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return string Backup directory path 退避ディレクトリパス
     */
    private function generateBackupDirectoryPath($itemId, $attrId, $fileNo){
        $flashDirectoryPath = $this->generateFlashDirectoryPath($itemId, $attrId, $fileNo). ".bak";
        return $flashDirectoryPath;
    }
    
    /**
     * Produce a version directory path
     * バージョンディレクトリパスを生成
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return string Version directory path バージョンディレクトリパス
     */
    private function generateVersionDirPath($itemId, $attrId, $fileNo){
        $versionDirPath = $this->versionFileStragePath. DIRECTORY_SEPARATOR. 
                          $itemId. "_". $attrId. "_". $fileNo;
        return $versionDirPath;
    }
    
    /**
     * Generating a save directory path of the version directory
     * バージョンディレクトリの退避ディレクトリパスを生成
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return string Save directory path of the version directory バージョンディレクトリの退避ディレクトリパス
     */
    private function generateBackupVersionDirPath($itemId, $attrId, $fileNo){
        $versionDirPath = $this->generateVersionDirPath($itemId, $attrId, $fileNo). ".bak";
        return $versionDirPath;
    }
    
    /**
     * Configuration file reading
     * 設定ファイル読込
     *
     */
    protected function onInitialize(){
        $config = parse_ini_file(BASE_DIR.'/webapp/modules/repository/config/main.ini');
        
        $this->contentFileStragePath = $config["define:_REPOSITORY_FILE_SAVE_PATH"];
        if(strlen($this->contentFileStragePath) == 0){
            // default directory
            $this->contentFileStragePath = BASE_DIR.'/webapp/uploads/repository/files';
        }
        
        $this->flashFileDirectoryStragePath = $config["define:_REPOSITORY_FLASH_SAVE_PATH"];
        if(strlen($this->flashFileDirectoryStragePath) == 0){
            // default directory
            $this->flashFileDirectoryStragePath = BASE_DIR.'/webapp/uploads/repository/flash';
        }
        
        $this->versionFileStragePath = WEBAPP_DIR. DIRECTORY_SEPARATOR. "uploads/repository/versionFiles";
    }
    
    /**
     * Unlock files that have not been released by the error of the file registration and update
     * ファイル登録や更新のエラーで解除されなかったロックファイルを解除する
     *
     */
    protected function onFinalize(){
        // update、insert時に失敗したとき、一時ファイルやロックファイルがまだ残っているので削除する
        // DBエラー等で削除されないまま残っている一時ファイルを削除する
        $this->removeTempFiles($this->operationList);
        
        // 既に解除したロックは再度解除しないよう、ロックを解除する
        $this->unlockFiles($this->operationList);
        
        // 全ての終了処理が終わったので、操作一覧を開放する
        $this->operationList = array();
    }
    
    /**
     * When an error occurs, delete the temporary files that have not been deleted while adding
     * エラー発生時、追加したまま削除されていない一時ファイルを削除する
     *
     * @param FileOperation $operationList File operation list ファイル操作一覧
     *                                     array[$ii]
     */
    private function removeTempFiles($operationList){
        foreach($operationList as $fileOperation){
            // ロックが実施されているファイルのみを削除する
            // insertやupdateのため作成した一時本文ファイルを削除する
            if(isset($fileOperation->tmpContentFilePath) && strlen($fileOperation->tmpContentFilePath) > 0){
                Repository_Components_Util_OperateFileSystem::unlink($fileOperation->tmpContentFilePath);
            }
            
            // 過去バージョンの一時ファイルがあれば削除する
            if(isset($fileOperation->tmpVersionFilePath) && strlen($fileOperation->tmpVersionFilePath) > 0){
                Repository_Components_Util_OperateFileSystem::unlink($fileOperation->tmpVersionFilePath);
            }
            
            // insertやupdateのため作成した一時flashファイルを削除する
            if(isset($fileOperation->tmpFlashDirectoryPath) && strlen($fileOperation->tmpFlashDirectoryPath) > 0){
                Repository_Components_Util_OperateFileSystem::removeDirectory($fileOperation->tmpFlashDirectoryPath);
            }
        }
    }
    
    /**
     * Carried a lock(Success at the time of the resource of the lock file is returned, on failure is returned false)
     * ロックを実施する(成功時はロックファイルのリソースが返り、失敗時はfalseが返る)
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param int $operation Lock operation ロック操作（LOCK_EX,LOCK_SH、LOCK_EX | LOCK_NB,LOCK_SH | LOCK_NB）
     * @return resource Lock file pointer resource ロックファイルのファイルポインタリソース
     */
    private function lock($itemId, $attrId, $fileNo, $operation) {
        $this->debugLog("[". __FUNCTION__. "] ", __FILE__, __CLASS__, __LINE__);
        $lockPath = BASE_DIR.'/webapp/uploads/repository/'. $itemId."_".$attrId."_".$fileNo.".lock";
        
        $ret = null;
        $handle = Repository_Components_Util_OperateFileSystem::fopen($lockPath, "w");
        
        if(flock($handle, $operation)) {
            $ret = $handle;
            $this->debugLog("[Success lock file] lockfile: ". $lockPath. " operation: ". $operation, __FILE__, __CLASS__, __LINE__);
        } else {
            // lock failed
            Repository_Components_Util_OperateFileSystem::fclose($handle);
            $this->debugLog("[Failed lock file] lockfile: ". $lockPath. " operation: ". $operation, __FILE__, __CLASS__, __LINE__);
            return false;
        }
        
        return $ret;
    }
    
    /**
     * Unlock and remove the lock file
     * ロックを解除し、ロックファイルを削除する
     *
     * @param resource $resource Lock file pointer resource ロックファイルのファイルポインタリソース
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     */
    private function unlock(&$resource, $itemId, $attrId, $fileNo){
        if(!isset($resource)){
            return;
        }
        
        if(!flock($resource, LOCK_UN)){
            $ex = new AppException("[Failed unlock file] itemId:".$itemId. " attrId:". $attrId. " fileNo:". $fileNo);
            $this->errorLog($ex->__toString(), __FILE__, __CLASS__, __LINE__);
        }
        $this->debugLog("[Success unlock file] itemId:".$itemId. " attrId:". $attrId. " fileNo:". $fileNo, __FILE__, __CLASS__, __LINE__);
        
        Repository_Components_Util_OperateFileSystem::fclose($resource);
        
        $resource = null;
        
        $lockPath = BASE_DIR.'/webapp/uploads/repository/'. $itemId."_".$attrId."_".$fileNo.".lock";
        
        try{
            Repository_Components_Util_OperateFileSystem::unlink($lockPath);
        } catch(AppException $ex){
            // ロックファイルの削除が失敗したとしても後続処理に問題はなく、ゴミファイルが残るだけであるため、例外発生のログだけ出力し、処理を続行する
            $this->exeptionLog($ex, __FILE__, __CLASS__, __LINE__);
        }
    }
    
    /**
     * And returns an instance of the specified file operation structure and (or null if it does not exist)
     * 指定したファイル操作構造体のインスタンスを返す(存在しない場合はnull)
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return FileOperation Instance of the file operation structure ファイル操作構造体のインスタンス
     */
    private function findOperation($itemId, $attrId, $fileNo){
        $key = sprintf("%010d_%010d_%010d", $itemId, $attrId, $fileNo);
        
        if(isset($this->operationList[$key])){
            return $this->operationList[$key];
        } else {
            return null;
        }
    }
    
    /**
     * Create an instance of the file operation structure with the specified value, to be added to the list
     * 指定した値でファイル操作構造体のインスタンスを作成し、リストに追加する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $operateContentFilePath To register and update content file path 登録や更新する本文ファイルパス
     * @param string $operateFlashFilePathList To register and update FLASH file path list 登録や更新するFLASHファイルパスリスト
     * @param string $operationId Operation id 操作ID
     * @return FileOperation Instance of the file operation structure ファイル操作構造体のインスタンス
     */
    private function addOperation($itemId, $attrId, $fileNo, $operateContentFilePath, $operateFlashFilePathList, $operationId){
        $key = sprintf("%010d_%010d_%010d", $itemId, $attrId, $fileNo);
        
        $contentFilePath = null;
        $flashDirectoryPath = null;
        
        if($operationId === FileOperation::OPERATION_ID_DELETE){
            // DELETE時は操作対象のファイルなどは確実にnullとなるため、データベースから拡張子を取得する
            $ext = $this->selectFileExtension($itemId, $attrId, $fileNo);
            $contentFilePath = $this->generateContentFilePath($itemId, $attrId, $fileNo, $ext);
            $flashDirectoryPath = $this->generateFlashDirectoryPath($itemId, $attrId, $fileNo). DIRECTORY_SEPARATOR;
        } else {
            if(isset($operateContentFilePath)){
                $fileInfo = pathinfo($operateContentFilePath);
                $ext = $fileInfo["extension"];
                $contentFilePath = $this->generateContentFilePath($itemId, $attrId, $fileNo, $ext);
            }
            if(isset($operateFlashFilePathList)){
                $flashDirectoryPath = $this->generateFlashDirectoryPath($itemId, $attrId, $fileNo). DIRECTORY_SEPARATOR;
            }
        }
        
        $fileOperation = new FileOperation($itemId, $attrId, $fileNo, $operateContentFilePath, $operateFlashFilePathList, $operationId, $contentFilePath, $flashDirectoryPath);
        $this->operationList[$key] = $fileOperation;
        
        return $fileOperation;
    }
    
    /**
     * To update the file table
     * ファイルテーブルの更新を行う
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $filePath Updating the target file path 更新対象のファイルパス
     * @param string $mimeType Mime Type of updated files 更新対象ファイルのMyme_type
     */
    private function updateFileTable($itemId, $attrId, $fileNo, $filePath, $mimeType){
        // ファイルパスよりファイル名、拡張子を取得
        $fileName = Repository_Components_Util_Stringoperator::extractFileNameFromFilePath($filePath);
        $pathInfo = pathinfo($filePath);
        $extension = $pathInfo["extension"];
        
        // ファイルテーブル更新
        $query = "UPDATE ". DATABASE_PREFIX. "repository_file". 
                 " SET file_name = ?, ".
                 "   mime_type = ?, ". 
                 "   extension = ?, ". 
                 "   file_prev = ?, ". 
                 "   file_prev_name = ?, ". 
                 "   mod_date = ?, ". 
                 "   mod_user_id = ? ". 
                 " WHERE item_id = ? ".
                 " AND item_no = ? ".
                 " AND attribute_id = ? ".
                 " AND file_no = ?;";
        $params = array();
        $params[] = $fileName;          // file_name
        $params[] = $mimeType;          // mime_type
        $params[] = $extension;         // extension
        $params[] = "";                 // file_prev
        $params[] = "";                 // file_prev_name
        $params[] = $this->accessDate;  // mod_date
        $params[] = $this->user_id;     // mod_user_id
        $params[] = $itemId;            // item_id
        $params[] = 1;                  // item_no
        $params[] = $attrId;            // attribute_id
        $params[] = $fileNo;            // file_no
        $this->executeSql($query, $params);
    }
    
    /**
     * To add a record to the file update history table
     * ファイル更新履歴テーブルへレコードを追加する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     */
    private function addFileUpdateHistory($itemId, $attrId, $fileNo){
        // バージョンを取得する(MAX(VERSION)+1)
        $query = "SELECT MAX(version) + 1 AS NEW_VER ". 
                 " FROM ". DATABASE_PREFIX. "repository_file_update_history ". 
                 " WHERE item_id = ? ". 
                 " AND item_no = ? ". 
                 " AND attribute_id = ? ". 
                 " AND file_no = ?;";
        $params = array();
        $params[] = $itemId;
        $params[] = 1;
        $params[] = $attrId;
        $params[] = $fileNo;
        $result = $this->executeSql($query, $params);
        if(isset($result[0]["NEW_VER"])){
            $newVersion = $result[0]["NEW_VER"];
            
            $query = "SELECT ins_user_id, ins_date ". 
                     " FROM ". DATABASE_PREFIX. "repository_file_update_history ". 
                     " WHERE item_id = ? ". 
                     " AND item_no = ? ". 
                     " AND attribute_id = ? ". 
                     " AND file_no = ? ". 
                     " AND version = ?;";
            $params = array();
            $params[] = $itemId;
            $params[] = 1;
            $params[] = $attrId;
            $params[] = $fileNo;
            $params[] = $newVersion - 1;
            $result = $this->executeSql($query, $params);
            
            // ファイル更新履歴を追加する(SQL内で必要な情報を渡す)
            $query = "INSERT INTO ". DATABASE_PREFIX. "repository_file_update_history ".
                     " (". 
                     "   item_id, item_no, attribute_id, file_no, ".
                     "   version, file_update_date, physical_file_name, file_update_user_id, ". 
                     "   file_shown_state, mime_type, ins_user_id, mod_user_id, del_user_id, ". 
                     "   ins_date, mod_date, del_date, is_delete". 
                     " )". 
                     " SELECT item_id, item_no, attribute_id, file_no, ".
                     "   ?, ?, file_name, ?, ". 
                     "   ?, mime_type, ?, ?, ?, ".
                     "   ?, ?, ?, ? ". 
                     " FROM ". DATABASE_PREFIX. "repository_file ".
                     " WHERE item_id = ? ". 
                     " AND item_no = ? ". 
                     " AND attribute_id = ? ". 
                     " AND file_no = ?;";
        } else {
            $newVersion = 1;
            // ファイル更新履歴を追加する(SQL内で必要な情報を渡す)
            $query = "INSERT INTO ". DATABASE_PREFIX. "repository_file_update_history ".
                     " (". 
                     "   item_id, item_no, attribute_id, file_no, ".
                     "   version, file_update_date, physical_file_name, file_update_user_id, ". 
                     "   file_shown_state, mime_type, ins_user_id, mod_user_id, del_user_id, ". 
                     "   ins_date, mod_date, del_date, is_delete". 
                     " )". 
                     " SELECT item_id, item_no, attribute_id, file_no, ".
                     "   ?, ins_date, file_name, ins_user_id, ". 
                     "   ?, mime_type, ?, ?, ?, ".
                     "   ?, ?, ?, ? ". 
                     " FROM ". DATABASE_PREFIX. "repository_file ".
                     " WHERE item_id = ? ". 
                     " AND item_no = ? ". 
                     " AND attribute_id = ? ". 
                     " AND file_no = ?;";
        }
        $params = array();
        $params[] = $newVersion;                     // version
        if($newVersion > 1)
        {
            $params[] = $result[0]["ins_date"];      // file_update_date
            $params[] = $result[0]["ins_user_id"];   // file_update_user_id
        }
        $params[] = 1;                               // file_shown_state
        $params[] = $this->user_id;                  // ins_user_id
        $params[] = $this->user_id;                  // mod_user_id
        $params[] = "";                              // del_user_id
        $params[] = $this->accessDate;               // ins_date
        $params[] = $this->accessDate;               // mod_date
        $params[] = "";                              // del_date
        $params[] = 0;                               // is_delete
        $params[] = $itemId;                         // item_id
        $params[] = 1;                               // item_no
        $params[] = $attrId;                         // attribute_id
        $params[] = $fileNo;                         // file_no
        $this->executeSql($query, $params);
        
        return $newVersion;
    }
    
    /**
     * To get a list of past versions of the updated history
     * 過去バージョンの更新履歴の一覧を取得する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return array File informations(Device number, inode number (always in Windows 0), inode protected mode, number of links, the owner of the user ID (always in Windows 0), the owner of the group ID (in Windows always 0), in the case of the inode device, the device type, size in bytes, last access time (Unix timestamp), time of last modification (Unix timestamp), last inode change time (Unix timestamp), on systems that support the block size (st_blksize type of file IO onlyIt enabled), ensuring the number of 512-byte blocks (valid only for systems that support st_blksize type)) 
     *               ファイル情報(デバイス番号,inode 番号(Windows では常に 0),inode プロテクトモード,リンク数,所有者のユーザー ID(Windows では常に 0),所有者のグループ ID(Windows では常に 0),inode デバイス の場合、デバイスの種類,バイト単位のサイズ,最終アクセス時間 (Unix タイムスタンプ),最終修正時間 (Unix タイムスタンプ),最終 inode 変更時間 (Unix タイムスタンプ),ファイル IO のブロックサイズ(st_blksize タイプをサポートするシステムでのみ有効),512 バイトのブロックの確保数(st_blksize タイプをサポートするシステムでのみ有効))
     *               array[$version][0-12|"dev"|"ino"|"mode"|"nlink"|"uid"|"gid"|"rdev"|"size"|"atime"|"mtime"|"ctime"|"blksize"|"blocks"]
     */
    public function getUpdateHistory($itemId, $attrId, $fileNo){
        $this->debugLog("[". __FUNCTION__. "] itemId: ". $itemId. " attrId: ". $attrId. " fileNo: ". $fileNo, __FILE__, __CLASS__, __LINE__);
        
        $ret = array();
        
        $fp = $this->lock($itemId, $attrId, $fileNo, LOCK_SH);
        if(!$fp){
            throw new AppException("[".__FUNCTION__."]"." [Failed lock] itemId: ".$itemId. " attrId: ". $attrId. " fileNo: ". $fileNo);
        }
        
        try{
            $query = "SELECT version ". 
                     " FROM ". DATABASE_PREFIX. "repository_file_update_history ". 
                     " WHERE item_id = ? ". 
                     " AND item_no = ? ". 
                     " AND attribute_id = ? ". 
                     " AND file_no = ? ". 
                     " AND is_delete = ?;";
            $params = array();
            $params[] = $itemId;
            $params[] = 1;
            $params[] = $attrId;
            $params[] = $fileNo;
            $params[] = 0;
            $result = $this->executeSql($query, $params);
            
            for($ii = 0; $ii < count($result); $ii++){
                $version = $result[$ii]["version"];
                $filePath = $this->generateVersionFilePath($itemId, $attrId, $fileNo, $version);
                if(file_exists($filePath)){
                    $ret[$version] = Repository_Components_Util_OperateFileSystem::stat($filePath);
                }
            }
        } catch (AppException $e){
            // ロック解除・削除
            $this->unlock($fp, $itemId, $attrId, $fileNo);
            throw $e;
        }
        
        // ロック解除・削除
        $this->unlock($fp, $itemId, $attrId, $fileNo);
        
        return $ret;
    }
    
    /**
     * To update file preview name
     * ファイルプレビュー名の更新を行う
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param string $filePrevName File preview name ファイルプレビュー名
     */
    public function updateFilePrevName($itemId, $attrId, $fileNo, $filePrevName){
        // ファイルテーブル更新
        $query = "UPDATE ". DATABASE_PREFIX. "repository_file". 
                 " SET file_prev_name = ? ". 
                 " WHERE item_id = ? ".
                 " AND item_no = ? ".
                 " AND attribute_id = ? ".
                 " AND file_no = ?;";
        $params = array();
        $params[] = $filePrevName;      // file_prev_name
        $params[] = $itemId;            // item_id
        $params[] = 1;                  // item_no
        $params[] = $attrId;            // attribute_id
        $params[] = $fileNo;            // file_no
        $this->executeSql($query, $params);
    }
}
?>