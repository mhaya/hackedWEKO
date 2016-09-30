<?php

/**
 * Action class for bulk download of string attached file to an item
 * アイテムに紐付くファイルの一括ダウンロード用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Filedownload.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * ZIP file manipulation library
 * ZIPファイル操作ライブラリ
 */
include_once MAPLE_DIR.'/includes/pear/File/Archive.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Item export processing common classes
 * アイテムエクスポート処理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/action/main/export/ExportCommon.class.php';
/**
 * Common class file download
 * ファイルダウンロード共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';

/**
 * Action class for bulk download of string attached file to an item
 * アイテムに紐付くファイルの一括ダウンロード用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Main_Export_Filedownload extends RepositoryAction
{
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
    
    // リクエストパラメータを受け取るため
    //var $item_type_id = null;     //前画面で選択したアイテムタイプID(編集時)
    /**
     * Item id
     * アイテムID
     *
     * @var int
     */
    var $item_id = null;
    /**
     * Item serial number
     * アイテム通番
     *
     * @var int
     */
    var $item_no = null;
    /**
     * Attribute id
     * 属性ID
     *
     * @var int
     */
    var $attribute_id = null;
    /**
     * File only flag
     * ファイルのみフラグ
     *
     * @var string
     */
    var $file_only = null;
    
    /**
     * To download the string attached file to an item
     * アイテムに紐付くファイルをダウンロードする
     *
     * @access  public
     * @return string Result 結果
     */
    function execute()
    {
        try {
            $this->exitFlag = true;
            
            // 共通の初期処理
            $result = $this->initAction();
            if ( $result == false ){
                // 未実装
                print "初期処理でエラー発生";
            }
            
            // Add check closed index 2010/01/08 Y.Nakao --start--
            $this->Session->removeParameter("uri_export");
            // ダウンロード可能なファイルをチェック
            // check download files
            if( strlen($this->item_id) == 0 || 
                strlen($this->item_no) == 0 ||
                strlen($this->attribute_id) == 0){
                // view呼び出しの場合、ブラウザのアドレスがこのアクションを指したままになるのでリダイレクトする
                // get block_id and page_id
                $block_info = $this->getBlockPageId();
                $redirect_url = BASE_URL.
                                "/?action=pages_view_main".
                                "&page_id=". $block_info["page_id"] .
                                "&block_id=". $block_info["block_id"];
                // redirect
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$redirect_url);
            }
            // Fix can download from closed item. 2013.12.17 Y.Nakao --strat--
            require_once WEBAPP_DIR. '/modules/repository/validator/Validator_DownloadCheck.class.php';
            $validator = new Repository_Validator_DownloadCheck();
            $initResult = $validator->setComponents($this->Session, $this->Db);
            if($initResult === 'error'){
                return 'error';
            }
            if(!$validator->checkCanItemAccess($this->item_id, $this->item_no))
            {
                // closed item, return detail view.
                $redirect_url = BASE_URL.
                                "/?action=repository_uri".
                                "&item_id=".$this->item_id.
                                "&item_no=".$this->item_no;
                // redirect
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$redirect_url);
            }
            
            // Fix can download from closed item. 2013.12.17 Y.Nakao --end--
            
            ///// file download /////
            $query = "SELECT * ".
                    " FROM ". DATABASE_PREFIX ."repository_file ".
                    " WHERE item_id = ? ".
                    " AND item_no = ? ".
                    " AND attribute_id = ? ;";
            $params = array();
            $params[] = $this->item_id;
            $params[] = $this->item_no;
            $params[] = $this->attribute_id;
            $ret = $this->Db->execute($query, $params);
            // SQLエラーの場合 終了
            if ($ret === false) {
                $error_msg = $this->Db->ErrorMsg();
                return;
            }           
            // Modify Price method move validator K.Matsuo 2011/10/19 --start--
            for($ii=0; $ii<count($ret); $ii++)
            {
                // Fix file download check Y.Nakao 2013/04/11 --start--
                // check file access
                $status = $validator->checkFileAccessStatus($ret[$ii]);
                if( $status == "free" || $status == "already" || $status == "admin" || $status == "license" )
                {
                    // this file use can download
                }
                else if($status == "login")
                {
                    // call login action
                    $uri_export = array();
                    $uri_export["item_id"] = $this->item_id;
                    $uri_export["item_no"] = "1";
                    $uri_export["attribute_id"] = $this->attribute_id;  // attribute_id
                    $uri_export["status"] = "login";
                    $this->Session->setParameter("uri_export", $uri_export);
                    // view呼び出しの場合、ブラウザのアドレスがこのアクションを指したままになるのでリダイレクトする
                    // get block_id and page_id
                    $block_info = $this->getBlockPageId();
                    $redirect_url = BASE_URL.
                                    "/?action=pages_view_main".
                                    "&page_id=". $block_info["page_id"] .
                                    "&block_id=". $block_info["block_id"];
                    // redirect
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".$redirect_url);
                    exit();
                }
                else
                {
                    // view呼び出しの場合、ブラウザのアドレスがこのアクションを指したままになるのでリダイレクトする
                    // get block_id and page_id
                    $block_info = $this->getBlockPageId();
                    $redirect_url = BASE_URL.
                                    "/?action=pages_view_main".
                                    "&active_action=repository_view_main_item_detail".
                                    "&item_id=".$this->item_id.
                                    "&item_no=".$this->item_no.
                                    "&page_id=". $block_info["page_id"] .
                                    "&block_id=". $block_info["block_id"];
                    // redirect
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".$redirect_url);
                    exit();
                }
                // Fix file download check Y.Nakao 2013/04/11 --end--
            }
            // Add check closed index 2010/01/08 Y.Nakao --end--
            // Modify Price method move validator K.Matsuo 2011/10/19 --end--
            
            // 作業用ディレクトリ作成
            $this->infoLog("businessWorkdirectory", __FILE__, __CLASS__, __LINE__);
            $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness("businessWorkdirectory");
            
            $tmp_dir = $businessWorkdirectory->create();
            
            // Exportファイル生成
            $export_common = new ExportCommon($this->Db, $this->Session, $this->TransStartDate);
            if($export_common === null){
                return false;
            }
            
            $idx = 0;
            $result = $this->getFilePriceTableData($this->item_id, $this->item_no, $this->attribute_id, $idx, $Result_List, $error_msg);
            if($result === false){
                return false;
            }
            
            $buf = '';
            $output_files = array();
            $dirCountForFile = 1;
            $result = $export_common->getFileXMLData($buf, $Result_List['item_attr'][$idx], $tmp_dir, $output_files, true, '', true, $ii, $dirCountForFile);
            if($result === false){
                
            }
            
            // Zipファイル生成
            if($this->file_only == "true"){
                $zip_file = "contents.zip";
            } else {
                $zip_file = "export.zip";
            }
            
            // zip用の一時ディレクトリを作成
            $tmp_dir_zip = $businessWorkdirectory->create();
            
            File_Archive::extract(
                File_Archive::read($tmp_dir),
                File_Archive::toArchive($zip_file, File_Archive::toFiles( $tmp_dir_zip ))
            );
            
            //ダウンロードアクション処理
            // Add RepositoryDownload action 2010/03/30 A.Suzuki --start--
            $repositoryDownload = new RepositoryDownload();
            if($this->file_only == "true"){
                $repositoryDownload->downloadFile($tmp_dir_zip.$zip_file, "contents.zip");
            } else {
                $repositoryDownload->downloadFile($tmp_dir_zip.$zip_file, "export.zip"); 
            }
            // Add RepositoryDownload action 2010/03/30 A.Suzuki --start--
            
            // 本来であればexitActionをよび、その中で
            // finalize処理を実施するべきだが、
            // コミットによる影響があるため、finalize処理のみを実施
            $this->finalize();
            
        } catch ( RepositoryException $exception){
            // 未実装
        }
    }
}
?>
