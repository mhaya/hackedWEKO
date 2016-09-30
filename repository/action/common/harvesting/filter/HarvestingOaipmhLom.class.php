<?php
/**
 * Class for Harvest item registration (LOM)
 * ハーベストアイテム登録用クラス(LOM)
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: HarvestingOaipmhLom.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * String format conversion common classes
 * 文字列形式変換共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryOutputFilter.class.php';

/**
 * LOM item type attribute ID constant class
 * LOMアイテムタイプ属性ID定数クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class AttrId
{
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_ID_URI = 1;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_ID_ISSN = 2;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_ID_NCID = 3;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_ID_BIBINFO = 4;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_ID_TEXTVERSION = 5;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_IDENTIFIER = 6;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_LANGUAGE = 7;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_DESCRIPTION = 8;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_COVERGE = 9;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_STRUCTURE = 10;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const GEN_AGGLEVEL = 11;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_VERSION = 12;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_STATUS = 13;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_CREATOR = 14;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_PUBLISHER = 15;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_PUB_DATE = 16;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_INITIATOR = 17;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_TERMINATOR = 18;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_VALIDATOR = 19;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_EDITOR = 20;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_GRA_DESIGNER = 21;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_TEC_IMPLEMENTER = 22;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_CNT_PROVIDER = 23;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_TEC_VALIDATOR = 24;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_EDU_VALIDATOR = 25;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_SCRIPT_WRITER = 26;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_INST_DESIGNER = 27;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_SUBJ_MATTER_EXPERT = 28;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CON_UNKNOWN = 29;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const LC_CONTRIBUTE = 30;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const MM_IDENTIFIER = 31;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const MM_CON_CREATOR = 32;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const MM_CON_VALIDATOR = 33;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const MM_CONTRIBUTE = 34;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const MM_META_SCHEMA = 35;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const MM_LANGUAGE = 36;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const TEC_FORMAT = 37;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const TEC_SIZE = 38;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const TEC_LOCATION = 39;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const TEC_REQ_ORCOMP_TYPE = 40;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const TEC_REQ_ORCOMP_NAME = 41;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const TEC_REQ_ORCOMP_MINVERSION = 42;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const TEC_REQ_ORCOMP_MAXVERSION = 43;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const TEC_INSTALL_REMARKS = 44;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const TEC_OTHER_PLATFORM_REQ = 45;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const TEC_DURATION = 46;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_INTERACTIVITY_TYPE = 47;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_LEARN_RESOURCE_TYPE = 48;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_INTERACTIVITY_LEVEL = 49;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_SEMANTIC_DENSITY = 50;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_INT_END_USER_ROLE = 51;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_CONTEXT = 52;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_TYP_AGE_RANGE = 53;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_DIFFICULTY = 54;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_TYP_LEARN_TIME = 55;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_DESCRIPTION = 56;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const EDU_LANGUAGE = 57;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const RIT_COST = 58;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const RIT_CPRIT_OTHRER_REST = 59;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const RIT_DESCRIPTION = 60;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_PMID = 61;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_DOI = 62;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_ISVERSIONOF = 63;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_HASVERSION = 64;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_ISREQUIREDBY = 65;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_REQUIRES = 66;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_ISPARTOF = 67;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_HASPART = 68;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_ISREFERENCEDBY = 69;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_REFERENCES = 70;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_ISFORMATOF = 71;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_HASFORMAT = 72;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_ISBASISFOR = 73;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_ISBASEDON = 74;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REL_RELATION = 75;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const ANO_ENTITY = 76;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const ANO_DATE = 77;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const ANO_DESCRIPTION = 78;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const CLS_PURPOSE = 79;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const CLS_TAXONPATH_SOURCE = 80;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const CLS_TAXONPATH_TAXON = 81;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const CLS_DESCRIPTION = 82;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const CLS_KEYWORD = 83;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const REPO_ID = 84;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const IDENTIFIER = 85;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const DATESTAMP = 86;
    
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const MIN_ID = self::GEN_ID_URI;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const MAX_ID = self::DATESTAMP;
    /**
     * Attribute id
     * 属性ID
     * 
     * @var int
     */
    const MAX_ATTR_ID = 87;
}

