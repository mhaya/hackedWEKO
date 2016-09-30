<?php

/**
 * Action class for load the item type authority
 * アイテムタイプ権限読込用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Load.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Item type manager class
 * アイテムタイプ管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/ItemtypeManager.class.php';
/**
 * NC2 user authority manager class
 * NC2ユーザー権限管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryUserAuthorityManager.class.php';

/**
 * Action class for load the item type authority
 * アイテムタイプ権限読込用アクションクラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Itemtype_Auth_Load extends RepositoryAction
{
    /**
     * Exclusive base auth JSON string
     * 除外ベース権限JSON出力文字列
     */
    const EXCLUSIVE_BASE_AUTH = "exclusive_base_auth";
    /**
     * Base auth JSON string
     * ベース権限JSON出力文字列
     */
    const BASE_AUTH = "base_auth";
    /**
     * Room auth JSON string
     * ルーム権限JSON出力文字列
     */
    const ROOM_AUTH = "room_auth";

    /**
     * Item type ID
     * アイテムタイプID
     *
     * @var int
     */
    var $item_type_id = null;

    /**
     * Execute
     * 実行
     */
    function executeApp()
    {
        // NC2に存在するユーザー権限情報を取得
        $userAuthManager = new RepositoryUserAuthorityManager($this->Session, $this->Db, $this->TransStartDate);
        $user_auth = $userAuthManager->getAllAuthority();
        
        // アイテムタイプの権限情報取得
        $base_auth = array();
        $room_auth = array();
        $itemtypeAuthManager = new Repository_Components_ItemtypeManager($this->Session, $this->Db, $this->TransStartDate);
        $itemtypeAuthManager->getItemtypeAuthority($this->item_type_id, $base_auth, $room_auth);
        
        // 権限情報をJSON形式で出力する
        // ベース権限（閲覧権限なし）、ベース権限（閲覧権限あり）、ルーム権限に項目を分けて出力する
        // 出力形式は以下の通り
        /* {
            exclusive_base_auth: [{key1: value1}, {key2: value2}...],
            base_auth:           [{key1: value1}, {key2: value2}...],
            room_auth:           "Int"
           }
        */
        $exclusive_base_auth_json = '';
        $base_auth_json = '';
        $room_auth_json = '';
        
        // ベース権限の判定
        for($ii = 0; $ii < count($user_auth); $ii++) {
            $exclusive_flag = false;
            for($jj = 0; $jj < count($base_auth); $jj++) {
                if($user_auth[$ii]["role_authority_id"] == $base_auth[$jj]["exclusive_base_auth_id"]) {
                    $exclusive_flag = true;
                    break;
                }
            }
            if($exclusive_flag) {
                // 除外ベース権限である
                if(strlen($exclusive_base_auth_json) > 0) {
                    $exclusive_base_auth_json .= ',';
                } else {
                    $exclusive_base_auth_json .= '[';
                }
                $exclusive_base_auth_json .= '{"'. $user_auth[$ii]["role_authority_id"].'": "'. $this->escapeJSON($user_auth[$ii]["role_authority_name"]).'"}';
            } else {
                // 使用可能なベース権限である
                if(strlen($base_auth_json) > 0) {
                    $base_auth_json .= ',';
                } else {
                    $base_auth_json .= '[';
                }
                $base_auth_json .= '{"'. $user_auth[$ii]["role_authority_id"].'": "'. $this->escapeJSON($user_auth[$ii]["role_authority_name"]).'"}';
            }
        }
        
        // 値がある場合は閉じ括弧を追記する。空配列だった場合は""だけを入れる
        if(strlen($exclusive_base_auth_json) > 0) {
            $exclusive_base_auth_json .= ']';
        } else {
            $exclusive_base_auth_json = '""';
        }
        if(strlen($base_auth_json) > 0) {
            $base_auth_json .= ']';
        } else {
            $base_auth_json = '""';
        }
        
        // ルーム権限
        if(isset($room_auth) && count($room_auth) > 0) {
            $room_auth_json .= '"'. $room_auth[0]["exclusive_room_auth_id"]. '"';
        } else {
            // 権限設定が設定されていなかった場合はどのユーザーでも使用可能
            $room_auth_json .= '"-1"';
        }
        // JSON文字列を作成する
        $json = '{'.
                '"'. self::EXCLUSIVE_BASE_AUTH. '": '. $exclusive_base_auth_json. ','.
                '"'. self::BASE_AUTH.'": '. $base_auth_json. ','.
                '"'. self::ROOM_AUTH.'": '. $room_auth_json.
                '}';
        echo $json;
        
        exit();
    }
    
    /**
     * escape JSON
     * JSON文字列用エスケープ処理
     *
     * @param string $str string 文字列
     * @param bool $lineFlg new line flag 改行フラグ
     * @return string escaped JSON string エスケープ済JSON用文字列
     */
    function escapeJSON($str, $lineFlg=false){
        
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
