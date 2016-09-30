<?php

/**
 * ELS registration common classes
 * ELS登録共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: ElsCommon.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * ELS registration common classes
 * ELS登録共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class ElsCommon extends RepositoryAction
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
     * Language Resource Management object
     * 言語リソース管理オブジェクト
     *
     * @var Smarty
     */
	var $smartyAssign = null;
	
	// member
	/**
	 * Display language
	 * 表示言語
	 *
	 * @var string
	 */
	var $lang = null;
	
	/**
	 * Constructor
	 * コンストラクタ
	 *
     * @param Session $Session Session management objects Session管理オブジェクト
     * @param Dbobject $Db Database management objects データベース管理オブジェクト
	 * @param 言語リソース管理オブジェクト $smartyAssign Language Resource Management object 言語リソース管理オブジェクト
	 */
	function ElsCommon($session, $db, $smartyAssign){
		if($session!=null){
			$this->Session = $session;
		}
		if($db!=null){
			$this->Db = $db;
		}
		if($smartyAssign!=null){
			$this->smartyAssign = $smartyAssign;
		} else {
			$this->smartyAssign = $this->Session->getParameter("smartyAssign");
			if($this->smartyAssign == null){
				// A resource tidy because it is not a call from view action is not obtained. 
				// However, it doesn't shutdown. 
				$this->setLangResource();
				$this->smartyAssign = $this->Session->getParameter("smartyAssign");
			}
		}
		// set lang
		$this->lang = $this->Session->getParameter("_lang");
	}
	
	/**
	 * To create a TSV data
	 * TSVデータを作成する
	 *
	 * @param array $els_item Registration item list 登録アイテム一覧
	 *                        array[$ii]["item_id"|"item_no"]
	 * @param string $buf Buffer バッファ
	 * @param string $result_message Result message 結果メッセージ
	 * @param array $els_file_data File list ファイル一覧
	 *                             array[$ii]
	 * @return boolean Execution result 実行結果
	 */
	function createElsData($els_item, &$buf, &$result_message, &$els_file_data){
		/////////////// Change ELS Mapping to ELS Format ///////////////
		$els_text_array = array();	// ELStext
		$result_message = array();	// Result message
		$els_file_data = array();	// ELS file data
		for($ii=0;$ii<count($els_item);$ii++){ // Loop for item num
			///// init /////
			$els_text = array();	// ELS for an item
			$Result_List = array();	// infomation for an item
			$result = $this->getItemData($els_item[$ii]['item_id'], $els_item[$ii]['item_no'], $Result_List, $Error_Msg, false, true);
			if($result === false){
				print($Error_Msg);
				//ROLLBACK
	  			$this->exitAction();
				return false;
			}
			//$result_message .= $this->smartyAssign->getLang("repository_els_item").$Result_List["item"][0]["title"]."<br/>";
			//print($result_message);
			$result = $this->getElsText($Result_List, $els_text, $Ret_Msg);
			if($result === false){
				// It is continued not to end
				//Add check language for title 2009/08/25 K.Ito --start--
				if($this->Session->getParameter("_lang") == "japanese"){
					if($Result_List["item"][0]["title"] != ""){
						array_push($result_message, array("0", $Result_List["item"][0]["title"], $Ret_Msg, $Result_List["item"][0]["uri"]));
					}else{
						array_push($result_message, array("0", $Result_List["item"][0]["title_english"], $Ret_Msg, $Result_List["item"][0]["uri"]));
					}
				}else{
					if($Result_List["item"][0]["title_english"] != ""){
						array_push($result_message, array("0", $Result_List["item"][0]["title_english"], $Ret_Msg, $Result_List["item"][0]["uri"]));
					}else{
						array_push($result_message, array("0", $Result_List["item"][0]["title"], $Ret_Msg, $Result_List["item"][0]["uri"]));
					}
				}
				//Add check language for title 2009/08/25 K.Ito --end--
				//$result_message .= $this->smartyAssign->getLang("repository_els_continue")."<br/>";
				continue;
			}
			$result = $this->checkElsText($els_text, $Ret_Msg);
			if($result === false){	
				//Add check language for title 2009/08/25 K.Ito --start--
				if($this->Session->getParameter("_lang") == "japanese"){
					if($Result_List["item"][0]["title"] != ""){
						array_push($result_message, array("0", $Result_List["item"][0]["title"], $Ret_Msg, $Result_List["item"][0]["uri"]));
					}else{
						array_push($result_message, array("0", $Result_List["item"][0]["title_english"], $Ret_Msg, $Result_List["item"][0]["uri"]));
					}
				}else{
					if($Result_List["item"][0]["title_english"] != ""){
						array_push($result_message, array("0", $Result_List["item"][0]["title_english"], $Ret_Msg, $Result_List["item"][0]["uri"]));
					}else{
						array_push($result_message, array("0", $Result_List["item"][0]["title"], $Ret_Msg, $Result_List["item"][0]["uri"]));
					}
				}
				//Add check language for title 2009/08/25 K.Ito --end--
				/*
				// The item that did not pass the check is excluded
				array_push($result_message, array("0",$Result_List["item"][0]["title"], $Ret_Msg));
				//$result_message .= $this->smartyAssign->getLang("repository_els_continue")."<br/>";
				*/
				continue;
			}
			// Use check creare item els data
			array_push($els_text_array, $els_text);
			//Add check language for title 2009/08/25 K.Ito --start--
			if($this->Session->getParameter("_lang") == "japanese"){
				if($Result_List["item"][0]["title"] != ""){
					array_push($result_message, array("1", $Result_List["item"][0]["title"], $this->smartyAssign->getLang("repository_els_success"), $Result_List["item"][0]["uri"]));
				}else{
					array_push($result_message, array("1", $Result_List["item"][0]["title_english"], $this->smartyAssign->getLang("repository_els_success"), $Result_List["item"][0]["uri"]));
				}
			}else{
				if($Result_List["item"][0]["title_english"] != ""){
					array_push($result_message, array("1", $Result_List["item"][0]["title_english"], $this->smartyAssign->getLang("repository_els_success"), $Result_List["item"][0]["uri"]));
				}else{
					array_push($result_message, array("1", $Result_List["item"][0]["title"], $this->smartyAssign->getLang("repository_els_success"), $Result_List["item"][0]["uri"]));
				}
			}
			//Add check language for title 2009/08/25 K.Ito --end--
			
			// Add file copy to contents lab 2010/06/28 A.Suzuki --start--
			for($jj=0;$jj<count($Result_List["item_attr_type"]);$jj++){
				if($Result_List["item_attr_type"][$jj]["input_type"] == "file" || $Result_List["item_attr_type"][$jj]["input_type"] == "file_price"){
					for($kk=0;$kk<count($Result_List["item_attr"][$jj]);$kk++){
						$file_path = $Result_List["item_attr"][$jj][$kk]["item_id"]."_".
									 $Result_List["item_attr"][$jj][$kk]["attribute_id"]."_".
									 $Result_List["item_attr"][$jj][$kk]["file_no"].".".
									 $Result_List["item_attr"][$jj][$kk]["extension"];
						array_push($els_file_data, array($file_path, $Result_List["item_attr"][$jj][$kk]["file_name"]));
					}
				}
			}
			// Add file copy to contents lab 2010/06/28 A.Suzuki --end--
			
			//array_push($result_message, array("1", $Result_List["item"][0]["title"], $this->smartyAssign->getLang("repository_els_success")));
		} // Loop for item num
		
		/////////////// Make ELS file and download this file ///////////////
		$buf = "";
		for($ii=0;$ii<count($els_text_array);$ii++){
			if($ii > 0){
				$buf .= "\r\n";
			}
			// output
			for($jj=0;$jj<count($els_text_array[$ii]);$jj++){
				// tab delimita
				if($jj > 0){
					$buf .= "\t";
				}
				$buf .= $els_text_array[$ii][$jj];
			}
		}
		return true;
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
		// Add multiple languages title 2009/08/20 K.Ito --start--
		//ja
		$els_text[4] = $Result_List["item"][0]["title"];
		//en
		$els_text[6] = $Result_List["item"][0]["title_english"];
		
		/*
		echo($Result_List["item"][0]["title"]);
		echo("<br>");
		echo($Result_List["item"][0]["title_english"]);
		exit();
		*/
		
		// Add multiple languages title 2009/08/20 K.Ito --end--
		/*
		if($this->lang == "japanese"){
			// title(ja)
			$els_text[4] = $Result_List["item"][0]["title"];
		} else {
			// title(en)
			$els_text[6] = $Result_List["item"][0]["title"];
		}
		*/
		
		///// lang /////
		// Add language remediation 2009/08/20 K.Ito --start--
		//$els_text[15] = $Result_List["item"][0]["language"];
		//$els_lang = $this->changeLangFormatToEls();
		$els_lang = $this->changeLangFormatToEls($Result_List["item"][0]["language"]);
		if($els_lang == ""){
			// error
			$msg = $this->smartyAssign->getLang("repository_els_lang_error");
			$Ret_Msg = sprintf("%s : %s", $msg, $this->lang)."<br/>";
			return false;
		}
		$els_text[15] = $els_lang;
		
		// Add language remediation 2009/08/20 K.Ito --end--
		
		///// keyword /////
		// Add multiple languages keyword 2009/08/20 K.Ito --start--
		$keyword = $Result_List["item"][0]["serch_key"];
		$keyword = explode("|", $keyword);
		for($nCnt=0;$nCnt<count($keyword);$nCnt++){
			if($els_text[18] != "" && $keyword[$nCnt] != ""){
				$els_text[18] .= " / ";
			}
			$els_text[18] .= $keyword[$nCnt];
		}
		$keyword_en = $Result_List["item"][0]["serch_key_english"];
		$keyword_en = explode("|", $keyword_en);
		for($nCnt=0;$nCnt<count($keyword_en);$nCnt++){
			if($els_text[19] != "" && $keyword_en[$nCnt] != ""){
				$els_text[19] .= " / ";
			}
			$els_text[19] .= $keyword_en[$nCnt];
		}
        // Modified to output the URL without file metadata. 2012/11/13 A.Suzuki --start--
        $els_text[22] = $Result_List["item"][0]["uri"];
        // Modified to output the URL without file metadata. 2012/11/13 A.Suzuki --end--
		
		/*
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
		*/
		// Add multiple languages keyword 2009/08/20 K.Ito --end--
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
					$date = explode(" ", $attr_info[0]["date_of_issued"]);
					$date = explode("-", $date[0]);
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
			//} else if($input_type == "file" || $input_type == "file_price"){
			//	/////////////// URL ///////////////
			//	// URL for PDF file 
			//	/*
			//	if(($els_text[22] != "") || ($els_text[21] != "")){
			//		// PDF file is only
			//		$Ret_Msg = $this->smartyAssign->getLang("repository_els_flnm_error");
			//		return false;
			//	}
			//	*/
			//	for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
			//		if($attr_info[$nCnt]["extension"] == "pdf"){
			//			if(($els_text[22] != "") || ($els_text[21] != "")){
			//				// PDF file is only
			//				$Ret_Msg = $this->smartyAssign->getLang("repository_els_flnm_error");
			//				return false;
			//			}
			//			// Chanege to detail page URL from download URL 2010/10/28 A.Suzuki --start--
			//			//$els_text[22] = BASE_URL."/?action=repository_uri".
			//			//				"&item_id=".$Result_List["item"][0]["item_id"].
			//			//				"&file_id=".$attr_info[$nCnt]["attribute_id"].
			//			//				"&file_no=".$attr_info[$nCnt]["file_no"];
			//			$els_text[22] = $Result_List["item"][0]["uri"];
			//			// Chanege to detail page URL from download URL 2010/10/28 A.Suzuki --end--
			//			
			//			// Add pdf file name 2009/08/21 K.Ito --start--
			//			/////file name/////
			//			//$els_text[21] = $attr_info[$nCnt]["file_name"];
			//			// Add pdf file name 2009/08/21 K.Ito --end--
			//		}
			//	}
            // Modified to output the URL without file metadata. 2012/11/13 A.Suzuki --end--
			///// name /////
			}else if($input_type == "name"){
				// Add multiple languages name 2009/08/20 K.Ito --start--
				if($Result_List["item_attr_type"][$ii]["junii2_mapping"] =="creator"){
					if($Result_List["item_attr_type"][$ii]["display_lang_type"] =="japanese"){
						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
							if($els_text[7] != ""){
								$els_text[7] .= " / ";
							}
//							// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
//							// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
//							$attr_info[$nCnt]["family"] = str_replace('/', '|', $attr_info[$nCnt]["family"]);
//							$attr_info[$nCnt]["name"] = str_replace('/', '|', $attr_info[$nCnt]["name"]);
//							$attr_info[$nCnt]["family"] = str_replace('／', '|', $attr_info[$nCnt]["family"]);
//							$attr_info[$nCnt]["name"] = str_replace('／', '|', $attr_info[$nCnt]["name"]);
//							// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
							$els_text[7] .= $attr_info[$nCnt]["family"]."," .$attr_info[$nCnt]["name"];
						}
						//echo($els_text[7]."<br>");
						
					}else if ($Result_List["item_attr_type"][$ii]["display_lang_type"] =="english"){
						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
							if($els_text[9] != ""){
								$els_text[9] .= " / ";
							}
//							// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
//							// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
//							$attr_info[$nCnt]["family"] = str_replace('/', '|', $attr_info[$nCnt]["family"]);
//							$attr_info[$nCnt]["name"] = str_replace('/', '|', $attr_info[$nCnt]["name"]);
//							$attr_info[$nCnt]["family"] = str_replace('／', '|', $attr_info[$nCnt]["family"]);
//							$attr_info[$nCnt]["name"] = str_replace('／', '|', $attr_info[$nCnt]["name"]);
//							// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
							$els_text[9] .= $attr_info[$nCnt]["family"]."," .$attr_info[$nCnt]["name"];
							
						}
						//echo($els_text[9]."<br>");
						//exit();
					}else if ($Result_List["item_attr_type"][$ii]["display_lang_type"] == ""){
						if($Result_List["item"][0]["language"] == "ja"){
							for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
								if($els_text[7] != ""){
									$els_text[7] .= " / ";
								}
//								// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
//								// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
//								$attr_info[$nCnt]["family"] = str_replace('/', '|', $attr_info[$nCnt]["family"]);
//								$attr_info[$nCnt]["name"] = str_replace('/', '|', $attr_info[$nCnt]["name"]);
//								$attr_info[$nCnt]["family"] = str_replace('／', '|', $attr_info[$nCnt]["family"]);
//								$attr_info[$nCnt]["name"] = str_replace('／', '|', $attr_info[$nCnt]["name"]);
//								// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
								$els_text[7] .= $attr_info[$nCnt]["family"]."," .$attr_info[$nCnt]["name"];
							}
						}else{
							for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
								if($els_text[9] != ""){
									$els_text[9] .= " / ";
								}
//								// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
//								// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
//								$attr_info[$nCnt]["family"] = str_replace('/', '|', $attr_info[$nCnt]["family"]);
//								$attr_info[$nCnt]["name"] = str_replace('/', '|', $attr_info[$nCnt]["name"]);
//								$attr_info[$nCnt]["family"] = str_replace('／', '|', $attr_info[$nCnt]["family"]);
//								$attr_info[$nCnt]["name"] = str_replace('／', '|', $attr_info[$nCnt]["name"]);
//								// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
								$els_text[9] .= $attr_info[$nCnt]["family"]."," .$attr_info[$nCnt]["name"];
							}
						}
					}
					/*
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
					*/
				}
				// Add multiple languages name 2009/08/20 K.Ito --end--
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
						if($els_text[5] == ""){
							$els_text[5] = $attr_info[0]["attribute_value"];
						}
						break;
						
					case "creator":
						// Add multiple languages creator 2009/08/21 K.Ito --start--
						if($Result_List["item_attr_type"][$ii]["display_lang_type"] =="japanese"){
								for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
									if($els_text[7] != ""){
										$els_text[7] .= " / ";
									}
//									// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
//									// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
//									$attr_info[$nCnt]["family"] = str_replace('/', '|', $attr_info[$nCnt]["family"]);
//									$attr_info[$nCnt]["name"] = str_replace('/', '|', $attr_info[$nCnt]["name"]);
//									$attr_info[$nCnt]["family"] = str_replace('／', '|', $attr_info[$nCnt]["family"]);
//									$attr_info[$nCnt]["name"] = str_replace('／', '|', $attr_info[$nCnt]["name"]);
//									// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
									$els_text[7] .= $attr_info[$nCnt]["attribute_value"];
								}
							}else if ($Result_List["item_attr_type"][$ii]["display_lang_type"] =="english"){
								for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
									if($els_text[9] != ""){
										$els_text[9] .= " / ";
									}
//									// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
//									// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
//									$attr_info[$nCnt]["family"] = str_replace('/', '|', $attr_info[$nCnt]["family"]);
//									$attr_info[$nCnt]["name"] = str_replace('/', '|', $attr_info[$nCnt]["name"]);
//									$attr_info[$nCnt]["family"] = str_replace('／', '|', $attr_info[$nCnt]["family"]);
//									$attr_info[$nCnt]["name"] = str_replace('／', '|', $attr_info[$nCnt]["name"]);
//									// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
									$els_text[9] .= $attr_info[$nCnt]["attribute_value"];
								}
							}else if ($Result_List["item_attr_type"][$ii]["display_lang_type"] == ""){
								if($Result_List["item"][0]["language"] == "ja"){
									for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
										if($els_text[7] != ""){
											$els_text[7] .= " / ";
										}
//										// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
//										// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
//										$attr_info[$nCnt]["family"] = str_replace('/', '|', $attr_info[$nCnt]["family"]);
//										$attr_info[$nCnt]["name"] = str_replace('/', '|', $attr_info[$nCnt]["name"]);
//										$attr_info[$nCnt]["family"] = str_replace('／', '|', $attr_info[$nCnt]["family"]);
//										$attr_info[$nCnt]["name"] = str_replace('／', '|', $attr_info[$nCnt]["name"]);
//										// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
										$els_text[7] .= $attr_info[$nCnt]["attribute_value"];
									}
								}else{
									for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
										if($els_text[9] != ""){
											$els_text[9] .= " / ";
										}
//										// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
//										// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
//										$attr_info[$nCnt]["family"] = str_replace('/', '|', $attr_info[$nCnt]["family"]);
//										$attr_info[$nCnt]["name"] = str_replace('/', '|', $attr_info[$nCnt]["name"]);
//										$attr_info[$nCnt]["family"] = str_replace('／', '|', $attr_info[$nCnt]["family"]);
//										$attr_info[$nCnt]["name"] = str_replace('／', '|', $attr_info[$nCnt]["name"]);
//										// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
										$els_text[9] .= $attr_info[$nCnt]["attribute_value"];
									}
								}
							}
						/*
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
						*/
						// Add multiple languages creator 2009/08/21 K.Ito --end--
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
					//著者所属(日)(英)
					case "contributor":
						// Add multiple languages contributor 2009/08/21 K.Ito --start--
						if($Result_List["item_attr_type"][$ii]["display_lang_type"] =="japanese"){
							for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
								if($els_text[10] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
									$els_text[10] .= " / ";
								}
								// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
								// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
								$attr_info[$nCnt]["attribute_value"] = str_replace('/', '|', $attr_info[$nCnt]["attribute_value"]);
								$attr_info[$nCnt]["attribute_value"] = str_replace('／', '|', $attr_info[$nCnt]["attribute_value"]);
								// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
								$els_text[10] .= $attr_info[$nCnt]["attribute_value"];
							}
						}else if ($Result_List["item_attr_type"][$ii]["display_lang_type"] =="english"){
							for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
								if($els_text[11] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
									$els_text[11] .= " / ";
								}
								// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
								// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
								$attr_info[$nCnt]["attribute_value"] = str_replace('/', '|', $attr_info[$nCnt]["attribute_value"]);
								$attr_info[$nCnt]["attribute_value"] = str_replace('／', '|', $attr_info[$nCnt]["attribute_value"]);
								// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
								$els_text[11] .= $attr_info[$nCnt]["attribute_value"];
							}
						}else if ($Result_List["item_attr_type"][$ii]["display_lang_type"] == ""){
							if($Result_List["item"][0]["language"] == "ja"){
								for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
									if($els_text[10] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
										$els_text[10] .= " / ";
									}
									// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
									// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
									$attr_info[$nCnt]["attribute_value"] = str_replace('/', '|', $attr_info[$nCnt]["attribute_value"]);
									$attr_info[$nCnt]["attribute_value"] = str_replace('／', '|', $attr_info[$nCnt]["attribute_value"]);
									// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
									$els_text[10] .= $attr_info[$nCnt]["attribute_value"];
								}
							}else{
								for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
									if($els_text[11] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
										$els_text[11] .= " / ";
									}
									// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --start--
									// ELS形式のデリミタ取り手死闘される/'が含まれる場合は'|'に置換する
									$attr_info[$nCnt]["attribute_value"] = str_replace('/', '|', $attr_info[$nCnt]["attribute_value"]);
									$attr_info[$nCnt]["attribute_value"] = str_replace('／', '|', $attr_info[$nCnt]["attribute_value"]);
									// ELS delimiter '/' is replace '|' 2009/09/16 Y.Nakao --end--
									$els_text[11] .= $attr_info[$nCnt]["attribute_value"];
								}
							}
						}
						/*
						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
							if($this->lang == "japanese"){
								if($els_text[10] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
									$els_text[10] .= " / ";
								}
								$els_text[10] .= $attr_info[$nCnt]["attribute_value"];
							} else {
								if($els_text[11] != "" && $attr_info[$nCnt]["attribute_value"] != ""){
									$els_text[11] .= " / ";
								}
								$els_text[11] .= $attr_info[$nCnt]["attribute_value"];
							}
						}
						*/
						// Add multiple languages contributor 2009/08/21 K.Ito --end--
						break;

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
					//記事種別の多言語化対応は保留 2009/08/20 K.Ito
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
					//抄録
					case "description":
						// Add multiple languages description 2009/08/21 K.Ito --start--
						if($Result_List["item_attr_type"][$ii]["display_lang_type"] == "japanese"){
							for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
								// delete "\r\n" and "\n" 
								$attr_info[$nCnt]["attribute_value"] = ereg_replace("\r\n","", $attr_info[$nCnt]["attribute_value"]);
								$attr_info[$nCnt]["attribute_value"] = ereg_replace("\n", "", $attr_info[$nCnt]["attribute_value"]);
								$els_text[16] .= $attr_info[$nCnt]["attribute_value"];
							}
						}else if($Result_List["item_attr_type"][$ii]["display_lang_type"] == "english"){
							for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
								// delete "\r\n" and "\n" 
								$attr_info[$nCnt]["attribute_value"] = ereg_replace("\r\n","", $attr_info[$nCnt]["attribute_value"]);
								$attr_info[$nCnt]["attribute_value"] = ereg_replace("\n", "", $attr_info[$nCnt]["attribute_value"]);
								$els_text[17] .= $attr_info[$nCnt]["attribute_value"];
							}
						}else if($Result_List["item_attr_type"][$ii]["display_lang_type"] == ""){
							if($Result_List["item"][0]["language"] == "ja"){
								for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
									// delete "\r\n" and "\n" 
									$attr_info[$nCnt]["attribute_value"] = ereg_replace("\r\n","", $attr_info[$nCnt]["attribute_value"]);
									$attr_info[$nCnt]["attribute_value"] = ereg_replace("\n", "", $attr_info[$nCnt]["attribute_value"]);
									$els_text[16] .= $attr_info[$nCnt]["attribute_value"];
								}
							}else{
								for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
									// delete "\r\n" and "\n" 
									$attr_info[$nCnt]["attribute_value"] = ereg_replace("\r\n","", $attr_info[$nCnt]["attribute_value"]);
									$attr_info[$nCnt]["attribute_value"] = ereg_replace("\n", "", $attr_info[$nCnt]["attribute_value"]);
									$els_text[17] .= $attr_info[$nCnt]["attribute_value"];
								}
							}
						}
						
						/*
						for($nCnt=0;$nCnt<count($attr_info);$nCnt++){
							// delete "\r\n" and "\n" 
							$attr_info[$nCnt]["attribute_value"] = ereg_replace("\r\n", $attr_info[$nCnt]["attribute_value"]);
							$attr_info[$nCnt]["attribute_value"] = ereg_replace("\n", "", $attr_info[$nCnt]["attribute_value"]);
							if($this->lang == "japanese"){
								$els_text[16] .= $attr_info[$nCnt]["attribute_value"];
							} else {
								$els_text[17] .= $attr_info[$nCnt]["attribute_value"];
							}
						}
						*/
						
						// Add multiple languages description 2009/08/21 K.Ito --end--
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
		
		////////// escape special characters //////////
		for($ii=0; $ii<count($els_text); $ii++)
		{
			$els_text[$ii] = str_replace("｢", "「", $els_text[$ii]);
			$els_text[$ii] = str_replace("｣", "」", $els_text[$ii]);
		}
		
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
		if($els_text[4] == "" && $els_text[6] == ""){
			$Ret_Msg = $this->smartyAssign->getLang("repository_els_titl_error");
			return false;
		}
		////////// alternative //////////
		// 論文名読みは必須ではなくなりました 2009/01/08 Y.Nakao
		//if($els_text[5] == ""){
		//	$els_text[5] = $this->smartyAssign->getLang("repository_els_dummy");
		//}
		////////// creater //////////
		//著者名日英が両方設定されている場合、数が一致しないとエラー
		if(($els_text[7] != "") && ($els_text[9] != "")){
			if(count(split(" / ", $els_text[7])) != count(split(" / ", $els_text[9]))){
				// The author name and the number of people are different. 
				$Ret_Msg = $this->smartyAssign->getLang("repository_els_auth_error");
				return false;
			}
		}
		//カウントする必要がなくなったのでコメントアウト 2009/08/20 K.Ito
		/*
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
		/*
		////////// creater alternative //////////
//		if( ($els_text[7]!="" || $els_text[9] != "")&&$els_text[6] == "") {
//			// 著者名があって読みがないのはエラーにはならないのでコメントアウト
//			return false;
//		}

		//このエラー処理は何かの間違いなのでコメントアウトしておきます （英タイトル数と著者数は一致する必要なし) K.Ito 2009/08/21
		/*
		if($els_text[6] != ""){
			if($cnt_auth == 0 || $cnt_auth != count(split(" / ", $els_text[6]))){
				// The author name and the number of people are different. 
				//$Ret_Msg = sprintf("Error autY");
				return false;
			}
		}
		*/
		////////// Author belonging //////////
		//著者所属日英が両方設定されている場合、数が一致しないとエラー
		if(($els_text[10] != "") && ($els_text[11] != "")){
			if(count(split(" / ", $els_text[10])) != count(split(" / ", $els_text[11]))){
				// The author name and the number of people are different. 
				$Ret_Msg = $this->smartyAssign->getLang("repository_els_affn_error");
				return false;
			}
		}
		//やっぱり所属数と著者数が一致する必要があったので判定復活	2009/08/24 K.Ito  --start--
		if(($els_text[10] != "") && ($els_text[7] != "")){
			if( count(split(" / ", $els_text[7])) != count(split(" / ", $els_text[10]))){
				// The author name and the number of people are different. 
				$Ret_Msg = $this->smartyAssign->getLang("repository_els_num_error");
				return false;
			}
		}
		if(($els_text[11] != "") && ($els_text[9] != "")){
			if( count(split(" / ", $els_text[9])) != count(split(" / ", $els_text[11]))){
				// The author name and the number of people are different. 
				$Ret_Msg = $this->smartyAssign->getLang("repository_els_num_error");
				return false;
			}
		}
		if(($els_text[11] != "") && ($els_text[7] != "")){
			if( count(split(" / ", $els_text[7])) != count(split(" / ", $els_text[11]))){
				// The author name and the number of people are different. 
				$Ret_Msg = $this->smartyAssign->getLang("repository_els_num_error");
				return false;
			}
		}
		if(($els_text[10] != "") && ($els_text[9] != "")){
			if( count(split(" / ", $els_text[9])) != count(split(" / ", $els_text[10]))){
				// The author name and the number of people are different. 
				$Ret_Msg = $this->smartyAssign->getLang("repository_els_num_error");
				return false;
			}
		}
		
		////////// lang **Indispensability** //////////
		if($els_text[15] == ""){
			$Ret_Msg = $this->smartyAssign->getLang("repository_els_url_error");
			return false;
		}
	}
	
	// Add remediation change Language Format To Els 2009/08/21 K.Ito --start--
	/**
	 * change lang to ELS format
     * 言語をELS形式に変換する
	 *
     * @param string $getlang Language 言語
     * @return string Language after conversion 変換後の言語
	 */
	function changeLangFormatToEls($getlang){
		// WEKO's language is repository/lang/***** of *****
		// 2008/09/30 now langage is japanese and english only
		$els_lang = "";
		switch ($getlang) {
			case "ja":
				$els_lang = "JPN";
				break;
			case "en":
				$els_lang = "ENG";
				break;
			case "fr":
				$els_lang = "FRE";
				break;
			case "it":
				$els_lang = "ITA";
				break;
			case "de":
				$els_lang = "GER";
				break;
			case "es":
				$els_lang = "SPA";
				break;
			case "zh":
				$els_lang = "CHI";
				break;
			case "ru":
				$els_lang = "RUS";
				break;
			case "la":
				$els_lang = "LAT";
				break;
			case "eo":
				$els_lang = "ESP";
				break;
			case "ar":
				$els_lang = "ARA";
				break;
			case "ko":
				$els_lang = "KOR";
				break;
			case "ms":
				$els_lang = "MAY";
				break;
			case "el":
				$els_lang = "GER";
				break;
			default:
				// is not lang
				$els_lang = "";
				break;
		}
		return $els_lang;
	}
	// Add remediation change Langage Format To Els 2009/08/21 K.Ito --end--
}
?>
