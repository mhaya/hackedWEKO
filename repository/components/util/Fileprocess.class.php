<?php

/**
 * External command execution common classes
 * 外部コマンド実行共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Fileprocess.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * External command execution common classes
 * 外部コマンド実行共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Util_Fileprocess
{
    /**
     * To execute the command to specify the time-out period
     * タイムアウト時間を指定してコマンドを実行する
     *
     * @param string $cmd Command コマンド
     * @param int $timeout Time-out time (specified in milliseconds) タイムアウト時間(ミリ秒単位で指定する)
     * @param int $interval Execution state interval (specified in milliseconds) 実行状態インターバル(ミリ秒単位で指定する)
     * @return int|boolean Execution result 実行結果
     *                     int Exit code 終了コード
     *                     boolean Time-out or start-up failure タイムアウトor起動失敗
     */
    public static function exec($cmd, $timeout=10000, $interval=100)
    {
        // コマンド実行
        $process = proc_open(escapeshellcmd($cmd), array(), $pipes);
        if(!is_resource($process)){
            return false;
        }
        
        // タイムアウト判定用
        $cnt = 0;
        $retry = ($timeout / $interval) + 1;
        $running = true;
        $exitcode = 0;
        
        do{
            // 実行状態を取得するために $interval の時間ごとにsleep
            usleep($interval*1000);
            
            // 実行状態を取得
            $status  = proc_get_status($process);
            $running = $status['running'];
            $exitcode = $status['exitcode'];
            
            // カウンタインクリメント
            $cnt++;
        }
        while($running && $cnt < $retry);
        
        // タイムアウトの場合強制終了
        if ($running) {
            proc_terminate($process);
            proc_close($process);
            return false;
        }
        
        // プロセス終了
        proc_close($process);
        
        return $exitcode;
    }
}
?>
