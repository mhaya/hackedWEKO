<?php

/**
 * Batch DB class
 * バッチ用DBクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BatchDbObject.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Batch exit code const class
 * バッチ終了コード定義クラス
 */
require_once(WEBAPP_DIR."/modules/".MODULE_NAME."/batch/FW/BatchExitCodes.class.php");

/**
 * Batch DB class
 * バッチ用DBクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class BatchDbObject {
    /**
     * Character set
     * 文字コード
     */
    const CHARSET = "UTF8";

    /**
     * Batch DB object
     * バッチ用DBオブジェクト
     *
     * @var BatchDbObject
     */
    private $Db = null;
    
    /**
     * Connect to MySQL
     * MySQL接続を行う
     *
     * @param string $host host name接続先ホスト名
     * @param string $dbName DB name 接続先DB名
     * @param string $user user name ユーザー名
     * @param string $pass password パスワード
     * @return BatchDbObject Batch DB object バッチ用DBオブジェクト
     */
    static public function openConnect($host, $dbName, $user, $pass) {
        $DbObject = @mysqli_connect($host, $user, $pass);
        if(!$DbObject) {
            return null;
        }
        mysqli_set_charset($DbObject, self::CHARSET);
        mysqli_select_db($DbObject, $dbName);
        
        return new BatchDbObject($DbObject);
    }

    /**
     * BatchDbObject constructor.
     * コンストラクタ
     *
     * @param BatchDbObject $Db
     */
    protected function __construct($Db) {
        $this->Db = $Db;
    }
    /**
     * Close connect MySQL
     * MySQL接続を終了する
     */
    function closeConnect() {
        mysqli_close($this->Db);
    }

    /**
     * Get error message
     * DBエラーメッセージ取得
     *
     * @return string error message エラーメッセージ
     */
    function ErrorMsg() {
        return mysqli_error($this->Db);
    }

    /**
     * Execute query
     * クエリ実行
     *
     * @param string $query query クエリ文
     * @param array $params parameter クエリパラメータ
     * @return array|bool query result クエリ実行結果
     *          array[$ii][column name]　(SELECT時)
     *          true Success query クエリ実行成功（SELECT以外時)
     *          false failed query クエリ実行失敗（SELECT以外時)
     * @throws Exception
     */
    public function execute($query, $params=array())
    {
        $tmpQueryArray = explode(" ", $query);
        $tmpQueryStr = "";
        $paramsIndex = 0;
        for($ii = 0; $ii < count($tmpQueryArray) ;$ii++) {
            $tmpStr = $tmpQueryArray[$ii];
            // 「{テーブル名}」形式で書かれたテーブル名にプレフィックスを付ける
            if(preg_match("/\{.*\}/", $tmpStr) === 1) {
                $tmpStr = strtr($tmpStr, array('{' => DATABASE_PREFIX."", '}' => ''));
            // パラメータの「?」を置き換える
            } elseif(preg_match("/[\+\(\)\=\,\;]?\?[\+\(\)\=\,\;]?/", $tmpStr) === 1) {
                // NullならNullという文字列を
                if(is_null($params[$paramsIndex])) {
                    $tmpParam = "null";
                // 空文字ならクォーテーションのみ
                } elseif(strlen($params[$paramsIndex]) == 0) {
                    $tmpParam = '""';
                // 数値ならクォーテーションを付けない
                } elseif(is_int($params[$paramsIndex])) {
                    $tmpParam = $params[$paramsIndex];
                // その他ならダブルクォーテーションを付ける
                } else {
                    $tmpParam = '"'.str_replace('"', '\\"', $params[$paramsIndex]).'"';
                }
                $tmpStr = str_replace("?", $tmpParam, $tmpStr);
                $paramsIndex++;
            }
            
            $tmpQueryStr .= $tmpStr." ";
        }
        $ret = mysqli_query($this->Db, $tmpQueryStr);
        if(is_bool($ret)) {
            if($ret === false) {
                throw new Exception($this->ErrorMsg(), BatchExitCodes::ERROR_SQL);
            }
            
            return $ret;
        } else {
                $data = array();
                while($row = mysqli_fetch_array($ret, MYSQLI_ASSOC)) {
                    $data[] = $row;
                }
            return $data;
        }
    }

    /**
     * Get query affected row number
     * クエリ実行結果の有効行取得
     *
     * @return int クエリ有効行数
     */
    function getAffectedRows() {
        return mysqli_affected_rows($this->Db);
    }

    /**
     * Start transaction
     * トランザクション開始
     *
     * @throws Exception
     */
    function StartTrans() {
        $this->execute("SET AUTOCOMMIT = 0;");
        $this->execute("BEGIN;");
    }

    /**
     * Rollback
     * ロールバック
     *
     * @throws Exception
     */
    function FailTrans() {
        $this->execute("ROLLBACK;");
        $this->execute("SET AUTOCOMMIT = 1;");
    }

    /**
     * Commit
     * トランザクション完了
     *
     * @throws Exception
     */
    function CompleteTrans() {
        $this->execute("COMMIT;");
        $this->execute("SET AUTOCOMMIT = 1;");
    }

    /**
     * Get next sequence
     * シーケンス番号発番・取得
     *
     * @param string $table table name テーブル名
     * @return int sequence number シーケンス番号
     * @throws Exception
     */
    function nextseq($table) {
        // シーケンステーブルの存在確認を行う
        $tableName = DATABASE_PREFIX.$table."_seq_id";
        $result = $this->execute("SHOW TABLES LIKE '". $tableName. "' ;");
        
        // 初回時はテーブル作成
        if(count($result) == 0) {
            $this->execute("CREATE TABLE ". $tableName. " (`id` INT NOT NULL); ");
            $this->execute("INSERT INTO ". $tableName. " VALUE (0) ;");
        }
        // シーケンス番号更新
        $this->execute("UPDATE ". $tableName. " SET id = LAST_INSERT_ID(id+1) ;");
        // シーケンス番号取得
        $num = $this->execute("SELECT LAST_INSERT_ID();");
        
        return intval($num[0]["LAST_INSERT_ID()"]);
    }
}
?>
