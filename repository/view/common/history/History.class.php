<?php

/**
 * View class for the file update history pop-up
 * ファイル更新履歴ポップアップ用ビュークラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: History.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Action base class for WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/common/WekoAction.class.php';
/**
 * DB object wrapper Class
 * DBオブジェクトラッパークラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryUserAuthorityManager.class.php';
/**
 * Common classes for user rights management
 * ユーザ権限管理用共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDbAccess.class.php';

/**
 * View class for the file update history pop-up
 * ファイル更新履歴ポップアップ用ビュークラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Common_History extends WekoAction
{
    /**
     * Item ID of the file to be displayed
     * 表示するファイルのアイテムID
     *
     * @var int
     */
    public $item_id = null;
    
    /**
     * Item serial number of the file to be displayed
     * 表示するファイルのアイテム通番
     *
     * @var int
     */
    public $item_no = null;
    
    /**
     * Attribute ID of the file to be displayed
     * 表示するファイルの属性ID
     *
     * @var int
     */
    public $attribute_id = null;
    
    /**
     * File serial number of the file to be displayed
     * 表示するファイルのファイル通番
     *
     * @var int
     */
    public $file_no = null;
    
    /**
     * List of files, including old and new
     * 新旧含めたファイルの一覧
     *
     * @var array[$ii]["version"|"modDate"|"fileName"|"userName"|"downloadLink"|"shownState"|"isCurrent"]
     */
    public $fileList = array();
    
    /**
     * Whether or not the public status can be updated (can be changed only item registration or administrator)
     * 公開状況を更新できるか否か(アイテム登録者又は管理者のみ変更可能)
     *
     * @var boolean
     */
    public $isChangeShownState = false;
    
    /**
     * 
     * ファイル更新履歴表示画面を表示する
     *
     * @return string Result code 実行結果
     */
    protected function executeApp()
    {
        // ファイル更新履歴画面が表示できるか確認し、閲覧不可であればエラーコード(表示するHTMLを明示する文字列)を返す
        $errorCode = $this->isViewFileHistoryPage();
        if(strlen($errorCode) > 0)
        {
            return $errorCode;
        }
        
        $businessName = "businessFileupdatehistorylistoperator";
        $business = BusinessFactory::getFactory()->getBusiness($businessName);
        
        // ファイルの一覧を取得する
        $this->fileList = $business->generateFileHistory($this->item_id, $this->attribute_id, $this->file_no);
        
        // 公開状況を操作できるユーザ(=管理者 or 更新者)は管理列を表示する
        for($ii = 0; $ii < count($this->fileList); $ii++){
            if($this->fileList[$ii]["isChange"]){
                $this->isChangeShownState = true;
                break;
            }
        }
        
        return "success";
    }
    
    /**
     * judged whether reading history screen indication is possible.
     * 閲覧履歴画面表示が可能か判断する。
     * 
     * @return string The indicated HTML file designation character. 空文字=>閲覧可能。空文字以外=>閲覧不可、表示するHTMLファイル指定文字。
     */
    private function isViewFileHistoryPage()
    {
        // ファイル更新履歴を表示しない時は権限不正
        if(!_REPOSITORY_SHOW_FILE_UPDATE_HISTORY_LIST)
        {
            return "invalid";
        }
        
        // リクエストパラメータのファイル指定が無い場合は権限不正
        if( preg_match("/^[1-9][0-9]*$/", $this->item_id) !== 1 || preg_match("/^[1-9][0-9]*$/", $this->item_no) !== 1 ||
        preg_match("/^[1-9][0-9]*$/", $this->attribute_id) !== 1 || preg_match("/^[1-9][0-9]*$/", $this->file_no) !== 1 )
        {
            return "invalid";
        }
        
        $bizItemAuthority = BusinessFactory::getFactory()->getBusiness("businessItemAuthority");
        $bizItemtypeAuthority = BusinessFactory::getFactory()->getBusiness("businessItemtypeAuthority");
        
        // アイテムが削除されている場合はアイテム存在エラー
        if($bizItemAuthority->isItemDeleted($this->item_id, $this->item_no))
        {
            return "invalid";
        }
        
        // カレントファイルが削除されている場合はファイル存在エラー
        if($bizItemAuthority->isFileDeleted($this->item_id, $this->item_no, $this->attribute_id, $this->file_no))
        {
            return "deleted";
        }
        
        // アイテム投稿者であるか確認する
        if($bizItemAuthority->isItemContributor($this->Session->getParameter("_user_id"), $this->item_id, $this->item_no))
        {
            return "";
        }

        // 管理者ユーザーであるか確認する
        $dbAccess = new RepositoryDbAccess($this->Db);
        $userAuthorityManager = new RepositoryUserAuthorityManager($this->Session, $dbAccess, $this->accessDate);
        $user_auth_id = $this->Session->getParameter("_user_auth_id");
        $auth_id = $userAuthorityManager->getRoomAuthorityID();
        if($user_auth_id >= $this->repository_admin_base && $auth_id >= $this->repository_admin_room)
        {
            return "";
        }
        
        // アイテムの閲覧権限を確認する
        if(!$bizItemAuthority->isItemPermission($this->Session, $this->item_id, $this->item_no))
        {
            return "invalid";
        }
        
        // メタデータ項目が非表示の時は権限不正
        if($bizItemtypeAuthority->isItemMetadataTopicHidden($this->item_id, $this->item_no, $this->attribute_id))
        {
            return "invalid";
        }
    }
}
?>