<?php

/**
 * Action class for the file update history operation
 * ファイル更新履歴操作用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: History.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Action base class for WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/common/WekoAction.class.php';

/**
 * Action class for the file update history operation
 * ファイル更新履歴操作用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Main_History extends WekoAction
{
    /**
     * Item ID of the updated file
     * 更新対象のファイルのアイテムID
     *
     * @var int
     */
    public $item_id = null;
    
    /**
     * Item serial number of the updated file
     * 更新対象のファイルのアイテム通番
     *
     * @var int
     */
    public $item_no = null;
    
    /**
     * Attribute ID of the updated file
     * 更新対象のファイルの属性ID
     *
     * @var int
     */
    public $attr_id = null;
    
    /**
     * File serial number of the updated file
     * 更新対象のファイルのファイル通番
     *
     * @var int
     */
    public $file_no = null;
    
    /**
     * Version of the updated file
     * 更新対象のファイルのバージョン
     *
     * @var int
     */
    public $version = null;
    
    /**
     * Show the changes of the public situation
     * 公開状況の変更内容を示す
     *
     * @var int
     */
    public $shown_state = null;
    
    /**
     * To update the information that is described in the file update history list
     * ファイル更新履歴一覧に記載されている情報を更新する
     *
     * @return string Result code 実行結果
     */
    protected function executeApp(){
        // 公開・非公開を切り替える
        $businessName = "businessFileupdatehistorylistoperator";
        $business = BusinessFactory::getFactory()->getBusiness($businessName);
        
        if($this->shown_state == 0){
            // to private
            $business->ChangeToPrivate($this->item_id, $this->attr_id, $this->file_no, $this->version);
        } else {
            // to public
            $business->ChangeToPublic($this->item_id, $this->attr_id, $this->file_no, $this->version);
        }
        
        return "success";
    }
}
?>