<?php

/**
 * File format confirmed common classes
 * ファイル形式確認共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryCheckFileTypeUtility.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Check file type utility class
 * ファイルタイプチェックユーティリティークラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryCheckFileTypeUtility.class.php';

/**
 * File format confirmed common classes
 * ファイル形式確認共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007 - 2008, National Institute of Informatics, Research and Development Center for Scientific Information Resources.
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositoryCheckFileTypeUtility
{
    
    /**
     * file is image file or not
     * 画像ファイルか判定する
     *
     * @param string $mimeType mime type MIME TYPE
     * @param string $extension extension 拡張子
     * @return bool true/false image file/not image file 画像ファイルである/画像ファイルではない
     */
    public static function isImageFile($mimeType, $extension)
    {
        if(preg_match('/^image\/([a-z]|-|\.)+$/', $mimeType) === 1 ||
           $extension == "emf" ||
           $extension == "wmf" ||
           $extension == "bmp" ||
           $extension == "png" ||
           $extension == "gif" ||
           $extension == "tiff" ||
           $extension == "jpg" ||
           $extension == "jp2") {
            return true;
        } else {
            return false;
        }
    }
    
}

?>
