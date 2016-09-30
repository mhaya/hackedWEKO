<?php

/**
 * Item type upload class
 * アイテムタイプアップロードクラス
 *
 * @package     WEKO
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

/**
 * Item type upload class
 * アイテムタイプアップロードクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Edit_Itemtype_Upload
{
	/**
	 * Session management objects
	 * Session管理オブジェクト
	 *
	 * @var Session
	 */
	var $Session = null;
	/**
	 * DB object
	 * DBオブジェクト
	 *
	 * @var DbObjectAdodb
	 */
	var $Db = null;
	/**
	 * Data upload objects
	 * データアップロードオブジェクト
	 *
	 * @var Uploads_View
	 */
	var $uploadsAction = null;

	/**
	 * Execute
	 * 実行
	 *
	 * @return bool true/false success/failed 成功/失敗
	 */
	function executeApp()
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
