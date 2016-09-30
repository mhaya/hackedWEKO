<?php
/**
 * File download / download permissions check.
 *
 * ファイルダウンロード時、ファイルのダウンロードが実施できるユーザであることを確認する。
 *   repository_action_common_downloadクラス実行前に呼び出される。
 *   また、一部関数は詳細表示などからファイルのアクセス権限チェックに使用する。
 */

// --------------------------------------------------------------------
//
// $Id: Validator_DownloadCheck.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * JSON library
 * JSONライブラリ
 */
require_once WEBAPP_DIR. '/modules/repository/components/JSON.php';
/**
 * Index rights management common classes
 * インデックス権限管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryIndexAuthorityManager.class.php';
/**
 * DB object wrapper Class
 * DBオブジェクトラッパークラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDbAccess.class.php';

/**
 * File format confirmed common classes
 * ファイル形式確認共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryCheckFileTypeUtility.class.php';

/**
 * Handle management common classes
 * ハンドル管理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';
/**
 * Class that defines the Validator interface
 * Validatorのインタフェースを規定するクラス
 */
require_once MAPLE_DIR. '/validator/Validator.interface.php';

/**
 * Class to make sure that when a file is downloaded, the download of the file, which is the user that can be implemented
 * ファイルダウンロード時、ファイルのダウンロードが実施できるユーザであることを確認するクラス
 *
 * @package WEKO
 * @copyright (c) 2007 - 2008, National Institute of Informatics, Research and Development Center for Scientific Information Resources.
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Validator_DownloadCheck extends Validator
{
    /**
     * Current file public situation " public ".
     * カレントファイル公開状況「オープンアクセス」
     * 
     * @var int
     */
    const ACCESS_OPEN  = 0;
    
    /**
     * Current file public situation " logged in users only .".
     * カレントファイル公開状況「ログインユーザーのみ」
     * 
     * @var int
     */
    const ACCESS_LOGIN = 1;
    
    /**
     * Current file public situation " does not publish the files ."
     * カレントファイル公開状況「ファイルを公開しない」
     * 
     * @var int
     */
    const ACCESS_CLOSE = 2;
    
    /**
     * Components/Session Object.
     * セッション操作クラス
     * 
     * @var Session
     */
    private $Session = null;
    
    /**
     * Components/Database Object.
     * データベース接続クラス
     * 
     * @var DbObject
     */
    private $Db = null;
    
    /**
     * RepositoryAction class object.
     * RepositoryActionクラス
     * 
     * @var RepositoryAction
     */
    private $RepositoryAction = null;
    
    /**
     * Unique ID of the modules that are running .
     * 動作しているモジュールのユニークID
     * 
     * @var string
     */
    private $block_id = "";
    
    /**
     * Unique ID of the page you are viewing.
     * 表示しているページのユニークID
     * 
     * @var string
     */
    private $page_id = "";
    
    // for index thumbnail download
    /**
     * Unique ID of the index you want to download a thumbnail ( image file )
     * インデックスのユニークID
     * 指定したインデックスのサムネイル(画像ファイル)をダウンロードする
     * 
     * @var string
     */
    private $index_id = "";
    
    // Add PDF cover page 2012/06/13 A.Suzuki --start--
    // for PDF cover page header image download
    /**
     * Parameters to get the header image of PDF cover pages to be displayed on the management screen.
     * "True" => to get the header image of PDF cover pages to be displayed on the management screen
     * PDFカバーページのヘッダー画像取得フラグ
     * "true" => 管理画面にて表示するPDFカバーページのヘッダー画像を取得する
     * 
     * @var string
     */
    private $pdf_cover_header = "";
    // Add PDF cover page 2012/06/13 A.Suzuki --end--
    
    // maple.iniを更新し、新しくValidateDefに変数を追加した場合、
    // privateのメンバ変数を追加する。
    // メンバ変数はNetCommons側から自動的に付与されず、
    // validate関数の第一引数attributesに入る
    // そのため、ValidateDefに変数を追加した場合、attributesから
    // 忘れずに値を取ってくるようにする
    
    // for file download
    /**
     * Unique ID of the item
     * アイテムのユニークID
     * 
     * @var int
     */
    private $item_id = "";
    
    /**
     * Serial number of the item
     * アイテム通番
     * 
     * @var int
     */
    private $item_no = "";
    
    /**
     * Unique ID of the metadata item (attribute ID).
     * メタデータ項目のユニークID（属性ID）
     * 
     * @var int
     */
    private $attribute_id = "";
    
    /**
     * File serial number.
     * ファイル通番
     * 
     * @var int
     */
    private $file_no = "";
    
    // for download content type
    /**
     * To request a thumbnail image download of the PDF file.
     * "true" => To download the thumbnail image file of the PDF file
     * PDFファイルプレビュー画像ダウンロードフラグ
     * "true" => PDFファイルのプレビュー画像ファイルをダウンロードする
     * 
     * @var string
     */
    private $file_prev = "";        // downlaod file preview
    
    /**
     * To download the image file registered in the input format " image file ".
     * "true" => To download the image file registered in the input format "thumbnail"
     * サムネイル画像ダウンロードフラグ
     * "true" => 入力形式「サムネイル」に登録した画像ファイルをダウンロードする
     * 
     * @var string
     */
    private $img = "";              // download thumbnail
    
    /**
     * Download of the specified item type icon (image file ) .
     * アイテムタイプのユニークID
     * 指定したアイテムタイプのアイコン(画像ファイル)をダウンロードする。
     * 
     * @var string
     */
    private $item_type_id = "";     // download item type icon
    
    /**
     * To download the FLASH file of the specified file .
     * "true" => To download the FLASH file of the specified file.
     * FLASHファイルダウンロードフラグ
     * "true" => 指定したファイルのFLASHファイルをダウンロードする。
     * 
     * @var string
     */
    private $flash = "";            // download flash
    
    /**
     * (Deprecated)To download the image file in the file attributes . unused.
     * "true" => To download the image file in the file attributes. unused.
     * (廃止予定)画像ファイルダウンロードフラグ
     * "true" => ファイル属性の画像ファイルをダウンロードする。未使用。
     * 
     * @var string
     */
    private $image_slide = "";      // download slide_image
    
    /**
     * Access user indicates whether chargeable .
     *      "false" => charging disabled
     *      "true" => accounting Allowed
     * アクセスユーザーが課金可能かを示す。
     *      "false" => 課金不可
     *      "true" => 課金可
     * 
     * @var string
     */
    private $pay = "false";         // user agree pay for view file.
    
    // Fix jump to close detail page. 2012/01/30 Y.Nakao --start--
    /**
     * Items published situation.
     *      "0" => private
     *      "1" => public
     * アイテム公開状況
     *      "0" => 非公開
     *      "1" => 公開
     * 
     * @var int
     */
    private $itemPubFlg = 1;
    // Fix jump to close detail page. 2012/01/30 Y.Nakao --end--
    
    // Fix when this class user else action_common_download, not access idserver 2013/04/10 Y.Nakao --start--
    // action_common_download以外からのアクセスだった場合、課金サーバーにcreateChargeリクエストが飛ばないように対応
    /**
     * To determine the confirmation of the download authority checks mere file permissions when a file is downloaded .
     *      true => Before repository_action_common_download implementation was called from NC2 framework.
     *      false => 
     * ファイルダウンロード時のダウンロード権限チェックか単なるファイルアクセス権の確認かを判断する。
     *      true => repository_action_common_download実施前にNC2フレームワークから呼び出された。
     *      false => 別クラスから意図的に呼び出された。ダウンロード処理ではない。
     * 
     * @var bool
     */
    private $fromCommonDownload = false;
    // Fix when this class user else action_common_download, not access idserver 2013/04/10 Y.Nakao --end--
    
    // for openaccess download 2013/06/12 K.Matsuo --start--
    /**
     * Open access date of the file.
     * ファイルのオープンアクセス日
     * （ファイルダウンロードにログイン要求を行う際、この日以降ダウンロード可能というメッセージ表示に使用する）
     * 
     * @var string
     */
    public $openAccessDate = "";
    // for openaccess download 2013/06/12 K.Matsuo --end--
    
    /**
     * Database access class.
     * データベースアクセスクラス
     * 
     * @var RepositoryDbAccess
     */
    private $dbAccess = null;
    
    // Add File replace T.Koyasu 2016/02/29 --start--
    // バージョンファイルダウンロードのためバージョン情報を追加
    /**
     * Version of the file that was registered in the file update history
     * ファイル更新履歴に登録したファイルのバージョン
     *
     * @var int
     */
    private $ver = 0;
    
    /**
     * Access status indicating that it is a private or you can not find the old version of the file
     * 古いバージョンファイルが見つからない or 非公開であることを示すアクセスステータス
     * 
     * @var string
     */
    const ACCESS_STATUS_FILE_IS_NOT_FOUND = "not_found";
    
    
    /**
     * Status indicating that the check result of file embargo is " available for download for the administrator or registrant ".
     * ファイルエンバーゴのチェック結果が「管理者または登録者のためダウンロード可能」であることを示すステータス
     * 
     * @var string
     */
    const ACCESS_STATUS_ADMIN = "admin";
    
    /**
     * Status indicating that the check result of file embargo is " open access ".
     * ファイルエンバーゴのチェック結果が「オープンアクセス」であることを示すステータス
     *
     * @var string
     */
    const ACCESS_STATUS_FREE = "free";
    
    /**
     * Status indicating that the check result of file embargo is " Login required".
     * ファイルエンバーゴのチェック結果が「ログインが必要」であることを示すステータス
     * 
     * @var string
     */
    const ACCESS_STATUS_LOGIN = "login";
    
    /**
     * Status indicating that the check result of file embargo is a "no download authority" (such as private)
     * ファイルエンバーゴのチェック結果が「ダウンロード権限なし」（非公開など）であることを示すステータス
     * 
     * @var string
     */
    const ACCESS_STATUS_CLOSE ="close";
    
    /**
     * Version file public status of " public "
     * バージョンファイルの公開ステータス「公開」
     * 
     * @var int
     */
    const VERSION_FILE_SHOWN_STATE_PUBLIC = 1;
    
    /**
     * Version file public status of " private "
     * バージョンファイルの公開ステータス「非公開」
     * 
     * @var int
     */
    const VERSION_FILE_SHOWN_STATE_PRIVATE = 0;
    
    // Add File replace T.Koyasu 2016/02/29 --end--
    
    /**
     * setting components
     * 初期値設定
     *
     * @param Session $session Session management objects Session管理オブジェクト
     * @param Dbobject $db Database management objects データベース管理オブジェクト
     * @return boolean Result 結果
     */
    public function setComponents($session, $db)
    {
        if($session==null || $db==null)
        {
            return false;
        }
        $this->Session = $session;
        $this->Db = $db;
        $this->RepositoryAction = new RepositoryAction();
        $this->RepositoryAction->Session = $this->Session;
        $this->RepositoryAction->Db = $this->Db;
        $result = $this->RepositoryAction->initAction(false);
        if ( $result === false )
        {
            return false;
        }
        return true;
    }
    
    /**
     * File downloads check
     * It will be called before the download process implementation.
     * ファイルダウンロードチェック
     *      ダウンロード処理実施前に呼び出される。
     * 
     * @param string $attributes Arguments that are passed from maple.ini of class you set the Validator. Validatorを設定したクラスのmaple.iniから渡される引数
     * @param string $errStr Error string that is passed from maple.ini of class you set the Validator. Validatorを設定したクラスのmaple.iniから渡されるエラー文字列
     * @param string $params Error string of replacement character ( not used). エラー文字列の置換文字（未使用）
     * @return string Empty => Successful completion. repository_Action_common_download-> execute is called .
     *                Not Empty => Error message to be passed to the repository_action_common_download_error.html. ":" Pass information separated .
     *                  空文字 => 正常終了。repository_Action_common_download->executeが呼び出される。
     *                  文字列 => repository_action_common_download_error.htmlに渡すエラーメッセージ。「:」区切りで情報を渡す。
     * */
    function validate($attributes, $errStr, $params)
    {
        ////////// set parameter //////////
        $this->setAttributesParameter($attributes);
        
        // ファイルダウンロード処理呼び出しの場合
        if( strlen($this->item_type_id) > 0 ||
            strlen($this->index_id) > 0 ||
            strlen($this->pdf_cover_header) > 0 ||
            strlen($this->file_prev) > 0 ||
            strlen($this->img) > 0)
        {
            // アイテムタイプアイコン、インデックスサムネイル、PDFカバーへーヘッダー画像、ファイルのサムネイル画像、
            // サムネイルメタデータ登録画像の場合はバリデートなしでDL可能
            return;
        }
        
        // error message
        $errorMsg = explode(',', $errStr);
        
        // Parameter invalid check
        if(!is_numeric($this->item_id) || $this->item_id < 1 || !is_numeric($this->item_no) || $this->item_no < 1 ||
           !is_numeric($this->attribute_id) || $this->attribute_id < 1 || !is_numeric($this->file_no) || $this->file_no < 1)
        {
            return "error:$errorMsg[0]:$this->page_id:$this->block_id";
        }
        
        // Fix File replace Y.Nakao 2016/05/23 --start--
        // バージョンファイルダウンロードに必要とな$this->verのチェック
        // 1以上の整数のみ可能。それ以外の場合は無効とし、エラー扱い。
        if(preg_match("/[0-9]+/", $this->ver) !== 1)
        {
            return "error:$errorMsg[0]:$this->page_id:$this->block_id";
        }
        // Fix File replace Y.Nakao 2016/05/23 --end--
        
        // download request file info.
        $fileinfo = $this->item_id."_".$this->item_no."_".$this->attribute_id."_".$this->file_no."_".$this->ver;
        
        // from common_download action
        $this->fromCommonDownload = true;
        
        $status = $this->checkFileDownloadViewStatus();
        if($status == "login")
        {
            // ----------------------------------------
            // reload url(for visible login dialog)
            // ----------------------------------------
            $container =& DIContainerFactory::getContainer();
            $actionChain =& $container->getComponent("ActionChain");
            if($this->Session->getParameter("_mobile_flag") != _ON && $actionChain->_recursive_action != 'pages_view_main')
            {
                // get action_name
                $_request =& $container->getComponent("Request");
                $actionName = $_request->getParameter("action");
                $url = BASE_URL.'/?action=pages_view_main'.
                        "&active_action=$actionName".
                        "&item_id=$this->item_id&item_no=$this->item_no&attribute_id=$this->attribute_id&file_no=$this->file_no";
                $url .= '&block_id='.$this->block_id.
                        '&page_id='.$this->page_id;
                
                // reload
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$url);
                
                exit();
            }
            
            // ----------------------------------------
            // remove or set reload parameter
            // ----------------------------------------
            if($this->Session->getParameter("_mobile_flag") == _OFF)
            {
                // Add File replace T.Koyasu 2016/02/29 --start--
                // バージョンファイルダウンロードのため$this->verをダウンロードキーに追加
                $this->Session->setParameter('repository'.$this->block_id.'FileDownloadKey', $this->item_id."_".$this->item_no."_".$this->attribute_id."_".$this->file_no."_".$this->ver);
                // Add File replace T.Koyasu 2016/02/29 --end--
            }
            
            // ----------------------------------------
            // login error
            // ----------------------------------------
            $loginInfo = $this->makeLoginInformation();
            if($this->openAccessDate == ""){
                return "loginRequest:$errorMsg[1]:$loginInfo:$fileinfo:$this->page_id:$this->block_id:".$this->itemPubFlg;
            }
            else {
                $tmpPubDate = explode("-", $this->openAccessDate);
                $tmpErrMsg = str_replace("YYYY", $tmpPubDate[0], $errorMsg[7]);
                $tmpErrMsg = str_replace("MM", $tmpPubDate[1], $tmpErrMsg);
                $tmpErrMsg = str_replace("DD", $tmpPubDate[2], $tmpErrMsg);
                return "loginRequest:$tmpErrMsg:$loginInfo:$fileinfo:$this->page_id:$this->block_id:".$this->itemPubFlg;
            }
        }
        else if($status == self::ACCESS_STATUS_FILE_IS_NOT_FOUND){
            return "$status:$errorMsg[2]:$this->page_id:$this->block_id";
        }
        else if($status == "delete")
        {
            return "$status:$errorMsg[5]:$this->page_id:$this->block_id";
        }
        else if($status == "close" || $status == "false")
        {
            // false => file_price and ID server not link
            return "$status:$errorMsg[3]:$fileinfo:$this->page_id:$this->block_id:".$this->itemPubFlg;
        }
        else if($status == "error")
        {
            return "$status:$errorMsg[4]";
        }
        else if($status == 'creditError')
        {
            return "$status";
        }
        else if($status == 'GMOError')
        {
            return "$status:$this->page_id:$this->block_id:$this->item_id:$this->item_no";
        }
        // Bug Fix setting not download file by bill server 2014/10/20 T.Koyasu --start--
        else if($status == "shared"){
            // this user can not credit card info, bacause user is shared account
            return "$status:$fileinfo:$this->page_id:$this->block_id:". $this->itemPubFlg;
        }
        else if($status == "unknown"){
            // this user is not regist credit card info
            return "$status";
        }
        // Bug Fix setting not download file by bill server 2014/10/20 T.Koyasu --end--
        else if($status == "free" || $status == "already" || $status == "admin" || $status == "license")
        {
            return;
        }
        else
        {
            // return "trade_id:price"
            if($this->pay == 'true')
            {
                $trade_id_price = split(":", $status, 2);
                if($trade_id_price[0] == ""){
                    return "error:$errorMsg[4]";
                }
                if($this->closeCharge($trade_id_price[0]))
                {
                    // Add File replace T.Koyasu 2016/02/29 --start--
                    // バージョンファイルダウンロードのため$this->verをダウンロードキーに追加
                    $this->Session->setParameter('repository'.$this->block_id.'FileDownloadKey', $this->item_id."_".$this->item_no."_".$this->attribute_id."_".$this->file_no."_".$this->ver);
                    // Add File replace T.Koyasu 2016/02/29 --end--
                    
                    $url = BASE_URL.'/?action=pages_view_main'.
                            "&active_action=repository_view_main_item_detail".
                            "&item_id=$this->item_id&item_no=$this->item_no".
                            '&block_id='.$this->block_id.
                            '&page_id='.$this->page_id;
                    // reload
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".$url);
                    exit();
                } else {
                    return "error:$errorMsg[4]";
                }
            }
            //status = trade_id:price
            return "needPay:$status:$errorMsg[6]:$fileinfo:$this->page_id:$this->block_id";
        }
    }
    
    /**
     * Check File Download Status
     * ファイルダウンロード状態をチェックする
     *
     * @return string File download status. ファイルダウンロードステータス
     */
    private function checkFileDownloadViewStatus()
    {
        $login_id = $this->Session->getParameter("_login_id");
        $user_id = $this->Session->getParameter("_user_id");
        
        // check item exists
        $itemData = array();
        $errorMsg = "";
        $this->RepositoryAction->getItemTableData($this->item_id, $this->item_no, $itemData, $errorMsg);
        if(strlen($errorMsg) > 0 || count($itemData["item"]) == 0){
            return "delete";
        }

        // Add File replace T.Koyasu 2016/02/29 --start--
        // バージョンファイルのダウンロードリクエストの場合、ダウンロード可能かチェックする
        if($this->ver > 0)
        {
            $status = $this->checkVersionFileAccessStatus(  $this->item_id,
                                                            $this->item_no,
                                                            $this->attribute_id,
                                                            $this->file_no,
                                                            $this->ver);
            // バージョンファイルがダウンロード出来ない場合、
            // カレントファイルの権限チェックはせずに返す
            if($status != self::ACCESS_STATUS_ADMIN && $status != self::ACCESS_STATUS_FREE)
            {
                return $status;
            }
        }
        // Add File replace T.Koyasu 2016/02/29 --end--
        
        
        // アイテムの公開状況
        $this->itemPubFlg = 1;
        if($this->checkCanItemAccess($this->item_id, $this->item_no) == false)
        {
            // アイテム非公開 / item closed
            $this->itemPubFlg = 0;
            // アイテムもしくは所属インデックスが非公開 => 会員のみ閲覧を許可される場合と同等
            // ログインしているかで判定
            if($user_id != "0" && strlen($login_id) != 0)
            {
                // Ok login
                return "close";
            }
            else
            {
                // not login
                return "login";
            }
        }
        
        // ファイルデータ取得
        $fileData = $this->getFileData($this->item_id, $this->item_no, $this->attribute_id, $this->file_no);
        if(count($fileData) == 0)
        {
            return "delete";
        }
        
        // ファイル/FLASHの公開状況をチェック
        $status = "close";
        if($this->flash == "true" || $this->image_slide == "true")
        {
            $status = $this->checkFlashAccessStatus($fileData);
        }
        else
        {
            $status = $this->checkFileAccessStatus($fileData);
        }
        
        return $status;
    }
    
    /**
     * check flash access status
     * FLASHファイルアクセス状態をチェックする
     *
     * @param array $fileData file table record. count == 1.
     * @return string File download status. ファイルダウンロードステータス
     */
    public function checkFlashAccessStatus($fileData)
    {
        // set file pub date
        $date = explode(" ", $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_PUB_DATE]);
        $filePubDate = implode('', explode("-", $date[0]));
        
        // set flash pub date in 'pub_date'
        if(strlen($fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_FLASH_PUB_DATE]) > 0)
        {
            $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_PUB_DATE] = $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_FLASH_PUB_DATE];
        }
        
        // check flash exists.
        if($this->existsFlashContents(  $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID], 
                                        $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_NO], 
                                        $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID], 
                                        $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO]) === false)
        {
            return "delete";
        }
        
        // when display_typ != flash ivew, flash can't download.
        $displayType = $fileData['display_type'];
        if($displayType != RepositoryConst::FILE_DISPLAY_TYPE_FLASH)
        {
            return "close";
        }
        // check file status.
        $status = $this->checkFileAccessStatus($fileData);
        
        // アクセスが非公開のとき、Flash公開日に関わらずViewerを表示する
        if($filePubDate === "99991231"){
            // ファイル登録時、アクセスが非公開の場合、課金額が空のレコードが登録されるため、
            // ログインしたユーザでは確定的にCloseとなる
            // 未ログインの場合はLoginが返ってくるので、返り値を調べている
            if($status === "close"){
                return "free";
            } else {
                return $status;
            }
        } else {
            return $status;
        }
    }
    
    /**
     * check file status
     * ファイルアクセス状態をチェックする
     *
     * @param array $fileData file table record. count == 1.
     * @param boolean $accessChargeFlg default true // Add Charge status is not check by snippet T.Koyasu 2014/09/24 
     * @return string File download status. ファイルダウンロードステータス
     */
    public function checkFileAccessStatus($fileData, $accessChargeFlg = true)
    {
        $login_id = $this->Session->getParameter("_login_id");
        $user_id = $this->Session->getParameter("_user_id");
        $user_auth_id = $this->Session->getParameter("_user_auth_id");
        $auth_id = $this->RepositoryAction->getRoomAuthorityID();
        
        $itemId = $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_ID];
        $itemNo = $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_NO];
        $attributeId = $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID];
        $fileNo = $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_FILE_NO];
        
        // check item exists
        $itemData = array();
        $errorMsg = "";
        $this->RepositoryAction->getItemTableData($itemId, $itemNo, $itemData, $errorMsg);
        if(strlen($errorMsg) > 0 || count($itemData["item"]) == 0){
            return "delete";
        }
        
        // check site license
        $siteLicense = $this->checkSiteLicense($itemId, $itemNo);
        
        // check admin user
        $adminUser = false;
        if( $user_auth_id >= $this->RepositoryAction->repository_admin_base && 
            $auth_id >= $this->RepositoryAction->repository_admin_room)
        {
            $adminUser = true;
        }
        
        // check insert user
        $insUser = false;
        if( $user_id == $itemData["item"][0][RepositoryConst::DBCOL_COMMON_INS_USER_ID])
        {
            $insUser = true;
        }
        
        // check file exists
        $fileName = $itemId."_".$attributeId."_".$fileNo.".".$fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_EXTENSION];
        if($this->existsFileContents($fileName) === false)
        {
            return "delete";
        }
        
        // check hidden metadata.
        if( !$adminUser && !$insUser && $this->checkHiddenMetadata($fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_ITEM_TYPE_ID], $attributeId))
        {
            // admin or inser user => download OK.
            // hidden metadata is close
            return "close";
        }
        
        // check file pub date.
        $pubDate = $fileData[RepositoryConst::DBCOL_REPOSITORY_FILE_PUB_DATE];
        $accessFlag = $this->checkFileDownloadViewFlag($pubDate, $this->RepositoryAction->TransStartDate);
        if($accessFlag == self::ACCESS_OPEN)
        {
            // open access file
            // オープンアクセスファイルなので"free"を返す
            return "free";
        }
        else if($adminUser || $insUser)
        {
            // supar user （管理者 または 登録者）
            return "admin";
        }
        else if($accessFlag == self::ACCESS_CLOSE)
        {
            // ファイルを公開しない
            return "close";
        }
        else if($siteLicense == "true")
        {
            // サイトライセンスが有効である
            return "license";
        }
        else if($user_id == "0" || strlen($login_id) == 0)
        {
            // not login user. （未ログインユーザー）
            return "login";
        }
        
        // check file price
        $priceStatus = $this->checkFilePrice($itemId, $itemNo, $attributeId, $fileNo);
        // Add Charge status is not check by snippet T.Koyasu 2014/09/24 --start--
        if($priceStatus != "free" && $priceStatus != "login" && $priceStatus != "close" && $accessChargeFlg == true)
        {
            // $priceStatus = file price
            // check pay status
            $priceStatus = $this->accessChargeServer($itemId, $itemNo, $attributeId, $fileNo, $priceStatus);
        }
        // Add Charge status is not check by snippet T.Koyasu 2014/09/24 --end--
        return $priceStatus;
    }
    
    /**
     * check file price
     * ファイルの課金額をチェックする
     * 
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテムの通番
     * @param int $attribute_id Unique ID of metadata items ( attribute value ). メタデータ項目のユニークID(属性ID)
     * @param int $file_no File no ファイル通番
     * @return string viewFlag_downloadFlag ダウンロードフラグ
     *                error
     *                free  
     *                unknown
     *                creditError
     *                close
     *                GMOError
     *                trade_id:price
     */
    public function checkFilePrice($item_id, $item_no, $attribute_id, $file_no){
        // check input_type (file or file_price)
        // Select 
        $query = "SELECT pub_date, flash_pub_date ".
                 "FROM ". DATABASE_PREFIX ."repository_file ".
                 "WHERE item_id = ? ".
                 "  AND item_no = ? ".
                 "  AND attribute_id = ? ".
                 "  AND file_no = ? ".
                 "  AND is_delete = 0";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $attribute_id;
        $params[] = $file_no;
        $file = $this->Db->execute($query, $params);
        if ($file === null) {
            return "error";
        }

        // Item Data
        $item = array();
        $errorMsg = "";
        $this->RepositoryAction->getItemTableData($item_id, $item_no, $item, $errorMsg);
        if(strlen($errorMsg) > 0 || count($item["item"]) == 0){
            return "error";
        }

        $user_auth_id = $this->Session->getParameter("_user_auth_id");
        $auth_id = $this->RepositoryAction->getRoomAuthorityID();
        $user_id = $this->Session->getParameter("_user_id");

        if(($user_auth_id >= $this->RepositoryAction->repository_admin_base && $auth_id >= $this->RepositoryAction->repository_admin_room) || $item["item"][0]['ins_user_id'] === $user_id){
            return 'free';    
        }
        $price = $this->getFilePriceTable($item_id, $item_no, $attribute_id, $file_no);
        // End when retrieval result is not one
        if(count($price) > 1){
            return "error";
        } else if (count($price) == 1) {
            // input_type == file_price
            // get file price
            $file_price = $this->getFilePrice($price[0]["price"]);         
            // get file price
            if($file_price === "0"){
                return 'free';
            } else if($file_price === ""){
                $login_id = $this->Session->getParameter("_login_id");
                if($user_id == "0" || strlen($login_id) == 0){
                    return "login";
                }
                return 'close';
            } else {
                $login_id = $this->Session->getParameter("_login_id");
                $user_id = $this->Session->getParameter("_user_id");
                if($user_id == "0" || strlen($login_id) == 0){
                    return "login";
                }
                return $file_price;
            }
        // no price
        } else {
            return "free";
        }
    }
    
    /**
     * getFilePriceTable
     * 課金情報を取得する
     * 
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテムの通番
     * @param int $attribute_id Unique ID of metadata items ( attribute value ). メタデータ項目のユニークID(属性ID)
     * @param int $file_no File no ファイル通番
     * @return $result_file_price_Table 課金ファイル情報
     */
    function getFilePriceTable($item_id, $item_no, $attribute_id, $file_no){
        // 課金情報をチェック
        $query = "SELECT * ".
                 "FROM ". DATABASE_PREFIX. "repository_file_price ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND attribute_id = ? ".
                 "AND file_no = ? ".
                 "AND is_delete = 0;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $attribute_id;
        $params[] = $file_no;
        $result_file_price_Table = $this->Db->execute($query, $params);
        if($result_file_price_Table === false)
        {
            return array();
        }
        return $result_file_price_Table;
    }
    
    /**
     * get access user's file price
     * ユーザごとの課金情報を取得する
     *
     * @param string $price Price each group グループごとの課金額
     *                      room_id,rpice|room_id,price|...
     * @return string Price 課金額
     */
    public function getFilePrice($price)
    {
        ///// get groupID and price /////
        $room_price = explode("|",$price);
        ///// ユーザが入っているグループIDを取得 /////
        $result = $this->RepositoryAction->getUsersGroupList($user_group,$error_msg);
        if($result===false){
            return false;
        }
        $file_price = "";
        for($price_Cnt=0;$price_Cnt<count($room_price);$price_Cnt++){
            $price = explode(",", $room_price[$price_Cnt]);
            // There is a pair of room_id and the price. 
            if($price!=null && count($price)==2)
            {
                // It is judged whether it is user's belonging group.
                for($user_group_cnt=0;$user_group_cnt<count($user_group);$user_group_cnt++){
                    if($price[0] == $user_group[$user_group_cnt]["room_id"]){
                        // When the price is set to the belonging group
                        if($file_price==""){
                            // The price is maintained at the unsetting. 
                            $file_price = $price[1];
                        } else if(intval($file_price) > intval($price[1])){
                            // It downloads it by the lowest price. 
                            $file_price = $price[1];
                        }
                    }
                }
            }
        }
        return $file_price;
    }
    
    /**
     * check hidden metadata
     * 非表示メタデータであるかを確認する
     * 
     * @param int $itemTypeId Unique ID of the item typeアイテムタイプのユニークID
     * @param int $attributeId Unique ID of metadata items ( attribute value ). メタデータ項目のユニークID(属性ID)
     * @return boolean Is hidden 非表示であるか否か
     *                 true:hidden metadata, false:not hidden metadata
     */
    private function checkHiddenMetadata($itemTypeId, $attributeId)
    {
        $query = " SELECT hidden ".
                 " FROM ".DATABASE_PREFIX."repository_item_attr_type ".
                 " WHERE item_type_id = ? ".
                 "   AND attribute_id = ? ".
                 "   AND is_delete = ? ";
        $params = array();
        $params[] = $itemTypeId;
        $params[] = $attributeId;
        $params[] = 0;
        $itemAttrType = $this->Db->execute($query, $params);
        if($itemAttrType === false)
        {
            return true;
        }
        if(count($itemAttrType) != 1)
        {
            return true;
        }
        if($itemAttrType[0][RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_HIDDEN] == 0)
        {
            return false;
        }
        return true;
    }
    
    /**
     * get filedata
     * ファイル情報を取得する
     * 
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテムの通番
     * @param int $attribute_id Unique ID of metadata items ( attribute value ). メタデータ項目のユニークID(属性ID)
     * @param int $file_no File no ファイル通番
     * @return array File info. ファイルテーブルのレコード
     */
    private function getFileData($itemId, $itemNo, $attributeId, $fileNo)
    {
        // ファイルデータの存在チェック
        $query = " SELECT * ".
                 " FROM ".DATABASE_PREFIX."repository_file ".
                 " WHERE item_id = ? ".
                 "  AND item_no = ? ".
                 "  AND attribute_id = ? ".
                 "  AND file_no = ? ".
                 "  AND is_delete = ? ";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attributeId;
        $params[] = $fileNo;
        $params[] = 0;
        $file = $this->Db->execute($query, $params);
        if($file === false)
        {
            return array();
        }
        else if(count($file) != 1)
        {
            return array();
        }
        return $file[0];
    }
    
    /**
     * check flash contents exists
     * FLASHファイルが存在するかを確認する
     *
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテムの通番
     * @param int $attribute_id Unique ID of metadata items ( attribute value ). メタデータ項目のユニークID(属性ID)
     * @param int $file_no File no ファイル通番
     * @return boolean Is exist 存在しているか否か
     *                 true:exists, false:not exists
     */
    function existsFlashContents($item_id, $item_no, $attribute_id, $file_no)
    {
        $flash_contents_path = $this->RepositoryAction->getFlashFolder($item_id,$attribute_id, $file_no);
        // get directory path of image
        $image_contents_path = $this->RepositoryAction->getFileSavePath('file');
        if(strlen($image_contents_path) == 0){
            // default directory
            $image_contents_path = BASE_DIR.'/webapp/uploads/repository/files';
        }
        // get file extension
        $query = "SELECT mime_type, extension FROM ". DATABASE_PREFIX ."repository_file ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND attribute_id = ? ".
                 "AND file_no = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $attribute_id;
        $params[] = $file_no;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            return false;
        }
        
        if(file_exists($flash_contents_path.DIRECTORY_SEPARATOR.'/weko.swf'))
        {
            return true;
        }
        else if(file_exists($flash_contents_path.DIRECTORY_SEPARATOR.'/weko1.swf'))
        {
            return true;
        }
        // Add multimedia support 2012/08/27 T.Koyasu -start-
        // add weko.flv to flash contents
        else if(file_exists($flash_contents_path.DIRECTORY_SEPARATOR.'/weko.flv'))
        {
            return true;
        }
        // Add multimedia support 2012/08/27 T.Koyasu -end-
        // Add image support 2014/01/16 R.Matsuura --start--
        else if( RepositoryCheckFileTypeUtility::isImageFile($result[0]['mime_type'], $result[0]['extension']) 
              && file_exists($image_contents_path.DIRECTORY_SEPARATOR.$item_id.'_'.$attribute_id.'_'.$file_no.'.'.$result[0]['extension']) )
        {
            return true;
        }
        // Add image support 2014/01/16 R.Matsuura --end--
        else
        {
            return false;
        }
    }
    
    /**
     * check file contents exists
     * ファイルが存在するかを確認する
     *
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテムの通番
     * @param int $attribute_id Unique ID of metadata items ( attribute value ). メタデータ項目のユニークID(属性ID)
     * @param int $file_no File no ファイル通番
     * @return boolean Is exist 存在するか否か
     *                 true:exists, false:not exists
     */
    function existsFileContents($fileName)
    {
        $filecontents_path = $this->RepositoryAction->getFileSavePath("file");
        if(strlen($filecontents_path) == 0)
        {
            // default directory
            $filecontents_path = BASE_DIR.'/webapp/uploads/repository/files';
        }
        if(file_exists($filecontents_path.DIRECTORY_SEPARATOR.$fileName))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Check file download view flag
     * ファイルの公開日を過ぎているか判定する。
     * 
     * @param string $pubDate File open date. ファイル公開日
     * @param string $transStartDate Now date. アクセス日時
     * @return string access flag アクセス状態
     *                0: open
     *                1: need login
     *                2: not access
     */
    public function checkFileDownloadViewFlag($pubDate, $transStartDate)
    {
        $accessFlag = self::ACCESS_CLOSE;
        $this->openAccessDate = "";
        // toInt now date
        $date = explode(" ", $transStartDate);
        $nowDate = implode('', explode("-", $date[0]));
        
        // toInt pub date
        $divPubDate = explode(" ", $pubDate);
        $tmpPubDate = implode('', explode("-", $divPubDate[0]));
        if($tmpPubDate == '99991231')
        {
            // not access
            $accessFlag = self::ACCESS_CLOSE;
        }
        else if($tmpPubDate == '99990101')
        {
            // login only or flash publish date is future.
            // need login.
            $accessFlag = self::ACCESS_LOGIN;
        }
        else if($tmpPubDate > $nowDate)
        {
            // login only or flash publish date is future.
            // need login.
            $this->openAccessDate = $divPubDate[0];
            $accessFlag = self::ACCESS_LOGIN;
        }
        else
        {
            // open
            $accessFlag = self::ACCESS_OPEN;
        }
        
        return $accessFlag;
    }
    
    /**
     * check item access flag
     * アイテムにアクセスできるかをチェックする
     * 
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテムの通番
     * @return boolean be able to access アクセスできるか否か
     *                 true:canAccess, false:cannnotAccess  
     */
    public function checkCanItemAccess($item_id, $item_no)
    {
        // Fix insert user fileDL 2012/01/30 Y.Nakao --start--
        $user_id = $this->Session->getParameter("_user_id");
        $user_auth_id = $this->Session->getParameter("_user_auth_id");
        $auth_id = $this->RepositoryAction->getRoomAuthorityID();
        // Fix insert user fileDL 2012/01/30 Y.Nakao --end--

        // check item public
        $query = "SELECT shown_date, shown_status, ins_user_id".
                " FROM ".DATABASE_PREFIX."repository_item ".
                " WHERE item_id = ? ".
                " AND item_no = ? ".
                 "AND is_delete = ? ";
        $param = array();
        $param[] = $item_id;
        $param[] = $item_no;
        $param[] = 0;
        $result = $this->Db->execute($query, $param);
        // check get data.
        if($result === false){
            return false;
        } else if(count($result) == 0){
            return false;
        } else if(count($result) > 1){
            return false;
        } else if(!isset($result[0])){
            return false;
        }

        // check NC2 admin user
        if($user_auth_id >= $this->RepositoryAction->repository_admin_base && $auth_id >= $this->RepositoryAction->repository_admin_room)
        {
            // for admin
            return true;
        }
        // Fix insert user fileDL 2012/01/30 Y.Nakao --start--
        if($user_id === $result[0]['ins_user_id'])
        {
            // for insert user.
            return true;
        }
        if($result[0]['shown_date'] > $this->RepositoryAction->TransStartDate || $result[0]['shown_status'] != 1)
        {
            // item close.
            return false;
        }
        // Fix insert user fileDL 2012/01/30 Y.Nakao --end--
        
        // check index public status.
        $public_index = array();
        // Add Open Depo 2013/12/03 R.Matsuura --start--
        // Mod OpenDepo 2014/01/31 S.Arata --start--
        $this->RepositoryAction->setConfigAuthority();
        $this->dbAccess = new RepositoryDbAccess($this->Db);
        $indexAuthorityManager = new RepositoryIndexAuthorityManager($this->Session, $this->dbAccess, $this->RepositoryAction->TransStartDate);
        // Mod OpenDepo 2014/01/31 S.Arata --end--
        // Add Open Depo 2013/12/03 R.Matsuura --end--
        
        // check position index public
        $query = "SELECT index_id ".
                " FROM ".DATABASE_PREFIX."repository_position_index ".
                " WHERE item_id = ? ".
                " AND item_no = ? ".
                " AND is_delete = 0 ; ";
        $param = array();
        $param[] = $item_id;
        $param[] = $item_no;
        $result = $this->Db->execute($query, $param);
        if($result === false){
            return false;
        } else if(count($result) == 0){
            return false;
        }else if(count($result) > 0){
            for($ii=0; $ii<count($result); $ii++){
                $public_index = $indexAuthorityManager->getPublicIndex(false, $this->RepositoryAction->repository_admin_base, $this->RepositoryAction->repository_admin_room, $result[$ii]["index_id"]);
                if(count($public_index) > 0){
                    // index is public
                    // and item public
                    return true;
                }
            }
        }
        
        // item is public, index is close.
        return false;
    }
    
    // Add check site license 2008/10/20 Y.Nakao --start--
    /**
     * To determine whether the access from the site license Regulatory.
     * サイトライセンス認可機関からのアクセスかを判断する
     * 
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテムの通番
     * @param number $sitelicense_id sitelisence org. ユーザーが所属しているサイトライセンス認可機関の情報
     * @return string Is access by site license organization サイトライセンス認可機関からのアクセスか
     *                "true" => Belong to the site license approval authority. サイトライセンス認可機関に所属している / "false" => It does not belong to the site license approval authority. サイトライセンス認可機関に所属していない
     */
    public function checkSiteLicense($item_id="", $item_no="", &$sitelicense_id=0){
        // サイトライセンス除外アイテムタイプのチェック（引数が設定されている場合のみ）
        // 除外アイテムタイプであった場合はSLユーザーであってもfalseを返すので一番先に処理をする
        $site_license_item_type_id = "";
        $item_type_id = "";
        if(strlen($item_id)>0 && strlen($item_no)>0 && $item_id > 0 && $item_no > 0)
        {
            // Add item_type_id for site license 2009/01/07 A.Suzuki --start--
            // get param table data : site_license_item_type_id
            $query = "SELECT param_value FROM ". DATABASE_PREFIX ."repository_parameter ".
                     "WHERE param_name = 'site_license_item_type_id'; ";
            $result = $this->Db->execute($query);
            if($result === false){
                return "false";
            }
            $site_license_item_type_id = explode(",", $result[0]['param_value']);
            
            // get item_type_id
            $query = "SELECT item_type_id ".
                     "FROM ".DATABASE_PREFIX."repository_item ".
                     "WHERE  item_id = ? ".
                     "AND    item_no = ? ".
                     "AND    is_delete = 0;";
            $params = null;
            $params[] = $item_id;
            $params[] = $item_no;
            $result = $this->Db->execute($query, $params);
            if($result === false){
                return "false";
            }
            $item_type_id = $result[0]['item_type_id'];
            // Add item_type_id for site license 2009/01/07 A.Suzuki --end--
            if(count($result) > 0) {
                $item_type_id = $result[0]['item_type_id'];
                // Add item_type_id for site license 2009/01/07 A.Suzuki --end--
                for($jj=0; $jj<count($site_license_item_type_id); $jj++){
                    if($site_license_item_type_id[$jj] == $item_type_id){
                        return "false";
                    }
                }
            }
        }
        
        // サイトライセンスユーザーであるかどうかのチェック
        // ユーザーのIPアドレスの取得
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strlen($_SERVER['HTTP_X_FORWARDED_FOR']) > 0) {
            $access_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $access_ip = getenv("REMOTE_ADDR");
        }
        // IPアドレスを0埋めの12桁の文字列にする
        $ipaddress = explode(".", $access_ip);
        $ip = sprintf("%03d", $ipaddress[0]).
              sprintf("%03d", $ipaddress[1]).
              sprintf("%03d", $ipaddress[2]).
              sprintf("%03d", $ipaddress[3]);
        // サイトライセンスに設定されたIPレンジの取得
        $query = "SELECT organization_id, start_ip_address, finish_ip_address FROM ". DATABASE_PREFIX. "repository_sitelicense_ip_address ".
                 "WHERE is_delete = ? ;";
        $params = array();
        $params[] = 0;
        $sitelicense_ip = $this->Db->execute($query, $params);
        if($sitelicense_ip === false){
            return "false";
        }
        // チェック処理
        for($ii=0; $ii<count($sitelicense_ip); $ii++){
            if(isset($sitelicense_ip[$ii]["start_ip_address"]) && strlen($sitelicense_ip[$ii]["start_ip_address"]) > 0){
                // IPレンジの始点を0埋め12ケタの文字列にする
                $start_ip = explode(".", $sitelicense_ip[$ii]["start_ip_address"]);
                $from = sprintf("%03d", $start_ip[0]).
                        sprintf("%03d", $start_ip[1]).
                        sprintf("%03d", $start_ip[2]).
                        sprintf("%03d", $start_ip[3]);
                if(isset($sitelicense_ip[$ii]["finish_ip_address"]) && strlen($sitelicense_ip[$ii]["finish_ip_address"]) > 0){
                    // IPレンジの終点を0埋め12ケタの文字列にする
                    $finish_ip = explode(".", $sitelicense_ip[$ii]["finish_ip_address"]);
                    $to = sprintf("%03d", $finish_ip[0]).
                          sprintf("%03d", $finish_ip[1]).
                          sprintf("%03d", $finish_ip[2]).
                          sprintf("%03d", $finish_ip[3]);
                    // ユーザーのIPアドレスが範囲内に収まっていればtrueを返す
                    if($from <= $ip && $ip <= $to){
                        $sitelicense_id = $sitelicense_ip[$ii]["organization_id"];
                        return "true";
                    }
                } elseif($ip == $from) {
                    // IPが始点(=from)のみ設定されている場合はそれと一致するかどうかを判定する
                    $sitelicense_id = $sitelicense_ip[$ii]["organization_id"];
                    return "true";
                }
            }
        }
        
        // add Check users oraganization 2015/01/19 T.Ichikawa --start--
        // ユーザーがサイトライセンス組織に所属しているかのチェック
        $sitelicense_group = $this->checkSiteLicenseGroup($sitelicense_id);
        // サイトライセンス組織に設定されている場合はtrueを返す
        if($sitelicense_group === true) {
            return "true";
        }
        // add Check users oraganization 2015/01/19 T.Ichikawa --end--
        return "false";
    }
    
    /**
     * set parameter
     * パラメータ設定
     *
     * @param array $attributes Arguments that are passed from maple.ini of class you set the Validator. Validatorを設定したクラスのmaple.iniから渡される引数
     */
    private function setAttributesParameter($attributes)
    {
        $container =& DIContainerFactory::getContainer();
        $this->Session =& $container->getComponent("Session");
        $this->Db =& $container->getComponent("DbObject");
        $this->RepositoryAction = new RepositoryAction();
        $this->RepositoryAction->Session = $this->Session;
        $this->RepositoryAction->Db = $this->Db;
        $result = $this->RepositoryAction->initAction();
        $result = $this->RepositoryAction->exitAction();
        
        // item_id
        if(isset($attributes[0]) && strlen($attributes[0]) > 0){
            $this->item_id = $attributes[0];
        }
        // item_no
        if(isset($attributes[1]) && strlen($attributes[1]) > 0){
            $this->item_no = $attributes[1];
        }
        // attribute_id
        if(isset($attributes[2]) && strlen($attributes[2]) > 0){
            $this->attribute_id = $attributes[2];
        }
        // file_no
        if(isset($attributes[3]) && strlen($attributes[3]) > 0){
            $this->file_no = $attributes[3];
        }
        // block id
        if(isset($attributes[4]) && strlen($attributes[4]) > 0 && $attributes[4] != "0"){
            $this->block_id = $attributes[4];
        }
        // page id
        if(isset($attributes[5]) && strlen($attributes[5]) > 0 && $attributes[4] != "0"){
            $this->page_id = $attributes[5];
        }
        
        // when block_id or page_id is not set, set parameter.
        if(strlen($this->block_id) == 0 || strlen($this->page_id) == 0)
        {
            $block_info = $this->RepositoryAction->getBlockPageId();
            $this->block_id = $block_info["block_id"];
            $this->page_id  = $block_info["page_id"];
        }
        
        // file prev
        if(isset($attributes[6]) && strlen($attributes[6]) > 0){
            $this->img = $attributes[6];
        }
        // file prev
        if(isset($attributes[7]) && strlen($attributes[7]) > 0){
            $this->item_type_id = $attributes[7];
        }
        // file prev
        if(isset($attributes[8]) && strlen($attributes[8]) > 0){
            $this->file_prev = $attributes[8];
        }
        // index thumnail
        if(isset($attributes[9]) && strlen($attributes[9]) > 0){
            $this->index_id = $attributes[9];
        }
        // The intention to pay 
        if(isset($attributes[10]) && strlen($attributes[10]) > 0){
            $this->flash = $attributes[10];
        }
        // The intention to pay 
        if(isset($attributes[11]) && strlen($attributes[11]) > 0){
            $this->pay = $attributes[11];
        }
        // PDF cover page header image
        if(isset($attributes[12]) && strlen($attributes[12]) > 0){
            $this->pdf_cover_header = $attributes[12];
        }
        if($this->pdf_cover_header != null){
            return;
        }
        // image_slide
        if(isset($attributes[13]) && strlen($attributes[13]) > 0){
            $this->image_slide = $attributes[13];
        }
        // Add File replace T.Koyasu 2016/02/29 --start--
        // バージョンファイルダウンロードのためバージョン情報を追加
        // ver
        if(isset($attributes[14]) && strlen($attributes[14]) > 0){
            $this->ver = $attributes[14];
        }
        // Add File replace T.Koyasu 2016/02/29 --start--
    }
    
    /**
     * make login error parameter
     * ログインエラーパラメータ作成
     *
     * @return string login error parameter ログインエラーパラメータ
     *                NC2version flg:shibboleth flg
     */
    private function makeLoginInformation(){
        $version = 0;
        $container =& DIContainerFactory::getContainer();
        $configView =& $container->getComponent("configView");
        $config_version = $configView->getConfigByConfname(_SYS_CONF_MODID, "version");
        if(isset($config_version) && isset($config_version['conf_value'])) {
            $version = $config_version['conf_value'];
        } else {
            $version = _NC_VERSION;
        }
        if(str_replace(".", "", $version) < 2301){
          // under ver.2.3.0.1
          $version = "0";
        }else{
          // over ver.2.3.0.1
          $version = "1";
        }
        $shibboleth = SHIB_ENABLED;
        $shibboleth = intval($shibboleth);
        
        // return error message
        // NC2version flg:shibboleth flg
        return "$version:$shibboleth";
    }
    
    /***************** charge class **************************/
    
    /**
     * check can access Charge Server
     * 課金サーバにアクセスできるかを確認する
     *
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテムの通番
     * @param int $attribute_id Unique ID of metadata items ( attribute value ). メタデータ項目のユニークID(属性ID)
     * @param int $file_no File no ファイル通番
     * @param string $file_price price ファイルの金額
     * @return string Billing implementation results. 課金サーバーへの接続結果
     */
    private function accessChargeServer($item_id, $item_no, $attribute_id, $file_no, $file_price)
    {
        // set user page url.
        $charge_pass = $this->getChargePass();
        $user_info_url = "https://".$charge_pass["user_fqdn"]."/user/menu/".
                         $charge_pass["sys_id"];//"/".$login_id;
        $this->Session->setParameter("user_info_url", $user_info_url);
        
        // create charge
        $trade_id = $this->createCharge($credit_url, $item_id, $item_no, $attribute_id, $file_no);
        if($trade_id == "unknown"){
            return 'unknown';
        }
        if($trade_id == "shared"){
            return 'shared';
        }
        if(strlen($trade_id) == 0 || $trade_id == "credit"){
            return 'creditError';
        }
        else if($trade_id == "false"){
            return "false";
        }else if($trade_id == "close"){
            return "close";
        }
        // Add GMO error 2009/06/19 A.Suzuki --start--
        else if($trade_id == "connection"){
            // go view action
            return "GMOError";
        } else if($trade_id == "free"){
            // paid
            return "free";
        } else if($trade_id == "already"){
            // already
            return "already";
        } else {
            return "$trade_id:$file_price";
        }
        // Add GMO error 2009/06/19 A.Suzuki --end--
    }
    
    /**
     * create charge action
     * 課金を実施する
     * 
     * @param string $credit_url 課金URL
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテムの通番
     * @param int $attribute_id Unique ID of metadata items ( attribute value ). メタデータ項目のユニークID(属性ID)
     * @param int $file_no File no ファイル通番
     * @return Billing implementation results. 課金実施結果
     */
    function createCharge(&$credit_url, $item_id, $item_no, $attribute_id, $file_no){
        $result = $this->checkChargeRecord($item_id, $item_no, $credit_url);
        if($result != "true"){
            return $result;
        }
        // get title
        $return = $this->RepositoryAction->getItemTableData($item_id, $item_no, $item_data, $errorMsg);
        if($return === false || count($item_data["item"])==0){
            return "unknown";
        }
        // search price
        $price = $this->getFilePriceTable($item_id, $item_no, $attribute_id, $file_no);
        if(count($price) == 0){
            // not price file
            return "";
        }
        $file_price = $this->getFilePrice($price[0]["price"]);
        if($file_price == "0"){
            // not charge
            return "free";
        }
        if($file_price == ""){
            return "false";
        }
        
        // Fix when this class user else action_common_download, not access idserver 2013/04/10 Y.Nakao --start--
        if($this->fromCommonDownload == false)
        {
            // ダウンロードではなく、ファイルの課金状態チェックなのでcreateChargeまで実施せずに戻る
            return "true";
        }
        // Fix when this class user else action_common_download, not access idserver 2013/04/10 Y.Nakao --end--
        
        // Modify add memo for charge record. 2012/02/28 Y.Nakao --start--
        $memo = $this->getChargeMemo($item_id, $item_no);
        // Modify add memo for charge record. 2012/02/28 Y.Nakao --end--
        
        ////////// create charge record //////////
        $block_info = $this->RepositoryAction->getBlockPageId();
        
        $repositoryDbAccess = new RepositoryDbAccess($this->Db);
        
        // Bug Fix TransStartDate is set in RepositoryAction instance T.Koyasu 2014/07/31 --start--
        $repositoryHandleManager = new RepositoryHandleManager($this->Session, $repositoryDbAccess, $this->RepositoryAction->TransStartDate);
        // Bug Fix TransStartDate is set in RepositoryAction instance T.Koyasu 2014/07/31 --end--
        
        $prefixID = $repositoryHandleManager->getPrefix(RepositoryHandleManager::ID_Y_HANDLE);
        $suffixID = $repositoryHandleManager->getSuffix($item_id, $item_no, RepositoryHandleManager::ID_Y_HANDLE);
        
        if (empty($prefixID) || empty($suffixID)) {
            return false;
        }
        
        // request uri is write ASCII
        // "/"->"%2F", ":"->"%3A", "&"->"%26", "."->"%2e"
        // change redirect url 2008/11/19 Y.Nakao --start--
        $url = str_replace("/", "%2F", BASE_URL);
        $url .= "%2F%3Faction=repository_uri".
                "%26item_id=".$item_id.
                "%26file_id=".$attribute_id;
        // change redirect url 2008/11/19 Y.Nakao --end--
        // create charge URL
        $charge_pass = $this->getChargePass();
        $send_param =   "https://".$charge_pass["charge_id"].":".$charge_pass["charge_pass"]."@".
                        $charge_pass["charge_fqdn"].
                        "/charge/create?".
                        "sys_id=".$charge_pass["sys_id"]. //sys_id :WEKOシステムを識別するID(現在 "weko01" のみ有効です)
                        // Fix change WEKO's user_id to WEKO'slogin_id 2008/10/30 Y.Nakao
                        "&user_id=".$this->Session->getParameter("_login_id").// user_id :利用者のWEKO_ID(LDAPと連携までは何でも通します)
                        "&content_id=".$prefixID."_".$suffixID.
                        "&price=".$file_price.
                        "&title=".urlencode($item_data["item"][0]["title"]).
                        "&uri=".$url.
                         // Modify add memo for charge record. 2012/02/28 Y.Nakao --start--
                         "&memo=".$memo;
                         // Modify add memo for charge record. 2012/02/28 Y.Nakao --end--
        // HTTP_Request init
        // send http request
        $option = array( 
            "timeout" => "10",
            "allowRedirects" => true, 
            "maxRedirects" => 3, 
        );
        // Modfy proxy 2011/12/06 Y.Nakao --start--
        $proxy = $this->RepositoryAction->getProxySetting();
        if($proxy['proxy_mode'] == 1)
        {
            $option = array( 
                    "timeout" => "10",
                    "allowRedirects" => true, 
                    "maxRedirects" => 3,
                    "proxy_host"=>$proxy['proxy_host'],
                    "proxy_port"=>$proxy['proxy_port'],
                    "proxy_user"=>$proxy['proxy_user'],
                    "proxy_pass"=>$proxy['proxy_pass']
                );
        }
        // Modfy proxy 2011/12/06 Y.Nakao --end--
        $http = new HTTP_Request($send_param, $option);
        
        // setting HTTP header
        $http->addHeader("User-Agent", $_SERVER['HTTP_USER_AGENT']); 
        $http->addHeader("Referer", $_SERVER['HTTP_REFERER']);
        
        // run HTTP request 
        $response = $http->sendRequest(); 
        if (!PEAR::isError($response)) { 
            $charge_code = $http->getResponseCode();// ResponseCode(200等)を取得 
            $charge_header = $http->getResponseHeader();// ResponseHeader(レスポンスヘッダ)を取得 
            $charge_body = $http->getResponseBody();// ResponseBody(レスポンステキスト)を取得 
            $charge_Cookies = $http->getResponseCookies();// クッキーを取得 
        }
        
        $result_js = $charge_body;
        
        $json = new Services_JSON();
        $decoded = $json->decode($result_js);
        
        // オーソリエラー(カード番号が未登録か無効)
        if($charge_header["weko_charge_status"] == -128){
            $credit_url = str_replace("\\", "", $decoded->location);
            return "credit";
        }
        
        // GMO通信エラー
        if($charge_header["weko_charge_status"] == -64){
            return "connection";
        }
        
        if($decoded->charge_status == "1"){
            // already download
            return "already";
        }
        
        return $decoded->trade_id;
    }
    
    // TODO スタブ解除
    /**
     * checkChargeRecord
     * IDServerと連携している場合、課金ログをチェックする
     *  
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテムの通番
     * @param string $credit_url Billing URL 課金URL
     * @return string Result 結果
     *                "true" And in cooperation with the IDServer, billing before IDServerと連携している、課金前
     *                "false" Not in conjunction with IDServer IDServerと連携していない
     *                "unknown" No credit card information クレジットカード情報なし
     *                "shared" Credit cards can not be registered(shared account) クレジットカード登録不可(共有アカウント)
     *                "credit" Credit card information error クレジットカード情報エラー
     *                "already" Billing already 課金済
     */
    function checkChargeRecord($item_id, $item_no, &$credit_url){
        // getPrefixID
        $prefixID_flg = false;

        $repositoryDbAccess = new RepositoryDbAccess($this->Db);
        // Bug Fix TransStartDate is set in RepositoryAction instance T.Koyasu 2014/07/31 --start--
        $repositoryHandleManager = new RepositoryHandleManager($this->Session, $repositoryDbAccess, $this->RepositoryAction->TransStartDate);
        // Bug Fix TransStartDate is set in RepositoryAction instance T.Koyasu 2014/07/31 --end--
        
        $prefixID = $repositoryHandleManager->getPrefix(RepositoryHandleManager::ID_Y_HANDLE);
        
        if (strlen($prefixID) != 0) {
            $prefixID_flg = true;
        }
        
        // get suffixID
        $suffixID_flg = false;
        
        $suffixID = $repositoryHandleManager->getSuffix($item_id, $item_no, RepositoryHandleManager::ID_Y_HANDLE);
        
        if (strlen($suffixID) != 0) {
            $suffixID_flg = true;
        }
        
        if($prefixID_flg && $suffixID_flg){
            // check charge record
            $result_js = $this->getChargeRecord($prefixID."_".$suffixID);
            $json = new Services_JSON();
            $decoded = $json->decode($result_js);
            if($decoded->message == "unknown_user_id"){
                // there is no credit card info
                return "unknown";
            } else if($decoded->message == "this_user_is_not_permit_to_use_credit_card" || $decoded->message == "this_user_does_not_have_permission_for_credit_card"){
                // Unable to register credit card info (Shared account user)
                return "shared";
            } else if($decoded->location != ""){
                // credit card info error
                $credit_url = str_replace("\\", "", $decoded->location);
                return "credit";
            } else if($decoded[0] != null || $decoded[0] != ""){
                // already charge
                return "already";
            } else {
                return "true";
            }
        } else {
            return "false";
        }
    }
    
    // TODO スタブ解除
    /**
     * stub for price test checkChargeRecord
     *
     * @param  $item_id
     *       $item_no
     *       &$credit_url 
     * @return "true"      IDServerと連携している、課金前
     *       "false"      IDServerと連携していない
     *       "unknown"  クレジットカード情報なし
     *       "shared"     クレジットカード登録不可(共有アカウント)
     *       "credit"    クレジットカード情報エラー
     *       "already"  課金済
     */
