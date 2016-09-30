<?php

/**
 * Repository tree export class
 * インデックスメタデータ出力クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Treeexport.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Download process class
 * ダウンロード処理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';

/**
 * Repository tree export class
 * インデックスメタデータ出力クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Edit_Treeexport extends WekoAction
{
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     */
    protected function executeApp()
    {
        $this->exitFlag = true;
        
        // 一時ディレクトリ作成
        $workDirectoryCreator = BusinessFactory::getFactory()->getBusiness("businessWorkdirectory");
        $tmpPath = $workDirectoryCreator->create();
        
        // KBART出力
        $outputIndexAdditionalData = BusinessFactory::getFactory()->getBusiness("businessOutputkbart2file");
        $kbart_file = $outputIndexAdditionalData->outputKbart2ToDirectory($tmpPath);
        
        // ダウンロード
        $repositoryDownload = new RepositoryDownload();
        $repositoryDownload->downloadFile($tmpPath.$kbart_file, $kbart_file);
        
        return "success";
    }

}

?>
