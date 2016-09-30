<?php

/**
 * Common classes of WEKO update process to ver.2.3.1
 * ver.2.3.1へのWEKOアップデート処理の共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Wekoupdaterver020301.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/update/Wekoupdaterver020300.class.php';

/**
 * Common classes of WEKO update process to ver.2.3.1
 * ver.2.3.1へのWEKOアップデート処理の共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Update_Wekoupdaterver020301 extends Repository_Components_Business_Update_Wekoupdaterver020300
{
    /**
     * Version after update: to add this constant in each inherited class, enter the version of the post-update
     * アップデート後のバージョン：各継承クラスで本定数を追加し、アップデート後のバージョンを入力する
     * 
     * @var string
     */
    const UPDATER_VERSION = "2.3.1";
    
    /**
     * The update process from the ver.2.3.0 to ver.2.3.1: overridden by each inherited class, there is need to write about the same content
     * ver.2.3.0からver.2.3.1へのアップデート処理：各継承クラスでオーバーライドし、ほぼ同じ内容を記述する必要あり
     *
     * @param string $nowVersion Current WEKO version 現在のWEKOバージョン
     */
    protected function update($nowVersion){
        $nowVersion = parent::update($nowVersion);
        
        if(!$this->isTargetVersion($nowVersion, self::UPDATER_VERSION)){
            return $nowVersion;
        }
        
        // ver.2.3.1へのアップデート処理(各継承クラスで実装したとき、ここだけそのバージョンのアップデート処理に合わせて変更する必要あり)
        $this->updateTo231();
        
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
     * The update process to WEKO ver.2.3.1
     * WEKO ver.2.3.1へのアップデート処理
     */
    protected function updateTo231(){
        // データベース側のテーブル作成・更新を実行する
        // DOI変更ログテーブルを追加
        $this->addDoiChangeLogTable();
    }
    
    /**
     * To add DOI change log table
     * DOI変更ログテーブルを追加する
     */
    protected function addDoiChangeLogTable(){
        $this->executeSqlFile(dirname(__FILE__). "/doi_change_log_createTable.sql");
    }
}
?>