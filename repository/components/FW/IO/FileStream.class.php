<?php

/**
 * Stream operation common classes
 * Stream操作共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: AppException.class.php 68416 2016-06-03 07:39:44Z tomohiro_ichikawa $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Stream base class
 * ストリーム基底クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/IO/Stream.class.php';
/**
 * Input and output expansion exception class
 * 入出力拡張例外クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/IO/IOException.class.php';


/**
 * Stream operation common classes
 * Stream操作共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class FileStream extends Stream
{
    /**
     * File path
     * ファイルパス
     * 
     * @var string
     */
    private $filePath = "";

    /**
     * File pointer after the fopen
     * fopenした後のファイルポインタ
     * 
     * @var resource
     */
    private $fp = null;

    /**
     * Get File Path
     * ファイルパスの取得
     * 
     * @return string File path ファイルパス
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Constructor
     * コンストラクタ
     * 
     * @param resource $fp Opened Stream オープンされたStream
     * @param string $filePath File path ファイルパス
     */
    protected function __construct($fp,$filePath)
    {
        $this->fp = $fp;
        $this->filePath = $filePath;
    }

    /**
     * To open a file or URL
     * ファイルまたは URL をオープンする
     * 
     * @param string $filePath File path ファイルパス
     * @param string $mode Access format (see fopen) アクセス形式(fopen参照)
     * @return FileStream Opened Stream オープンしたStream
     */
    public static function open($filePath ,$mode)
    {
        if(!isset($filePath) || strlen($filePath) == 0){
            throw new InvalidArgumentException("Repository_Components_FW_IO_FileStream::open filePath null");
        }
        else if(!isset($mode) || strlen($mode) == 0)
        {
            throw new InvalidArgumentException("Repository_Components_FW_IO_FileStream::open mode null");
        }

        $openResult = fopen($filePath, $mode);
        $fileStream = new FileStream($openResult,$filePath);
        if($openResult === false)
        {
            throw new IOException($fileStream,"open");
        }

        return $fileStream;
    }

    /**
     * Reading of Stream
     * Streamの読み込み
     * 
     * @param int $length The number of bytes read 読込むバイト数
     */
    public function read($length)
    {
        if($this->fp === false)
        {
            return false;
        }

        $readResult = fread($this->fp, $length);
        if($readResult === false)
        {
            $this->close();

            throw new IOException($this,"read");
        }

        return $readResult;
    }

    /**
     * Write to the Stream
     * Streamへの書き込み
     * 
     * @param string $string String to be written 書き込む文字列
     * @param int $length Number of write bytes 書き込むバイト数
     */
    public function write($string,$length = null)
    {
        if($this->fp === false)
        {
            return false;
        }

        // lengthがnullの場合、0バイトと認識されてファイルに空文字が入力されてしまうため判定処理を行う
        if(isset($length))
        {
            $writeResult = fwrite($this->fp,$string,$length);
        }
        else
        {
            $writeResult = fwrite($this->fp,$string);
        }

        if($writeResult === false)
        {
            $this->close();

            throw new IOException($this,"write");
        }

        return $writeResult;
    }

    /**
     * Examine whether the file pointer opened in fopen has reached the end-of-file
     * fopenで開いたファイルポインタがファイル終端に達しているかどうか調べる
     * 
     * @return boolean Whether or not it is the end (true if you have reached the EOF, others are false) 終端であるか否か(EOF に達している場合はtrue、その他はfalse)
     */
    public function eof()
    {
        if($this->fp === false)
        {
            return false;
        }

        return feof($this->fp);
    }

    /**
     * Close the Stream
     * Streamをクローズする
     */
    public function close()
    {
        if($this->fp === false)
        {
            return;
        }

        fclose($this->fp);

        $this->fp = false;
    }
}
?>