<?php

/**
 * Lock file management common classes
 * ロックファイル管理共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: LockPhysicalFile.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Lock file management common classes
 * ロックファイル管理共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_LockPhysicalFile
{
    /**
     * Lock the file
     * ファイルをロック
     *
     * @param int $itemId Item id アイテムID
     * @param int $attributeId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @return resource Lock file resource ロックファイルリソース
     */
    public function lockFile($itemId, $attributeId, $fileNo) {
        $lockName = $itemId."_".$attributeId."_".$fileNo;
        $lockFile = self::getLockPath($lockName);
        
        return $this->lock($lockFile);
    }
    
    /**
     * Unlock the file
     * ファイルをアンロック
     *
     * @param resource $handle Lock file resource ロックファイルリソース
     * @param int $itemId Item id アイテムID
     * @param int $attributeId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     */
    public function unlockFile($handle, $itemId, $attributeId, $fileNo) {
        if($handle !== null){
            $lockName = $itemId."_".$attributeId."_".$fileNo;
            $lockFile = self::getLockPath($lockName);
            
            $this->unlock($handle, $lockFile);
        }
    }
    
    /**
     * To lock in the specified lock file path
     * 指定されたロックファイルパスでロックする
     *
     * @param string $lockFile Lock file path ロックファイルパス
     * @return resource Lock file resource ロックファイルリソース
     */
    private function lock($lockFile) {
        $ret = null;
        $handle = fopen($lockFile, "w");
        if(flock($handle, LOCK_EX)) {
            $ret = $handle;
        } else {
            fclose($handle);
        }
        
        return $ret;
    }
    
    
    /**
     * To lock in the specified lock file path
     * 指定されたロックファイルパスでロックする
     *
     * @param resource $handle Lock file resource ロックファイルリソース
     * @param string $lockFile Lock file path ロックファイルパス
     */
    private function unlock($handle, $lockFile) {
        flock($handle, LOCK_UN);
        fclose($handle);
        unlink($lockFile);
    }
    
    /**
     * Get the lock file path
     * ロックファイルパスを取得
     * 
     * @param string $lockName Lock file name ロックファイル名
     * @return string Lock file path ロックファイルパス
     */
    private function getLockPath($lockName){
        $lockFile = "";
        $dirPath = BASE_DIR.'/webapp/uploads/repository/';
        $lockFile = $dirPath.$lockName.".lock";
        
        return $lockFile;
    }
}
?>
