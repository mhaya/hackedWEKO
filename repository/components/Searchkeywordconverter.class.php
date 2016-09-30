<?php

/**
 * Search keyword converter abstract class
 * 検索キーワード変換基底クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Aggregatesitelicenseusagestatistics.class.php 68463 2016-06-06 06:05:40Z tomohiro_ichikawa $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR."/modules/repository/components/FW/BusinessBase.class.php";

/**
 * Search keyword converter abstract class
 * 検索キーワード変換基底クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
abstract class Repository_Components_Searchkeywordconverter extends BusinessBase
{
    
    /**
     * Conversion with respect to the passed metadata value from the meta-data item information
     * メタデータ項目情報から渡されたメタデータ値に関して変換
     *
     * @param string $metadata metadata input アイテムのメタデータ入力値
     * @param ToSearchKey $metadataInfo metadata object メタデータ項目情報オブジェクト
     */
    abstract public function toSearchKey($metadata, $metadataInfo);
    
    /**
     * The inputted at the time of the search keyword search condition information with reference to the conversion
     * 検索時に入力されたキーワードを検索条件情報を参照して変換
     *
     * @param string $searchKeyword input keyword 検索時に入力されたワード
     * @param ToSearchCondition $searchCondition search keyword object 検索条件情報
     */
    abstract public function toSearchCondition($searchKeyword, $searchCondition);
}

/**
 * Search keyword class
 * 検索キーワードクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class ToSearchKey
{
    /**
     * metadata attribute
     * メタデータ属性
     *
     * @var string
     */
    public $itemAttr = null;
    
    /**
     * mapping
     * マッピング情報
     *
     * @var array
     */
    public $mapping = array();
    
    /**
     * metadata option
     * 属性オプション
     *
     * @var array
     */
    public $option = array();
    
    /**
     * metadata language
     * メタデータ表示言語
     *
     * @var string
     */
    public $language = null;
}

/**
 * Search keyword condition class
 * 検索キーワード制約クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class ToSearchCondition
{
    /**
     * Condition number
     * 制約番号
     */
    const ALLMETADATA = 1;
    /**
     * Condition number
     * 制約番号
     */
    const TITLE = 2;
    /**
     * Condition number
     * 制約番号
     */
    const CREATOR = 3;
    /**
     * Condition number
     * 制約番号
     */
    const KEYWORD = 4;
    /**
     * Condition number
     * 制約番号
     */
    const SUBJECT = 5;
    /**
     * Condition number
     * 制約番号
     */
    const DESCRIPTION = 6;
    /**
     * Condition number
     * 制約番号
     */
    const PUBLISHER = 7;
    /**
     * Condition number
     * 制約番号
     */
    const CONTRIBUTOR = 8;
    /**
     * Condition number
     * 制約番号
     */
    const DATE = 9;
    /**
     * Condition number
     * 制約番号
     */
    const ITEMTYPE = 10;
    /**
     * Condition number
     * 制約番号
     */
    const TYPE = 11;
    /**
     * Condition number
     * 制約番号
     */
    const FORMAT = 12;
    /**
     * Condition number
     * 制約番号
     */
    const ID = 13;
    /**
     * Condition number
     * 制約番号
     */
    const JTITLE = 14;
    /**
     * Condition number
     * 制約番号
     */
    const DATEOFISSUED = 15;
    /**
     * Condition number
     * 制約番号
     */
    const LANGUAGE = 16;
    /**
     * Condition number
     * 制約番号
     */
    const SPATIAL = 17;
    /**
     * Condition number
     * 制約番号
     */
    const TEMPORAL = 18;
    /**
     * Condition number
     * 制約番号
     */
    const RIGHTS = 19;
    /**
     * Condition number
     * 制約番号
     */
    const TEXTVERSION = 20;
    /**
     * Condition number
     * 制約番号
     */
    const GRANTID = 21;
    /**
     * Condition number
     * 制約番号
     */
    const DATEOFGRANTED = 22;
    /**
     * Condition number
     * 制約番号
     */
    const DEGREENAME = 23;
    /**
     * Condition number
     * 制約番号
     */
    const GRANTOR = 24;
    
    /**
     * search keyword
     * 検索キーワード
     *
     * @var int
     */
    public $detailSearchCondition = null;
    
    /**
     * Junii2 mapping array
     * JuNii2マッピング配列
     *
     * @var array
     */
    public $Junii2Mapping = array();
}

?>
