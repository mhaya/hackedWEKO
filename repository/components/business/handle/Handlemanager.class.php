<?php

/**
 * Handle management business class
 * ハンドル管理ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Handlemanager.class.php 70520 2016-08-02 10:58:47Z keiya_sugimoto $
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
 * Check grant doi class
 * DOI付与チェックビジネスクラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/doi/Checkdoi.class.php';

/**
 * Handle management business class
 * ハンドル管理ビジネスクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Handle_Handlemanager extends BusinessBase
{
    /**
     * NDL JaLC DOI unique ID
     * NDL JaLC DOI ユニークID
     *
     * @var int
     */
    const ID_LIBRARY_JALC_DOI = 50;
    /**
     * JalC DOI unique ID
     * JaLC DOI ユニークID
     *
     * @var int
     */
    const ID_JALC_DOI = 40;
    /**
     * CrossRef DOI unique ID
     * CrossRef DOI ユニークID
     *
     * @var int
     */
    const ID_CROSS_REF_DOI = 30;
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * DataCite DOI unique ID
     * DataCite DOI ユニークID
     *
     * @var int
     */
    const ID_DATACITE_DOI = 25;
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    /**
     * CNRI unique ID
     * CNRI ユニークID
     *
     * @var int
     */
    const ID_CNRI_HANDLE = 20;
    /**
     * Y handle unique ID
     * YハンドルユニークID
     *
     * @var int
     */
    const ID_Y_HANDLE = 10;
    /**
     * DOI announcement for the address
     * DOIアナウンス用アドレス
     *
     * @var string
     */
    const PREFIX_SELF_DOI = "info:doi/";
    /**
     * Old DOI URL
     * 旧DOI URL
     *
     * @var string
     */
    const PREFIX_LIBRARY_DOI_HTTP_DX = "http://dx.doi.org/";
    /**
     * DOI URL
     * DOI URL
     *
     * @var string
     */
    const PREFIX_LIBRARY_DOI_HTTP = "http://doi.org/";
    /**
     * DOI URL schemes
     * DOIのURNスキーム
     *
     * @var string
     */
    const PREFIX_LIBRARY_DOI_DOI = "doi:";
    
    /**
     * Get LibraryJalcDoi prefix
     * 国立国会図書館prefix取得
     * 
     * @return string prefix prefix
     */
    public function getLibraryJalcDoiPrefix()
    {
        $result = $this->getPrefix(self::ID_LIBRARY_JALC_DOI);
        
        return $result;
    }
    
    /**
     * Get JalcDoi prefix
     * JalC DOI prefix取得
     * 
     * @return string prefix prefix
     */
    public function getJalcDoiPrefix()
    {
        $result = $this->getPrefix(self::ID_JALC_DOI);
        
        return $result;
    }
    
    /**
     * Get CrossRef prefix
     * CrossRef DOI prefix取得
     * 
     * @return string prefix prefix
     */
    public function getCrossRefPrefix()
    {
        $result = $this->getPrefix(self::ID_CROSS_REF_DOI);
        
        return $result;
    }
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * Get DataCite prefix
     * DataCite DOI prefix取得
     * 
     * @return string prefix prefix
     */
    public function getDataCitePrefix()
    {
        $result = $this->getPrefix(self::ID_DATACITE_DOI);
        
        return $result;
    }
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    
    /**
     * Get Y handle prefix
     * Yハンドルprefix取得
     * 
     * @return string prefix prefix
     */
    public function getYHandlePrefix()
    {
        $result = $this->getPrefix(self::ID_Y_HANDLE);
        
        return $result;
    }
    
    /**
     * Get prefix
     * prefix取得
     * 
     * @param int $prefix_id Prefix unique ID PrefixユニークID
     * @return string prefix prefix
     */
    public function getPrefix($prefix_id)
    {
        $query = "SELECT prefix_id".
                 " FROM ".DATABASE_PREFIX."repository_prefix".
                 " WHERE prefix_id IS NOT NULL".
                 " AND prefix_id != ''".
                 " AND id = ?".
                 " AND is_delete = ?".
                 " ;";
        $params = array();
        $params[] = $prefix_id;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        if(count($result) == 0) {
            return "";
        }
        
        return $result[0]['prefix_id'];
    }
    
    /**
     * Check regist suffix is half size number 8 digit
     * 登録するsuffixが半角数字8桁か判定する
     *
     * @param string $suffix Suffix サフィックス
     *
     * @return boolean Whether or not is half size number 8 digit
     *                 半角数字8桁かどうか
     */
    public function checkSuffixHalfSizeNumber8Digit($suffix){
        if(preg_match("/^[\d]{8}$/", $suffix) == 1)
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check prefix or suffix format. if format is invalid, return empty string
     * プレフィックスまたはサフィックスのフォーマットが正しいかチェックし、正しくない場合は空文字を返す
     * 
     * @param string $doi prefix or suffix prefixまたはsuffix
     * @return string prefix or suffix prefixまたはsuffix
     *                any -> format is valid
     *                empty -> format is invalid
     */
    public function checkDoiFormat($doi)
    {
        $match = array();
        
        // 半角英数字、_、-、.、;、(、)、/のみ使用可
        // JaLCのDOI付与ルールに関しては下記に記載
        //  https://japanlinkcenter.org/top/doc/JaLC_tech_journal_article_manual.pdf
        // CrossRefのDOI付与ルールに関しては下記に記載
        //  http://help.crossref.org/obtaining_a_doi_prefix
        //  http://help.crossref.org/establishing_a_doi_suffix_pattern
        if(preg_match("/^(\w|\-|\.|\;|\(|\)|\/)*$/", $doi, $match)==1)
        {
            return $doi;
        }
        else
        {
            return "";
        }
    }
    
    /**
     * Check regist suffix string length is lower 300 format of 「info doi:[prefix]/[suffix]」
     * 登録するsuffixが「info doi:[prefix]/[suffix]」の形式で300字以内か判定する
     *
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param string $sufix Suffix サフィックス
     *
     * @return boolean Whether or not is lower 300
     *                 300字以内かどうか
     */
    public function checkSuffixAvailableDoiStringLength($type, $suffix){
        switch($type)
        {
            case Repository_Components_Business_Doi_Checkdoi::TYPE_JALC_DOI:
                $prefix = $this->getJalcDoiPrefix();
                break;
            case Repository_Components_Business_Doi_Checkdoi::TYPE_CROSS_REF:
                $prefix = $this->getCrossRefPrefix();
                break;
            case Repository_Components_Business_Doi_Checkdoi::TYPE_DATACITE:
                $prefix = $this->getDataCitePrefix();
                break;
            case Repository_Components_Business_Doi_Checkdoi::TYPE_LIBRARY_JALC_DOI:
                $prefix = $this->getLibraryJalcDoiPrefix();
                break;
            default:
                $prefix = "";
                break;
        }
        
        // Prefixがなければfalse
        if(strlen($prefix) == 0)
        {
            return false;
        }
        // 「info doi:[prefix]/[suffix]」が300字より多ければfalse
        if(strlen(self::PREFIX_SELF_DOI.$prefix."/".$suffix) > 300)
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check regist suffix is existed
     * 登録するsuffixが存在するか判定する
     *
     * @param int $item_id Item id 
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param string $sufix Suffix サフィックス
     *
     * @return array Item information having same suffix
     *               同じsuffixを持つアイテムの情報
     *               array["item_id"|"item_no"|"suffix"|"status"|"is_delete"]
     */
    public function checkSuffixExisted($item_id, $item_no, $type, $suffix){
        switch($type)
        {
            case Repository_Components_Business_Doi_Checkdoi::TYPE_JALC_DOI:
                $id = self::ID_JALC_DOI;
                break;
            case Repository_Components_Business_Doi_Checkdoi::TYPE_CROSS_REF:
                $id = self::ID_CROSS_REF_DOI;
                break;
            case Repository_Components_Business_Doi_Checkdoi::TYPE_DATACITE:
                $id = self::ID_DATACITE_DOI;
                break;
            case Repository_Components_Business_Doi_Checkdoi::TYPE_LIBRARY_JALC_DOI:
                $id = self::ID_LIBRARY_JALC_DOI;
                break;
            default:
                $id = "";
                break;
        }
        
        if(strlen($id) > 0)
        {
            $query = "SELECT STATUS.item_id AS item_id, STATUS.item_no AS item_no, suffix, STATUS.status AS status, STATUS.is_delete AS is_delete ".
                     " FROM ".DATABASE_PREFIX."repository_suffix AS SUF, ".DATABASE_PREFIX."repository_doi_status AS STATUS".
                     " WHERE SUF.item_id = STATUS.item_id".
                     " AND SUF.item_no = STATUS.item_no".
                     " AND SUF.id = ?".
                     " AND SUF.suffix = ?";
            $params = array();
            $params[] = $id;
            $params[] = $suffix;
            $result = $this->executeSql($query, $params);
            
            for($cnt = 0; $cnt < count($result); $cnt++)
            {
                if(!($item_id == $result[$cnt]["item_id"] && $item_no == $result[$cnt]["item_no"]))
                {
                    return $result[$cnt];
                }
            }
        }
        $result = array();
        return $result;
    }
}
?>