<?php

/**
 * Repository Components Util Create Work Directory Class
 * 作業ディレクトリ作成クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: CreateWorkDirectory.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Repository Components Util Create Work Directory Class
 * 作業ディレクトリ作成クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Util_CreateWorkDirectory
{
    /*
     * Try create directory number
     * ディレクトリ作成を試行する回数
     */
    const TRY_NUM = 100;

    /**
     * Create work directory
     * 作業ディレクトリを作成する
     *
     * @param string $parentDir parent directory 親ディレクトリを作成する
     * @return string|bool work directory path/create failed 作業ディレクトリパス/作成失敗
     */
    public static function create($parentDir)
    {
        // 作成
        for($ii = 0; $ii < self::TRY_NUM; $ii++) {
            // ディレクトリ名決定
            $tmpDirPath = self::makeWorkDirectoryName($parentDir);
            // 作成実行
            $result = mkdir($tmpDirPath);
            if($result) {
                return $tmpDirPath."/";
            }
            usleep(1000);
        }
        
        return false;
    }

    /**
     * Make work directory name
     * 作業ディレクトリ名を割くせりする
     *
     * @param string $parentDir parent directory 親ディレクトリ
     * @return string work directory name 作業ディレクトリ名
     */
    private static function makeWorkDirectoryName($parentDir)
    {
        // 乱数を設定
        $rand = mt_rand(0,99999999);
        $tmpDirPath = $parentDir."tmp.". $rand;
        
        return $tmpDirPath;
    }
}
?>
