<?php

/**
 * Action class for author information automatic input
 * 著者情報自動入力用アクションクラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Fillauthor.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * JSON library class
 * JSONライブラリクラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/JSON.php';
/**
 * Name authority class
 * 氏名メタデータ管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/NameAuthority.class.php';

/**
 * Action class for author information automatic input
 * 著者情報自動入力用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Main_Item_Fillauthor extends RepositoryAction
{
    // conponents
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    public $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
    public $Db = null;
    
    // member
    /**
     * Sur name
     * 苗字
     *
     * @var string
     */
    public $surName = null;
    /**
     * Given name
     * 名前
     *
     * @var string
     */
    public $givenName = null;
    /**
     * Sur name ruby
     * 苗字（ルビ）
     *
     * @var string
     */
    public $surNameRuby = null;
    /**
     * Given name ruby
     * 名前（ルビ）
     *
     * @var string
     */
    public $givenNameRuby = null;
    /**
     *　E-mail address
     * メールアドレス
     *
     * @var string
     */
    public $emailAddress = null;
    /**
     * Attribute ID
     * メタデータ属性ID
     *
     * @var int
     */
    public $attrId = null;
    /**
     * Attribute number
     * メタデータ属性通番
     *
     * @var int
     */
    public $attrNo = null;
    /**
     * Fill string
     * Fillした文字列
     *
     * @var string
     */
    public $fillStr = null;
    /**
     * Execute mode
     * 実行モード
     *
     * @var string
     */
    public $mode = null;
    /**
     * Author prefix ID
     * 著者プレフィックスID
     *
     * @var int
     */
    public $prefixId = null;
    /**
     * Author suffix ID
     * 著者サフィックスID
     *
     * @var int
     */
    public $suffixId = null;
    // Add e-person 2013/11/19 R.Matsuura --start--
    /**
     * External Author ID
     * 外部著者ID
     *
     * @var string
     */
    public $externalAuthorID = null;
    // Add e-person 2013/11/19 R.Matsuura --end--
    
    // Form
    /**
     * base attr parameter
     * 基本情報パラメータ配列
     *
     * @var array
     */
    public $base_attr = null;                           // base info
    /**
     * item publish year parameter array
     * アイテム公開日の年情報パラメータ配列
     *
     * @var array
     */
    public $item_pub_date_year = null;                  // pub_date : year
    /**
     * item publish month parameter array
     * アイテム公開日の月情報パラメータ配列
     *
     * @var array
     */
    public $item_pub_date_month = null;                 // pub_date : month
    /**
     * item publish day parameter array
     * アイテム公開日の日情報パラメータ配列
     *
     * @var array
     */
    public $item_pub_date_day = null;                   // pub_date : day
    /**
     * item keyword parameter array
     * アイテムキーワードパラメータ配列
     *
     * @var array
     */
    public $item_keyword = null;                        // keyword
    /**
     * item keyword(english) parameter array
     * アイテムキーワード(英)パラメータ配列
     *
     * @var array
     */
    public $item_keyword_english = null;                // keyword_english
    /**
     * text parameter array
     * text属性パラメータ配列
     *
     * @var array
     */
    public $item_attr_text = null;                      // text
    /**
     * textarea parameter array
     * textarea属性パラメータ配列
     *
     * @var array
     */
    public $item_attr_textarea = null;                  // textarea
    /**
     * checkbox parameter array
     * checkbox属性パラメータ配列
     *
     * @var array
     */
    public $item_attr_checkbox = null;                  // checkbox
    /**
     * name: family parameter array
     * name属性の姓情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_name_family = null;               // name : surname
    /**
     * name: name parameter array
     * name属性の名前情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_name_given = null;                // name : given name
    /**
     * name: family ruby parameter array
     * name属性の姓(ルビ)情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_name_family_ruby = null;          // name : surname ruby
    /**
     * name: name ruby parameter array
     * name属性の名(ルビ)情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_name_given_ruby = null;           // name : given name ruby
    /**
     * name: e-mail parameter array
     * name属性のメールアドレス情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_name_email = null;                // name : e-mail
    /**
     * name: author ID prefix parameter array
     * name属性の著者PrefixID情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_name_author_id_prefix = null;     // name : authorID prefix
    /**
     * name: author ID suffix parameter array
     * name属性の著者SuffixID情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_name_author_id_suffix = null;     // name : authorID suffix
    /**
     * select parameter array
     * select属性パラメータ配列
     *
     * @var array
     */
    public $item_attr_select = null;                    // select
    /**
     * link: URL parameter array
     * link属性のURL情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_link = null;                      // link : value
    /**
     * link: display name parameter array
     * text属性の表示名情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_link_name = null;                 // link : name
    /**
     * radio parameter array
     * radio属性パラメータ配列
     *
     * @var array
     */
    public $item_attr_radio = null;                     // radio
    /**
     * biblio: name year parameter array
     * biblio属性の雑誌名情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_biblio_name = null;               // biblio_info : title
    /**
     * biblio: name(english) parameter array
     * biblio属性の雑誌名(英)情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_biblio_name_english = null;       // biblio_info : title_english
    /**
     * biblio: volume parameter array
     * biblio属性の巻情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_biblio_volume = null;             // biblio_info : volume
    /**
     * biblio: issue parameter array
     * biblio属性の号情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_biblio_issue = null;              // biblio_info : issue
    /**
     * biblio: spage parameter array
     * biblio属性の開始ページ情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_biblio_spage = null;              // biblio_info : start_page
    /**
     * biblio: epage parameter array
     * biblio属性の終了ページ情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_biblio_epage = null;              // biblio_info : end_page
    /**
     * biblio: date of issued year parameter array
     * biblio属性の発行年情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_biblio_dateofissued_year = null;  // biblio_info : year
    /**
     * biblio: date of issued month parameter array
     * biblio属性の発行月情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_biblio_dateofissued_month = null; // biblio_info : month
    /**
     * biblio: date of issued day parameter array
     * biblio属性の発効日情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_biblio_dateofissued_day = null;   // biblio_info : day
    /**
     * date: year parameter array
     * date属性の年情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_date_year = null;                 // date : year
    /**
     * date: month parameter array
     * date属性の月情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_date_month = null;                // date : month
    /**
     * date: day parameter array
     * date属性の日情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_date_day = null;                  // date : day
    /**
     * heading: headline parameter array
     * heading属性の大見出し情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_heading = null;                   // heading
    /**
     * heading: headline(english) parameter array
     * heading属性の大見出し(英)情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_heading_en = null;                // heading(english)
    /**
     * heading: subhead parameter array
     * heading属性の小見出し情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_heading_sub = null;               // subheading
    /**
     * heading: subhead(english) parameter array
     * heading属性の小見出し(英)情報パラメータ配列
     *
     * @var array
     */
    public $item_attr_heading_sub_en = null;            // subheading(english)

    // Add Contributor(Posted agency) A.Suzuki 2011/12/13 --start--
    /**
     * Form data : contributor radio button select
     * 画面で選択したアイテム所有者ラジオボタンの選択値
     *
     * @var int
     */
    public $item_contributor = null;
    /**
     * Form data : contributor(handle)
     * アイテム所有者ハンドル名
     *
     * @var string
     */
    public $item_contributor_handle = null;
    /**
     * Form data : contributor(name)
     * アイテム所有者ユーザー名
     *
     * @var string
     */
    public $item_contributor_name= null;
    /**
     * Form data : contributor(email)
     * アイテム所有者メールアドレス
     *
     * @var string
     */
    public $item_contributor_email = null;
    // Add Contributor(Posted agency) A.Suzuki 2011/12/13 --start--

    /**
     * Researcher Name Resolver ID Contact URL
     * 研究者リゾルバーID問い合わせ先URL
     *
     * @var string
     */
    private $resolverUrl = "http://rns.nii.ac.jp/";     // http://rns.nii.ac.jp/opensearch?q5=xxxx : 科研費研究者番号で検索
                                                               // http://rns.nii.ac.jp/opensearch?q6=xxxx : 研究者リゾルバーIDで検索
    /**
     * CiNiiID Contact URL Researcher Name Resolver ID Contact URL
     * CiNiiID問い合わせ先URL
     *
     * @var string
     */
    private $ciniiUrl = "http://ci.nii.ac.jp/";         // http://ci.nii.ac.jp/nrid/xxxxxxxx.rdf   : CiNiiIDで検索

    /**
     * (Deprecated) Fill sur name
     * (廃止予定) Fillしてきた苗字
     *
     * @var string
     */
    private $fillSurName = "";
    /**
     * (Deprecated) Fill sur name ruby
     * (廃止予定) Fillしてきた苗字(ルビ)
     *
     * @var string
     */
    private $fillSurNameRuby = "";
    /**
     * (Deprecated) Fill sur name english
     * (廃止予定) Fillしてきた苗字英名
     *
     * @var string
     */
    private $fillSurNameEn = "";
    /**
     * (Deprecated) Fill given name
     * (廃止予定) Fillしてきた名前
     *
     * @var string
     */
    private $fillGivenName = "";
    /**
     * (Deprecated) Fill given name ruby
     * (廃止予定) Fillしてきた名前(ルビ)
     *
     * @var string
     */
    private $fillGivenNameRuby = "";
    /**
     * (Deprecated) Fill given name english
     * (廃止予定) Fillしてきた名前英名
     *
     * @var string
     */
    private $fillGivenNameEn = "";
    /**
     * (Deprecated) Fill organization
     * (廃止予定) Fillしてきた所属機関名
     *
     * @var string
     */
    private $fillOrganization = "";
    /**
     * (Deprecated) Fill organization english
     * (廃止予定) Fillしてきた所属機関英名
     *
     * @var string
     */
    private $fillOrganizationEn = "";
    
    // add error message 2011/02/21 H.Goto --start--
    /**
     * Error message
     * エラーメッセージ
     *
     * @var string
     */
    var $error_msg = null;          // error message
    /**
     * Language Resource Management object
     * 言語リソース管理オブジェクト
     *
     * @var Smarty
     */
    var $smartyAssign = null;       // for get language resource
    // add error message 2011/02/21 H.Goto --end--
    
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
            ////////////////////////////////
            // init action
            ////////////////////////////////
            $result = $this->initAction();
            if ( $result === false ) {
                $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );
                $DetailMsg = null;
                sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
                $exception->setDetailMsg( $DetailMsg );
                $this->failTrans();
                $user_error_msg = '';
                throw $exception;
            }
            
            $NameAuthority = new NameAuthority($this->Session, $this->Db);
            
            if($this->mode == "suggest"){
                if(strlen($this->fillStr) > 0){
                    // Fill suggest data
                    $this->saveFormData();
                    $this->fillSuggestData($this->fillStr);
                    return 'success';
                }
            } else if($this->mode == "fill"){
                $this->saveFormData();
                if(strlen($this->prefixId) > 0 && strlen($this->suffixId)){
                    // Check name authority
                    
                    // Add Check name authority 2011/01/17 H.Goto --start--
                    // check Language setting
                    $item_attr_type = $this->Session->getParameter("item_attr_type");
                    $display_lang_type = $item_attr_type[$this->attrId]["display_lang_type"];
                    
                    //check wekoDB same prefixId and suffixId
                    $author_id_suffix =  $NameAuthority->getAuthorByPrefixAndSuffix($this->prefixId, $this->suffixId);
                    
                    //When there is no pertinent person,check other site
                    if($author_id_suffix == false || count($author_id_suffix)==0){
                        // Search other site
                        if($this->prefixId == "1" || $this->prefixId == "2" || $this->prefixId == "3"){
                            $name = $this->getAuthorFillData($this->prefixId, $this->suffixId,$display_lang_type);
                            if($name != false){
                                // Get item_attr by Session
                                $item_attr = $this->Session->getParameter("item_attr");
                                $item_attr[$this->attrId][$this->attrNo]["family"] = $name["familyname"];
                                $item_attr[$this->attrId][$this->attrNo]["given"] = $name["firstname"];
                                $item_attr[$this->attrId][$this->attrNo]["family_ruby"] = $name["familyname_ruby"];
                                $item_attr[$this->attrId][$this->attrNo]["given_ruby"] = $name["firstname_ruby"];
                                $item_attr[$this->attrId][$this->attrNo]["author_id"] = 0;
                                $item_attr[$this->attrId][$this->attrNo]["email"] = "";
                                // Set fill data to session
                                $this->Session->setParameter("item_attr", $item_attr);
                            }else{
                                $item_attr = $this->Session->getParameter("item_attr");
                                $item_attr[$this->attrId][$this->attrNo]["family"] = "";
                                $item_attr[$this->attrId][$this->attrNo]["given"] = "";
                                $item_attr[$this->attrId][$this->attrNo]["family_ruby"] = "";
                                $item_attr[$this->attrId][$this->attrNo]["given_ruby"] = "";
                                $item_attr[$this->attrId][$this->attrNo]["author_id"] = 0;
                                $item_attr[$this->attrId][$this->attrNo]["email"] = "";
                                $item_attr[$this->attrId][$this->attrNo]["external_author_id"] = array(array('prefix_id'=>'', 'suffix'=>''));
                                // Set fill data to session
                                $this->Session->setParameter("item_attr", $item_attr);
                            }
                        } else {
                            $item_attr = $this->Session->getParameter("item_attr");
                            $item_attr[$this->attrId][$this->attrNo]["family"] = "";
                            $item_attr[$this->attrId][$this->attrNo]["given"] = "";
                            $item_attr[$this->attrId][$this->attrNo]["family_ruby"] = "";
                            $item_attr[$this->attrId][$this->attrNo]["given_ruby"] = "";
                            $item_attr[$this->attrId][$this->attrNo]["author_id"] = 0;
                            $item_attr[$this->attrId][$this->attrNo]["email"] = "";
                            $item_attr[$this->attrId][$this->attrNo]["external_author_id"] = array(array('prefix_id'=>'', 'suffix'=>''));
                            // Set fill data to session
                            $this->Session->setParameter("item_attr", $item_attr);
                        }
                    }else{
                        // get name
                        $this->fillAuthorData($author_id_suffix,$display_lang_type);
                    }
                // Add Check name authority 2011/01/17 H.Goto --end--
                }
                return 'success';
            }
            $this->surName = urldecode($this->surName);
            $this->surName = trim(mb_convert_encoding($this->surName, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS"));
            $this->givenName = urldecode($this->givenName);
            $this->givenName = trim(mb_convert_encoding($this->givenName, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS"));
            $this->surNameRuby = urldecode($this->surNameRuby);
            $this->surNameRuby = trim(mb_convert_encoding($this->surNameRuby, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS"));
            $this->givenNameRuby = urldecode($this->givenNameRuby);
            $this->givenNameRuby = trim(mb_convert_encoding($this->givenNameRuby, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS"));
            $this->emailAddress = urldecode($this->emailAddress);
            $this->emailAddress = trim(mb_convert_encoding($this->emailAddress, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS"));
            // Add e-person 2013/11/19 R.Matsuura --start--
            $this->externalAuthorID = urldecode($this->externalAuthorID);
            $this->externalAuthorID = trim(mb_convert_encoding($this->externalAuthorID, "UTF-8", "ASCII,JIS,UTF-8,EUC-JP,SJIS"));
            // Add e-person 2013/11/19 R.Matsuura --end--
            $item_attr_type = $this->Session->getParameter("item_attr_type");
            $display_lang_type = "";
            if(count($item_attr_type) > 0)
            {
                $display_lang_type = $item_attr_type[$this->attrId]["display_lang_type"];
            }
            $str = "";
            
            $authorId = $NameAuthority->getSuggestAuthorBySuffix($this->externalAuthorID);
            if((strlen($this->surName.$this->givenName.$this->surNameRuby.$this->givenNameRuby.$this->emailAddress) > 0) || (strlen($this->externalAuthorID) > 0 && count($authorId) > 0)){
                // Get suggest data
                $result = $NameAuthority->searchSuggestData(
                                                            $this->surName,
                                                            $this->givenName,
                                                            $this->surNameRuby,
                                                            $this->givenNameRuby,
                                                            $this->emailAddress,
                                                            $this->externalAuthorID,
                                                            $display_lang_type
                                                        );
                if($result===false){
                    $error_msg = $this->Db->ErrorMsg();
                    return false;
                }
                if(count($result)!=0){
                    $str_candidate = '"candidate":[';
                    $str_authorList = '"authorList":[';
                    $prefixsufix = "";
                    for($ii=0;$ii<count($result);$ii++){
                        
                        // Add 2011/04/25 H.Ito --start--
                        $resultID = $NameAuthority->getExternalAuthorIdData($result[$ii]['author_id']);
                        $prefixsufix = $this->fillSuggestPrefixIDtoString($resultID);
                        // Add 2011/04/25 H.Ito --end--
                        
                        // Fix fill data sanitizing 2011/07/05 Y.Nakao --start--
                        $result[$ii]['family'] = $this->escapeJSON($result[$ii]['family']);
                        $result[$ii]['name'] = $this->escapeJSON($result[$ii]['name']);
                        $result[$ii]['family_ruby'] = $this->escapeJSON($result[$ii]['family_ruby']);
                        $result[$ii]['name_ruby'] = $this->escapeJSON($result[$ii]['name_ruby']);
                        $result[$ii]['e_mail_address'] = $this->escapeJSON($result[$ii]['suffix']);
                        $prefixsufix = $this->escapeJSON($prefixsufix);
                        // Fix fill data sanitizing 2011/07/05 Y.Nakao --end--
                        
                        if($ii!=0){
                            $str_candidate .= ',';
                            $str_authorList .= ',';
                        }
                        $str_candidate .= '"'.
                                          $result[$ii]['family'].' '.
                                          $result[$ii]['name'].' '.
                                          $result[$ii]['family_ruby'].' '.
                                          $result[$ii]['name_ruby'].' '.
                                          $result[$ii]['e_mail_address'].'"';
                        $str_authorList .= '{'.
                                           '"surName":"'.$result[$ii]['family'].'", '.
                                           '"givenName":"'.$result[$ii]['name'].'", '.
                                           '"surNameRuby":"'.$result[$ii]['family_ruby'].'", '.
                                           '"givenNameRuby":"'.$result[$ii]['name_ruby'].'", '.
                                           '"emailAddress":"'.$result[$ii]['e_mail_address'].'", '.
                        // Add 2011/04/25 H.Ito --start--
                                           '"prefixsufix":"'.$prefixsufix.'", '.
                        // Add 2011/04/25 H.Ito --end--
                                           //'"fillStr":"'.$result[$ii]['family'].'|'.$result[$ii]['name'].'|'.$result[$ii]['family_ruby'].'|'.$result[$ii]['name_ruby'].'|'.$result[$ii]['e_mail_address'].'"}';
                                           '"fillStr":"{'.
                                                '\"family\":\"'.$result[$ii]['family'].'\",'.
                                                '\"name\":\"'.$result[$ii]['name'].'\",'.
                                                '\"family_ruby\":\"'.$result[$ii]['family_ruby'].'\",'.
                                                '\"name_ruby\":\"'.$result[$ii]['name_ruby'].'\",'.
                                                '\"e_mail_address\":\"'.$result[$ii]['e_mail_address'].'\",'.
                                                '\"author_id\":\"'.$result[$ii]['author_id'].'\",'.
                                                '\"attrId\":\"'.$this->attrId.'\",'.
                                                '\"attrNo\":\"'.$this->attrNo.'\"}"'.
                                           '}';
                    }
                    $str_candidate .= ']';
                    $str_authorList .= ']';
                    
                    $str = '{'.$str_candidate.','.$str_authorList.'}';
                    
                    // exit action
                    $result = $this->exitAction();
                    if ( $result === false ) {
                        $exception = new RepositoryException( "ERR_MSG_xxx-xxx3", 1 );
                        throw $exception;
                    }
                }
            }
            echo $str;
            $this->finalize();
            exit();
        } catch ( RepositoryException $Exception) {
            //エラーログ出力
            $this->logFile(
                "SampleAction",                 //クラス名
                "execute",                      //メソッド名
            $Exception->getCode(),          //ログID
            $Exception->getMessage(),       //主メッセージ
            $Exception->getDetailMsg() );   //詳細メッセージ         
            //アクション終了処理
            $this->exitAction();                   //トランザクションが失敗していればROLLBACKされる        
            //異常終了
            $this->Session->setParameter("error_msg", $user_error_msg);
            return "error";
        }
    }
    
    // Add 2011/04/25 H.Ito --start--
    /**
     * Fill suggest Prefix Suffix get to string provide colon
     * FillしてきたIDを文字列に変換する(コロン繋ぎ)
     *
     * @param string $resultID get result ID 取得してきたID
     * @return string result ID to string 文字列に変換したID文字列
     */
    function fillSuggestPrefixIDtoString($resultID){
        $outStr = "";
        if($resultID===false){
            return "";
        }
        if(count($resultID)!=0){
            for($ii=0;$ii<count($resultID);$ii++){
                if(strlen($outStr) > 0){
                    $outStr .= ',';
                }
                $outStr .= $resultID[$ii]['prefix_name'].':'.$resultID[$ii]['suffix'];
            }
            return $outStr;
        }
        return "";
    }
    // Add 2011/04/25 H.Ito --end--
    
    
    /**
     * Fill suggest data to session
     * Fillしてきたデータをセッションにセットする
     *
     * @param string $fillData filled data フィルされてきたデータ
     */
    function fillSuggestData($fillData){
        // Decode fill data
        $json = new Services_JSON();
        $decoded = $json->decode($fillData);
        
        $NameAuthority = new NameAuthority($this->Session, $this->Db);
        $external_author_id = $NameAuthority->getExternalAuthorIdPrefixAndSuffix($decoded->author_id);
        
        // Get item_attr by Session
        $item_attr = $this->Session->getParameter("item_attr");
        
        // Fill data
        $item_attr[$decoded->attrId][$decoded->attrNo]["family"] = htmlspecialchars_decode($decoded->family, ENT_QUOTES);
        $item_attr[$decoded->attrId][$decoded->attrNo]["given"] = htmlspecialchars_decode($decoded->name, ENT_QUOTES);
        $item_attr[$decoded->attrId][$decoded->attrNo]["family_ruby"] = htmlspecialchars_decode($decoded->family_ruby, ENT_QUOTES);
        $item_attr[$decoded->attrId][$decoded->attrNo]["given_ruby"] = htmlspecialchars_decode($decoded->name_ruby, ENT_QUOTES);
        $item_attr[$decoded->attrId][$decoded->attrNo]["email"] = htmlspecialchars_decode($decoded->e_mail_address, ENT_QUOTES);
        $item_attr[$decoded->attrId][$decoded->attrNo]["author_id"] = $decoded->author_id;
        $item_attr[$decoded->attrId][$decoded->attrNo]["external_author_id"] = $external_author_id;
        
        // Set fill data to session
        $this->Session->setParameter("item_attr", $item_attr);
    }
    
    /**
     * Save form data to session
     * フォーム入力情報をセッションにセットする
     */
    function saveFormData(){
        // Get session data
        $item_type_all = $this->Session->getParameter("item_type_all");
        $item_attr_type = $this->Session->getParameter("item_attr_type");
        $item_num_cand = $this->Session->getParameter("item_num_cand");
        $option_data = $this->Session->getParameter("option_data");
        $item_num_attr = $this->Session->getParameter("item_num_attr");
        $item_attr_old = $this->Session->getParameter("item_attr");
        $item_attr = array();
        
        // counter
        $cnt_text = 0;      // text
        $cnt_textarea = 0;  // textarea
        $cnt_name = 0;      // name
        $cnt_author_id = 0; // name author_id
        $cnt_link = 0;      // link
        $cnt_select = 0;    // select
        $cnt_checkbox = 0;  // checkbox
        $cnt_radio = 0;     // radio
        $cnt_biblio = 0;    // biblio_info
        $cnt_date = 0;      // date
        
        // ------------------------------------------------------------
        // Save to session
        // ------------------------------------------------------------     
        
        // base_attr
        $this->Session->setParameter("base_attr", array( 
            "title" => ($this->base_attr[0]==' ') ? '' : $this->base_attr[0],
            "title_english" => ($this->base_attr[1]==' ') ? '' : $this->base_attr[1],
            "language" => $this->base_attr[2])
        );      
        // item_pub_date
        $this->Session->setParameter("item_pub_date", array(
                "year" => ($this->item_pub_date_year == ' ') ? '' : $this->item_pub_date_year,
                "month" => $this->item_pub_date_month,
                "day" => $this->item_pub_date_day
            )
        );
        
        // keyword
        $keywords = split("[|]", $this->item_keyword);
        $keywords_en = split("[|]", $this->item_keyword_english);
        $item_keyword_new = '';
        $item_keyword_en_new = '';
        for($ii=0; $ii<count($keywords); $ii++) {
            $keywords[$ii] = trim($keywords[$ii]);
            $item_keyword_new = $item_keyword_new . $keywords[$ii];
            if($ii != count($keywords)-1) {
                $item_keyword_new = $item_keyword_new . '|';
            }               
        }
        for($ii=0; $ii<count($keywords_en); $ii++) {
            $keywords_en[$ii] = trim($keywords_en[$ii]);
            $item_keyword_en_new = $item_keyword_en_new . $keywords_en[$ii];
            if($ii != count($keywords_en)-1) {
                $item_keyword_en_new = $item_keyword_en_new . '|';
            }               
        }
        $item_keyword = $item_keyword_new;
        $item_keyword_english = $item_keyword_en_new;
        $this->Session->setParameter("item_keyword", $item_keyword);
        $this->Session->setParameter("item_keyword_english", $item_keyword_english);
        
        // Add Contributor(Posted agency) A.Suzuki 2011/12/13 --start--
        // ------------------------------------------------------------------
        // Contributor
        // ------------------------------------------------------------------
        $item_contributor = null;
        if($this->item_contributor_handle == " ")
        {
            $this->item_contributor_handle = "";
        }
        if($this->item_contributor_name == " ")
        {
            $this->item_contributor_name = "";
        }
        if($this->item_contributor_email == " ")
        {
            $this->item_contributor_email = "";
        }
        
        if(strlen($this->item_contributor) > 0 && $this->item_contributor == "1")
        {
            $item_contributor = array(
                RepositoryConst::ITEM_CONTRIBUTOR_HANDLE => $this->item_contributor_handle,
                RepositoryConst::ITEM_CONTRIBUTOR_NAME => $this->item_contributor_name,
                RepositoryConst::ITEM_CONTRIBUTOR_EMAIL => $this->item_contributor_email);
        }
        $this->Session->setParameter(RepositoryConst::SESSION_PARAM_ITEM_CONTRIBUTOR, $item_contributor);
        // Add Contributor(Posted agency) A.Suzuki 2011/12/13 --end--
        
        // item_attr
        for($ii=0; $ii<count($item_attr_type); $ii++) {
            $attr_elm = array();
            $nCnt_attr = 0;
            $nCnt_attr_flg = 0;
            for($jj=0; $jj<$item_num_attr[$ii]; $jj++) {
                $metadata = array();
                $metadata["attribute_id"] = $item_attr_type[$ii]["attribute_id"];
                $metadata["item_type_id"] = $item_type_all["item_type_id"];
                $metadata["input_type"] = $item_attr_type[$ii]['input_type'];
                switch($item_attr_type[$ii]['input_type']) {
                case 'text':
                    $metadata["attribute_no"] = $jj+1;
                    if($this->item_attr_text[$cnt_text]==' ') {
                        array_push($attr_elm, '');
                        $metadata["attribute_value"] = '';
                    } else {
                        array_push($attr_elm, $this->item_attr_text[$cnt_text]);
                        $metadata["attribute_value"] = $this->item_attr_text[$cnt_text];
                    }
                    $cnt_text++;
                    break;
                case 'link':
                    $metadata["attribute_no"] = $jj+1;
                    // URL
                    if($this->item_attr_link[$cnt_link]==' ') {
                        $link_url = "";
                    } else {
                        $link_url = str_replace("|", "", $this->item_attr_link[$cnt_link]);
                    }
                    // link_name
                    if($this->item_attr_link_name[$cnt_link]==' ') {
                        $link_name = "";
                    } else {
                        $link_name = str_replace("|", "", $this->item_attr_link_name[$cnt_link]);
                    }
                    if($link_name != ""){
                        array_push($attr_elm, $link_url."|".$link_name);
                        $metadata["attribute_value"] = $link_url."|".$link_name;
                    } else {
                        array_push($attr_elm, $link_url);
                        $metadata["attribute_value"] = $link_url;
                    }
                    $cnt_link++;
                    break;
                case 'name':
                    $metadata["personal_name_no"] = $jj+1;
                    $family = '';
                    $given = '';
                    $family_ruby = '';
                    $given_ruby = '';
                    $email = '';
                    $author_id = '';
                    $language = $item_attr_type[$ii]['display_lang_type'];
                    $external_author_id = array();
                    
                    if($this->item_attr_name_family[$cnt_name]!=' ') {
                        $family = $this->item_attr_name_family[$cnt_name];
                    }
                    if($this->item_attr_name_given[$cnt_name]!=' ') {
                        $given = $this->item_attr_name_given[$cnt_name];
                    }
                    if($language == "japanese"){
                        if($this->item_attr_name_family_ruby[$cnt_name]!=' ') {
                            $family_ruby = $this->item_attr_name_family_ruby[$cnt_name];
                        }
                        if($this->item_attr_name_given_ruby[$cnt_name]!=' ') {
                            $given_ruby = $this->item_attr_name_given_ruby[$cnt_name];
                        }
                    }
                    if($this->item_attr_name_email[$cnt_name]!=' ') {
                        $email = $this->item_attr_name_email[$cnt_name];
                    }
                    
                    for($kk=0; $kk<count($item_attr_old[$ii][$jj]["external_author_id"]); $kk++){
                        $external_author_id_prefix = '';
                        $external_author_id_suffix = '';
                        if($this->item_attr_name_author_id_prefix[$kk+$cnt_author_id]!=0) {
                            $external_author_id_prefix = $this->item_attr_name_author_id_prefix[$kk+$cnt_author_id];
                        }
                        if($this->item_attr_name_author_id_suffix[$kk+$cnt_author_id]!=' ') {
                            $external_author_id_suffix = $this->item_attr_name_author_id_suffix[$kk+$cnt_author_id];
                        }
                        array_push($external_author_id, array('prefix_id'=>$external_author_id_prefix, 'suffix'=>$external_author_id_suffix));
                    }
                    $cnt_author_id = $cnt_author_id + $kk;
                    $author_id = intval($item_attr_old[$ii][$jj]["author_id"]);
                    array_push($attr_elm, array(
                            'family' => $family,
                            'given' => $given,
                            'family_ruby' => $family_ruby,
                            'given_ruby' => $given_ruby,
                            'email' => $email,
                            'author_id' => $author_id,
                            'language' => $language,
                            'external_author_id' => $external_author_id
                        )
                    );
                    $metadata["family"] = $family;
                    $metadata["name"] = $given;
                    $metadata["family_ruby"] = $family_ruby;
                    $metadata["name_ruby"] = $given_ruby;
                    $metadata["e_mail_address"] = $email;
                    $metadata["author_id"] = $author_id;
                    $metadata["language"] = $language;
                    $metadata["external_author_id"] = $external_author_id;
                    $cnt_name++;
                    break;
                case 'textarea':
                    $metadata["attribute_no"] = $jj+1;
                    if($this->item_attr_textarea[$cnt_textarea]==' ') {
                        array_push($attr_elm, '');
                        $metadata["attribute_value"] = '';
                    } else {
                        array_push($attr_elm, $this->item_attr_textarea[$cnt_textarea]);
                        $metadata["attribute_value"] = $this->item_attr_textarea[$cnt_textarea];
                    }
                    $cnt_textarea++;
                    break;
                case 'select':
                    $metadata["attribute_no"] = $jj+1;
                    if($this->item_attr_select[$cnt_select]=='') {
                        array_push($attr_elm, '');
                        $metadata["attribute_value"] = '';
                    } else {
                        array_push($attr_elm, $this->item_attr_select[$cnt_select]);
                        $metadata["attribute_value"] = $this->item_attr_select[$cnt_select];
                    }
                    $cnt_select++;
                    break;
                case 'checkbox':
                    $metadata["attribute_no"] = array();
                    $metadata["attribute_value"] = array();
                    for($kk=0; $kk<count($option_data[$ii]); $kk++){
                        array_push($attr_elm, $this->item_attr_checkbox[$cnt_checkbox]);    // チェックON
                        if($this->item_attr_checkbox[$cnt_checkbox] == 1){
                            $metadata["attribute_no"] = $jj + $kk + 1;
                            $metadata["attribute_value"] = $option_data[$ii][$kk];
                        }
                        $cnt_checkbox++;
                    }
                    break;
                case 'radio':
                    $metadata["attribute_no"] = $jj+1;
                    array_push($attr_elm, $this->item_attr_radio[$cnt_radio]);
                    $metadata["attribute_value"] = $option_data[$ii][$this->item_attr_radio[$cnt_radio]];
                    $cnt_radio++;
                    break;
                case 'biblio_info':
                    $biblio_name = '';
                    $biblio_name_english = '';
                    $volume = '';
                    $issue = '';
                    $spage = '';
                    $epage = '';
                    $year = '';
                    $month = '';
                    $day = '';
                    $dateofissued = '';
                    if($this->item_attr_biblio_name[$cnt_biblio]!=' ') {
                        $biblio_name = $this->item_attr_biblio_name[$cnt_biblio];
                    }
                    if($this->item_attr_biblio_name_english[$cnt_biblio]!=' ') {
                        $biblio_name_english = $this->item_attr_biblio_name_english[$cnt_biblio];
                    }
                    if($this->item_attr_biblio_volume[$cnt_biblio]!=' ') {
                        $volume = $this->item_attr_biblio_volume[$cnt_biblio];
                    }
                    if($this->item_attr_biblio_issue[$cnt_biblio]!=' ') {
                        $issue = $this->item_attr_biblio_issue[$cnt_biblio];
                    }
                    if($this->item_attr_biblio_spage[$cnt_biblio]!=' ') {
                        $spage = $this->item_attr_biblio_spage[$cnt_biblio];
                    }
                    if($this->item_attr_biblio_epage[$cnt_biblio]!=' ') {
                        $epage = $this->item_attr_biblio_epage[$cnt_biblio];
                    }
                    if($this->item_attr_biblio_dateofissued_year[$cnt_biblio]!=' ') {
                        $year = trim($this->item_attr_biblio_dateofissued_year[$cnt_biblio]);
                    }
                    if($this->item_attr_biblio_dateofissued_month[$cnt_biblio]!=' ') {
                        $month = $this->item_attr_biblio_dateofissued_month[$cnt_biblio];
                    }
                    if($this->item_attr_biblio_dateofissued_day[$cnt_biblio]!=' ') {
                        $day = $this->item_attr_biblio_dateofissued_day[$cnt_biblio];
                    }
                    if($year != '') {
                        $dateofissued = $year;
                        if($month != '') {
                            if (strlen($month) == 1) {
                                $dateofissued = $dateofissued.'-0'.$month;
                            } else {
                                $dateofissued = $dateofissued.'-'.$month;
                            }
                            if($day != '') {
                                if (strlen($day) == 1) {
                                    $dateofissued = $dateofissued.'-0'.$day;
                                } else {
                                    $dateofissued = $dateofissued.'-'.$day;
                                }
                            }
                        }
                    }
                    array_push($attr_elm, array(
                            'biblio_name' => $biblio_name,
                            'biblio_name_english' => $biblio_name_english,
                            'volume' => $volume,
                            'issue' => $issue,
                            'spage' => $spage,
                            'epage' => $epage,
                            'date_of_issued' => $dateofissued,
                            'year' => $year,
                            'month' => $month,
                            'day' => $day
                        )
                    );
                    $metadata["biblio_no"] = $jj+1;
                    $metadata["biblio_name"] = $biblio_name;
                    $metadata["biblio_name_english"] = $biblio_name_english;
                    $metadata["volume"] = $volume;
                    $metadata["issue"] = $issue;
                    $metadata["start_page"] = $spage;
                    $metadata["end_page"] = $epage;
                    $metadata["date_of_issued"] = $dateofissued;
                    $cnt_biblio++;
                    break;
                case 'date':
                    $date_year = '';
                    $date_month = '';
                    $date_day = '';
                    $date = '';
                    if($this->item_attr_date_year[$cnt_date]!=' ') {
                        $date_year = trim($this->item_attr_date_year[$cnt_date]);
                    }
                    if($this->item_attr_date_month[$cnt_date]!=' ') {
                        $date_month = $this->item_attr_date_month[$cnt_date];
                    }
                    if($this->item_attr_date_day[$cnt_date]!=' ') {
                        $date_day = $this->item_attr_date_day[$cnt_date];
                    }
                    if($date_year != '') {
                        $date = $date_year;
                        if($date_month != '') {
                            if (strlen($date_month) == 1) {
                                $date = $date.'-0'.$date_month;
                            } else {
                                $date = $date.'-'.$date_month;
                            }
                            if($date_day != '') {
                                if (strlen($date_day) == 1) {
                                    $date = $date.'-0'.$date_day;
                                } else {
                                    $date = $date.'-'.$date_day;
                                }
                            }
                        }
                    }
                    array_push($attr_elm, array(
                            'date' => $date,
                            'date_year' => $date_year,
                            'date_month' => $date_month,
                            'date_day' => $date_day
                        )
                    );
                    $metadata["attribute_no"] = $jj+1;
                    $metadata["attribute_value"] = $date;
                    $cnt_date++;
                    break;
                case 'heading':
                    $metadata["attribute_no"] = $jj+1;
                    $heading = "";
                    $heading_en = "";
                    $heading_sub = "";
                    $heading_sub_en = "";
                    // check string empty
                    if($this->item_attr_heading!=' ') {
                        $heading = $this->item_attr_heading; 
                    }
                    if($this->item_attr_heading_en!=' ') {
                        $heading_en = $this->item_attr_heading_en;
                    }
                    if($this->item_attr_heading_sub!=' ') {
                        $heading_sub = $this->item_attr_heading_sub;
                    }
                    if($this->item_attr_heading_sub_en!=' ') {
                        $heading_sub_en = $this->item_attr_heading_sub_en;
                    }
                    $metadata["attribute_value"] = $heading."|".$heading_en."|".$heading_sub."|".$heading_sub_en; 
                    array_push($attr_elm, $metadata["attribute_value"]);
                    break;
                default :
                    array_push($attr_elm, $item_attr_old[$ii][$jj]);
                    break;
                }
            }
            array_push($item_attr, $attr_elm);      // 1メタデータ分のユーザ入力値をセット
        }
        $this->Session->setParameter("item_attr", $item_attr);
        return true;
    }
    
    /**
     * Get author fill data by external author ID
     * 外部著者IDから著者情報を取得する
     *
     * @param int $prefix prefix ID 著者プレフィックスID
     * @param string $suffix suffix ID 著者サフィックスID
     * @param string $display_lang_type display mapping's language setting 表示言語
     * @return string author name 著者名
     */
    function getAuthorFillData($prefix, $suffix ,$display_lang_type){
        if($prefix == "1"){
            // CiNII ID
            $reqUrl = $this->ciniiUrl."nrid/".$suffix.".rdf";
            //get XML
            $vals = $this->getXml($reqUrl);
            if ($vals == false){
                return false;
            }
            // get ID
            $name = $this->getCiNiiId($vals,$display_lang_type);
            return $name;
        } else if($prefix == "2"){
            // resolverID
            $reqUrl = $this->resolverUrl."opensearch?q6=".$suffix;
            //get XML
            $vals = $this->getXml($reqUrl);
            if ($vals == false){
                return false;
            }
            // get resolver ID
            $name = $this->getResolver($vals,$display_lang_type);
            return $name;
        } else if($prefix == "3"){
            // ResearcherNo
            $reqUrl = $this->resolverUrl."opensearch?q5=".$suffix;
            //get XML
            $vals = $this->getXml($reqUrl);
            if ($vals == false){
                return false;
            }
            // get resolver No
            $name = $this->getResolver($vals,$display_lang_type);
            return $name;
        }
    }

    /**
     * Get Xml
     * XMLを取得する
     *
     * @param string $reqUrl request URL リクエスト先URL
     * @return string response XML レスポンスXML
     */
    function getXml($reqUrl){
        $option = array( 
            "timeout" => "10",
            "allowRedirects" => true,
            "maxRedirects" => 3, 
        );
        // Modfy proxy 2011/12/06 Y.Nakao --start--
        $proxy = $this->getProxySetting();
        if($proxy['proxy_mode'] == 1)
        {
            $option = array( 
                    "timeout" => "10",
                    "allowRedirects" => true, 
                    "maxRedirects" => 3,
                    "proxy_host"=>$proxy['proxy_host'],
                    "proxy_port"=>$proxy['proxy_port'],
                    "proxy_user"=>$proxy['proxy_user'],
                    "proxy_pass"=>$proxy['proxy_pass']
                );
        }
        // Modfy proxy 2011/12/06 Y.Nakao --end--
        $http = new HTTP_Request($reqUrl, $option);
        // setting HTTP header
        $http->addHeader("User-Agent", $_SERVER['HTTP_USER_AGENT']);
        $response = $http->sendRequest(); 
        if (!PEAR::isError($response)) { 
            $resCode = $http->getResponseCode();        // get ResponseCode(200etc.)
            $resHeader = $http->getResponseHeader();    // get ResponseHeader
            $resBody = $http->getResponseBody();        // get ResponseBody
            $resCookies = $http->getResponseCookies();  // get Cookie
        }
        /////////////////////////////
        // parse response XML
        /////////////////////////////
        $response_xml = $resBody; 
        
        // add get lang 2011/02/21 H.Goto --start--
        $this->smartyAssign = $this->Session->getParameter("smartyAssign");
        // add get lang 2011/02/21 H.Goto --end--
        try{
            $xml_parser = xml_parser_create();
            $rtn = xml_parse_into_struct( $xml_parser, $response_xml, $vals );
            if($rtn == 0){
                $this->error_msg = $this->smartyAssign->getLang("repository_item_fill_data_no_data_error");
                return false;
            }
            xml_parser_free($xml_parser);
        } catch(Exception $ex){
            $this->error_msg = $this->smartyAssign->getLang("repository_item_fill_data_no_data_error");
            return false;
        }
        return $vals;
        
    }
    
    /**
     * Get CiNII ID other site
     * 外部サイトから取得したXMLからCiNiiIDを取得する
     *
     * @param string $vals XML data XML情報
     * @param string $display_lang_type  mapping's language setting 表示言語
     * @return string author name 著者名
     */
    function getCiNiiId($vals,$display_lang_type){
        // get item's language type
        $lang_sess = $this->Session->getParameter("base_attr");
        $cnt = count($vals);
        // get name for XML
        foreach($vals as $tmp){
            if($tmp["tag"] == "FOAF:PERSON" && $tmp["type"] == "open"){
                $person_open_flg = "1";
            }else if($tmp["tag"] == "FOAF:PERSON" && $tmp["type"] == "close"){
                $person_open_flg = "0";
            }
            if($tmp["tag"]=="FOAF:NAME" && $person_open_flg == "1"){
                if($tmp["attributes"]["XML:LANG"] == "en"){
                    $name_en = explode(" ",$tmp["value"]);
                }else{
                    $name = explode(" ",$tmp["value"]);
                }
            }
        }
        //language flag
        $lang_flg = 1; // 1 englosh
        if($display_lang_type == "japanese"){
            $lang_flg = 0;
        }elseif($display_lang_type == "english"){
            $name = $name_en;
        }else{
            if($lang_sess["language"] = "ja"){
                $lang_flg = 0;
            }else{
                $name = $name_en;
            }
        }
        if($name == ""){
            $name = $name_en;
        }
        $cnt = count($name);
        $firstname = NULL;
        
        for ($ii = 0;$ii < $cnt; $ii++){
            if($ii === 0){
                $familyname = $name[$ii];
            }else{
                if($firstname == NULL){
                    $firstname = $name[$ii];
                }else{
                    $firstname = $firstname." ".$name[$ii];
                }
            }
        }
        $name["familyname"] = $familyname;
        $name["firstname"] = $firstname;
        return $name;
    }

    /**
     * Get Researcher's ResolverID and Get Department laboratory expense researcher No
     * リゾルバーIDを取得する
     *
     * @param string $vals XML data XML情報
     * @param string $lang  mapping's language setting 表示言語
     * @return string author name 著者名
     */
    function getResolver($vals,$lang){
        // get item's language type
        $lang_sess = $this->Session->getParameter("base_attr");
        $item_open_flg = "0";
        $name = array();
        // get name for XML
        foreach($vals as $tmp){
            if($tmp["tag"] == "ITEM" && $tmp["type"] == "open"){
                $item_open_flg = "1";
            }else if ($tmp["tag"] == "ITEM" && $tmp["type"] == "close"){
                $item_open_flg = "0";
            }
            if($tmp["tag"] == "TITLE" && $item_open_flg == "1"){
                if (preg_match("/ \| (.*)\([0-9]+\)/", $tmp["value"])){
                    // It doesn't exist japanese
                    preg_match("/ \| (.*)\([0-9]+\)/", $tmp["value"], $name);
                    break;
                }else{
                    // exist japanese
                    preg_match("/ \- (.*)\([0-9]+\)/", $tmp["value"], $name);
                    break;
                }
            }
        }
        if(count($name) == 0){
            return false;
        }
        // get name
        if($lang == "japanese"){
            // japanese
            $fillname = $this->resolverJpn($name);
        }else if($lang == "english"){
            // english
            $fillname = $this->resolverEng($name);
        }else{
            if($lang_sess["language"] = "ja"){
                // japanese
                $fillname = $this->resolverJpn($name);
            }else{
                // others
                $fillname = $this->resolverEng($name);
            }
        }
        return $fillname;
    }

    /**
     * (Deprecated) Get name metadata
     * (廃止予定) 氏名メタデータを取得する
     *
     * @param int $author_id_suffix  author id suffix  著者ID
     * @param string $lang display language 表示言語
     */
    function get_name_auth($author_id_suffix,$lang){
        $query = "SELECT * ".
         "FROM ". DATABASE_PREFIX ."repository_name_authority ".
         "WHERE author_id = ? ".
         "AND language = ? ";
        $params = null;
        // $queryの?を置き換える配列
        $params[] = $author_id_suffix;    // author_id
        $params[] = $lang;                   // language_type
        // Execution SELECT
        $name_authority = $this->Db->execute($query, $params);
        
        $firstname = $name_authority[0]["name"];
        $familyname = $name_authority[0]["family"];
        $firstname_ruby = $name_authority[0]["name_ruby"];
        $familyname_ruby = $name_authority[0]["family_ruby"];
    }
    
    /**
     * Set authority data by DB set to session
     * DBから取得した著者情報をセッションにセットする
     *
     * @param array $fillNameDataArray author data array 著者情報配列
     *               array[$ii]["language"|"external_author_id"|"e_mail_address"|"suffix"]
     * @param string $lang display language 表示言語
     */
    function fillAuthorData($fillNameDataArray,$lang){
        
        // Get item_attr by Session
        $item_attr = $this->Session->getParameter("item_attr");
        
        $fillNameData = null;
        $fillNameData_none = null;
        $fillNameData_othor = null;
        
        //search same language mapping setting and DB setting
        $cnt = count($fillNameDataArray);
        for ($ii = 0; $ii < $cnt; $ii++){
            if($fillNameDataArray[$ii]["language"] == $lang){
                $fillNameData = $fillNameDataArray[$ii];
                break;
            } else if($fillNameDataArray[$ii]["language"] == ""){
                if($fillNameData_none == null){
                    $fillNameData_none = $fillNameDataArray[$ii];
                }
            } else {
                if($fillNameData_othor == null){
                    $fillNameData_othor = $fillNameDataArray[$ii];
                }
            }
        }
        
        if($fillNameData != null){
            // It doesn't do at all.
        }else if ($fillNameData_none != null){
            $fillNameData = $fillNameData_none;
        }else{
            $fillNameData = $fillNameData_othor;
        }
        
        $fillExternalAuthorId = $fillNameData["external_author_id"];
        $fillNameData["external_author_id"] = array();
        $fillNameData["e_mail_address"] = "";
        for($idCnt = 0; $idCnt < count($fillExternalAuthorId); $idCnt++)
        {
            if($fillExternalAuthorId[$idCnt]["prefix_id"] == 0)
            {
                $fillNameData["e_mail_address"] = $fillExternalAuthorId[$idCnt]["suffix"];
            }
            else
            {
                array_push($fillNameData["external_author_id"], $fillExternalAuthorId[$idCnt]);
            }
        }
        if(!isset($fillNameData["external_author_id"]) || count($fillNameData["external_author_id"]) < 1)
        {
            $fillNameData["external_author_id"] = array();
            $tmpArray = array("prefix_id" => "", "suffix" => "");
            array_push($fillNameData["external_author_id"], $tmpArray);
        }
        
        $item_attr[$this->attrId][$this->attrNo]["family"] = $fillNameData["family"];
        $item_attr[$this->attrId][$this->attrNo]["given"] = $fillNameData["name"];
        $item_attr[$this->attrId][$this->attrNo]["family_ruby"] = $fillNameData["family_ruby"];
        $item_attr[$this->attrId][$this->attrNo]["given_ruby"] = $fillNameData["name_ruby"];
        $item_attr[$this->attrId][$this->attrNo]["email"] = $fillNameData["e_mail_address"];
        $item_attr[$this->attrId][$this->attrNo]["author_id"] = $fillNameData["author_id"];
        $item_attr[$this->attrId][$this->attrNo]["external_author_id"] = $fillNameData["external_author_id"];
        
        // Set fill data to session
        $this->Session->setParameter("item_attr", $item_attr);
    }

    /**
     * Return to convert the author's name of Japanese acquired from researchers resolver ID in the form of a first and last name and reading
     * 研究者リゾルバIDから取得した日本語の著者名を姓名とヨミの形式に変換し返す
     *
     * @param $name The author first and last names of Japanese and reading 日本語の著者姓名とヨミ
     *              array[$ii][$jj]
     * @return array Author name information in Japanese 日本語の著者名情報
     *               array["familyname"|"firstname"|"familyname_ruby"|"firstname_ruby"]
     */
    function resolverJpn($name){
        $name_knj = explode(" ",$name[1]);
        $name_kana = explode(" ",$name[2]);
        $cnt_knj = count($name_knj);
        $cnt_kana = count($name_kana);

        for ($ii = 0;$ii < $cnt_knj; $ii++){
            if($ii === 0){
                $familyname = $name_knj[$ii];
            }else{
                $firstname = $firstname." ".$name_knj[$ii];
                $firstname = ltrim($firstname);
            }
        }
        // Furigana name
        for ($ii = 0;$ii < $cnt_kana; $ii++){
            if($ii === 0){
                $familyname_ruby = $name_kana[$ii];
                $familyname_ruby = str_replace("(","",$familyname_ruby);
            }else{
                $firstname_ruby = $firstname_ruby." ".$name_kana[$ii];
                $firstname_ruby = str_replace(")","",$firstname_ruby);
                $firstname_ruby = ltrim($firstname_ruby);
            }
        }
        
            $fillname["familyname"] = $familyname;
            $fillname["firstname"] = $firstname;
            $fillname["familyname_ruby"] = $familyname_ruby;
            $fillname["firstname_ruby"] = $firstname_ruby;
        return $fillname;
    }

    /**
     * get resolver english name
     * リゾルバーから著者英名を取得する
     *
     * @param string $name author name 著者名
     * @return string author name english 著者英名
     */
    function resolverEng($name){
        $cnt = count($name);
        $alpha_flg = ctype_alpha(str_replace(" ","",$name[1]));
        $las_alpha_flg = ctype_alpha(str_replace(" ","",$name[$cnt-1]));
        if($alpha_flg == true){
            $name_eng = explode(" ",$name[1]);
        }else if($las_alpha_flg == false){
            $name_eng = explode(" ",$name[1]);
        }else{
            $name_eng = explode(" ",$name[$cnt-1]);
        }
        $cnt_eng = count($name_eng);
        for($ii = 0; $ii < $cnt_eng; $ii++){
            if ($ii === 0){
                $firstname = str_replace("(","",$name_eng[$ii]);
            }else{
                $familyname = $familyname." ".$name_eng[$ii];
                $familyname = str_replace(")","",$familyname);
                $familyname = ltrim($familyname);
                
            }
        }
        $fillname["familyname"] = $familyname;
        $fillname["firstname"] = $firstname;
        return $fillname;
    }
    
    // Fix fill data sanitizing 2011/07/05 Y.Nakao --start--
    /**
     * escape JSON
     * JSON文字列をエスケープする
     *
     * @param string $str JSON string JSON文字列
     * @param bool $lineFlg new line/or not 改行する/しない
     * @return string escaped JSON string エスケープ済JSON文字列
     */
    private function escapeJSON($str, $lineFlg=false){
        
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
    // Fix fill data sanitizing 2011/07/05 Y.Nakao --end--
}
?>
