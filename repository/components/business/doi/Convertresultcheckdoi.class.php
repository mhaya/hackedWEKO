<?php

/**
 * Convert result of check grant DOI business class
 * DOI付与チェック結果変換ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Convertresultcheckdoi.class.php 70857 2016-08-08 11:04:08Z tomohiro_ichikawa $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';

/**
 * Convert result of check grant DOI business class
 * DOI付与チェック結果変換ビジネスクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Doi_Convertresultcheckdoi extends BusinessBase
{
    /**
     * Choose the DOI grant error message to output
     * 出力するDOI付与エラーメッセージを選択する
     * 
     * @param int $item_id Item ID アイテムID
     * @param int $item_no Item No アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     * @return array Error message エラーメッセージ
     *               array[$ii]
     */
    public function chooseDoiErrMsg($item_id, $item_no, $resultCheckDoi){
        $errMsg = array();
        
        $CheckDoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
        $item_type_id = $CheckDoi->getItemTypeId($item_id, $item_no);
        $item_type_name = $this->getItemTypeName($item_type_id);
        $nii_type = $CheckDoi->getNiiType($item_type_id);
        
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        $smarty_assign = $session->getParameter("smartyAssign");
        if(isset($resultCheckDoi->isSetYhandlePrefix) && !$resultCheckDoi->isSetYhandlePrefix)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_yhandle_prefix");
            $errMsg[] = $smarty_assign->getLang("repository_error_contact_administrator");
        }
        else if(isset($resultCheckDoi->isAllowDoi) && !$resultCheckDoi->isAllowDoi)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_allow_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_contact_administrator");
        }
        else if(isset($resultCheckDoi->isSetDoiPrefix) && !$resultCheckDoi->isSetDoiPrefix)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_doi_prefix");
            $errMsg[] = $smarty_assign->getLang("repository_error_contact_administrator");
        }
        else if(isset($resultCheckDoi->isNotReviewItem) && !$resultCheckDoi->isNotReviewItem)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_review_item");
            $errMsg[] = $smarty_assign->getLang("repository_error_contact_administrator");
        }
        else if(isset($resultCheckDoi->isPublicItem) && !$resultCheckDoi->isPublicItem)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_public_item");
            $errMsg[] = $smarty_assign->getLang("repository_error_contact_administrator");
        }
        else if(isset($resultCheckDoi->isSetNiiType) && !$resultCheckDoi->isSetNiiType)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_grant_doi_itemtype");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_nii_type");
            $errMsg[] = $smarty_assign->getLang("repository_error_name_itemtype").
                        $item_type_name;
            $errMsg[] = $smarty_assign->getLang("repository_error_contact_administrator");
        }
        else if(isset($resultCheckDoi->isSetItemTypeMapping) && !$resultCheckDoi->isSetItemTypeMapping)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_grant_doi_itemtype");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_itemtype_mapping");
            $errMsg[] = $smarty_assign->getLang("repository_error_name_itemtype").
                        $item_type_name."<".$nii_type.">";
            $errMsg[] = sprintf($smarty_assign->getLang("repository_error_show_help"), BASE_URL);
            $errMsg[] = $smarty_assign->getLang("repository_error_contact_administrator");
        }
        else if(isset($resultCheckDoi->isSetMetadata) && !$resultCheckDoi->isSetMetadata)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_metadata");
            $errMsg[] = $smarty_assign->getLang("repository_error_name_itemtype").
                        $item_type_name."<".$nii_type.">";
            $errMsg[] = sprintf($smarty_assign->getLang("repository_error_show_help"), BASE_URL);
        }
        else if(isset($resultCheckDoi->isNotAlreadyGrantDoi) && !$resultCheckDoi->isNotAlreadyGrantDoi)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_already_grant_doi");
        }
        else if(isset($resultCheckDoi->isCorrectLibraryDoiFormat) && !$resultCheckDoi->isCorrectLibraryDoiFormat)
        {
            $handleManager = BusinessFactory::getFactory()->getBusiness("businessHandlemanager");
            $libraryPrefix = $handleManager->getLibraryJalcDoiPrefix();
            $errMsg[] = $smarty_assign->getLang("repository_error_failed_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        sprintf($smarty_assign->getLang("repository_error_library_doi_format"), $libraryPrefix);
        }
        else if(isset($resultCheckDoi->isNotSpecifyDoiInAuto) && !$resultCheckDoi->isNotSpecifyDoiInAuto)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_failed_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_doi_specified");
        }
        else if(isset($resultCheckDoi->isSpecifyDoiInFree) && !$resultCheckDoi->isSpecifyDoiInFree)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_failed_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_doi_not_specified");
        }
        else if(isset($resultCheckDoi->isNotHalfSizeNumber8Digit) && !$resultCheckDoi->isNotHalfSizeNumber8Digit)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_failed_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_half_size_number_8_digit");
        }
        else if(isset($resultCheckDoi->isStringAvailable) && !$resultCheckDoi->isStringAvailable)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_failed_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_string_available");
        }
        else if(isset($resultCheckDoi->isAvailableDoiStringLength) && !$resultCheckDoi->isAvailableDoiStringLength)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_failed_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_available_string_length");
        }
        else if(isset($resultCheckDoi->isNotDoiSuffixUsed) && !$resultCheckDoi->isNotDoiSuffixUsed)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_failed_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_used_suffix");
        }
        else if(isset($resultCheckDoi->isNotDoiSuffixDroped) && !$resultCheckDoi->isNotDoiSuffixDroped)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_failed_grant_doi");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_droped_suffix");
        }
        // インデックス公開判定はDOI付与可能でもfalseになる場合があるため、表示優先度低
        else if(isset($resultCheckDoi->isPublicIndex) && !$resultCheckDoi->isPublicIndex)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_grant_doi_index");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_public_index");
            $errMsg[] = $smarty_assign->getLang("repository_error_contact_administrator");
        }
        else if(isset($resultCheckDoi->isPublicHarvestIndex) && !$resultCheckDoi->isPublicHarvestIndex)
        {
            $errMsg[] = $smarty_assign->getLang("repository_error_grant_doi_index");
            $errMsg[] = $smarty_assign->getLang("repository_error_error_reason").
                        $smarty_assign->getLang("repository_error_public_harvest_index");
            $errMsg[] = $smarty_assign->getLang("repository_error_contact_administrator");
        }
        
        return $errMsg;
    }
    
    /**
     * Choose the DOI grant warning message to output
     * 出力するDOI付与警告メッセージを選択する
     * 
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     * @return array Warning message 警告メッセージ
     *               array[$ii]
     */
    public function chooseDoiWarnMsg($resultCheckDoi){
        $warnMsg = array();
        
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        $smarty_assign = $session->getParameter("smartyAssign");
        if(isset($resultCheckDoi->isPublicIndex) && !$resultCheckDoi->isPublicIndex)
        {
            $warnMsg[] = $smarty_assign->getLang("repository_warning_public_index");
        }
        else if(isset($resultCheckDoi->isPublicHarvestIndex) && !$resultCheckDoi->isPublicHarvestIndex)
        {
            $warnMsg[] = $smarty_assign->getLang("repository_warning_public_harvest_index");
        }
        
        return $warnMsg;
    }
    
    /**
     * Get item type name
     * アイテムのアイテムタイプ名を取得する
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @return string Item type name アイテムタイプ名
     */
    private function getItemTypeName($item_type_id)
    {
        $query = "SELECT item_type_name ".
                 "FROM ".DATABASE_PREFIX."repository_item_type ".
                 "WHERE item_type_id = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_type_id;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        $item_type_name = $result[0]['item_type_name'];
        
        return $item_type_name;
    }
}
?>