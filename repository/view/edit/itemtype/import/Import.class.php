<?php

/**
 * View for item type import
 * アイテムタイプのインポート画面表示クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Import.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * View for item type import
 * アイテムタイプのインポート画面表示クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Edit_Itemtype_Import extends RepositoryAction
{

    /**
     * Execute
     * 実行
     *
     * @return string "success" success 成功
     */
    function executeApp()
    {
        return 'success';
    }
}
?>
