<?php

/**
 * Structure class summarizes metadata name and metadata value of the specified mapping format, the attribute
 * 指定したマッピング形式のメタデータ名とメタデータ値、属性をまとめた構造体クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: MetadataStruct.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Structure class summarizes metadata name and metadata value of the specified mapping format, the attribute
 * 指定したマッピング形式のメタデータ名とメタデータ値、属性をまとめた構造体クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class MetadataStruct
{
    /**
     * The name of the meta data (representing the like XML tag name)
     * メタデータの名称(XMLのタグ名などを表す)
     *
     * @var string
     */
    public $metadataName = "";
    
    /**
     * Variable representing the metadata in an array of arrays or strings of OaipmhMetadata (sequence of OaipmhMetadata represent a hierarchy, the sequence of strings representing the values of the metadata)
     * メタデータをOaipmhMetadataの配列または文字列の配列で表現した変数(OaipmhMetadataの配列は階層を表し、文字列の配列はメタデータの値を表す)
     *
     * @var array array[$ii|$metadataName]
     */
    public $metadataValue = array();
    
    /**
     * OaipmhAttribute array of string attached attribute to tag
     * タグに紐付く属性のOaipmhAttribute配列
     *
     * @var array array[$ii]
     */
    public $attributes = array();
    
    /**
     * Constructor of structure
     * 構造体のコンストラクタ
     *
     * @param string $metadataName Meta data name メタデータ名
     * @param array $metadataValue Array of metadata values メタデータ値の配列
     *                             array[$ii|$metadataName]
     * @param array $attributes OaipmhAttribute array of string attached attribute to tag タグに紐付く属性のOaipmhAttribute配列
     *                          array[$ii]->OaipmhAttribute
     */
    public function __construct($metadataName, $metadataValue, $attributes){
        $this->metadataName = $metadataName;
        $this->metadataValue = $metadataValue;
        $this->attributes = $attributes;
    }
}
?>