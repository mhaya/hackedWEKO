<?php
/**
 * View class for the administrator account confirmation screen display
 * 管理者アカウント確認画面表示用ビュークラス
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
 * Harvest processing common classes
 * ハーベスト処理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHarvesting.class.php';

/**
 * View class for the administrator account confirmation screen display
 * 管理者アカウント確認画面表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Edit_AdminConf_Confirm extends RepositoryAction
{
	// component
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

	// request parmater
    /**
     * Administrator login ID
     * 管理者ログインID
     *
     * @var string
     */
	var $login_id = null;
	
    /**
     * Error message
     * エラーメッセージ
     *
     * @var string
     */
	var $error_msg = null;
	/**
	 * Action name
	 * アクション名
	 *
	 * @var string
	 */
	var $adminconfirm_action = null;	// action Name(sitemap, ranking, filecleanup, harvesting, usagestatistics, feedback)
	/**
	 * (Deprecated)
	 * (廃止予定)
	 *
	 * @var string
	 */
	public $is_create_data = null;
    /**
     * Harvest all item acquisition flag
     * ハーベスト全件取得フラグ
     *
     * @var boolean
     */
    public $harvesting_all_item_acquisition = null;
    
    /**
     * Harvesting warning repository
     * ハーベスト警告リポジトリ
     *
     * @var array[$ii]
     */
    public $harvestWarningRepos = array();
	
	/**
	 * To perform the specified action
	 * 指定されたアクションを実行する
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
				sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
				$exception->setDetailMsg( $DetailMsg );         //詳細メッセージ設定
				$this->failTrans();                             //トランザクション失敗を設定(ROLLBACK)
				throw $exception;
			}
			
			$this->Session->setParameter("redirect_flg", "admin");
			
            // Fix active tab check Y.Nakao 2012/03/22 --start--
            if( $this->adminconfirm_action == 'sitemap' || 
                $this->adminconfirm_action == 'ranking' || 
                $this->adminconfirm_action == 'filecleanup' || 
                $this->adminconfirm_action == 'usagestatistics' || 
                $this->adminconfirm_action == 'feedback' || 
                $this->adminconfirm_action == 'reconstructindexauth' || 
                $this->adminconfirm_action == 'reconstructsearch' ||
                $this->adminconfirm_action == 'externalsearchstopword' )
            {
                $this->Session->setParameter("admin_active_tab", 1);
            }
            else if($this->adminconfirm_action == 'harvesting')
            {
                $this->Session->setParameter("admin_active_tab", 2);
                
                // set flag executing harvest all item or finite difference
                if($this->harvesting_all_item_acquisition == null)
                {
                    $this->harvesting_all_item_acquisition = $this->Session->getParameter("harvesting_all_item_acquisition_flag");
                }
                if($this->harvesting_all_item_acquisition == null)
                {
                    $this->Session->setParameter("harvesting_all_item_acquisition_flag", false);
                }
                else if($this->harvesting_all_item_acquisition == true)
                {
                    $this->Session->setParameter("harvesting_all_item_acquisition_flag", true);
                }
                
                $this->harvestWarningRepos = array();
                $Harvesting = new RepositoryHarvesting($this->Session, $this->Db);
                $harvestingRepositories = array();
                $result = $Harvesting->getHarvestingTable($harvestingRepositories);
                foreach($harvestingRepositories as $repos)
                {
                    if(strlen($repos["repository_name"])==0 && strlen($repos["base_url"])==0)
                    {
                        continue;
                    }
                    
                    if(strlen($repos["base_url"])==0 || strlen($repos["post_index_id"])==0 || $repos["post_index_id"]==0)
                    {
                        $warningRepos = "";
                        if(strlen($repos["repository_name"])>0)
                        {
                            $warningRepos .= $repos["repository_name"];
                            if(strlen($repos["base_url"])>0)
                            {
                                $warningRepos .= "(".$repos["base_url"].")";
                            }
                        }
                        else if(strlen($repos["base_url"])>0)
                        {
                            $warningRepos .= $repos["base_url"];
                        }
                        if(strlen($warningRepos) > 0)
                        {
                            array_push($this->harvestWarningRepos, $warningRepos);
                        }
                    }
                }
            }
            // Fix active tab check Y.Nakao 2012/03/22 --end--
			
			// アクション終了処理
			$result = $this->exitAction();	// トランザクションが成功していればCOMMITされる
			
			if ( $result == false ){
				$exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
				$DetailMsg = null;                              //詳細メッセージ文字列作成
				sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
				$exception->setDetailMsg( $DetailMsg );         //詳細メッセージ設定
				$this->failTrans();                             //トランザクション失敗を設定(ROLLBACK)
				throw $exception;
			}
			$this->finalize();
			return 'success';
			
		} catch ( RepositoryException $Exception) {
			//エラーログ出力
			$this->logFile(
	        	"Repository_View_Edit_AdminConf_Confirm",		//クラス名
	        	"execute",									//メソッド名
			$Exception->getCode(),							//ログID
			$Exception->getMessage(),						//主メッセージ
			$Exception->getDetailMsg() );					//詳細メッセージ	      
			//アクション終了処理
			$this->exitAction();                   //トランザクションが失敗していればROLLBACKされる        
			//異常終了
			$this->Session->setParameter("error_msg", $user_error_msg);
			return "error";
		}
			return 'success';
	}
}
?>