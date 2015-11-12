<?php
// --------------------------------------------------------------------
//
<<<<<<< HEAD
// $Id: Upload.class.php 53594 2015-05-28 05:25:53Z kaede_matsushita $
=======
// $Id: Upload.class.php 3 2010-02-02 05:07:44Z atsushi_suzuki $
>>>>>>> 79feb9270c7c677534f19fc1f5ec8b3c86ef213a
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
 * [[機能説明]]
 *
 * @package     [[package名]]
 * @access      public
 */
//class Repository_Action_Edit_Import_Upload extends RepositoryAction
class Repository_Action_Edit_Itemtype_Upload
{

	var $Session = null;
	var $Db = null;
	var $uploadsAction = null;
	/**
	 * [[機能説明]]
	 *
	 * @access  public
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
