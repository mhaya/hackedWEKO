<?php

/**
 * Item register: View for setting link
 * アイテム登録：リンク設定画面表示
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Editlinks.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
require_once WEBAPP_DIR. '/modules/repository/components/common/WekoAction.class.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Search request parameter class
 * 検索リクエストパラメータ処理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositorySearchRequestParameter.class.php';

/**
 * Item register: View for setting link
 * アイテム登録：リンク設定画面表示
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Main_Item_Editlinks extends WekoAction
{
    // 表示用パラメーター
    /**
     * Relation candidate
     * relation選択肢
     *
     * @var array
     */
    public $relationArray = array();
    
    /**
     * Relation count
     * relation個数
     *
     * @var int
     */
    public $relationArray_count = 0;
    
    /**
     * Display help icon flag
     * ヘルプアイコン表示フラグ
     *
     * @var string
     */
    public $help_icon_display =  "";
    
    // リクエストパラメーター
    /**
     * Warning message
     * 警告メッセージ配列
     *
     * @var array
     */
    public $warningMsg = null;
    
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws AppException
     */
    protected function executeApp()
    {
        // RepositoryActionのインスタンス
        $repositoryAction = new RepositoryAction();
        $repositoryAction->Session = $this->Session;
        $repositoryAction->Db = $this->Db;
        $repositoryAction->dbAccess = $this->Db;
        $repositoryAction->TransStartDate = $this->accessDate;
        $repositoryAction->setLangResource();
        $smartyAssign = $this->Session->getParameter("smartyAssign");
        
        $this->relationArray = array(
            //languageリソースから項目を取得する
            array("isVersionOf", $smartyAssign->getLang("repository_item_relation_is_version_of")),
            array("hasVersion", $smartyAssign->getLang("repository_item_relation_has_version")),
            array("isReplacedBy", $smartyAssign->getLang("repository_item_relation_is_replaced_by")),
            array("replaces", $smartyAssign->getLang("repository_item_relation_replaces")),
            array("isRequiredBy", $smartyAssign->getLang("repository_item_relation_is_required_by")),
            array("requires", $smartyAssign->getLang("repository_item_relation_requires")),
            array("isPartOf", $smartyAssign->getLang("repository_item_relation_is_part_of")),
            array("hasPart", $smartyAssign->getLang("repository_item_relation_has_part")),
            array("isReferencedBy", $smartyAssign->getLang("repository_item_relation_is_referenced_by")),
            array("references", $smartyAssign->getLang("repository_item_relation_references")),
            array("isFormatOf", $smartyAssign->getLang("repository_item_relation_is_format_of")),
            array("hasFormat", $smartyAssign->getLang("repository_item_relation_has_format"))
        );
        $this->Session->setParameter("relationArray", $this->relationArray);
        $this->relationArray_count = count($this->relationArray);
        
        // ツリー開閉情報
        $indice = $this->Session->getParameter("indice");
        $open_ids = $this->Session->getParameter("view_open_node_index_id_item_link");
        if(strlen($open_ids) > 0)
        {
            for($ii=0; $ii<count($indice); $ii++){
                $parent_index = array();
                $repositoryAction->getParentIndex($indice[$ii]["index_id"], $parent_index);
                for($jj=0; $jj<count($parent_index); $jj++){
                    if(!is_numeric(strpos($open_ids, ",".$parent_index[$jj]["index_id"].","))){
                        if(substr_count($open_ids, ",".$parent_index[$jj]["index_id"].",")==0 &&
                          $parent_index[$jj]["index_id"] != $indice[$ii]["index_id"]){
                            $open_ids .= ",".$parent_index[$jj]["index_id"].",";
                        }
                    }
                }
            }
            $this->Session->setParameter("view_open_node_index_id_item_link", $open_ids);
        }
        
        if($this->Session->getParameter("link_search_error") != null){
            $this->addErrMsg(sprintf($smartyAssign->getLang("repository_item_link_search_error"), $this->Session->getParameter("link_search_error")));
        }
        if($this->Session->getParameter("link_search_no_item") != null){
            $this->addErrMsg(sprintf($smartyAssign->getLang("repository_item_link_search_no_item"), $this->Session->getParameter("link_search_no_item")));
        }
        $this->Session->removeParameter("link_search_error");
        $this->Session->removeParameter("link_search_no_item");
        $this->Session->removeParameter("edit_jalc_flag");
        
        // Add Default Search Type 2014/12/03 K.Sugimoto --start--
        if(strlen($this->Session->getParameter("link_searchtype")) == 0){
            $searchParam = new RepositorySearchRequestParameter();
            $result = $searchParam->getDefaultSearchType();
            if(isset($result[0]['param_value']))
            {
                if($result[0]['param_value'] == 0)
                {
                    $this->Session->setParameter("link_searchtype", "detail");
                }
                else if($result[0]['param_value'] == 1)
                {
                    $this->Session->setParameter("link_searchtype", "simple");
                }
            }
        }
        // Add Default Search Type 2014/12/03 K.Sugimoto --end--
        
        // Set help icon setting
        $tmpErrorMsg = "";
        $result = $repositoryAction->getAdminParam('help_icon_display', $this->help_icon_display, $tmpErrorMsg);
        if ( $result === false ){
            $this->errorLog($tmpErrorMsg, __FILE__, __CLASS__, __LINE__);
            $exception = new AppException($tmpErrorMsg);
            $exception->addError($tmpErrorMsg);
            throw $exception;
        }
        
        return 'success';
    }
}
?>
