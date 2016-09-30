<?php

/**
 * Action class for private tree editing
 * プライベートツリー編集用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Tree.class.php 20897 2013-01-11 07:26:13Z ayumi_jin $
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
 * JSON library
 * JSON用ライブラリ
 */
require_once WEBAPP_DIR. '/modules/repository/components/JSON.php';
/**
 * Common class file download
 * ファイルダウンロード共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';
/**
 * Action class for the index operation
 * インデックス操作用アクションクラス
 */
require_once WEBAPP_DIR. '/modules/repository/action/edit/tree/Tree.class.php';

/**
 * Action class for private tree editing
 * プライベートツリー編集用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Main_Privatetree extends RepositoryAction
{

    // component
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    public $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
    public $Db = null;
    /**
     * Data upload objects
     * データアップロードオブジェクト
     *
     * @var Uploads_View
     */
    public $uploadsView = null;
    
    // request parameter
    /**
     * Index id
     * インデックスID
     *
     * @var int
     */
    public $edit_id = null;         // click index ID
    /**
     * Index edits
     * インデックス編集内容
     *
     * @var string
     */
    public $edit_mode = null;           // edit mode
                                    // '' : select edit index
                                    // 'insert' : make new index
                                    // 'update' : edit index
                                    // 'delete' : delete index
                                    // 'sort' : sort index
    // request parameter for now edit index data
    /**
     * Index Name
     * インデックス名
     *
     * @var string
     */
    public $name_jp = null;             // now edit index japanese name
    /**
     * Index name of English
     * 英語のインデックス名
     *
     * @var string
     */
    public $name_en = null;             // now edit index english name
    /**
     * Index Comments
     * インデックスコメント
     *
     * @var string
     */
    public $comment = null;             // now edit index comment
    /**
     * Index ID of the parent index
     * 親インデックスのインデックスID
     *
     * @var int
     */
    public $pid = null;                 // now edit index parent_index_id
    /**
     * Order of display
     * 表示順序
     *
     * @var int
     */
    public $show_order= null;               // now edit index show order
    /**
     * Public status( "true": the public, "false": private)
     * 公開状況("true":公開、"false":非公開)
     *
     * @var string
     */
    public $pub_chk = null;             // now edit index pub flg
    /**
     * Publication year
     * 公開年
     *
     * @var int
     */
    public $pub_year = null;                // now edit index pub year
    /**
     * Public month
     * 公開月
     *
     * @var int
     */
    public $pub_month = null;               // now edit index pub month
    /**
     * Public day
     * 公開日
     *
     * @var int
     */
    public $pub_day = null;             // now edit index pub day 
    /**
     * ID of the group that can be item registration
     * アイテム登録が可能なグループのID
     *
     * @var string
     */
    public $access_group_ids = null;        // now edit index entry item group id
    /**
     * ID of the group that can not be item registration
     * アイテム登録が不可能なグループのID
     *
     * @var string
     */
    public $not_access_group_ids = null;    // now edit index not entry item group id
    /**
     * Base sufficient authority to item registration
     * アイテム登録が可能なベース権限
     *
     * @var string
     */
    public $access_role_ids = null;     // now edit index entry item auth id
    /**
     * Based authority that can not be item registration
     * アイテム登録が不可能なベース権限
     *
     * @var string
     */
    public $not_access_role_ids = null; // now edit index not entry item auth id
    /**
     * Date Modified
     * 更新日時
     *
     * @var string
     */
    public $mod_date = null;                // now edit index mod date
    /**
     * Drag the index ID
     * ドラッグしたインデックスID
     *
     * @var int
     */
    public $drag_id = null;             // drag index id at drag ivent
    /**
     * Drop destination index ID
     * ドロップ先インデックスID
     *
     * @var int
     */
    public $drop_id = null;             // drop index id at drop ivent
    /**
     * Drop destination of the index location
     * ドロップ先のインデックス場所
     *
     * @var string
     */
    public $drop_index = null;              // true  : index drop in index
                                        // false : index drop in sentry
    // Add child index display more 2009/01/16 Y.Nakao --start--
    /**
     * Display range setting( "true": ON, "false": OFF)
     * 表示範囲設定("true":ON、"false":OFF)
     *
     * @var string
     */
    public $display_more = null;            // first display child index show all or a little
    /**
     * Display range index number
     * 表示範囲インデックス数
     *
     * @var int
     */
    public $display_more_num = null;        // first display child index num
    // Add child index display more 2009/01/16 Y.Nakao --end--
    
    /**
     * RSS icon display flag( "True": display, "false": non-display)
     * RSSアイコン表示フラグ("true":表示、"false":非表示)
     *
     * @var string
     */
    public $rss_display = null;         // RSS icon display
    
    // Add config management authority 2010/02/23 Y.Nakao --start--
    /**
     * Room sufficient authority to item registration
     * アイテム登録が可能なルーム権限
     *
     * @var string
     */
    public $access_role_room = null;        // now edit index access OK room authority
    // Add config management authority 2010/02/23 Y.Nakao --end--
    
    // Add contents page 2010/08/06 Y.Nakao --start--
    /**
     * Display format
     * 表示形式
     *
     * @var int
     */
    public $display_type = null;
    // Add contents page 2010/08/06 Y.Nakao --end--
    
    // Add index list 2011/4/5 S.Abe --start--
    /**
     * Index list set
     * インデックス一覧表示設定
     *
     * @var int
     */
    public $select_index_list_display = null;
    /**
     * Display name in the index list
     * インデックス一覧での表示名
     *
     * @var string
     */
    public $select_index_list_name = null;
    /**
     * English display name in the index list
     * インデックス一覧での英語表示名
     *
     * @var string
     */
    public $select_index_list_name_english = null;
    // Add index list 2011/4/5 S.Abe --end--

    /**
     * Language Resource Management object
     * 言語リソース管理オブジェクト
     *
     * @var Smarty
     */
    public $smartyAssign = null;

    // Add index thumbnail 2010/08/20 Y.Nakao --start--
    /**
     * Thumbnail Delete flag
     * サムネイル削除フラグ
     *
     * @var int
     */
    public $thumbnail_del = null;
    // Add index thumbnail 2010/08/20 Y.Nakao --end--
    
    // Add tree access control list 2012/02/22 T.Koyasu -start-
    /**
     * Based authority that can not be indexed view
     * インデックス閲覧が不可能なベース権限
     *
     * @var string
     */
    public $exclusiveAclRoleIds = null;
    /**
     * Room authority that can not be indexed view
     * インデックス閲覧が不可能なルーム権限
     *
     * @var string
     */
    public $exclusiveAclRoomAuth = null;
    /**
     * Group list that can not be indexed view
     * インデックス閲覧が不可能なグループ一覧
     *
     * @var string
     */
    public $exclusiveAclGroupIds = null;
    // Add tree access control list 2012/02/22 T.Koyasu -end-
        
    /**
     * Cover page creation flag( "true": ON, "false": OFF)
     * カバーページ作成フラグ("true":ON、"false":OFF)
     *
     * @var string
     */
    public $create_cover_flag = null;
    // Add harvest public flag 2013/07/05 K.Matsuo --start--
    /**
     * Public situation at the time of harvest( "true": the public, "false": private)
     * ハーベスト時の公開状況("true":公開、"false":非公開)
     *
     * @var string
     */
    public $harvest_public_state = null;
    // Add harvest public flag 2013/07/05 K.Matsuo --end--
    
    /**
     * Run private tree edit
     * プライベートツリー編集実行
     *
     * @return string Execution result 実行結果
     */
    function execute()
    {
        // Add specialized support for open.repo "Be published private tree" Y.Nakao 2013/06/21 --start--
        $this->validatorPrivateTree();
        // Add specialized support for open.repo "Be published private tree" Y.Nakao 2013/06/21 --end--
        
        $treeInstance = new Repository_Action_Edit_Tree();
        $treeInstance->Session = $this->Session;
        $treeInstance->Db = $this->Db;
        $treeInstance->edit_id = $this->edit_id;
        $treeInstance->edit_mode = $this->edit_mode;
        $treeInstance->name_jp = $this->name_jp;
        $treeInstance->name_en = $this->name_en;
        $treeInstance->comment = $this->comment;
        $treeInstance->pid = $this->pid;
        $treeInstance->show_order = $this->show_order;
        $treeInstance->pub_chk = $this->pub_chk;
        $treeInstance->pub_year = $this->pub_year;
        $treeInstance->pub_month = $this->pub_month;
        $treeInstance->pub_day = $this->pub_day;
        $treeInstance->access_group_ids = $this->access_group_ids;
        $treeInstance->not_access_group_ids = $this->not_access_group_ids;
        $treeInstance->access_role_ids = $this->access_role_ids;
        $treeInstance->not_access_role_ids = $this->not_access_role_ids;
        $treeInstance->mod_date = $this->mod_date;
        $treeInstance->drag_id = $this->drag_id;
        $treeInstance->drop_id = $this->drop_id;
        $treeInstance->drop_index = $this->drop_index;          // false : index drop in sentry
        $treeInstance->display_more = $this->display_more;
        $treeInstance->display_more_num = $this->display_more_num;
        $treeInstance->rss_display = $this->rss_display;
        $treeInstance->access_role_room = $this->access_role_room;
        $treeInstance->display_type = $this->display_type;
        $treeInstance->select_index_list_display = $this->select_index_list_display;
        $treeInstance->select_index_list_name = $this->select_index_list_name;
        $treeInstance->select_index_list_name_english = $this->select_index_list_name_english;  
        $treeInstance->smartyAssign = $this->smartyAssign;
        $treeInstance->thumbnail_del = $this->thumbnail_del;
        $treeInstance->exclusiveAclRoleIds = $this->exclusiveAclRoleIds;
        $treeInstance->exclusiveAclRoomAuth = $this->exclusiveAclRoomAuth;
        $treeInstance->exclusiveAclGroupIds = $this->exclusiveAclGroupIds;
        $treeInstance->create_cover_flag = $this->create_cover_flag;
        $treeInstance->harvest_public_state = $this->harvest_public_state;
        
        $result = $treeInstance->execute();
        $this->Session->setParameter("redirect_flg", "privatetree_update");
        return $result;
    }
    
    // Add specialized support for open.repo "Be published private tree" Y.Nakao 2013/06/21 --start--
    /**
     * If private tree was a public setting, to change the setting value according to it
     * プライベートツリーが公開設定だった場合、それに合わせ設定値を変更する
     */
    private function validatorPrivateTree()
    {
        // check published private tree status.
        if(_REPOSITORY_PRIVATETREE_PUBLIC)
        {
            // public_state is ON
            $this->pub_chk = "true";
            
            // set TransStartDate
            $this->initAction();
            
            // pub_date is past date
            $pubDate = $this->pub_year.$this->pub_month.$this->pub_day;
            $nowDate = substr($this->TransStartDate, 0, 10);
            $nowDate = str_replace("-", "", $nowDate);
            if($pubDate > $nowDate)
            {
                // when pub_date is past date, set now_date in pub_date.
                $this->pub_year  = substr($this->TransStartDate, 0, 4);
                $this->pub_month = substr($this->TransStartDate, 5, 2);
                $this->pub_day   = substr($this->TransStartDate, 7, 2);
            }
            
            // exit database transe
            $this->exitAction();
            $this->finalize();
        }
    }
    // Add specialized support for open.repo "Be published private tree" Y.Nakao 2013/06/21 --end--
}

?>
