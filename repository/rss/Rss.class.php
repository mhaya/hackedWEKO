<?php

/**
 * Action class for RSS output
 * RSS出力用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Rss.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Index rights management common classes
 * インデックス権限管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryIndexAuthorityManager.class.php';
/**
 * String format conversion common classes
 * 文字列形式変換共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryOutputFilter.class.php';

/**
 * Action class for RSS output
 * RSS出力用アクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Rss extends RepositoryAction
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
	
	// Request param
	/**
	 * Display page number
	 * 表示ページ番号
	 *
	 * @var int
	 */
	var $page = null;			// 表示ページ番号
	/**
	 * Search results display the number of 1 page
	 * 1ページの検索結果表示数
	 *
	 * @var int
	 */
	var $count = null;			// 1ページの検索結果表示数
	/**
	 * Period
	 * 集計期間
	 *
	 * @var int
	 */
	var $term = null;			// 集計期間
	/**
	 * Index id
	 * インデックスID
	 *
	 * @var int
	 */
	var $index_id = null;		// インデックスID
	//Add multiple language 2009/09/04 K.Ito --start--
	/**
	 * Language setting
	 * 言語設定
	 *
	 * @var string
	 */
	var $lang = null;			//言語設定
	//Add multiple language 2009/09/04 K.Ito --end--
	/**
	 * Start number
	 * 開始番号
	 *
	 * @var int
	 */
	var $start_num = 0;
	/**
	 * End number
	 * 終了番号
	 *
	 * @var int
	 */
	var $end_num = 0;
	/**
	 * All item number
	 * 全アイテム数
	 *
	 * @var int
	 */
	var $all_num = 0;
	
	// member
	/**
	 * Feed title
	 * フィードタイトル
	 *
	 * @var string
	 */
	var $feed_title = null;
	
	/**
	 * Output RSS
	 * RSS出力
	 */
	function execute()
	{
		// check Session and Db Object
		if($this->Session == null){
			$container =& DIContainerFactory::getContainer();
	        $this->Session =& $container->getComponent("Session");
		}
		if($this->Db== null){
			$container =& DIContainerFactory::getContainer();
			$this->Db =& $container->getComponent("DbObject");
		}

		// case RSS
					
    	// 初期処理
    	$this->initAction();
    	
		// ヘッダ出力
    	header("Content-Type: application/rss+xml; charset=utf-8");	// レスポンスのContent-Typeを明示的に指定する("application/rss+xml")
		
    	//Add multiple language 2009/09/04 K.Ito --start--
		if($this->lang != "" && $this->lang != null){
			// 言語指定あり
			if($this->lang == "ja" || $this->lang == "japanese"){
				$this->lang = "japanese";
			} else {
				$this->lang = "english";
			}
		} else {
			$this->lang = $this->Session->getParameter("_lang");
		}
		//Add multiple language 2009/09/04 K.Ito --end--
		
		// フィード文字列取得
		$xml = $this->getRssForNewItem();
    	
    	// 取得結果がfalseでなければ
    	if ( $xml != false ) {
	    	// フィード出力
			print $xml;
       	}else{
       		// ヘッダ出力
       		header("Content-Type: text/html; charset=utf-8");	// レスポンスのContent-Typeを明示的に指定する("text/html")
       		print $this->errorMsg;
       	}
       	
       	// 終了処理
    	$this->exitAction();
		$this->finalize();
		// XML書き出し終了後にexit関数を呼び出す
    	exit();

	}

	/**
	 * getRssForNewItem
	 * 検索結果をRSSで出力する
	 * 
	 * @return string Output string 出力文字列
	 */
	function getRssForNewItem()
	{
		$xml = null;
    	$LF = $this->forXmlChange("\n");
    	
    	// xmlヘッダ出力
    	$xml = 	'<?xml version="1.0" encoding="UTF-8" ?>'.$LF.
    			'<rdf:RDF'.$LF.
    			'	xmlns="http://purl.org/rss/1.0/"'.$LF.
    			'	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"'.$LF.
    			'	xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"'.$LF.
    			'	xmlns:dc="http://purl.org/dc/elements/1.1/"'.$LF.
    			'	xmlns:prism="http://prismstandard.org/namespaces/basic/2.0/"'.$LF.
    			'	xml:lang="ja">'.$LF.$LF;
		
        // Add tree access control list 2012/03/07 T.Koyasu -start-
        $public_index = array();
        $role_auth_id = $this->Session->getParameter('_role_auth_id');
        $user_auth_id = $this->Session->getParameter('_user_auth_id');
        $user_id = $this->Session->getParameter('_user_id');
        $this->Session->removeParameter('_role_auth_id');
        $this->Session->removeParameter('_user_auth_id');
        $this->Session->setParameter('_user_id', '0');
        // Add Open Depo 2013/12/03 R.Matsuura --start--
        $this->setConfigAuthority();
        $indexAuthorityManager = new RepositoryIndexAuthorityManager($this->Session, $this->dbAccess, $this->TransStartDate);
        $public_index = $indexAuthorityManager->getPublicIndex(false, $this->repository_admin_base, $this->repository_admin_room, $this->index_id);
        // Add Open Depo 2013/12/03 R.Matsuura --end--
        $this->Session->setParameter('_role_auth_id', $role_auth_id);
        $this->Session->setParameter('_user_auth_id', $user_auth_id);
        $this->Session->setParameter('_user_id', $user_id);

        if(count($public_index) > 0){
            $item_data = $this->getNewItemData();
        } else {
            $this->feed_title = "This index is private.";
        }
        // Add tree access control list 2012/03/07 T.Koyasu -end-
    	// Fix get request_url 2010/04/19 A.Suzuki --start--
		$request_url = BASE_URL;
		if(substr($request_url, -1, 1)!="/"){
			$request_url .= "/";
		}
		$request_url .= "?".$_SERVER['QUERY_STRING'];
		// Fix get request_url 2010/04/19 A.Suzuki --end--
        
        // 検索日時
        //$search_date = date('c');
        $DATE = new Date();
	    $search_date = $DATE->format("%Y-%m-%dT%H:%M:%S%O");
    	
    	// 検索情報出力
    	$xml .= '	<channel rdf:about="'.$this->forXmlChange($request_url).'">'.$LF;	// リクエストURL
    	$xml .= '		<title>'.$this->forXmlChange($this->feed_title).'</title>'.$LF;	// フィードタイトル
    	$xml .= '		<link>'.$this->forXmlChange($request_url).'</link>'.$LF;		// リクエストURL
    	$xml .= '		<dc:date>'.$this->forXmlChange($search_date).'</dc:date>'.$LF;	// 検索日時
    	if($this->all_num != 0){
	    	$xml .= '		<items>'.$LF;
	    	$xml .= '			<rdf:Seq>'.$LF;
	    	for($ii=0;$ii<count($item_data);$ii++){
	    		$xml .= '				<rdf:li rdf:resource="'.$this->forXmlChange($item_data[$ii]["uri"]).'" />'.$LF;	// 詳細画面URL
	    	}
	    	$xml .= '			</rdf:Seq>'.$LF;
	    	$xml .= '		</items>'.$LF;
		}
    	$xml .= '	</channel>'.$LF.$LF;
    	
		// エントリ出力
		for($ii=0;$ii<count($item_data);$ii++){
			$xml .= '	<item rdf:about="'.$this->forXmlChange($item_data[$ii]["uri"]).'">'.$LF;
			$xml .= '		<title>'.$this->forXmlChange($item_data[$ii]["title"]).'</title>'.$LF;		// アイテムタイトル
			$xml .= '		<link>'.$this->forXmlChange($item_data[$ii]["uri"]).'</link>'.$LF;			// 詳細画面URL
			$xml .= '		<rdfs:seeAlso rdf:resource="'.$this->forXmlChange(BASE_URL."/?action=repository_swrc&itemId=".$item_data[$ii]["item_id"]."&itemNo=".$item_data[$ii]["item_no"]).'" />'.$LF;		// SRWC出力URL
			for($jj=0;$jj<count($item_data[$ii]["name"]);$jj++){
				if($item_data[$ii]["name"][$jj] != null){
					$xml .= '		<dc:creator>'.$this->forXmlChange($item_data[$ii]["name"][$jj]).'</dc:creator>'.$LF;
				}
			}
			//Add multiple publisher 2009/09/07 K.Ito --start--
			for($jj=0;$jj<count($item_data[$ii]["publisher"]);$jj++){
				if($item_data[$ii]["publisher"][$jj] != null){
					$xml .= '		<dc:publisher>'.$this->forXmlChange($item_data[$ii]["publisher"][$jj]).'</dc:publisher>'.$LF;	// 出版者
				}
			}
			//Add multiple publisher 2009/09/07 K.Ito --end--
			//Add multiple jtitle 2009/09/07 K.Ito  --start--
			for($jj=0;$jj<count($item_data[$ii]["jtitle"]);$jj++){
				if($item_data[$ii]["jtitle"][$jj] != null){
					$xml .= '		<prism:publicationName>'.$this->forXmlChange($item_data[$ii]["jtitle"][$jj]).'</prism:publicationName>'.$LF;	// 刊行物名
				}
			}
			//Add multiple jtitle 2009/09/07 K.Ito --end--
			// Add index name 2009/08/11 K.ito --start--
			if($item_data[$ii]["index_name"] != null){
				$xml .= '		<dc:subject>'.$this->forXmlChange($item_data[$ii]["index_name"]).'</dc:subject>'.$LF;	// インデックス階層
			}
			// Add index name 2009/08/11 K.ito --end--
			if($item_data[$ii]["issn"] != null){
				$xml .= '		<prism:issn>'.$this->forXmlChange($item_data[$ii]["issn"]).'</prism:issn>'.$LF;	// ISSN
			}
			if($item_data[$ii]["volume"] != null){
				$xml .= '		<prism:volume>'.$this->forXmlChange($item_data[$ii]["volume"]).'</prism:volume>'.$LF;	// 巻
			}
			if($item_data[$ii]["number"] != null){
				$xml .= '		<prism:number>'.$this->forXmlChange($item_data[$ii]["number"]).'</prism:number>'.$LF;	// 号
			}
			if($item_data[$ii]["spage"] != null){
				$xml .= '		<prism:startingPage>'.$this->forXmlChange($item_data[$ii]["spage"]).'</prism:startingPage>'.$LF;	// 開始ページ
			}
			if($item_data[$ii]["epage"] != null){
				$xml .= '		<prism:endingPage>'.$this->forXmlChange($item_data[$ii]["epage"]).'</prism:endingPage>'.$LF;	// 終了ページ
			}
			if($item_data[$ii]["dateofissued"] != null){
				$xml .= '		<prism:publicationDate>'.$this->forXmlChange($item_data[$ii]["dateofissued"]).'</prism:publicationDate>'.$LF;	// 発行年月日
			}
			//Add multiple description 2009/09/07 K.Ito --start--
			//for($jj=0;$jj<count($item_data[$ii]["description"]);$jj++){
				if($item_data[$ii]["description"][0] != null){
					$xml .= '		<description>'.$this->forXmlChange($item_data[$ii]["description"][0]).'</description>'.$LF;	// 抄録
				}
			//}
			//Add multiple description 2009/09/07 K.Ito --end--
			if($item_data[$ii]["shown_date"] != null){
				$xml .= '		<dc:date>'.$this->forXmlChange($item_data[$ii]["shown_date"]).'</dc:date>'.$LF;	// 更新日
			}
			$xml .= '	</item>'.$LF.$LF;
		}
			
    	$xml .= '</rdf:RDF>';
    	return $xml;
	}

	/**
	 * getNewItemData
	 * 新着アイテム情報を返す
	 *
	 * @return array Item information アイテム情報
	 *               array[$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"name"|"publisher"|"description"|"jtitle"]
	 */
	function getNewItemData() {
		// パラメタテーブルの値を取得
		$admin_params = null;
    	$error_msg = null;
    	$return = $this->getParamTableData($admin_params, $error_msg);
    	if($return == false){
    		return false;
    	}
    	
		// 表示件数
		if($this->count != "" || $this->count != null){
    		$this->count = (int)$this->count;
	    	if($this->count > 100){
	    		// 100以上の場合100として処理
	    		$this->count = 100;
	    	} else if($this->count <= 0){
	    		// 0以下の場合デフォルト値を指定
	    		$this->count = $admin_params['default_list_view_num'];
	    	}
    	} else {
    		// デフォルト
	    	$this->count = $admin_params['default_list_view_num'];
    	}
    	
    	// ページ番号
    	if($this->page != "" || $this->page != null){
    		$this->page = (int)$this->page;
    		if($this->page >= 1){
	    		$this->page--;
    		} else {
    			$this->page = 0;
    		}
    	} else {
    		// 指定されていない場合は1ページ目を表示
    		$this->page = 0;
    	}
		
    	// 集計期間
		if($this->term != "" || $this->term != null){
			$this->term = (int)$this->term;
    		if($this->term < 1){
    			// 不正な値の場合はランキングの設定に従う
    			$this->term = $admin_params['ranking_term_recent_regist'];
    		}
    	} else {
    		// 指定されていない場合はランキングの設定に従う
    		$this->term = $admin_params['ranking_term_recent_regist'];
    	}
    	
    	// インデックスID
    	$index_array = array();
	    if($this->index_id != "" || $this->index_id != null){
    		$this->index_id = (int)$this->index_id;
	    	if($this->index_id < 1){
    			// 不正な値の場合は全インデックスから検索
    			$this->index_id = 0;
    		} else {
    			// 子インデックスを検索
    			$this->getChildIndexId($this->index_id, $index_array);
    		}
    	} else {
    		// 指定されていない場合は全インデックスから検索
    		$this->index_id = 0;
    	}
    	
    	// Modify to rss feed title 2010/06/01 A.Suzuki --start--
    	// get parents index name
    	$index_data = array();
    	$this->getParentIndex($this->index_id, $index_data);
    	$idx_names = "";
    	for($ii=0; $ii<count($index_data); $ii++){
			if($idx_names != ""){
				// デリミタ(/)
				$idx_names .= "/";
			}
			if($this->lang == "japanese"){
				if($index_data[$ii]["index_name"] != ""){
					$idx_names .= $index_data[$ii]["index_name"];
				} else {
					$idx_names .= $index_data[$ii]["index_name_english"];
				}
			} else {
				if($index_data[$ii]["index_name_english"] != ""){
					$idx_names .= $index_data[$ii]["index_name_english"];
				} else {
					$idx_names .= $index_data[$ii]["index_name"];
				}
			}
		}
		$this->feed_title = $idx_names;
    	// Modify to rss feed title 2010/06/01 A.Suzuki --end--

    	// 新着アイテム検索
    	$params = array();
    	$query = "SELECT ITEM.item_id, ITEM.item_no ".
    			 "FROM ".DATABASE_PREFIX."repository_item AS ITEM, ".DATABASE_PREFIX."repository_position_index AS P_INDEX ".
    			 //"WHERE ITEM.shown_date >= '".date('Y-m-d H:i:s',mktime()-60*60*24*$this->term)."' ".
    			 "WHERE ITEM.shown_date >= (SELECT DATE_SUB(NOW(), INTERVAL ? DAY)) ".
    			 //"AND ITEM.shown_date <= '".date('Y-m-d H:i:s',mktime())."' ".
    			 "AND ITEM.shown_date <= NOW() ".
    			 $this->log_exception." ".// Add log exception from ip address 2008.11.10 Y.Nakao
			     "AND ITEM.shown_status = 1 ".
			     "AND ITEM.is_delete = 0 ".
    			 "AND ITEM.item_id = P_INDEX.item_id ".
    			 "AND ITEM.item_no = P_INDEX.item_no ";
    		$params[] = $this->term;
    	if($this->index_id != 0 && count($index_array) != 0){
    		$query .= "AND (";
    		for($ii=0; $ii<count($index_array); $ii++){
    			if($ii != 0){
    				$query .= "OR ";
    			}
    			$query .= "P_INDEX.index_id = ? ";
    			$params[] = $index_array[$ii];
    		}
    		$query .= ") ";
    	}
    	$query .= "AND P_INDEX.is_delete = 0 ".
			 	 "ORDER BY ITEM.shown_date desc, ITEM.item_id desc;";
  		$result = $this->Db->execute($query, $params);
  		if($result === false){
  			return false;
  		}
  		
  		// 非公開インデックスのアイテムは除外する
  		$items = array();
  		$Item_ID = array();
  		$Item_No = array();
  		$prev_item_id = "";
  		for($ii=0; $ii<count($result); $ii++){
  			if($result[$ii]['item_id'] != $prev_item_id){
	  			// アイテムの所属するインデックスの公開状況を取得
	  			$query = "SELECT ".DATABASE_PREFIX."repository_index.public_state ,".DATABASE_PREFIX."repository_index.index_id ".
	  					 "FROM ".DATABASE_PREFIX."repository_index, ".DATABASE_PREFIX."repository_position_index ".
	  					 "WHERE ".DATABASE_PREFIX."repository_position_index.item_id = ? ".
	  					 "AND ".DATABASE_PREFIX."repository_position_index.item_no = ? ".
	  					 "AND ".DATABASE_PREFIX."repository_position_index.is_delete = 0 ".
	  					 "AND ".DATABASE_PREFIX."repository_position_index.index_id = ".DATABASE_PREFIX."repository_index.index_id ".
	  					 "AND ".DATABASE_PREFIX."repository_index.is_delete = 0 ;";
	  			$params = array();
	  			$params[] = $result[$ii]['item_id'];
	  			$params[] = $result[$ii]['item_no'];
	  			$tmp_result = $this->Db->execute($query, $params);
	  			
	  			$pub_index_flag = false;
	  			for($jj=0; $jj<count($tmp_result); $jj++){
	  				// 公開中のインデックスがあるか
	  				if($tmp_result[$jj]['public_state'] == "1"){
	  					// 親インデックスが公開されているか
	  					if($this->checkParentPublicState($tmp_result[$jj]['index_id'])){
	  						$pub_index_flag = true;
	  						break;
	  					}
	  				}
	  			}
	  			
	  			// 公開インデックスに所属している場合
	  			if($pub_index_flag){
	  				array_push($Item_ID, $result[$ii]['item_id']);
	  				array_push($Item_No, $result[$ii]['item_no']);
	  				$prev_item_id = $result[$ii]['item_id'];
	  			}
  			}
  		}

		// 検索結果総数
		$this->all_num = count($Item_ID);
		
    	// 表示開始番号を取得
    	$this->start_num = $this->count * $this->page;
    	
    	// 表示可能ページ数以上のページが指定された場合
    	if($this->start_num > $this->all_num){
	    	// 表示される最大ページ数計算
	        $number = (int)($this->all_num / $this->count);
	        if(($this->all_num%$this->count) != 0){
	        	$number++;
	        }
	        $this->page = $number - 1;
	        
	        // 表示開始番号を再取得
    		$this->start_num = $this->count * $this->page;
    	}
    	
    	// 表示終了番号を取得
    	$this->end_num = $this->start_num + $this->count;
		
    	// アイテム情報取得
		$item_data = null;
		$nCnt_view = 0;
		for($nCnt_ID=0;$nCnt_ID<$this->all_num;$nCnt_ID++){
			// 表示範囲内の場合、結果格納
			if($this->start_num<=$nCnt_ID && $nCnt_ID<$this->end_num){
		    	$query = 'SELECT ITEMTYPE.mapping_info, '.
		    			 '		 ITEM.title, '.
		    			//Add multiple language 2009/09/04 K.Ito --start--
		    			'		 ITEM.title_english, '.
		    			//Add multiple language 2009/09/04 K.Ito --start--
		    			 '		 ITEM.language, '.
		    			 '		 ITEM.item_type_id, '.
		    			 '		 ITEM.shown_date, '.
		    			 '		 ITEM.serch_key, '.
		    			 '		 ITEM.mod_date '.
		    			 'FROM '.DATABASE_PREFIX.'repository_item ITEM, '.
		    			 '     '.DATABASE_PREFIX.'repository_item_type ITEMTYPE '.
						 'WHERE ITEM.item_type_id = ITEMTYPE.item_type_id '.
		   				 '  AND ITEM.item_id = ? '.
		    			 '  AND ITEM.item_no = ? '.
		    			 '  AND ITEM.is_delete = 0;';
		    	$params = null;
				$params[] = $Item_ID[$nCnt_ID];
				$params[] = $Item_No[$nCnt_ID];
		 
		    	$retItem = $this->Db->execute($query, $params);
				if ($retItem === false) {
					return false;
				}
		    	
		    	// メタデータ取得
		    	$query = 'SELECT attribute_id, '.
		    	    	 '		 show_order, '.
		    			 '		 attribute_name, '.
		    			 '		 input_type, '.
		    			//Add display lang type 2009/09/04 K.It --start--
		    			 '		 display_lang_type, '.
		    			//Add display lang type 2009/09/04 K.It --end--
		    			 '		 junii2_mapping, '.
		    			 // Fix output hidden metadata 2011/11/28 Y.Nakao --start--
		    			 '		 hidden '.
		    			 // Fix output hidden metadata 2011/11/28 Y.Nakao --end--
		    			 'FROM '.DATABASE_PREFIX.'repository_item_attr_type '.
						 'WHERE item_type_id = ? '.
		    			 '	AND is_delete = 0 '.
		    			 'order by show_order;';
			    $params = null;
			    $params[] = $retItem[0]['item_type_id'];
		    	$retAttr = $this->Db->execute($query, $params);
				if ($retAttr === false) {
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
			    $params[] = $Item_ID[$nCnt_ID];
			    $params[] = $Item_No[$nCnt_ID];
			    $params[] = $retItem[0]['item_type_id'];
		    	$retAttrValue = $this->Db->execute($query, $params);
				if ($retAttrValue === false) {
					return false;
				}
				
				// 複数入力の紐づけ
		    	for($ii=0;$ii<count($retAttr);$ii++){
		    		$cntValue = 0;
		    		for($jj=0;$jj<count($retAttrValue);$jj++){
			    		if($retAttr[$ii]['attribute_id'] == $retAttrValue[$jj]['attribute_id'] && $retAttrValue[$jj]['attribute_id'] !== RepositoryConst::BLANK_WORD){
							$retAttr[$ii]['value'][$cntValue] = $retAttrValue[$jj]['attribute_value'];
							$cntValue++;
						}
		    		}
				}
				
				// Add index_id for rss 2009/08/10 K.Ito --start--		
			    
			    //インデックスIDを求め、そのIDからインデックス階層の文字列を収得
				$query = 'SELECT index_id '.
						 'FROM '. DATABASE_PREFIX .'repository_position_index '.
						 'WHERE item_id = ? '.
						 'AND item_no = ? '.
						 'AND is_delete = 0;';
				$params = null;
				$params[] = $Item_ID[$nCnt_ID];
			    $params[] = $Item_No[$nCnt_ID];		    
			    
			    $retIndex = $this->Db->execute($query,$params);
			    
    			if ($retIndex === false) {
					return false;
				}
				
				$index_name = null;		//一つのインデックス階層文字列保存用
				$index_Sumname = null;	//インデックス階層の文字列の結合用
				
				//インデックス数の分だけ、インデックスの階層文字列を作る
				for ($nCount = 0; $nCount<count($retIndex); $nCount++){
					
				    $parent_index = array();
				    //インデックスIDを収得
				    $index_id = $retIndex[$nCount]["index_id"];
				    
				    //親インデックスの取得
				    $this->getParentIndex($index_id, $parent_index);
			  		
				    
			  		//インデックス階層文字列用
			  		//Add multiple language 2009/09/04 K.Ito --start--
				    if($this->lang == "japanese"){
						if($parent_index[0]["index_name"] != ""){
			  				$index_name[$nCount] = $parent_index[0]["index_name"];
						}else if($parent_index[0]["index_name_english"] != ""){
							$index_name[$nCount] = $parent_index[0]["index_name_english"];
						}
				    }else{
				    	if($parent_index[0]["index_name_english"] != ""){
			  				$index_name[$nCount] = $parent_index[0]["index_name_english"];
						}else if($parent_index[0]["index_name"] != ""){
							$index_name[$nCount] = $parent_index[0]["index_name"];
						}
				    }
			  		
			  		//名前の後ろに"/"で区切って追加していく
			  		for ($nNum = 1; $nNum < count($parent_index); $nNum++){
				  		if($this->lang == "japanese"){
							if($parent_index[$nNum]["index_name"] != ""){
				  				$index_name[$nCount] .= "/".$parent_index[$nNum]["index_name"];
							}else if($parent_index[$nNum]["index_name_english"] != ""){
								$index_name[$nCount] .= "/".$parent_index[$nNum]["index_name_english"];
							}
					    }else{
					    	if($parent_index[$nNum]["index_name_english"] != ""){
				  				$index_name[$nCount] .= "/".$parent_index[$nNum]["index_name_english"];
							}else if($parent_index[$nNum]["index_name"] != ""){
								$index_name[$nCount] .= "/".$parent_index[$nNum]["index_name"];
							}
					    }
			  		}
			  		// Modify to rss feed title 2010/06/01 A.Suzuki --start--
			  		$preg_subject = str_replace("/","\/",$this->feed_title);
			  		if(preg_match("/^".$preg_subject."$/", $index_name[$nCount]) == 1){
			  			$index_name[$nCount] = "";
			  		} else {
			  			$index_name[$nCount] = preg_replace("/^".$preg_subject."\//", "", $index_name[$nCount]);
			  		}
			  		// Modify to rss feed title 2010/06/01 A.Suzuki --end--
			  		
			  		//Add multiple language 2009/09/04 K.Ito --start--
				}
				
				//インデックス階層文字列の連結　最初以降カンマつける
				for ($nCount = 0; $nCount<count($index_name); $nCount++){
					// インデックス階層文字列を結合
					if(strlen($index_Sumname)!=0){
						$index_Sumname .= ", ";
					}
					$index_Sumname .= $index_name[$nCount];
				}
				// Add index_name for rss 2009/08/10 K.Ito --end--
		
				// アイテム詳細画面のURLを取得
				$item_detail = null;
				// Add detail uri 2008/11/13 Y.Nakao --start--
				$item_detail = $this->getDetailUri($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID]);
				
				$item_data[$nCnt_view]["index_name"] = $index_Sumname;	// インデックス名の文字列
				
				$item_data[$nCnt_view]["item_id"] = $Item_ID[$nCnt_ID];	// アイテムID
				$item_data[$nCnt_view]["item_no"] = $Item_No[$nCnt_ID];	// アイテムNo
				$item_data[$nCnt_view]["uri"] = $item_detail;			// 詳細画面URI
				//Add multiple language 2009/09/04 K.Ito --start--
				if($this->lang == "japanese"){
					if($retItem[0]["title"] != ""){
						$item_data[$nCnt_view]["title"] = $retItem[0]["title"];	// タイトル
					}else{
						$item_data[$nCnt_view]["title"] = $retItem[0]["title_english"];	// タイトル
					}
				}else{
					if($retItem[0]["title_english"] != ""){
						$item_data[$nCnt_view]["title"] = $retItem[0]["title_english"];	// タイトル
					}else{
						$item_data[$nCnt_view]["title"] = $retItem[0]["title"];	// タイトル
					}
				}
				//Add multiple language 2009/09/04 K.Ito --end--
				$item_data[$nCnt_view]["shown_date"] = $this->changeDatetimeToW3C($retItem[0]["shown_date"]);	// 更新日
				
				//Add multiple lanaguage 2009/09/07 K.Ito --start--
				//予備の初期化
				$name_sub = null;
				$publisher_sub = null;
				$description_sub = null;
				$jtitle_sub = null;
				//Add multiple language 2009/09/07 K.Ito --end--
				
				for($ii=0;$ii<count($retAttr);$ii++){
					// Fix output hidden metadata 2011/11/28 Y.Nakao --start--
					if($retAttr[$ii]['hidden'] == '1')
					{
						continue;
					}
					// Fix output hidden metadata 2011/11/28 Y.Nakao --end--
					if($retAttr[$ii]['junii2_mapping'] == "creator" && $item_data[$nCnt_view]["name"] == ""){
						//Add multiple language 2009/09/04 K.Ito --start--
						if($this->lang == "japanese" && ($retAttr[$ii]['display_lang_type'] == "japanese" || $retAttr[$ii]['display_lang_type'] == "")){
							$item_data[$nCnt_view]["name"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
						}else if ($this->lang == "english" && ($retAttr[$ii]['display_lang_type'] == "english" || $retAttr[$ii]['display_lang_type'] == "") ){
							$item_data[$nCnt_view]["name"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
						}else{
							if($name_sub == null){
								$name_sub = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
							}
						}
						//Add multiple language 2009/09/04 K.Ito --end--
					} else if($retAttr[$ii]['junii2_mapping'] == "publisher" && $item_data[$nCnt_view]["publisher"] == ""){
						//Add multiple language 2009/09/04 K.Ito --start--
						if($this->lang == "japanese" && ($retAttr[$ii]['display_lang_type'] == "japanese" || $retAttr[$ii]['display_lang_type'] == "")){
							$item_data[$nCnt_view]["publisher"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
						}else if ($this->lang == "english" && ($retAttr[$ii]['display_lang_type'] == "english" || $retAttr[$ii]['display_lang_type'] == "") ){
							$item_data[$nCnt_view]["publisher"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
						}else{
							if($publisher_sub == null){
								$publisher_sub = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
							}
						}
						//$item_data[$nCnt_view]["publisher"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
						//Add multiple language 2009/09/04 K.Ito --end--
					} else if($retAttr[$ii]['junii2_mapping'] == "jtitle" && $item_data[$nCnt_view]["jtitle"] == "" ){
						//Add multiple language 2009/09/07 K.Ito --start--
						if($this->lang == "japanese" && ($retAttr[$ii]['display_lang_type'] == "japanese" || $retAttr[$ii]['display_lang_type'] == "")){
							$item_data[$nCnt_view]["jtitle"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
						}else if ($this->lang == "english" && ($retAttr[$ii]['display_lang_type'] == "english" || $retAttr[$ii]['display_lang_type'] == "") ){
							$item_data[$nCnt_view]["jtitle"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
						}else{
							if($jtitle_sub == null){
								$jtitle_sub = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
							}
						}
						//Add multiple language 2009/09/07 K.Ito --end--
						//$item_data[$nCnt_view]["jtitle"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
					} else if($retAttr[$ii]['junii2_mapping'] == "volume" && $item_data[$nCnt_view]["volume"] == ""){
						$item_data[$nCnt_view]["volume"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
					} else if($retAttr[$ii]['junii2_mapping'] == "issue" && $item_data[$nCnt_view]["number"] == ""){
						$item_data[$nCnt_view]["number"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
					} else if($retAttr[$ii]['junii2_mapping'] == "spage" && $item_data[$nCnt_view]["spage"] == ""){
						$item_data[$nCnt_view]["spage"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
					} else if($retAttr[$ii]['junii2_mapping'] == "epage" && $item_data[$nCnt_view]["epage"] == ""){
						$item_data[$nCnt_view]["epage"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
					} else if($retAttr[$ii]['junii2_mapping'] == "dateofissued" && $item_data[$nCnt_view]["dateofissued"] == ""){
						$item_data[$nCnt_view]["dateofissued"] = $this->changeDatetimeToW3C($this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]));
					} else if($retAttr[$ii]['junii2_mapping'] == "description" && $item_data[$nCnt_view]["description"] == ""){
						//Add multiple language 2009/09/04 K.Ito --start--
						if($this->lang == "japanese" && ($retAttr[$ii]['display_lang_type'] == "japanese" || $retAttr[$ii]['display_lang_type'] == "")){
							$item_data[$nCnt_view]["description"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
						}else if ($this->lang == "english" && ($retAttr[$ii]['display_lang_type'] == "english" || $retAttr[$ii]['display_lang_type'] == "") ){
							$item_data[$nCnt_view]["description"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
						}else{
							if($description_sub == null){
								$description_sub = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
							}
						}
						//$item_data[$nCnt_view]["description"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
						//Add multiple language 2009/09/04 K.Ito --end--
					} else if($retAttr[$ii]['junii2_mapping'] == "issn" && $item_data[$nCnt_view]["ISSN"] == ""){
						$item_data[$nCnt_view]["ISSN"] = $this->getMetaData($Item_ID[$nCnt_ID], $Item_No[$nCnt_ID], $retAttr[$ii]);
					} else if($retAttr[$ii]['junii2_mapping'] == "jtitle,volume,issue,spage,epage,dateofissued"){
	    		    	// 書誌情報の各データを取得
				        $query = 'SELECT biblio_name, '.
				        		 //Add multiple language 2009/09/04 K.Ito --start--
				        		 '		 biblio_name_english, '.
				        		 //Add multiple language 2009/09/04 K.Ito --end--
				        		 '		 volume, '.
				        		 '		 issue, '.
				        		 '		 start_page, '.
				        		 '		 end_page, '.
					    	     '		 date_of_issued '.
				        		 'FROM '.DATABASE_PREFIX.'repository_biblio_info '.
				        		 'WHERE item_id = ? '.
				        		 '	AND item_no = ? '.
				        		 '	AND attribute_id = ? '.
				        		 '	AND is_delete = 0;';
				        $params = null;
				        $params[] = $Item_ID[$nCnt_ID];
				        $params[] = $Item_No[$nCnt_ID];
				        $params[] = $retAttr[$ii]['attribute_id'];
				    	$retBiblio_info = $this->Db->execute($query, $params);
						if ($retBiblio_info === false) {
							return false;
						}
						
						// Add LIDO 2014/05/09 S.Suzuki --start--
						for ($jj = 0; $jj < count($retBiblio_info); $jj++) {
							$retBiblio_info[$jj]['biblio_name']         = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$jj]['biblio_name']);
							$retBiblio_info[$jj]['biblio_name_english'] = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$jj]['biblio_name_english']);
							$retBiblio_info[$jj]['volume']              = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$jj]['volume']);
							$retBiblio_info[$jj]['issue']               = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$jj]['issue']);
							$retBiblio_info[$jj]['start_page']          = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$jj]['start_page']);
							$retBiblio_info[$jj]['end_page']            = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$jj]['end_page']);
							$retBiblio_info[$jj]['date_of_issued']      = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$jj]['date_of_issued']);
						}
						// Add LIDO 2014/05/09 S.Suzuki --end--
						
						if(count($retBiblio_info) > 0){
							//Add multiple language 2009/09/04 K.Ito --start--
							//空チェック
							if($item_data[$nCnt_view]["jtitle"] == ""){
								if($retBiblio_info[0]['biblio_name'] != null || $retBiblio_info[0]['biblio_name_english'] != null){	
									if($this->lang == "japanese"){
										if($retBiblio_info[0]['biblio_name'] != ""){
											$item_data[$nCnt_view]["jtitle"][0] = $retBiblio_info[0]['biblio_name'];
										}else{
											$jtitle_sub[0] = $retBiblio_info[0]['biblio_name_english'];
										}
									}else{
										if($retBiblio_info[0]['biblio_name_english'] != ""){
											$item_data[$nCnt_view]["jtitle"][0] = $retBiblio_info[0]['biblio_name_english'];
										}else{
											$jtitle_sub[0] = $retBiblio_info[0]['biblio_name'];
										}
									}
								}
							}
							//Add multiple language 2009/09/04 K.Ito --end--
							//Add check null 2009/09/07 K.Ito --start--
							//空チェックしないと上書きしてしまいます
							if($item_data[$nCnt_view]["volume"] == ""){
								if($retBiblio_info[0]['volume
								'] != null){
									$item_data[$nCnt_view]["volume"] = $retBiblio_info[0]['volume'];
								}
							}
							if($item_data[$nCnt_view]["number"] == ""){
								if($retBiblio_info[0]['issue'] != null){
									$item_data[$nCnt_view]["number"] = $retBiblio_info[0]['issue'];
								}
							}
							if($item_data[$nCnt_view]["spage"] == ""){
								if($retBiblio_info[0]['start_page'] != null){
									$item_data[$nCnt_view]["spage"] = $retBiblio_info[0]['start_page'];
								}
							}
							if($item_data[$nCnt_view]["epage"] == ""){
								if($retBiblio_info[0]['end_page'] != null){
									$item_data[$nCnt_view]["epage"] = $retBiblio_info[0]['end_page'];
								}
							}
							if($item_data[$nCnt_view]["dateofissued"] == ""){
								if($retBiblio_info[0]['date_of_issued'] != null){
									$item_data[$nCnt_view]["dateofissued"] = $this->changeDatetimeToW3C($retBiblio_info[0]['date_of_issued']);
								}
							}
							//Add check null 2009/09/07 K.Ito --end--
						}
					}
				}
				//Add multiple language 2009/09/07 K.Ito --start--
				//name、publisher,description,jtitleの空チェック
				//空だった場合は代わりを入れる
				if($item_data[$nCnt_view]["name"] == ""){
					$item_data[$nCnt_view]["name"] = $name_sub;
				}
				if($item_data[$nCnt_view]["publisher"] == ""){
					$item_data[$nCnt_view]["publisher"] = $publisher_sub;
				}
				if($item_data[$nCnt_view]["description"] == ""){
					$item_data[$nCnt_view]["description"] = $description_sub;
				}
				if($item_data[$nCnt_view]['jtitle'][0] == ""){
					$item_data[$nCnt_view]['jtitle'] = $jtitle_sub;
				}
				//Add multiple language 2009/09/07 K.Ito --end--
				$nCnt_view++;
			}
        }
		
		return $item_data;
	}
	
	/**
	 * Get metadata
	 * メタデータ取得
	 *
	 * @param int $item_id Item id アイテムID
	 * @param int $item_no Item serial number アイテム通番
	 * @param array $itemData Item information アイテム情報
	 *                        array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr_type"][$ii]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
	 * @return string Metadata メタデータ
	 */
	function getMetaData($item_id, $item_no, $itemData) {
		$rtnVal = '';
		if($itemData['input_type'] == 'name'){
    		$rtnVal = $this->getName($item_id, $item_no, $itemData['attribute_id']);
    	}else{
    		$rtnVal = $itemData['value'];
    	}
	    return str_replace("\n", " ", $rtnVal);
	}
	
	/**
	 * Get author name
	 * 氏名を取得
	 *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
	 * @param int $attribute_id Attribute id 属性ID
	 * @return array Author name list 氏名一覧
	 *               array[$ii]
	 */
	function getName($item_id, $item_no, $attribute_id) {
		// 氏名を取得
        $query = 'SELECT family, '.
    			 '		 name '.
    			 'FROM '.DATABASE_PREFIX.'repository_personal_name '.
				 'WHERE item_id = ? '.
    			 '  AND item_no = ? '.
    			 '  AND attribute_id = ? '.
    			 '  AND is_delete = 0;';
	    $params = null;
		$params[] = $item_id;
		$params[] = $item_no;
		$params[] = $attribute_id;
    	$retName = $this->Db->execute($query, $params);
		if ($retName === false) {
			return false;
		}
		// Add LIDO 2014/05/09 S.Suzuki --start--
		for ($ii = 0; $ii < count($retBiblio_info); $ii++) {
			$retBiblio_info[$ii]['family'] = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$ii]['family']);
			$retBiblio_info[$ii]['name']   = RepositoryOutputFilter::exclusiveReservedWords($retBiblio_info[$ii]['name']);
		}
		
		// 氏名を連結
		$name_array = null;
		for($ii=0;$ii<count($retName);$ii++){
			if ($retName[$ii]['family'] != '' && $retName[$ii]['name'] != '') {
				array_push($name_array, $retName[$ii]['family'].','.$retName[$ii]['name']);
			}
			if ($retName[$ii]['family'] != '' || $retName[$ii]['name'] != '') {
				array_push($name_array, $retName[$ii]['family'].$retName[$ii]['name']);
			}
    	}
		// Add LIDO 2014/05/09 S.Suzuki --end--
		
		return $name_array;
	}
	
	/**
	 * Get child index id
	 * 子インデックスのIDを取得
	 *
	 * @param int $index_id Index id インデックスID
	 * @param array $index_array Child index id list 子インデックスのID一覧
	 *                           array[$ii]
	 */
	function getChildIndexId($index_id, &$index_array) {
		array_push($index_array, $index_id);
		$query = 'SELECT index_id '.
    			 'FROM '.DATABASE_PREFIX.'repository_index '.
				 'WHERE parent_index_id = ? '.
    			 '  AND is_delete = 0;';
	    $params = null;
		$params[] = $index_id;
    	$result = $this->Db->execute($query, $params);
		if ($result === false) {
			return false;
		}
		
		// 子インデックスを取得
		if(count($result) != 0){
	    	for($ii=0;$ii<count($result);$ii++){
	    		$this->getChildIndexId($result[$ii]['index_id'], $index_array);
	    	}
		}
	}
}
?>