/*    function checkChargeRecord($item_id, $item_no, &$credit_url){
        $user = $this->Session->getParameter("_login_id");
        if($user == "userCmn" || $user == "userCmnGrp"){
            return "shared";
        }
        
        if($user == "userGuest_ER" || $user == "userAAA_ER"){
            return "credit";
        }
        
        if($user == "kuserGuest" 
        || $user == "kuserGuest_ER" 
        || $user == "kuserIns" 
        || $user == "kuserAAA" 
        || $user == "kuserAAA_ER" 
        || $user == "kadmin" ){
            return "already";
        }
        
//      return "credit";
//      return "connection";
//      return "unknown";
//      return "shared";
//      return "already";
        $testPay = $this->Session->getParameter("testPay");
        if($testPay == "true")
        {
            return "already";
        }
        if($this->pay === "true"){
            $this->Session->setParameter("testPay" ,"true");
        }
        
        if($this->fromCommonDownload)
        {
            $trade_id = 1;
            return $trade_id;
        }
        else
        {
            return "true";
        }
    }
    */
    // TODO スタブ解除
    
    /**
     * close charge action
     * 課金処理を確定する
     * 
     * @param string $trade_id Unique ID of the request that charge 課金したリクエストのユニークID
     * @return boolean Result 結果
     *                 true => success / false => failed
     *                 true => 課金成功 / false => 課金失敗 
     */
    function closeCharge($trade_id){
        // close charge URL
        $charge_pass = $this->getChargePass();
        $send_param =   "https://".$charge_pass["charge_id"].":".$charge_pass["charge_pass"]."@".
                        $charge_pass["charge_fqdn"].
                        "/charge/close?".
                        "sys_id=".$charge_pass["sys_id"]. //sys_id :WEKOシステムを識別するID(現在 "weko01" のみ有効です)
                        // Fix change WEKO's user_id to WEKO'slogin_id 2008/10/30 Y.Nakao
                        "&user_id=".$this->Session->getParameter("_login_id").// user_id :利用者のWEKO_ID(LDAPと連携までは何でも通します)
                        "&trade_id=".$trade_id; // trade_id
        // HTTP_Request init
        // send http request
        $option = array( 
            "timeout" => "10",
            "allowRedirects" => true, 
            "maxRedirects" => 3, 
        );
        // Modfy proxy 2011/12/06 Y.Nakao --start--
        $proxy = $this->RepositoryAction->getProxySetting();
        if($proxy['proxy_mode'] == 1)
        {
            $option = array( 
                    "timeout" => "10",
                    "allowRedirects" => true, 
                    "maxRedirects" => 3,
                    "proxy_host"=>$proxy['proxy_host'],
                    "proxy_port"=>$proxy['proxy_port'],
                    "proxy_user"=>$proxy['proxy_user'],
                    "proxy_pass"=>$proxy['proxy_pass']
                );
        }
        // Modfy proxy 2011/12/06 Y.Nakao --end--
        $http = new HTTP_Request($send_param, $option);
        
        // setting HTTP header
        $http->addHeader("User-Agent", $_SERVER['HTTP_USER_AGENT']); 
        $http->addHeader("Referer", $_SERVER['HTTP_REFERER']);
        
        // run HTTP request 
        $response = $http->sendRequest(); 
        if (!PEAR::isError($response)) { 
            $charge_code = $http->getResponseCode();// ResponseCode(200等)を取得 
            $charge_header = $http->getResponseHeader();// ResponseHeader(レスポンスヘッダ)を取得 
            $charge_body = $http->getResponseBody();// ResponseBody(レスポンステキスト)を取得 
            $charge_Cookies = $http->getResponseCookies();// クッキーを取得 
        }
        $result_js = $charge_body;
        
        $json = new Services_JSON();
        $decoded = $json->decode($result_js);
        
        if($decoded->charge_status == "0"){
            return false;
        }
        
        return true;
    }
    // Add download action for repository_uri called 2009/10/02 A.Suzuki --end--
    
    /**
     * Connection information acquisition to the accounting server.
     * 課金サーバーへの接続情報取得
     *
     * @return array Connection information to the billing server課金サーバーへの接続情報
     *               array["charge_id"|"charge_pass"|"charge_fqdn"|"user_fqdn"|"sys_id"]
     */
    function getChargePass(){
        $config = parse_ini_file(BASE_DIR.'/webapp/modules/repository/config/main.ini');
        $ret_info = array();
        // Fix there parameter get from config file 2008/10/30 Y.Nakao --start--
        $ret_info = array("charge_id" => $config["define:_REPOSITORY_CHARGE_ID"],
                          "charge_pass" => $config["define:_REPOSITORY_CHARGE_PASS"],
                          "charge_fqdn" => $config["define:_REPOSITORY_CHARGE_FQDN"],
                          "user_fqdn" => $config["define:_REPOSITORY_USER_FQDN"],
                          "sys_id" => $config["define:_REPOSITORY_CHARGE_SYSID"]
                    );
        // Fix there parameter get from config file 2008/10/30 Y.Nakao --end--
        return $ret_info;
    }
    
    /**
     * check price for access user, and highlight
     * ユーザに合わせて課金額をハイライト表示する
     *
     * @param array $file_info file information. ファイルを特定するための情報
     * @param string $status Whether or not charged 課金されているか否か
     * @return boolean|array Result 結果
     *                       false => None Highlights ( download authority None or administrator ). ハイライトなし（ダウンロード権限なしor管理者) 
     *                       array => Highlight information of download amount of access users. アクセスユーザーのダウンロード金額のハイライト情報
     *                       array[$ii]
     */
    function checkPriceAccent($file_info, $status="true"){
        ///// get groupID and price /////
        $query = "SELECT price FROM ". DATABASE_PREFIX ."repository_file_price ".
                 "WHERE item_id = ? AND ".
                 "item_no = ? AND ".
                 "attribute_id = ? AND ".
                 "file_no = ? AND ".
                 "is_delete = 0; ";
        $params = array();
        $params[] = $file_info["item_id"];
        $params[] = $file_info["item_no"];
        $params[] = $file_info["attribute_id"];
        $params[] = $file_info["file_no"];
        $group_price = $this->Db->execute( $query, $params );
        if($group_price === false){
            return false;
        }
        $accent_array = array();
        $accent_room_id = array();
        if(!isset($group_price[0]["price"])){
            return $accent_array;
        }
        $room_price = explode("|", $group_price[0]["price"]);
        ///// ユーザが入っているグループIDを取得 /////
        $result = $this->RepositoryAction->getUsersGroupList($user_group,$error_msg);
        if($result===false){
            return false;
        }
        $file_price = "";
        for($price_Cnt=0;$price_Cnt<count($room_price);$price_Cnt++){
            $accent_flg = "false";
            $price = explode(",", $room_price[$price_Cnt]);
            // There is a pair of room_id and the price. 
            if($price!=null && count($price)==2 && $this->Session->getParameter("_user_id")!="0")
            {
                // It is judged whether it is user's belonging group.
                for($user_group_cnt=0;$user_group_cnt<count($user_group);$user_group_cnt++){
                    if($price[0] == $user_group[$user_group_cnt]["room_id"]){
                        // When the price is set to the belonging group
                        if($file_price==""){
                            // The price is maintained at the unsetting.
                            $file_price = $price[1];
                            $accent_flg = "true";
                        } else if(intval($file_price) > intval($price[1])){
                            // It downloads it by the lowest price. 
                            $file_price = $price[1];
                            $accent_flg = "true";
                        } else if(intval($file_price) == intval($price[1])){
                            // same the lowest price. 
                            $accent_flg = "same";
                        }
                    }
                }
            }
            
            // アクセントフラグチェック
            if($accent_flg == "true"){
                // 最安値更新
                for($ii=0;$ii<count($accent_array);$ii++){
                    $accent_array[$ii] = "false";
                }
                array_push($accent_array, $status);
                array_push($accent_room_id, $price[0]);
            } else if($accent_flg == "same"){
                // 同価格
                for($ii=0;$ii<count($accent_array);$ii++){
                    if($accent_array[$ii] == "true"){
                        // 非会員と価格が同じ場合、非会員はハイライトをつけない
                        if($accent_room_id[$ii] == 0){
                            $accent_array[$ii] = "false";
                        }
                    }
                }
                array_push($accent_array, $status);
                array_push($accent_room_id, $price[0]);
            } else {
                // 該当せず
                array_push($accent_array, "false");
                array_push($accent_room_id, $price[0]);
            }
        }
        return $accent_array;
    }
    // Add put the accent on user's price 2009/05/28 A.Suzuki --end--
    
    // Modify add memo for charge record. 2012/02/28 Y.Nakao --start--
    /**
     * get charge memo
     * 課金時のコメント取得
     *
     * @param int $item_id Item id アイテムのユニークID
     * @param int $item_no Item no アイテム通番
     * @return string charge memo Billing at the time of the comment information ( enumeration of affiliation index name). 課金時のコメント情報（所属インデックス名の列挙）
     */
    private function getChargeMemo($item_id, $item_no)
    {
        // memo for index tree list
        $memo = '';
        
        // get position index_id
        $query = "SELECT index_id ".
                " FROM ".DATABASE_PREFIX."repository_position_index ".
                " WHERE item_id = ? ".
                " AND   item_no = ? ".
                " AND   is_delete = ? ";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 0;
        $posIdxList = $this->Db->execute($query, $params);
        if($posIdxList === false || count($posIdxList) == 0)
        {
            return "";
        }
        for($ii=0; $ii<count($posIdxList); $ii++)
        {
            $index = '';
            $idxList = array();
            $this->RepositoryAction->getParentIndex($posIdxList[$ii]['index_id'], $idxList);
            for($jj=0; $jj<count($idxList); $jj++)
            {
                if(strlen($index) > 0)
                {
                    $index .= ',';
                }
                if(strlen($idxList[$jj]['index_name']) > 0)
                {
                    $index .= $idxList[$jj]['index_name'];
                }
                else
                {
                    $index .= $idxList[$jj]['index_name_english'];
                }
            }
            if(strlen($memo) > 0)
            {
                $memo .= '|';
            }
            $memo .= $index;
        }
        return urlencode($memo);
    }
    // Modify add memo for charge record. 2012/02/28 Y.Nakao --end--
    
    // Add check charge record from log table 2008/10/16 Y.Nakao --start--
    /**
     * Get billing records.
     * 課金レコード取得
     * 
     * @param string $content_id Unique ID of the billing content. 課金コンテンツのユニークID(課金時に指定したアイテムのキー<prefix/suffix>) 
     * @return array|string string Failed 失敗
     *                      array billing records 課金レコード
     */
    function getChargeRecord($content_id){
        ////////// get charge record //////////
        // request uri is write ASCII
        // create charge URL
        $charge_pass = $this->getChargePass();
        $send_param =   "https://".$charge_pass["charge_id"].":".$charge_pass["charge_pass"]."@".
                        $charge_pass["charge_fqdn"].
                        "/charge/show?".
                        "sys_id=".$charge_pass["sys_id"]. //sys_id :WEKOシステムを識別するID(現在 "weko01" のみ有効です)
                        // Fix change WEKO's user_id to WEKO'slogin_id 2008/10/30 Y.Nakao
                        "&user_id=".$this->Session->getParameter("_login_id");// user_id :利用者のWEKO_ID(LDAPと連携までは何でも通します)
        if($content_id != ""){
            $send_param .= "&content_id=".$content_id;
        }
        // HTTP_Request init
        // send http request
        $option = array( 
            "timeout" => "10",
            "allowRedirects" => true, 
            "maxRedirects" => 3, 
        );
        // Modfy proxy 2011/12/06 Y.Nakao --start--
        $proxy = $this->RepositoryAction->getProxySetting();
        if($proxy['proxy_mode'] == 1)
        {
            $option = array( 
                    "timeout" => "10",
                    "allowRedirects" => true, 
                    "maxRedirects" => 3,
                    "proxy_host"=>$proxy['proxy_host'],
                    "proxy_port"=>$proxy['proxy_port'],
                    "proxy_user"=>$proxy['proxy_user'],
                    "proxy_pass"=>$proxy['proxy_pass']
                );
        }
        // Modfy proxy 2011/12/06 Y.Nakao --end--
        $http = new HTTP_Request($send_param, $option);
        
        // setting HTTP header
        $http->addHeader("User-Agent", $_SERVER['HTTP_USER_AGENT']); 
        $http->addHeader("Referer", $_SERVER['HTTP_REFERER']);
        
        // run HTTP request 
        $response = $http->sendRequest(); 
        if (!PEAR::isError($response)) { 
            $charge_code = $http->getResponseCode();// ResponseCode(200等)を取得 
            $charge_header = $http->getResponseHeader();// ResponseHeader(レスポンスヘッダ)を取得 
            $charge_body = $http->getResponseBody();// ResponseBody(レスポンステキスト)を取得 
            $charge_Cookies = $http->getResponseCookies();// クッキーを取得 
        }
        $result_js = $charge_body;

        if($charge_code == "200"){
            return $result_js;
        } else {
            return "false";
        }
        
    }
    // Add check charge record from log table 2008/10/16 Y.Nakao --end--
    
    // Add check sitelicense group 2015/01/19 T.Ichikawa --start--
    /**
     * check sitelicense group
     * サイトライセンス認可機関に所属しているかを確認する
     *
     * @param array Site license authorized institution to which the user belongs. ユーザーが所属するサイトライセンス認可機関
     * @return boolean sitelicense flag サイトライセンスフラグ
     */
    public function checkSiteLicenseGroup(&$sitelicense_id)
    {
        // ユーザー所属組織情報の取得
        $query = "SELECT content FROM ". DATABASE_PREFIX. "users_items_link ".
                 "WHERE user_id = ? ".
                 "AND item_id = ? ;";
        $params = array();
        $params[] = $this->Session->getParameter("_user_id");
        $params[] = 8;
        $result = $this->Db->execute($query, $params);
        if($result == false) {
            return false;
        }
        // ユーザーが組織に所属している場合、それがサイトライセンス組織であるか判定する
        if(isset($result) && count($result) > 0 && strlen($result[0]["content"]) > 0) {
            // サイトライセンス組織情報の取得
            $query = "SELECT organization_id, group_name FROM ". DATABASE_PREFIX. "repository_sitelicense_info ".
                     "WHERE is_delete = ? ;";
            $params = array();
            $params[] = 0;
            $sitelicense_groups = $this->Db->execute($query, $params);
            if($result == false) {
                return false;
            }
            // チェック処理
            for($ii = 0; $ii < count($sitelicense_groups); $ii++) {
                // ユーザーの所属組織名がサイトライセンス組織名と一致した場合trueを返す
                if($result[0]["content"] == $sitelicense_groups[$ii]["group_name"]) {
                    $sitelicense_id = $sitelicense_groups[$ii]["organization_id"];
                    return true;
                }
            }
        }
        
        return false;
    }
    // Add check sitelicense group 2015/01/19 T.Ichikawa --end--
    
    // Add File replace T.Koyasu 2016/02/29 --start--
    /**
     * In order to determine whether it is possible to download at a higher processing, return the access status of the old version of the file
     * 上位処理でダウンロード可能かどうかを判断するため、古いバージョンファイルのアクセスステータスを返す
     *
     * @param int $itemId Item id アイテムID
     * @param int %itemNo Item no アイテムNo
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param int $version File version ファイルのバージョン
     * @return string Access status アクセスステータス
     *                              "not_found" => Version file (physically) can not download because or authority that does not exist is not enough バージョンファイルが(物理的に)存在しない or 権限が足りないのでダウンロード不可
     *                              "admin" => Administrator. Available for download version file 管理者。バージョンファイルがダウンロード可能
     *                              "free" => Not an administrator, but the version files are available for download 管理者ではないが、バージョンファイルはダウンロード可能
     */
    private function checkVersionFileAccessStatus($itemId, $itemNo, $attrId, $fileNo, $ver){
        // 存在確認を行い、バージョンファイルが存在しないならファイルがないことを示すステータスを返す(ファイルが存在しません、となるので既存のものとは別。上位関数の処理も修正する必要あり)
        $query = "SELECT version, file_shown_state, file_update_user_id ". 
                 " FROM ". DATABASE_PREFIX. "repository_file_update_history ". 
                 " WHERE item_id = ? ". 
                 " AND item_no = ? ". 
                 " AND attribute_id = ? ". 
                 " AND file_no = ? ". 
                 " AND version = ? ". 
                 " AND is_delete = ?;";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attrId;
        $params[] = $fileNo;
        $params[] = $ver;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false || count($result) === 0){
            // データベースアクセス時にエラーが発生している⇒ダウンロード不可（ステータス＝ファイルが存在しない）
            return self::ACCESS_STATUS_FILE_IS_NOT_FOUND;
        }
        $fileShownStatus = $result[0]["file_shown_state"];
        
        // バージョンファイルが物理的に存在しない ⇒ ダウンロード不可（ステータス＝ファイルが存在しない）
        $verFilePath = $this->generateVersionFilePath($itemId, $attrId, $fileNo, $ver);
        if(!file_exists($verFilePath)){
            return self::ACCESS_STATUS_FILE_IS_NOT_FOUND;
        }
        
        // 管理者またはアイテム登録者である ⇒ ダウンロード可能
        if($this->isAdmin($itemId, $itemNo)){
            return self::ACCESS_STATUS_ADMIN;
        }
        
        // 管理者またはアイテム登録者ではない かつ バージョンファイルの公開ステータス「非公開」の場合
        // ログイン済 ⇒ ダウンロード不可（ステータス＝閲覧権限なし）
        // 未ログイン ⇒ ログイン要求
        if($fileShownStatus == self::VERSION_FILE_SHOWN_STATE_PRIVATE){
            
            $login_id = $this->Session->getParameter("_login_id");
            $user_id = $this->Session->getParameter("_user_id");
            
            if($user_id != "0" && strlen($login_id) != 0)
            {
                // ログイン済
                return self::ACCESS_STATUS_CLOSE;
            }
            else
            {
                // 未ログイン
                return self::ACCESS_STATUS_LOGIN;
            }
        }
        
        // ダウンロードOK
        return self::ACCESS_STATUS_FREE;
    }
    
    /**
     * It returns the path of the old version of the file specified version
     * 指定したバージョンの旧バージョンファイルのパスを返す
     *
     * @param int $itemId Item id アイテムID
     * @param int $attrId Attribute id 属性ID
     * @param int $fileNo File number ファイル通番
     * @param int $version File version ファイルバージョン
     * @return string the path of the old version of the file 旧バージョンファイルのパス
     */
    private function generateVersionFilePath($itemId, $attrId, $fileNo, $version){
        // 拡張子を取得
        $query = "SELECT physical_file_name ". 
                 " FROM ". DATABASE_PREFIX. "repository_file_update_history ".
                 " WHERE item_id = ? ".
                 " AND item_no = ? ". 
                 " AND attribute_id = ? ". 
                 " AND file_no = ? ". 
                 " AND version = ?;";
        $params = array();
        $params[] = $itemId;
        $params[] = 1;
        $params[] = $attrId;
        $params[] = $fileNo;
        $params[] = $version;
        $result = $this->Db->execute($query, $params);
        $pathInfo = pathinfo($result[0]["physical_file_name"]);
        $extension = $pathInfo["extension"];
        
        $path = "";
        $path = WEBAPP_DIR. DIRECTORY_SEPARATOR. "uploads/repository/versionFiles". DIRECTORY_SEPARATOR;
        $path .= $itemId. "_". $attrId. "_". $fileNo. DIRECTORY_SEPARATOR. $version. ".". $extension;
        
        return $path;
    }
    
    /**
     * Access user determines whether the administrator ( the owner of the administrator or item of WEKO).
     * アクセスユーザーが管理者（WEKOの管理者またはアイテムの登録者）であるか判定する。
     * 
     * @param $item_id int ItemId. アイテムId
     * @param $item_no int ItemNo. アイテムNo
     * @return bool Whether or not the administrator 管理者であるか否か（true=>管理者である / false=>管理者ではない）
     */
    private function isAdmin($item_id, $item_no){
        // アクセスユーザー情報取得
        $user_id = $this->Session->getParameter("_user_id");
        $login_id = $this->Session->getParameter("_login_id");
        $user_auth_id = $this->Session->getParameter("_user_auth_id");
        $auth_id = $this->RepositoryAction->getRoomAuthorityID();
        
        if( !($user_id != "0" && strlen($login_id) != 0) )
        {
            // 未ログイン
            return false;
        }
        
        // check admin user
        // WEKO管理者か判定
        if( $user_auth_id >= $this->RepositoryAction->repository_admin_base && 
            $auth_id >= $this->RepositoryAction->repository_admin_room){
            // WEKO管理者である
            return true;
        }
        
        // アイテムの登録ユーザーIDを取得
        $query = "SELECT ".RepositoryConst::DBCOL_COMMON_INS_USER_ID." ".
                " FROM {".RepositoryConst::DBTABLE_REPOSITORY_ITEM."} ".
                " WHERE ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_ID." = ? ".
                " AND ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_NO." = ? ".
                " AND ".RepositoryConst::DBCOL_COMMON_IS_DELETE." = ?; ";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false || count($result) != 1)
        {
            // データベースエラー。アイテム登録者と判定できなかった。
            return false;
        }
        
        // check insert user
        if( $user_id == $result[0][RepositoryConst::DBCOL_COMMON_INS_USER_ID]){
            // アクセスユーザー＝アイテム登録者である。
            return true;
        }
        
        return false;
    }
    // Add File replace T.Koyasu 2016/02/29 --end--
}
?>