<?php

/**
 * View for item type icon edit
 * アイテムタイプアイコン編集画面表示クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Icon.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * View for item type icon edit
 * アイテムタイプアイコン編集画面表示クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Edit_Itemtype_Icon extends RepositoryAction 
{
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
        
        // Mod fix a glitch with upload icon is deleted when back from repository_item_type_confirm 2012/02/16 T.Koyasu -start-
        // session.icon_edit is const value
        $uploadIcon = $this->Session->getParameter('upload_icon'); 
        // add the if construst
        if(isset($uploadIcon)){
            $this->Session->setParameter("icon_edit", RepositoryConst::SESSION_PARAM_UPLOAD_ICON);
        }
		else if($this->Session->getParameter("item_type_edit_flag")==1){
	    	// 既存編集の場合
	    	$query = "SELECT * FROM ". DATABASE_PREFIX ."repository_item_type ".
	    			 "WHERE item_type_id = ?;";
	    	$params = null;
	    	$params = $this->Session->getParameter("item_type_id");
	    	$result = $this->Db->execute($query, $params);
	    	if($result===false){
	    		$this->Session->setParameter("icon_edit",RepositoryConst::SESSION_PARAM_DEFAULT_ICON);
	    		echo "SELECT ICON ERROR ";
	    		return 'success';
	    	}
	    	
            // changed condition(if exists icon in database and pushes icon delete button)
            $defaultIconFlg = $this->Session->getParameter('icon_edit');
	    	if($result[0]['icon'] && $defaultIconFlg != RepositoryConst::SESSION_PARAM_DEFAULT_ICON){
	    		// アイコン登録済みアイコンあり
	    		$this->Session->setParameter("icon_edit", RepositoryConst::SESSION_PARAM_DATABASE_ICON);
	    	} else {
	    		// アイコン未登録(共通リソース)
	    		$this->Session->setParameter("icon_edit", RepositoryConst::SESSION_PARAM_DEFAULT_ICON);
	    	}
	    	
		} else {
			// 新規作成
			$this->Session->setParameter("icon_edit", RepositoryConst::SESSION_PARAM_NEW_ITEM_TYPE);
		}
        // Mod fix a glitch with upload icon is deleted when back from repository_item_type_confirm 2012/02/16 T.Koyasu -end-
		
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
