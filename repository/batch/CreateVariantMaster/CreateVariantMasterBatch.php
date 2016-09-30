<?php
/**
 * Batch class for create variant master
 * 異体字マスタ作成バッチクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: CreateVariantMasterBatch.php 69174 2016-06-22 06:43:30Z tatsuya_koyasu $
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
 */
require_once(dirname(__FILE__)."/../FW/BatchNC2Const.inc.php");
/**
 * Create variant master action batch class
 * 異体字マスタテーブル作成アクションバッチクラス
 * 
 */
require_once(dirname(__FILE__)."/CreateVariantMasterAction.class.php");

$class = new CreateVariantMasterAction();
$ret = $class->execute($argv, dirname(__FILE__)."/CreateVariantMaster.ini");

if($ret != 0) echo $ret."\n";
?>