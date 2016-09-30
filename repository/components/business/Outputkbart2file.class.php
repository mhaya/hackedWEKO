<?php

/**
 * Repository Components Business Output KBART2 File Class
 * インデックス付属情報出力ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Outputkbart2file.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Repository Components Business Output KBART2 File Class
 * インデックス付属情報出力ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Outputkbart2file extends BusinessBase
{
    /**
     * KBART2 additional data type ID
     * KBART2の付属データタイプID
     */
    const TYPE_ID_KBART = 10001;
    /**
     * KBART2 file name max length
     * KBART2ファイル名最大長
     */
    const MAX_FILE_NAME_LENGTH = 50;
    
    /**
     * Outputs the magazine information to the specified directory
     * 雑誌情報を指定のディレクトリに出力する
     *
     * @param  string $output_dir output directory with slash 出力先パス(末尾スラッシュ)
     * @return string $file_path  KBART2 file full path 作成したファイルフルパス
     * @throws Exception|AppException
     */
    public function outputKbart2ToDirectory($output_dir) {
        // インデックス付属情報管理クラス
        $indexAdditionalDataManager = BusinessFactory::getFactory()->getBusiness("businessIndexadditionaldatamanager");
        
        // 出力データのID一覧を取得する
        $id_list = $indexAdditionalDataManager->getOutputAdditionalDataByTypeId(self::TYPE_ID_KBART);
        // 出力先ファイル名を設定する
        $output_file = $this->makeOutputFileName(self::TYPE_ID_KBART, $output_dir);
        
        // データ変換クラス
        $dataConverter = BusinessFactory::getFactory()->getBusiness("businessConvertkbart2format");
        
        // 出力先ファイルをオープンする
        $fp = fopen($output_dir.$output_file, "w");
        if(!$fp) {
            throw new AppException("Open file is failed.");
        }
        // オープンしたファイルを確実に閉じるために例外はこの階層で一旦捕捉する。
        // エラー判定がされた場合は最後に例外を投げ直す
        try
        {
            if(count($id_list) > 0) {
                // ファイルにヘッダー情報を書き込む
                $dataConverter->writeHeader($fp);
                // データを1行ずつ変換して書き込みを行う
                for($ii = 0; $ii < count($id_list); $ii++) {
                    $dataConverter->writeConvertData($fp, $id_list[$ii]["index_id"], $id_list[$ii]["additionaldata_id"]);
                }
            }
        }
        catch (Exception $ex)
        {
            fclose($fp);
            throw $ex;
        }
        fclose($fp);
        
        return $output_file;
    }
    
    /**
     * Create a data output destination file name
     * 出力ファイル名を作成する
     *
     * @param  int    $output_type_id data type ID データタイプID
     * @return string $file_name      file name    ファイル名
     * @throws AppException
     */
    private function makeOutputFileName($output_type_id) {
        $file_name = "";
        
        // リポジトリ名取得
        $query = "SELECT param_value FROM ". DATABASE_PREFIX. "repository_parameter ".
                 "WHERE param_name = ? ;";
        $params = array();
        $params[] = "prvd_Identify_repositoryName";
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException("Get repository name is Failed.");
        }
        // リポジトリ名から空白・記号を削除
        $repositoryName = str_replace(' ', '', $result[0]["param_value"]);
        $repositoryName = str_replace('　', '', $repositoryName);
        $repositoryName = preg_replace('/["#_$\'|\*,\/.<>`;:?_=\\\]/i','',$repositoryName);
        $repositoryName = mb_substr($repositoryName, 0, self::MAX_FILE_NAME_LENGTH, "UTF-8");
        
        // 実行日付取得
        $date = date("Y-m-d");
        
        // ファイル名作成
        $file_name = $repositoryName."_Global_AllTitles_".$date.".txt";
        
        return $file_name;
    }
}
?>