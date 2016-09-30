<?php
/**
 * Create variant master action batch class
 * 異体字マスタテーブル作成アクションバッチクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: CreateVariantMasterAction.class.php 69174 2016-06-22 06:43:30Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Batch base Class
 * バッチ基底クラス
 * 
 */
require_once WEBAPP_DIR."/modules/repository/batch/FW/BatchBase.class.php";

/**
 * Create variant master action batch class
 * 異体字マスタテーブル作成アクションバッチクラス
 * 
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class CreateVariantMasterAction extends BatchBase
{
    /**
     * Create variant master data from variants file
     * 異体字ファイルから異体字マスタデータを作成する
     * 
     * @param class $progress Class of status for executing batch
     *                        バッチ実行用ステータスクラス
     * 
     * @return class Class of status for executing batch
     *               バッチ実行用ステータスクラス
     */
    function executeStep($progress)
    {
        $filePath = WEBAPP_DIR. '/modules/repository/files/variants/variants.csv';
        $createVariantMaster = BusinessFactory::getFactory()->getBusiness("businessCreatevariantmaster");
        // 異体字マスタデータの作成
        $createVariantMaster->executeTrans("createVariantMasterFromVariantsFile", array($filePath));
        
        // 終了処理
        $progress->exitCode = BatchExitCodes::END_SUCCESS;
        
        return $progress;
    }
}
?>