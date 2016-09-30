<?php

/**
 * Structure class with a string attached attribute name and attribute value to the tag of the specified mapping format
 * 指定したマッピング形式のタグに紐付く属性名と属性値を持つ構造体クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: AttributeStruct.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Structure class with a string attached attribute name and attribute value to the tag of the specified mapping format
 * 指定したマッピング形式のタグに紐付く属性名と属性値を持つ構造体クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class AttributeStruct
{
    /**
     * Attribute name of the attribute to retrieve the XML tags
     * XMLタグに取得する属性の属性名
     *
     * @var string
     */
    public $attributeName = "";
    
    /**
     * String stick attribute value to the attribute name
     * 属性名に紐付く属性値
     *
     * @var string
     */
    public $attributeValue = "";
    
    /**
     * Constructor of structure
     * 構造体のコンストラクタ
     *
     * @param string $attributeName Attribute name 属性名
     * @param string $attributeValue Attribute value 属性値
     */
    public function __construct($attributeName, $attributeValue){
        $this->attributeName = $attributeName;
        $this->attributeValue = $attributeValue;
    }
}
?>