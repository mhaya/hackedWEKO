<?php

/**
 * Execute sitelicense usage statistics batch interface
 * サイトライセンス利用統計実行バッチインターフェース
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: SitelicenseUsagestatisticsBatch.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Setting Const process
 * 定数設定処理
 */
require_once(dirname(__FILE__)."/../FW/BatchNC2Const.inc.php");
/**
 * Execute sitelicense usage statistics class
 * サイトライセンス利用統計実行クラス
 */
require_once(dirname(__FILE__)."/SitelicenseUsagestatisticsAction.class.php");

$class = new SitelicenseUsagestatisticsAction();
$ret = $class->execute($argv, WEBAPP_DIR."/modules/repository/batch/SitelicenseUsagestatistics/SitelicenseUsagestatistics.ini");

echo $ret;
?>