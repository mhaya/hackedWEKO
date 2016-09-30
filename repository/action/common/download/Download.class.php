<?php

/**
 * Download Action class of registered file in WEKO
 * WEKOに登録されたファイルのダウンロードアクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Download.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Common class file download
 * ファイルダウンロード共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';

/**
 * Download Action class of registered file in WEKO
 * WEKOに登録されたファイルのダウンロードアクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Common_Download extends RepositoryAction
{
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
     * File number
     * ファイル通番
     *
     * @var int
     */
    var $file_no = null;
    
    /**
     * Thumbnail download flag
     * サムネイルダウンロードフラグ
     *
     * @var string|null string Thumbnail display to "true" when setting "true"設定時にサムネイル表示
     *                  null Not performed downloaded thumbnail サムネイルのダウンロードを実施しない
     */
    var $img = null;    // true設定時にサムネイル表示
    
    /**
     * Item type icon download flag
     * アイテムタイプアイコンダウンロードフラグ
     *
     * @var int|null int Item type ID アイテムタイプID
     *               null Do not download the item type icon アイテムタイプアイコンをダウンロードしない
     */
    var $item_type_id = null; // アイテムタイプアイコン追加　 2008/07/16 Y.Nakao
    
    /**
     * File preview image Download flag
     * ファイルプレビュー画像ダウンロードフラグ
     *
     * @var string|null string "True" file preview image to download when setting "true"設定時にファイルプレビュー画像をダウンロード
     *                  null Do not download files preview image ファイルプレビュー画像をダウンロードしない
     */
    var $file_prev = null;    // サムネイル追加 2008/07/22 Y.Nakao
    
    /**
     * FLASH file download flag
     * FLASHファイルダウンロードフラグ
     *
     * @var string|null string The FLASH file downloaded to the "true" when setting "true"設定時にFLASHファイルをダウンロード
     *                  null Do not download the FLASH file FLASHファイルをダウンロードしない
     */
    var $flash = null;        // フラッシュ追加 2010/01/05 A.Suzuki
    
    /**
     * Block ID of arranged WEKO to NC2
     * NC2に配置されたWEKOのブロックID
     *
     * @var int
     */
    public $block_id = "";  // ブロックID追加2011/10/13 K.Matsuo
    
    /**
     * ID of the page WEKO is located
     * WEKOが配置されているページのID
     *
     * @var int
     */
    public $page_id = "";   // ページID追加2011/10/13 K.Matsuo
    
    // オブジェクト類
    /**
     * Upload table display object
     * アップロードテーブル表示用オブジェクト
     *
     * @var Uploads_View
     */
    var $uploadsView = null;
    
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    var $Session = null;
    
    // Add index thumbnail 2010/08/11 Y.Nakao --start--
    /**
     * Index thumbnail download flag
     * インデックスサムネイルダウンロードフラグ
     *
     * @var int|null int IndexID インデックスID
     *               null Do not download the index thumbnail インデックスサムネイルをダウンロードしない
     */
    public $index_id = null;
    // Add index thumbnail 2010/08/11 Y.Nakao --end--
    
    /**
     * The name of the download to FLASH files
     * ダウンロードするFLASHファイルの名称
     *
     * @var string
     */
    public $flashpath = null;
    
    // Add PDF cover page 2012/06/13 A.Suzuki --start--
    /**
     * PDF cover page header image download flag
     * PDFカバーページヘッダ画像ダウンロードフラグ
     *
     * @var string|null string PDF cover page header image downloaded to the "true" when setting "true"設定時にPDFカバーページヘッダ画像をダウンロード
     *                  null Do not download PDF cover page header image PDFカバーページヘッダ画像をダウンロードしない
     */
    public $pdf_cover_header = null;
    // Add PDF cover page 2012/06/13 A.Suzuki --end--
    
    // Add advanced image thubnail 2014/02/13 R.Matsurua --start--
    /**
     * Download flag of the image file FLASH display is specified
     * FLASH表示が指定された画像ファイルのダウンロードフラグ
     *
     * @var string|null string The image file FLASH display downloaded to the "true" when setting "true"設定時にFLASH表示が指定された画像ファイル
     *                  null Do not download the image file FLASH display FLASH表示が指定された画像ファイルをダウンロードしない
     */
    public $image_slide = null;
    // Add advanced image thubnail 2014/02/13 R.Matsurua --end--
    
    /**
     * Version information for specifying the saved old file
     * 退避した古いファイルを特定するためのバージョン情報
     *
     * @var int
     */
    public $ver = null;
    
    /**
     * Download the file specified by the request parameter
     * リクエストパラメータで指定されたファイルをダウンロード
     */
    function executeApp()
    {
        // 正常終了フラグを有効にする（異常終了の場合は例外で脱出する）
        $this->exitFlag = true;
        
        /////////////// download and view item type icon ///////////////
        // Add item type icon download 2008/07/16 Y.Nakao --start--
        if($this->item_type_id != null){
            $this->getItemTypeIcom();
            return;
        }
        // Add item type icon download 2008/07/16 Y.Nakao --end--
        
        // Add index thumbnail 2010/08/11 Y.Nakao --start--
        if($this->index_id != null){
            $this->getIndexThumbnail();
            return;
        }
        // Add index thumbnail 2010/08/11 Y.Nakao --start--
        
        // Add PDF cover page 2012/06/13 A.Suzuki --start--
        if($this->pdf_cover_header != null && $this->pdf_cover_header == "true"){
            $this->getPdfCoverImage();
            return;
        }
        // Add PDF cover page 2012/06/13 A.Suzuki --end--
        
        // request param check
        if (!is_numeric($this->item_id) || $this->item_id < 1 || !is_numeric($this->item_no) || $this->item_no < 1 ||
            !is_numeric($this->attribute_id) || $this->attribute_id < 1 || !is_numeric($this->file_no) || $this->file_no < 1) {
            $error_msg = "Invalid request parameter";
            $this->errorLog($error_msg, __FILE__, __CLASS__, __LINE__);
            throw new AppException($error_msg);
        }
        
        /////////////// view prev ///////////////
        // Add prev 2008/07/22 Y.Nakao --start--
        if($this->file_prev == "true")
        {
            $this->getFilePreview();
            return;
        }       
        // Add prev 20008/07/22 Y.Nakao --end--
        
        /////////////// download thumbnail ///////////////
        if ($this->img == "true")
        {
            $this->getThumbnail();
            return;
        }
        
        
        // Add fileDownload after login 2011/10/12 K.Matsuo  
        $this->Session->removeParameter('repository'.$this->block_id.'FileDownloadKey');
        // ### Add for Test K.Matsuo 2011/11/1
        $this->Session->removeParameter("testPay");
        
        $this->exitFlag = true;
        
        /////////////// view flash ///////////////
        // Add flash 2010/01/05 A.Suzuki --start--
        if($this->flash == "true"){
            // Add File replace T.Koyasu 2016/02/29 --start--
            // 一時ディレクトリ作成
            $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness("businessWorkdirectory");
            $tmpDirPath = $businessWorkdirectory->create();
            
            // flashファイル名を取得
            $flashFilePaths = explode("/", rtrim($this->flashpath, "/"));
            $flashFileName = $flashFilePaths[count($flashFilePaths)-1];
            
            // コピー先ファイルパス
            $flashPath = $tmpDirPath.$flashFileName;
            
            // 一時ディレクトリへflashファイルをコピー
            $business = BusinessFactory::getFactory()->getBusiness("businessContentfiletransaction");
            try{
                $business->copyPreviewTo($this->item_id, $this->attribute_id, $this->file_no, $flashFileName, $flashPath);
            } catch (AppException $e){
                header("HTTP/1.0 404 Not Found");
                $error_msg = "Download file is not exist : ".$this->flashpath;
                $this->errorLog($error_msg, __FILE__, __CLASS__, __LINE__);
                return;
            }
            // Add File replace T.Koyasu 2016/02/29 --end--
            
            // Add multiple FLASH files download 2011/02/04 Y.Nakao --start--
            $mimetype = "";
            if( preg_match("/*\.flv$/", $flashFileName) === 1 )
            {
                $mimetype = "video/x-flv";
            }
            else
            {
                $mimetype = "application/x-shockwave-flash";
            }
            
            $repositoryDownload = new RepositoryDownload();
            $repositoryDownload->downloadFile($flashPath, $flashFileName, $mimetype);
            // Add multiple FLASH files download 2011/02/04 Y.Nakao --end--
            return;
        }
        // Add flash 2010/01/05 A.Suzuki --end--
        
        ///// file download /////
        $query = "SELECT extension, file_name, mime_type, pub_date, ins_user_id ".
                 "FROM ". DATABASE_PREFIX ."repository_file ".
                 "WHERE item_id = ? ".
                 "  AND item_no = ? ".
                 "  AND attribute_id = ? ".
                 "  AND file_no = ? ".
                 "  AND is_delete = ?; ";
        $params = array();
        $params[] = $this->item_id;
        $params[] = $this->item_no;
        $params[] = $this->attribute_id;
        $params[] = $this->file_no;
        $params[] = 0;
        
        $ret = $this->Db->execute($query, $params);
        
        // SQLエラーの場合 終了
        if ($ret === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // 検索結果が一件でない場合 終了
        if (count($ret) != 1) {
            $block_info = $this->getBlockPageId();
            $redirect_url = BASE_URL.
                            "/?action=pages_view_main".
                            "&active_action=repository_view_main_item_snippet".
                            "&page_id=". $block_info["page_id"].
                            "&block_id=". $block_info["block_id"];
            // redirect
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$redirect_url);
            
            return;
        }
        // Download check
        
        // Add delete pdf cover 2015/01/27 K.Matsushita -start-
        // get temporary directory path for pdfCover instance
        $tmpDirPath = $this->getDefaltTmpDirPath();
        
        $user_id = $this->Session->getParameter("_user_id");
        
        $this->infoLog("businessPdfcover", __FILE__, __CLASS__, __LINE__);
        $pdfCover = BusinessFactory::getFactory()->getBusiness("businessPdfcover");
        if(strtolower($ret[0]['extension']) == "pdf"){
            // create pdf cover page
            $download_file_path = "";
            $download_file_path = $pdfCover->grantPdfCover($this->item_id, $this->item_no, $this->attribute_id, $this->file_no, $tmpDirPath, $this->ver);
            if(file_exists($download_file_path) === TRUE){
                // 古いバージョンのファイルである場合、ファイル名などが異なる可能性があるため、データベースから取得する
                $fileName = $ret[0]['file_name'];
                $mimeType = $ret[0]['mime_type'];
                if(isset($this->ver) && $this->ver > 0){
                    $this->selectVersionFileDownloadInfo($this->item_id, $this->attribute_id, $this->file_no, $this->ver, $fileName, $mimeType);
                }
                
                $repositoryDownload = new RepositoryDownload();
                $repositoryDownload->downloadFile(
                        $download_file_path,
                        $fileName,
                        $mimeType
                );
            }
        }
        else 
        {
            // 一時ディレクトリにfilesからコピー
            $copy_path = $this->copyToTempDir($ret[0]['extension'], $tmpDirPath, $this->item_id, $this->item_no, $this->attribute_id, $this->file_no, $this->ver);
            if($copy_path === false) {
                $error_msg = "Download file is not exist";
                $this->errorLog($error_msg, __FILE__, __CLASS__, __LINE__);
                throw new AppException($error_msg);
            }
            
            // 古いバージョンのファイルである場合、ファイル名などが異なる可能性があるため、データベースから取得する
            $fileName = $ret[0]['file_name'];
            $mimeType = $ret[0]['mime_type'];
            if(isset($this->ver) && $this->ver > 0){
                $this->selectVersionFileDownloadInfo($this->item_id, $this->attribute_id, $this->file_no, $this->ver, $fileName, $mimeType);
            }
            
            // Add RepositoryDownload action 2010/03/30 A.Suzuki --start--
            $repositoryDownload = new RepositoryDownload();
            $repositoryDownload->downloadFile($copy_path, $fileName, $mimeType);
            // Add RepositoryDownload action 2010/03/30 A.Suzuki --end--
        }
        // Add delete pdf cover 2015/01/27 K.Matsushita -end-
        
        if($this->image_slide == null)
        {
            // Mod entryLog T.Koyasu 2015/03/06 --start--
            $this->infoLog("businessLogmanager", __FILE__, __CLASS__, __LINE__);
            $logManager = BusinessFactory::getFactory()->getBusiness("businessLogmanager");
            $logManager->entryLogForDownload($this->item_id, $this->item_no, $this->attribute_id, $this->file_no);
            // Mod entryLog T.Koyasu 2015/03/06 --end--
        }
        
        // 終了処理
        return;
    }
    
    // アイテムタイプアイコン追加 2008/07/16 Y.Nakao --start--
    /**
     * Get the item type of icon information from the DB, and download
     * アイテムタイプのアイコン情報をDBから取得し、ダウンロードする
     */
    private function getItemTypeIcom(){
        $query = "SELECT icon, icon_name, icon_mime_type ".
                 "FROM ". DATABASE_PREFIX ."repository_item_type ".
                 "WHERE item_type_id  = ?; ";
        $params = null;
        $params[] = $this->item_type_id;
        
        $ret = $this->Db->execute($query, $params);
        // SQLエラーの場合 終了
        if ($ret === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // 検索結果が一件でない場合 終了
        if (count($ret) != 1) {
            $error_msg = "Invalid result : SELECT item type icon num";
            $this->errorLog($error_msg, __FILE__, __CLASS__, __LINE__);
            throw new AppException($error_msg);
        }
        
        // Add RepositoryDownload action 2010/03/30 A.Suzuki --start--
        $repositoryDownload = new RepositoryDownload();
        $repositoryDownload->download($ret[0]['icon'],$ret[0]['icon_name'],$ret[0]['icon_mime_type']);
        // Add RepositoryDownload action 2010/03/30 A.Suzuki --end--
        
        return;
    }
    // アイテムタイプアイコン追加 2008/07/16 Y.Nakao --end--
    
    // Add index thumbnail 2010/08/11 Y.Nakao --start--
    /**
     * To get the thumbnail of the index from the DB, and download
     * インデックスのサムネイルをDBから取得し、ダウンロードする
     */
    private function getIndexThumbnail(){
        $query = "SELECT thumbnail, thumbnail_name, thumbnail_mime_type  ".
                 "FROM ". DATABASE_PREFIX ."repository_index ".
                 "WHERE index_id = ? ".
                 " AND is_delete = ?; ";
        $params = null;
        $params[] = $this->index_id;
        $params[] = 0;
        
        $ret = $this->Db->execute($query, $params);
        // SQLエラーの場合 終了
        if ($ret === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // 検索結果が一件でない場合 終了
        if (count($ret) != 1) {
            $error_msg = "Invalid result : SELECT index thumbnail num";
            $this->errorLog($error_msg, __FILE__, __CLASS__, __LINE__);
            throw new AppException($error_msg);
        }
        
        $repositoryDownload = new RepositoryDownload();
        $repositoryDownload->download($ret[0]['thumbnail'],$ret[0]['thumbnail_name'],$ret[0]['thumbnail_mime_type']);
        
        return;
        
    }
    // Add index thumbnail 2010/08/11 Y.Nakao --end--
    
    // Add PDF cover page 2012/06/13 A.Suzuki --start--
    /**
     * Get the PDF cover page image from the DB, and download
     * PDFカバーページ画像をDBから取得し、ダウンロードする
     */
    private function getPdfCoverImage(){
        $query = "SELECT ".RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_IMAGE.", ".
                 RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_TEXT.", ".
                 RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_MIMETYPE." ".
                 "FROM ". DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_PDF_COVER_PARAMETER." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_PARAM_NAME." = ?; ";
        $params = array();
        $params[] = RepositoryConst::PDF_COVER_PARAM_NAME_HEADER_IMAGE;
        $ret = $this->Db->execute($query, $params);
        if ($ret === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // 検索結果が一件でない場合 終了
        if(count($ret) != 1) {
            $error_msg = "Invalid result : SELECT pdf cover image num";
            $this->errorLog($error_msg, __FILE__, __CLASS__, __LINE__);
            throw new AppException($error_msg);
        }
        
        $repositoryDownload = new RepositoryDownload();
        $repositoryDownload->download(
            $ret[0][RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_IMAGE],
            $ret[0][RepositoryConst::DBCOL_REPOSITORY_PDF_COVER_PARAMETER_TEXT],
            $ret[0][DBCOL_REPOSITORY_PDF_COVER_PARAMETER_MIMETYPE]);
        
        return;
        
    }
    // Add PDF cover page 2012/06/13 A.Suzuki --end--
    /**
     * Get a preview image of the file from the DB, and download
     * ファイルのプレビュー画像をDBから取得し、ダウンロードする
     */
    private function getFilePreview()
    {
        $query = "SELECT file_prev, file_prev_name ";
        $query .= "FROM ". DATABASE_PREFIX ."repository_file ";
        $query .= "WHERE item_id = ? ".
                  "  AND item_no = ? ".
                  "  AND attribute_id = ? ".
                  "  AND file_no = ? ";
        $params[] = $this->item_id;
        $params[] = $this->item_no;
        $params[] = $this->attribute_id;
        $params[] = $this->file_no;
        $ret = $this->Db->execute($query, $params);
        // Error check
        if ($ret === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // if select result is 0 then this action end
        if (count($ret) != 1) {
            $error_msg = "Invalid result : SELECT file prev num";
            $this->errorLog($error_msg, __FILE__, __CLASS__, __LINE__);
            throw new AppException($error_msg);
        }
        
        // Add RepositoryDownload action 2010/03/30 A.Suzuki --start--
        $repositoryDownload = new RepositoryDownload();
        $repositoryDownload->download($ret[0]['file_prev'], $ret[0]['file_prev_name']);
        // Add RepositoryDownload action 2010/03/30 A.Suzuki --end--
        
        return;
    }
    
    /**
     * To get the thumbnail from the DB, and download
     * サムネイルをDBから取得し、ダウンロードする
     */
    private function getThumbnail()
    {
        $query = "SELECT file, file_name, mime_type ".
                 "FROM ". DATABASE_PREFIX ."repository_thumbnail ".
                 "WHERE item_id = ? ".
                 "  AND item_no = ? ".
                 "  AND attribute_id = ? ".
                 "  AND file_no = ? ";
        $params = array();
        $params[] = $this->item_id;
        $params[] = $this->item_no;
        $params[] = $this->attribute_id;
        $params[] = $this->file_no;
        $ret = $this->Db->execute($query, $params);
        // Error check
        if ($ret === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // if select result is 0 then this action end
        if (count($ret) != 1) {
            $error_msg = "Invalid result : SELECT thumbnail num";
            $this->errorLog($error_msg, __FILE__, __CLASS__, __LINE__);
            throw new AppException($error_msg);
        }
        
        // Add RepositoryDownload action 2010/03/30 A.Suzuki --start--
        $repositoryDownload = new RepositoryDownload();
        $repositoryDownload->download($ret[0]['file'],$ret[0]['file_name'],$ret[0]['mime_type']);
        // Add RepositoryDownload action 2010/03/30 A.Suzuki --end--
        
        return;
    }
    
    // Add delete pdf cover 2015/01/27 K.Matsushita -start-
    /**
     * Create a temporary directory and returns its path
     * 一時ディレクトリを作成し、そのパスを返す
     *
     * @return string Temporary directory path 一時ディレクトリパス
     */
    private function getDefaltTmpDirPath()
    {
        $tmpDirPath = "";
        
        $this->infoLog("businessWorkdirectory", __FILE__, __CLASS__, __LINE__);
        $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness("businessWorkdirectory");
        
        $tmpDirPath = $businessWorkdirectory->create();
        
        return $tmpDirPath;
    }
    
    /**
     * Copy the temporary directory to the specified download file, returning a path
     * ダウンロードファイルを指定された一時ディレクトリにコピーし、パスを返す
     * 
     * @param string $extension Extension 拡張子
     * @param string $tmpDirPath Temporary directory path 一時ディレクトリパス
     * @param int $itemId Item ID for identifying the file ファイルを特定するためのアイテムID
     * @param int $itemNo Item sequence number for identifying the file ファイルを特定するためのアイテム通番
     * @param int $attrId Attribute ID for identifying the file ファイルを特定するための属性ID
     * @param int $fileNo File sequence number for identifying the file ファイルを特定するためのファイル通番
     * @param int $ver Version for identifying the file ファイルを特定するためのバージョン
     * @return string Destination path コピー先パス
     */
    private function copyToTempDir($extension, $tmpDirPath, $itemId, $itemNo, $attrId, $fileNo, $ver){
        $destPath = $tmpDirPath.
                $this->item_id.'_'.
                $this->attribute_id.'_'.
                $this->file_no.'.'.
                $extension;
        
        $businessName = "businessContentfiletransaction";
        $business = BusinessFactory::getFactory()->getBusiness($businessName);
        
        if(!isset($ver) || $ver < 0){
            $ver = 0;
        }
        $business->copyTo($itemId, $attrId, $fileNo, $ver, $destPath);
        
        return $destPath;
    }
    // Add delete pdf cover 2015/01/27 K.Matsushita -end-
    
    /**
     * In order to use at the time of download, to get the file name and file type of the old version of the file
     * ダウンロード時に利用するため、古いバージョンのファイルのファイル名およびファイル種別を取得する
     *
     * @param int $itemId Item ID for identifying the file ファイルを特定するためのアイテムID
     * @param int $attrId Attribute ID for identifying the file ファイルを特定するための属性ID
     * @param int $fileNo File sequence number for identifying the file ファイルを特定するためのファイル通番
     * @param int $ver Version for identifying the file ファイルを特定するためのバージョン
     * @param string $fileName File name ファイル名
     * @param string $mimeType mimetype Mimetype
     */
    private function selectVersionFileDownloadInfo($itemId, $attrId, $fileNo, $ver, &$fileName, &$mimeType){
        $query = "SELECT physical_file_name, mime_type ". 
                 " FROM ". DATABASE_PREFIX. "repository_file_update_history ". 
                 " WHERE item_id = ? ". 
                 " AND item_no = ? ". 
                 " AND attribute_id = ? ". 
                 " AND file_no = ? ". 
                 " AND version = ? ". 
                 " AND is_delete = ?;";
        $params = array();
        $params[] = $itemId;
        $params[] = 1;
        $params[] = $attrId;
        $params[] = $fileNo;
        $params[] = $ver;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        $fileName = $result[0]["physical_file_name"];
        $mimeType = $result[0]["mime_type"];
    }
}
?>
