<?php
/**
 * View class for workflow screen display
 * インポート完了画面表示用ビュークラス
 *
 * @package WEKO
 */
// --------------------------------------------------------------------
//
// $Id: Confirm.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * View class for workflow screen display
 * インポート完了画面表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Edit_Import_Confirm extends RepositoryAction
{
    // component 
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    var $Session = null;
    // member
    /**
     * Error message
     * エラーメッセージ
     *
     * @var string
     */
    var $error_msg = null;
    
    // Add e-person 2013/12/04 R.Matsuura --start--
    /**
     * import mode
     * インポートモード
     * 
     * @var int
     */
    public $importmode = null;
    /**
     * authority import success number
     * インポート数
     * 
     * @var int
     */
    public $successnum = null;
    /**
     * Help icon display flag
     * ヘルプアイコン表示フラグ
     *
     * @var int
     */
    public $help_icon_display =  null;
    // Add e-person 2013/12/04 R.Matsuura --end--
    
    /**
     * Import completion screen display
     * インポート完了画面表示
     * 
     * @access  public
     */
    function executeApp()
    {
        $this->error_msg = $this->Session->getParameter("error_msg");
        $this->Session->removeParameter("error_msg");
        $this->importmode = $this->Session->getParameter("importmode");
        $this->Session->removeParameter("importmode");
        $this->successnum = $this->Session->getParameter("successnum");
        $this->Session->removeParameter("successnum");
        
        $result = $this->getAdminParam('help_icon_display', $this->help_icon_display, $Error_Msg);
        return 'success';
    }
}
?>
