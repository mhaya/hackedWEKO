<?php

/**
 * View class for other language metadata item name setting pop-up display
 * 他言語メタデータ項目名設定ポップアップ表示用ビュークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Multilang.class.php 22759 2013-05-21 04:47:11Z koji_matsuo $
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
 * View class for other language metadata item name setting pop-up display
 * 他言語メタデータ項目名設定ポップアップ表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Common_Itemtype_Multilang extends RepositoryAction
{
    /**
     * Edit attribute id
     * 編集している項目のID
     *
     * @var int
     */
    public $edit_id = null;
    /**
     * ID of the parent window
     * 親ウィンドウのID
     *
     * @var int
     */
    public $sel_node_pid = null;
    /**
     * Of the flag can edit the default
     * デフォルトを編集できるかのフラグ
     *
     * @var int
     */
    public $default_edit = null;

    /**
     * Other languages metadata item name setting pop-up display
     * 他言語メタデータ項目名設定ポップアップ表示
     *
     * @access  public
     * @return string Result 結果
     */
    function executeApp()
    {
        return "success";
    }
}
?>
