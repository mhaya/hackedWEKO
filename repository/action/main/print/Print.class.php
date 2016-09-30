<?php

/**
 * Action class for printing
 * 印刷用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Print.class.php 3131 2011-01-28 11:36:33Z haruka_goto $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Common class file download
 * ファイルダウンロード共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';
/**
 * Search common classes
 * 検索共通クラス
 * 
 * @package WEKO
 */
require_once WEBAPP_DIR.'/modules/repository/components/RepositorySearch.class.php';

/**
 * Item authority common classes
 * アイテム権限共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryItemAuthorityManager.class.php';

/**
 * Action class for printing
 * 印刷用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Main_Print extends RepositoryAction
{
    // components
    /**
     * Data upload objects
     * データアップロードオブジェクト
     *
     * @var Uploads_View
     */
    var $uploadView = null;
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    var $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
    var $Db = null;
    
    // request parameter
    /**
     * All Items print flag ( "true": all items)
     * 全アイテム印刷フラグ("true"：全アイテム)
     *
     * @var string
     */
    var $all_print = null;
    
    // member
    /**
     * Minimum icon width
     * 最少アイコン幅
     *
     * @var int
     */
    var $_DOWNLOAD_ICON_WIDTH_MIN = "";
    /**
     * Maximum icon width
     * 最大アイコン幅
     *
     * @var unknown_type
     */
    var $_DOWNLOAD_ICON_WIDTH_MAX = "";
    
    /**
     * Print run
     * 印刷実行
     */
    function execute() {
        // Add all print 2010/07/21 A.Suzuki --start--
        if($this->all_print == "true"){
            // 共通の初期処理
            $result = $this->initAction();
            if ( $result == false ){
                // 未実装
                print "初期処理でエラー発生";
            }
            
            $smartyAssign = $this->Session->getParameter("smartyAssign");
            if($smartyAssign == null){
                // A resource tidy because it is not a call from view action is not obtained. 
                // However, it doesn't shutdown. 
                $this->setLangResource();
                $smartyAssign = $this->Session->getParameter("smartyAssign");
            }
            // set lang
            $lang = $this->Session->getParameter("_lang");
            
            $html = '';
            
            // Make item list bar --start--
            // Add design adjustment 2012/02/07 T.Koyasu -start-
            // th -> td & add font-weight=bold
            $html .= '<div class="th_repos_title_bar text_color">'.
                     '<table cellspacing="0" width="95%" class="text_color">'.
                     '<tr>'.
                     '<td style="text-align:left; font-weight:bold;" width="100px">'.
                     '<span style="white-space: nowrap;">';
            // Add design adjustment 2012/02/07 T.Koyasu -end-
            
            // List bar title
            if($this->Session->getParameter("searchkeyword")!=null){
                $html .= $smartyAssign->getLang("repository_search_result");
            } else {
                $html .= $smartyAssign->getLang("repository_search_list_view");
            }
            // Add design adjustment 2012/02/07 T.Koyasu -start-
            // th -> td
            $html .= '</span>'.
                     '</td>';
            // Add design adjustment 2012/02/07 T.Koyasu -end-
            
            // Get item data
            $repositorySearch = new RepositorySearch();
            $repositorySearch->Db = $this->Db;
            $repositorySearch->dbAccess = $this->dbAccess;
            $repositorySearch->Session = $this->Session;
            $repositorySearch->setRequestParameterFromReferrer();
            if((!isset($repositorySearch->index_id) || $repositorySearch->index_id == "" || $repositorySearch->index_id == "0")
                 && count($repositorySearch->search_term) == 0)
            {
                $repositorySearch->setDefaultSearchParameter();
            }
            $repositorySearch->listResords = "all";
            $searchResult = $repositorySearch->search();
            
            // Searched items count
            // Add design adjustment 2012/02/07 T.Koyasu -start-
            // th -> td & add font-weight=bold
            $html .= '<td style="font-size:85%;font-style:normal;font-weight:normal;text-align:right" valign="top" nowrap>';
            $html .= $repositorySearch->getTotal().' items</td>';
            // Add design adjustment 2012/02/07 T.Koyasu -end-
            
            $html .= '</tr></table></div>';
            // Make item list bar --end--
            
            // Add design adjustment 2012/02/07 T.Koyasu -start-
            // remove index list 2012/02/07 T.Koyasu
            $html .= '<br/>';
            // Add design adjustment 2012/02/07 T.Koyasu -end-
            
            //  Make item list --start--
            $html .= '<table class="text_color"><tbody>';
            
            // Get alternative language flag
            $alter_flg = "0";
            
            for($nCnt_ID=0;$nCnt_ID<count($searchResult);$nCnt_ID++){
                $Result_List = null;
                $search_result = $this->getItemData($searchResult[$nCnt_ID]["item_id"],
                                                    $searchResult[$nCnt_ID]["item_no"],
                                                    $Result_List,
                                                    $Error_Msg,
                                                    false,
                                                    true);
                
                $html .= '<tr>';

                // Item data
                $html .= '<td style="width: 590px;">';
                $html .= '<div class="paging2">';
                
                // title
                // Add design adjustment 2012/02/07 T.Koyasu -start-
                // set padding of item title (=view_print and check_print)
                $html .= '<div class="list_title_line pl00">';
                if($Result_List['item_type'][0]['icon_name']){
                    $html .= '<div class="fl pd10" style="width:16px; height: 16px;"><img onload="javascript: if(this.height < this.width){this.width=16;}else{this.height=16};" 
                            src="'.BASE_URL.'/?action=repository_action_common_download&item_type_id='.$Result_List['item'][0]['item_type_id'].'"/></div>';   // Modify Directory specification K.Matsuo 2011/9/1
                } else {
                    $html .= '<div class="fl pd10" style="width:16px; height: 16px;"><img width="16px" height="16px" src="'.BASE_URL.'/images/repository/tree/item.png"/></div>';
                }
                $html .= '<div class="list_title item_title bold ml40 pl00" style="padding-top: 8px; padding-bottom: 8px; border: 0px none;">';
                // Add design adjustment 2012/02/07 T.Koyasu -end-
                if($lang=="japanese"){
                    if($Result_List['item'][0]['title']!="" && $Result_List['item'][0]['title']!=null){
                        $html .= $this->forXmlChange($Result_List['item'][0]['title']);
                    } else {
                        $html .= $this->forXmlChange($Result_List['item'][0]['title_english']);
                    }
                } else {
                    if($Result_List['item'][0]['title_english']!="" && $Result_List['item'][0]['title_english']!=null){
                        $html .= $this->forXmlChange($Result_List['item'][0]['title_english']);
                    } else {
                        $html .= $this->forXmlChange($Result_List['item'][0]['title']);
                    }
                }
                $html .= '</div></div>';
                
                // alter title
                if($alter_flg=="1" && $Result_List['item'][0]['title']!="" && $Result_List['item'][0]['title_english']!=""){
                    $html .= '<div style="margin: 2px 3px 2px 2px;">';
                    if($lang=="japanese"){
                        $html .= $this->forXmlChange($Result_List['item'][0]['title_english']);
                    } else {
                        $html .= $this->forXmlChange($Result_List['item'][0]['title']);
                    }
                    $html .= '<br/></div>';
                }
                
                // metadata
                for($ii=0; $ii<count($Result_List['item_attr_type']); $ii++){
                    // display_flag default is "true"
                    if(!isset($Result_List['item_attr_type'][$ii]['display_flag'])){
                        $Result_List['item_attr_type'][$ii]['display_flag'] = "true";
                    }
                    
                    // ignore "file" and "file_price"
                    if($Result_List['item_attr_type'][$ii]['input_type']!="file" 
                        && $Result_List['item_attr_type'][$ii]['input_type']!="file_price")
                    {
                        if($alter_flg == "1"){
                            $Result_List['item_attr_type'][$ii]['display_flag'] = "true";
                        }
                        else if(count($Result_List['item_attr'][$ii])>0 
                         && $Result_List['item_attr_type'][$ii]['display_flag'] != "false" 
                         && $Result_List['item_attr_type'][$ii]['junii2_mapping'] != "")
                        {
                            // Check junii2 mapping
                            for($jj=$ii+1; $jj<count($Result_List['item_attr_type']); $jj++){
                                // junii2 mapping is same
                                if($Result_List['item_attr_type'][$ii]['junii2_mapping'] == $Result_List['item_attr_type'][$jj]['junii2_mapping']){
                                    // display_lang_type is not match to now langage
                                    if($Result_List['item_attr_type'][$jj]['display_lang_type'] != "" && $Result_List['item_attr_type'][$jj]['display_lang_type'] != $lang){
                                        // that data is no display
                                        $Result_List['item_attr_type'][$jj]['display_flag'] = "false";
                                    } else if($Result_List['item_attr_type'][$ii]['display_lang_type'] != "" && $Result_List['item_attr_type'][$ii]['display_lang_type'] != $lang){
                                        // that metadata is not null
                                        if(count($Result_List['item_attr'][$jj])>0 && $Result_List['item_attr_type'][$ii]['display_lang_type'] != "" && $Result_List['item_attr_type'][$ii]['display_lang_type'] != $lang){
                                            // this data is display
                                            $Result_List['item_attr_type'][$ii]['display_flag'] = "false";
                                        }
                                    }
                                }
                            }
                        } else if( $Result_List['item_attr_type'][$ii]['display_flag'] != "false" 
                         && ($Result_List['item_attr_type'][$ii]['display_lang_type'] == $lang 
                            || $Result_List['item_attr_type'][$ii]['display_lang_type'] == "") 
                         && count($Result_List['item_attr'][$ii])>0)
                        {
                            $Result_List['item_attr_type'][$ii]['display_flag'] = "true";
                        } else {
                            $Result_List['item_attr_type'][$ii]['display_flag'] = "false";
                        }
                    }
                }
                
                $str_metadata = "";
                for($nCnt_attr_type=0;$nCnt_attr_type<count($Result_List['item_attr_type']);$nCnt_attr_type++){
                    $str = "";
                    if($Result_List['item_attr_type'][$nCnt_attr_type]['list_view_enable'] == 1 && $Result_List['item_attr_type'][$nCnt_attr_type]['display_flag'] == "true"){
                        if($Result_List['item_attr_type'][$nCnt_attr_type]['input_type'] == "name"){
                            for($nCnt=0;$nCnt<count($Result_List['item_attr'][$nCnt_attr_type]);$nCnt++){
                                if($str!=""){
                                    $str .= " , ";
                                }
                                $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['family']." " .$Result_List['item_attr'][$nCnt_attr_type][$nCnt]['name']);
                            }
                        } else if($Result_List['item_attr_type'][$nCnt_attr_type]['input_type'] == "thumbnail"){
                            // Add show thumbnail in search result 2012/02/13 T.Koyasu -start-
                            // thumbnail name was not show, has no process
                            // thumbnail image was shown under metadata upper file icon
                            // Add show thumbnail in search result 2012/02/13 T.Koyasu -end-
                        } else if($Result_List['item_attr_type'][$nCnt_attr_type]['input_type'] == "file" ||
                                $Result_List['item_attr_type'][$nCnt_attr_type]['input_type'] == "file_price"
                        ){
                            for($nCnt=0;$nCnt<count($Result_List['item_attr'][$nCnt_attr_type]);$nCnt++){
                                // display_type : simple
                                if($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['display_type'] == '1'){
                                    if($str!=""){
                                        $str .= " , ";
                                    }
                                    if($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['display_name'] != ""){
                                        $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['display_name']);
                                    } else {
                                        $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['file_name']);
                                    }
                                }
                            }
                        } else if($Result_List['item_attr_type'][$nCnt_attr_type]['input_type'] == "link"){
                            for($nCnt=0;$nCnt<count($Result_List['item_attr'][$nCnt_attr_type]);$nCnt++){
                                if($str!=""){
                                    $str .= " , ";
                                }
                                $link_array = explode("|", $Result_List['item_attr'][$nCnt_attr_type][$nCnt]['attribute_value'], 2);
                                if($link_array[1]!=""){
                                    $str .= $this->forXmlChange($link_array[1]);
                                } else {
                                    $str .= $this->forXmlChange($link_array[0]);
                                }
                            }
                        } else if($Result_List['item_attr_type'][$nCnt_attr_type]['input_type'] == "biblio_info"){
                            for($nCnt=0;$nCnt<count($Result_List['item_attr'][$nCnt_attr_type]);$nCnt++){
                                // 書誌情報の表示は「雑誌名, 巻(号), 開始ページ-終了ページ(発行年)」とする
                                if($str != ""){
                                    $str .= " , ";
                                }
                                
                                // biblio_name
                                if($lang=="japanese"){
                                    if($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name']!="" && $Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name']!=null){
                                        $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name']);
                                    } else {
                                        $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name_english']);
                                    }
                                } else {
                                    if($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name_english']!="" && $Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name_english']!=null){
                                        $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name_english']);
                                    } else {
                                        $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name']);
                                    }
                                }
                                
                                if($alter_flg == "1" 
                                    && $Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name']!="" 
                                    && $Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name_english']!="")
                                {
                                    if($lang=="japanese"){
                                        $str .= "/".$this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name_english']);
                                    } else {
                                        $str .= "/".$this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['biblio_name']);
                                    }
                                }
                                
                                // volume
                                if($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['volume']!=""){
                                    if($str != ""){
                                        $str .= ",";
                                    }
                                    $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['volume']);
                                }
                                
                                // issue
                                if($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['issue']!=""){
                                    if(($str!="") && ($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['volume']=="")){
                                        $str .= ",";
                                    }
                                    $str .= $this->forXmlChange("(".$Result_List['item_attr'][$nCnt_attr_type][$nCnt]['issue'].")");
                                }
                                
                                // spage and epage
                                if(($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['start_page']!="") && ($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['end_page']!="")){
                                    if($str != ""){
                                        $str .= ",";
                                    }
                                    $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['start_page']."-".$Result_List['item_attr'][$nCnt_attr_type][$nCnt]['end_page']);
                                }else if($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['start_page']!=""){
                                    if($str != ""){
                                        $str .= ",";
                                    }
                                    $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['start_page']);
                                }else if($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['end_page']!=""){
                                    if($str != ""){
                                        $str .= ",";
                                    }
                                    $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['end_page']);
                                }
                                
                                // date_of_issued
                                if($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['date_of_issued']!=""){
                                    if(($str != "") && ($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['start_page']=="") && ($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['end_page']=="")){
                                        $str .= ",";
                                    }
                                    $str .= $this->forXmlChange(" (".$Result_List['item_attr'][$nCnt_attr_type][$nCnt]['date_of_issued'].")");
                                }
                            }
                        } else if($Result_List['item_attr_type'][$nCnt_attr_type]['input_type'] == "textarea"){
                            for($nCnt=0;$nCnt<count($Result_List['item_attr'][$nCnt_attr_type]);$nCnt++){
                                if($str!=""){
                                    $str .= " , ";
                                }
                                $str .= str_replace("\n", "<br/>", $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['attribute_value']));
                            }
                        } else if($Result_List['item_attr_type'][$nCnt_attr_type]['input_type'] == "heading"){
                            // Add design adjustment 2012/02/08 T.Koyasu -start-
                            $headingArray = explode("|", $Result_List['item_attr'][$nCnt_attr_type][0]['attribute_value'], 4);
                            for($nCnt=0;$nCnt<count($headingArray);$nCnt++){
                                if($str!="" && $headingArray[$nCnt]!=""){
                                    $str .= " , ";
                                }
                                if($headingArray[$nCnt]!=""){
                                    $str .= $this->forXmlChange($headingArray[$nCnt]);
                                }
                            }
                            // Add design adjustment 2012/02/08 T.Koyasu -end-
                        } else {
                            for($nCnt=0;$nCnt<count($Result_List['item_attr'][$nCnt_attr_type]);$nCnt++){
                                if($str!=""){
                                    $str .= " , ";
                                }
                                $str .= $this->forXmlChange($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['attribute_value']);
                            }
                            // 改行指定があった場合、文末に " , "は表示しない
                            if($Result_List['item_attr_type'][$nCnt_attr_type]['line_feed_enable'] == 1){
                                $this->array_attr[count($Result_List['item_attr'][$nCnt_attr_type])]["last_flg"] = 1;
                            }
                            
                        }
                        
                        // check line_feed_enable
                        if($str!=""){
                            if($Result_List['item_attr_type'][$nCnt_attr_type]['line_feed_enable']==1){
                                $str .= "<br>";
                            } else {
                                $str .= " , ";
                            }
                            $str_metadata .= $str;
                        }
                    }
                }
                if($str_metadata!=""){
                    if(substr($str_metadata,-3,3)==" , "){
                        $str_metadata = substr($str_metadata,0,-3);
                    }
                    $html .= '<div class="list_attr_repos ml40 pl00">'.$str_metadata.'</div>';
                }
                
                // Add show thumbnail in search result 2012/02/13 T.Koyasu -start-
                $str_thumbnail = "";
                for($nCntAttrType=0;$nCntAttrType<count($Result_List['item_attr_type']);$nCntAttrType++){
                    if($Result_List['item_attr_type'][$nCntAttrType]['input_type'] == 'thumbnail'){
                        if($Result_List['item_attr_type'][$nCntAttrType]['list_view_enable'] == 1){
                            $thumbnail = array();
                            $thumbnail = $this->getThumbnailInfo($Result_List, $nCntAttrType);
                            for($nCnt=0;$nCnt<count($thumbnail);$nCnt++){
                                $size = "";
                                if($thumbnail[$nCnt]['width'] > 50 || $thumbnail[$nCnt]['height'] > 50){
                                    if($thumbnail[$nCnt]['width'] > $thumbnail[$nCnt]['height']){
                                        $size = ' width="50px"';
                                    }else{
                                        $size = ' height="50px"';
                                    }
                                }
                                
                                $str_thumbnail .= '<img '. 
                                                  'class="mr05"'. 
                                                  $size. 
                                                  ' src="'. BASE_URL. '/?action=repository_action_common_download'. 
                                                     '&item_id='. $thumbnail[$nCnt]['item_id']. 
                                                     '&item_no='. $thumbnail[$nCnt]['item_no']. 
                                                     '&attribute_id='. $thumbnail[$nCnt]['attribute_id']. 
                                                     '&file_no='. $thumbnail[$nCnt]['file_no']. 
                                                     '&img=true"'. 
                                                  ' />';
                            }
                        }
                    }
                }
                if($str_thumbnail != ""){
                    $html .= '<div class="list_attr_repos ml40 pl00">'. $str_thumbnail. '</div>';
                }
                // Add show thumbnail in search result 2012/02/13 T.Koyasu -end-
                
                $str_file = "";
                for($nCnt_attr_type=0;$nCnt_attr_type<count($Result_List['item_attr_type']);$nCnt_attr_type++){
                    if($Result_List['item_attr_type'][$nCnt_attr_type]['list_view_enable'] == 1){
                        if($Result_List['item_attr_type'][$nCnt_attr_type]['input_type'] == "file" || 
                           $Result_List['item_attr_type'][$nCnt_attr_type]['input_type'] == "file_price"){
                            for($nCnt=0;$nCnt<count($Result_List['item_attr'][$nCnt_attr_type]);$nCnt++){
                                if($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['display_type']!='1'){
                                    $extension = $Result_List['item_attr'][$nCnt_attr_type][$nCnt]['extension'];
                                    $file_label = $this->mimetypeSimpleName($Result_List['item_attr'][$nCnt_attr_type][$nCnt]['mime_type']);
                                    if($file_label == "" && $extension != ""){
                                        $file_label = $extension;
                                    }
                                    $adjusted_label = array();
                                    $this->AdjustLabelWidth($file_label, $adjusted_label );
                                    
                                    // Add design adjustment 2012/02/07 T.Koyasu -start-
                                    // remove link and set border
                                    $str_file .= '<span class="brdl01 brdt01 brdb01 brdr01 mr05 ptb02">';
                                    for($nCnt_label=0;$nCnt_label<count($adjusted_label);$nCnt_label++){
                                        if($adjusted_label[$nCnt_label]==' '){
                                            $str_file .= ' &nbsp; ';
                                        } else {
                                            $str_file .= $adjusted_label[$nCnt_label];
                                        }
                                    }
                                    $str_file .= '</span>';
                                    // Add design adjustment 2012/02/07 T.Koyasu -end-
                                }
                            }
                        }
                    }
                }
                if($str_file!=""){
                    // Add design adjustment 2012/02/07 T.Koyasu -start-
                    // remove padding left
                    $html .= '<div class="list_attr_repos ml40 pl00">'.$str_file.'</div>';
                    // Add design adjustment 2012/02/07 T.Koyasu -end-
                }
                
                $html .= '</div></td>';
                
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table>';
            //  Make item list --end--
            
            echo($html);
            exit();
        }
        // Add all print 2010/07/21 A.Suzuki --start--
        else {
            // Add RepositoryDownload action 2010/03/30 A.Suzuki --start--
            $repositoryDownload = new RepositoryDownload();
            $repositoryDownload->download("", "print.html");
            // Add RepositoryDownload action 2010/03/30 A.Suzuki --end--
            exit();
        }
    }
    
    /**
     * To adjust the display of the label to the maximum number of characters / minimum number of characters
     * ラベルの表記を最大文字数/最小文字数に合わせて調整する
     *
     * @param int $original Original size 元サイズ
     * @param int $adjusted Adjusted size 調整後サイズ
     */
    private function AdjustLabelWidth($original, &$adjusted)
    {
        if($this->_DOWNLOAD_ICON_WIDTH_MIN == ""){
            $config = parse_ini_file(BASE_DIR.'/webapp/modules/repository/config/main.ini');
            if( isset($config["define:_DOWNLOAD_ICON_WIDTH_MIN"]) && 
                strlen($config["define:_DOWNLOAD_ICON_WIDTH_MIN"]) > 0 && 
                is_numeric($config["define:_DOWNLOAD_ICON_WIDTH_MIN"])){
                $this->_DOWNLOAD_ICON_WIDTH_MIN = intval($config["define:_DOWNLOAD_ICON_WIDTH_MIN"]);
            } else {
                $this->_DOWNLOAD_ICON_WIDTH_MIN = 10;
            }
        }
        if($this->_DOWNLOAD_ICON_WIDTH_MAX == ""){
            if( isset($config["define:_DOWNLOAD_ICON_WIDTH_MAX"]) && 
                strlen($config["define:_DOWNLOAD_ICON_WIDTH_MAX"]) > 0 && 
                is_numeric($config["define:_DOWNLOAD_ICON_WIDTH_MAX"])){
                $this->_DOWNLOAD_ICON_WIDTH_MAX = intval($config["define:_DOWNLOAD_ICON_WIDTH_MAX"]);
            } else {
                $this->_DOWNLOAD_ICON_WIDTH_MAX = 15;
            }
        }
        
        $len_ori = strlen($original);
        $len_adjusted = $len_ori;
        $str = '';
        // 調整後の文字列長を決定
        if( $len_ori < $this->_DOWNLOAD_ICON_WIDTH_MIN  ) {
            // オリジナルが短すぎる場合は半角スペースを追加
            $len_adjusted = $this->_DOWNLOAD_ICON_WIDTH_MIN;
            $str = $original;
            $len_gap = $len_adjusted - $len_ori;
            $len_gap_bef = (int)($len_gap/2);
            $len_gap_aft = (int)($len_gap/2) + (int)($len_gap%2);
            for($ii=0; $ii<$len_gap_bef; $ii++){
                $str = ' '.$str;
            }
            for($ii=0; $ii<$len_gap_aft; $ii++){
                $str = $str . ' ';
            }
        } else if( $len_ori > $this->_DOWNLOAD_ICON_WIDTH_MAX  ) {
            // オリジナルが長すぎる場合はトリム
            $len_adjusted = $this->_DOWNLOAD_ICON_WIDTH_MAX;
            $str = substr($original, 0, $len_adjusted);
        } else {
            // オリジナルをそのままコピー
            $str = $original;
        }
        
        // "___pdf____"として整形された文字列を配列に詰める
        // htmlの表示の都合上、
        // [0]=" ",[1]=" ",[2]=" ",[3]="p",[4]="d",[5]="f",[6]=" ",[7]=" ",[8]=" ",[9]=" "
        // ではなく、(上記だとhtmlで"___p_d_f____"と半角スペース表示が混じるため。これはhtmlの仕様)
        // [0]=" ",[1]=" ",[2]=" ",[3]="pdf",[4]=" ",[5]=" ",[6]=" "として詰め込む。
        for($ii=0; $ii<strlen($str); $ii++){
            if(substr($str, $ii, 1) == ' '){
                // 空白はそのまま詰め込む
                array_push($adjusted, substr($str, $ii, 1));
            } else {
                // 空白以外の場合、前後の空白を取り除き、表示する文字列を一塊で詰め込む
                array_push($adjusted, trim($str));
                // 詰め込んだ文字列分、ポインタを移動
                $ii += strlen(trim($str)) - 1;
            }
        }
        
        return;     
    }
    
    // Add show thumbnail in search result 2012/02/13 T.Koyasu -start-
    /**
     * Get thumbnail data
     * サムネイル取得
     *
     * @param array $ResultList Item data アイテムデータ
     *                          array["item"][$ii]["item_id"|"item_no"|"revision_no"|"item_type_id"|"prev_revision_no"|"title"|"title_english"|"language"|"review_status"|"review_date"|"shown_status"|"shown_date"|"reject_status"|"reject_date"|"reject_reason"|"serch_key"|"serch_key_english"|"remark"|"uri"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                          array["item_type"][$ii]["item_type_id"|"item_type_name"|"item_type_short_name"|"explanation"|"mapping_info"|"icon_name"|"icon_mime_type"|"icon_extension"|"icon"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                          array["item_attr_type"]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                          array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                          array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                          array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                          array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                          array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                          array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                          array["item_attr"][$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param int $nCntAttrType Attribute ID 属性ID
     * @return array Thumbnail data サムネイルデータ
     *               array[$ii]["item_id"|"item_no"|"attribute_id"|"file_no"|"width"|"height"]
     */
    private function getThumbnailInfo($ResultList, $nCntAttrType)
    {
        // get itemId, itemNo and attributeId by ResultList
        $itemId = $ResultList['item'][0]['item_id'];
        $itemNo = $ResultList['item'][0]['item_no'];
        $attrId = $ResultList['item_attr_type'][$nCntAttrType]['attribute_id'];
        
        // get thumbnail image and data
        $result = $this->getThumbnailTableData($itemId, $itemNo, $attrId, $nCntAttrType, $tmpData, $errorMsg, true);
        if($result === false && count($tmpData['item_attr'][$nCntAttrType]) === 0)
        {
            return;
        }
        $thumbnailData = array();
        $thumbnailData = $tmpData['item_attr'][$nCntAttrType];
        
        // array for return
        $thumbnail = array();
        
        // thumbnail data
        for($nCnt=0;$nCnt<count($thumbnailData);$nCnt++)
        {
            $fileNo = $thumbnailData[$nCnt]['file_no'];
            
            // create image
            $img = imagecreatefromstring($thumbnailData[$nCnt]['file']);
            if($img !== false)
            {
                // get width
                $width = imagesx($img);
                // get height
                $height = imagesy($img);
                // drop image
                imagedestroy($img);
            }
            
            // set value
            array_push($thumbnail, array('item_id'=>$itemId, 
                                         'item_no'=>$itemNo, 
                                         'attribute_id'=>$attrId, 
                                         'file_no'=>$fileNo, 
                                         'width'=>$width, 
                                         'height'=>$height));
        }
        
        return $thumbnail;
    }
    // Add show thumbnail in search result 2012/02/13 T.Koyasu -end-
}
?>
