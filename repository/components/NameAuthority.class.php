<?php

/**
 * Name authority common classes
 * 著者名典拠共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: NameAuthority.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Date manipulation library
 * 日付操作ライブラリ
 */
include_once WEBAPP_DIR. '/modules/repository/files/pear/Date.php';

/**
 * Name authority common classes
 * 著者名典拠共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class NameAuthority extends Action
{
    // member
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    private $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
    private $Db = null;
    /**
     * User id
     * ユーザID
     *
     * @var string
     */
    private $user_id = null;
    /**
     * Date Modified
     * 更新日時
     *
     * @var string
     */
    private $mod_date = null;
    /**
     * Block ID of arranged WEKO to NC2
     * NC2に配置されたWEKOのブロックID
     *
     * @var int
     */
    private $block_id = 0;
    /**
     * ID of the page WEKO is located
     * WEKOが配置されているページのID
     *
     * @var int
     */
    private $room_id = 0;
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $Session Session management objects Session管理オブジェクト
     * @param DbObject $Db Database management objects データベース管理オブジェクト
     * @param int $block_id Block ID of arranged WEKO to NC2 NC2に配置されたWEKOのブロックID
     * @param int $room_id ID of the page WEKO is located WEKOが配置されているページのID
     */
    public function NameAuthority($Session, $Db, $block_id=0, $room_id=0){
        if($Session != null){
            $this->Session = $Session;
        } else {
            return null;
        }
        if($Db != null){
            $this->Db = $Db;
        } else {
            return null;
        }
        $DATE = new Date();
        $this->mod_date = $DATE->getDate().".000";
        $this->setBlockId($block_id);
        $this->setRoomId($room_id);
        $this->user_id = $this->Session->getParameter("_user_id");
    }
    
    /**
     * Set block id
     *
     * @param int $block_id
     */
    /**
     * Set block id
     * ブロックID設定
     *
     * @param int $block_id Block id ブロックID
     */
    public function setBlockId($block_id){
        $this->block_id = $block_id;
    }
    
    /**
     * Set room id
     * ルームID設定
     *
     * @param int $room_id Room id ルームID
     */
    public function setRoomId($room_id){
        $this->room_id = $room_id;
    }
    
    /**
     * Name authority data insertion
     * 著者名典拠データ挿入
     *
     * @param array $params Insert parameters 挿入パラメータ
     *                      array["author_id"|"language"|"family"|"name"|"family_ruby"|"name_ruby"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return boolean Execution result 実行結果
     */
    private function insNameAuthority($params=array())
    {
        $result = $this->Db->insertExecute("repository_name_authority", $params);
        if ($result === false) {
            return false;
        }
        return true;
    }
    
    /**
     * Name authority data update
     * 著者名典拠データ更新
     *
     * @param array $params Update parameters 更新パラメータ
     *                      array["author_id"|"language"|"family"|"name"|"family_ruby"|"name_ruby"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $where_params Condition parameters 条件パラメータ
     *                            array["author_id"|"language"|"family"|"name"|"family_ruby"|"name_ruby"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return boolean Execution result 実行結果
     */
    private function updNameAuthority($params=array(), $where_params=array())
    {
        $result = $this->Db->updateExecute("repository_name_authority", $params, $where_params);
        if ($result === false) {
            return false;
        }
        return true;
    }
    
    /**
     * Name authority acquisition
     * 著者名典拠取得
     * 
     * @param array $where_params Condition parameters 条件パラメータ
     *                            array["author_id"|"language"|"family"|"name"|"family_ruby"|"name_ruby"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array order_params Order parameters 順序パラメータ
     *                           array["author_id"|"language"|"family"|"name"|"family_ruby"|"name_ruby"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param function Callback function コールバック関数
     * @param array Callback argument コールバック引数
     * @return array Execution result 実行結果
     *                         array[$ii]["author_id"|"language"|"family"|"name"|"family_ruby"|"name_ruby"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    public function getNameAuthority($where_params=array(), $order_params=array(), $func = null, $func_param = null)
    {
        $result = $this->Db->selectExecute("repository_name_authority", $where_params, $order_params, null, null, $func, $func_param);
        if ($result === false) {
            return $result;
        }
        return $result;
    }
    
    /**
     * External author ID prefix insertion
     * 外部著者ID prefix挿入
     *
     * @param array $params Insert parameters 挿入パラメータ
     *                      array["prefix_id"|"prefix_name"|"url"|"block_id"|"room_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return boolean Execution result 実行結果
     */
    private function insExternalAuthorIdPrefix($params=array())
    {
        $result = $this->Db->insertExecute("repository_external_author_id_prefix", $params);
        if ($result === false) {
            return false;
        }
        return true;
    }
    
    /**
     * External AuthorId Prefix Update
     * @param array()
     * @return boolean
     * @access  public
     */
    
    /**
     * External author ID prefix update
     * 外部著者ID prefix更新
     *
     * @param array $params Update parameters 挿入パラメータ
     *                      array["prefix_id"|"prefix_name"|"url"|"block_id"|"room_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $where_params Condition parameters 条件パラメータ
     *                            array["prefix_id"|"prefix_name"|"url"|"block_id"|"room_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return boolean Execution result 実行結果
     */
    private function updExternalAuthorIdPrefix($params=array(), $where_params=array())
    {
        $result = $this->Db->updateExecute("repository_external_author_id_prefix", $params, $where_params);
        if ($result === false) {
            return false;
        }
        return true;
    }
    
    /**
     * External author ID prefix acquisition
     * 外部著者ID prefix取得
     * 
     * @param array $where_params Condition parameters 条件パラメータ
     *                            array["prefix_id"|"prefix_name"|"url"|"block_id"|"room_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $order_params Order parameters 順序パラメータ
     *                           array["prefix_id"|"prefix_name"|"url"|"block_id"|"room_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param function $func Callback function コールバック関数
     * @param array $func_param Callback argument コールバック引数
     * @return array Execution result 実行結果
     *               array[$ii]["prefix_id"|"prefix_name"|"url"|"block_id"|"room_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    public function getExternalAuthorIdPrefix($where_params=array(), $order_params=array(), $func = null, $func_param = null)
    {
        $result = $this->Db->selectExecute("repository_external_author_id_prefix", $where_params, $order_params, null, null, $func, $func_param);
        if ($result === false) {
            return $result;
        }
        return $result;
    }
    
    /**
     * Get external authorID prefix list
     *
     * @return array() 
     */
    /**
     * External author ID prefix list acquisition
     * 外部著者ID prefix一覧取得
     *
     * @return array External author ID prefix list 外部著者ID prefix一覧
     *               array[$ii]["prefix_id"|"prefix_name"|"block_id"|"room_id"]
     */
    public function getExternalAuthorIdPrefixList(){
        $query = "SELECT prefix_id, prefix_name, url, block_id, room_id ".
                 "FROM ".DATABASE_PREFIX."repository_external_author_id_prefix ".
                 "WHERE ((block_id = 0 AND room_id = 0) OR (block_id = ? AND room_id = ?)) ".
                 "AND is_delete = 0 ".
                 "AND prefix_id > 0 ".
                 "ORDER BY prefix_id ASC;";
        $params = array();
        $params[] = $this->block_id;  // block_id
        $params[] = $this->room_id;  // room_id
        $result = $this->Db->execute($query, $params);
        if($result===false){
            return false;
        }
        return $result;
    }
    
    /**
     * External author ID prefix and suffix acquisition
     * 外部著者ID prefixおよびsuffix取得
     *
     * @param int $author_id Author id 著者ID
     * @param boolean $getEmailFlag Whether or not to get the e-mail address メールアドレスを取得するか否か
     * @return array External author ID prefix and suffix 外部著者ID prefixおよびsuffix
     *               array[$ii]["suffix.prefix_id"|"suffix.suffix"]
     */
    public function getExternalAuthorIdPrefixAndSuffix($author_id, $getEmailFlag=false){
        $query = "SELECT suffix.prefix_id, suffix.suffix ".
                 "FROM ".DATABASE_PREFIX."repository_external_author_id_suffix AS suffix, ".
                 "     ".DATABASE_PREFIX."repository_external_author_id_prefix AS prefix ".
                 "WHERE suffix.author_id = ? ".
                 "AND suffix.prefix_id = prefix.prefix_id ".
                 "AND ((prefix.block_id = 0 AND prefix.room_id = 0) OR (prefix.block_id = ? AND prefix.room_id = ?)) ".
                 "AND prefix.is_delete = 0 ";
        if(!$getEmailFlag)
        {
            $query .= "AND prefix.prefix_id > 0 ";
        }
        $query .= "ORDER BY suffix.author_id ASC;";
        $params = array();
        $params[] = $author_id; // author_id
        $params[] = $this->block_id;  // block_id
        $params[] = $this->room_id;   // room_id
        $result = $this->Db->execute($query, $params);
        if($result===false){
            return false;
        }
        if(count($result)==0){
            $result = array(array('prefix_id'=>'', 'suffix'=>''));
        }
        return $result;
    }
    
    /**
     * External author ID prefix added
     * 外部著者ID prefix追加
     *
     * @param string $prefix_name External author ID prefix name 外部著者ID prefix名
     * @param int $prefix_id Unique ID of the external author ID prefix 外部著者ID prefixのユニークID
     * @return boolean Execution result 実行結果
     */
    public function addExternalAuthorIdPrefix($prefix_name, $prefix_id=0){
        if($prefix_id==0){
            $prefix_id = $this->getNewPrefixId();
        }
        $params = array(
                        "prefix_id" => $prefix_id,
                        "prefix_name" => $prefix_name,
                        "block_id" => $this->block_id,
                        "room_id" => $this->room_id,
                        "ins_user_id" => $this->user_id,
                        "mod_user_id" => $this->user_id,
                        "del_user_id" => 0,
                        "ins_date" => $this->mod_date,
                        "mod_date" => $this->mod_date,
                        "del_date" => "",
                        "is_delete" => 0
                    );
        $result = $this->insExternalAuthorIdPrefix($params);
        if($result === false){
            return false;
        }
        return $prefix_id;
    }
    
    /**
     * External author ID prefix update
     * 外部著者ID prefix更新
     *
     * @param int $prefix_id Unique ID of the external author ID prefix 外部著者ID prefixのユニークID
     * @param string $url External author ID URL 外部著者ID URL
     * @return boolean Execution result 実行結果
     */
    private function updateExternalAuthorIdPrefix($prefix_id, $url){
        $params = array(
                        "url" => $url,
                        "mod_user_id" => $this->user_id,
                        "del_user_id" => 0,
                        "mod_date" => $this->mod_date,
                        "del_date" => "",
                        "is_delete" => 0
                    );
        $where_params = array("prefix_id" => $prefix_id);
        $result = $this->updExternalAuthorIdPrefix($params, $where_params);
        if($result===false){
            return false;
        }
        return true;
    }
    
    /**
     * Get new external authorID's prefix_id
     *
     * @return int $new_prefix_id
     */
    /**
     * New unique ID acquisition of external author ID
     * 外部著者IDの新規ユニークID取得
     *
     * @return int Unique ID ユニークID
     */
    private function getNewPrefixId(){
        $new_prefix_id = intval($this->Db->nextSeq("repository_external_author_id_prefix"));
        return $new_prefix_id;
    }
    
    /**
     * Entry external authorID prefix data
     * 外部著者IDのプレフィックス情報を追加する
     *
     * @param array $prefix_data External author id prefix data
     *                           外部著者IDのプレフィックス情報
     *                           array[$ii]["prefix_id"|"prefix_name"|"url"]
     *
     * @return boolean Whether or not entry external authorID prefix data success
     *                 外部著者IDのプレフィックス情報の追加に成功したかどうか
     */
    public function entryExternalAuthorIdPrefix($prefix_data){
        // Delete record by block_id and room_id
        $params = array(
                        "mod_user_id" => $this->user_id,
                        "del_user_id" => $this->user_id,
                        "mod_date" => $this->mod_date,
                        "del_date" => $this->mod_date,
                        "is_delete" => 1,
                    );
        $where_params = array(
                                "block_id" => $this->block_id,
                                "room_id" => $this->room_id,
                                "prefix_id!=0" => null,
                                "prefix_id!=1" => null,
                                "prefix_id!=2" => null,
                                "prefix_id!=3" => null
                            );
        $result = $this->updExternalAuthorIdPrefix($params, $where_params);
        if($result===false){
            return false;
        }
        
        // Update or Insert record
        for($ii=0;$ii<count($prefix_data);$ii++){
            if($prefix_data[$ii]["prefix_name"]!="e_mail_address"){
                if(($prefix_data[$ii]["prefix_id"]==0 || $prefix_data[$ii]["prefix_id"]==null) && $prefix_data[$ii]["prefix_name"]!=""){
                    // Insert record
                    $prefixId = $this->addExternalAuthorIdPrefix($prefix_data[$ii]["prefix_name"]);
                    if($prefixId===false){
                        return false;
                    }
                    $result = $this->updateExternalAuthorIdPrefix($prefixId, $prefix_data[$ii]["url"]);
                    if($result===false){
                        return false;
                    }
                } else if($prefix_data[$ii]["prefix_name"]!="") {
                    // Update record
                    $result = $this->updateExternalAuthorIdPrefix($prefix_data[$ii]["prefix_id"], $prefix_data[$ii]["url"]);
                    if($result===false){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    
    /**
     * Get new author_id
     * 新規著者ID取得
     * 
     * @return int Author id 著者ID
     */
    public function getNewAuthorId(){
        $query = "SELECT MAX(author_id) FROM ".DATABASE_PREFIX."repository_name_authority;";
        $result = $this->Db->execute($query);
        if ($result === false) {
            return false;
        }
        $author_id = intval($result[0]['MAX(author_id)'])+1;
        return $author_id;
    }
    
    /**
     * Name authority data acquisition
     * 著者名典拠データ取得
     *
     * @param int $author_id Author id 著者ID
     * @param string $language Language 言語
     * @return array Name authority data 著者名典拠データ
     *               array[$ii]["author_id"|"language"|"family"|"name"|"family_ruby"|"name_ruby"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     */
    public function getNameAuthorityData($author_id, $language){
        $where_params = array(
                                "author_id" => $author_id,
                                "language" => $language,
                                "is_delete" => 0
                            );
        $order_params = array("author_id" => "ASC");
        $result = $this->getNameAuthority($where_params, $order_params);
        return $result;
    }
    
    /**
     * Get the external author ID prefix name
     * 外部著者ID prefix名を取得
     *
     * @param int $prefix_id Unique ID ユニークID
     * @return string External author ID prefix name 外部著者ID prefix名
     */
    public function getExternalAuthorIdPrefixName($prefix_id){
        $where_params = array("prefix_id" => $prefix_id);
        $result = $this->getExternalAuthorIdPrefix($where_params);
        if($result===false){
            return false;
        }
        return $result[0]["prefix_name"];
    }
    
    /**
     * External author ID prefix and suffix get attached straps to the author ID
     * 著者IDに紐付く外部著者ID prefixおよびsuffix取得
     *
     * @param int $author_id Author id 著者ID
     * @return array Execution result 実行結果
     *               array[$ii]["SUFFIX.author_id"|"SUFFIX.prefix_id"|"PREFIX.prefix_name"|"SUFFIX.suffix"]
     */
    public function getExternalAuthorIdData($author_id){
        $query = "SELECT SUFFIX.author_id, SUFFIX.prefix_id, PREFIX.prefix_name, SUFFIX.suffix ".
                 "FROM ".DATABASE_PREFIX."repository_external_author_id_suffix AS SUFFIX, ".
                 "     ".DATABASE_PREFIX."repository_external_author_id_prefix AS PREFIX ".
                 "WHERE SUFFIX.author_id = ? ".
                 "AND SUFFIX.prefix_id = PREFIX.prefix_id ".
                 "AND ((PREFIX.block_id = 0 AND PREFIX.room_id = 0) OR (PREFIX.block_id = ? AND PREFIX.room_id = ?)) ".
                 "AND SUFFIX.is_delete = 0 ".
                 "AND PREFIX.prefix_id > 0 ".
                 "ORDER BY SUFFIX.author_id ASC;";
        $params = array();
        $params[] = $author_id; // author_id
        $params[] = $this->block_id;  // block_id
        $params[] = $this->room_id;   // room_id
        $result = $this->Db->execute($query, $params);
        if($result===false){
            return array();
        }
        return $result;
    }
    
    /**
     * Unique ID acquisition of external author ID prefix
     * 外部著者ID prefixのユニークID取得
     *
     * @param string $prefix_name External author ID prefix name 外部著者ID prefix名
     * @return int Unique ID of external author ID prefix
     */
    public function getExternalAuthorIdPrefixId($prefix_name){
        $where_params = array(
                                "prefix_name" => $prefix_name,
                                "block_id" => $this->block_id,
                                "room_id" => $this->room_id,
                                "is_delete" => 0
                            );
        $result = $this->getExternalAuthorIdPrefix($where_params);
        if($result===false){
            return false;
        }
        if(count($result)>0 && strlen($result[0]["prefix_id"])>0){
            $prefix_id = intval($result[0]["prefix_id"]);
        } else {
            $prefix_id = 0;
        }
        return $prefix_id;
    }
    

    
    /**
     * Search for author information for suggestions display
     * サジェスト表示用に著者情報を検索
     *
     * @param string $surName Last name 姓
     * @param string $givenName Name 名
     * @param string $surNameRuby Last name (reading) 姓(ヨミ)
     * @param string $givenNameRuby Name (reading) 名(ヨミ)
     * @param string $emailAddress Mail address メールアドレス
     * @param string $externalAuthorID External author ID suffix 外部著者ID suffix
     * @param string $language Language 言語
     * @return array Author information 著者情報
     *               array[$ii]["AUTHOR.author_id"|"AUTHOR.family"|"$surNameRuby"|"AUTHOR.family_ruby"|"AUTHOR.name_ruby"|"SUFFIX.suffix"]
     */
    public function searchSuggestData($surName, $givenName, $surNameRuby, $givenNameRuby, $emailAddress, $externalAuthorID, $language=""){
        $query = "SELECT DISTINCT AUTHOR.author_id, AUTHOR.family, AUTHOR.name, ".
                 "AUTHOR.family_ruby, AUTHOR.name_ruby, SUFFIX.suffix ".
                 "FROM ".DATABASE_PREFIX."repository_name_authority AS AUTHOR ".
                 "LEFT JOIN ".
                 "( SELECT author_id, suffix FROM ".DATABASE_PREFIX."repository_external_author_id_suffix WHERE prefix_id = 0) AS SUFFIX ".
                 "ON AUTHOR.author_id = SUFFIX.author_id ";
        $where_query = "";
        $params = array();
        if(strlen($surName)>0){
            if(strlen($where_query)>0){
                $where_query .= "AND ";
            } else {
                $where_query .= "WHERE ";
            }
            $where_query .= "AUTHOR.family LIKE ? ";
            $params[] = $surName."%";
        }
        if(strlen($givenName)>0){
            if(strlen($where_query)>0){
                $where_query .= "AND ";
            } else {
                $where_query .= "WHERE ";
            }
            $where_query .= "AUTHOR.name LIKE ? ";
            $params[] = $givenName."%";
        }
        if(strlen($surNameRuby)>0){
            if(strlen($where_query)>0){
                $where_query .= "AND ";
            } else {
                $where_query .= "WHERE ";
            }
            $where_query .= "AUTHOR.family_ruby LIKE ? ";
            $params[] = $surNameRuby."%";
        }
        if(strlen($givenNameRuby)>0){
            if(strlen($where_query)>0){
                $where_query .= "AND ";
            } else {
                $where_query .= "WHERE ";
            }
            $where_query .= "AUTHOR.name_ruby LIKE ? ";
            $params[] = $givenNameRuby."%";
        }
        if(strlen($emailAddress)>0){
            if(strlen($where_query)>0){
                $where_query .= "AND ";
            } else {
                $where_query .= "WHERE ";
            }
            $where_query .= "SUFFIX.suffix LIKE ? ";
            $params[] = $emailAddress."%";
        }
        if(strlen($externalAuthorID)>0){
            $authorId = $this->getSuggestAuthorBySuffix($externalAuthorID);
            if(count($authorId) > 0) {
                if(strlen($where_query)>0){
                    $where_query .= "AND ";
                } else {
                    $where_query .= "WHERE ";
                }
                for($cnt = 0; $cnt < count($authorId); $cnt++)
                {
                    if($cnt == 0)
                    {
                        $where_query .= "AUTHOR.author_id IN( ?";
                        $params[] = $authorId[$cnt]["author_id"];
                    }
                    else
                    {
                        $where_query .= ", ?";
                        $params[] = $authorId[$cnt]["author_id"];
                    }
                }
                $where_query .= ") ";
            }
            else
            {
                return array();
            }
        }
        if(strlen($language)>0){
            if(strlen($where_query)>0){
                $where_query .= "AND ";
            } else {
                $where_query .= "WHERE ";
            }
            $where_query .= "(AUTHOR.language = ? ".
                            "OR AUTHOR.language = '') ";
            $params[] = $language;  // Selected languege
        }
        $query .= $where_query."ORDER BY AUTHOR.author_id ASC;";
        $result = $this->Db->execute($query, $params);
        if($result===false){
            return false;
        }
        return $result;
    }
    
    /**
     * duplicate key insert external author id
     * 外部著者IDを上書き保存する
     *
     * @param array $extAuthorIdArray External author ID prefix and suffix 外部著者ID prefixおよびsuffix
     *                                array[$ii]["prefix_id"|"suffix"|"old_prefix_id"|"old_suffix"|"prefix_name"]
     * @param int $authorId Author id 著者ID
     */
    private function upsertExternalAuthorId($extAuthorIdArray, $authorId){
        // Prefixが配列内に含まれているので、それを利用して保存する
        // 上書きする必要があるため、Duplicate Key Insertを利用する
        $query = "INSERT INTO ". DATABASE_PREFIX. "repository_external_author_id_suffix ".
                 " (author_id, prefix_id, suffix, ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete )".  
                 " VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ". 
                 " ON DUPLICATE KEY UPDATE ". 
                 "   suffix = ?, mod_user_id = ?, mod_date = ?;";
        for($ii = 0; $ii < count($extAuthorIdArray); $ii++){
            $params = array();
            $params[] = $authorId;
            $params[] = $extAuthorIdArray[$ii]["prefix_id"];
            $params[] = $extAuthorIdArray[$ii]["suffix"];
            $params[] = $this->user_id;
            $params[] = $this->user_id;
            $params[] = "";
            $params[] = $this->mod_date;
            $params[] = $this->mod_date;
            $params[] = "";
            $params[] = 0;
            $params[] = $extAuthorIdArray[$ii]["suffix"];
            $params[] = $this->user_id;
            $params[] = $this->mod_date;
            
            $result = $this->Db->execute($query, $params);
            if($result === false){
                $ex = new Exception($this->Db->ErrorMsg());
                throw $ex;
            }
        }
    }
    
    
    
    /**
     * Get a list of the author ID that partially match the external author ID
     * 外部著者ID群に部分一致する著者IDの一覧を取得する
     *
     * @param array $extAuthorIdArray External author ID prefix and suffix 外部著者ID prefixおよびsuffix
     *                                array[$ii]["prefix_id"|"suffix"|"old_prefix_id"|"old_suffix"|"prefix_name"]
     * @return array List of author ID that matches the one of the external author ID 外部著者IDのいずれかに一致した著者IDの一覧
     *               $authorIds[$ii]["author_id"]
     */
    private function selectAuthorIdList($extAuthorIdArray){
        $params = array();
        $whereString = "";
        for($ii = 0; $ii < count($extAuthorIdArray); $ii++){
            if(strlen($whereString) > 0){
                $whereString .= " OR ";
            } else {
                $whereString = " WHERE ";
            }
            $whereString .= " ( prefix_id = ? AND suffix = ?) ";
            $params[] = $extAuthorIdArray[$ii]["prefix_id"];
            $params[] = $extAuthorIdArray[$ii]["suffix"];
        }
        
        // 入力された外部著者IDとの比較のため、suffixが部分一致する著者IDのリストを作成する
        $query = "SELECT author_id ". 
                 " FROM ". DATABASE_PREFIX. "repository_external_author_id_suffix ". 
                 $whereString. 
                 " GROUP BY author_id ". 
                 " ORDER BY COUNT(author_id) DESC, author_id ASC;";
        
        $authorIds = $this->Db->execute($query, $params);
        if($authorIds === false){
            // データベースエラー
            $ex = new Exception($this->Db->ErrorMsg());
            throw $ex;
        }
        
        return $authorIds;
    }
    
    /**
     * With the exception of the non-input, it is confirmed that there is no difference 
     * in the external author ID stick string to an external author ID and the author ID
     * 未入力を除き、外部著者ID群と著者IDに紐付く外部著者ID群に差異がないことを確認する
     *
     * @param array $extAuthorIdArray External author ID prefix and suffix 外部著者ID prefixおよびsuffix
     *                                array[$ii]["prefix_id"|"suffix"|"old_prefix_id"|"old_suffix"|"prefix_name"]
     * @param int $authorId Authur id 著者ID
     * @return boolean Whether or not there is a difference in the database and input データベースと入力に差異があるか否か
     */
    private function isDiffExternalAuthorId($extAuthorIdArray, $authorId){
        // 著者IDのprefixおよびsuffixを全て取得する
        $query = "SELECT prefix_id, suffix ". 
                 " FROM ". DATABASE_PREFIX. "repository_external_author_id_suffix ". 
                 " WHERE author_id = ? ". 
                 " AND is_delete = ?;";
        $params = array();
        $params[] = $authorId;
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false){
            // データベースエラー
            $ex = new Exception($this->Db->ErrorMsg());
            throw $ex;
        }
        
        // 入力された外部著者ID群と比較する
        $isDiff = false;
        for($jj = 0; $jj < count($extAuthorIdArray); $jj++){
            for($kk = 0; $kk < count($result); $kk++){
                if($extAuthorIdArray[$jj]["prefix_id"] == $result[$kk]["prefix_id"]){
                    if(strcmp($extAuthorIdArray[$jj]["suffix"], $result[$kk]["suffix"]) == 0){
                        // 問題無し
                        break;
                    } else {
                        // 入力された外部著者ID群と著者IDを指定した外部著者ID群の間に差異がある
                        $isDiff = true;
                        break;
                    }
                }
            }
        }
        
        if($isDiff === false){
            // 外部著者IDには合致している
            return false;
        } else {
            // ここまで来て外部著者IDに合致しないものがあった
            return true;
        }
    }
    
    /**
     * identify author id by input external id list and database
     * データベースに登録されている外部著者IDと入力された外部著者ID群から著者を特定し、外部著者IDを登録する
     *
     * @param array $extAuthorIdArray External author ID prefix and suffix 外部著者ID prefixおよびsuffix
     *                                array[$ii]["prefix_id"|"suffix"|"old_prefix_id"|"old_suffix"|"prefix_name"]
     * @param int $authorId Author id 著者ID
     * @return int Author id 著者ID
     */
    private function identifyAuthorId($extAuthorIdArray, $authorId){
        $retAuthorId = 0;
        if(!isset($authorId) || $authorId === 0){
            // 著者の新規登録時
            $retAuthorId = $this->identifyAuthorIdForNew($extAuthorIdArray);
        } else {
            // 著者の更新時
            $retAuthorId = $this->identifyAuthorIdForEdit($extAuthorIdArray, $authorId);
        }
        return $retAuthorId;
    }
    
    
    /**
     * identify author id by external id for new
     * 著者新規登録時に外部著者ID群より著者IDを特定し、外部著者IDを登録する
     *
     * @param array $extAuthorIdArray External author ID prefix and suffix 外部著者ID prefixおよびsuffix
     *                                array[$ii]["prefix_id"|"suffix"|"old_prefix_id"|"old_suffix"|"prefix_name"]
     * @return int Corresponding author ID(0: None) 該当著者ID(0:該当なし)
     */
    private function identifyAuthorIdForNew($extAuthorIdArray){
        $authorId = 0;
        
        // 外部著者ID群の要素が0である時、確実に該当著者は存在しない
        if(count($extAuthorIdArray) > 0){
            // 外部著者ID群に部分一致する著者IDの一覧を取得する
            $authorIds = $this->selectAuthorIdList($extAuthorIdArray);
            
            for($ii = 0; $ii < count($authorIds); $ii++){
                // 入力された外部著者IDとデータベースに保存されている著者IDに紐付く外部著者IDで差異があるかを調べる
                // 未入力分は互いに無視される
                if(!$this->isDiffExternalAuthorId($extAuthorIdArray, $authorIds[$ii]["author_id"])){
                    $authorId = $authorIds[$ii]["author_id"];
                    break;
                }
            }
            
            // upsert
            if($authorId === 0){
                $authorId = $this->getNewAuthorId();
            }
            
            $this->upsertExternalAuthorId($extAuthorIdArray, $authorId);
        } else {
            // 外部著者ID群が空だった場合でも著者IDの発番は実施する
            // 外部著者IDを登録はしないが個人名や著者名典拠にデータを入力するために必要
            $authorId = $this->getNewAuthorId();
        }
        
        return $authorId;
    }
    
    /**
     * is exists mail address by external id list
     * 外部著者ID群内にメールアドレスが存在するかを確認する
     *
     * @param array $extAuthorIdArray External author ID prefix and suffix 外部著者ID prefixおよびsuffix
     *                                array[$ii]["prefix_id"|"suffix"|"old_prefix_id"|"old_suffix"|"prefix_name"]
     * @return boolean Whether or not the e-mail address in the external author ID group is present 外部著者ID群の中にメールアドレスが存在するか否か
     */
    private function isExistsMailaddress($extAuthorIdArray){
        // 外部著者ID群の中からpreifxが0である値がないかを探す
        for($ii = 0; $ii < count($extAuthorIdArray); $ii++){
            if($extAuthorIdArray[$ii]["prefix_id"] === 0){
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * identify author id by external id for editting
     * 編集している著者の著者IDを外部著者IDから特定し、外部著者IDを登録する
     *
     * @param array $extAuthorIdArray External author ID prefix and suffix 外部著者ID prefixおよびsuffix
     *                                array[$ii]["prefix_id"|"suffix"|"old_prefix_id"|"old_suffix"|"prefix_name"]
     * @param int $editAuthorId Edited author ID 編集中著者ID
     * @return int Author ID that you identified from outside the author ID 外部著者IDから特定した著者ID
     */
    private function identifyAuthorIdForEdit($extAuthorIdArray, $editAuthorId){
        if(count($extAuthorIdArray) === 0){
            // 外部著者IDが空である場合、著者の同定を実施することはできない
            // 著者の同定も行われないため、入力された著者IDを返す
            return $editAuthorId;
        }
        
        $authorId = 0;
        
        // 編集中著者IDに紐付く外部著者ID群と入力の外部著者ID群と比較する
        if($this->isDiffExternalAuthorId($extAuthorIdArray, $editAuthorId)){
            // 差異があるならば新しく同定可能な著者IDを探し、登録を実施する
            $authorId = $this->identifyAuthorIdForNew($extAuthorIdArray);
        } else {
            // 外部著者IDを保存する
            $authorId = $editAuthorId;
            
            // 差異がないなら追加分を含めて登録を実施する
            $this->upsertExternalAuthorId($extAuthorIdArray, $authorId);
        }
        
        return $authorId;
    }
    
    /**
     * Entry NameAuthority data
     * 著者名典拠データ登録
     * 
     * @param array $metadata Author information 著者メタデータ
     *                        array["family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"author_id"|"language"]
     *                        array["external_author_id"][x]["prefix_id"|"suffix"]
     * @param string $errMsg Error message エラーメッセージ
     * @param boolean Whether merge is not performed マージが実施されなかったか否か
     */
    public function entryNameAuthority($metadata, &$errMsg, $noMerge=false){
        if(count($metadata)==0){
            $errMsg = "Cannot regist author data.";
            return false;
        }
        
        $metadata["author_id"] = $this->identifyAuthorId($metadata["external_author_id"], $metadata["author_id"]);
        
        // Check exist same author ID
        $result = $this->getNameAuthorityData($metadata["author_id"], $metadata["language"]);
        if(count($result)==0){
            // Insert
            $params = array(
                            "author_id" => $metadata["author_id"],
                            "language" => $metadata["language"],
                            "family" => $metadata["family"],
                            "name" => $metadata["name"],
                            "family_ruby" => $metadata["family_ruby"],
                            "name_ruby" => $metadata["name_ruby"],
                            "ins_user_id" => $this->user_id,
                            "mod_user_id" => $this->user_id,
                            "del_user_id" => 0,
                            "ins_date" => $this->mod_date,
                            "mod_date" => $this->mod_date,
                            "is_delete" => 0
                        );
            $result = $this->insNameAuthority($params);
            if($result === false){
                $errMsg = $this->Db->ErrorMsg();
                return false;
            }
        } else if(count($result)>0){
            // Add author data
            // 空のカラムがある場合、追加更新を行う
            $update_params = array();
            $where_params = array();
            if(strlen($result[0]["family"])==0 && strlen($metadata["family"])>0){
                $update_params["family"] = $metadata["family"];
            }
            if(strlen($result[0]["name"])==0 && strlen($metadata["name"])>0){
                $update_params["name"] = $metadata["name"];
            }
            if(strlen($result[0]["family_ruby"])==0 && strlen($metadata["family_ruby"])>0){
                $update_params["family_ruby"] = $metadata["family_ruby"];
            }
            if(strlen($result[0]["name_ruby"])==0 && strlen($metadata["name_ruby"])>0){
                $update_params["name_ruby"] = $metadata["name_ruby"];
            }
            if(count($update_params)>0){
                $update_params["mod_user_id"] = $this->user_id;
                $update_params["mod_date"] = $this->mod_date;
                $where_params = array(
                                    "author_id" => $metadata["author_id"],
                                    "language" => $metadata["language"]
                                );
                $result = $this->updNameAuthority($update_params, $where_params);
                if($result === false){
                    $errMsg = $this->Db->ErrorMsg();
                    return false;
                }
            }
        }
        
        return $metadata["author_id"];
    }
    
    /**
     * Get the author information from external author ID
     * 外部著者IDから著者情報を取得
     *
     * @param int $prefixId Unique ID of the external author ID prefix 外部著者ID prefixのユニークID
     * @param string $suffix External author ID suffix 外部著者ID suffix
     * @return array Author information 著者情報
     *               array[$ii]["AUTHOR.author_id"|"AUTHOR.language"|"AUTHOR.family"|"AUTHOR.name"|"AUTHOR.family_ruby"|"AUTHOR.name_ruby"|"SUFFIX.prefix_id"|"SUFFIX.suffix"]
     *               array[$ii]["external_author_id"][$ii]["suffix.prefix_id"|"suffix.suffix"]
     */
    public function getAuthorByPrefixAndSuffix($prefixId, $suffix){
        $query = "SELECT AUTHOR.author_id, AUTHOR.language, AUTHOR.family, ".
                 "AUTHOR.name, AUTHOR.family_ruby, AUTHOR.name_ruby, ".
                 "SUFFIX.prefix_id, SUFFIX.suffix ".
                 "FROM ". DATABASE_PREFIX ."repository_external_author_id_suffix AS SUFFIX ".
                 "INNER JOIN ".DATABASE_PREFIX ."repository_name_authority AS AUTHOR ".
                 "ON SUFFIX.author_id = AUTHOR.author_id ".
                 "WHERE SUFFIX.prefix_id = ? ".
                 "AND SUFFIX.suffix = ? ".
                 "AND SUFFIX.prefix_id >= 0 ".
                 "AND SUFFIX.is_delete = 0 ".
                 "AND AUTHOR.is_delete = 0;";
        $params = array();
        $params[] = $prefixId;    // prefix_id
        $params[] = $suffix;    // suffix
        // Execution SELECT
        $author_id_suffix = $this->Db->execute($query, $params);
        if($author_id_suffix === false){
            return false;
        }
        if(count($author_id_suffix) != 0){
            for($ii=0; $ii<count($author_id_suffix); $ii++){
                $author_id_suffix[$ii]["external_author_id"] = $this->getExternalAuthorIdPrefixAndSuffix($author_id_suffix[$ii]["author_id"], true);
            }
        }
        return $author_id_suffix;
    }
    
    /**
     * Get author by PrefixID and Suffix
     * 著者IDを外部著者IDから取得
     *
     * @param string $suffix External author ID suffix 外部著者ID suffix
     * @return int Author id 著者ID
     */
    public function getSuggestAuthorBySuffix($suffix){
        $query = "SELECT DISTINCT author_id ".
                 "FROM ". DATABASE_PREFIX ."repository_external_author_id_suffix ".
                 "WHERE suffix LIKE ? ".
                 "AND prefix_id > 0 ".
                 "AND is_delete = 0;";
        $params = array();
        $params[] = $suffix."%";    // suffix
        // Execution SELECT
        $author_id = $this->Db->execute($query, $params);
        if($author_id === false){
            return false;
        }
        return $author_id;
    }
}

?>
