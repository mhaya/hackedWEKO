<?php

/**
 * View for the item type setting
 * アイテムタイプ選択表示クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Setting.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * DI Container class
 * DIコンテナクラス
 */
require_once MAPLE_DIR.'/core/DIContainer.class.php';

/**
 * View for the item type setting
 * アイテムタイプ選択表示クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Edit_Itemtype_Setting extends RepositoryAction
{
	// get component
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

	// Member variable
	/**
	 * Item type data
	 * アイテムタイプ情報配列
	 *
	 * @var array
	 */
	var $itemtype_data= null;
	/**
	 * Error message
	 * エラーメッセージ
	 *
	 * @var string
	 */
	var $error_msg = "";

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
	    
		// init session for item type setting
		$this->Session->removeParameter("item_type_id");		// item type id
		$this->Session->removeParameter("item_type"); 		// item type
		$this->Session->removeParameter("metadata_table");	// item attr table
		$this->Session->removeParameter("item_type_update");
		$this->Session->removeParameter("error_code");

		// init session for metadata 2008/03/04
		$this->Session->removeParameter("metadata_num");
		$this->Session->removeParameter("metadata_title");
		$this->Session->removeParameter("metadata_type");
		$this->Session->removeParameter("metadata_required");
		$this->Session->removeParameter("metadata_disp");
		$this->Session->removeParameter("metadata_candidate");
		 
		$this->error_msg = $this->Session->getParameter("error_msg");
		$this->Session->removeParameter("error_msg");

		// get all item type from DB
		$result = $this->Db->selectExecute("repository_item_type", array('is_delete' => 0), array("item_type_id" => "ASC"));
		if($result === false) {
			return 'error';
		}
		 
		// get show item type
		// default item type is header
		$default_itemtype = array();
		$create_itemtype = array();
		for($ii=0; $ii<count($result); $ii++) {
			if($result[$ii]['item_type_id']>10000){
                // Comment out to can edit itemtype for harvesting 2013/01/17 A.Suzuki --start--
                //if($result[$ii]['item_type_id']<20001)
                //{
                    array_push($default_itemtype,
                    array($result[$ii]['item_type_id'], $result[$ii]['item_type_name']));
                //}
                // Comment out to can edit itemtype for harvesting 2013/01/17 A.Suzuki --end--
			} else {
				array_push($create_itemtype,
				array($result[$ii]['item_type_id'], $result[$ii]['item_type_name']));
			}
		}
		$this->itemtype_data = array();
		$this->itemtype_data = array_merge($default_itemtype, $create_itemtype);
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
