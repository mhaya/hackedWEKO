<?php

/**
 * WEKO全体設定
 * WEKO config
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: define.inc.php 70936 2016-08-09 09:53:57Z keiya_sugimoto $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Administrator authority transfer (for administrators hide)
 * 管理者権限以上(管理者非表示用)
 * 
 * @var int
 */
define("REPOSITORY_ADMIN_MORE",6);

/**
 * Items can be registered authority
 * REPOSITORY_ADMIN_MORE: above the administrator
 * _AUTH_ADMIN: Administrator
 * _AUTH_CHIEF: Main responsible
 * _AUTH_MODERATE: Moderator
 * _AUTH_GENERAL: General
 * _AUTH_GUEST: Guest (not logged included)
 * アイテム登録可能権限
 * REPOSITORY_ADMIN_MORE : 管理者より上
 * _AUTH_ADMIN    : 管理者
 * _AUTH_CHIEF    : 主担
 * _AUTH_MODERATE : モデレータ
 * _AUTH_GENERAL  : 一般
 * _AUTH_GUEST    : ゲスト（未ログイン含）
 * 
 * @var int
 */
define("REPOSITORY_ITEM_REGIST_AUTH",_AUTH_GENERAL);

/**
 * Use details that can be displayed authority
 * 利用明細表示可能権限
 * 
 * @var int
 */
define("REPOSITORY_CHARGE_AUTH", REPOSITORY_ADMIN_MORE);

//--------------Shibboleth定数未設定時の初期化---------------------
if(!defined('SHIB_ENABLED'))
{
    /**
     * Shibboleth parameter
     * Shibboleth設定
     *
     * @var int
     */
    define('SHIB_ENABLED', '0');
}

//--------------プライベートツリー作成権限---------------------
/**
 * Private tree creation authority
 * When setting the private tree is not created, REPOSITORY_ADMIN_MORE administrator authority more (can not be created)
 * When the setting you want to create, the item registration authority of REPOSITORY_ITEM_REGIST_AUTH
 * The management screen, rewrite automatically the following privileges when you switch the setting of the private tree creation
 * プライベートツリー作成権限
 * プライベートツリーが作成しない設定の時は、REPOSITORY_ADMIN_MOREの管理者権限以上(作成できない)
 * 作成する設定の時は、REPOSITORY_ITEM_REGIST_AUTHのアイテム登録権限
 * 管理画面で、プライベートツリー作成の設定を切り替えたとき下記権限を自動で書き換える
 *
 * @var int
 */
define("_REPOSITORY_PRIVATETREE_AUTHORITY", REPOSITORY_ADMIN_MORE);		// CHANGE_TEXT_PRIVATETREE

/**
 * Private tree public
 * Flag for is usually private tree is a private (can only post and watch users and administrators), to be deliberately "public" treats this.
 * True: public, false: private (usually here).
 * プライベートツリー公開
 * 通常プライベートツリーは非公開(ユーザーおよび管理者のみ投稿・閲覧可)であるが、これを意図的に「公開」扱いとするためのフラグ
 * true：公開、false：非公開(通常はこちら)
 * 
 * @var boolean
 */
define("_REPOSITORY_PRIVATETREE_PUBLIC",false);

/**
 * Private tree automatic affiliation
 * Whether to automatically belong to a private tree at the time of item registration
 * True: to implement, false: not carried out (usually here)
 * プライベートツリー自動所属
 * アイテム登録時にプライベートツリーへの自動所属を行うかどうか
 * true：実施する、false：実施しない(通常はこちら)
 * 
 * @var boolean
 */
define("_REPOSITORY_PRIVATETREE_AUTO_AFFILIATION", false);

/**
 * Smartphone flag
 * Flag to determine whether or not to smartphones display
 * True: to implement, false: not carried out (usually here)
 * スマートフォンフラグ
 * スマートフォン対応表示を行うかどうか判別するフラグ
 * true：実施する、false：実施しない(通常はこちら)
 * 
 * @var boolean
 */
define("_REPOSITORY_SMART_PHONE_DISPLAY", false);

/**
 * Debug flag
 * True: to implement, false: not carried out (usually here)
 * デバッグフラグ
 * true：実施する、false：実施しない(通常はこちら)
 * 
 * @var boolean
 */
define("_DEBUG_FLG", false);

/**
 * Image slide playback speed setting
 * Playback speed of image slide to be displayed the image when specified in the flash display
 * From beginning to change to the next image, until the next image is fully display time (in milliseconds)
 * Default value: 1000
 * 画像スライド再生スピード設定
 * 画像をflash表示に指定した時に表示される画像スライドの再生スピード
 * 次の画像に変わり始めてから、次の画像が完全に表示されるまでの時間(ミリ秒単位)
 * default value: 1000
 * 
 * @var int
 */
define("_REPOSITORY_THUMBNAIL_TRANSITION_SPEED", 1000);

