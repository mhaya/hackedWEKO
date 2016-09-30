<?php

/**
 * ELS data download action class
 * ELSデータダウンロードアクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Download.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Download Action class of registered file in WEKO
 * WEKOに登録されたファイルのダウンロードアクションクラス
 */
require_once WEBAPP_DIR. '/modules/repository/action/common/download/Download.class.php';
/**
 * Common class file download
 * ファイルダウンロード共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';

/**
 * ELS data download action class
 * ELSデータダウンロードアクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Cinii_Els_Download extends RepositoryAction
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
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
	var $Db = null;
    /**
     * Data upload objects
     * データアップロードオブジェクト
     *
     * @var Uploads_View
     */
	var $uploadsView = null;
	
	/**
	 * Run download 
	 * ダウンロード実行
	 */
	function execute()
	{
		try {
			$result = $this->initAction();
	        if ( $result === false ) {
	            $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );
	            $DetailMsg = null;
	            sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
	            $exception->setDetailMsg( $DetailMsg );
	            $this->failTrans(); // ROLLBACK
	            throw $exception;
	        }
			
	        $buf = $this->Session->getParameter("els_data");
	        $this->Session->removeParameter("els_data");
	        $this->Session->removeParameter("els_download");
	        $this->Session->removeParameter("els_auto_entry");

			// change encoding
			$buf = mb_convert_encoding($buf, "SJIS", "UTF-8");
			
			$now_time = $this->TransStartDate;
			$now_time = str_replace("-", "", $now_time);
			$now_time = str_replace(" ", "_", $now_time);
			$now_time = str_replace(":", "", $now_time);
			$now_time = str_replace(".", "", $now_time);
			$now_time = substr($now_time, 0, 15);	// YYYYMMDD_hhmmssの形式にする
			
			//Add Download zip file in tsv 2009/08/24 K.Ito --start--
			//$date = date("YmdHis");
			$query = "SELECT DATE_FORMAT(NOW(), '%Y%m%d%H%i%s') AS now_date;";
			$result = $this->Db->execute($query);
			if($result === false || count($result) != 1){
				return false;
			}
			$date = $result[0]['now_date'];
			$tmp_dir = WEBAPP_DIR."/uploads/repository/_".$date;
			mkdir( $tmp_dir, 0777 );
			$file_name = $tmp_dir.DIRECTORY_SEPARATOR."ELS_data_".$now_time. ".tsv";
			$file_report = fopen($file_name, "w");
			fwrite($file_report, $buf);
			fclose($file_report);
			$output_files = array($file_name);
			// set zip file name
			$zip_file = "ELS_data_". $now_time. ".zip";
			// compress zip file	
			File_Archive::extract(
				$output_files,
				File_Archive::toArchive($zip_file, File_Archive::toFiles( $tmp_dir."/" ))
			);
			// -----------------------------------------------
			// download zip file
			// -----------------------------------------------
			// download
            $repositoryDownload = new RepositoryDownload();
			$repositoryDownload->downloadFile($tmp_dir."/".$zip_file, $zip_file);
			// delete tmp folder
			$this->removeDirectory($tmp_dir);
			//Add Download zip file in tsv 2009/08/24 K.Ito --end--
			
			// end action
			$result = $this->exitAction();	// COMMIT
			if ( $result == false ){
				// error
				return false;
			}
			
			// end
			exit();
			
		}
		catch ( RepositoryException $Exception) {
			//end action
		  	$this->exitAction(); // ROLLBACK
			
			//error
			return false;
		}
	}
	
    /**
     * An item info change to ELS format
     * アイテム情報をELS形式に変換する
     *
     * @param array $Result_List Item information アイテム情報
     *                     array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                     array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                     array["item_attr_type"]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                     array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                     array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                     array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                     array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                     array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                     array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                     array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $els_text Els format data ELS形式データ
     *                        array[$ii]
     * @param result $Ret_Msg Message メッセージ
     * @return boolean Execution result 実行結果
     */
	function getElsText($Result_List, &$els_text, &$Ret_Msg){
		/////////// an item info change to ELS format //////////
		// init
		$els_text = array();
		for($ii=0;$ii<26;$ii++){
			$els_text[$ii] = "";
		}
		/////////////// Fixed value ///////////////
		// page attribute ***Indispensability***
		$els_text[3] = "P";
		
		/////////////// Base attribute ///////////////
		///// title /////
		if($this->lang == "japanese"){
			// title(ja)
			$els_text[4] = $Result_List["item"][0]["title"];
		} else {
			// title(en)
			$els_text[6] = $Result_List["item"][0]["title"];
		}
		///// lang /////
		$els_lang = $this->changeLangFormatToEls();
		if($els_lang == ""){
			// error
			$msg = $this->smartyAssign->getLang("repository_els_lang_error");
			$Ret_Msg = sprintf("%s : %s", $msg, $this->lang)."\r\n";
			return false;
		}
		$els_text[15] = $els_lang;
		///// keyword /////
		$keyword = $Result_List["item"][0]["serch_key"];
		$keyword = explode("|", $keyword);
		for($nCnt=0;$nCnt<count($keyword);$nCnt++){
			if($this->lang == "japanese"){
				if($els_text[18] != "" && $keyword[$nCnt] != ""){
					$els_text[18] .= " / ";
				}
				$els_text[18] .= $keyword[$nCnt];
			} else {
				if($els_text[19] != "" && $keyword[$nCnt] != ""){
					$els_text[19] .= " / ";
				}
				$els_text[19] .= $keyword[$nCnt];
			}
		}
        // Modified to output the URL without file metadata. 2012/11/13 A.Suzuki --start--
        $els_text[22] = $Result_List["item"][0]["uri"];
        // Modified to output the URL without file metadata. 2012/11/13 A.Suzuki --end--
		/////////////// item attribute ///////////////
		for($ii=0;$ii<count($Result_List["item_attr_type"]);$ii++){ // loop for attribute
			// have attribute
			$input_type = $Result_List["item_attr_type"][$ii]["input_type"];
			// have attr value
			$attr_info = $Result_List["item_attr"][$ii];
			
			///// biblio info /////
			if($input_type == "biblio_info"){
				//volume ***Indispensability***
				if($els_text[1] != ""){
					// Error
					$Ret_Msg = $this->smartyAssign->getLang("repository_els_voln_error");
					return false;
				}
				$els_text[1] = $attr_info[0]["volume"];
				if($attr_info[0]["volume"]!= "" && $attr_info[0]["issue"] != ""){
					$els_text[1] .=  "(".$attr_info[0]["issue"].")";
				}
				// dateofissued ***Indispensability*** YYYYMMDD or YYYYMM00 or YYYY0000
				if($attr_info[0]["date_of_issued"] != ""){
					if($els_text[2] != ""){
						// 書誌情報が複数ある場合はエラー
						$Ret_Msg = $this->smartyAssign->getLang("repository_els_year_error");
						return false;
					}
					$date = explode("-", $attr_info[0]["date_of_issued"]);
					if(strlen($date[0]) == 0){
						// 年が存在しない
						$Ret_Msg = $this->smartyAssign->getLang("repository_els_year_error");
						return false;
					}
					$els_text[2] .= $date[0];
					if(strlen($date[1]) > 0){
						$els_text[2] .= $date[1];
					} else {
						$els_text[2] .= "00";
					}
					if(strlen($date[2]) > 0){
						$els_text[2] .= $date[2];
					} else {
						$els_text[2] .= "00";
					}
				}
				// spage-epage
				if($attr_info[0]["start_page"] != ""){
					if($els_text[12] != ""){
						// Error
						$Ret_Msg = $this->smartyAssign->getLang("repository_els_page_error");
						return false;
					}
					$els_text[12] = $attr_info[0]["start_page"];
					if($attr_info[0]["end_page"] != "" && $attr_info[0]["end_page"] != $attr_info[0]["start_page"]){
						$els_text[12] .= "-".$attr_info[0]["end_page"];
					}
				}
            // Modified to output the URL without file metadata. 2012/11/13 A.Suzuki --start--
//			} else if($input_type == "file" || $input_type == "file_price"){
//				/////////////// URL ///////////////
//				// URL for PDF file 
//				if($els_text[22] != ""){
//					// PDF file is only
//					$Ret_Msg = $this->smartyAssign->getLang("repository_els_flnm_error");
//					return false;
//				}
//				if($attr_info[0]["extension"] == "pdf"){
//					// Url for download of not link to detailed page but text
//					// Add page_id, block_id, certificate 2008/10/09 Y.Nakao --start--
//					// change redirect url 2008/11/19 Y.Nakao --start--
//					//$block_info = $this->getBlockPageId();
//					$els_text[22] = BASE_URL."/?action=repository_uri".
//									"&item_id=".$Result_List["item"][0]["item_id"].
//									"&file_id=".$attr_info[0]["attribute_id"];
//					// change redirect url 2008/11/19 Y.Nakao --start--
//					// Add page_id, block_id, certificate 2008/10/09 Y.Nakao --start--
//				}
            // Modified to output the URL without file metadata. 2012/11/13 A.Suzuki --end--
			///// name /////
			} else if($input_type == "name"){
				for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
					if($attr_info[$nCnt]["family"] != "" && $attr_info[$nCnt]["name"] != ""){
						if($this->lang == "japanese"){
							if($els_text[7] != ""){
								$els_text[7] .= " / ";
							}
							$els_text[7] .= $attr_info[$nCnt]["family"]."," .$attr_info[$nCnt]["name"];
						} else {
							if($els_text[9] != ""){
								$els_text[9] .= " / ";
							}
							$els_text[9] .= $attr_info[$nCnt]["family"]."," .$attr_info[$nCnt]["name"];
						}
					}
				}
			} else {
				switch ($Result_List["item_attr_type"][$ii]["junii2_mapping"]){
					case "NCID": // ***Indispensability***
						if($els_text[0] != ""){
							// 雑誌書誌IDは一つでなければならないためエラー
							$Ret_Msg = $this->smartyAssign->getLang("repository_els_ncid_error");
							return false;
						}
						$els_text[0] = $attr_info[0]["attribute_value"];
						break;
						
					case "volume": // ***Indispensability***
						if(strlen($els_text[1]) > 0){
							$issue = $els_text[1];
							if($issue[0]!="("){
								$Ret_Msg = $this->smartyAssign->getLang("repository_els_voln_error");
								return false;
							}
							$els_text[1] = $attr_info[0]["attribute_value"].$issue;
						} else {
							$els_text[1] = $attr_info[0]["attribute_value"];
						}
						break;
						
					case "issue":
						if(strlen($els_text[1]) > 0){
							$issue = $els_text[1];
							$els_text[1] = $issue."(".$attr_info[0]["attribute_value"].")";
						} else {
							$els_text[1] = "(".$attr_info[0]["attribute_value"].")";
						}
						break;
					
					case "dateofissued":// ***Indispensability***
						if(count($attr_info) > 1 || $els_text[2] != ""){
							// Error
							$Ret_Msg = $this->smartyAssign->getLang("repository_els_year_error");
							return false;
						}
						$date = stristr($attr_info[0]["attribute_value"], "-");
						if($date){
							$date = explode("-", $attr_info[0]["attribute_value"]);
							if(strlen($date[0]) <= 0){
								// is not year
								$Ret_Msg = $this->smartyAssign->getLang("repository_els_year_error");
								return false;
							}
							$els_text[2] .= $date[0];
							if(strlen($date[1]) > 0){
								$els_text[2] .= $date[1];
							} else {
								$els_text[2] .= "00";
							}
							if(strlen($date[2]) > 0){
								$els_text[2] .= $date[2];
							} else {
								$els_text[2] .= "00";
							}
						} else {
							if( !(is_numeric($attr_info[0]["attribute_value"])) ){
								// Ng format
								$Ret_Msg = $this->smartyAssign->getLang("repository_els_year_error");
								return false;
							}
							if( strlen($attr_info[0]["attribute_value"]) != 8 ){
								if(strlen($attr_info[0]["attribute_value"]) < 8){
									$els_text[2] .= $attr_info[0]["attribute_value"];
									for($jj=strlen($els_text[2]);$jj<8;$jj++){
										$els_text[2] .= "0";
									}
								} else {
									$Ret_Msg = $this->smartyAssign->getLang("repository_els_year_error");
									return false;
								}
							} else {
								$els_text[2] .= $attr_info[0]["attribute_value"];
							}
						}
						break;
						
					case "alternative": // ***Indispensability***
						if($els_text[5] != ""){
							$Ret_Msg = $this->smartyAssign->getLang("repository_els_tity_error");
							return false;
						}
						$els_text[5] = $attr_info[0]["attribute_value"];
						break;
						
					case "creator":
						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
							if($attr_info[0]["attribute_value"] != ""){
								if($this->lang == "japanese"){
									if($els_text[7] != ""){
										$els_text[7] .= " / ";
									}
									$els_text[7] .= $attr_info[0]["attribute_value"];
								} else {
									if($els_text[9] != ""){
										$els_text[9] .= " / ";
									}
									$els_text[9] .= $attr_info[0]["attribute_value"];
								}
							}
						}
						break;
					// 著者名よみ
//					case "":
//						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
//							if($els_text[8] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
//								$els_text[8] .= " / ";
//							}
//							$els_text[8] .= $attr_info[$nCnt]["attribute_value"];
//						}
//						break;
					// 著者所属(日)(英)
//					case "":
//						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
//							if($this->lang == "japanese"){
//								if($els_text[10] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
//									$els_text[10] .= " / ";
//								}
//								$els_text[10] .= $attr_info[$nCnt]["attribute_value"];
//							} else {
//								if($els_text[11] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
//									$els_text[11] .= " / ";
//								}
//								$els_text[11] .= $attr_info[$nCnt]["attribute_value"];
//							}
//						}
//						break;

					case "spage":
						if(count($attr_info) > 1){
							$Ret_Msg = $this->smartyAssign->getLang("repository_els_page_error");
							return false;
						}
						if(strlen($els_text[12]) > 0){
							$epage = $els_text[12];
							if($epage != $attr_info[0]["attribute_value"]){
								$els_text[12] = $attr_info[0]["attribute_value"] ."-". $epage;
							}
						} else {
							$els_text[12] = $attr_info[0]["attribute_value"];
						}
						break;
						
					case "epage":
						if(count($attr_info) > 1){
							$Ret_Msg = $this->smartyAssign->getLang("repository_els_page_error");
							return false;
						}
						if(strlen($els_text[12]) > 0){
							$spage = $els_text[12];
							if($spage != $attr_info[0]["attribute_value"]){
								$els_text[12] = $spage . "-" . $attr_info[0]["attribute_value"];
							}
						} else {
							$els_text[12] = $attr_info[0]["attribute_value"];
						}
						break;
					// 記事種別(日)(英)
//					case "":
//						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
//							if($this->lang == "japanese"){
//								if($els_text[13] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
//									$els_text[13] .= " ";
//								}
//								$els_text[13] .= $attr_info[$nCnt]["attribute_value"];
//							} else {
//								if($els_text[14] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
//									$els_text[14] .= " ";
//								}
//								$els_text[14] .= $attr_info[$nCnt]["attribute_value"];
//							}
//						}
//						break;

					case "description":
						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
							// delete "\r\n" and "\n" 
							$attr_info[$nCnt]["attribute_value"] = ereg_replace("\r\n", "", $attr_info[$nCnt]["attribute_value"]);
							$attr_info[$nCnt]["attribute_value"] = ereg_replace("\n", "", $attr_info[$nCnt]["attribute_value"]);
							if($this->lang == "japanese"){
								$els_text[16] .= $attr_info[$nCnt]["attribute_value"];
							} else {
								$els_text[17] .= $attr_info[$nCnt]["attribute_value"];
							}
						}
						break;

					case "subject":
						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
							if($this->lang == "japanese"){
								$els_text[18] .= $attr_info[$nCnt]["attribute_value"];
							}
						}
						break;
					// レポート・講演番号
