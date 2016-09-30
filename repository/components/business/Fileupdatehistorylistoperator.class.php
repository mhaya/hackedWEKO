<?php

/**
 * Common classes to perform the acquisition and operation of the file update history list
 * ファイル更新履歴一覧の取得および操作を行う共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Fileupdatehistorylistoperator.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Common classes for user rights management
 * ユーザ権限管理用共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryUserAuthorityManager.class.php';

/**
 * Common classes to perform the acquisition and operation of the file update history list
 * ファイル更新履歴一覧の取得および操作を行う共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Fileupdatehistorylistoperator extends BusinessBase 
{
    /**
     * It shows that the public status of the version file is public
     * バージョンファイルの公開状況が公開であることを示す
     * 
     * @var int
     */
    const FILE_SHOWN_STATE_IS_PUBLIC = 1;
    
    /**
     * It shows that the public status of the version file is private
     * バージョンファイルの公開状況が非公開であることを示す
     * 
     * @var int
     */
    const FILE_SHOWN_STATE_IS_PRIVATE = 0;
    
    /**
     * Creating a file list, including new and old
     * 新旧含めたファイル一覧の作成
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return array New and old file list 新旧ファイル一覧
     *               array[$ii]["version"|"modDate"|"fileName"|"userName"|"downloadLink"|"shownState"|"isCurrent"|"isChange"]
     *                  "version"       int
     *                  "modDate"       string
     *                  "fileName"      string
     *                  "userName"      string
     *                  "downloadLink"  string
     *                  "shownState"    int
     *                  "isCurrent"     boolean
     *                  "isChange"      boolean
     */
    public function generateFileHistory($itemId, $attrId, $fileNo){
        // アクセスユーザが管理者かどうか
        $isAdmin = $this->isUserAdminUser($this->user_id);
        
        // アイテム登録者を取得
        $itemInsUser = $this->selectItemRegister($itemId);
        
        // 新旧ファイルの一覧を連結
        $fileList = array_merge($this->selectCurrentFile($itemId, $attrId, $fileNo), $this->selectVersionFiles($itemId, $attrId, $fileNo));
        
        if(!$isAdmin && $itemInsUser !== $this->user_id){
            // 管理者でもアイテム登録者でもないなら、公開状況を見て、表示できるか否かを確認する
            for($ii = count($fileList) - 1; $ii > 0; $ii--){
                if($fileList[$ii]["shownState"] == self::FILE_SHOWN_STATE_IS_PUBLIC){
                    // 公開ならば問題はない
                    continue;
                }
                
                // 表示権限のないバージョンファイルは要素ごと削除する
                array_splice($fileList, $ii, 1);
            }
        }
        
        for($ii = 0; $ii < count($fileList); $ii++){
            if($isAdmin || $itemInsUser === $this->user_id){
                $fileList[$ii]["isChange"] = true;
            } else {
                $fileList[$ii]["isChange"] = false;
            }
        }
        
        return $fileList;
    }
    
    /**
     * To get the information of the latest version of the file
     * 最新版ファイルの情報を取得する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return array New file list 新ファイル一覧
     *               array[$ii]["version"|"modDate"|"fileName"|"userName"|"downloadLink"|"shownState"|"isCurrent"]
     */
    private function selectCurrentFile($itemId, $attrId, $fileNo){
        // 過去バージョンが存在する場合、バージョン最大値の作成者・作成日時を参照する
        $query = "SELECT FILE.file_name AS fileName, ". 
                    " HIST.ins_date AS modDate, ".
                    " USERS.handle AS userName ".
                 " FROM ". DATABASE_PREFIX. "repository_file AS FILE, ". 
                    " ". DATABASE_PREFIX. "repository_file_update_history AS HIST, ". 
                    " ". DATABASE_PREFIX. "users AS USERS ". 
                 " WHERE FILE.item_id = ? ". 
                 " AND FILE.item_no = ? ". 
                 " AND FILE.attribute_id = ? ". 
                 " AND FILE.file_no = ? ". 
                 " AND FILE.item_id = HIST.item_id ". 
                 " AND FILE.item_no = HIST.item_no ". 
                 " AND FILE.attribute_id = HIST.attribute_id ". 
                 " AND FILE.file_no = HIST.file_no ". 
                 " AND HIST.ins_user_id = USERS.user_id ". 
                 " AND FILE.is_delete = ? ".
                 " ORDER BY HIST.version DESC ".
                 " LIMIT 0, 1;";
        $params = array();
        $params[] = $itemId;    // アイテムID
        $params[] = 1;          // アイテム通番
        $params[] = $attrId;    // 属性ID
        $params[] = $fileNo;    // ファイル通番
        $params[] = 0;          // 削除フラグ
        $currentFile = $this->executeSql($query, $params);
        
        // 過去バージョンが存在しない場合、ファイルテーブルの作成者・作成日時を参照する
        if(count($currentFile) == 0)
        {
            $query = "SELECT FILE.file_name AS fileName, ". 
                        " FILE.ins_date AS modDate, ".
                        " USERS.handle AS userName ".
                     " FROM ". DATABASE_PREFIX. "repository_file AS FILE, ". 
                        " ". DATABASE_PREFIX. "users AS USERS ". 
                     " WHERE FILE.item_id = ? ". 
                     " AND FILE.item_no = ? ". 
                     " AND FILE.attribute_id = ? ". 
                     " AND FILE.file_no = ? ". 
                     " AND FILE.ins_user_id = USERS.user_id ". 
                     " AND FILE.is_delete = ?;";
            $params = array();
            $params[] = $itemId;    // アイテムID
            $params[] = 1;          // アイテム通番
            $params[] = $attrId;    // 属性ID
            $params[] = $fileNo;    // ファイル通番
            $params[] = 0;          // 削除フラグ
            $currentFile = $this->executeSql($query, $params);
        }
        $currentFile[0]["downloadLink"] = BASE_URL. 
                                            "?action=repository_uri".
                                            "&item_id=". $itemId. 
                                            "&file_id=". $attrId. 
                                            "&file_no=". $fileNo;
        $currentFile[0]["isCurrent"] = true;
        $currentFile[0]["version"] = "Current";
        $currentFile[0]["shownState"] = self::FILE_SHOWN_STATE_IS_PUBLIC;
        
        return $currentFile;
    }
    
    /**
     * To get a list of the version files in descending order
     * 降順でバージョンファイルの一覧を取得する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return array Old file list 旧ファイル一覧
     *               array[$ii]["version"|"modDate"|"fileName"|"userName"|"downloadLink"|"shownState"|"isCurrent"]
     */
    private function selectVersionFiles($itemId, $attrId, $fileNo){
        $query = "SELECT HIST.version AS version, ". 
                    " HIST.file_update_date AS modDate, ". 
                    " HIST.physical_file_name AS fileName, ". 
                    " HIST.file_shown_state AS shownState, ". 
                    " USERS.handle AS userName ".
                 " FROM ". DATABASE_PREFIX. "repository_file_update_history AS HIST, ". 
                    " ". DATABASE_PREFIX. "users AS USERS ". 
                 " WHERE HIST.item_id = ? ". 
                 " AND HIST.item_no = ? ". 
                 " AND HIST.attribute_id = ? ". 
                 " AND HIST.file_no = ? ". 
                 " AND HIST.is_delete = ?". 
                 " AND HIST.file_update_user_id = USERS.user_id ". 
                 " ORDER BY HIST.version DESC;";
        $params = array();
        $params[] = $itemId;    // アイテムID
        $params[] = 1;          // アイテム通番
        $params[] = $attrId;    // 属性ID
        $params[] = $fileNo;    // ファイル通番
        $params[] = 0;          // 削除フラグ
        $versionResultList = $this->executeSql($query, $params);
        
        // 実ファイルのリストを取ってきて、実ファイルがないファイルはダウンロードリンクを表示しない
        $this->infoLog("businessContentfiletransaction", __FILE__, __CLASS__, __LINE__);
        $business = BusinessFactory::getFactory()->getBusiness("businessContentfiletransaction");
        $versionList = $business->getUpdateHistory($itemId, $attrId, $fileNo);
        
        foreach($versionResultList as $key => $vals){
            if(array_key_exists($vals["version"], $versionList)){
                $versionResultList[$key]["downloadLink"] = BASE_URL. 
                                                            "?action=repository_uri".
                                                            "&item_id=". $itemId. 
                                                            "&file_id=". $attrId. 
                                                            "&file_no=". $fileNo.
                                                            "&ver=". $vals["version"];
            } else {
                $versionResultList[$key]["downloadLink"] = "";
            }
            $versionResultList[$key]["isCurrent"] = false; // isCurrent
        }
        return $versionResultList;
    }
    
    /**
     * To get the information of target item register
     * 対象アイテムの登録者の情報を取得する
     *
     * @param int $itemId Item ID アイテムID
     * @return string Item register アイテム登録者
     */
    private function selectItemRegister($itemId){
        $ins_user_id = "";
        $query = "SELECT ITEM.ins_user_id ". 
                 " FROM ". DATABASE_PREFIX. "repository_item AS ITEM ". 
                 " WHERE ITEM.item_id = ? ". 
                 " AND ITEM.item_no = ? ". 
                 " AND ITEM.is_delete = ?;";
        $params = array();
        $params[] = $itemId;    // アイテムID
        $params[] = 1;          // アイテム通番
        $params[] = 0;          // 削除フラグ
        $result = $this->executeSql($query, $params);
        if(count($result) > 0)
        {
            $ins_user_id = $result[0]["ins_user_id"];
        }
        
        return $ins_user_id;
    }
    
    /**
     * Check whether access user is admin user or not
     * アクセスユーザが管理者ユーザかどうか判定する
     *
     * @param string $user_id Access user ID アクセスユーザID
     * @return bool Is or not user admin user ユーザが管理者ユーザかどうか
     */
    private function isUserAdminUser($user_id)
    {
        $user_auth_id = $this->loadUserAuthIdByUserId($user_id);
        
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        $userAuthorityManager = new RepositoryUserAuthorityManager($session, $this->Db, $this->accessDate);
        $auth_id = $userAuthorityManager->getRoomAuthorityID($user_id);
        
        require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
        $repositoryAction = new RepositoryAction();
        $repositoryAction->Session = $session;
        $repositoryAction->Db = $this->Db;
        $repositoryAction->TransStartDate = $this->accessDate;
        $repositoryAction->dbAccess = $this->Db;
        $repositoryAction->setConfigAuthority();
        
        if($user_auth_id >= $repositoryAction->repository_admin_base && $auth_id >= $repositoryAction->repository_admin_room)
        {
            return true;
        }
        return false;
    }
    
    /**
     * Load user_authority_id by user ID
     * ユーザIDからユーザ権限IDをロードする
     *
     * @param  string $user_id User ID ユーザID
     * @return string User authority ID ユーザ権限ID
     */
    private function loadUserAuthIdByUserId($user_id)
    {
        $user_auth_id = "";
        if(strlen($user_id) != 0 && $user_id != "0")
        {
            $query = "SELECT AUTH.user_authority_id ".
                    "FROM ".DATABASE_PREFIX."users AS USERS, ".
                    DATABASE_PREFIX."authorities AS AUTH ".
                    "WHERE USERS.role_authority_id = AUTH.role_authority_id ".
                    "AND USERS.user_id = ? ;";
            $params = array();
            $params[] = $user_id;
            $result = $this->executeSql($query, $params);
            if($result !== false && count($result) > 0)
            {
                $user_auth_id = $result[0]["user_authority_id"];
            }
        }
    
        return $user_auth_id;
    }
    
    /**
     * To change the public status of the file update history in private
     * ファイル更新履歴の公開状況を非公開に変更する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param int $version Version バージョン
     */
    public function changeToPrivate($itemId, $attrId, $fileNo, $version){
        $this->changeFileShownState($itemId, $attrId, $fileNo, $version, self::FILE_SHOWN_STATE_IS_PRIVATE);
    }
    
    /**
     * To change the public status of the file update history to the public
     * ファイル更新履歴の公開状況を公開に変更する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param int $version Version バージョン
     */
    public function changeToPublic($itemId, $attrId, $fileNo, $version){
        $this->changeFileShownState($itemId, $attrId, $fileNo, $version, self::FILE_SHOWN_STATE_IS_PUBLIC);
    }
    
    /**
     * To change the public status of the file update history
     * ファイル更新履歴の公開状況を変更する
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param int $version Version バージョン
     * @param int $fileShownState Changed value 変更値
     */
    private function changeFileShownState($itemId, $attrId, $fileNo, $version, $fileShownState){
        $query = "UPDATE ". DATABASE_PREFIX. "repository_file_update_history". 
                 " SET file_shown_state = ? ". 
                 " WHERE item_id = ? ". 
                 " AND item_no = ? ". 
                 " AND attribute_id = ? ". 
                 " AND file_no = ? ". 
                 " AND version = ?;";
        $params = array();
        $params[] = $fileShownState;
        $params[] = $itemId;
        $params[] = 1;
        $params[] = $attrId;
        $params[] = $fileNo;
        $params[] = $version;
        $this->executeSql($query, $params);
    }
}
?>