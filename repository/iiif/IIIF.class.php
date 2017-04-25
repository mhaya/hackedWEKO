<?php
/// --------------------------------------------------------------------
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
// ref. http://iiif.io/api/presentation/2.1/
// --------------------------------------------------------------------

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

error_reporting(0);

include_once MAPLE_DIR.'/includes/pear/File/Archive.php';
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
require_once WEBAPP_DIR. '/modules/repository/action/main/export/ExportCommon.class.php';
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryItemAuthorityManager.class.php';
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryOutputFilter.class.php';
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';

/**
 * [[機能説明]]
 *
 * @package     [[package名]]
 * @access      public
 */
class Repository_Iiif extends RepositoryAction
{
	// リクエストパラメータを受け取るため
	var $verb = null;
	var $itemId = null;
	var $itemNo = null;
	
	// ダウンロード用メンバ
	var $uploadsView = null;
	
	// 改行
	var $LF = "\n";
	// タブシフト
	var $TAB_SHIFT = "\t";

	// 出力文字列
	var $feed = '';
	
	// エラーメッセージ
	var $errorMsg = "";
	
	// グローバル
	var $iiif_fields = array();

	//config
	var $config = array();



     /**
     * for instance of RepositoryHandleManager class
     * 
     * @var object
     */
    private $repositoryHandleManager = null;

    
    /**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute()
    {
    	// 初期処理
    	$this->initAction();
	// 設定読み込み
	$this->config = parse_ini_file("config.ini");

        $this->getRepositoryHandleManager();
        
	// フィード文字列取得
	$feed = $this->outputIIIF();
    	
    	// 取得結果がfalseでなければ
    	if ( $feed != false ) {
    		// ヘッダ出力
    		header("Content-Type: application/json; charset=utf-8");
	    	// フィード出力
			print $feed;
       	}else{
       		// ヘッダ出力
    		header("Content-Type: text/html; charset=utf-8");	// レスポンスのContent-Typeを明示的に指定する("text/html")
       		// エラー出力
       		print $this->errorMsg;
       	}
		
		// テキスト書き出し終了後にexit関数を呼び出す
    	exit();

    }
	
    function outputIIIF()
    {




    	// アイテム情報の取得
    	$query = 'SELECT ITEMTYPE.mapping_info, '.
    			 '		 ITEM.title, '.
    			 '		 ITEM.title_english, '.
    			 '		 ITEM.language, '.
    			 '		 ITEM.item_type_id, '.
    			 '		 ITEM.serch_key, '.
    			 '		 ITEM.shown_status '.
    			 'FROM '.DATABASE_PREFIX.'repository_item ITEM, '.
    			 '     '.DATABASE_PREFIX.'repository_item_type ITEMTYPE '.
				 'WHERE ITEM.item_type_id = ITEMTYPE.item_type_id '.
   				 '  AND ITEM.item_id = ? '.
    			 '  AND ITEM.item_no = ? '.
    			 '  AND ITEM.is_delete = 0;';
    	$params = null;
		$params[] = $this->itemId;
		$params[] = $this->itemNo;
 
    	$retItem = $this->Db->execute($query, $params);
		if ($retItem === false) {
			$this->errorMsg = 'Database access error.';
			return false;
		}
		
		// アイテム情報が無い場合、終了
		if (count($retItem) < 1){
			$this->errorMsg = 'This item data is not found.';
			return false;
    	}
    	// アイテムタイプのマッピング情報がない場合終了
    	if($retItem[0]['mapping_info'] == null){
    		$this->errorMsg = 'This item has no mapping info.';
    		return false;
    	}
    	


        // Add check item public status 2010/01/12 Y.Nakao --start--
        // Add tree access control list 2012/03/07 T.Koyasu -start-
        $role_auth_id = $this->Session->getParameter('_role_auth_id');
        $user_auth_id = $this->Session->getParameter('_user_auth_id');
        $user_id = $this->Session->getParameter('_user_id');
        $this->Session->removeParameter('_role_auth_id');
        $this->Session->removeParameter('_user_auth_id');
        $this->Session->setParameter('_user_id', '0');
        $this->Session->setParameter('_role_auth_id', $role_auth_id);
        $this->Session->setParameter('_user_auth_id', $user_auth_id);
        $this->Session->setParameter('_user_id', $user_id);
        // Add tree access control list 2012/03/07 T.Koyasu -end-
		// アイテム公開チェック
        // Add Advanced Search 2013/11/26 R.Matsuura --start--
        $itemAuthorityManager = new RepositoryItemAuthorityManager($this->Session, $this->dbAccess, $this->TransStartDate);
        // Add Advanced Search 2013/11/26 R.Matsuura --end--
        // Mod OpenDepo 2014/01/31 S.Arata --start--
        if(!$itemAuthorityManager->checkItemPublicFlg($this->itemId, $this->itemNo, $this->repository_admin_base, $this->repository_admin_room)){
			// item close
			$retItem[0]['shown_status'] = "0";
		}
		// Mod OpenDepo 2014/01/31 S.Arata --end--
		// Add check item public status 2010/01/12 Y.Nakao --end--

    	// アイテムが非公開の場合エラー
    	if($retItem[0]['shown_status'] != 1){
    		$this->errorMsg = 'This item is private.';
    		return false;
    	}

        
    	// メタデータ取得
    	$query = 'SELECT attribute_id, '.
    	    	 '		 show_order, '.
    			 '		 attribute_name, '.
    			 '		 input_type, '.
    			 '		 junii2_mapping, '.
    			 // Fix output hidden metadata 2011/11/28 Y.Nakao --start--
    			 '		 hidden, '.
    			 // Fix output hidden metadata 2011/11/28 Y.Nakao --end--
    			//Add display lang type 2009/09/01 K.Ito --start--
    			 '		 display_lang_type '.
    			//Add display lang type 2009/09/01 K.Ito --end--
    			 'FROM '.DATABASE_PREFIX.'repository_item_attr_type '.
				 'WHERE item_type_id = ? '.
    			 '	AND is_delete = 0 '.
    			 'order by show_order;';
	    $params = null;
	    $params[] = $retItem[0]['item_type_id'];
    	$retAttr = $this->Db->execute($query, $params);
		if ($retAttr === false) {
			$this->errorMsg = 'Database access error.';
			return false;
		}
		
		// 入力されているメタデータのvalueを取得
    	$query = 'SELECT attribute_id, '.
    	    	 '		 attribute_no, '.
    			 '		 attribute_value '.
    			 'FROM '.DATABASE_PREFIX.'repository_item_attr '.
				 'WHERE item_id = ? '.
    			 '	AND item_no = ? '.
    			 '	AND item_type_id = ? '.
    			 '	AND is_delete = 0 '.
    			 'order by attribute_id;';
	    $params = null;
	    $params[] = $this->itemId;
	    $params[] = $this->itemNo;
	    $params[] = $retItem[0]['item_type_id'];
    	$retAttrValue = $this->Db->execute($query, $params);
		if ($retAttrValue === false) {
			$this->errorMsg = 'Database access error.';
			return false;
		}
    	
    	// Add LIDO 2014/05/09 S.Suzuki --start--
    	for ($ii = 0; $ii < count($retAttrValue); $ii++) {
    		$retAttrValue[$ii]['attribute_value'] = RepositoryOutputFilter::exclusiveReservedWords($retAttrValue[$ii]['attribute_value']);
    	}
		// Add LIDO 2014/05/09 S.Suzuki --end--
    	
		// 複数入力の紐づけ
    	for($ii=0;$ii<count($retAttr);$ii++){
    		$cntValue = 0;
    		for($jj=0;$jj<count($retAttrValue);$jj++){
	    		if($retAttr[$ii]['attribute_id'] == $retAttrValue[$jj]['attribute_id']){
					$retAttr[$ii]['value'][$cntValue] = $retAttrValue[$jj]['attribute_value'];
					$cntValue++;
				}
    		}
		}
		
    	// 書誌情報を持つ場合、各データを取得
        $query = 'SELECT BIBLIO_INFO.biblio_name, '.
        		 '		 BIBLIO_INFO.volume, '.
        		 '		 BIBLIO_INFO.biblio_name_english, '.
        		 '		 BIBLIO_INFO.issue, '.
        		 '		 BIBLIO_INFO.start_page, '.
        		 '		 BIBLIO_INFO.end_page, '.
	    	     '		 BIBLIO_INFO.date_of_issued '.
        		 'FROM '.DATABASE_PREFIX.'repository_biblio_info BIBLIO_INFO, '.
        		 '	   '.DATABASE_PREFIX.'repository_item_attr_type ATTRTYPE '.
        		 'WHERE ATTRTYPE.input_type = "biblio_info" '.
        		 '	AND ATTRTYPE.item_type_id = BIBLIO_INFO.item_type_id '.
        		 '	AND ATTRTYPE.attribute_id = BIBLIO_INFO.attribute_id '.
        		 '	AND ATTRTYPE.item_type_id = ? '.
        		 '	AND BIBLIO_INFO.item_id = ? '.
        		 '	AND BIBLIO_INFO.item_no = ? '.
        		 '	AND ATTRTYPE.is_delete = 0 '.
        		 // Fix output hidden metadata 2011/11/28 Y.Nakao --start--
        		 '	AND ATTRTYPE.hidden = 0 '.
        		 // Fix output hidden metadata 2011/11/28 Y.Nakao --end--
        		 '	AND BIBLIO_INFO.is_delete = 0;';
        $params = null;
        $params[] = $retItem[0]['item_type_id'];
        $params[] = $this->itemId;
        $params[] = $this->itemNo;
    	$retBiblio_info = $this->Db->execute($query, $params);
		if ($retBiblio_info === false) {
			$this->errorMsg = 'Database access error.';
			return false;
		}
    	
    	// Add LIDO 2014/05/09 S.Suzuki --start--
    	for ($ii = 0; $ii < count($retBiblio_info); $ii++) {
    		$retBiblio_info[$ii]['biblio_name']         = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$ii]['biblio_name']);
    		$retBiblio_info[$ii]['volume']              = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$ii]['volume']);
    		$retBiblio_info[$ii]['biblio_name_english'] = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$ii]['biblio_name_english']);
    		$retBiblio_info[$ii]['issue']               = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$ii]['issue']);
    		$retBiblio_info[$ii]['start_page']          = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$ii]['start_page']);
    		$retBiblio_info[$ii]['end_page']            = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$ii]['end_page']);
    		$retBiblio_info[$ii]['date_of_issued']      = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$ii]['date_of_issued']);
    	}
		// Add LIDO 2014/05/09 S.Suzuki --end--
    	
		// 書誌情報に含まれるデータを加工
		if(count($retBiblio_info) > 0){
	    	// ページ数の連結
	    	$biblio_info_pages = null;
	    	if($retBiblio_info[0]['start_page'] != null && $retBiblio_info[0]['end_page'] != null){
				$biblio_info_pages = $retBiblio_info[0]['start_page'].'--'.$retBiblio_info[0]['end_page'];
	    	}
			
	    	// 発行年月の取得
	    	$biblio_info_year = null;
	    	$biblio_info_month = null;
	    	if($retBiblio_info[0]['date_of_issued'] != null){
	    		$split_date = split("-", $retBiblio_info[0]['date_of_issued']);
	    		$biblio_info_year = $split_date[0];
	    		if($split_date[1] != null){
	    			switch($split_date[1]){
	    				case '01':
	    					$biblio_info_month = 'jan';
	    					break;
	    				case '02':
	    					$biblio_info_month = 'feb';
	    					break;
	    				case '03':
	    					$biblio_info_month = 'mar';
	    					break;
	    				case '04':
	    					$biblio_info_month = 'apr';
	    					break;
	    				case '05':
	    					$biblio_info_month = 'may';
	    					break;
	    				case '06':
	    					$biblio_info_month = 'jun';
	    					break;
	    				case '07':
	    					$biblio_info_month = 'jul';
	    					break;
	    				case '08':
	    					$biblio_info_month = 'aug';
	    					break;
	    				case '09':
	    					$biblio_info_month = 'sep';
	    					break;
	    				case '10':
	    					$biblio_info_month = 'oct';
	    					break;
	    				case '11':
	    					$biblio_info_month = 'nov';
	    					break;
	    				case '12':
	    					$biblio_info_month = 'dec';
	    					break;
	    				default:
	    					$biblio_info_month = $split_date[1];
	    			}
	    		}
	    	}
		}


		// 出力データ作成
		//Add multiple language 2009/09/02 K.Ito --start--
		if($this->Session->getParameter("_lang") == "japanese"){
			if($retItem[0]['title'] != ""){
				$this->iiif_fields['title'] = $retItem[0]['title'];
			}else if($retItem[0]['title_english'] != ""){
				$this->iiif_fields['title'] = $retItem[0]['title_english'];
			}
			if($retBiblio_info[0]['biblio_name'] != ""){
				$this->iiif_fields['booktitle'] = $retBiblio_info[0]['biblio_name'];
				$this->iiif_fields['journal'] = $retBiblio_info[0]['biblio_name'];
			}else if($retBiblio_info[0]['biblio_name_english'] != ""){
				$this->iiif_fields['booktitle'] = $retBiblio_info[0]['biblio_name_english'];
				$this->iiif_fields['journal'] = $retBiblio_info[0]['biblio_name_english'];
			}
		}else{
			if($retItem[0]['title_english'] != ""){
				$this->iiif_fields['title'] = $retItem[0]['title_english'];
			}else if($retItem[0]['title'] != ""){
				$this->iiif_fields['title'] = $retItem[0]['title'];
			}
			if($retBiblio_info[0]['biblio_name_english'] != ""){
				$this->iiif_fields['booktitle'] = $retBiblio_info[0]['biblio_name_english'];
				$this->iiif_fields['journal'] = $retBiblio_info[0]['biblio_name_english'];
			}else if($retBiblio_info[0]['biblio_name'] != ""){
				$this->iiif_fields['booktitle'] = $retBiblio_info[0]['biblio_name'];
				$this->iiif_fields['journal'] = $retBiblio_info[0]['biblio_name'];
			}
		}
		//Add multiple language 2009/09/02 K.Ito --end--
		//$this->iiif_fields['title'] = $retItem[0]['title'];
		//$this->iiif_fields['booktitle'] = $retBiblio_info[0]['biblio_name'];
		//$this->iiif_fields['journal'] = $retBiblio_info[0]['biblio_name'];
		$this->iiif_fields['volume'] = $retBiblio_info[0]['volume'];
		$this->iiif_fields['number'] = $retBiblio_info[0]['issue'];
		$this->iiif_fields['pages'] = $biblio_info_pages;
		$this->iiif_fields['month'] = $biblio_info_month;
		$this->iiif_fields['year'] = $biblio_info_year;
    	//print_r($retAttr);
    	
    	// init
    	$author_sub = null;
    	$jtitle_sub = null;
    	$publisher_sub = null;
    	$contributor_sub = null;
    	
    	
			for($ii=0;$ii<count($retAttr);$ii++){//print_r($this->iiif_fields);
			// Fix output hidden metadata 2011/11/28 Y.Nakao --start--
			if($retAttr[$ii]['hidden'] == '1')
			{
				continue;
			}
			// Fix output hidden metadata 2011/11/28 Y.Nakao --end--
				switch($retAttr[$ii]['junii2_mapping']){
				case 'creator':
					//Add multiple language for creator 2009/09/02 K.Ito --start--
					if(!isset($this->iiif_fields['author'])){
						if($retAttr[$ii]['input_type'] == 'name'){
					    	// Authorを取得
					        $query = 'SELECT family, '.
					    			 '		 name '.
					    			 'FROM '.DATABASE_PREFIX.'repository_personal_name '.
									 'WHERE item_id = ? '.
					    			 '  AND item_no = ? '.
					    			 '  AND attribute_id = ? '.
					    			 '  AND is_delete = 0;';
						    $params = null;
							$params[] = $this->itemId;
							$params[] = $this->itemNo;
							$params[] = $retAttr[$ii]['attribute_id'];
					    	$retName = $this->Db->execute($query, $params);
							if ($retName === false) {
								$this->errorMsg = 'Database access error.';
								return false;
							}
							
							// Add LIDO 2014/05/22 S.Suzuki --start--
					    	for ($jj = 0; $jj < count($retName); $jj++) {
					    		$retName[$jj]['family'] = RepositoryOutputFilter::exclusiveReservedWords($retName[$jj]['family']);
					    		$retName[$jj]['name']   = RepositoryOutputFilter::exclusiveReservedWords($retName[$jj]['name']);
					    	}
							
							//一時保存用Name初期化
							$Name = "";
					
					    	// 氏名を連結
					    	for($jj=0;$jj<count($retName);$jj++){
					    		if($jj != 0){
					    			$Name .= ' and ';
					    		}
					    		if ($retName[$jj]['family'] !== '' && $retName[$jj]['name'] !== '') {
					    			$Name .= $retName[$jj]['family'].','.$retName[$jj]['name'];
					    		}
					    		else if ($retName[$jj]['family'] !== '') {
					    			$Name .= $retName[$jj]['family'];
					    		}
					    		else {
					    			$Name .= $retName[$jj]['name'];
					    		}
					    	}
							// Add LIDO 2014/05/22 S.Suzuki --end--
							
					    	//氏名を格納
							if($this->Session->getParameter("_lang") == "japanese"){
								if($retAttr[$ii]['display_lang_type'] == "japanese" || $retAttr[$ii]['display_lang_type'] == ""){
									if(!isset($this->iiif_fields['author'])){
					    				$this->iiif_fields['author'] = $Name;
									}
									else{
										$this->iiif_fields['author'] .= $Name;
									}
								}else{
									if($author_sub == null){
										$author_sub = $Name;
									}
								}
					    	}else{
					    		if($retAttr[$ii]['display_lang_type'] == "english" || $retAttr[$ii]['display_lang_type'] == ""){
					    			$this->iiif_fields['author'] .= $Name;
					    		}else{
					    			if($author_sub == null){
					    				$author_sub = $Name;
					    			}
					    		}	
					    	}
						}else{
							//$Name初期化
							$Name = "";
							for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
								if($jj != 0){
					    			$Name .= ' and ';
					    		}
					    		$Name .= $retAttr[$ii]['value'][$jj];					
							}
							//氏名を格納
							if($this->Session->getParameter("_lang") == "japanese"){
								if($retAttr[$ii]['display_lang_type'] == "japanese" || $retAttr[$ii]['display_lang_type'] == ""){
					    			$this->iiif_fields['author'] .= $Name;
								}else{
									//上書きはしない。最初のsubを保持する
									if($author_sub == null){
										$author_sub = $Name;
									}
								}
					    	}else{
					    		if($retAttr[$ii]['display_lang_type'] == "english" || $retAttr[$ii]['display_lang_type'] == ""){
					    			$this->iiif_fields['author'] .= $Name;
					    		}else{
					    			if($author_sub == null){
					    				$author_sub = $Name;
					    			}
					    		}	
					    	}
						}
					}
					//Add multiple language for creator 2009/09/02 K.Ito --end--
					break;
					
				case 'jtitle':
					//Add multiple language 2009/09/02 K.Ito --start--
					if($this->iiif_fields['booktitle'] == null && $this->iiif_fields['journal'] == null){
						if($this->Session->getParameter("_lang") == "japanese"){
							if($retAttr[$ii]['display_lang_type'] == "japanese" || $retAttr[$ii]['display_lang_type'] == ""){
								for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
									if($jj != 0){
										$this->iiif_fields['booktitle'] .= ', ';
										$this->iiif_fields['journal'] .= ', ';
									}
									$this->iiif_fields['booktitle'] .= $retAttr[$ii]['value'][$jj];
									$this->iiif_fields['journal'] .= $retAttr[$ii]['value'][$jj];							
								}
							}else{
								if($jtitle_sub == null){
									for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
										if($jj != 0){
											$jtitle_sub .= ', ';
										}
										$jtitle_sub .= $retAttr[$ii]['value'][$jj];						
									}
								}
							}
						}else{
							if($retAttr[$ii]['display_lang_type'] == "english" || $retAttr[$ii]['display_lang_type'] == ""){
								for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
									if($jj != 0){
										$this->iiif_fields['booktitle'] .= ', ';
										$this->iiif_fields['journal'] .= ', ';
									}
									$this->iiif_fields['booktitle'] .= $retAttr[$ii]['value'][$jj];
									$this->iiif_fields['journal'] .= $retAttr[$ii]['value'][$jj];							
								}
							}else{
								if($jtitle_sub == null){
									for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
										if($jj != 0){
											$jtitle_sub .= ', ';
										}
										$jtitle_sub .= $retAttr[$ii]['value'][$jj];	
									}						
								}
							}
						}
					}
					//Add multiple language 2009/09/02 K.Ito --end--
					break;
				
				case 'volume':
					if(!isset($this->iiif_fields['volume']) && isset($retAttr[$ii]['value'])){
						for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
							if($jj != 0){
								$this->iiif_fields['volume'] .= ', ';
							}
							$this->iiif_fields['volume'] .= $retAttr[$ii]['value'][$jj];					
						}
					}
					break;
					
				case 'issue':
					if(!isset($this->iiif_fields['number']) && isset($retAttr[$ii]['value'])){
						for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
							if($jj != 0){
								$this->iiif_fields['number'] .= ', ';
							}
							$this->iiif_fields['number'] .= $retAttr[$ii]['value'][$jj];					
						}
					}
					break;
					
				case 'spage':
					if(!isset($this->iiif_fields['spage']) && isset($retAttr[$ii]['value'])){
						for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
							$this->iiif_fields['spage'][$jj] .= $retAttr[$ii]['value'][$jj];					
						}
					}
					break;
					
				case 'epage':
					if(!isset($this->iiif_fields['epage']) && isset($retAttr[$ii]['value'])){
						for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
							$this->iiif_fields['epage'][$jj] .= $retAttr[$ii]['value'][$jj];					
						}
					}
					break;
					
				case 'publisher':
					if(!isset($this->iiif_fields['publisher'])){
						//Add multiple language 2009/09/03 K.Ito --start--
						//$pub 初期化;
						$pub = null;
							if($retAttr[$ii]['input_type'] == 'name'){
						        $query = 'SELECT family, '.
						    			 '		 name '.
						    			 'FROM '.DATABASE_PREFIX.'repository_personal_name '.
										 'WHERE item_id = ? '.
						    			 '  AND item_no = ? '.
						    			 '  AND attribute_id = ? '.
						    			 '  AND is_delete = 0;';
							    $params = null;
								$params[] = $this->itemId;
								$params[] = $this->itemNo;
								$params[] = $retAttr[$ii]['attribute_id'];
						    	$retName = $this->Db->execute($query, $params);
								if ($retName === false) {
									$this->errorMsg = 'Database access error.';
									return false;
								}
								
								// Add LIDO 2014/05/22 S.Suzuki --start--
						    	$tmp_name = array();
								
								for ($jj = 0; $jj < count($retName); $jj++) {
						    		$retName[$jj]['family'] = RepositoryOutputFilter::exclusiveReservedWords($retName[$jj]['family']);
						    		$retName[$jj]['name']   = RepositoryOutputFilter::exclusiveReservedWords($retName[$jj]['name']);
						    		if ($retName[$jj]['family'] !== '' || $retName[$jj]['name'] !== '') {
						    			array_push($tmp_name, $retName[$jj]);
						    		}
						    	}
								
						    	for($jj=0;$jj<count($tmp_name);$jj++){
						    		if($jj != 0){
						    			$pub .= ' and ';
						    		}
						    		if ($tmp_name[$jj]['family'] !== '' && $tmp_name[$jj]['name'] !== '') {
						    			$pub .= $tmp_name[$jj]['family'].','.$tmp_name[$jj]['name'];
						    		}
						    		else if ($tmp_name[$jj]['family'] !== '') {
					    				$pub .= $tmp_name[$jj]['family'];
						    		}
						    		else {
						    			$pub .= $tmp_name[$jj]['name'];
						    		}
						    	}
								// Add LIDO 2014/05/22 S.Suzuki --end--
							}else{
								if(isset($retAttr[$ii]['value'])){
									for ($jj = 0; $jj < count($retAttr[$ii]['value']); $jj++) {
										if($jj != 0){
											$pub .= ', ';
										}
										$pub .= $retAttr[$ii]['value'][$jj];
									}
								}
							}
						//最後に$pub格納
						if($this->Session->getParameter("_lang") == "japanese"){
							if($retAttr[$ii]['display_lang_type'] == "japanese" || $retAttr[$ii]['display_lang_type'] == ""){
								$this->iiif_fields['publisher'] = $pub;		
							}else{
								if($publisher_sub == null){
									$publisher_sub = $pub;
								}
							}
						}else{
							if($retAttr[$ii]['display_lang_type'] == "english" || $retAttr[$ii]['display_lang_type'] == ""){
								$this->iiif_fields['publisher'] = $pub;
							}else{
								if($publisher_sub == null){
									$publisher_sub = $pub;
								}
							}
						}
						//Add multiple language 2009/09/03 K.Ito --end--
					}
					break;
					
				case 'format':
					if(!isset($this->iiif_fields['howpublished']) && isset($retAttr[$ii]['value'])){
						for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
							if($jj != 0){
								$this->iiif_fields['howpublished'] .= ', ';
							}
							$this->iiif_fields['howpublished'] .= $retAttr[$ii]['value'][$jj];					
						}
					}
					break;
					
				case 'contributor':
					if(!isset($this->iiif_fields['institution'])){
						//Add multiple language 2009/09/03 K.Ito --start--
						//con初期化
						$con = null;
							if($retAttr[$ii]['input_type'] == 'name'){
						        $query = 'SELECT family, '.
						    			 '		 name '.
						    			 'FROM '.DATABASE_PREFIX.'repository_personal_name '.
										 'WHERE item_id = ? '.
						    			 '  AND item_no = ? '.
						    			 '  AND attribute_id = ? '.
						    			 '  AND is_delete = 0;';
							    $params = null;
								$params[] = $this->itemId;
								$params[] = $this->itemNo;
								$params[] = $retAttr[$ii]['attribute_id'];
						    	$retName = $this->Db->execute($query, $params);
								if ($retName === false) {
									$this->errorMsg = 'Database access error.';
									return false;
								}
								
								// Add LIDO 2014/05/22 S.Suzuki --start--
								for ($jj = 0; $jj < count($retName); $jj++) {
						    		$retName[$jj]['family'] = RepositoryOutputFilter::exclusiveReservedWords($retName[$jj]['family']);
						    		$retName[$jj]['name']   = RepositoryOutputFilter::exclusiveReservedWords($retName[$jj]['name']);
						    	}
								
						    	for($jj=0;$jj<count($retName);$jj++){
						    		if($jj != 0){
						    			$con .= ' and ';
						    		}
						    		if ($retName[$jj]['family'] !== '' && $retName[$jj]['name'] !== '') {
						    			$con .= $retName[$jj]['family'].','.$retName[$jj]['name'];
						    		}
						    		else if ($retName[$jj]['family'] !== '') {
						    			$con .= $retName[$jj]['family'];
						    		}
						    		else {
						    			$con .= $retName[$jj]['name'];
						    		}
						    	}
								// Add LIDO 2014/05/22 S.Suzuki --end--
							}else{
								if(isset($retAttr[$ii]['value'])){
									for ($jj = 0; $jj < count($retAttr[$ii]['value']); $jj++) {
										if($jj != 0){
											$con .= ', ';
										}
										$con .= $retAttr[$ii]['value'][$jj];
									}
								}
							}
						//最後に$pub格納
						if($this->Session->getParameter("_lang") == "japanese"){
							if($retAttr[$ii]['display_lang_type'] == "japanese" || $retAttr[$ii]['display_lang_type'] == ""){
								$this->iiif_fields['institution'] = $con;		
							}else{
								if($contributor_sub == null){
									$contributor_sub = $con;
								}
							}
						}else{
							if($retAttr[$ii]['display_lang_type'] == "english" || $retAttr[$ii]['display_lang_type'] == ""){
								$this->iiif_fields['institution'] = $con;
							}else{
								if($contributor_sub == null){
									$contributor_sub = $con;
								}
							}
						}
						//Add multiple language 2009/09/03 K.Ito --start--
					}
					break;
					
				case 'type':
					if(!isset($this->iiif_fields['type']) && isset($retAttr[$ii]['value'])){
						for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
							if($jj != 0){
								$this->iiif_fields['type'] .= ', ';
							}
							$this->iiif_fields['type'] .= $retAttr[$ii]['value'][$jj];					
						}
					}
					break;
				
				case 'dateofissued':
					if(!isset($this->iiif_fields['year']) && isset($retAttr[$ii]['value'])){
						for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
							if($jj != 0){
								$this->iiif_fields['year'] .= ', ';
							}
							$split_date = split("-", $retAttr[$ii]['value'][$jj]);
				    		$this->iiif_fields['year'] .= $split_date[0];
				    		if($split_date[1] != null){
					    		if($jj != 0){
									$this->iiif_fields['month'] .= ', ';
								}
				    			switch($split_date[1]){
				    				case '01':
				    					$this->iiif_fields['month'] .= 'jan';
				    					break;
				    				case '02':
				    					$this->iiif_fields['month'] .= 'feb';
				    					break;
				    				case '03':
				    					$this->iiif_fields['month'] .= 'mar';
				    					break;
				    				case '04':
				    					$this->iiif_fields['month'] .= 'apr';
				    					break;
				    				case '05':
				    					$this->iiif_fields['month'] .= 'may';
				    					break;
				    				case '06':
				    					$this->iiif_fields['month'] .= 'jun';
				    					break;
				    				case '07':
				    					$this->iiif_fields['month'] .= 'jul';
				    					break;
				    				case '08':
				    					$this->iiif_fields['month'] .= 'aug';
				    					break;
				    				case '09':
				    					$this->iiif_fields['month'] .= 'sep';
				    					break;
				    				case '10':
				    					$this->iiif_fields['month'] .= 'oct';
				    					break;
				    				case '11':
				    					$this->iiif_fields['month'] .= 'nov';
				    					break;
				    				case '12':
				    					$this->iiif_fields['month'] .= 'dec';
				    					break;
				    				default:
				    					$this->iiif_fields['month'] .= $split_date[1];
				    			}
				    		}
						}
					}
					break;
                case 'description':
                    if(!isset($this->iiif_fields['description']) && isset($retAttr[$ii]['value'])){
                        for($jj=0;$jj<count($retAttr[$ii]['value']);$jj++){
                            $this->iiif_fields['description'] .= $retAttr[$ii]['value'][$jj]."\n";
                        }
                    }
                    break;
                case 'fullTextURL':
                    if(isset($retAttr[$ii]['value'])){
                         $this->iiif_fields['fullTextURL'] = $retAttr[$ii]['value'];
                    }
                    break;
				default:
			}
		}
		
		// ページのデータがなかった場合、作成
		if(!isset($this->iiif_fields['pages']) && isset($this->iiif_fields['spage']) && isset($this->iiif_fields['epage'])){
			for($ii=0;$ii<count($this->iiif_fields['spage']);$ii++){
				if($this->iiif_fields['spage'][$ii] != null && $this->iiif_fields['epage'][$ii] != null){
					if(isset($this->iiif_fields['pages'])){
						$this->iiif_fields['pages'] .= ', ';
					}
					$this->iiif_fields['pages'] .= $this->iiif_fields['spage'][$ii].'--'.$this->iiif_fields['epage'][$ii];
				}
			}
		}
		
		//Add multiple language 2009/09/03 K.Ito --start--
		//authorがなかった場合
		if( empty($this->iiif_fields['author']) ){
			$this->iiif_fields['author'] = $author_sub;
		}
		
		//jtitleがなかった場合
		if( empty($this->iiif_fields['booktitle']) ){
			$this->iiif_fields['booktitle'] = $jtitle_sub;
			$this->iiif_fields['journal'] =  $jtitle_sub;
		}
		
		//publisherがなかった場合
    	if( empty($this->iiif_fields['publisher']) ){
			$this->iiif_fields['publisher'] = $publisher_sub;
		}
		
		//contributorがなかった場合
    	if( empty($this->iiif_fields['institution']) ){
			$this->iiif_fields['institution'] = $contributor_sub;
		}
		//Add multiple language 2009/09/03 K.Ito --end--

		
    	$feed = null;
    	
    	switch($retItem[0]['mapping_info']) {
    		case 'Journal Article':
    		case 'Departmental Bulletin Paper':
    		case 'Article':
    		case 'Conference Paper':
    		case 'Presentation':
    		case 'Preprint':
    		case 'Book':
    		case 'Technical Report':
    		case 'Research Paper':
    		case 'Thesis or Dissertation':
    		case 'Learning Material':
    		case 'Data or Dataset':
    		case 'Software':
    		case 'Others':
    			$feed = $this->getManifest();
    			break;
    		default:
    			$feed = $this->getManifest();
    	}
    	return $feed;
    }

    /**
       mhaya
     **/
    function getManifest(){

        // set Context
        $data = array("@context"=>"http://iiif.io/api/presentation/2/context.json","@type"=>"sc:Manifest");
        
        //$uri = $this->repositoryHandleManager->createUri($this->itemId, $this->itemNo);
        // get Detail Item Uri
        // $item_detail = $this->getDetailUri($this->itemId, $this->itemNo);

        // set id
        $manifest_url = BASE_URL."?action=repository_iiif&itemId=".$this->itemId."&itemNo=".$this->itemNo;
        $url = BASE_URL."/repository_iiif/".$this->itemId."/".$this->itemNo;
        
        $data = $data+array("@id"=>$manifest_url);

        // set label
        if(isset($this->iiif_fields['title'])){
            $data += array("label"=>$this->iiif_fields['title']); 
        }
        // set description
        if(isset($this->iiif_fields['description'])){
            $data += array("description"=>$this->iiif_fields['description']); 
        }

        // set metadata
        $metadata = [];
        if(isset($this->iiif_fields['author'])){
            $metadata[] = array("label"=>"Author","value"=>$this->iiif_fields['author']);
        }
        $data += array("metadata"=>$metadata);
        
        // set image url

        $canvases =[];
        if(isset($this->iiif_fields['fullTextURL'])){
              for($jj=0;$jj<count($this->iiif_fields['fullTextURL']);$jj++){
                  $images = [];
                  
                  list($img_url,$imgDesc) = split('\\|',$this->iiif_fields['fullTextURL'][$jj]);
		  
		  $tmp = $this->config['IIIF_IMG_SRV_BASE_URL'];
		  // img_urlからinfoとprofileを取り出す
		  $profile_level = "";
		  $img_info_url="";
		  for($i =0 ;$i < count($tmp);$i++){
		  	 if(strstr($img_url,$tmp[$i])){
				$profile_level = $this->config['IIIF_IMG_SRV_PROFILE'][$i];
				$tmp2=str_replace($tmp[$i],"",$img_url);
	    			$idx=strpos($tmp2,"/");
	        		$idx +=strlen($tmp[$i]);
		    		$img_info_url = substr($img_url,0,$idx);
				break;

			 }
		  }
		  

                  //$img_info_url = preg_replace("/\\/full\\/full\\/0\\/default.jpg$/","",$img_url);
		  if($profile_level!=""){
			$service = array("@context"=>"http://iiif.io/api/image/2/context.json","@id"=>$img_info_url,"profile"=>$profile_level);
                  	$resource = array("@id"=>$img_url,"@type"=>"dctypes:Image","format"=>"image/jpeg","service"=>$service);
		  }else{
			$resource = array("@id"=>$img_url,"@type"=>"dctypes:Image","format"=>"image/jpeg");
}


                  $canvas_url = $url."/canvaces/p".$jj;
                  $images[] = array("@type"=>"oa:Annotation","motivation"=>"sc:painting","on"=>$canvas_url,"resource"=>$resource);
//                  $canvas = array("@id"=>$canvas_url,"@type"=>"sc:Canvas","images"=>$images,"label"=>"p".$jj,"width"=>100,"height"=>150);
                  $canvas = array("@id"=>$canvas_url,"@type"=>"sc:Canvas","images"=>$images,"label"=>$imgDesc,"width"=>100,"height"=>150);
              $canvases[]= $canvas;
              }
        }
        $sequences[]= array("@id"=>$url."/sequence/normal","@type"=>"sc:Sequence","canvases"=>$canvases);

        $data +=array("sequences"=>$sequences);
        $feed = json_encode($data);
        return $feed;
    }

        private function getRepositoryHandleManager()
    {
        if(!isset($this->repositoryHandleManager)){
            if(strlen($this->RepositoryAction->TransStartDate) == 0){
                $date = new Date();
                $this->RepositoryAction->TransStartDate = $date->getDate().".000
";
            }
            $this->repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->dbAccess, $this->RepositoryAction->TransStartDate);
        }
        
    }
        
}
?>