//					case "":
//						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
//							if($els_text[20] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
//								$els_text[20] .= " / ";
//							}
//							$els_text[20] .= $attr_info[$nCnt]["attribute_value"];
//						}
//						break;
                    // Modified to output the URL without file metadata. 2012/11/13 A.Suzuki --start--
//					case "URI":
//						if($els_text[22] != ""){
//							// PDF file is only
//							$Ret_Msg = $this->smartyAssign->getLang("repository_els_flnm_error");
//							return false;
//						}
//						$els_text[22] = $attr_info[$nCnt]["attribute_value"];
//						break;
                    // Modified to output the URL without file metadata. 2012/11/13 A.Suzuki --end--
					default:
						break;
				}
			}
		}
	}
	
    /**
     * check Els format
     *  show : http://www.nii.ac.jp/nels/man/man12.html#12.0
     * ELS形式をチェックする
     * 
     * @param string $els_text Text format Els ELS形式データ
     * @param string $Ret_Msg Error string エラーメッセージ
     * @return boolean Check result チェック結果
     */
	function checkElsText(&$els_text, &$Ret_Msg){
		////////// NCID **Indispensability** //////////
		if($els_text[0] == ""){
			$Ret_Msg = $this->smartyAssign->getLang("repository_els_ncid_error");
			return false;
		}
		////////// volume and issu **Indispensability** //////////
		if($els_text[1] == ""){
			$Ret_Msg = $this->smartyAssign->getLang("repository_els_voln_error");
			return false;
		}
		////////// dateofissued **Indispensability** //////////
		if($els_text[2] == "" || strlen($els_text[2]) != 8 || !(is_numeric($els_text[2])) ){
			// not number or not count 8 
			$Ret_Msg = $this->smartyAssign->getLang("repository_els_year_error");
			return false;
		}		
		////////// pagea attribute **Indispensability** //////////
		if($els_text[3] != "P"){
			$Ret_Msg = $this->smartyAssign->getLang("repository_els_attr_error");
			return false;
		}
		////////// title **Indispensability** //////////
		if($els_text[4] != "" && $els_text[6] != ""){
			$Ret_Msg = $this->smartyAssign->getLang("repository_els_titl_error");
			return false;
		}
		////////// alternative **Indispensability** //////////
		// 論文名読みは必須ではなくなりました 2009/01/08 Y.Nakao
		//if($els_text[5] == ""){
		//	$els_text[5] = $this->smartyAssign->getLang("repository_els_dummy");
		//}
		////////// creater //////////
		if($els_text[7] != ""){
			// for check member num
			$cnt_auth = count(split(" / ", $els_text[7]));
			// check format but CiNii not check
//			$anyone = explode(" / ", $els_text[7] );
//			for($ii=0;$ii<count($anyone);$ii++){
//				$name = explode(",", $anyone[$ii]);
//				if(count($name) != 2){
//					// いずれかがIndispensability
//					$Ret_Msg = $this->smartyAssign->getLang("repository_els_auth_error");
//					return false;
//				}
//			}
		}
		if($els_text[9] != ""){
			// for check member num
			$cnt_auth = count(split(" / ", $els_text[9]));
			// check format but CiNii not check
//			$anyone = explode(" / ", $els_text[9] );
//			for($ii=0;$ii<count($anyone);$ii++){
//				$name = explode(",", $anyone[$ii]);
//				if(count($name) != 2){
//					// いずれかがIndispensability
//					$Ret_Msg = $this->smartyAssign->getLang("repository_els_auth_error");
//					return false;
//				}
//			}
		}
		////////// creater alternative //////////
//		if( ($els_text[7]!="" || $els_text[9] != "")&&$els_text[6] == "") {
//			// 著者名があって読みがないのはエラーにはならないのでコメントアウト
//			return false;
//		}
		if($els_text[6] != ""){
			if($cnt_auth == 0 || $cnt_auth != count(split(" / ", $els_text[6]))){
				// The author name and the number of people are different. 
				//$Ret_Msg = sprintf("Error autY");
				return false;
			}
		}
		////////// Author belonging //////////
		if($els_text[10] != ""){
			if($cnt_auth == 0 || $cnt_auth != count(split(" / ", $els_text[10]))){
				// The author name and the number of people are different. 
				//$Ret_Msg = sprintf("Error affN");
				return false;
			}
		}
		if($els_text[11] != ""){
			if($cnt_auth == 0 || $cnt_auth != count(split(" / ", $els_text[11]))){
				// The author name and the number of people are different. 
				//$Ret_Msg = sprintf("Error affE");
				return false;
			}
		}
		////////// lang **Indispensability** //////////
		if($els_text[15] == ""){
			//$Ret_Msg = sprintf("Error affE");
			return false;
		}
	}
	
    /**
     * change lang to ELS format
     * 言語をELS形式に変換する
     *
     * @return string Language after conversion 変換後の言語
     */
	function changeLangFormatToEls(){
		// WEKO's language is repository/lang/***** of *****
		// 2008/09/30 now langage is japanese and english only
		$els_lang = "";
		switch ($this->lang) {
			case "japanese":
				$els_lang = "JPN";
				break;
			case "english":
				$els_lang = "ENG";
				break;
			case "french":
				$els_lang = "FRE";
				break;
			case "italian":
				$els_lang = "ITA";
				break;
			case "german":
				$els_lang = "GER";
				break;
			case "spanish":
				$els_lang = "SPA";
				break;
			case "chinese":
				$els_lang = "CHI";
				break;
			case "russian":
				$els_lang = "RUS";
				break;
			case "la":
				$els_lang = "LAT";
				break;
			case "esperanto":
				$els_lang = "ESP";
				break;
			case "arabia":
				$els_lang = "ARA";
				break;
			case "korean":
				$els_lang = "KOR";
				break;
			case "dutch":
				$els_lang = "DUT";
				break;
			case "portuguese":
				$els_lang = "POR";
				break;
			case "sanskrit":
				$els_lang = "SAN";
				break;
			default:
				// is not lang
				$els_lang = "";
				break;
		}
		
		return $els_lang;
		
	}
		
}
?>
