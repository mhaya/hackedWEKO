<?php

/**
 * Action class for grant DOI
 * アイテム登録：DOI付与画面からの入力処理アクション
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Editdoi.class.php 70936 2016-08-09 09:53:57Z keiya_sugimoto $
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
 * Handle manager class
 * ハンドル管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
/**
 * Item register manager class
 * アイテム登録管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/ItemRegister.class.php';
/**
 * Const for WEKO class
 * WEKO用定数クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryConst.class.php';

/**
 * Action class for grant DOI
 * アイテム登録：DOI付与画面からの入力処理アクション
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Main_Item_Editdoi extends RepositoryAction
{
    // リクエストパラメーター
    /**
     * Process mode
     * 処理モード
     *   'selecttype'   : アイテムタイプ選択画面
     *   'files'        : ファイル選択画面
     *   'texts'        : メタデータ入力画面
     *   'links'        : リンク設定画面
     *   'doi'          : DOI設定画面
     *   'confirm'      : 確認画面
     *   'stay'         : save
     *   'next'         : go next page
     * @var string
     */
    public $save_mode = null;
    
    /**
     * JaLC DOI checkbox parameter
     * JaLC DOIチェック情報
     *
     * @var string
     */
    public $entry_jalcdoi_checkbox = null;
    /**
     * JaLC DOI free input parameter
     * JaLC DOI自由入力情報
     *
     * @var string
     */
    public $entry_jalcdoi_text = null;
    /**
     * JaLC DOI hidden parameter
     * JaLC DOI URI
     *
     * @var string
     */
    public $entry_jalcdoi_hidden = null;
    /**
     * Cross Ref checkbox parameter
     * Cross Ref チェック情報
     *
     * @var string
     */
    public $entry_crossref_checkbox = null;
    /**
     * Cross Ref free input parameter
     * Cross Ref自由入力情報
     *
     * @var string
     */
    public $entry_crossref_text = null;
    /**
     * Cross Ref hidden parameter
     * Cross Ref URI
     *
     * @var string
     */
    public $entry_crossref_hidden = null;
    /**
     * DataCite checkbox parameter
     * DataCite チェック情報
     *
     * @var string
     */
    public $entry_datacite_checkbox = null;
    /**
     * DataCite free input parameter
     * DataCite自由入力情報
     *
     * @var string
     */
    public $entry_datacite_text = null;
    /**
     * DateCite hidden parameter
     * DataCite URI
     *
     * @var string
     */
    public $entry_datacite_hidden = null;
    /**
     * Library JaLC DOI text parameter
     * Library JaLC DOI 入力値
     * @var string
     */
    public $entry_library_jalcdoi_text = null;
    
    /**
     * Library JaLC DOI hidden parameter
     * Library JaLC DOI URI
     * @var string
     */
    public $entry_library_jalcdoi_hidden = null;
    
    // メンバ変数
    /**
     * Warning message
     * 警告メッセージ
     *
     * @var array
     */
    private $warningMsg = array();
    
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     */
    public function executeApp()
    {
        // セッション情報取得
        $edit_flag = $this->Session->getParameter("edit_flag");
        if($edit_flag == 0){
            // 新規登録時
            $ItemRegister = new ItemRegister($this->Session, $this->Db);
            $base_attr = $this->Session->getParameter("base_attr");
            $item_type_all = $this->Session->getParameter("item_type_all");
            $item_id = intval($this->Db->nextSeq("repository_item"));
            $item_no = 1;
            if($base_attr["language"] == RepositoryConst::ITEM_LANG_JA)
            {
                //WEKOの設定言語に依存せず、論文の言語で決めるので仮タイトル文字列はベタ書きです
                $base_attr["title"] = "タイトル無し";
                $base_attr["title_english"] = "";
            }
            else
            {
                $base_attr["title"] = "";
                $base_attr["title_english"] = "no title";
            }
            // アイテムの雛形を作成する
            $ItemRegister->entryItemModel($item_id, $item_no, $item_type_all["item_type_id"], $base_attr["title"], $base_attr["title_english"], $base_attr["language"]);
            
            $this->Session->setParameter("base_attr", $base_attr);
            $this->Session->setParameter("edit_item_id", $item_id);
            $this->Session->setParameter("edit_item_no", $item_no);
            $this->Session->setParameter("edit_flag", 1);
            $edit_flag = 1;
        } else if($edit_flag == 1){
            //既存編集時
            // 編集中のアイテムIDをセッションから取得
            $item_id = intval($this->Session->getParameter("edit_item_id"));
            $item_no = intval($this->Session->getParameter("edit_item_no"));
        }
        
        // 付与するDOI suffixをセッションに保存
        $suffix = $this->preserveEntryingSuffix($item_id, $item_no);
        
        // 指定遷移先へ遷移可能かチェック＆遷移先の決定
        $this->infoLog("Get instance: businessItemedittranscheck", __FILE__, __CLASS__, __LINE__);
        $transCheck = BusinessFactory::getFactory()->getBusiness("businessItemedittranscheck");
        $transCheck->setData(   "doi",
                                $this->save_mode,
                                $this->Session->getParameter("isfile"),
                                $this->Session->getParameter("doi_itemtype_flag"),
                                $this->Session->getParameter("base_attr"),
                                $this->Session->getParameter("item_pub_date"),
                                $this->Session->getParameter("item_attr_type"),
                                $this->Session->getParameter("item_attr"),
                                $this->Session->getParameter("item_num_attr"),
                                $this->Session->getParameter("indice"),
                                $this->Session->getParameter("edit_item_id"),
                                $this->Session->getParameter("edit_item_no")
        );
        $ret = $transCheck->getDestination();
        foreach($transCheck->getErrorMsg() as $msg){
            $this->addErrMsg($msg);
        }
        $this->warningMsg = array_merge($this->warningMsg, $transCheck->getWarningMsg());
        
        // warningをViewに渡す処理
        if(count($this->warningMsg) > 0){
            $container =& DIContainerFactory::getContainer();
            $request =& $container->getComponent("Request");
            $request->setParameter("warningMsg", $this->warningMsg);
        }
        
        $this->Session->removeParameter("doi_suffix");
        $this->Session->removeParameter("doi_suffix_type");
        // DOI付与チェック後、問題なければDOI付与
        if(!isset($this->errMsg) || count($this->errMsg) == 0)
        {
            $this->entryDoi($item_id, $item_no, $suffix);
        }
        
        return $ret;
    }
    
    /**
     * Preserve entrying DOI suffix to session
     * セッションに付与するDOI suffixを保存する
     * 
     * @param int $item_id Item ID アイテムID
     * @param int $item_no Item No アイテム通番
     * @return string DOI suffix preserved to session セッションに保存したDOI suffix
     */
    private function preserveEntryingSuffix($item_id, $item_no)
    {
        $suffix = null;
        $suffixType = null;
        
        require_once WEBAPP_DIR. '/modules/repository/components/business/doi/Checkdoi.class.php';
        
        // インスタンス作成
        $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->Db, $this->accessDate);
        
        if(isset($this->entry_library_jalcdoi_text) && strlen($this->entry_library_jalcdoi_text) > 0)
        {
            $suffix = $this->entry_library_jalcdoi_text;
            $suffixType = Repository_Components_Business_Doi_Checkdoi::TYPE_LIBRARY_JALC_DOI;
        }
        else if(isset($this->entry_jalcdoi_checkbox) && strlen($this->entry_jalcdoi_checkbox) > 0)
        {
            if(!defined("_REPOSITORY_WEKO_DOISUFFIX_FREE") || !_REPOSITORY_WEKO_DOISUFFIX_FREE)
            {
                $suffix = $repositoryHandleManager->getYHandleSuffix($item_id, $item_no);
            }
            else
            {
                $suffix = $this->entry_jalcdoi_text;
            }
            $suffixType = Repository_Components_Business_Doi_Checkdoi::TYPE_JALC_DOI;
        }
        else if(isset($this->entry_crossref_checkbox) && strlen($this->entry_crossref_checkbox) > 0)
        {
            if(!defined("_REPOSITORY_WEKO_DOISUFFIX_FREE") || !_REPOSITORY_WEKO_DOISUFFIX_FREE)
            {
                $suffix = $repositoryHandleManager->getYHandleSuffix($item_id, $item_no);
            }
            else
            {
                $suffix = $this->entry_crossref_text;
            }
            $suffixType = Repository_Components_Business_Doi_Checkdoi::TYPE_CROSS_REF;
        }
        else if(isset($this->entry_datacite_checkbox) && strlen($this->entry_datacite_checkbox) > 0)
        {
            if(!defined("_REPOSITORY_WEKO_DOISUFFIX_FREE") || !_REPOSITORY_WEKO_DOISUFFIX_FREE)
            {
                $suffix = $repositoryHandleManager->getYHandleSuffix($item_id, $item_no);
            }
            else
            {
                $suffix = $this->entry_datacite_text;
            }
            $suffixType = Repository_Components_Business_Doi_Checkdoi::TYPE_DATACITE;
        }
        
        $this->Session->setParameter("doi_suffix", $suffix);
        $this->Session->setParameter("doi_suffix_type", $suffixType);
        
        return $suffix;
    }
    
    /**
     * Entry DOI
     * DOIを付与する
     * 
     * @param int $item_id Item ID アイテムID
     * @param int $item_no Item No アイテム通番
     * @param string $suffix DOI suffix preserved to session セッションに保存したDOI suffix
     */
    private function entryDoi($item_id, $item_no, $suffix)
    {
        if(strlen($suffix) > 0)
        {
            // インスタンス作成
            $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->Db, $this->accessDate);
            // Add Library JaLC DOI
            if(isset($this->entry_library_jalcdoi_text) && strlen($this->entry_library_jalcdoi_text) > 0)
            {
                $repositoryHandleManager->registLibraryJalcdoiSuffix($item_id, $item_no, $suffix);
            }
            // Add JaLC DOI
            else if(isset($this->entry_jalcdoi_checkbox) && strlen($this->entry_jalcdoi_checkbox) > 0)
            {
                $repositoryHandleManager->registJalcdoiSuffix($item_id, $item_no, $suffix);
            }
            // Add Cross Ref
            else if(isset($this->entry_crossref_checkbox) && strlen($this->entry_crossref_checkbox) > 0)
            {
                $repositoryHandleManager->registCrossrefSuffix($item_id, $item_no, $suffix);
            }
            // Add DataCite 2015/02/09 K.Sugimoto --start--
            // Add DataCite
            else if(isset($this->entry_datacite_checkbox) && strlen($this->entry_datacite_checkbox) > 0)
            {
                $repositoryHandleManager->registDataciteSuffix($item_id, $item_no, $suffix);
            }
            // Add DataCite 2015/02/09 K.Sugimoto --end--
        }
    }
}
?>
