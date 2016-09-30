<?php

/**
 * Batch exit code const class
 * バッチ終了コード定義クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BatchExitCodes.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Batch exit code const class
 * バッチ終了コード定義クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class BatchExitCodes
{
    /**
     * End success
     * 正常終了
     */
    const END_SUCCESS = 0;
    /**
     * End error
     * 不明なエラー終了
     */
    const END_ERROR = 1;
    /**
     * End interrupt
     * 割込み実行による強制終了
     */
    const END_INTERRUPT = 2;
    /**
     * End multi execute
     * 多重実行による未実行終了
     */
    const END_MULTI_EXECUTION = 3;
    /**
     * End DB error
     * DBエラーによる終了
     */
    const ERROR_SQL = 100;
    /**
     * End INI error
     * INIファイル不正による終了
     */
    const ERROR_INI_FILE = 101;
    /**
     * End unknown process
     * プロセスID取得エラー終了
     */
    const ERROR_UNKNOWN_PID = 102;
    /**
     * End get factory class
     * ファクトリークラス初期化エラー終了
     */
    const ERROR_GET_FACTORY = 103;
    /**
     * End get logger class
     * ロガークラス初期化エラー終了
     */
    const ERROR_GET_LOGGER = 104;
    /**
     * End update process error
     * プロセステーブル更新エラー終了
     */
    const ERROR_UPDATE_PROCESS = 105;

    // 200～254番はバッチ派生クラスで独自に定義するためここには書かない
}

?>
