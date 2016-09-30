<?php

/**
 * Convert Search Word business class
 * 検索文字変換ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Convertsearchword.class.php 64424 2016-03-08 10:44:33Z keiya_sugimoto $
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
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';

/**
 * Convert Search Word business class
 * 検索文字変換ビジネスクラス
 * 
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Convertsearchword extends BusinessBase
{
    /**
     * Convert search string so as to correspond to variants
     * 検索文字列を異体字に対応するように変換する
     *
     * @param string $str Search word
     *                    検索文字列
     *
     * @return string Search string after converting variants
     *                異体字変換後の検索文字列
     */
    public function convertSearchWordToCorrespondVariants($str) {
        // 検索文字列を1文字ずつに分解して配列にする
        $strArray = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
        
        $ret = "";
        for($ii = 0; $ii < count($strArray); $ii++) {
            $variantList = array();
            // シングルバイト文字列は異体字の検索をしない
            if(strlen($strArray[$ii]) >= 2)
            {
                // 文字に一致する異体字の一覧を取得する
                $variantList = $this->createVariantListOfSearchCharacter($strArray[$ii]);
            }
            
            if($variantList == false || count($variantList) <= 1)
            {
                if(strlen($ret) > 0 && mb_substr($ret, -1) == ")" && mb_substr($ret, -2) != "\\")
                {
                    $ret .= " +";
                }
                $ret .= $strArray[$ii];
            }
            else
            {
                if(strlen($ret) > 0)
                {
                    $ret .= " +";
                }
                $ret .= "(";
                for($jj = 0; $jj < count($variantList); $jj++)
                {
                    if($jj > 0)
                    {
                        $ret .= " ";
                    }
                    $ret .= $variantList[$jj]["variants"];
                }
                $ret .= ")";
            }
        }
        
        return $ret;
    }
    
    /**
     * Create variant list that matches the character
     * 文字に一致する異体字リストを作成する
     *
     * @param string $char Search character
     *                     検索文字
     *
     * @return array Variant List of search character
     *               検索文字の異体字リスト
     *               array[$ii]["variants"]
     */
    private function createVariantListOfSearchCharacter($char) {
        $query = "SELECT VAR.variants ".
                 " FROM " . DATABASE_PREFIX . "repository_variant_master AS VAR ".
                 " WHERE VAR.group_no = (".
                 " SELECT VAR_TMP.group_no ".
                 " FROM " . DATABASE_PREFIX . "repository_variant_master AS VAR_TMP ".
                 " WHERE VAR_TMP.variants = ? ".
                 " LIMIT 1 ); ";
        $params = array();
        $params[] = $char;
        $variantList = $this->Db->execute($query, $params);
        
        return $variantList;
    }
}
?>