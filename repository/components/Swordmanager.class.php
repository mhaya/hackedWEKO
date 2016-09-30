<?php
/**
 * Common class for create item type XML
 * アイテムタイプXML作成共通クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Swordmanager.class.php 70936 2016-08-09 09:53:57Z keiya_sugimoto $
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
 * WEKO logic-based base class
 * WEKOロジックベース基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryLogicBase.class.php';

/**
 * Common classes for factory
 * ファクトリー用共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/Factory.class.php';

/**
 * Item export processing common classes
 * アイテムエクスポート処理共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/action/main/export/ExportCommon.class.php';

/**
 * SCfW metadata file output common classes
 * SCfWメタデータファイル出力共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryOutputTSV.class.php';
/**
 * Date manipulation common classes
 * 日付操作共通クラス
 */
include_once WEBAPP_DIR. '/modules/repository/files/pear/Date.php';

/**
 * Common class for create item type XML
 * アイテムタイプXML作成共通クラス
 *
 * @package WEKO
 * @copyright (c) 2007 - 2008, National Institute of Informatics, Research and Development Center for Scientific Information Resources.
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Swordmanager extends RepositoryLogicBase
{
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ROOT = 'wekoDataConvertFilter';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_METADATA = 'metadata';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ITEMTYPES = 'itemTypes';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ITEMTYPE = 'itemType';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ITEMTYPE_NAME = 'name';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_BASICATTRIBUTES = 'basicAttributes';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_TITLE = 'title';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_TITLEINENGLISH = 'titleInEnglish';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_LANGUAGE = 'language';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_KEYWORDS = 'keywords';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_KEYWORDSINENGLISH = 'keywordsInEnglish';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_PUBLICATIONDATE = 'publicationDate';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ADDITIONALATTRIBUTES = 'additionalAttributes';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ADDITIONALATTRIBUTE = 'additionalAttribute';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ADD_ATTR_NAME = 'name';
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ADD_ATTR_CANDIDATES = 'candidates';
    
    
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_TYPE = 'type';
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_MAPPING_INFO = 'mapping_info';
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_REQUIRED = 'required';
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_ALLOWMULTIPLEINPUT = 'allowmultipleinput';
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_LISTING = 'listing';
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_SPECIFYNEWLINE = 'specifynewline';
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_HIDDEN = 'hidden';
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_JUNII2_MAPPING = 'junii2_mapping';
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_DUBLIN_CORE_MAPPING = 'dublin_core_mapping';
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_DELIMITERS = 'delimiters';
    /**
     * Attribute name
     * 属性名
     * 
     * @var string
     */
    const ATTRIBUTE_DISPLAY_LANG_TYPE = 'display_lang_type';
    
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_ITEMTYPE = 'columnname_itemtype';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_VALUE = 'columnname_value';
    /**
     * Name connect flag
     * 姓名連結フラグ
     * 
     * @var string
     */
    const ATTRIBUTE_ISFAMILYGIVENCONNECT = 'isfamilygivenconnected';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_FAMILY = 'columnname_family';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_GIVEN = 'columnname_given';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_FAMILYRUBY = 'columnname_familyruby';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_GIVENRUBY = 'columnname_givenruby';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_EMAILADDRESS = 'columnname_emailaddress';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_AUTHORIDS = 'columnname_authorids';
    /**
     * Input format
     * 記入形式
     * 
     * @var string
     */
    const ATTRIBUTE_ISSTARTENDPAGECONNECT = 'isstartendpageconnected';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_BIBLIONAME = 'columnname_biblioname';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_BIBLIONAMEENGLISH = 'columnname_biblionameenglish';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_VOLUME = 'columnname_volume';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_ISSUE = 'columnname_issue';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_STARTPAGE = 'columnname_startpage';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_ENDPAGE = 'columnname_endpage';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_DATEOFISSUED = 'columnname_dateofissued';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_LINKNAME = 'columnname_linkname';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_LINKURL = 'columnname_linkurl';
    /**
     * Display format
     * 表示形式
     * 
     * @var string
     */
    const ATTRIBUTE_DISPLAYTYPE = 'displaytype';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_FILENAME = 'columnname_filename';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_DISPLAYNAME = 'columnname_displayname';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_PUBDATE = 'columnname_pubdate';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_LICENSE_CC = 'columnname_license_cc';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_LICENSE_FREE = 'columnname_license_free';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_FLASH_PUBDATE = 'columnname_flashpubdate';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_ACCOUNTING_NONSUBSCRIBER = 'columnname_accounting_nonsubscriber';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_ACCOUNTING = 'columnname_accounting';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_HEADINGJP = 'columnname_headingjp';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_HEADINGEN = 'columnname_headingen';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_HEADINGSUBJP = 'columnname_headingsubjp';
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const ATTRIBUTE_COLNAME_HEADINGSUBEN = 'columnname_headingsuben';
    
    /**
     * create xml
     * XML作成
     *
     * @param string $xml_str XML string XML文字列
     * @return boolean Result 結果
     */
    public function createItemtypeXml(&$xml_str)
    {
        $smartyAssign = $this->Session->getParameter("smartyAssign");
        if($smartyAssign == null){
            // A resource tidy because it is not a call from view action is not obtained. 
            // However, it doesn't shutdown. 
            $RepositoryAction = new RepositoryAction();
            $RepositoryAction->Session = $this->Session;
            $RepositoryAction->setLangResource();
            $smartyAssign = $this->Session->getParameter("smartyAssign");
        }
        $column_item_type = $smartyAssign->getLang("repository_itemtype");
        
        $xml_str = "";
        $xml_str .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
                   ."<". self::TAG_ROOT .">\n"
                   ."<". self::TAG_METADATA ." ".self::ATTRIBUTE_COLNAME_ITEMTYPE."=\"".$column_item_type."\" />\n"
                   ."<". self::TAG_ITEMTYPES .">\n";
        // get the mapping information and item type ID of the item type of harvest for default item type other than
        $query = "SELECT item_type_id, mapping_info ".
                 "FROM ".DATABASE_PREFIX."repository_item_type ".
                 "WHERE ( item_type_id < ? OR item_type_id  > ? ) ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = 20001;
        $params[] = 20017;
        $params[] = 0;
        $itemtype_mapping = $this->dbAccess->executeQuery($query, $params);
        
        // convert to XML in a format that SCfW can recognize the item type information of all
        //$exportCommon = new ExportCommon($this->Db, $this->Session, $this->TransStartDate);
        $exportCommon = Repository_Components_Factory::getComponent('ExportCommon');
        for($cnt = 0; $cnt < count($itemtype_mapping); $cnt++)
        {
            $DATE = new Date();
            $execute_time = str_replace(":","-",$DATE->getDate());
            $tmp_dir = WEBAPP_DIR. '/uploads/repository/'.$execute_time;
            // get xml of item type information from ExportCommon
            $item_type_xml = "<?xml version=\"1.0\"?>\n".
                             "<export>\n";
            $result = $exportCommon->createItemTypeExportFile($tmp_dir, $itemtype_mapping[$cnt]['item_type_id']);
            if($result === false)
            {
                return false;
            }
            $item_type_xml .= $result['buf'];
            $item_type_xml .= "</export>\n";
            // change xml format into SCfW format by result gotten ExportCommon
            $filter_xml = $this->convertItemtypeXmlToFilterXml($item_type_xml, $itemtype_mapping[$cnt]['item_type_id']);
            if($filter_xml === false)
            {
                return false;
            }
            $xml_str .= "<". self::TAG_ITEMTYPE . " " . 
                        self::ATTRIBUTE_MAPPING_INFO . "=\"" . 
                        $itemtype_mapping[$cnt]['mapping_info'] . 
                        "\">\n" . 
                        $filter_xml .
                        "</" . self::TAG_ITEMTYPE .">\n";
        }
        
        // delete temporary directory
        $exportCommon->removeDirectory($tmp_dir);
        
        $xml_str .= "</" . self::TAG_ITEMTYPES . ">\n".
                    "</" . self::TAG_ROOT . ">\n";
    }
    
    /**
     * convert item type xml to filter xml
     * アイテムタイプXML作成
     *
     * @param string $item_type_xml Item type XML アイテムタイプXML
     * @param int $item_type_id Item type id アイテムタイプID
     * @return string XML string XML文字列
     */
    private function convertItemtypeXmlToFilterXml($item_type_xml, $item_type_id)
    {
        try
        {
            // parse xml
            $output_array = array();
            $xml_parser = xml_parser_create();
            $result = xml_parse_into_struct($xml_parser, $item_type_xml, $output_array);
            
            if($result === 0)
            {
                $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );
                throw $exception;
            }
            
            // get header string
            $header_str = '';
            //$outputTsv = new RepositoryOutputTSV($this->Db, $this->Session);
            $outputTsv = Repository_Components_Factory::getComponent('RepositoryOutputTSV');
            $header_str = $outputTsv->getTsvHeader($item_type_id);
            // divide header string
            $header_str_array = explode("\t", $header_str);
            
            // create base metadata
            $update_xml = "";
            $update_xml .= "<" . self::TAG_ITEMTYPE_NAME . ">" .
                               $output_array[1]['attributes']['ITEM_TYPE_NAME'] . 
                           "</".self::TAG_ITEMTYPE_NAME.">\n";
            
            $update_xml .= "<" . self::TAG_BASICATTRIBUTES . ">\n" .
                               "<" . self::TAG_TITLE . " " . self::ATTRIBUTE_COLNAME_VALUE . "=\"" . $header_str_array[2] ."\"/>\n" .
                               "<" . self::TAG_TITLEINENGLISH . " " . self::ATTRIBUTE_COLNAME_VALUE . "=\"" . $header_str_array[3] ."\"/>\n" .
                               "<" . self::TAG_LANGUAGE . " " . self::ATTRIBUTE_COLNAME_VALUE . "=\"" . $header_str_array[4] ."\"/>\n" .
                               "<" . self::TAG_KEYWORDS . " " . self::ATTRIBUTE_COLNAME_VALUE . "=\"" . $header_str_array[5] ."\"/>\n" .
                               "<" . self::TAG_KEYWORDSINENGLISH . " " . self::ATTRIBUTE_COLNAME_VALUE . "=\"" . $header_str_array[6] ."\"/>\n" .
                               "<" . self::TAG_PUBLICATIONDATE . " " . self::ATTRIBUTE_COLNAME_VALUE . "=\"" . $header_str_array[7] ."\"/>\n" .
                           "</" . self::TAG_BASICATTRIBUTES . ">\n";
            // get options
            $options_array = array();
            for($cnt = 0; $cnt < count($output_array); $cnt++)
            {
                $tag_name = $output_array[$cnt]['tag'];
                if($tag_name === 'REPOSITORY_ITEM_ATTR_CANDIDATE')
                {
                    $attribute_id = $output_array[$cnt]['attributes']['ATTRIBUTE_ID'];
                    $options = $output_array[$cnt]['attributes']['CANDIDATE_VALUE'];
                    
                    if(isset($options_array[$attribute_id]))
                    {
                        $options_array[$attribute_id] .= '|';
                    }
                    else
                    {
                        $options_array[$attribute_id] = '';
                    }
                    $options_array[$attribute_id] .= $options;
                }
            }
            $update_xml .= "<" . self::TAG_ADDITIONALATTRIBUTES . ">\n";
            // add metadata node
            $header_num = 8;
            for($cnt = 0; $cnt < count($output_array); $cnt++)
            {
                $tag_name = $output_array[$cnt]['tag'];
                if($tag_name === 'REPOSITORY_ITEM_ATTR_TYPE')
                {
                    // Initialize variables.
                    $attribute_name = $output_array[$cnt]['attributes']['ATTRIBUTE_NAME'];
                    $input_type = $output_array[$cnt]['attributes']['INPUT_TYPE'];
                    $is_required = $output_array[$cnt]['attributes']['IS_REQUIRED'];
                    $plural_enable = $output_array[$cnt]['attributes']['PLURAL_ENABLE'];
                    $list_view_enable = $output_array[$cnt]['attributes']['LIST_VIEW_ENABLE'];
                    $line_feed_enable = $output_array[$cnt]['attributes']['LINE_FEED_ENABLE'];
                    $hidden = $output_array[$cnt]['attributes']['HIDDEN'];
                    $junii2_mapping = $output_array[$cnt]['attributes']['JUNII2_MAPPING'];
                    $dublin_core_mapping = $output_array[$cnt]['attributes']['DUBLIN_CORE_MAPPING'];
                    $lom_mapping = $output_array[$cnt]['attributes']['LOM_MAPPING'];
                    $display_lang_type = $output_array[$cnt]['attributes']['DISPLAY_LANG_TYPE'];
                    $attribute_id = $output_array[$cnt]['attributes']['ATTRIBUTE_ID'];
                    
                    if($input_type == 'select')
                    {
                        $input_type = 'pulldownmenu';
                    }
                    else if($input_type == 'radio')
                    {
                        $input_type = 'radiobutton';
                    }
                    else if($input_type == 'biblio_info')
                    {
                        $input_type = 'biblioinfo';
                    }
                    else if($input_type == 'supple')
                    {
                        $input_type = 'supplementalcontents';
                    }
                    
                    $common_attr_str = '';
                    $common_attr_str .= self::ATTRIBUTE_TYPE."=\"".$input_type."\" ".
                                        self::ATTRIBUTE_REQUIRED."=\"".$this->boolToString($is_required)."\" ".
                                        self::ATTRIBUTE_ALLOWMULTIPLEINPUT."=\"".$this->boolToString($plural_enable)."\" ".
                                        self::ATTRIBUTE_LISTING."=\"".$this->boolToString($list_view_enable)."\" ".
                                        self::ATTRIBUTE_SPECIFYNEWLINE."=\"".$this->boolToString($line_feed_enable)."\" ".
                                        self::ATTRIBUTE_HIDDEN."=\"".$this->boolToString($hidden)."\" ".
                                        self::ATTRIBUTE_JUNII2_MAPPING."=\"".$junii2_mapping."\" ".
                                        self::ATTRIBUTE_DUBLIN_CORE_MAPPING."=\"".$dublin_core_mapping."\" ".
                                        self::ATTRIBUTE_DELIMITERS."=\"|\" ".
                                        self::ATTRIBUTE_DISPLAY_LANG_TYPE."=\"".$display_lang_type."\" ";
                    
                    switch($input_type)
                    {
                        case "text":
                        case "date":
                            $this->addTextDateXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml);
                            break;
                        case "textarea":
                            $this->addTextareaXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml);
                            break;
                        case "name":
                            $this->addNameXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml);
                            break;
                        case "biblioinfo":
                            $this->addBiblioXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml);
                            break;
                        case "link":
                            $this->addLinkXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml);
                            break;
                        case "file":
                            $this->addFileXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml);
                            break;
                        case "file_price":
                            $this->addFilePriceXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml);
                            break;
                        case "thumbnail":
                            $this->addThumbnailXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml);
                            break;
                        case "heading":
                            $this->addHeadingXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml);
                            break;
                        case "checkbox":
                        case "radiobutton":
                        case "pulldownmenu":
                            $this->addCandidateXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml, $options_array, $attribute_id);
                            break;
                        case "supplementalcontents":
                            $this->addSuppleXmlNode($attribute_name, $common_attr_str, $header_str_array, $header_num, $update_xml);
                            break;
                        default:
                            break;
                    }
                }
            }
            $update_xml .= "</" . self::TAG_ADDITIONALATTRIBUTES . ">\n";
            // return updated xml
            return $update_xml;
        
        }
        catch(RepositoryException $Exception)
        {
            return false;
        }
    }
    
    /**
     * add xml node for 'text' and 'date'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     * @return boolean Result 結果
     */
    private function addTextDateXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_COLNAME_VALUE."=\"".$header_str_array[$header_num]."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n".
                       "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        $header_num++;
        return true;
    }
    
    /**
     * add xml node for 'textarea'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     */
    private function addTextareaXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_COLNAME_VALUE."=\"".$attribute_name."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n".
                       "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        
        $query = "SELECT MAX(attr.count) ".
                 "FROM ( ".
                 "SELECT item_id, item_no, attribute_id, count(attribute_no) as count ".
                 "FROM ".DATABASE_PREFIX."repository_item_attr ".
                 "WHERE ( `item_type_id`, `attribute_id` ) IN ".
                 "( ".
                 "SELECT item_type_id, attribute_id ".
                 "FROM ".DATABASE_PREFIX."repository_item_attr_type ".
                 "WHERE attribute_name = ? ".
                 "AND input_type = 'textarea' ".
                 ") ".
                 "GROUP BY item_id, item_no, attribute_id ".
                 ") AS attr ; ";
        $params = array();
        $params[] = $attribute_name;
        $result = $this->dbAccess->executeQuery($query, $params);
        if($result[0]['MAX(attr.count)'] == null)
        {
            $result[0]['MAX(attr.count)'] = 0;
        }
        $additional_num = intval($result[0]['MAX(attr.count)']);
        $header_num += $additional_num;
    }
    
    /**
     * add xml node for 'name'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     */
    private function addNameXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_ISFAMILYGIVENCONNECT."=\"true\" ".
                           self::ATTRIBUTE_COLNAME_FAMILY."=\"".$header_str_array[$header_num]."\" ".
                           self::ATTRIBUTE_COLNAME_FAMILYRUBY."=\"".$header_str_array[$header_num+1]."\" ".
                           self::ATTRIBUTE_COLNAME_EMAILADDRESS."=\"".$header_str_array[$header_num+2]."\" ".
                           self::ATTRIBUTE_COLNAME_AUTHORIDS."=\"".$header_str_array[$header_num+3]."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n".
                       "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        $header_num += 3;
        
        $query = "SELECT COUNT(prefix_id) AS CNT ".
                 "FROM ".DATABASE_PREFIX."repository_external_author_id_prefix ".
                 "WHERE prefix_id > ? ".
                 "AND is_delete = ? ; ";
        $params = array();
        $params[] = 0;
        $params[] = 0;
        $result = $this->dbAccess->executeQuery($query, $params);
        
        $header_num += $result[0]['CNT'];
    }
    
    /**
     * add xml node for 'biblioinfo'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     */
    private function addBiblioXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_ISSTARTENDPAGECONNECT."=\"false\" ".
                           self::ATTRIBUTE_COLNAME_BIBLIONAME."=\"".$header_str_array[$header_num]."\" ".
                           self::ATTRIBUTE_COLNAME_BIBLIONAMEENGLISH."=\"".$header_str_array[$header_num+1]."\" ".
                           self::ATTRIBUTE_COLNAME_VOLUME."=\"".$header_str_array[$header_num+2]."\" ".
                           self::ATTRIBUTE_COLNAME_ISSUE."=\"".$header_str_array[$header_num+3]."\" ".
                           self::ATTRIBUTE_COLNAME_STARTPAGE."=\"".$header_str_array[$header_num+4]."\" ".
                           self::ATTRIBUTE_COLNAME_ENDPAGE."=\"".$header_str_array[$header_num+5]."\" ".
                           self::ATTRIBUTE_COLNAME_DATEOFISSUED."=\"".$header_str_array[$header_num+6]."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n".
                       "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        $header_num += 7;
    }
    
    /**
     * add xml node for 'link'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     */
    private function addLinkXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_COLNAME_LINKNAME."=\"".$header_str_array[$header_num]."\" ".
                           self::ATTRIBUTE_COLNAME_LINKURL."=\"".$header_str_array[$header_num+1]."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n".
                       "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        $header_num += 2;
    }
    
    /**
     * add xml node for 'file'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     */
    private function addFileXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_DISPLAYTYPE."=\"detail\" ".
                           self::ATTRIBUTE_COLNAME_FILENAME."=\"".$header_str_array[$header_num]."\" ".
                           self::ATTRIBUTE_COLNAME_DISPLAYNAME."=\"".$header_str_array[$header_num+1]."\" ".
                           self::ATTRIBUTE_COLNAME_PUBDATE."=\"".$header_str_array[$header_num+2]."\" ".
                           self::ATTRIBUTE_COLNAME_FLASH_PUBDATE."=\"".$header_str_array[$header_num+3]."\" ".
                           self::ATTRIBUTE_COLNAME_LICENSE_CC."=\"".$header_str_array[$header_num+4]."\" ".
                           self::ATTRIBUTE_COLNAME_LICENSE_FREE."=\"".$header_str_array[$header_num+5]."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n".
                       "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        $header_num += 6;
    }
    
    /**
     * add xml node for 'file_price'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     */
    private function addFilePriceXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_DISPLAYTYPE."=\"detail\" ".
                           self::ATTRIBUTE_COLNAME_FILENAME."=\"".$header_str_array[$header_num]."\" ".
                           self::ATTRIBUTE_COLNAME_DISPLAYNAME."=\"".$header_str_array[$header_num+1]."\" ".
                           self::ATTRIBUTE_COLNAME_PUBDATE."=\"".$header_str_array[$header_num+2]."\" ".
                           self::ATTRIBUTE_COLNAME_FLASH_PUBDATE."=\"".$header_str_array[$header_num+3]."\" ".
                           self::ATTRIBUTE_COLNAME_LICENSE_CC."=\"".$header_str_array[$header_num+4]."\" ".
                           self::ATTRIBUTE_COLNAME_LICENSE_FREE."=\"".$header_str_array[$header_num+5]."\" ".
                           self::ATTRIBUTE_COLNAME_ACCOUNTING_NONSUBSCRIBER."=\"".$header_str_array[$header_num+6]."\" ".
                           self::ATTRIBUTE_COLNAME_ACCOUNTING."=\"".$header_str_array[$header_num+7]."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n".
                       "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        $header_num += 8;
    }
    
    /**
     * add xml node for 'thumbnail'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     */
    private function addThumbnailXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_COLNAME_FILENAME."=\"".$header_str_array[$header_num]."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n".
                       "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        $header_num++;
    }
    
    /**
     * add xml node for 'heading'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     */
    private function addHeadingXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_COLNAME_HEADINGJP."=\"".$header_str_array[$header_num]."\" ".
                           self::ATTRIBUTE_COLNAME_HEADINGEN."=\"".$header_str_array[$header_num+1]."\" ".
                           self::ATTRIBUTE_COLNAME_HEADINGSUBJP."=\"".$header_str_array[$header_num+2]."\" ".
                           self::ATTRIBUTE_COLNAME_HEADINGSUBEN."=\"".$header_str_array[$header_num+3]."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n".
                       "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        $header_num += 4;
    }
    
    /**
     * add xml node for 'checkbox' and 'radiobutton' and 'pulldownmenu'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     * @param array $options_array Option list オプション一覧
     * @param int $attribute_id Attribute id 属性ID
     */
    private function addCandidateXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml, $options_array, $attribute_id)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_COLNAME_VALUE."=\"".$header_str_array[$header_num]."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n";
        if(isset($options_array[$attribute_id]) && strlen($options_array[$attribute_id]) > 0)
        {
            $update_xml .=  "<".self::TAG_ADD_ATTR_CANDIDATES.">".
                            $options_array[$attribute_id].
                            "</".self::TAG_ADD_ATTR_CANDIDATES.">\n";
        }
        $update_xml .= "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        $header_num++;
    }
    
    /**
     * add xml node for 'supplementalcontents'
     * XMLノード追加
     *
     * @param string $attribute_name Attribute name 属性名
     * @param string $common_attr_str Common attribute string 共通属性文字列
     * @param array $header_str_array Header string list ヘッダ名一覧
     *                                array[$ii]
     * @param int $header_num Hearder number ヘッダ数
     * @param string $update_xml XML string XML文字列
     * @return boolean Result 結果
     */
    private function addSuppleXmlNode($attribute_name, $common_attr_str, $header_str_array, &$header_num, &$update_xml)
    {
        $update_xml .= "<".self::TAG_ADDITIONALATTRIBUTE." ".
                           self::ATTRIBUTE_COLNAME_VALUE."=\"".$header_str_array[$header_num]."\" ".
                           $common_attr_str.">\n".
                       "<".self::TAG_ADD_ATTR_NAME.">".
                       $attribute_name.
                       "</".self::TAG_ADD_ATTR_NAME.">\n".
                       "</".self::TAG_ADDITIONALATTRIBUTE.">\n";
        $header_num++;
        return true;
    }
    
    /**
     * call superclass' __construct
     * コンストラクタ
     *
     * @param Session $session Session セッション管理オブジェクト
     * @param DbObject $db Database object データベース管理オブジェクト
     * @param string $transStartDate Transaction start date トランザクション開始日
     */
    public function __construct($session, $db, $transStartDate)
    {
        parent::__construct($session, $db, $transStartDate);
    }
    
    /**
     * bool to string
     * Booleanをstringに変換
     *
     * @param boolean $bool Input 入力値
     * @return string Output string 出力文字列
     */
    private function boolToString($bool)
    {
        if($bool)
        {
            return 'true';
        }
        else
        {
            return 'false';
        }
    }
}

?>
