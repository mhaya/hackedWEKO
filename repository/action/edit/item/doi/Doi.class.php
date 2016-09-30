<?php

/**
 * DOI bulk granted class action
 * DOI一括付与クラスアクション
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Doi.class.php 70936 2016-08-09 09:53:57Z keiya_sugimoto $
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
 * Handle management common classes
 * ハンドル管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
/**
 * Common classes for creating and updating the search table that holds the metadata and file contents of each item to search speed improvement
 * 検索速度向上のためアイテム毎のメタデータおよびファイル内容を保持する検索テーブルの作成・更新を行う共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositorySearchTableProcessing.class.php';
/**
 * Check grant doi class
 * DOI付与チェックビジネスクラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/doi/Checkdoi.class.php';

/**
 * DOI bulk granted class action
 * DOI一括付与クラスアクション
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Item_Doi extends RepositoryAction
{
    // *********************
    // リクエストパラメータ
    // *********************
    // 選択インデックスID
    /**
     * Select index id
     * 選択インデックスID
     *
     * @var int
     */
    public $targetIndexId = null;
    // JaLC DOI付与アイテムリスト
    /**
     * JaLC DOI grant item list
     * JaLC DOI付与アイテムリスト
     *
     * @var array[$ii]
     */
    public $registing_jalcdoi_items = null;
    /**
     * CrossRef DOI grant item list
     * CrossRef DOI付与アイテムリスト
     *
     * @var array[$ii]
     */
    public $registing_crossref_items = null;
    // Add DataCite 2015/02/09 K.Sugimoto --start--
    /**
     * DataCite DOI grant item list
     * DataCite DOI付与アイテムリスト
     *
     * @var array[$ii]
     */
    public $registing_datacite_items = null;
    // Add DataCite 2015/02/09 K.Sugimoto --end--
    
    /**
     * DOI bulk grant tab ID
     * DOI一括付与タブID
     *
     * @var int
     */
    const JALC_DOI_MANAGEMENT_TAB_NUMBER = 3;
    
    /**
     * DOI bulk grant
     * DOI一括付与
     *
     * @return string Result 結果
     */
    public function executeApp()
    {
        // 選択タブの保存
        $this->Session->setParameter("item_setting_active_tab", self::JALC_DOI_MANAGEMENT_TAB_NUMBER);
        
        // 引数チェック //
        if( $this->targetIndexId == null || $this->targetIndexId == "" ){
            $this->Session->setParameter("error_msg", "Select Index Error.");
            return 'error';
        }
        //セッションクリア
        $this->Session->removeParameter("targetIndexId");
        $this->Session->removeParameter("searchIndexId");
        
        $this->Session->setParameter("targetIndexId", $this->targetIndexId);
        $this->Session->setParameter("searchIndexId", $this->targetIndexId);
        
        $CheckDoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
        
        $doi_count = 0;
        
        if(count($this->registing_jalcdoi_items) > 0)
        {
            $item_no = 1;
            $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->dbAccess, $this->TransStartDate);
            $repositorySearchTableProcessing = new RepositorySearchTableProcessing($this->Session, $this->Db);
            foreach($this->registing_jalcdoi_items as $item_id)
            {
                $suffix = $repositoryHandleManager->getYHandleSuffix($item_id, $item_no);
                $resultCheckDoi = $CheckDoi->checkDoiGrant($item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_JALC_DOI, $suffix, Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_ITEM_MANAGEMENT);
                if($resultCheckDoi->isGrantDoi)
                {
                    $repositoryHandleManager->registJalcdoiSuffix($item_id, $item_no, $suffix);
                    $repositorySearchTableProcessing->updateSelfDoiSearchTable($item_id, $item_no);
                    $this->updateModDate($item_id, $item_no);
                    $doi_count++;
                }
                else
                {
                    $this->outputErrorDoi($item_id, $item_no, $resultCheckDoi);
                    return 'error';
                }
            }
        }
        if(count($this->registing_crossref_items) > 0)
        {
            $item_no = 1;
            $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->dbAccess, $this->TransStartDate);
            $repositorySearchTableProcessing = new RepositorySearchTableProcessing($this->Session, $this->Db);
            foreach($this->registing_crossref_items as $item_id)
            {
                $suffix = $repositoryHandleManager->getYHandleSuffix($item_id, $item_no);
                $resultCheckDoi = $CheckDoi->checkDoiGrant($item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_CROSS_REF, $suffix, Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_ITEM_MANAGEMENT);
                if($resultCheckDoi->isGrantDoi)
                {
                    $repositoryHandleManager->registCrossrefSuffix($item_id, $item_no, $suffix);
                    $repositorySearchTableProcessing->updateSelfDoiSearchTable($item_id, $item_no);
                    $this->updateModDate($item_id, $item_no);
                    $doi_count++;
                }
                else
                {
                    $this->outputErrorDoi($item_id, $item_no, $resultCheckDoi);
                    return 'error';
                }
            }
        }
        // Add DataCite 2015/02/09 K.Sugimoto --start--
        if(count($this->registing_datacite_items) > 0)
        {
            $item_no = 1;
            $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->dbAccess, $this->TransStartDate);
            $repositorySearchTableProcessing = new RepositorySearchTableProcessing($this->Session, $this->Db);
            foreach($this->registing_datacite_items as $item_id)
            {
                $suffix = $repositoryHandleManager->getYHandleSuffix($item_id, $item_no);
                $resultCheckDoi = $CheckDoi->checkDoiGrant($item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_DATACITE, $suffix, Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_ITEM_MANAGEMENT);
                if($resultCheckDoi->isGrantDoi)
                {
                    $repositoryHandleManager->registDataciteSuffix($item_id, $item_no, $suffix);
                    $repositorySearchTableProcessing->updateSelfDoiSearchTable($item_id, $item_no);
                    $this->updateModDate($item_id, $item_no);
                    $doi_count++;
                }
                else
                {
                    $this->outputErrorDoi($item_id, $item_no, $resultCheckDoi);
                    return 'error';
                }
            }
        }
        
        $this->Session->setParameter("doi_count", $doi_count);
        // Add DataCite 2015/02/09 K.Sugimoto --end--
        
        // エラーメッセージ開放
        $this->Session->removeParameter("error_msg");
        return 'success';
    }
    
    /**
     * Update of the renewal date
     * 更新日の更新
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     */
    private function updateModDate($item_id, $item_no)
    {
        $query = "UPDATE ". DATABASE_PREFIX ."repository_item ".
                 "SET mod_date = ? ".
                 "WHERE item_id = ? ".
                 "AND item_no = ?; ";
        $params = null;
        $params[] = $this->TransStartDate;         // mod_date
        $params[] = $item_id;                   // item_id
        $params[] = $item_no;                   // item_no
        //UPDATE
        $result = $this->dbAccess->executeQuery($query, $params);              
    }

    /**
     * Output error message about DOI
     * DOIに関するエラーメッセージを出力する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function outputErrorDoi($item_id, $item_no, $resultCheckDoi)
    {
        $ConvertResultCheckDoi = BusinessFactory::getFactory()->getBusiness("businessConvertresultcheckdoi");
        $errMsg = $ConvertResultCheckDoi->chooseDoiErrMsg($item_id, $item_no, $resultCheckDoi);
        foreach($errMsg as $msg){
            $this->addErrMsg($msg);
        }
    }

}
?>
