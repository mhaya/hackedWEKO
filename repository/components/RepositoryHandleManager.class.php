<?php

/**
 * Handle management common classes
 * ハンドル管理共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryHandleManager.class.php 71165 2016-08-22 09:20:28Z keiya_sugimoto $
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
 * WEKO logic-based base class
 * WEKOロジックベース基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryLogicBase.class.php';

/**
 * Y handle (http://id.nii.ac.jp/) cooperative processing common classes
 * Yハンドル(http://id.nii.ac.jp/)連携処理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/IDServer.class.php';

/**
 * Exception class
 * 例外基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/AppException.class.php';
/**
 * WEKO logger class
 * WEKOロガークラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/AppLogger.class.php';
/**
 * Repository module constant class
 * WEKO共通定数クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryConst.class.php';

/**
 * Check grant doi class
 * DOI付与チェックビジネスクラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/doi/Checkdoi.class.php';

/**
 * Handle management common classes
 * ハンドル管理共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositoryHandleManager extends RepositoryLogicBase
{
    /**
     * NDL JaLC DOI unique ID
     * NDL JaLC DOI ユニークID
     *
     * @var int
     */
    const ID_LIBRARY_JALC_DOI = 50;
    /**
     * JalC DOI unique ID
     * JaLC DOI ユニークID
     *
     * @var int
     */
    const ID_JALC_DOI = 40;
    /**
     * CrossRef DOI unique ID
     * CrossRef DOI ユニークID
     *
     * @var int
     */
    const ID_CROSS_REF_DOI = 30;
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * DataCite DOI unique ID
     * DataCite DOI ユニークID
     *
     * @var int
     */
    const ID_DATACITE_DOI = 25;
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    /**
     * CNRI unique ID
     * CNRI ユニークID
     *
     * @var int
     */
    const ID_CNRI_HANDLE = 20;
    /**
     * Y handle unique ID
     * YハンドルユニークID
     *
     * @var int
     */
    const ID_Y_HANDLE = 10;
    /**
     * DOI URL prefix
     * DOI URL接頭辞
     *
     * @var string
     */
    const PREFIX_DOI = "http://doi.org/";
    /**
     * CNRI URL
     * CNRI URL
     *
     * @var string
     */
    const PREFIX_CNRI = "http://hdl.handle.net/";
    /**
     * Y handle URL
     * YハンドルURL
     *
     * @var string
     */
    const PREFIX_Y_HANDLE = "http://id.nii.ac.jp/";
    /**
     * DOI announcement for the address
     * DOIアナウンス用アドレス
     *
     * @var string
     */
    const PREFIX_SELF_DOI = "info:doi/";
    /**
     * Old DOI URL
     * 旧DOI URL
     *
     * @var string
     */
    const PREFIX_LIBRARY_DOI_HTTP_DX = "http://dx.doi.org/";
    /**
     * DOI URL
     * DOI URL
     *
     * @var string
     */
    const PREFIX_LIBRARY_DOI_HTTP = "http://doi.org/";
    /**
     * DOI URL schemes
     * DOIのURNスキーム
     *
     * @var string
     */
    const PREFIX_LIBRARY_DOI_DOI = "doi:";
    /**
     * DOI registered
     * DOI登録済み
     *
     * @var int
     */
    const DOI_STATUS_GRANTED = 1;
    /**
     * DOI withdrawn already
     * DOI取り下げ済み
     *
     * @var int
     */
    const DOI_STATUS_DROPED = 2;
    
    /**
     * NDL prefix unset
     * 国立国会図書館 prefix未設定
     *
     * @var string
     */
    const ERROR_KEY_NO_PREFIX_NDL = 'repository_no_prefix_ndl';
    /**
     * Y handle prefix unset
     * Yハンドル prefix未設定
     *
     * @var string
     */
    const ERROR_KEY_NO_PREFIX_Y_HANDLE = 'repository_no_prefix_y_handle';
    /**
     * CNRI prefix unset
     * CNRI prefix未設定
     *
     * @var string
     */
    const ERROR_KEY_NO_PREFIX_CNRI = 'repository_no_prefix_cnri';
    /**
     * DOI format error
     * DOI形式エラー
     *
     * @var string
     */
    const ERROR_KEY_INVALID_NDL_DOI_FORMAT = 'repository_invalid_doi_format';
    /**
     * Y handle format error
     * Yハンドル形式エラー
     *
     * @var string
     */
    const ERROR_KEY_INVALID_Y_HANDLE_FORMAT = 'repository_invalid_y_handle_format';
    /**
     * CNRI format error
     * CNRI形式エラー
     *
     * @var string
     */
    const ERROR_KEY_INVALID_CNRI_FORMAT = 'repository_invalid_cnri_format';
    
    // Add DataCite 2015/02/26 K.Sugimoto --start--
    /**
     * Error message
     * エラーメッセージ
     *
     * @var string
     */
    public $err_msg = null;
    // Add DataCite 2015/02/26 K.Sugimoto --end--
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $session Session management objects Session管理オブジェクト
     * @param RepositoryDbAccess $dbAccess DB object wrapper Class DBオブジェクトラッパークラス
     * @param string $transStartDate Transaction start date トランザクション開始日時
     */
    public function __construct($session, $dbAccess, $transStartDate)
    {
        parent::__construct($session, $dbAccess, $transStartDate);
    }
    
    /**
     * Register Y handle prefix
     * Yハンドルprefix登録
     * 
     * @param string $captcha_string Input string 入力された文字列
     * @param string $auth_session_ide Authentication session ID 認証セッションID
     * @return boolean Result 結果
     */
    public function registerYHandlePrefix($captcha_string, $auth_session_ide)
    {
        $IDServer = new IDServer($this->Session, $this->dbAccess->getDb());
        
        $YHandlePrefix = $IDServer->postCaptchaString($captcha_string, $auth_session_ide);
        // Bug fix WEKO-2014-006 2014/04/28 T.Koyasu --start--
        // return value is string, mismatch method of compare
        if(strcmp($YHandlePrefix, "false") === 0) {
            return false;
        }
        // Bug fix WEKO-2014-006 2014/04/28 T.Koyasu --end--
        
        $this->registerPrefixById(self::ID_Y_HANDLE, $YHandlePrefix);
        
        return true;
    }
    
    /**
     * Get prefix
     * prefix取得
     * 
     * @param int $prefix_id Prefix unique ID PrefixユニークID
     * @return string prefix prefix
     */
    public function getPrefix($prefix_id)
    {
        $query = "SELECT prefix_id".
                 " FROM ".DATABASE_PREFIX."repository_prefix".
                 " WHERE prefix_id IS NOT NULL".
                 " AND prefix_id != ''".
                 " AND id = ?".
                 " AND is_delete = ?".
                 " ;";
        $params = array();
        $params[] = $prefix_id;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        if(count($result) == 0) {
            return "";
        }
        
        return $result[0]['prefix_id'];
    }
    
    /**
     * Get LibraryJalcDoi prefix
     * 国立国会図書館prefix取得
     * 
     * @return string prefix prefix
     */
    public function getLibraryJalcDoiPrefix()
    {
        $result = $this->getPrefix(self::ID_LIBRARY_JALC_DOI);
        
        return $result;
    }
    
    /**
     * Get JalcDoi prefix
     * JalC DOI prefix取得
     * 
     * @return string prefix prefix
     */
    public function getJalcDoiPrefix()
    {
        $result = $this->getPrefix(self::ID_JALC_DOI);
        
        return $result;
    }
    
    /**
     * Get CrossRef prefix
     * CrossRef DOI prefix取得
     * 
     * @return string prefix prefix
     */
    public function getCrossRefPrefix()
    {
        $result = $this->getPrefix(self::ID_CROSS_REF_DOI);
        
        return $result;
    }
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * Get DataCite prefix
     * DataCite DOI prefix取得
     * 
     * @return string prefix prefix
     */
    public function getDataCitePrefix()
    {
        $result = $this->getPrefix(self::ID_DATACITE_DOI);
        
        return $result;
    }
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    
    /**
     * Get Cnri prefix
     * CNRI prefix取得
     * 
     * @return string prefix prefix
     */
    public function getCnriPrefix()
    {
        $result = $this->getPrefix(self::ID_CNRI_HANDLE);
        
        return $result;
    }
    
    /**
     * Get Y handle prefix
     * Yハンドルprefix取得
     * 
     * @return string prefix prefix
     */
    public function getYHandlePrefix()
    {
        $result = $this->getPrefix(self::ID_Y_HANDLE);
        
        return $result;
    }
    
    /**
     * Register prefix
     * prefix登録
     * 
     * @param string $jalcDoiPrefix JaLC DOI prefix JaLC DOI prefix
     * @param string $crossRefDoiPrefix CrossRef DOI prefix CrossRef DOI prefix
     * @param string $dataCiteDoiPrefix DataCite DOI prefix DataCite DOI prefix
     * @param string $cnriPrefix CNRI prefix CNRI prefix
     */
    public function registerPrefix($jalcDoiPrefix, $crossRefDoiPrefix, $dataCiteDoiPrefix, $cnriPrefix)
    {
        $this->registerPrefixById(self::ID_JALC_DOI, $jalcDoiPrefix);
        $this->registerPrefixById(self::ID_CROSS_REF_DOI, $crossRefDoiPrefix);
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        $this->registerPrefixById(self::ID_DATACITE_DOI, $dataCiteDoiPrefix);
        // Add DataCite 2015/02/10 K.Sugimoto --end--
        $this->registerPrefixById(self::ID_CNRI_HANDLE, $cnriPrefix);
    }
    
    /**
     * Register Y handle suffix
     * Yハンドルsuffix登録
     * 
     * @param string $title Title タイトル
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     */
    public function registerYhandleSuffix($title, $item_id, $item_no)
    {
        $IDServer = new IDServer($this->Session, $this->dbAccess->getDb());
        
        $url = $IDServer->getSuffix($title, $item_id, $this->transStartDate);
        
        $suf = $this->extractYhandleSuffix($url);
        
        $query = "INSERT INTO ".DATABASE_PREFIX."repository_suffix".
                 " (item_id, item_no, id, suffix, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete)".
                 " VALUES".
                 " (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)".
                 " ON DUPLICATE KEY ". 
                 " UPDATE suffix = ?, mod_user_id = ?, mod_date = ? ". 
                 " ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = self::ID_Y_HANDLE;
        $params[] = $suf;
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = "";
        $params[] = $this->transStartDate;
        $params[] = $this->transStartDate;
        $params[] = "";
        $params[] = 0;
        $params[] = $suf;
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->transStartDate;
        
        $this->dbAccess->executeQuery($query, $params);
    }
    
    /**
     * Register Y handle suffix stub
     * Yハンドルsuffix登録 スタブ
     * 
     * @param string $title Title タイトル
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     */
/*    public function registerYhandleSuffix($title, $item_id, $item_no)
    {
        $pre = $this->getPrefix(self::ID_Y_HANDLE);
        if(empty($pre)) {
            return false;
        }
        
        $suf = str_pad($item_id, 8, "0", STR_PAD_LEFT);
        
        $query = "INSERT INTO ".DATABASE_PREFIX."repository_suffix".
                 " (item_id, item_no, id, suffix, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete)".
                 " VALUES".
                 " (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)".
                 " ON DUPLICATE KEY ". 
                 " UPDATE suffix = ?, mod_user_id = ?, mod_date = ? ". 
                 " ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = self::ID_Y_HANDLE;
        $params[] = $suf;
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = "";
        $params[] = $this->transStartDate;
        $params[] = $this->transStartDate;
        $params[] = "";
        $params[] = 0;
        $params[] = $suf;
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->transStartDate;
                 
        $this->dbAccess->executeQuery($query, $params);
    }*/
    
    
    /**
     * Register prefix by id
     * prefix登録
     * 
     * @param int $prefix_id prefix unique id prefixユニークID
     * @param string $prefix prefix prefix
     */
    private function registerPrefixById($prefix_id, $prefix)
    {
        if(isset($prefix))
        {
            if($prefix_id == self::ID_JALC_DOI || $prefix_id == self::ID_CROSS_REF_DOI || $prefix_id == self::ID_DATACITE_DOI)
            {
                $result = $this->checkDoiFormat($prefix);
                
                if($result === $prefix)
                {
                    $query = "UPDATE ".DATABASE_PREFIX."repository_prefix".
                             " SET prefix_id = ?".
                             " WHERE id = ?".
                             " ;";
                    $params = array();
                    $params[] = $prefix;
                    $params[] = $prefix_id;
                         
                    $this->dbAccess->executeQuery($query, $params);
                }
            }
            else
            {
                $query = "UPDATE ".DATABASE_PREFIX."repository_prefix".
                         " SET prefix_id = ?".
                         " WHERE id = ?".
                         " ;";
                $params = array();
                $params[] = $prefix;
                $params[] = $prefix_id;
                     
                $this->dbAccess->executeQuery($query, $params);
            }
        }
    }
    
    /**
     * Get suffix
     * suffix取得
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $prefix_id prefix unique id prefixユニークID
     * @return string suffix suffix
     */
    public function getSuffix($item_id, $item_no, $prefix_id)
    {
        $query = "SELECT suffix".
                 " FROM ".DATABASE_PREFIX."repository_suffix".
                 " WHERE item_id = ?".
                 " AND item_no = ?".
                 " AND id <= ?".
                 " AND is_delete = ?".
                 " ORDER BY id DESC".
                 " LIMIT 1".
                 " ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $prefix_id;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        if(count($result) == 0) {
            return "";
        }
        
        return $result[0]['suffix'];
    }
    
    /**
     * Get suffix by prefix ID
     * 指定したPrefixに合致するsuffixのみ取得
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $prefix_id prefix unique id prefixユニークID
     * @return string suffix suffix
     */
    public function getSuffixByPrefixId($item_id, $item_no, $prefix_id)
    {
        $query = "SELECT suffix".
                 " FROM ".DATABASE_PREFIX."repository_suffix".
                 " WHERE item_id = ?".
                 " AND item_no = ?".
                 " AND id = ?".
                 " AND is_delete = ?".
                 " ORDER BY id DESC".
                 " LIMIT 1".
                 " ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $prefix_id;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        if(count($result) == 0) {
            return "";
        }
        
        return $result[0]['suffix'];
    }
    
    /**
     * Create URI
     * URI生成
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $prefix_id prefix unique id prefixユニークID
     * @return string URI URI
     */
    public function createUri($item_id, $item_no, $prefix_id)
    {
        $pre = "";
        $suf = "";
        
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        $suf = $this->getHandleSuffix($item_id, $item_no, $prefix_id);
        
        switch($prefix_id)
        {
        	case self::ID_LIBRARY_JALC_DOI:
            	$pre = $this->getLibraryJalcDoiPrefix();
            	break;
            	
        	case self::ID_JALC_DOI:
            	$pre = $this->getJalcDoiPrefix();
            	break;
            	
        	case self::ID_CROSS_REF_DOI:
            	$pre = $this->getCrossRefPrefix();
            	break;
            	
        	case self::ID_DATACITE_DOI:
            	$pre = $this->getDataCitePrefix();
            	break;
            	
        	case self::ID_CNRI_HANDLE:
                // CNRIのSuffixがない場合はYハンドルのSuffixを参照するようにする
                if(strlen($suf) == 0){
                    $suf = $this->getHandleSuffix($item_id, $item_no, self::ID_Y_HANDLE);
                }
            	$pre = $this->getCnriPrefix();
            	break;
            	
        	case self::ID_Y_HANDLE:
            	$pre = $this->getYHandlePrefix();
            	break;
            	
        	default:
            	break;
            	
        }
        
        if(strlen($pre) == 0 || strlen($suf) == 0) {
            return "";
        }
        // Add DataCite 2015/02/10 K.Sugimoto --end--
        
        $uri = "";
        if($prefix_id == self::ID_LIBRARY_JALC_DOI) {
            $uri = self::PREFIX_LIBRARY_DOI_HTTP
                 . $pre
                 . "/". $suf;
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        } elseif($prefix_id == self::ID_JALC_DOI || $prefix_id == self::ID_CROSS_REF_DOI || $prefix_id == self::ID_DATACITE_DOI) {
        // Add DataCite 2015/02/10 K.Sugimoto --end--
            $uri = self::PREFIX_DOI
                 . $pre
                 . "/". $suf;
        } elseif($prefix_id == self::ID_CNRI_HANDLE) {
            // delete the end of a sentence "/" if CNRI
            $uri = self::PREFIX_CNRI
                 . $pre
                 . "/". $suf;
        } elseif($prefix_id == self::ID_Y_HANDLE) {
            // add the end of a sentence "/" if Y handle
            $uri = self::PREFIX_Y_HANDLE
                 . $pre
                 . "/". $suf. "/";
        }
        
        return $uri;
    }
    
    /**
     * Create URI for detail
     * 詳細画面用URI作成
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return string URI URI
     */
    public function createUriForDetail($item_id, $item_no)
    {
        $uri = $this->createUri($item_id, $item_no, self::ID_LIBRARY_JALC_DOI);
        if(strlen($uri) == 0) {
            $uri = $this->createUri($item_id, $item_no, self::ID_JALC_DOI);
        }
        if(strlen($uri) == 0) {
            $uri = $this->createUri($item_id, $item_no, self::ID_CROSS_REF_DOI);
        }
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        if(strlen($uri) == 0) {
            $uri = $this->createUri($item_id, $item_no, self::ID_DATACITE_DOI);
        }
        // Add DataCite 2015/02/10 K.Sugimoto --end--
        if(strlen($uri) == 0) {
            $uri = $this->createUri($item_id, $item_no, self::ID_CNRI_HANDLE);
        }
        if(strlen($uri) == 0) {
            $uri = $this->createUri($item_id, $item_no, self::ID_Y_HANDLE);
        }
        return $uri;
    }
    
    /**
     * Create URI for junii2
     * junii2 URI生成
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return stirng URI URI
     */
    public function createUriForJuNii2($item_id, $item_no)
    {
        $url = $this->createUri($item_id, $item_no, self::ID_CNRI_HANDLE);
        if(strlen($url) == 0) {
            $url = $this->createUri($item_id, $item_no, self::ID_Y_HANDLE);
        }
        if(strlen($url) == 0) {
            $url = $this->getSubstanceUri($item_id, $item_no);
        }
        
        return $url;
    }
    
    /**
     * Create URI for Dublin Core
     * Dublin Core用のURIを作成する
     * 
     * @param int $item_id Item ID
     *                     アイテムID
     * @param int $item_no Item No
     *                     アイテム通番
     *
     * @return stirng Uri for Dublin Core
     *                Dublin Core用のURI
     */
    public function createUriForDublinCore($item_id, $item_no)
    {
        $url = $this->createUri($item_id, $item_no, self::ID_CNRI_HANDLE);
        if(strlen($url) == 0) {
            $url = $this->createUri($item_id, $item_no, self::ID_Y_HANDLE);
        }
        if(strlen($url) == 0) {
            return "";
        }
        
        return $url;
    }
    
    /**
     * Get substance URI
     * 詳細画面アクセスURI取得
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return string URI URI
     */
    public function getSubstanceUri($item_id, $item_no)
    {
        $query = "SELECT uri".
                 " FROM ".DATABASE_PREFIX."repository_item".
                 " WHERE item_id = ?".
                 " AND item_no = ?".
                 " AND is_delete = ?".
                 " ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        if(count($result) == 0) {
            return "";
        }
        
        return $result[0]['uri'];
    }
    
    /**
     * Register Y handle prefix by primary key
     * 秘密鍵を使用してYハンドルprefixを登録する
     * 
     * @return boolean Result 結果
     */
    public function registerYHandlePrefixByPriKey()
    {
        $IDServer = new IDServer($this->Session, $this->dbAccess->getDb());
        $YHandlePrefix = $IDServer->getPrefixID(true);
        if($YHandlePrefix == false) {
            return false;
        }
        
        $this->registerPrefixById(self::ID_Y_HANDLE, $YHandlePrefix);
        
        return true;
    }
    
    /**
     * Set Y-handle suffix
     * Yハンドルsuffixを生成、登録する
     *
     * @param string $title Title タイトル
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return boolean Result 結果
     */
    public function setSuffix($title, $itemId, $itemNo)
    {
        // check registered suffix
        $suffix = $this->getSuffix($itemId, $itemNo, RepositoryHandleManager::ID_Y_HANDLE);
        
        if(strlen($suffix) > 0)
        {
            // suffix is registered
            return true;
        }
        
        // regist suffix
        $result = $this->registerYhandleSuffix($title, $itemId, $itemNo);
        if($result === false)
        {
            // prefix is not registered
            return false;
        }
        
        // check suffix
        $suffix = $this->getSuffix($itemId, $itemNo, RepositoryHandleManager::ID_Y_HANDLE);
        if(strlen($suffix) == 0)
        {
            // failed to regist suffix
            return false;
        }
        
        return true;
    }
    
    /**
     * insert suffix
     * suffix挿入
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $prefix_id prefix unique id prefixユニークID
     * @param string $suffix suffix suffix
     */
    private function registSuffix($item_id, $item_no, $prefix_id, $suffix)
    {
        $query = "INSERT INTO ".DATABASE_PREFIX."repository_suffix".
                 " (item_id, item_no, id, suffix, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete)".
                 " VALUES".
                 " (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)".
                 " ON DUPLICATE KEY ". 
                 " UPDATE suffix = ?, mod_user_id = ?, mod_date = ? ". 
                 " ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $prefix_id;
        $params[] = $suffix;
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = "";
        $params[] = $this->transStartDate;
        $params[] = $this->transStartDate;
        $params[] = "";
        $params[] = 0;
        $params[] = $suffix;
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->transStartDate;
        
        $this->dbAccess->executeQuery($query, $params);
    }
    
    /**
     * Delete DOI suffix exclude regist DOI
     * 登録DOI以外のDOI suffixを削除する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $prefix_id prefix unique id prefixユニークID
     */
    private function deleteOtherDoiSuffix($item_id, $item_no, $prefix_id)
    {
        $query = "DELETE FROM ".DATABASE_PREFIX."repository_suffix ".
                 " WHERE item_id = ? ".
                 " AND item_no = ? ".
                 " AND id != ? ".
                 " AND id != ? ".
                 " AND id != ? ".
                 " ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $prefix_id;
        $params[] = self::ID_Y_HANDLE;
        $params[] = self::ID_CNRI_HANDLE;
        
        $this->dbAccess->executeQuery($query, $params);
    }
    
    /**
     * Regist DOI change log
     * DOI変更ログを登録する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $prefix_id prefix unique id prefixユニークID
     * @param string $suffix suffix suffix
     */
    private function entryDoiChangeLog($item_id, $item_no, $prefix_id, $suffix)
    {
        $prefix = $this->getPrefix($prefix_id);
        
        $query = "SELECT MAX(log_no) AS log_no ".
                 " FROM ".DATABASE_PREFIX."repository_doi_change_log ".
                 " ;";
        $result = $this->dbAccess->executeQuery($query);
        $log_no = 1;
        if(count($result) > 0)
        {
            $log_no = intval($result[0]["log_no"]) + 1;
        }
        $query = "INSERT INTO ".DATABASE_PREFIX."repository_doi_change_log ".
                 " (log_no, record_date, user_id, item_id, ra_changed, doi_changed)".
                 " VALUES".
                 " (?, ?, ?, ?, ?, ?)".
                 " ;";
        $params = array();
        $params[] = $log_no;
        $params[] = $this->transStartDate;
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $item_id;
        if($prefix_id == self::ID_LIBRARY_JALC_DOI || $prefix_id == self::ID_JALC_DOI)
        {
            $params[] = "JaLC";
        }
        else if($prefix_id == self::ID_CROSS_REF_DOI)
        {
            $params[] = "CrossRef";
        }
        else if($prefix_id == self::ID_DATACITE_DOI)
        {
            $params[] = "DataCite";
        }
        $params[] = $prefix."/".$suffix;
        
        $this->dbAccess->executeQuery($query, $params);
    }
    
    /**
     * set JaLC DOI status
     * DOI登録状況を設定する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $status DOI registration status DOI登録状況
     */
    private function registDoiStatus($item_id, $item_no, $status)
    {
        $query = "INSERT INTO ".DATABASE_PREFIX."repository_doi_status".
                 " (item_id, item_no, status, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete)".
                 " VALUES".
                 " (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)".
                 " ON DUPLICATE KEY ". 
                 " UPDATE status = ?, mod_user_id = ?, mod_date = ? ". 
                 " ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $status;
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = "";
        $params[] = $this->transStartDate;
        $params[] = $this->transStartDate;
        $params[] = "";
        $params[] = 0;
        $params[] = $status;
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->transStartDate;
        
        $this->dbAccess->executeQuery($query, $params);
    }
    
    /**
     * extract suffix by Permalink of National Diet Library
     * 国立国会図書館のPermalinkからsuffixを抽出する
     *
     * @param string $uri Permalink パーマリンク
     * @return string suffix suffix
     */
    private function extractLibraryDoiSuffix($uri){
        $matches = array();
        $suffix = "";
        
        $prefix = $this->getLibraryJalcDoiPrefix();
        if(strlen($prefix) === 0){
            $exception = new AppException(self::ERROR_KEY_NO_PREFIX_NDL);
            $exception->addError(self::ERROR_KEY_NO_PREFIX_NDL);
            
            $this->Logger->debugLog(self::ERROR_KEY_NO_PREFIX_NDL, __FILE__, __CLASS__, __LINE__);
            throw $exception;
        }
        
        // 下記形式のURIから[suffix]を抽出する
        // DOIの入力形式は下記の通り
        //   - http://doi.org/[prefix]/[suffix]
        //   - http://dx.doi.org/[prefix]/[suffix]
        //   - doi:[prefix]/[suffix]
        //   - info:doi/[prefix]/[suffix]
        //   - [prefix]/[suffix]
        if(preg_match("/^". preg_quote(self::PREFIX_SELF_DOI. $prefix, "/"). "\/(.+)$/", $uri, $matches) === 1
            || preg_match("/^". preg_quote(self::PREFIX_LIBRARY_DOI_HTTP_DX. $prefix, "/"). "\/(.+)$/", $uri, $matches) === 1
            || preg_match("/^". preg_quote(self::PREFIX_LIBRARY_DOI_HTTP. $prefix, "/"). "\/(.+)$/", $uri, $matches) === 1
            || preg_match("/^". preg_quote(self::PREFIX_LIBRARY_DOI_DOI. $prefix, "/"). "\/(.+)$/", $uri, $matches) === 1
            || preg_match("/^". preg_quote($prefix, "/"). "\/(.+)$/", $uri, $matches) === 1){
            
            $suffix = $this->checkDoiFormat($matches[1]);
            if(strlen($suffix) === 0){
                $exception = new AppException(self::ERROR_KEY_INVALID_NDL_DOI_FORMAT);
                $exception->addError(self::ERROR_KEY_INVALID_NDL_DOI_FORMAT);
                
                $this->Logger->debugLog(self::ERROR_KEY_INVALID_NDL_DOI_FORMAT. "::uri=". $uri, __FILE__, __CLASS__, __LINE__);
                throw $exception;
            }
        } else {
            $exception = new AppException(self::ERROR_KEY_INVALID_NDL_DOI_FORMAT);
            $exception->addError(self::ERROR_KEY_INVALID_NDL_DOI_FORMAT);
            
            $this->Logger->debugLog(self::ERROR_KEY_INVALID_NDL_DOI_FORMAT. "::uri=". $uri, __FILE__, __CLASS__, __LINE__);
            throw $exception;
        }
        
        return $suffix;
    }
    
    /**
     * extract suffix by Permalink of Y-Handle
     * Yハンドル(IDサーバ)のPermalinkからsuffixを抽出する
     *
     * @param string $uri Permalink パーマリンク
     * @return string suffix suffix
     */
    private function extractYhandleSuffix($uri){
        $matches = array();
        $suffix = "";
    
        $prefix = $this->getYHandlePrefix();
        if(strlen($prefix) === 0){
            // Prefix未設定での運用もあり、そちらはエラーというわけではないので、
            // 空文字としてSuffixを登録する
            return "";
        }
        
        // http://id.nii.ac.jp/[prefix]/[suffix]の
        // [suffix]を抽出する(0埋め8桁)
        // [prefix]内に正規表現の特殊文字が含まれる可能性があるため、
        // preg_quoteでエスケープするようにしている
        if(preg_match("/^". preg_quote(self::PREFIX_Y_HANDLE. $prefix, "/"). "\/([0-9]{8})\//", $uri, $matches) === 1){
            $suffix = $matches[1];
        } else {
            $exception = new AppException(self::ERROR_KEY_INVALID_Y_HANDLE_FORMAT);
            $exception->addError(self::ERROR_KEY_INVALID_Y_HANDLE_FORMAT);
            
            $this->Logger->debugLog(self::ERROR_KEY_INVALID_Y_HANDLE_FORMAT. "::uri=". $uri, __FILE__, __CLASS__, __LINE__);
            throw $exception;
        }
        
        return $suffix;
    }
    
    /**
     * extract suffix by Permalink of CNRI-Handle
     * CNRIハンドルのPermalinkからsuffixを抽出する
     *
     * @param string $uri Permalink パーマリンク
     * @return string suffix suffix
     */
    public function extractCnriSuffix($uri){
        $matches = array();
        $suffix = "";
        
        $prefix = $this->getCnriPrefix();
        if(strlen($prefix) === 0){
            $exception = new AppException(self::ERROR_KEY_NO_PREFIX_CNRI);
            $exception->addError(self::ERROR_KEY_NO_PREFIX_CNRI);
            
            $this->Logger->debugLog(self::ERROR_KEY_NO_PREFIX_CNRI, __FILE__, __CLASS__, __LINE__);
            throw $exception;
        }
        
        // http://hdl.handle.net/[prefix]/[suffix]または[prefix]/[suffix]の
        // [suffix]を抽出する
        // [prefix]内に正規表現の特殊文字が含まれる可能性があるため、
        // preg_quoteでエスケープするようにしている
        // 下記URLより、suffixとして使用できる値に制限はない
        //  http://www.handle.net/HSj/hdlnet-2-SVC-AGREE-3.pdf
        if(preg_match("/^". preg_quote(self::PREFIX_CNRI. $prefix, "/"). "\/(.+)$/", $uri, $matches) === 1
           || preg_match("/^". preg_quote($prefix, "/"). "\/(.+)$/", $uri, $matches) === 1){
           
            $suffix = $matches[1];
        } else {
            $exception = new AppException(self::ERROR_KEY_INVALID_CNRI_FORMAT);
            $exception->addError(self::ERROR_KEY_INVALID_CNRI_FORMAT);
            
            $this->Logger->debugLog(self::ERROR_KEY_INVALID_CNRI_FORMAT. "::uri=". $uri, __FILE__, __CLASS__, __LINE__);
            throw $exception;
        }
        
        return $suffix;
    }
    
    /**
     * Regist Library JaLC DOI suffix
     * 国立国会図書館suffixを登録
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param string $suffix suffix suffix
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode) 
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     */
    public function registLibraryJalcdoiSuffix($item_id, $item_no, $uri, $changeMode=Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_NORMAL)
    {
        // Permalinkからsuffixを抽出する
        // 抽出できなかった場合、空文字が返却される
        $suffix = $this->extractLibraryDoiSuffix($uri);
        
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        $query = "SELECT param_value".
                 " FROM ".DATABASE_PREFIX."repository_parameter".
                 " WHERE param_name = ?".
                 " AND is_delete = ?;";
        $params = array();
        $params[] = "prefix_flag";
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        if(count($result) > 0 && $result[0]["param_value"] == 1)
        {
        	$yHandlePrefix = $this->getYHandlePrefix();
        	$suffix = $yHandlePrefix.".".$suffix;
        }
        // Add DataCite 2015/02/10 K.Sugimoto --end--
        
        // 現在登録されているsuffixから変更がある時のみUPSERTを実施する
        $nowSuffix = $this->getSuffixByPrefixId($item_id, $item_no, self::ID_LIBRARY_JALC_DOI);
        if($suffix != $nowSuffix) {
            $this->registSuffix($item_id, $item_no, self::ID_LIBRARY_JALC_DOI, $suffix);
            $this->registDoiStatus($item_id, $item_no, self::DOI_STATUS_GRANTED);
    
            // DOI変更モード時は登録DOI以外のDOI suffix物理削除、DOI変更ログ登録実施
            if($changeMode == Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_DOI_CHANGE) {
                $this->deleteOtherDoiSuffix($item_id, $item_no, self::ID_LIBRARY_JALC_DOI);
                $this->entryDoiChangeLog($item_id, $item_no, self::ID_LIBRARY_JALC_DOI, $suffix);
            }
        }
    }
    
    /**
     * Regist JaLC DOI suffix
     * JaLC DOI suffixを登録
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param string $suffix suffix suffix
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode) 
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     */
    public function registJalcdoiSuffix($item_id, $item_no, $suffix, $changeMode=Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_NORMAL)
    {
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        $query = "SELECT param_value".
                 " FROM ".DATABASE_PREFIX."repository_parameter".
                 " WHERE param_name = ?".
                 " AND is_delete = ?;";
        $params = array();
        $params[] = "prefix_flag";
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        if(count($result) > 0 && $result[0]["param_value"] == 1)
        {
        	$yHandlePrefix = $this->getYHandlePrefix();
        	$suffix = $yHandlePrefix.".".$suffix;
        }
        // Add DataCite 2015/02/10 K.Sugimoto --end--
    
        // 現在登録されているsuffixから変更がある時のみUPSERTを実施する
        $nowSuffix = $this->getSuffixByPrefixId($item_id, $item_no, self::ID_JALC_DOI);
        if($suffix != $nowSuffix) {
            $this->registSuffix($item_id, $item_no, self::ID_JALC_DOI, $suffix);
            $this->registDoiStatus($item_id, $item_no, self::DOI_STATUS_GRANTED);
    
            // DOI変更モード時は登録DOI以外のDOI suffix物理削除、DOI変更ログ登録実施
            if($changeMode == Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_DOI_CHANGE) {
                $this->deleteOtherDoiSuffix($item_id, $item_no, self::ID_JALC_DOI);
                $this->entryDoiChangeLog($item_id, $item_no, self::ID_JALC_DOI, $suffix);
            }
        }
    }
    
    /**
     * Regist Cross Ref suffix
     * CrossRef DOIのsuffixを登録
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param string $suffix suffix suffix
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode) 
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     */
    public function registCrossrefSuffix($item_id, $item_no, $suffix, $changeMode=Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_NORMAL)
    {
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        $query = "SELECT param_value".
                 " FROM ".DATABASE_PREFIX."repository_parameter".
                 " WHERE param_name = ?".
                 " AND is_delete = ?;";
        $params = array();
        $params[] = "prefix_flag";
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        if(count($result) > 0 && $result[0]["param_value"] == 1)
        {
        	$yHandlePrefix = $this->getYHandlePrefix();
        	$suffix = $yHandlePrefix.".".$suffix;
        }
        // Add DataCite 2015/02/10 K.Sugimoto --end--
    
        // 現在登録されているsuffixから変更がある時のみUPSERTを実施する
        $nowSuffix = $this->getSuffixByPrefixId($item_id, $item_no, self::ID_CROSS_REF_DOI);
        if($suffix != $nowSuffix) {
            $this->registSuffix($item_id, $item_no, self::ID_CROSS_REF_DOI, $suffix);
            $this->registDoiStatus($item_id, $item_no, self::DOI_STATUS_GRANTED);
    
            // DOI変更モード時は登録DOI以外のDOI suffix物理削除、DOI変更ログ登録実施
            if($changeMode == Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_DOI_CHANGE) {
                $this->deleteOtherDoiSuffix($item_id, $item_no, self::ID_CROSS_REF_DOI);
                $this->entryDoiChangeLog($item_id, $item_no, self::ID_CROSS_REF_DOI, $suffix);
            }
        }
    }
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * Regist DataCite suffix
     * DataCite DOIのsuffixを登録
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param string $suffix suffix suffix
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode) 
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     */
    public function registDataciteSuffix($item_id, $item_no, $suffix, $changeMode=Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_NORMAL)
    {
        $query = "SELECT param_value".
                 " FROM ".DATABASE_PREFIX."repository_parameter".
                 " WHERE param_name = ?".
                 " AND is_delete = ?;";
        $params = array();
        $params[] = "prefix_flag";
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        if(count($result) > 0 && $result[0]["param_value"] == 1)
        {
        	$yHandlePrefix = $this->getYHandlePrefix();
        	$suffix = $yHandlePrefix.".".$suffix;
        }
    
        // 現在登録されているsuffixから変更がある時のみUPSERTを実施する
        $nowSuffix = $this->getSuffixByPrefixId($item_id, $item_no, self::ID_DATACITE_DOI);
        if($suffix != $nowSuffix) {
            $this->registSuffix($item_id, $item_no, self::ID_DATACITE_DOI, $suffix);
            $this->registDoiStatus($item_id, $item_no, self::DOI_STATUS_GRANTED);
    
            // DOI変更モード時は登録DOI以外のDOI suffix物理削除、DOI変更ログ登録実施
            if($changeMode == Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_DOI_CHANGE) {
                $this->deleteOtherDoiSuffix($item_id, $item_no, self::ID_DATACITE_DOI);
                $this->entryDoiChangeLog($item_id, $item_no, self::ID_DATACITE_DOI, $suffix);
            }
        }
    }
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    
    /**
     * Get Y Handle suffix
     * Yハンドルsuffix取得
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param string $suffix suffix suffix
     */
    public function getYHandleSuffix($item_id, $item_no)
    {
        return $this->getHandleSuffix($item_id, $item_no, self::ID_Y_HANDLE);
    }
    
    /**
     * Get Library Jalc Doi suffix
     * 国立国会図書館suffixを取得
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param string $suffix suffix suffix
     */
    public function getLibraryJalcdoiSuffix($item_id, $item_no)
    {
        return $this->getHandleSuffix($item_id, $item_no, self::ID_LIBRARY_JALC_DOI);
    }
    
    /**
     * Get Jalc Doi suffix
     * JalC DOI suffixを取得
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param string $suffix suffix suffix
     */
    public function getJalcdoiSuffix($item_id, $item_no)
    {
        return $this->getHandleSuffix($item_id, $item_no, self::ID_JALC_DOI);
    }
    
    /**
     * Get CrossRef suffix
     * CrossRef DOI suffixを取得
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param string $suffix suffix suffix
     */
    public function getCrossrefSuffix($item_id, $item_no)
    {
        return $this->getHandleSuffix($item_id, $item_no, self::ID_CROSS_REF_DOI);
    }
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * Get DataCite suffix
     * DataCite DOI suffixを取得
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param string $suffix suffix suffix
     */
    public function getDataciteSuffix($item_id, $item_no)
    {
        return $this->getHandleSuffix($item_id, $item_no, self::ID_DATACITE_DOI);
    }
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    
    /**
     * Get suffix
     * suffix取得
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $prefix_id Prefix unique id prefixユニークID
     * @return string suffix suffix
     */
    private function getHandleSuffix($item_id, $item_no, $prefix_id)
    {
        $query = "SELECT suffix".
                 " FROM ".DATABASE_PREFIX."repository_suffix".
                 " WHERE item_id = ?".
                 " AND item_no = ?".
                 " AND id = ?".
                 " AND is_delete = ?".
                 " LIMIT 1".
                 " ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $prefix_id;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        if(count($result) == 0) {
            return '';
        }
        
        return $result[0]['suffix'];
    }
    
    /**
     * Withdraw the DOI (to 2 status)
     * DOIを取り下げる(statusを2にする)
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return boolean Result 結果
     */
    public function dropDoiSuffix($item_id, $item_no)
    {
        // JaLC DOIを取り下げる
        $this->registDoiStatus($item_id, $item_no, self::DOI_STATUS_DROPED);
        
        // 国会図書館JaLC DOIを削除する
        $this->deleteSuffix($item_id, $item_no, self::ID_LIBRARY_JALC_DOI);
        
        // Cross Refを削除する
        $this->deleteSuffix($item_id, $item_no, self::ID_CROSS_REF_DOI);
        
        // JaLC DOIを削除する
        $this->deleteSuffix($item_id, $item_no, self::ID_JALC_DOI);
        
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        // DataCiteを削除する
        $this->deleteSuffix($item_id, $item_no, self::ID_DATACITE_DOI);
        // Add DataCite 2015/02/10 K.Sugimoto --end--
    }
    
    /**
     * Delete suffix
     * サフィックスを削除する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $id prefix unique id prefixユニークID
     * @return boolean Result 結果
     */
    private function deleteSuffix($item_id, $item_no, $id)
    {
        $query = "UPDATE ".DATABASE_PREFIX."repository_suffix ".
                 "SET mod_user_id = ?, del_user_id = ?, mod_date = ?, del_date = ?, is_delete = ? ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND id = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->transStartDate;
        $params[] = $this->transStartDate;
        $params[] = 1;
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $id;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
    }
    
    /**
     * To remove the DOI grant state
     * DOI付与状態を削除する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return boolean Result 結果
     */
    public function deleteDoiStatus($item_id, $item_no)
    {
        $query = "UPDATE ".DATABASE_PREFIX."repository_doi_status ".
                 "SET mod_user_id = ?, del_user_id = ?, mod_date = ?, del_date = ?, is_delete = ? ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = $this->transStartDate;
        $params[] = $this->transStartDate;
        $params[] = 1;
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
    }
    
    /**
     * Create Self DOI URI
     * selfDOIタグに出力するURIを作成する
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $id prefix unique id prefixユニークID
     * @return string URI URI
     */
    public function createSelfDoiUri($item_id, $item_no, $id)
    {
        $uri = "";
        $prefix = "";
        $suffix = "";
        if($id == self::ID_LIBRARY_JALC_DOI)
        {
            $prefix = $this->getLibraryJalcDoiPrefix();
            $suffix = $this->getLibraryJalcdoiSuffix($item_id, $item_no);
            $suffix = $this->checkDoiFormat($suffix);
        }        
        else if($id == self::ID_JALC_DOI)
        {
            $prefix = $this->getJalcDoiPrefix();
            $prefix = $this->checkDoiFormat($prefix);
            $suffix = $this->getJalcdoiSuffix($item_id, $item_no);
            $suffix = $this->checkDoiFormat($suffix);
        }
        else if($id == self::ID_CROSS_REF_DOI)
        {
            $prefix = $this->getCrossRefPrefix();
            $prefix = $this->checkDoiFormat($prefix);
            $suffix = $this->getCrossrefSuffix($item_id, $item_no);
            $suffix = $this->checkDoiFormat($suffix);
        }
        // Add DataCite 2015/02/10 K.Sugimoto --start--
        else if($id == self::ID_DATACITE_DOI)
        {
            $prefix = $this->getDataCitePrefix();
            $prefix = $this->checkDoiFormat($prefix);
            $suffix = $this->getDataciteSuffix($item_id, $item_no);
            $suffix = $this->checkDoiFormat($suffix);
        }
        // Add DataCite 2015/02/10 K.Sugimoto --end--
        
        if(strlen($prefix) > 0 && strlen($suffix) > 0)
        {
            $uri = self::PREFIX_SELF_DOI . $prefix . "/" . $suffix;
        }
        
        return $uri;
    }

    /**
     * Register CNRI suffix
     * CNRI suffixを登録
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial id アイテム通番
     * @param string $suffix suffix suffix
     */
    public function registCnriSuffix($item_id, $item_no, $suffix)
    {
        $this->registSuffix($item_id, $item_no, self::ID_CNRI_HANDLE, $suffix);
    }
    
    /**
     * Get CNRI suffix
     * CNRI suffixを取得
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial id アイテム通番
     * @return string suffix suffix
     */
    public function getCnriSuffix($item_id, $item_no)
    {
        return $this->getHandleSuffix($item_id, $item_no, self::ID_CNRI_HANDLE);
    }
    
    // Bug Fix WEKO-2014-083 K.Sugimoto 2014/09/05 --start--
    /**
     * To create the URI of the Y handle
     * YハンドルのURIを作成する
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial id アイテム通番
     * @return string URI URI
     */
    public function createYHandleUri($item_id, $item_no)
    {
        $uri = "";
        $prefix = "";
        $suffix = "";
        
        // Yハンドルのプレフィックスを取得する
        $prefix = $this->getYHandlePrefix();
        // Yハンドルのサフィックスを取得する
        $suffix = $this->getYHandleSuffix($item_id, $item_no);
        
        // YハンドルのURIを作成する
        if(strlen($prefix) > 0 && strlen($suffix) > 0)
        {
            $uri = self::PREFIX_Y_HANDLE . $prefix . "/" . $suffix;
        }
        
        return $uri;
    }
    // Bug Fix WEKO-2014-083 K.Sugimoto 2014/09/05 --end--
    
    // Add DataCite 2015/02/26 K.Sugimoto --start--
    /**
     * check prefix or suffix format. if format is invalid, return empty string
     * プレフィックスまたはサフィックスのフォーマットが正しいかチェックし、正しくない場合は空文字を返す
     * 
     * @param string $doi prefix or suffix prefixまたはsuffix
     * @return string prefix or suffix prefixまたはsuffix
     *                any -> format is valid
     *                empty -> format is invalid
     */
    public function checkDoiFormat($doi)
    {
        $match = array();
        
        // 半角英数字、_、-、.、;、(、)、/のみ使用可
        // JaLCのDOI付与ルールに関しては下記に記載
        //  https://japanlinkcenter.org/top/doc/JaLC_tech_journal_article_manual.pdf
        // CrossRefのDOI付与ルールに関しては下記に記載
        //  http://help.crossref.org/obtaining_a_doi_prefix
        //  http://help.crossref.org/establishing_a_doi_suffix_pattern
        if(preg_match("/^(\w|\-|\.|\;|\(|\)|\/)*$/", $doi, $match)==1)
        {
        	return $doi;
        }
        else
        {
        	$this->err_msg = "DoiFormatIncorrect";
        	return "";
        }
    }
    // Add DataCite 2015/02/26 K.Sugimoto --end--

    /**
     * Entry data of selfDOI
     * selfDOIのデータを登録する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテムNo
     * @param string $ra Kind of selfDOI selfDOIの種類(JaLC、CrossRef、DataCite、空文字)
     * @param string $selfdoi Value of selfDOI selfDOIの値
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode) 
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @return Object Structure of result of check DOI grant
     *                DOI付与チェック結果の構造体
     */
    public function entrySelfdoi($item_id, $item_no, $ra, $selfdoi, $changeMode)
    {
        // 登録モードがnullの場合、通常モードとして扱う
        if(!isset($changeMode))
        {
            $changeMode = Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_NORMAL;
        }
        
        if(isset($ra) && strlen($ra) > 0)
        {
            $ra = strtoupper($ra);
            if($ra === strtoupper(RepositoryConst::JUNII2_SELFDOI_RA_JALC))
            {
                $libraryJalcdoiPrefix = $this->getLibraryJalcDoiPrefix();
                $libraryJalcdoiSuffix = $selfdoi;
                // 抽出できた場合は抽出結果をsuffixとして扱う
                // 抽出できなかった場合は元の値をsuffixとして扱う
                $isExtract = $this->extractSuffix($libraryJalcdoiSuffix, $libraryJalcdoiPrefix);
                // 抽出できた場合のみ国立国会図書館JaLC DOIとしてDOI付与処理を行う
                if($isExtract)
                {
                    $checkdoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
                    $checkRegist = $checkdoi->checkDoiGrant($item_id, 
                                                            $item_no, 
                                                            Repository_Components_Business_Doi_Checkdoi::TYPE_LIBRARY_JALC_DOI, 
                                                            $libraryJalcdoiSuffix, 
                                                            Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_IMPORT_SWORD, 
                                                            $changeMode);
                    if($checkRegist->isGrantDoi)
                    {
                        $this->registLibraryJalcdoiSuffix($item_id, $item_no, $selfdoi, $changeMode);
                    }
                }
                else
                {
                    $checkRegist = $this->entryJalcdoi($item_id, $item_no, $selfdoi, $changeMode);
                }
            }
            else if($ra === strtoupper(RepositoryConst::JUNII2_SELFDOI_RA_CROSSREF))
            {
                $checkRegist = $this->entryCrossrefdoi($item_id, $item_no, $selfdoi, $changeMode);
            }
            // Add DataCite 2015/02/09 K.Sugimoto --start--
            else if($ra === strtoupper(RepositoryConst::JUNII2_SELFDOI_RA_DATACITE))
            {
                $checkRegist = $this->entryDatacitedoi($item_id, $item_no, $selfdoi, $changeMode);
            }
        // Add DataCite 2015/02/09 K.Sugimoto --end--
        }
        
        return $checkRegist;
    }

    /**
     * Entry data of JaLC DOI
     * JaLC DOIのデータを登録する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテムNo
     * @param string $jalcdoi Value of JaLC DOI JaLC DOIの値
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode) 
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @return Object Structure of result of check DOI grant
     *                DOI付与チェック結果の構造体
     *
     */
    private function entryJalcdoi($item_id, $item_no, $jalcdoi, $changeMode)
    {
        $checkdoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
        $jalcdoiPrefix = $this->getJalcDoiPrefix();
        $jalcdoiSuffix = $jalcdoi;
        // 抽出できた場合は抽出結果をsuffixとして扱う
        // 抽出できなかった場合は元の値をsuffixとして扱う
        $isExtract = $this->extractSuffix($jalcdoiSuffix, $jalcdoiPrefix);
        if(strlen($jalcdoiSuffix) == 0 && $changeMode == Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_DOI_CHANGE)
        {
            $jalcdoiSuffix = $this->getYHandleSuffix($item_id, $item_no);
        }
        $checkRegist = $checkdoi->checkDoiGrant($item_id, 
                                                $item_no, 
                                                Repository_Components_Business_Doi_Checkdoi::TYPE_JALC_DOI, 
                                                $jalcdoiSuffix, 
                                                Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_IMPORT_SWORD, 
                                                $changeMode);
        if($checkRegist->isGrantDoi)
        {
            if($changeMode == Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_DOI_CHANGE)
            {
                $this->registJalcdoiSuffix($item_id, $item_no, $jalcdoiSuffix, $changeMode);
            }
            else if(defined("_REPOSITORY_WEKO_DOISUFFIX_FREE") && _REPOSITORY_WEKO_DOISUFFIX_FREE)
            {
                if(isset($jalcdoiSuffix) && strlen($jalcdoiSuffix) > 0)
                {
                    $this->registJalcdoiSuffix($item_id, $item_no, $jalcdoiSuffix, $changeMode);
                }
            }
            else
            {
                $suffix = $this->getYHandleSuffix($item_id, $item_no);
                $this->registJalcdoiSuffix($item_id, $item_no, $suffix, $changeMode);
            }
        }
        
        return $checkRegist;
    }
    
    /**
     * Entry data of CrossRef DOI
     * CrossRef DOIのデータを登録する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテムNo
     * @param string $crossref Value of CrossRef DOI CrossRef DOIの値
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode) 
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @return Object Structure of result of check DOI grant
     *                DOI付与チェック結果の構造体
     *
     */
    private function entryCrossrefdoi($item_id, $item_no, $crossref, $changeMode)
    {
        $checkdoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
        $crossrefPrefix = $this->getCrossRefPrefix();
        $crossrefSuffix = $crossref;
        // 抽出できた場合は抽出結果をsuffixとして扱う
        // 抽出できなかった場合は元の値をsuffixとして扱う
        $isExtract = $this->extractSuffix($crossrefSuffix, $crossrefPrefix);
        if(strlen($crossrefSuffix) == 0 && $changeMode == Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_DOI_CHANGE)
        {
            $crossrefSuffix = $this->getYHandleSuffix($item_id, $item_no);
        }
        $checkRegist = $checkdoi->checkDoiGrant($item_id, 
                                                $item_no, 
                                                Repository_Components_Business_Doi_Checkdoi::TYPE_CROSS_REF, 
                                                $crossrefSuffix, 
                                                Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_IMPORT_SWORD, 
                                                $changeMode);
        if($checkRegist->isGrantDoi)
        {
            if($changeMode == Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_DOI_CHANGE)
            {
                $this->registCrossrefSuffix($item_id, $item_no, $crossrefSuffix, $changeMode);
            }
            else if(defined("_REPOSITORY_WEKO_DOISUFFIX_FREE") && _REPOSITORY_WEKO_DOISUFFIX_FREE)
            {
                if(isset($crossrefSuffix) && strlen($crossrefSuffix) > 0)
                {
                    $this->registCrossrefSuffix($item_id, $item_no, $crossrefSuffix, $changeMode);
                }
            }
            else
            {
                $suffix = $this->getYHandleSuffix($item_id, $item_no);
                $this->registCrossrefSuffix($item_id, $item_no, $suffix, $changeMode);
            }
        }
        
        return $checkRegist;
    }
    
    /**
     * Entry data of DataCite DOI
     * DataCite DOIのデータを登録する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテムNo
     * @param string $datacite Value of DataCite DOI DataCite DOIの値
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode) 
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @return Object Structure of result of check DOI grant
     *                DOI付与チェック結果の構造体
     *
     */
    private function entryDatacitedoi($item_id, $item_no, $datacite, $changeMode)
    {
        $checkdoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
        $datacitePrefix = $this->getDataCitePrefix();
        $dataciteSuffix = $datacite;
        // 抽出できた場合は抽出結果をsuffixとして扱う
        // 抽出できなかった場合は元の値をsuffixとして扱う
        $isExtract = $this->extractSuffix($dataciteSuffix, $datacitePrefix);
        if(strlen($dataciteSuffix) == 0 && $changeMode == Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_DOI_CHANGE)
        {
            $dataciteSuffix = $this->getYHandleSuffix($item_id, $item_no);
        }
        $checkRegist = $checkdoi->checkDoiGrant($item_id, 
                                                $item_no, 
                                                Repository_Components_Business_Doi_Checkdoi::TYPE_DATACITE, 
                                                $dataciteSuffix, 
                                                Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_IMPORT_SWORD, 
                                                $changeMode);
        if($checkRegist->isGrantDoi)
        {
            if($changeMode == Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_DOI_CHANGE)
            {
                $this->registDataciteSuffix($item_id, $item_no, $dataciteSuffix, $changeMode);
            }
            else if(defined("_REPOSITORY_WEKO_DOISUFFIX_FREE") && _REPOSITORY_WEKO_DOISUFFIX_FREE)
            {
                if(isset($dataciteSuffix) && strlen($dataciteSuffix) > 0)
                {
                    $this->registDataciteSuffix($item_id, $item_no, $dataciteSuffix, $changeMode);
                }
            }
            else
            {
                $suffix = $this->getYHandleSuffix($item_id, $item_no);
                $this->registDataciteSuffix($item_id, $item_no, $suffix, $changeMode);
            }
        }
        
        return $checkRegist;
    }
    
    /**
     * Extract suffix by permalink
     * Permalinkからsuffixを抽出する
     *
     * @param string $uri Permalink パーマリンク
     * @param string $prefix Prefix of selfDOI selfDOIのプレフィックス
     *
     * @return boolean Whether or not be able to extract 抽出できたか
     */
    private function extractSuffix(&$uri, $prefix){
        $matches = array();
        $isExtract = false;
        
        // prefixが存在しない場合は抽出しない
        if(!isset($prefix) || strlen($prefix) === 0){
            return false;
        }
        
        // 下記形式のURIから[suffix]を抽出する
        // DOIの入力形式は下記の通り
        //   - http://doi.org/[prefix]/[suffix]
        //   - http://dx.doi.org/[prefix]/[suffix]
        //   - doi:[prefix]/[suffix]
        //   - info:doi/[prefix]/[suffix]
        //   - [prefix]/[suffix]
        if(preg_match("/^". preg_quote(self::PREFIX_SELF_DOI. $prefix, "/"). "\/(.*)$/", $uri, $matches) === 1
            || preg_match("/^". preg_quote(self::PREFIX_LIBRARY_DOI_HTTP_DX. $prefix, "/"). "\/(.*)$/", $uri, $matches) === 1
            || preg_match("/^". preg_quote(self::PREFIX_LIBRARY_DOI_HTTP. $prefix, "/"). "\/(.*)$/", $uri, $matches) === 1
            || preg_match("/^". preg_quote(self::PREFIX_LIBRARY_DOI_DOI. $prefix, "/"). "\/(.*)$/", $uri, $matches) === 1
            || preg_match("/^". preg_quote($prefix, "/"). "\/(.*)$/", $uri, $matches) === 1)
        {
            $uri = $matches[1];
            $isExtract = true;
        }
        
        return $isExtract;
    }
    
    /**
     * Check suffix having prefix, and having prefix extract suffix
     * suffixにprefixが存在するかチェックし、あればsuffixのみを抽出する
     * 
     * @param string $suffix DOI suffix DOI suffix
     * @param int $suffixType DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                        DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @return boolean Suffix after check チェック後のsuffix
     */
    public function checkAndExtractSuffix($suffix, $suffixType){
        switch($suffixType)
        {
            case Repository_Components_Business_Doi_Checkdoi::TYPE_LIBRARY_JALC_DOI:
                $libraryJalcdoiPrefix = $this->getLibraryJalcDoiPrefix();
                // 抽出できた場合は抽出結果をsuffixとして扱う
                // 抽出できなかった場合は元の値をsuffixとして扱う
                $isExtract = $this->extractSuffix($suffix, $libraryJalcdoiPrefix);
                if(!$isExtract)
                {
                    $suffix = "";
                }
                break;
            case Repository_Components_Business_Doi_Checkdoi::TYPE_JALC_DOI:
            case Repository_Components_Business_Doi_Checkdoi::TYPE_CROSS_REF:
            case Repository_Components_Business_Doi_Checkdoi::TYPE_DATACITE:
                break;
            default:
                $suffix = "";
                break;
        }
        
        return $suffix;
    }
    
    /**
     * check Item entried DOI
     * DOI付与済みアイテムに対してチェックを行う
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテムNo
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD) 
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode) 
     *                        登録のモード(0:通常モード, 1:DOI変更モード)
     * @return Object Structure of result of check DOI grant
     *                DOI付与チェック結果の構造体
     */
    public function checkItemEntriedDoi($item_id, $item_no, $status, $changeMode)
    {
        $CheckDoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
        $libraryJalcSuffix = $this->getLibraryJalcdoiSuffix($item_id, $item_no);
        $jalcSuffix = $this->getJalcdoiSuffix($item_id, $item_no);
        $crossrefSuffix = $this->getCrossrefSuffix($item_id, $item_no);
        $dataciteSuffix = $this->getDataciteSuffix($item_id, $item_no);
        if(isset($jalcSuffix) && strlen($jalcSuffix) > 0)
        {
            $resultCheckDoi = $CheckDoi->checkDoiGrant($item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_JALC_DOI, null, $status, $changeMode);
        }
        else if(isset($crossrefSuffix) && strlen($crossrefSuffix) > 0)
        {
            $resultCheckDoi = $CheckDoi->checkDoiGrant($item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_CROSS_REF, null, $status, $changeMode);
        }
        else if(isset($libraryJalcSuffix) && strlen($libraryJalcSuffix) > 0)
        {
            $resultCheckDoi = $CheckDoi->checkDoiGrant($item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_LIBRARY_JALC_DOI, null, $status, $changeMode);
        }
        else if(isset($dataciteSuffix) && strlen($dataciteSuffix) > 0)
        {
            $resultCheckDoi = $CheckDoi->checkDoiGrant($item_id, $item_no, Repository_Components_Business_Doi_Checkdoi::TYPE_DATACITE, null, $status, $changeMode);
        }
        // DOI付与済みアイテムにDOIを付与することはないため、必ずtrue
        if(isset($resultCheckDoi->isNotAlreadyGrantDoi) && !$resultCheckDoi->isNotAlreadyGrantDoi)
        {
            $resultCheckDoi->isNotAlreadyGrantDoi = true;
            
            // アイテム関連のチェックが全てtrueであれば、DOI付与チェックもtrue
            if(isset($resultCheckDoi->isPublicItem) && $resultCheckDoi->isPublicItem
               && isset($resultCheckDoi->isSetItemTypeMapping) && $resultCheckDoi->isSetItemTypeMapping
               && isset($resultCheckDoi->isSetMetadata) && $resultCheckDoi->isSetMetadata)
            {
                $resultCheckDoi->isGrantDoi = true;
            }
        }
        return $resultCheckDoi;
    }
}
?>
