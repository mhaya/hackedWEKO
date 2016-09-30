<?php

/**
 * View for item type edit
 * アイテムタイプ編集画面表示クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Edit.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Handle manager class
 * ハンドル管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';

/**
 * View for item type edit
 * アイテムタイプ編集画面表示クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Edit_Itemtype_Edit extends RepositoryAction 
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
	//リクエストパラメータを受け取るため

	// メタデータ表示用メンバ
	// (追加項目分はここに詰めるが、基本属性分も他のメンバに持たせておく必要あり多分。)
	/**
	 * Metadata display body array
	 * メタデータ表示内容配列
	 *
	 * @var array
	 */
	var $metadata_array = null;
	/**
	 * Metadata title array
	 * メタデータ項目配列
	 *
	 * @var array
	 */
	var $metadata_title = null;
	/**
	 * Metadata type array
	 * メタデータタイプ配列
	 *
	 * @var array
	 */
	var $metadata_type = null;
	/**
	 * Metadata required flag array
	 * メタデータ必須フラグ配列
	 *
	 * @var array
	 */
	var $metadata_required = null;
	/**
	 * Metadata show list flag array
	 * メタデータ一覧表示フラグ配列
	 *
	 * @var array
	 */
	var $metadata_disp = null;
	/**
	 * Input item type name
	 * 入力アイテムタイプ名
	 *
	 * @var string
	 */
	var $item_type_name = null;		//前画面で入力したアイテムタイプ名
	/**
	 * Metadata candidate array
	 * メタデータ選択候補配列
	 *
	 * @var array
	 */
	var $metadata_candidate = null;	// 選択肢
	/**
	 * Metadata plural enable flag array
	 * メタデータ複数可否フラグ配列
	 *
	 * @var array
	 */
	var $metadata_plural = null;	// メタデータ複数可否 2008/03/04 追加
	/**
	 * Metadata new line flag array
	 * メタデータ改行指定配列
	 *
	 * @var array
	 */
	var $metadata_newline = null;	// メタデータ改行指定 2008/03/13
	/**
	 * Metadata hidden flag array
	 * メタデータ非表示フラグ配列
	 *
	 * @var array
	 */
	var $metadata_hidden = null;	// メタデータ非表示指定 2009/01/27
	/**
	 * Item type data array
	 * 既存アイテムタイプ情報配列
	 *
	 * @var array
	 */
	var $item_type_data = null;		// 既存アイテムタイプロード用のため追加 2008/09/01 Y.Nakao
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
        
    	// 
    	// メタデータ入力情報初期設定
		//
    	$this->metadata_array = array();
    	for($ii=0; $ii<$this->Session->getParameter("metadata_num"); $ii++) {
 			array_push($this->metadata_array , array("", "text", 0, 0, 0, 0, 0, $ii));
    	}
    	
    	//
    	// メタデータ情報がセッションにある場合は値をコピー
    	//
    	
    	// 項目名
    	$this->metadata_title = array();
    	$this->metadata_title = $this->Session->getParameter("metadata_title");
   	   	for($ii=0; $ii<count($this->metadata_title); $ii++) {
    	   	$this->metadata_array[$ii][0] = $this->metadata_title[$ii];
    	}
    	// 属性
    	$this->metadata_type = array();
    	$this->metadata_type = $this->Session->getParameter("metadata_type");
   	   	for($ii=0; $ii<count($this->metadata_type); $ii++) {
    	   	$this->metadata_array[$ii][1] = $this->metadata_type[$ii];
    	}
    	// 必須
    	$this->metadata_required = array();
    	$this->metadata_required = $this->Session->getParameter("metadata_required");
   	   	for($ii=0; $ii<count($this->metadata_required); $ii++) {
    	   	$this->metadata_array[$ii][2] = $this->metadata_required[$ii];
    	}
    	// 一覧表示
        $this->metadata_disp = array();
    	$this->metadata_disp = $this->Session->getParameter("metadata_disp");
   	   	for($ii=0; $ii<count($this->metadata_disp); $ii++) {
    	   	$this->metadata_array[$ii][3] = $this->metadata_disp[$ii];
    	}
        // 複数可否
        $this->metadata_plural = array();
    	$this->metadata_plural = $this->Session->getParameter("metadata_plural");
   	   	for($ii=0; $ii<count($this->metadata_plural); $ii++) {
    	   	$this->metadata_array[$ii][4] = $this->metadata_plural[$ii];
    	}
	    // 改行指定
        $this->metadata_newline = array();
    	$this->metadata_newline = $this->Session->getParameter("metadata_newline");
   	   	for($ii=0; $ii<count($this->metadata_newline); $ii++) {
    	   	$this->metadata_array[$ii][5] = $this->metadata_newline[$ii];
    	}
    	// Add hidden metadata 2009/01/28 A.Suzuki --start--
    	// 非表示指定
        $this->metadata_hidden = array();
    	$this->metadata_hidden = $this->Session->getParameter("metadata_hidden");
   	   	for($ii=0; $ii<count($this->metadata_hidden); $ii++) {
    	   	$this->metadata_array[$ii][6] = $this->metadata_hidden[$ii];
    	}
    	// Add hidden metadata 2009/01/28 A.Suzuki --end--
    	
    	// Extension Itemtype 2008/09/01 Y.Nakao --start-- 
    	// 既存の全アイテムタイプをDBから取得
		$result = $this->Db->selectExecute("repository_item_type", array('is_delete' => 0), array("item_type_id" => "ASC"));
        if($result === false) {
    		return 'error';
    	}
    	// アイテムタイプが0の場合はエラー用テンプレートに遷移
    	if( count($result)<1 ) {
    		return 'noitemtype';
    	}
   		// default item type is header   	
   		$default_itemtype = array();
   		$create_itemtype = array();
    	for($ii=0; $ii<count($result); $ii++) {
    		if($result[$ii]['item_type_id']>10000){
                if($result[$ii]['item_type_id']<20001)
                {
                    array_push($default_itemtype,
                    array($result[$ii]['item_type_id'], $result[$ii]['item_type_name']));
                }
    		} else {
    			array_push($create_itemtype,
    				array($result[$ii]['item_type_id'], $result[$ii]['item_type_name']));
    		}
    	}
    	$this->itemtype_data = array();
    	$this->itemtype_data = array_merge($default_itemtype, $create_itemtype);
    	// Extension Itemtype 2008/09/01 Y.Nakao --end--
    	
    	// Add id server connect check for "file_price" 2009/04/01 Y.Nakao --start--
    	// get prefixID
    	$this->Session->removeParameter("id_server");
		$this->Session->setParameter("id_server", 'false');

        $this->dbAccess = new RepositoryDbAccess($this->Db);
        $DATE = new Date();
        $this->TransStartDate = $DATE->getDate().".000";
        $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->dbAccess, $this->TransStartDate);
        
        $prefixID = $repositoryHandleManager->getPrefix(RepositoryHandleManager::ID_Y_HANDLE);

    	if( is_numeric($prefixID) ){
			$this->Session->setParameter("id_server", 'true');
		}
		$array_input_type = $this->Session->getParameter("metadata_type");
		$chk_file_price = 0;
		for($ii=0; $ii<count($array_input_type); $ii++){
	    	if($array_input_type[$ii] == "file_price"){
	    		$chk_file_price++;
	    		if($chk_file_price > 1){
	    			$this->Session->setParameter("error_code", 7);
	    		} else if($this->Session->getParameter("id_server") != "true"){
	    			$this->Session->setParameter("error_code", 7);
	    		}
	    	}
		}
    	// Add id server connect check for "file_price" 2009/04/01 Y.Nakao --end--

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
