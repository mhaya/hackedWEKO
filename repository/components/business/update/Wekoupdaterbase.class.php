<?php

/**
 * Common classes of WEKO update process
 * WEKOアップデート処理の共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Wekoupdaterbase.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';

/**
 * Common classes of WEKO update process
 * WEKOアップデート処理の共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Wekoupdaterbase extends BusinessBase 
{
    /**
     * Version after update: to add this constant in each inherited class, enter the version of the post-update
     * アップデート後のバージョン：各継承クラスで本定数を追加し、アップデート後のバージョンを入力する
     * 
     * @var string
     */
    const UPDATER_VERSION = "2.2.3";
    
    /**
     * 2.2.3 to implement the WEKO later updates
     * 2.2.3以降のWEKOアップデートを実施する
     *
     * @param string $nowVersion Current WEKO version 現在のWEKOバージョン
     */
    public function execute($nowVersion){
        $updatedVersion = $this->update($nowVersion);
        
        if($nowVersion != $updatedVersion){
            // アップデートが実施されたとき、最新バージョンにWEKOバージョンを更新する
            $this->updateWekoVersionOnParamTable($updatedVersion);
        }
    }
    
    /**
     * To get the version after the update of this updater: it is necessary to override an inherited class, the processing content is not changed
     * 本アップデーターのアップデート後のバージョンを取得する：継承クラスでオーバーライドする必要があるが、処理内容は変わらない
     *
     * @return string Version after update アップデート後のバージョン
     */
    public function getUpdaterVersion(){
        return self::UPDATER_VERSION;
    }
    
    /**
     * To update the WEKO version information in the parameter table to the latest version
     * パラメータテーブル内のWEKOバージョン情報を最新版に更新する
     *
     * @param string $updatedVersion Version after update アップデート後のバージョン
     */
    protected function updateWekoVersionOnParamTable($updatedVersion){
        $query = "UPDATE ".DATABASE_PREFIX ."repository_parameter ".
                 " SET param_value = ?, ".
                 " mod_user_id = ?, ".
                 " mod_date = ? ".
                 " WHERE param_name = ?;";
        $params = array();
        $params[] = $updatedVersion;
        $params[] = $this->user_id;
        $params[] = $this->accessDate;
        $params[] = 'WEKO_version';
        $this->executeSql($query, $params);
    }
    
    /**
     * The update process for each version: the update process will be implemented by inheriting class
     * 各バージョンごとのアップデート処理：アップデート処理は継承先クラスで実装する
     *
     * @param string $nowVersion Current WEKO version 現在のWEKOバージョン
     */
    protected function update($nowVersion){
        if(!$this->isTargetVersion($nowVersion, self::UPDATER_VERSION)){
            return $nowVersion;
        }
        // 2.2.3以降のアップデートしか実施しないため、それより古いバージョンが指定された場合は例外を投げる
        throw new AppException("Invalid version");
    }
    
    /**
     * Return whether or not the version that performs the update(If the current WEKO version is old, it is necessary to perform the update)
     * アップデートを行うバージョンであるか否かを返す(現在のWEKOバージョンが古い場合、アップデートを実施する必要がある)
     *
     * @param string $version Current WEKO version 現在のWEKOバージョン
     * @param string $updaterVersion Version after update アップデート後のバージョン
     * @return bool Whether or not to perform updates アップデートを実施するか否か
     */
    protected function isTargetVersion($nowVersion, $updaterVersion){
        // WEKOバージョンのメジャー、マイナー、ビルドバージョンを比較し、
        // 古いバージョンであるか否かを判定する
        $nowVersionList = explode(".", $nowVersion);
        $updaterVersionList = explode(".", $updaterVersion);
        
        // メジャーバージョンの比較
        if($nowVersionList[0] < $updaterVersionList[0]){
            return true;
        } else if($nowVersionList[0] > $updaterVersionList[0]){
            // 現在のWEKOバージョンがアップデート後のバージョンより新しいならばバージョンを行う必要はない
            return false;
        }
        
        // マイナーバージョンの比較
        if($nowVersionList[1] < $updaterVersionList[1]){
            return true;
        } else if($nowVersionList[1] > $updaterVersionList[1]){
            // 現在のWEKOバージョンがアップデート後のバージョンより新しいならばバージョンを行う必要はない
            return false;
        }
        
        // ビルドバージョンの比較
        if($nowVersionList[2] < $updaterVersionList[2]){
            return true;
        } else if($nowVersionList[2] > $updaterVersionList[2]){
            // 現在のWEKOバージョンがアップデート後のバージョンより新しいならばバージョンを行う必要はない
            return false;
        }
        
        // 二つのバージョンが一致する場合、アップデートは実施しないのでfalseを返す
        return false;
    }
}
?>