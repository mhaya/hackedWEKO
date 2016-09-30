<?php

/**
 * DB object wrapper Class
 * DBオブジェクトラッパークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryDbAccess.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * DB object wrapper Class
 * DBオブジェクトラッパークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositoryDbAccess
{
    /**
     * Database Instance
     * DBオブジェクト
     *
     * @var DbObjectAdodb
     */
    private $Db = null;
    
    /**
     * constructor
     * コンストラクタ
     * 
     * @param $Db DbObjectAdodb
     */
    public function __construct(DbObjectAdodb $Db)
    {
        if($Db == null)
        {
            throw new InvalidArgumentException("RepositoryDbAccess : Failed construct, but argument at DbObjectAdodb.");
        }
        $this->Db = $Db;
    }
    
    /**
     * Execute Query
     * クエリ実行
     *
     * @param string $query query of sql SQL文
     * @param array $params query parameters クエリパラメータ
     *                     array(0=>param1, 1=>param2, ...)
     * @return array $result query result クエリ実行結果
     *          bool  false execute failed 実行失敗
     * @throws RepositoryException
     */
    public function executeQuery($query, $params = array())
    {
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            // Set Error Msg
            $errMsg =   $query. '\n'. 
                        print_r($params, true). '\n'. 
                        $this->Db->ErrorMsg();
            
            $exception = new RepositoryException( 'ERR_MSG_EXECUTE_QUERY', 00001 );
            $exception->setDetailMsg($errMsg);
            
            throw $exception;
        }
        
        return $result;
    }
    /**
     * Number of updated or deleted record retrieval
     * 更新・削除レコード数取得
     * @return int  $result update or delete record number 更新/削除レコード数
     *          bool false   No update or delete            更新/削除レコード無し または未サポート
     * @access public
     */
    public function affectedRows() {
        $result = $this->Db->affectedRows();
        return $result;
    }
    /**
     * LOB更新用
     * @param  string  $tableName table name    対象テーブル名称
     * @param  string  $column    column number カラム名称
     * @param  string  $path      file path     パス
     * @param  array   $where     where         場所
     * @param  string  $blobtype  blob type     BLOBタイプ
     * @return bool               true or false 実行結果成否
     */
    function updateBlobFile($tableName, $column, $path, $where, $blobtype='BLOB') {
        $result = $this->Db->UpdateBlobFile($tableName, $column, $path, $where, $blobtype);
        return $result;
    }
    /**
     * Returns the last state or error messages.
     * 最後の状態あるいはエラーメッセージを返します。
     *
     * @return string error message エラーメッセージ
     */
    function ErrorMsg() {
        return $this->Db->ErrorMsg();
    }
    
    /**
     * Db object getter
     * DBオブジェクト取得
     *
     * @return DbObjectAdodb
     */
    public function getDb()
    {
        return $this->Db;
    }
}
?>