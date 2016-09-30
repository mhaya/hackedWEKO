<?php

/**
 * Upload file acquisition action class on the management screen
 * 管理画面でのアップロードファイル取得アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Adminupload.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Upload file acquisition action class on the management screen
 * 管理画面でのアップロードファイル取得アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Adminupload
{
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    public $Session = null;
    /**
     * DB object
     * Dbコンポーネントを受け取る
     *
     * @var DbObjectAdodb
     */
    public $Db = null;
    /**
     * Upload file management objects
     * アップロードファイル管理オブジェクト
     *
     * @var Uploads_Action
     */
    public $uploadsAction = null;

    /**
     * Upload file acquisition
     * アップロードファイル取得
     *
     * @access  public
     */
    function execute()
    {
        $garbage_flag = 1;
        
        // get upload file data
        $filelist = $this->uploadsAction->uploads($garbage_flag);
        for ($ii = 0; $ii < count($filelist); $ii++){
            if ($filelist[$ii]['upload_id'] === 0) {
                return false;
            }
        }
        // set to Session upload image file
        $this->Session->removeParameter("repositoryAdminFileList");
        $this->Session->setParameter("repositoryAdminFileList", $filelist[0]);
        
        return true;
    }
}
?>
