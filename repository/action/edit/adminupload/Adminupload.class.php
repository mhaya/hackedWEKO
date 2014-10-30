<?php
// --------------------------------------------------------------------
//
// $Id: Adminupload.class.php 535 2012-12-07 09:26:24Z ivis $
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
 * [[機能説明]]
 *
 * @package     [[package名]]
 * @access      public
 */
class Repository_Action_Edit_Adminupload
{
    public $Session = null;
    public $Db = null;
    public $uploadsAction = null;

    /**
     * [[機能説明]]
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
