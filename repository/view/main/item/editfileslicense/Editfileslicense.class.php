<?php

/**
 * Item register: View for setting file license
 * アイテム登録：ファイルライセンス設定画面表示
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Editfileslicense.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR. '/modules/repository/components/common/WekoAction.class.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Item regsiter class
 * アイテム登録処理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/ItemRegister.class.php';
/**
 * Handle manager class
 * ハンドル管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryHandleManager.class.php';

/**
 * Item register: View for setting file license
 * アイテム登録：ファイルライセンス設定画面表示
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Main_Item_Editfileslicense extends WekoAction
{
    // 表示用パラメーター
    /**
     * Row number
     * 行番号
     *
     * @var array
     */
    public $row_num = array();
    
    /**
     * Flash display convertible flag array
     * フラッシュ表示選択可能フラグ配列
     *
     * @var array
     */
    public $flash_convertible = array();

    /**
     * Display help icon flag
     * ヘルプアイコン表示フラグ
     *
     * @var string
     */
    public $help_icon_display =  "";
    
    // リクエストパラメーター
    /**
     * Warning message
     * 警告メッセージ配列
     *
     * @var array
     */
    public $warningMsg = null;
    
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws AppException
     */
    protected function executeApp()
    {
        // RepositoryActionのインスタンス
        $repositoryAction = new RepositoryAction();
        $repositoryAction->Session = $this->Session;
        $repositoryAction->Db = $this->Db;
        $repositoryAction->dbAccess = $this->Db;
        $repositoryAction->TransStartDate = $this->accessDate;
        
        // ファイル(not multimedia)のFLASH変換はIDサーバと連携している場合のみ
        $fileConvertFlag = false;
        
        // prefixIDをDBから取得
        $repositoryHandleManager = new RepositoryHandleManager($this->Session, $this->Db, $this->accessDate);
        $prefixID = $repositoryHandleManager->getPrefix(RepositoryHandleManager::ID_Y_HANDLE);
        if(strlen($prefixID) > 0){
            $fileConvertFlag = true;
        }
        
        // マルチメディアファイルのFLVへの変換はffmpegが使用可能な場合のみ
        $itemRegister = new ItemRegister($this->Session, $this->Db);
        $multimediaConvertFlag = $itemRegister->getIsValidFfmpeg();
        
        // テーブル描画用Row数情報を作成
        $item_attr = $this->Session->getParameter("item_attr");
        $item_attr_type = $this->Session->getParameter("item_attr_type");
        
        $this->row_num = array_fill(0, count($item_attr), 0);
        for ($ii = 0; $ii < count($item_attr_type); $ii++) {
            if($item_attr_type[$ii]['input_type'] == "file" || $item_attr_type[$ii]['input_type']=='file_price'){
                for ($jj = 0; $jj < count($item_attr[$ii]); $jj++) {
                    if ($item_attr[$ii][$jj] != null) {
                        // ファイルが存在している個数分、Row数を増やしていく
                        if($item_attr_type[$ii]['input_type']=='file_price')
                        {
                            $this->row_num[$ii] += 2;
                        } else {
                            $this->row_num[$ii]++;
                        }
                        // Add convert to flash 2010/02/10 A.Suzuki --start--
                        $this->flash_convertible[$ii][$jj] = null;
                        if (array_key_exists('upload', $item_attr[$ii][$jj]) && $item_attr[$ii][$jj]['upload'] != null)
                        {
                            $extension = strtolower($item_attr[$ii][$jj]['upload']['extension']);
                            switch($extension){
                                case "swf":
                                case "flv":
                                    // swf, flv の場合はFlash表示を常に選択可能
                                    $this->flash_convertible[$ii][$jj] = "true";
                                    break;
                                case "doc":
                                case "docx":
                                case "xls":
                                case "xlsx":
                                case "ppt":
                                case "pptx":
                                case "pdf":
                                    if($fileConvertFlag){
                                        // ファイルのFLASH変換はIDサーバと連携している場合のみ
                                        $this->flash_convertible[$ii][$jj] = "true";
                                    }
                                    break;
                                case "emf":
                                case "wmf":
                                case "bmp":
                                case "png":
                                case "gif":
                                case "tiff":
                                case "jpg":
                                case "jpeg":
                                case "jp2":
                                    $this->flash_convertible[$ii][$jj] = "true";
                                    break;
                                default :
                                    if( $multimediaConvertFlag &&
                                        $repositoryAction->isMultimediaFile(
                                                strtolower($item_attr[$ii][$jj]['upload']['mimetype']),
                                                strtolower($item_attr[$ii][$jj]['upload']['extension'])))
                                    {
                                        // マルチメディアファイルのFLVへの変換はffmpegが使用可能な場合のみ
                                        $this->flash_convertible[$ii][$jj] = "true";
                                    }
                                    break;
                            }
                        }
                        // Add convert to flash 2010/02/10 A.Suzuki --end--
                    }
                }
            }
        }
        
        // Set help icon setting
        $tmpErrorMsg = "";
        $result = $repositoryAction->getAdminParam('help_icon_display', $this->help_icon_display, $tmpErrorMsg);
        if ( $result === false ){
            $this->errorLog($tmpErrorMsg, __FILE__, __CLASS__, __LINE__);
            $exception = new AppException($tmpErrorMsg);
            $exception->addError($tmpErrorMsg);
            throw $exception;
        }
        
        return 'success';
    }
}
?>
