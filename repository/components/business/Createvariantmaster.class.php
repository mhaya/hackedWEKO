<?php
/**
 * Create variant master data business class
 * 異体字マスタデータ作成ビジネスクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Createvariantmaster.class.php 69174 2016-06-22 06:43:30Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Business Logic base class
 * ビジネスロジック基底クラス
 * 
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';
/**
 * Operate the file system
 * ファイルシステムの操作を行う
 * 
 */
require_once WEBAPP_DIR. '/modules/repository/components/util/OperateFileSystem.class.php';

/**
 * Create variant master data business class
 * 異体字マスタデータ作成ビジネスクラス
 * 
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Createvariantmaster extends BusinessBase
{
    /**
     * Error code that variants file is not existed
     * 異体字ファイルが存在しないエラーコード
     *
     * @var int
     */
    const ERROR_VARIANTS_FILE_NOT_EXIST = 200;
    
    /**
     * Error code that variants file is empty
     * 異体字ファイルが空であるエラーコード
     *
     * @var int
     */
    const ERROR_VARIANTS_FILE_EMPTY = 201;
    
    /**
     * Error code that variants format is incorrect
     * 異体字の入力形式が不正であるエラーコード
     *
     * @var int
     */
    const ERROR_VARIANTS_FORMAT_INCORRECT = 202;
    
    /**
     * Number of characters gotten from a row of variants file
     * 異体字ファイル1行から取得する文字数
     *
     * @var int
     */
    const NUM_CHAR_FROM_VARIANTS_FILE_ROW = 256;
    
    /**
     * Number of elements in a row of variants file
     * 異体字ファイル1行の要素数
     *
     * @var int
     */
    const NUM_ELEMENTS_VARIANTS_FILE_ROW = 2;
    
    /**
     * Create variant master table
     * 異体字マスタテーブルを作成する
     */
    public function createVariantMasterTable() 
    {
        $query = "CREATE TABLE {repository_variant_master} ( " .
                 " `id` INT(11) NOT NULL, " .
                 " `group_no` INT(11) NOT NULL, " .
                 " `variants` varchar(1) NOT NULL, " .
                 " PRIMARY KEY(`id`) ".
                 " ) ENGINE=InnoDb; ";
        $this->Db->execute($query);
    }
    
    /**
     * Create variant master data
     * 異体字ファイルから異体字マスタデータを作成する
     *
     * @param string $filePath Variant file path
     *                         異体字ファイルのパス
     */
    public function createVariantMasterFromVariantsFile($filePath) 
    {
        // 異体字ファイルの読み込み
        $variantList = $this->readVariantsFile($filePath);
        // 異体字マスタデータの作成
        $this->createVariantMaster($variantList);
    }
    
    /**
     * Read variants data from variants file
     * 異体字ファイルから異体字のデータを読み込む
     * 
     * @param string $filePath Variant file path
     *                         異体字ファイルのパス
     * 
     * @return array Variant list read from variants file
     *               異体字ファイルから読み込まれた異体字のリスト
     *               array["一"|"下"|"高"|...][$ii]
     */
    private function readVariantsFile($filePath)
    {
        // variants.csvが存在しなければエラー終了
        try
        {
            Repository_Components_Util_OperateFileSystem::stat($filePath);
        }
        catch (AppException $ex)
        {
            throw new AppException("Variants file is not existed.", self::ERROR_VARIANTS_FILE_NOT_EXIST, $ex);
        }
        
        // 異体字をグループごとに配列にまとめる
        $variantList = array();
        $fp = Repository_Components_Util_OperateFileSystem::fopen($filePath, "r");
        // fgetcsvの動作を遅くしないため、文字数を定義する
        while( $ret = fgetcsv($fp, self::NUM_CHAR_FROM_VARIANTS_FILE_ROW) ) 
        {
            // 要素数が定義通りでない場合、入力形式不正としてエラー終了
            if(count($ret) != self::NUM_ELEMENTS_VARIANTS_FILE_ROW)
            {
                Repository_Components_Util_OperateFileSystem::fclose($fp);
                throw new AppException("Variants format is incorrect.", self::ERROR_VARIANTS_FORMAT_INCORRECT);
            }
            $variant = $this->convertUTF16HexToUTF8Str($ret[0]);
            $unificationChar = $this->convertUTF16HexToUTF8Str($ret[1]);
            $variantList[$unificationChar][] = $variant;
        }
        Repository_Components_Util_OperateFileSystem::fclose($fp);
        
        // variants.csvが空であればエラー終了
        if(count($variantList) == 0)
        {
            throw new AppException("Variants file is empty.", self::ERROR_VARIANTS_FILE_EMPTY);
        }
        
        return $variantList;
    }
    
    /**
     * Convert UTF-16 character to UTF-8 character
     * UTF-16の文字をUTF-8に変換する
     * 
     * @param string $str UTF-16 character
     *                    UTF-16の文字
     * 
     * @return string UTF-8 character
     *                UTF-8の文字
     */
    private function convertUTF16HexToUTF8Str($str) 
    {
        return mb_convert_encoding(pack("H*", str_replace("0x", "", $str)), "UTF-8", "UTF-16");
    }
    
    /**
     * Create variant master data
     * 異体字マスタデータを作成する
     *
     * @param array $variantList Variant list read from variants file
     *                           異体字ファイルから読み込まれた異体字のリスト
     *                           array["一"|"下"|"高"|...][$ii]
     */
    private function createVariantMaster($variantList) 
    {
        // 異体字マスタテーブルの中身を削除
        // TRUNCATEではROLLBACKができないため、DELETEを使用
        $query = "DELETE FROM ". DATABASE_PREFIX. "repository_variant_master;";
        $result = $this->Db->execute($query);
        if($result == false) {
            throw new AppException("Delete records in table failed.");
        }
        
        // 異体字のリストを順にINSERT
        $id = 1;
        $group_no = 1;
        foreach($variantList as $key => $value) {
            for($ii = 0; $ii < count($value); $ii++) {
                $query = "INSERT INTO ". DATABASE_PREFIX. "repository_variant_master VALUES (?, ?, ?)";
                $params = array();
                $params[] = $id;
                $params[] = $group_no;
                $params[] = $value[$ii];
                
                $result = $this->Db->execute($query, $params);
                if($result == false) {
                    throw new AppException("Insert variant data failed.");
                }
                $id++;
            }
            $group_no++;
        }
   }
}
?>