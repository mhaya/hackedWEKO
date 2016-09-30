<?php

/**
 * Item type icon upload class
 * アイテムタイプアイコンアップロードクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: UploadIcon.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Item type icon upload class
 * アイテムタイプアイコンアップロードクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Edit_Itemtype_Uploadicon
{
	// 使用コンポーネントを受け取るため
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
    function execute()
    {
		//ガーベージフラグが"1"の場合、いつかファイル・DB共にクリアしてくれる。
		// ただし、詳細なタイミングは不明。
		$garbage_flag = 1;
		//アップロードしたファイルの情報を取得する。
		//形式はuploadテーブルをSELECT *した結果と同等。
		$itemtype_icon = $this->uploadsAction->uploads($garbage_flag);
		for ($ii = 0; $ii < count($itemtype_icon); $ii++){
			if ($itemtype_icon[$ii]['upload_id'] === 0) {
				return false;
			}
		}
		//sessionにアップロードしたファイルの情報を設定
		$this->Session->removeParameter("itemtype_icon");
		$this->Session->setParameter("itemtype_icon",$itemtype_icon);

		//'success'ではなく、trueを返す
		return true;
    }
}
?>
