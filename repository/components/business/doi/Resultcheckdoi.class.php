<?php

/**
 * Result of check grant DOI class
 * DOI付与チェック結果クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Resultcheckdoi.class.php 70857 2016-08-08 11:04:08Z tomohiro_ichikawa $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Result of check grant DOI class
 * DOI付与チェック結果クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class ResultCheckDoi 
{
    /**
     * Whether or not grant DOI
     * DOI付与できるか
     *
     * @var boolean
     */
    public $isGrantDoi = null;
    /**
     * Whether or not set Y handle prefix
     * YハンドルのPrefixが設定されているか
     *
     * @var boolean
     */
    public $isSetYhandlePrefix = null;
    /**
     * Whether or not allowed use of DOI
     * DOIの使用が許可されているか
     *
     * @var boolean
     */
    public $isAllowDoi = null;
    /**
     * Whether or not set DOI prefix
     * DOIのPrefixが設定されているか
     *
     * @var boolean
     */
    public $isSetDoiPrefix = null;
    /**
     * Whether or not set review after regist item
     * アイテム登録後、査読を行う設定でないか
     *
     * @var boolean
     */
    public $isNotReviewItem = null;
    /**
     * Whether or not set public after regist item
     * アイテム登録後、公開にする設定であるか
     *
     * @var boolean
     */
    public $isPublicItem = null;
    /**
     * Whether or not set Nii type on item type
     * アイテムタイプにNII資源タイプが設定されているか
     *
     * @var boolean
     */
    public $isSetNiiType = null;
    /**
     * Whether or not set necessary mapping for grant DOI on item type
     * アイテムタイプにDOI付与に必要なマッピングが設定されているか
     *
     * @var boolean
     */
    public $isSetItemTypeMapping = null;
    /**
     * Mapping lacking for grant DOI
     * DOI付与に不足しているマッピング
     *
     * @var array array[$ii]
     */
    public $LackItemTypeMapping = array();
    /**
     * Whether or not set public on position index
     * 所属インデックスが公開に設定されているか
     *
     * @var boolean
     */
    public $isPublicIndex = null;
    /**
     * Whether or not set public harvest on position index
     * 所属インデックスがハーベスト公開に設定されているか
     *
     * @var boolean
     */
    public $isPublicHarvestIndex = null;
    /**
     * Whether or not regist necessary metadata for grant DOI
     * DOI付与に必要なメタデータが登録されているか
     *
     * @var boolean
     */
    public $isSetMetadata = null;
    /**
     * Mapping lacking or incorrect for grant DOI
     * DOI付与に不足または誤りがあるメタデータ
     *
     * @var array array[$ii]
     */
    public $LackMetadata = array();
    /**
     * Whether or not already grant DOI
     * DOIが既に付与されていないか
     *
     * @var boolean
     */
    public $isNotAlreadyGrantDoi = null;
    
    /**
     * Whether or not format of the library DOI is correct
     * 図書館DOIのフォーマットが正しいか
     *
     * @var boolean
     */
    public $isCorrectLibraryDoiFormat = null;
    /**
     * Whether or not DOI suffix is not specified in the automatic departure number mode
     * 自動発番モード時にDOI suffixが指定されていないか
     *
     * @var boolean
     */
    public $isNotSpecifyDoiInAuto = null;
    /**
     * Whether or not DOI suffix is specified in the free input mode
     * 自由入力モード時にDOI suffixが指定されているか
     *
     * @var boolean
     */
    public $isSpecifyDoiInFree = null;
    /**
     * Whether or not is half size number 8 digit DOI suffix
     * DOI Suffixが半角数字8桁でないか
     *
     * @var boolean
     */
    public $isNotHalfSizeNumber8Digit = null;
    /**
     * Whether or not set string available DOI suffix
     * DOI Suffixに使用できる文字が設定されているか
     *
     * @var boolean
     */
    public $isStringAvailable = null;
    /**
     * Whether or not is string length of 「info:doi/[prefix]/[suffix]」 lower 300
     * 「info:doi/[prefix]/[suffix]」が300字以内であるか
     *
     * @var boolean
     */
    public $isAvailableDoiStringLength = null;
    /**
     * Whether or not already used DOI suffix
     * DOI Suffixが既に使用済みのものでないか
     *
     * @var boolean
     */
    public $isNotDoiSuffixUsed = null;
    /**
     * Whether or not already droped DOI suffix
     * DOI Suffixが既に取り下げ済みのものでないか
     *
     * @var boolean
     */
    public $isNotDoiSuffixDroped = null;
}
?>