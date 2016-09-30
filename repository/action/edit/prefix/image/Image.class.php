<?php

/**
 * Action class for cooperation data automatic creation of the Y handle (http://id.nii.ac.jp/)
 * Yハンドル(http://id.nii.ac.jp/)との連携データ自動作成用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Image.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Y handle (http://id.nii.ac.jp/) cooperative processing common classes
 * Yハンドル(http://id.nii.ac.jp/)連携処理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/IDServer.class.php';

/**
 * Action class for cooperation data automatic creation of the Y handle (http://id.nii.ac.jp/)
 * Yハンドル(http://id.nii.ac.jp/)との連携データ自動作成用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Prefix_Image extends RepositoryAction
{
	// リクエストパラメタ
	/**
	 * SSH path
	 * SSHパス
	 *
	 * @var string
	 */
	var $ssl_path = null;

	/**
	 * Auto get the Y handle prefix
	 * Yハンドル prefixを自動取得
	 * 
	 * @return string Result 結果
	 * @access  public
	 */
	function execute()
	{
		try {
			// ----------------------------------------------------
			// call init action
			// ----------------------------------------------------
			$result = $this->initAction();			
			if ( $result === false ) {
				$exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
				$DetailMsg = null;							  //詳細メッセージ文字列作成
				sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
				$exception->setDetailMsg( $DetailMsg );			 //詳細メッセージ設定
				$this->failTrans();										//トランザクション失敗を設定(ROLLBACK)
				throw $exception;
			}
			$this->Session->removeParameter("error_flg");
			$id_server = new IDServer($this->Session, $this->Db);
			$result = $id_server->prefixAutoEntry($this->ssl_path);
			
			if($result === false){
				// エラーのためリトライ
				$this->Session->setParameter("error_flg", "true");
				return "error";
			}
			
			// アクション終了処理
			$result = $this->exitAction();	// トランザクションが成功していればCOMMITされる
			$this->finalize();
			return 'success';
		}
		catch ( RepositoryException $Exception) {
			//エラーログ出力
			$this->logFile(
				"SampleAction",					//クラス名
				"execute",						//メソッド名
				$Exception->getCode(),			//ログID
				$Exception->getMessage(),		//主メッセージ
				$Exception->getDetailMsg() );	//詳細メッセージ			
			//アクション終了処理
	  		$this->exitAction();				   //トランザクションが失敗していればROLLBACKされる		
			//異常終了
			return "error";
		}
	}
}
?>
