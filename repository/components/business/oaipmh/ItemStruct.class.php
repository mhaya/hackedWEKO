<?php

/**
 * Structure class summarizes the metadata name and metadata value for each item in the specified mapping format
 * 指定したマッピング形式でアイテム毎にメタデータ名とメタデータ値をまとめた構造体クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: ItemStruct.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Structure class summarizes the metadata name and metadata value for each item in the specified mapping format
 * 指定したマッピング形式でアイテム毎にメタデータ名とメタデータ値をまとめた構造体クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class ItemStruct
{
    /**
     * Format name when obtaining the item information
     * アイテム情報を取得した際の形式名
     *
     * @var string
     */
    public $tagName = "";
    
    /**
     * Item ID for identifying the item
     * アイテムを特定するためのアイテムID
     *
     * @var int
     */
    public $itemId = 0;
    
    /**
     * Associative array of string attached metadata to an item representing the hierarchical structure
     * 階層構造を表現したアイテムに紐付くメタデータの連想配列
     *
     * @var array array[$tagName]->OaipmhMetadata
     */
    public $metadataList = array();
    
    /**
     * Constructor of structure
     * 構造体のコンストラクタ
     *
     * @param string $tagName Tag name タグ名
     * @param int $itemId Item ID for identifying the item アイテムを特定するためのアイテムID
     * @param array $metadataList Array of metadata that attach straps to the item アイテムに紐付くメタデータの配列
     *                            array[$tagName]->OaipmhMetadata
     */
    public function __construct($tagName, $itemId, $metadataList){
        $this->tagName = $tagName;
        $this->itemId = $itemId;
        $this->metadataList = $metadataList;
    }
}
?>