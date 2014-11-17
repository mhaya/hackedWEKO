<?php
// --------------------------------------------------------------------
//
// $Id: Embargo.class.php 436 2010-10-06 00:30:06Z ivis $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * [[アイテム管理viewアクション]]
 * ツリーデータ格納
 * ライセンスマスタ情報取得
 * 現在時刻取得
 * 
 * @package	 [[package名]]
 * @access	  public
 */
class Repository_View_Edit_Item_Embargo extends RepositoryAction
{
	// 使用コンポーネントを受け取るため
	var $Session = null;
	var $Db = null;
	
	// ライセンスマスタ情報
	var $licence_mastere = array();
	
	// 現在時刻格納
	var $date = array();
	
	// 初期値はオープンアクセス
	var $embargo_flag_chk = "1";
	
	// ライセンスマスタの数
	var $licence_num = 0;
	
    // Set help icon setting 2010/02/10 K.Ando --start--
    var $help_icon_display =  null;
    // Set help icon setting 2010/02/10 K.Ando --end--
	
	/**
	 * [[機能説明]]
	 *
	 * @access  public
	 */
	function execute()
	{
		
		// ツリーデータをSessionに格納
		// change index tree view action 2008/12/04 Y.Nakao --start--
		//$this->setIndexTreeData2Session();
		// change index tree view action 2008/12/04 Y.Nakao --end--
		
		// ライセンスマスタ情報取得
		$quety = "SELECT * ".
				 "FROM ". DATABASE_PREFIX ."repository_license_master ".
				 "WHERE is_delete = 0; "; 
	    $this->licence_master = $this->Db->Execute($quety);	    	
	    if ($this->licence_master == false) {
	    	return 'error';
	    }
	    
	    $this->licence_num = count($this->licence_master);
		
		// 現在時刻取得
		//$this->date["year"] = date("Y");
		//$this->date["month"] = date("m");
		//$this->date["day"] = date("d");
		$DATE = new Date();
		$this->date["year"] = $DATE->getYear();
		$this->date["month"] = sprintf("%02d", $DATE->getMonth());
		$this->date["day"] = sprintf("%02d", $DATE->getDay());
		
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