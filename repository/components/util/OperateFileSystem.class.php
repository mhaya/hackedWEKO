<?php

/**
 * Operate the file system
 * ファイルシステムの操作を行う
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: OperateFileSystem.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Exception class
 * 例外クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/AppException.class.php';

/**
 * WEKO logger class
 * WEKOロガークラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/AppLogger.class.php';

/**
 * Operate the file system
 * ファイルシステムの操作を行う
 * 
 * @package WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Util_OperateFileSystem
{
    /**
     * To delete a directory recursively
     * ディレクトリを再帰的に削除する
     *
     * @param string $dir Directory path ディレクトリパス
     * @return boolean Execution result 実行結果
     */
    public static function removeDirectory($dir)
    {
        chmod ($dir, 0777);
        if (!($handle = opendir($dir))) {
            return false;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $removeDir = $dir. DIRECTORY_SEPARATOR. $file;
                if (is_dir($removeDir)) {
                    self::removeDirectory($removeDir);
                    if(file_exists($removeDir)) {
                        rmdir($removeDir);
                    }
                } else {
                    chmod ($removeDir, 0777);
                    unlink($removeDir);
                }
            }
        }
        closedir($handle);
        rmdir($dir);
    }
    
    /**
     * Copy directory
     * ディレクトリをコピー
     *
     * @param string $source The path of the source directory コピー元ディレクトリのパス
     * @param string $dest The path of the destination directory コピー先ディレクトリのパス
     */
    public static function copyDirectory($source, $dest){
        if(!file_exists($source)){
            // ディレクトリが存在しないとき
            $ex = new AppException("is not exists directory: $source");
            $ex->addError("repository_error_operate_file_system");
            throw $ex;
        }
        
        if ($handle = opendir("$source")) {
            while (false !== ($file = readdir($handle))) {
                if (strpos($file, "svn") === false && $file != "." && $file != "..") {
                    if (is_dir("$source/$file")) {
                        // directory
                        if( !file_exists("$dest/$file")){
                            if(mkdir ( "$dest/$file", 0300) === false){
                                $ex = new AppException("failed mkdir: $dest/$file");
                                $ex->addError("repository_error_operate_file_system");
                                throw $ex;
                            }
                        }
                        self::copyDirectory("$source/$file", "$dest/$file");
                    } else {
                        // file
                        if(!file_exists(dirname("$dest/$file"))){
                            // ファイルの親ディレクトリを作成
                            mkdir ( dirname("$dest/$file"), 0300);
                        }
                        if(copy("$source/$file", "$dest/$file") === false){
                            $ex = new AppException("failed copy: $source/$file -> $dest/$file");
                            $ex->addError("repository_error_operate_file_system");
                            throw $ex;
                        }
                        if(chmod("$dest/$file", 0644) === false){
                            $ex = new AppException("failed chmod: $dest/$file");
                            $ex->addError("repository_error_operate_file_system");
                            throw $ex;
                        }
                    }
                }
            }
            closedir($handle);
        } else {
            // ディレクトリを開けなかった場合
            $ex = new AppException("failed open directory: $source");
            $ex->addError("repository_error_operate_file_system");
            throw $ex;
        }
    }
    
    /**
     * To rename a file or directory
     * ファイルまたはディレクトリをリネーム
     *
     * @param string $oldname Rename the original path リネーム元パス
     * @param string $newname Rename the destination path リネーム先パス
     */
    public static function rename($oldname, $newname){
        // Linuxでは問題はないが、Windowsではrename時にnewFileが存在する場合、
        // エラーとなってしまうので、newFileが存在する場合、削除する必要がある
        if(file_exists($newname)){
            if(is_dir($newname)){
                if(self::removeDirectory($newname) === false){
                    $ex = new AppException("[Failed removeDirectory] $newname");
                    $ex->addError("repository_error_operate_file_system");
                    throw $ex;
                }
            } else {
                self::unlink($newname);
            }
        }
        
        if(!rename($oldname, $newname)){
            $ex = new AppException("[Failed rename] $oldname -> $newname");
            $ex->addError("repository_error_operate_file_system");
            throw $ex;
        }
    }
    
    /**
     * Copies file
     * ファイルをコピーする
     *
     * @param string $source Path to the source file コピー元ファイルへのパス。
     * @param string $dest The destination path コピー先のパス
     */
    public static function copy($source, $dest){
        if(!copy($source, $dest)){
            $ex = new AppException("[Failed copy file] ".$source. " -> ". $dest);
            $ex->addError("repository_error_operate_file_system");
            throw $ex;
        }
    }
    
    /**
     * Makes directory
     * ディレクトリを作る
     *
     * @param string $pathname The directory path ディレクトリのパス
     * @param int $mode Access restriction(mode is ignored on Windows) アクセス制限(Windows では mode は無視)
     * @param boolean $recursive Allows the creation of nested directories 入れ子構造のディレクトリの作成を許可
     */
    public static function mkdir($pathname, $mode = 0777, $recursive = false){
        if(!mkdir($pathname, $mode, $recursive)){
            $ex = new AppException("[Failed make directory] pathname: ".$pathname. " mode: ". $mode. " recursive: ". $recursive);
            $ex->addError("repository_error_operate_file_system");
            throw $ex;
        }
    }
    
    /**
     * Gives information about a file
     * ファイルに関する情報を取得する
     *
     * @param string $filename Path to the file ファイルへのパス
     * @return array File information(Device number, inode number (always in Windows 0), inode protected mode, number of links, the owner of the user ID (always in Windows 0), the owner of the group ID (in Windows always 0), in the case of the inode device, the device type, size in bytes, last access time (Unix timestamp), time of last modification (Unix timestamp), last inode change time (Unix timestamp), on systems that support the block size (st_blksize type of file IO onlyIt enabled), ensuring the number of 512-byte blocks (valid only for systems that support st_blksize type)) 
     *               ファイル情報(デバイス番号,inode 番号(Windows では常に 0),inode プロテクトモード,リンク数,所有者のユーザー ID(Windows では常に 0),所有者のグループ ID(Windows では常に 0),inode デバイス の場合、デバイスの種類,バイト単位のサイズ,最終アクセス時間 (Unix タイムスタンプ),最終修正時間 (Unix タイムスタンプ),最終 inode 変更時間 (Unix タイムスタンプ),ファイル IO のブロックサイズ(st_blksize タイプをサポートするシステムでのみ有効),512 バイトのブロックの確保数(st_blksize タイプをサポートするシステムでのみ有効))
     *               array[0-12|"dev"|"ino"|"mode"|"nlink"|"uid"|"gid"|"rdev"|"size"|"atime"|"mtime"|"ctime"|"blksize"|"blocks"]
     */
    public static function stat($filename){
        if(!file_exists($filename)){
            $ex = new AppException("[Failed stat] File not exists: $filename");
            $ex->addError("repository_error_operate_file_system");
            throw $ex;
        }
        $result = stat($filename);
        if($result === false){
            $ex = new AppException("[Failed stat] filename: $filename");
            $ex->addError("repository_error_operate_file_system");
            throw $ex;
        }
        return $result;
    }
    
    /**
     * Opens file or URL
     * ファイルまたは URL をオープンする
     *
     * @param string $filename File path ファイルパス
     * @param mode $mode The type of access アクセス形式
     * @param boolean $use_include_path Search for the file in the include_path include_pathのファイルの検索も行う
     * @return resource File pointer resource ファイルポインタリソース
     */
    public static function fopen($filename, $mode, $use_include_path = false){
        $resource = fopen($filename, $mode, $use_include_path);
        if($resource === false){
            // error
            $ex = new AppException("[Failed fopen] filename:".$filename. " mode:". $mode. " use_include_path:". $use_include_path);
            $ex->addError("repository_error_operate_file_system");
            throw $ex;
        }
        
        return $resource;
    }
    
    /**
     * Closes an open file pointer
     * オープンされたファイルポインタをクローズ
     *
     * @param resource $handle The file pointer ファイルポインタ
     */
    public static function fclose($handle){
        if(!fclose($handle)){
            // failed close
            AppLogger::errorLog("[Failed fclose]", __FILE__, __CLASS__, __LINE__);
        }
    }
    
    /**
     * Deletes a file
     * ファイルを削除する
     *
     * @param string $filename Path to the file ファイルへのパス
     */
    public static function unlink($filename){
        if(!unlink($filename)){
            // failed unlink
            $ex = new AppException("[Failed unlink]");
            $ex->addError("repository_error_operate_file_system");
            throw $ex;
        }
    }
    
    /**
     * To create a system directory
     * システムディレクトリを作成する
     *
     * @param string $path Directory path to create 作成するディレクトリパス
     * @param string $permission Directory permissions ディレクトリのパーミッション
     */
    public static function makeSystemDirectory($path, $permission){
        if(!file_exists($path)){
            Repository_Components_Util_OperateFileSystem::mkdir($path, $permission);
        }
        Repository_Components_Util_OperateFileSystem::chmod($path, $permission);
    }
    
    /**
     * To change the permissions of the specified path
     * 指定したパスのパーミッションを変更する
     *
     * @param string $path Path パス
     * @param string $permission Permissions パーミッション
     */
    public static function chmod($path, $permission){
        if(!chmod($path, $permission)){
            // failed chmod
            $ex = new AppException("[Failed chmod] path: ". $path. " mode: ". $permission);
            $ex->addError("repository_error_operate_file_system");
            throw $ex;
        }
    }
}
?>
