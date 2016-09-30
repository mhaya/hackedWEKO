<?php

/**
 * Common classes of WEKO update process to ver.2.3.0
 * ver.2.3.0へのWEKOアップデート処理の共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Wekoupdaterver020300.class.php 70936 2016-08-09 09:53:57Z keiya_sugimoto $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Common classes of WEKO update process
 * WEKOアップデート処理の共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/update/Wekoupdaterbase.class.php';

/**
 * Common classes of WEKO update process to ver.2.3.0
 * ver.2.3.0へのWEKOアップデート処理の共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Update_Wekoupdaterver020300 extends Wekoupdaterbase 
{
    /**
     * Version after update: to add this constant in each inherited class, enter the version of the post-update
     * アップデート後のバージョン：各継承クラスで本定数を追加し、アップデート後のバージョンを入力する
     * 
     * @var string
     */
    const UPDATER_VERSION = "2.3.0";
    
    /**
     * The update process from the ver.2.2.3 to ver.2.3.0: overridden by each inherited class, there is need to write about the same content
     * ver.2.2.3からver.2.3.0へのアップデート処理：各継承クラスでオーバーライドし、ほぼ同じ内容を記述する必要あり
     *
     * @param string $nowVersion Current WEKO version 現在のWEKOバージョン
     */
    protected function update($nowVersion){
        $nowVersion = parent::update($nowVersion);
        
        if(!$this->isTargetVersion($nowVersion, self::UPDATER_VERSION)){
            return $nowVersion;
        }
        
        // ver.2.3.0へのアップデート処理(各継承クラスで実装したとき、ここだけそのバージョンのアップデート処理に合わせて変更する必要あり)
        $this->updateTo230();
        
        return self::UPDATER_VERSION;
    }
    
    /**
     * To get the version after the update of this updater: it is necessary to override an inherited class, the processing content is not changed
     * 本アップデーターのアップデート後のバージョンを取得する：継承クラスでオーバーライドし、同じ内容を記述する必要あり
     *
     * @return string Version after update アップデート後のバージョン
     */
    public function getUpdaterVersion(){
        return self::UPDATER_VERSION;
    }
    
    /**
     * The update process to WEKO ver.2.3.0
     * WEKO ver.2.3.0へのアップデート処理
     */
    protected function updateTo230(){
        // ディレクトリ・ファイルの作成については先に実行する
        // Create KBART file directory
        $this->makeKbartDirectory();
        // Make directory for version files
        $this->makeFileVersionDirectory();
        // Make batch log directory
        $this->makeBatchLogDirectory();
        
        // データベース側のテーブル作成・更新を実行する
        // Batch Process Table
        $this->createBatchProcessTable();
        // Additional Data Table
        $this->createAdditionalDataTable();
        // create repository_ranking_cont_period table
        $this->createRankingCountPeriodTable();
        // Create file update history table
        $this->createFileUpdateHistoryTable();
        // 不要になったパラメータテーブルのパラメータ名のレコードを削除する
        // (フルテキストテーブルデータの作成、ver.2.0.9からver.2.0.10にてドロップした機能)
        $this->deleteParameterOnCreateFullText();
        // SPASEマッピングの保存欄を作成
        $this->addColumnForSpaseMapping();
        // 異体字マスタテーブルの作成
        $this->createVariantMasterTableAndData();
        // WEKO著者ID検索機能の追加
        $this->addSettingsWekoAuthorIdSearch();
        // W著者名検索切替用の設定追加
        $this->addSettingsSwitchAuthorSearch();
        // 著者ID外部リンク用の設定を追加
        $this->addSettingsExternalLinkAuthorId();
        // create sitelicense usage statistics table
        $this->createSitelicenseUsageStatisticsTable();
        // アイテム属性タイプ：チェックボックスの複数可の値を1に更新する
        $this->updateItemAttrTypeCheckboxPluralEnable();
    }
    
    /**
     * To add a column for SPACE mapping
     * SPASEマッピング用のカラムを追加する
     */
    protected function addColumnForSpaseMapping(){
        $this->executeSqlFile(dirname(__FILE__). "/item_attr_type_alterTable_addSpaseMapping.sql");
    }
    
    /**
     * Delete of the parameters involved in the old text file search table creation
     * 古い本文ファイル検索テーブル作成にかかわるパラメータの削除
     */
    protected function deleteParameterOnCreateFullText(){
        $query = $this->loadSql(dirname(__FILE__). "/parameter_delete_deleteTargetParamName.sql");
        $params = array();
        $params[0] = "fulltextindex_contents";
        $this->executeSql($query, $params);
        $params[0] = "fulltextindex_starttime";
        $this->executeSql($query, $params);
        $params[0] = "fulltextindex_endtime";
        $this->executeSql($query, $params);
    }
    
    /**
     * To create a state management table of batch processing
     * バッチ処理の状態管理テーブルを作成する
     */
    protected function createBatchProcessTable()
    {
        $this->executeSqlFile(dirname(__FILE__). "/bat_status_createTable.sql");
    }
    
    /**
     * Creating the index attachment metadata table
     * インデックス付属メタデータテーブルの作成
     */
    protected function createIndexAdditionalDataTable(){
        // インデックス付属データ関係テーブル
        $this->executeSqlFile(dirname(__FILE__). "/index_additionaldata_createTable.sql");
    }
    
    /**
     * Insert creation and the initial value of the supplied metadata type table
     * 付属メタデータタイプテーブルの作成と初期値挿入
     */
    protected function createAdditionalDataTypeTable(){
        // 付属データタイプテーブル
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_type_createTable.sql");
        // 初期値挿入
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_type_insert_kbartData.sql");
    }
    
    /**
     * Insert creation and the initial value of the supplied metadata attribute type table
     * 付属メタデータ属性タイプテーブルの作成と初期値挿入
     */
    protected function createAdditionalDataAttrTypeTable(){
        // 付属データタイプ属性テーブル
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_attr_type_createTable.sql");
        // 初期値挿入
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_attr_type_insert_kbartData.sql");
    }
    
    /**
     * Insert creation and the initial value of the supplied metadata attribute type name table
     * 付属メタデータ属性タイプ名テーブルの作成と初期値挿入
     */
    protected function createAdditionalDataAttrTypeNameTable(){
        // 付属データタイプ属性名テーブル
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_attr_type_name_createTable.sql");
        // 初期値挿入
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_attr_type_name_insert_kbartData.sql");
    }
    
    /**
     * Creating accessory metadata attribute table
     * 付属メタデータ属性テーブルの作成
     */
    protected function createAdditionalDataAttrTable(){
        // 付属データ属性テーブル
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_attr_createTable.sql");
    }
    
    /**
     * Insert creation and the initial value of the supplied metadata choice candidate table
     * 付属メタデータ選択肢候補テーブルの作成と初期値挿入
     */
    protected function createAdditionalDataAttrCandidateTable(){
        // 付属データ選択肢候補テーブル
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_attr_candidate_createTable.sql");
        // 初期値挿入
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_attr_candidate_insert_kbartData.sql");

        // 付属データ選択肢候補表示名テーブル
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_attr_candidate_label_createTable.sql");
        // 初期値挿入
        $this->executeSqlFile(dirname(__FILE__). "/additionaldata_attr_candidate_label_insert_kbartData.sql");
    }
    
    /**
     * Delete sitelicense info record in parameter table
     * パラメータテーブルからサイトライセンス情報レコードを削除する
     */
    protected function deleteSitelicenseRecordInParameter(){
        // 本来site_licenseレコードはWEKO2.1.8→2.2.0のアップデート時に削除されるはずであったが、
        // 不具合により消えない場合があるため、WEKO2.3.0へのアップデート時に再度削除を実行する
        $params = array();
        $params[] = "site_license";
        $this->executeSqlFile(dirname(__FILE__). "/parameter_delete_deleteTargetParamName.sql", $params);
    }
    
    /**
     * To create a table for managing the data that is included in the index
     * インデックスに付属するデータを管理するテーブルを作成する
     */
    protected function createAdditionalDataTable()
    {
        $this->createIndexAdditionalDataTable();
        $this->createAdditionalDataTypeTable();
        $this->createAdditionalDataAttrTypeTable();
        $this->createAdditionalDataAttrTypeNameTable();
        $this->createAdditionalDataAttrTable();
        $this->createAdditionalDataAttrCandidateTable();
        $this->deleteSitelicenseRecordInParameter();
    }
    
    /**
     * To create a KBART format file output destination directory
     * KBART形式ファイル出力先ディレクトリを作成する
     */
    protected function makeKbartDirectory() {
        // KBART公開ディレクトリ
        Repository_Components_Util_OperateFileSystem::makeSystemDirectory(BASE_DIR."/htdocs/weko/kbart/", 0777);
    }
    
    /**
     * create repository_ranking_cont_period table
     * ランキング集計結果をデータベースから取得するとき、集計期間を保存するテーブルを作成
     */
    protected function createRankingCountPeriodTable()
    {
        $this->executeSqlFile(dirname(__FILE__). "/ranking_count_period_createTable.sql");
        $this->executeSqlFile(dirname(__FILE__). "/ranking_count_period_insert_initValue.sql");
    }

    /**
     * Create file_update_history table
     * ファイル更新履歴テーブルを作成する
     */
    protected function createFileUpdateHistoryTable()
    {
        $this->executeSqlFile(dirname(__FILE__). "/file_update_history_createTable.sql");
    }
    
    /**
     * To create a version control directory
     * バージョン管理ディレクトリを作成する
     */
    protected function makeFileVersionDirectory()
    {
        Repository_Components_Util_OperateFileSystem::makeSystemDirectory(WEBAPP_DIR. DIRECTORY_SEPARATOR. "uploads/repository/versionFiles", 0777);
    }
    
    /**
     * After new additional tables, the structure change, 
     * and deletion has been completed, flag is set to reconstruct search table
     * (Call this function if how to make search tables has been changed due to a change of WEKO source code)
     * テーブルの新規追加、構造変更、削除が完了した後、検索テーブルの再構築を行うよう設定する
     * (WEKOソースコードの変更により検索テーブルの作成方法が変更された場合、本関数を呼び出す)
     */
    protected function changeFlagForReconstructingSearchTableOn()
    {
        $this->recursiveProcessingFlgList[self::KEY_REPOSITORY_SEARCH_TABLE_PROCESSING] = true;
    }
    
    /**
     * Create variant master table and data in order to search variants
     * 異体字を検索するため、異体字マスタテーブル及びデータを作成する
     */
    protected function createVariantMasterTableAndData()
    {
        $createVariantMaster = BusinessFactory::getFactory()->getBusiness("businessCreatevariantmaster");
        // 異体字マスタテーブルの作成
        $createVariantMaster->createVariantMasterTable();
        // 異体字マスタデータの作成
        $filePath = WEBAPP_DIR. '/modules/repository/files/variants/variants.csv';
        $createVariantMaster->createVariantMasterFromVariantsFile($filePath);
    }
    
    /**
     * Add settings for WEKO author id search
     * WEKO著者ID検索用の設定を追加する
     */
    protected function addSettingsWekoAuthorIdSearch()
    {
        // WEKO著者ID検索用のデータ追加
        $this->executeSqlFile(dirname(__FILE__). "/search_item_setup_insert_addWekoAuthorId.sql");
        // 氏名テーブルの著者IDにインデックスを張る
        $this->executeSqlFile(dirname(__FILE__). "/personal_name_alterTable_addIndexAuthorId.sql");
        // 集計対象詳細検索項目にWEKO著者ID検索のデータ追加
        $this->executeSqlFile(dirname(__FILE__). "/target_search_item_insert_addWekoAuthorId.sql");
    }
    
    /**
     * Add settings for switching author search
     * 著者名検索切替え用の設定を追加する
     */
    protected function addSettingsSwitchAuthorSearch()
    {
        // 著者名検索切替用のデータ追加
        $this->executeSqlFile(dirname(__FILE__). "/parameter_insert_addAuthorSearchType.sql");
    }
    
    /**
     * Add settings for external link of author id
     * 著者ID外部リンク用の設定を追加する
     */
    protected function addSettingsExternalLinkAuthorId()
    {
        // 著者ID外部リンク用のカラム追加
        $this->executeSqlFile(dirname(__FILE__). "/external_author_id_prefix_alterTable_addExternalLink.sql");
        // デフォルトの著者ID外部リンク追加
        $params = array();
        $params[] = 'http://ci.nii.ac.jp/nrid/##';
        $params[] = 1;
        $this->executeSqlFile(dirname(__FILE__). "/external_author_id_prefix_update_addDefaultExternalLink.sql", $params);
        $params = array();
        $params[] = '';
        $params[] = 2;
        $this->executeSqlFile(dirname(__FILE__). "/external_author_id_prefix_update_addDefaultExternalLink.sql", $params);
        $params = array();
        $params[] = 'https://kaken.nii.ac.jp/ja/search/?qm=##';
        $params[] = 3;
        $this->executeSqlFile(dirname(__FILE__). "/external_author_id_prefix_update_addDefaultExternalLink.sql", $params);
    }
    
    /**
     * Add sitelicense tables
     * サイトライセンス用のテーブルを追加する
     */
    private function createSitelicenseUsageStatisticsTable() {
        // サイトライセンス用テーブル作成
        $this->executeSqlFile(dirname(__FILE__). "/sitelicense_dlview_createTable.sql");
        $this->executeSqlFile(dirname(__FILE__). "/sitelicense_usage_searchkeyword_createTable.sql");
        $this->executeSqlFile(dirname(__FILE__). "/send_mail_sitelicense_dropTable.sql");
        $this->executeSqlFile(dirname(__FILE__). "/sitelicense_mail_send_status_createTable.sql");
        $this->executeSqlFile(dirname(__FILE__). "/sitelicense_mail_send_status_all_createTable.sql");
        // パラメータテーブルにPHPの実行パスを追加
        if(PHP_OS == "Linux" || PHP_OS == "MacOS") {
            exec("printenv PATH", $path);
        } else if(PHP_OS == "WIN32" || PHP_OS == "WINNT") {
            exec("PATH", $path);
            $path = str_replace("PATH=", "", $path);
        } else {
            $path = null;
        }
        
        $php_path = "";
        $path = split(PATH_SEPARATOR,$path[0]);
        for($ii = 0; $ii < count($path); $ii++){	
            // 取得した環境変数には最後にディレクトリセパレータがない場合は追加
            if(strlen($path[$ii]) > 0 && $path[$ii][strlen($path[$ii])-1] != DIRECTORY_SEPARATOR){
                $path[$ii] .= DIRECTORY_SEPARATOR;
            }
            if(file_exists($path[$ii]."php") || file_exists($path[$ii]."php.exe")){
                $php_path = $path[$ii];
            }
        }
        
        $params = array();
        $params[] = $php_path;
        $this->executeSqlFile(dirname(__FILE__). "/parameter_insert_pathPhp.sql", $params);
    }
    
    /**
     * Create a batch log directory
     * バッチログディレクトリの作成
     */
    private function makeBatchLogDirectory(){
        Repository_Components_Util_OperateFileSystem::makeSystemDirectory(WEBAPP_DIR."/logs/weko/batch/", 0777);
    }
    
    /**
     * To Update to plural enable for checkbox
     * チェックボックスの属性を複数可に更新する
     */
    protected function updateItemAttrTypeCheckboxPluralEnable(){
        $params = array();
        $params[] = 1;
        $params[] = 'checkbox';
        $this->executeSqlFile(dirname(__FILE__). "/item_attr_type_update_checkboxPluralEnable.sql", $params);
    }
}
?>