/**
 * Class for Harvest item registration (LOM)
 * ハーベストアイテム登録用クラス(LOM)
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class HarvestingOaipmhLom extends RepositoryAction
{
    // ---------------------------------------------
    // Const
    // ---------------------------------------------
    // Itemtype data
    /**
     * Item type id
     * アイテムタイプID
     * 
     * @var int
     */
    const ITEMTYPE_ID = 20016;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_LINK = RepositoryConst::ITEM_ATTR_TYPE_LINK;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_TEXT = RepositoryConst::ITEM_ATTR_TYPE_TEXT;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_BIBLIOINFO = RepositoryConst::ITEM_ATTR_TYPE_BIBLIOINFO;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_SELECT = RepositoryConst::ITEM_ATTR_TYPE_SELECT;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_TEXTAREA = RepositoryConst::ITEM_ATTR_TYPE_TEXTAREA;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_NAME = RepositoryConst::ITEM_ATTR_TYPE_NAME;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_DATE = RepositoryConst::ITEM_ATTR_TYPE_DATE;
    /**
     * Input type
     * 入力タイプ
     * 
     * @var string
     */
    const INPUT_TYPE_CHECKBOX = RepositoryConst::ITEM_ATTR_TYPE_CHECKBOX;
    
    // Tags
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_GENERAL = RepositoryConst::LOM_TAG_GENERAL;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_LIFE_CYCLE = RepositoryConst::LOM_TAG_LIFE_CYCLE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_META_METADATA = RepositoryConst::LOM_TAG_META_METADATA;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_TECHNICAL = RepositoryConst::LOM_TAG_TECHNICAL;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_EDUCATIONAL = RepositoryConst::LOM_TAG_EDUCATIONAL;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_RIGHTS = RepositoryConst::LOM_TAG_RIGHTS;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_RELATION = RepositoryConst::LOM_TAG_RELATION;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ANNOTAION = RepositoryConst::LOM_TAG_ANNOTAION;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_CLASSIFICATION = RepositoryConst::LOM_TAG_CLASSIFICATION;
    
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_IDENTIFIER = RepositoryConst::LOM_TAG_IDENTIFIER;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_CATALOG = RepositoryConst::LOM_TAG_CATALOG;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ENTRY = RepositoryConst::LOM_TAG_ENTRY;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_TITLE = RepositoryConst::LOM_TAG_TITLE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_LANGUAGE = RepositoryConst::LOM_TAG_LANGUAGE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_DESCRIPTION = RepositoryConst::LOM_TAG_DESCRIPTION;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_KEYWORD = RepositoryConst::LOM_TAG_KEYWORD;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_COVERAGE = RepositoryConst::LOM_TAG_COVERAGE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_STRUCTURE = RepositoryConst::LOM_TAG_STRUCTURE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_AGGREGATION_LEVEL = RepositoryConst::LOM_TAG_AGGREGATION_LEVEL;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_VERSION = RepositoryConst::LOM_TAG_VERSION;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_STATUS = RepositoryConst::LOM_TAG_STATUS;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_CONTRIBUTE = RepositoryConst::LOM_TAG_CONTRIBUTE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ROLE = RepositoryConst::LOM_TAG_ROLE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ENTITY = RepositoryConst::LOM_TAG_ENTITY;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_DATE = RepositoryConst::LOM_TAG_DATE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_METADATA_SCHEMA = RepositoryConst::LOM_TAG_METADATA_SCHEMA;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_FORMAT = RepositoryConst::LOM_TAG_FORMAT;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_SIZE = RepositoryConst::LOM_TAG_SIZE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_LOCATION = RepositoryConst::LOM_TAG_LOCATION;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_REQUIREMENT = RepositoryConst::LOM_TAG_REQUIREMENT;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_OR_COMPOSITE = RepositoryConst::LOM_TAG_OR_COMPOSITE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_TYPE = RepositoryConst::LOM_TAG_TYPE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_NAME = RepositoryConst::LOM_TAG_NAME;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_MINIMUM_VERSION = RepositoryConst::LOM_TAG_MINIMUM_VERSION;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_MAXIMUM_VERSION = RepositoryConst::LOM_TAG_MAXIMUM_VERSION;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_INSTALLATION_REMARKS = RepositoryConst::LOM_TAG_INSTALLATION_REMARKS;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_OTHER_PLATFORM_REQIREMENTS = RepositoryConst::LOM_TAG_OTHER_PLATFORM_REQIREMENTS;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_DURATION = RepositoryConst::LOM_TAG_DURATION;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_INTERACTIVITY_TYPE = RepositoryConst::LOM_TAG_INTERACTIVITY_TYPE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_LEARNING_RESOURCE_TYPE = RepositoryConst::LOM_TAG_LEARNING_RESOURCE_TYPE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_INTERACTIVITY_LEVEL = RepositoryConst::LOM_TAG_INTERACTIVITY_LEVEL;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_SEMANTIC_DENSITY = RepositoryConst::LOM_TAG_SEMANTIC_DENSITY;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_INTENDED_END_USER_ROLE = RepositoryConst::LOM_TAG_INTENDED_END_USER_ROLE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_CONTEXT = RepositoryConst::LOM_TAG_CONTEXT;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_TYPICAL_AGE_RANGE = RepositoryConst::LOM_TAG_TYPICAL_AGE_RANGE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_DIFFICULTY = RepositoryConst::LOM_TAG_DIFFICULTY;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_TYPICAL_LEARNING_TIME = RepositoryConst::LOM_TAG_TYPICAL_LEARNING_TIME;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_COST = RepositoryConst::LOM_TAG_COST;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_COPYRIGHT_AND_OTHER_RESTRICTIONS = RepositoryConst::LOM_TAG_COPYRIGHT_AND_OTHER_RESTRICTIONS;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_KIND = RepositoryConst::LOM_TAG_KIND;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_RESOURCE = RepositoryConst::LOM_TAG_RESOURCE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_PURPOSE = RepositoryConst::LOM_TAG_PURPOSE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_TAXON_PATH = RepositoryConst::LOM_TAG_TAXON_PATH;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_SOURCE = RepositoryConst::LOM_TAG_SOURCE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_TAXON = RepositoryConst::LOM_TAG_TAXON;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_ID = RepositoryConst::LOM_TAG_ID;
    
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_LANGSTR_LANG = RepositoryConst::LOM_TAG_LANGUAGE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_LANGSTR_STR = RepositoryConst::LOM_TAG_STRING;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_VOCAB_SRC = RepositoryConst::LOM_TAG_SOURCE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_VOCAB_VAL = RepositoryConst::LOM_TAG_VALUE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_DATE_TIME = RepositoryConst::LOM_TAG_DATE_TIME;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_DUR_DURATION = RepositoryConst::LOM_TAG_DURATION;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TAG_DUR_DESCRIPTION = RepositoryConst::LOM_TAG_DESCRIPTION;
    
    // Error / Warning message
    /**
     * Error message
     * エラーメッセージ
     * 
     * @var string
     */
    const MSG_ER_GET_TITLE = "repository_harvesting_error_get_title";
    /**
     * Warning message
     * 警告メッセージ
     * 
     * @var string
     */
    const MSG_WN_MISS_LANGAGE = "repository_harvesting_warning_miss_language";
    
    // Log status
    /**
     * Status
     * 状態
     * 
     * @var int
     */
    const LOG_STATUS_OK = RepositoryConst::HARVESTING_LOG_STATUS_OK;
    /**
     * Status
     * 状態
     * 
     * @var int
     */
    const LOG_STATUS_WARNING = RepositoryConst::HARVESTING_LOG_STATUS_WARNING;
    /**
     * Status
     * 状態
     * 
     * @var int
     */
    const LOG_STATUS_ERROR = RepositoryConst::HARVESTING_LOG_STATUS_ERROR;
    
    // Metadata array for ItemRegister
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_IR_BASIC = "irBasic";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_IR_METADATA = "irMetadata";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ITEM_ID = "item_id";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ITEM_NO = "item_no";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ITEM_TYPE_ID = "item_type_id";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_TITLE = "title";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_TITLE_EN = "title_english";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_LANGUAGE = "language";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_PUB_YEAR = "pub_year";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_PUB_MONTH = "pub_month";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_PUB_DAY = "pub_day";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_SEARCH_KEY = "serch_key";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_SEARCH_KEY_EN = "serch_key_english";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ATTR_ID = "attribute_id";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ATTR_NO = "attribute_no";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_INPUT_TYPE = "input_type";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ATTR_VALUE = "attribute_value";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_FAMILY = "family";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_NAME = "name";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_FAMILY_RUBY = "family_ruby";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_NAME_RUBY = "name_ruby";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_EMAIL = "e_mail_address";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_AUTHOR_ID = "author_id";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_NAME_NO = "personal_name_no";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_BIBLIO_NAME = "biblio_name";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_BIBLIO_NAME_EN = "biblio_name_english";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_VOLUME = "volume";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_ISSUE = "issue";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_SPAGE = "start_page";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_EPAGE = "end_page";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_DATE_OF_ISSUED = "date_of_issued";
    /**
     * Key name
     * キー名
     * 
     * @var string
     */
    const KEY_BIBLIO_NO = "biblio_no";
    
    // For General identifier catalog
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_URI = RepositoryConst::LOM_URI;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_ISSN = RepositoryConst::LOM_ISSN;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_NCID = RepositoryConst::LOM_NCID;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_JTITLE = RepositoryConst::LOM_JTITLE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_VOLUME = RepositoryConst::LOM_VOLUME;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_ISSUE = RepositoryConst::LOM_ISSUE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_SPAGE = RepositoryConst::LOM_SPAGE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_EPAGE = RepositoryConst::LOM_EPAGE;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_DATEOFISSUED = RepositoryConst::LOM_DATE_OF_ISSUED;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_TEXTVERSION = RepositoryConst::LOM_TEXTVERSION;
    
    // For General identifier textversion value
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TEXTVERSION_AUTHOR = "author";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TEXTVERSION_PUBLISHER = "publisher";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TEXTVERSION_NONE = "none";
    
    // For LifeCycle role value
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_AUTHOR = "author";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_PUBLISHER = "publisher";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_INITIATOR = "initiator";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_TERMINATOR = "terminator";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_VALIDATOR = "validator";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_EDITOR = "editor";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_GRA_DESIGNER = "graphical designer";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_TEC_IMPLEMENTER = "technical implementer";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_CNT_PROVIDER = "content provider";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_TEC_VALIDATOR = "technical validator";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_EDU_VALIDATOR = "educational validator";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_SCRIPT_WRITER = "script writer";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_INST_DESIGNER = "instructional designer";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_SUBJ_MATTER_EXPERT = "subject matter expert";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_LC_UNKNOWN = "unknown";
    
    // For Meta-Metadata role value
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_MM_CREATOR = "creator";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const ROLE_MM_VALIDATOR = "validator";
    
    // For Technical requirement orComposite type value
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TYPE_OPERATING_SYSTEM = "operating system";
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const TYPE_BROWSER = "browser";
    
    // For Relation kind value
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_ISPARTOF = RepositoryConst::LOM_IS_PART_OF;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_HASPART = RepositoryConst::LOM_HAS_PART;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_ISVERSIONOF = RepositoryConst::LOM_IS_VERSION_OF;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_HASVERSION = RepositoryConst::LOM_HAS_VERSION;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_ISFORMATOF = RepositoryConst::LOM_IS_FORMAT_OF;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_HASFORMAT = RepositoryConst::LOM_HAS_FORMAT;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_REFERENCES = RepositoryConst::LOM_REFERENCES;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_ISREFERENCEDBY = RepositoryConst::LOM_IS_REFERENCED_BY;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_ISBASEDON = RepositoryConst::LOM_IS_BASED_ON;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_ISBASISFOR = RepositoryConst::LOM_IS_BASIS_FOR;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_REQUIRES = RepositoryConst::LOM_REQUIRES;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const KIND_ISREQUIREDBY = RepositoryConst::LOM_IS_REQUIRESD_BY;
    
    // For Relation resource identifier catalog
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_PMID = RepositoryConst::LOM_PMID;
    /**
     * Tag name
     * タグ名
     * 
     * @var string
     */
    const CATALOG_DOI = RepositoryConst::LOM_DOI;
    
    // Others
    /**
     * Delimiter
     * 区切り文字
     * 
     * @var string
     */
    const IDENTIFIER_DELIMITER = ":";
    /**
     * Delimiter
     * 区切り文字
     * 
     * @var string
     */
    const TAXON_DELIMITER = ":";
    /**
     * Delimiter
     * 区切り文字
     * 
     * @var string
     */
    const NAME_DELIMITER = " ";
    /**
     * Language
     * 言語
     * 
     * @var string
     */
    const ITEM_LANG_JA = RepositoryConst::ITEM_LANG_JA;
    /**
     * Language
     * 言語
     * 
     * @var string
     */
    const ITEM_LANG_EN = RepositoryConst::ITEM_LANG_EN;
    /**
     * Language
     * 言語
     * 
     * @var string
     */
    const DEFAULT_LANGUAGE = RepositoryConst::ITEM_LANG_OTHER;
    
    // Database
    /**
     * Table name
     * テーブル名
     * 
     * @var string
     */
    const DB_ITEM = RepositoryConst::DBTABLE_REPOSITORY_ITEM;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ITEM_ID = RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ITEM_NO = RepositoryConst::DBCOL_REPOSITORY_ITEM_ITEM_NO;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_IS_DELETE = RepositoryConst::DBCOL_COMMON_IS_DELETE;
    /**
     * Table name
     * テーブル名
     * 
     * @var string
     */
    const DB_ITEM_ATTR = RepositoryConst::DBTABLE_REPOSITORY_ITEM_ATTR;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ITEM_ID = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ITEM_NO = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_NO;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ATTR_ID = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ATTR_NO = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_NO;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ATTR_VAL = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_VALUE;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_ITEM_ATTR_ITEM_TYPE_ID = RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_TYPE_ID;
    /**
     * Table name
     * テーブル名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME = RepositoryConst::DBTABLE_REPOSITORY_PERSONAL_NAME;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_ITEM_ID = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_ITEM_NO = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_NO;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_ATTR_ID = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ATTRIBUTE_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_NAME_NO = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_PERSONAL_NAME_NO;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_FAMILY = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_FAMILY;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_NAME = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_NAME;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_FAMILY_RUBY = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_FAMILY_RUBY;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_NAME_RUBY = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_NAME_RUBY;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_EMAIL_ADDRES = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_E_MAIL_ADDRESS;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_ITEM_TYPE_ID = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_TYPE_ID;
    /**
     * Column name
     * カラム名
     * 
     * @var string
     */
    const DB_PERSONAL_NAME_AUTHOR_ID = RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_AUTHOR_ID;
    
    
    // ---------------------------------------------
    // Private member
    // ---------------------------------------------
    /**
     * Value list
     * XML文字列一覧
     * 
     * @var array[$ii]
     */
    private $generalXmlStrArray = array();
    /**
     * Value list
     * XML文字列一覧
     * 
     * @var array[$ii]
     */
    private $lifeCycleXmlStrArray = array();
    /**
     * Value list
     * XML文字列一覧
     * 
     * @var array[$ii]
     */
    private $metaMetadataXmlStrArray = array();
    /**
     * Value list
     * XML文字列一覧
     * 
     * @var array[$ii]
     */
    private $technicalXmlStrArray = array();
    /**
     * Value list
     * XML文字列一覧
     * 
     * @var array[$ii]
     */
    private $educationalXmlStrArray = array();
    /**
     * Value list
     * XML文字列一覧
     * 
     * @var array[$ii]
     */
    private $rightsXmlStrArray = array();
    /**
     * Value list
     * XML文字列一覧
     * 
     * @var array[$ii]
     */
    private $relationXmlStrArray = array();
    /**
     * Value list
     * XML文字列一覧
     * 
     * @var array[$ii]
     */
    private $annotationXmlStrArray = array();
    /**
     * Value list
     * XML文字列一覧
     * 
     * @var array[$ii]
     */
    private $classificationXmlStrArray = array();
    /**
     * Metadata count list
     * メタデータ数一覧
     * 
     * @var array[$ii]
     */
    private $cntMetadata = array();
    /**
     * Duplicate check list
     * 競合一覧
     * 
     * @var array[$ii]
     */
    private $chkDuplication = array();
    
    // ---------------------------------------------
    // Constructor
    // ---------------------------------------------
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $Session Session object セッション管理オブジェクト
     * @param DbObject $Db Database object データベース管理オブジェクト
     */
    public function HarvestingOaipmhLom($Session, $Db){
        $this->Session = $Session;
        $this->Db = $Db;
        $this->initMember();
    }
    
    // ---------------------------------------------
    // Private method
    // ---------------------------------------------
    /**
     * Init data
     * 初期化
     */
    private function initMember()
    {
        $this->generalXmlStrArray = array();
        $this->lifeCycleXmlStrArray = array();
        $this->metaMetadataXmlStrArray = array();
        $this->technicalXmlStrArray = array();
        $this->educationalXmlStrArray = array();
        $this->rightsXmlStrArray = array();
        $this->relationXmlStrArray = array();
        $this->annotationXmlStrArray = array();
        $this->classificationXmlStrArray = array();
        $this->cntMetadata = array();
        for($ii=AttrId::MIN_ID; $ii<=AttrId::MAX_ID; $ii++)
        {
            $this->cntMetadata[$ii] = 0;
        }
        $this->chkDuplication = array(
            // Input_type: checkbox
            AttrId::EDU_INTERACTIVITY_TYPE => array(),
            AttrId::EDU_LEARN_RESOURCE_TYPE => array(),
            AttrId::EDU_INTERACTIVITY_LEVEL => array(),
            AttrId::EDU_SEMANTIC_DENSITY => array(),
            AttrId::EDU_INT_END_USER_ROLE => array(),
            AttrId::EDU_CONTEXT => array(),
            AttrId::EDU_DIFFICULTY => array(),
            AttrId::CLS_PURPOSE => array()
        );
    }
    
    /**
     * Parse xml
     * XMLパース
     *
     * @param string $xml XML XML
     * @param array $vals Value list 値一覧
     *                    array[$ii]
     * @return boolean Result 結果
     */
    private function parseXml($xml, &$vals)
    {
        // parse xml
        try
        {
            $vals = array();
            $xml_parser = xml_parser_create();
            xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
            $ret = xml_parse_into_struct($xml_parser, $xml, $vals);
            xml_parser_free($xml_parser);
            if($ret == 0)
            {
                return false;
            }
        }
        catch(Exception $ex)
        {
            return false;
        }
        return true;
    }
    
    /**
     * Get xml tag value
     * XMLタグ名取得
     *
     * @param string $xml XML XML
     * @param string $tagName Tag name
     * @return string Tag name タグ名
     */
    private function getXmlTagValue($xml, $tagName)
    {
        $pattern = "/(\<$tagName\>.*?\<\/$tagName\>)/";
        preg_match_all($pattern, $xml, $matches);
        return $matches[1];
    }
    
    /**
     * Set basic metadata xml string
     * アイテム基本情報設定
     *
     * @param string $xml XML XML
     * @param string $tagName Tag name
     */
    private function setBasicMetadataXmlStr($xml)
    {
        $this->generalXmlStrArray = $this->getXmlTagValue($xml, self::TAG_GENERAL);
        $this->lifeCycleXmlStrArray = $this->getXmlTagValue($xml, self::TAG_LIFE_CYCLE);
        $this->metaMetadataXmlStrArray = $this->getXmlTagValue($xml, self::TAG_META_METADATA);
        $this->technicalXmlStrArray = $this->getXmlTagValue($xml, self::TAG_TECHNICAL);
        $this->educationalXmlStrArray = $this->getXmlTagValue($xml, self::TAG_EDUCATIONAL);
        $this->rightsXmlStrArray = $this->getXmlTagValue($xml, self::TAG_RIGHTS);
        $this->relationXmlStrArray = $this->getXmlTagValue($xml, self::TAG_RELATION);
        $this->annotationXmlStrArray = $this->getXmlTagValue($xml, self::TAG_ANNOTAION);
        $this->classificationXmlStrArray = $this->getXmlTagValue($xml, self::TAG_CLASSIFICATION);
    }
    
    /**
     * Set metadata to array
     * メタデータ設定
     *
     * @param int $repositoryId Repository id リポジトリID
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     * @return boolean Result 結果
     */
    private function setMetadataToArray($repositoryId, &$metadata)
    {
        // 0. Set pub_date by TransStartDate
        $tmpDate = explode(" ", $this->TransStartDate);
        $tmpDate = explode("-", $tmpDate[0]);
        $metadata[self::KEY_IR_BASIC][self::KEY_PUB_YEAR] = intval($tmpDate[0]);
        $metadata[self::KEY_IR_BASIC][self::KEY_PUB_MONTH] = intval($tmpDate[1]);
        $metadata[self::KEY_IR_BASIC][self::KEY_PUB_DAY] = intval($tmpDate[2]);
        
        // 1. Set General
        if(!$this->setGeneralToArray($metadata))
        {
            return false;
        }
        // 2. Set LifeCycle
        if(!$this->setLifeCycleToArray($metadata))
        {
            return false;
        }
        // 3. Set Meta-Metadata
        if(!$this->setMetaMetadataToArray($metadata))
        {
            return false;
        }
        // 4. Set Technical
        if(!$this->setTechnicalToArray($metadata))
        {
            return false;
        }
        // 5. Set Educational
        if(!$this->setEducationalToArray($metadata))
        {
            return false;
        }
        // 6. Set Rights
        if(!$this->setRightsToArray($metadata))
        {
            return false;
        }
        // 7. Set Relation
        if(!$this->setRelationToArray($metadata))
        {
            return false;
        }
        // 8. Set Annotation
        if(!$this->setAnnotationToArray($metadata))
        {
            return false;
        }
        // 9. Set Classification
        if(!$this->setClassificationToArray($metadata))
        {
            return false;
        }
        
        // 10. Set require metadata
        $this->setRequireMetadataToArray($repositoryId, $metadata);
        
        return true;
    }
    
    /**
     * Set general metadata to array
     * メタデータ設定
     *
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     * @return boolean Result 結果
     */
    private function setGeneralToArray(&$metadata)
    {
        $biblioData = array();
        foreach($this->generalXmlStrArray as $xmlStr)
        {
            // Parse xml
            $vals = array();
            if(!$this->parseXml($xmlStr, $vals))
            {
                return false;
            }
            
            $openTag = "";
            $tagDataArray = array();
            foreach($vals as $val)
            {
                if($val["type"] == "open" && strlen($openTag)==0)
                {
                    switch($val["tag"])
                    {
                        case self::TAG_IDENTIFIER:
                        case self::TAG_TITLE:
                        case self::TAG_DESCRIPTION:
                        case self::TAG_KEYWORD:
                        case self::TAG_COVERAGE:
                        case self::TAG_STRUCTURE:
                        case self::TAG_AGGREGATION_LEVEL:
                            $openTag = $val["tag"];
                            array_push($tagDataArray, $val);
                            break;
                        default:
                            break;
                    }
                }
                else if($val["type"] == "close" && strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                    if($openTag == $val["tag"])
                    {
                        switch($val["tag"])
                        {
                            case self::TAG_IDENTIFIER:
                                // Get identifer(catalog, entry)
                                $this->getIdentifier($tagDataArray, $catalog, $entry);
                                $this->setGeneralIdentifier($catalog, $entry, $metadata, $biblioData);
                                break;
                            case self::TAG_TITLE:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                if(strlen($string)>0)
                                {
                                    $language = RepositoryOutputFilterLOM::language($language);
                                    if($language == self::ITEM_LANG_EN)
                                    {
                                        if(strlen($metadata[self::KEY_IR_BASIC][self::KEY_TITLE_EN])==0)
                                        {
                                            $metadata[self::KEY_IR_BASIC][self::KEY_TITLE_EN] = $string;
                                        }
                                    }
                                    else
                                    {
                                        if(strlen($metadata[self::KEY_IR_BASIC][self::KEY_TITLE])==0)
                                        {
                                            $metadata[self::KEY_IR_BASIC][self::KEY_TITLE] = $string;
                                        }
                                    }
                                }
                                break;
                            case self::TAG_DESCRIPTION:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                if(strlen($string)>0)
                                {
                                    $attrId = AttrId::GEN_DESCRIPTION;
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXTAREA, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_KEYWORD:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                if(strlen($string)>0)
                                {
                                    $language = RepositoryOutputFilterLOM::language($language);
                                    if($language == self::ITEM_LANG_EN)
                                    {
                                        if(strlen($metadata[self::KEY_IR_BASIC][self::KEY_SEARCH_KEY_EN])>0)
                                        {
                                            $metadata[self::KEY_IR_BASIC][self::KEY_SEARCH_KEY_EN] .= "|";
                                        }
                                        $metadata[self::KEY_IR_BASIC][self::KEY_SEARCH_KEY_EN] .= $string;
                                    }
                                    else
                                    {
                                        if(strlen($metadata[self::KEY_IR_BASIC][self::KEY_SEARCH_KEY])>0)
                                        {
                                            $metadata[self::KEY_IR_BASIC][self::KEY_SEARCH_KEY] .= "|";
                                        }
                                        $metadata[self::KEY_IR_BASIC][self::KEY_SEARCH_KEY] .= $string;
                                    }
                                }
                                break;
                            case self::TAG_COVERAGE:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                if(strlen($string)>0)
                                {
                                    $attrId = AttrId::GEN_COVERGE;
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_STRUCTURE:
                                // Get vocabulary(source, value)
                                $this->getVocabulary($tagDataArray, $source, $value);
                                $str = RepositoryOutputFilterLOM::generalStructureValue($value);
                                $attrId = AttrId::GEN_STRUCTURE;
                                if(strlen($str)>0 && $this->cntMetadata[$attrId] < 1)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $str);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_SELECT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_AGGREGATION_LEVEL:
                                // Get vocabulary(source, value)
                                $this->getVocabulary($tagDataArray, $source, $value);
                                $str = RepositoryOutputFilterLOM::generalAggregationLevelValue($value);
                                $attrId = AttrId::GEN_AGGLEVEL;
                                if(strlen($str)>0 && $this->cntMetadata[$attrId] < 1)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $str);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_SELECT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                        
                        $openTag = "";
                        $tagDataArray = array();
                    }
                }
                else if($val["type"] == "complete" && strlen($openTag)==0)
                {
                    switch($val["tag"])
                    {
                        case self::TAG_LANGUAGE:
                            $language = RepositoryOutputFilterLOM::language($this->forXmlChangeDecode($val["value"]));
                            if(strlen($language)>0)
                            {
                                if(strlen($metadata[self::KEY_IR_BASIC][self::KEY_LANGUAGE])==0)
                                {
                                    $metadata[self::KEY_IR_BASIC][self::KEY_LANGUAGE] = $language;
                                }
                                else
                                {
                                    $attrId = AttrId::GEN_LANGUAGE;
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $language);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }
                else if(strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                }
            }
        }
        
        if(count($biblioData)>0)
        {
            $attrId = AttrId::GEN_ID_BIBINFO;
            if($this->cntMetadata[$attrId] < 1)
            {
                $this->cntMetadata[$attrId]++;
                $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_BIBLIOINFO, $biblioData);
                if(count($irMetadata)>0)
                {
                    array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                }
            }
        }
        return true;
    }
    
    /**
     * Set General identifier metadata
     * メタデータ設定
     *
     * @param string $catalog Catalog Catalog
     * @param string $entry Entry Entry
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     * @param array $biblioData Biblio data 書誌情報
     *                          array["biblio_name"|"biblio_name_english"|"vokume"|"issue"|"spage"|"epage"|"date_of_issued"] 
     */
    private function setGeneralIdentifier($catalog, $entry, &$metadata, &$biblioData)
    {
        $irMetadata = array();
        
        if(RepositoryOutputFilterLOM::string($catalog)==RepositoryOutputFilterLOM::string(self::CATALOG_URI) && strlen($entry)>0)
        {
            $attrId = AttrId::GEN_ID_URI;
            $this->cntMetadata[$attrId]++;
            $data = array(self::KEY_ATTR_VALUE => $entry);
            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_LINK, $data);
        }
        else if(RepositoryOutputFilterLOM::string($catalog)==RepositoryOutputFilterLOM::string(self::CATALOG_ISSN) && strlen($entry)>0)
        {
            $attrId = AttrId::GEN_ID_ISSN;
            $this->cntMetadata[$attrId]++;
            $data = array(self::KEY_ATTR_VALUE => $entry);
            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
        }
        else if(RepositoryOutputFilterLOM::string($catalog)==RepositoryOutputFilterLOM::string(self::CATALOG_NCID) && strlen($entry)>0)
        {
            $attrId = AttrId::GEN_ID_NCID;
            $this->cntMetadata[$attrId]++;
            $data = array(self::KEY_ATTR_VALUE => $entry);
            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
        }
        else if(RepositoryOutputFilterLOM::string($catalog)==RepositoryOutputFilterLOM::string(self::CATALOG_JTITLE) && strlen($entry)>0)
        {
            if(isset($biblioData[self::KEY_BIBLIO_NAME]) || isset($biblioData[self::KEY_BIBLIO_NAME_EN]))
            {
                $irMetadata = $this->setIdentifier(AttrId::GEN_IDENTIFIER, $catalog, $entry);
            }
            else
            {
                $biblioNameArray = explode(" = ", $entry, 2);
                $biblioData[self::KEY_BIBLIO_NAME] = $biblioNameArray[0];
                if(isset($biblioNameArray[0]))
                    $biblioData[self::KEY_BIBLIO_NAME_EN] = $biblioNameArray[1];
            }
        }
        else if(RepositoryOutputFilterLOM::string($catalog)==RepositoryOutputFilterLOM::string(self::CATALOG_VOLUME) && strlen($entry)>0)
        {
            if(isset($biblioData[self::KEY_VOLUME]))
            {
                $irMetadata = $this->setIdentifier(AttrId::GEN_IDENTIFIER, $catalog, $entry);
            }
            else
            {
                $biblioData[self::KEY_VOLUME] = $entry;
            }
        }
        else if(RepositoryOutputFilterLOM::string($catalog)==RepositoryOutputFilterLOM::string(self::CATALOG_ISSUE) && strlen($entry)>0)
        {
            if(isset($biblioData[self::KEY_ISSUE]))
            {
                $irMetadata = $this->setIdentifier(AttrId::GEN_IDENTIFIER, $catalog, $entry);
            }
            else
            {
                $biblioData[self::KEY_ISSUE] = $entry;
            }
        }
        else if(RepositoryOutputFilterLOM::string($catalog)==RepositoryOutputFilterLOM::string(self::CATALOG_SPAGE) && strlen($entry)>0)
        {
            if(isset($biblioData[self::KEY_SPAGE]))
            {
                $irMetadata = $this->setIdentifier(AttrId::GEN_IDENTIFIER, $catalog, $entry);
            }
            else
            {
                $biblioData[self::KEY_SPAGE] = $entry;
            }
        }
        else if(RepositoryOutputFilterLOM::string($catalog)==RepositoryOutputFilterLOM::string(self::CATALOG_EPAGE) && strlen($entry)>0)
        {
            if(isset($biblioData[self::KEY_EPAGE]))
            {
                $irMetadata = $this->setIdentifier(AttrId::GEN_IDENTIFIER, $catalog, $entry);
            }
            else
            {
                $biblioData[self::KEY_EPAGE] = $entry;
            }
        }
        else if(RepositoryOutputFilterLOM::string($catalog)==RepositoryOutputFilterLOM::string(self::CATALOG_DATEOFISSUED) && strlen($entry)>0)
        {
            if(isset($biblioData[self::KEY_DATE_OF_ISSUED]))
            {
                $irMetadata = $this->setIdentifier(AttrId::GEN_IDENTIFIER, $catalog, $entry);
            }
            else
            {
                $biblioData[self::KEY_DATE_OF_ISSUED] = $entry;
            }
        }
        else if(RepositoryOutputFilterLOM::string($catalog)==RepositoryOutputFilterLOM::string(self::CATALOG_TEXTVERSION) && strlen($entry)>0)
        {
            $attrId = AttrId::GEN_ID_TEXTVERSION;
            if($this->cntMetadata[$attrId] < 1)
            {
                $str = RepositoryOutputFilter::string($entry);
                if($str != self::TEXTVERSION_AUTHOR && $str != self::TEXTVERSION_PUBLISHER && $str != self::TEXTVERSION_NONE)
                {
                    $str = "";
                }
                if(strlen($str)>0)
                {
                    
                    $this->cntMetadata[$attrId]++;
                    $data = array(self::KEY_ATTR_VALUE => $str);
                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                }
            }
        }
        else if(strlen($catalog)>0 || strlen($entry)>0)
        {
            $irMetadata = $this->setIdentifier(AttrId::GEN_IDENTIFIER, $catalog, $entry);
        }
        
        if(count($irMetadata)>0)
        {
            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
        }
    }
    
    /**
     * Set identifier metadata
     * Identifier設定
     *
     * @param int $attrId Attribute id 属性ID
     * @param string $catalog Catalog Catalog
     * @param string $entry Entry Entry
     * @return array Metadata list メタデータ一覧
     *               array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setIdentifier($attrId, $catalog, $entry)
    {
        $irMetadata = array();
        $str = $catalog;
        if(strlen($str)>0 && strlen($entry)>0)
        {
            $str .= self::IDENTIFIER_DELIMITER;
        }
        $str .= $entry;
        if(strlen($str) > 0)
        {
            $this->cntMetadata[$attrId]++;
            $data = array(self::KEY_ATTR_VALUE => $str);
            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
        }
        
        return $irMetadata;
    }
    
    /**
     * Set identifier metadata
     * メタデータ設定
     *
     * @param array Metadata list メタデータ一覧
     *              array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setLifeCycleToArray(&$metadata)
    {
        foreach($this->lifeCycleXmlStrArray as $xmlStr)
        {
            // Parse xml
            $vals = array();
            if(!$this->parseXml($xmlStr, $vals))
            {
                return false;
            }
            
            $openTag = "";
            $tagDataArray = array();
            foreach($vals as $val)
            {
                if($val["type"] == "open" && strlen($openTag)==0)
                {
                    switch($val["tag"])
                    {
                        case self::TAG_VERSION:
                        case self::TAG_STATUS:
                        case self::TAG_CONTRIBUTE:
                            $openTag = $val["tag"];
                            array_push($tagDataArray, $val);
                            break;
                        default:
                            break;
                    }
                }
                else if($val["type"] == "close" && strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                    if($openTag == $val["tag"])
                    {
                        switch($val["tag"])
                        {
                            case self::TAG_VERSION:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                $attrId = AttrId::LC_VERSION;
                                if(strlen($string)>0 && $this->cntMetadata[$attrId] < 1)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_STATUS:
                                // Get vocabulary(source, value)
                                $this->getVocabulary($tagDataArray, $source, $value);
                                $str = RepositoryOutputFilterLOM::lifeCycleStatusValue($value);
                                $attrId = AttrId::LC_STATUS;
                                if(strlen($str)>0 && $this->cntMetadata[$attrId] < 1)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $str);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_SELECT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_CONTRIBUTE:
                                // Get contribute(role, entity, date)
                                $this->getContribute($tagDataArray, $role, $entity, $date);
                                $roleVal = RepositoryOutputFilterLOM::lyfeCycleContributeRole($role[self::TAG_VOCAB_VAL]);
                                $dateTime = $date[self::TAG_DATE_TIME];
                                if($roleVal == self::ROLE_LC_PUBLISHER)
                                {
                                    foreach($entity as $entityStr)
                                    {
                                        if(strlen($entityStr)>0)
                                        {
                                            $attrId = AttrId::LC_CON_PUBLISHER;
                                            $this->cntMetadata[$attrId]++;
                                            $nameArray = $this->explodeNameStr($entityStr);
                                            $data = array(
                                                    self::KEY_FAMILY => $nameArray[self::KEY_FAMILY],
                                                    self::KEY_NAME => $nameArray[self::KEY_NAME]
                                                );
                                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_NAME, $data);
                                            if(count($irMetadata)>0)
                                            {
                                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                            }
                                        }
                                    }
                                    
                                    if(strlen($dateTime)>0)
                                    {
                                        $attrId = AttrId::LC_CON_PUB_DATE;
                                        $this->cntMetadata[$attrId]++;
                                        $data = array(self::KEY_ATTR_VALUE => $dateTime);
                                        $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_DATE, $data);
                                        if(count($irMetadata)>0)
                                        {
                                            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                        }
                                    }
                                }
                                else if($roleVal == self::ROLE_LC_AUTHOR)
                                {
                                    foreach($entity as $entityStr)
                                    {
                                        if(strlen($entityStr)>0)
                                        {
                                            $attrId = AttrId::LC_CON_CREATOR;
                                            $this->cntMetadata[$attrId]++;
                                            $nameArray = $this->explodeNameStr($entityStr);
                                            $data = array(
                                                    self::KEY_FAMILY => $nameArray[self::KEY_FAMILY],
                                                    self::KEY_NAME => $nameArray[self::KEY_NAME]
                                                );
                                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_NAME, $data);
                                            if(count($irMetadata)>0)
                                            {
                                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    foreach($entity as $entityStr)
                                    {
                                        if(strlen($entityStr)>0)
                                        {
                                            if($roleVal == self::ROLE_LC_INITIATOR){
                                                $attrId = AttrId::LC_CON_INITIATOR;
                                            } else if($roleVal == self::ROLE_LC_TERMINATOR){
                                                $attrId = AttrId::LC_CON_TERMINATOR;
                                            } else if($roleVal == self::ROLE_LC_VALIDATOR){
                                                $attrId = AttrId::LC_CON_VALIDATOR;
                                            } else if($roleVal == self::ROLE_LC_EDITOR){
                                                $attrId = AttrId::LC_CON_EDITOR;
                                            } else if($roleVal == self::ROLE_LC_GRA_DESIGNER){
                                                $attrId = AttrId::LC_CON_GRA_DESIGNER;
                                            } else if($roleVal == self::ROLE_LC_TEC_IMPLEMENTER){
                                                $attrId = AttrId::LC_CON_TEC_IMPLEMENTER;
                                            } else if($roleVal == self::ROLE_LC_CNT_PROVIDER){
                                                $attrId = AttrId::LC_CON_CNT_PROVIDER;
                                            } else if($roleVal == self::ROLE_LC_TEC_VALIDATOR){
                                                $attrId = AttrId::LC_CON_TEC_VALIDATOR;
                                            } else if($roleVal == self::ROLE_LC_EDU_VALIDATOR){
                                                $attrId = AttrId::LC_CON_EDU_VALIDATOR;
                                            } else if($roleVal == self::ROLE_LC_SCRIPT_WRITER){
                                                $attrId = AttrId::LC_CON_SCRIPT_WRITER;
                                            } else if($roleVal == self::ROLE_LC_INST_DESIGNER){
                                                $attrId = AttrId::LC_CON_INST_DESIGNER;
                                            } else if($roleVal == self::ROLE_LC_SUBJ_MATTER_EXPERT){
                                                $attrId = AttrId::LC_CON_SUBJ_MATTER_EXPERT;
                                            } else if($roleVal == self::ROLE_LC_UNKNOWN){
                                                $attrId = AttrId::LC_CON_UNKNOWN;
                                            } else {
                                                $attrId = AttrId::LC_CONTRIBUTE;
                                            }
                                            $this->cntMetadata[$attrId]++;
                                            $data = array(self::KEY_ATTR_VALUE => $entityStr);
                                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                            if(count($irMetadata)>0)
                                            {
                                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                            }
                                        }
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                        
                        $openTag = "";
                        $tagDataArray = array();
                    }
                }
                else if(strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Set Meta-Metadata metadata to array
     * メタデータ設定
     *
     * @param array Metadata list メタデータ一覧
     *              array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setMetaMetadataToArray(&$metadata)
    {
        foreach($this->metaMetadataXmlStrArray as $xmlStr)
        {
            // Parse xml
            $vals = array();
            if(!$this->parseXml($xmlStr, $vals))
            {
                return false;
            }
            
            $openTag = "";
            $tagDataArray = array();
            foreach($vals as $val)
            {
                if($val["type"] == "open" && strlen($openTag)==0)
                {
                    switch($val["tag"])
                    {
                        case self::TAG_IDENTIFIER:
                        case self::TAG_CONTRIBUTE:
                            $openTag = $val["tag"];
                            array_push($tagDataArray, $val);
                            break;
                        default:
                            break;
                    }
                }
                else if($val["type"] == "close" && strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                    if($openTag == $val["tag"])
                    {
                        switch($val["tag"])
                        {
                            case self::TAG_IDENTIFIER:
                                // Get identifer(catalog, entry)
                                $this->getIdentifier($tagDataArray, $catalog, $entry);
                                $irMetadata = $this->setIdentifier(AttrId::MM_IDENTIFIER, $catalog, $entry);
                                if(count($irMetadata)>0)
                                {
                                    array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                }
                                break;
                            case self::TAG_CONTRIBUTE:
                                // Get contribute(role, entity, date)
                                $this->getContribute($tagDataArray, $role, $entity, $date);
                                $roleVal = RepositoryOutputFilterLOM::metaMetadataContributeRole($role[self::TAG_VOCAB_VAL]);
                                foreach($entity as $entityStr)
                                {
                                    if(strlen($entityStr)>0)
                                    {
                                        if($roleVal == self::ROLE_MM_CREATOR){
                                            $attrId = AttrId::MM_CON_CREATOR;
                                        } else if($roleVal == self::ROLE_MM_VALIDATOR){
                                            $attrId = AttrId::MM_CON_VALIDATOR;
                                        } else {
                                            $attrId = AttrId::MM_CONTRIBUTE;
                                        }
                                        $this->cntMetadata[$attrId]++;
                                        $data = array(self::KEY_ATTR_VALUE => $entityStr);
                                        $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                        if(count($irMetadata)>0)
                                        {
                                            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                        }
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                        $openTag = "";
                        $tagDataArray = array();
                    }
                }
                else if($val["type"] == "complete" && strlen($openTag)==0)
                {
                    if($val["tag"] == self::TAG_METADATA_SCHEMA)
                    {
                        $attrId = AttrId::MM_META_SCHEMA;
                        if(strlen($val["value"])>0)
                        {
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $this->forXmlChangeDecode($val["value"]));
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                        }
                    }
                    else if($val["tag"] == self::TAG_LANGUAGE)
                    {
                        $attrId = AttrId::MM_LANGUAGE;
                        $language = RepositoryOutputFilterLOM::language($this->forXmlChangeDecode($val["value"]));
                        if(strlen($language)>0 && $this->cntMetadata[$attrId] < 1)
                        {
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $language);
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                        }
                    }
                }
                else if(strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Set Technical metadata to array
     * メタデータ設定
     *
     * @param array Metadata list メタデータ一覧
     *              array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setTechnicalToArray(&$metadata)
    {
        foreach($this->technicalXmlStrArray as $xmlStr)
        {
            // Parse xml
            $vals = array();
            if(!$this->parseXml($xmlStr, $vals))
            {
                return false;
            }
            
            $openTag = "";
            $tagDataArray = array();
            foreach($vals as $val)
            {
                if($val["type"] == "open" && strlen($openTag)==0)
                {
                    switch($val["tag"])
                    {
                        case self::TAG_REQUIREMENT:
                        case self::TAG_INSTALLATION_REMARKS:
                        case self::TAG_OTHER_PLATFORM_REQIREMENTS:
                        case self::TAG_DURATION:
                            $openTag = $val["tag"];
                            array_push($tagDataArray, $val);
                            break;
                        default:
                            break;
                    }
                }
                else if($val["type"] == "close" && strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                    if($openTag == $val["tag"])
                    {
                        switch($val["tag"])
                        {
                            case self::TAG_REQUIREMENT:
                                $this->setTechnicalRequirement($tagDataArray, $metadata);
                                break;
                            case self::TAG_INSTALLATION_REMARKS:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                $attrId = AttrId::TEC_INSTALL_REMARKS;
                                if(strlen($string)>0 && $this->cntMetadata[$attrId] < 1)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_OTHER_PLATFORM_REQIREMENTS:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                $attrId = AttrId::TEC_OTHER_PLATFORM_REQ;
                                if(strlen($string)>0 && $this->cntMetadata[$attrId] < 1)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_DURATION:
                                // Get duration(duration, description)
                                $this->getDuration($tagDataArray, $duration, $discription);
                                $attrId = AttrId::TEC_DURATION;
                                if(strlen($duration)>0 && $this->cntMetadata[$attrId] < 1)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $duration);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                        $openTag = "";
                        $tagDataArray = array();
                    }
                }
                else if($val["type"] == "complete" && strlen($openTag)==0)
                {
                    if($val["tag"] == self::TAG_FORMAT)
                    {
                        $attrId = AttrId::TEC_FORMAT;
                        if(strlen($val["value"])>0)
                        {
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $this->forXmlChangeDecode($val["value"]));
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                        }
                    }
                    else if($val["tag"] == self::TAG_SIZE)
                    {
                        $attrId = AttrId::TEC_SIZE;
                        if(strlen($val["value"])>0 && $this->cntMetadata[$attrId] < 1)
                        {
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $this->forXmlChangeDecode($val["value"]));
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                        }
                    }
                    else if($val["tag"] == self::TAG_LOCATION)
                    {
                        $attrId = AttrId::TEC_LOCATION;
                        if(strlen($val["value"])>0)
                        {
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $this->forXmlChangeDecode($val["value"]));
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                        }
                    }
                }
                else if(strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Set Technical requirement
     * メタデータ設定
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param array Metadata list メタデータ一覧
     *              array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setTechnicalRequirement($vals, &$metadata)
    {
        $openTag = "";
        $tagDataArray = array();
        foreach($vals as $val)
        {
            if($val["type"] == "open" && strlen($openTag)==0)
            {
                if($val["tag"] == self::TAG_OR_COMPOSITE)
                {
                    array_push($tagDataArray, $val);
                    $openTag = $val["tag"];
                }
            }
            else if($val["type"] == "close" && strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
                if($openTag == $val["tag"])
                {
                    if($val["tag"] == self::TAG_OR_COMPOSITE)
                    {
                        // Get orComposite(type, name, minVersion, maxVersion)
                        $result = $this->getTechnicalOrComposite($tagDataArray, $type, $name, $minVersion, $maxVersion);
                        if($result)
                        {
                            // Type
                            $attrId = AttrId::TEC_REQ_ORCOMP_TYPE;
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $type[self::TAG_VOCAB_VAL]);
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                            
                            // Name
                            $attrId = AttrId::TEC_REQ_ORCOMP_NAME;
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $name[self::TAG_VOCAB_VAL]);
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                            
                            // MinimumVersion
                            $attrId = AttrId::TEC_REQ_ORCOMP_MINVERSION;
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $minVersion);
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                            
                            // MaximumVersion
                            $attrId = AttrId::TEC_REQ_ORCOMP_MAXVERSION;
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $maxVersion);
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                        }
                    }
                    $openTag = "";
                    $tagDataArray = array();
                }
            }
            else if(strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
            }
        }
    }
    
    /**
     * Set Educational metadata to array
     * メタデータ設定
     *
     * @param array Metadata list メタデータ一覧
     *              array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setEducationalToArray(&$metadata)
    {
        foreach($this->educationalXmlStrArray as $xmlStr)
        {
            // Parse xml
            $vals = array();
            if(!$this->parseXml($xmlStr, $vals))
            {
                return false;
            }
            
            $openTag = "";
            $tagDataArray = array();
            foreach($vals as $val)
            {
                if($val["type"] == "open" && strlen($openTag)==0)
                {
                    switch($val["tag"])
                    {
                        case self::TAG_INTERACTIVITY_TYPE:
                        case self::TAG_LEARNING_RESOURCE_TYPE:
                        case self::TAG_INTERACTIVITY_LEVEL:
                        case self::TAG_SEMANTIC_DENSITY:
                        case self::TAG_INTENDED_END_USER_ROLE:
                        case self::TAG_CONTEXT:
                        case self::TAG_TYPICAL_AGE_RANGE:
                        case self::TAG_DIFFICULTY:
                        case self::TAG_TYPICAL_LEARNING_TIME:
                        case self::TAG_DESCRIPTION:
                            $openTag = $val["tag"];
                            array_push($tagDataArray, $val);
                            break;
                        default:
                            break;
                    }
                }
                else if($val["type"] == "close" && strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                    if($openTag == $val["tag"])
                    {
                        switch($val["tag"])
                        {
                            case self::TAG_INTERACTIVITY_TYPE:
                            case self::TAG_LEARNING_RESOURCE_TYPE:
                            case self::TAG_INTERACTIVITY_LEVEL:
                            case self::TAG_SEMANTIC_DENSITY:
                            case self::TAG_INTENDED_END_USER_ROLE:
                            case self::TAG_CONTEXT:
                            case self::TAG_DIFFICULTY:
                                // Get vocabulary(source, value)
                                $this->getVocabulary($tagDataArray, $source, $value);
                                $str = "";
                                $attrId= "";
                                if($val["tag"] == self::TAG_INTERACTIVITY_TYPE)
                                {
                                    $str = RepositoryOutputFilterLOM::educationalInteractivityType($value);
                                    $attrId = AttrId::EDU_INTERACTIVITY_TYPE;
                                }
                                else if($val["tag"] == self::TAG_LEARNING_RESOURCE_TYPE)
                                {
                                    $str = RepositoryOutputFilterLOM::educationalLearningResourceType($value);
                                    $attrId = AttrId::EDU_LEARN_RESOURCE_TYPE;
                                }
                                else if($val["tag"] == self::TAG_INTERACTIVITY_LEVEL)
                                {
                                    $str = RepositoryOutputFilterLOM::educationalInteractivityLevel($value);
                                    $attrId = AttrId::EDU_INTERACTIVITY_LEVEL;
                                }
                                else if($val["tag"] == self::TAG_SEMANTIC_DENSITY)
                                {
                                    $str = RepositoryOutputFilterLOM::educationalSemanticDensity($value);
                                    $attrId = AttrId::EDU_SEMANTIC_DENSITY;
                                }
                                else if($val["tag"] == self::TAG_INTENDED_END_USER_ROLE)
                                {
                                    $str = RepositoryOutputFilterLOM::educationalIntendedEndUserRole($value);
                                    $attrId = AttrId::EDU_INT_END_USER_ROLE;
                                }
                                else if($val["tag"] == self::TAG_CONTEXT)
                                {
                                    $str = RepositoryOutputFilterLOM::educationalContext($value);
                                    $attrId = AttrId::EDU_CONTEXT;
                                }
                                else if($val["tag"] == self::TAG_DIFFICULTY)
                                {
                                    $str = RepositoryOutputFilterLOM::educationalDifficulty($value);
                                    $attrId = AttrId::EDU_DIFFICULTY;
                                }
                                if(strlen($str)>0)
                                {
                                    if(isset($this->chkDuplication[$attrId])
                                        && is_array($this->chkDuplication[$attrId])
                                        && !in_array($str, $this->chkDuplication[$attrId]))
                                    {
                                        $this->cntMetadata[$attrId]++;
                                        array_push($this->chkDuplication[$attrId], $str);
                                        $data = array(self::KEY_ATTR_VALUE => $str);
                                        $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_CHECKBOX, $data);
                                        if(count($irMetadata)>0)
                                        {
                                            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                        }
                                    }
                                }
                                break;
                            case self::TAG_TYPICAL_AGE_RANGE:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                $attrId = AttrId::EDU_TYP_AGE_RANGE;
                                if(strlen($string)>0)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_TYPICAL_LEARNING_TIME:
                                // Get duration(duration, description)
                                $this->getDuration($tagDataArray, $duration, $discription);
                                $attrId = AttrId::EDU_TYP_LEARN_TIME;
                                if(strlen($duration)>0)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $duration);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_DESCRIPTION:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                $attrId = AttrId::EDU_DESCRIPTION;
                                if(strlen($string)>0)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXTAREA, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                        $openTag = "";
                        $tagDataArray = array();
                    }
                }
                else if($val["type"] == "complete" && strlen($openTag)==0)
                {
                    if($val["tag"] == self::TAG_LANGUAGE)
                    {
                        $attrId = AttrId::EDU_LANGUAGE;
                        $language = RepositoryOutputFilterLOM::language($this->forXmlChangeDecode($val["value"]));
                        if(strlen($language)>0)
                        {
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $language);
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                        }
                    }
                }
                else if(strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Set Rights metadata to array
     * メタデータ設定
     *
     * @param array Metadata list メタデータ一覧
     *              array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setRightsToArray(&$metadata)
    {
        foreach($this->rightsXmlStrArray as $xmlStr)
        {
            // Parse xml
            $vals = array();
            if(!$this->parseXml($xmlStr, $vals))
            {
                return false;
            }
            
            $openTag = "";
            $tagDataArray = array();
            foreach($vals as $val)
            {
                if($val["type"] == "open" && strlen($openTag)==0)
                {
                    switch($val["tag"])
                    {
                        case self::TAG_COST:
                        case self::TAG_COPYRIGHT_AND_OTHER_RESTRICTIONS:
                        case self::TAG_DESCRIPTION:
                            $openTag = $val["tag"];
                            array_push($tagDataArray, $val);
                            break;
                        default:
                            break;
                    }
                }
                else if($val["type"] == "close" && strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                    if($openTag == $val["tag"])
                    {
                        switch($val["tag"])
                        {
                            case self::TAG_COST:
                            case self::TAG_COPYRIGHT_AND_OTHER_RESTRICTIONS:
                                // Get vocabulary(source, value)
                                $this->getVocabulary($tagDataArray, $source, $value);
                                $str = "";
                                $attrId= "";
                                if($val["tag"] == self::TAG_COST)
                                {
                                    $str = RepositoryOutputFilterLOM::yesno($value);
                                    $attrId = AttrId::RIT_COST;
                                }
                                else if($val["tag"] == self::TAG_COPYRIGHT_AND_OTHER_RESTRICTIONS)
                                {
                                    $str = RepositoryOutputFilterLOM::yesno($value);
                                    $attrId = AttrId::RIT_CPRIT_OTHRER_REST;
                                }
                                
                                if(strlen($str)>0 && $this->cntMetadata[$attrId] < 1)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $str);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_SELECT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_DESCRIPTION:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                $attrId = AttrId::RIT_DESCRIPTION;
                                if(strlen($string)>0 && $this->cntMetadata[$attrId] < 1)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXTAREA, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                        $openTag = "";
                        $tagDataArray = array();
                    }
                }
                else if(strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Set Relation metadata to array
     * メタデータ設定
     *
     * @param array Metadata list メタデータ一覧
     *              array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setRelationToArray(&$metadata)
    {
        foreach($this->relationXmlStrArray as $xmlStr)
        {
            // Parse xml
            $vals = array();
            if(!$this->parseXml($xmlStr, $vals))
            {
                return false;
            }
            
            $openTag = "";
            $tagDataArray = array();
            $relationData =array();
            foreach($vals as $val)
            {
                if($val["type"] == "open" && strlen($openTag)==0)
                {
                    switch($val["tag"])
                    {
                        case self::TAG_KIND:
                        case self::TAG_RESOURCE:
                            $openTag = $val["tag"];
                            array_push($tagDataArray, $val);
                            break;
                        default:
                            break;
                    }
                }
                else if($val["type"] == "close" && strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                    if($openTag == $val["tag"])
                    {
                        switch($val["tag"])
                        {
                            case self::TAG_KIND:
                                // Get vocabulary(source, value)
                                $this->getVocabulary($tagDataArray, $source, $value);
                                $str = RepositoryOutputFilterLOM::relation($value);
                                if($str == self::KIND_ISVERSIONOF)
                                    $attrId = AttrId::REL_ISVERSIONOF;
                                else if($str == self::KIND_HASVERSION)
                                    $attrId = AttrId::REL_HASVERSION;
                                else if($str == self::KIND_ISREQUIREDBY)
                                    $attrId = AttrId::REL_ISREQUIREDBY;
                                else if($str == self::KIND_REQUIRES)
                                    $attrId = AttrId::REL_REQUIRES;
                                else if($str == self::KIND_ISPARTOF)
                                    $attrId = AttrId::REL_ISPARTOF;
                                else if($str == self::KIND_HASPART)
                                    $attrId = AttrId::REL_HASPART;
                                else if($str == self::KIND_ISREFERENCEDBY)
                                    $attrId = AttrId::REL_ISREFERENCEDBY;
                                else if($str == self::KIND_REFERENCES)
                                    $attrId = AttrId::REL_REFERENCES;
                                else if($str == self::KIND_ISFORMATOF)
                                    $attrId = AttrId::REL_ISFORMATOF;
                                else if($str == self::KIND_HASFORMAT)
                                    $attrId = AttrId::REL_HASFORMAT;
                                else if($str == self::KIND_ISBASISFOR)
                                    $attrId = AttrId::REL_ISBASISFOR;
                                else if($str == self::KIND_ISBASEDON)
                                    $attrId = AttrId::REL_ISBASEDON;
                                else
                                    $attrId = AttrId::REL_RELATION;
                                    
                                $relationData[self::KEY_ATTR_ID] = $attrId;
                                break;
                            case self::TAG_RESOURCE:
                                // Get resource(source, value)
                                $this->getRelationResource($tagDataArray, $identifierArray, $descriptionArray);
                                $relationData[self::TAG_IDENTIFIER] = $identifierArray;
                                $relationData[self::TAG_DESCRIPTION] = $descriptionArray;
                                break;
                            default:
                                break;
                        }
                        $openTag = "";
                        $tagDataArray = array();
                    }
                }
                else if(strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                }
            }
            
            if(count($relationData)>0)
            {
                if(isset($relationData[self::TAG_IDENTIFIER]))
                {
                    $defaultAttrId = AttrId::REL_RELATION;
                    if(isset($relationData[self::KEY_ATTR_ID]))
                    {
                        $defaultAttrId = $relationData[self::KEY_ATTR_ID];
                    }
                    
                    for($ii=0; $ii<count($relationData[self::TAG_IDENTIFIER]); $ii++)
                    {
                        $catalog = $relationData[self::TAG_IDENTIFIER][$ii][self::TAG_CATALOG];
                        $entry = $relationData[self::TAG_IDENTIFIER][$ii][self::TAG_ENTRY];
                        $attrId = $defaultAttrId;
                        $str = "";
                        if(RepositoryOutputFilterLOM::string($catalog) == RepositoryOutputFilterLOM::string(self::CATALOG_PMID))
                        {
                            $attrId = AttrId::REL_PMID;
                            $str = $entry;
                        }
                        else if(RepositoryOutputFilterLOM::string($catalog) == RepositoryOutputFilterLOM::string(self::CATALOG_DOI))
                        {
                            $attrId = AttrId::REL_DOI;
                            $str = $entry;
                        }
                        else
                        {
                            $str = $catalog;
                            if(strlen($str)>0 && strlen($entry)>0)
                            {
                                $str .= self::IDENTIFIER_DELIMITER;
                            }
                            $str .= $entry;
                        }
                        if(strlen($str) > 0)
                        {
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $str);
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    
    /**
     * Set Annotation metadata to array
     * メタデータ設定
     *
     * @param array Metadata list メタデータ一覧
     *              array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setAnnotationToArray(&$metadata)
    {
        foreach($this->annotationXmlStrArray as $xmlStr)
        {
            // Parse xml
            $vals = array();
            if(!$this->parseXml($xmlStr, $vals))
            {
                return false;
            }
            
            $openTag = "";
            $tagDataArray = array();
            foreach($vals as $val)
            {
                if($val["type"] == "open" && strlen($openTag)==0)
                {
                    switch($val["tag"])
                    {
                        case self::TAG_DATE:
                        case self::TAG_DESCRIPTION:
                            $openTag = $val["tag"];
                            array_push($tagDataArray, $val);
                            break;
                        default:
                            break;
                    }
                }
                else if($val["type"] == "close" && strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                    if($openTag == $val["tag"])
                    {
                        switch($val["tag"])
                        {
                            case self::TAG_DATE:
                                // Get dateTime(dateTime, discription)
                                $this->getDateTime($tagDataArray, $dateTime, $discription);
                                $attrId = AttrId::ANO_DATE;
                                if(strlen($dateTime)>0)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $dateTime);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_DATE, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_DESCRIPTION:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                $attrId = AttrId::ANO_DESCRIPTION;
                                if(strlen($string)>0)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXTAREA, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                        $openTag = "";
                        $tagDataArray = array();
                    }
                }
                else if($val["type"] == "complete" && strlen($openTag)==0)
                {
                    if($val["tag"] == self::TAG_ENTITY)
                    {
                        $attrId = AttrId::ANO_ENTITY;
                        $str = $this->forXmlChangeDecode($val["value"]);
                        if(strlen($str)>0)
                        {
                            $this->cntMetadata[$attrId]++;
                            $data = array(self::KEY_ATTR_VALUE => $str);
                            $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                            if(count($irMetadata)>0)
                            {
                                array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                            }
                        }
                    }
                }
                else if(strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Set Classification metadata to array
     * メタデータ設定
     *
     * @param array Metadata list メタデータ一覧
     *              array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setClassificationToArray(&$metadata)
    {
        foreach($this->classificationXmlStrArray as $xmlStr)
        {
            // Parse xml
            $vals = array();
            if(!$this->parseXml($xmlStr, $vals))
            {
                return false;
            }
            
            $openTag = "";
            $tagDataArray = array();
            foreach($vals as $val)
            {
                if($val["type"] == "open" && strlen($openTag)==0)
                {
                    switch($val["tag"])
                    {
                        case self::TAG_PURPOSE:
                        case self::TAG_TAXON_PATH:
                        case self::TAG_DESCRIPTION:
                        case self::TAG_KEYWORD:
                            $openTag = $val["tag"];
                            array_push($tagDataArray, $val);
                            break;
                        default:
                            break;
                    }
                }
                else if($val["type"] == "close" && strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                    if($openTag == $val["tag"])
                    {
                        switch($val["tag"])
                        {
                            case self::TAG_PURPOSE:
                                // Get vocabulary(source, value)
                                $this->getVocabulary($tagDataArray, $source, $value);
                                $str = RepositoryOutputFilterLOM::classificationPurpose($value);
                                $attrId = AttrId::CLS_PURPOSE;
                                if(strlen($str)>0)
                                {
                                    if(isset($this->chkDuplication[$attrId])
                                        && is_array($this->chkDuplication[$attrId])
                                        && !in_array($str, $this->chkDuplication[$attrId]))
                                    {
                                        $this->cntMetadata[$attrId]++;
                                        array_push($this->chkDuplication[$attrId], $str);
                                        $data = array(self::KEY_ATTR_VALUE => $str);
                                        $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_CHECKBOX, $data);
                                        if(count($irMetadata)>0)
                                        {
                                            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                        }
                                    }
                                }
                                break;
                            case self::TAG_TAXON_PATH:
                                // Set Classification taxonPath
                                $this->setClassificationTaxonPath($tagDataArray, $metadata);
                                break;
                            case self::TAG_DESCRIPTION:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                $attrId = AttrId::CLS_DESCRIPTION;
                                if(strlen($string)>0)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXTAREA, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            case self::TAG_KEYWORD:
                                // Get langstring(language, string)
                                $this->getLangString($tagDataArray, $language, $string);
                                $attrId = AttrId::CLS_KEYWORD;
                                if(strlen($string)>0)
                                {
                                    $this->cntMetadata[$attrId]++;
                                    $data = array(self::KEY_ATTR_VALUE => $string);
                                    $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                    if(count($irMetadata)>0)
                                    {
                                        array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                        $openTag = "";
                        $tagDataArray = array();
                    }
                }
                else if(strlen($openTag)>0)
                {
                    array_push($tagDataArray, $val);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Set Classification taxonPath
     * メタデータ設定
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param array Metadata list メタデータ一覧
     *              array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setClassificationTaxonPath($vals, &$metadata)
    {
        $openTag = "";
        $tagDataArray = array();
        foreach($vals as $val)
        {
            if($val["type"] == "open" && strlen($openTag)==0)
            {
                switch($val["tag"])
                {
                    case self::TAG_SOURCE:
                    case self::TAG_TAXON:
                        $openTag = $val["tag"];
                        array_push($tagDataArray, $val);
                        break;
                    default:
                        break;
                }
            }
            else if($val["type"] == "close" && strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
                if($openTag == $val["tag"])
                {
                    switch($val["tag"])
                    {
                        case self::TAG_SOURCE:
                            // Get langstring(language, string)
                            $this->getLangString($tagDataArray, $language, $string);
                            $attrId = AttrId::CLS_TAXONPATH_SOURCE;
                            if(strlen($string)>0)
                            {
                                $this->cntMetadata[$attrId]++;
                                $data = array(self::KEY_ATTR_VALUE => $string);
                                $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                if(count($irMetadata)>0)
                                {
                                    array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                }
                            }
                            break;
                        case self::TAG_TAXON:
                            // Get taxon(id, entry)
                            $this->getClassificationTaxon($tagDataArray, $id, $entry);
                            $str = $id;
                            if(strlen($str)>0 && strlen($entry[self::TAG_LANGSTR_STR]))
                            {
                                $str .= self::TAXON_DELIMITER;
                            }
                            $str .= $entry[self::TAG_LANGSTR_STR];
                            $attrId = AttrId::CLS_TAXONPATH_TAXON;
                            if(strlen($str) > 0)
                            {
                                $this->cntMetadata[$attrId]++;
                                $data = array(self::KEY_ATTR_VALUE => $str);
                                $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
                                if(count($irMetadata)>0)
                                {
                                    array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
                                }
                            }
                            break;
                        default:
                            break;
                    }
                    $openTag = "";
                    $tagDataArray = array();
                }
            }
            else if(strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
            }
        }
    }
    
    /**
     * Get identifer
     * メタデータ取得
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param string $catalog
     * @param string $entry
     */
    private function getIdentifier($vals, &$catalog, &$entry)
    {
        $catalog = "";
        $entry = "";
        foreach($vals as $val)
        {
            if($val["type"] == "complete")
            {
                if($val["tag"] == self::TAG_CATALOG)
                {
                    $catalog = $this->forXmlChangeDecode($val["value"]);
                }
                else if($val["tag"] == self::TAG_ENTRY)
                {
                    $entry = $this->forXmlChangeDecode($val["value"]);
                }
            }
        }
    }
    
    /**
     * Get contribute
     * メタデータ取得
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param array $role Role Role
     *                    aray["vocab_src"|"vocab_val"]
     * @param array $entity Entity Entity
     *                    aray[$ii]
     * @param array $date Date 日時
     *                    aray["date_time"|"description"]
     */
    private function getContribute($vals, &$role, &$entity, &$date)
    {
        $role = array(self::TAG_VOCAB_SRC => "", self::TAG_VOCAB_VAL => "");
        $entity = array();
        $date = array(self::TAG_DATE_TIME => "", self::TAG_DESCRIPTION => array());
        $openTag = "";
        $tagDataArray = array();
        foreach($vals as $val)
        {
            if($val["type"] == "open" && strlen($openTag)==0)
            {
                switch($val["tag"])
                {
                    case self::TAG_ROLE:
                    case self::TAG_DATE:
                        $openTag = $val["tag"];
                        array_push($tagDataArray, $val);
                        break;
                    default:
                        break;
                }
            }
            else if($val["type"] == "close" && strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
                if($openTag == $val["tag"])
                {
                    if($val["tag"] == self::TAG_ROLE)
                    {
                        // Get vocabulary(source, value)
                        $this->getVocabulary($tagDataArray, $source, $value);
                        $role[self::TAG_VOCAB_SRC] = $source;
                        $role[self::TAG_VOCAB_VAL] = $value;
                    }
                    else if($val["tag"] == self::TAG_DATE)
                    {
                        // Get dateTime(dateTime, discription)
                        $this->getDateTime($tagDataArray, $dateTime, $discription);
                        $date[self::TAG_DATE_TIME] = $dateTime;
                        $date[self::TAG_DESCRIPTION] = $discription;
                    }
                    $openTag = "";
                    $tagDataArray = array();
                }
            }
            else if($val["type"] == "complete" && strlen($openTag)==0)
            {
                if($val["tag"] == self::TAG_ENTITY)
                {
                    if(strlen($val["value"])>0)
                    {
                        array_push($entity, $this->forXmlChangeDecode($val["value"]));
                    }
                }
            }
            else if(strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
            }
        }
    }
    
    /**
     * Get Technical orComposite
     * メタデータ取得
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param array $type Type Type
     *                    aray["vocab_src"|"vocab_val"]
     * @param array $name Name 名前
     *                    aray["vocab_src"|"vocab_val"]
     * @param string $minVersion Minimum version 最少バージョン
     * @param string $maxVersion Maximum version 最大バージョン
     * @return boolean Result 結果
     */
    private function getTechnicalOrComposite($vals, &$type, &$name, &$minVersion, &$maxVersion)
    {
        $type = array(self::TAG_VOCAB_SRC => "", self::TAG_VOCAB_VAL => "");
        $name = array(self::TAG_VOCAB_SRC => "", self::TAG_VOCAB_VAL => "");
        $minVersion = "";
        $maxVersion = "";
        $openTag = "";
        $tagDataArray = array();
        foreach($vals as $val)
        {
            if($val["type"] == "open" && strlen($openTag)==0)
            {
                switch($val["tag"])
                {
                    case self::TAG_TYPE:
                    case self::TAG_NAME:
                        $openTag = $val["tag"];
                        array_push($tagDataArray, $val);
                        break;
                    default:
                        break;
                }
            }
            else if($val["type"] == "close" && strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
                if($openTag == $val["tag"])
                {
                    if($val["tag"] == self::TAG_TYPE)
                    {
                        // Get vocabulary(source, value)
                        $this->getVocabulary($tagDataArray, $source, $value);
                        $type[self::TAG_VOCAB_SRC] = $source;
                        $type[self::TAG_VOCAB_VAL] = $value;
                    }
                    else if($val["tag"] == self::TAG_NAME)
                    {
                        // Get vocabulary(source, value)
                        $this->getVocabulary($tagDataArray, $source, $value);
                        $name[self::TAG_VOCAB_SRC] = $source;
                        $name[self::TAG_VOCAB_VAL] = $value;
                    }
                    $openTag = "";
                    $tagDataArray = array();
                }
            }
            else if($val["type"] == "complete" && strlen($openTag)==0)
            {
                if($val["tag"] == self::TAG_MINIMUM_VERSION)
                {
                    $minVersion = $this->forXmlChangeDecode($val["value"]);
                }
                else if($val["tag"] == self::TAG_MAXIMUM_VERSION)
                {
                    $maxVersion = $this->forXmlChangeDecode($val["value"]);
                }
            }
            else if(strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
            }
        }
        
        $type[self::TAG_VOCAB_VAL] = RepositoryOutputFilterLOM::technicalRequirementOrCompositeTypeValue($type[self::TAG_VOCAB_VAL]);
        if($type[self::TAG_VOCAB_VAL] == self::TYPE_OPERATING_SYSTEM)
        {
            $name[self::TAG_VOCAB_VAL] = RepositoryOutputFilterLOM::technicalRequirementOrCompositeNameValueForOperatingSystem($name[self::TAG_VOCAB_VAL]);
        }
        else if($type[self::TAG_VOCAB_VAL] == self::TYPE_BROWSER)
        {
            $name[self::TAG_VOCAB_VAL] = RepositoryOutputFilterLOM::technicalRequirementOrCompositeNameValueForBrowser($name[self::TAG_VOCAB_VAL]);
        }
        else
        {
            $name[self::TAG_VOCAB_VAL] = "";
        }
        
        if(!RepositoryOutputFilterLOM::technicalRequirementOrCompositeCombination(
                                    $type[self::TAG_VOCAB_VAL], $name[self::TAG_VOCAB_VAL]))
        {
            $type = array(self::TAG_VOCAB_SRC => "", self::TAG_VOCAB_VAL => "");
            $name = array(self::TAG_VOCAB_SRC => "", self::TAG_VOCAB_VAL => "");
            $minVersion = "";
            $maxVersion = "";
            return false;
        }
        
        return true;
    }
    
    /**
     * Get Relation resource
     * メタデータ取得
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param array $identifier Identifier Identifier
     *                    aray[$ii]
     * @param array $description Description Description
     *                    aray[$ii]
     */
    private function getRelationResource($vals, &$identifierArray, &$descriptionArray)
    {
        $identifierArray = array();
        $descriptionArray = array();
        $openTag = "";
        $tagDataArray = array();
        foreach($vals as $val)
        {
            if($val["type"] == "open" && strlen($openTag)==0)
            {
                switch($val["tag"])
                {
                    case self::TAG_IDENTIFIER:
                    case self::TAG_DESCRIPTION:
                        $openTag = $val["tag"];
                        array_push($tagDataArray, $val);
                        break;
                    default:
                        break;
                }
            }
            else if($val["type"] == "close" && strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
                if($openTag == $val["tag"])
                {
                    if($val["tag"] == self::TAG_IDENTIFIER)
                    {
                        // Get identifier(catalog, entry)
                        $this->getIdentifier($tagDataArray, $catalog, $entry);
                        $identifier = array(self::TAG_CATALOG => $catalog, self::TAG_ENTRY => $entry);
                        array_push($identifierArray, $identifier);
                    }
                    else if($val["tag"] == self::TAG_DESCRIPTION)
                    {
                        // Get langstring(language, string)
                        $this->getLangString($tagDataArray, $language, $string);
                        $description = array(self::TAG_LANGSTR_LANG => $language, self::TAG_LANGSTR_STR => $string);
                        array_push($descriptionArray, $description);
                    }
                    $openTag = "";
                    $tagDataArray = array();
                }
            }
            else if(strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
            }
        }
    }
    
    /**
     * Get Classification taxon
     * メタデータ取得
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param string $id ID ID
     * @param array $entry Entry Entry
     *                     array["lang"|"str"]
     */
    private function getClassificationTaxon($vals, &$id, &$entry)
    {
        $id = "";
        $entry = array(self::TAG_LANGSTR_LANG => "", self::TAG_LANGSTR_STR => "");
        $openTag = "";
        $tagDataArray = array();
        foreach($vals as $val)
        {
            if($val["type"] == "open" && strlen($openTag)==0)
            {
                if($val["tag"] == self::TAG_ENTRY)
                {
                    $openTag = $val["tag"];
                    array_push($tagDataArray, $val);
                }
            }
            else if($val["type"] == "close" && strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
                if($openTag == $val["tag"])
                {
                    if($val["tag"] == self::TAG_ENTRY)
                    {
                        // Get langstring(language, string)
                        $this->getLangString($tagDataArray, $language, $string);
                        $entry[self::TAG_LANGSTR_LANG] = $language;
                        $entry[self::TAG_LANGSTR_STR] = $string;
                    }
                    $openTag = "";
                    $tagDataArray = array();
                }
            }
            else if($val["type"] == "complete" && strlen($openTag)==0)
            {
                if($val["tag"] == self::TAG_ID)
                {
                    if(strlen($val["value"])>0)
                    {
                        $id = $this->forXmlChangeDecode($val["value"]);
                    }
                }
            }
            else if(strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
            }
        }
    }
    
    /**
     * Get langString
     * メタデータ取得
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param string $language Language 言語
     * @param string $string String 文字列
     */
    private function getLangString($vals, &$language, &$string)
    {
        $language = "";
        $string = "";
        foreach($vals as $val)
        {
            if($val["type"] == "complete")
            {
                if($val["tag"] == self::TAG_LANGSTR_LANG)
                {
                    $language = $this->forXmlChangeDecode($val["value"]);
                }
                else if($val["tag"] == self::TAG_LANGSTR_STR)
                {
                    $string = $this->forXmlChangeDecode($val["value"]);
                }
            }
        }
    }
    
    /**
     * Get vocabulary
     * メタデータ取得
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param string $source Source Source
     * @param string $value Value Value
     */
    private function getVocabulary($vals, &$source, &$value)
    {
        $source = "";
        $value = "";
        foreach($vals as $val)
        {
            if($val["type"] == "complete")
            {
                if($val["tag"] == self::TAG_VOCAB_SRC)
                {
                    $source = $this->forXmlChangeDecode($val["value"]);
                }
                else if($val["tag"] == self::TAG_VOCAB_VAL)
                {
                    $value = $this->forXmlChangeDecode($val["value"]);
                }
            }
        }
    }
    
    /**
     * Get dateTime
     * メタデータ取得
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param string $dateTime Date time 日時
     * @param array $discription Description Desription
     *                           array["lang"|"str"]
     */
    private function getDateTime($vals, &$dateTime, &$discription)
    {
        $dateTime = "";
        $discription = array(self::TAG_LANGSTR_LANG => "", self::TAG_LANGSTR_STR => "");
        $openTag = "";
        $tagDataArray = array();
        foreach($vals as $val)
        {
            if($val["type"] == "open" && strlen($openTag)==0 && $val["tag"]==self::TAG_DESCRIPTION)
            {
                $openTag = $val["tag"];
                array_push($tagDataArray, $val);
            }
            else if($val["type"] == "close" && strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
                if($openTag == $val["tag"])
                {
                    if($val["tag"] == self::TAG_DESCRIPTION)
                    {
                        // Get identifer(catalog, entry)
                        $this->getLangString($tagDataArray, $language, $string);
                        $discription[self::TAG_LANGSTR_LANG] = $language;
                        $discription[self::TAG_LANGSTR_STR] = $string;
                    }
                    $openTag = "";
                    $tagDataArray = array();
                }
            }
            else if($val["type"] == "complete" && strlen($openTag)==0)
            {
                if($val["tag"] == self::TAG_DATE_TIME)
                {
                    $dateTime = RepositoryOutputFilterLOM::date($this->forXmlChangeDecode($val["value"]));
                }
            }
            else if(strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
            }
        }
    }
    
    /**
     * Get duration
     * メタデータ取得
     *
     * @param array $vals Value list 値一覧
     *                    array[$ii]["type"|"tag"|"value"]
     * @param string $duration Duration Duration
     * @param array $discription Discription Discription
     *                           array["lang"|"str"]
     */
    private function getDuration($vals, &$duration, &$discription)
    {
        $duration = "";
        $discription = array(self::TAG_LANGSTR_LANG => "", self::TAG_LANGSTR_STR => "");
        $openTag = "";
        $tagDataArray = array();
        foreach($vals as $val)
        {
            if($val["type"] == "open" && strlen($openTag)==0 && $val["tag"]==self::TAG_DUR_DESCRIPTION)
            {
                $openTag = $val["tag"];
                array_push($tagDataArray, $val);
            }
            else if($val["type"] == "close" && strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
                if($openTag == $val["tag"])
                {
                    if($val["tag"] == self::TAG_DUR_DESCRIPTION)
                    {
                        // Get langstring(language, string)
                        $this->getLangString($tagDataArray, $language, $string);
                        $discription[self::TAG_LANGSTR_LANG] = $language;
                        $discription[self::TAG_LANGSTR_STR] = $string;
                    }
                    $openTag = "";
                    $tagDataArray = array();
                }
            }
            else if($val["type"] == "complete" && strlen($openTag)==0)
            {
                if($val["tag"] == self::TAG_DUR_DURATION)
                {
                    $duration = RepositoryOutputFilterLOM::duration($this->forXmlChangeDecode($val["value"]));
                }
            }
            else if(strlen($openTag)>0)
            {
                array_push($tagDataArray, $val);
            }
        }
    }
    
    /**
     * Explode name string
     * 名前分割
     *
     * @param string $str String 文字列
     * @return array["family"|"name"] Name 名前
     */
    private function explodeNameStr($str)
    {
        $family = "";
        $name = "";
        
        $str = str_replace("　", " ", $str);
        $str = preg_replace("/ +/", " ", $str);
        $str = preg_replace("/ +/", " ", $str);
        
        $nameArray = explode(self::NAME_DELIMITER, $str, 2);
        $family = $nameArray[0];
        if(isset($nameArray[1]))
        {
            $name = $nameArray[1];
        }
        
        return array(self::KEY_FAMILY => $family, self::KEY_NAME => $name);
    }
    
    /**
     * Init irBasic array
     * アイテム基本情報初期化
     *
     * @return array Basic item information list アイテム基本情報
     *               array["item_id"|"item_no"|"item_type_id"|"title"|"title_english"|"language"|"pub_year"|"pub_month"|"pub_day"|"serch_key"|"serch_key_english"]
     */
    private function initIrBasic()
    {
        $irBasic = array(
                self::KEY_ITEM_ID => "",
                self::KEY_ITEM_NO => "",
                self::KEY_ITEM_TYPE_ID => self::ITEMTYPE_ID,
                self::KEY_TITLE => "",
                self::KEY_TITLE_EN => "",
                self::KEY_LANGUAGE => "",
                self::KEY_PUB_YEAR => "",
                self::KEY_PUB_MONTH => "",
                self::KEY_PUB_DAY => "",
                self::KEY_SEARCH_KEY => "",
                self::KEY_SEARCH_KEY_EN => ""
            );
        return $irBasic;
    }
    
    /**
     * Create irMetadata array
     * メタデータ一覧作成
     *
     * @param int $attrId Attribute id 属性ID
     * @param string $inputType Input type 入力タイプ
     * @param string $data Value 値
     * @return array Metadata list メタデータ一覧
     *               array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function createIrMetadata($attrId, $inputType, $data)
    {
        $irMetadata = array();
        switch($inputType)
        {
            case self::INPUT_TYPE_LINK:
            case self::INPUT_TYPE_TEXT:
            case self::INPUT_TYPE_SELECT:
            case self::INPUT_TYPE_TEXTAREA:
            case self::INPUT_TYPE_CHECKBOX:
            case self::INPUT_TYPE_DATE:
                $irMetadata = $this->initIrMetadata();
                $irMetadata[self::KEY_ATTR_ID] = $attrId;
                $irMetadata[self::KEY_ATTR_NO] = $this->cntMetadata[$attrId];
                $irMetadata[self::KEY_INPUT_TYPE] = $inputType;
                if(isset($data[self::KEY_ATTR_VALUE]))
                    $irMetadata[self::KEY_ATTR_VALUE] = $data[self::KEY_ATTR_VALUE];
                break;
            case self::INPUT_TYPE_BIBLIOINFO:
                $irMetadata = $this->initIrBiblioMetadata();
                $irMetadata[self::KEY_ATTR_ID] = $attrId;
                $irMetadata[self::KEY_BIBLIO_NO] = $this->cntMetadata[$attrId];
                if(isset($data[self::KEY_BIBLIO_NAME]))
                    $irMetadata[self::KEY_BIBLIO_NAME] = $data[self::KEY_BIBLIO_NAME];
                if(isset($data[self::KEY_BIBLIO_NAME_EN]))
                    $irMetadata[self::KEY_BIBLIO_NAME_EN] = $data[self::KEY_BIBLIO_NAME_EN];
                if(isset($data[self::KEY_VOLUME]))
                    $irMetadata[self::KEY_VOLUME] = $data[self::KEY_VOLUME];
                if(isset($data[self::KEY_ISSUE]))
                    $irMetadata[self::KEY_ISSUE] = $data[self::KEY_ISSUE];
                if(isset($data[self::KEY_SPAGE]))
                    $irMetadata[self::KEY_SPAGE] = $data[self::KEY_SPAGE];
                if(isset($data[self::KEY_EPAGE]))
                    $irMetadata[self::KEY_EPAGE] = $data[self::KEY_EPAGE];
                if(isset($data[self::KEY_DATE_OF_ISSUED]))
                    $irMetadata[self::KEY_DATE_OF_ISSUED] = $data[self::KEY_DATE_OF_ISSUED];
                break;
            case self::INPUT_TYPE_NAME:
                $irMetadata = $this->initIrNameMetadata();
                $irMetadata[self::KEY_ATTR_ID] = $attrId;
                $irMetadata[self::KEY_NAME_NO] = $this->cntMetadata[$attrId];
                if(isset($data[self::KEY_FAMILY]))
                    $irMetadata[self::KEY_FAMILY] = $data[self::KEY_FAMILY];
                if(isset($data[self::KEY_NAME]))
                    $irMetadata[self::KEY_NAME] = $data[self::KEY_NAME];
                if(isset($data[self::KEY_FAMILY_RUBY]))
                    $irMetadata[self::KEY_FAMILY_RUBY] = $data[self::KEY_FAMILY_RUBY];
                if(isset($data[self::KEY_NAME_RUBY]))
                    $irMetadata[self::KEY_NAME_RUBY] = $data[self::KEY_NAME_RUBY];
                if(isset($data[self::KEY_EMAIL]))
                    $irMetadata[self::KEY_EMAIL] = $data[self::KEY_EMAIL];
                if(isset($data[self::KEY_AUTHOR_ID]))
                    $irMetadata[self::KEY_AUTHOR_ID] = $data[self::KEY_AUTHOR_ID];
                if(isset($data[self::KEY_LANGUAGE]))
                    $irMetadata[self::KEY_LANGUAGE] = $data[self::KEY_LANGUAGE];
                break;
            default:
                break;
        }
        return $irMetadata;
    }
    
    /**
     * Init irMetadata array
     * メタデータ一覧初期化
     *
     * @return array Metadata list メタデータ一覧
     *               array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function initIrMetadata()
    {
        $irMetadata = array(
                self::KEY_ITEM_ID => "",
                self::KEY_ITEM_NO => "",
                self::KEY_ITEM_TYPE_ID => self::ITEMTYPE_ID,
                self::KEY_ATTR_ID => "",
                self::KEY_ATTR_NO => "",
                self::KEY_INPUT_TYPE => "",
                self::KEY_ATTR_VALUE => ""
            );
        return $irMetadata;
    }
    
    /**
     * Init irMetadata array for name
     * メタデータ一覧初期化
     *
     * @return array Metadata list メタデータ一覧
     *               array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"name_no"|"input_type"|"family"|"name"|"family_ruby"|"name_ruby"|"email"|"author_id"|"language"]
     */
    private function initIrNameMetadata()
    {
        $irMetadata = array(
                self::KEY_ITEM_ID => "",
                self::KEY_ITEM_NO => "",
                self::KEY_ITEM_TYPE_ID => self::ITEMTYPE_ID,
                self::KEY_ATTR_ID => "",
                self::KEY_FAMILY => "",
                self::KEY_NAME => "",
                self::KEY_FAMILY_RUBY => "",
                self::KEY_NAME_RUBY => "",
                self::KEY_EMAIL => "",
                self::KEY_AUTHOR_ID => "",
                self::KEY_LANGUAGE => "",
                self::KEY_INPUT_TYPE => self::INPUT_TYPE_NAME,
                self::KEY_NAME_NO => ""
            );
        return $irMetadata;
    }
    
    /**
     * Init irMetadata array for biblio_info
     * メタデータ一覧初期化
     *
     * @return array Metadata list メタデータ一覧
     *               array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"biblio_name"|"biblio_name_en"|"volume"|"issue"|"spage"|"epage"|"date_of_issued"|"biblio_no"]
     */
    private function initIrBiblioMetadata()
    {
        $irMetadata = array(
                self::KEY_ITEM_ID => "",
                self::KEY_ITEM_NO => "",
                self::KEY_ITEM_TYPE_ID => self::ITEMTYPE_ID,
                self::KEY_ATTR_ID => "",
                self::KEY_BIBLIO_NAME => "",
                self::KEY_BIBLIO_NAME_EN => "",
                self::KEY_VOLUME => "",
                self::KEY_ISSUE => "",
                self::KEY_SPAGE => "",
                self::KEY_EPAGE => "",
                self::KEY_DATE_OF_ISSUED => "",
                self::KEY_INPUT_TYPE => self::INPUT_TYPE_BIBLIOINFO,
                self::KEY_BIBLIO_NO => ""
            );
        return $irMetadata;
    }
    
    /**
     * Set require metadata to array
     * 必須オプション設定
     *
     * @param int $repositoryId Repository id リポジトリID
     * @param string $metadata Metadata メタデータ
     *                         array["irMetadata"][$ii]["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     */
    private function setRequireMetadataToArray($repositoryId, &$metadata)
    {
        // repositoryId
        $attrId = AttrId::REPO_ID;
        $this->cntMetadata[$attrId]++;
        $data = array(self::KEY_ATTR_VALUE => $repositoryId);
        $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
        if(count($irMetadata)>0)
        {
            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
        }
        
        // identifier
        $attrId = AttrId::IDENTIFIER;
        $this->cntMetadata[$attrId]++;
        $data = array(self::KEY_ATTR_VALUE => $metadata[RepositoryConst::HARVESTING_COL_HEADERIDENTIFIER][0]["value"]);
        $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
        if(count($irMetadata)>0)
        {
            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
        }
        
        // datestamp
        $attrId = AttrId::DATESTAMP;
        $this->cntMetadata[$attrId]++;
        $data = array(self::KEY_ATTR_VALUE => $metadata[RepositoryConst::HARVESTING_COL_DATESTAMP][0]["value"]);
        $irMetadata = $this->createIrMetadata($attrId, self::INPUT_TYPE_TEXT, $data);
        if(count($irMetadata)>0)
        {
            array_push($metadata[self::KEY_IR_METADATA], $irMetadata);
        }
    }
    
    // ---------------------------------------------
    // Public method
    // ---------------------------------------------
    /**
     * Set TransStartDate
     * トランザクション開始日時設定
     *
     * @param string $transStartDate Transaction start date トランザクション開始日時
     */
    public function setTransStartDate($transStartDate)
    {
        $this->TransStartDate = $transStartDate;
    }
    
    /**
     * Get metadata array from ListRecords(record)
     * メタデータ取得
     *
     * @param string $metadataXml Metadata XML XML文字列
     * @param int $repositoryId Repository id リポジトリID
     * @param array $metadata Metadata list メタデータ一覧
     *                        array[TAGNAME][$ii]["value"]
     *                                           ["attribute"][KEY]
     * @return boolean Result 結果
     */
    public function setMetadataFromListRecords($metadataXml, $repositoryId, &$metadata)
    {
        // 1. Init member
        $this->initMember();
        
        // 2. Set basic metadata xml
        $this->setBasicMetadataXmlStr($metadataXml);
        
        // 3. Init metadata array
        $metadata[self::KEY_IR_BASIC] = $this->initIrBasic();
        $metadata[self::KEY_IR_METADATA] = array();
        
        // 4. Set metadata to array
        $ret = $this->setMetadataToArray($repositoryId, $metadata);
        
        return $ret;
    }
    
    /**
     * Check metadata
     * メタデータチェック
     *
     * @param array $metadata Metadata メタデータ
     *                        array["HEARDER"][0]["attributes"]["STATUS"]
     *                        array["TITLE"|"LANGUAGE"|"URI"|"NIITYPE"]
     * @param int $logStatus Status 状態
     * @param array $logMsg Log message ログメッセージ
     *                      array[$ii]
     * @return boolean Result 結果
     */
    public function checkMetadata($metadata, &$logStatus, &$logMsg)
    {
        // title
        $title = $metadata[self::KEY_IR_BASIC][self::KEY_TITLE];
        $titleEn = $metadata[self::KEY_IR_BASIC][self::KEY_TITLE_EN];
        if(strlen($title)==0 && strlen($titleEn)==0)
        {
            array_push($logMsg, self::MSG_ER_GET_TITLE);
            $logStatus = self::LOG_STATUS_ERROR;
            return false;
        }
        
        // language
        $language = RepositoryOutputFilterLOM::language($metadata[self::KEY_IR_BASIC][self::KEY_LANGUAGE]);
        if(strlen($language)==0)
        {
            array_push($logMsg, self::MSG_WN_MISS_LANGAGE);
            $logStatus = self::LOG_STATUS_WARNING;
        }
        
        return true;
    }
    
    /**
     * Check item exists
     * アイテム存在確認
     * 
     * @param array $metadata Metadata メタデータ
     *                        array[KEYNAME]
     * @param int $repositoryId Repository id リポジトリID
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param string $datestamp Date stamp Date stamp
     * @param string $isDelete Is delete 削除済みか
     * @return bool true: Exists / false: No exists
     */
    public function isItemExists($metadata, $repositoryId, &$itemId, &$itemNo, &$datestamp, &$isDelete)
    {
        // Init
        $itemId = "";
        $itemNo = "";
        $datestamp = "";
        $isDelete = "";
        $query = "SELECT DISTINCT ".self::DB_ITEM_ATTR_ITEM_ID.", ".self::DB_ITEM_ATTR_ITEM_NO." ".
                 "FROM ".DATABASE_PREFIX.self::DB_ITEM_ATTR." ".
                 "WHERE ".self::DB_ITEM_ATTR_ATTR_ID." = ? ".
                 "AND ".self::DB_ITEM_ATTR_ATTR_NO." = 1 ".
                 "AND ".self::DB_ITEM_ATTR_ATTR_VAL." = ? ".
                 "AND ".self::DB_ITEM_ATTR_ITEM_ID." IN (".
                 "  SELECT DISTINCT ".self::DB_ITEM_ATTR_ITEM_ID." ".
                 "  FROM ".DATABASE_PREFIX.self::DB_ITEM_ATTR." ".
                 "  WHERE ".self::DB_ITEM_ATTR_ATTR_ID." = ? ".
                 "  AND ".self::DB_ITEM_ATTR_ATTR_NO." = 1 ".
                 "  AND ".self::DB_ITEM_ATTR_ATTR_VAL." = ? ".
                 "  AND ".self::DB_ITEM_ATTR_ITEM_TYPE_ID." = ?);";
        $params = array();
        $params[] = AttrId::IDENTIFIER; //attribute_id / Identifier
        $params[] = $metadata[RepositoryConst::HARVESTING_COL_HEADERIDENTIFIER][0]["value"];  //attribute_value / Itentifier
        $params[] = AttrId::REPO_ID;    //attribute_id / repositoryId
        $params[] = $repositoryId;    //attribute_value / repositoryId
        $params[] = self::ITEMTYPE_ID;  //item_type_id
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        
        if(count($result) == 0)
        {
            // Not exists
            return false;
        }
        else
        {
            $itemId = $result[0][self::DB_ITEM_ATTR_ITEM_ID];
            $itemNo = $result[0][self::DB_ITEM_ATTR_ITEM_NO];
            
            // Exists
            $query = "SELECT ".self::DB_ITEM_ATTR_ATTR_VAL." ".
                     "FROM ".DATABASE_PREFIX.self::DB_ITEM_ATTR." ".
                     "WHERE ".self::DB_ITEM_ATTR_ITEM_ID." = ? ".
                     "AND ".self::DB_ITEM_ATTR_ITEM_NO." = ? ".
                     "AND ".self::DB_ITEM_ATTR_ATTR_ID." = ? ".
                     "AND ".self::DB_ITEM_ATTR_ITEM_NO." = 1 ;";
            $params = array();
            $params[] = $itemId;
            $params[] = $itemNo;
            $params[] = AttrId::DATESTAMP; //attribute_id / datestamp
            $result = $this->Db->execute($query, $params);
            if(count($result) > 0)
            {
                $datestamp = $result[0][self::DB_ITEM_ATTR_ATTR_VAL];
            }
            
            // Get repository_item table's is_delete
            $query = "SELECT ".self::DB_ITEM_IS_DELETE." ".
                     "FROM ".DATABASE_PREFIX.self::DB_ITEM." ".
                     "WHERE ".self::DB_ITEM_ITEM_ID." = ? ".
                     "AND ".self::DB_ITEM_ITEM_NO." = ? ;";
            $params = array();
            $params[] = $itemId;
            $params[] = $itemNo;
            $result = $this->Db->execute($query, $params);
            if(count($result) > 0)
            {
                $isDelete = intval($result[0][self::DB_ITEM_IS_DELETE]);
            }
            
            return true;
        }
    }
    
    /**
     * Set item_id and item_no to irBasic and irMetadata
     * アイテムID、アイテム通番設定
     *
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param array $metadata Metadata メタデータ
     *                        array[KEYNAME]
     * @param array $irBasic Basic item information list アイテム基本情報
     *                       array["item_id"|"item_no"|"item_type_id"|"title"|"title_english"|"language"|"pub_year"|"pub_month"|"pub_day"|"serch_key"|"serch_key_english"]
     * @param array $irMetadata Metadata list メタデータ一覧
     *                          array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     * @return boolean Result 結果
     */
    public function setItemIdForIrData($itemId, $itemNo, &$metadata, &$irBasic, &$irMetadataArray)
    {
        // Check param
        $itemId = intval($itemId);
        $itemNo = intval($itemNo);
        if($itemId<1 || $itemNo<1)
        {
            return false;
        }
        
        // Set item_id and item_no
        $metadata[self::KEY_IR_BASIC][self::KEY_ITEM_ID] = $itemId;
        $metadata[self::KEY_IR_BASIC][self::KEY_ITEM_NO] = $itemNo;
        if(strlen($metadata[self::KEY_IR_BASIC][self::KEY_LANGUAGE])==0)
        {
            $metadata[self::KEY_IR_BASIC][self::KEY_LANGUAGE] = self::DEFAULT_LANGUAGE;
        }
        
        for($ii=0; $ii<count($metadata[self::KEY_IR_METADATA]); $ii++)
        {
            $metadata[self::KEY_IR_METADATA][$ii][self::KEY_ITEM_ID] = $itemId;
            $metadata[self::KEY_IR_METADATA][$ii][self::KEY_ITEM_NO] = $itemNo;
            
            if($metadata[self::KEY_IR_METADATA][$ii][self::KEY_INPUT_TYPE] == self::INPUT_TYPE_NAME)
            {
                // Set author ID
                $attrId = $metadata[self::KEY_IR_METADATA][$ii][self::KEY_ATTR_ID];
                $nameNo = $metadata[self::KEY_IR_METADATA][$ii][self::KEY_NAME_NO];
                $family = $metadata[self::KEY_IR_METADATA][$ii][self::KEY_FAMILY];
                $name = $metadata[self::KEY_IR_METADATA][$ii][self::KEY_NAME];
                $authorId = $this->getAuthorIdForIrMetadata($itemId, $itemNo, $attrId, $nameNo, $family, $name);
                $metadata[self::KEY_IR_METADATA][$ii][self::KEY_AUTHOR_ID] = $authorId;
            }
        }
        
        $irBasic = $metadata[self::KEY_IR_BASIC];
        $irMetadataArray = $metadata[self::KEY_IR_METADATA];
        
        // Set additional metadata
        $this->setAdditionalMetadata($itemId, $itemNo, $irMetadataArray);
        
        return true;
    }
    
    /**
     * Get author id
     * 著者ID取得
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param int $attrId Attribute id 属性ID
     * @param int $nameNo Name number 名前通番
     * @param int $family Family 姓
     * @param int $name Name 名
     * @return int Author id 著者ID
     */
    private function getAuthorIdForIrMetadata($itemId, $itemNo, $attrId, $nameNo, $family, $name)
    {
        // Get author ID from database
        $query = "SELECT ".self::DB_PERSONAL_NAME_FAMILY.", ".self::DB_PERSONAL_NAME_NAME.", ".self::DB_PERSONAL_NAME_AUTHOR_ID." ".
                 "FROM ".DATABASE_PREFIX.self::DB_PERSONAL_NAME." ".
                 "WHERE ".self::DB_PERSONAL_NAME_ITEM_ID." = ? ".
                 "AND ".self::DB_PERSONAL_NAME_ITEM_NO." = ? ".
                 "AND ".self::DB_PERSONAL_NAME_ATTR_ID." = ? ".
                 "AND ".self::DB_PERSONAL_NAME_NAME_NO." = ? ;";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attrId;
        $params[] = $nameNo;
        $result = $this->Db->execute($query, $params);
        if($result===false || count($result)==0)
        {
            return 0;
        }
        
        // Check author ID
        $authorId = 0;
        if($result[0][self::DB_PERSONAL_NAME_FAMILY] == $family && $result[0][self::DB_PERSONAL_NAME_NAME] == $name)
        {
            $authorId = intval($result[0][self::DB_PERSONAL_NAME_AUTHOR_ID]);
        }
        
        return $authorId;
    }
    
    /**
     * Set additional metadata
     * 追加メタデータ設定
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param array $metadataArray Metadata list メタデータ一覧
     *                             array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     * @return boolean Result 結果
     */
    private function setAdditionalMetadata($itemId, $itemNo, &$metadataArray)
    {
        $itemTypeId = self::ITEMTYPE_ID;
        $startAddAttrId = AttrId::MAX_ATTR_ID;
        
        // Get itemAttrType
        $query = "SELECT * ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_ITEM_ATTR_TYPE." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ITEM_TYPE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_ID." >= ? ".
                 "AND ".RepositoryConst::DBCOL_COMMON_IS_DELETE." = 0 ".
                 "ORDER BY ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_ID." ASC;";
        $params = array();
        $params[] = $itemTypeId;
        $params[] = $startAddAttrId;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        
        foreach($result as $itemAttrType)
        {
            $inputType = $itemAttrType[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_IMPUT_TYPE];
            $attrId = $itemAttrType[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_ATTRIBUTE_ID];
            if($inputType == RepositoryConst::ITEM_ATTR_TYPE_BIBLIOINFO)
            {
                $this->setAdditionalBiblioInfo($itemId, $itemNo, $attrId, $itemTypeId, $metadataArray);
            }
            else if($inputType == RepositoryConst::ITEM_ATTR_TYPE_FILE || $inputType == RepositoryConst::ITEM_ATTR_TYPE_FILEPRICE)
            {
                // no update
                continue;
            }
            else if($inputType == RepositoryConst::ITEM_ATTR_TYPE_NAME)
            {
                $language = $itemAttrType[RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_TYPE_DISPLAY_LANG_TYPE];
                $this->setAdditionalName($itemId, $itemNo, $attrId, $itemTypeId, $language, $metadataArray);
            }
            else if($inputType == RepositoryConst::ITEM_ATTR_TYPE_THUMBNAIL)
            {
                // no update
                continue;
            }
            else
            {
                $this->setAdditionalAttribute($itemId, $itemNo, $attrId, $itemTypeId, $inputType, $metadataArray);
            }
        }
        
        return true;
    }
    
    /**
     * Set additional biblioInfo
     * 追加メタデータ設定
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param int $attrId Attribute id 属性ID
     * @param int $itemTypeId Item type id アイテムタイプID
     * @param array $metadataArray Metadata list メタデータ一覧
     *                             array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     * @return boolean Result 結果
     */
    private function setAdditionalBiblioInfo($itemId, $itemNo, $attrId, $itemTypeId, &$metadataArray)
    {
        // Get BiblioInfo
        $query = "SELECT * ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_BIBLIO_INFO." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_BIBLIO_INFO_ITEM_TYPE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_BIBLIO_INFO_ITEM_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_BIBLIO_INFO_ITEM_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_BIBLIO_INFO_ATTRIBUTE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_COMMON_IS_DELETE." = 0 ".
                 "ORDER BY ".RepositoryConst::DBCOL_REPOSITORY_BIBLIO_INFO_BIBLIO_NO." ASC;";
        $params = array();
        $params[] = $itemTypeId;
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attrId;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        
        foreach($result as $biblioInfo)
        {
            $biblioInfo["input_type"] = RepositoryConst::ITEM_ATTR_TYPE_BIBLIOINFO;
            array_push($metadataArray, $biblioInfo);
        }
    }
    
    /**
     * Set additional name
     * 追加メタデータ設定
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param int $attrId Attribute id 属性ID
     * @param int $itemTypeId Item type id アイテムタイプID
     * @param string $language Language 言語
     * @param array $metadataArray Metadata list メタデータ一覧
     *                             array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     * @return boolean Result 結果
     */
    private function setAdditionalName($itemId, $itemNo, $attrId, $itemTypeId, $language, &$metadataArray)
    {
        // Get personalName
        $query = "SELECT * ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_PERSONAL_NAME." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_TYPE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ITEM_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_ATTRIBUTE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_COMMON_IS_DELETE." = 0 ".
                 "ORDER BY ".RepositoryConst::DBCOL_REPOSITORY_PERSONAL_NAME_PERSONAL_NAME_NO." ASC;";
        $params = array();
        $params[] = $itemTypeId;
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attrId;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        
        foreach($result as $personalName)
        {
            $personalName["language"] = $language;
            $personalName["input_type"] = RepositoryConst::ITEM_ATTR_TYPE_NAME;
            array_push($metadataArray, $personalName);
        }
    }
    
    /**
     * Set additional attribute
     * 追加メタデータ設定
     * 
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param int $attrId Attribute id 属性ID
     * @param int $itemTypeId Item type id アイテムタイプID
     * @param string $inputType Input type 入力タイプ
     * @param array $metadataArray Metadata list メタデータ一覧
     *                             array["item_id"|"item_no"|"item_type_id"|"attribute_id"|"attribute_no"|"input_type"|"attribute_value"]
     * @return boolean Result 結果
     */
    private function setAdditionalAttribute($itemId, $itemNo, $attrId, $itemTypeId, $inputType, &$metadataArray)
    {
        // Get attribute
        $query = "SELECT * ".
                 "FROM ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_ITEM_ATTR." ".
                 "WHERE ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_TYPE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ITEM_NO." = ? ".
                 "AND ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_ID." = ? ".
                 "AND ".RepositoryConst::DBCOL_COMMON_IS_DELETE." = 0 ".
                 "ORDER BY ".RepositoryConst::DBCOL_REPOSITORY_ITEM_ATTR_ATTRIBUTE_NO." ASC;";
        $params = array();
        $params[] = $itemTypeId;
        $params[] = $itemId;
        $params[] = $itemNo;
        $params[] = $attrId;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            return false;
        }
        
        foreach($result as $itemAttr)
        {
            $itemAttr["input_type"] = $inputType;
            array_push($metadataArray, $itemAttr);
        }
    }
}

?>
