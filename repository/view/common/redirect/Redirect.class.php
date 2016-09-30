<?php

/**
 * View class for redirect screen display
 * リダイレクト画面表示用ビュークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Redirect.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * View class for redirect screen display
 * リダイレクト画面表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Common_Redirect extends RepositoryAction
{
	// セッションとデータベースのオブジェクトを受け取る
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

    // member
    /**
     * Redirect url
     * リダイレクトURL
     *
     * @var string
     */
    var $redirect_url = "";
    /**
     * Redirect block id
     * リダイレクトブロックID
     *
     * @var int
     */
    var $redirect_block_id = "";
    /**
     * Redirect message
     * リダイレクトメッセージ
     *
     * @var string
     */
    var $redirect_message = "";
    /**
     * Top page transition flag
     * トップページ遷移フラグ
     *
     * @var string
     */
    var $top_flag = "false";
    
    /**
     * Redirection screen display
     * リダイレクト画面表示
     *
     * @access  public
     * @return string Result 結果
     */
    function executeApp()
    {
		// check base_url with "/"
		$this->redirect_url = BASE_URL."|";
		$this->redirect_url = str_replace("/|", "/", $this->redirect_url);
		$this->redirect_url = str_replace("|", "/", $this->redirect_url);
		
		$this->top_flag = "false";
        
		// set redirect URL
		if($this->Session->getParameter("redirect_flg") == "workflow"){
			$this->redirect_url .= "index.php?action=pages_view_main&active_action=repository_view_main_workflow";
		} else if($this->Session->getParameter("redirect_flg") == "selecttype"){
			$this->redirect_url .= "index.php?action=pages_view_main&active_action=repository_view_main_item_selecttype";
		} else if($this->Session->getParameter("redirect_flg") == "itemtype"){
			$this->redirect_url .= "index.php?action=pages_view_main&active_action=repository_view_edit_itemtype_setting";
		} else if($this->Session->getParameter("redirect_flg") == "detail"){
			$this->redirect_url .= "index.php?action=repository_uri&item_id=".$this->Session->getParameter("redirect_item_id");
		} else if($this->Session->getParameter("redirect_flg") == "review"){
			$this->redirect_url .= "index.php?action=pages_view_main&active_action=repository_view_edit_review";
		} else if($this->Session->getParameter("redirect_flg") == "admin"){
			$this->redirect_url .= "index.php?action=pages_view_main&active_action=repository_view_edit_admin";
		} else if($this->Session->getParameter("redirect_flg") == "tree_update"){
			$this->redirect_url .= "index.php?action=pages_view_main&active_action=repository_view_edit_tree";
		} else if($this->Session->getParameter("redirect_flg") == "privatetree_update"){
			$this->redirect_url .= "index.php?action=pages_view_main&active_action=repository_view_main_privatetree";
		} else if($this->Session->getParameter("redirect_flg") == "logreport" || $this->Session->getParameter("redirect_flg") == "sitelicensemail"){
			$this->redirect_url .= "index.php?action=pages_view_main&active_action=repository_view_edit_log";
		} else if($this->Session->getParameter("redirect_flg") == "supple"){
			if($this->Session->getParameter("ej_workflow_flag") == "true"){
				$this->redirect_url = $this->Session->getParameter("ej_weko_url")."index.php?action=pages_view_main&active_action=repository_view_common_item_supple_redirect".
									  "&item_id=".$this->Session->getParameter("ej_item_id").
									  "&item_no=".$this->Session->getParameter("ej_item_no").
									  "&attribute_id=".$this->Session->getParameter("ej_attribute_id").
									  "&supple_no=".$this->Session->getParameter("ej_supple_no").
									  "&mode=edit".
									  "&weko_id=".$this->Session->getParameter("supple_weko_id").
									  "&page_id=".$this->Session->getParameter("ej_page_id").
									  "&block_id=".$this->Session->getParameter("ej_block_id");
			} else {
				$this->redirect_url = $this->Session->getParameter("ej_weko_url")."index.php?action=pages_view_main&active_action=repository_view_common_item_supple_redirect".
									  "&item_id=".$this->Session->getParameter("ej_item_id").
									  "&item_no=".$this->Session->getParameter("ej_item_no").
									  "&mode=add_new".
									  "&weko_id=".$this->Session->getParameter("supple_weko_id").
									  "&page_id=".$this->Session->getParameter("ej_page_id").
									  "&block_id=".$this->Session->getParameter("ej_block_id");
			}
			$this->Session->removeParameter("ej_weko_url");
			$this->Session->removeParameter("ej_item_id");
			$this->Session->removeParameter("supple_weko_id");
			$this->Session->removeParameter("ej_page_id");
			$this->Session->removeParameter("ej_block_id");
			$this->Session->removeParameter("ej_workflow_flag");
			$this->Session->removeParameter("ej_workflow_active_tab");
			$this->Session->removeParameter("ej_attribute_id");
			$this->Session->removeParameter("ej_supple_no");
			$this->top_flag = "true";
		} else {
			// go top
//			$this->redirect_url .= "index.php";
			$this->top_flag = "true";
		}

		// get BlockId and PageId
//		if($this->redirect_url != null && $this->redirect_url != ""){
		if($this->top_flag != "true"){
			$block_info = $this->getBlockPageId();
			$this->redirect_block_id = $this->redirect_url."&page_id=".$block_info["page_id"]."&block_id=".$block_info["block_id"];
			$this->redirect_url = $this->redirect_url."&page_id=".$block_info["page_id"]."&block_id=".$block_info["block_id"];		// Add Page specification K.Matsuo 2011/09/02
		}
        
		// set redirect message
		$this->setLangResource();
		if($this->Session->getParameter("redirect_flg") == "select_lang"){
			$this->redirect_message = $this->Session->getParameter("smartyAssign")->getLang("repository_select_lang_change_before")."<br/>".
									  $this->Session->getParameter("smartyAssign")->getLang("repository_select_lang_change_after");
		} else {
			$this->redirect_message = $this->Session->getParameter("smartyAssign")->getLang("repository_db_update_OK");
		}
		
		// delete session
		$this->Session->removeParameter("redirect_flg");
		$this->Session->removeParameter("redirect_item_id");
		
		return "redirect";
    }
}
?>
