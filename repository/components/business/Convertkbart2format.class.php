<?php

/**
 * Repository Components Business Convert KBART2 Format Class
 * KBART2データ形式変換クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Convertkbart2format.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Repository Components Business Convert KBART2 Format Class
 * KBART2データ形式変換クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Convertkbart2format extends BusinessBase
{
    /**
     * KBART2 additional data type ID
     * KBART2の付属データタイプID
     */
    const TYPE_ID_KBART = 10001;

    // データ形式
    /**
     * Format none data
     * 空文字データフォーマット
     */
    const FORMAT_DATA_NONE = 0;
    /**
     * Format DB data
     * DBから取得したデータフォーマット
     */
    const FORMAT_DATA_NORMAL = 1;
    /**
     * Format index ID
     * インデックスIDデータフォーマット
     */
    const FORMAT_DATA_INDEX_ID = 2;
    /**
     * Format Open Search URL
     * OpenSearchのURLデータフォーマット
     */
    const FORMAT_DATA_OPENSEARCH = 3;

    /**
     * KBART2 item ID corresponding sequence
     * KBART項目ID対応配列
     *
     * @var array array[$ii]["id"|header"]
     */
    private $kbartHeader = array();

    /**
     * Associate the item ID and the header of KBART data type
     * KBARTデータタイプの項目IDとヘッダーの対応付けを行う
     */
    protected function onInitialize() {
        // id=0はシステムで決められる値
        $this->kbartHeader = array(
            array("id" => 1, "header" => "publication_title", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 2, "header" => "print_identifier", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 3, "header" => "online_identifier", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 4, "header" => "date_first_issue_online", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 5, "header" => "num_first_vol_online", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 6, "header" => "num_first_issue_online", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 7, "header" => "date_last_issue_online", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 8, "header" => "num_last_vol_online", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 9, "header" => "num_last_issue_online", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 0, "header" => "title_url", "format" => self::FORMAT_DATA_OPENSEARCH),
            array("id" => 0, "header" => "first_author", "format" => self::FORMAT_DATA_NONE),
            array("id" => 0, "header" => "title_id", "format" => self::FORMAT_DATA_INDEX_ID),
            array("id" => 10, "header" => "embargo_info", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 11, "header" => "coverage_depth", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 12, "header" => "notes", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 13, "header" => "publisher_name", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 14, "header" => "publication_type", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 0, "header" => "date_monograph_published_print", "format" => self::FORMAT_DATA_NONE),
            array("id" => 0, "header" => "date_monograph_published_online", "format" => self::FORMAT_DATA_NONE),
            array("id" => 0, "header" => "monograph_volume", "format" => self::FORMAT_DATA_NONE),
            array("id" => 0, "header" => "monograph_edition", "format" => self::FORMAT_DATA_NONE),
            array("id" => 0, "header" => "first_editor", "format" => self::FORMAT_DATA_NONE),
            array("id" => 15, "header" => "parent_publication_title_id", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 16, "header" => "preceding_publication_title_id", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 17, "header" => "access_type", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 18, "header" => "language", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 19, "header" => "title_alternative", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 20, "header" => "title_transcription", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 21, "header" => "ncid", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 22, "header" => "ndl_callno", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 23, "header" => "jstage_code", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 24, "header" => "ichushi_code", "format" => self::FORMAT_DATA_NORMAL),
            array("id" => 0, "header" => "deleted", "format" => self::FORMAT_DATA_NONE)
        );
    }
    
    /**
     * Written to convert the data obtained from the ID to the file
     * IDから取得したデータを変換してファイルへ書き込む
     *
     * @param  resource $fileSource       file source                出力先ファイルリソース
     * @param  int      $indexId          index ID                   インデックスID
     * @param  int      $additionalDataId additional data ID         付属データID
     * @param  string   $delimiter        delimiter                  デリミタ
     * @return bool     true/false        write success/write failed 書込成功/書込失敗
     * @throws AppException
     */
    public function writeConvertData($fileSource, $indexId, $additionalDataId, $delimiter="\t") {
        $additionalDataManager = BusinessFactory::getFactory()->getBusiness("businessAdditionaldatamanager");
        $additionalDataTypeManager = BusinessFactory::getFactory()->getBusiness("businessAdditionaldatatypemanager");
        
        // データを取得する
        // attr[$attribute_id] = $attribute_value
        $attr = array();
        $attr_type = $additionalDataTypeManager->getAttrTypeByTypeId(self::TYPE_ID_KBART, "english");
        for($ii = 0; $ii < count($attr_type); $ii++) {
            $tmpAttr = $additionalDataManager->getAttrByAttrId($additionalDataId, self::TYPE_ID_KBART, $attr_type[$ii]["attribute_id"]);
            $attr[$attr_type[$ii]["attribute_id"]] = $tmpAttr[0]["attribute_value"];
        }
        
        $line = "";
        for($ii = 0; $ii < count($this->kbartHeader); $ii++) {
            // デリミタ
            if($ii > 0) { $line .= $delimiter; }
            
            // 行データ作成
            // 特定のデータは空文字固定となる
            if($this->kbartHeader[$ii]["format"] == self::FORMAT_DATA_NONE) { continue; }
            // 取得したデータ
            elseif($this->kbartHeader[$ii]["format"] == self::FORMAT_DATA_NORMAL) {
                // attribute_valueを設定する
                if(array_key_exists($this->kbartHeader[$ii]["id"], $attr)) {
                    $line .= trim($attr[$this->kbartHeader[$ii]["id"]]);
                }
            }
            // OpenSearchのURL
            elseif($this->kbartHeader[$ii]["format"] == self::FORMAT_DATA_OPENSEARCH) { $line .= BASE_URL."/?action=repository_opensearch&index_id=".$indexId; }
            // インデックスID
            elseif($this->kbartHeader[$ii]["format"] == self::FORMAT_DATA_INDEX_ID) { $line .= $indexId; }
        }
        
        // 行書き込み
        if(fwrite($fileSource, $line."\n") === false) {
            throw new AppException("Write kbart data is failed.");
        }
        
        return true;
    }
    
    /**
     * Written to convert the data obtained from the ID to the file
     * IDから取得したデータを変換してファイルへ書き込む
     *
     * @param  resource $fileSource file source 出力先ファイルリソース
     * @param  string $delimiter    delimiter   デリミタ
     * @return bool     true/false        write success/write failed 書込成功/書込失敗
     * @throws AppException
     */
    public function writeHeader($fileSource, $delimiter="\t") {
        // ヘッダー文字列
        $header = "";
        for($ii = 0; $ii < count($this->kbartHeader); $ii++) {
            if($ii > 0) { $header .= $delimiter; }
            $header .= $this->kbartHeader[$ii]["header"];
        }
        
        // 書き込み
        if(fwrite($fileSource, $header."\n") === false) {
            throw new AppException("Write kbart header is failed.");
        }
        
        return true;
    }
}
?>