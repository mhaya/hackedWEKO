<?php

/**
 * Simple WEKO access action class
 * 簡易WEKOアクセスアクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Uri.class.php 71165 2016-08-22 09:20:28Z keiya_sugimoto $
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
 * Simple WEKO access action class
 * 簡易WEKOアクセスアクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007 - 2008, National Institute of Informatics, Research and Development Center for Scientific Information Resources.
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Uri extends RepositoryAction
{
	// component
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
	var $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
	var $Db = null;
	
	// Request param
    /**
     * Item ID of the file to be displayed
     * 表示するファイルのアイテムID
     *
     * @var int
     */
	var $item_id = null;
	//var $attribute_id = null;
    /**
     * Attribute ID of the file to be downloaded
     * 表示するファイルの属性ID
     *
     * @var int
     */
	var $file_id = null; // this parameter is equal attribute_id 
    /**
     * File serial number of the file to be downloaded
     * ダウンロードするファイルのファイル通番
     *
     * @var int
     */
	var $file_no = null; // file no
	
    /**
     * Version information for specifying the saved old file
     * 退避した古いファイルを特定するためのバージョン情報
     *
     * @var int
     */
    public $ver = null;
    
	/**
	 * To implement the screen access or download in accordance with the request parameters
	 * リクエストパラメータに従い画面アクセスまたはダウンロードを実施する
	 */
	function executeApp()
	{
		// check Session and Db Object
		if($this->Session == null){
			$container =& DIContainerFactory::getContainer();
	        $this->Session =& $container->getComponent("Session");
		}
		if($this->Db== null){
			$container =& DIContainerFactory::getContainer();
			$this->Db =& $container->getComponent("DbObject");
		}
		
		// get block_id and page_id
		$block_info = $this->getBlockPageId();

		// Add suppleContentsEntry  Y.Yamazawa --start-- 2015/04/01 --start--
		$this->setLangResource();
		// Add suppleContentsEntry  Y.Yamazawa --end-- 2015/04/01 --end--

		// make redirect URL
		$redirect_url = BASE_URL;
        if($_SERVER["REQUEST_METHOD"] == HTTP_REQUEST_METHOD_PUT){
            // Execute sword update
            $result = $this->executeSwordUpdate($redirect_url, $block_info);
            $this->exitFlag = true;
            if($result === false)
            {
                throw new AppException("ERROR: Failed to SWORD Update by function executeSwordUpdate");
            }
            return;
        } else if($_SERVER["REQUEST_METHOD"] == HTTP_REQUEST_METHOD_DELETE){
            // Execute sword update
            $this->executeSwordDelete($redirect_url, $block_info);
            $this->exitFlag = true;
            return;
        } else if(strlen($this->file_id) == 0){
			// go to item detail
			$redirect_url .= "/?action=pages_view_main".
							 "&active_action=repository_view_main_item_detail".
							 "&item_id=". $this->item_id .
							 "&item_no=1";
			
		} else if(strlen($this->file_no) == 0){
			$query = "SELECT file_no ".
					" FROM ".DATABASE_PREFIX."repository_file ".
					" WHERE item_id = ".$this->item_id." ".
					" AND attribute_id = ".$this->file_id." ; ";
			$result = $this->Db->execute($query);
			if($result === false || count($result) == 0) {
				// go to item detail
				$redirect_url .= "/?action=pages_view_main".
								 "&active_action=repository_view_main_item_detail".
								 "&item_id=". $this->item_id .
								 "&item_no=1";
				// remove download info
			} else if(count($result) > 1){
				$redirect_url .= "/?action=pages_view_main".
								 "&active_action=repository_action_main_export_filedownload".
								 "&item_id=". $this->item_id.
								 "&item_no=1".
								 "&attribute_id=". $this->file_id.
								 "&file_only=true";
			} else {
				$redirect_url .= "/index.php?action=pages_view_main".
							 "&active_action=repository_action_common_download".
							 "&item_id=". $this->item_id .
							 "&item_no=1".
							 "&attribute_id=". $this->file_id .
							 "&file_no=". $result[0]['file_no'];
			}
		} else {
			// go to file download
			$redirect_url .= "/index.php?action=pages_view_main".
							 "&active_action=repository_action_common_download".
							 "&item_id=". $this->item_id .
							 "&item_no=1".
							 "&attribute_id=". $this->file_id .
							 "&file_no=".$this->file_no; 
            // Add file update history 2016/02/26 T.Koyasu --start--
            // ファイル更新履歴
            if(isset($this->ver)){
                $redirect_url .= "&ver=". $this->ver;
            }
            // Add file update history 2016/02/26 T.Koyasu --end--
		}
       
		$redirect_url .= "&page_id=". $block_info["page_id"] .
						 "&block_id=". $block_info["block_id"];
		
		// redirect
		header("HTTP/1.1 301 Moved Permanently");
  		header("Location: ".$redirect_url);
		
		return;
	}
    
    /**
     * Execute sword update
     * SWORD一括更新を実施する
     * 
     * @param string $redirect_url Redirect URL リダイレクトURL
     * @param array $block_info Block information ブロック情報
     * @return boolean Result 結果
     */
    private function executeSwordUpdate($redirect_url, $block_info)
    {
        // Add for error check 2014/09/16 T.Ichikawa --start--
        $error_list = array();
        // Create sword update class
        require_once(WEBAPP_DIR. "/modules/repository/action/main/sword/SwordUpdate.class.php");
        $swordUpdate = new SwordUpdate($this->Session, $this->Db, $this->TransStartDate, true);
        // Add for error check 2014/09/16 T.Ichikawa --end--
        // Get authorize information.
        $authUser = $_SERVER["PHP_AUTH_USER"];
        $authPw = $_SERVER["PHP_AUTH_PW"];
        if(isset($_SERVER['HTTP_X_ON_BEHALF_OF']) && strlen($_SERVER['HTTP_X_ON_BEHALF_OF'])>0 )
        {
            $owner = $_SERVER['HTTP_X_ON_BEHALF_OF'];
        }
        else
        {
            $owner = $authUser;
        }
        
        // Get index infomation
        $insertIndex = "";
        $newIndex = "";
        if(isset($_SERVER['HTTP_INSERT_INDEX']) && strlen($_SERVER['HTTP_INSERT_INDEX'])>0 )
        {
            $insertIndex = $_SERVER['HTTP_INSERT_INDEX'];
        }
        // Add for error check 2014/09/16 T.Ichikawa --start--
        if(!isset($insertIndex)) {
            // エラーで終了してheaderに値詰めて返す
            $error_list[] = new DetailErrorInfo(0, "", "Update index is not set");
            $swordUpdate->setHeader(500, $error_list);
            return true;
        }
        // Add for error check 2014/09/16 T.Ichikawa --end--
        
        if(isset($_SERVER['HTTP_NEW_INDEX']) && strlen($_SERVER['HTTP_NEW_INDEX'])>0 )
        {
            $newIndex = urldecode($_SERVER['HTTP_NEW_INDEX']);
            $newIndex = mb_convert_encoding($newIndex, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS");
        }
        
        // Fix check index_id Y.Nakao 2013/06/07
        
        // Check DOI change mode
        $changeDoiFlag = 0;
        if(isset($_SERVER['HTTP_CHANGE_SELFDOI'])) {
            $changeDoiFlag = $_SERVER['HTTP_CHANGE_SELFDOI'];
        }
        
        // Init
        $swordUpdate->init($this->item_id, 1, $authUser, $authPw, $insertIndex, $newIndex, $changeDoiFlag, $owner);
        
        // Login check
        $result = $swordUpdate->checkSwordLogin($statusCode, $userId);
        if(!$result)
        {
            $swordUpdate->setHeader($statusCode);
            return true;
        }
        
        // Get upload file data
        require_once(WEBAPP_DIR. "/modules/repository/components/RepositoryFileUpload.class.php");
        $fileUpload = new RepositoryFileUpload();
        $fileData = $fileUpload->getUploadData($statusCodeMsg);
        if(empty($fileData))
        {
            $swordUpdate->setHeader($statusCodeMsg);

            return true;
        }
        $this->Session->setParameter("swordFileData", $fileData);
        
        // Execute update
        $result = $swordUpdate->executeSwordUpdate($statusCode, $error_list);
        $swordUpdate->setHeader($statusCode, $error_list);
        return $result;
    }
    
    /**
     * Execute sword delete
     * SWORD削除を行う
     *
     * @param string $redirect_url Redirect URL リダイレクトURL
     * @param array $block_info Block information ブロック情報
     */
    private function executeSwordDelete($redirect_url, $block_info)
    {
        // Get authorize information.
        $authUser = $_SERVER["PHP_AUTH_USER"];
        $authPw = $_SERVER["PHP_AUTH_PW"];
        if(isset($_SERVER['HTTP_X_ON_BEHALF_OF']) && strlen($_SERVER['HTTP_X_ON_BEHALF_OF'])>0)
        {
            $owner = $_SERVER['HTTP_X_ON_BEHALF_OF'];
        }
        else
        {
            $owner = $authUser;
        }
        
        // Create sword delete class
        require_once(WEBAPP_DIR. "/modules/repository/action/main/sword/SwordDelete.class.php");
        $swordDelete = new SwordDelete($this->Session, $this->Db, $this->TransStartDate);
        
        // Init
        $swordDelete->init($this->item_id, 1, $authUser, $authPw, $owner);
        
        // Login check
        $result = $swordDelete->checkSwordLogin($statusCode, $userId);
        if(!$result)
        {
            $swordDelete->setHeader($statusCode);
            return;
        }
        
        // Execute delete
        $swordDelete->executeSwordDelete($statusCode);
        $swordDelete->setHeader($statusCode);
        return;
    }
}
?>
