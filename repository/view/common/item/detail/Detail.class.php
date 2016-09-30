<?php

/**
 * View class for item detail pop-up display
 * アイテム詳細ポップアップ表示用ビュークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Detail.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Name authority class
 * 氏名メタデータクラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/NameAuthority.class.php';
// Add smartPhone support T.Koyasu 2012/04/10 -start-
/**
 * Item view class
 * アイテム詳細画面表示クラス
 */
require_once WEBAPP_DIR. '/modules/repository/view/main/item/detail/Detail.class.php';
// Add smartPhone support T.Koyasu 2012/04/10 -end-

/**
 * View class for item detail pop-up display
 * アイテム詳細ポップアップ表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Common_Item_Detail extends RepositoryAction
{
	// リクエストパラメタ
    /**
     * Item ID
     * アイテムID
     * 
     * @var int
     */
	var $item_id = null;			// アイテムID
    /**
     * Item number
     * アイテム通番
     * 
     * @var int
     */
	var $item_no = null;			// アイテム通番
    /**
     * review flag
     * 査読フラグ
     * 
     * @var bool
     */
	var $file_flg = null;			// ワークフローならnull,査読ならtrue
    /**
     * Has file flag
     * ファイル所持フラグ
     * 
     * @var bool
     */
	var $IsFile = "false";				// ファイルを保持しているか否か(2009/02/17 A.Suzuki)
    /**
     * Had detail view file flag
     * 詳細表示ファイル所持フラグ
     * 
     * @var bool
     */
	var $IsFileView = "false";			// 詳細表示で表示するファイルがあるか否か(2009/11/24 A.Suzuki)
    /**
     * Has simple view file flag
     * 簡易表示ファイル所持フラグ
     * 
     * @var array
     */
	var $IsFileSimpleView = array();	// 簡易表示で表示するファイルがあるか否か(2009/12/14 A.Suzuki)
    /**
     * Has FLASH file flag
     * FLASHファイル所持フラグ
     * 
     * @var bool
     */
	var $IsFlashView = "false";		// FLASH表示で表示するファイルがあるか否か(2010/01/19 A.Suzuki)
	
	// For flash annotation
    /**
     * Encode base URL
     * エンコード済ベースURL
     *
     * @var string
     */
    var $encode_baseurl = "";
    /**
     * annotea user
     * annotea user
     *
     * @var string
     */
    var $annoteaUser = "";
    
    // Bugfix close contents data shown 2011/06/14 Y.Nakao --start--
    // components
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    public $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObjectAdodb
     */
    public $Db = null;
    /**
     * Workflow error message
     * ワークフローでのエラーメッセージ
     *
     * @var string
     */
    public $workflow_error = "";
    // Bugfix close contents data shown 2011/06/14 Y.Nakao --end--
    
    // Add smartPhone support T.Koyasu 2012/04/10 -start-
    /**
     * iPhone access flag
     * iPhoneからのアクセスフラグ
     *
     * @var bool
     */
    public $iPhoneFlg = false;
    /**
     * Get data component
     * データ取得コンポーネント
     *
     * @var object
     */
    public $getData = null;
    /**
     * Module block ID
     * モジュールブロックID
     *
     * @var int
     */
    public $block_id = null;
    /**
     * NC2 main class
     * NC2汎用クラス
     *
     * @var Common_Main
     */
    public $commonMain = null;
    /**
     * Languages view object
     * 言語情報オブジェクト
     *
     * @var object
     */
    public $languagesView = null;
    /**
     * review status
     * 査読状態
     *
     * @var string
     */
    public $review_status = "";
    /**
     * review reject status
     * 査読却下状態
     *
     * @var string
     */
    public $reject_status = "";
    /**
     * Shown status
     * 公開状態
     *
     * @var string
     */
    public $shown_status = "";
    // Add smartPhone support T.Koyasu 2012/04/10 -end-
    
    // Add multimedia support 2012/08/27 T.Koyasu -start-
    /**
     * Mulit media file flag
     * マルチメディアファイルの存在フラグ
     *
     * @var bool
     */
    public $IsMultimediaView = false;
    /**
     * Contents type
     * コンテンツタイプ
     *
     * @var string
     */
    public $contentsType = "";
    // Add multimedia support 2012/08/27 T.Koyasu -end-
    /**
     * Error message
     * エラーメッセージ
     *
     * @var array
     */
    public $errMsg = array();
    
    // Get reference data 2013/10/21 S.Suzuki --start--
    /**
     * File name
     * ファイル名
     *
     * @var array
     */
    public $fileName = array();
    /**
     * File download URL
     * ファイルダウンロードURL
     *
     * @var array
     */
    public $fileURL = array();
    // Get reference data 2013/10/21 S.Suzuki --end--
    
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     */
    function execute()
    {
    	try {
	        $NameAuthority = new NameAuthority($this->Session, $this->Db);
	        
	        // For flash annotation
	        $this->encode_baseurl = urlencode(BASE_URL);
            if($this->Session->getParameter("_handle") != null){
                $this->annoteaUser = $this->Session->getParameter("_handle");
            }

	    	// 初期設定
	    	$this->IsSelectPublish = "false";
			$this->IsSelectDelete = "false";
			$this->IsSelectEdit = "false";
			
	        // Bugfix close contents data shown 2011/06/14 Y.Nakao --start--
	        $smartyAssign = $this->Session->getParameter("smartyAssign");
	        if($smartyAssign == null){
	             $container =& DIContainerFactory::getContainer();
                $filterChain =& $container->getComponent("FilterChain");
                $smartyAssign =& $filterChain->getFilterByName("SmartyAssign");
	        }
	        // initialize display data
            $this->Session->removeParameter("item_info");
            $this->Session->removeParameter("position_index");
            $this->Session->removeParameter("oaipmh_uri");
            $this->Session->removeParameter("bibtex_uri");
            $this->Session->removeParameter("swrc_uri");
            // Add iiif presentation api support mhaya 2016/08/25
            $this->Session->removeParameter("iiif_uri");           
 
            // Add smartPhone support T.Koyasu 2012/04/09 -start-
            $mainDetail = new Repository_View_Main_Item_Detail($this->Session, $this->Db, $this->item_id, $this->item_no, $this->getData, $this->languagesView, $this->block_id, $this->commonMain, true);

            $result = $mainDetail->execute();
            
            if(is_numeric(strpos($result, 'error'))){
                if($this->smartphoneFlg)
                {
                    return 'error_sp';
                } else {
                    return 'error';
                }
            }
            
            // ワークフローによる詳細表示を示す
            $this->Session->setParameter("workflow_flg", true);
            
            // check state
            $detailInfo = $mainDetail->detail_info;
            
            if(preg_match('/not_access|login/', $detailInfo) > 0){
                $this->workflow_error = "close";
                
                if($this->smartphoneFlg){
                    return 'error_sp';
                } else {
                    return 'error';
                }
            } else if(is_numeric(strpos($detailInfo, 'del_item'))){
                $this->workflow_error = "delete";
                
                if($this->smartphoneFlg){
                    return 'error_sp';
                } else {
                    return 'error';
                }
            }
            
            // hidden supple add button
            // can not add supple mental contents in workflow
            $this->Session->setParameter("IsSuppleAdd", "false");
            
            $this->IsFile = $mainDetail->IsFile;
            $this->IsFileView = $mainDetail->IsFileView;
            $this->IsFileSimpleView = $mainDetail->IsFileSimpleView;
            $this->IsFlashView = $mainDetail->IsFlashView;
            $this->reject_status = $mainDetail->reject_status;
            $this->review_status = $mainDetail->review_status;
            $this->shown_status = $mainDetail->shown_status;
            $this->iPhoneFlg = $mainDetail->iPhoneFlg;
            // Add smartPhone support T.Koyasu 2012/04/09 -end-
            // Add multimedia support 2012/08/27 T.Koyasu -start-
            $this->IsMultimediaView = $mainDetail->IsMultimediaView;
            $this->contentsTypeList = $mainDetail->contentsTypeList;
            // Add multimedia support 2012/08/27 T.Koyasu -start-
	        
	        $this->Session->removeParameter("error_msg");
	        $this->Session->setParameter("error_msg", null);

            // Add smartPhone support T.Koyasu 2012/04/10 -start-
            if($this->smartphoneFlg){
                return 'success_sp';
            } else {
                return 'success';
            }
            // Add smartPhone support T.Koyasu 2012/04/10 -end-
    	 } catch ( RepositoryException $Exception) {
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
    	    
            // Add smartPhone support T.Koyasu 2012/04/10 -start-
            if($this->smartphoneFlg){
                return "error_sp";
            } else {
                return "error";
            }
            // Add smartPhone support T.Koyasu 2012/04/10 -end-
		}
    }
}
?>
