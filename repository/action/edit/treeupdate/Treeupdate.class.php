<?php

/**
 * repository edit index tree action
 * ツリー更新Actionクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Treeupdate.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Download process class
 * ダウンロード処理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';
/**
 * Search table manager class
 * 検索テーブル管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositorySearchTableProcessing.class.php';
/**
 * Index manager class
 * インデックス管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryIndexManager.class.php';

/**
 * repository edit index tree action
 * ツリー更新Actionクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Edit_Treeupdate extends RepositoryAction
{
    // component
    /**
     * Data upload objects
     * データアップロードオブジェクト
     *
     * @var Uploads_View
     */
    var $uploadsView = null;
    
    // request parameter for now edit index data
    /**
     * Edit index ID
     * 編集中のインデックスID
     *
     * @var int
     */
    var $edit_id = null;
    /**
     * Index name
     * インデックス名
     *
     * @var string
     */
    var $name_jp = null;                // now edit index japanese name
    /**
     * Index name english
     * インデックス英名
     *
     * @var string
     */
    var $name_en = null;                // now edit index english name
    /**
     * Comment
     * コメント
     *
     * @var string
     */
    var $comment = null;                // now edit index comment
    /**
     * Parent index ID
     * 親インデックスID
     *
     * @var int
     */
    var $pid = null;                    // now edit index parent_index_id
    /**
     * Showr order
     * 表示順序
     *
     * @var int
     */
    var $show_order= null;              // now edit index show order
    /**
     * public flag
     * 公開フラグ値
     *
     * @var int
     */
    var $pub_chk = null;                // now edit index pub flg
    /**
     * public year
     * 公開年
     *
     * @var int
     */
    var $pub_year = null;               // now edit index pub year
    /**
     * public month
     * 公開月
     *
     * @var int
     */
    var $pub_month = null;              // now edit index pub month
    /**
     * public day
     * 公開日
     *
     * @var int
     */
    var $pub_day = null;                // now edit index pub day
    /**
     * access group IDs
     * アクセス可能グループID
     *
     * @var string
     */
    var $access_group_ids = null;       // now edit index entry item group id
    /**
     * not access group ID
     * アクセス不可能グループID
     *
     * @var string
     */
    var $not_access_group_ids = null;   // now edit index not entry item group id
    /**
     * access role ID
     * アクセス可能ユーザー権限
     *
     * @var string
     */
    var $access_role_ids = null;        // now edit index entry item auth id
    /**
     * not access role ID
     * アクセス不可能ユーザー権限
     *
     * @var string
     */
    var $not_access_role_ids = null;    // now edit index not entry item auth id
    /**
     * mod date
     * 更新日
     *
     * @var string
     */
    var $mod_date = null;               // now edit index mod date
    /**
     * Drag from index ID
     * ドラッグ元インデックスID
     *
     * @var int
     */
    var $drag_id = null;                // drag index id at drag event
    /**
     * Drop to index ID
     * ドロップ先インデックスID
     *
     * @var int
     */
    var $drop_id = null;                // drop index id at drop event
    /**
     * Drop flag
     * DD操作フラグ
     *
     * @var bool true/false drop into index/shuffle インデックス内に移動/同階層での並び替え
     */
    var $drop_index = null;             // true  : index drop in index
    //                                     // false : index drop in sentry
    /**
     * "display more" use flag
     * "Display more"の使用フラグ
     *
     * @var int
     */
    var $display_more = null;           // first display child index show all or a little
    /**
     * The number to be displayed in more than
     * "Display more"を表示し始める件数
     *
     * @var int
     */
    var $display_more_num = null;       // first display child index num
    // Add child index display more 2009/01/16 Y.Nakao --end--
    /**
     * RSS display flag
     * RSSアイコン表示フラグ
     *
     * @var int
     */
    var $rss_display = null;            // RSS icon display

    // Add config management authority 2010/02/23 Y.Nakao --start--
    /**
     * access room authority
     * アクセス可能ルーム権限
     *
     * @var int
     */
    var $access_role_room = null;       // now edit index access OK room authority
    // Add config management authority 2010/02/23 Y.Nakao --end--

    // Add contents page 2010/08/06 Y.Nakao --start--
    /**
     * Display type
     * 表示形式
     *
     * @var int
     */
    var $display_type = null;

    /**
     * create PDF cover page flag
     * PDFカバーページ作成フラグ
     *
     * @var int
     */
    public $create_cover_flag = null;

    // Add harvest public flag 2013/07/05 K.Matsuo --start--
    /**
     * Harvest public flag
     * ハーベスト公開フラグ
     *
     * @var int
     */
    public $harvest_public_state = null;
    // Add harvest public flag 2013/07/05 K.Matsuo --end--
    // Add issn and biblio flag 2014/04/16 T.Ichikawa --start--
    /**
     * Bibilio flag
     * 書籍フラグ
     *
     * @var int
     */
    public $biblio_flag = null;
    /**
     * ONLINE ISSN
     * ONLINE ISSN
     *
     * @var string
     */
    public $online_issn = null;
    
    /**
     * Opening child index list
     * オープン状態となっているインデックス一覧
     *
     * @var array
     */
    var $select_index_list_display = null;
    /**
     * Opening child index name list
     * オープン状態となっているインデックス名前一覧
     *
     * @var array
     */
    var $select_index_list_name = null;
    /**
     * Opening child index name english list
     * オープン状態となっているインデックス英名一覧
     *
     * @var array
     */
    var $select_index_list_name_english = null;
    /**
     * Delete thumbnail flag
     * サムネイル削除フラグ
     *
     * @var int
     */
    public $thumbnail_del = null;
    
    // 除外権限情報
    /**
     * Exclusive role IDs
     * アクセス除外ベース権限
     *
     * @var string
     */
    public $exclusiveAclRoleIds = null;
    /**
     * Exclusive room auth
     * アクセス除外ルーム権限
     *
     * @var string
     */
    public $exclusiveAclRoomAuth = null;
    /**
     * Exclusive group id
     * アクセス除外グループID権限
     *
     * @var string
     */
    public $exclusiveAclGroupIds = null;
    
    // デフォルト権限
    /**
     * default access role ids
     * アクセス可能ベース権限初期値
     *
     * @var string
     */
    private $defaultAccessRoleIds_ = '';
    /**
     * default access role room
     * アクセス可能ルーム権限初期値
     *
     * @var int
     */
    private $defaultAccessRoleRoom_ = _AUTH_CHIEF;
    
    // 再帰フラグ
    /**
     * Public date recursive udpate flag
     * 公開日再帰反映フラグ
     *
     * @var int
     */
    public $pubdate_recursive = null;
    /**
     * Create PDF cover page recursive update flag
     * PDFカバーページ付与再帰反映フラグ
     *
     * @var int
     */
    public $create_cover_recursive = null;
    /**
     * Role IDs recursive update flag
     * ベース権限再帰反映フラグ
     *
     * @var int
     */
    public $aclRoleIds_recursive = null;
    /**
     * Room IDs recursive update flag
     * ルーム権限再帰反映フラグ
     *
     * @var int
     */
    public $aclRoomAuth_recursive = null;
    /**
     * Group IDs recursive update flag
     * グループ情報再帰反映フラグ
     *
     * @var int
     */
    public $aclGroupIds_recursive = null;
    
    /**
     * Change browsing authority flag
     * 閲覧権限変更フラグ
     *
     * @var bool
     */
    public $changeBrowsingAuthorityFlag = false; // 閲覧権限変更フラグ

    // インデックス付属情報
    /**
     * Index additional journal type ID
     * インデックス付属雑誌情報タイプID
     *
     * @var array
     */
    public $journalTypeId = null;
    /**
     * Index additional journal information
     * インデックス付属雑誌基本情報
     *
     * @var array
     */
    public $journalInfo = null;
    /**
     * Index additional journal attribute ids
     * インデックス付属雑誌情報属性ID
     *
     * @var array
     */
    public $journalAttrId = null;
    /**
     * Index additional journal attribute value
     * インデックス付属雑誌情報属性値
     *
     * @var array
     */
    public $journalAttrValue = null;
    /**
     * Index additional journal attribute required flag
     * インデックス付属雑誌情報必須属性フラグ
     *
     * @var array
     */
    public $journalAttrRequired = null;

    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws AppException
     */
    function executeApp()
    {
        // セッションの設定
        $this->smartyAssign = $this->Session->getParameter("smartyAssign");
        $this->Session->removeParameter("tree_error_msg");

        $indexManager = new RepositoryIndexManager($this->Session, $this->dbAccess, $this->TransStartDate);
        $businessIndexManager = BusinessFactory::getFactory()->getBusiness("businessIndexmanager");

        $this->setDefaultAccessControlList();

        // インデックスの公開状態を取得する
        $query = "SELECT public_state, parent_index_id, owner_user_id ".
            "FROM ".DATABASE_PREFIX."repository_index ".
            "WHERE index_id = ? ".
            "AND is_delete = 0;";
        $params = array();
        $params[] = $this->edit_id;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        $parent_id = $result[0]['parent_index_id'];
        // 上位インデックスの公開状況を調べる
        if($this->checkParentPublicState($this->edit_id)){  // 上位はすべて公開中
            if($result[0]['public_state'] == "1"){
                // 自身も公開中
                $old_state = "public_all";
            } else {
                // 自身は非公開
                $old_state = "unpublic";
            }
        } else {    // 上位に非公開あり
            $old_state = "unpublic_parent";
        }

        //////////////////////////////////
        // update index data for DB
        //////////////////////////////////

        // get edit index data
        $edit_index_data = $this->getEditIndexParameter($result[0]["owner_user_id"]);
        // update edit data
        $result = $indexManager->updateIndex($edit_index_data);
        if($result === false){
            $this->errorLog("Update index failed.", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Update index failed.");
        } else if($result === "noEnglishName"){
            $this->warnLog("English title is not input.", __FILE__, __CLASS__, __LINE__);
            $this->Session->setParameter("tree_error_msg", $this->smartyAssign->getLang("repository_tree_noEnglishName"));
            throw new AppException("English title is not input.");
        } else if($result === "wrongFormatIssn"){
            $this->warnLog("Wrong format issn value is input.", __FILE__, __CLASS__, __LINE__);
            $this->Session->setParameter("tree_error_msg", $this->smartyAssign->getLang("repository_tree_wrongFormatIssn"));
            throw new AppException("Wrong format issn value is input.");
        }
        
        //////////////////////////////////
        // update index journal for DB
        //////////////////////////////////
        $this->updateAdditionalData();
        
        //////////////////////////////////
        // update recursive
        //////////////////////////////////
        $pubdate_recursive = false;
        if ($this->pubdate_recursive == "true") {
            $pubdate_recursive = true;
        }

        $create_cover_recursive = false;
        if ($this->create_cover_recursive == "true") {
            $create_cover_recursive = true;
        }

        $aclRoleIds_recursive = false;
        if ($this->aclRoleIds_recursive == "true") {
            $aclRoleIds_recursive = true;
        }

        $aclRoomAuth_recursive = false;
        if ($this->aclRoomAuth_recursive == "true") {
            $aclRoomAuth_recursive = true;
        }

        $aclGroupIds_recursive = false;
        if ($this->aclGroupIds_recursive == "true") {
            $aclGroupIds_recursive = true;
        }

        if ($pubdate_recursive || $create_cover_recursive || $aclRoleIds_recursive || $aclRoomAuth_recursive || $aclGroupIds_recursive) {
            $indexManager->recursiveUpdate($edit_index_data, $pubdate_recursive, $create_cover_recursive, $aclRoleIds_recursive, $aclRoomAuth_recursive, $aclGroupIds_recursive);
        }

        //////////////////////////////////
        // update contents number
        //////////////////////////////////
        // 上位インデックスが公開であるか閲覧権限が変更された時のみ再集計を行う
        if($old_state == "public_all"){
            if($this->pub_chk == "false"){
                // 公開 -> 非公開
                // 親インデックスのコンテンツ数から自身のコンテンツ数を引く
                $result = $this->subIndexContents($parent_id, $this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // 自身以下のコンテンツ数をリセットする
                $result = $this->resetContents($this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // このインデックス以下の非公開コンテンツ数を再計算
                $result = $this->recountPrivateContents($this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // 上位インデックスに非公開コンテンツ数追加 add index private_contents num for after move index parent index
                $result = $this->addIndexContents($parent_id, $this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
            } else {
                // 公開 -> 公開
                // 親インデックスの非公開コンテンツ数から自身の非公開コンテンツ数を引く
                $result = $this->subIndexContents($parent_id, $this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // 自身以下のコンテンツ数を再取得する
                $result = $this->recountContents($this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // このインデックス以下の非公開コンテンツ数を再計算
                $result = $this->recountPrivateContents($this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // 親インデックスのコンテンツ数に自身のコンテンツ数を足す
                $result = $this->addIndexContents($parent_id, $this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
            }
        } else if($old_state == "unpublic"){
            if($this->pub_chk != "false"){
                // 非公開 -> 公開
                // 親インデックスの非公開コンテンツ数から自身の非公開コンテンツ数を引く
                $result = $this->subIndexContents($parent_id, $this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // 自身以下のコンテンツ数を再取得する
                $result = $this->recountContents($this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // このインデックス以下の非公開コンテンツ数を再計算
                $result = $this->recountPrivateContents($this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // 親インデックスのコンテンツ数に自身のコンテンツ数を足す
                $result = $this->addIndexContents($parent_id, $this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
            } else {
                // 非公開 -> 非公開
                // 親インデックスのコンテンツ数から自身のコンテンツ数を引く
                $result = $this->subIndexContents($parent_id, $this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // 自身以下のコンテンツ数をリセットする
                $result = $this->resetContents($this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // このインデックス以下の非公開コンテンツ数を再計算
                $result = $this->recountPrivateContents($this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
                // 上位インデックスに非公開コンテンツ数追加 add index private_contents num for after move index parent index
                $result = $this->addIndexContents($parent_id, $this->edit_id);
                if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
                }
            }
        }
        
        if($this->pub_chk == "false"){
            $this->deleteWhatsnewForIndex($this->edit_id);
        }
        
        if($this->changeBrowsingAuthorityFlag) {
            $result = $this->subIndexContents($parent_id, $this->edit_id);
            if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
            }
            // 自身以下のコンテンツ数を再取得する
            $result = $this->recountContents($this->edit_id);
            if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
            }
            // このインデックス以下の非公開コンテンツ数を再計算
            $result = $this->recountPrivateContents($this->edit_id);
            if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
            }
            // 親インデックスのコンテンツ数に自身のコンテンツ数を足す
            $result = $this->addIndexContents($parent_id, $this->edit_id);
            if($result === false){
                    $this->errorLog("Update contents number failed.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("Update contents number failed.");
            }
        }
        
        $this->Session->setParameter("redirect_flg", "tree_update");
        // not remove edit data
        $this->Session->setParameter("edit_tree_continue", "continue");
        
        return 'redirect';
    }

    /**
     * get edit index access group list
     * NetCommonsに登録されている全グループを取得し、投稿権限のあるグループID、グループ名、投稿権限のないグループID、グループ名のリストを取得する。
     *
     *
     * @param int $access_group_id access OK group room ids アクセス許可グループ
     * @param int $exclusive_acl_group_id NG group room ids アクセス不可グループ
     * @param int $edit_index add result in this parameter 編集中のインデックスID
     * @return bool true/false success/failed 成功/失敗
     */
    function getAccessGroupData($access_group_id, $exclusive_acl_group_id, &$edit_index)
    {
        // get access group or not
        $result = $this->getGroupList($all_group, $error);
        if($result === false) {
            return false;;
        }
        // add get (not member)
        $smartyAssign = $this->Session->getParameter("smartyAssign");
        $add_array = array("page_id"=>'0', "page_name"=>$smartyAssign->getLang("repository_item_gest"));
        array_unshift($all_group, $add_array);
        
        // 投稿権限
        $edit_index["access_group_id"] = '';
        $edit_index["access_group_name"] = '';
        $edit_index["not_access_group_id"] = '';
        $edit_index["not_access_group_name"] = '';
        
        // 閲覧権限
        $edit_index["acl_group_id"] = '';
        $edit_index["acl_group_name"] = '';
        $edit_index["exclusive_acl_group_id"] = '';
        $edit_index["exclusive_acl_group_name"] = '';
        
        for($ii=0; $ii<count($all_group); $ii++) {
            if(is_numeric(strpos(",".$access_group_id.",", ",".$all_group[$ii]["page_id"].","))) {
                if($edit_index["access_group_id"] != "") {
                    $edit_index["access_group_id"] .= ",";
                    $edit_index["access_group_name"] .= ",";
                }
                $edit_index["access_group_id"] .= $all_group[$ii]["page_id"];
                $edit_index["access_group_name"] .= '"'.$all_group[$ii]["page_name"].'"';
            } else {
                if($edit_index["not_access_group_id"] != "") {
                    $edit_index["not_access_group_id"] .= ",";
                    $edit_index["not_access_group_name"] .= ",";
                }
                $edit_index["not_access_group_id"] .= $all_group[$ii]["page_id"];
                $edit_index["not_access_group_name"] .= '"'.$all_group[$ii]["page_name"].'"';
            }
            
            if(is_numeric(strpos(",".$exclusive_acl_group_id.",", ",".$all_group[$ii]["page_id"].","))) {
                if(strlen($edit_index["exclusive_acl_group_id"]) > 0) {
                    $edit_index["exclusive_acl_group_id"] .= ",";
                    $edit_index["exclusive_acl_group_name"] .= ",";
                }
                $edit_index["exclusive_acl_group_id"] .= $all_group[$ii]["page_id"];
                $edit_index["exclusive_acl_group_name"] .= '"'.$all_group[$ii]["page_name"].'"';
            } else {
                if(strlen($edit_index["acl_group_id"]) > 0) {
                    $edit_index["acl_group_id"] .= ",";
                    $edit_index["acl_group_name"] .= ",";
                }
                $edit_index["acl_group_id"] .= $all_group[$ii]["page_id"];
                $edit_index["acl_group_name"] .= '"'.$all_group[$ii]["page_name"].'"';
            }
            
        }
        if($edit_index["access_group_name"] == '') {
            $edit_index["access_group_name"] = '""';
        }
        if($edit_index["not_access_group_name"] == '') {
            $edit_index["not_access_group_name"] = '""';
        }
        
        if(strlen($edit_index["acl_group_name"]) == 0) {
            $edit_index["acl_group_name"] = '""';
        }
        if(strlen($edit_index["exclusive_acl_group_name"]) == 0) {
            $edit_index["exclusive_acl_group_name"] = '""';
        }
        
        return true;
    }
    
    /**
     * get edit index access user authority list
     * NetCommonsに登録されている全権限を取得し、投稿権限のある権限ID、権限名、投稿権限のない権限ID、権限名のリストを取得する。
     *
     * @param int $access_role access OK group room ids アクセス可能ベース権限
     * @param int $exclusive_acl_role NG role ID アクセス除外ベース権限
     * @param int $edit_index add result in this parameter 編集中のインデックスID
     * @return bool true/false success/failed 成功/失敗
     */
    function getAccessAuthData($access_role, $exclusive_acl_role, &$edit_index){
        // separate access role base authority and room authority
        $access_auth = explode("|", $access_role);
        $access_role_id = $this->defaultAccessRoleIds_;
        $access_role_room = $this->defaultAccessRoleRoom_;
        if(count($access_auth) == 2) {
            $access_role_id = $access_auth[0];
            $access_role_room = $access_auth[1];
        } else if(count($access_auth) == 1) {
            $access_role_id = $access_auth[0];
        }
        
        $aclAuthorities = explode("|", $exclusive_acl_role);
        $exclusiveAclRoleId = $this->defaultAccessRoleIds_;
        $exclusiveAclRoleRoom = $this->defaultAccessRoleRoom_;
        if(count($aclAuthorities) == 2) {
            // max user_authority_id
            $exclusiveAclRoleId = $aclAuthorities[0];
            $exclusiveAclRoleRoom = $aclAuthorities[1];
        } else if(count($aclAuthorities) == 1) {
            $exclusiveAclRoleId = $aclAuthorities[0];
        }
        
        // get all access auth
        $query = "SELECT * FROM ". DATABASE_PREFIX ."authorities;";
        $all_auth = $this->Db->execute($query);
        if($all_auth === false) {
            return false;
        }
        // add get (not member)
        $smartyAssign = $this->Session->getParameter("smartyAssign");
        
        // 投稿権限
        $edit_index["access_role_id"] = '';
        $edit_index["access_role_name"] = '';
        $edit_index["not_access_role_id"] = '';
        $edit_index["not_access_role_name"] = '';
        
        // 閲覧権限
        $edit_index["acl_role_id"] = '';
        $edit_index["acl_role_name"] = '';
        $edit_index["exclusive_acl_role_id"] = '';
        $edit_index["exclusive_acl_role_name"] = '';
        $edit_index["acl_user_auth_id"] = '';
        $edit_index["exclusive_acl_user_auth_id"] = '';
        
        for($ii=0; $ii<count($all_auth); $ii++) {
            if(is_numeric(strpos(",".$access_role_id.",", ",".$all_auth[$ii]["role_authority_id"].","))) {
                if($edit_index["access_role_id"] != "") {
                    $edit_index["access_role_id"] .= ",";
                    $edit_index["access_role_name"] .= ",";
                }
                $edit_index["access_role_id"] .= $all_auth[$ii]["role_authority_id"];
                $edit_index["access_role_name"] .= '"'.$all_auth[$ii]["role_authority_name"].'"';
            } else {
                if($edit_index["not_access_role_id"] != "") {
                    $edit_index["not_access_role_id"] .= ",";
                    $edit_index["not_access_role_name"] .= ",";
                }
                $edit_index["not_access_role_id"] .= $all_auth[$ii]["role_authority_id"];
                $edit_index["not_access_role_name"] .= '"'.$all_auth[$ii]["role_authority_name"].'"';
            }

            if($exclusiveAclRoleId >= intval($all_auth[$ii]["user_authority_id"])) {
                // Mod access_role_id -> exclusive_acl_role_id
                if($edit_index["exclusive_acl_role_id"] != "") {
                    $edit_index["exclusive_acl_role_id"] .= ",";
                    $edit_index["exclusive_acl_role_name"] .= ",";
                    // add user_authority_id for gimic by Koyasu
                    $edit_index["exclusive_acl_user_auth_id"] .= ",";
                }
                $edit_index["exclusive_acl_role_id"] .= $all_auth[$ii]["role_authority_id"];
                $edit_index["exclusive_acl_role_name"] .= '"'.$all_auth[$ii]["role_authority_name"].'"';
                // add user_authority_id for gimic by Koyasu
                $edit_index["exclusive_acl_user_auth_id"] .= $all_auth[$ii]["user_authority_id"];
            } else {
                if($edit_index["acl_role_id"] != "") {
                    $edit_index["acl_role_id"] .= ",";
                    $edit_index["acl_role_name"] .= ",";
                    // add user_authority_id for gimic by Koyasu
                    $edit_index["acl_user_auth_id"] .= ",";
                }
                $edit_index["acl_role_id"] .= $all_auth[$ii]["role_authority_id"];
                $edit_index["acl_role_name"] .= '"'.$all_auth[$ii]["role_authority_name"].'"';
                // add user_authority_id for gimic by Koyasu
                $edit_index["acl_user_auth_id"] .= $all_auth[$ii]["user_authority_id"];
            }
        }
        if($edit_index["access_role_name"] == '') {
            $edit_index["access_role_name"] = '""';
        }
        if($edit_index["not_access_role_name"] == '') {
            $edit_index["not_access_role_name"] = '""';
        }
        
        if(strlen($edit_index["acl_role_name"]) == 0) {
            $edit_index["acl_role_name"] = '""';
        }
        if(strlen($edit_index["exclusive_acl_role_name"]) == 0) {
            $edit_index["exclusive_acl_role_name"] = '""';
        }

        // set access role for room authority
        $edit_index["room_auth_moderate"] = "false";
        $edit_index["room_auth_general"] = "false";
        if(intval($access_role_room) == _AUTH_GENERAL) {
            $edit_index["room_auth_moderate"] = "true";
            $edit_index["room_auth_general"] = "true";
        } else if(intval($access_role_room) == _AUTH_MODERATE) {
            $edit_index["room_auth_moderate"] = "true";
        }

        // modify true/false with value of exclusive_acl_role column
        $edit_index['acl_room_auth_moderate'] = "true";
        $edit_index['acl_room_auth_general'] = "true";
        $edit_index['acl_room_auth_guest'] = "true";
        $edit_index['acl_room_auth_logout'] = "true";
        if(intval($exclusiveAclRoleRoom) == _AUTH_OTHER) {
            $edit_index['acl_room_auth_logout'] = "false";
        } else if(intval($exclusiveAclRoleRoom) == _AUTH_GUEST) {
            $edit_index['acl_room_auth_guest'] = "false";
            $edit_index['acl_room_auth_logout'] = "false";
        } else if(intval($exclusiveAclRoleRoom) == _AUTH_GENERAL) {
            $edit_index['acl_room_auth_general'] = "false";
            $edit_index['acl_room_auth_guest'] = "false";
            $edit_index['acl_room_auth_logout'] = "false";
        } else if(intval($exclusiveAclRoleRoom) == _AUTH_MODERATE) {
            $edit_index['acl_room_auth_moderate'] = "false";
            $edit_index['acl_room_auth_general'] = "false";
            $edit_index['acl_room_auth_guest'] = "false";
            $edit_index['acl_room_auth_logout'] = "false";
        }

        return true;
    }

    /**
     * get edit index data
     * 編集中のインデックス情報を取得する
     *
     * @param int $id index ID 編集中のインデックスID
     * @return array index info インデックス情報
     *                array[0]["index_id"|"index_name"|"index_name_english"|...]
     */
    function getIndexEditData($id){
        // 編集に必要な情報
        // index_id、名前(日/英)、公開/非公開、公開日、投稿権限あり、投稿権限なし、下にアイテム/インデックスがあるかないか
        $query = "SELECT * FROM ". DATABASE_PREFIX ."repository_index ".
                 "WHERE index_id = ". $id ." AND ".
                 "is_delete = 0; ";
        $result = $this->Db->execute($query);
        if($result === false || count($result)!=1) {
            return "";
        }
        if(count($result)==1){
            $edit_index = $result[0];
            $edit_index["old_index_id"] = $result[0]["index_id"];
            if($edit_index["public_state"] == "1"){
                // set pub dtate for html 
                $edit_index["public_state"] = "true";
                // set pub date 
                // Bugfix input scrutiny 2011/06/17 --start--
                $pos = strpos($edit_index["pub_date"], " ");
                if(!is_numeric($pos)){
                    $pos = strlen($edit_index["pub_date"]);
                }
                // Bugfix input scrutiny 2011/06/17 --end--
                $edit_index["pub_date"] = substr($edit_index["pub_date"],0,$pos);
                $date = explode("-", $edit_index["pub_date"]);
                $edit_index["pub_year"] = $date[0];
                $edit_index["pub_month"] = $date[1];
                $edit_index["pub_day"] = $date[2];
            } else {
                // set pub dtate for html
                $edit_index["public_state"] = "false";
                // set pub date to now date
                $DATE = new Date();
                $edit_index["pub_year"] = $DATE->getYear();
                $edit_index["pub_month"] = sprintf("%02d",$DATE->getMonth());
                $edit_index["pub_day"] = sprintf("%02d",$DATE->getDay());
            }
            // Add index thumbnail 2010/08/11 Y.Nakao --start--
            if(strlen($edit_index["thumbnail"]) > 0){
                $edit_index["thumbnail"] = "true";
            } else {
                $edit_index["thumbnail"] = "false";
            }
            $edit_index["thumbnail_name"] = "";
            $edit_index["thumbnail_mime_type"] = "";
            // Add index thumbnail 2010/08/11 Y.Nakao --start--
            $this->getAccessGroupData($edit_index["access_group"], $edit_index["exclusive_acl_group"], $edit_index);
            $this->getAccessAuthData($edit_index["access_role"], $edit_index["exclusive_acl_role"], $edit_index);
            return $edit_index;
        }
    }

    /**
     *  index contents num for index_id is $pid sub index contents num for index_id is $id
     * index_idが$pidのインデックスコンテンツ数から、index_idが$idのインデックスコンテンツ数を引く
     *
     * @param int $pid parent index id 親インデックスID
     * @param int $id index id インデックスID
     * @return bool true/false success/failed 成功/失敗
     */
    function subIndexContents($pid, $id){
        // 親インデックスをチェック check parent index id
        if($pid == 0){
            // 親インデックスがルートの場合、計算しない when $pid is root, do not calculation
            return true;
        }
        // 引くコンテンツ数を取得する get contents num
        $parent_index = $this->getIndexEditData($pid);
        if($parent_index === false){
            $error = $this->Db->ErrorMsg();
            return false;
        }
        $index = $this->getIndexEditData($id);
        if($index === false){
            $error = $this->Db->ErrorMsg();
            return false;
        }
        // コンテンツ数を減算 sub coontents num
        $query = "UPDATE ". DATABASE_PREFIX ."repository_index ".
                 "SET contents = ?, ".
                 "private_contents = ?, ".
                 "mod_user_id = ?, ".
                 "mod_date = ?, ".
                 "is_delete = ? ".
                 "WHERE index_id = ?; ";
        $params = array();
        $params[] = $parent_index["contents"] - $index["contents"];
        $params[] = $parent_index["private_contents"] - $index["private_contents"];
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->TransStartDate;
        $params[] = 0;
        $params[] = $pid;
        $result = $this->Db->execute($query, $params);
        // error check
        if($result === false) {
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        // 親インデックスにも実行 run at parent index 
        $result = $this->subIndexContents($parent_index["parent_index_id"], $id);
        return $result;
    }
    
    /**
     *  index contents num for index_id is $pid add index contents num for index_id is $id
     * index_idが$pidのインデックスコンテンツ数から、index_idが$idのインデックスコンテンツ数を足す
     *
     * @param int $pid parent index id 親インデックスID
     * @param int $id index id インデックスID
     * @return bool true/false success/failed 成功/失敗
     */
    function addIndexContents($pid, $id){
        // 親インデックスをチェック check parent index id
        if($pid == 0){
            // 親インデックスがルートの場合、計算しない when $pid is root, do not calculation
            return true;
        }
        // 加えるコンテンツ数を取得する get contents num
        $parent_index = $this->getIndexEditData($pid);
        if($parent_index === false){
            $error = $this->Db->ErrorMsg();
            return false;
        }
        $index = $this->getIndexEditData($id);
        if($index === false){
            $error = $this->Db->ErrorMsg();
            return false;
        }
        // コンテンツ数を加算 add coontents num
        $query = "UPDATE ". DATABASE_PREFIX ."repository_index ".
                 "SET contents = ?, ".
                 "private_contents = ?, ".
                 "mod_user_id = ?, ".
                 "mod_date = ?, ".
                 "is_delete = ? ".
                 "WHERE index_id = ?; ";
        $params = array();
        $params[] = $parent_index["contents"] + $index["contents"];
        $params[] = $parent_index["private_contents"] + $index["private_contents"];
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->TransStartDate;
        $params[] = 0;
        $params[] = $pid;
        $result = $this->Db->execute($query, $params);
        // error check
        if($result === false) {
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        // 親インデックスにも実行 run at parent index
        $result = $this->addIndexContents($parent_index["parent_index_id"], $id);
        return $result;
    }

    /**
     * If removed from the new information that belongs only to the child index of the index and private
     * インデックスおよび非公開の子インデックスにのみ所属している場合新着情報から削除
     *
     * @param int $index_id index ID インデックスID
     * @return bool true/false delete success/delete failed 削除成功/削除失敗
     */
    function deleteWhatsnewForIndex($index_id){
        // インデックスに所属するアイテムのitem_id, item_noを取得
        $query = "SELECT item_id, item_no ".
                 "FROM ".DATABASE_PREFIX."repository_position_index ".
                 "WHERE index_id = ? ".
                 "AND is_delete = 0;";
        $params = array();
        $params[] = $index_id;
        $items = $this->Db->execute($query, $params);
        if($items === false) {
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        
        // 各アイテムの所属を確認
        for($ii=0; $ii<count($items); $ii++){
            $no_delete_flg = false;
            
            $query = "SELECT ".DATABASE_PREFIX."repository_index.index_id, ".
                     "       ".DATABASE_PREFIX."repository_index.public_state ".
                     "FROM   ".DATABASE_PREFIX."repository_index, ".
                     "       ".DATABASE_PREFIX."repository_position_index ".
                     "WHERE  ".DATABASE_PREFIX."repository_position_index.item_id = ? ".
                     "AND    ".DATABASE_PREFIX."repository_position_index.item_no = ? ".
                     "AND    ".DATABASE_PREFIX."repository_position_index.is_delete = 0 ".
                     "AND    ".DATABASE_PREFIX."repository_position_index.index_id = ".DATABASE_PREFIX."repository_index.index_id ".
                     "AND    ".DATABASE_PREFIX."repository_index.is_delete = 0;";
            $params = array();
            $params[] = $items[$ii]['item_id'];
            $params[] = $items[$ii]['item_no'];
            $result = $this->Db->execute($query, $params);
            if($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                return false;
            }
            
            for($jj=0; $jj<count($result); $jj++){
                if($result[$jj]['index_id'] != $index_id){
                    // 編集中のインデックス以外に所属しているインデックスの公開状況を確認
                    if($result[$jj]['public_state'] == 1){
                        // 公開中である場合、その親が非公開でないかチェック
                        if($this->checkParentPublicState($result[$jj]['index_id']) == true){
                            // 親に非公開がない場合
                            $no_delete_flg = true;
                            break;
                        }
                    }
                }
            }
            
            // 非公開インデックスおよび非公開の子インデックスにのみ所属している場合新着情報から削除
            if($no_delete_flg == false){
                $this->deleteWhatsnew($items[$ii]['item_id']);
            }
        }
        
        // 子インデックスのindex_idを取得
        $query = "SELECT index_id ".
                 "FROM ".DATABASE_PREFIX."repository_index ".
                 "WHERE parent_index_id = ? ".
                 "AND is_delete = 0;";
        $params = array();
        $params[] = $index_id;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        
        for($ii=0; $ii<count($result); $ii++){
            // 子インデックスおよび非公開の子インデックスにのみ所属している場合新着情報から削除する
            $this->deleteWhatsnewForIndex($result[$ii]['index_id']);
        }
    }

    /**
     * It recalculates the number of contents of the specified index or later, to update
     * 指定インデックス以下のコンテンツ数を再計算し、更新する
     *
     * @param  int $index_id index ID
     * @return int $contents contents count インデックス以下のコンテンツ数
     */
    function recountContents($index_id=0){
        $contents = 0;
        if($index_id != 0) {    // ルートインデックス以外
            // インデックス公開フラグ
            $index_public_flag = $this->getIndexState($index_id);
            if($index_public_flag === false) {
                return false;
            }
            
            if($index_public_flag == "public"){
                // インデックス直下のコンテンツ数取得
                $query = "SELECT ".DATABASE_PREFIX."repository_position_index.item_id, ".
                         "       ".DATABASE_PREFIX."repository_position_index.item_no, ".
                         "       ".DATABASE_PREFIX."repository_position_index.index_id ".
                         "FROM   ".DATABASE_PREFIX."repository_position_index, ".
                         "       ".DATABASE_PREFIX."repository_item ".
                         "WHERE  ".DATABASE_PREFIX."repository_position_index.index_id = ? ".
                         "AND    ".DATABASE_PREFIX."repository_position_index.is_delete = 0 ".
                         "AND    ".DATABASE_PREFIX."repository_position_index.item_id = ".DATABASE_PREFIX."repository_item.item_id ".
                         "AND    ".DATABASE_PREFIX."repository_position_index.item_no = ".DATABASE_PREFIX."repository_item.item_no ".
                         "AND    ".DATABASE_PREFIX."repository_item.shown_status = 1 ".
                         "AND    ".DATABASE_PREFIX."repository_item.is_delete = 0; ";
                $params = array();
                $params[] = $index_id;
                $result = $this->Db->execute($query, $params);
                if($result === false) {
                    $errMsg = $this->Db->ErrorMsg();
                    return false;
                }
                
                for($ii=0; $ii<count($result); $ii++){
                    $contents++;
                }
            }
        }
        
        // 子インデックスのindex_id, public_stateを取得
        $query = "SELECT index_id, public_state ".
                 "FROM ".DATABASE_PREFIX."repository_index ".
                 "WHERE parent_index_id = ? ".
                 "AND is_delete = 0;";
        $params = array();
        $params[] = $index_id;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        
        for($ii=0; $ii<count($result); $ii++){
            // 子インデックスが公開中である場合
            if($result[$ii]["public_state"] == "1"){
                // 子インデックスのアイテム数を取得
                $contents += $this->recountContents($result[$ii]["index_id"]);
            }
        }
        
        // update contents
        $query = "UPDATE ". DATABASE_PREFIX ."repository_index ".
                 "SET contents = ? ".
                 "WHERE index_id = ? ";
                 "AND is_delete = 0; ";
        $params = array();
        $params[] = $contents;
        $params[] = $index_id;
        //execute
        $result = $this->Db->execute($query,$params);
        if($result == false){
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        
        return $contents;
    }
    
    /**
     * Recalculates the number of private content specified index or later, to update
     * 指定インデックス以下の非公開コンテンツ数を再計算し、更新する
     *
     * @param  int $index_id index ID インデックスID
     * @return int $contents contents count インデックス以下のコンテンツ数
     */
    function recountPrivateContents($index_id=0){
        $contents = 0;
        if($index_id != 0) {    // ルートインデックス以外
            // インデックス公開フラグ
            $index_public_flag = $this->getIndexState($index_id);
            if($index_public_flag === false) {
                return false;
            }
            
            if($index_public_flag == "public"){
                // インデックスが公開状態の場合、非公開設定のアイテムのみ取得する
                $query = "SELECT ".DATABASE_PREFIX."repository_position_index.item_id, ".
                         "       ".DATABASE_PREFIX."repository_position_index.item_no, ".
                         "       ".DATABASE_PREFIX."repository_position_index.index_id ".
                         "FROM   ".DATABASE_PREFIX."repository_position_index, ".
                         "       ".DATABASE_PREFIX."repository_item ".
                         "WHERE  ".DATABASE_PREFIX."repository_position_index.index_id = ? ".
                         "AND    ".DATABASE_PREFIX."repository_position_index.is_delete = 0 ".
                         "AND    ".DATABASE_PREFIX."repository_position_index.item_id = ".DATABASE_PREFIX."repository_item.item_id ".
                         "AND    ".DATABASE_PREFIX."repository_position_index.item_no = ".DATABASE_PREFIX."repository_item.item_no ".
                         "AND    ".DATABASE_PREFIX."repository_item.shown_status = 0 ".
                         "AND    ".DATABASE_PREFIX."repository_item.is_delete = 0; ";
                $params = array();
                $params[] = $index_id;
                $result = $this->Db->execute($query, $params);
                if($result === false) {
                    $errMsg = $this->Db->ErrorMsg();
                    return false;
                }
                
                for($ii=0; $ii<count($result); $ii++){
                    $contents++;
                }
            } else {
                // インデックスが非公開状態の場合、全てのアイテムを取得する
                $query = "SELECT ".DATABASE_PREFIX."repository_position_index.item_id, ".
                         "       ".DATABASE_PREFIX."repository_position_index.item_no, ".
                         "       ".DATABASE_PREFIX."repository_position_index.index_id ".
                         "FROM   ".DATABASE_PREFIX."repository_position_index, ".
                         "       ".DATABASE_PREFIX."repository_item ".
                         "WHERE  ".DATABASE_PREFIX."repository_position_index.index_id = ? ".
                         "AND    ".DATABASE_PREFIX."repository_position_index.is_delete = 0 ".
                         "AND    ".DATABASE_PREFIX."repository_position_index.item_id = ".DATABASE_PREFIX."repository_item.item_id ".
                         "AND    ".DATABASE_PREFIX."repository_position_index.item_no = ".DATABASE_PREFIX."repository_item.item_no ".
                         "AND    ".DATABASE_PREFIX."repository_item.is_delete = 0; ";
                $params = array();
                $params[] = $index_id;
                $result = $this->Db->execute($query, $params);
                if($result === false) {
                    $errMsg = $this->Db->ErrorMsg();
                    return false;
                }
                for($ii=0; $ii<count($result); $ii++){
                    $contents++;
                }               
            }
        }
        
        // 子インデックスのindex_idを取得
        $query = "SELECT index_id ".
                 "FROM ".DATABASE_PREFIX."repository_index ".
                 "WHERE parent_index_id = ? ".
                 "AND is_delete = 0;";
        $params = array();
        $params[] = $index_id;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        
        for($ii=0; $ii<count($result); $ii++){
            // 子インデックスのアイテム数を取得
            $contents += $this->recountPrivateContents($result[$ii]["index_id"]);
        }
        
        // update contents
        $query = "UPDATE ". DATABASE_PREFIX ."repository_index ".
                 "SET private_contents = ? ".
                 "WHERE index_id = ? ";
                 "AND is_delete = 0; ";
        $params = array();
        $params[] = $contents;
        $params[] = $index_id;
        //execute
        $result = $this->Db->execute($query,$params);
        if($result == false){
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        
        return $contents;
    }
    
    /**
     * To reset the number of contents of the specified index or later
     * 指定インデックス以下のコンテンツ数をリセットする
     *
     * @param  int $index_id index ID インデックスID
     * @return bool true/false update success/update failed 更新成功/更新失敗
     */
    function resetContents($index_id=0){
        // update contents
        $query = "UPDATE ". DATABASE_PREFIX ."repository_index ".
                 "SET contents = 0 ".
                 "WHERE index_id = ? ";
                 "AND is_delete = 0; ";
        $params = array();
        $params[] = $index_id;
        //execute
        $result = $this->Db->execute($query,$params);
        if($result == false){
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        
        // 子インデックスのindex_idを取得
        $query = "SELECT index_id ".
                 "FROM ".DATABASE_PREFIX."repository_index ".
                 "WHERE parent_index_id = ? ".
                 "AND is_delete = 0;";
        $params = array();
        $params[] = $index_id;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $errMsg = $this->Db->ErrorMsg();
            return false;
        }
        
        for($ii=0; $ii<count($result); $ii++){
            // 子インデックスのコンテンツ数をリセットする
            $this->resetContents($result[$ii]["index_id"]);
        }
        
        return true;
    }

    /**
     * check whether input value is blank only or not
     * 空白文字を削除する
     *
     * @param string $name index list name インデックス名
     * @return string $name index list name 空白文字除去済みインデックス名
     */
    function checkBlank($name) {
        $check_name = str_replace(" ","", $name);
        $check_name = str_replace("　","",$check_name);
        if(strlen($check_name) == 0) {
            $name = "";
        }
        return $name;
    }

    /**
     * set default
     * 初期値を設定する
     */
    public function setDefaultAccessControlList()
    {
        // setting defaultAccessRole_
        $query = "SELECT `".RepositoryConst::DBCOL_AUTHORITIES_ROLE_AUTHORITY_ID."` ".
                " FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_AUTHORITIES.
                " ORDER BY `".RepositoryConst::DBCOL_AUTHORITIES_ROLE_AUTHORITY_ID."` ASC;";
        $result = $this->Db->execute($query);
        if($result===false || count($result) == 0) {
            return;
        }
        for($ii=0;$ii<count($result); $ii++) {
            if($result[$ii][RepositoryConst::DBCOL_AUTHORITIES_ROLE_AUTHORITY_ID] == _ROLE_AUTH_GUEST) {
                continue;
            }
            if(strlen($this->defaultAccessRoleIds_) > 0) {
                $this->defaultAccessRoleIds_ .= ',';
            }
            $this->defaultAccessRoleIds_ .= $result[$ii][RepositoryConst::DBCOL_AUTHORITIES_ROLE_AUTHORITY_ID];
        }
    }

    /**
     * To check whether the viewing rights has been changed
     * 閲覧権限が変更されたかチェックする
     *
     * @param int $indexId             index ID                   インデックスID
     * @param int $exclusiveRoleId     exclusive access role ID   除外閲覧権限ID
     * @param int $exclusiveRoomAuth   exclusive access room auth 除外ルーム権限
     * @param string $exclusiveGroupId exclusive access group     除外グループID
     * @return bool true/false changed/not changed 変更された/変更されていない
     */
    private function checkChangeBrowsingAuthority($indexId, $exclusiveRoleId, $exclusiveRoomAuth, $exclusiveGroupId)
    {
        // 閲覧除外権限情報の取得
        $query = "SELECT exclusive_acl_role, exclusive_acl_group ".
                 "FROM ".DATABASE_PREFIX. "repository_index ".
                 "WHERE index_id = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $indexId;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            // 何か起きたら処理しない
            return false;
        }
        
        // ベース権限とルーム権限に分ける
        $roles = explode("|", $result[0]["exclusive_acl_role"]);
        $exclusive_acl_role_id = $roles[0];
        $exclusive_acl_room_auth = $roles[1];
        
        // 画面上で設定した値とDBの値が違う場合はtrueを返す
        // ベース権限の変更チェック
        if($exclusive_acl_role_id != $exclusiveRoleId) {
            return true;
        }
        // ルーム権限の変更チェック
        if($exclusive_acl_room_auth != $exclusiveRoomAuth) {
            return true;
        }
        
        // 画面上で設定した除外グループ権限
        $exclusiveGroupIds = explode(",", $exclusiveGroupId);
        // DBに保存された除外グループ権限
        $exclusive_acl_group_id = explode(",", $result[0]["exclusive_acl_group"]);
        // 除外グループ数が違う場合は変更があったという事なのでtrueを返す
        if(count($exclusive_acl_group_id) != count($exclusiveGroupIds)) {
            return true;
        } else {
            for($ii = 0; $ii < count($exclusive_acl_group_id); $ii++) {
                // DBの値と除外グループ配列の値をチェックして、一致しない値がある場合はtrueを返す
                if(!in_array($exclusive_acl_group_id[$ii], $exclusiveGroupIds)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check index public state
     * 指定インデックスの公開状況をチェックする
     *
     * @param  int $index_id index ID インデックスID
     * @return string "public"/"private" "公開"/"非公開"
     * @throws AppException
     */
    private function getIndexState($index_id) {
        // インデックス自身の公開状況取得
        $businessIndexManager = BusinessFactory::getFactory()->getBusiness("businessIndexmanager");
        $result = $businessIndexManager->checkIndexState($index_id);
        if($result === false) {
            throw new AppException("Get index state failed.");
        }

        return $result;
    }

    /**
     * Get edit Index parameter
     * 編集中のインデックス情報をパラメータから取得する
     *
     * @param string $owner_user_id owner user ID 所有者ユーザーID
     * @return array edit index data 編集中のインデックス情報
     *                array["index_id"|"index_name"|index_name_english"|...]
     */
    private function getEditIndexParameter($owner_user_id) {
        $edit_index_data = array();
        $edit_index_data["index_id"] = $this->edit_id;
        $edit_index_data["index_name"] = $this->name_jp;
        $edit_index_data["index_name_english"] = $this->name_en;
        $edit_index_data["parent_index_id"] = $this->pid;
        $edit_index_data["show_order"] = $this->show_order;
        $edit_index_data["mod_date"] = $this->mod_date;
        if ($this->pub_chk == "true") {
            $edit_index_data["public_state"] = "1";
        } else {
            $edit_index_data["public_state"] = "0";
        }
        $edit_index_data["pub_year"] = $this->pub_year;
        $edit_index_data["pub_month"] = $this->pub_month;
        $edit_index_data["pub_day"] = $this->pub_day;
        $edit_index_data["pub_date"] = sprintf("%d-%02d-%02d 00:00:00.000", $this->pub_year, $this->pub_month, $this->pub_day);
        if(!$this->checkDate($this->pub_year, $this->pub_month, $this->pub_day)){
            $DATE = new Date();
            $now_date = explode(" ", $DATE->getDate(), 2);
            $edit_index_data["pub_year"] = $DATE->getYear();
            $edit_index_data["pub_month"] = sprintf("%02d",$DATE->getMonth());
            $edit_index_data["pub_day"] = sprintf("%02d",$DATE->getDay());
            $edit_index_data["pub_date"] = $now_date[0].' 00:00:00.000';
        }
        $edit_index_data["comment"] = $this->comment;
        if($this->display_more == "true"){
            $edit_index_data["display_more"] = intval($this->display_more_num);
        } else {
            $edit_index_data["display_more"] = "";
        }
        if($this->rss_display == "true"){
            $edit_index_data["rss_display"] = "1";
        } else {
            $edit_index_data["rss_display"] = "0";
        }
        $edit_index_data["display_type"] = intval($this->display_type);
        $edit_index_data["select_index_list_display"] = intval($this->select_index_list_display);
        $edit_index_data["select_index_list_name"] = $this->checkBlank($this->select_index_list_name);
        $edit_index_data["select_index_list_name_english"] = $this->checkBlank($this->select_index_list_name_english);
        $edit_index_data["access_group_id"] = $this->access_group_ids;
        $edit_index_data["not_access_group_id"] = $this->not_access_group_ids;
        $edit_index_data["access_role_id"] = $this->access_role_ids;
        $edit_index_data["not_access_role_id"] = $this->not_access_role_ids;
        $edit_index_data["access_role_room"] = $this->access_role_room;
        $edit_index_data["access_role"] = $edit_index_data["access_role_id"] . "|" . $edit_index_data["access_role_room"];
        $edit_index_data["thumbnail_del"] = $this->thumbnail_del;

        // set exclusive_acl_role in repository_index
        $edit_index_data["exclusive_acl_role_id"] = $this->exclusiveAclRoleIds;
        $edit_index_data["exclusive_acl_room_auth"] = $this->exclusiveAclRoomAuth;

        // set exclusive_acl_group in repository_index
        $edit_index_data["exclusive_acl_group_id"] = $this->exclusiveAclGroupIds;

        // 閲覧権限に変更があった場合フラグをtrueにする
        $this->changeBrowsingAuthorityFlag = $this->checkChangeBrowsingAuthority($edit_index_data["index_id"],
            $edit_index_data["exclusive_acl_role_id"],
            $edit_index_data["exclusive_acl_room_auth"],
            $edit_index_data["exclusive_acl_group_id"]);
        if($this->create_cover_flag == "true"){
            $edit_index_data["create_cover_flag"] = "1";
        } else {
            $edit_index_data["create_cover_flag"] = "0";
        }

        $edit_index_data["owner_user_id"] = $owner_user_id;
        if($this->harvest_public_state == "true"){
            $edit_index_data["harvest_public_state"] = "1";
        } else {
            $edit_index_data["harvest_public_state"] = "0";
        }
        $edit_index_data["biblio_flag"] = $this->biblio_flag;
        $edit_index_data["online_issn"] = $this->online_issn;

        return $edit_index_data;
    }

    /**
     * Update index additional data
     * インデックス付属情報を更新する
     *
     * @throws AppException
     */
    private function updateAdditionalData() {
        $indexAddtionalDataManager = BusinessFactory::getFactory()->getBusiness("businessIndexadditionaldatamanager");
        
        if($this->journalInfo == 1) {
            // 「出力する」の場合はデータを更新する
            // 雑誌情報の入力チェック
            $tmpErrorMsg = "";
            $result = $this->checkAdditionalDataInput($tmpErrorMsg);
            if($result === false) {
                $this->warnLog("Invalid format Journal input.", __FILE__, __CLASS__, __LINE__);
                $this->Session->setParameter("tree_error_msg", $tmpErrorMsg);
                throw new AppException("Invalid format Journal input.");
            }
            // 雑誌情報の更新
            $result = $indexAddtionalDataManager->upsertIndexAdditionalData($this->edit_id,
                                                                            $this->journalTypeId,
                                                                            $this->journalInfo,
                                                                            $this->journalAttrId,
                                                                            $this->journalAttrValue);
        } else {
            // 登録済雑誌情報の存在チェック
            $result = $indexAddtionalDataManager->getAdditionalDataIdByIndexId($this->edit_id);
            
            // 登録済データが「出力しない」に変更された場合は出力フラグのみを変更する
            // 登録済データが無く、「出力しない」の場合は何もしない
            if(count($result) > 0) {
                $indexAddtionalDataManager->updateOutputFlag($this->edit_id, $result[0]["additionaldata_id"], $this->journalTypeId, $this->journalInfo);
            }
        }
    }

    /**
     * Check index additionaldata input format
     * インデックス付属情報の入力フォーマットを精査する
     *
     * @param $ErrorMsg error message エラーメッセージ
     * @return bool true/false no problem/problem 問題無し/あり
     */
    private function checkAdditionalDataInput(&$ErrorMsg) {
        // TODO:DBの要素を使用して各チェックを行うように今後修正
        $additionalDataTypeManager = BusinessFactory::getFactory()->getBusiness("businessAdditionaldatatypemanager");
        
        // 表示言語取得
        $lang = $this->Session->getParameter("_lang");
        
        // 必須入力チェック
        for($ii = 0; $ii < count($this->journalAttrId); $ii++) {
            if($this->journalAttrRequired[$ii] == 1 && strlen($this->journalAttrValue[$ii]) == 0) {
                $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[$ii], $lang);
                $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_required_error"), $tmpAttrType[0]["attribute_name"]);
                return false;
            }
        }
        
        // 入力フォーマットチェック
        // TODO:暫定対応のため、今後の改修ではDBとBusinessを使うようにする
        // プリント版ISSN/プリント版ISBN : ISSN or ISBN-10 or ISBN-13
        if(strlen($this->journalAttrValue[1]) > 0 &&
           !(
             (preg_match("/^\d{4}-?\d{3}[0-9X]$/", $this->journalAttrValue[1])) || // ISSN(ハイフン有無問わず)
             (mb_strlen($this->journalAttrValue[1]) == 10 && preg_match("/^\d{9}[0-9X]$/", $this->journalAttrValue[1])) || // ISBN-10(ハイフン無)
             (mb_strlen($this->journalAttrValue[1]) == 13 && preg_match("/^\d+-\d+-\d+-[0-9X]$/", $this->journalAttrValue[1])) || // ISBN-10(ハイフン有)
             (mb_strlen($this->journalAttrValue[1]) == 13 && preg_match("/^97[8-9]\d{9}[0-9X]$/", $this->journalAttrValue[1])) || // ISBN-13(ハイフン無)
             (mb_strlen($this->journalAttrValue[1]) == 17 && preg_match("/^97[8-9]-\d+-\d+-\d+-[0-9X]$/", $this->journalAttrValue[1]))  // ISBN-13(ハイフン有)
            )
          )
        {
            $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[1], $lang);
            $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
            return false;
        }
        // eISSN/eISBN : ISSN or ISBN-10 or ISBN-13
        if(strlen($this->journalAttrValue[2]) > 0 &&
           !(
             (preg_match("/^\d{4}-?\d{3}[0-9X]$/", $this->journalAttrValue[2])) ||
             (mb_strlen($this->journalAttrValue[2]) == 10 && preg_match("/^\d{9}[0-9X]$/", $this->journalAttrValue[2])) || // ISBN-10(ハイフン無)
             (mb_strlen($this->journalAttrValue[2]) == 13 && preg_match("/^\d+-\d+-\d+-[0-9X]$/", $this->journalAttrValue[2])) || // ISBN-10(ハイフン有)
             (mb_strlen($this->journalAttrValue[2]) == 13 && preg_match("/^97[8-9]\d{9}[0-9X]$/", $this->journalAttrValue[2])) || // ISBN-13(ハイフン無)
             (mb_strlen($this->journalAttrValue[2]) == 17 && preg_match("/^97[8-9]-\d+-\d+-\d+-[0-9X]$/", $this->journalAttrValue[2])) // ISBN-13(ハイフン有)
            )
        )
        {
            $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[2], $lang);
            $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
            return false;
            
        }
        // 最古オンライン巻号の出版年月日 : YYYY-MM-DD, YYYY-MM, YYYY
        if(strlen($this->journalAttrValue[3]) > 0) {
            if(!(preg_match("/^\d{4}-\d{2}-\d{2}$/", $this->journalAttrValue[3]) || // YYYY-MM-DD
                 preg_match("/^\d{4}-\d{2}$/", $this->journalAttrValue[3]) || // YYYY-MM
                 preg_match("/^\d{4}$/", $this->journalAttrValue[3]) // YYYY
                )
              ) {
                $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[3], $lang);
                $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
                return false;
            }

            // 年月日で分割
            $tmpDate = explode("-", $this->journalAttrValue[3]);

            // 年範囲のチェック(1700年～2030年のみ)
            if($tmpDate[0] < 1700 || $tmpDate[0] > 2030) {
                $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[3], $lang);
                $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
                return false;
            }

            // 日付の妥当性チェック
            if(preg_match("/^\d{4}-\d{2}-\d{2}$/", $this->journalAttrValue[3]) && !checkdate($tmpDate[1], $tmpDate[2], $tmpDate[0])) {
                $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[3], $lang);
                $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
                return false;
            }

        }

        // 最新オンライン巻号の出版年月日 : YYYY-MM-DD, YYYY-MM, YYYY
        if(strlen($this->journalAttrValue[6]) > 0) {
            if(!(preg_match("/^\d{4}-\d{2}-\d{2}$/", $this->journalAttrValue[6]) || // YYYY-MM-DD
                preg_match("/^\d{4}-\d{2}$/", $this->journalAttrValue[6]) || // YYYY-MM
                preg_match("/^\d{4}$/", $this->journalAttrValue[6]) // YYYY
            )
            ) {
                $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[6], $lang);
                $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);

                return false;
            }

            // 年月日で分割
            $tmpDate = explode("-", $this->journalAttrValue[6]);

            // 年範囲のチェック(1700年～2030年のみ)
            if($tmpDate[0] < 1700 || $tmpDate[0] > 2030) {
                $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[6], $lang);
                $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
                return false;
            }

            // 日付の妥当性チェック
            if(preg_match("/^\d{4}-\d{2}-\d{2}$/", $this->journalAttrValue[6]) && !checkdate($tmpDate[1], $tmpDate[2], $tmpDate[0])) {
                $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[6], $lang);
                $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
                return false;
            }
        }
        // シリーズのタイトルID
        // 1以上の整数
        if(strlen($this->journalAttrValue[14]) > 0 && !preg_match("/^[1-9]\d*$/", $this->journalAttrValue[14])) {
            $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[14], $lang);
            $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
            return false;
        }

        // 変遷前誌のタイトルID
        // 1以上の整数
        if(strlen($this->journalAttrValue[15]) > 0 && !preg_match("/^[1-9]\d*$/", $this->journalAttrValue[15])) {
            $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[15], $lang);
            $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
            return false;
        }

        // NCID
        if(strlen($this->journalAttrValue[20]) > 0 && !preg_match("/^[AB][ABN][0-9]{7}[0-9X]$/", $this->journalAttrValue[20])) {
            $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[20], $lang);
            $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
            return false;
        }

        // NDL請求記号
        if(strlen($this->journalAttrValue[21]) > 0 &&
            !(strlen($this->journalAttrValue[21]) <= 20 && strlen($this->journalAttrValue[21]) == mb_strlen($this->journalAttrValue[21]))
        ) {
            $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[21], $lang);
            $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
            return false;
        }

        // J-STAGE資料コード（雑誌名の略料）
        if(strlen($this->journalAttrValue[22]) > 0 &&
            !(strlen($this->journalAttrValue[22]) <= 20 && strlen($this->journalAttrValue[22]) == mb_strlen($this->journalAttrValue[22]))
        ) {
            $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[22], $lang);
            $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
            return false;
        }

        // 医中誌コード
        if(strlen($this->journalAttrValue[23]) > 0 &&
            !preg_match("/^J[0-9]{5}$/", $this->journalAttrValue[23])
        ) {
            $tmpAttrType = $additionalDataTypeManager->getAttrTypeByAttrId($this->journalTypeId, $this->journalAttrId[23], $lang);
            $ErrorMsg = sprintf($this->smartyAssign->getLang("repository_input_edittree_format_error"), $tmpAttrType[0]["attribute_name"]);
            return false;
        }
        
        return true;
    }
}

?>
