<?php

/**
 * Setting Const process
 * 定数設定処理
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BatchNC2Const.inc.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/////////////////////////////////////////////////
// NC2定数取得処理                             //
/////////////////////////////////////////////////

// 定数
define("NC2_DEPTH", 5);     // NC2ディレクトリからの距離
define("MODULE_DEPTH", 2);  // 配置モジュールディレクトリからの距離

setNC2Const();
setDbConnectConst();
setModuleName();

/**
 * set NC2 INI
 * NC2の定数を取り込む
 */
function setNC2Const()
{
    $nc2_dir = dirname(__FILE__);
    for($ii = 0; $ii < NC2_DEPTH; $ii++) {
        $nc2_dir = dirname($nc2_dir);
    }
    define('START_INDEX_DIR', transPathSeparator($nc2_dir.'/htdocs'));
    /**
     * NC2 maple INI file
     * NC2maple設定ファイル
     */
    require_once($nc2_dir.'/webapp/config/maple.inc.php');
    /**
     * NC2 install INI file
     * NC2インストール設定ファイル
     */
    @require_once($nc2_dir.'/webapp/config/install.inc.php');
}

/**
 * set DB connect info by NC2 INI
 * NC2のINIファイルからDB接続情報を取り込む
 */
function setDbConnectConst()
{
    // 分割して詰める
    $defArray = array();
    $tmpArray = explode("@", DATABASE_DSN);
    // ユーザー、パスワード
    $loginArray = explode(":", $tmpArray[0]);
    $defArray["user"] = str_replace("//", "", $loginArray[1]);
    $defArray["pass"] = $loginArray[2];
    // 接続先、DB
    $serverArray = explode("/", $tmpArray[1]);
    $defArray["server"] = $serverArray[0];
    $defArray["db"] = $serverArray[1];
    
    // 値チェック
    if(strlen($defArray["user"]) > 0 && strlen($defArray["pass"]) > 0 && strlen($defArray["server"]) > 0 && strlen($defArray["db"]) > 0) {
        define("DB_SERVER", $defArray["server"]);
        define("DB_NAME", $defArray["db"]);
        define("DB_USER", $defArray["user"]);
        define("DB_PASS", $defArray["pass"]);
    }
}

/**
 * Convert directory separator
 * ディレクトリセパレーター変換（NetCommons2の関数のコピー）
 *
 * @param  string $path ディレクトリパス
 * @return string $path 変換済ディレクトリパウs
 */
function transPathSeparator($path)
{
        if ( DIRECTORY_SEPARATOR != '/' ) {
                // IIS6 doubles the \ chars
                $path = str_replace( strpos( $path, '\\\\', 2 ) ? '\\\\' : DIRECTORY_SEPARATOR, '/', $path);
        }
        return $path;
}

/**
 * Set module name
 * モジュール名を定数に設定する
 */
function setModuleName()
{
    $module_dir = dirname(__FILE__);
    for($ii = 0; $ii < MODULE_DEPTH; $ii++) {
        $module_dir = dirname($module_dir);
    }
    
    /** モジュール名 */
    define('MODULE_NAME', basename($module_dir));
}

?>