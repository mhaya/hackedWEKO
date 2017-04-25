<?php

/**
 * Common classes of WEKO update process to ver.2.3.2
 * ver.2.3.2へのWEKOアップデート処理の共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Wekoupdaterver020302.class.php 73468 2016-10-26 04:53:37Z tomohiro_ichikawa $
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
require_once WEBAPP_DIR. '/modules/repository/components/business/update/Wekoupdaterver020301.class.php';

/**
 * Common classes of WEKO update process to ver.2.3.2
 * ver.2.3.2へのWEKOアップデート処理の共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Update_Wekoupdaterver020302 extends Repository_Components_Business_Update_Wekoupdaterver020301
{
    /**
     * Version after update: to add this constant in each inherited class, enter the version of the post-update
     * アップデート後のバージョン：各継承クラスで本定数を追加し、アップデート後のバージョンを入力する
     * 
     * @var string
     */
    const UPDATER_VERSION = "2.3.2";
    
    /**
     * The update process from the ver.2.3.1 to ver.2.3.2: overridden by each inherited class, there is need to write about the same content
     * ver.2.3.1からver.2.3.2へのアップデート処理：各継承クラスでオーバーライドし、ほぼ同じ内容を記述する必要あり
     *
     * @param string $nowVersion Current WEKO version 現在のWEKOバージョン
     */
    protected function update($nowVersion){
        $nowVersion = parent::update($nowVersion);
        
        if(!$this->isTargetVersion($nowVersion, self::UPDATER_VERSION)){
            return $nowVersion;
        }
        
        // ver.2.3.2へのアップデート処理(各継承クラスで実装したとき、ここだけそのバージョンのアップデート処理に合わせて変更する必要あり)
        $this->updateTo232();
        
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
     * WEKO ver.2.3.2へのアップデート処理
     */
    protected function updateTo232(){
        // 変更なし
    }
}
?>