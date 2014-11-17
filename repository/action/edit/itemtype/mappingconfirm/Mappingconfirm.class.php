<?php
// --------------------------------------------------------------------
//
// $Id: Mappingconfirm.class.php 599 2014-07-14 09:06:17Z ivis $
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
class Repository_Action_Edit_Itemtype_Mappingconfirm
{
	// 使用コンポーネントを受け取るため
	var $session = null;
	// リクエストパラメタ
	var $niitype = null;			// type選択値
	var $dublin_core = null;		// Dublin Core選択値
	var $junii2 = null;				// JuNii2選択値
	var $lom = null;                // lom選択値
	public $lido = null;
//	var $junii2_child = null;		// JuNii2選択値(子)=>廃止

	var $disp_lang = null;			// 表示言語選択値
	
    /**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute()
    {
    	// セッションからアイテムタイプ情報を取得
    	$itemtype = $this->session->getParameter("itemtype");
        // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -start-
        // change condition of 'if' '未設定'->"0"
    	if($this->niitype != "0"){
    		// 02/26 まだマッピング項目がないのでショート名で代用
    		$itemtype['mapping_info'] = $this->niitype;
    	} else {
    		$itemtype['mapping_info'] = ""; 
    	}
        // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -end-
    	$this->session->setParameter("itemtype", $itemtype);
    	// リクエストパラメタ分を置き換え
    	$arrays = $this->session->getParameter("metadata_table");
    	// 書誌情報追加 2008/08/22 Y.Nakao --start--
    	$cnt_biblio = 0; // 書誌情報の個数を数える
    	for($ii=0; $ii<count($arrays); $ii++ ) {
    		///// dublin core mapping /////
            // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -start-
    		if($this->dublin_core[$ii] != "0"){
	    		$arrays[$ii]['dublin_core_mapping'] = $this->dublin_core[$ii];
	    	} else {
	    		$arrays[$ii]['dublin_core_mapping'] = "";
	    	}
            // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -end-
            
            ///// lom mapping /////
            // Add LOM column insert 2013/01/28 A.Jin --start--
    		if($this->lom[$ii] != "0"){
	    		$arrays[$ii]['lom_mapping'] = $this->lom[$ii];
	    	} else {
	    		$arrays[$ii]['lom_mapping'] = "";
	    	}
	    	// Add LOM column insert 2013/01/28 A.Jin --end--
	    	
	    	// Add LIDO 2014/04/15 R.Matsuura --start--
	    	if($this->lido[$ii] != "0")
	    	{
	    	    $arrays[$ii]['lido_mapping'] = $this->lido[$ii];
	    	}
	    	else
	    	{
	    	    $arrays[$ii]['lido_mapping'] = "";
	    	}
	    	// Add LIDO 2014/04/15 R.Matsuura --end--
	    	
	    	///// junii2 mapping & Language type /////
    		// 書誌情報の場合
    		if($arrays[$ii]['input_type'] == "biblio_info"){
    			for($jj=0;$jj<7;$jj++){
    				// Junii2
                    // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -start-
		    	    if($this->junii2[$ii+$cnt_biblio+$jj] != "0"){
		    			$arrays[$ii]['junii2_mapping'][$jj] = $this->junii2[$ii+$cnt_biblio+$jj];
		    	    } else {
		    	    	$arrays[$ii]['junii2_mapping'][$jj] = "";
		    	    }
                    // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -end-
		    	    
		    	    // Language type
    				if($this->disp_lang[$ii+$cnt_biblio+$jj] != " "){
		    			$arrays[$ii]['display_lang_type'][$jj] = $this->disp_lang[$ii+$cnt_biblio+$jj];
		    	    } else {
		    	    	$arrays[$ii]['display_lang_type'][$jj] = "";
		    	    }
    			}
    			$cnt_biblio += 6;
   			// 書誌情報ではない場合
    		} else {
                // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -start-
	    	    if($this->junii2[$ii+$cnt_biblio] != "0"){
	    			$arrays[$ii]['junii2_mapping'] = $this->junii2[$ii+$cnt_biblio];
	    	    } else {
	    	    	$arrays[$ii]['junii2_mapping'] = "";
	    	    }
                // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -end-
	    	    
    			// Language type
    			if($this->disp_lang[$ii+$cnt_biblio] != " "){
	    			$arrays[$ii]['display_lang_type'] = $this->disp_lang[$ii+$cnt_biblio];
	    	    } else {
	    	    	$arrays[$ii]['display_lang_type'] = "";
	    	    }
    		}
    	}
    	// 書誌情報追加 2008/08/22 Y.Nakao --start--
    	// 再セット
    	$this->session->setParameter("metadata_table", $arrays);
        return 'success';
    }
}
?>