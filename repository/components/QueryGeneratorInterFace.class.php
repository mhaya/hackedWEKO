<?php

/**
 * Query generator interface
 * クエリ作成クラスインターフェース
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: QueryGeneratorInterFace.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Query generator interface
 * クエリ作成クラスインターフェース
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
interface Repository_Components_Querygeneratorinterface
{
    /**
     * create detail search query
     * 詳細検索クエリ作成
     *
     * @param RepositorySearchQueryParameter $searchInfo detail search parameter object 詳細検索パラメータオブジェクト
     * @param string $searchQuery search query string 検索クエリ文字列
     * @param array $searchQueryParameter search query parameter 検索クエリパラメータ
     *                                     array[$ii]
     */
    public function createDetailSearchQuery($searchInfo, &$searchQuery, &$searchQueryParameter);
}
?>
