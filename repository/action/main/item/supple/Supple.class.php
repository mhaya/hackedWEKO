<?php
/**
 * Action for supple item register and delete class
 * サプリアイテム既存登録＆削除用アクション
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Supple.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR.'/modules/repository/components/common/WekoAction.class.php';
/**
 * Handle manager class
 * ハンドル管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
/**
 * Search table manager class
 * 検索テーブル管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositorySearchTableProcessing.class.php';

/**
 * Action for supple item register and delete class
 * サプリアイテム既存登録＆削除用アクション
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Main_Item_Supple extends WekoAction
{
	// 使用コンポーネントを受け取るため
	/**
	 * Mail main components
	 * mailMainのコンポーネント
	 *
	 * @var Mail_Main
	 */
	public $mailMain = null;

	// リクエストパラメータ
	/**
	 * Item ID
	 * アイテムID
	 *
	 * @var int
	 */
	public $item_id = null;

	/**
	 * Item number
	 * アイテムNo
	 *
	 * @var int
	 */
	public $item_no = null;

	/**
	 * supple content mode(register or delete)
	 * サプリコンテンツのモード(登録or削除)
	 *
	 * @var string
	 */
	public $mode = null;

	/**
	 * supple contents URL
	 * サプリコンテンツURL
	 *
	 * @var string
	 */
	public $weko_key = null;

	/**
	 * supple number
	 * サプリNo
	 *
	 * @var int
	 */
	public $supple_no = null;

	/**
	 * workflow flag
	 * ワークフローフラグ
	 *
	 * @var bool
	 */
	public $workflow_flag = null;

	/**
	 * workflow active tab
	 * ワークフローアクティブタブ
	 *
	 * @var string
	 */
	public $workflow_active_tab = null;
	
	/**
	 * Execute
	 * 実行
	 *
	 * @return string "success"/"workflow"/"error" success/return workflow/failed 成功/ワークフロー画面へ戻る/失敗
	 */
    protected function executeApp()
    {
		/**
		 * 1.既存登録ボタン押下の場合
		 * 1-1.サプリコンテンツの登録を行う
		 * 2.削除ボタン押下の場合
		 * 2-2.サプリコンテンツの削除を行う
		 */
        // Update suppleContentsEntry Y.Yamazawa --start-- 2015/03/17 --start--
        try{
            // デコード
            $this->weko_key = rawurldecode($this->weko_key);// Add suppleContentsEntry Y.Yamazawa --start-- 2015/03/23 --start--

        	// サプリアクションからの遷移を示すフラグ
            $this->Session->setParameter("supple_flag", "true");

            $this->infoLog("businessSupple", __FILE__, __CLASS__, __LINE__);
            $businessSupple = BusinessFactory::getFactory()->getBusiness("businessSupple");
        	if($this->mode == "add_existing"){
            	// 既存サプリアイテム登録
    	        $businessSupple->entrySuppleContents($this->item_id,$this->item_no,$this->weko_key);

        	}else if($this->mode == "delete"){
        	    // サプリアイテム削除
        	    $businessSupple->deleteSuppleContents($this->item_id,$this->item_no,$this->supple_no);
        	    if($this->workflow_flag == "true"){
        	        $this->Session->setParameter("supple_workflow_active_tab", $this->workflow_active_tab);
        	    }
        	}
            
            // TODO：検索テーブル再作成のビジネスロジックを作る
            $searchTableProcessing = new RepositorySearchTableProcessing($this->Session, $this->Db);
            $searchTableProcessing->updateSearchTableForItem($this->item_id, $this->item_no);
            
    		if($this->workflow_flag == "true"){
        		return "workflow";
        	} else {
        		return "success";
        	}
        }
        catch(AppException $e){
            $msg = $e->getMessage();
            $this->Session->setParameter("supple_error",$msg);
            return "error";
    	}
        // Update suppleContentsEntry Y.Yamazawa --end-- 2015/03/17 --end--
    }
}
?>
