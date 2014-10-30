<?php
// --------------------------------------------------------------------
//
// $Id: Detail.class.php 392 2010-02-15 05:33:39Z ivis $
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
 * [[機能説明]]
 *
 * @package     [[package名]]
 * @access      public
 */
class Repository_Action_Edit_Item_Detail
{
	// 使用コンポーネントを受け取るため
	var $session = null;
	var $db = null;
	
	//パラメータを受ける
	var $item_id = null;
	
    /**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute()
    {
    	
    	//-----------------------------------------------------------------------
    	// セッション情報一覧
    	// 1.アイテムタイプ (1レコード) : "item_type", アイテムタイプのレコードをそのまま保存したものである。
    	// 2.アイテム(1レコード) : "item", アイテムのレコードをそのまま保存したものである。
    	// 3.アイテム属性タイプ (Nレコード, Order順) : "item_attr_type"[N][''], アイテム属性タイプの必要部分を連想配列で保持したものである。
    	// 4.アイテム属性 (Nレコード, Order順) : "item_attr"[N][''], アイテム属性の必要部分を連想配列で保持したものである。
    	// ---------------------------------------------------------------------- 
    	
    	// リクエストパラメタにアイテムIDを用いて、アイテムタイプ情報、アイテム情報を取得してセッションに保存
    	if ($this->item_id != null) {
    		
    		//　アイテムをDBから取得
    		//is_delete = 0
    		$params = array(
    				"item_id" => $this->item_id,
    				"is_delete" => 0
    		);
    		$item = $this->db->selectExecute("repository_item",$params);
    		if ($item == false) {
    			return 'error';
    		}
    		//　セッションにアイテムレコードを設定
    		$this->session->setParameter("item",$item[0]);
    		
    		//　アイテム属性をDBから取得
    		$item_attr = $this->db->selectExecute("repository_item_attr",$params);
    		if ($item_attr == false) {
    			return 'error';
    		}
    		
    		// 氏名テーブルをDBから取得
    	    $name = $this->db->selectExecute("repository_name",$params);
    		
    		// ファイルテーブルをDBから取得
    		$file = $this->db->selectExecute("repository_file",$params);

    		//　セッションにアイテム属性レコードを設定
    		$this->session->setParameter("item_attr",$item_attr);
    		
    		//　アイテムタイプをDBから取得
    		$params2 = array("item_type_id" => $item[0]['item_type_id']);
    		$item_type = $this->db->selectExecute("repository_item_type",$params2);
    	    if ($item_attr == false) {
    			return 'error2';
    		}
    		$this->session->setParameter("item_type",$item_type[0]);
    		
    		//　アイテム属性タイプをDBから取得
    		$item_attr_type = $this->db->selectExecute("repository_item_attr_type",$params2);
    		
    		// アイテム属性タイプ情報を表示順に並び換える。
    		$item_element_type = array();	// アイテム属性タイプ
    		$item_element = array();		// アイテム属性
    		$file_element = array();		// ファイル情報
    		$name_element = array();		// 氏名情報
    		$show_no = 1;
    		for ($ii = 0; $ii < count($item_attr_type); $ii++) {
    			for ($ii2 = 0; $ii2 < count($item_attr_type); $ii2++) {
    				if ($item_attr_type[$ii2]['show_order'] == $show_no) {
	    				// アイテム属性タイプの共通項目の設定

	    				array_push( $item_element_type, array(
	    						'attribute_id' => $item_attr_type[$ii2]['attribute_id'],
	    						'attribute_name' => $item_attr_type[$ii2]['attribute_name'],
	    						'plural_enable' => $item_attr_type[$ii2]['plural_enable'],
	    						'line_feed_enable' => $item_attr_type[$ii2]['line_feed_enable']
	    				));
	    				
	    				// アイテム属性の設定
	    				if ($item_attr_type[$ii2]['input_type'] == 'text' || 
	    					$item_attr_type[$ii2]['input_type'] == 'textarea' ||
	    					$item_attr_type[$ii2]['input_type'] == 'select' ||
	    					$item_attr_type[$ii2]['input_type'] == 'checkbox' ||
	    					$item_attr_type[$ii2]['input_type'] == 'radio') {
	    					// アイテム属性タイプの属性IDとアイテム属性の属性IDが同じなら、
	    					// 対象のアイテム属性タイプのアイテム属性となる
	    					for ($ii3 = 0; $ii3 < count($item_attr_type); $ii3++) {
	    						if ($item_attr_type[$ii2]['attribute_id'] == $item_attr[$ii3]['attribute_id']) {
									array_push( $item_element_type, array(
	    								'attribute_id' => $item_attr[$ii3]['attribute_id'],
	    								'attribute_value' => $item_attr[$ii3]['attribute_value']
	    							));
	    						}
	    					}
	    				}
	    				// 氏名情報の設定
	    				elseif ($item_attr_type[$ii2]['input_type'] == 'name') {
	    					// アイテム属性タイプの属性IDと氏名情報の属性IDが同じなら、
	    					// 対象のアイテム属性タイプの氏名情報となる
	    					for ($ii3 = 0; $ii3 < count($name); $ii3++) {
	    						if ($item_attr_type[$ii2]['attribute_id'] == $name[$ii3]['attribute_id']) {
									array_push( $name_element, array(
	    								'attribute_id' => $name[$ii3]['attribute_id'],
	    								'family_en' => $name[$ii3]['family_en'],
										'name_en' => $name[$ii3]['name_en']
	    							));
	    						}
	    					}
	    				}
	    				// ファイル情報の設定
	    				elseif ($item_attr_type[$ii2]['input_type'] == 'file') {
	    					// アイテム属性タイプの属性IDとファイル情報の属性IDが同じなら、
	    					// 対象のアイテム属性タイプの氏名情報となる
	    					for ($ii3 = 0; $ii3 < count($file); $ii3++) {
	    						if ($item_attr_type[$ii2]['attribute_id'] == $file[$ii3]['attribute_id']) {
									array_push( $file_element, array(
	    								'attribute_id' => $file[$ii3]['attribute_id'],
	    								'family_en' => $file[$ii3]['family_en'],
										'file_name' => $file[$ii3]['file_name']
	    							));
	    						}
	    					}
	    				}
	    				
	    				$show_no++;
	    				break;
    				}
    			}
    		}
    		// セッションに表示順に並ひ変えたアイテム属性タイプ情報を設定
    		$this->session->setParameter("item_attr_type",$item_element_type);
    		$this->session->setParameter("item_attr",$item_element);
    		$this->session->setParameter("name",$name_element);
    		$this->session->setParameter("file",$file_element);
    		
        	return 'success';
    	} else {
    		
    	}
    }
}
?>
