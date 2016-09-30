<?php
/**
 * Covnert to flash business class
 * Flashファイル変換ビジネスクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Converttoflash.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Business Logic base class
 * ビジネスロジック基底クラス
 * 
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 * 
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Y handle (http://id.nii.ac.jp/) cooperative processing common classes
 * Yハンドル(http://id.nii.ac.jp/)連携処理共通クラス
 * 
 */
require_once WEBAPP_DIR. '/modules/repository/components/IDServer.class.php';
/**
 * Item registration and editing process common classes
 * アイテム登録・編集処理共通クラス
 * 
 */
require_once WEBAPP_DIR. '/modules/repository/components/ItemRegister.class.php';
/**
 * Handle management common classes
 * ハンドル管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';

/**
 * Covnert to flash business class
 * Flashファイル変換ビジネスクラス
 * 
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Flash_Converttoflash extends BusinessBase
{
    /**
     * Convert flv, swf, mpeg, files such as pdf to flash file
     * flv、swf、mpeg、pdfなどのファイルをflashファイルに変換する
     *
     * @param int $itemId Item ID to item specific 
     *                    アイテムを特定するためのアイテムID
     * @param string $filePath Convert the target file path 
     *                         コンバート対象のファイルパス
     * @param string $mimeType File type of the conversion target file 
     *                         コンバート対象ファイルのファイル種別
     * @param string $extension Extension of the conversion target file 
     *                          コンバート対象ファイルの拡張子
     * @param array $flashList List of convert the flash file 
     *                          コンバートしたflashファイルの一覧
     *                          array[$ii]
     * @param string $errorMsg Error message 
     *                         エラーメッセージ
     *
     * @return bool Is able to convert flash 
     *              flash変換できたか否か
     */
    public function convertFileToFlash($itemId, $filePath, $mimeType, $extension, &$flashList, &$errorMsg)
    {
        $this->debugLog("[". __FUNCTION__. "] convert", __FILE__, __CLASS__, __LINE__);
        
        // get session from DIContainer
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        
        // RepositoryActionのインスタンス
        $repositoryAction = new RepositoryAction();
        $repositoryAction->Session = $session;
        $repositoryAction->Db = $this->Db;
        $repositoryAction->TransStartDate = $this->accessDate;
        
        $flashList = null;
        
        $isConvertedFlash = true;
        
        //swf または flvファイルの場合
        if( strtolower($extension) == "swf" ||
            strtolower($extension) == "flv")
        {
            $isConvertedFlash = $this->convertSwfOrFlvFileToFlash($filePath, $extension, $flashList);
        }
        // マルチメディアファイルの場合
        else if($repositoryAction->isMultimediaFile(strtolower($mimeType), strtolower($extension)))
        {
            $isConvertedFlash = $this->convertMultimediaFileToFlash($filePath, $mimeType, $extension, $flashList, $errorMsg);
        }
        // マルチメディアファイル以外(pdf, ppt など)
        else if(!RepositoryCheckFileTypeUtility::isImageFile($mimeType, $extension))
        {
            $isConvertedFlash = $this->convertNotMultimediaFileToFlash($itemId, $filePath, $extension, $flashList, $errorMsg);
        }
        
        return $isConvertedFlash;
    }
    
    /**
     * Convert swf or flv files to flash file
     * swfまたはflvファイルをflashファイルに変換する
     *
     * @param string $filePath Convert the target file path 
     *                         コンバート対象のファイルパス
     * @param string $extension Extension of the conversion target file 
     *                          コンバート対象ファイルの拡張子
     * @param array $flashList List of convert the flash file 
     *                          コンバートしたflashファイルの一覧
     *                          array[$ii]
     *
     * @return bool Is able to convert flash 
     *              flash変換できたか否か
     */
    private function convertSwfOrFlvFileToFlash($filePath, $extension, &$flashList)
    {
        // swf, flv のファイルはそのままコピー
        // 本文ファイルを再度コピーし、flashファイル一覧に詰める
        $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness('businessWorkdirectory');
        $tempDirPath = $businessWorkdirectory->create();
        $tempDirPath = substr($tempDirPath, 0, -1);
        $flashPath = $tempDirPath. DIRECTORY_SEPARATOR. "weko.".strtolower($extension);
        Repository_Components_Util_OperateFileSystem::copy($filePath, $flashPath);
        $flashList = array();
        array_push($flashList, $flashPath);
        
        return true;
    }
    
    /**
     * Convert multimedia files to flash file
     * マルチメディアファイルをflashファイルに変換する
     *
     * @param string $filePath Convert the target file path 
     *                         コンバート対象のファイルパス
     * @param string $mimeType File type of the conversion target file 
     *                         コンバート対象ファイルのファイル種別
     * @param string $extension Extension of the conversion target file 
     *                          コンバート対象ファイルの拡張子
     * @param array $flashList List of convert the flash file 
     *                          コンバートしたflashファイルの一覧
     *                          array[$ii]
     * @param string $errorMsg Error message 
     *                         エラーメッセージ
     *
     * @return bool Is able to convert flash 
     *              flash変換できたか否か
     */
    private function convertMultimediaFileToFlash($filePath, $mimeType, $extension, &$flashList, &$errorMsg)
    {
        // get session from DIContainer
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        
        $itemRegister = new ItemRegister($session, $this->Db);
        
        $isConvertedFlash = true;
        
        // マルチメディアファイルは flv へ変換する
        // create arg for convert
        $flashList = $itemRegister->convertFileToFlv($filePath, $extension, $mimeType, $errMsg);
        if(!isset($flashList))
        {
            $this->warnLog("[". __FUNCTION__. "] convert failed. file path = ".$filePath, __FILE__, __CLASS__, __LINE__);
            $isConvertedFlash = false;
        }
        
        return $isConvertedFlash;
    }
    
    /**
     * Convert not multimedia files to flash file
     * マルチメディアでないファイルをflashファイルに変換する
     *
     * @param int $itemId Item ID to item specific 
     *                    アイテムを特定するためのアイテムID
     * @param string $filePath Convert the target file path 
     *                         コンバート対象のファイルパス
     * @param string $extension Extension of the conversion target file 
     *                          コンバート対象ファイルの拡張子
     * @param array $flashList List of convert the flash file 
     *                          コンバートしたflashファイルの一覧
     *                          array[$ii]
     * @param string $errorMsg Error message 
     *                         エラーメッセージ
     *
     * @return bool Is able to convert flash 
     *              flash変換できたか否か
     */
    private function convertNotMultimediaFileToFlash($itemId, $filePath, $extension, &$flashList, &$errorMsg)
    {
        // get session from DIContainer
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        
        $idServer = new IDServer($session, $this->Db);
        $repositoryHandleManager = new RepositoryHandleManager($session, $this->Db, $this->accessDate);
        
        $isConvertedFlash = true;
        
        $prefixId = $repositoryHandleManager->getPrefix(RepositoryHandleManager::ID_Y_HANDLE);
        
        if(strlen($prefixId) > 0){
            $flashData = array();
            $flashData['upload']['file_name'] = basename($filePath);
            $flashData['upload']['extension'] = $extension;
            $url = BASE_URL . "/?action=repository_uri&item_id=".$itemId;
            // PDF to Flash
            $flashResult = $idServer->convertToFlash($flashData, $url, $errorMsg, $filePath, $flashList);
            if($flashResult !== "true"){
                $this->warnLog("[". __FUNCTION__. "] convert failed. file path = ".$filePath, __FILE__, __CLASS__, __LINE__);
                $isConvertedFlash = false;
            }
        }
        else
        {
            $this->warnLog("[". __FUNCTION__. "] not cooperate with IDServer. ", __FILE__, __CLASS__, __LINE__);
            $errorMsg = "Not cooperate with IDServer.";
            $isConvertedFlash = false;
        }
        
        return $isConvertedFlash;
    }
}
?>