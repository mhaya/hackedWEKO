<?php

/**
 * Action class for the item type export
 * アイテムタイプエクスポート用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Export.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Export common class
 * エクスポート汎用処理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/action/main/export/ExportCommon.class.php';
/**
 * Download class
 * ダウンロード処理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';

/**
 * Action class for the item type export
 * アイテムタイプエクスポート用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Itemtype_Export extends RepositoryAction
{
	// Request param
	/**
	 * Item type ID
	 * アイテムタイプID
	 *
	 * @var int
	 */
	var $item_type_id = null;
	
	// Component
	/**
	 * Uploads View components
	 * アップロードコンポーネントクラス
	 *
	 * @var Uploads_View
	 */
	var $uploadsView = null;
	/**
	 * Session management objects
	 * Session管理オブジェクト
	 *
	 * @var Session
	 */
	public $Session = null;
	/**
	 * Db object
	 * DBオブジェクト
	 *
	 * @var DbObjectAdodb
	 */
	public $Db = null;

	/**
	 * Execute
	 * 実行
	 *
	 * @return bool true/false success/failed 成功/失敗
	 * @throws RepositoryException
	 */
	function execute()
	{
		try {
			////////// Init //////////
			$result = $this->initAction();
			if ( $result === false ){
				$exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
				$DetailMsg = null;							  //詳細メッセージ文字列作成
				sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
				$exception->setDetailMsg( $DetailMsg );			 //詳細メッセージ設定
				$this->failTrans();										//トランザクション失敗を設定(ROLLBACK)
				throw $exception;
			}
			$buf = "";
			$output_files = array();
			// get export common
			$export_common = new ExportCommon($this->Db, $this->Session, $this->TransStartDate);
			
			////////// mkdir //////////
            $this->infoLog("businessWorkdirectory", __FILE__, __CLASS__, __LINE__);
            $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness('businessWorkdirectory');
            $tmp_dir = $businessWorkdirectory->create();
            $tmp_dir = substr($tmp_dir, 0, -1);
            
			////////// make xml text & icon file ///////////
			$buf = "<?xml version=\"1.0\"?>\n" .
					"<export>\n";
			$export_info = $export_common->createItemTypeExportFile($tmp_dir, $this->item_type_id);
			if($export_info === false){
				return false;
			}
			
			$buf .= $export_info["buf"];
			$buf .= "	</export>\n";
			
			////////// make xml file //////////
			$filename = $tmp_dir . "/import.xml";
			$fp = @fopen( $filename, "w" );
			if (!$fp){
				return false;
			}
			fputs($fp, $buf);
			if ($fp){
				fclose($fp);
			}
			////////// make zip file //////////
			array_push($output_files, $export_info["output_files"]);
			array_push($output_files, $filename );	// xml file name
			
			// make zip file
			$zip_file = "export.zip";
			File_Archive::extract(
				$output_files,
				File_Archive::toArchive($zip_file, File_Archive::toFiles( $tmp_dir."/" ))
			);
			
			/////////// Download //////////
			// DL action
			// Add RepositoryDownload action 2010/03/30 A.Suzuki --start--
			$repositoryDownload = new RepositoryDownload();
			$repositoryDownload->downloadFile($tmp_dir."/".$zip_file, "export.zip");
			//$this->uploadsView->download($bret, "export.zip");
			// Add RepositoryDownload action 2010/03/30 A.Suzuki --end--
			
			// del dir
			$this->removeDirectory($tmp_dir);

			// exit
			$result = $this->exitAction();
			if ( $result == false ){
				return false;
			}
			$this->finalize();
			exit();
			
		} catch ( RepositoryException $exception){
			return false;
		}
	}
}
?>
