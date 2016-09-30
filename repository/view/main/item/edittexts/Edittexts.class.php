<?php

/**
 * Item register: View for input metadata
 * アイテム登録：メタデータ入力画面表示
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Edittexts.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Name authority manager class
 * 氏名メタデータ管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/NameAuthority.class.php';

/**
 * Item register: View for input metadata
 * アイテム登録：メタデータ入力画面表示
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_View_Main_Item_Edittexts extends WekoAction
{
    // 表示用パラメーター
    
    /**
     * Item publish date: month option array
     * アイテム公開日：月 選択肢配列
     *
     * @var array
     */
    public $month_option_pub = array();
    
    /**
     * Item publish date: day option array
     * アイテム公開日：日 選択肢配列
     *
     * @var array
     */
    public $day_option_pub = array();
    
    /**
     * Biblio info: date of issued month option array
     * 書誌情報：発行年月日 月 選択肢配列
     *
     * @var array
     */
    public $month_of_issued = array();
    
    /**
     * Biblio info: date of issued day option array
     * 書誌情報：発行年月日 日 選択肢配列
     *
     * @var array
     */
    public $day_of_issued = array();
    
    /**
     * date: month option array
     * 日付：月 選択肢配列
     *
     * @var array
     */
    public $month_of_date = array();
    
    /**
     * date: day option array
     * 日付：日 選択肢配列
     *
     * @var array
     */
    public $day_of_date = array();
    
    /**
     *  biblio info existing flag
     * 書誌情報の存在チェックフラグ
     *
     * @var bool
     */
    public $biblio_info_check = false;
    
    /**
     * AWSAccessKeyId existing flag
     * AWSAccessKeyIdの存在チェックフラグ
     *
     * @var bool
     */
    public $access_key_check = false;
    
    /**
     * Link metadata array
     * link情報配列
     *
     * @var array
     */
    public $link_data = array();
    
    /**
     * Heading metadata array
     * heading情報配列
     *
     * @var array
     */
    public $heading = array();
    
    /**
     * Author ID prefix array
     * author_id_prefix配列
     * @var array
     */
    public $author_id_prefix_list = array();
    // Auto Input Metadata by CrossRef DOI 2015/03/02 K.Sugimoto --start--
    /**
     * CrossRef flag
     * CrossRefフラグ
     *
     * @var bool
     */
    public $crossref_flg = false;
    // Auto Input Metadata by CrossRef DOI 2015/03/02 K.Sugimoto --end--
    
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
        
        // セッションから現在のアイテム公開日を取得
        $item_pub_date = $this->Session->getParameter('item_pub_date');
        
        // 月
        for ( $ii=1; $ii<=12; $ii++ ) {
            $str_month = ($ii);
            $select = ($ii == $item_pub_date['month']) ? 1 : 0;
            array_push($this->month_option_pub, array($str_month,$select));
        }
        // 日
        for ( $ii=1; $ii<=31; $ii++ ) {
            $str_day = ($ii);
            $select = ($ii == $item_pub_date['day']) ? 1 : 0;
            array_push($this->day_option_pub, array($str_day,$select));
        }
        
        // セッションから現在の発行年月日を取得
        $temp_item_attr_type = $this->Session->getParameter('item_attr_type');
        $temp_item_attr = $this->Session->getParameter('item_attr');
        $this->month_of_issued = array();
        $this->day_of_issued = array();
        $this->month_of_date = array();
        $this->day_of_date = array();
        if(count($temp_item_attr_type) > 0)
        {
            $this->month_of_issued = array_fill(0,count($temp_item_attr_type),array());
            $this->day_of_issued = array_fill(0,count($temp_item_attr_type),array());
            
            $this->month_of_date = array_fill(0,count($temp_item_attr_type),array());
            $this->day_of_date = array_fill(0,count($temp_item_attr_type),array());
        }
        
        for ($ii=0; $ii<count($temp_item_attr_type); $ii++) {
            if ($temp_item_attr_type[$ii]['input_type'] == 'biblio_info') {
                // アイテムタイプに書誌情報があるかチェック
                if($this->biblio_info_check == false){
                    $this->biblio_info_check = true;
                }
                // 月
                for ( $jj=1; $jj<=12; $jj++ ) {
                    $str_month = ($jj);
                    $select = 0;
                    if(isset($temp_item_attr[$ii][0]['month']) && $jj == $temp_item_attr[$ii][0]['month'])
                    {
                        $select = 1;
                    }
                    array_push($this->month_of_issued[$ii], array($str_month,$select));
                }
                // 日
                for ( $jj=1; $jj<=31; $jj++ ) {
                    $str_day = ($jj);
                    $select = 0;
                    if(isset($temp_item_attr[$ii][0]['day']) && $jj == $temp_item_attr[$ii][0]['day'])
                    {
                        $select = 1;
                    }
                    array_push($this->day_of_issued[$ii], array($str_day,$select));
                }
            } else if ($temp_item_attr_type[$ii]['input_type'] == 'date') {
                for($ll=0; $ll<count($temp_item_attr[$ii]); $ll++){
                    $monthArray[$ll] = array();
                    $dayArray[$ll] = array();
                    // 月
                    for ( $jj=1; $jj<=12; $jj++ ) {
                        $str_month = ($jj);
                        $select = 0;
                        if(isset($temp_item_attr[$ii][$ll]['date_month']) && $temp_item_attr[$ii][$ll]['date_month'] == $jj) {
                            $select = 1;
                        }
                        array_push($monthArray[$ll], array($str_month,$select));
                    }
                    // 日
                    for ( $jj=1; $jj<=31; $jj++ ) {
                        $str_day = ($jj);
                        $select = 0;
                        if(isset($temp_item_attr[$ii][$ll]['date_day']) && $temp_item_attr[$ii][$ll]['date_day'] == $jj) {
                            $select = 1;
                        }
                        array_push($dayArray[$ll], array($str_day,$select));
                    }
                    array_push($this->month_of_date[$ii], $monthArray[$ll]);
                    array_push($this->day_of_date[$ii], $dayArray[$ll]);
                }
            } else if($temp_item_attr_type[$ii]['input_type'] == 'link'){
                for($jj=0; $jj<count($temp_item_attr[$ii]); $jj++){
                    $this->link_data[$ii][$jj] = explode("|", $temp_item_attr[$ii][$jj], 2);
                }
            } else if($temp_item_attr_type[$ii]['input_type'] == 'heading'){
                for($jj=0; $jj<count($temp_item_attr[$ii]); $jj++){
                    $this->heading[$jj] = explode("|", $temp_item_attr[$ii][$jj], 4);
                }
            }
        }
        
        // AWSAccesskeyIdが登録されているかをチェック
        $query = "SELECT param_value ".
                 "FROM {repository_parameter} ".
                 "WHERE param_name = ? ".
                 "AND is_delete = ?; ";
        $params = array();
        $params[] = 'AWSAccessKeyId';
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            $exception = new AppException($this->Db->ErrorMsg());
            $exception->addError($this->Db->ErrorMsg());
            throw $exception;
        }
        //AWSAccessKeyIdが登録されている場合
        if(trim($result[0]['param_value']) != null) {
            $this->access_key_check = true;
        }
        // Add search bar for fill data 2008/11/20 A.Suzuki --end--
        
        // アイテムリンク設定画面から戻ってきた場合
        // リンクアイテム検索で使用したセッションを削除する
        $this->Session->removeParameter("search_index_id_link");
        $this->Session->removeParameter("link_searchkeyword");
        $this->Session->removeParameter("link_search");
        $this->Session->removeParameter("link_searchtype");
        $this->Session->removeParameter("view_open_node_index_id_item_link");
        
        // Get author ID prefix list 2010/11/04 A.Suzuki --start--
        $this->author_id_prefix_list = $this->loadExternalAuthorIdPrefixList();
        // Get author ID prefix list 2010/11/04 A.Suzuki --end--
        
        // Check ichushi connect staus 2012/12/05 A.Jin --start--
        //医中誌連携チェックがONか調べる
        $query = "SELECT param_value ".
                 "FROM {repository_parameter} ". 
                 "WHERE param_name = ? ".
                 "AND is_delete = ?; ";
        $params = array();
        $params[] = 'ichushi_is_connect';
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            $exception = new AppException($this->Db->ErrorMsg());
            $exception->addError($this->Db->ErrorMsg());
            throw $exception;
        }
        // ichushi_is_connectが登録されている場合
        if($result[0]['param_value'] == 1) {
            $login_id = "";
            $login_password = "";
            $cookie = "";
            //ログイン情報取得
            $is_get_login_info = $repositoryAction->getLoginInfoIchushi($login_id, $login_password);
            //ログインチェック
            if($is_get_login_info == true) {
                $result = $repositoryAction->loginIchushi($login_id,$login_password,$cookie);
                if($result === true){
                    $this->Session->setParameter("login_connect", 1);
                    //ログアウト
                    $repositoryAction->logoutIchushi($cookie);
                } else {
                    $this->Session->setParameter("login_connect", 0);
                }
            } else {
                $this->Session->setParameter("login_connect", 0);
            }
        } else {
            $this->Session->setParameter("login_connect", 0);
        }
        // Check ichushi connect staus 2012/12/05 A.Jin --end--
            
        // Auto Input Metadata by CrossRef DOI 2015/03/02 K.Sugimoto --start--
        $query = "SELECT param_value ".
                 "FROM {repository_parameter} ".
                 "WHERE param_name = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = "crossref_query_service_account";
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            $exception = new AppException($this->Db->ErrorMsg());
            $exception->addError($this->Db->ErrorMsg());
            throw $exception;
        }
        if(count($result) > 0 && strlen($result[0]['param_value']) > 0)
        {
            $this->crossref_flg = true;
        }
        // Auto Input Metadata by CrossRef DOI 2015/03/02 K.Sugimoto --end--
        
        $error_msg = $this->Session->getParameter("error_msg");
        if(count($error_msg) > 0)
        {
            $this->errMsg = $error_msg;
            $this->Session->removeParameter("error_msg");
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
    
    /**
     * Load list of external author id prefix data
     * 外部著者IDのプレフィックス情報一覧を取得する
     * 
     * @return array List of external author id prefix data
     *               外部著者IDのプレフィックス情報一覧
     *               array[$ii]["prefix_id"|"prefix_name"|"url"|...]
     * @throws AppException
     */
    private function loadExternalAuthorIdPrefixList()
    {
        $NameAuthority = new NameAuthority($this->Session, $this->Db);
        $result = $NameAuthority->getExternalAuthorIdPrefixList();
        if($result === false) {
            $tmpErrorMsg = "Cannot get external authorID prefix list.";
            $this->errorLog($tmpErrorMsg, __FILE__, __CLASS__, __LINE__);
            $exception = new AppException($tmpErrorMsg);
            $exception->addError($tmpErrorMsg);
            throw $exception;
        }
        
        for($ii = 0; $ii < count($result); $ii++)
        {
            if(preg_match("/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/", $result[$ii]["url"]) && strpos($result[$ii]["url"], "##"))
            {
                $url_explode = explode("##", $result[$ii]["url"], 2);
                $result[$ii]["url_start"] = $url_explode[0];
                $result[$ii]["url_end"] = $url_explode[1];
            }
            else
            {
                $result[$ii]["url_start"] = "";
                $result[$ii]["url_end"] = "";
            }
        }
        
        return $result;
    }
}
?>
