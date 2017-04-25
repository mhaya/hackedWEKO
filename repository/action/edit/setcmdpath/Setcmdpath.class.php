<?php

/**
 * Action class for the automatic setting of the external command absolute path
 * 外部コマンド絶対パスの自動設定用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Setcmdpath.class.php 73468 2016-10-26 04:53:37Z tomohiro_ichikawa $
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
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * Action class for the automatic setting of the external command absolute path
 * 外部コマンド絶対パスの自動設定用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Setcmdpath extends RepositoryAction
{
	//コンポーネント取得
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
	
	// リクエストパラメタ (配列ではなく個別で渡す)
    /**
     * Showing tab information
     * 表示中タブ情報
     *
     * @var int
     */
	var $admin_active_tab = null;
	
	/**
	 * To get the external command absolute path
	 * 外部コマンド絶対パスを取得する
	 *
	 * @access  public
	 * @return string Result 結果
	 */
	function executeApp()
	{
		// 表示中タブ情報
		$this->Session->setParameter("admin_active_tab", $this->admin_active_tab);
		
		// ----------------------------------------------------
		// 準備処理
		// ----------------------------------------------------
		$edit_start_date = $this->Session->getParameter("edit_start_date");
		$Error_Msg = '';			// エラーメッセージ
		$user_id = $this->Session->getParameter("_user_id");	// ユーザID
		$params = null;				// パラメタテーブル更新用クエリ
		$params[] = '';				// param_value
		$params[] = $user_id;				// mod_user_id
		$params[] = $this->TransStartDate;	// mod_date
		$params[] = '';				// param_name
		
		// ----------------------------------------------------
		// 更新時の管理パラメタの読み込み
		// ----------------------------------------------------
		$admin_records = array();		// 管理パラメタレコード列
		$Error_Msg = '';				// エラーメッセージ
		// パラメタテーブルの全属性を取得
		$result = $this->getParamTableRecord($admin_records, $Error_Msg);
		if ($result === false) {
			$tmpStr = sprintf("item_coef_cp update failed : %s", $this->Db->ErrorMsg());
			$this->Session->setParameter("error_msg", $tmpStr);
			throw new AppException($tmpStr);
		}
		
		// ----------------------------------------------------
		// 更新開始時刻 > 最終更新日になっているか検査
		// ※１つでも編集中に他の管理者に変更されたパラメタがあればアウト
		// ----------------------------------------------------
		$admin_records_old = $this->Session->getParameter("admin_params");
		foreach( $admin_records as $key => $value ){
			// 編集開始時の更新日時が変わっている場合、更新を許さない
			if( $admin_records_old[$key]['mod_date'] != $value['mod_date'] ) {
				$tmpStr = "error : probably " . $key . " was updated by other admin.";
				$this->Session->setParameter("error_msg", $tmpStr);
				throw new AppException($tmpStr);
			}
		}
		// ------------------------------------------------
		// コマンドのパスが通っていない場合、anonymousが取得できる
		// 環境変数に指定されるフォルダ以下にコマンドがあれば登録する
		// ------------------------------------------------
		// Session情報から、パスの現状を取得
		$admin_params = $this->Session->getParameter("admin_params");
		// 環境変数取得
		// OSを自動で判別する
		if(PHP_OS == "Linux" || PHP_OS == "MacOS"){
			exec("printenv PATH", $path);
		} else if(PHP_OS == "WIN32" || PHP_OS == "WINNT"){
			exec("PATH", $path);
			$path = str_replace("PATH=", "", $path);
		} else {
			$path = null;
		}
		$path = split(PATH_SEPARATOR,$path[0]);
		for($ii=0;$ii<count($path);$ii++){
			// 取得した環境変数には最後にディレクトリセパレータがない場合は追加
			if(strlen($path[$ii]) > 0 && $path[$ii][strlen($path[$ii])-1] != DIRECTORY_SEPARATOR){
				$path[$ii] .= DIRECTORY_SEPARATOR;
			}
			// wvWareコマンドまでの絶対パス
			if( $admin_params['path_wvWare']['path']=="false"){
				// 環境変数に指定されているフォルダ内を検索
				if(file_exists($path[$ii]."wvHtml")){
					// パスが通ったので更新
					$params[0] = $path[$ii];		// param_value
					$params[3] = 'path_wvWare';		// param_name
					$result = $this->updateParamTableData($params, $Error_Msg);
					if ($result === false) {
						$tmpStr = sprintf("path_wvWare update failed : %s", $this->Db->ErrorMsg());
						$this->Session->setParameter("error_msg", $tmpStr);
						throw new AppException($tmpStr);
					}
				}
			}
			// xlhtmlコマンドまでの絶対パス
			if( $admin_params['path_xlhtml']['path']=="false"){
				// 環境変数に指定されているフォルダ内を検索
				if(file_exists($path[$ii]."xlhtml") || file_exists($path[$ii]."xlhtml.exe")){
					// パスが通ったので更新
					$params[0] = $path[$ii];		// param_value
					$params[3] = 'path_xlhtml';		// param_name
					$result = $this->updateParamTableData($params, $Error_Msg);
					if ($result === false) {
						$tmpStr = sprintf("path_xlhtml update failed : %s", $this->Db->ErrorMsg());
						$this->Session->setParameter("error_msg", $tmpStr);
						throw new AppException($tmpStr);
					}
				}
			}
			// popplerコマンドまでの絶対パス
			if( $admin_params['path_poppler']['path']=="false"){
				// 環境変数に指定されているフォルダ内を検索
				if(file_exists($path[$ii]."pdftotext") || file_exists($path[$ii]."pdftotext.exe")){
					// パスが通ったので更新
					$params[0] = $path[$ii];			// param_value
					$params[3] = 'path_poppler';		// param_name
					$result = $this->updateParamTableData($params, $Error_Msg);
					if ($result === false) {
						$tmpStr = sprintf("path_poppler update failed : %s", $this->Db->ErrorMsg());
						$this->Session->setParameter("error_msg", $tmpStr);
						throw new AppException($tmpStr);
					}
				}
			}
			// ImageMagickコマンドまでの絶対パス
			if( $admin_params['path_ImageMagick']['path']=="false"){
				// 環境変数に指定されているフォルダ内を検索
				if(file_exists($path[$ii]."convert") || file_exists($path[$ii]."convert.exe")){
					// パスが通ったので更新
					$params[0] = $path[$ii];				// param_value
					$params[3] = 'path_ImageMagick';		// param_name
					$result = $this->updateParamTableData($params, $Error_Msg);
					if ($result === false) {
						$tmpStr = sprintf("path_ImageMagick update failed : %s", $this->Db->ErrorMsg());
						$this->Session->setParameter("error_msg", $tmpStr);
						throw new AppException($tmpStr);
					}
				}
			}
			// Get PATH for PDFTK
			if( $admin_params['path_pdftk']['path']=="false"){
				// Search command
				if(file_exists($path[$ii]."pdftk") || file_exists($path[$ii]."pdftk.exe") || file_exists($path[$ii]."qpdf") || file_exists($path[$ii]."qpdf.exe")){
					// Update
					$params[0] = $path[$ii];    // param_value
					$params[3] = 'path_pdftk';  // param_name
					$result = $this->updateParamTableData($params, $Error_Msg);
					if ($result === false) {
						$tmpStr = sprintf("path_pdftk update failed : %s", $this->Db->ErrorMsg());
						$this->Session->setParameter("error_msg", $tmpStr);
						throw new AppException($tmpStr);
					}
				}
			}
			// Add multimedia support 2012/08/27 T.Koyasu -start-
			// get path for ffmpeg
			if( $admin_params['path_ffmpeg']['path'] == "false"){
				// search command
				if(file_exists($path[$ii]."ffmpeg") || file_exists($path[$ii]."ffmpeg.exe")){
					// update
					$params[0] = $path[$ii];
					$params[3] = "path_ffmpeg";
					$result = $this->updateParamTableData($params, $Error_Msg);
					if ($result === false) {
						$tmpStr = sprintf("path_ffmpeg update failed : %s", $this->Db->ErrorMsg());
						$this->Session->setParameter("error_msg", $tmpStr);
						throw new AppException($tmpStr);
					}
				}
			}
			// Add multimedia support 2012/08/27 T.Koyasu -end-
			// Add external search word 2014/05/23 K.Matsuo -start-
			// get path for mecab
			if( $admin_params['path_mecab']['path'] == "false"){
				// search command
				if(file_exists($path[$ii]."mecab") || file_exists($path[$ii]."mecab.exe")){
					// update
					$params[0] = $path[$ii];
					$params[3] = "path_mecab";
					$result = $this->updateParamTableData($params, $Error_Msg);
					if ($result === false) {
						$tmpStr = sprintf("path_mecab update failed : %s", $this->Db->ErrorMsg());
						$this->Session->setParameter("error_msg", $tmpStr);
						throw new AppException($tmpStr);
					}
				}
			}
			// Add external search word 2014/05/23 K.Matsuo -end-
			// PHP
			if( $admin_params['path_php']['path'] == "false"){
				// search command
				if(file_exists($path[$ii]."php") || file_exists($path[$ii]."php.exe")){
					// update
					$params[0] = $path[$ii];
					$params[3] = "path_php";
					$result = $this->updateParamTableData($params, $Error_Msg);
					if ($result === false) {
						$tmpStr = sprintf("path_php update failed : %s", $this->Db->ErrorMsg());
						$this->Session->setParameter("error_msg", $tmpStr);
						throw new AppException($tmpStr);
					}
				}
			}
		}
		
		//セッションの初期化
		$this->Session->removeParameter("admin_params");
		$this->Session->removeParameter("edit_start_date");
		$this->Session->removeParameter("error_msg");
		
		// アクション終了処理
		return 'success';
	}
}
?>
