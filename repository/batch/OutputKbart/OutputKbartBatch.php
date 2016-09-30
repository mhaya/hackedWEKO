<?php

/**
 * Output KBART 2 file batch interface
 * KBART2ファイル出力バッチインターフェース
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: OutputKbartBatch.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
/*
 * Output KBART 2 file class
 * KBART2ファイル出力クラス
 */
require_once(dirname(__FILE__)."/OutputKbartAction.class.php");

$class = new OutputKbartAction();
$ret = $class->execute($argv, dirname(__FILE__)."/OutputKbart.ini");

if($ret != 0) echo $ret."\n";
?>