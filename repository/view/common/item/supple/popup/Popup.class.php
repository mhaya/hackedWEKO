<?php

/**
 * View class for supplemental content registration pop-up display
 * サプリメンタルコンテンツ登録ポップアップ表示用ビュークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Popup.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * View class for supplemental content registration pop-up display
 * サプリメンタルコンテンツ登録ポップアップ表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Common_Item_Supple_Popup extends RepositoryAction
{
	// リクエストパラメタ
    /**
     * Item id
     * アイテムID
     *
     * @var int
     */
	var $item_id = null;			// アイテムID
    /**
     * Item serial number
     * アイテム通番
     *
     * @var int
     */
	var $item_no = null;			// アイテム通番
	/**
	 * Supple WEKO URL
	 * サプリWEKOURL
	 *
	 * @var unknown_type
	 */
	var $supple_weko_url = "";
	
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
     * @var DbObject
     */
	var $Db = null;
	
    /**
     * Pop-up display
     * ポップアップ表示
     *
     * @access  public
     * @return string|boolean Result 結果
     */
    function execute()
    {
    	//アクション初期化処理
        $result = $this->initAction();
        if ( $result === false ) {
            $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
            $DetailMsg = null;                              //詳細メッセージ文字列作成
            sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
            $exception->setDetailMsg( $DetailMsg );             //詳細メッセージ設定
            $this->failTrans();                                        //トランザクション失敗を設定(ROLLBACK)
            throw $exception;
        }
        
    	// パラメタテーブルからサプリWEKOのアドレスを取得する
		$query = "SELECT param_value FROM ".DATABASE_PREFIX."repository_parameter ".
				 "WHERE param_name = 'supple_weko_url';";
		$result = $this->Db->execute($query);
		if($result === false){
			return false;
		}
		if($result[0]['param_value'] != ""){
			$this->supple_weko_url = $result[0]['param_value'].
									 "/?action=repository_view_common_item_supple_logincheck".
									 "&ej_item_id=".$this->item_id.
									 "&ej_item_no=".$this->item_no;
		}
    	
    	//アクション終了処理
		$result = $this->exitAction();     // トランザクションが成功していればCOMMITされる
    	$this->finalize();
        return 'success';
    }
}
?>
