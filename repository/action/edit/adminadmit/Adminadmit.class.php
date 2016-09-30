<?php
/**
 * Action for update admin
 * 管理画面更新Actionクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Adminadmit.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Connect ID server ckass
 * IDサーバー連携クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/IDServer.class.php';
/**
 * Name authority class
 * 氏名メタデータ管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/NameAuthority.class.php';
/**
 * Harvesting process class
 * ハーベスト処理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHarvesting.class.php';
/**
 * Edit tree class
 * ツリー編集クラス
 */
require_once WEBAPP_DIR. '/modules/repository/action/edit/tree/Tree.class.php';
/**
 * Handle manager class
 * ハンドル管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
/**
 * DB const class
 * DB用定数クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDatabaseConst.class.php';

/**
 * Action for update admin
 * 管理画面更新Actionクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Adminadmit extends RepositoryAction
{
	/**
	 * Upload file management objects
	 * アップロードファイル管理オブジェクト
	 *
	 * @var Uploads_Action
	 */
    public $uploadsAction = null;
    
	// リクエストパラメタ (配列ではなく個別で渡す)	
	/**
	 * OS type
	 * OS種別
	 *
	 * @var string
	 */
	var $OS_type = null;						// All : OS kind
	/**
	 * Display index type
	 * インデックス表示形式
	 *
	 * @var int
	 */
	var $disp_index_type = null;				// All : first index view setting
	/**
	 * Default display index
	 * 初期表示インデックス
	 *
	 * @var int
	 */
	var $default_disp_index = null;				// All : first index
	/**
	 * Number of days to be treated as new in the rankings
	 * ランキングで新着として扱う日数
	 *
	 * @var int
	 */
	var $ranking_term_recent_regist = null;		// ranking : new insert time
	/**
	 * Ranking statistics range
	 * ランキング統計範囲
	 *
	 * @var null
	 */
	var $ranking_term_stats = null;				// ranking : ranking time
	/**
	 * Ranking display number
	 * ランキング表示件数
	 *
	 * @var int
	 */
	var $ranking_disp_num = null;	   			// ranking : disp num
	/**
	 * Ranking display flag: most browse item
	 * ランキング表示可否：最も閲覧されたアイテム
	 *
	 * @var int
	 */
	var $ranking_is_disp_browse_item = null;	// ランキング表示可否, 最も閲覧されたアイテム
	/**
	 * Ranking display flag: most download item
	 * ランキング表示可否：最もダウンロードされたアイテム
	 *
	 * @var int
	 */
	var $ranking_is_disp_download_item = null;	// ランキング表示可否, 最もダウンロードされたアイテム
	/**
	 * Ranking display flag: most number of create item user
	 * ランキング表示可否：最もアイテムを作成したユーザー
	 *
	 * @var int
	 */
	var $ranking_is_disp_item_creator = null;	// ランキング表示可否, 最もアイテムを作成したユーザ
	/**
	 * Ranking display flag: most search keyword
	 * ランキング表示可否：最も検索されたキーワード
	 *
	 * @var int
	 */
	var $ranking_is_disp_keyword = null;		// ランキング表示可否, 最も検索されたキーワード
	/**
	 * Ranking display flag: new item
	 * ランキング表示可否：新着アイテム
	 *
	 * @var int
	 */
	var $ranking_is_disp_recent_item = null;	// ランキング表示可否, 新着アイテム
	/**
	 * File output destination at the time of export file
	 * エクスポートファイル時のファイル出力可否
	 *
	 * @var int
	 */
	var $export_is_include_files = null;		// アイテム管理 : Export ファイル出力の可否
	/**
	 * OAI-PMH: admin mail address
	 * OAI-PMH管理：管理者メールアドレス
	 *
	 * @var string
	 */
	var $prvd_Identify_adminEmail = null;		// OAI-PMH管理 : 管理者メールアドレス
	/**
	 * OAI-PMH: repository name
	 * OAI-PMH管理：リポジトリ名
	 *
	 * @var string
	 */
	var $prvd_Identify_repositoryName = null;	// OAI-PMH管理 : リポジトリ名
	/**
	 * OAI-PMH: latest date stamp year
	 * OAI-PMH管理：最新データスタンプ(年)
	 *
	 * @var string
	 */
	var $prvd_Identify_earliestDatestamp_year = null;	// OAI-PMH管理 : earliest_datestamp : year
	/**
	 * OAI-PMH: latest date stamp month
	 * OAI-PMH管理：最新データスタンプ(月)
	 *
	 * @var string
	 */
	var $prvd_Identify_earliestDatestamp_month = null;	// OAI-PMH管理 : earliest_datestamp : month
	/**
	 * OAI-PMH: latest date stamp day
	 * OAI-PMH管理：最新データスタンプ(日)
	 *
	 * @var string
	 */
	var $prvd_Identify_earliestDatestamp_day = null;	// OAI-PMH管理 : earliest_datestamp : day
	/**
	 * OAI-PMH: latest date stamp hour
	 * OAI-PMH管理：最新データスタンプ(時)
	 *
	 * @var string
	 */
	var $prvd_Identify_earliestDatestamp_hour = null;	// OAI-PMH管理 : earliest_datestamp : hour
	/**
	 * OAI-PMH: latest date stamp minutes
	 * OAI-PMH管理：最新データスタンプ(分)
	 *
	 * @var string
	 */
	var $prvd_Identify_earliestDatestamp_minute = null;	// OAI-PMH管理 : earliest_datestamp : minute
	/**
	 * OAI-PMH: latest date stamp second
	 * OAI-PMH管理：最新データスタンプ(秒)
	 *
	 * @var string
	 */
	var $prvd_Identify_earliestDatestamp_second = null;	// OAI-PMH管理 : earliest_datestamp : second
	// Add URL rewrite 2011/11/15 T.Koyasu -start-
	/**
	 * OAI-PMH URL reqrite
	 * OAI-PMH URL書換フラグ
	 *
	 * @var int
	 */
	var $use_url_rewrite = null;                        // OAI-PMH管理 : URL rewrite
	// Add URL rewrite 2011/11/15 T.Koyasu -end-
    // Add Selective Harvesting 2013/09/04 R.Matsuura --start--
	/**
	 * Harvest: start year
	 * ハーベスト：開始年
	 *
	 * @var string
	 */
	public $harvesting_from_date_year = null;	// Selective Harvesting : from : year
	/**
	 * Harvest: start month
	 * ハーベスト：開始月
	 *
	 * @var string
	 */
	public $harvesting_from_date_month = null;	// Selective Harvesting : from : month
	/**
	 * Harvest: start day
	 * ハーベスト：開始日
	 *
	 * @var string
	 */
	public $harvesting_from_date_day = null;	// Selective Harvesting : from : day
	/**
	 * Harvest: start hour
	 * ハーベスト：開始時
	 *
	 * @var string
	 */
	public $harvesting_from_date_hour = null;	// Selective Harvesting : from : hour
	/**
	 * Harvest: start minutes
	 * ハーベスト：開始分
	 *
	 * @var string
	 */
	public $harvesting_from_date_minute = null;	// Selective Harvesting : from : minute
	/**
	 * Harvest: start second
	 * ハーベスト：開始秒
	 *
	 * @var string
	 */
	public $harvesting_from_date_second = null;	// Selective Harvesting : from : second
	/**
	 * Harvest: finish year
	 * ハーベスト：終了年
	 *
	 * @var string
	 */
	public $harvesting_until_date_year = null;	// Selective Harvesting : until : year
	/**
	 * Harvest: finish month
	 * ハーベスト：終了月
	 *
	 * @var string
	 */
	public $harvesting_until_date_month = null;	// Selective Harvesting : until : month
	/**
	 * Harvest: finish day
	 * ハーベスト：終了日
	 *
	 * @var string
	 */
	public $harvesting_until_date_day = null;	// Selective Harvesting : until : day
	/**
	 * Harvest: finish hour
	 * ハーベスト：終了時
	 *
	 * @var string
	 */
	public $harvesting_until_date_hour = null;	// Selective Harvesting : until : hour
	/**
	 * Harvest: finish minutes
	 * ハーベスト：終了分
	 *
	 * @var string
	 */
	public $harvesting_until_date_minute = null;// Selective Harvesting : until : minute
	/**
	 * Harvest: finish second
	 * ハーベスト：終了秒
	 *
	 * @var string
	 */
	public $harvesting_until_date_second = null;// Selective Harvesting : until : second
	/**
	 * Harvesting parameter
	 * ハーベストパラメータ
	 *
	 * @var string
	 */
	public $harvesting_set_param = null;		// Selective Harvesting : set
	/**
	 * Harvesting execute data
	 * ハーベスト実行時間
	 *
	 * @var string
	 */
	public $harvesting_execution_date = null;	// Selective Harvesting : execution date
    // Add Selective Harvesting 2013/09/04 R.Matsuura --end--
	
	
	// Add commd path 2008/08/07 Y.Nakao --start--
	/**
	 * Command path: wvWare
	 * wvWareの実行パス
	 *
	 * @var string
	 */
	var $path_wvWare = null;	// Commd "wvWare"
	/**
	 * Command path: wvWare
	 * wvWareの実行パス
	 *
	 * @var string
	 */
	var $path_xlhtml = null;	// Commd "xlhtml"
	/**
	 * Command path: poppler
	 * popplerの実行パス
	 *
	 * @var string
	 */
	var $path_poppler = null;	// Commd "poppler"
	/**
	 * Command path: ImageMagick
	 * ImageMagickの実行パス
	 *
	 * @var string
	 */
	var $path_ImageMagick = null;	// Commd "ImageMagick"
	/**
	 * Command path: pdftk
	 * pdftkの実行パス
	 *
	 * @var string
	 */
	public $path_pdftk = null;   // Commd "pdftk"  // Add pdftk 2012/06/07 A.Suzuki --start--
	/**
	 * Command path: ffmpeg
	 * ffmpegの実行パス
	 *
	 * @var string
	 */
	public $path_ffmpeg = null; // Commd "ffmpeg"   // Add multimedia support 2012/08/27 T.Koyasu
	/**
	 * Command path: mecab
	 * mecabの実行パス
	 *
	 * @var string
	 */
	public $path_mecab = null; // Commd "mecab"   // Add external search word 2014/05/23 K.Matsuo
	/**
	 * Command path: php
	 * phpの実行パス
	 *
	 * @var string
	 */
	public $path_php = null; // Command "php"
	// Add commd path 2008/08/07 Y.Nakao --end--

	// Add review ON/OFF autoPub etc. 2008/08/08 Y.Nakao --start--
	/**
	 * Item review flag
	 * アイテム査読フラグ
	 *
	 * @var int
	 */
	var $review_flg = null;			// アイテム管理 : 査読・承認ON/OFF
	/**
	 * Item auto public flag after review
	 * アイテム査読後の自動公開フラグ
	 *
	 * @var int
	 */
	var $item_auto_public = null;	// アイテム管理 : 承認済みアイテムの自動公開ss
	// Add review ON/OFF autoPub etc. 2008/08/08 Y.Nakao --end--
	
	// Add Log exclusion 2008/09/12 Y.Nakao --start--
	/**
	 * exclusion log
	 * 除外するログ
	 *
	 * @var string
	 */
	var $log_exclusion = null;	// log restriction
	// Add Log exclusion 2008/09/12 Y.Nakao --end--
	
	// Add Site License 2008/10/20 Y.Nakao --start--
	// Add Site License 2008/10/09 Y.Nakao --start--
	//var $site_license = null;	// site license authorization
	// Add Site License 2008/10/09 Y.Nakao --end--
	/**
	 * Sitelicense ID
	 * サイトライセンス機関ID
	 *
	 * @var array
	 */
	var $sitelicense_id = null;      // org id for site license
	/**
	 * Sitelicense name
	 * サイトライセンス機関名
	 *
	 * @var array
	 */
	var $sitelicense_org = null;	// org name for site license
	/**
	 * Sitelicense group name
	 * サイトライセンスグループ名
	 *
	 * @var array
	 */
	var $sitelicense_group = null;   // org group name for site license
	/**
	 * Sitelicense ip address from
	 * サイトライセンスIPアドレス範囲開始文字列
	 *
	 * @var array
	 */
	var $ip_sitelicense_from = null;	// ip address from for site license
	/**
	 * Sitelicense ip address to
	 * サイトライセンスIPアドレス範囲終了文字列
	 *
	 * @var array
	 */
	var $ip_sitelicense_to = null;	// ip address to for site license
	// Add mail address for feedback mail 2014/04/11 T.Ichikawa --start--
	/**
	 * Sitelicense mail address
	 * サイトライセンス機関メールアドレス
	 *
	 * @var array
	 */
	var $sitelicense_mail = null; // mail address for feedback mail
	// Add mail address for feedback mail 2014/04/11 T.Ichikawa --end--
	// Add select item type for Site License 2009/01/06 A.Suzuki --start--
	/**
	 * Sitelicense usage statistics exclusion item type
	 * サイトライセンス利用統計除外アイテムタイプ
	 *
	 * @var string
	 */
	var $sitelicense_itemtype = null; // allow item type for site license
	// Add select item type for Site License 2009/01/06 A.Suzuki --end--
	// Add Site License 2008/10/20 Y.Nakao --start--
	
	// Add AWSAccessKeyId 2008/11/18 A.Suzuki --start--
	/**
	 * AWS access key ID
	 * 書誌情報簡単登録
	 *
	 * @var int
	 */
	var $AWSAccessKeyId = null;	// 書誌情報簡単登録 : AWSAccessKeyId
	/**
	 * AWS secret access key
	 * AWS秘密鍵
	 *
	 * @var string
	 */
	var $AWSSecretAccessKey = null;
	// Add AWSAccessKeyId 2008/11/18 A.Suzuki --end--
	
	// Add get PrefixID 2008/11/19 --start--
	/**
	 * Y handle prefix ID
	 * YハンドルのプレフィックスID
	 *
	 * @var int
	 */
	var $prefix = null;
	// Add get PrefixID 2008/11/19 --end--
	
	// Add ranking disp setting 2008/12/1 A.Suzuki --start--
	/**
	 * Ranking display setting
	 * ランキング表示設定
	 *
	 * @var int
	 */
	var $ranking_disp_setting = null;
	// Add ranking disp setting 2008/12/1 A.Suzuki --end--
	
	// Add default display type 2008/12/8 A.Suzuki --start--
	// Add default display type 2008/12/8 A.Suzuki --start--
	/**
	 * Ranking display type
	 * ランキング表示タイプ
	 *
	 * @var int
	 */
	var $default_disp_type = null;		// All : default display type setting
	// Add default display type 2008/12/8 A.Suzuki --end--
	
	// Add tab 2009/01/14 A.Suzuki --start--
	/**
	 * Admin view active tab
	 * 管理画面で表示中のタブ
	 *
	 * @var int
	 */
	var $admin_active_tab = null;
	// Add tab 2009/01/14 A.Suzuki --end--
	
	// Add search result setting 2009/03/13 A.Suzuki --start--
	/**
	 * Sort not display
	 * 表示しないソートタイプ
	 *
	 * @var string
	 */
	var $sort_not_disp = null;
	/**
	 * Sort display
	 * 表示するソートタイプ
	 *
	 * @var string
	 */
	var $sort_disp = null;
	/**
	 * Sort display default index
	 * インデックス検索時のデフォルトソートタイプ
	 *
	 * @var string
	 */
	var $sort_disp_default_index = null;
	/**
	 * Sort display default keyword
	 * キーワード検索時のデフォルトソートタイプ
	 *
	 * @var string
	 */
	var $sort_disp_default_keyword = null;
	// Add search result setting 2009/03/13 A.Suzuki --end--
	
	// Add default_list_view_num 2009/03/27 A.Suzuki --start--
	/**
	 * Default search result number
	 * 検索時に1ページに表示するアイテム数
	 *
	 * @var int
	 */
	var $default_list_view_num = null;
	// Add default_list_view_num 2009/03/27 A.Suzuki --end--
	
	// Add currency_setting 2009/06/29 A.Suzuki --start--
	/**
	 * Currency setting
	 * 通貨単位
	 *
	 * @var string
	 */
	var $currency_setting = null;
	// Add currency_setting 2009/06/29 A.Suzuki --end--
	
	// Add select_language 2009/07/01 A.Suzuki --start--
	/**
	 * Select language pull down display
	 * 言語選択プルダウンの表示設定
	 *
	 * @var int
	 */
	var $select_language = null;
	// Add select_language 2009/07/01 A.Suzuki --end--
	
	// Add alternative language setting 2009/08/11 A.Suzuki --start--
	/**
	 * alternatice language display flag: japanese
	 * 他言語メタデータ表示フラグ：日本語
	 *
	 * @var int
	 */
	var $alternative_language_ja = null;
	/**
	 * alternatice language display flag: english
	 * 他言語メタデータ表示フラグ：英語
	 *
	 * @var int
	 */
	var $alternative_language_en = null;
	// Add alternative language setting 2009/08/11 A.Suzuki --end--
	
	// Add supple WEKO setting 2009/09/09 A.Suzuki --start--
	/**
	 * Supple WEKO URL
	 * サプリWEKOのURL
	 *
	 * @var string
	 */
	var $supple_weko_url = null;
	/**
	 * Supple review flag
	 * サプリWEKO査読フラグ
	 *
	 * @var int
	 */
	var $review_flg_supple = null;
	// Add supple WEKO setting 2009/09/09 A.Suzuki --end--

	// Add review mail setting 2009/09/24 Y.Nakao --start--
	/**
	 * Send review mail flag
	 * 査読メール通知フラグ
	 *
	 * @var int
	 */
	var $review_mail_flg = null;
	/**
	 * review mail address
	 * 査読通知先メールアドレス
	 *
	 * @var string
	 */
	var $review_mail = null;
	// Add review mail setting 2009/09/24 Y.Nakao --end--

	// Add help icon display setting  2010/02/10 K.Ando --start--
	/**
	 * Help icon display flag
	 * ヘルプアイコン表示フラグ
	 *
	 * @var int
	 */
	var $help_icon_display = null;
	/**
	 * OAI-ORE icon display flag
	 * OAI-OREアイコン表示フラグ
	 *
	 * @var int
	 */
	var $oaiore_icon_display = null;
	// Add help icon display setting  2010/02/10 K.Ando --end--
	
	// Add index list 2011/4/5 S.Abe --start--
	/**
	 * Select index list display
	 * 選択インデックス表示フラグ
	 *
	 * @var int
	 */
	var $select_index_list_display = null;
	// Add index list 2011/4/5 S.Abe --end-- 
	
    // Add AssociateTag for modify API Y.Nakao 2011/10/19 --start--
	/**
	 * Associate tag
	 * Amazon associate tag
	 *
	 * @var string
	 */
    public $AssociateTag = null;
    // Add AssociateTag for modify API Y.Nakao 2011/10/19 --end--
    
    // Add harvesting 2012/03/05 A.Suzuki --start--
	/**
	 * repositoryIDs for harvesting
	 * ハーベスト機関ID
	 *
	 * @var array
	 */
	public $harvesting_repositoryId = null;
	/**
	 * repositoryNames for harvesting
	 * ハーベスト機関名
	 *
	 * @var array
	 */
	public $harvesting_repositoryName = null;

	/**
	 * baseUrl for harvesting
	 * ハーベスト機関URL
	 *
	 * @var array
	 */
	public $harvesting_baseUrl = null;

	/**
	 * metadataPrefix for harvesting
	 * ハーベストのプレフィックス
	 *
	 * @var array
	 */
	public $harvesting_metadataPrefix = null;

	/**
	 * post_index for harvesting
	 * ハーベストアイテム登録先インデックス
	 *
	 * @var array
	 */
	public $harvesting_post_index = null;

	/**
	 * harvesting_post_name for harvesting
	 * ハーベスト登録名
	 *
	 * @var array
	 */
	public $harvesting_post_name = null;

	/**
	 * automatic_sorting for harvesting
	 * ハーベストインデックス自動振り分けフラグ
	 *
	 * @var array
	 */
	public $harvesting_automatic_sorting = null;
    // Add harvesting 2012/03/05 A.Suzuki --end--

	/**
	 * PDF cover header type
	 * PDFカバーヘッダータイプ
	 *
	 * @var string
	 */
	public $pdf_cover_header_type = null;

	/**
	 * PDF cover header text
	 * PDFカバーヘッダーメッセージ
	 *
	 * @var string
	 */
	public $pdf_cover_header_text = null;

	/**
	 * PDF cover header align
	 * PDFカバーページヘッダーレイアウト
	 *
	 * @var string
	 */
	public $pdf_cover_header_align = null;

	/**
	 * PDF cover header image delete flag
	 * PDFカバーページ画像削除フラグ
	 *
	 * @var int
	 */
	public $pdf_cover_header_image_del = null;

	/**
	 * Additional exclude address for feedback mail
	 * フィードバックメール送信除外追加リスト
	 *
	 * @var int
	 */
    public $feedback_exclude_address_list = null;

	/**
	 * is connect checked ICHUSHI
	 * 医中誌コンテンツフラグ
	 *
	 * @var string
	 */
	public $ichushiIsConnect = null;

	/**
	 * ICHUSHI login id
	 * 医中誌ログインID
	 *
	 * @var string
	 */
	public $ichushiLoginId = null;

	/**
	 * ICHUSHI login password
	 * 医中誌ログインパスワード
	 *
	 * @var string
	 */
	public $ichushiLoginPasswd = null;

	/**
	 * Feedback send mail activate flag
	 * フィードバックメール送信可否フラグ
	 *
	 * @var string
	 */
    public $feedbackSendMailActivateFlag = null;
    // Add private tree parameter K.matsuo 2013/4/5 --start--
	/**
	 * make private tree flag
	 * プライベートツリー作成フラグ
	 *
	 * @var int
	 */
	public $is_make_privatetree = null;		// プライベートツリー : プライベートツリー作成の可否
	/**
	 * Private tree sort order
	 * プライベートツリーのソート順序
	 *
	 * @var int
	 */
	public $privatetree_sort_order = null;	// プライベートツリー : プライベートツリーのソート順
	/**
	 * Private tree parent index ID
	 * プライベートツリー作成先インデックスID
	 *
	 * @var null
	 */
	public $privatetree_parent_indexid = NULL;	// プライベートツリー : プライベートツリーの親インデックスのID
    // Add private tree parameter K.matsuo 2013/4/5 --end--
    
    // Add prefix Admin T.Ichikawa 2013/12/24 --start--
	/**
	 * JaLC DOI prefix
	 * JaLC DOIのプレフィックス
	 *
	 * @var string
	 */
	public $prefixJalcDoi = null;
	/**
	 * CrossRef DOI prefix
	 * CrossRef DOIのプレフィックス
	 *
	 * @var string
	 */
	public $prefixCrossRef = null;
	// Add DataCite 2015/02/09 K.Sugimoto --start--
	/**
	 * DataCite DOI prefix
	 * DataCite DOIのプレフィックス
	 *
	 * @var string
	 */
	public $prefixDataCite = null;
	// Add DataCite 2015/02/09 K.Sugimoto --end--
	/**
	 * CNRI prefix
	 * CNRIのプレフィックス
	 *
	 * @var string
	 */
	public $prefixCnri = null;
    // Add prefix Admin T.Ichikawa 2013/12/24 --end--
    
    // Add Detail Search 2013/11/20 R.Matsuura --start--
    /**
     * Detail Search Flag for Use or Not
	 * 詳細検索使用フラグ
     *
     * @var array
     */
    public $search_use_flag = null;
    /**
     * Detail Search Default Display Flag
     * 詳細検索表示フラグ
     *
     * @var array
     */
    public $search_default_flag = null;
    /**
     * Detail Search ID List
	 * 検索IDリスト
     *
     * @var array
     */
    public $search_type_id = null;
    // Add Detail Search 2013/11/20 R.Matsuura --end--
    
    // Add External Search Word 2014/05/22 K.Matsuo --start--
    /**
     * external searchword
	 * 外部検索キーワード
     *
     * @var array
     */
    public $external_searchword_word = null;
	/**
	 * external searchword stopword rules
	 * 外部検索キーワードストップワードルール
	 *
	 * @var array
	 */
    public $external_searchword_stopword_rule = null;
	/**
	 * external searchword show flag
	 * 外部検索キーワード表示フラグ
	 *
	 * @var int
	 */
    public $external_searchword_show = null;
    // Add External Search Word 2014/05/22 K.Matsuo --end--
    
    // OAI-PMH Output Flag
	/**
	 * OAI-PMH output flag
	 * OAI-PMH出力フラグ
	 *
	 * @var int
	 */
    public $oaipmh_output_flag = null;
    
    // Institution Name
	/**
	 * Institution name
	 * 機関名
	 *
	 * @var string
	 */
    public $institutionName = null;
    
    // Add Default Search Type 2014/12/03 K.Sugimoto --start--
    // Default Search Type
	/**
	 * Default search type
	 * デフォルト検索タイプ
	 *
	 * @var string
	 */
	public $default_search_type = null;
	// Add Default Search Type 2014/12/03 K.Sugimoto --end--

	// Add Usage Statistics link display setting 2014/12/16 K.Matsushita --start--
	/**
	 * Usage statistics display flag
	 * 利用統計リンク表示フラグ
	 *
	 * @var null
	 */
    public $usagestatistics_link_display = null;
    // Add Usage Statistics link display setting 2014/12/16 K.Matsushita --end--

    // Add ranking tab display setting 2014/12/19 K.Matsushita --start--
	/**
	 * Ranking tab display
	 * 表示中のランキングタブ
	 *
	 * @var int
	 */
    public $ranking_tab_display = null;
    // Add ranking tab display setting 2014/12/19 K.Matsushita --end--
    
    // Add DataCite 2015/02/09 K.Sugimoto --start--
    // PrefixID Add Flag
	/**
	 * ConnectID serer flag
	 * IDServer連携フラグ
	 *
	 * @var int
	 */
    public $prefix_flag = null;
    // Add DataCite 2015/02/09 K.Sugimoto --end--
    
    // Auto Input Metadata by CrossRef DOI 2015/03/02 K.Sugimoto --start--
	/**
	 * CrossRef service account
	 * CrossRefのアカウント情報
	 *
	 * @var string
	 */
    public $CrossRefQueryServicesAccount = null;
    // Auto Input Metadata by CrossRef DOI 2015/03/02 K.Sugimoto --end--
    
    // Add RobotList 2015/04/06 S.Suzuki --start--
	/**
	 * Robot list valid flag
	 * ロボットリスト使用フラグ
	 *
	 * @var int
	 */
    public $robotlistValid = null;
    // Add RobotList 2015/04/06 S.Suzuki --end--
    
    /**
     * Value of type of author search
     * 著者名検索の設定値
     *
     * @var int
     */
    public $author_search_type = null;
    
    /**
     * List of external author ID prefix name
     * 外部著者IDのプレフィックス名一覧
     *
     * @var array array[$ii]
     */
    public $external_author_id_prefix_list = null;
    
    /**
     * List of external author ID prefix ID
     * 外部著者IDのプレフィックスID一覧
     *
     * @var array array[$ii]
     */
    public $external_author_id_prefix_list_id = null;
    
    /**
     * List of external author ID prefix url
     * 外部著者IDのプレフィックスURL一覧
     *
     * @var array array[$ii]
     */
    public $external_author_id_prefix_list_url = null;

	/**
	 * Execute
	 * 実行
	 *
	 * @return string "success"/"error" success/failed 成功/失敗
	 * @throws AppException
	 */
	function executeApp()
	{
            // add get lang 2012/12/05 A.Jin --start--
            $smartyAssign = $this->Session->getParameter("smartyAssign");    // for get language resource
            // add get lang 2012/12/05 A.Jin --end--
			
			$this->Session->removeParameter("error_msg");
			
			// fix active tab is numeric. 2011/07/28 Y.Nakao --start--
			$this->admin_active_tab = intval($this->admin_active_tab);
			if($this->admin_active_tab < 0){
			    $this->admin_active_tab = 0;
			} else if($this->admin_active_tab > 2){
			    $this->admin_active_tab = 2;
			}
			$this->Session->setParameter("admin_active_tab", intval($this->admin_active_tab));
			// fix active tab is numeric. 2011/07/28 Y.Nakao --end--

			// ----------------------------------------------------
			// init
			// ----------------------------------------------------
 			$edit_start_date = $this->Session->getParameter("edit_start_date");
			$Error_Msg = '';			// エラーメッセージ
			$user_id = $this->Session->getParameter("_user_id");	// ユーザID
			$params = null;				// パラメタテーブル更新用クエリ			
			$params[] = '';				// param_value
			$params[] = $user_id;				// mod_user_id
			$params[] = $this->TransStartDate;	// mod_date
			$params[] = '';				// param_name
			// Add remove URL rewrite error message 2011/11/16 T.Koyasu --start--
            $this->Session->removeParameter("repository_admin_url_rewrite_error");
            // Add remove URL rewrite error message 2011/11/16 T.Koyasu --end--
			
			// check date : year, month, date
			if($this->prvd_Identify_earliestDatestamp_year == null) {
                $this->Session->setParameter("error_msg", "error : invalid date input.");
                $this->errorLog("error : invalid date input.", __FILE__, __CLASS__, __LINE__);
                throw new AppException("error : invalid date input.");
			}
			if( checkdate(	intval($this->prvd_Identify_earliestDatestamp_month),
							intval($this->prvd_Identify_earliestDatestamp_day),
							intval($this->prvd_Identify_earliestDatestamp_year)) == false ) {
                $this->Session->setParameter("error_msg", "error : invalid date input.");
                $this->errorLog("error : invalid date input.", __FILE__, __CLASS__, __LINE__);
                throw new AppException("error : invalid date input.");
			}					
			// Add Selective Harvesting 2013/09/04 R.Matsuura --start--
			for($ii=0;$ii<count($this->harvesting_from_date_year);$ii++) {
			    if(checkdate( intval($this->harvesting_from_date_month[$ii]),
			                  intval($this->harvesting_from_date_day[$ii]),
			                  intval($this->harvesting_from_date_year[$ii])) == false ) {
                    $this->Session->setParameter("error_msg", "error : invalid date input.");
                    $this->errorLog("error : invalid date input.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("error : invalid date input.");
			    }
                if(checkdate( intval($this->harvesting_until_date_month[$ii]),
                              intval($this->harvesting_until_date_day[$ii]),
                              intval($this->harvesting_until_date_year[$ii])) == false ) {
                    $this->Session->setParameter("error_msg", "error : invalid date input.");
                    $this->errorLog("error : invalid date input.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("error : invalid date input.");
                }
                
                // Consolidated inthe form of YYYY-MM-DDThh:mm:ssZ
                $from_date = $this->harvesting_from_date_year[$ii] . "-" .
                            $this->harvesting_from_date_month[$ii] . "-" .
                            $this->harvesting_from_date_day[$ii] . "T" .
                            $this->harvesting_from_date_hour[$ii] . ":" .
                            $this->harvesting_from_date_minute[$ii] . ":" .
                            $this->harvesting_from_date_second[$ii] . "Z";
                // Consolidated inthe form of YYYY-MM-DDThh:mm:ssZ
                $until_date = $this->harvesting_until_date_year[$ii] . "-" .
                            $this->harvesting_until_date_month[$ii] . "-" .
                            $this->harvesting_until_date_day[$ii] . "T" .
                            $this->harvesting_until_date_hour[$ii] . ":" .
                            $this->harvesting_until_date_minute[$ii] . ":" .
                            $this->harvesting_until_date_second[$ii] . "Z";
                if($from_date > $until_date) {
                    $this->Session->setParameter("error_msg", "error : invalid date input.");
                    $this->errorLog("error : invalid date input.", __FILE__, __CLASS__, __LINE__);
                    throw new AppException("error : invalid date input.");
                }
            }
			// Add Selective Harvesting 2013/09/04 R.Matsuura --end--
			
		 	// ----------------------------------------------------
			// read admin params
			// ----------------------------------------------------
			$admin_records = array();		// admin param rec colom
			$Error_Msg = '';				// error msg
			// get all params data
		   	$result = $this->getParamTableRecord($admin_records, $Error_Msg);
	   		if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("item_coef_cp update failed : %s", $ii, $jj, $errMsg );
                $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}

			// ----------------------------------------------------
			// update start time > last update time
			// *** If different admin update data then OUT			
			// ----------------------------------------------------
			$admin_records_old = $this->Session->getParameter("admin_params");
			foreach( $admin_records as $key => $value ){
//				if($istest){echo $key . " : " . $edit_start_date . " : " . $value['mod_date'] .  "<br>";}
				// If Edit start time different update time then not update
				if( $admin_records_old[$key]['mod_date'] != $value['mod_date'] ) {	
					$this->Session->setParameter("error_msg", "error : probably " . $key . " was updated by other admin.");
					$tmpstr = "error : probably " . $key . " was updated by other admin.";
                    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			
			// Add get Prefix 2008/11/19 --start--
			// ----------------------------------------------------
			// update only prefix
			// ----------------------------------------------------
			if($this->prefix){
                // Mod Item handle management T.Koyasu 2014/01/28 --start--
                $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->Db, $this->TransStartDate);
                
                // register prefix by private key and insert database
                $result = $repositoryHandleManager->registerYHandlePrefixByPriKey();
                
                if($result === false)
                {
                    // when not entry prefix, entry prefix.
                    return "entry";
                }
                // Mod Item handle management T.Koyasu 2014/01/28 --end--
				// end action
				//$result = $this->exitAction();
				return 'success';
			}
			// Add get Prefix 2008/11/19 --end--
			
			// ----------------------------------------------------
			// every admin param upadte
			// ***If insert "" then not upadte
			// ----------------------------------------------------
			/* OS設定：自動判別を行うため削除 2009/03/18 A.Suzuki
			if( $this->OS_type != null ){
				// All : OS kind
				$params[0] = $this->OS_type;	// param_value
				$params[3] = 'OS_type';			// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("OS_type update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
					$this->failTrans();		//ROLLBACK
                    $this->exitAction();
					return 'error';
				}
			}
			*/
			
			// Add default display type setting 2008/12/8 A.Suzuki --start--
			if( $this->default_disp_type != null ){
				// All : default display type
				$params[0] = $this->default_disp_type;	// param_value
				$params[3] = 'default_disp_type';			// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("default_disp_type update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
                    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// Add default display type setting 2008/12/8 A.Suzuki --end--
			if( $this->disp_index_type != null ){
				// リポジトリ全般 : 初期表示インデックス表示方法
				$params[0] = $this->disp_index_type;	// param_value
				$params[3] = 'disp_index_type';			// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("disp_index_type update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
                    throw new AppException($tmpstr);
				}
			}
			if( $this->default_disp_index != null ){
				// リポジトリ全般 : 初期表示インデックス
				$params[0] = $this->default_disp_index;	// param_value
				$params[3] = 'default_disp_index';		// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("default_disp_index update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
                    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			
			// Add select_language 2009/07/01 A.Suzuki --start--
			if( $this->select_language != null ){
				// 表示設定 : 言語選択表示設定
				$params[0] = $this->select_language;	// param_value
				$params[3] = 'select_language';			// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("select_language update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
                    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// Add select_language 2009/07/01 A.Suzuki --end--
			
			// Add alternative language setting 2009/08/11 A.Suzuki --start--
			if( $this->alternative_language_ja == null ){
				$alter_flg_jp = "japanese:0";
			} else {
				$alter_flg_jp = "japanese:1";
			}
			if( $this->alternative_language_en == null ){
				$alter_flg_en = "english:0";
			} else {
				$alter_flg_en = "english:1";
			}
			// 表示設定 : 他言語表示設定
			$params[0] = $alter_flg_jp.",".$alter_flg_en;	// param_value
			$params[3] = 'alternative_language';			// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("alternative_language update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
                $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Add alternative language setting 2009/08/11 A.Suzuki --end--
			
			// Add currency_setting 2009/06/29 A.Suzuki --start--
			if( $this->currency_setting != null ){
				// 表示設定 : 通貨単位設定
				$params[0] = $this->currency_setting;	// param_value
				$params[3] = 'currency_setting';		// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("currency_setting update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
                    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// Add currency_setting 2009/06/29 A.Suzuki --end--
			
			// Add ranking disp setting 2008/12/1 A.Suzuki --start--
			if( $this->ranking_disp_setting != null ){
				// ランキング管理 : 表示設定
				$params[0] = $this->ranking_disp_setting;	// param_value
				$params[3] = 'ranking_disp_setting';		// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("ranking_disp_setting update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
                    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// Add ranking disp setting 2008/12/1 A.Suzuki --end-- 
			
			// Add ranking tab display setting 2014/12/19 K.Matsushita --start--
			// 現在の設定値取得
			$paramsGetRankingDisplay = array('param_name' => 'ranking_tab_display');
			$rankingDisplayInfo = $this->Db->selectExecute("repository_parameter",$paramsGetRankingDisplay);
			if ($rankingDisplayInfo === false) {
			    return 'error';
			}
			
			// DBの値とセッションの値を比較
			if( $rankingDisplayInfo[0]['param_value'] !== $this->ranking_tab_display ){
			    // 異なる場合
			    
			    // maple.ini書き換え
			    $view_main_maple_ini_file_path = WEBAPP_DIR. '/modules/repository/view/main/maple.ini';
			    
			    // Check file read & write rights
			    if(is_readable($view_main_maple_ini_file_path) && is_writable($view_main_maple_ini_file_path)){
			        
			        // 読み込み
			        $fp = fopen($view_main_maple_ini_file_path, "r");
			        $maple_ini_text = array();
			        while ($line = fgets($fp)) {
			            $maple_ini_text[] = $line;
			        }
			        fclose($fp);
			        
			        // 書き込み
			        $fp = fopen($view_main_maple_ini_file_path, "w");
			        for($ii = 0; $ii < count($maple_ini_text); $ii++){
			             
			            if( $this->ranking_tab_display == 1 && strpos($maple_ini_text[$ii], "define:repository_view_main_ranking") !== false ){
			                $maple_ini_text[$ii] = '_ranking = "define:repository_view_main_ranking"'."\n";
			            }
			            else if( $this->ranking_tab_display == 0 && strpos($maple_ini_text[$ii], "define:repository_view_main_ranking") !== false )
			            {
			                $maple_ini_text[$ii] = ';_ranking = "define:repository_view_main_ranking"'."\n";//先頭をコメントアウト
			            }
			            fwrite($fp, $maple_ini_text[$ii]);
			        }
			        fclose($fp);
			        
			        // DBの値を更新
			        if( $this->ranking_tab_display != null ){
			            // ランキング管理 : ランキングタブ表示設定
			            $params[0] = $this->ranking_tab_display;	// param_value
			            $params[3] = 'ranking_tab_display';		// param_name
			            $result = $this->updateParamTableData($params, $Error_Msg);
			            if ($result === false) {
			                $errMsg = $this->Db->ErrorMsg();
			                $tmpstr = sprintf("ranking_tab_display_setting update failed : %s", $ii, $jj, $errMsg );
			                $this->Session->setParameter("error_msg", $tmpstr);
                            $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                            throw new AppException($tmpstr);
			            }
			        }
			    }
			    else{
			        // エラーメッセージ
			        $tmpstr = "error: impossible to read and write view/main/maple.ini";
			        $this->Session->setParameter("error_msg", $tmpstr);
                    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
			    }
			}
			// Add ranking tab display setting 2014/12/19 K.Matsushita --end--
			
			
			if( $this->ranking_term_recent_regist != null ){
				// ランキング管理 : 新規登録期間
				$params[0] = intval($this->ranking_term_recent_regist);	// param_value
				$params[3] = 'ranking_term_recent_regist';				// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("ranking_term_recent_regist update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
				    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			if( $this->ranking_term_stats != null ){
				// ランキング管理 : 統計期間
				$params[0] = intval($this->ranking_term_stats);	// param_value
				$params[3] = 'ranking_term_stats';				// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("ranking_term_stats update failed : %s", $ii, $jj, $errMsg ); 
                    $this->Session->setParameter("error_msg", $tmpstr);
				    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			if( $this->ranking_disp_num != null ){
				// ランキング管理 : 表示順位
				$params[0] = intval($this->ranking_disp_num);	// param_value
				$params[3] = 'ranking_disp_num';		// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("ranking_disp_num update failed : %s", $ii, $jj, $errMsg ); 
                    $this->Session->setParameter("error_msg", $tmpstr);
				    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// ランキング表示可否, 最も閲覧されたアイテム
			if( $this->ranking_is_disp_browse_item == null ){
				$flg = 0;
			} else {
				$flg = 1;
			}
   			$params[0] = $flg;								// param_value
   			$params[3] = 'ranking_is_disp_browse_item';		// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("ranking_is_disp_browse_item update failed : %s", $ii, $jj, $errMsg ); 
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// ランキング表示可否, 最もダウンロードされたアイテム
			if( $this->ranking_is_disp_download_item == null ){
				$flg = 0;
			} else {
				$flg = 1;
			}
   			$params[0] = $flg;								// param_value
   			$params[3] = 'ranking_is_disp_download_item';	// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("ranking_is_disp_download_item update failed : %s", $ii, $jj, $errMsg ); 
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// ランキング表示可否, 最もアイテムを作成したユーザ
			if( $this->ranking_is_disp_item_creator == null ){
				$flg = 0;
			} else {
				$flg = 1;
			}
   			$params[0] = $flg;								// param_value
   			$params[3] = 'ranking_is_disp_item_creator';	// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("ranking_is_disp_item_creator update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// ランキング表示可否, 最も検索されたキーワード
			if( $this->ranking_is_disp_keyword == null ){
				$flg = 0;
			} else {
				$flg = 1;
			}
   			$params[0] = $flg;							// param_value
   			$params[3] = 'ranking_is_disp_keyword';		// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("ranking_is_disp_keyword update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
                $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// ランキング表示可否, 新着アイテム
			if( $this->ranking_is_disp_recent_item == null ){
				$flg = 0;
			} else {
				$flg = 1;
			}
   			$params[0] = $flg;								// param_value
   			$params[3] = 'ranking_is_disp_recent_item';		// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("ranking_is_disp_recent_item update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
/*
			// アイテム数／ファイル要領制限関連は保留
			if( $this->item_coef_cp != null ){
				// アイテム管理 : 係数Cp
				$params[0] = floatval($this->item_coef_cp);	// param_value
				$params[3] = 'item_coef_cp';				// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("item_coef_cp update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
					$this->failTrans();		//トランザクション失敗を設定(ROLLBACK)
                    $this->exitAction();
					return 'error';
				}
			}
			
			if( $this->item_coef_ci != null ){
				// アイテム管理 : 係数Ci
				$params[0] = intval($this->item_coef_ci);	// param_value
				$params[3] = 'item_coef_ci';				// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("item_coef_ci update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
					$this->failTrans();		//トランザクション失敗を設定(ROLLBACK)
                    $this->exitAction();
					return 'error';
				}
			}
			if( $this->file_coef_cp != null ){
				// アイテム管理 : 係数Cpf
				$params[0] = floatval($this->file_coef_cp);	// param_value
				$params[3] = 'file_coef_cp';				// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("file_coef_cp update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
					$this->failTrans();		//トランザクション失敗を設定(ROLLBACK)
                    $this->exitAction();
					return 'error';
				}
			}
			
			if( $this->file_coef_ci != null ){
				// アイテム管理 : 係数Cif
				$params[0] = floatval($this->file_coef_ci);	// param_value
				$params[3] = 'file_coef_ci';				// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("file_coef_ci update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
					$this->failTrans();		//トランザクション失敗を設定(ROLLBACK)
                    $this->exitAction();
					return 'error';
				}
			}
*/
			// アイテム管理 : Export ファイル出力の可否
			if( $this->export_is_include_files != null ){
				$params[0] = $this->export_is_include_files;	// param_value
				$params[3] = 'export_is_include_files';			// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("export_is_include_files update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// Add review mail setting 2009/09/24 Y.Nakao --start--
			// 査読設定
			// アイテムの査読承認のON/OFF
			if( $this->review_flg == null ){
				$flg = 0;
			} else {
				$flg = 1;
			}
   			$params[0] = $flg; // param_value
			$params[3] = 'review_flg';			// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("review_flg update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// アイテム管理 : 査読後のアイテム公開方式
			if( $this->item_auto_public != null ){
				$params[0] = $this->item_auto_public;	// param_value
				$params[3] = 'item_auto_public';			// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("item_auto_public update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// 査読通知メール設定
			if( $this->review_mail_flg == null ){
				$flg = 0;
			} else {
				$flg = 1;
			}
   			$params[0] = $flg; // param_value
			$params[3] = 'review_mail_flg';			// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("review_mail_flg update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			
            // Bugfix input scrutiny 2011/06/17 Y.Nakao --start--
            // check 'Review mail address'
            $this->review_mail = str_replace("\r\n", "\n", $this->review_mail);
            $add = array();
            $add = explode("\n", $this->review_mail);
            $this->review_mail = "";
            for($ii=0; $ii<count($add); $ii++){
                if(strlen($add[$ii]) > 0){
                    if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $add[$ii]) > 0){
                        $this->review_mail .= $add[$ii]."\n";
                    }
                }
            }
            // Bugfix input scrutiny 2011/06/17 Y.Nakao --end--
			// 査読通知メールアドレス
   			$params[0] = $this->review_mail; // param_value
			$params[3] = 'review_mail';			// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("review_mail update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Add review mail setting 2009/09/24 Y.Nakao --end--
			
			// OAI-PMH管理 : 管理者メールアドレス
			if( $this->prvd_Identify_adminEmail != null ){
				$params[0] = $this->prvd_Identify_adminEmail;	// param_value
				$params[3] = 'prvd_Identify_adminEmail';		// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("prvd_Identify_adminEmail update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// OAI-PMH管理 : リポジトリ名
			if( $this->prvd_Identify_repositoryName != null ){
				$params[0] = $this->prvd_Identify_repositoryName;	// param_value
				$params[3] = 'prvd_Identify_repositoryName';		// param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("prvd_Identify_repositoryName update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// OAI-PMH管理 : earliest_datestamp
			$datestamp = $this->prvd_Identify_earliestDatestamp_year . '-' .
						$this->prvd_Identify_earliestDatestamp_month . '-' .
						$this->prvd_Identify_earliestDatestamp_day . 'T' .
						$this->prvd_Identify_earliestDatestamp_hour . ':' .
						$this->prvd_Identify_earliestDatestamp_minute . ':' .
						$this->prvd_Identify_earliestDatestamp_second . 'Z';
   			$params[0] = $datestamp;	// param_value
   			$params[3] = 'prvd_Identify_earliestDatestamp';		// param_name
   			$result = $this->updateParamTableData($params, $Error_Msg);
   			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("prvd_Identify_earliestDatestamp update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			
			// Add command path setting 2008/08/07 Y.Nakao --start--
			// Server environment : pass to wvWare
			if( $this->path_wvWare != null ){
				if($this->path_wvWare[strlen($this->path_wvWare)-1] !=DIRECTORY_SEPARATOR){
					$this->path_wvWare .= DIRECTORY_SEPARATOR;
				}
			}
			$params[0] = $this->path_wvWare;	// param_value
			$params[3] = 'path_wvWare';		// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("path_wvWare update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Server environment : pass to xlhtml
			if( $this->path_xlhtml != null ){
				if($this->path_xlhtml[strlen($this->path_xlhtml)-1] !=DIRECTORY_SEPARATOR){
					$this->path_xlhtml .= DIRECTORY_SEPARATOR;
				}
			}
			$params[0] = $this->path_xlhtml;	// param_value
			$params[3] = 'path_xlhtml';		// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("path_xlhtml update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Server environment : pass to poppler
			if( $this->path_poppler != null ){
				if($this->path_poppler[strlen($this->path_poppler)-1] !=DIRECTORY_SEPARATOR){
					$this->path_poppler .= DIRECTORY_SEPARATOR;
				}
			}
			$params[0] = $this->path_poppler;	// param_value
			$params[3] = 'path_poppler';		// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("path_poppler update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Server environment : pass to ImageMagick
			if( $this->path_ImageMagick != null ){
				if($this->path_ImageMagick[strlen($this->path_ImageMagick)-1] !=DIRECTORY_SEPARATOR){
					$this->path_ImageMagick .= DIRECTORY_SEPARATOR;
				}
			}
			$params[0] = $this->path_ImageMagick;	// param_value
			$params[3] = 'path_ImageMagick';		// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("path_ImageMagick update failed : %s", $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
            // Add pdftk 2012/06/07 A.Suzuki --start--
            // Server environment : path to pdftk
            if( $this->path_pdftk != null ){
                if($this->path_pdftk[strlen($this->path_pdftk)-1] !=DIRECTORY_SEPARATOR){
                    $this->path_pdftk .= DIRECTORY_SEPARATOR;
                }
            }
            $params[0] = $this->path_pdftk;     // param_value
            $params[3] = 'path_pdftk';          // param_name
            $result = $this->updateParamTableData($params, $Error_Msg);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("path_pdftk update failed : %s", $errMsg ); 
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
            // Add pdftk 2012/06/07 A.Suzuki --end--
            
            // Add multimedia support 2012/08/27 T.Koyasu -start-
            // Server Environment : path to ffmpeg
            if($this->path_ffmpeg != null){
                if($this->path_ffmpeg[strlen($this->path_ffmpeg)-1] != DIRECTORY_SEPARATOR){
                    $this->path_ffmpeg .= DIRECTORY_SEPARATOR;
                }
            }
            $params[0] = $this->path_ffmpeg;        // param_value
            $params[3] = 'path_ffmpeg';             // param_name
            $result = $this->updateParamTableData($params, $Error_Msg);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("path_ffmpeg update failed : %s", $errMsg);
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
            // Add multimedia support 2012/08/27 T.Koyasu -end-
            // Add external serarch word 2014/05/23 K.Matsuo -start-
            // Server Environment : path to mecab
            if($this->path_mecab != null){
                if($this->path_mecab[strlen($this->path_mecab)-1] != DIRECTORY_SEPARATOR){
                    $this->path_mecab .= DIRECTORY_SEPARATOR;
                }
            }
            $params[0] = $this->path_mecab;        // param_value
            $params[3] = 'path_mecab';             // param_name
            $result = $this->updateParamTableData($params, $Error_Msg);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("path_mecab update failed : %s", $errMsg);
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
            // Add external serarch word 2014/05/23 K.Matsuo -end-
            if($this->path_php != null){
                if($this->path_php[strlen($this->path_php)-1] != DIRECTORY_SEPARATOR){
                    $this->path_php .= DIRECTORY_SEPARATOR;
                }
            }
            $params[0] = $this->path_php;        // param_value
            $params[3] = 'path_php';           // param_name
            $result = $this->updateParamTableData($params, $Error_Msg);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("path_mecab update failed : %s", $errMsg);
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
            
			// Add command path setting 2008/08/07 Y.Nakao --end--

			// Add Site License 2008/10/09 Y.Nakao --start--
			// Add Site License 2008/10/20 Y.Nakao --start--
            // Sitelicense 2015/01/21 T.Ichikawa --start--
            $result = $this->executeUpdateSitelicense($Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
                if(strlen($errMsg) > 0) {
                    $tmpstr = sprintf("site_license update failed : %s", $errMsg ); 
                } else {
                    $tmpstr = sprintf("site_license update failed : %s", $Error_Msg ); 
                }
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Add Site License 2008/10/20 Y.Nakao --end--
			// Add Site License 2008/10/09 Y.Nakao --end--
			
			// Add AWSAccessKeyId 2008/11/18 A.Suzuki --start--
			if( $this->AWSAccessKeyId == null ){
				$this->AWSAccessKeyId = "";
			}

			$params[0] = $this->AWSAccessKeyId;	// param_value
			$params[3] = 'AWSAccessKeyId';		// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("AWSAccessKeyId update failed : %s", $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Add AWSAccessKeyId 2008/11/18 A.Suzuki --end--
			
            // Add AWSSecretAccessKey 2010/03/01 S.Nonomura --start--
            if($this->AWSSecretAccessKey == null ){
            	$this->AWSSecretAccessKey = "";
			}

            $params[0] = $this->AWSSecretAccessKey;	// param_value
			$params[3] = 'AWSSecretAccessKey';		// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("AWSSecretAccessKey update failed : %s", $errMsg );
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Add AWSSecretAccessKey 2010/03/01 S.Nonomura --end--
			
            // Add AssociateTag for modify API Y.Nakao 2011/10/19 --start--
            if($this->AssociateTag == null)
            {
                $this->AssociateTag = "";
            }
            $params[0] = $this->AssociateTag;
            $params[3] = 'AssociateTag';
            $result = $this->updateParamTableData($params, $Error_Msg);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("AWSSecretAccessKey update failed : %s", $errMsg );
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
            // Add AssociateTag for modify API Y.Nakao 2011/10/19 --end--
            
            // Add ICHUSHI Login Info A.jin 2012/11/21 --start--
            if($this->ichushiIsConnect == null)
            {
                $flg = 0;
            } else {
                $flg = 1;
            }
            $params[0] = $flg; // param_value
            $params[3] = 'ichushi_is_connect'; // param_name
            $result = $this->updateParamTableData($params, $Error_Msg);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("ichushiIsConnect update failed : %s", $errMsg );
                $this->Session->setParameter("error_msg", $tmpstr);
                $this->failTrans();     //トランザクション失敗を設定(ROLLBACK)
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
            
            if($this->ichushiLoginId == null)
            {
                $this->ichushiLoginId = "";
            }
            $params[0] = $this->ichushiLoginId; // param_value
            $params[3] = 'ichushi_login_id'; // param_name
            $result = $this->updateParamTableData($params, $Error_Msg);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("ichushiLoginId update failed : %s", $errMsg );
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
            
            if($this->ichushiLoginPasswd == null)
            {
                $this->ichushiLoginPasswd = "";
            }
            // 変更なしの場合(********)登録しない
            if($this->ichushiLoginPasswd != "********"){
                //医中誌連携有無チェック
                if($flg == 1)
                {
                    //エラーメッセージ (医中誌にアクセスできない)
                    $error_message = "";
                    //1. ログイン
                    $result = $this->loginIchushi($this->ichushiLoginId, $this->ichushiLoginPasswd, $cookie);
                    if($result === true)
                    {
                        //2. ログアウト
                        $this->logoutIchushi($cookie);
                        $ichushiLoginPasswd = $this->ichushiLoginPasswd;
                    } else {
                        $ichushiLoginPasswd = "";
                        //エラーメッセージ「医中誌にアクセスできませんでした。」
                        $error_message = $smartyAssign->getLang("repository_admin_ichushi")
                        .$smartyAssign->getLang("repository_admin_ichushi_access_error");
                        $this->Session->setParameter("error_msg", $error_message);
                    }
                    
                    $params[0] = $ichushiLoginPasswd; // param_value
                    $params[3] = 'ichushi_login_passwd'; // param_name
                    $result = $this->updateParamTableData($params, $Error_Msg);
                    if ($result === false) {
                        $errMsg = $this->Db->ErrorMsg();
                        $tmpstr = sprintf("ichushiLoginPasswd update failed : %s", $errMsg );
                        $this->Session->setParameter("error_msg", $tmpstr);
        			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                        throw new AppException($tmpstr);
                    }
                    
                } else {
                    //医中誌連携しない場合、データベースを更新しない
                }
            }
            // Add ICHUSHI Login Info A.jin 2012/11/21 --end--

			// Add Item Type Select for Site License 2009/01/06 A.Suzuki --start--
			$params[0] = $this->sitelicense_itemtype;	// param_value
			$params[3] = 'site_license_item_type_id';	// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("site_license_item_type_id update failed : %s", $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Add Item Type Select for Site License 2009/01/06 A.Suzuki --end--
			
			// Add search result setting 2009/03/13 A.Suzuki --start--
			$params[0] = $this->sort_disp;	// param_value
			$params[3] = 'sort_disp';	// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("sort_disp update failed : %s", $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			
			$params[0] = $this->sort_not_disp;	// param_value
			$params[3] = 'sort_not_disp';	// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("sort_not_disp update failed : %s", $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			
			$params[0] = $this->sort_disp_default_index."|".$this->sort_disp_default_keyword;	// param_value
			$params[3] = 'sort_disp_default';	// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("sort_disp_default update failed : %s", $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Add search result setting 2009/03/13 A.Suzuki --start--
			
			// Add default_list_view_num 2009/03/27 A.Suzuki --start--
			$params[0] = $this->default_list_view_num;	// param_value
			$params[3] = 'default_list_view_num';	// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("default_list_view_num update failed : %s", $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			$this->Session->removeParameter("list_view_num");
			// Add default_list_view_num 2009/03/27 A.Suzuki --end--
			
			// Add Log exclusion 2008/09/12 Y.Nakao --start--
			// Bugfix form sanitizing Y.Nakao 2011/06/16 --start--
            if(strlen($this->log_exclusion) > 0){
                $this->log_exclusion = str_replace("\r\n", "\n", $this->log_exclusion);
                $exclusion = explode("\n", $this->log_exclusion);
                $this->log_exclusion = "";
                for($ii=0; $ii<count($exclusion); $ii++){
                    if(strlen($exclusion[$ii]) > 0){
                        if(preg_match("/^[0-9]+.[0-9]+.[0-9]+.[0-9]+$/", $exclusion[$ii]) != 0){
                            $this->log_exclusion .= $exclusion[$ii]."\n";
                        }
                    }
                }
            }
			// Bugfix form sanitizing Y.Nakao 2011/06/16 --end--
			$params[0] = $this->log_exclusion;	// param_value
			$params[3] = 'log_exclusion';		// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("log_exclusion update failed : %s", $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			
			// Add supple WEKO setting 2009/09/09 A.Suzuki --start--
			// サプリコンテンツのURL
            // Bugfix form sanitizing Y.Nakao 2011/06/16 --start--
            if(strlen($this->supple_weko_url) > 0){
                // delete at the end of '/'
                $this->supple_weko_url = preg_replace("/\/+$/", "", $this->supple_weko_url);
                // check string 'URL'
                if(preg_match("/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/", $this->supple_weko_url) == 0){
                    $this->supple_weko_url = "";
                    $this->Session->setParameter("error_msg", "ERROR : supple weko URL");
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
                }
            }
            // Bugfix form sanitizing Y.Nakao 2011/06/16 --end--
			$params[0] = $this->supple_weko_url;	// param_value
			$params[3] = 'supple_weko_url';	// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("supple_weko_url update failed : %s", $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			
			// サプリコンテンツ査読承認のON/OFF
			if( $this->review_flg_supple == null ){
				$flg = 0;
			} else {
				$flg = 1;
			}
   			$params[0] = $flg; // param_value
			$params[3] = 'review_flg_supple';			// param_name
			$result = $this->updateParamTableData($params, $Error_Msg);
			if ($result === false) {
				$errMsg = $this->Db->ErrorMsg();
				$tmpstr = sprintf("review_flg_supple update failed : %s", $ii, $jj, $errMsg ); 
				$this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
			}
			// Add supple WEKO setting 2009/09/09 A.Suzuki --end--
			
			// Add help icon and OAI-ORE icon setting 2010/02/10 K.Ando --start--
			if($this->help_icon_display != null)
			{
				$params[0] = $this->help_icon_display;
				$params[3] = 'help_icon_display';
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("help_icon_display update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// Add index list 2011/4/5 S.Abe --start--
		    if($this->select_index_list_display != null)
            {
                $params[0] = $this->select_index_list_display;
                $params[3] = 'select_index_list_display';
                $result = $this->updateParamTableData($params, $Error_Msg);
                if ($result === false) {
                    $errMsg = $this->Db->ErrorMsg();
                    $tmpstr = sprintf("select_index_list_display update failed : %s", $ii, $jj, $errMsg ); 
                    $this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
                }
            }
			// Add index list 2011/4/5 S.Abe --end--
			if($this->oaiore_icon_display != null)
			{
				$params[0] = $this->oaiore_icon_display;
				$params[3] = 'oaiore_icon_display';
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("oaiore_icon_display update failed : %s", $ii, $jj, $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// Add help icon and OAI-ORE icon setting 2010/02/10 K.Ando --end--
            
            // Add send feedback mail 2012/08/24 A.Suzuki --start--
            if($this->feedbackSendMailActivateFlag == null)
            {
                $flg = "0";
            }
            else
            {
                $flg = "1";
            }
            $params[0] = $flg;                              // param_value
            $params[3] = 'send_feedback_mail_activate_flg'; // param_name
            $result = $this->updateParamTableData($params, $Error_Msg);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("exclude_address_for_feedback update failed : %s", $errMsg ); 
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
            
            $params[0] = $this->feedback_exclude_address_list;  // param_value
            $params[3] = 'exclude_address_for_feedback';        // param_name
            $result = $this->updateParamTableData($params, $Error_Msg);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("exclude_address_for_feedback update failed : %s", $errMsg ); 
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
            // Add send feedback mail 2012/08/24 A.Suzuki --end--
			// Add private tree parameter K.Matsuo 2013/4/5 --start--
			
			$define_inc_file_path = WEBAPP_DIR. '/modules/repository/config/define.inc.php';
			if(is_writable($define_inc_file_path)){
				
				if( $this->is_make_privatetree == null ){
					$flg = 0;
				} else {
					$flg = 1;
				}
	   			$params[0] = $flg; // param_value
				$params[3] = 'is_make_privatetree';        // param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("is_make_privatetree update failed : %s", $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
				// Add file rewrite for privatetree edit tab authority K.Matsuo 2013/04/24 --start--
				$define_inc_file_path = WEBAPP_DIR. '/modules/repository/config/define.inc.php';
				$fp = fopen($define_inc_file_path, "r");
				$define_inc_text = array();
				while ($line = fgets($fp)) {
					$define_inc_text[] = $line;
				}
				fclose($fp);
				$fp = fopen($define_inc_file_path, "w");
				for($ii = 0; $ii < count($define_inc_text); $ii++){
					if(strpos($define_inc_text[$ii], "CHANGE_TEXT_PRIVATETREE") !== false){
						if( $this->is_make_privatetree == null ){
							$define_inc_text[$ii] = 'define("_REPOSITORY_PRIVATETREE_AUTHORITY", REPOSITORY_ADMIN_MORE);		// CHANGE_TEXT_PRIVATETREE'.PHP_EOL;
						} else {
							$define_inc_text[$ii] = 'define("_REPOSITORY_PRIVATETREE_AUTHORITY", REPOSITORY_ITEM_REGIST_AUTH);		// CHANGE_TEXT_PRIVATETREE'.PHP_EOL;
						}
					}
					fwrite($fp, $define_inc_text[$ii]);
				}
				fclose($fp);
			} else {
	   			$params[0] = 0; // param_value
				$params[3] = 'is_make_privatetree';        // param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("is_make_privatetree update failed : %s", $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			// Add file rewrite for privatetree edit tab authority K.Matsuo 2013/04/24 --end--
			if($this->privatetree_sort_order != null){
				$params[0] = $this->privatetree_sort_order;  // param_value
				$params[3] = 'privatetree_sort_order';        // param_name
				$result = $this->updateParamTableData($params, $Error_Msg);
				if ($result === false) {
					$errMsg = $this->Db->ErrorMsg();
					$tmpstr = sprintf("privatetree_sort_order update failed : %s", $errMsg ); 
					$this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
				}
			}
			if($this->privatetree_parent_indexid != null){
				if($admin_records['privatetree_parent_indexid']['param_value'] != $this->privatetree_parent_indexid){
				
					$params[0] = $this->privatetree_parent_indexid;  // param_value
					$params[3] = 'privatetree_parent_indexid';        // param_name
					$result = $this->updateParamTableData($params, $Error_Msg);
					if ($result === false) {
						$errMsg = $this->Db->ErrorMsg();
						$tmpstr = sprintf("privatetree_parent_indexid update failed : %s", $errMsg ); 
						$this->Session->setParameter("error_msg", $tmpstr);
        			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                        throw new AppException($tmpstr);
					}
					// 現在のプライベートツリーの親インデックスを新しく設定した親インデックスに変更
					$query = "SELECT `index_id` ".
							 "FROM ". DATABASE_PREFIX ."repository_index ".
							 "WHERE `owner_user_id` != '' AND " .
							 "`parent_index_id` = ? AND ".
							 "`is_delete` = 0 ".
							 "ORDER BY show_order ;";
					$queryParams = null;
					$queryParams[] = $admin_records['privatetree_parent_indexid']['param_value'];
					
					$ret = $this->Db->execute($query, $queryParams);
					$editTree = new Repository_Action_Edit_Tree();
					$editTree->Session = $this->Session;
					$editTree->Db = $this->Db;
					$editTree->setDefaultAccessControlList();
					$editTree->TransStartDate = $this->TransStartDate;
                    
                    // parent index id of root private tree changes to new setting
                    require_once WEBAPP_DIR. '/modules/repository/components/RepositoryIndexManager.class.php';
                    $indexManager = new RepositoryIndexManager($this->Session, $this->dbAccess, $this->TransStartDate);
                    
                    for($ii=0; $ii<count($ret); $ii++)
                    {
                        // get index data for update
                        $indexData = $editTree->getIndexEditData($ret[$ii]["index_id"]);
                        
                        // 既存の処理ではルートのprivatetreeが移動できない、owner_user_idを変更する処理を行っているため修正
                        $indexData["parent_index_id"] = $this->privatetree_parent_indexid;
                        $indexManager->updateIndex($indexData);
                    }
				}
			}
			// Add private tree parameter K.Matsuo 2013/4/5 --end--
			
			// Add institution Name S.Suzuki 2014/12/19 --start--
            $params[0] = $this->institutionName;    // param_value
            $params[3] = 'institution_name';        // param_name
            $result = $this->updateParamTableData($params, $Error_Msg);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("institution_name update failed : %s", $errMsg ); 
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
			// Add institution Name S.Suzuki 2014/12/19 --end--
			
			// Add external author ID prefix 2010/11/11 A.Suzuki --start--
			$this->updateExternalAuthorIdPrefix();
			// Add external author ID prefix 2010/11/11 A.Suzuki --end--
			
            // Add URL rewrite 2011/11/15 T.Koyasu -start-
            $use_url_rewrite = $this->Session->getParameter('use_url_rewrite');
            // chage url rewrite radio button -> resetting .htaccess
            if(($use_url_rewrite == '1' && $this->use_url_rewrite == 0) ||
                ($use_url_rewrite == '' && $this->use_url_rewrite == 1)) {
                $result = $this->urlRewrite();
                if($result == false){
                    $this->Session->setParameter("repository_admin_url_rewrite_error", "error");
                    return 'error';
                }
            }
            // Add URL rewrite 2011/11/15 T.Koyasu -end-
            
            // Add harvesting 2012/03/05 A.Suzuki --start--
            // Get harvesting status
            $Harvesting = new RepositoryHarvesting($this->Session, $this->Db);
            $harvestingStartDate = "";
            $harvestingEndDate = "";
            $Harvesting->openProgressFile(false);
            
            // If harvesting is executing, not run regist and update.
            if($Harvesting->getStatus() == RepositoryHarvesting::STATUS_START)
            {
                $harvestingRepositories = array();
                for($ii=0;$ii<count($this->harvesting_repositoryName);$ii++){
                    // If input text data is half space only, change to empty.
                    if($this->harvesting_repositoryName[$ii] == " ")
                    {
                        $this->harvesting_repositoryName[$ii] = "";
                    }
                    if($this->harvesting_baseUrl[$ii] == " ")
                    {
                        $this->harvesting_baseUrl[$ii] = "";
                    }
                    if($this->harvesting_post_index[$ii] == " ")
                    {
                        $this->harvesting_post_index[$ii] = "";
                    }
                    
                    // Add Selective Harvesting 2013/09/04 R.Matsuura --start--
                    // Replace double-byte space to single-byte space
                    $this->harvesting_set_param[$ii] = str_replace("　", " ", $this->harvesting_set_param[$ii]);
                    // When harvesting_set_param is space
                    if($this->harvesting_set_param[$ii] == " ")
                    {
                        $this->harvesting_set_param[$ii] = "";
                    }
                    // When harvesting_from_date_~ is not numeric
                    if(!(is_numeric($this->harvesting_from_date_year[$ii])
                        && is_numeric($this->harvesting_from_date_month[$ii])
                        && is_numeric($this->harvesting_from_date_day[$ii])
                        && is_numeric($this->harvesting_from_date_hour[$ii])
                        && is_numeric($this->harvesting_from_date_minute[$ii])
                        && is_numeric($this->harvesting_from_date_second[$ii])))
                    {
                        return "error";
                    }
                    // When harvesting_untile_date_~ is not numeric
                    if(!(is_numeric($this->harvesting_until_date_year[$ii])
                        && is_numeric($this->harvesting_until_date_month[$ii])
                        && is_numeric($this->harvesting_until_date_day[$ii])
                        && is_numeric($this->harvesting_until_date_hour[$ii])
                        && is_numeric($this->harvesting_until_date_minute[$ii])
                        && is_numeric($this->harvesting_until_date_second[$ii])))
                    {
                        return "error";
                    }
                    // When more than five digits
                    if(strlen($this->harvesting_from_date_year[$ii]) >= 5
                        || strlen($this->harvesting_until_date_year[$ii]) >= 5)
                    {
                        return "error";
                    }
                    // When the three digits or less, do to 4-digit zero-fill
                    $this->harvesting_from_date_year[$ii] = sprintf("%04d", $this->harvesting_from_date_year[$ii]);
                    $this->harvesting_until_date_year[$ii] = sprintf("%04d", $this->harvesting_until_date_year[$ii]);
                    // Consolidated inthe form of YYYY-MM-DDThh:mm:ssZ
                    $from_date = $this->harvesting_from_date_year[$ii] . "-" .
                                $this->harvesting_from_date_month[$ii] . "-" .
                                $this->harvesting_from_date_day[$ii] . "T" .
                                $this->harvesting_from_date_hour[$ii] . ":" .
                                $this->harvesting_from_date_minute[$ii] . ":" .
                                $this->harvesting_from_date_second[$ii] . "Z";
                    // if match with DEFAULT_FROM_DATE
                    if($from_date == RepositoryHarvesting::DEFAULT_FROM_DATE)
                    {
                        $from_date = "";
                    }
                    // Consolidated inthe form of YYYY-MM-DDThh:mm:ssZ
                    $until_date = $this->harvesting_until_date_year[$ii] . "-" .
                                $this->harvesting_until_date_month[$ii] . "-" .
                                $this->harvesting_until_date_day[$ii] . "T" .
                                $this->harvesting_until_date_hour[$ii] . ":" .
                                $this->harvesting_until_date_minute[$ii] . ":" .
                                $this->harvesting_until_date_second[$ii] . "Z";
                    // if match with DEFAULT_UNTIL_DATE
                    if($until_date == RepositoryHarvesting::DEFAULT_UNTIL_DATE)
                    {
                        $until_date = "";
                    }
                    // Add Selective Harvesting 2013/09/04 R.Matsuura --end--
                    
                    $repoData = array(
                                "repository_id" => $this->harvesting_repositoryId[$ii],
                                "repository_name" => $this->harvesting_repositoryName[$ii],
                                "base_url" => $this->harvesting_baseUrl[$ii],
                                "metadata_prefix" => $this->harvesting_metadataPrefix[$ii],
                                "post_index_id" => $this->harvesting_post_index[$ii],
                                "automatic_sorting" => $this->harvesting_automatic_sorting[$ii],
                                
                                // Add Selective Harvesting 2013/09/04 R.Matsuura --start--
                                "from_date" => $from_date,
                                "until_date" => $until_date,
                                "set_param" => $this->harvesting_set_param[$ii],
                                "execution_date" => $this->harvesting_execution_date[$ii]
                                // Add Selective Harvesting 2013/09/04 R.Matsuura --end--
                            );
                    array_push($harvestingRepositories, $repoData);
                    
                }
                
                // Disable all repositories data
                $Harvesting->disableRepositoriesData($user_id, $edit_start_date);
                
                if(count($harvestingRepositories) > 0)
                {
                    // Upsert repositories data
                    if(!$Harvesting->UpsertRepositoriesData($harvestingRepositories, $user_id, $edit_start_date))
                    {
                        $tmpstr = "Failed to upsert repository information for Harvesting.";
                        $this->Session->setParameter("error_msg", $tmpstr);
        			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                        throw new AppException($tmpstr);
                    }
                }
            }
            // Add harvesting 2012/03/05 A.Suzuki --end--
            
            // Add PDF cover page 2012/06/12 A.Suzuki --start--
            // PDF cover header type
            if($this->pdf_cover_header_type != null){
                $params = array();
                $params[RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_PARAM_NAME] = RepositoryConst::PDF_COVER_PARAM_NAME_HEADER_TYPE;
                $params[RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_TEXT] = $this->pdf_cover_header_type;
                $params[RepositoryConst::DBCOL_COMMON_MOD_USER_ID] = $user_id;
                $params[RepositoryConst::DBCOL_COMMON_MOD_DATE] = $this->TransStartDate;
                $result = $this->updatePdfCoverParamByParamName($params);
                if ($result === false) {
                    $errMsg = $this->Db->ErrorMsg();
                    $tmpstr = sprintf("headerType update failed : %s", $errMsg ); 
                    $this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
                }
            }
            
            // PDF cover header text 
            // Delete null check(pdf_cover_header_text) K.Matsushita 2015/2/5 --start--
            $params = array();
            $params[RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_PARAM_NAME] = RepositoryConst::PDF_COVER_PARAM_NAME_HEADER_TEXT;
            $params[RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_TEXT] = mb_strimwidth($this->pdf_cover_header_text, 0, 100);
            $params[RepositoryConst::DBCOL_COMMON_MOD_USER_ID] = $user_id;
            $params[RepositoryConst::DBCOL_COMMON_MOD_DATE] = $this->TransStartDate;
            $result = $this->updatePdfCoverParamByParamName($params);
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                $tmpstr = sprintf("headerText update failed : %s", $errMsg ); 
                $this->Session->setParameter("error_msg", $tmpstr);
			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                throw new AppException($tmpstr);
            }
            // Delete null check(pdf_cover_header_text) K.Matsushita 2015/2/5 --end--
            
            // PDF cover header image
            $uploadFile = $this->Session->getParameter("repositoryAdminFileList");
            $this->Session->removeParameter("repositoryAdminFileList");
            if(isset($this->pdf_cover_header_image_del) && $this->pdf_cover_header_image_del == 1)
            {
                // delete upload file
                if(is_array($uploadFile) && count($uploadFile) > 0)
                {
                    $this->uploadsAction->delUploadsById($uploadFile["upload_id"]);
                }
                
                // delete image
                $query = "UPDATE ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_PDF_COVER_PARAMETER." ".
                         "SET ".RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_IMAGE." = ?, ".
                         RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_TEXT." = ?, ".
                         RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_EXTENSION." = ?, ".
                         RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_MIMETYPE." = ? ".
                         "WHERE ".RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_PARAM_NAME." = ?; ";
                $params = array();
                $params[] = "";
                $params[] = "";
                $params[] = "";
                $params[] = "";
                $params[] = RepositoryConst::PDF_COVER_PARAM_NAME_HEADER_IMAGE;
                $result = $this->Db->execute($query,$params);
                if($result == false){
                    $errMsg = $this->Db->ErrorMsg();
                    $tmpstr = sprintf("headerImage update failed : %s", $errMsg ); 
                    $this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
                }
            }
            else if(is_array($uploadFile) && count($uploadFile) > 0)
            {
                if(!is_numeric(strpos($uploadFile["mimetype"],"image")) || !$this->checkHeaderImageExtension($uploadFile["extension"]))
                {
                    $tmpstr = "Uploaded file to the Header Image cannot be used!";
                    $this->Session->setParameter("error_msg", $tmpstr);
                    
                    // delete upload file
                    $this->uploadsAction->delUploadsById($uploadFile["upload_id"]);
                    
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
                }
                else
                {
                    // insert thumbnail
                    $query = "UPDATE ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_PDF_COVER_PARAMETER." ".
                             "SET ".RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_IMAGE." = ?, ".
                             RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_TEXT." = ?, ".
                             RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_EXTENSION." = ?, ".
                             RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_MIMETYPE." = ? ".
                             "WHERE ".RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_PARAM_NAME." = ?; ";
                    $params = array();
                    $params[] = "";
                    $params[] = $uploadFile["file_name"];
                    $params[] = $uploadFile["extension"];
                    $params[] = $uploadFile["mimetype"];
                    $params[] = RepositoryConst::PDF_COVER_PARAM_NAME_HEADER_IMAGE;
                    $result = $this->Db->execute($query,$params);
                    if($result == false){
                        $errMsg = $this->Db->ErrorMsg();
                        $tmpstr = sprintf("headerImage update failed : %s", $errMsg ); 
                        $this->Session->setParameter("error_msg", $tmpstr);
                        
                        // delete upload file
                        $this->uploadsAction->delUploadsById($uploadFile["upload_id"]);
                        
        			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                        throw new AppException($tmpstr);
                    }
                    $path = WEBAPP_DIR. "/uploads/repository/".$uploadFile['physical_file_name'];
                    $ret = $this->Db->updateBlobFile(
                                RepositoryConst::DBTABLE_REPOSITORY_PDF_COVER_PARAMETER,
                                RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_IMAGE,
                                $path, 
                                RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_PARAM_NAME.' = "'.RepositoryConst::PDF_COVER_PARAM_NAME_HEADER_IMAGE.'"',
                                'LONGBLOB'
                            );
                    if($ret == false){
                        $errMsg = $this->Db->ErrorMsg();
                        $tmpstr = sprintf("headerImage file update failed : %s", $errMsg ); 
                        $this->Session->setParameter("error_msg", $tmpstr);
                        
                        // delete upload file
                        $this->uploadsAction->delUploadsById($uploadFile["upload_id"]);
                        
        			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                        throw new AppException($tmpstr);
                    }
                    
                    // delete upload file
                    $this->uploadsAction->delUploadsById($uploadFile["upload_id"]);
                }
            }
            
            // PDF cover header align
            if($this->pdf_cover_header_align != null){
                $params = array();
                $params[RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_PARAM_NAME] = RepositoryConst::PDF_COVER_PARAM_NAME_HEADER_ALIGN;
                $params[RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_TEXT] = $this->pdf_cover_header_align;
                $params[RepositoryConst::DBCOL_COMMON_MOD_USER_ID] = $user_id;
                $params[RepositoryConst::DBCOL_COMMON_MOD_DATE] = $this->TransStartDate;
                $result = $this->updatePdfCoverParamByParamName($params);
                if ($result === false) {
                    $errMsg = $this->Db->ErrorMsg();
                    $tmpstr = sprintf("headerAlign update failed : %s", $errMsg ); 
                    $this->Session->setParameter("error_msg", $tmpstr);
    			    $this->errorLog($tmpstr, __FILE__, __CLASS__, __LINE__);
                    throw new AppException($tmpstr);
                }
            }
            
			//セッションの初期化
			$this->Session->removeParameter("admin_params");
			$this->Session->removeParameter("edit_start_date");
			
            //Add Prefix Admin 2013/12/24 T.Ichikawa --start--
            $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->dbAccess, $this->TransStartDate);
        	// Add DataCite 2015/02/09 K.Sugimoto --start--
            $repositoryHandleManager->registerPrefix($this->prefixJalcDoi, $this->prefixCrossRef, $this->prefixDataCite, $this->prefixCnri);
            if(isset($repositoryHandleManager->err_msg) && strlen($repositoryHandleManager->err_msg) > 0)
            {
                //エラーメッセージ「PrefixIDのフォーマットが不正です」
                $error_message = $smartyAssign->getLang("repository_admin_prefix_incorrect");
                $this->Session->setParameter("error_msg", $error_message);
			    $this->errorLog($error_message, __FILE__, __CLASS__, __LINE__);
                throw new AppException($error_message);
            }
        	// Add DataCite 2015/02/09 K.Sugimoto --end--
            //Add Prefix Admin 2013/12/24 T.Ichikawa --end--
			
            // Add Detail Search 2013/11/20 R.Matsuura --start--
			// Set Detail Search Parameter
			$query = "UPDATE ".DATABASE_PREFIX. "repository_search_item_setup ".
			         "SET `use_search` = ?, `default_show` = ? ".
			         "WHERE `type_id` = ? ;";
			for($cnt = 0; $cnt < count($this->search_type_id); $cnt++)
			{
			    $params = array();
			    $params[] = $this->search_use_flag[$cnt];
			    $params[] = $this->search_default_flag[$cnt];
			    $params[] = $this->search_type_id[$cnt];
			    $this->dbAccess->executeQuery($query, $params);
			}
			// Add Detail Search 2013/11/20 R.Matsuura --end--
            
            // Add External Search Word 2014/05/22 K.Matsuo --start--
            require_once WEBAPP_DIR. '/modules/repository/components/RepositoryExternalSearchWordManager.class.php';
            $searchword_show = 0;
            if( $this->external_searchword_show != null ){
            	$searchword_show = 1;
            }
            $Repository_Components_RepositoryExternalSearchWordManager = 
                new Repository_Components_RepositoryExternalSearchWordManager($this->Session, $this->Db, $this->TransStartDate);
            $Repository_Components_RepositoryExternalSearchWordManager->updateExternalSearchStopWord($this->external_searchword_word, 
                $this->external_searchword_stopword_rule, $searchword_show);
            // Add External Search Word 2014/05/22 K.Matsuo --end--
            
            // OAI-PMH Output Flag
            $query = "UPDATE ".DATABASE_PREFIX. "repository_parameter ".
			         "SET `param_value` = ? ".
			         "WHERE `param_name` = ? ;";
		    $params = array();
		    $params[] = $this->oaipmh_output_flag;
		    $params[] = "output_oaipmh";
		    $this->dbAccess->executeQuery($query, $params);
            
            // Add Default Search Type 2014/12/03 K.Sugimoto --start--
            // Default Search Type
            $query = "UPDATE ".DATABASE_PREFIX. "repository_parameter ".
			         "SET `param_value` = ? ".
			         "WHERE `param_name` = ? ;";
		    $params = array();
		    $params[] = $this->default_search_type;
		    $params[] = "default_search_type";
		    $this->dbAccess->executeQuery($query, $params);
            // Add Default Search Type 2014/12/03 K.Sugimoto --end--

            // Add Usage Statistics link display setting 2014/12/16 K.Matsushita --start--
            $query = "UPDATE ".DATABASE_PREFIX. "repository_parameter ".
                    "SET `param_value` = ? ".
                    "WHERE `param_name` = ? ;";
            $params = array();
            $params[] = $this->usagestatistics_link_display;
            $params[] = "usagestatistics_link_display";
            $this->dbAccess->executeQuery($query, $params);
            // Add Usage Statistics link display setting 2014/12/16 K.Matsushita --start--
            
            // Add DataCite 2015/02/09 K.Sugimoto --start--
	        $query = "SELECT COUNT(*) ".
	                 "FROM ".DATABASE_PREFIX."repository_doi_status ;";
            $params = array();
	        $result = $this->dbAccess->executeQuery($query, $params);
	        
	        if(isset($this->prefix_flag) && $result[0]["COUNT(*)"] == 0){
	            // PrefixID Add Flag
	            $query = "UPDATE ".DATABASE_PREFIX. "repository_parameter ".
				         "SET `param_value` = ? ".
				         "WHERE `param_name` = ? ;";
			    $params = array();
			    $params[] = $this->prefix_flag;
			    $params[] = "prefix_flag";
			    $this->dbAccess->executeQuery($query, $params);
		    }
            // Add DataCite 2015/02/09 K.Sugimoto --end--
            
            // Auto Input Metadata by CrossRef DOI 2015/03/02 K.Sugimoto --start--
            $query = "UPDATE ".DATABASE_PREFIX. "repository_parameter ".
                    "SET `param_value` = ? ".
                    "WHERE `param_name` = ? ;";
            $params = array();
            $params[] = $this->CrossRefQueryServicesAccount;
            $params[] = "crossref_query_service_account";
            $this->dbAccess->executeQuery($query, $params);
            // Auto Input Metadata by CrossRef DOI 2015/03/02 K.Sugimoto --end--
            
            // Add RobotList 2015/04/06 S.Suzuki --start--
            $this->robotlistCheckboxUpdate();
            // Add RobotList 2015/04/06 S.Suzuki --end--
            
            // 著者名検索設定
            $this->updateParameterValue("author_search_type", $this->author_search_type);
		    
			// アクション終了処理
			// $result = $this->exitAction();	// トランザクションが成功していればCOMMITされる
			// return 'success';
			
			// redirect
			$this->Session->setParameter("redirect_flg", "admin");
			return 'redirect';
			
	}
	
	/**
	 * setting url rewrite(oai-pmh)
	 * OAI-PMHのURL書き換えを設定する
	 *
	 * @return bool true/false success/failed 成功/失敗
	 */
	private function urlRewrite()
	{
        // get file path(.htaccess)
        $htaccess_path = START_INDEX_DIR ."/" .".htaccess";
        $writing_data = "";

        // get dir_path of index.php from document root( / = document root of httpd )
        $url = parse_url(BASE_URL);
        $path = "";
        if(isset($url['path'])) {
            $path = preg_replace("/\s+$|[\t]+$|[\n]+$|[\r]+$|[\x0B]+$|\\+$|　+$/", "", $url['path']) .'/';
        }else {
            $path = '/';
        }
        
        if($this->use_url_rewrite == 1) {
            // Url rewrite is valid
            // file exist?
            if(file_exists($htaccess_path)) { // rewrite .htaccess
                if(!chmod($htaccess_path, 0777)) {
                    return false;
                }
                if(!is_writable($htaccess_path)) {
                    return false;
                }
                if(!($fp = fopen($htaccess_path, 'r'))) {
                    chmod($htaccess_path, 0444);
                    return false;
                }
                
                // get setting of .htaccess
                $reading_data_array = array();
                // make pattern for preg_match
                $replaced_path = preg_quote($path, "/");
                $pattern = "/RewriteBase +" .$replaced_path ."/";
                
                // file change or not
                $url_rewrite_flg = false;
                $flg_pattern = "/RewriteEngine +On/";
                $add_url_rewrite_flg = false;
                
                while(!feof($fp)) {
                    $line = fgets($fp);
                    array_push($reading_data_array, $line);
                    // need sentence existent or not
                    if(preg_match($flg_pattern, $line) == 1) {
                        $url_rewrite_flg = true;
                    }else if($url_rewrite_flg) {
                        // if find string(RewriteBase /nc/htdocs/), add url rewrite
                        if(preg_match($pattern, $line) == 1) {
                            array_push($reading_data_array, "RewriteRule ^oai$ " .$path ."index.php?action=repository_oaipmh&%{QUERY_STRING} [L]" ."\n");
                            $add_url_rewrite_flg = true;
                        }
                    }
                }
                fclose($fp);
                
                if(!$url_rewrite_flg || !$add_url_rewrite_flg) {
                    $writing_data = "<IfModule mod_rewrite.c>"."\n".
                                    "RewriteEngine On"."\n".
                                    "RewriteBase ".$path."\n".
                                    "RewriteRule ^oai$ ".$path."index.php?action=repository_oaipmh&%{QUERY_STRING} [L]"."\n".
                                    "</IfModule>\n\n";
                }
                
                // make setting of .htaccess from reading_data
                $writing_data .= implode("", $reading_data_array);
                
                // set .htaccess
                if(!($fp = fopen($htaccess_path, 'w'))) {
                    chmod($htaccess_path, 0444);
                    return false;
                }
                
                // set .htaccess
                fwrite($fp, $writing_data);
                fclose($fp);
            }else { // make .htaccess
                if(!($fp = fopen($htaccess_path, 'w'))) {
                    return false;
                }
                // make setting of .htaccess
                $writing_data = "<IfModule mod_rewrite.c>"."\n".
                                "RewriteEngine On"."\n".
                                "RewriteBase ".$path."\n".
                                "RewriteRule ^oai$ ".$path."index.php?action=repository_oaipmh&%{QUERY_STRING} [L]"."\n".
                                "</IfModule>\n";
                
                // set .htaccess
                fwrite($fp, $writing_data);
                fclose($fp);
            }
            chmod($htaccess_path, 0444);
        }else {
            // Url rewrite is not valid
            if(file_exists($htaccess_path)) {
                if(!chmod($htaccess_path, 0777))
                {
                    return false;
                }
                if(!($fp = fopen($htaccess_path, 'r'))) {
                    chmod($htaccess_path, 0444);
                    return false;
                }

                // get setting of .htaccess
                $reading_data_array = array();
                // make pattern for preg_match
                $replaced_path = preg_quote("^oai$ " .$path ."index.php?action=repository_oaipmh&%{QUERY_STRING} [L]", "/");
                $pattern = "/RewriteRule +" .$replaced_path ."/";
                while(!feof($fp)) {
                    $line = fgets($fp);
                    // read .htaccess outside string(RewriteRule ^oai$ dir_path?action....)
                    if(!(preg_match($pattern, $line)==1)) {
                        array_push($reading_data_array ,$line);
                    }
                }
                fclose($fp);

                if(!is_writable($htaccess_path)) {
                    return false;
                }
                if(!($fp = fopen($htaccess_path, 'w'))) {
                    chmod($htaccess_path, 0444);
                    return false;
                }
                // make setting of .htaccess from reading_data
                $writing_data = implode("", $reading_data_array);
                // set .htaccess
                fwrite($fp, $writing_data);
                fclose($fp);
                chmod($htaccess_path, 0444);
            }
        }
        
        return true;
	}
    
    // Add multi ip address 2015/01/21 T.Ichikawa --start--
    /**
     * update sitelicense info
	 * サイトライセンス情報を更新する
     *
	 * @param string $errMsg error message エラーメッセージ
	 * @return bool true/false update success/update failed 更新成功/更新失敗
     */
    private function executeUpdateSitelicense(&$errMsg="")
    {
        // 一度全てのデータを削除状態にする
        $query = "UPDATE ".DATABASE_PREFIX. "repository_sitelicense_info ".
                 "SET show_order = ?, del_user_id = ?, del_date = ?, is_delete = ? ;";
        $params = array();
        $params[] = 0;
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->TransStartDate;
        $params[] = 1;
        $result1 = $this->dbAccess->executeQuery($query, $params);
        $query = "UPDATE ".DATABASE_PREFIX. "repository_sitelicense_ip_address ".
                 "SET del_user_id = ?, del_date = ?, is_delete = ? ;";
        $params = array();
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->TransStartDate;
        $params[] = 1;
        $result2 = $this->dbAccess->executeQuery($query, $params);
        if($result1 == false || $result2 == false) {
            $errMsg = "Updating database is failed";
            return false;
        }
        
        // サイトライセンス情報を取得する
        $show_order = 0;
        for($ii= 0 ; $ii < count($this->sitelicense_id); $ii++) {
            $this->sitelicense_org[$ii] = str_replace(" ", "", $this->sitelicense_org[$ii]);
            // 機関名が入力されていない場合はエラー
            if(strlen($this->sitelicense_org[$ii]) == 0) {
                $errMsg = "Please enter Organization name";
                return false;
            } else {
                // 新規追加の機関の場合はIDを発行する
                if($this->sitelicense_id[$ii] == 0) {
                    // 最新のサイトライセンス機関IDを取得する
                    $query = "SELECT organization_id FROM ". DATABASE_PREFIX. "repository_sitelicense_info ".
                             "ORDER BY organization_id DESC ".
                             "LIMIT 0, 1 ;";
                    $sitelicense_seq_id = $this->dbAccess->executeQuery($query);
                    if(count($sitelicense_seq_id) > 0) {
                        $this->sitelicense_id[$ii] = intval($sitelicense_seq_id[0]["organization_id"]) + 1;
                    } else {
                        $this->sitelicense_id[$ii] = 1;
                    }
                }
                $this->sitelicense_group[$ii] = str_replace(" ", "", $this->sitelicense_group[$ii]);
                $this->sitelicense_mail[$ii] = str_replace(" ", "", $this->sitelicense_mail[$ii]);
                
                // IPアドレス整形
                $ip_address = array();
                $cnt_enable_ip = 0;
                for($jj = 0; $jj < count($this->ip_sitelicense_from[$ii]); $jj++) {
                    // 開始IP
                    $tmp_start_ip = str_replace(" ", "", $this->ip_sitelicense_from[$ii][$jj][0]). ".".
                                    str_replace(" ", "", $this->ip_sitelicense_from[$ii][$jj][1]). ".".
                                    str_replace(" ", "", $this->ip_sitelicense_from[$ii][$jj][2]). ".".
                                    str_replace(" ", "", $this->ip_sitelicense_from[$ii][$jj][3]);
                    if(preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/", $tmp_start_ip) == 1) {
                        $ip_address[$cnt_enable_ip]["start"] = $tmp_start_ip;
                        // 終了IP
                        $tmp_finish_ip = str_replace(" ", "", $this->ip_sitelicense_to[$ii][$jj][0]). ".".
                                         str_replace(" ", "", $this->ip_sitelicense_to[$ii][$jj][1]). ".".
                                         str_replace(" ", "", $this->ip_sitelicense_to[$ii][$jj][2]). ".".
                                         str_replace(" ", "", $this->ip_sitelicense_to[$ii][$jj][3]);
                        if(preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/", $tmp_finish_ip) == 1) {
                            $ip_address[$cnt_enable_ip]["finish"] = $tmp_finish_ip;
                        } else {
                            $ip_address[$cnt_enable_ip]["finish"] = "";
                        }
                        
                        $cnt_enable_ip++;
                    }
                }
                
                // 組織名もIPアドレスもない場合エラー
                if(count($ip_address) == 0 && strlen($this->sitelicense_group[$ii]) == 0) {
                    $errMsg = "Please enter OrganizationName or IP Adress Range ";
                    return false;
                } else if(count($ip_address) == 0 && strlen($this->sitelicense_group[$ii]) > 0) {
                    // 組織名のみ入力されている場合IP配列に空文字を追加
                    $ip_address[0]["start"] = "";
                    $ip_address[0]["finish"] = "";
                }
                
                // サイトライセンス機関情報登録
                $show_order++;
                $query = "INSERT INTO ". DATABASE_PREFIX. "repository_sitelicense_info ".
                         "(organization_id, show_order, organization_name, group_name, mail_address, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete) ".
                         "VALUES ".
                         "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ".
                         "ON DUPLICATE KEY UPDATE ".
                         "show_order = ?, ".
                         "organization_name = ?, ".
                         "group_name = ?, ".
                         "mail_address = ?, ".
                         "mod_user_id = ?, ".
                         "del_user_id = ?, ".
                         "mod_date = ?, ".
                         "del_date = ?, ".
                         "is_delete = ? ;";
                $params = array();
                $params[] = $this->sitelicense_id[$ii];               // INSERT:機関ID
                $params[] = $show_order;                              // INSERT:ソート順
                $params[] = $this->sitelicense_org[$ii];              // INSERT:機関名
                $params[] = $this->sitelicense_group[$ii];            // INSERT:組織名
                $params[] = $this->sitelicense_mail[$ii];             // INSERT:メールアドレス
                $params[] = $this->Session->getParameter("_user_id"); // INSERT:登録者
                $params[] = $this->Session->getParameter("_user_id"); // INSERT:更新者
                $params[] = "";                                       // INSERT:削除者
                $params[] = $this->TransStartDate;                    // INSERT:登録日時
                $params[] = $this->TransStartDate;                    // INSERT:更新日時
                $params[] = "";                                       // INSERT:削除日時
                $params[] = 0;                                        // INSERT:削除フラグ
                $params[] = $show_order;                              // UPDATE:ソート順
                $params[] = $this->sitelicense_org[$ii];              // UPDATE:機関名
                $params[] = $this->sitelicense_group[$ii];            // UPDATE:組織名
                $params[] = $this->sitelicense_mail[$ii];             // UPDATE:メールアドレス
                $params[] = $this->Session->getParameter("_user_id"); // UPDATE:更新者
                $params[] = "";                                       // UPDATE:削除者
                $params[] = $this->TransStartDate;                    // UPDATE:更新日時
                $params[] = "";                                       // UPDATE:削除日時
                $params[] = 0;                                        // UPDATE:削除フラグ
                $result = $this->dbAccess->executeQuery($query, $params);
                if($result == false) {
                    $errMsg = "Updating database is failed";
                    return false;
                }
                
                // サイトライセンスIP情報登録
                if(count($ip_address) > 0) {
                    $ip_address_no = 0;
                    for($jj = 0; $jj < count($ip_address); $jj++) {
                        $ip_address_no++;
                        $query = "INSERT INTO ". DATABASE_PREFIX. "repository_sitelicense_ip_address ".
                                 "(organization_id, organization_no, start_ip_address, finish_ip_address, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete) ".
                                 "VALUES ".
                                 "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ".
                                 "ON DUPLICATE KEY UPDATE ".
                                 "start_ip_address = ?, ".
                                 "finish_ip_address = ?, ".
                                 "mod_user_id = ?, ".
                                 "del_user_id = ?, ".
                                 "mod_date = ?, ".
                                 "del_date = ?, ".
                                 "is_delete = ? ;";
                        $params = array();
                        $params[] = $this->sitelicense_id[$ii];               // INSERT:機関ID
                        $params[] = $ip_address_no;                           // INSERT:機関IPアドレスNo
                        $params[] = $ip_address[$jj]["start"];                // INSERT:IPアドレス（開始部分）
                        $params[] = $ip_address[$jj]["finish"];               // INSERT:IPアドレス（終了部分）
                        $params[] = $this->Session->getParameter("_user_id"); // INSERT:登録者
                        $params[] = $this->Session->getParameter("_user_id"); // INSERT:更新者
                        $params[] = "";                                       // INSERT:削除者
                        $params[] = $this->TransStartDate;                    // INSERT:登録日時
                        $params[] = $this->TransStartDate;                    // INSERT:更新日時
                        $params[] = "";                                       // INSERT:削除日時
                        $params[] = 0;                                        // INSERT:削除フラグ
                        $params[] = $ip_address[$jj]["start"];                // UPDATE:IPアドレス（開始部分）
                        $params[] = $ip_address[$jj]["finish"];               // UPDATE:IPアドレス（終了部分）
                        $params[] = $this->Session->getParameter("_user_id"); // UPDATE:更新者
                        $params[] = "";                                       // UPDATE:削除者
                        $params[] = $this->TransStartDate;                    // UPDATE:更新日時
                        $params[] = "";                                       // UPDATE:削除日時
                        $params[] = 0;                                        // UPDATE:削除フラグ
                        $result = $this->dbAccess->executeQuery($query, $params);
                        if($result == false) {
                            $errMsg = "Updating database is failed";
                            return false;
                        }
                    }
                }
            }
        }
        
        return true;
    }
    // Add multi ip address 2015/01/21 T.Ichikawa --end--
    
    // Add RobotList 2015/04/06 S.Suzuki --start--
    /**
     * update robotlist status
	 * ロボットリスト状態を更新する
     *
	 * @return bool true/false success/failed 成功/失敗
	 * @throws AppException
     */
    private function robotlistCheckboxUpdate()
    {
        // 現在の全てのロボットリストマスタの情報
        $query = "SELECT * " . 
                 "FROM " . DATABASE_PREFIX . "repository_robotlist_master ; ";
        
        $params = array();
        $robotlistMasters = $this->Db->execute($query, $params);
        
        if($robotlistMasters === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        for ($ii = 0; $ii < count($robotlistMasters); $ii++) {
            // 更新後のクローラーリストが有効の場合
            if ($this->isRobotlistEnabled($robotlistMasters[$ii]["robotlist_id"], $this->robotlistValid)) {
                $this->enableRobotlist($robotlistMasters[$ii]);
            }
            // 更新後のクローラーリストが無効の場合
            else {
                $this->disableRobotlist($robotlistMasters[$ii]);
            }
        }
    }
    
    /**
     * check robotlist enabled
	 * ロボットリストが有効化を判定する
	 *
	 * @param int $robotlist_id robot list ID ロボットリストID
	 * @param array $robotlistValid robot list data ID ロボットリストの詳細データID配列
	 *                               array[$ii]
     * @return bool true/false enable/disable 有効/無効
     */
    private function isRobotlistEnabled($robotlist_id, $robotlistValid)
    {
        if(!isset($robotlistValid))
        {
            return false;
        }
        
        $result = array_key_exists($robotlist_id, $robotlistValid);
        
        return $result;
    }
    
    /**
     * update checked robotlist
	 * チェックされたロボットリストを更新する
	 *
	 * @param array $robotlist robot list ロボットリスト
	 *               array["robotlist_id"]
     */
    private function enableRobotlist($robotlist)
    {
        // 更新前のクローラーリストが無効の場合
        // 有効の場合、ログ削除を実施したかどうかの情報を持つため、変更しない
        if ($robotlist["is_robotlist_use"] == RepositoryDatabaseConst::ROBOTLIST_MASTER_NOTUSED)
        {
            $this->updateRobotlistDataStatus($robotlist["robotlist_id"], RepositoryDatabaseConst::ROBOTLIST_DATA_STATUS_NOTDELETED);
            $this->updateRobotlistMasterStatus($robotlist["robotlist_id"], RepositoryDatabaseConst::ROBOTLIST_MASTER_USED);
        }
        
    }

    /**
     * update unchecked robotlist
	 * 未チェックとなったロボットリストを更新する
     *
	 * @param array $robotlist robot list ロボットリスト
	 *               array["robotlist_id"]
     */
    private function disableRobotlist($robotlist)
    {
        // 更新前のクローラーリストが有効の場合
        if ($robotlist["is_robotlist_use"] == RepositoryDatabaseConst::ROBOTLIST_MASTER_USED)
        {
            $this->updateRobotlistDataStatus($robotlist["robotlist_id"], RepositoryDatabaseConst::ROBOTLIST_DATA_STATUS_DISABLED);
            $this->updateRobotlistMasterStatus($robotlist["robotlist_id"], RepositoryDatabaseConst::ROBOTLIST_MASTER_NOTUSED);
        }
    }

    /**
     * update robotlist data status
	 * ロボットリストの状態を更新する
	 *
	 * @param int $robotlist_id robot list ID ロボットリストID
	 * @param int $status check status ステータス
     * @throws AppException
     */
    private function updateRobotlistDataStatus($robotlist_id, $status)
    {
        $query = "UPDATE " . DATABASE_PREFIX . "repository_robotlist_data " . 
                 "SET status = ?, " . 
                 "mod_user_id = ?, " . 
                 "mod_date = ? " . 
                 "WHERE robotlist_id = ? ; " ;
        
        $params = array();
        $params[] = $status; 
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->TransStartDate;
        $params[] = $robotlist_id;
        
        $update = $this->Db->execute($query, $params);
        
        if($update === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }

    /**
     * update robotlist master status
	 * ロボットリストマスタを更新する
     *
	 * @param int $robotlist_id robot list ID ロボットリストID
	 * @param int $status check status ステータス
	 * @throws AppException
     */
    private function updateRobotlistMasterStatus($robotlist_id, $status)
    {
        $query = "UPDATE " . DATABASE_PREFIX . "repository_robotlist_master " . 
                 "SET is_robotlist_use = ?, " . 
                 "mod_user_id = ?, " . 
                 "mod_date = ? " . 
                 "WHERE robotlist_id = ? ; " ;
        
        $params = array();
        $params[] = $status; 
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->TransStartDate;
        $params[] = $robotlist_id;
        
        $update = $this->Db->execute($query, $params);
        
        if($update === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    // Add RobotList 2015/04/06 S.Suzuki --end--

    /**
     * Update value of parameter table
     * パラメータテーブルの設定値を更新する
     *
     * @param string $param_name Parameter name
     *                           パラメータ名
     * @param string $value Parameter value after update
     *                      更新後のパラメータ値
     */
    private function updateParameterValue($param_name, $value)
    {
        $query = "UPDATE ".DATABASE_PREFIX. "repository_parameter ".
                 "SET `param_value` = ? ".
                 "WHERE `param_name` = ? ;";
        $params = array();
        $params[] = $value;
        $params[] = $param_name;
        $ret = $this->executeSql($query, $params);
    }
    
    /**
     * Update external author id prefix
     * 外部著者IDプレフィックスを更新する
	 *
	 * @throws AppException
     */
    private function updateExternalAuthorIdPrefix()
    {
        $NameAuthority = new NameAuthority($this->Session, $this->Db);
        $prefix_data = array();
        for($ii=0;$ii<count($this->external_author_id_prefix_list);$ii++){
            array_push($prefix_data, array("prefix_id" => $this->external_author_id_prefix_list_id[$ii], "prefix_name" => $this->external_author_id_prefix_list[$ii], "url" => $this->external_author_id_prefix_list_url[$ii]));
        }
        $result = $NameAuthority->entryExternalAuthorIdPrefix($prefix_data);
        if($result===false){
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($tmpstr);
        }
    }
}
?>
