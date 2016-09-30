<?php

/**
 * Stream base class
 * ストリーム基底クラス
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
 * Stream base class
 * ストリーム基底クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
abstract class Stream
{
    /**
     * Reading of Stream
     * Streamの読み込み
     * 
     * @param int $length The number of bytes read 読込むバイト数
     */
    abstract public function read($length);

    /**
     * Write to the Stream
     * Streamへの書き込み
     * 
     * @param string $string String to be written 書き込む文字列
     * @param int $length Number of write bytes 書き込むバイト数
     */
    abstract public function write($string,$length = null);

    /**
     * Close the Stream
     * Streamをクローズする
     */
    abstract public function close();
}
?>