<?php

/**
 * Action base class for WEKO
 * WEKO用アクション基底クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: WekoAction.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Action base class for NetCommons
 * NetCommons用アクション基底クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/ActionBase.class.php';

/**
 * Action base class for WEKO
 * WEKO用アクション基底クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
abstract class WekoAction extends ActionBase
{
    /**
     * Block ID of arranged WEKO to NC2
     * NC2に配置されたWEKOのブロックID
     *
     * @var int
     */
    public $block_id = null;
    /**
     * Administrator-based authority level
     * 管理者ベース権限レベル
     *
     * @var int
     */
    public $repository_admin_base;
    /**
     * Administrator Room authority level
     * 管理者ルーム権限レベル
     *
     * @var int
     */
    public $repository_admin_room;
    /**
     * Theme name of WEKO module
     * WEKOモジュールのテーマ名
     *
     * @var string
     */
    public $wekoThemeName = 'default';
    
    /**
     * Whether or not it is being accessed from a smartphone
     * スマートフォンからアクセスされているか否か
     *
     * @var boolean
     */
    protected $smartphoneFlg = false;
    
    /**
     * Processing start operation log number
     * 処理開始操作ログ番号
     * @var int
     */
    private $startLogId = 0;
    
    /**
     * Outside a transaction before processing
     * トランザクション外前処理
     */
    protected function beforeTrans(){
        try {
            // 操作ログ記録
            $userId = $this->Session->getParameter("_user_id");
            $request = BusinessFactory::getFactory()->getBusiness("Request");
            $requestPrams = $request->getStrParameters();
            $businessOperationlog = BusinessFactory::getFactory()->getBusiness("businessOperationlog");
            $this->startLogId = $businessOperationlog->startLog($userId, $requestPrams);
        } catch (Exception $e){}
    }
    
    /**
     * Transaction outside the post-processing
     * トランザクション外後処理
     */
    protected function afterTrans(){
        try {
            // 操作ログ記録
            $userId = $this->Session->getParameter("_user_id");
            $request = BusinessFactory::getFactory()->getBusiness("Request");
            $requestPrams = $request->getStrParameters();
            $businessOperationlog = BusinessFactory::getFactory()->getBusiness("businessOperationlog");
            $businessOperationlog->endLog($this->startLogId, $userId, $requestPrams);
        } catch (Exception $e){}
    }
    
    /**
     * Transaction in the pre-treatment
     * トランザクション内前処理
     */
    protected function preExecute(){
        $this->setConfigAuthority();
        $this->setThemeName();
        
        require_once WEBAPP_DIR. '/modules/repository/components/RepositoryIndexManager.class.php';
        $repositoryIndexManager = new RepositoryIndexManager($this->Session, $this->Db, $this->accessDate);
        $repositoryIndexManager->createPrivateTree();
        
        if(!defined("SHIB_ENABLED")){
            define("SHIB_ENABLED", 0);
        }
        $this->shib_login_flg = SHIB_ENABLED;
        
        if(defined("_REPOSITORY_CINII"))
        {
            $this->Session->setParameter("_repository_cinii", _REPOSITORY_CINII);
        }
        
        if(isset($_SERVER['HTTP_USER_AGENT']))
        {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            if(preg_match('/Android..*Mobile|iPhone|IEMobile/', $userAgent) > 0){
                $this->smartphoneFlg = true;
            }
        }
        if(_REPOSITORY_SMART_PHONE_DISPLAY)
        {
            $this->smartphoneFlg = true;
        }
    }
    
    /**
     * Transaction commit processing
     * トランザクションコミット処理
     */
    protected function completeTrans(){
        // ファイル操作の終了処理を行う
        $contentFileTransaction = null;
        $bizFactory = BusinessFactory::getFactory();
        if(isset($bizFactory))
        {
            $contentFileTransaction = $bizFactory->getBusiness("businessContentfiletransaction");
        }
        
        if(!is_null($contentFileTransaction))
        {
            // ビジネスロジックの判定でNULLチェックを行ってはいけない。
            // ここでは、DIconファイルにファイル操作を行う宣言がない場合、あえてファイル操作を行う処理は行わない
            $contentFileTransaction->finishFileOperation();
        }
        
        // データベースのコミット処理
        try {
            parent::completeTrans();
        } catch(AppException $ex){
            $exception = new AppException("Failed commit trance.", 4, $ex);
            $exception->addError("repository_error_failed_db_commit");
            throw $exception;
        }
        
        // ファイル操作をコミットする
        if(!is_null($contentFileTransaction))
        {
            // ビジネスロジックの判定でNULLチェックを行ってはいけない。
            // ここでは、DIconファイルにファイル操作を行う宣言がない場合、あえてファイル操作を行う処理は行わない
            $contentFileTransaction->commit();
        }
    }
    
    /**
     * The setting of privilege level regarded as administrator
     * 管理者と見なす権限レベルの設定を行う
     */
    private function setConfigAuthority(){
        // set authority level from config file
        $config = parse_ini_file(BASE_DIR.'/webapp/modules/repository/config/main.ini');
        if( isset($config["define:_REPOSITORY_BASE_AUTH"]) &&
            strlen($config["define:_REPOSITORY_BASE_AUTH"]) > 0 &&
            is_numeric($config["define:_REPOSITORY_BASE_AUTH"])){
                $this->repository_admin_base = intval($config["define:_REPOSITORY_BASE_AUTH"]);
        } else {
            $this->repository_admin_base = _AUTH_CHIEF;
        }
        
        if( isset($config["define:_REPOSITORY_ROOM_AUTH"]) &&
            strlen($config["define:_REPOSITORY_ROOM_AUTH"]) > 0 &&
            is_numeric($config["define:_REPOSITORY_ROOM_AUTH"])){
                $this->repository_admin_room = $config["define:_REPOSITORY_ROOM_AUTH"];
        } else {
            $this->repository_admin_room = _AUTH_CHIEF;
        }
        
        // check authority level
        if($this->repository_admin_base < _AUTH_CHIEF){
            $this->repository_admin_base = _AUTH_CHIEF;
        } else if(_AUTH_ADMIN < $this->repository_admin_base){
            $this->repository_admin_base = _AUTH_ADMIN;
        }
        
        if($this->repository_admin_room < _AUTH_GUEST){
            $this->repository_admin_room = _AUTH_GUEST;
        } else if(_AUTH_CHIEF < $this->repository_admin_base){
            $this->repository_admin_room = _AUTH_CHIEF;
        }
    }
    
    /**
     * To set the themes that have been used in the WEKO module
     * WEKOモジュールで使われているテーマの設定を行う
     */
    private function setThemeName(){
        $getdata = BusinessFactory::getFactory()->getBusiness("GetData");
        $blocks =& $getdata->getParameter("blocks");
        
        // when weko module uninstall, $blocks==false.
        if(!isset($blocks) || !is_array($blocks) || !$blocks[$this->block_id])
        {
            $this->wekoThemeName = 'default';
            return;
        }
        $block_obj = $blocks[$this->block_id];
        $themeName = $block_obj['theme_name'];
        if(strlen($themeName) == 0){
            $pages =& $getdata->getParameter("pages");
            $themeList = $this->Session->getParameter("_theme_list");
            $themeName = "default";
            if(isset($pages[$block_obj['page_id']]) && isset($themeList[$pages[$block_obj['page_id']]['display_position']])){
                $themeName = $themeList[$pages[$block_obj['page_id']]['display_position']];
            }
        }
        if(is_numeric(strpos($themeName, 'blue'))){
            $this->wekoThemeName = 'blue';
        } else if(is_numeric(strpos($themeName, 'green'))){
            $this->wekoThemeName = 'green';
        } else if(is_numeric(strpos($themeName, 'orange'))){
            $this->wekoThemeName = 'orange';
        } else if(is_numeric(strpos($themeName, 'orange2'))){
            $this->wekoThemeName = 'orange';
        } else if(is_numeric(strpos($themeName, 'red'))){
            $this->wekoThemeName = 'red';
        } else if(is_numeric(strpos($themeName, 'red2'))){
            $this->wekoThemeName = 'red';
        } else if(is_numeric(strpos($themeName, 'pink'))){
            $this->wekoThemeName = 'pink';
        } else if(is_numeric(strpos($themeName, 'pink2'))){
            $this->wekoThemeName = 'pink';
        } else {
            $this->wekoThemeName = 'default';
        }
    }
}
?>