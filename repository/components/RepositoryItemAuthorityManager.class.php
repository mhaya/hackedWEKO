<?php

/**
 * Item authority common classes
 * アイテム権限共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryItemAuthorityManager.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Logic abstract class
 * WEKOロジック基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryLogicBase.class.php';
/**
 * Index authority manager class
 * インデックス権限管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryIndexAuthorityManager.class.php';

/**
 * Item authority common classes
 * アイテム権限共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositoryItemAuthorityManager extends RepositoryLogicBase
{
    /**
     * check item public flg
     * アイテム公開フラグをチェックする
     *
     * @param int $item_id item_id アイテムID
     * @param int $item_no item_no アイテム通番
     * @param int $adminBaseAuth admin base authority 管理者ベース権限
     * @param int $adminRoomAuth admin room authority  管理者ルーム権限
     * @param bool $harvest_flg harvest_flg ハーベスト公開フラグ
     * @return bool true/false public/close 公開/非公開
     */
    public function checkItemPublicFlg($item_id, $item_no, $adminBaseAuth, $adminRoomAuth, $harvest_flg=null)
    {
        $repositoryIndexAuthorityManager = new RepositoryIndexAuthorityManager($this->Session, $this->dbAccess, $this->transStartDate);
        
        // check item public
        $query = "SELECT ins_user_id ".
                " FROM ".DATABASE_PREFIX."repository_item ".
                " WHERE item_id = ? ".
                " AND item_no = ? ".
                " AND shown_status = ? ".
                " AND shown_date <= '".$this->transStartDate."' ".
                 "AND is_delete = ? ";
        $param = array();
        $param[] = $item_id;
        $param[] = $item_no;
        $param[] = 1;
        $param[] = 0;
        $result = $this->dbAccess->executeQuery($query, $param);
        
        if($result === false){
            return false;
        } else if(count($result) == 0){
            return false;
        } else if(count($result) > 1){
            return false;
        }
        
        // check position index public
        $query = "SELECT index_id ".
                " FROM ".DATABASE_PREFIX."repository_position_index ".
                " WHERE item_id = ? ".
                " AND item_no = ? ".
                " AND is_delete = 0 ; ";
        $param = array();
        $param[] = $item_id;
        $param[] = $item_no;
        $result = $this->dbAccess->executeQuery($query, $param);
        if($result === false){
            echo $this->Db->mysqlError();
            return false;
        } else if(count($result) == 0){
            return false;
        }else if(count($result) > 0){
            for($ii=0; $ii<count($result); $ii++){
                $public_index = $repositoryIndexAuthorityManager->getPublicIndex($harvest_flg, $adminBaseAuth, $adminRoomAuth, $result[$ii]["index_id"]);
                if(count($public_index) > 0){
                    // index is public
                    // and item public
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * construct
     * コンストラクタ
     *
     * @param Session $session Session object セッションオブジェクト
     * @param RepositoryDbAccess $dbAccess DB object DBオブジェクト
     * @param string $transStartDate process start date 処理開始時間
     */
    public function __construct($session, $dbAccess, $transStartDate)
    {
        parent::__construct($session, $dbAccess, $transStartDate);
    }
}

?>
