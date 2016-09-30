<?php

/**
 * Item register: View of granting DOI
 * アイテム登録：DOI付与画面表示
 *
 * @package     WEKO
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
require_once WEBAPP_DIR. '/modules/repository/components/common/WekoAction.class.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Check grant doi business class
 * DOI付与チェックビジネスクラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/doi/Checkdoi.class.php';

/**
 * Item register: View of granting DOI
 * アイテム登録：DOI付与画面表示
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Main_Item_Editdoi extends WekoAction
{
    // 表示用パラメーター
    /**
     * Display help icon flag
     * ヘルプアイコン表示フラグ
     *
     * @var string
     */
    public $help_icon_display =  "";
    
    /**
     * Display DOI flag
     * DOI表示フラグ
     *
     * @var bool
     */
    public $displays_doi_grant = false;
    
    /**
     * DOI status
     * DOIステータス
     *
     * @var int
     */
    public $doi_status = 0;
    
    /**
     * registered DOI(0:not registed, 1:jalcdoi registed, 2:crossref registed, 3:datacite registed)
     * 登録されたDOI（0：登録されていない、1：jalc DOI, 2：CrossRef、3：DataCite）
     *
     * @var int
     */
    public $registed_doi = 0;
    
    /**
     * Registed library jalcdoi(0:not registered,1:registered)
     * 図書館DOI(0: 未発番, 1: 発番済)
     *
     * @var int
     */
    public $registed_library_jalcdoi = 0;
    
    /**
     * prefix of JaLC DOI
     * JaLC DOI プレフィックス
     *
     * @var string
     */
    public $prefix_jalcdoi = null;
    
    /**
     * prefix of Cross Ref
     * CrossRef DOI プレフィックス
     *
     * @var string
     */
    public $prefix_crossref = null;
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * prefix of DataCite
     * DataCite DOI プレフィックス
     *
     * @var string
     */
    public $prefix_datacite = null;
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    
    /**
     * prefix of Library JaLC DOI
     * 図書館DOI プレフィックス
     *
     * @var string
     */
    public $prefix_library_jalcdoi = null;
    
    /**
     * suffix
     * サフィックス
     *
     * @var string
     */
    public $suffix = null;
    
    /**
     * drop doi flag
     * DOI取り下げフラグ
     *
     * @var string
     */
    public $drop_doi = null;
    
    /**
     * edit flag(0:new item,1:edit item)
     * 編集フラグ(0: 新規, 1: 既存編集)
     *
     * @var string
     */
    public $edit_flag = 0;
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * prefix add flag(0:do not add, 1:add)
     * プレフィックス追加フラグ(0: 未追加, 1: 追加)
     */
    public $prefix_flag = null;
    
    /**
     * prefix of YHandle
     * Yハンドルプレフィックス
     *
     * @var string
     */
    public $prefix_yhandle = null;
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    
    // リクエストパラメーター
    /**
     * Warning message
     * 警告メッセージ配列
     *
     * @var array
     */
    public $warningMsg = null;

    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws AppException
     */
    protected function executeApp()
    {
        // RepositoryActionのインスタンス
        $repositoryAction = new RepositoryAction();
        $repositoryAction->Session = $this->Session;
        $repositoryAction->Db = $this->Db;
        $repositoryAction->dbAccess = $this->Db;
        $repositoryAction->TransStartDate = $this->accessDate;
        $repositoryAction->setLangResource();
        $smartyAssign = $this->Session->getParameter("smartyAssign");
        
        $item_id = intval($this->Session->getParameter("edit_item_id"));
        $item_no = intval($this->Session->getParameter("edit_item_no"));
        
        // DOI付与可能かどうかをチェック
        $CheckDoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
        require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
        $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->Db, $this->accessDate);
        if($this->drop_doi == 'true')
        {
            $repositoryHandleManager->dropDoiSuffix($item_id, $item_no);
        }
        $displays_jalcdoi_grant = $CheckDoi->checkDoiGrant(
            $item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_JALC_DOI, null);
        $displays_crossref_grant = $CheckDoi->checkDoiGrant(
            $item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_CROSS_REF, null);
        $displays_datacite_grant = $CheckDoi->checkDoiGrant(
            $item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_DATACITE, null);
        $displays_library_jalcdoi_grant = $CheckDoi->checkDoiGrant(
            $item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_LIBRARY_JALC_DOI, null);
        if($displays_jalcdoi_grant->isGrantDoi)
        {
            $this->prefix_jalcdoi = $repositoryHandleManager->getJalcDoiPrefix();
            $this->prefix_yhandle = $repositoryHandleManager->getYHandlePrefix();
            $this->suffix = $repositoryHandleManager->getYHandleSuffix($item_id, $item_no);
            $this->displays_doi_grant = true;
        }
        if($displays_crossref_grant->isGrantDoi)
        {
            $this->prefix_crossref = $repositoryHandleManager->getCrossRefPrefix();
            $this->prefix_yhandle = $repositoryHandleManager->getYHandlePrefix();
            $this->suffix = $repositoryHandleManager->getYHandleSuffix($item_id, $item_no);
            $this->displays_doi_grant = true;
        }
        if($displays_datacite_grant->isGrantDoi)
        {
            $this->prefix_datacite = $repositoryHandleManager->getDataCitePrefix();
            $this->prefix_yhandle = $repositoryHandleManager->getYHandlePrefix();
            $this->suffix = $repositoryHandleManager->getYHandleSuffix($item_id, $item_no);
            $this->displays_doi_grant = true;
        }
        if($displays_library_jalcdoi_grant->isGrantDoi)
        {
            $this->prefix_library_jalcdoi = $repositoryHandleManager->getLibraryJalcDoiPrefix();
            $this->prefix_yhandle = $repositoryHandleManager->getYHandlePrefix();
            $this->suffix = $repositoryHandleManager->getYHandleSuffix($item_id, $item_no);
            $this->displays_doi_grant = true;
        }
        
        $query = "SELECT param_value ".
                 "FROM {repository_parameter} ".
                 "WHERE `param_name` = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = "prefix_flag";
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if(count($result) > 0 && $result[0]["param_value"] == 1)
        {
        	$this->prefix_flag = 1;
        }
        else
        {
        	$this->prefix_flag = 0;
        }
        
        $this->doi_status = $CheckDoi->getDoiStatus($item_id, $item_no);
        if($this->doi_status == 1)
        {
            $jalcdoi_suffix = $repositoryHandleManager->getJalcdoiSuffix($item_id, $item_no);
            $crossref_suffix = $repositoryHandleManager->getCrossrefSuffix($item_id, $item_no);
            $datacite_suffix = $repositoryHandleManager->getDataciteSuffix($item_id, $item_no);
            if(strlen($jalcdoi_suffix) > 0 && strlen($crossref_suffix) < 1 && strlen($datacite_suffix) < 1)
            {
                $this->registed_doi = 1;
            }
            else if(strlen($crossref_suffix) > 0 && strlen($jalcdoi_suffix) < 1 && strlen($datacite_suffix) < 1)
            {
                $this->registed_doi = 2;
            }
            else if(strlen($datacite_suffix) > 0 && strlen($jalcdoi_suffix) < 1 && strlen($crossref_suffix) < 1)
            {
                $this->registed_doi = 3;
            }
            
            $library_jalcdoi_suffix = $repositoryHandleManager->getLibraryJalcdoiSuffix($item_id, $item_no);
            if(strlen($library_jalcdoi_suffix) > 0)
            {
                $this->registed_library_jalcdoi = 1;
            }
            else
            {
                $this->registed_library_jalcdoi = 0;
            }
        }
        
        $uri = $repositoryHandleManager->getSubstanceUri($item_id, $item_no);
        if(strlen($uri) == 0)
        {
            $this->edit_flag = 0;
        }
        else
        {
            $this->edit_flag = 1;
        }
        
        // DOI付与画面を通過したことを示すフラグをセッションに保存
        $this->Session->removeParameter("edit_jalc_flag");
        $this->Session->setParameter("edit_jalc_flag", "true");
        
        // Set help icon setting
        $tmpErrorMsg = "";
        $result = $repositoryAction->getAdminParam('help_icon_display', $this->help_icon_display, $tmpErrorMsg);
        if ( $result === false ){
            $this->errorLog($tmpErrorMsg, __FILE__, __CLASS__, __LINE__);
            $exception = new AppException($tmpErrorMsg);
            $exception->addError($tmpErrorMsg);
            throw $exception;
        }
        
        return 'success';
    }
}
?>
