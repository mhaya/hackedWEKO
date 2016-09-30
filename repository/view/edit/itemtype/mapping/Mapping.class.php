<?php

/**
 * View class for the item type of mapping setting screen display
 * アイテムタイプのマッピング設定画面表示用ビュークラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Mapping.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Space mapping const class
 * SPASEマッピング定数クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/oaipmh/SpaseMappingConst.class.php';

/**
 * View class for the item type of mapping setting screen display
 * アイテムタイプのマッピング設定画面表示用ビュークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_View_Edit_Itemtype_Mapping extends RepositoryAction 
{
    // 使用コンポーネントを受け取るため
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
     * @var DbObjectAdodb
     */
    var $Db = null;
    
    // メンバ変数
    /**
     * Array of set NII type
     * NIIタイプ選択肢
     *
     * @var array
     */
    var $typeArray = null;
    /**
     * Array of set Dublin Core mapping
     * DublinCoreマッピング選択肢
     *
     * @var array
     */
    var $dublinCoreArray = null;
    /**
     * Array of set JuNii2 mapping
     * JuNii2マッピング選択肢
     *
     * @var array
     */
    var $junii2Array = null;
    /**
     * Array of set LoM mapping
     * LOMマッピング選択肢
     *
     * @var array
     */
    var $lomArray = null;
    /**
     * Array of set LIDO mapping
     * LIDOマッピング選択肢
     *
     * @var array
     */
    public $lidoArray = null;
    
    /**
     * Space mapping choices
     * Spaseマッピング選択肢
     *
     * @var array array[$ii]["displayName"|"selectFlag"]
     */
    public $spaseArray = null;

    /**
     * Help icon dispplay flag
     * ヘルプアイコン表示フラグ
     *
     * @var bool
     */
    var $help_icon_display =  null;
    
    /**
     * Execute
     * 実行
     *
     * @return string "success"/"error" success/failed 成功/失敗
     * @throws RepositoryException
     */
    function executeApp()
    {
        
        // Add theme_name for image file Y.Nakao 2011/08/03 --start--
        $this->setThemeName();
        // Add theme_name for image file Y.Nakao 2011/08/03 --end--
        
        //$this->setLangResource();
        $container =& DIContainerFactory::getContainer();
        $filterChain =& $container->getComponent("FilterChain");
        $smartyAssign =& $filterChain->getFilterByName("SmartyAssign");
        
        // マッピング選択肢を設定メンバに保存
        // ※項目一覧をDBから参照することもあるかもしれないのでDOMではやらない(・・・としておく。)	。
        
        // 0.アイテムタイプ名(type, NIItype)
        $this->setNiitype($this->typeArray);
        
        // 1.Dublin Core
        $this->setDublinCore($this->dublinCoreArray);
        
        // 2.JuNii2
        $this->setJunii2($this->junii2Array);
        
        // Add learning Object Material A.Jin -- start --
        // 3.LOM
        $this->setLom($this->lomArray);
        // Add learning Object Material A.Jin -- end --
        
        // Add LIDO R.Matsuura -- start --
        $this->setLido($this->lidoArray);
        // Add LIDO R.Matsuura -- end --

        // Add SPASE -- start --
        $this->setSpase($this->spaseArray);
        // Add SPASE -- end --

        // 4.表示言語
        $this->disp_lang_array = array(
            // '未設定', 'japanese', 'english'
            // languageリソースから項目を取得する
            array(" ", $smartyAssign->getLang("repository_language_no_mapping")),
            array("japanese", $smartyAssign->getLang("repository_language_ja")),
            array("english", $smartyAssign->getLang("repository_language_en"))
        );
        // Set help icon setting 2010/02/10 K.Ando --start--
        $result = $this->getAdminParam('help_icon_display', $this->help_icon_display, $Error_Msg);
        if ( $result == false ){
            $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
            $DetailMsg = null;                              //詳細メッセージ文字列作成
            sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
            $exception->setDetailMsg( $DetailMsg );         //詳細メッセージ設定
            $this->failTrans();                             //トランザクション失敗を設定(ROLLBACK)
            throw $exception;
        }
        // Set help icon setting 2010/02/10 K.Ando --end--
        
        return 'success';
    }
    
    /**
     * set Nii type
     * NIIタイプを設定する
     *
     * @param array $niiTypeCandidateArray NII type candidate array NIIタイプ選択肢配列
     * @return string "success" success 成功
     */
    private function setNiitype(&$niiTypeCandidateArray)
    {
        $container =& DIContainerFactory::getContainer();
        $filterChain =& $container->getComponent("FilterChain");
        $smartyAssign =& $filterChain->getFilterByName("SmartyAssign");
        
        $niiTypeCandidateArray = array(
        //  '未設定',
        //  'Journal Article','Thesis or Dissertation','Departmental',
        //  'Bulletin Paper','Conference Paper','Presentation','Book',
        //  'Technical Report','Research Paper','Article','Preprint',
        //  'Learning Material','Data or Dataset','Software','Others',
        
            //languageリソースから項目を取得する
            // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -start-
            "0",
            // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -end-
            $smartyAssign->getLang("repository_niitype_journal_article"),
            $smartyAssign->getLang("repository_niitype_thesis_or_dissertation"),
            $smartyAssign->getLang("repository_niitype_departmental_bulletin_paper"),
            $smartyAssign->getLang("repository_niitype_conference_paper"),
            $smartyAssign->getLang("repository_niitype_presentation"),
            $smartyAssign->getLang("repository_niitype_book"),
            $smartyAssign->getLang("repository_niitype_technical_report"),
            $smartyAssign->getLang("repository_niitype_research_paper"),
            $smartyAssign->getLang("repository_niitype_article"),
            $smartyAssign->getLang("repository_niitype_preprint"),
            $smartyAssign->getLang("repository_niitype_learning_material"),
            $smartyAssign->getLang("repository_niitype_data_or_dataset"),
            $smartyAssign->getLang("repository_niitype_software"),
            $smartyAssign->getLang("repository_niitype_others")
        );
        return "success";
    }
    
    /**
     * set Dublin Core type
     * DublinCoreマッピング選択肢を設定する
     *
     * @param array $dublinCoreCandidateArray Dublin Core candidate array DublinCoreマッピング選択肢配列
     * @return string "success" success 成功
     */
    private function setDublinCore(&$dublinCoreCandidateArray)
    {
        $container =& DIContainerFactory::getContainer();
        $filterChain =& $container->getComponent("FilterChain");
        $smartyAssign =& $filterChain->getFilterByName("SmartyAssign");
        
        $dublinCoreCandidateArray = array(
        //  '未設定',
        //  'title', 'creator', 'subject', 'description', 'publisher', 'contributor',
        //  'date', 'format', 'identifier', 'source', 'language',
        //  'Date', 'Type', 'Format', 'Identifier', 'Source', 'Language',   // 2008.02.22 typeはアイテムタイプ名にマッピングされるため、選択肢から削除
        //  'relation', 'coverage', 'rights'
            
            //languageリソースから項目を取得する
            // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -start-
            "0",
            // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -end-
            $smartyAssign->getLang("repository_dublin_core_title"),
            $smartyAssign->getLang("repository_dublin_core_creator"),
            $smartyAssign->getLang("repository_dublin_core_subject"),
            $smartyAssign->getLang("repository_dublin_core_description"),
            $smartyAssign->getLang("repository_dublin_core_publisher"),
            $smartyAssign->getLang("repository_dublin_core_contributor"),
            $smartyAssign->getLang("repository_dublin_core_date"),
            $smartyAssign->getLang("repository_dublin_core_type"),
            $smartyAssign->getLang("repository_dublin_core_format"),
            $smartyAssign->getLang("repository_dublin_core_identifier"),
            $smartyAssign->getLang("repository_dublin_core_source"),
            $smartyAssign->getLang("repository_dublin_core_language"),
            $smartyAssign->getLang("repository_dublin_core_relation"),
            $smartyAssign->getLang("repository_dublin_core_coverage"),
            $smartyAssign->getLang("repository_dublin_core_rights")
        );
        return "success";
    }
    
    /**
     * set JuNii2 type
     * JuNii2マッピング選択肢を設定する
     *
     * @param array $junii2CandidateArray JuNii2 candidate array JuNii2マッピング選択肢配列
     * @return string "success" success 成功
     */
    private function setJunii2(&$junii2CandidateArray)
    {
        $container =& DIContainerFactory::getContainer();
        $filterChain =& $container->getComponent("FilterChain");
        $smartyAssign =& $filterChain->getFilterByName("SmartyAssign");
        
        $junii2CandidateArray = array(
        //  '未設定',
        //  'title', 'alternative', 'creator', 'subject', 'NIIsubject', 
        //  'NDC', 'NDLC', 'BSH', 'NDLSH', 'MeSH', 'DDC', 'LCC', 
        //  'UDC', 'LCSH', 'description', 'publisher', 'contributor', 
        //'date', 'type', 'NIItype', 'format', 'identifier',            // 2008.02.22 NIItypeはアイテムタイプ名にマッピングされるため、選択肢から削除
        //  'date', 'type', 'format', 'identifier', 
        //  'URI', 'fullTextURL', 'issn', 'NCID', 'jtitle', 
        //  'volume', 'issue', 'spage', 'epage', 'dateofissued', 
        //  'source', 'language', 'relation', 'pmid', 'doi', 'isVersionOf', 
        //  'hasVersion', 'isReplacedBy', 'replaces', 'isRequiredBy', 
        //  'requires', 'isPartOf', 'hasPart', 'isReferencedBy', 
        //  'references', 'isFormatOf', 'hasFormat', 'coverage', 
        //  'spatial', 'NIIspatial', 'temporal', 'NIItemporal', 
        //  'rights', 'textversion'
            
            //languageリソースから項目を取得する
            // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -start-
            "0",
            // Mod insert 'undefine' to database in English 2012/02/14 T.Koyasu -end-
            $smartyAssign->getLang("repository_junii2_title"),
            $smartyAssign->getLang("repository_junii2_alternative"),
            $smartyAssign->getLang("repository_junii2_creator"),
            $smartyAssign->getLang("repository_junii2_subject"),
            $smartyAssign->getLang("repository_junii2_nii_subject"),
            $smartyAssign->getLang("repository_junii2_ndc"),
            $smartyAssign->getLang("repository_junii2_ndlc"),
            $smartyAssign->getLang("repository_junii2_bsh"),
            $smartyAssign->getLang("repository_junii2_ndlsh"),
            $smartyAssign->getLang("repository_junii2_mesh"),
            $smartyAssign->getLang("repository_junii2_ddc"),
            $smartyAssign->getLang("repository_junii2_lcc"),
            $smartyAssign->getLang("repository_junii2_udc"),
            $smartyAssign->getLang("repository_junii2_lcsh"),
            $smartyAssign->getLang("repository_junii2_description"),
            $smartyAssign->getLang("repository_junii2_publisher"),
            $smartyAssign->getLang("repository_junii2_contributor"),
            $smartyAssign->getLang("repository_junii2_date"),
            $smartyAssign->getLang("repository_junii2_type"),
            $smartyAssign->getLang("repository_junii2_format"),
            $smartyAssign->getLang("repository_junii2_identifier"),
            $smartyAssign->getLang("repository_junii2_uri"),
            $smartyAssign->getLang("repository_junii2_full_text_url"),
            RepositoryConst::JUNII2_ISBN,
            $smartyAssign->getLang("repository_junii2_issn"),
            $smartyAssign->getLang("repository_junii2_ncid"),
            $smartyAssign->getLang("repository_junii2_jtitle"),
            $smartyAssign->getLang("repository_junii2_volume"),
            $smartyAssign->getLang("repository_junii2_issue"),
            $smartyAssign->getLang("repository_junii2_spage"),
            $smartyAssign->getLang("repository_junii2_epage"),
            $smartyAssign->getLang("repository_junii2_date_of_issued"),
            $smartyAssign->getLang("repository_junii2_source"),
            $smartyAssign->getLang("repository_junii2_language"),
            $smartyAssign->getLang("repository_junii2_relation"),
            $smartyAssign->getLang("repository_junii2_pmid"),
            $smartyAssign->getLang("repository_junii2_doi"),
            RepositoryConst::JUNII2_NAID,
            RepositoryConst::JUNII2_ICHUSHI,
            $smartyAssign->getLang("repository_junii2_is_version_of"),
            $smartyAssign->getLang("repository_junii2_has_version"),
            $smartyAssign->getLang("repository_junii2_is_replaced_by"),
            $smartyAssign->getLang("repository_junii2_replaces"),
            $smartyAssign->getLang("repository_junii2_is_required_by"),
            $smartyAssign->getLang("repository_junii2_requires"),
            $smartyAssign->getLang("repository_junii2_is_part_of"),
            $smartyAssign->getLang("repository_junii2_has_part"),
            $smartyAssign->getLang("repository_junii2_is_referenced_by"),
            $smartyAssign->getLang("repository_junii2_references"),
            $smartyAssign->getLang("repository_junii2_is_format_of"),
            $smartyAssign->getLang("repository_junii2_has_format"),
            $smartyAssign->getLang("repository_junii2_coverage"),
            $smartyAssign->getLang("repository_junii2_spatial"),
            $smartyAssign->getLang("repository_junii2_nii_spatial"),
            $smartyAssign->getLang("repository_junii2_temporal"),
            $smartyAssign->getLang("repository_junii2_nii_temporal"),
            $smartyAssign->getLang("repository_junii2_rights"),
            $smartyAssign->getLang("repository_junii2_textversion"),
            RepositoryConst::JUNII2_GRANTID,
            RepositoryConst::JUNII2_DATEOFGRANTED,
            RepositoryConst::JUNII2_DEGREENAME,
            RepositoryConst::JUNII2_GRANTOR
        );
        return "success";
    }
    
    /**
     * set Learning Object Material
     * LOMマッピング選択肢を設定する
     *
     * @param array $lomCandidateArray LOM candidate array LOMマッピング選択肢配列
     * @return string "success" success 成功
     */
    private function setLom(&$lomCandidateArray)
    {
        $lomCandidateArray = array(
            //  '未設定',
            //languageリソースから項目を取得する
            "0",
            RepositoryConst::LOM_MAP_GNRL_IDENTIFER,
            RepositoryConst::LOM_MAP_GNRL_TITLE,
            RepositoryConst::LOM_MAP_GNRL_LANGUAGE,
            RepositoryConst::LOM_MAP_GNRL_DESCRIPTION,
            RepositoryConst::LOM_MAP_GNRL_KEYWORD,
            RepositoryConst::LOM_MAP_GNRL_COVERAGE,
            RepositoryConst::LOM_MAP_GNRL_STRUCTURE,
            RepositoryConst::LOM_MAP_GNRL_AGGREGATION_LEVEL,
            RepositoryConst::LOM_MAP_LFCYCL_VERSION,
            RepositoryConst::LOM_MAP_LFCYCL_STATUS,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_AUTHOR,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_PUBLISHER,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_PUBLISH_DATE,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_UNKNOWN,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_INITIATOR,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_TERMINATOR,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_VALIDATOR,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_EDITOR,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_GRAPHICAL_DESIGNER,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_TECHNICAL_IMPLEMENTER,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_CONTENT_PROVIDER,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_TECHNICAL_VALIDATOR,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_EDUCATIONAL_VALIDATOR,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_SCRIPT_WRITER,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_INSTRUCTIONAL_DESIGNER,
            RepositoryConst::LOM_MAP_LFCYCL_CONTRIBUTE_SUBJECT_MATTER_EXPERT,
            RepositoryConst::LOM_MAP_MTMTDT_IDENTIFER,
            RepositoryConst::LOM_MAP_MTMTDT_CONTRIBUTE,
            RepositoryConst::LOM_MAP_MTMTDT_CONTRIBUTE_CREATOR,
            RepositoryConst::LOM_MAP_MTMTDT_CONTRIBUTE_VALIDATOR,
            RepositoryConst::LOM_MAP_MTMTDT_METADATA_SCHEMA,
            RepositoryConst::LOM_MAP_MTMTDT_LANGUAGE,
            RepositoryConst::LOM_MAP_TCHNCL_FORMAT,
            RepositoryConst::LOM_MAP_TCHNCL_SIZE,
            RepositoryConst::LOM_MAP_TCHNCL_LOCATION,
            RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_TYPE,
            RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_NAME,
            RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MINIMUM_VERSION,
            RepositoryConst::LOM_MAP_TCHNCL_REQIREMENT_ORCOMPOSITE_MAXIMUM_VERSION,
            RepositoryConst::LOM_MAP_TCHNCL_INSTALLATION_REMARKS,
            RepositoryConst::LOM_MAP_TCHNCL_OTHER_PLATFORM_REQUIREMENTS,
            RepositoryConst::LOM_MAP_TCHNCL_DURATION,
            RepositoryConst::LOM_MAP_EDUCTNL_INTERACTIVITY_TYPE,
            RepositoryConst::LOM_MAP_EDUCTNL_LEARNING_RESOURCE_TYPE,
            RepositoryConst::LOM_MAP_EDUCTNL_INTERACTIVITY_LEVEL,
            RepositoryConst::LOM_MAP_EDUCTNL_SEMANTIC_DENSITY,
            RepositoryConst::LOM_MAP_EDUCTNL_INTENDED_END_USER_ROLE,
            RepositoryConst::LOM_MAP_EDUCTNL_CONTEXT,
            RepositoryConst::LOM_MAP_EDUCTNL_TYPICAL_AGE_RANGE,
            RepositoryConst::LOM_MAP_EDUCTNL_DIFFICULTY,
            RepositoryConst::LOM_MAP_EDUCTNL_TYPICAL_LEARNING_TIME,
            RepositoryConst::LOM_MAP_EDUCTNL_DESCRIPTION,
            RepositoryConst::LOM_MAP_EDUCTNL_LANGUAGE,
            RepositoryConst::LOM_MAP_RLTN,
            RepositoryConst::LOM_MAP_RLTN_IS_PART_OF,
            RepositoryConst::LOM_MAP_RLTN_HAS_PART_OF,
            RepositoryConst::LOM_MAP_RLTN_IS_VERSION_OF,
            RepositoryConst::LOM_MAP_RLTN_HAS_VERSION,
            RepositoryConst::LOM_MAP_RLTN_IS_FORMAT_OF,
            RepositoryConst::LOM_MAP_RLTN_HAS_FORMAT,
            RepositoryConst::LOM_MAP_RLTN_REFERENCES,
            RepositoryConst::LOM_MAP_RLTN_IS_REFERENCED_BY,
            RepositoryConst::LOM_MAP_RLTN_IS_BASED_ON,
            RepositoryConst::LOM_MAP_RLTN_IS_BASIS_FOR,
            RepositoryConst::LOM_MAP_RLTN_REQUIRES,
            RepositoryConst::LOM_MAP_RLTN_IS_REQUIRED_BY,
            RepositoryConst::LOM_MAP_RGHTS_COST,
            RepositoryConst::LOM_MAP_RGHTS_COPYRIGHT_AND_OTHER_RESTRICTIONS,
            RepositoryConst::LOM_MAP_RGHTS_DESCRIPTION,
            RepositoryConst::LOM_MAP_ANNTTN_ENTITY,
            RepositoryConst::LOM_MAP_ANNTTN_DATE,
            RepositoryConst::LOM_MAP_ANNTTN_DESCRIPTION,
            RepositoryConst::LOM_MAP_CLSSFCTN_PURPOSE,
            RepositoryConst::LOM_MAP_CLSSFCTN_DESCRIPTION,
            RepositoryConst::LOM_MAP_CLSSFCTN_KEYWORD,
            RepositoryConst::LOM_MAP_CLSSFCTN_TAXON_PATH_SOURCE,
            RepositoryConst::LOM_MAP_CLSSFCTN_TAXON
        );
        return "success";
    }
    
    /**
     * set LIDO
     * LIDOマッピング選択肢を設定する
     *
     * @param array $lidoCandidateArray LIDO candidate array LIDOマッピング選択肢配列
     * @return string "success" success 成功
     */
    private function setLido(&$lidoCandidateArray)
    {
        $lidoCandidateArray = array(
                "0", // 未設定
                array('displayName' => RepositoryConst::LIDO_TAG_LIDO_REC_ID, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_DESCRIPTIVE_METADATA.".".RepositoryConst::LIDO_TAG_OBJECT_CLASSIFICATION_WRAP, 'selectFlag' => 'false'),
                array('displayName' => RepositoryConst::LIDO_TAG_OBJECT_WORK_TYPE_WRAP.".".RepositoryConst::LIDO_TAG_OBJECT_WORK_TYPE.".".RepositoryConst::LIDO_TAG_CONCEPT_ID, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_OBJECT_WORK_TYPE_WRAP.".".RepositoryConst::LIDO_TAG_OBJECT_WORK_TYPE.".".RepositoryConst::LIDO_TAG_TERM, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_CLASSIFICATION_WRAP.".".RepositoryConst::LIDO_TAG_CLASSIFICATION.".".RepositoryConst::LIDO_TAG_CONCEPT_ID, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_CLASSIFICATION_WRAP.".".RepositoryConst::LIDO_TAG_CLASSIFICATION.".".RepositoryConst::LIDO_TAG_TERM, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_DESCRIPTIVE_METADATA.".".RepositoryConst::LIDO_TAG_OBJECT_IDENTIFICATION_WRAP, 'selectFlag' => 'false'),
                array('displayName' => RepositoryConst::LIDO_TAG_TITLE_WRAP.".".RepositoryConst::LIDO_TAG_TITLE_SET.".".RepositoryConst::LIDO_TAG_APPELLATION_VALUE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_INSCRIPTIONS_WRAP.".".RepositoryConst::LIDO_TAG_INSCRIPTIONS.".".RepositoryConst::LIDO_TAG_INSCRIPTION_TRANSCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_REPOSITORY_WRAP.".".RepositoryConst::LIDO_TAG_REPOSITORY_SET.".".RepositoryConst::LIDO_TAG_REPOSITORY_NAME.".".RepositoryConst::LIDO_TAG_LEGAL_BODY_NAME.".".RepositoryConst::LIDO_TAG_APPELLATION_VALUE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_REPOSITORY_WRAP.".".RepositoryConst::LIDO_TAG_REPOSITORY_SET.".".RepositoryConst::LIDO_TAG_REPOSITORY_NAME.".".RepositoryConst::LIDO_TAG_LEGAL_BODY_WEB_LINK, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_REPOSITORY_WRAP.".".RepositoryConst::LIDO_TAG_REPOSITORY_SET.".".RepositoryConst::LIDO_TAG_WORK_ID, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_DISPLAY_STATE_EDITION_WRAP.".".RepositoryConst::LIDO_TAG_DISPLAY_STATE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_OBJECT_DESCRIPTION_WRAP.".".RepositoryConst::LIDO_TAG_OBJECT_DESCRIPTION_SET.".".RepositoryConst::LIDO_TAG_DESCRIPTIVE_NOTE_VALUE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_OBJECT_MEASUREMENTS_WRAP.".".RepositoryConst::LIDO_TAG_OBJECT_MEASUREMENTS_SET.".".RepositoryConst::LIDO_TAG_DISPLAY_OBJECT_MEASUREMENTS, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_DESCRIPTIVE_METADATA.".".RepositoryConst::LIDO_TAG_EVENT_WRAP, 'selectFlag' => 'false'),
                array('displayName' => RepositoryConst::LIDO_TAG_EVENT_SET.".".RepositoryConst::LIDO_TAG_DISPLAY_EVENT, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_EVENT_SET.".".RepositoryConst::LIDO_TAG_EVENT.".".RepositoryConst::LIDO_TAG_EVENT_TYPE.".".RepositoryConst::LIDO_TAG_TERM, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_EVENT_SET.".".RepositoryConst::LIDO_TAG_EVENT.".".RepositoryConst::LIDO_TAG_EVENT_ACTOR.".".RepositoryConst::LIDO_TAG_DISPLAY_ACTOR_IN_ROLE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_EVENT_SET.".".RepositoryConst::LIDO_TAG_EVENT.".".RepositoryConst::LIDO_TAG_EVENT_DATE.".".RepositoryConst::LIDO_TAG_DISPLAY_DATE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_EVENT_SET.".".RepositoryConst::LIDO_TAG_EVENT.".".RepositoryConst::LIDO_TAG_EVENT_DATE.".".RepositoryConst::LIDO_TAG_DATE.".".RepositoryConst::LIDO_TAG_EARLIEST_DATE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_EVENT_SET.".".RepositoryConst::LIDO_TAG_EVENT.".".RepositoryConst::LIDO_TAG_EVENT_DATE.".".RepositoryConst::LIDO_TAG_DATE.".".RepositoryConst::LIDO_TAG_LATEST_DATE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_EVENT_SET.".".RepositoryConst::LIDO_TAG_EVENT.".".RepositoryConst::LIDO_TAG_PERIOD_NAME.".".RepositoryConst::LIDO_TAG_TERM, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_EVENT_SET.".".RepositoryConst::LIDO_TAG_EVENT.".".RepositoryConst::LIDO_TAG_EVENT_PLACE.".".RepositoryConst::LIDO_TAG_DISPLAY_PLACE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_EVENT_SET.".".RepositoryConst::LIDO_TAG_EVENT.".".RepositoryConst::LIDO_TAG_EVENT_PLACE.".".RepositoryConst::LIDO_TAG_PLACE.".".RepositoryConst::LIDO_TAG_GML, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_EVENT_SET.".".RepositoryConst::LIDO_TAG_EVENT.".".RepositoryConst::LIDO_TAG_EVENT_MATERIALS_TECH.".".RepositoryConst::LIDO_TAG_DISPLAY_MATERIALS_TECH, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_DESCRIPTIVE_METADATA.".".RepositoryConst::LIDO_TAG_OBJECT_RELATION_WRAP, 'selectFlag' => 'false'),
                array('displayName' => RepositoryConst::LIDO_TAG_SUBJECT_WRAP.".".RepositoryConst::LIDO_TAG_SUBJECT_SET.".".RepositoryConst::LIDO_TAG_DISPLAY_SUBJECT, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_RELATED_WORKS_WRAP.".".RepositoryConst::LIDO_TAG_RELATED_WORK_SET.".".RepositoryConst::LIDO_TAG_RELATED_WORK.".".RepositoryConst::LIDO_TAG_DISPLAY_OBJECT, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_ADMINISTRATIVE_METADATA.".".RepositoryConst::LIDO_TAG_RECORD_WRAP, 'selectFlag' => 'false'),
                array('displayName' => RepositoryConst::LIDO_TAG_RECORD_ID, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_RECORD_TYPE.".".RepositoryConst::LIDO_TAG_TERM, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_RECORD_SOURCE.".".RepositoryConst::LIDO_TAG_LEGAL_BODY_NAME.".".RepositoryConst::LIDO_TAG_APPELLATION_VALUE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_RECORD_INFO_SET.".".RepositoryConst::LIDO_TAG_RECORD_INFO_LINK, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_RECORD_INFO_SET.".".RepositoryConst::LIDO_TAG_RECORD_METADATA_DATE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_ADMINISTRATIVE_METADATA.".".RepositoryConst::LIDO_TAG_RESOURCE_WRAP, 'selectFlag' => 'false'),
                array('displayName' => RepositoryConst::LIDO_TAG_RESOURCE_SET.".".RepositoryConst::LIDO_TAG_RESOURCE_REPRESENTATION.".".RepositoryConst::LIDO_TAG_LINK_RESOURCE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_RESOURCE_SET.".".RepositoryConst::LIDO_TAG_RESOURCE_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_RESOURCE_SET.".".RepositoryConst::LIDO_TAG_RESOURCE_SOURCE.".".RepositoryConst::LIDO_TAG_LEGAL_BODY_NAME.".".RepositoryConst::LIDO_TAG_APPELLATION_VALUE, 'selectFlag' => 'true'),
                array('displayName' => RepositoryConst::LIDO_TAG_RESOURCE_SET.".".RepositoryConst::LIDO_TAG_RIGHT_RESOURCE.".".RepositoryConst::LIDO_TAG_CREDIT_LINE, 'selectFlag' => 'true')
        );

    }

    /**
     * To set the choice of Space mapping
     * Spaseマッピングの選択肢を設定する
     *
     * @param array $spaseCandidateArray Space mapping choices Spaseマッピング選択肢
     *                                   array[$ii]["displayName"|"selectFlag"]
     * @return string "success" success 成功
     */
    private function setSpase(&$spaseCandidateArray)
    {
        $spaseCandidateArray = array(
                "0", // 未設定
                array('displayName' => SpaseMappingConst::CATALOG_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_RESOURCEHEADER_RESOURCENAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_RESOURCEHEADER_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_RESOURCEHEADER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_RESOURCEHEADER_ACKNOWLEDGEMENT, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_RESOURCEHEADER_CONTACT_PERSONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_RESOURCEHEADER_CONTACT_ROLE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_RESOURCEHEADER_INFORMATIONURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_ACCESSINFORMATION_REPOSITORYID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_ACCESSINFORMATION_AVAILABILITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_ACCESSINFORMATION_ACCESSRIGHTS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_ACCESSINFORMATION_ACCESSURL_NAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_ACCESSINFORMATION_ACCESSURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_ACCESSINFORMATION_ACCESSURL_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_ACCESSINFORMATION_FORMAT, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_ACCESSINFORMATION_DATAEXTENT_QUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_INSTRUMENTID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PHENOMENONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_TIMESPAN_STARTDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_TIMESPAN_STOPDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_TIMESPAN_RELATIVESTOPDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_KEYWORD, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_NAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_COORDINATESYSTEM_COORDINATEREPRESENTATION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_COORDINATESYSTEM_COORDINATESYSTEMNAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_STRUCTURE_SIZE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_STRUCTURE_ELEMENT_NAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_STRUCTURE_ELEMENT_INDEX, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_FIELD_FIELDQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_FIELD_FREQUENCYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_FIELD_FREQUENCYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_FIELD_FREQUENCYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_FIELD_FREQUENCYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_FIELD_FREQUENCYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_PARTICLETYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_PARTICLEQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_ENERGYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_ENERGYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_ENERGYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_ENERGYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_ENERGYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_POLARANGLERANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_POLARANGLERANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_POLARANGLERANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_WAVETYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_WAVEQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_ENERGYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_ENERGYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_ENERGYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_ENERGYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_ENERGYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_FREQUENCYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_FREQUENCYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_FREQUENCYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_FREQUENCYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_FREQUENCYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_WAVELENGTHRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_WAVELENGTHRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_WAVELENGTHRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_MIXED_MIXEDQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::CATALOG_PARAMETER_SUPPORT_SUPPORTQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_RESOURCEHEADER_RESOURCENAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_RESOURCEHEADER_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_RESOURCEHEADER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_RESOURCEHEADER_ACKNOWLEDGEMENT, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_RESOURCEHEADER_CONTACT_PERSONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_RESOURCEHEADER_CONTACT_ROLE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_RESOURCEHEADER_INFORMATIONURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_ACCESSINFORMATION_REPOSITORYID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_ACCESSINFORMATION_AVAILABILITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_ACCESSINFORMATION_ACCESSRIGHTS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_ACCESSINFORMATION_ACCESSURL_NAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_ACCESSINFORMATION_ACCESSURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_ACCESSINFORMATION_ACCESSURL_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_ACCESSINFORMATION_FORMAT, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_ACCESSINFORMATION_DATAEXTENT_QUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_INSTRUMENTID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_MEASUREMENTTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_TEMPORALDESCRIPTION_TIMESPAN_STARTDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_TEMPORALDESCRIPTION_TIMESPAN_STOPDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_TEMPORALDESCRIPTION_TIMESPAN_RELATIVESTOPDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_OBSERVEDREGION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_KEYWORD, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_NAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_COORDINATESYSTEM_COORDINATEREPRESENTATION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_COORDINATESYSTEM_COORDINATESYSTEMNAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_STRUCTURE_SIZE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_STRUCTURE_ELEMENT_NAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_STRUCTURE_ELEMENT_INDEX, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_FIELD_FIELDQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_FIELD_FREQUENCYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_FIELD_FREQUENCYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_FIELD_FREQUENCYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_FIELD_FREQUENCYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_FIELD_FREQUENCYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_PARTICLETYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_PARTICLEQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_ENERGYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_ENERGYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_ENERGYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_ENERGYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_ENERGYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_POLARANGLERANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_POLARANGLERANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_POLARANGLERANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_WAVETYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_WAVEQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_ENERGYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_ENERGYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_ENERGYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_ENERGYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_ENERGYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_FREQUENCYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_FREQUENCYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_FREQUENCYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_FREQUENCYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_FREQUENCYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_WAVELENGTHRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_WAVELENGTHRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_WAVELENGTHRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_MIXED_MIXEDQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DISPLAYDATA_PARAMETER_SUPPORT_SUPPORTQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_RESOURCEHEADER_RESOURCENAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_RESOURCEHEADER_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_RESOURCEHEADER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_RESOURCEHEADER_ACKNOWLEDGEMENT, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_RESOURCEHEADER_CONTACT_PERSONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_RESOURCEHEADER_CONTACT_ROLE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_RESOURCEHEADER_INFORMATIONURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_ACCESSINFORMATION_REPOSITORYID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_ACCESSINFORMATION_AVAILABILITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_ACCESSINFORMATION_ACCESSRIGHTS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_ACCESSINFORMATION_ACCESSURL_NAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_ACCESSINFORMATION_ACCESSURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_ACCESSINFORMATION_ACCESSURL_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_ACCESSINFORMATION_FORMAT, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_ACCESSINFORMATION_DATAEXTENT_QUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_INSTRUMENTID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_MEASUREMENTTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_TEMPORALDESCRIPTION_TIMESPAN_STARTDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_TEMPORALDESCRIPTION_TIMESPAN_STOPDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_TEMPORALDESCRIPTION_TIMESPAN_RELATIVESTOPDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_OBSERVEDREGION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_KEYWORD, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_NAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_COORDINATESYSTEM_COORDINATEREPRESENTATION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_COORDINATESYSTEM_COORDINATESYSTEMNAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_STRUCTURE_SIZE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_STRUCTURE_ELEMENT_NAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_STRUCTURE_ELEMENT_INDEX, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_FIELD_FIELDQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_FIELD_FREQUENCYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_FIELD_FREQUENCYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_FIELD_FREQUENCYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_FIELD_FREQUENCYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_FIELD_FREQUENCYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_PARTICLETYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_PARTICLEQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_ENERGYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_ENERGYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_ENERGYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_ENERGYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_ENERGYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_POLARANGLERANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_POLARANGLERANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_POLARANGLERANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_WAVETYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_WAVEQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_ENERGYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_ENERGYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_ENERGYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_ENERGYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_ENERGYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_FREQUENCYRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_FREQUENCYRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_FREQUENCYRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_FREQUENCYRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_FREQUENCYRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_WAVELENGTHRANGE_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_WAVELENGTHRANGE_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_WAVELENGTHRANGE_UNITS, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_LOW, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_HIGH, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_MIXED_MIXEDQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::NUMERICALDATA_PARAMETER_SUPPORT_SUPPORTQUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_RESOURCEHEADER_RESOURCENAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_RESOURCEHEADER_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_RESOURCEHEADER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_RESOURCEHEADER_CONTACT_PERSONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_RESOURCEHEADER_CONTACT_ROLE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_RESOURCEHEADER_INFORMATIONURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_ACCESSINFORMATION_REPOSITORYID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_ACCESSINFORMATION_ACCESSURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_ACCESSINFORMATION_FORMAT, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_ACCESSINFORMATION_DATAEXTENT_QUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_DOCUMENTTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::DOCUMENT_MIMETYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::GRANULE_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::GRANULE_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::GRANULE_PARENTID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::GRANULE_STARTDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::GRANULE_STOPDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::GRANULE_SOURCE_SOURCETYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::GRANULE_SOURCE_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::GRANULE_SOURCE_CHECKSUM_HASHVALUE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::GRANULE_SOURCE_CHECKSUM_HASHFUNCTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::GRANULE_SOURCE_DATAEXTENT_QUANTITY, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_RESOURCEHEADER_RESOURCENAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_RESOURCEHEADER_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_RESOURCEHEADER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_RESOURCEHEADER_CONTACT_PERSONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_RESOURCEHEADER_CONTACT_ROLE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_RESOURCEHEADER_INFORMATIONURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_INSTRUMENTTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_INVESTIGATIONNAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_OPERATINGSPAN_STARTDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::INSTRUMENT_OBSERVATORYID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_RESOURCEHEADER_RESOURCENAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_RESOURCEHEADER_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_RESOURCEHEADER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_RESOURCEHEADER_CONTACT_PERSONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_RESOURCEHEADER_CONTACT_ROLE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_RESOURCEHEADER_INFORMATIONURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_LOCATION_OBSERVATORYREGION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::OBSERVATORY_OPERATINGSPAN_STARTDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::PERSON_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::PERSON_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::PERSON_PERSONNAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::PERSON_ORGANIZATIONNAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::PERSON_EMAIL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REGISTRY_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REGISTRY_RESOURCEHEADER_RESOURCENAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REGISTRY_RESOURCEHEADER_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REGISTRY_RESOURCEHEADER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REGISTRY_RESOURCEHEADER_CONTACT_PERSONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REGISTRY_RESOURCEHEADER_CONTACT_ROLE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REGISTRY_RESOURCEHEADER_INFORMATIONURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REGISTRY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REGISTRY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REGISTRY_ACCESSURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REPOSITORY_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REPOSITORY_RESOURCEHEADER_RESOURCENAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REPOSITORY_RESOURCEHEADER_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REPOSITORY_RESOURCEHEADER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REPOSITORY_RESOURCEHEADER_CONTACT_PERSONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REPOSITORY_RESOURCEHEADER_CONTACT_ROLE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REPOSITORY_RESOURCEHEADER_INFORMATIONURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REPOSITORY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REPOSITORY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::REPOSITORY_ACCESSURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::SERVICE_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::SERVICE_RESOURCEHEADER_RESOURCENAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::SERVICE_RESOURCEHEADER_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::SERVICE_RESOURCEHEADER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::SERVICE_RESOURCEHEADER_CONTACT_PERSONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::SERVICE_RESOURCEHEADER_CONTACT_ROLE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::SERVICE_RESOURCEHEADER_INFORMATIONURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::SERVICE_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::SERVICE_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::SERVICE_ACCESSURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_RESOURCEID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_RESOURCEHEADER_RESOURCENAME, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_RESOURCEHEADER_RELEASEDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_RESOURCEHEADER_DESCRIPTION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_RESOURCEHEADER_CONTACT_PERSONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_RESOURCEHEADER_CONTACT_ROLE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_RESOURCEHEADER_INFORMATIONURL_URL, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_ANNOTATIONTYPE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_TIMESPAN_STARTDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_TIMESPAN_STOPDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_TIMESPAN_RELATIVESTOPDATE, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_OBSERVATIONEXTENT_STARTLOCATION, 'selectFlag' => 'true'),
                array('displayName' => SpaseMappingConst::ANNOTATION_OBSERVATIONEXTENT_STOPLOCATION, 'selectFlag' => 'true')
        );
    }
}
?>
