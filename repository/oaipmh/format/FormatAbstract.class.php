<?php

/**
 * OAI-PMH item information output base class
 * OAI-PMHアイテム情報出力基底クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: FormatAbstract.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * String format conversion common classes
 * 文字列形式変換共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryOutputFilter.class.php';

/**
 * OAI-PMH item information output base class
 * OAI-PMHアイテム情報出力基底クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Oaipmh_FormatAbstract
{
    /**
     * Line feed code
     * 改行コード
     * 
     * @var string
     */
    const LF = "\n";
    
    /**
     * List display metadata only output
     * 一覧表示メタデータのみ出力
     * 
     * @var string
     */
    const DATA_FILTER_SIMPLE = "simple";
    /**
     * Output the meta-data of other than the non-display
     * 非表示以外のメタデータを出力
     * 
     * @var string
     */
    const DATA_FILTER_DETAIL = "detail";
    
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    protected $Session = null;
    
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
    protected $Db = null;
    /**
     * Database management wrapper objects
     * データベース管理ラッパーオブジェクト
     *
     * @var RepositoryDbAccess
     */
    protected $dbAccess = null;
    
    /**
     * Action objects for WEKO
     * WEKO用アクションオブジェクト
     *
     * @var RepositoryAction
     */
    protected $RepositoryAction = null;
    
    /**
     * Output form
     * 出力形式
     * 
     * @var string
     */
    protected $dataFilter = self::DATA_FILTER_DETAIL;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $sesssion Session management objects Session管理オブジェクト
     * @param DbObject $db Database management objects データベース管理オブジェクト
     */
    public function Repository_Oaipmh_FormatAbstract($session, $db)
    {
        if(isset($session) && $session!=null)
        {
            $this->Session = $session;
        }
        else
        {
            return null;
        }
        
        // set database object
        if(isset($db) && $db!=null)
        {
            $this->Db = $db;
        }
        else
        {
            return null;
        }
        
        // set Repository Action class
        $this->RepositoryAction = new RepositoryAction();
        $this->RepositoryAction->Session = $this->Session;
        $this->RepositoryAction->Db = $this->Db;
        $this->dbAccess = new RepositoryDbAccess($this->Db);
        $this->RepositoryAction->dbAccess = $this->dbAccess;
        
        // individual initialize
        $this->initialize();
    }
    
    /**
     * Initialization
     * 初期化
     */
    private function initialize()
    {
    }
    
    /**
     * Set the output format
     * 出力形式を設定
     *
     * @param string $filter Output form 出力形式
     */
    public function setDataFilter($filter)
    {
        if($filter == self::DATA_FILTER_SIMPLE)
        {
            $this->dataFilter = $filter;
        }
    }
    
    /**
     * Output item information
     * アイテム情報を出力
     *
     * @param array $itemData Item data アイテムデータ
     *                        array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr_type"]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return string The output string 出力文字列
     */
    public function outputRecord($itemData)
    {
        $xml = '';
        return $xml;
    }
}

?>