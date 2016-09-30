<?php

/**
 * ZIP file manipulation common classes
 * ZIPファイル操作共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: ZipUtility.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * ZIP file manipulation library
 * ZIPファイル操作ライブラリ
 */
include_once MAPLE_DIR.'/includes/pear/File/Archive.php';

/**
 * ZIP file manipulation common classes
 * ZIPファイル操作共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Util_ZipUtility
{
    /**
     * Compressed into zip file
     * ※ If you want to compress the file is not a slash to the end (which is recognized as a folder)
     * zipファイルに圧縮する
     * ※ファイルを圧縮する場合は末尾にスラッシュを付けない(フォルダとして認識される)
     *
     * @param string $cmpFrom Compressed original file / folder path 圧縮元ファイル/フォルダパス
     * @param string $cmpTo Compressed file path 圧縮先ファイルパス
     * @return boolean Execution result 実行結果
     */
    public static function compress($cmpFrom, $cmpTo)
    {
        // Update SuppleContentsEntry Y.Yamazawa --start--
        if ( file_exists($cmpFrom) ) {

            $zip = new ZipArchive();
            // ZIPファイルをオープン
            $res = $zip->open($cmpTo, ZipArchive::CREATE);

            // zipファイルのオープンに成功した場合
            if ($res === true) {

                // 圧縮するファイルを指定する
                $zip->addFile($cmpFrom);

                // ZIPファイルをクローズ
                $zip->close();
            }
            return true;
        }
        else {
            return false;
        }
        // Update SuppleContentsEntry Y.Yamazawa --end--
    }

    /**
     * Unzip the zip file
     * zipファイルを解凍する
     *
     * @param string $extFrom Compressed file path 圧縮ファイルパス
     * @param string $extTo Destination folder path 解凍先フォルダパス
     * @return boolean Success or failure of the zip decompression zip解凍の成功失敗
     */
    public static function extract($extFrom, $extTo)
    {
        // Update SuppleContentsEntry Y.Yamazawa --start--
        if ( file_exists($extFrom) && file_exists($extTo) ) {

            // ZipArchiveを利用して解凍時の処理を低減しようとしたが、
            // 日本語のファイル名を解凍する際、EUC、UTF8、SJIS以外の不明な文字コードで
            // 解凍してしまい、文字化けしないようにすることができなかったので、
            // File_Archiveを利用する
            File_Archive::extract(
                File_Archive::read($extFrom . "/"),
                File_Archive::appender($extTo)
            );
//            $zip = new ZipArchive();
//            $res = $zip->open($extFrom);
//            // zipファイルのオープンに成功した場合
//            if ($res === true) {
//                // 圧縮ファイル内の全てのファイルを指定した解凍先に展開する
//                $rsult = $zip->extractTo(self::_addSlash($extTo));
//                if($rsult === false){
//                    return false;
//                }
//
//                // ZIPファイルをクローズ
//                $zip->close();
//            }else{
//                return false;
//            }

            return true;
        }
        else {
            return false;
        }
        // Update SuppleContentsEntry Y.Yamazawa --end--
    }

    /**
     * The end of the path is a slash if not slash
     * パスの最後がスラッシュでなければスラッシュを付ける
     * 
     * @param $str Path パス
     * @return string Path after processing 処理後のパス
     */
    private static function _addSlash($str)
    {
        if (substr($str, -1) == '/') {
            $ret = $str;
        } else {
            $ret = $str.'/';
        }
        return $ret;
    }
}
?>