/**
 * Image slide playback interval setting
 * Image slide playback interval to be displaying an image on the time specified in the flash display
 * Image from is displayed, the time to begin to change to the next image (in milliseconds)
 * Default value: 4000
 * 画像スライド再生インターバル設定
 * 画像をflash表示に指定した時に表示される画像スライドの再生インターバル
 * 画像が表示されてから、次の画像に変わり始めるまでの時間(ミリ秒単位)
 * default value: 4000
 * 
 * @var int
 */
define("_REPOSITORY_THUMBNAIL_TRANSITION_INTERVAL", 4000);

/**
 * It is determined that all of the index is a public index
 * Search the table is one
 * Search item of Advanced Search is limited
 * If it is true from the false, to carry out the re-configuration of the search table
 * MySQLv4.1.1 more not equal does not work (MATCH AGAINST the environment can be used)
 * Default value: false
 * 全てのインデックスは公開インデックスであると判定される
 * 検索テーブルがひとつになる
 * 詳細検索の検索項目が制限される
 * falseからtrueにしたときは、検索テーブルの再構成を行うこと
 * MySQLv4.1.1以上(MATCH AGAINSTが使用できる環境)でないと動作しない
 * default value: false
 * 
 * @var boolean
 */
define("_REPOSITORY_HIGH_SPEED", false);

/**
 * TOP non-display
 * TOP screen is no longer displayed
 * Default value: false
 * TOP画面非表示
 * TOP画面が表示されなくなる
 * default value: false
 * 
 * @var boolean
 */
define("_REPOSITORY_NOT_SHOW_TOP_PAGE", false);

/**
 * DOI display settings (JaLC DOI)
 * Setting the DOI to be used
 * Default value: false
 * DOI表示設定(JaLC DOI)
 * 使用するDOIの設定
 * default value: false
 * 
 * @var boolean
 */
define("_REPOSITORY_JALC_DOI", false);

/**
 * DOI display settings (CrossRef DOI)
 * Setting the DOI to be used
 * Default value: false
 * DOI表示設定(CrossRef DOI)
 * 使用するDOIの設定
 * default value: false
 * 
 * @var boolean
 */
define("_REPOSITORY_JALC_CROSSREF_DOI", false);
/**
 * DOI display settings (DataCite DOI)
 * Setting the DOI to be used
 * Default value: false
 * DOI表示設定(DataCite DOI)
 * 使用するDOIの設定
 * default value: false
 * 
 * @var boolean
 */
define("_REPOSITORY_JALC_DATACITE_DOI", false);

/**
 * Physical file version management setting
 * Whether or not to perform the version management of the physical file
 * Default value: false (disabled)
 * 物理ファイルバージョン管理設定
 * 物理ファイルのバージョン管理を行うか否か
 * default value: false(無効)
 * 
 * @var boolean
 */
define("_REPOSITORY_MANAGE_PHYSICAL_FILE_VERSION", false);

/**
 * File update history list display setting
 * Whether or not to display the file version list
 * Default value: false (disabled)
 * ファイル更新履歴一覧表示設定
 * ファイルバージョン一覧を表示するか否か
 * default value: false(無効)
 * 
 * @var boolean
 */
define("_REPOSITORY_SHOW_FILE_UPDATE_HISTORY_LIST", false);

/**
 * Author e-mail address display settings
 * Whether or not to display the e-mail address of the author
 * Default value: false (disabled)
 * 著者メールアドレス表示設定
 * 著者のメールアドレスを表示するか否か
 * default value: false(無効)
 * 
 * @var boolean
 */
define("_REPOSITORY_SHOW_AUTHOR_MAIL_ADDRESS", false);

/**
 * ADetails screen image display settings
 * Whether or not to display an image or a Web page of the URL specified in the link attribute to detail screen
 * Default value: false (disabled)
 * 詳細画面画像表示設定
 * リンク属性で指定したURLの画像またはWebページを詳細画面内に表示するか否か
 * default value: false(無効)
 * 
 * @var boolean
 */
define("_REPOSITORY_SHOW_IMAGE_URL_OF_LINK", true);

/**
 * E-mail address of export file display settings by not administrator or register
 * Whether or not to display the e-mail address
 * Default value: false (disabled)
 * 非管理者/非登録者に対するエクスポートファイルのメールアドレス表示設定
 * メールアドレスを表示するか否か
 * default value: false(無効)
 * 
 * @var boolean
 */
define("_REPOSITORY_SHOW_EXPORT_MAIL_ADDRESS", false);

/**
 * Free input propriety set of DOI suffix
 * true: free input mode, false: automatic departure number mode
 * Default value: false (disabled)
 * DOI suffixの自由入力可否設定
 * true: 自由入力モード、false: 自動発番モード
 * default value: false(無効)
 * 
 * @var boolean
 */
define("_REPOSITORY_WEKO_DOISUFFIX_FREE", false);

//------------repon.org設定----------
// default value false
define("_REPOSITORY_REPON_JUNII2_EXDESCRIPTION",true);
define("_REPOSITORY_REPON_IIIF_SUPPORT",true);

?>
