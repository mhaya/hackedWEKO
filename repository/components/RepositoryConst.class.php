<?php

/**
 * Repository module constant class
 * WEKO共通定数クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryConst.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Repository module constant class
 * WEKO共通定数クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class RepositoryConst
{
    // -------------------------------------------
    // DB table name
    // -------------------------------------------
    /**
     * dbtable repository attached file
     * repository_attached_file
     * @var string 
     */
    const DBTABLE_REPOSITORY_ATTACHED_FILE = "repository_attached_file";
    /**
     * dbtable repository biblio info
     * repository_biblio_info
     * @var string 
     */
    const DBTABLE_REPOSITORY_BIBLIO_INFO = "repository_biblio_info";
    /**
     * dbtable repository element cd
     * repository_element_cd
     * @var string 
     */
    const DBTABLE_REPOSITORY_ELEMENT_CD = "repository_element_cd";
    /**
     * dbtable repository external author id prefix
     * repository_external_author_id_prefix
     * @var string 
     */
    const DBTABLE_REPOSITORY_EXTERNAL_AUTHOR_ID_PREFIX = "repository_external_author_id_prefix";
    /**
     * dbtable repository external author id suffix
     * repository_external_author_id_suffix
     * @var string 
     */
    const DBTABLE_REPOSITORY_EXTERNAL_AUTHOR_ID_SUFFIX = "repository_external_author_id_suffix";
    /**
     * dbtable repository file
     * repository_file
     * @var string 
     */
    const DBTABLE_REPOSITORY_FILE = "repository_file";
    /**
     * dbtable repository file price
     * repository_file_price
     * @var string 
     */
    const DBTABLE_REPOSITORY_FILE_PRICE = "repository_file_price";
    /**
     * dbtable repository fulltext data
     * repository_fulltext_data
     * @var string 
     */
    const DBTABLE_REPOSITORY_FULLTEXT_DATA = "repository_fulltext_data";
    /**
     * dbtable repository index
     * repository_index
     * @var string 
     */
    const DBTABLE_REPOSITORY_INDEX = "repository_index";
    /**
     * dbtable repository item
     * repository_item
     * @var string 
     */
    const DBTABLE_REPOSITORY_ITEM = "repository_item";
    /**
     * dbtable repository item attr
     * repository_item_attr
     * @var string 
     */
    const DBTABLE_REPOSITORY_ITEM_ATTR = "repository_item_attr";
    /**
     * dbtable repository item attr candidate
     * repository_item_attr_candidate
     * @var string 
     */
    const DBTABLE_REPOSITORY_ITEM_ATTR_CANDIDATE = "repository_item_attr_candidate";
    /**
     * dbtable repository item attr type
     * repository_item_attr_type
     * @var string 
     */
    const DBTABLE_REPOSITORY_ITEM_ATTR_TYPE = "repository_item_attr_type";
    /**
     * dbtable repository item type
     * repository_item_type
     * @var string 
     */
    const DBTABLE_REPOSITORY_ITEM_TYPE = "repository_item_type";
    /**
     * dbtable repository license master
     * repository_license_master
     * @var string 
     */
    const DBTABLE_REPOSITORY_LICENSE_MASTER = "repository_license_master";
    /**
     * dbtable repository log
     * repository_log
     * @var string 
     */
    const DBTABLE_REPOSITORY_LOG = "repository_log";
    /**
     * dbtable repository name authority
     * repository_name_authority
     * @var string 
     */
    const DBTABLE_REPOSITORY_NAME_AUTHORITY = "repository_name_authority";
    /**
     * dbtable repository parameter
     * repository_parameter
     * @var string 
     */
    const DBTABLE_REPOSITORY_PARAMETER = "repository_parameter";
    /**
     * dbtable repository personal name
     * repository_personal_name
     * @var string 
     */
    const DBTABLE_REPOSITORY_PERSONAL_NAME = "repository_personal_name";
    /**
     * dbtable repository position index
     * repository_position_index
     * @var string 
     */
    const DBTABLE_REPOSITORY_POSITION_INDEX = "repository_position_index";
    /**
     * dbtable repository ranking
     * repository_ranking
     * @var string 
     */
    const DBTABLE_REPOSITORY_RANKING = "repository_ranking";
    /**
     * dbtable repository reference
     * repository_reference
     * @var string 
     */
    const DBTABLE_REPOSITORY_REFERENCE = "repository_reference";
    /**
     * dbtable repository supple
     * repository_supple
     * @var string 
     */
    const DBTABLE_REPOSITORY_SUPPLE = "repository_supple";
    /**
     * dbtable repository thumbnail
     * repository_thumbnail
     * @var string 
     */
    const DBTABLE_REPOSITORY_THUMBNAIL = "repository_thumbnail";
    /**
     * dbtable repository users
     * repository_users
     * @var string 
     */
    const DBTABLE_REPOSITORY_USERS = "repository_users";
    /**
     * dbtable repository harvesting log
     * repository_harvesting_log
     * @var string 
     */
    const DBTABLE_REPOSITORY_HARVESTING_LOG = 'repository_harvesting_log';
    /**
     * dbtable repository pdf cover parameter
     * repository_pdf_cover_parameter
     * @var string 
     */
    const DBTABLE_REPOSITORY_PDF_COVER_PARAMETER = 'repository_pdf_cover_parameter';
    /**
     * dbtable repository usagestatistics
     * repository_usagestatistics
     * @var string 
     */
    const DBTABLE_REPOSITORY_USAGESTATISTICS = 'repository_usagestatistics';
    /**
     * dbtable repository send feedbackmail author id
     * repository_send_feedbackmail_author_id
     * @var string 
     */
    const DBTABLE_REPOSITORY_SEND_FEEDBACKMAIL_AUTHOR_ID = 'repository_send_feedbackmail_author_id';
    /**
     * dbtable authorities
     * authorities
     * @var string 
     */
    const DBTABLE_AUTHORITIES = "authorities";
    
    // -------------------------------------------
    // DB table column
    // -------------------------------------------
    // Common column name
    /**
     * dbcol common ins user id
     * ins_user_id
     * @var string 
     */
    const DBCOL_COMMON_INS_USER_ID = "ins_user_id";
    /**
     * dbcol common mod user id
     * mod_user_id
     * @var string 
     */
    const DBCOL_COMMON_MOD_USER_ID = "mod_user_id";
    /**
     * dbcol common del user id
     * del_user_id
     * @var string 
     */
    const DBCOL_COMMON_DEL_USER_ID = "del_user_id";
    /**
     * dbcol common ins date
     * ins_date
     * @var string 
     */
    const DBCOL_COMMON_INS_DATE = "ins_date";
    /**
     * dbcol common mod date
     * mod_date
     * @var string 
     */
    const DBCOL_COMMON_MOD_DATE = "mod_date";
    /**
     * dbcol common del date
     * del_date
     * @var string 
     */
    const DBCOL_COMMON_DEL_DATE = "del_date";
    /**
     * dbcol common is delete
     * is_delete
     * @var string 
     */
    const DBCOL_COMMON_IS_DELETE = "is_delete";
    
    // repository_item
    /**
     * dbcol repository item item id
     * item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ITEM_ID = "item_id";
    /**
     * dbcol repository item item no
     * item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ITEM_NO = "item_no";
    /**
     * dbcol repository item revision no
     * revision_no
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_REVISION_NO = "revision_no";
    /**
     * dbcol repository item item type id
     * item_type_id
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ITEM_TYPE_ID = "item_type_id";
    /**
     * dbcol repository item prev revision no
     * prev_revision_no
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_PREV_REVISION_NO = "prev_revision_no";
    /**
     * dbcol repository item title
     * title
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TITLE = "title";
    /**
     * dbcol repository item title english
     * title_english
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TITLE_ENGLISH = "title_english";
    /**
     * dbcol repository item language
     * language
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_LANGUAGE = "language";
    /**
     * dbcol repository item review status
     * review_status
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_REVIEW_STATUS = "review_status";
    /**
     * dbcol repository item review date
     * review_date
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_REVIEW_DATE = "review_date";
    /**
     * dbcol repository item shown status
     * shown_status
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_SHOWN_STATUS = "shown_status";
    /**
     * dbcol repository item shown date
     * shown_date
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_SHOWN_DATE = "shown_date";
    /**
     * dbcol repository item reject status
     * reject_status
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_REJECT_STATUS = "reject_status";
    /**
     * dbcol repository item reject date
     * reject_date
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_REJECT_DATE = "reject_date";
    /**
     * dbcol repository item reject reason
     * reject_reason
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_REJECT_REASON = "reject_reason";
    /**
     * dbcol repository item search key
     * serch_key
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_SEARCH_KEY = "serch_key";
    /**
     * dbcol repository item search key english
     * serch_key_english
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_SEARCH_KEY_ENGLISH = "serch_key_english";
    /**
     * dbcol repository item remark
     * remark
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_REMARK = "remark";
    /**
     * dbcol repository item uri
     * uri
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_URI = "uri";
    
    // repository_item_type
    /**
     * dbcol repository item type id
     * item_type_id
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TYPE_ID = "item_type_id";
    /**
     * dbcol repository item type name
     * item_type_name
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TYPE_NAME = "item_type_name";
    /**
     * dbcol repository item type short name
     * item_type_short_name
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TYPE_SHORT_NAME = "item_type_short_name";
    /**
     * dbcol repository item type explanation
     * explanation
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TYPE_EXPLANATION = "explanation";
    /**
     * dbcol repository item type mapping info
     * mapping_info
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TYPE_MAPPING_INFO = "mapping_info";
    /**
     * dbcol repository item type icon name
     * icon_name
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TYPE_ICON_NAME = "icon_name";
    /**
     * dbcol repository item type icon mime type
     * icon_mime_type
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TYPE_ICON_MIME_TYPE = "icon_mime_type";
    /**
     * dbcol repository item type icon extension
     * icon_extension
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TYPE_ICON_EXTENSION = "icon_extension";
    /**
     * dbcol repository item type icon
     * icon
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_TYPE_ICON = "icon";
    /**
     * dbcol repository item attr type item type id
     * item_type_id
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ITEM_TYPE_ID = "item_type_id";
    /**
     * dbcol repository item attr type attribute id
     * attribute_id
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_ID = "attribute_id";
    /**
     * dbcol repository item attr type show order
     * show_order
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_SHOW_ORDER = "show_order";
    /**
     * dbcol repository item attr type attribute name
     * attribute_name
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_NAME = "attribute_name";
    /**
     * dbcol repository item attr type attribute short name
     * attribute_short_name
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_SHORT_NAME = "attribute_short_name";
    /**
     * dbcol repository item attr type imput type
     * input_type
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IMPUT_TYPE = "input_type";
    /**
     * dbcol repository item attr type is required
     * is_required
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IS_REQUIRED = "is_required";
    /**
     * dbcol repository item attr type plural enable
     * plural_enable
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_PLURAL_ENABLE = "plural_enable";
    /**
     * dbcol repository item attr type line feed enable
     * line_feed_enable
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LINE_FEED_ENABLE = "line_feed_enable";
    /**
     * dbcol repository item attr type list view enable
     * list_view_enable
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LIST_VIEW_ENABLE = "list_view_enable";
    /**
     * dbcol repository item attr type hidden
     * hidden
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_HIDDEN = "hidden";
    /**
     * dbcol repository item attr type junii2 mapping
     * junii2_mapping
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_JUNII2_MAPPING = "junii2_mapping";
    /**
     * dbcol repository item attr type lom mapping
     * lom_mapping
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_LOM_MAPPING = "lom_mapping";
    /**
     * dbcol repository item attr type doblin core mapping
     * dublin_core_mapping
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DOBLIN_CORE_MAPPING = "dublin_core_mapping";
    /**
     * dbcol repository item attr type display lang type
     * display_lang_type
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE = "display_lang_type";
    /**
     * dbcol repository item attr item id
     * item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_ITEM_ID = "item_id";
    /**
     * dbcol repository item attr item no
     * item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_ITEM_NO = "item_no";
    /**
     * dbcol repository item attr attribute id
     * attribute_id
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_ID = "attribute_id";
    /**
     * dbcol repository item attr attribute no
     * attribute_no
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_NO = "attribute_no";
    /**
     * dbcol repository item attr attribute value
     * attribute_value
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_VALUE = "attribute_value";
    /**
     * dbcol repository item attr item type id
     * item_type_id
     * @var string 
     */
    const DBCOL_REPOSITORY_ITEM_ATTR_ITEM_TYPE_ID = "item_type_id";
    /**
     * dbcol repository personal name item id
     * item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_ID = "item_id";
    /**
     * dbcol repository personal name item no
     * item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_NO = "item_no";
    /**
     * dbcol repository personal name attribute id
     * attribute_id
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_ATTRIBUTE_ID = "attribute_id";
    /**
     * dbcol repository personal name personal name no
     * personal_name_no
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_PERSONAL_NAME_NO = "personal_name_no";
    /**
     * dbcol repository personal name family
     * family
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_FAMILY = "family";
    /**
     * dbcol repository personal name name
     * name
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_NAME = "name";
    /**
     * dbcol repository personal name family ruby
     * family_ruby
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_FAMILY_RUBY = "family_ruby";
    /**
     * dbcol repository personal name name ruby
     * name_ruby
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_NAME_RUBY = "name_ruby";
    /**
     * dbcol repository personal name e mail address
     * e_mail_address
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_E_MAIL_ADDRESS = "e_mail_address";
    /**
     * dbcol repository personal name item type id
     * item_type_id
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_TYPE_ID = "item_type_id";
    /**
     * dbcol repository personal name author id
     * author_id
     * @var string 
     */
    const DBCOL_REPOSITORY_PERSONAL_NAME_AUTHOR_ID = "author_id";
    /**
     * dbcol repository biblio info item id
     * item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_ITEM_ID = "item_id";
    /**
     * dbcol repository biblio info item no
     * item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_ITEM_NO = "item_no";
    /**
     * dbcol repository biblio info attribute id
     * attribute_id
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_ATTRIBUTE_ID = "attribute_id";
    /**
     * dbcol repository biblio info biblio no
     * biblio_no
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_BIBLIO_NO = "biblio_no";
    /**
     * dbcol repository biblio info biblio name
     * biblio_name
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_BIBLIO_NAME = "biblio_name";
    /**
     * dbcol repository biblio info biblio name english
     * biblio_name_english
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_BIBLIO_NAME_ENGLISH = "biblio_name_english";
    /**
     * dbcol repository biblio info volume
     * volume
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_VOLUME = "volume";
    /**
     * dbcol repository biblio info issue
     * issue
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_ISSUE = "issue";
    /**
     * dbcol repository biblio info start page
     * start_page
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_START_PAGE = "start_page";
    /**
     * dbcol repository biblio info end page
     * end_page
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_END_PAGE = "end_page";
    /**
     * dbcol repository biblio info date of issued
     * date_of_issued
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_DATE_OF_ISSUED = "date_of_issued";
    /**
     * dbcol repository biblio info item type id
     * item_type_id
     * @var string 
     */
    const DBCOL_REPOSITORY_BIBLIO_INFO_ITEM_TYPE_ID = "item_type_id";
    /**
     * dbcol repository harvesting repository id
     * repository_id
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_REPOSITORY_ID = "repository_id";
    /**
     * dbcol repository harvesting repository name
     * repository_name
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_REPOSITORY_NAME = "repository_name";
    /**
     * dbcol repository harvesting base url
     * base_url
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_BASE_URL = "base_url";
    /**
     * dbcol repository harvesting from date
     * from_date
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_FROM_DATE = "from_date";
    /**
     * dbcol repository harvesting until date
     * until_date
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_UNTIL_DATE = "until_date";
    /**
     * dbcol repository harvesting set param
     * set_param
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_SET_PARAM = "set_param";
    /**
     * dbcol repository harvesting metadata prefix
     * metadata_prefix
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_METADATA_PREFIX = "metadata_prefix";
    /**
     * dbcol repository harvesting post index id
     * post_index_id
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_POST_INDEX_ID = "post_index_id";
    /**
     * dbcol repository harvesting automatic sorting
     * automatic_sorting
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_AUTOMATIC_SORTING = "automatic_sorting";
    /**
     * dbcol repository harvesting execution date
     * execution_date
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_EXECUTION_DATE = "execution_date";
    /**
     * dbcol repository harvesting log log id
     * log_id
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_LOG_ID = "log_id";
    /**
     * dbcol repository harvesting log repository id
     * repository_id
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_REPOSITORY_ID = "repository_id";
    /**
     * dbcol repository harvesting log operation id
     * oparation_id
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_OPERATION_ID = "oparation_id";
    /**
     * dbcol repository harvesting log metadata prefix
     * metadata_prefix
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_METADATA_PREFIX = "metadata_prefix";
    /**
     * dbcol repository harvesting log list sets
     * list_sets
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_LIST_SETS = "list_sets";
    /**
     * dbcol repository harvesting log set spec
     * set_spec
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_SET_SPEC = "set_spec";
    /**
     * dbcol repository harvesting log index id
     * index_id
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_INDEX_ID = "index_id";
    /**
     * dbcol repository harvesting log identifier
     * identifier
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_IDENTIFIER = "identifier";
    /**
     * dbcol repository harvesting log item id
     * item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_ITEM_ID = "item_id";
    /**
     * dbcol repository harvesting log uri
     * uri
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_URI = "uri";
    /**
     * dbcol repository harvesting log status
     * status
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_STATUS = "status";
    /**
     * dbcol repository harvesting log update
     * `update`
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_UPDATE = "`update`";
    /**
     * dbcol repository harvesting log error msg
     * error_msg
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_ERROR_MSG = "error_msg";
    /**
     * dbcol repository harvesting log response date
     * response_date
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_RESPONSE_DATE = "response_date";
    /**
     * dbcol repository harvesting log last mod date
     * last_mod_date
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_LAST_MOD_DATE = "last_mod_date";
    /**
     * dbcol repository harvesting log ins user id
     * ins_user_id
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_INS_USER_ID = "ins_user_id";
    /**
     * dbcol repository harvesting log ins date
     * ins_date
     * @var string 
     */
    const DBCOL_REPOSITORY_HARVESTING_LOG_INS_DATE = "ins_date";
    /**
     * dbcol repository index index id
     * index_id
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_INDEX_ID = "index_id";
    /**
     * dbcol repository index index name
     * index_name
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_INDEX_NAME = "index_name";
    /**
     * dbcol repository index index name english
     * index_name_english
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_INDEX_NAME_ENGLISH = "index_name_english";
    /**
     * dbcol repository index parent index id
     * parent_index_id
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_PARENT_INDEX_ID = "parent_index_id";
    /**
     * dbcol repository index contents
     * contents
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_CONTENTS = "contents";
    /**
     * dbcol repository index show order
     * show_order
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_SHOW_ORDER = "show_order";
    /**
     * dbcol repository index public state
     * public_state
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_PUBLIC_STATE = "public_state";
    /**
     * dbcol repository index pub date
     * pub_date
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_PUB_DATE = "pub_date";
    /**
     * dbcol repository index access role
     * access_role
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_ACCESS_ROLE = "access_role";
    /**
     * dbcol repository index access group
     * access_group
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_ACCESS_GROUP = "access_group";
    /**
     * dbcol repository index exclisive acl role
     * exclusive_acl_role
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_EXCLISIVE_ACL_ROLE = "exclusive_acl_role";
    /**
     * dbcol repository index exclisive acl group
     * exclusive_acl_group
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_EXCLISIVE_ACL_GROUP = "exclusive_acl_group";
    /**
     * dbcol repository index comment
     * comment
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_COMMENT = "comment";
    /**
     * dbcol repository index display more
     * display_more
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_DISPLAY_MORE = "display_more";
    /**
     * dbcol repository index display type
     * display_type
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_DISPLAY_TYPE = "display_type";
    /**
     * dbcol repository index rss display
     * rss_display
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_RSS_DISPLAY = "rss_display";
    /**
     * dbcol repository index select index list display
     * select_index_list_display
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_SELECT_INDEX_LIST_DISPLAY = "select_index_list_display";
    /**
     * dbcol repository index select index list name
     * select_index_list_name
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_SELECT_INDEX_LIST_NAME = "select_index_list_name";
    /**
     * dbcol repository index select index list name english
     * select_index_list_name_english
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_SELECT_INDEX_LIST_NAME_ENGLISH = "select_index_list_name_english";
    /**
     * dbcol repository index thumbnail
     * thumbnail
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_THUMBNAIL = "thumbnail";
    /**
     * dbcol repository index thumbnail name
     * thumbnail_name
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_THUMBNAIL_NAME = "thumbnail_name";
    /**
     * dbcol repository index thumbnail mame type
     * thumbnail_mime_type
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_THUMBNAIL_MAME_TYPE = "thumbnail_mime_type";
    /**
     * dbcol repository index repository id
     * repository_id
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_REPOSITORY_ID = "repository_id";
    /**
     * dbcol repository index set spec
     * set_spec
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_SET_SPEC = "set_spec";
    /**
     * dbcol repository index create cover flag
     * create_cover_flag
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_CREATE_COVER_FLAG = "create_cover_flag";
    /**
     * dbcol repository index owner user id
     * owner_user_id
     * @var string 
     */
    const DBCOL_REPOSITORY_INDEX_OWNER_USER_ID = "owner_user_id";
    /**
     * dbcol repository pdf cover parameter param name
     * param_name
     * @var string 
     */
    const DBCOL_REPOSITORY_PDF_COVER_PARAMETER_PARAM_NAME = "param_name";
    /**
     * dbcol repository pdf cover parameter text
     * text
     * @var string 
     */
    const DBCOL_REPOSITORY_PDF_COVER_PARAMETER_TEXT = "text";
    /**
     * dbcol repository pdf cover parameter image
     * image
     * @var string 
     */
    const DBCOL_REPOSITORY_PDF_COVER_PARAMETER_IMAGE = "image";
    /**
     * dbcol repository pdf cover parameter extension
     * extension
     * @var string 
     */
    const DBCOL_REPOSITORY_PDF_COVER_PARAMETER_EXTENSION = "extension";
    /**
     * dbcol repository pdf cover parameter mimetype
     * mimetype
     * @var string 
     */
    const DBCOL_REPOSITORY_PDF_COVER_PARAMETER_MIMETYPE = "mimetype";
    /**
     * dbcol repository thumb item id
     * item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_THUMB_ITEM_ID = "item_id";
    /**
     * dbcol repository thumb item no
     * item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_THUMB_ITEM_NO = "item_no";
    /**
     * dbcol repository thumb attr id
     * attribute_id
     * @var string 
     */
    const DBCOL_REPOSITORY_THUMB_ATTR_ID = "attribute_id";
    /**
     * dbcol repository thumb file no
     * file_no
     * @var string 
     */
    const DBCOL_REPOSITORY_THUMB_FILE_NO = "file_no";
    /**
     * dbcol repository thumb file name
     * file_name
     * @var string 
     */
    const DBCOL_REPOSITORY_THUMB_FILE_NAME = "file_name";
    /**
     * dbcol repository thumb mime type
     * mime_type
     * @var string 
     */
    const DBCOL_REPOSITORY_THUMB_MIME_TYPE = "mime_type";
    /**
     * dbcol repository thumb extension
     * extension
     * @var string 
     */
    const DBCOL_REPOSITORY_THUMB_EXTENSION = "extension";
    /**
     * dbcol repository thumb file
     * file
     * @var string 
     */
    const DBCOL_REPOSITORY_THUMB_FILE = "file";
    /**
     * dbcol repository thumb item type id
     * item_type_id
     * @var string 
     */
    const DBCOL_REPOSITORY_THUMB_ITEM_TYPE_ID = "item_type_id";
    /**
     * dbcol repository file item id
     * item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_ITEM_ID = "item_id";
    /**
     * dbcol repository file item no
     * item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_ITEM_NO = "item_no";
    /**
     * dbcol repository file attribute id
     * attribute_id
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_ATTRIBUTE_ID = "attribute_id";
    /**
     * dbcol repository file file no
     * file_no
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_FILE_NO = "file_no";
    /**
     * dbcol repository file file name
     * file_name
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_FILE_NAME = "file_name";
    /**
     * dbcol repository file display name
     * display_name
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_DISPLAY_NAME = "display_name";
    /**
     * dbcol repository file display type
     * display_type
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_DISPLAY_TYPE = "display_type";
    /**
     * dbcol repository file mime type
     * mime_type
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_MIME_TYPE = "mime_type";
    /**
     * dbcol repository file extension
     * extension
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_EXTENSION = "extension";
    /**
     * dbcol repository file prev id
     * prev_id
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_PREV_ID = "prev_id";
    /**
     * dbcol repository file file prev
     * file_prev
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_FILE_PREV = "file_prev";
    /**
     * dbcol repository file file prev name
     * file_prev_name
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_FILE_PREV_NAME = "file_prev_name";
    /**
     * dbcol repository file license id
     * license_id
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_LICENSE_ID = "license_id";
    /**
     * dbcol repository file license notation
     * license_notation
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_LICENSE_NOTATION = "license_notation";
    /**
     * dbcol repository file pub date
     * pub_date
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_PUB_DATE = "pub_date";
    /**
     * dbcol repository file flash pub date
     * flash_pub_date
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_FLASH_PUB_DATE = "flash_pub_date";
    /**
     * dbcol repository file item type id
     * item_type_id
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_ITEM_TYPE_ID = "item_type_id";
    /**
     * dbcol repository file browsing flag
     * browsing_flag
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_BROWSING_FLAG = "browsing_flag";
    /**
     * dbcol repository file cover created flag
     * cover_created_flag
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_COVER_CREATED_FLAG = "cover_created_flag";
    /**
     * dbcol repository file price item id
     * item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_PRICE_ITEM_ID = "item_id";
    /**
     * dbcol repository file price item no
     * item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_PRICE_ITEM_NO = "item_no";
    /**
     * dbcol repository file price attribute id
     * attribute_id
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_PRICE_ATTRIBUTE_ID = "attribute_id";
    /**
     * dbcol repository file price file no
     * file_no
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_PRICE_FILE_NO = "file_no";
    /**
     * dbcol repository file price price
     * price
     * @var string 
     */
    const DBCOL_REPOSITORY_FILE_PRICE_PRICE = "price";
    /**
     * dbcol repository license mastaer license id
     * license_id
     * @var string 
     */
    const DBCOL_REPOSITORY_LICENSE_MASTAER_LICENSE_ID = "license_id";
    /**
     * dbcol repository license mastaer license notation
     * license_notation
     * @var string 
     */
    const DBCOL_REPOSITORY_LICENSE_MASTAER_LICENSE_NOTATION = "license_notation";
    /**
     * dbcol repository license mastaer img url
     * img_url
     * @var string 
     */
    const DBCOL_REPOSITORY_LICENSE_MASTAER_IMG_URL = "img_url";
    /**
     * dbcol repository license mastaer text url
     * text_url
     * @var string 
     */
    const DBCOL_REPOSITORY_LICENSE_MASTAER_TEXT_URL = "text_url";
    /**
     * dbcol repository log log no
     * log_no
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_LOG_NO = "log_no";
    /**
     * dbcol repository log record date
     * record_date
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_RECORD_DATE = "record_date";
    /**
     * dbcol repository log user id
     * user_id
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_USER_ID = "user_id";
    /**
     * dbcol repository log operation id
     * operation_id
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_OPERATION_ID = "operation_id";
    /**
     * dbcol repository log item id
     * item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_ITEM_ID = "item_id";
    /**
     * dbcol repository log item no
     * item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_ITEM_NO = "item_no";
    /**
     * dbcol repository log attribute id
     * attribute_id
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_ATTRIBUTE_ID = "attribute_id";
    /**
     * dbcol repository log file no
     * file_no
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_FILE_NO = "file_no";
    /**
     * dbcol repository log search keyword
     * search_keyword
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_SEARCH_KEYWORD = "search_keyword";
    /**
     * dbcol repository log ip address
     * ip_address
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_IP_ADDRESS = "ip_address";
    /**
     * dbcol repository log host
     * host
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_HOST = "host";
    /**
     * dbcol repository log user agent
     * user_agent
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_USER_AGENT = "user_agent";
    /**
     * dbcol repository log file status
     * file_status
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_FILE_STATUS = "file_status";
    /**
     * dbcol repository log site license
     * site_license
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_SITE_LICENSE = "site_license";
    /**
     * dbcol repository log input type
     * input_type
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_INPUT_TYPE = "input_type";
    /**
     * dbcol repository log login status
     * login_status
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_LOGIN_STATUS = "login_status";
    /**
     * dbcol repository log group id
     * group_id
     * @var string 
     */
    const DBCOL_REPOSITORY_LOG_GROUP_ID = "group_id";
    /**
     * dbcol repository usagestatistics record date
     * record_date
     * @var string 
     */
    const DBCOL_REPOSITORY_USAGESTATISTICS_RECORD_DATE = "record_date";
    /**
     * dbcol repository usagestatistics item id
     * item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_USAGESTATISTICS_ITEM_ID = "item_id";
    /**
     * dbcol repository usagestatistics item no
     * item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_USAGESTATISTICS_ITEM_NO = "item_no";
    /**
     * dbcol repository usagestatistics attribute id
     * attribute_id
     * @var string 
     */
    const DBCOL_REPOSITORY_USAGESTATISTICS_ATTRIBUTE_ID = "attribute_id";
    /**
     * dbcol repository usagestatistics file no
     * file_no
     * @var string 
     */
    const DBCOL_REPOSITORY_USAGESTATISTICS_FILE_NO = "file_no";
    /**
     * dbcol repository usagestatistics operation id
     * operation_id
     * @var string 
     */
    const DBCOL_REPOSITORY_USAGESTATISTICS_OPERATION_ID = "operation_id";
    /**
     * dbcol repository usagestatistics domain
     * domain
     * @var string 
     */
    const DBCOL_REPOSITORY_USAGESTATISTICS_DOMAIN = "domain";
    /**
     * dbcol repository usagestatistics cnt
     * cnt
     * @var string 
     */
    const DBCOL_REPOSITORY_USAGESTATISTICS_CNT = "cnt";
    /**
     * dbcol repository parameter param name
     * param_name
     * @var string 
     */
    const DBCOL_REPOSITORY_PARAMETER_PARAM_NAME = "param_name";
    /**
     * dbcol repository parameter param value
     * param_value
     * @var string 
     */
    const DBCOL_REPOSITORY_PARAMETER_PARAM_VALUE = "param_value";
    /**
     * dbcol repository parameter explanation
     * explanation
     * @var string 
     */
    const DBCOL_REPOSITORY_PARAMETER_EXPLANATION = "explanation";
    /**
     * dbcol repository ref org item id
     * org_reference_item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_REF_ORG_ITEM_ID = "org_reference_item_id";
    /**
     * dbcol repository ref org item no
     * org_reference_item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_REF_ORG_ITEM_NO = "org_reference_item_no";
    /**
     * dbcol repository ref dest item id
     * dest_reference_item_id
     * @var string 
     */
    const DBCOL_REPOSITORY_REF_DEST_ITEM_ID = "dest_reference_item_id";
    /**
     * dbcol repository ref dest item no
     * dest_reference_item_no
     * @var string 
     */
    const DBCOL_REPOSITORY_REF_DEST_ITEM_NO = "dest_reference_item_no";
    /**
     * dbcol repository ref reference
     * reference
     * @var string 
     */
    const DBCOL_REPOSITORY_REF_REFERENCE = "reference";
    /**
     * dbcol authorities role authority id
     * role_authority_id
     * @var string 
     */
    const DBCOL_AUTHORITIES_ROLE_AUTHORITY_ID = "role_authority_id";
    /**
     * dbcol external author id prefix prefix id
     * prefix_id
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_PREFIX_ID = "prefix_id";
    /**
     * dbcol external author id prefix prefix name
     * prefix_name
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_PREFIX_NAME = "prefix_name";
    /**
     * dbcol external author id prefix block id
     * block_id
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_BLOCK_ID = "block_id";
    /**
     * dbcol external author id prefix room id
     * room_id
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_ROOM_ID = "room_id";
    /**
     * dbcol external author id prefix ins user id
     * ins_user_id
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_INS_USER_ID = "ins_user_id";
    /**
     * dbcol external author id prefix mod user id
     * mod_user_id
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_MOD_USER_ID = "mod_user_id";
    /**
     * dbcol external author id prefix del user id
     * del_user_id
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_DEL_USER_ID = "del_user_id";
    /**
     * dbcol external author id prefix ins date
     * ins_date
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_INS_DATE = "ins_date";
    /**
     * dbcol external author id prefix mod date
     * mod_date
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_MOD_DATE = "mod_date";
    /**
     * dbcol external author id prefix deldate
     * del_date
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_DELDATE = "del_date";
    /**
     * dbcol external author id prefix is delete
     * is_delete
     * @var string 
     */
    const DBCOL_EXTERNAL_AUTHOR_ID_PREFIX_IS_DELETE = "is_delete";
    
    
    // repository_harvesting_logの各列に入る値
    /**
     * harvesting operation id repository
     * 進捗ファイル作成中
     * @var int 
     */
    const HARVESTING_OPERATION_ID_REPOSITORY = 1;
    /**
     * harvesting operation id listsets
     * ListSets実施中
     * @var int 
     */
    const HARVESTING_OPERATION_ID_LISTSETS = 2;
    /**
     * harvesting operation id listrecord
     * ListRecords実施中
     * @var int 
     */
    const HARVESTING_OPERATION_ID_LISTRECORD = 3;
    /**
     * harvesting log status ok
     * 問題無し状態
     * @var int 
     */
    const HARVESTING_LOG_STATUS_OK = 1;
    /**
     * harvesting log status warning
     * 警告が出ている状態
     * @var int 
     */
    const HARVESTING_LOG_STATUS_WARNING = 0;
    /**
     * harvesting log status error
     * エラーが出ている状態
     * @var int 
     */
    const HARVESTING_LOG_STATUS_ERROR = -1;
    /**
     * harvesting log update no update
     * 更新なし
     * @var int 
     */
    const HARVESTING_LOG_UPDATE_NO_UPDATE = 0;
    /**
     * harvesting log update insert
     * アイテム追加
     * @var int 
     */
    const HARVESTING_LOG_UPDATE_INSERT = 1;
    /**
     * harvesting log update update
     * アイテム更新
     * @var int 
     */
    const HARVESTING_LOG_UPDATE_UPDATE = 2;
    /**
     * harvesting log update delete
     * アイテム削除
     * @var int 
     */
    const HARVESTING_LOG_UPDATE_DELETE = 3;
    /**
     * harvesting dc add attr show order
     * Dublin Core用ハーベストアイテムタイプの追加メタデータ表示順序
     * @var int 
     */
    const HARVESTING_DC_ADD_ATTR_SHOW_ORDER = 18;
    /**
     * harvesting junii2 add attr show order
     * junii2用ハーベストアイテムタイプの追加メタデータ表示順序
     * @var int 
     */
    const HARVESTING_JUNII2_ADD_ATTR_SHOW_ORDER = 62;
    
    // -------------------------------------------
    // Item attribute type
    // -------------------------------------------
    /**
     * item attr type text
     * text
     * @var string 
     */
    const ITEM_ATTR_TYPE_TEXT = "text";
    /**
     * item attr type textarea
     * textarea
     * @var string 
     */
    const ITEM_ATTR_TYPE_TEXTAREA = "textarea";
    /**
     * item attr type link
     * link
     * @var string 
     */
    const ITEM_ATTR_TYPE_LINK = "link";
    /**
     * item attr type checkbox
     * checkbox
     * @var string 
     */
    const ITEM_ATTR_TYPE_CHECKBOX = "checkbox";
    /**
     * item attr type radio
     * radio
     * @var string 
     */
    const ITEM_ATTR_TYPE_RADIO = "radio";
    /**
     * item attr type select
     * select
     * @var string 
     */
    const ITEM_ATTR_TYPE_SELECT = "select";
    /**
     * item attr type name
     * name
     * @var string 
     */
    const ITEM_ATTR_TYPE_NAME = "name";
    /**
     * item attr type thumbnail
     * thumbnail
     * @var string 
     */
    const ITEM_ATTR_TYPE_THUMBNAIL = "thumbnail";
    /**
     * item attr type file
     * file
     * @var string 
     */
    const ITEM_ATTR_TYPE_FILE = "file";
    /**
     * item attr type biblioinfo
     * biblio_info
     * @var string 
     */
    const ITEM_ATTR_TYPE_BIBLIOINFO = "biblio_info";
    /**
     * item attr type date
     * date
     * @var string 
     */
    const ITEM_ATTR_TYPE_DATE = "date";
    /**
     * item attr type heading
     * heading
     * @var string 
     */
    const ITEM_ATTR_TYPE_HEADING = "heading";
    /**
     * item attr type supple
     * supple
     * @var string 
     */
    const ITEM_ATTR_TYPE_SUPPLE = "supple";
    /**
     * item attr type fileprice
     * file_price
     * @var string 
     */
    const ITEM_ATTR_TYPE_FILEPRICE = "file_price";
    
    // -------------------------------------------
    // NII Type
    // -------------------------------------------
    /**
     * niitype journal article
     * Journal Article
     * @var string 
     */
    const NIITYPE_JOURNAL_ARTICLE = "Journal Article";
    /**
     * niitype thesis or dissertation
     * Thesis or Dissertation
     * @var string 
     */
    const NIITYPE_THESIS_OR_DISSERTATION = "Thesis or Dissertation";
    /**
     * niitype departmental bulletin paper
     * Departmental Bulletin Paper
     * @var string 
     */
    const NIITYPE_DEPARTMENTAL_BULLETIN_PAPER = "Departmental Bulletin Paper";
    /**
     * niitype conference paper
     * Conference Paper
     * @var string 
     */
    const NIITYPE_CONFERENCE_PAPER = "Conference Paper";
    /**
     * niitype presentation
     * Presentation
     * @var string 
     */
    const NIITYPE_PRESENTATION = "Presentation";
    /**
     * niitype book
     * Book
     * @var string 
     */
    const NIITYPE_BOOK = "Book";
    /**
     * niitype technical report
     * Technical Report
     * @var string 
     */
    const NIITYPE_TECHNICAL_REPORT = "Technical Report";
    /**
     * niitype research paper
     * Research Paper
     * @var string 
     */
    const NIITYPE_RESEARCH_PAPER = "Research Paper";
    /**
     * niitype article
     * Article
     * @var string 
     */
    const NIITYPE_ARTICLE = "Article";
    /**
     * niitype preprint
     * Preprint
     * @var string 
     */
    const NIITYPE_PREPRINT = "Preprint";
    /**
     * niitype learning material
     * Learning Material
     * @var string 
     */
    const NIITYPE_LEARNING_MATERIAL = "Learning Material";
    /**
     * niitype data or dataset
     * Data or Dataset
     * @var string 
     */
    const NIITYPE_DATA_OR_DATASET = "Data or Dataset";
    /**
     * niitype software
     * Software
     * @var string 
     */
    const NIITYPE_SOFTWARE = "Software";
    /**
     * niitype others
     * Others
     * @var string 
     */
    const NIITYPE_OTHERS = "Others";
    
    // -------------------------------------------
    // OAI-PMH metadata prefix
    // -------------------------------------------
    /**
     * oaipmh metadata prefix dc
     * メタデータ接頭辞
     * @var string 
     */
    const OAIPMH_METADATA_PREFIX_DC = "oai_dc";
    /**
     * oaipmh metadata prefix junii2
     * メタデータ接頭辞
     * @var string 
     */
    const OAIPMH_METADATA_PREFIX_JUNII2 = "junii2";
    /**
     * oaipmh metadata prefix lom
     * メタデータ接頭辞
     * @var string 
     */
    const OAIPMH_METADATA_PREFIX_LOM = "oai_lom";
    /**
     * oaipmh metadata prefix lido
     * メタデータ接頭辞
     * @var string 
     */
    const OAIPMH_METADATA_PREFIX_LIDO = "lido";
    /**
     * oaipmh tag oaipmh
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_OAIPMH = "OAI-PMH";
    /**
     * oaipmh tag res date
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_RES_DATE = "responseDate";
    /**
     * oaipmh tag request
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_REQUEST = "request";
    /**
     * oaipmh tag identifier
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_IDENTIFIER = "identifier";
    /**
     * oaipmh tag repo name
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_REPO_NAME = "repositoryName";
    /**
     * oaipmh tag base url
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_BASE_URL = "baseURL";
    /**
     * oaipmh tag prot ver
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_PROT_VER = "protocolVersion";
    /**
     * oaipmh tag admin email
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_ADMIN_EMAIL = "adminEmail";
    /**
     * oaipmh tag earliest datestamp
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_EARLIEST_DATESTAMP = "earliestDatestamp";
    /**
     * oaipmh tag del rec
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_DEL_REC = "deletedRecord";
    /**
     * oaipmh val del rec no
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_VAL_DEL_REC_NO = "no";
    /**
     * oaipmh val del rec pre
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_VAL_DEL_REC_PRE = "persistent";
    /**
     * oaipmh val del rec trn
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_VAL_DEL_REC_TRN = "transient";
    /**
     * oaipmh tag granularity
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_GRANULARITY = "granularity";
    /**
     * oaipmh val granularity
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_VAL_GRANULARITY = "YYYY-MM-DDThh:mm:ssZ";
    /**
     * oaipmh tag identify
     * verbリクエスト
     * @var string 
     */
    const OAIPMH_TAG_IDENTIFY = "Identify";
    /**
     * oaipmh tag list mata formt
     * verbリクエスト
     * @var string 
     */
    const OAIPMH_TAG_LIST_MATA_FORMT = "ListMetadataFormats";
    /**
     * oaipmh tag mata formt
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_MATA_FORMT = "metadataFormat";
    /**
     * oaipmh tag meta prefix
     * メタデータ接頭辞
     * @var string 
     */
    const OAIPMH_TAG_META_PREFIX = "metadataPrefix";
    /**
     * oaipmh tag schema
     * Attribute name
     * 属性名
     * @var string 
     */
    const OAIPMH_TAG_SCHEMA = "schema";
    /**
     * oaipmh tag meta namesp
     * Attribute name
     * 属性名
     * @var string 
     */
    const OAIPMH_TAG_META_NAMESP = "metadataNamespace";
    /**
     * oaipmh tag list sets
     * verbリクエスト
     * @var string 
     */
    const OAIPMH_TAG_LIST_SETS = "ListSets";
    /**
     * oaipmh tag resump token
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_RESUMP_TOKEN = "resumptionToken";
    /**
     * oaipmh attr expriration date
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_ATTR_EXPRIRATION_DATE = "expirationDate";
    /**
     * oaipmh tag set
     * verbリクエスト
     * @var string 
     */
    const OAIPMH_TAG_SET = "set";
    /**
     * oaipmh tag set spec
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_SET_SPEC = "setSpec";
    /**
     * oaipmh tag set name
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_SET_NAME = "setName";
    /**
     * oaipmh tag record
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_RECORD = "record";
    /**
     * oaipmh tag metadata
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_Metadata = "metadata";
    /**
     * oaipmh tag header
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_HEADER = "header";
    /**
     * oaipmh tag header del
     * header status="deleted"
     * アイテム削除時のStatus値
     * @var string 
     */
    const OAIPMH_TAG_HEADER_DEL = 'header status="deleted"';
    /**
     * oaipmh tag datestamp
     * Tag name
     * タグ名
     * @var string 
     */
    const OAIPMH_TAG_DATESTAMP = "datestamp";
    /**
     * oaipmh tag error
     * エラー
     * @var string 
     */
    const OAIPMH_TAG_ERROR = "error";
    
    // -------------------------------------------
    // Dublin Core
    // -------------------------------------------
    /**
     * dublin core start
     * oai_dc:dc
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_START = "oai_dc:dc";
    /**
     * dublin core prefix
     * dc:
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_PREFIX = "dc:";
    /**
     * dublin core title
     * title
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_TITLE = "title";
    /**
     * dublin core creator
     * creator
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_CREATOR = "creator";
    /**
     * dublin core subject
     * subject
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_SUBJECT = "subject";
    /**
     * dublin core description
     * description
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_DESCRIPTION = "description";
    /**
     * dublin core publisher
     * publisher
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_PUBLISHER = "publisher";
    /**
     * dublin core contributor
     * contributor
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_CONTRIBUTOR = "contributor";
    /**
     * dublin core date
     * date
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_DATE = "date";
    /**
     * dublin core type
     * type
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_TYPE = "type";
    /**
     * dublin core format
     * format
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_FORMAT = "format";
    /**
     * dublin core identifier
     * identifier
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_IDENTIFIER = "identifier";
    /**
     * dublin core source
     * source
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_SOURCE = "source";
    /**
     * dublin core language
     * language
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_LANGUAGE = "language";
    /**
     * dublin core relation
     * relation
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_RELATION = "relation";
    /**
     * dublin core coverage
     * coverage
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_COVERAGE = "coverage";
    /**
     * dublin core rights
     * rights
     * Tag name
     * タグ名
     * @var string 
     */
    const DUBLIN_CORE_RIGHTS = "rights";
    
    // -------------------------------------------
    // JuNii2
    // -------------------------------------------
    /**
     * junii2 start
     * junii2
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_START = "junii2";
    /**
     * junii2 title
     * title
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_TITLE = "title";
    /**
     * junii2 alternative
     * alternative
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_ALTERNATIVE = "alternative";
    /**
     * junii2 creator
     * creator
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_CREATOR = "creator";
    /**
     * junii2 subject
     * subject
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_SUBJECT = "subject";
    /**
     * junii2 nii subject
     * NIIsubject
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_NII_SUBJECT = "NIIsubject";
    /**
     * junii2 ndc
     * NDC
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_NDC = "NDC";
    /**
     * junii2 ndlc
     * NDLC
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_NDLC = "NDLC";
    /**
     * junii2 bsh
     * BSH
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_BSH = "BSH";
    /**
     * junii2 ndlsh
     * NDLSH
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_NDLSH = "NDLSH";
    /**
     * junii2 mesh
     * MeSH
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_MESH = "MeSH";
    /**
     * junii2 ddc
     * DDC
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_DDC = "DDC";
    /**
     * junii2 lcc
     * LCC
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_LCC = "LCC";
    /**
     * junii2 udc
     * UDC
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_UDC = "UDC";
    /**
     * junii2 lcsh
     * LCSH
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_LCSH = "LCSH";
    /**
     * junii2 description
     * description
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_DESCRIPTION = "description";
    /**
     * junii2 publisher
     * publisher
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_PUBLISHER = "publisher";
    /**
     * junii2 contributor
     * contributor
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_CONTRIBUTOR = "contributor";
    /**
     * junii2 date
     * date
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_DATE = "date";
    /**
     * junii2 type
     * type
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_TYPE = "type";
    /**
     * junii2 niitype
     * NIItype
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_NIITYPE = "NIItype";
    /**
     * junii2 format
     * format
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_FORMAT = "format";
    /**
     * junii2 identifier
     * identifier
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_IDENTIFIER = "identifier";
    /**
     * junii2 uri
     * URI
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_URI = "URI";
    /**
     * junii2 full text url
     * fullTextURL
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_FULL_TEXT_URL = "fullTextURL";
    /**
     * junii2 issn
     * issn
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_ISSN = "issn";
    /**
     * junii2 ncid
     * NCID
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_NCID = "NCID";
    /**
     * junii2 jtitle
     * jtitle
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_JTITLE = "jtitle";
    /**
     * junii2 volume
     * volume
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_VOLUME = "volume";
    /**
     * junii2 issue
     * issue
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_ISSUE = "issue";
    /**
     * junii2 spage
     * spage
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_SPAGE = "spage";
    /**
     * junii2 epage
     * epage
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_EPAGE = "epage";
    /**
     * junii2 date of issued
     * dateofissued
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_DATE_OF_ISSUED = "dateofissued";
    /**
     * junii2 source
     * source
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_SOURCE = "source";
    /**
     * junii2 language
     * language
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_LANGUAGE = "language";
    /**
     * junii2 relation
     * relation
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_RELATION = "relation";
    /**
     * junii2 pmid
     * pmid
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_PMID = "pmid";
    /**
     * junii2 doi
     * doi
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_DOI = "doi";
    /**
     * junii2 is version of
     * isVersionOf
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_IS_VERSION_OF = "isVersionOf";
    /**
     * junii2 has version
     * hasVersion
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_HAS_VERSION = "hasVersion";
    /**
     * junii2 is replaced by
     * isReplacedBy
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_IS_REPLACED_BY = "isReplacedBy";
    /**
     * junii2 replaces
     * replaces
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_REPLACES = "replaces";
    /**
     * junii2 is requiresd by
     * isRequiredBy
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_IS_REQUIRESD_BY = "isRequiredBy";
    /**
     * junii2 requires
     * requires
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_REQUIRES = "requires";
    /**
     * junii2 is part of
     * isPartOf
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_IS_PART_OF = "isPartOf";
    /**
     * junii2 has part
     * hasPart
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_HAS_PART = "hasPart";
    /**
     * junii2 is referenced by
     * isReferencedBy
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_IS_REFERENCED_BY = "isReferencedBy";
    /**
     * junii2 references
     * references
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_REFERENCES = "references";
    /**
     * junii2 is format of
     * isFormatOf
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_IS_FORMAT_OF = "isFormatOf";
    /**
     * junii2 has format
     * hasFormat
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_HAS_FORMAT = "hasFormat";
    /**
     * junii2 coverage
     * coverage
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_COVERAGE = "coverage";
    /**
     * junii2 spatial
     * spatial
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_SPATIAL = "spatial";
    /**
     * junii2 nii spatial
     * NIIspatial
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_NII_SPATIAL = "NIIspatial";
    /**
     * junii2 temporal
     * temporal
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_TEMPORAL = "temporal";
    /**
     * junii2 nii temporal
     * NIItemporal
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_NII_TEMPORAL = "NIItemporal";
    /**
     * junii2 rights
     * rights
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_RIGHTS = "rights";
    /**
     * junii2 textversion
     * textversion
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_TEXTVERSION = "textversion";
    /**
     * junii2 attribute lang
     * lang
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_ATTRIBUTE_LANG = "lang";
    /**
     * junii2 attribute version
     * version
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_ATTRIBUTE_VERSION = "version";
    /**
     * junii2 selfdoi
     * selfDOI
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_SELFDOI = "selfDOI";
    /**
     * junii2 selfdoi jalc
     * selfDOI(JaLC)
     * @var string 
     */
    const JUNII2_SELFDOI_JALC = "selfDOI(JaLC)";
    /**
     * junii2 selfdoi crossref
     * selfDOI(CrossRef)
     * @var string 
     */
    const JUNII2_SELFDOI_CROSSREF = "selfDOI(CrossRef)";
    /**
     * junii2 selfdoi datacite
     * selfDOI(DataCite)
     * @var string 
     */
    const JUNII2_SELFDOI_DATACITE = "selfDOI(DataCite)";
    /**
     * junii2 isbn
     * isbn
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_ISBN = "isbn";
    /**
     * junii2 naid
     * NAID
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_NAID = "NAID";
    /**
     * junii2 ichushi
     * ichushi
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_ICHUSHI = "ichushi";
    /**
     * junii2 grantid
     * grantid
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_GRANTID = "grantid";
    /**
     * junii2 dateofgranted
     * dateofgranted
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_DATEOFGRANTED = "dateofgranted";
    /**
     * junii2 degreename
     * degreename
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_DEGREENAME = "degreename";
    /**
     * junii2 grantor
     * grantor
     * Tag name
     * タグ名
     * @var string 
     */
    const JUNII2_GRANTOR = "grantor";
    /**
     * junii2 selfdoi ra jalc
     * JaLC
     * Attribute value ra
     * ra属性値
     * @var string 
     */
    const JUNII2_SELFDOI_RA_JALC = "JaLC";
    /**
     * junii2 selfdoi ra crossref
     * CrossRef
     * Attribute value ra
     * ra属性値
     * @var string 
     */
    const JUNII2_SELFDOI_RA_CROSSREF = "CrossRef";
    /**
     * junii2 selfdoi ra datacite
     * DataCite
     * Attribute value ra
     * ra属性値
     * @var string 
     */
    const JUNII2_SELFDOI_RA_DATACITE = "DataCite";
    /**
     * junii2 selfdoi attribute jalc doi
     * Attribute name
     * 属性名
     * @var string 
     */
    const JUNII2_SELFDOI_ATTRIBUTE_JALC_DOI = "ra";
    
    // -------------------------------------------
    // LOM
    // -------------------------------------------
    /**
     * lom start
     * lom
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_START = "lom";
    /**
     * lom uri
     * URI
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_URI = "URI";
    /**
     * lom issn
     * issn
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_ISSN = "issn";
    /**
     * lom ncid
     * NCID
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_NCID = "NCID";
    /**
     * lom jtitle
     * jtitle
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_JTITLE = "jtitle";
    /**
     * lom volume
     * volume
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_VOLUME = "volume";
    /**
     * lom issue
     * issue
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_ISSUE = "issue";
    /**
     * lom spage
     * spage
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_SPAGE = "spage";
    /**
     * lom epage
     * epage
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_EPAGE = "epage";
    /**
     * lom date of issued
     * dateofissued
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_DATE_OF_ISSUED = "dateofissued";
    /**
     * lom textversion
     * textversion
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TEXTVERSION = "textversion";
    /**
     * lom pmid
     * pmid
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_PMID = "pmid";
    /**
     * lom doi
     * doi
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_DOI = "doi";
    /**
     * lom is version of
     * isversionof
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_IS_VERSION_OF = "isversionof";
    /**
     * lom has version
     * hasversion
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_HAS_VERSION = "hasversion";
    /**
     * lom is requiresd by
     * isrequiredby
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_IS_REQUIRESD_BY = "isrequiredby";
    /**
     * lom requires
     * requires
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_REQUIRES = "requires";
    /**
     * lom is part of
     * ispartof
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_IS_PART_OF = "ispartof";
    /**
     * lom has part
     * haspart
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_HAS_PART = "haspart";
    /**
     * lom is referenced by
     * isreferencedby
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_IS_REFERENCED_BY = "isreferencedby";
    /**
     * lom references
     * references
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_REFERENCES = "references";
    /**
     * lom is format of
     * isformatof
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_IS_FORMAT_OF = "isformatof";
    /**
     * lom has format
     * hasformat
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_HAS_FORMAT = "hasformat";
    /**
     * lom is basis for
     * isbasisfor
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_IS_BASIS_FOR = "isbasisfor";
    /**
     * lom is based on
     * isbasedon
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_IS_BASED_ON = "isbasedon";
    /**
     * lom publish date
     * publisher
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_PUBLISH_DATE = "publisher";
    
    // -------------------------------------------
    // LOM TAG
    // -------------------------------------------
    /**
     * lom tag general
     * general
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_GENERAL = "general";
    /**
     * lom tag life cycle
     * lifeCycle
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_LIFE_CYCLE = "lifeCycle";
    /**
     * lom tag meta metadata
     * metaMetadata
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_META_METADATA = "metaMetadata";
    /**
     * lom tag technical
     * technical
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_TECHNICAL = "technical";
    /**
     * lom tag educational
     * educational
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_EDUCATIONAL = "educational";
    /**
     * lom tag rights
     * rights
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_RIGHTS = "rights";
    /**
     * lom tag cost
     * cost
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_COST = "cost";
    /**
     * lom tag copyright and other restrictions
     * copyrightAndOtherRestrictions
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_COPYRIGHT_AND_OTHER_RESTRICTIONS = "copyrightAndOtherRestrictions";
    /**
     * lom tag relation
     * relation
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_RELATION = "relation";
    /**
     * lom tag annotaion
     * annotation
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_ANNOTAION = "annotation";
    /**
     * lom tag classification
     * classification
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_CLASSIFICATION = "classification";
    /**
     * lom tag identifier
     * identifier
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_IDENTIFIER = "identifier";
    /**
     * lom tag catalog
     * catalog
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_CATALOG = "catalog";
    /**
     * lom tag entry
     * entry
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_ENTRY = "entry";
    /**
     * lom tag title
     * title
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_TITLE = "title";
    /**
     * lom tag source
     * source
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_SOURCE = "source";
    /**
     * lom tag value
     * value
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_VALUE = "value";
    /**
     * lom tag language
     * language
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_LANGUAGE = "language";
    /**
     * lom tag description
     * description
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_DESCRIPTION = "description";
    /**
     * lom tag keyword
     * keyword
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_KEYWORD = "keyword";
    /**
     * lom tag coverage
     * coverage
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_COVERAGE = "coverage";
    /**
     * lom tag structure
     * structure
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_STRUCTURE = "structure";
    /**
     * lom tag aggregation level
     * aggregationLevel
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_AGGREGATION_LEVEL = "aggregationLevel";
    /**
     * lom tag version
     * version
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_VERSION = "version";
    /**
     * lom tag status
     * status
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_STATUS = "status";
    /**
     * lom tag contribute
     * contribute
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_CONTRIBUTE = "contribute";
    /**
     * lom tag metadata schema
     * metadataSchema
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_METADATA_SCHEMA = "metadataSchema";
    /**
     * lom tag format
     * format
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_FORMAT = "format";
    /**
     * lom tag size
     * size
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_SIZE = "size";
    /**
     * lom tag location
     * location
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_LOCATION = "location";
    /**
     * lom tag requirement
     * requirement
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_REQUIREMENT = "requirement";
    /**
     * lom tag installation remarks
     * installationRemarks
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_INSTALLATION_REMARKS = "installationRemarks";
    /**
     * lom tag other platform reqirements
     * otherPlatformRequirements
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_OTHER_PLATFORM_REQIREMENTS = "otherPlatformRequirements";
    /**
     * lom tag duration
     * duration
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_DURATION = "duration";
    /**
     * lom tag interactivity type
     * interactivityType
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_INTERACTIVITY_TYPE = "interactivityType";
    /**
     * lom tag interactivity level
     * interactivityLevel
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_INTERACTIVITY_LEVEL = "interactivityLevel";
    /**
     * lom tag learning resource type
     * learningResourceType
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_LEARNING_RESOURCE_TYPE = "learningResourceType";
    /**
     * lom tag semantic density
     * semanticDensity
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_SEMANTIC_DENSITY = "semanticDensity";
    /**
     * lom tag intended end user role
     * intendedEndUserRole
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_INTENDED_END_USER_ROLE = "intendedEndUserRole";
    /**
     * lom tag context
     * context
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_CONTEXT = "context";
    /**
     * lom tag typical age range
     * typicalAgeRange
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_TYPICAL_AGE_RANGE = "typicalAgeRange";
    /**
     * lom tag difficulty
     * difficulty
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_DIFFICULTY = "difficulty";
    /**
     * lom tag typical learning time
     * typicalLearningTime
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_TYPICAL_LEARNING_TIME = "typicalLearningTime";
    /**
     * lom tag resource
     * resource
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_RESOURCE = "resource";
    /**
     * lom tag kind
     * kind
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_KIND = "kind";
    /**
     * lom tag date
     * date
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_DATE = "date";
    /**
     * lom tag date time
     * dateTime
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_DATE_TIME = "dateTime";
    /**
     * lom tag entity
     * entity
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_ENTITY = "entity";
    /**
     * lom tag purpose
     * purpose
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_PURPOSE = "purpose";
    /**
     * lom tag taxon path
     * taxonPath
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_TAXON_PATH = "taxonPath";
    /**
     * lom tag taxon
     * taxon
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_TAXON = "taxon";
    /**
     * lom tag id
     * id
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_ID = "id";
    /**
     * lom tag string
     * string
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_STRING = "string";
    /**
     * lom tag role
     * role
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_ROLE = "role";
    /**
     * lom tag type
     * type
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_TYPE = "type";
    /**
     * lom tag or composite
     * orComposite
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_OR_COMPOSITE = "orComposite";
    /**
     * lom tag name
     * name
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_NAME = "name";
    /**
     * lom tag minimum version
     * minimumVersion
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_MINIMUM_VERSION = "minimumVersion";
    /**
     * lom tag maximum version
     * maximumVersion
     * Tag name
     * タグ名
     * @var string 
     */
    const LOM_TAG_MAXIMUM_VERSION = "maximumVersion";
    
    // -------------------------------------------
    // LOM Mapping Attribute Name
    // -------------------------------------------
    /**
     * lom map gnrl identifer
     * generalIdentifier
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_GNRL_IDENTIFER = "generalIdentifier";
    /**
     * lom map gnrl title
     * generalTitle
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_GNRL_TITLE = "generalTitle";
    /**
     * lom map gnrl language
     * generalLanguage
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_GNRL_LANGUAGE = "generalLanguage";
    /**
     * lom map gnrl description
     * generalDescription
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_GNRL_DESCRIPTION = "generalDescription";
    /**
     * lom map gnrl keyword
     * generalKeyword
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_GNRL_KEYWORD = "generalKeyword";
    /**
     * lom map gnrl coverage
     * generalCoverage
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_GNRL_COVERAGE = "generalCoverage";
    /**
     * lom map gnrl structure
     * generalStructure
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_GNRL_STRUCTURE = "generalStructure";
    /**
     * lom map gnrl aggregation level
     * generalAggregationLevel
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_GNRL_AGGREGATION_LEVEL = "generalAggregationLevel";
    /**
     * lom map lfcycl version
     * lifeCycleVersion
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_VERSION = "lifeCycleVersion";
    /**
     * lom map lfcycl status
     * lifeCycleStatus
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_STATUS = "lifeCycleStatus";
    /**
     * lom map lfcycl contribute
     * lifeCycleContribute
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE = "lifeCycleContribute";
    /**
     * lom map lfcycl contribute author
     * lifeCycleContributeRoleAuthor
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_AUTHOR = "lifeCycleContributeRoleAuthor";
    /**
     * lom map lfcycl contribute publisher
     * lifeCycleContributeRolePublisher
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_PUBLISHER = "lifeCycleContributeRolePublisher";
    /**
     * lom map lfcycl contribute publish date
     * lifeCycleContributeDate
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_PUBLISH_DATE = "lifeCycleContributeDate";
    /**
     * lom map lfcycl contribute unknown
     * lifeCycleContributeRoleUnknown
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_UNKNOWN = "lifeCycleContributeRoleUnknown";
    /**
     * lom map lfcycl contribute initiator
     * lifeCycleContributeRoleInitiator
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_INITIATOR = "lifeCycleContributeRoleInitiator";
    /**
     * lom map lfcycl contribute terminator
     * lifeCycleContributeRoleTerminator
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_TERMINATOR = "lifeCycleContributeRoleTerminator";
    /**
     * lom map lfcycl contribute validator
     * lifeCycleContributeRoleValidator
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_VALIDATOR = "lifeCycleContributeRoleValidator";
    /**
     * lom map lfcycl contribute editor
     * lifeCycleContributeRoleEditor
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_EDITOR = "lifeCycleContributeRoleEditor";
    /**
     * lom map lfcycl contribute graphical designer
     * lifeCycleContributeRoleGraphicalDesigner
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_GRAPHICAL_DESIGNER = "lifeCycleContributeRoleGraphicalDesigner";
    /**
     * lom map lfcycl contribute technical implementer
     * lifeCycleContributeRoleTechnicalImplementer
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_TECHNICAL_IMPLEMENTER = "lifeCycleContributeRoleTechnicalImplementer";
    /**
     * lom map lfcycl contribute content provider
     * lifeCycleContributeRoleContentProvider
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_CONTENT_PROVIDER = "lifeCycleContributeRoleContentProvider";
    /**
     * lom map lfcycl contribute technical validator
     * lifeCycleContributeRoleTechnicalValidator
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_TECHNICAL_VALIDATOR = "lifeCycleContributeRoleTechnicalValidator";
    /**
     * lom map lfcycl contribute educational validator
     * lifeCycleContributeRoleEducationalValidator
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_EDUCATIONAL_VALIDATOR = "lifeCycleContributeRoleEducationalValidator";
    /**
     * lom map lfcycl contribute script writer
     * lifeCycleContributeRoleScriptWriter
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_SCRIPT_WRITER = "lifeCycleContributeRoleScriptWriter";
    /**
     * lom map lfcycl contribute instructional designer
     * lifeCycleContributeRoleInstructionalDesigner
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_INSTRUCTIONAL_DESIGNER = "lifeCycleContributeRoleInstructionalDesigner";
    /**
     * lom map lfcycl contribute subject matter expert
     * lifeCycleContributeRoleSubjectMatterExpert
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_LFCYCL_CONTRIBUTE_SUBJECT_MATTER_EXPERT = "lifeCycleContributeRoleSubjectMatterExpert";
    /**
     * lom map mtmtdt identifer
     * metaMetadataIdentifer
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_MTMTDT_IDENTIFER = "metaMetadataIdentifer";
    /**
     * lom map mtmtdt contribute
     * metaMetadataContribute
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_MTMTDT_CONTRIBUTE = "metaMetadataContribute";
    /**
     * lom map mtmtdt contribute creator
     * metaMetadataContributeRoleCreator
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_MTMTDT_CONTRIBUTE_CREATOR = "metaMetadataContributeRoleCreator";
    /**
     * lom map mtmtdt contribute validator
     * metaMetadataContributeRoleValidator
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_MTMTDT_CONTRIBUTE_VALIDATOR = "metaMetadataContributeRoleValidator";
    /**
     * lom map mtmtdt metadata schema
     * metaMetadataMetadataSchema
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_MTMTDT_METADATA_SCHEMA = "metaMetadataMetadataSchema";
    /**
     * lom map mtmtdt language
     * metaMetadataLanguage
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_MTMTDT_LANGUAGE = "metaMetadataLanguage";
    /**
     * lom map tchncl format
     * technicalFormat
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_TCHNCL_FORMAT = "technicalFormat";
    /**
     * lom map tchncl size
     * technicalSize
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_TCHNCL_SIZE = "technicalSize";
    /**
     * lom map tchncl location
     * technicalLocation
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_TCHNCL_LOCATION = "technicalLocation";
    /**
     * lom map tchncl reqirement orcomposite type
     * technicalRequirementOrCompositeType
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_TYPE = "technicalRequirementOrCompositeType";
    /**
     * lom map tchncl reqirement orcomposite name
     * technicalRequirementOrCompositeName
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_NAME = "technicalRequirementOrCompositeName";
    /**
     * lom map tchncl reqirement orcomposite minimum version
     * technicalRequirementOrCompositeMinimumVersion
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MINIMUM_VERSION = "technicalRequirementOrCompositeMinimumVersion";
    /**
     * lom map tchncl reqirement orcomposite maximum version
     * technicalRequirementOrCompositeMaximumVersion
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MAXIMUM_VERSION = "technicalRequirementOrCompositeMaximumVersion";
    /**
     * lom map tchncl installation remarks
     * technicalInstallationRemarks
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_TCHNCL_INSTALLATION_REMARKS = "technicalInstallationRemarks";
    /**
     * lom map tchncl other platform requirements
     * technicalOtherPlatformRequirements
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_TCHNCL_OTHER_PLATFORM_REQUIREMENTS = "technicalOtherPlatformRequirements";
    /**
     * lom map tchncl duration
     * technicalDuration
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_TCHNCL_DURATION = "technicalDuration";
    /**
     * lom map eductnl interactivity type
     * educationalInteractivityType
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_INTERACTIVITY_TYPE = "educationalInteractivityType";
    /**
     * lom map eductnl learning resource type
     * educationalLearningResourceType
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_LEARNING_RESOURCE_TYPE = "educationalLearningResourceType";
    /**
     * lom map eductnl interactivity level
     * educationalInteractivityLevel
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_INTERACTIVITY_LEVEL = "educationalInteractivityLevel";
    /**
     * lom map eductnl semantic density
     * educationalSemanticDensity
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_SEMANTIC_DENSITY = "educationalSemanticDensity";
    /**
     * lom map eductnl intended end user role
     * educationalIntendedEndUserRole
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_INTENDED_END_USER_ROLE = "educationalIntendedEndUserRole";
    /**
     * lom map eductnl context
     * educationalContext
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_CONTEXT = "educationalContext";
    /**
     * lom map eductnl typical age range
     * educationalTypicalAgeRange
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_TYPICAL_AGE_RANGE = "educationalTypicalAgeRange";
    /**
     * lom map eductnl difficulty
     * educationalDifficulty
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_DIFFICULTY = "educationalDifficulty";
    /**
     * lom map eductnl typical learning time
     * educationalTypicalLearningTime
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_TYPICAL_LEARNING_TIME = "educationalTypicalLearningTime";
    /**
     * lom map eductnl description
     * educationalDescription
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_DESCRIPTION = "educationalDescription";
    /**
     * lom map eductnl language
     * educationalLanguage
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_EDUCTNL_LANGUAGE = "educationalLanguage";
    /**
     * lom map rltn
     * relationResource
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN = "relationResource";
    /**
     * lom map rltn is part of
     * relationIsPartOf
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_IS_PART_OF = "relationIsPartOf";
    /**
     * lom map rltn has part of
     * relationHasPart
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_HAS_PART_OF = "relationHasPart";
    /**
     * lom map rltn is version of
     * relationIsVersionOf
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_IS_VERSION_OF = "relationIsVersionOf";
    /**
     * lom map rltn has version
     * relationHasVersion
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_HAS_VERSION = "relationHasVersion";
    /**
     * lom map rltn is format of
     * relationIsFormatOf
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_IS_FORMAT_OF = "relationIsFormatOf";
    /**
     * lom map rltn has format
     * relationHasFormat
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_HAS_FORMAT = "relationHasFormat";
    /**
     * lom map rltn references
     * relationReferences
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_REFERENCES = "relationReferences";
    /**
     * lom map rltn is referenced by
     * relationIsReferencedBy
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_IS_REFERENCED_BY = "relationIsReferencedBy";
    /**
     * lom map rltn is based on
     * relationIsBasedOn
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_IS_BASED_ON = "relationIsBasedOn";
    /**
     * lom map rltn is basis for
     * relationIsBasisFor
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_IS_BASIS_FOR = "relationIsBasisFor";
    /**
     * lom map rltn requires
     * relationRequires
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_REQUIRES = "relationRequires";
    /**
     * lom map rltn is required by
     * relationIsRequiredBy
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RLTN_IS_REQUIRED_BY = "relationIsRequiredBy";
    /**
     * lom map rghts cost
     * rightsCost
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RGHTS_COST = "rightsCost";
    /**
     * lom map rghts copyright and other restrictions
     * rightsCopyrightAndOtherRestrictions
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RGHTS_COPYRIGHT_AND_OTHER_RESTRICTIONS = "rightsCopyrightAndOtherRestrictions";
    /**
     * lom map rghts description
     * rightsDescription
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_RGHTS_DESCRIPTION = "rightsDescription";
    /**
     * lom map annttn entity
     * annotationEntity
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_ANNTTN_ENTITY = "annotationEntity";
    /**
     * lom map annttn date
     * annotationDate
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_ANNTTN_DATE = "annotationDate";
    /**
     * lom map annttn description
     * annotationDescription
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_ANNTTN_DESCRIPTION = "annotationDescription";
    /**
     * lom map clssfctn purpose
     * classificationPurpose
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_CLSSFCTN_PURPOSE = "classificationPurpose";
    /**
     * lom map clssfctn description
     * classificationDescription
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_CLSSFCTN_DESCRIPTION = "classificationDescription";
    /**
     * lom map clssfctn keyword
     * classificationKeyword
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_CLSSFCTN_KEYWORD = "classificationKeyword";
    /**
     * lom map clssfctn taxon path source
     * classificationTaxonPathSource
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_CLSSFCTN_TAXON_PATH_SOURCE = "classificationTaxonPathSource";
    /**
     * lom map clssfctn taxon
     * classificationTaxonPathTaxon
     * Mapping name
     * マッピング名
     * @var string 
     */
    const LOM_MAP_CLSSFCTN_TAXON = "classificationTaxonPathTaxon";
    
    // -------------------------------------------
    // LIDO 
    // -------------------------------------------
    /**
     * Empty string
     * 空文字
     *
     * @var string
     */
    const BLANK_WORD = "&EMPTY&";
    /**
     * XML line feed
     * XML改行コード
     *
     * @var string
     */
    const XML_LF = "&#xA;";
    /**
     * LIDO schema
     * LIDO スキーマ
     *
     * @var string
     */
    const LIDO_SCHEMA_ORG = "http://www.lido-schema.org";
    /**
     * LIDO XSD schema
     * LIDO XSD スキーマ
     *
     * @var string
     */
    const LIDO_SCHEMA_XSD = "http://www.lido-schema.org/schema/v1.0/lido-v1.0.xsd";
    /**
     * LIDO schema instance
     * LIDO スキーマインスタンス
     *
     * @var string
     */
    const LIDO_XML_SCHEMAINSTANCE = "http://www.w3.org/2001/XMLSchema-instance";
    /**
     * LIDO schema name space
     * LIDO スキーマ名前空間
     *
     * @var string
     */
    const LIDO_XML_NAMESPACE_URL = "http://www.w3.org/XML/1998/namespace";
    /**
     * LIDO language(jp)
     * LIDO言語(jp)
     *
     * @var string
     */
    const LIDO_LANG_JAPANESE = "jp";
    /**
     * LIDO language(en)
     * LIDO言語(en)
     *
     * @var string
     */
    const LIDO_LANG_ENGLISH = "en";
    /**
     * Attribute name
     * 属性名
     *
     * @var string
     */
    const LIDO_ATTR_LANG = "lang";
    /**
     * Attribute name
     * 属性名
     *
     * @var string
     */
    const LIDO_ATTR_XML_LANG = "xml:lang";
    /**
     * Attribute name
     * 属性名
     *
     * @var string
     */
    const LIDO_ATTR_XML_TYPE = "lido:type";
    /**
     * Tag name space
     * タグ名前空間
     *
     * @var string
     */
    const LIDO_TAG_NAMESPACE = "lido:";
    /**
     * Tag name
     * タグ名
     *
     * @var string
     */
    const LIDO_TAG_LIDO_LIDO = "lido:lido";
    /**
     * Tag name
     * タグ名
     *
     * @var string
     */
    const LIDO_TAG_LIDO_WRAP = "lido:lidoWrap";
    /**
     * Attribute name
     * 属性名
     *
     * @var string
     */
    const LIDO_ATTRIBUTE_TYPE_URI = "URI";
    
    /**
     * GML schema
     * GMLスキーマ
     *
     * @var string
     */
    const GML_SCHEMA = "http://www.opengis.net/gml";
    /**
     * GML XSD
     * GML XSD
     *
     * @var string
     */
    const GML_SCHEMA_XSD = "http://schemas.opengis.net/gml/3.1.1/base/gml.xsd";
    /**
     * GML tag name space
     * GMLタグ名前空間
     *
     * @var string
     */
    const GML_TAG_NAMESPACE = "gml:";
    /**
     * GML tag name
     * GMLタグ名
     *
     * @var string
     */
    const GML_TAG_POINT = "Point";
    /**
     * GML tag name
     * GMLタグ名
     *
     * @var string
     */
    const GML_TAG_POS = "pos";
    /**
     * GML tag name
     * GMLタグ名
     *
     * @var string
     */
    const GML_TAG_POLYGON = "Polygon";
    /**
     * GML tag name
     * GMLタグ名
     *
     * @var string
     */
    const GML_TAG_EXTERIOR = "exterior";
    /**
     * GML tag name
     * GMLタグ名
     *
     * @var string
     */
    const GML_TAG_LINEAR_RING = "LinearRing";
    /**
     * GML tag name
     * GMLタグ名
     *
     * @var string
     */
    const GML_TAG_COORDINATES = "coordinates";
    
    // -------------------------------------------
    // LIDO TAG
    // -------------------------------------------
    /**
     * lido tag lido rec id
     * lidoRecID
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_LIDO_REC_ID = "lidoRecID";
    /**
     * lido tag descriptive metadata
     * descriptiveMetadata
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DESCRIPTIVE_METADATA = "descriptiveMetadata";
    /**
     * lido tag object classification wrap
     * objectClassificationWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_OBJECT_CLASSIFICATION_WRAP = "objectClassificationWrap";
    /**
     * lido tag object work type wrap
     * objectWorkTypeWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_OBJECT_WORK_TYPE_WRAP = "objectWorkTypeWrap";
    /**
     * lido tag object work type
     * objectWorkType
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_OBJECT_WORK_TYPE = "objectWorkType";
    /**
     * lido tag concept id
     * conceptID
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_CONCEPT_ID = "conceptID";
    /**
     * lido tag term
     * term
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_TERM = "term";
    /**
     * lido tag classification wrap
     * classificationWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_CLASSIFICATION_WRAP = "classificationWrap";
    /**
     * lido tag classification
     * classification
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_CLASSIFICATION = "classification";
    /**
     * lido tag object identification wrap
     * objectIdentificationWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_OBJECT_IDENTIFICATION_WRAP = "objectIdentificationWrap";
    /**
     * lido tag title wrap
     * titleWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_TITLE_WRAP = "titleWrap";
    /**
     * lido tag title set
     * titleSet
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_TITLE_SET = "titleSet";
    /**
     * lido tag appellation value
     * appellationValue
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_APPELLATION_VALUE = "appellationValue";
    /**
     * lido tag inscriptions wrap
     * inscriptionsWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_INSCRIPTIONS_WRAP = "inscriptionsWrap";
    /**
     * lido tag inscriptions
     * inscriptions
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_INSCRIPTIONS = "inscriptions";
    /**
     * lido tag inscription transcription
     * inscriptionTranscription
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_INSCRIPTION_TRANSCRIPTION = "inscriptionTranscription";
    /**
     * lido tag repository wrap
     * repositoryWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_REPOSITORY_WRAP = "repositoryWrap";
    /**
     * lido tag repository set
     * repositorySet
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_REPOSITORY_SET = "repositorySet";
    /**
     * lido tag repository name
     * repositoryName
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_REPOSITORY_NAME = "repositoryName";
    /**
     * lido tag legal body name
     * legalBodyName
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_LEGAL_BODY_NAME = "legalBodyName";
    /**
     * lido tag legal body web link
     * legalBodyWeblink
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_LEGAL_BODY_WEB_LINK = "legalBodyWeblink";
    /**
     * lido tag work id
     * workID
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_WORK_ID = "workID";
    /**
     * lido tag display state edition wrap
     * displayStateEditionWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DISPLAY_STATE_EDITION_WRAP = "displayStateEditionWrap";
    /**
     * lido tag display state
     * displayState
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DISPLAY_STATE = "displayState";
    /**
     * lido tag object description wrap
     * objectDescriptionWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_OBJECT_DESCRIPTION_WRAP = "objectDescriptionWrap";
    /**
     * lido tag object description set
     * objectDescriptionSet
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_OBJECT_DESCRIPTION_SET = "objectDescriptionSet";
    /**
     * lido tag descriptive note value
     * descriptiveNoteValue
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DESCRIPTIVE_NOTE_VALUE = "descriptiveNoteValue";
    /**
     * lido tag object measurements wrap
     * objectMeasurementsWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_OBJECT_MEASUREMENTS_WRAP = "objectMeasurementsWrap";
    /**
     * lido tag object measurements set
     * objectMeasurementsSet
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_OBJECT_MEASUREMENTS_SET = "objectMeasurementsSet";
    /**
     * lido tag display object measurements
     * displayObjectMeasurements
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DISPLAY_OBJECT_MEASUREMENTS = "displayObjectMeasurements";
    /**
     * lido tag event wrap
     * eventWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_EVENT_WRAP = "eventWrap";
    /**
     * lido tag event set
     * eventSet
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_EVENT_SET = "eventSet";
    /**
     * lido tag display event
     * displayEvent
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DISPLAY_EVENT = "displayEvent";
    /**
     * lido tag event
     * event
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_EVENT = "event";
    /**
     * lido tag event type
     * eventType
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_EVENT_TYPE = "eventType";
    /**
     * lido tag event actor
     * eventActor
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_EVENT_ACTOR = "eventActor";
    /**
     * lido tag event date
     * eventDate
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_EVENT_DATE = "eventDate";
    /**
     * lido tag display actor in role
     * displayActorInRole
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DISPLAY_ACTOR_IN_ROLE = "displayActorInRole";
    /**
     * lido tag display date
     * displayDate
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DISPLAY_DATE = "displayDate";
    /**
     * lido tag earliest date
     * earliestDate
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_EARLIEST_DATE = "earliestDate";
    /**
     * lido tag date
     * date
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DATE = "date";
    /**
     * lido tag latest date
     * latestDate
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_LATEST_DATE = "latestDate";
    /**
     * lido tag period name
     * periodName
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_PERIOD_NAME = "periodName";
    /**
     * lido tag event place
     * eventPlace
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_EVENT_PLACE = "eventPlace";
    /**
     * lido tag display place
     * displayPlace
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DISPLAY_PLACE = "displayPlace";
    /**
     * lido tag place
     * place
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_PLACE = "place";
    /**
     * lido tag gml
     * gml
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_GML = "gml";
    /**
     * lido tag event materials tech
     * eventMaterialsTech
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_EVENT_MATERIALS_TECH = "eventMaterialsTech";
    /**
     * lido tag display materials tech
     * displayMaterialsTech
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DISPLAY_MATERIALS_TECH = "displayMaterialsTech";
    /**
     * lido tag object relation wrap
     * objectRelationWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_OBJECT_RELATION_WRAP = "objectRelationWrap";
    /**
     * lido tag subject wrap
     * subjectWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_SUBJECT_WRAP = "subjectWrap";
    /**
     * lido tag subject set
     * subjectSet
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_SUBJECT_SET = "subjectSet";
    /**
     * lido tag display subject
     * displaySubject
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DISPLAY_SUBJECT = "displaySubject";
    /**
     * lido tag related works wrap
     * relatedWorksWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RELATED_WORKS_WRAP = "relatedWorksWrap";
    /**
     * lido tag related work set
     * relatedWorkSet
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RELATED_WORK_SET = "relatedWorkSet";
    /**
     * lido tag related work
     * relatedWork
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RELATED_WORK = "relatedWork";
    /**
     * lido tag display object
     * displayObject
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_DISPLAY_OBJECT = "displayObject";
    /**
     * lido tag administrative metadata
     * administrativeMetadata
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_ADMINISTRATIVE_METADATA = "administrativeMetadata";
    /**
     * lido tag record wrap
     * recordWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RECORD_WRAP = "recordWrap";
    /**
     * lido tag record id
     * recordID
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RECORD_ID = "recordID";
    /**
     * lido tag record type
     * recordType
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RECORD_TYPE = "recordType";
    /**
     * lido tag record source
     * recordSource
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RECORD_SOURCE = "recordSource";
    /**
     * lido tag record info set
     * recordInfoSet
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RECORD_INFO_SET = "recordInfoSet";
    /**
     * lido tag record info link
     * recordInfoLink
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RECORD_INFO_LINK = "recordInfoLink";
    /**
     * lido tag record metadata date
     * recordMetadataDate
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RECORD_METADATA_DATE = "recordMetadataDate";
    /**
     * lido tag resource wrap
     * resourceWrap
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RESOURCE_WRAP = "resourceWrap";
    /**
     * lido tag resource set
     * resourceSet
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RESOURCE_SET = "resourceSet";
    /**
     * lido tag resource representation
     * resourceRepresentation
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RESOURCE_REPRESENTATION = "resourceRepresentation";
    /**
     * lido tag link resource
     * linkResource
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_LINK_RESOURCE = "linkResource";
    /**
     * lido tag resource description
     * resourceDescription
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RESOURCE_DESCRIPTION = "resourceDescription";
    /**
     * lido tag resource source
     * resourceSource
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RESOURCE_SOURCE = "resourceSource";
    /**
     * lido tag right resource
     * rightsResource
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_RIGHT_RESOURCE = "rightsResource";
    /**
     * lido tag credit line
     * creditLine
     * Tag name
     * タグ名
     * @var string 
     */
    const LIDO_TAG_CREDIT_LINE = "creditLine";
    
    // -------------------------------------------
    // LIDO FULL
    // -------------------------------------------
    /**
     * lido fullname objectworktype conceptid
     * descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.conceptID
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_OBJECTWORKTYPE_CONCEPTID = "descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.conceptID";
    /**
     * lido fullname objectworktype term
     * descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_OBJECTWORKTYPE_TERM = "descriptiveMetadata.objectClassificationWrap.objectWorkTypeWrap.objectWorkType.term";
    /**
     * lido fullname classification conceptid
     * descriptiveMetadata.objectClassificationWrap.classificationWrap.classification.conceptID
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_CLASSIFICATION_CONCEPTID = "descriptiveMetadata.objectClassificationWrap.classificationWrap.classification.conceptID";
    /**
     * lido fullname classification term
     * descriptiveMetadata.objectClassificationWrap.classificationWrap.classification.term
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_CLASSIFICATION_TERM = "descriptiveMetadata.objectClassificationWrap.classificationWrap.classification.term";
    /**
     * lido fullname titleset
     * descriptiveMetadata.objectIdentificationWrap.titleWrap.titleSet.appellationValue
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_TITLESET = "descriptiveMetadata.objectIdentificationWrap.titleWrap.titleSet.appellationValue";
    /**
     * lido fullname inscriptiontranscription
     * descriptiveMetadata.objectIdentificationWrap.inscriptionsWrap.inscriptions.inscriptionTranscription
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_INSCRIPTIONTRANSCRIPTION = "descriptiveMetadata.objectIdentificationWrap.inscriptionsWrap.inscriptions.inscriptionTranscription";
    /**
     * lido fullname repositoryname legalbodyname
     * descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.repositoryName.legalBodyName.appellationValue
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_REPOSITORYNAME_LEGALBODYNAME = "descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.repositoryName.legalBodyName.appellationValue";
    /**
     * lido fullname repositoryname legalbodyweblink
     * descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.repositoryName.legalBodyWeblink
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_REPOSITORYNAME_LEGALBODYWEBLINK = "descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.repositoryName.legalBodyWeblink";
    /**
     * lido fullname workid
     * descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.workID
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_WORKID = "descriptiveMetadata.objectIdentificationWrap.repositoryWrap.repositorySet.workID";
    /**
     * lido fullname displaystate
     * descriptiveMetadata.objectIdentificationWrap.displayStateEditionWrap.displayState
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_DISPLAYSTATE = "descriptiveMetadata.objectIdentificationWrap.displayStateEditionWrap.displayState";
    /**
     * lido fullname descriptivenotevalue
     * descriptiveMetadata.objectIdentificationWrap.objectDescriptionWrap.objectDescriptionSet.descriptiveNoteValue
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_DESCRIPTIVENOTEVALUE = "descriptiveMetadata.objectIdentificationWrap.objectDescriptionWrap.objectDescriptionSet.descriptiveNoteValue";
    /**
     * lido fullname displayobjectmeasurements
     * descriptiveMetadata.objectIdentificationWrap.objectMeasurementsWrap.objectMeasurementsSet.displayObjectMeasurements
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_DISPLAYOBJECTMEASUREMENTS = "descriptiveMetadata.objectIdentificationWrap.objectMeasurementsWrap.objectMeasurementsSet.displayObjectMeasurements";
    /**
     * lido fullname displayevent
     * descriptiveMetadata.eventWrap.eventSet.displayEvent
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_DISPLAYEVENT = "descriptiveMetadata.eventWrap.eventSet.displayEvent";
    /**
     * lido fullname eventtype
     * descriptiveMetadata.eventWrap.eventSet.event.eventType.term
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_EVENTTYPE = "descriptiveMetadata.eventWrap.eventSet.event.eventType.term";
    /**
     * lido fullname displayactorinrole
     * descriptiveMetadata.eventWrap.eventSet.event.eventActor.displayActorInRole
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_DISPLAYACTORINROLE = "descriptiveMetadata.eventWrap.eventSet.event.eventActor.displayActorInRole";
    /**
     * lido fullname displaydate
     * descriptiveMetadata.eventWrap.eventSet.event.eventDate.displayDate
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_DISPLAYDATE = "descriptiveMetadata.eventWrap.eventSet.event.eventDate.displayDate";
    /**
     * lido fullname earliestdate
     * descriptiveMetadata.eventWrap.eventSet.event.eventDate.date.earliestDate
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_EARLIESTDATE ="descriptiveMetadata.eventWrap.eventSet.event.eventDate.date.earliestDate";
    /**
     * lido fullname latestdate
     * descriptiveMetadata.eventWrap.eventSet.event.eventDate.date.latestDate
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_LATESTDATE = "descriptiveMetadata.eventWrap.eventSet.event.eventDate.date.latestDate";
    /**
     * lido fullname periodname
     * descriptiveMetadata.eventWrap.eventSet.event.periodName.term
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_PERIODNAME = "descriptiveMetadata.eventWrap.eventSet.event.periodName.term";
    /**
     * lido fullname displayplace
     * descriptiveMetadata.eventWrap.eventSet.event.eventPlace.displayPlace
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_DISPLAYPLACE = "descriptiveMetadata.eventWrap.eventSet.event.eventPlace.displayPlace";
    /**
     * lido fullname place gml
     * descriptiveMetadata.eventWrap.eventSet.event.eventPlace.place.gml
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_PLACE_GML = "descriptiveMetadata.eventWrap.eventSet.event.eventPlace.place.gml";
    /**
     * lido fullname displaymaterialstech
     * descriptiveMetadata.eventWrap.eventSet.event.eventMaterialsTech.displayMaterialsTech
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_DISPLAYMATERIALSTECH = "descriptiveMetadata.eventWrap.eventSet.event.eventMaterialsTech.displayMaterialsTech";
    /**
     * lido fullname displaysubject
     * descriptiveMetadata.objectRelationWrap.subjectWrap.subjectSet.displaySubject
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_DISPLAYSUBJECT = "descriptiveMetadata.objectRelationWrap.subjectWrap.subjectSet.displaySubject";
    /**
     * lido fullname relatedworkdisplayobject
     * descriptiveMetadata.objectRelationWrap.relatedWorksWrap.relatedWorkSet.relatedWork.displayObject
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_RELATEDWORKDISPLAYOBJECT = "descriptiveMetadata.objectRelationWrap.relatedWorksWrap.relatedWorkSet.relatedWork.displayObject";
    /**
     * lido fullname recordid
     * administrativeMetadata.recordWrap.recordID
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_RECORDID = "administrativeMetadata.recordWrap.recordID";
    /**
     * lido fullname recordtype
     * administrativeMetadata.recordWrap.recordType.term
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_RECORDTYPE = "administrativeMetadata.recordWrap.recordType.term";
    /**
     * lido fullname recordsource
     * administrativeMetadata.recordWrap.recordSource.legalBodyName.appellationValue
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_RECORDSOURCE = "administrativeMetadata.recordWrap.recordSource.legalBodyName.appellationValue";
    /**
     * lido fullname recourdinfolink
     * administrativeMetadata.recordWrap.recordInfoSet.recordInfoLink
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_RECOURDINFOLINK = "administrativeMetadata.recordWrap.recordInfoSet.recordInfoLink";
    /**
     * lido fullname recordmetadatadate
     * administrativeMetadata.recordWrap.recordInfoSet.recordMetadataDate
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_RECORDMETADATADATE = "administrativeMetadata.recordWrap.recordInfoSet.recordMetadataDate";
    /**
     * lido fullname linkresource
     * administrativeMetadata.resourceWrap.resourceSet.resourceRepresentation.linkResource
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_LINKRESOURCE = "administrativeMetadata.resourceWrap.resourceSet.resourceRepresentation.linkResource";
    /**
     * lido fullname resourcedescription
     * administrativeMetadata.resourceWrap.resourceSet.resourceDescription
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_RESOURCEDESCRIPTION = "administrativeMetadata.resourceWrap.resourceSet.resourceDescription";
    /**
     * lido fullname resourcesource
     * administrativeMetadata.resourceWrap.resourceSet.resourceSource.legalBodyName.appellationValue
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_RESOURCESOURCE = "administrativeMetadata.resourceWrap.resourceSet.resourceSource.legalBodyName.appellationValue";
    /**
     * lido fullname creditline
     * administrativeMetadata.resourceWrap.resourceSet.rightsResource.creditLine
     * LIDO mapping name
     * LIDOマッピング名
     * @var string 
     */
    const LIDO_FULLNAME_CREDITLINE = "administrativeMetadata.resourceWrap.resourceSet.rightsResource.creditLine";
    
    // -------------------------------------------
    // Item's language
    // -------------------------------------------
    /**
     * item lang ja
     * ja
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_JA = "ja";
    /**
     * item lang en
     * en
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_EN = "en";
    /**
     * item lang fr
     * fr
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_FR = "fr";
    /**
     * item lang it
     * it
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_IT = "it";
    /**
     * item lang de
     * de
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_DE = "de";
    /**
     * item lang es
     * es
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_ES = "es";
    /**
     * item lang zh
     * zh
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_ZH = "zh";
    /**
     * item lang ru
     * ru
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_RU = "ru";
    /**
     * item lang la
     * la
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_LA = "la";
    /**
     * item lang ms
     * ms
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_MS = "ms";
    /**
     * item lang eo
     * eo
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_EO = "eo";
    /**
     * item lang ar
     * ar
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_AR = "ar";
    /**
     * item lang el
     * el
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_EL = "el";
    /**
     * item lang ko
     * ko
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_KO = "ko";
    /**
     * item lang other
     * otherlanguage
     * アイテム言語
     * @var string 
     */
    const ITEM_LANG_OTHER = "otherlanguage";
    
    // -------------------------------------------
    // Item Attr Type display language
    // -------------------------------------------
    /**
     * item attr type lang ja
     * japanese
     * メタデータ項目の言語
     * @var string 
     */
    const ITEM_ATTR_TYPE_LANG_JA = "japanese";
    /**
     * item attr type lang en
     * english
     * メタデータ項目の言語
     * @var string 
     */
    const ITEM_ATTR_TYPE_LANG_EN = "english";
    
    // -------------------------------------------
    // Google Scholar
    // -------------------------------------------
    /**
     * Google scholar tag name
     * Google scholarタグ名
     *
     * @var string
     */
    const TAG_NAME_META = "meta";
    /**
     * Google scholar attribute name
     * Google scholar属性名
     *
     * @var string
     */
    const TAG_ATTR_KEY_NAME = "name";
    /**
     * Google scholar attribute name
     * Google scholar属性名
     *
     * @var string
     */
    const TAG_ATTR_KEY_CONTENT = "content";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_title
     * @var string 
     */
    const GOOGLESCHOLAR_TITLE = "citation_title";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_author
     * @var string 
     */
    const GOOGLESCHOLAR_AUTHOR = "citation_author";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_publication_date
     * @var string 
     */
    const GOOGLESCHOLAR_PUB_DATE = "citation_publication_date";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_online_date
     * @var string 
     */
    const GOOGLESCHOLAR_ONLINE_DATE = "citation_online_date";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_journal_title
     * @var string 
     */
    const GOOGLESCHOLAR_JOURNAL_TITLE = "citation_journal_title";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_volume
     * @var string 
     */
    const GOOGLESCHOLAR_VOLUME = "citation_volume";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_issue
     * @var string 
     */
    const GOOGLESCHOLAR_ISSUE = "citation_issue";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_firstpage
     * @var string 
     */
    const GOOGLESCHOLAR_FIRSTPAGE = "citation_firstpage";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_lastpage
     * @var string 
     */
    const GOOGLESCHOLAR_LASTPAGE = "citation_lastpage";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_pdf_url
     * @var string 
     */
    const GOOGLESCHOLAR_PDF_URL = "citation_pdf_url";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_abstract_html_url
     * @var string 
     */
    const GOOGLESCHOLAR_ABSTRACT_HTML_URL = "citation_abstract_html_url";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_fulltext_html_url
     * @var string 
     */
    const GOOGLESCHOLAR_FULLTEXT_HTML_URL = "citation_fulltext_html_url";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_doi
     * @var string 
     */
    const GOOGLESCHOLAR_DOI = "citation_doi";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_issn
     * @var string 
     */
    const GOOGLESCHOLAR_ISSN = "citation_issn";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_publisher
     * @var string 
     */
    const GOOGLESCHOLAR_PUBLISHER = "citation_publisher";
    /**
     * Google scholar name value
     * Google scholar name値
     * dc.Contributor
     * @var string 
     */
    const GOOGLESCHOLAR_DC_CONTRIBUTOR = "dc.Contributor";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_keywords
     * @var string 
     */
    const GOOGLESCHOLAR_KEYWORD = "citation_keywords";
    /**
     * Google scholar name value
     * Google scholar name値
     * citation_dissertation_institution
     * @var string 
     */
    const GOOGLESCHOLAR_DISSERTATION_INSTITUTION = "citation_dissertation_institution";
        
    // -------------------------------------------
    // Operation ID
    // -------------------------------------------
    /**
     * Log operation id(Entry item)
     * ログ操作ID(アイテム登録)
     * )
     * @var int
     */
    const LOG_OPERATION_ENTRY_ITEM = 1;
    /**
     * Log operation id(Download)
     * ログ操作ID(ダウンロード)
     * )
     * @var int
     */
    const LOG_OPERATION_DOWNLOAD_FILE = 2;
    /**
     * Log operation id(Detail)
     * ログ操作ID(詳細画面)
     * )
     * @var int
     */
    const LOG_OPERATION_DETAIL_VIEW = 3;
    /**
     * Log operation id(Search keyword)
     * ログ操作ID(キーワード検索)
     * )
     * @var int
     */
    const LOG_OPERATION_SEARCH = 4;
    /**
     * Log operation id(Top page)
     * ログ操作ID(トップページ)
     * )
     * @var int
     */
    const LOG_OPERATION_TOP = 5;
    /**
     * Log operation id(Ebook viewer)
     * ログ操作ID(EBook)
     * )
     * @var int
     */
    const LOG_OPERATION_EBOOK_VIEW = 6;
    
    // -------------------------------------------
    // Contributor(Posted agency)
    // -------------------------------------------
    // item_contributor column name
    /**
     * item contributor handle
     * handle
     * Contributor key name
     * 代理投稿者キー名
     * @var string 
     */
    const ITEM_CONTRIBUTOR_HANDLE = "handle";
    /**
     * item contributor name
     * name
     * Contributor key name
     * 代理投稿者キー名
     * @var string 
     */
    const ITEM_CONTRIBUTOR_NAME = "name";
    /**
     * item contributor email
     * email
     * Contributor key name
     * 代理投稿者キー名
     * @var string 
     */
    const ITEM_CONTRIBUTOR_EMAIL = "email";
    
    // Status for function "getUserIdForContributor" in Repository_Action_Main_Item_Editlinks
    /**
     * item contributor status success
     * Success
     * アイテム代理投稿者状態
     * @var string 
     */
    const ITEM_CONTRIBUTOR_STATUS_SUCCESS = "Success";
    /**
     * item contributor status notexist
     * NotExist
     * アイテム代理投稿者状態
     * @var string 
     */
    const ITEM_CONTRIBUTOR_STATUS_NOTEXIST = "NotExist";
    /**
     * item contributor status conflict
     * Conflict
     * アイテム代理投稿者状態
     * @var string 
     */
    const ITEM_CONTRIBUTOR_STATUS_CONFLICT = "Conflict";
    /**
     * item contributor status noauth
     * NoAuth
     * アイテム代理投稿者状態
     * @var string 
     */
    const ITEM_CONTRIBUTOR_STATUS_NOAUTH = "NoAuth";
    
    // -------------------------------------------
    // Session Parameter Name
    // -------------------------------------------
    // For item_contributor
    /**
     * session param org contributor user id
     * orgContributorUserId
     * セッションパラメータ名
     * @var string 
     */
    const SESSION_PARAM_ORG_CONTRIBUTOR_USER_ID = "orgContributorUserId";
    /**
     * session param contributor user id
     * contributorUserId
     * セッションパラメータ名
     * @var string 
     */
    const SESSION_PARAM_CONTRIBUTOR_USER_ID = "contributorUserId";
    /**
     * session param item contributor
     * item_contributor
     * セッションパラメータ名
     * @var string 
     */
    const SESSION_PARAM_ITEM_CONTRIBUTOR = "item_contributor";
    /**
     * session param contributor error msg
     * contributorErrorMsg
     * セッションパラメータ名
     * @var string 
     */
    const SESSION_PARAM_CONTRIBUTOR_ERROR_MSG = "contributorErrorMsg";
	
	// -------------------------------------------
    // File display_type
    // -------------------------------------------
    /**
     * file display type detail
     * ファイル詳細表示形式
     * @var string 
     */
    const FILE_DISPLAY_TYPE_DETAIL = "0";
    /**
     * file display type simple
     * ファイル簡易表示形式
     * @var string 
     */
    const FILE_DISPLAY_TYPE_SIMPLE = "1";
    /**
     * file display type flash
     * ファイルFLASH表示形式
     * @var string 
     */
    const FILE_DISPLAY_TYPE_FLASH = "2";
    
    // -------------------------------------------
    // File size data
    // -------------------------------------------
    /**
     * item data key file size
     * file_size
     * キー名
     * @var string 
     */
    const ITEM_DATA_KEY_FILE_SIZE = "file_size";
    /**
     * item data key file size full
     * file_size_full
     * キー名
     * @var string 
     */
    const ITEM_DATA_KEY_FILE_SIZE_FULL = "file_size_full";
    
    // Mod back from repository_item_type_confirm, icon is deleted 2012/02/15 T.Koyasu -start-
    // -------------------------------------------
    // status of edit icon
    // -------------------------------------------
    // For show icon
    // in Repository_View_Edit_Itemtype_Icon and Repository_Action_Edit_Itemtype_Icon
    /**
     * session param new item type
     * セッションパラメータ名
     * @var string 
     */
    const SESSION_PARAM_NEW_ITEM_TYPE = "0";
    /**
     * session param default icon
     * セッションパラメータ名
     * @var string 
     */
    const SESSION_PARAM_DEFAULT_ICON = "1";
    /**
     * session param database icon
     * セッションパラメータ名
     * @var string 
     */
    const SESSION_PARAM_DATABASE_ICON = "2";
    /**
     * session param upload icon
     * セッションパラメータ名
     * @var string 
     */
    const SESSION_PARAM_UPLOAD_ICON = "3";
    // Mod back from repository_item_type_confirm, icon is deleted 2012/02/15 T.Koyasu -end-
    
    // Add tree access control list 2012/02/24 T.Koyasu -start-
    /**
     * tree default exclusive acl role room
     * インデックスデフォルト閲覧ルーム権限
     * @var string 
     */
    const TREE_DEFAULT_EXCLUSIVE_ACL_ROLE_ROOM = "-1";
    // Add tree access control list 2012/02/24 T.Koyasu -end-
    
    // -------------------------------------------
    // column name for harvesting
    // -------------------------------------------
    /**
     * harvesting col header
     * HEADER
     * カラム名
     * @var string 
     */
    const HARVESTING_COL_HEADER = "HEADER";
    /**
     * harvesting col identifier
     * IDENTIFIER
     * カラム名
     * @var string 
     */
    const HARVESTING_COL_IDENTIFIER = "IDENTIFIER";
    /**
     * harvesting col headeridentifier
     * HEADERIDENTIFIER
     * カラム名
     * @var string 
     */
    const HARVESTING_COL_HEADERIDENTIFIER = "HEADERIDENTIFIER";
    /**
     * harvesting col datestamp
     * DATESTAMP
     * カラム名
     * @var string 
     */
    const HARVESTING_COL_DATESTAMP = "DATESTAMP";
    /**
     * harvesting col setspec
     * SETSPEC
     * カラム名
     * @var string 
     */
    const HARVESTING_COL_SETSPEC = "SETSPEC";
    /**
     * harvesting col setname
     * SETNAME
     * カラム名
     * @var string 
     */
    const HARVESTING_COL_SETNAME = "SETNAME";
    /**
     * harvesting col status
     * STATUS
     * カラム名
     * @var string 
     */
    const HARVESTING_COL_STATUS = "STATUS";
    
    // ----------------------------------------------
    // param name for repository_pdf_cover_patameter
    // ----------------------------------------------
    /**
     * pdf cover param name header type
     * headerType
     * PDFカバーページパラメータ名
     * @var string 
     */
    const PDF_COVER_PARAM_NAME_HEADER_TYPE = "headerType";
    /**
     * pdf cover param name header text
     * headerText
     * PDFカバーページパラメータ名
     * @var string 
     */
    const PDF_COVER_PARAM_NAME_HEADER_TEXT = "headerText";
    /**
     * pdf cover param name header image
     * headerImage
     * PDFカバーページパラメータ名
     * @var string 
     */
    const PDF_COVER_PARAM_NAME_HEADER_IMAGE = "headerImage";
    /**
     * pdf cover param name header align
     * headerAlign
     * PDFカバーページパラメータ名
     * @var string 
     */
    const PDF_COVER_PARAM_NAME_HEADER_ALIGN = "headerAlign";
    
    // ----------------------------------------------
    // repository_pdf_cover_patameter value
    // ----------------------------------------------
    /**
     * pdf cover header type text
     * text
     * PDFカバーページヘッダ形式
     * @var string 
     */
    const PDF_COVER_HEADER_TYPE_TEXT = "text";
    /**
     * pdf cover header type image
     * image
     * PDFカバーページヘッダ形式
     * @var string 
     */
    const PDF_COVER_HEADER_TYPE_IMAGE = "image";
    /**
     * pdf cover header align right
     * right
     * PDFカバーページヘッダ位置
     * @var string 
     */
    const PDF_COVER_HEADER_ALIGN_RIGHT = "right";
    /**
     * pdf cover header align center
     * center
     * PDFカバーページヘッダ位置
     * @var string 
     */
    const PDF_COVER_HEADER_ALIGN_CENTER = "center";
    /**
     * pdf cover header align left
     * left
     * PDFカバーページヘッダ位置
     * @var string 
     */
    const PDF_COVER_HEADER_ALIGN_LEFT = "left";
    
    // ----------------------------------------------
    // domain for usagestatistics
    // ----------------------------------------------
    /**
     * usagestatistics domain unknown
     * unknown
     * 利用統計画面ドメイン表示
     * @var string 
     */
    const USAGESTATISTICS_DOMAIN_UNKNOWN = "unknown";
    /**
     * usagestatistics domain com
     * com
     * 利用統計画面ドメイン表示
     * @var string 
     */
    const USAGESTATISTICS_DOMAIN_COM = "com";
    /**
     * usagestatistics domain org
     * org
     * 利用統計画面ドメイン表示
     * @var string 
     */
    const USAGESTATISTICS_DOMAIN_ORG = "org";
    /**
     * usagestatistics domain edu
     * edu
     * 利用統計画面ドメイン表示
     * @var string 
     */
    const USAGESTATISTICS_DOMAIN_EDU = "edu";
    /**
     * usagestatistics domain jp
     * jp
     * 利用統計画面ドメイン表示
     * @var string 
     */
    const USAGESTATISTICS_DOMAIN_JP = "jp";
    /**
     * usagestatistics domain us
     * us
     * 利用統計画面ドメイン表示
     * @var string 
     */
    const USAGESTATISTICS_DOMAIN_US = "us";
    /**
     * usagestatistics domain ac jp
     * ac.jp
     * 利用統計画面ドメイン表示
     * @var string 
     */
    const USAGESTATISTICS_DOMAIN_AC_JP = "ac.jp";
    /**
     * usagestatistics domain co jp
     * co.jp
     * 利用統計画面ドメイン表示
     * @var string 
     */
    const USAGESTATISTICS_DOMAIN_CO_JP = "co.jp";
    /**
     * usagestatistics domain go jp
     * go.jp
     * 利用統計画面ドメイン表示
     * @var string 
     */
    const USAGESTATISTICS_DOMAIN_GO_JP = "go.jp";
    
    // ----------------------------------------------
    // ffmpeg setting value
    // ----------------------------------------------
    /**
     * ffmpeg default sampling rate
     * 44100
     * ffpmegサンプリングレート
     * @var string 
     */
    const FFMPEG_DEFAULT_SAMPLING_RATE = "44100";
    /**
     * ffmpeg default sampling rate
     * 44100
     * ffpmegビットレート
     * @var string 
     */
    const FFMPEG_DEFAULT_BIT_RATE = "600000";
    
    // ----------------------------------------------
    // column value for repository_log table
    // ----------------------------------------------
    // file_status
    /**
     * log file status unknown
     * ファイルダウンロード状態(不明)
     * @var int
     */
    const LOG_FILE_STATUS_UNKNOWN = 0;
    /**
     * log file status open
     * ファイルダウンロード状態(オープンアクセス)
     * @var int 
     */
    const LOG_FILE_STATUS_OPEN = 1;
    /**
     * log file status close
     * ファイルダウンロード状態(非公開)
     * @var int 
     */
    const LOG_FILE_STATUS_CLOSE = -1;
    // site_locense
    /**
     * log site license off
     * 詳細画面表示、ダウンロード時、サイトライセンスOFF
     * @var int 
     */
    const LOG_SITE_LICENSE_OFF = 0;
    /**
     * log site license on
     * 詳細画面表示、ダウンロード時、サイトライセンスON
     * @var int 
     */
    const LOG_SITE_LICENSE_ON = 1;
    // input_type
    /**
     * log input type file
     * 入力タイプ=ファイル
     * @var int 
     */
    const LOG_INPUT_TYPE_FILE = 0;
    /**
     * log input type file
     * 入力タイプ=課金ファイル
     * @var int 
     */
    const LOG_INPUT_TYPE_FILE_PRICE = 1;
    // login_status
    /**
     * log login status no login
     * 未ログイン状態
     * @var int 
     */
    const LOG_LOGIN_STATUS_NO_LOGIN = 0;
    /**
     * log login status login
     * ログイン状態
     * @var int 
     */
    const LOG_LOGIN_STATUS_LOGIN = 1;
    /**
     * log login status group
     * ログイン状態(ダウンロード時、非会員グループ以外のグループによりダウンロード)
     * @var int 
     */
    const LOG_LOGIN_STATUS_GROUP = 2;
    /**
     * log login status register
     * ログイン状態(ダウンロード時、アイテムの登録者としてダウンロード)
     * @var int 
     */
    const LOG_LOGIN_STATUS_REGISTER = 5;
    /**
     * log login status admin
     * ログイン状態(ダウンロード時、管理者としてダウンロード)
     * @var int 
     */
    const LOG_LOGIN_STATUS_ADMIN = 10;
    
    // ----------------------------------------------
    // $this->getItemData
    // get item data access Key
    // ----------------------------------------------
    /**
     * item data key item
     * item
     * アイテム情報キー名
     * @var string 
     */
    const ITEM_DATA_KEY_ITEM = 'item';
    /**
     * item data key item type
     * item_type
     * アイテム情報キー名
     * @var string 
     */
    const ITEM_DATA_KEY_ITEM_TYPE = 'item_type';
    /**
     * item data key item attr type
     * item_attr_type
     * アイテム情報キー名
     * @var string 
     */
    const ITEM_DATA_KEY_ITEM_ATTR_TYPE = 'item_attr_type';
    /**
     * item data key item attr
     * item_attr
     * アイテム情報キー名
     * @var string 
     */
    const ITEM_DATA_KEY_ITEM_ATTR = 'item_attr';
    /**
     * item data key item reference
     * reference
     * アイテム情報キー名
     * @var string 
     */
    const ITEM_DATA_KEY_ITEM_REFERENCE = 'reference';
    /**
     * item data key position index
     * position_index
     * アイテム情報キー名
     * @var string 
     */
    const ITEM_DATA_KEY_POSITION_INDEX = 'position_index';
    
    // ----------------------------------------------
    // licence id and licence notation
    // ----------------------------------------------
    /**
     * licence id cc by
     * Creative CommonsライセンスID
     * @var int 
     */
    const LICENCE_ID_CC_BY = 101;
    /**
     * licence id cc by sa
     * Creative CommonsライセンスID
     * @var int 
     */
    const LICENCE_ID_CC_BY_SA = 102;
    /**
     * licence id cc by nd
     * Creative CommonsライセンスID
     * @var int 
     */
    const LICENCE_ID_CC_BY_ND = 103;
    /**
     * licence id cc by nc
     * Creative CommonsライセンスID
     * @var int 
     */
    const LICENCE_ID_CC_BY_NC = 104;
    /**
     * licence id cc by nc sa
     * Creative CommonsライセンスID
     * @var int 
     */
    const LICENCE_ID_CC_BY_NC_SA = 105;
    /**
     * licence id cc by nc nd
     * Creative CommonsライセンスID
     * @var int 
     */
    const LICENCE_ID_CC_BY_NC_ND = 106;
    /**
     * licence str cc by
     * CC BY
     * Creative Commonsライセンス名
     * @var string 
     */
    const LICENCE_STR_CC_BY = "CC BY";
    /**
     * licence str cc by sa
     * CC BY-SA
     * Creative Commonsライセンス名
     * @var string 
     */
    const LICENCE_STR_CC_BY_SA = "CC BY-SA";
    /**
     * licence str cc by nd
     * CC BY-ND
     * Creative Commonsライセンス名
     * @var string 
     */
    const LICENCE_STR_CC_BY_ND = "CC BY-ND";
    /**
     * licence str cc by nc
     * CC BY-NC
     * Creative Commonsライセンス名
     * @var string 
     */
    const LICENCE_STR_CC_BY_NC = "CC BY-NC";
    /**
     * licence str cc by nc sa
     * CC BY-NC-SA
     * Creative Commonsライセンス名
     * @var string 
     */
    const LICENCE_STR_CC_BY_NC_SA = "CC BY-NC-SA";
    /**
     * licence str cc by nc nd
     * CC BY-NC-ND
     * Creative Commonsライセンス名
     * @var string 
     */
    const LICENCE_STR_CC_BY_NC_ND = "CC BY-NC-ND";

    // ----------------------------------------------
    // new prefix ID
    // ----------------------------------------------
    /**
     * index jalc doi not set
     * DOI未設定
     * @var int 
     */
    const INDEX_JALC_DOI_NOT_SET = 0;
    /**
     * index jalc doi jalc
     * JaLC DOI設定済み
     * @var int 
     */
    const INDEX_JALC_DOI_JALC = 1;
    /**
     * index jalc doi cross ref
     * CrossRef DOI設定済み
     * @var int 
     */
    const INDEX_JALC_DOI_CROSS_REF = 2;
    
    // Bug fix WEKO-2014-031 T.Koyasu 2014/06/25 --start--
    // ----------------------------------------------
    // alternative language on repository_parameter table
    // ----------------------------------------------
    /**
     * param show alternative lang
     * 他言語表示
     * @var string 
     */
    const PARAM_SHOW_ALTERNATIVE_LANG = "1";
    /**
     * param hide alternative lang
     * 他言語非表示
     * @var string 
     */
    const PARAM_HIDE_ALTERNATIVE_LANG = "0";
    // Bug fix WEKO-2014-031 T.Koyasu 2014/06/25 --end--
    
    // -------------------------------------------
    // FPDF Header Status
    // -------------------------------------------
    /**
     * align left
     * L
     * FPDFヘッダ状態
     * @var string 
     */
    const ALIGN_LEFT = 'L';
    /**
     * align center
     * C
     * FPDFヘッダ状態
     * @var string 
     */
    const ALIGN_CENTER = 'C';
    /**
     * align right
     * R
     * FPDFヘッダ状態
     * @var string 
     */
    const ALIGN_RIGHT = 'R';
    /**
     * align centerleft
     * CL
     * FPDFヘッダ状態
     * @var string 
     */
    const ALIGN_CENTERLEFT = 'CL';
}
?>
