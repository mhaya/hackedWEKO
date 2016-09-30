<?php

/**
 * Tree thumbnail upload class
 * ツリーサムネイルアップロードクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Treethumbnail.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Tree thumbnail upload class
 * ツリーサムネイルアップロードクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Edit_Treethumbnail
{
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    var $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
    var $Db = null;
    /**
     * Upload file management objects
     * アップロードファイル管理オブジェクト
     *
     * @var Uploads_Action
     */
    var $uploadsAction = null;

    /**
     * Execute
     * 実行
     *
     * @return bool true/false success/failed 成功/失敗
     */
    function execute()
    {
        $garbage_flag = 1;
        
        // get upload file data
        $filelist = $this->uploadsAction->uploads($garbage_flag);
        if(count($filelist) > 0){
            if ($filelist[0]['upload_id'] === 0) {
                // upload file none
                return true;
            } else if(strpos($filelist[0]["mimetype"],"image") === false){
                // not image file
                return false;
            }
            // set to Session uoload image file
            $this->Session->setParameter("tree_thumbnail", $filelist[0]);
        }
        
        // upload file none
        return true;
    }
}
?>
