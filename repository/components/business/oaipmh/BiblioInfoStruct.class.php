<?php

/**
 * Structure class of bibliographic information
 * 書誌情報の構造体クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BiblioInfoStruct.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Structure class of bibliographic information
 * 書誌情報の構造体クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class BiblioInfoStruct
{
    /**
     * Bibliography name
     * 書誌名
     *
     * @var string
     */
    public $biblioName = "";
    
    /**
     * Bibliography name of English
     * 英語の書誌名
     *
     * @var string
     */
    public $biblioNameEnglish = "";
    
    /**
     * Winding of bibliographic
     * 書誌の巻
     *
     * @var string
     */
    public $volume = "";
    
    /**
     * Issue of bibliographic
     * 書誌の号
     *
     * @var string
     */
    public $issue = "";
    
    /**
     * Starting page
     * 開始ページ
     *
     * @var string
     */
    public $startPage = "";
    
    /**
     * Exit page
     * 終了ページ
     *
     * @var string
     */
    public $endPage = "";
    
    /**
     * Issuance of bibliographic date
     * 書誌の発行年月日
     *
     * @var string
     */
    public $dateOfIssued = "";
    
    /**
     * Constructor of structure
     * 構造体のコンストラクタ
     *
     * @param string $biblioName Bibliography name 書誌名
     * @param string $biblioNameEnglish Bibliography name of English 英語の書誌名
     * @param string $volume Winding of bibliographic 書誌の巻
     * @param string $issue Issue of bibliographic 書誌の号
     * @param string $startPage Starting page 開始ページ
     * @param string $endPage Exit page 終了ページ
     * @param string $dateOfIssued Issuance of bibliographic date 書誌の発行年月日
     */
    public function __construct($biblioName, $biblioNameEnglish, $volume, $issue, $startPage, $endPage, $dateOfIssued){
        $this->biblioName = $biblioName;
        $this->biblioNameEnglish = $biblioNameEnglish;
        $this->volume = $volume;
        $this->issue = $issue;
        $this->startPage = $startPage;
        $this->endPage = $endPage;
        $this->dateOfIssued = $dateOfIssued;
    }
}
?>