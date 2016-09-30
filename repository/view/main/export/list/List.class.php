<?php
/**
 * View class for item list export confirmation
 * アイテム一覧エクスポート確認用ビュークラス
 *
 * @package WEKO
 */
// --------------------------------------------------------------------
//
// $Id: List.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * View class for item list export confirmation
 * アイテム一覧エクスポート確認用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Main_Export_List extends RepositoryAction
{
    // Components
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    var $Session = null;
    
    // member
    /**
     * Error message(size over)
     * エラーメッセージ(サイズオーバー)
     *
     * @var string
     */
    var $size_over_msg = "";
    /**
     * Error message (100 over)
     * エラーメッセージ(100件より大きい)
     *
     * @var string
     */
    var $count_over_msg = "";
    /**
     * Maximum export count message
     * 最大エクスポート数メッセージ
     *
     * @var string
     */
    var $max_export_count_msg = "";
    
    /**
     * Display item list export confirmation
     * アイテム一覧エクスポート画面表示
     *
     * @access  public
     */
    function executeApp()
    {
        //$this->setLangResource();
        $smartyAssign = $this->Session->getParameter("smartyAssign");
        if($smartyAssign == null || !isset($smartyAssign))
        {
            $this->setLangResource();
            $smartyAssign = $this->Session->getParameter("smartyAssign");
        }
        
        if($this->Session->getParameter("size_over")==true){
            $this->size_over_msg = $smartyAssign->getLang("repository_export_file_size_over");
        }
        
        if($this->Session->getParameter("count_over")==true){
            $this->count_over_msg = $smartyAssign->getLang("repository_export_item_count_over");
        }
        
        if(($this->size_over_msg!=null || $this->count_over_msg!=null) && $this->Session->getParameter("max_export_count")!=null){
            $this->max_export_count_msg = sprintf($smartyAssign->getLang("repository_export_no_export_over_items"), $this->Session->getParameter("max_export_count")+1);
        }

        $this->Session->removeParameter("size_over");
        $this->Session->removeParameter("count_over");
        $this->Session->removeParameter("max_export_count");
        $this->Session->removeParameter("export_print");
        
        return 'success';
    }
}
?>
