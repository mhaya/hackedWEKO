<?php
/**
 * View class for item export confirmation
 * アイテムエクスポート確認用ビュークラス
 *
 * @package WEKO
 */
// --------------------------------------------------------------------
//
// $Id: Detailconfirm.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * View class for item export confirmation
 * アイテムエクスポート確認用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Main_Export_DetailConfirm extends RepositoryAction
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
     * @var DbObject
     */
	var $Db = null;
    /**
     * Previous screen URL
     * 前画面URL
     *
     * @var unknown_type
     */
	var $prev_url = null;
    
    /**
     * Item export confirmation confirmation screen display
     * アイテムエクスポート確認確認画面表示
     *
     * @access  public
     */
    function execute()
    {
    	try {
    		//アクション初期化処理
	        $result = $this->initAction();
    		if ( $result === false ) {
	            $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
	            $DetailMsg = null;                              //詳細メッセージ文字列作成
	            //sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
	            $exception->setDetailMsg( $DetailMsg );             //詳細メッセージ設定
	            $this->failTrans();                                        //トランザクション失敗を設定(ROLLBACK)
	            $user_error_msg = 'initで既に・・・';
	            throw $exception;
	        }
    	
    		// 通貨単位設定
	    	$money_units = null;
	    	$money_units = $this->getMoneyUnit();
	    	if($money_units != null){
                // Mod change yen mark to html special char T.Koyasu 2014/07/31 --start--
                if(strpos($money_units['money_unit'], "\\") === 0){
                    $money_units['money_unit'] = "&yen;";
                }
                // Mod change yen mark to html special char T.Koyasu 2014/07/31 --end--
                
    			$this->Session->setParameter('money_unit', $money_units['money_unit']);
    			$this->Session->setParameter('money_unit_conf', $money_units['money_unit_conf']);
	    	} else {
	    		$this->Session->removeParameter('money_unit');
    			$this->Session->removeParameter('money_unit_conf');
	    	}
	    	
	    	if($this->prev_url != ""){
				$this->Session->setParameter("prev_page_uri", urlencode($this->prev_url));
			}
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
			$result = $this->exitAction();     //トランザクションが成功していればCOMMITされる
        
	        //異常終了
    	    return 'error';
		}
    }
}
?>
