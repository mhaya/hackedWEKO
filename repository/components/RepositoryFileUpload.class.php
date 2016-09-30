<?php

/**
 * File upload common classes
 * ファイルアップロード共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryFileUpload.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Read a common class of the uploaded file in a multi-part
 * マルチパートでアップロードされたファイルの読み込み共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/util/MultipartStreamDecoder.class.php';
/**
 * Exception class
 * 例外基底クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/AppException.class.php';
/**
 * Input and output expansion exception class
 * 入出力拡張例外クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/IO/IOException.class.php';
/**
 * Stream operation common classes
 * Stream操作共通クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/IO/FileStream.class.php';

/**
 * File upload common classes
 * ファイルアップロード共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositoryFileUpload
{
    // member
    /**
     * File name
     * ファイル名
     *
     * @var string
     */
    private $fileName = null;
    /**
     * Physical file name
     * 物理ファイル名
     *
     * @var string
     */
    private $physicalFileName = null;
    /**
     * Extension
     * 拡張子
     *
     * @var string
     */
    private $extension = null;
    /**
     * File size
     * ファイルサイズ
     *
     * @var int
     */
    private $fileSize = null;
    /**
     * MIME type
     * MYMEタイプ
     *
     * @var string
     */
    private $mimetype = null;
    /**
     * Insert date and time
     * 挿入日時
     *
     * @var string
     */
    private $insertTime = null;
    
    /**
     * Log file path
     * ログファイルパス
     *
     * @var string
     */
    private $logFile = "";
    /**
     * Logging flag
     * ログ作成フラグ
     *
     * @var boolean
     */
    private $isCreateLog = true;
    
    // Const
    /**
     * Key name (file name)
     * キー名(ファイル名)
     *
     * @var string
     */
    const KEY_FILE_NAME = "file_name";
    /**
     * Key name (physical file name)
     * キー名(物理ファイル名)
     *
     * @var string
     */
    const KEY_PHYSICAL_FILE_NAME = "physical_file_name";
    /**
     * Key name (file size)
     * キー名(ファイルサイズ)
     *
     * @var string
     */
    const KEY_FILE_SIZE = "file_size";
    /**
     * Key name (mime type)
     * キー名(MIMEタイプ)
     *
     * @var string
     */
    const KEY_MIMETYPE = "mimetype";
    /**
     * Key name (extension)
     * キー名(拡張子)
     *
     * @var string
     */
    const KEY_EXTENSION = "extension";
    /**
     * Key name (insert date and time)
     * キー名(挿入日時)
     *
     * @var string
     */
    const KEY_INSERT_TIME = "insert_time";
    /**
     * Key name (updaload directory)
     * キー名(アップロードディレクトリ)
     *
     * @var string
     */
    const KEY_UPLOAD_DIR = "upload_dir";
    
    
    /**
     * Constructor
     * コンストラクタ
     */
    public function __construct()
    {
        $this->init();
        
        // Create log file
        if($this->isCreateLog)
        {
            $this->logFile = WEBAPP_DIR."/logs/weko/sword/file_upload_log.txt";
            $logFh = fopen($this->logFile, "w");
            chmod($this->logFile, 0600);
            fwrite($logFh, "Start RepositoryFileUpload. (".date("Y/m/d H:i:s").")\n");
            fwrite($logFh, "\n");
            fclose($logFh);
        }
    }
    
    /**
     * Get upload data
     * アップロードデータの取得
     *
     * @param int $statusCode Status code ステータスコード
     * @return array
     */
    public function getUploadData(&$statusCode)
    {
        $this->writeLog("-- Start getUploadData (".date("Y/m/d H:i:s").") --\n");

        $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness("businessWorkdirectory");
        $uploadDir = $businessWorkdirectory->create();
        $this->getFileByInput($statusCode, $uploadDir);

        $fileData = array();
        if( isset($this->fileName) && isset($this->physicalFileName) && isset($this->fileSize) &&
            isset($this->mimetype) && isset($this->extension) && isset($this->insertTime) && isset($uploadDir))
        {
            $fileData[self::KEY_FILE_NAME] = $this->fileName;
            $fileData[self::KEY_PHYSICAL_FILE_NAME] = $this->physicalFileName;
            $fileData[self::KEY_FILE_SIZE] = $this->fileSize;
            $fileData[self::KEY_MIMETYPE] = $this->mimetype;
            $fileData[self::KEY_EXTENSION] = $this->extension;
            $fileData[self::KEY_INSERT_TIME] = $this->insertTime;
            $fileData[self::KEY_UPLOAD_DIR] = $uploadDir;
        }
        else
        {
            if(!isset($statusCode) || strlen($statusCode) == 0)
            {
                $statusCode = 400;
            }
        }
        
        $this->writeLog("-- End getUploadData (".date("Y/m/d H:i:s").") --\n\n");
        
        return $fileData;
    }

    /**
     * Initialize
     * 初期化処理
     */
    private function init()
    {
        $this->fileName = null;
        $this->physicalFileName = null;
        $this->extension = null;
        $this->fileSize = null;
        $this->mimetype = null;
        $this->insertTime = null;
        
        $this->writeLog("  Init data.\n");
    }

    /**
     * Get file information
     * ファイル情報の取得
     * @param string $statusCode Status code 状態コード
     * @param string $uploadDir Upload directory of zip
     *                          zipファイルアップロードディレクトリ
     */
    private function getFileByInput(&$statusCode, $uploadDir)
    {
        $this->writeLog("-- Start getFileByInput (".date("Y/m/d H:i:s").") --\n");

        $this->init();
        // Update SuppleContentsEntry Y.Yamazawa --start-- 2015/04/08 --start--
        // ファイル情報を決める
        $this->decideInsertTime();
        $this->decideMimeType();
        $this->decideFileName();
        $this->decideExtension();
        $this->decidePhysicalName();

        // ファイルのアップロード
        $this->decodeFile($statusCode, $uploadDir);

        // ログの出力
        $this->outPutLogOfFileInfo();
        $this->writeLog("-- End getFileByInput (".date("Y/m/d H:i:s").") --\n\n");
        // Update SuppleContentsEntry Y.Yamazawa --end-- 2015/04/08 --end--
    }

    // Add SuppleContentsEntry Y.Yamazawa --start-- 2015/04/08 --start--
    /**
     * Determination of the inserted date and time
     * 挿入日時の決定
     */
    private function decideInsertTime()
    {
        // Set insertTime
        $now = new Date();
        $this->insertTime = $now->getDate(DATE_FORMAT_TIMESTAMP);
    }
    // Add SuppleContentsEntry Y.Yamazawa --end-- 2015/04/08 --end--

    // Add SuppleContentsEntry Y.Yamazawa --start-- 2015/04/08 --start--
    /**
     * Determination of the MIME type
     * MIMEタイプの決定
     */
    private function decideMimeType()
    {
        // Set mimetype
        if(isset($_SERVER['CONTENT_TYPE'])){
            $this->mimetype = $_SERVER['CONTENT_TYPE'];
        } else if(isset($_SERVER['HTTP_CONTENT_TYPE'])){
            $this->mimetype = $_SERVER['HTTP_CONTENT_TYPE'];
        }
    }
    // Add SuppleContentsEntry Y.Yamazawa --end-- 2015/04/08 --end--

    // Add SuppleContentsEntry Y.Yamazawa --start-- 2015/04/08 --start--
    /**
     * Determination of the file name
     * ファイル名の決定
     */
    private function decideFileName()
    {
        if(isset($_SERVER["CONTENT_DISPOSITION"])){
            $contentDisposition = urldecode($_SERVER["CONTENT_DISPOSITION"]);
            $contentDisposition = mb_convert_encoding($contentDisposition, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS");
        } else if(isset($_SERVER["HTTP_CONTENT_DISPOSITION"])){
            $contentDisposition = urldecode($_SERVER["HTTP_CONTENT_DISPOSITION"]);
            $contentDisposition = mb_convert_encoding($contentDisposition, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS");
        }
        $pattern = "/filename=[\"|\']([^\"\']+)[\"|\' ]/";
        $result = preg_match($pattern, $contentDisposition, $matches);
        if($result > 0)
        {
            if(isset($matches) && is_array($matches) && count($matches) > 1)
            {
                $this->fileName = trim($matches[1]);
            }
        }
        else
        {
            $pattern = "/filename=([^ ]+)/";
            $result = preg_match($pattern, $contentDisposition, $matches);
            if($result > 0)
            {
                if(isset($matches) && is_array($matches) && count($matches) > 1)
                {
                    $this->fileName = trim($matches[1]);
                }
            }
        }
    }
    // Add SuppleContentsEntry Y.Yamazawa --end-- 2015/04/08 --end--

    // Add SuppleContentsEntry Y.Yamazawa --start-- 2015/04/08 --start--
    /**
     * Determination of extension
     * Extensionの決定
     */
    private function  decideExtension()
    {
        // Set extension
        if(strlen($this->fileName) > 0)
        {
            if(preg_match("/^application\/zip/", $this->mimetype) ||
                    preg_match("/^application\/x-zip/",$this->mimetype) ||
                    preg_match("/^application\/x-compress/", $this->mimetype) ||
                    preg_match("/^multipart\/form-data/", $this->mimetype))
            {
                $this->extension = "zip";
                $this->fileName .= ".".$this->extension;
            }
            else
            {
                $pos = strrpos($this->fileName, '.');
                if($pos !== false)
                {
                    $this->extension = strtolower(substr($this->fileName, $pos+1));
                }
            }
        }
    }
    // Add SuppleContentsEntry Y.Yamazawa --end-- 2015/04/08 --end--

    // Add SuppleContentsEntry Y.Yamazawa --start-- 2015/04/08 --start--
    /**
     * Determination of the physical file name
     * 物理ファイル名の決定
     */
    private function decidePhysicalName()
    {
        // Set physicalName
        $this->physicalFileName = $this->insertTime.".".$this->extension;
        if(strlen($this->fileName) == 0)
        {
            $this->fileName = $this->physicalFileName;
        }
    }
    // Add SuppleContentsEntry Y.Yamazawa --end-- 2015/04/08 --end--

    // Add SuppleContentsEntry Y.Yamazawa --start-- 2015/04/08 --start--
    /**
     * File upload
     * ファイルアップロード処理
     * @param string $statusCode Status code ステータスコード
     * @param string $uploadDir Upload directory of zip
     *                          zipファイルアップロードディレクトリ
     *
     * @return boolean Success or failure of the decoding process デコード処理の成功失敗
     */
    private function decodeFile(&$statusCode, $uploadDir)
    {
        try {
            $readFileStream = FileStream::open("php://input", "rb");
            $this->writeLog("[decode]:");
            $fileList = Repository_Components_Util_MultipartStreamDecoder::decodeMultiPartFile($readFileStream, $uploadDir.$this->physicalFileName);
            $this->writeLog("Success\n");
            $this->writeLog("[UPLODE FILE]"."\n");
            foreach ($fileList as $fileName){
                $this->writeLog($fileName."\n");
            }
        }
        catch(AppException $e){
            $errorMsg = $e->getMessage();
            $this->writeLog("ERROR\n".$errorMsg."\n");
            $readFileStream->close();

            // マルチパートでない場合のzipファイル出力処理
            $readFileStream = FileStream::open("php://input", "rb");
            $outputFileStream = FileStream::open($uploadDir.$this->physicalFileName, "w");
            while ($data = $readFileStream->read(1024))
            {
                $outputFileStream->write($data);
            }
            $outputFileStream->close();
            $readFileStream->close();
        }
        catch(IOException $e)
        {
            $errorMsg = $e->getMessage();
            $this->writeLog("ERROR\n".$errorMsg."\n");
            $statusCode = 500;

            return false;
        }

        $this->fileSize = filesize($uploadDir.$this->physicalFileName);

        return true;
    }
    // Add SuppleContentsEntry Y.Yamazawa --end-- 2015/04/08 --end--

    // Add SuppleContentsEntry Y.Yamazawa --start-- 2015/04/08 --start--
    /**
     * The output of the file information log
     * ファイル情報ログの出力
     */
    private function outPutLogOfFileInfo()
    {
        $this->writeLog(" [Session data]\n");
        if(isset($_SERVER['CONTENT_TYPE']))
        {
            $this->writeLog("  CONTENT_TYPE: ".$_SERVER['CONTENT_TYPE']."\n");
        }
        if(isset($_SERVER['HTTP_CONTENT_TYPE']))
        {
            $this->writeLog("  HTTP_CONTENT_TYPE: ".$_SERVER['HTTP_CONTENT_TYPE']."\n");
        }
        if(isset($_SERVER['CONTENT_DISPOSITION']))
        {
            $this->writeLog("  CONTENT_DISPOSITION: ".$_SERVER['CONTENT_DISPOSITION']."\n");
        }
        $this->writeLog("  HTTP_CONTENT_DISPOSITION: ".$_SERVER['HTTP_CONTENT_DISPOSITION']."\n");
        $this->writeLog("\n");
        
        $this->writeLog(" [Acquired file data]\n");
        $this->writeLog("  insertTime: ".$this->insertTime."\n");
        $this->writeLog("  mimetype: ".$this->mimetype."\n");
        $this->writeLog("  extension: ".$this->extension."\n");
        $this->writeLog("  fileName: ".$this->fileName."\n");
        $this->writeLog("  physicalFileName: ".$this->physicalFileName."\n");
        $this->writeLog("  fileSize: ".$this->fileSize."\n");
        $this->writeLog("-- End getFileByInput (".date("Y/m/d H:i:s").") --\n\n");
    }
    // Add SuppleContentsEntry Y.Yamazawa --end-- 2015/04/08 --end--

    /**
     * Write log to file
     * ログ書込み
     *
     * @param string $string
     * @param int $length [optional]
     * @return int
     */
    private function writeLog($string, $length=null)
    {
        if($this->isCreateLog && strlen($this->logFile)>0)
        {
            $ret = "";
            $fp = fopen($this->logFile, "a");
            if(isset($length))
            {
                $ret = fwrite($fp, $string, $length);
            }
            else
            {
                $ret = fwrite($fp, $string);
            }
            fclose($fp);
            
            return $ret;
        }
        else
        {
            return false;
        }
    }
}
?>
