<?php

/**
 * Aggregate Sitelicense usage statistics mail  batch interface
 * サイトライセンス利用統計集計バッチインターフェース
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: SitelicenseUsagestatisticsSummaryBatch.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 *
 * @package WEKO
 */
require_once(dirname(__FILE__)."/../FW/BatchNC2Const.inc.php");
/**
 * Aggregate sitelicense usage statistics mail class
 * サイトライセンス利用統計集計クラス
 */
require_once(dirname(__FILE__)."/SitelicenseUsagestatisticsSummaryAction.class.php");

$class = new SitelicenseUsagestatisticsSummaryAction();
$ret = $class->execute($argv, WEBAPP_DIR."/modules/repository/batch/SitelicenseUsagestatisticsSummary/SitelicenseUsagestatisticsSummary.ini");

echo $ret;
?>