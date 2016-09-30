<?php

/**
 * Name authority upload action class
 * 著者名典拠アップロードアクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Upload.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
//require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Action base class for WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/common/WekoAction.class.php';

//class Repository_Action_Edit_Importauthority_Upload extends RepositoryAction
/**
 * Name authority file upload action class
 * 著者名典拠ファイルアップロードアクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Importauthority_Upload extends WekoAction
{

    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    public $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
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
     * Author name authority file upload
     * 著者名典拠ファイルアップロード
     *
     * @access  public
     * @return boolean Result 結果
     */
    public function executeApp()
    {
        // ガーベージフラグが"1"の場合、いつかファイル・DB共にクリアしてくれる。
        // ただし、詳細なタイミングは不明。
        $garbage_flag = 1;

        // アップロードしたファイルの情報を取得する。
        // 形式はuploadテーブルをSELECT *した結果と同等。
        $filelist = $this->uploadsAction->uploads($garbage_flag);
        for ($ii = 0; $ii < count($filelist); $ii++){
            if(!isset($filelist[$ii]))
            {
                continue;
            }
            if ($filelist[$ii]['upload_id'] === 0) {
                return false;
            }
        }

        // sessionにアップロードしたファイルの情報を設定
        $this->Session->setParameter("filelist", $filelist);
        //'success'ではなく、trueを返す
        return true;
    }
}
?>
