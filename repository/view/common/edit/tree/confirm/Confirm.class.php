<?php

/**
 * View class for the index Delete confirmation pop-up display
 * インデックス削除確認ポップアップ表示用ビュークラス
 *
 * @package WEKO
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
 * View class for the index Delete confirmation pop-up display
 * インデックス削除確認ポップアップ表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Common_Edit_Tree_Confirm extends RepositoryAction
{
	// change index tree 2008/12/10 Y.Nakao --start--
	/**
	 * Selected index ID
	 * 選択されたインデックスID
	 *
	 * @var int
	 */
	var $sel_node_id = null;
	/**
	 * Selected parent index ID
	 * 選択されたインデックスの親インデックスID
	 *
	 * @var int
	 */
	var $sel_node_pid = null;
	/**
	 * 選択されたインデックス名
	 *
	 * @var string
	 */
	var $sel_node_name = null;
	
	/**
	 * Execute
	 * 実行
	 *
	 * @return string "success"/"error" success/failed 成功/失敗
	 * @throws RepositoryException
	 */
	function execute()
	{
		try {
			//アクション初期化処理
			$result = $this->initAction();
			if ( $result === false ) {
				$exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
				$DetailMsg = null;							  //詳細メッセージ文字列作成
				sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
				$exception->setDetailMsg( $DetailMsg );			 //詳細メッセージ設定
				$this->failTrans();										//トランザクション失敗を設定(ROLLBACK)
				throw $exception;
			}
			
            // decode node name 2011/05/31 A.Suzuki --start--
            $this->sel_node_name = rawurldecode($this->sel_node_name);
            $this->sel_node_name = htmlspecialchars_decode($this->sel_node_name);
            // decode node name 2011/05/31 A.Suzuki --end--
            
            // Add rollback bug of #292 2012/01/12 T.Koyasu -start-
            // when execute cansel of delete_all and open child indexes, unselect edit_index
            $this->Session->removeParameter("edit_index");
            // Add rollback bug of #292 2012/01/12 T.Koyasu -end-
            
            //アクション終了処理
            $this->exitAction();   //トランザクションが成功していればCOMMITされる
            $this->finalize();
			return 'success';
			
		 } catch ( RepositoryException $Exception) {
			//エラーログ出力
			$this->logFile(
				"SampleAction",					//クラス名
				"execute",						//メソッド名
				$Exception->getCode(),			//ログID
				$Exception->getMessage(),		//主メッセージ
				$Exception->getDetailMsg() );	//詳細メッセージ
			
			//アクション終了処理
			$result = $this->exitAction();	 //トランザクションが成功していればCOMMITされる
			
			return "error";
		}
	}
	// change index tree 2008/12/10 Y.Nakao --end--

}
?>
