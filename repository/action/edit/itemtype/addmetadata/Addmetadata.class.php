<?php

/**
 * Action class for add the item type attribute
 * アイテムタイプ属性追加用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Addmetadata.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Action class for add the item type attribute
 * アイテムタイプ属性追加用アクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Itemtype_Addmetadata extends RepositoryAction
{
	// 使用コンポーネントを受け取るため
	/**
	 * Request components
	 * リクエストコンポーネント
	 *
	 * @var object
	 */
	var $request = null;
	
	// リクエストパラメタ
	/**
	 * Metadata title array
	 * メタデータ項目配列
	 *
	 * @var array
	 */
	var $metadata_title = null;
	/**
	 * Metadata type array
	 * メタデータタイプ配列
	 *
	 * @var array
	 */
	var $metadata_type = null;
	/**
	 * Metadata required flag array
	 * メタデータ必須フラグ配列
	 *
	 * @var array
	 */
	var $metadata_required = null;
	/**
	 * Metadata show list flag array
	 * メタデータ一覧表示フラグ配列
	 *
	 * @var array
	 */
	var $metadata_disp = null;
	/**
	 * Metadata candidate array
	 * メタデータ選択候補配列
	 *
	 * @var array
	 */
	var $metadata_candidate = null;
	/**
	 * Metadata plural enable flag array
	 * メタデータ複数可否フラグ配列
	 *
	 * @var array
	 */
	var $metadata_plural = null;
	/**
	 * Metadata new line flag array
	 * メタデータ改行指定配列
	 *
	 * @var array
	 */
	var $metadata_newline = null;
	/**
	 * Metadata hidden flag array
	 * メタデータ非表示フラグ配列
	 *
	 * @var array
	 */
	var $metadata_hidden = null;	// メタデータ非表示設定配列 2009/01/28
	/**
	 * Item type name
	 * アイテムタイプ名
	 *
	 * @var string
	 */
	var $item_type_name = null;
	
	/**
	 * Execute
	 * 実行
	 *
	 * @return string "success"/"error" success/failed 成功/失敗
	 */
    function executeApp()
    {
	    ////////////////////////// " "をnull文字列に ///////////////////////////
    	$array_title = array();	// 項目名一時保管用 
    	$array_candidate = array();	// 選択肢一時保管用		
	    // 項目名(metadata_title)が空のチェック
    	for($nCnt=0;$nCnt<count($this->metadata_title);$nCnt++){
    		if($this->metadata_title[$nCnt] == " "){
	    		array_push($array_title, "");
    		} else {
	    		array_push($array_title, $this->metadata_title[$nCnt]);
    		}
    	}
    	// 選択肢(metadata_candidate)のチェック
    	for($nCnt=0;$nCnt<count($this->metadata_type);$nCnt++){
    		if( $this->metadata_type[$nCnt] == "checkbox" ||
    			$this->metadata_type[$nCnt] == "radio" ||
    			$this->metadata_type[$nCnt] == "select"){
    			if($this->metadata_candidate[$nCnt] == " "){
	    			array_push($array_candidate, "");
    			} else {
    				array_push($array_candidate, $this->metadata_candidate[$nCnt]);
    			}
    		} else {
    			array_push($array_candidate, "");
    		}
    	}
    	
    	// Save item type name
    	$this->Session->setParameter("item_type_name", $this->item_type_name);
    	
        // metadata_titleをまとめて配列でセッションに保存
        $this->Session->setParameter("metadata_title", $array_title);
	   	// metadata_typeをまとめて配列でセッションに保存
	   	$this->Session->setParameter("metadata_type", $this->metadata_type);

	   	// 2008/02/28 選択肢をまとめて配列でセッションに保存
	   	$this->Session->setParameter("metadata_candidate", $array_candidate);

	   	//チェックボックスはチェックの入ったnameのvalueのみが送信されるため、データを調整
	   	// フラグもまとめてセッションに保存
	   	$array_req = array();
	   	$array_dis = array();
	   	$array_plu = array(); // 2008/03/04 複数可否追加
	   	$array_newline = array();	// 改行指定追加 2008/03/13
	   	$array_hidden = array();	// 非表示設定追加 2009/01/28
	   	for($ii=0; $ii<count($this->metadata_title); $ii++) {
        	array_push($array_req, 0);
        	array_push($array_dis, 0);
        	array_push($array_plu, 0);
        	array_push($array_newline, 0);
        	array_push($array_hidden, 0);
        	$tmp_str = sprintf("%d", $ii);
        	for($jj=0; $jj<count($this->metadata_required); $jj++) {
        		if( strcmp($tmp_str, $this->metadata_required[$jj]) == 0 ){
        			$array_req[$ii] = 1;
        			break;
        		}
        	}
		    for($jj=0; $jj<count($this->metadata_disp); $jj++) {
        		if( strcmp($tmp_str, $this->metadata_disp[$jj]) == 0 ){
        			$array_dis[$ii] = 1;
        			break;
        		}
        	}
        	for($jj=0; $jj<count($this->metadata_plural); $jj++) {
        		if( strcmp($tmp_str, $this->metadata_plural[$jj]) == 0 ){
        			$array_plu[$ii] = 1;
        			break;
        		}
        	}
      		for($jj=0; $jj<count($this->metadata_newline); $jj++) {
        		if( strcmp($tmp_str, $this->metadata_newline[$jj]) == 0 ){
        			$array_newline[$ii] = 1;
        			break;
        		}
        	}
	   		for($jj=0; $jj<count($this->metadata_hidden); $jj++) {
        		if( strcmp($tmp_str, $this->metadata_hidden[$jj]) == 0 ){
        			$array_hidden[$ii] = 1;
        			break;
        		}
        	}
	   	}
	   	$this->Session->setParameter("metadata_required", $array_req);
	   	$this->Session->setParameter("metadata_disp", $array_dis);
	   	$this->Session->setParameter("metadata_plural", $array_plu);
	   	$this->Session->setParameter("metadata_newline", $array_newline);
	   	$this->Session->setParameter("metadata_hidden", $array_hidden);

	   	// 既存編集時 2008/03/03
 		if($this->Session->getParameter("item_type_edit_flag") == 1) {
 			$array_attri_id = $this->Session->getParameter("attribute_id");
            if(!isset($array_attri_id)) {
                $array_attri_id = array();
            }
 			array_push($array_attri_id,-1);
 			// 一行増えた場合、その分sessionにも反映
 			$this->Session->setParameter("attribute_id", $array_attri_id);
 		}
 		//2008/03/03
 		
    	// メタデータ数を増やす
    	$this->Session->setParameter("metadata_num", $this->Session->getParameter("metadata_num") + 1);
    	
    	// エラーなし 2008/02/28
    	$this->Session->setParameter("error_code", 0);
        // Add multi language K.Matsuo 2013/07/24 --start--
        $lang_list = $this->Session->getParameter("lang_list");
        $array_metadata_multi_title = $this->Session->getParameter("metadata_multi_title");
        $multiLang = array();
        foreach($lang_list as $key => $lang){
            $multiLang[$key] = "";
        }
        array_push($array_metadata_multi_title,$multiLang);
        $this->Session->setParameter("metadata_multi_title", $array_metadata_multi_title);
        // Add multi language K.Matsuo 2013/07/24 --end--
        return 'success';

    }
}
?>
