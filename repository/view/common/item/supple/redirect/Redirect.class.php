<?php
// --------------------------------------------------------------------
//
// $Id: Redirect.class.php 53594 2015-05-28 05:25:53Z kaede_matsushita $
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
require_once WEBAPP_DIR. '/modules/repository/components/RepositorySearchTableProcessing.class.php';

/**
 * アイテム詳細画面のサプリコンテンツ新規登録処理・サプリコンテンツタブからのサプリコンテンツ更新処理
 *
 * @author IVIS
 */
class Repository_View_Common_Item_Supple_Redirect extends RepositoryAction
{
	// リクエストパラメタ
	var $item_id = null;					// アイテムID
	var $item_no = null;					// アイテム通番
	var $mode = null;						// 処理モード
	var $weko_id = null;					// weko_id
	var $attribute_id = null;				// attribute_id
	var $supple_no = null;					// suuple_no
	
	var $Session = null;
	var $Db = null;
	var $mailMain = null;
	
    /**
     * アイテム詳細画面の新規登録時のサプリコンテンツ登録
     * または、サプリコンテンツタブでのサプリコンテンツ更新処理
     * 1.アイテム詳細画面から新規登録を行った場合、サプリコンテンツの登録を行う
     * 2.サプリコンテンツタブからサプリWEKOのアイテム更新を行った場合、サプリコンテンツの更新を行う
     * @access  public
     */
    function execute()
    {
        try{
            $this->initActionParam();
            // Update suppleContentsEntry Y.Yamazawa --start-- 2015/03/17 --start--
        	// EJWEKO サプリアイテム情報登録＆詳細画面へリダイレクト
        	if($this->mode == "add_new"){
        	    // リダイレクトURL
        	    $redirect_url = $this->redirectUrl();

        	    // サプリコンテンツの登録
        	    $this->entrySupple();
        	}
        	else if($this->mode == "edit"){
        	    // サプリコンテンツの更新
        	    $this->updateSupple($redirect_url);
        	}
        	// Update suppleContentsEntry Y.Yamazawa --end-- 2015/03/17 --end--
            
            // TODO：検索テーブル再作成のビジネスロジックを作る
            $searchTableProcessing = new RepositorySearchTableProcessing($this->Session, $this->Db);
            $searchTableProcessing->updateSearchTableForItem($this->item_id, $this->item_no);
            
          	//アクション終了処理
    		$result = $this->exitAction();     // トランザクションが成功していればCOMMITされる
    		header("HTTP/1.1 301 Moved Permanently");
      		header("Location: ".$redirect_url);
      		return;
        }
        catch(AppException $e){
            if(!isset($redirect_url)){
                $redirect_url = $this->redirectUrl();
            }
            $msg = $e->getMessage();
            $this->errorLog($msg, __FILE__, __CLASS__, __LINE__);
            $this->Session->setParameter("supple_error",$msg);
            header("HTTP/1.1 301 Moved Permanently");
      		header("Location: ".$redirect_url);
      		return;
        }
    }

    /**
     * パラメータの初期化
     * @throws RepositoryException
     */
    private function initActionParam()
    {
        // check Session and Db Object
        if($this->Session == null){
            $container =& DIContainerFactory::getContainer();
            $this->Session =& $container->getComponent("Session");
        }
        $result = $this->initAction();
        if ( $result === false ) {
            $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
            $DetailMsg = null;                              //詳細メッセージ文字列作成
            sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
            $exception->setDetailMsg( $DetailMsg );             //詳細メッセージ設定
            $this->failTrans();                                        //トランザクション失敗を設定(ROLLBACK)
            throw $exception;
        }
    }

    // Add suppleContentsEntry Y.Yamazawa --start-- 2015/03/30 --start--
    /**
     * サプリコンテンツの登録
     * @param unknown $redirect_url
     */
    private function entrySupple()
    {
        $businessSupple = BusinessFactory::getFactory()->getBusiness("businessSupple");
        $this->Session->setParameter("supple_flag", "true");

        $this->infoLog("businessSupple", __FILE__, __CLASS__, __LINE__);
        $businessSupple->entrySuppleContents($this->item_id,$this->item_no,$this->weko_id);
    }
    // Add suppleContentsEntry Y.Yamazawa --end-- 2015/03/30 --end--

    // Add suppleContentsEntry Y.Yamazawa --start-- 2015/03/30 --start--
    /**
     * サプリコンテンツの更新
     * @param リダイレクトURL $redirect_url
     */
    private function updateSupple(&$redirect_url)
    {
        // サプリアクションからの遷移を示すフラグ
        $this->Session->setParameter("supple_flag", "true");
        // サプリコンテンツ更新
        $this->infoLog("businessSupple", __FILE__, __CLASS__, __LINE__);
        $businessSupple = BusinessFactory::getFactory()->getBusiness("businessSupple");
        $businessSupple->updateSuppleContents($this->item_id,$this->item_no,$this->attribute_id,$this->supple_no,$this->weko_id);
        $supple_workflow_active_tab = $businessSupple->redirectURLParameterOfSuppleWorkflowActiveTab();
        
        // リダイレクトURL
        $id_array = $this->getBlockPageId();
        $redirect_url = BASE_URL."/index.php?action=pages_view_main&active_action=repository_view_main_suppleworkflow".
                "&supple_workflow_active_tab=".$supple_workflow_active_tab.
                "&page_id=".$id_array['page_id'].
                "&block_id=".$id_array['block_id'];
    }
    // Add suppleContentsEntry Y.Yamazawa --end-- 2015/03/30 --end--

    // Add suppleContentsEntry Y.Yamazawa --start-- 2015/03/30 --start--
    /**
     * リダイレクトURLの取得
     * @return リダイレクトURL
     */
    private function redirectUrl()
    {
        // アイテムタイプ情報を取得
        $query = "SELECT attr_type.item_type_id, attr_type.attribute_id, item.uri, item.title, item.title_english ".
                "FROM ".DATABASE_PREFIX."repository_item_attr_type AS attr_type, ".DATABASE_PREFIX."repository_item AS item ".
                "WHERE item.item_id = ? ".
                "AND item.item_no = ? ".
                "AND item.item_type_id = attr_type.item_type_id ".
                "AND attr_type.input_type = 'supple' ".
                "AND item.is_delete = 0 ".
                "AND attr_type.is_delete = 0;";
        $params = array();
        $params[] = $this->item_id;	// item_id
        $params[] = $this->item_no;	// item_no
        $item_type_result = $this->Db->execute($query, $params);
        if($item_type_result === false){
            $this->Session->setParameter("supple_error","repository_entry_failed_entry_suppleContents");
            return "error";
        }
        // リダイレクトURL
        $redirect_url = $item_type_result[0]['uri'];

        return $redirect_url;
    }
    // Add suppleContentsEntry Y.Yamazawa --end-- 2015/03/30 --end--
}
?>
