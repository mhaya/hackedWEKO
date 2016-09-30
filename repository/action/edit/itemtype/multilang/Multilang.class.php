<?php
/**
 * Action class for other language setting of meta data item names attached straps to the item type
 * アイテムタイプに紐付くメタデータ項目名の他言語設定用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Aggregatesitelicenseusagestatistics.class.php 68463 2016-06-06 06:05:40Z tomohiro_ichikawa $
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
 * Action class for other language setting of meta data item names attached straps to the item type
 * アイテムタイプに紐付くメタデータ項目名の他言語設定用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Itemtype_Multilang extends RepositoryAction
{
    // 2008/02/25 itemtype を item_type に変更
    // 使用コンポーネントを受け取るため
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    var $Session = null;
    /**
     * DB object
     * DBオブジェクト
     *
     * @var DbObjectAdodb
     */
    var $Db = null;
    
    // リクエストパラメータを受け取るため
    /**
     * Metadata multi language title array
     * メタデータ項目多言語配列
     *
     * @var array
     */
    public $metadata_multititle = null;
    /**
     * Metadata title array
     * メタデータ項目配列
     *
     * @var array
     */
    public $metadata_defaulttitle = null;
    /**
     * Edit flag
     * デフォルトを変更するかのフラグ
     *
     * @var bool
     */
    public $default_edit_flag = null;
    /**
     * Edit ID
     * 編集データID
     *
     * @var int
     */
    public $edit_id = null;
    
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws RepositoryException
     */
    function execute()
    {
        try {
            //アクション初期化処理
            $result = $this->initAction();
            
            if ( $result === false ) {
                $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );    //主メッセージとログIDを指定して例外を作成
                $DetailMsg = null;                              //詳細メッセージ文字列作成
                sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
                $exception->setDetailMsg( $DetailMsg );             //詳細メッセージ設定
                $this->failTrans();                                        //トランザクション失敗を設定(ROLLBACK)
                throw $exception;
            }

            $this->Session->removeParameter("error_code");
            $array_metadata_title = $this->Session->getParameter("metadata_title");
            $array_metadata_multi_title = $this->Session->getParameter("metadata_multi_title");
            if($this->default_edit_flag != "false"){
                $array_metadata_title[$this->edit_id] = $this->metadata_defaulttitle;
            }
            foreach($this->metadata_multititle as $key => $value){
                $array_metadata_multi_title[$this->edit_id][$key] = $value;
            }
            $this->Session->setParameter("metadata_title", $array_metadata_title);
            $this->Session->setParameter("metadata_multi_title", $array_metadata_multi_title);
            
            //アクション終了処理
            $result = $this->exitAction();     //トランザクションが成功していればCOMMITされる
            if ( $result === false ) {
                $exception = new RepositoryException( "ERR_MSG_xxx-xxx3", 1 );    //主メッセージとログIDを指定して例外を作成
                //$DetailMsg = null;                              //詳細メッセージ文字列作成
                //sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx3, $埋込み文字1, $埋込み文字2 );
                //$exception->setDetailMsg( $DetailMsg );             //詳細メッセージ設定
                throw $exception;
            }
            $this->finalize();
            return 'success';
        } catch (RepositoryException $Exception) {
            //アクション終了処理
            $result = $this->exitAction();     //トランザクションが成功していればCOMMITされる
        
            return "error";
        }

    }
}
?>
