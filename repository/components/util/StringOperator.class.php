<?php

/**
 * Common class to operate string
 * 文字列の操作を行う共通クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: StringOperator.class.php 70936 2016-08-09 09:53:57Z keiya_sugimoto $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Common class to operate string
 * 文字列の操作を行う共通クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Util_Stringoperator
{
    /**
     * Delimiter (pipe)
     * デリミタ（パイプ）
     *
     * @var string
     */
    const DELIMITER_PIPE = "|";

    /**
     * To convert a string of pipe-delimited to the array by checking the contents
     * パイプ区切りの文字列を内容をチェックして配列へ変換する
     *
     * @param string $idStr ID string of pipe-delimited
     *                         パイプ区切りのID文字列
     * @param string $nameStr Name string of pipe-delimited
     *                        パイプ区切りの名前文字列
     * @param array $idArray Checked Index Id List for return
     *                          画面でチェックされたインデックスのIDリスト(返却用)
     *                          array[$ii]
     * @param array $nameArray Checked Index Name List for return
     *                            画面でチェックされたインデックスの名前リスト(返却用)
     *                            array[$ii]
     * @param string $delimiter Delimiter string (default: the pipe)
     *                           デリミタ文字列(デフォルト：パイプ文字)
     * @return boolean  true/false Whether or not the process has been executed
     *                              処理が実行されたか否か
     */
    public static function explodeIdAndName($idStr, $nameStr, &$idArray, &$nameArray, $delimiter=self::DELIMITER_PIPE) {
        // 引数：$idStrに値が入っていなければ処理を行わない
        if(strlen($idStr) == 0) return false;

        // 引数：$nameStrがNULLでなければ名前文字列処理フラグをONにする
        if(is_null($nameStr)) {
            $existName = false;
        } else {
            $existName = true;
        }

        // ID文字列をデリミタで配列に変換する
        $tmpIds = explode($delimiter, $idStr);
        // 名前文字列があればデリミタで配列に変換する
        if($existName) {
            $tmpNames = explode($delimiter, $nameStr);
            // IDと名前の要素数が異なる場合エラー
            if(count($tmpIds) != count($tmpNames)) return false;
        }

        // 重複判定用配列
        $processedId = array();

        // ID要素のチェック
        for($ii = 0; $ii < count($tmpIds); $ii++) {
            // ID文字列の前後の半角・全角スペースを除去する
            $id = trim(mb_convert_kana($tmpIds[$ii], "s", "UTF-8"));
            // IDは1以上の数値の必要があるため、それ以外は処理しない
            if(!(preg_match("/^[1-9][0-9]*$/", $id) === 1)) continue;
            // IDに重複があった場合、2つ目以降は処理を飛ばす
            if(in_array($id, $processedId, TRUE)) continue;

            // 引数配列にインデックス情報を入力する
            $idArray[] = $id;
            if($existName) $nameArray[] = $tmpNames[$ii];
            // 処理済インデックスIDを重複判定用配列に追加する
            $processedId[] = $id;
        }

        return true;
    }
    
    /**
     * Extract file name from file path
     * ファイルパスからファイル名を抽出する
     *
     * @param string $filePath File path ファイルパス
     * @return string File name extracted from file path ファイルパスから抽出されたファイル名
     */
    public static function extractFileNameFromFilePath($filePath){
        $filePathSplited = preg_split('/[\/\\\\]+/', $filePath);
        $fileName = $filePathSplited[count($filePathSplited) - 1];
        return $fileName;
    }
    
    /**
     * make random string
     * 指定した桁数のランダム文字列を作成する
     *
     * @param int $length string length 文字数
     * @return string random string 乱数文字列
     */
    public function makeRandStr($length) {
        $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
        $r_str = null;
        for ($i = 0; $i < $length; $i++) {
            $r_str .= $str[rand(0, count($str) - 1)];
        }
        return $r_str;
    }
    
    /**
     * Extract extension from file name
     * ファイル名から拡張子を抽出する
     *
     * @param string $filename File name
     *                         ファイル名
     * @return string Extension
     *                拡張子
     */
    public static function extractExtensionFromFilename($filename)
    {
        $extension = "";
        $pos = strrpos($filename, '.');
        if($pos !== false)
        {
            $extensionLength = strlen($filename) - ($pos+1);
            if($extensionLength > 0)
            {
                $extension = substr($filename, $pos+1);
            }
        }
        
        return $extension;
    }
    
    /**
     * Replace [;] to [;;] to output [;] by SWORD Client for WEKO
     * SWORD Client for WEKOで[;]を出力するため、[;;]に置換する
     *
     * @param string $str String
     *                    文字列
     * @return string String after replace
     *                置換後の文字列
     */
    public static function replaceSemicolonToDouble($str)
    {
        return str_replace(";", ";;", $str);
    }
}
?>
