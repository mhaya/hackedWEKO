<?php

/**
 * Action class for the site map update
 * サイトマップ更新用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Sitemap.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Action class for the site map update
 * サイトマップ更新用アクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Sitemap extends RepositoryAction
{
	// コンポーネント
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
	var $Db;
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
	var $Session;

	// リクエストパラメータ
    /**
     * Administrator login ID
     * 管理者ログインID
     *
     * @var string
     */
	var $login_id = null;
    /**
     * Administrator password
     * 管理者パスワード
     *
     * @var string
     */
	var $password = null;

    /**
     * User of the base level of authority
     * ユーザのベース権限レベル
     *
     * @var string
     */
	var $user_authority_id = "";	// ユーザの権限レベル
	// currentdir is nc2/htdocs
	/**
	 * Sitemap directory path
	 * サイトマップディレクトリパス
	 *
	 * @var unknown_type
	 */
	var $sitemap_dir = "./weko/sitemaps/";
	
	// Add config management authority 2010/02/23 Y.Nakao --start--
    /**
     * User of room privilege level
     * ユーザのルーム権限レベル
     *
     * @var string
     */
	var $authority_id = "";
	// Add config management authority 2010/02/23 Y.Nakao --end--

	/**
	 * Update sitemap
	 * サイトマップ更新
	 *
	 * @access  public
	 */
	function execute()
	{
		try {
			//アクション初期化処理
			$result = $this->initAction();
			if ( $result === false ) {
				$exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
				$DetailMsg = null;                              //詳細メッセージ文字列作成
				sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
				$exception->setDetailMsg( $DetailMsg );         //詳細メッセージ設定
				$this->failTrans();                             //トランザクション失敗を設定(ROLLBACK)
				throw $exception;
			}
			
			// Add config management authority 2010/02/23 Y.Nakao --start--
			$this->setConfigAuthority();
			// Add config management authority 2010/02/23 Y.Nakao --end--
			 
			// check login
			$result = null;
			$error_msg = null;
			$return = $this->checkLogin($this->login_id, $this->password, $result, $error_msg);
			if($return == false){
				print("Incorrect Login!\n");
				return false;
			}
			 
			// check user authority id
			// ログインチェック時に取得される
			// Add config management authority 2010/02/23 Y.Nakao --start--
			//if($this->user_authority_id != 5){
			if($this->user_authority_id < $this->repository_admin_base || $this->authority_id < $this->repository_admin_room){
			// Add config management authority 2010/02/23 Y.Nakao --end--
				print("You are not authorized update.\n");
				return false;
			}

			// サイトマップディレクトリの読み込み権限追加（権限がないと内容取得不可）
			chmod($this->sitemap_dir, 0700);
			 
			// 既存のサイトマップ削除
			if ($handle = opendir("$this->sitemap_dir")) {
				while (false !== ($item = readdir($handle))) {
					if ($item != "." && $item != "..") {
						if (is_dir("$this->sitemap_dir/$item")) {
							$this->removeDirectory("$this->sitemap_dir/$item");
						} else {
							unlink("$this->sitemap_dir/$item");
						}
					}
				}
				closedir($handle);
			}
				
			// サイトマップディレクトリの権限を戻す
			chmod($this->sitemap_dir, 0300);
			 
			//////////////////////////////////////////////
			// アイテム10000件ずつサイトマップ作成
			// item_id順でURLを出力
			// 削除されたアイテムは含めない
			//////////////////////////////////////////////
			$item_num = 0;
			$count = 1;
			// Add 2011/04/04 H.Ito --start--
			$time = array();
			// Add 2011/04/04 H.Ito --end--
			while($count <= 10000){
				// Mod $query for binary-file uri put sitemaps 2009/12/14 K.Ando --start--
				//$query = "SELECT uri, mod_date FROM ".DATABASE_PREFIX. "repository_item".
				$query = "SELECT uri, mod_date, item_id, item_no FROM ".DATABASE_PREFIX. "repository_item".
	        			 " WHERE uri != '' ".
	        			 " AND is_delete = 0".
	        			 " ORDER BY item_id ".
	        			 " LIMIT ". $item_num. ", 10000;";
				// Mod $query for binary-file uri put sitemaps 2009/12/14 K.Ando --end--

				$result = $this->Db->execute($query);
				if($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					return false;
				}
				if(count($result) != 0){
					$this->createSitemap($count, $result);
					// Add 2011/04/04 H.Ito --start--
					// 更新日時取得用
					$time[] = $this->checkTime($result);
					// Add 2011/04/04 H.Ito --end--
				}

				// アイテムなし or 10000件に満たない or 1000番目のサイトマップの場合終了
				if(count($result) < 10000 || count($result) == 0 || $count == 1000){
					break;
				} else {
					$item_num += 10000;
					$count++;
				}
			}
			 // Mod 2011/04/04 H.Ito --start--
			// sitemap_indexファイル作成
            $this->createSitemapIndex($time);
            // Mod 2011/04/04 H.Ito --end--
				
			// アクション終了処理
			$result = $this->exitAction();	// トランザクションが成功していればCOMMITされる
			if ( $result == false ){
				//print "終了処理失敗";
			}

			print("Successfully updated.\n");
			$this->finalize();
			return 'success';

		} catch ( RepositoryException $Exception) {
			//エラーログ出力
			$this->logFile(
	        	"SampleAction",					//クラス名
	        	"execute",						//メソッド名
			$Exception->getCode(),			//ログID
			$Exception->getMessage(),		//主メッセージ
			$Exception->getDetailMsg() );	//詳細メッセージ	      
			//アクション終了処理
			$this->exitAction();                   //トランザクションが失敗していればROLLBACKされる        
			//異常終了
			$this->Session->setParameter("error_msg", $user_error_msg);
			return "error";
		}
	}

	/**
	 * Create sitemap file
	 * サイトマップファイルの作成
	 * 
	 * @param int $num Sitemap numberサイトマップファイルの番号
	 * @param array $item_data Item information アイテム情報の配列
	 *                         array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr_type"][$ii]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
	 */
	function createSitemap($num, $item_data){
		// 改行
		$LF = $this->forXmlChange("\n");
		 
		// $numを0詰めの4桁にフォーマットする
		$fnum = sprintf("%04d", $num);
		 
		// sitemapファイル作成し, gzip形式に圧縮 (ファイル名：sitemap_xxxx.xml.gz)

		$xml = '';
		$xml .= '<?xml version="1.0" encoding="utf-8" ?>'.$LF;
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$LF;
		 
		// アイテムURL記述
		for($ii=0;$ii<count($item_data);$ii++){
			$xml .= '	<url>'.$LF;
			
			// Bug fix item URL 2010/04/16 A.Suzuki --start--
			// item URL is local URL. (No use IDServer URL.)
			// $xml .= '		<loc>'.$this->forXmlChange($item_data[$ii]["uri"]).'</loc>'.$LF;
			$detail_uri = BASE_URL."/?action=repository_uri&item_id=".$item_data[$ii]["item_id"];
			$xml .= '		<loc>'.$this->forXmlChange($detail_uri).'</loc>'.$LF;
			// Bug fix item URL 2010/04/16 A.Suzuki --end--
			
			$xml .= '		<lastmod>'.$this->forXmlChange($this->changeDatetimeToW3C($item_data[$ii]["mod_date"])).'</lastmod>'.$LF;
			$xml .= '	</url>'.$LF;
			// Add binary-file sitemaps 2009/12/14 K.Ando --start--
			// No output binary-file 2010/04/16 A.Suzuki --start--
			// $xml .= $this->createFileSitemap($item_data[$ii]['item_id'], 1);
			// No output binary-file 2010/04/16 A.Suzuki --end--
			// Add binary-file sitemaps 2009/12/14 K.Ando --end--
		}
		 
		$xml .= '</urlset>';

		$file_name = $this->sitemap_dir."sitemap_".$fnum.".xml.gz";
		$gz = gzopen($file_name, "w9");
		if($gz === false){
			return false;
		}
		gzwrite($gz, $xml);
		gzclose($gz);
	}
	
    // Add 2011/04/04 H.Ito --start--
	/**
	 * Search date
	 * 日付検索
	 * 
	 * @param array $item_data Item information アイテム情報の配列
     *                         array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr_type"][$ii]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return string Update date 更新日時
	 */
	function checkTime($item_data){
	    
		$checktime = $this->convTimetoInteger($item_data[0]["mod_date"]);
		$outTime = $item_data[0]["mod_date"];
		for($ii=0;$ii<count($item_data);$ii++){
		    $newtime = $this->convTimetoInteger($item_data[$ii]["mod_date"]);
			if ($checktime < $newtime) {
				$checktime = $newtime;
				$outTime = $item_data[$ii]["mod_date"];
			}
		}
		return $outTime;
	}
	
	/**
	 * Convert timestamp
     * タイムスタンプ数値型変換
     * 
     * @param string $time_data Date string 時刻文字列
     * @return int Convert result 変換結果
     */
	function convTimetoInteger($time_data){
	    
	    $tmp = explode(" ",$time_data);
	    $tmp_day = explode("-", $tmp[0]);
	    
	    // year
	    $outTime = $tmp_day[0];
	    // month
	    if(isset($tmp_day[1])){
	       $outTime .= sprintf("%02d", intVal($tmp_day[1]));
	    } else {
	       $outTime .= "00";
	    }
	    // day
	    if(isset($tmp_day[2])){
	       $outTime .= sprintf("%02d", intVal($tmp_day[2]));
	    } else {
	       $outTime .= "00";
	    }
	    
	    if(isset($tmp[1])){
            $tmp_time = preg_replace("/\..*$/", "", $tmp[1]); 
            $time_times = explode(":", $tmp_time); 
            // hour
            if(isset($time_times[0])){
                $outTime .= sprintf("%02d", intVal($time_times[0]));
            } else {
                $outTime .= "00";
            }
            // minits
            if(isset($time_times[1])){
                $outTime .= sprintf("%02d", intVal($time_times[1]));
            } else {
                $outTime .= "00";
            }
            // second
            if(isset($time_times[2])){
                $outTime .= sprintf("%02d", intVal($time_times[2]));
            } else {
                $outTime .= "00";
            }
        } else{
            $outTime .= "000000";
        }
	    
        return $outTime;
	}
	
	// Add 2011/04/04 H.Ito --end--

	// Mod 2011/04/04 H.Ito --start--
	/**
	 * Create sitemap index
	 * サイトマップインデックスの作成
	 *
	 * @param array $time Updated List 更新日一覧
	 *                    array[$ii]
	 */
	function createSitemapIndex($time){
	    // Mod 2011/04/04 H.Ito --end--

		// 改行
		$LF = $this->forXmlChange("\n");
		 
		// サイトマップディレクトリの読み込み権限追加（権限がないと内容取得不可）
		chmod($this->sitemap_dir, 0700);
		 
		// サイトマップディレクトリの内容取得
		$files = scandir($this->sitemap_dir);
		 
		$xml = '';
		$xml .= '<?xml version="1.0" encoding="UTF-8"?>'.$LF;
		$xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$LF;
		 
		// Add 2011/04/04 H.Ito --start--
		// 更新日時取得用カウンタ
		$timeCnt = 0;
		// Add 2011/04/04 H.Ito --end--
		for($ii=0;$ii<count($files);$ii++){
			// サイトマップファイルを検索
			if(strpos($files[$ii], ".gz") != false){
				// ファイルパス
				$file_path = $this->sitemap_dir. $files[$ii];
				// 更新日取得
				$mtime = filemtime($file_path);
				// Mod 2011/04/04 H.Ito --start--
				$mod_date = $this->changeDatetimeToW3C($time[$timeCnt]);
                $timeCnt++;
                // Mod 2011/04/04 H.Ito --end--
				// XMLに記述
				$xml .= '	<sitemap>'.$LF;
				$xml .= '		<loc>'.$this->forXmlChange(BASE_URL.substr($file_path, 1)).'</loc>'.$LF;
				$xml .= '		<lastmod>'.$this->forXmlChange($mod_date).'</lastmod>'.$LF;
				$xml .= '	</sitemap>'.$LF;
			}
		}
		 
		$xml .= '</sitemapindex>';
		 
		$file_name = $this->sitemap_dir."sitemapindex.xml";
		$handle = fopen($file_name, "w");
		fwrite($handle, $xml);
		fclose($handle);
		 
		// サイトマップディレクトリの権限を戻す
		chmod($this->sitemap_dir, 0300);
	}

    /**
     * create sitemap for files
     * サイトマップファイル作成
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return string XML string XML文字列
     */
	function createFileSitemap($item_id, $item_no)
	{
		// 改行
		$LF = $this->forXmlChange("\n");
		
		// DBよりitem_id にひもつくファイルを取得
		// ファイル名を全て取得
		$query = "SELECT `attribute_id`, `file_name`, `file_no`, `mod_date`".
				 "FROM `". DATABASE_PREFIX ."repository_file` ".
				 "WHERE `item_id` = ? AND ".
				 "	  `item_no` = ? AND ".
				 "	  `is_delete` = 0 ;";
		$xml = NULL;
		$params = array();
		$params[] = $item_id;
		$params[] = $item_no;
		$file_info = $this->Db->execute($query, $params);
		if($file_info === false){
			// SQLエラー
			$this->failTrans();
			return false;
		} else if(count($file_info) != 0){
			for($ii=0;$ii<count($file_info);$ii++){
				// fileのURI作成
				$detail_uri = BASE_URL . "/?action=repository_uri&item_id=".$item_id;
				$detail_uri .= "&file_id=" .$file_info[$ii]['attribute_id'];
				$detail_uri .= "&file_no=" .$file_info[$ii]['file_no'];
				// XML 作成
				$xml .= '	<url>'.$LF;
				$xml .= '		<loc>'.$this->forXmlChange($detail_uri).'</loc>'.$LF;
				$xml .= '		<lastmod>'.$this->forXmlChange($this->changeDatetimeToW3C($file_info[$ii]["mod_date"])).'</lastmod>'.$LF;
				$xml .= '	</url>'.$LF;
			}
		}

		// 作成したURIを返す
		return $xml;
	}
}
?>
