<?php

/**
 * View for item type mapping confirm
 * アイテムタイプマッピング編集決定画面表示クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Mappingconfirm.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * View for item type mapping confirm
 * アイテムタイプマッピング編集決定画面表示クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Edit_Itemtype_Mappingconfirm extends RepositoryAction 
{
	// 使用コンポーネントを受け取るため
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
     * @var DbObjectAdodb
     */
    var $Db = null;

    /**
     * Help icon dispplay flag
     * ヘルプアイコン表示フラグ
     *
     * @var bool
     */
    var $help_icon_display =  null;

    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws RepositoryException
     */
    function executeApp()
    {
        
        // Add theme_name for image file Y.Nakao 2011/08/03 --start--
        $this->setThemeName();
        // Add theme_name for image file Y.Nakao 2011/08/03 --end--
        
        // Set help icon setting 2010/02/10 K.Ando --start--
        $result = $this->getAdminParam('help_icon_display', $this->help_icon_display, $Error_Msg);
		if ( $result == false ){
			$exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
            $DetailMsg = null;                              //詳細メッセージ文字列作成
            sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
            $exception->setDetailMsg( $DetailMsg );         //詳細メッセージ設定
            $this->failTrans();                             //トランザクション失敗を設定(ROLLBACK)
            throw $exception;
		}
        // Set help icon setting 2010/02/10 K.Ando --end--
    	
		return 'success';
    }
}
?>
