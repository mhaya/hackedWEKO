<?php

/**
 * Input and output expansion exception class
 * 入出力拡張例外クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: MultipartStreamDecoder.class.php 42605 2015-04-02 01:02:01Z yuya_yamazawa $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Input and output expansion exception class
 * 入出力拡張例外クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class IOException extends Exception
{
    /**
     * Occurred Stream of error
     * エラーの発生したStream
     */
    private $errorStream = null;

    /**
     * Constructor
     * コンストラクタ
     * 
     * @param Stream $errorStream Occurred Stream of error エラーの発生したStream
     * @param string $message Exception message 例外メッセージ
     * @param string $code Exception code 例外コード
     * @param String $previous Before the exception 前の例外
     */
    public function __construct ($errorStream ,$message = "", $code = 0, $previous = null)
    {
        parent::__construct($message, $code,$previous);

        $this->errorStream = $errorStream;
    }

    /**
     * To get the generated Stream of error
     * エラーの発生したStreamを取得する
     * 
     * @return Stream Occurred Stream of error エラーの発生したStream
     */
    public function getErrorStream()
    {
        return $this->errorStream;
    }
}
?>