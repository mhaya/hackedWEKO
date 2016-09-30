<?php

/**
 * Item editing screen transition check business class
 * アイテム編集画面遷移チェックビジネスクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BusinessBase.class.php 68427 2016-06-03 08:40:01Z tomohiro_ichikawa $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';
/**
 * Check grant doi class
 * DOI付与チェックビジネスクラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/doi/Checkdoi.class.php';
/**
 * Handle management common classes
 * ハンドル管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';

/**
 * Item editing screen transition check business class
 * アイテム編集画面遷移チェックビジネスクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Itemedittranscheck extends BusinessBase
{
    // 遷移先定義文字列
    /**
     * Transition destination definition string to the error screen
     * エラー画面への遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_ERROR         = "error";
    /**
     * Item type transition destination definition string to the selection screen
     * アイテムタイプ選択画面への遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_SELECTTYPE    = "selecttype";
    /**
     * Transition destination definition string to the file selection screen
     * ファイル選択画面への遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_FILES         = "files";
    /**
     * Transition destination definition string to the license configuration screen
     * ライセンス設定画面への遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_LICENSE       = "license";
    /**
     * Transition destination definition string to the meta-data input screen
     * メタデータ入力画面への遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_TEXTS         = "texts";
    /**
     * Transition destination definition string to the link setting screen
     * リンク設定画面への遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_LINKS         = "links";
    /**
     * Transition destination definition string to the DOI grant screen
     * DOI付与画面への遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_DOI           = "doi";
    /**
     * Transition destination definition string to the confirmation screen
     * 確認画面への遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_CONFIRM       = "confirm";
    
    /**
     * Transition destination definition string of redirect
     * リダイレクトの遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_REDIRECT      = "redirect";
    
    /**
     * Transition destination definition string of temporary storage at the time
     * 一時保存時の遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_STAY          = "stay";
    
    /**
     * Transition destination definition string to the next screen
     * 次画面への遷移先定義文字列
     * 
     * @var string
     */
    const DISTINATION_NEXT          = "next";
    
    // メンバ変数
    /**
     * Invoker
     * 呼び出し元
     *
     * @var string
     */
    private $caller_ = "";
    /**
     * Hope the transition destination
     * 希望遷移先
     *
     * @var string
     */
    private $target_ = "";
    
    /**
     * File selection screen transition flag
     * ファイル選択画面遷移可能フラグ
     *
     * @var int
     */
    private $isFile_ = 0;
    /**
     * DOI grant selection screen transition flag
     * DOI付与選択画面遷移可能フラグ
     *
     * @var boolean
     */
    private $doiItemTypeFlag_ = false;
    
    /**
     * Item id
     * アイテムID
     *
     * @var int
     */
    private $itemId_ = 0;
    /**
     * Item serial number
     * アイテム通番
     *
     * @var int
     */
    private $itemNo_ = 0;
    /**
     * Item basic information
     * アイテム基本情報
     *
     * @var array["title"|"title_english"|"language"]
     */
    private $baseAttr_ = array();
    /**
     * Items Published
     * アイテム公開日
     *
     * @var array["year"|"month"|"day"]
     */
    private $itemPubDate_ = array();
    /**
     * Item attribute type information
     * アイテム属性タイプ情報
     *
     * @var array[$ii]["input_type"|"is_required"|"attribute_name"]
     */
    private $itemAttrType_ = array();
    /**
     * Item attribute information(The contents of the third element is different by the input type)
     * アイテム属性情報(入力タイプにより第3要素の内容が異なる)
     *
     * @var array[$ii][$jj]["date"]
     *           [$ii][$jj]["biblio_name"|"biblio_name_english"|"volume"|"issue"|"spage"|"epage"|"date_of_issued"]
     *           [$ii][$jj]["family"]
     *           [$ii][$jj]["embargo_flag"|"embargo_year"|"embargo_month"|"embargo_day"|"room_id"|"price_value"|"price_num"]
     *           [$ii][$jj]["upload"]["file_name"]
     *           [$ii][$jj]
     */
    private $itemAttr_ = array();
    /**
     * Item number of attributes information
     * アイテム属性数情報
     *
     * @var array[$ii]
     */
    private $itemNumAttr_ = array();
    /**
     * Affiliation index information
     * 所属インデックス情報
     *
     * @var array[$ii]["index_id"]
     */
    private $index_ = array();
    /**
     * The original hope transition destination
     * 元の希望遷移先
     *
     * @var string
     */
    private $orgTarget = "";
    
    // チェック結果
    /**
     * Essential condition judgment result (file, thumbnail, accounting file)
     * 必須条件判定結果(ファイル、サムネイル、課金ファイル)
     *
     * @var boolean
     */
    private $checkFile_ = null;
    /**
     * Prerequisite input determination result (file, thumbnail, accounting file)
     * 必須条件入力判定結果(ファイル、サムネイル、課金ファイル)
     *
     * @var boolean
     */
    private $checkFileInput_ = null;
    /**
     * Essential condition judgment result (file license)
     * 必須条件判定結果(ファイルライセンス)
     *
     * @var boolean
     */
    private $checkFileLicense_ = null;
    /**
     * Prerequisite input determination result (item basic information)
     * 必須条件入力判定結果(アイテム基本情報)
     *
     * @var boolean
     */
    private $checkBaseInfo_ = null;
    /**
     * Prerequisite input determination result (Required metadata)
     * 必須条件入力判定結果(必須メタデータ)
     *
     * @var boolean
     */
    private $checkRequired_ = null;
    /**
     * Prerequisite input determination result (index)
     * 必須条件入力判定結果(インデックス)
     *
     * @var boolean
     */
    private $checkIndex_ = null;
    /**
     * DOI grant decision
     * DOI付与判定
     *
     * @var boolean
     */
    private $checkDoi_ = null;
    /**
     * Prerequisite input determination result (item granted DOI)
     * 必須条件入力判定結果(DOI付与済みアイテム)
     *
     * @var boolean
     */
    private $checkItemGrantedDoi_ = null;
    /**
     * Prerequisite input determination result (DOI input value)
     * 必須条件入力判定結果(DOIの入力値)
     *
     * @var boolean
     */
    private $checkDoiInput_ = null;
    
    // メッセージ
    /**
     * Error message
     * エラーメッセージ
     *
     * @var array[$ii]
     */
    private $errMsg_ = array();
    /**
     * Warning message
     * 警告メッセージ
     *
     * @var array[$ii]
     */
    private $warningMsg_ = array();
    
    /**
     * Item information setting
     * アイテム情報設定
     * 
     * @param string $caller Invoker 呼び出し元
     * @param string $target Hope the transition destination 希望遷移先
     * @param int $isFile File selection screen transition flag ファイル選択画面遷移可能フラグ
     * @param bool $doiItemtypeFlag DOI grant selection screen transition flag DOI付与選択画面遷移可能フラグ
     * @param array $baseAttr Item basic information アイテム基本情報
     *                        array["title"|"title_english"|"language"]
     * @param array $itemPubDate Items Published アイテム公開日
     *                           array["year"|"month"|"day"]
     * @param array $itemAttrType Item attribute type information アイテム属性タイプ情報
     *                            array[$ii]["input_type"|"is_required"|"attribute_name"]
     * @param array $itemAttr Item attribute information(The contents of the third element is different by the input type) アイテム属性情報(入力タイプにより第3要素の内容が異なる)
     *                             array[$ii][$jj]["date"]
     *                             array[$ii][$jj]["biblio_name"|"biblio_name_english"|"volume"|"issue"|"spage"|"epage"|"date_of_issued"]
     *                             array[$ii][$jj]["embargo_flag"|"embargo_year"|"embargo_month"|"embargo_day"|"room_id"|"price_value"|"price_num"]
     *                             array[$ii][$jj]["upload"]["file_name"]
     *                             array[$ii][$jj]["family"]
     *                             array[$ii][$jj]
     * @param array $itemNumAttr Item number of attributes information アイテム属性数情報
     *                           array[$ii]
     * @param array $index Affiliation index information 所属インデックス情報
     * @param int $editItemId Item id アイテムID
     * @param int $editItemNo Item serial number アイテムNo
     */
    public function setData($caller, $target, $isFile, $doiItemtypeFlag, $baseAttr, $itemPubDate,
                            $itemAttrType, $itemAttr, $itemNumAttr, $index, $editItemId=0, $editItemNo=0)
    {
        $this->caller_ = $caller;
        $this->isFile_ = intval($isFile);
        $this->doiItemTypeFlag_ = $doiItemtypeFlag;
        $this->itemId_ = $editItemId;
        $this->itemNo_ = $editItemNo;
        $this->baseAttr_ = $baseAttr;
        $this->itemPubDate_ = $itemPubDate;
        $this->itemAttrType_ = $itemAttrType;
        $this->itemAttr_ = $itemAttr;
        $this->itemNumAttr_ = $itemNumAttr;
        $this->index_ = $index;
        $this->orgTarget = $target;
        $this->setTarget($caller, $target);
    }

    /**
     * Transition destination acquisition
     * 遷移先取得
     * 
     * @return string
     */
    public function getDestination()
    {
        return $this->judgeDestination();
    }
    
    /**
     * Error message retrieval
     * エラーメッセージ取得
     * 
     * @return array[$ii]
     */
    public function getErrorMsg(){
        return $this->errMsg_;
    }
    
    /**
     * Warning message retrieval
     * 警告メッセージ取得
     * 
     * @return array[$ii]
     */
    public function getWarningMsg(){
        return $this->warningMsg_;
    }
    
    /**
     * Specified transition destination setting
     * 指定遷移先設定
     *
     * @param string $caller Invoker 呼び出し元
     * @param string $target Hope the transition destination 希望遷移先
     */
    private function setTarget($caller, $target)
    {
        if($target==self::DISTINATION_STAY){
            // 呼び出し元と同一画面を指定
            $this->target_ = $caller;
        } else if($target==self::DISTINATION_NEXT){
            // 呼び出し元の次の画面を指定
            switch($caller){
                case self::DISTINATION_CONFIRM:
                    $this->target_ = self::DISTINATION_REDIRECT;
                    break;
                case self::DISTINATION_DOI:
                    $this->target_ = self::DISTINATION_CONFIRM;
                    break;
                case self::DISTINATION_LINKS:
                    if($this->getCheckDoi()){
                        $this->target_ = self::DISTINATION_DOI;
                    } else {
                        $this->target_ = self::DISTINATION_CONFIRM;
                    }
                    break;
                case self::DISTINATION_TEXTS:
                    $this->target_ = self::DISTINATION_LINKS;
                    break;
                case self::DISTINATION_LICENSE:
                    $this->target_ = self::DISTINATION_TEXTS;
                    break;
                case self::DISTINATION_FILES:
                    $this->target_ = self::DISTINATION_LICENSE;
                    // サムネイルのみしか存在しない場合はTEXTへ
                    if($this->isFile_ == 1){
                        $this->target_ = self::DISTINATION_TEXTS;
                    }
                    break;
                case self::DISTINATION_SELECTTYPE:
                    if($this->getCheckFile()){
                        $this->target_ = self::DISTINATION_FILES;
                    } else {
                        $this->target_ = self::DISTINATION_TEXTS;
                    }
                    break;
                default:
                    $this->target_ = $caller;
                    break;
            }
        } else {
            // 指定された遷移先のまま
            $this->target_ = $target;
        }
    }
    
    /**
     * Transition destination determination
     * 遷移先判定
     *
     * @return string
     */
    private function judgeDestination()
    {
        $ret = self::DISTINATION_ERROR;
        switch($this->target_){
            case self::DISTINATION_REDIRECT:
                // サムネイル or ファイル or 課金ファイルが必須の場合：ファイルがなければ遷移不可
                // サムネイル or ファイル or 課金ファイルが必須の場合：正しくライセンス設定されていない場合は不可
                // アイテム基本情報（タイトル、公開日）がなければ遷移不可
                // メタデータ必須の場合：該当メタデータがなければ遷移不可
                // 所属インデックスがなければ遷移不可
                // DOI付与済みアイテムに対してはDOIの条件を満たしていなければ遷移不可
                if( $this->getCheckFileInput() && $this->getCheckFileLicense() && $this->getCheckBaseInfo() &&
                    $this->getCheckRequired() && $this->getCheckIndex() && $this->getCheckItemGrantedDoi())
                {
                    $ret = $this->target_;
                }
                break;
            case self::DISTINATION_CONFIRM:
                // サムネイル or ファイル or 課金ファイルが必須の場合：ファイルがなければ遷移不可
                // サムネイル or ファイル or 課金ファイルが必須の場合：正しくライセンス設定されていない場合は不可
                // アイテム基本情報（タイトル、公開日）がなければ遷移不可
                // メタデータ必須の場合：該当メタデータがなければ遷移不可
                // 所属インデックスがなければ遷移不可
                // DOI入力値が条件を満たしていなければ遷移不可
                if( $this->getCheckFileInput() && $this->getCheckFileLicense() && $this->getCheckBaseInfo() &&
                    $this->getCheckRequired() && $this->getCheckIndex() && $this->getCheckDoiInput())
                {
                    // DOI付与済みアイテムに対してのDOI条件チェックは、遷移条件に含めない
                    // エラーメッセージ、警告メッセージは詰める
                    $this->getCheckItemGrantedDoi();
                    $ret = self::DISTINATION_CONFIRM;
                }
                break;
            case self::DISTINATION_DOI:
                // サムネイル or ファイル or 課金ファイルが必須の場合：ファイルがなければ遷移不可
                // サムネイル or ファイル or 課金ファイルが必須の場合：正しくライセンス設定されていない場合は不可
                // アイテム基本情報（タイトル、公開日）がなければ遷移不可
                // メタデータ必須の場合：該当メタデータがなければ遷移不可
                // 所属インデックスがなければ遷移不可
                // DOIフラグがない場合は遷移不可
                // DOI入力値が条件を満たしていなければ遷移不可
                if( $this->getCheckFileInput() && $this->getCheckFileLicense() && $this->getCheckBaseInfo() &&
                    $this->getCheckRequired() && $this->getCheckIndex() && $this->getCheckDoi() && $this->getCheckDoiInput())
                {
                    $ret = self::DISTINATION_DOI;
                }
                break;
            case self::DISTINATION_LINKS:
                // サムネイル or ファイル or 課金ファイルが必須の場合：ファイルがなければ遷移不可
                // サムネイル or ファイル or 課金ファイルが必須の場合：正しくライセンス設定されていない場合は不可
                // アイテム基本情報（タイトル、公開日）がなければ遷移不可
                // メタデータ必須の場合：該当メタデータがなければ遷移不可
                if( $this->getCheckFileInput() && $this->getCheckFileLicense() && $this->getCheckBaseInfo() && 
                    $this->getCheckRequired())
                {
                    $ret = self::DISTINATION_LINKS;
                }
                break;
            case self::DISTINATION_TEXTS:
                // サムネイル or ファイル or 課金ファイルが必須の場合：ファイルがなければ遷移不可
                // サムネイル or ファイル or 課金ファイルが必須の場合：正しくライセンス設定されていない場合は不可
                if( $this->getCheckFileInput() && $this->getCheckFileLicense())
                {
                    $ret = self::DISTINATION_TEXTS;
                    if($this->orgTarget == self::DISTINATION_STAY){
                        // 保存ボタン押下時にエラー/警告があれば出す
                        $this->getCheckBaseInfo();
                    }
                }
                break;
            case self::DISTINATION_LICENSE:
                // サムネイル or ファイル or 課金ファイルのメタデータ項目がなければ遷移不可
                // サムネイル or ファイル or 課金ファイルが必須の場合：ファイルがなければ遷移不可
                if( $this->getCheckFile() && $this->getCheckFileInput())
                {
                    $ret = self::DISTINATION_LICENSE;
                }
                break;
            case self::DISTINATION_FILES:
                // サムネイル or ファイル or 課金ファイルのメタデータ項目がなければ遷移不可
                if( $this->getCheckFile())
                {
                    $ret = self::DISTINATION_FILES;
                }
                break;
            case self::DISTINATION_SELECTTYPE:
                $ret = self::DISTINATION_SELECTTYPE;
                // 条件なし
                break;
            default:
                $ret = self::DISTINATION_ERROR;
                break;
        }
        return $ret;
    }
    
    /**
     * Initializing process
     * 初期化処理
     */
    protected function onInitialize(){
        $this->initCheckFlag();
    }
    
    /**
     * Prerequisite check flag initialization
     * 必須条件チェックフラグ初期化
     */
    private function initCheckFlag(){
        $this->checkFile_ = null;
        $this->checkFileInput_ = null;
        $this->checkBaseInfo_ = null;
        $this->checkRequired_ = null;
        $this->checkDoi_ = null;
        $this->checkIndex_ = null;
        $this->checkItemGrantedDoi_ = null;
        $this->checkDoiInput_ = null;
        
        // メッセージ初期化
        $this->errMsg_ = array();
        $this->warningMsg_ = array();
    }
    
    /**
     * Essential condition determination result acquisition: metadata item of thumbnail or file or accounting file
     * 必須条件判定結果取得：サムネイル or ファイル or 課金ファイルのメタデータ項目
     * 
     * @return boolean Essential condition determination result 必須条件判定結果
     */
    private function getCheckFile(){
        if(!isset($this->checkFile_)){
            $ret = false;
            if($this->isFile_ > 0){
                // サムネイル or ファイル or 課金ファイルのメタデータ項目が存在する
                $ret = true;
            }
            $this->checkFile_ = $ret;
            $this->debugLog("checkFile_: ".var_export($ret, true), __FILE__, __CLASS__, __LINE__);
        }
        return $this->checkFile_;
    }
    
    /**
     * Essential condition determination result acquisition: the input of the thumbnail or file or accounting file
     * 必須条件判定結果取得：サムネイル or ファイル or 課金ファイルの入力
     * 
     * @return boolean Essential condition determination result 必須条件判定結果
     */
    private function getCheckFileInput(){
        if(!isset($this->checkFileInput_)){
            $ret = false;
            if($this->getCheckFile()){
                // ItemRegister のチェック関数を使用
                require_once WEBAPP_DIR. '/modules/repository/components/ItemRegister.class.php';
                $this->debugLog("Get session", __FILE__, __CLASS__, __LINE__);
                $container = & DIContainerFactory::getContainer();
                $session = $container->getComponent("Session");
                $itemRegister = new ItemRegister($session, $this->Db);
                $itemRegister->setEditStartDate($this->accessDate);
                $tmpErrMsg = array();
                $tmpWarningMsg = array();
                $itemRegister->checkEntryInfo($this->itemAttrType_, $this->itemNumAttr_, $this->itemAttr_, "file", $tmpErrMsg, $tmpWarningMsg);
                $this->errMsg_ = array_merge($this->errMsg_, $tmpErrMsg);
                $this->warningMsg_ = array_merge($this->warningMsg_, $tmpWarningMsg);
                if(count($tmpErrMsg) == 0)
                {
                    $ret = true;
                }
            } else {
                // サムネイル or ファイル or 課金ファイルのメタデータ項目が存在しない場合はチェックOKとする
                $ret = true;
            }
            $this->checkFileInput_ = $ret;
            $this->debugLog("checkFileInput_: ".var_export($ret, true), __FILE__, __CLASS__, __LINE__);
        }
        return $this->checkFileInput_;
    }
    
    /**
     * Essential condition determination result acquisition: input file license
     * 必須条件判定結果取得：ファイルライセンスの入力
     * 
     * @return boolean Essential condition determination result 必須条件判定結果
     */
    private function getCheckFileLicense(){
        if(!isset($this->checkFileLicense_)){
            $ret = false;
            if($this->getCheckFile()){
                // ItemRegister のチェック関数を使用
                require_once WEBAPP_DIR. '/modules/repository/components/ItemRegister.class.php';
                $this->debugLog("Get session", __FILE__, __CLASS__, __LINE__);
                $container = & DIContainerFactory::getContainer();
                $session = $container->getComponent("Session");
                $itemRegister = new ItemRegister($session, $this->Db);
                $itemRegister->setEditStartDate($this->accessDate);
                $tmpErrMsg = array();
                $tmpWarningMsg = array();
                $itemRegister->checkEntryInfo($this->itemAttrType_, $this->itemNumAttr_, $this->itemAttr_, "license", $tmpErrMsg, $tmpWarningMsg);
                $this->errMsg_ = array_merge($this->errMsg_, $tmpErrMsg);
                $this->warningMsg_ = array_merge($this->warningMsg_, $tmpWarningMsg);
                if(count($tmpErrMsg) == 0)
                {
                    $ret = true;
                }
            } else {
                // サムネイル or ファイル or 課金ファイルのメタデータ項目が存在しない場合はチェックOKとする
                $ret = true;
            }
            $this->checkFileLicense_ = $ret;
            $this->debugLog("checkFileLicense_: ".var_export($ret, true), __FILE__, __CLASS__, __LINE__);
        }
        return $this->checkFileLicense_;
    }
    
    /**
     * Essential condition determination result acquisition: the input of the item basic information (title, publication date)
     * 必須条件判定結果取得：アイテム基本情報（タイトル、公開日）の入力
     * 
     * @return boolean Essential condition determination result 必須条件判定結果
     */
    private function getCheckBaseInfo(){
        if(!isset($this->checkBaseInfo_)){
            $ret = false;
            
            // ItemRegister のチェック関数を使用
            require_once WEBAPP_DIR. '/modules/repository/components/ItemRegister.class.php';
            $this->debugLog("Get session", __FILE__, __CLASS__, __LINE__);
            $container = & DIContainerFactory::getContainer();
            $session = $container->getComponent("Session");
            $itemRegister = new ItemRegister($session, $this->Db);
            $itemRegister->setEditStartDate($this->accessDate);
            $baseInfo = array(  "item_id" => $this->itemId_,
                                "item_no" => $this->itemNo_,
                                "title" => $this->baseAttr_['title'],
                                "title_english" => $this->baseAttr_['title_english'],
                                "language" => $this->baseAttr_['language'],
                                "pub_year" => $this->itemPubDate_['year'],
                                "pub_month" => $this->itemPubDate_['month'],
                                "pub_day" => $this->itemPubDate_['day']);
            $tmpErrMsg = array();
            $tmpWarningMsg = array();
            $itemRegister->checkBaseInfo($baseInfo, $tmpErrMsg, $tmpWarningMsg);
            $this->errMsg_ = array_merge($this->errMsg_, $tmpErrMsg);
            $this->warningMsg_ = array_merge($this->warningMsg_, $tmpWarningMsg);
            if(count($tmpErrMsg) == 0)
            {
                $ret = true;
            }
            $this->checkBaseInfo_ = $ret;
            $this->debugLog("checkBaseInfo_: ".var_export($ret, true), __FILE__, __CLASS__, __LINE__);
        }
        return $this->checkBaseInfo_;
    }
    
    /**
     * Essential condition determination result acquisition: Required input of metadata
     * 必須条件判定結果取得：必須メタデータの入力
     * 
     * @return boolean Essential condition determination result 必須条件判定結果
     */
    private function getCheckRequired(){
        if(!isset($this->checkRequired_)){
            $ret = false;
            
            // ItemRegister のチェック関数を使用
            require_once WEBAPP_DIR. '/modules/repository/components/ItemRegister.class.php';
            $this->debugLog("Get session", __FILE__, __CLASS__, __LINE__);
            $container = & DIContainerFactory::getContainer();
            $session = $container->getComponent("Session");
            $itemRegister = new ItemRegister($session, $this->Db);
            $itemRegister->setEditStartDate($this->accessDate);
            $tmpErrMsg = array();
            $tmpWarningMsg = array();
            $itemRegister->checkEntryInfo($this->itemAttrType_, $this->itemNumAttr_, $this->itemAttr_, "meta", $tmpErrMsg, $tmpWarningMsg);
            $this->errMsg_ = array_merge($this->errMsg_, $tmpErrMsg);
            $this->warningMsg_ = array_merge($this->warningMsg_, $tmpWarningMsg);
            if(count($tmpErrMsg) == 0)
            {
                $ret = true;
            }
            $this->checkRequired_ = $ret;
            $this->debugLog("checkRequired_: ".var_export($ret, true), __FILE__, __CLASS__, __LINE__);
        }
        return $this->checkRequired_;
    }
    
    /**
     * Essential condition determination result acquisition: Enabling DOI setting
     * 必須条件判定結果取得：DOI設定の有効化
     * 
     * @return boolean Essential condition determination result 必須条件判定結果
     */
    private function getCheckDoi(){
        if(!isset($this->checkDoi_)){
            // DOIアイテムタイプフラグで一次チェック
            $ret = false;
            if($this->doiItemTypeFlag_ === true){
                // Checkdoi クラスを使用
                require_once WEBAPP_DIR. '/modules/repository/components/business/doi/Checkdoi.class.php';
                // check this item can be granted doi
                $CheckDoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
                $displays_jalcdoi_flag = $CheckDoi->checkDoiGrant($this->itemId_, $this->itemNo_, Repository_Components_Business_Doi_Checkdoi::TYPE_JALC_DOI, null);
                $displays_crossref_flag = $CheckDoi->checkDoiGrant($this->itemId_, $this->itemNo_, Repository_Components_Business_Doi_Checkdoi::TYPE_CROSS_REF, null);
                $displays_library_jalcdoi_flag = $CheckDoi->checkDoiGrant($this->itemId_, $this->itemNo_, Repository_Components_Business_Doi_Checkdoi::TYPE_LIBRARY_JALC_DOI, null);
                $displays_datacite_flag = $CheckDoi->checkDoiGrant($this->itemId_, $this->itemNo_, Repository_Components_Business_Doi_Checkdoi::TYPE_DATACITE, null);
                // check this item was already granted doi or not
                $doi_status = $CheckDoi->getDoiStatus($this->itemId_, $this->itemNo_);
                if($displays_jalcdoi_flag->isGrantDoi || $displays_crossref_flag->isGrantDoi || $displays_library_jalcdoi_flag->isGrantDoi || $displays_datacite_flag->isGrantDoi || $doi_status >= 1)
                {
                    $ret = true;
                } else {
                    if($this->orgTarget !== self::DISTINATION_NEXT){
                        $ConvertResultCheckDoi = BusinessFactory::getFactory()->getBusiness("businessConvertresultcheckdoi");
                        if(!$displays_jalcdoi_flag->isGrantDoi)
                        {
                            $errMsg = $ConvertResultCheckDoi->chooseDoiErrMsg($this->itemId_, $this->itemNo_, $displays_jalcdoi_flag);
                        }
                        else if(!$displays_crossref_flag->isGrantDoi)
                        {
                            $errMsg = $ConvertResultCheckDoi->chooseDoiErrMsg($this->itemId_, $this->itemNo_, $displays_crossref_flag);
                        }
                        else if(!$displays_library_jalcdoi_flag->isGrantDoi)
                        {
                            $errMsg = $ConvertResultCheckDoi->chooseDoiErrMsg($this->itemId_, $this->itemNo_, $displays_library_jalcdoi_flag);
                        }
                        else if(!$displays_datacite_flag->isGrantDoi)
                        {
                            $errMsg = $ConvertResultCheckDoi->chooseDoiErrMsg($this->itemId_, $this->itemNo_, $displays_datacite_flag);
                        }
                        $this->errMsg_ = array_merge($this->errMsg_, $errMsg);
                    }
                }
            }
            $this->checkDoi_ = $ret;
            $this->debugLog("checkDoi_: ".var_export($ret, true), __FILE__, __CLASS__, __LINE__);
        }
        return $this->checkDoi_;
    }
    
    /**
     * Essential condition determination result acquisition: input of affiliation index
     * 必須条件判定結果取得：所属インデックスの入力
     * 
     * @return boolean Essential condition determination result 必須条件判定結果
     */
    private function getCheckIndex(){
        if(!isset($this->checkIndex_)){
            $ret = false;
            
            // ItemRegister のチェック関数を使用
            require_once WEBAPP_DIR. '/modules/repository/components/ItemRegister.class.php';
            $this->debugLog("Get session", __FILE__, __CLASS__, __LINE__);
            $container = & DIContainerFactory::getContainer();
            $session = $container->getComponent("Session");
            $itemRegister = new ItemRegister($session, $this->Db);
            $itemRegister->setEditStartDate($this->accessDate);
            $tmpErrMsg = array();
            $tmpWarningMsg = array();
            $itemRegister->checkIndex($this->index_, $tmpErrMsg, $tmpWarningMsg);
            $this->errMsg_ = array_merge($this->errMsg_, $tmpErrMsg);
            $this->warningMsg_ = array_merge($this->warningMsg_, $tmpWarningMsg);
            if(count($tmpErrMsg) == 0)
            {
                $ret = true;
            }
            $this->checkIndex_ = $ret;
            $this->debugLog("checkIndex_: ".var_export($ret, true), __FILE__, __CLASS__, __LINE__);
        }
        return $this->checkIndex_;
    }
    
    /**
     * Essential condition determination result acquisition: Input DOI value
     * 必須条件判定結果取得：DOIの入力値
     * 
     * @return boolean Essential condition determination result 必須条件判定結果
     */
    private function getCheckDoiInput(){
        if(!isset($this->checkDoiInput_)){
            // DOIアイテムタイプフラグで一次チェック
            $ret = false;
            if($this->doiItemTypeFlag_ === true){
                $container = & DIContainerFactory::getContainer();
                $session = $container->getComponent("Session");
                $suffix = $session->getParameter("doi_suffix");
                $suffixType = $session->getParameter("doi_suffix_type");
                // Prefix入力チェック
                $container = & DIContainerFactory::getContainer();
                $session = $container->getComponent("Session");
                $handleManager = new RepositoryHandleManager($session, $this->Db, $this->accessDate);
                $suffix = $handleManager->checkAndExtractSuffix($suffix, $suffixType);
                // check this item can be granted doi
                $CheckDoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
                if(isset($suffixType) && $suffixType >= Repository_Components_Business_Doi_Checkdoi::TYPE_JALC_DOI && $suffixType <= Repository_Components_Business_Doi_Checkdoi::TYPE_DATACITE)
                {
                    $displays_flag = $CheckDoi->checkDoiGrant($this->itemId_, $this->itemNo_, $suffixType, $suffix);
                }
                // check this item was already granted doi or not
                $doi_status = $CheckDoi->getDoiStatus($this->itemId_, $this->itemNo_);
                if(!isset($displays_flag) || $displays_flag->isGrantDoi || $doi_status >= 1)
                {
                    $ret = true;
                } else {
                    $ConvertResultCheckDoi = BusinessFactory::getFactory()->getBusiness("businessConvertresultcheckdoi");
                    if(!$displays_flag->isGrantDoi)
                    {
                        $errMsg = $ConvertResultCheckDoi->chooseDoiErrMsg($this->itemId_, $this->itemNo_, $displays_flag);
                    }
                    $this->errMsg_ = array_merge($this->errMsg_, $errMsg);
                }
            }
            else
            {
                $ret = true;
            }
            $this->checkDoiInput_ = $ret;
            $this->debugLog("checkDoiInput_: ".var_export($ret, true), __FILE__, __CLASS__, __LINE__);
        }
        return $this->checkDoiInput_;
    }
    
    /**
     * Essential condition determination result acquisition: exchange information of item granted DOI
     * 必須条件判定結果取得：DOI付与済みアイテムの条件合致
     * 
     * @return boolean Essential condition determination result 必須条件判定結果
     */
    private function getCheckItemGrantedDoi(){
        if(!isset($this->checkItemGrantedDoi_)){
            $ret = true;
            $CheckDoi = BusinessFactory::getFactory()->getBusiness("businessCheckdoi");
            // check this item was already granted doi or not
            $doi_status = $CheckDoi->getDoiStatus($this->itemId_, $this->itemNo_);
            // DOI付与フラグで一次チェック
            if($doi_status == 1)
            {
                // Checkdoi クラスを使用
                require_once WEBAPP_DIR. '/modules/repository/components/business/doi/Checkdoi.class.php';
                // check this item can be granted doi
                $container = & DIContainerFactory::getContainer();
                $session = $container->getComponent("Session");
                // RepositoryHandleManager クラスを使用
                require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
                $repositoryHandleManager = new RepositoryHandleManager($session, $this->Db, $this->accessDate);
                $resultCheckDoi = $repositoryHandleManager->checkItemEntriedDoi($this->itemId_, $this->itemNo_, Repository_Components_Business_Doi_Checkdoi::STATUS_FOR_REGISTRATION, Repository_Components_Business_Doi_Checkdoi::CHECKING_STATUS_SWORD_NORMAL);
                
                $ConvertResultCheckDoi = BusinessFactory::getFactory()->getBusiness("businessConvertresultcheckdoi");
                if($resultCheckDoi->isGrantDoi)
                {
                    $ret = true;
                    $warnMsg = $ConvertResultCheckDoi->chooseDoiWarnMsg($resultCheckDoi);
                    $this->warningMsg_ = array_merge($this->warningMsg_, $warnMsg);
                } else {
                    $ret = false;
                    $errMsg = $ConvertResultCheckDoi->chooseDoiErrMsg($this->itemId_, $this->itemNo_, $resultCheckDoi);
                    $this->errMsg_ = array_merge($this->errMsg_, $errMsg);
                }
            }
            $this->checkItemGrantedDoi_ = $ret;
            $this->debugLog("checkItemGrantedDoi_: ".var_export($ret, true), __FILE__, __CLASS__, __LINE__);
        }
        return $this->checkItemGrantedDoi_;
    }
}
?>