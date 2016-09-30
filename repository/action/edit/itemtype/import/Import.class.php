<?php

/**
 * Action class for the item type import
 * アイテムタイプインポート用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Import.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * File archive library class
 * ファイルアーカイブライブラリクラス
 */
include_once MAPLE_DIR.'/includes/pear/File/Archive.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Import common class
 * インポート汎用処理クラス
 */
require_once WEBAPP_DIR.'/modules/repository/action/edit/import/ImportCommon.class.php';

/**
 * Action class for the item type import
 * アイテムタイプインポート用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Itemtype_Import extends RepositoryAction
{
	// component
	/**
	 * Session management objects
	 * Session管理オブジェクト
	 *
	 * @var Session
	 */
	var $Session = null;

	/**
	 * Execute
	 * 実行
	 *
	 * @return string "success"/"error" success/failed 成功/失敗
	 * @throws RepositoryException
	 */
	function execute()
	{
		try {
			
			/////////////////////////////////////////////////////
			// init action
			/////////////////////////////////////////////////////
			$result = $this->initAction();
			if ( $result == false ){
				// 未実装
				$this->Session->setParameter("error_msg", "init error");
			}
			
			$this->Session->removeParameter("error_msg");
			
			/////////////////////////////////////////////////////
			// extrac upload zip file
			/////////////////////////////////////////////////////
			$tmp_dir = $this->extraction();
			if($tmp_dir == false){
				// not zip file
				$this->Session->setParameter("error_msg", "select file is not zip file");
				return 'error';
			}
			
			/////////////////////////////////////////
			// import item type
			/////////////////////////////////////////
			// import common class new
			$import_common = new ImportCommon($this->Session, $this->Db, $this->TransStartDate);
			// get XML data
			// get XML data
			$error_list = array();
			$return = $import_common->XMLAnalysis($tmp_dir, $array_item_data, $error_list);
			if($return === false){
				//echo "XML不備";
				// error action
				$exception = new RepositoryException( "ERR_MSG_xxx-xxx1", 001 );
				// ROLLBACK
				$this->failTrans();
				throw $exception;
			}
			// Insert item type
			$result = $import_common->itemtypeEntry($array_item_data['item_type'], $tmp_dir, $item_type_info, $error_msg);
			if($result === false){
				// error action
				$exception = new RepositoryException( "ERR_MSG_xxx-xxx1", 001 );
				// ROLLBACK
				$this->failTrans();
				throw $exception;
			}
			$insert_itemtype = array();
			for($ii=0;$ii<count($item_type_info);$ii++){
				if( !(in_array($item_type_info[$ii]['item_type_name'],$insert_itemtype)) ){
					array_push($insert_itemtype, $item_type_info[$ii]['item_type_name']);
				}
			}
			
			$this->Session->setParameter("import_item_type_info", $insert_itemtype);
			
			// del work dir
			$this->removeDirectory($tmp_dir);
			
			// end action
			$result = $this->exitAction();	// commit
			if ( $result === false ){
				// error action
				$exception = new RepositoryException( "ERR_MSG_xxx-xxx1", 001 );
				// ROLLBACK
				$this->failTrans();
				throw $exception;
			}
			$this->finalize();
			return 'success';
		
		} catch ( RepositoryException $Exception) {
			// error log 
//			$this->logFile(
//				"Import",
//				"execute",
//				$Exception->getCode(),
//				$Exception->getMessage(),
//				$Exception->getDetailMsg() );
			// end action
			$result = $this->exitAction();
			return "error";
		}
	}
	
	/**
	 * Extraction import ZIP file
	 * インポートZIPファイル解凍
	 *
	 * @return string|bool extracted file path/false 解凍されたファイルのパス/失敗
	 */
	function extraction(){

		// get upload file
		$tmp_file = $this->Session->getParameter("filelist");

		if($tmp_file[0]['extension'] != "zip"){
			unlink($file_path);
			return false;
		}
		
		//$dir_path = WEBAPP_DIR. "\\uploads\\repository\\";
		$dir_path = WEBAPP_DIR. "/uploads/repository/";
		$file_path = $dir_path . $tmp_file[0]['physical_file_name'];

		// mkdir for extrac
        $this->infoLog("businessWorkdirectory", __FILE__, __CLASS__, __LINE__);
        $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness('businessWorkdirectory');
        $dir = $businessWorkdirectory->create();
        $dir = substr($dir, 0, -1);
        
		// extrac file
		File_Archive::extract(
		File_Archive::read($file_path . "/"),
		File_Archive::appender($dir)
		);
		
		// delete upload xip file
		unlink($file_path);
		
		return $dir;
	}
}
?>
