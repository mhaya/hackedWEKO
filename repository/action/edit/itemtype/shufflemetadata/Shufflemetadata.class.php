<?php

/**
 * Action for shuffle item type metadata show order
 * アイテムタイプのメタデータ項目順番入替アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Shufflemetadata.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Action for shuffle item type metadata show order
 * アイテムタイプのメタデータ項目順番入替アクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Itemtype_Shufflemetadata extends RepositoryAction
{
	// 使用コンポーネントを受け取るため
	/**
	 * Request components
	 * リクエストコンポーネント
	 *
	 * @var object
	 */
	var $request = null;

	/**
	 * Item type name
	 * アイテムタイプ名
	 *
	 * @var string
	 */
	var $item_type_name = null;	//前画面で入力したアイテムタイプ名(新規作成時)

	// jsの引数がリクエストとして送信される
	/**
	 * shuffle index ID
	 * 移動されるインデックスID
	 *
	 * @var int
	 */
	var $shuffle_idx = null;
	/**
	 * Movement determination flag (true: move up, false: move down)
	 * 移動判定フラグ(true: 上に移動, false: 下に移動)
	 *
	 * @var bool
	 */
	var $shuffle_flg = null;	// true:上に移動, false:下に移動	
	
	// メタデータ用配列
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
	 * Metadata candidate array
	 * メタデータ選択候補配列
	 *
	 * @var array
	 */
	var $metadata_candidate = null;
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
	var $metadata_hidden = null;
	
	/**
	 * Execute
	 * 実行
	 *
	 * @return string "success"/"error" success/failed 成功/失敗
	 */
    function executeApp()
    {
    	$metadata_title = $this->metadata_title;
    	$metadata_type = $this->metadata_type;
    	$metadata_candidate = $this->metadata_candidate;
    	$metadata_required = $this->metadata_required;
    	$metadata_disp = $this->metadata_disp;
    	$metadata_plural = $this->metadata_plural;
    	$metadata_newline = $this->metadata_newline;
    	$metadata_hidden = $this->metadata_hidden;
        $array_metadata_multi_title = $this->Session->getParameter("metadata_multi_title");
    	
	    // チェックボックスはチェックの入ったnameのvalueのみが送信されるため、データを調整
	   	$array_req = array();
	   	$array_dis = array();
	   	$array_plu = array(); // 2008/03/04 複数可否追加
	   	$array_newline = array();	// 2008/03/20 改行指定追加
	   	$array_hidden = array();	// 2009/02/09 非表示指定追加
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
	   	
    	// $shuffle_idx行目を上に移動
    	if($this->shuffle_flg == "true"){
    		// 入れ替え処理
    		for($nCnt=1;$nCnt<count($this->metadata_title);$nCnt++){
    			if($nCnt == $this->shuffle_idx){
    				// 項目名入れ替え
    				$tmp = $metadata_title[$this->shuffle_idx];
    				$metadata_title[$this->shuffle_idx] = $metadata_title[$this->shuffle_idx-1];
    				$metadata_title[$this->shuffle_idx-1] = $tmp;
    				// 属性入れ替え
    				$tmp = $metadata_type[$this->shuffle_idx];
    				$metadata_type[$this->shuffle_idx] = $metadata_type[$this->shuffle_idx-1];
    				$metadata_type[$this->shuffle_idx-1] = $tmp;
    				// 選択肢配列入れ替え
    				$tmp = $metadata_candidate[$this->shuffle_idx];
    				$metadata_candidate[$this->shuffle_idx] = $metadata_candidate[$this->shuffle_idx-1];
    				$metadata_candidate[$this->shuffle_idx-1] = $tmp;
    				// 必須チェック
    				$tmp = $array_req[$this->shuffle_idx];
    				$array_req[$this->shuffle_idx] = $array_req[$this->shuffle_idx-1];
    				$array_req[$this->shuffle_idx-1] = $tmp;
    				// 一覧表示チェック
    				$tmp = $array_dis[$this->shuffle_idx];
    				$array_dis[$this->shuffle_idx] = $array_dis[$this->shuffle_idx-1];
    				$array_dis[$this->shuffle_idx-1] = $tmp;
	   				// 複数可否チェック
    				$tmp = $array_plu[$this->shuffle_idx];
    				$array_plu[$this->shuffle_idx] = $array_plu[$this->shuffle_idx-1];
    				$array_plu[$this->shuffle_idx-1] = $tmp;
    				// 改行指定チェック
    				$tmp = $array_newline[$this->shuffle_idx];
    				$array_newline[$this->shuffle_idx] = $array_newline[$this->shuffle_idx-1];
    				$array_newline[$this->shuffle_idx-1] = $tmp;
    				// 非表示指定チェック
    				$tmp = $array_hidden[$this->shuffle_idx];
    				$array_hidden[$this->shuffle_idx] = $array_hidden[$this->shuffle_idx-1];
    				$array_hidden[$this->shuffle_idx-1] = $tmp;
                    // アイテムタイプ項目名多言語 2013/7/24 K.Matsuo --start--
    				$tmp = $array_metadata_multi_title[$this->shuffle_idx];
    				$array_metadata_multi_title[$this->shuffle_idx] = $array_metadata_multi_title[$this->shuffle_idx-1];
    				$array_metadata_multi_title[$this->shuffle_idx-1] = $tmp;
                    // アイテムタイプ項目名多言語 2013/7/24 K.Matsuo --end--
    				// 既存編集の場合
    				if($this->Session->getParameter("item_type_edit_flag") == 1){
    					// attribute_id配列入れ替え
    					$array_attri_id = $this->Session->getParameter("attribute_id");
	    				$tmp = $array_attri_id[$this->shuffle_idx];
	    				$array_attri_id[$this->shuffle_idx] = $array_attri_id[$this->shuffle_idx-1];
	    				$array_attri_id[$this->shuffle_idx-1] = $tmp;
	    				$this->Session->setParameter("attribute_id",$array_attri_id);
    				}
    				break;
    			}    			
    		}
    	}
    	// $shuffle_idx行目を上に移動
    	else {
    	// 入れ替え処理
    		for($nCnt=0;$nCnt<count($this->metadata_title)-1;$nCnt++){
    			if($nCnt == $this->shuffle_idx){
    				// 項目名入れ替え
    				$tmp = $metadata_title[$this->shuffle_idx];
    				$metadata_title[$this->shuffle_idx] = $metadata_title[$this->shuffle_idx+1];
    				$metadata_title[$this->shuffle_idx+1] = $tmp;
    				// 属性入れ替え
    				$tmp = $this->metadata_type[$this->shuffle_idx];
    				$metadata_type[$this->shuffle_idx] = $metadata_type[$this->shuffle_idx+1];
    				$metadata_type[$this->shuffle_idx+1] = $tmp;
    				// 選択肢配列入れ替え
    				$tmp = $this->metadata_candidate[$this->shuffle_idx];
    				$metadata_candidate[$this->shuffle_idx] = $metadata_candidate[$this->shuffle_idx+1];
    				$metadata_candidate[$this->shuffle_idx+1] = $tmp;
    				// 必須チェック
    				$tmp = $array_req[$this->shuffle_idx];
    				$array_req[$this->shuffle_idx] = $array_req[$this->shuffle_idx+1];
    				$array_req[$this->shuffle_idx+1] = $tmp;
    				// 一覧表示チェック
    				$tmp = $array_dis[$this->shuffle_idx];
    				$array_dis[$this->shuffle_idx] = $array_dis[$this->shuffle_idx+1];
    				$array_dis[$this->shuffle_idx+1] = $tmp;
	   				// 複数可否チェック
    				$tmp = $array_plu[$this->shuffle_idx];
    				$array_plu[$this->shuffle_idx] = $array_plu[$this->shuffle_idx+1];
    				$array_plu[$this->shuffle_idx+1] = $tmp;
    				// 改行指定チェック
    				$tmp = $array_newline[$this->shuffle_idx];
    				$array_newline[$this->shuffle_idx] = $array_newline[$this->shuffle_idx+1];
    				$array_newline[$this->shuffle_idx+1] = $tmp;
    				// 非表示指定チェック
    				$tmp = $array_hidden[$this->shuffle_idx];
    				$array_hidden[$this->shuffle_idx] = $array_hidden[$this->shuffle_idx+1];
    				$array_hidden[$this->shuffle_idx+1] = $tmp;
                    // アイテムタイプ項目名多言語 2013/7/24 K.Matsuo --start--
    				$tmp = $array_metadata_multi_title[$this->shuffle_idx];
    				$array_metadata_multi_title[$this->shuffle_idx] = $array_metadata_multi_title[$this->shuffle_idx+1];
    				$array_metadata_multi_title[$this->shuffle_idx+1] = $tmp;
                    // アイテムタイプ項目名多言語 2013/7/24 K.Matsuo --end--
    				// 既存編集の場合
    				if($this->Session->getParameter("item_type_edit_flag") == 1){
    					// attribute_id配列入れ替え
    					$array_attri_id = $this->Session->getParameter("attribute_id");
	    				$tmp = $array_attri_id[$this->shuffle_idx];
	    				$array_attri_id[$this->shuffle_idx] = $array_attri_id[$this->shuffle_idx+1];
	    				$array_attri_id[$this->shuffle_idx+1] = $tmp;
	    				$this->Session->setParameter("attribute_id",$array_attri_id);
    				}
    				break;
    			}    			
    		}
    	}

        $array_title = array();	// 項目名一時保管用 
    	$array_candidate = array();	// 選択肢一時保管用		
	    // 項目名(metadata_title)が空のチェック
    	for($nCnt=0;$nCnt<count($metadata_title);$nCnt++){
    		if($metadata_title[$nCnt] == " "){
	    		array_push($array_title, "");
    		} else {
	    		array_push($array_title, $metadata_title[$nCnt]);
    		}
    	}
    	// 選択肢(metadata_candidate)のチェック
    	for($nCnt=0;$nCnt<count($metadata_type);$nCnt++){
    		if( $metadata_type[$nCnt] == "checkbox" ||
    			$metadata_type[$nCnt] == "radio" ||
    			$metadata_type[$nCnt] == "select"){
    			if($metadata_candidate[$nCnt] == " "){
	    			array_push($array_candidate, "");
    			} else {
    				array_push($array_candidate, $metadata_candidate[$nCnt]);
    			}
    		} else {
    			array_push($array_candidate, "");
    		}
    	}
    	
		// Sessionの保存
		// metadata_titleをまとめて配列でセッションに保存
        $this->Session->setParameter("metadata_title", $array_title);
	   	// metadata_typeをまとめて配列でセッションに保存
	   	$this->Session->setParameter("metadata_type", $metadata_type);
	   	// 選択肢をまとめて配列でセッションに保存
	   	$this->Session->setParameter("metadata_candidate",$array_candidate);
    	// 必須チェック
		$this->Session->setParameter("metadata_required", $array_req);
		// 一覧表示チェック
	   	$this->Session->setParameter("metadata_disp", $array_dis);
	   	// 複数可否チェック
	   	$this->Session->setParameter("metadata_plural", $array_plu);
	   	// 改行指定チェック
	   	$this->Session->setParameter("metadata_newline", $array_newline);
	   	// 非表示指定チェック
	   	$this->Session->setParameter("metadata_hidden", $array_hidden);
    	// アイテムタイプ名保存処理　追加 2009/12/10 K.Ando --start--
    	//$this->Session->setParameter("itemtype_name", $this->item_type_name);
    	$this->Session->setParameter("item_type_name", $this->item_type_name);
    	// アイテムタイプ名保存処理　追加 2009/12/10 K.Ando --end--	   	
    	// アイテムタイプ項目名多言語 2013/7/24 K.Matsuo --start--
        $this->Session->setParameter("metadata_multi_title", $array_metadata_multi_title);
    	// アイテムタイプ項目名多言語 2013/7/24 K.Matsuo --end--
	   	return 'success';
		
    }
}
?>
