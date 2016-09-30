<?php

/**
 * repository edit index tree action
 * ツリー選択Actionクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Treeselect.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/ActionBase.class.php';
/**
 * Download process class
 * ダウンロード処理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';

/**
 * repository edit index tree action
 * ツリー選択Actionクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Edit_Treeselect extends ActionBase
{
    /**
     * KBART2 data type ID
     * KBART2データタイプID
     */
    const TYPE_ID_KBART = 10001;
    
    // request parameter
    /**
     * Selected index ID
     * 選択されたインデックスID
     *
     * @var int
     */
    public $edit_id = null;

    /**
     * Process before transaction
     * トランザクション開始前処理
     */
    final function beforeTrans() {}
    /**
     * Process after transaction
     * トランザクション終了後処理
     */
    final function afterTrans() {}
    /**
     * Process before execute
     * 実行開始前処理
     */
    final function preExecute() {}
    /**
     * Process after execute
     * 実行後処理
     */
    final function postExecute() {}
    
    /**
     * Returns in JSON format to get the index attachment metadata
     * インデックス付属メタデータを取得してJSON形式で出力する
     *
     * @return string "success"/"error" 成功/失敗
     */
    function executeApp()
    {
        $this->Session->removeParameter("tree_error_msg");
        
        $this->exitFlg = true;
        
        // 選択インデックスのチェック
        if(is_numeric($this->edit_id) == 0) {
            return "error";
        }
        
        // 付属データタイプ設定
        $type_id = self::TYPE_ID_KBART;
        
        // 雑誌情報取得
        $indexAdditionalDataManager = BusinessFactory::getFactory()->getBusiness("businessIndexadditionaldatamanager");
        // 画面側の要素名として英名を使うため言語はenglish固定で使用する
        $index_additional_data = $indexAdditionalDataManager->getIndexAdditionalDataById($this->edit_id, $type_id, "english");
        
        // JSON形式で返却
        $this->sendAdditionalDataToHtml($index_additional_data);
        
        return "success";
    }
    
    /**
     * Outputs the data are converted into JSON format
     * データをJSON形式に変換して出力する
     *
     * @param  array $additional_data index additional metadataインデックス付属メタデータ
     *                                 array[0]["additionaldata_type_id"|"additionaldata_type_name"|"additionaldata_type_name_english"|"output_flag"]
     *                                                                   "attribute"][0]["attribute_id"|"attribute_name"|"input_type"|"format"|"is_required"|"plural_enable"|"hidden"|"attribute_value"][$ii]
     */
    private function sendAdditionalDataToHtml($additional_data)
    {
        $repositoryDownload = new RepositoryDownload();
        
        // make JSON
        $json = '';
        $json .= '{';
        for($ii = 0; $ii < count($additional_data); $ii++) {
            if($ii > 0) { $json .= ","; }
            //// 付属データタイプ名
            $json .= '"'.$this->escapeJSON($additional_data[0]["additionaldata_type_name_english"]).'": ';
            $json .= '{';
            //// 出力フラグ
            $json .= '"journal_info": ["'.$additional_data[0]["output_flag"].'"],';
            for($jj = 0; $jj < count($additional_data[$ii]["attribute"]); $jj++) {
                if($jj > 0) { $json .= ","; }
                // 属性タイプ名
                $attr_type = $this->escapeJSON($additional_data[$ii]["attribute"][$jj]["attribute_name"]);
                $json .= '"'.'attr_'.$additional_data[$ii]["attribute"][$jj]["attribute_id"].'": [';
                for($kk = 0; $kk < count($additional_data[$ii]["attribute"][$jj]["attribute_value"]); $kk++) {
                    if($kk > 0) { $json .= ","; }
                    // 属性値
                    $attr = $this->escapeJSON($additional_data[$ii]["attribute"][$jj]["attribute_value"][$kk]);
                    
                    // 日付型の文字列の場合はパースしてオブジェクトとして詰める
                    if($additional_data[$ii]["attribute"][$jj]["input_type"] == "date") {
                        $date = explode("-", $attr);
                        if(isset($date[0])) { $year = $date[0]; } else { $year = ""; }
                        if(isset($date[1])) { $month = $date[1]; } else { $month = ""; }
                        if(isset($date[2])) { $day = $date[2]; } else { $day = ""; }
                        $json .= '{"year": "'.$date[0].'","month": "'.$date[1].'","day": "'.$date[2].'"}';
                    // その他の場合は文字列をそのまま詰める
                    } else {
                        $json .= '"'.$attr.'"';
                    }
                }
                $json .= ']';
            }
            $json .= '}';
        }
        $json .= '}';
        
        $repositoryDownload->download($json, "index_additionaldata_json.txt");
    }
    
    /**
     * Escapes a string for JSON
     * JSON用に文字列をエスケープする
     *
     * @param  string $str     JSON string     変換するJSON文字列
     * @param  bool   $lineFlg new line/or not 改行する/しない
     * @return string escaped JSON string      エスケープ済JSON文字列
     */
    private function escapeJSON($str, $lineFlg=false){
        
        $str = str_replace("\\", "\\\\", $str);
        $str = str_replace('[', '\[', $str);
        $str = str_replace(']', '\]', $str);
        $str = str_replace('"', '\"', $str);
        if($lineFlg){
            $str = str_replace("\r\n", "\n", $str);
            $str = str_replace("\n", "\\n", $str);
        }
        $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        
        return $str;
    }
}

?>