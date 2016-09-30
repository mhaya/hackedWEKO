<?php

/**
 * Action class for WEKO import format downloads of items
 * アイテムのWEKOインポート形式ダウンロード用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Detaildownload.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Item export processing common classes
 * アイテムエクスポート処理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/action/main/export/ExportCommon.class.php';
/**
 * Common class file download
 * ファイルダウンロード共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';
/**
 * Item authority common classes
 * アイテム権限共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryItemAuthorityManager.class.php';

/**
 * Action class for WEKO import format downloads of items
 * アイテムのWEKOインポート形式ダウンロード用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Main_Export_Detaildownload extends RepositoryAction
{
	// リクエストパラメータを受け取るため
	//var $item_type_id = null;		//前画面で選択したアイテムタイプID(編集時)
    /**
     * Item id
     * アイテムID
     *
     * @var int
     */
	var $item_id = null;
    /**
     * Item serial number
     * アイテム通番
     *
     * @var int
     */
	var $item_no = null;
	/**
	 * All file download flag
	 * 全ファイルダウンロードフラグ
	 *
	 * @var string
	 */
	var $check_radio = null;
	/**
	 * Download flag of each file
	 * 各ファイルのダウンロードフラグ
	 *
	 * @var string
	 */
	var $license_check = null;
	
	// ダウンロード用メンバ
    /**
     * Data upload objects
     * データアップロードオブジェクト
     *
     * @var Uploads_View
     */
	var $uploadsView = null;
	
    /**
     * Export item
     * アイテムをエクスポートする
     *
     * @access  public
     */
    function executeApp()
    {
    	try {
    	    $this->exitFlag = true;
    	    
			if ($this->Session != null) {
				// エラー処理を記述する。（未実装）
				// return 'false'
			}
			
			//$license_check = _GET['license_status'];
			
			//print("check_radio=".$this->check_radio.'<br>');
			//print("license_check=".$this->license_check.'<br>');
			
			// $this->license_checkを「_」で分割
			$license_agree_file_no = array();
            if(strlen($this->license_check) > 0){
                $license_agree_file_no = explode("_", $this->license_check);
            }

			// 共通の初期処理
			$result = $this->initAction();
			if ( $result == false ){
				// 未実装
				print "初期処理でエラー発生";
			}
			
			// 現在表示されている詳細情報のレコードの集合
			$result = $this->getItemData($this->item_id,$this->item_no,$item_infos,$Error_Msg);
			if($result == false){
    			$this->Session->setParameter("error_msg",$Error_Msg);
    			$this->failTrans();                                 //トランザクション失敗を設定(ROLLBACK)
    			//アクション終了処理
				$result = $this->exitAction();     //トランザクションが成功していればCOMMITされる
				//return 'error';
			}
			if(count($item_infos) == 0){
				$this->failTrans();                                 //トランザクション失敗を設定(ROLLBACK)
    			//アクション終了処理
				$result = $this->exitAction();     //トランザクションが成功していればCOMMITされる
				$Error_Msg = "対称が存在しません";
				//return 'error';
			}
			// セッションに保存
			$this->Session->removeParameter("item_info");
            // Bugfix close item download 2011/06/09 --start--
            $user_auth_id = $this->Session->getParameter("_user_auth_id");
            $user_id = $this->Session->getParameter("_user_id");
            $auth_id = $this->getRoomAuthorityID($user_id);
            
            // Add Advanced Search 2013/11/26 R.Matsuura --start--
            $itemAuthorityManager = new RepositoryItemAuthorityManager($this->Session, $this->dbAccess, $this->TransStartDate);
            // Add Advanced Search 2013/11/26 R.Matsuura --end--
            
            // check authority
            if($user_auth_id >= $this->repository_admin_base && $auth_id >= $this->repository_admin_room){
                // is admin
            }
            else if($user_id != "0" && $item_infos['item'][0]['ins_user_id'] == $user_id){
                // is insert user
            }
            else if(!($itemAuthorityManager->checkItemPublicFlg($this->item_id, $this->item_no, $this->repository_admin_base, $this->repository_admin_room))){
                // close item.
                return false;
            }
            else {
                // open item.
            }
            // Bugfix close item download 2011/06/09 --start--
			
			// 作業用ディレクトリ作成
            $this->infoLog("businessWorkdirectory", __FILE__, __CLASS__, __LINE__);
            $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness("businessWorkdirectory");
            
            $tmp_dir = $businessWorkdirectory->create();
            
			// Exportファイルはimport.txt固定（仮）とする
			$filename = $tmp_dir . "import.xml";

			$buf = "<?xml version=\"1.0\"?>\n" .
				   "	<export>\n";

			// 同意されているファイルライセンスを受け取る
			$has_license = null;
			$where_clause = "";
			if(count($license_agree_file_no) > 0){
				if($this->check_radio == "true"){
					$has_license = true;
				} else {
					$has_license = null;
				}
				$where_clause .= "AND ( ";
				for($nCnt=0;$nCnt<count($license_agree_file_no);$nCnt=$nCnt+2){
					if($nCnt != 0){
						$where_clause .= " OR ";
					}
					$where_clause .= "(attribute_id = " . $license_agree_file_no[$nCnt] ." AND ";
					$where_clause .= "file_no = " . $license_agree_file_no[$nCnt+1] . " )";
				}
				$where_clause .= " ) ";				
			}
			
			// 指定されているアイテムから付随する情報を取得する
			$output_files = array();
			// Exportファイル生成
			$export_common = new ExportCommon($this->Db, $this->Session, $this->TransStartDate);
			if($export_common === null){
				return false;
			}
			$export_info = $export_common->createExportFile($item_infos['item'][0], $tmp_dir, $has_license, $where_clause);

			// Zipファイル生成
			$zip_file = "export.zip";

			// ファイルオープン
			$fp = fopen( $filename, "w" );
			if (!$fp){
				// ファイルのオープンに失敗した場合
				// エラー処理を実行（未実装）
	//				echo "ファイルオープンエラー<br>";
			}
			
			//print_r( "BUF=" . $export_info["buf"]);

			$buf .= $export_info["buf"];
			$buf .= "	</export>\n";
				
			// Txtファイルへ出力する
			fputs($fp, $buf);
			if ($fp){
				fclose($fp);
			}

			// 出力したファイルをZip形式で圧縮する
			$output_files = $export_info["output_files"];
			array_push( $output_files, $filename );
			
			// zip用の一時ディレクトリを作成
            $tmp_dir_zip = $businessWorkdirectory->create();
            
			File_Archive::extract(
				File_Archive::read($tmp_dir),
				File_Archive::toArchive($zip_file, File_Archive::toFiles( $tmp_dir_zip ))
			);
			
			//ダウンロードアクション処理
			// Add RepositoryDownload action 2010/03/30 A.Suzuki --start--
			$repositoryDownload = new RepositoryDownload();
			$repositoryDownload->downloadFile($tmp_dir_zip.$zip_file, "export.zip");
			// Add RepositoryDownload action 2010/03/30 A.Suzuki --end--
            
			// アクション終了処理
			$result = $this->exitAction();	// トランザクションが成功していればCOMMITされる
			if ( $result == false ){
				// 未実装
				print "終了処理失敗";
			}			
		} catch ( RepositoryException $exception){
			// 未実装
		}
    }
}
?>
