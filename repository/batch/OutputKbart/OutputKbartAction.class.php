<?php

/**
 * Output KBART 2 file class
 * KBART2ファイル出力クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: OutputKbartAction.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 */
require_once WEBAPP_DIR."/modules/repository/batch/FW/BatchBase.class.php";

/**
 * Output KBART 2 file class
 * KBART2ファイル出力クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class OutputKbartAction extends BatchBase
{
    // エラーコード
    /**
     * Not exist KBART directory error
     * KBART2ファイル出力ディレクトリ不在エラー
     */
    const ERROR_KBART_DIRECTORY_DELETED = 200;
    /**
     * Make backup error
     * バックアップ作成エラー
     */
    const ERROR_MAKE_BACKUP = 201;
    /**
     * Read latest KBART 2 information file error
     * 最新KBART2情報ファイル読み込みエラー
     */
    const ERROR_READ_LATEST_INFO = 202;
    /**
     * Place KBART2 file error
     * KBART2ファイル配置エラー
     */
    const ERROR_PLACEMENT_KBART_FILE = 203;
    /**
     * Update latest KBART 2 information file error
     * 最新KBART2情報ファイル更新エラー
     */
    const ERROR_UPDATE_LATEST_INFO = 204;

    /**
     * Execute
     * 実行
     *
     * @param BatchProgress $progress batch progress object バッチ進捗情報オブジェクト
     * @return BatchProgress $progress batch progress object バッチ進捗情報オブジェクト
     * @throws AppException
     */
    function executeStep($progress) {
        // 一時ディレクトリ作成
        $workDirectoryCreator = BusinessFactory::getFactory()->getBusiness("businessWorkdirectory");
        $tmpPath = $workDirectoryCreator->create();
        
        // KBART出力
        $outputIndexAdditionalData = BusinessFactory::getFactory()->getBusiness("businessOutputkbart2file");
        $kbart_file = $outputIndexAdditionalData->outputKbart2ToDirectory($tmpPath);
        
        // 公開ディレクトリへ移動
        $result = $this->placeKbartFile($tmpPath, $kbart_file);
        
        // 終了処理
        $progress->exitCode = BatchExitCodes::END_SUCCESS;
        
        return $progress;
    }
    
    /**
     * Place KBART 2 file
     * KBART2ファイル配置
     *
     * @param $source_dir source directory 移動元ディレクトリ
     * @param $source_file_name source file name 移動元ファイル名
     * @return bool true/false success/failed 成功/失敗
     * @throws AppException
     */
    private function placeKbartFile($source_dir, $source_file_name) {
        $this->debugLog("Start replace KBART file.", __FILE__, __CLASS__, __LINE__);
        // 公開ディレクトリのパス
        // 公開ディレクトリ下には最新のKBARTファイルと、そのファイル名が記述された情報ファイルのみが存在する
        $kbart_dir = BASE_DIR."/htdocs/weko/kbart/";
        if(!file_exists($kbart_dir)) {
            throw new AppException("Kbart directory deleted.", self::ERROR_KBART_DIRECTORY_DELETED);
        }
        $this->debugLog("KBART directory exists.", __FILE__, __CLASS__, __LINE__);
        
        // 最新KBART情報ファイル名
        $latest_info_file = "filelist.txt";
        
        // 更新前の最新KBARTファイル情報を取得する
        $old_kbart_file = "";
        if(file_exists($kbart_dir.$latest_info_file)) {
            // 1行のみ
            $fp = fopen($kbart_dir.$latest_info_file, "r");
            if(!$fp) {
                throw new AppException("Read KBART latest info is failed.", self::ERROR_READ_LATEST_INFO);
            }
            $old_kbart_file = rtrim(fgets($fp));
            fclose($fp);
        }
        $this->debugLog("Old KBART file : ".$old_kbart_file, __FILE__, __CLASS__, __LINE__);
        
        // 最新KBART情報ファイルを作成する
        $fp = fopen($kbart_dir.$latest_info_file.".new", "w");
        if(!$fp) {
            throw new AppException("Create KBART latest info is failed.", self::ERROR_UPDATE_LATEST_INFO);
        }
        fwrite($fp, $source_file_name);
        fclose($fp);
        $this->debugLog("Make new filelist.txt", __FILE__, __CLASS__, __LINE__);
        
        // 同名のKBARTファイルがあればバックアップを作成する
        $kbartBackupFlag = false;
        if(strlen($old_kbart_file) > 0 && file_exists($kbart_dir.$old_kbart_file)) {
            if(!rename($kbart_dir.$old_kbart_file, $kbart_dir.$old_kbart_file.".old")) {
                throw new AppException("Make backup KBART file is failed.", self::ERROR_MAKE_BACKUP);
            }
            $kbartBackupFlag = true;
            $this->debugLog("Make backup KBART file : ".$kbart_dir.$old_kbart_file.".old", __FILE__, __CLASS__, __LINE__);
        }
        // 最新KBART情報ファイルのバックアップを作成する
        $infoBackupFlag = false;
        if(file_exists($kbart_dir.$latest_info_file)) {
            if(!rename($kbart_dir . $latest_info_file, $kbart_dir.$latest_info_file.".old")) {
                // KBARTファイルを移動していた場合は差し戻す
                if($kbartBackupFlag) {
                    rename($kbart_dir.$old_kbart_file.".old", $kbart_dir.$old_kbart_file);
                }
                throw new AppException("Make backup latest info file is failed.", self::ERROR_MAKE_BACKUP);
            }
            $infoBackupFlag = true;
            $this->debugLog("Make backup filelist : ".$kbart_dir.$latest_info_file.".old", __FILE__, __CLASS__, __LINE__);
        }
        
        // 移動先のファイルパス文字列を作成する
        if(stristr(php_uname(), "Windows")){
            $dest_kbart_file = $kbart_dir.mb_convert_encoding($source_file_name, "SJIS", "auto");
        } else {
            $dest_kbart_file = $kbart_dir.$source_file_name;
        }
        
        // 新しいKBARTファイルを配置する
        if(!rename($source_dir.$source_file_name, $dest_kbart_file)) {
            // 失敗した場合バックアップから復元を試みる
            if($kbartBackupFlag) {
                if(!rename($kbart_dir.$old_kbart_file.".old", $kbart_dir.$old_kbart_file)) {
                    throw new AppException("Place new KBART file is failed, but recovered by backup.", self::ERROR_PLACEMENT_KBART_FILE);
                }
            }
            // 失敗した場合はFATALエラー
            $this->fatalLog("Place new KBART file is failed.", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Place new KBART file is failed.", self::ERROR_PLACEMENT_KBART_FILE);
        }
        $this->debugLog("Place KBART file success.", __FILE__, __CLASS__, __LINE__);
        
        // 最新KBART情報ファイルを差替える
        if(!rename($kbart_dir.$latest_info_file.".new", $kbart_dir.$latest_info_file)) {
            $this->fatalLog("Update KBART latest info is failed.", __FILE__, __CLASS__, __LINE__);
            // 失敗した場合はKBARTファイル・情報ファイル共に差し戻す
            if($infoBackupFlag) {
                if(!rename($old_kbart_file.".old", $old_kbart_file)) {
                    $this->fatalLog("Recover latest info file is failed.", __FILE__, __CLASS__, __LINE__);
                }
            }
            if($kbartBackupFlag) {
                if(!rename($kbart_dir.$old_kbart_file.".old", $kbart_dir.$old_kbart_file)) {
                    $this->fatalLog("Recover Kbart file is failed.", __FILE__, __CLASS__, __LINE__);
                }
            }
            $this->fatalLog("Update KBART latest info is failed. Mismatch occured.", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Update KBART latest info is failed. Mismatch occured.", self::ERROR_UPDATE_LATEST_INFO);
        }
        $this->debugLog("Place filelist success.", __FILE__, __CLASS__, __LINE__);
        
        // 旧ファイル・バックアップを削除する
        // 同名ファイルで上書き更新されている場合は削除しない
        if(strlen($old_kbart_file) > 0 && $old_kbart_file != $source_file_name && file_exists($kbart_dir.$old_kbart_file)) { unlink($kbart_dir.$old_kbart_file); }
        if($kbartBackupFlag && file_exists($kbart_dir.$old_kbart_file.".old")) { unlink($kbart_dir.$old_kbart_file.".old"); }
        if($infoBackupFlag && file_exists($kbart_dir.$latest_info_file.".old")) { unlink($kbart_dir.$latest_info_file.".old"); }
        
        $this->debugLog("End replace KBART file.", __FILE__, __CLASS__, __LINE__);
        
        return true;
    }
    
}
?>