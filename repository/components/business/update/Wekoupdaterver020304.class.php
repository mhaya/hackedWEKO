<?php

/**
 * Common classes of WEKO update process to ver.2.3.4
 * ver.2.3.4へのWEKOアップデート処理の共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Wekoupdaterver020302.class.php 71964 2016-09-20 06:54:03Z tomohiro_ichikawa $
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
 * Common classes of WEKO update process to ver.2.3.2
 * ver.2.3.3へのWEKOアップデート処理の共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/update/Wekoupdaterver020303.class.php';

/**
 * Common classes of WEKO update process to ver.2.3.4
 * ver.2.3.4へのWEKOアップデート処理の共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Update_Wekoupdaterver020304 extends Repository_Components_Business_Update_Wekoupdaterver020303
{
    /**
     * Version after update: to add this constant in each inherited class, enter the version of the post-update
     * アップデート後のバージョン：各継承クラスで本定数を追加し、アップデート後のバージョンを入力する
     * 
     * @var string
     */
    const UPDATER_VERSION = "2.3.4";
    
    /**
     * The update process from the ver.2.3.3 to ver.2.3.4: overridden by each inherited class, there is need to write about the same content
     * ver.2.3.3からver.2.3.4へのアップデート処理：各継承クラスでオーバーライドし、ほぼ同じ内容を記述する必要あり
     *
     * @param string $nowVersion Current WEKO version 現在のWEKOバージョン
     */
    protected function update($nowVersion){
        $nowVersion = parent::update($nowVersion);
        
        if(!$this->isTargetVersion($nowVersion, self::UPDATER_VERSION)){
            return $nowVersion;
        }
        
        // ver.2.3.4へのアップデート処理(各継承クラスで実装したとき、ここだけそのバージョンのアップデート処理に合わせて変更する必要あり)
        $this->updateTo234();
        
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
     * The update process to WEKO ver.2.3.4
     * WEKO ver.2.3.4へのアップデート処理
     */
    protected function updateTo234(){
        // 変更なし
    }
}
?>