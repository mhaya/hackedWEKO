<?php
/**
 * Action class to save the mapping that has been set on the screen
 * 画面上で設定されたマッピングを保存するためのアクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Mappingconfirm.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Action class to save the mapping that has been set on the screen
 * 画面上で設定されたマッピングを保存するためのアクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Itemtype_Mappingconfirm extends RepositoryAction
{
	// リクエストパラメタ
	/**
	 * NII type
	 * NIIタイプ選択値
	 *
	 * @var int
	 */
	var $niitype = null;
	/**
	 * Array of set Dublin Core mapping
	 * 設定されたDublinCoreマッピングの配列
	 *
	 * @var array
	 */
	var $dublin_core = null;
	/**
	 * Array of set JuNii2 mapping
	 * 設定されたJuNii2マッピングの配列
	 *
	 * @var array
	 */
	var $junii2 = null;
	/**
	 * Array of set LoM mapping
	 * 設定されたLoMマッピングの配列
	 *
	 * @var array
	 */
	var $lom = null;
	/**
	 * Array of set LIDO mapping
	 * 設定されたLIDOマッピングの配列
	 *
	 * @var array
	 */
	public $lido = null;
    /**
     * Array of set SPACE mapping
     * 設定されたSPASEマッピングの配列
     *
     * @var array
     */
    public $spase = null;
	/**
	 * Array of set language
	 * 設定された表示言語の配列
	 *
	 * @var array
	 */
	var $disp_lang = null;
	
	/**
	 * Execute
	 * 実行
	 *
	 * @return string "success"/"error" success/failed 成功/失敗
	 */
    function executeApp()
    {
    	// セッションからアイテムタイプ情報を取得
    	$itemtype = $this->Session->getParameter("itemtype");
        // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -start-
        // change condition of 'if' '未設定'->"0"
    	if($this->niitype != "0"){
    		// 02/26 まだマッピング項目がないのでショート名で代用
    		$itemtype['mapping_info'] = $this->niitype;
    	} else {
    		$itemtype['mapping_info'] = ""; 
    	}
        // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -end-
    	$this->Session->setParameter("itemtype", $itemtype);
    	// リクエストパラメタ分を置き換え
    	$arrays = $this->Session->getParameter("metadata_table");
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

			// Add SPASE
	    	if($this->spase[$ii] != "0")
	    	{
	    		$arrays[$ii]['spase_mapping'] = $this->spase[$ii];
	    	}
	    	else
	    	{
	    		$arrays[$ii]['spase_mapping'] = "";
	    	}
	    	// Add SPASE

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
    	$this->Session->setParameter("metadata_table", $arrays);
        return 'success';
    }
}
?>
