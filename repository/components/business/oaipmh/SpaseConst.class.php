<?php

/**
 * Constant class that defines the constants necessary to Oaipmh output in Spase format
 * Spase形式でのOaipmh出力に必要な定数を定義した定数クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: SpaseConst.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Constant class that defines the constants necessary to Oaipmh output in Spase format
 * Spase形式でのOaipmh出力に必要な定数を定義した定数クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class SpaseConst
{
    /**
     * During the OAI-PMH output, string of SPARSE format
     * OAIPMH出力時、SPASE形式の指定文字列
     * 
     * @var string
     */
    const METADATA_PREFIX = "spase";
    
    /**
     * Root tag name of Space
     * Spaseのルートタグ名
     * 
     * @var string
     */
    const ROOT_TAG_NAME = "Spase";
    
    /**
     * Attribute name that indicates the language of the Space tag
     * Spaseタグの言語を示す属性名
     * 
     * @var string
     */
    const ROOT_TAG_ATTRIBUTE_NAME_LANG = "lang";
    
    /**
     * The default value of the language attribute of the Space tag
     * Spaseタグの言語属性のデフォルト値
     * 
     * @var string
     */
    const ROOT_TAG_ATTRIBUTE_VALUE_LANG_DEFAULT = "en";
    
    /**
     * Attribute name that indicates the Space URL of the namespace and the schema of the tag
     * Spaseタグの名前空間およびスキーマのURLを示す属性名
     * 
     * @var string
     */
    const ROOT_TAG_ATTRIBUTE_NAME_SCHEMA_LOCATION = "xsi:schemaLocation";
    
    /**
     * Attribute name that indicates the instance of the Space tag
     * Spaseタグのインスタンスを示す属性名
     * 
     * @var string
     */
    const ROOT_TAG_ATTRIBUTE_NAME_XMLNS_XSI = "xmlns:xsi";
    
    /**
     * Attribute name that indicates the name space of the Space tag
     * Spaseタグの名前空間を示す属性名
     * 
     * @var string
     */
    const ROOT_TAG_ATTRIBUTE_NAME_XMLNS = "xmlns";
    
    /**
     * Space schema location
     * Spaseスキーマロケーション
     * 
     * @var string
     */
    const SCHEMA_LOCATION = "http://www.spase-group.org/data/schema http://www.spase-group.org/data/schema/spase-2_2_6.xsd";
    
    /**
     * Space name spase
     * Spase名前空間
     * 
     * @var string
     */
    const NAME_SPACE = "http://www.spase-group.org/data/schema";
    
    /**
     * Schema url of Spase
     * SpaseのスキーマURL
     * 
     * @var string
     */
    const SCHEMA = "http://www.spase-group.org/data/schema/spase-2_2_6.xsd";
    
    /**
     * An instance of the schema of Space
     * Spaseのスキーマのインスタンス
     * 
     * @var string
     */
    const INSTANCE = "http://www.w3.org/2001/XMLSchema-instance";
    
    /**
     * Space version
     * Spaseバージョン
     *
     * @var string
     */
    const VERSION = "2.2.6";
}
?>