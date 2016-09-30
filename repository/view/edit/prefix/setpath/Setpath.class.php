<?php
/**
 * View class for use in the OpenSSL path verification
 * OpenSSLパス確認用ビュークラス
 *
 * @package WEKO
 */
// --------------------------------------------------------------------
//
// $Id: Setpath.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * View class for use in the OpenSSL path verification
 * OpenSSLパス確認用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Edit_Prefix_Setpath extends RepositoryAction
{
    /**
     * To view the OpenSSL path
     * OpenSSLパスを表示する
     *
     * @access  public
     */
    function executeApp()
    {
    	return 'success';
    }
}
?>
