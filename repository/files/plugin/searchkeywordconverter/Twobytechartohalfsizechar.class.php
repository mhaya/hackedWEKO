<?php
/**
 * Class for convert metadata to half size char
 * メタデータ文字半角変換クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Twobytechartohalfsizechar.class.php 69174 2016-06-22 06:43:30Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Search keyword converter abstract class
 * 検索キーワード変換基底クラス
 */
require_once WEBAPP_DIR."/modules/repository/components/Searchkeywordconverter.class.php";

/**
 * Class for convert metadata to half size char
 * メタデータ文字半角変換クラス
 * 
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Files_Plugin_Searchkeywordconverter_Twobytechartohalfsizechar extends Repository_Components_Searchkeywordconverter
{
    
    /**
     * Convert metadata to half size from metadata information
     * メタデータ項目情報から渡されたメタデータ値を半角に変換する
     *
     * @param string $metadata Input metadata of item
     *                         アイテムのメタデータ入力値
     * @param ToSearchKey $metadataInfo Metadata information
     *                                  メタデータ項目情報
     *
     * @return string Metadata converted to half size
     *                半角に変換されたメタデータ値
     */
    public function toSearchKey($metadata, $metadataInfo)
    {
    	return $this->convertToHalfByte($metadata);
    }
    
    /**
     * 検索時に入力されたキーワードを半角に変換
     *
     * @param string $searchKeyword Input keyword at search
     *                              検索時に入力されたキーワード
     * @param ToSearchCondition $searchCondition Search condition information
     *                                           検索条件情報
     *
     * @return string Search keyword converted to half size
     *                半角に変換された検索キーワード
     */
    public function toSearchCondition($searchKeyword, $searchCondition)
    {
    	return $this->convertToHalfByte($searchKeyword);
    }
    
    /**
     * Convert string to half size
     * 文字列を半角に変換する
     *
     * @param string $keyword Converted string
     *                        変換する文字列
     *
     * @return string String converted to half size
     *                半角に変換された文字列
     */
    private function convertToHalfByte($keyword)
    {
    	$keyword = mb_convert_kana($keyword, "ask", "UTF-8");
    	
		// yen mark
		$yen = chr(hexdec('EF')).chr(hexdec('BF')).chr(hexdec('A5'));
		$keyword = str_replace($yen, "\\", $keyword);
		
		// double quotation
		$double_quo_st = chr(hexdec('E2')).chr(hexdec('80')).chr(hexdec('9C'));
		$double_quo_en = chr(hexdec('E2')).chr(hexdec('80')).chr(hexdec('9D'));
		$keyword = str_replace($double_quo_st, "\"", $keyword);
		$keyword = str_replace($double_quo_en, "\"", $keyword);
		
		// single quotation
		$single_quo_st = chr(hexdec('E2')).chr(hexdec('80')).chr(hexdec('98'));
		$single_quo_en = chr(hexdec('E2')).chr(hexdec('80')).chr(hexdec('99'));
		$keyword = str_replace($single_quo_st, "'", $keyword);
		$keyword = str_replace($single_quo_en, "'", $keyword);
    	
		// tilde
		$tilde = chr(hexdec('EF')).chr(hexdec('BD')).chr(hexdec('9E'));
		$keyword = str_replace($tilde, "~", $keyword);

    	return $keyword;
    }
}

?>
