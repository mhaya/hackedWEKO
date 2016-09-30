<?php

/**
 * View for item type edit confirm
 * アイテムタイプ編集決定画面表示クラス
 *
 * @package     WEKO
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
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * View for item type edit confirm
 * アイテムタイプ編集決定画面表示クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Edit_Itemtype_Confirm extends RepositoryAction 
{
	// メタデータ表示用メンバ
    /**
     * Metadata display body array
     * メタデータ表示内容配列
     *
     * @var array
     */
	var $metadata_array = null;		// メタデータ表示内容配列
    /**
     * Metadata title array
     * メタデータ項目配列
     *
     * @var array
     */
	var $metadata_title = null;		// メタデータ項目名配列
    /**
     * Metadata type array
     * メタデータタイプ配列
     *
     * @var array
     */
	var $metadata_type = null;		// メタデータタイプ配列
    /**
     * Metadata required flag array
     * メタデータ必須フラグ配列
     *
     * @var array
     */
	var $metadata_required = null;	// メタデータ必須フラグ列
    /**
     * Metadata show list flag array
     * メタデータ一覧表示フラグ配列
     *
     * @var array
     */
	var $metadata_disp = null;		// メタデータ一覧表示フラグ列
    /**
     * Input item type name
     * 入力アイテムタイプ名
     *
     * @var string
     */
	var $itemtype_name = null;		//前画面で入力したアイテムタイプ名
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
