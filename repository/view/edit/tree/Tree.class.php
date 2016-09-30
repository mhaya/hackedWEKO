<?php

/**
 * View class for index editing screen display
 * インデックス編集画面表示用ビュークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Tree.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * View class for index editing screen display
 * インデックス編集画面表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Edit_Tree extends RepositoryAction
{
    /**
     * Additional data type id of KBART2
     * 雑誌情報の付属メタデータタイプID
     *
     * @var int
     */
    const TYPE_ID_KBART = 10001;

    // member
    /**
     * Error flag
     * エラー発生フラグ
     *
     * @var bool
     */
    public $error_flg = null;
    /**
     * Popup type
     * ポップアップタイプ
     *
     * @var string
     */
    public $view_popup = null;
    // Set help icon setting 2010/02/10 K.Ando --start--
    /**
     * help icon display flag
     * ヘルプアイコン表示フラグ
     *
     * @var int
     */
    public $help_icon_display =  null;
    // Set help icon setting 2010/02/10 K.Ando --end--
    /**
     * Tree error message
     * ツリーエラーメッセージ
     *
     * @var string
     */
    public $tree_error_msg = '';
    
    /**
     * Index additional data 
     * インデックス付属メタデータ
     *
     * @var array[$ii]["input_type"|"attribute_id"|"attribute_name"|"is_required"|"candidate_value"]
     */
    public $metadata = array();
    
    /**
     * Additional data type id for action class
     * アクションクラス用の付属メタデータID
     *
     * @var int
     */
    public $journalTypeId = self::TYPE_ID_KBART;
    
    /**
     * [[機能説明]]
     *
     * @access  public
     */
    function executeApp()
    {
        //ツリー情報をセッションに設定する処理
        $this->Session->removeParameter("error_code");
        $this->Session->removeParameter("error_msg");
        
        $this->tree_error_msg = $this->Session->getParameter("tree_error_msg");
        $this->Session->removeParameter("tree_error_msg");
        $this->Session->removeParameter("MyPrivateTreeRootId");        // Add remove privateTree edit flag  K.Matsuo 2013/04/15
        //タブ押下の場合,open index情報削除
        if($this->Session->getParameter("edit_tree_continue") == null || $this->Session->getParameter("edit_tree_continue") == ""){
            // for open tree node
            // string index_id1,index_id2,index_id3,...
            $this->Session->removeParameter("view_open_node_index_id_edit");
            $this->Session->removeParameter("view_open_node_index_id_editPrivatetree");
            // for tree mod date
            $this->Session->setParameter("tree_mod_Date", $this->TransStartDate);
        }
        // for select index focus
        // string now focus index_id
        $this->Session->removeParameter("edit_index");
        // for edit contine flg
        $this->Session->removeParameter("edit_tree_continue");
        // 更新成功ポップアップ表示用 for update OK popup 
        $this->view_popup = $this->Session->getParameter("repository_edit_update");
        $this->Session->removeParameter("repository_edit_update");
        // get lang resource
        $this->setLangResource();
        
        // Set help icon setting 2010/02/10 K.Ando --start--
        $result = $this->getAdminParam('help_icon_display', $this->help_icon_display, $Error_Msg);
        if ( $result == false ){
            throw new AppException($Error_Msg);
        }
        // Set help icon setting 2010/02/10 K.Ando --end--

        // インデックス付属データ
        $this->metadata = $this->getIndexAdditionalDataType(self::TYPE_ID_KBART);
        
        return "success";
    }
    
    /**
     * Get index additional data
     * インデックス付属メタデータタイプを取得する
     *
     * @param int $additionaldata_type_id Additional Data Type Id
     *                                    付属メタデータタイプID
     * 
     * @return array additional data
     *               付属メタデータ
     *               array[$ii]["input_type"|"attribute_id"|"attribute_name"|"is_required"|"length"|"candidate_value"]
     */
    private function getIndexAdditionalDataType($additionaldata_type_id)
    {
        $additionalDataTypeManager = BusinessFactory::getFactory()->getBusiness("businessAdditionaldatatypemanager");

        // 表示言語を取得
        $language = $this->Session->getParameter("_lang");
        // 属性タイプ取得
        $attr_type = $additionalDataTypeManager->getAttrTypeByTypeId($additionaldata_type_id, $language);
        // 一致する表示言語が無い場合は英語名で取得する
        if(count($attr_type) == 0) {
            $attr_type = $additionalDataTypeManager->getAttrTypeByTypeId($additionaldata_type_id, "english");
        }

        // 選択肢候補取得
        $candidate = $additionalDataTypeManager->getCandidateValueByTypeId($additionaldata_type_id, $language);

        // 属性タイプと選択肢情報の配列をマージする
        for($ii = 0; $ii < count($attr_type); $ii++) {
            $attr_type[$ii]["candidate_value"] = array();
            $attr_type[$ii]["candidate_label"] = array();
            for($jj = 0; $jj < count($candidate); $jj++) {
                if($attr_type[$ii]["attribute_id"] == $candidate[$jj]["attribute_id"]) {
                    $attr_type[$ii]["candidate_value"][] = $candidate[$jj]["candidate_value"];
                    $attr_type[$ii]["candidate_label"][] = $candidate[$jj]["candidate_label"];
                }
            }
        }

        return $attr_type;
    }
} 
?>
