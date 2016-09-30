<?php

/**
 * Action class for WEKO uninstall
 * WEKOアンインストール用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Uninstall.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * ZIP file manipulation library
 * ZIPファイル操作ライブラリ
 */
include_once MAPLE_DIR.'/includes/pear/File/Archive.php';

/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * Action class for WEKO uninstall
 * WEKOアンインストール用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_main_Uninstall extends RepositoryAction
{
    /**
     * Uninstall WEKO
     * WEKOをアンインストールする
     *
     * @return boolean Execution result 実行結果
     */
	function execute()
	{
		// init action
		$this->initAction();
		
		//////////////////////////////
		// delete weko directry 
		//////////////////////////////
		$dir = HTDOCS_DIR."/weko";	// NetCommons2/htdocs/weko
		if (file_exists($dir)) {
			$this->removeDirectory($dir);
			if (file_exists($dir)) {
				echo "not delete directory<br/>";
			}
		}
		
		//////////////////////////////
		// delete log report file
		//////////////////////////////
		// check directry
		$dir = HTDOCS_DIR."/_repository/";	// NetCommons2/htdocs/_repository
		if (file_exists($dir)) {
			$this->removeDirectory($dir);
			if (file_exists($dir)) {
				echo "not delete directory<br/>";
			}
		}
		
		//////////////////////////////
		// delete file contents
		//////////////////////////////
		$dir = $this->getFileSavePath("file");
		if(strlen($dir) == 0){
			// default directory
			$dir = BASE_DIR.'/webapp/uploads/repository/files';
		}
		if (file_exists($dir)) {
			$this->removeDirectory($dir);
			if (file_exists($dir)) {
				echo "not delete directory at save file<br/>";
			}
		}
		
		// delete flash contents
		$dir = $this->getFileSavePath("flash");
		if(strlen($dir) == 0){
			// default directory
			$dir = BASE_DIR.'/webapp/uploads/repository/flash';
		}
		if (file_exists($dir)) {
			$this->removeDirectory($dir);
			if (file_exists($dir)) {
				echo "not delete directory at save flash<br/>";
			}
		}
		// Add flash save directory 2010/01/06 A.Suzuki --end--
		
		///////////////////////////////
		// delete weko logs directry
		///////////////////////////////
		$dir = WEBAPP_DIR."/logs/weko";
		if (file_exists($dir)) {
			$this->removeDirectory($dir);
			if (file_exists($dir)) {
				echo "not delete directory at save log<br/>";
			}
		}
		
		// Add private tree 2013/06/27 --start--
		$define_inc_file_path = WEBAPP_DIR. '/modules/repository/config/define.inc.php';
		if(is_writable($define_inc_file_path)){
			// Add file rewrite for privatetree edit tab authority K.Matsuo 2013/04/24 --start--
			$fp = fopen($define_inc_file_path, "r");
			$define_inc_text = array();
			while ($line = fgets($fp)) {
				$define_inc_text[] = $line;
			}
			fclose($fp);
			$fp = fopen($define_inc_file_path, "w");
			for($ii = 0; $ii < count($define_inc_text); $ii++){
				if(strpos($define_inc_text[$ii], "CHANGE_TEXT_PRIVATETREE") !== false){
					$define_inc_text[$ii] = 'define("_REPOSITORY_PRIVATETREE_AUTHORITY", REPOSITORY_ADMIN_MORE);		// CHANGE_TEXT_PRIVATETREE'.PHP_EOL;
				}
				fwrite($fp, $define_inc_text[$ii]);
			}
			fclose($fp);
		}
		// Add private tree 2013/06/27 --end--
		// end action
		$this->exitAction();
		$this->finalize();
		return true;
	}
}
?>
