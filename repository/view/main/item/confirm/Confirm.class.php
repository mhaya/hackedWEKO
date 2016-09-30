<?php

/**
 * Item registration: the view class for the confirmation screen display
 * アイテム登録：確認画面表示用ビュークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Confirm.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Item register class
 * アイテム登録処理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/ItemRegister.class.php';

/**
 * Item registration: the view class for the confirmation screen display
 * アイテム登録：確認画面表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Main_Item_Confirm extends WekoAction
{
    // 表示用パラメーター
    /**
     * Textarea metadata for display array
     * input_type : textarea 表示用配列
     *
     * @var array
     */
    public $textarea_data = array();
    
    /**
     * Link metadata for display array
     * input_type : link 表示用配列
     *
     * @var array
     */
    public $link_data = array();
    
    /**
     * file license  for display array
     * ファイルライセンス情報表示用配列
     *
     * @var array
     */
    public $license_data = array();
    
    /**
     * Heading metadata for display array
     * input_type : heading 表示用配列
     *
     * @var array
     */
    public $heading = array();
    
    /**
     * Help icon display flag
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
        if(!is_array($this->warningMsg)){
            $this->warningMsg = array();
        }
        
        // RepositoryActionのインスタンス
        $repositoryAction = new RepositoryAction();
        $repositoryAction->Session = $this->Session;
        $repositoryAction->Db = $this->Db;
        $repositoryAction->dbAccess = $this->Db;
        $repositoryAction->TransStartDate = $this->accessDate;
        $repositoryAction->setLangResource();
        $smartyAssign = $this->Session->getParameter("smartyAssign");
        
        // セッション情報取得
        $attr_type = $this->Session->getParameter("item_attr_type");
        $item_attr = $this->Session->getParameter("item_attr");
        $license_master = $this->Session->getParameter("license_master");
        
        $this->textarea_data = array();
        for($ii=0; $ii<count($attr_type); $ii++){
            if($attr_type[$ii]['input_type'] == "textarea"){
                $tmp_textarea_data = array();
                for($jj=0; $jj<count($item_attr[$ii]); $jj++){
                    $textarea_array = explode("\n", $item_attr[$ii][$jj]);
                    array_push($tmp_textarea_data, $textarea_array);
                }
                array_push($this->textarea_data, array($ii, $tmp_textarea_data));
            } else if($attr_type[$ii]['input_type'] == "link"){
                for($jj=0; $jj<count($item_attr[$ii]); $jj++){
                    $this->link_data[$ii][$jj] = explode("|", $item_attr[$ii][$jj], 2);
                }
            } else if($attr_type[$ii]['input_type'] == "file" || $attr_type[$ii]['input_type'] == "file_price"){
                for($jj=0; $jj<count($item_attr[$ii]); $jj++){
                    if(isset($item_attr[$ii][$jj]['licence']))
                    {
                        if($item_attr[$ii][$jj]['licence'] !== "licence_free"){
                            foreach($license_master as $kk){
                                if($kk['license_id'] == $item_attr[$ii][$jj]['license_id']){
                                    $this->license_data[$ii][$jj]['img_url'] = $kk['img_url'];
                                    $this->license_data[$ii][$jj]['text_url'] = $kk['text_url'];
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            else if($attr_type[$ii]['input_type'] == 'heading'){
                for($jj=0; $jj<count($item_attr[$ii]); $jj++){
                    $this->heading[$jj] = explode("|", $item_attr[$ii][$jj], 4);
                }
            }
        }
        
        $this->Session->removeParameter('item_entry_flg');
        if(count($this->errMsg) == 0){
            // entry OK
            $this->Session->setParameter('item_entry_flg', 'true');
        } else {
            // entry NG
            $this->Session->setParameter('item_entry_flg', 'false');
        }
        
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
