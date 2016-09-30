<?php

/**
 * Database constant class
 * データベース定数クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryDatabaseConst.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Database constant class
 * データベース定数クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositoryDatabaseConst
{
    /**
      * repository_cover_delete_status
      */
    // status
    /**
     * Cover page without deleting situation
     * カバーページ削除状況なし
     * 
     * @var int
     */
    const COVER_DELETE_STATUS_NONE = null;
    /**
     * Cover page Delete situation (not yet implemented)
     * カバーページ削除状況(未実施)
     * 
     * @var int
     */
    const COVER_DELETE_STATUS_NOTYET = "0";
    /**
     * Cover page Delete circumstances (implementation)
     * カバーページ削除状況(実施)
     * 
     * @var int
     */
    const COVER_DELETE_STATUS_DONE = "1";

    /**
      * repository_robotlist_data_status
      */
    // status
    /**
     * Robot list usage (non-execution)
     * ロボットリスト使用状況(非実施)
     * 
     * @var int
     */
    const ROBOTLIST_DATA_STATUS_DISABLED = "-1";
    /**
     * Robot list usage (not delete)
     * ロボットリスト使用状況(未削除)
     * 
     * @var int
     */
    const ROBOTLIST_DATA_STATUS_NOTDELETED = "0";
    /**
     * Robot list usage (Deleted)
     * ロボットリスト使用状況(削除済み)
     * 
     * @var int
     */
    const ROBOTLIST_DATA_STATUS_DELETED = "1";

    /**
      * repository_robotlist_master_is_robotlist_use
      */
    // status
    /**
     * Robot list usage (not used)
     * ロボットリスト使用状況(未使用)
     * 
     * @var int
     */
    const ROBOTLIST_MASTER_NOTUSED = 0;
    /**
     * Robot list usage (use)
     * ロボットリスト使用状況(使用)
     *
     * @var int
     */
    const ROBOTLIST_MASTER_USED = 1;
}
?>
