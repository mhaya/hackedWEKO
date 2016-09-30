<?php

/**
 * Action for select item type
 * アイテムタイプ選択時のアクションクラス
 *
 * @package WEKO
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
 * Action for select item type
 * アイテムタイプ選択時のアクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Itemtype_Setting extends RepositoryAction
{
	/**
	 * Execute
	 * 実行
	 *
	 * @return string "success"/"error" success/failed 成功/失敗
	 */
    function executeApp()
    {
    	// セッション情報初期化 for アイテムタイプ設定
    	$this->Session->removeParameter("item_type_id");		// アイテムタイプID
    	$this->Session->removeParameter("item_type"); 		// アイテムタイプ
    	$this->Session->removeParameter("metadata_table");	// アイテムタイプ属性テーブル
    	
    	// セッション情報初期化 メタデータ 2008/03/04
    	$this->Session->removeParameter("metadata_num");
    	$this->Session->removeParameter("metadata_title");
   		$this->Session->removeParameter("metadata_type");
    	$this->Session->removeParameter("metadata_required");
   		$this->Session->removeParameter("metadata_disp");
   		$this->Session->removeParameter("metadata_candidate");
   		
   		$this->Session->removeParameter("import_item_type_name");
   		
   		// エラーコード開放
   		$this->Session->removeParameter("error_code");
   		
        return 'success';
    }
}
?>
