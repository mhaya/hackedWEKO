<?php

/**
 * Action class for deletion item type
 * アイテムタイプ削除用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Delete.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Search table manager class
 * 検索テーブル管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositorySearchTableProcessing.class.php';
/**
 * Item type manager class
 * アイテムタイプ管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/ItemtypeManager.class.php';

/**
 * Action class for deletion item type
 * アイテムタイプ削除用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Itemtype_Delete extends RepositoryAction
{
	
	// リクエストパラメタ
	/**
	 * Item type ID
	 * アイテムタイプID
	 *
	 * @var int
	 */
	var $item_type_id = null;

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
			// INIT
			$result = $this->initAction();
			if ( $result === false ) {
				$exception = new RepositoryException( "ERR_MSG_xxx-xxx1", 001 );	//主メッセージとログIDを指定して例外を作成
				//$DetailMsg = null;							  //詳細メッセージ文字列作成
				//sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
				//$exception->setDetailMsg( $DetailMsg );			 //詳細メッセージ設定
				$this->failTrans();										//トランザクション失敗を設定(ROLLBACK)
				throw $exception;
			}
			
			//////////////////// Lock for update ///////////////////////
			$query = "SELECT mod_date ".
					 "FROM ". DATABASE_PREFIX ."repository_item_type ".
					 "WHERE item_type_id = ? AND ".
					 "is_delete = ? ".
					 "FOR UPDATE;";
			$params = null;
			$params[] = $this->item_type_id;			// item_type_id
			$params[] = 0;
			$result = $this->Db->execute($query, $params);
			//Error
			if($result === false) {
				//Get error
				$errNo = $this->Db->ErrorNo();
				$errMsg = $this->Db->ErrorMsg();
				$this->Session->setParameter("error_code", $errMsg);
				//エラー処理を行う
				$exception = new RepositoryException( "ERR_MSG_xxx-xxx1", 001 );
				//$DetailMsg = null;
				//sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1, $str1, $str2 );
				//$exception->setDetailMsg( $DetailMsg );
				// ROLLBACK
				$this->failTrans();
				throw $exception;
			}
			// count = 0 is no update recorde
			if(count($result)==0) {
				//Get error
				$errNo = $this->Db->ErrorNo();
				$errMsg = $this->Db->ErrorMsg();
				$this->Session->setParameter("error_code", $errMsg);
				//エラー処理を行う
				$exception = new RepositoryException( "ERR_MSG_xxx-xxx1", 001 );
				//$DetailMsg = null;
				//sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1, $str1, $str2 );
				//$exception->setDetailMsg( $DetailMsg );
				// ROLLBACK
				$this->failTrans();
				throw $exception;
			}
			// Delete item type
			$query = "UPDATE ". DATABASE_PREFIX ."repository_item_type ".
					 "SET mod_user_id = ?, ".
					 "mod_date = ?, ".
					 "del_user_id = ?, ".
					 "del_date = ?, ".
					 "is_delete = ? ".
					 "WHERE item_type_id = ?; ";
			$params = null;
			$params[] = $this->Session->getParameter("_user_id");   // mod_user_id
			$params[] = $this->TransStartDate;				// mod_date
			$params[] = $this->Session->getParameter("_user_id");   // del_user_id
			$params[] = $this->TransStartDate;				// del_date
			$params[] = 1;									// is_delete
			$params[] = $this->item_type_id;						// item_type_id
			//Run update
			$result = $this->Db->execute($query,$params);
			if($result === false){
				//Get DB error
				$errNo = $this->Db->ErrorNo();
				$errMsg = $this->Db->ErrorMsg();
				$this->Session->setParameter("error_code", $errMsg);
				$exception = new RepositoryException( "ERR_MSG_xxx-xxx1", 001 );
				//$DetailMsg = null;
				//sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1, $str1, $str2 );
				//$exception->setDetailMsg( $DetailMsg );
				// ROLLBACK
				$this->failTrans();
				throw $exception;
			}
			
            // Add detail search 2013/11/25 K.Matsuo --start--
            $searchTableProcessing = new RepositorySearchTableProcessing($this->Session, $this->Db);
            $searchTableProcessing->deleteDataFromSearchTable();
            // Add detail search 2013/11/25 K.Matsuo --end--
            
            // Add itemtype authority 2014/12/17 T.Ichikawa --start--
            $itemtypeManager = new Repository_Components_ItemtypeManager($this->Session, $this->Db, $this->TransStartDate);
            $itemtypeManager->removeExclusiveItemtypeAuthority($this->item_type_id);
            // Add itemtype authority 2014/12/17 T.Ichikawa --end--
            
			//exit commit
			$result = $this->exitAction();
			if ( $result === false ) {
				$exception = new RepositoryException( "ERR_MSG_xxx-xxx3", 1 );
				// ROLLBACK
				$this->failTrans();
				throw $exception;
			}
			$this->finalize();
			return 'success';
		}
		catch ( RepositoryException $Exception) {
			//エラーログ出力
			/*
			logFile(
				"SampleAction",					//クラス名
				"execute",						//メソッド名
				$Exception->getCode(),			//ログID
				$Exception->getMessage(),		//主メッセージ
				$Exception->getDetailMsg() );	//詳細メッセージ
			*/
			//アクション終了処理
	  		$this->exitAction();				   //トランザクションが失敗していればROLLBACKされる
		
			//異常終了
			return "error";
		}
	}
}
?>
