<?php

/**
 * Batch progress Class
 * バッチ進捗クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BatchProgress.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Batch progress Class
 * バッチ進捗クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class BatchProgress
{
    // バッチ実行用ステータス
    /**
     * Status
     * 実行ステータス
     *
     * @var int
     */
    public $status = null;
    /**
     * Now process number
     * 現在の処理回数
     *
     * @var int
     */
    public $current = 0;
    /**
     * Max process number
     * 最大処理回数
     *
     * @var int
     */
    public $max_length = 1;
    /**
     * Exit code
     * 終了コード
     *
     * @var int
     */
    public $exitCode = null;
    /**
     * Options
     * 実行オプション
     *
     * @var array
     */
    public $options = null;

    /**
     * BatchProgress constructor.
     * コンストラクタ
     *
     * @param array $options options 実行オプション
     *               array[$executeCommandOption]
     */
    public function __construct($options) {
        $this->options = $options;
    }
}

?>