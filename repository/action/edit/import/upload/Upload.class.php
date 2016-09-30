<?php

/**
 * Import action class
 * インポートアクションクラス
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

//class Repository_Action_Edit_Import_Upload extends RepositoryAction
/**
 * Import file upload action class
 * インポートファイルアップロードアクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Import_Upload
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
	 * To upload a file by import
	 * インポートによりファイルをアップロードする
	 */
	function execute()
	{
		// ガーベージフラグが"1"の場合、いつかファイル・DB共にクリアしてくれる。
		// ただし、詳細なタイミングは不明。
		$garbage_flag = 1;

		// アップロードしたファイルの情報を取得する。
		// 形式はuploadテーブルをSELECT *した結果と同等。
		$filelist = $this->uploadsAction->uploads($garbage_flag);
		for ($ii = 0; $ii < count($filelist); $ii++){
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
