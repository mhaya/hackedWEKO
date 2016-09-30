<?php

/**
 * Action class for deletion ranking data
 * ランキングデータ削除用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Resetranking.class.php 3131 2011-01-28 11:36:33Z haruka_goto $
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
 * Action class for deletion ranking data
 * ランキングデータ削除用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Resetranking extends RepositoryAction
{
	// request parameter	
	/**
	 * (deprecated)OS type
	 * (廃止予定)OS種類
	 *
	 * @var string
	 */
	var $OS_type = null;						// リポジトリ全般 : OS種類
    /**
     * (deprecated)Initial display index display method
     * (廃止予定)初期表示インデックス表示方法
     *
     * @var string
     */
	var $disp_index_type = null;			    // リポジトリ全般 : 初期表示インデックス表示方法
    /**
     * (deprecated)Initial display index
     * (廃止予定)初期表示インデックス
     *
     * @var string
     */
	var $default_disp_index = null;			    // リポジトリ全般 : 初期表示インデックス
    /**
     * (deprecated)New registration period
     * (廃止予定)新規登録期間
     *
     * @var string
     */
	var $ranking_term_recent_regist = null;		// ランキング管理 : 新規登録期間
    /**
     * (deprecated)Statistics period
     * (廃止予定)統計期間
     *
     * @var string
     */
	var $ranking_term_stats = null;	    		// ランキング管理 : 統計期間
    /**
     * (deprecated)Display order
     * (廃止予定)表示順位
     *
     * @var string
     */
	var $ranking_disp_num = null;	   			// ランキング管理 : 表示順位
    /**
     * (deprecated)Ranking display propriety, most the viewed items
     * (廃止予定)ランキング表示可否, 最も閲覧されたアイテム
     *
     * @var string
     */
	var $ranking_is_disp_browse_item = null;	// ランキング表示可否, 最も閲覧されたアイテム
    /**
     * (deprecated)Ranking display permission, items that are the most downloaded
     * (廃止予定)ランキング表示可否, 最もダウンロードされたアイテム
     *
     * @var string
     */
	var $ranking_is_disp_download_item = null;	// ランキング表示可否, 最もダウンロードされたアイテム
    /**
     * (deprecated)Ranking display propriety, the user who created the most items
     * (廃止予定)ランキング表示可否, 最もアイテムを作成したユーザ
     *
     * @var string
     */
	var $ranking_is_disp_item_creator = null;	// ランキング表示可否, 最もアイテムを作成したユーザ
    /**
     * (deprecated)Ranking display propriety, most searched keyword
     * (廃止予定)ランキング表示可否, 最も検索されたキーワード
     *
     * @var string
     */
	var $ranking_is_disp_keyword = null;		// ランキング表示可否, 最も検索されたキーワード
    /**
     * (deprecated)Ranking display propriety, New Items
     * (廃止予定)ランキング表示可否, 新着アイテム
     *
     * @var string
     */
	var $ranking_is_disp_recent_item = null;	// ランキング表示可否, 新着アイテム
    /**
     * (deprecated)Item management: coefficient Cp
     * (廃止予定)アイテム管理 : 係数Cp
     *
     * @var string
     */
	var $item_coef_cp = null;	    			// アイテム管理 : 係数Cp
    /**
     * (deprecated)Item management: coefficient Ci
     * (廃止予定)アイテム管理 : 係数Ci
     *
     * @var string
     */
	var $item_coef_ci = null;					// アイテム管理 : 係数Ci
    /**
     * (deprecated)Item management: coefficient Cpf
     * (廃止予定)アイテム管理 : 係数Cpf
     *
     * @var string
     */
	var $file_coef_cp = null;	    			// アイテム管理 : 係数Cpf
    /**
     * (deprecated)Item management: coefficient Cif
     * (廃止予定)アイテム管理 : 係数Cif
     *
     * @var string
     */
	var $file_coef_ci = null;					// アイテム管理 : 係数Cif
	/**
	 * (deprecated)Item management: Export whether the file output
	 * (廃止予定)アイテム管理 : Export ファイル出力の可否
	 *
	 * @var string
	 */
	var $export_is_include_files = null;		// アイテム管理 : Export ファイル出力の可否
	
	/**
	 * Active tab
	 * アクティブタブ
	 *
	 * @var int
	 */
	var $admin_active_tab = null;				// アクティブタブ

	/**
	 * To initialize the rankings
	 * ランキングを初期化する
	 *
	 * @return string
	 */
	function execute()
	{
		try
		{
			//initialize
			$result = $this->initAction();
			if( $result == false)
			{
				$exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	// set error message
				$DetailMsg = null;
				sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
				$exception->setDetailMsg($DetailMsg);
				$this->failTrans();	
				throw $exception;
			}
			$this->Session->setParameter("admin_active_tab", $this->admin_active_tab);	//set active_tab_info
			
			// ---------------------------------------------------------
			// Delete Ranking Data
			// ---------------------------------------------------------
			//repository_log
			/*
	        $query = "DELETE FROM ". DATABASE_PREFIX ."repository_log";
   			$result = $this->Db->execute($query);
   			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("DROP repository_log  failed : %s", $errMsg ); 
		        $this->Session->setParameter("error_msg", $tmpstr);
		        $this->failTrans();		//rollback
				return 'error';
		   	}
				
			//repository_log_seq_id
			$query = "DROP TABLE IF EXISTS ". DATABASE_PREFIX. "repository_log_seq_id";
   			$result = $this->Db->execute($query);
   			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("DROP repository_log_seq_id failed : %s", $errMsg ); 
		        $this->Session->setParameter("error_msg", $tmpstr);
		        $this->failTrans();		//rollback
		        return 'error';
		   	}
			*/
						
			//repository_ranking
	        $query = "DELETE FROM ". DATABASE_PREFIX ."repository_ranking";
   			$result = $this->Db->execute($query);
   			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("DROP repository_ranking failed : %s", $errMsg ); 
		        $this->Session->setParameter("error_msg", $tmpstr);
		        $this->failTrans();		//rollback
		        return 'error';
		   	}
		   	// get execute date
		   	//$execute_time = date( "Y/m/d H:i:s", time() );
		   	$DATE = new Date();
		   	$execute_time = str_replace("-","/",$DATE->getDate());
		   	
		   	// ----------------------------------------------------
	        // Add Last Reset Ranking Date To repository_parameter 
	        // ----------------------------------------------------
		   	$params = null;				// パラメタテーブル更新用クエリ    		
    		$params[] = '';				// param_value
    		$params[] = $this->Session->getParameter("_user_id");// mod_user_id
    		$params[] = $this->TransStartDate;				// mod_date
    		$params[] = '';									// param_name
    		// 開始日時
    		$params[0] = $execute_time;						// param_value
    		$params[3] = 'ranking_last_reset_date';			// param_name
    		$result = $this->updateParamTableData($params, $Error_Msg);
    		if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("ranking_last_reset_date update failed : %s", $errMsg ); 
	            $this->Session->setParameter("error_msg", $tmpstr);
	            $this->failTrans();		//トランザクション失敗を設定(ROLLBACK)
				return 'error';
    		}
    		
    		// finalize
			$result = $this->exitAction();	// If Transaction is success, it is committed
			$this->finalize();
	        return 'success';
		}
		catch (RepositoryException $Exception)
		{
    	    //エラーログ出力
        	$this->logFile(
	        	"Repository_Action_Edit_ResetRanking",				//クラス名
	        	"execute",						//メソッド名
	        	$Exception->getCode(),			//ログID
	        	$Exception->getMessage(),		//主メッセージ
	        	$Exception->getDetailMsg() );	//詳細メッセージ	        
        	//アクション終了処理
      		$this->exitAction();                   //トランザクションが失敗していればROLLBACKされる        
	        //異常終了
    	    return "error";
		}
	}
}
?>